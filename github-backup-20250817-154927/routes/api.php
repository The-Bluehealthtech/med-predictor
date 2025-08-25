<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeagueChampionshipController;
use App\Http\Controllers\RefereeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CalendarManagementController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PlayerController;
use App\Http\Controllers\Api\V1\ClubController;
use App\Http\Controllers\Api\V1\SeasonController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\PlayerLicenseController;
use App\Http\Controllers\Api\V1\MatchEventController;
use App\Http\Controllers\Api\V1\MatchSheetController;
use App\Http\Controllers\Api\V1\AnalyticsController;
use App\Http\Controllers\Api\V1\FifaWebhookController;
use App\Http\Controllers\Api\V1\PerformanceApiController;
use App\Http\Controllers\FitDashboardController;
use App\Models\Competition;
use App\Http\Controllers\Api\V1\TeamController;
use App\Http\Controllers\Api\V1\MatchController;
use App\Http\Controllers\Api\V1\CompetitionController;
use App\Http\Controllers\Api\V1\PlayerDashboardController;
use App\Http\Controllers\Api\V1\DTNController;
use App\Http\Controllers\Api\V1\RPMController;
use App\Http\Controllers\Api\V1\AthleteController;
use App\Http\Controllers\Api\V1\PCMAController;
use App\Http\Controllers\Api\V1\InjuryController;
use App\Http\Controllers\Api\V1\SCATAssessmentController;
use App\Http\Controllers\Api\V1\MedicalNoteController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\RiskAlertController;
use App\Http\Controllers\Api\ICD11Controller;
use App\Http\Controllers\Api\FHIRController;
use App\Http\Controllers\ImmunisationController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransferDocumentController;
use App\Http\Controllers\TransferPaymentController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\FederationController;
use App\Http\Controllers\Api\AutoKeyController;
use App\Http\Controllers\LandingPageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Landing page route without web middleware
Route::get('/landing', [LandingPageController::class, 'index'])->name('api.landing');

// Public player routes for portail-joueur (no authentication required)
Route::prefix('players')->group(function () {
    Route::get('/', function () {
        $players = \App\Models\Player::with(['club'])
            ->select('id', 'first_name', 'last_name', 'position', 'nationality', 'club_id')
            ->limit(50)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $players->map(function ($player) {
                return [
                    'id' => $player->id,
                    'first_name' => $player->first_name,
                    'last_name' => $player->last_name,
                    'position' => $player->position,
                    'nationality' => $player->nationality,
                    'club' => $player->club ? [
                        'id' => $player->club->id,
                        'name' => $player->club->name,
                        'logo_url' => $player->club->logo_url ?? null
                    ] : null
                ];
            })
        ]);
    });
    
    Route::get('/{player}', function (\App\Models\Player $player) {
        $player->load(['club', 'healthRecords', 'performances']);
        
        // Calculate derived fields
        $playerData = [
            'id' => $player->id,
            'first_name' => $player->first_name,
            'last_name' => $player->last_name,
            'position' => $player->position,
            'nationality' => $player->nationality,
            'date_of_birth' => $player->date_of_birth,
            'age' => $player->age,
            'height' => $player->height,
            'weight' => $player->weight,
            'preferred_foot' => $player->preferred_foot,
            'overall_rating' => $player->overall_rating ?? rand(70, 95),
            'potential_rating' => $player->potential_rating ?? rand(75, 99),
            'skill_moves' => $player->skill_moves ?? rand(1, 5),
            'international_reputation' => $player->international_reputation ?? rand(1, 5),
            'club' => $player->club ? [
                'id' => $player->club->id,
                'name' => $player->club->name,
                'logo_url' => $player->club->logo_url ?? null
            ] : null,
            // Mock FIT Health System data
            'ghs_overall_score' => rand(75, 95),
            'ghs_physical_score' => rand(70, 90),
            'ghs_mental_score' => rand(75, 95),
            'injury_risk_score' => rand(10, 80) / 100,
            'contribution_score' => rand(60, 90),
            'match_availability' => rand(0, 1),
            'value_eur' => rand(1000000, 50000000),
            'wage_eur' => rand(50000, 500000),
            'last_availability_update' => now()->subDays(rand(1, 30))->toISOString()
        ];
        
        return response()->json([
            'success' => true,
            'data' => $playerData
        ]);
    });
    
    Route::get('/{player}/health-records', function (\App\Models\Player $player) {
        $healthRecords = $player->healthRecords()->latest()->limit(10)->get();
        
        return response()->json([
            'success' => true,
            'data' => $healthRecords
        ]);
    });
});

// Player information route - REMOVED (duplicate of the one above)

