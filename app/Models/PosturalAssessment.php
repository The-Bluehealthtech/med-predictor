<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosturalAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'user_id',
        'health_record_id',
        'assessment_type',
        'view',
        'annotations',
        'markers',
        'angles',
        'overall_impression',
        'clinical_notes',
        'recommendations',
        'context',
        'status',
        'assessment_date',
        'validated_at',
        'validated_by',
    ];

    protected $casts = [
        'annotations' => 'array',
        'markers' => 'array',
        'angles' => 'array',
        'context' => 'array',
        'assessment_date' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function clinician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function healthRecord(): BelongsTo
    {
        return $this->belongsTo(HealthRecord::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(PosturalFinding::class);
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(PosturalMeasurement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'archived');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('assessment_type', $type);
    }

    public function scopeByView($query, $view)
    {
        return $query->where('view', $view);
    }

    public function getSummaryAttribute(): array
    {
        return [
            'total_findings' => $this->relationLoaded('findings') ? $this->findings->count() : 0,
            'total_measurements' => $this->relationLoaded('measurements') ? $this->measurements->count() : 0,
            'total_markers' => count($this->markers ?? []),
            'total_angles' => count($this->angles ?? []),
            'total_annotations' => count($this->annotations ?? []),
            'has_clinical_notes' => !empty($this->clinical_notes),
            'has_recommendations' => !empty($this->recommendations),
        ];
    }

    public function getSessionDataAttribute(): array
    {
        return [
            'view' => $this->view,
            'annotations' => $this->annotations ?? [],
            'markers' => $this->markers ?? [],
            'angles' => $this->angles ?? [],
        ];
    }

    public function setSessionData(array $data): void
    {
        $this->update([
            'view' => $data['view'] ?? $this->view,
            'annotations' => $data['annotations'] ?? [],
            'markers' => $data['markers'] ?? [],
            'angles' => $data['angles'] ?? [],
        ]);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->assessment_date->format('d/m/Y H:i');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->assessment_type) {
            'baseline' => 'Baseline',
            'routine' => 'Routine',
            'injury' => 'Blessure',
            'follow_up' => 'Suivi',
            'return_to_play' => 'Retour au jeu',
            default => 'Autre',
        };
    }

    public function getViewLabelAttribute(): string
    {
        return match($this->view) {
            'anterior' => 'Antérieure',
            'posterior' => 'Postérieure',
            'lateral' => 'Latérale',
            'left_lateral' => 'Latérale gauche',
            'right_lateral' => 'Latérale droite',
            default => 'Inconnue',
        };
    }
}
