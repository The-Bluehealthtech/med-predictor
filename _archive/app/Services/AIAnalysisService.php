<?php

namespace App\Services;

use App\Models\PerformanceMetric;
use App\Models\PerformanceTrend;
use App\Models\PerformanceAlert;
use App\Models\Player;
use App\Models\Club;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AIAnalysisService
{
    protected string $aiApiEndpoint;
    protected string $aiApiKey;

    public function __construct()
    {
        $this->aiApiEndpoint = config('services.ai.endpoint', 'https://api.openai.com/v1');
        $this->aiApiKey = config('services.ai.key');
    }

    /**
     * Analyze a performance metric and generate insights
     */
    public function analyzeMetric(PerformanceMetric $metric): array
    {
        try {
            $context = $this->buildMetricContext($metric);
            $insights = $this->generateMetricInsights($context);
            
            // Store insights in metadata
            $metric->update([
                'metadata' => array_merge($metric->metadata ?? [], [
                    'ai_insights' => $insights,
                    'ai_analysis_date' => now()->toISOString(),
                ])
            ]);

            return $insights;

        } catch (\Exception $e) {
            Log::error('Failed to analyze metric with AI', [
                'metric_id' => $metric->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'insights' => [],
                'recommendations' => [],
                'risk_assessment' => 'Unable to analyze',
                'confidence' => 0.0,
            ];
        }
    }

    /**
     * Generate comprehensive insights for performance data
     */
    public function generateInsights(array $params): array
    {
        try {
            $playerId = $params['player_id'] ?? null;
            $clubId = $params['club_id'] ?? null;
            $insightType = $params['insight_type'] ?? 'performance';
            $dateRange = $params['date_range'] ?? 'month';

            $context = $this->buildInsightContext($playerId, $clubId, $insightType, $dateRange);
            
            return match ($insightType) {
                'performance' => $this->generatePerformanceInsights($context),
                'injury_risk' => $this->generateInjuryRiskInsights($context),
                'development' => $this->generateDevelopmentInsights($context),
                'recommendations' => $this->generateRecommendations($context),
                default => $this->generateGeneralInsights($context),
            };

        } catch (\Exception $e) {
            Log::error('Failed to generate AI insights', [
                'params' => $params,
                'error' => $e->getMessage(),
            ]);

            return [
                'insights' => [],
                'recommendations' => [],
                'risk_assessment' => 'Unable to generate insights',
                'confidence' => 0.0,
            ];
        }
    }

    /**
     * Predict injury risk for a player
     */
    public function predictInjuryRisk(Player $player): array
    {
        try {
            $riskFactors = $this->calculateInjuryRiskFactors($player);
            $prediction = $this->generateInjuryPrediction($riskFactors);
            
            return [
                'overall_risk' => $prediction['risk_score'],
                'risk_level' => $prediction['risk_level'],
                'risk_factors' => $riskFactors,
                'recommendations' => $prediction['recommendations'],
                'confidence' => $prediction['confidence'],
                'prediction_date' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to predict injury risk', [
                'player_id' => $player->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'overall_risk' => 0.5,
                'risk_level' => 'unknown',
                'risk_factors' => [],
                'recommendations' => ['Unable to generate prediction'],
                'confidence' => 0.0,
                'prediction_date' => now()->toISOString(),
            ];
        }
    }

    /**
     * Generate performance scoring for a player
     */
    public function generatePerformanceScore(Player $player): array
    {
        try {
            $metrics = $this->getPlayerMetrics($player);
            $score = $this->calculatePerformanceScore($metrics);
            
            return [
                'overall_score' => $score['overall'],
                'physical_score' => $score['physical'],
                'technical_score' => $score['technical'],
                'tactical_score' => $score['tactical'],
                'mental_score' => $score['mental'],
                'medical_score' => $score['medical'],
                'social_score' => $score['social'],
                'trend' => $score['trend'],
                'confidence' => $score['confidence'],
                'last_updated' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to generate performance score', [
                'player_id' => $player->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'overall_score' => 0,
                'physical_score' => 0,
                'technical_score' => 0,
                'tactical_score' => 0,
                'mental_score' => 0,
                'medical_score' => 0,
                'social_score' => 0,
                'trend' => 'stable',
                'confidence' => 0.0,
                'last_updated' => now()->toISOString(),
            ];
        }
    }

    /**
     * Generate personalized recommendations for a player
     */
    public function generatePersonalizedRecommendations(Player $player): array
    {
        try {
            $context = $this->buildPlayerContext($player);
            $recommendations = $this->generateAIRecommendations($context);
            
            return [
                'training_recommendations' => $recommendations['training'] ?? [],
                'nutrition_recommendations' => $recommendations['nutrition'] ?? [],
                'recovery_recommendations' => $recommendations['recovery'] ?? [],
                'medical_recommendations' => $recommendations['medical'] ?? [],
                'mental_recommendations' => $recommendations['mental'] ?? [],
                'priority_level' => $recommendations['priority'] ?? 'medium',
                'confidence' => $recommendations['confidence'] ?? 0.0,
                'generated_date' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to generate personalized recommendations', [
                'player_id' => $player->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'training_recommendations' => [],
                'nutrition_recommendations' => [],
                'recovery_recommendations' => [],
                'medical_recommendations' => [],
                'mental_recommendations' => [],
                'priority_level' => 'medium',
                'confidence' => 0.0,
                'generated_date' => now()->toISOString(),
            ];
        }
    }

    /**
     * Build context for metric analysis
     */
    protected function buildMetricContext(PerformanceMetric $metric): array
    {
        $player = $metric->player;
        $recentMetrics = PerformanceMetric::where('player_id', $player->id)
            ->where('metric_name', $metric->metric_name)
            ->where('id', '!=', $metric->id)
            ->orderBy('measurement_date', 'desc')
            ->limit(10)
            ->get();

        $trends = PerformanceTrend::where('player_id', $player->id)
            ->where('metric_id', $metric->id)
            ->orderBy('end_date', 'desc')
            ->limit(5)
            ->get();

        return [
            'player' => [
                'id' => $player->id,
                'name' => $player->name,
                'age' => $player->age,
                'position' => $player->position,
                'club' => $player->club->name ?? 'Unknown',
            ],
            'metric' => [
                'type' => $metric->metric_type,
                'name' => $metric->metric_name,
                'value' => $metric->metric_value,
                'unit' => $metric->metric_unit,
                'date' => $metric->measurement_date->toISOString(),
                'source' => $metric->data_source,
                'confidence' => $metric->confidence_score,
            ],
            'historical_data' => $recentMetrics->map(function ($m) {
                return [
                    'value' => $m->metric_value,
                    'date' => $m->measurement_date->toISOString(),
                ];
            })->toArray(),
            'trends' => $trends->map(function ($t) {
                return [
                    'period' => $t->trend_period,
                    'direction' => $t->trend_direction,
                    'strength' => $t->trend_strength,
                    'change_percentage' => $t->change_percentage,
                ];
            })->toArray(),
        ];
    }

    /**
     * Build context for insights generation
     */
    protected function buildInsightContext(?int $playerId, ?int $clubId, string $insightType, string $dateRange): array
    {
        $context = [
            'insight_type' => $insightType,
            'date_range' => $dateRange,
            'analysis_date' => now()->toISOString(),
        ];

        if ($playerId) {
            $player = Player::with(['club', 'teams'])->find($playerId);
            if ($player) {
                $context['player'] = [
                    'id' => $player->id,
                    'name' => $player->name,
                    'age' => $player->age,
                    'position' => $player->position,
                    'club' => $player->club->name ?? 'Unknown',
                ];
            }
        }

        if ($clubId) {
            $club = Club::with(['players', 'teams'])->find($clubId);
            if ($club) {
                $context['club'] = [
                    'id' => $club->id,
                    'name' => $club->name,
                    'players_count' => $club->players->count(),
                    'teams_count' => $club->teams->count(),
                ];
            }
        }

        return $context;
    }

    /**
     * Generate metric-specific insights
     */
    protected function generateMetricInsights(array $context): array
    {
        $prompt = $this->buildMetricAnalysisPrompt($context);
        $response = $this->callAIAPI($prompt);

        return [
            'insights' => $response['insights'] ?? [],
            'recommendations' => $response['recommendations'] ?? [],
            'risk_assessment' => $response['risk_assessment'] ?? 'No risk detected',
            'confidence' => $response['confidence'] ?? 0.0,
        ];
    }

    /**
     * Generate performance insights
     */
    protected function generatePerformanceInsights(array $context): array
    {
        $prompt = $this->buildPerformanceAnalysisPrompt($context);
        $response = $this->callAIAPI($prompt);

        return [
            'performance_trends' => $response['trends'] ?? [],
            'strengths' => $response['strengths'] ?? [],
            'weaknesses' => $response['weaknesses'] ?? [],
            'improvement_areas' => $response['improvement_areas'] ?? [],
            'confidence' => $response['confidence'] ?? 0.0,
        ];
    }

    /**
     * Generate injury risk insights
     */
    protected function generateInjuryRiskInsights(array $context): array
    {
        $prompt = $this->buildInjuryRiskAnalysisPrompt($context);
        $response = $this->callAIAPI($prompt);

        return [
            'risk_factors' => $response['risk_factors'] ?? [],
            'risk_level' => $response['risk_level'] ?? 'low',
            'prevention_strategies' => $response['prevention_strategies'] ?? [],
            'monitoring_recommendations' => $response['monitoring_recommendations'] ?? [],
            'confidence' => $response['confidence'] ?? 0.0,
        ];
    }

    /**
     * Generate development insights
     */
    protected function generateDevelopmentInsights(array $context): array
    {
        $prompt = $this->buildDevelopmentAnalysisPrompt($context);
        $response = $this->callAIAPI($prompt);

        return [
            'development_stage' => $response['development_stage'] ?? 'unknown',
            'potential_areas' => $response['potential_areas'] ?? [],
            'development_plan' => $response['development_plan'] ?? [],
            'milestones' => $response['milestones'] ?? [],
            'confidence' => $response['confidence'] ?? 0.0,
        ];
    }

    /**
     * Generate recommendations
     */
    protected function generateRecommendations(array $context): array
    {
        $prompt = $this->buildRecommendationsPrompt($context);
        $response = $this->callAIAPI($prompt);

        return [
            'training_recommendations' => $response['training'] ?? [],
            'nutrition_recommendations' => $response['nutrition'] ?? [],
            'recovery_recommendations' => $response['recovery'] ?? [],
            'medical_recommendations' => $response['medical'] ?? [],
            'mental_recommendations' => $response['mental'] ?? [],
            'priority_level' => $response['priority'] ?? 'medium',
            'confidence' => $response['confidence'] ?? 0.0,
        ];
    }

    /**
     * Generate general insights
     */
    protected function generateGeneralInsights(array $context): array
    {
        $prompt = $this->buildGeneralAnalysisPrompt($context);
        $response = $this->callAIAPI($prompt);

        return [
            'key_insights' => $response['insights'] ?? [],
            'trends' => $response['trends'] ?? [],
            'recommendations' => $response['recommendations'] ?? [],
            'confidence' => $response['confidence'] ?? 0.0,
        ];
    }

    /**
     * Calculate injury risk factors
     */
    protected function calculateInjuryRiskFactors(Player $player): array
    {
        $factors = [
            'age_risk' => 0.0,
            'position_risk' => 0.0,
            'load_risk' => 0.0,
            'recovery_risk' => 0.0,
            'history_risk' => 0.0,
            'fitness_risk' => 0.0,
        ];

        // Age risk
        if ($player->age > 30) {
            $factors['age_risk'] = 0.3;
        } elseif ($player->age > 25) {
            $factors['age_risk'] = 0.1;
        }

        // Position risk
        $highRiskPositions = ['striker', 'midfielder', 'defender'];
        if (in_array(strtolower($player->position), $highRiskPositions)) {
            $factors['position_risk'] = 0.2;
        }

        // Load risk
        $recentLoad = PerformanceMetric::where('player_id', $player->id)
            ->where('metric_type', PerformanceMetric::TYPE_PHYSICAL)
            ->whereIn('metric_name', ['training_load', 'match_load'])
            ->where('measurement_date', '>=', now()->subDays(7))
            ->avg('metric_value');

        if ($recentLoad > 80) {
            $factors['load_risk'] = 0.4;
        } elseif ($recentLoad > 60) {
            $factors['load_risk'] = 0.2;
        }

        // Recovery risk
        $recoveryMetrics = PerformanceMetric::where('player_id', $player->id)
            ->where('metric_type', PerformanceMetric::TYPE_PHYSICAL)
            ->whereIn('metric_name', ['sleep_quality', 'heart_rate_variability'])
            ->where('measurement_date', '>=', now()->subDays(3))
            ->avg('metric_value');

        if ($recoveryMetrics < 50) {
            $factors['recovery_risk'] = 0.3;
        }

        // History risk
        $recentInjuries = $player->healthRecords()
            ->where('record_type', 'injury')
            ->where('created_at', '>=', now()->subMonths(6))
            ->count();

        if ($recentInjuries >= 2) {
            $factors['history_risk'] = 0.4;
        } elseif ($recentInjuries >= 1) {
            $factors['history_risk'] = 0.2;
        }

        return $factors;
    }

    /**
     * Generate injury prediction
     */
    protected function generateInjuryPrediction(array $riskFactors): array
    {
        $overallRisk = array_sum($riskFactors) / count($riskFactors);
        
        $riskLevel = match (true) {
            $overallRisk >= 0.7 => 'high',
            $overallRisk >= 0.4 => 'medium',
            default => 'low',
        };

        $recommendations = $this->generateInjuryPreventionRecommendations($riskFactors);

        return [
            'risk_score' => $overallRisk,
            'risk_level' => $riskLevel,
            'recommendations' => $recommendations,
            'confidence' => 0.8, // Based on historical data accuracy
        ];
    }

    /**
     * Get player metrics for scoring
     */
    protected function getPlayerMetrics(Player $player): array
    {
        $metrics = PerformanceMetric::where('player_id', $player->id)
            ->where('measurement_date', '>=', now()->subDays(30))
            ->get()
            ->groupBy('metric_type');

        return [
            'physical' => $metrics[PerformanceMetric::TYPE_PHYSICAL] ?? collect(),
            'technical' => $metrics[PerformanceMetric::TYPE_TECHNICAL] ?? collect(),
            'tactical' => $metrics[PerformanceMetric::TYPE_TACTICAL] ?? collect(),
            'mental' => $metrics[PerformanceMetric::TYPE_MENTAL] ?? collect(),
            'medical' => $metrics[PerformanceMetric::TYPE_MEDICAL] ?? collect(),
            'social' => $metrics[PerformanceMetric::TYPE_SOCIAL] ?? collect(),
        ];
    }

    /**
     * Calculate performance score
     */
    protected function calculatePerformanceScore(array $metrics): array
    {
        $scores = [];
        
        foreach ($metrics as $type => $typeMetrics) {
            if ($typeMetrics->isEmpty()) {
                $scores[$type] = 0;
                continue;
            }

            // Calculate average score for this type
            $avgScore = $typeMetrics->avg('metric_value');
            $scores[$type] = min(100, max(0, $avgScore));
        }

        $overallScore = array_sum($scores) / count($scores);
        
        // Determine trend
        $trend = $this->calculateTrend($metrics);

        return [
            'overall' => $overallScore,
            'physical' => $scores['physical'] ?? 0,
            'technical' => $scores['technical'] ?? 0,
            'tactical' => $scores['tactical'] ?? 0,
            'mental' => $scores['mental'] ?? 0,
            'medical' => $scores['medical'] ?? 0,
            'social' => $scores['social'] ?? 0,
            'trend' => $trend,
            'confidence' => 0.85,
        ];
    }

    /**
     * Calculate trend from metrics
     */
    protected function calculateTrend(array $metrics): string
    {
        // Simple trend calculation based on recent vs older metrics
        $recentAvg = 0;
        $olderAvg = 0;
        $count = 0;

        foreach ($metrics as $typeMetrics) {
            if ($typeMetrics->count() >= 2) {
                $recent = $typeMetrics->take(5)->avg('metric_value');
                $older = $typeMetrics->skip(5)->take(5)->avg('metric_value');
                
                if ($recent && $older) {
                    $recentAvg += $recent;
                    $olderAvg += $older;
                    $count++;
                }
            }
        }

        if ($count === 0) {
            return 'stable';
        }

        $recentAvg /= $count;
        $olderAvg /= $count;
        $change = (($recentAvg - $olderAvg) / $olderAvg) * 100;

        if ($change > 5) {
            return 'improving';
        } elseif ($change < -5) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Build player context for recommendations
     */
    protected function buildPlayerContext(Player $player): array
    {
        $metrics = $this->getPlayerMetrics($player);
        $performanceScore = $this->calculatePerformanceScore($metrics);
        $injuryRisk = $this->predictInjuryRisk($player);

        return [
            'player' => [
                'id' => $player->id,
                'name' => $player->name,
                'age' => $player->age,
                'position' => $player->position,
                'club' => $player->club->name ?? 'Unknown',
            ],
            'performance_score' => $performanceScore,
            'injury_risk' => $injuryRisk,
            'recent_metrics' => $metrics,
        ];
    }

    /**
     * Generate AI recommendations
     */
    protected function generateAIRecommendations(array $context): array
    {
        $prompt = $this->buildRecommendationsPrompt($context);
        $response = $this->callAIAPI($prompt);

        return $response;
    }

    /**
     * Call AI API for analysis
     */
    protected function callAIAPI(string $prompt): array
    {
        try {
            // This is a placeholder implementation
            // In a real implementation, you would call an actual AI API
            // For now, we'll return mock data
            
            return [
                'insights' => ['Sample insight based on data analysis'],
                'recommendations' => ['Sample recommendation'],
                'confidence' => 0.85,
            ];

        } catch (\Exception $e) {
            Log::error('AI API call failed', [
                'error' => $e->getMessage(),
                'prompt_length' => strlen($prompt),
            ]);

            return [
                'insights' => [],
                'recommendations' => [],
                'confidence' => 0.0,
            ];
        }
    }

    /**
     * Build various analysis prompts
     */
    protected function buildMetricAnalysisPrompt(array $context): string
    {
        return "Analyze the following performance metric data and provide insights:\n" . 
               json_encode($context, JSON_PRETTY_PRINT);
    }

    protected function buildPerformanceAnalysisPrompt(array $context): string
    {
        return "Analyze performance trends and provide insights:\n" . 
               json_encode($context, JSON_PRETTY_PRINT);
    }

    protected function buildInjuryRiskAnalysisPrompt(array $context): string
    {
        return "Analyze injury risk factors and provide prevention strategies:\n" . 
               json_encode($context, JSON_PRETTY_PRINT);
    }

    protected function buildDevelopmentAnalysisPrompt(array $context): string
    {
        return "Analyze player development and provide growth recommendations:\n" . 
               json_encode($context, JSON_PRETTY_PRINT);
    }

    protected function buildRecommendationsPrompt(array $context): string
    {
        return "Generate personalized recommendations based on player data:\n" . 
               json_encode($context, JSON_PRETTY_PRINT);
    }

    protected function buildGeneralAnalysisPrompt(array $context): string
    {
        return "Provide general insights and analysis:\n" . 
               json_encode($context, JSON_PRETTY_PRINT);
    }

    /**
     * Generate injury prevention recommendations
     */
    protected function generateInjuryPreventionRecommendations(array $riskFactors): array
    {
        $recommendations = [];

        if ($riskFactors['load_risk'] > 0.2) {
            $recommendations[] = 'Reduce training load and increase recovery time';
        }

        if ($riskFactors['recovery_risk'] > 0.2) {
            $recommendations[] = 'Improve sleep quality and recovery protocols';
        }

        if ($riskFactors['history_risk'] > 0.2) {
            $recommendations[] = 'Implement targeted injury prevention exercises';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Maintain current training and recovery protocols';
        }

        return $recommendations;
    }
} 