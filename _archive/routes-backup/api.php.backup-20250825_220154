<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeagueChampionshipController;
use App\Http\Controllers\RefereeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CalendarManagementController;
use App\Http\Controllers\Api\V1\PerformanceApiController;
use App\Http\Controllers\FitDashboardController;
use App\Models\Competition;
use App\Http\Controllers\Api\V1\PCMAController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\ImmunisationController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransferPaymentController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\FederationController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\GoogleAssistantController;
use Illuminate\Support\Facades\DB;

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
        });
        
        // Analytics routes
        Route::prefix('analytics')->group(function () {
        });
        
        // Player routes
        Route::prefix('players')->group(function () {
        });
        
        // Club routes
        Route::prefix('clubs')->group(function () {
        });
        
        // Team routes
        Route::prefix('teams')->group(function () {
        });
        
        // Match routes
        Route::prefix('matches')->group(function () {
        });
        
        // Season routes
        Route::prefix('seasons')->group(function () {
        });
        
        // User routes
        Route::prefix('users')->group(function () {
        });
        
        // Player License routes
        Route::prefix('player-licenses')->group(function () {
        });
        
        // Match Event routes
        Route::prefix('match-events')->group(function () {
        });
        
        // Match-specific event routes
        Route::prefix('matches/{match}/events')->group(function () {
        });
        
        // Team-specific event routes
        Route::prefix('teams/{team}/events')->group(function () {
        });
        
        // Player-specific event routes
        Route::prefix('players/{player}/events')->group(function () {
        });
        
        // Match Sheet routes
        Route::prefix('match-sheets')->group(function () {
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
        });

        // Athlete Injury routes
        Route::prefix('athletes/{athlete}/injuries')->group(function () {
        });

        // SCAT Assessment routes
        Route::prefix('scat-assessments')->group(function () {
        });

        // Athlete SCAT Assessment routes
        Route::prefix('athletes/{athlete}/scat-assessments')->group(function () {
        });

        // Medical Note routes
        Route::prefix('medical-notes')->group(function () {
        });

        // Medical Note AI routes
        Route::prefix('medical-notes/ai')->group(function () {
        });

        // Athlete Medical Note routes
        Route::prefix('athletes/{athlete}/medical-notes')->group(function () {
        });

        // Dashboard routes
        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::get('/alerts-summary', [DashboardController::class, 'alertsSummary']);
            Route::get('/quick-actions', [DashboardController::class, 'quickActions']);
        });

        // Risk Alert routes
        Route::prefix('risk-alerts')->group(function () {
        });

        Route::prefix('risk-alerts')->group(function () {
        });

        // Athlete Risk Alert routes
        Route::prefix('athletes/{athlete}/risk-alerts')->group(function () {
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
});

// Competition API routes for Vue.js components
Route::middleware(['auth:sanctum'])->prefix('competitions')->name('competitions.')->group(function () {
});

// Match API routes for Vue.js components
Route::middleware(['auth:sanctum'])->prefix('matches')->name('matches.')->group(function () {
});

