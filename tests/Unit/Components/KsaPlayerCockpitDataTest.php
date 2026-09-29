<?php

namespace Tests\Unit\Components;

use App\Services\KsaPlayerCockpitData;
use PHPUnit\Framework\TestCase;

class KsaPlayerCockpitDataTest extends TestCase
{
    public function test_values_are_taken_from_the_requested_players_ksa_rows_without_zero_fallback(): void
    {
        $service = new KsaPlayerCockpitData();
        $metrics = collect([
            (object) ['metric_name' => 'minutes_played', 'metric_unit' => 'minutes', 'metric_value' => '459'],
            (object) ['metric_name' => 'matches_played', 'metric_unit' => 'count', 'metric_value' => '7'],
            (object) ['metric_name' => 'index_ksa', 'metric_unit' => 'ksa_index', 'metric_value' => '171'],
            (object) ['metric_name' => 'goals', 'metric_unit' => 'count', 'metric_value' => '0.29'],
            (object) ['metric_name' => 'passes_accuracy', 'metric_unit' => 'percent', 'metric_value' => '0.8411'],
        ]);
        $data = $service->fromMetrics($metrics);
        self::assertSame(459.0, $data['minutes']);
        self::assertSame(7, $data['matches']);
        self::assertSame(171.0, $data['index']);
        self::assertSame(0.29, $data['v']['goals']);
        self::assertSame(0.8411, $data['p']['passes_accuracy']);
        self::assertNull($data['v']['shots']);
        self::assertNull($data['p']['crosses_accurate']);
        self::assertCount(30, $data['v']);
        self::assertCount(11, $data['p']);
    }

    public function test_missing_invalid_and_zero_values_keep_their_meanings(): void
    {
        $service = new KsaPlayerCockpitData();
        $data = $service->fromMetrics(collect([
            (object) ['metric_name' => 'minutes_played', 'metric_unit' => 'minutes', 'metric_value' => 0],
            (object) ['metric_name' => 'goals', 'metric_unit' => 'count', 'metric_value' => null],
            (object) ['metric_name' => 'crosses_accurate', 'metric_unit' => 'percent', 'metric_value' => 1.25],
        ]));
        self::assertSame(0.0, $data['minutes']);
        self::assertNull($data['matches']);
        self::assertNull($data['v']['goals']);
        self::assertNull($data['p']['crosses_accurate']);
    }
}
