<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalPlayerPerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'index_ksa',
        'mistakes_leading_to_goals',
        'mistakes_leading_to_chances',
        'chances',
        'chances_successful',
        'chances_successful_percentage',
        'chances_created',
        'involvement_in_scoring_attacks',
        'goals_by_head',
        'free_kick_shots',
        'free_kick_goals',
        'progressive_passes',
        'progressive_passes_accurate',
        'short_passes',
        'short_passes_accurate',
        'long_passes',
        'long_passes_accurate',
        'passes_for_a_shot',
        'super_long_passes',
        'challenges',
        'challenges_won',
        'defensive_challenges',
        'defensive_challenges_won',
        'attacking_challenges',
        'attacking_challenges_won',
        'aerial_challenges',
        'aerial_challenges_won',
        'dribbles',
        'dribbles_successful',
        'dribbling_final_third',
        'dribbling_final_third_successful',
        'loose_ball_recoveries',
        'metric_name',
        'metric_value',
        'metric_unit',
        'source',
        'season',
        'competition',
        'measured_at',
        'raw_data',
        'notes',
    ];

    protected $casts = [
        'metric_value' => 'decimal:4',
        'measured_at' => 'date',
        'raw_data' => 'array',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
