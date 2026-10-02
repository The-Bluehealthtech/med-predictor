<?php

namespace App\Services\Analytics;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Suivi individuel des joueurs d'un club à partir des feuilles de match
 * (participations, notes de match, événements) : forme, temps de jeu,
 * charge, efficacité, et alertes explicables. Aucune donnée médicale.
 */
final class PlayerFormAnalytics
{
    /** Écart de note (moyenne des 3 derniers matchs vs moyenne de saison) qui déclenche un signal. */
    public const FORM_DELTA = 0.5;
    /** Matchs notés minimum dans la saison pour juger une tendance de forme. */
    public const FORM_MIN_MATCHES = 5;
    /** Charge sur les 3 derniers matchs du club au-delà de laquelle on signale une charge élevée (% de 270 min). */
    public const HIGH_LOAD_PCT = 95;
    /** … et seulement si ces 3 matchs se sont joués en peu de jours (calendrier serré). */
    public const CONGESTED_DAYS = 10;
    public const WINDOWS = ['5' => '5 derniers matchs', '10' => '10 derniers matchs', 'season' => 'Saison'];
    public const POSITIONS = ['GK' => 'Gardiens', 'DEF' => 'Défenseurs', 'MID' => 'Milieux', 'FWD' => 'Attaquants'];

