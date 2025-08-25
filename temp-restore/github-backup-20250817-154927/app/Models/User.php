<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role', // club_admin, club_manager, club_medical, association_admin, association_registrar, association_medical, system_admin
        'entity_type', // club, association (legacy field)
        'entity_id', // club_id or association_id (legacy field)
        'club_id', // direct club relationship
        'association_id', // direct association relationship
        'team_id', // team relationship for team officials
        'player_id', // player relationship for player portal
        'fifa_connect_id',
        'permissions',
        'preferences',
        'status',
        'last_login_at',
        'login_count',
        'timezone',
        'language',
        'notifications_email',
        'notifications_sms',
        'profile_picture_url',
        'profile_picture_alt'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'preferences' => 'array',
            'last_login_at' => 'datetime',
            'login_count' => 'integer'
        ];
    }

    // Relationships
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class, 'association_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    // Legacy relationships for backward compatibility
    public function legacyClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'entity_id');
    }

    public function legacyAssociation(): BelongsTo
    {
        return $this->belongsTo(Association::class, 'entity_id');
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    // Role methods
    public function isClubAdmin(): bool
    {
        return $this->role === 'club_admin';
    }

    public function isClubManager(): bool
    {
        return $this->role === 'club_manager';
    }

    public function isClubMedical(): bool
    {
        return $this->role === 'club_medical';
    }

    public function isAssociationAdmin(): bool
    {
        return $this->role === 'association_admin';
    }

    public function isAssociationRegistrar(): bool
    {
        return $this->role === 'association_registrar';
    }

    public function isAssociationMedical(): bool
    {
        return $this->role === 'association_medical';
    }

    public function isSystemAdmin(): bool
    {
        return $this->role === 'system_admin';
    }

    public function isPlayer(): bool
    {
        return $this->role === 'player';
    }

    public function isClubUser(): bool
    {
        return in_array($this->role, ['club_admin', 'club_manager', 'club_medical']);
    }

    public function isAssociationUser(): bool
    {
        return in_array($this->role, ['association_admin', 'association_registrar', 'association_medical']);
    }

    public function isReferee(): bool
    {
        return $this->role === 'referee';
    }

    public function isTeamOfficial(): bool
    {
        return in_array($this->role, ['club_admin', 'club_manager']);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['system_admin', 'association_admin']);
    }

    public function hasPermission(string $permission): bool
    {
        // System admin has access to everything
        if ($this->isSystemAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    public function canAccessModule(string $module): bool
    {
        // System admin has access to all modules
        if ($this->isSystemAdmin()) {
            return true;
        }

        // Check specific module permissions
        return match($module) {
            'player_registration' => $this->hasPermission('player_registration_access'),
            'competition_management' => $this->hasPermission('competition_management_access'),
            'healthcare' => $this->hasPermission('healthcare_access'),
            'user_management' => $this->hasPermission('user_management'),
            'back_office' => $this->hasPermission('back_office_access'),
            'fifa_connect' => $this->hasPermission('fifa_connect_access'),
            'account_requests' => $this->hasPermission('account_request_management'),
            'audit_trail' => $this->hasPermission('audit_trail_access'),
            'role_management' => $this->hasPermission('role_management'),
            'system_configuration' => $this->hasPermission('system_configuration'),
            'club_management' => $this->hasPermission('club_management'),
            'team_management' => $this->hasPermission('team_management'),
            'association_management' => $this->hasPermission('association_management'),
            'license_management' => $this->hasPermission('license_management'),
            'match_sheet' => $this->hasPermission('match_sheet_management'),
            'referee' => $this->hasPermission('referee_access'),
            'player_dashboard' => $this->hasPermission('player_dashboard_access'),
            default => false
        };
    }

    /**
     * Check if user can access a specific feature
     */
    public function canAccessFeature(string $feature): bool
    {
        // System admin can access all features
        if ($this->isSystemAdmin()) {
            return true;
        }

        return match($feature) {
            'create_users' => $this->hasPermission('user_management'),
            'edit_users' => $this->hasPermission('user_management'),
            'delete_users' => $this->hasPermission('user_management'),
            'approve_account_requests' => $this->hasPermission('account_request_management'),
            'reject_account_requests' => $this->hasPermission('account_request_management'),
            'view_audit_trail' => $this->hasPermission('audit_trail_access'),
            'manage_roles' => $this->hasPermission('role_management'),
            'system_settings' => $this->hasPermission('system_configuration'),
            'fifa_data_sync' => $this->hasPermission('fifa_data_sync'),
            'create_players' => $this->hasPermission('player_registration_access'),
            'edit_players' => $this->hasPermission('player_registration_access'),
            'delete_players' => $this->hasPermission('player_registration_access'),
            'create_competitions' => $this->hasPermission('competition_management_access'),
            'edit_competitions' => $this->hasPermission('competition_management_access'),
            'delete_competitions' => $this->hasPermission('competition_management_access'),
            'create_health_records' => $this->hasPermission('healthcare_access'),
            'edit_health_records' => $this->hasPermission('healthcare_access'),
            'delete_health_records' => $this->hasPermission('healthcare_access'),
            'view_all_clubs' => $this->isSystemAdmin() || $this->isAssociationAdmin(),
            'view_all_associations' => $this->isSystemAdmin(),
            'view_all_players' => $this->isSystemAdmin() || $this->isAssociationAdmin(),
            default => false
        };
    }

    public function getEntityName(): string
    {
        // Use new direct relationships first
        if ($this->club) {
            return $this->club->name;
        }

        if ($this->association) {
            return $this->association->name;
        }

        // Fallback to legacy relationships
        if ($this->entity_type === 'club' && $this->legacyClub) {
            return $this->legacyClub->name;
        }

        if ($this->entity_type === 'association' && $this->legacyAssociation) {
            return $this->legacyAssociation->name;
        }

        return 'System';
    }

    public function getEntityTypeDisplay(): string
    {
        return match($this->entity_type) {
            'club' => 'Club',
            'association' => 'Football Association',
            default => 'System'
        };
    }

    public function getRoleDisplay(): string
    {
        return match($this->role) {
            'club_admin' => 'Club Administrator',
            'club_manager' => 'Club Manager',
            'club_medical' => 'Club Medical Staff',
            'association_admin' => 'Association Administrator',
            'association_registrar' => 'Association Registrar',
            'association_medical' => 'Association Medical Director',
            'system_admin' => 'System Administrator',
            'referee' => 'Referee',
            default => 'User'
        };
    }

    // Profile Picture Methods
    public function getProfilePictureUrl(): string
    {
        if ($this->profile_picture_url) {
            return $this->profile_picture_url;
        }
        
        // Return a default profile picture based on role or gender
        return asset('images/defaults/profile-picture.png');
    }

    public function getProfilePictureAlt(): string
    {
        if ($this->profile_picture_alt) {
            return $this->profile_picture_alt;
        }
        
        return $this->name . ' Profile Picture';
    }

    public function hasProfilePicture(): bool
    {
        return !empty($this->profile_picture_url);
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }

    public function getInitials(): string
    {
        $names = explode(' ', $this->name);
        $initials = '';
        
        foreach ($names as $name) {
            if (!empty($name)) {
                $initials .= strtoupper(substr($name, 0, 1));
            }
        }
        
        return substr($initials, 0, 2);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    // Audit Trail Methods
    public function getAuditIdentifier(): string
    {
        return "User:{$this->id}";
    }

    public function getAuditDisplayName(): string
    {
        return $this->name;
    }

    public function getAuditType(): string
    {
        return 'user';
    }

    public function getAuditData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'club_id' => $this->club_id,
            'association_id' => $this->association_id,
            'team_id' => $this->team_id,
        ];
    }
}
