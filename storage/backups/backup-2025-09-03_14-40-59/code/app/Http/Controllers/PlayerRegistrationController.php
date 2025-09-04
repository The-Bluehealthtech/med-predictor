<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Support\Facades\Auth;

class PlayerRegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical,system_admin,super_admin');
    }

    /**
     * Affiche la liste des joueurs enregistrés
     */
    public function index()
    {
        $user = Auth::user();
        $players = collect();

        // Get players based on user role
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            if ($user->club_id) {
                $players = Player::where('club_id', $user->club_id)
                    ->with(['club', 'association'])
                    ->orderBy('first_name')
                    ->paginate(15);
            }
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $players = Player::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->with(['club', 'association'])
            ->orderBy('first_name')
            ->paginate(15);
        } elseif (in_array($user->role, ['system_admin', 'admin', 'super_admin'])) {
            $players = Player::with(['club', 'association'])
                ->orderBy('first_name')
                ->paginate(15);
        }

        return view('modules.player-registration.index', compact('players'));
    }

    /**
     * Affiche le formulaire de création de joueur
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $clubs = collect();
        $associations = collect();
        $existingPlayer = null;
        $isReadOnly = false;

                // Vérifier si un player_id est fourni pour pré-remplir le formulaire
        if ($request->has('player_id')) {
            $existingPlayer = Player::with(['club', 'association', 'licenses'])
                ->find($request->player_id);

            if ($existingPlayer) {
                // Un joueur qui existe déjà n'est JAMAIS un nouveau joueur
                // Il a soit un FIFA Connect ID, soit des licences précédentes
                $isReadOnly = false;
            }
        }

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

        // Types de licence FIFA Connect
        $licenseTypes = [
            'amateur' => 'Amateur',
            'professional' => 'Professionnel',
            'futsal' => 'Futsal',
            'women' => 'Football Féminin',
            'youth' => 'Jeunes',
            'senior' => 'Séniors',
            'veteran' => 'Vétérans'
        ];

        // $teams est un alias pour $clubs (pour compatibilité avec la vue)
        $teams = $clubs;

        // Vérifier si l'utilisateur connecté a un PCMA valide
        $user = Auth::user();
        
        $pcmaStatus = null;
        if ($user) {
            // Chercher un PCMA valide et signé pour l'utilisateur connecté
            // Utiliser player_id si disponible, sinon chercher par d'autres moyens
            $validPcma = \App\Models\PCMA::where(function($query) use ($user) {
                    $query->where('player_id', $user->id)
                          ->orWhere('athlete_id', $user->id);
                })
                ->where('is_signed', true)
                ->where('status', 'cleared')
                ->first();
            
            if ($validPcma) {
                $pcmaStatus = [
                    'status' => 'valid',
                    'message' => 'PCMA valide et signé trouvé',
                    'pcma' => $validPcma
                ];
            } else {
                $pcmaStatus = [
                    'status' => 'missing',
                    'message' => 'Aucun PCMA valide et signé trouvé',
                    'note' => 'Un PCMA avec statut "cleared" et signé est requis'
                ];
            }
        }

        return view('modules.player-registration.create', compact(
            'clubs', 
            'associations', 
            'nationalities', 
            'positions', 
            'teams', 
            'pcmaStatus',
            'existingPlayer',
            'isReadOnly',
            'licenseTypes'
        ));
    }

    /**
     * Enregistre un nouveau joueur
     */
    public function store(Request $request)
    {
        // Vérifier que l'utilisateur a un PCMA valide avant de permettre l'inscription
        $user = Auth::user();
        
        $validPcma = \App\Models\PCMA::where(function($query) use ($user) {
                $query->where('player_id', $user->id)
                      ->orWhere('athlete_id', $user->id);
            })
            ->where('is_signed', true)
            ->where('status', 'cleared')
            ->first();
            
        if (!$validPcma) {
            return redirect()->back()
                ->with('error', 'Un PCMA (Protocole de Concertation Médicale d\'Aptitude) valide et signé avec statut "cleared" est obligatoire pour créer un joueur.')
                ->withInput();
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'club_id' => 'required|exists:clubs,id',
            'association_id' => 'required|exists:associations,id',
            'player_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            // Nouveaux champs pour la demande de licence
            'address' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'legal_guardian' => 'nullable|string|max:255',
            'school_professional_status' => 'nullable|string|max:50',
            'parental_consent' => 'nullable|string|max:50',
            'license_type' => 'nullable|string|max:50',
            'previous_clubs' => 'nullable|string|max:500',
            'previous_license_number' => 'nullable|string|max:100',
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
            'club_id.required' => 'Le club est obligatoire.',
            'club_id.exists' => 'Le club sélectionné n\'existe pas.',
            'association_id.required' => 'L\'association est obligatoire.',
            'association_id.exists' => 'L\'association sélectionnée n\'existe pas.',
            'contact_email.email' => 'L\'email de contact doit être une adresse email valide.',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier l'action demandée
            $action = $request->input('action', 'create');
            
            // Déterminer si c'est un nouveau joueur ou une demande de licence
            $isNewPlayer = !$request->has('player_id') || 
                           (!$existingPlayer || 
                            (!$existingPlayer->fifa_connect_id && 
                             !$existingPlayer->licenses()->exists()));
            
            if ($action === 'save' && $request->has('player_id') && !$isNewPlayer) {
                // Sauvegarder une demande de licence existante
                $player = Player::findOrFail($request->player_id);
                
                // Mettre à jour les champs de la demande de licence
                $player->update([
                    'address' => $validated['address'] ?? null,
                    'contact_phone' => $validated['contact_phone'] ?? null,
                    'contact_email' => $validated['contact_email'] ?? null,
                    'legal_guardian' => $validated['legal_guardian'] ?? null,
                    'school_professional_status' => $validated['school_professional_status'] ?? null,
                    'parental_consent' => $validated['parental_consent'] ?? null,
                    'license_type' => $validated['license_type'] ?? null,
                    'previous_clubs' => $validated['previous_clubs'] ?? null,
                    'previous_license_number' => $validated['previous_license_number'] ?? null,
                ]);
                
                // Créer ou mettre à jour la demande de licence
                $licenseRequest = \App\Models\LicenseRequest::updateOrCreate(
                    ['player_id' => $player->id],
                    [
                        'club_id' => $player->club_id,
                        'association_id' => $player->association_id,
                        'created_by' => Auth::id(),
                        'status' => 'draft', // État "En cours"
                        'address' => $validated['address'] ?? null,
                        'contact_phone' => $validated['contact_phone'] ?? null,
                        'contact_email' => $validated['contact_email'] ?? null,
                        'legal_guardian' => $validated['legal_guardian'] ?? null,
                        'school_professional_status' => $validated['school_professional_status'] ?? null,
                        'parental_consent' => $validated['parental_consent'] ?? null,
                        'license_type' => $validated['license_type'] ?? null,
                        'previous_clubs' => $validated['previous_clubs'] ?? null,
                        'previous_license_number' => $validated['previous_license_number'] ?? null,
                    ]
                );
                
                DB::commit();
                
                return redirect()->back()
                    ->with('success', 'Demande de licence sauvegardée avec succès ! (État: En cours)');
                    
            } elseif ($action === 'submit' && $request->has('player_id') && !$isNewPlayer) {
                // Envoyer la demande à l'association
                $player = Player::findOrFail($request->player_id);
                
                // Mettre à jour les champs de la demande de licence
                $player->update([
                    'address' => $validated['address'] ?? null,
                    'contact_phone' => $validated['contact_phone'] ?? null,
                    'contact_email' => $validated['contact_email'] ?? null,
                    'legal_guardian' => $validated['legal_guardian'] ?? null,
                    'school_professional_status' => $validated['school_professional_status'] ?? null,
                    'parental_consent' => $validated['parental_consent'] ?? null,
                    'license_type' => $validated['license_type'] ?? null,
                    'previous_clubs' => $validated['previous_clubs'] ?? null,
                    'previous_license_number' => $validated['previous_license_number'] ?? null,
                ]);
                
                // Créer ou mettre à jour la demande de licence avec statut "submitted"
                $licenseRequest = \App\Models\LicenseRequest::updateOrCreate(
                    ['player_id' => $player->id],
                    [
                        'club_id' => $player->club_id,
                        'association_id' => $player->association_id,
                        'created_by' => Auth::id(),
                        'status' => 'submitted', // État "Demande envoyée"
                        'submitted_at' => now(),
                        'address' => $validated['address'] ?? null,
                        'contact_phone' => $validated['contact_phone'] ?? null,
                        'contact_email' => $validated['contact_email'] ?? null,
                        'legal_guardian' => $validated['legal_guardian'] ?? null,
                        'school_professional_status' => $validated['school_professional_status'] ?? null,
                        'parental_consent' => $validated['parental_consent'] ?? null,
                        'license_type' => $validated['license_type'] ?? null,
                        'previous_clubs' => $validated['previous_clubs'] ?? null,
                        'previous_license_number' => $validated['previous_license_number'] ?? null,
                    ]
                );
                
                DB::commit();
                
                return redirect()->route('player-registration.index')
                    ->with('success', 'Demande de licence envoyée avec succès à l\'association ! (État: Demande envoyée)');
                    
            } elseif ($isNewPlayer) {
                // Créer un VRAI nouveau joueur (sans FIFA Connect ID ni licence précédente)
                $player = Player::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'nationality' => $validated['nationality'],
                    'position' => $validated['position'],
                    'club_id' => $validated['club_id'],
                    'association_id' => $validated['association_id'],
                    'fifa_connect_id' => Player::generateFifaConnectId(),
                    'created_by' => Auth::id(),
                    // Nouveaux champs pour la demande de licence
                    'address' => $validated['address'] ?? null,
                    'contact_phone' => $validated['contact_phone'] ?? null,
                    'contact_email' => $validated['contact_email'] ?? null,
                    'legal_guardian' => $validated['legal_guardian'] ?? null,
                    'school_professional_status' => $validated['school_professional_status'] ?? null,
                    'parental_consent' => $validated['parental_consent'] ?? null,
                    'license_type' => $validated['license_type'] ?? null,
                    'previous_clubs' => $validated['previous_clubs'] ?? null,
                    'previous_license_number' => $validated['previous_license_number'] ?? null,
                ]);
                
                // Créer automatiquement une demande de licence pour ce nouveau joueur
                $licenseRequest = \App\Models\LicenseRequest::create([
                    'player_id' => $player->id,
                    'club_id' => $player->club_id,
                    'association_id' => $player->association_id,
                    'created_by' => Auth::id(),
                    'status' => 'submitted', // Directement envoyée car nouveau joueur
                    'submitted_at' => now(),
                    'address' => $validated['address'] ?? null,
                    'contact_phone' => $validated['contact_phone'] ?? null,
                    'contact_email' => $validated['contact_email'] ?? null,
                    'legal_guardian' => $validated['legal_guardian'] ?? null,
                    'school_professional_status' => $validated['school_professional_status'] ?? null,
                    'parental_consent' => $validated['parental_consent'] ?? null,
                    'license_type' => $validated['license_type'] ?? null,
                    'previous_clubs' => $validated['previous_clubs'] ?? null,
                    'previous_license_number' => $validated['previous_license_number'] ?? null,
                ]);
            } else {
                // Cas d'erreur : action non reconnue
                throw new \Exception('Action non reconnue ou joueur non trouvé');
            }

            // Gérer l'upload de photo si fournie
            if ($request->hasFile('player_picture')) {
                $photoPath = $request->file('player_picture')->store('player_photos', 'public');
                
                // Mettre à jour le joueur avec la photo
                $player->update([
                    'player_picture' => $photoPath,
                ]);
            }

            DB::commit();

            return redirect()->route('player-registration.index')
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
        
        return view('modules.player-registration.show', compact('player'));
    }

    /**
     * Affiche le formulaire de modification d'un joueur
     */
    public function edit(Player $player)
    {
        $clubs = Club::orderBy('name')->get();
        $associations = Association::orderBy('name')->get();
        $nationalities = ['France', 'Allemagne', 'Espagne', 'Italie', 'Angleterre', 'Portugal', 'Pays-Bas', 'Belgique', 'Suisse', 'Autriche'];
        $positions = ['Gardien', 'Défenseur', 'Milieu', 'Attaquant'];
        $teams = Team::orderBy('name')->get();
        
        // Vérifier le statut PCMA pour ce joueur
        $pcmaStatus = null;
        try {
            $validPcma = \App\Models\PCMA::where(function($query) use ($player) {
                    $query->where('player_id', $player->id)
                          ->orWhere('athlete_id', $player->id);
                })
                ->where('is_signed', true)
                ->where('status', 'cleared')
                ->first();
            
            if ($validPcma) {
                $pcmaStatus = [
                    'status' => 'valid',
                    'message' => 'PCMA valide et signé trouvé',
                    'pcma' => $validPcma
                ];
            } else {
                $pcmaStatus = [
                    'status' => 'missing',
                    'message' => 'Aucun PCMA valide et signé trouvé',
                    'note' => 'Un PCMA avec statut "cleared" et signé est requis'
                ];
            }
        } catch (\Exception $e) {
            $pcmaStatus = [
                'status' => 'error',
                'message' => 'Erreur lors de la vérification PCMA',
                'note' => 'Impossible de vérifier le statut PCMA'
            ];
        }
        
        return view('modules.player-registration.edit', compact('player', 'clubs', 'associations', 'nationalities', 'positions', 'teams', 'pcmaStatus'));
    }

    /**
     * Met à jour un joueur existant
     */
    public function update(Request $request, Player $player)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'club_id' => 'required|exists:clubs,id',
            'association_id' => 'required|exists:associations,id',
            'player_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
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
            'club_id.required' => 'Le club est obligatoire.',
            'club_id.exists' => 'Le club sélectionné n\'existe pas.',
            'association_id.required' => 'L\'association est obligatoire.',
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
                'club_id' => $validated['club_id'],
                'association_id' => $validated['association_id'],
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

            return redirect()->route('player-registration.index')
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
            
            return redirect()->route('player-registration.index')
                ->with('success', 'Joueur supprimé avec succès !');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du joueur : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les dossiers médicaux d'un joueur
     */
    public function healthRecords(Player $player)
    {
        $player->load(['healthRecords', 'medicalNotes', 'injuries']);
        
        return view('modules.player-registration.health-records', compact('player'));
    }
}


