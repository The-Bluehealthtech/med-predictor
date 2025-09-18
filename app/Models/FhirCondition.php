<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle FHIR Condition pour le workflow clinique FIT
 * Représente les conditions médicales, diagnostics, symptômes
 */
class FhirCondition extends Model
{
    protected $table = 'fhir_conditions';
    
    protected $fillable = [
        'patient_id',
        'resource_type',
        'identifier',
        'clinical_status',
        'verification_status',
        'category',
        'severity',
        'code',
        'body_site',
        'subject',
        'encounter',
        'onset_date_time',
        'onset_age',
        'onset_period',
        'onset_range',
        'onset_string',
        'abatement_date_time',
        'abatement_age',
        'abatement_period',
        'abatement_range',
        'abatement_string',
        'recorded_date',
        'recorder',
        'asserter',
        'stage',
        'evidence',
        'note',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'identifier' => 'array',
        'category' => 'array',
        'severity' => 'array',
        'code' => 'array',
        'body_site' => 'array',
        'subject' => 'array',
        'encounter' => 'array',
        'onset_age' => 'array',
        'onset_period' => 'array',
        'onset_range' => 'array',
        'abatement_age' => 'array',
        'abatement_period' => 'array',
        'abatement_range' => 'array',
        'recorder' => 'array',
        'asserter' => 'array',
        'stage' => 'array',
        'evidence' => 'array',
        'note' => 'array',
        'onset_date_time' => 'datetime',
        'abatement_date_time' => 'datetime',
        'recorded_date' => 'datetime'
    ];

    protected $dates = [
        'onset_date_time',
        'abatement_date_time',
        'recorded_date',
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
     * Relation avec les observations liées
     */
    public function observations(): HasMany
    {
        return $this->hasMany(FhirObservation::class, 'condition_id');
    }

    /**
     * Relation avec l'utilisateur créateur
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur modificateur
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Accessor pour le nom de la condition
     */
    public function getConditionNameAttribute(): string
    {
        if (empty($this->code) || !is_array($this->code)) {
            return 'Condition non spécifiée';
        }

        $coding = $this->code['coding'] ?? [];
        if (empty($coding) || !is_array($coding)) {
            return 'Condition non spécifiée';
        }

        $display = $coding[0]['display'] ?? '';
        return $display ?: 'Condition non spécifiée';
    }

    /**
     * Accessor pour le statut clinique lisible
     */
    public function getClinicalStatusTextAttribute(): string
    {
        $statusMap = [
            'active' => 'Actif',
            'recurrence' => 'Récidive',
            'relapse' => 'Rechute',
            'inactive' => 'Inactif',
            'remission' => 'Rémission',
            'resolved' => 'Résolu'
        ];

        return $statusMap[$this->clinical_status] ?? $this->clinical_status;
    }

    /**
     * Scope pour les conditions actives
     */
    public function scopeActive($query)
    {
        return $query->where('clinical_status', 'active');
    }

    /**
     * Scope pour les conditions par catégorie
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->whereJsonContains('category', ['coding' => [['code' => $category]]]);
    }

    /**
     * Scope pour les conditions par code ICD-10
     */
    public function scopeByIcd10Code($query, string $code)
    {
        return $query->whereJsonContains('code', ['coding' => [['system' => 'http://hl7.org/fhir/sid/icd-10', 'code' => $code]]]);
    }

    /**
     * Méthode pour valider les données FHIR
     */
    public function validateFhirData(): array
    {
        $errors = [];

        if (empty($this->patient_id)) {
            $errors[] = 'L\'ID du patient est requis';
        }

        if (empty($this->clinical_status)) {
            $errors[] = 'Le statut clinique est requis';
        }

        if (empty($this->code) || !is_array($this->code)) {
            $errors[] = 'Le code de la condition est requis';
        }

        return $errors;
    }

    /**
     * Méthode pour convertir en format FHIR JSON
     */
    public function toFhirJson(): array
    {
        return [
            'resourceType' => 'Condition',
            'id' => $this->id,
            'identifier' => $this->identifier,
            'clinicalStatus' => $this->clinical_status,
            'verificationStatus' => $this->verification_status,
            'category' => $this->category,
            'severity' => $this->severity,
            'code' => $this->code,
            'bodySite' => $this->body_site,
            'subject' => $this->subject,
            'encounter' => $this->encounter,
            'onsetDateTime' => $this->onset_date_time?->format('c'),
            'onsetAge' => $this->onset_age,
            'onsetPeriod' => $this->onset_period,
            'onsetRange' => $this->onset_range,
            'onsetString' => $this->onset_string,
            'abatementDateTime' => $this->abatement_date_time?->format('c'),
            'abatementAge' => $this->abatement_age,
            'abatementPeriod' => $this->abatement_period,
            'abatementRange' => $this->abatement_range,
            'abatementString' => $this->abatement_string,
            'recordedDate' => $this->recorded_date?->format('c'),
            'recorder' => $this->recorder,
            'asserter' => $this->asserter,
            'stage' => $this->stage,
            'evidence' => $this->evidence,
            'note' => $this->note
        ];
    }

    /**
     * Méthode pour créer une condition à partir de symptômes
     */
    public static function createFromSymptoms(array $symptoms, string $patientId, string $userId): self
    {
        $condition = new self();
        $condition->patient_id = $patientId;
        $condition->resource_type = 'Condition';
        $condition->clinical_status = 'active';
        $condition->verification_status = 'provisional';
        $condition->category = [
            'coding' => [
                [
                    'system' => 'http://terminology.hl7.org/CodeSystem/condition-category',
                    'code' => 'symptom',
                    'display' => 'Symptom'
                ]
            ]
        ];
        $condition->code = [
            'coding' => [
                [
                    'system' => 'http://snomed.info/sct',
                    'code' => '22253000',
                    'display' => 'Pain'
                ]
            ],
            'text' => implode(', ', $symptoms)
        ];
        $condition->onset_date_time = now();
        $condition->recorded_date = now();
        $condition->created_by = $userId;
        $condition->updated_by = $userId;

        return $condition;
    }
}
