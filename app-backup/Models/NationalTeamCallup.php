<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NationalTeamCallup extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'title',
        'message',
        'callup_date',
        'priority',
        'type',
        'opponents',
        'venue',
        'meeting_time',
        'is_confirmed'
    ];

    protected $casts = [
        'callup_date' => 'date',
        'meeting_time' => 'datetime',
        'is_confirmed' => 'boolean'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
