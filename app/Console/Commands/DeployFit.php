<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeployFit extends Command
{
    private const LOCK_NAME = 'med_predictor_fit_deploy';

    private const MIGRATIONS = [
        'database/migrations/2026_09_24_160000_create_fit_score_snapshots_table.php',
        'database/migrations/2026_09_24_170000_add_tenant_id_to_fit_score_snapshots_table.php',
        'database/migrations/2026_09_24_171000_add_input_signature_to_fit_score_snapshots_table.php',
        'database/migrations/2026_09_24_180000_add_verify_performance_metrics_permission.php',
    ];

    protected $signature = 'fit:deploy
        {--days=30 : FIT metric lookback window in days}
        {--strict-snapshots : Fail if FIT snapshot generation reports an error}';

    protected $description = 'Apply FIT migrations and generate canonical snapshots safely';

    public function handle(): int
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

        $driver = DB::connection()->getDriverName();

        if (!$this->acquireLock($driver)) {
            $this->error(
                "Could not acquire FIT deployment lock for database driver: {$driver}"
            );

            return self::FAILURE;
        }

        try {
            $this->info("FIT deployment lock acquired ({$driver}).");

            $migrationExitCode = $this->call('migrate', [
                '--force' => true,
                '--path' => self::MIGRATIONS,
            ]);

            if ($migrationExitCode !== self::SUCCESS) {
                $this->error('FIT migrations failed.');

                return self::FAILURE;
            }

            $snapshotArguments = [
                '--days' => (string) $days,
            ];

            if ($this->option('strict-snapshots')) {
                $snapshotArguments['--strict'] = true;
            }

            $snapshotExitCode = $this->call(
                'fit:snapshots',
                $snapshotArguments
            );

            if ($snapshotExitCode !== self::SUCCESS) {
                $this->error('FIT snapshot generation failed.');

                return self::FAILURE;
            }

            $this->info('FIT deployment preparation completed.');

            return self::SUCCESS;
        } finally {
            $this->releaseLock($driver);
        }
    }

    private function acquireLock(string $driver): bool
    {
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $result = DB::selectOne(
                'SELECT GET_LOCK(?, 120) AS acquired',
                [self::LOCK_NAME]
            );

            return (int) ($result->acquired ?? 0) === 1;
        }

        if ($driver === 'pgsql') {
            for ($attempt = 0; $attempt < 120; $attempt++) {
                $result = DB::selectOne(
                    'SELECT pg_try_advisory_lock(hashtext(?)) AS acquired',
                    [self::LOCK_NAME]
                );

                if ((bool) ($result->acquired ?? false)) {
                    return true;
                }

                sleep(1);
            }

            return false;
        }

        // Local development/tests use SQLite and do not have
        // multiple Render containers competing for deployment.
        if ($driver === 'sqlite') {
            return true;
        }

        return false;
    }

    private function releaseLock(string $driver): void
    {
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::selectOne(
                'SELECT RELEASE_LOCK(?) AS released',
                [self::LOCK_NAME]
            );

            return;
        }

        if ($driver === 'pgsql') {
            DB::selectOne(
                'SELECT pg_advisory_unlock(hashtext(?)) AS released',
                [self::LOCK_NAME]
            );
        }
    }
}
