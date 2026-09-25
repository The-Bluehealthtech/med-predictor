<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;

class MatchPhase extends Model
{
    protected $table = 'fifa_connect_match_phases';

    protected $guarded = [];

    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
    ];
}
