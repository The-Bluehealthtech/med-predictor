<?php

namespace App\Services;

use App\Models\PerformanceMetric;
use App\Models\PerformanceTrend;
use App\Models\PerformanceAlert;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceAnalysisService
{
    /**
     * Generate trends for a performance metric
     */
    public function generateTrends(PerformanceMetric $metric): void
    {
        try {
            // Get historical data for the same metric
            $historicalData = PerformanceMetric::where('player_id', $metric->player_id)
                ->where('metric_name', $metric->metric_name)
                ->where('id', '!=', $metric->id)
                ->orderBy('measurement_date')
                ->get();

            if ($historicalData->count() < 2) {
                return; // Need at least 2 data points for trend analysis
            }

            // Generate trends for different periods
            $this->generateTrendForPeriod($metric, $historicalData, 'weekly');
            $this->generateTrendForPeriod($metric, $historicalData, 'monthly');
            $this->generateTrendForPeriod($metric, $historicalData, 'quarterly');

        } catch (\Exception $e) {
            Log::error('Failed to generate trends for metric', [
                'metric_id' => $metric->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate trend for a specific period
     */
    protected function generateTrendForPeriod(PerformanceMetric $metric, $historicalData, string $period): void
    {
        $endDate = $metric->measurement_date;
        $startDate = $this->getStartDateForPeriod($endDate, $period);

        // Get data points within the period
        $periodData = $historicalData->filter(function ($item) use ($startDate, $endDate) {
            return $item->measurement_date >= $startDate && $item->measurement_date <= $endDate;
        });

        if ($periodData->count() < 2) {
            return;
        }

        $initialValue = $periodData->first()->metric_value;
        $finalValue = $periodData->last()->metric_value;
        $changePercentage = $this->calculateChangePercentage($initialValue, $finalValue);

        // Create trend record
        PerformanceTrend::create([
            'metric_id' => $metric->id,
            'player_id' => $metric->player_id,
            'trend_period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'initial_value' => $initialValue,
            'final_value' => $finalValue,
            'change_percentage' => $changePercentage,
            'trend_direction' => $this->determineTrendDirection($changePercentage),
            'trend_strength' => $this->determineTrendStrength($changePercentage),
            'data_points_count' => $periodData->count(),
            'confidence_level' => $this->calculateConfidenceLevel($periodData),
            'analysis_notes' => $this->generateTrendNotes($metric, $changePercentage, $period),
            'created_by' => $metric->created_by,
        ]);
    }

    /**
     * Check for alerts based on performance metric
     */
    public function checkForAlerts(PerformanceMetric $metric): void
    {
        try {
            // Check for injury risk alerts
            $this->checkInjuryRiskAlerts($metric);

            // Check for performance decline alerts
            $this->checkPerformanceDeclineAlerts($metric);

            // Check for medical alerts
            $this->checkMedicalAlerts($metric);

            // Check for compliance alerts
            $this->checkComplianceAlerts($metric);

        } catch (\Exception $e) {
            Log::error('Failed to check for alerts', [
                'metric_id' => $metric->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check for injury risk alerts
     */
    protected function checkInjuryRiskAlerts(PerformanceMetric $metric): void
    {
        if ($metric->metric_type !== PerformanceMetric::TYPE_PHYSICAL) {
            return;
        }

        $player = $metric->player;
        $riskFactors = $this->calculateInjuryRiskFactors($player, $metric);

        if ($riskFactors['overall_risk'] > 0.7) {
            $this->createAlert(
                $metric,
                PerformanceAlert::TYPE_INJURY_RISK,
                PerformanceAlert::LEVEL_HIGH,
                'High Injury Risk Detected',
                "Player {$player->name} shows high injury risk factors. Overall risk: " . 
                number_format($riskFactors['overall_risk'] * 100, 1) . "%"
            );
        } elseif ($riskFactors['overall_risk'] > 0.5) {
            $this->createAlert(
                $metric,
                PerformanceAlert::TYPE_INJURY_RISK,
                PerformanceAlert::LEVEL_MEDIUM,
                'Moderate Injury Risk Detected',
                "Player {$player->name} shows moderate injury risk factors. Overall risk: " . 
                number_format($riskFactors['overall_risk'] * 100, 1) . "%"
            );
        }
    }

    /**
     * Check for performance decline alerts
     */
    protected function checkPerformanceDeclineAlerts(PerformanceMetric $metric): void
    {
        $recentTrends = PerformanceTrend::where('player_id', $metric->player_id)
            ->where('metric_id', $metric->id)
            ->where('trend_direction', PerformanceTrend::DIRECTION_DECREASING)
            ->where('trend_strength', PerformanceTrend::STRENGTH_STRONG)
            ->where('end_date', '>=', now()->subDays(30))
            ->get();

        if ($recentTrends->count() >= 2) {
            $player = $metric->player;
            $this->createAlert(
                $metric,
                PerformanceAlert::TYPE_PERFORMANCE_DECLINE,
                PerformanceAlert::LEVEL_HIGH,
                'Performance Decline Detected',
                "Player {$player->name} shows consistent performance decline in {$metric->metric_name}"
            );
        }
    }

    /**
     * Check for medical alerts
     */
    protected function checkMedicalAlerts(PerformanceMetric $metric): void
    {
        if ($metric->metric_type !== PerformanceMetric::TYPE_MEDICAL) {
            return;
        }

        $player = $metric->player;
        $medicalThresholds = $this->getMedicalThresholds($metric->metric_name);

        if (isset($medicalThresholds['critical']) && $metric->metric_value >= $medicalThresholds['critical']) {
            $this->createAlert(
                $metric,
                PerformanceAlert::TYPE_MEDICAL_ALERT,
                PerformanceAlert::LEVEL_CRITICAL,
                'Critical Medical Alert',
                "Critical medical value detected for {$player->name}: {$metric->metric_name} = {$metric->display_value}"
            );
        } elseif (isset($medicalThresholds['warning']) && $metric->metric_value >= $medicalThresholds['warning']) {
            $this->createAlert(
                $metric,
                PerformanceAlert::TYPE_MEDICAL_ALERT,
                PerformanceAlert::LEVEL_HIGH,
                'Medical Warning Alert',
                "Medical warning value detected for {$player->name}: {$metric->metric_name} = {$metric->display_value}"
            );
        }
    }

    /**
     * Check for compliance alerts
     */
    protected function checkComplianceAlerts(PerformanceMetric $metric): void
    {
        $player = $metric->player;
        $complianceIssues = $this->checkComplianceIssues($player);

        foreach ($complianceIssues as $issue) {
            $this->createAlert(
                $metric,
                PerformanceAlert::TYPE_COMPLIANCE_ALERT,
                $issue['level'],
                $issue['title'],
                $issue['description']
            );
        }
    }

    /**
     * Create a performance alert
     */
    protected function createAlert(
        PerformanceMetric $metric,
        string $alertType,
        string $alertLevel,
        string $title,
        string $description
    ): void {
        $player = $metric->player;

        PerformanceAlert::create([
            'alert_type' => $alertType,
            'alert_level' => $alertLevel,
            'title' => $title,
            'description' => $description,
            'player_id' => $player->id,
            'club_id' => $player->club_id,
            'team_id' => $player->current_team_id,
            'metric_id' => $metric->id,
            'trigger_value' => $metric->metric_value,
            'alert_condition' => PerformanceAlert::CONDITION_ABOVE,
            'is_active' => true,
            'notification_channels' => ['email', 'in_app'],
            'ai_recommendation' => $this->generateAIRecommendation($alertType, $metric),
            'created_by' => $metric->created_by,
        ]);
    }

    /**
     * Calculate injury risk factors for a player
     */
    protected function calculateInjuryRiskFactors(Player $player, PerformanceMetric $metric): array
    {
        $riskFactors = [
            'fatigue' => 0.0,
            'load' => 0.0,
            'recovery' => 0.0,
            'history' => 0.0,
            'overall_risk' => 0.0,
        ];

        // Calculate fatigue risk
        $recentLoad = PerformanceMetric::where('player_id', $player->id)
            ->where('metric_type', PerformanceMetric::TYPE_PHYSICAL)
            ->whereIn('metric_name', ['training_load', 'match_load', 'total_distance'])
            ->where('measurement_date', '>=', now()->subDays(7))
            ->avg('metric_value');

        if ($recentLoad > 80) {
            $riskFactors['fatigue'] = 0.8;
        } elseif ($recentLoad > 60) {
            $riskFactors['fatigue'] = 0.5;
        }

        // Calculate recovery risk
        $recoveryMetrics = PerformanceMetric::where('player_id', $player->id)
            ->where('metric_type', PerformanceMetric::TYPE_PHYSICAL)
            ->whereIn('metric_name', ['sleep_quality', 'heart_rate_variability', 'muscle_soreness'])
            ->where('measurement_date', '>=', now()->subDays(3))
            ->get();

        $recoveryScore = $recoveryMetrics->avg('metric_value');
        if ($recoveryScore < 30) {
            $riskFactors['recovery'] = 0.9;
        } elseif ($recoveryScore < 50) {
            $riskFactors['recovery'] = 0.6;
        }

        // Calculate injury history risk
        $recentInjuries = $player->healthRecords()
            ->where('record_type', 'injury')
            ->where('created_at', '>=', now()->subMonths(6))
            ->count();

        if ($recentInjuries >= 2) {
            $riskFactors['history'] = 0.7;
        } elseif ($recentInjuries >= 1) {
            $riskFactors['history'] = 0.4;
        }

        // Calculate overall risk
        $riskFactors['overall_risk'] = array_sum($riskFactors) / count($riskFactors);

        return $riskFactors;
    }

    /**
     * Get medical thresholds for different metrics
     */
    protected function getMedicalThresholds(string $metricName): array
    {
        $thresholds = [
            'heart_rate' => [
                'warning' => 100,
                'critical' => 120,
            ],
            'blood_pressure_systolic' => [
                'warning' => 140,
                'critical' => 160,
            ],
            'blood_pressure_diastolic' => [
                'warning' => 90,
                'critical' => 100,
            ],
            'temperature' => [
                'warning' => 37.5,
                'critical' => 38.0,
            ],
            'oxygen_saturation' => [
                'warning' => 95,
                'critical' => 90,
            ],
        ];

        return $thresholds[$metricName] ?? [];
    }

    /**
     * Check compliance issues for a player
     */
    protected function checkComplianceIssues(Player $player): array
    {
        $issues = [];

        // Check for missing medical certificates
        $medicalCertExpiry = $player->medical_certificate_expiry;
        if ($medicalCertExpiry && $medicalCertExpiry->isPast()) {
            $issues[] = [
                'level' => PerformanceAlert::LEVEL_HIGH,
                'title' => 'Medical Certificate Expired',
                'description' => "Player {$player->name} has an expired medical certificate",
            ];
        } elseif ($medicalCertExpiry && $medicalCertExpiry->diffInDays(now()) <= 30) {
            $issues[] = [
                'level' => PerformanceAlert::LEVEL_MEDIUM,
                'title' => 'Medical Certificate Expiring Soon',
                'description' => "Player {$player->name} medical certificate expires in {$medicalCertExpiry->diffInDays(now())} days",
            ];
        }

        // Check for missing performance tests
        $lastPerformanceTest = PerformanceMetric::where('player_id', $player->id)
            ->where('metric_type', PerformanceMetric::TYPE_PHYSICAL)
            ->where('metric_name', 'performance_test')
            ->latest('measurement_date')
            ->first();

        if (!$lastPerformanceTest || $lastPerformanceTest->measurement_date->diffInDays(now()) > 90) {
            $issues[] = [
                'level' => PerformanceAlert::LEVEL_MEDIUM,
                'title' => 'Performance Test Overdue',
                'description' => "Player {$player->name} needs a performance test",
            ];
        }

        return $issues;
    }

    /**
     * Generate AI recommendation for an alert
     */
    protected function generateAIRecommendation(string $alertType, PerformanceMetric $metric): string
    {
        $recommendations = [
            PerformanceAlert::TYPE_INJURY_RISK => [
                'Reduce training load by 20-30%',
                'Increase recovery time between sessions',
                'Monitor fatigue levels closely',
                'Consider consultation with medical staff',
            ],
            PerformanceAlert::TYPE_PERFORMANCE_DECLINE => [
                'Review training program effectiveness',
                'Assess player motivation and mental state',
                'Check for underlying health issues',
                'Consider adjusting training intensity',
            ],
            PerformanceAlert::TYPE_MEDICAL_ALERT => [
                'Immediate medical evaluation required',
                'Monitor vital signs closely',
                'Consider temporary training suspension',
                'Consult with medical professionals',
            ],
            PerformanceAlert::TYPE_COMPLIANCE_ALERT => [
                'Schedule required medical examinations',
                'Update player documentation',
                'Ensure regulatory compliance',
                'Follow up with administrative staff',
            ],
        ];

        $recommendationList = $recommendations[$alertType] ?? ['Monitor situation closely'];
        return implode('. ', $recommendationList);
    }

    /**
     * Get start date for trend period
     */
    protected function getStartDateForPeriod(Carbon $endDate, string $period): Carbon
    {
        return match ($period) {
            'daily' => $endDate->copy()->subDay(),
            'weekly' => $endDate->copy()->subWeek(),
            'monthly' => $endDate->copy()->subMonth(),
            'quarterly' => $endDate->copy()->subQuarter(),
            'yearly' => $endDate->copy()->subYear(),
            default => $endDate->copy()->subWeek(),
        };
    }

    /**
     * Calculate change percentage
     */
    protected function calculateChangePercentage(float $initialValue, float $finalValue): float
    {
        if ($initialValue == 0) {
            return 0;
        }

        return (($finalValue - $initialValue) / $initialValue) * 100;
    }

    /**
     * Determine trend direction
     */
    protected function determineTrendDirection(float $changePercentage): string
    {
        $threshold = 5.0; // 5% threshold for significant change

        if (abs($changePercentage) < $threshold) {
            return PerformanceTrend::DIRECTION_STABLE;
        }

        return $changePercentage > 0 ? PerformanceTrend::DIRECTION_INCREASING : PerformanceTrend::DIRECTION_DECREASING;
    }

    /**
     * Determine trend strength
     */
    protected function determineTrendStrength(float $changePercentage): string
    {
        $absoluteChange = abs($changePercentage);

        if ($absoluteChange < 10.0) {
            return PerformanceTrend::STRENGTH_WEAK;
        } elseif ($absoluteChange < 25.0) {
            return PerformanceTrend::STRENGTH_MODERATE;
        } else {
            return PerformanceTrend::STRENGTH_STRONG;
        }
    }

    /**
     * Calculate confidence level for trend analysis
     */
    protected function calculateConfidenceLevel($dataPoints): float
    {
        $count = $dataPoints->count();
        
        if ($count >= 10) {
            return 1.0;
        } elseif ($count >= 5) {
            return 0.8;
        } elseif ($count >= 3) {
            return 0.6;
        } else {
            return 0.4;
        }
    }

    /**
     * Generate trend analysis notes
     */
    protected function generateTrendNotes(PerformanceMetric $metric, float $changePercentage, string $period): string
    {
        $direction = $changePercentage > 0 ? 'increased' : 'decreased';
        $absoluteChange = abs($changePercentage);
        
        return "Over the {$period} period, {$metric->metric_name} has {$direction} by " . 
               number_format($absoluteChange, 1) . "%. " .
               "This represents a " . $this->determineTrendStrength($changePercentage) . " trend.";
    }
} 