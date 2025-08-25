<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAthleteRequest;
use App\Http\Resources\AthleteResource;
use App\Models\Athlete;
use App\Jobs\PushPatientToFHIR;
use App\Services\DICOMwebService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AthleteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Athlete::with(['team']);

        // Apply filters
        if ($request->has('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->has('nationality')) {
            $query->where('nationality', $request->nationality);
        }

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('fifa_id', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $athletes = $query->paginate($perPage);

        return response()->json([
            'data' => AthleteResource::collection($athletes),
            'current_page' => $athletes->currentPage(),
            'last_page' => $athletes->lastPage(),
            'per_page' => $athletes->perPage(),
            'total' => $athletes->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAthleteRequest $request): JsonResponse
    {
        $athlete = Athlete::create($request->validated());

        // Dispatch FHIR push job asynchronously
        PushPatientToFHIR::dispatch($athlete);

        return response()->json([
            'message' => 'Athlete created successfully',
            'data' => new AthleteResource($athlete)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Athlete $athlete): JsonResponse
    {
        $athlete->load(['team', 'pcmas', 'injuries', 'scatAssessments', 'tueRequests', 'medicalNotes', 'riskAlerts']);

        return response()->json([
            'data' => new AthleteResource($athlete)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Athlete $athlete): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'dob' => 'sometimes|date|before:today',
            'nationality' => 'sometimes|string|max:100',
            'team_id' => 'sometimes|exists:teams,id',
            'position' => 'nullable|string|max:50',
            'jersey_number' => 'nullable|integer|min:1|max:99',
            'gender' => 'sometimes|in:male,female,other',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'emergency_contact' => 'nullable|array',
            'medical_history' => 'nullable|array',
            'allergies' => 'nullable|array',
            'medications' => 'nullable|array',
            'active' => 'sometimes|boolean',
        ]);

        $athlete->update($validated);

        return response()->json([
            'message' => 'Athlete updated successfully',
            'data' => new AthleteResource($athlete)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Athlete $athlete): JsonResponse
    {
        $athlete->delete();

        return response()->json([
            'message' => 'Athlete deleted successfully'
        ]);
    }

    /**
     * Get medical dashboard data for an athlete.
     */
    public function medicalDashboard(Athlete $athlete): JsonResponse
    {
        $athlete->load([
            'team',
            'pcmas' => function ($query) {
                $query->latest('completed_at');
            },
            'injuries' => function ($query) {
                $query->where('status', 'open')->latest('date');
            },
            'scatAssessments' => function ($query) {
                $query->latest('assessment_date');
            },
            'tueRequests' => function ($query) {
                $query->latest('request_date');
            },
            'medicalNotes' => function ($query) {
                $query->latest('created_at');
            },
            'riskAlerts' => function ($query) {
                $query->where('resolved', false)->latest('created_at');
            }
        ]);

        // Calculate medical status
        $medicalStatus = [
            'has_active_injuries' => $athlete->injuries->isNotEmpty(),
            'has_pending_pcma' => $athlete->pcmas->where('status', 'pending')->isNotEmpty(),
            'has_unresolved_alerts' => $athlete->riskAlerts->isNotEmpty(),
        ];

        // Calculate health score (simplified)
        $healthScore = 100;
        if ($medicalStatus['has_active_injuries']) $healthScore -= 30;
        if ($medicalStatus['has_pending_pcma']) $healthScore -= 20;
        if ($medicalStatus['has_unresolved_alerts']) $healthScore -= 25;

        // FIFA compliance status
        $fifaCompliance = [
            'pcma_compliant' => $athlete->pcmas->where('status', 'completed')->isNotEmpty(),
            'tue_compliant' => $athlete->tueRequests->where('status', 'approved')->isNotEmpty(),
            'fully_compliant' => $athlete->pcmas->where('status', 'completed')->isNotEmpty() && 
                               $athlete->tueRequests->where('status', 'approved')->isNotEmpty(),
        ];

        return response()->json([
            'data' => [
                'athlete' => new AthleteResource($athlete),
                'medical_status' => $medicalStatus,
                'health_score' => max(0, $healthScore),
                'fifa_compliance' => $fifaCompliance,
                'recent_activities' => [
                    'pcmas' => $athlete->pcmas->take(5),
                    'injuries' => $athlete->injuries->take(5),
                    'alerts' => $athlete->riskAlerts->take(5),
                ]
            ]
        ]);
    }

    /**
     * Get health summary for an athlete.
     */
    public function getHealthSummary(Athlete $athlete): JsonResponse
    {
        // Get the most recent health score
        $currentScore = \App\Models\HealthScore::where('athlete_id', $athlete->id)
                                              ->latest('calculated_date')
                                              ->first();

        // Get historical scores for trend analysis (last 10 scores)
        $historicalScores = \App\Models\HealthScore::where('athlete_id', $athlete->id)
                                                  ->orderBy('calculated_date', 'desc')
                                                  ->limit(10)
                                                  ->get()
                                                  ->map(function ($score) {
                                                      return [
                                                          'date' => $score->calculated_date->toDateString(),
                                                          'score' => $score->score,
                                                          'trend' => $score->trend,
                                                          'grade' => $score->grade,
                                                          'status' => $score->status,
                                                      ];
                                                  });

        // Calculate average score over the last 30 days
        $averageScore = \App\Models\HealthScore::where('athlete_id', $athlete->id)
                                               ->where('calculated_date', '>=', now()->subDays(30))
                                               ->avg('score');

        // Get trend analysis
        $trendAnalysis = $this->analyzeHealthTrend($athlete->id);

        return response()->json([
            'data' => [
                'current_score' => $currentScore ? [
                    'score' => $currentScore->score,
                    'trend' => $currentScore->trend,
                    'trend_icon' => $currentScore->trend_icon,
                    'trend_text' => $currentScore->trend_text,
                    'grade' => $currentScore->grade,
                    'status' => $currentScore->status,
                    'color' => $currentScore->color,
                    'calculated_date' => $currentScore->calculated_date->toDateString(),
                    'contributing_factors' => $currentScore->contributing_factors,
                    'ai_analysis' => $currentScore->ai_analysis,
                ] : null,
                'historical_scores' => $historicalScores,
                'average_score_30_days' => round($averageScore, 1),
                'trend_analysis' => $trendAnalysis,
                'last_updated' => $currentScore?->updated_at?->toISOString(),
            ]
        ]);
    }

    /**
     * Analyze health trend for an athlete.
     */
    private function analyzeHealthTrend(int $athleteId): array
    {
        $scores = \App\Models\HealthScore::where('athlete_id', $athleteId)
                                        ->orderBy('calculated_date', 'desc')
                                        ->limit(7)
                                        ->pluck('score')
                                        ->toArray();

        if (count($scores) < 2) {
            return [
                'trend' => 'stable',
                'direction' => 'neutral',
                'change_rate' => 0,
                'volatility' => 'low',
            ];
        }

        $recent = array_slice($scores, 0, 3);
        $older = array_slice($scores, 3);

        if (empty($older)) {
            return [
                'trend' => 'stable',
                'direction' => 'neutral',
                'change_rate' => 0,
                'volatility' => 'low',
            ];
        }

        $recentAvg = array_sum($recent) / count($recent);
        $olderAvg = array_sum($older) / count($older);
        $changeRate = $recentAvg - $olderAvg;

        // Calculate volatility
        $allScores = array_merge($recent, $older);
        $mean = array_sum($allScores) / count($allScores);
        $variance = array_sum(array_map(function($score) use ($mean) {
            return pow($score - $mean, 2);
        }, $allScores)) / count($allScores);
        $volatility = sqrt($variance);

        return [
            'trend' => $changeRate > 5 ? 'improving' : ($changeRate < -5 ? 'worsening' : 'stable'),
            'direction' => $changeRate > 0 ? 'up' : ($changeRate < 0 ? 'down' : 'neutral'),
            'change_rate' => round($changeRate, 1),
            'volatility' => $volatility > 10 ? 'high' : ($volatility > 5 ? 'medium' : 'low'),
        ];
    }

    /**
     * Get imaging studies for an athlete.
     */
    public function getImagingStudies(Athlete $athlete): JsonResponse
    {
        try {
            $dicomService = new DICOMwebService();
            $result = $dicomService->getStudiesForPatient($athlete);

            if ($result['success']) {
                return response()->json([
                    'data' => [
                        'athlete' => new AthleteResource($athlete),
                        'studies' => $result['studies'],
                        'count' => $result['count'],
                        'pacs_connected' => true
                    ]
                ]);
            } else {
                return response()->json([
                    'data' => [
                        'athlete' => new AthleteResource($athlete),
                        'studies' => [],
                        'count' => 0,
                        'pacs_connected' => false,
                        'error' => $result['message']
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'data' => [
                    'athlete' => new AthleteResource($athlete),
                    'studies' => [],
                    'count' => 0,
                    'pacs_connected' => false,
                    'error' => 'Exception occurred while retrieving imaging studies: ' . $e->getMessage()
                ]
            ]);
        }
    }
} 