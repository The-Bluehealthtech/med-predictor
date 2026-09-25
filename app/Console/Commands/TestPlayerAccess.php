<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
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

        // Create user account for the player
        $user = User::firstOrCreate([
            'email' => 'john.doe@testfc.com'
        ], [
            'name' => 'John Doe',
            'password' => Hash::make('password123'),
            'role' => 'player',
            'club_id' => $club->id,
            'association_id' => $association->id,
            'fifa_connect_id' => null,
            'permissions' => ['player_dashboard_access'],
            'status' => 'active'
        ]);

        $this->info("✅ Created test player: {$player->first_name} {$player->last_name}");
        $this->info("✅ Created test user: {$user->email}");
        $this->info('ℹ️ FIFA Connect ID: non attribué (donnée autoritative externe)');
    }
} 