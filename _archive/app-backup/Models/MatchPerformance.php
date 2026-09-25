<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'result',
        'match_date',
        'opponent',
        'competition',
        'venue',
        'goals_scored',
        'assists',
        'rating',
        'minutes_played',
        'notes'
    ];

    protected $casts = [
        'match_date' => 'date',
        'rating' => 'decimal:1',
        'minutes_played' => 'integer'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
