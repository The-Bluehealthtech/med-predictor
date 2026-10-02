<?php

namespace App\Console\Commands;

use App\Services\Audit\Auditor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Seule voie de suppression de l'audit trail : purge des événements plus
 * anciens que la durée de conservation (config audit.retention_years).
 * Par défaut, simple simulation ; --force supprime, dans une transaction qui
 * lève la protection PostgreSQL pour cette seule purge, et journalise la purge.
 */
class AuditRetentionCommand extends Command
{
    protected $signature = 'audit:retention {--force : Supprime réellement (sinon simulation)}';

    protected $description = 'Purge les événements d\'audit plus anciens que la durée de conservation (audit.retention_years)';

    public function handle(Auditor $auditor): int
    {
        $years = (int) config('audit.retention_years');
        if ($years < 1) {
            $this->error('audit.retention_years doit valoir au moins 1 an.');

            return self::FAILURE;
        }
        $cutoff = now()->subYears($years)->startOfDay();
        $count = DB::table('audit_logs')->where('created_at', '<', $cutoff)->count();

        if (!$this->option('force')) {
            $this->info("Simulation : {$count} événement(s) antérieur(s) au {$cutoff->format('d/m/Y')} seraient supprimés (conservation : {$years} ans). Ajoutez --force pour purger.");

            return self::SUCCESS;
        }

        DB::transaction(function () use ($cutoff) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SET LOCAL fit.audit_retention_purge = 'on'");
            }
            DB::table('audit_logs')->where('created_at', '<', $cutoff)->delete();
        });
        $auditor->record(['event_type' => 'system', 'module' => 'system', 'action' => 'retention_purge', 'severity' => 'warning',
            'description' => "Purge de rétention : {$count} événement(s) antérieur(s) au {$cutoff->format('d/m/Y')}",
            'metadata' => ['deleted' => $count, 'cutoff' => $cutoff->toDateString(), 'retention_years' => $years]]);
        $this->info("{$count} événement(s) supprimé(s) ; la purge est journalisée.");

        return self::SUCCESS;
    }
}