// Route API pour récupérer toutes les données d'un joueur (360°)
Route::middleware(['auth'])->get('/players/{player}/complete-profile', function ($playerId) {
    try {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur peut accéder à ces données
        if ($user->role !== 'player' || !$user->player || $user->player->id != $playerId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $player = $user->player;
        
        // Charger toutes les relations nécessaires
        $player->load([
            'club.association',
            'healthRecords' => function($query) {
                $query->orderBy('record_date', 'desc')->limit(10);
            },
            'performances' => function($query) {
                $query->orderBy('performance_date', 'desc')->limit(5);
            },
            'pcmas' => function($query) {
                $query->orderBy('assessment_date', 'desc')->limit(5);
            },
            'medicalPredictions' => function($query) {
                $query->orderBy('prediction_date', 'desc')->limit(5);
            },
            'licenses' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'passport',
            'seasonStats',
            'matchEvents' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(20);
            }
        ]);
        
        return response()->json([
            // Informations de base
            'identity' => [
                'id' => $player->id,
                'fifa_connect_id' => $player->fifa_connect_id,
                'first_name' => $player->first_name,
                'last_name' => $player->last_name,
                'full_name' => $player->first_name . ' ' . $player->last_name,
                'date_of_birth' => $player->date_of_birth,
                'age' => $player->age,
                'nationality' => $player->nationality,
                'position' => $player->position,
                'height' => $player->height,
                'weight' => $player->weight,
                'preferred_foot' => $player->preferred_foot,
                'weak_foot' => $player->weak_foot,
                'skill_moves' => $player->skill_moves,
                'player_picture' => $player->player_picture,
                'nation_flag_url' => $player->nation_flag_url
            ],
            
            // Club et contrat
            'club' => $player->club ? [
                'id' => $player->club->id,
                'name' => $player->club->name,
                'logo_url' => $player->club->logo_url,
                'association' => $player->club->association ? [
                    'name' => $player->club->association->name
                ] : null
            ] : null,
            'contract' => [
                'valid_until' => $player->contract_valid_until,
                'release_clause_eur' => $player->release_clause_eur
            ],
            
            // Évaluations FIFA
            'fifa_ratings' => [
                'overall_rating' => $player->overall_rating,
                'potential_rating' => $player->potential_rating,
                'international_reputation' => $player->international_reputation,
                'work_rate' => $player->work_rate,
                'body_type' => $player->body_type
            ],
            
            // Valeur marchande
            'market_value' => [
                'value_eur' => $player->value_eur,
                'wage_eur' => $player->wage_eur,
                'data_value_estimate' => $player->data_value_estimate
            ],
            
            // Score de santé FIT
            'health_score' => [
                'overall_score' => $player->ghs_overall_score,
                'physical_score' => $player->ghs_physical_score,
                'mental_score' => $player->ghs_mental_score,
                'civic_score' => $player->ghs_civic_score,
                'sleep_score' => $player->ghs_sleep_score,
                'color_code' => $player->ghs_color_code,
                'ai_suggestions' => json_decode($player->ghs_ai_suggestions ?? '[]', true),
                'last_updated' => $player->ghs_last_updated
            ],
            
            // Risque de blessure
            'injury_risk' => [
                'score' => $player->injury_risk_score,
                'level' => $player->injury_risk_level,
                'reason' => $player->injury_risk_reason,
                'last_assessed' => $player->injury_risk_last_assessed
            ],
            
            // Disponibilité
            'availability' => [
                'match_availability' => $player->match_availability,
                'last_update' => $player->last_availability_update
            ],
            
            // Performances récentes
            'recent_performances' => $player->performances->map(function($perf) {
                return [
                    'date' => $perf->performance_date,
                    'overall_score' => $perf->overall_performance_score,
                    'endurance_score' => $perf->endurance_score,
                    'strength_score' => $perf->strength_score,
                    'speed_score' => $perf->speed_score,
                    'agility_score' => $perf->agility_score,
                    'technical_score' => $perf->technical_score,
                    'tactical_score' => $perf->tactical_score,
                    'mental_score' => $perf->mental_score,
                    'social_score' => $perf->social_score,
                    'notes' => $perf->notes
                ];
            }),
            
            // Dossiers médicaux récents
            'recent_health_records' => $player->healthRecords->map(function($record) {
                return [
                    'id' => $record->id,
                    'record_date' => $record->record_date,
                    'visit_type' => $record->visit_type,
                    'diagnosis' => $record->diagnosis,
                    'risk_score' => $record->risk_score,
                    'status' => $record->status
                ];
            }),
            
            // PCMA récents
            'recent_pcma' => $player->pcmas->map(function($pcma) {
                return [
                    'id' => $pcma->id,
                    'assessment_date' => $pcma->assessment_date,
                    'type' => $pcma->type,
                    'status' => $pcma->status,
                    'fifa_compliant' => $pcma->fifa_compliant
                ];
            }),
            
            // Prédictions médicales
            'medical_predictions' => $player->medicalPredictions->map(function($prediction) {
                return [
                    'id' => $prediction->id,
                    'prediction_type' => $prediction->prediction_type,
                    'predicted_condition' => $prediction->predicted_condition,
                    'risk_probability' => $prediction->risk_probability,
                    'confidence_score' => $prediction->confidence_score,
                    'prediction_date' => $prediction->prediction_date,
                    'recommendations' => $prediction->recommendations
                ];
            }),
            
            // Licences
            'licenses' => $player->licenses->map(function($license) {
                return [
                    'id' => $license->id,
                    'license_type' => $license->license_type,
                    'license_number' => $license->license_number,
                    'status' => $license->status,
                    'issue_date' => $license->issue_date,
                    'expiry_date' => $license->expiry_date,
                    'club' => $license->club ? [
                        'name' => $license->club->name,
                        'logo_url' => $license->club->logo_url
                    ] : null,
                    'transfer_status' => $license->transfer_status,
                    'contract_type' => $license->contract_type,
                    'contract_start_date' => $license->contract_start_date,
                    'contract_end_date' => $license->contract_end_date,
                    'medical_clearance' => $license->medical_clearance,
                    'international_clearance' => $license->international_clearance,
                    'is_active' => $license->isActive(),
                    'days_until_expiry' => $license->daysUntilExpiry()
                ];
            }),
            
            // Passeport joueur
            'passport' => $player->passport ? [
                'id' => $player->passport->id,
                'passport_number' => $player->passport->passport_number,
                'issue_date' => $player->passport->issue_date,
                'expiry_date' => $player->passport->expiry_date,
                'issuing_country' => $player->passport->issuing_country,
                'nationality' => $player->passport->nationality,
                'eligibility_countries' => $player->passport->eligibility_countries,
                'international_caps' => $player->passport->international_caps,
                'youth_international_caps' => $player->passport->youth_international_caps
            ] : null,
            
            // Statistiques saisonnières
            'seasonal_stats' => $player->seasonStats->map(function($stat) {
                return [
                    'season' => $stat->season,
                    'club' => $stat->club_name,
                    'appearances' => $stat->appearances,
                    'goals' => $stat->goals,
                    'assists' => $stat->assists,
                    'yellow_cards' => $stat->yellow_cards,
                    'red_cards' => $stat->red_cards,
                    'minutes_played' => $stat->minutes_played,
                    'average_rating' => $stat->average_rating
                ];
            }),
            
            // Événements de match récents
            'recent_match_events' => $player->matchEvents->map(function($event) {
                return [
                    'id' => $event->id,
                    'event_type' => $event->event_type,
                    'minute' => $event->minute,
                    'description' => $event->description,
                    'match_date' => $event->match ? $event->match->match_date : null,
                    'opponent' => $event->match ? $event->match->opponent_name : null
                ];
            }),
            
            // Statistiques
            'statistics' => [
                'total_health_records' => $player->healthRecords->count(),
                'total_performances' => $player->performances->count(),
                'total_pcma' => $player->pcmas->count(),
                'total_predictions' => $player->medicalPredictions->count(),
                'total_licenses' => $player->licenses->count(),
                'active_licenses' => $player->licenses->where('status', 'active')->count(),
                'international_caps' => $player->passport ? $player->passport->international_caps : 0,
                'contribution_score' => $player->contribution_score,
                'matches_contributed' => $player->matches_contributed,
                'training_sessions_logged' => $player->training_sessions_logged,
                'health_records_contributed' => $player->health_records_contributed
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
})->name('api.players.complete-profile');

// PDF generation routes (public access)
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
})->name('api.test.pdf');

Route::post('/pcma/pdf', [App\Http\Controllers\PCMAController::class, 'generatePdf'])->name('api.pcma.pdf');
Route::post('/pcma/store', [App\Http\Controllers\PCMAController::class, 'store'])->name('api.pcma.store');

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    // Public routes (no authentication required)
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/auth/check', function () {
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => auth()->check() ? [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
            ] : null,
        ]);
    });
    
    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication routes
        Route::prefix('auth')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });
        
        // Analytics routes
        Route::prefix('analytics')->group(function () {
            Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
            Route::get('/matches', [AnalyticsController::class, 'matchAnalytics']);
            Route::get('/players', [AnalyticsController::class, 'playerAnalytics']);
            Route::get('/licenses', [AnalyticsController::class, 'licenseAnalytics']);
            Route::get('/clubs', [AnalyticsController::class, 'clubAnalytics']);
            Route::get('/export', [AnalyticsController::class, 'export']);
        });
        
        // Player routes
        Route::prefix('players')->group(function () {
            Route::post('/', [PlayerController::class, 'store']);
            Route::put('/{player}', [PlayerController::class, 'update']);
            Route::delete('/{player}', [PlayerController::class, 'destroy']);
            Route::get('/{player}/profile', [PlayerController::class, 'profile']);
            Route::get('/{player}/statistics', [PlayerController::class, 'statistics']);
        });
        
        // Club routes
        Route::prefix('clubs')->group(function () {
            Route::get('/', [ClubController::class, 'index']);
            Route::post('/', [ClubController::class, 'store']);
            Route::get('/{club}', [ClubController::class, 'show']);
            Route::put('/{club}', [ClubController::class, 'update']);
            Route::delete('/{club}', [ClubController::class, 'destroy']);
            Route::get('/{club}/players', [ClubController::class, 'players']);
            Route::get('/{club}/teams', [ClubController::class, 'teams']);
            Route::get('/{club}/statistics', [ClubController::class, 'statistics']);
        });
        
        // Team routes
        Route::prefix('teams')->group(function () {
            Route::get('/', [TeamController::class, 'index']);
            Route::post('/', [TeamController::class, 'store']);
            Route::get('/{team}', [TeamController::class, 'show']);
            Route::put('/{team}', [TeamController::class, 'update']);
            Route::delete('/{team}', [TeamController::class, 'destroy']);
            Route::post('/{team}/players', [TeamController::class, 'addPlayer']);
            Route::delete('/{team}/players', [TeamController::class, 'removePlayer']);
            Route::get('/{team}/roster', [TeamController::class, 'roster']);
            Route::get('/{team}/statistics', [TeamController::class, 'statistics']);
            Route::get('/{team}/standings', [TeamController::class, 'standings']);
        });
        
        // Match routes
        Route::prefix('matches')->group(function () {
            Route::get('/', [MatchController::class, 'index']);
            Route::post('/', [MatchController::class, 'store']);
            Route::get('/{match}', [MatchController::class, 'show']);
            Route::put('/{match}', [MatchController::class, 'update']);
            Route::delete('/{match}', [MatchController::class, 'destroy']);
            Route::get('/{match}/lineups', [MatchController::class, 'lineups']);
            Route::get('/{match}/statistics', [MatchController::class, 'statistics']);
            Route::patch('/{match}/status', [MatchController::class, 'updateStatus']);
        });
        
        // Season routes
        Route::prefix('seasons')->group(function () {
            Route::get('/', [SeasonController::class, 'index']);
            Route::post('/', [SeasonController::class, 'store']);
            Route::get('/current', [SeasonController::class, 'current']);
            Route::get('/active', [SeasonController::class, 'active']);
            Route::get('/upcoming', [SeasonController::class, 'upcoming']);
            Route::get('/completed', [SeasonController::class, 'completed']);
            Route::get('/{season}', [SeasonController::class, 'show']);
            Route::put('/{season}', [SeasonController::class, 'update']);
            Route::delete('/{season}', [SeasonController::class, 'destroy']);
            Route::post('/{season}/set-current', [SeasonController::class, 'setCurrent']);
            Route::patch('/{season}/status', [SeasonController::class, 'updateStatus']);
            Route::get('/{season}/statistics', [SeasonController::class, 'statistics']);
        });
        
        // User routes
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'store']);
            Route::get('/by-role', [UserController::class, 'byRole']);
            Route::get('/by-status', [UserController::class, 'byStatus']);
            Route::get('/by-club', [UserController::class, 'byClub']);
            Route::get('/by-association', [UserController::class, 'byAssociation']);
            Route::get('/online', [UserController::class, 'online']);
            Route::get('/recently-active', [UserController::class, 'recentlyActive']);
            Route::get('/{user}', [UserController::class, 'show']);
            Route::put('/{user}', [UserController::class, 'update']);
            Route::delete('/{user}', [UserController::class, 'destroy']);
            Route::patch('/{user}/role', [UserController::class, 'updateRole']);
            Route::patch('/{user}/permissions', [UserController::class, 'updatePermissions']);
            Route::patch('/{user}/status', [UserController::class, 'updateStatus']);
            Route::get('/{user}/statistics', [UserController::class, 'statistics']);
        });
        
        // Player License routes
        Route::prefix('player-licenses')->group(function () {
            Route::get('/', [PlayerLicenseController::class, 'index']);
            Route::post('/', [PlayerLicenseController::class, 'store']);
            Route::get('/{player_license}', [PlayerLicenseController::class, 'show']);
            Route::put('/{player_license}', [PlayerLicenseController::class, 'update']);
            Route::delete('/{player_license}', [PlayerLicenseController::class, 'destroy']);
            Route::post('/{player_license}/approve', [PlayerLicenseController::class, 'approve']);
            Route::post('/{player_license}/reject', [PlayerLicenseController::class, 'reject']);
            Route::post('/{player_license}/renew', [PlayerLicenseController::class, 'renew']);
            Route::post('/{player_license}/suspend', [PlayerLicenseController::class, 'suspend']);
            Route::post('/{player_license}/transfer', [PlayerLicenseController::class, 'transfer']);
            Route::patch('/{player_license}/status', [PlayerLicenseController::class, 'status']);
        });
        
        // Match Event routes
        Route::prefix('match-events')->group(function () {
            Route::get('/', [MatchEventController::class, 'index']);
            Route::post('/', [MatchEventController::class, 'store']);
            Route::get('/statistics', [MatchEventController::class, 'statistics']);
            Route::get('/{match_event}', [MatchEventController::class, 'show']);
            Route::put('/{match_event}', [MatchEventController::class, 'update']);
            Route::delete('/{match_event}', [MatchEventController::class, 'destroy']);
            Route::post('/{match_event}/confirm', [MatchEventController::class, 'confirm']);
            Route::post('/{match_event}/contest', [MatchEventController::class, 'contest']);
        });
        
        // Match-specific event routes
        Route::prefix('matches/{match}/events')->group(function () {
            Route::get('/', [MatchController::class, 'events']);
            Route::post('/', [MatchController::class, 'addEvent']);
            Route::get('/timeline', [MatchEventController::class, 'timeline']);
        });
        
        // Team-specific event routes
        Route::prefix('teams/{team}/events')->group(function () {
            Route::get('/', [MatchEventController::class, 'byTeam']);
        });
        
        // Player-specific event routes
        Route::prefix('players/{player}/events')->group(function () {
            Route::get('/', [MatchEventController::class, 'byPlayer']);
        });
        
        // Match Sheet routes
        Route::prefix('match-sheets')->group(function () {
            Route::get('/', [MatchSheetController::class, 'index']);
            Route::post('/', [MatchSheetController::class, 'store']);
            Route::get('/{match_sheet}', [MatchSheetController::class, 'show']);
            Route::put('/{match_sheet}', [MatchSheetController::class, 'update']);
            Route::delete('/{match_sheet}', [MatchSheetController::class, 'destroy']);
            Route::post('/{match_sheet}/submit', [MatchSheetController::class, 'submit']);
            Route::post('/{match_sheet}/validate', [MatchSheetController::class, 'validate']);
            Route::post('/{match_sheet}/reject', [MatchSheetController::class, 'reject']);
            Route::post('/{match_sheet}/assign-referee', [MatchSheetController::class, 'assignReferee']);
            Route::post('/{match_sheet}/sign-team', [MatchSheetController::class, 'signByTeam']);
            Route::post('/{match_sheet}/sign-referee', [MatchSheetController::class, 'signByReferee']);
            Route::post('/{match_sheet}/sign-observer', [MatchSheetController::class, 'signByObserver']);
            Route::post('/{match_sheet}/sign-lineup', [MatchSheetController::class, 'signLineup']);
            Route::post('/{match_sheet}/sign-post-match', [MatchSheetController::class, 'signPostMatch']);
            Route::post('/{match_sheet}/fa-validate', [MatchSheetController::class, 'faValidate']);
            Route::post('/{match_sheet}/lock-lineups', [MatchSheetController::class, 'lockLineups']);
            Route::post('/{match_sheet}/unlock-lineups', [MatchSheetController::class, 'unlockLineups']);
            Route::post('/{match_sheet}/lock-match-events', [MatchSheetController::class, 'lockMatchEvents']);
            Route::post('/{match_sheet}/unlock-match-events', [MatchSheetController::class, 'unlockMatchEvents']);
            Route::post('/{match_sheet}/transition-stage', [MatchSheetController::class, 'transitionStage']);
            Route::get('/{match_sheet}/stage-progress', [MatchSheetController::class, 'getStageProgress']);
            Route::get('/{match_sheet}/statistics', [MatchSheetController::class, 'getStatistics']);
            Route::get('/{match_sheet}/export', [MatchSheetController::class, 'export']);
        });
        
        // Performance API routes
        Route::prefix('performances')->group(function () {
            Route::get('/', [PerformanceApiController::class, 'index']);
            Route::post('/', [PerformanceApiController::class, 'store']);
            Route::get('/{performance}', [PerformanceApiController::class, 'show']);
            Route::put('/{performance}', [PerformanceApiController::class, 'update']);
            Route::delete('/{performance}', [PerformanceApiController::class, 'destroy']);
            Route::get('/analytics', [PerformanceApiController::class, 'analytics']);
            Route::get('/export', [PerformanceApiController::class, 'export']);
        });
        
        // Medical Data Management routes
        Route::prefix('athletes')->group(function () {
            Route::get('/', [AthleteController::class, 'index']);
            Route::post('/', [AthleteController::class, 'store']);
            Route::get('/{athlete}', [AthleteController::class, 'show']);
            Route::put('/{athlete}', [AthleteController::class, 'update']);
            Route::delete('/{athlete}', [AthleteController::class, 'destroy']);
            Route::get('/{athlete}/medical-dashboard', [AthleteController::class, 'medicalDashboard']);
            Route::get('/{athlete}/health-summary', [AthleteController::class, 'getHealthSummary']);
            Route::get('/{athlete}/imaging-studies', [AthleteController::class, 'getImagingStudies']);
        });

        // PCMA routes
        Route::prefix('pcmas')->group(function () {
            Route::get('/', [PCMAController::class, 'index']);
            Route::post('/', [PCMAController::class, 'store']);
            Route::get('/{pcma}', [PCMAController::class, 'show']);
            Route::put('/{pcma}', [PCMAController::class, 'update']);
            Route::delete('/{pcma}', [PCMAController::class, 'destroy']);
            Route::post('/{pcma}/complete', [PCMAController::class, 'complete']);
            Route::post('/{pcma}/fail', [PCMAController::class, 'fail']);
            Route::post('/prefill-from-transcript', [PCMAController::class, 'prefillFromTranscript']);
        });

        // PCMA Management
        Route::prefix('pcmas')->group(function () {
            Route::get('/player/{player}', [PCMAController::class, 'getPlayerPCMAs']);
            Route::get('/fifa-connect/{fifaConnectId}', [PCMAController::class, 'getFifaConnectPCMAs']);
            Route::get('/signed', [PCMAController::class, 'getSignedPCMAs']);
            Route::post('/prefill-from-transcript', [PCMAController::class, 'prefillFromTranscript']);
            Route::post('/whisper-transcribe', [PCMAController::class, 'whisperTranscribe']);
            Route::post('/fetch-fhir-data', [PCMAController::class, 'fetchFhirData']);
            Route::post('/ocr-extract', [PCMAController::class, 'ocrExtract']);
            
            // AI Analysis Routes
            Route::post('/ai-analyze-ecg', [PCMAController::class, 'aiAnalyzeEcg']);
            Route::post('/ai-analyze-mri', [PCMAController::class, 'aiAnalyzeMri']);
            Route::post('/ai-analyze-complete', [PCMAController::class, 'aiAnalyzeComplete']);
            
            // DICOM Viewer Routes
            Route::post('/dicom-viewer/process', [PCMAController::class, 'processDicomFile']);
            Route::get('/dicom-viewer/metadata/{file}', [PCMAController::class, 'getDicomMetadata']);
        });

        // Athlete PCMA routes
        Route::prefix('athletes/{athlete}/pcmas')->group(function () {
            Route::get('/', [PCMAController::class, 'indexForAthlete']);
            Route::get('/statistics', [PCMAController::class, 'statisticsForAthlete']);
        });

        // Injury routes
        Route::prefix('injuries')->group(function () {
            Route::get('/', [InjuryController::class, 'index']);
            Route::post('/', [InjuryController::class, 'store']);
            Route::get('/{injury}', [InjuryController::class, 'show']);
            Route::put('/{injury}', [InjuryController::class, 'update']);
            Route::delete('/{injury}', [InjuryController::class, 'destroy']);
            Route::post('/{injury}/resolve', [InjuryController::class, 'resolve']);
            Route::post('/{injury}/progress', [InjuryController::class, 'updateProgress']);
            Route::get('/{injury}/validate-rtp', [InjuryController::class, 'validateReturnToPlay']);
        });

        // Athlete Injury routes
        Route::prefix('athletes/{athlete}/injuries')->group(function () {
            Route::get('/', [InjuryController::class, 'indexForAthlete']);
            Route::get('/statistics', [InjuryController::class, 'statisticsForAthlete']);
        });

        // SCAT Assessment routes
        Route::prefix('scat-assessments')->group(function () {
            Route::get('/', [SCATAssessmentController::class, 'index']);
            Route::post('/', [SCATAssessmentController::class, 'store']);
            Route::get('/{assessment}', [SCATAssessmentController::class, 'show']);
            Route::put('/{assessment}', [SCATAssessmentController::class, 'update']);
            Route::delete('/{assessment}', [SCATAssessmentController::class, 'destroy']);
            Route::get('/{assessment}/compare-baseline', [SCATAssessmentController::class, 'compareWithBaseline']);
        });

        // Athlete SCAT Assessment routes
        Route::prefix('athletes/{athlete}/scat-assessments')->group(function () {
            Route::get('/', [SCATAssessmentController::class, 'indexForAthlete']);
            Route::get('/statistics', [SCATAssessmentController::class, 'statisticsForAthlete']);
            Route::get('/concussion-timeline', [SCATAssessmentController::class, 'concussionTimeline']);
        });

        // Medical Note routes
        Route::prefix('medical-notes')->group(function () {
            Route::get('/', [MedicalNoteController::class, 'index']);
            Route::post('/', [MedicalNoteController::class, 'store']);
            Route::get('/{note}', [MedicalNoteController::class, 'show']);
            Route::put('/{note}', [MedicalNoteController::class, 'update']);
            Route::delete('/{note}', [MedicalNoteController::class, 'destroy']);
            Route::post('/{note}/sign', [MedicalNoteController::class, 'sign']);
            Route::get('/ai/health-check', [MedicalNoteController::class, 'aiHealthCheck']);
        });

        // Medical Note AI routes
        Route::prefix('medical-notes/ai')->group(function () {
            Route::post('/generate-draft', [MedicalNoteController::class, 'generateDraft']);
        });

        // Athlete Medical Note routes
        Route::prefix('athletes/{athlete}/medical-notes')->group(function () {
            Route::get('/', [MedicalNoteController::class, 'indexForAthlete']);
            Route::get('/statistics', [MedicalNoteController::class, 'statisticsForAthlete']);
        });

        // Dashboard routes
        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::get('/alerts-summary', [DashboardController::class, 'alertsSummary']);
            Route::get('/quick-actions', [DashboardController::class, 'quickActions']);
        });

        // Risk Alert routes
        Route::prefix('risk-alerts')->group(function () {
            Route::get('/', [RiskAlertController::class, 'index']);
            Route::post('/', [RiskAlertController::class, 'store']);
            Route::get('/statistics', [RiskAlertController::class, 'statistics']);
            Route::get('/by-source/{source}', [RiskAlertController::class, 'bySource']);
            Route::post('/bulk-acknowledge', [RiskAlertController::class, 'bulkAcknowledge']);
        });

        Route::prefix('risk-alerts')->group(function () {
            Route::get('/{alert}', [RiskAlertController::class, 'show']);
            Route::put('/{alert}', [RiskAlertController::class, 'update']);
            Route::delete('/{alert}', [RiskAlertController::class, 'destroy']);
            Route::patch('/{alert}/acknowledge', [RiskAlertController::class, 'acknowledge']);
        });

        // Athlete Risk Alert routes
        Route::prefix('athletes/{athlete}/risk-alerts')->group(function () {
            Route::get('/', [RiskAlertController::class, 'indexForAthlete']);
        });
        
        // Performance API additional routes
        Route::prefix('performances')->group(function () {
            Route::post('/bulk-import', [PerformanceApiController::class, 'bulkImport']);
            Route::get('/dashboard', [PerformanceApiController::class, 'dashboard']);
            Route::get('/compare', [PerformanceApiController::class, 'compare']);
            Route::get('/trends', [PerformanceApiController::class, 'trends']);
            Route::post('/generate-alerts', [PerformanceApiController::class, 'generateAlerts']);
        });
        
        // ICD-11 API routes
        Route::prefix('icd11')->group(function () {
            Route::get('/search', [ICD11Controller::class, 'search']);
            Route::get('/code/{code}', [ICD11Controller::class, 'getCode']);
            Route::get('/chapters', [ICD11Controller::class, 'getChapters']);
            Route::get('/health', [ICD11Controller::class, 'health']);
        });

        // Immunization API routes
        Route::prefix('athletes/{athlete}/immunisations')->group(function () {
            Route::get('/', [ImmunisationController::class, 'index']);
            Route::post('/', [ImmunisationController::class, 'store']);
            Route::get('/statistics', [ImmunisationController::class, 'statistics']);
            Route::post('/sync', [ImmunisationController::class, 'sync']);
            Route::get('/export', [ImmunisationController::class, 'export']);
        });

        Route::prefix('immunisations')->group(function () {
            Route::get('/{immunisation}', [ImmunisationController::class, 'show']);
            Route::put('/{immunisation}', [ImmunisationController::class, 'update']);
            Route::delete('/{immunisation}', [ImmunisationController::class, 'destroy']);
            Route::post('/{immunisation}/verify', [ImmunisationController::class, 'verify']);
        });

        // FHIR connectivity routes
        Route::prefix('fhir')->group(function () {
            Route::get('/connectivity', [FHIRController::class, 'connectivity']);
            Route::get('/sync-statistics', [FHIRController::class, 'syncStatistics']);
            Route::get('/capabilities', [FHIRController::class, 'capabilities']);
            Route::post('/test-resource', [FHIRController::class, 'testResource']);
        });

        // Debug route to test authentication
        Route::get('/debug/auth', function (Request $request) {
            return response()->json([
                'user' => $request->user(),
                'authenticated' => auth()->check(),
                'abilities' => $request->user() ? $request->user()->getAbilities() : []
            ]);
        });
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// League Championship Routes (temporarily public for testing)
Route::prefix('league-championship')->group(function () {
    Route::get('/competitions/{competition}', [LeagueChampionshipController::class, 'show']);
    Route::get('/competitions/{competition}/schedule', [LeagueChampionshipController::class, 'getSchedule']);
    Route::post('/competitions/{competition}/generate-schedule', [LeagueChampionshipController::class, 'generateSchedule']);
    Route::get('/competitions/{competition}/standings', [LeagueChampionshipController::class, 'getStandings']);
    Route::get('/competitions/{competition}/player-stats', [LeagueChampionshipController::class, 'getPlayerStats']);
    
    // Individual match route
    Route::get('/matches/{gameMatch}', [LeagueChampionshipController::class, 'getMatch']);
    
    Route::prefix('matches/{gameMatch}')->group(function () {
        Route::post('/roster', [LeagueChampionshipController::class, 'submitRoster']);
        Route::get('/rosters', [LeagueChampionshipController::class, 'getRosters']);
        Route::post('/officials', [LeagueChampionshipController::class, 'assignOfficials']);
        Route::patch('/status', [LeagueChampionshipController::class, 'updateMatchStatus']);
        Route::patch('/statistics', [LeagueChampionshipController::class, 'updateStatistics']);
        
        // Event routes
        Route::get('/events', [LeagueChampionshipController::class, 'getEvents']);
        Route::post('/events', [LeagueChampionshipController::class, 'addEvent']);
        Route::delete('/events/{eventId}', [LeagueChampionshipController::class, 'deleteEvent']);
    });
});

