<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RpmController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $query = DB::table('player_real_time_health as health')
            ->join('players', 'players.id', '=', 'health.player_id')
            ->leftJoin('clubs', 'clubs.id', '=', 'players.club_id');

        $query = $this->scopeQuery($query);

        $measurements = $query
            ->select([
                'health.id',
                'health.player_id',
                'health.measurement_time',
                'health.data_source',
                'health.heart_rate',
                'health.oxygen_saturation',
                'health.temperature',
                'health.hydration_level',
                'health.recovery_score',
                'health.readiness_score',
                'health.stress_level',
                'health.high_heart_rate_alert',
                'health.low_oxygen_alert',
                'health.high_temperature_alert',
                'health.dehydration_alert',
                'health.stress_alert',
                'players.first_name',
                'players.last_name',
                'clubs.name as club_name',
            ])
            ->orderByDesc('health.measurement_time')
            ->limit(50)
            ->get();

        $latest = $measurements->first();

        $stats = [
            'measurements' => $measurements->count(),
            'latest_measurement' => $latest?->measurement_time,
            'players' => $measurements->pluck('player_id')->unique()->count(),
            'active_alerts' => $measurements->filter(function ($row) {
                return (bool) $row->high_heart_rate_alert
                    || (bool) $row->low_oxygen_alert
                    || (bool) $row->high_temperature_alert
                    || (bool) $row->dehydration_alert
                    || (bool) $row->stress_alert;
            })->count(),
        ];

        return view('rpm.index-canonical', compact('measurements', 'stats'));
    }

    private function scopeQuery(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user->isSystemAdmin()) {
            return $query;
        }

        if ($user->isPlayer()) {
            return $user->player_id
                ? $query->where('health.player_id', $user->player_id)
                : $query->whereRaw('1 = 0');
        }

        if ($user->isClubUser()) {
            return $user->club_id
                ? $query->where('players.club_id', $user->club_id)
                : $query->whereRaw('1 = 0');
        }

        if ($user->isAssociationUser()) {
            return $user->association_id
                ? $query->where('players.association_id', $user->association_id)
                : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }
}
