<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchRoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'team_id',
    ];

    public function match()
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function players()
    {
        return $this->hasMany(MatchRosterPlayer::class, 'match_roster_id');
    }
} 