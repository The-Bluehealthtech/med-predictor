<?php

namespace App\Console\Commands;

use App\Models\PosturalAssessment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateLegacyPosturalDataCommand extends Command
{
    protected $signature = 'postural:migrate-legacy
        {--apply : Persist converted measurements. Without this option the command is dry-run only.}
        {--assessment= : Restrict to one postural assessment id}
        {--player= : Restrict to one player id}';

    protected $description = 'Convert legacy postural markers and angles into postural_measurements without deleting source data.';

    public function handle(): int
    {
        $query = PosturalAssessment::query()->orderBy('id');

        if ($assessmentId = $this->option('assessment')) {
            $query->whereKey($assessmentId);
        }

        if ($playerId = $this->option('player')) {
            $query->where('player_id', $playerId);
        }

        $apply = (bool) $this->option('apply');
        $converted = 0;
        $skipped = 0;

        $query->chunkById(100, function ($assessments) use ($apply, &$converted, &$skipped) {
            foreach ($assessments as $assessment) {
                $markers = (array) ($assessment->markers ?? []);
                $angles = (array) ($assessment->angles ?? []);

                if (!$markers && !$angles) {
                    continue;
                }

                $alreadyMigrated = $assessment->measurements()
                    ->get(['metadata'])
                    ->contains(function ($measurement) {
                        return (bool) data_get($measurement->metadata, 'legacy_migrated', false);
                    });

                if ($alreadyMigrated) {
                    $skipped++;
                    continue;
                }

                $rows = $this->convertRows($assessment, $markers, $angles);
                $converted += count($rows);

                if (!$apply) {
                    $this->line("Assessment {$assessment->id}: ".count($rows).' measurement(s) would be created.');
                    continue;
                }

                DB::transaction(function () use ($assessment, $rows) {
                    foreach ($rows as $row) {
                        $assessment->measurements()->create($row);
                    }
                });

                $this->info("Assessment {$assessment->id}: ".count($rows).' measurement(s) created.');
            }
        });

        $mode = $apply ? 'APPLY' : 'DRY-RUN';
        $this->newLine();
        $this->info("[$mode] Measurements: $converted; already migrated assessments skipped: $skipped.");

        return self::SUCCESS;
    }

    private function convertRows(PosturalAssessment $assessment, array $markers, array $angles): array
    {
        $rows = [];
        $view = in_array($assessment->view, ['anterior', 'posterior', 'left_lateral', 'right_lateral'], true)
            ? $assessment->view
            : ($assessment->view === 'lateral' ? 'right_lateral' : 'anterior');

        foreach ($markers as $index => $marker) {
            if (!isset($marker['x'], $marker['y'])) {
                continue;
            }

            $rows[] = [
                'view' => $view,
                'measurement_type' => 'marker',
                'measurement_key' => null,
                'value' => null,
                'unit' => null,
                'points' => [[
                    'x' => $this->normalize((float) $marker['x'], 600),
                    'y' => $this->normalize((float) $marker['y'], 800),
                ]],
                'metadata' => [
                    'legacy_migrated' => true,
                    'legacy_kind' => 'marker',
                    'legacy_index' => $index,
                    'legacy_canvas' => ['width' => 600, 'height' => 800],
                    'color' => $marker['color'] ?? null,
                    'timestamp' => $marker['timestamp'] ?? null,
                ],
            ];
        }

        foreach ($angles as $index => $angle) {
            $points = [];
            foreach ((array) ($angle['points'] ?? []) as $point) {
                if (!isset($point['x'], $point['y'])) {
                    continue 2;
                }

                $points[] = [
                    'x' => $this->normalize((float) $point['x'], 600),
                    'y' => $this->normalize((float) $point['y'], 800),
                ];
            }

            if (count($points) !== 3) {
                continue;
            }

            $rows[] = [
                'view' => $view,
                'measurement_type' => 'angle',
                'measurement_key' => null,
                'value' => isset($angle['angle']) ? round((float) $angle['angle'], 3) : null,
                'unit' => 'deg',
                'points' => $points,
                'metadata' => [
                    'legacy_migrated' => true,
                    'legacy_kind' => 'angle',
                    'legacy_index' => $index,
                    'legacy_canvas' => ['width' => 600, 'height' => 800],
                    'timestamp' => $angle['timestamp'] ?? null,
                ],
            ];
        }

        return $rows;
    }

    private function normalize(float $value, int $dimension): float
    {
        return round(max(0, min(1, $value / $dimension)), 6);
    }
}
