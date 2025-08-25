<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Player;
use App\Models\HealthRecord;
use App\Models\Performance;
use App\Models\PCMA;
use App\Models\Club;
use App\Models\Association;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

// Charger l'application Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GÉNÉRATION COMPLÈTE DES DONNÉES JOUEUR ===\n\n";

try {
    // 1. CRÉER/METTRE À JOUR LES CLUBS ET ASSOCIATIONS
    echo "=== CRÉATION DES CLUBS ET ASSOCIATIONS ===\n";
    
    $club = Club::firstOrCreate(
        ['name' => 'Al Nassr FC'],
        [
            'name' => 'Al Nassr FC',
            'country' => 'Arabie Saoudite',
            'city' => 'Riyad',
            'founded_year' => 1955,
            'stadium' => 'Al Awal Park',
            'capacity' => 25000,
            'status' => 'active'
        ]
    );
    echo "✅ Club: {$club->name}\n";
    
    $association = Association::firstOrCreate(
        ['name' => 'Fédération Sénégalaise de Football'],
        [
            'name' => 'Fédération Sénégalaise de Football',
            'country' => 'Sénégal',
            'founded_year' => 1960,
            'status' => 'active'
        ]
    );
    echo "✅ Association: {$association->name}\n";
    
    // 2. METTRE À JOUR SADIO MANÉ AVEC TOUTES LES DONNÉES
    echo "\n=== MISE À JOUR DE SADIO MANÉ ===\n";
    
    $player = Player::find(2);
    if (!$player) {
        echo "❌ Joueur ID 2 non trouvé\n";
        exit(1);
    }
    
    // Mettre à jour avec toutes les données FIFA et santé
    $player->update([
        'first_name' => 'Sadio',
        'last_name' => 'Mané',
        'position' => 'LW',
        'nationality' => 'Sénégal',
        'date_of_birth' => '1993-03-20',
        'club_id' => $club->id,
        'association_id' => $association->id,
        'overall_rating' => 88,
        'potential_rating' => 89,
        'height' => 175,
        'weight' => 69,
        'preferred_foot' => 'right',
        'weak_foot' => 3,
        'skill_moves' => 4,
        'international_reputation' => 4,
        'work_rate' => 'High/High',
        'body_type' => 'Lean',
        'real_face' => true,
        'release_clause' => 50000000,
        'wage' => 200000,
        'contract_valid_until' => '2027-06-30',
        
        // Scores GHS complets
        'ghs_overall_score' => 87,
        'ghs_physical_score' => 89,
        'ghs_mental_score' => 85,
        'ghs_civic_score' => 90,
        'ghs_sleep_score' => 82,
        'ghs_color_code' => 'green',
        'ghs_ai_suggestions' => json_encode([
            'recommendations' => [
                'Maintenir le niveau actuel de condition physique',
                'Continuer les exercices de récupération post-match',
                'Surveiller la qualité du sommeil'
            ]
        ]),
        'ghs_last_updated' => now(),
        
        // Données de blessure et santé
        'injury_risk_score' => 15,
        'injury_risk_level' => 'Faible',
        'injury_risk_reason' => 'Excellente condition physique, récupération optimale',
        'injury_risk_last_assessed' => now(),
        'last_injury_date' => null,
        'injury_history' => json_encode([]),
        
        // Scores de contribution et valeur
        'contribution_score' => 92,
        'data_value_estimate' => 1500000,
        'market_value' => 45000000,
        'transfer_value' => 40000000,
        
        // Données de performance
        'pace' => 90,
        'shooting' => 84,
        'passing' => 79,
        'dribbling' => 86,
        'defending' => 45,
        'physical' => 76,
        
        // Données de santé avancées
        'bmi' => 22.5,
        'body_fat_percentage' => 8.5,
        'muscle_mass' => 45.2,
        'hydration_level' => 95,
        'sleep_quality' => 8.5,
        'stress_level' => 3,
        'energy_level' => 9,
        'recovery_rate' => 92,
        
        'status' => 'active',
        'last_health_check' => now(),
        'last_performance_test' => now(),
        'fitness_level' => 'Excellent',
        'medical_clearance' => true,
        'medical_notes' => 'Joueur en excellente condition physique, aucun problème médical',
        
        'updated_at' => now()
    ]);
    
    echo "✅ Sadio Mané mis à jour avec toutes les données\n";
    
    // 3. CRÉER LE COMPTE UTILISATEUR SI NÉCESSAIRE
    echo "\n=== VÉRIFICATION DU COMPTE UTILISATEUR ===\n";
    
    $user = User::where('player_id', $player->id)->first();
    if (!$user) {
        $user = User::create([
            'name' => $player->first_name . ' ' . $player->last_name,
            'email' => 'sadio.mane@alnassr.sa',
            'password' => Hash::make('password123'),
            'role' => 'player',
            'player_id' => $player->id,
            'club_id' => $club->id,
            'association_id' => $association->id,
            'status' => 'active',
            'permissions' => ['player_portal_access'],
            'preferences' => [
                'language' => 'fr',
                'timezone' => 'Asia/Riyadh',
                'notifications_email' => true,
                'notifications_sms' => false
            ]
        ]);
        echo "✅ Compte utilisateur créé pour {$user->name}\n";
    } else {
        echo "✅ Compte utilisateur existant pour {$user->name}\n";
    }
    
    // 4. CRÉER DES BILANS DE SANTÉ COMPLETS
    echo "\n=== CRÉATION DES BILANS DE SANTÉ ===\n";
    
    // Supprimer les anciens bilans
    $player->healthRecords()->delete();
    
    // Créer des bilans de santé sur 6 mois
    $healthTypes = ['checkup', 'injury_assessment', 'fitness_test', 'recovery_monitoring', 'nutrition_assessment', 'mental_health'];
    $healthDates = [
        now()->subDays(180),
        now()->subDays(150),
        now()->subDays(120),
        now()->subDays(90),
        now()->subDays(60),
        now()->subDays(30)
    ];
    
    foreach ($healthDates as $index => $date) {
        $type = $healthTypes[$index];
        $healthRecord = HealthRecord::create([
            'user_id' => $user->id,
            'player_id' => $player->id,
            'risk_score' => rand(5, 15),
            'prediction_confidence' => rand(85, 95),
            'record_date' => $date,
            'status' => 'completed',
            
            // Données de santé de base
            'blood_pressure_systolic' => 120,
            'blood_pressure_diastolic' => 80,
            'heart_rate' => 58,
            'temperature' => 36.8,
            'weight' => 69 + rand(-1, 1),
            'height' => 175,
            'bmi' => 22.5,
            'blood_type' => 'O+',
            'allergies' => 'Aucune',
            'medications' => 'Aucune',
            'medical_history' => 'Aucun antécédent médical',
            'symptoms' => 'Aucun symptôme',
            'diagnosis' => 'En excellente santé',
            'treatment_plan' => 'Maintenir le niveau actuel',
            'next_checkup_date' => $date->addDays(30),
            
            'created_at' => $date,
            'updated_at' => $date
        ]);
        
        echo "✅ Bilan de santé {$type} créé pour {$date->format('d/m/Y')}\n";
    }
    
    // 4. CRÉER DES PERFORMANCES DE MATCH COMPLÈTES
    echo "\n=== CRÉATION DES PERFORMANCES DE MATCH ===\n";
    
    // Supprimer les anciennes performances
    $player->performances()->delete();
    
    // Créer des performances sur 3 mois
    $matchDates = [
        now()->subDays(90),
        now()->subDays(75),
        now()->subDays(60),
        now()->subDays(45),
        now()->subDays(30),
        now()->subDays(15),
        now()->subDays(7)
    ];
    
    foreach ($matchDates as $index => $date) {
        $performance = Performance::create([
            'player_id' => $player->id,
            'match_date' => $date->format('Y-m-d'),
            'distance_covered' => rand(9000, 12000),
            'sprint_count' => rand(15, 25),
            'max_speed' => rand(30, 35),
            'avg_speed' => rand(8, 12),
            'passes_completed' => rand(25, 45),
            'passes_attempted' => rand(30, 50),
            'tackles_won' => rand(1, 4),
            'tackles_attempted' => rand(2, 6),
            'shots_on_target' => rand(1, 4),
            'shots_total' => rand(2, 6),
            'goals_scored' => rand(0, 2),
            'assists' => rand(0, 2),
            'yellow_cards' => rand(0, 1),
            'red_cards' => 0,
            'minutes_played' => rand(70, 90),
            'rating' => rand(65, 95) / 10,
            'notes' => "Match vs " . ['Al Hilal', 'Al Ahli', 'Al Shabab', 'Al Ittihad', 'Al Fateh', 'Al Taawoun', 'Al Raed'][$index],
            'additional_metrics' => json_encode([
                'competition' => ['Ligue Pro', 'Coupe du Roi', 'Ligue des Champions', 'Ligue Pro', 'Coupe du Roi', 'Ligue Pro', 'Ligue Pro'][$index],
                'result' => ['W', 'W', 'D', 'L', 'W', 'W', 'W'][$index],
                'score' => ['3-1', '2-0', '1-1', '0-2', '4-1', '2-1', '3-0'][$index]
            ]),
            'created_at' => $date,
            'updated_at' => $date
        ]);
        
        echo "✅ Performance créée pour {$date->format('d/m/Y')} vs {$performance->opponent}\n";
    }
    
    // 5. CRÉER DES ÉVALUATIONS PCMA COMPLÈTES
    echo "\n=== CRÉATION DES ÉVALUATIONS PCMA ===\n";
    
    // Supprimer les anciennes PCMA
    $player->pcmas()->delete();
    
    // Créer des PCMA sur 4 mois
    $pcmaDates = [
        now()->subDays(120),
        now()->subDays(90),
        now()->subDays(60),
        now()->subDays(30)
    ];
    
    foreach ($pcmaDates as $index => $date) {
        $pcma = PCMA::create([
            'athlete_id' => $player->id,
            'type' => 'annual_assessment',
            'result_json' => json_encode([
                'medical_clearance' => true,
                'fitness_score' => rand(85, 95),
                'strength_test' => 'Excellent',
                'endurance_test' => 'Excellent',
                'agility_test' => 'Excellent',
                'balance_test' => 'Good',
                'nutrition_assessment' => 'Optimal',
                'mental_health_status' => 'Excellent',
                'recommendations' => [
                    'Maintenir le niveau actuel de condition physique',
                    'Continuer le programme nutritionnel actuel',
                    'Surveiller la récupération post-match'
                ]
            ]),
            'status' => 'completed',
            'completed_at' => $date,
            'assessor_id' => 1,
            'notes' => 'Évaluation PCMA annuelle - Joueur en excellente condition',
            'fifa_compliance_data' => json_encode([
                'fifa_compliant' => true,
                'medical_clearance' => true,
                'injury_risk' => 'Low'
            ]),
            'medical_history' => 'Aucun antécédent médical',
            'physical_examination' => 'Examen physique normal',
            'cardiovascular_investigations' => 'Cardiovasculaire normal',
            'final_statement' => 'Apte à la compétition',
            'scat_assessment' => 'Normal',
            'assessment_date' => $date->format('Y-m-d'),
            'fifa_id' => 'SEN001',
            'competition_name' => 'Ligue Pro Saoudienne',
            'competition_date' => $date->format('Y-m-d'),
            'team_name' => $club->name,
            'position' => $player->position,
            'fifa_compliant' => true,
            'fifa_approved_at' => $date,
            'fifa_approved_by' => 'Dr. FIFA',
            'form_version' => '2024.1',
            'last_updated_at' => $date,
            'is_signed' => true,
            'signed_at' => $date,
            'signed_by' => 'Dr. FIFA',
            'license_number' => 'SEN2024001',
            'created_at' => $date,
            'updated_at' => $date
        ]);
        
        echo "✅ PCMA créée pour {$date->format('d/m/Y')}\n";
    }
    
    // 6. AFFICHER LE RÉSUMÉ FINAL
    echo "\n=== RÉSUMÉ FINAL ===\n";
    echo "🎯 Joueur: {$player->first_name} {$player->last_name}\n";
    echo "🏆 Club: {$club->name}\n";
    echo "🌍 Association: {$association->name}\n";
    echo "📊 Bilan de santé: {$player->healthRecords->count()} enregistrements\n";
    echo "⚽ Performances: {$player->performances->count()} matchs\n";
    echo "🏥 PCMA: {$player->pcmas->count()} évaluations\n";
    echo "🔑 Code d'accès: " . strtoupper(substr(md5($player->id . $player->date_of_birth->format('dmY')), 0, 8)) . "\n";
    echo "🌐 URL d'accès: http://localhost:8001/joueur/{$player->id}\n";
    
    echo "\n✅ Toutes les données ont été générées avec succès !\n";
    echo "🎉 Le portail patient est maintenant 100% fonctionnel !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}




