<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Pièce justificative d'une demande de licence (contenu en base, encodé en base64). */
class PlayerLicenseDocument extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['content_base64'];

    public function license(): BelongsTo
    {
        return $this->belongsTo(PlayerLicense::class, 'player_license_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function label(): string
    {
        return config('licensing.documents.' . $this->document_type, $this->document_type);
    }
}
