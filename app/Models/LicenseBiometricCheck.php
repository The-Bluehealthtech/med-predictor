<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseBiometricCheck extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'score' => 'float',
        'threshold' => 'float',
        'metadata' => 'array',
        'checked_at' => 'datetime',
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
