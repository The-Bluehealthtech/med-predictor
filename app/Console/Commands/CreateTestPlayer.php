<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Support\Facades\Hash;

class CreateTestPlayer extends Command
{
    protected $signature = 'create:test-player';
    protected $description = 'Create a simple test player user for Player Dashboard testing';

    public function handle()
    {
        $this->info('🔧 Creating test player user...');

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

        $this->info("✅ Created test player user: {$user->name}");
        $this->info("✅ Email: {$user->email}");
        $this->info("✅ Password: password123");
        $this->info("✅ Role: {$user->role}");
        $this->info('ℹ️ FIFA Connect ID: non attribué (donnée autoritative externe)');
        $this->info('');
        $this->info('🔐 Test the Player Dashboard:');
        $this->info('1. Go to: http://localhost:8000/login?access_type=player');
        $this->info('2. Login with the credentials above');
        $this->info('3. Access: http://localhost:8000/player-dashboard');
    }
} 