<?php

namespace App\Services\Fit;

use App\Models\PerformanceMetric;
use App\Models\Player;
use Illuminate\Support\Collection;

class FitScoreService
{
    private const CATALOG = [
        'physical' => [
            'endurance' => ['unit' => '%', 'scale' => 'percentage'],
            'recovery' => ['unit' => '%', 'scale' => 'percentage'],
            'agility' => ['unit' => 'score', 'scale' => 'score_10'],
        ],

        'technical' => [
            'passing' => ['unit' => '%', 'scale' => 'percentage'],
            'shooting' => ['unit' => '%', 'scale' => 'percentage'],
            'dribbling' => ['unit' => '%', 'scale' => 'percentage'],
            'tackling' => ['unit' => '%', 'scale' => 'percentage'],
        ],

        'tactical' => [
            'positioning' => ['unit' => 'score', 'scale' => 'score_10'],
            'decision_making' => ['unit' => 'score', 'scale' => 'score_10'],
            'game_intelligence' => ['unit' => 'score', 'scale' => 'score_10'],
            'teamwork' => ['unit' => 'score', 'scale' => 'score_10'],
            'leadership' => ['unit' => 'score', 'scale' => 'score_10'],
        ],

        'mental' => [
            'concentration' => ['unit' => '%', 'scale' => 'percentage'],
            'motivation' => ['unit' => '%', 'scale' => 'percentage'],
            'pressure_handling' => ['unit' => 'score', 'scale' => 'score_10'],
            'adaptability' => ['unit' => 'score', 'scale' => 'score_10'],
        ],

        'social' => [
            'team_cohesion' => ['scale' => 'explicit'],
            'communication_skills' => ['scale' => 'explicit'],
            'coachability' => ['scale' => 'explicit'],
            'discipline_level' => ['scale' => 'explicit'],
            'professional_attitude' => ['scale' => 'explicit'],
        ],
    ];

    public function calculate(Player $player, int $days = 30): array
    {
        $metrics = PerformanceMetric::query()
            ->where('player_id', $player->id)
            ->verified()
            ->where('measurement_date', '>=', now()->subDays($days))
            ->orderByDesc('measurement_date')
            ->get();

        $axes = [];

        foreach (self::CATALOG as $axis => $catalog) {
            $axes[$axis] = $this->calculateAxis(
                $metrics->where('metric_type', $axis),
                $catalog
            );
        }

        $complete = collect($axes)
            ->every(fn ($axis) => $axis['score'] !== null);

        $overall = $complete
            ? round(
                collect($axes)->avg(fn ($axis) => $axis['score']),
                1
            )
            : null;

        $usedMetrics = collect($axes)
            ->flatMap(fn ($axis) => $axis['metrics']);

        $confidence = $usedMetrics->isNotEmpty()
            ? round($usedMetrics->avg('confidence'), 2)
            : null;

        return [
            'physical_score' => $axes['physical']['score'],
            'technical_score' => $axes['technical']['score'],
            'tactical_score' => $axes['tactical']['score'],
            'mental_score' => $axes['mental']['score'],
            'social_score' => $axes['social']['score'],
            'overall_score' => $overall,
            'confidence' => $confidence,
            'complete' => $complete,
            'axes' => $axes,
        ];
    }

    private function calculateAxis(
        Collection $metrics,
        array $catalog
    ): array {
        $normalized = [];

        foreach ($catalog as $metricName => $config) {
            $metric = $metrics
                ->where('metric_name', $metricName)
                ->sortByDesc('measurement_date')
                ->first();

            if (!$metric) {
                continue;
            }

            $score = $this->normalize($metric, $config);

            if ($score === null) {
                continue;
            }

            $normalized[] = [
                'name' => $metricName,
                'score' => $score,
                'confidence' => $metric->confidence_score,
                'source' => $metric->data_source,
                'measurement_date' => $metric->measurement_date,
            ];
        }

        if ($normalized === []) {
            return [
                'score' => null,
                'metrics' => [],
            ];
        }

        return [
            'score' => round(
                collect($normalized)->avg('score'),
                1
            ),
            'metrics' => $normalized,
        ];
    }

    private function normalize(
        PerformanceMetric $metric,
        array $config
    ): ?float {
        $value = (float) $metric->metric_value;

        if (($config['scale'] ?? null) === 'percentage') {
            if ($metric->metric_unit !== '%' || $value < 0 || $value > 100) {
                return null;
            }

            return round($value, 1);
        }

        if (($config['scale'] ?? null) === 'score_10') {
            if (
                $metric->metric_unit !== 'score'
                || $value < 0
                || $value > 10
            ) {
                return null;
            }

            return round($value * 10, 1);
        }

        // Social : aucune conversion implicite.
        // L'échelle devra être explicitement définie avant utilisation.
        return null;
    }
}
