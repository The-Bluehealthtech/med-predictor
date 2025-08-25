<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\StoreUserRequest;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use App\Http\Resources\Api\V1\UserCollection;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Services\AuditTrailService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private AuditTrailService $auditTrailService
    ) {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query()
            ->with(['club', 'association', 'team'])
            ->orderBy('name', 'asc');

        // Apply filters
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        if ($request->has('association_id')) {
            $query->where('association_id', $request->association_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => UserResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'links' => [
                'first' => $users->url(1),
                'last' => $users->url($users->lastPage()),
                'prev' => $users->previousPageUrl(),
                'next' => $users->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        DB::beginTransaction();
        try {
            $user = User::create($data);

            // Log audit trail
            $this->auditTrailService->log(
                'user_created',
                'User created',
                $user,
                auth()->user(),
                array_merge($data, ['password' => '[HIDDEN]'])
            );

            DB::commit();

            return response()->json([
                'message' => 'User created successfully',
                'data' => new UserResource($user->load(['club', 'association', 'team']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['club', 'association', 'team']);

        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $originalData = $user->toArray();

        DB::beginTransaction();
        try {
            $user->update($data);

            // Log audit trail
            $this->auditTrailService->log(
                'user_updated',
                'User updated',
                $user,
                auth()->user(),
                array_merge($data, ['original' => $originalData])
            );

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully',
                'data' => new UserResource($user->load(['club', 'association', 'team']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Cannot delete your own account'
            ], 422);
        }

        // Check if user has related data
        if ($user->healthRecords()->exists()) {
            return response()->json([
                'message' => 'Cannot delete user with existing health records'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $userData = $user->toArray();
            $user->delete();

            // Log audit trail
            $this->auditTrailService->log(
                'user_deleted',
                'User deleted',
                null,
                auth()->user(),
                ['deleted_user' => $userData]
            );

            DB::commit();

            return response()->json([
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user role.
     */
    public function updateRole(Request $request, User $user): JsonResponse
    {
        $this->authorize('updateRole', $user);

        $request->validate([
            'role' => 'required|string|in:club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical,system_admin,referee'
        ]);

        $originalRole = $user->role;
        $newRole = $request->role;

        // Prevent association admins from promoting to system_admin
        if (auth()->user()->isAssociationAdmin() && $newRole === 'system_admin') {
            return response()->json([
                'message' => 'Association admins cannot promote users to system admin'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $user->update(['role' => $newRole]);

            // Log audit trail
            $this->auditTrailService->log(
                'user_role_updated',
                'User role updated',
                $user,
                auth()->user(),
                [
                    'old_role' => $originalRole,
                    'new_role' => $newRole
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'User role updated successfully',
                'data' => new UserResource($user->load(['club', 'association', 'team']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update user role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user permissions.
     */
    public function updatePermissions(Request $request, User $user): JsonResponse
    {
        $this->authorize('updatePermissions', $user);

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|in:player_registration_access,competition_management_access,healthcare_access,user_management_access,audit_trail_access'
        ]);

        $originalPermissions = $user->permissions;
        $newPermissions = $request->permissions;

        DB::beginTransaction();
        try {
            $user->update(['permissions' => $newPermissions]);

            // Log audit trail
            $this->auditTrailService->log(
                'user_permissions_updated',
                'User permissions updated',
                $user,
                auth()->user(),
                [
                    'old_permissions' => $originalPermissions,
                    'new_permissions' => $newPermissions
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'User permissions updated successfully',
                'data' => new UserResource($user->load(['club', 'association', 'team']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update user permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user status.
     */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $this->authorize('updateStatus', $user);

        $request->validate([
            'status' => 'required|string|in:active,inactive,suspended'
        ]);

        $originalStatus = $user->status;
        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            $user->update(['status' => $newStatus]);

            // Log audit trail
            $this->auditTrailService->log(
                'user_status_updated',
                'User status updated',
                $user,
                auth()->user(),
                [
                    'old_status' => $originalStatus,
                    'new_status' => $newStatus
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'User status updated successfully',
                'data' => new UserResource($user->load(['club', 'association', 'team']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update user status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics.
     */
    public function statistics(User $user): JsonResponse
    {
        $this->authorize('viewStatistics', $user);

        $statistics = [
            'health_records_count' => $user->healthRecords()->count(),
            'last_login_at' => $user->last_login_at?->toISOString(),
            'login_count' => $user->login_count,
            'days_since_last_login' => $user->last_login_at ? now()->diffInDays($user->last_login_at) : null,
            'account_age_days' => now()->diffInDays($user->created_at),
            'is_online' => $user->last_login_at && $user->last_login_at->diffInMinutes(now()) < 30,
        ];

        return response()->json([
            'data' => $statistics
        ]);
    }

    /**
     * Get users by role.
     */
    public function byRole(Request $request): JsonResponse
    {
        $request->validate([
            'role' => 'required|string|in:club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical,system_admin,referee'
        ]);

        $users = User::where('role', $request->role)
            ->with(['club', 'association', 'team'])
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Get users by status.
     */
    public function byStatus(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:active,inactive,suspended'
        ]);

        $users = User::where('status', $request->status)
            ->with(['club', 'association', 'team'])
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Get users by club.
     */
    public function byClub(Request $request): JsonResponse
    {
        $request->validate([
            'club_id' => 'required|integer|exists:clubs,id'
        ]);

        $users = User::where('club_id', $request->club_id)
            ->with(['club', 'association', 'team'])
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Get users by association.
     */
    public function byAssociation(Request $request): JsonResponse
    {
        $request->validate([
            'association_id' => 'required|integer|exists:associations,id'
        ]);

        $users = User::where('association_id', $request->association_id)
            ->with(['club', 'association', 'team'])
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Get online users.
     */
    public function online(): JsonResponse
    {
        $onlineUsers = User::where('last_login_at', '>=', now()->subMinutes(30))
            ->with(['club', 'association', 'team'])
            ->orderBy('last_login_at', 'desc')
            ->get();

        return response()->json([
            'data' => UserResource::collection($onlineUsers)
        ]);
    }

    /**
     * Get recently active users.
     */
    public function recentlyActive(): JsonResponse
    {
        $recentlyActiveUsers = User::where('last_login_at', '>=', now()->subDays(7))
            ->with(['club', 'association', 'team'])
            ->orderBy('last_login_at', 'desc')
            ->get();

        return response()->json([
            'data' => UserResource::collection($recentlyActiveUsers)
        ]);
    }
} 