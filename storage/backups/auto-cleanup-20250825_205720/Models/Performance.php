<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Performance extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'club_id',
        'performance_type',
        'date',
        'match_date',
        'overall_score',
        'trend',
        'notes',
        'match_id',
        'minutes_played',
        'goals_scored',
        'assists',
        'passes_completed',
        'passes_attempted',
        'pass_accuracy',
        'tackles_won',
        'tackles_attempted',
        'interceptions',
        'distance_covered',
        'sprint_count',
        'max_speed',
        'avg_speed',
        'shots_on_target',
        'shots_total',
        'yellow_cards',
        'red_cards',
        'rating',
        // Fitness metrics
        'speed',
        'stamina',
        'strength',
        'agility',
        'recovery',
        // Technical metrics
        'passing',
        'shooting',
        'dribbling',
        'defending',
        'ball_control',
        // Tactical metrics
        'positioning',
        'vision',
        'decision_making',
        'teamwork',
        'leadership',
        // Mental metrics
        'confidence',
        'focus',
        'motivation',
        'pressure_handling',
        'adaptability',
        // Overall metrics
        'match_rating',
        'consistency',
        'impact',
        'potential',
        'form',
    ];

    protected $casts = [
        'date' => 'date',
        'match_date' => 'date',
        'overall_score' => 'integer',
        'match_id' => 'integer',
        'minutes_played' => 'integer',
        'goals_scored' => 'integer',
        'assists' => 'integer',
        'passes_completed' => 'integer',
        'passes_attempted' => 'integer',
        'pass_accuracy' => 'integer',
        'tackles_won' => 'integer',
        'tackles_attempted' => 'integer',
        'interceptions' => 'integer',
        'distance_covered' => 'integer',
        'sprint_count' => 'integer',
        'max_speed' => 'float',
        'avg_speed' => 'float',
        'shots_on_target' => 'integer',
        'shots_total' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
        'rating' => 'float',
        // Fitness metrics
        'speed' => 'integer',
        'stamina' => 'integer',
        'strength' => 'integer',
        'agility' => 'integer',
        'recovery' => 'integer',
        // Technical metrics
        'passing' => 'integer',
        'shooting' => 'integer',
        'dribbling' => 'integer',
        'defending' => 'integer',
        'ball_control' => 'integer',
        // Tactical metrics
        'positioning' => 'integer',
        'vision' => 'integer',
        'decision_making' => 'integer',
        'teamwork' => 'integer',
        'leadership' => 'integer',
        // Mental metrics
        'confidence' => 'integer',
        'focus' => 'integer',
        'motivation' => 'integer',
        'pressure_handling' => 'integer',
        'adaptability' => 'integer',
        // Overall metrics
        'match_rating' => 'integer',
        'consistency' => 'integer',
        'impact' => 'integer',
        'potential' => 'integer',
        'form' => 'integer',
    ];

    /**
     * Get the player that owns the performance.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the club that owns the performance.
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the pass accuracy percentage.
     */
    public function getPassAccuracyAttribute(): float
    {
        if ($this->passes_attempted > 0) {
            return round(($this->passes_completed / $this->passes_attempted) * 100, 2);
        }
        return 0;
    }

    /**
     * Get the tackle success percentage.
     */
    public function getTackleSuccessAttribute(): float
    {
        if ($this->tackles_attempted > 0) {
            return round(($this->tackles_won / $this->tackles_attempted) * 100, 2);
        }
        return 0;
    }

    /**
     * Get the shot accuracy percentage.
     */
    public function getShotAccuracyAttribute(): float
    {
        if ($this->shots_total > 0) {
            return round(($this->shots_on_target / $this->shots_total) * 100, 2);
        }
        return 0;
    }

    /**
     * Get the distance per minute.
     */
    public function getDistancePerMinuteAttribute(): float
    {
        if ($this->minutes_played > 0) {
            return round($this->distance_covered / $this->minutes_played, 2);
        }
        return 0;
    }

    /**
     * Scope a query to only include performances within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('match_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include performances for a specific player.
     */
    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    /**
     * Scope a query to only include performances above a certain rating.
     */
    public function scopeHighRating($query, $minRating = 7.0)
    {
        return $query->where('rating', '>=', $minRating);
    }
} 