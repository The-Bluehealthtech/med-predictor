<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatchPerformanceTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $created = 0;
        DB::table('player_season_stats')->select('player_id')->distinct()
            ->orderBy('player_id')->chunk(100, function ($players) use (&$created): void {
                $ids = $players->pluck('player_id')->all();
                $existing = DB::table('performances')
                    ->whereIn('player_id', $ids)
                    ->where('match_date', '2026-09-01')
                    ->pluck('player_id')->all();
                $missing = array_diff($ids, $existing);
                $rows = [];
                foreach ($missing as $id) {
                    $id = (int) $id;
                    $attempted = 28 + ($id % 19);
                    $completed = $attempted - (2 + ($id % 8));
                    $tackles = 1 + ($id % 5);
                    $shots = 1 + ($id % 4);
                    $rows[] = [
                        'player_id' => $id,
                        'match_date' => '2026-09-01',
                        'distance_covered' => 8100 + ($id % 2500),
                        'sprint_count' => 8 + ($id % 10),
                        'max_speed' => 24 + ($id % 8),
                        'avg_speed' => 7 + ($id % 3),
                        'passes_attempted' => $attempted,
                        'passes_completed' => $completed,
                        'tackles_attempted' => $tackles + 1,
                        'tackles_won' => $tackles,
                        'additional_metrics' => json_encode(['interceptions' => 1 + ($id % 4)], JSON_THROW_ON_ERROR),
                        'shots_total' => $shots + 1,
                        'shots_on_target' => $shots,
                        'goals_scored' => 0,
                        'assists' => 0,
                        'minutes_played' => 75 + ($id % 16),
                        'rating' => 6 + (($id % 24) / 10),
                        'notes' => 'synthetic_demo: match fictif pour tests du portail',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if ($rows) {
                    DB::table('performances')->insert($rows);
                    $created += count($rows);
                }
            });
        $this->command?->info("Matchs fictifs créés : {$created}.");
    }
}
