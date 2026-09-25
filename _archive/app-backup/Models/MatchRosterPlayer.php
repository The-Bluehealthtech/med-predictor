<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchRosterPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_roster_id',
        'player_id',
        'position',
        'is_starter',
        'jersey_number',
    ];

    protected $casts = [
        'is_starter' => 'boolean',
    ];

    /**
     * Get the match roster that owns the player
     */
    public function roster(): BelongsTo
    {
        return $this->belongsTo(MatchRoster::class, 'match_roster_id');
    }

    /**
     * Get the player
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Scope for starters
     */
    public function scopeStarters($query)
    {
        return $query->where('is_starter', true);
    }

    /**
     * Scope for substitutes
     */
    public function scopeSubstitutes($query)
    {
        return $query->where('is_starter', false);
    }

    /**
     * Scope for specific position
     */
    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }
} 