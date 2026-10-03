<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Consentement du joueur au partage hors du club (IHE PCF Basic Consent). */
class PlayerConsent extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['period_end' => 'date', 'signed_at' => 'datetime', 'revoked_at' => 'datetime'];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(PrivacyPolicy::class, 'privacy_policy_id');
    }

    public function signatureRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentSignatureRequest::class, 'signature_request_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
