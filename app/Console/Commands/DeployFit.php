<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeployFit extends Command
{
    private const LOCK_NAME = 'med_predictor_fit_deploy';

    private const MIGRATIONS = [
        'database/migrations/2026_10_01_000003_normalize_pcma_type.php',
        'database/migrations/2026_10_01_120000_extend_postural_assessments_for_clinical_v1.php',
        'database/migrations/2026_10_01_120100_create_postural_findings_table.php',
        'database/migrations/2026_10_01_120200_create_postural_measurements_table.php',
        'database/migrations/2024_01_15_000006_create_tue_requests_table.php',
        'database/migrations/2026_10_01_000001_add_icd11_and_aut_to_health_records.php',
        'database/migrations/2026_10_01_000002_create_health_record_documents.php',
        'database/migrations/2026_10_02_000001_create_medical_imaging_workspace.php',
        'database/migrations/2026_09_30_110000_create_medication_catalogue_table.php',
        'database/migrations/2026_09_24_160000_create_fit_score_snapshots_table.php',
        'database/migrations/2026_09_24_170000_add_tenant_id_to_fit_score_snapshots_table.php',
        'database/migrations/2026_09_24_171000_add_input_signature_to_fit_score_snapshots_table.php',
        'database/migrations/2026_09_24_180000_add_verify_performance_metrics_permission.php',
        'database/migrations/2026_09_24_181000_add_record_performance_metrics_permission.php',
        'database/migrations/2026_10_02_090000_create_national_selections_tables.php',
        'database/migrations/2026_10_02_130000_add_performance_score_provenance.php',
        'database/migrations/2026_10_02_140000_create_external_entity_links_table.php',
        'database/migrations/2026_10_02_150000_create_club_officials_table.php',
        'database/migrations/2026_10_02_160000_create_passport_attestations_table.php',
        'database/migrations/2026_10_02_220000_allow_sourced_club_officials.php',
        'database/migrations/2026_10_02_223000_seed_current_saudi_club_officials.php',
        'database/migrations/2026_10_02_230000_repoint_lineups_and_rosters_to_matches.php',
        'database/migrations/2026_10_02_230100_repoint_player_detailed_stats_to_players.php',
        'database/migrations/2026_10_03_090000_create_platform_activities_table.php',
        'database/migrations/2026_10_03_100000_protect_audit_logs_append_only.php',
        'database/migrations/2026_10_03_110000_add_identity_check_to_player_licenses.php',
        'database/migrations/2026_10_03_120000_create_player_license_documents_and_events.php',
        'database/migrations/2026_10_03_130000_create_license_age_scale.php',
        'database/migrations/2026_10_03_140000_convert_demo_licenses_to_fifa_connect.php',
        'database/migrations/2026_10_03_150000_finish_demo_licenses_conversion.php',
        'database/migrations/2026_10_03_160000_create_license_integrity_reviews.php',
        'database/migrations/2026_10_03_170000_create_license_biometric_checks.php',
        'database/migrations/2026_10_03_180000_create_document_signature_requests.php',
        'database/migrations/2026_10_03_220000_add_request_reason_to_player_licenses.php',
        'database/migrations/2026_10_03_221000_add_fifa_connect_person_fields.php',
        'database/migrations/2026_10_03_222000_add_pcma_visit_type.php',
        'database/migrations/2026_10_03_223000_create_fhir_patient_links_table.php',
        'database/migrations/2026_10_03_224000_create_fhir_documents_table.php',
        'database/migrations/2026_10_03_225000_create_medical_files_table.php',
        'database/migrations/2026_10_03_226000_create_fhir_orders_table.php',
        'database/migrations/2026_10_03_227000_create_privacy_consent_tables.php',
        'database/migrations/2026_10_03_227100_add_storage_integrity_to_transfer_documents.php',
        'database/migrations/2026_10_03_227200_add_tms_workflow_to_transfers.php',
        'database/migrations/2026_10_03_227300_add_proof_workflow_to_transfer_payments.php',
        'database/migrations/2026_10_03_227400_create_match_medical_emergency_plans.php',
        'database/migrations/2026_10_03_228000_create_match_medical_incidents_table.php',
        'database/migrations/2026_10_03_229000_add_emergency_protocol_to_match_medical_incidents.php',
        'database/migrations/2026_10_03_229100_add_connect_context_to_match_medical_emergency_plans.php',
        'database/migrations/2026_10_04_090000_add_team_leader_connect_id_to_match_medical_emergency_plans.php',
        'database/migrations/2026_10_04_093000_add_matchday_medical_config_to_competitions_and_clubs.php',
    ];

    protected $signature = 'fit:deploy
        {--days=30 : FIT metric lookback window in days}
        {--schema-only : Apply required schema without imports or snapshots}
        {--refresh-only : Refresh data after successful schema preparation}
        {--demo-data : Populate repeatable demonstration fixtures}
        {--strict-snapshots : Fail if FIT snapshot generation reports an error}';

    protected $description = 'Apply FIT migrations and generate canonical snapshots safely';

    public function handle(): int
    {
        if ($this->option('schema-only') && $this->option('refresh-only')) {
            $this->error('Choose either schema-only or refresh-only.');
            return self::FAILURE;
        }

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

            if (!$this->option('refresh-only')) {
                $this->info('Applying required schema migrations...');
                $migrationExitCode = $this->call('migrate', [
                    '--force' => true,
                    '--path' => self::MIGRATIONS,
                ]);
                if ($migrationExitCode !== self::SUCCESS) {
                    $this->error('FIT migrations failed.');
                    return self::FAILURE;
                }

                $this->reportMigrationDrift();
            }
            if ($this->option('schema-only')) {
                $this->info('FIT schema preparation completed.');
                return self::SUCCESS;
            }

            // RxNorm est interrogé à la demande ; conserver seulement le catalogue historique existant.

            if ($this->option('demo-data')) {
                $seedExitCode = $this->call('db:seed', [
                    '--class' => \Database\Seeders\FitDemoFixturesSeeder::class,
                    '--force' => true,
                ]);
                if ($seedExitCode !== self::SUCCESS) {
                    $this->error('FIT demonstration fixtures failed.');
                    return self::FAILURE;
                }
                $ksaCountExitCode = $this->call('db:seed', [
                    '--class' => \Database\Seeders\KsaConfirmedMatchCountsSeeder::class,
                    '--force' => true,
                ]);
                if ($ksaCountExitCode !== self::SUCCESS) {
                    $this->error('Confirmed KSA match counts could not be imported.');
                    return self::FAILURE;
                }
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

    /**
     * Report migration files that exist in the repository but are not recorded
     * in the canonical database. This is intentionally read-only: FIT keeps an
     * explicit migration allowlist so an unexpected migration is never applied
     * to production automatically.
     */
    private function reportMigrationDrift(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('migrations')) {
            return;
        }

        $applied = DB::table('migrations')->pluck('migration')->all();
        $applied = array_fill_keys($applied, true);
        $pending = [];

        foreach (glob(database_path('migrations/*.php')) ?: [] as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if (!isset($applied[$name])) {
                $pending[] = $name;
            }
        }

        sort($pending);

        if ($pending === []) {
            $this->info('Migration drift check: no unapplied repository migrations.');
            return;
        }

        $this->warn(
            'Migration drift detected: '.count($pending).
            ' repository migration(s) are not applied to this database.'
        );

        foreach ($pending as $migration) {
            $this->line('  - '.$migration);
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
