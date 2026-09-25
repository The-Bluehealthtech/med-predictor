<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Standing extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'season_id',
        'team_id',
        'played',
        'won',
        'drawn',
        'lost',
        'goals_for',
        'goals_against',
        'goal_difference',
        'points',
        'position',
        'form',
        'home_played',
        'home_won',
        'home_drawn',
        'home_lost',
        'home_goals_for',
        'home_goals_against',
        'away_played',
        'away_won',
        'away_drawn',
        'away_lost',
        'away_goals_for',
        'away_goals_against',
        'clean_sheets',
        'failed_to_score',
        'biggest_win',
        'biggest_loss',
        'last_updated'
    ];

    protected $casts = [
        'form' => 'array',
        'last_updated' => 'datetime'
    ];

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
