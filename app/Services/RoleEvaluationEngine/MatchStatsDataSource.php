<?php

namespace App\Services\RoleEvaluationEngine;

use Illuminate\Support\Facades\DB;

/**
 * Adaptateur de données : match_participations + player_match_detailed_stats
 * + matches (tables du Livrable 1) -> format d'entrée attendu par
 * app\Services\PerformanceScoreCalculator::calculate() (non modifié).
 *
 * Rôle strictement analogue à PerformanceScoreDataSource (moteur phase 1),
 * mais sur les tables match par match de ce mandat plutôt que sur
 * performances/external_player_performance_metrics/player_season_stats.
 *
 * Ne fonctionne qu'en mode "précis" (voir config/role_evaluation_engine.php) :
 * ne produit jamais d'entrée "average", uniquement des joueurs avec au
 * moins un match détaillé. Un joueur sans aucune ligne correspondante est
 * simplement absent du résultat (pas d'entrée vide).
 *
 * Filtrage is_demo strict et obligatoire : une exécution du moteur porte
 * sur des données démo OU réelles, jamais un mélange, pour que le is_demo
 * unique écrit sur player_role_evaluations reste toujours exact.
 */
final class MatchStatsDataSource
{
    /**
     * @param  int[]  $playerIds  Joueurs à charger (players.id).
     * @param  bool  $isDemo  Ne charge que les lignes dont is_demo correspond exactement.
     * @param  int[]|null  $matchIds  Restreint aux matchs listés, sinon tout l'historique disponible.
     * @return array Tableau au format attendu par PerformanceScoreCalculator::calculate() :
     *               [['id' => int, 'position' => string|null, 'matches' => [['date','match_id','position','minutes','stats'], ...]], ...]
     */
    public function forPlayers(array $playerIds, bool $isDemo, ?array $matchIds = null): array
    {
        if ($playerIds === []) {
            return [];
        }

        $query = DB::table('match_participations as mp')
            ->join('player_match_detailed_stats as s', function ($join) {
                $join->on('s.match_id', '=', 'mp.match_id')
                    ->on('s.player_id', '=', 'mp.player_id');
            })
            ->join('matches as m', 'm.id', '=', 'mp.match_id')
            ->whereIn('mp.player_id', $playerIds)
            ->where('mp.is_demo', $isDemo)
            ->where('s.is_demo', $isDemo)
            ->select('mp.player_id', 'mp.match_id', 'mp.detailed_position', 'm.match_date', 's.*');

        if ($matchIds !== null) {
            $query->whereIn('mp.match_id', $matchIds);
        }

        $rows = $query->orderBy('m.match_date')->orderBy('mp.match_id')->get();

        $byPlayer = [];
        foreach ($rows as $row) {
            $row = (array) $row;
            $playerId = (int) $row['player_id'];
            $stats = $row;
            // Colonnes de jointure/clé, pas des statistiques : retirées du
            // tableau "stats" pour ne garder que les indicateurs bruts.
            foreach (['player_id', 'match_id', 'detailed_position', 'match_date', 'id', 'team_id', 'competition_id', 'season_id', 'created_at', 'updated_at', 'is_demo', 'import_batch_id'] as $key) {
                unset($stats[$key]);
            }

            $byPlayer[$playerId]['id'] = $playerId;
            $byPlayer[$playerId]['matches'][] = [
                'date' => $row['match_date'],
                'match_id' => (int) $row['match_id'],
                // Poste JOUÉ CE MATCH (position_catalog.code, 12 codes détaillés),
                // pas players.position (générique GK/DEF/MID/FWD) : c'est ce que
                // le moteur a besoin pour dériver la famille via cfg['positions'].
                'position' => $row['detailed_position'],
                'minutes' => $stats['minutes_played'] ?? null,
                'stats' => $stats,
            ];
        }

        return array_values($byPlayer);
    }
}
