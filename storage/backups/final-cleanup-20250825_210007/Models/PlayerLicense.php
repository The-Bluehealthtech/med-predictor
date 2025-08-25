<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'club_id',
        'fifa_connect_id',
        'license_number',
        'license_type', // professional, amateur, youth, international
        'status', // pending, active, suspended, expired, revoked
        'issue_date',
        'expiry_date',
        'renewal_date',
        'issuing_authority',
        'license_category', // A, B, C, D, E
        'registration_number',
        'transfer_status', // registered, pending_transfer, transferred
        'contract_type', // permanent, loan, free_agent
        'contract_start_date',
        'contract_end_date',
        'wage_agreement',
        'bonus_structure',
        'release_clause',
        'medical_clearance',
        'fitness_certificate',
        'disciplinary_record',
        'international_clearance',
        'work_permit',
        'visa_status',
        'documentation_status',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes',
        'requested_by',
        'document_path',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewal_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'approved_at' => 'datetime',
        'wage_agreement' => 'decimal:2',
        'release_clause' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function fraudAnalysis()
    {
        return $this->hasOne(\App\Models\PlayerFraudAnalysis::class, 'player_license_id');
    }

    /**
     * Check if player has valid PCMA for license approval
     */
    public function hasValidPCMA(): bool
    {
        return $this->player->pcmas()
            ->where('status', 'completed')
            ->where('fifa_compliant', true)
            ->where('completed_at', '>=', now()->subYear())
            ->exists();
    }

    /**
     * Get the most recent valid PCMA for this player
     */
    public function getValidPCMA()
    {
        return $this->player->pcmas()
            ->where('status', 'completed')
            ->where('fifa_compliant', true)
            ->where('completed_at', '>=', now()->subYear())
            ->orderBy('completed_at', 'desc')
            ->first();
    }

    /**
     * Check if PCMA is required for this license type
     */
    public function requiresPCMA(): bool
    {
        return in_array($this->license_type, ['professional', 'amateur', 'youth']);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('license_type', $type);
    }

    public function scopeByClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('status', 'active');
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expiry_date > now();
    }

    public function isExpired(): bool
    {
        return $this->expiry_date <= now();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function daysUntilExpiry(): int
    {
        return now()->diffInDays($this->expiry_date, false);
    }

    public function requiresRenewal(): bool
    {
        return $this->daysUntilExpiry() <= 30 && $this->status === 'active';
    }

    public function canTransfer(): bool
    {
        return $this->status === 'active' && 
               $this->transfer_status !== 'pending_transfer' &&
               $this->contract_end_date > now();
    }

    public function getLicenseStatusColor(): string
    {
        return match($this->status) {
            'active' => 'green',
            'pending' => 'yellow',
            'suspended' => 'red',
            'expired' => 'gray',
            'revoked' => 'red',
            default => 'gray'
        };
    }

    public function getLicenseStatusText(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'pending' => 'Pending Approval',
            'suspended' => 'Suspended',
            'expired' => 'Expired',
            'revoked' => 'Revoked',
            default => 'Unknown'
        };
    }

    public function generateLicenseNumber(): string
    {
        $prefix = strtoupper(substr($this->club->country, 0, 3));
        $year = date('Y');
        $sequence = str_pad($this->id, 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    public function validateLicense(): array
    {
        $errors = [];

        // Check if player has FIFA Connect ID
        if (!$this->fifa_connect_id) {
            $errors[] = 'Player must have a FIFA Connect ID';
        }

        // Check if license is not expired
        if ($this->isExpired()) {
            $errors[] = 'License has expired';
        }

        // Check if all required documents are provided
        if (!$this->medical_clearance) {
            $errors[] = 'Medical clearance is required';
        }

        if (!$this->fitness_certificate) {
            $errors[] = 'Fitness certificate is required';
        }

        // Check contract validity
        if ($this->contract_end_date && $this->contract_end_date <= now()) {
            $errors[] = 'Contract has expired';
        }

        return $errors;
    }

    public function approve($approvedBy): bool
    {
        $errors = $this->validateLicense();
        if (!empty($errors)) {
            return false;
        }
        $oldStatus = $this->status;
        $this->update([
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'issue_date' => now(),
            'license_number' => $this->generateLicenseNumber()
        ]);
        // Notify player and club
        $player = $this->player;
        $club = $this->club;
        if ($player) { $player->notify(new \App\Notifications\LicenseStatusChanged($this, $oldStatus, 'active', $club ? $club->user : null)); }
        if ($club && $club->user) { $club->user->notify(new \App\Notifications\LicenseStatusChanged($this, $oldStatus, 'active', $club->user)); }
        return true;
    }

    public function reject($reason, $rejectedBy): bool
    {
        $oldStatus = $this->status;
        $this->update([
            'status' => 'revoked',
            'approval_status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => $rejectedBy,
            'approved_at' => now()
        ]);
        // Notify player and club
        $player = $this->player;
        $club = $this->club;
        if ($player) { $player->notify(new \App\Notifications\LicenseStatusChanged($this, $oldStatus, 'revoked', $club ? $club->user : null, $reason)); }
        if ($club && $club->user) { $club->user->notify(new \App\Notifications\LicenseStatusChanged($this, $oldStatus, 'revoked', $club->user, $reason)); }
        return true;
    }

    public function requestExplanation($explanationRequest, $requestedBy): bool
    {
        $oldStatus = $this->status;
        $this->update([
            'status' => 'justification_requested',
            'approval_status' => 'pending',
            'rejection_reason' => $explanationRequest,
            'approved_by' => $requestedBy,
            'approved_at' => now()
        ]);
        // Notify club about the explanation request
        $club = $this->club;
        if ($club && $club->user) { 
            $club->user->notify(new \App\Notifications\LicenseStatusChanged($this, $oldStatus, 'justification_requested', $club->user, $explanationRequest)); 
        }
        return true;
    }

    public function suspend($reason): bool
    {
        $this->update([
            'status' => 'suspended',
            'notes' => $this->notes . "\nSuspended: " . $reason . " (" . now()->format('Y-m-d H:i:s') . ")"
        ]);

        return true;
    }

    public function renew($newExpiryDate): bool
    {
        $this->update([
            'expiry_date' => $newExpiryDate,
            'renewal_date' => now(),
            'status' => 'active'
        ]);

        return true;
    }

    public function transfer($newClubId): bool
    {
        if (!$this->canTransfer()) {
            return false;
        }

        $this->update([
            'club_id' => $newClubId,
            'transfer_status' => 'transferred',
            'notes' => $this->notes . "\nTransferred to club ID: {$newClubId} (" . now()->format('Y-m-d H:i:s') . ")"
        ]);

        return true;
    }

    // Audit Trail Methods
    public function getAuditIdentifier(): string
    {
        return "PlayerLicense:{$this->id}";
    }

    public function getAuditDisplayName(): string
    {
        $playerName = $this->player ? $this->player->name : 'Unknown Player';
        return "License #{$this->license_number} - {$playerName}";
    }

    public function getAuditType(): string
    {
        return 'player_license';
    }

    public function getAuditData(): array
    {
        return [
            'id' => $this->id,
            'player_id' => $this->player_id,
            'club_id' => $this->club_id,
            'license_number' => $this->license_number,
            'license_type' => $this->license_type,
            'status' => $this->status,
            'approval_status' => $this->approval_status,
        ];
    }
} 