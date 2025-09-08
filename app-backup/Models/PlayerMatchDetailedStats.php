<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlayerMatchDetailedStats extends Model
{
    use HasFactory;

    protected $table = 'player_match_detailed_stats';

    protected $fillable = [
        'player_id', 'match_id', 'team_id', 'competition_id', 'season_id',
        'position_played', 'minutes_played', 'started_match', 'goals_scored',
        'assists_provided', 'shots_total', 'shots_on_target', 'passes_total',
        'passes_completed', 'key_passes', 'crosses_total', 'crosses_completed',
        'dribbles_attempted', 'dribbles_completed', 'tackles_total', 'tackles_won',
        'interceptions', 'clearances', 'yellow_cards', 'red_cards',
        'distance_covered_km', 'max_speed_kmh', 'avg_speed_kmh', 'sprint_count',
        'acceleration_count', 'direction_changes', 'match_rating', 'performance_level',
        'man_of_match', 'team_of_week', 'fifa_rating', 'data_source', 'data_confidence'
    ];

    protected $casts = [
        'started_match' => 'boolean',
        'man_of_match' => 'boolean',
        'team_of_week' => 'boolean',
        'goals_scored' => 'integer',
        'assists_provided' => 'integer',
        'shots_total' => 'integer',
        'shots_on_target' => 'integer',
        'passes_total' => 'integer',
        'passes_completed' => 'integer',
        'key_passes' => 'integer',
        'crosses_total' => 'integer',
        'crosses_completed' => 'integer',
        'dribbles_attempted' => 'integer',
        'dribbles_completed' => 'integer',
        'tackles_total' => 'integer',
        'tackles_won' => 'integer',
        'interceptions' => 'integer',
        'clearances' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
        'distance_covered_km' => 'decimal:2',
        'max_speed_kmh' => 'decimal:2',
        'avg_speed_kmh' => 'decimal:2',
        'sprint_count' => 'integer',
        'acceleration_count' => 'integer',
        'direction_changes' => 'integer',
        'match_rating' => 'decimal:2',
        'fifa_rating' => 'decimal:2',
        'data_confidence' => 'decimal:2'
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}
