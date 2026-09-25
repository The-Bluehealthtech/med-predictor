<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\V3\PerformanceMetric;
use App\Models\Player;
use Carbon\Carbon;

class V3PerformanceMetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = Player::take(15)->get();
        
        if ($players->isEmpty()) {
            $this->command->warn('Aucun joueur trouvé. Création des métriques annulée.');
            return;
        }

        $metricTypes = [
            'speed' => ['km/h', 25, 35],
            'endurance' => ['%', 70, 95],
            'strength' => ['kg', 80, 120],
            'agility' => ['score', 6, 10],
            'recovery' => ['%', 60, 90],
            'passing' => ['%', 75, 95],
            'shooting' => ['%', 70, 90],
            'dribbling' => ['%', 65, 90],
            'tackling' => ['%', 70, 95],
            'positioning' => ['score', 7, 10],
            'decision_making' => ['score', 6, 10],
            'game_intelligence' => ['score', 7, 10],
            'teamwork' => ['score', 6, 10],
            'leadership' => ['score', 5, 10],
            'concentration' => ['%', 70, 95],
            'motivation' => ['%', 75, 95],
            'pressure_handling' => ['score', 6, 10],
            'adaptability' => ['score', 6, 10],
            'heart_rate' => ['bpm', 60, 85],
            'oxygen_saturation' => ['%', 95, 99],
            'lactate_threshold' => ['mmol/L', 2.5, 4.5],
            'muscle_fatigue' => ['%', 20, 60],
        ];

        $categories = [
            'match_performance',
            'training_session',
            'recovery_session',
            'medical_assessment',
            'fitness_test',
            'biometric_monitoring',
        ];

        $sources = ['manual', 'gps', 'heart_rate_monitor', 'force_plate', 'video_analysis', 'coach_assessment'];

        foreach ($players as $player) {
            // Créer des métriques pour les 30 derniers jours
            for ($day = 30; $day >= 0; $day--) {
                $timestamp = now()->subDays($day);
                
                // Créer 2-5 métriques par jour
                $metricsPerDay = rand(2, 5);
                
                for ($i = 0; $i < $metricsPerDay; $i++) {
                    $metricType = array_rand($metricTypes);
                    $metricConfig = $metricTypes[$metricType];
                    
                    $this->createMetric(
                        $player,
                        $metricType,
                        $metricConfig,
                        $categories[array_rand($categories)],
                        $sources[array_rand($sources)],
                        $timestamp
                    );
                }
            }
            
            $this->command->info("Métriques créées pour le joueur {$player->first_name} {$player->last_name}");
        }

        $this->command->info('Seeder V3 PerformanceMetric terminé avec succès !');
    }

    /**
     * Créer une métrique spécifique
     */
    private function createMetric(
        Player $player,
        string $metricType,
        array $config,
        string $category,
        string $source,
        Carbon $timestamp
    ): void {
        [$unit, $minValue, $maxValue] = $config;
        
        // Générer une valeur réaliste avec variation
        $baseValue = rand($minValue, $maxValue);
        $variation = rand(-10, 10) / 100; // ±10% de variation
        $value = $baseValue * (1 + $variation);
        
        // Ajuster selon le contexte
        $value = $this->adjustValueByContext($value, $metricType, $timestamp);
        
        // Déterminer la sous-catégorie
        $subcategory = $this->getSubcategory($metricType, $category);
        
        // Générer le contexte
        $context = $this->generateContext($metricType, $category, $timestamp);
        
        // Calculer le niveau de confiance
        $confidence = $this->calculateConfidence($source, $metricType);

        PerformanceMetric::create([
            'player_id' => $player->id,
            'metric_type' => $metricType,
            'value' => round($value, 2),
            'unit' => $unit,
            'category' => $category,
            'subcategory' => $subcategory,
            'context' => $context,
            'source' => $source,
            'confidence' => $confidence,
            'timestamp' => $timestamp,
            'metadata' => [
                'player_age' => $player->age ?? rand(18, 35),
                'position' => $player->position ?? 'Unknown',
                'measurement_conditions' => $this->getMeasurementConditions($timestamp),
                'equipment_used' => $this->getEquipmentUsed($source),
                'operator' => $this->getOperator($source),
            ],
        ]);
    }

    /**
     * Ajuster la valeur selon le contexte
     */
    private function adjustValueByContext(float $value, string $metricType, Carbon $timestamp): float
    {
        $dayOfWeek = $timestamp->dayOfWeek;
        $hour = $timestamp->hour;
        
        // Ajustements selon le jour de la semaine
        if ($dayOfWeek == 0 || $dayOfWeek == 6) { // Weekend
            if (in_array($metricType, ['motivation', 'pressure_handling'])) {
                $value *= 0.9; // Légère baisse le weekend
            }
        }
        
        // Ajustements selon l'heure
        if ($hour < 8 || $hour > 20) { // Tôt le matin ou tard le soir
            if (in_array($metricType, ['concentration', 'reaction_time'])) {
                $value *= 0.85; // Baisse de performance
            }
        }
        
        // Ajustements saisonniers
        $month = $timestamp->month;
        if ($month >= 12 || $month <= 2) { // Hiver
            if (in_array($metricType, ['speed', 'agility'])) {
                $value *= 0.95; // Légère baisse en hiver
            }
        }
        
        return $value;
    }

    /**
     * Obtenir la sous-catégorie
     */
    private function getSubcategory(string $metricType, string $category): ?string
    {
        $subcategories = [
            'match_performance' => ['first_half', 'second_half', 'extra_time', 'penalties'],
            'training_session' => ['warm_up', 'main_session', 'cool_down', 'recovery'],
            'medical_assessment' => ['pre_match', 'post_match', 'routine', 'injury_follow_up'],
            'fitness_test' => ['aerobic', 'anaerobic', 'strength', 'flexibility'],
        ];
        
        if (isset($subcategories[$category])) {
            return $subcategories[$category][array_rand($subcategories[$category])];
        }
        
        return null;
    }

    /**
     * Générer le contexte
     */
    private function generateContext(string $metricType, string $category, Carbon $timestamp): array
    {
        $context = [
            'session_type' => $category,
            'measurement_time' => $timestamp->format('H:i'),
            'weather_conditions' => $this->getRandomWeather(),
            'temperature' => rand(15, 35),
            'humidity' => rand(30, 80),
        ];
        
        if ($category === 'match_performance') {
            $context['match_importance'] = ['low', 'medium', 'high'][rand(0, 2)];
            $context['opponent_strength'] = rand(1, 10);
            $context['venue'] = ['home', 'away', 'neutral'][rand(0, 2)];
        }
        
        if ($category === 'training_session') {
            $context['session_intensity'] = ['low', 'medium', 'high'][rand(0, 2)];
            $context['focus_area'] = ['technical', 'tactical', 'physical', 'mental'][rand(0, 3)];
        }
        
        return $context;
    }

    /**
     * Calculer le niveau de confiance
     */
    private function calculateConfidence(string $source, string $metricType): float
    {
        $baseConfidence = [
            'manual' => 0.7,
            'gps' => 0.95,
            'heart_rate_monitor' => 0.98,
            'force_plate' => 0.92,
            'video_analysis' => 0.88,
            'coach_assessment' => 0.75,
        ];
        
        $confidence = $baseConfidence[$source] ?? 0.8;
        
        // Ajuster selon le type de métrique
        if (in_array($metricType, ['heart_rate', 'oxygen_saturation'])) {
            $confidence = min(0.98, $confidence + 0.05); // Plus précis pour les métriques biométriques
        }
        
        // Ajouter une petite variation
        $variation = rand(-5, 5) / 100;
        $confidence = max(0.5, min(1.0, $confidence + $variation));
        
        return round($confidence, 4);
    }

    /**
     * Obtenir les conditions de mesure
     */
    private function getMeasurementConditions(Carbon $timestamp): array
    {
        return [
            'indoor_outdoor' => rand(0, 1) ? 'indoor' : 'outdoor',
            'surface_type' => ['grass', 'artificial', 'indoor', 'gym'][rand(0, 3)],
            'lighting' => ['natural', 'artificial', 'mixed'][rand(0, 2)],
            'noise_level' => ['low', 'medium', 'high'][rand(0, 2)],
        ];
    }

    /**
     * Obtenir l'équipement utilisé
     */
    private function getEquipmentUsed(string $source): string
    {
        $equipment = [
            'gps' => 'GPS Tracker Pro X1',
            'heart_rate_monitor' => 'Polar H10',
            'force_plate' => 'Kistler Force Plate',
            'video_analysis' => 'Dartfish ProSuite',
            'manual' => 'Stopwatch & Notebook',
            'coach_assessment' => 'Assessment Form',
        ];
        
        return $equipment[$source] ?? 'Standard Equipment';
    }

    /**
     * Obtenir l'opérateur
     */
    private function getOperator(string $source): string
    {
        $operators = [
            'gps' => 'GPS System',
            'heart_rate_monitor' => 'Heart Rate Monitor',
            'force_plate' => 'Biomechanics Lab',
            'video_analysis' => 'Video Analyst',
            'manual' => 'Coach/Staff',
            'coach_assessment' => 'Head Coach',
        ];
        
        return $operators[$source] ?? 'Staff Member';
    }

    /**
     * Obtenir la météo aléatoire
     */
    private function getRandomWeather(): string
    {
        $weathers = [
            'sunny', 'cloudy', 'rainy', 'windy', 'overcast',
            'partly_cloudy', 'foggy', 'clear', 'stormy'
        ];
        
        return $weathers[array_rand($weathers)];
    }
}
