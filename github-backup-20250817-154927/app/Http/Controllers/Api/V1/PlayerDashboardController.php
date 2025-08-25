<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerDocument;
use App\Models\PlayerFitnessLog;
use App\Models\HealthRecord;
use App\Models\MedicalPrediction;
use App\Models\PlayerLicense;
use App\Models\GameMatch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlayerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('player.access');
    }

    /**
     * Get player profile and FIFA ID data
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $data = [
            'player' => [
                'id' => $player->id,
                'fifa_connect_id' => $player->fifaConnectId?->fifa_id,
                'first_name' => $player->first_name,
                'last_name' => $player->last_name,
                'full_name' => $player->full_name,
                'date_of_birth' => $player->date_of_birth,
                'nationality' => $player->nationality,
                'position' => $player->position,
                'jersey_number' => $player->jersey_number,
                'height' => $player->height,
                'weight' => $player->weight,
                'bmi' => $player->bmi,
                'bmi_category' => $player->bmi_category,
                'age' => $player->age,
                'overall_rating' => $player->overall_rating,
                'potential_rating' => $player->potential_rating,
                'value_eur' => $player->value_eur,
                'wage_eur' => $player->wage_eur,
                'contract_valid_until' => $player->contract_valid_until,
                'player_picture_url' => $player->player_picture_url,
                'has_picture' => $player->has_picture,
            ],
            'club' => $player->club ? [
                'id' => $player->club->id,
                'name' => $player->club->name,
                'logo_url' => $player->club->logo_url,
            ] : null,
            'association' => $player->association ? [
                'id' => $player->association->id,
                'name' => $player->association->name,
                'logo_url' => $player->association->logo_url,
            ] : null,
            'current_team' => $player->currentTeam ? [
                'id' => $player->currentTeam->team->id,
                'name' => $player->currentTeam->team->name,
                'squad_number' => $player->currentTeam->squad_number,
                'role' => $player->currentTeam->role,
            ] : null,
            'transfer_history' => $this->getTransferHistory($player),
            'licenses' => $this->getLicenses($player),
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Get performance dashboard data
     */
    public function performance(): JsonResponse
    {
        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $data = [
            'career_stats' => [
                'total_matches' => $player->total_matches_played,
                'total_goals' => $player->total_goals,
                'total_assists' => $player->total_assists,
                'total_minutes' => $player->total_minutes_played,
                'goals_per_match' => $player->goals_per_match,
                'assists_per_match' => $player->assists_per_match,
                'minutes_per_match' => $player->minutes_per_match,
            ],
            'current_season_stats' => $player->currentSeasonStats,
            'recent_performance' => $player->getRecentPerformanceStats(30),
            'recent_matches' => $player->recentMatchEvents,
            'fitness_logs' => $this->getFitnessLogs($player),
            'chart_data' => $this->getChartData($player),
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Get General Health Score (GHS) data
     */
    public function ghs(): JsonResponse
    {
        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $data = [
            'scores' => [
                'physical' => $player->ghs_physical_score,
                'mental' => $player->ghs_mental_score,
                'civic' => $player->ghs_civic_score,
                'sleep' => $player->ghs_sleep_score,
                'overall' => $player->ghs_overall_score,
            ],
            'color_code' => $player->ghs_color_code,
            'color_class' => $player->getGHSColorClass(),
            'ai_suggestions' => $player->ghs_ai_suggestions ?? [],
            'last_updated' => $player->ghs_last_updated,
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Update GHS scores
     */
    public function updateGHS(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'physical_score' => 'nullable|numeric|min:0|max:100',
            'mental_score' => 'nullable|numeric|min:0|max:100',
            'civic_score' => 'nullable|numeric|min:0|max:100',
            'sleep_score' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $player->update([
            'ghs_physical_score' => $request->physical_score,
            'ghs_mental_score' => $request->mental_score,
            'ghs_civic_score' => $request->civic_score,
            'ghs_sleep_score' => $request->sleep_score,
        ]);

        // Recalculate overall GHS
        $player->calculateGHS();
        
        // Generate AI suggestions
        $player->ghs_ai_suggestions = $this->generateGHSSuggestions($player);
        $player->save();

        return response()->json([
            'message' => 'GHS updated successfully',
            'data' => [
                'overall_score' => $player->ghs_overall_score,
                'color_code' => $player->ghs_color_code,
                'ai_suggestions' => $player->ghs_ai_suggestions,
            ]
        ]);
    }

    /**
     * Get health and fitness data
     */
    public function health(): JsonResponse
    {
        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $data = [
            'health_records' => $player->healthRecords()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'medical_predictions' => $player->medicalPredictions()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'fitness_logs' => $player->fitnessLogs()
                ->orderBy('log_date', 'desc')
                ->limit(10)
                ->get(),
            'clearances' => $this->getMedicalClearances($player),
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Get AI-powered injury risk assessment
     */
    public function injuryRisk(): JsonResponse
    {
        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        // Generate new assessment if needed (older than 7 days)
        if (!$player->injury_risk_last_assessed || 
            $player->injury_risk_last_assessed->diffInDays(now()) > 7) {
            $this->generateInjuryRiskAssessment($player);
        }

        $data = [
            'risk_score' => $player->injury_risk_score,
            'risk_level' => $player->injury_risk_level,
            'risk_reason' => $player->injury_risk_reason,
            'weekly_tips' => $player->weekly_health_tips ?? [],
            'last_assessed' => $player->injury_risk_last_assessed,
            'color_class' => $player->getInjuryRiskColorClass(),
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Get match sheet access data
     */
    public function matchSheet(): JsonResponse
    {
        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $data = [
            'availability' => $player->match_availability,
            'last_availability_update' => $player->last_availability_update,
            'upcoming_matches' => $player->getUpcomingMatches(10),
            'recent_matches' => $this->getRecentMatches($player),
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Update match availability
     */
    public function updateAvailability(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'availability' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $player->update([
            'match_availability' => $request->availability,
            'last_availability_update' => now(),
        ]);

        return response()->json([
            'message' => 'Availability updated successfully',
            'data' => [
                'availability' => $player->match_availability,
                'last_updated' => $player->last_availability_update,
            ]
        ]);
    }

    /**
     * Get licensing data
     */
    public function licensing(): JsonResponse
    {
        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $currentLicense = $player->getCurrentLicense();
        
        $data = [
            'current_license' => $currentLicense ? [
                'id' => $currentLicense->id,
                'license_number' => $currentLicense->license_number,
                'type' => $currentLicense->type,
                'status' => $currentLicense->status,
                'issued_date' => $currentLicense->issued_date,
                'expiry_date' => $currentLicense->expiry_date,
                'issued_by' => $currentLicense->issued_by,
                'days_until_expiry' => $currentLicense->expiry_date ? 
                    now()->diffInDays($currentLicense->expiry_date, false) : null,
                'is_expiring_soon' => $currentLicense->expiry_date ? 
                    $currentLicense->expiry_date->diffInDays(now(), false) <= 30 : false,
            ] : null,
            'all_licenses' => $player->playerLicenses()
                ->orderBy('issued_date', 'desc')
                ->get(),
            'download_url' => $currentLicense ? 
                route('api.v1.player-dashboard.license.download', $currentLicense->id) : null,
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Get data ownership information
     */
    public function dataOwnership(): JsonResponse
    {
        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        // Update contribution score and data value
        $player->updateContributionScore();
        $player->calculateDataValue();
        $player->save();

        $data = [
            'contribution_score' => $player->contribution_score,
            'data_value_estimate' => $player->data_value_estimate,
            'contribution_breakdown' => [
                'matches_contributed' => $player->matches_contributed,
                'training_sessions_logged' => $player->training_sessions_logged,
                'health_records_contributed' => $player->health_records_contributed,
            ],
            'export_info' => [
                'last_export' => $player->last_data_export,
                'export_count' => $player->data_export_count,
            ],
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Export player data
     */
    public function exportData(): JsonResponse
    {
        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        // Generate comprehensive data export
        $exportData = [
            'player_info' => [
                'id' => $player->id,
                'fifa_connect_id' => $player->fifaConnectId?->fifa_id,
                'personal_data' => [
                    'first_name' => $player->first_name,
                    'last_name' => $player->last_name,
                    'date_of_birth' => $player->date_of_birth,
                    'nationality' => $player->nationality,
                    'position' => $player->position,
                    'height' => $player->height,
                    'weight' => $player->weight,
                    'bmi' => $player->bmi,
                ],
                'performance_data' => [
                    'overall_rating' => $player->overall_rating,
                    'potential_rating' => $player->potential_rating,
                    'career_stats' => [
                        'total_matches' => $player->total_matches_played,
                        'total_goals' => $player->total_goals,
                        'total_assists' => $player->total_assists,
                        'total_minutes' => $player->total_minutes_played,
                    ],
                ],
                'health_data' => [
                    'ghs_scores' => [
                        'physical' => $player->ghs_physical_score,
                        'mental' => $player->ghs_mental_score,
                        'civic' => $player->ghs_civic_score,
                        'sleep' => $player->ghs_sleep_score,
                        'overall' => $player->ghs_overall_score,
                    ],
                    'injury_risk' => [
                        'score' => $player->injury_risk_score,
                        'level' => $player->injury_risk_level,
                        'reason' => $player->injury_risk_reason,
                    ],
                ],
            ],
            'health_records' => $player->healthRecords()->get(),
            'fitness_logs' => $player->fitnessLogs()->get(),
            'medical_predictions' => $player->medicalPredictions()->get(),
            'licenses' => $player->playerLicenses()->get(),
            'match_events' => $player->matchEvents()->get(),
            'export_metadata' => [
                'exported_at' => now()->toISOString(),
                'exported_by' => $user->id,
                'data_points' => $player->matches_contributed + $player->training_sessions_logged + $player->health_records_contributed,
            ],
        ];

        // Update export tracking
        $player->update([
            'last_data_export' => now(),
            'data_export_count' => $player->data_export_count + 1,
        ]);

        return response()->json([
            'message' => 'Data export generated successfully',
            'data' => $exportData,
            'download_url' => route('api.v1.player-dashboard.export.download'),
        ]);
    }

    /**
     * Get documents and media
     */
    public function documents(): JsonResponse
    {
        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $data = [
            'documents' => $player->documents()
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($doc) {
                    return [
                        'id' => $doc->id,
                        'type' => $doc->document_type,
                        'title' => $doc->title,
                        'description' => $doc->description,
                        'file_name' => $doc->file_name,
                        'file_size' => $doc->file_size_formatted,
                        'file_type' => $doc->file_type,
                        'mime_type' => $doc->mime_type,
                        'is_private' => $doc->is_private,
                        'expiry_date' => $doc->expiry_date,
                        'status' => $doc->status,
                        'is_expired' => $doc->isExpired(),
                        'download_url' => route('api.v1.player-dashboard.documents.download', $doc->id),
                        'icon' => $doc->getDocumentTypeIcon(),
                        'status_color' => $doc->getStatusColorClass(),
                    ];
                }),
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Upload document
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|string|in:medical_certificate,fitness_report,contract,license,medical_clearance,injury_report,training_certificate,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
            'is_private' => 'boolean',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $player = $this->getPlayerFromUser($user);
        
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $file = $request->file('file');
        $filePath = $file->store('player-documents/' . $player->id, 'public');

        $document = PlayerDocument::create([
            'player_id' => $player->id,
            'document_type' => $request->document_type,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'is_private' => $request->is_private ?? true,
            'expiry_date' => $request->expiry_date,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'data' => [
                'id' => $document->id,
                'title' => $document->title,
                'file_name' => $document->file_name,
                'download_url' => route('api.v1.player-dashboard.documents.download', $document->id),
            ]
        ]);
    }

    // Helper methods
    private function getPlayerFromUser($user): ?Player
    {
        // Find player by user's FIFA Connect ID or email
        return Player::where('fifa_connect_id', $user->fifa_connect_id)
            ->orWhere('email', $user->email)
            ->first();
    }

    private function getTransferHistory($player): array
    {
        // This would be implemented based on your transfer history model
        return [];
    }

    private function getLicenses($player): array
    {
        return $player->playerLicenses()
            ->orderBy('issued_date', 'desc')
            ->get()
            ->map(function($license) {
                return [
                    'id' => $license->id,
                    'license_number' => $license->license_number,
                    'type' => $license->type,
                    'status' => $license->status,
                    'issued_date' => $license->issued_date,
                    'expiry_date' => $license->expiry_date,
                    'issued_by' => $license->issued_by,
                ];
            })
            ->toArray();
    }

    private function getFitnessLogs($player): array
    {
        return $player->fitnessLogs()
            ->orderBy('log_date', 'desc')
            ->limit(7)
            ->get()
            ->toArray();
    }

    private function getChartData($player): array
    {
        // Generate chart data for performance visualization
        $last30Days = $player->fitnessLogs()
            ->where('log_date', '>=', now()->subDays(30))
            ->orderBy('log_date')
            ->get();

        return [
            'labels' => $last30Days->pluck('log_date')->map(fn($date) => $date->format('M d')),
            'datasets' => [
                [
                    'label' => 'Distance (km)',
                    'data' => $last30Days->pluck('distance_km'),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Calories',
                    'data' => $last30Days->pluck('calories_burned'),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ]
        ];
    }

    private function getMedicalClearances($player): array
    {
        return $player->documents()
            ->where('document_type', 'medical_clearance')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    private function getRecentMatches($player): array
    {
        return $player->matchRosters()
            ->with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->whereHas('match', function($query) {
                $query->where('match_date', '<', now());
            })
            ->orderBy('match_date', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function generateGHSSuggestions($player): array
    {
        $suggestions = [];
        
        if ($player->ghs_physical_score && $player->ghs_physical_score < 70) {
            $suggestions[] = 'Consider increasing physical training intensity gradually';
        }
        
        if ($player->ghs_sleep_score && $player->ghs_sleep_score < 70) {
            $suggestions[] = 'Focus on improving sleep quality - aim for 7-9 hours per night';
        }
        
        if ($player->ghs_mental_score && $player->ghs_mental_score < 70) {
            $suggestions[] = 'Practice stress management techniques and mental wellness exercises';
        }
        
        if (empty($suggestions)) {
            $suggestions[] = 'Maintain current healthy lifestyle practices';
        }
        
        return $suggestions;
    }

    private function generateInjuryRiskAssessment($player): void
    {
        // Simple ML-like assessment based on various factors
        $riskFactors = [];
        $riskScore = 0.1; // Base risk
        
        // Check recent injuries
        $recentInjuries = $player->healthRecords()
            ->where('record_type', 'injury_report')
            ->where('created_at', '>=', now()->subMonths(6))
            ->count();
        
        if ($recentInjuries > 2) {
            $riskFactors[] = 'Multiple recent injuries';
            $riskScore += 0.3;
        } elseif ($recentInjuries > 0) {
            $riskFactors[] = 'Recent injury history';
            $riskScore += 0.2;
        }
        
        // Check GHS scores
        if ($player->ghs_physical_score && $player->ghs_physical_score < 60) {
            $riskFactors[] = 'Low physical health score';
            $riskScore += 0.2;
        }
        
        if ($player->ghs_sleep_score && $player->ghs_sleep_score < 60) {
            $riskFactors[] = 'Poor sleep quality';
            $riskScore += 0.15;
        }
        
        // Check age factor
        if ($player->age > 30) {
            $riskFactors[] = 'Age-related risk factor';
            $riskScore += 0.1;
        }
        
        // Determine risk level
        $riskLevel = match(true) {
            $riskScore >= 0.7 => 'high',
            $riskScore >= 0.4 => 'medium',
            default => 'low'
        };
        
        $player->update([
            'injury_risk_score' => min(1.0, $riskScore),
            'injury_risk_level' => $riskLevel,
            'injury_risk_reason' => implode(', ', $riskFactors) ?: 'No significant risk factors identified',
            'weekly_health_tips' => $player->generateWeeklyHealthTips(),
            'injury_risk_last_assessed' => now(),
        ]);
    }
} 