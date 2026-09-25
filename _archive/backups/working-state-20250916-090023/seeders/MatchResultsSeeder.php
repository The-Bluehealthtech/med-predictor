<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MatchResultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = DB::table('players')->get();
        $clubs = DB::table('clubs')->get();
        
        $opponents = [
            'Real Madrid', 'Barcelona', 'Manchester City', 'Liverpool', 'Bayern Munich',
            'PSG', 'Juventus', 'AC Milan', 'Inter Milan', 'Arsenal', 'Chelsea',
            'Manchester United', 'Tottenham', 'Atletico Madrid', 'Sevilla'
        ];
        
        $competitions = [
            'Premier League', 'La Liga', 'Bundesliga', 'Serie A', 'Ligue 1',
            'Champions League', 'Europa League', 'FA Cup', 'Copa del Rey'
        ];
        
        foreach ($players as $player) {
            // Créer 10 matchs pour chaque joueur
            for ($i = 0; $i < 10; $i++) {
                $matchDate = Carbon::now()->subDays(rand(1, 90));
                $result = $this->getRealisticResult();
                
                DB::table('match_results')->insert([
                    'player_id' => $player->id,
                    'club_id' => $player->club_id ?? $clubs->random()->id,
                    'opponent' => $opponents[array_rand($opponents)],
                    'result' => $result,
                    'match_date' => $matchDate->format('Y-m-d'),
                    'competition' => $competitions[array_rand($competitions)],
                    'goals_scored' => $result === 'W' ? rand(0, 3) : rand(0, 1),
                    'assists' => $result === 'W' ? rand(0, 2) : rand(0, 1),
                    'rating' => $result === 'W' ? rand(70, 100) / 10 : rand(50, 79) / 10,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
    private function getRealisticResult(): string
    {
        // 60% de victoires, 25% de nuls, 15% de défaites (plus réaliste pour un bon joueur)
        $rand = rand(1, 100);
        if ($rand <= 60) return 'W';
        if ($rand <= 85) return 'D';
        return 'L';
    }
}
