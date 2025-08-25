<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'match_sheet_id',
        'player_id',
        'team_id',
        'assisted_by_player_id',
        'substituted_player_id',
        'recorded_by_user_id',
        'event_type',
        'type',
        'minute',
        'extra_time_minute',
        'period',
        'event_data',
        'description',
        'location',
        'severity',
        'is_confirmed',
        'is_contested',
        'contest_reason',
        'recorded_at'
    ];

    protected $casts = [
        'event_data' => 'array',
        'is_confirmed' => 'boolean',
        'is_contested' => 'boolean',
        'recorded_at' => 'datetime',
        'minute' => 'integer',
        'extra_time_minute' => 'integer'
    ];

    // Relationships
    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function assistedByPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'assisted_by_player_id');
    }

    public function substitutedPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'substituted_player_id');
    }

    public function recordedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    // Scopes
    public function scopeGoals($query)
    {
        return $query->where('event_type', 'goal');
    }

    public function scopeCards($query)
    {
        return $query->whereIn('event_type', ['yellow_card', 'red_card']);
    }

    public function scopeSubstitutions($query)
    {
        return $query->whereIn('event_type', ['substitution_in', 'substitution_out']);
    }

    public function scopeByTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeByPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('is_confirmed', true);
    }

    public function scopeContested($query)
    {
        return $query->where('is_contested', true);
    }

    // Methods
    public function getDisplayTimeAttribute(): string
    {
        $time = $this->minute ?? 0;
        
        if ($this->extra_time_minute) {
            return "{$time}+{$this->extra_time_minute}'";
        }
        
        return "{$time}'";
    }

    public function getEventTypeLabelAttribute(): string
    {
        return match($this->event_type) {
            'goal' => 'Goal',
            'assist' => 'Assist',
            'yellow_card' => 'Yellow Card',
            'red_card' => 'Red Card',
            'substitution_in' => 'Substitution In',
            'substitution_out' => 'Substitution Out',
            'injury' => 'Injury',
            'save' => 'Save',
            'missed_penalty' => 'Missed Penalty',
            'penalty_saved' => 'Penalty Saved',
            'own_goal' => 'Own Goal',
            'var_decision' => 'VAR Decision',
            'free_kick_goal' => 'Free Kick Goal',
            'header_goal' => 'Header Goal',
            'volley_goal' => 'Volley Goal',
            'long_range_goal' => 'Long Range Goal',
            'penalty_goal' => 'Penalty Goal',
            default => ucfirst(str_replace('_', ' ', $this->event_type))
        };
    }

    public function getEventTypeColorAttribute(): string
    {
        return match($this->event_type) {
            'goal' => 'green',
            'assist' => 'blue',
            'yellow_card' => 'yellow',
            'red_card' => 'red',
            'substitution_in' => 'green',
            'substitution_out' => 'gray',
            'injury' => 'red',
            'save' => 'blue',
            'missed_penalty' => 'red',
            'penalty_saved' => 'green',
            'own_goal' => 'red',
            'var_decision' => 'purple',
            default => 'gray'
        };
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
            default => 'gray'
        };
    }

    public function isGoalEvent(): bool
    {
        return in_array($this->event_type, [
            'goal', 'own_goal', 'free_kick_goal', 'header_goal', 
            'volley_goal', 'long_range_goal', 'penalty_goal'
        ]);
    }

    public function isCardEvent(): bool
    {
        return in_array($this->event_type, ['yellow_card', 'red_card']);
    }

    public function isSubstitutionEvent(): bool
    {
        return in_array($this->event_type, ['substitution_in', 'substitution_out']);
    }

    public function isMatchControlEvent(): bool
    {
        return in_array($this->event_type, [
            'match_start', 'half_time', 'full_time', 'extra_time_start', 
            'extra_time_end', 'penalty_shootout_start', 'penalty_shootout_end'
        ]);
    }

    public function getEventDescription(): string
    {
        if ($this->description) {
            return $this->description;
        }

        $playerName = $this->player ? $this->player->name : 'Unknown Player';
        $teamName = $this->team ? $this->team->name : 'Unknown Team';

        return match($this->event_type) {
            'goal' => "Goal scored by {$playerName} ({$teamName})",
            'assist' => "Assist by {$playerName} ({$teamName})",
            'yellow_card' => "Yellow card for {$playerName} ({$teamName})",
            'red_card' => "Red card for {$playerName} ({$teamName})",
            'substitution_in' => "{$playerName} comes on for {$this->substitutedPlayer?->name} ({$teamName})",
            'substitution_out' => "{$playerName} is substituted ({$teamName})",
            'injury' => "Injury to {$playerName} ({$teamName})",
            'save' => "Save by {$playerName} ({$teamName})",
            default => "{$this->event_type_label} - {$playerName} ({$teamName})"
        };
    }

    public function contest($reason): void
    {
        $this->update([
            'is_contested' => true,
            'contest_reason' => $reason
        ]);
    }

    public function confirm(): void
    {
        $this->update([
            'is_confirmed' => true,
            'is_contested' => false,
            'contest_reason' => null
        ]);
    }

    // Audit Trail Methods
    public function getAuditIdentifier(): string
    {
        return "MatchEvent:{$this->id}";
    }

    public function getAuditDisplayName(): string
    {
        $playerName = $this->player ? $this->player->name : 'Unknown Player';
        $teamName = $this->team ? $this->team->name : 'Unknown Team';
        return "{$this->event_type_label} - {$playerName} ({$teamName}) at {$this->display_time}";
    }

    public function getAuditType(): string
    {
        return 'match_event';
    }

    public function getAuditData(): array
    {
        return [
            'match_id' => $this->match_id,
            'team_id' => $this->team_id,
            'player_id' => $this->player_id,
            'event_type' => $this->event_type,
            'minute' => $this->minute,
            'period' => $this->period,
            'is_confirmed' => $this->is_confirmed,
            'is_contested' => $this->is_contested
        ];
    }
}
