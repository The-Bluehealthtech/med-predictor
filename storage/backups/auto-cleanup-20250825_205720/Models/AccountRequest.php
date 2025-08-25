<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class AccountRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'organization_name',
        'organization_type',
        'football_type',
        'fifa_connect_type',
        'country',
        'city',
        'description',
        'status',
        'admin_notes',
        'contacted_at',
        'approved_at',
        'rejected_at',
        'approved_by',
        'rejected_by',
        'association_id',
        'generated_username',
        'generated_password',
        'user_created_at'
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'user_created_at' => 'datetime',
    ];

    // Football types
    const FOOTBALL_TYPES = [
        '11-a-side' => 'Football 11 à 11',
        'futsal' => 'Futsal',
        'women' => 'Football Féminin',
        'beach-soccer' => 'Beach Soccer'
    ];

    // FIFA Connect types
    const FIFA_CONNECT_TYPES = [
        'club_admin' => 'Administrateur de Club',
        'club_manager' => 'Manager de Club',
        'club_medical' => 'Staff Médical de Club',
        'association_admin' => 'Administrateur d\'Association',
        'association_registrar' => 'Registraire d\'Association',
        'association_medical' => 'Staff Médical d\'Association',
        'referee' => 'Arbitre',
        'assistant_referee' => 'Arbitre Assistant',
        'fourth_official' => '4ème Arbitre',
        'var_official' => 'Officiel VAR',
        'match_commissioner' => 'Commissaire de Match',
        'match_official' => 'Officiel de Match',
        'team_doctor' => 'Médecin d\'Équipe',
        'physiotherapist' => 'Physiothérapeute',
        'sports_scientist' => 'Scientifique du Sport'
    ];

    // Organization types (FIFA Connect compatible)
    const ORGANIZATION_TYPES = [
        'club' => 'Club',
        'association' => 'Association',
        'federation' => 'Fédération',
        'league' => 'Ligue',
        'academy' => 'Académie',
        'medical_center' => 'Centre Médical',
        'training_center' => 'Centre d\'Entraînement',
        'other' => 'Autre'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONTACTED = 'contacted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the football type label
     */
    public function getFootballTypeLabelAttribute(): string
    {
        return self::FOOTBALL_TYPES[$this->football_type] ?? $this->football_type;
    }

    /**
     * Get the organization type label
     */
    public function getOrganizationTypeLabelAttribute(): string
    {
        return self::ORGANIZATION_TYPES[$this->organization_type] ?? $this->organization_type;
    }

    /**
     * Get the FIFA Connect type label
     */
    public function getFifaConnectTypeLabelAttribute(): string
    {
        return self::FIFA_CONNECT_TYPES[$this->fifa_connect_type] ?? $this->fifa_connect_type;
    }

    /**
     * Get the full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if the request is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the request is contacted
     */
    public function isContacted(): bool
    {
        return $this->status === self::STATUS_CONTACTED;
    }

    /**
     * Check if the request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if a user account has been created
     */
    public function hasUserAccount(): bool
    {
        return !is_null($this->user_created_at);
    }

    /**
     * Get the approver user
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the rejector user
     */
    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the association
     */
    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for contacted requests
     */
    public function scopeContacted($query)
    {
        return $query->where('status', self::STATUS_CONTACTED);
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope for requests that need approval
     */
    public function scopeNeedsApproval($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONTACTED]);
    }

    /**
     * Mark as contacted
     */
    public function markAsContacted(User $admin): void
    {
        $this->update([
            'status' => self::STATUS_CONTACTED,
            'contacted_at' => now(),
            'admin_notes' => $this->admin_notes . "\n\nContacted by " . $admin->name . " on " . now()->format('Y-m-d H:i:s')
        ]);

        Log::info("Account request {$this->id} marked as contacted by admin {$admin->id}");
    }

    /**
     * Approve the request
     */
    public function approve(User $admin, string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'admin_notes' => $this->admin_notes . "\n\nApproved by " . $admin->name . " on " . now()->format('Y-m-d H:i:s') . ($notes ? "\nNotes: " . $notes : '')
        ]);

        Log::info("Account request {$this->id} approved by admin {$admin->id}");
    }

    /**
     * Reject the request
     */
    public function reject(User $admin, string $reason): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejected_by' => $admin->id,
            'admin_notes' => $this->admin_notes . "\n\nRejected by " . $admin->name . " on " . now()->format('Y-m-d H:i:s') . "\nReason: " . $reason
        ]);

        Log::info("Account request {$this->id} rejected by admin {$admin->id}. Reason: {$reason}");
    }

    /**
     * Generate username for the new user
     */
    public function generateUsername(): string
    {
        $base = strtolower($this->first_name . '.' . $this->last_name);
        $base = preg_replace('/[^a-z0-9.]/', '', $base);
        
        // Check if username exists
        $username = $base;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
    }

    /**
     * Generate a secure password
     */
    public function generatePassword(): string
    {
        return \Illuminate\Support\Str::random(12);
    }

    /**
     * Create user account from this request
     */
    public function createUserAccount(): User
    {
        $username = $this->generateUsername();
        $password = $this->generatePassword();
        
        // Generate FIFA Connect ID based on role and organization type
        $fifaConnectId = $this->generateFifaConnectId();
        
        // Get default permissions for the role
        $permissions = $this->getDefaultPermissions();
        
        $user = User::create([
            'name' => $this->full_name,
            'email' => $this->email,
            'username' => $username,
            'password' => bcrypt($password),
            'phone' => $this->phone,
            'role' => $this->getDefaultRole(),
            'association_id' => $this->association_id,
            'club_id' => $this->club_id,
            'fifa_connect_id' => $fifaConnectId,
            'permissions' => $permissions,
            'status' => 'active',
            'email_verified_at' => now(), // Auto-verify since approved by admin
            'timezone' => 'UTC',
            'language' => 'en',
        ]);

        // Update the request with generated credentials
        $this->update([
            'generated_username' => $username,
            'generated_password' => $password,
            'user_created_at' => now(),
        ]);

        Log::info("User account created for account request {$this->id}. User ID: {$user->id}");

        return $user;
    }

    /**
     * Generate FIFA Connect ID based on role and organization type
     */
    private function generateFifaConnectId(): string
    {
        $role = $this->getDefaultRole();
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
        $random = strtoupper(\Illuminate\Support\Str::random(6));
        
        return $prefix . '_' . $timestamp . '_' . $random;
    }

    /**
     * Get default permissions for the role
     */
    private function getDefaultPermissions(): array
    {
        $role = $this->getDefaultRole();
        
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
                'system_configuration'
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
                'association_management'
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
     * Get default role based on organization type
     */
    private function getDefaultRole(): string
    {
        return match($this->organization_type) {
            'club' => 'club_admin',
            'association' => 'association_admin',
            'federation' => 'system_admin',
            'league' => 'association_admin',
            'academy' => 'club_admin',
            'referee' => 'referee',
            'player' => 'player',
            default => 'club_admin',
        };
    }
}
