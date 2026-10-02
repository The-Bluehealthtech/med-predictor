<?php

namespace App\Services\CoachCockpit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Données du cockpit entraîneur pour un club : bilan, classement, matchs,
 * effectif, indicateurs de jeu et entrées du modèle de sélection.
 *
 * Périmètre : la compétition dans laquelle le club a joué le plus de matchs
 * (et non un marquage démo), pour que l'écran serve aussi sur des données
 * réelles. Toutes les agrégations sont faites en PHP sur des requêtes
 * simples, compatibles PostgreSQL et SQLite.
 */
final class CoachCockpitData
{
    public function __construct(private readonly SelectionModel $model)
    {
    }

    /**
     * Clubs proposés dans le sélecteur : tous les clubs de la base, avec
     * l'indication de ceux qui ont des matchs joués avec feuille de match
     * (cockpit complet) ; les autres s'ouvrent sur leur fiche club.
     *
     * @return Collection<int, object{id:int, name:string, association:?string, has_matches:bool}>
     */
    public function availableClubs(): Collection
    {
        $withMatches = collect();
        $matchIds = DB::table('match_participations')->distinct()->pluck('match_id');
        if ($matchIds->isNotEmpty()) {
            $withMatches = DB::table('matches')->whereIn('id', $matchIds)->whereNotNull('home_score')
                ->get(['home_club_id', 'away_club_id'])
                ->flatMap(fn ($m) => [$m->home_club_id, $m->away_club_id])->filter()->map(fn ($id) => (int) $id)->unique()->flip();
        }
        $associations = DB::table('associations')->pluck('name', 'id');

        return DB::table('clubs')->orderBy('name')->get(['id', 'name', 'association_id'])
            ->map(fn ($c) => (object) ['id' => (int) $c->id, 'name' => $c->name,
                'association' => $c->association_id ? ($associations[$c->association_id] ?? null) : null,
                'has_matches' => $withMatches->has((int) $c->id)]);
    }

    public function forClub(int $clubId): ?array
    {
        return Cache::remember("coach-cockpit:v1:club:{$clubId}", now()->addMinutes(15), fn () => $this->build($clubId));
    }

