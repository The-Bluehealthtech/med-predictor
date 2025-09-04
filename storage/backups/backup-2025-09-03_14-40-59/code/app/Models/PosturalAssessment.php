<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosturalAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'user_id',
        'assessment_type',
        'view',
        'annotations',
        'markers',
        'angles',
        'clinical_notes',
        'recommendations',
        'status',
        'assessment_date',
    ];

    protected $casts = [
        'annotations' => 'array',
        'markers' => 'array',
        'angles' => 'array',
        'assessment_date' => 'datetime',
    ];

    /**
     * Get the player that owns the assessment.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the clinician who performed the assessment.
     */
    public function clinician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for active assessments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for assessments by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('assessment_type', $type);
    }

    /**
     * Scope for assessments by view.
     */
    public function scopeByView($query, $view)
    {
        return $query->where('view', $view);
    }

    /**
     * Get the assessment summary.
     */
    public function getSummaryAttribute(): array
    {
        $markers = $this->markers ?? [];
        $angles = $this->angles ?? [];
        $annotations = $this->annotations ?? [];

        return [
            'total_markers' => count($markers),
            'total_angles' => count($angles),
            'total_annotations' => count($annotations),
            'has_clinical_notes' => !empty($this->clinical_notes),
            'has_recommendations' => !empty($this->recommendations),
        ];
    }

    /**
     * Get the assessment data for the Vue component.
     */
    public function getSessionDataAttribute(): array
    {
        return [
            'view' => $this->view,
            'annotations' => $this->annotations ?? [],
            'markers' => $this->markers ?? [],
            'angles' => $this->angles ?? [],
        ];
    }

    /**
     * Set the session data from the Vue component.
     */
    public function setSessionData(array $data): void
    {
        $this->update([
            'view' => $data['view'] ?? $this->view,
            'annotations' => $data['annotations'] ?? [],
            'markers' => $data['markers'] ?? [],
            'angles' => $data['angles'] ?? [],
        ]);
    }

    /**
     * Get the formatted assessment date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->assessment_date->format('d/m/Y H:i');
    }

    /**
     * Get the assessment type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->assessment_type) {
            'routine' => 'Routine',
            'injury' => 'Blessure',
            'follow_up' => 'Suivi',
            default => 'Autre'
        };
    }

    /**
     * Get the view label.
     */
    public function getViewLabelAttribute(): string
    {
        return match($this->view) {
            'anterior' => 'Antérieure',
            'posterior' => 'Postérieure',
            'lateral' => 'Latérale',
            default => 'Inconnue'
        };
    }
} 