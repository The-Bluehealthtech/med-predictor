<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Réglages du barème d'une fédération : devise, saison, date de référence de l'âge, tarifs des officiels. */
class LicenseScaleSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['official_fees' => 'array'];
}
