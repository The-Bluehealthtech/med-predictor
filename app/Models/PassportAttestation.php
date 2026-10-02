<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Attestation d'un passeport médical par un médecin (signature électronique simple). */
class PassportAttestation extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['content' => 'array', 'signed_at' => 'datetime'];

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
