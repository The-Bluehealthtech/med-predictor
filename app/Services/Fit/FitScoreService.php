<?php

namespace App\Services\Fit;

use App\Models\PerformanceMetric;
use App\Models\Player;
use Illuminate\Support\Carbon;
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

    public function calculate(
        Player $player,
        int $days = 30,
        ?Carbon $asOf = null
    ): array {
        $asOf ??= now();

        $metrics = PerformanceMetric::query()
            ->where('player_id', $player->id)
            ->verified()
            ->whereBetween('measurement_date', [
                $asOf->copy()->subDays($days),
                $asOf,
            ])
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

    public function diagnose(
        Player $player,
        int $days = 30,
        ?Carbon $asOf = null
    ): array {
        $asOf ??= now();

        $verifiedMetricCount = PerformanceMetric::query()
            ->where('player_id', $player->id)
            ->verified()
            ->whereBetween('measurement_date', [
                $asOf->copy()->subDays($days),
                $asOf,
            ])
            ->count();

        $calculation = $this->calculate(
            $player,
            $days,
            $asOf
        );

        $acceptedMetricCount = collect($calculation['axes'])
            ->sum(
                fn (array $axis): int => count($axis['metrics'])
            );

        $missingAxes = collect($calculation['axes'])
            ->filter(
                fn (array $axis): bool => $axis['score'] === null
            )
            ->keys()
            ->values()
            ->all();

        return [
            'window_days' => $days,
            'verified_metric_count' => $verifiedMetricCount,
            'accepted_metric_count' => $acceptedMetricCount,
            'missing_axes' => $missingAxes,
            'calculation' => $calculation,
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
                'metric_id' => $metric->id,
                'name' => $metricName,
                'raw_value' => (float) $metric->metric_value,
                'unit' => $metric->metric_unit,
                'score' => $score,
                'confidence' => $metric->confidence_score !== null
                    ? (float) $metric->confidence_score
                    : null,
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

        if (($config['scale'] ?? null) === 'explicit') {
            // Une valeur déjà exprimée en pourcentage est directement exploitable.
            if (
                $metric->metric_unit === '%'
                && $value >= 0
                && $value <= 100
            ) {
                return round($value, 1);
            }

            // Toute autre échelle doit être explicitement déclarée
            // dans les métadonnées de la métrique.
            $metadata = is_array($metric->metadata)
                ? $metric->metadata
                : [];

            $min = $metadata['scale_min'] ?? null;
            $max = $metadata['scale_max'] ?? null;

            if (!is_numeric($min) || !is_numeric($max)) {
                return null;
            }

            $min = (float) $min;
            $max = (float) $max;

            if (
                $max <= $min
                || $value < $min
                || $value > $max
            ) {
                return null;
            }

            return round(
                (($value - $min) / ($max - $min)) * 100,
                1
            );
        }

        return null;
    }
}
