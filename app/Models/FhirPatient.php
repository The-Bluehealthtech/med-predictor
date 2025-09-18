<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle FHIR Patient pour le workflow clinique FIT
 * Compatible avec la base MySQL existante
 */
class FhirPatient extends Model
{
    protected $table = 'fhir_patients';
    
    protected $fillable = [
        'fit_patient_id',
        'resource_type',
        'identifier',
        'active',
        'name',
        'telecom',
        'gender',
        'birth_date',
        'address',
        'marital_status',
        'multiple_birth',
        'photo',
        'contact',
        'communication',
        'general_practitioner',
        'managing_organization',
        'link',
        'preferred_language',
        'emergency_contact',
        'insurance_info',
        'medical_history',
        'allergies',
        'medications',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'identifier' => 'array',
        'name' => 'array',
        'telecom' => 'array',
        'address' => 'array',
        'marital_status' => 'array',
        'multiple_birth' => 'array',
        'photo' => 'array',
        'contact' => 'array',
        'communication' => 'array',
        'general_practitioner' => 'array',
        'managing_organization' => 'array',
        'link' => 'array',
        'emergency_contact' => 'array',
        'insurance_info' => 'array',
        'medical_history' => 'array',
        'allergies' => 'array',
        'medications' => 'array',
        'active' => 'boolean',
        'birth_date' => 'date'
    ];

    protected $dates = [
        'birth_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Relation avec les conditions médicales
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(FhirCondition::class, 'patient_id');
    }

    /**
     * Relation avec les plans de soins
     */
    public function carePlans(): HasMany
    {
        return $this->hasMany(FhirCarePlan::class, 'patient_id');
    }

    /**
     * Relation avec les observations
     */
    public function observations(): HasMany
    {
        return $this->hasMany(FhirObservation::class, 'patient_id');
    }

    /**
     * Relation avec les consultations
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(ClinicalConsultation::class, 'patient_id');
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
     * Accessor pour le nom complet
     */
    public function getFullNameAttribute(): string
    {
        if (empty($this->name) || !is_array($this->name)) {
            return 'Nom non disponible';
        }

        $name = $this->name[0] ?? [];
        $family = $name['family'] ?? '';
        $given = is_array($name['given'] ?? []) ? implode(' ', $name['given']) : '';

        return trim($family . ' ' . $given);
    }

    /**
     * Accessor pour l'âge
     */
    public function getAgeAttribute(): int
    {
        if (!$this->birth_date) {
            return 0;
        }

        return $this->birth_date->age;
    }

    /**
     * Scope pour les patients actifs
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope pour rechercher par nom
     */
    public function scopeByName($query, string $name)
    {
        return $query->whereJsonContains('name', ['family' => $name])
                    ->orWhereJsonContains('name', ['given' => $name]);
    }

    /**
     * Méthode pour générer un identifiant FIT unique
     */
    public static function generateFitPatientId(): string
    {
        return 'FIT-PAT-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    /**
     * Méthode pour valider les données FHIR
     */
    public function validateFhirData(): array
    {
        $errors = [];

        if (empty($this->name) || !is_array($this->name)) {
            $errors[] = 'Le nom du patient est requis';
        }

        if (empty($this->gender)) {
            $errors[] = 'Le genre du patient est requis';
        }

        if (empty($this->birth_date)) {
            $errors[] = 'La date de naissance est requise';
        }

        return $errors;
    }

    /**
     * Méthode pour convertir en format FHIR JSON
     */
    public function toFhirJson(): array
    {
        return [
            'resourceType' => 'Patient',
            'id' => $this->id,
            'identifier' => $this->identifier,
            'active' => $this->active,
            'name' => $this->name,
            'telecom' => $this->telecom,
            'gender' => $this->gender,
            'birthDate' => $this->birth_date?->format('Y-m-d'),
            'address' => $this->address,
            'maritalStatus' => $this->marital_status,
            'multipleBirth' => $this->multiple_birth,
            'photo' => $this->photo,
            'contact' => $this->contact,
            'communication' => $this->communication,
            'generalPractitioner' => $this->general_practitioner,
            'managingOrganization' => $this->managing_organization,
            'link' => $this->link
        ];
    }
}