    /**
     * @param string $window '5', '10' ou 'season'
     * @return array{club_matches:int, window_matches:int, record:array, team_trend:array, players:array, alerts:array, leaders:array}|null
     */
    public function forClub(int $clubId, string $window = '10', ?string $position = null, int $minMatches = 3): ?array
    {
        // Matchs joués du club (avec feuille de match), du plus ancien au plus récent.
        $teamIds = DB::table('teams')->where('club_id', $clubId)->pluck('id');
        $matches = DB::table('matches as m')
            ->where(fn ($q) => $q->where('m.home_club_id', $clubId)->orWhere('m.away_club_id', $clubId))
            ->whereNotNull('m.home_score')
            ->whereExists(fn ($q) => $q->from('match_participations as mp')->whereColumn('mp.match_id', 'm.id')->whereIn('mp.team_id', $teamIds))
            ->orderBy('m.match_date')->orderBy('m.id')
            ->get(['m.id', 'm.match_date', 'm.home_club_id', 'm.away_club_id', 'm.home_score', 'm.away_score']);
        if ($matches->isEmpty()) {
            return null;
        }

        $windowMatches = $window === 'season' ? $matches : $matches->slice(-((int) $window))->values();
        $windowIds = $windowMatches->pluck('id')->all();
        $last3 = $matches->slice(-3)->values();
        $last3Ids = $last3->pluck('id')->all();
        $last3SpanDays = $last3->count() === 3
            ? (int) round((strtotime((string) $last3->last()->match_date) - strtotime((string) $last3->first()->match_date)) / 86400)
            : null;

        $rows = DB::table('match_participations as mp')
            ->leftJoin('player_match_detailed_stats as s', fn ($j) => $j->on('s.match_id', '=', 'mp.match_id')->on('s.player_id', '=', 'mp.player_id'))
            ->whereIn('mp.match_id', $matches->pluck('id'))
            ->whereIn('mp.team_id', $teamIds)
            ->get(['mp.match_id', 'mp.player_id', 'mp.minute_in', 'mp.minute_out', 'mp.is_starter', 's.match_rating']);
        $events = DB::table('match_events')->whereIn('match_id', $matches->pluck('id'))
            ->whereIn('type', ['goal', 'yellow_card', 'red_card'])
            ->get(['match_id', 'type', 'player_id', 'assisted_by_player_id']);

        $order = $matches->pluck('id')->flip();
        $players = DB::table('players')->whereIn('id', $rows->pluck('player_id')->unique())
            ->get(['id', 'first_name', 'last_name', 'position'])->keyBy('id');

        $perPlayer = $rows->groupBy('player_id')->map(function (Collection $apps, $playerId) use ($order, $windowIds, $last3Ids, $last3SpanDays, $events, $players) {
            $apps = $apps->sortBy(fn ($r) => $order[$r->match_id])->values()->map(function ($r) use ($events, $playerId) {
                $ev = $events->where('match_id', $r->match_id);

                return [
                    'match_id' => $r->match_id,
                    'minutes' => max(0, (int) $r->minute_out - (int) $r->minute_in),
                    'starter' => (bool) $r->is_starter,
                    'rating' => $r->match_rating !== null ? (float) $r->match_rating : null,
                    'goals' => $ev->where('type', 'goal')->where('player_id', $playerId)->count(),
                    'assists' => $ev->where('type', 'goal')->where('assisted_by_player_id', $playerId)->count(),
                    'yellow' => $ev->where('type', 'yellow_card')->where('player_id', $playerId)->count(),
                    'red' => $ev->where('type', 'red_card')->where('player_id', $playerId)->count(),
                ];
            });
            $inWindow = $apps->whereIn('match_id', $windowIds);
            $seasonRated = $apps->whereNotNull('rating');
            $last3Rated = $seasonRated->slice(-3);
            $minutes = $inWindow->sum('minutes');
            $seasonAvg = $seasonRated->count() ? $seasonRated->avg('rating') : null;
            $last3Avg = $last3Rated->count() === 3 ? $last3Rated->avg('rating') : null;
            $p = $players[$playerId] ?? null;
            $yellowSeason = $apps->sum('yellow');

            return [
                'id' => (int) $playerId,
                'name' => trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? '')) ?: 'Joueur #' . $playerId,
                'position' => $p->position ?? null,
                'matches' => $inWindow->count(),
                'starts' => $inWindow->where('starter', true)->count(),
                'minutes' => $minutes,
                'rating' => $inWindow->whereNotNull('rating')->count() ? round($inWindow->whereNotNull('rating')->avg('rating'), 2) : null,
                'goals' => $inWindow->sum('goals'),
                'assists' => $inWindow->sum('assists'),
                'ga_per90' => $minutes >= 90 ? round(90 * ($inWindow->sum('goals') + $inWindow->sum('assists')) / $minutes, 2) : null,
                'yellow' => $inWindow->sum('yellow'),
                'red' => $inWindow->sum('red'),
                'season_matches' => $apps->count(),
                'season_rated' => $seasonRated->count(),
                'season_rating' => $seasonAvg !== null ? round($seasonAvg, 2) : null,
                'last3_rating' => $last3Avg !== null ? round($last3Avg, 2) : null,
                'form_delta' => ($seasonAvg !== null && $last3Avg !== null) ? round($last3Avg - $seasonAvg, 2) : null,
                'load_last3_pct' => (int) round(100 * $apps->whereIn('match_id', $last3Ids)->sum('minutes') / 270),
                'minutes_last3' => $apps->whereIn('match_id', $last3Ids)->sum('minutes'),
                'last3_span_days' => $last3SpanDays,
                'yellow_season' => $yellowSeason,
                'one_yellow_from_suspension' => $yellowSeason > 0 && $yellowSeason % 5 === 4,
            ];
        })->values();

        $seasonCount = $matches->count();
        $alerts = $perPlayer->flatMap(fn ($p) => $this->alertsFor($p, $seasonCount))->sortBy('priority')->values();

        $visible = $perPlayer
            ->filter(fn ($p) => $position === null || $p['position'] === $position)
            ->filter(fn ($p) => $p['matches'] > 0)
            ->sortByDesc('minutes')->values();
        $qualified = $visible->filter(fn ($p) => $p['matches'] >= $minMatches);

        return [
            'club_matches' => $seasonCount,
            'window_matches' => $windowMatches->count(),
            'record' => $this->record($windowMatches, $clubId),
            'team_trend' => $this->teamTrend($matches, $rows, $clubId),
            'players' => $visible->all(),
            'alerts' => $alerts->filter(fn ($a) => $position === null || $a['position'] === $position)->values()->all(),
            'leaders' => [
                'rating' => $qualified->whereNotNull('rating')->sortByDesc('rating')->take(5)->values()->all(),
                'ga_per90' => $qualified->filter(fn ($p) => $p['minutes'] >= 270 && $p['ga_per90'] !== null)->sortByDesc('ga_per90')->take(5)->values()->all(),
            ],
        ];
    }

    /** Alertes explicables, chacune avec sa raison chiffrée. */
    private function alertsFor(array $p, int $seasonMatches): array
    {
        $alerts = [];
        $base = ['player_id' => $p['id'], 'player' => $p['name'], 'position' => $p['position']];
        if ($p['form_delta'] !== null && $p['season_rated'] >= self::FORM_MIN_MATCHES) {
            if ($p['form_delta'] <= -self::FORM_DELTA) {
                $alerts[] = $base + ['type' => 'form_drop', 'level' => 'warning', 'priority' => 2, 'label' => 'Baisse de forme',
                    'reason' => sprintf('Note moyenne %.2f sur les 3 derniers matchs, contre %.2f sur la saison (%+.2f).', $p['last3_rating'], $p['season_rating'], $p['form_delta'])];
            } elseif ($p['form_delta'] >= self::FORM_DELTA) {
                $alerts[] = $base + ['type' => 'form_rise', 'level' => 'positive', 'priority' => 4, 'label' => 'En progression',
                    'reason' => sprintf('Note moyenne %.2f sur les 3 derniers matchs, contre %.2f sur la saison (%+.2f).', $p['last3_rating'], $p['season_rating'], $p['form_delta'])];
            }
        }
        if ($p['load_last3_pct'] >= self::HIGH_LOAD_PCT && $p['last3_span_days'] !== null && $p['last3_span_days'] <= self::CONGESTED_DAYS) {
            $alerts[] = $base + ['type' => 'high_load', 'level' => 'warning', 'priority' => 1, 'label' => 'Charge élevée',
                'reason' => sprintf('%d minutes (%d %% du temps possible) sur les 3 derniers matchs, joués en %d jours.', $p['minutes_last3'], $p['load_last3_pct'], $p['last3_span_days'])];
        }
        if ($seasonMatches >= 6 && $p['season_matches'] >= 0.6 * $seasonMatches && $p['minutes_last3'] === 0) {
            $alerts[] = $base + ['type' => 'minutes_drop', 'level' => 'info', 'priority' => 3, 'label' => 'Temps de jeu en chute',
                'reason' => sprintf('Joueur régulier (%d matchs joués sur %d) mais aucune minute sur les 3 derniers matchs du club.', $p['season_matches'], $seasonMatches)];
        }
        if ($p['one_yellow_from_suspension']) {
            $alerts[] = $base + ['type' => 'suspension_risk', 'level' => 'info', 'priority' => 3, 'label' => 'Suspension proche',
                'reason' => sprintf('%d cartons jaunes cette saison : le prochain entraîne une suspension.', $p['yellow_season'])];
        }

        return $alerts;
    }

    private function record(Collection $matches, int $clubId): array
    {
        $r = ['won' => 0, 'drawn' => 0, 'lost' => 0, 'for' => 0, 'against' => 0];
        foreach ($matches as $m) {
            $home = (int) $m->home_club_id === $clubId;
            [$for, $against] = $home ? [(int) $m->home_score, (int) $m->away_score] : [(int) $m->away_score, (int) $m->home_score];
            $r['for'] += $for;
            $r['against'] += $against;
            $r[$for > $against ? 'won' : ($for === $against ? 'drawn' : 'lost')]++;
        }

        return $r;
    }

    /** Note moyenne de l'équipe à chaque match (moyenne des notes des joueurs ayant joué). */
    private function teamTrend(Collection $matches, Collection $rows, int $clubId): array
    {
        $byMatch = $rows->whereNotNull('match_rating')->groupBy('match_id');
        $labels = [];
        $values = [];
        $results = [];
        foreach ($matches as $m) {
            if (!isset($byMatch[$m->id])) {
                continue;
            }
            $home = (int) $m->home_club_id === $clubId;
            [$for, $against] = $home ? [(int) $m->home_score, (int) $m->away_score] : [(int) $m->away_score, (int) $m->home_score];
            $labels[] = substr((string) $m->match_date, 0, 10);
            $values[] = round($byMatch[$m->id]->avg('match_rating'), 2);
            $results[] = $for > $against ? 'V' : ($for === $against ? 'N' : 'D');
        }

        return ['labels' => $labels, 'values' => $values, 'results' => $results];
    }
}
