<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceMetric extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'player_id',
        'metric_type', // physical, technical, tactical, mental, medical, social
        'metric_name',
        'metric_value',
        'metric_unit',
        'measurement_date',
        'data_source', // manual, device, fifa_connect, hl7_fhir
        'confidence_score',
        'is_verified',
        'verified_by',
        'verified_at',
        'notes',
        'metadata', // JSON field for additional data
        'fifa_connect_id',
        'hl7_fhir_resource_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metric_value' => 'float',
        'measurement_date' => 'datetime',
        'confidence_score' => 'float',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Metric types constants
    const TYPE_PHYSICAL = 'physical';
    const TYPE_TECHNICAL = 'technical';
    const TYPE_TACTICAL = 'tactical';
    const TYPE_MENTAL = 'mental';
    const TYPE_MEDICAL = 'medical';
    const TYPE_SOCIAL = 'social';

    // Data sources constants
    const SOURCE_MANUAL = 'manual';
    const SOURCE_DEVICE = 'device';
    const SOURCE_FIFA_CONNECT = 'fifa_connect';
    const SOURCE_HL7_FHIR = 'hl7_fhir';
    const SOURCE_ITMS = 'itms';
    const SOURCE_DTMS = 'dtms';

    /**
     * Get the player that owns the performance metric
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the user who created the metric
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who verified the metric
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the FIFA Connect ID record
     */
    public function fifaConnectId(): BelongsTo
    {
        return $this->belongsTo(FifaConnectId::class, 'fifa_connect_id');
    }

    /**
     * Get performance trends for this metric
     */
    public function trends(): HasMany
    {
        return $this->hasMany(PerformanceTrend::class, 'metric_id');
    }

    /**
     * Get alerts related to this metric
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(PerformanceAlert::class, 'metric_id');
    }

    /**
     * Scope for physical metrics
     */
    public function scopePhysical($query)
    {
        return $query->where('metric_type', self::TYPE_PHYSICAL);
    }

    /**
     * Scope for technical metrics
     */
    public function scopeTechnical($query)
    {
        return $query->where('metric_type', self::TYPE_TECHNICAL);
    }

    /**
     * Scope for tactical metrics
     */
    public function scopeTactical($query)
    {
        return $query->where('metric_type', self::TYPE_TACTICAL);
    }

    /**
     * Scope for mental metrics
     */
    public function scopeMental($query)
    {
        return $query->where('metric_type', self::TYPE_MENTAL);
    }

    /**
     * Scope for medical metrics
     */
    public function scopeMedical($query)
    {
        return $query->where('metric_type', self::TYPE_MEDICAL);
    }

    /**
     * Scope for social metrics
     */
    public function scopeSocial($query)
    {
        return $query->where('metric_type', self::TYPE_SOCIAL);
    }

    /**
     * Scope for verified metrics
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for recent metrics (last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('measurement_date', '>=', now()->subDays(30));
    }

    /**
     * Get metric type options
     */
    public static function getMetricTypes(): array
    {
        return [
            self::TYPE_PHYSICAL => 'Physical',
            self::TYPE_TECHNICAL => 'Technical',
            self::TYPE_TACTICAL => 'Tactical',
            self::TYPE_MENTAL => 'Mental',
            self::TYPE_MEDICAL => 'Medical',
            self::TYPE_SOCIAL => 'Social',
        ];
    }

    /**
     * Get data source options
     */
    public static function getDataSources(): array
    {
        return [
            self::SOURCE_MANUAL => 'Manual Entry',
            self::SOURCE_DEVICE => 'Device/Equipment',
            self::SOURCE_FIFA_CONNECT => 'FIFA Connect',
            self::SOURCE_HL7_FHIR => 'HL7 FHIR',
            self::SOURCE_ITMS => 'iTMS',
            self::SOURCE_DTMS => 'dTMS',
        ];
    }

    /**
     * Calculate trend for this metric
     */
    public function calculateTrend(): float
    {
        $recentMetrics = self::where('player_id', $this->player_id)
            ->where('metric_name', $this->metric_name)
            ->where('measurement_date', '>=', now()->subDays(90))
            ->orderBy('measurement_date')
            ->get();

        if ($recentMetrics->count() < 2) {
            return 0;
        }

        $firstValue = $recentMetrics->first()->metric_value;
        $lastValue = $recentMetrics->last()->metric_value;
        
        return (($lastValue - $firstValue) / $firstValue) * 100;
    }

    /**
     * Check if metric is within normal range
     */
    public function isWithinNormalRange(): bool
    {
        // This would be implemented based on sport-specific standards
        // For now, return true as placeholder
        return true;
    }

    /**
     * Get metric display value with unit
     */
    public function getDisplayValueAttribute(): string
    {
        return $this->metric_value . ' ' . $this->metric_unit;
    }

    /**
     * Get metric status (improving, declining, stable)
     */
    public function getStatusAttribute(): string
    {
        $trend = $this->calculateTrend();
        
        if ($trend > 5) {
            return 'improving';
        } elseif ($trend < -5) {
            return 'declining';
        } else {
            return 'stable';
        }
    }
} 