// Simple test endpoints (temporary)
Route::get('/competitions', function () {
    $competitions = Competition::with('teams')->get();
    return response()->json(['success' => true, 'data' => $competitions]);
});

Route::get('/competitions/{competition}', function (Competition $competition) {
    $competition->load('teams');
    return response()->json(['success' => true, 'data' => $competition]);
});

Route::get('/matches/recent', function () {
    $matches = \App\Models\GameMatch::with(['homeTeam', 'awayTeam'])
        ->orderBy('kickoff_time', 'desc')
        ->limit(10)
        ->get();
    return response()->json(['success' => true, 'data' => $matches]);
});

Route::get('/matches/{gameMatch}', function (\App\Models\GameMatch $gameMatch) {
    $gameMatch->load(['homeTeam', 'awayTeam', 'competition']);
    return response()->json(['success' => true, 'data' => $gameMatch]);
});

Route::get('/matches/{gameMatch}/events', function (\App\Models\GameMatch $gameMatch) {
    $events = $gameMatch->events()->with('player')->get();
    return response()->json(['success' => true, 'data' => $events]);
});

Route::post('/matches/{gameMatch}/events', function (Request $request, \App\Models\GameMatch $gameMatch) {
    $event = $gameMatch->events()->create($request->all());
    return response()->json(['success' => true, 'data' => $event]);
});

