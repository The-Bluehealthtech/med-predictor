<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Adaptateur de lecture, sans accès nominatif ni requête à une base externe. */
final class PerformanceScoreDataSource
{
    public function all(): array
    {
        $players = DB::table('players')->select('id', 'position', 'fifa_connect_id')->get()->keyBy('id');
        $rows = [];
        foreach ($players as $player) {
            $rows[$player->id] = ['id' => $player->id, 'position' => $player->position, 'matches' => []];
        }
        if (Schema::hasTable('player_match_detailed_stats')) {
            // Le FK historique vise « joueurs » et non « players ». Seule une
            // correspondance FIFA ID vérifiée autorise le rattachement.
            $linked = DB::table('joueurs as j')->join('players as p', 'j.fifa_id', '=', 'p.fifa_connect_id')
                ->whereNotNull('j.fifa_id')->where('j.fifa_id', '<>', '')
                ->whereNotNull('p.fifa_connect_id')->select('j.id as legacy_id', 'p.id as portal_id')->get();
            $counts = $linked->groupBy('legacy_id');
            $reverse = $linked->groupBy('portal_id');
            $map = [];
            foreach ($linked as $item) {
                if ($counts[$item->legacy_id]->count() === 1 && $reverse[$item->portal_id]->count() === 1) {
                    $map[$item->legacy_id] = $item->portal_id;
                }
            }
            $matchRows = DB::table('player_match_detailed_stats as s')
                ->leftJoin('matches as m', 'm.id', '=', 's.match_id')
                ->select('s.*', 'm.match_date')->orderBy('m.match_date')->orderBy('s.match_id')->get();
            foreach ($matchRows as $row) {
                $portalId = $map[$row->player_id] ?? null;
                if ($portalId === null || !isset($rows[$portalId])) {
                    continue;
                }
                $stats = (array) $row;
                $additional = json_decode($row->additional_metrics ?? 'null', true);
                if (is_array($additional)) {
                    // Champs nommés exclusivement ; une valeur absente reste absente.
                    foreach (['saves', 'shots_on_target_against', 'post_shot_xg', 'goals_conceded',
                        'goals_prevented', 'high_claims', 'high_claims_attempted',
                        'sweeper_actions', 'sweeper_actions_attempted', 'mistakes_leading_to_goals',
                        'mistakes_leading_to_chances', 'penalties_conceded'] as $name) {
                        if (!array_key_exists($name, $stats) && array_key_exists($name, $additional)) {
                            $stats[$name] = $additional[$name];
                        }
                    }
                }
                $rows[$portalId]['matches'][] = [
                    'match_id' => $row->match_id,
                    'date' => (string) $row->match_date,
                    'position' => $row->position_played,
                    'minutes' => $row->minutes_played,
                    'stats' => $stats,
                ];
            }
        }
        if (Schema::hasTable('external_player_performance_metrics')) {
            $metrics = DB::table('external_player_performance_metrics')->get()->groupBy('player_id');
            $mapper = new KsaPlayerCockpitData();
            foreach ($metrics as $id => $group) {
                if (!isset($rows[$id]) || count($rows[$id]['matches'])) {
                    continue;
                }
                $data = $mapper->fromMetrics($group);
                $stats = array_merge($data['v'], $data['p']);
                foreach ($data['extra'] as $extra) {
                    if (is_numeric($extra['value']) && !array_key_exists($extra['name'], $stats)) {
                        $stats[$extra['name']] = $extra['value'];
                    }
                    if ($extra['name'] === 'Position') {
                        $rows[$id]['position'] = $extra['value'];
                    }
                }
                $rows[$id]['average'] = ['matches' => $data['matches'], 'minutes' => $data['minutes'], 'stats' => $stats];
            }
        }
        if (Schema::hasTable('player_season_stats')) {
            $columns = Schema::getColumnListing('player_season_stats');
            foreach (DB::table('player_season_stats')->get()->groupBy('player_id') as $id => $seasons) {
                if (!isset($rows[$id]) || count($rows[$id]['matches']) || isset($rows[$id]['average'])) {
                    continue;
                }
                $matches = $seasons->sum('matches_played');
                $minutes = $seasons->sum('minutes_played');
                $stats = [];
                foreach (['goals' => 'goals', 'assists' => 'assists', 'yellow_cards' => 'yellow_cards', 'red_cards' => 'red_cards'] as $name => $column) {
                    if (in_array($column, $columns, true) && $matches > 0) {
                        $stats[$name] = $seasons->sum($column) / $matches;
                    }
                }
                $rows[$id]['average'] = compact('matches', 'minutes', 'stats');
            }
        }

        return array_values($rows);
    }
}
