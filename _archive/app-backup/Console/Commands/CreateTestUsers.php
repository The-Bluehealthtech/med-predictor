<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUsers extends Command
{
    protected $signature = 'users:create-test';
    protected $description = 'Create test users with known credentials';

    public function handle()
    {
        $this->info('Creating test users...');

        $testUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@testfc.com',
                'role' => 'player',
                'fifa_connect_id' => 'TEST_PLAYER_001'
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@testfc.com',
                'role' => 'system_admin',
                'fifa_connect_id' => 'TEST_ADMIN_001'
            ],
            [
                'name' => 'Club Manager',
                'email' => 'manager@testfc.com',
                'role' => 'club_manager',
                'fifa_connect_id' => 'TEST_MANAGER_001'
            ],
            [
                'name' => 'Medical Staff',
                'email' => 'medical@testfc.com',
                'role' => 'club_medical',
                'fifa_connect_id' => 'TEST_MEDICAL_001'
            ]
        ];

        foreach ($testUsers as $userData) {
            $existing = User::where('email', $userData['email'])->first();
            
            if ($existing) {
                // Update existing user
                $existing->update([
                    'password' => Hash::make('password123'),
                    'status' => 'active'
                ]);
                $this->line("Updated: {$existing->name} ({$existing->email})");
            } else {
                // Create new user
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password123'),
                    'role' => $userData['role'],
                    'status' => 'active',
                    'fifa_connect_id' => $userData['fifa_connect_id'],
                    'profile_picture_url' => $this->getRealisticPlayerPhoto($userData['name']),
                    'profile_picture_alt' => $userData['name'] . ' Profile Picture'
                ]);
                $this->line("Created: {$user->name} ({$user->email})");
            }
        }

        $this->info('Test users ready!');
        $this->info('All users have password: password123');
        $this->info('');
        $this->info('Test Credentials:');
        $this->info('1. Player: john.doe@testfc.com / password123');
        $this->info('2. Admin: admin@testfc.com / password123');
        $this->info('3. Manager: manager@testfc.com / password123');
        $this->info('4. Medical: medical@testfc.com / password123');
    }

    private function getRealisticPlayerPhoto($name): string
    {
        // Collection of realistic football player photos from Unsplash
        $playerPhotos = [
            'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1552318965-6e6be7484ada?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1560272564-c83b66b1ad12?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face'
        ];
        
        // Use name hash to consistently assign photos
        $hash = crc32($name);
        return $playerPhotos[$hash % count($playerPhotos)];
    }
} 