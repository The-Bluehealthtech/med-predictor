<?php

namespace App\Models\FifaConnect;

use Illuminate\Database\Eloquent\Model;

class OrganisationNationalIdentifier extends Model
{
    protected $table = 'fifa_connect_organisation_national_identifiers';

    protected $guarded = [];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];
}
