<?php

namespace App\Console\Commands;

use App\Services\ExternalClubImport\ExternalClubImporter;
use Illuminate\Console\Command;

class ImportExternalClub extends Command
{
    protected $signature = 'clubs:import-external
        {url : URL de la page effectif du club}
        {--commit : Écrit réellement en base}
        {--no-media : Ne télécharge pas logo et portraits}';

    protected $description = 'Prévisualise ou importe un club et son effectif depuis une source externe supportée.';

    public function handle(ExternalClubImporter $importer): int
    {
        $url = (string) $this->argument('url');
        $commit = (bool) $this->option('commit');
        $downloadMedia = ! (bool) $this->option('no-media');

        if (! str_contains($url, 'footmercato.net/club/')) {
            $this->error('Source non supportée. Fournir une URL Foot Mercato de club.');
            return self::FAILURE;
        }
        if (! $commit) {
            $preview = $importer->preview($url);
            $data = $preview['data'];
            $club = $data['club'];

            $this->info('DRY-RUN : aucune écriture en base.');
            $this->newLine();
            $this->line('Club source : '.($club['name'] ?? 'inconnu'));
            $this->line('Alias : '.($club['short_name'] ?? '-'));
            $this->line('Saison : '.($data['season'] ?? '-'));
            $this->line('Coach : '.($club['coach']['name'] ?? '-'));
            $this->line('Logo : '.($club['logo_url'] ?? '-'));
            $this->line('Club FIT correspondant : '.($preview['club_match']['name'] ?? 'aucun'));

            $summary = $preview['summary'];
            $this->table(
                ['Joueurs', 'Matchés', 'Nouveaux', 'Photos', 'DOB', 'Nationalités', 'Tailles', 'Poids'],
                [[
                    $summary['players_found'],
                    $summary['players_matched'],
                    $summary['players_new'],
                    $summary['photos_found'],
                    $summary['dob_found'],
                    $summary['nationalities_found'],
                    $summary['height_found'],
                    $summary['weight_found'],
                ]]
            );
            $this->table(
                ['#', 'Joueur', 'Poste', 'Âge', 'FIT', 'Photo'],
                collect($data['players'])->map(function (array $player) use ($preview) {
                    $match = collect($preview['player_matches'])
                        ->firstWhere('external_id', $player['external_id']);

                    return [
                        $player['jersey_number'] ?? '-',
                        $player['name'],
                        $player['position'] ?? '-',
                        $player['age'] ?? '-',
                        $match['local_id'] ?? 'NOUVEAU',
                        $player['photo_url'] ? 'oui' : 'non',
                    ];
                })->all()
            );

            $this->newLine();
            $this->comment('Relancer avec --commit pour importer. Ajouter --no-media pour ne pas télécharger les images.');
            return self::SUCCESS;
        }

        $result = $importer->import($url, $downloadMedia);
        $this->info('Import terminé.');
        $this->table(
            ['Club FIT', 'ID', 'Joueurs créés', 'Joueurs mis à jour', 'Total'],
            [[
                $result['club_name'],
                $result['club_id'],
                $result['players_created'],
                $result['players_updated'],
                $result['players_total'],
            ]]
        );

        return self::SUCCESS;
    }
}
