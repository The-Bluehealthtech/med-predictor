<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Club;
use App\Models\Player;
use App\Models\LicenseComplete;
use App\Models\LicensePhoto;
use Illuminate\Support\Facades\DB;

class LicensePhotoController extends Controller
{
    /**
     * Affiche la page d'upload de photos
     */
    public function showUploadForm(Request $request)
    {
        // Charger les clubs tunisiens avec leurs associations
        $clubs = Club::where('country', 'Tunisie')
            ->with('association')
            ->orderBy('name')
            ->get();
            
        $recentPhotos = LicensePhoto::with(['player', 'club'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Pour le test, créons des données factices si pas de photos
        if ($recentPhotos->isEmpty()) {
            $recentPhotos = collect();
        }

        // Vérifier si on vient de la liste des joueurs avec des paramètres pré-remplis
        $preSelectedPlayer = null;
        $preSelectedClub = null;
        
        if ($request->has('player_id') && $request->has('club_id')) {
            $preSelectedPlayer = Player::with('club')->find($request->player_id);
            $preSelectedClub = Club::find($request->club_id);
            
            \Log::info('Paramètres pré-remplis détectés:', [
                'player_id' => $request->player_id,
                'club_id' => $request->club_id,
                'player' => $preSelectedPlayer ? $preSelectedPlayer->first_name . ' ' . $preSelectedPlayer->last_name : 'Non trouvé',
                'club' => $preSelectedClub ? $preSelectedClub->name : 'Non trouvé'
            ]);
        }

        // Debug: vérifier que les clubs sont chargés
        \Log::info('Clubs chargés pour upload:', [
            'count' => $clubs->count(),
            'clubs' => $clubs->pluck('name', 'id')->toArray()
        ]);

        return view('licenses.upload-photo', compact('clubs', 'recentPhotos', 'preSelectedPlayer', 'preSelectedClub'));
    }

    /**
     * Traite l'upload de la photo et crée la licence
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'player_id' => 'required|exists:players,id',
            'player_photo' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'license_type' => 'required|in:amateur,semi_pro,professional,international',
        ]);

        try {
            DB::beginTransaction();

            // Récupérer d'abord les informations du joueur
            $player = Player::with('club')->find($request->player_id);
            if (!$player) {
                throw new \Exception('Joueur non trouvé');
            }

            // Upload de la photo
            $photoPath = $request->file('player_photo')->store('player_photos', 'public');

            // Créer l'enregistrement de la photo
            $licensePhoto = LicensePhoto::create([
                'player_id' => $request->player_id,
                'club_id' => $request->club_id,
                'photo_path' => $photoPath,
                'uploaded_by' => auth()->id() ?? 1, // Admin par défaut
                'uploaded_at' => now(),
            ]);

            // Créer la licence FIFA complète du joueur
            $playerLicense = LicenseComplete::create([
                'fifa_connect_id' => LicenseComplete::generateFifaConnectId(),
                'license_type' => 'player',
                'applicant_name' => $player->first_name . ' ' . $player->last_name,
                'date_of_birth' => $player->date_of_birth,
                'nationality' => $player->nationality,
                'position' => $player->position,
                'player_id' => $request->player_id,
                'club_id' => $request->club_id,
                'association_id' => $player->club->association_id ?? null,
                'license_reason' => 'Licence professionnelle FIFA avec photo',
                'validity_period' => '2_years',
                'status' => 'approved',
                'fifa_license_number' => LicenseComplete::generateFifaLicenseNumber(),
                'fifa_license_category' => $request->license_type,
                'fifa_license_level' => 'intermediate',
                'fifa_license_issued_date' => now(),
                'fifa_license_expiry_date' => now()->addYears(2),
                'fifa_license_status' => 'active',
            ]);

            // Mettre à jour le joueur avec la nouvelle photo
            $player->update([
                'player_picture' => $photoPath,
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', '✅ Photo uploadée et licence créée avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Supprimer la photo si elle a été uploadée
            if (isset($photoPath) && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return redirect()->back()
                ->with('error', '❌ Erreur lors de l\'upload : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API pour récupérer les joueurs d'un club
     */
    public function getClubPlayers($clubId)
    {
        $players = Player::where('club_id', $clubId)
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get();

        return response()->json($players);
    }

    /**
     * Affiche la liste des licences FIFA avec photos
     */
    public function showLicenses()
    {
        // Récupérer les licences FIFA complètes
        $licenses = LicenseComplete::with(['player', 'club', 'association'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Récupérer les photos des joueurs
        $playerPhotos = LicensePhoto::with(['player', 'club'])->get();

        return view('licenses.index', compact('licenses', 'playerPhotos'));
    }

    /**
     * Supprime une photo de licence
     */
    public function deletePhoto($photoId)
    {
        $photo = LicensePhoto::findOrFail($photoId);
        
        // Supprimer le fichier physique
        if (Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        // Supprimer l'enregistrement
        $photo->delete();

        return redirect()->back()->with('success', '✅ Photo supprimée avec succès !');
    }
}


