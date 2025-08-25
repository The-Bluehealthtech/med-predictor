<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Injury;
use App\Models\PCMA;
use App\Models\RiskAlert;
use App\Models\SCATAssessment;
use App\Models\MedicalNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard data for the medical module.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get counts for statistics
            $activeInjuryCount = Injury::where('status', 'open')->count();
            $pendingPCMACount = $this->getPendingPCMACount();
            $unresolvedAlertsCount = RiskAlert::where('resolved', false)->count();
            $totalAthletes = Athlete::where('active', true)->count();
            $concussionAssessmentsCount = SCATAssessment::where('concussion_confirmed', true)->count();
            $aiGeneratedNotesCount = MedicalNote::where('generated_by_ai', true)->count();

            // Get recent activities
            $recentInjuries = Injury::with(['athlete', 'diagnosedBy'])
                ->latest('date')
                ->limit(5)
                ->get();

            $recentPCMAs = PCMA::with(['athlete', 'assessor'])
                ->latest('created_at')
                ->limit(5)
                ->get();

            $recentAlerts = RiskAlert::with(['athlete'])
                ->where('resolved', false)
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Get health statistics
            $healthStats = $this->getHealthStatistics();

            // Get team-wise statistics
            $teamStats = $this->getTeamStatistics();

            // Get compliance data
            $complianceData = $this->getComplianceData();

            return response()->json([
                'data' => [
                    'statistics' => [
                        'active_injuries' => $activeInjuryCount,
                        'pending_pcmas' => $pendingPCMACount,
                        'unresolved_alerts' => $unresolvedAlertsCount,
                        'total_athletes' => $totalAthletes,
                        'concussion_assessments' => $concussionAssessmentsCount,
                        'ai_generated_notes' => $aiGeneratedNotesCount,
                    ],
                    'recent_activities' => [
                        'injuries' => $recentInjuries,
                        'pcmas' => $recentPCMAs,
                        'alerts' => $recentAlerts,
                    ],
                    'health_statistics' => $healthStats,
                    'team_statistics' => $teamStats,
                    'compliance_data' => $complianceData,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending PCMA count (athletes without completed annual PCMA).
     */
    private function getPendingPCMACount(): int
    {
        $currentYear = now()->year;
        
        // Get athletes who don't have a completed PCMA this year
        $athletesWithoutPCMA = Athlete::where('active', true)
            ->whereDoesntHave('pcmas', function ($query) use ($currentYear) {
                $query->where('status', 'completed')
                      ->whereYear('completed_at', $currentYear);
            })
            ->count();

        return $athletesWithoutPCMA;
    }

    /**
     * Get health statistics.
     */
    private function getHealthStatistics(): array
    {
        $totalAthletes = Athlete::where('active', true)->count();
        
        if ($totalAthletes === 0) {
            return [
                'injury_rate' => 0,
                'concussion_rate' => 0,
                'average_health_score' => 0,
                'compliance_rate' => 0,
            ];
        }

        $activeInjuries = Injury::where('status', 'open')->count();
        $concussions = SCATAssessment::where('concussion_confirmed', true)
            ->whereYear('assessment_date', now()->year)
            ->count();

        // Calculate average health score (placeholder logic)
        $averageHealthScore = 85; // This would be calculated from actual health metrics

        // Calculate compliance rate
        $compliantAthletes = Athlete::where('active', true)
            ->whereHas('pcmas', function ($query) {
                $query->where('status', 'completed')
                      ->whereYear('completed_at', now()->year);
            })
            ->count();

        $complianceRate = $totalAthletes > 0 ? ($compliantAthletes / $totalAthletes) * 100 : 0;

        return [
            'injury_rate' => $totalAthletes > 0 ? ($activeInjuries / $totalAthletes) * 100 : 0,
            'concussion_rate' => $totalAthletes > 0 ? ($concussions / $totalAthletes) * 100 : 0,
            'average_health_score' => $averageHealthScore,
            'compliance_rate' => round($complianceRate, 1),
        ];
    }

    /**
     * Get team-wise statistics.
     */
    private function getTeamStatistics(): array
    {
        return DB::table('teams')
            ->leftJoin('athletes', 'teams.id', '=', 'athletes.team_id')
            ->leftJoin('injuries', function ($join) {
                $join->on('athletes.id', '=', 'injuries.athlete_id')
                     ->where('injuries.status', 'open');
            })
            ->select(
                'teams.id',
                'teams.name',
                DB::raw('COUNT(DISTINCT athletes.id) as athlete_count'),
                DB::raw('COUNT(DISTINCT injuries.id) as active_injuries'),
                DB::raw('ROUND(AVG(CASE WHEN athletes.active = 1 THEN 1 ELSE 0 END) * 100, 1) as active_rate')
            )
            ->groupBy('teams.id', 'teams.name')
            ->orderBy('teams.name')
            ->get()
            ->toArray();
    }

    /**
     * Get compliance data.
     */
    private function getComplianceData(): array
    {
        $currentYear = now()->year;
        
        // PCMA Compliance
        $totalAthletes = Athlete::where('active', true)->count();
        $pcmaCompliant = Athlete::where('active', true)
            ->whereHas('pcmas', function ($query) use ($currentYear) {
                $query->where('status', 'completed')
                      ->whereYear('completed_at', $currentYear);
            })
            ->count();

        // Injury Reporting Compliance
        $injuryReportingCompliant = Athlete::where('active', true)
            ->whereHas('injuries', function ($query) use ($currentYear) {
                $query->whereYear('date', $currentYear);
            })
            ->count();

        // Medical Notes Compliance
        $medicalNotesCompliant = Athlete::where('active', true)
            ->whereHas('medicalNotes', function ($query) use ($currentYear) {
                $query->where('status', 'signed')
                      ->whereYear('created_at', $currentYear);
            })
            ->count();

        return [
            'pcma_compliance' => [
                'total' => $totalAthletes,
                'compliant' => $pcmaCompliant,
                'rate' => $totalAthletes > 0 ? round(($pcmaCompliant / $totalAthletes) * 100, 1) : 0,
            ],
            'injury_reporting_compliance' => [
                'total' => $totalAthletes,
                'compliant' => $injuryReportingCompliant,
                'rate' => $totalAthletes > 0 ? round(($injuryReportingCompliant / $totalAthletes) * 100, 1) : 0,
            ],
            'medical_notes_compliance' => [
                'total' => $totalAthletes,
                'compliant' => $medicalNotesCompliant,
                'rate' => $totalAthletes > 0 ? round(($medicalNotesCompliant / $totalAthletes) * 100, 1) : 0,
            ],
        ];
    }

    /**
     * Get alerts summary.
     */
    public function alertsSummary(): JsonResponse
    {
        $alerts = RiskAlert::with(['athlete'])
            ->where('resolved', false)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $alertsByType = $alerts->groupBy('type');
        $alertsByPriority = $alerts->groupBy('priority');

        return response()->json([
            'data' => [
                'total_unresolved' => $alerts->count(),
                'by_type' => $alertsByType->map->count(),
                'by_priority' => $alertsByPriority->map->count(),
                'recent_alerts' => $alerts->take(10),
            ]
        ]);
    }

    /**
     * Get quick actions data.
     */
    public function quickActions(): JsonResponse
    {
        $actions = [
            [
                'id' => 'new_athlete',
                'title' => 'Nouvel Athlète',
                'description' => 'Enregistrer un nouvel athlète',
                'icon' => '👤',
                'route' => '/medical/athletes/new',
                'color' => 'blue'
            ],
            [
                'id' => 'new_pcma',
                'title' => 'Nouveau PCMA',
                'description' => 'Effectuer une évaluation PCMA',
                'icon' => '📋',
                'route' => '/medical/pcma/new',
                'color' => 'red'
            ],
            [
                'id' => 'new_injury',
                'title' => 'Nouvelle Blessure',
                'description' => 'Enregistrer une blessure',
                'icon' => '🩹',
                'route' => '/medical/injuries/new',
                'color' => 'orange'
            ],
            [
                'id' => 'new_note',
                'title' => 'Nouvelle Note',
                'description' => 'Créer une note médicale',
                'icon' => '📝',
                'route' => '/medical/notes/new',
                'color' => 'green'
            ],
            [
                'id' => 'reports',
                'title' => 'Rapports',
                'description' => 'Générer des rapports',
                'icon' => '📊',
                'route' => '/medical/reports',
                'color' => 'purple'
            ],
            [
                'id' => 'settings',
                'title' => 'Paramètres',
                'description' => 'Configurer le module médical',
                'icon' => '⚙️',
                'route' => '/medical/settings',
                'color' => 'gray'
            ],
        ];

        return response()->json([
            'data' => $actions
        ]);
    }
} 