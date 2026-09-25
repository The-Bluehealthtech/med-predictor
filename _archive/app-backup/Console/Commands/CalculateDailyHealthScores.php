<?php

namespace App\Console\Commands;

use App\Models\Athlete;
use App\Models\HealthScore;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateDailyHealthScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health:calculate-scores {--date= : Date to calculate scores for (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate daily health scores for all active athletes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? now()->parse($this->option('date'))->toDateString() : now()->toDateString();
        
        $this->info("Calculating health scores for date: {$date}");
        
        $athletes = Athlete::where('active', true)->get();
        $processed = 0;
        $errors = 0;

        foreach ($athletes as $athlete) {
            try {
                $this->calculateHealthScore($athlete, $date);
                $processed++;
                
                if ($processed % 10 === 0) {
                    $this->info("Processed {$processed} athletes...");
                }
            } catch (\Exception $e) {
                $errors++;
                Log::error("Error calculating health score for athlete {$athlete->id}: " . $e->getMessage());
                $this->error("Error processing athlete {$athlete->id}: " . $e->getMessage());
            }
        }

        $this->info("Health score calculation completed!");
        $this->info("Processed: {$processed} athletes");
        $this->info("Errors: {$errors} athletes");
        
        if ($errors > 0) {
            return 1;
        }
        
        return 0;
    }

    /**
     * Calculate health score for a specific athlete.
     */
    private function calculateHealthScore(Athlete $athlete, string $date): void
    {
        // Check if score already exists for this date
        $existingScore = HealthScore::where('athlete_id', $athlete->id)
                                   ->where('calculated_date', $date)
                                   ->first();

        if ($existingScore) {
            $this->warn("Health score already exists for athlete {$athlete->id} on {$date}");
            return;
        }

        // Calculate base score (100 points)
        $score = 100;
        $contributingFactors = [];
        $metrics = [];

        // Deduct points for active injuries
        $activeInjuries = $athlete->injuries()->where('status', 'open')->count();
        $injuryDeduction = $activeInjuries * 15; // 15 points per active injury
        $score -= $injuryDeduction;
        
        if ($activeInjuries > 0) {
            $contributingFactors['active_injuries'] = [
                'count' => $activeInjuries,
                'deduction' => $injuryDeduction,
                'impact' => 'negative'
            ];
        }

        // Deduct points for unresolved risk alerts
        $unresolvedAlerts = $athlete->riskAlerts()->where('resolved', false)->count();
        $alertDeduction = $unresolvedAlerts * 10; // 10 points per unresolved alert
        $score -= $alertDeduction;
        
        if ($unresolvedAlerts > 0) {
            $contributingFactors['unresolved_alerts'] = [
                'count' => $unresolvedAlerts,
                'deduction' => $alertDeduction,
                'impact' => 'negative'
            ];
        }

        // Deduct points for critical risk alerts
        $criticalAlerts = $athlete->riskAlerts()
                                 ->where('resolved', false)
                                 ->where('priority', 'critical')
                                 ->count();
        $criticalDeduction = $criticalAlerts * 20; // 20 points per critical alert
        $score -= $criticalDeduction;
        
        if ($criticalAlerts > 0) {
            $contributingFactors['critical_alerts'] = [
                'count' => $criticalAlerts,
                'deduction' => $criticalDeduction,
                'impact' => 'negative'
            ];
        }

        // Deduct points for recent concussions
        $recentConcussions = $athlete->scatAssessments()
                                    ->where('concussion_confirmed', true)
                                    ->where('assessment_date', '>=', now()->subDays(30))
                                    ->count();
        $concussionDeduction = $recentConcussions * 25; // 25 points per recent concussion
        $score -= $concussionDeduction;
        
        if ($recentConcussions > 0) {
            $contributingFactors['recent_concussions'] = [
                'count' => $recentConcussions,
                'deduction' => $concussionDeduction,
                'impact' => 'negative'
            ];
        }

        // Add points for good compliance
        $recentPCMAs = $athlete->pcmas()
                               ->where('status', 'completed')
                               ->where('completed_at', '>=', now()->subDays(365))
                               ->count();
        $complianceBonus = min($recentPCMAs * 5, 20); // 5 points per PCMA, max 20
        $score += $complianceBonus;
        
        if ($complianceBonus > 0) {
            $contributingFactors['pcmas_completed'] = [
                'count' => $recentPCMAs,
                'bonus' => $complianceBonus,
                'impact' => 'positive'
            ];
        }

        // Add points for recent medical notes (good documentation)
        $recentNotes = $athlete->medicalNotes()
                               ->where('status', 'signed')
                               ->where('created_at', '>=', now()->subDays(30))
                               ->count();
        $notesBonus = min($recentNotes * 2, 10); // 2 points per note, max 10
        $score += $notesBonus;
        
        if ($notesBonus > 0) {
            $contributingFactors['recent_notes'] = [
                'count' => $recentNotes,
                'bonus' => $notesBonus,
                'impact' => 'positive'
            ];
        }

        // Ensure score is within bounds
        $score = max(0, min(100, $score));

        // Calculate trend by comparing with previous score
        $previousScore = HealthScore::where('athlete_id', $athlete->id)
                                   ->latest('calculated_date')
                                   ->first();

        $trend = 'stable';
        if ($previousScore) {
            $difference = $score - $previousScore->score;
            if ($difference > 5) {
                $trend = 'improving';
            } elseif ($difference < -5) {
                $trend = 'worsening';
            }
        }

        // Prepare metrics
        $metrics = [
            'active_injuries_count' => $activeInjuries,
            'unresolved_alerts_count' => $unresolvedAlerts,
            'critical_alerts_count' => $criticalAlerts,
            'recent_concussions_count' => $recentConcussions,
            'recent_pcmas_count' => $recentPCMAs,
            'recent_notes_count' => $recentNotes,
            'total_deductions' => $injuryDeduction + $alertDeduction + $criticalDeduction + $concussionDeduction,
            'total_bonuses' => $complianceBonus + $notesBonus,
        ];

        // Generate AI analysis insights
        $aiAnalysis = $this->generateAIAnalysis($score, $contributingFactors, $metrics);

        // Create health score record
        HealthScore::create([
            'athlete_id' => $athlete->id,
            'score' => $score,
            'trend' => $trend,
            'contributing_factors' => $contributingFactors,
            'metrics' => $metrics,
            'ai_analysis' => $aiAnalysis,
            'calculated_date' => $date,
        ]);

        Log::info("Health score calculated for athlete {$athlete->id}: {$score} ({$trend})");
    }

    /**
     * Generate AI analysis insights for the health score.
     */
    private function generateAIAnalysis(int $score, array $contributingFactors, array $metrics): array
    {
        $insights = [];
        $recommendations = [];

        // Analyze score level
        if ($score >= 90) {
            $insights[] = "Excellent health status maintained";
            $recommendations[] = "Continue current wellness protocols";
        } elseif ($score >= 80) {
            $insights[] = "Good health status with room for improvement";
            $recommendations[] = "Monitor for any emerging issues";
        } elseif ($score >= 70) {
            $insights[] = "Fair health status requiring attention";
            $recommendations[] = "Review injury prevention strategies";
        } elseif ($score >= 60) {
            $insights[] = "Poor health status requiring intervention";
            $recommendations[] = "Implement comprehensive health review";
        } else {
            $insights[] = "Critical health status requiring immediate attention";
            $recommendations[] = "Urgent medical evaluation recommended";
        }

        // Analyze specific factors
        if (isset($contributingFactors['active_injuries']) && $contributingFactors['active_injuries']['count'] > 0) {
            $insights[] = "Active injuries are impacting health score";
            $recommendations[] = "Prioritize injury rehabilitation";
        }

        if (isset($contributingFactors['critical_alerts']) && $contributingFactors['critical_alerts']['count'] > 0) {
            $insights[] = "Critical risk alerts require immediate attention";
            $recommendations[] = "Address critical alerts promptly";
        }

        if (isset($contributingFactors['recent_concussions']) && $contributingFactors['recent_concussions']['count'] > 0) {
            $insights[] = "Recent concussions affecting health status";
            $recommendations[] = "Ensure proper concussion protocol compliance";
        }

        return [
            'insights' => $insights,
            'recommendations' => $recommendations,
            'analysis_date' => now()->toISOString(),
            'model_version' => 'health-score-v1.0',
        ];
    }
} 