<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use App\Models\PlayerLicense;
use App\Models\User;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the English Football Association
        $englishFA = Association::where('name', 'The Football Association')->first();
        $associationId = $englishFA ? $englishFA->id : null;

        // Get Manchester United club
        $manUtd = Club::where('name', 'Manchester United')->first();
        $clubId = $manUtd ? $manUtd->id : null;

        // Create players with real photos using reliable image URLs
        $players = [
            [
                'name' => 'Lionel Messi',
                'first_name' => 'Lionel',
                'last_name' => 'Messi',
                'date_of_birth' => '1987-06-24',
                'nationality' => 'Argentina',
                'position' => 'RW',
                'overall_rating' => 93,
                'potential_rating' => 93,
                'age' => 36,
                'height' => 170,
                'weight' => 72,
                'preferred_foot' => 'Left',
                'work_rate' => 'Medium/Medium',
                'player_face_url' => 'https://picsum.photos/400/400?random=1',
                'fifa_connect_id' => '158023',
                'club_id' => $clubId,
                'association_id' => $associationId,
                'status' => 'active'
            ],
            [
                'name' => 'Cristiano Ronaldo',
                'first_name' => 'Cristiano',
                'last_name' => 'Ronaldo',
                'date_of_birth' => '1985-02-05',
                'nationality' => 'Portugal',
                'position' => 'ST',
                'overall_rating' => 88,
                'potential_rating' => 88,
                'age' => 38,
                'height' => 187,
                'weight' => 83,
                'preferred_foot' => 'Right',
                'work_rate' => 'High/High',
                'player_face_url' => 'https://picsum.photos/400/400?random=2',
                'fifa_connect_id' => '20801',
                'club_id' => $clubId,
                'association_id' => $associationId,
                'status' => 'active'
            ],
            [
                'name' => 'Kylian Mbappé',
                'first_name' => 'Kylian',
                'last_name' => 'Mbappé',
                'date_of_birth' => '1998-12-20',
                'nationality' => 'France',
                'position' => 'ST',
                'overall_rating' => 91,
                'potential_rating' => 95,
                'age' => 24,
                'height' => 178,
                'weight' => 73,
                'preferred_foot' => 'Right',
                'work_rate' => 'High/Medium',
                'player_face_url' => 'https://picsum.photos/400/400?random=3',
                'fifa_connect_id' => '231747',
                'club_id' => $clubId,
                'association_id' => $associationId,
                'status' => 'active'
            ],
            [
                'name' => 'Bukayo Saka',
                'first_name' => 'Bukayo',
                'last_name' => 'Saka',
                'date_of_birth' => '2001-09-05',
                'nationality' => 'England',
                'position' => 'RW',
                'overall_rating' => 86,
                'potential_rating' => 90,
                'age' => 22,
                'height' => 178,
                'weight' => 65,
                'preferred_foot' => 'Left',
                'work_rate' => 'High/Medium',
                'player_face_url' => 'https://picsum.photos/400/400?random=4',
                'fifa_connect_id' => '238794',
                'club_id' => $clubId,
                'association_id' => $associationId,
                'status' => 'active'
            ],
            [
                'name' => 'Erling Haaland',
                'first_name' => 'Erling',
                'last_name' => 'Haaland',
                'date_of_birth' => '2000-07-21',
                'nationality' => 'Norway',
                'position' => 'ST',
                'overall_rating' => 91,
                'potential_rating' => 94,
                'age' => 23,
                'height' => 195,
                'weight' => 88,
                'preferred_foot' => 'Left',
                'work_rate' => 'High/Medium',
                'player_face_url' => 'https://picsum.photos/400/400?random=5',
                'fifa_connect_id' => '239085',
                'club_id' => $clubId,
                'association_id' => $associationId,
                'status' => 'active'
            ],
            [
                'name' => 'Kevin De Bruyne',
                'first_name' => 'Kevin',
                'last_name' => 'De Bruyne',
                'date_of_birth' => '1991-06-28',
                'nationality' => 'Belgium',
                'position' => 'CAM',
                'overall_rating' => 91,
                'potential_rating' => 91,
                'age' => 32,
                'height' => 181,
                'weight' => 70,
                'preferred_foot' => 'Right',
                'work_rate' => 'High/Medium',
                'player_face_url' => 'https://picsum.photos/400/400?random=6',
                'fifa_connect_id' => '192985',
                'club_id' => $clubId,
                'association_id' => $associationId,
                'status' => 'active'
            ],
            [
                'name' => 'Virgil van Dijk',
                'first_name' => 'Virgil',
                'last_name' => 'van Dijk',
                'date_of_birth' => '1991-07-08',
                'nationality' => 'Netherlands',
                'position' => 'CB',
                'overall_rating' => 89,
                'potential_rating' => 89,
                'age' => 32,
                'height' => 193,
                'weight' => 92,
                'preferred_foot' => 'Right',
                'work_rate' => 'Medium/Medium',
                'player_face_url' => 'https://picsum.photos/400/400?random=7',
                'fifa_connect_id' => '192985',
                'club_id' => $clubId,
                'association_id' => $associationId,
                'status' => 'active'
            ],
            [
                'name' => 'Jude Bellingham',
                'first_name' => 'Jude',
                'last_name' => 'Bellingham',
                'date_of_birth' => '2003-06-29',
                'nationality' => 'England',
                'position' => 'CM',
                'overall_rating' => 86,
                'potential_rating' => 92,
                'age' => 20,
                'height' => 186,
                'weight' => 75,
                'preferred_foot' => 'Right',
                'work_rate' => 'High/High',
                'player_face_url' => 'https://picsum.photos/400/400?random=8',
                'fifa_connect_id' => '246834',
                'club_id' => $clubId,
                'association_id' => $associationId,
                'status' => 'active'
            ],
            [
                'name' => 'Vinícius Júnior',
                'first_name' => 'Vinícius',
                'last_name' => 'Júnior',
                'date_of_birth' => '2000-07-12',
                'nationality' => 'Brazil',
                'position' => 'LW',
                'overall_rating' => 89,
                'potential_rating' => 93,
                'age' => 23,
                'height' => 176,
                'weight' => 73,
                'preferred_foot' => 'Right',
                'work_rate' => 'High/Medium',
                'player_face_url' => 'https://picsum.photos/400/400?random=9',
                'fifa_connect_id' => '238794',
                'club_id' => $clubId,
                'association_id' => $associationId,
                'status' => 'active'
            ],
            [
                'name' => 'Mohamed Salah',
                'first_name' => 'Mohamed',
                'last_name' => 'Salah',
                'date_of_birth' => '1992-06-15',
                'nationality' => 'Egypt',
                'position' => 'RW',
                'overall_rating' => 89,
                'potential_rating' => 89,
                'age' => 31,
                'height' => 175,
                'weight' => 71,
                'preferred_foot' => 'Left',
                'work_rate' => 'High/Medium',
                'player_face_url' => 'https://picsum.photos/400/400?random=10',
                'fifa_connect_id' => '209331',
                'club_id' => $clubId,
                'association_id' => $associationId,
                'status' => 'active'
            ]
        ];

        foreach ($players as $playerData) {
            Player::updateOrCreate(
                ['fifa_connect_id' => $playerData['fifa_connect_id']],
                $playerData
            );
        }

        // Generate 40 players for each EPL club
        $eplClubs = Club::where('league', 'Premier League')->get();
        $positions = ['GK', 'RB', 'CB', 'CB', 'LB', 'CDM', 'CM', 'CM', 'RM', 'LM', 'CAM', 'RW', 'LW', 'ST', 'CF'];
        $nationalities = ['England', 'France', 'Spain', 'Germany', 'Brazil', 'Portugal', 'Netherlands', 'Belgium', 'Italy', 'Argentina', 'Nigeria', 'Senegal', 'Ivory Coast', 'USA', 'Japan', 'South Korea', 'Norway', 'Sweden', 'Denmark', 'Scotland'];
        $firstNames = ['John', 'James', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Joseph', 'Thomas', 'Charles', 'Daniel', 'Matthew', 'Anthony', 'Mark', 'Donald', 'Steven', 'Paul', 'Andrew', 'Joshua', 'Kenneth'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin'];
        $associationId = Association::where('name', 'The Football Association')->first()?->id;

        foreach ($eplClubs as $club) {
            for ($i = 1; $i <= 40; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $position = $positions[array_rand($positions)];
                $nationality = $nationalities[array_rand($nationalities)];
                $dob = now()->subYears(rand(18, 35))->subDays(rand(0, 364))->format('Y-m-d');
                $rating = rand(60, 90);
                $potential = $rating + rand(0, 10);
                $height = rand(170, 200);
                $weight = rand(65, 95);
                $fifaId = strtoupper(substr($club->short_name, 0, 3)) . sprintf('%03d', $i);
                $player = Player::updateOrCreate(
                    ['fifa_connect_id' => $fifaId],
                    [
                        'name' => "$firstName $lastName",
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'date_of_birth' => $dob,
                        'nationality' => $nationality,
                        'position' => $position,
                        'overall_rating' => $rating,
                        'potential_rating' => $potential,
                        'age' => now()->diffInYears($dob),
                        'height' => $height,
                        'weight' => $weight,
                        'preferred_foot' => rand(0, 1) ? 'Right' : 'Left',
                        'work_rate' => 'Medium/Medium',
                        'player_face_url' => 'https://picsum.photos/400/400?random=' . rand(1000, 9999),
                        'club_id' => $club->id,
                        'association_id' => $associationId,
                        'status' => 'active',
                    ]
                );
                // Add sample performance data for last season
                $player->performances()->updateOrCreate(
                    ['season' => '2023/24'],
                    [
                        'appearances' => rand(0, 38),
                        'goals' => rand(0, 20),
                        'assists' => rand(0, 15),
                        'minutes_played' => rand(0, 3420),
                        'yellow_cards' => rand(0, 8),
                        'red_cards' => rand(0, 2),
                        'clean_sheets' => $position === 'GK' ? rand(0, 20) : null,
                        'season_rating' => rand(60, 90),
                        'performance_date' => '2024-05-19',
                        'created_by' => 1,
                    ]
                );
            }
        }

        $testClub = Club::where('name', 'Test Club')->first();
        $testAssociation = Association::where('name', 'Test Association')->first();
        Player::updateOrCreate(
            ['fifa_connect_id' => 'TEST_PLAYER_001'],
            [
                'name' => 'Test Player',
                'first_name' => 'Test',
                'last_name' => 'Player',
                'date_of_birth' => '2000-01-01',
                'nationality' => 'Testland',
                'position' => 'CM',
                'overall_rating' => 75,
                'potential_rating' => 85,
                'age' => 24,
                'height' => 180,
                'weight' => 75,
                'preferred_foot' => 'Right',
                'work_rate' => 'Medium/Medium',
                'player_face_url' => 'https://via.placeholder.com/100x100.png?text=Test+Player',
                'club_id' => $testClub ? $testClub->id : null,
                'association_id' => $testAssociation ? $testAssociation->id : null,
                'status' => 'active',
            ]
        );

        // Seed 25 player license requests in 'pending' status for association review
        echo "Seeding 25 player license requests...\n";
        $playersForLicense = Player::inRandomOrder()->limit(25)->get();
        $created = 0;
        foreach ($playersForLicense as $player) {
            try {
                // Only create if not already pending
                $existing = PlayerLicense::where('player_id', $player->id)->where('status', 'pending')->first();
                if (!$existing) {
                    $license = PlayerLicense::create([
                        'player_id' => $player->id,
                        'club_id' => $player->club_id,
                        'status' => 'pending',
                        'requested_by' => User::where('club_id', $player->club_id)->whereIn('role', ['club_admin', 'club_manager'])->inRandomOrder()->first()?->id ?? 1,
                    ]);
                    if ($license) {
                        $created++;
                        echo "Created license for player_id={$player->id}, club_id={$player->club_id}\n";
                    } else {
                        echo "Failed to create license for player_id={$player->id}\n";
                    }
                }
            } catch (\Exception $e) {
                echo "Exception for player_id={$player->id}: " . $e->getMessage() . "\n";
            }
        }
        echo "Total licenses created: $created\n";
    }
}
