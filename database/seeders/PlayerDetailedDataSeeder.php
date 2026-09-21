<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Joueur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PlayerDetailedDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📊 Création des données détaillées des joueurs...');
        
        $joueurs = Joueur::all();
        
        foreach ($joueurs as $joueur) {
            // Créer les statistiques détaillées
            $this->createDetailedStats($joueur);
            
            // Créer les appareils connectés
            $this->createConnectedDevices($joueur);
            
            // Créer les données de santé en temps réel
            $this->createRealTimeHealth($joueur);
            
            // Créer les données SDOH
            $this->createSDOHData($joueur);
            
            // Créer les statistiques de match détaillées
            $this->createMatchStats($joueur);
        }
        
        $this->command->info('✅ Données détaillées créées pour ' . $joueurs->count() . ' joueurs !');
    }
    
    private function createDetailedStats($joueur)
    {
        $position = $joueur->poste;
        
        // Statistiques adaptées au poste
        $stats = $this->getPositionBasedStats($position);
        
        $positionCode = $this->toPositionCode($position);
        $rating = rand(60, 95) / 10;

        DB::table('player_detailed_stats')->insert([
            'player_id' => $joueur->id,
            'match_id' => null,
            'season_id' => 1,
            'stats_date' => now()->toDateString(),
            'shots_on_target' => round($stats['shots'] * 0.6),
            'total_shots' => $stats['shots'],
            'shooting_accuracy' => rand(55, 85),
            'key_passes' => $stats['key_passes'],
            'successful_crosses' => round($stats['crosses'] * (rand(20, 40) / 100)),
            'successful_dribbles' => round($stats['dribbles'] * (rand(60, 80) / 100)),
            'big_chances_created' => rand(0, 8),
            'big_chances_missed' => rand(0, 5),
            'offsides' => rand(0, 6),
            'fouls_drawn' => rand(15, 50),
            'distance_covered_km' => $stats['distance'],
            'max_speed_kmh' => $stats['max_speed'],
            'avg_speed_kmh' => $stats['avg_speed'],
            'sprint_count' => $stats['sprints'],
            'acceleration_count' => $stats['accelerations'],
            'deceleration_count' => $stats['decelerations'],
            'direction_changes' => $stats['direction_changes'],
            'jump_count' => $stats['jumps'],
            'high_intensity_distance' => round($stats['distance'] * 0.25, 2),
            'sprint_distance' => round($stats['distance'] * 0.1, 2),
            'long_passes' => rand(5, 40),
            'successful_tackles' => $stats['tackles'],
            'interceptions' => $stats['interceptions'],
            'clearances' => $stats['clearances'],
            'fouls_committed' => rand(10, 40),
            'yellow_cards' => rand(2, 8),
            'red_cards' => rand(0, 2),
            'blocks' => rand(5, 25),
            'aerial_duels_won' => rand(10, 50),
            'ground_duels_won' => rand(10, 50),
            'minutes_played' => rand(2700, 3240), // 30-36 matchs
            'match_rating' => $rating,
            'position_played' => $positionCode,
            'man_of_match' => rand(0, 1),
            'performance_level' => $this->ratingToPerformanceLevel($rating),
            'data_source' => 'match_analysis',
            'data_confidence' => 100,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function toPositionCode($position)
    {
        $map = [
            'Gardien' => 'GK',
            'Défenseur Central' => 'CB',
            'Latéral' => 'RB',
            'Milieu Défensif' => 'DM',
            'Milieu Offensif' => 'AM',
            'Attaquant' => 'ST',
        ];
        return $map[$position] ?? 'CM';
    }

    private function ratingToPerformanceLevel($rating)
    {
        if ($rating >= 8.5) return 'excellent';
        if ($rating >= 7.0) return 'good';
        if ($rating >= 5.5) return 'average';
        if ($rating >= 4.0) return 'poor';
        return 'very_poor';
    }
    
    private function createConnectedDevices($joueur)
    {
        $devices = [
            'Apple Watch Series 8',
            'iPhone 15 Pro',
            'Garmin Fenix 7',
            'Polar Vantage V3',
            'Suunto 9 Peak'
        ];
        
        $deviceCount = rand(1, 3);
        $selectedDevices = array_rand(array_flip($devices), $deviceCount);
        
        if (!is_array($selectedDevices)) {
            $selectedDevices = [$selectedDevices];
        }
        
        foreach ($selectedDevices as $device) {
            $manufacturer = explode(' ', $device)[0];
            DB::table('player_connected_devices')->insert([
                'player_id' => $joueur->id,
                'device_name' => $device,
                'device_type' => $this->getDeviceType($device),
                'device_model' => $device,
                'manufacturer' => $manufacturer,
                'serial_number' => strtoupper(Str::random(10)) . '-' . $joueur->id . '-' . rand(1000, 9999),
                'battery_level' => rand(20, 100),
                'is_connected' => rand(0, 1),
                'last_sync_at' => now()->subHours(rand(0, 24)),
                'sensors_available' => json_encode($this->getAvailableSensors($device)),
                'firmware_version' => 'v' . rand(1, 5) . '.' . rand(0, 9) . '.' . rand(0, 9),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    
    private function createRealTimeHealth($joueur)
    {
        // Données de santé en temps réel (simulation de 30 jours)
        for ($i = 0; $i < 30; $i++) {
            $date = now()->subDays($i);
            
            DB::table('player_real_time_health')->insert([
                'player_id' => $joueur->id,
                'device_id' => null,
                'measurement_time' => $date,
                'data_source' => 'smartwatch',
                'heart_rate' => rand(45, 85),
                'blood_pressure_systolic' => rand(100, 140),
                'blood_pressure_diastolic' => rand(60, 90),
                'temperature' => round(36.0 + rand(-10, 15) / 10, 1),
                'oxygen_saturation' => rand(94, 99),
                'hydration_level' => rand(65, 95),
                'cortisol_level' => rand(5, 30),
                'weight_kg' => $joueur->poids_kg + rand(-2, 2),
                'body_fat_percentage' => rand(8, 18),
                'sleep_duration_hours' => rand(6, 9),
                'stress_level' => rand(15, 65),
                'steps_count' => rand(8000, 18000),
                'calories_burned' => rand(1800, 4000),
                'active_minutes' => rand(100, 350),
                'exercise_minutes' => rand(45, 200),
                'stand_hours' => rand(7, 16),
                'distance_walked_km' => rand(4, 15),
                'created_at' => $date,
                'updated_at' => $date
            ]);
        }
    }
    
    private function createSDOHData($joueur)
    {
        $nationality = $joueur->nationalite;
        $age = Carbon::parse($joueur->date_naissance)->age;
        
        // Profils SDOH narratifs selon la nationalité et l'âge (texte -> notes uniquement)
        $sdohProfile = $this->generateSDOHProfile($nationality, $age);
        $wellIntegrated = in_array($nationality, ['Tunisie', 'Maroc', 'Algérie']);

        DB::table('player_sdoh_data')->insert([
            'player_id' => $joueur->id,
            'assessment_date' => now(),
            'assessment_type' => 'initial',
            'assessed_by' => 1, // Admin
            'living_environment_score' => $joueur->score_environnement_vie,
            'housing_quality' => $wellIntegrated ? 'good' : 'adequate',
            'housing_stability' => 'stable',
            'access_to_public_transport' => true,
            'living_environment_notes' => $sdohProfile['housing'],
            'social_support_score' => $joueur->score_soutien_social,
            'has_emotional_support' => true,
            'has_practical_support' => $wellIntegrated,
            'relationship_status' => 'single',
            'social_support_notes' => $sdohProfile['family_support'],
            'healthcare_access_score' => $joueur->score_acces_soins,
            'has_health_insurance' => (bool) $sdohProfile['insurance'],
            'insurance_quality' => 'good',
            'has_primary_care_physician' => true,
            'healthcare_access_notes' => 'Suivi médical assuré par le staff du club',
            'financial_situation_score' => $joueur->score_situation_financiere,
            'income_level' => 'middle',
            'has_stable_income' => true,
            'financial_situation_notes' => $sdohProfile['income'],
            'mental_wellbeing_score' => $joueur->score_bien_etre_mental,
            'has_mental_health_history' => false,
            'stress_level' => $wellIntegrated ? 'low' : 'moderate',
            'anxiety_level' => 'low',
            'depression_level' => 'very_low',
            'mental_wellbeing_notes' => $sdohProfile['notes'],
            'employment_status' => 'employed',
            'smoking_status' => str_contains($sdohProfile['smoking'], 'Non') ? 'never' : 'occasional',
            'alcohol_consumption' => str_contains($sdohProfile['alcohol'], 'Faible') ? 'light' : 'moderate',
            'exercises_regularly' => true,
            'has_healthy_diet' => true,
            'overall_sdoh_score' => $joueur->score_sdoh_global,
            'risk_category' => $wellIntegrated ? 'low' : 'moderate',
            'assessment_method' => 'interview',
            'assessment_reliability' => 90,
            'requires_follow_up' => false,
            'next_assessment_date' => now()->addMonths(6),
            'general_notes' => $sdohProfile['notes'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    private function createMatchStats($joueur)
    {
        $matchIds = DB::table('matches')->pluck('id')->all();
        $teamIds = DB::table('teams')->pluck('id')->all();
        $competitionIds = DB::table('competitions')->pluck('id')->all();

        if (empty($matchIds) || empty($teamIds)) {
            return; // pas de matchs/équipes disponibles pour lier les stats
        }

        // Statistiques de match, une entrée par match réellement existant
        foreach ($matchIds as $i => $matchId) {
            $position = $joueur->poste;
            $stats = $this->getPositionBasedStats($position);

            DB::table('player_match_detailed_stats')->insertOrIgnore([
                'player_id' => $joueur->id,
                'match_id' => $matchId,
                'team_id' => $teamIds[array_rand($teamIds)],
                'competition_id' => !empty($competitionIds) ? $competitionIds[array_rand($competitionIds)] : null,
                'season_id' => 1,
                'position_played' => $this->toPositionCode($position),
                'minutes_played' => rand(60, 90),
                'goals_scored' => rand(0, $stats['goals_per_match']),
                'assists_provided' => rand(0, $stats['assists_per_match']),
                'shots_total' => rand(0, $stats['shots_per_match']),
                'shots_on_target' => rand(0, round($stats['shots_per_match'] * 0.6)),
                'passes_total' => rand(10, $stats['passes_per_match']),
                'passes_completed' => rand(8, round($stats['passes_per_match'] * 0.9)),
                'key_passes' => rand(0, $stats['key_passes_per_match']),
                'crosses_total' => rand(0, $stats['crosses_per_match']),
                'crosses_completed' => rand(0, round($stats['crosses_per_match'] * 0.3)),
                'dribbles_attempted' => rand(0, $stats['dribbles_per_match']),
                'dribbles_completed' => rand(0, round($stats['dribbles_per_match'] * 0.7)),
                'tackles_won' => rand(0, $stats['tackles_per_match']),
                'interceptions' => rand(0, $stats['interceptions_per_match']),
                'clearances' => rand(0, $stats['clearances_per_match']),
                'aerial_duels_won' => rand(0, $stats['aerial_duels_per_match']),
                'fouls_committed' => rand(0, 3),
                'yellow_cards' => rand(0, 1),
                'red_cards' => rand(0, 1),
                'distance_covered_km' => rand($stats['distance_per_match'] * 0.8, $stats['distance_per_match'] * 1.2),
                'max_speed_kmh' => rand($stats['max_speed'] - 2, $stats['max_speed'] + 2),
                'match_rating' => rand(60, 95) / 10,
                'man_of_match' => rand(0, 1),
                'created_at' => now()->subDays(count($matchIds) - $i),
                'updated_at' => now()->subDays(count($matchIds) - $i)
            ]);
        }
    }
    
    private function getPositionBasedStats($position)
    {
        $stats = [
            'Gardien' => [
                'goals' => 0, 'assists' => rand(0, 2), 'shots' => 0, 'passes' => rand(15, 25), 'key_passes' => rand(0, 3),
                'crosses' => 0, 'dribbles' => 0, 'tackles' => rand(0, 2), 'interceptions' => rand(0, 3),
                'clearances' => rand(5, 15), 'distance' => rand(8, 12), 'max_speed' => rand(18, 22),
                'avg_speed' => rand(6, 9), 'sprints' => rand(15, 25), 'accelerations' => rand(10, 20),
                'decelerations' => rand(8, 18), 'direction_changes' => rand(20, 40), 'jumps' => rand(8, 15),
                'goals_per_match' => 0, 'assists_per_match' => 0.1, 'shots_per_match' => 0,
                'key_passes_per_match' => 0.1, 'crosses_per_match' => 0, 'dribbles_per_match' => 0,
                'tackles_per_match' => 0.1, 'interceptions_per_match' => 0.1, 'clearances_per_match' => 0.5,
                'aerial_duels_per_match' => 0.3, 'distance_per_match' => 10, 'passes_per_match' => 20
            ],
            'Défenseur Central' => [
                'goals' => rand(0, 3), 'assists' => rand(0, 4), 'shots' => rand(0, 5), 'passes' => rand(40, 60), 'key_passes' => rand(0, 5),
                'crosses' => 0, 'dribbles' => rand(0, 3), 'tackles' => rand(15, 35), 'interceptions' => rand(10, 25),
                'clearances' => rand(20, 50), 'distance' => rand(9, 11), 'max_speed' => rand(28, 32),
                'avg_speed' => rand(8, 10), 'sprints' => rand(20, 35), 'accelerations' => rand(15, 30),
                'decelerations' => rand(12, 25), 'direction_changes' => rand(25, 50), 'jumps' => rand(15, 30),
                'goals_per_match' => 0.1, 'assists_per_match' => 0.1, 'shots_per_match' => 0.2,
                'key_passes_per_match' => 0.2, 'crosses_per_match' => 0, 'dribbles_per_match' => 0.1,
                'tackles_per_match' => 1.2, 'interceptions_per_match' => 0.8, 'clearances_per_match' => 2.0,
                'aerial_duels_per_match' => 1.5, 'distance_per_match' => 10, 'passes_per_match' => 55
            ],
            'Latéral' => [
                'goals' => rand(0, 4), 'assists' => rand(2, 8), 'shots' => rand(0, 6), 'passes' => rand(25, 40), 'key_passes' => rand(3, 10),
                'crosses' => rand(10, 30), 'dribbles' => rand(5, 15), 'tackles' => rand(8, 20), 'interceptions' => rand(5, 15),
                'clearances' => rand(10, 25), 'distance' => rand(10, 12), 'max_speed' => rand(30, 35),
                'avg_speed' => rand(9, 11), 'sprints' => rand(25, 40), 'accelerations' => rand(20, 35),
                'decelerations' => rand(15, 30), 'direction_changes' => rand(30, 60), 'jumps' => rand(10, 20),
                'goals_per_match' => 0.1, 'assists_per_match' => 0.3, 'shots_per_match' => 0.2,
                'key_passes_per_match' => 0.3, 'crosses_per_match' => 1.0, 'dribbles_per_match' => 0.5,
                'tackles_per_match' => 0.7, 'interceptions_per_match' => 0.5, 'clearances_per_match' => 0.8,
                'aerial_duels_per_match' => 0.8, 'distance_per_match' => 11, 'passes_per_match' => 45
            ],
            'Milieu Défensif' => [
                'goals' => rand(0, 5), 'assists' => rand(3, 10), 'shots' => rand(0, 8), 'passes' => rand(50, 70), 'key_passes' => rand(5, 15),
                'crosses' => rand(5, 20), 'dribbles' => rand(3, 12), 'tackles' => rand(20, 40), 'interceptions' => rand(15, 30),
                'clearances' => rand(15, 35), 'distance' => rand(10, 12), 'max_speed' => rand(29, 33),
                'avg_speed' => rand(9, 11), 'sprints' => rand(30, 45), 'accelerations' => rand(25, 40),
                'decelerations' => rand(20, 35), 'direction_changes' => rand(35, 70), 'jumps' => rand(12, 22),
                'goals_per_match' => 0.2, 'assists_per_match' => 0.3, 'shots_per_match' => 0.3,
                'key_passes_per_match' => 0.5, 'crosses_per_match' => 0.7, 'dribbles_per_match' => 0.4,
                'tackles_per_match' => 1.5, 'interceptions_per_match' => 1.0, 'clearances_per_match' => 1.2,
                'aerial_duels_per_match' => 1.0, 'distance_per_match' => 11, 'passes_per_match' => 65
            ],
            'Milieu Offensif' => [
                'goals' => rand(2, 8), 'assists' => rand(5, 12), 'shots' => rand(5, 15), 'passes' => rand(30, 50), 'key_passes' => rand(8, 20),
                'crosses' => rand(3, 15), 'dribbles' => rand(8, 20), 'tackles' => rand(5, 15), 'interceptions' => rand(3, 12),
                'clearances' => rand(5, 15), 'distance' => rand(9, 11), 'max_speed' => rand(30, 34),
                'avg_speed' => rand(8, 10), 'sprints' => rand(25, 40), 'accelerations' => rand(20, 35),
                'decelerations' => rand(15, 30), 'direction_changes' => rand(30, 60), 'jumps' => rand(8, 18),
                'goals_per_match' => 0.3, 'assists_per_match' => 0.4, 'shots_per_match' => 0.5,
                'key_passes_per_match' => 0.7, 'crosses_per_match' => 0.5, 'dribbles_per_match' => 0.7,
                'tackles_per_match' => 0.5, 'interceptions_per_match' => 0.4, 'clearances_per_match' => 0.5,
                'aerial_duels_per_match' => 0.6, 'distance_per_match' => 10, 'passes_per_match' => 50
            ],
            'Attaquant' => [
                'goals' => rand(5, 15), 'assists' => rand(3, 10), 'shots' => rand(10, 25), 'passes' => rand(15, 30), 'key_passes' => rand(5, 15),
                'crosses' => rand(2, 10), 'dribbles' => rand(10, 25), 'tackles' => rand(2, 10), 'interceptions' => rand(2, 8),
                'clearances' => rand(2, 10), 'distance' => rand(8, 10), 'max_speed' => rand(31, 36),
                'avg_speed' => rand(7, 9), 'sprints' => rand(20, 35), 'accelerations' => rand(18, 32),
                'decelerations' => rand(12, 25), 'direction_changes' => rand(25, 50), 'jumps' => rand(6, 15),
                'goals_per_match' => 0.5, 'assists_per_match' => 0.3, 'shots_per_match' => 0.8,
                'key_passes_per_match' => 0.5, 'crosses_per_match' => 0.3, 'dribbles_per_match' => 0.8,
                'tackles_per_match' => 0.3, 'interceptions_per_match' => 0.3, 'clearances_per_match' => 0.3,
                'aerial_duels_per_match' => 0.8, 'distance_per_match' => 9, 'passes_per_match' => 30
            ]
        ];
        
        return $stats[$position] ?? $stats['Milieu Défensif'];
    }
    
    private function getDeviceType($device)
    {
        if (strpos($device, 'Watch') !== false) return 'Smartwatch';
        if (strpos($device, 'iPhone') !== false) return 'Smartphone';
        if (strpos($device, 'Garmin') !== false || strpos($device, 'Polar') !== false || strpos($device, 'Suunto') !== false) return 'Fitness Tracker';
        return 'Other';
    }
    
    private function getAvailableSensors($device)
    {
        $sensors = [
            'heart_rate', 'gps', 'accelerometer', 'gyroscope', 'barometer', 'compass',
            'temperature', 'blood_oxygen', 'ecg', 'sleep_tracking', 'stress_monitoring'
        ];
        
        $deviceSensors = [
            'Apple Watch Series 8' => ['heart_rate', 'gps', 'accelerometer', 'gyroscope', 'barometer', 'compass', 'blood_oxygen', 'ecg', 'sleep_tracking', 'stress_monitoring'],
            'iPhone 15 Pro' => ['gps', 'accelerometer', 'gyroscope', 'barometer', 'compass'],
            'Garmin Fenix 7' => ['heart_rate', 'gps', 'accelerometer', 'gyroscope', 'barometer', 'compass', 'blood_oxygen', 'sleep_tracking', 'stress_monitoring'],
            'Polar Vantage V3' => ['heart_rate', 'gps', 'accelerometer', 'gyroscope', 'barometer', 'compass', 'sleep_tracking', 'stress_monitoring'],
            'Suunto 9 Peak' => ['heart_rate', 'gps', 'accelerometer', 'gyroscope', 'barometer', 'compass', 'sleep_tracking']
        ];
        
        return $deviceSensors[$device] ?? array_slice($sensors, 0, rand(5, 8));
    }
    
    private function generateSDOHProfile($nationality, $age)
    {
        $profiles = [
            'Tunisie' => [
                'housing' => 'Stable, logement fourni par le club',
                'income' => 'Revenu stable, contrat professionnel',
                'insurance' => true,
                'smoking' => 'Non-fumeur',
                'alcohol' => 'Consommation modérée',
                'family_support' => 'Forte, famille proche',
                'cultural_integration' => 'Excellente, culture locale',
                'language_barriers' => 'Aucune, arabe et français',
                'social_network' => 'Large, intégré dans la communauté',
                'community_involvement' => 'Actif, respecté localement',
                'stress_factors' => ['Performance sportive', 'Pression médiatique'],
                'coping_mechanisms' => ['Support familial', 'Méditation', 'Sport'],
                'notes' => 'Joueur local bien intégré, excellent support social'
            ],
            'Algérie' => [
                'housing' => 'Stable, logement club',
                'income' => 'Revenu stable',
                'insurance' => true,
                'smoking' => 'Occasionnel',
                'alcohol' => 'Faible consommation',
                'family_support' => 'Bonne, famille en Algérie',
                'cultural_integration' => 'Bonne, culture similaire',
                'language_barriers' => 'Minimes, parle français',
                'social_network' => 'Moyenne, communauté algérienne',
                'community_involvement' => 'Modérée',
                'stress_factors' => ['Éloignement familial', 'Adaptation culturelle'],
                'coping_mechanisms' => ['Appels familiaux', 'Communauté locale'],
                'notes' => 'Bonne intégration, quelques difficultés d\'adaptation'
            ],
            'Maroc' => [
                'housing' => 'Stable, logement club',
                'income' => 'Revenu stable',
                'insurance' => true,
                'smoking' => 'Non-fumeur',
                'alcohol' => 'Faible consommation',
                'family_support' => 'Bonne, famille au Maroc',
                'cultural_integration' => 'Bonne, culture similaire',
                'language_barriers' => 'Minimes, parle français',
                'social_network' => 'Moyenne, communauté marocaine',
                'community_involvement' => 'Modérée',
                'stress_factors' => ['Éloignement familial', 'Adaptation culturelle'],
                'coping_mechanisms' => ['Appels familiaux', 'Communauté locale'],
                'notes' => 'Bonne intégration, quelques difficultés d\'adaptation'
            ],
            'Côte d\'Ivoire' => [
                'housing' => 'Stable, logement club',
                'income' => 'Revenu stable',
                'insurance' => true,
                'smoking' => 'Occasionnel',
                'alcohol' => 'Consommation modérée',
                'family_support' => 'Modérée, famille éloignée',
                'cultural_integration' => 'Moyenne, culture différente',
                'language_barriers' => 'Quelques difficultés, français parlé',
                'social_network' => 'Limitée, communauté ivoirienne',
                'community_involvement' => 'Faible',
                'stress_factors' => ['Éloignement familial', 'Différences culturelles', 'Langue'],
                'coping_mechanisms' => ['Appels familiaux', 'Communauté locale', 'Sport'],
                'notes' => 'Intégration en cours, quelques barrières culturelles'
            ],
            'Nigeria' => [
                'housing' => 'Stable, logement club',
                'income' => 'Revenu stable',
                'insurance' => true,
                'smoking' => 'Non-fumeur',
                'alcohol' => 'Faible consommation',
                'family_support' => 'Modérée, famille éloignée',
                'cultural_integration' => 'Moyenne, culture différente',
                'language_barriers' => 'Difficultés, anglais parlé',
                'social_network' => 'Limitée, communauté nigériane',
                'community_involvement' => 'Faible',
                'stress_factors' => ['Éloignement familial', 'Différences culturelles', 'Langue'],
                'coping_mechanisms' => ['Appels familiaux', 'Communauté locale', 'Sport'],
                'notes' => 'Intégration en cours, barrières linguistiques et culturelles'
            ]
        ];
        
        $profile = $profiles[$nationality] ?? $profiles['Tunisie'];
        
        // Ajuster selon l'âge
        if ($age < 22) {
            $profile['family_support'] = 'Très forte, jeune joueur';
            $profile['stress_factors'][] = 'Pression de performance';
        } elseif ($age > 30) {
            $profile['family_support'] = 'Stable, joueur expérimenté';
            $profile['stress_factors'][] = 'Fin de carrière';
        }
        
        return $profile;
    }
}