// Player Dashboard API Routes
Route::middleware(['auth:sanctum', 'player.access'])->prefix('v1/player-dashboard')->name('api.v1.player-dashboard.')->group(function () {
    // Profile and FIFA ID
    
    // Performance Dashboard
    
    // General Health Score (GHS)
    
    // Health & Fitness
    
    // AI-Powered Injury Risk
    
    // Match Sheet Access
    
    // Licensing
    
    // Data Ownership
    
    // Documents & Media
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

// FIFA Sync Management Routes (protected)
Route::middleware(['web', 'auth'])->prefix('fifa')->group(function () {
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
    
    // International Selections
    
    // Expatriate Players
    
    // Medical Interface
    
    // FIFA Connect Integration
    

    
    // Reports
});

// RPM API Routes
Route::middleware(['auth:sanctum'])->prefix('rpm')->group(function () {
    // Training Sessions
    
    // Match Preparation
    
    // Player Load Monitoring
    
    // Attendance Tracking
    
    // Performance Sync
    
    // Calendar
    
    // Reports
});

// ClubBridge API Routes (for foreign clubs)
Route::middleware(['auth:sanctum'])->prefix('club')->group(function () {
});

Route::get('/fit/kpis', [FitDashboardController::class, 'kpis']);

// FIFA TMS Integration (Public API)
Route::prefix('fifa-tms')->group(function () {
});

// Route pour gérer les actions des clés automatiques

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

// =============================================================================
// FIT V3 - Routes API avec Intelligence Artificielle
// =============================================================================
require __DIR__.'/api-v3.php';

// =============================================================================
// Google Assistant Routes (API sans CSRF)
// =============================================================================
require __DIR__.'/google-assistant.php';

// Google Assistant routes
Route::prefix('google-assistant')->group(function () {
    Route::post('/webhook', [GoogleAssistantController::class, 'handleIntent']);
    Route::post('/submit-pcma', [GoogleAssistantController::class, 'submitPcmaToFit']);
    Route::get('/fit-health', [GoogleAssistantController::class, 'checkFitApiHealth']);
    Route::get('/health', [GoogleAssistantController::class, 'health']);
});

// Route pour récupérer les données de session PCMA
Route::get('/google-assistant/session/{sessionId}', [GoogleAssistantController::class, 'getSessionData']);

// Route pour récupérer les données de session PCMA
Route::get('/google-assistant/session/{sessionId}', [GoogleAssistantController::class, 'getSessionData']);

// Route pour la recherche de joueurs (renommée pour éviter les conflits)
Route::get('/athletes/search', function (Request $request) {
    $name = $request->query('name');
    
    if (!$name) {
        return response()->json([
            'success' => false,
            'message' => 'Nom du joueur requis'
        ], 400);
    }
    
    try {
        // Log de débogage
        \Log::info("🔍 Recherche du joueur: '$name'");
        
        // Rechercher dans la table players (structure complète avec first_name, last_name, fifa_connect_id, etc.)
        $query = DB::table('players');
        
        // Recherche exacte d'abord
        $player = $query->where('name', $name)
            ->orWhere('first_name', $name)
            ->orWhere('last_name', $name)
            ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), $name)
            ->first();
        
        // Si pas trouvé, recherche partielle
        if (!$player) {
            \Log::info("🔍 Recherche exacte échouée, tentative de recherche partielle...");
            
            $player = $query->where('name', 'LIKE', '%' . $name . '%')
                ->orWhere('first_name', 'LIKE', '%' . $name . '%')
                ->orWhere('last_name', 'LIKE', '%' . $name . '%')
                ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%' . $name . '%')
                ->orWhere(DB::raw("CONCAT(last_name, ' ', first_name)"), 'LIKE', '%' . $name . '%')
                ->first();
        }
        
        // Log du résultat
        if ($player) {
            \Log::info("✅ Joueur trouvé:", [
                'id' => $player->id,
                'name' => $player->name,
                'first_name' => $player->first_name ?? 'N/A',
                'last_name' => $player->last_name ?? 'N/A'
            ]);
            
            return response()->json([
                'success' => true,
                'player' => [
                    'id' => $player->id,
                    'name' => $player->name ?: ($player->first_name . ' ' . $player->last_name),
                    'fifa_connect_id' => $player->fifa_connect_id,
                    'club' => $player->current_club_id ? 'Club ID: ' . $player->current_club_id : null,
                    'position' => $player->position,
                    'age' => $player->age ?: ($player->birth_date ? \Carbon\Carbon::parse($player->birth_date)->age : null),
                    'nationality' => $player->nationality,
                    'jersey_number' => $player->jersey_number,
                    'overall_rating' => $player->overall_rating,
                    'potential_rating' => $player->potential_rating
                ]
            ]);
        } else {
            \Log::info("❌ Joueur non trouvé dans la base");
            
            // Retourner plus d'informations pour le débogage
            return response()->json([
                'success' => false,
                'message' => 'Joueur non trouvé',
                'debug' => [
                    'searched_name' => $name,
                    'search_type' => 'exact_and_partial'
                ]
            ]);
        }
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la recherche: ' . $e->getMessage()
        ], 500);
    }
});
