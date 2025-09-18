<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle pour les consultations cliniques dans le workflow FIT
 */
class ClinicalConsultation extends Model
{
    protected $table = 'clinical_consultations';
    
    protected $fillable = [
        'patient_id',
        'clinician_id',
        'consultation_type',
        'status',
        'chief_complaint',
        'history_present_illness',
        'physical_exam',
        'assessment',
        'plan',
        'ai_summary',
        'ai_insights',
        'ai_recommendations',
        'vital_signs',
        'medications',
        'allergies',
        'diagnosis_codes',
        'notes',
        'scheduled_at',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'ai_insights' => 'array',
        'ai_recommendations' => 'array',
        'vital_signs' => 'array',
        'medications' => 'array',
        'allergies' => 'array',
        'diagnosis_codes' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    protected $dates = [
        'scheduled_at',
        'started_at',
        'completed_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Relation avec le patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(FhirPatient::class, 'patient_id');
    }

    /**
     * Relation avec le clinicien
     */
    public function clinician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'clinician_id');
    }

    /**
     * Relation avec les conditions créées lors de la consultation
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(FhirCondition::class, 'encounter_id');
    }

    /**
     * Relation avec les plans de soins créés
     */
    public function carePlans(): HasMany
    {
        return $this->hasMany(FhirCarePlan::class, 'encounter_id');
    }

    /**
     * Accessor pour la durée de la consultation
     */
    public function getDurationAttribute(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        return null;
    }

    /**
     * Accessor pour le statut lisible
     */
    public function getStatusTextAttribute(): string
    {
        $statusMap = [
            'scheduled' => 'Programmée',
            'in_progress' => 'En cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée'
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    /**
     * Accessor pour le type de consultation lisible
     */
    public function getConsultationTypeTextAttribute(): string
    {
        $typeMap = [
            'initial' => 'Consultation initiale',
            'follow_up' => 'Consultation de suivi',
            'emergency' => 'Consultation d\'urgence'
        ];

        return $typeMap[$this->consultation_type] ?? $this->consultation_type;
    }

    /**
     * Scope pour les consultations programmées
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope pour les consultations en cours
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope pour les consultations terminées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les consultations d'un clinicien
     */
    public function scopeByClinician($query, $clinicianId)
    {
        return $query->where('clinician_id', $clinicianId);
    }

    /**
     * Scope pour les consultations d'un patient
     */
    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope pour les consultations d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    /**
     * Méthode pour démarrer une consultation
     */
    public function start(): bool
    {
        if ($this->status !== 'scheduled') {
            return false;
        }

        $this->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        return true;
    }

    /**
     * Méthode pour terminer une consultation
     */
    public function complete(): bool
    {
        if ($this->status !== 'in_progress') {
            return false;
        }

        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return true;
    }

    /**
     * Méthode pour annuler une consultation
     */
    public function cancel(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }

        $this->update([
            'status' => 'cancelled'
        ]);

        return true;
    }

    /**
     * Méthode pour générer un résumé automatique
     */
    public function generateSummary(): string
    {
        $summary = "Résumé de la consultation du " . $this->created_at->format('d/m/Y à H:i') . "\n\n";
        
        if ($this->chief_complaint) {
            $summary .= "Motif de consultation: " . $this->chief_complaint . "\n\n";
        }
        
        if ($this->history_present_illness) {
            $summary .= "Histoire de la maladie: " . substr($this->history_present_illness, 0, 200) . "...\n\n";
        }
        
        if ($this->assessment) {
            $summary .= "Évaluation: " . $this->assessment . "\n\n";
        }
        
        if ($this->plan) {
            $summary .= "Plan de traitement: " . $this->plan . "\n\n";
        }

        if ($this->vital_signs) {
            $summary .= "Signes vitaux: " . json_encode($this->vital_signs) . "\n\n";
        }

        return $summary;
    }

    /**
     * Méthode pour valider les données de consultation
     */
    public function validateConsultationData(): array
    {
        $errors = [];

        if (empty($this->patient_id)) {
            $errors[] = 'L\'ID du patient est requis';
        }

        if (empty($this->clinician_id)) {
            $errors[] = 'L\'ID du clinicien est requis';
        }

        if (empty($this->chief_complaint)) {
            $errors[] = 'Le motif de consultation est requis';
        }

        if (empty($this->history_present_illness)) {
            $errors[] = 'L\'histoire de la maladie actuelle est requise';
        }

        return $errors;
    }

    /**
     * Méthode pour obtenir les statistiques de consultation
     */
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'scheduled' => self::scheduled()->count(),
            'in_progress' => self::inProgress()->count(),
            'completed' => self::completed()->count(),
            'cancelled' => self::where('status', 'cancelled')->count(),
            'today' => self::today()->count(),
            'this_week' => self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => self::whereMonth('created_at', now()->month)->count()
        ];
    }
}
