<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFifaCompliantPCMARequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Basic PCMA fields
            'athlete_id' => 'required|exists:athletes,id',
            'player_id' => 'nullable|exists:players,id',
            'fifa_connect_id' => 'nullable|exists:fifa_connect_ids,id',
            'type' => ['required', Rule::in(['bpma', 'cardio', 'dental', 'neurological', 'orthopedic'])],
            'status' => ['required', Rule::in(['pending', 'completed', 'failed', 'cleared', 'not_cleared'])],
            'assessor_id' => 'required|exists:users,id',
            'assessment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            
            // FIFA-specific fields
            'fifa_id' => 'nullable|string|max:50',
            'competition_name' => 'nullable|string|max:100',
            'competition_date' => 'nullable|string|max:20',
            'team_name' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:50',
            
            // Medical History (Player Questionnaire)
            'medical_history' => 'nullable|array',
            'medical_history.personal_details' => 'nullable|array',
            'medical_history.personal_details.name' => 'nullable|string|max:100',
            'medical_history.personal_details.date_of_birth' => 'nullable|date',
            'medical_history.personal_details.age' => 'nullable|integer|min:0|max:120',
            'medical_history.personal_details.gender' => 'nullable|string|in:male,female,other',
            'medical_history.personal_details.nationality' => 'nullable|string|max:50',
            'medical_history.personal_details.passport_number' => 'nullable|string|max:50',
            
            'medical_history.contact_information' => 'nullable|array',
            'medical_history.contact_information.address' => 'nullable|string|max:200',
            'medical_history.contact_information.phone' => 'nullable|string|max:20',
            'medical_history.contact_information.email' => 'nullable|email|max:100',
            'medical_history.contact_information.emergency_contact' => 'nullable|string|max:100',
            'medical_history.contact_information.emergency_phone' => 'nullable|string|max:20',
            
            'medical_history.medical_questionnaire' => 'nullable|array',
            'medical_history.medical_questionnaire.has_heart_problems' => 'nullable|boolean',
            'medical_history.medical_questionnaire.heart_problem_details' => 'nullable|string|max:500',
            'medical_history.medical_questionnaire.has_chest_pain' => 'nullable|boolean',
            'medical_history.medical_questionnaire.chest_pain_details' => 'nullable|string|max:500',
            'medical_history.medical_questionnaire.has_dizziness' => 'nullable|boolean',
            'medical_history.medical_questionnaire.dizziness_details' => 'nullable|string|max:500',
            'medical_history.medical_questionnaire.has_shortness_of_breath' => 'nullable|boolean',
            'medical_history.medical_questionnaire.shortness_of_breath_details' => 'nullable|string|max:500',
            'medical_history.medical_questionnaire.has_fainting' => 'nullable|boolean',
            'medical_history.medical_questionnaire.fainting_details' => 'nullable|string|max:500',
            'medical_history.medical_questionnaire.has_high_blood_pressure' => 'nullable|boolean',
            'medical_history.medical_questionnaire.high_blood_pressure_details' => 'nullable|string|max:500',
            'medical_history.medical_questionnaire.has_diabetes' => 'nullable|boolean',
            'medical_history.medical_questionnaire.diabetes_details' => 'nullable|string|max:500',
            'medical_history.medical_questionnaire.has_asthma' => 'nullable|boolean',
            'medical_history.medical_questionnaire.asthma_details' => 'nullable|string|max:500',
            'medical_history.medical_questionnaire.has_epilepsy' => 'nullable|boolean',
            'medical_history.medical_questionnaire.epilepsy_details' => 'nullable|string|max:500',
            'medical_history.medical_questionnaire.has_other_conditions' => 'nullable|boolean',
            'medical_history.medical_questionnaire.other_conditions_details' => 'nullable|string|max:500',
            
            'medical_history.family_history' => 'nullable|array',
            'medical_history.family_history.has_family_heart_problems' => 'nullable|boolean',
            'medical_history.family_history.family_heart_problem_details' => 'nullable|string|max:500',
            'medical_history.family_history.has_family_sudden_death' => 'nullable|boolean',
            'medical_history.family_history.family_sudden_death_details' => 'nullable|string|max:500',
            'medical_history.family_history.has_family_other_conditions' => 'nullable|boolean',
            'medical_history.family_history.family_other_conditions_details' => 'nullable|string|max:500',
            
            'medical_history.medication_history' => 'nullable|array',
            'medical_history.medication_history.current_medications' => 'nullable|string|max:500',
            'medical_history.medication_history.allergies' => 'nullable|string|max:500',
            'medical_history.medication_history.supplements' => 'nullable|string|max:500',
            
            'medical_history.lifestyle_factors' => 'nullable|array',
            'medical_history.lifestyle_factors.smoking' => 'nullable|boolean',
            'medical_history.lifestyle_factors.smoking_details' => 'nullable|string|max:200',
            'medical_history.lifestyle_factors.alcohol' => 'nullable|boolean',
            'medical_history.lifestyle_factors.alcohol_details' => 'nullable|string|max:200',
            'medical_history.lifestyle_factors.drugs' => 'nullable|boolean',
            'medical_history.lifestyle_factors.drugs_details' => 'nullable|string|max:200',
            
            // Physical Examination (Physician Record)
            'physical_examination' => 'nullable|array',
            'physical_examination.general_appearance' => 'nullable|string|max:200',
            'physical_examination.vital_signs' => 'nullable|array',
            'physical_examination.vital_signs.blood_pressure_systolic' => 'nullable|integer|min:0|max:300',
            'physical_examination.vital_signs.blood_pressure_diastolic' => 'nullable|integer|min:0|max:200',
            'physical_examination.vital_signs.heart_rate' => 'nullable|integer|min:0|max:300',
            'physical_examination.vital_signs.temperature' => 'nullable|numeric|min:30|max:45',
            'physical_examination.vital_signs.weight' => 'nullable|numeric|min:0|max:300',
            'physical_examination.vital_signs.height' => 'nullable|numeric|min:0|max:300',
            'physical_examination.vital_signs.bmi' => 'nullable|numeric|min:0|max:100',
            
            'physical_examination.cardiovascular_examination' => 'nullable|array',
            'physical_examination.cardiovascular_examination.heart_sounds' => 'nullable|string|max:200',
            'physical_examination.cardiovascular_examination.pulse' => 'nullable|string|max:200',
            'physical_examination.cardiovascular_examination.blood_pressure' => 'nullable|string|max:200',
            'physical_examination.cardiovascular_examination.capillary_refill' => 'nullable|string|max:200',
            'physical_examination.cardiovascular_examination.jugular_venous_pressure' => 'nullable|string|max:200',
            'physical_examination.cardiovascular_examination.peripheral_edema' => 'nullable|string|max:200',
            
            'physical_examination.respiratory_examination' => 'nullable|array',
            'physical_examination.respiratory_examination.breathing_pattern' => 'nullable|string|max:200',
            'physical_examination.respiratory_examination.lung_sounds' => 'nullable|string|max:200',
            'physical_examination.respiratory_examination.chest_movement' => 'nullable|string|max:200',
            
            'physical_examination.musculoskeletal_examination' => 'nullable|array',
            'physical_examination.musculoskeletal_examination.general_mobility' => 'nullable|string|max:200',
            'physical_examination.musculoskeletal_examination.joint_range_of_motion' => 'nullable|string|max:200',
            'physical_examination.musculoskeletal_examination.muscle_strength' => 'nullable|string|max:200',
            'physical_examination.musculoskeletal_examination.coordination' => 'nullable|string|max:200',
            'physical_examination.musculoskeletal_examination.balance' => 'nullable|string|max:200',
            'physical_examination.musculoskeletal_examination.gait' => 'nullable|string|max:200',
            
            'physical_examination.neurological_examination' => 'nullable|array',
            'physical_examination.neurological_examination.mental_status' => 'nullable|string|max:200',
            'physical_examination.neurological_examination.cranial_nerves' => 'nullable|string|max:200',
            'physical_examination.neurological_examination.motor_function' => 'nullable|string|max:200',
            'physical_examination.neurological_examination.sensory_function' => 'nullable|string|max:200',
            'physical_examination.neurological_examination.reflexes' => 'nullable|string|max:200',
            'physical_examination.neurological_examination.coordination' => 'nullable|string|max:200',
            
            // Anatomical annotations for interactive diagrams
            'physical_examination.anatomical_annotations' => 'nullable|array',
            'physical_examination.anatomical_annotations.anterior' => 'nullable|array',
            'physical_examination.anatomical_annotations.anterior.*.x' => 'required_with:physical_examination.anatomical_annotations.anterior.*|integer|min:0|max:1000',
            'physical_examination.anatomical_annotations.anterior.*.y' => 'required_with:physical_examination.anatomical_annotations.anterior.*|integer|min:0|max:1000',
            'physical_examination.anatomical_annotations.anterior.*.note' => 'required_with:physical_examination.anatomical_annotations.anterior.*|string|max:500',
            'physical_examination.anatomical_annotations.posterior' => 'nullable|array',
            'physical_examination.anatomical_annotations.posterior.*.x' => 'required_with:physical_examination.anatomical_annotations.posterior.*|integer|min:0|max:1000',
            'physical_examination.anatomical_annotations.posterior.*.y' => 'required_with:physical_examination.anatomical_annotations.posterior.*|integer|min:0|max:1000',
            'physical_examination.anatomical_annotations.posterior.*.note' => 'required_with:physical_examination.anatomical_annotations.posterior.*|string|max:500',
            
            // Cardiovascular Investigations
            'cardiovascular_investigations' => 'nullable|array',
            'cardiovascular_investigations.ecg' => 'nullable|array',
            'cardiovascular_investigations.ecg.performed' => 'nullable|boolean',
            'cardiovascular_investigations.ecg.date' => 'nullable|date',
            'cardiovascular_investigations.ecg.result' => 'nullable|string|max:200',
            'cardiovascular_investigations.ecg.abnormalities' => 'nullable|string|max:500',
            'cardiovascular_investigations.ecg.interpretation' => 'nullable|string|max:500',
            
            'cardiovascular_investigations.echocardiogram' => 'nullable|array',
            'cardiovascular_investigations.echocardiogram.performed' => 'nullable|boolean',
            'cardiovascular_investigations.echocardiogram.date' => 'nullable|date',
            'cardiovascular_investigations.echocardiogram.result' => 'nullable|string|max:200',
            'cardiovascular_investigations.echocardiogram.abnormalities' => 'nullable|string|max:500',
            'cardiovascular_investigations.echocardiogram.interpretation' => 'nullable|string|max:500',
            
            'cardiovascular_investigations.stress_test' => 'nullable|array',
            'cardiovascular_investigations.stress_test.performed' => 'nullable|boolean',
            'cardiovascular_investigations.stress_test.date' => 'nullable|date',
            'cardiovascular_investigations.stress_test.result' => 'nullable|string|max:200',
            'cardiovascular_investigations.stress_test.abnormalities' => 'nullable|string|max:500',
            'cardiovascular_investigations.stress_test.interpretation' => 'nullable|string|max:500',
            
            'cardiovascular_investigations.other_tests' => 'nullable|array',
            'cardiovascular_investigations.other_tests.*.test_name' => 'nullable|string|max:100',
            'cardiovascular_investigations.other_tests.*.performed' => 'nullable|boolean',
            'cardiovascular_investigations.other_tests.*.date' => 'nullable|date',
            'cardiovascular_investigations.other_tests.*.result' => 'nullable|string|max:200',
            'cardiovascular_investigations.other_tests.*.abnormalities' => 'nullable|string|max:500',
            'cardiovascular_investigations.other_tests.*.interpretation' => 'nullable|string|max:500',
            
            // Final Statement
            'final_statement' => 'nullable|array',
            'final_statement.assessment_summary' => 'nullable|string|max:1000',
            'final_statement.recommendations' => 'nullable|string|max:1000',
            'final_statement.restrictions' => 'nullable|string|max:500',
            'final_statement.follow_up_required' => 'nullable|boolean',
            'final_statement.follow_up_details' => 'nullable|string|max:500',
            'final_statement.cleared_for_competition' => 'nullable|boolean',
            'final_statement.cleared_with_restrictions' => 'nullable|boolean',
            'final_statement.not_cleared' => 'nullable|boolean',
            'final_statement.reason_for_not_cleared' => 'nullable|string|max:500',
            'final_statement.physician_signature' => 'nullable|string|max:100',
            'final_statement.physician_license_number' => 'nullable|string|max:50',
            'final_statement.assessment_date' => 'nullable|date',
            
            // SCAT Assessment
            'scat_assessment' => 'nullable|array',
            'scat_assessment.performed' => 'nullable|boolean',
            'scat_assessment.date' => 'nullable|date',
            'scat_assessment.total_score' => 'nullable|integer|min:0|max:132',
            'scat_assessment.symptom_severity_score' => 'nullable|integer|min:0|max:132',
            'scat_assessment.cognitive_score' => 'nullable|integer|min:0|max:30',
            'scat_assessment.neck_pain_score' => 'nullable|integer|min:0|max:10',
            'scat_assessment.balance_score' => 'nullable|integer|min:0|max:30',
            'scat_assessment.coordination_score' => 'nullable|integer|min:0|max:40',
            'scat_assessment.interpretation' => 'nullable|string|max:500',
            'scat_assessment.recommendations' => 'nullable|string|max:500',
            
            // Attachments
            'attachments' => 'nullable|array',
            'attachments.*.file_name' => 'nullable|string|max:200',
            'attachments.*.file_path' => 'nullable|string|max:500',
            'attachments.*.file_type' => 'nullable|string|max:50',
            'attachments.*.file_size' => 'nullable|integer|min:0',
            'attachments.*.upload_date' => 'nullable|date',
            
            // Medical Imaging Files
            'ecg_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
            'ecg_date' => 'nullable|date',
            'ecg_interpretation' => 'nullable|string|max:500',
            'ecg_notes' => 'nullable|string',
            
            'mri_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
            'mri_date' => 'nullable|date',
            'mri_type' => 'nullable|string|max:100',
            'mri_findings' => 'nullable|string|max:500',
            'mri_notes' => 'nullable|string',
            
            'xray_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            'ct_scan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            'ultrasound_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            
            // Version tracking
            'form_version' => 'nullable|string|max:10',
            'last_updated_at' => 'nullable|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'athlete_id.required' => 'L\'athlète est requis.',
            'athlete_id.exists' => 'L\'athlète sélectionné n\'existe pas.',
            'type.required' => 'Le type de PCMA est requis.',
            'type.in' => 'Le type de PCMA doit être valide.',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être valide.',
            'assessor_id.required' => 'L\'évaluateur est requis.',
            'assessor_id.exists' => 'L\'évaluateur sélectionné n\'existe pas.',
            'assessment_date.required' => 'La date d\'évaluation est requise.',
            'assessment_date.date' => 'La date d\'évaluation doit être une date valide.',
            
            // Anatomical annotations validation
            'physical_examination.anatomical_annotations.anterior.*.x.required_with' => 'La coordonnée X est requise pour les annotations.',
            'physical_examination.anatomical_annotations.anterior.*.y.required_with' => 'La coordonnée Y est requise pour les annotations.',
            'physical_examination.anatomical_annotations.anterior.*.note.required_with' => 'La note est requise pour les annotations.',
            'physical_examination.anatomical_annotations.posterior.*.x.required_with' => 'La coordonnée X est requise pour les annotations.',
            'physical_examination.anatomical_annotations.posterior.*.y.required_with' => 'La coordonnée Y est requise pour les annotations.',
            'physical_examination.anatomical_annotations.posterior.*.note.required_with' => 'La note est requise pour les annotations.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'athlete_id' => 'athlète',
            'type' => 'type de PCMA',
            'status' => 'statut',
            'assessor_id' => 'évaluateur',
            'assessment_date' => 'date d\'évaluation',
            'medical_history' => 'historique médical',
            'physical_examination' => 'examen physique',
            'cardiovascular_investigations' => 'investigations cardiovasculaires',
            'final_statement' => 'déclaration finale',
            'scat_assessment' => 'évaluation SCAT',
            'anatomical_annotations' => 'annotations anatomiques',
        ];
    }
} 