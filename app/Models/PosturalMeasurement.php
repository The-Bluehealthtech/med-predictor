<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosturalMeasurement extends Model
{
    protected $fillable = [
        'postural_assessment_id', 'view', 'measurement_type', 'measurement_key',
        'anatomical_region', 'side', 'value', 'unit', 'points', 'metadata',
    ];

    protected $casts = [
        'value' => 'float',
        'points' => 'array',
        'metadata' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(PosturalAssessment::class, 'postural_assessment_id');
    }
}
