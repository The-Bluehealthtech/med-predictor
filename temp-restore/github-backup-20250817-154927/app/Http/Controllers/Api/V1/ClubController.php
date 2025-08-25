<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Club\StoreClubRequest;
use App\Http\Requests\Api\V1\Club\UpdateClubRequest;
use App\Http\Resources\Api\V1\ClubResource;
use App\Http\Resources\Api\V1\ClubCollection;
use App\Models\Club;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClubController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private AuditTrailService $auditTrailService
    ) {
        $this->authorizeResource(Club::class, 'club');
    }

    /**
     * Display a listing of clubs
     */
    public function index(Request $request): JsonResponse
    {
        $query = Club::query()
            ->with(['association', 'teams', 'players']);

        // Apply filters
        if ($request->has('association_id')) {
            $query->where('association_id', $request->association_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%");
            });
        }

        $clubs = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => 'Clubs retrieved successfully',
            'data' => new ClubCollection($clubs)
        ]);
    }

    /**
     * Store a newly created club
     */
    public function store(StoreClubRequest $request): JsonResponse
    {
        $club = Club::create($request->validated());

        $this->auditTrailService->logClubCreated($request->user(), $club);

        return response()->json([
            'message' => 'Club created successfully',
            'data' => new ClubResource($club->load('association'))
        ], 201);
    }

    /**
     * Display the specified club
     */
    public function show(Club $club): JsonResponse
    {
        $club->load(['association', 'teams', 'players']);

        return response()->json([
            'message' => 'Club retrieved successfully',
            'data' => new ClubResource($club)
        ]);
    }

    /**
     * Update the specified club
     */
    public function update(UpdateClubRequest $request, Club $club): JsonResponse
    {
        $oldData = $club->toArray();
        $club->update($request->validated());

        $this->auditTrailService->logClubUpdated($request->user(), $club, $oldData);

        return response()->json([
            'message' => 'Club updated successfully',
            'data' => new ClubResource($club->load('association'))
        ]);
    }

    /**
     * Remove the specified club
     */
    public function destroy(Request $request, Club $club): JsonResponse
    {
        $clubData = $club->toArray();
        $club->delete();

        $this->auditTrailService->logClubDeleted($request->user(), $clubData);

        return response()->json([
            'message' => 'Club deleted successfully'
        ]);
    }

    /**
     * Get club players
     */
    public function players(Club $club): JsonResponse
    {
        $players = $club->players()
            ->with(['team', 'licenses'])
            ->paginate(request()->get('per_page', 15));

        return response()->json([
            'message' => 'Club players retrieved successfully',
            'data' => [
                'club' => new ClubResource($club->load('association')),
                'players' => $players
            ]
        ]);
    }

    /**
     * Get club teams
     */
    public function teams(Club $club): JsonResponse
    {
        $teams = $club->teams()
            ->with(['players', 'competitions'])
            ->paginate(request()->get('per_page', 15));

        return response()->json([
            'message' => 'Club teams retrieved successfully',
            'data' => [
                'club' => new ClubResource($club->load('association')),
                'teams' => $teams
            ]
        ]);
    }

    /**
     * Get club statistics
     */
    public function statistics(Club $club): JsonResponse
    {
        $statistics = [
            'total_players' => $club->players()->count(),
            'active_players' => $club->players()->where('status', 'active')->count(),
            'total_teams' => $club->teams()->count(),
            'active_teams' => $club->teams()->where('status', 'active')->count(),
            'total_licenses' => $club->players()->withCount('licenses')->get()->sum('licenses_count'),
            'pending_licenses' => $club->players()->withCount(['licenses' => function ($query) {
                $query->where('status', 'pending');
            }])->get()->sum('licenses_count'),
        ];

        return response()->json([
            'message' => 'Club statistics retrieved successfully',
            'data' => [
                'club' => new ClubResource($club->load('association')),
                'statistics' => $statistics
            ]
        ]);
    }
} 