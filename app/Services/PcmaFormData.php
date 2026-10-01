<?php
namespace App\Services;
use App\Models\PCMA;
use App\Rules\FifaIdentifier;
final class PcmaFormData
{
    public function rules(bool $complete = true): array
    {
        $rules = [
                'player_id' => 'required|exists:players,id',
                'pcma_id' => 'nullable|integer',
                'draft_token' => 'nullable|uuid',
                'type' => 'required|in:pcma,cardio,dental,neurological,orthopedic',
                'assessor_id' => 'required|exists:users,id',
                'assessment_date' => 'required|date',
                'result_json' => 'nullable|json',
                'status' => 'required|in:pending,completed,failed',
                'notes' => 'nullable|string',
                'clinical_notes' => 'nullable|string',
                'final_statement' => 'required|array',
                'final_statement.overall_decision' => 'required|in:FIT,NOT_FIT,CONDITIONAL',
                // FIFA Compliance Fields
                'fifa_connect_id' => [
                    'nullable',
                    new FifaIdentifier(),
                ],
                'fifa_id' => [
                    'nullable',
                    new FifaIdentifier(),
                ],
                'competition_name' => 'nullable|string|max:255',
                'competition_date' => 'nullable|date',
                'team_name' => 'nullable|string|max:255',
                'position' => 'nullable|in:goalkeeper,defender,midfielder,forward',
                'fifa_compliant' => 'nullable|boolean',
                // Vital Signs
                'blood_pressure' => 'nullable|string|max:255',
                'heart_rate' => 'nullable|integer|min:0|max:300',
                'temperature' => 'nullable|numeric|min:30|max:45',
                'respiratory_rate' => 'nullable|integer|min:0|max:100',
                'oxygen_saturation' => 'nullable|integer|min:0|max:100',
                'weight' => 'nullable|numeric|min:0|max:500',
                // Medical History
                'medical_history' => 'nullable|string',
                'cardiovascular_history' => 'nullable|string',
                'cardiovascular_icd11' => ['nullable', \Illuminate\Validation\Rule::in(array_keys(config('pcma_icd11.cardiovascular', [])))],
                'surgical_history' => 'nullable|string',
                'medications' => 'nullable|string',
                'medication_selection' => 'nullable|json|max:20000',
                'cardiovascular_icd11_selection' => 'nullable|json|max:20000',
                'surgical_icd11_selection' => 'nullable|json|max:20000',
                'allergies_icd11_selection' => 'nullable|json|max:20000',
                'allergies' => 'nullable|string',
                // Physical Examination
                'general_appearance' => 'nullable|in:normal,abnormal',
                'skin_examination' => 'nullable|in:normal,abnormal',
                'lymph_nodes' => 'nullable|in:normal,enlarged',
                'abdomen_examination' => 'nullable|in:normal,abnormal',
                // Cardiovascular Assessment
                'cardiac_rhythm' => 'nullable|in:sinus,irregular,arrhythmia',
                'heart_murmur' => 'nullable|in:none,systolic,diastolic',
                'blood_pressure_rest' => 'nullable|string|max:255',
                'blood_pressure_exercise' => 'nullable|string|max:255',
                // Neurological Assessment
                'consciousness' => 'nullable|in:alert,confused,drowsy',
                'cranial_nerves' => 'nullable|in:normal,abnormal',
                'motor_function' => 'nullable|in:normal,weakness,paralysis',
                'sensory_function' => 'nullable|in:normal,decreased,absent',
                // Musculoskeletal Assessment
                'joint_mobility' => 'nullable|in:normal,limited,restricted',
                'muscle_strength' => 'nullable|in:normal,reduced,weak',
                'pain_assessment' => 'nullable|in:none,mild,moderate,severe',
                'range_of_motion' => 'nullable|in:full,limited,restricted',
                // Medical Imaging
                'ecg_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ecg_date' => 'nullable|date',
                'ecg_interpretation' => 'nullable|in:normal,sinus_bradycardia,sinus_tachycardia,atrial_fibrillation,ventricular_tachycardia,st_elevation,st_depression,qt_prolongation,abnormal',
                'ecg_notes' => 'nullable|string',
                'mri_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'mri_date' => 'nullable|date',
                'mri_type' => 'nullable|in:brain,spine,knee,shoulder,ankle,hip,cardiac,other',
                'mri_findings' => 'nullable|in:normal,mild_abnormality,moderate_abnormality,severe_abnormality,fracture,tumor,inflammation,degenerative,other',
                'mri_notes' => 'nullable|string',
                'xray_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ct_scan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ultrasound_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                // Signature fields
                'is_signed' => 'nullable|boolean',
                'signed_at' => 'nullable|date',
                'signed_by' => 'nullable|string|max:255',
                'license_number' => 'nullable|string|max:255',
                'signature_image' => 'nullable|string|max:4194304',
                'signature_data' => 'nullable|json',
            ];
        if (!$complete) { $rules['final_statement'] = 'nullable|array'; $rules['final_statement.overall_decision'] = 'nullable|in:FIT,NOT_FIT,CONDITIONAL'; }
        return $rules;
    }
    // Seul le parcours de signature vérifiée peut renseigner ces attributs.
    public function withoutSignature(array $data): array
    {
        $fields = ['is_signed', 'signed_at', 'signed_by', 'license_number', 'signature_image',
            'signature_data', 'fifa_compliant', 'fifa_approved_at', 'fifa_approved_by'];
        foreach ($fields as $field) unset($data[$field]);
        if (isset($data['result_json'])) {
            $json = is_array($data['result_json']) ? $data['result_json'] : json_decode($data['result_json'], true);
            if (is_array($json)) {
                foreach ($fields as $field) unset($json[$field]);
                $data['result_json'] = $json;
            }
        }
        return $data;
    }

