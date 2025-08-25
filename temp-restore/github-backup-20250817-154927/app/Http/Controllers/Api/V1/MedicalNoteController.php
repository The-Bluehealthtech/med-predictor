<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalNoteRequest;
use App\Http\Resources\MedicalNoteResource;
use App\Models\MedicalNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MedicalNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MedicalNote::with(['athlete', 'approvedByPhysician']);

        // Filter by athlete
        if ($request->has('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        // Filter by note type
        if ($request->has('note_type')) {
            $query->where('note_type', $request->note_type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by AI generation
        if ($request->has('generated_by_ai')) {
            $query->where('generated_by_ai', $request->boolean('generated_by_ai'));
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
        $notes = $query->paginate($perPage);

        return MedicalNoteResource::collection($notes);
    }

    /**
     * Generate a draft medical note using AI.
     */
    public function generateDraft(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transcript' => 'required|string|max:10000',
            'athlete_id' => 'required|exists:athletes,id',
            'note_type' => 'required|string|max:100',
            'context' => 'nullable|array',
        ]);

        try {
            // Make request to AI microservice
            $aiResponse = Http::timeout(30)->post(config('services.ai.base_url') . '/ai/generate-note', [
                'transcript' => $validated['transcript'],
                'athlete_id' => $validated['athlete_id'],
                'note_type' => $validated['note_type'],
                'context' => $validated['context'] ?? [],
            ]);

            if ($aiResponse->successful()) {
                $aiData = $aiResponse->json();
                
                return response()->json([
                    'message' => 'Draft generated successfully',
                    'data' => [
                        'draft_content' => $aiData['content'] ?? '',
                        'metadata' => $aiData['metadata'] ?? [],
                        'confidence_score' => $aiData['confidence'] ?? 0,
                        'ai_model_version' => $aiData['model_version'] ?? 'unknown',
                        'generation_timestamp' => now()->toISOString(),
                    ]
                ]);
            } else {
                Log::error('AI service error', [
                    'status' => $aiResponse->status(),
                    'response' => $aiResponse->body(),
                ]);

                return response()->json([
                    'message' => 'AI service temporarily unavailable',
                    'error' => 'Unable to generate draft at this time'
                ], 503);
            }

        } catch (\Exception $e) {
            Log::error('Error calling AI service', [
                'error' => $e->getMessage(),
                'athlete_id' => $validated['athlete_id'],
            ]);

            return response()->json([
                'message' => 'Error generating draft',
                'error' => 'AI service communication failed'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicalNoteRequest $request): JsonResponse
    {
        $note = MedicalNote::create($request->validated());

        // Load relationships for response
        $note->load(['athlete', 'approvedByPhysician']);

        return response()->json([
            'message' => 'Medical note created successfully',
            'data' => new MedicalNoteResource($note)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalNote $note): JsonResponse
    {
        $note->load(['athlete', 'approvedByPhysician']);

        return response()->json([
            'data' => new MedicalNoteResource($note)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalNote $note): JsonResponse
    {
        $validated = $request->validate([
            'note_json' => 'sometimes|array',
            'note_type' => 'sometimes|string|max:100',
            'status' => 'sometimes|in:draft,approved,signed',
            'ai_metadata' => 'nullable|array',
            'fifa_compliance_data' => 'nullable|array',
        ]);

        $note->update($validated);

        $note->load(['athlete', 'approvedByPhysician']);

        return response()->json([
            'message' => 'Medical note updated successfully',
            'data' => new MedicalNoteResource($note)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalNote $note): JsonResponse
    {
        $note->delete();

        return response()->json([
            'message' => 'Medical note deleted successfully'
        ]);
    }

    /**
     * Get all medical notes for a specific athlete.
     */
    public function indexForAthlete(int $athleteId): AnonymousResourceCollection
    {
        $notes = MedicalNote::where('athlete_id', $athleteId)
            ->with(['approvedByPhysician'])
            ->orderBy('created_at', 'desc')
            ->get();

        return MedicalNoteResource::collection($notes);
    }

    /**
     * Sign a medical note (approve and sign).
     */
    public function sign(Request $request, MedicalNote $note): JsonResponse
    {
        $validated = $request->validate([
            'signature_data' => 'required|array',
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        $note->update([
            'status' => 'signed',
            'signed_at' => now(),
            'approved_by_physician_id' => auth()->id(),
            'approval_notes' => $validated['approval_notes'] ?? $note->approval_notes,
        ]);

        $note->load(['athlete', 'approvedByPhysician']);

        return response()->json([
            'message' => 'Medical note signed successfully',
            'data' => new MedicalNoteResource($note)
        ]);
    }

    /**
     * Get medical note statistics for an athlete.
     */
    public function statisticsForAthlete(int $athleteId): JsonResponse
    {
        $statistics = [
            'total_notes' => MedicalNote::where('athlete_id', $athleteId)->count(),
            'ai_generated_notes' => MedicalNote::where('athlete_id', $athleteId)
                ->where('generated_by_ai', true)
                ->count(),
            'signed_notes' => MedicalNote::where('athlete_id', $athleteId)
                ->where('status', 'signed')
                ->count(),
            'draft_notes' => MedicalNote::where('athlete_id', $athleteId)
                ->where('status', 'draft')
                ->count(),
            'by_type' => MedicalNote::where('athlete_id', $athleteId)
                ->selectRaw('note_type, COUNT(*) as count')
                ->groupBy('note_type')
                ->orderBy('count', 'desc')
                ->get(),
            'latest_note' => MedicalNote::where('athlete_id', $athleteId)
                ->latest('created_at')
                ->first(),
        ];

        return response()->json([
            'data' => $statistics
        ]);
    }

    /**
     * Get AI service health status.
     */
    public function aiHealthCheck(): JsonResponse
    {
        try {
            $response = Http::timeout(10)->get(config('services.ai.base_url') . '/health');
            
            return response()->json([
                'ai_service_status' => $response->successful() ? 'healthy' : 'unhealthy',
                'response_time' => $response->handlerStats()['total_time'] ?? null,
                'last_check' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ai_service_status' => 'unreachable',
                'error' => $e->getMessage(),
                'last_check' => now()->toISOString(),
            ], 503);
        }
    }
} 