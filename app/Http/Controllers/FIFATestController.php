<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class FIFATestController extends Controller
{
    public function show($player_id = null)
    {
        // Utiliser EXACTEMENT la même méthode que la page de test qui fonctionne
        $player = Player::with(['club', 'association'])->find($player_id);
        
        if (!$player) {
            $player = Player::with(['club', 'association'])->first();
        }
        
        // Passer directement le modèle Player à la vue (comme la page de test)
        return view('fifa-portal-integrated', compact('player'));
    }
}
