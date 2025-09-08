<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Association;
use App\Traits\Searchable;

class ClubController extends Controller
{
    use Searchable;

    /**
     * Afficher la liste des clubs
     */
    public function index(Request $request)
    {
        $clubs = Club::with(['association'])->orderBy('name')->get();
        $filtered = false;
        $association = null;
        
        return view('modules.clubs.index', compact('clubs', 'filtered', 'association'));
    }

    /**
     * Afficher un club spécifique
     */
    public function show(Club $club)
    {
        $club->load(['association', 'players']);
        return view('modules.clubs.show', compact('club'));
    }
}
