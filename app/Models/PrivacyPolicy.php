<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Politique de confidentialité d'une fédération (version immuable, référencée par les consentements). */
class PrivacyPolicy extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['published_at' => 'datetime'];

    public function uri(): string
    {
        return route('privacy-policies.show', $this);
    }
}
