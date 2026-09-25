<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;

class TeamOfficial extends Model
{
    protected $table = 'fifa_connect_team_officials';

    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
    ];
}
