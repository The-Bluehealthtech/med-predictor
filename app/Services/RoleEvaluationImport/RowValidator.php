<?php

namespace App\Services\RoleEvaluationImport;

/**
 * Règles de validation du mandat (Livrable 2, RÈGLES + section LIVRABLE 2) :
 * - minutes entre 0 et 130
 * - taux entre 0 et 1
 * - réussites <= tentatives
 * - "Données non disponibles" -> NULL, jamais 0
 * - joueur et match connus (voir EntityResolver, pas ce validateur)
 *
 * Une instance est réutilisée pour tout un lot (les codes de poste valides
 * sont chargés une seule fois, pas requête par requête).
 */
class RowValidator
{
    public const KIND_COUNT = 'count';
    public const KIND_DECIMAL = 'decimal';
    public const KIND_MINUTES = 'minutes';
    public const KIND_RATE = 'rate';
    public const KIND_BOOLEAN = 'boolean';
    public const KIND_STRING = 'string';
    public const KIND_POSITION_CODE = 'position_code';
    public const KIND_LEGACY_POSITION_CODE = 'legacy_position_code';

    public const KINDS = [
        self::KIND_COUNT,
        self::KIND_DECIMAL,
        self::KIND_MINUTES,
        self::KIND_RATE,
        self::KIND_BOOLEAN,
        self::KIND_STRING,
        self::KIND_POSITION_CODE,
        self::KIND_LEGACY_POSITION_CODE,
    ];

    private const TRUE_TOKENS = ['1', 'true', 'oui', 'yes', 'vrai', 'titulaire', 'starter'];
    private const FALSE_TOKENS = ['0', 'false', 'non', 'no', 'faux', 'remplacant', 'remplaçant', 'sub', 'substitute'];

    /**
     * Vocabulaire RESTREINT existant sur player_match_detailed_stats.position_played
     * (contrainte CHECK déjà en base, antérieure à ce mandat) — DISTINCT du
     * nouveau référentiel à 12 codes de position_catalog utilisé par
     * match_participations.detailed_position. Les deux coexistent
     * volontairement (cf. Livrable 1, §9) : ne pas essayer d'écrire un code
     * detailed_position (LCB, RCAM...) dans position_played, la contrainte
     * CHECK de la base le refuserait.
     */
    private const LEGACY_POSITION_CODES = ['GK', 'CB', 'LB', 'RB', 'DM', 'CM', 'AM', 'LW', 'RW', 'ST'];

    /** @var string[] codes de position_catalog.code valides */
    private array $validPositionCodes;

    /**
     * @param string[] $validPositionCodes
     */
    public function __construct(array $validPositionCodes = [])
    {
        $this->validPositionCodes = $validPositionCodes;
    }

    /**
     * @return array{0:mixed,1:?string} [valeur_convertie_ou_null, raison_erreur_ou_null]
     */
    public function convertAndValidate(string $kind, mixed $value): array
    {
        if ($value === null) {
            return [null, null];
        }

        return match ($kind) {
            self::KIND_COUNT => $this->validateCount($value),
            self::KIND_DECIMAL => $this->validateDecimal($value),
            self::KIND_MINUTES => $this->validateMinutes($value),
            self::KIND_RATE => $this->validateRate($value),
            self::KIND_BOOLEAN => $this->validateBoolean($value),
            self::KIND_STRING => [trim((string) $value), null],
            self::KIND_POSITION_CODE => $this->validatePositionCode($value),
            self::KIND_LEGACY_POSITION_CODE => $this->validateLegacyPositionCode($value),
            default => [null, "kind inconnu : {$kind}"],
        };
    }

    private function validateCount(mixed $value): array
    {
        if (! is_numeric($value)) {
            return [null, "valeur non numérique attendue pour un comptage : '{$value}'"];
        }
        $n = (float) $value;
        if ($n < 0 || $n != (int) $n) {
            return [null, "comptage invalide (doit être un entier >= 0) : '{$value}'"];
        }

        return [(int) $n, null];
    }

    private function validateDecimal(mixed $value): array
    {
        if (! is_numeric($value)) {
            return [null, "valeur décimale invalide : '{$value}'"];
        }

        return [(float) $value, null];
    }

    private function validateMinutes(mixed $value): array
    {
        if (! is_numeric($value)) {
            return [null, "minute invalide (non numérique) : '{$value}'"];
        }
        $n = (float) $value;
        if ($n < 0 || $n > 130 || $n != (int) $n) {
            return [null, "minute hors intervalle [0,130] : '{$value}'"];
        }

        return [(int) $n, null];
    }

    private function validateRate(mixed $value): array
    {
        if (! is_numeric($value)) {
            return [null, "taux invalide (non numérique) : '{$value}'"];
        }
        $n = (float) $value;
        if ($n < 0 || $n > 1) {
            return [null, "taux hors intervalle [0,1] : '{$value}'"];
        }

        return [$n, null];
    }

    private function validateBoolean(mixed $value): array
    {
        $normalized = mb_strtolower(trim((string) $value));
        if (in_array($normalized, self::TRUE_TOKENS, true)) {
            return [true, null];
        }
        if (in_array($normalized, self::FALSE_TOKENS, true)) {
            return [false, null];
        }

        return [null, "booléen non reconnu : '{$value}'"];
    }

    private function validatePositionCode(mixed $value): array
    {
        $code = mb_strtoupper(trim((string) $value));
        if (! in_array($code, $this->validPositionCodes, true)) {
            return [null, "code de poste inconnu de position_catalog : '{$value}'"];
        }

        return [$code, null];
    }

    private function validateLegacyPositionCode(mixed $value): array
    {
        $code = mb_strtoupper(trim((string) $value));
        if (! in_array($code, self::LEGACY_POSITION_CODES, true)) {
            return [null, "code de poste (ancien référentiel) inconnu : '{$value}' (attendu : " . implode(', ', self::LEGACY_POSITION_CODES) . ')'];
        }

        return [$code, null];
    }

    /**
     * Vérifie une paire tentatives/réussites déjà convertie (entiers ou null).
     * Retourne une raison d'erreur, ou null si OK.
     */
    public function checkAttemptSuccessPair(string $attemptLabel, ?int $attempts, string $successLabel, ?int $successes): ?string
    {
        if ($attempts === null || $successes === null) {
            return null; // on ne peut pas comparer si l'un des deux est absent/NA
        }
        if ($successes > $attempts) {
            return "{$successLabel} ({$successes}) > {$attemptLabel} ({$attempts})";
        }

        return null;
    }
}
