<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TUERequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'medication',
        'reason',
        'physician_id',
        'status',
        'request_date',
        'approved_date',
        'expiry_date',
        'approved_by',
        'approval_notes',
        'rejection_reason',
        'supporting_documents',
        'fifa_tue_data',
    ];

    protected $casts = [
        'request_date' => 'date',
        'approved_date' => 'date',
        'expiry_date' => 'date',
        'supporting_documents' => 'array',
        'fifa_tue_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the athlete that this TUE request belongs to.
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the physician who submitted this request.
     */
    public function physician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'physician_id');
    }

    /**
     * Get the user who approved this request.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to filter rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to filter expired requests.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope to filter active (approved and not expired) requests.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
                    ->where('expiry_date', '>=', now());
    }

    /**
     * Check if TUE is active (approved and not expired).
     */
    public function isActive(): bool
    {
        return $this->status === 'approved' && 
               $this->expiry_date && 
               $this->expiry_date->isAfter(now());
    }

    /**
     * Check if TUE is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'approved' && 
               $this->expiry_date && 
               $this->expiry_date->isBefore(now());
    }

    /**
     * Get days until expiry.
     */
    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->expiry_date || $this->status !== 'approved') {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Get TUE summary.
     */
    public function getTUESummary(): array
    {
        return [
            'id' => $this->id,
            'athlete_name' => $this->athlete->name,
            'medication' => $this->medication,
            'reason' => $this->reason,
            'status' => $this->status,
            'request_date' => $this->request_date,
            'approved_date' => $this->approved_date,
            'expiry_date' => $this->expiry_date,
            'physician_name' => $this->physician->name,
            'approved_by_name' => $this->approvedBy?->name,
            'is_active' => $this->isActive(),
            'is_expired' => $this->isExpired(),
            'days_until_expiry' => $this->getDaysUntilExpiry(),
            'approval_notes' => $this->approval_notes,
            'rejection_reason' => $this->rejection_reason,
        ];
    }

    /**
     * Check if TUE is FIFA compliant.
     */
    public function isFifaCompliant(): bool
    {
        return $this->status === 'approved' && 
               $this->expiry_date && 
               $this->expiry_date->isAfter(now());
    }
} 