<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;

class MatchOfficial extends Model
{
    protected $table = 'fifa_connect_match_officials';

    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
    ];
}
