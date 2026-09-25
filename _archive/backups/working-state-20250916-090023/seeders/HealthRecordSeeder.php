<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HealthRecord;
use App\Models\MedicalPrediction;
use App\Models\User;
use App\Models\Player;

class HealthRecordSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing health records first
        HealthRecord::truncate();
        MedicalPrediction::truncate();
        
        // Créer un utilisateur de test si il n'existe pas
        $user = User::firstOrCreate(
            ['email' => 'test@medpredictor.com'],
            [
                'name' => 'Dr. Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Créer quelques joueurs de test si ils n'existent pas
        $players = [
            [
                'name' => 'Lionel Messi',
                'first_name' => 'Lionel',
                'last_name' => 'Messi',
                'date_of_birth' => '1987-06-24',
                'nationality' => 'Argentina',
                'position' => 'RW',
                'height' => 170,
                'weight' => 72,
                'overall_rating' => 93,
                'potential_rating' => 93,
                'value_eur' => 50000000,
                'wage_eur' => 500000,
                'fifa_connect_id' => 'MESSI_001',
            ],
            [
                'name' => 'Cristiano Ronaldo',
                'first_name' => 'Cristiano',
                'last_name' => 'Ronaldo',
                'date_of_birth' => '1985-02-05',
                'nationality' => 'Portugal',
                'position' => 'ST',
                'height' => 187,
                'weight' => 83,
                'overall_rating' => 88,
                'potential_rating' => 88,
                'value_eur' => 15000000,
                'wage_eur' => 200000,
                'fifa_connect_id' => 'RONALDO_001',
            ],
            [
                'name' => 'Kylian Mbappé',
                'first_name' => 'Kylian',
                'last_name' => 'Mbappé',
                'date_of_birth' => '1998-12-20',
                'nationality' => 'France',
                'position' => 'ST',
                'height' => 178,
                'weight' => 75,
                'overall_rating' => 91,
                'potential_rating' => 95,
                'value_eur' => 180000000,
                'wage_eur' => 800000,
                'fifa_connect_id' => 'MBAPPE_001',
            ],
            [
                'name' => 'Erling Haaland',
                'first_name' => 'Erling',
                'last_name' => 'Haaland',
                'date_of_birth' => '2000-07-21',
                'nationality' => 'Norway',
                'position' => 'ST',
                'height' => 194,
                'weight' => 88,
                'overall_rating' => 91,
                'potential_rating' => 94,
                'value_eur' => 180000000,
                'wage_eur' => 700000,
                'fifa_connect_id' => 'HAALAND_001',
            ],
        ];

        foreach ($players as $playerData) {
            Player::firstOrCreate(
                ['name' => $playerData['name']],
                $playerData
            );
        }

        // Créer des dossiers médicaux variés et réalistes
        $healthRecords = [
            // Messi - Dossier médical complet
            [
                'player_id' => Player::where('name', 'Lionel Messi')->first()->id,
                'visit_date' => now()->subDays(5),
                'doctor_name' => 'Dr. Maria Garcia',
                'visit_type' => 'consultation',
                'chief_complaint' => 'Examen médical annuel et contrôle de forme',
                'physical_examination' => 'Examen physique complet: cœur, poumons, articulations. Excellent état général.',
                'laboratory_results' => 'Analyses sanguines: hémoglobine 15.2 g/dL, cholestérol total 180 mg/dL, glycémie 95 mg/dL',
                'imaging_results' => 'Radiographie thorax: normale. Échographie cardiaque: fonction cardiaque excellente.',
                'prescriptions' => 'Vitamine D 1000 UI/jour, Oméga-3 1000 mg/jour',
                'follow_up_instructions' => 'Contrôle dans 6 mois. Maintenir l\'entraînement actuel.',
                'visit_notes' => 'Joueur en excellente condition physique. Aucune restriction d\'activité.',
                'blood_pressure_systolic' => 120,
                'blood_pressure_diastolic' => 80,
                'heart_rate' => 65,
                'temperature' => 36.8,
                'weight' => 72,
                'height' => 170,
                'blood_type' => 'A+',
                'allergies' => ['Pénicilline'],
                'medications' => ['Vitamine D', 'Oméga-3'],
                'medical_history' => ['Blessure au genou droit (2019)', 'Fracture de la main gauche (2018)'],
                'symptoms' => ['Fatigue légère en fin de saison'],
                'diagnosis' => 'État de santé excellent, forme physique optimale',
                'treatment_plan' => 'Maintenir l\'activité physique régulière et l\'alimentation équilibrée. Supplémentation vitaminique.',
                'record_date' => now()->subDays(5),
                'next_checkup_date' => now()->addMonths(6),
                'status' => 'active',
                'risk_score' => 0.15,
                'prediction_confidence' => 0.85,
            ],
            
            // Ronaldo - Dossier avec quelques préoccupations
            [
                'player_id' => Player::where('name', 'Cristiano Ronaldo')->first()->id,
                'visit_date' => now()->subDays(3),
                'doctor_name' => 'Dr. Carlos Silva',
                'visit_type' => 'follow_up',
                'chief_complaint' => 'Suivi arythmie cardiaque légère et contrôle général',
                'physical_examination' => 'Pouls irrégulier détecté, rythme cardiaque 62 bpm. Autres paramètres normaux.',
                'laboratory_results' => 'Enzymes cardiaques normales. Électrolytes équilibrés.',
                'imaging_results' => 'ECG: arythmie sinusale bénigne. Échographie cardiaque: structure normale.',
                'prescriptions' => 'Bêta-bloquant à faible dose, Vitamine C, Protéines',
                'follow_up_instructions' => 'Surveillance cardiaque. Contrôle dans 3 mois.',
                'visit_notes' => 'Arythmie légère sous contrôle. Aucune restriction majeure.',
                'blood_pressure_systolic' => 118,
                'blood_pressure_diastolic' => 78,
                'heart_rate' => 62,
                'temperature' => 36.6,
                'weight' => 83,
                'height' => 187,
                'blood_type' => 'O+',
                'allergies' => [],
                'medications' => ['Bêta-bloquant', 'Vitamine C', 'Protéines'],
                'medical_history' => ['Blessure à la cheville (2020)', 'Problème cardiaque léger (2021)'],
                'symptoms' => ['Palpitations occasionnelles'],
                'diagnosis' => 'Arythmie sinusale bénigne, état général bon',
                'treatment_plan' => 'Traitement bêta-bloquant, surveillance cardiaque, entraînement modéré.',
                'record_date' => now()->subDays(3),
                'next_checkup_date' => now()->addMonths(4),
                'status' => 'active',
                'risk_score' => 0.35,
                'prediction_confidence' => 0.78,
            ],
            
            // Mbappé - Dossier préventif
            [
                'player_id' => Player::where('name', 'Kylian Mbappé')->first()->id,
                'visit_date' => now()->subDays(10),
                'doctor_name' => 'Dr. Jean Dupont',
                'visit_type' => 'pre_season',
                'chief_complaint' => 'Examen médical pré-saison et évaluation de forme',
                'physical_examination' => 'Examen complet: excellent état physique, récupération optimale.',
                'laboratory_results' => 'Analyses complètes: tous paramètres dans les normes.',
                'imaging_results' => 'Aucun examen d\'imagerie requis.',
                'prescriptions' => 'Complexe vitaminique B, Magnésium',
                'follow_up_instructions' => 'Contrôle dans 4 mois. Continuer l\'entraînement intensif.',
                'visit_notes' => 'Joueur en parfaite condition. Aucune restriction.',
                'blood_pressure_systolic' => 115,
                'blood_pressure_diastolic' => 75,
                'heart_rate' => 58,
                'temperature' => 36.7,
                'weight' => 75,
                'height' => 178,
                'blood_type' => 'B+',
                'allergies' => [],
                'medications' => ['Complexe B', 'Magnésium'],
                'medical_history' => ['Blessure musculaire (2022)'],
                'symptoms' => [],
                'diagnosis' => 'État de santé excellent, forme physique optimale',
                'treatment_plan' => 'Entraînement intensif autorisé, supplémentation vitaminique.',
                'record_date' => now()->subDays(10),
                'next_checkup_date' => now()->addMonths(4),
                'status' => 'active',
                'risk_score' => 0.12,
                'prediction_confidence' => 0.92,
            ],
            
            // Haaland - Dossier avec antécédents
            [
                'player_id' => Player::where('name', 'Erling Haaland')->first()->id,
                'visit_date' => now()->subDays(7),
                'doctor_name' => 'Dr. Anders Hansen',
                'visit_type' => 'consultation',
                'chief_complaint' => 'Suivi post-blessure et contrôle général',
                'physical_examination' => 'Récupération complète de la blessure. Excellent état physique.',
                'laboratory_results' => 'Analyses normales. Marqueurs inflammatoires bas.',
                'imaging_results' => 'IRM genou: cicatrisation complète. Aucune lésion résiduelle.',
                'prescriptions' => 'Anti-inflammatoire si nécessaire, Physiothérapie préventive',
                'follow_up_instructions' => 'Contrôle dans 2 mois. Reprise progressive de l\'entraînement.',
                'visit_notes' => 'Récupération excellente. Reprise complète autorisée.',
                'blood_pressure_systolic' => 122,
                'blood_pressure_diastolic' => 82,
                'heart_rate' => 64,
                'temperature' => 36.9,
                'weight' => 88,
                'height' => 194,
                'blood_type' => 'A-',
                'allergies' => ['Latex'],
                'medications' => ['Anti-inflammatoire', 'Physiothérapie'],
                'medical_history' => ['Blessure ligamentaire genou (2023)', 'Fracture de fatigue (2022)'],
                'symptoms' => ['Raideur matinale légère'],
                'diagnosis' => 'Récupération complète, forme physique excellente',
                'treatment_plan' => 'Physiothérapie préventive, reprise progressive, surveillance.',
                'record_date' => now()->subDays(7),
                'next_checkup_date' => now()->addMonths(2),
                'status' => 'active',
                'risk_score' => 0.28,
                'prediction_confidence' => 0.88,
            ],
            
            // Messi - Visite de suivi
            [
                'player_id' => Player::where('name', 'Lionel Messi')->first()->id,
                'visit_date' => now()->subDays(30),
                'doctor_name' => 'Dr. Maria Garcia',
                'visit_type' => 'follow_up',
                'chief_complaint' => 'Contrôle post-match et évaluation de récupération',
                'physical_examination' => 'Récupération excellente. Aucun signe de fatigue excessive.',
                'laboratory_results' => 'Analyses de récupération: marqueurs normaux.',
                'imaging_results' => 'Aucun examen d\'imagerie requis.',
                'prescriptions' => 'Récupération active, étirements',
                'follow_up_instructions' => 'Contrôle dans 3 mois.',
                'visit_notes' => 'Récupération optimale. Continuer l\'entraînement actuel.',
                'blood_pressure_systolic' => 118,
                'blood_pressure_diastolic' => 78,
                'heart_rate' => 68,
                'temperature' => 36.8,
                'weight' => 72.5,
                'height' => 170,
                'blood_type' => 'A+',
                'allergies' => ['Pénicilline'],
                'medications' => ['Vitamine D', 'Oméga-3'],
                'medical_history' => ['Blessure au genou droit (2019)'],
                'symptoms' => [],
                'diagnosis' => 'Récupération excellente, forme optimale',
                'treatment_plan' => 'Maintenir l\'entraînement actuel, récupération active.',
                'record_date' => now()->subDays(30),
                'next_checkup_date' => now()->addMonths(3),
                'status' => 'completed',
                'risk_score' => 0.18,
                'prediction_confidence' => 0.82,
            ],
        ];

        foreach ($healthRecords as $recordData) {
            // Calculer le BMI
            if (isset($recordData['weight']) && isset($recordData['height'])) {
                $heightInMeters = $recordData['height'] / 100;
                $recordData['bmi'] = round($recordData['weight'] / ($heightInMeters * $heightInMeters), 2);
            }

            $recordData['user_id'] = $user->id;

            $healthRecord = HealthRecord::create($recordData);
            
            // Créer des prédictions médicales pour chaque dossier
            $this->createMedicalPredictions($healthRecord, $user);
        }

        $this->command->info('Health records seeded successfully!');
        $this->command->info('Created ' . count($healthRecords) . ' health records with medical predictions');
    }
    
    /**
     * Créer des prédictions médicales pour un dossier de santé
     */
    private function createMedicalPredictions(HealthRecord $healthRecord, User $user): void
    {
        $predictions = [
            [
                'prediction_type' => 'injury_risk',
                'predicted_condition' => 'Risque de blessure musculaire',
                'risk_probability' => $healthRecord->risk_score + 0.1,
                'confidence_score' => $healthRecord->prediction_confidence,
                'prediction_factors' => [
                    'Historique de blessures',
                    'Intensité d\'entraînement',
                    'Âge du joueur',
                    'Position sur le terrain'
                ],
                'recommendations' => [
                    'Échauffement prolongé avant l\'entraînement',
                    'Récupération active post-match',
                    'Surveillance de la charge d\'entraînement',
                    'Physiothérapie préventive'
                ],
                'prediction_date' => now(),
                'valid_until' => now()->addMonths(3),
                'status' => 'active',
                'ai_model_version' => '2.1',
                'prediction_notes' => [
                    'Modèle basé sur l\'historique médical',
                    'Facteurs de risque évalués',
                    'Recommandations personnalisées'
                ]
            ],
            [
                'prediction_type' => 'performance_prediction',
                'predicted_condition' => 'Prédiction de performance',
                'risk_probability' => 0.3,
                'confidence_score' => 0.85,
                'prediction_factors' => [
                    'État de santé actuel',
                    'Historique de performances',
                    'Métriques physiques',
                    'Récupération'
                ],
                'recommendations' => [
                    'Maintenir l\'entraînement actuel',
                    'Optimiser la nutrition',
                    'Surveiller la récupération',
                    'Ajuster la charge selon la fatigue'
                ],
                'prediction_date' => now(),
                'valid_until' => now()->addMonths(2),
                'status' => 'active',
                'ai_model_version' => '2.1',
                'prediction_notes' => [
                    'Analyse basée sur les données actuelles',
                    'Prédiction optimiste basée sur la forme'
                ]
            ]
        ];
        
        foreach ($predictions as $predictionData) {
            MedicalPrediction::create([
                'health_record_id' => $healthRecord->id,
                'player_id' => $healthRecord->player_id,
                'user_id' => $user->id,
                ...$predictionData
            ]);
        }
    }
}
