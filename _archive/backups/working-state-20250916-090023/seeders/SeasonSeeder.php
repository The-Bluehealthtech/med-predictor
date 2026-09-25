<?php

namespace Database\Seeders;

use App\Models\Season;
use App\Models\User;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first system admin user for created_by field
        $systemAdmin = User::where('role', 'system_admin')->first();
        
        if (!$systemAdmin) {
            // If no system admin exists, create a default one or use the first user
            $systemAdmin = User::first();
        }

        // Create current season (2024/25)
        Season::updateOrCreate(
            ['short_name' => '2024/25'],
            [
                'name' => '2024/25 Season',
                'start_date' => '2024-08-01',
                'end_date' => '2025-07-31',
                'registration_start_date' => '2024-06-01',
                'registration_end_date' => '2024-07-31',
                'status' => 'active',
                'is_current' => true,
                'description' => 'Current football season 2024/25',
                'settings' => json_encode([
                    'max_players_per_team' => 25,
                    'transfer_window_open' => true,
                    'medical_checks_required' => true,
                ]),
                'created_by' => $systemAdmin ? $systemAdmin->id : 1,
                'updated_by' => $systemAdmin ? $systemAdmin->id : 1,
            ]
        );

        // Create previous season (2023/24)
        Season::updateOrCreate(
            ['short_name' => '2023/24'],
            [
                'name' => '2023/24 Season',
                'start_date' => '2023-08-01',
                'end_date' => '2024-07-31',
                'registration_start_date' => '2023-06-01',
                'registration_end_date' => '2023-07-31',
                'status' => 'completed',
                'is_current' => false,
                'description' => 'Previous football season 2023/24',
                'settings' => json_encode([
                    'max_players_per_team' => 25,
                    'transfer_window_open' => false,
                    'medical_checks_required' => true,
                ]),
                'created_by' => $systemAdmin ? $systemAdmin->id : 1,
                'updated_by' => $systemAdmin ? $systemAdmin->id : 1,
            ]
        );

        // Create next season (2025/26)
        Season::updateOrCreate(
            ['short_name' => '2025/26'],
            [
                'name' => '2025/26 Season',
                'start_date' => '2025-08-01',
                'end_date' => '2026-07-31',
                'registration_start_date' => '2025-06-01',
                'registration_end_date' => '2025-07-31',
                'status' => 'upcoming',
                'is_current' => false,
                'description' => 'Upcoming football season 2025/26',
                'settings' => json_encode([
                    'max_players_per_team' => 25,
                    'transfer_window_open' => false,
                    'medical_checks_required' => true,
                ]),
                'created_by' => $systemAdmin ? $systemAdmin->id : 1,
                'updated_by' => $systemAdmin ? $systemAdmin->id : 1,
            ]
        );

        $this->command->info('Seasons seeded successfully!');
    }
} 