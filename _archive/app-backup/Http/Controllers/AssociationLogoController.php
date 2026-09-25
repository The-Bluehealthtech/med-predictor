<?php

namespace App\Http\Controllers;

use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AssociationLogoController extends Controller
{
    /**
     * Affiche la page de gestion du logo d'une association
     */
    public function editLogo(Request $request, $associationId)
    {
        $association = Association::findOrFail($associationId);
        
        // Vérifier si le logo national existe
        $countryCode = $this->getCountryCode($association->country);
        $nationalLogoExists = false;
        $nationalLogoUrl = null;
        
        if ($countryCode) {
            $logoPath = public_path("associations/{$countryCode}.png");
            $nationalLogoExists = file_exists($logoPath);
            if ($nationalLogoExists) {
                $nationalLogoUrl = asset("associations/{$countryCode}.png");
            }
        }
        
        return view('associations.edit-logo', compact('association', 'nationalLogoExists', 'nationalLogoUrl', 'countryCode'));
    }
    
    /**
     * Met à jour le logo d'une association
     */
    public function updateLogo(Request $request, $associationId)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $association = Association::findOrFail($associationId);
        
        try {
            // Supprimer l'ancien logo s'il existe
            if ($association->association_logo_url) {
                $oldPath = str_replace(asset(''), public_path(''), $association->association_logo_url);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Sauvegarder le nouveau logo
            $logo = $request->file('logo');
            $filename = 'association_' . $associationId . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $path = $logo->storeAs('associations', $filename, 'public');
            
            // Mettre à jour l'association
            $association->update([
                'association_logo_url' => asset('storage/' . $path)
            ]);
            
            return redirect()->back()->with('success', 'Logo mis à jour avec succès !');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du logo : ' . $e->getMessage());
        }
    }
    
    /**
     * Supprime le logo personnalisé et revient au logo national
     */
    public function resetToNationalLogo(Request $request, $associationId)
    {
        $association = Association::findOrFail($associationId);
        
        try {
            // Supprimer l'ancien logo personnalisé s'il existe
            if ($association->association_logo_url) {
                $oldPath = str_replace(asset(''), public_path(''), $association->association_logo_url);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Réinitialiser à null pour utiliser le logo national
            $association->update([
                'association_logo_url' => null
            ]);
            
            return redirect()->back()->with('success', 'Logo réinitialisé au logo national !');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la réinitialisation : ' . $e->getMessage());
        }
    }
    
    /**
     * Met à jour les logos nationaux depuis l'API
     */
    public function updateNationalLogos()
    {
        try {
            // Exécuter le script de mise à jour
            $scriptPath = base_path('scripts/update-national-logos.js');
            $output = shell_exec("node {$scriptPath} 2>&1");
            
            return redirect()->back()->with('success', 'Logos nationaux mis à jour ! Sortie : ' . $output);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }
    
    /**
     * Obtient le code pays ISO
     */
    private function getCountryCode($countryName)
    {
        $countryMapping = [
            'france' => 'FR',
            'maroc' => 'MA',
            'tunisie' => 'TN',
            'algérie' => 'DZ',
            'sénégal' => 'SN',
            'mali' => 'ML',
            'côte d\'ivoire' => 'CI',
            'nigeria' => 'NG',
            'ghana' => 'GH',
            'cameroun' => 'CM',
            'égypte' => 'EG',
            'ftf' => 'TN',
            'faf' => 'DZ',
            'frmf' => 'MA',
        ];
        
        return $countryMapping[strtolower($countryName)] ?? null;
    }
}

