<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    protected $table = 'fifa_connect_certifications';

    protected $guarded = [];

    protected $casts = [
        'certification_valid_from' => 'date',
        'certification_valid_to' => 'date',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
