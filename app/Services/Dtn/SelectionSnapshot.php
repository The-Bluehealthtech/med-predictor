<?php

namespace App\Services\Dtn;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Données préparées automatiquement pour la DTN à partir de ce que le club a
 * déjà saisi (feuilles de match, statistiques, évaluations), et indice de
 * performance calculé au retour de sélection.
 *
 * Aucune donnée médicale n'est lue ici : la partie médicale est saisie à part
 * par les rôles médicaux.
 */
final class SelectionSnapshot
{
    public function forPlayer(int $playerId): array
    {
        $rows = DB::table('match_participations as mp')
            ->join('matches as m', 'm.id', '=', 'mp.match_id')
            ->leftJoin('player_match_detailed_stats as s', function ($join) {
                $join->on('s.match_id', '=', 'mp.match_id')->on('s.player_id', '=', 'mp.player_id');
            })
            ->where('mp.player_id', $playerId)
            ->orderByDesc('m.match_date')->orderByDesc('m.id')
            ->get(['mp.match_id', 'mp.team_id', 'mp.minute_in', 'mp.minute_out', 'mp.is_starter', 'm.match_date',
                'm.home_club_id', 'm.away_club_id', 'm.home_score', 'm.away_score', 's.match_rating']);

        $events = DB::table('match_events')->whereIn('match_id', $rows->pluck('match_id'))
            ->whereIn('type', ['goal', 'yellow_card', 'red_card'])
            ->where(fn ($q) => $q->where('player_id', $playerId)->orWhere('assisted_by_player_id', $playerId))
            ->get(['match_id', 'type', 'player_id', 'assisted_by_player_id']);
        $clubNames = DB::table('clubs')->whereIn('id', $rows->pluck('home_club_id')->merge($rows->pluck('away_club_id'))->unique())->pluck('name', 'id');
        $teamClub = DB::table('teams')->whereIn('id', $rows->pluck('team_id')->unique())->pluck('club_id', 'id');

        $matches = $rows->map(function ($r) use ($events, $clubNames, $teamClub, $playerId) {
            $own = (int) ($teamClub[$r->team_id] ?? $r->team_id);
            $home = (int) $r->home_club_id === $own;
            $ev = $events->where('match_id', $r->match_id);

            return [
                'date' => substr((string) $r->match_date, 0, 10),
                'opponent' => str_replace(' (Démo)', '', (string) ($clubNames[$home ? $r->away_club_id : $r->home_club_id] ?? '')),
                'score' => $home ? "{$r->home_score}–{$r->away_score}" : "{$r->away_score}–{$r->home_score}",
                'minutes' => max(0, (int) $r->minute_out - (int) $r->minute_in),
                'starter' => (bool) $r->is_starter,
                'rating' => $r->match_rating !== null ? round((float) $r->match_rating, 1) : null,
                'goals' => $ev->where('type', 'goal')->where('player_id', $playerId)->count(),
                'assists' => $ev->where('type', 'goal')->where('assisted_by_player_id', $playerId)->count(),
                'yellow' => $ev->where('type', 'yellow_card')->where('player_id', $playerId)->count(),
                'red' => $ev->where('type', 'red_card')->where('player_id', $playerId)->count(),
            ];
        })->values();

        $last5 = $matches->take(5);
        $rated = $matches->whereNotNull('rating');
        $yellowTotal = $matches->sum('yellow');

        return [
            'generated_at' => now()->toIso8601String(),
            'last_matches' => $last5->all(),
            'load' => [
                'minutes_last3' => $matches->take(3)->sum('minutes'),
                'load_last3_pct' => (int) round(100 * $matches->take(3)->sum('minutes') / 270),
                'matches_played' => $matches->count(),
            ],
            'form' => [
                'rating_last5' => $last5->whereNotNull('rating')->count() ? round($last5->whereNotNull('rating')->avg('rating'), 2) : null,
                'rating_season' => $rated->count() ? round($rated->avg('rating'), 2) : null,
            ],
            'season' => [
                'matches' => $matches->count(),
                'starts' => $matches->where('starter', true)->count(),
                'minutes' => $matches->sum('minutes'),
                'goals' => $matches->sum('goals'),
                'assists' => $matches->sum('assists'),
                'yellow' => $yellowTotal,
                'red' => $matches->sum('red'),
            ],
            'discipline' => [
                'one_yellow_from_suspension' => $yellowTotal > 0 && $yellowTotal % 5 === 4,
                'red_last_match' => (bool) ($matches->first()['red'] ?? false),
            ],
            'role_evaluation' => $this->roleEvaluation($playerId),
            'fit' => $this->fit($playerId),
        ];
    }

    private function roleEvaluation(int $playerId): ?array
    {
        $row = DB::table('player_role_evaluations')->where('player_id', $playerId)->whereNull('period_end')
            ->where('role_fit_score', 0)->orderByDesc('computed_at')->first(['position_family_evaluated', 'score', 'reliability']);

        return $row ? ['family' => $row->position_family_evaluated, 'score' => round((float) $row->score, 1),
            'reliability' => $row->reliability !== null ? (int) round(100 * (float) $row->reliability) : null] : null;
    }

    private function fit(int $playerId): ?array
    {
        if (!Schema::hasTable('fit_score_snapshots')) {
            return null;
        }
        $row = DB::table('fit_score_snapshots')->where('player_id', $playerId)->where('is_complete', true)
            ->whereNotNull('fit_score')->orderByDesc('snapshot_at')->first(['fit_score', 'snapshot_at']);

        return $row ? ['score' => round((float) $row->fit_score, 1), 'date' => substr((string) $row->snapshot_at, 0, 10)] : null;
    }

    /**
     * Indice de performance en sélection (0–100) :
     * 60 % note moyenne en sélection (× 10) + 40 % évaluation du staff national (× 10),
     * avec l'écart à la note moyenne du joueur en club sur la saison.
     */
    public function performanceIndex(array $return, ?array $departureSnapshot): array
    {
        $rating = isset($return['avg_rating']) && $return['avg_rating'] !== '' ? (float) $return['avg_rating'] : null;
        $staff = isset($return['staff_evaluation']) && $return['staff_evaluation'] !== '' ? (float) $return['staff_evaluation'] : null;
        $parts = array_filter([[0.6, $rating], [0.4, $staff]], fn ($p) => $p[1] !== null);
        $weight = array_sum(array_column($parts, 0));
        $index = $weight > 0 ? round(array_sum(array_map(fn ($p) => $p[0] * $p[1] * 10, $parts)) / $weight, 1) : null;
        $clubRating = $departureSnapshot['form']['rating_season'] ?? null;

        return [
            'index' => $index,
            'club_rating' => $clubRating,
            'rating_delta' => ($rating !== null && $clubRating !== null) ? round($rating - $clubRating, 2) : null,
        ];
    }
}
