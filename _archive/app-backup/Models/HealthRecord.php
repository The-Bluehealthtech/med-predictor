<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'player_id',
        'visit_id',
        // EMR Visit Information
        'visit_date',
        'doctor_name',
        'visit_type',
        'chief_complaint',
        'physical_examination',
        'laboratory_results',
        'imaging_results',
        'prescriptions',
        'follow_up_instructions',
        'visit_notes',
        // Basic Health Data
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'temperature',
        'weight',
        'height',
        'bmi',
        'blood_type',
        'allergies',
        'medications',
        'medical_history',
        'symptoms',
        'diagnosis',
        'treatment_plan',
        'risk_score',
        'prediction_confidence',
        'record_date',
        'next_checkup_date',
        'status',
        // Illness and Heart Diseases
        'illness_records',
        'heart_disease_records',
        'icd_10_primary_diagnosis',
        'icd_10_secondary_diagnosis',
        'snomed_ct_condition',
        // Doping Control
        'doping_tests',
        'doping_test_status',
        'last_doping_test_date',
        'next_doping_test_date',
        'doping_test_lab',
        'loinc_doping_panel',
        // AUT - Therapeutic Use Authorization
        'aut_records',
        'aut_status',
        'aut_approval_date',
        'aut_expiry_date',
        'aut_authorized_substance',
        'aut_authorizing_physician',
        'aut_medical_justification',
        'snomed_ct_aut',
        // Enhanced AUT fields
        'aut_substance',
        'aut_dosage',
        'aut_diagnosis',
        'aut_justification',
        'aut_start_date',
        'aut_end_date',
        'aut_application_form_path',
        'aut_response_document_path',
        'aut_notes',
        // Blood Tests
        'blood_test_results',
        'blood_test_panel',
        'last_blood_test_date',
        'next_blood_test_date',
        'hematology_results',
        'biochemistry_results',
        'hormone_results',
        'vitamin_results',
        'mineral_results',
        // Biological Profile
        'biological_profile',
        'biological_age',
        'metabolic_markers',
        'inflammatory_markers',
        'oxidative_stress_markers',
        'snomed_ct_biological_profile',
        // Dental and Postural
        'dental_records',
        'postural_assessment',
        'dental_health_status',
        'postural_alignment',
        'dental_treatments',
        'postural_corrections',
        'icd_10_dental',
        'icd_10_postural',
        // Injuries
        'injury_records',
        'fifa_fmarc_assessments',
        'scat_assessments',
        'injury_severity',
        'injury_mechanism',
        'injury_location',
        'injury_date',
        'return_to_play_date',
        'snomed_ct_injury',
        'icd_10_injury',
        // Heart Diseases
        'heart_disease_assessments',
        'cardiac_risk_factors',
        'cardiac_markers',
        'heart_disease_status',
        'icd_10_cardiac',
        'snomed_ct_cardiac',
        // MAPA
        'mapa_results',
        'mapa_status',
        'mapa_test_date',
        'mapa_24h_profile',
        'mapa_day_night_ratio',
        'mapa_dipper_status',
        'loinc_mapa',
        // MRI
        'mri_results',
        'mri_body_part',
        'mri_technique',
        'mri_date',
        'mri_facility',
        'mri_radiologist',
        'mri_findings',
        'loinc_mri',
        'snomed_ct_mri',
        // ECG Effort
        'ecg_effort_results',
        'ecg_effort_status',
        'ecg_effort_date',
        'ecg_effort_duration',
        'ecg_effort_max_hr',
        'ecg_effort_interpretation',
        'ecg_effort_findings',
        'loinc_ecg_effort',
        'snomed_ct_ecg_effort',
        // Scintigraphy
        'scintigraphy_results',
        'scintigraphy_type',
        'scintigraphy_isotope',
        'scintigraphy_date',
        'scintigraphy_facility',
        'scintigraphy_radiologist',
        'scintigraphy_findings',
        'loinc_scintigraphy',
        'snomed_ct_scintigraphy',
        // Medical Terminology
        'icd_10_codes',
        'snomed_ct_codes',
        'loinc_codes',
        'medical_record_type',
        'medical_record_category'
    ];

    protected $casts = [
        'record_date' => 'datetime',
        'next_checkup_date' => 'datetime',
        'visit_date' => 'datetime',
        'allergies' => 'array',
        'medications' => 'array',
        'medical_history' => 'array',
        'symptoms' => 'array',
        'risk_score' => 'float',
        'prediction_confidence' => 'float',
        'blood_pressure_systolic' => 'integer',
        'blood_pressure_diastolic' => 'integer',
        'heart_rate' => 'integer',
        'temperature' => 'float',
        'weight' => 'float',
        'height' => 'float',
        'bmi' => 'float',
        // New comprehensive medical fields
        'illness_records' => 'array',
        'heart_disease_records' => 'array',
        'doping_tests' => 'array',
        'last_doping_test_date' => 'datetime',
        'next_doping_test_date' => 'datetime',
        'aut_records' => 'array',
        'aut_approval_date' => 'datetime',
        'aut_expiry_date' => 'datetime',
        'aut_start_date' => 'date',
        'aut_end_date' => 'date',
        'blood_test_results' => 'array',
        'last_blood_test_date' => 'datetime',
        'next_blood_test_date' => 'datetime',
        'hematology_results' => 'array',
        'biochemistry_results' => 'array',
        'hormone_results' => 'array',
        'vitamin_results' => 'array',
        'mineral_results' => 'array',
        'biological_profile' => 'array',
        'metabolic_markers' => 'array',
        'inflammatory_markers' => 'array',
        'oxidative_stress_markers' => 'array',
        'dental_records' => 'array',
        'postural_assessment' => 'array',
        'dental_treatments' => 'array',
        'postural_corrections' => 'array',
        'injury_records' => 'array',
        'fifa_fmarc_assessments' => 'array',
        'scat_assessments' => 'array',
        'injury_date' => 'datetime',
        'return_to_play_date' => 'datetime',
        'heart_disease_assessments' => 'array',
        'cardiac_markers' => 'array',
        'mapa_results' => 'array',
        'mapa_test_date' => 'datetime',
        'mapa_24h_profile' => 'array',
        'mapa_day_night_ratio' => 'array',
        'mri_results' => 'array',
        'mri_date' => 'datetime',
        'mri_findings' => 'array',
        'ecg_effort_results' => 'array',
        'ecg_effort_date' => 'datetime',
        'ecg_effort_duration' => 'integer',
        'ecg_effort_max_hr' => 'integer',
        'ecg_effort_findings' => 'array',
        'scintigraphy_results' => 'array',
        'scintigraphy_date' => 'datetime',
        'scintigraphy_findings' => 'array',
        'icd_10_codes' => 'array',
        'snomed_ct_codes' => 'array',
        'loinc_codes' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(MedicalPrediction::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function dentalAnnotations(): HasMany
    {
        return $this->hasMany(DentalAnnotation::class);
    }

    public function getRiskLevelAttribute(): string
    {
        if ($this->risk_score < 0.3) return 'Faible';
        if ($this->risk_score < 0.6) return 'Modéré';
        return 'Élevé';
    }

    public function getBmiCategoryAttribute(): string
    {
        if ($this->bmi < 18.5) return 'Insuffisance pondérale';
        if ($this->bmi < 25) return 'Normal';
        if ($this->bmi < 30) return 'Surpoids';
        return 'Obésité';
    }
}
