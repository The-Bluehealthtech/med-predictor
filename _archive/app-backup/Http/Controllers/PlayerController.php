<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical,system_admin,super_admin');
    }

    /**
     * Affiche la liste des joueurs avec standards FIFA Connect
     */
    public function index()
    {
        $user = Auth::user();
        $players = collect();

        // Get players based on user role with FIFA Connect hierarchy
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            if ($user->club_id) {
                $players = Player::where('club_id', $user->club_id)
                    ->with(['club.association.confederation', 'association.confederation', 'fifaConnectId'])
                    ->orderBy('first_name')
                    ->paginate(15);
            }
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $players = Player::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->with(['club.association.confederation', 'association.confederation', 'fifaConnectId'])
            ->orderBy('first_name')
            ->paginate(15);
        } elseif (in_array($user->role, ['system_admin', 'admin', 'super_admin'])) {
            $players = Player::with(['club', 'association'])
                ->orderBy('first_name')
                ->paginate(15);
        }

        return view('players.index', compact('players'));
    }

    /**
     * Affiche le formulaire de création de joueur
     */
    public function create()
    {
        $user = Auth::user();
        $clubs = collect();
        $associations = collect();

        // Get clubs based on user role
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            if ($user->club_id) {
                $clubs = Club::where('id', $user->club_id)->get();
            }
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        } elseif (in_array($user->role, ['system_admin', 'admin', 'super_admin'])) {
            $clubs = Club::orderBy('name')->get();
        }

        // Get associations for system admin, admin and super_admin
        if (in_array($user->role, ['system_admin', 'admin', 'super_admin'])) {
            $associations = Association::orderBy('name')->get();
        }

        // Liste des nationalités communes
        $nationalities = [
            'Tunisie', 'Algérie', 'Maroc', 'Égypte', 'Libye', 'Soudan',
            'France', 'Allemagne', 'Espagne', 'Italie', 'Portugal', 'Pays-Bas',
            'Belgique', 'Suisse', 'Autriche', 'Suède', 'Norvège', 'Danemark',
            'Royaume-Uni', 'Irlande', 'Pologne', 'République tchèque', 'Slovaquie',
            'Hongrie', 'Roumanie', 'Bulgarie', 'Grèce', 'Turquie', 'Ukraine',
            'Russie', 'Biélorussie', 'Lituanie', 'Lettonie', 'Estonie',
            'États-Unis', 'Canada', 'Mexique', 'Brésil', 'Argentine', 'Chili',
            'Uruguay', 'Paraguay', 'Colombie', 'Venezuela', 'Pérou', 'Équateur',
            'Bolivie', 'Guyana', 'Suriname', 'Guyana française',
            'Japon', 'Chine', 'Corée du Sud', 'Corée du Nord', 'Vietnam',
            'Thaïlande', 'Malaisie', 'Singapour', 'Indonésie', 'Philippines',
            'Inde', 'Pakistan', 'Bangladesh', 'Sri Lanka', 'Népal', 'Bhoutan',
            'Australie', 'Nouvelle-Zélande', 'Fidji', 'Papouasie-Nouvelle-Guinée',
            'Afrique du Sud', 'Nigeria', 'Ghana', 'Kenya', 'Éthiopie', 'Somalie',
            'Ouganda', 'Tanzanie', 'Zambie', 'Zimbabwe', 'Botswana', 'Namibie',
            'Angola', 'Mozambique', 'Madagascar', 'Maurice', 'Seychelles'
        ];

        // Liste des positions de joueur
        $positions = [
            'Attaquant' => 'Attaquant',
            'Milieu' => 'Milieu',
            'Défenseur' => 'Défenseur',
            'Gardien' => 'Gardien',
            'Entraîneur' => 'Entraîneur',
            'Staff médical' => 'Staff médical',
            'Arbitre' => 'Arbitre',
            'Préparateur physique' => 'Préparateur physique',
            'Kinésithérapeute' => 'Kinésithérapeute',
            'Médecin' => 'Médecin',
            'Psychologue' => 'Psychologue',
            'Nutritionniste' => 'Nutritionniste'
        ];

        return view('players.create', compact('clubs', 'associations', 'nationalities', 'positions'));
    }

    /**
     * Enregistre un nouveau joueur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'club_id' => 'nullable|exists:clubs,id',
            'association_id' => 'nullable|exists:associations,id',
            'player_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'overall_rating' => 'nullable|integer|min:1|max:100',
            'potential_rating' => 'nullable|integer|min:1|max:100',
            'fitness' => 'nullable|integer|min:0|max:100',
        ], [
            'player_picture.image' => 'Le fichier doit être une image (JPG, PNG, JPEG).',
            'player_picture.mimes' => 'Le format de fichier doit être : JPG, PNG ou JPEG.',
            'player_picture.max' => 'La taille de l\'image ne doit pas dépasser 5MB.',
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom de famille est obligatoire.',
            'date_of_birth.required' => 'La date de naissance est obligatoire.',
            'date_of_birth.before' => 'La date de naissance doit être dans le passé.',
            'nationality.required' => 'La nationalité est obligatoire.',
            'position.required' => 'La position est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'club_id.exists' => 'Le club sélectionné n\'existe pas.',
            'association_id.exists' => 'L\'association sélectionnée n\'existe pas.',
        ]);

        try {
            DB::beginTransaction();

            // Créer le joueur
            $player = Player::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'nationality' => $validated['nationality'],
                'position' => $validated['position'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'club_id' => $validated['club_id'],
                'association_id' => $validated['association_id'],
                'overall_rating' => $validated['overall_rating'],
                'potential_rating' => $validated['potential_rating'],
                'fitness' => $validated['fitness'],
                'fifa_connect_id' => Player::generateFifaConnectId(),
                'created_by' => Auth::id(),
            ]);

            // Gérer l'upload de photo si fournie
            if ($request->hasFile('player_picture')) {
                $photoPath = $request->file('player_picture')->store('player_photos', 'public');
                
                // Mettre à jour le joueur avec la photo
                $player->update([
                    'player_picture' => $photoPath,
                ]);
            }

            DB::commit();

            return redirect()->route('players.index')
                ->with('success', 'Joueur créé avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Supprimer la photo si elle a été uploadée
            if (isset($photoPath) && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return redirect()->back()
                ->with('error', 'Erreur lors de la création du joueur : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche les détails d'un joueur
     */
    public function show(Player $player)
    {
        $player->load(['club', 'association', 'licenses']);
        return view('players.show', compact('player'));
    }

    /**
     * Affiche le formulaire d'édition d'un joueur
     */
    public function edit(Player $player)
    {
        $user = Auth::user();
        $clubs = collect();
        $associations = collect();

        // Get clubs based on user role
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            if ($user->club_id) {
                $clubs = Club::where('id', $user->club_id)->get();
            }
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        } elseif (in_array($user->role, ['system_admin', 'admin', 'super_admin'])) {
            $clubs = Club::orderBy('name')->get();
        }

        // Get associations for system admin, admin and super_admin
        if (in_array($user->role, ['system_admin', 'admin', 'super_admin'])) {
            $associations = Association::orderBy('name')->get();
        }

        // Liste des nationalités communes
        $nationalities = [
            'Tunisie', 'Algérie', 'Maroc', 'Égypte', 'Libye', 'Soudan',
            'France', 'Allemagne', 'Espagne', 'Italie', 'Portugal', 'Pays-Bas',
            'Belgique', 'Suisse', 'Autriche', 'Suède', 'Norvège', 'Danemark',
            'Royaume-Uni', 'Irlande', 'Pologne', 'République tchèque', 'Slovaquie',
            'Hongrie', 'Roumanie', 'Bulgarie', 'Grèce', 'Turquie', 'Ukraine',
            'Russie', 'Biélorussie', 'Lituanie', 'Lettonie', 'Estonie',
            'États-Unis', 'Canada', 'Mexique', 'Brésil', 'Argentine', 'Chili',
            'Uruguay', 'Paraguay', 'Colombie', 'Venezuela', 'Pérou', 'Équateur',
            'Bolivie', 'Guyana', 'Suriname', 'Guyana française',
            'Japon', 'Chine', 'Corée du Sud', 'Corée du Nord', 'Vietnam',
            'Thaïlande', 'Malaisie', 'Singapour', 'Indonésie', 'Philippines',
            'Inde', 'Pakistan', 'Bangladesh', 'Sri Lanka', 'Népal', 'Bhoutan',
            'Australie', 'Nouvelle-Zélande', 'Fidji', 'Papouasie-Nouvelle-Guinée',
            'Afrique du Sud', 'Nigeria', 'Ghana', 'Kenya', 'Éthiopie', 'Somalie',
            'Ouganda', 'Tanzanie', 'Zambie', 'Zimbabwe', 'Botswana', 'Namibie',
            'Angola', 'Mozambique', 'Madagascar', 'Maurice', 'Seychelles'
        ];

        // Liste des positions de joueur
        $positions = [
            'Attaquant' => 'Attaquant',
            'Milieu' => 'Milieu',
            'Défenseur' => 'Défenseur',
            'Gardien' => 'Gardien',
            'Entraîneur' => 'Entraîneur',
            'Staff médical' => 'Staff médical',
            'Arbitre' => 'Arbitre',
            'Préparateur physique' => 'Préparateur physique',
            'Kinésithérapeute' => 'Kinésithérapeute',
            'Médecin' => 'Médecin',
            'Psychologue' => 'Psychologue',
            'Nutritionniste' => 'Nutritionniste'
        ];

        return view('players.edit', compact('player', 'clubs', 'associations', 'nationalities', 'positions'));
    }

    /**
     * Met à jour un joueur
     */
    public function update(Request $request, Player $player)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'club_id' => 'nullable|exists:clubs,id',
            'association_id' => 'nullable|exists:associations,id',
            'player_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'overall_rating' => 'nullable|integer|min:1|max:100',
            'potential_rating' => 'nullable|integer|min:1|max:100',
            'fitness' => 'nullable|integer|min:0|max:100',
        ], [
            'player_picture.image' => 'Le fichier doit être une image (JPG, PNG, JPEG).',
            'player_picture.mimes' => 'Le format de fichier doit être : JPG, PNG ou JPEG.',
            'player_picture.max' => 'La taille de l\'image ne doit pas dépasser 5MB.',
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom de famille est obligatoire.',
            'date_of_birth.required' => 'La date de naissance est obligatoire.',
            'date_of_birth.before' => 'La date de naissance doit être dans le passé.',
            'nationality.required' => 'La nationalité est obligatoire.',
            'position.required' => 'La position est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'club_id.exists' => 'Le club sélectionné n\'existe pas.',
            'association_id.exists' => 'L\'association sélectionnée n\'existe pas.',
        ]);

        try {
            DB::beginTransaction();

            // Mettre à jour le joueur
            $player->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'nationality' => $validated['nationality'],
                'position' => $validated['position'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'club_id' => $validated['club_id'],
                'association_id' => $validated['association_id'],
                'overall_rating' => $validated['overall_rating'],
                'potential_rating' => $validated['potential_rating'],
                'fitness' => $validated['fitness'],
            ]);

            // Gérer l'upload de photo si fournie
            if ($request->hasFile('player_picture')) {
                // Supprimer l'ancienne photo si elle existe
                if ($player->player_picture && Storage::disk('public')->exists($player->player_picture)) {
                    Storage::disk('public')->delete($player->player_picture);
                }
                
                $photoPath = $request->file('player_picture')->store('player_photos', 'public');
                
                // Mettre à jour le joueur avec la nouvelle photo
                $player->update([
                    'player_picture' => $photoPath,
                ]);
            }

            DB::commit();

            return redirect()->route('players.index')
                ->with('success', 'Joueur mis à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du joueur : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime un joueur
     */
    public function destroy(Player $player)
    {
        try {
            // Supprimer la photo si elle existe
            if ($player->player_picture && Storage::disk('public')->exists($player->player_picture)) {
                Storage::disk('public')->delete($player->player_picture);
            }
            
            $player->delete();
            
            return redirect()->route('players.index')
                ->with('success', 'Joueur supprimé avec succès !');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du joueur : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les dossiers de santé d'un joueur
     */
    public function healthRecords(Player $player)
    {
        $player->load(['healthRecords', 'club', 'association']);
        return view('players.health-records', compact('player'));
    }

    /**
     * Affiche le formulaire d'import en masse
     */
    public function bulkImportForm()
    {
        $user = Auth::user();
        $clubs = collect();
        $associations = collect();

        // Get clubs based on user role
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            if ($user->club_id) {
                $clubs = Club::where('id', $user->club_id)->get();
            }
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        } elseif (in_array($user->role, ['system_admin', 'admin', 'super_admin'])) {
            $clubs = Club::orderBy('name')->get();
        }

        // Get associations for system admin, admin and super_admin
        if (in_array($user->role, ['system_admin', 'admin', 'super_admin'])) {
            $associations = Association::orderBy('name')->get();
        }

        return view('players.bulk-import-form', compact('clubs', 'associations'));
    }

    /**
     * Traite l'import en masse
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'club_id' => 'required|exists:clubs,id',
            'association_id' => 'required|exists:associations,id',
        ], [
            'csv_file.required' => 'Le fichier CSV est obligatoire.',
            'csv_file.file' => 'Le fichier doit être valide.',
            'csv_file.mimes' => 'Le fichier doit être au format CSV.',
            'csv_file.max' => 'Le fichier ne doit pas dépasser 2MB.',
            'club_id.required' => 'Le club est obligatoire.',
            'club_id.exists' => 'Le club sélectionné n\'existe pas.',
            'association_id.required' => 'L\'association est obligatoire.',
            'association_id.exists' => 'L\'association sélectionnée n\'existe pas.',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('csv_file');
            $clubId = $request->club_id;
            $associationId = $request->association_id;
            
            $importedCount = 0;
            $errors = [];

            // Lire le fichier CSV
            $handle = fopen($file->getPathname(), 'r');
            $headers = fgetcsv($handle); // Ignorer la première ligne (en-têtes)
            
            while (($data = fgetcsv($handle)) !== false) {
                try {
                    // Vérifier que nous avons assez de colonnes
                    if (count($data) < 5) {
                        $errors[] = "Ligne " . ($importedCount + 2) . ": Données insuffisantes";
                        continue;
                    }

                    // Créer le joueur
                    $player = Player::create([
                        'first_name' => trim($data[0]),
                        'last_name' => trim($data[1]),
                        'date_of_birth' => \Carbon\Carbon::parse(trim($data[2])),
                        'nationality' => trim($data[3]),
                        'position' => trim($data[4]),
                        'club_id' => $clubId,
                        'association_id' => $associationId,
                        'fifa_connect_id' => Player::generateFifaConnectId(),
                        'created_by' => Auth::id(),
                    ]);

                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($importedCount + 2) . ": " . $e->getMessage();
                }
            }
            
            fclose($handle);

            DB::commit();

            $message = "Import terminé avec succès ! $importedCount joueurs importés.";
            if (!empty($errors)) {
                $message .= " Erreurs: " . implode(', ', $errors);
            }

            return redirect()->route('players.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage())
                ->withInput();
        }
    }
}


