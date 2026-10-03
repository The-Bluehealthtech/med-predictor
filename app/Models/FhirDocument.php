<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Document publié par FIT sur le serveur FHIR (IHE MHD), ex. IPS du passeport médical. */
class FhirDocument extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['published_at' => 'datetime'];

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
