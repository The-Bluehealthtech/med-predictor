<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Athlete;
use App\Models\Player;
use App\Models\Visit;

class PCMA extends Model
{
    use HasFactory;

    protected $table = 'pcmas';

    protected $fillable = [
        'athlete_id',
        'player_id', // Link to Player model
        'visit_id', // Link to Visit model
        'fifa_connect_id', // Link to FIFA Connect ID
        'type',
        'result_json',
        'medical_history',
        'physical_examination',
        'cardiovascular_investigations',
        'final_statement',
        'scat_assessment',
        'status',
        'completed_at',
        'assessor_id',
        'assessment_date',
        'notes',
        'fifa_compliance_data',
        'fifa_id',
        'competition_name',
        'competition_date',
        'team_name',
        'position',
        'fifa_compliant',
        'fifa_approved_at',
        'fifa_approved_by',
        'anatomical_annotations',
        'attachments',
        'form_version',
        'last_updated_at',
        // Medical Imaging Files
        'ecg_file',
        'ecg_date',
        'ecg_interpretation',
        'ecg_notes',
        'mri_file',
        'mri_date',
        'mri_type',
        'mri_findings',
        'mri_notes',
        'xray_file',
        'ct_scan_file',
        'ultrasound_file',
        // Signature fields
        'is_signed',
        'signed_at',
        'signed_by',
        'license_number',
        'signature_image',
        'signature_data',
    ];

    protected $casts = [
        'result_json' => 'array',
        'medical_history' => 'array',
        'physical_examination' => 'array',
        'cardiovascular_investigations' => 'array',
        'final_statement' => 'array',
        'scat_assessment' => 'array',
        'fifa_compliance_data' => 'array',
        'anatomical_annotations' => 'array',
        'attachments' => 'array',
        'completed_at' => 'datetime',
        'assessment_date' => 'date',
        'ecg_date' => 'date',
        'mri_date' => 'date',
        'fifa_approved_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'fifa_compliant' => 'boolean',
        'is_signed' => 'boolean',
        'signed_at' => 'datetime',
        'signature_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the athlete that this PCMA belongs to.
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the player that this PCMA belongs to.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the FIFA Connect ID that this PCMA belongs to.
     */
    public function fifaConnectId(): BelongsTo
    {
        return $this->belongsTo(FifaConnectId::class, 'fifa_connect_id');
    }

    /**
     * Get the assessor who conducted this PCMA.
     */
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    /**
     * Get the visit that this PCMA belongs to.
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter completed PCMAs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to filter pending PCMAs.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter FIFA compliant PCMAs.
     */
    public function scopeFifaCompliant($query)
    {
        return $query->where('fifa_compliant', true);
    }

    /**
     * Check if PCMA is FIFA compliant.
     */
    public function isFifaCompliant(): bool
    {
        return $this->fifa_compliant && 
               $this->status === 'completed' && 
               $this->completed_at && 
               $this->completed_at->isAfter(now()->subYear());
    }

    /**
     * Get PCMA result summary.
     */
    public function getResultSummary(): array
    {
        return [
            'type' => $this->type,
            'status' => $this->status,
            'completed_at' => $this->completed_at,
            'assessor' => $this->assessor?->name,
            'fifa_compliant' => $this->isFifaCompliant(),
            'medical_history' => $this->medical_history ?? [],
            'physical_examination' => $this->physical_examination ?? [],
            'cardiovascular_investigations' => $this->cardiovascular_investigations ?? [],
            'final_statement' => $this->final_statement ?? [],
            'scat_assessment' => $this->scat_assessment ?? [],
            'anatomical_annotations' => $this->anatomical_annotations ?? [],
        ];
    }

    /**
     * Get anatomical annotations for a specific view (anterior/posterior).
     */
    public function getAnatomicalAnnotations(string $view = 'anterior'): array
    {
        $annotations = $this->anatomical_annotations ?? [];
        return $annotations[$view] ?? [];
    }

    /**
     * Add anatomical annotation.
     */
    public function addAnatomicalAnnotation(string $view, int $x, int $y, string $note): void
    {
        $annotations = $this->anatomical_annotations ?? [];
        if (!isset($annotations[$view])) {
            $annotations[$view] = [];
        }
        
        $annotations[$view][] = [
            'id' => uniqid(),
            'x' => $x,
            'y' => $y,
            'note' => $note,
            'created_at' => now()->toISOString(),
        ];
        
        $this->update(['anatomical_annotations' => $annotations]);
    }

    /**
     * Remove anatomical annotation.
     */
    public function removeAnatomicalAnnotation(string $view, string $annotationId): void
    {
        $annotations = $this->anatomical_annotations ?? [];
        if (isset($annotations[$view])) {
            $annotations[$view] = array_filter(
                $annotations[$view], 
                fn($annotation) => $annotation['id'] !== $annotationId
            );
            $this->update(['anatomical_annotations' => $annotations]);
        }
    }

    /**
     * Get FIFA compliance status.
     */
    public function getFifaComplianceStatus(): array
    {
        return [
            'is_compliant' => $this->isFifaCompliant(),
            'fifa_id' => $this->fifa_id,
            'competition_name' => $this->competition_name,
            'competition_date' => $this->competition_date,
            'team_name' => $this->team_name,
            'position' => $this->position,
            'approved_at' => $this->fifa_approved_at,
            'approved_by' => $this->fifa_approved_by,
            'form_version' => $this->form_version,
        ];
    }

    /**
     * Mark as FIFA compliant.
     */
    public function markAsFifaCompliant(?string $approvedBy = null): void
    {
        $this->update([
            'fifa_compliant' => true,
            'fifa_approved_at' => now(),
            'fifa_approved_by' => $approvedBy,
        ]);
    }

    /**
     * Get SCAT assessment data.
     */
    public function getScatAssessment(): array
    {
        return $this->scat_assessment ?? [];
    }

    /**
     * Update SCAT assessment.
     */
    public function updateScatAssessment(array $scatData): void
    {
        $this->update(['scat_assessment' => $scatData]);
    }

    /**
     * Get medical history data.
     */
    public function getMedicalHistory(): array
    {
        return $this->medical_history ?? [];
    }

    /**
     * Update medical history.
     */
    public function updateMedicalHistory(array $historyData): void
    {
        $this->update(['medical_history' => $historyData]);
    }

    /**
     * Get physical examination data.
     */
    public function getPhysicalExamination(): array
    {
        return $this->physical_examination ?? [];
    }

    /**
     * Update physical examination.
     */
    public function updatePhysicalExamination(array $examData): void
    {
        $this->update(['physical_examination' => $examData]);
    }

    /**
     * Get cardiovascular investigations data.
     */
    public function getCardiovascularInvestigations(): array
    {
        return $this->cardiovascular_investigations ?? [];
    }

    /**
     * Update cardiovascular investigations.
     */
    public function updateCardiovascularInvestigations(array $cardioData): void
    {
        $this->update(['cardiovascular_investigations' => $cardioData]);
    }

    /**
     * Get final statement data.
     */
    public function getFinalStatement(): array
    {
        return $this->final_statement ?? [];
    }

    /**
     * Update final statement.
     */
    public function updateFinalStatement(array $statementData): void
    {
        $this->update(['final_statement' => $statementData]);
    }
} 