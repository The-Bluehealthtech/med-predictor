<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'round',
        'standings',
    ];

    protected $casts = [
        'standings' => 'array',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}
