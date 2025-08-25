<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RiskAlertResource;
use App\Models\RiskAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RiskAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = RiskAlert::with(['athlete']);

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by resolved status
        if ($request->has('resolved')) {
            $query->where('resolved', $request->boolean('resolved'));
        }

        // Filter by athlete
        if ($request->has('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $alerts = $query->paginate($perPage);

        return RiskAlertResource::collection($alerts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'type' => 'required|in:sca,injury,concussion,medication,other',
            'source' => 'required|string|max:100',
            'score' => 'required|numeric|between:0,1',
            'message' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high,critical',
            'recommendations' => 'nullable|array',
            'ai_metadata' => 'nullable|array',
            'fifa_alert_data' => 'nullable|array',
        ]);

        $alert = RiskAlert::create($validated);

        $alert->load(['athlete']);

        return response()->json([
            'message' => 'Risk alert created successfully',
            'data' => new RiskAlertResource($alert)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(RiskAlert $alert): JsonResponse
    {
        $alert->load(['athlete', 'acknowledgedBy']);

        return response()->json([
            'data' => new RiskAlertResource($alert)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiskAlert $alert): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:sca,injury,concussion,medication,other',
            'source' => 'sometimes|string|max:100',
            'score' => 'sometimes|numeric|between:0,1',
            'message' => 'sometimes|string|max:1000',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'resolved' => 'sometimes|boolean',
            'recommendations' => 'nullable|array',
            'ai_metadata' => 'nullable|array',
            'fifa_alert_data' => 'nullable|array',
        ]);

        $alert->update($validated);

        $alert->load(['athlete', 'acknowledgedBy']);

        return response()->json([
            'message' => 'Risk alert updated successfully',
            'data' => new RiskAlertResource($alert)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiskAlert $alert): JsonResponse
    {
        $alert->delete();

        return response()->json([
            'message' => 'Risk alert deleted successfully'
        ]);
    }

    /**
     * Acknowledge a risk alert.
     */
    public function acknowledge(Request $request, RiskAlert $alert): JsonResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'action_taken' => 'nullable|string|max:500',
        ]);

        $alert->update([
            'resolved' => true,
            'acknowledged_by' => auth()->id(),
            'acknowledged_at' => now(),
            'notes' => $validated['notes'] ?? $alert->notes,
            'action_taken' => $validated['action_taken'] ?? $alert->action_taken,
        ]);

        $alert->load(['athlete', 'acknowledgedBy']);

        return response()->json([
            'message' => 'Risk alert acknowledged successfully',
            'data' => new RiskAlertResource($alert)
        ]);
    }

    /**
     * Get all risk alerts for a specific athlete.
     */
    public function indexForAthlete(int $athleteId): AnonymousResourceCollection
    {
        $alerts = RiskAlert::where('athlete_id', $athleteId)
            ->with(['acknowledgedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return RiskAlertResource::collection($alerts);
    }

    /**
     * Get risk alert statistics.
     */
    public function statistics(): JsonResponse
    {
        $statistics = [
            'total_alerts' => RiskAlert::count(),
            'unresolved_alerts' => RiskAlert::where('resolved', false)->count(),
            'resolved_alerts' => RiskAlert::where('resolved', true)->count(),
            'by_type' => [
                'sca' => RiskAlert::where('type', 'sca')->count(),
                'injury' => RiskAlert::where('type', 'injury')->count(),
                'concussion' => RiskAlert::where('type', 'concussion')->count(),
                'medication' => RiskAlert::where('type', 'medication')->count(),
                'other' => RiskAlert::where('type', 'other')->count(),
            ],
            'by_priority' => [
                'critical' => RiskAlert::where('priority', 'critical')->count(),
                'high' => RiskAlert::where('priority', 'high')->count(),
                'medium' => RiskAlert::where('priority', 'medium')->count(),
                'low' => RiskAlert::where('priority', 'low')->count(),
            ],
            'unresolved_by_priority' => [
                'critical' => RiskAlert::where('priority', 'critical')->where('resolved', false)->count(),
                'high' => RiskAlert::where('priority', 'high')->where('resolved', false)->count(),
                'medium' => RiskAlert::where('priority', 'medium')->where('resolved', false)->count(),
                'low' => RiskAlert::where('priority', 'low')->where('resolved', false)->count(),
            ],
            'average_response_time' => $this->calculateAverageResponseTime(),
        ];

        return response()->json([
            'data' => $statistics
        ]);
    }

    /**
     * Calculate average response time for alerts.
     */
    private function calculateAverageResponseTime(): ?float
    {
        $resolvedAlerts = RiskAlert::where('resolved', true)
            ->whereNotNull('acknowledged_at')
            ->whereNotNull('created_at')
            ->get();

        if ($resolvedAlerts->isEmpty()) {
            return null;
        }

        $totalResponseTime = $resolvedAlerts->sum(function ($alert) {
            return $alert->acknowledged_at->diffInHours($alert->created_at);
        });

        return round($totalResponseTime / $resolvedAlerts->count(), 1);
    }

    /**
     * Bulk acknowledge alerts.
     */
    public function bulkAcknowledge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'alert_ids' => 'required|array',
            'alert_ids.*' => 'exists:risk_alerts,id',
            'notes' => 'nullable|string|max:1000',
            'action_taken' => 'nullable|string|max:500',
        ]);

        $alerts = RiskAlert::whereIn('id', $validated['alert_ids'])
            ->where('resolved', false)
            ->get();

        $updatedCount = 0;
        foreach ($alerts as $alert) {
            $alert->update([
                'resolved' => true,
                'acknowledged_by' => auth()->id(),
                'acknowledged_at' => now(),
                'notes' => $validated['notes'] ?? $alert->notes,
                'action_taken' => $validated['action_taken'] ?? $alert->action_taken,
            ]);
            $updatedCount++;
        }

        return response()->json([
            'message' => "Successfully acknowledged {$updatedCount} alerts",
            'data' => [
                'acknowledged_count' => $updatedCount,
                'total_requested' => count($validated['alert_ids']),
            ]
        ]);
    }

    /**
     * Get alerts by source (AI agent).
     */
    public function bySource(string $source): AnonymousResourceCollection
    {
        $alerts = RiskAlert::where('source', $source)
            ->with(['athlete', 'acknowledgedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return RiskAlertResource::collection($alerts);
    }
} 