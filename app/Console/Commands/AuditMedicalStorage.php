<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
final class AuditMedicalStorage extends Command
{
    protected $signature='medical:audit-storage';
    protected $description='Vérifier la couverture médicale de tous les joueurs, sans écrire ni exposer leurs données';
    public function handle(): int
    {
        $report=app(\App\Services\MedicalStorageAudit::class)->run();
        $this->line(json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR));
        return $report['missing_columns']?self::FAILURE:self::SUCCESS;
    }
}
