<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Club;
use Carbon\Carbon;

class PremierLeaguePlayersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👥 Création des joueurs Premier League 2023/2024...');
        
        // Arsenal
        $this->createArsenalPlayers();
        
        // Aston Villa
        $this->createAstonVillaPlayers();
        
        // Bournemouth
        $this->createBournemouthPlayers();
        
        // Brentford
        $this->createBrentfordPlayers();
        
        // Brighton
        $this->createBrightonPlayers();
        
        // Burnley
        $this->createBurnleyPlayers();
        
        // Chelsea
        $this->createChelseaPlayers();
        
        // Crystal Palace
        $this->createCrystalPalacePlayers();
        
        // Everton
        $this->createEvertonPlayers();
        
        // Fulham
        $this->createFulhamPlayers();
        
        // Liverpool
        $this->createLiverpoolPlayers();
        
        // Luton Town
        $this->createLutonTownPlayers();
        
        // Manchester City
        $this->createManchesterCityPlayers();
        
        // Manchester United
        $this->createManchesterUnitedPlayers();
        
        // Newcastle United
        $this->createNewcastlePlayers();
        
        // Nottingham Forest
        $this->createNottinghamForestPlayers();
        
        // Sheffield United
        $this->createSheffieldUnitedPlayers();
        
        // Tottenham Hotspur
        $this->createTottenhamPlayers();
        
        // West Ham United
        $this->createWestHamPlayers();
        
        // Wolverhampton Wanderers
        $this->createWolvesPlayers();
        
        $this->command->info('✅ Tous les joueurs Premier League créés !');
    }

    private function createArsenalPlayers()
    {
        $club = Club::where('name', 'Arsenal')->first();
        if (!$club) return;
        
        $players = [
            [
                'name' => 'David Raya',
                'first_name' => 'David',
                'last_name' => 'Raya',
                'date_of_birth' => '1995-09-15',
                'nationality' => 'Spain',
                'position' => 'GK',
                'height' => 183,
                'weight' => 80,
                'preferred_foot' => 'right',
                'overall_rating' => 82,
                'potential_rating' => 84,
                'value_eur' => 35000000,
                'wage_eur' => 120000,
                'contract_valid_until' => '2027-06-30',
                'fifa_connect_id' => 'ARS_RAYA_001',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_raya.jpg',
                'jersey_number' => 1,
            ],
            [
                'name' => 'William Saliba',
                'first_name' => 'William',
                'last_name' => 'Saliba',
                'date_of_birth' => '2001-03-24',
                'nationality' => 'France',
                'position' => 'DEF',
                'height' => 192,
                'weight' => 85,
                'preferred_foot' => 'right',
                'overall_rating' => 85,
                'potential_rating' => 89,
                'value_eur' => 65000000,
                'wage_eur' => 180000,
                'contract_valid_until' => '2027-06-30',
                'fifa_connect_id' => 'ARS_SALIBA_002',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_saliba.jpg',
                'jersey_number' => 2,
            ],
            [
                'name' => 'Gabriel Magalhães',
                'first_name' => 'Gabriel',
                'last_name' => 'Magalhães',
                'date_of_birth' => '1997-12-19',
                'nationality' => 'Brazil',
                'position' => 'DEF',
                'height' => 190,
                'weight' => 78,
                'preferred_foot' => 'left',
                'overall_rating' => 83,
                'potential_rating' => 86,
                'value_eur' => 55000000,
                'wage_eur' => 150000,
                'contract_valid_until' => '2027-06-30',
                'fifa_connect_id' => 'ARS_GABRIEL_003',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_gabriel.jpg',
                'jersey_number' => 6,
            ],
            [
                'name' => 'Ben White',
                'first_name' => 'Ben',
                'last_name' => 'White',
                'date_of_birth' => '1997-10-08',
                'nationality' => 'England',
                'position' => 'DEF',
                'height' => 186,
                'weight' => 77,
                'preferred_foot' => 'right',
                'overall_rating' => 82,
                'potential_rating' => 84,
                'value_eur' => 45000000,
                'wage_eur' => 140000,
                'contract_valid_until' => '2028-06-30',
                'fifa_connect_id' => 'ARS_WHITE_004',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_white.jpg',
                'jersey_number' => 4,
            ],
            [
                'name' => 'Oleksandr Zinchenko',
                'first_name' => 'Oleksandr',
                'last_name' => 'Zinchenko',
                'date_of_birth' => '1996-12-15',
                'nationality' => 'Ukraine',
                'position' => 'DEF',
                'height' => 175,
                'weight' => 71,
                'preferred_foot' => 'left',
                'overall_rating' => 80,
                'potential_rating' => 82,
                'value_eur' => 40000000,
                'wage_eur' => 130000,
                'contract_valid_until' => '2026-06-30',
                'fifa_connect_id' => 'ARS_ZINCHENKO_005',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_zinchenko.jpg',
                'jersey_number' => 35,
            ],
            [
                'name' => 'Declan Rice',
                'first_name' => 'Declan',
                'last_name' => 'Rice',
                'date_of_birth' => '1999-01-14',
                'nationality' => 'England',
                'position' => 'MID',
                'height' => 185,
                'weight' => 80,
                'preferred_foot' => 'right',
                'overall_rating' => 86,
                'potential_rating' => 88,
                'value_eur' => 90000000,
                'wage_eur' => 200000,
                'contract_valid_until' => '2028-06-30',
                'fifa_connect_id' => 'ARS_RICE_006',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_rice.jpg',
                'jersey_number' => 41,
            ],
            [
                'name' => 'Martin Ødegaard',
                'first_name' => 'Martin',
                'last_name' => 'Ødegaard',
                'date_of_birth' => '1998-12-17',
                'nationality' => 'Norway',
                'position' => 'MID',
                'height' => 178,
                'weight' => 68,
                'preferred_foot' => 'left',
                'overall_rating' => 86,
                'potential_rating' => 88,
                'value_eur' => 85000000,
                'wage_eur' => 180000,
                'contract_valid_until' => '2028-06-30',
                'fifa_connect_id' => 'ARS_ODEGAARD_007',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_odegaard.jpg',
                'jersey_number' => 8,
            ],
            [
                'name' => 'Bukayo Saka',
                'first_name' => 'Bukayo',
                'last_name' => 'Saka',
                'date_of_birth' => '2001-09-05',
                'nationality' => 'England',
                'position' => 'MID',
                'height' => 178,
                'weight' => 72,
                'preferred_foot' => 'left',
                'overall_rating' => 86,
                'potential_rating' => 90,
                'value_eur' => 100000000,
                'wage_eur' => 200000,
                'contract_valid_until' => '2027-06-30',
                'fifa_connect_id' => 'ARS_SAKA_008',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_saka.jpg',
                'jersey_number' => 7,
            ],
            [
                'name' => 'Gabriel Martinelli',
                'first_name' => 'Gabriel',
                'last_name' => 'Martinelli',
                'date_of_birth' => '2001-06-18',
                'nationality' => 'Brazil',
                'position' => 'FWD',
                'height' => 178,
                'weight' => 75,
                'preferred_foot' => 'right',
                'overall_rating' => 84,
                'potential_rating' => 88,
                'value_eur' => 75000000,
                'wage_eur' => 160000,
                'contract_valid_until' => '2027-06-30',
                'fifa_connect_id' => 'ARS_MARTINELLI_009',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_martinelli.jpg',
                'jersey_number' => 11,
            ],
            [
                'name' => 'Kai Havertz',
                'first_name' => 'Kai',
                'last_name' => 'Havertz',
                'date_of_birth' => '1999-06-11',
                'nationality' => 'Germany',
                'position' => 'FWD',
                'height' => 189,
                'weight' => 82,
                'preferred_foot' => 'left',
                'overall_rating' => 82,
                'potential_rating' => 85,
                'value_eur' => 60000000,
                'wage_eur' => 170000,
                'contract_valid_until' => '2028-06-30',
                'fifa_connect_id' => 'ARS_HAVERTZ_010',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_havertz.jpg',
                'jersey_number' => 29,
            ],
            [
                'name' => 'Gabriel Jesus',
                'first_name' => 'Gabriel',
                'last_name' => 'Jesus',
                'date_of_birth' => '1997-04-03',
                'nationality' => 'Brazil',
                'position' => 'FWD',
                'height' => 175,
                'weight' => 73,
                'preferred_foot' => 'right',
                'overall_rating' => 83,
                'potential_rating' => 85,
                'value_eur' => 65000000,
                'wage_eur' => 180000,
                'contract_valid_until' => '2027-06-30',
                'fifa_connect_id' => 'ARS_JESUS_011',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_jesus.jpg',
                'jersey_number' => 9,
            ],
        ];

        foreach ($players as $playerData) {
            $this->createPlayer($club, $playerData);
        }
        
        $this->command->info("✅ Arsenal: " . count($players) . " joueurs créés");
    }

    private function createAstonVillaPlayers()
    {
        $club = Club::where('name', 'Aston Villa')->first();
        if (!$club) return;
        
        $players = [
            [
                'name' => 'Emiliano Martínez',
                'first_name' => 'Emiliano',
                'last_name' => 'Martínez',
                'date_of_birth' => '1992-09-02',
                'nationality' => 'Argentina',
                'position' => 'GK',
                'height' => 195,
                'weight' => 88,
                'preferred_foot' => 'right',
                'overall_rating' => 84,
                'potential_rating' => 85,
                'value_eur' => 45000000,
                'wage_eur' => 120000,
                'contract_valid_until' => '2027-06-30',
                'fifa_connect_id' => 'AVL_MARTINEZ_001',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_martinez.jpg',
                'jersey_number' => 1,
            ],
            [
                'name' => 'Matty Cash',
                'first_name' => 'Matty',
                'last_name' => 'Cash',
                'date_of_birth' => '1997-08-07',
                'nationality' => 'Poland',
                'position' => 'DEF',
                'height' => 185,
                'weight' => 75,
                'preferred_foot' => 'right',
                'overall_rating' => 78,
                'potential_rating' => 80,
                'value_eur' => 25000000,
                'wage_eur' => 80000,
                'contract_valid_until' => '2027-06-30',
                'fifa_connect_id' => 'AVL_CASH_002',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_cash.jpg',
                'jersey_number' => 2,
            ],
            [
                'name' => 'Diego Carlos',
                'first_name' => 'Diego',
                'last_name' => 'Carlos',
                'date_of_birth' => '1993-03-15',
                'nationality' => 'Brazil',
                'position' => 'DEF',
                'height' => 186,
                'weight' => 79,
                'preferred_foot' => 'right',
                'overall_rating' => 79,
                'potential_rating' => 80,
                'value_eur' => 30000000,
                'wage_eur' => 90000,
                'contract_valid_until' => '2026-06-30',
                'fifa_connect_id' => 'AVL_CARLOS_003',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_carlos.jpg',
                'jersey_number' => 3,
            ],
            [
                'name' => 'Pau Torres',
                'first_name' => 'Pau',
                'last_name' => 'Torres',
                'date_of_birth' => '1997-01-16',
                'nationality' => 'Spain',
                'position' => 'DEF',
                'height' => 191,
                'weight' => 80,
                'preferred_foot' => 'left',
                'overall_rating' => 82,
                'potential_rating' => 84,
                'value_eur' => 45000000,
                'wage_eur' => 110000,
                'contract_valid_until' => '2028-06-30',
                'fifa_connect_id' => 'AVL_TORRES_004',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_torres.jpg',
                'jersey_number' => 14,
            ],
            [
                'name' => 'Lucas Digne',
                'first_name' => 'Lucas',
                'last_name' => 'Digne',
                'date_of_birth' => '1993-07-20',
                'nationality' => 'France',
                'position' => 'DEF',
                'height' => 178,
                'weight' => 74,
                'preferred_foot' => 'left',
                'overall_rating' => 78,
                'potential_rating' => 79,
                'value_eur' => 25000000,
                'wage_eur' => 85000,
                'contract_valid_until' => '2026-06-30',
                'fifa_connect_id' => 'AVL_DIGNE_005',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_digne.jpg',
                'jersey_number' => 12,
            ],
            [
                'name' => 'Douglas Luiz',
                'first_name' => 'Douglas',
                'last_name' => 'Luiz',
                'date_of_birth' => '1998-05-09',
                'nationality' => 'Brazil',
                'position' => 'MID',
                'height' => 178,
                'weight' => 74,
                'preferred_foot' => 'right',
                'overall_rating' => 81,
                'potential_rating' => 83,
                'value_eur' => 50000000,
                'wage_eur' => 100000,
                'contract_valid_until' => '2026-06-30',
                'fifa_connect_id' => 'AVL_LUIZ_006',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_luiz.jpg',
                'jersey_number' => 6,
            ],
            [
                'name' => 'John McGinn',
                'first_name' => 'John',
                'last_name' => 'McGinn',
                'date_of_birth' => '1994-10-18',
                'nationality' => 'Scotland',
                'position' => 'MID',
                'height' => 178,
                'weight' => 70,
                'preferred_foot' => 'right',
                'overall_rating' => 80,
                'potential_rating' => 81,
                'value_eur' => 35000000,
                'wage_eur' => 95000,
                'contract_valid_until' => '2027-06-30',
                'fifa_connect_id' => 'AVL_MCGINN_007',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_mcginn.jpg',
                'jersey_number' => 7,
            ],
            [
                'name' => 'Leon Bailey',
                'first_name' => 'Leon',
                'last_name' => 'Bailey',
                'date_of_birth' => '1997-08-09',
                'nationality' => 'Jamaica',
                'position' => 'MID',
                'height' => 178,
                'weight' => 70,
                'preferred_foot' => 'left',
                'overall_rating' => 78,
                'potential_rating' => 80,
                'value_eur' => 30000000,
                'wage_eur' => 85000,
                'contract_valid_until' => '2025-06-30',
                'fifa_connect_id' => 'AVL_BAILEY_008',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_bailey.jpg',
                'jersey_number' => 31,
            ],
            [
                'name' => 'Moussa Diaby',
                'first_name' => 'Moussa',
                'last_name' => 'Diaby',
                'date_of_birth' => '1999-07-07',
                'nationality' => 'France',
                'position' => 'FWD',
                'height' => 170,
                'weight' => 68,
                'preferred_foot' => 'left',
                'overall_rating' => 81,
                'potential_rating' => 84,
                'value_eur' => 55000000,
                'wage_eur' => 120000,
                'contract_valid_until' => '2028-06-30',
                'fifa_connect_id' => 'AVL_DIABY_009',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_diaby.jpg',
                'jersey_number' => 19,
            ],
            [
                'name' => 'Ollie Watkins',
                'first_name' => 'Ollie',
                'last_name' => 'Watkins',
                'date_of_birth' => '1995-12-30',
                'nationality' => 'England',
                'position' => 'FWD',
                'height' => 180,
                'weight' => 70,
                'preferred_foot' => 'right',
                'overall_rating' => 82,
                'potential_rating' => 84,
                'value_eur' => 50000000,
                'wage_eur' => 110000,
                'contract_valid_until' => '2028-06-30',
                'fifa_connect_id' => 'AVL_WATKINS_010',
                'player_face_url' => 'https://media.api-sports.io/football/players/player_watkins.jpg',
                'jersey_number' => 11,
            ],
        ];

        foreach ($players as $playerData) {
            $this->createPlayer($club, $playerData);
        }
        
        $this->command->info("✅ Aston Villa: " . count($players) . " joueurs créés");
    }

    private function createPlayer($club, $playerData)
    {
        $player = Player::updateOrCreate(
            ['fifa_connect_id' => $playerData['fifa_connect_id']],
            array_merge($playerData, [
                'club_id' => $club->id,
                'association_id' => $club->association_id,
                'status' => 'active',
                'fifa_sync_status' => 'synced',
                'fifa_sync_date' => now(),
                
                'real_face' => true,
                'weak_foot' => 3,
                'skill_moves' => 3,
                'international_reputation' => 3,
                'work_rate' => 'High/High',
                'body_type' => 'Normal',
                
            ])
        );
        
        return $player;
    }

    // Méthodes pour les autres clubs (à continuer...)
    private function createBournemouthPlayers() { /* À implémenter */ }
    private function createBrentfordPlayers() { /* À implémenter */ }
    private function createBrightonPlayers() { /* À implémenter */ }
    private function createBurnleyPlayers() { /* À implémenter */ }
    private function createChelseaPlayers() { /* À implémenter */ }
    private function createCrystalPalacePlayers() { /* À implémenter */ }
    private function createEvertonPlayers() { /* À implémenter */ }
    private function createFulhamPlayers() { /* À implémenter */ }
    private function createLiverpoolPlayers() { /* À implémenter */ }
    private function createLutonTownPlayers() { /* À implémenter */ }
    private function createManchesterCityPlayers() { /* À implémenter */ }
    private function createManchesterUnitedPlayers() { /* À implémenter */ }
    private function createNewcastlePlayers() { /* À implémenter */ }
    private function createNottinghamForestPlayers() { /* À implémenter */ }
    private function createSheffieldUnitedPlayers() { /* À implémenter */ }
    private function createTottenhamPlayers() { /* À implémenter */ }
    private function createWestHamPlayers() { /* À implémenter */ }
    private function createWolvesPlayers() { /* À implémenter */ }
}
