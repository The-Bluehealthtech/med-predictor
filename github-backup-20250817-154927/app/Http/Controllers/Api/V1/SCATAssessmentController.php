<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSCATRequest;
use App\Http\Resources\SCATAssessmentResource;
use App\Models\Athlete;
use App\Models\SCATAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SCATAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = SCATAssessment::with(['athlete', 'assessor']);

        // Filter by athlete
        if ($request->has('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        // Filter by result
        if ($request->has('result')) {
            $query->where('result', $request->result);
        }

        // Filter by concussion confirmation
        if ($request->has('concussion_confirmed')) {
            $query->where('concussion_confirmed', $request->boolean('concussion_confirmed'));
        }

        // Filter by assessment type
        if ($request->has('assessment_type')) {
            $query->where('assessment_type', $request->assessment_type);
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('assessment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('assessment_date', '<=', $request->date_to);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'assessment_date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $assessments = $query->paginate($perPage);

        return SCATAssessmentResource::collection($assessments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSCATRequest $request): JsonResponse
    {
        $assessment = SCATAssessment::create($request->validated());

        // Load relationships for response
        $assessment->load(['athlete', 'assessor']);

        return response()->json([
            'message' => 'SCAT assessment recorded successfully',
            'data' => new SCATAssessmentResource($assessment)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SCATAssessment $assessment): JsonResponse
    {
        $assessment->load(['athlete', 'assessor']);

        return response()->json([
            'data' => new SCATAssessmentResource($assessment)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SCATAssessment $assessment): JsonResponse
    {
        $validated = $request->validate([
            'data_json' => 'sometimes|array',
            'result' => 'sometimes|in:normal,abnormal,unclear',
            'concussion_confirmed' => 'sometimes|boolean',
            'assessment_date' => 'sometimes|date|before_or_equal:today',
            'assessment_type' => 'sometimes|in:baseline,post_injury,follow_up',
            'scat_score' => 'nullable|integer|min:0|max:132',
            'recommendations' => 'nullable|string|max:1000',
            'fifa_concussion_data' => 'nullable|array',
        ]);

        $assessment->update($validated);

        $assessment->load(['athlete', 'assessor']);

        return response()->json([
            'message' => 'SCAT assessment updated successfully',
            'data' => new SCATAssessmentResource($assessment)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SCATAssessment $assessment): JsonResponse
    {
        $assessment->delete();

        return response()->json([
            'message' => 'SCAT assessment deleted successfully'
        ]);
    }

    /**
     * Get all SCAT assessment records for a specific athlete.
     */
    public function indexForAthlete(Athlete $athlete): AnonymousResourceCollection
    {
        $assessments = $athlete->scatAssessments()
            ->with(['assessor'])
            ->orderBy('assessment_date', 'desc')
            ->get();

        return SCATAssessmentResource::collection($assessments);
    }

    /**
     * Get SCAT assessment statistics for an athlete.
     */
    public function statisticsForAthlete(Athlete $athlete): JsonResponse
    {
        $statistics = [
            'total_assessments' => $athlete->scatAssessments()->count(),
            'baseline_assessments' => $athlete->scatAssessments()->where('assessment_type', 'baseline')->count(),
            'post_injury_assessments' => $athlete->scatAssessments()->where('assessment_type', 'post_injury')->count(),
            'follow_up_assessments' => $athlete->scatAssessments()->where('assessment_type', 'follow_up')->count(),
            'by_result' => [
                'normal' => $athlete->scatAssessments()->where('result', 'normal')->count(),
                'abnormal' => $athlete->scatAssessments()->where('result', 'abnormal')->count(),
                'unclear' => $athlete->scatAssessments()->where('result', 'unclear')->count(),
            ],
            'concussions_confirmed' => $athlete->scatAssessments()->where('concussion_confirmed', true)->count(),
            'latest_assessment' => $athlete->scatAssessments()
                ->latest('assessment_date')
                ->first(),
            'baseline_assessment' => $athlete->scatAssessments()
                ->where('assessment_type', 'baseline')
                ->latest('assessment_date')
                ->first(),
            'average_scat_score' => $athlete->scatAssessments()
                ->whereNotNull('scat_score')
                ->avg('scat_score'),
        ];

        return response()->json([
            'data' => $statistics
        ]);
    }

    /**
     * Compare assessment with baseline.
     */
    public function compareWithBaseline(SCATAssessment $assessment): JsonResponse
    {
        $baseline = $assessment->athlete->scatAssessments()
            ->where('assessment_type', 'baseline')
            ->latest('assessment_date')
            ->first();

        if (!$baseline) {
            return response()->json([
                'message' => 'No baseline assessment found for comparison',
                'data' => null
            ], 404);
        }

        $comparison = [
            'current_assessment' => new SCATAssessmentResource($assessment),
            'baseline_assessment' => new SCATAssessmentResource($baseline),
            'score_difference' => $assessment->scat_score && $baseline->scat_score 
                ? $assessment->scat_score - $baseline->scat_score 
                : null,
            'significant_change' => $assessment->scat_score && $baseline->scat_score 
                ? abs($assessment->scat_score - $baseline->scat_score) > 5 
                : null,
        ];

        return response()->json([
            'data' => $comparison
        ]);
    }

    /**
     * Get concussion timeline for an athlete.
     */
    public function concussionTimeline(Athlete $athlete): JsonResponse
    {
        $concussions = $athlete->scatAssessments()
            ->where('concussion_confirmed', true)
            ->with(['assessor'])
            ->orderBy('assessment_date', 'desc')
            ->get();

        $timeline = $concussions->map(function ($assessment) {
            return [
                'date' => $assessment->assessment_date,
                'type' => $assessment->assessment_type,
                'result' => $assessment->result,
                'scat_score' => $assessment->scat_score,
                'assessor' => $assessment->assessor?->name,
                'recommendations' => $assessment->recommendations,
            ];
        });

        return response()->json([
            'data' => [
                'total_concussions' => $concussions->count(),
                'timeline' => $timeline,
            ]
        ]);
    }
} 