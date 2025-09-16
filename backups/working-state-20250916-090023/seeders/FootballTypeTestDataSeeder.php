<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\Player;
use App\Models\Competition;
use App\Models\User;
use App\Models\Association;
use App\Models\Season;
use Illuminate\Support\Facades\Hash;

class FootballTypeTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test association if it doesn't exist
        $association = Association::firstOrCreate(
            ['name' => 'Test Football Association'],
            [
                'name' => 'Test Football Association',
                'country' => 'United Kingdom',
                'logo_url' => '/images/defaults/association-logo.png',
                'contact_email' => 'info@testfa.com',
                'contact_phone' => '+44 123 456 7890',
                'address' => '123 Football Street, London, UK',
                'website' => 'https://testfa.com',
                'founded_date' => '2020-01-01',
                'status' => 'active'
            ]
        );

        // Create test season if it doesn't exist
        $season = Season::firstOrCreate(
            ['name' => '2024/25 Season'],
            [
                'name' => '2024/25 Season',
                'start_date' => '2024-08-01',
                'end_date' => '2025-05-31',
                'status' => 'active',
                'association_id' => $association->id
            ]
        );

        // Create test clubs for different football types
        $this->createTestClubs($association);

        // Create test competitions
        $this->createTestCompetitions($association, $season);

        // Create test users for different roles
        $this->createTestUsers($association);
    }

    private function createTestClubs($association)
    {
        $clubs = [
            // 11-a-side clubs
            [
                'name' => 'Manchester United',
                'short_name' => 'Man Utd',
                'city' => 'Manchester',
                'country' => 'England',
                'founded_year' => 1878,
                'stadium_name' => 'Old Trafford',
                'stadium_capacity' => 74140,
                'logo_url' => '/images/defaults/club-logo.png',
                'website' => 'https://manutd.com',
                'email' => 'info@manutd.com',
                'phone' => '+44 161 868 8000',
                'address' => 'Sir Matt Busby Way, Old Trafford, Manchester M16 0RA',
                'status' => 'active',
                'football_type' => '11aside'
            ],
            [
                'name' => 'Liverpool FC',
                'short_name' => 'Liverpool',
                'city' => 'Liverpool',
                'country' => 'England',
                'founded_year' => 1892,
                'stadium_name' => 'Anfield',
                'stadium_capacity' => 53394,
                'logo_url' => '/images/defaults/club-logo.png',
                'website' => 'https://liverpoolfc.com',
                'email' => 'info@liverpoolfc.com',
                'phone' => '+44 151 260 6677',
                'address' => 'Anfield Road, Liverpool L4 0TH',
                'status' => 'active',
                'football_type' => '11aside'
            ],
            [
                'name' => 'Arsenal FC',
                'short_name' => 'Arsenal',
                'city' => 'London',
                'country' => 'England',
                'founded_year' => 1886,
                'stadium_name' => 'Emirates Stadium',
                'stadium_capacity' => 60704,
                'logo_url' => '/images/defaults/club-logo.png',
                'website' => 'https://arsenal.com',
                'email' => 'info@arsenal.com',
                'phone' => '+44 20 7619 5003',
                'address' => 'Hornsey Road, London N7 7AJ',
                'status' => 'active',
                'football_type' => '11aside'
            ],
            // Women's clubs
            [
                'name' => 'Arsenal Women',
                'short_name' => 'Arsenal W',
                'city' => 'London',
                'country' => 'England',
                'founded_year' => 1987,
                'stadium_name' => 'Meadow Park',
                'stadium_capacity' => 4500,
                'logo_url' => '/images/defaults/club-logo.png',
                'website' => 'https://arsenal.com/women',
                'email' => 'women@arsenal.com',
                'phone' => '+44 20 7619 5003',
                'address' => 'Meadow Park, Borehamwood WD6 5AL',
                'status' => 'active',
                'football_type' => 'womens'
            ],
            [
                'name' => 'Chelsea Women',
                'short_name' => 'Chelsea W',
                'city' => 'London',
                'country' => 'England',
                'founded_year' => 1992,
                'stadium_name' => 'Kingsmeadow',
                'stadium_capacity' => 4850,
                'logo_url' => '/images/defaults/club-logo.png',
                'website' => 'https://chelseafc.com/women',
                'email' => 'women@chelseafc.com',
                'phone' => '+44 20 7386 9373',
                'address' => 'Jack Goodchild Way, Kingston upon Thames KT1 3PB',
                'status' => 'active',
                'football_type' => 'womens'
            ],
            // Futsal clubs
            [
                'name' => 'Futsal United',
                'short_name' => 'Futsal Utd',
                'city' => 'Manchester',
                'country' => 'England',
                'founded_year' => 2015,
                'stadium_name' => 'Indoor Arena',
                'stadium_capacity' => 2000,
                'logo_url' => '/images/defaults/club-logo.png',
                'website' => 'https://futsalunited.com',
                'email' => 'info@futsalunited.com',
                'phone' => '+44 161 123 4567',
                'address' => 'Indoor Sports Centre, Manchester M1 1AA',
                'status' => 'active',
                'football_type' => 'futsal'
            ],
            [
                'name' => 'Court Masters',
                'short_name' => 'Court Masters',
                'city' => 'London',
                'country' => 'England',
                'founded_year' => 2018,
                'stadium_name' => 'Futsal Court',
                'stadium_capacity' => 1500,
                'logo_url' => '/images/defaults/club-logo.png',
                'website' => 'https://courtmasters.com',
                'email' => 'info@courtmasters.com',
                'phone' => '+44 20 123 4567',
                'address' => 'Futsal Centre, London SW1 1AA',
                'status' => 'active',
                'football_type' => 'futsal'
            ],
            // Beach soccer clubs
            [
                'name' => 'Beach United',
                'short_name' => 'Beach Utd',
                'city' => 'Brighton',
                'country' => 'England',
                'founded_year' => 2020,
                'stadium_name' => 'Beach Arena',
                'stadium_capacity' => 1000,
                'logo_url' => '/images/defaults/club-logo.png',
                'website' => 'https://beachunited.com',
                'email' => 'info@beachunited.com',
                'phone' => '+44 1273 123 456',
                'address' => 'Beach Sports Complex, Brighton BN1 1AA',
                'status' => 'active',
                'football_type' => 'beach'
            ],
            [
                'name' => 'Sand Warriors',
                'short_name' => 'Sand Warriors',
                'city' => 'Bournemouth',
                'country' => 'England',
                'founded_year' => 2021,
                'stadium_name' => 'Sand Court',
                'stadium_capacity' => 800,
                'logo_url' => '/images/defaults/club-logo.png',
                'website' => 'https://sandwarriors.com',
                'email' => 'info@sandwarriors.com',
                'phone' => '+44 1202 123 456',
                'address' => 'Beach Sports Arena, Bournemouth BH1 1AA',
                'status' => 'active',
                'football_type' => 'beach'
            ]
        ];

        foreach ($clubs as $clubData) {
            Club::firstOrCreate(
                ['name' => $clubData['name']],
                array_merge($clubData, ['association_id' => $association->id])
            );
        }
    }

    private function createTestCompetitions($association, $season)
    {
        $competitions = [
            [
                'name' => 'Premier League 2024/25',
                'type' => 'league',
                'format' => 'round_robin',
                'start_date' => '2024-08-10',
                'end_date' => '2025-05-25',
                'status' => 'active',
                'football_type' => '11aside',
                'description' => 'Top tier men\'s football league in England'
            ],
            [
                'name' => 'FA Cup 2024/25',
                'type' => 'cup',
                'format' => 'knockout',
                'start_date' => '2024-08-01',
                'end_date' => '2025-05-17',
                'status' => 'active',
                'football_type' => '11aside',
                'description' => 'Annual knockout football competition'
            ],
            [
                'name' => 'Women\'s Super League 2024/25',
                'type' => 'league',
                'format' => 'round_robin',
                'start_date' => '2024-09-01',
                'end_date' => '2025-05-18',
                'status' => 'active',
                'football_type' => 'womens',
                'description' => 'Top tier women\'s football league in England'
            ],
            [
                'name' => 'Futsal Championship 2024/25',
                'type' => 'league',
                'format' => 'round_robin',
                'start_date' => '2024-09-15',
                'end_date' => '2025-04-30',
                'status' => 'active',
                'football_type' => 'futsal',
                'description' => 'National futsal championship'
            ],
            [
                'name' => 'Beach Soccer Cup 2024/25',
                'type' => 'cup',
                'format' => 'knockout',
                'start_date' => '2024-06-01',
                'end_date' => '2024-08-31',
                'status' => 'active',
                'football_type' => 'beach',
                'description' => 'Annual beach soccer tournament'
            ]
        ];

        foreach ($competitions as $competitionData) {
            Competition::firstOrCreate(
                ['name' => $competitionData['name']],
                array_merge($competitionData, [
                    'association_id' => $association->id,
                    'season_id' => $season->id
                ])
            );
        }
    }

    private function createTestUsers($association)
    {
        $users = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@testfc.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'entity_type' => 'App\\Models\\Association',
                'entity_id' => $association->id
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@testfc.com',
                'password' => Hash::make('password'),
                'role' => 'healthcare',
                'entity_type' => 'App\\Models\\Association',
                'entity_id' => $association->id
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@testfc.com',
                'password' => Hash::make('password'),
                'role' => 'referee',
                'entity_type' => 'App\\Models\\Association',
                'entity_id' => $association->id
            ],
            [
                'name' => 'Emma Davis',
                'email' => 'emma.davis@testfc.com',
                'password' => Hash::make('password'),
                'role' => 'player',
                'entity_type' => 'App\\Models\\Association',
                'entity_id' => $association->id
            ]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
} 