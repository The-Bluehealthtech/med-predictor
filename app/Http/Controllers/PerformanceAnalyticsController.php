<?php

namespace App\Http\Controllers;

use App\Models\PerformanceAlert;
use App\Models\Player;
use App\Models\PlayerPerformance;
use App\Models\PlayerSeasonStat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PerformanceAnalyticsController extends Controller
{
    public function index(): View
    {
        $query = PlayerPerformance::query();

        $stats = [
            'records' => (clone $query)->count(),
            'overall' => $this->roundedAverage(clone $query, 'overall_performance_score'),
            'physical' => $this->roundedAverage(clone $query, 'physical_score'),
            'technical' => $this->roundedAverage(clone $query, 'technical_score'),
            'tactical' => $this->roundedAverage(clone $query, 'tactical_score'),
            'mental' => $this->roundedAverage(clone $query, 'mental_score'),
            'social' => $this->roundedAverage(clone $query, 'social_score'),
            'passing_accuracy' => $this->roundedAverage(clone $query, 'passing_accuracy'),
            'shooting_accuracy' => $this->roundedAverage(clone $query, 'shooting_accuracy'),
        ];

        // Buts, passes décisives et minutes : ces colonnes de player_performances
        // ne sont pas renseignées ; les totaux viennent des statistiques de saison
        // (feuilles de match), limitées aux joueurs visibles par l'utilisateur.
        $seasonStats = PlayerSeasonStat::query()->whereIn('player_id', Player::query()->select('id'));
        $stats['goals'] = (int) (clone $seasonStats)->sum('goals');
        $stats['assists'] = (int) (clone $seasonStats)->sum('assists');
        $stats['minutes_played'] = (int) (clone $seasonStats)->sum('minutes_played');
        $stats['season_players'] = (int) (clone $seasonStats)->distinct()->count('player_id');

        // Alertes de performance actives (reprises de l'ancien Analytics Dashboard)
        $alertsQuery = $this->scopeAlerts(
            PerformanceAlert::query()
                ->with(['player', 'club'])
                ->where('is_active', true)
                ->where('is_resolved', false)
        );
        $stats['active_alerts'] = (clone $alertsQuery)->count();
        $stats['critical_alerts'] = (clone $alertsQuery)->where('alert_level', 'critical')->count();
        $alerts = $alertsQuery->orderByDesc('created_at')->limit(10)->get();

        $recent = PlayerPerformance::query()
            ->with('player')
            ->orderByDesc('performance_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $trendRows = PlayerPerformance::query()
            ->whereNotNull('performance_date')
            ->whereNotNull('overall_performance_score')
            ->orderByDesc('performance_date')
            ->limit(12)
            ->get(['performance_date', 'overall_performance_score'])
            ->sortBy('performance_date')
            ->values();

        $trend = [
            'labels' => $trendRows
                ->map(fn ($row) => optional($row->performance_date)->format('d/m/Y'))
                ->all(),
            'values' => $trendRows
                ->map(fn ($row) => (float) $row->overall_performance_score)
                ->all(),
        ];

        $topRows = PlayerPerformance::query()
            ->select(
                'player_id',
                DB::raw('AVG(overall_performance_score) as average_score')
            )
            ->whereNotNull('overall_performance_score')
            ->groupBy('player_id')
            ->orderByDesc('average_score')
            ->limit(5)
            ->with('player')
            ->get();

        $topPerformers = [
            'labels' => $topRows->map(function ($row) {
                $name = trim(
                    ($row->player?->first_name ?? '')
                    . ' '
                    . ($row->player?->last_name ?? '')
                );

                return $name !== '' ? $name : 'Joueur #' . $row->player_id;
            })->all(),
            'values' => $topRows
                ->map(fn ($row) => round((float) $row->average_score, 1))
                ->all(),
        ];

        return view('modules.performances.analytics-canonical', compact(
            'stats',
            'recent',
            'trend',
            'topPerformers',
            'alerts'
        ));
    }

    /** Périmètre des alertes selon le rôle : tout pour l'admin système, sinon joueur, club ou association. */
    private function scopeAlerts(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user->isSystemAdmin()) {
            return $query;
        }

        if ($user->isPlayer()) {
            return $user->player_id
                ? $query->where('player_id', $user->player_id)
                : $query->whereRaw('1 = 0');
        }

        if ($user->isClubUser()) {
            return $user->club_id
                ? $query->where('club_id', $user->club_id)
                : $query->whereRaw('1 = 0');
        }

        if ($user->isAssociationUser()) {
            return $user->association_id
                ? $query->whereHas('club', fn ($club) =>
                    $club->where('association_id', $user->association_id)
                )
                : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }

    private function roundedAverage($query, string $column): ?float
    {
        $value = $query->avg($column);

        return $value === null ? null : round((float) $value, 1);
    }
}
