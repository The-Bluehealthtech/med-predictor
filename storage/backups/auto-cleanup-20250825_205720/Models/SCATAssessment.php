<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SCATAssessment extends Model
{
    use HasFactory;

    protected $table = 'scat_assessments';

    protected $fillable = [
        'athlete_id',
        'assessor_id',
        'data_json',
        'result',
        'concussion_confirmed',
        'assessment_date',
        'assessment_type',
        'scat_score',
        'recommendations',
        'fifa_concussion_data',
    ];

    protected $casts = [
        'data_json' => 'array',
        'fifa_concussion_data' => 'array',
        'concussion_confirmed' => 'boolean',
        'assessment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the athlete that this assessment belongs to.
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the assessor who conducted this assessment.
     */
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    /**
     * Scope to filter by result.
     */
    public function scopeByResult($query, $result)
    {
        return $query->where('result', $result);
    }

    /**
     * Scope to filter by assessment type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('assessment_type', $type);
    }

    /**
     * Scope to filter confirmed concussions.
     */
    public function scopeConfirmedConcussions($query)
    {
        return $query->where('concussion_confirmed', true);
    }

    /**
     * Scope to filter baseline assessments.
     */
    public function scopeBaseline($query)
    {
        return $query->where('assessment_type', 'baseline');
    }

    /**
     * Scope to filter post-injury assessments.
     */
    public function scopePostInjury($query)
    {
        return $query->where('assessment_type', 'post_injury');
    }

    /**
     * Get assessment summary.
     */
    public function getAssessmentSummary(): array
    {
        return [
            'id' => $this->id,
            'athlete_name' => $this->athlete->name,
            'assessor_name' => $this->assessor->name,
            'assessment_date' => $this->assessment_date,
            'assessment_type' => $this->assessment_type,
            'result' => $this->result,
            'concussion_confirmed' => $this->concussion_confirmed,
            'scat_score' => $this->scat_score,
            'recommendations' => $this->recommendations,
        ];
    }

    /**
     * Check if this is a baseline assessment.
     */
    public function isBaseline(): bool
    {
        return $this->assessment_type === 'baseline';
    }

    /**
     * Check if this is a post-injury assessment.
     */
    public function isPostInjury(): bool
    {
        return $this->assessment_type === 'post_injury';
    }

    /**
     * Get the baseline assessment for this athlete.
     */
    public function getBaselineAssessment()
    {
        return static::where('athlete_id', $this->athlete_id)
            ->where('assessment_type', 'baseline')
            ->latest()
            ->first();
    }

    /**
     * Compare with baseline assessment.
     */
    public function compareWithBaseline(): array
    {
        $baseline = $this->getBaselineAssessment();
        
        if (!$baseline || $this->isBaseline()) {
            return [
                'has_baseline' => false,
                'comparison' => null,
            ];
        }

        $baselineScore = $baseline->scat_score ?? 0;
        $currentScore = $this->scat_score ?? 0;
        $difference = $currentScore - $baselineScore;

        return [
            'has_baseline' => true,
            'baseline_score' => $baselineScore,
            'current_score' => $currentScore,
            'difference' => $difference,
            'improvement' => $difference > 0,
            'significant_change' => abs($difference) > 5, // Threshold for significant change
        ];
    }
} 