<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Club;
use App\Models\Nationality;
use Illuminate\Http\Request;

class PlayerSelectionController extends Controller
{
    public function index()
    {
        // Récupérer tous les joueurs avec leurs relations
        $players = Player::with(['club'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Récupérer tous les clubs pour les filtres
        $clubs = Club::orderBy('name')->get();

        // Récupérer toutes les nationalités pour les filtres
        $nationalities = Nationality::orderBy('name')->get();

        return view('player-selection', compact('players', 'clubs', 'nationalities'));
    }

    public function show($id)
    {
        // Rediriger vers le portail du joueur
        return redirect()->route('joueur.portal', $id);
    }
}
