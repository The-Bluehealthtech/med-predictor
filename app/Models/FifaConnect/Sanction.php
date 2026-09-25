<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sanction extends Model
{
    protected $table = 'fifa_connect_sanctions';

    protected $guarded = [];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public function disciplineCase(): BelongsTo
    {
        return $this->belongsTo(
            DisciplineCase::class,
            'case_id'
        );
    }
}
