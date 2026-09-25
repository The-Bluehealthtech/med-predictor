<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerTrophy extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'trophy_name',
        'trophy_type',
        'competition',
        'year',
        'club',
        'country',
        'description'
    ];

    protected $casts = [
        'year' => 'integer'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