    // Conserver les champs validés sans colonne dédiée dans le JSON existant.
    public function preserve(array $validated, $previous = null): array
    {
        if (array_key_exists('clinical_notes', $validated)) {
            $validated['notes'] = $validated['clinical_notes'];
            unset($validated['clinical_notes']);
        }
        $result = $validated['result_json'] ?? $previous ?? [];
        if (is_string($result)) { $result = json_decode($result, true); }
        $result = is_array($result) ? $result : [];
        if (is_array($previous)) $result = array_replace_recursive($previous, $result);

        $groups = [
            'abdomen_examination' => 'physical_examination',
            'allergies' => 'medical_history',
            'blood_pressure' => 'vital_signs',
            'blood_pressure_exercise' => 'cardiovascular_assessment',
            'blood_pressure_rest' => 'cardiovascular_assessment',
            'cardiac_rhythm' => 'cardiovascular_assessment',
            'cardiovascular_history' => 'medical_history',
            'consciousness' => 'neurological_assessment',
            'cranial_nerves' => 'neurological_assessment',
            'general_appearance' => 'physical_examination',
            'heart_murmur' => 'cardiovascular_assessment',
            'heart_rate' => 'vital_signs',
            'joint_mobility' => 'musculoskeletal_assessment',
            'lymph_nodes' => 'physical_examination',
            'medications' => 'medical_history',
            'motor_function' => 'neurological_assessment',
            'muscle_strength' => 'musculoskeletal_assessment',
            'oxygen_saturation' => 'vital_signs',
            'pain_assessment' => 'musculoskeletal_assessment',
            'range_of_motion' => 'musculoskeletal_assessment',
            'respiratory_rate' => 'vital_signs',
            'sensory_function' => 'neurological_assessment',
            'skin_examination' => 'physical_examination',
            'surgical_history' => 'medical_history',
            'temperature' => 'vital_signs',
            'weight' => 'vital_signs',
        ];
        foreach ($groups as $field => $group) {
            if (array_key_exists($field, $validated)) {
                if (!is_array($result[$group] ?? null)) { $result[$group] = []; }
                $result[$group][$field] = $validated[$field];
                unset($validated[$field]);
            }
        }
        if (array_key_exists('medical_history', $validated)) {
            if (!is_array($result['medical_history'] ?? null)) { $result['medical_history'] = []; }
            $result['medical_history']['cardiovascular_history'] = $validated['medical_history'];
            $validated['medical_history'] = $result['medical_history'];
        }
        if (array_key_exists('medication_selection', $validated)) {
            $result['medical_history']['medication_products'] = app(MedicationCatalogue::class)
                ->selections($validated['medication_selection'] ?? '[]');
            unset($validated['medication_selection']);
        }
        if (array_key_exists('cardiovascular_icd11', $validated)) {
            $result['medical_history']['cardiovascular_icd11'] = $validated['cardiovascular_icd11']
                ? config('pcma_icd11.cardiovascular.'.$validated['cardiovascular_icd11']) : null;
            unset($validated['cardiovascular_icd11']);
        }
        $validated['result_json'] = $result;
        return app(WhoIcd11::class)->applySelections($validated);
    }

}