Route::delete('/matches/{gameMatch}/events/{event}', function (\App\Models\GameMatch $gameMatch, \App\Models\MatchEvent $event) {
    $event->delete();
    return response()->json(['success' => true]);
});

// Add rosters endpoint for MatchSheet component
Route::get('/matches/{gameMatch}/rosters', function (\App\Models\GameMatch $gameMatch) {
    $rosters = [];
    foreach ([$gameMatch->home_team_id, $gameMatch->away_team_id] as $teamId) {
        $players = \App\Models\MatchRoster::where('match_id', $gameMatch->id)
            ->where('team_id', $teamId)
            ->with('player')
            ->orderBy('is_starter', 'desc')
            ->orderBy('jersey_number')
            ->get();
        $rosters[] = [
            'team_id' => $teamId,
            'players' => $players
        ];
    }
    return response()->json(['success' => true, 'data' => $rosters]);
});

// Add match status update endpoint
Route::put('/matches/{gameMatch}/status', function (Request $request, \App\Models\GameMatch $gameMatch) {
    $validated = $request->validate([
        'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        'home_score' => 'nullable|integer|min:0',
        'away_score' => 'nullable|integer|min:0'
    ]);
    
    $gameMatch->update($validated);
    return response()->json(['success' => true, 'data' => $gameMatch]);
});

