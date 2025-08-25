<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Season\StoreSeasonRequest;
use App\Http\Requests\Api\V1\Season\UpdateSeasonRequest;
use App\Http\Resources\Api\V1\SeasonCollection;
use App\Http\Resources\Api\V1\SeasonResource;
use App\Models\Season;
use App\Services\AuditTrailService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class SeasonController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private AuditTrailService $auditTrailService
    ) {
        $this->authorizeResource(Season::class, 'season');
    }

    /**
     * Display a listing of seasons.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Season::query()
            ->with(['competitions', 'createdBy'])
            ->orderBy('start_date', 'desc');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('is_current')) {
            $query->where('is_current', $request->boolean('is_current'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%");
            });
        }

        $seasons = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => SeasonResource::collection($seasons->items()),
            'meta' => [
                'current_page' => $seasons->currentPage(),
                'last_page' => $seasons->lastPage(),
                'per_page' => $seasons->perPage(),
                'total' => $seasons->total(),
                'from' => $seasons->firstItem(),
                'to' => $seasons->lastItem(),
            ],
            'links' => [
                'first' => $seasons->url(1),
                'last' => $seasons->url($seasons->lastPage()),
                'prev' => $seasons->previousPageUrl(),
                'next' => $seasons->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Store a newly created season.
     */
    public function store(StoreSeasonRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $season = Season::create($data);

            // Log audit trail
            $this->auditTrailService->log(
                'season_created',
                'Season created',
                $season,
                auth()->user(),
                $data
            );

            DB::commit();

            return response()->json([
                'message' => 'Season created successfully',
                'data' => new SeasonResource($season->load(['competitions', 'createdBy']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create season',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified season.
     */
    public function show(Season $season): JsonResponse
    {
        $season->load(['competitions', 'createdBy', 'updatedBy', 'players']);

        return response()->json([
            'data' => new SeasonResource($season)
        ]);
    }

    /**
     * Update the specified season.
     */
    public function update(UpdateSeasonRequest $request, Season $season): JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $originalData = $season->toArray();

        DB::beginTransaction();
        try {
            $season->update($data);

            // Log audit trail
            $this->auditTrailService->log(
                'season_updated',
                'Season updated',
                $season,
                auth()->user(),
                array_merge($data, ['original' => $originalData])
            );

            DB::commit();

            return response()->json([
                'message' => 'Season updated successfully',
                'data' => new SeasonResource($season->load(['competitions', 'createdBy', 'updatedBy']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update season',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified season.
     */
    public function destroy(Season $season): JsonResponse
    {
        // Check if season has competitions
        if ($season->competitions()->exists()) {
            return response()->json([
                'message' => 'Cannot delete season with existing competitions'
            ], 422);
        }

        // Check if season has players
        if ($season->players()->exists()) {
            return response()->json([
                'message' => 'Cannot delete season with existing players'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $seasonData = $season->toArray();
            $season->delete();

            // Log audit trail
            $this->auditTrailService->log(
                'season_deleted',
                'Season deleted',
                null,
                auth()->user(),
                ['deleted_season' => $seasonData]
            );

            DB::commit();

            return response()->json([
                'message' => 'Season deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete season',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set season as current.
     */
    public function setCurrent(Season $season): JsonResponse
    {
        $this->authorize('setCurrent', $season);

        DB::beginTransaction();
        try {
            // Deactivate all current seasons
            Season::where('is_current', true)->update(['is_current' => false]);

            // Set this season as current
            $season->update(['is_current' => true]);

            // Log audit trail
            $this->auditTrailService->log(
                'season_set_current',
                'Season set as current',
                $season,
                auth()->user(),
                ['action' => 'set_current']
            );

            DB::commit();

            return response()->json([
                'message' => 'Season set as current successfully',
                'data' => new SeasonResource($season->load(['competitions', 'createdBy']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to set season as current',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update season status.
     */
    public function updateStatus(Request $request, Season $season): JsonResponse
    {
        $this->authorize('updateStatus', $season);

        $request->validate([
            'status' => 'required|in:upcoming,active,completed,archived'
        ]);

        $originalStatus = $season->status;
        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            $season->update(['status' => $newStatus]);

            // Log audit trail
            $this->auditTrailService->log(
                'season_status_updated',
                'Season status updated',
                $season,
                auth()->user(),
                [
                    'old_status' => $originalStatus,
                    'new_status' => $newStatus
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Season status updated successfully',
                'data' => new SeasonResource($season->load(['competitions', 'createdBy']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update season status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get season statistics.
     */
    public function statistics(Season $season): JsonResponse
    {
        $this->authorize('viewStatistics', $season);

        $statistics = [
            'competitions_count' => $season->competitions()->count(),
            'active_competitions_count' => $season->competitions()->where('status', 'active')->count(),
            'players_count' => $season->players()->count(),
            'matches_count' => $season->competitions()
                ->withCount('matches')
                ->get()
                ->sum('matches_count'),
            'duration_days' => $season->start_date->diffInDays($season->end_date),
            'is_registration_open' => $season->isRegistrationOpen(),
            'days_until_start' => now()->diffInDays($season->start_date, false),
            'days_until_end' => now()->diffInDays($season->end_date, false),
        ];

        return response()->json([
            'data' => $statistics
        ]);
    }

    /**
     * Get current season.
     */
    public function current(): JsonResponse
    {
        $currentSeason = Season::current()->with(['competitions', 'createdBy'])->first();

        if (!$currentSeason) {
            return response()->json([
                'message' => 'No current season found'
            ], 404);
        }

        return response()->json([
            'data' => new SeasonResource($currentSeason)
        ]);
    }

    /**
     * Get active seasons.
     */
    public function active(): JsonResponse
    {
        $activeSeasons = Season::active()
            ->with(['competitions', 'createdBy'])
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'data' => SeasonResource::collection($activeSeasons)
        ]);
    }

    /**
     * Get upcoming seasons.
     */
    public function upcoming(): JsonResponse
    {
        $upcomingSeasons = Season::upcoming()
            ->with(['competitions', 'createdBy'])
            ->orderBy('start_date', 'asc')
            ->get();

        return response()->json([
            'data' => SeasonResource::collection($upcomingSeasons)
        ]);
    }

    /**
     * Get completed seasons.
     */
    public function completed(): JsonResponse
    {
        $completedSeasons = Season::completed()
            ->with(['competitions', 'createdBy'])
            ->orderBy('end_date', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => SeasonResource::collection($completedSeasons->items()),
            'meta' => [
                'current_page' => $completedSeasons->currentPage(),
                'last_page' => $completedSeasons->lastPage(),
                'per_page' => $completedSeasons->perPage(),
                'total' => $completedSeasons->total(),
            ],
        ]);
    }
} 