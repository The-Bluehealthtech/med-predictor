<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleManagementController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:system_admin');
    }

    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $query = Role::with(['creator', 'updater']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            if ($request->type === 'system') {
                $query->systemRoles();
            } elseif ($request->type === 'custom') {
                $query->customRoles();
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $roles = $query->orderBy('is_system_role', 'desc')
                      ->orderBy('display_name')
                      ->paginate(20);

        $stats = [
            'total_roles' => Role::count(),
            'system_roles' => Role::systemRoles()->count(),
            'custom_roles' => Role::customRoles()->count(),
            'active_roles' => Role::active()->count(),
            'roles_with_users' => Role::whereHas('users')->count(),
        ];

        return view('modules.role-management.index', compact('roles', 'stats'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $availablePermissions = $this->getAvailablePermissions();
        $systemRoles = Role::systemRoles()->get();
        
        return view('modules.role-management.create', compact('availablePermissions', 'systemRoles'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->getAvailablePermissions())),
            'fifa_connect_id_prefix' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'permissions' => $request->permissions ?? [],
                'fifa_connect_id_prefix' => $request->fifa_connect_id_prefix,
                'is_active' => $request->boolean('is_active', true),
                'is_system_role' => false,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            Log::info('Role created', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'created_by' => auth()->id()
            ]);

            return redirect()->route('role-management.index')
                ->with('success', 'Role "' . $role->display_name . '" created successfully');

        } catch (\Exception $e) {
            Log::error('Role creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create role. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load(['creator', 'updater', 'users']);
        
        $usersWithRole = $role->users()->with(['club', 'association'])->paginate(10);
        
        return view('modules.role-management.show', compact('role', 'usersWithRole'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        // Temporarily allowing editing of all roles for testing
        // if ($role->is_system_role) {
        //     return back()->with('error', 'System roles cannot be edited');
        // }

        $availablePermissions = $this->getAvailablePermissions();
        
        return view('modules.role-management.edit', compact('role', 'availablePermissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        // Temporarily allowing updating of all roles for testing
        // if ($role->is_system_role) {
        //     return back()->with('error', 'System roles cannot be modified');
        // }

        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->getAvailablePermissions())),
            'fifa_connect_id_prefix' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $role->update([
                'display_name' => $request->display_name,
                'description' => $request->description,
                'permissions' => $request->permissions ?? [],
                'fifa_connect_id_prefix' => $request->fifa_connect_id_prefix,
                'is_active' => $request->boolean('is_active', true),
                'updated_by' => auth()->id(),
            ]);

            Log::info('Role updated', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('role-management.index')
                ->with('success', 'Role "' . $role->display_name . '" updated successfully');

        } catch (\Exception $e) {
            Log::error('Role update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update role. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        if (!$role->isDeletable()) {
            return back()->with('error', 'This role cannot be deleted. It may be a system role or has users assigned to it.');
        }

        try {
            $roleName = $role->display_name;
            $role->delete();

            Log::info('Role deleted', [
                'role_name' => $roleName,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('role-management.index')
                ->with('success', 'Role "' . $roleName . '" deleted successfully');

        } catch (\Exception $e) {
            Log::error('Role deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete role. Please try again.');
        }
    }

    /**
     * Toggle role active status
     */
    public function toggleStatus(Role $role)
    {
        if ($role->is_system_role) {
            return back()->with('error', 'System roles cannot be deactivated');
        }

        try {
            $role->update([
                'is_active' => !$role->is_active,
                'updated_by' => auth()->id(),
            ]);

            $status = $role->is_active ? 'activated' : 'deactivated';
            
            Log::info('Role status toggled', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'new_status' => $status,
                'updated_by' => auth()->id()
            ]);

            return back()->with('success', 'Role "' . $role->display_name . '" ' . $status . ' successfully');

        } catch (\Exception $e) {
            Log::error('Role status toggle failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update role status. Please try again.');
        }
    }

    /**
     * Duplicate a role
     */
    public function duplicate(Role $role)
    {
        try {
            $newRole = $role->replicate();
            $newRole->name = $role->name . '_copy_' . time();
            $newRole->display_name = $role->display_name . ' (Copy)';
            $newRole->is_system_role = false;
            $newRole->created_by = auth()->id();
            $newRole->updated_by = auth()->id();
            $newRole->save();

            Log::info('Role duplicated', [
                'original_role_id' => $role->id,
                'new_role_id' => $newRole->id,
                'duplicated_by' => auth()->id()
            ]);

            return redirect()->route('role-management.edit', $newRole)
                ->with('success', 'Role duplicated successfully. Please review and save the new role.');

        } catch (\Exception $e) {
            Log::error('Role duplication failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to duplicate role. Please try again.');
        }
    }

    /**
     * Get available permissions for roles
     */
    private function getAvailablePermissions(): array
    {
        return [
            // Module Access Permissions
            'player_registration_access' => 'Player Registration Access',
            'competition_management_access' => 'Competition Management Access',
            'healthcare_access' => 'Healthcare Access',
            'match_sheet_management' => 'Match Sheet Management',
            'referee_access' => 'Referee Access',
            'health_record_management' => 'Health Record Management',
            
            // Administrative Permissions
            'user_management' => 'User Management',
            'system_administration' => 'System Administration',
            'back_office_access' => 'Back Office Access',
            
            // FIFA Connect Permissions
            'fifa_connect_access' => 'FIFA Connect Access',
            'fifa_data_sync' => 'FIFA Data Sync',
        ];
    }
}