    private function build(int $clubId): ?array
    {
        $competitionId = DB::table('matches')
            ->where(fn ($q) => $q->where('home_club_id', $clubId)->orWhere('away_club_id', $clubId))
            ->whereNotNull('home_score')
            ->select('competition_id', DB::raw('count(*) as n'))
            ->groupBy('competition_id')->orderByDesc('n')->value('competition_id');
        if ($competitionId === null) {
            return null;
        }

        $matches = DB::table('matches')->where('competition_id', $competitionId)->whereNotNull('home_score')
            ->orderBy('matchday')->orderBy('id')
            ->get(['id', 'matchday', 'match_date', 'home_club_id', 'away_club_id', 'home_score', 'away_score']);
        $matchIds = $matches->pluck('id')->all();
        $byMatch = $matches->keyBy('id');
        $clubNames = DB::table('clubs')->whereIn('id', $matches->pluck('home_club_id')->merge($matches->pluck('away_club_id'))->unique())->pluck('name', 'id');

        $teamStats = DB::table('match_team_stats')->whereIn('match_id', $matchIds)
            ->get(['match_id', 'team_id', 'possession_pct', 'shots_total', 'shots_on_target', 'expected_goals', 'corners', 'fouls', 'yellow_cards', 'red_cards', 'goals_scored', 'is_home']);
        $teamToClub = DB::table('teams')->whereIn('id', $teamStats->pluck('team_id')->unique())->pluck('club_id', 'id')->map(fn ($c) => (int) $c)->all();
        $ownTeamId = (int) (array_search($clubId, $teamToClub, true) ?: DB::table('teams')->where('club_id', $clubId)->value('id'));

        $participations = DB::table('match_participations as mp')
            ->join('position_catalog as pc', 'pc.code', '=', 'mp.detailed_position')
            ->whereIn('mp.match_id', $matchIds)
            ->get(['mp.player_id', 'mp.team_id', 'mp.match_id', 'pc.family', 'mp.minute_in', 'mp.minute_out', 'mp.is_starter']);
        $detailed = DB::table('player_match_detailed_stats')->whereIn('match_id', $matchIds)
            ->get(['player_id', 'match_id', 'team_id', 'match_rating', 'expected_goals', 'key_passes', 'interceptions', 'recoveries',
                'ground_duels_won', 'ground_duels_total', 'aerial_duels_won', 'aerial_duels_total', 'passes_completed', 'passes_total',
                'progressive_passes', 'chances_created', 'tackles_won', 'tackles_total', 'gk_saves', 'gk_shots_faced_on_target', 'gk_goals_conceded',
                'distance_covered_km', 'dribbles_attempted', 'dribbles_completed'])
            ->keyBy(fn ($s) => $s->match_id . '|' . $s->player_id);
        $events = DB::table('match_events')->whereIn('match_id', $matchIds)->whereIn('type', ['goal', 'yellow_card', 'red_card'])
            ->get(['match_id', 'type', 'player_id', 'assisted_by_player_id']);
        $eventCount = [];
        foreach ($events as $e) {
            if ($e->type === 'goal') {
                $eventCount[$e->match_id . '|' . $e->player_id]['g'] = ($eventCount[$e->match_id . '|' . $e->player_id]['g'] ?? 0) + 1;
                if ($e->assisted_by_player_id) {
                    $k = $e->match_id . '|' . $e->assisted_by_player_id;
                    $eventCount[$k]['a'] = ($eventCount[$k]['a'] ?? 0) + 1;
                }
            } else {
                $k = $e->match_id . '|' . $e->player_id;
                $t = $e->type === 'yellow_card' ? 'yc' : 'rc';
                $eventCount[$k][$t] = ($eventCount[$k][$t] ?? 0) + 1;
            }
        }

        // Lignes de participation enrichies (format du script d'apprentissage)
        $rows = $participations->map(function ($p) use ($byMatch, $detailed, $eventCount) {
            $s = $detailed[$p->match_id . '|' . $p->player_id] ?? null;
            $ev = $eventCount[$p->match_id . '|' . $p->player_id] ?? [];
            $v = fn ($k) => $s ? (float) ($s->{$k} ?? 0) : 0.0;

            return [
                'player_id' => (int) $p->player_id, 'team_id' => (int) $p->team_id, 'match_id' => (int) $p->match_id,
                'matchday' => (int) $byMatch[$p->match_id]->matchday, 'family' => $p->family,
                'mins' => max(0, (int) $p->minute_out - (int) $p->minute_in), 'is_starter' => $p->is_starter ? 1 : 0,
                'rating' => $s && $s->match_rating !== null ? (float) $s->match_rating : null,
                'xg' => $v('expected_goals'), 'kp' => $v('key_passes'), 'itc' => $v('interceptions'), 'rec' => $v('recoveries'),
                'dw' => $v('ground_duels_won') + $v('aerial_duels_won'), 'dt' => $v('ground_duels_total') + $v('aerial_duels_total'),
                'aw' => $v('aerial_duels_won'), 'at' => $v('aerial_duels_total'), 'pc' => $v('passes_completed'), 'pt' => $v('passes_total'),
                'pp' => $v('progressive_passes'), 'cc' => $v('chances_created'), 'tw' => $v('tackles_won'), 'tt' => $v('tackles_total'),
                'sv' => $v('gk_saves'), 'sf' => $v('gk_shots_faced_on_target'), 'gkc' => $v('gk_goals_conceded'),
                'km' => $v('distance_covered_km'), 'dra' => $v('dribbles_attempted'), 'drc' => $v('dribbles_completed'),
                'g' => $ev['g'] ?? 0, 'a' => $ev['a'] ?? 0, 'yc' => $ev['yc'] ?? 0, 'rc' => $ev['rc'] ?? 0,
            ];
        });

        // Statistiques d'équipe par match, côté club et côté adversaire
        $statsByMatchTeam = $teamStats->keyBy(fn ($t) => $t->match_id . '|' . $teamToClub[$t->team_id]);
        $statsByMatch = $teamStats->groupBy('match_id');
        $teamMatchRows = [];
        foreach ($teamStats as $t) {
            $opp = $statsByMatch[$t->match_id]->first(fn ($o) => $o->team_id !== $t->team_id);
            if (!$opp) {
                continue;
            }
            $teamMatchRows[] = ['team_id' => (int) $t->team_id, 'club_id' => $teamToClub[$t->team_id], 'matchday' => (int) $byMatch[$t->match_id]->matchday,
                'is_home' => (bool) $t->is_home, 'gf' => (int) $t->goals_scored, 'ga' => (int) $opp->goals_scored,
                'xg' => $t->expected_goals !== null ? (float) $t->expected_goals : null, 'xga' => $opp->expected_goals !== null ? (float) $opp->expected_goals : null, 'sh' => (int) $t->shots_total, 'sha' => (int) $opp->shots_total,
                'sot' => (int) $t->shots_on_target, 'poss' => (float) $t->possession_pct, 'fo' => (int) $t->fouls];
        }

        $clubMatches = $this->clubMatches($clubId, $matches, $statsByMatchTeam, $rows, $clubNames);
        $table = $this->table($matches, $teamMatchRows, $clubNames);
        $squadIds = DB::table('players')->where('club_id', $clubId)->pluck('id')->map(fn ($i) => (int) $i)->all();
        $squadRows = $rows->filter(fn ($r) => in_array($r['player_id'], $squadIds, true));

        $roleHistory = $this->roleHistory($squadIds, $matches);
        $playerRows = $squadRows->groupBy('player_id')->map(fn ($g) => $g->values()->all())->all();

        return [
            'club' => ['id' => $clubId, 'name' => (string) ($clubNames[$clubId] ?? ''), 'team_id' => $ownTeamId],
            'competition' => DB::table('competitions')->where('id', $competitionId)->value('name') ?? 'Compétition',
            'isDemo' => (bool) DB::table('players')->where('club_id', $clubId)->where('is_demo', true)->exists(),
            'matches' => $clubMatches,
            'table' => $table,
            'cum' => $this->cumulativePoints($matches),
            'clubs' => $this->clubProfiles($teamMatchRows, $rows, $clubNames, $teamToClub),
            'squad' => $this->squad($clubId, $squadIds, $squadRows),
            'pm' => $squadRows->sortBy([['matchday', 'asc'], ['player_id', 'asc']])->map(fn ($r) => [
                $r['player_id'], $r['matchday'], $r['family'], $r['mins'], (bool) $r['is_starter'], $r['rating'], round($r['xg'], 2), $r['kp'], $r['itc'],
                $r['dw'], $r['dt'], $r['aw'], $r['at'], $r['pc'], $r['pt'], $r['rec'], $r['pp'], $r['cc'], $r['g'], $r['a'], $r['yc'], $r['rc'], $r['sv'], $r['sf'], $r['gkc'],
            ])->values()->all(),
            'model' => $this->model->payload($playerRows, $teamMatchRows, $roleHistory, $ownTeamId, $teamToClub),
        ];
    }

