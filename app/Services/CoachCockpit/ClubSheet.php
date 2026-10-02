<?php

namespace App\Services\CoachCockpit;

use App\Models\Club;
use App\Services\ClubOfficials\ClubOfficials;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fiche d'un club dans le cockpit entraîneur, pour les clubs sans feuille de
 * match : identité, entraîneur principal, effectif et, s'ils ont été importés,
 * les profils de saison des joueurs (exports « Player statistics ») et leurs
 * scores « Rôle et apport ».
 */
final class ClubSheet
{
    /** Indicateurs de saison affichés dans l'effectif : nom enregistré => [libellé, format]. */
    private const COLUMNS = [
        'minutes_played' => ['Minutes', 'int'],
        'goals' => ['Buts / match', 'dec'],
        'assists' => ['Passes déc. / match', 'dec'],
        'expected_goals' => ['xG / match', 'dec'],
        'passes_accuracy' => ['Passes réussies', 'pct'],
        'challenges_won' => ['Duels gagnés', 'pct'],
        'index_ksa' => ['Index', 'int'],
    ];

    public function __construct(private readonly ClubOfficials $officials)
    {
    }

    public function forClub(int $clubId): ?array
    {
        $club = Club::query()->find($clubId);
        if (!$club) {
            return null;
        }

        $players = DB::table('players')->where('club_id', $clubId)
            ->orderBy('last_name')->orderBy('first_name')
            ->get(['id', 'name', 'first_name', 'last_name', 'position', 'nationality', 'date_of_birth', 'age']);
        $ids = $players->pluck('id')->all();

        $metrics = $ids ? DB::table('external_player_performance_metrics')->whereIn('player_id', $ids)
            ->whereIn('metric_name', array_merge(array_keys(self::COLUMNS), ['matches_played']))
            ->orderBy('measured_at')->get(['player_id', 'metric_name', 'metric_value', 'season', 'competition', 'measured_at', 'source', 'raw_data'])
            ->groupBy('player_id') : collect();
        $roles = $this->roles($ids);

        $squad = $players->map(function ($p) use ($metrics, $roles) {
            $rows = ($metrics[$p->id] ?? collect())->keyBy('metric_name');
            $raw = optional($rows->first())->raw_data;
            $raw = is_string($raw) ? (json_decode($raw, true) ?: []) : (array) $raw;

            return [
                'id' => $p->id,
                'name' => trim($p->first_name . ' ' . $p->last_name) ?: $p->name,
                'position' => ($raw['Position'] ?? null) && $raw['Position'] !== '-' ? $raw['Position'] : $p->position,
                'nationality' => $p->nationality ?: (($raw['Nationality'] ?? '-') !== '-' ? $raw['Nationality'] : null),
                'age' => $p->date_of_birth ? Carbon::parse($p->date_of_birth)->age : ($p->age ?: (is_numeric($raw['Age'] ?? null) ? (int) $raw['Age'] : null)),
                'stats' => collect(self::COLUMNS)->map(fn ($c, $name) => isset($rows[$name]) ? (float) $rows[$name]->metric_value : null)->all(),
                'role' => $roles[$p->id] ?? null,
            ];
        })->sortByDesc(fn ($p) => $p['stats']['minutes_played'] ?? -1)->values()->all();

        $latest = $metrics->flatten(1)->sortByDesc('measured_at')->first();
        $coach = $this->officials->headCoach($club);

        return [
            'club' => [
                'id' => $club->id,
                'name' => str_replace(' (Démo)', '', $club->name),
                'association' => $club->association_id ? DB::table('associations')->where('id', $club->association_id)->value('name') : null,
                'founded_year' => $club->founded_year,
                'website' => $club->website,
            ],
            'coach' => $coach ? [
                'name' => trim(($coach->popular_name ?: $coach->international_first_name . ' ' . $coach->international_last_name)),
                'nationality' => $coach->nationality,
                'certification' => $coach->certification_name ?: $coach->certification_type,
                'since' => $coach->registration_valid_from ? Carbon::parse($coach->registration_valid_from)->format('m/Y') : null,
            ] : null,
            'columns' => self::COLUMNS,
            'squad' => $squad,
            'summary' => [
                'players' => count($squad),
                'profiles' => $metrics->filter(fn ($rows) => $rows->contains('metric_name', 'minutes_played'))->count(),
                'scored' => count(array_filter($squad, fn ($p) => $p['role'] !== null)),
                'season' => $latest->season ?? null,
                'competition' => $latest->competition ?? null,
                'source' => $latest->source ?? null,
                'measured_at' => isset($latest->measured_at) ? Carbon::parse($latest->measured_at) : null,
            ],
        ];
    }

    /** Dernier score « Rôle et apport » sur le poste joué, par joueur. */
    private function roles(array $ids): array
    {
        if (!$ids || !Schema::hasTable('player_role_evaluations')) {
            return [];
        }

        return DB::table('player_role_evaluations')->whereIn('player_id', $ids)->where('role_fit_score', 0)->whereNull('period_end')
            ->orderBy('computed_at')->get(['player_id', 'score', 'reliability', 'position_family_evaluated'])
            ->keyBy('player_id')
            ->map(fn ($r) => ['score' => (float) $r->score, 'reliability' => $r->reliability !== null ? (int) round(100 * $r->reliability) : null, 'family' => $r->position_family_evaluated])
            ->all();
    }
}
