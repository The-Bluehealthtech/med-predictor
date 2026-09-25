<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonNationalIdentifier extends Model
{
    protected $table = 'fifa_connect_person_national_identifiers';

    protected $guarded = [];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