    private function clubMatches(int $clubId, Collection $matches, Collection $statsByMatchTeam, Collection $rows, $clubNames): array
    {
        $out = [];
        $rowsByMatch = $rows->groupBy('match_id');
        foreach ($matches as $m) {
            $home = (int) $m->home_club_id === $clubId;
            if (!$home && (int) $m->away_club_id !== $clubId) {
                continue;
            }
            $oppId = $home ? (int) $m->away_club_id : (int) $m->home_club_id;
            $a = $statsByMatchTeam[$m->id . '|' . $clubId] ?? null;
            $b = $statsByMatchTeam[$m->id . '|' . $oppId] ?? null;
            $own = ($rowsByMatch[$m->id] ?? collect())->filter(fn ($r) => $r['team_id'] === ($a ? (int) $a->team_id : -1));
            $sum = fn ($k) => $own->sum($k);
            $out[] = [
                'md' => (int) $m->matchday, 'date' => substr((string) $m->match_date, 0, 10), 'home' => $home, 'opp' => (string) ($clubNames[$oppId] ?? ''),
                'gf' => (int) ($home ? $m->home_score : $m->away_score), 'ga' => (int) ($home ? $m->away_score : $m->home_score),
                'poss' => (float) ($a->possession_pct ?? 0), 'sh' => (int) ($a->shots_total ?? 0), 'sot' => (int) ($a->shots_on_target ?? 0),
                'xg' => (float) ($a->expected_goals ?? 0), 'cor' => (int) ($a->corners ?? 0), 'fo' => (int) ($a->fouls ?? 0),
                'yc' => (int) ($a->yellow_cards ?? 0), 'rc' => (int) ($a->red_cards ?? 0),
                'sh_a' => (int) ($b->shots_total ?? 0), 'sot_a' => (int) ($b->shots_on_target ?? 0), 'xga' => (float) ($b->expected_goals ?? 0),
                'pt' => $sum('pt'), 'pc' => $sum('pc'), 'tt' => $sum('tt'), 'tw' => $sum('tw'),
                'gdt' => $sum('dt') - $sum('at'), 'gdw' => $sum('dw') - $sum('aw'), 'adt' => $sum('at'), 'adw' => $sum('aw'),
                'itc' => $sum('itc'), 'rec' => $sum('rec'), 'kp' => $sum('kp'), 'pp' => $sum('pp'), 'km' => round($sum('km'), 1),
                'dra' => $sum('dra'), 'drc' => $sum('drc'), 'cc' => $sum('cc'),
            ];
        }

        return $out;
    }

