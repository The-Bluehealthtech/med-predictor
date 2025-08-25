<?php

namespace App\Http\Controllers;

use App\Models\PosturalAssessment;
use App\Models\Player;
use App\Services\PosturalAssessmentService;
use App\Services\PosturalReportService;
use App\Services\PosturalAIAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PosturalAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $assessments = PosturalAssessment::with(['player', 'clinician'])
            ->orderBy('assessment_date', 'desc')
            ->paginate(15);

        return view('postural-assessments.index', compact('assessments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $players = Player::orderBy('name')->get();
        return view('postural-assessments.create', compact('players'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'assessment_type' => 'required|in:routine,injury,follow_up',
            'view' => 'required|in:anterior,posterior,lateral',
            'annotations' => 'nullable|array',
            'markers' => 'nullable|array',
            'angles' => 'nullable|array',
            'clinical_notes' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'assessment_date' => 'required|date',
        ]);

        $validated['user_id'] = auth()->id();

        $assessment = PosturalAssessment::create($validated);

        return redirect()->route('postural-assessments.show', $assessment)
            ->with('success', 'Évaluation posturale créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PosturalAssessment $assessment): View
    {
        $assessment->load(['player', 'clinician']);
        return view('postural-assessments.show', compact('assessment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PosturalAssessment $assessment): View
    {
        $players = Player::orderBy('name')->get();
        return view('postural-assessments.edit', compact('assessment', 'players'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosturalAssessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'assessment_type' => 'required|in:routine,injury,follow_up',
            'view' => 'required|in:anterior,posterior,lateral',
            'annotations' => 'nullable|array',
            'markers' => 'nullable|array',
            'angles' => 'nullable|array',
            'clinical_notes' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'assessment_date' => 'required|date',
        ]);

        $assessment->update($validated);

        return redirect()->route('postural-assessments.show', $assessment)
            ->with('success', 'Évaluation posturale mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosturalAssessment $assessment): RedirectResponse
    {
        $assessment->delete();

        return redirect()->route('postural-assessments.index')
            ->with('success', 'Évaluation posturale supprimée avec succès.');
    }

    /**
     * Save session data from the Vue component.
     */
    public function saveSession(Request $request, PosturalAssessment $assessment): JsonResponse
    {
        $validated = $request->validate([
            'view' => 'required|in:anterior,posterior,lateral',
            'annotations' => 'nullable|array',
            'markers' => 'nullable|array',
            'angles' => 'nullable|array',
        ]);

        $assessment->setSessionData($validated);

        return response()->json([
            'success' => true,
            'message' => 'Session sauvegardée avec succès',
            'assessment' => $assessment->fresh()
        ]);
    }

    /**
     * Get session data for the Vue component.
     */
    public function getSessionData(PosturalAssessment $assessment): JsonResponse
    {
        return response()->json([
            'sessionData' => $assessment->session_data
        ]);
    }

    /**
     * Get assessments for a specific player.
     */
    public function playerAssessments(Player $player): View
    {
        $assessments = $player->posturalAssessments()
            ->with('clinician')
            ->orderBy('assessment_date', 'desc')
            ->paginate(10);

        return view('postural-assessments.player', compact('player', 'assessments'));
    }

    /**
     * Export assessment data.
     */
    public function export(PosturalAssessment $assessment): JsonResponse
    {
        $data = [
            'player' => $assessment->player->only(['name', 'fifa_connect_id']),
            'clinician' => $assessment->clinician->name,
            'assessment_date' => $assessment->assessment_date->format('Y-m-d H:i:s'),
            'type' => $assessment->type_label,
            'view' => $assessment->view_label,
            'summary' => $assessment->summary,
            'clinical_notes' => $assessment->clinical_notes,
            'recommendations' => $assessment->recommendations,
            'annotations' => $assessment->annotations,
            'markers' => $assessment->markers,
            'angles' => $assessment->angles,
        ];

        return response()->json($data);
    }

    /**
     * Compare assessments for a player.
     */
    public function compare(Player $player): View
    {
        $assessments = $player->posturalAssessments()
            ->with('clinician')
            ->orderBy('assessment_date', 'desc')
            ->limit(5)
            ->get();

        return view('postural-assessments.compare', compact('player', 'assessments'));
    }
} 