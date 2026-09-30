<?php

namespace App\Services\RoleEvaluationImport;

use RuntimeException;

/**
 * Charge et valide le fichier de correspondance JSON entre les champs du
 * fournisseur et les colonnes cibles (Livrable 2 du mandat "role-eval").
 *
 * Un fichier de mapping décrit UNE combinaison (type de données x source).
 * Voir docs/role-evaluation/livrable-2-importer/example-mapping-*.json
 * pour des exemples complets et commentés.
 */
class ImportMapping
{
    public const TYPES = [
        'participations',
        'player-match-stats',
        'team-stats',
        'events',
    ];

    public const TARGET_TABLE = [
        'participations' => 'match_participations',
        'player-match-stats' => 'player_match_detailed_stats',
        'team-stats' => 'match_team_stats',
        'events' => 'match_events',
    ];

    /** Jetons reconnus comme "Données non disponibles" -> NULL (jamais 0). */
    public const DEFAULT_NOT_AVAILABLE_TOKENS = [
        'données non disponibles',
        'donnees non disponibles',
        'data not available',
        'n/a',
        'na',
        '',
    ];

    public string $type;
    public string $table;
    public string $sourceLabel;
    public string $csvDelimiter;
    public bool $csvHasHeader;
    /** @var string[] */
    public array $notAvailableTokens;
    /** @var array<string,array> */
    public array $resolve;
    /** @var array<int,array{source:string,target:string,kind:string}> */
    public array $columns;
    /** @var array<int,array{0:string,1:string}> paires [colonne_tentatives, colonne_reussites] (noms SOURCE) */
    public array $attemptSuccessPairs;

    public static function fromFile(string $path): self
    {
        if (! is_file($path)) {
            throw new RuntimeException("Fichier de correspondance introuvable : {$path}");
        }

        $raw = file_get_contents($path);
        $data = json_decode($raw, true);

        if (! is_array($data)) {
            throw new RuntimeException("Fichier de correspondance JSON invalide : {$path}");
        }

        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        $mapping = new self();

        $mapping->type = $data['type'] ?? '';
        if (! in_array($mapping->type, self::TYPES, true)) {
            throw new RuntimeException(
                "Champ 'type' invalide dans le mapping (" . ($mapping->type ?: '<absent>') . "). "
                . 'Attendu : ' . implode(', ', self::TYPES)
            );
        }

        $mapping->table = self::TARGET_TABLE[$mapping->type];
        $mapping->sourceLabel = $data['source_label'] ?? 'source-inconnue';

        $csv = $data['csv'] ?? [];
        $mapping->csvDelimiter = $csv['delimiter'] ?? ',';
        $mapping->csvHasHeader = (bool) ($csv['has_header'] ?? true);

        $tokens = $data['not_available_tokens'] ?? self::DEFAULT_NOT_AVAILABLE_TOKENS;
        $mapping->notAvailableTokens = array_map(
            static fn ($t) => mb_strtolower(trim((string) $t)),
            $tokens
        );

        $mapping->resolve = $data['resolve'] ?? [];

        $columns = $data['columns'] ?? [];
        if (empty($columns)) {
            throw new RuntimeException("Le mapping ne définit aucune colonne ('columns' vide ou absent).");
        }
        foreach ($columns as $i => $col) {
            foreach (['source', 'target', 'kind'] as $required) {
                if (empty($col[$required])) {
                    throw new RuntimeException("Colonne #{$i} du mapping : champ '{$required}' manquant.");
                }
            }
            if (! in_array($col['kind'], RowValidator::KINDS, true)) {
                throw new RuntimeException("Colonne #{$i} ('{$col['source']}') : kind '{$col['kind']}' inconnu.");
            }
        }
        $mapping->columns = $columns;

        $mapping->attemptSuccessPairs = $data['attempt_success_pairs'] ?? [];

        return $mapping;
    }

    public function isNotAvailable(?string $rawValue): bool
    {
        if ($rawValue === null) {
            return true;
        }

        return in_array(mb_strtolower(trim($rawValue)), $this->notAvailableTokens, true);
    }
}
