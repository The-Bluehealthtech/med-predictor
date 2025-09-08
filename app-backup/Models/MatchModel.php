<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatchModel extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $casts = [
        'match_date' => 'date',
        'kickoff_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'competition_id',
        'home_team_id',
        'away_team_id',
        'home_club_id',
        'away_club_id',
        'match_date',
        'kickoff_time',
        'venue',
        'stadium',
        'capacity',
        'attendance',
        'weather_conditions',
        'pitch_condition',
        'referee',
        'assistant_referee_1',
        'assistant_referee_2',
        'fourth_official',
        'var_referee',
        'match_status',
        'home_score',
        'away_score',
        'home_penalties',
        'away_penalties',
        'home_yellow_cards',
        'away_yellow_cards',
        'home_red_cards',
        'away_red_cards',
        'home_possession',
        'away_possession',
        'home_shots',
        'away_shots',
        'home_shots_on_target',
        'away_shots_on_target',
        'home_corners',
        'away_corners',
        'home_fouls',
        'away_fouls',
        'home_offsides',
        'away_offsides',
        'match_highlights',
        'match_report',
        'broadcast_info',
        'ticket_info',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class, 'match_id');
    }

    public function rosters(): HasMany
    {
        return $this->hasMany(MatchRoster::class, 'match_id');
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(Lineup::class, 'match_id');
    }

    public function officials()
    {
        return $this->hasMany(\App\Models\MatchOfficial::class, 'match_id');
    }

    public function matchSheet(): HasOne
    {
        return $this->hasOne(MatchSheet::class, 'match_id');
    }
} 