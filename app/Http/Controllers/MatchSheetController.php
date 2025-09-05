<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchSheet;

class MatchSheetController extends Controller
{
    public function index()
    {
        // Pour l'instant, on utilise des collections vides car la table match_sheets n'existe pas encore
        $matchSheets = collect([]);
        $upcomingMatches = collect([]);
        $recentMatches = collect([]);
        
        return view('match-sheet.index', compact('matchSheets', 'upcomingMatches', 'recentMatches'));
    }
}


