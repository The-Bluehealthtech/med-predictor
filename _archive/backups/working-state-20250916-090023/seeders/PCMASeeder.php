<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PCMA;
use App\Models\User;
use App\Models\Player;

class PCMASeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing PCMA records first
        PCMA::truncate();
        
        // Créer un utilisateur de test si il n'existe pas
        $user = User::firstOrCreate(
            ['email' => 'test@medpredictor.com'],
            [
                'name' => 'Dr. Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Récupérer les joueurs existants
        $players = Player::all();
        
        if ($players->isEmpty()) {
            $this->command->warn('Aucun joueur trouvé. Créez d\'abord des joueurs avec PlayerSeeder.');
            return;
        }

        // Créer des évaluations PCMA variées et réalistes
        $pcmaRecords = [
            [
                'player_id' => $players->first()->id,
                'athlete_id' => DB::table('athletes')->where('name', $players->first()->first_name . ' ' . $players->first()->last_name)->value('id'),
                'type' => 'pre_competition',
                'result_json' => [
                    'cardiovascular_status' => 'excellent',
                    'musculoskeletal_status' => 'good',
                    'neurological_status' => 'excellent',
                    'overall_assessment' => 'fit_to_play'
                ],
                'medical_history' => [
                    'previous_injuries' => ['Blessure au genou (2019)'],
                    'surgeries' => [],
                    'chronic_conditions' => [],
                    'medications' => ['Vitamine D']
                ],
                'physical_examination' => [
                    'blood_pressure' => '120/80',
                    'heart_rate' => 65,
                    'respiratory_rate' => 16,
                    'temperature' => 36.8,
                    'weight' => 72,
                    'height' => 170,
                    'bmi' => 24.9
                ],
                'cardiovascular_investigations' => [
                    'ecg_rest' => 'normal',
                    'ecg_stress' => 'normal',
                    'echocardiogram' => 'normal',
                    'blood_pressure_monitoring' => 'normal'
                ],
                'final_statement' => [
                    'medical_clearance' => 'cleared',
                    'restrictions' => 'none',
                    'recommendations' => 'Continue current training regimen',
                    'next_assessment' => '6 months'
                ],
                'scat_assessment' => [
                    'symptoms' => 'none',
                    'cognitive_function' => 'normal',
                    'balance' => 'excellent',
                    'coordination' => 'excellent'
                ],
                'status' => 'completed',
                'completed_at' => now()->subDays(30),
                'assessor_id' => $user->id,
                'assessment_date' => now()->subDays(30),
                'notes' => 'Joueur en excellente condition physique. Aucune restriction médicale.',
                'fifa_compliance_data' => [
                    'fifa_standards_met' => true,
                    'compliance_score' => 95,
                    'risk_assessment' => 'low'
                ],
                'fifa_compliant' => true,
                'fifa_approved_at' => now()->subDays(30),
                'fifa_approved_by' => 'Dr. FIFA Medical Committee',
                'form_version' => '2024.1'
            ],
            
            [
                'player_id' => $players->skip(1)->first()->id,
                'athlete_id' => DB::table('athletes')->where('name', $players->skip(1)->first()->first_name . ' ' . $players->skip(1)->first()->last_name)->value('id'),
                'type' => 'annual_review',
                'result_json' => [
                    'cardiovascular_status' => 'good',
                    'musculoskeletal_status' => 'excellent',
                    'neurological_status' => 'good',
                    'overall_assessment' => 'fit_to_play'
                ],
                'medical_history' => [
                    'previous_injuries' => ['Blessure à la cheville (2020)'],
                    'surgeries' => [],
                    'chronic_conditions' => ['Arythmie légère'],
                    'medications' => ['Bêta-bloquant']
                ],
                'physical_examination' => [
                    'blood_pressure' => '118/78',
                    'heart_rate' => 62,
                    'respiratory_rate' => 15,
                    'temperature' => 36.6,
                    'weight' => 83,
                    'height' => 187,
                    'bmi' => 23.7
                ],
                'cardiovascular_investigations' => [
                    'ecg_rest' => 'mild_arrhythmia',
                    'ecg_stress' => 'normal_response',
                    'echocardiogram' => 'normal_structure',
                    'blood_pressure_monitoring' => 'normal'
                ],
                'final_statement' => [
                    'medical_clearance' => 'cleared_with_restrictions',
                    'restrictions' => 'Monitor heart rate during intense exercise',
                    'recommendations' => 'Continue beta-blocker treatment, regular monitoring',
                    'next_assessment' => '3 months'
                ],
                'scat_assessment' => [
                    'symptoms' => 'none',
                    'cognitive_function' => 'normal',
                    'balance' => 'excellent',
                    'coordination' => 'excellent'
                ],
                'status' => 'completed',
                'completed_at' => now()->subDays(15),
                'assessor_id' => $user->id,
                'assessment_date' => now()->subDays(15),
                'notes' => 'Joueur en bonne condition avec surveillance cardiaque requise.',
                'fifa_compliance_data' => [
                    'fifa_standards_met' => true,
                    'compliance_score' => 88,
                    'risk_assessment' => 'moderate'
                ],
                'fifa_compliant' => true,
                'fifa_approved_at' => now()->subDays(15),
                'fifa_approved_by' => 'Dr. FIFA Medical Committee',
                'form_version' => '2024.1'
            ],
            
            [
                'player_id' => $players->skip(2)->first()->id,
                'athlete_id' => DB::table('athletes')->where('name', $players->skip(2)->first()->first_name . ' ' . $players->skip(2)->first()->last_name)->value('id'),
                'type' => 'pre_season',
                'result_json' => [
                    'cardiovascular_status' => 'excellent',
                    'musculoskeletal_status' => 'excellent',
                    'neurological_status' => 'excellent',
                    'overall_assessment' => 'fit_to_play'
                ],
                'medical_history' => [
                    'previous_injuries' => ['Blessure musculaire (2022)'],
                    'surgeries' => [],
                    'chronic_conditions' => [],
                    'medications' => ['Complexe B', 'Magnésium']
                ],
                'physical_examination' => [
                    'blood_pressure' => '115/75',
                    'heart_rate' => 58,
                    'respiratory_rate' => 14,
                    'temperature' => 36.7,
                    'weight' => 75,
                    'height' => 178,
                    'bmi' => 23.7
                ],
                'cardiovascular_investigations' => [
                    'ecg_rest' => 'normal',
                    'ecg_stress' => 'excellent_response',
                    'echocardiogram' => 'normal',
                    'blood_pressure_monitoring' => 'normal'
                ],
                'final_statement' => [
                    'medical_clearance' => 'cleared',
                    'restrictions' => 'none',
                    'recommendations' => 'Continue intensive training, maintain nutrition',
                    'next_assessment' => '4 months'
                ],
                'scat_assessment' => [
                    'symptoms' => 'none',
                    'cognitive_function' => 'excellent',
                    'balance' => 'excellent',
                    'coordination' => 'excellent'
                ],
                'status' => 'completed',
                'completed_at' => now()->subDays(45),
                'assessor_id' => $user->id,
                'assessment_date' => now()->subDays(45),
                'notes' => 'Joueur en parfaite condition physique. Aucune restriction.',
                'fifa_compliance_data' => [
                    'fifa_standards_met' => true,
                    'compliance_score' => 98,
                    'risk_assessment' => 'very_low'
                ],
                'fifa_compliant' => true,
                'fifa_approved_at' => now()->subDays(45),
                'fifa_approved_by' => 'Dr. FIFA Medical Committee',
                'form_version' => '2024.1'
            ],
            
            [
                'player_id' => $players->skip(3)->first()->id,
                'athlete_id' => DB::table('athletes')->where('name', $players->skip(3)->first()->first_name . ' ' . $players->skip(3)->first()->last_name)->value('id'),
                'type' => 'post_injury',
                'result_json' => [
                    'cardiovascular_status' => 'good',
                    'musculoskeletal_status' => 'recovering',
                    'neurological_status' => 'excellent',
                    'overall_assessment' => 'fit_to_play_with_caution'
                ],
                'medical_history' => [
                    'previous_injuries' => ['Blessure ligamentaire genou (2023)', 'Fracture de fatigue (2022)'],
                    'surgeries' => ['Réparation ligamentaire genou (2023)'],
                    'chronic_conditions' => [],
                    'medications' => ['Anti-inflammatoire', 'Physiothérapie']
                ],
                'physical_examination' => [
                    'blood_pressure' => '122/82',
                    'heart_rate' => 64,
                    'respiratory_rate' => 16,
                    'temperature' => 36.9,
                    'weight' => 88,
                    'height' => 194,
                    'bmi' => 23.4
                ],
                'cardiovascular_investigations' => [
                    'ecg_rest' => 'normal',
                    'ecg_stress' => 'good_response',
                    'echocardiogram' => 'normal',
                    'blood_pressure_monitoring' => 'normal'
                ],
                'final_statement' => [
                    'medical_clearance' => 'cleared_with_restrictions',
                    'restrictions' => 'Progressive return to full training, monitor knee stability',
                    'recommendations' => 'Continue physiotherapy, gradual intensity increase',
                    'next_assessment' => '2 months'
                ],
                'scat_assessment' => [
                    'symptoms' => 'none',
                    'cognitive_function' => 'excellent',
                    'balance' => 'good',
                    'coordination' => 'good'
                ],
                'status' => 'completed',
                'completed_at' => now()->subDays(20),
                'assessor_id' => $user->id,
                'assessment_date' => now()->subDays(20),
                'notes' => 'Récupération excellente de la blessure ligamentaire. Reprise progressive autorisée.',
                'fifa_compliance_data' => [
                    'fifa_standards_met' => true,
                    'compliance_score' => 85,
                    'risk_assessment' => 'moderate'
                ],
                'fifa_compliant' => true,
                'fifa_approved_at' => now()->subDays(20),
                'fifa_approved_by' => 'Dr. FIFA Medical Committee',
                'form_version' => '2024.1'
            ],
            
            // PCMA expiré pour tester les alertes
            [
                'player_id' => $players->first()->id,
                'athlete_id' => DB::table('athletes')->where('name', $players->first()->first_name . ' ' . $players->first()->last_name)->value('id'),
                'type' => 'annual_review',
                'result_json' => [
                    'cardiovascular_status' => 'good',
                    'musculoskeletal_status' => 'good',
                    'neurological_status' => 'good',
                    'overall_assessment' => 'fit_to_play'
                ],
                'medical_history' => [
                    'previous_injuries' => ['Blessure au genou (2019)'],
                    'surgeries' => [],
                    'chronic_conditions' => [],
                    'medications' => ['Vitamine D']
                ],
                'physical_examination' => [
                    'blood_pressure' => '120/80',
                    'heart_rate' => 65,
                    'respiratory_rate' => 16,
                    'temperature' => 36.8,
                    'weight' => 72,
                    'height' => 170,
                    'bmi' => 24.9
                ],
                'cardiovascular_investigations' => [
                    'ecg_rest' => 'normal',
                    'ecg_stress' => 'normal',
                    'echocardiogram' => 'normal',
                    'blood_pressure_monitoring' => 'normal'
                ],
                'final_statement' => [
                    'medical_clearance' => 'cleared',
                    'restrictions' => 'none',
                    'recommendations' => 'Continue current training regimen',
                    'next_assessment' => '6 months'
                ],
                'scat_assessment' => [
                    'symptoms' => 'none',
                    'cognitive_function' => 'normal',
                    'balance' => 'excellent',
                    'coordination' => 'excellent'
                ],
                'status' => 'completed',
                'completed_at' => now()->subYear()->subDays(30),
                'assessor_id' => $user->id,
                'assessment_date' => now()->subYear()->subDays(30),
                'notes' => 'PCMA annuel standard. Renouvellement requis.',
                'fifa_compliance_data' => [
                    'fifa_standards_met' => true,
                    'compliance_score' => 90,
                    'risk_assessment' => 'low'
                ],
                'fifa_compliant' => false, // Expiré
                'fifa_approved_at' => now()->subYear()->subDays(30),
                'fifa_approved_by' => 'Dr. FIFA Medical Committee',
                'form_version' => '2023.1'
            ]
        ];

        foreach ($pcmaRecords as $pcmaData) {
            PCMA::create($pcmaData);
        }

        $this->command->info('PCMA records seeded successfully!');
        $this->command->info('Created ' . count($pcmaRecords) . ' PCMA records');
    }
} 