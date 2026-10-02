<?php

namespace App\Services\RoleEvaluationImport;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Orchestrateur du Livrable 2 : lit un fichier CSV source ligne par ligne,
 * applique le mapping (Livrable 2), valide (RowValidator), résout joueur/
 * équipe/match (EntityResolver), et écrit (ou simule en --dry-run) dans la
 * table cible, de façon rejouable sans doublon.
 *
 * Non exécuté par moi : aucun PHP disponible dans l'environnement où ce
 * travail a été produit. Voir docs/role-evaluation/03-implementation-
 * livrable-2.md pour la validation faite par ailleurs et les limites.
 */
class RoleEvaluationImporter
{
    private RowValidator $validator;
    private EntityResolver $resolver;
    public ImportReport $report;

    public function __construct(
        private ImportMapping $mapping,
        private bool $dryRun = false,
        private bool $isDemo = false
    ) {
        $validPositionCodes = DB::table('position_catalog')->pluck('code')->map(
            static fn ($c) => mb_strtoupper($c)
        )->all();

        $this->validator = new RowValidator($validPositionCodes);
        $this->resolver = new EntityResolver();
        $this->report = new ImportReport();
    }

    /**
     * @return int l'id du lot d'import créé (import_batches.id). En
     * --dry-run, l'ensemble de ce qui a été écrit (y compris le lot
     * lui-même) est annulé avant le retour : l'id retourné n'existe donc
     * plus en base, il ne sert qu'à l'affichage du rapport par la commande.
     */
    public function run(string $csvPath, ?string $sourceLabelOverride = null): int
    {
        if ($this->dryRun) {
            $batchId = null;
            try {
                DB::transaction(function () use ($csvPath, $sourceLabelOverride, &$batchId) {
                    $batchId = $this->runInternal($csvPath, $sourceLabelOverride);
                    throw new DryRunRollback($batchId);
                });
            } catch (DryRunRollback) {
                // Attendu : c'est uniquement ce qui annule l'écriture du dry-run.
            }

            return $batchId;
        }

        return $this->runInternal($csvPath, $sourceLabelOverride);
    }

