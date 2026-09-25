<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PlayerPhotoController extends Controller
{
    /**
     * Afficher le formulaire d'upload de photo
     */
    public function showUploadForm(string $playerId): View
    {
        // Charger le joueur avec toutes les relations nécessaires
        $player = Player::with(['club', 'association'])->findOrFail($playerId);
        
        // Récupérer le type d'élément à gérer depuis la requête
        $type = request()->get('type', 'photo');
        
        // Définir les informations selon le type
        $typeInfo = $this->getTypeInfo($type, $player);
        
        return view('player.photo.upload', array_merge(compact('player'), $typeInfo));
    }
    
    /**
     * Obtenir les informations selon le type d'élément
     */
    private function getTypeInfo(string $type, Player $player): array
    {
        switch ($type) {
                            case 'nationality':
                    return [
                        'type' => $type,
                        'typeLabel' => 'la nationalité',
                        'typeIcon' => '🏳️',
                        'currentImageUrl' => $player->nationality ? \App\Helpers\CountryHelper::getFlagUrl(\App\Helpers\CountryHelper::getCountryCode($player->nationality), 'svg', 'lg') : null,
                        'currentExternalUrl' => null,
                        'externalLink' => 'https://github.com/lipis/flag-icons'
                    ];
                
            case 'club':
                return [
                    'type' => $type,
                    'typeLabel' => 'le logo du club',
                    'typeIcon' => '🏟️',
                    'currentImageUrl' => $player->club ? ($player->club->club_logo_url ?? asset('storage/' . $player->club->logo)) : null,
                    'currentExternalUrl' => $player->club ? $player->club->club_logo_url : null
                ];
                
            case 'association':
                return [
                    'type' => $type,
                    'typeLabel' => 'le logo de l\'association',
                    'typeIcon' => '🏆',
                    'currentImageUrl' => $player->association ? ($player->association->logo_url ?? asset('storage/' . $player->association->logo)) : null,
                    'currentExternalUrl' => $player->association ? $player->association->logo_url : null
                ];
                
                            case 'association_flag':
                    return [
                        'type' => $type,
                        'typeLabel' => 'le drapeau de l\'association',
                        'typeIcon' => '🏴',
                        'currentImageUrl' => $player->association && $player->association->country ? \App\Helpers\CountryHelper::getFlagUrl(\App\Helpers\CountryHelper::getCountryCode($player->association->country), 'svg', 'xl') : null,
                        'currentExternalUrl' => null,
                        'externalLink' => 'https://github.com/lipis/flag-icons'
                    ];
                
            default: // photo
                return [
                    'type' => $type,
                    'typeLabel' => 'la photo',
                    'typeIcon' => '📸',
                    'currentImageUrl' => $player->getPlayerPictureUrlAttribute(),
                    'currentExternalUrl' => $player->player_face_url
                ];
        }
    }

    /**
     * Uploader une nouvelle photo
     */
    public function upload(Request $request, string $playerId): RedirectResponse
    {
        $player = Player::findOrFail($playerId);
        $type = $request->get('type', 'photo');
        
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            switch ($type) {
                case 'nationality':
                    // Pour la nationalité, on met à jour le champ nationality avec le nom du pays
                    // L'image sera stockée temporairement pour analyse
                    $path = $request->file('photo')->store('temp', 'public');
                    // Ici vous pourriez ajouter une logique pour détecter le pays depuis l'image
                    // Pour l'instant, on supprime le fichier temporaire
                    Storage::disk('public')->delete($path);
                    return back()->with('warning', 'La gestion de la nationalité via image n\'est pas encore implémentée. Utilisez le formulaire de profil.');
                    
                case 'club':
                    // Pour le club, on met à jour le logo du club
                    if ($player->club) {
                        // Supprimer l'ancien logo si il existe
                        if ($player->club->logo && !filter_var($player->club->logo, FILTER_VALIDATE_URL)) {
                            Storage::disk('public')->delete($player->club->logo);
                        }
                        
                        $path = $request->file('photo')->store('clubs/logos', 'public');
                        $player->club->update(['logo' => $path]);
                        
                        return back()->with('success', 'Logo du club mis à jour avec succès !');
                    }
                    return back()->with('error', 'Aucun club associé à ce joueur');
                    
                case 'association':
                    // Pour l'association, on met à jour le logo de l'association
                    if ($player->association) {
                        // Supprimer l'ancien logo si il existe
                        if ($player->association->logo && !filter_var($player->association->logo, FILTER_VALIDATE_URL)) {
                            Storage::disk('public')->delete($player->association->logo);
                        }
                        
                        $path = $request->file('photo')->store('associations/logos', 'public');
                        $player->association->update(['logo' => $path]);
                        
                        return back()->with('success', 'Logo de l\'association mis à jour avec succès !');
                    }
                    return back()->with('error', 'Aucune association associée à ce joueur');
                    
                case 'association_flag':
                    // Pour le drapeau de l'association, on met à jour le pays de l'association
                    if ($player->association) {
                        // L'image sera stockée temporairement pour analyse
                        $path = $request->file('photo')->store('temp', 'public');
                        // Ici vous pourriez ajouter une logique pour détecter le pays depuis l'image
                        // Pour l'instant, on supprime le fichier temporaire
                        Storage::disk('public')->delete($path);
                        return back()->with('warning', 'La gestion du drapeau via image n\'est pas encore implémentée. Utilisez le formulaire de profil.');
                    }
                    return back()->with('error', 'Aucune association associée à ce joueur');
                    
                default: // photo
                    // Supprimer l'ancienne photo si elle existe
                    if ($player->player_picture && !filter_var($player->player_picture, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($player->player_picture);
                    }

                    // Upload de la nouvelle photo
                    $path = $request->file('photo')->store('players/photos', 'public');
                    
                    // Mettre à jour le joueur
                    $player->update(['player_picture' => $path]);

                    return back()->with('success', 'Photo mise à jour avec succès !');
            }

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'upload : ' . $e->getMessage()]);
        }
    }

    /**
     * Supprimer la photo actuelle
     */
    public function delete(Request $request, string $playerId): RedirectResponse
    {
        $player = Player::findOrFail($playerId);
        $type = $request->get('type', 'photo');
        
        try {
            switch ($type) {
                case 'nationality':
                    return back()->with('warning', 'La suppression de la nationalité n\'est pas autorisée. Utilisez le formulaire de profil.');
                    
                case 'club':
                    if ($player->club && $player->club->logo && !filter_var($player->club->logo, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($player->club->logo);
                        $player->club->update(['logo' => null]);
                        return back()->with('success', 'Logo du club supprimé avec succès !');
                    }
                    return back()->with('error', 'Aucun logo de club à supprimer');
                    
                case 'association':
                    if ($player->association && $player->association->logo && !filter_var($player->association->logo, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($player->association->logo);
                        $player->association->update(['logo' => null]);
                        return back()->with('success', 'Logo de l\'association supprimé avec succès !');
                    }
                    return back()->with('error', 'Aucun logo d\'association à supprimer');
                    
                case 'association_flag':
                    return back()->with('warning', 'La suppression du drapeau de l\'association n\'est pas autorisée. Utilisez le formulaire de profil.');
                    
                default: // photo
                    if ($player->player_picture && !filter_var($player->player_picture, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($player->player_picture);
                    }
                    
                    $player->update(['player_picture' => null]);
                    
                    return back()->with('success', 'Photo supprimée avec succès !');
            }

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
        }
    }

    /**
     * Mettre à jour l'URL de la photo externe
     */
    public function updateExternalUrl(Request $request, string $playerId): RedirectResponse
    {
        $player = Player::findOrFail($playerId);
        $type = $request->get('type', 'photo');
        
        $validator = Validator::make($request->all(), [
            'external_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            switch ($type) {
                case 'nationality':
                    return back()->with('warning', 'La mise à jour de la nationalité via URL n\'est pas autorisée. Utilisez le formulaire de profil.');
                    
                case 'club':
                    if ($player->club) {
                        $player->club->update(['club_logo_url' => $request->external_url]);
                        return back()->with('success', 'URL du logo du club mise à jour avec succès !');
                    }
                    return back()->with('error', 'Aucun club associé à ce joueur');
                    
                case 'association':
                    if ($player->association) {
                        $player->association->update(['logo_url' => $request->external_url]);
                        return back()->with('success', 'URL du logo de l\'association mise à jour avec succès !');
                    }
                    return back()->with('error', 'Aucune association associée à ce joueur');
                    
                case 'association_flag':
                    return back()->with('warning', 'La mise à jour du drapeau de l\'association via URL n\'est pas autorisée. Utilisez le formulaire de profil.');
                    
                default: // photo
                    $player->update(['player_face_url' => $request->external_url]);
                    return back()->with('success', 'URL externe mise à jour avec succès !');
            }

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
        }
    }

    /**
     * Générer une photo avec DiceBear (API gratuite)
     */
    public function generateAvatar(string $playerId): RedirectResponse
    {
        $player = Player::findOrFail($playerId);
        
        try {
            // Générer une URL DiceBear basée sur le nom du joueur
            $seed = urlencode($player->first_name . ' ' . $player->last_name);
            $avatarUrl = "https://api.dicebear.com/7.x/avataaars/svg?seed={$seed}&backgroundColor=b6e3f4&mouth=smile&style=circle";
            
            $player->update(['player_picture' => $avatarUrl]);
            
            return redirect()->route('joueur.show', $playerId)
                           ->with('success', 'Avatar généré avec succès !');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la génération : ' . $e->getMessage()]);
        }
    }
}



