<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Association;
use Illuminate\Http\Request;

class ClubViewController extends Controller
{
    /**
     * Affiche la liste des clubs
     */
    public function index(Request $request)
    {
        $filtered = false;
        $association = null;
        
        // Récupérer les clubs avec leurs associations
        $query = Club::with(['association']);
        
        // Filtrer par association si spécifié
        if ($request->has('association_id') && $request->association_id) {
            $association = Association::find($request->association_id);
            if ($association) {
                $query->where('association_id', $association->id);
                $filtered = true;
            }
        }
        
        // Filtrer par confédération si spécifié
        if ($request->has('confederation_id') && $request->confederation_id) {
            $query->whereHas('association', function($q) use ($request) {
                $q->where('confederation_id', $request->confederation_id);
            });
        }
        
        $clubs = $query->orderBy('name')->get();
        
        return view('modules.clubs.index', compact('clubs', 'filtered', 'association'));
    }
    
    /**
     * Affiche les détails d'un club
     */
    public function show(Request $request)
    {
        $club = Club::with(['association', 'teams', 'players'])->findOrFail($request->id);
        return view('modules.clubs.show', compact('club'));
    }
    
    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $club = Club::with('association')->findOrFail($id);
        $associations = Association::orderBy('name')->get();
        return view('modules.clubs.edit', compact('club', 'associations'));
    }
    
    /**
     * Met à jour un club
     */
    public function update(Request $request, $id)
    {
        $club = Club::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'status' => 'required|in:active,inactive,pending',
            'association_id' => 'nullable|exists:associations,id',
        ]);
        
        $club->update($validated);
        
        return redirect()->route('clubs-view.show', ['id' => $club->id])
                        ->with('success', 'Club mis à jour avec succès !');
    }
}





