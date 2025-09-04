<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use Illuminate\Support\Facades\DB;

class SimplePlayerStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding simple player performance statistics...');
        
        // Mettre à jour les joueurs existants avec des statistiques de base
        $this->updatePlayerStats();
        
        $this->command->info('Simple player performance statistics seeded successfully!');
    }

    private function updatePlayerStats()
    {
        $players = Player::all();
        
        if ($players->isEmpty()) {
            $this->command->warn('No players found');
            return;
        }

        $this->command->info("Updating stats for {$players->count()} players...");

        foreach ($players as $player) {
            // Générer des statistiques réalistes basées sur le rating FIFA
            $rating = $player->overall_rating ?? rand(70, 85);
            $ratingMultiplier = $rating / 100;

            // Mettre à jour le joueur avec des statistiques de base
            $player->update([
                'ghs_physical_score' => rand(70, 95),
                'ghs_mental_score' => rand(70, 95),
                'ghs_civic_score' => rand(70, 95),
                'ghs_sleep_score' => rand(70, 95),
                'ghs_overall_score' => rand(70, 95),
                'contribution_score' => rand(60, 100),
                'data_value_estimate' => rand(1000, 10000),
                'matches_contributed' => rand(15, 30),
                'training_sessions_logged' => rand(20, 50),
                'health_records_contributed' => rand(5, 15),
                'injury_risk_score' => rand(5, 25),
                'injury_risk_level' => ['low', 'medium', 'high'][rand(0, 2)],
                'match_availability' => rand(0, 1),
            ]);

            // Les statistiques sont déjà mises à jour dans le modèle Player
        }
    }


}
