<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Étape de l'historique d'une demande de licence (suivi club et fédération). */
class PlayerLicenseEvent extends Model
{
    public const UPDATED_AT = null;

    public const LABELS = [
        'submitted' => 'Demande envoyée à la fédération',
        'document_added' => 'Pièce justificative ajoutée',
        'identity_checked' => 'Identité vérifiée auprès de FIFA ID',
        'integrity_review' => 'Revue anti-fraude enregistrée',
        'info_requested' => 'Complément demandé au club',
        'responded' => 'Complément fourni par le club',
        'approved' => 'Licence approuvée',
        'rejected' => 'Demande refusée',
    ];

    protected $guarded = ['id'];

    protected $casts = ['created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function label(): string
    {
        return self::LABELS[$this->action] ?? $this->action;
    }
}
