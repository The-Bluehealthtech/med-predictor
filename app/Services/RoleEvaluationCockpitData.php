<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerRoleEvaluation;
use Illuminate\Support\Facades\DB;

/**
 * Lecture seule, pour le portail joueur (onglet "Statistiques avancées" /
 * "Évaluation des performances") : présente la dernière évaluation "rôle et
 * apport" (Livrable 4 — voir docs/role-evaluation/06-implementation-moteur-
 * calcul.md) calculée pour un joueur, sa comparaison aux familles voisines,
 * son profil par dimension, l'évolution de son score sur la saison et la
 * répartition de ses minutes jouées par poste.
 *
 * N'écrit jamais dans player_role_evaluations ni ailleurs. Si aucune
 * évaluation n'existe pour ce joueur — le cas de tous les joueurs
 * aujourd'hui, tant qu'aucune version de configuration de poids réels n'a
 * été calculée (role-eval:compute) — renvoie hasEvaluation=false plutôt que
 * d'inventer une valeur.
 *
 * "Dernière évaluation" = la ligne la plus récente PARMI CELLES SANS
 * period_end (computed_at, id) : period_end distingue un "instantané
 * complet" (role-eval:compute, period_end = null — score et comparaisons
 * affichés) d'un "point d'historique" (role-eval:compute-history,
 * period_end renseigné — n'alimente que la courbe). Toutes les lignes d'un
 * même calcul partagent exactement le même computed_at
 * (RoleEvaluationComputeCommand écrit un seul $now pour tout l'appel), donc
 * ce couple identifie sans ambiguïté "le dernier calcul complet" pour ce
 * joueur.
 */
final class RoleEvaluationCockpitData
{
    public function forPlayer(Player $player): array
    {
        $latest = PlayerRoleEvaluation::query()
            ->where('player_id', $player->id)
            ->whereNull('period_end')
            ->orderByDesc('computed_at')
            ->orderByDesc('id')
            ->first();

        if ($latest === null) {
            return ['hasEvaluation' => false];
        }

        $rows = PlayerRoleEvaluation::query()
            ->where('player_id', $player->id)
            ->where('computed_at', $latest->computed_at)
            ->where('role_config_version_id', $latest->role_config_version_id)
            ->whereNull('period_end')
            ->get();

        $played = $rows->first(fn (PlayerRoleEvaluation $row) => (float) $row->role_fit_score === 0.0) ?? $rows->first();

        $comparisons = $rows
            ->reject(fn (PlayerRoleEvaluation $row) => $played !== null && $row->is($played))
            ->sortByDesc(fn (PlayerRoleEvaluation $row) => (float) $row->role_fit_score)
            ->values();

        $configVersion = DB::table('role_config_versions')->find($latest->role_config_version_id);

        return [
            'hasEvaluation' => true,
            'isDemo' => (bool) $latest->is_demo,
            'computedAt' => $latest->computed_at,
            'modelVersion' => $latest->model_version,
            'configLabel' => $configVersion?->label,
            'configStatus' => $configVersion?->status,
            'played' => $played,
            'comparisons' => $comparisons,
            'dimensionBreakdown' => $played?->dimension_breakdown,
            'trend' => $played !== null ? $this->trend($player, $latest, $played) : collect(),
            'minutesByPosition' => $this->minutesByPosition($player),
        ];
    }

    /**
     * Points d'historique (role-eval:compute-history, period_end
     * renseigné) pour la famille réellement jouée, même joueur et même
     * version de configuration que l'instantané courant : la courbe
     * "évolution du score sur la saison" du cockpit.
     */
    private function trend(Player $player, PlayerRoleEvaluation $latest, PlayerRoleEvaluation $played): \Illuminate\Support\Collection
    {
        return PlayerRoleEvaluation::query()
            ->where('player_id', $player->id)
            ->where('role_config_version_id', $latest->role_config_version_id)
            ->where('position_family_evaluated', $played->position_family_evaluated)
            ->where('role_fit_score', 0)
            ->whereNotNull('period_end')
            ->orderBy('period_end')
            ->get(['period_end', 'score']);
    }

    /**
     * Répartition des minutes jouées par famille de poste (match_participations,
     * minute_in/minute_out), toutes les participations de ce joueur — donnée
     * d'entrée existante (Livrable 1), aucun calcul du moteur "rôle et apport"
     * n'y est impliqué.
     */
    private function minutesByPosition(Player $player): \Illuminate\Support\Collection
    {
        return DB::table('match_participations')
            ->join('position_catalog', 'position_catalog.code', '=', 'match_participations.detailed_position')
            ->where('match_participations.player_id', $player->id)
            ->whereNotNull('match_participations.minute_in')
            ->whereNotNull('match_participations.minute_out')
            ->selectRaw('position_catalog.family as family, SUM(match_participations.minute_out - match_participations.minute_in) as minutes')
            ->groupBy('position_catalog.family')
            ->havingRaw('SUM(match_participations.minute_out - match_participations.minute_in) > 0')
            ->orderByDesc('minutes')
            ->get();
    }
}
