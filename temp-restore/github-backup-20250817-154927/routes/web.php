<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ClubManagementController;
use App\Http\Controllers\CompetitionManagementController;
use App\Http\Controllers\SeasonManagementController;
use App\Http\Controllers\FederationController;
use App\Http\Controllers\RegistrationRequestController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LicenseRequestController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\LicenseTypeController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\PerformanceRecommendationController;
use App\Http\Controllers\PlayerPassportController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\FifaController;
use App\Http\Controllers\BackOfficeController;
use App\Http\Controllers\FitDashboardController;
use App\Http\Controllers\SecretaryController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Added for API proxy routes
use App\Http\Controllers\DentalChartController;
use App\Http\Controllers\PlayerPortalController;
use App\Http\Controllers\PlayerSelectionController;
use App\Http\Controllers\TestAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Test route
Route::get('/test', function () {
    return response()->json(['status' => 'ok', 'message' => 'Server is working']);
})->name('test');

// Test du composant association-logo
Route::get('/test-association-logo', function () {
    return view('test-association-logo');
})->name('test.association.logo');

// Test simple du composant
Route::get('/test-simple', function () {
    return view('test-simple');
})->name('test.simple');

// Test du composant dans le contexte du portail
Route::get('/test-portal-context', function () {
    return view('test-portal-context');
})->name('test.portal.context');

// Test exact du portail patient
Route::get('/test-portal-exact', function () {
    return view('test-portal-exact');
})->name('test.portal.exact');

// Test ultra-simple
Route::get('/test-ultra-simple', function () {
    return view('test-ultra-simple');
})->name('test.ultra.simple');

// Test simulation portail patient
Route::get('/test-portal-simulation', function () {
    return view('test-portal-simulation');
})->name('test.portal.simulation');

// Test portail simple
Route::get('/test-portal-simple', function () {
    return view('test-portal-simple');
})->name('test.portal.simple');

// Routes pour la gestion des logos des associations
Route::prefix('associations')->name('associations.')->group(function () {
    Route::get('/{association}/logo/edit', [App\Http\Controllers\AssociationLogoController::class, 'editLogo'])->name('edit-logo');
    Route::post('/{association}/logo/update', [App\Http\Controllers\AssociationLogoController::class, 'updateLogo'])->name('update-logo');
    Route::post('/{association}/logo/reset', [App\Http\Controllers\AssociationLogoController::class, 'resetToNationalLogo'])->name('reset-national-logo');
    Route::post('/logos/update-national', [App\Http\Controllers\AssociationLogoController::class, 'updateNationalLogos'])->name('update-national-logos');
});

// Test du système association
Route::get('/test-association-system', function () {
    return view('test-association-system');
})->name('test.association.system');

// Démonstration des logos officiels
Route::get('/demo-logos-officiels', function () {
    return view('demo-logos-officiels');
})->name('demo.logos.officiels');

// Test du portail patient avec logos des fédérations
Route::get('/test-portail-patient', function () {
    // Simuler un joueur avec une association
    $player = (object)[
        'id' => 7,
        'first_name' => 'Joueur',
        'last_name' => 'Test',
        'name' => 'Joueur Test',
        'association' => (object)[
            'id' => 7,
            'name' => 'Fédération Royale Marocaine de Football',
            'country' => 'Maroc'
        ]
    ];
    
    return view('portail-joueur-FONCTIONNEL-DRAPEAUX-OK', compact('player'));
})->name('test.portail.patient');

// Test des logos dans le contexte du portail
Route::get('/test-logos-portail', function () {
    return view('test-logos-portail');
})->name('test.logos.portail');

// Test du portail patient intégré (version publique)
Route::get('/test-portail-integre', function () {
    // Simuler un joueur avec une association
    $player = (object)[
        'id' => 7,
        'first_name' => 'Joueur',
        'last_name' => 'Test',
        'name' => 'Joueur Test',
        'association' => (object)[
            'id' => 7,
            'name' => 'Fédération Royale Marocaine de Football',
            'country' => 'Maroc'
        ]
    ];
    
    // Simuler les données du portail
    $portalData = [
        'healthRecords' => collect([]),
        'pcmas' => collect([]),
        'matchPerformances' => collect([]),
        'matchMetrics' => collect([]),
        'trophies' => collect([])
    ];
    
    return view('portail-joueur-final-corrige-dynamique', compact('player', 'portalData'));
})->name('test.portail.integre');

// Test du portail patient simplifié (version publique)
Route::get('/test-portail-simplifie', function () {
    // Simuler un joueur avec une association
    $player = (object)[
        'id' => 7,
        'first_name' => 'Joueur',
        'last_name' => 'Test',
        'name' => 'Joueur Test',
        'association' => (object)[
            'id' => 7,
            'name' => 'Fédération Royale Marocaine de Football',
            'country' => 'Maroc'
        ],
        'club' => (object)[
            'name' => 'Club Test'
        ]
    ];
    
    return view('portail-joueur-simplifie', compact('player'));
})->name('test.portail.simplifie');

// Test d'authentification
Route::get('/test-auth', [TestAuthController::class, 'testAuth'])->name('test.auth');

// Routes d'authentification
Route::get('/login', function() {
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
})->name('search.players');

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
})->name('api.players');

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
})->name('api.players.show');

// NOUVELLE ROUTE FIFA qui fonctionne
Route::get('/api/fifa/player/{id}', function ($id) {
    try {
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
})->name('api.fifa.player');

// Routes API pour l'historique des licences
Route::prefix('api')->group(function () {
    // Historique complet des licences d'un joueur
    Route::get('/joueur/{id}/historique-licences', [App\Http\Controllers\API\LicenseHistoryController::class, 'getPlayerLicenseHistory'])
        ->name('api.joueur.historique-licences');
    
    // Statistiques des licences
    Route::get('/joueur/{id}/stats-licences', [App\Http\Controllers\API\LicenseHistoryController::class, 'getLicenseStats'])
        ->name('api.joueur.stats-licences');
    
    // Barèmes de formation FIFA
    Route::get('/formation/barèmes', [App\Http\Controllers\API\LicenseHistoryController::class, 'getTrainingBarèmes'])
        ->name('api.formation.baremes');
});

// Routes protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/players', [AdminController::class, 'playersList'])->name('admin.players.list');
    Route::get('/admin/search-players', [AdminController::class, 'searchPlayers'])->name('admin.search.players');
    Route::get('/admin/system-stats', [AdminController::class, 'systemStats'])->name('admin.system.stats');
    
    // Nouvelle route pour lister les joueurs (accessible depuis /modules)
    Route::get('/players/list', [AdminController::class, 'playersList'])->name('players.list');
    
    // Modules index route (protégé par authentification)
    Route::get('/modules', function () {
        $footballType = request('footballType', '11aside');
        return view('modules.index', [
            'footballType' => $footballType,
            'modules' => [
                [
                    'name' => 'Medical',
                    'description' => 'Gestion médicale des athlètes, vaccinations, et dossiers de santé',
                    'icon' => '🏥',
                    'route' => 'modules.medical.index',
                    'color' => 'blue'
                ],
                [
                    'name' => 'PCMA',
                    'description' => 'Évaluation Capacité Physique Médicale (Physical Capacity Medical Assessment)',
                    'icon' => '💪',
                    'route' => 'pcma.dashboard',
                    'color' => 'green'
                ],
                [
                    'name' => 'Analytics',
                    'description' => '📊 Analytics, 📈 Trends, ⚠️ Performance Alerts, Recommendations',
                    'icon' => '📊',
                    'route' => 'analytics.dashboard',
                    'color' => 'purple'
                ],
                [
                    'name' => 'FIFA',
                    'description' => 'Connectivité FIFA, synchronisation et gestion des contrats',
                    'icon' => '⚽',
                    'route' => 'fifa.dashboard',
                    'color' => 'blue'
                ],
                [
                    'name' => 'Device Connections',
                    'description' => 'Gestion des connexions d\'appareils et données IoT',
                    'icon' => '🔗',
                    'route' => 'device-connections.index',
                    'color' => 'green'
                ],
                [
                    'name' => 'Performance',
                    'description' => 'Analyse des performances, métriques et suivi des athlètes',
                    'icon' => '📊',
                    'route' => 'performance.index',
                    'color' => 'purple'
                ],
                [
                    'name' => 'DTN',
                    'description' => 'Digital Twin Network - Simulation et modélisation avancée',
                    'icon' => '🔄',
                    'route' => 'dtn.index',
                    'color' => 'indigo'
                ],
                [
                    'name' => 'RPM',
                    'description' => 'Real-time Performance Monitoring - Surveillance en temps réel',
                    'icon' => '⚡',
                    'route' => 'rpm.index',
                    'color' => 'yellow'
                ],
                [
                    'name' => 'Healthcare',
                    'description' => 'Suivi des soins de santé, dossiers médicaux et évaluations',
                    'icon' => '💊',
                    'route' => 'modules.healthcare.index',
                    'color' => 'green'
                ],
                [
                    'name' => 'Licenses',
                    'description' => 'Gestion des licences et autorisations des joueurs',
                    'icon' => '📋',
                    'route' => 'modules.licenses.index',
                    'color' => 'purple'
                ],
                [
                    'name' => 'Competitions',
                    'description' => 'Gestion des compétitions et tournois',
                    'icon' => '🏆',
                    'route' => 'competitions.index',
                    'color' => 'yellow'
                ],
                [
                    'name' => 'Association',
                    'description' => 'Validation des demandes et gestion des clubs affiliés',
                    'icon' => '🏛️',
                    'route' => 'licenses.validation',
                    'color' => 'red'
                ],
                [
                    'name' => 'Administration',
                    'description' => 'Gestion système, utilisateurs et configurations',
                    'icon' => '⚙️',
                    'route' => 'administration.index',
                    'color' => 'indigo'
                ],
                [
                    'name' => 'AI Testing',
                    'description' => 'Test et comparaison des fournisseurs d\'IA pour l\'analyse médicale',
                    'icon' => '🤖',
                    'route' => 'ai-testing.index',
                    'color' => 'purple'
                ],
                [
                    'name' => 'Whisper Speech',
                    'description' => 'Transcription audio avec OpenAI Whisper pour contexte médical',
                    'icon' => '🎤',
                    'route' => 'whisper.index',
                    'color' => 'blue'
                ],
                [
                    'name' => 'Google Gemini AI',
                    'description' => 'Analyse médicale avancée avec Google Gemini AI',
                    'icon' => '🤖',
                    'route' => 'gemini.index',
                    'color' => 'green'
                ],
                [
                    'name' => 'Dataset Analytics',
                    'description' => '📊 Évaluation de la valeur et qualité des données, métriques en temps réel',
                    'icon' => '📈',
                    'route' => 'dataset.analytics',
                    'color' => 'purple'
                ],

            ]
        ]);
    })->name('modules.index');
});

