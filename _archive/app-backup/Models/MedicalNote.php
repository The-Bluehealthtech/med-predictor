<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalNote extends Model
{
    use HasFactory;

    protected $table = 'medical_notes';

    protected $fillable = [
        'athlete_id',
        'note_json',
        'generated_by_ai',
        'approved_by_physician_id',
        'signed_at',
        'status',
        'note_type',
        'ai_metadata',
        'fifa_compliance_data',
    ];

    protected $casts = [
        'note_json' => 'array',
        'ai_metadata' => 'array',
        'fifa_compliance_data' => 'array',
        'generated_by_ai' => 'boolean',
        'signed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the athlete that this note belongs to.
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the physician who approved this note.
     */
    public function approvedByPhysician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_physician_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by note type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('note_type', $type);
    }

    /**
     * Scope to filter AI-generated notes.
     */
    public function scopeAIGenerated($query)
    {
        return $query->where('generated_by_ai', true);
    }

    /**
     * Scope to filter manually created notes.
     */
    public function scopeManuallyCreated($query)
    {
        return $query->where('generated_by_ai', false);
    }

    /**
     * Scope to filter approved notes.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to filter pending review notes.
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    /**
     * Check if note is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if note is pending review.
     */
    public function isPendingReview(): bool
    {
        return $this->status === 'pending_review';
    }

    /**
     * Check if note is signed.
     */
    public function isSigned(): bool
    {
        return $this->signed_at !== null;
    }

    /**
     * Get note content from JSON.
     */
    public function getNoteContent(): array
    {
        return $this->note_json ?? [];
    }

    /**
     * Get AI metadata.
     */
    public function getAIMetadata(): array
    {
        return $this->ai_metadata ?? [];
    }

    /**
     * Get note summary.
     */
    public function getNoteSummary(): array
    {
        return [
            'id' => $this->id,
            'athlete_name' => $this->athlete->name,
            'note_type' => $this->note_type,
            'status' => $this->status,
            'generated_by_ai' => $this->generated_by_ai,
            'approved_by' => $this->approvedByPhysician?->name,
            'signed_at' => $this->signed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get note content for display.
     */
    public function getDisplayContent(): string
    {
        $content = $this->getNoteContent();
        
        if (isset($content['text'])) {
            return $content['text'];
        }
        
        if (isset($content['content'])) {
            return $content['content'];
        }
        
        return json_encode($content, JSON_PRETTY_PRINT);
    }

    /**
     * Check if note is FIFA compliant.
     */
    public function isFifaCompliant(): bool
    {
        return $this->status === 'approved' && 
               $this->signed_at !== null;
    }
} 