<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManchesterUnitedAndFAUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Manchester United club
        $manchesterUnited = Club::where('name', 'Manchester United')->first();
        
        // Get The Football Association
        $footballAssociation = Association::where('name', 'The Football Association')->first();

        if (!$manchesterUnited) {
            $this->command->error('Manchester United club not found. Please run ClubSeeder first.');
            return;
        }

        if (!$footballAssociation) {
            $this->command->error('The Football Association not found. Please run AssociationSeeder first.');
            return;
        }

        // Create Manchester United Users
        $manUtdUsers = [
            [
                'name' => 'Erik ten Hag',
                'email' => 'erik.tenhag@manutd.com',
                'password' => Hash::make('password123'),
                'role' => 'club_admin',
                'club_id' => $manchesterUnited->id,
                'association_id' => $footballAssociation->id,
                'fifa_connect_id' => 'MUFC_ADMIN_001',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access',
                    'healthcare_access',
                    'fifa_connect_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'Erik ten Hag - Manchester United Manager'
            ],
            [
                'name' => 'John Murtough',
                'email' => 'john.murtough@manutd.com',
                'password' => Hash::make('password123'),
                'role' => 'club_manager',
                'club_id' => $manchesterUnited->id,
                'association_id' => $footballAssociation->id,
                'fifa_connect_id' => 'MUFC_MGR_001',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access',
                    'healthcare_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'John Murtough - Manchester United Director of Football'
            ],
            [
                'name' => 'Dr. Gary O\'Driscoll',
                'email' => 'gary.odriscoll@manutd.com',
                'password' => Hash::make('password123'),
                'role' => 'club_medical',
                'club_id' => $manchesterUnited->id,
                'association_id' => $footballAssociation->id,
                'fifa_connect_id' => 'MUFC_MED_001',
                'permissions' => [
                    'healthcare_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'Dr. Gary O\'Driscoll - Manchester United Head of Medical'
            ],
            [
                'name' => 'Steve McClaren',
                'email' => 'steve.mcclaren@manutd.com',
                'password' => Hash::make('password123'),
                'role' => 'club_manager',
                'club_id' => $manchesterUnited->id,
                'association_id' => $footballAssociation->id,
                'fifa_connect_id' => 'MUFC_MGR_002',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'Steve McClaren - Manchester United Assistant Manager'
            ],
            [
                'name' => 'Richard Hawkins',
                'email' => 'richard.hawkins@manutd.com',
                'password' => Hash::make('password123'),
                'role' => 'club_medical',
                'club_id' => $manchesterUnited->id,
                'association_id' => $footballAssociation->id,
                'fifa_connect_id' => 'MUFC_MED_002',
                'permissions' => [
                    'healthcare_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'Richard Hawkins - Manchester United Fitness Coach'
            ]
        ];

        // Create The Football Association Users
        $faUsers = [
            [
                'name' => 'Mark Bullingham',
                'email' => 'mark.bullingham@thefa.com',
                'password' => Hash::make('password123'),
                'role' => 'association_admin',
                'club_id' => null,
                'association_id' => $footballAssociation->id,
                'fifa_connect_id' => 'FA_ADMIN_001',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access',
                    'healthcare_access',
                    'fifa_connect_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'Mark Bullingham - FA Chief Executive'
            ],
            [
                'name' => 'Debbie Hewitt',
                'email' => 'debbie.hewitt@thefa.com',
                'password' => Hash::make('password123'),
                'role' => 'association_admin',
                'club_id' => null,
                'association_id' => $footballAssociation->id,
                'fifa_connect_id' => 'FA_ADMIN_002',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access',
                    'healthcare_access',
                    'fifa_connect_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'Debbie Hewitt - FA Chair'
            ],
            [
                'name' => 'Dr. Charlotte Cowie',
                'email' => 'charlotte.cowie@thefa.com',
                'password' => Hash::make('password123'),
                'role' => 'association_medical',
                'club_id' => null,
                'association_id' => $footballAssociation->id,
                'fifa_connect_id' => 'FA_MED_001',
                'permissions' => [
                    'healthcare_access',
                    'player_registration_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'Dr. Charlotte Cowie - FA Head of Performance Medicine'
            ],
            [
                'name' => 'James Kendall',
                'email' => 'james.kendall@thefa.com',
                'password' => Hash::make('password123'),
                'role' => 'association_registrar',
                'club_id' => null,
                'association_id' => $footballAssociation->id,
                'fifa_connect_id' => 'FA_REG_001',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'James Kendall - FA Director of Football Development'
            ],
            [
                'name' => 'Kay Cossington',
                'email' => 'kay.cossington@thefa.com',
                'password' => Hash::make('password123'),
                'role' => 'association_registrar',
                'club_id' => null,
                'association_id' => $footballAssociation->id,
                'fifa_connect_id' => 'FA_REG_002',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access'
                ],
                'status' => 'active',
                'login_count' => 0,
                'profile_picture_url' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face',
                'profile_picture_alt' => 'Kay Cossington - FA Head of Women\'s Technical'
            ]
        ];

        // Create all users
        $allUsers = array_merge($manUtdUsers, $faUsers);

        foreach ($allUsers as $userData) {
            // Check if user already exists
            $existingUser = User::where('email', $userData['email'])->first();
            
            if (!$existingUser) {
                User::create($userData);
                $this->command->info("Created user: {$userData['name']} ({$userData['email']})");
            } else {
                $this->command->warn("User already exists: {$userData['name']} ({$userData['email']})");
            }
        }

        $this->command->info('Manchester United and FA users seeded successfully!');
        $this->command->info('Created users for Manchester United and The Football Association.');
        
        // Display login credentials
        $this->command->info('');
        $this->command->info('=== Login Credentials ===');
        $this->command->info('All users use password: password123');
        $this->command->info('');
        $this->command->info('Manchester United Users:');
        foreach ($manUtdUsers as $user) {
            $this->command->info("- {$user['name']}: {$user['email']} (Role: {$user['role']})");
        }
        $this->command->info('');
        $this->command->info('The Football Association Users:');
        foreach ($faUsers as $user) {
            $this->command->info("- {$user['name']}: {$user['email']} (Role: {$user['role']})");
        }
    }
} 