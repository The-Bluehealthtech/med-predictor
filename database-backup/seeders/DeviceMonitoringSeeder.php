<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeviceMonitoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = DB::table('players')->get();
        
        foreach ($players as $player) {
            // Smartwatch
            DB::table('device_monitoring')->insert([
                'player_id' => $player->id,
                'device_type' => 'smartwatch',
                'device_name' => 'Apple Watch Series 9',
                'device_model' => 'GPS + Cellular 45mm',
                'serial_number' => 'AW' . rand(100000, 999999),
                'status' => 'active',
                'activation_date' => Carbon::now()->subMonths(rand(1, 6)),
                'last_sync' => Carbon::now()->subHours(rand(1, 12)),
                'next_maintenance' => Carbon::now()->addMonths(3),
                'current_data' => json_encode([
                    'heart_rate' => rand(65, 85),
                    'steps' => rand(8000, 15000),
                    'calories' => rand(400, 800),
                    'sleep_hours' => rand(7, 9),
                    'battery' => rand(20, 100),
                    'gps_active' => true,
                    'last_activity' => Carbon::now()->subMinutes(rand(5, 60))->toISOString()
                ]),
                'settings' => json_encode([
                    'heart_rate_monitoring' => true,
                    'gps_tracking' => true,
                    'sleep_tracking' => true,
                    'notifications' => true,
                    'workout_detection' => true
                ]),
                'notes' => 'Smartwatch principal pour le suivi quotidien',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // GPS Tracker
            DB::table('device_monitoring')->insert([
                'player_id' => $player->id,
                'device_type' => 'gps_tracker',
                'device_name' => 'Catapult OptimEye S5',
                'device_model' => 'Professional GPS Tracker',
                'serial_number' => 'CT' . rand(100000, 999999),
                'status' => 'active',
                'activation_date' => Carbon::now()->subMonths(rand(2, 8)),
                'last_sync' => Carbon::now()->subHours(rand(1, 6)),
                'next_maintenance' => Carbon::now()->addMonths(2),
                'current_data' => json_encode([
                    'distance' => rand(8, 12),
                    'speed_max' => rand(25, 35),
                    'speed_avg' => rand(15, 22),
                    'acceleration' => rand(3, 8),
                    'deceleration' => rand(3, 8),
                    'sprints' => rand(15, 25),
                    'last_match' => Carbon::now()->subDays(rand(1, 7))->toISOString()
                ]),
                'settings' => json_encode([
                    'gps_frequency' => '10Hz',
                    'accelerometer' => true,
                    'gyroscope' => true,
                    'battery_saver' => false,
                    'auto_sync' => true
                ]),
                'notes' => 'Tracker GPS professionnel pour l\'analyse des performances',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Moniteur cardiaque
            DB::table('device_monitoring')->insert([
                'player_id' => $player->id,
                'device_type' => 'heart_monitor',
                'device_name' => 'Polar H10',
                'device_model' => 'Chest Strap Heart Rate Monitor',
                'serial_number' => 'PL' . rand(100000, 999999),
                'status' => 'active',
                'activation_date' => Carbon::now()->subMonths(rand(1, 4)),
                'last_sync' => Carbon::now()->subHours(rand(2, 8)),
                'next_maintenance' => Carbon::now()->addMonths(4),
                'current_data' => json_encode([
                    'heart_rate_current' => rand(65, 85),
                    'heart_rate_max' => rand(180, 200),
                    'heart_rate_rest' => rand(45, 65),
                    'hrv' => rand(20, 60),
                    'training_load' => rand(200, 400),
                    'recovery_time' => rand(12, 48),
                    'last_workout' => Carbon::now()->subDays(rand(1, 3))->toISOString()
                ]),
                'settings' => json_encode([
                    'heart_rate_zones' => true,
                    'hrv_monitoring' => true,
                    'training_load' => true,
                    'recovery_advisor' => true,
                    'bluetooth' => true
                ]),
                'notes' => 'Moniteur cardiaque pour l\'optimisation de l\'entraînement',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Capteur de sommeil
            DB::table('device_monitoring')->insert([
                'player_id' => $player->id,
                'device_type' => 'sleep_monitor',
                'device_name' => 'Oura Ring Gen 3',
                'device_model' => 'Smart Ring',
                'serial_number' => 'OR' . rand(100000, 999999),
                'status' => 'active',
                'activation_date' => Carbon::now()->subMonths(rand(1, 3)),
                'last_sync' => Carbon::now()->subHours(rand(8, 16)),
                'next_maintenance' => Carbon::now()->addMonths(6),
                'current_data' => json_encode([
                    'sleep_score' => rand(70, 95),
                    'sleep_duration' => rand(7, 9),
                    'deep_sleep' => rand(1.5, 3),
                    'rem_sleep' => rand(1.5, 2.5),
                    'sleep_efficiency' => rand(80, 95),
                    'readiness_score' => rand(60, 90),
                    'last_sleep' => Carbon::now()->subHours(rand(8, 16))->toISOString()
                ]),
                'settings' => json_encode([
                    'sleep_tracking' => true,
                    'activity_tracking' => true,
                    'recovery_monitoring' => true,
                    'notifications' => true,
                    'auto_sync' => true
                ]),
                'notes' => 'Anneau connecté pour le suivi du sommeil et de la récupération',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Tablette médicale
            DB::table('device_monitoring')->insert([
                'player_id' => $player->id,
                'device_type' => 'medical_tablet',
                'device_name' => 'iPad Pro 12.9"',
                'device_model' => '6th Generation',
                'serial_number' => 'IP' . rand(100000, 999999),
                'status' => 'active',
                'activation_date' => Carbon::now()->subMonths(rand(3, 12)),
                'last_sync' => Carbon::now()->subHours(rand(1, 24)),
                'next_maintenance' => Carbon::now()->addMonths(6),
                'current_data' => json_encode([
                    'battery' => rand(30, 100),
                    'storage_used' => rand(20, 80),
                    'last_backup' => Carbon::now()->subDays(rand(1, 7))->toISOString(),
                    'apps_installed' => 15,
                    'security_status' => 'updated'
                ]),
                'settings' => json_encode([
                    'medical_apps' => true,
                    'secure_access' => true,
                    'auto_backup' => true,
                    'encryption' => true,
                    'remote_wipe' => true
                ]),
                'notes' => 'Tablette médicale pour la consultation des dossiers et la formation',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
