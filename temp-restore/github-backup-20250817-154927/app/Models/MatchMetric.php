<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_performance_id',
        'shots_on_target',
        'total_shots',
        'shot_accuracy',
        'key_passes',
        'successful_crosses',
        'successful_dribbles',
        'distance',
        'max_speed',
        'avg_speed',
        'sprints',
        'accelerations',
        'decelerations',
        'direction_changes',
        'jumps',
        'pass_accuracy',
        'long_passes',
        'crosses',
        'tackles',
        'interceptions',
        'clearances'
    ];

    protected $casts = [
        'shot_accuracy' => 'decimal:2',
        'distance' => 'decimal:2',
        'max_speed' => 'decimal:2',
        'avg_speed' => 'decimal:2',
        'pass_accuracy' => 'decimal:2'
    ];

    public function matchPerformance()
    {
        return $this->belongsTo(MatchPerformance::class);
    }
}
