<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'match_date' => 'date',
        'kickoff_time' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Club::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Club::class, 'away_team_id');
    }

    public function officials()
    {
        return $this->hasMany(MatchOfficial::class, 'match_id');
    }

    public function rosters()
    {
        return $this->hasMany(\App\Models\MatchRoster::class, 'match_id');
    }

    public function events()
    {
        return $this->hasMany(\App\Models\MatchEvent::class, 'match_id');
    }

    // Le champ status est directement mappé dans la table
} 