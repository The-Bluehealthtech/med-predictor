<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerSeasonStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'player_id',
        'team_id',
        'matches_played',
        'minutes_played',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
        'clean_sheets',
        'saves',
        'goals_conceded',
    ];

    protected $casts = [
        'matches_played' => 'integer',
        'minutes_played' => 'integer',
        'goals' => 'integer',
        'assists' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
        'clean_sheets' => 'integer',
        'saves' => 'integer',
        'goals_conceded' => 'integer',
    ];

    /**
     * Get the competition that owns the stats
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the player that owns the stats
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the team that owns the stats
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get total points (goals + assists)
     */
    public function getTotalPointsAttribute(): int
    {
        return $this->goals + $this->assists;
    }

    /**
     * Get goals per match ratio
     */
    public function getGoalsPerMatchAttribute(): float
    {
        return $this->matches_played > 0 ? round($this->goals / $this->matches_played, 2) : 0;
    }

    /**
     * Get assists per match ratio
     */
    public function getAssistsPerMatchAttribute(): float
    {
        return $this->matches_played > 0 ? round($this->assists / $this->matches_played, 2) : 0;
    }

    /**
     * Get minutes per match ratio
     */
    public function getMinutesPerMatchAttribute(): float
    {
        return $this->matches_played > 0 ? round($this->minutes_played / $this->matches_played, 2) : 0;
    }

    /**
     * Scope for top scorers
     */
    public function scopeTopScorers($query, int $limit = 10)
    {
        return $query->orderBy('goals', 'desc')->limit($limit);
    }

    /**
     * Scope for top assisters
     */
    public function scopeTopAssisters($query, int $limit = 10)
    {
        return $query->orderBy('assists', 'desc')->limit($limit);
    }

    /**
     * Scope for most appearances
     */
    public function scopeMostAppearances($query, int $limit = 10)
    {
        return $query->orderBy('matches_played', 'desc')->limit($limit);
    }

    /**
     * Scope for goalkeepers
     */
    public function scopeGoalkeepers($query)
    {
        return $query->whereHas('player', function ($q) {
            $q->where('position', 'goalkeeper');
        });
    }

    /**
     * Scope for outfield players
     */
    public function scopeOutfieldPlayers($query)
    {
        return $query->whereHas('player', function ($q) {
            $q->where('position', '!=', 'goalkeeper');
        });
    }
} 