<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseIntegrityReview extends Model
{
    public const STATUSES = [
        'coherent' => 'Cohérent',
        'uncertain' => 'À vérifier',
        'mismatch' => 'Écart constaté',
        'insufficient' => 'Données insuffisantes',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'evidence' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(PlayerLicense::class, 'player_license_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
