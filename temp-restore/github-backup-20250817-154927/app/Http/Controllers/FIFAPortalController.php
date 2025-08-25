<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class FIFAPortalController extends Controller
{
    public function index()
    {
        // Récupérer un joueur par défaut avec ses relations
        $player = Player::with(['club', 'association'])->first();
        
        if (!$player) {
            // Créer un joueur par défaut si aucun n'existe
            $player = new \stdClass();
            $player->id = 1;
            $player->first_name = 'Joueur';
            $player->last_name = 'Par Défaut';
            $player->photo_url = null;
            $player->photo_path = null;
            $player->profile_image = null;
            $player->flag_image = null;
            $player->nationality = 'France';
            $player->club = new \stdClass();
            $player->club->name = 'Club Par Défaut';
            $player->club->logo_url = null;
            $player->club->logo_path = null;
            $player->club->logo_image = null;
            $player->association = new \stdClass();
            $player->association->name = 'Association Par Défaut';
            $player->association->logo_url = null;
            $player->association->logo_path = null;
        }
        
        // Passer les données à la vue
        return view('fifa-portal-integrated', compact('player'));
    }
}