    private function table(Collection $matches, array $teamMatchRows, $clubNames): array
    {
        $acc = [];
        foreach ($matches as $m) {
            foreach ([[(int) $m->home_club_id, (int) $m->home_score, (int) $m->away_score], [(int) $m->away_club_id, (int) $m->away_score, (int) $m->home_score]] as [$c, $gf, $ga]) {
                $r = $acc[$c] ?? ['id' => $c, 'name' => (string) ($clubNames[$c] ?? ''), 'j' => 0, 'w' => 0, 'd' => 0, 'l' => 0, 'gf' => 0, 'ga' => 0, 'gd' => 0, 'pts' => 0, 'xg' => 0.0, 'xga' => 0.0];
                $r['j']++; $r['gf'] += $gf; $r['ga'] += $ga; $r['gd'] += $gf - $ga;
                if ($gf > $ga) { $r['w']++; $r['pts'] += 3; } elseif ($gf === $ga) { $r['d']++; $r['pts'] += 1; } else { $r['l']++; }
                $acc[$c] = $r;
            }
        }
        foreach ($teamMatchRows as $t) {
            if (isset($acc[$t['club_id']])) {
                $acc[$t['club_id']]['xg'] += $t['xg'];
                $acc[$t['club_id']]['xga'] += $t['xga'];
            }
        }
        $table = array_values(array_map(function ($r) { $r['xg'] = round($r['xg'], 1); $r['xga'] = round($r['xga'], 1); return $r; }, $acc));
        usort($table, fn ($a, $b) => [$b['pts'], $b['gd'], $b['gf']] <=> [$a['pts'], $a['gd'], $a['gf']]);

        return $table;
    }

    private function cumulativePoints(Collection $matches): array
    {
        $perMatchday = [];
        foreach ($matches as $m) {
            $h = (int) $m->home_score; $a = (int) $m->away_score;
            $perMatchday[(int) $m->home_club_id][(int) $m->matchday] = $h > $a ? 3 : ($h === $a ? 1 : 0);
            $perMatchday[(int) $m->away_club_id][(int) $m->matchday] = $a > $h ? 3 : ($h === $a ? 1 : 0);
        }
        $out = [];
        foreach ($perMatchday as $club => $byMd) {
            ksort($byMd);
            $total = 0;
            $out[$club] = array_values(array_map(function ($p) use (&$total) { return $total += $p; }, $byMd));
        }

        return $out;
    }

    private function clubProfiles(array $teamMatchRows, Collection $rows, $clubNames, array $teamToClub): array
    {
        $maxMd = max(array_merge([0], array_column($teamMatchRows, 'matchday')));
        $byClub = collect($teamMatchRows)->groupBy('club_id');
        $playerSums = $rows->groupBy(fn ($r) => $teamToClub[$r['team_id']] ?? $r['team_id']);

        return $byClub->map(function ($g, $clubId) use ($maxMd, $playerSums, $clubNames) {
            $home = $g->where('is_home', true);
            $away = $g->where('is_home', false);
            $p = $playerSums[$clubId] ?? collect();

            return [
                'id' => (int) $clubId, 'name' => (string) ($clubNames[$clubId] ?? ''), 'j' => $g->count(),
                'gf' => $g->sum('gf'), 'ga' => $g->sum('ga'), 'xg' => round($g->sum('xg'), 2), 'xga' => round($g->sum('xga'), 2),
                'sh' => $g->sum('sh'), 'sha' => $g->sum('sha'), 'sot' => $g->sum('sot'), 'poss' => round($g->avg('poss'), 1), 'fo' => $g->sum('fo'),
                'jh' => $home->count(), 'xgh' => round($home->sum('xg'), 2), 'xgah' => round($home->sum('xga'), 2),
                'xgaw' => round($away->sum('xg'), 2), 'xgaaw' => round($away->sum('xga'), 2),
                'last5' => $g->filter(fn ($r) => $r['matchday'] > $maxMd - 5)->sortBy('matchday')
                    ->map(fn ($r) => [$r['matchday'], $r['gf'], $r['ga'], round($r['xg'], 2), round($r['xga'], 2)])->values()->all(),
                'pc' => $p->sum('pc'), 'pt' => $p->sum('pt'), 'dw' => $p->sum('dw'), 'dt' => $p->sum('dt'), 'aw' => $p->sum('aw'), 'at' => $p->sum('at'),
                'itc' => $p->sum('itc'), 'rec' => $p->sum('rec'), 'pp' => $p->sum('pp'), 'cc' => $p->sum('cc'),
            ];
        })->values()->all();
    }

