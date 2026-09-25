<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Association;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting test data seeding...');

        // Create test association if not exists
        $association = Association::firstOrCreate(
            ['name' => 'Test Football Association'],
            [
                'short_name' => 'TFA',
                'country' => 'Testland',
                'confederation' => 'Test Confederation',
                'fifa_ranking' => 1,
                'association_logo_url' => 'https://via.placeholder.com/100x100.png?text=TFA',
                'nation_flag_url' => 'https://via.placeholder.com/100x100.png?text=Flag',
                'fifa_version' => 'FIFA 24',
                'status' => 'active'
            ]
        );

        // Create test clubs
        $clubs = [
            [
                'name' => 'Test Club A',
                'short_name' => 'TCA',
                'country' => 'Testland',
                'city' => 'Test City A',
                'stadium' => 'Test Stadium A',
                'founded_year' => 2020,
                'logo_url' => 'https://via.placeholder.com/100x100.png?text=TCA',
                'website' => 'https://testcluba.com',
                'email' => 'info@testcluba.com',
                'phone' => '+1234567891',
                'address' => '123 Test St A',
                'fifa_connect_id' => 'TEST_CLUB_A_001',
                'association_id' => $association->id,
                'league' => 'Test League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 1000000,
                'wage_budget_eur' => 500000,
                'transfer_budget_eur' => 200000,
                'reputation' => 50,
                'facilities_level' => 2,
                'youth_development' => 2,
                'scouting_network' => 1,
                'medical_team' => 1,
                'coaching_staff' => 1,
            ],
            [
                'name' => 'Test Club B',
                'short_name' => 'TCB',
                'country' => 'Testland',
                'city' => 'Test City B',
                'stadium' => 'Test Stadium B',
                'founded_year' => 2020,
                'logo_url' => 'https://via.placeholder.com/100x100.png?text=TCB',
                'website' => 'https://testclubb.com',
                'email' => 'info@testclubb.com',
                'phone' => '+1234567892',
                'address' => '456 Test St B',
                'fifa_connect_id' => 'TEST_CLUB_B_001',
                'association_id' => $association->id,
                'league' => 'Test League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 800000,
                'wage_budget_eur' => 400000,
                'transfer_budget_eur' => 150000,
                'reputation' => 45,
                'facilities_level' => 2,
                'youth_development' => 2,
                'scouting_network' => 1,
                'medical_team' => 1,
                'coaching_staff' => 1,
            ],
            [
                'name' => 'Test Club C',
                'short_name' => 'TCC',
                'country' => 'Testland',
                'city' => 'Test City C',
                'stadium' => 'Test Stadium C',
                'founded_year' => 2020,
                'logo_url' => 'https://via.placeholder.com/100x100.png?text=TCC',
                'website' => 'https://testclubc.com',
                'email' => 'info@testclubc.com',
                'phone' => '+1234567893',
                'address' => '789 Test St C',
                'fifa_connect_id' => 'TEST_CLUB_C_001',
                'association_id' => $association->id,
                'league' => 'Test League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 600000,
                'wage_budget_eur' => 300000,
                'transfer_budget_eur' => 100000,
                'reputation' => 40,
                'facilities_level' => 2,
                'youth_development' => 2,
                'scouting_network' => 1,
                'medical_team' => 1,
                'coaching_staff' => 1,
            ]
        ];

        foreach ($clubs as $clubData) {
            $club = Club::firstOrCreate(
                ['name' => $clubData['name']],
                $clubData
            );

            // Create team for each club
            Team::firstOrCreate(
                [
                    'club_id' => $club->id,
                    'name' => $club->name . ' First Team',
                    'type' => 'first_team'
                ],
                [
                    'association_id' => $association->id,
                    'formation' => '4-3-3',
                    'tactical_style' => 'possession',
                    'playing_philosophy' => 'attacking',
                    'coach_name' => 'Test Coach',
                    'assistant_coach' => 'Test Assistant',
                    'fitness_coach' => 'Test Fitness Coach',
                    'goalkeeper_coach' => 'Test GK Coach',
                    'scout' => 'Test Scout',
                    'medical_staff' => 'Test Medical Staff',
                    'status' => 'active',
                    'season' => '2024/25',
                    'competition_level' => 'professional',
                    'budget_allocation' => 500000,
                    'training_facility' => 'Test Training Ground',
                    'home_ground' => $club->stadium,
                    'founded_year' => 2020,
                    'capacity' => 25000,
                    'colors' => 'Red, White',
                    'logo_url' => $club->logo_url,
                    'website' => $club->website,
                    'description' => 'First team of ' . $club->name
                ]
            );
        }

        // Create test competitions
        $competitions = [
            [
                'name' => 'Test League Championship',
                'short_name' => 'TLC',
                'type' => 'league',
                'country' => 'Testland',
                'region' => 'Test Region',
                'season' => '2024/25',
                'start_date' => '2024-08-01',
                'end_date' => '2025-05-31',
                'registration_deadline' => '2024-07-31',
                'max_teams' => 10,
                'min_teams' => 8,
                'format' => 'round_robin',
                'prize_pool' => 1000000,
                'entry_fee' => 0,
                'status' => 'active',
                'description' => 'Test league championship for testing purposes',
                'rules' => 'Standard football rules apply',
                'logo_url' => 'https://via.placeholder.com/100x100.png?text=TLC',
                'website' => 'https://testleague.com',
                'organizer' => 'Test Football Association',
                'sponsors' => 'Test Sponsor',
                'broadcast_partners' => 'Test TV'
            ],
            [
                'name' => 'Test Cup Competition',
                'short_name' => 'TCC',
                'type' => 'cup',
                'country' => 'Testland',
                'region' => 'Test Region',
                'season' => '2024/25',
                'start_date' => '2024-09-01',
                'end_date' => '2025-04-30',
                'registration_deadline' => '2024-08-31',
                'max_teams' => 16,
                'min_teams' => 8,
                'format' => 'knockout',
                'prize_pool' => 500000,
                'entry_fee' => 0,
                'status' => 'active',
                'description' => 'Test cup competition for testing purposes',
                'rules' => 'Standard cup rules apply',
                'logo_url' => 'https://via.placeholder.com/100x100.png?text=TCC',
                'website' => 'https://testcup.com',
                'organizer' => 'Test Football Association',
                'sponsors' => 'Test Sponsor',
                'broadcast_partners' => 'Test TV'
            ]
        ];

        foreach ($competitions as $competitionData) {
            Competition::firstOrCreate(
                ['name' => $competitionData['name']],
                $competitionData
            );
        }

        // Create test players
        $players = [
            [
                'name' => 'Test Player 1',
                'first_name' => 'Test',
                'last_name' => 'Player 1',
                'date_of_birth' => '1995-01-15',
                'nationality' => 'Testland',
                'position' => 'ST',
                'height' => 180,
                'weight' => 75,
                'overall_rating' => 75,
                'potential_rating' => 80,
                'value_eur' => 5000000,
                'wage_eur' => 50000,
                'fifa_connect_id' => 'TEST_PLAYER_001',
                'club_id' => Club::where('name', 'Test Club A')->first()->id ?? null,
            ],
            [
                'name' => 'Test Player 2',
                'first_name' => 'Test',
                'last_name' => 'Player 2',
                'date_of_birth' => '1993-03-20',
                'nationality' => 'Testland',
                'position' => 'CM',
                'height' => 175,
                'weight' => 70,
                'overall_rating' => 78,
                'potential_rating' => 82,
                'value_eur' => 6000000,
                'wage_eur' => 60000,
                'fifa_connect_id' => 'TEST_PLAYER_002',
                'club_id' => Club::where('name', 'Test Club B')->first()->id ?? null,
            ],
            [
                'name' => 'Test Player 3',
                'first_name' => 'Test',
                'last_name' => 'Player 3',
                'date_of_birth' => '1997-07-10',
                'nationality' => 'Testland',
                'position' => 'CB',
                'height' => 185,
                'weight' => 80,
                'overall_rating' => 72,
                'potential_rating' => 78,
                'value_eur' => 4000000,
                'wage_eur' => 40000,
                'fifa_connect_id' => 'TEST_PLAYER_003',
                'club_id' => Club::where('name', 'Test Club C')->first()->id ?? null,
            ],
            [
                'name' => 'Test Player 4',
                'first_name' => 'Test',
                'last_name' => 'Player 4',
                'date_of_birth' => '1994-11-05',
                'nationality' => 'Testland',
                'position' => 'GK',
                'height' => 188,
                'weight' => 85,
                'overall_rating' => 70,
                'potential_rating' => 75,
                'value_eur' => 3000000,
                'wage_eur' => 35000,
                'fifa_connect_id' => 'TEST_PLAYER_004',
                'club_id' => Club::where('name', 'Test Club A')->first()->id ?? null,
            ],
            [
                'name' => 'Test Player 5',
                'first_name' => 'Test',
                'last_name' => 'Player 5',
                'date_of_birth' => '1996-05-25',
                'nationality' => 'Testland',
                'position' => 'RW',
                'height' => 170,
                'weight' => 65,
                'overall_rating' => 76,
                'potential_rating' => 81,
                'value_eur' => 5500000,
                'wage_eur' => 55000,
                'fifa_connect_id' => 'TEST_PLAYER_005',
                'club_id' => Club::where('name', 'Test Club B')->first()->id ?? null,
            ]
        ];

        foreach ($players as $playerData) {
            Player::firstOrCreate(
                ['name' => $playerData['name']],
                $playerData
            );
        }

        // Create system admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@medpredictor.com'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('password123'),
                'role' => 'system_admin',
                'entity_type' => null,
                'entity_id' => null,
                'fifa_connect_id' => 'FIFA_SYS_ADMIN_001',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access',
                    'healthcare_access',
                    'system_administration',
                    'user_management',
                    'back_office_access',
                    'fifa_connect_access',
                    'fifa_data_sync',
                    'account_request_management',
                    'audit_trail_access',
                    'role_management',
                    'system_configuration',
                    'club_management',
                    'team_management',
                    'association_management',
                    'license_management',
                    'match_sheet_management',
                    'referee_access',
                    'player_dashboard_access',
                    'health_record_management',
                    'league_championship_access',
                    'registration_requests_management_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'System Administrator Profile'
            ]
        );

        $this->command->info('✅ Test data seeded successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   - 1 Association');
        $this->command->info('   - 3 Clubs with teams');
        $this->command->info('   - 2 Competitions');
        $this->command->info('   - 5 Players');
        $this->command->info('   - 1 System Admin user (admin@medpredictor.com / password123)');
        $this->command->info('');
        $this->command->info('🔗 You can now test the /modules/competitions page with this data!');
    }
} 