// Referee Routes
Route::prefix('referee')->middleware(['auth:sanctum', 'referee'])->group(function () {
    Route::get('/dashboard', [RefereeController::class, 'dashboard']);
    Route::get('/matches/{gameMatch}/events', [RefereeController::class, 'getMatchEvents']);
    Route::post('/matches/{gameMatch}/events', [RefereeController::class, 'recordEvent']);
    Route::patch('/matches/{gameMatch}/status', [RefereeController::class, 'updateMatchStatus']);
    Route::post('/events/{event}/contest', [RefereeController::class, 'contestEvent']);
    Route::post('/events/{event}/confirm', [RefereeController::class, 'confirmEvent']);
});

// Report Routes
Route::prefix('reports')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/competitions/{competition}/standings', [ReportController::class, 'standingsReport']);
    Route::get('/competitions/{competition}/standings/export', [ReportController::class, 'exportStandings']);
    Route::get('/competitions/{competition}/player-stats', [ReportController::class, 'playerStatsReport']);
    Route::get('/competitions/{competition}/summary', [ReportController::class, 'competitionSummary']);
    Route::get('/matches/{gameMatch}', [ReportController::class, 'matchReport']);
});

// Calendar Management Routes
Route::prefix('calendar')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('competitions/{competition}')->group(function () {
        Route::post('/generate-full-schedule', [CalendarManagementController::class, 'generateFullSchedule']);
        Route::get('/schedule', [CalendarManagementController::class, 'getSchedule']);
        Route::post('/matches/validate-and-create', [CalendarManagementController::class, 'validateAndCreateMatch']);
        Route::get('/teams', [CalendarManagementController::class, 'getAvailableTeams']);
        Route::delete('/schedule', [CalendarManagementController::class, 'clearSchedule']);
    });
    
    Route::prefix('matches/{gameMatch}')->group(function () {
        Route::put('/', [CalendarManagementController::class, 'updateMatch']);
        Route::delete('/', [CalendarManagementController::class, 'deleteMatch']);
    });
    
    Route::get('/venues', [CalendarManagementController::class, 'getAvailableVenues']);
});

// Club Management API Routes
Route::middleware(['auth:sanctum', 'role:club_admin,club_manager,club_medical'])->prefix('club')->group(function () {
    Route::get('/eligible-players/{competition}', [App\Http\Controllers\ClubManagementController::class, 'getEligiblePlayers']);
    Route::post('/teams/{team}/players', [App\Http\Controllers\ClubManagementController::class, 'addPlayerToTeam']);
});

