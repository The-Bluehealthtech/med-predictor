<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'club_id',
        'license_type',
        'start_date',
        'end_date',
        'status',
        'issued_at',
        'notes',
        'license_number',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'issued_at' => 'datetime',
    ];

    /**
     * Relation avec le joueur
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Relation avec le club
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Relation avec la photo de licence
     */
    public function photo()
    {
        return $this->hasOne(LicensePhoto::class, 'player_id', 'player_id')
            ->where('club_id', $this->club_id);
    }

    /**
     * Accesseur pour le statut de la licence
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'active' => 'Active',
            'expired' => 'Expirée',
            'suspended' => 'Suspendue',
            'revoked' => 'Révoquée',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Accesseur pour le type de licence
     */
    public function getLicenseTypeTextAttribute()
    {
        $types = [
            'amateur' => 'Amateur',
            'semi_pro' => 'Semi-Professionnel',
            'professional' => 'Professionnel',
            'international' => 'International',
        ];

        return $types[$this->license_type] ?? $this->license_type;
    }

    /**
     * Accesseur pour vérifier si la licence est expirée
     */
    public function getIsExpiredAttribute()
    {
        return $this->end_date->isPast();
    }

    /**
     * Accesseur pour vérifier si la licence est active
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && !$this->is_expired;
    }

    /**
     * Accesseur pour les jours restants
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->is_expired) {
            return 0;
        }

        return now()->diffInDays($this->end_date, false);
    }

    /**
     * Scope pour les licences actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '>', now());
    }

    /**
     * Scope pour les licences expirées
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Scope pour les licences d'un club spécifique
     */
    public function scopeForClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    /**
     * Scope pour les licences d'un joueur spécifique
     */
    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    /**
     * Scope pour un type de licence spécifique
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('license_type', $type);
    }

    /**
     * Génère un numéro de licence unique
     */
    public static function generateLicenseNumber()
    {
        do {
            $number = 'LIC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('license_number', $number)->exists());

        return $number;
    }

    /**
     * Boot method pour générer automatiquement le numéro de licence
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($license) {
            if (empty($license->license_number)) {
                $license->license_number = static::generateLicenseNumber();
            }
        });
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