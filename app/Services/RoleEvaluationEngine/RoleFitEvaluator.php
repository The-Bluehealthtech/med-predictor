<?php

namespace App\Services\RoleEvaluationEngine;

use App\Services\PerformanceScoreCalculator;

/**
 * Capacité « adéquation au rôle » — la seule partie du mandat qui ne
 * réutilise aucune logique préexistante (voir proposition, section
 * "Adéquation au rôle"). Compose app\Services\PerformanceScoreCalculator
 * (non modifié) plutôt que de dupliquer sa méthode statistique.
 *
 * Portée validée le 30/09 :
 *  - familles voisines uniquement (cfg['reference_fallback'], question 5) ;
 *  - une ligne player_role_evaluations par famille comparée (question 6) ;
 *  - role_fit_score = écart (delta) entre le score de la famille comparée
 *    et le score de la famille réellement jouée. 0 pour la ligne "famille
 *    jouée" elle-même ; positif si le joueur "marquerait mieux" jugé sur la
 *    famille voisine, négatif sinon. Formule validée mot pour mot le 30/09
 *    (réponse "écart ok").
 *
 * LIMITE CONNUE, documentée au rapport : pour une famille candidate,
 * l'unique moyen de réutiliser PerformanceScoreCalculator sans le modifier
 * est de lui donner une population contenant le joueur évalué ; ce joueur
 * entre donc dans le pool utilisé pour calculer la référence (médiane/MAD)
 * de la famille candidate, même s'il n'y contribue qu'une observation parmi
 * (au minimum) les min_reference_players (30) joueurs réels de cette
 * famille. Un vrai isolement demanderait de modifier
 * PerformanceScoreCalculator (rendre references() réutilisable), ce que
 * cette proposition s'est engagée à ne pas faire.
 */
final class RoleFitEvaluator
{
    private const SYNTHETIC_POSITION_CODE = '__role_fit_probe__';

    // Décision validée le 30/09 (question 7) : identifiant composite —
    // le composant propre à ce mandat ("role-fit-v1") + la version du
    // moteur réutilisé sans modification (config/player_performance_score.php
    // "version" => "1.1.0"), pour que toute ligne calculée reste traçable
    // jusqu'à la méthode exacte qui l'a produite.
    public const MODEL_VERSION = 'role-fit-v1+phase1-calculator@1.1.0';

    public function __construct(
        private readonly MatchStatsDataSource $dataSource,
        private readonly CalculatorConfigBuilder $configBuilder,
    ) {
    }

    /**
     * @param  int[]  $playerIds
     * @return array Lignes prêtes pour player_role_evaluations (sans
     *               role_config_version_id / model_version / is_demo /
     *               source_*_batch_id / computed_at, remplis par l'appelant).
     */
    public function evaluate(array $playerIds, int $roleConfigVersionId, bool $isDemo, ?array $matchIds = null): array
    {
        $cfg = $this->configBuilder->build($roleConfigVersionId);
        $cfg['version'] = self::MODEL_VERSION; // lu par PerformanceScoreCalculator::scorePlayer()
        $population = $this->dataSource->forPlayers($playerIds, $isDemo, $matchIds);

        if ($population === []) {
            return [];
        }

        $calculator = new PerformanceScoreCalculator($cfg);
        $playedByPlayer = [];
        foreach ($calculator->calculate($population) as $row) {
            $playedByPlayer[$row['player_id']] = $row;
        }

        $rows = [];
        foreach ($population as $playerEntry) {
            $playerId = $playerEntry['id'];
            $played = $playedByPlayer[$playerId] ?? null;

            if ($played === null || ($played['famille'] ?? null) === null) {
                continue; // poste détaillé inconnu ou aucune donnée : rien à écrire
            }

            $playedScore = $this->extractScore($played);
            if ($playedScore === null) {
                continue; // fiabilité insuffisante ou aucun indicateur exploitable : pas de ligne
            }

            $rows[] = $this->toRow($playerId, $played['famille'], $played, roleFitScore: 0.0);

            if ($played['famille'] === 'goalkeeper') {
                continue; // jamais de comparaison transversale pour les gardiens
            }

            $neighbors = array_filter(
                $cfg['reference_fallback'][$played['famille']] ?? [],
                fn ($family) => $family !== 'field'
            );

            foreach ($neighbors as $candidateFamily) {
                $alt = $this->scoreAgainstFamily($playerId, $playerEntry, $candidateFamily, $cfg, $population);
                $altScore = $alt !== null ? $this->extractScore($alt) : null;
                if ($altScore === null) {
                    continue;
                }
                $rows[] = $this->toRow($playerId, $candidateFamily, $alt, roleFitScore: round($altScore - $playedScore, 2));
            }
        }

        return $rows;
    }

    private function scoreAgainstFamily(int $playerId, array $playerEntry, string $candidateFamily, array $cfg, array $fullPopulation): ?array
    {
        $cfg['positions'][self::SYNTHETIC_POSITION_CODE] = $candidateFamily;

        $realFamilyPlayers = array_values(array_filter(
            $fullPopulation,
            fn ($p) => $p['id'] !== $playerId && $this->dominantFamily($p, $cfg) === $candidateFamily
        ));

        $probeEntry = $playerEntry;
        $probeEntry['matches'] = array_map(function (array $match) {
            $match['position'] = self::SYNTHETIC_POSITION_CODE;

            return $match;
        }, $playerEntry['matches']);

        $calculator = new PerformanceScoreCalculator($cfg);
        foreach ($calculator->calculate(array_merge($realFamilyPlayers, [$probeEntry])) as $row) {
            if ($row['player_id'] === $playerId) {
                return $row;
            }
        }

        return null;
    }

    private function dominantFamily(array $playerEntry, array $cfg): ?string
    {
        $minutesByFamily = [];
        foreach ($playerEntry['matches'] as $match) {
            $family = $cfg['positions'][$match['position']] ?? null;
            if ($family !== null) {
                $minutesByFamily[$family] = ($minutesByFamily[$family] ?? 0) + (float) ($match['minutes'] ?? 0);
            }
        }

        if ($minutesByFamily === []) {
            return null;
        }

        arsort($minutesByFamily);

        return array_key_first($minutesByFamily);
    }

    private function extractScore(array $result): ?float
    {
        $score = $result['score_corrige'] ?? null;

        return $score === null ? null : (float) $score;
    }

    private function toRow(int $playerId, string $englishFamily, array $result, float $roleFitScore): array
    {
        return [
            'player_id' => $playerId,
            // position_family_evaluated reste en français en base (aligné
            // sur position_catalog.family, Livrable 1) ; seul le calcul
            // interne (voir PositionFamilyTranslator) travaille en anglais.
            'position_family_evaluated' => PositionFamilyTranslator::toFrench($englishFamily),
            'score' => $result['score_corrige'],
            'reliability' => isset($result['fiabilite']) ? round($result['fiabilite'] / 100, 4) : null,
            'interval_low' => $result['intervalle_80'][0] ?? null,
            'interval_high' => $result['intervalle_80'][1] ?? null,
            'role_fit_score' => $roleFitScore,
        ];
    }
}
