<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'score',
        'trend',
        'contributing_factors',
        'metrics',
        'ai_analysis',
        'calculated_date',
    ];

    protected $casts = [
        'contributing_factors' => 'array',
        'metrics' => 'array',
        'ai_analysis' => 'array',
        'calculated_date' => 'date',
    ];

    /**
     * Get the athlete that owns the health score.
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the health score grade.
     */
    public function getGradeAttribute(): string
    {
        if ($this->score >= 90) return 'excellent';
        if ($this->score >= 80) return 'good';
        if ($this->score >= 70) return 'fair';
        if ($this->score >= 60) return 'poor';
        return 'critical';
    }

    /**
     * Get the health score color.
     */
    public function getColorAttribute(): string
    {
        return match ($this->grade) {
            'excellent' => 'blue',
            'good' => 'green',
            'fair' => 'yellow',
            'poor' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the health score status.
     */
    public function getStatusAttribute(): string
    {
        return match ($this->grade) {
            'excellent' => 'Wellness',
            'good' => 'Fit',
            'fair' => 'Monitor',
            'poor' => 'Intervention',
            'critical' => 'Critical',
            default => 'Unknown',
        };
    }

    /**
     * Get the trend icon.
     */
    public function getTrendIconAttribute(): string
    {
        return match ($this->trend) {
            'improving' => '🔼',
            'worsening' => '🔽',
            'stable' => '➡️',
            default => '➡️',
        };
    }

    /**
     * Get the trend text.
     */
    public function getTrendTextAttribute(): string
    {
        return match ($this->trend) {
            'improving' => 'Amélioration',
            'worsening' => 'Détérioration',
            'stable' => 'Stable',
            default => 'Stable',
        };
    }

    /**
     * Scope to get scores for a specific athlete.
     */
    public function scopeForAthlete($query, $athleteId)
    {
        return $query->where('athlete_id', $athleteId);
    }

    /**
     * Scope to get recent scores.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('calculated_date', '>=', now()->subDays($days));
    }

    /**
     * Scope to get scores by trend.
     */
    public function scopeByTrend($query, $trend)
    {
        return $query->where('trend', $trend);
    }

    /**
     * Scope to get scores by grade.
     */
    public function scopeByGrade($query, $grade)
    {
        return $query->where('score', '>=', $this->getGradeMinScore($grade))
                    ->where('score', '<=', $this->getGradeMaxScore($grade));
    }

    /**
     * Get the minimum score for a grade.
     */
    private function getGradeMinScore(string $grade): int
    {
        return match ($grade) {
            'excellent' => 90,
            'good' => 80,
            'fair' => 70,
            'poor' => 60,
            'critical' => 0,
            default => 0,
        };
    }

    /**
     * Get the maximum score for a grade.
     */
    private function getGradeMaxScore(string $grade): int
    {
        return match ($grade) {
            'excellent' => 100,
            'good' => 89,
            'fair' => 79,
            'poor' => 69,
            'critical' => 59,
            default => 100,
        };
    }

    /**
     * Get the latest score for an athlete.
     */
    public static function getLatestForAthlete($athleteId)
    {
        return static::where('athlete_id', $athleteId)
                    ->latest('calculated_date')
                    ->first();
    }

    /**
     * Get the average score for an athlete over a period.
     */
    public static function getAverageForAthlete($athleteId, $days = 30)
    {
        return static::where('athlete_id', $athleteId)
                    ->where('calculated_date', '>=', now()->subDays($days))
                    ->avg('score');
    }

    /**
     * Get the trend for an athlete.
     */
    public static function getTrendForAthlete($athleteId)
    {
        $scores = static::where('athlete_id', $athleteId)
                        ->orderBy('calculated_date', 'desc')
                        ->limit(7)
                        ->pluck('score')
                        ->toArray();

        if (count($scores) < 2) {
            return 'stable';
        }

        $recent = array_slice($scores, 0, 3);
        $older = array_slice($scores, 3);

        if (empty($older)) {
            return 'stable';
        }

        $recentAvg = array_sum($recent) / count($recent);
        $olderAvg = array_sum($older) / count($older);

        if ($recentAvg > $olderAvg + 5) {
            return 'improving';
        } elseif ($recentAvg < $olderAvg - 5) {
            return 'worsening';
        } else {
            return 'stable';
        }
    }
} 