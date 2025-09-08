<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'title',
        'description',
        'session_date',
        'start_time',
        'end_time',
        'priority',
        'type',
        'location',
        'coach',
        'is_mandatory'
    ];

    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_mandatory' => 'boolean'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
