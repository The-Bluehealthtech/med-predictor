<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'club_origin_id',
        'club_destination_id',
        'federation_origin_id',
        'federation_destination_id',
        'transfer_type',
        'transfer_status',
        'itc_status',
        'transfer_window_start',
        'transfer_window_end',
        'transfer_date',
        'contract_start_date',
        'contract_end_date',
        'itc_request_date',
        'itc_response_date',
        'transfer_fee',
        'currency',
        'payment_status',
        'fifa_transfer_id',
        'fifa_itc_id',
        'fifa_payload',
        'fifa_response',
        'fifa_error_message',
        'is_minor_transfer',
        'is_international',
        'special_conditions',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transfer_window_start' => 'date',
        'transfer_window_end' => 'date',
        'transfer_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'itc_request_date' => 'date',
        'itc_response_date' => 'date',
        'fifa_payload' => 'array',
        'fifa_response' => 'array',
        'is_minor_transfer' => 'boolean',
        'is_international' => 'boolean',
        'transfer_fee' => 'decimal:2',
    ];

    /**
     * Relations
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function clubOrigin(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club_origin_id');
    }

    public function clubDestination(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club_destination_id');
    }

    public function federationOrigin(): BelongsTo
    {
        return $this->belongsTo(Federation::class, 'federation_origin_id');
    }

    public function federationDestination(): BelongsTo
    {
        return $this->belongsTo(Federation::class, 'federation_destination_id');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TransferDocument::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TransferPayment::class);
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
    public function scopePending($query)
    {
        return $query->where('transfer_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('transfer_status', 'approved');
    }

    public function scopeInternational($query)
    {
        return $query->where('is_international', true);
    }

    public function scopeInTransferWindow($query)
    {
        $now = Carbon::now();
        return $query->where('transfer_window_start', '<=', $now)
                    ->where('transfer_window_end', '>=', $now);
    }

    public function scopeByPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeByClub($query, $clubId)
    {
        return $query->where(function($q) use ($clubId) {
            $q->where('club_origin_id', $clubId)
              ->orWhere('club_destination_id', $clubId);
        });
    }

    /**
     * Accessors & Mutators
     */
    public function getIsInTransferWindowAttribute(): bool
    {
        $now = Carbon::now();
        return $now->between($this->transfer_window_start, $this->transfer_window_end);
    }

    public function getItcDaysRemainingAttribute(): ?int
    {
        if (!$this->itc_request_date) {
            return null;
        }

        $deadline = $this->itc_request_date->addDays(7);
        $now = Carbon::now();
        
        return max(0, $now->diffInDays($deadline, false));
    }

    public function getIsItcOverdueAttribute(): bool
    {
        if (!$this->itc_request_date || $this->itc_status === 'approved') {
            return false;
        }

        return $this->itc_request_date->addDays(7)->isPast();
    }

    public function getFormattedTransferFeeAttribute(): string
    {
        if (!$this->transfer_fee) {
            return 'N/A';
        }

        return number_format($this->transfer_fee, 2) . ' ' . $this->currency;
    }

    /**
     * Méthodes FIFA
     */
    public function isFifaTransfer(): bool
    {
        return !empty($this->fifa_transfer_id);
    }

    public function isItcRequired(): bool
    {
        return $this->is_international && $this->transfer_type !== 'free_agent';
    }

    public function canBeSubmitted(): bool
    {
        return $this->transfer_status === 'draft' && 
               $this->isInTransferWindow && 
               $this->hasRequiredDocuments();
    }

    public function hasRequiredDocuments(): bool
    {
        $requiredTypes = ['passport', 'contract'];
        if ($this->is_minor_transfer) {
            $requiredTypes[] = 'parental_consent';
        }

        $uploadedTypes = $this->documents()
            ->where('validation_status', 'approved')
            ->pluck('document_type')
            ->toArray();

        return count(array_intersect($requiredTypes, $uploadedTypes)) === count($requiredTypes);
    }

    public function getFifaPayload(): array
    {
        return [
            'transfer_id' => $this->id,
            'player' => [
                'fifa_id' => $this->player->fifa_player_id,
                'name' => $this->player->name,
                'nationality' => $this->player->nationality,
                'birth_date' => $this->player->date_of_birth?->format('Y-m-d'),
                'is_minor' => $this->is_minor_transfer,
            ],
            'clubs' => [
                'origin' => [
                    'fifa_id' => $this->clubOrigin->fifa_club_id,
                    'name' => $this->clubOrigin->name,
                    'country' => $this->clubOrigin->country,
                ],
                'destination' => [
                    'fifa_id' => $this->clubDestination->fifa_club_id,
                    'name' => $this->clubDestination->name,
                    'country' => $this->clubDestination->country,
                ],
            ],
            'transfer_details' => [
                'type' => $this->transfer_type,
                'date' => $this->transfer_date->format('Y-m-d'),
                'fee' => $this->transfer_fee,
                'currency' => $this->currency,
                'is_international' => $this->is_international,
            ],
            'contract' => [
                'start_date' => $this->contract_start_date->format('Y-m-d'),
                'end_date' => $this->contract_end_date?->format('Y-m-d'),
            ],
        ];
    }
}
