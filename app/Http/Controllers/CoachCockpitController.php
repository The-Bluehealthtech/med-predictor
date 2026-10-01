<?php

namespace App\Http\Controllers;

use App\Services\CoachCockpit\CoachCockpitData;
use Illuminate\Http\Request;

/**
 * Cockpit entraîneur (module Analytics & Performance) : bilan, pronostic,
 * onze optimal du modèle de sélection, grille des postes, indicateurs de jeu
 * et effectif d'une équipe choisie.
 */
class CoachCockpitController extends Controller
{
    public function show(Request $request, CoachCockpitData $data)
    {
        $user = $request->user();
        abort_if($user->isPlayer(), 403, 'Accès réservé au staff et aux administrateurs.');

        // Un compte de club ne voit que son club ; les autres rôles choisissent librement.
        $clubs = $data->availableClubs();
        if ($user->isClubUser()) {
            $clubs = $clubs->where('id', (int) $user->club_id)->values();
        }

        $requested = $request->filled('club_id') ? filter_var($request->query('club_id'), FILTER_VALIDATE_INT) : null;
        if ($requested !== null) {
            abort_if($requested === false || !$clubs->contains('id', $requested), 404, 'Équipe introuvable ou non autorisée.');
            $clubId = $requested;
        } else {
            $clubId = $clubs->contains('id', (int) $user->club_id) ? (int) $user->club_id : $clubs->first()?->id;
        }

        $cockpit = $clubId !== null ? $data->forClub((int) $clubId) : null;

        return view('modules.coach-cockpit.show', [
            'clubs' => $clubs,
            'clubId' => $clubId,
            'cockpit' => $cockpit,
        ]);
    }
}
