<?php

namespace App\Console\Commands;

use App\Services\RoleEvaluationImport\ImportMapping;
use App\Services\RoleEvaluationImport\RoleEvaluationImporter;
use Illuminate\Console\Command;
use Throwable;

/**
 * Livrable 2 du mandat "fondation de données rôle et apport / cockpit" :
 * une commande d'import générique, pilotée par un fichier de correspondance
 * (mapping) par source et par type de données.
 *
 * Exemples :
 *   php artisan role-eval:import participations donnees.csv --mapping=docs/role-evaluation/livrable-2-importer/example-mapping-participations.json
 *   php artisan role-eval:import player-match-stats donnees.csv --mapping=... --dry-run
 *
 * Voir docs/role-evaluation/03-implementation-livrable-2.md pour le détail,
 * les hypothèses, et ce qui n'a pas pu être vérifié (aucun PHP disponible
 * dans l'environnement où cette commande a été écrite).
 */
class ImportRoleEvaluationDataCommand extends Command
{
    protected $signature = 'role-eval:import
        {type : participations|player-match-stats|team-stats|events}
        {file : chemin vers le fichier CSV source}
        {--mapping= : chemin vers le fichier de correspondance JSON (obligatoire)}
        {--source= : libellé de la source, remplace celui du mapping si fourni}
        {--dry-run : valide et produit le rapport sans écrire en base}';

    protected $description = "Importe des données de match (participations, stats, événements) depuis un fichier source, selon un mapping déclaratif";

    public function handle(): int
    {
        $type = $this->argument('type');
        if (! in_array($type, ImportMapping::TYPES, true)) {
            $this->error("Type inconnu : '{$type}'. Attendu : " . implode(', ', ImportMapping::TYPES));

            return self::FAILURE;
        }

        $mappingPath = $this->option('mapping');
        if (! $mappingPath) {
            $this->error('--mapping=<fichier.json> est obligatoire.');

            return self::FAILURE;
        }

        $file = $this->argument('file');
        $dryRun = (bool) $this->option('dry-run');

        try {
            $mapping = ImportMapping::fromFile($mappingPath);
        } catch (Throwable $e) {
            $this->error("Mapping invalide : {$e->getMessage()}");

            return self::FAILURE;
        }

        if ($mapping->type !== $type) {
            $this->error("Le mapping déclare le type '{$mapping->type}', différent du type demandé '{$type}'.");

            return self::FAILURE;
        }

        $this->info(($dryRun ? '[DRY-RUN] ' : '') . "Import '{$type}' depuis {$file}, mapping {$mappingPath}...");

        $importer = new RoleEvaluationImporter($mapping, $dryRun);

        try {
            $batchId = $importer->run($file, $this->option('source'));
        } catch (Throwable $e) {
            $this->error("Échec de l'import : {$e->getMessage()}");

            return self::FAILURE;
        }

        $suffix = $dryRun ? ' (aucune écriture conservée, dry-run)' : '';
        $this->info("Lot import_batches#{$batchId} — " . $importer->report->summaryLine() . $suffix);
        $this->printRejections($importer);

        return self::SUCCESS;
    }

    private function printRejections(RoleEvaluationImporter $importer): void
    {
        if ($importer->report->rowsRejected() === 0) {
            return;
        }

        $this->warn('Lignes rejetées :');
        foreach ($importer->report->rejections as $rejection) {
            $this->line("  ligne {$rejection['row_number']} : " . implode(' ; ', $rejection['reasons']));
        }
    }
}