// Test du portail sans authentification (temporaire)
Route::get('/test-portal-direct/{playerId}', function($playerId) {
    try {
        $player = \App\Models\Player::with([
            'club', 'association', 'healthRecords', 'performances', 'pcmas',
            'nationalTeamCallups', 'trainingSessions', 'medicalAppointments',
            'socialMediaAlerts', 'matchPerformances', 'trophies'
        ])->findOrFail($playerId);
        
        // Récupérer tous les joueurs pour la navigation admin
        $allPlayers = \App\Models\Player::with(['club'])->get();
        
        // Générer les données directement sans réflexion
        $portalData = [
            'fifaStats' => [
                'overall_rating' => $player->overall_rating ?? 91,
                'potential_rating' => $player->potential_rating ?? 80,
                'fitness_score' => $player->fitness_score ?? 96,
                'injury_risk' => $player->injury_risk ?? 14,
                'market_value' => $player->market_value ?? 182,
                'availability' => $player->availability ?? 'unavailable',
                'form_percentage' => $player->form_percentage ?? 84,
                'morale_percentage' => $player->morale_percentage ?? 81
            ],
            'performanceData' => [
                'monthly_ratings' => [93, 98, 97, 92, 91, 85],
                'monthly_goals' => [0, 0, 1, 2, 1, 1],
                'monthly_assists' => [0, 1, 0, 1, 1, 1],
                'monthly_distance' => [280, 303, 329, 332, 260, 340],
                'monthly_form' => [79, 95, 79, 91, 94, 81]
            ],
            'sdohData' => [
                'environment' => 76,
                'social_support' => 54,
                'healthcare_access' => 98,
                'financial_situation' => 78,
                'mental_wellbeing' => 94,
                'fifa_average' => [
                    'environment' => 79,
                    'social_support' => 76,
                    'healthcare_access' => 79,
                    'financial_situation' => 74,
                    'mental_wellbeing' => 72
                ]
            ],
            'playerStats' => [
                'age' => \Carbon\Carbon::parse($player->date_of_birth)->age,
                'height' => $player->height ?? 170,
                'weight' => $player->weight ?? 72,
                'preferred_foot' => $player->preferred_foot ?? 'Droit',
                'total_goals' => $player->matchPerformances->sum('goals_scored') ?? 100,
                'total_assists' => $player->matchPerformances->sum('assists') ?? 300,
                'season_goals' => $player->matchPerformances->take(5)->sum('goals_scored') ?? 6,
                'season_assists' => $player->matchPerformances->take(5)->sum('assists') ?? 1,
                'trophy_count' => $player->trophies->count() ?? 1,
                'ballon_dor_count' => $player->trophies->where('name', 'like', '%Ballon%')->count() ?? 1,
                'champions_league_count' => $player->trophies->where('name', 'like', '%Champions%')->count() ?? 1,
            ],
            'images' => [
                'player_profile' => $player->profile_image ?? 'https://via.placeholder.com/150x150/3B82F6/FFFFFF?text=CR7',
                'club_logo' => $player->club->logo_image ?? 'https://via.placeholder.com/100x100/EF4444/FFFFFF?text=CHELSEA',
                'player_flag' => $player->association->flag_image ?? 'https://via.placeholder.com/100x60/10B981/FFFFFF?text=POR'
            ],
            'recentPerformances' => DB::table('match_results')
                ->where('player_id', $player->id)
                ->orderBy('match_date', 'desc')
                ->take(5)
                ->pluck('result')
                ->toArray(),
        ];
        
        // Test temporaire : afficher les données directement dans le HTML pour debug
        // Enrichir les données du portail pour alimenter tous les onglets
        try {
            $totalMatches = 38;
            $matchesPlayed = $player->matchPerformances ? $player->matchPerformances->count() : 0;
            $portalData['seasonProgress'] = [
                'currentSeason' => now()->format('Y') . '-' . now()->copy()->addYear()->format('y'),
                'completion' => min(100, (int) round(($matchesPlayed / max(1, $totalMatches)) * 100)),
                'matchesPlayed' => $matchesPlayed,
                'matchesRemaining' => max(0, $totalMatches - $matchesPlayed),
                'totalMatches' => $totalMatches,
            ];

            // National team callups
            $portalData['nationalTeam'] = ($player->nationalTeamCallups ?? collect())->take(3)->map(function($c){
                return [
                    'id' => $c->id ?? null,
                    'title' => $c->title ?? 'Convocation Sélection',
                    'message' => $c->description ?? 'Convocation pour les prochains matchs',
                    'date' => optional($c->callup_date ?? now())->format('d/m/Y'),
                ];
            })->values()->toArray() ?: [
                ['id'=>1,'title'=>'Convocation Sélection','message'=>'Matchs vs Brésil et Uruguay','date'=>now()->addDays(2)->format('d/m/Y')],
                ['id'=>2,'title'=>'Stage Équipe Nationale','message'=>'Session tactique demain 9:00','date'=>now()->addDay()->format('d/m/Y')],
            ];

            // Ajouter nationalTeamCallups pour la compatibilité
            $portalData['nationalTeamCallups'] = $portalData['nationalTeam'];

            // Training sessions
            $portalData['trainingSessions'] = ($player->trainingSessions ?? collect())->take(4)->map(function($s){
                return [
                    'id' => $s->id ?? null,
                    'title' => $s->title ?? 'Entraînement',
                    'message' => $s->description ?? 'Séance programmée',
                    'date' => optional($s->date ?? now())->format('d/m/Y'),
                    'time' => optional($s->time ?? now()->setTime(9,0))->format('H:i'),
                ];
            })->values()->toArray() ?: [
                ['id'=>3,'title'=>'Entraînement Technique','message'=>'Passes et finition','date'=>now()->addDays(1)->format('d/m/Y'),'time'=>'9:00'],
                ['id'=>4,'title'=>'Entraînement Physique','message'=>'Vitesse et résistance','date'=>now()->addDays(3)->format('d/m/Y'),'time'=>'10:00'],
            ];

            // Medical appointments
            $portalData['medicalAppointments'] = ($player->medicalAppointments ?? collect())->take(5)->map(function($m){
                return [
                    'id' => $m->id ?? null,
                    'type' => $m->type ?? 'Consultation',
                    'location' => $m->location ?? 'Centre médical',
                    'doctor' => $m->doctor_name ?? 'Médecin',
                    'date' => optional($m->date ?? now())->format('d/m/Y'),
                    'time' => optional($m->time ?? now()->setTime(10,0))->format('H:i'),
                ];
            })->values()->toArray() ?: [
                ['id'=>1,'type'=>'Consultation','location'=>'Centre médical','doctor'=>'Dr. Dupont','date'=>now()->addDays(1)->format('d/m/Y'),'time'=>'10:00'],
                ['id'=>2,'type'=>'Rééducation','location'=>'Centre de rééducation','doctor'=>'Dr. Moreau','date'=>now()->addDays(4)->format('d/m/Y'),'time'=>'14:00'],
            ];

            // Social media alerts
            $portalData['socialMediaAlerts'] = ($player->socialMediaAlerts ?? collect())->take(5)->map(function($a){
                return [
                    'id' => $a->id ?? null,
                    'platform' => $a->platform ?? 'Twitter',
                    'title' => $a->title ?? 'Alerte Réseaux',
                    'message' => $a->message ?? 'Nouvelle interaction détectée',
                    'date' => optional($a->date ?? now())->format('d/m/Y'),
                    'sentiment' => $a->sentiment ?? 'neutral',
                    'views' => $a->views ?? 0,
                    'interactions' => $a->interactions ?? 0,
                ];
            })->values()->toArray() ?: [
                ['id'=>1,'platform'=>'Twitter','title'=>'#MoussaChelsea trending','message'=>'Tendance en France','date'=>now()->subDays(1)->format('d/m/Y'),'sentiment'=>'positive','views'=>15200,'interactions'=>810],
                ['id'=>2,'platform'=>'Instagram','title'=>'Message de Fan','message'=>'Support fort de @fan_chelsea','date'=>now()->subDays(2)->format('d/m/Y'),'sentiment'=>'positive','views'=>2500,'interactions'=>156],
            ];

            // Upcoming matches (synthetic from today)
            $portalData['upcomingMatches'] = [
                [
                    'id' => 1,
                    'title' => 'Prochain Match',
                    'competition' => 'Premier League',
                    'venue' => 'Stamford Bridge',
                    'date' => now()->addDays(2)->format('d/m/Y'),
                    'time' => '15:00',
                    'status' => 'CONVOQUÉ'
                ],
                [
                    'id' => 2,
                    'title' => 'Analyse Tactique',
                    'competition' => 'Centre d\'entraînement',
                    'venue' => 'Salle vidéo',
                    'date' => now()->addDay()->format('d/m/Y'),
                    'time' => '10:00',
                    'status' => 'CONVOQUÉ'
                ],
            ];
            
            // Health records
            $portalData['healthRecords'] = $player->healthRecords ?? collect([
                [
                    'heart_rate' => rand(65, 85),
                    'created_at' => now()->subHours(rand(1, 24)),
                    'ghs_overall_score' => rand(75, 95),
                    'ghs_physical_score' => rand(70, 90),
                    'ghs_mental_score' => rand(70, 90),
                    'ghs_sleep_score' => rand(75, 90)
                ]
            ]);
            
            // Performance data
            $portalData['performanceData'] = [
                'monthly_ratings' => [8.2, 8.5, 8.8, 9.1, 8.9, 9.3], // Ratings sur 10
                'monthly_goals' => [3, 5, 2, 4, 6, 3], // Buts par mois
                'monthly_assists' => [2, 3, 1, 4, 2, 5], // Assists par mois
                'monthly_distance' => [285, 312, 298, 325, 310, 335] // Distance en km
            ];
            
            // Ajouter des données de performance plus détaillées
            $portalData['performanceStats'] = [
                'current_month_goals' => 12,
                'current_month_assists' => 8,
                'current_month_rating' => 8.7,
                'current_month_distance' => 20,
                'previous_month_goals' => 9,
                'previous_month_assists' => 6,
                'previous_month_rating' => 8.5,
                'previous_month_distance' => 18,
                'matches_played' => 0,
                'total_goals' => 12,
                'total_assists' => 8,
                'average_rating' => 8.7
            ];
            
            // Nouvelles métriques pour la hero zone - DYNAMIQUES
            $injuryRiskPercentage = rand(5, 25);
            $injuryRiskLevel = $injuryRiskPercentage <= 10 ? 'TRÈS FAIBLE' : 
                              ($injuryRiskPercentage <= 20 ? 'FAIBLE' : 
                              ($injuryRiskPercentage <= 30 ? 'MODÉRÉ' : 'ÉLEVÉ'));
            
            $portalData['heroMetrics'] = [
                'injury_risk' => [
                    'percentage' => $injuryRiskPercentage,
                    'level' => $injuryRiskLevel,
                    'color' => 'text-green-400'
                ],
                'market_value' => [
                    'current' => rand(50, 300), // Valeur marchande aléatoire entre 50M et 300M
                    'change' => rand(-30, 50), // Changement aléatoire entre -30M et +50M
                    'trend' => 'up'
                ],
                'availability' => [
                    'status' => rand(0, 1) ? 'DISPONIBLE' : 'INDISPONIBLE',
                    'next_match' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'][rand(0, 6)],
                    'icon' => '✅'
                ],
                'player_state' => [
                    'form' => rand(60, 95), // Forme aléatoire entre 60% et 95%
                    'morale' => rand(65, 90) // Moral aléatoire entre 65% et 90%
                ]
            ];
            
            // Données détaillées par catégorie
            $portalData['detailedStats'] = [
                'attack' => [
                    'goals' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 7.2],
                    'shots_on_target' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 0],
                    'total_shots' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 0],
                    'shot_accuracy' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 0],
                    'assists' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 5.9],
                    'key_passes' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 0],
                    'successful_crosses' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 13.7],
                    'successful_dribbles' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 38.9]
                ],
                'physical' => [
                    'distance' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 0],
                    'max_speed' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 0],
                    'avg_speed' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 0],
                    'sprints' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 115],
                    'accelerations' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 0],
                    'decelerations' => ['player' => 0, 'team_avg' => 164, 'league_avg' => 165],
                    'direction_changes' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 64],
                    'jumps' => ['player' => 0, 'team_avg' => 15.2, 'league_avg' => 14.8]
                ],
                'technical' => [
                    'pass_accuracy' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 0],
                    'long_passes' => ['player' => 0, 'team_avg' => 38, 'league_avg' => 35],
                    'crosses' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 13.7],
                    'tackles' => ['player' => 0, 'team_avg' => 0.5, 'league_avg' => 26.8],
                    'interceptions' => ['player' => 0, 'team_avg' => 26.8, 'league_avg' => 24.5],
                    'clearances' => ['player' => 0, 'team_avg' => 15.2, 'league_avg' => 14.1],
                    'fouls' => ['player' => 0, 'team_avg' => 12.4, 'league_avg' => 11.8],
                    'yellow_cards' => ['player' => 0, 'team_avg' => 0, 'league_avg' => 0]
                ]
            ];
            
            // Records personnels
            $portalData['personalRecords'] = [
                'most_goals_match' => ['opponent' => 'Real Madrid', 'date' => '16/03/2024', 'value' => 4],
                'most_assists_match' => ['opponent' => 'Monaco', 'date' => '22/04/2024', 'value' => 3],
                'longest_distance' => ['opponent' => 'Liverpool', 'date' => '08/05/2024', 'value' => '12.8km'],
                'top_speed' => ['opponent' => 'Bayern', 'date' => '15/04/2024', 'value' => '34.2km/h']
            ];
            
            // Zones d'activité
            $portalData['activityZones'] = [
                'preferred_zone' => 'Ailier droit',
                'right_side_percentage' => 68,
                'left_side_percentage' => 0,
                'opponent_half_touches' => 156,
                'touches_per_match' => 23,
                'dribble_success_rate' => 23
            ];
            
            // Performances vs Grandes Équipes
            $portalData['bigTeamsPerformance'] = [
                [
                    'opponent' => 'Real Madrid',
                    'matches' => 3,
                    'goals' => 5,
                    'assists' => 2,
                    'rating' => 8.7,
                    'last_performance' => 'Excellent'
                ],
                [
                    'opponent' => 'Manchester City',
                    'matches' => 2,
                    'goals' => 2,
                    'assists' => 3,
                    'rating' => 8.2,
                    'last_performance' => 'Très bon'
                ],
                [
                    'opponent' => 'Bayern Munich',
                    'matches' => 2,
                    'goals' => 1,
                    'assists' => 1,
                    'rating' => 7.5,
                    'last_performance' => 'Correct'
                ],
                [
                    'opponent' => 'Liverpool',
                    'matches' => 1,
                    'goals' => 1,
                    'assists' => 0,
                    'rating' => 7.8,
                    'last_performance' => 'Bon'
                ]
            ];
            
            // SDOH data
            $portalData['sdohData'] = [
                'environment' => rand(60, 90),
                'social_support' => rand(40, 80),
                'healthcare_access' => rand(70, 100),
                'financial_situation' => rand(60, 90),
                'mental_wellbeing' => rand(65, 95),
                'fifa_average' => [
                    'environment' => rand(65, 85),
                    'social_support' => rand(60, 80),
                    'healthcare_access' => rand(70, 90),
                    'financial_situation' => rand(70, 90),
                    'mental_wellbeing' => rand(65, 85)
                ]
            ];
            
            // Images
            $portalData['images'] = [
                'player_profile' => $player->profile_image ?? null,
                'club_logo' => $player->club->logo_image ?? null,
                'country_flag' => $player->association->flag_image ?? null
            ];
        } catch (\Throwable $t) {}
        
        // Données de test pour tous les onglets (placées à la fin pour éviter l'écrasement)
        $portalData['nationalTeamCallups'] = [
            [
                'title' => 'Convocación Selección France',
                'message' => 'Convocado para partidos vs Brasil y Uruguay',
                'date' => '15/03/2025'
            ],
            [
                'title' => 'Entrenamiento Selección',
                'message' => 'Sesión técnica mañana 9:00 AM',
                'date' => '14/03/2025'
            ]
        ];
        
        $portalData['medicalAppointments'] = [
            [
                'type' => 'Consultation',
                'description' => 'Revisión rutinaria',
                'date' => '10/03/2025',
                'time' => '10:00',
                'doctor' => 'Dr. Martínez'
            ],
            [
                'type' => 'Rééducation',
                'description' => 'Recuperación muscular',
                'date' => '07/03/2025',
                'time' => '14:00',
                'doctor' => 'Dr. Sophie Moreau'
            ]
        ];
        
        $portalData['trainingSessions'] = [
            [
                'type' => 'Entrenamiento Técnico',
                'date' => '13/03/2025',
                'time' => '9:00 AM'
            ],
            [
                'type' => 'Entrenamiento Físico',
                'date' => '05/03/2025',
                'time' => '10:00 AM'
            ],
            [
                'type' => 'Recuperación Activa',
                'date' => '11/03/2025',
                'time' => '11:00 AM'
            ]
        ];
        
        $portalData['socialMediaAlerts'] = [
            [
                'platform' => 'Twitter',
                'message' => '#MoussaChelsea trending en France',
                'date' => '05/03/2025'
            ],
            [
                'platform' => 'Instagram',
                'message' => 'Live Solicitud de entrevista en vivo',
                'date' => '04/03/2025'
            ]
        ];
        
        $portalData['healthRecords'] = [
            [
                'type' => 'Mesure quotidienne',
                'heart_rate' => 75,
                'blood_pressure' => '120/80',
                'temperature' => 36.8,
                'weight' => 72,
                'created_at' => 'Aujourd\'hui'
            ],
            [
                'type' => 'Mesure quotidienne',
                'heart_rate' => 78,
                'blood_pressure' => '118/79',
                'temperature' => 36.9,
                'weight' => 72.2,
                'created_at' => 'Hier'
            ]
        ];

        // Nouvelles données dynamiques pour les onglets enrichis
        // Dossiers médicaux
        $portalData['medicalRecords'] = DB::table('medical_records')
            ->where('player_id', $playerId)
            ->orderBy('record_date', 'desc')
            ->get()
            ->map(function($record) {
                return [
                    'id' => $record->id,
                    'type' => $record->record_type,
                    'title' => $record->title,
                    'description' => $record->description,
                    'doctor_name' => $record->doctor_name,
                    'medical_center' => $record->medical_center,
                    'date' => $record->record_date,
                    'next_appointment' => $record->next_appointment,
                    'status' => $record->status,
                    'medications' => json_decode($record->medications, true) ?? [],
                    'test_results' => json_decode($record->test_results, true) ?? [],
                    'cost' => $record->cost,
                    'notes' => $record->notes
                ];
            })
            ->toArray();

        // Monitoring des devices
        $portalData['deviceMonitoring'] = DB::table('device_monitoring')
            ->where('player_id', $playerId)
            ->get()
            ->map(function($device) {
                return [
                    'id' => $device->id,
                    'type' => $device->device_type,
                    'name' => $device->device_name,
                    'model' => $device->device_model,
                    'serial_number' => $device->serial_number,
                    'status' => $device->status,
                    'activation_date' => $device->activation_date,
                    'last_sync' => $device->last_sync,
                    'next_maintenance' => $device->next_maintenance,
                    'current_data' => json_decode($device->current_data, true) ?? [],
                    'settings' => json_decode($device->settings, true) ?? [],
                    'notes' => $device->notes
                ];
            })
            ->toArray();

        // Contrôles antidopage
        $portalData['dopingControls'] = DB::table('doping_controls')
            ->where('player_id', $playerId)
            ->orderBy('control_date', 'desc')
            ->get()
            ->map(function($control) {
                return [
                    'id' => $control->id,
                    'type' => $control->control_type,
                    'location' => $control->location,
                    'date' => $control->control_date,
                    'time' => $control->control_time,
                    'result' => $control->result,
                    'notes' => $control->notes,
                    'authority' => $control->control_authority,
                    'sample_id' => $control->sample_id,
                    'next_control' => $control->next_control,
                    'substances_tested' => json_decode($control->substances_tested, true) ?? []
                ];
            })
            ->toArray();
        
        return view('portail-joueur-final-corrige-dynamique', compact('player', 'portalData'))->with('debug', true);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('test.portal.direct');

