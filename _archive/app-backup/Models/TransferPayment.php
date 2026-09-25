<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TransferPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'payer_id',
        'payee_id',
        'payment_type',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'due_date',
        'payment_date',
        'processed_at',
        'transaction_id',
        'reference_number',
        'payment_notes',
        'fifa_payment_id',
        'fifa_payment_data',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
        'processed_at' => 'datetime',
        'fifa_payment_data' => 'array',
    ];

    /**
     * Relations
     */
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'payer_id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'payee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now())
                    ->where('payment_status', '!=', 'completed');
    }

    public function scopeByClub($query, $clubId)
    {
        return $query->where(function($q) use ($clubId) {
            $q->where('payer_id', $clubId)
              ->orWhere('payee_id', $clubId);
        });
    }

    /**
     * Accessors & Mutators
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() && $this->payment_status !== 'completed';
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->isOverdue) {
            return 0;
        }

        return Carbon::now()->diffInDays($this->due_date);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match($this->payment_type) {
            'transfer_fee' => 'Frais de transfert',
            'training_compensation' => 'Compensation de formation',
            'solidarity_contribution' => 'Contribution de solidarité',
            'other' => 'Autre',
            default => ucfirst(str_replace('_', ' ', $this->payment_type)),
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'En attente',
            'processing' => 'En cours',
            'completed' => 'Terminé',
            'failed' => 'Échoué',
            'cancelled' => 'Annulé',
            default => ucfirst($this->payment_status),
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'bank_transfer' => 'Virement bancaire',
            'check' => 'Chèque',
            'cash' => 'Espèces',
            'other' => 'Autre',
            default => ucfirst(str_replace('_', ' ', $this->payment_method)),
        };
    }

    /**
     * Méthodes FIFA
     */
    public function isFifaPayment(): bool
    {
        return !empty($this->fifa_payment_id);
    }

    public function getFifaPaymentData(): array
    {
        return [
            'payment_id' => $this->id,
            'transfer_id' => $this->transfer_id,
            'payer_id' => $this->payer?->fifa_club_id,
            'payee_id' => $this->payee?->fifa_club_id,
            'type' => $this->payment_type,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'method' => $this->payment_method,
            'status' => $this->payment_status,
            'due_date' => $this->due_date->format('Y-m-d'),
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'transaction_id' => $this->transaction_id,
        ];
    }

    /**
     * Méthodes métier
     */
    public function markAsProcessing(): void
    {
        $this->update(['payment_status' => 'processing']);
    }

    public function markAsCompleted(?string $transactionId = null): void
    {
        $this->update([
            'payment_status' => 'completed',
            'payment_date' => now(),
            'processed_at' => now(),
            'transaction_id' => $transactionId ?? $this->transaction_id,
        ]);
    }

    public function markAsFailed(string $notes = null): void
    {
        $this->update([
            'payment_status' => 'failed',
            'payment_notes' => $notes,
        ]);
    }

    public function cancel(string $notes = null): void
    {
        $this->update([
            'payment_status' => 'cancelled',
            'payment_notes' => $notes,
        ]);
    }

    public function isRequiredForTransfer(): bool
    {
        return $this->payment_type === 'transfer_fee' && $this->transfer->transfer_fee > 0;
    }

    public function getTrainingCompensationAmount(): float
    {
        // Calcul de la compensation de formation selon les règles FIFA
        // À implémenter selon les règles spécifiques
        return 0.0;
    }

    public function getSolidarityContributionAmount(): float
    {
        // Calcul de la contribution de solidarité selon les règles FIFA
        // À implémenter selon les règles spécifiques
        return 0.0;
    }
}
