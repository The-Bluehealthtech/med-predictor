<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Player;
use App\Models\PlayerPerformance;
use App\Models\PlayerMedicalRecord;
use Exception;

class AiIntelligenceService
{
    /**
     * Configuration de l'IA
     */
    protected array $config;

    public function __construct()
    {
        $this->config = config('fit-v3.ai');
    }

    /**
     * Prédiction de performance d'un joueur
     */
    public function predictPlayerPerformance(int $playerId, array $context = []): array
    {
        try {
            $cacheKey = "ai:performance:{$playerId}:" . md5(json_encode($context));
            
            // Vérifier le cache
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $player = Player::with(['performances', 'medicalRecords'])->find($playerId);
            if (!$player) {
                throw new Exception("Joueur non trouvé: {$playerId}");
            }

            // Préparer les données pour l'IA
            $inputData = $this->preparePerformanceData($player, $context);
            
            // Appeler l'API d'IA
            $prediction = $this->callAiApi('performance_prediction', $inputData);
            
            // Traiter et valider la prédiction
            $result = $this->processPerformancePrediction($prediction, $player);
            
            // Mettre en cache
            Cache::put($cacheKey, $result, $this->config['prediction_cache_ttl']);
            
            Log::info("Prédiction de performance IA générée pour le joueur {$playerId}", [
                'accuracy' => $result['accuracy'],
                'confidence' => $result['confidence']
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error("Erreur lors de la prédiction de performance IA", [
                'player_id' => $playerId,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackPrediction($playerId);
        }
    }

    /**
     * Prédiction de risque de blessure
     */
    public function predictInjuryRisk(int $playerId): array
    {
        try {
            $cacheKey = "ai:injury:{$playerId}";
            
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $player = Player::with(['medicalRecords', 'performances'])->find($playerId);
            if (!$player) {
                throw new Exception("Joueur non trouvé: {$playerId}");
            }

            $inputData = $this->prepareInjuryData($player);
            $prediction = $this->callAiApi('injury_prediction', $inputData);
            $result = $this->processInjuryPrediction($prediction, $player);
            
            Cache::put($cacheKey, $result, $this->config['prediction_cache_ttl']);
            
            return $result;

        } catch (Exception $e) {
            Log::error("Erreur lors de la prédiction de blessure IA", [
                'player_id' => $playerId,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackInjuryPrediction($playerId);
        }
    }

    /**
     * Prédiction de valeur marchande
     */
    public function predictMarketValue(int $playerId): array
    {
        try {
            $cacheKey = "ai:market:{$playerId}";
            
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $player = Player::with(['performances', 'contracts'])->find($playerId);
            if (!$player) {
                throw new Exception("Joueur non trouvé: {$playerId}");
            }

            $inputData = $this->prepareMarketData($player);
            $prediction = $this->callAiApi('market_value_prediction', $inputData);
            $result = $this->processMarketPrediction($prediction, $player);
            
            Cache::put($cacheKey, $result, $this->config['prediction_cache_ttl']);
            
            return $result;

        } catch (Exception $e) {
            Log::error("Erreur lors de la prédiction de valeur marchande IA", [
                'player_id' => $playerId,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackMarketPrediction($playerId);
        }
    }

    /**
     * Recommandations intelligentes pour entraîneur
     */
    public function generateCoachRecommendations(int $playerId): array
    {
        try {
            $performance = $this->predictPlayerPerformance($playerId);
            $injury = $this->predictInjuryRisk($playerId);
            
            $recommendations = [
                'training_intensity' => $this->calculateTrainingIntensity($performance, $injury),
                'recovery_time' => $this->calculateRecoveryTime($injury),
                'position_focus' => $this->suggestPositionFocus($performance),
                'skill_development' => $this->suggestSkillDevelopment($performance),
                'rest_days' => $this->suggestRestDays($injury, $performance),
            ];

            Log::info("Recommandations IA générées pour le joueur {$playerId}", [
                'recommendations_count' => count($recommendations)
            ]);

            return $recommendations;

        } catch (Exception $e) {
            Log::error("Erreur lors de la génération de recommandations IA", [
                'player_id' => $playerId,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackRecommendations();
        }
    }

    /**
     * Détection d'anomalies dans les performances
     */
    public function detectPerformanceAnomalies(int $playerId): array
    {
        try {
            $player = Player::with(['performances'])->find($playerId);
            if (!$player) {
                throw new Exception("Joueur non trouvé: {$playerId}");
            }

            $performances = $player->performances()->orderBy('date', 'desc')->limit(10)->get();
            
            if ($performances->isEmpty()) {
                return ['anomalies' => [], 'risk_level' => 'low'];
            }

            $data = $performances->map(function ($perf) {
                return [
                    'goals' => $perf->goals ?? 0,
                    'assists' => $perf->assists ?? 0,
                    'minutes_played' => $perf->minutes_played ?? 0,
                    'rating' => $perf->rating ?? 0,
                    'date' => $perf->date,
                ];
            })->toArray();

            $anomalies = $this->callAiApi('anomaly_detection', $data);
            
            return $this->processAnomalyDetection($anomalies, $player);

        } catch (Exception $e) {
            Log::error("Erreur lors de la détection d'anomalies IA", [
                'player_id' => $playerId,
                'error' => $e->getMessage()
            ]);

            return ['anomalies' => [], 'risk_level' => 'low'];
        }
    }

    /**
     * Appel à l'API d'IA
     */
    protected function callAiApi(string $endpoint, array $data): array
    {
        if (!$this->config['enabled']) {
            throw new Exception("IA désactivée dans la configuration");
        }

        $response = Http::timeout($this->config['api']['timeout'])
            ->retry($this->config['api']['retry_attempts'], 1000)
            ->post($this->config['api']['endpoint'] . "/{$endpoint}", [
                'data' => $data,
                'model' => $this->config['algorithms'][$endpoint]['model'] ?? 'default',
                'timestamp' => now()->timestamp,
            ]);

        if (!$response->successful()) {
            throw new Exception("Erreur API IA: " . $response->status());
        }

        return $response->json();
    }

    /**
     * Préparation des données de performance
     */
    protected function preparePerformanceData(Player $player, array $context): array
    {
        $performances = $player->performances()
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get();

        $data = [
            'player_info' => [
                'age' => $player->age,
                'position' => $player->position,
                'height' => $player->height,
                'weight' => $player->weight,
                'experience_years' => $player->experience_years ?? 0,
            ],
            'recent_performances' => $performances->map(function ($perf) {
                return [
                    'goals' => $perf->goals ?? 0,
                    'assists' => $perf->assists ?? 0,
                    'minutes_played' => $perf->minutes_played ?? 0,
                    'rating' => $perf->rating ?? 0,
                    'date' => $perf->date,
                    'opponent_strength' => $perf->opponent_strength ?? 50,
                ];
            })->toArray(),
            'context' => $context,
        ];

        return $data;
    }

    /**
     * Préparation des données de blessure
     */
    protected function prepareInjuryData(Player $player): array
    {
        $medicalRecords = $player->medicalRecords()
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        $data = [
            'player_info' => [
                'age' => $player->age,
                'position' => $player->position,
                'height' => $player->height,
                'weight' => $player->weight,
            ],
            'medical_history' => $medicalRecords->map(function ($record) {
                return [
                    'injury_type' => $record->injury_type ?? 'none',
                    'severity' => $record->severity ?? 'low',
                    'recovery_days' => $record->recovery_days ?? 0,
                    'date' => $record->date,
                ];
            })->toArray(),
            'recent_fatigue' => $this->calculateRecentFatigue($player),
        ];

        return $data;
    }

    /**
     * Préparation des données de marché
     */
    protected function prepareMarketData(Player $player): array
    {
        $performances = $player->performances()
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        $data = [
            'player_info' => [
                'age' => $player->age,
                'position' => $player->position,
                'nationality' => $player->nationality,
                'current_value' => $player->market_value ?? 0,
            ],
            'performance_trends' => $this->calculatePerformanceTrends($performances),
            'market_factors' => [
                'transfer_window' => $this->isTransferWindow(),
                'league_level' => $player->league_level ?? 'unknown',
                'contract_expiry' => $player->contract_expiry ?? null,
            ],
        ];

        return $data;
    }

    /**
     * Traitement de la prédiction de performance
     */
    protected function processPerformancePrediction(array $prediction, Player $player): array
    {
        $accuracy = $prediction['accuracy'] ?? 0.0;
        $confidence = $prediction['confidence'] ?? 0.0;

        // Vérifier le seuil de précision
        if ($accuracy < $this->config['algorithms']['performance_prediction']['accuracy_threshold']) {
            Log::warning("Précision IA insuffisante pour la prédiction de performance", [
                'player_id' => $player->id,
                'accuracy' => $accuracy,
                'threshold' => $this->config['algorithms']['performance_prediction']['accuracy_threshold']
            ]);
        }

        return [
            'player_id' => $player->id,
            'predicted_rating' => $prediction['predicted_rating'] ?? 6.0,
            'predicted_goals' => $prediction['predicted_goals'] ?? 0,
            'predicted_assists' => $prediction['predicted_assists'] ?? 0,
            'confidence' => $confidence,
            'accuracy' => $accuracy,
            'factors' => $prediction['factors'] ?? [],
            'next_match_prediction' => $prediction['next_match'] ?? [],
            'generated_at' => now(),
            'model_version' => $this->config['algorithms']['performance_prediction']['model'],
        ];
    }

    /**
     * Traitement de la prédiction de blessure
     */
    protected function processInjuryPrediction(array $prediction, Player $player): array
    {
        $accuracy = $prediction['accuracy'] ?? 0.0;
        $riskLevel = $prediction['risk_level'] ?? 'low';

        return [
            'player_id' => $player->id,
            'injury_risk' => $riskLevel,
            'risk_score' => $prediction['risk_score'] ?? 0.0,
            'predicted_injuries' => $prediction['predicted_injuries'] ?? [],
            'prevention_recommendations' => $prediction['prevention'] ?? [],
            'confidence' => $prediction['confidence'] ?? 0.0,
            'accuracy' => $accuracy,
            'generated_at' => now(),
            'model_version' => $this->config['algorithms']['injury_prediction']['model'],
        ];
    }

    /**
     * Traitement de la prédiction de marché
     */
    protected function processMarketPrediction(array $prediction, Player $player): array
    {
        $accuracy = $prediction['accuracy'] ?? 0.0;

        return [
            'player_id' => $player->id,
            'predicted_value' => $prediction['predicted_value'] ?? 0,
            'value_change_percentage' => $prediction['value_change'] ?? 0.0,
            'market_trends' => $prediction['trends'] ?? [],
            'transfer_recommendations' => $prediction['recommendations'] ?? [],
            'confidence' => $prediction['confidence'] ?? 0.0,
            'accuracy' => $accuracy,
            'generated_at' => now(),
            'model_version' => $this->config['algorithms']['market_value_prediction']['model'],
        ];
    }

    /**
     * Calcul de l'intensité d'entraînement recommandée
     */
    protected function calculateTrainingIntensity(array $performance, array $injury): string
    {
        $riskLevel = $injury['injury_risk'] ?? 'low';
        $performanceLevel = $performance['predicted_rating'] ?? 6.0;

        if ($riskLevel === 'high') {
            return 'low';
        } elseif ($riskLevel === 'medium') {
            return 'medium';
        } elseif ($performanceLevel < 5.0) {
            return 'high';
        } else {
            return 'medium';
        }
    }

    /**
     * Calcul du temps de récupération recommandé
     */
    protected function calculateRecoveryTime(array $injury): int
    {
        $riskLevel = $injury['injury_risk'] ?? 'low';
        
        return match($riskLevel) {
            'high' => 48,
            'medium' => 24,
            'low' => 12,
            default => 24,
        };
    }

    /**
     * Suggestions de focus de position
     */
    protected function suggestPositionFocus(array $performance): array
    {
        $rating = $performance['predicted_rating'] ?? 6.0;
        
        if ($rating < 5.5) {
            return ['basic_skills', 'positioning', 'tactical_understanding'];
        } elseif ($rating < 7.0) {
            return ['advanced_skills', 'decision_making', 'leadership'];
        } else {
            return ['excellence', 'innovation', 'mentoring'];
        }
    }

    /**
     * Suggestions de développement de compétences
     */
    protected function suggestSkillDevelopment(array $performance): array
    {
        $goals = $performance['predicted_goals'] ?? 0;
        $assists = $performance['predicted_assists'] ?? 0;
        
        $suggestions = [];
        
        if ($goals < 5) {
            $suggestions[] = 'finishing';
        }
        if ($assists < 3) {
            $suggestions[] = 'passing';
        }
        
        $suggestions[] = 'fitness';
        $suggestions[] = 'tactical_awareness';
        
        return array_unique($suggestions);
    }

    /**
     * Suggestions de jours de repos
     */
    protected function suggestRestDays(array $injury, array $performance): int
    {
        $riskLevel = $injury['injury_risk'] ?? 'low';
        $rating = $performance['predicted_rating'] ?? 6.0;
        
        if ($riskLevel === 'high') {
            return 2;
        } elseif ($rating < 5.5) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Calcul de la fatigue récente
     */
    protected function calculateRecentFatigue(Player $player): float
    {
        // Logique simplifiée pour calculer la fatigue
        $recentMatches = $player->performances()
            ->where('date', '>=', now()->subDays(7))
            ->count();
            
        return min(1.0, $recentMatches / 3.0);
    }

    /**
     * Calcul des tendances de performance
     */
    protected function calculatePerformanceTrends($performances): array
    {
        if ($performances->count() < 2) {
            return ['trend' => 'stable', 'change' => 0.0];
        }

        $recent = $performances->take(5)->avg('rating') ?? 6.0;
        $older = $performances->skip(5)->take(5)->avg('rating') ?? 6.0;
        
        $change = $recent - $older;
        
        return [
            'trend' => $change > 0.5 ? 'improving' : ($change < -0.5 ? 'declining' : 'stable'),
            'change' => round($change, 2),
        ];
    }

    /**
     * Vérifier si c'est la période de transfert
     */
    protected function isTransferWindow(): bool
    {
        $month = now()->month;
        return in_array($month, [1, 2, 7, 8]); // Janvier, Février, Juillet, Août
    }

    /**
     * Prédictions de fallback en cas d'erreur
     */
    protected function getFallbackPrediction(int $playerId): array
    {
        return [
            'player_id' => $playerId,
            'predicted_rating' => 6.0,
            'predicted_goals' => 0,
            'predicted_assists' => 0,
            'confidence' => 0.0,
            'accuracy' => 0.0,
            'factors' => ['fallback_mode'],
            'next_match_prediction' => [],
            'generated_at' => now(),
            'model_version' => 'fallback',
        ];
    }

    protected function getFallbackInjuryPrediction(int $playerId): array
    {
        return [
            'player_id' => $playerId,
            'injury_risk' => 'low',
            'risk_score' => 0.1,
            'predicted_injuries' => [],
            'prevention_recommendations' => ['maintain_current_routine'],
            'confidence' => 0.0,
            'accuracy' => 0.0,
            'generated_at' => now(),
            'model_version' => 'fallback',
        ];
    }

    protected function getFallbackMarketPrediction(int $playerId): array
    {
        return [
            'player_id' => $playerId,
            'predicted_value' => 0,
            'value_change_percentage' => 0.0,
            'market_trends' => ['stable'],
            'transfer_recommendations' => ['maintain_current_contract'],
            'confidence' => 0.0,
            'accuracy' => 0.0,
            'generated_at' => now(),
            'model_version' => 'fallback',
        ];
    }

    protected function getFallbackRecommendations(): array
    {
        return [
            'training_intensity' => 'medium',
            'recovery_time' => 24,
            'position_focus' => ['basic_skills', 'fitness'],
            'skill_development' => ['tactical_awareness'],
            'rest_days' => 1,
        ];
    }

    /**
     * Traitement de la détection d'anomalies
     */
    protected function processAnomalyDetection(array $anomalies, Player $player): array
    {
        return [
            'player_id' => $player->id,
            'anomalies' => $anomalies['detected'] ?? [],
            'risk_level' => $anomalies['risk_level'] ?? 'low',
            'confidence' => $anomalies['confidence'] ?? 0.0,
            'generated_at' => now(),
        ];
    }
}
