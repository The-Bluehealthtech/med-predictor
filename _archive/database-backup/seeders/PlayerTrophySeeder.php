<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\PlayerTrophy;
use Carbon\Carbon;

class PlayerTrophySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding player trophies...');
        
        $players = Player::all();
        
        if ($players->isEmpty()) {
            $this->command->warn('No players found');
            return;
        }

        $this->command->info("Seeding trophies for {$players->count()} players...");

        foreach ($players as $player) {
            $this->seedPlayerTrophies($player);
        }

        $this->command->info('Player trophies seeded successfully!');
    }

    private function seedPlayerTrophies(Player $player)
    {
        $rating = $player->overall_rating ?? 75;
        $trophyCount = $this->getTrophyCount($rating);
        
        $individualTrophies = [
            'Ballon d\'Or' => ['type' => 'individual', 'competition' => 'FIFA', 'years' => [2023, 2022, 2021, 2020, 2019]],
            'Meilleur Buteur' => ['type' => 'individual', 'competition' => 'Ligue', 'years' => [2023, 2022, 2021, 2020, 2019]],
            'Meilleur Passeur' => ['type' => 'individual', 'competition' => 'Ligue', 'years' => [2023, 2022, 2021, 2020, 2019]],
            'Joueur de l\'Année' => ['type' => 'individual', 'competition' => 'Club', 'years' => [2023, 2022, 2021, 2020, 2019]],
            'Équipe de l\'Année' => ['type' => 'individual', 'competition' => 'FIFA', 'years' => [2023, 2022, 2021, 2020, 2019]]
        ];

        $teamTrophies = [
            'Champions League' => ['type' => 'team', 'competition' => 'UEFA', 'years' => [2023, 2022, 2021, 2020, 2019]],
            'Premier League' => ['type' => 'team', 'competition' => 'Angleterre', 'years' => [2023, 2022, 2021, 2020, 2019]],
            'FA Cup' => ['type' => 'team', 'competition' => 'Angleterre', 'years' => [2023, 2022, 2021, 2020, 2019]],
            'Carabao Cup' => ['type' => 'team', 'competition' => 'Angleterre', 'years' => [2023, 2022, 2021, 2020, 2019]],
            'Europa League' => ['type' => 'team', 'competition' => 'UEFA', 'years' => [2023, 2022, 2021, 2020, 2019]],
            'Super Cup' => ['type' => 'team', 'competition' => 'UEFA', 'years' => [2023, 2022, 2021, 2020, 2019]]
        ];

        $internationalTrophies = [
            'Coupe du Monde' => ['type' => 'international', 'competition' => 'FIFA', 'years' => [2022, 2018, 2014, 2010]],
            'Championnat d\'Europe' => ['type' => 'international', 'competition' => 'UEFA', 'years' => [2021, 2016, 2012, 2008]],
            'Ligue des Nations' => ['type' => 'international', 'competition' => 'UEFA', 'years' => [2023, 2021, 2019]]
        ];

        $allTrophies = array_merge($individualTrophies, $teamTrophies, $internationalTrophies);
        $trophyNames = array_keys($allTrophies);
        
        // Sélectionner aléatoirement les trophées
        $maxTrophies = min($trophyCount, count($trophyNames));
        if ($maxTrophies > 0) {
            $selectedTrophies = array_rand($trophyNames, $maxTrophies);
            if (!is_array($selectedTrophies)) {
                $selectedTrophies = [$selectedTrophies];
            }
        } else {
            $selectedTrophies = [];
        }

        foreach ($selectedTrophies as $index) {
            $trophyName = $trophyNames[$index];
            $trophyInfo = $allTrophies[$trophyName];
            $year = $trophyInfo['years'][array_rand($trophyInfo['years'])];
            
            PlayerTrophy::create([
                'player_id' => $player->id,
                'trophy_name' => $trophyName,
                'trophy_type' => $trophyInfo['type'],
                'competition' => $trophyInfo['competition'],
                'year' => $year,
                'club' => $player->club ? $player->club->name : 'Club inconnu',
                'country' => $player->nationality,
                'description' => "Gagné en {$year} avec " . ($player->club ? $player->club->name : 'le club')
            ]);
        }
    }

    private function getTrophyCount(int $rating): int
    {
        if ($rating >= 90) {
            return rand(8, 15); // Top players: 8-15 trophées
        } elseif ($rating >= 85) {
            return rand(5, 10); // Elite players: 5-10 trophées
        } elseif ($rating >= 80) {
            return rand(3, 7);  // Good players: 3-7 trophées
        } elseif ($rating >= 75) {
            return rand(1, 4);  // Average players: 1-4 trophées
        } else {
            return rand(0, 2);  // Lower players: 0-2 trophées
        }
    }
}
