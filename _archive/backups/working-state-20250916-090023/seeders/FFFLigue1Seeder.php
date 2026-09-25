<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Association;
use App\Models\Confederation;
use App\Models\Club;
use App\Models\Player;
use App\Models\Competition;
use App\Models\Season;
use App\Models\Team;
use App\Models\GameMatch;
use App\Models\Standing;
use Carbon\Carbon;

class FFFLigue1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating Tunisian League data...');

        // Use existing Tunisian Association
        $ftf = Association::find(1); // Fédération Tunisienne de Football
        
        if (!$ftf) {
            $this->command->error('Tunisian Association (FTF) not found!');
            return;
        }

        // Create 2023-2024 Season
        $season = Season::firstOrCreate(
            ['name' => '2023-2024'],
            [
                'name' => '2023-2024',
                'start_date' => '2023-08-12',
                'end_date' => '2024-05-19',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create Tunisian Ligue 1 Competition
        $ligue1 = Competition::firstOrCreate(
            ['short_name' => 'L1-TN'],
            [
                'name' => 'Ligue 1 Tunisienne',
                'short_name' => 'L1-TN',
                'type' => 'league',
                'format' => 'round_robin',
                'max_teams' => 20,
                'min_teams' => 20,
                'start_date' => '2023-08-12',
                'end_date' => '2024-05-19',
                'status' => 'completed',
                'association_id' => $ftf->id,
                'season' => '2023-2024',
                'require_federation_license' => true,
                'fifa_sync_enabled' => true,
                'fifa_sync_status' => 'synced',
                'description' => 'Championnat de Tunisie de football - Division 1',
                'rules' => 'Règlement de la Ligue 1 Tunisienne 2023-2024',
                'country' => 'Tunisie',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Get existing Tunisian clubs
        $existingClubs = Club::where('association_id', $ftf->id)->get();
        $this->command->info('Found ' . $existingClubs->count() . ' existing Tunisian clubs');
        
        // Create additional Tunisian clubs to reach 20 total
        $additionalClubs = [
            ['name' => 'CS Sfaxien', 'short_name' => 'CSS', 'city' => 'Sfax', 'founded_year' => 1928, 'stadium' => 'Stade Taïeb Mhiri'],
            ['name' => 'US Monastir', 'short_name' => 'USM', 'city' => 'Monastir', 'founded_year' => 1959, 'stadium' => 'Stade Mustapha Ben Jannet'],
            ['name' => 'CA Bizertin', 'short_name' => 'CAB', 'city' => 'Bizerte', 'founded_year' => 1928, 'stadium' => 'Stade 15 Octobre'],
            ['name' => 'JS Kairouan', 'short_name' => 'JSK', 'city' => 'Kairouan', 'founded_year' => 1942, 'stadium' => 'Stade Hamda Laouani'],
            ['name' => 'AS Gabès', 'short_name' => 'ASG', 'city' => 'Gabès', 'founded_year' => 1977, 'stadium' => 'Stade Municipal de Gabès'],
            ['name' => 'Stade Tunisien', 'short_name' => 'ST', 'city' => 'Tunis', 'founded_year' => 1948, 'stadium' => 'Stade Chedli Zouiten'],
            ['name' => 'US Ben Guerdane', 'short_name' => 'USBG', 'city' => 'Ben Guerdane', 'founded_year' => 1936, 'stadium' => 'Stade du 7 Mars'],
            ['name' => 'Olympique de Béja', 'short_name' => 'OB', 'city' => 'Béja', 'founded_year' => 1929, 'stadium' => 'Stade Boujemaa Kmiti'],
            ['name' => 'AS Soliman', 'short_name' => 'ASS', 'city' => 'Soliman', 'founded_year' => 1958, 'stadium' => 'Stade Municipal de Soliman'],
            ['name' => 'CS Hammam-Lif', 'short_name' => 'CSHL', 'city' => 'Hammam-Lif', 'founded_year' => 1944, 'stadium' => 'Stade Municipal de Hammam-Lif'],
            ['name' => 'JS Médenine', 'short_name' => 'JSM', 'city' => 'Médenine', 'founded_year' => 1956, 'stadium' => 'Stade Municipal de Médenine'],
            ['name' => 'US Tataouine', 'short_name' => 'UST', 'city' => 'Tataouine', 'founded_year' => 1980, 'stadium' => 'Stade Municipal de Tataouine'],
            ['name' => 'AS Marsa', 'short_name' => 'ASM', 'city' => 'La Marsa', 'founded_year' => 1948, 'stadium' => 'Stade Abdelaziz Chtioui'],
            ['name' => 'ES Métlaoui', 'short_name' => 'ESM', 'city' => 'Métlaoui', 'founded_year' => 1950, 'stadium' => 'Stade Municipal de Métlaoui'],
            ['name' => 'US Siliana', 'short_name' => 'USS', 'city' => 'Siliana', 'founded_year' => 1975, 'stadium' => 'Stade Municipal de Siliana'],
            ['name' => 'AS Djerba', 'short_name' => 'ASD', 'city' => 'Djerba', 'founded_year' => 1960, 'stadium' => 'Stade Municipal de Djerba'],
            ['name' => 'JS Tabarka', 'short_name' => 'JST', 'city' => 'Tabarka', 'founded_year' => 1985, 'stadium' => 'Stade Municipal de Tabarka'],
        ];
        
        // Create the additional clubs
        foreach ($additionalClubs as $clubData) {
            $club = Club::firstOrCreate(
                ['name' => $clubData['name']],
                [
                    'name' => $clubData['name'],
                    'short_name' => $clubData['short_name'],
                    'address' => $clubData['stadium'] . ', ' . $clubData['city'],
                    'founded_year' => $clubData['founded_year'],
                    'status' => 'active',
                    'association_id' => $ftf->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            $this->command->info("Created club: {$club->name}");
        }
        
        // Get all Tunisian clubs (existing + newly created)
        $allClubs = Club::where('association_id', $ftf->id)->get();
        $this->command->info('Total Tunisian clubs: ' . $allClubs->count());

        // Add players to all clubs
        foreach ($allClubs as $club) {
            // Create first team for each club if it doesn't exist
            $team = Team::firstOrCreate(
                ['name' => 'Équipe Première', 'club_id' => $club->id],
                [
                    'name' => 'Équipe Première',
                    'club_id' => $club->id,
                    'level' => 'club',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Check if club already has players
            $existingPlayers = Player::where('club_id', $club->id)->count();
            
            if ($existingPlayers < 50) {
                // Add players to reach 50 total
                $playersToAdd = 50 - $existingPlayers;
                $players = $this->generatePlayers($club->short_name, $playersToAdd);
                
                foreach ($players as $playerData) {
                    Player::firstOrCreate(
                        [
                            'first_name' => $playerData['first_name'],
                            'last_name' => $playerData['last_name'],
                            'club_id' => $club->id,
                        ],
                        [
                            'name' => $playerData['first_name'] . ' ' . $playerData['last_name'],
                            'first_name' => $playerData['first_name'],
                            'last_name' => $playerData['last_name'],
                            'club_id' => $club->id,
                            'association_id' => $ftf->id,
                            'position' => $playerData['position'],
                            'nationality' => $playerData['nationality'],
                            'date_of_birth' => $playerData['date_of_birth'],
                            'license_type' => 'professional',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
                
                $this->command->info("Added {$playersToAdd} players to {$club->name} (now has 50 players)");
            } else {
                $this->command->info("Club {$club->name} already has {$existingPlayers} players");
            }
        }

        // Register clubs in the competition
        $this->registerClubsInCompetition($ligue1, $allClubs);

        $this->command->info('Tunisian League data created successfully!');
    }

    private function generatePlayers($clubShort, $count)
    {
        $firstNames = [
            'Alexandre', 'Antoine', 'Baptiste', 'Benjamin', 'Cédric', 'Damien', 'Édouard', 'Fabien', 'Gabriel', 'Hugo',
            'Ibrahim', 'Julien', 'Kévin', 'Lucas', 'Mathieu', 'Nicolas', 'Olivier', 'Pierre', 'Quentin', 'Romain',
            'Sébastien', 'Thomas', 'Vincent', 'William', 'Yann', 'Zinedine', 'Adrien', 'Benoît', 'Corentin', 'David'
        ];
        
        $lastNames = [
            'Martin', 'Bernard', 'Thomas', 'Petit', 'Robert', 'Richard', 'Durand', 'Dubois', 'Moreau', 'Laurent',
            'Simon', 'Michel', 'Lefebvre', 'Leroy', 'Roux', 'David', 'Bertrand', 'Morel', 'Fournier', 'Girard',
            'André', 'Lefèvre', 'Mercier', 'Dupont', 'Lambert', 'Bonnet', 'François', 'Martinez', 'Legrand', 'Garnier'
        ];
        
        $positions = ['GK', 'DEF', 'MID', 'FWD'];
        $nationalities = ['France', 'Brazil', 'Argentina', 'Spain', 'Portugal', 'Italy', 'Germany', 'Netherlands', 'Belgium', 'Senegal', 'Morocco', 'Algeria', 'Ivory Coast', 'Cameroon', 'Mali', 'Burkina Faso', 'Tunisia', 'Croatia', 'Serbia', 'Poland'];
        
        $players = [];
        $usedNumbers = [];
        
        for ($i = 0; $i < $count; $i++) {
            do {
                $jerseyNumber = rand(1, 99);
            } while (in_array($jerseyNumber, $usedNumbers));
            
            $usedNumbers[] = $jerseyNumber;
            
            $players[] = [
                'first_name' => $firstNames[array_rand($firstNames)],
                'last_name' => $lastNames[array_rand($lastNames)],
                'position' => $positions[array_rand($positions)],
                'jersey_number' => $jerseyNumber,
                'nationality' => $nationalities[array_rand($nationalities)],
                'date_of_birth' => Carbon::now()->subYears(rand(18, 35))->subDays(rand(0, 365))->format('Y-m-d')
            ];
        }
        
        return $players;
    }

    private function registerClubsInCompetition($competition, $clubs)
    {
        $this->command->info('Registering clubs in competition...');

        foreach ($clubs as $index => $club) {
            DB::table('competition_club')->insertOrIgnore([
                'competition_id' => $competition->id,
                'club_id' => $club->id,
                'status' => 'registered',
                'points' => rand(20, 40),
                'goals_for' => rand(20, 45),
                'goals_against' => rand(15, 35),
                'goal_difference' => rand(5, 15),
                'matches_played' => rand(15, 20),
                'wins' => rand(5, 12),
                'draws' => rand(2, 6),
                'losses' => rand(2, 8),
                'registration_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info("Registered {$club->name} in competition");
        }
    }
}
