<?php

namespace App\Services\RoleEvaluationEngine;

/**
 * DÉCOUVERTE (exécution réelle, 30/09) : contrairement à ce que la
 * proposition affirmait ("le moteur existant est agnostique au nom des
 * clés"), app\Services\PerformanceScoreCalculator compare la chaîne
 * littérale anglaise "goalkeeper" à cinq endroits de son code (non modifié)
 * pour son traitement spécifique du gardien (exclusion du pool de
 * référence des joueurs de champ, sélection du jeu de dimensions à 4
 * plutôt qu'à 7). Utiliser "gardien" (français) comme clé de famille cassait
 * silencieusement cette logique : tous les postes, gardien compris,
 * tombaient dans la branche "field" à 7 dimensions, provoquant une erreur
 * "Undefined array key" dès qu'un gardien était évalué (7 dimensions
 * attendues, seulement 4 poids configurés pour "gardien").
 *
 * Corrigé par une traduction aux deux bords, sans modifier
 * PerformanceScoreCalculator.php : le calcul interne (CalculatorConfigBuilder,
 * RoleFitEvaluator) utilise les 8 clés anglaises de
 * config/player_performance_score.php ; position_catalog.family et
 * player_role_evaluations.position_family_evaluated / role_config_weights.
 * position_family restent en français, comme prévu au Livrable 1. La
 * correspondance un-à-un entre les 8 familles reste exacte (voir
 * proposition, section "Vocabulaire des dimensions") ; seul le fait que le
 * moteur soit "agnostique" au nom exact de la famille "gardien" était faux.
 */
final class PositionFamilyTranslator
{
    private const FRENCH_TO_ENGLISH = [
        'défenseur central' => 'central',
        'latéral' => 'lateral',
        'milieu défensif' => 'defensive_midfield',
        'milieu relayeur' => 'central_midfield',
        'milieu offensif' => 'attacking_midfield',
        'ailier' => 'winger',
        'avant-centre' => 'striker',
        'gardien' => 'goalkeeper',
    ];

    public static function toEnglish(string $frenchFamily): string
    {
        return self::FRENCH_TO_ENGLISH[$frenchFamily]
            ?? throw new \InvalidArgumentException("Famille position_catalog inconnue : \"{$frenchFamily}\".");
    }

    public static function toFrench(string $englishFamily): string
    {
        static $flipped = null;
        $flipped ??= array_flip(self::FRENCH_TO_ENGLISH);

        return $flipped[$englishFamily]
            ?? throw new \InvalidArgumentException("Famille interne (moteur) inconnue : \"{$englishFamily}\".");
    }
}
