<?php

namespace App\Services\CoachCockpit;

use App\Models\User;
use App\Services\RBACService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * État de l'évaluation « Rôle et apport » pour le club affiché dans le cockpit,
 * exprimé pour un entraîneur : combien de joueurs sont évalués, depuis quand,
 * et ce qu'il reste à faire pour mettre les scores à jour.
 */
final class RoleEvaluationPanel
{
    public function __construct(private readonly RBACService $rbac)
    {
    }

    public function forClub(?int $clubId, ?User $user): array
    {
        $canManage = $user && $this->rbac->userHasPermission($user, 'record-performance-metrics');
        $ready = Schema::hasTable('role_config_versions') && Schema::hasTable('player_role_evaluations');
        $published = $ready ? DB::table('role_config_versions')->where('status', 'published')->orderByDesc('id')->get(['id', 'label', 'published_at']) : collect();
        $drafts = $ready ? DB::table('role_config_versions')->where('status', 'draft')->orderByDesc('id')->get(['id', 'label']) : collect();

        $club = ['players_played' => 0, 'players_evaluated' => 0, 'participations' => 0, 'detailed_stats' => 0, 'period_profiles' => 0, 'last_computed_at' => null, 'families' => []];
        if ($clubId && $ready) {
            $teamIds = DB::table('teams')->where('club_id', $clubId)->pluck('id');
            // Profils de saison importés (exports « Player statistics ») : évaluables sans feuille de match.
            $periodIds = DB::table('external_player_performance_metrics as m')->join('players as p', 'p.id', '=', 'm.player_id')
                ->where('p.club_id', $clubId)->where('m.score_origin', 'observed')->where('m.metric_name', 'minutes_played')->distinct()->pluck('m.player_id');
            $playerIds = DB::table('match_participations')->whereIn('team_id', $teamIds)->distinct()->pluck('player_id')->merge($periodIds)->unique()->values();
            $evaluations = DB::table('player_role_evaluations')->whereIn('player_id', $playerIds);
            $club = [
                'players_played' => $playerIds->count(),
                'players_evaluated' => (clone $evaluations)->distinct()->count('player_id'),
                'participations' => DB::table('match_participations')->whereIn('team_id', $teamIds)->count(),
                'detailed_stats' => DB::table('player_match_detailed_stats')->whereIn('player_id', $playerIds)->count(),
                'period_profiles' => $periodIds->count(),
                'last_computed_at' => ($last = (clone $evaluations)->max('computed_at')) ? Carbon::parse($last) : null,
                'families' => (clone $evaluations)->select('position_family_evaluated', DB::raw('count(distinct player_id) as n'))
                    ->groupBy('position_family_evaluated')->orderByDesc('n')->pluck('n', 'position_family_evaluated')->all(),
            ];
        }

        $coverage = $club['players_played'] > 0 ? (int) round(100 * $club['players_evaluated'] / $club['players_played']) : 0;
        $state = match (true) {
            $published->isEmpty() => 'locked',
            $club['participations'] === 0 && $club['period_profiles'] === 0 => 'no_data',
            $club['players_evaluated'] === 0 => 'to_compute',
            $coverage < 100 => 'partial',
            default => 'up_to_date',
        };

        return compact('canManage', 'published', 'drafts', 'club', 'coverage', 'state');
    }
}
