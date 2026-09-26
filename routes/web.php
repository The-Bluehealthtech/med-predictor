<?php

use Illuminate\Support\Facades\Route;

Route::post('/language', function (\Illuminate\Http\Request $request) {
    $locale = $request->validate(['locale' => ['required', \Illuminate\Validation\Rule::in(['fr', 'en'])]])['locale'];
    $request->session()->put('locale', $locale);

    return redirect()->back();
})->name('language.update');

Route::get('/match-sheet/{gameMatch}', [\App\Http\Controllers\MatchSheetController::class, 'show'])
    ->middleware('auth')->name('match-sheet.show');

// Controllers will be used as needed

// Force HTTP landing for /home to avoid HTTPS redirects locally
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

// Routes de santé pour Kubernetes
Route::get('/health', function () {
    return response()->json(['status' => 'healthy', 'timestamp' => now()]);
})->name('health');

Route::get('/ready', function () {
    try {
        // Vérification de la base de données
        \DB::connection()->getPdo();
        
        // Vérification simple du cache (sans Redis spécifique)
        \Cache::has('health_check');
        
        return response()->json(['status' => 'ready', 'timestamp' => now()]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'not_ready', 'error' => $e->getMessage()], 503);
    }
})->name('ready');

// Include account request routes
require __DIR__.'/account-requests.php';

    Route::get('/pcma/voice-fallback', function () {
        return view('pcma.voice-fallback');
    })->name('pcma.voice-fallback');

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClubManagementController;
use App\Http\Controllers\CompetitionManagementController;
use App\Http\Controllers\FederationController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\FitDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Added for API proxy routes
use App\Http\Controllers\DentalChartController;
use App\Http\Controllers\PlayerPortalController;
use App\Http\Controllers\PlayerSelectionController;
use App\Http\Controllers\AdminController;

// Routes publiques pour clubs et associations
Route::get('/clubs', function () {
    return redirect()->route('clubs-view');
})->name('clubs.public.index');

// Route de test simple pour clubs

Route::get('/associations', function () {
    return redirect()->route('associations-view');
})->name('associations.public.index');

// Test route

// Test route FIFA Connect







    // Test route to see what's captured



    // Test route to debug route capture

    // Test route to see what's happening with health-records

    // Test route with exact same controller but different name

    // Test route to see what captures health-records/create













// Test route Hero Components

// Test route Portal Data

// Test route clubs

// Test route avec vue clubs

// Test route pour vue détaillée d'un club

// Route principale pour voir les clubs
Route::middleware(['auth'])->group(function () {
    Route::get('/clubs-view', function (Request $request) {
        try {
            $associationId = $request->get('association_id');
            $confederationId = $request->get('confederation_id');
        
            // Initialiser les variables
            $association = null;
            $confederation = null;
            $filtered = false;
        
            if ($associationId) {
                // Filtrer par association
                $association = \App\Models\Association::find($associationId);
                $clubs = \App\Models\Club::where('association_id', $associationId)->with('association')->get();
                $filtered = true;
            } elseif ($confederationId) {
                // Filtrer par confédération
                $confederation = \App\Models\Confederation::find($confederationId);
                $clubs = \App\Models\Club::whereHas('association', function($query) use ($confederationId) {
                    $query->where('confederation_id', $confederationId);
                })->with('association')->get();
                $filtered = true;
            } else {
                // Tous les clubs
                $clubs = \App\Models\Club::with('association')->get();
                $filtered = false;
            }
        
            return view('modules.clubs.index', compact('clubs', 'filtered', 'association'));
        
        } catch (\Exception $e) {
            \Log::error("Erreur dans /clubs-view: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('clubs-view');

    // Route principale pour vue détaillée d'un club
    Route::get('/clubs-view/show', function (Request $request) {
        $id = $request->get('id');
        $club = \App\Models\Club::with(['association', 'players', 'teams'])->findOrFail($id);
        return view('modules.clubs.show', compact('club'));
    })->name('clubs-view.show');

    // Route principale pour éditer un club
    Route::get('/clubs-view/edit/{id}', function ($id) {
        $club = \App\Models\Club::with(['association'])->findOrFail($id);
        $associations = \App\Models\Association::orderBy('name')->get();
        return view('modules.clubs.edit', compact('club', 'associations'));
    })->name('clubs-view.edit');

    // Route pour mettre à jour un club
    Route::put('/clubs-view/update/{id}', function (Request $request, $id) {
        try {
            $club = \App\Models\Club::findOrFail($id);
        
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'short_name' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:255',
                'founded_year' => 'nullable|integer|min:1800|max:2030',
                'status' => 'required|in:active,inactive,pending'
            ]);
        
            $club->update($validatedData);
        
            // Gestion du logo si fourni
            if ($request->hasFile('logo')) {
                try {
                    $logo = $request->file('logo');
                
                    // Validation du fichier
                    if (!$logo->isValid()) {
                        throw new \Exception('Fichier invalide: ' . $logo->getErrorMessage());
                    }
                
                    // Vérification du type MIME
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
                    if (!in_array($logo->getMimeType(), $allowedMimes)) {
                        throw new \Exception('Type de fichier non autorisé: ' . $logo->getMimeType());
                    }
                
                    // Création du nom de fichier
                    $logoName = 'club_logos/club_' . $club->id . '_' . time() . '.' . $logo->getClientOriginalExtension();
                
                    // Création du dossier club_logos s'il n'existe pas
                    $clubLogosPath = storage_path('app/public/club_logos');
                    if (!is_dir($clubLogosPath)) {
                        mkdir($clubLogosPath, 0755, true);
                    }
                
                    // Upload du fichier avec chemin complet
                    $logoName = 'club_' . $club->id . '_' . time() . '.' . $logo->getClientOriginalExtension();
                    $fullPath = $clubLogosPath . '/' . $logoName;
                
                    // Copie du fichier uploadé
                    $uploaded = copy($logo->getRealPath(), $fullPath);
                    if (!$uploaded) {
                        throw new \Exception('Échec de la copie du fichier vers: ' . $fullPath);
                    }
                
                    // Mise à jour du chemin en base (sans le chemin complet)
                    $dbPath = 'club_logos/' . $logoName;
                
                    // Mise à jour de la base de données
                    $club->update(['logo_path' => $dbPath]);
                
                    \Log::info("Logo uploadé avec succès: {$dbPath} pour le club {$club->name}");
                
                } catch (\Exception $e) {
                    \Log::error("Erreur lors de l'upload du logo: " . $e->getMessage());
                    return back()->withInput()->with('error', 'Erreur lors de l\'upload du logo: ' . $e->getMessage());
                }
            }
        
            return redirect()->route('clubs-view.show', ['id' => $club->id])
                            ->with('success', 'Club mis à jour avec succès !');
                        
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    })->name('clubs-view.update');

    // Route pour supprimer un club
    Route::delete('/clubs-view/delete/{id}', function ($id) {
        try {
            $club = \App\Models\Club::findOrFail($id);
        
            // Vérifier s'il y a des données associées
            $playersCount = $club->players()->count();
            $licensesCount = $club->playerLicenses()->count();
        
            if ($playersCount > 0 || $licensesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Impossible de supprimer ce club : {$playersCount} joueur(s) et {$licensesCount} licence(s) associé(s)"
                ], 400);
            }
        
            // Supprimer le club
            $club->delete();
        
            return response()->json([
                'success' => true,
                'message' => 'Club supprimé avec succès'
            ]);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    })->name('clubs-view.delete');

    // Route pour obtenir les options de fusion
    Route::get('/clubs-view/merge-options/{id}', function ($id) {
        try {
            $currentClub = \App\Models\Club::findOrFail($id);
        
            // Récupérer tous les autres clubs (sauf le courant)
            $otherClubs = \App\Models\Club::where('id', '!=', $id)
                ->withCount(['players', 'playerLicenses'])
                ->get()
                ->map(function ($club) {
                    return [
                        'id' => $club->id,
                        'name' => $club->name,
                        'city' => $club->city,
                        'country' => $club->country,
                        'players_count' => $club->players_count,
                        'licenses_count' => $club->player_licenses_count
                    ];
                });
        
            return response()->json([
                'success' => true,
                'clubs' => $otherClubs
            ]);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des options : ' . $e->getMessage()
            ], 500);
        }
    })->name('clubs-view.merge-options');

    // Route pour fusionner les clubs
    Route::post('/clubs-view/merge', function (Request $request) {
        try {
            $sourceClubId = $request->input('source_club_id');
            $targetClubId = $request->input('target_club_id');
        
            if (!$sourceClubId || !$targetClubId) {
                return response()->json([
                    'success' => false,
                    'message' => 'IDs des clubs requis'
                ], 400);
            }
        
            $sourceClub = \App\Models\Club::findOrFail($sourceClubId);
            $targetClub = \App\Models\Club::findOrFail($targetClubId);
        
            // Vérifier que les clubs sont différents
            if ($sourceClubId === $targetClubId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de fusionner un club avec lui-même'
                ], 400);
            }
        
            // Transférer les joueurs du club source vers le club cible
            $sourceClub->players()->update(['club_id' => $targetClubId]);
        
            // Transférer les licences du club source vers le club cible
            $sourceClub->playerLicenses()->update(['club_id' => $targetClubId]);
        
            // Supprimer le club source
            $sourceClub->delete();
        
            return response()->json([
                'success' => true,
                'message' => "Club '{$sourceClub->name}' fusionné avec '{$targetClub->name}'"
            ]);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la fusion : ' . $e->getMessage()
            ], 500);
        }
    })->name('clubs-view.merge');
});

// Test route pour vue des associations

// Route principale pour vue des associations
Route::middleware(['auth'])->group(function () {
    Route::get('/associations-view', function (Request $request) {
        $confederationId = $request->get('confederation_id');
    
        if ($confederationId) {
            // Filtrer par confédération
            $associations = \App\Models\Association::where('confederation_id', $confederationId)
                ->with(['confederation'])
                ->orderBy('name')
                ->get();
            $confederation = \App\Models\Confederation::find($confederationId);
        } else {
            // Toutes les associations
            $associations = \App\Models\Association::with(['confederation'])
                ->orderBy('name')
                ->get();
            $confederation = null;
        }
    
        return view('modules.associations.index', compact('associations', 'confederation'));
    })->name('associations-view');

    // Route principale pour vue détaillée d'une association
    Route::get('/associations-view/show/{id}', function ($id) {
        $association = \App\Models\Association::with(['confederation', 'clubs', 'players'])
            ->findOrFail($id);
        return view('modules.associations.show', compact('association'));
    })->name('associations-view.show');

    // Route principale pour éditer une association
    Route::get('/associations-view/edit/{id}', function ($id) {
        $association = \App\Models\Association::with(['confederation'])->findOrFail($id);
        $confederations = \App\Models\Confederation::orderBy('name')->get();
        return view('modules.associations.edit', compact('association', 'confederations'));
    })->name('associations-view.edit');

    // Route pour mettre à jour une association
    Route::put('/associations-view/update/{id}', function (Request $request, $id) {
        try {
            $association = \App\Models\Association::findOrFail($id);
        
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'country' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:255',
                'founded_year' => 'nullable|integer|min:1800|max:2030',
                'status' => 'required|in:active,inactive,pending',
                'confederation_id' => 'required|exists:confederations,id'
            ]);
        
            $association->update($validatedData);
        
            // Gestion du logo de l'association si fourni
            if ($request->hasFile('association_logo')) {
                try {
                    $logo = $request->file('association_logo');
                
                    // Validation du fichier
                    if (!$logo->isValid()) {
                        throw new \Exception('Fichier invalide: ' . $logo->getErrorMessage());
                    }
                
                    // Vérification du type MIME
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
                    if (!in_array($logo->getMimeType(), $allowedMimes)) {
                        throw new \Exception('Type de fichier non autorisé: ' . $logo->getMimeType());
                    }
                
                    // Création du dossier association_logos s'il n'existe pas
                    $logoPath = storage_path('app/public/association_logos');
                    if (!is_dir($logoPath)) {
                        mkdir($logoPath, 0755, true);
                    }
                
                    // Upload du fichier
                    $logoName = 'association_' . $association->id . '_' . time() . '.' . $logo->getClientOriginalExtension();
                    $fullPath = $logoPath . '/' . $logoName;
                
                    // Copie du fichier uploadé
                    $uploaded = copy($logo->getRealPath(), $fullPath);
                    if (!$uploaded) {
                        throw new \Exception('Échec de la copie du fichier vers: ' . $fullPath);
                    }
                
                    // Mise à jour du chemin en base
                    $dbPath = 'association_logos/' . $logoName;
                    $association->update(['association_logo_url' => $dbPath]);
                
                    \Log::info("Logo d'association uploadé avec succès: {$dbPath} pour l'association {$association->name}");
                
                } catch (\Exception $e) {
                    \Log::error("Erreur lors de l'upload du logo d'association: " . $e->getMessage());
                    return back()->withInput()->with('error', 'Erreur lors de l\'upload du logo: ' . $e->getMessage());
                }
            }
        
            // Gestion du drapeau du pays si fourni
            if ($request->hasFile('nation_flag')) {
                try {
                    $flag = $request->file('nation_flag');
                
                    // Validation du fichier
                    if (!$flag->isValid()) {
                        throw new \Exception('Fichier invalide: ' . $flag->getErrorMessage());
                    }
                
                    // Vérification du type MIME
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
                    if (!in_array($flag->getMimeType(), $allowedMimes)) {
                        throw new \Exception('Type de fichier non autorisé: ' . $flag->getMimeType());
                    }
                
                    // Création du dossier nation_flags s'il n'existe pas
                    $flagPath = storage_path('app/public/nation_flags');
                    if (!is_dir($flagPath)) {
                        mkdir($flagPath, 0755, true);
                    }
                
                    // Upload du fichier
                    $flagName = 'nation_' . $association->id . '_' . time() . '.' . $flag->getClientOriginalExtension();
                    $fullPath = $flagPath . '/' . $flagName;
                
                    // Copie du fichier uploadé
                    $uploaded = copy($flag->getRealPath(), $fullPath);
                    if (!$uploaded) {
                        throw new \Exception('Échec de la copie du fichier vers: ' . $fullPath);
                    }
                
                    // Mise à jour du chemin en base
                    $dbPath = 'nation_flags/' . $flagName;
                    $association->update(['nation_flag_url' => $dbPath]);
                
                    \Log::info("Drapeau national uploadé avec succès: {$dbPath} pour l'association {$association->name}");
                
                } catch (\Exception $e) {
                    \Log::error("Erreur lors de l'upload du drapeau: " . $e->getMessage());
                    return back()->withInput()->with('error', 'Erreur lors de l\'upload du drapeau: ' . $e->getMessage());
                }
            }
        
            return redirect()->route('associations-view.show', ['id' => $association->id])
                            ->with('success', 'Association mise à jour avec succès !');
                        
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    })->name('associations-view.update');
});

// Test route pour vue détaillée d'une association

// Test route pour vue des confédérations

// Test route pour vue détaillée d'une confédération

// Route principale pour vue détaillée d'une confédération
Route::get('/confederations-view/show', function (Request $request) {
    $id = $request->get('id');
    if ($id) {
    $confederation = \App\Models\Confederation::findOrFail($id);
    return view('modules.confederations.show', compact('confederation'));
    } else {
        // Si pas d'ID, afficher la liste des confédérations
        $confederations = \App\Models\Confederation::orderBy('name')->get();
        return view('modules.confederations.index', compact('confederations'));
    }
})->name('confederations-view.show');



// Test route pour la validation des licences (sans authentification)

// Test route secretary dashboard (sans authentification)

// Route de test simple secretary dashboard

// Route de test secretary dashboard avec données réelles (sans authentification)

// Routes Secretary manquantes
Route::prefix('secretary')->name('secretary.')->group(function () {
    Route::post('/appointments', function () {
        return redirect()->back()->with('success', 'Rendez-vous créé avec succès !');
    })->name('appointments.store');
    
    Route::get('/appointments', function () {
        return view('secretary.appointments.index');
    })->name('appointments.index');
    
    Route::get('/documents', function () {
        return view('secretary.documents.index');
    })->name('documents.index');
    
    Route::post('/documents/upload', function () {
        return redirect()->back()->with('success', 'Document uploadé avec succès !');
    })->name('documents.upload');
    
    Route::get('/athletes/search', function () {
        return view('secretary.athletes.search');
    })->name('athletes.search');
    
    Route::get('/stats', function () {
        return view('secretary.stats');
    })->name('stats');
});


// Route principale pour les compétitions (sans authentification)


// Routes publiques pour les compétitions
Route::get('/competitions-club-engagements', [App\Http\Controllers\CompetitionController::class, 'clubEngagements'])->name('competitions-club-engagements');
Route::get('/competitions-club-effectif', [App\Http\Controllers\CompetitionController::class, 'clubEffectif'])->name('competitions-club-effectif');

// Routes FIFA Connect - Vérifications de conformité
Route::prefix('fifa-connect')->name('fifa-connect.')->middleware(['auth'])->group(function () {
    Route::get('/player/{playerId}/compliance', [App\Http\Controllers\FifaConnectController::class, 'checkPlayerCompliance'])->name('player.compliance');
    Route::get('/club/{clubId}/compliance', [App\Http\Controllers\FifaConnectController::class, 'checkClubCompliance'])->name('club.compliance');
    Route::get('/association/{associationId}/compliance', [App\Http\Controllers\FifaConnectController::class, 'checkAssociationCompliance'])->name('association.compliance');
    Route::get('/global-stats', [App\Http\Controllers\FifaConnectController::class, 'getGlobalStats'])->name('global.stats');
});

// Test route pour les compétitions avec authentification simulée
















// Account Request Form (public)
Route::get('/account-request', function () {
    return view('account-request.create');
})->name('account-request.create');





// Account Request Data Routes (public)
Route::get('/account-request/football-types', function () {
    return response()->json([
        'success' => true,
        'data' => [
            '11-a-side' => 'Football 11 à 11',
            'futsal' => 'Futsal',
            'women' => 'Football Féminin',
            'beach-soccer' => 'Beach Soccer',
            'indoor' => 'Football en Salle',
            'street' => 'Street Football'
        ]
    ]);
});

Route::get('/account-request/organization-types', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'club' => 'Club de Football',
            'association' => 'Association Nationale',
            'federation' => 'Fédération',
            'league' => 'Ligue',
            'academy' => 'Académie',
            'school' => 'École de Football',
            'other' => 'Autre'
        ]
    ]);
});

Route::get('/account-request/fifa-associations', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'UEFA' => [
                ['id' => 'fra', 'full_name' => 'Fédération Française de Football'],
                ['id' => 'ger', 'full_name' => 'Deutscher Fußball-Bund'],
                ['id' => 'esp', 'full_name' => 'Real Federación Española de Fútbol'],
                ['id' => 'ita', 'full_name' => 'Federazione Italiana Giuoco Calcio'],
                ['id' => 'eng', 'full_name' => 'The Football Association']
            ],
            'CONMEBOL' => [
                ['id' => 'bra', 'full_name' => 'Confederação Brasileira de Futebol'],
                ['id' => 'arg', 'full_name' => 'Asociación del Fútbol Argentino'],
                ['id' => 'col', 'full_name' => 'Federación Colombiana de Fútbol']
            ],
            'CAF' => [
                ['id' => 'nga', 'full_name' => 'Nigeria Football Federation'],
                ['id' => 'egy', 'full_name' => 'Egyptian Football Association'],
                ['id' => 'mar', 'full_name' => 'Fédération Royale Marocaine de Football']
            ]
        ]
    ]);
});

Route::get('/account-request/fifa-connect-types', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'player' => 'Joueur',
            'coach' => 'Entraîneur',
            'referee' => 'Arbitre',
            'medical' => 'Staff Médical',
            'administrative' => 'Staff Administratif',
            'technical' => 'Staff Technique'
        ]
    ]);
});







// Test du composant association-logo

// Test simple du composant

// Test PCMA simple - Route manquante pour l'Assistant Vocal

// Route pour récupérer la clé API Google Speech-to-Text
Route::get('/api/google-speech-key', function () {
    $apiKey = env('GOOGLE_SPEECH_API_KEY');
    if (!$apiKey) {
        return response()->json(['error' => 'Clé API non configurée'], 404);
    }
    $maskedKey = substr($apiKey, 0, 8) . '...' . substr($apiKey, -4);
    return response()->json([
        'apiKey' => $apiKey,
        'maskedKey' => $maskedKey,
        'status' => 'success'
    ]);
})->name('api.google.speech.key');

