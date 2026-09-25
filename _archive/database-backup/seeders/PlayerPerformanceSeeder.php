<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlayerPerformance;
use App\Models\Player;
use App\Models\Season;
use App\Models\Competition;

class PlayerPerformanceSeeder extends Seeder
{
    public function run(): void
    {
        // Créer une saison et une compétition si elles n'existent pas
        $season = Season::firstOrCreate(['id' => 1], [
            'name' => 'Saison 2024-2025',
            'start_date' => '2024-08-01',
            'end_date' => '2025-05-31',
            'is_active' => true
        ]);

        $competition = Competition::firstOrCreate(['id' => 1], [
            'name' => 'Ligue 1 Tunisienne',
            'country' => 'Tunisie',
            'level' => 'national',
            'type' => 'league'
        ]);

        // Récupérer tous les joueurs
        $players = Player::limit(20)->get();

        foreach ($players as $player) {
            // Créer des performances réalistes pour chaque joueur
            $this->createPlayerPerformances($player, $season, $competition);
        }
    }

    private function createPlayerPerformances($player, $season, $competition)
    {
        // Nombre de matchs joués (entre 15 et 30)
        $matchesPlayed = rand(15, 30);
        
        // Statistiques de base
        $goalsScored = rand(0, 25);
        $assists = rand(0, 20);
        $shotsTotal = rand(20, 80);
        $shotsOnTarget = rand(10, min($shotsTotal, 50));
        $passesTotal = rand(400, 1200);
        $passesCompleted = rand(300, min($passesTotal, 1000));
        $keyPasses = rand(5, 40);
        
        // Statistiques défensives
        $tacklesTotal = rand(30, 100);
        $tacklesWon = rand(20, min($tacklesTotal, 80));
        $interceptions = rand(15, 60);
        $clearances = rand(10, 50);
        $duelsTotal = rand(100, 300);
        $duelsWon = rand(60, min($duelsTotal, 250));
        
        // Statistiques physiques
        $distanceCovered = rand(180, 320) / 10; // 18.0 à 32.0 km
        $maxSpeed = rand(28, 36); // 28.0 à 36.0 km/h
        $sprintsCount = rand(20, 80);
        
        // Ratings FIFA-style
        $overallRating = rand(65, 88);
        $attackingRating = rand(60, 90);
        $defendingRating = rand(60, 90);
        $physicalRating = rand(60, 90);
        $technicalRating = rand(60, 90);
        $mentalRating = rand(60, 90);
        
        // Créer l'enregistrement de performance
        PlayerPerformance::create([
            'player_id' => $player->id,
            'season_id' => $season->id,
            'competition_id' => $competition->id,
            'matches_played' => $matchesPlayed,
            'minutes_played' => $matchesPlayed * rand(70, 90),
            'goals_scored' => $goalsScored,
            'assists' => $assists,
            'shots_on_target' => $shotsOnTarget,
            'shots_total' => $shotsTotal,
            'passes_completed' => $passesCompleted,
            'passes_total' => $passesTotal,
            'key_passes' => $keyPasses,
            'crosses_completed' => rand(5, 30),
            'crosses_total' => rand(15, 60),
            'tackles_won' => $tacklesWon,
            'tackles_total' => $tacklesTotal,
            'interceptions' => $interceptions,
            'clearances' => $clearances,
            'blocks' => rand(5, 25),
            'duels_won' => $duelsWon,
            'duels_total' => $duelsTotal,
            'fouls_committed' => rand(5, 25),
            'fouls_drawn' => rand(5, 20),
            'yellow_cards' => rand(0, 8),
            'red_cards' => rand(0, 2),
            'distance_covered' => $distanceCovered,
            'sprint_distance' => rand(20, 60) / 10,
            'max_speed' => $maxSpeed,
            'sprints_count' => $sprintsCount,
            'avg_speed' => rand(8, 12),
            'expected_goals' => rand(0, 30) / 10,
            'expected_assists' => rand(0, 25) / 10,
            'pass_accuracy' => $passesTotal > 0 ? round(($passesCompleted / $passesTotal) * 100, 1) : 0,
            'shot_accuracy' => $shotsTotal > 0 ? round(($shotsOnTarget / $shotsTotal) * 100, 1) : 0,
            'tackle_success_rate' => $tacklesTotal > 0 ? round(($tacklesWon / $tacklesTotal) * 100, 1) : 0,
            'duel_success_rate' => $duelsTotal > 0 ? round(($duelsWon / $duelsTotal) * 100, 1) : 0,
            'overall_rating' => $overallRating,
            'attacking_rating' => $attackingRating,
            'defending_rating' => $defendingRating,
            'physical_rating' => $physicalRating,
            'technical_rating' => $technicalRating,
            'mental_rating' => $mentalRating,
        ]);
    }
}

