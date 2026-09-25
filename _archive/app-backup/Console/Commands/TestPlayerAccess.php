<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use App\Models\FifaConnectId;
use Illuminate\Support\Facades\Hash;

class TestPlayerAccess extends Command
{
    protected $signature = 'test:player-access';
    protected $description = 'Test Player Dashboard access control';

    public function handle()
    {
        $this->info('🧪 Testing Player Dashboard Access Control...');

        // Create test data
        $this->createTestData();

        $this->info('✅ Test data created successfully!');
        $this->info('');
        $this->info('🔐 Access Control Test Instructions:');
        $this->info('');
        $this->info('1. Try accessing Player Dashboard directly:');
        $this->info('   http://localhost:8000/player-dashboard');
        $this->info('   → Should redirect to login with access_type=player');
        $this->info('');
        $this->info('2. Login with player credentials:');
        $this->info('   http://localhost:8000/login?access_type=player');
        $this->info('   Email: john.doe@testfc.com');
        $this->info('   Password: password123');
        $this->info('');
        $this->info('3. Try accessing Player Dashboard after login:');
        $this->info('   http://localhost:8000/player-dashboard');
        $this->info('   → Should work correctly');
        $this->info('');
        $this->info('4. Try accessing with different access_type:');
        $this->info('   http://localhost:8000/login?access_type=club');
        $this->info('   → Should be denied access');
        $this->info('');
        $this->info('5. Try accessing without access_type:');
        $this->info('   http://localhost:8000/login');
        $this->info('   → Should be denied access to Player Dashboard');
    }

    private function createTestData()
    {
        // Create test association and club
        $association = Association::firstOrCreate([
            'name' => 'Test Football Association',
            'fifa_association_id' => 'TFA-001'
        ], [
            'country' => 'Test Country',
            'association_logo_url' => null,
            'contact_email' => 'test@tfa.com',
            'contact_phone' => '+1234567890',
            'website' => 'https://tfa.test',
            'status' => 'active'
        ]);

        $club = Club::firstOrCreate([
            'name' => 'Test FC',
            'fifa_club_id' => 'TFC-001'
        ], [
            'association_id' => $association->id,
            'country' => 'Test Country',
            'city' => 'Test City',
            'club_logo_url' => null,
            'contact_email' => 'info@testfc.com',
            'contact_phone' => '+1234567890',
            'website' => 'https://testfc.test',
            'status' => 'active'
        ]);

        // Create FIFA Connect ID for player
        $fifaConnectId = FifaConnectId::firstOrCreate([
            'fifa_id' => 'PLAYER-001'
        ], [
            'entity_type' => 'player',
            'status' => 'active',
            'metadata' => [
                'created_by' => 1
            ]
        ]);

        // Create test player
        $player = Player::firstOrCreate([
            'email' => 'john.doe@testfc.com'
        ], [
            'fifa_connect_id' => $fifaConnectId->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1995-06-15',
            'nationality' => 'Test Country',
            'position' => 'Forward',
            'club_id' => $club->id,
            'association_id' => $association->id,
            'height' => 180,
            'weight' => 75,
            'phone' => '+1234567890',
            'jersey_number' => 10,
            'overall_rating' => 85,
            'potential_rating' => 88,
            'value_eur' => 50000000,
            'wage_eur' => 50000,
            'contract_valid_until' => '2026-06-30',
            'player_picture' => null,
            // Player Dashboard fields
            'ghs_physical_score' => 85.5,
            'ghs_mental_score' => 78.2,
            'ghs_civic_score' => 92.1,
            'ghs_sleep_score' => 76.8,
            'ghs_overall_score' => 83.2,
            'ghs_color_code' => 'blue',
            'ghs_ai_suggestions' => [
                'Consider increasing sleep quality by 10% for better recovery',
                'Excellent civic engagement score - keep up the community work',
                'Mental health score could improve with stress management techniques'
            ],
            'ghs_last_updated' => now(),
            'contribution_score' => 87.5,
            'data_value_estimate' => 1250.00,
            'matches_contributed' => 45,
            'training_sessions_logged' => 120,
            'health_records_contributed' => 18,
            'injury_risk_score' => 0.15,
            'injury_risk_level' => 'low',
            'injury_risk_reason' => 'Good fitness levels, regular medical checkups, no recent injuries',
            'weekly_health_tips' => [
                'Stay hydrated during training sessions',
                'Focus on proper warm-up routines',
                'Consider yoga for flexibility improvement'
            ],
            'injury_risk_last_assessed' => now(),
            'match_availability' => true,
            'last_availability_update' => now(),
            'last_data_export' => now()->subDays(30),
            'data_export_count' => 3
        ]);

        // Create user account for the player
        $user = User::firstOrCreate([
            'email' => 'john.doe@testfc.com'
        ], [
            'name' => 'John Doe',
            'password' => Hash::make('password123'),
            'role' => 'player',
            'club_id' => $club->id,
            'association_id' => $association->id,
            'fifa_connect_id' => $fifaConnectId->id,
            'permissions' => ['player_dashboard_access'],
            'status' => 'active'
        ]);

        $this->info("✅ Created test player: {$player->first_name} {$player->last_name}");
        $this->info("✅ Created test user: {$user->email}");
        $this->info("✅ FIFA Connect ID: {$fifaConnectId->fifa_id}");
    }
} 