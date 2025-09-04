<?php

namespace App\Models\V3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Player;

class AiPrediction extends Model
{
    protected $table = 'ai_predictions';

    protected $fillable = [
        'player_id',
        'prediction_type',
        'input_data',
        'prediction_result',
        'confidence_score',
        'accuracy_score',
        'model_version',
        'processing_time',
        'cache_status',
        'metadata',
        'expires_at',
    ];

    protected $casts = [
        'input_data' => 'array',
        'prediction_result' => 'array',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'processing_time' => 'float',
    ];

    /**
     * Relation avec le joueur
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Scope pour les prédictions actives (non expirées)
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope pour un type de prédiction spécifique
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('prediction_type', $type);
    }

    /**
     * Vérifier si la prédiction est encore valide
     */
    public function isActive(): bool
    {
        return $this->expires_at && $this->expires_at->isFuture();
    }

    /**
     * Obtenir le score de confiance formaté
     */
    public function getConfidencePercentageAttribute(): float
    {
        return round($this->confidence_score * 100, 2);
    }

    /**
     * Obtenir le score de précision formaté
     */
    public function getAccuracyPercentageAttribute(): float
    {
        return round($this->accuracy_score * 100, 2);
    }
}
