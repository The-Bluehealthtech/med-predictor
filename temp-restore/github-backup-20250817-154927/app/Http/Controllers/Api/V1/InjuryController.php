<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInjuryRequest;
use App\Http\Resources\InjuryResource;
use App\Models\Athlete;
use App\Models\Injury;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Http;

class InjuryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Injury::with(['athlete', 'diagnosedBy']);

        // Filter by athlete
        if ($request->has('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by severity
        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by body zone
        if ($request->has('body_zone')) {
            $query->where('body_zone', 'like', '%' . $request->body_zone . '%');
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $injuries = $query->paginate($perPage);

        return InjuryResource::collection($injuries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInjuryRequest $request): JsonResponse
    {
        $injury = Injury::create($request->validated());

        // Load relationships for response
        $injury->load(['athlete', 'diagnosedBy']);

        return response()->json([
            'message' => 'Injury recorded successfully',
            'data' => new InjuryResource($injury)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Injury $injury): JsonResponse
    {
        $injury->load(['athlete', 'diagnosedBy']);

        return response()->json([
            'data' => new InjuryResource($injury)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Injury $injury): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'sometimes|string|max:100',
            'body_zone' => 'sometimes|string|max:100',
            'severity' => 'sometimes|in:minor,moderate,severe',
            'description' => 'sometimes|string|max:1000',
            'status' => 'sometimes|in:open,resolved,recurring',
            'estimated_recovery_days' => 'nullable|integer|min:1|max:365',
            'expected_return_date' => 'nullable|date|after:today',
            'actual_return_date' => 'nullable|date|before_or_equal:today',
            'diagnosed_by' => 'nullable|exists:users,id',
            'treatment_plan' => 'nullable|array',
            'rehabilitation_progress' => 'nullable|array',
            'fifa_injury_data' => 'nullable|array',
        ]);

        $injury->update($validated);

        $injury->load(['athlete', 'diagnosedBy']);

        return response()->json([
            'message' => 'Injury updated successfully',
            'data' => new InjuryResource($injury)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Injury $injury): JsonResponse
    {
        $injury->delete();

        return response()->json([
            'message' => 'Injury deleted successfully'
        ]);
    }

    /**
     * Get all injury records for a specific athlete.
     */
    public function indexForAthlete(Athlete $athlete): AnonymousResourceCollection
    {
        $injuries = $athlete->injuries()
            ->with(['diagnosedBy'])
            ->orderBy('date', 'desc')
            ->get();

        return InjuryResource::collection($injuries);
    }

    /**
     * Get injury statistics for an athlete.
     */
    public function statisticsForAthlete(Athlete $athlete): JsonResponse
    {
        $statistics = [
            'total_injuries' => $athlete->injuries()->count(),
            'active_injuries' => $athlete->injuries()->where('status', 'open')->count(),
            'resolved_injuries' => $athlete->injuries()->where('status', 'resolved')->count(),
            'recurring_injuries' => $athlete->injuries()->where('status', 'recurring')->count(),
            'by_severity' => [
                'minor' => $athlete->injuries()->where('severity', 'minor')->count(),
                'moderate' => $athlete->injuries()->where('severity', 'moderate')->count(),
                'severe' => $athlete->injuries()->where('severity', 'severe')->count(),
            ],
            'by_body_zone' => $athlete->injuries()
                ->selectRaw('body_zone, COUNT(*) as count')
                ->groupBy('body_zone')
                ->orderBy('count', 'desc')
                ->get(),
            'latest_injury' => $athlete->injuries()
                ->latest('date')
                ->first(),
            'longest_recovery' => $athlete->injuries()
                ->whereNotNull('estimated_recovery_days')
                ->orderBy('estimated_recovery_days', 'desc')
                ->first(),
        ];

        return response()->json([
            'data' => $statistics
        ]);
    }

    /**
     * Resolve an injury.
     */
    public function resolve(Request $request, Injury $injury): JsonResponse
    {
        $validated = $request->validate([
            'actual_return_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $injury->update([
            'status' => 'resolved',
            'actual_return_date' => $validated['actual_return_date'],
            'notes' => $validated['notes'] ?? $injury->notes,
        ]);

        $injury->load(['athlete', 'diagnosedBy']);

        return response()->json([
            'message' => 'Injury resolved successfully',
            'data' => new InjuryResource($injury)
        ]);
    }

    /**
     * Update rehabilitation progress.
     */
    public function updateProgress(Request $request, Injury $injury): JsonResponse
    {
        $validated = $request->validate([
            'rehabilitation_progress' => 'required|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        $injury->update([
            'rehabilitation_progress' => $validated['rehabilitation_progress'],
            'notes' => $validated['notes'] ?? $injury->notes,
        ]);

        $injury->load(['athlete', 'diagnosedBy']);

        return response()->json([
            'message' => 'Rehabilitation progress updated successfully',
            'data' => new InjuryResource($injury)
        ]);
    }

    /**
     * Validate return to play for an injury using AI.
     */
    public function validateReturnToPlay(Injury $injury): JsonResponse
    {
        try {
            // Load athlete data
            $injury->load(['athlete']);

            // Calculate days in rehab
            $daysInRehab = $injury->date->diffInDays(now());

            // Prepare rehab data
            $rehabData = [
                'treatment_plan' => $injury->treatment_plan ?? [],
                'rehabilitation_progress' => $injury->rehabilitation_progress ?? [],
                'estimated_recovery_days' => $injury->estimated_recovery_days,
                'expected_return_date' => $injury->expected_return_date,
                'actual_return_date' => $injury->actual_return_date,
            ];

            // Prepare performance metrics (mock data for now)
            $performanceMetrics = [
                'strength_tests' => [
                    'pre_injury' => 100,
                    'current' => 85,
                    'target' => 95,
                ],
                'mobility_tests' => [
                    'pre_injury' => 100,
                    'current' => 90,
                    'target' => 95,
                ],
                'functional_tests' => [
                    'pre_injury' => 100,
                    'current' => 88,
                    'target' => 95,
                ],
            ];

            // Prepare request data for AI service
            $requestData = [
                'injuryType' => $injury->type,
                'daysInRehab' => $daysInRehab,
                'rehabData' => $rehabData,
                'performanceMetrics' => $performanceMetrics,
                'athleteId' => $injury->athlete_id,
                'injuryId' => $injury->id,
            ];

            // Make request to AI service
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.ai.token'),
                    'Content-Type' => 'application/json',
                ])
                ->post(config('services.ai.base_url') . '/ai/rtp-validator', $requestData);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'AI service unavailable',
                    'message' => 'Unable to validate return to play at this time'
                ], 503);
            }

            $aiResponse = $response->json();

            return response()->json([
                'data' => [
                    'injury' => new InjuryResource($injury),
                    'rtp_validation' => [
                        'status' => $aiResponse['status'] ?? 'Unknown',
                        'confidence' => $aiResponse['confidence'] ?? 0,
                        'recommendation' => $aiResponse['recommendation'] ?? 'No recommendation available',
                        'risk_assessment' => $aiResponse['risk_assessment'] ?? null,
                        'next_steps' => $aiResponse['next_steps'] ?? [],
                        'validation_date' => now()->toISOString(),
                        'days_in_rehab' => $daysInRehab,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'Error validating return to play: ' . $e->getMessage()
            ], 500);
        }
    }
} 