    private function runInternal(string $csvPath, ?string $sourceLabelOverride): int
    {
        if (! is_file($csvPath)) {
            throw new RuntimeException("Fichier source introuvable : {$csvPath}");
        }
        if (! $this->mapping->csvHasHeader) {
            throw new RuntimeException(
                "Ce mapping déclare csv.has_header=false : non supporté par cette version de l'importeur "
                . '(limite documentée du Livrable 2, voir 03-implementation-livrable-2.md).'
            );
        }

        $batchId = DB::table('import_batches')->insertGetId([
            'batch_type' => 'import',
            'source_label' => $sourceLabelOverride ?? $this->mapping->sourceLabel,
            'status' => 'running',
            'params' => json_encode([
                'type' => $this->mapping->type,
                'csv_path_basename' => basename($csvPath),
                'dry_run' => $this->dryRun,
            ]),
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            throw new RuntimeException("Impossible d'ouvrir le fichier source : {$csvPath}");
        }

        $header = fgetcsv($handle, 0, $this->mapping->csvDelimiter);
        if ($header === false) {
            fclose($handle);
            throw new RuntimeException("Fichier source vide ou illisible : {$csvPath}");
        }
        $header = array_map('trim', $header);

        $rowNumber = 0;
        while (($fields = fgetcsv($handle, 0, $this->mapping->csvDelimiter)) !== false) {
            $rowNumber++;
            $this->report->recordRead();

            if (count($fields) !== count($header)) {
                $this->report->recordRejected($rowNumber, [
                    'nombre de colonnes incohérent avec l\'en-tête (' . count($fields) . ' vs ' . count($header) . ')',
                ]);
                continue;
            }

            $row = array_combine($header, $fields);
            $this->processRow($rowNumber, $row, $batchId);
        }
        fclose($handle);

        DB::table('import_batches')->where('id', $batchId)->update([
            'status' => 'completed',
            'finished_at' => now(),
            'rows_read' => $this->report->rowsRead,
            'rows_imported' => $this->report->rowsImported,
            'rows_rejected' => $this->report->rowsRejected(),
            'report' => json_encode($this->report->toArray()),
            'updated_at' => now(),
        ]);

        return $batchId;
    }

    private function processRow(int $rowNumber, array $row, int $batchId): void
    {
        $errors = [];

        // --- Résolution des entités (selon le type) ---
        $matchId = $playerId = $teamId = null;

        if (in_array($this->mapping->type, ['participations', 'player-match-stats', 'team-stats', 'events'], true)) {
            [$matchId, $err] = $this->resolver->resolveMatch($this->mapping->resolve, $row);
            if ($err) {
                $errors[] = "match : {$err}";
            }
        }

        if (in_array($this->mapping->type, ['participations', 'player-match-stats', 'events'], true)) {
            [$playerId, $err] = $this->resolver->resolvePlayer($this->mapping->resolve, $row);
            if ($err) {
                $errors[] = "joueur : {$err}";
            }
        }

        $isHome = null;
        if (in_array($this->mapping->type, ['participations', 'player-match-stats', 'team-stats'], true)) {
            if (isset($this->mapping->resolve['club'])) {
                // Approche recommandée (décision du 30/09) : équipe déduite du
                // club (FIFA Connect ID) + du match déjà résolu, jamais du nom
                // d'équipe (voir EntityResolver::resolveTeamForMatch).
                if ($matchId === null) {
                    $errors[] = 'équipe : résolution impossible, le match lui-même est introuvable';
                } else {
                    [$teamId, $isHome, $err] = $this->resolver->resolveTeamForMatch(
                        $matchId,
                        $this->mapping->resolve['club']['column'],
                        $row
                    );
                    if ($err) {
                        $errors[] = "équipe : {$err}";
                    }
                }
            } elseif (isset($this->mapping->resolve['team'])) {
                // Mode hérité (id direct ou nom d'équipe) : conservé pour un
                // fournisseur qui ne peut vraiment pas donner de FIFA Connect
                // ID club, mais déconseillé (voir docs/role-evaluation/
                // 03-implementation-livrable-2.md).
                [$teamId, $err] = $this->resolver->resolveTeam($this->mapping->resolve, $row);
                if ($err) {
                    $errors[] = "équipe : {$err}";
                }
            } else {
                $errors[] = "équipe : mapping incomplet (ni 'resolve.club' ni 'resolve.team')";
            }
        }

        $recipientPlayerId = null;
        if ($this->mapping->type === 'events' && isset($this->mapping->resolve['recipient_player'])) {
            [$rid, $err] = $this->resolver->resolvePlayer(
                ['player' => $this->mapping->resolve['recipient_player']],
                $row
            );
            // Le destinataire est optionnel : une erreur de résolution n'est
            // pas bloquante (toutes les lignes d'événements n'en ont pas).
            $recipientPlayerId = $err ? null : $rid;
        }

        // --- Conversion + validation des colonnes mappées ---
        $target = [];
        $convertedBySource = [];
        foreach ($this->mapping->columns as $col) {
            $raw = $row[$col['source']] ?? null;
            if ($this->mapping->isNotAvailable($raw)) {
                $target[$col['target']] = null;
                $convertedBySource[$col['source']] = null;
                continue;
            }

            [$value, $err] = $this->validator->convertAndValidate($col['kind'], $raw);
            if ($err) {
                $errors[] = "{$col['source']} -> {$col['target']} : {$err}";
                continue;
            }
            $target[$col['target']] = $value;
            $convertedBySource[$col['source']] = $value;
        }

        // --- Cohérence tentatives/réussites ---
        foreach ($this->mapping->attemptSuccessPairs as [$attemptSrc, $successSrc]) {
            $err = $this->validator->checkAttemptSuccessPair(
                $attemptSrc,
                $convertedBySource[$attemptSrc] ?? null,
                $successSrc,
                $convertedBySource[$successSrc] ?? null
            );
            if ($err) {
                $errors[] = $err;
            }
        }

        if (! empty($errors)) {
            $this->report->recordRejected($rowNumber, $errors);

            return;
        }

        $hash = hash('sha256', $this->mapping->type . '|' . $this->mapping->sourceLabel . '|' . implode('|', $fields = array_values($row)));

        $target['is_demo'] = $this->isDemo;
        $target['import_batch_id'] = $batchId;
        $target['source_row_hash'] = $hash;
        if ($matchId !== null) {
            $target['match_id'] = $matchId;
        }
        if ($playerId !== null) {
            $target['player_id'] = $playerId;
        }
        if ($teamId !== null) {
            $target['team_id'] = $teamId;
        }
        if ($this->mapping->type === 'events' && $recipientPlayerId !== null) {
            $target['recipient_player_id'] = $recipientPlayerId;
        }
        if ($this->mapping->type === 'team-stats' && $isHome !== null) {
            // is_home déduit de la résolution club->équipe plutôt que d'une
            // colonne source déclarative, pour éviter une incohérence entre
            // ce que dit la source et ce que confirme matches.home_club_id.
            $target['is_home'] = $isHome;
        }

        try {
            $this->upsert($target);
        } catch (\Throwable $e) {
            // Filet de sécurité : une erreur DB inattendue (non anticipée par
            // le validateur, ex. contrainte non couverte) rejette la ligne
            // au lieu de faire échouer tout le lot.
            $this->report->recordRejected($rowNumber, ["erreur d'écriture inattendue : " . $e->getMessage()]);

            return;
        }

        $this->report->recordImported();
    }

    private function upsert(array $values): void
    {
        $table = $this->mapping->table;

        $naturalKey = match ($this->mapping->type) {
            'participations' => ['match_id' => $values['match_id'], 'player_id' => $values['player_id']],
            'player-match-stats' => ['match_id' => $values['match_id'], 'player_id' => $values['player_id']],
            'team-stats' => ['match_id' => $values['match_id'], 'team_id' => $values['team_id']],
            'events' => ['match_id' => $values['match_id'], 'source_row_hash' => $values['source_row_hash']],
        };

        $existingId = DB::table($table)->where($naturalKey)->value('id');

        if ($existingId !== null) {
            $update = $values;
            unset($update['created_at']);
            $update['updated_at'] = now();
            DB::table($table)->where('id', $existingId)->update($update);
        } else {
            $insert = $values;
            $insert['created_at'] = now();
            $insert['updated_at'] = now();
            DB::table($table)->insert($insert);
        }
    }
}
