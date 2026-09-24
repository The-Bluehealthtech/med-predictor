<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FitScoreSnapshot extends Model
{
    protected $fillable = [
        'player_id',
        'snapshot_at',
        'physical_score',
        'technical_score',
        'tactical_score',
        'mental_score',
        'social_score',
        'fit_score',
        'confidence_score',
        'is_complete',
        'window_days',
        'calculation_version',
        'evidence',
        'generated_by_user_id',
    ];

    protected $casts = [
        'snapshot_at' => 'datetime',
        'physical_score' => 'float',
        'technical_score' => 'float',
        'tactical_score' => 'float',
        'mental_score' => 'float',
        'social_score' => 'float',
        'fit_score' => 'float',
        'confidence_score' => 'float',
        'is_complete' => 'boolean',
        'window_days' => 'integer',
        'evidence' => 'array',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_user_id');
    }
}
