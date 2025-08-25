<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Match\StoreMatchRequest;
use App\Http\Requests\Api\V1\Match\UpdateMatchRequest;
use App\Http\Resources\Api\V1\MatchResource;
use App\Http\Resources\Api\V1\MatchCollection;
use App\Models\MatchModel;
use App\Models\MatchEvent;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MatchController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private AuditTrailService $auditTrailService
    ) {
        // $this->authorizeResource(GameMatch::class, 'match');
    }

    /**
     * Display a listing of matches
     */
    public function index(Request $request): JsonResponse
    {
        // Debug: log the authenticated user
        \Log::info('Authenticated user', [
            'id' => $request->user()?->id,
            'role' => $request->user()?->role,
            'association_id' => $request->user()?->association_id,
        ]);
        
        // Temporary: bypass policy and use simple role check
        $user = $request->user();
        if (!in_array($user->role, ['admin', 'system_admin', 'organizer', 'referee', 'committee_member', 'club_manager'])) {
            abort(403, 'Unauthorized');
        }

        $query = MatchModel::query()
            ->with(['homeTeam', 'awayTeam', 'competition']);

        // Apply filters
        if ($request->has('competition_id')) {
            $query->where('competition_id', $request->competition_id);
        }

        if ($request->has('status')) {
            $query->where('match_status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('matchday', 'like', '%' . $request->search . '%');
        }

        if ($request->has('date_from')) {
            $query->where('match_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('match_date', '<=', $request->date_to);
        }

        $matches = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => 'Matches retrieved successfully',
            'data' => [
                'data' => MatchResource::collection($matches),
                'pagination' => [
                    'current_page' => $matches->currentPage(),
                    'last_page' => $matches->lastPage(),
                    'per_page' => $matches->perPage(),
                    'total' => $matches->total(),
                    'from' => $matches->firstItem(),
                    'to' => $matches->lastItem(),
                    'has_more_pages' => $matches->hasMorePages(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created match
     */
    public function store(StoreMatchRequest $request): JsonResponse
    {
        $this->authorize('create', MatchModel::class);

        $match = MatchModel::create($request->validated());

        $this->auditTrailService->logMatchCreated($request->user(), $match);

        return response()->json([
            'message' => 'Match created successfully',
            'data' => new MatchResource($match->load(['homeTeam', 'awayTeam', 'competition']))
        ], 201);
    }

    /**
     * Display the specified match
     */
    public function show(MatchModel $match): JsonResponse
    {
        $this->authorize('view', $match);

        $match->load(['homeTeam', 'awayTeam', 'competition']);

        return response()->json([
            'message' => 'Match retrieved successfully',
            'data' => new MatchResource($match)
        ]);
    }

    /**
     * Update the specified match
     */
    public function update(UpdateMatchRequest $request, MatchModel $match): JsonResponse
    {
        $this->authorize('update', $match);

        $oldData = $match->toArray();
        $match->update($request->validated());

        $this->auditTrailService->logMatchUpdated($request->user(), $match, $oldData);

        return response()->json([
            'message' => 'Match updated successfully',
            'data' => new MatchResource($match->load(['homeTeam', 'awayTeam', 'competition']))
        ]);
    }

    /**
     * Remove the specified match
     */
    public function destroy(Request $request, MatchModel $match): JsonResponse
    {
        $this->authorize('delete', $match);

        $matchData = $match->toArray();
        $match->delete();

        $this->auditTrailService->logMatchDeleted($request->user(), $matchData);

        return response()->json([
            'message' => 'Match deleted successfully'
        ]);
    }

    /**
     * Get match events
     */
    public function events(MatchModel $match): JsonResponse
    {
        $match->load('competition');
        
        // Manual policy check (temporary fix for Gate system issue)
        $policy = new \App\Policies\MatchPolicy();
        $user = auth()->user();
        
        if (!$policy->view($user, $match)) {
            abort(403, 'This action is unauthorized.');
        }

        $events = $match->events()
            ->with(['player', 'team'])
            ->orderBy('minute', 'asc')
            ->get();

        return response()->json([
            'message' => 'Match events retrieved successfully',
            'data' => [
                'match' => new MatchResource($match->load(['homeTeam', 'awayTeam'])),
                'events' => $events
            ]
        ]);
    }

    /**
     * Add match event
     */
    public function addEvent(Request $request, MatchModel $match): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:goal,assist,yellow_card,red_card,injury,substitution',
            'minute' => 'required|integer|min:1|max:120',
            'player_id' => 'required|exists:players,id',
            'team_id' => 'required|exists:teams,id',
            'description' => 'nullable|string|max:500',
            'additional_data' => 'nullable|array'
        ]);

        $event = $match->events()->create($request->all());

        $this->auditTrailService->logMatchEventCreated($request->user(), $event);

        return response()->json([
            'message' => 'Match event added successfully',
            'data' => $event->load(['player', 'team'])
        ], 201);
    }

    /**
     * Get match lineups
     */
    public function lineups(MatchModel $match): JsonResponse
    {
        $lineups = $match->lineups()
            ->with(['team', 'players'])
            ->get();

        return response()->json([
            'message' => 'Match lineups retrieved successfully',
            'data' => [
                'match' => new MatchResource($match->load(['homeTeam', 'awayTeam'])),
                'lineups' => $lineups
            ]
        ]);
    }

    /**
     * Get match statistics
     */
    public function statistics(MatchModel $match): JsonResponse
    {
        $statistics = [
            'home_team' => [
                'goals' => $match->events()->where('type', 'goal')->where('team_id', $match->home_team_id)->count(),
                'yellow_cards' => $match->events()->where('type', 'yellow_card')->where('team_id', $match->home_team_id)->count(),
                'red_cards' => $match->events()->where('type', 'red_card')->where('team_id', $match->home_team_id)->count(),
                'injuries' => $match->events()->where('type', 'injury')->where('team_id', $match->home_team_id)->count(),
            ],
            'away_team' => [
                'goals' => $match->events()->where('type', 'goal')->where('team_id', $match->away_team_id)->count(),
                'yellow_cards' => $match->events()->where('type', 'yellow_card')->where('team_id', $match->away_team_id)->count(),
                'red_cards' => $match->events()->where('type', 'red_card')->where('team_id', $match->away_team_id)->count(),
                'injuries' => $match->events()->where('type', 'injury')->where('team_id', $match->away_team_id)->count(),
            ],
            'total_events' => $match->events()->count(),
            'match_duration' => $match->match_status === 'completed' ? $match->duration ?? null : null,
        ];

        return response()->json([
            'message' => 'Match statistics retrieved successfully',
            'data' => [
                'match' => new MatchResource($match->load(['homeTeam', 'awayTeam'])),
                'statistics' => $statistics
            ]
        ]);
    }

    /**
     * Update match status
     */
    public function updateStatus(Request $request, MatchModel $match): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:scheduled,in_progress,completed,cancelled,postponed'
        ]);

        $oldStatus = $match->match_status;
        $match->update(['match_status' => $request->status]);

        $this->auditTrailService->logMatchStatusUpdated($request->user(), $match, $oldStatus);

        return response()->json([
            'message' => 'Match status updated successfully',
            'data' => new MatchResource($match->load(['homeTeam', 'awayTeam']))
        ]);
    }
} 