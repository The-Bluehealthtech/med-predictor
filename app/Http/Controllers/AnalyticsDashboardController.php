<?php

namespace App\Http\Controllers;

use App\Models\PerformanceAlert;
use App\Models\PlayerPerformance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnalyticsDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $performances = PlayerPerformance::query();

        $stats = [
            'performance_records' => (clone $performances)->count(),
            'average_overall' => $this->average(clone $performances, 'overall_performance_score'),
            'average_physical' => $this->average(clone $performances, 'physical_score'),
            'average_technical' => $this->average(clone $performances, 'technical_score'),
        ];

        $alertsQuery = $this->scopeAlerts(
            PerformanceAlert::query()
                ->with(['player', 'club'])
                ->where('is_active', true)
                ->where('is_resolved', false)
        );

        $stats['active_alerts'] = (clone $alertsQuery)->count();
        $stats['critical_alerts'] = (clone $alertsQuery)
            ->where('alert_level', 'critical')
            ->count();

        $alerts = $alertsQuery
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('analytics.dashboard-canonical', compact('stats', 'alerts'));
    }

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

    private function average($query, string $column): ?float
    {
        $value = $query->avg($column);

        return $value === null ? null : round((float) $value, 1);
    }
}
