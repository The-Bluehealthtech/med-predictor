<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\Confederation;
use Illuminate\Http\Request;

class AssociationController extends Controller
{
    /**
     * Affiche la liste des associations avec filtrage par confédération
     */
    public function index(Request $request)
    {
        $query = Association::with(['confederation', 'clubs', 'players']);

        // Filtrage par confédération
        if ($request->has('confederation_id') && $request->confederation_id) {
            $query->where('confederation_id', $request->confederation_id);
            $confederation = Confederation::find($request->confederation_id);
        } else {
            $confederation = null;
        }

        $associations = $query->orderBy('name')->get();

        // Ajouter les compteurs manuellement
        foreach ($associations as $association) {
            $association->clubs_count = $association->clubs->count();
            $association->players_count = $association->players->count();
        }

        return view('modules.associations.index', compact('associations', 'confederation'));
    }

    /**
     * Affiche les détails d'une association
     */
    public function show($id)
    {
        $association = Association::with(['confederation', 'clubs', 'players'])->findOrFail($id);
        return view('modules.associations.show', compact('association'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $association = Association::findOrFail($id);
        $confederations = Confederation::orderBy('name')->get();
        return view('modules.associations.edit', compact('association', 'confederations'));
    }

    /**
     * Met à jour une association
     */
    public function update(Request $request, $id)
    {
        $association = Association::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:10|unique:associations,short_name,' . $id,
            'country' => 'required|string|max:255',
            'confederation_id' => 'nullable|exists:confederations,id',
            'fifa_ranking' => 'nullable|integer',
            'fifa_version' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'association_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nation_flag' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Gestion de l'upload du logo
            if ($request->hasFile('association_logo')) {
                if ($association->association_logo_url) {
                    \Storage::disk('public')->delete($association->association_logo_url);
                }
                $logoPath = $request->file('association_logo')->store('association_logos', 'public');
                $association->association_logo_url = $logoPath;
            }

            // Gestion de l'upload du drapeau
            if ($request->hasFile('nation_flag')) {
                if ($association->nation_flag_url) {
                    \Storage::disk('public')->delete($association->nation_flag_url);
                }
                $flagPath = $request->file('nation_flag')->store('nation_flags', 'public');
                $association->nation_flag_url = $flagPath;
            }

            // Mise à jour des autres champs
            $association->update([
                'name' => $request->name,
                'short_name' => $request->short_name,
                'country' => $request->country,
                'confederation_id' => $request->confederation_id,
                'fifa_ranking' => $request->fifa_ranking,
                'fifa_version' => $request->fifa_version,
                'status' => $request->status,
            ]);

            return redirect()->route('associations.show', $association->id)
                           ->with('success', 'Association mise à jour avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }
}