// Competition Management API Routes
Route::middleware(['auth:sanctum', 'role:association_admin,association_registrar'])->prefix('competitions')->group(function () {
    Route::post('/{competition}/register-team', [App\Http\Controllers\CompetitionManagementController::class, 'registerTeam']);
});

// Competition API Routes
Route::middleware(['auth:sanctum', 'verified'])->prefix('v1')->group(function () {
    Route::apiResource('competitions', CompetitionController::class);
    Route::get('competitions/{competition}/standings', [CompetitionController::class, 'standings']);
    Route::get('competitions/{competition}/seasons', [CompetitionController::class, 'seasons']);
    Route::get('competitions/{competition}/teams', [CompetitionController::class, 'teams']);
    Route::get('competitions/{competition}/statistics', [CompetitionController::class, 'statistics']);
    Route::post('competitions/{competition}/teams', [CompetitionController::class, 'addTeam']);
    Route::delete('competitions/{competition}/teams', [CompetitionController::class, 'removeTeam']);
});

// Competition API routes for Vue.js components
Route::middleware(['auth:sanctum'])->prefix('competitions')->name('competitions.')->group(function () {
    Route::get('/{competition}/matches', [CompetitionController::class, 'matches'])->name('matches');
    Route::get('/{competition}/standings', [CompetitionController::class, 'standings'])->name('standings');
});

// Match API routes for Vue.js components
Route::middleware(['auth:sanctum'])->prefix('matches')->name('matches.')->group(function () {
    Route::get('/{match}', [MatchController::class, 'show'])->name('show');
    Route::get('/{match}/sheet', [MatchController::class, 'sheet'])->name('sheet');
});

// Player Dashboard API Routes
Route::middleware(['auth:sanctum', 'player.access'])->prefix('v1/player-dashboard')->name('api.v1.player-dashboard.')->group(function () {
    // Profile and FIFA ID
    Route::get('/profile', [PlayerDashboardController::class, 'profile'])->name('profile');
    
    // Performance Dashboard
    Route::get('/performance', [PlayerDashboardController::class, 'performance'])->name('performance');
    
    // General Health Score (GHS)
    Route::get('/ghs', [PlayerDashboardController::class, 'ghs'])->name('ghs');
    Route::post('/ghs', [PlayerDashboardController::class, 'updateGHS'])->name('ghs.update');
    
    // Health & Fitness
    Route::get('/health', [PlayerDashboardController::class, 'health'])->name('health');
    
    // AI-Powered Injury Risk
    Route::get('/injury-risk', [PlayerDashboardController::class, 'injuryRisk'])->name('injury-risk');
    
    // Match Sheet Access
    Route::get('/match-sheet', [PlayerDashboardController::class, 'matchSheet'])->name('match-sheet');
    Route::post('/match-sheet/availability', [PlayerDashboardController::class, 'updateAvailability'])->name('availability.update');
    
    // Licensing
    Route::get('/licensing', [PlayerDashboardController::class, 'licensing'])->name('licensing');
    Route::get('/licensing/{license}/download', [PlayerDashboardController::class, 'downloadLicense'])->name('license.download');
    
    // Data Ownership
    Route::get('/data-ownership', [PlayerDashboardController::class, 'dataOwnership'])->name('data-ownership');
    Route::get('/export', [PlayerDashboardController::class, 'exportData'])->name('export');
    Route::get('/export/download', [PlayerDashboardController::class, 'downloadExport'])->name('export.download');
    
    // Documents & Media
    Route::get('/documents', [PlayerDashboardController::class, 'documents'])->name('documents');
    Route::post('/documents', [PlayerDashboardController::class, 'uploadDocument'])->name('documents.upload');
    Route::get('/documents/{document}/download', [PlayerDashboardController::class, 'downloadDocument'])->name('documents.download');
    Route::delete('/documents/{document}', [PlayerDashboardController::class, 'deleteDocument'])->name('documents.delete');
});

// Routes pour les transferts
Route::middleware(['auth:sanctum'])->group(function () {
    // Transferts
    Route::get('/transfers', [TransferController::class, 'index']);
    Route::post('/transfers', [TransferController::class, 'store']);
    Route::get('/transfers/{transfer}', [TransferController::class, 'show']);
    Route::put('/transfers/{transfer}', [TransferController::class, 'update']);
    Route::delete('/transfers/{transfer}', [TransferController::class, 'destroy']);
    Route::post('/transfers/{transfer}/submit-fifa', [TransferController::class, 'submitToFifa']);
    Route::post('/transfers/{transfer}/check-itc', [TransferController::class, 'checkItcStatus']);
    Route::get('/transfers/statistics', [TransferController::class, 'statistics']);

    // Documents de transfert
    Route::get('/transfers/{transfer}/documents', [TransferDocumentController::class, 'index']);
    Route::post('/transfers/{transfer}/documents', [TransferDocumentController::class, 'store']);
    Route::get('/transfers/{transfer}/documents/{document}', [TransferDocumentController::class, 'show']);
    Route::put('/transfers/{transfer}/documents/{document}', [TransferDocumentController::class, 'update']);
    Route::delete('/transfers/{transfer}/documents/{document}', [TransferDocumentController::class, 'destroy']);
    Route::post('/transfers/{transfer}/documents/{document}/approve', [TransferDocumentController::class, 'approve']);
    Route::post('/transfers/{transfer}/documents/{document}/reject', [TransferDocumentController::class, 'reject']);

    // Paiements de transfert
    Route::get('/transfers/{transfer}/payments', [TransferPaymentController::class, 'index']);
    Route::post('/transfers/{transfer}/payments', [TransferPaymentController::class, 'store']);
    Route::get('/transfers/{transfer}/payments/{payment}', [TransferPaymentController::class, 'show']);
    Route::put('/transfers/{transfer}/payments/{payment}', [TransferPaymentController::class, 'update']);
    Route::delete('/transfers/{transfer}/payments/{payment}', [TransferPaymentController::class, 'destroy']);

    // Passeport du jour
    Route::get('/clubs/{club}/players/daily-passport', [PassportController::class, 'clubPassport']);
    Route::get('/federations/{federation}/daily-passport', [PassportController::class, 'federationPassport']);
    Route::get('/players/{player}/transfers', [PassportController::class, 'playerTransfers']);

    // Fédérations
    Route::get('/federations', [FederationController::class, 'index']);
    Route::get('/federations/{federation}', [FederationController::class, 'show']);
});

// Webhook FIFA (pas d'authentification requise)
Route::post('/webhooks/fifa', [FifaWebhookController::class, 'handle']);

// FIFA Sync Management Routes (protected)
Route::middleware(['web', 'auth'])->prefix('fifa')->group(function () {
    Route::get('/sync-stats', [FifaWebhookController::class, 'getSyncStats']);
    Route::get('/sync-logs', [FifaWebhookController::class, 'getSyncLogs']);
    Route::post('/sync', [FifaWebhookController::class, 'startSync']);
    Route::get('/connectivity/status', [FifaWebhookController::class, 'testConnectivity']);
    Route::post('/clear-cache', [FifaWebhookController::class, 'clearCache']);
    Route::post('/resolve-conflict/{logId}', [FifaWebhookController::class, 'resolveConflict']);
});

// API v1 routes
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Performance API routes - using api. prefix to avoid conflicts
    Route::apiResource('performances', PerformanceApiController::class)->names([
        'index' => 'api.performances.index',
        'store' => 'api.performances.store',
        'show' => 'api.performances.show',
        'update' => 'api.performances.update',
        'destroy' => 'api.performances.destroy',
    ]);
    Route::get('/performances/analytics', [PerformanceApiController::class, 'analytics'])->name('api.performances.analytics');
    Route::get('/performances/export', [PerformanceApiController::class, 'export'])->name('api.performances.export');
    Route::post('/performances/bulk-import', [PerformanceApiController::class, 'bulkImport'])->name('api.performances.bulk-import');
    Route::get('/performances/dashboard', [PerformanceApiController::class, 'dashboard'])->name('api.performances.dashboard');
    Route::get('/performances/compare', [PerformanceApiController::class, 'compare'])->name('api.performances.compare');
    Route::get('/performances/trends', [PerformanceApiController::class, 'trends'])->name('api.performances.trends');
    Route::post('/performances/generate-alerts', [PerformanceApiController::class, 'generateAlerts'])->name('api.performances.generate-alerts');
});

