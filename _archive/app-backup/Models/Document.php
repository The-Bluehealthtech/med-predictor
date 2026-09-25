<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'description',
        'analysis_result',
        'uploaded_by',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'analysis_result' => 'array',
    ];

    /**
     * Get the visit that owns the document.
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the document type label.
     */
    public function getDocumentTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            'medical_report' => 'Rapport médical',
            'lab_result' => 'Résultat de laboratoire',
            'radiology' => 'Imagerie médicale',
            'prescription' => 'Ordonnance',
            'consent_form' => 'Formulaire de consentement',
            'insurance_form' => 'Formulaire d\'assurance',
            'referral' => 'Demande de consultation',
            'discharge_summary' => 'Résumé de sortie',
            'progress_note' => 'Note de progression',
            'other' => 'Autre',
            default => $this->document_type,
        };
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'analyzing' => 'En cours d\'analyse',
            'analyzed' => 'Analysé',
            'error' => 'Erreur',
            'archived' => 'Archivé',
            default => $this->status,
        };
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if the document has been analyzed.
     */
    public function isAnalyzed(): bool
    {
        return $this->status === 'analyzed' && !empty($this->analysis_result);
    }

    /**
     * Check if the document can be analyzed.
     */
    public function canBeAnalyzed(): bool
    {
        return in_array($this->mime_type, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'text/plain',
        ]);
    }
} 