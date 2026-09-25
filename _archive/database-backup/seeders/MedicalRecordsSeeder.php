<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MedicalRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = DB::table('players')->get();
        
        foreach ($players as $player) {
            // Consultation de routine
            DB::table('medical_records')->insert([
                'player_id' => $player->id,
                'record_type' => 'consultation',
                'title' => 'Consultation de routine',
                'description' => 'Bilan de santé complet, tous les paramètres sont normaux',
                'doctor_name' => 'Dr. Sophie Moreau',
                'medical_center' => 'Centre médical du club',
                'record_date' => Carbon::now()->subDays(rand(1, 30)),
                'next_appointment' => Carbon::now()->addMonths(3),
                'status' => 'completed',
                'medications' => json_encode([]),
                'test_results' => json_encode([
                    'blood_pressure' => '120/80',
                    'heart_rate' => rand(65, 85),
                    'temperature' => '36.8',
                    'weight' => rand(65, 85),
                    'height' => rand(170, 190)
                ]),
                'cost' => 150.00,
                'notes' => 'Joueur en excellente santé, apte à la compétition',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Vaccination
            DB::table('medical_records')->insert([
                'player_id' => $player->id,
                'record_type' => 'vaccination',
                'title' => 'Vaccin COVID-19 - 3ème dose',
                'description' => 'Vaccination de rappel COVID-19, aucun effet secondaire',
                'doctor_name' => 'Dr. Jean Martin',
                'medical_center' => 'Centre de vaccination',
                'record_date' => Carbon::now()->subDays(rand(10, 60)),
                'next_appointment' => null,
                'status' => 'completed',
                'medications' => json_encode(['COVID-19 Vaccine']),
                'test_results' => json_encode(['vaccination_status' => 'completed']),
                'cost' => 0.00,
                'notes' => 'Vaccination obligatoire pour la compétition',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Test de performance
            DB::table('medical_records')->insert([
                'player_id' => $player->id,
                'record_type' => 'test',
                'title' => 'Test de performance cardiovasculaire',
                'description' => 'Test d\'effort sur tapis roulant, VO2 max excellent',
                'doctor_name' => 'Dr. Pierre Dubois',
                'medical_center' => 'Laboratoire de physiologie',
                'record_date' => Carbon::now()->subDays(rand(5, 25)),
                'next_appointment' => Carbon::now()->addMonths(6),
                'status' => 'completed',
                'medications' => json_encode([]),
                'test_results' => json_encode([
                    'vo2_max' => rand(55, 75),
                    'max_heart_rate' => rand(180, 200),
                    'recovery_time' => rand(2, 5),
                    'endurance_score' => rand(80, 95)
                ]),
                'cost' => 200.00,
                'notes' => 'Performance cardiovasculaire excellente',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Prescription médicale
            DB::table('medical_records')->insert([
                'player_id' => $player->id,
                'record_type' => 'prescription',
                'title' => 'Prescription vitamines et minéraux',
                'description' => 'Compléments nutritionnels pour optimiser la performance',
                'doctor_name' => 'Dr. Marie Laurent',
                'medical_center' => 'Centre de nutrition sportive',
                'record_date' => Carbon::now()->subDays(rand(1, 15)),
                'next_appointment' => Carbon::now()->addMonths(1),
                'status' => 'active',
                'medications' => json_encode([
                    'vitamin_d' => '2000 UI/jour',
                    'omega_3' => '1000mg/jour',
                    'magnesium' => '400mg/jour',
                    'vitamin_c' => '500mg/jour'
                ]),
                'test_results' => json_encode([]),
                'cost' => 75.00,
                'notes' => 'Compléments autorisés, pas de TUE nécessaire',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Consultation spécialisée
            DB::table('medical_records')->insert([
                'player_id' => $player->id,
                'record_type' => 'consultation',
                'title' => 'Consultation nutritionniste',
                'description' => 'Plan alimentaire personnalisé pour la compétition',
                'doctor_name' => 'Dr. Claire Rousseau',
                'medical_center' => 'Centre de nutrition sportive',
                'record_date' => Carbon::now()->subDays(rand(2, 20)),
                'next_appointment' => Carbon::now()->addMonths(2),
                'status' => 'completed',
                'medications' => json_encode([]),
                'test_results' => json_encode([
                    'body_fat' => rand(8, 15),
                    'muscle_mass' => rand(65, 80),
                    'hydration_level' => rand(55, 65),
                    'nutrition_score' => rand(75, 90)
                ]),
                'cost' => 120.00,
                'notes' => 'Plan nutritionnel adapté au calendrier sportif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
