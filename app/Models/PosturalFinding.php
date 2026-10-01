<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosturalFinding extends Model
{
    protected $fillable = [
        'postural_assessment_id', 'view', 'region', 'finding_key', 'side',
        'severity', 'value', 'unit', 'source', 'confidence', 'source_metadata', 'notes',
    ];

    protected $casts = [
        'value' => 'float',
        'confidence' => 'float',
        'source_metadata' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(PosturalAssessment::class, 'postural_assessment_id');
    }
}
