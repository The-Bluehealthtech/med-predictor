<?php

namespace App\Http\Controllers;

use App\Models\PlayerPerformance;
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
            'goals' => (int) (clone $query)->sum('goals'),
            'assists' => (int) (clone $query)->sum('assists'),
            'minutes_played' => (int) (clone $query)->sum('minutes_played'),
        ];

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
            'topPerformers'
        ));
    }

    private function roundedAverage($query, string $column): ?float
    {
        $value = $query->avg($column);

        return $value === null ? null : round((float) $value, 1);
    }
}
