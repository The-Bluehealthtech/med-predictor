<?php

namespace App\Console\Commands;

use App\Services\RoleEvaluationDemo\DemoDataGenerator;
use Illuminate\Console\Command;

/**
 * Livrable 3 : `php artisan role-eval:generate-demo`.
 *
 * NE SUPPRIME NI NE TOUCHE JAMAIS le jeu de démonstration actuel (49 joueurs
 * sur 10 matchs, club CS Sfaxien) : cette commande ne fait qu'AJOUTER de
 * nouveaux clubs/équipes/joueurs/matchs fictifs, tous marqués is_demo=true
 * (mandat, LIVRABLE 3 : "remplace le jeu actuel ... SANS le supprimer avant
 * mon accord"). Une éventuelle purge de l'ancien jeu reste une action
 * séparée, décidée par vous, jamais automatique ici.
 */
class GenerateDemoDataCommand extends Command
{
    protected $signature = 'role-eval:generate-demo
        {--seed=42 : Graine de reproductibilité}
        {--clubs=16 : Nombre de clubs démo à créer (pair, >= 4 ; 16 donne exactement 30 matchs/équipe)}
        {--dry-run : Simule sans rien écrire en base (transaction annulée)}';

    protected $description = "Génère un jeu de données de démonstration réaliste pour l'évaluation rôle et apport (Livrable 3), sans toucher au jeu existant.";

    public function handle(): int
    {
        $seed = (int) $this->option('seed');
        $clubs = (int) $this->option('clubs');
        $dryRun = (bool) $this->option('dry-run');

        if ($clubs < 4 || $clubs % 2 !== 0) {
            $this->error("--clubs doit être pair et >= 4 (double round-robin), reçu : {$clubs}");

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Génération démo — seed=%d, clubs=%d (%d matchs/équipe attendus)%s',
            $seed,
            $clubs,
            2 * ($clubs - 1),
            $dryRun ? ' [DRY-RUN, rien ne sera écrit]' : ''
        ));

        try {
            $generator = new DemoDataGenerator($seed, $clubs, $dryRun);
            $result = $generator->run();
        } catch (\Throwable $e) {
            $this->error('Échec de la génération : '.$e->getMessage());

            return self::FAILURE;
        }

        $report = $result['report'];
        $this->info('--- Rapport ---');
        foreach ($report as $key => $value) {
            if ($key === 'degraded_cases') {
                continue;
            }
            $this->line("  {$key} : {$value}");
        }
        $this->info('Cas dégradés :');
        foreach ($report['degraded_cases'] as $line) {
            $this->line('  - '.$line);
        }

        if ($dryRun) {
            $this->comment('Dry-run : transaction annulée, rien n\'a été écrit en base.');
        } else {
            $this->info("Lot d'import (import_batches.id) : {$result['batch_id']}");
        }

        return self::SUCCESS;
    }
}
