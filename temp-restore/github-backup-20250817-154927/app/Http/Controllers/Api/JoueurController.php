<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Joueur;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JoueurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $joueurs = Joueur::select(['id', 'fifa_id', 'nom', 'prenom', 'poste', 'club', 'fifa_ovr', 'score_fit'])
            ->orderBy('fifa_ovr', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $joueurs,
            'message' => 'Liste des joueurs récupérée avec succès'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $joueur = Joueur::where('fifa_id', $id)->orWhere('id', $id)->first();

        if (!$joueur) {
            return response()->json([
                'success' => false,
                'message' => 'Joueur non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $joueur,
            'message' => 'Joueur récupéré avec succès'
        ]);
    }

    /**
     * Get player by FIFA ID
     */
    public function getByFifaId(string $fifaId): JsonResponse
    {
        $joueur = Joueur::where('fifa_id', $fifaId)->first();

        if (!$joueur) {
            return response()->json([
                'success' => false,
                'message' => 'Joueur non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $joueur,
            'message' => 'Joueur récupéré avec succès'
        ]);
    }

    /**
     * Get player statistics
     */
    public function getStats(string $id): JsonResponse
    {
        $joueur = Joueur::where('fifa_id', $id)->orWhere('id', $id)->first();

        if (!$joueur) {
            return response()->json([
                'success' => false,
                'message' => 'Joueur non trouvé'
            ], 404);
        }

        $stats = [
            'sportives' => [
                'buts' => $joueur->buts,
                'passes_decisives' => $joueur->passes_decisives,
                'matchs' => $joueur->matchs,
                'minutes_jouees' => $joueur->minutes_jouees,
                'note_moyenne' => $joueur->note_moyenne,
                'buts_par_match' => $joueur->buts_par_match,
                'passes_par_match' => $joueur->passes_par_match
            ],
            'fifa' => [
                'ovr' => $joueur->fifa_ovr,
                'pot' => $joueur->fifa_pot,
                'score_fit' => $joueur->score_fit,
                'risque_blessure' => $joueur->risque_blessure,
                'valeur_marchande' => $joueur->valeur_marchande
            ],
            'statistiques' => [
                'offensives' => $joueur->statistiques_offensives,
                'physiques' => $joueur->statistiques_physiques,
                'techniques' => $joueur->statistiques_techniques
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Statistiques récupérées avec succès'
        ]);
    }

    /**
     * Get player health data
     */
    public function getHealthData(string $id): JsonResponse
    {
        $joueur = Joueur::where('fifa_id', $id)->orWhere('id', $id)->first();

        if (!$joueur) {
            return response()->json([
                'success' => false,
                'message' => 'Joueur non trouvé'
            ], 404);
        }

        $healthData = [
            'donnees_sante' => $joueur->donnees_sante,
            'historique_blessures' => $joueur->historique_blessures,
            'donnees_devices' => $joueur->donnees_devices,
            'donnees_dopage' => $joueur->donnees_dopage,
            'donnees_sdoh' => $joueur->donnees_sdoh
        ];

        return response()->json([
            'success' => true,
            'data' => $healthData,
            'message' => 'Données de santé récupérées avec succès'
        ]);
    }

    /**
     * Get player notifications
     */
    public function getNotifications(string $id): JsonResponse
    {
        $joueur = Joueur::where('fifa_id', $id)->orWhere('id', $id)->first();

        if (!$joueur) {
            return response()->json([
                'success' => false,
                'message' => 'Joueur non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $joueur->notifications,
            'message' => 'Notifications récupérées avec succès'
        ]);
    }
}
