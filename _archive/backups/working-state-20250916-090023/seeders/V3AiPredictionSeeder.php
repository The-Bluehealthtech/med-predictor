<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\V3\AiPrediction;
use App\Models\Player;
use Carbon\Carbon;

class V3AiPredictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = Player::take(10)->get();
        
        if ($players->isEmpty()) {
            $this->command->warn('Aucun joueur trouvé. Création des prédictions annulée.');
            return;
        }

        $predictionTypes = ['performance', 'injury', 'market_value', 'recovery'];
        $modelVersions = ['xgboost_v1.2', 'lstm_v1.1', 'random_forest_v1.0', 'neural_net_v1.3'];

        foreach ($players as $player) {
            // Créer plusieurs prédictions par joueur
            for ($i = 0; $i < rand(3, 8); $i++) {
                $predictionType = $predictionTypes[array_rand($predictionTypes)];
                $modelVersion = $modelVersions[array_rand($modelVersions)];
                
                $prediction = $this->createPrediction($player, $predictionType, $modelVersion);
                
                $this->command->info("Prédiction {$predictionType} créée pour le joueur {$player->first_name} {$player->last_name}");
            }
        }

        $this->command->info('Seeder V3 AiPrediction terminé avec succès !');
    }

    /**
     * Créer une prédiction spécifique
     */
    private function createPrediction(Player $player, string $type, string $modelVersion): AiPrediction
    {
        $inputData = $this->generateInputData($type);
        $predictionResult = $this->generatePredictionResult($type);
        
        return AiPrediction::create([
            'player_id' => $player->id,
            'prediction_type' => $type,
            'input_data' => $inputData,
            'prediction_result' => $predictionResult,
            'confidence_score' => rand(75, 98) / 100,
            'accuracy_score' => rand(80, 95) / 100,
            'model_version' => $modelVersion,
            'processing_time' => rand(50, 500) / 1000, // 0.05 à 0.5 secondes
            'cache_status' => rand(0, 1) ? 'hit' : 'miss',
            'metadata' => [
                'algorithm' => $this->getAlgorithmForType($type),
                'training_data_size' => rand(10000, 1000000),
                'last_training' => now()->subDays(rand(1, 30))->toISOString(),
                'feature_importance' => $this->getFeatureImportance($type),
            ],
            'expires_at' => now()->addHours(rand(1, 24)),
        ]);
    }

    /**
     * Générer des données d'entrée selon le type
     */
    private function generateInputData(string $type): array
    {
        switch ($type) {
            case 'performance':
                return [
                    'recent_matches' => rand(3, 10),
                    'training_sessions' => rand(5, 15),
                    'rest_days' => rand(1, 3),
                    'opponent_strength' => rand(1, 10),
                    'venue' => ['home', 'away', 'neutral'][rand(0, 2)],
                    'weather' => ['sunny', 'rainy', 'cloudy', 'windy'][rand(0, 3)],
                    'temperature' => rand(15, 35),
                    'humidity' => rand(30, 80),
                ];
                
            case 'injury':
                return [
                    'age' => rand(18, 35),
                    'previous_injuries' => rand(0, 5),
                    'training_load' => rand(60, 100),
                    'recovery_time' => rand(12, 72),
                    'sleep_quality' => rand(1, 10),
                    'stress_level' => rand(1, 10),
                    'nutrition_score' => rand(1, 10),
                    'hydration_level' => rand(70, 100),
                ];
                
            case 'market_value':
                return [
                    'age' => rand(18, 35),
                    'contract_years' => rand(1, 5),
                    'performance_rating' => rand(60, 95),
                    'international_caps' => rand(0, 100),
                    'club_competition_level' => rand(1, 5),
                    'market_demand' => rand(1, 10),
                    'economic_factors' => rand(1, 10),
                ];
                
            case 'recovery':
                return [
                    'injury_severity' => rand(1, 5),
                    'treatment_type' => ['physio', 'surgery', 'rest', 'medication'][rand(0, 3)],
                    'compliance_score' => rand(60, 100),
                    'support_network' => rand(1, 10),
                    'previous_recovery_history' => rand(1, 10),
                ];
                
            default:
                return [];
        }
    }

    /**
     * Générer un résultat de prédiction selon le type
     */
    private function generatePredictionResult(string $type): array
    {
        switch ($type) {
            case 'performance':
                return [
                    'predicted_rating' => rand(60, 95),
                    'confidence_interval' => [rand(55, 65), rand(90, 100)],
                    'key_factors' => ['form', 'motivation', 'tactical_preparation'],
                    'recommendations' => [
                        'focus_on_technical_skills',
                        'maintain_physical_condition',
                        'study_opponent_patterns'
                    ],
                ];
                
            case 'injury':
                return [
                    'risk_level' => ['low', 'medium', 'high'][rand(0, 2)],
                    'risk_percentage' => rand(5, 45),
                    'vulnerable_areas' => ['knee', 'hamstring', 'ankle', 'groin'][rand(0, 3)],
                    'prevention_measures' => [
                        'reduce_training_intensity',
                        'focus_on_recovery',
                        'monitor_fatigue_levels'
                    ],
                ];
                
            case 'market_value':
                return [
                    'current_value' => rand(1000000, 100000000),
                    'predicted_change' => rand(-20, 50),
                    'market_trend' => ['rising', 'stable', 'declining'][rand(0, 2)],
                    'influencing_factors' => [
                        'performance_consistency',
                        'market_demand',
                        'contract_situation'
                    ],
                ];
                
            case 'recovery':
                return [
                    'estimated_recovery_time' => rand(2, 12),
                    'recovery_probability' => rand(70, 95),
                    'milestones' => [
                        'week_2' => 'Return to light training',
                        'week_4' => 'Full training participation',
                        'week_6' => 'Match fitness'
                    ],
                ];
                
            default:
                return [];
        }
    }

    /**
     * Obtenir l'algorithme selon le type
     */
    private function getAlgorithmForType(string $type): string
    {
        $algorithms = [
            'performance' => 'XGBoost',
            'injury' => 'LSTM Neural Network',
            'market_value' => 'Random Forest',
            'recovery' => 'Gradient Boosting',
        ];
        
        return $algorithms[$type] ?? 'Unknown';
    }

    /**
     * Obtenir l'importance des caractéristiques
     */
    private function getFeatureImportance(string $type): array
    {
        $features = [
            'performance' => [
                'recent_form' => rand(70, 95),
                'opponent_strength' => rand(60, 90),
                'venue_factor' => rand(50, 80),
                'weather_conditions' => rand(40, 70),
            ],
            'injury' => [
                'previous_injuries' => rand(80, 95),
                'training_load' => rand(70, 90),
                'recovery_time' => rand(60, 85),
                'age_factor' => rand(50, 80),
            ],
            'market_value' => [
                'performance_rating' => rand(85, 95),
                'age_factor' => rand(70, 90),
                'contract_situation' => rand(60, 85),
                'market_demand' => rand(50, 80),
            ],
            'recovery' => [
                'injury_severity' => rand(80, 95),
                'treatment_compliance' => rand(70, 90),
                'previous_history' => rand(60, 85),
                'support_network' => rand(50, 75),
            ],
        ];
        
        return $features[$type] ?? [];
    }
}