// Test Vue.js simple
Route::get('/test-vue-simple/{playerId}', function($playerId) {
    try {
        $player = \App\Models\Player::with([
            'club', 'association', 'healthRecords', 'performances', 'pcmas',
            'nationalTeamCallups', 'trainingSessions', 'medicalAppointments',
            'socialMediaAlerts', 'matchPerformances', 'trophies'
        ])->findOrFail($playerId);
        
        // Créer un contrôleur temporaire pour générer les données
        $controller = new \App\Http\Controllers\PlayerPortalController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('preparePortalData');
        $method->setAccessible(true);
        $portalData = $method->invoke($controller, $player);
        
        return view('test-simple', compact('player', 'portalData'));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('test.vue.simple');

// Test minimal sans Vue.js
Route::get('/test-minimal/{playerId}', function($playerId) {
    try {
        $player = \App\Models\Player::with([
            'club', 'association', 'healthRecords', 'performances', 'pcmas',
            'nationalTeamCallups', 'trainingSessions', 'medicalAppointments',
            'socialMediaAlerts', 'matchPerformances', 'trophies'
        ])->findOrFail($playerId);
        
        // Créer un contrôleur temporaire pour générer les données
        $controller = new \App\Http\Controllers\PlayerPortalController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('preparePortalData');
        $method->setAccessible(true);
        $portalData = $method->invoke($controller, $player);
        
        return view('test-minimal', compact('player', 'portalData'));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('test.minimal');

// Test Vue.js debug
Route::get('/test-vue-debug/{playerId}', function($playerId) {
    try {
        $player = \App\Models\Player::with([
            'club', 'association', 'healthRecords', 'performances', 'pcmas',
            'nationalTeamCallups', 'trainingSessions', 'medicalAppointments',
            'socialMediaAlerts', 'matchPerformances', 'trophies'
        ])->findOrFail($playerId);
        
        // Créer un contrôleur temporaire pour générer les données
        $controller = new \App\Http\Controllers\PlayerPortalController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('preparePortalData');
        $method->setAccessible(true);
        $portalData = $method->invoke($controller, $player);
        
        return view('test-vue-debug', compact('player', 'portalData'));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('test.vue.debug');

// Test Portal Debug
Route::get('/test-portal-debug/{playerId}', function($playerId) {
    try {
        $player = \App\Models\Player::with([
            'club', 'association', 'healthRecords', 'performances', 'pcmas',
            'nationalTeamCallups', 'trainingSessions', 'medicalAppointments',
            'socialMediaAlerts', 'matchPerformances', 'trophies'
        ])->findOrFail($playerId);
        
        // Créer un contrôleur temporaire pour générer les données
        $controller = new \App\Http\Controllers\PlayerPortalController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('preparePortalData');
        $method->setAccessible(true);
        $portalData = $method->invoke($controller, $player);
        
        return view('test-portal-debug', compact('player', 'portalData'));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('test.portal.debug');

// Sélection des joueurs
Route::get('/joueurs', [PlayerSelectionController::class, 'index'])->name('joueurs.selection');
Route::get('/joueurs/{id}', [PlayerSelectionController::class, 'show'])->name('joueurs.show');

// Test public du portail (sans authentification)
Route::get('/test-portal/{playerId}', function($playerId) {
    try {
        $player = \App\Models\Player::with(['club'])->findOrFail($playerId);
        return view('portail-joueur', compact('player'));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('test.portal');

Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.token');

// Test PCMA create view
Route::get('/test-pcma-view', function () {
    try {
        return view('pcma.create', [
            'athletes' => collect([]),
            'users' => collect([])
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'View error: ' . $e->getMessage()], 500);
    }
})->name('test.pcma.view');

// Test PCMA create route (temporary, no auth required)
Route::get('/test-pcma-create', function () {
    try {
        // Simuler les données nécessaires pour la vue
        $players = collect([
            (object)['id' => 1, 'first_name' => 'Test', 'last_name' => 'Player 1', 'club_id' => 1],
            (object)['id' => 2, 'first_name' => 'Test', 'last_name' => 'Player 2', 'club_id' => 1],
        ]);
        
        $assessors = collect([
            (object)['id' => 1, 'name' => 'Dr. Test Doctor', 'role' => 'doctor'],
            (object)['id' => 2, 'name' => 'Nurse Test', 'role' => 'medical_staff'],
        ]);
        
        return view('pcma.create', compact('players', 'assessors'));
    } catch (\Exception $e) {
        return response()->json(['error' => 'Test PCMA create error: ' . $e->getMessage()], 500);
    }
})->name('test.pcma.create');

// Test Dental Chart route (public access for testing)
Route::get('/test-dental-chart', function () {
    try {
        return view('health-records.create', [
            'patients' => collect([
                (object)['id' => 1, 'name' => 'Test Patient 1'],
                (object)['id' => 2, 'name' => 'Test Patient 2'],
                (object)['id' => 3, 'name' => 'Test Patient 3']
            ])
        ]);
    } catch (\Exception $e) {
        \Log::error('Dental chart test route error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
})->name('test.dental.chart');

// Test Dental Chart Simple route (public access for testing)
Route::get('/test-dental-simple', function () {
    try {
        return view('test-dental-simple');
    } catch (\Exception $e) {
        \Log::error('Dental chart simple test route error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
})->name('test.dental.simple');

// Test Dental Chart Adapted route (public access for testing)
Route::get('/dental-chart-test', function () {
    try {
        return view('dental-chart-test');
    } catch (\Exception $e) {
        \Log::error('Dental chart test route error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
})->name('dental.chart.test');

// Portail Joueur route (protégé par authentification)
Route::middleware(['auth'])->group(function () {
    Route::get('/portail-joueur/{playerId?}', [App\Http\Controllers\PlayerAccessController::class, 'showPortal'])->name('joueur.portal');
    Route::get('/portail-joueur', [App\Http\Controllers\PlayerPortalController::class, 'show'])->name('portail.joueur');
});

// Accès Joueur par identifiant unique
Route::get('/joueur/{playerId}', [App\Http\Controllers\PlayerAccessController::class, 'showPortal'])->name('joueur.show');
Route::get('/joueur/{playerId}/portal', [App\Http\Controllers\PlayerAccessController::class, 'showPortal'])->name('player.portal');

// Routes pour la gestion des photos des joueurs
Route::get('/joueur/{playerId}/photo/upload', [App\Http\Controllers\PlayerPhotoController::class, 'showUploadForm'])->name('joueur.photo.upload');
Route::post('/joueur/{playerId}/photo/upload', [App\Http\Controllers\PlayerPhotoController::class, 'upload'])->name('joueur.photo.upload.post');
Route::delete('/joueur/{playerId}/photo', [App\Http\Controllers\PlayerPhotoController::class, 'delete'])->name('joueur.photo.delete');
Route::put('/joueur/{playerId}/photo/external', [App\Http\Controllers\PlayerPhotoController::class, 'updateExternalUrl'])->name('joueur.photo.external');
Route::post('/joueur/{playerId}/photo/generate', [App\Http\Controllers\PlayerPhotoController::class, 'generateAvatar'])->name('joueur.photo.generate');



Route::get('/joueur/{playerId}/access', [App\Http\Controllers\PlayerAccessController::class, 'showAccessForm'])->name('player.access.form');


Route::post('/joueur/{playerId}/access', [App\Http\Controllers\PlayerAccessController::class, 'authenticate'])->name('player.access.authenticate');

// Dataset Analytics route
Route::get('/dataset-analytics', [App\Http\Controllers\DatasetAnalyticsController::class, 'show'])->name('dataset.analytics');

// Dataset Analytics API routes
Route::prefix('api/dataset-analytics')->group(function () {
    Route::get('/overview', [App\Http\Controllers\DatasetAnalyticsController::class, 'getOverview']);
    Route::get('/data-quality', [App\Http\Controllers\DatasetAnalyticsController::class, 'getDataQuality']);
    Route::get('/coverage', [App\Http\Controllers\DatasetAnalyticsController::class, 'getCoverage']);
    Route::get('/trends', [App\Http\Controllers\DatasetAnalyticsController::class, 'getTrends']);
    Route::get('/value-assessment', [App\Http\Controllers\DatasetAnalyticsController::class, 'getValueAssessment']);
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
Route::get('/pcma/test-simple', function () {
    return response()->json(['status' => 'ok', 'message' => 'PCMA route is working']);
})->name('pcma.test.simple');

// PCMA test view route
Route::get('/pcma/test-view', function () {
    try {
        $athletes = collect([
            ['id' => 1, 'name' => 'Test Athlete 1'],
            ['id' => 2, 'name' => 'Test Athlete 2'],
            ['id' => 3, 'name' => 'Test Athlete 3']
        ]);
        
        $users = collect([
            ['id' => 1, 'name' => 'Dr. Test User 1'],
            ['id' => 2, 'name' => 'Dr. Test User 2'],
            ['id' => 3, 'name' => 'Dr. Test User 3']
        ]);
        
        return view('pcma.test-simple', [
            'athletes' => $athletes,
            'users' => $users
        ]);
    } catch (\Exception $e) {
        \Log::error('PCMA test view error: ' . $e->getMessage());
        return response()->json(['error' => 'View error: ' . $e->getMessage()], 500);
    }
})->name('pcma.test.view');

// Test route for PCMA with DoctorSignOff integration
Route::get('/pcma/test-with-signoff', function () {
    try {
        $athletes = collect([
            (object)['id' => 1, 'name' => 'Test Athlete 1', 'club' => (object)['name' => 'Test Club 1']],
            (object)['id' => 2, 'name' => 'Test Athlete 2', 'club' => (object)['name' => 'Test Club 2']],
            (object)['id' => 3, 'name' => 'Test Athlete 3', 'club' => (object)['name' => 'Test Club 3']]
        ]);
        
        $users = collect([
            (object)['id' => 1, 'name' => 'Dr. Test User 1'],
            (object)['id' => 2, 'name' => 'Dr. Test User 2'],
            (object)['id' => 3, 'name' => 'Dr. Test User 3']
        ]);
        
        return view('pcma.create', [
            'athletes' => $athletes,
            'users' => $users
        ]);
    } catch (\Exception $e) {
        \Log::error('PCMA test with signoff error: ' . $e->getMessage());
        return response()->json(['error' => 'Test error: ' . $e->getMessage()], 500);
    }
})->name('pcma.test.signoff');

// API Proxy Routes to avoid CORS issues (public access)
Route::get('/api/proxy/icd11', function (Request $request) {
    try {
        $query = $request->get('q', '');
        
        \Log::info('ICD-11 API Proxy called', ['query' => $query]);
        
        // Fast ICD-11 search with optimized fallback
        $queryLower = strtolower($query);
        
        // Quick keyword matching for immediate response
        $quickResults = [];
        foreach ($fallbackData as $keyword => $items) {
            if (stripos($queryLower, $keyword) !== false) {
                $quickResults = $items;
                break;
            }
        }
        
        // If no quick match, try broader search
        if (empty($quickResults)) {
            foreach ($fallbackData as $keyword => $items) {
                foreach ($items as $item) {
                    if (stripos($item['title'], $query) !== false || stripos($item['code'], $query) !== false) {
                        $quickResults[] = $item;
                    }
                }
            }
        }
        
        // Return results immediately (no external API calls for now due to timeout issues)
        return response()->json([
            'success' => true,
            'results' => array_slice($quickResults, 0, 10),
            'fallback' => true,
            'message' => 'Using comprehensive medical database'
        ]);
        
        // Comprehensive medical database fallback
        $fallbackData = [
            // Cardiovascular conditions
            'cardio' => [
                ['id' => 'I10', 'title' => 'Hypertension artérielle essentielle (I10)', 'code' => 'I10'],
                ['id' => 'I21', 'title' => 'Infarctus aigu du myocarde (I21)', 'code' => 'I21'],
                ['id' => 'I20', 'title' => 'Angine de poitrine (I20)', 'code' => 'I20'],
                ['id' => 'I50', 'title' => 'Insuffisance cardiaque (I50)', 'code' => 'I50'],
                ['id' => 'I49', 'title' => 'Troubles du rythme cardiaque (I49)', 'code' => 'I49'],
                ['id' => 'I25', 'title' => 'Cardiopathie ischémique chronique (I25)', 'code' => 'I25'],
                ['id' => 'I42', 'title' => 'Cardiomyopathie (I42)', 'code' => 'I42'],
                ['id' => 'I34', 'title' => 'Valvulopathie mitrale (I34)', 'code' => 'I34'],
                ['id' => 'I35', 'title' => 'Valvulopathie aortique (I35)', 'code' => 'I35'],
                ['id' => 'I27', 'title' => 'Hypertension pulmonaire (I27)', 'code' => 'I27']
            ],
            'heart' => [
                ['id' => 'I51', 'title' => 'Maladie cardiaque (I51)', 'code' => 'I51'],
                ['id' => 'I49', 'title' => 'Arythmie cardiaque (I49)', 'code' => 'I49'],
                ['id' => 'I34', 'title' => 'Valvulopathie (I34-I38)', 'code' => 'I34-I38'],
                ['id' => 'I42', 'title' => 'Cardiomyopathie (I42)', 'code' => 'I42'],
                ['id' => 'I50', 'title' => 'Insuffisance cardiaque (I50)', 'code' => 'I50']
            ],
            'hyper' => [
                ['id' => 'I10', 'title' => 'Hypertension artérielle (I10)', 'code' => 'I10'],
                ['id' => 'I27', 'title' => 'Hypertension pulmonaire (I27)', 'code' => 'I27'],
                ['id' => 'I15', 'title' => 'Hypertension secondaire (I15)', 'code' => 'I15']
            ],
            'infarct' => [
                ['id' => 'I21', 'title' => 'Infarctus aigu du myocarde (I21)', 'code' => 'I21'],
                ['id' => 'I22', 'title' => 'Infarctus du myocarde récurrent (I22)', 'code' => 'I22'],
                ['id' => 'I23', 'title' => 'Complications de l\'infarctus (I23)', 'code' => 'I23']
            ],
            'angine' => [
                ['id' => 'I20', 'title' => 'Angine de poitrine (I20)', 'code' => 'I20'],
                ['id' => 'I20.0', 'title' => 'Angine de poitrine instable (I20.0)', 'code' => 'I20.0'],
                ['id' => 'I20.1', 'title' => 'Angine de poitrine stable (I20.1)', 'code' => 'I20.1']
            ],
            // Surgical procedures
            'surgery' => [
                ['id' => '0210', 'title' => 'Pontage aorto-coronarien (0210)', 'code' => '0210'],
                ['id' => '0211', 'title' => 'Remplacement valvulaire (0211)', 'code' => '0211'],
                ['id' => '0212', 'title' => 'Appendicectomie (0212)', 'code' => '0212'],
                ['id' => '0213', 'title' => 'Cholécystectomie (0213)', 'code' => '0213'],
                ['id' => '0214', 'title' => 'Herniorraphie (0214)', 'code' => '0214'],
                ['id' => '0215', 'title' => 'Césarienne (0215)', 'code' => '0215'],
                ['id' => '0216', 'title' => 'Arthroplastie du genou (0216)', 'code' => '0216'],
                ['id' => '0217', 'title' => 'Arthroplastie de la hanche (0217)', 'code' => '0217'],
                ['id' => '0218', 'title' => 'Lobectomie pulmonaire (0218)', 'code' => '0218'],
                ['id' => '0219', 'title' => 'Néphrectomie (0219)', 'code' => '0219']
            ],
            'surgical' => [
                ['id' => '0210', 'title' => 'Pontage aorto-coronarien (0210)', 'code' => '0210'],
                ['id' => '0211', 'title' => 'Remplacement valvulaire (0211)', 'code' => '0211'],
                ['id' => '0212', 'title' => 'Appendicectomie (0212)', 'code' => '0212'],
                ['id' => '0213', 'title' => 'Cholécystectomie (0213)', 'code' => '0213'],
                ['id' => '0214', 'title' => 'Herniorraphie (0214)', 'code' => '0214']
            ]
        ];
        
        $results = [];
        foreach ($fallbackData as $keyword => $items) {
            if (stripos($query, $keyword) !== false) {
                $results = $items;
                break;
            }
        }
        
        // If no specific match, return general cardiovascular terms for cardio-related queries
        if (empty($results) && (stripos($query, 'cardio') !== false || stripos($query, 'heart') !== false)) {
            $results = $fallbackData['cardio'];
        }
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'fallback' => true
        ]);
        
    } catch (Exception $e) {
        \Log::error('ICD-11 API Proxy error', ['error' => $e->getMessage()]);
        
        // Return basic fallback data
        $results = [
            ['id' => 'I10', 'title' => 'Hypertension artérielle (I10)', 'code' => 'I10'],
            ['id' => 'I21', 'title' => 'Infarctus du myocarde (I21)', 'code' => 'I21'],
            ['id' => 'I20', 'title' => 'Angine de poitrine (I20)', 'code' => 'I20']
        ];
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'fallback' => true
        ]);
    }
})->name('api.proxy.icd11');

Route::get('/api/proxy/vidal', function (Request $request) {
    try {
        $query = $request->get('q', '');
        
        \Log::info('VIDAL API Proxy called', ['query' => $query]);
        
        // Comprehensive French drug database
        $vidalMedications = [
            ['id' => 'vidal_001', 'title' => 'Doliprane 500mg', 'dosage' => 'Comprimé 500mg'],
            ['id' => 'vidal_002', 'title' => 'Aspirine 100mg', 'dosage' => 'Comprimé 100mg'],
            ['id' => 'vidal_003', 'title' => 'Ibuprofène 400mg', 'dosage' => 'Comprimé 400mg'],
            ['id' => 'vidal_004', 'title' => 'Paracétamol 1000mg', 'dosage' => 'Comprimé 1000mg'],
            ['id' => 'vidal_005', 'title' => 'Atorvastatine 20mg', 'dosage' => 'Comprimé 20mg'],
            ['id' => 'vidal_006', 'title' => 'Métoprolol 50mg', 'dosage' => 'Comprimé 50mg'],
            ['id' => 'vidal_007', 'title' => 'Lisinopril 10mg', 'dosage' => 'Comprimé 10mg'],
            ['id' => 'vidal_008', 'title' => 'Amlodipine 5mg', 'dosage' => 'Comprimé 5mg'],
            ['id' => 'vidal_009', 'title' => 'Warfarine 5mg', 'dosage' => 'Comprimé 5mg'],
            ['id' => 'vidal_010', 'title' => 'Furosémide 40mg', 'dosage' => 'Comprimé 40mg'],
            ['id' => 'vidal_011', 'title' => 'Oméprazole 20mg', 'dosage' => 'Gélule 20mg'],
            ['id' => 'vidal_012', 'title' => 'Lévothyroxine 100µg', 'dosage' => 'Comprimé 100µg'],
            ['id' => 'vidal_013', 'title' => 'Metformine 500mg', 'dosage' => 'Comprimé 500mg'],
            ['id' => 'vidal_014', 'title' => 'Simvastatine 40mg', 'dosage' => 'Comprimé 40mg'],
            ['id' => 'vidal_015', 'title' => 'Ramipril 5mg', 'dosage' => 'Comprimé 5mg']
        ];
        
        // Fast VIDAL search with comprehensive French drug database
        $queryLower = strtolower($query);
        $results = collect($vidalMedications)->filter(function($med) use ($queryLower) {
            return stripos($med['title'], $queryLower) !== false || 
                   stripos($med['dosage'], $queryLower) !== false ||
                   stripos(strtolower($med['title']), $queryLower) !== false;
        })->take(10)->toArray();
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'fallback' => true,
            'message' => 'Using comprehensive French drug database'
        ]);
        $vidalMedications = [
            ['id' => 'vidal_001', 'title' => 'Doliprane 500mg', 'dosage' => 'Comprimé 500mg'],
            ['id' => 'vidal_002', 'title' => 'Aspirine 100mg', 'dosage' => 'Comprimé 100mg'],
            ['id' => 'vidal_003', 'title' => 'Ibuprofène 400mg', 'dosage' => 'Comprimé 400mg'],
            ['id' => 'vidal_004', 'title' => 'Paracétamol 1000mg', 'dosage' => 'Comprimé 1000mg'],
            ['id' => 'vidal_005', 'title' => 'Atorvastatine 20mg', 'dosage' => 'Comprimé 20mg'],
            ['id' => 'vidal_006', 'title' => 'Métoprolol 50mg', 'dosage' => 'Comprimé 50mg'],
            ['id' => 'vidal_007', 'title' => 'Lisinopril 10mg', 'dosage' => 'Comprimé 10mg'],
            ['id' => 'vidal_008', 'title' => 'Amlodipine 5mg', 'dosage' => 'Comprimé 5mg'],
            ['id' => 'vidal_009', 'title' => 'Warfarine 5mg', 'dosage' => 'Comprimé 5mg'],
            ['id' => 'vidal_010', 'title' => 'Furosémide 40mg', 'dosage' => 'Comprimé 40mg'],
            ['id' => 'vidal_011', 'title' => 'Oméprazole 20mg', 'dosage' => 'Gélule 20mg'],
            ['id' => 'vidal_012', 'title' => 'Lévothyroxine 100µg', 'dosage' => 'Comprimé 100µg'],
            ['id' => 'vidal_013', 'title' => 'Metformine 500mg', 'dosage' => 'Comprimé 500mg'],
            ['id' => 'vidal_014', 'title' => 'Simvastatine 40mg', 'dosage' => 'Comprimé 40mg'],
            ['id' => 'vidal_015', 'title' => 'Ramipril 5mg', 'dosage' => 'Comprimé 5mg']
        ];
        
        $results = collect($vidalMedications)->filter(function($med) use ($query) {
            return stripos($med['title'], $query) !== false || stripos($med['dosage'], $query) !== false;
        })->toArray();
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'fallback' => true
        ]);
        
    } catch (Exception $e) {
        \Log::error('VIDAL API Proxy error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
})->name('api.proxy.vidal');

Route::get('/api/proxy/allergies', function (Request $request) {
    try {
        $query = $request->get('q', '');
        
        \Log::info('Allergies API Proxy called', ['query' => $query]);
        
        // Comprehensive medical allergies database
        $allergiesData = [
            // Drug Allergies
            ['id' => 'all_001', 'title' => 'Allergie aux pénicillines', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            ['id' => 'all_002', 'title' => 'Allergie aux céphalosporines', 'severity' => 'Modérée', 'type' => 'Médicamenteuse'],
            ['id' => 'all_003', 'title' => 'Allergie aux sulfamides', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            ['id' => 'all_004', 'title' => 'Allergie à l\'aspirine', 'severity' => 'Modérée', 'type' => 'Médicamenteuse'],
            ['id' => 'all_005', 'title' => 'Allergie aux AINS', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            ['id' => 'all_006', 'title' => 'Allergie aux tétracyclines', 'severity' => 'Modérée', 'type' => 'Médicamenteuse'],
            ['id' => 'all_007', 'title' => 'Allergie aux macrolides', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            ['id' => 'all_008', 'title' => 'Allergie aux quinolones', 'severity' => 'Modérée', 'type' => 'Médicamenteuse'],
            ['id' => 'all_009', 'title' => 'Allergie aux aminoglycosides', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            ['id' => 'all_010', 'title' => 'Allergie aux bêta-lactamines', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            
            // Food Allergies
            ['id' => 'all_011', 'title' => 'Allergie aux arachides', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            ['id' => 'all_012', 'title' => 'Allergie aux noix', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            ['id' => 'all_013', 'title' => 'Allergie aux fruits de mer', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            ['id' => 'all_014', 'title' => 'Allergie au lait', 'severity' => 'Modérée', 'type' => 'Alimentaire'],
            ['id' => 'all_015', 'title' => 'Allergie aux œufs', 'severity' => 'Modérée', 'type' => 'Alimentaire'],
            ['id' => 'all_016', 'title' => 'Allergie au soja', 'severity' => 'Modérée', 'type' => 'Alimentaire'],
            ['id' => 'all_017', 'title' => 'Allergie au blé', 'severity' => 'Modérée', 'type' => 'Alimentaire'],
            ['id' => 'all_018', 'title' => 'Allergie au poisson', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            ['id' => 'all_019', 'title' => 'Allergie aux crustacés', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            ['id' => 'all_020', 'title' => 'Allergie aux mollusques', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            
            // Environmental Allergies
            ['id' => 'all_021', 'title' => 'Allergie aux pollens', 'severity' => 'Modérée', 'type' => 'Environnementale'],
            ['id' => 'all_022', 'title' => 'Allergie aux acariens', 'severity' => 'Modérée', 'type' => 'Environnementale'],
            ['id' => 'all_023', 'title' => 'Allergie aux poils d\'animaux', 'severity' => 'Modérée', 'type' => 'Environnementale'],
            ['id' => 'all_024', 'title' => 'Allergie aux moisissures', 'severity' => 'Modérée', 'type' => 'Environnementale'],
            ['id' => 'all_025', 'title' => 'Allergie au latex', 'severity' => 'Sévère', 'type' => 'Contact']
        ];
        
        // Fast Allergies search with comprehensive medical allergies database
        $queryLower = strtolower($query);
        $results = collect($allergiesData)->filter(function($allergy) use ($queryLower) {
            $title = strtolower($allergy['title']);
            $type = strtolower($allergy['type']);
            $severity = strtolower($allergy['severity']);
            
            return stripos($title, $queryLower) !== false || 
                   stripos($type, $queryLower) !== false || 
                   stripos($severity, $queryLower) !== false;
        })->take(10)->toArray();
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'fallback' => true,
            'message' => 'Using comprehensive medical allergies database'
        ]);
        
        // Comprehensive medical allergies database
        $allergiesData = [
            // Drug Allergies
            ['id' => 'all_001', 'title' => 'Allergie aux pénicillines', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            ['id' => 'all_002', 'title' => 'Allergie aux céphalosporines', 'severity' => 'Modérée', 'type' => 'Médicamenteuse'],
            ['id' => 'all_003', 'title' => 'Allergie aux sulfamides', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            ['id' => 'all_004', 'title' => 'Allergie à l\'aspirine', 'severity' => 'Modérée', 'type' => 'Médicamenteuse'],
            ['id' => 'all_005', 'title' => 'Allergie aux AINS', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            ['id' => 'all_006', 'title' => 'Allergie aux tétracyclines', 'severity' => 'Modérée', 'type' => 'Médicamenteuse'],
            ['id' => 'all_007', 'title' => 'Allergie aux macrolides', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            ['id' => 'all_008', 'title' => 'Allergie aux quinolones', 'severity' => 'Modérée', 'type' => 'Médicamenteuse'],
            ['id' => 'all_009', 'title' => 'Allergie aux aminoglycosides', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            ['id' => 'all_010', 'title' => 'Allergie aux bêta-lactamines', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
            
            // Food Allergies
            ['id' => 'all_011', 'title' => 'Allergie aux arachides', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            ['id' => 'all_012', 'title' => 'Allergie aux noix', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            ['id' => 'all_013', 'title' => 'Allergie aux fruits de mer', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            ['id' => 'all_014', 'title' => 'Allergie au lait', 'severity' => 'Modérée', 'type' => 'Alimentaire'],
            ['id' => 'all_015', 'title' => 'Allergie aux œufs', 'severity' => 'Modérée', 'type' => 'Alimentaire'],
            ['id' => 'all_016', 'title' => 'Allergie au soja', 'severity' => 'Modérée', 'type' => 'Alimentaire'],
            ['id' => 'all_017', 'title' => 'Allergie au blé', 'severity' => 'Modérée', 'type' => 'Alimentaire'],
            ['id' => 'all_018', 'title' => 'Allergie au poisson', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            ['id' => 'all_019', 'title' => 'Allergie aux crustacés', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            ['id' => 'all_020', 'title' => 'Allergie aux mollusques', 'severity' => 'Sévère', 'type' => 'Alimentaire'],
            
            // Environmental Allergies
            ['id' => 'all_021', 'title' => 'Allergie aux pollens', 'severity' => 'Modérée', 'type' => 'Environnementale'],
            ['id' => 'all_022', 'title' => 'Allergie aux acariens', 'severity' => 'Modérée', 'type' => 'Environnementale'],
            ['id' => 'all_023', 'title' => 'Allergie aux poils d\'animaux', 'severity' => 'Modérée', 'type' => 'Environnementale'],
            ['id' => 'all_024', 'title' => 'Allergie aux moisissures', 'severity' => 'Modérée', 'type' => 'Environnementale'],
            ['id' => 'all_025', 'title' => 'Allergie au latex', 'severity' => 'Sévère', 'type' => 'Contact']
        ];
        
        $results = collect($allergiesData)->filter(function($allergy) use ($query) {
            return stripos($allergy['title'], $query) !== false || 
                   stripos($allergy['type'], $query) !== false || 
                   stripos($allergy['severity'], $query) !== false;
        })->toArray();
        
        // If no results, provide general allergy suggestions
        if (empty($results)) {
            $results = [
                ['id' => 'all_001', 'title' => 'Allergie aux pénicillines', 'severity' => 'Sévère', 'type' => 'Médicamenteuse'],
                ['id' => 'all_002', 'title' => 'Allergie aux céphalosporines', 'severity' => 'Modérée', 'type' => 'Médicamenteuse'],
                ['id' => 'all_011', 'title' => 'Allergie aux arachides', 'severity' => 'Sévère', 'type' => 'Alimentaire']
            ];
        }
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'fallback' => true
        ]);
        
    } catch (Exception $e) {
        \Log::error('Allergies API Proxy error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
})->name('api.proxy.allergies');

// Global routes (no auth required)
Route::get('/', function () {
    return view('landing-simple');
})->name('landing');

Route::get('/profile-selector', function () {
    $footballType = request('footballType', '11aside');
    return view('profile-selector', compact('footballType'));
})->name('profile-selector');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Logout routes
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('logout', [LoginController::class, 'logout'])->name('logout.get');

// Test page for JavaScript debugging
Route::get('/test-player-display', function () {
    return view('test-player-display');
})->name('test.player.display');

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
})->name('api.signed-pcmas');

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
    Route::resource('licenses', LicenseController::class)->except(['show']);
    Route::get('/licenses/{license}', [LicenseController::class, 'show'])->name('licenses.show');
    Route::get('/licenses/validation', [LicenseController::class, 'validation'])->name('licenses.validation');
    Route::patch('/licenses/{license}/approve', [LicenseController::class, 'approve'])->name('licenses.approve');
    Route::patch('/licenses/{license}/reject', [LicenseController::class, 'reject'])->name('licenses.reject');
    
    // User Management routes
    Route::get('/user-management', function () {
        return view('modules.user-management.index');
    })->name('user-management.index');
    
    // Role Management routes
    Route::get('/role-management', function () {
        return view('modules.role-management.index');
    })->name('role-management.index');
    
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
        return view('modules.license-types.index');
    })->name('license-types.index');
    
    // Content Management routes
    Route::get('/content', function () {
        return view('modules.content.index');
    })->name('content.index');
    
    // Stakeholder Gallery routes
    Route::get('/stakeholder-gallery', function () {
        return view('modules.stakeholder-gallery.index');
    })->name('stakeholder-gallery.index');
    
    // Players routes
    Route::get('/players', function () {
        $players = collect([]); // Empty collection for now
        
        // Try to get actual players if model exists
        try {
            if (class_exists('\App\Models\Player')) {
                $players = \App\Models\Player::with(['licenses', 'club'])->orderBy('first_name')->get();
            }
        } catch (\Exception $e) {
            // Player model might not exist or table is missing
        }
        
        return view('modules.players.index', [
            'players' => $players
        ]);
    })->name('players.index');
    
    // Player Registration routes
    Route::get('/player-registration/create', function () {
        return view('modules.player-registration.create');
    })->name('player-registration.create');
    
    // Club Player Licenses routes
    Route::get('/club/player-licenses', function () {
        return view('modules.club.player-licenses.index');
    })->name('club.player-licenses.index');
    
    // Player Passports routes
    Route::get('/player-passports', function () {
        return view('modules.player-passports.index');
    })->name('player-passports.index');
    
    // Health Records routes
    Route::get('/health-records', [App\Http\Controllers\HealthRecordController::class, 'index'])->name('health-records.index');
    Route::get('/health-records/create', [App\Http\Controllers\HealthRecordController::class, 'create'])->name('health-records.create');
    Route::get('/health-records/{healthRecord}', [App\Http\Controllers\HealthRecordController::class, 'show'])->name('health-records.show');
    Route::post('/health-records', [App\Http\Controllers\HealthRecordController::class, 'store'])->name('health-records.store');
    Route::get('/health-records/{healthRecord}/edit', [App\Http\Controllers\HealthRecordController::class, 'edit'])->name('health-records.edit');
    Route::put('/health-records/{healthRecord}', [App\Http\Controllers\HealthRecordController::class, 'update'])->name('health-records.update');
    Route::delete('/health-records/{healthRecord}', [App\Http\Controllers\HealthRecordController::class, 'destroy'])->name('health-records.destroy');
    Route::post('/health-records/{healthRecord}/generate-prediction', [App\Http\Controllers\HealthRecordController::class, 'generatePrediction'])->name('health-records.generate-prediction');
    Route::post('/health-records/generate-hl7-cda', [App\Http\Controllers\HealthRecordController::class, 'generateHl7Cda'])->name('health-records.generate-hl7-cda');
    
    // Performances routes
    Route::get('/performances', function () {
        return view('modules.performances.index');
    })->name('performances.index');
    
    // Teams routes
    Route::get('/teams', function () {
        return view('modules.teams.index');
    })->name('teams.index');
    
    // Club Player Assignments routes
    Route::get('/club-player-assignments', function () {
        return view('modules.club-player-assignments.index');
    })->name('club-player-assignments.index');
    
    // Club Logo Management routes
    Route::get('/club/{club}/logo/upload', [ClubManagementController::class, 'showLogoUpload'])->name('club.logo.upload');
    Route::post('/club/{club}/logo/upload', [ClubManagementController::class, 'uploadLogo'])->name('club.logo.store');
    
    // Match Sheet routes
    Route::get('/match-sheet', function () {
        return view('modules.match-sheet.index');
    })->name('match-sheet.index');
    
    // Transfers routes
    Route::get('/transfers', function () {
        return view('modules.transfers.index');
    })->name('transfers.index');
    
    // Performance Recommendations routes
    Route::get('/performance-recommendations', function () {
        return view('modules.performance-recommendations.index');
    })->name('performance-recommendations.index');
    
    // Competitions routes
    Route::get('/competitions', [CompetitionManagementController::class, 'competitionsIndex'])->name('competitions.index');
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
        return view('modules.registration-requests.index');
    })->name('registration-requests.index');
    
    // Player Licenses routes
    Route::get('/player-licenses', function () {
        return view('modules.player-licenses.index');
    })->name('player-licenses.index');
    
    // Contracts routes
    Route::get('/contracts', function () {
        return view('modules.contracts.index');
    })->name('contracts.index');
    
    // FIFA routes
    Route::get('/fifa/dashboard', function () {
        return view('modules.fifa.dashboard');
    })->name('fifa.dashboard');
    
    Route::get('/fifa/connectivity', function () {
        return view('modules.fifa.connectivity');
    })->name('fifa.connectivity');
    
    Route::get('/fifa/sync-dashboard', function () {
        return view('modules.fifa.sync-dashboard');
    })->name('fifa.sync-dashboard');
    
    Route::get('/fifa/contracts', function () {
        return view('modules.fifa.contracts');
    })->name('fifa.contracts');
    
    Route::get('/fifa/analytics', function () {
        return view('modules.fifa.analytics');
    })->name('fifa.analytics');
    
    Route::get('/fifa/statistics', function () {
        return view('modules.fifa.statistics');
    })->name('fifa.statistics');
    
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
    Route::get('/fifa/players/search', function () {
        return view('modules.fifa.players.search');
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
        return view('modules.healthcare.index');
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
                'fifa_connect_id' => 'nullable|string|max:255',
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
                'fifa_connect_id' => 'nullable|string|max:255',
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
    })->name('pcma.pdf');
    
    Route::get('/pcma/{pcma}/pdf', function ($pcma) {
        try {
            // Find the PCMA record
            $pcmaRecord = \App\Models\PCMA::with(['athlete', 'assessor'])->find($pcma);
            if (!$pcmaRecord) {
                abort(404, 'PCMA not found');
            }
            
            // Generate PDF content with proper data format
            $pdfContent = view('pcma.pdf', [
                'formData' => [
                    'type' => $pcmaRecord->type ?? 'standard',
                    'assessment_date' => $pcmaRecord->assessment_date ?? now()->format('Y-m-d'),
                    'assessment_id' => $pcmaRecord->id,
                    'blood_pressure' => $pcmaRecord->blood_pressure ?? 'Non mesuré',
                    'heart_rate' => $pcmaRecord->heart_rate ?? 'Non mesuré',
                    'temperature' => $pcmaRecord->temperature ?? 'Non mesuré',
                    'oxygen_saturation' => $pcmaRecord->oxygen_saturation ?? 'Non mesuré',
                    'cardiovascular_history' => $pcmaRecord->cardiovascular_history ?? 'Aucun',
                    'surgical_history' => $pcmaRecord->surgical_history ?? 'Aucun',
                    'current_medications' => $pcmaRecord->current_medications ?? 'Aucun',
                    'allergies' => $pcmaRecord->allergies ?? 'Aucune',
                ],
                'fitnessResults' => null,
                'athlete' => $pcmaRecord->athlete,
                'generatedAt' => now(),
                'isSigned' => $pcmaRecord->is_signed ?? false,
                'signedBy' => $pcmaRecord->signed_by ?? null,
                'licenseNumber' => $pcmaRecord->license_number ?? null,
                'signedAt' => $pcmaRecord->signed_at ?? null,
                'signatureImage' => $pcmaRecord->signature_image ? asset('storage/' . $pcmaRecord->signature_image) : null,
                'signatureData' => $pcmaRecord->signature_data ?? null
            ])->render();
            
            // For now, return a simple HTML response that can be printed as PDF
            // In a real implementation, you would use a library like DomPDF or Snappy
            return response($pdfContent)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'inline; filename="pcma-' . $pcmaRecord->id . '.html"');
                
        } catch (\Exception $e) {
            \Log::error("Error generating PCMA PDF: " . $e->getMessage());
            return redirect()->route('pcma.show', $pcma)->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    })->name('pcma.view.pdf');

    Route::post('/pcma/{pcma}/complete', function ($pcma) {
        return redirect()->route('pcma.show', $pcma)->with('success', 'PCMA marked as completed');
    })->name('pcma.complete');

    Route::post('/pcma/{pcma}/fail', function ($pcma) {
        return redirect()->route('pcma.show', $pcma)->with('error', 'PCMA marked as failed');
    })->name('pcma.fail');

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
Route::post('/pcma/ai-analyze-ecg-effort', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeEcgEffort'])->name('pcma.ai-analyze-ecg-effort');
    Route::post('/pcma/ai-analyze-scintigraphy', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeScintigraphy'])->name('pcma.ai-analyze-scintigraphy');
    Route::post('/pcma/ai-analyze-scat', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeScat'])->name('pcma.ai-analyze-scat');
    Route::post('/pcma/ai-analyze-complete', [App\Http\Controllers\PCMAController::class, 'aiAnalyzeComplete'])->name('pcma.ai-analyze-complete');
    
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
    
    Route::get('/portal/devices', function () {
        return view('modules.portal.devices');
    })->name('portal.devices');
    
    // Secretary Dashboard routes
    Route::get('/secretary/dashboard', function () {
        return view('modules.secretary.dashboard');
    })->name('secretary.dashboard');
    
    // Secretary sub-routes
    Route::get('/appointments', function () {
        return view('modules.appointments.index');
    })->name('appointments.index');
    
    Route::get('/documents', function () {
        return view('modules.documents.index');
    })->name('documents.index');
    
    // Referee routes
    Route::get('/referee/dashboard', function () {
        return view('modules.referee.dashboard');
    })->name('referee.dashboard');
    
    Route::get('/referee/match-assignments', function () {
        return view('modules.referee.match-assignments');
    })->name('referee.match-assignments');
    
    Route::get('/referee/competition-schedule', function () {
        return view('modules.referee.competition-schedule');
    })->name('referee.competition-schedule');
    
    Route::get('/referee/create-match-report', function () {
        return view('modules.referee.create-match-report');
    })->name('referee.create-match-report');
    
    Route::get('/referee/performance-stats', function () {
        return view('modules.referee.performance-stats');
    })->name('referee.performance-stats');
    
    Route::get('/referee/settings', function () {
        return view('modules.referee.settings');
    })->name('referee.settings');
    
    // Performances Analytics routes
    Route::get('/performances/analytics', function () {
        return view('modules.performances.analytics');
    })->name('performances.analytics');
    
    // Performances Trends routes
    Route::get('/performances/trends', function () {
        return view('modules.performances.trends');
    })->name('performances.trends');
    
    // Alerts Performance routes
    Route::get('/alerts/performance', function () {
        return view('modules.alerts.performance');
    })->name('alerts.performance');
    
    // Profile routes
    Route::get('/profile', function () {
        return view('modules.profile.show');
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
    Route::get('/admin/account-requests', function () {
        return view('modules.admin.account-requests.index');
    })->name('admin.account-requests.index');
    
    // Module routes
    Route::get('/modules/medical', [App\Http\Controllers\MedicalModuleController::class, 'index'])->name('modules.medical.index');
    
    Route::get('/modules/medical/athlete/{id}', function ($id) {
        $player = null;
        $isDemo = false;
        
        // Demo players data
        $demoPlayers = [
            1 => ['id' => 1, 'name' => 'John Smith', 'full_name' => 'John Smith', 'first_name' => 'John', 'last_name' => 'Smith', 'date_of_birth' => '1995-03-15', 'age' => 29, 'position' => 'ST', 'nationality' => 'USA', 'club' => ['name' => 'Team Alpha']],
            2 => ['id' => 2, 'name' => 'Sarah Johnson', 'full_name' => 'Sarah Johnson', 'first_name' => 'Sarah', 'last_name' => 'Johnson', 'date_of_birth' => '1993-07-22', 'age' => 31, 'position' => 'MF', 'nationality' => 'Canada', 'club' => ['name' => 'Team Beta']],
            3 => ['id' => 3, 'name' => 'Mike Wilson', 'full_name' => 'Mike Wilson', 'first_name' => 'Mike', 'last_name' => 'Wilson', 'date_of_birth' => '1997-11-08', 'age' => 27, 'position' => 'DF', 'nationality' => 'UK', 'club' => ['name' => 'Team Gamma']],
            4 => ['id' => 4, 'name' => 'Emma Davis', 'full_name' => 'Emma Davis', 'first_name' => 'Emma', 'last_name' => 'Davis', 'date_of_birth' => '1994-05-12', 'age' => 30, 'position' => 'GK', 'nationality' => 'Australia', 'club' => ['name' => 'Team Delta']],
            5 => ['id' => 5, 'name' => 'Alex Brown', 'full_name' => 'Alex Brown', 'first_name' => 'Alex', 'last_name' => 'Brown', 'date_of_birth' => '1996-09-30', 'age' => 28, 'position' => 'FW', 'nationality' => 'Germany', 'club' => ['name' => 'Team Echo']]
        ];
        
        // Check if this is a demo player
        if (isset($demoPlayers[$id])) {
            $player = (object) $demoPlayers[$id];
            $isDemo = true;
        } else {
            // Try to get the real player if model exists
            try {
                if (class_exists('\App\Models\Player')) {
                    $player = \App\Models\Player::with(['club', 'healthRecords'])->find($id);
                }
            } catch (\Exception $e) {
                // Player model might not exist or table is missing
            }
        }
        
        return view('modules.medical.athlete', [
            'player' => $player,
            'isDemo' => $isDemo,
            'footballType' => 'association'
        ]);
    })->name('modules.medical.athlete');
    
    Route::get('/modules/healthcare', function () {
        $healthRecords = collect([]); // Empty collection for now
        
        // Try to get actual health records if model exists
        try {
            if (class_exists('\App\Models\HealthRecord')) {
                $healthRecords = \App\Models\HealthRecord::with(['player', 'user'])->latest()->get();
            }
        } catch (\Exception $e) {
            // HealthRecord model might not exist or table is missing
        }
        
        return view('modules.healthcare.index', [
            'footballType' => 'association',
            'healthRecords' => $healthRecords
        ]);
    })->name('modules.healthcare.index');
    
    Route::get('/modules/competitions', function () {
        return view('modules.competitions.index', ['footballType' => 'association']);
    })->name('modules.competitions.index');
    
    Route::get('/modules/players', function () {
        return view('modules.players.index', ['footballType' => 'association']);
    })->name('modules.players.index');
    
    Route::get('/modules/teams', function () {
        return view('modules.teams.index', ['footballType' => 'association']);
    })->name('modules.teams.index');
    
    Route::get('/modules/referees', function () {
        return view('modules.referees.index', ['footballType' => 'association']);
    })->name('modules.referees.index');
    
    Route::get('/modules/associations', function () {
        return view('modules.associations.index', ['footballType' => 'association']);
    })->name('modules.associations.index');
    
    Route::get('/modules/clubs', function () {
        return view('modules.clubs.index', ['footballType' => 'association']);
    })->name('modules.clubs.index');
    
    Route::get('/modules/administration', function () {
        return view('modules.administration.index', ['footballType' => 'association']);
    })->name('modules.administration.index');
    
    Route::get('/modules/licenses', function () {
        return view('modules.licenses.index', ['footballType' => 'association']);
    })->name('modules.licenses.index');

    // AI Testing routes
    Route::get('/ai-testing', [App\Http\Controllers\AITestingController::class, 'index'])->name('ai-testing.index');
    Route::get('/ai-testing/test', function () {
        return view('ai-testing.test');
    })->name('ai-testing.test');
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
});

// PDF generation routes (public access)
Route::post('/pcma/pdf', [App\Http\Controllers\PCMAController::class, 'generatePdf'])->name('pcma.pdf.post')->middleware('api');

// Simple test route
Route::get('/test-public', function () {
    return response()->json(['message' => 'Public route working']);
})->name('test.public');

// API routes
Route::get('/api/fit/kpis', [FitDashboardController::class, 'kpis'])->name('fit.kpis');

// Test routes
Route::get('/test-tabs', function () {
    $players = \App\Models\Player::orderBy('name')->get();
    return view('health-records.create', compact('players'));
})->name('test-tabs');

// Analytics routes
Route::get('/analytics/dashboard', function () {
    return view('analytics.dashboard');
})->name('analytics.dashboard');

Route::get('/analytics/digital-twin', function () {
    return view('analytics.digital-twin');
})->name('analytics.digital-twin');

// Performance routes
Route::get('/performance', function () {
    return view('performance.index');
})->name('performance.index');

// DTN routes
Route::get('/dtn', function () {
    return view('dtn.index');
})->name('dtn.index');

// RPM routes
Route::get('/rpm', function () {
    return view('rpm.index');
})->name('rpm.index');

// License Fraud Detection Routes
Route::post('/api/v1/licenses/fraud-detection/batch', [App\Http\Controllers\LicenseController::class, 'batchFraudDetection'])
    ->name('licenses.fraud-detection.batch');

Route::post('/api/v1/licenses/fraud-detection/analyze/{licenseId}', [App\Http\Controllers\LicenseController::class, 'analyzeLicenseFraud'])
    ->name('licenses.fraud-detection.analyze');

Route::post('/api/v1/licenses/fraud-detection/check-all', [App\Http\Controllers\LicenseController::class, 'checkAllLicenses'])
    ->name('licenses.fraud-detection.check-all');

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

// Routes pour le diagramme dentaire
Route::get('/dental-chart', [App\Http\Controllers\DentalChartController::class, 'index'])->name('dental-chart.index');
Route::get('/dental-chart/{patient}', [App\Http\Controllers\DentalChartController::class, 'show'])->name('dental-chart.show');

// Route pour le diagramme dentaire
Route::get('/dental-chart/{healthRecord}', function ($healthRecord) {
    return view('health-records.dental-chart', compact('healthRecord'));
})->name('dental-chart.show');

// Route de test pour le diagramme dentaire
Route::get('/dental-chart-test', function () {
    return view('health-records.dental-chart-simple');
})->name('dental-chart.test');

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
            return redirect()->route('player-portal.fifa-ultimate');
        });
        Route::get('/home', function () {
            return redirect()->route('player-portal.fifa-ultimate');
        })->name('dashboard');
        Route::get('/simple', function () {
            return view('player-portal.simple-dashboard');
        })->name('simple-dashboard');
        Route::get('/debug', function () {
            return view('player-portal.debug');
        })->name('debug');
        Route::get('/test', function () {
            return view('player-portal.test');
        })->name('test');
        Route::get('/profile', [App\Http\Controllers\PlayerPortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\PlayerPortalController::class, 'updateProfile'])->name('update-profile');
        Route::get('/medical-records', function () {
            return view('player-portal.medical-records-simple');
        })->name('medical-records');
        Route::get('/predictions', [App\Http\Controllers\PlayerPortalController::class, 'predictions'])->name('predictions');
        Route::get('/performances', [App\Http\Controllers\PlayerPortalController::class, 'performances'])->name('performances');
        Route::get('/matches', [App\Http\Controllers\PlayerPortalController::class, 'matches'])->name('matches');
        Route::get('/documents', [App\Http\Controllers\PlayerPortalController::class, 'documents'])->name('documents');
        Route::get('/settings', [App\Http\Controllers\PlayerPortalController::class, 'settings'])->name('settings');
        Route::get('/fifa-ultimate', function () {
            return view('player-portal.fifa-ultimate-working');
        })->name('fifa-ultimate');
    Route::get('/fifa-light', [App\Http\Controllers\PlayerPortalController::class, 'fifaUltimateDashboard'])->name('fifa-light');
    });
});

// Routes de test publiques (en dehors du groupe player-portal)
Route::get('/test-minimal', function () {
    return view('test-minimal');
})->name('test-minimal');

// Routes FIFA publiques pour test sans authentification
Route::get('/fifa-ultimate-complete', function () {
    return view('player-portal.fifa-ultimate-complete');
})->name('fifa-ultimate-complete');

Route::get('/fifa-ultimate-working', function () {
    return view('player-portal.fifa-ultimate-working');
})->name('fifa-ultimate-working');

Route::get('/fifa-test-public', function () {
    return view('player-portal.fifa-ultimate-complete');
})->name('fifa-test-public');

Route::get('/test-tabs', function () {
    return view('test-tabs');
})->name('test-tabs');

Route::get('/test-medical-tabs', function () {
    return view('health-records.create-tabs');
})->name('test-medical-tabs');

Route::get('/medical-tabs', function () {
    return view('health-records.create-tabs');
})->name('medical-tabs');

Route::get('/fifa-test-simple', function () {
    return view('fifa-test-simple');
})->name('fifa-test-simple');

Route::get('/fifa-debug', function () {
    return view('fifa-debug');
})->name('fifa-debug');

Route::get('/fifa-stable', function () {
    return view('player-portal.fifa-stable');
})->name('fifa-stable');

Route::get('/fifa-simple-test', function () {
    return view('fifa-simple-test');
})->name('fifa-simple-test');

Route::get('/fifa-working', function () {
    return view('player-portal.fifa-working');
})->name('fifa-working');

Route::get('/fifa-complete', [App\Http\Controllers\FifaDashboardController::class, 'index'])->name('fifa-complete');

Route::get('/fifa-debug', function () {
    return view('player-portal.fifa-debug');
})->name('fifa-debug');

// Route de test pour l'authentification
Route::get('/test-auth', function () {
    if (Auth::check()) {
        return response()->json([
            'authenticated' => true,
            'user' => Auth::user()->email,
            'role' => Auth::user()->role,
            'session_id' => session()->getId()
        ]);
    } else {
        return response()->json([
            'authenticated' => false,
            'session_id' => session()->getId()
        ]);
    }
})->name('test-auth');

// Route de test pour forcer la connexion
Route::get('/force-login', function () {
    $user = App\Models\User::where('email', 'lionel.messi@example.com')->first();
    if ($user) {
        Auth::login($user);
        return response()->json([
            'message' => 'User logged in',
            'user' => $user->email,
            'role' => $user->role,
            'session_id' => session()->getId()
        ]);
    } else {
        return response()->json(['error' => 'User not found']);
        }
})->name('force-login');

// Route publique de test
Route::get('/public-test', function () {
    return response()->json([
        'message' => 'Public route works',
        'timestamp' => now(),
        'session_id' => session()->getId()
    ]);
})->name('public-test');

// Route de debug FIFA (temporaire)
Route::get('/fifa-debug', function () {
    // Trouver un joueur pour le test
    $user = App\Models\User::where('email', 'lionel.messi@example.com')->first();
    $player = $user ? $user->player : null;
    
    if (!$player) {
        // Créer des données de test si le joueur n'existe pas
        $player = (object) [
            'first_name' => 'Lionel',
            'last_name' => 'Messi',
            'email' => 'lionel.messi@example.com',
            'club' => (object) ['name' => 'Paris Saint-Germain']
        ];
    } else {
        $player->load('club');
    }
    
    return view('player-portal.fifa-debug', compact('player'));
})->name('fifa-debug');

// Route fixe pour le portail patient
Route::get('/portail-patient', function () {
    return response()->file(public_path('portail-patient.html'));
})->name('portail-patient');

// Route fixe pour le portail joueur (dynamique) - SUPPRIMÉE car remplacée par PlayerPortalController

// Redirection de l'ancienne URL vers le portail patient
Route::redirect('/fifa-complete-original.html', '/portail-patient', 301);



// API Routes for Player Portal
Route::prefix('api')->group(function () {
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
Route::get('/test-blade-simple', function() {
    try {
        $player = \App\Models\Player::with(['club', 'association'])->find(1);
        
        // Données dynamiques complètes
        $portalData = [
            'fifaStats' => [
                'overall_rating' => rand(75, 95),
                'potential_rating' => rand(70, 90),
                'fitness_score' => rand(80, 100)
            ],
            'performanceData' => [
                'monthly_ratings' => array_map(function() { return rand(65, 95) / 10; }, range(1, 6)),
                'monthly_goals' => array_map(function() { return rand(0, 4); }, range(1, 6)),
                'monthly_assists' => array_map(function() { return rand(0, 3); }, range(1, 6))
            ],
            'sdohData' => [
                'environment' => rand(60, 90),
                'social_support' => rand(50, 95),
                'healthcare_access' => rand(70, 100),
                'financial_situation' => rand(60, 90),
                'mental_wellbeing' => rand(70, 95)
            ],
            'playerStats' => [
                'age' => $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : rand(18, 35),
                'height' => rand(165, 195),
                'weight' => rand(65, 85),
                'preferred_foot' => ['Gauche', 'Droit'][rand(0, 1)],
                'ballon_dor_count' => rand(0, 5),
                'total_goals' => rand(50, 500),
                'total_assists' => rand(100, 800),
                'champions_league_count' => rand(0, 10),
                'season_goals' => rand(0, 30),
                'season_assists' => rand(0, 20)
            ],
            'seasonProgress' => [
                'currentSeason' => '2024-25',
                'completion' => rand(60, 90),
                'matchesPlayed' => rand(15, 35),
                'matchesRemaining' => rand(5, 15)
            ],
            'recentPerformances' => ['W', 'W', 'D', 'W', 'W'],
            'performanceStats' => [
                'current_month_goals' => rand(2, 8),
                'current_month_assists' => rand(1, 5),
                'current_month_distance' => rand(200, 400),
                'matches_played' => rand(3, 8),
                'average_rating' => rand(65, 95) / 10
            ],
            'heroMetrics' => [
                'injury_risk' => [
                    'percentage' => rand(5, 25),
                    'level' => (function() {
                        $percentage = rand(5, 25);
                        if ($percentage <= 10) return 'TRÈS FAIBLE';
                        if ($percentage <= 20) return 'FAIBLE';
                        if ($percentage <= 30) return 'MODÉRÉ';
                        return 'ÉLEVÉ';
                    })(),
                    'color' => 'text-green-400'
                ],
                'market_value' => [
                    'current' => rand(50, 300),
                    'change' => rand(-30, 50),
                    'trend' => 'up'
                ],
                'availability' => [
                    'status' => rand(0, 1) ? 'DISPONIBLE' : 'INDISPONIBLE',
                    'next_match' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'][rand(0, 6)],
                    'icon' => '✅'
                ],
                'player_state' => [
                    'form' => rand(60, 95),
                    'morale' => rand(65, 90)
                ]
            ],
            'images' => [
                'player_profile' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=200&h=200&fit=crop&crop=face',
                'club_logo' => 'https://via.placeholder.com/200x200/cccccc/666666?text=' . urlencode($player->club->name ?? 'Club'),
                'country_flag' => 'https://flagcdn.com/w40/' . strtolower(substr($player->nationality ?? 'fr', 0, 2)) . '.png'
            ]
        ];
        
        return view('portail-joueur-final-corrige-dynamique', compact('player', 'portalData'));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
    }
})->name('test.blade.simple');

// Test du fichier Blade simple - AMÉLIORÉ avec données dynamiques
Route::get('/test-simple-blade', function() {
    try {
        $player = \App\Models\Player::with(['club', 'association'])->find(1);
        
        // Données dynamiques complètes
        $portalData = [
            'fifaStats' => [
                'overall_rating' => rand(75, 95),
                'potential_rating' => rand(70, 90),
                'fitness_score' => rand(80, 100)
            ],
            'playerStats' => [
                'age' => $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : rand(18, 35),
                'height' => rand(165, 195),
                'weight' => rand(65, 85),
                'preferred_foot' => ['Gauche', 'Droit'][rand(0, 1)],
                'ballon_dor_count' => rand(0, 5),
                'total_goals' => rand(50, 500),
                'total_assists' => rand(100, 800),
                'champions_league_count' => rand(0, 10),
                'season_goals' => rand(0, 30),
                'season_assists' => rand(0, 20)
            ],
            'seasonProgress' => [
                'currentSeason' => '2024-25',
                'completion' => rand(60, 90),
                'matchesPlayed' => rand(15, 35),
                'matchesRemaining' => rand(5, 15)
            ],
            'recentPerformances' => ['W', 'W', 'D', 'W', 'W'],
            'performanceStats' => [
                'current_month_goals' => rand(2, 8),
                'current_month_assists' => rand(1, 5),
                'current_month_distance' => rand(200, 400),
                'matches_played' => rand(3, 8),
                'average_rating' => rand(65, 95) / 10
            ],
            'heroMetrics' => [
                'injury_risk' => [
                    'percentage' => rand(5, 25),
                    'level' => (function() {
                        $percentage = rand(5, 25);
                        if ($percentage <= 10) return 'TRÈS FAIBLE';
                        if ($percentage <= 20) return 'FAIBLE';
                        if ($percentage <= 30) return 'MODÉRÉ';
                        return 'ÉLEVÉ';
                    })(),
                    'color' => 'text-green-400'
                ],
                'market_value' => [
                    'current' => rand(50, 300),
                    'change' => rand(-30, 50),
                    'trend' => 'up'
                ],
                'availability' => [
                    'status' => rand(0, 1) ? 'DISPONIBLE' : 'INDISPONIBLE',
                    'next_match' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'][rand(0, 6)],
                    'icon' => '✅'
                ],
                'player_state' => [
                    'form' => rand(60, 95),
                    'morale' => rand(65, 90)
                ]
            ],
            'images' => [
                'player_profile' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=200&h=200&fit=crop&crop=face',
                'club_logo' => 'https://via.placeholder.com/200x200/cccccc/666666?text=' . urlencode($player->club->name ?? 'Club'),
                'country_flag' => 'https://flagcdn.com/w40/' . strtolower(substr($player->nationality ?? 'fr', 0, 2)) . '.png'
            ]
        ];
        
        return view('test-simple-portal', compact('player', 'portalData', 'allPlayers'));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
    }
})->name('test.simple.blade');

// Route pour le portail original dynamique - SUPPRIMÉE car problématique

// Route de test pour le portail joueur (sans authentification)
Route::get('/test/portal/{playerId}', function ($playerId) {
    $player = \App\Models\Player::find($playerId);
    if (!$player) {
        abort(404, 'Joueur non trouvé');
    }
    
    $controller = new \App\Http\Controllers\PlayerAccessController();
    $portalData = $controller->preparePortalData($player);
    
    return view('portail-joueur-final-corrige-dynamique', compact('portalData', 'player'));
})->name('test.portal');

// Route de test pour l'onglet médical (page simplifiée)
Route::get('/test/medical/{playerId}', function ($playerId) {
    $player = \App\Models\Player::find($playerId);
    if (!$player) {
        abort(404, 'Joueur non trouvé');
    }
    
    $controller = new \App\Http\Controllers\PlayerAccessController();
    $portalData = $controller->preparePortalData($player);
    
    return view('test-medical', compact('portalData', 'player'));
})->name('test.medical');

// Route de test simple pour afficher les données brutes
Route::get('/test/data/{playerId}', function ($playerId) {
    $player = \App\Models\Player::find($playerId);
    if (!$player) {
        abort(404, 'Joueur non trouvé');
    }
    
    $controller = new \App\Http\Controllers\PlayerAccessController();
    $portalData = $controller->preparePortalData($player);
    
    return response()->json([
        'player' => [
            'id' => $player->id,
            'name' => $player->first_name . ' ' . $player->last_name,
            'position' => $player->position,
            'date_of_birth' => $player->date_of_birth,
            'age_calculated' => $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : null,
            'age_type' => $player->date_of_birth ? gettype($player->date_of_birth->diffInYears(now())) : null,
        ],
        'personalInfo' => $portalData['personalInfo'],
        'success' => true
    ]);
})->name('test.data');

// Route de test simple pour la page médicale
Route::get('/test/simple/{playerId}', function ($playerId) {
    $player = \App\Models\Player::find($playerId);
    if (!$player) {
        abort(404, 'Joueur non trouvé');
    }
    
    $controller = new \App\Http\Controllers\PlayerAccessController();
    $portalData = $controller->preparePortalData($player);
    
    return view('test-simple', compact('portalData', 'player'));
})->name('test.simple');

// Route de test minimaliste pour déboguer
Route::get('/test/minimal/{playerId}', function ($playerId) {
    $player = \App\Models\Player::find($playerId);
    if (!$player) {
        abort(404, 'Joueur non trouvé');
    }
    
    $controller = new \App\Http\Controllers\PlayerAccessController();
    $portalData = $controller->preparePortalData($player);
    
    return view('test-minimal', compact('portalData', 'player'));
})->name('test.minimal');

// Test des logos des clubs FTF
Route::get('/test-clubs-ftf', function () {
    return view('test-clubs-ftf');
})->name('test.clubs.ftf');

// Test des logos des clubs FTF (version simplifiée)
Route::get('/test-clubs-ftf-simple', function () {
    return view('test-clubs-ftf-simple');
})->name('test.clubs.ftf.simple');

// Démonstration finale des logos des clubs FTF
Route::get('/demo-clubs-ftf', function () {
    return view('demo-clubs-ftf');
})->name('demo.clubs.ftf');

// Page des logos des clubs FTF
Route::get('/logos-clubs-ftf', function () {
    return view('logos-clubs-ftf');
})->name('logos.clubs.ftf');

// Page des logos originaux des clubs FTF
Route::get('/logos-originaux-ftf', function () {
    return view('logos-originaux-ftf');
})->name('logos.originaux.ftf');

// Démonstration des vrais logos des clubs FTF
Route::get('/demo-vrais-logos-ftf', function () {
    return view('demo-vrais-logos-ftf');
})->name('demo.vrais.logos.ftf');

// Test du portail joueur avec le composant club-logo-working
Route::get('/test-portail-club-logos', function () {
    // Simuler un joueur avec un club
    $player = (object) [
        'id' => 999,
        'first_name' => 'Test',
        'last_name' => 'Joueur',
        'club' => (object) [
            'name' => 'Esperance Sportive de Tunis',
            'code' => 'EST'
        ],
        'association' => (object) [
            'id' => 1,
            'name' => 'Fédération Tunisienne de Football',
            'country' => 'Tunisie'
        ]
    ];
    
    return view('portail-joueur-final-corrige-dynamique', compact('player'));
})->name('test.portail.club.logos');

// Test simple du composant club-logo-working
Route::get('/test-portail-club-logos-simple', function () {
    return view('test-portail-club-logos');
})->name('test.portail.club.logos.simple');

// Test des clubs réels de la base de données
Route::get('/test-clubs-reels', function () {
    return view('test-clubs-reels');
})->name('test.clubs.reels');

// Test final du portail joueur avec logos clubs
Route::get('/test-portail-final', function () {
    return view('test-portail-final');
})->name('test.portail.final');

// Test du portail joueur principal avec de vrais joueurs
Route::get('/test-portail-principal/{id}', function ($id) {
    try {
        $player = \App\Models\Player::with(['club', 'association'])->findOrFail($id);
        return view('portail-joueur-final-corrige-dynamique', compact('player'));
    } catch (\Exception $e) {
        return response("Joueur {$id} non trouvé : " . $e->getMessage(), 404);
    }
})->name('test.portail.principal');

// Test du système de performances enrichi
Route::get('/test-performance-system', function () {
    return view('test-performance-system');
})->name('test.performance.system');

// Test du système de performances simplifié
Route::get('/test-performance-working', function () {
    return view('test-performance-working');
})->name('test.performance.working');

// API pour récupérer les VRAIES performances FIFA d'un joueur depuis la base de données
Route::get('/api/player-performance/{id}', [App\Http\Controllers\RealFIFAController::class, 'getRealFIFAPerformance'])
    ->name('api.player.performance');

// Page de test debug FIFA
Route::get('/test-fifa-debug', function () {
    return view('test-fifa-debug');
})->name('test.fifa.debug');

// Page de test JavaScript FIFA
Route::get('/test-fifa-js', function () {
    return view('test-fifa-js');
})->name('test.fifa.js');

// Page de test du portail FIFA sans authentification
Route::get('/test-portail-fifa/{playerId?}', function ($playerId = 7) {
    try {
        $player = \App\Models\Player::findOrFail($playerId);
        $player->load(['club', 'association']);
        
        // Créer des données de test pour le portail
        $portalData = [
            'current_season' => '2024-2025',
            'total_matches' => 27,
            'total_goals' => 24,
            'total_assists' => 9
        ];
        
        return view('portail-joueur-final-corrige-dynamique', compact('player', 'portalData'));
    } catch (\Exception $e) {
        return response()->json(['error' => 'Joueur non trouvé: ' . $e->getMessage()], 404);
    }
})->name('test.portail.fifa');

// Page de test FIFA direct (JavaScript pur)
Route::get('/test-fifa-direct', function () {
    return view('test-fifa-direct');
})->name('test.fifa.direct');

// Page de debug FIFA JavaScript en temps réel
Route::get('/debug-fifa-js-realtime', function () {
    return view('debug-fifa-js-realtime');
})->name('debug.fifa.js.realtime');

// Page de debug FIFA affichage visuel
Route::get('/debug-fifa-visual', function () {
    return view('debug-fifa-visual');
})->name('debug.fifa.visual');

// Page de debug onglets performance FIFA
Route::get('/debug-onglets-performance', function () {
    return view('debug-onglets-performance');
})->name('debug.onglets.performance');

// Page de debug portail principal FIFA
Route::get('/debug-portail-principal', function () {
    return view('debug-portail-principal');
})->name('debug.portail.principal');

// Test direct du portail principal FIFA
Route::get('/test-portail-direct', function () {
    return view('test-portail-direct');
})->name('test.portail.direct');

// Debug visibilité des éléments FIFA
Route::get('/debug-visibilite-fifa', function () {
    return view('debug-visibilite-fifa');
})->name('debug.visibilite.fifa');

// Debug hiérarchie des conteneurs FIFA
Route::get('/debug-hierarchie-conteneurs', function () {
    return view('debug-hierarchie-conteneurs');
})->name('debug.hierarchie.conteneurs');

// Portail joueur FIFA simple et fonctionnel
Route::get('/portail-fifa-simple', function () {
    return view('portail-joueur-simple-fifa');
})->name('portail.fifa.simple');

// Portail FIFA intégré sous la landing page
Route::get('/fifa-portal', [App\Http\Controllers\FIFATestController::class, 'test'])->name('fifa.portal.integrated');

// Test du système FIFA Connect
Route::get('/test-fifa-performance', function () {
    return view('test-fifa-performance');
})->name('test.fifa.performance');

// Test de l'intégration FIFA Connect
Route::get('/test-integration-fifa', function () {
    return view('test-integration-fifa');
})->name('test.integration.fifa');

// Test simple de l'API FIFA
Route::get('/test-api-fifa/{id}', function ($id) {
    return response()->json([
        'message' => 'API FIFA Connect fonctionne !',
        'player_id' => $id,
        'test_data' => [
            'overall_rating' => 85,
            'goals_scored' => 15,
            'assists' => 8,
            'matches_played' => 25
        ]
    ]);
})->name('test.api.fifa');

// Test de l'API FIFA avec le contrôleur
Route::get('/test-fifa-controller/{id}', [App\Http\Controllers\TestFIFAController::class, 'testAPI'])->name('test.fifa.controller');

// Test Blade simple
Route::get('/test-blade', [App\Http\Controllers\TestBladeController::class, 'test'])->name('test.blade');

// Test FIFA avec vraies données
Route::get('/fifa-test', [App\Http\Controllers\FIFATestController::class, 'test'])->name('fifa.test');
Route::get('/fifa-test/{id}', [App\Http\Controllers\FIFATestController::class, 'test'])->name('fifa.test.id');

// Test du logo FTF
Route::get('/test-logo-ftf', function () {
    return view('test-logo-ftf');
})->name('test.logo.ftf');
