<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class FIFATestController extends Controller
{
    public function test($id = null)
    {
        // Récupérer le player_id depuis la requête
        $request = request();
        $playerId = $request->get('player_id');
        
        // Si pas de player_id dans la requête, utiliser le paramètre de route
        if (!$playerId && $id) {
            $playerId = $id;
        }
        
        // Récupérer le joueur
        if ($playerId) {
            $player = Player::with(['club', 'association'])->find($playerId);
        }
        
        // Si pas de joueur trouvé, prendre le premier
        if (!$player) {
            $player = Player::with(['club', 'association'])->first();
        }
        
        // Debug: afficher les données récupérées
        if (app()->environment('local')) {
            \Log::info('Contrôleur FIFA - Données récupérées:', [
                'player_id_requested' => $playerId,
                'player_found' => $player ? true : false,
                'player_name' => $player ? $player->first_name . ' ' . $player->last_name : 'AUCUN',
                'player_photo' => $player ? $player->player_picture : 'AUCUNE'
            ]);
        }
        
        if (!$player) {
            // Créer un joueur de test si aucun n'existe
            $player = new \stdClass();
            $player->id = 1;
            $player->first_name = 'Test';
            $player->last_name = 'Joueur';
            $player->photo_url = null;
            $player->photo_path = '/images/players/default_player.svg';
            $player->profile_image = null;
            $player->flag_image = null;
            $player->nationality = 'Test';
            $player->club = new \stdClass();
            $player->club->name = 'Club Test';
            $player->club->logo_url = null;
            $player->club->logo_path = '/images/clubs/default_club.svg';
            $player->association = new \stdClass();
            $player->association->name = 'Association Test';
            $player->association->logo_url = null;
        }
        
        // Debug: afficher les données avant de passer à la vue
        if (app()->environment('local')) {
            \Log::info('Données du joueur:', [
                'id' => $player->id,
                'name' => $player->first_name . ' ' . $player->last_name,
                'photo' => $player->profile_image,
                'flag' => $player->flag_image,
                'club_logo' => $player->club ? $player->club->logo_url : null
            ]);
        }
        
        // Améliorer les données avec de vraies images tunisiennes
        if ($player->nationality && (stripos($player->nationality, 'Tunis') !== false)) {
            // Ajouter le drapeau tunisien si pas de drapeau
            if (!$player->flag_image) {
                $player->flag_image = 'https://flagcdn.com/w80/tn.png';
            }
        }
        
        // Améliorer les logos des associations tunisiennes
        if ($player->association) {
            if ($player->association->name === 'FTF') {
                $player->association->logo_url = '/images/associations/ftf-logo-officiel.png';
                \Log::info('Logo FTF officiel utilisé:', ['association' => $player->association->name, 'logo' => $player->association->logo_url]);
            }
        }
        
        // Améliorer les logos des clubs tunisiens avec les vraies images locales
        if ($player->club) {
            // Mapping des clubs tunisiens vers leurs vraies images locales (noms exacts de la base)
            $clubImages = [
                'Espérance de Tunis' => '/clubs/EST.webp',
                'Club Africain' => '/clubs/CA.webp',
                'Étoile du Sahel' => '/clubs/ESS.webp',
                'US Monastir' => '/clubs/USM.webp',
                'JS Kairouan' => '/clubs/JSK.webp',
                'Stade Tunisien' => '/clubs/ST.webp',
                'AS Gabès' => '/clubs/ASG.webp',
                'CA Bizertin' => '/clubs/CAB.webp',
                'Union Sportive Ben Guerdane' => '/clubs/USBG.webp',
                'Olympique Béja' => '/clubs/OB.webp',
                'Jendouba Sport Olympique' => '/clubs/JSO.webp',
                'Union Sportive Tataouine' => '/clubs/UST.webp',
                'Étoile Sportive de Métlaoui' => '/clubs/ESM.webp',
                'Étoile Sportive de Zarzis' => '/clubs/ESZ.webp',
                'CS Sfaxien' => '/clubs/CSS.webp',
                'Étoile Sportive du Sahel Gabès' => '/clubs/EGSG.webp',
                'Association Sportive de Sousse' => '/clubs/ASS.webp'
            ];
            
            // Si c'est un club tunisien, utiliser l'image locale
            if (isset($clubImages[$player->club->name])) {
                $player->club->logo_url = $clubImages[$player->club->name];
                \Log::info('Logo local tunisien utilisé:', ['club' => $player->club->name, 'logo' => $player->club->logo_url]);
            }
        }
        
        // Améliorer les photos des joueurs avec des images locales au lieu des images externes bloquées
        if ($player->player_face_url && (strpos($player->player_face_url, 'http') === 0)) {
            // Remplacer les URLs externes par des photos locales
            $player->player_face_url = null;
            \Log::info('URL externe bloquée remplacée par photo locale pour:', ['player' => $player->first_name . ' ' . $player->last_name]);
        }
        
        // Utiliser la photo locale du joueur si disponible
        if ($player->player_picture && strpos($player->player_picture, 'players/photos/') === 0) {
            $player->player_face_url = '/storage/' . $player->player_picture;
            \Log::info('Photo locale utilisée:', ['photo' => $player->player_face_url]);
        }
        
        // Passer les données à la vue
        return view('fifa-portal-integrated', compact('player'));
    }
}