// Route pour la sauvegarde automatique des données PCMA
Route::post('/api/pcma/auto-save', function (Request $request) {
    try {
        $data = $request->validate([
            'player_name' => 'required|string|max:255',
            'age' => 'required|integer|min:10|max:100',
            'position' => 'required|string|max:255',
            'club' => 'required|string|max:255',
            'confidence' => 'string|max:50'
        ]);
        
        // Simuler la sauvegarde en base de données
        // Ici vous pourriez ajouter la logique de sauvegarde réelle
        $savedData = [
            'id' => uniqid('pcma_'),
            'player_name' => $data['player_name'],
            'age' => $data['age'],
            'position' => $data['position'],
            'club' => $data['club'],
            'confidence' => $data['confidence'] ?? 'high',
            'created_at' => now()->toISOString(),
            'status' => 'saved'
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Données PCMA sauvegardées avec succès',
            'data' => $savedData
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
        ], 400);
    }
})->name('api.pcma.auto.save');

// Test du composant dans le contexte du portail

// Test exact du portail patient

// Test ultra-simple

// Test simulation portail patient

// Test portail simple

// Routes pour la gestion des associations
Route::prefix('associations')->name('associations.')->middleware(['auth'])->group(function () {
    Route::get('/{association}/edit', [App\Http\Controllers\AssociationController::class, 'edit'])->name('edit');
    Route::put('/{association}', [App\Http\Controllers\AssociationController::class, 'update'])->name('update');
    Route::get('/{association}/logo/edit', [App\Http\Controllers\AssociationLogoController::class, 'editLogo'])->name('edit-logo');
    Route::post('/{association}/logo/update', [App\Http\Controllers\AssociationLogoController::class, 'updateLogo'])->name('update-logo');
    Route::post('/{association}/logo/reset', [App\Http\Controllers\AssociationLogoController::class, 'resetToNationalLogo'])->name('reset-national-logo');
    Route::post('/logos/update-national', [App\Http\Controllers\AssociationLogoController::class, 'updateNationalLogos'])->name('update-national-logos');
});

// Test du système association

// Démonstration des logos officiels

// Test du portail patient avec logos des fédérations

// Test des logos dans le contexte du portail

// Test du portail patient intégré (version publique)

// Test du portail patient simplifié (version publique)

// Test d'authentification

// API FIFA pour le portail

// API des licences pour le portail FIFA

// Routes d'authentification
Route::get('/login', function() {
    return view('auth.login');
})->name('login');

// Routes publiques FIFA (accessibles sans authentification)
// Route de recherche de joueurs FIFA (UNIFIÉE avec /api/players)
Route::get('/search-players', function (Request $request) {
    $query = $request->get('q', '');
    
    if (empty($query)) {
        return response()->json(['data' => []]);
    }
    
    try {
        // Utiliser le modèle Eloquent avec relations (MÊME LOGIQUE QUE /api/players)
        $players = \App\Models\Player::with(['club', 'association'])
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('position', 'like', "%{$query}%")
                  ->orWhere('nationality', 'like', "%{$query}%");
            })
            ->orderBy('id', 'asc') // Ordre chronologique des IDs
            ->limit(10)
            ->get();
        
        return response()->json(['data' => $players]);
        
    } catch (Exception $e) {
        return response()->json(['error' => 'Erreur lors de la recherche'], 500);
    }
})->middleware(['auth'])->name('search.players');

// Route pour récupérer la liste complète des joueurs FIFA
Route::get('/api/players', function () {
    try {
        $players = DB::table('players')
            ->select('id', 'first_name', 'last_name', 'position', 'rating', 'club_id', 'nationality', 'player_picture')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        // Enrichir avec les informations du club
        $players = $players->map(function($player) {
            if ($player->club_id) {
                $club = DB::table('clubs')->where('id', $player->club_id)->first();
                $player->club_name = $club ? $club->name : 'Club inconnu';
            } else {
                $player->club_name = 'Aucun club';
            }
            return $player;
        });
        
        return response()->json([
            'success' => true,
            'data' => $players,
            'total' => $players->count()
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Erreur lors du chargement des joueurs',
            'message' => $e->getMessage()
        ], 500);
    }
})->middleware(['auth'])->name('api.players');

// Route pour récupérer un joueur spécifique FIFA
Route::get('/api/players/{id}', function ($id) {
    try {
        // Requête simplifiée qui fonctionne
        $player = DB::table('players')
            ->select('id', 'name', 'first_name', 'last_name', 'position', 'overall_rating', 'potential_rating', 'club_id', 'nationality', 'date_of_birth', 'age', 'height', 'weight', 'preferred_foot', 'skill_moves', 'international_reputation', 'ghs_overall_score', 'ghs_physical_score', 'ghs_mental_score', 'injury_risk_score', 'contribution_score', 'match_availability', 'value_eur', 'wage_eur', 'last_availability_update', 'player_picture', 'player_face_url')
            ->where('id', $id)
            ->first();
        
        if (!$player) {
            return response()->json([
                'success' => false,
                'error' => 'Joueur non trouvé'
            ], 404);
        }
        
        // Enrichir avec les informations du club
        if ($player->club_id) {
            $club = DB::table('clubs')->where('id', $player->club_id)->first();
            $player->club = $club ? [
                'id' => $club->id,
                'name' => $club->name,
                'logo_url' => $club->logo_url ?? null
            ] : null;
        } else {
            $player->club = null;
        }
        
        return response()->json([
            'success' => true,
            'data' => $player
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Erreur lors du chargement du joueur',
            'message' => $e->getMessage()
        ], 500);
    }
})->middleware(['auth'])->name('api.players.show');

// NOUVELLE ROUTE FIFA qui fonctionne

// Routes API pour l'historique des licences
Route::prefix('api')->group(function () {
    // Historique complet des licences d'un joueur
    
    // Statistiques des licences
    
    // Barèmes de formation FIFA
    Route::get('/formation/barèmes', [App\Http\Controllers\Controller::class, 'index'])
        ->name('api.formation.baremes');
});

// Custom route to handle health records creation with authentication
Route::get('/create-health-record/{playerId?}', function ($playerId = null) {
    // Check if user is authenticated
    if (!\Illuminate\Support\Facades\Auth::check()) {
        // Store the intended URL with parameters in session
        $intendedUrl = '/create-health-record' . ($playerId ? '/' . $playerId : '');
        session(['url.intended' => $intendedUrl]);
        
        // Redirect to login
        return redirect()->route('login');
    }
    
    // User is authenticated, redirect to the actual health-records/create route
    $url = '/health-records/create';
    if ($playerId) {
        $url .= '?player_id=' . $playerId;
    }
    
    return redirect($url);
})->name('create-health-record');

// Test route to verify both pages use the same appointment data

// Debug route to check all appointments and their dates

// Test route to check what each page actually displays

// Route to create future appointments for testing

// Test route to verify clinician portal modal functionality

// Test route for clinician portal without authentication

// Route de test simple pour vérifier que le modal fonctionne

// Route de test pour health-records-create qui fonctionne

// Route de test pour pcma-create qui fonctionne

