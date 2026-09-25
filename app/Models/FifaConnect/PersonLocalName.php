<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonLocalName extends Model
{
    protected $table = 'fifa_connect_person_local_names';

    protected $guarded = [];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
