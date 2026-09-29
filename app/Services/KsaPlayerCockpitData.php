<?php

namespace App\Services;

use Illuminate\Support\Collection;

class KsaPlayerCockpitData
{
    public const COUNTS = [
        'goals', 'expected_goals', 'shots', 'shots_on_target', 'chances',
        'chances_created', 'key_passes', 'passes_for_a_shot',
        'dribbling_in_the_final_third', 'involvement_in_scoring_attacks',
        'passes', 'short_passes', 'long_passes', 'progressive_passes',
        'progressive_open_passes', 'passes_forward_to_the_final_third',
        'passes_into_the_penalty_box', 'crosses', 'tackles', 'interceptions',
        'loose_ball_recoveries', 'challenges', 'defensive_challenges',
        'attacking_challenges', 'aerial_challenges', 'dribbles',
        'fouls_committed', 'fouls_suffered', 'mistakes_leading_to_chances',
        'mistakes_leading_to_goals',
    ];

    public const RATES = [
        'passes_accuracy', 'progressive_passes_accurate',
        'short_passes_accurate', 'passes_into_the_penalty_box_accurate',
        'crosses_accurate', 'challenges_won', 'defensive_challenges_won',
        'attacking_challenges_won', 'aerial_challenges_won',
        'tackles_successful', 'dribbles_successful',
    ];

    public function fromMetrics(Collection $metrics): array
    {
        $value = function (string $key, ?string $unit = null) use ($metrics): ?float {
            $row = $metrics->first(function ($item) use ($key) {
                $name = strtolower(str_replace(' ', '_', trim((string) data_get($item, 'metric_name', ''))));
                return $name === $key && data_get($item, 'metric_value') !== null;
            });
            $raw = data_get($row, 'metric_value');
            if (!is_numeric($raw) || ($unit !== null && strtolower((string) data_get($row, 'metric_unit')) !== $unit)) {
                return null;
            }
            $number = (float) $raw;
            if (!is_finite($number) || $number < 0 || ($unit === 'percent' && $number > 1)) {
                return null;
            }
            return $number;
        };

        $matches = $value('matches_played', 'count');
        return [
            'minutes' => $value('minutes_played', 'minutes'),
            'matches' => $matches !== null && floor($matches) === $matches ? (int) $matches : null,
            'index' => $value('index_ksa', 'ksa_index'),
            'v' => collect(self::COUNTS)->mapWithKeys(fn ($key) => [$key => $value($key)])->all(),
            'p' => collect(self::RATES)->mapWithKeys(fn ($key) => [$key => $value($key, 'percent')])->all(),
        ];
    }
}