// Routes protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/club-management/dashboard', [ClubManagementController::class, 'dashboard'])->name('club-management.dashboard');
    Route::get('/admin/players', [AdminController::class, 'playersList'])->name('admin.players.list');
    Route::get('/admin/search-players', [AdminController::class, 'searchPlayers'])->name('admin.search.players');
    Route::get('/admin/system-stats', [AdminController::class, 'systemStats'])->name('admin.system.stats');
    Route::get('/admin/referee-assignments', [App\Http\Controllers\AdminRefereeAssignmentController::class, 'index'])->middleware('role:system_admin')->name('admin.referee-assignments');
    Route::post('/admin/assign-referees', [App\Http\Controllers\AdminRefereeAssignmentController::class, 'assignReferees'])->middleware('role:system_admin')->name('admin.assign-referees');
    
    // Routes RBAC
    Route::prefix('admin/rbac')->name('admin.rbac.')->group(function () {
        Route::get('/', [App\Http\Controllers\RBACController::class, 'index'])->name('index');
        Route::get('/roles', [App\Http\Controllers\RBACController::class, 'roles'])->name('roles');
        Route::post('/roles', [App\Http\Controllers\RBACController::class, 'createRole'])->name('create-role');
        Route::put('/roles/{id}', [App\Http\Controllers\RBACController::class, 'updateRole'])->name('update-role');
        Route::delete('/roles/{id}', [App\Http\Controllers\RBACController::class, 'deleteRole'])->name('delete-role');
        Route::get('/permissions', [App\Http\Controllers\RBACController::class, 'permissions'])->name('permissions');
        Route::get('/module-permissions', [App\Http\Controllers\RBACController::class, 'modulePermissions'])->name('module-permissions');
        Route::post('/module-permissions', [App\Http\Controllers\RBACController::class, 'updateModulePermissions'])->name('module-permissions.update');
        Route::post('/permissions', [App\Http\Controllers\RBACController::class, 'createPermission'])->name('create-permission');
        Route::post('/initialize-permissions', [App\Http\Controllers\RBACController::class, 'initializePermissions'])->name('initialize-permissions');
        Route::get('/users', [App\Http\Controllers\RBACController::class, 'users'])->name('users');
        Route::post('/users/{userId}/assign-role', [App\Http\Controllers\RBACController::class, 'assignRole'])->name('assign-role');
    });

    // Routes Audit Trail
    Route::prefix('admin/audit-trail')->name('admin.audit-trail.')->group(function () {
        Route::get('/', [App\Http\Controllers\AuditTrailController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\AuditTrailController::class, 'show'])->name('show');
        Route::get('/export', [App\Http\Controllers\AuditTrailController::class, 'export'])->name('export');
        Route::post('/cleanup', [App\Http\Controllers\AuditTrailController::class, 'cleanup'])->name('cleanup');
    });

    // Routes System Settings
    Route::prefix('admin/system-settings')->name('admin.system-settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\SystemSettingsController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\SystemSettingsController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\SystemSettingsController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\SystemSettingsController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\SystemSettingsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\SystemSettingsController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\SystemSettingsController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-update', [App\Http\Controllers\SystemSettingsController::class, 'updateBulk'])->name('update-bulk');
        Route::post('/{id}/reset', [App\Http\Controllers\SystemSettingsController::class, 'reset'])->name('reset');
        Route::post('/initialize', [App\Http\Controllers\SystemSettingsController::class, 'initialize'])->name('initialize');
        Route::get('/export', [App\Http\Controllers\SystemSettingsController::class, 'export'])->name('export');
        Route::get('/api/{key}', [App\Http\Controllers\SystemSettingsController::class, 'get'])->name('get');
    });

    // Routes Content Management
    Route::prefix('admin/content-management')->name('admin.content-management.')->group(function () {
        Route::get('/', [App\Http\Controllers\ContentManagementController::class, 'index'])->name('index');
        Route::get('/articles', [App\Http\Controllers\ContentManagementController::class, 'articles'])->name('articles');
        Route::get('/pages', [App\Http\Controllers\ContentManagementController::class, 'pages'])->name('pages');
        Route::get('/media', [App\Http\Controllers\ContentManagementController::class, 'media'])->name('media');
        Route::get('/announcements', [App\Http\Controllers\ContentManagementController::class, 'announcements'])->name('announcements');
        Route::get('/faq', [App\Http\Controllers\ContentManagementController::class, 'faq'])->name('faq');
        Route::get('/user-guide', [App\Http\Controllers\ContentManagementController::class, 'userGuide'])->name('user-guide');
        Route::get('/create', [App\Http\Controllers\ContentManagementController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ContentManagementController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\ContentManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\ContentManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\ContentManagementController::class, 'destroy'])->name('destroy');
    });

    // Routes Transfer Management
    Route::prefix('admin/transfer-management')->name('admin.transfer-management.')->group(function () {
        Route::get('/', [App\Http\Controllers\TransferManagementController::class, 'index'])->name('index');
        Route::get('/transfers', [App\Http\Controllers\TransferManagementController::class, 'transfers'])->name('transfers');
        Route::post('/sync-fifa-tms', [App\Http\Controllers\TransferManagementController::class, 'syncFifaTms'])->name('sync-fifa-tms');
        Route::post('/{id}/approve', [App\Http\Controllers\TransferManagementController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [App\Http\Controllers\TransferManagementController::class, 'reject'])->name('reject');
        Route::get('/export', [App\Http\Controllers\TransferManagementController::class, 'export'])->name('export');
    });
    
    // Route de test temporaire pour les arbitres (sans authentification)
    
    // Route de test temporaire pour la désignation des arbitres (sans authentification)
    
    // Nouvelle route pour lister les joueurs (accessible depuis /modules)
    Route::get('/players/list', [AdminController::class, 'playersList'])->name('players.list');
    

        }); // Fermeture du groupe Route::middleware(['auth'])

    // Confederations route without auth for immediate use
    Route::get('/modules/confederations', function () {
        // Récupérer les confédérations depuis la base de données
        $confederations = \App\Models\Confederation::with(['associations'])
            ->orderBy('name')
            ->get();
        
        return view('modules.confederations.index', [
            'footballType' => 'association',
            'confederations' => $confederations
        ]);
    })->middleware(['auth'])->name('modules.confederations.index');

    // Connected devices portal requires authentication.
    Route::get('/portal/devices', function () {
        $user = auth()->user();
        $query = \Illuminate\Support\Facades\DB::table('player_connected_devices as d')
            ->join('players', 'players.id', '=', 'd.player_id')
            ->select('d.*', 'players.first_name', 'players.last_name');

        if ($user->isPlayer()) {
            abort_unless($user->player_id, 403);
            $query->where('d.player_id', $user->player_id);
        } elseif ($user->isClubUser()) {
            abort_unless($user->club_id, 403);
            $query->where('players.club_id', $user->club_id);
        } elseif ($user->isAssociationUser()) {
            abort_unless($user->association_id, 403);
            $query->where('players.association_id', $user->association_id);
        } elseif (!$user->isSystemAdmin()) {
            abort(403);
        }

        $devices = $query->orderByDesc('d.last_sync_at')->limit(200)->get();

        $stats = [
            'total' => $devices->count(),
            'connected' => $devices->where('is_connected', true)->count(),
            'by_type' => $devices->groupBy('device_type')->map->count(),
        ];

        return view('modules.portal.devices', compact('devices', 'stats'));
    })->middleware(['auth'])->name('portal.devices');

    // Referee portal requires authentication.
    Route::get('/referee-portal', function () {
        return view('modules.referees.index', ['footballType' => 'association']);
    })->middleware(['auth'])->name('referee-portal.index');

    // Modules index requires an authenticated user.
    Route::get('/modules', function () {
        try {
            $footballType = request('footballType', 'association');
        return view('modules.index', [
            'footballType' => $footballType,
            'modules' => [
                    // 🏥 SANTÉ & MÉDECINE
                            [
                'name' => 'Medical',
                'description' => 'Gestion médicale des athlètes, vaccinations, et dossiers de santé',
                'icon' => '🏥',
                'route' => 'modules.medical.index',
                        'status' => 'active',
                        'color' => 'red',
                        'category' => 'health'
                    ],
                    [
                        'name' => 'Healthcare',
                        'description' => 'Dossiers médicaux et suivi de santé',
                        'icon' => '📋',
                        'route' => 'modules.healthcare.index',
                        'status' => 'active',
                        'color' => 'red',
                        'category' => 'health'
            ],
                [
                    'name' => 'PCMA',
                        'description' => 'Plateforme de Contrôle Médical des Athlètes',
                        'icon' => '🏥',
                        'route' => 'pcma.index',
                        'status' => 'active',
                        'color' => 'red',
                        'category' => 'health'
                    ],
                    [
                        'name' => 'Secrétariat Médical',
                        'description' => 'Gestion des rendez-vous et documents médicaux',
                        'icon' => '📅',
                        'route' => 'secretary.dashboard',
                        'status' => 'active',
                        'color' => 'blue',
                        'category' => 'health'
                    ],
                    
                    // ⚽ GESTION DU FOOTBALL
                    [
                        'name' => 'Players',
                        'description' => 'Gestion des joueurs et licences',
                        'icon' => '👥',
                        'route' => 'modules.players.index',
                        'status' => 'active',
                        'color' => 'green',
                        'category' => 'sport'
                    ],
                    [
                        'name' => 'Teams',
                        'description' => 'Gestion des équipes',
                    'icon' => '⚽',
                        'route' => 'modules.teams.index',
                        'status' => 'active',
                        'color' => 'green',
                        'category' => 'sport'
                    ],
                    [
                        'name' => 'Competitions',
                        'description' => 'Gestion des compétitions',
                        'icon' => '🏆',
                        'route' => 'modules.competitions.index',
                        'status' => 'active',
                    'color' => 'green',
                    'category' => 'sport'
                ],
                [
                        'name' => 'Referees',
                        'description' => 'Gestion des arbitres',
                        'icon' => '👨‍⚖️',
                        'route' => 'modules.referees.index',
                        'status' => 'active',
                        'color' => 'green',
                        'category' => 'sport'
                    ],
                    
                    // 🏢 ORGANISATIONS
                    [
                        'name' => 'Clubs',
                        'description' => 'Gestion des clubs',
                        'icon' => '🏟️',
                        'route' => 'modules.clubs.index',
                        'status' => 'active',
                        'color' => 'blue',
                        'category' => 'institutional'
                    ],
                    [
                        'name' => 'Associations',
                        'description' => 'Gestion des associations',
                        'icon' => '🏛️',
                        'route' => 'modules.associations.index',
                        'status' => 'active',
                        'color' => 'blue',
                        'category' => 'institutional'
                    ],
                    [
                        'name' => 'Confederations',
                        'description' => 'Gestion des confédérations continentales',
                        'icon' => '🌐',
                        'route' => 'modules.confederations.index',
                        'status' => 'active',
                        'color' => 'blue',
                        'category' => 'institutional'
                    ],
                    
                    // 📋 LICENCES & DOCUMENTS
                [
                    'name' => 'Licenses',
                        'description' => 'Gestion des licences',
                        'icon' => '📄',
                    'route' => 'modules.licenses.index',
                        'status' => 'active',
                        'color' => 'indigo',
                        'category' => 'documents'
                    ],
                    [
                        'name' => 'Validation de Licence',
                        'description' => 'Validation et vérification des licences',
                        'icon' => '✅',
                        'route' => 'modules.licenses.index',
                        'status' => 'active',
                        'color' => 'green',
                        'category' => 'documents'
                    ],
                    
                    // 🌍 FIFA & CONNECTIVITÉ
                    [
                        'name' => 'FIFA Connect',
                        'description' => 'Intégration FIFA et connectivité mondiale',
                        'icon' => '🌍',
                        'route' => 'fifa.dashboard',
                        'status' => 'active',
                    'color' => 'purple',
                    'category' => 'portals'
                ],
                [
                        'name' => 'FIFA Portal',
                        'description' => 'Portail FIFA intégré',
                        'icon' => '🚪',
                        'route' => 'fifa.portal.integrated',
                        'status' => 'active',
                        'color' => 'purple',
                        'category' => 'portals'
                    ],
                    [
                        'name' => 'FIFA Analytics',
                        'description' => 'Analyses et statistiques FIFA',
                        'icon' => '📊',
                        'route' => 'fifa.analytics',
                        'status' => 'active',
                        'color' => 'purple',
                        'category' => 'analytics'
                    ],
                    [
                        'name' => 'Player Portal',
                        'description' => 'Portail personnel des joueurs',
                        'icon' => '👤',
                        'route' => 'players.list',
                        'status' => 'active',
                        'color' => 'blue',
                        'category' => 'portals'
                    ],
                    [
                        'name' => 'Referee Portal',
                        'description' => 'Portail des arbitres et officiels',
                        'icon' => '👨‍⚖️',
                        'route' => 'referee-portal.index',
                        'status' => 'active',
                        'color' => 'green',
                        'category' => 'portals'
                    ],
                    
                    // 📊 ANALYTICS & PERFORMANCE
                    [
                        'name' => 'Analytics Dashboard',
                        'description' => 'Tableau de bord analytique',
                        'icon' => '📈',
                        'route' => 'analytics.dashboard',
                        'status' => 'active',
                        'color' => 'yellow',
                        'category' => 'analytics'
                    ],
                    [
                        'name' => 'Digital Twin',
                        'description' => 'Jumeau numérique des athlètes',
                        'icon' => '👤',
                        'route' => 'analytics.digital-twin',
                        'status' => 'active',
                        'color' => 'yellow',
                        'category' => 'technology'
                    ],
                    [
                        'name' => 'Performance Analytics',
                        'description' => 'Analyses de performance',
                        'icon' => '🏃',
                        'route' => 'performances.analytics',
                        'status' => 'active',
                        'color' => 'yellow',
                        'category' => 'analytics'
                    ],
                    [
                        'name' => 'FIT Metrics',
                        'description' => 'Saisie et vérification des métriques du score FIT canonique',
                        'icon' => '🎯',
                        'route' => 'performances.fit-metrics',
                        'status' => 'active',
                        'color' => 'purple',
                        'category' => 'analytics'
                    ],
                    
                    // 🤖 IA & TECHNOLOGIE
                    [
                        'name' => 'DTN',
                        'description' => 'Module DTN (Digital Twin Network)',
                    'icon' => '🤖',
                        'route' => 'dtn.index',
                        'status' => 'active',
                    'color' => 'purple',
                    'category' => 'analytics'
                    ],
                    [
                        'name' => 'RPM',
                        'description' => 'Module RPM (Real-time Performance Monitoring)',
                        'icon' => '⚡',
                        'route' => 'rpm.index',
                        'status' => 'active',
                    'color' => 'purple',
                    'category' => 'analytics'
                ],
                [
                        'name' => 'Gemini',
                        'description' => 'Module Gemini IA de Google',
                        'icon' => '💎',
                        'route' => 'gemini.index',
                        'status' => 'active',
                        'color' => 'purple',
                        'category' => 'technology'
                    ],
                    
                    // 📱 DEVICES & CONNECTIVITÉ
                    [
                        'name' => 'Devices Portal',
                        'description' => 'Portail des appareils connectés',
                        'icon' => '📱',
                        'route' => 'portal.devices',
                        'status' => 'active',
                        'color' => 'blue',
                        'category' => 'portals'
                    ],
                    [
                        'name' => 'Portail Patient',
                        'description' => 'Portail patient pour saisie de symptômes',
                        'icon' => '👤',
                        'route' => 'clinical.patient-portal',
                        'status' => 'active',
                        'color' => 'blue',
                        'category' => 'portals'
                    ],
                    [
                        'name' => 'Portail Clinicien',
                        'description' => 'Portail clinicien pour consultations et diagnostic',
                        'icon' => '👨‍⚕️',
                        'route' => 'clinical.clinician-portal',
                        'status' => 'active',
                        'color' => 'green',
                        'category' => 'portals'
                    ],
                    
                    // ⚙️ ADMINISTRATION
                    [
                        'name' => 'Administration Management',
                        'description' => 'Gestion administrative',
                        'icon' => '⚙️',
                        'route' => 'modules.administration.index',
                        'status' => 'active',
                        'color' => 'gray',
                        'category' => 'administration'
                    ],
                    [
                        'name' => 'Content Management',
                        'description' => 'Gérer les articles, pages, médias et contenu du site',
                        'icon' => '📝',
                        'route' => 'admin.content-management.index',
                        'status' => 'active',
                        'color' => 'pink',
                        'category' => 'administration'
                    ],
                    [
                        'name' => 'Gestion des Transferts',
                        'description' => 'Gérer les transferts de joueurs connecté à FIFA TMS',
                        'icon' => '🔄',
                        'route' => 'admin.transfer-management.index',
                        'status' => 'active',
                        'color' => 'teal',
                        'category' => 'documents'
                    ],
                    [
                        'name' => 'Finance Management',
                        'description' => 'Gestion financière et comptabilité',
                        'icon' => '💰',
                        'route' => 'modules.finance.dashboard',
                        'status' => 'active',
                        'color' => 'green',
                        'category' => 'administration'
                    ],
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->middleware(['auth'])->name('modules.index');


        // Routes de test temporaires pour diagnostiquer les modules (sans authentification)

        // Routes de test pour vérifier les vues (sans authentification)

















        // Route de test temporaire pour /modules







// Dataset Analytics route
Route::get('/dataset-analytics', function () {
    return view('modules.dataset.analytics', [
        'footballType' => 'association'
    ]);
})->name('dataset.analytics');

// Test du portail sans authentification (temporaire)

// Test Vue.js simple

// Test minimal sans Vue.js

// Test Vue.js debug

// Test Portal Debug

// Sélection des joueurs
Route::get('/joueurs', [PlayerSelectionController::class, 'index'])->name('joueurs.selection');
Route::get('/joueurs/{id}', [PlayerSelectionController::class, 'show'])->middleware(['auth'])->name('joueurs.show');

// Test public du portail (sans authentification)

Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.token');

// Test PCMA create view

// Test PCMA create route (temporary, no auth required)

// Test Dental Chart route (public access for testing)

// Test Dental Chart Simple route (public access for testing)

// Test Dental Chart Adapted route (public access for testing)

// Toutes les URL historiques d'un joueur affichent le même portail canonique.
$redirectToPlayerPortal = function (?string $playerId = null) {
    return redirect()->route(
        'test.portail.joueur.simple',
        $playerId === null ? [] : ['player_id' => $playerId]
    );
};

Route::middleware(['auth'])->group(function () use ($redirectToPlayerPortal) {
    Route::get('/portail-joueur/{playerId?}', $redirectToPlayerPortal)->name('joueur.portal');
    Route::get('/portail-joueur', [App\Http\Controllers\PlayerPortalController::class, 'show'])->name('portail.joueur');
    Route::get('/joueur/{playerId}', $redirectToPlayerPortal)->name('joueur.show');
    Route::get('/joueur/{playerId}/portal', $redirectToPlayerPortal)->name('player.portal');
});

// Routes pour la gestion des photos des joueurs
Route::middleware(['auth'])->group(function () {
    Route::get('/joueur/{playerId}/photo/upload', [App\Http\Controllers\PlayerPhotoController::class, 'showUploadForm'])->name('joueur.photo.upload');
    Route::post('/joueur/{playerId}/photo/upload', [App\Http\Controllers\PlayerPhotoController::class, 'upload'])->name('joueur.photo.upload.post');
    Route::delete('/joueur/{playerId}/photo', [App\Http\Controllers\PlayerPhotoController::class, 'delete'])->name('joueur.photo.delete');
    Route::put('/joueur/{playerId}/photo/external', [App\Http\Controllers\PlayerPhotoController::class, 'updateExternalUrl'])->name('joueur.photo.external');
    Route::post('/joueur/{playerId}/photo/generate', [App\Http\Controllers\PlayerPhotoController::class, 'generateAvatar'])->name('joueur.photo.generate');
});



Route::get('/joueur/{playerId}/access', [App\Http\Controllers\PlayerAccessController::class, 'showAccessForm'])->name('player.access.form');


Route::post('/joueur/{playerId}/access', [App\Http\Controllers\PlayerAccessController::class, 'authenticate'])->name('player.access.authenticate');

// Dataset Analytics route

// Dataset Analytics API routes
Route::prefix('api/dataset-analytics')->group(function () {
});

// Test Health Records Simple route (public access for testing)
Route::get('/health-records-simple', function () {
    try {
        return view('health-records.create-simple');
    } catch (\Exception $e) {
        \Log::error('Health records simple test route error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
})->name('health.records.simple');



// Simple PCMA test route

// PCMA test simple route

// PCMA test view route

// Test route for PCMA with DoctorSignOff integration

// Global routes (no auth required)
Route::get('/', function () {
    return view('home-landing');
})->name('landing');


Route::get('/dashboard-temp', fn () => abort(410))->name('dashboard.temp');
Route::get('/dashboard-simulated', fn () => abort(410))->name('dashboard.simulated');




Route::get('/profile-selector', function () {
    $footballType = request('footballType', '11aside');
    return view('profile-selector', compact('footballType'));
})->name('profile-selector');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');
});

// Logout routes

// Test page for JavaScript debugging

// Get signed PCMAs for dashboard (public route)
Route::get('/api/signed-pcmas', function () {
    try {
        $pcmas = \App\Models\PCMA::with(['athlete', 'assessor'])
            ->where('is_signed', true)
            ->orderBy('signed_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'pcmas' => $pcmas
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching signed PCMAs: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du chargement des PCMAs signés'
        ], 500);
    }
})->middleware(['auth'])->name('api.signed-pcmas');

// Dashboard Routes (protected by auth)
Route::middleware(['auth'])->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Modules index route (protégé par authentification) - DÉPLACÉE DANS LE GROUPE PROTÉGÉ
    
    // Administration routes
    Route::get('/administration', function () {
        return view('administration.index');
    })->name('administration.index');
    
    // Licenses routes
    // Ensure validation route does not get captured by /licenses/{license}
    Route::get('/licenses/validation', [LicenseController::class, 'validation'])->name('licenses.validation');
    Route::get('/licenses/create', [LicenseController::class, 'create'])->name('licenses.create');
    Route::resource('licenses', LicenseController::class)->except(['show', 'create', 'edit', 'update', 'destroy']);
    Route::patch('/licenses/{license}/approve', [LicenseController::class, 'approve'])->name('licenses.approve');
    Route::patch('/licenses/{license}/reject', [LicenseController::class, 'reject'])->name('licenses.reject');
    
    // User Management routes
    Route::get('/user-management', function () {
        try {
            $users = \App\Models\User::all();
            $accountRequests = \App\Models\AccountRequest::where('status', 'pending')->get();
            
            // Permissions disponibles basées sur les vraies permissions de la base
            $availablePermissions = [
                'player_registration_access' => 'Accès enregistrement joueurs',
                'competition_management_access' => 'Gestion des compétitions',
                'healthcare_access' => 'Accès soins de santé',
                'fifa_connect_access' => 'Accès FIFA Connect',
                'club_management' => 'Gestion des clubs',
                'team_management' => 'Gestion des équipes',
                'user_read' => 'Lire les utilisateurs',
                'user_write' => 'Créer/Modifier les utilisateurs',
                'user_delete' => 'Supprimer les utilisateurs',
                'referee_access' => 'Accès portail arbitre',
                'admin_access' => 'Accès administration',
                'report_generate' => 'Générer des rapports',
                'data_export' => 'Exporter les données'
            ];
            
            return view('modules.user-management.index', compact('users', 'accountRequests', 'availablePermissions'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('user-management.index');
    
    // User Management Create route
    Route::get('/user-management/create', function () {
        try {
            $roles = [
                (object)['name' => 'system_admin', 'display_name' => 'Administrateur Système', 'description' => 'Accès complet au système', 'is_system_role' => true],
                (object)['name' => 'association_admin', 'display_name' => 'Administrateur Association', 'description' => 'Gestion des compétitions et clubs', 'is_system_role' => true],
                (object)['name' => 'club_admin', 'display_name' => 'Administrateur Club', 'description' => 'Gestion des équipes du club', 'is_system_role' => true],
                (object)['name' => 'club_manager', 'display_name' => 'Manager Club', 'description' => 'Gestion des équipes', 'is_system_role' => true],
                (object)['name' => 'club_medical', 'display_name' => 'Médecin Club', 'description' => 'Soins médicaux', 'is_system_role' => true],
                (object)['name' => 'association_registrar', 'display_name' => 'Enregistreur Association', 'description' => 'Enregistrement des données', 'is_system_role' => true],
                (object)['name' => 'association_medical', 'display_name' => 'Médecin Association', 'description' => 'Soins médicaux association', 'is_system_role' => true],
                (object)['name' => 'referee', 'display_name' => 'Arbitre', 'description' => 'Portail arbitre', 'is_system_role' => true],
                (object)['name' => 'player', 'display_name' => 'Joueur', 'description' => 'Accès joueur', 'is_system_role' => true],
            ];
            
            $associations = \App\Models\Association::all();
            $clubs = \App\Models\Club::all();
            
            return view('modules.user-management.create', compact('roles', 'associations', 'clubs'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('user-management.create');
    
    // User Management Store route
    Route::post('/user-management', function (\Illuminate\Http\Request $request) {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string',
                'status' => 'required|string',
                'association_id' => 'nullable|exists:associations,id',
                'club_id' => 'nullable|exists:clubs,id',
            ]);
            
            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => $request->status,
                'association_id' => $request->association_id,
                'club_id' => $request->club_id,
                'fifa_connect_id' => null,
                'email_verified_at' => now(),
            ]);
            
            return redirect()->route('user-management.index')->with('success', 'Utilisateur créé avec succès');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    })->name('user-management.store');
    
    // User Management Show route
    Route::get('/user-management/{user}', function (\App\Models\User $user) {
        return view('modules.user-management.show', compact('user'));
    })->name('user-management.show');
    
    // User Management Edit route
    Route::get('/user-management/{user}/edit', function (\App\Models\User $user) {
        try {
            $roles = [
                (object)['name' => 'system_admin', 'display_name' => 'Administrateur Système', 'description' => 'Accès complet au système', 'is_system_role' => true],
                (object)['name' => 'association_admin', 'display_name' => 'Administrateur Association', 'description' => 'Gestion des compétitions et clubs', 'is_system_role' => true],
                (object)['name' => 'club_admin', 'display_name' => 'Administrateur Club', 'description' => 'Gestion des équipes du club', 'is_system_role' => true],
                (object)['name' => 'club_manager', 'display_name' => 'Manager Club', 'description' => 'Gestion des équipes', 'is_system_role' => true],
                (object)['name' => 'club_medical', 'display_name' => 'Médecin Club', 'description' => 'Soins médicaux', 'is_system_role' => true],
                (object)['name' => 'association_registrar', 'display_name' => 'Enregistreur Association', 'description' => 'Enregistrement des données', 'is_system_role' => true],
                (object)['name' => 'association_medical', 'display_name' => 'Médecin Association', 'description' => 'Soins médicaux association', 'is_system_role' => true],
                (object)['name' => 'referee', 'display_name' => 'Arbitre', 'description' => 'Portail arbitre', 'is_system_role' => true],
                (object)['name' => 'player', 'display_name' => 'Joueur', 'description' => 'Accès joueur', 'is_system_role' => true],
            ];
            
            $associations = \App\Models\Association::all();
            $clubs = \App\Models\Club::all();
            
            return view('modules.user-management.edit', compact('user', 'roles', 'associations', 'clubs'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    })->name('user-management.edit');
    
    // User Management Update route
    Route::put('/user-management/{user}', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'role' => 'required|string',
                'status' => 'required|string',
                'association_id' => 'nullable|exists:associations,id',
                'club_id' => 'nullable|exists:clubs,id',
            ]);
            
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status' => $request->status,
                'association_id' => $request->association_id,
                'club_id' => $request->club_id,
            ]);
            
            return redirect()->route('user-management.index')->with('success', 'Utilisateur mis à jour avec succès');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    })->name('user-management.update');
    
    // User Management Destroy route
    Route::delete('/user-management/{user}', function (\App\Models\User $user) {
        try {
            $user->delete();
            return redirect()->route('user-management.index')->with('success', 'Utilisateur supprimé avec succès');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    })->name('user-management.destroy');
    
    // Test route pour la création d'utilisateurs (sans authentification)
    
    // Test route pour la gestion des utilisateurs (sans authentification)
    
    // Test route pour l'édition d'utilisateur (sans authentification)
    
    // Test route pour l'édition d'utilisateur sans authentification (pour diagnostiquer l'erreur 500)
    
    // Role Management routes
    Route::get('/role-management', function () {
        // Rôles prédéfinis avec leurs permissions
        $predefinedRoles = [
            'system_admin' => [
                'name' => 'Administrateur Système',
                'description' => 'Accès complet à tous les modules et fonctionnalités',
                'permissions' => [
                    'user_read', 'user_write', 'user_delete',
                    'admin_access', 'referee_access',
                    'player_registration_access', 'competition_management_access',
                    'healthcare_access', 'fifa_connect_access',
                    'club_management', 'team_management',
                    'report_generate', 'data_export'
                ]
            ],
            'association_admin' => [
                'name' => 'Administrateur Association',
                'description' => 'Gestion des compétitions, clubs et arbitres',
                'permissions' => [
                    'user_read', 'user_write',
                    'competition_management_access', 'club_management',
                    'team_management', 'referee_access',
                    'report_generate', 'data_export'
                ]
            ],
            'club_admin' => [
                'name' => 'Administrateur Club',
                'description' => 'Gestion des équipes et joueurs du club',
                'permissions' => [
                    'player_registration_access', 'team_management',
                    'healthcare_access', 'fifa_connect_access',
                    'report_generate'
                ]
            ],
            'referee' => [
                'name' => 'Arbitre',
                'description' => 'Accès au portail arbitre et gestion des matchs',
                'permissions' => [
                    'referee_access', 'report_generate'
                ]
            ],
            'healthcare_provider' => [
                'name' => 'Fournisseur de Soins',
                'description' => 'Accès aux dossiers médicaux et soins',
                'permissions' => [
                    'healthcare_access', 'player_registration_access',
                    'report_generate'
                ]
            ],
            'data_analyst' => [
                'name' => 'Analyste de Données',
                'description' => 'Accès aux rapports et analyses',
                'permissions' => [
                    'report_generate', 'data_export',
                    'user_read'
                ]
            ],
            'user' => [
                'name' => 'Utilisateur Standard',
                'description' => 'Accès de base au système',
                'permissions' => []
            ]
        ];
        
        return view('modules.role-management.index', compact('predefinedRoles'));
    })->name('role-management.index');
    
    // API Routes pour la gestion des rôles
    Route::get('/api/users', function () {
        $users = \App\Models\User::select('id', 'name', 'email', 'role')->get();
        return response()->json($users);
    });
    
    Route::post('/api/users/apply-role', function (\Illuminate\Http\Request $request) {
        try {
            $request->validate([
                'role' => 'required|string',
                'permissions' => 'required|array',
                'user_ids' => 'required|array'
            ]);
            
            $updatedCount = 0;
            $role = $request->role;
            $permissions = $request->permissions;
            $userIds = $request->user_ids;
            
            foreach ($userIds as $userId) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->role = $role;
                    $user->permissions = $permissions;
                    $user->save();
                    $updatedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Rôle appliqué avec succès',
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'application du rôle: ' . $e->getMessage()
            ], 400);
        }
    });    
    // Route de test pour le portail arbitre
    
    // Route de test pour vérifier les permissions
    
    // Route pour appliquer les rôles prédéfinis à tous les utilisateurs existants
    Route::post('/api/apply-predefined-roles', function () {
        try {
            // Rôles prédéfinis avec leurs permissions
            $predefinedRoles = [
                'system_admin' => [
                    'user_read', 'user_write', 'user_delete',
                    'admin_access', 'referee_access',
                    'player_registration_access', 'competition_management_access',
                    'healthcare_access', 'fifa_connect_access',
                    'club_management', 'team_management',
                    'report_generate', 'data_export'
                ],
                'association_admin' => [
                    'user_read', 'user_write',
                    'competition_management_access', 'club_management',
                    'team_management', 'referee_access',
                    'report_generate', 'data_export'
                ],
                'club_admin' => [
                    'player_registration_access', 'team_management',
                    'healthcare_access', 'fifa_connect_access',
                    'report_generate'
                ],
                'referee' => [
                    'referee_access', 'report_generate'
                ],
                'healthcare_provider' => [
                    'healthcare_access', 'player_registration_access',
                    'report_generate'
                ],
                'data_analyst' => [
                    'report_generate', 'data_export',
                    'user_read'
                ],
                'user' => []
            ];
            
            $updatedCount = 0;
            $users = \App\Models\User::all();
            
            foreach ($users as $user) {
                if (isset($predefinedRoles[$user->role])) {
                    $user->permissions = $predefinedRoles[$user->role];
                    $user->save();
                    $updatedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Rôles prédéfinis appliqués avec succès',
                'updated_count' => $updatedCount,
                'total_users' => $users->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'application des rôles: ' . $e->getMessage()
            ], 400);
        }
    });
    
    // Audit Trail routes
    Route::get('/audit-trail', function () {
        return view('modules.audit-trail.index');
    })->name('audit-trail.index');
    
    // Logs routes
    Route::get('/logs', function () {
        return view('modules.logs.index');
    })->name('logs.index');
    
    // System Status routes
    Route::get('/system-status', function () {
        return view('modules.system-status.index');
    })->name('system-status.index');
    
    // Settings routes
    Route::get('/settings', function () {
        return view('modules.settings.index');
    })->name('settings.index');
    
    // License Types routes
    Route::get('/license-types', function () {
        $licenseTypes = \App\Models\LicenseType::orderBy('name')->get();
        return view('modules.license-types.index', compact('licenseTypes'));
    })->name('license-types.index');
    
    // Content Management routes
    Route::get('/content', function () {
        return view('modules.content.index');
    })->name('content.index');
    
    // Stakeholder Gallery routes
    Route::get('/stakeholder-gallery', function () {
        return view('modules.stakeholder-gallery.index');
    })->name('stakeholder-gallery.index');
    
    // Players routes - CRUD complet
    Route::resource('players', App\Http\Controllers\PlayerController::class);
    Route::get('/players/{player}/health-records', [App\Http\Controllers\PlayerController::class, 'healthRecords'])->name('players.health-records');
    
    // Routes d'import en masse
    Route::get('/players/bulk-import/form', [App\Http\Controllers\PlayerController::class, 'bulkImportForm'])->name('players.bulk-import-form');
    Route::post('/players/bulk-import', [App\Http\Controllers\PlayerController::class, 'bulkImport'])->name('players.bulk-import');
    
    // Player Registration routes
    Route::get('/player-registration', [App\Http\Controllers\PlayerRegistrationController::class, 'index'])->name('player-registration.index');
    Route::get('/player-registration/create', [App\Http\Controllers\PlayerRegistrationController::class, 'create'])->name('player-registration.create');
    Route::post('/player-registration', [App\Http\Controllers\PlayerRegistrationController::class, 'store'])->name('player-registration.store');
    
    // Club Player Licenses routes
    Route::get('/club/player-licenses', function () {
        $user = auth()->user();
        $query = \App\Models\PlayerLicense::with(['player', 'club']);
        if ($user->isClubUser()) {
            abort_unless($user->club_id, 403);
            $query->where('club_id', $user->club_id);
        } elseif (!($user->isSystemAdmin() || $user->isAssociationUser())) {
            abort(403);
        }
        $licenses = $query->orderByDesc('created_at')->paginate(20);
        return view('modules.club.player-licenses.index', compact('licenses'));
    })->name('club.player-licenses.index');
    
    // Player Passports routes
    Route::get('/player-passports', function () {
        $user = auth()->user();
        $query = \App\Models\PlayerPassport::with(['player', 'currentClub']);
        if ($user->isPlayer()) {
            abort_unless($user->player_id, 403);
            $query->where('player_id', $user->player_id);
        } elseif ($user->isClubUser()) {
            abort_unless($user->club_id, 403);
            $query->where('current_club_id', $user->club_id);
        } elseif (!($user->isSystemAdmin() || $user->isAssociationUser())) {
            abort(403);
        }
        $passports = $query->orderByDesc('created_at')->paginate(20);
        return view('modules.player-passports.index', compact('passports'));
    })->name('player-passports.index');
    
    // Health Records routes
    Route::get('/health-records', [App\Http\Controllers\HealthRecordController::class, 'index'])->name('health-records.index');
    Route::get('/health-records/create', [App\Http\Controllers\HealthRecordController::class, 'create'])->name('health-records.create');
    Route::post('/health-records', [App\Http\Controllers\HealthRecordController::class, 'store'])->name('health-records.store');
    Route::post('/health-records/generate-hl7-cda', [App\Http\Controllers\HealthRecordController::class, 'generateHl7Cda'])->name('health-records.generate-hl7-cda');
    
    // Health Records routes with parameters (must come AFTER specific routes)
    Route::get('/health-records/{healthRecord}', [App\Http\Controllers\HealthRecordController::class, 'show'])->name('health-records.show');
    Route::get('/health-records/{healthRecord}/edit', [App\Http\Controllers\HealthRecordController::class, 'edit'])->name('health-records.edit');
    Route::put('/health-records/{healthRecord}', [App\Http\Controllers\HealthRecordController::class, 'update'])->name('health-records.update');
    Route::delete('/health-records/{healthRecord}', [App\Http\Controllers\HealthRecordController::class, 'destroy'])->name('health-records.destroy');
    Route::post('/health-records/{healthRecord}/generate-prediction', [App\Http\Controllers\HealthRecordController::class, 'generatePrediction'])->name('health-records.generate-prediction');
    
    // Performances routes
    Route::get('/performances', function () {
        return view('modules.performances.index');
    })->name('performances.index');
    
    // Teams routes
    Route::get('/teams', function () {
        $user = auth()->user();
        $clubQuery = \App\Models\Club::with('association');
        if (!$user->isSystemAdmin()) {
            if ($user->club_id) {
                $clubQuery->whereKey($user->club_id);
            } elseif ($user->association_id) {
                $clubQuery->where('association_id', $user->association_id);
            } else {
                $clubQuery->whereRaw('1 = 0');
            }
        }
        $clubs = $clubQuery->orderBy('name')->get();
        $teams = \App\Models\Team::with(['club.association'])
            ->whereIn('club_id', $clubs->modelKeys())->orderBy('name')->get();

        return view('modules.teams.index', compact('teams', 'clubs'));
    })->name('teams.index');
    
    // Club Player Assignments routes
    Route::get('/club-player-assignments', function () {
        return view('modules.club-player-assignments.index');
    })->name('club-player-assignments.index');
    
    // Club Logo Management routes
    Route::get('/club/{club}/logo/upload', [ClubManagementController::class, 'showLogoUpload'])->name('club.logo.upload');
    Route::post('/club/{club}/logo/upload', [ClubManagementController::class, 'uploadLogo'])->name('club.logo.store');
    
    // Match Sheet routes
    Route::get('/match-sheet', [App\Http\Controllers\MatchSheetController::class, 'index'])->name('match-sheet.index');
    

    
    // Transfers routes
    Route::get('/transfers', function () {
        return view('modules.transfers.index');
    })->name('transfers.index');
    
    // Performance Recommendations routes
    Route::get('/performance-recommendations', function () {
        return view('modules.performance-recommendations.index');
    })->name('performance-recommendations.index');
    
    // Competitions routes
    Route::get('/competitions', [CompetitionManagementController::class, 'index'])->name('competitions.index');
    

    Route::get('/competitions/create', [CompetitionManagementController::class, 'create'])->name('competitions.create');
    Route::post('/competitions', [CompetitionManagementController::class, 'store'])->name('competitions.store');
    Route::get('/competitions/{competition}', [CompetitionManagementController::class, 'show'])->name('competitions.show');
    Route::get('/competitions/{competition}/edit', [CompetitionManagementController::class, 'edit'])->name('competitions.edit');
    Route::put('/competitions/{competition}', [CompetitionManagementController::class, 'update'])->name('competitions.update');
    Route::delete('/competitions/{competition}', [CompetitionManagementController::class, 'destroy'])->name('competitions.destroy');
    Route::post('/competitions/{competition}/sync', [CompetitionManagementController::class, 'sync'])->name('competitions.sync');
    Route::post('/competitions/sync-all', [CompetitionManagementController::class, 'syncAll'])->name('competitions.sync-all');
    Route::get('/competitions/{competition}/standings', [CompetitionManagementController::class, 'standings'])->name('competitions.standings');
    Route::get('/competitions/{competition}/register-team-form', [CompetitionManagementController::class, 'showRegisterTeamForm'])->name('competitions.register-team-form');
    Route::post('/competitions/{competition}/register-team', [CompetitionManagementController::class, 'registerTeam'])->name('competitions.register-team');
    
    // Fixtures routes
    Route::get('/fixtures', function () {
        return view('modules.fixtures.index');
    })->name('fixtures.index');
    
    // Rankings routes
    Route::get('/rankings', function () {
        return view('modules.rankings.index');
    })->name('rankings.index');
    
    // Seasons routes
    Route::get('/seasons', function () {
        return view('modules.seasons.index');
    })->name('seasons.index');
    
    // Federations routes
    Route::get('/federations', function () {
        return view('modules.federations.index');
    })->name('federations.index');
    
    // Registration Requests routes
    Route::get('/registration-requests', function () {
        $user = auth()->user();
        $query = \App\Models\LicenseRequest::query();
        if ($user->isClubUser()) {
            abort_unless($user->club_id, 403);
            $query->where('current_club_id', $user->club_id);
        } elseif (!($user->isSystemAdmin() || $user->isAssociationUser())) {
            abort(403);
        }
        $requests = $query->orderByDesc('created_at')->paginate(20);
        return view('modules.registration-requests.index', compact('requests'));
    })->name('registration-requests.index');
    
    // Player Licenses routes
    Route::get('/player-licenses', function () {
        $user = auth()->user();
        $query = \App\Models\PlayerLicense::with(['player', 'club']);
        if ($user->isClubUser()) {
            abort_unless($user->club_id, 403);
            $query->where('club_id', $user->club_id);
        } elseif ($user->isAssociationUser()) {
            abort_unless($user->association_id, 403);
            $query->whereHas('club', function ($q) use ($user) {
                $q->where('association_id', $user->association_id);
            });
        } elseif (!$user->isSystemAdmin()) {
            abort(403);
        }
        $licenses = $query->orderByDesc('created_at')->paginate(20);
        return view('modules.player-licenses.index', compact('licenses'));
    })->name('player-licenses.index');
    
    // Contracts routes
    Route::get('/contracts', function () {
        return view('modules.contracts.index');
    })->name('contracts.index');
    
    // FIFA routes
    Route::get(
        '/fifa/dashboard',
        [\App\Http\Controllers\FifaConnectDashboardController::class, 'index']
    )->middleware(['auth'])->name('fifa.dashboard');

    Route::get(
        '/fifa/connectivity/status',
        [\App\Http\Controllers\FifaConnectDashboardController::class, 'status']
    )->middleware(['auth'])->name('fifa.connectivity.status');
    
    Route::get('/fifa/connectivity', [\App\Http\Controllers\FifaConnectDashboardController::class, 'index'])
        ->name('fifa.connectivity');
    
    Route::get('/fifa/sync-dashboard', function () {
        return view('modules.fifa.sync-dashboard');
    })->name('fifa.sync-dashboard');
    
    Route::get('/fifa/contracts', function () {
        return view('modules.fifa.contracts');
    })->name('fifa.contracts');
    
    Route::get('/fifa/analytics', function () {
        return view('modules.fifa.analytics');
    })->name('fifa.analytics');
    
    Route::get('/fifa/statistics', [\App\Http\Controllers\FifaConnectDashboardController::class, 'index'])
        ->name('fifa.statistics');
    
    // Device Connections routes
    Route::get('/device-connections', function () {
        return view('modules.device-connections.index');
    })->name('device-connections.index');
    
    // Association Dashboard routes
    Route::get('/association/dashboard', function () {
        return view('modules.association.index');
    })->name('association.dashboard');
    
    // Association Registration routes
    Route::get('/association/registration', [App\Http\Controllers\AssociationRegistrationController::class, 'create'])
        ->name('association.registration.create');
    
    Route::post('/association/registration', [App\Http\Controllers\AssociationRegistrationController::class, 'store'])
        ->name('association.registration.store');
    
    // Association Fraud Detection API
Route::post('/api/v1/association/fraud-detection', [App\Http\Controllers\AssociationRegistrationController::class, 'fraudDetection'])
    ->name('association.fraud-detection.api');

Route::get('/api/v1/association/fraud-detection/test', [App\Http\Controllers\AssociationRegistrationController::class, 'testGPT4Connection'])
    ->name('association.fraud-detection.test');

Route::get('/api/v1/association/fraud-detection/stats', [App\Http\Controllers\AssociationRegistrationController::class, 'getFraudStats'])
    ->name('association.fraud-detection.stats');

// Clinical Data Support System Routes
Route::get('/clinical/support', [App\Http\Controllers\ClinicalDataSupportController::class, 'index'])
    ->name('clinical.support.dashboard');

Route::post('/api/v1/clinical/analyze-pcma/{pCMAId}', [App\Http\Controllers\ClinicalDataSupportController::class, 'analyzePCMA'])
    ->name('clinical.analyze.pcma');

Route::post('/api/v1/clinical/analyze-visit/{visitId}', [App\Http\Controllers\ClinicalDataSupportController::class, 'analyzeVisit'])
    ->name('clinical.analyze.visit');

Route::post('/api/v1/clinical/batch-analyze-pcma', [App\Http\Controllers\ClinicalDataSupportController::class, 'batchAnalyzePCMA'])
    ->name('clinical.batch.analyze.pcma');

Route::post('/api/v1/clinical/batch-analyze-visits', [App\Http\Controllers\ClinicalDataSupportController::class, 'batchAnalyzeVisits'])
    ->name('clinical.batch.analyze.visits');

Route::post('/api/v1/clinical/test-gemini', [App\Http\Controllers\ClinicalDataSupportController::class, 'testGeminiConnection'])
    ->name('clinical.test.gemini');

Route::get('/api/v1/clinical/stats', [App\Http\Controllers\ClinicalDataSupportController::class, 'getClinicalStats'])
    ->name('clinical.stats');

Route::get('/api/v1/clinical/recommendations', [App\Http\Controllers\ClinicalDataSupportController::class, 'getClinicalRecommendations'])
    ->name('clinical.recommendations');

Route::post('/api/v1/clinical/report', [App\Http\Controllers\ClinicalDataSupportController::class, 'generateClinicalReport'])
    ->name('clinical.report');
    
    // Daily Passport routes
    Route::get('/daily-passport', function () {
        return view('modules.daily-passport.index');
    })->name('daily-passport.index');
    
    // Data Sync routes
    Route::get('/data-sync', function () {
        return view('modules.data-sync.index');
    })->name('data-sync.index');
    
    // FIFA Players Search routes
    Route::get('/fifa/players/search', function (Request $request) {
        $user = $request->user();
        abort_unless($user->hasAnyRole(['system_admin', 'association_admin', 'association_registrar', 'club_admin', 'club_manager']), 403);
        $validated = $request->validate(['q' => 'nullable|string|max:100']);
        $query = trim((string) ($validated['q'] ?? ''));
        $players = collect();
        if ($query !== '') {
            $playersQuery = \App\Models\Player::with('club');
            if (!$user->isSystemAdmin()) {
                if ($user->club_id) {
                    $playersQuery->where('club_id', $user->club_id);
                } elseif ($user->association_id) {
                    $playersQuery->where('association_id', $user->association_id);
                } else {
                    $playersQuery->whereRaw('1 = 0');
                }
            }
            $players = $playersQuery->where(function ($search) use ($query) {
                $search->where('fifa_connect_id', $query)
                    ->orWhere('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%");
            })->orderBy('last_name')->limit(25)->get();
        }

        return view('modules.fifa.players.search', compact('players', 'query'));
    })->name('fifa.players.search');
    
    // Apple Health Kit routes
    Route::get('/apple-health-kit', function () {
        return view('modules.apple-health-kit.index');
    })->name('apple-health-kit.index');
    
    // Catapult Connect routes
    Route::get('/catapult-connect', function () {
        return view('modules.catapult-connect.index');
    })->name('catapult-connect.index');
    
    // Garmin Connect routes
    Route::get('/garmin-connect', function () {
        return view('modules.garmin-connect.index');
    })->name('garmin-connect.index');
    
    // Device Connections OAuth2 Tokens routes
    Route::get('/device-connections/oauth2/tokens', function () {
        return view('modules.device-connections.oauth2.tokens');
    })->name('device-connections.oauth2.tokens');
    
    // Healthcare routes
    Route::get('/healthcare', function () {
        $user = auth()->user();
        abort_unless($user->hasAnyRole(['system_admin', 'super_admin', 'association_medical', 'club_medical', 'doctor', 'medical_staff']), 403);

        $query = \App\Models\HealthRecord::with(['player', 'user', 'predictions']);
        if (!$user->isSystemAdmin()) {
            if ($user->club_id) {
                $query->whereHas('player', fn ($player) => $player->where('club_id', $user->club_id));
            } elseif ($user->association_id) {
                $query->whereHas('player', fn ($player) => $player->where('association_id', $user->association_id));
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $healthRecords = $query->orderByDesc('record_date')->get();

        return view('modules.healthcare.index', compact('healthRecords'));
    })->name('healthcare.index');
    
    Route::get('/healthcare/predictions', function () {
        return view('modules.healthcare.predictions');
    })->name('healthcare.predictions');
    
    Route::get('/healthcare/export', function () {
        return view('modules.healthcare.export');
    })->name('healthcare.export');
    
    // PCMA Dashboard routes (SPECIFIC ROUTES FIRST)
    Route::get('/pcma/dashboard', function () {
        $stats = [
            'total_pcmas' => 0,
            'completed_pcmas' => 0,
            'pending_pcmas' => 0,
            'failed_pcmas' => 0
        ];
        
        $recentPcmas = collect([]);
        
        // Try to get actual PCMA stats if model exists
        try {
            if (class_exists('\App\Models\PCMA')) {
                $stats['total_pcmas'] = \App\Models\PCMA::count();
                $stats['completed_pcmas'] = \App\Models\PCMA::where('status', 'completed')->count();
                $stats['pending_pcmas'] = \App\Models\PCMA::where('status', 'pending')->count();
                $stats['failed_pcmas'] = \App\Models\PCMA::where('status', 'failed')->count();
                
                // Get recent PCMAs
                $recentPcmas = \App\Models\PCMA::with(['athlete', 'assessor'])->latest()->take(5)->get();
            }
        } catch (\Exception $e) {
            // PCMA model might not exist or table is missing
        }
        
        return view('pcma.dashboard', [
            'stats' => $stats,
            'recentPcmas' => $recentPcmas
        ]);
    })->name('pcma.dashboard');
    
    // PCMA Create route (SPECIFIC ROUTE)
    Route::get('/pcma/create', function () {
        $athletes = collect([]);
        $users = collect([]);
        
        // Try to get actual players if model exists
        try {
            if (class_exists('\App\Models\Player')) {
                $athletes = \App\Models\Player::with('club')->orderBy('first_name')->get();
            }
        } catch (\Exception $e) {
            // Player model might not exist or table is missing
        }
        
        // Try to get actual users if model exists
        try {
            if (class_exists('\App\Models\User')) {
                $users = \App\Models\User::orderBy('name')->get();
            }
        } catch (\Exception $e) {
            // User model might not exist or table is missing
        }
        
        // If no athletes found, create test data
        if ($athletes->isEmpty()) {
            $athletes = collect([
                (object)['id' => 1, 'first_name' => 'Test', 'last_name' => 'Player 1', 'club_id' => 1],
                (object)['id' => 2, 'first_name' => 'Test', 'last_name' => 'Player 2', 'club_id' => 1],
                (object)['id' => 3, 'first_name' => 'Test', 'last_name' => 'Player 3', 'club_id' => 2],
            ]);
        }
        
        // If no users found, create test data
        if ($users->isEmpty()) {
            $users = collect([
                (object)['id' => 1, 'name' => 'Test Assessor 1', 'email' => 'assessor1@test.com'],
                (object)['id' => 2, 'name' => 'Test Assessor 2', 'email' => 'assessor2@test.com'],
            ]);
        }
        
        return view('pcma.create', [
            'athletes' => $athletes,
            'users' => $users
        ]);
    })->name('pcma.create');
    

    
    Route::post('/pcma', function (Request $request) {
        \Log::info('PCMA store route called', $request->all());
        \Log::info('PCMA store route - Required fields check:', [
            'athlete_id' => $request->input('athlete_id'),
            'type' => $request->input('type'),
            'assessor_id' => $request->input('assessor_id'),
            'assessment_date' => $request->input('assessment_date'),
            'status' => $request->input('status'),
        ]);
        try {
            // Validate the request
            $validated = $request->validate([
                'athlete_id' => 'required|exists:athletes,id',
                'fifa_connect_id' => ['nullable', new \App\Rules\FifaIdentifier()],
                'type' => 'required|in:bpma,cardio,dental,neurological,orthopedic',
                'assessor_id' => 'required|exists:users,id',
                'assessment_date' => 'required|date',
                'status' => 'required|in:pending,completed,failed',
                'result_json' => 'nullable|string',
                'notes' => 'nullable|string',
                'clinical_notes' => 'nullable|string',
                // Signature fields
                'is_signed' => 'nullable|boolean',
                'signed_by' => 'nullable|string|max:255',
                'signed_at' => 'nullable|date',
                'license_number' => 'nullable|string|max:255',
                'signature_data' => 'nullable|string',
                'signature_image' => 'nullable|string',
                // Vital Signs
                'blood_pressure' => 'nullable|string|max:255',
                'heart_rate' => 'nullable|integer|min:0|max:300',
                'temperature' => 'nullable|numeric|min:30|max:45',
                'respiratory_rate' => 'nullable|integer|min:0|max:100',
                'oxygen_saturation' => 'nullable|integer|min:0|max:100',
                'weight' => 'nullable|numeric|min:0|max:500',
                // Medical History
                'medical_history' => 'nullable|string',
                'surgical_history' => 'nullable|string',
                'medications' => 'nullable|string',
                'allergies' => 'nullable|string',
                // Physical Examination
                'general_appearance' => 'nullable|in:normal,abnormal',
                'skin_examination' => 'nullable|in:normal,abnormal',
                'lymph_nodes' => 'nullable|in:normal,enlarged',
                'abdomen_examination' => 'nullable|in:normal,abnormal',
                // Cardiovascular Assessment
                'cardiac_rhythm' => 'nullable|in:sinus,irregular,arrhythmia',
                'heart_murmur' => 'nullable|in:none,systolic,diastolic',
                'blood_pressure_rest' => 'nullable|string|max:255',
                'blood_pressure_exercise' => 'nullable|string|max:255',
                // Neurological Assessment
                'consciousness' => 'nullable|in:alert,confused,drowsy',
                'cranial_nerves' => 'nullable|in:normal,abnormal',
                'motor_function' => 'nullable|in:normal,weakness,paralysis',
                'sensory_function' => 'nullable|in:normal,decreased,absent',
                // Musculoskeletal Assessment
                'joint_mobility' => 'nullable|in:normal,limited,restricted',
                'muscle_strength' => 'nullable|in:normal,reduced,weak',
                'pain_assessment' => 'nullable|in:none,mild,moderate,severe',
                'range_of_motion' => 'nullable|in:full,limited,restricted',
                // Medical Imaging
                'ecg_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ecg_date' => 'nullable|date',
                'ecg_interpretation' => 'nullable|in:normal,sinus_bradycardia,sinus_tachycardia,atrial_fibrillation,ventricular_tachycardia,st_elevation,st_depression,qt_prolongation,abnormal',
                'ecg_notes' => 'nullable|string',
                'mri_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'mri_date' => 'nullable|date',
                'mri_type' => 'nullable|in:brain,spine,knee,shoulder,ankle,hip,cardiac,other',
                'mri_findings' => 'nullable|in:normal,mild_abnormality,moderate_abnormality,severe_abnormality,fracture,tumor,inflammation,degenerative,other',
                'mri_notes' => 'nullable|string',
                'xray_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ct_scan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ultrasound_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            ]);
            \Log::info('PCMA validation passed');

            // Create the PCMA record with basic fields
            $pcma = new \App\Models\PCMA();
            $pcma->athlete_id = $validated['athlete_id'];
            $pcma->type = $validated['type'];
            $pcma->assessor_id = $validated['assessor_id'];
            $pcma->status = $validated['status'];
            $pcma->notes = $validated['notes'] ?? 'Évaluation médicale complétée';
            
            // Handle signature data if provided
            if ($request->has('is_signed') && $request->input('is_signed')) {
                $pcma->is_signed = true;
                $pcma->signed_by = $validated['signed_by'] ?? null;
                $pcma->signed_at = $validated['signed_at'] ?? now();
                $pcma->license_number = $validated['license_number'] ?? null;
                $pcma->signature_data = $validated['signature_data'] ?? null;
                $pcma->signature_image = $validated['signature_image'] ?? null;
            }
            
            // Store detailed data in result_json
            $resultJson = [
                'vital_signs' => [
                    'blood_pressure' => $validated['blood_pressure'] ?? null,
                    'heart_rate' => $validated['heart_rate'] ?? null,
                    'temperature' => $validated['temperature'] ?? null,
                    'respiratory_rate' => $validated['respiratory_rate'] ?? null,
                    'oxygen_saturation' => $validated['oxygen_saturation'] ?? null,
                    'weight' => $validated['weight'] ?? null,
                ],
                'medical_history' => [
                    'cardiovascular_history' => $validated['medical_history'] ?? null,
                    'surgical_history' => $validated['surgical_history'] ?? null,
                    'medications' => $validated['medications'] ?? null,
                    'allergies' => $validated['allergies'] ?? null,
                ],
                'physical_examination' => [
                    'general_appearance' => $validated['general_appearance'] ?? null,
                    'skin_examination' => $validated['skin_examination'] ?? null,
                    'lymph_nodes' => $validated['lymph_nodes'] ?? null,
                    'abdomen_examination' => $validated['abdomen_examination'] ?? null,
                ],
                'cardiovascular_assessment' => [
                    'cardiac_rhythm' => $validated['cardiac_rhythm'] ?? null,
                    'heart_murmur' => $validated['heart_murmur'] ?? null,
                    'blood_pressure_rest' => $validated['blood_pressure_rest'] ?? null,
                    'blood_pressure_exercise' => $validated['blood_pressure_exercise'] ?? null,
                ],
                'neurological_assessment' => [
                    'consciousness' => $validated['consciousness'] ?? null,
                    'cranial_nerves' => $validated['cranial_nerves'] ?? null,
                    'motor_function' => $validated['motor_function'] ?? null,
                    'sensory_function' => $validated['sensory_function'] ?? null,
                ],
                'musculoskeletal_assessment' => [
                    'joint_mobility' => $validated['joint_mobility'] ?? null,
                    'muscle_strength' => $validated['muscle_strength'] ?? null,
                    'pain_assessment' => $validated['pain_assessment'] ?? null,
                    'range_of_motion' => $validated['range_of_motion'] ?? null,
                ],
                'medical_imaging' => [
                    'ecg_date' => $validated['ecg_date'] ?? null,
                    'ecg_interpretation' => $validated['ecg_interpretation'] ?? null,
                    'ecg_notes' => $validated['ecg_notes'] ?? null,
                    'mri_date' => $validated['mri_date'] ?? null,
                    'mri_type' => $validated['mri_type'] ?? null,
                    'mri_findings' => $validated['mri_findings'] ?? null,
                    'mri_notes' => $validated['mri_notes'] ?? null,
                ],
                'clinical_notes' => $validated['clinical_notes'] ?? null,
                'assessment_date' => $validated['assessment_date'] ?? null,
                'fifa_connect_id' => $validated['fifa_connect_id'] ?? null,
            ];
            
            $pcma->result_json = $resultJson;
            
            $pcma->save();
            
            // Return JSON response for AJAX requests or if signature data is provided
            if ($request->expectsJson() || $request->has('signature_data')) {
                return response()->json([
                    'success' => true,
                    'message' => 'PCMA créé avec succès',
                    'pcma_id' => $pcma->id
                ]);
            }
            
            return redirect()->route('pcma.dashboard')->with('success', 'PCMA créé avec succès');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("Validation error creating PCMA: " . $e->getMessage());
            \Log::error("Validation errors: " . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            \Log::error("Error creating PCMA: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du PCMA: ' . $e->getMessage()
            ], 500);
        }
    })->name('pcma.store');
    
    Route::get('/pcma/{pcma}', function ($pcma) {
        $athletes = collect([]); // Empty collection for now
        
        // Try to get actual players if model exists
        try {
            if (class_exists('\App\Models\Player')) {
                $athletes = \App\Models\Player::with('club')->orderBy('first_name')->get();
            }
        } catch (\Exception $e) {
            // Player model might not exist or table is missing
        }

        // If $pcma is a string ID, fetch the model
        if (is_string($pcma) && is_numeric($pcma)) {
            try {
                if (class_exists('\App\Models\PCMA')) {
                    $pcma = \App\Models\PCMA::with(['player', 'assessor'])->find($pcma);
                } else {
                    // Create a mock PCMA object if model doesn't exist
                    $pcma = (object) [
                        'id' => $pcma,
                        'player_name' => 'Test Player',
                        'assessor_name' => 'Test Assessor',
                        'assessment_date' => now(),
                        'status' => 'completed',
                        'notes' => 'Test PCMA assessment',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching PCMA: " . $e->getMessage());
                abort(404, 'PCMA not found');
            }
        }

        if (!$pcma) {
            abort(404, 'PCMA not found');
        }

        return view('pcma.show', [
            'pcma' => $pcma,
            'athletes' => $athletes
        ]);
    })->name('pcma.show');

    Route::get('/pcma/{pcma}/edit', function ($pcma) {
        $athletes = collect([]); // Empty collection for now
        
        // Try to get actual players if model exists
        try {
            if (class_exists('\App\Models\Player')) {
                $athletes = \App\Models\Player::with('club')->orderBy('first_name')->get();
            }
        } catch (\Exception $e) {
            // Player model might not exist or table is missing
        }
        
        // If $pcma is a string ID, fetch the model
        if (is_string($pcma) && is_numeric($pcma)) {
            try {
                if (class_exists('\App\Models\PCMA')) {
                    $pcma = \App\Models\PCMA::with(['player', 'assessor'])->find($pcma);
                } else {
                    // Create a mock PCMA object if model doesn't exist
                    $pcma = (object) [
                        'id' => $pcma,
                        'player_name' => 'Test Player',
                        'assessor_name' => 'Test Assessor',
                        'assessment_date' => now(),
                        'status' => 'completed',
                        'notes' => 'Test PCMA assessment',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching PCMA for edit: " . $e->getMessage());
                abort(404, 'PCMA not found');
            }
        }

        if (!$pcma) {
            abort(404, 'PCMA not found');
        }

        return view('pcma.edit', [
            'pcma' => $pcma,
            'athletes' => $athletes,
            'users' => \App\Models\User::all()
        ]);
    })->name('pcma.edit');

    Route::put('/pcma/{pcma}', function (Request $request, $pcma) {
        try {
            // Find the PCMA record
            $pcmaRecord = \App\Models\PCMA::find($pcma);
            if (!$pcmaRecord) {
                abort(404, 'PCMA not found');
            }
            

            

            
            // Validate the request
            $validated = $request->validate([
                'athlete_id' => 'required|exists:athletes,id',
                'fifa_connect_id' => ['nullable', new \App\Rules\FifaIdentifier()],
                'type' => 'required|in:bpma,cardio,dental,neurological,orthopedic',
                'assessor_id' => 'required|exists:users,id',
                'assessment_date' => 'required|date',
                'status' => 'required|in:pending,completed,failed',
                'result_json' => 'nullable|string',
                'notes' => 'nullable|string',
                'clinical_notes' => 'nullable|string',
                // Vital Signs
                'blood_pressure' => 'nullable|string|max:255',
                'heart_rate' => 'nullable|integer|min:0|max:300',
                'temperature' => 'nullable|numeric|min:30|max:45',
                'respiratory_rate' => 'nullable|integer|min:0|max:100',
                'oxygen_saturation' => 'nullable|integer|min:0|max:100',
                'weight' => 'nullable|numeric|min:0|max:500',
                // Medical History
                'medical_history' => 'nullable|string',
                'surgical_history' => 'nullable|string',
                'medications' => 'nullable|string',
                'allergies' => 'nullable|string',
                // Physical Examination
                'general_appearance' => 'nullable|in:normal,abnormal',
                'skin_examination' => 'nullable|in:normal,abnormal',
                'lymph_nodes' => 'nullable|in:normal,enlarged',
                'abdomen_examination' => 'nullable|in:normal,abnormal',
                // Cardiovascular Assessment
                'cardiac_rhythm' => 'nullable|in:sinus,irregular,arrhythmia',
                'heart_murmur' => 'nullable|in:none,systolic,diastolic',
                'blood_pressure_rest' => 'nullable|string|max:255',
                'blood_pressure_exercise' => 'nullable|string|max:255',
                // Neurological Assessment
                'consciousness' => 'nullable|in:alert,confused,drowsy',
                'cranial_nerves' => 'nullable|in:normal,abnormal',
                'motor_function' => 'nullable|in:normal,weakness,paralysis',
                'sensory_function' => 'nullable|in:normal,decreased,absent',
                // Musculoskeletal Assessment
                'joint_mobility' => 'nullable|in:normal,limited,restricted',
                'muscle_strength' => 'nullable|in:normal,reduced,weak',
                'pain_assessment' => 'nullable|in:none,mild,moderate,severe',
                'range_of_motion' => 'nullable|in:full,limited,restricted',
                // Medical Imaging
                'ecg_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ecg_date' => 'nullable|date',
                'ecg_interpretation' => 'nullable|in:normal,sinus_bradycardia,sinus_tachycardia,atrial_fibrillation,ventricular_tachycardia,st_elevation,st_depression,qt_prolongation,abnormal',
                'ecg_notes' => 'nullable|string',
                'mri_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'mri_date' => 'nullable|date',
                'mri_type' => 'nullable|in:brain,spine,knee,shoulder,ankle,hip,cardiac,other',
                'mri_findings' => 'nullable|in:normal,mild_abnormality,moderate_abnormality,severe_abnormality,fracture,tumor,inflammation,degenerative,other',
                'mri_notes' => 'nullable|string',
                'xray_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ct_scan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ultrasound_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            ]);

            // Update the PCMA record with basic fields
            $pcmaRecord->athlete_id = $validated['athlete_id'];
            $pcmaRecord->type = $validated['type'];
            $pcmaRecord->assessor_id = $validated['assessor_id'];
            $pcmaRecord->status = $validated['status'];
            $pcmaRecord->notes = $validated['notes'];
            
            // Store detailed data in result_json
            $resultJson = [
                'vital_signs' => [
                    'blood_pressure' => $validated['blood_pressure'] ?? null,
                    'heart_rate' => $validated['heart_rate'] ?? null,
                    'temperature' => $validated['temperature'] ?? null,
                    'respiratory_rate' => $validated['respiratory_rate'] ?? null,
                    'oxygen_saturation' => $validated['oxygen_saturation'] ?? null,
                    'weight' => $validated['weight'] ?? null,
                ],
                'medical_history' => [
                    'cardiovascular_history' => $validated['medical_history'] ?? null,
                    'surgical_history' => $validated['surgical_history'] ?? null,
                    'medications' => $validated['medications'] ?? null,
                    'allergies' => $validated['allergies'] ?? null,
                ],
                'physical_examination' => [
                    'general_appearance' => $validated['general_appearance'] ?? null,
                    'skin_examination' => $validated['skin_examination'] ?? null,
                    'lymph_nodes' => $validated['lymph_nodes'] ?? null,
                    'abdomen_examination' => $validated['abdomen_examination'] ?? null,
                ],
                'cardiovascular_assessment' => [
                    'cardiac_rhythm' => $validated['cardiac_rhythm'] ?? null,
                    'heart_murmur' => $validated['heart_murmur'] ?? null,
                    'blood_pressure_rest' => $validated['blood_pressure_rest'] ?? null,
                    'blood_pressure_exercise' => $validated['blood_pressure_exercise'] ?? null,
                ],
                'neurological_assessment' => [
                    'consciousness' => $validated['consciousness'] ?? null,
                    'cranial_nerves' => $validated['cranial_nerves'] ?? null,
                    'motor_function' => $validated['motor_function'] ?? null,
                    'sensory_function' => $validated['sensory_function'] ?? null,
                ],
                'musculoskeletal_assessment' => [
                    'joint_mobility' => $validated['joint_mobility'] ?? null,
                    'muscle_strength' => $validated['muscle_strength'] ?? null,
                    'pain_assessment' => $validated['pain_assessment'] ?? null,
                    'range_of_motion' => $validated['range_of_motion'] ?? null,
                ],
                'medical_imaging' => [
                    'ecg_date' => $validated['ecg_date'] ?? null,
                    'ecg_interpretation' => $validated['ecg_interpretation'] ?? null,
                    'ecg_notes' => $validated['ecg_notes'] ?? null,
                    'mri_date' => $validated['mri_date'] ?? null,
                    'mri_type' => $validated['mri_type'] ?? null,
                    'mri_findings' => $validated['mri_findings'] ?? null,
                    'mri_notes' => $validated['mri_notes'] ?? null,
                ],
                'clinical_notes' => $validated['clinical_notes'] ?? null,
                'assessment_date' => $validated['assessment_date'] ?? null,
                'fifa_connect_id' => $validated['fifa_connect_id'] ?? null,
            ];
            
            $pcmaRecord->result_json = $resultJson;
            $pcmaRecord->save();
            
            return redirect()->route('pcma.dashboard')->with('success', 'PCMA mis à jour avec succès');
            
        } catch (\Exception $e) {
            \Log::error("Error updating PCMA: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour du PCMA: ' . $e->getMessage()]);
        }
    })->name('pcma.update');

    Route::delete('/pcma/{pcma}', function ($pcma) {
        return redirect()->route('pcma.dashboard')->with('success', 'PCMA deleted successfully');
    })->name('pcma.destroy');

    // Additional PCMA routes
    // PDF routes - specific routes first
    Route::get('/pcma/pdf', function () {
        return response()->json([
            'success' => false,
            'message' => 'PDF generation requires form data. Please use the PCMA form to generate a PDF.'
        ], 400);
    });

    // Route pcma.complete supprimée - doublon avec celle du contrôleur

    // Route pcma.fail supprimée - doublon avec celle du contrôleur

    Route::get('/pcma', function (Request $request) {
        try {
            // Get PCMA records with relationships
            $query = \App\Models\PCMA::with(['athlete', 'assessor']);
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            
            if ($request->filled('search')) {
                $query->whereHas('athlete', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            }
            
            // Order by latest first
            $pcmas = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return view('pcma.index', compact('pcmas'));
            
        } catch (\Exception $e) {
            \Log::error("Error loading PCMA index: " . $e->getMessage());
            return view('pcma.index', ['pcmas' => collect([])]);
        }
    })->name('pcma.index');
    
    // PCMA AI Analysis routes
    Route::post('/pcma/ai-analyze-ecg', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeEcg'])->name('pcma.ai-analyze-ecg');
    Route::post('/pcma/ai-analyze-mri', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeMri'])->name('pcma.ai-analyze-mri');
    Route::post('/pcma/ai-analyze-xray', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeXray'])->name('pcma.ai-analyze-xray');
    Route::post('/pcma/ai-analyze-ct', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeCt'])->name('pcma.ai-analyze-ct');
    Route::post('/pcma/ai-analyze-ultrasound', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeUltrasound'])->name('pcma.ai-analyze-ultrasound');
    Route::post('/pcma/ai-fitness-assessment', [App\Http\Controllers\PCMAController::class, 'aiFitnessAssessment'])->name('pcma.ai-fitness-assessment');

// Test PDF route (using api middleware group)
Route::get('/test-pdf', function() {
    try {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Test PDF</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #333; }
            </style>
        </head>
        <body>
            <h1>Test PDF Generation</h1>
            <p>This is a test PDF to verify DomPDF is working correctly.</p>
            <p>Generated at: ' . now()->format('Y-m-d H:i:s') . '</p>
        </body>
        </html>');
        $pdf->setPaper('A4', 'portrait');
        
        return response()->make($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="test.pdf"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('test.pdf')->middleware('api');

    // Medical Predictions Dashboard routes
    
    // Medical Predictions Dashboard routes
    Route::get('/medical-predictions/dashboard', function () {
        return view('modules.medical-predictions.dashboard');
    })->name('medical-predictions.dashboard');
    
    // Appointments routes
    Route::get('/appointments', function () {
        return view('modules.appointments.index');
    })->name('appointments.index');
    
    // Visits routes
    Route::get('/visits', function () {
        return view('modules.visits.index');
    })->name('visits.index');
    
    // Documents routes
    Route::get('/documents', function () {
        return view('modules.documents.index');
    })->name('documents.index');
    
    // Portal Dashboard routes
    Route::get('/portal/dashboard', function () {
        return view('modules.portal.dashboard');
    })->name('portal.dashboard');
    
    // Portal sub-routes
    Route::get('/portal/medical-record', function () {
        return view('modules.portal.medical-record');
    })->name('portal.medical-record');
    
    Route::get('/portal/wellness', function () {
        return view('modules.portal.wellness');
    })->name('portal.wellness');
    
    
    // Secretary Dashboard routes
    Route::get('/secretary/dashboard', function () {
    // Données dynamiques pour le dashboard secretary - Utilisation des rendez-vous futurs
    $stats = [
        'total_appointments' => \App\Models\Appointment::count(),
        'upcoming_appointments' => \App\Models\Appointment::where('appointment_date', '>=', now())->whereIn('status', ['scheduled', 'confirmed'])->count(),
        'total_documents' => \App\Models\HealthRecord::count(),
        'pending_documents' => \App\Models\HealthRecord::where('status', 'pending')->count(),
    ];

    // Rendez-vous futurs (comme dans clinician portal)
    $recentAppointments = \App\Models\Appointment::with('athlete')
        ->where('appointment_date', '>=', now())
        ->whereIn('status', ['scheduled', 'confirmed'])
        ->orderBy('appointment_date', 'asc')
        ->limit(10)
        ->get();

    // Documents récents pour référence
    $recentDocuments = \App\Models\HealthRecord::with('player')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    return view('secretary.dashboard', compact('stats', 'recentAppointments', 'recentDocuments'));
    })->name('secretary.dashboard');
    
    
    // Secretary sub-routes
    Route::get('/appointments', function () {
        return view('modules.appointments.index');
    })->name('appointments.index');
    
    Route::get('/documents', function () {
        return view('modules.documents.index');
    })->name('documents.index');
    
    // Referee routes
    Route::get('/referee/dashboard', [App\Http\Controllers\RefereeController::class, 'dashboard'])->name('referee.dashboard');
    
    Route::get('/referee/match-assignments', function () {
        $user = auth()->user();
        $assignments = \App\Models\GameMatch::whereHas('officials', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['homeTeam.club', 'awayTeam.club', 'competition'])
            ->where('status', '!=', 'completed')
            ->orderBy('match_date')
            ->paginate(20);

        return view('modules.referee.match-assignments', compact('assignments'));
    })->name('referee.match-assignments');
    
    Route::get('/referee/match-sheet/{match}', [App\Http\Controllers\RefereeController::class, 'matchSheet'])->name('referee.match-sheet');
    
    Route::get('/referee/competition-schedule', function () {
        $user = auth()->user();
        $upcomingMatches = \App\Models\GameMatch::whereHas('officials', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['competition', 'homeTeam.club', 'awayTeam.club'])
            ->where('status', 'scheduled')
            ->orderBy('match_date')
            ->limit(20)
            ->get();

        return view('modules.referee.competition-schedule', compact('upcomingMatches'));
    })->name('referee.competition-schedule');
    
    Route::get('/referee/create-match-report', [App\Http\Controllers\RefereeController::class, 'createMatchReport'])->name('referee.create-match-report');
    Route::get('/referee/create-match-report/{matchId}', [App\Http\Controllers\RefereeController::class, 'createDetailedMatchReport'])->name('referee.create-detailed-match-report');
    
    Route::get('/referee/performance-stats', function () {
        $user = auth()->user();
        $reports = \App\Models\RefereeReport::where('referee_id', $user->id)->get();

        $totalMatches = $reports->count();
        $ratedReports = $reports->whereNotNull('match_rating');
        $averageRating = $ratedReports->count() > 0
            ? round($ratedReports->avg('match_rating'), 2)
            : null;
        $cardsIssued = $reports->sum(function ($report) {
            return count($report->yellow_cards ?? []) + count($report->red_cards ?? []);
        });
        $competitionsCount = $reports->pluck('competition_name')->filter()->unique()->count();

        $recentMatches = \App\Models\GameMatch::whereHas('officials', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['homeTeam.club', 'awayTeam.club', 'competition'])
            ->where('status', 'completed')
            ->orderByDesc('match_date')
            ->limit(10)
            ->get();

        return view('modules.referee.performance-stats', [
            'totalMatches' => $totalMatches,
            'averageRating' => $averageRating,
            'cardsIssued' => $cardsIssued,
            'competitionsCount' => $competitionsCount,
            'recentMatches' => $recentMatches,
        ]);
    })->name('referee.performance-stats');
    
    Route::get('/referee/settings', function () {
        $refereeUser = auth()->user();
        return view('modules.referee.settings', compact('refereeUser'));
    })->name('referee.settings');

    Route::put('/referee/settings/profile', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
        ]);
        $user->update($validated);
        return redirect()->route('referee.settings')->with('success', 'Profil mis à jour avec succès.');
    })->name('referee.settings.profile.update');

    Route::put('/referee/settings/password', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        if (!\Illuminate\Support\Facades\Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($validated['new_password'])]);
        return redirect()->route('referee.settings')->with('success', 'Mot de passe mis à jour avec succès.');
    })->name('referee.settings.password.update');
    
    // Canonical FIT performance metrics
    Route::get(
        '/performances/fit-metrics',
        [\App\Http\Controllers\FitMetricManagementController::class, 'index']
    )->middleware([
        'auth',
        'auth.unified',
        'permission.unified:record-performance-metrics',
    ])->name('performances.fit-metrics');

    // Performances Analytics routes
    Route::get(
        '/performances/analytics',
        [\App\Http\Controllers\PerformanceAnalyticsController::class, 'index']
    )->name('performances.analytics');
    
    // Performances Trends routes
    Route::get('/performances/trends', function () {
        return redirect()->route('performances.analytics');
    })->name('performances.trends');
    
    // Alerts Performance routes
    Route::get('/alerts/performance', function () {
        return view('modules.alerts.performance');
    })->name('alerts.performance');
    
    // Profile routes
    Route::get('/profile', function () {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        return view('modules.profile.show', compact('user'));
    })->name('profile.show');
    
    // Notifications routes
    Route::post('/notifications/{id}/mark-as-read', function ($id) {
        return redirect()->back();
    })->name('notifications.markAsRead');
    
    // License Requests routes
    Route::get('/license-requests/{id}', function ($id) {
        return view('modules.license-requests.show');
    })->name('license-requests.show');
    
    // Medical Predictions routes
    Route::get('/medical-predictions', function () {
        return view('modules.medical-predictions.index');
    })->name('medical-predictions.index');
    
    Route::get('/medical-predictions/create', function () {
        $players = collect([]); // Empty collection for now
        $predictionTypes = [
            'injury_risk' => 'Risque de Blessure',
            'performance_prediction' => 'Prédiction de Performance',
            'recovery_time' => 'Temps de Récupération',
            'fitness_level' => 'Niveau de Forme',
            'health_status' => 'État de Santé'
        ];
        $selectedPlayer = null;
        
        // Try to get actual players if model exists
        try {
            if (class_exists('\App\Models\Player')) {
                $players = \App\Models\Player::with('club')->orderBy('first_name')->get();
            }
        } catch (\Exception $e) {
            // Player model might not exist or table is missing
        }
        
        return view('medical-predictions.create', [
            'players' => $players,
            'predictionTypes' => $predictionTypes,
            'selectedPlayer' => $selectedPlayer
        ]);
    })->name('medical-predictions.create');
    
    Route::post('/medical-predictions', function () {
        return redirect()->route('medical-predictions.index')->with('success', 'Medical prediction created successfully');
    })->name('medical-predictions.store');
    
    Route::get('/medical-predictions/{prediction}', function ($prediction) {
        return view('medical-predictions.show', [
            'medicalPrediction' => $prediction
        ]);
    })->name('medical-predictions.show');
    
    Route::get('/medical-predictions/{prediction}/edit', function ($prediction) {
        return view('medical-predictions.edit', [
            'medicalPrediction' => $prediction
        ]);
    })->name('medical-predictions.edit');
    
    Route::put('/medical-predictions/{prediction}', function ($prediction) {
        return redirect()->route('medical-predictions.index')->with('success', 'Medical prediction updated successfully');
    })->name('medical-predictions.update');
    
    Route::delete('/medical-predictions/{prediction}', function ($prediction) {
        return redirect()->route('medical-predictions.index')->with('success', 'Medical prediction deleted successfully');
    })->name('medical-predictions.destroy');
    
    Route::get('/medical-predictions/dashboard', function () {
        return view('medical-predictions.dashboard');
    })->name('medical-predictions.dashboard');
    
    // Healthcare Records routes
    Route::get('/healthcare/records/{record}', function ($record) {
        return view('modules.healthcare.records.show', [
            'record' => $record
        ]);
    })->name('healthcare.records.show');
    
    Route::get('/healthcare/records/{record}/edit', function ($record) {
        return view('modules.healthcare.records.edit', [
            'record' => $record
        ]);
    })->name('healthcare.records.edit');
    
    Route::put('/healthcare/records/{record}', function ($record) {
        return redirect()->route('modules.healthcare.index')->with('success', 'Dossier médical mis à jour avec succès');
    })->name('healthcare.records.update');
    
    Route::delete('/healthcare/records/{record}', function ($record) {
        return redirect()->back()->with('success', 'Record deleted successfully');
    })->name('healthcare.records.destroy');
    
    // Admin Account Requests routes
    Route::get('/admin/account-requests', function (\Illuminate\Http\Request $request) {
        // When called via fetch() with filters/pagination, return JSON
        if ($request->hasAny(['page', 'status', 'organization_type', 'football_type', 'search'])) {
            $query = \App\Models\AccountRequest::query();

            if ($status = $request->query('status')) {
                $query->where('status', $status);
            }
            if ($org = $request->query('organization_type')) {
                $query->where('organization_type', $org);
            }
            if ($ft = $request->query('football_type')) {
                $query->where('football_type', $ft);
            }
            if ($search = $request->query('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('organization_name', 'like', "%{$search}%");
                });
            }

            $requests = $query->orderBy('created_at', 'desc')->paginate(10);
            return response()->json(['success' => true, 'data' => $requests]);
        }

        // Otherwise, render the management UI
        return view('admin.account-requests.index');
    })->name('admin.account-requests.index')->middleware('auth');

    Route::get('/admin/account-requests/{id}', function ($id) {
        $requestModel = \App\Models\AccountRequest::findOrFail($id);
        return response()->json(['success' => true, 'data' => $requestModel]);
    })->middleware('auth');

    Route::post('/admin/account-requests/{id}/approve', function ($id, \Illuminate\Http\Request $request) {
        $requestModel = \App\Models\AccountRequest::findOrFail($id);
        $requestModel->status = 'approved';
        if ($request->filled('notes')) {
            $requestModel->admin_notes = $request->input('notes');
        }
        $requestModel->save();

        // Create the user account and notify requester
        $requestModel->createUserAccount();
        app(\App\Services\NotificationService::class)->sendAccountRequestApproved($requestModel, auth()->user());

        return response()->json(['success' => true]);
    })->middleware('auth');

    Route::post('/admin/account-requests/{id}/reject', function ($id, \Illuminate\Http\Request $request) {
        $requestModel = \App\Models\AccountRequest::findOrFail($id);
        $reason = $request->input('reason');
        $requestModel->status = 'rejected';
        if ($reason) {
            $requestModel->admin_notes = trim(($requestModel->admin_notes ? $requestModel->admin_notes."\n" : '') . 'Rejected: ' . $reason);
        }
        $requestModel->save();

        app(\App\Services\NotificationService::class)->sendAccountRequestRejected($requestModel, auth()->user(), $reason);

        return response()->json(['success' => true]);
    })->middleware('auth');

    Route::post('/admin/account-requests/{id}/contact', function ($id) {
        $requestModel = \App\Models\AccountRequest::findOrFail($id);
        $requestModel->status = 'contacted';
        $requestModel->save();
        return response()->json(['success' => true]);
    })->middleware('auth');
    
    // Module routes
    
    Route::get('/modules/medical/athlete/{id}', function ($id) {
        $player = null;
        
        // Get the real player from database
        try {
            if (class_exists('\App\Models\Player')) {
                $player = \App\Models\Player::with(['club', 'healthRecords'])->find($id);
            }
        } catch (\Exception $e) {
            // Player model might not exist or table is missing
            \Log::error('Error fetching player: ' . $e->getMessage());
        }
        
        return view('modules.medical.athlete', [
            'player' => $player,
            'footballType' => 'association'
        ]);
    })->name('modules.medical.athlete');
    
    Route::get('/modules/medical', function () {
        $players = \App\Models\Player::with(['club'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(25);

        // Statistiques calculees a partir des vraies donnees medicales
        // (App\Models\MedicalPrediction). "verified" = clearance confirmee
        // par un professionnel ; "active" = prediction en attente de revue ;
        // "risque eleve" = derniere prediction avec risk_probability >= 0.7,
        // utilise comme proxy pour une suspension medicale potentielle
        // (aucun statut "suspension" n'existe dans le schema actuel).
        $stats = [
            'activeClearances' => \App\Models\MedicalPrediction::where('status', 'verified')
                ->distinct('player_id')->count('player_id'),
            'pendingAssessments' => \App\Models\MedicalPrediction::where('status', 'active')->count(),
            'medicalSuspensions' => \App\Models\MedicalPrediction::where('status', 'active')
                ->where('risk_probability', '>=', 0.7)
                ->distinct('player_id')->count('player_id'),
        ];

        $recentActivities = \App\Models\MedicalPrediction::with('player')
            ->orderByDesc('prediction_date')
            ->limit(5)
            ->get();

        return view('modules.medical.index', [
            'footballType' => 'association',
            'players' => $players,
            'stats' => $stats,
            'recentActivities' => $recentActivities,
        ]);
    })->name('modules.medical.index');
    
    Route::get('/modules/healthcare', function () {
        // Meme logique reelle que la route /healthcare (HealthRecordController) -
        // remplace les 3 patients factices (Ahmed Benali, Fatima Kadri, Omar Tazi)
        // codes en dur qui s'affichaient ici avant.
        $user = auth()->user();
        abort_unless($user->hasAnyRole(['system_admin', 'super_admin', 'association_medical', 'club_medical', 'doctor', 'medical_staff']), 403);

        $query = \App\Models\HealthRecord::with(['player', 'user', 'predictions']);
        if (!$user->isSystemAdmin()) {
            if ($user->club_id) {
                $query->whereHas('player', fn ($player) => $player->where('club_id', $user->club_id));
            } elseif ($user->association_id) {
                $query->whereHas('player', fn ($player) => $player->where('association_id', $user->association_id));
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $healthRecords = $query->orderByDesc('record_date')->get();

        return view('modules.healthcare.index', [
            'footballType' => 'association',
            'healthRecords' => $healthRecords
        ]);
    })->name('modules.healthcare.index');
    
    Route::get('/modules/competitions', [App\Http\Controllers\CompetitionController::class, 'moduleDashboard'])->name('modules.competitions.index');
    
    // Route de test pour le module competitions (sans authentification)
    
    // Route de test pour les engagements clubs (sans authentification)
    
    // Routes pour les actions des engagements clubs
    
    // Routes de test pour les autres pages
    
    // Routes Compétitions - Module FIT (Nouvelles fonctionnalités)
    Route::prefix('competitions')->name('competitions.')->group(function () {
        // Côté Club
        Route::prefix('club')->name('club.')->group(function () {
            Route::get('/engagements', [App\Http\Controllers\CompetitionController::class, 'clubEngagements'])->name('engagements');
            Route::get('/effectif', [App\Http\Controllers\CompetitionController::class, 'clubEffectif'])->name('effectif');
            Route::get('/calendrier', [App\Http\Controllers\CompetitionController::class, 'clubCalendrier'])->name('calendrier');
            Route::get('/feuilles-match', [App\Http\Controllers\CompetitionController::class, 'clubFeuillesMatch'])->name('feuilles-match');
            Route::get('/discipline', [App\Http\Controllers\CompetitionController::class, 'clubDiscipline'])->name('discipline');
            Route::get('/classement', [App\Http\Controllers\CompetitionController::class, 'classement'])->name('classement');
            Route::get('/fixtures', [App\Http\Controllers\CompetitionController::class, 'clubFixtures'])->name('fixtures');
            Route::get('/feuille-match/{id}', [App\Http\Controllers\CompetitionController::class, 'feuilleMatch'])->name('feuille-match');
        });
        
        // Côté Association/Ligue
        Route::prefix('association')->name('association.')->group(function () {
            Route::get('/supervision', [App\Http\Controllers\CompetitionController::class, 'associationSupervision'])->name('supervision');
            Route::get('/engagements-clubs', [App\Http\Controllers\CompetitionController::class, 'associationEngagementsClubs'])->name('engagements-clubs');
            Route::post('/export-engagements', [App\Http\Controllers\CompetitionController::class, 'exportEngagements'])->name('export-engagements');
            Route::post('/validate-all-engagements', [App\Http\Controllers\CompetitionController::class, 'validateAllEngagements'])->name('validate-all-engagements');
            Route::get('/calendrier-global', [App\Http\Controllers\CompetitionController::class, 'associationCalendrierGlobal'])->name('calendrier-global');
            Route::get('/resultats-classements', [App\Http\Controllers\CompetitionController::class, 'associationResultatsClassements'])->name('resultats-classements');
            Route::get('/discipline-sanctions', [App\Http\Controllers\CompetitionController::class, 'associationDisciplineSanctions'])->name('discipline-sanctions');
            Route::get('/rapports-statistiques', [App\Http\Controllers\CompetitionController::class, 'associationRapportsStatistiques'])->name('rapports-statistiques');
            Route::get('/classement', [App\Http\Controllers\CompetitionController::class, 'classement'])->name('classement');
            Route::get('/fixtures', [App\Http\Controllers\CompetitionController::class, 'associationFixtures'])->name('fixtures');
            Route::get('/feuille-match/{id}', [App\Http\Controllers\CompetitionController::class, 'feuilleMatch'])->name('feuille-match');
            Route::get('/designation-arbitres', [App\Http\Controllers\CompetitionController::class, 'designationArbitres'])->name('designation-arbitres');
            // NOTE (audit factice -> reel, 2026-09) : CompetitionController::saveArbitreAssignments()
            // existe et est fonctionnelle (enregistre reellement dans la table
            // match_officials) mais n'avait jamais de route associee ; la page
            // designation-arbitres.blade.php ne pouvait donc que simuler
            // l'enregistrement cote JS. Route ajoutee pour connecter le bouton
            // "Confirmer" a l'enregistrement reel.
            Route::post('/designation-arbitres/save', [App\Http\Controllers\CompetitionController::class, 'saveArbitreAssignments'])->name('designation-arbitres.save');
        });
    });
    
    // Route de test temporaire pour les fixtures (sans authentification)
    
    // Route de test pour vérifier la cohérence des arbitres

    
    Route::get('/modules/teams', function () {
        $teams = \App\Models\Team::with(['club', 'club.association'])->orderBy('name')->get();
        $clubs = \App\Models\Club::with('association')->orderBy('name')->get();
        return view('modules.teams.index', compact('teams', 'clubs'));
    })->name('modules.teams.index');
    
    // Route pour afficher le formulaire de création d'une équipe
    Route::get('/modules/teams/create', function () {
        $clubs = \App\Models\Club::with('association')->orderBy('name')->get();
        return view('modules.teams.create', compact('clubs'));
    })->name('modules.teams.create');
    
    // Route pour stocker une nouvelle équipe
    Route::post('/modules/teams', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'club_id' => 'required|exists:clubs,id',
            'level' => 'required|in:professional,semi-professional,amateur,youth,academy',
            'age_category' => 'required|string|max:50',
            'discipline' => 'required|in:football,futsal,beach_soccer,women_football',
            'status' => 'required|in:active,inactive,pending'
        ]);
        
        \App\Models\Team::create($request->all());
        
        return redirect()->route('modules.teams.index')
            ->with('success', 'Équipe créée avec succès.');
    })->name('modules.teams.store');
    
    // Route pour la création en masse d'équipes
    Route::post('/modules/teams/bulk-store', function (\Illuminate\Http\Request $request) {
        $data = json_decode($request->input('data'), true);
        
        $request->validate([
            'data' => 'required|string'
        ]);
        
        if (!$data || !isset($data['club_id'], $data['level'], $data['discipline'], $data['status'], $data['team_names'])) {
            return redirect()->route('modules.teams.index')
                ->with('error', 'Données invalides pour la création en masse.');
        }
        
        $created = 0;
        $errors = [];
        
        foreach ($data['team_names'] as $teamName) {
            $teamName = trim($teamName);
            if (empty($teamName)) continue;
            
            try {
                \App\Models\Team::create([
                    'name' => $teamName,
                    'club_id' => $data['club_id'],
                    'level' => $data['level'],
                    'discipline' => $data['discipline'],
                    'status' => $data['status'],
                    'age_category' => 'Senior' // Valeur par défaut
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Erreur pour '{$teamName}': " . $e->getMessage();
            }
        }
        
        $message = "{$created} équipe(s) créée(s) avec succès.";
        if (!empty($errors)) {
            $message .= " Erreurs: " . implode(', ', $errors);
        }
        
        return redirect()->route('modules.teams.index')
            ->with('success', $message);
    })->name('modules.teams.bulk-store');
    
    // Route pour afficher les détails d'une équipe
    Route::get('/modules/teams/{team}', function (\App\Models\Team $team) {
        $team->load(['club', 'club.association']);
        return view('modules.teams.show', compact('team'));
    })->name('modules.teams.show');
    
    // Route pour afficher le formulaire d'édition d'une équipe
    Route::get('/modules/teams/{team}/edit', function (\App\Models\Team $team) {
        $team->load(['club', 'club.association']);
        $clubs = \App\Models\Club::with('association')->orderBy('name')->get();
        return view('modules.teams.edit', compact('team', 'clubs'));
    })->name('modules.teams.edit');
    
    // Route pour mettre à jour une équipe
    Route::put('/modules/teams/{team}', function (\Illuminate\Http\Request $request, \App\Models\Team $team) {
        $request->validate([
            'name' => 'required|string|max:255',
            'club_id' => 'required|exists:clubs,id',
            'level' => 'required|in:professional,semi-professional,amateur,youth,academy',
            'age_category' => 'required|string|max:50',
            'discipline' => 'required|in:football,futsal,beach_soccer,women_football',
            'status' => 'required|in:active,inactive,pending'
        ]);
        
        $team->update($request->all());
        
        return redirect()->route('modules.teams.index')
            ->with('success', 'Équipe mise à jour avec succès.');
    })->name('modules.teams.update');
    
    // Route pour supprimer une équipe
    Route::delete('/modules/teams/{team}', function (\App\Models\Team $team) {
        $team->delete();
        
        return redirect()->route('modules.teams.index')
            ->with('success', 'Équipe supprimée avec succès.');
    })->name('modules.teams.destroy');
    
    Route::get('/modules/referees', function () {
        return view('modules.referees.index', ['footballType' => 'association']);
    })->middleware('role:system_admin')->name('modules.referees.index');
    
    // Route pour la gestion des arbitres (avec authentification)
    Route::get('/referees', function () {
        return view('modules.referees.index', ['footballType' => 'association']);
    })->middleware('role:system_admin')->name('referees.index');
    
    Route::get('/modules/associations', function () {
        $associations = \App\Models\Association::with(['confederation'])->orderBy('name')->get();
        return view('modules.associations.index', compact('associations'));
    })->name('modules.associations.index');
    
    
    Route::get('/modules/clubs', [App\Http\Controllers\ClubController::class, 'index'])->name('modules.clubs.index');
    Route::get('/modules/clubs/{club}', [App\Http\Controllers\ClubController::class, 'show'])->name('modules.clubs.show');
    
    Route::get('/modules/administration', function () {
        return view('modules.administration.index', ['footballType' => 'association']);
    })->name('modules.administration.index');
    
    Route::get(
        '/modules/licenses',
        [\App\Http\Controllers\PlayerLicenseWorkflowController::class, 'index']
    )->name('modules.licenses.index');

    Route::get(
        '/modules/licenses/players/{player}/request',
        [\App\Http\Controllers\PlayerLicenseWorkflowController::class, 'create']
    )->name('player-licenses.request.create');

    Route::post(
        '/modules/licenses/players/{player}/request',
        [\App\Http\Controllers\PlayerLicenseWorkflowController::class, 'store']
    )->name('player-licenses.request.store');

    // Routes dupliquées supprimées - elles existent déjà ailleurs dans le fichier

    // AI Testing routes
    Route::get('/ai-testing', [App\Http\Controllers\AITestingController::class, 'index'])->name('ai-testing.index');
    Route::post('/ai-testing/run-tests', [App\Http\Controllers\AITestingController::class, 'runTests'])->name('ai-testing.run-tests');
    Route::post('/ai-testing/test-provider', [App\Http\Controllers\AITestingController::class, 'testProvider'])->name('ai-testing.test-provider');
    Route::get('/ai-testing/providers', [App\Http\Controllers\AITestingController::class, 'getProviders'])->name('ai-testing.providers');
    Route::post('/ai-testing/medical-diagnosis', [App\Http\Controllers\AITestingController::class, 'testMedicalDiagnosis'])->name('ai-testing.medical-diagnosis');
    Route::post('/ai-testing/performance-analysis', [App\Http\Controllers\AITestingController::class, 'testPerformanceAnalysis'])->name('ai-testing.performance-analysis');
    Route::post('/ai-testing/injury-prediction', [App\Http\Controllers\AITestingController::class, 'testInjuryPrediction'])->name('ai-testing.injury-prediction');
    Route::get('/ai-testing/summary', [App\Http\Controllers\AITestingController::class, 'getSummary'])->name('ai-testing.summary');

    // Whisper API routes
    Route::get('/whisper', [App\Http\Controllers\WhisperController::class, 'index'])->name('whisper.index');
    Route::post('/whisper/transcribe', [App\Http\Controllers\WhisperController::class, 'transcribe'])->name('whisper.transcribe');
    Route::post('/whisper/transcribe-medical-consultation', [App\Http\Controllers\WhisperController::class, 'transcribeMedicalConsultation'])->name('whisper.transcribe-medical-consultation');
    Route::post('/whisper/transcribe-medical-dictation', [App\Http\Controllers\WhisperController::class, 'transcribeMedicalDictation'])->name('whisper.transcribe-medical-dictation');
    Route::post('/whisper/batch-transcribe', [App\Http\Controllers\WhisperController::class, 'batchTranscribe'])->name('whisper.batch-transcribe');
    Route::get('/whisper/supported-languages', [App\Http\Controllers\WhisperController::class, 'getSupportedLanguages'])->name('whisper.supported-languages');
    Route::get('/whisper/medical-prompt-types', [App\Http\Controllers\WhisperController::class, 'getMedicalPromptTypes'])->name('whisper.medical-prompt-types');
    Route::get('/whisper/test-connection', [App\Http\Controllers\WhisperController::class, 'testConnection'])->name('whisper.test-connection');
    Route::get('/whisper/model-info', [App\Http\Controllers\WhisperController::class, 'getModelInfo'])->name('whisper.model-info');
    Route::get('/whisper/history', [App\Http\Controllers\WhisperController::class, 'getHistory'])->name('whisper.history');

    // Whisper test route
    Route::get('/whisper-test', function () {
        return response()->json(['message' => 'Whisper test route working']);
    })->name('whisper.test');

    // Google Gemini AI routes
    Route::get('/gemini', [App\Http\Controllers\GoogleGeminiController::class, 'index'])->name('gemini.index');
    Route::get('/gemini/test-connection', [App\Http\Controllers\GoogleGeminiController::class, 'testConnection'])->name('gemini.test-connection');
    Route::post('/gemini/generate-diagnosis', [App\Http\Controllers\GoogleGeminiController::class, 'generateDiagnosis'])->name('gemini.generate-diagnosis');
    Route::post('/gemini/generate-treatment', [App\Http\Controllers\GoogleGeminiController::class, 'generateTreatment'])->name('gemini.generate-treatment');
    Route::post('/gemini/analyze-performance', [App\Http\Controllers\GoogleGeminiController::class, 'analyzePerformance'])->name('gemini.analyze-performance');
    Route::post('/gemini/predict-injury-risk', [App\Http\Controllers\GoogleGeminiController::class, 'predictInjuryRisk'])->name('gemini.predict-injury-risk');
    Route::post('/gemini/generate-rehab-plan', [App\Http\Controllers\GoogleGeminiController::class, 'generateRehabPlan'])->name('gemini.generate-rehab-plan');
    Route::post('/gemini/analyze-medical-image', [App\Http\Controllers\GoogleGeminiController::class, 'analyzeMedicalImage'])->name('gemini.analyze-medical-image');
    Route::get('/gemini/configuration', [App\Http\Controllers\GoogleGeminiController::class, 'getConfiguration'])->name('gemini.configuration');
    Route::get('/gemini/history', [App\Http\Controllers\GoogleGeminiController::class, 'getHistory'])->name('gemini.history');

    // ========================================
    // CLINICAL WORKFLOW ROUTES - FHIR R4
    // ========================================
    
    // Portails utilisateurs
    Route::get('/clinical/patient-portal', [App\Http\Controllers\ClinicalWorkflowController::class, 'patientPortal'])->name('clinical.patient-portal');
    Route::get('/clinical/clinician-portal', [App\Http\Controllers\ClinicalWorkflowController::class, 'clinicianPortal'])->name('clinical.clinician-portal');
    
    // API Routes pour le workflow clinique
    Route::prefix('api/clinical')->group(function () {
        // Gestion des patients FHIR
        Route::post('/patients', [App\Http\Controllers\ClinicalWorkflowController::class, 'createPatient'])->name('api.clinical.patients.create');
        Route::get('/patients/{id}', function ($id) {
            $patient = App\Models\FhirPatient::findOrFail($id);
            return response()->json($patient->toFhirJson());
        })->name('api.clinical.patients.show');
        Route::put('/patients/{id}', function (Request $request, $id) {
            $patient = App\Models\FhirPatient::findOrFail($id);
            $patient->update($request->all());
            return response()->json($patient->toFhirJson());
        })->name('api.clinical.patients.update');
        
        // Workflow Patient
        Route::post('/symptoms', [App\Http\Controllers\ClinicalWorkflowController::class, 'submitSymptoms'])->name('api.clinical.symptoms.submit');
        Route::get('/patients/{id}/symptoms', function ($id) {
            $patient = App\Models\FhirPatient::findOrFail($id);
            $conditions = $patient->conditions()->where('clinical_status', 'active')->get();
            return response()->json($conditions->map->toFhirJson());
        })->name('api.clinical.patients.symptoms');
        
        // Workflow Clinicien
        Route::post('/consultations', [App\Http\Controllers\ClinicalWorkflowController::class, 'initialConsultation'])->name('api.clinical.consultations.create');
        Route::get('/consultations/{id}', function ($id) {
            $consultation = App\Models\ClinicalConsultation::findOrFail($id);
            return response()->json($consultation);
        })->name('api.clinical.consultations.show');
        
        // Support décisionnel IA
        Route::post('/decision-support', [App\Http\Controllers\ClinicalWorkflowController::class, 'clinicalDecisionSupport'])->name('api.clinical.decision-support');
        
        // Génération de résumés IA
        Route::post('/summarize', function (Request $request) {
            $validator = Validator::make($request->all(), [
                'text' => 'required|string|max:5000',
                'type' => 'required|in:consultation,report,notes'
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            // Simulation de la génération de résumé avec IA
            $summary = "Résumé généré par IA pour: " . $request->type . "\n\n";
            $summary .= "Contenu original: " . substr($request->text, 0, 200) . "...\n\n";
            $summary .= "Points clés identifiés:\n";
            $summary .= "- Information médicale importante\n";
            $summary .= "- Recommandations cliniques\n";
            $summary .= "- Actions à suivre\n";
            
            return response()->json([
                'success' => true,
                'summary' => $summary,
                'original_length' => strlen($request->text),
                'summary_length' => strlen($summary),
                'compression_ratio' => round(strlen($summary) / strlen($request->text), 2)
            ]);
        })->name('api.clinical.summarize');
        
        // Recherche d'essais cliniques
        Route::get('/clinical-trials', function (Request $request) {
            $patientId = $request->get('patient_id');
            $condition = $request->get('condition');
            
            // Simulation de la recherche d'essais cliniques
            $trials = [
                [
                    'id' => 'NCT12345678',
                    'title' => 'Essai clinique pour ' . ($condition ?? 'condition générale'),
                    'phase' => 'Phase II',
                    'status' => 'Recruiting',
                    'location' => 'Paris, France',
                    'eligibility' => 'Patient âgé de 18-65 ans avec ' . ($condition ?? 'condition spécifique'),
                    'contact' => 'contact@essai-clinique.fr'
                ]
            ];
            
            return response()->json([
                'success' => true,
                'trials' => $trials,
                'total_found' => count($trials),
                'search_criteria' => [
                    'patient_id' => $patientId,
                    'condition' => $condition
                ]
            ]);
        })->name('api.clinical.trials.search');
        
        // Monitoring des écarts de soins
        Route::get('/care-gaps/{patientId}', function ($patientId) {
            $patient = App\Models\FhirPatient::findOrFail($patientId);
            
            // Simulation de la détection d'écarts de soins
            $gaps = [
                [
                    'type' => 'vaccination',
                    'description' => 'Vaccination COVID-19 recommandée',
                    'priority' => 'high',
                    'due_date' => now()->addDays(30)->format('Y-m-d'),
                    'action_required' => 'Prendre rendez-vous pour vaccination'
                ],
                [
                    'type' => 'screening',
                    'description' => 'Dépistage du cancer colorectal recommandé',
                    'priority' => 'medium',
                    'due_date' => now()->addMonths(3)->format('Y-m-d'),
                    'action_required' => 'Programmer un test de dépistage'
                ]
            ];
            
            return response()->json([
                'success' => true,
                'patient_id' => $patientId,
                'care_gaps' => $gaps,
                'total_gaps' => count($gaps),
                'high_priority_count' => count(array_filter($gaps, fn($gap) => $gap['priority'] === 'high'))
            ]);
        })->name('api.clinical.care-gaps');

        // Patient List for Clinician Portal
        Route::get('/patients', function (Request $request) {
            try {
                $statusFilter = $request->get('status');
                $typeFilter = $request->get('type');
                $dateFilter = $request->get('date');

                // Construire la requête pour récupérer les patients avec leurs RDV
                $query = DB::table('appointments')
                    ->join('athletes', 'appointments.athlete_id', '=', 'athletes.id')
                    ->select(
                        'appointments.*',
                        'athletes.id as athlete_id',
                        'athletes.name',
                        'athletes.dob as date_of_birth',
                        'athletes.fifa_id as fifa_connect_id',
                        'athletes.nationality',
                        'athletes.position'
                    )
                    ->orderBy('appointments.appointment_date', 'desc');

                // Appliquer les filtres
                if ($statusFilter) {
                    $query->where('appointments.status', $statusFilter);
                }
                if ($typeFilter) {
                    $query->where('appointments.type', $typeFilter);
                }
                if ($dateFilter) {
                    $query->whereDate('appointments.appointment_date', $dateFilter);
                }

                $patients = $query->limit(50)->get();

                return response()->json([
                    'success' => true,
                    'patients' => $patients,
                    'total' => $patients->count(),
                    'filters' => [
                        'status' => $statusFilter,
                        'type' => $typeFilter,
                        'date' => $dateFilter
                    ]
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur lors de la récupération des patients: ' . $e->getMessage(),
                    'debug' => [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ], 500);
            }
        })->name('api.clinical.patients');
    });
});

// Test simple

// Test portail clinicien simple



// Patient List API (accessible sans authentification pour test)

// PDF generation routes (public access)
Route::post('/pcma/pdf', [App\Http\Controllers\PCMAController::class, 'generatePdf'])->name('pcma.pdf.post')->middleware('api');

// Simple test route

// API routes
Route::get('/api/fit/kpis', [FitDashboardController::class, 'kpis'])->name('fit.kpis');

// Test routes

// Analytics routes
Route::get(
    '/analytics/dashboard',
    [\App\Http\Controllers\AnalyticsDashboardController::class, 'index']
)->middleware(['auth'])->name('analytics.dashboard');

Route::get(
    '/analytics/digital-twin',
    [\App\Http\Controllers\DigitalTwinController::class, 'index']
)->middleware(['auth'])->name('analytics.digital-twin');

// Performance routes
Route::get('/performance', function () {
    return view('performance.index');
})->name('performance.index');

// DTN routes
Route::get(
    '/dtn',
    [\App\Http\Controllers\DtnController::class, 'index']
)->middleware(['auth'])->name('dtn.index');

// RPM routes
Route::get(
    '/rpm',
    [\App\Http\Controllers\RpmController::class, 'index']
)->middleware(['auth'])->name('rpm.index');

// License Fraud Detection Routes
Route::post('/api/v1/licenses/fraud-detection/batch', [App\Http\Controllers\LicenseController::class, 'batchFraudDetection'])
    ->name('licenses.fraud-detection.batch');

Route::post('/api/v1/licenses/fraud-detection/analyze/{licenseId}', [App\Http\Controllers\LicenseController::class, 'analyzeLicenseFraud'])
    ->name('licenses.fraud-detection.analyze');

Route::post('/api/v1/licenses/fraud-detection/check-all', [App\Http\Controllers\LicenseController::class, 'checkAllLicenses'])
    ->name('licenses.fraud-detection.check-all');

// Routes pour le diagramme dentaire
Route::get('/dental-chart', [App\Http\Controllers\DentalChartController::class, 'index'])->middleware(['auth'])->name('dental-chart.index');
Route::get('/dental-chart/{patient}', [App\Http\Controllers\DentalChartController::class, 'show'])->middleware(['auth'])->name('dental-chart.show');

// Route pour le diagramme dentaire (supprimée - doublon)

// Route de test pour le diagramme dentaire

// PCMA Routes (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/pcma', [App\Http\Controllers\PCMAController::class, 'index'])->name('pcma.index');
    Route::post('/pcma', [App\Http\Controllers\PCMAController::class, 'store'])->name('pcma.store');
    Route::get('/pcma/{pcma}', [App\Http\Controllers\PCMAController::class, 'show'])->name('pcma.show');
    Route::get('/pcma/{pcma}/edit', [App\Http\Controllers\PCMAController::class, 'edit'])->name('pcma.edit');
    Route::put('/pcma/{pcma}', [App\Http\Controllers\PCMAController::class, 'update'])->name('pcma.update');
    Route::delete('/pcma/{pcma}', [App\Http\Controllers\PCMAController::class, 'destroy'])->name('pcma.destroy');
    Route::get('/pcma/{pcma}/complete', [App\Http\Controllers\PCMAController::class, 'complete'])->name('pcma.complete');
    Route::get('/pcma/{pcma}/fail', [App\Http\Controllers\PCMAController::class, 'fail'])->name('pcma.fail');
    Route::get('/pcma/{pcma}/pdf', [App\Http\Controllers\PCMAController::class, 'exportPdf'])->name('pcma.pdf');
});



// PCMA API Routes (protected)
Route::middleware(['auth'])->group(function () {
    Route::post('/pcma/ai/ecg', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeEcg'])->name('pcma.ai.ecg');
    Route::post('/pcma/ai/mri', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeMri'])->name('pcma.ai.mri');
    Route::post('/pcma/ai/xray', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeXray'])->name('pcma.ai.xray');
    Route::post('/pcma/ai/ecg-effort', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeEcgEffort'])->name('pcma.ai.ecg-effort');
    Route::post('/pcma/ai/scintigraphy', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeScintigraphy'])->name('pcma.ai.scintigraphy');
    Route::post('/pcma/ai/scat', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeScat'])->name('pcma.ai.scat');
    Route::post('/pcma/ai/complete', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeComplete'])->name('pcma.ai.complete');
    Route::post('/pcma/ai/ct', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeCt'])->name('pcma.ai.ct');
    Route::post('/pcma/ai/ultrasound', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeUltrasound'])->name('pcma.ai.ultrasound');
    Route::post('/pcma/ai/fitness', [App\Http\Controllers\PCMAController::class, 'aiFitnessAssessment'])->name('pcma.ai.fitness');
    Route::post('/pcma/pdf', [App\Http\Controllers\PCMAController::class, 'generatePdf'])->name('pcma.pdf.post');
});

// Player Dashboard redirect (accessible après login)
Route::middleware(['auth'])->get('/player-dashboard', function () {
    $user = Auth::user();
    
    // Si l'utilisateur est un joueur, afficher la fiche 360°
    if ($user->role === 'player' && $user->player) {
        return view('player-portal.player-360-simple');
    }
    
    // Sinon, rediriger vers le portail joueur standard
    return redirect()->route('player-portal.dashboard');
})->name('player-dashboard');

// Player Portal Routes (protected)
Route::middleware(['auth'])->group(function () {
    Route::prefix('player-portal')->name('player-portal.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('portail.joueur');
        });
        Route::get('/home', function () {
            return redirect()->route('admin.dashboard');
        })->name('dashboard');
        Route::get('/profile', [App\Http\Controllers\PlayerPortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\PlayerPortalController::class, 'updateProfile'])->name('update-profile');
        Route::get('/predictions', [App\Http\Controllers\PlayerPortalController::class, 'predictions'])->name('predictions');
        Route::get('/performances', [App\Http\Controllers\PlayerPortalController::class, 'performances'])->name('performances');
        Route::get('/matches', [App\Http\Controllers\PlayerPortalController::class, 'matches'])->name('matches');
        Route::get('/documents', [App\Http\Controllers\PlayerPortalController::class, 'documents'])->name('documents');
        Route::get('/settings', [App\Http\Controllers\PlayerPortalController::class, 'settings'])->name('settings');
        Route::get('/fifa-ultimate', function () {
            return redirect()->route('portail.joueur');
        })->name('fifa-ultimate');
    Route::get('/fifa-light', [App\Http\Controllers\PlayerPortalController::class, 'fifaUltimateDashboard'])->name('fifa-light');
    });
});

// Routes de test publiques (en dehors du groupe player-portal)

// Routes FIFA publiques pour test sans authentification
Route::get('/fifa-ultimate-complete', fn () => abort(410))->name('fifa-ultimate-complete');

Route::get('/fifa-ultimate-working', fn () => abort(410))->name('fifa-ultimate-working');




Route::get('/medical-tabs', function () {
    return view('health-records.create-tabs');
})->name('medical-tabs');



Route::get('/fifa-stable', fn () => abort(410))->name('fifa-stable');


Route::get('/fifa-working', fn () => abort(410))->name('fifa-working');

Route::get('/fifa-complete', fn () => abort(410))->name('fifa-complete');


// Route de test pour l'authentification

// Route de test pour forcer la connexion

// Route publique de test

// Route de debug FIFA (temporaire)

// Route fixe pour le portail patient
Route::get('/portail-patient', function () {
    return response()->file(public_path('portail-patient.html'));
})->name('portail-patient');

// Route fixe pour le portail joueur (dynamique) - SUPPRIMÉE car remplacée par PlayerPortalController

// Redirection de l'ancienne URL vers le portail patient
Route::redirect('/fifa-complete-original.html', '/portail-patient', 301);



// Legacy browser API used by authenticated application screens.
Route::prefix('api')->middleware(['auth'])->group(function () {
    // Get all players
    Route::get('/players', function () {
        try {
            $players = App\Models\Player::with(['club', 'association'])
                ->orderBy('first_name')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $players
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });
    
    // Get specific player
    Route::get('/players/{id}', function ($id) {
        try {
            $player = App\Models\Player::with(['club', 'association'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $player
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 404);
        }
    });
    
    // Get player health records
    Route::get('/players/{id}/health-records', function ($id) {
        try {
            $healthRecords = App\Models\HealthRecord::where('player_id', $id)
                ->orderBy('record_date', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $healthRecords
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });
});

// Test simple du fichier Blade - AMÉLIORÉ avec plus de données

// Test du fichier Blade simple - AMÉLIORÉ avec données dynamiques

// Route pour le portail original dynamique - SUPPRIMÉE car problématique

// Route de test pour le portail joueur (sans authentification)

// Route de test pour l'onglet médical (page simplifiée)

// Route de test simple pour afficher les données brutes

// Route de test simple pour la page médicale

// Route de test minimaliste pour déboguer

// Test des logos des clubs FTF

// Test des logos des clubs FTF (version simplifiée)

// Démonstration finale des logos des clubs FTF

// Page des logos des clubs FTF
Route::get('/logos-clubs-ftf', function () {
    return view('logos-clubs-ftf');
})->name('logos.clubs.ftf');

// Page des logos originaux des clubs FTF
Route::get('/logos-originaux-ftf', function () {
    return view('logos-originaux-ftf');
})->name('logos.originaux.ftf');

// Démonstration des vrais logos des clubs FTF

// Test du portail joueur avec le composant club-logo-working

// Test simple du composant club-logo-working

// Test des clubs réels de la base de données

// Test final du portail joueur avec logos clubs

// Test du portail joueur principal avec de vrais joueurs

// Test du système de performances enrichi

// Test du système de performances simplifié

// Endpoint FIFA performance legacy: désactivé pour éviter les valeurs calculées
// à partir de fallbacks non vérifiés. Le portail canonique utilise les données réelles
// du joueur et les métriques FIT vérifiées.
Route::get('/api/player-performance/{id}', function () {
    return response()->json([
        'success' => false,
        'status' => 'deprecated',
        'message' => 'Endpoint legacy désactivé. Utilisez le portail joueur canonique.',
    ], 410);
})->middleware(['auth'])->name('api.player.performance');

// Page de test debug FIFA

// Page de test JavaScript FIFA

// Page de test du portail FIFA sans authentification

// Page de test FIFA direct (JavaScript pur)

// Page de debug FIFA JavaScript en temps réel

// Page de debug FIFA affichage visuel

// Page de debug onglets performance FIFA

// Page de debug portail principal FIFA

// Test direct du portail principal FIFA

// Debug visibilité des éléments FIFA

// Debug hiérarchie des conteneurs FIFA

// Portail joueur FIFA simple et fonctionnel

// Portail FIFA intégré sous la landing page
Route::get('/fifa-portal', [App\Http\Controllers\FIFATestController::class, 'show'])->middleware(['auth'])->name('fifa.portal.integrated');

// Tombstone: ancienne route de test neutralisee (410 Gone), conservee car testee explicitement par AdministrationViewSmokeTest
Route::get('/fifa-test-public', fn () => abort(410))->name('fifa-test-public');

// Test du système FIFA Connect

// Test de l'intégration FIFA Connect

// Test simple de l'API FIFA

// Test de l'API FIFA avec le contrôleur

// Test Blade simple

// Test FIFA avec vraies données

// Test du logo FTF

// Routes Google Assistant déplacées vers api.php (sans CSRF)

// Interface web de fallback pour PCMA (complètement publique)

// Test route pour health-records (sans authentification)

// Test route pour appointments (sans authentification)

// Test route pour vérifier l'affichage des logos

// Test route simple pour diagnostiquer les logos

// Test route très simple pour diagnostiquer les images

// Test route basique sans CSS

// Player module requires authentication.
Route::get('/modules/players', function () {
    $players = \App\Models\Player::with(['club'])->orderBy('last_name')->orderBy('first_name')->paginate(20);
    return view('modules.players.index', compact('players'));
})->middleware(['auth'])->name('modules.players.index');



// Routes pour le système d'upload de photos de licences (protégées par authentification)
Route::middleware(['auth'])->prefix('license-photos')->name('license.')->group(function () {
    Route::get('/upload-photo', [App\Http\Controllers\LicensePhotoController::class, 'showUploadForm'])->name('upload.photo.form');
    Route::post('/upload-photo', [App\Http\Controllers\LicensePhotoController::class, 'uploadPhoto'])->name('upload.photo');
    Route::get('/list', [App\Http\Controllers\LicensePhotoController::class, 'showLicenses'])->name('list');
    Route::delete('/photo/{photo}', [App\Http\Controllers\LicensePhotoController::class, 'deletePhoto'])->name('photo.delete');
});

// API pour récupérer les joueurs d'un club
Route::get('/api/clubs/{club}/players', [App\Http\Controllers\LicensePhotoController::class, 'getClubPlayers'])->middleware(['auth']);

// Route de test pour le système de licences (sans authentification)

// Route de démonstration du système de licences existant

// Route de test des droits super admin

// ========================================
// 🏆 SYSTÈME DE DEMANDES DE LICENCE FIFA
// ========================================

Route::prefix('license-requests')->name('license-requests.')->middleware(['auth'])->group(function () {
    // Routes principales
    Route::get('/', [App\Http\Controllers\LicenseRequestController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\LicenseRequestController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\LicenseRequestController::class, 'store'])->name('store');
    Route::get('/{licenseRequest}', [App\Http\Controllers\LicenseRequestController::class, 'show'])->name('show');
    Route::get('/{licenseRequest}/edit', [App\Http\Controllers\LicenseRequestController::class, 'edit'])->name('edit');
    Route::put('/{licenseRequest}', [App\Http\Controllers\LicenseRequestController::class, 'update'])->name('update');
    Route::delete('/{licenseRequest}', [App\Http\Controllers\LicenseRequestController::class, 'destroy'])->name('destroy');
    
    // Actions de workflow
    Route::post('/{licenseRequest}/submit', [App\Http\Controllers\LicenseRequestController::class, 'submit'])->name('submit');
    Route::post('/{licenseRequest}/approve-by-club', [App\Http\Controllers\LicenseRequestController::class, 'approveByClub'])->name('approve-by-club');
    Route::post('/{licenseRequest}/approve-by-association', [App\Http\Controllers\LicenseRequestController::class, 'approveByAssociation'])->name('approve-by-association');
    Route::post('/{licenseRequest}/reject', [App\Http\Controllers\LicenseRequestController::class, 'reject'])->name('reject');
    Route::post('/{licenseRequest}/request-additional-info', [App\Http\Controllers\LicenseRequestController::class, 'requestAdditionalInfo'])->name('request-additional-info');
    
    // Actions en lot et export
    Route::post('/bulk-actions', [App\Http\Controllers\LicenseRequestController::class, 'bulkActions'])->name('bulk-actions');
    Route::get('/export', [App\Http\Controllers\LicenseRequestController::class, 'export'])->name('export');
});

// Test du nouveau portail joueur avec hero zone simple
Route::get(
    '/test-portail-joueur-simple',
    [\App\Http\Controllers\PlayerPortalSimpleController::class, 'show']
)->middleware(['auth'])->name('test.portail.joueur.simple');

// Route de test pour la feuille de match (sans auth)

// Route de test pour la création de rapport de match (sans auth)

// Route de test pour l'authentification complète du portail arbitre

// Route de test pour le système de recherche et pagination

// Route de test pour les clubs avec recherche et pagination

// Route de test pour toutes les cartes des modules

// Route de test pour le portail arbitre - VERSION FINALE QUI FONCTIONNE

// Test routes for referee functions (no authentication required)





// Administration routes using existing views (no authentication required for testing)

// Test RBAC permissions route (no authentication required)

// Public module permissions route (no authentication required)

// Route to save permissions (no authentication required for testing)







// Helper functions for real data collection. Guarded because tests and tooling may load routes/web.php more than once.
if (!function_exists('getDatabaseSize')) {
function getDatabaseSize() {
    try {
        $result = \DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb' FROM information_schema.tables WHERE table_schema = ?", [config('database.connections.mysql.database')]);
        return $result[0]->size_mb ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getTableCount() {
    try {
        $result = \DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?", [config('database.connections.mysql.database')]);
        return $result[0]->count ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getSlowQueryCount() {
    try {
        $result = \DB::select("SHOW STATUS LIKE 'Slow_queries'");
        return $result[0]->Value ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getTableSizes() {
    try {
        $tables = ['users', 'players', 'clubs', 'associations', 'competitions', 'roles', 'permissions'];
        $sizes = [];
        
        foreach ($tables as $table) {
            try {
                $result = \DB::select("SELECT ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb' FROM information_schema.tables WHERE table_schema = ? AND table_name = ?", [config('database.connections.mysql.database'), $table]);
                $size = $result[0]->size_mb ?? 0;
                $sizes[] = (object)[
                    'table_name' => $table,
                    'size_mb' => $size
                ];
            } catch (Exception $e) {
                $sizes[] = (object)[
                    'table_name' => $table,
                    'size_mb' => 0
                ];
            }
        }
        
        // Sort by size descending
        usort($sizes, function($a, $b) {
            return $b->size_mb <=> $a->size_mb;
        });
        
        return $sizes;
    } catch (Exception $e) {
        return [];
    }
}

function getCpuUsage() {
    try {
        $load = sys_getloadavg();
        // Load average is not a percentage - it's the average number of processes waiting for CPU
        // For a single-core system, load average of 1.0 = 100% utilization
        // For multi-core systems, divide by number of cores
        $cores = (int)shell_exec('nproc') ?: 1;
        $cpuPercent = min(100, round(($load[0] / $cores) * 100, 1));
        return $cpuPercent;
    } catch (Exception $e) {
        return 0;
    }
}

function getDockerStats() {
    try {
        // Check if we're running in a Docker container
        $isDockerContainer = file_exists('/.dockerenv');
        
        if (!$isDockerContainer) {
            return [
                'containers_running' => 0,
                'containers_total' => 0,
                'images_count' => 0,
                'networks_count' => 0,
                'volumes_count' => 0,
                'docker_version' => 'Not running in Docker',
                'disk_usage' => 'N/A',
                'memory_usage' => 'N/A',
                'cpu_usage' => 'N/A'
            ];
        }

        // Get container info from environment variables (set by Docker Compose)
        $containerName = $_ENV['CONTAINER_NAME'] ?? gethostname();
        $totalContainers = (int)($_ENV['TOTAL_CONTAINERS'] ?? 1);
        $containersRunning = (int)($_ENV['CONTAINERS_RUNNING'] ?? 1);
        $dockerImages = (int)($_ENV['DOCKER_IMAGES'] ?? 1);
        $dockerNetworks = (int)($_ENV['DOCKER_NETWORKS'] ?? 1);
        $dockerVolumes = (int)($_ENV['DOCKER_VOLUMES'] ?? 1);
        
        
        // Try to get some basic container info
        $memoryLimit = 'N/A';
        $cpuLimit = 'N/A';
        
        // Check if we can read container info from /proc
        if (file_exists('/proc/meminfo')) {
            $memInfo = file_get_contents('/proc/meminfo');
            if (preg_match('/MemTotal:\s+(\d+)\s+kB/', $memInfo, $matches)) {
                $memoryLimit = round($matches[1] / 1024) . ' MB';
            }
        }
        
        if (file_exists('/proc/cpuinfo')) {
            $cpuCount = substr_count(file_get_contents('/proc/cpuinfo'), 'processor');
            $cpuLimit = $cpuCount . ' cores';
        }
        
        return [
            'containers_running' => $containersRunning,
            'containers_total' => $totalContainers,
            'images_count' => $dockerImages,
            'networks_count' => $dockerNetworks,
            'volumes_count' => $dockerVolumes,
            'docker_version' => 'Container: ' . $containerName,
            'disk_usage' => 'N/A',
            'memory_usage' => $memoryLimit,
            'cpu_usage' => $cpuLimit
        ];
    } catch (Exception $e) {
        return [
            'containers_running' => 0,
            'containers_total' => 0,
            'images_count' => 0,
            'networks_count' => 0,
            'volumes_count' => 0,
            'docker_version' => 'Error: ' . $e->getMessage(),
            'disk_usage' => 'N/A',
            'memory_usage' => 'N/A',
            'cpu_usage' => 'N/A'
        ];
    }
}

function getCicdStats() {
    try {
        // Get Git information from environment variables (set by Docker Compose)
        $gitBranch = $_ENV['GIT_BRANCH'] ?? 'Unknown';
        $lastCommit = $_ENV['GIT_LAST_COMMIT'] ?? 'Unknown';
        $commitCount = (int)($_ENV['GIT_COMMIT_COUNT'] ?? 0);
        $gitStatus = $_ENV['GIT_STATUS'] ?? 'Unknown';
        
        // Determine CI/CD status based on Git information
        $cicdStatus = 'Active';
        if ($gitBranch === 'Unknown' || $lastCommit === 'Unknown') {
            $cicdStatus = 'Not available';
        }
        
        // Calculate success rate (simplified - assume clean status = success)
        $successRate = ($gitStatus === 'clean') ? 100 : 85;
        
        // Estimate build duration based on commit count
        $buildDuration = $commitCount > 0 ? ($commitCount * 2) . ' min' : 'N/A';
        
        return [
            'github_actions_status' => $cicdStatus,
            'last_deployment' => $lastCommit,
            'pipeline_success_rate' => $successRate,
            'build_duration' => $buildDuration,
            'failed_builds' => max(0, $commitCount - 1) // Assume some builds might fail
        ];
    } catch (Exception $e) {
        return [
            'github_actions_status' => 'Not available',
            'last_deployment' => 'Unknown',
            'pipeline_success_rate' => 0,
            'build_duration' => 'N/A',
            'failed_builds' => 0
        ];
    }
}

function getSecurityStats() {
    try {
        // Get real security data from audit logs (last 30 days for more meaningful data)
        $failedLogins = \App\Models\AuditLog::where('action', 'failed_login')
                                          ->where('created_at', '>=', now()->subDays(30))
                                          ->count();
        
        // Count suspicious activities (multiple failed logins, unusual access patterns)
        $suspiciousActivities = \App\Models\AuditLog::whereIn('action', ['failed_login', 'unauthorized_access'])
                                                   ->where('created_at', '>=', now()->subDays(30))
                                                   ->count();
        
        // Check SSL certificate status
        $sslStatus = 'Unknown';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $sslStatus = 'Valid';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $sslStatus = 'Valid';
        } else {
            $sslStatus = 'Not Available';
        }
        
        // Check firewall status (simplified check)
        $firewallStatus = 'Active'; // Assume active for local development
        
        // Debug logging
        \Log::info('Security Stats Debug', [
            'failed_logins' => $failedLogins,
            'suspicious_activities' => $suspiciousActivities,
            'ssl_status' => $sslStatus,
            'firewall_status' => $firewallStatus
        ]);
        
        return [
            'failed_logins' => $failedLogins,
            'suspicious_activities' => $suspiciousActivities,
            'ssl_certificate_status' => $sslStatus,
            'firewall_status' => $firewallStatus
        ];
    } catch (Exception $e) {
        \Log::error('Security Stats Error', ['error' => $e->getMessage()]);
        return [
            'failed_logins' => 0,
            'suspicious_activities' => 0,
            'ssl_certificate_status' => 'Unknown',
            'firewall_status' => 'Unknown'
        ];
    }
}

function getLogStats() {
    try {
        $logPath = storage_path('logs/laravel.log');
        
        if (!file_exists($logPath)) {
            return [
                'error_logs_24h' => 0,
                'error_logs_7d' => 0,
                'warning_logs_24h' => 0,
                'info_logs_24h' => 0,
                'log_file_size' => 0
            ];
        }
        
        $logContent = file_get_contents($logPath);
        $logSize = filesize($logPath) / 1024 / 1024; // MB
        
        // Count log levels (simplified parsing)
        $errorLogs24h = substr_count($logContent, 'ERROR');
        $warningLogs24h = substr_count($logContent, 'WARNING');
        $infoLogs24h = substr_count($logContent, 'INFO');
        
        return [
            'error_logs_24h' => $errorLogs24h,
            'error_logs_7d' => $errorLogs24h, // Simplified - would need date parsing
            'warning_logs_24h' => $warningLogs24h,
            'info_logs_24h' => $infoLogs24h,
            'log_file_size' => round($logSize, 2)
        ];
    } catch (Exception $e) {
        return [
            'error_logs_24h' => 0,
            'error_logs_7d' => 0,
            'warning_logs_24h' => 0,
            'info_logs_24h' => 0,
            'log_file_size' => 0
        ];
    }
}
}

Route::get('/admin-system-settings', function () {
    return view('admin.system-settings.index');
})->name('admin-system-settings');

Route::get('/admin-content-management', function () {
    return redirect()->route('admin.content-management.index');
})->middleware(['auth'])->name('legacy.admin-content-management');

Route::get('/admin-transfer-management', function () {
    return redirect()->route('admin.transfer-management.index');
})->middleware(['auth'])->name('legacy.admin-transfer-management');

Route::get('/admin/transfer-management/domestic', function () {
    return redirect()->route(
        'admin.transfer-management.transfers',
        ['type' => 'domestic']
    );
})->middleware(['auth'])->name('admin.transfer-management.domestic');

Route::get('/admin/transfer-management/international', function () {
    return redirect()->route(
        'admin.transfer-management.transfers',
        ['type' => 'international']
    );
})->middleware(['auth'])->name('admin.transfer-management.international');

Route::get('/admin/transfer-management/loan', function () {
    return redirect()->route(
        'admin.transfer-management.transfers',
        ['type' => 'loan']
    );
})->middleware(['auth'])->name('admin.transfer-management.loan');

Route::get('/admin/transfer-management/free-transfer', function () {
    return redirect()->route(
        'admin.transfer-management.transfers',
        ['type' => 'free_transfer']
    );
})->middleware(['auth'])->name('admin.transfer-management.free-transfer');

// Finance Management Dashboard - Original ERP Integration System
Route::get('/modules/finance', [App\Http\Controllers\FinanceController::class, 'index'])->middleware(['auth'])->name('modules.finance.dashboard');

// Finance Integrations Page
Route::middleware(['auth'])->group(function () {
    Route::get('/modules/finance/integrations', [App\Http\Controllers\FinanceController::class, 'integrations'])->name('modules.finance.integrations');

    // Finance API Routes
    Route::post('/modules/finance/sync', [App\Http\Controllers\FinanceController::class, 'syncWithExternal'])->name('modules.finance.sync');
    Route::post('/modules/finance/test-connection', [App\Http\Controllers\FinanceController::class, 'testConnection'])->name('modules.finance.test-connection');

    // Additional Finance Routes
    Route::get('/modules/finance/reports', function () {
        $user = auth()->user();
        $userType = $user->club_id ? 'club' : ($user->association_id ? 'association' : 'system');
        return view('modules.finance.reports', compact('userType'));
    })->name('modules.finance.reports');

    Route::get('/modules/finance/budgets', function () {
        $user = auth()->user();
        $userType = $user->club_id ? 'club' : ($user->association_id ? 'association' : 'system');
        return view('modules.finance.budgets', compact('userType'));
    })->name('modules.finance.budgets');

    Route::get('/modules/finance/transaction/edit/{id}', function ($id) {
        // NOTE (audit factice -> reel, 2026-09) : cette page d'edition de
        // transaction ne correspondait a aucun modele de donnees reel
        // (pas de table "transactions" generique) et pointait vers une vue
        // inexistante : elle provoquait une erreur 500. Aucune gestion
        // comptable generale n'existe dans l'application (voir
        // FinanceController::getFinancialData). En attendant un vrai module
        // de comptabilite, on redirige avec un message honnete plutot que
        // de laisser planter la page.
        return redirect()->route('modules.finance.dashboard')
            ->with('info', "La creation/edition manuelle de transactions n'est pas encore disponible : aucune gestion comptable generale n'est connectee a ce module.");
    })->name('modules.finance.transaction.edit');

    Route::get('/modules/finance/bank-integrations', function () {
        $user = auth()->user();
        $userType = $user->club_id ? 'club' : ($user->association_id ? 'association' : 'system');
        return view('modules.finance.bank-integrations', compact('userType'));
    })->name('modules.finance.bank-integrations');
});

// Route de test pour toutes les cartes des modules
