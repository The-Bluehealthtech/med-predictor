<?php

namespace App\Http\Controllers;

use App\Models\Confederation;
use App\Services\FifaSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ConfederationController extends Controller
{
    /**
     * Affiche la liste des confédérations
     */
    public function index()
    {
        $confederations = Confederation::with('associations')->orderBy('name')->get();
        return view('modules.confederations.index', compact('confederations'));
    }

    /**
     * Affiche les détails d'une confédération
     */
    public function show($id)
    {
        $confederation = Confederation::with('associations')->findOrFail($id);
        return view('modules.confederations.show', compact('confederation'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $confederation = Confederation::findOrFail($id);
        return view('modules.confederations.edit', compact('confederation'));
    }

    /**
     * Met à jour une confédération
     */
    public function update(Request $request, $id)
    {
        $confederation = Confederation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:10|unique:confederations,short_name,' . $id,
            'country' => 'required|string|max:255',
            'fifa_ranking' => 'nullable|string|max:255',
            'fifa_version' => 'nullable|string|max:20',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'status' => 'required|in:active,inactive,suspended',
            'confederation_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Gestion de l'upload du logo
            if ($request->hasFile('confederation_logo')) {
                // Supprimer l'ancien logo s'il existe
                if ($confederation->confederation_logo_url) {
                    Storage::disk('public')->delete($confederation->confederation_logo_url);
                }

                // Sauvegarder le nouveau logo
                $logoPath = $request->file('confederation_logo')->store('confederation_logos', 'public');
                $confederation->confederation_logo_url = $logoPath;
            }

            // Mise à jour des autres champs
            $confederation->update([
                'name' => $request->name,
                'short_name' => $request->short_name,
                'country' => $request->country,
                'fifa_ranking' => $request->fifa_ranking,
                'fifa_version' => $request->fifa_version,
                'founded_year' => $request->founded_year,
                'status' => $request->status,
            ]);

            return redirect()->route('confederations-view.show', ['id' => $confederation->id])
                           ->with('success', 'Confédération mise à jour avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Synchronise avec l'API FIFA
     */
    public function syncWithFifa($id)
    {
        $confederation = Confederation::findOrFail($id);
        $fifaService = new FifaSyncService();

        try {
            $result = $fifaService->syncConfederation($confederation);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'confederation' => $confederation->fresh()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de synchronisation : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime le logo d'une confédération
     */
    public function deleteLogo($id)
    {
        $confederation = Confederation::findOrFail($id);

        try {
            if ($confederation->confederation_logo_url) {
                Storage::disk('public')->delete($confederation->confederation_logo_url);
                $confederation->update(['confederation_logo_url' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Logo supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }
}
