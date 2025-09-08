<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_system_role',
        'is_active',
        'fifa_connect_id_prefix',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system_role' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystemRoles($query)
    {
        return $query->where('is_system_role', true);
    }

    public function scopeCustomRoles($query)
    {
        return $query->where('is_system_role', false);
    }

    // Methods
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return !empty(array_intersect($permissions, $this->permissions ?? []));
    }

    public function hasAllPermissions(array $permissions): bool
    {
        return empty(array_diff($permissions, $this->permissions ?? []));
    }

    public function addPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    public function removePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_diff($permissions, [$permission]);
        $this->update(['permissions' => array_values($permissions)]);
    }

    public function getPermissionsList(): array
    {
        return $this->permissions ?? [];
    }

    public function getPermissionsCount(): int
    {
        return count($this->permissions ?? []);
    }

    public function getUsersCount(): int
    {
        return $this->users()->count();
    }

    public function isDeletable(): bool
    {
        return !$this->is_system_role && $this->getUsersCount() === 0;
    }

    public function getDisplayName(): string
    {
        return $this->display_name ?: $this->name;
    }

    public function getFifaConnectIdPrefix(): string
    {
        return $this->fifa_connect_id_prefix ?: 'FIFA_USER';
    }

    // Static methods for default roles
    public static function getDefaultRoles(): array
    {
        return [
            'system_admin' => [
                'display_name' => 'System Administrator',
                'description' => 'Full system access with all permissions',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access',
                    'healthcare_access',
                    'system_administration',
                    'user_management',
                    'back_office_access',
                    'fifa_connect_access',
                    'fifa_data_sync'
                ],
                'fifa_connect_id_prefix' => 'FIFA_SYS',
                'is_system_role' => true
            ],
            'club_admin' => [
                'display_name' => 'Club Administrator',
                'description' => 'Club management with administrative access',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access',
                    'healthcare_access',
                    'fifa_connect_access'
                ],
                'fifa_connect_id_prefix' => 'FIFA_CLUB_ADMIN',
                'is_system_role' => true
            ],
            'club_manager' => [
                'display_name' => 'Club Manager',
                'description' => 'Club operations management',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access',
                    'healthcare_access',
                    'fifa_connect_access'
                ],
                'fifa_connect_id_prefix' => 'FIFA_CLUB_MGR',
                'is_system_role' => true
            ],
            'club_medical' => [
                'display_name' => 'Club Medical Staff',
                'description' => 'Healthcare and medical record management',
                'permissions' => [
                    'healthcare_access',
                    'health_record_management'
                ],
                'fifa_connect_id_prefix' => 'FIFA_CLUB_MED',
                'is_system_role' => true
            ],
            'association_admin' => [
                'display_name' => 'Association Administrator',
                'description' => 'Football association management',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access',
                    'healthcare_access',
                    'user_management',
                    'fifa_connect_access',
                    'fifa_data_sync'
                ],
                'fifa_connect_id_prefix' => 'FIFA_ASSOC_ADMIN',
                'is_system_role' => true
            ],
            'association_registrar' => [
                'display_name' => 'Association Registrar',
                'description' => 'Player registration and competition management',
                'permissions' => [
                    'player_registration_access',
                    'competition_management_access',
                    'fifa_connect_access'
                ],
                'fifa_connect_id_prefix' => 'FIFA_ASSOC_REG',
                'is_system_role' => true
            ],
            'association_medical' => [
                'display_name' => 'Association Medical Director',
                'description' => 'Healthcare oversight and medical records',
                'permissions' => [
                    'healthcare_access',
                    'health_record_management'
                ],
                'fifa_connect_id_prefix' => 'FIFA_ASSOC_MED',
                'is_system_role' => true
            ],
            'referee' => [
                'display_name' => 'Referee',
                'description' => 'Match officiating and referee functions',
                'permissions' => [
                    'match_sheet_management',
                    'referee_access'
                ],
                'fifa_connect_id_prefix' => 'FIFA_REF',
                'is_system_role' => true
            ],
            'assistant_referee' => [
                'display_name' => 'Assistant Referee',
                'description' => 'Assistant referee functions',
                'permissions' => [
                    'match_sheet_management'
                ],
                'fifa_connect_id_prefix' => 'FIFA_ASST_REF',
                'is_system_role' => true
            ],
            'fourth_official' => [
                'display_name' => 'Fourth Official',
                'description' => 'Fourth official functions',
                'permissions' => [
                    'match_sheet_management'
                ],
                'fifa_connect_id_prefix' => 'FIFA_4TH_OFF',
                'is_system_role' => true
            ],
            'var_official' => [
                'display_name' => 'VAR Official',
                'description' => 'Video Assistant Referee functions',
                'permissions' => [
                    'match_sheet_management'
                ],
                'fifa_connect_id_prefix' => 'FIFA_VAR_OFF',
                'is_system_role' => true
            ],
            'match_commissioner' => [
                'display_name' => 'Match Commissioner',
                'description' => 'Match oversight and competition management',
                'permissions' => [
                    'match_sheet_management',
                    'competition_management_access'
                ],
                'fifa_connect_id_prefix' => 'FIFA_MATCH_COMM',
                'is_system_role' => true
            ],
            'match_official' => [
                'display_name' => 'Match Official',
                'description' => 'General match official functions',
                'permissions' => [
                    'match_sheet_management'
                ],
                'fifa_connect_id_prefix' => 'FIFA_MATCH_OFF',
                'is_system_role' => true
            ],
            'team_doctor' => [
                'display_name' => 'Team Doctor',
                'description' => 'Team medical care and health records',
                'permissions' => [
                    'healthcare_access',
                    'health_record_management'
                ],
                'fifa_connect_id_prefix' => 'FIFA_TEAM_DOC',
                'is_system_role' => true
            ],
            'physiotherapist' => [
                'display_name' => 'Physiotherapist',
                'description' => 'Physical therapy and healthcare',
                'permissions' => [
                    'healthcare_access'
                ],
                'fifa_connect_id_prefix' => 'FIFA_PHYSIO',
                'is_system_role' => true
            ],
            'sports_scientist' => [
                'display_name' => 'Sports Scientist',
                'description' => 'Sports science and performance analysis',
                'permissions' => [
                    'healthcare_access'
                ],
                'fifa_connect_id_prefix' => 'FIFA_SPORTS_SCI',
                'is_system_role' => true
            ]
        ];
    }

    public static function createDefaultRoles(): void
    {
        $defaultRoles = self::getDefaultRoles();
        
        foreach ($defaultRoles as $name => $roleData) {
            self::firstOrCreate(
                ['name' => $name],
                array_merge($roleData, [
                    'is_active' => true,
                    'created_by' => null, // Remove foreign key constraint during seeding
                    'updated_by' => null
                ])
            );
        }
    }
} 