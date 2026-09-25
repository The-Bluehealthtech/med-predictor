<?php

namespace App\Models\V3;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Player;

class PerformanceMetric extends Model
{
    protected $table = 'v3_performance_metrics';

    protected $fillable = [
        'player_id',
        'metric_type',
        'value',
        'unit',
        'category',
        'subcategory',
        'context',
        'source',
        'confidence',
        'timestamp',
        'metadata',
    ];

    protected $casts = [
        'value' => 'float',
        'context' => 'array',
        'metadata' => 'array',
        'timestamp' => 'datetime',
        'confidence' => 'float',
    ];

    /**
     * Types de métriques disponibles
     */
    const TYPES = [
        'physical' => ['speed', 'endurance', 'strength', 'agility', 'recovery'],
        'technical' => ['passing', 'shooting', 'dribbling', 'tackling', 'positioning'],
        'tactical' => ['decision_making', 'game_intelligence', 'teamwork', 'leadership'],
        'mental' => ['concentration', 'motivation', 'pressure_handling', 'adaptability'],
        'biometric' => ['heart_rate', 'oxygen_saturation', 'lactate_threshold', 'muscle_fatigue'],
    ];

    /**
     * Catégories de métriques
     */
    const CATEGORIES = [
        'match_performance',
        'training_session',
        'recovery_session',
        'medical_assessment',
        'fitness_test',
        'biometric_monitoring',
    ];

    /**
     * Relation avec le joueur
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Scope pour un type de métrique spécifique
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('metric_type', $type);
    }

    /**
     * Scope pour une catégorie spécifique
     */
    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope pour les métriques récentes
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('timestamp', '>=', now()->subDays($days));
    }

    /**
     * Obtenir la valeur formatée avec l'unité
     */
    public function getFormattedValueAttribute(): string
    {
        return $this->value . ' ' . $this->unit;
    }

    /**
     * Vérifier si la métrique est dans la plage normale
     */
    public function isInNormalRange(float $min, float $max): bool
    {
        return $this->value >= $min && $this->value <= $max;
    }

    /**
     * Calculer la tendance par rapport à une valeur précédente
     */
    public function calculateTrend(float $previousValue): float
    {
        if ($previousValue == 0) return 0;
        return (($this->value - $previousValue) / $previousValue) * 100;
    }
}
