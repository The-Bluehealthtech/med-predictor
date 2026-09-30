<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Support\Facades\DB;

class GoalkeeperCockpitData
{
    public const COUNTS = [
        'shots_on_target_against', 'saves', 'goals_conceded', 'post_shot_xg',
        'goals_prevented', 'high_claims', 'punches', 'sweeper_actions',
        'passes', 'long_passes', 'forward_passes', 'goal_kicks',
        'errors_leading_to_shot', 'errors_leading_to_goal',
        'penalties_conceded', 'fouls_committed',
    ];

    public const RATES = [
        'saves_pct', 'claims_success', 'sweeper_success', 'passes_accuracy',
        'long_passes_accurate', 'forward_passes_accurate',
    ];

    public function forPlayer(Player $player, ?object $seasonStat = null): array
    {
        $data = [
            'minutes' => null,
            'matches' => null,
            'v' => array_fill_keys(self::COUNTS, null),
            'p' => array_fill_keys(self::RATES, null),
        ];
        // This is the same authorized player selected by the portal controller.
        $rows = DB::table('player_match_detailed_stats')
            ->where('player_id', $player->id)
            ->where('position_played', 'GK')
            ->get();
        if ($rows->isEmpty()) {
            // Use the same season row as the statistics card on this page.
            $matches = data_get($seasonStat, 'matches_played');
            $minutes = data_get($seasonStat, 'minutes_played');
            $data['matches'] = is_numeric($matches) && $matches >= 0 ? (int) $matches : null;
            $data['minutes'] = is_numeric($minutes) && $minutes >= 0 ? (int) $minutes : null;
            if ($data['matches'] > 0) {
                foreach (['saves' => 'saves', 'goals_conceded' => 'goals_conceded'] as $key => $column) {
                    $total = data_get($seasonStat, $column);
                    if (is_numeric($total) && $total >= 0) {
                        $data['v'][$key] = (float) $total / $data['matches'];
                    }
                }
            }
            return $data;
        }
        $data['matches'] = $rows->unique('match_id')->count();
        $data['minutes'] = $rows->sum('minutes_played');
        $matches = $data['matches'];
        if ($matches <= 0) {
            return $data;
        }
        $avg = static fn (string $field): float => $rows->sum($field) / $matches;
        $data['v']['passes'] = $avg('passes_total');
        $data['v']['long_passes'] = $avg('long_passes');
        $data['v']['fouls_committed'] = $avg('fouls_committed');
        $ratio = static function (string $num, string $den) use ($rows): ?float {
            $attempts = $rows->sum($den);
            $successful = $rows->sum($num);
            return $attempts > 0 && $successful >= 0 && $successful <= $attempts
                ? $successful / $attempts
                : null;
        };
        $data['p']['passes_accuracy'] = $ratio('passes_completed', 'passes_total');
        $data['p']['long_passes_accurate'] = $ratio('long_passes_completed', 'long_passes');
        // Team goals conceded are not interchangeable with goalkeeper goals conceded.
        // The other goalkeeper-specific fields are absent from this table.
        return $data;
    }
}
