<?php

namespace App\Console\Commands;

use App\Services\ExternalClubImport\ExternalClubImporter;
use App\Services\ExternalClubImport\Providers\FootMercatoProvider;
use Illuminate\Console\Command;

class ImportSaudiProLeague extends Command
{
    protected $signature = 'clubs:import-saudi-pro-league
        {--commit : Importe réellement les clubs}
        {--no-media : Ne télécharge pas les médias}
        {--limit= : Limite le nombre de clubs traités}
        {--only= : Slug Foot Mercato d’un seul club}';

    protected $description = 'Découvre et prévisualise ou importe les clubs de Saudi Pro League depuis Foot Mercato.';

    private const CLUBS_URL = 'https://www.footmercato.net/arabie-saoudite/saudi-pro-league/club';

    public function handle(FootMercatoProvider $provider, ExternalClubImporter $importer): int
    {
        $clubs = collect($provider->fetchCompetitionClubs(self::CLUBS_URL));

        if ($only = $this->option('only')) {
            $clubs = $clubs->where('external_id', $only);
        }

        if ($limit = (int) $this->option('limit')) {
            $clubs = $clubs->take($limit);
        }

        if ($clubs->isEmpty()) {
            $this->error('Aucun club trouvé avec les filtres demandés.');
            return self::FAILURE;
        }
        $this->info(sprintf(
            '%d club(s) SPL détecté(s). Mode : %s',
            $clubs->count(),
            $this->option('commit') ? 'IMPORT' : 'DRY-RUN'
        ));

        $results = [];
        foreach ($clubs as $club) {
            $this->newLine();
            $this->line('→ '.$club['name'].' ['.$club['external_id'].']');

            try {
                if ($this->option('commit')) {
                    $result = $importer->import(
                        $club['squad_url'],
                        ! (bool) $this->option('no-media')
                    );

                    $results[] = [
                        $club['name'],
                        'importé',
                        $result['players_total'],
                        $result['players_created'],
                        $result['players_updated'],
                    ];
                    continue;
                }

                $preview = $importer->preview($club['squad_url']);
                $summary = $preview['summary'];
                $results[] = [
                    $club['name'],
                    $preview['club_match']['name'] ?? 'nouveau',
                    $summary['players_found'],
                    $summary['players_new'],
                    $summary['photos_found'],
                ];
            } catch (\Throwable $e) {
                $this->warn('  Échec : '.$e->getMessage());
                $results[] = [$club['name'], 'ERREUR', '-', '-', '-'];
            }
        }
        $this->newLine();
        if ($this->option('commit')) {
            $this->table(
                ['Club', 'Statut', 'Effectif', 'Créés', 'Mis à jour'],
                $results
            );
        } else {
            $this->table(
                ['Club', 'FIT', 'Effectif', 'Nouveaux', 'Photos'],
                $results
            );
            $this->comment('Aucune écriture. Relancer avec --commit pour importer.');
        }

        return self::SUCCESS;
    }
}
