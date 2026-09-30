<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerRoleEvaluation;
use Illuminate\Support\Facades\DB;

/**
 * Lecture seule, pour le portail joueur (onglet "Statistiques avancées" /
 * "Évaluation des performances") : présente la dernière évaluation "rôle et
 * apport" (Livrable 4 — voir docs/role-evaluation/06-implementation-moteur-
 * calcul.md) calculée pour un joueur.
 *
 * N'écrit jamais dans player_role_evaluations ni ailleurs. Si aucune
 * évaluation n'existe pour ce joueur — le cas de tous les joueurs
 * aujourd'hui, tant qu'aucune version de configuration de poids réels n'a
 * été calculée (role-eval:compute) — renvoie hasEvaluation=false plutôt que
 * d'inventer une valeur.
 *
 * "Dernière évaluation" = la ligne la plus récente (computed_at, id), puis
 * toutes les lignes qui partagent exactement son computed_at et sa
 * role_config_version_id : RoleEvaluationComputeCommand écrit toutes les
 * lignes d'un même calcul avec un seul et même $now, donc ce couple
 * identifie sans ambiguïté "le dernier calcul" pour ce joueur.
 */
final class RoleEvaluationCockpitData
{
    public function forPlayer(Player $player): array
    {
        $latest = PlayerRoleEvaluation::query()
            ->where('player_id', $player->id)
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
        ];
    }
}
