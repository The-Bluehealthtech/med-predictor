<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Catégorie d'âge du barème d'une fédération, pour un genre et une discipline FIFA Connect. */
class LicenseAgeCategory extends Model
{
    public const PCMA_RULES = ['none' => 'Jamais', 'pro' => 'Joueurs professionnels', 'all' => 'Tous les joueurs'];

    protected $guarded = ['id'];

    protected $casts = ['allowed_levels' => 'array', 'fees' => 'array', 'required_documents' => 'array', 'max_age' => 'integer'];
}