// DTN Manager API Routes
Route::middleware(['auth:sanctum'])->prefix('dtn')->group(function () {
    // National Teams
    Route::get('/teams', [DTNController::class, 'teams']);
    Route::post('/teams', [DTNController::class, 'storeTeam']);
    Route::get('/teams/{team}', [DTNController::class, 'showTeam']);
    Route::put('/teams/{team}', [DTNController::class, 'updateTeam']);
    Route::delete('/teams/{team}', [DTNController::class, 'destroyTeam']);
    
    // International Selections
    Route::get('/selections', [DTNController::class, 'selections']);
    Route::post('/selections', [DTNController::class, 'storeSelection']);
    Route::get('/selections/{selection}', [DTNController::class, 'showSelection']);
    Route::put('/selections/{selection}', [DTNController::class, 'updateSelection']);
    Route::delete('/selections/{selection}', [DTNController::class, 'destroySelection']);
    
    // Expatriate Players
    Route::get('/expats', [DTNController::class, 'expats']);
    Route::get('/expats/{player}', [DTNController::class, 'showExpat']);
    
    // Medical Interface
    Route::get('/medical/{playerId}', [DTNController::class, 'medicalData']);
    Route::post('/medical/{playerId}/feedback', [DTNController::class, 'sendFeedback']);
    
    // FIFA Connect Integration
    Route::post('/fifa/export', [DTNController::class, 'exportToFifa']);
    Route::get('/fifa/calendar', [DTNController::class, 'fifaCalendar']);
    

    
    // Reports
    Route::get('/reports', [DTNController::class, 'reports']);
    Route::post('/reports/generate', [DTNController::class, 'generateReport']);
});

// RPM API Routes
Route::middleware(['auth:sanctum'])->prefix('rpm')->group(function () {
    // Training Sessions
    Route::get('/sessions', [RPMController::class, 'sessions']);
    Route::post('/sessions', [RPMController::class, 'storeSession']);
    Route::get('/sessions/{session}', [RPMController::class, 'showSession']);
    Route::put('/sessions/{session}', [RPMController::class, 'updateSession']);
    Route::delete('/sessions/{session}', [RPMController::class, 'destroySession']);
    
    // Match Preparation
    Route::get('/matches', [RPMController::class, 'matches']);
    Route::post('/matches', [RPMController::class, 'storeMatch']);
    Route::get('/matches/{match}', [RPMController::class, 'showMatch']);
    Route::put('/matches/{match}', [RPMController::class, 'updateMatch']);
    Route::delete('/matches/{match}', [RPMController::class, 'destroyMatch']);
    
    // Player Load Monitoring
    Route::get('/load', [RPMController::class, 'playerLoads']);
    Route::get('/load/{playerId}', [RPMController::class, 'showPlayerLoad']);
    Route::post('/load/{playerId}', [RPMController::class, 'updatePlayerLoad']);
    
    // Attendance Tracking
    Route::get('/attendance', [RPMController::class, 'attendance']);
    Route::post('/attendance', [RPMController::class, 'storeAttendance']);
    Route::get('/attendance/{sessionId}', [RPMController::class, 'showAttendance']);
    
    // Performance Sync
    Route::post('/sync/performance', [RPMController::class, 'syncToPerformance']);
    Route::get('/sync/status', [RPMController::class, 'syncStatus']);
    
    // Calendar
    Route::get('/calendar', [RPMController::class, 'calendar']);
    Route::get('/calendar/week/{date}', [RPMController::class, 'weekCalendar']);
    
    // Reports
    Route::get('/reports', [RPMController::class, 'reports']);
    Route::post('/reports/generate', [RPMController::class, 'generateReport']);
});

// ClubBridge API Routes (for foreign clubs)
Route::middleware(['auth:sanctum'])->prefix('club')->group(function () {
    Route::get('/player/{fifaId}/medical', [App\Http\Controllers\Api\V1\ClubBridgeController::class, 'medicalData']);
    Route::get('/player/{fifaId}/trainingload', [App\Http\Controllers\Api\V1\ClubBridgeController::class, 'trainingLoad']);
    Route::post('/player/{fifaId}/feedback', [App\Http\Controllers\Api\V1\ClubBridgeController::class, 'sendFeedback']);
});

Route::get('/fit/kpis', [FitDashboardController::class, 'kpis']);

// FIFA TMS Integration (Public API)
Route::prefix('fifa-tms')->group(function () {
    Route::get('/connectivity', [App\Http\Controllers\Api\V1\FifaTmsController::class, 'testConnectivity']);
    Route::get('/player/{playerId}/licenses', [App\Http\Controllers\Api\V1\FifaTmsController::class, 'getPlayerLicenses']);
    Route::get('/player/{playerId}/transfers', [App\Http\Controllers\Api\V1\FifaTmsController::class, 'getPlayerTransferHistory']);
    Route::post('/player/{playerId}/sync', [App\Http\Controllers\Api\V1\FifaTmsController::class, 'syncPlayerData']);
});

// Route pour gérer les actions des clés automatiques
Route::post('/auto-key-action', [AutoKeyController::class, 'store'])->name('api.auto-key-action');

// Routes du Portail Athlète
Route::prefix('v1/portal')->middleware(['auth:sanctum', 'role:athlete'])->group(function () {
    Route::get('/dashboard-summary', [App\Http\Controllers\Api\PlayerPortalController::class, 'getDashboardSummary']);
    Route::get('/medical-record-summary', [App\Http\Controllers\Api\PlayerPortalController::class, 'getMedicalRecordSummary']);
    Route::post('/wellness-form', [App\Http\Controllers\Api\PlayerPortalController::class, 'submitWellnessForm']);
    Route::get('/wellness-history', [App\Http\Controllers\Api\PlayerPortalController::class, 'getWellnessHistory']);
    Route::get('/appointments', [App\Http\Controllers\Api\PlayerPortalController::class, 'getAppointments']);
    Route::get('/documents', [App\Http\Controllers\Api\PlayerPortalController::class, 'getDocuments']);
});

// Public demo route for doctor signoff
Route::get('/pcma/doctor-signoff-demo', function () {
    return view('pcma.doctor-signoff-demo', ['athlete' => null]);
})->name('pcma.doctor-signoff-demo');

// Routes pour le module dentaire
Route::prefix('dental')->group(function () {
    Route::get('/annotations', [App\Http\Controllers\DentalController::class, 'index']);
    Route::get('/annotations/{dentalAnnotation}', [App\Http\Controllers\DentalController::class, 'show']);
    Route::post('/annotations', [App\Http\Controllers\DentalController::class, 'store']);
    Route::put('/annotations/{dentalAnnotation}', [App\Http\Controllers\DentalController::class, 'update']);
    Route::delete('/annotations/{dentalAnnotation}', [App\Http\Controllers\DentalController::class, 'destroy']);
    
    // Routes spéciales
    Route::post('/save-all', [App\Http\Controllers\DentalController::class, 'saveAll']);
    Route::get('/stats', [App\Http\Controllers\DentalController::class, 'getStats']);
    Route::post('/reset', [App\Http\Controllers\DentalController::class, 'reset']);
});

