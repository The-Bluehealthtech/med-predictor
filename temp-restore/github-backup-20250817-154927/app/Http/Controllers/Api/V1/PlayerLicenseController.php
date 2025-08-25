<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PlayerLicense\StorePlayerLicenseRequest;
use App\Http\Requests\Api\V1\PlayerLicense\UpdatePlayerLicenseRequest;
use App\Http\Resources\Api\V1\PlayerLicenseResource;
use App\Models\PlayerLicense;
use App\Services\AuditTrailService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerLicenseController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private AuditTrailService $auditTrailService)
    {
        $this->authorizeResource(PlayerLicense::class, 'player_license');
    }

    public function index(Request $request): JsonResponse
    {
        $query = PlayerLicense::query()->with(['player', 'club']);
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('club_id')) {
            $query->where('club_id', $request->club_id);
        }
        if ($request->has('player_id')) {
            $query->where('player_id', $request->player_id);
        }
        $licenses = $query->paginate($request->get('per_page', 15));
        return response()->json([
            'data' => PlayerLicenseResource::collection($licenses->items()),
            'meta' => [
                'current_page' => $licenses->currentPage(),
                'last_page' => $licenses->lastPage(),
                'per_page' => $licenses->perPage(),
                'total' => $licenses->total(),
                'from' => $licenses->firstItem(),
                'to' => $licenses->lastItem(),
            ],
            'links' => [
                'first' => $licenses->url(1),
                'last' => $licenses->url($licenses->lastPage()),
                'prev' => $licenses->previousPageUrl(),
                'next' => $licenses->nextPageUrl(),
            ],
        ]);
    }

    public function store(StorePlayerLicenseRequest $request): JsonResponse
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $license = PlayerLicense::create($data);
            $this->auditTrailService->log('player_license_created', 'Player license created', $license, auth()->user(), $data);
            DB::commit();
            return response()->json([
                'message' => 'Player license created successfully',
                'data' => new PlayerLicenseResource($license->load(['player', 'club']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create player license',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(PlayerLicense $player_license): JsonResponse
    {
        $player_license->load(['player', 'club']);
        return response()->json([
            'data' => new PlayerLicenseResource($player_license)
        ]);
    }

    public function update(UpdatePlayerLicenseRequest $request, PlayerLicense $player_license): JsonResponse
    {
        $data = $request->validated();
        $originalData = $player_license->toArray();
        DB::beginTransaction();
        try {
            $player_license->update($data);
            $this->auditTrailService->log('player_license_updated', 'Player license updated', $player_license, auth()->user(), array_merge($data, ['original' => $originalData]));
            DB::commit();
            return response()->json([
                'message' => 'Player license updated successfully',
                'data' => new PlayerLicenseResource($player_license->load(['player', 'club']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update player license',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(PlayerLicense $playerLicense)
    {
        $this->authorize('delete', $playerLicense);
        $playerLicense->delete();
        return response()->noContent();
    }

    public function approve(Request $request, PlayerLicense $player_license): JsonResponse
    {
        $this->authorize('approve', $player_license);
        DB::beginTransaction();
        try {
            $player_license->approve(auth()->id());
            $this->auditTrailService->log('player_license_approved', 'Player license approved', $player_license, auth()->user(), []);
            DB::commit();
            return response()->json([
                'message' => 'Player license approved successfully',
                'data' => new PlayerLicenseResource($player_license->fresh(['player', 'club']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to approve player license',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, PlayerLicense $player_license): JsonResponse
    {
        $this->authorize('reject', $player_license);
        $request->validate(['reason' => 'required|string|max:255']);
        DB::beginTransaction();
        try {
            $player_license->reject($request->reason, auth()->id());
            $this->auditTrailService->log('player_license_rejected', 'Player license rejected', $player_license, auth()->user(), ['reason' => $request->reason]);
            DB::commit();
            return response()->json([
                'message' => 'Player license rejected successfully',
                'data' => new PlayerLicenseResource($player_license->fresh(['player', 'club']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to reject player license',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function renew(Request $request, PlayerLicense $playerLicense)
    {
        $this->authorize('renew', $playerLicense);
        $newExpiryDate = $request->input('new_expiry_date') ?? $request->input('expiry_date');
        if (!$newExpiryDate) {
            return response()->json([
                'message' => 'The expiry date field is required.',
                'errors' => ['expiry_date' => ['The expiry date field is required.']]
            ], 422);
        }
        DB::beginTransaction();
        try {
            $playerLicense->renew($newExpiryDate);
            $this->auditTrailService->log('player_license_renewed', 'Player license renewed', $playerLicense, auth()->user(), ['expiry_date' => $newExpiryDate]);
            DB::commit();
            return response()->json([
                'message' => 'Player license renewed successfully',
                'data' => new PlayerLicenseResource($playerLicense->fresh(['player', 'club']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to renew player license',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function suspend(Request $request, PlayerLicense $player_license): JsonResponse
    {
        $this->authorize('suspend', $player_license);
        $request->validate(['reason' => 'required|string|max:255']);
        DB::beginTransaction();
        try {
            $player_license->suspend($request->reason);
            $this->auditTrailService->log('player_license_suspended', 'Player license suspended', $player_license, auth()->user(), ['reason' => $request->reason]);
            DB::commit();
            return response()->json([
                'message' => 'Player license suspended successfully',
                'data' => new PlayerLicenseResource($player_license->fresh(['player', 'club']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to suspend player license',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function transfer(Request $request, PlayerLicense $playerLicense)
    {
        $this->authorize('transfer', $playerLicense);
        $newClubId = $request->input('new_club_id') ?? $request->input('club_id');
        if (!$newClubId) {
            return response()->json([
                'message' => 'The club id field is required.',
                'errors' => ['club_id' => ['The club id field is required.']]
            ], 422);
        }
        DB::beginTransaction();
        try {
            if (!$playerLicense->transfer($newClubId)) {
                return response()->json([
                    'message' => 'Transfer not allowed. License may be expired, suspended, or already pending transfer.',
                    'errors' => ['transfer' => ['Transfer not allowed']]
                ], 422);
            }
            $this->auditTrailService->log('player_license_transferred', 'Player license transferred', $playerLicense, auth()->user(), ['club_id' => $newClubId]);
            DB::commit();
            return response()->json([
                'message' => 'Player license transferred successfully',
                'data' => new PlayerLicenseResource($playerLicense->fresh(['player', 'club']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to transfer player license',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function status(Request $request, PlayerLicense $player_license): JsonResponse
    {
        $this->authorize('update', $player_license);
        $request->validate(['status' => 'required|string|in:pending,active,suspended,expired,revoked']);
        DB::beginTransaction();
        try {
            $player_license->update(['status' => $request->status]);
            $this->auditTrailService->log('player_license_status_updated', 'Player license status updated', $player_license, auth()->user(), ['status' => $request->status]);
            DB::commit();
            return response()->json([
                'message' => 'Player license status updated successfully',
                'data' => new PlayerLicenseResource($player_license->fresh(['player', 'club']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update player license status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 