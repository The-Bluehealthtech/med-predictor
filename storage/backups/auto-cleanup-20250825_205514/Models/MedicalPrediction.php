<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalPrediction extends Model
{
    protected $fillable = [
        'health_record_id',
        'player_id',
        'user_id',
        'prediction_type',
        'predicted_condition',
        'risk_probability',
        'confidence_score',
        'prediction_factors',
        'recommendations',
        'prediction_date',
        'valid_until',
        'status',
        'ai_model_version',
        'prediction_notes'
    ];

    protected $casts = [
        'prediction_date' => 'datetime',
        'valid_until' => 'datetime',
        'risk_probability' => 'float',
        'confidence_score' => 'float',
        'prediction_factors' => 'array',
        'recommendations' => 'array',
        'prediction_notes' => 'array'
    ];

    public function healthRecord(): BelongsTo
    {
        return $this->belongsTo(HealthRecord::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRiskLevelAttribute(): string
    {
        if ($this->risk_probability < 0.3) return 'Faible';
        if ($this->risk_probability < 0.6) return 'Modéré';
        return 'Élevé';
    }

    public function getConfidenceLevelAttribute(): string
    {
        if ($this->confidence_score < 0.5) return 'Faible';
        if ($this->confidence_score < 0.8) return 'Modéré';
        return 'Élevé';
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }
}
