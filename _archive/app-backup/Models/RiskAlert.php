<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskAlert extends Model
{
    use HasFactory;

    protected $table = 'risk_alerts';

    protected $fillable = [
        'athlete_id',
        'type',
        'source',
        'score',
        'message',
        'resolved',
        'priority',
        'ai_metadata',
        'recommendations',
        'acknowledged_by',
        'acknowledged_at',
        'fifa_alert_data',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'resolved' => 'boolean',
        'ai_metadata' => 'array',
        'recommendations' => 'array',
        'fifa_alert_data' => 'array',
        'acknowledged_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the athlete that this alert belongs to.
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the user who acknowledged this alert.
     */
    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to filter unresolved alerts.
     */
    public function scopeUnresolved($query)
    {
        return $query->where('resolved', false);
    }

    /**
     * Scope to filter resolved alerts.
     */
    public function scopeResolved($query)
    {
        return $query->where('resolved', true);
    }

    /**
     * Scope to filter high priority alerts.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    /**
     * Scope to filter critical alerts.
     */
    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    /**
     * Scope to filter by source.
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Check if alert is critical.
     */
    public function isCritical(): bool
    {
        return $this->priority === 'critical';
    }

    /**
     * Check if alert is high priority.
     */
    public function isHighPriority(): bool
    {
        return in_array($this->priority, ['high', 'critical']);
    }

    /**
     * Check if alert is acknowledged.
     */
    public function isAcknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }

    /**
     * Get risk level based on score.
     */
    public function getRiskLevel(): string
    {
        if ($this->score >= 0.8) {
            return 'critical';
        } elseif ($this->score >= 0.6) {
            return 'high';
        } elseif ($this->score >= 0.4) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get alert summary.
     */
    public function getAlertSummary(): array
    {
        return [
            'id' => $this->id,
            'athlete_name' => $this->athlete->name,
            'type' => $this->type,
            'source' => $this->source,
            'score' => $this->score,
            'priority' => $this->priority,
            'risk_level' => $this->getRiskLevel(),
            'message' => $this->message,
            'resolved' => $this->resolved,
            'acknowledged' => $this->isAcknowledged(),
            'acknowledged_by' => $this->acknowledgedBy?->name,
            'acknowledged_at' => $this->acknowledged_at,
            'created_at' => $this->created_at,
            'recommendations' => $this->recommendations,
        ];
    }

    /**
     * Get AI metadata.
     */
    public function getAIMetadata(): array
    {
        return $this->ai_metadata ?? [];
    }

    /**
     * Check if alert is FIFA compliant.
     */
    public function isFifaCompliant(): bool
    {
        return $this->resolved || $this->isAcknowledged();
    }
} 