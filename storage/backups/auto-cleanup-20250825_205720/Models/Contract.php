<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'club_id',
        'transfer_id',
        'contract_type',
        'start_date',
        'end_date',
        'is_active',
        'salary',
        'bonus',
        'currency',
        'payment_frequency',
        'clauses',
        'bonuses',
        'special_conditions',
        'fifa_contract_id',
        'fifa_contract_data',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'clauses' => 'array',
        'bonuses' => 'array',
        'fifa_contract_data' => 'array',
    ];

    /**
     * Relations
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeByClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    public function scopeCurrent($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
                    ->where(function($q) use ($now) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $now);
                    });
    }

    /**
     * Accessors & Mutators
     */
    public function getIsCurrentAttribute(): bool
    {
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date ?? Carbon::now()->addYears(100));
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        return max(0, Carbon::now()->diffInDays($this->end_date, false));
    }

    public function getFormattedSalaryAttribute(): string
    {
        if (!$this->salary) {
            return 'N/A';
        }

        return number_format($this->salary, 2) . ' ' . $this->currency;
    }

    public function getFormattedBonusAttribute(): string
    {
        if (!$this->bonus) {
            return 'N/A';
        }

        return number_format($this->bonus, 2) . ' ' . $this->currency;
    }

    public function getTotalCompensationAttribute(): float
    {
        return ($this->salary ?? 0) + ($this->bonus ?? 0);
    }

    /**
     * Méthodes FIFA
     */
    public function isFifaContract(): bool
    {
        return !empty($this->fifa_contract_id);
    }

    public function getFifaContractData(): array
    {
        return [
            'contract_id' => $this->id,
            'player_id' => $this->player->fifa_player_id,
            'club_id' => $this->club->fifa_club_id,
            'type' => $this->contract_type,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'salary' => $this->salary,
            'bonus' => $this->bonus,
            'currency' => $this->currency,
            'payment_frequency' => $this->payment_frequency,
            'clauses' => $this->clauses,
            'bonuses' => $this->bonuses,
        ];
    }

    /**
     * Méthodes métier
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function extend(int $months): void
    {
        $this->update([
            'end_date' => $this->end_date ? $this->end_date->addMonths($months) : Carbon::now()->addMonths($months)
        ]);
    }
}