    private function squad(int $clubId, array $squadIds, Collection $squadRows): array
    {
        $players = DB::table('players')->whereIn('id', $squadIds)->get(['id', 'first_name', 'last_name', 'jersey_number']);
        $version = DB::table('player_role_evaluations')->whereIn('player_id', $squadIds)->max('role_config_version_id');
        $roles = $version === null ? collect() : DB::table('player_role_evaluations')->whereIn('player_id', $squadIds)
            ->where('role_config_version_id', $version)->whereNull('period_end')->where('role_fit_score', 0)
            ->orderBy('computed_at')->get(['player_id', 'score', 'reliability'])->keyBy('player_id');

        $rowsByPlayer = $squadRows->groupBy('player_id');
        $out = $players->map(function ($p) use ($rowsByPlayer, $roles) {
            $r = $rowsByPlayer[(int) $p->id] ?? collect();
            $played = $r->filter(fn ($x) => $x['mins'] > 0);
            $family = $played->groupBy('family')->map(fn ($g) => $g->sum('mins'))->sortDesc()->keys()->first();
            $rated = $played->whereNotNull('rating');
            $role = $roles[$p->id] ?? null;

            return [
                'id' => (int) $p->id, 'name' => trim($p->first_name . ' ' . $p->last_name), 'num' => $p->jersey_number !== null ? (int) $p->jersey_number : null,
                'family' => $family, 'mj' => $played->count(), 'tit' => $played->where('is_starter', 1)->count(), 'mins' => $played->sum('mins'),
                'g' => $r->sum('g'), 'a' => $r->sum('a'), 'yc' => $r->sum('yc'), 'rc' => $r->sum('rc'),
                'rating' => $rated->isNotEmpty() ? round($rated->avg('rating'), 2) : null,
                'pt' => $r->sum('pt'), 'pc' => $r->sum('pc'), 'dt' => $r->sum('dt'), 'dw' => $r->sum('dw'), 'kp' => $r->sum('kp'), 'itc' => $r->sum('itc'),
                'xg' => round($r->sum('xg'), 2),
                'score' => $role ? (float) $role->score : null, 'reliability' => $role ? (float) $role->reliability : null,
            ];
        })->sortByDesc('mins')->values()->all();

        return $out;
    }

    /** Historique « Rôle et apport » (ligne famille jouée), daté par la dernière journée jouée à sa date de fin de période. */
    private function roleHistory(array $playerIds, Collection $matches): array
    {
        $version = DB::table('player_role_evaluations')->whereIn('player_id', $playerIds)->max('role_config_version_id');
        if ($version === null) {
            return [];
        }
        $dates = $matches->map(fn ($m) => [substr((string) $m->match_date, 0, 10), (int) $m->matchday]);

        return DB::table('player_role_evaluations')->whereIn('player_id', $playerIds)->where('role_config_version_id', $version)
            ->whereNotNull('period_end')->where('role_fit_score', 0)->get(['player_id', 'period_end', 'score', 'reliability'])
            ->map(function ($r) use ($dates) {
                $end = substr((string) $r->period_end, 0, 10);
                $md = $dates->filter(fn ($d) => $d[0] <= $end)->max(fn ($d) => $d[1]);

                return ['player_id' => (int) $r->player_id, 'md' => $md, 'score' => (float) $r->score, 'reliability' => (float) $r->reliability];
            })->all();
    }
}
