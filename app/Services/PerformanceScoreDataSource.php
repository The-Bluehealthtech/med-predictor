<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Lecture des seules observations vérifiées et liées directement à players.id. */
final class PerformanceScoreDataSource
{
    public function all(): array
    {
        $rows = [];
        foreach (DB::table('players')->select('id', 'position')->get() as $player) {
            $rows[$player->id] = ['id' => $player->id, 'position' => $player->position, 'matches' => []];
        }

        if ($this->hasObservedSource('performances')) {
            foreach (DB::table('performances')->where('score_origin', 'observed')
                ->orderBy('match_date')->orderBy('id')->get() as $record) {
                if (!isset($rows[$record->player_id])) continue;
                $extra = json_decode($record->additional_metrics ?? 'null', true);
                $stats = [
                    'goals_scored' => $record->goals_scored,
                    'assists_provided' => $record->assists,
                    'shots_on_target' => $record->shots_on_target,
                    'passes_total' => $record->passes_attempted,
                    'passes_completed' => $record->passes_completed,
                    'tackles_total' => $record->tackles_attempted,
                    'tackles_won' => $record->tackles_won,
                    'yellow_cards' => $record->yellow_cards,
                    'red_cards' => $record->red_cards,
                ];
                // Uniquement les clés de mesure autorisées. Aucun identifiant ni nom
                // provenant du JSON ne peut modifier l'identité de la ligne.
                if (is_array($extra)) {
                    foreach (['key_passes', 'long_passes', 'long_passes_completed', 'crosses_total',
                        'crosses_completed', 'dribbles_attempted', 'dribbles_completed',
                        'interceptions', 'recoveries', 'ground_duels_total', 'ground_duels_won',
                        'aerial_duels_total', 'aerial_duels_won', 'fouls_committed',
                        'times_dispossessed', 'mistakes_leading_to_chances', 'mistakes_leading_to_goals',
                        'penalties_conceded', 'saves', 'shots_on_target_against',
                        'post_shot_xg', 'goals_conceded', 'goals_prevented',
                        'high_claims', 'high_claims_attempted', 'sweeper_actions',
                        'sweeper_actions_attempted'] as $field) {
                        if (array_key_exists($field, $extra) && !array_key_exists($field, $stats)) {
                            $stats[$field] = $extra[$field];
                        }
                    }
                }
                $rows[$record->player_id]['matches'][] = [
                    'match_id' => $record->id,
                    'date' => (string) $record->match_date,
                    'position' => $record->position_played ?: $rows[$record->player_id]['position'],
                    'minutes' => $record->minutes_played,
                    'stats' => $stats,
                ];
            }
        }

        if ($this->hasObservedSource('external_player_performance_metrics')) {
            $metrics = DB::table('external_player_performance_metrics')->where('score_origin', 'observed')
                ->get()->groupBy('player_id');
            $mapper = new KsaPlayerCockpitData();
            foreach ($metrics as $id => $group) {
                if (!isset($rows[$id]) || $rows[$id]['matches']) continue;
                $data = $mapper->fromMetrics($group);
                $stats = array_merge($data['v'], $data['p']);
                foreach ($data['extra'] as $extra) {
                    if ($extra['name'] === 'Position') {
                        $rows[$id]['position'] = $extra['value'];
                    } elseif (is_numeric($extra['value']) && !array_key_exists($extra['name'], $stats)) {
                        $stats[$extra['name']] = $extra['value'];
                    }
                }
                $rows[$id]['average'] = ['matches' => $data['matches'], 'minutes' => $data['minutes'], 'stats' => $stats];
            }
        }

        if ($this->hasObservedSource('player_season_stats')) {
            $columns = Schema::getColumnListing('player_season_stats');
            foreach (DB::table('player_season_stats')->where('score_origin', 'observed')
                ->get()->groupBy('player_id') as $id => $seasons) {
                if (!isset($rows[$id]) || $rows[$id]['matches'] || isset($rows[$id]['average'])) continue;
                $matches = $seasons->sum('matches_played');
                $minutes = $seasons->sum('minutes_played');
                $stats = [];
                foreach (['goals', 'assists', 'yellow_cards', 'red_cards'] as $name) {
                    if (in_array($name, $columns, true) && $matches > 0) {
                        $stats[$name] = $seasons->sum($name) / $matches;
                    }
                }
                $rows[$id]['average'] = compact('matches', 'minutes', 'stats');
            }
        }

        return array_values($rows);
    }

    private function hasObservedSource(string $table): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, 'score_origin');
    }
}
