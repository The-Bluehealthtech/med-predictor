<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'season_id',
        'competition_id',
        'matches_played',
        'minutes_played',
        'goals_scored',
        'assists',
        'shots_on_target',
        'shots_total',
        'passes_completed',
        'passes_total',
        'key_passes',
        'crosses_completed',
        'crosses_total',
        'tackles_won',
        'tackles_total',
        'interceptions',
        'clearances',
        'blocks',
        'duels_won',
        'duels_total',
        'fouls_committed',
        'fouls_drawn',
        'yellow_cards',
        'red_cards',
        'distance_covered',
        'sprint_distance',
        'max_speed',
        'sprints_count',
        'avg_speed',
        'expected_goals',
        'expected_assists',
        'pass_accuracy',
        'shot_accuracy',
        'tackle_success_rate',
        'duel_success_rate',
        'overall_rating',
        'attacking_rating',
        'defending_rating',
        'physical_rating',
        'technical_rating',
        'mental_rating',
    ];

    protected $casts = [
        'distance_covered' => 'decimal:2',
        'sprint_distance' => 'decimal:2',
        'max_speed' => 'decimal:1',
        'avg_speed' => 'decimal:1',
        'expected_goals' => 'decimal:2',
        'expected_assists' => 'decimal:2',
        'pass_accuracy' => 'decimal:2',
        'shot_accuracy' => 'decimal:2',
        'tackle_success_rate' => 'decimal:2',
        'duel_success_rate' => 'decimal:2',
        'overall_rating' => 'decimal:1',
        'attacking_rating' => 'decimal:1',
        'defending_rating' => 'decimal:1',
        'physical_rating' => 'decimal:1',
        'technical_rating' => 'decimal:1',
        'mental_rating' => 'decimal:1',
    ];

    /**
     * Relation avec le joueur
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Relation avec la saison
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Relation avec la compétition
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Calculer la précision des tirs
     */
    public function getShotAccuracyAttribute($value)
    {
        if ($this->shots_total > 0) {
            return round(($this->shots_on_target / $this->shots_total) * 100, 1);
        }
        return $value ?? 0;
    }

    /**
     * Calculer la précision des passes
     */
    public function getPassAccuracyAttribute($value)
    {
        if ($this->passes_total > 0) {
            return round(($this->passes_completed / $this->passes_total) * 100, 1);
        }
        return $value ?? 0;
    }

    /**
     * Calculer le taux de réussite des tacles
     */
    public function getTackleSuccessRateAttribute($value)
    {
        if ($this->tackles_total > 0) {
            return round(($this->tackles_won / $this->tackles_total) * 100, 1);
        }
        return $value ?? 0;
    }

    /**
     * Calculer le taux de réussite des duels
     */
    public function getDuelSuccessRateAttribute($value)
    {
        if ($this->duels_total > 0) {
            return round(($this->duels_won / $this->duels_total) * 100, 1);
        }
        return $value ?? 0;
    }

    /**
     * Calculer la distance moyenne par match
     */
    public function getDistancePerMatchAttribute()
    {
        if ($this->matches_played > 0) {
            return round($this->distance_covered / $this->matches_played, 2);
        }
        return 0;
    }

    /**
     * Calculer les buts par match
     */
    public function getGoalsPerMatchAttribute()
    {
        if ($this->matches_played > 0) {
            return round($this->goals_scored / $this->matches_played, 2);
        }
        return 0;
    }

    /**
     * Calculer les passes décisives par match
     */
    public function getAssistsPerMatchAttribute()
    {
        if ($this->matches_played > 0) {
            return round($this->assists / $this->matches_played, 2);
        }
        return 0;
    }

    /**
     * Calculer le score de forme (basé sur les performances récentes)
     */
    public function getFormScoreAttribute()
    {
        $baseScore = $this->overall_rating * 10;
        
        // Bonus pour les performances exceptionnelles
        if ($this->goals_scored > 0) $baseScore += 5;
        if ($this->assists > 0) $baseScore += 3;
        if ($this->pass_accuracy > 80) $baseScore += 2;
        if ($this->shot_accuracy > 70) $baseScore += 2;
        
        return min(100, max(0, round($baseScore)));
    }

    /**
     * Obtenir le statut de forme
     */
    public function getFormStatusAttribute()
    {
        $score = $this->form_score;
        
        if ($score >= 85) return 'Excellent';
        if ($score >= 75) return 'Très bon';
        if ($score >= 65) return 'Bon';
        if ($score >= 55) return 'Moyen';
        return 'À améliorer';
    }

    /**
     * Obtenir la couleur du statut de forme
     */
    public function getFormStatusColorAttribute()
    {
        $score = $this->form_score;
        
        if ($score >= 85) return 'text-green-400';
        if ($score >= 75) return 'text-blue-400';
        if ($score >= 65) return 'text-yellow-400';
        if ($score >= 55) return 'text-orange-400';
        return 'text-red-400';
    }
} 