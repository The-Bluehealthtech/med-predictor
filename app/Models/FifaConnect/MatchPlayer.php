<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;

class MatchPlayer extends Model
{
    protected $table = 'fifa_connect_match_players';

    protected $guarded = [];

    protected $casts = [
        'captain' => 'boolean',
        'goalkeeper' => 'boolean',
        'starting_lineup' => 'boolean',
        'played' => 'boolean',
        'date_of_birth' => 'date',
    ];
}
