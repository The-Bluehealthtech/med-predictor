<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\MatchPerformance;
use Carbon\Carbon;

class MatchPerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding match performances...');
        
        $players = Player::all();
        
        if ($players->isEmpty()) {
            $this->command->warn('No players found');
            return;
        }

        $this->command->info("Seeding match performances for {$players->count()} players...");

        foreach ($players as $player) {
            $this->seedMatchPerformances($player);
        }

        $this->command->info('Match performances seeded successfully!');
    }

    private function seedMatchPerformances(Player $player)
    {
        $opponents = ['Manchester City', 'Arsenal', 'Liverpool', 'Tottenham', 'Manchester United', 'Chelsea', 'West Ham', 'Aston Villa', 'Newcastle', 'Brighton'];
        $competitions = ['Premier League', 'Champions League', 'FA Cup', 'Carabao Cup', 'Europa League'];
        $venues = ['Home', 'Away', 'Neutral'];

        // Créer les 5 derniers matchs avec des dates récentes
        for ($i = 0; $i < 5; $i++) {
            $matchDate = now()->subDays(($i + 1) * 2); // Matchs espacés de 2 jours
            $result = $this->getRealisticResult($player);
            $opponent = $opponents[array_rand($opponents)];
            $competition = $competitions[array_rand($competitions)];
            $venue = $venues[array_rand($venues)];
            
            // Générer des statistiques réalistes basées sur le résultat
            $goals = $result === 'W' ? rand(0, 3) : ($result === 'D' ? rand(0, 1) : 0);
            $assists = $result === 'W' ? rand(0, 2) : rand(0, 1);
            $rating = $this->getRealisticRating($result, $goals, $assists);
            
            MatchPerformance::create([
                'player_id' => $player->id,
                'result' => $result,
                'match_date' => $matchDate,
                'opponent' => $opponent,
                'competition' => $competition,
                'venue' => $venue,
                'goals_scored' => $goals,
                'assists' => $assists,
                'rating' => $rating,
                'minutes_played' => rand(60, 90),
                'notes' => $this->getPerformanceNotes($result, $goals, $assists)
            ]);
        }
    }

    private function getRealisticResult(Player $player): string
    {
        // Les joueurs avec un meilleur rating ont plus de chances de gagner
        $rating = $player->overall_rating ?? 75;
        
        if ($rating >= 85) {
            // Top player: 60% W, 25% D, 15% L
            $rand = rand(1, 100);
            if ($rand <= 60) return 'W';
            if ($rand <= 85) return 'D';
            return 'L';
        } elseif ($rating >= 75) {
            // Good player: 45% W, 35% D, 20% L
            $rand = rand(1, 100);
            if ($rand <= 45) return 'W';
            if ($rand <= 80) return 'D';
            return 'L';
        } else {
            // Average player: 30% W, 40% D, 30% L
            $rand = rand(1, 100);
            if ($rand <= 30) return 'W';
            if ($rand <= 70) return 'D';
            return 'L';
        }
    }

    private function getRealisticRating(string $result, int $goals, int $assists): float
    {
        $baseRating = 6.0;
        
        // Bonus pour la victoire
        if ($result === 'W') $baseRating += 0.5;
        if ($result === 'L') $baseRating -= 0.5;
        
        // Bonus pour les buts
        $baseRating += $goals * 0.3;
        
        // Bonus pour les passes décisives
        $baseRating += $assists * 0.2;
        
        // Ajouter un peu de variation aléatoire
        $baseRating += (rand(-10, 10) / 100);
        
        // Limiter entre 4.0 et 10.0
        return max(4.0, min(10.0, round($baseRating, 1)));
    }

    private function getPerformanceNotes(string $result, int $goals, int $assists): string
    {
        if ($result === 'W') {
            if ($goals > 0 && $assists > 0) {
                return 'Excellent match avec buts et passes décisives';
            } elseif ($goals > 0) {
                return 'Bon match avec buts marqués';
            } elseif ($assists > 0) {
                return 'Bon match avec passes décisives';
            } else {
                return 'Match solide en défense';
            }
        } elseif ($result === 'D') {
            return 'Match équilibré, performance correcte';
        } else {
            return 'Match difficile, à améliorer';
        }
    }
}
