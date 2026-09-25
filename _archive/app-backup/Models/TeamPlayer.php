<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'player_id',
        'role',
        'squad_number',
        'joined_date',
        'contract_end_date',
        'position_preference',
        'notes',
        'status'
    ];

    protected $casts = [
        'joined_date' => 'date',
        'contract_end_date' => 'date',
        'squad_number' => 'integer'
    ];

    // Relationships
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    // Scopes
    public function scopeStarters($query)
    {
        return $query->where('role', 'starter');
    }

    public function scopeSubstitutes($query)
    {
        return $query->where('role', 'substitute');
    }

    public function scopeReserves($query)
    {
        return $query->where('role', 'reserve');
    }

    public function scopeLoans($query)
    {
        return $query->where('role', 'loan');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInjured($query)
    {
        return $query->where('status', 'injured');
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position_preference', $position);
    }

    // Methods
    public function getStatusColor(): string
    {
        return match($this->status) {
            'active' => 'green',
            'injured' => 'red',
            'suspended' => 'yellow',
            'loaned_out' => 'blue',
            'retired' => 'gray',
            default => 'gray'
        };
    }

    public function getStatusText(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'injured' => 'Injured',
            'suspended' => 'Suspended',
            'loaned_out' => 'Loaned Out',
            'retired' => 'Retired',
            default => 'Unknown'
        };
    }

    public function getRoleText(): string
    {
        return match($this->role) {
            'starter' => 'Starter',
            'substitute' => 'Substitute',
            'reserve' => 'Reserve',
            'loan' => 'Loan',
            default => 'Unknown'
        };
    }

    public function isStarter(): bool
    {
        return $this->role === 'starter';
    }

    public function isSubstitute(): bool
    {
        return $this->role === 'substitute';
    }

    public function isReserve(): bool
    {
        return $this->role === 'reserve';
    }

    public function isLoan(): bool
    {
        return $this->role === 'loan';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInjured(): bool
    {
        return $this->status === 'injured';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isLoanedOut(): bool
    {
        return $this->status === 'loaned_out';
    }

    public function daysUntilContractExpiry(): int
    {
        if (!$this->contract_end_date) {
            return -1; // No contract end date
        }
        
        return now()->diffInDays($this->contract_end_date, false);
    }

    public function contractExpiresSoon(): bool
    {
        return $this->daysUntilContractExpiry() <= 30 && $this->daysUntilContractExpiry() > 0;
    }

    public function contractExpired(): bool
    {
        return $this->daysUntilContractExpiry() < 0;
    }

    public function getFormattedSquadNumber(): string
    {
        return $this->squad_number ? "#{$this->squad_number}" : "No Number";
    }
} 