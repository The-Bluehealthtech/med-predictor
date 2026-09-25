<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceTrend extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'metric_id',
        'player_id',
        'trend_period', // daily, weekly, monthly, quarterly, yearly
        'start_date',
        'end_date',
        'initial_value',
        'final_value',
        'change_percentage',
        'trend_direction', // increasing, decreasing, stable
        'trend_strength', // weak, moderate, strong
        'data_points_count',
        'confidence_level',
        'analysis_notes',
        'ai_insights',
        'metadata', // JSON field for additional data
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'initial_value' => 'float',
        'final_value' => 'float',
        'change_percentage' => 'float',
        'confidence_level' => 'float',
        'metadata' => 'array',
    ];

    // Trend periods constants
    const PERIOD_DAILY = 'daily';
    const PERIOD_WEEKLY = 'weekly';
    const PERIOD_MONTHLY = 'monthly';
    const PERIOD_QUARTERLY = 'quarterly';
    const PERIOD_YEARLY = 'yearly';

    // Trend directions constants
    const DIRECTION_INCREASING = 'increasing';
    const DIRECTION_DECREASING = 'decreasing';
    const DIRECTION_STABLE = 'stable';

    // Trend strength constants
    const STRENGTH_WEAK = 'weak';
    const STRENGTH_MODERATE = 'moderate';
    const STRENGTH_STRONG = 'strong';

    /**
     * Get the performance metric associated with the trend
     */
    public function metric(): BelongsTo
    {
        return $this->belongsTo(PerformanceMetric::class, 'metric_id');
    }

    /**
     * Get the player associated with the trend
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the user who created the trend analysis
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for increasing trends
     */
    public function scopeIncreasing($query)
    {
        return $query->where('trend_direction', self::DIRECTION_INCREASING);
    }

    /**
     * Scope for decreasing trends
     */
    public function scopeDecreasing($query)
    {
        return $query->where('trend_direction', self::DIRECTION_DECREASING);
    }

    /**
     * Scope for stable trends
     */
    public function scopeStable($query)
    {
        return $query->where('trend_direction', self::DIRECTION_STABLE);
    }

    /**
     * Scope for strong trends
     */
    public function scopeStrong($query)
    {
        return $query->where('trend_strength', self::STRENGTH_STRONG);
    }

    /**
     * Scope for recent trends (last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('end_date', '>=', now()->subDays(30));
    }

    /**
     * Get trend period options
     */
    public static function getTrendPeriods(): array
    {
        return [
            self::PERIOD_DAILY => 'Daily',
            self::PERIOD_WEEKLY => 'Weekly',
            self::PERIOD_MONTHLY => 'Monthly',
            self::PERIOD_QUARTERLY => 'Quarterly',
            self::PERIOD_YEARLY => 'Yearly',
        ];
    }

    /**
     * Get trend direction options
     */
    public static function getTrendDirections(): array
    {
        return [
            self::DIRECTION_INCREASING => 'Increasing',
            self::DIRECTION_DECREASING => 'Decreasing',
            self::DIRECTION_STABLE => 'Stable',
        ];
    }

    /**
     * Get trend strength options
     */
    public static function getTrendStrengths(): array
    {
        return [
            self::STRENGTH_WEAK => 'Weak',
            self::STRENGTH_MODERATE => 'Moderate',
            self::STRENGTH_STRONG => 'Strong',
        ];
    }

    /**
     * Calculate trend direction based on change percentage
     */
    public function calculateTrendDirection(): string
    {
        $threshold = 5.0; // 5% threshold for significant change

        if (abs($this->change_percentage) < $threshold) {
            return self::DIRECTION_STABLE;
        }

        return $this->change_percentage > 0 ? self::DIRECTION_INCREASING : self::DIRECTION_DECREASING;
    }

    /**
     * Calculate trend strength based on change percentage
     */
    public function calculateTrendStrength(): string
    {
        $absoluteChange = abs($this->change_percentage);

        if ($absoluteChange < 10.0) {
            return self::STRENGTH_WEAK;
        } elseif ($absoluteChange < 25.0) {
            return self::STRENGTH_MODERATE;
        } else {
            return self::STRENGTH_STRONG;
        }
    }

    /**
     * Get trend duration in days
     */
    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get trend color based on direction
     */
    public function getTrendColorAttribute(): string
    {
        $colors = [
            self::DIRECTION_INCREASING => 'green',
            self::DIRECTION_DECREASING => 'red',
            self::DIRECTION_STABLE => 'blue',
        ];

        return $colors[$this->trend_direction] ?? 'gray';
    }

    /**
     * Get trend icon based on direction
     */
    public function getTrendIconAttribute(): string
    {
        $icons = [
            self::DIRECTION_INCREASING => 'arrow-up',
            self::DIRECTION_DECREASING => 'arrow-down',
            self::DIRECTION_STABLE => 'minus',
        ];

        return $icons[$this->trend_direction] ?? 'minus';
    }

    /**
     * Check if trend is significant
     */
    public function isSignificant(): bool
    {
        return $this->trend_strength === self::STRENGTH_STRONG || 
               $this->trend_strength === self::STRENGTH_MODERATE;
    }

    /**
     * Get trend description
     */
    public function getTrendDescriptionAttribute(): string
    {
        $direction = ucfirst($this->trend_direction);
        $strength = ucfirst($this->trend_strength);
        $change = number_format($this->change_percentage, 1);
        
        return "{$direction} trend ({$strength}) - {$change}% change";
    }

    /**
     * Get trend summary for display
     */
    public function getTrendSummaryAttribute(): array
    {
        return [
            'direction' => $this->trend_direction,
            'strength' => $this->trend_strength,
            'change_percentage' => $this->change_percentage,
            'duration_days' => $this->duration_in_days,
            'color' => $this->trend_color,
            'icon' => $this->trend_icon,
            'description' => $this->trend_description,
            'is_significant' => $this->isSignificant(),
        ];
    }
} 