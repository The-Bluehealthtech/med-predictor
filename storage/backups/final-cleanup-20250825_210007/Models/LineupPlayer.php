<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineupPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'lineup_id',
        'player_id',
        'is_substitute',
        'position_order',
        'assigned_position',
        'tactical_instructions',
        'fitness_status',
        'expected_performance'
    ];

    protected $casts = [
        'is_substitute' => 'boolean',
        'position_order' => 'integer',
        'expected_performance' => 'decimal:1'
    ];

    // Relationships
    public function lineup(): BelongsTo
    {
        return $this->belongsTo(Lineup::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    // Scopes
    public function scopeStarters($query)
    {
        return $query->where('is_substitute', false);
    }

    public function scopeSubstitutes($query)
    {
        return $query->where('is_substitute', true);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('assigned_position', $position);
    }

    // Methods
    public function getStatusColor(): string
    {
        return match($this->fitness_status) {
            'fit' => 'green',
            'doubtful' => 'yellow',
            'injured' => 'red',
            'suspended' => 'gray',
            default => 'gray'
        };
    }

    public function getStatusText(): string
    {
        return match($this->fitness_status) {
            'fit' => 'Fit',
            'doubtful' => 'Doubtful',
            'injured' => 'Injured',
            'suspended' => 'Suspended',
            default => 'Unknown'
        };
    }

    public function isStarter(): bool
    {
        return !$this->is_substitute;
    }

    public function isSubstitute(): bool
    {
        return $this->is_substitute;
    }

    public function getFormattedPosition(): string
    {
        if ($this->is_substitute) {
            return "Sub {$this->position_order}";
        }
        
        return $this->assigned_position ?? "Position {$this->position_order}";
    }
} 