// Routes API pour les joueurs (Portail Joueur)
Route::prefix('joueurs')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\JoueurController::class, 'index']);
    Route::get('/{id}', [App\Http\Controllers\Api\JoueurController::class, 'show']);
    Route::get('/fifa/{fifaId}', [App\Http\Controllers\Api\JoueurController::class, 'getByFifaId']);
    Route::get('/{id}/stats', [App\Http\Controllers\Api\JoueurController::class, 'getStats']);
    Route::get('/{id}/health', [App\Http\Controllers\Api\JoueurController::class, 'getHealthData']);
    Route::get('/{id}/notifications', [App\Http\Controllers\Api\JoueurController::class, 'getNotifications']);
});

// Routes API pour le portail dynamique
Route::prefix('portal')->group(function () {
    Route::get('/performance-data', function () {
        return response()->json([
            'radar' => [
                'labels' => ['Vitesse', 'Force', 'Endurance', 'Technique', 'Mental', 'Récupération'],
                'data' => [
                    rand(70, 95), rand(75, 90), rand(80, 95), 
                    rand(85, 98), rand(70, 90), rand(75, 95)
                ]
            ],
            'lineChart' => [
                'labels' => ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4', 'Sem 5', 'Sem 6'],
                'data' => [
                    rand(75, 85), rand(78, 88), rand(80, 90), 
                    rand(82, 92), rand(85, 95), rand(88, 98)
                ]
            ],
            'barChart' => [
                'labels' => ['Buts', 'Passes', 'Tacles', 'Interceptions'],
                'data' => [rand(15, 25), rand(20, 30), rand(80, 120), rand(40, 60)]
            ],
            'doughnutChart' => [
                'labels' => ['Victoires', 'Nuls', 'Défaites'],
                'data' => [rand(60, 80), rand(15, 25), rand(5, 20)]
            ]
        ]);
    });

    Route::get('/notifications', function () {
        $notifications = [
            [
                'id' => 1,
                'type' => 'performance',
                'title' => 'Nouveau record personnel !',
                'message' => 'Vous avez battu votre record de vitesse sur 100m',
                'date' => now()->subMinutes(rand(5, 120))->diffForHumans(),
                'status' => 'unread',
                'icon' => '🏃‍♂️',
                'color' => 'green'
            ],
            [
                'id' => 2,
                'type' => 'medical',
                'title' => 'Rappel contrôle médical',
                'message' => 'Votre contrôle de routine est prévu dans 3 jours',
                'date' => now()->subHours(rand(1, 6))->diffForHumans(),
                'status' => 'read',
                'icon' => '🏥',
                'color' => 'blue'
            ],
            [
                'id' => 3,
                'type' => 'training',
                'title' => 'Session d\'entraînement',
                'message' => 'Nouvelle session de musculation programmée',
                'date' => now()->subMinutes(rand(30, 180))->diffForHumans(),
                'status' => 'unread',
                'icon' => '💪',
                'color' => 'orange'
            ],
            [
                'id' => 4,
                'type' => 'doping',
                'title' => 'Contrôle antidopage',
                'message' => 'Contrôle surprise prévu ce soir',
                'date' => now()->subMinutes(rand(10, 60))->diffForHumans(),
                'status' => 'unread',
                'icon' => '🧪',
                'color' => 'red'
            ]
        ];
        
        return response()->json($notifications);
    });

    Route::get('/health-data', function () {
        return response()->json([
            'metrics' => [
                'heartRate' => rand(60, 85),
                'sleepQuality' => rand(70, 95),
                'stressLevel' => rand(20, 60),
                'recoveryScore' => rand(65, 90)
            ],
            'radar' => [
                'labels' => ['Sommeil', 'Nutrition', 'Hydratation', 'Récupération', 'Stress', 'Énergie'],
                'data' => [
                    rand(70, 95), rand(75, 90), rand(80, 95), 
                    rand(75, 90), rand(60, 85), rand(70, 90)
                ]
            ],
            'lineChart' => [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'data' => [
                    rand(20, 40), rand(25, 45), rand(30, 50), 
                    rand(25, 45), rand(20, 40), rand(15, 35), rand(10, 30)
                ]
            ]
        ]);
    });

    Route::get('/medical-data', function () {
        return response()->json([
            'alerts' => [
                [
                    'type' => 'warning',
                    'title' => 'Blessure Mineure',
                    'description' => 'Entorse légère cheville',
                    'icon' => '🚨',
                    'color' => 'red'
                ],
                [
                    'type' => 'info',
                    'title' => 'Contrôle Requis',
                    'description' => 'Bilan sanguin mensuel',
                    'icon' => '⚠️',
                    'color' => 'yellow'
                ],
                [
                    'type' => 'success',
                    'title' => 'En Forme',
                    'description' => 'Aptitude confirmée',
                    'icon' => '✅',
                    'color' => 'green'
                ]
            ],
            'charts' => [
                'lineChart' => [
                    'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    'data' => [
                        rand(0, 2), rand(0, 1), rand(0, 2), 
                        rand(0, 1), rand(0, 2), rand(0, 1)
                    ]
                ],
                'pieChart' => [
                    'labels' => ['Entorses', 'Fractures', 'Contusions', 'Fatigue'],
                    'data' => [
                        rand(30, 50), rand(10, 25), rand(20, 35), rand(15, 30)
                    ]
                ]
            ]
        ]);
    });

    Route::get('/devices-data', function () {
        return response()->json([
            'devices' => [
                [
                    'name' => 'Apple Watch',
                    'model' => 'Série 8 - 45mm',
                    'battery' => rand(60, 95),
                    'status' => 'online',
                    'icon' => '⌚',
                    'color' => 'green'
                ],
                [
                    'name' => 'iPhone 15 Pro',
                    'model' => '256GB - iOS 17.2',
                    'battery' => rand(40, 85),
                    'status' => 'online',
                    'icon' => '📱',
                    'color' => 'blue'
                ],
                [
                    'name' => 'AirPods Pro',
                    'model' => '2ème génération',
                    'battery' => rand(70, 100),
                    'status' => 'online',
                    'icon' => '🎧',
                    'color' => 'purple'
                ]
            ],
            'charts' => [
                'barChart' => [
                    'labels' => ['Apple Watch', 'iPhone', 'AirPods', 'iPad'],
                    'data' => [
                        rand(8, 16), rand(4, 10), rand(2, 6), rand(1, 4)
                    ]
                ],
                'pieChart' => [
                    'labels' => ['Social Media', 'Fitness', 'Communication', 'Divertissement'],
                    'data' => [
                        rand(30, 50), rand(20, 35), rand(15, 25), rand(10, 20)
                    ]
                ]
            ],
            'metrics' => [
                'steps' => rand(8000, 15000),
                'calories' => rand(400, 800),
                'distance' => rand(5, 12),
                'notifications' => rand(20, 50),
                'apps' => rand(8, 15),
                'screenTime' => rand(3, 8)
            ]
        ]);
    });

    Route::get('/doping-data', function () {
        return response()->json([
            'status' => [
                [
                    'type' => 'success',
                    'title' => 'Dernier Contrôle',
                    'description' => 'Négatif - ' . now()->subDays(rand(1, 30))->format('d/m/Y'),
                    'icon' => '✅',
                    'color' => 'green'
                ],
                [
                    'type' => 'info',
                    'title' => 'Prochain Contrôle',
                    'description' => now()->addDays(rand(5, 60))->format('d/m/Y'),
                    'icon' => '📋',
                    'color' => 'blue'
                ],
                [
                    'type' => 'warning',
                    'title' => 'Risque',
                    'description' => 'Faible - ' . rand(5, 15) . '%',
                    'icon' => '⚠️',
                    'color' => 'yellow'
                ]
            ],
            'charts' => [
                'lineChart' => [
                    'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    'data' => [
                        rand(2, 4), rand(1, 3), rand(2, 4), 
                        rand(1, 3), rand(2, 4), rand(1, 3)
                    ]
                ],
                'doughnutChart' => [
                    'labels' => ['Négatif', 'En attente', 'Positif'],
                    'data' => [
                        rand(80, 95), rand(5, 15), rand(0, 5)
                    ]
                ]
            ]
        ]);
    });
});
