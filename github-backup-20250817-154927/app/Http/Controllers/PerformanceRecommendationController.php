<?php

namespace App\Http\Controllers;

use App\Models\PerformanceRecommendation;
use App\Models\Player;
use Illuminate\Http\Request;

class PerformanceRecommendationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PerformanceRecommendation::with(['player']);

        // Apply filters
        if ($request->filled('player_id')) {
            $query->where('player_id', $request->player_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $recommendations = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('performance-recommendations.index', compact('recommendations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $players = Player::orderBy('first_name')->get();
        return view('performance-recommendations.create', compact('players'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'target_date' => 'nullable|date',
        ]);

        PerformanceRecommendation::create($validated);

        return redirect()->route('performance-recommendations.index')
            ->with('success', 'Performance recommendation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PerformanceRecommendation $performanceRecommendation)
    {
        $performanceRecommendation->load('player');
        return view('performance-recommendations.show', compact('performanceRecommendation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PerformanceRecommendation $performanceRecommendation)
    {
        $players = Player::orderBy('first_name')->get();
        return view('performance-recommendations.edit', compact('performanceRecommendation', 'players'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PerformanceRecommendation $performanceRecommendation)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:pending,in_progress,implemented,cancelled',
            'implementation_notes' => 'nullable|string',
            'target_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
        ]);

        $performanceRecommendation->update($validated);

        return redirect()->route('performance-recommendations.index')
            ->with('success', 'Performance recommendation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PerformanceRecommendation $performanceRecommendation)
    {
        $performanceRecommendation->delete();

        return redirect()->route('performance-recommendations.index')
            ->with('success', 'Performance recommendation deleted successfully.');
    }

    /**
     * Update recommendation status.
     */
    public function updateStatus(Request $request, PerformanceRecommendation $performanceRecommendation)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,implemented,cancelled',
            'implementation_notes' => 'nullable|string',
        ]);

        $performanceRecommendation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Recommendation status updated successfully'
        ]);
    }

    /**
     * Generate AI recommendations for a player.
     */
    public function generateForPlayer(Request $request, Player $player)
    {
        // This would typically call an AI service
        $recommendations = [
            [
                'type' => 'performance_improvement',
                'title' => 'Increase Sprint Count',
                'description' => 'Player should aim for 25+ sprints per match to improve performance.',
                'priority' => 'high'
            ],
            [
                'type' => 'training_focus',
                'title' => 'Focus on Endurance Training',
                'description' => 'Implement interval training to improve stamina and recovery.',
                'priority' => 'medium'
            ]
        ];

        foreach ($recommendations as $rec) {
            PerformanceRecommendation::create([
                'player_id' => $player->id,
                'type' => $rec['type'],
                'title' => $rec['title'],
                'description' => $rec['description'],
                'priority' => $rec['priority'],
                'status' => 'pending'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'AI recommendations generated successfully',
            'count' => count($recommendations)
        ]);
    }
}
