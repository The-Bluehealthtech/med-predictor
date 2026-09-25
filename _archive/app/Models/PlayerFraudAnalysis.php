<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerFraudAnalysis extends Model
{
    protected $fillable = [
        'player_license_id',
        'identity_score',
        'age_score',
        'anomalies',
        'status',
    ];

    protected $casts = [
        'anomalies' => 'array',
    ];

    public function license()
    {
        return $this->belongsTo(PlayerLicense::class, 'player_license_id');
    }
} 