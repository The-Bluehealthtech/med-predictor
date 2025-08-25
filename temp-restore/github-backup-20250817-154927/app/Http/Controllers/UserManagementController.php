<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Club;
use App\Models\Association;
use App\Models\Role;
use App\Services\FifaConnectService;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserManagementController extends Controller
{
    use AuthorizesRequests;
    
    protected $fifaConnectService;

    public function __construct(FifaConnectService $fifaConnectService)
    {
        $this->fifaConnectService = $fifaConnectService;
        $this->middleware('auth');
        $this->middleware('role:system_admin,association_admin');
    }

    /**
     * Display the user management dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'total_users' => $this->getEntityUsers()->count(),
            'active_users' => $this->getEntityUsers()->where('status', 'active')->count(),
            'inactive_users' => $this->getEntityUsers()->where('status', 'inactive')->count(),
            'suspended_users' => $this->getEntityUsers()->where('status', 'suspended')->count(),
        ];

        $recentUsers = $this->getEntityUsers()
            ->with(['club', 'association'])
            ->latest()
            ->take(10)
            ->get();

        $usersByRole = $this->getEntityUsers()
            ->selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role');

        $usersByStatus = $this->getEntityUsers()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('modules.user-management.dashboard', compact(
            'stats', 
            'recentUsers', 
            'usersByRole', 
            'usersByStatus'
        ));
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = $this->getEntityUsers()->with(['club', 'association']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('fifa_connect_id', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by entity type
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        $roles = $this->getAvailableRoles();
        $statuses = ['active', 'inactive', 'suspended'];
        $entityTypes = ['club', 'association'];

        return view('modules.user-management.index', compact(
            'users', 
            'roles', 
            'statuses', 
            'entityTypes'
        ));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::active()->orderBy('display_name')->get();
        $clubs = Club::orderBy('name')->get();
        $associations = Association::orderBy('name')->get();
        
        return view('modules.user-management.create', compact('roles', 'clubs', 'associations'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:' . implode(',', array_keys($this->getAvailableRoles())),
            'club_id' => 'nullable|exists:clubs,id',
            'association_id' => 'nullable|exists:associations,id',
            'permissions' => 'array',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Generate FIFA Connect ID
            $fifaConnectId = $this->generateFifaConnectId($request->role);

            // Use custom permissions if provided, otherwise use default permissions
            $permissions = $request->has('permissions') && is_array($request->permissions) 
                ? $request->permissions 
                : $this->getDefaultPermissions($request->role);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'club_id' => $request->club_id,
                'association_id' => $request->association_id,
                'fifa_connect_id' => $fifaConnectId,
                'permissions' => $permissions,
                'status' => $request->status,
                'login_count' => 0,
            ]);

            // Sync with FIFA Connect if applicable
            if ($this->shouldSyncWithFifa($request->role)) {
                $this->syncUserWithFifa($user);
            }

            // Log user creation
            AuditTrailService::logModelChange('create', $user, null, $user->toArray(), "User {$user->name} created");

            return redirect()->route('user-management.index')
                ->with('success', 'User created successfully with FIFA Connect ID: ' . $fifaConnectId);

        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create user. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        $user->load(['club', 'association']);
        
        $loginHistory = $this->getLoginHistory($user);
        $activityLog = $this->getActivityLog($user);
        
        return view('modules.user-management.show', compact('user', 'loginHistory', 'activityLog'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $roles = $this->getAvailableRoles();
        $clubs = Club::orderBy('name')->get();
        $associations = Association::orderBy('name')->get();
        
        return view('modules.user-management.edit', compact('user', 'roles', 'clubs', 'associations'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:' . implode(',', array_keys($this->getAvailableRoles())),
            'entity_type' => 'required_if:role,club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical|in:club,association',
            'entity_id' => 'required_if:entity_type,club,association|exists:' . $this->getEntityTable($request->entity_type) . ',id',
            'permissions' => 'array',
            'status' => 'required|in:active,inactive,suspended',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $oldRole = $user->role;
            $oldEntityType = $user->entity_type;

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'entity_type' => $request->entity_type,
                'entity_id' => $request->entity_id,
                'permissions' => $request->permissions ?? $this->getDefaultPermissions($request->role),
                'status' => $request->status,
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            // Regenerate FIFA Connect ID if role or entity changed
            if ($oldRole !== $request->role || $oldEntityType !== $request->entity_type) {
                $newFifaConnectId = $this->generateFifaConnectId($request->role);
                $user->update(['fifa_connect_id' => $newFifaConnectId]);
                
                // Sync with FIFA Connect
                if ($this->shouldSyncWithFifa($request->role)) {
                    $this->syncUserWithFifa($user);
                }
            }

            // Log user update
            AuditTrailService::logModelChange('update', $user, $user->getOriginal(), $user->toArray(), "User {$user->name} updated");
            
            // Log role change if it changed
            if ($oldRole !== $request->role) {
                AuditTrailService::logRoleAssignment($user, $oldRole, $request->role);
            }

            return redirect()->route('user-management.index')
                ->with('success', 'User updated successfully');

        } catch (\Exception $e) {
            Log::error('User update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update user. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        try {
            // Don't allow deletion of system admin or self
            if ($user->isSystemAdmin() || $user->id === auth()->id()) {
                return back()->with('error', 'Cannot delete this user.');
            }

            // Log user deletion before deleting
            AuditTrailService::logModelChange('delete', $user, $user->toArray(), null, "User {$user->name} deleted");

            $user->delete();

            return redirect()->route('user-management.index')
                ->with('success', 'User deleted successfully');

        } catch (\Exception $e) {
            Log::error('User deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Bulk operations on users
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,suspend,delete',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $users = User::whereIn('id', $request->user_ids)->get();
        $action = $request->action;

        try {
            foreach ($users as $user) {
                if ($user->isSystemAdmin() || $user->id === auth()->id()) {
                    continue; // Skip system admin and current user
                }

                switch ($action) {
                    case 'activate':
                        $user->update(['status' => 'active']);
                        break;
                    case 'deactivate':
                        $user->update(['status' => 'inactive']);
                        break;
                    case 'suspend':
                        $user->update(['status' => 'suspended']);
                        break;
                    case 'delete':
                        $user->delete();
                        break;
                }
            }

            // Log bulk operation
            AuditTrailService::logBulkOperation(auth()->user(), $action, 'users', count($users), true);

            return back()->with('success', ucfirst($action) . ' completed for ' . count($users) . ' users');

        } catch (\Exception $e) {
            Log::error('Bulk user action failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to perform bulk action. Please try again.');
        }
    }

    /**
     * Export users
     */
    public function export(Request $request)
    {
        $users = $this->getEntityUsers()
            ->with(['club', 'association'])
            ->get()
            ->map(function ($user) {
                return [
                    'ID' => $user->id,
                    'Name' => $user->name,
                    'Email' => $user->email,
                    'Role' => $user->getRoleDisplay(),
                    'Entity Type' => $user->getEntityTypeDisplay(),
                    'Entity Name' => $user->getEntityName(),
                    'FIFA Connect ID' => $user->fifa_connect_id,
                    'Status' => ucfirst($user->status),
                    'Created At' => $user->created_at->format('Y-m-d H:i:s'),
                    'Last Login' => $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
                    'Login Count' => $user->login_count,
                ];
            });

        // Log data export
        AuditTrailService::logDataExport(auth()->user(), 'users', $users->count());

        $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            if ($users->isNotEmpty()) {
                fputcsv($file, array_keys($users->first()));
            }
            
            // Add data
            foreach ($users as $user) {
                fputcsv($file, $user);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get available roles based on current user permissions
     */
    private function getAvailableRoles(): array
    {
        $user = auth()->user();
        
        if ($user->isSystemAdmin()) {
            return [
                'system_admin' => 'System Administrator',
                'club_admin' => 'Club Administrator',
                'club_manager' => 'Club Manager',
                'club_medical' => 'Club Medical Staff',
                'association_admin' => 'Association Administrator',
                'association_registrar' => 'Association Registrar',
                'association_medical' => 'Association Medical Director',
                'referee' => 'Referee',
                'assistant_referee' => 'Assistant Referee',
                'fourth_official' => 'Fourth Official',
                'var_official' => 'VAR Official',
                'match_commissioner' => 'Match Commissioner',
                'match_official' => 'Match Official',
                'team_doctor' => 'Team Doctor',
                'physiotherapist' => 'Physiotherapist',
                'sports_scientist' => 'Sports Scientist',
                'player' => 'Player',
            ];
        }

        if ($user->isAssociationAdmin()) {
            return [
                'association_admin' => 'Association Administrator',
                'association_registrar' => 'Association Registrar',
                'association_medical' => 'Association Medical Director',
                'club_admin' => 'Club Administrator',
                'club_manager' => 'Club Manager',
                'club_medical' => 'Club Medical Staff',
                'referee' => 'Referee',
                'assistant_referee' => 'Assistant Referee',
                'fourth_official' => 'Fourth Official',
                'var_official' => 'VAR Official',
                'match_commissioner' => 'Match Commissioner',
                'match_official' => 'Match Official',
                'team_doctor' => 'Team Doctor',
                'physiotherapist' => 'Physiotherapist',
                'sports_scientist' => 'Sports Scientist',
            ];
        }

        return [];
    }

    /**
     * Get default permissions for a role
     */
    private function getDefaultPermissions(string $role): array
    {
        return match($role) {
            'system_admin' => [
                'player_registration_access',
                'competition_management_access',
                'healthcare_access',
                'system_administration',
                'user_management',
                'back_office_access',
                'fifa_connect_access',
                'fifa_data_sync',
                'account_request_management',
                'audit_trail_access',
                'role_management',
                'system_configuration',
                'club_management',
                'team_management',
                'association_management',
                'license_management',
                'match_sheet_management',
                'referee_access',
                'player_dashboard_access',
                'health_record_management',
                'league_championship_access',
                'registration_requests_management_access'
            ],
            'club_admin' => [
                'player_registration_access',
                'competition_management_access',
                'healthcare_access',
                'fifa_connect_access',
                'club_management',
                'team_management'
            ],
            'club_manager' => [
                'player_registration_access',
                'competition_management_access',
                'healthcare_access',
                'fifa_connect_access',
                'team_management'
            ],
            'club_medical' => [
                'healthcare_access',
                'health_record_management',
                'fifa_connect_access'
            ],
            'association_admin' => [
                'player_registration_access',
                'competition_management_access',
                'healthcare_access',
                'user_management',
                'fifa_connect_access',
                'fifa_data_sync',
                'account_request_management',
                'association_management',
                'license_management'
            ],
            'association_registrar' => [
                'player_registration_access',
                'competition_management_access',
                'fifa_connect_access',
                'license_management'
            ],
            'association_medical' => [
                'healthcare_access',
                'health_record_management',
                'fifa_connect_access'
            ],
            'referee' => [
                'match_sheet_management',
                'referee_access',
                'fifa_connect_access'
            ],
            'assistant_referee' => [
                'match_sheet_management',
                'fifa_connect_access'
            ],
            'fourth_official' => [
                'match_sheet_management',
                'fifa_connect_access'
            ],
            'var_official' => [
                'match_sheet_management',
                'fifa_connect_access'
            ],
            'match_commissioner' => [
                'match_sheet_management',
                'competition_management_access',
                'fifa_connect_access'
            ],
            'match_official' => [
                'match_sheet_management',
                'fifa_connect_access'
            ],
            'team_doctor' => [
                'healthcare_access',
                'health_record_management',
                'fifa_connect_access'
            ],
            'physiotherapist' => [
                'healthcare_access',
                'fifa_connect_access'
            ],
            'sports_scientist' => [
                'healthcare_access',
                'fifa_connect_access'
            ],
            'player' => [
                'player_dashboard_access',
                'fifa_connect_access'
            ],
            default => []
        };
    }

    /**
     * Generate FIFA Connect ID
     */
    private function generateFifaConnectId(string $role): string
    {
        $prefix = match($role) {
            'system_admin' => 'FIFA_SYS',
            'club_admin' => 'FIFA_CLUB_ADMIN',
            'club_manager' => 'FIFA_CLUB_MGR',
            'club_medical' => 'FIFA_CLUB_MED',
            'association_admin' => 'FIFA_ASSOC_ADMIN',
            'association_registrar' => 'FIFA_ASSOC_REG',
            'association_medical' => 'FIFA_ASSOC_MED',
            'referee' => 'FIFA_REF',
            'assistant_referee' => 'FIFA_ASST_REF',
            'fourth_official' => 'FIFA_4TH_OFF',
            'var_official' => 'FIFA_VAR_OFF',
            'match_commissioner' => 'FIFA_MATCH_COMM',
            'match_official' => 'FIFA_MATCH_OFF',
            'team_doctor' => 'FIFA_TEAM_DOC',
            'physiotherapist' => 'FIFA_PHYSIO',
            'sports_scientist' => 'FIFA_SPORTS_SCI',
            'player' => 'FIFA_PLAYER',
            default => 'FIFA_USER'
        };

        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(6));
        
        return "{$prefix}_{$timestamp}_{$random}";
    }

    /**
     * Get users based on current user's entity
     */
    private function getEntityUsers()
    {
        $user = auth()->user();
        
        if ($user->isSystemAdmin()) {
            return User::query();
        }

        if ($user->isAssociationAdmin()) {
            return User::where(function($query) use ($user) {
                $query->where('association_id', $user->association_id)
                      ->orWhereHas('club', function($clubQuery) use ($user) {
                          $clubQuery->where('association_id', $user->association_id);
                      })
                      ->orWhere('role', 'system_admin');
            });
        }

        return User::query();
    }

    /**
     * Get entity table name
     */
    private function getEntityTable(?string $entityType): string
    {
        return match($entityType) {
            'club' => 'clubs',
            'association' => 'associations',
            default => 'users'
        };
    }

    /**
     * Check if user should be synced with FIFA Connect
     */
    private function shouldSyncWithFifa(string $role): bool
    {
        return in_array($role, [
            'club_admin',
            'club_manager',
            'club_medical',
            'association_admin',
            'association_registrar',
            'association_medical',
            'referee',
            'assistant_referee',
            'fourth_official',
            'var_official',
            'match_commissioner',
            'match_official',
            'team_doctor',
            'physiotherapist',
            'sports_scientist'
        ]);
    }

    /**
     * Sync user with FIFA Connect
     */
    private function syncUserWithFifa(User $user): void
    {
        try {
            // This would integrate with the actual FIFA Connect API
            // For now, we'll just log the sync attempt
            Log::info('FIFA Connect sync attempted', [
                'user_id' => $user->id,
                'fifa_connect_id' => $user->fifa_connect_id,
                'role' => $user->role
            ]);
        } catch (\Exception $e) {
            Log::error('FIFA Connect sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Get user login history
     */
    private function getLoginHistory(User $user): array
    {
        // This would typically come from a login history table
        // For now, return basic info
        return [
            'last_login' => $user->last_login_at,
            'login_count' => $user->login_count,
            'status' => $user->status
        ];
    }

    /**
     * Get user activity log
     */
    private function getActivityLog(User $user): array
    {
        // This would typically come from an activity log table
        // For now, return basic info
        return [
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'status_changes' => []
        ];
    }
} 