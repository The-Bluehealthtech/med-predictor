<?php

namespace App\Console\Commands;

use App\Models\PerformanceMetric;
use App\Models\Player;
use App\Services\Fit\FitSnapshotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateFitSnapshots extends Command
{
    protected $signature = 'fit:snapshots
        {--days=30 : FIT metric lookback window in days}
        {--player= : Generate only for one player ID}
        {--strict : Return a failure exit code if any player fails}';

    protected $description = 'Generate idempotent FIT snapshots from verified performance metrics';

    public function handle(FitSnapshotService $fitSnapshotService): int
    {
        $days = filter_var(
            $this->option('days'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 365]]
        );

        if ($days === false) {
            $this->error('--days must be an integer between 1 and 365.');

            return self::FAILURE;
        }

        $playerOption = $this->option('player');

        if ($playerOption !== null
            && filter_var(
                $playerOption,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            ) === false
        ) {
            $this->error('--player must be a positive integer.');

            return self::FAILURE;
        }

        $snapshotAt = now();
        $windowStart = $snapshotAt->copy()->subDays($days);

        $playerIds = PerformanceMetric::query()
            ->verified()
            ->whereBetween('measurement_date', [$windowStart, $snapshotAt])
            ->when(
                $playerOption !== null,
                fn ($query) => $query->where('player_id', (int) $playerOption)
            )
            ->whereNotNull('player_id')
            ->distinct()
            ->orderBy('player_id')
            ->pluck('player_id');

        if ($playerIds->isEmpty()) {
            $this->info('No players with recent verified performance metrics.');

            return self::SUCCESS;
        }

        $created = 0;
        $unchanged = 0;
        $incomplete = 0;
        $missingPlayers = 0;
        $failed = 0;

        foreach ($playerIds as $playerId) {
            $player = Player::query()->find($playerId);

            if (!$player) {
                $missingPlayers++;

                $this->warn(
                    "Player {$playerId}: metric exists but player record is unavailable."
                );

                continue;
            }

            try {
                $result = $fitSnapshotService->createSnapshot(
                    $player,
                    $days,
                    $snapshotAt
                );

                $snapshot = $result['snapshot'];

                if (!$snapshot->is_complete || $snapshot->fit_score === null) {
                    $incomplete++;

                    $this->line(
                        "Player {$player->id}: incomplete — FIT score unavailable."
                    );

                    continue;
                }

                if ($result['created']) {
                    $created++;

                    $this->info(
                        "Player {$player->id}: FIT snapshot created ({$snapshot->fit_score}/100)."
                    );
                } else {
                    $unchanged++;

                    $this->line(
                        "Player {$player->id}: unchanged — existing snapshot reused."
                    );
                }
            } catch (\Throwable $exception) {
                $failed++;

                Log::error('FIT snapshot generation failed', [
                    'player_id' => $player->id,
                    'days' => $days,
                    'exception' => $exception,
                ]);

                $this->error(
                    "Player {$player->id}: {$exception->getMessage()}"
                );
            }
        }

        $this->newLine();
        $this->info('FIT snapshot generation completed.');
        $this->line("Created: {$created}");
        $this->line("Unchanged: {$unchanged}");
        $this->line("Incomplete: {$incomplete}");
        $this->line("Missing players: {$missingPlayers}");
        $this->line("Failed: {$failed}");

        if ($failed > 0) {
            $this->warn(
                "{$failed} player(s) could not be processed. See application logs."
            );
        }

        return $failed > 0 && $this->option('strict')
            ? self::FAILURE
            : self::SUCCESS;
    }
}
