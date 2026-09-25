<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateSampleStakeholders extends Command
{
    protected $signature = 'stakeholders:create-sample {--count=10 : Number of sample stakeholders to create}';
    protected $description = 'Create sample stakeholders with profile pictures for testing';

    public function handle()
    {
        $count = $this->option('count');
        $this->info("Creating {$count} sample stakeholders...");

        // Get or create a club and association
        $club = Club::first() ?? Club::create([
            'name' => 'Sample FC',
            'short_name' => 'SFC',
            'country' => 'Sample Country',
            'city' => 'Sample City',
            'founded_year' => 2020,
            'status' => 'active'
        ]);

        $association = Association::first() ?? Association::create([
            'name' => 'Sample Football Association',
            'short_name' => 'SFA',
            'country' => 'Sample Country',
            'founded_year' => 2020,
            'status' => 'active'
        ]);

        $this->info("Using Club: {$club->name}");
        $this->info("Using Association: {$association->name}");

        // Sample data arrays
        $sampleNames = [
            'John Smith', 'Maria Garcia', 'Ahmed Hassan', 'Sarah Johnson', 'Michael Chen',
            'Fatima Al-Zahra', 'David Wilson', 'Elena Rodriguez', 'James Brown', 'Aisha Patel',
            'Robert Taylor', 'Sofia Ivanova', 'Mohammed Ali', 'Emma Thompson', 'Carlos Silva',
            'Yuki Tanaka', 'Anna Kowalski', 'Lucas Santos', 'Zara Ahmed', 'Thomas Mueller'
        ];

        $sampleRoles = [
            'club_admin', 'club_manager', 'club_medical', 'association_admin', 'association_registrar',
            'association_medical', 'referee', 'assistant_referee', 'fourth_official', 'team_doctor',
            'physiotherapist', 'sports_scientist'
        ];

        $samplePositions = [
            'Goalkeeper', 'Defender', 'Midfielder', 'Forward', 'Winger', 'Striker'
        ];

        $sampleNationalities = [
            'English', 'Spanish', 'French', 'German', 'Italian', 'Brazilian', 'Argentine',
            'Portuguese', 'Dutch', 'Belgian', 'Swiss', 'Austrian', 'Swedish', 'Norwegian'
        ];

        $createdCount = 0;

        for ($i = 0; $i < $count; $i++) {
            $name = $sampleNames[$i % count($sampleNames)];
            $role = $sampleRoles[$i % count($sampleRoles)];
            $email = Str::slug($name) . $i . '@sample.com';

            // Check if user already exists
            $existingUser = User::where('email', $email)->first();
            
            if ($existingUser) {
                // Update existing user with new photo
                $existingUser->update([
                    'profile_picture_url' => $this->generateProfilePictureUrl($name),
                    'profile_picture_alt' => $name . ' Profile Picture'
                ]);
                $user = $existingUser;
                $this->line("Updated existing user: {$name} ({$email})");
            } else {
                // Create new user
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'role' => $role,
                    'club_id' => in_array($role, ['club_admin', 'club_manager', 'club_medical']) ? $club->id : null,
                    'association_id' => in_array($role, ['association_admin', 'association_registrar', 'association_medical', 'referee', 'assistant_referee', 'fourth_official']) ? $association->id : null,
                    'fifa_connect_id' => 'SAMPLE_' . strtoupper(Str::random(8)),
                    'status' => 'active',
                    'profile_picture_url' => $this->generateProfilePictureUrl($name),
                    'profile_picture_alt' => $name . ' Profile Picture',
                    'permissions' => $this->getDefaultPermissions($role)
                ]);
                $this->line("Created new user: {$name} ({$email})");
            }

            // If it's a player role, also create a Player record
            if ($role === 'player') {
                $position = $samplePositions[$i % count($samplePositions)];
                $nationality = $sampleNationalities[$i % count($sampleNationalities)];

                Player::create([
                    'fifa_connect_id' => $user->fifa_connect_id,
                    'name' => $name,
                    'first_name' => explode(' ', $name)[0],
                    'last_name' => explode(' ', $name)[1] ?? '',
                    'date_of_birth' => now()->subYears(rand(18, 35)),
                    'nationality' => $nationality,
                    'position' => $position,
                    'club_id' => $club->id,
                    'association_id' => $association->id,
                    'height' => rand(165, 195),
                    'weight' => rand(65, 85),
                    'preferred_foot' => rand(0, 1) ? 'Right' : 'Left',
                    'weak_foot' => rand(1, 5),
                    'skill_moves' => rand(1, 5),
                    'international_reputation' => rand(1, 5),
                    'overall_rating' => rand(60, 90),
                    'potential_rating' => rand(65, 95),
                    'player_picture' => $this->generateProfilePictureUrl($name),
                    'email' => $email,
                    'status' => 'active'
                ]);
            }

            $createdCount++;
            $this->line("Created {$role}: {$name} ({$email})");
        }

        $this->info("Successfully created {$createdCount} sample stakeholders!");
        $this->info("You can now visit /stakeholder-gallery to see them.");
    }

    private function generateProfilePictureUrl($name): string
    {
        // Generate a placeholder image URL using a service like UI Faces or DiceBear
        $initials = $this->getInitials($name);
        $colors = ['blue', 'green', 'red', 'purple', 'orange', 'teal', 'pink', 'indigo'];
        $color = $colors[array_rand($colors)];
        
        // Using realistic photos from Unsplash
        return $this->getRealisticPhoto($name);
    }

    private function getInitials($name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        return substr($initials, 0, 2);
    }

    private function getDefaultPermissions($role): array
    {
        return match($role) {
            'club_admin' => ['player_registration_access', 'competition_management_access', 'healthcare_access'],
            'club_manager' => ['player_registration_access', 'healthcare_access'],
            'club_medical' => ['healthcare_access'],
            'association_admin' => ['player_registration_access', 'competition_management_access', 'healthcare_access', 'back_office_access'],
            'association_registrar' => ['player_registration_access', 'competition_management_access'],
            'association_medical' => ['healthcare_access'],
            'referee', 'assistant_referee', 'fourth_official' => ['competition_management_access'],
            'team_doctor', 'physiotherapist', 'sports_scientist' => ['healthcare_access'],
            default => []
        };
    }

    private function getRealisticPhoto($name): string
    {
        // Collection of realistic professional photos from Unsplash
        $photos = [
            // Football players and sports professionals
            'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1552318965-6e6be7484ada?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1560272564-c83b66b1ad12?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face'
        ];
        
        // Use name hash to consistently assign photos
        $hash = crc32($name);
        return $photos[$hash % count($photos)];
    }
} 