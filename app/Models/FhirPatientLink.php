<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Lien entre un joueur et un Patient du serveur FHIR de FIT (PIXm / PDQm). */
class FhirPatientLink extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['snapshot' => 'array', 'decided_at' => 'datetime', 'synced_at' => 'datetime'];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
