<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RBACService
{
    /**
     * Cache key for permissions
     */
    private const PERMISSIONS_CACHE_KEY = 'rbac_permissions';
    private const ROLE_PERMISSIONS_CACHE_KEY = 'rbac_role_permissions';
    private const USER_PERMISSIONS_CACHE_KEY = 'rbac_user_permissions';

    /**
     * Default permissions for the system
     */
    private const DEFAULT_PERMISSIONS = [
        // User Management
        'user_management' => 'Manage users and their roles',
        'user_view' => 'View user information',
        'user_create' => 'Create new users',
        'user_edit' => 'Edit existing users',
        'user_delete' => 'Delete users',
        
        // Role Management
        'role_management' => 'Manage roles and permissions',
        'role_view' => 'View roles',
        'role_create' => 'Create new roles',
        'role_edit' => 'Edit existing roles',
        'role_delete' => 'Delete roles',
        
        // Player Management
        'player_registration_access' => 'Access player registration module',
        'player_view' => 'View player information',
        'player_create' => 'Create new players',
        'player_edit' => 'Edit player information',
        'player_delete' => 'Delete players',
        
        // Competition Management
        'competition_management_access' => 'Access competition management module',
        'competition_view' => 'View competitions',
        'competition_create' => 'Create new competitions',
        'competition_edit' => 'Edit competitions',
        'competition_delete' => 'Delete competitions',
        
        // Healthcare
        'healthcare_access' => 'Access healthcare module',
        'health_record_view' => 'View health records',
        'health_record_create' => 'Create health records',
        'health_record_edit' => 'Edit health records',
        'health_record_delete' => 'Delete health records',
        
        // FIFA Connect
        'fifa_data_sync' => 'Synchronize data with FIFA Connect',
        'fifa_data_view' => 'View FIFA Connect data',
        'fifa_data_edit' => 'Edit FIFA Connect data',
        
        // System Administration
        'system_configuration' => 'Access system configuration',
        'audit_trail_access' => 'Access audit trail',
        'account_request_management' => 'Manage account requests',
        
        // Club Management
        'club_management' => 'Manage clubs',
        'club_view' => 'View club information',
        'club_create' => 'Create new clubs',
        'club_edit' => 'Edit club information',
        'club_delete' => 'Delete clubs',
        
        // Association Management
        'association_management' => 'Manage associations',
        'association_view' => 'View association information',
        'association_create' => 'Create new associations',
        'association_edit' => 'Edit association information',
        'association_delete' => 'Delete associations',
        
        // Match Management
        'match_management' => 'Manage matches',
        'match_view' => 'View match information',
        'match_create' => 'Create new matches',
        'match_edit' => 'Edit match information',
        'match_delete' => 'Delete matches',
        
        // Referee Management
        'referee_management' => 'Manage referees',
        'referee_view' => 'View referee information',
        'referee_create' => 'Create new referees',
        'referee_edit' => 'Edit referee information',
        'referee_delete' => 'Delete referees',
        
        // License Management
        'access-license-management' => 'Access license management module',
        'access-license-validation' => 'Access license validation module',
        'license_validation' => 'Validate licenses',
    ];

    /**
     * Default role permissions mapping
     */
    private const DEFAULT_ROLE_PERMISSIONS = [
        'system_admin' => [
            'user_management', 'user_view', 'user_create', 'user_edit', 'user_delete',
            'role_management', 'role_view', 'role_create', 'role_edit', 'role_delete',
            'player_registration_access', 'player_view', 'player_create', 'player_edit', 'player_delete',
            'competition_management_access', 'competition_view', 'competition_create', 'competition_edit', 'competition_delete',
            'healthcare_access', 'health_record_view', 'health_record_create', 'health_record_edit', 'health_record_delete',
            'fifa_data_sync', 'fifa_data_view', 'fifa_data_edit',
            'system_configuration', 'audit_trail_access', 'account_request_management',
            'club_management', 'club_view', 'club_create', 'club_edit', 'club_delete',
            'association_management', 'association_view', 'association_create', 'association_edit', 'association_delete',
            'match_management', 'match_view', 'match_create', 'match_edit', 'match_delete',
            'referee_management', 'referee_view', 'referee_create', 'referee_edit', 'referee_delete',
        ],
        'association_admin' => [
            'user_management', 'user_view', 'user_create', 'user_edit',
            'role_management', 'role_view', 'role_create', 'role_edit',
            'player_registration_access', 'player_view', 'player_create', 'player_edit',
            'competition_management_access', 'competition_view', 'competition_create', 'competition_edit',
            'healthcare_access', 'health_record_view', 'health_record_create', 'health_record_edit',
            'fifa_data_sync', 'fifa_data_view', 'fifa_data_edit',
            'audit_trail_access', 'account_request_management',
            'club_management', 'club_view', 'club_create', 'club_edit',
            'association_view', 'association_edit',
            'match_management', 'match_view', 'match_create', 'match_edit',
            'referee_management', 'referee_view', 'referee_create', 'referee_edit',
        ],
        'association_registrar' => [
            'user_view', 'user_create', 'user_edit',
            'player_registration_access', 'player_view', 'player_create', 'player_edit',
            'competition_management_access', 'competition_view', 'competition_create', 'competition_edit',
            'healthcare_access', 'health_record_view', 'health_record_create', 'health_record_edit',
            'fifa_data_view', 'fifa_data_edit',
            'club_view', 'club_create', 'club_edit',
            'association_view',
            'match_view', 'match_create', 'match_edit',
            'referee_view', 'referee_create', 'referee_edit',
        ],
        'association_medical' => [
            'user_view',
            'player_registration_access', 'player_view',
            'competition_management_access', 'competition_view',
            'healthcare_access', 'health_record_view', 'health_record_create', 'health_record_edit', 'health_record_delete',
            'fifa_data_view',
            'club_view',
            'association_view',
            'match_view',
            'referee_view',
        ],
        'club_admin' => [
            'user_view', 'user_create', 'user_edit',
            'player_registration_access', 'player_view', 'player_create', 'player_edit',
            'competition_management_access', 'competition_view',
            'healthcare_access', 'health_record_view', 'health_record_create', 'health_record_edit',
            'fifa_data_view',
            'club_view', 'club_edit',
            'association_view',
            'match_view',
            'referee_view',
        ],
        'club_manager' => [
            'user_view',
            'player_registration_access', 'player_view', 'player_create', 'player_edit',
            'competition_management_access', 'competition_view',
            'healthcare_access', 'health_record_view', 'health_record_create', 'health_record_edit',
            'fifa_data_view',
            'club_view',
            'association_view',
            'match_view',
            'referee_view',
        ],
        'club_medical' => [
            'user_view',
            'player_registration_access', 'player_view',
            'competition_management_access', 'competition_view',
            'healthcare_access', 'health_record_view', 'health_record_create', 'health_record_edit', 'health_record_delete',
            'fifa_data_view',
            'club_view',
            'association_view',
            'match_view',
            'referee_view',
        ],
        'referee' => [
            'user_view',
            'player_registration_access', 'player_view',
            'competition_management_access', 'competition_view',
            'healthcare_access', 'health_record_view',
            'fifa_data_view',
            'club_view',
            'association_view',
            'match_view', 'match_edit',
            'referee_view', 'referee_edit',
        ],
        'player' => [
            'user_view',
            'player_registration_access', 'player_view', 'player_edit',
            'competition_management_access', 'competition_view',
            'healthcare_access', 'health_record_view',
            'fifa_data_view',
            'club_view',
            'association_view',
            'match_view',
            'referee_view',
        ],
    ];

    /**
     * Get all available permissions
     */
    public function getAllPermissions(): array
    {
        return Cache::remember(self::PERMISSIONS_CACHE_KEY, 3600, function () {
            return self::DEFAULT_PERMISSIONS;
        });
    }

    /**
     * Get permissions for a specific role
     */
    public function getRolePermissions(string $roleName): array
    {
        $cacheKey = self::ROLE_PERMISSIONS_CACHE_KEY . '_' . $roleName;
        
        return Cache::remember($cacheKey, 3600, function () use ($roleName) {
            // First check database
            $role = Role::where('name', $roleName)->first();
            if ($role && !empty($role->permissions)) {
                return $role->permissions;
            }
            
            // Fallback to default permissions
            return self::DEFAULT_ROLE_PERMISSIONS[$roleName] ?? [];
        });
    }

    /**
     * Get permissions for a specific user
     */
    public function getUserPermissions(User $user): array
    {
        $cacheKey = self::USER_PERMISSIONS_CACHE_KEY . '_' . $user->id;
        
        return Cache::remember($cacheKey, 1800, function () use ($user) {
            $permissions = [];
            
            // System admin has all permissions
            if ($user->isSystemAdmin()) {
                return array_keys(self::DEFAULT_PERMISSIONS);
            }
            
            // Get role-based permissions
            $rolePermissions = $this->getRolePermissions($user->role);
            $permissions = array_merge($permissions, $rolePermissions);
            
            // Get user-specific permissions
            $userPermissions = $user->permissions ?? [];
            if (is_string($userPermissions)) {
                $userPermissions = json_decode($userPermissions, true) ?? [];
            }
            $permissions = array_merge($permissions, $userPermissions);
            
            // Remove duplicates and return
            return array_unique($permissions);
        });
    }

    /**
     * Check if user has a specific permission
     */
    public function userHasPermission(User $user, string $permission): bool
    {
        $userPermissions = $this->getUserPermissions($user);
        return in_array($permission, $userPermissions);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function userHasAnyPermission(User $user, array $permissions): bool
    {
        $userPermissions = $this->getUserPermissions($user);
        return !empty(array_intersect($permissions, $userPermissions));
    }

    /**
     * Check if user has all of the given permissions
     */
    public function userHasAllPermissions(User $user, array $permissions): bool
    {
        $userPermissions = $this->getUserPermissions($user);
        return empty(array_diff($permissions, $userPermissions));
    }

    /**
     * Get permissions for a specific module
     */
    public function getModulePermissions(string $module): array
    {
        $modulePermissions = [
            'user_management' => ['user_management', 'user_view', 'user_create', 'user_edit', 'user_delete'],
            'role_management' => ['role_management', 'role_view', 'role_create', 'role_edit', 'role_delete'],
            'player_registration' => ['player_registration_access', 'player_view', 'player_create', 'player_edit', 'player_delete'],
            'competition_management' => ['competition_management_access', 'competition_view', 'competition_create', 'competition_edit', 'competition_delete'],
            'healthcare' => ['healthcare_access', 'health_record_view', 'health_record_create', 'health_record_edit', 'health_record_delete'],
            'fifa_connect' => ['fifa_data_sync', 'fifa_data_view', 'fifa_data_edit'],
            'system_admin' => ['system_configuration', 'audit_trail_access', 'account_request_management'],
            'club_management' => ['club_management', 'club_view', 'club_create', 'club_edit', 'club_delete'],
            'association_management' => ['association_management', 'association_view', 'association_create', 'association_edit', 'association_delete'],
            'match_management' => ['match_management', 'match_view', 'match_create', 'match_edit', 'match_delete'],
            'referee_management' => ['referee_management', 'referee_view', 'referee_create', 'referee_edit', 'referee_delete'],
        ];
        
        return $modulePermissions[$module] ?? [];
    }

    /**
     * Check if user can access a specific module
     */
    public function userCanAccessModule(User $user, string $module): bool
    {
        $modulePermissions = $this->getModulePermissions($module);
        return $this->userHasAnyPermission($user, $modulePermissions);
    }

    /**
     * Clear all RBAC caches
     */
    public function clearCache(): void
    {
        Cache::forget(self::PERMISSIONS_CACHE_KEY);
        Cache::forget(self::ROLE_PERMISSIONS_CACHE_KEY);
        Cache::forget(self::USER_PERMISSIONS_CACHE_KEY);
        
        // Clear role-specific caches
        $roles = Role::all();
        foreach ($roles as $role) {
            Cache::forget(self::ROLE_PERMISSIONS_CACHE_KEY . '_' . $role->name);
        }
        
        // Clear user-specific caches
        $users = User::all();
        foreach ($users as $user) {
            Cache::forget(self::USER_PERMISSIONS_CACHE_KEY . '_' . $user->id);
        }
        
        Log::info('RBAC cache cleared');
    }

    /**
     * Update role permissions in database
     */
    public function updateRolePermissions(string $roleName, array $permissions): bool
    {
        try {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                return false;
            }
            
            $role->update(['permissions' => $permissions]);
            
            // Clear related caches
            Cache::forget(self::ROLE_PERMISSIONS_CACHE_KEY . '_' . $roleName);
            
            // Clear user caches for this role
            $usersWithRole = User::where('role', $roleName)->get();
            foreach ($usersWithRole as $user) {
                Cache::forget(self::USER_PERMISSIONS_CACHE_KEY . '_' . $user->id);
            }
            
            Log::info("Role permissions updated for role: {$roleName}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update role permissions for {$roleName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user permissions
     */
    public function updateUserPermissions(User $user, array $permissions): bool
    {
        try {
            $user->update(['permissions' => $permissions]);
            
            // Clear user cache
            Cache::forget(self::USER_PERMISSIONS_CACHE_KEY . '_' . $user->id);
            
            Log::info("User permissions updated for user: {$user->id}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update user permissions for user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all roles with their permissions
     */
    public function getAllRolesWithPermissions(): array
    {
        $roles = Role::all();
        $result = [];
        
        foreach ($roles as $role) {
            $result[$role->name] = [
                'display_name' => $role->display_name,
                'description' => $role->description,
                'permissions' => $this->getRolePermissions($role->name),
                'is_system_role' => $role->is_system_role,
                'is_active' => $role->is_active,
                'users_count' => $role->users()->count(),
            ];
        }
        
        return $result;
    }

    /**
     * Validate permission exists
     */
    public function isValidPermission(string $permission): bool
    {
        return array_key_exists($permission, self::DEFAULT_PERMISSIONS);
    }

    /**
     * Get permission description
     */
    public function getPermissionDescription(string $permission): string
    {
        return self::DEFAULT_PERMISSIONS[$permission] ?? 'Unknown permission';
    }
}






