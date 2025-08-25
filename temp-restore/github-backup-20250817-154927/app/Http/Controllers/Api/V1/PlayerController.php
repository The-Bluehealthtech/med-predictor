<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Player\StorePlayerRequest;
use App\Http\Requests\Api\V1\Player\UpdatePlayerRequest;
use App\Http\Resources\Api\V1\PlayerResource;
use App\Http\Resources\Api\V1\PlayerCollection;
use App\Models\Player;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PlayerController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private AuditTrailService $auditTrailService
    ) {
        $this->authorizeResource(Player::class, 'player');
    }

    /**
     * Display a listing of players
     */
    public function index(Request $request): JsonResponse
    {
        $query = Player::query()
            ->with(['club', 'team', 'healthRecords', 'licenses']);

        // Apply filters
        if ($request->has('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        if ($request->has('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $players = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => 'Players retrieved successfully',
            'data' => new PlayerCollection($players)
        ]);
    }

    /**
     * Store a newly created player
     */
    public function store(StorePlayerRequest $request): JsonResponse
    {
        $player = Player::create($request->validated());

        $this->auditTrailService->logPlayerCreated($request->user(), $player);

        return response()->json([
            'message' => 'Player created successfully',
            'data' => new PlayerResource($player->load(['club', 'team']))
        ], 201);
    }

    /**
     * Display the specified player
     */
    public function show(Player $player): JsonResponse
    {
        $player->load(['club', 'team', 'healthRecords', 'licenses', 'seasonStats']);

        return response()->json([
            'message' => 'Player retrieved successfully',
            'data' => new PlayerResource($player)
        ]);
    }

    /**
     * Update the specified player
     */
    public function update(UpdatePlayerRequest $request, Player $player): JsonResponse
    {
        $oldData = $player->toArray();
        $player->update($request->validated());

        $this->auditTrailService->logPlayerUpdated($request->user(), $player, $oldData);

        return response()->json([
            'message' => 'Player updated successfully',
            'data' => new PlayerResource($player->load(['club', 'team']))
        ]);
    }

    /**
     * Remove the specified player
     */
    public function destroy(Request $request, Player $player): JsonResponse
    {
        $playerData = $player->toArray();
        $player->delete();

        $this->auditTrailService->logPlayerDeleted($request->user(), $playerData);

        return response()->json([
            'message' => 'Player deleted successfully'
        ]);
    }

    /**
     * Get player profile with detailed information
     */
    public function profile(Player $player): JsonResponse
    {
        $player->load([
            'club', 
            'team', 
            'healthRecords' => function ($query) {
                $query->latest()->limit(5);
            },
            'licenses' => function ($query) {
                $query->latest()->limit(5);
            },
            'seasonStats' => function ($query) {
                $query->latest()->limit(10);
            }
        ]);

        return response()->json([
            'message' => 'Player profile retrieved successfully',
            'data' => new PlayerResource($player)
        ]);
    }

    /**
     * Get player statistics
     */
    public function statistics(Player $player): JsonResponse
    {
        $statistics = $player->seasonStats()
            ->with('season')
            ->orderBy('season_id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Player statistics retrieved successfully',
            'data' => [
                'player' => new PlayerResource($player->load('club')),
                'statistics' => $statistics
            ]
        ]);
    }
} 