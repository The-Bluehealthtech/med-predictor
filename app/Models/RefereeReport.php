<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefereeReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'referee_id',
        'competition_name',
        'match_date',
        'stadium',
        'weather',
        'pitch_condition',
        'main_referee',
        'assistant_referee_1',
        'assistant_referee_2',
        'fourth_official',
        'var_referee',
        'avar_referee',
        'final_score',
        'half_time_score',
        'extra_time_minutes',
        'penalty_shootout',
        'penalty_shootout_score',
        'goals',
        'yellow_cards',
        'red_cards',
        'substitutions',
        'injuries',
        'disciplinary_incidents',
        'crowd_incidents',
        'safety_issues',
        'general_comments',
        'match_quality_assessment',
        'match_rating',
        'status',
        'submitted_at',
        'electronic_signature'
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'goals' => 'array',
        'yellow_cards' => 'array',
        'red_cards' => 'array',
        'substitutions' => 'array',
        'injuries' => 'array',
        'penalty_shootout' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    // Relations
    public function match()
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }
}

        'match_id',
        'referee_id',
        'competition_name',
        'match_date',
        'stadium',
        'weather',
        'pitch_condition',
        'main_referee',
        'assistant_referee_1',
        'assistant_referee_2',
        'fourth_official',
        'var_referee',
        'avar_referee',
        'final_score',
        'half_time_score',
        'extra_time_minutes',
        'penalty_shootout',
        'penalty_shootout_score',
        'goals',
        'yellow_cards',
        'red_cards',
        'substitutions',
        'injuries',
        'disciplinary_incidents',
        'crowd_incidents',
        'safety_issues',
        'general_comments',
        'match_quality_assessment',
        'match_rating',
        'status',
        'submitted_at',
        'electronic_signature'
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'goals' => 'array',
        'yellow_cards' => 'array',
        'red_cards' => 'array',
        'substitutions' => 'array',
        'injuries' => 'array',
        'penalty_shootout' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    // Relations
    public function match()
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }
}
