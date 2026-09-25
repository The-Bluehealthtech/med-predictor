<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Injury extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'date',
        'type',
        'body_zone',
        'severity',
        'description',
        'status',
        'estimated_recovery_days',
        'expected_return_date',
        'actual_return_date',
        'diagnosed_by',
        'treatment_plan',
        'rehabilitation_progress',
        'fifa_injury_data',
    ];

    protected $casts = [
        'date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'treatment_plan' => 'array',
        'rehabilitation_progress' => 'array',
        'fifa_injury_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the athlete that this injury belongs to.
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the physician who diagnosed this injury.
     */
    public function diagnosedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diagnosed_by');
    }

    /**
     * Scope to filter by severity.
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter active injuries.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope to filter resolved injuries.
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope to filter by body zone.
     */
    public function scopeByBodyZone($query, $bodyZone)
    {
        return $query->where('body_zone', $bodyZone);
    }

    /**
     * Calculate recovery progress percentage.
     */
    public function getRecoveryProgress(): int
    {
        if ($this->status === 'resolved') {
            return 100;
        }

        if (!$this->estimated_recovery_days || !$this->date) {
            return 0;
        }

        $daysSinceInjury = $this->date->diffInDays(now());
        $progress = ($daysSinceInjury / $this->estimated_recovery_days) * 100;

        return min(100, max(0, round($progress)));
    }

    /**
     * Check if injury is overdue for return.
     */
    public function isOverdue(): bool
    {
        if ($this->status === 'resolved') {
            return false;
        }

        if ($this->expected_return_date) {
            return now()->isAfter($this->expected_return_date);
        }

        if ($this->estimated_recovery_days) {
            $expectedDate = $this->date->addDays($this->estimated_recovery_days);
            return now()->isAfter($expectedDate);
        }

        return false;
    }

    /**
     * Get injury summary for reporting.
     */
    public function getInjurySummary(): array
    {
        return [
            'id' => $this->id,
            'athlete_name' => $this->athlete->name,
            'date' => $this->date,
            'type' => $this->type,
            'body_zone' => $this->body_zone,
            'severity' => $this->severity,
            'status' => $this->status,
            'recovery_progress' => $this->getRecoveryProgress(),
            'is_overdue' => $this->isOverdue(),
            'diagnosed_by' => $this->diagnosedBy?->name,
            'expected_return_date' => $this->expected_return_date,
            'actual_return_date' => $this->actual_return_date,
        ];
    }
} 