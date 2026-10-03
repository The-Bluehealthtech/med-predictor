<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Prescription d'examen transmise au serveur FHIR (ServiceRequest) et suivi de ses comptes rendus. */
class FhirOrder extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['report_ids' => 'array', 'sent_at' => 'datetime', 'results_at' => 'datetime'];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function label(): string
    {
        return config("fhir.orders.{$this->module}.label", $this->module);
    }
}
