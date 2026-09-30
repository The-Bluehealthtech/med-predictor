<?php

namespace App\Services\RoleEvaluationEngine;

use App\Models\PositionCatalog;
use App\Models\RoleConfigWeight;

/**
 * Construit le tableau $cfg injecté au constructeur de
 * app\Services\PerformanceScoreCalculator (non modifié), à partir de :
 *  - la structure fixe de config/role_evaluation_engine.php (dimensions,
 *    indicateurs, hyperparamètres — identique pour toutes les versions) ;
 *  - position_catalog (code -> famille, Livrable 1, §9) pour cfg['positions'] ;
 *  - role_config_weights, pour UNE version donnée, pour cfg['weights'].
 *
 * Échoue explicitement (RuntimeException) si un poids est manquant pour une
 * famille/dimension de la version demandée : jamais de valeur par défaut
 * silencieuse pour un poids, conformément à la règle du mandat (les poids
 * n'existent que par un réglage explicite, jamais improvisés par le code).
 */
final class CalculatorConfigBuilder
{
    public function build(int $roleConfigVersionId): array
    {
        $cfg = config('role_evaluation_engine');

        // cfg['positions'] : code détaillé (position_catalog.code) -> famille
        // ANGLAISE interne (voir PositionFamilyTranslator — le calcul reste
        // en anglais, comme la phase 1 ; seule la base reste en français).
        $cfg['positions'] = [];
        foreach (PositionCatalog::query()->pluck('family', 'code') as $code => $frenchFamily) {
            $cfg['positions'][$code] = PositionFamilyTranslator::toEnglish($frenchFamily);
        }

        $weightsByFamily = [];
        foreach (RoleConfigWeight::where('role_config_version_id', $roleConfigVersionId)->get() as $row) {
            $englishFamily = PositionFamilyTranslator::toEnglish($row->position_family);
            $weightsByFamily[$englishFamily][$row->dimension_key] = (float) $row->weight;
        }

        $cfg['weights'] = [];
        foreach (array_unique(array_values($cfg['positions'])) as $family) {
            $familyType = $family === 'goalkeeper' ? 'goalkeeper' : 'field';
            $dimensionOrder = array_keys($cfg['dimensions'][$familyType]);

            $ordered = [];
            foreach ($dimensionOrder as $dimensionKey) {
                if (!isset($weightsByFamily[$family][$dimensionKey])) {
                    $frenchFamily = PositionFamilyTranslator::toFrench($family);
                    throw new \RuntimeException(
                        "Poids manquant pour la famille \"{$frenchFamily}\", dimension \"{$dimensionKey}\", ".
                        "version de configuration #{$roleConfigVersionId}. Aucun calcul ne peut être produit ".
                        "tant que role_config_weights ne couvre pas toutes les dimensions de toutes les familles ".
                        'pour cette version.'
                    );
                }
                $ordered[] = $weightsByFamily[$family][$dimensionKey];
            }
            $cfg['weights'][$family] = $ordered;
        }

        return $cfg;
    }
}
