<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\Player;

class TestPlayersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Chelsea FC
        $chelsea = Club::where('name', 'Chelsea FC')->first();
        if (!$chelsea) {
            $this->command->error('Chelsea FC not found in database');
            return;
        }

        // Chelsea Players
        $chelseaPlayers = [
            ['name' => 'Cole Palmer', 'position' => 'Midfielder', 'age' => 22, 'overall_rating' => 82],
            ['name' => 'Enzo Fernández', 'position' => 'Midfielder', 'age' => 23, 'overall_rating' => 84],
            ['name' => 'Moises Caicedo', 'position' => 'Midfielder', 'age' => 22, 'overall_rating' => 83],
            ['name' => 'Raheem Sterling', 'position' => 'Forward', 'age' => 29, 'overall_rating' => 85],
            ['name' => 'Nicolas Jackson', 'position' => 'Forward', 'age' => 23, 'overall_rating' => 78],
            ['name' => 'Mykhailo Mudryk', 'position' => 'Forward', 'age' => 23, 'overall_rating' => 76],
            ['name' => 'Levi Colwill', 'position' => 'Defender', 'age' => 21, 'overall_rating' => 79],
            ['name' => 'Axel Disasi', 'position' => 'Defender', 'age' => 25, 'overall_rating' => 80],
            ['name' => 'Malo Gusto', 'position' => 'Defender', 'age' => 20, 'overall_rating' => 77],
            ['name' => 'Robert Sánchez', 'position' => 'Goalkeeper', 'age' => 26, 'overall_rating' => 81],
            ['name' => 'Djordje Petrovic', 'position' => 'Goalkeeper', 'age' => 24, 'overall_rating' => 75],
            ['name' => 'Conor Gallagher', 'position' => 'Midfielder', 'age' => 24, 'overall_rating' => 80],
            ['name' => 'Carney Chukwuemeka', 'position' => 'Midfielder', 'age' => 20, 'overall_rating' => 74],
            ['name' => 'Nonso Madueke', 'position' => 'Forward', 'age' => 22, 'overall_rating' => 75],
            ['name' => 'Alfie Gilchrist', 'position' => 'Defender', 'age' => 20, 'overall_rating' => 68],
        ];

        foreach ($chelseaPlayers as $playerData) {
            Player::create([
                'name' => $playerData['name'],
                'first_name' => explode(' ', $playerData['name'])[0],
                'last_name' => explode(' ', $playerData['name'])[1] ?? '',
                'position' => $playerData['position'],
                'age' => $playerData['age'],
                'overall_rating' => $playerData['overall_rating'],
                'club_id' => $chelsea->id,
                'fifa_connect_id' => 'TEST_' . rand(100000, 999999),
                'nationality' => 'English',
                'height' => rand(170, 190),
                'weight' => rand(70, 85),
                'preferred_foot' => rand(0, 1) ? 'Right' : 'Left',
                'weak_foot' => rand(2, 4),
                'skill_moves' => rand(2, 5),
                'international_reputation' => rand(1, 3),
                'work_rate' => 'Medium/Medium',
                'body_type' => 'Normal',
                'real_face' => false,
                'value_eur' => rand(1000000, 50000000),
                'wage_eur' => rand(50000, 200000),
                'contract_valid_until' => now()->addYears(rand(1, 5)),
                'fifa_version' => '24',
                'last_updated' => now(),
            ]);
        }

        // Get Arsenal FC
        $arsenal = Club::where('name', 'Arsenal FC')->first();
        if ($arsenal) {
            $arsenalPlayers = [
                ['name' => 'Bukayo Saka', 'position' => 'Forward', 'age' => 22, 'overall_rating' => 86],
                ['name' => 'Martin Ødegaard', 'position' => 'Midfielder', 'age' => 25, 'overall_rating' => 87],
                ['name' => 'Declan Rice', 'position' => 'Midfielder', 'age' => 25, 'overall_rating' => 85],
                ['name' => 'William Saliba', 'position' => 'Defender', 'age' => 23, 'overall_rating' => 84],
                ['name' => 'Gabriel Martinelli', 'position' => 'Forward', 'age' => 23, 'overall_rating' => 83],
                ['name' => 'Kai Havertz', 'position' => 'Forward', 'age' => 24, 'overall_rating' => 82],
                ['name' => 'David Raya', 'position' => 'Goalkeeper', 'age' => 28, 'overall_rating' => 83],
                ['name' => 'Ben White', 'position' => 'Defender', 'age' => 26, 'overall_rating' => 82],
                ['name' => 'Gabriel Magalhães', 'position' => 'Defender', 'age' => 26, 'overall_rating' => 83],
                ['name' => 'Oleksandr Zinchenko', 'position' => 'Defender', 'age' => 27, 'overall_rating' => 80],
                ['name' => 'Thomas Partey', 'position' => 'Midfielder', 'age' => 30, 'overall_rating' => 84],
                ['name' => 'Jorginho', 'position' => 'Midfielder', 'age' => 32, 'overall_rating' => 82],
                ['name' => 'Emile Smith Rowe', 'position' => 'Midfielder', 'age' => 23, 'overall_rating' => 78],
                ['name' => 'Eddie Nketiah', 'position' => 'Forward', 'age' => 24, 'overall_rating' => 76],
                ['name' => 'Reiss Nelson', 'position' => 'Forward', 'age' => 24, 'overall_rating' => 75],
            ];

            foreach ($arsenalPlayers as $playerData) {
                Player::create([
                    'name' => $playerData['name'],
                    'first_name' => explode(' ', $playerData['name'])[0],
                    'last_name' => explode(' ', $playerData['name'])[1] ?? '',
                    'position' => $playerData['position'],
                    'age' => $playerData['age'],
                    'overall_rating' => $playerData['overall_rating'],
                    'club_id' => $arsenal->id,
                    'fifa_connect_id' => 'TEST_' . rand(100000, 999999),
                    'nationality' => 'English',
                    'height' => rand(170, 190),
                    'weight' => rand(70, 85),
                    'preferred_foot' => rand(0, 1) ? 'Right' : 'Left',
                    'weak_foot' => rand(2, 4),
                    'skill_moves' => rand(2, 5),
                    'international_reputation' => rand(1, 3),
                    'work_rate' => 'Medium/Medium',
                    'body_type' => 'Normal',
                    'real_face' => false,
                    'value_eur' => rand(1000000, 50000000),
                    'wage_eur' => rand(50000, 200000),
                    'contract_valid_until' => now()->addYears(rand(1, 5)),
                    'fifa_version' => '24',
                    'last_updated' => now(),
                ]);
            }
        }

        // Get Manchester City
        $manCity = Club::where('name', 'Manchester City')->first();
        if ($manCity) {
            $manCityPlayers = [
                ['name' => 'Erling Haaland', 'position' => 'Forward', 'age' => 23, 'overall_rating' => 91],
                ['name' => 'Kevin De Bruyne', 'position' => 'Midfielder', 'age' => 32, 'overall_rating' => 91],
                ['name' => 'Phil Foden', 'position' => 'Midfielder', 'age' => 24, 'overall_rating' => 85],
                ['name' => 'Rodri', 'position' => 'Midfielder', 'age' => 27, 'overall_rating' => 87],
                ['name' => 'Bernardo Silva', 'position' => 'Midfielder', 'age' => 29, 'overall_rating' => 86],
                ['name' => 'Jack Grealish', 'position' => 'Forward', 'age' => 28, 'overall_rating' => 83],
                ['name' => 'Ederson', 'position' => 'Goalkeeper', 'age' => 30, 'overall_rating' => 88],
                ['name' => 'Rúben Dias', 'position' => 'Defender', 'age' => 26, 'overall_rating' => 87],
                ['name' => 'John Stones', 'position' => 'Defender', 'age' => 29, 'overall_rating' => 84],
                ['name' => 'Kyle Walker', 'position' => 'Defender', 'age' => 33, 'overall_rating' => 84],
                ['name' => 'Nathan Aké', 'position' => 'Defender', 'age' => 29, 'overall_rating' => 82],
                ['name' => 'Julián Álvarez', 'position' => 'Forward', 'age' => 24, 'overall_rating' => 82],
                ['name' => 'Jeremy Doku', 'position' => 'Forward', 'age' => 21, 'overall_rating' => 79],
                ['name' => 'Oscar Bobb', 'position' => 'Forward', 'age' => 20, 'overall_rating' => 72],
                ['name' => 'Rico Lewis', 'position' => 'Defender', 'age' => 19, 'overall_rating' => 75],
            ];

            foreach ($manCityPlayers as $playerData) {
                Player::create([
                    'name' => $playerData['name'],
                    'first_name' => explode(' ', $playerData['name'])[0],
                    'last_name' => explode(' ', $playerData['name'])[1] ?? '',
                    'position' => $playerData['position'],
                    'age' => $playerData['age'],
                    'overall_rating' => $playerData['overall_rating'],
                    'club_id' => $manCity->id,
                    'fifa_connect_id' => 'TEST_' . rand(100000, 999999),
                    'nationality' => 'English',
                    'height' => rand(170, 190),
                    'weight' => rand(70, 85),
                    'preferred_foot' => rand(0, 1) ? 'Right' : 'Left',
                    'weak_foot' => rand(2, 4),
                    'skill_moves' => rand(2, 5),
                    'international_reputation' => rand(1, 3),
                    'work_rate' => 'Medium/Medium',
                    'body_type' => 'Normal',
                    'real_face' => false,
                    'value_eur' => rand(1000000, 50000000),
                    'wage_eur' => rand(50000, 200000),
                    'contract_valid_until' => now()->addYears(rand(1, 5)),
                    'fifa_version' => '24',
                    'last_updated' => now(),
                ]);
            }
        }

        $this->command->info('Test players created successfully!');
        $this->command->info('Chelsea FC: ' . $chelsea->players()->count() . ' players');
        if ($arsenal) {
            $this->command->info('Arsenal FC: ' . $arsenal->players()->count() . ' players');
        }
        if ($manCity) {
            $this->command->info('Manchester City: ' . $manCity->players()->count() . ' players');
        }
    }
} 