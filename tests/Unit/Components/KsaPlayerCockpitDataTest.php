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

    public function test_remaining_excel_columns_are_preserved_including_a_count_and_its_rate(): void
    {
        $source = json_encode([
            'Player' => 'Test Player', 'Minutes played' => '450', 'Goals' => '0.2',
            'Yellow cards' => '0.5', 'Red cards' => '-', 'Assists' => '0',
            'Tackles successful' => '2', 'Tackles successful, %' => '0.8',
            'Chances successful' => '1', 'Chances successful, %' => '0.5',
        ]);
        $data = (new KsaPlayerCockpitData())->fromMetrics(collect([
            (object) ['metric_name' => 'minutes_played', 'metric_value' => '450', 'metric_unit' => 'minutes', 'raw_data' => $source],
            (object) ['metric_name' => 'goals', 'metric_value' => '0.2', 'metric_unit' => 'count'],
            (object) ['metric_name' => 'tackles_successful', 'metric_value' => '0.8', 'metric_unit' => 'percent'],
        ]));
        self::assertSame(['Yellow cards', 'Assists', 'Tackles successful', 'Chances successful', 'Chances successful, %'], array_column(array_filter($data['extra'], fn ($row) => $row['unit'] !== 'text'), 'label'));
        self::assertSame([0.5, 0.0, 2.0, 1.0, 0.5], array_column(array_filter($data['extra'], fn ($row) => $row['unit'] !== 'text'), 'value'));
        self::assertSame(0.8, $data['p']['tackles_successful']);
    }

    public function test_every_numeric_column_of_all_ksa_source_rows_has_a_display_destination(): void
    {
        $sourcePath = dirname(__DIR__, 3) . '/storage/app/imports/ksa_player_statistics.csv';
        if (!is_file($sourcePath)) self::markTestSkipped('KSA source CSV is not distributed with the repository.');
        $handle = fopen($sourcePath, 'r');
        $headers = fgetcsv($handle, 0, ',', '"', '');
        $definitions = require dirname(__DIR__, 3) . '/config/ksa_metrics.php';
        $metadata = ['№', 'Player', 'Age', 'Height', 'Weight', 'Nationality', 'Position'];
        $checked = 0;
        while (($cells = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            $row = array_combine($headers, $cells);
            $metricRows = [];
            $numericHeaders = 0;
            foreach ($row as $header => $raw) {
                if (in_array($header, $metadata, true) || !is_numeric($raw)) continue;
                $numericHeaders++;
                $name = $definitions[$header]['name'] ?? \Illuminate\Support\Str::slug($header, '_');
                $unit = $definitions[$header]['unit'] ?? (str_contains($header, '%') ? 'percent' : 'count');
                $metricRows[$name] = (object) ['metric_name' => $name, 'metric_value' => $raw, 'metric_unit' => $unit, 'raw_data' => json_encode($row)];
            }
            $data = (new KsaPlayerCockpitData())->fromMetrics(collect(array_values($metricRows)));
            $displayed = count(array_filter($data['extra'], fn ($row) => $row['unit'] !== 'text')) + count(array_filter($data['v'], fn ($v) => $v !== null))
                + count(array_filter($data['p'], fn ($v) => $v !== null))
                + (int) ($data['minutes'] !== null) + (int) ($data['index'] !== null);
            self::assertSame($numericHeaders, $displayed, $row['Player']);
            $classified = ['assists', 'chances_successful', 'goals_by_head', 'free_kick_shots',
                'free_kick_goals', 'key_passes_accurate', 'dribbling_in_the_final_third_successful',
                'passes_accurate', 'long_passes_accurate', 'passes_forward_to_the_final_third_accurate',
                'super_long_passes', 'super_long_passes_accurate', 'tackles_successful', 'yellow_cards', 'red_cards'];
            $unclassified = array_diff(array_column(array_filter($data['extra'], fn ($item) => $item['unit'] !== 'text'), 'name'), $classified);
            self::assertSame([], array_values($unclassified), $row['Player']);
            $checked++;
        }
        fclose($handle);
        self::assertSame(24, $checked);
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
