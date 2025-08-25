<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Team\StoreTeamRequest;
use App\Http\Requests\Api\V1\Team\UpdateTeamRequest;
use App\Http\Requests\Api\V1\Team\AddPlayerRequest;
use App\Http\Requests\Api\V1\Team\RemovePlayerRequest;
use App\Http\Resources\Api\V1\TeamResource;
use App\Http\Resources\Api\V1\TeamCollection;
use App\Models\Team;
use App\Models\Player;
use App\Models\Club;
use App\Models\Competition;
use App\Policies\TeamPolicy;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamController extends Controller
{
    use AuthorizesRequests;
    
    protected AuditTrailService $auditTrailService;

    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
        $this->authorizeResource(Team::class, 'team');
    }

    /**
     * Display a listing of teams with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $query = Team::with(['club', 'association', 'players', 'competitions']);

        // Filter by club
        if ($request->has('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        // Filter by association
        if ($request->has('association_id')) {
            $query->where('association_id', $request->association_id);
        }

        // Filter by competition
        if ($request->has('competition_id')) {
            $query->whereHas('competitions', function ($q) use ($request) {
                $q->where('competitions.id', $request->competition_id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $teams = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => TeamResource::collection($teams),
            'meta' => [
                'current_page' => (int) $teams->currentPage(),
                'last_page' => (int) $teams->lastPage(),
                'per_page' => (int) $teams->perPage(),
                'total' => (int) $teams->total(),
                'from' => $teams->firstItem() !== null ? (int) $teams->firstItem() : null,
                'to' => $teams->lastItem() !== null ? (int) $teams->lastItem() : null,
            ],
            'links' => [
                'first' => $teams->url(1),
                'last' => $teams->url($teams->lastPage()),
                'prev' => $teams->previousPageUrl(),
                'next' => $teams->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Store a newly created team
     */
    public function store(StoreTeamRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $team = Team::create($validated);

            // Attach to competitions if provided
            if (isset($validated['competition_ids'])) {
                $team->competitions()->attach($validated['competition_ids']);
            }

            // Attach players if provided
            if (isset($validated['player_ids'])) {
                $playerData = [];
                foreach ($validated['player_ids'] as $playerId) {
                    $playerData[$playerId] = [
                        'joined_date' => now()->toDateString(),
                        'role' => 'substitute'
                    ];
                }
                $team->players()->attach($playerData);
            }

            $this->auditTrailService->log(
                'team_created',
                'Team created successfully',
                $team,
                $request->user()
            );

            DB::commit();

            return response()->json([
                'message' => 'Team created successfully',
                'data' => new TeamResource($team->load(['club', 'association', 'players', 'competitions']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified team
     */
    public function show(Team $team): JsonResponse
    {
        $team->load(['club', 'association', 'players', 'competitions', 'standings']);

        return response()->json([
            'data' => new TeamResource($team)
        ]);
    }

    /**
     * Update the specified team
     */
    public function update(UpdateTeamRequest $request, Team $team): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $team->update($validated);

            // Update competitions if provided
            if (isset($validated['competition_ids'])) {
                $team->competitions()->sync($validated['competition_ids']);
            }

            $this->auditTrailService->log(
                'team_updated',
                'Team updated successfully',
                $team,
                $request->user()
            );

            DB::commit();

            return response()->json([
                'message' => 'Team updated successfully',
                'data' => new TeamResource($team->load(['club', 'association', 'players', 'competitions']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified team
     */
    public function destroy(Request $request, Team $team): JsonResponse
    {
        // Check if team can be deleted (no active matches, etc.)
        if ($team->matches()->where('match_status', '!=', 'completed')->exists()) {
            throw ValidationException::withMessages([
                'team' => 'Cannot delete team with active matches'
            ]);
        }

        DB::beginTransaction();
        try {
            // Detach all relationships
            $team->players()->detach();
            $team->competitions()->detach();
            $team->standings()->delete();

            $this->auditTrailService->log(
                'team_deleted',
                'Team deleted successfully',
                $team,
                $request->user()
            );

            $team->delete();

            DB::commit();

            return response()->json([
                'message' => 'Team deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Add player to team
     */
    public function addPlayer(AddPlayerRequest $request, Team $team): JsonResponse
    {
        $validated = $request->validated();
        $player = Player::findOrFail($validated['player_id']);

        // Check if player is already in team
        if ($team->players()->where('player_id', $player->id)->exists()) {
            throw ValidationException::withMessages([
                'player_id' => 'Player is already in this team'
            ]);
        }

        // Check if player is in another team in the same competition
        $competitionIds = $team->competitions->pluck('id');
        if ($competitionIds->isNotEmpty()) {
            $otherTeams = $player->teams()->whereHas('competitions', function ($q) use ($competitionIds) {
                $q->whereIn('competitions.id', $competitionIds);
            })->exists();

            if ($otherTeams) {
                throw ValidationException::withMessages([
                    'player_id' => 'Player is already in another team in the same competition'
                ]);
            }
        }

        DB::beginTransaction();
        try {
            $team->players()->attach($player->id, [
                'joined_date' => now()->toDateString(),
                'role' => $validated['role'] ?? 'substitute'
            ]);

            $this->auditTrailService->log(
                'player_added_to_team',
                "Player {$player->name} added to team {$team->name}",
                $team,
                $request->user(),
                ['player_id' => $player->id]
            );

            DB::commit();

            return response()->json([
                'message' => 'Player added to team successfully',
                'data' => new TeamResource($team->load(['players']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove player from team
     */
    public function removePlayer(RemovePlayerRequest $request, Team $team): JsonResponse
    {
        $validated = $request->validated();
        $player = Player::findOrFail($validated['player_id']);

        // Check if player is in team
        if (!$team->players()->where('player_id', $player->id)->exists()) {
            throw ValidationException::withMessages([
                'player_id' => 'Player is not in this team'
            ]);
        }

        // Check if player has active matches
        if ($team->matches()->whereHas('lineups', function ($q) use ($player) {
            $q->where('player_id', $player->id);
        })->where('match_status', '!=', 'completed')->exists()) {
            throw ValidationException::withMessages([
                'player_id' => 'Cannot remove player with active matches'
            ]);
        }

        DB::beginTransaction();
        try {
            $team->players()->detach($player->id);

            $this->auditTrailService->log(
                'player_removed_from_team',
                "Player {$player->name} removed from team {$team->name}",
                $team,
                $request->user(),
                ['player_id' => $player->id]
            );

            DB::commit();

            return response()->json([
                'message' => 'Player removed from team successfully',
                'data' => new TeamResource($team->load(['players']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get team roster
     */
    public function roster(Team $team): JsonResponse
    {
        $team->load(['players' => function ($query) {
            $query->withPivot(['joined_date', 'role', 'squad_number', 'status']);
        }]);

        return response()->json([
            'data' => [
                'team' => new TeamResource($team),
                'roster' => $team->players->map(function ($player) {
                    return [
                        'id' => $player->id,
                        'name' => $player->name,
                        'position' => $player->position,
                        'joined_at' => $player->pivot->joined_date,
                        'role' => $player->pivot->role,
                        'squad_number' => $player->pivot->squad_number,
                        'status' => $player->pivot->status
                    ];
                })
            ]
        ]);
    }

    /**
     * Get team statistics
     */
    public function statistics(Team $team): JsonResponse
    {
        $stats = [
            'total_matches' => $team->matches()->count(),
            'wins' => $team->matches()->where('winner_team_id', $team->id)->count(),
            'losses' => $team->matches()->where('loser_team_id', $team->id)->count(),
            'draws' => $team->matches()->whereNull('winner_team_id')->whereNull('loser_team_id')->count(),
            'total_players' => $team->players()->count(),
            'active_competitions' => $team->competitions()->where('status', 'active')->count(),
        ];

        return response()->json([
            'data' => [
                'team' => new TeamResource($team),
                'statistics' => $stats
            ]
        ]);
    }

    /**
     * Get team standings
     */
    public function standings(Team $team): JsonResponse
    {
        $standings = $team->standings()->with('competition')->get();

        return response()->json([
            'data' => [
                'team' => new TeamResource($team),
                'standings' => $standings->map(function ($standing) {
                    return [
                        'competition' => $standing->competition->name,
                        'position' => $standing->position,
                        'points' => $standing->points,
                        'matches_played' => $standing->matches_played,
                        'wins' => $standing->wins,
                        'draws' => $standing->draws,
                        'losses' => $standing->losses,
                        'goals_for' => $standing->goals_for,
                        'goals_against' => $standing->goals_against,
                        'goal_difference' => $standing->goal_difference
                    ];
                })
            ]
        ]);
    }
} 