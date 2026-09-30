@extends('layouts.app')

@section('title', __('health_records_edit.edit_medical_record').' - Med Predictor')

@push('scripts')
<!-- Vue.js for Postural Assessment Component -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
@endpush

<style>
/* Dental Chart Styles */
.dental-chart-container {
    position: relative;
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.chart-header {
    margin-bottom: 20px;
}

.chart-wrapper {
    display: flex;
    justify-content: center;
    margin: 20px 0;
    overflow-x: auto;
}

.dental-svg-container {
    position: relative;
    min-width: 800px;
}

.dental-tooltip {
    position: absolute;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
    pointer-events: none;
    white-space: nowrap;
    z-index: 1000;
    display: none;
}

.tooltip-content {
    line-height: 1.4;
}

/* SVG Styles */
.tooth {
    cursor: pointer;
    transition: all 0.2s ease;
}

.tooth:hover {
    opacity: 0.8;
}

.tooth-surface {
    cursor: pointer;
    transition: fill 0.2s ease;
}

.status-healthy {
    fill: #ffffff;
    stroke: #cccccc;
}

.status-caries {
    fill: #ff4d4d;
    stroke: #cc0000;
}

.status-restoration {
    fill: #4d94ff;
    stroke: #0066cc;
}

.status-crown {
    stroke: #4d94ff;
    stroke-width: 3px;
    fill-opacity: 0.1;
}

.status-missing {
    fill: #808080;
    opacity: 0.5;
}
</style>

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">🏥 {{ __('health_records_edit.edit_medical_record') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('health_records_edit.edit_the_existing_medical_record') }}</p>
        </div>

        <form action="{{ route('health-records.update', $healthRecord) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- AI-Assisted Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h2 class="text-xl font-semibold text-blue-900">{{ __('health_records_edit.medical_ai_assistant') }}</h2>
                </div>
                <p class="text-blue-700 mb-4">{{ __('health_records_edit.describe_symptoms_for_automatic_analysis') }}</p>
                
                <div class="space-y-4">
                    <div>
                        <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_edit.clinical_notes_2') }}
                        </label>
                        <textarea 
                            id="clinical_notes" 
                            name="clinical_notes" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="{{ __('health_records_edit.example_patient_complains_of_chest_pain_') }}"
                        ></textarea>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button 
                            type="button" 
                            id="ai-analyze-btn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        >
                            🔍 {{ __('health_records_edit.analyze_with_ai') }}
                        </button>
                        <button 
                            type="button" 
                            id="clear-notes-btn"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
                        >
                            {{ __('health_records_edit.clear') }}
                        </button>
                    </div>
                    
                    <div id="ai-results" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('health_records_edit.ai_analysis') }}</h3>
                        <div id="ai-content" class="text-sm text-gray-700"></div>
                    </div>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('health_records_edit.patient_information') }}</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Player Selection -->
                    <div>
                        <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_edit.player') }}
                        </label>
                        <select 
                            id="player_id" 
                            name="player_id" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">{{ __('health_records_edit.select_a_player') }}</option>
                            @foreach($players as $player)
                                <option value="{{ $player->id }}" {{ old('player_id', $healthRecord->player_id) == $player->id ? 'selected' : '' }}>
                                    {{ $player->name }} ({{ $player->club->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- EMR Visit Information -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="visit_date" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.visit_date') }}
                            </label>
                            <input 
                                type="date" 
                                id="visit_date" 
                                name="visit_date" 
                                value="{{ old('visit_date', $healthRecord->visit_date ? $healthRecord->visit_date->format('Y-m-d') : date('Y-m-d')) }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label for="doctor_name" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.doctor') }}
                            </label>
                            <input 
                                type="text" 
                                id="doctor_name" 
                                name="doctor_name" 
                                value="{{ old('doctor_name', $healthRecord->doctor_name) }}"
                                required
                                placeholder="{{ __('health_records_edit.doctor_s_name') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label for="visit_type" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.visit_type') }}
                            </label>
                            <select 
                                id="visit_type" 
                                name="visit_type" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">{{ __('health_records_edit.select_type') }}</option>
                                <option value="consultation" {{ old('visit_type', $healthRecord->visit_type) == 'consultation' ? 'selected' : '' }}>Consultation</option>
                                <option value="emergency" {{ old('visit_type', $healthRecord->visit_type) == 'emergency' ? 'selected' : '' }}>{{ __('health_records_edit.emergency') }}</option>
                                <option value="follow_up" {{ old('visit_type', $healthRecord->visit_type) == 'follow_up' ? 'selected' : '' }}>{{ __('health_records_edit.follow_up') }}</option>
                                <option value="pre_season" {{ old('visit_type', $healthRecord->visit_type) == 'pre_season' ? 'selected' : '' }}>{{ __('health_records_edit.pre_season') }}</option>
                                <option value="post_match" {{ old('visit_type', $healthRecord->visit_type) == 'post_match' ? 'selected' : '' }}>Post-match</option>
                                <option value="rehabilitation" {{ old('visit_type', $healthRecord->visit_type) == 'rehabilitation' ? 'selected' : '' }}>{{ __('health_records_edit.rehabilitation') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Chief Complaint -->
                    <div>
                        <label for="chief_complaint" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_edit.chief_complaint') }}
                        </label>
                        <textarea 
                            id="chief_complaint" 
                            name="chief_complaint" 
                            rows="3"
                            placeholder="{{ __('health_records_edit.describe_the_main_reason_for_the_consult') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >{{ old('chief_complaint', $healthRecord->chief_complaint) }}</textarea>
                    </div>

                    <!-- Vital Signs -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="blood_pressure_systolic" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.systolic_blood_pressure_mmhg') }}
                            </label>
                            <input 
                                type="number" 
                                id="blood_pressure_systolic" 
                                name="blood_pressure_systolic" 
                                value="{{ old('blood_pressure_systolic', $healthRecord->blood_pressure_systolic) }}"
                                min="70" max="200"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label for="blood_pressure_diastolic" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.diastolic_blood_pressure_mmhg') }}
                            </label>
                            <input 
                                type="number" 
                                id="blood_pressure_diastolic" 
                                name="blood_pressure_diastolic" 
                                value="{{ old('blood_pressure_diastolic', $healthRecord->blood_pressure_diastolic) }}"
                                min="40" max="130"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.heart_rate_bpm') }}
                            </label>
                            <input 
                                type="number" 
                                id="heart_rate" 
                                name="heart_rate" 
                                value="{{ old('heart_rate', $healthRecord->heart_rate) }}"
                                min="40" max="200"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                    </div>

                    <!-- Physical Measurements -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.temperature_c') }}
                            </label>
                            <input 
                                type="number" 
                                id="temperature" 
                                name="temperature" 
                                value="{{ old('temperature', $healthRecord->temperature) }}"
                                min="35" max="42" step="0.1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.weight_kg') }}
                            </label>
                            <input 
                                type="number" 
                                id="weight" 
                                name="weight" 
                                value="{{ old('weight', $healthRecord->weight) }}"
                                min="30" max="200" step="0.1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.height_cm') }}
                            </label>
                            <input 
                                type="number" 
                                id="height" 
                                name="height" 
                                value="{{ old('height', $healthRecord->height) }}"
                                min="100" max="250"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.blood_type') }}
                            </label>
                            <select 
                                id="blood_type" 
                                name="blood_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">{{ __('health_records_edit.select') }}</option>
                                <option value="A+" {{ old('blood_type', $healthRecord->blood_type) == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_type', $healthRecord->blood_type) == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_type', $healthRecord->blood_type) == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_type', $healthRecord->blood_type) == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_type', $healthRecord->blood_type) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('blood_type', $healthRecord->blood_type) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('blood_type', $healthRecord->blood_type) == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_type', $healthRecord->blood_type) == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                        </div>
                    </div>

                    <!-- Medical Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                                Allergies
                            </label>
                            <textarea 
                                id="allergies" 
                                name="allergies" 
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('health_records_edit.list_of_known_allergies') }}"
                            >{{ old('allergies', is_array($healthRecord->allergies) ? json_encode($healthRecord->allergies, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) : $healthRecord->allergies) }}</textarea>
                        </div>
                        
                        <div>
                            <label for="medications" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.current_medications_2') }}
                            </label>
                            <textarea 
                                id="medications" 
                                name="medications" 
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('health_records_edit.current_medications') }}"
                            >{{ old('medications', is_array($healthRecord->medications) ? json_encode($healthRecord->medications, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) : $healthRecord->medications) }}</textarea>
                        </div>
                    </div>

                    <!-- Medical History and Symptoms -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.medical_history') }}
                            </label>
                            <textarea 
                                id="medical_history" 
                                name="medical_history" 
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('health_records_edit.important_medical_history') }}"
                            >{{ old('medical_history', is_array($healthRecord->medical_history) ? json_encode($healthRecord->medical_history, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) : $healthRecord->medical_history) }}</textarea>
                        </div>
                        
                        <div>
                            <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.current_symptoms') }}
                            </label>
                            <textarea 
                                id="symptoms" 
                                name="symptoms" 
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('health_records_edit.symptoms_reported_by_the_patient') }}"
                            >{{ old('symptoms', is_array($healthRecord->symptoms) ? json_encode($healthRecord->symptoms, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) : $healthRecord->symptoms) }}</textarea>
                        </div>
                    </div>

                    <!-- Comprehensive Medical Categories Section -->
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-purple-900">🏥 {{ __('health_records_edit.comprehensive_medical_categories') }}</h3>
                        </div>
                        <p class="text-purple-700 mb-4">{{ __('health_records_edit.medical_records_compliant_standards') }}</p>
                        
                        <!-- Illness & Heart Diseases -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="icd_10_primary_diagnosis" class="block text-sm font-medium text-gray-700 mb-2">
                                    🦠 {{ __('health_records_edit.general_diseases_icd_10_z00_z99_a00_b99_') }}
                                </label>
                                <select 
                                    id="icd_10_primary_diagnosis" 
                                    name="icd_10_primary_diagnosis"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                >
                                    <option value="">{{ __('health_records_edit.select_a_disease') }}</option>
                                    <optgroup label="{{ __('health_records_edit.z00_z99_factors_influencing_health_statu') }}">
                                        <option value="Z00.0">{{ __('health_records_edit.z00_0_general_medical_examination') }}</option>
                                        <option value="Z00.1">{{ __('health_records_edit.z00_1_routine_child_health_examination') }}</option>
                                        <option value="Z00.2">{{ __('health_records_edit.z00_2_routine_examination_during_growth') }}</option>
                                        <option value="Z00.8">{{ __('health_records_edit.z00_8_other_general_medical_examinations') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.a00_b99_infectious_diseases') }}">
                                        <option value="A00.0">{{ __('health_records_edit.a00_0_cholera_due_to_vibrio_cholerae_01_') }}</option>
                                        <option value="A01.0">{{ __('health_records_edit.a01_0_typhoid_fever') }}</option>
                                        <option value="A02.0">{{ __('health_records_edit.a02_0_salmonella_enteritis') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.c00_d49_neoplasms') }}">
                                        <option value="C00.0">{{ __('health_records_edit.c00_0_malignant_neoplasm_of_upper_lip') }}</option>
                                        <option value="C01">{{ __('health_records_edit.c01_malignant_neoplasm_of_base_of_tongue') }}</option>
                                        <option value="C02.0">{{ __('health_records_edit.c02_0_malignant_neoplasm_of_dorsal_surfa') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.e00_e89_endocrine_diseases') }}">
                                        <option value="E00.0">{{ __('health_records_edit.e00_0_congenital_iodine_deficiency_syndr') }}</option>
                                        <option value="E01.0">{{ __('health_records_edit.e01_0_diffuse_endemic_goiter_due_to_iodi') }}</option>
                                        <option value="E02">{{ __('health_records_edit.e02_subclinical_iodine_deficiency_hypoth') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.f00_f99_mental_disorders') }}">
                                        <option value="F00.0">{{ __('health_records_edit.f00_0_early_onset_alzheimer_s_disease_de') }}</option>
                                        <option value="F01.0">{{ __('health_records_edit.f01_0_acute_onset_vascular_dementia') }}</option>
                                        <option value="F02.0">{{ __('health_records_edit.f02_0_dementia_in_pick_s_disease') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.g00_g99_diseases_of_the_nervous_system') }}">
                                        <option value="G00.0">{{ __('health_records_edit.g00_0_haemophilus_meningitis') }}</option>
                                        <option value="G01">{{ __('health_records_edit.g01_meningitis_in_bacterial_diseases_cla') }}</option>
                                        <option value="G02.0">{{ __('health_records_edit.g02_0_meningitis_in_viral_diseases_class') }}</option>
                                    </optgroup>
                                </select>
                            </div>
                            
                            <div>
                                <label for="icd_10_cardiac" class="block text-sm font-medium text-gray-700 mb-2">
                                    ❤️ {{ __('health_records_edit.heart_diseases_icd_10_i00_i99_snomed_ct_') }}
                                </label>
                                <select 
                                    id="icd_10_cardiac" 
                                    name="icd_10_cardiac"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                >
                                    <option value="">{{ __('health_records_edit.select_a_cardiac_condition') }}</option>
                                    <optgroup label="{{ __('health_records_edit.i00_i02_acute_rheumatic_fever') }}">
                                        <option value="I00">{{ __('health_records_edit.i00_acute_rheumatic_fever_without_heart_') }}</option>
                                        <option value="I01.0">{{ __('health_records_edit.i01_0_acute_rheumatic_pericarditis') }}</option>
                                        <option value="I01.1">{{ __('health_records_edit.i01_1_acute_rheumatic_endocarditis') }}</option>
                                        <option value="I01.2">{{ __('health_records_edit.i01_2_acute_rheumatic_myocarditis') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.i05_i09_chronic_rheumatic_heart_diseases') }}">
                                        <option value="I05.0">{{ __('health_records_edit.i05_0_mitral_stenosis') }}</option>
                                        <option value="I05.1">{{ __('health_records_edit.i05_1_rheumatic_mitral_insufficiency') }}</option>
                                        <option value="I05.2">{{ __('health_records_edit.i05_2_mitral_stenosis_with_insufficiency') }}</option>
                                        <option value="I06.0">{{ __('health_records_edit.i06_0_rheumatic_aortic_stenosis') }}</option>
                                        <option value="I06.1">{{ __('health_records_edit.i06_1_rheumatic_aortic_insufficiency') }}</option>
                                        <option value="I06.2">{{ __('health_records_edit.i06_2_aortic_stenosis_with_insufficiency') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.i10_i15_hypertensive_diseases') }}">
                                        <option value="I10">{{ __('health_records_edit.i10_essential_primary_hypertension') }}</option>
                                        <option value="I11.0">{{ __('health_records_edit.i11_0_hypertensive_heart_failure_with_le') }}</option>
                                        <option value="I11.9">{{ __('health_records_edit.i11_9_hypertensive_heart_failure_without') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.i20_i25_ischemic_heart_diseases') }}">
                                        <option value="I20.0">{{ __('health_records_edit.i20_0_unstable_angina_pectoris') }}</option>
                                        <option value="I20.1">{{ __('health_records_edit.i20_1_angina_pectoris_with_documented_sp') }}</option>
                                        <option value="I20.8">{{ __('health_records_edit.i20_8_other_forms_of_angina_pectoris') }}</option>
                                        <option value="I20.9">{{ __('health_records_edit.i20_9_angina_pectoris_unspecified') }}</option>
                                        <option value="I21.0">{{ __('health_records_edit.i21_0_transmural_myocardial_infarction_o') }}</option>
                                        <option value="I21.1">{{ __('health_records_edit.i21_1_transmural_myocardial_infarction_o') }}</option>
                                        <option value="I21.2">{{ __('health_records_edit.i21_2_transmural_myocardial_infarction_o') }}</option>
                                        <option value="I21.3">{{ __('health_records_edit.i21_3_transmural_myocardial_infarction_o') }}</option>
                                        <option value="I21.4">{{ __('health_records_edit.i21_4_subendocardial_myocardial_infarcti') }}</option>
                                        <option value="I21.9">{{ __('health_records_edit.i21_9_acute_myocardial_infarction_unspec') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.i30_i52_other_forms_of_heart_disease') }}">
                                        <option value="I30.0">{{ __('health_records_edit.i30_0_acute_idiopathic_pericarditis') }}</option>
                                        <option value="I30.1">{{ __('health_records_edit.i30_1_infective_pericarditis') }}</option>
                                        <option value="I30.8">{{ __('health_records_edit.i30_8_other_forms_of_acute_pericarditis') }}</option>
                                        <option value="I30.9">{{ __('health_records_edit.i30_9_acute_pericarditis_unspecified') }}</option>
                                        <option value="I31.0">{{ __('health_records_edit.i31_0_chronic_adhesive_pericarditis') }}</option>
                                        <option value="I31.1">{{ __('health_records_edit.i31_1_chronic_constrictive_pericarditis') }}</option>
                                        <option value="I31.2">{{ __('health_records_edit.i31_2_hemopericardium_not_elsewhere_clas') }}</option>
                                        <option value="I31.8">{{ __('health_records_edit.i31_8_other_chronic_pericardial_diseases') }}</option>
                                        <option value="I31.9">{{ __('health_records_edit.i31_9_chronic_pericardial_disease_unspec') }}</option>
                                        <option value="I32.0">{{ __('health_records_edit.i32_0_pericarditis_in_bacterial_diseases') }}</option>
                                        <option value="I32.1">{{ __('health_records_edit.i32_1_pericarditis_in_other_infectious_a') }}</option>
                                        <option value="I32.8">{{ __('health_records_edit.i32_8_pericarditis_in_other_diseases_cla') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.i60_i69_cerebrovascular_diseases') }}">
                                        <option value="I60.0">{{ __('health_records_edit.i60_0_subarachnoid_hemorrhage_from_carot') }}</option>
                                        <option value="I60.1">{{ __('health_records_edit.i60_1_subarachnoid_hemorrhage_from_middl') }}</option>
                                        <option value="I60.2">{{ __('health_records_edit.i60_2_subarachnoid_hemorrhage_from_anter') }}</option>
                                        <option value="I60.3">{{ __('health_records_edit.i60_3_subarachnoid_hemorrhage_from_poste') }}</option>
                                        <option value="I60.4">{{ __('health_records_edit.i60_4_subarachnoid_hemorrhage_from_basil') }}</option>
                                        <option value="I60.5">{{ __('health_records_edit.i60_5_subarachnoid_hemorrhage_from_verte') }}</option>
                                        <option value="I60.6">{{ __('health_records_edit.i60_6_subarachnoid_hemorrhage_from_other') }}</option>
                                        <option value="I60.7">{{ __('health_records_edit.i60_7_subarachnoid_hemorrhage_from_unspe') }}</option>
                                        <option value="I60.8">{{ __('health_records_edit.i60_8_other_subarachnoid_hemorrhage') }}</option>
                                        <option value="I60.9">{{ __('health_records_edit.i60_9_subarachnoid_hemorrhage_unspecifie') }}</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <!-- Doping Control & AUT -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="loinc_doping_panel" class="block text-sm font-medium text-gray-700 mb-2">
                                    🧪 {{ __('health_records_edit.anti_doping_control_loinc_11556_8_11557_') }}
                                </label>
                                <select 
                                    id="loinc_doping_panel" 
                                    name="loinc_doping_panel"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    onchange="updateDopingResultFields()"
                                >
                                    <option value="">{{ __('health_records_edit.select_an_anti_doping_test') }}</option>
                                    <optgroup label="{{ __('health_records_edit.loinc_11556_8_prohibited_substances_pane') }}">
                                        <option value="11556-8">{{ __('health_records_edit.k_11556_8_prohibited_substances_panel_urin') }}</option>
                                        <option value="11557-6">{{ __('health_records_edit.k_11557_6_prohibited_substances_panel_bloo') }}</option>
                                        <option value="11558-4">{{ __('health_records_edit.k_11558_4_prohibited_substances_panel_sali') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.anabolic_steroids_loinc_11559_2') }}">
                                        <option value="LOINC_11559-2_TEST">{{ __('health_records_edit.testosterone_t_e_ratio') }}</option>
                                        <option value="LOINC_11559-2_NAND">Nandrolone (19-NA)</option>
                                        <option value="LOINC_11559-2_STAN">Stanozolol</option>
                                        <option value="LOINC_11559-2_METH">{{ __('health_records_edit.methandienone') }}</option>
                                        <option value="LOINC_11559-2_DECA">{{ __('health_records_edit.nandrolone_decanoate') }}</option>
                                        <option value="LOINC_11559-2_BOLD">{{ __('health_records_edit.boldenone') }}</option>
                                        <option value="LOINC_11559-2_TREN">Trenbolone</option>
                                        <option value="LOINC_11559-2_OXAN">Oxandrolone</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.peptide_hormones_loinc_11560_0') }}">
                                        <option value="LOINC_11560-0_GH">{{ __('health_records_edit.growth_hormone_gh') }}</option>
                                        <option value="LOINC_11560-0_IGF">IGF-1 (Insulin-like Growth Factor)</option>
                                        <option value="LOINC_11560-0_EPO">{{ __('health_records_edit.erythropoietin_epo') }}</option>
                                        <option value="LOINC_11560-0_HCG">{{ __('health_records_edit.human_chorionic_gonadotropin_hcg') }}</option>
                                        <option value="LOINC_11560-0_LH">{{ __('health_records_edit.luteinizing_hormone_lh') }}</option>
                                        <option value="LOINC_11560-0_FSH">{{ __('health_records_edit.follicle_stimulating_hormone_fsh') }}</option>
                                        <option value="LOINC_11560-0_ACTH">{{ __('health_records_edit.acth_adrenocorticotropic_hormone') }}</option>
                                        <option value="LOINC_11560-0_TSH">{{ __('health_records_edit.tsh_thyroid_stimulating_hormone') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.beta_2_agonists_loinc_11561_8') }}">
                                        <option value="LOINC_11561-8_SALB">Salbutamol</option>
                                        <option value="LOINC_11561-8_TERB">Terbutaline</option>
                                        <option value="LOINC_11561-8_FORM">{{ __('health_records_edit.formoterol') }}</option>
                                        <option value="LOINC_11561-8_SALM">{{ __('health_records_edit.salmeterol') }}</option>
                                        <option value="LOINC_11561-8_CLEN">{{ __('health_records_edit.clenbuterol') }}</option>
                                        <option value="LOINC_11561-8_FENO">{{ __('health_records_edit.fenoterol') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.diuretics_loinc_11562_6') }}">
                                        <option value="LOINC_11562-6_FURO">{{ __('health_records_edit.furosemide') }}</option>
                                        <option value="LOINC_11562-6_HCTZ">Hydrochlorothiazide</option>
                                        <option value="LOINC_11562-6_SPIR">Spironolactone</option>
                                        <option value="LOINC_11562-6_AMIL">Amiloride</option>
                                        <option value="LOINC_11562-6_TRIAM">{{ __('health_records_edit.triamterene') }}</option>
                                        <option value="LOINC_11562-6_CHLOR">Chlortalidone</option>
                                    </optgroup>
                                    <optgroup label="Stimulants (LOINC 11564-2)">
                                        <option value="LOINC_11564-2_AMPH">{{ __('health_records_edit.amphetamines') }}</option>
                                        <option value="LOINC_11564-2_METH">{{ __('health_records_edit.methamphetamine') }}</option>
                                        <option value="LOINC_11564-2_EPHE">{{ __('health_records_edit.ephedrine') }}</option>
                                        <option value="LOINC_11564-2_PSEU">{{ __('health_records_edit.pseudoephedrine') }}</option>
                                        <option value="LOINC_11564-2_COCA">{{ __('health_records_edit.cocaine') }}</option>
                                        <option value="LOINC_11564-2_METHY">{{ __('health_records_edit.methylphenidate') }}</option>
                                        <option value="LOINC_11564-2_MODAF">Modafinil</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.cannabinoids_loinc_11566_7') }}">
                                        <option value="LOINC_11566-7_THC">{{ __('health_records_edit.thc_tetrahydrocannabinol') }}</option>
                                        <option value="LOINC_11566-7_CBD">CBD (Cannabidiol)</option>
                                        <option value="LOINC_11566-7_CBN">CBN (Cannabinol)</option>
                                        <option value="LOINC_11566-7_METAB">{{ __('health_records_edit.thc_metabolites') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.glucocorticoids_loinc_11567_5') }}">
                                        <option value="LOINC_11567-5_PRED">Prednisone</option>
                                        <option value="LOINC_11567-5_DEXA">{{ __('health_records_edit.dexamethasone') }}</option>
                                        <option value="LOINC_11567-5_HYDRO">Hydrocortisone</option>
                                        <option value="LOINC_11567-5_METHY">{{ __('health_records_edit.methylprednisolone') }}</option>
                                        <option value="LOINC_11567-5_TRIAM">Triamcinolone</option>
                                        <option value="LOINC_11567-5_BETAM">{{ __('health_records_edit.betamethasone') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.detection_methods') }}">
                                        <option value="LOINC_11568-3">{{ __('health_records_edit.k_11568_3_gas_chromatography_gc') }}</option>
                                        <option value="LOINC_11569-1">{{ __('health_records_edit.k_11569_1_mass_spectrometry_ms') }}</option>
                                        <option value="LOINC_11570-9">{{ __('health_records_edit.k_11570_9_immunoassay_ia') }}</option>
                                        <option value="LOINC_11571-7">{{ __('health_records_edit.k_11571_7_elisa_test') }}</option>
                                        <option value="LOINC_11572-5">{{ __('health_records_edit.k_11572_5_liquid_chromatography_lc') }}</option>
                                        <option value="LOINC_11573-3">11573-3 - LC-MS/MS</option>
                                        <option value="LOINC_11574-1">11574-1 - GC-MS</option>
                                    </optgroup>
                                </select>
                                
                                <!-- Résultats des tests anti-dopage -->
                                <div id="doping-result-fields" class="mt-4 space-y-3" style="display: none;">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label for="doping_result_value" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ __('health_records_edit.measured_value') }}
                                            </label>
                                            <input 
                                                type="text" 
                                                id="doping_result_value" 
                                                name="doping_result_value"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                placeholder="{{ __('health_records_edit.value') }}"
                                            >
                                        </div>
                                        <div>
                                            <label for="doping_result_unit" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ __('health_records_edit.unit') }}
                                            </label>
                                            <input 
                                                type="text" 
                                                id="doping_result_unit" 
                                                name="doping_result_unit"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                placeholder="ng/mL, pg/mL..."
                                                readonly
                                            >
                                        </div>
                                        <div>
                                            <label for="doping_result_status" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ __('health_records_edit.status') }}
                                            </label>
                                            <select 
                                                id="doping_result_status" 
                                                name="doping_result_status"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                            >
                                                <option value="">{{ __('health_records_edit.select_2') }}</option>
                                                <option value="NEGATIVE">{{ __('health_records_edit.negative') }}</option>
                                                <option value="POSITIVE">{{ __('health_records_edit.positive') }}</option>
                                                <option value="SUSPICIOUS">{{ __('health_records_edit.suspicious') }}</option>
                                                <option value="INCONCLUSIVE">{{ __('health_records_edit.inconclusive') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label for="doping_normal_range" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ __('health_records_edit.normal_range') }}
                                            </label>
                                            <input 
                                                type="text" 
                                                id="doping_normal_range" 
                                                name="doping_normal_range"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                placeholder="{{ __('health_records_edit.normal_values') }}"
                                                readonly
                                            >
                                        </div>
                                        <div>
                                            <label for="doping_threshold" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ __('health_records_edit.wada_threshold') }}
                                            </label>
                                            <input 
                                                type="text" 
                                                id="doping_threshold" 
                                                name="doping_threshold"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                placeholder="{{ __('health_records_edit.anti_doping_threshold') }}"
                                                readonly
                                            >
                                        </div>
                                    </div>
                                    <div>
                                        <label for="doping_interpretation" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ __('health_records_edit.interpretation') }}
                                        </label>
                                        <textarea 
                                            id="doping_interpretation" 
                                            name="doping_interpretation"
                                            rows="2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                            placeholder="{{ __('health_records_edit.comments_on_the_results') }}"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="snomed_ct_aut" class="block text-sm font-medium text-gray-700 mb-2">
                                    💊 {{ __('health_records_edit.tue_therapeutic_use_exemption_snomed_ct_') }}
                                </label>
                                <select 
                                    id="snomed_ct_aut" 
                                    name="snomed_ct_aut"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    onchange="updateAUTFields()"
                                >
                                    <option value="">{{ __('health_records_edit.select_a_tue_type') }}</option>
                                    <optgroup label="{{ __('health_records_edit.snomed_ct_416940007_therapeutic_use_exem') }}">
                                        <option value="416940007">{{ __('health_records_edit.k_416940007_general_tue') }}</option>
                                        <option value="416940008">{{ __('health_records_edit.k_416940008_tue_for_stimulants') }}</option>
                                        <option value="416940009">{{ __('health_records_edit.k_416940009_tue_for_anabolic_agents') }}</option>
                                        <option value="416940010">{{ __('health_records_edit.k_416940010_tue_for_diuretics') }}</option>
                                        <option value="416940011">{{ __('health_records_edit.k_416940011_tue_for_beta_blockers') }}</option>
                                        <option value="416940012">{{ __('health_records_edit.k_416940012_tue_for_peptide_hormones') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.specific_substances') }}">
                                        <option value="AUT_ADHD">{{ __('health_records_edit.attention_deficit_disorder_adhd') }}</option>
                                        <option value="AUT_ASTHMA">{{ __('health_records_edit.asthma_and_respiratory_disorders') }}</option>
                                        <option value="AUT_DIABETES">{{ __('health_records_edit.diabetes_and_metabolic_disorders') }}</option>
                                        <option value="AUT_CARDIO">{{ __('health_records_edit.cardiovascular_disorders') }}</option>
                                        <option value="AUT_PSYCH">{{ __('health_records_edit.psychiatric_disorders') }}</option>
                                        <option value="AUT_ENDOCRINE">{{ __('health_records_edit.endocrine_disorders') }}</option>
                                        <option value="AUT_NEURO">{{ __('health_records_edit.neurological_disorders') }}</option>
                                        <option value="AUT_DERMATO">{{ __('health_records_edit.dermatological_disorders') }}</option>
                                        <option value="AUT_GASTRO">{{ __('health_records_edit.gastrointestinal_disorders') }}</option>
                                        <option value="AUT_RHEUMATO">{{ __('health_records_edit.rheumatological_disorders') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.tue_statuses') }}">
                                        <option value="AUT_PENDING">{{ __('health_records_edit.tue_pending') }}</option>
                                        <option value="AUT_APPROVED">{{ __('health_records_edit.tue_approved') }}</option>
                                        <option value="AUT_REJECTED">{{ __('health_records_edit.tue_rejected') }}</option>
                                        <option value="AUT_EXPIRED">{{ __('health_records_edit.tue_expired') }}</option>
                                        <option value="AUT_REVOKED">{{ __('health_records_edit.tue_revoked') }}</option>
                                    </optgroup>
                                </select>

                                <!-- AUT Details Section -->
                                <div id="aut-details-section" class="mt-4 space-y-4" style="display: none;">
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                                            <span class="mr-2">📋</span>
                                            {{ __('health_records_edit.tue_details') }}
                                        </h4>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label for="aut_substance" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.drug_substance') }}
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="aut_substance" 
                                                    name="aut_substance"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="{{ __('health_records_edit.substance_name') }}"
                                                >
                                            </div>
                                            <div>
                                                <label for="aut_dosage" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.prescribed_dosage') }}
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="aut_dosage" 
                                                    name="aut_dosage"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="{{ __('health_records_edit.dosage_and_frequency') }}"
                                                >
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label for="aut_diagnosis" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.medical_diagnosis') }}
                                                </label>
                                                <textarea 
                                                    id="aut_diagnosis" 
                                                    name="aut_diagnosis"
                                                    rows="2"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="{{ __('health_records_edit.diagnosis_justifying_the_tue') }}"
                                                ></textarea>
                                            </div>
                                            <div>
                                                <label for="aut_justification" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.medical_justification') }}
                                                </label>
                                                <textarea 
                                                    id="aut_justification" 
                                                    name="aut_justification"
                                                    rows="2"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="{{ __('health_records_edit.justification_for_therapeutic_use') }}"
                                                ></textarea>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                            <div>
                                                <label for="aut_start_date" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.start_date') }}
                                                </label>
                                                <input 
                                                    type="date" 
                                                    id="aut_start_date" 
                                                    name="aut_start_date"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                >
                                            </div>
                                            <div>
                                                <label for="aut_end_date" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.end_date') }}
                                                </label>
                                                <input 
                                                    type="date" 
                                                    id="aut_end_date" 
                                                    name="aut_end_date"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                >
                                            </div>
                                            <div>
                                                <label for="aut_status" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.tue_status') }}
                                                </label>
                                                <select 
                                                    id="aut_status" 
                                                    name="aut_status"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                >
                                                    <option value="">{{ __('health_records_edit.select_2') }}</option>
                                                    <option value="pending">{{ __('health_records_edit.pending') }}</option>
                                                    <option value="approved">{{ __('health_records_edit.approved') }}</option>
                                                    <option value="rejected">{{ __('health_records_edit.rejected') }}</option>
                                                    <option value="expired">{{ __('health_records_edit.expired') }}</option>
                                                    <option value="revoked">{{ __('health_records_edit.revoked') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- AUT File Upload Section -->
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-green-800 mb-3 flex items-center">
                                            <span class="mr-2">📄</span>
                                            {{ __('health_records_edit.tue_documents') }}
                                        </h4>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="aut_application_form" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.tue_application_form') }}
                                                </label>
                                                <div class="flex items-center space-x-2">
                                                    <input 
                                                        type="file" 
                                                        id="aut_application_form" 
                                                        name="aut_application_form"
                                                        accept=".pdf,.doc,.docx"
                                                        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500"
                                                    >
                                                    <button 
                                                        type="button"
                                                        onclick="downloadIAAFForm()"
                                                        class="px-3 py-2 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    >
                                                        📋 IAAF
                                                    </button>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ __('health_records_edit.iaaf_tue_form') }}
                                                </p>
                                            </div>
                                            <div>
                                                <label for="aut_response_document" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.official_response') }}
                                                </label>
                                                <input 
                                                    type="file" 
                                                    id="aut_response_document" 
                                                    name="aut_response_document"
                                                    accept=".pdf,.doc,.docx"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500"
                                                >
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ __('health_records_edit.authority_response_document') }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <label for="aut_notes" class="block text-xs font-medium text-gray-600 mb-1">
                                                {{ __('health_records_edit.notes_and_observations') }}
                                            </label>
                                            <textarea 
                                                id="aut_notes" 
                                                name="aut_notes"
                                                rows="3"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500"
                                                placeholder="{{ __('health_records_edit.additional_notes_on_the_tue') }}"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Blood Tests & Biological Profile -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="blood_test_panel" class="block text-sm font-medium text-gray-700 mb-2">
                                    🩸 {{ __('health_records_edit.blood_tests_loinc_58410_2_58409_4_58408_') }}
                                </label>
                                <select 
                                    id="blood_test_panel" 
                                    name="blood_test_panel"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    onchange="updateBloodTestResultFields()"
                                >
                                    <option value="">{{ __('health_records_edit.select_a_blood_test') }}</option>
                                    <optgroup label="{{ __('health_records_edit.loinc_58410_2_comprehensive_metabolic_pa') }}">
                                        <option value="58410-2">{{ __('health_records_edit.k_58410_2_comprehensive_metabolic_panel_cb') }}</option>
                                        <option value="58409-4">{{ __('health_records_edit.k_58409_4_basic_metabolic_panel_biochemist') }}</option>
                                        <option value="58408-6">{{ __('health_records_edit.k_58408_6_extended_metabolic_panel_complet') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.hematology') }}">
                                        <option value="LOINC_58410-2_CBC">{{ __('health_records_edit.complete_blood_count_cbc') }}</option>
                                        <option value="LOINC_58410-2_HGB">{{ __('health_records_edit.hemoglobin_hgb') }}</option>
                                        <option value="LOINC_58410-2_HCT">{{ __('health_records_edit.hematocrit_hct') }}</option>
                                        <option value="LOINC_58410-2_RBC">{{ __('health_records_edit.red_blood_cells_rbc') }}</option>
                                        <option value="LOINC_58410-2_WBC">{{ __('health_records_edit.white_blood_cells_wbc') }}</option>
                                        <option value="LOINC_58410-2_PLT">{{ __('health_records_edit.platelets_plt') }}</option>
                                        <option value="LOINC_58410-2_MCV">{{ __('health_records_edit.mean_corpuscular_volume_mcv') }}</option>
                                        <option value="LOINC_58410-2_MCH">{{ __('health_records_edit.mean_corpuscular_hemoglobin_mch') }}</option>
                                        <option value="LOINC_58410-2_MCHC">{{ __('health_records_edit.mean_corpuscular_hemoglobin_concentratio') }}</option>
                                        <option value="LOINC_58410-2_RDW">{{ __('health_records_edit.red_cell_distribution_width_rdw') }}</option>
                                        <option value="LOINC_58410-2_MPV">{{ __('health_records_edit.mean_platelet_volume_mpv') }}</option>
                                        <option value="LOINC_58410-2_NEUT">{{ __('health_records_edit.neutrophils') }}</option>
                                        <option value="LOINC_58410-2_LYMPH">Lymphocytes</option>
                                        <option value="LOINC_58410-2_MONO">Monocytes</option>
                                        <option value="LOINC_58410-2_EOS">{{ __('health_records_edit.eosinophils') }}</option>
                                        <option value="LOINC_58410-2_BASO">{{ __('health_records_edit.basophils') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.biochemistry') }}">
                                        <option value="LOINC_58409-4_GLU">Glucose</option>
                                        <option value="LOINC_58409-4_CREA">{{ __('health_records_edit.creatinine') }}</option>
                                        <option value="LOINC_58409-4_BUN">{{ __('health_records_edit.blood_urea_nitrogen_bun') }}</option>
                                        <option value="LOINC_58409-4_NA">Sodium (Na)</option>
                                        <option value="LOINC_58409-4_K">Potassium (K)</option>
                                        <option value="LOINC_58409-4_CL">{{ __('health_records_edit.chloride_cl') }}</option>
                                        <option value="LOINC_58409-4_CO2">{{ __('health_records_edit.total_co2') }}</option>
                                        <option value="LOINC_58409-4_CA">Calcium (Ca)</option>
                                        <option value="LOINC_58409-4_PHOS">{{ __('health_records_edit.phosphorus') }}</option>
                                        <option value="LOINC_58409-4_MG">{{ __('health_records_edit.magnesium_mg') }}</option>
                                        <option value="LOINC_58409-4_UA">{{ __('health_records_edit.uric_acid') }}</option>
                                        <option value="LOINC_58409-4_LDH">{{ __('health_records_edit.lactate_dehydrogenase_ldh') }}</option>
                                        <option value="LOINC_58409-4_CPK">{{ __('health_records_edit.creatine_phosphokinase_cpk') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.lipids') }}">
                                        <option value="LOINC_58408-6_CHOL">{{ __('health_records_edit.total_cholesterol') }}</option>
                                        <option value="LOINC_58408-6_HDL">{{ __('health_records_edit.hdl_cholesterol') }}</option>
                                        <option value="LOINC_58408-6_LDL">{{ __('health_records_edit.ldl_cholesterol') }}</option>
                                        <option value="LOINC_58408-6_TRIG">{{ __('health_records_edit.triglycerides') }}</option>
                                        <option value="LOINC_58408-6_APOA">{{ __('health_records_edit.apolipoprotein_a') }}</option>
                                        <option value="LOINC_58408-6_APOB">{{ __('health_records_edit.apolipoprotein_b') }}</option>
                                        <option value="LOINC_58408-6_LP">{{ __('health_records_edit.lipoprotein_a') }}</option>
                                        <option value="LOINC_58408-6_NONHDL">{{ __('health_records_edit.non_hdl_cholesterol') }}</option>
                                        <option value="LOINC_58408-6_RATIO">{{ __('health_records_edit.total_cholesterol_hdl_ratio') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.liver_enzymes') }}">
                                        <option value="LOINC_58408-6_ALT">{{ __('health_records_edit.alanine_aminotransferase_alt') }}</option>
                                        <option value="LOINC_58408-6_AST">{{ __('health_records_edit.aspartate_aminotransferase_ast') }}</option>
                                        <option value="LOINC_58408-6_ALP">{{ __('health_records_edit.alkaline_phosphatase_alp') }}</option>
                                        <option value="LOINC_58408-6_GGT">{{ __('health_records_edit.gamma_glutamyl_transferase_ggt') }}</option>
                                        <option value="LOINC_58408-6_TBIL">{{ __('health_records_edit.total_bilirubin') }}</option>
                                        <option value="LOINC_58408-6_DBIL">{{ __('health_records_edit.direct_bilirubin') }}</option>
                                        <option value="LOINC_58408-6_IBIL">{{ __('health_records_edit.indirect_bilirubin') }}</option>
                                        <option value="LOINC_58408-6_ALB">{{ __('health_records_edit.albumin') }}</option>
                                        <option value="LOINC_58408-6_TP">{{ __('health_records_edit.total_protein') }}</option>
                                        <option value="LOINC_58408-6_GLOB">{{ __('health_records_edit.globulins') }}</option>
                                        <option value="LOINC_58408-6_AG">{{ __('health_records_edit.albumin_globulin_ratio') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.cardiac_markers') }}">
                                        <option value="LOINC_58408-6_TROP">{{ __('health_records_edit.troponin') }}</option>
                                        <option value="LOINC_58408-6_CK">{{ __('health_records_edit.creatine_kinase_ck') }}</option>
                                        <option value="LOINC_58408-6_CKMB">{{ __('health_records_edit.creatine_kinase_mb_ck_mb') }}</option>
                                        <option value="LOINC_58408-6_BNP">{{ __('health_records_edit.b_type_natriuretic_peptide_bnp') }}</option>
                                        <option value="LOINC_58408-6_NT">NT-proBNP</option>
                                        <option value="LOINC_58408-6_CRP">{{ __('health_records_edit.c_reactive_protein_crp') }}</option>
                                        <option value="LOINC_58408-6_ESR">{{ __('health_records_edit.erythrocyte_sedimentation_rate_esr') }}</option>
                                        <option value="LOINC_58408-6_HSCRP">{{ __('health_records_edit.high_sensitivity_crp_hs_crp') }}</option>
                                        <option value="LOINC_58408-6_LPA">{{ __('health_records_edit.lipoprotein_a') }}</option>
                                        <option value="LOINC_58408-6_HOMOC">{{ __('health_records_edit.homocysteine') }}</option>
                                    </optgroup>
                                    <optgroup label="Hormones">
                                        <option value="LOINC_58408-6_TSH">{{ __('health_records_edit.tsh_thyroid_stimulating_hormone') }}</option>
                                        <option value="LOINC_58408-6_T4">{{ __('health_records_edit.free_t4') }}</option>
                                        <option value="LOINC_58408-6_T3">{{ __('health_records_edit.free_t3') }}</option>
                                        <option value="LOINC_58408-6_CORT">Cortisol</option>
                                        <option value="LOINC_58408-6_INSU">{{ __('health_records_edit.insulin') }}</option>
                                        <option value="LOINC_58408-6_HBA1C">{{ __('health_records_edit.glycated_hemoglobin_hba1c') }}</option>
                                        <option value="LOINC_58408-6_VITD">{{ __('health_records_edit.vitamin_d') }}</option>
                                        <option value="LOINC_58408-6_FOL">{{ __('health_records_edit.folic_acid') }}</option>
                                        <option value="LOINC_58408-6_B12">{{ __('health_records_edit.vitamin_b12') }}</option>
                                        <option value="LOINC_58408-6_TEST">{{ __('health_records_edit.testosterone') }}</option>
                                        <option value="LOINC_58408-6_EST">Estradiol</option>
                                        <option value="LOINC_58408-6_PROG">{{ __('health_records_edit.progesterone') }}</option>
                                        <option value="LOINC_58408-6_FSH">FSH</option>
                                        <option value="LOINC_58408-6_LH">LH</option>
                                        <option value="LOINC_58408-6_PROL">{{ __('health_records_edit.prolactin') }}</option>
                                        <option value="LOINC_58408-6_GH">{{ __('health_records_edit.growth_hormone_gh') }}</option>
                                        <option value="LOINC_58408-6_IGF">IGF-1</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.inflammatory_markers') }}">
                                        <option value="LOINC_58408-6_IL6">{{ __('health_records_edit.interleukin_6_il_6') }}</option>
                                        <option value="LOINC_58408-6_TNF">{{ __('health_records_edit.tumor_necrosis_factor_alpha_tnf') }}</option>
                                        <option value="LOINC_58408-6_FER">{{ __('health_records_edit.ferritin') }}</option>
                                        <option value="LOINC_58408-6_IRON">{{ __('health_records_edit.serum_iron') }}</option>
                                        <option value="LOINC_58408-6_TIBC">{{ __('health_records_edit.total_iron_binding_capacity_tibc') }}</option>
                                        <option value="LOINC_58408-6_UIBC">{{ __('health_records_edit.unsaturated_iron_binding_capacity_uibc') }}</option>
                                        <option value="LOINC_58408-6_SAT">{{ __('health_records_edit.iron_saturation') }}</option>
                                        <option value="LOINC_58408-6_TRANS">{{ __('health_records_edit.transferrin') }}</option>
                                        <option value="LOINC_58408-6_CERUL">{{ __('health_records_edit.ceruloplasmin') }}</option>
                                        <option value="LOINC_58408-6_COPPER">{{ __('health_records_edit.copper') }}</option>
                                        <option value="LOINC_58408-6_ZINC">Zinc</option>
                                        <option value="LOINC_58408-6_SELEN">{{ __('health_records_edit.selenium') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.tumor_markers') }}">
                                        <option value="LOINC_58408-6_PSA">{{ __('health_records_edit.prostate_specific_antigen_psa') }}</option>
                                        <option value="LOINC_58408-6_CEA">{{ __('health_records_edit.carcinoembryonic_antigen_cea') }}</option>
                                        <option value="LOINC_58408-6_AFP">{{ __('health_records_edit.alpha_fetoprotein_afp') }}</option>
                                        <option value="LOINC_58408-6_CA125">CA 125</option>
                                        <option value="LOINC_58408-6_CA199">CA 19-9</option>
                                        <option value="LOINC_58408-6_CA153">CA 15-3</option>
                                        <option value="LOINC_58408-6_CA724">CA 72-4</option>
                                        <option value="LOINC_58408-6_SCC">{{ __('health_records_edit.squamous_cell_carcinoma_antigen_scc') }}</option>
                                        <option value="LOINC_58408-6_NSE">{{ __('health_records_edit.neuron_specific_enolase_nse') }}</option>
                                        <option value="LOINC_58408-6_CYFRA">CYFRA 21-1</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.autoimmunity_markers') }}">
                                        <option value="LOINC_58408-6_ANA">{{ __('health_records_edit.antinuclear_antibodies_ana') }}</option>
                                        <option value="LOINC_58408-6_RF">{{ __('health_records_edit.rheumatoid_factor_rf') }}</option>
                                        <option value="LOINC_58408-6_CCP">{{ __('health_records_edit.cyclic_citrullinated_peptide_ccp') }}</option>
                                        <option value="LOINC_58408-6_DSDNA">{{ __('health_records_edit.anti_double_stranded_dna_antibodies') }}</option>
                                        <option value="LOINC_58408-6_SM">{{ __('health_records_edit.anti_sm_antibodies') }}</option>
                                        <option value="LOINC_58408-6_RO">{{ __('health_records_edit.anti_ro_ssa_antibodies') }}</option>
                                        <option value="LOINC_58408-6_LA">{{ __('health_records_edit.anti_la_ssb_antibodies') }}</option>
                                        <option value="LOINC_58408-6_ANCA">{{ __('health_records_edit.anti_neutrophil_cytoplasmic_antibodies_a') }}</option>
                                        <option value="LOINC_58408-6_ASMA">{{ __('health_records_edit.anti_smooth_muscle_antibodies_asma') }}</option>
                                        <option value="LOINC_58408-6_AMA">{{ __('health_records_edit.anti_mitochondrial_antibodies_ama') }}</option>
                                    </optgroup>
                                </select>
                                
                                <!-- Résultats des tests sanguins -->
                                <div id="blood-test-result-fields" class="mt-4 space-y-3" style="display: none;">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label for="blood_test_result_value" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ __('health_records_edit.measured_value') }}
                                            </label>
                                            <input 
                                                type="text" 
                                                id="blood_test_result_value" 
                                                name="blood_test_result_value"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                placeholder="{{ __('health_records_edit.value') }}"
                                            >
                                        </div>
                                        <div>
                                            <label for="blood_test_result_unit" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ __('health_records_edit.unit') }}
                                            </label>
                                            <input 
                                                type="text" 
                                                id="blood_test_result_unit" 
                                                name="blood_test_result_unit"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                placeholder="g/dL, mg/dL, μmol/L..."
                                                readonly
                                            >
                                        </div>
                                        <div>
                                            <label for="blood_test_result_status" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ __('health_records_edit.status') }}
                                            </label>
                                            <select 
                                                id="blood_test_result_status" 
                                                name="blood_test_result_status"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                            >
                                                <option value="">{{ __('health_records_edit.select_2') }}</option>
                                                <option value="NORMAL">Normal</option>
                                                <option value="LOW">{{ __('health_records_edit.low') }}</option>
                                                <option value="HIGH">{{ __('health_records_edit.high') }}</option>
                                                <option value="CRITICAL_LOW">{{ __('health_records_edit.critically_low') }}</option>
                                                <option value="CRITICAL_HIGH">{{ __('health_records_edit.critically_high') }}</option>
                                                <option value="ABNORMAL">{{ __('health_records_edit.abnormal') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label for="blood_test_normal_range" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ __('health_records_edit.normal_range') }}
                                            </label>
                                            <input 
                                                type="text" 
                                                id="blood_test_normal_range" 
                                                name="blood_test_normal_range"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                placeholder="{{ __('health_records_edit.normal_values') }}"
                                                readonly
                                            >
                                        </div>
                                        <div>
                                            <label for="blood_test_reference" class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ __('health_records_edit.reference_2') }}
                                            </label>
                                            <input 
                                                type="text" 
                                                id="blood_test_reference" 
                                                name="blood_test_reference"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                placeholder="{{ __('health_records_edit.reference') }}"
                                                readonly
                                            >
                                        </div>
                                    </div>
                                    <div>
                                        <label for="blood_test_interpretation" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ __('health_records_edit.clinical_interpretation') }}
                                        </label>
                                        <textarea 
                                            id="blood_test_interpretation" 
                                            name="blood_test_interpretation"
                                            rows="2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                            placeholder="{{ __('health_records_edit.interpretation_of_results') }}"
                                        ></textarea>
                                    </div>
                                </div>

                                <!-- Dynamic Panel Tests Cards -->
                                <div id="panel-tests-container" class="mt-6 space-y-4" style="display: none;">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                        <span class="mr-2">📊</span>
                                        {{ __('health_records_edit.selected_panel_tests') }}
                                    </h4>
                                    <div id="panel-tests-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <!-- Dynamic test cards will be inserted here -->
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="snomed_ct_biological_profile" class="block text-sm font-medium text-gray-700 mb-2">
                                    🧬 {{ __('health_records_edit.biological_profile_snomed_ct_363787002') }}
                                </label>
                                <select 
                                    id="snomed_ct_biological_profile" 
                                    name="snomed_ct_biological_profile"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                >
                                    <option value="">{{ __('health_records_edit.select_a_biological_profile') }}</option>
                                    <optgroup label="{{ __('health_records_edit.snomed_ct_363787002_biological_profile') }}">
                                        <option value="363787002">{{ __('health_records_edit.k_363787002_complete_biological_profile') }}</option>
                                        <option value="363787003">{{ __('health_records_edit.k_363787003_metabolic_profile') }}</option>
                                        <option value="363787004">{{ __('health_records_edit.k_363787004_hormonal_profile') }}</option>
                                        <option value="363787005">{{ __('health_records_edit.k_363787005_inflammatory_profile') }}</option>
                                        <option value="363787006">{{ __('health_records_edit.k_363787006_oxidative_profile') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.metabolic_markers') }}">
                                        <option value="SNOMED_363787007">{{ __('health_records_edit.k_363787007_oxidative_stress_markers') }}</option>
                                        <option value="SNOMED_363787008">{{ __('health_records_edit.k_363787008_inflammatory_markers') }}</option>
                                        <option value="SNOMED_363787009">{{ __('health_records_edit.k_363787009_hormonal_markers') }}</option>
                                        <option value="SNOMED_363787010">{{ __('health_records_edit.k_363787010_aging_markers') }}</option>
                                        <option value="SNOMED_363787011">{{ __('health_records_edit.k_363787011_performance_markers') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.biological_age') }}">
                                        <option value="BIOLOGICAL_AGE_NORMAL">{{ __('health_records_edit.normal_biological_age') }}</option>
                                        <option value="BIOLOGICAL_AGE_YOUNGER">{{ __('health_records_edit.lower_biological_age') }}</option>
                                        <option value="BIOLOGICAL_AGE_OLDER">{{ __('health_records_edit.higher_biological_age') }}</option>
                                        <option value="BIOLOGICAL_AGE_ACCELERATED">{{ __('health_records_edit.accelerated_aging') }}</option>
                                    </optgroup>
                                    <optgroup label="{{ __('health_records_edit.specific_profiles') }}">
                                        <option value="PROFILE_ATHLETE">{{ __('health_records_edit.athlete_profile') }}</option>
                                        <option value="PROFILE_ELITE">{{ __('health_records_edit.elite_profile') }}</option>
                                        <option value="PROFILE_RECOVERY">{{ __('health_records_edit.recovery_profile') }}</option>
                                        <option value="PROFILE_MONITORING">{{ __('health_records_edit.monitoring_profile') }}</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <!-- Dental Health Section -->
                        <div class="mb-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                                    <span class="mr-2">🦷</span>
                                    {{ __('health_records_edit.dental_health_icd_10_k00_k14') }}
                                </h3>
                                
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <label for="icd_10_dental" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_edit.dental_diagnosis') }}
                                        </label>
                                        <select 
                                            id="icd_10_dental" 
                                            name="icd_10_dental"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">{{ __('health_records_edit.select_a_dental_diagnosis') }}</option>
                                            <optgroup label="{{ __('health_records_edit.icd_10_k00_k14_diseases_of_the_oral_cavi') }}">
                                                <option value="K00.0">{{ __('health_records_edit.k00_0_anodontia') }}</option>
                                                <option value="K00.1">{{ __('health_records_edit.k00_1_supernumerary_teeth') }}</option>
                                                <option value="K00.2">{{ __('health_records_edit.k00_2_abnormalities_of_size_and_form_of_') }}</option>
                                                <option value="K00.3">{{ __('health_records_edit.k00_3_mottled_teeth') }}</option>
                                                <option value="K00.4">{{ __('health_records_edit.k00_4_disturbances_in_tooth_formation') }}</option>
                                                <option value="K00.5">{{ __('health_records_edit.k00_5_hereditary_disturbances_in_tooth_s') }}</option>
                                                <option value="K00.6">{{ __('health_records_edit.k00_6_disturbances_in_tooth_eruption') }}</option>
                                                <option value="K00.7">{{ __('health_records_edit.k00_7_teething_syndrome') }}</option>
                                                <option value="K00.8">{{ __('health_records_edit.k00_8_other_disorders_of_tooth_developme') }}</option>
                                                <option value="K00.9">{{ __('health_records_edit.k00_9_disorder_of_tooth_development_unsp') }}</option>
                                            </optgroup>
                                            <optgroup label="{{ __('health_records_edit.caries_and_pulpal_diseases') }}">
                                                <option value="K02.0">{{ __('health_records_edit.k02_0_caries_limited_to_enamel') }}</option>
                                                <option value="K02.1">{{ __('health_records_edit.k02_1_caries_of_dentine') }}</option>
                                                <option value="K02.2">{{ __('health_records_edit.k02_2_caries_of_cementum') }}</option>
                                                <option value="K02.3">{{ __('health_records_edit.k02_3_arrested_dental_caries') }}</option>
                                                <option value="K02.4">{{ __('health_records_edit.k02_4_odontoclasia') }}</option>
                                                <option value="K02.5">{{ __('health_records_edit.k02_5_caries_with_pulp_exposure') }}</option>
                                                <option value="K02.8">{{ __('health_records_edit.k02_8_other_dental_caries') }}</option>
                                                <option value="K02.9">{{ __('health_records_edit.k02_9_dental_caries_unspecified') }}</option>
                                            </optgroup>
                                            <optgroup label="{{ __('health_records_edit.pulpal_and_periapical_diseases') }}">
                                                <option value="K04.0">{{ __('health_records_edit.k04_0_pulpitis') }}</option>
                                                <option value="K04.1">{{ __('health_records_edit.k04_1_pulp_necrosis') }}</option>
                                                <option value="K04.2">{{ __('health_records_edit.k04_2_pulp_degeneration') }}</option>
                                                <option value="K04.3">{{ __('health_records_edit.k04_3_abnormal_hard_tissue_formation_in_') }}</option>
                                                <option value="K04.4">{{ __('health_records_edit.k04_4_acute_apical_periodontitis_of_pulp') }}</option>
                                                <option value="K04.5">{{ __('health_records_edit.k04_5_chronic_apical_periodontitis') }}</option>
                                                <option value="K04.6">{{ __('health_records_edit.k04_6_periapical_abscess_with_sinus') }}</option>
                                                <option value="K04.7">{{ __('health_records_edit.k04_7_periapical_abscess_without_sinus') }}</option>
                                                <option value="K04.8">{{ __('health_records_edit.k04_8_radicular_cyst') }}</option>
                                                <option value="K04.9">{{ __('health_records_edit.k04_9_other_diseases_of_pulp_and_periapi') }}</option>
                                            </optgroup>
                                            <optgroup label="{{ __('health_records_edit.gingival_and_periodontal_diseases') }}">
                                                <option value="K05.0">{{ __('health_records_edit.k05_0_acute_gingivitis') }}</option>
                                                <option value="K05.1">{{ __('health_records_edit.k05_1_chronic_gingivitis') }}</option>
                                                <option value="K05.2">{{ __('health_records_edit.k05_2_acute_periodontitis') }}</option>
                                                <option value="K05.3">{{ __('health_records_edit.k05_3_chronic_periodontitis') }}</option>
                                                <option value="K05.4">{{ __('health_records_edit.k05_4_periodontosis') }}</option>
                                                <option value="K05.5">{{ __('health_records_edit.k05_5_other_periodontal_diseases') }}</option>
                                                <option value="K05.6">{{ __('health_records_edit.k05_6_periodontal_disease_unspecified') }}</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="dental_health_status" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_edit.general_dental_health_status') }}
                                        </label>
                                        <select 
                                            id="dental_health_status" 
                                            name="dental_health_status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">{{ __('health_records_edit.select_status') }}</option>
                                            <option value="excellent">Excellent</option>
                                            <option value="good">{{ __('health_records_edit.good') }}</option>
                                            <option value="fair">{{ __('health_records_edit.average') }}</option>
                                            <option value="poor">{{ __('health_records_edit.poor') }}</option>
                                            <option value="critical">{{ __('health_records_edit.critical') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="dental_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('health_records_edit.dental_notes') }}
                                    </label>
                                    <textarea 
                                        id="dental_notes" 
                                        name="dental_notes"
                                        rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="{{ __('health_records_edit.dental_observations_ongoing_treatments_r') }}"
                                    >{{ old('dental_notes') }}</textarea>
                                </div>
                            </div>

                            <!-- Interactive Dental Chart -->
                            <div class="mt-4 bg-white border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <span class="mr-2">📊</span>
                                    {{ __('health_records_edit.interactive_dental_chart') }}
                                </h4>
                                
                                <div class="dental-chart-wrapper">
                                    <div class="dental-chart-container">
                                        <div class="chart-header">
                                            <div class="flex items-center space-x-4 text-sm mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-4 h-4 bg-white border border-gray-300 rounded"></div>
                                                    <span>{{ __('health_records_edit.healthy') }}</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                                                    <span>{{ __('health_records_edit.caries') }}</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-4 h-4 bg-blue-500 rounded"></div>
                                                    <span>{{ __('health_records_edit.restoration') }}</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-4 h-4 bg-gray-500 opacity-50 rounded"></div>
                                                    <span>{{ __('health_records_edit.missing') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="chart-wrapper" id="chartContainer">
                                            <div 
                                                class="dental-svg-container" 
                                                id="dental-svg-container"
                                            >
                                                <!-- Dental Chart SVG will be injected here -->
                                            </div>
                                        </div>

                                        <!-- Dental Tooltip -->
                                        <div 
                                            id="dental-tooltip"
                                            class="dental-tooltip"
                                            style="display: none;"
                                        >
                                            <div class="tooltip-content">
                                                <div class="font-semibold" id="tooltip-tooth-number">Dent 1</div>
                                                <div class="text-sm" id="tooltip-status">{{ __('health_records_edit.healthy_2') }}</div>
                                                <div class="text-xs text-gray-500" id="tooltip-condition">{{ __('health_records_edit.no_condition') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dental Status Summary -->
                                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                                    <h5 class="text-sm font-semibold text-gray-700 mb-3">{{ __('health_records_edit.dental_summary') }}</h5>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div class="flex items-center justify-between">
                                            <span>{{ __('health_records_edit.healthy_teeth') }}</span>
                                            <span class="font-semibold text-green-600" id="healthy-teeth-count">32</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>{{ __('health_records_edit.caries') }}s:</span>
                                            <span class="font-semibold text-red-600" id="caries-count">0</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>{{ __('health_records_edit.restorations') }}</span>
                                            <span class="font-semibold text-blue-600" id="restoration-count">0</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>{{ __('health_records_edit.missing_2') }}</span>
                                            <span class="font-semibold text-gray-600" id="missing-teeth-count">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Postural Assessment -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                🦴 {{ __('health_records_edit.interactive_postural_assessment_icd_10_m') }}
                            </label>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-3">
                                        {{ __('health_records_edit.use_the_interactive_tool_to_analyze_the_') }}
                                    </p>
                                    <ul class="text-sm text-gray-600 space-y-1 mb-4">
                                        <li>• <strong>🎯 {{ __('health_records_edit.marker') }} :</strong> {{ __('health_records_edit.click_on_an_anatomical_point_to_add_an_a') }}</li>
                                        <li>• <strong>{{ __('health_records_extra.label_c603506c14c1') }}</strong> {{ __('health_records_edit.click_on_3_points_to_measure_an_angle') }}</li>
                                        <li>• <strong>📏 {{ __('health_records_edit.plumb_line') }} :</strong> {{ __('health_records_edit.displays_a_vertical_reference_line') }}</li>
                                        <li>• <strong>🗑️ {{ __('health_records_edit.clear') }} :</strong> {{ __('health_records_edit.removes_all_annotations') }}</li>
                                        <li>• <strong>💾 {{ __('health_records_edit.export') }} :</strong> {{ __('health_records_edit.downloads_the_assessment_data_as_json') }}</li>
                                    </ul>
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                        <p class="text-sm text-yellow-800">
                                            <strong>{{ __('health_records_edit.tip_colon') }}</strong> {{ __('health_records_edit.postural_data_is_automatically_saved_in_') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Composant d'analyse posturale -->
                                <div id="postural-chart-container">
                                    <div class="postural-chart-container">
                                        <!-- Toolbar -->
                                        <div class="toolbar bg-white border-b border-gray-200 p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <!-- View Selector -->
                                                    <div class="flex items-center space-x-2">
                                                        <label class="text-sm font-medium text-gray-700">{{ __('health_records_edit.view') }}</label>
                                                        <select id="postural-view-selector" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                                            <option value="anterior">{{ __('health_records_edit.anterior') }}</option>
                                                            <option value="posterior">{{ __('health_records_edit.posterior') }}</option>
                                                            <option value="lateral">{{ __('health_records_edit.lateral') }}</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <!-- Tool Selector -->
                                                    <div class="flex items-center space-x-2">
                                                        <label class="text-sm font-medium text-gray-700">{{ __('health_records_edit.tool') }}</label>
                                                        <div class="flex space-x-1">
                                                            <button 
                                                                id="postural-marker-tool"
                                                                class="px-3 py-1 rounded text-sm bg-blue-500 text-white"
                                                                title="{{ __('health_records_edit.marker') }}"
                                                            >
                                                                🎯
                                                            </button>
                                                            <button 
                                                                id="postural-angle-tool"
                                                                class="px-3 py-1 rounded text-sm bg-gray-200 text-gray-700"
                                                                title="{{ __('health_records_edit.angle_measurement') }}"
                                                            >
                                                                📐
                                                            </button>
                                                            <button 
                                                                id="postural-plumb-tool"
                                                                class="px-3 py-1 rounded text-sm bg-gray-200 text-gray-700"
                                                                title="{{ __('health_records_edit.plumb_line') }}"
                                                            >
                                                                📏
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Color Palette -->
                                                    <div id="postural-color-palette" class="flex items-center space-x-2" style="display: none;">
                                                        <label class="text-sm font-medium text-gray-700">{{ __('health_records_edit.color') }}</label>
                                                        <div class="flex space-x-1">
                                                            <button class="w-6 h-6 rounded border-2 border-gray-800 bg-red-500" data-color="#ff0000"></button>
                                                            <button class="w-6 h-6 rounded border-2 border-gray-300 bg-green-500" data-color="#00ff00"></button>
                                                            <button class="w-6 h-6 rounded border-2 border-gray-300 bg-blue-500" data-color="#0000ff"></button>
                                                            <button class="w-6 h-6 rounded border-2 border-gray-300 bg-yellow-500" data-color="#ffff00"></button>
                                                            <button class="w-6 h-6 rounded border-2 border-gray-300 bg-magenta-500" data-color="#ff00ff"></button>
                                                            <button class="w-6 h-6 rounded border-2 border-gray-300 bg-cyan-500" data-color="#00ffff"></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Actions -->
                                                <div class="flex items-center space-x-2">
                                                    <button id="postural-clear-btn" class="px-3 py-1 bg-red-500 text-white rounded text-sm">
                                                        🗑️ {{ __('health_records_edit.clear') }}
                                                    </button>
                                                    <button id="postural-export-btn" class="px-3 py-1 bg-green-500 text-white rounded text-sm">
                                                        💾 {{ __('health_records_edit.export') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Chart Container -->
                                        <div class="chart-area bg-gray-50 p-4">
                                            <div class="flex flex-col lg:flex-row">
                                                <!-- SVG Chart -->
                                                <div class="svg-container relative mx-auto lg:mx-0" style="width: 600px; height: 800px; max-width: 100%; max-height: 80vh;">
                                                    <div id="postural-svg-content" class="cursor-crosshair w-full h-full">
                                                        <!-- SVG content will be loaded here -->
                                                    </div>
                                                    
                                                    <!-- Annotations Layer -->
                                                    <svg id="postural-annotations" class="absolute top-0 left-0 w-full h-full pointer-events-none" style="width: 600px; height: 800px; max-width: 100%; max-height: 80vh;">
                                                        <!-- Markers and angles will be added here -->
                                                    </svg>
                                                </div>
                                                
                                                <!-- Sidebar -->
                                                <div class="lg:ml-6 mt-4 lg:mt-0 flex-1">
                                                    <div class="bg-white rounded-lg shadow p-4">
                                                        <h3 class="text-lg font-semibold mb-4">📊 {{ __('health_records_edit.assessment_data') }}</h3>
                                                        
                                                        <!-- Markers List -->
                                                        <div class="mb-4">
                                                            <h4 class="font-medium text-gray-700 mb-2">🎯 {{ __('health_records_edit.markers') }}<span id="postural-markers-count">0</span>)</h4>
                                                            <div id="postural-markers-list" class="space-y-2 max-h-32 overflow-y-auto">
                                                                <!-- Markers will be listed here -->
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Angles List -->
                                                        <div class="mb-4">
                                                            <h4 class="font-medium text-gray-700 mb-2">📐 Angles (<span id="postural-angles-count">0</span>)</h4>
                                                            <div id="postural-angles-list" class="space-y-2 max-h-32 overflow-y-auto">
                                                                <!-- Angles will be listed here -->
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Export Data -->
                                                        <div class="mt-4 p-3 bg-blue-50 rounded">
                                                            <h4 class="font-medium text-blue-800 mb-2">💾 {{ __('health_records_edit.export_data') }}</h4>
                                                            <textarea 
                                                                id="postural-export-data"
                                                                rows="4" 
                                                                class="w-full text-xs border border-blue-200 rounded p-2"
                                                                readonly
                                                            ></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Données cachées pour le formulaire -->
                                <input type="hidden" name="postural_assessment_data" id="postural_assessment_data" value="{{ old('postural_assessment_data') }}">
                                
                                <!-- Postural Assessment Summary -->
                                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                                        <span class="mr-2">📊</span>
                                        {{ __('health_records_edit.postural_assessment_summary') }}
                                    </h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div class="flex items-center justify-between">
                                            <span>{{ __('health_records_edit.markers_2') }}</span>
                                            <span class="font-semibold text-blue-600" id="postural-markers-count">0</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>Angles:</span>
                                            <span class="font-semibold text-green-600" id="postural-angles-count">0</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>{{ __('health_records_edit.current_view') }}</span>
                                            <span class="font-semibold text-purple-600" id="postural-current-view">{{ __('health_records_edit.anterior') }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>{{ __('health_records_edit.status_2') }}</span>
                                            <span class="font-semibold text-orange-600" id="postural-status">{{ __('health_records_edit.in_progress') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Injuries (FIFA F-MARC & SCAT) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="injury_records" class="block text-sm font-medium text-gray-700 mb-2">
                                    🏃 {{ __('health_records_edit.general_injuries_fifa_f_marc_snomed_ct_2') }}
                                </label>
                                <select 
                                    id="injury_records" 
                                    name="injury_records" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                >
                                    <option value="">{{ __('health_records_edit.select_an_injury') }}</option>
                                    
                                    <!-- Head & Neck Injuries -->
                                    <optgroup label="{{ __('health_records_edit.head_and_neck_injuries') }}">
                                        <option value="21522001|Fracture du crâne">{{ __('health_records_edit.k_21522001_skull_fracture') }}</option>
                                        <option value="21522002|Commotion cérébrale">{{ __('health_records_edit.k_21522002_concussion') }}</option>
                                        <option value="21522003|Luxation cervicale">{{ __('health_records_edit.k_21522003_cervical_dislocation') }}</option>
                                        <option value="21522004|Entorse cervicale">{{ __('health_records_edit.k_21522004_cervical_sprain') }}</option>
                                        <option value="21522005|Fracture de la mâchoire">{{ __('health_records_edit.k_21522005_jaw_fracture') }}</option>
                                        <option value="21522006|Fracture du nez">{{ __('health_records_edit.k_21522006_nasal_fracture') }}</option>
                                        <option value="21522007|Lacération du cuir chevelu">{{ __('health_records_edit.k_21522007_scalp_laceration') }}</option>
                                    </optgroup>
                                    
                                    <!-- Upper Limb Injuries -->
                                    <optgroup label="{{ __('health_records_edit.upper_limb_injuries') }}">
                                        <option value="21522008|Fracture de l'épaule">{{ __('health_records_edit.k_21522008_shoulder_fracture') }}</option>
                                        <option value="21522009|Luxation de l'épaule">{{ __('health_records_edit.k_21522009_shoulder_dislocation') }}</option>
                                        <option value="21522010|Entorse de l'épaule">{{ __('health_records_edit.k_21522010_shoulder_sprain') }}</option>
                                        <option value="21522011|Fracture du bras">{{ __('health_records_edit.k_21522011_arm_fracture') }}</option>
                                        <option value="21522012|Fracture de l'avant-bras">{{ __('health_records_edit.k_21522012_forearm_fracture') }}</option>
                                        <option value="21522013|Fracture du poignet">{{ __('health_records_edit.k_21522013_wrist_fracture') }}</option>
                                        <option value="21522014|Entorse du poignet">{{ __('health_records_edit.k_21522014_wrist_sprain') }}</option>
                                        <option value="21522015|Fracture de la main">{{ __('health_records_edit.k_21522015_hand_fracture') }}</option>
                                        <option value="21522016|Fracture du doigt">{{ __('health_records_edit.k_21522016_finger_fracture') }}</option>
                                        <option value="21522017|Luxation du doigt">{{ __('health_records_edit.k_21522017_finger_dislocation') }}</option>
                                    </optgroup>
                                    
                                    <!-- Trunk Injuries -->
                                    <optgroup label="{{ __('health_records_edit.trunk_injuries') }}">
                                        <option value="21522018|Fracture de la clavicule">{{ __('health_records_edit.k_21522018_clavicle_fracture') }}</option>
                                        <option value="21522019|Fracture de la côte">{{ __('health_records_edit.k_21522019_rib_fracture') }}</option>
                                        <option value="21522020|Fracture du sternum">{{ __('health_records_edit.k_21522020_sternum_fracture') }}</option>
                                        <option value="21522021|Fracture vertébrale">{{ __('health_records_edit.k_21522021_vertebral_fracture') }}</option>
                                        <option value="21522022|Hernie discale">{{ __('health_records_edit.k_21522022_herniated_disc') }}</option>
                                        <option value="21522023|Entorse lombaire">{{ __('health_records_edit.k_21522023_lumbar_sprain') }}</option>
                                        <option value="21522024|Contusion abdominale">{{ __('health_records_edit.k_21522024_abdominal_contusion') }}</option>
                                    </optgroup>
                                    
                                    <!-- Lower Limb Injuries -->
                                    <optgroup label="{{ __('health_records_edit.lower_limb_injuries') }}">
                                        <option value="21522025|Fracture de la hanche">{{ __('health_records_edit.k_21522025_hip_fracture') }}</option>
                                        <option value="21522026|Luxation de la hanche">{{ __('health_records_edit.k_21522026_hip_dislocation') }}</option>
                                        <option value="21522027|Fracture de la cuisse">{{ __('health_records_edit.k_21522027_thigh_fracture') }}</option>
                                        <option value="21522028|Fracture du genou">{{ __('health_records_edit.k_21522028_knee_fracture') }}</option>
                                        <option value="21522029|Luxation du genou">{{ __('health_records_edit.k_21522029_knee_dislocation') }}</option>
                                        <option value="21522030|Entorse du genou">{{ __('health_records_edit.k_21522030_knee_sprain') }}</option>
                                        <option value="21522031|Rupture du ligament croisé">{{ __('health_records_edit.k_21522031_cruciate_ligament_rupture') }}</option>
                                        <option value="21522032|Rupture du ménisque">{{ __('health_records_edit.k_21522032_meniscus_rupture') }}</option>
                                        <option value="21522033|Fracture de la jambe">{{ __('health_records_edit.k_21522033_leg_fracture') }}</option>
                                        <option value="21522034|Fracture de la cheville">{{ __('health_records_edit.k_21522034_ankle_fracture') }}</option>
                                        <option value="21522035|Entorse de la cheville">{{ __('health_records_edit.k_21522035_ankle_sprain') }}</option>
                                        <option value="21522036|Fracture du pied">{{ __('health_records_edit.k_21522036_foot_fracture') }}</option>
                                        <option value="21522037|Fracture de l'orteil">{{ __('health_records_edit.k_21522037_toe_fracture') }}</option>
                                        <option value="21522038|Luxation de l'orteil">{{ __('health_records_edit.k_21522038_toe_dislocation') }}</option>
                                    </optgroup>
                                    
                                    <!-- Muscle & Tendon Injuries -->
                                    <optgroup label="{{ __('health_records_edit.muscle_and_tendon_injuries') }}">
                                        <option value="21522039|Déchirure musculaire">{{ __('health_records_edit.k_21522039_muscle_tear') }}</option>
                                        <option value="21522040|Rupture tendineuse">{{ __('health_records_edit.k_21522040_tendon_rupture') }}</option>
                                        <option value="21522041|Tendinite">{{ __('health_records_edit.k_21522041_tendinitis') }}</option>
                                        <option value="21522042|Contracture musculaire">{{ __('health_records_edit.k_21522042_muscle_contracture') }}</option>
                                        <option value="21522043|Élongation musculaire">{{ __('health_records_edit.k_21522043_muscle_strain') }}</option>
                                        <option value="21522044|Syndrome de la bandelette ilio-tibiale">{{ __('health_records_edit.k_21522044_iliotibial_band_syndrome') }}</option>
                                        <option value="21522045|Syndrome rotulien">{{ __('health_records_edit.k_21522045_patellofemoral_syndrome') }}</option>
                                    </optgroup>
                                    
                                    <!-- Skin & Soft Tissue Injuries -->
                                    <optgroup label="{{ __('health_records_edit.skin_and_soft_tissue_injuries') }}">
                                        <option value="21522046|Lacération">{{ __('health_records_edit.k_21522046_laceration') }}</option>
                                        <option value="21522047|Abrasion">21522047 - Abrasion</option>
                                        <option value="21522048|Contusion">21522048 - Contusion</option>
                                        <option value="21522049|Hématome">{{ __('health_records_edit.k_21522049_hematoma') }}</option>
                                        <option value="21522050|Brûlure">{{ __('health_records_edit.k_21522050_burn') }}</option>
                                        <option value="21522051|Plaie par perforation">{{ __('health_records_edit.k_21522051_puncture_wound') }}</option>
                                    </optgroup>
                                    
                                    <!-- Overuse & Chronic Injuries -->
                                    <optgroup label="{{ __('health_records_edit.overuse_and_chronic_injuries') }}">
                                        <option value="21522052|Stress fracture">21522052 - Stress fracture</option>
                                        <option value="21522053|Tendinopathie chronique">{{ __('health_records_edit.k_21522053_chronic_tendinopathy') }}</option>
                                        <option value="21522054|Bursite">{{ __('health_records_edit.k_21522054_bursitis') }}</option>
                                        <option value="21522055|Fasciite plantaire">{{ __('health_records_edit.k_21522055_plantar_fasciitis') }}</option>
                                        <option value="21522056|Syndrome de compression nerveuse">{{ __('health_records_edit.k_21522056_nerve_compression_syndrome') }}</option>
                                        <option value="21522057|Ostéochondrite">{{ __('health_records_edit.k_21522057_osteochondritis') }}</option>
                                    </optgroup>
                                    
                                    <!-- Other Injuries -->
                                    <optgroup label="{{ __('health_records_edit.other_injuries') }}">
                                        <option value="21522058|Blessure non spécifiée">{{ __('health_records_edit.k_21522058_unspecified_injury') }}</option>
                                        <option value="21522059|Blessure multiple">{{ __('health_records_edit.k_21522059_multiple_injury') }}</option>
                                        <option value="21522060|Blessure par surcharge">{{ __('health_records_edit.k_21522060_overuse_injury') }}</option>
                                        <option value="21522061|Blessure par traumatisme direct">{{ __('health_records_edit.k_21522061_direct_trauma_injury') }}</option>
                                        <option value="21522062|Blessure par traumatisme indirect">{{ __('health_records_edit.k_21522062_indirect_trauma_injury') }}</option>
                                    </optgroup>
                                </select>
                                
                                <!-- Additional injury details textarea -->
                                <div class="mt-3">
                                    <label for="injury_details" class="block text-sm font-medium text-gray-600 mb-1">
                                        {{ __('health_records_edit.additional_injury_details') }}
                                    </label>
                                    <textarea 
                                        id="injury_details" 
                                        name="injury_details" 
                                        rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="{{ __('health_records_edit.injury_mechanism_severity_treatment_reco') }}"
                                    >{{ old('injury_details') }}</textarea>
                                </div>
                            </div>
                            
                            <div>
                                <label for="scat_assessments" class="block text-sm font-medium text-gray-700 mb-2">
                                    🧠 {{ __('health_records_edit.scat_assessments_concussions') }}
                                </label>
                                <textarea 
                                    id="scat_assessments" 
                                    name="scat_assessments" 
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="{{ __('health_records_edit.scat_assessments_neurological_symptoms_r') }}"
                                >{{ old('scat_assessments') }}</textarea>
                            </div>
                        </div>

                        <!-- MAPA & Advanced Imaging -->
                        <div class="grid grid-cols-1 gap-6 mb-6">
                            <div>
                                <label for="mapa_results" class="block text-sm font-medium text-gray-700 mb-2">
                                    📊 {{ __('health_records_edit.abpm_ambulatory_blood_pressure_monitorin') }}
                                </label>
                                
                                <!-- MAPA Comprehensive Interface -->
                                <div class="bg-white border border-gray-300 rounded-lg p-4 space-y-6">
                                    
                                    <!-- 1. Informations Générales sur l'Examen -->
                                    <div class="border-b border-gray-200 pb-4">
                                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('health_records_edit.k_1_general_examination_information') }}</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="mapa_date" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.measurement_date') }}
                                                </label>
                                                <input 
                                                    type="date" 
                                                    id="mapa_date" 
                                                    name="mapa_date" 
                                                    value="{{ old('mapa_date', date('Y-m-d')) }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                                                >
                                            </div>
                                            <div>
                                                <label for="mapa_reason" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.reason_for_examination') }}
                                                </label>
                                                <select 
                                                    id="mapa_reason" 
                                                    name="mapa_reason" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                                                >
                                                    <option value="">{{ __('health_records_edit.select_a_reason') }}</option>
                                                    <option value="bilan_pre_saison" {{ old('mapa_reason') == 'bilan_pre_saison' ? 'selected' : '' }}>{{ __('health_records_edit.pre_season_assessment') }}</option>
                                                    <option value="suivi_hta_effort" {{ old('mapa_reason') == 'suivi_hta_effort' ? 'selected' : '' }}>{{ __('health_records_edit.exercise_hypertension_follow_up') }}</option>
                                                    <option value="symptomes_suspects" {{ old('mapa_reason') == 'symptomes_suspects' ? 'selected' : '' }}>{{ __('health_records_edit.suspicious_symptoms') }}</option>
                                                    <option value="controle_traitement" {{ old('mapa_reason') == 'controle_traitement' ? 'selected' : '' }}>{{ __('health_records_edit.treatment_monitoring') }}</option>
                                                    <option value="evaluation_risque" {{ old('mapa_reason') == 'evaluation_risque' ? 'selected' : '' }}>{{ __('health_records_edit.cardiovascular_risk_assessment') }}</option>
                                                    <option value="autre" {{ old('mapa_reason') == 'autre' ? 'selected' : '' }}>{{ __('health_records_edit.other') }}</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label for="mapa_device" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.device_information_optional') }}
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="mapa_device" 
                                                    name="mapa_device" 
                                                    value="{{ old('mapa_device') }}"
                                                    placeholder="{{ __('health_records_edit.abpm_device_identifier') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                                                >
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Synthèse et Conclusion Médicale -->
                                    <div class="border-b border-gray-200 pb-4">
                                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('health_records_edit.k_2_summary_and_medical_conclusion') }}</h4>
                                        <div class="space-y-4">
                                            <div>
                                                <label for="mapa_conclusion" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.physician_s_conclusion') }}
                                                </label>
                                                <textarea 
                                                    id="mapa_conclusion" 
                                                    name="mapa_conclusion" 
                                                    rows="4"
                                                    placeholder="{{ __('health_records_edit.interpretation_conclusions_and_recommend') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                                                >{{ old('mapa_conclusion') }}</textarea>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label for="mapa_dipping" class="block text-xs font-medium text-gray-600 mb-1">
                                                        {{ __('health_records_edit.blood_pressure_profile_nocturnal_dipping') }}
                                                    </label>
                                                    <select 
                                                        id="mapa_dipping" 
                                                        name="mapa_dipping" 
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                                                    >
                                                        <option value="">{{ __('health_records_edit.select_the_profile') }}</option>
                                                        <option value="dipper_normal" {{ old('mapa_dipping') == 'dipper_normal' ? 'selected' : '' }}>{{ __('health_records_edit.normal_dipper_10_20') }}</option>
                                                        <option value="non_dipper" {{ old('mapa_dipping') == 'non_dipper' ? 'selected' : '' }}>Non-dipper (<10%)</option>
                                                        <option value="dipper_extreme" {{ old('mapa_dipping') == 'dipper_extreme' ? 'selected' : '' }}>{{ __('health_records_edit.extreme_dipper_20') }}</option>
                                                        <option value="reverse_dipper" {{ old('mapa_dipping') == 'reverse_dipper' ? 'selected' : '' }}>Reverse dipper</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <label for="mapa_pas_24h" class="block text-xs font-medium text-gray-600 mb-1">
                                                        {{ __('health_records_edit.average_systolic_bp_mmhg') }}
                                                    </label>
                                                    <input 
                                                        type="number" 
                                                        id="mapa_pas_24h" 
                                                        name="mapa_pas_24h" 
                                                        value="{{ old('mapa_pas_24h') }}"
                                                        step="0.1"
                                                        placeholder="120"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                                                    >
                                                </div>
                                                <div>
                                                    <label for="mapa_pad_24h" class="block text-xs font-medium text-gray-600 mb-1">
                                                        {{ __('health_records_edit.average_diastolic_bp_mmhg') }}
                                                    </label>
                                                    <input 
                                                        type="number" 
                                                        id="mapa_pad_24h" 
                                                        name="mapa_pad_24h" 
                                                        value="{{ old('mapa_pad_24h') }}"
                                                        step="0.1"
                                                        placeholder="80"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                                                    >
                                                </div>
                                                <div>
                                                    <label for="mapa_fc_24h" class="block text-xs font-medium text-gray-600 mb-1">
                                                        {{ __('health_records_edit.average_heart_rate_bpm') }}
                                                    </label>
                                                    <input 
                                                        type="number" 
                                                        id="mapa_fc_24h" 
                                                        name="mapa_fc_24h" 
                                                        value="{{ old('mapa_fc_24h') }}"
                                                        step="0.1"
                                                        placeholder="70"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. Données Détaillées par Période -->
                                    <div class="border-b border-gray-200 pb-4">
                                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('health_records_edit.k_3_detailed_data_by_period') }}</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Période d'Éveil (Jour) -->
                                            <div class="bg-blue-50 p-4 rounded-lg">
                                                <h5 class="text-sm font-medium text-blue-800 mb-3">{{ __('health_records_edit.awake_period_day') }}</h5>
                                                <div class="space-y-3">
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div>
                                                            <label for="mapa_day_start" class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ __('health_records_edit.start_time') }}
                                                            </label>
                                                            <input 
                                                                type="time" 
                                                                id="mapa_day_start" 
                                                                name="mapa_day_start" 
                                                                value="{{ old('mapa_day_start', '06:00') }}"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                            >
                                                        </div>
                                                        <div>
                                                            <label for="mapa_day_end" class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ __('health_records_edit.end_time') }}
                                                            </label>
                                                            <input 
                                                                type="time" 
                                                                id="mapa_day_end" 
                                                                name="mapa_day_end" 
                                                                value="{{ old('mapa_day_end', '22:00') }}"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                            >
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-3 gap-2">
                                                        <div>
                                                            <label for="mapa_pas_day" class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ __('health_records_edit.sbp_mmhg') }}
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                id="mapa_pas_day" 
                                                                name="mapa_pas_day" 
                                                                value="{{ old('mapa_pas_day') }}"
                                                                step="0.1"
                                                                placeholder="125"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                            >
                                                        </div>
                                                        <div>
                                                            <label for="mapa_pad_day" class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ __('health_records_edit.dbp_mmhg') }}
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                id="mapa_pad_day" 
                                                                name="mapa_pad_day" 
                                                                value="{{ old('mapa_pad_day') }}"
                                                                step="0.1"
                                                                placeholder="85"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                            >
                                                        </div>
                                                        <div>
                                                            <label for="mapa_fc_day" class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ __('health_records_edit.hr_bpm') }}
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                id="mapa_fc_day" 
                                                                name="mapa_fc_day" 
                                                                value="{{ old('mapa_fc_day') }}"
                                                                step="0.1"
                                                                placeholder="75"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                            >
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="mapa_load_day" class="block text-xs font-medium text-gray-600 mb-1">
                                                            {{ __('health_records_edit.blood_pressure_load') }}
                                                        </label>
                                                        <input 
                                                            type="number" 
                                                            id="mapa_load_day" 
                                                            name="mapa_load_day" 
                                                            value="{{ old('mapa_load_day') }}"
                                                            step="0.1"
                                                            placeholder="15"
                                                            class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                        >
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Période de Sommeil (Nuit) -->
                                            <div class="bg-indigo-50 p-4 rounded-lg">
                                                <h5 class="text-sm font-medium text-indigo-800 mb-3">{{ __('health_records_edit.sleep_period_night') }}</h5>
                                                <div class="space-y-3">
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div>
                                                            <label for="mapa_night_start" class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ __('health_records_edit.start_time') }}
                                                            </label>
                                                            <input 
                                                                type="time" 
                                                                id="mapa_night_start" 
                                                                name="mapa_night_start" 
                                                                value="{{ old('mapa_night_start', '22:00') }}"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                                                            >
                                                        </div>
                                                        <div>
                                                            <label for="mapa_night_end" class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ __('health_records_edit.end_time') }}
                                                            </label>
                                                            <input 
                                                                type="time" 
                                                                id="mapa_night_end" 
                                                                name="mapa_night_end" 
                                                                value="{{ old('mapa_night_end', '06:00') }}"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                                                            >
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-3 gap-2">
                                                        <div>
                                                            <label for="mapa_pas_night" class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ __('health_records_edit.sbp_mmhg') }}
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                id="mapa_pas_night" 
                                                                name="mapa_pas_night" 
                                                                value="{{ old('mapa_pas_night') }}"
                                                                step="0.1"
                                                                placeholder="110"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                                                            >
                                                        </div>
                                                        <div>
                                                            <label for="mapa_pad_night" class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ __('health_records_edit.dbp_mmhg') }}
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                id="mapa_pad_night" 
                                                                name="mapa_pad_night" 
                                                                value="{{ old('mapa_pad_night') }}"
                                                                step="0.1"
                                                                placeholder="70"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                                                            >
                                                        </div>
                                                        <div>
                                                            <label for="mapa_fc_night" class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ __('health_records_edit.hr_bpm') }}
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                id="mapa_fc_night" 
                                                                name="mapa_fc_night" 
                                                                value="{{ old('mapa_fc_night') }}"
                                                                step="0.1"
                                                                placeholder="60"
                                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                                                            >
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="mapa_load_night" class="block text-xs font-medium text-gray-600 mb-1">
                                                            {{ __('health_records_edit.blood_pressure_load') }}
                                                        </label>
                                                        <input 
                                                            type="number" 
                                                            id="mapa_load_night" 
                                                            name="mapa_load_night" 
                                                            value="{{ old('mapa_load_night') }}"
                                                            step="0.1"
                                                            placeholder="8"
                                                            class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 4. ECG d'Effort et Scintigraphie -->
                                    <!-- 4. Visualisation et Données Brutes -->
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('health_records_edit.k_5_visualization_and_raw_data') }}</h4>
                                        <div class="space-y-4">
                                            <!-- Graphique 24h (placeholder) -->
                                            <div class="bg-gray-50 p-4 rounded-lg">
                                                <h5 class="text-sm font-medium text-gray-700 mb-2">{{ __('health_records_edit.k_24_hour_graph') }}</h5>
                                                <div class="bg-white border border-gray-200 rounded-lg p-4 h-48 flex items-center justify-center">
                                                    <div class="text-center text-gray-500">
                                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                        </svg>
                                                        <p class="text-sm">{{ __('health_records_edit.interactive_24h_graph') }}</p>
                                                        <p class="text-xs">{{ __('health_records_edit.sbp_dbp_hr_with_day_night_periods') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tableau des Mesures Individuelles -->
                                            <div class="bg-gray-50 p-4 rounded-lg">
                                                <h5 class="text-sm font-medium text-gray-700 mb-2">{{ __('health_records_edit.individual_measurements_table') }}</h5>
                                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                                    <div class="overflow-x-auto">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('health_records_edit.time') }}</th>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('health_records_edit.sbp_mmhg') }}</th>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('health_records_edit.dbp_mmhg') }}</th>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('health_records_edit.hr_bpm') }}</th>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('health_records_edit.period') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                <tr class="text-xs">
                                                                    <td class="px-3 py-2 text-gray-900">06:00</td>
                                                                    <td class="px-3 py-2 text-gray-900">125</td>
                                                                    <td class="px-3 py-2 text-gray-900">85</td>
                                                                    <td class="px-3 py-2 text-gray-900">75</td>
                                                                    <td class="px-3 py-2"><span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">{{ __('health_records_edit.day') }}</span></td>
                                                                </tr>
                                                                <tr class="text-xs">
                                                                    <td class="px-3 py-2 text-gray-900">08:00</td>
                                                                    <td class="px-3 py-2 text-gray-900">130</td>
                                                                    <td class="px-3 py-2 text-gray-900">88</td>
                                                                    <td class="px-3 py-2 text-gray-900">78</td>
                                                                    <td class="px-3 py-2"><span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">{{ __('health_records_edit.day') }}</span></td>
                                                                </tr>
                                                                <tr class="text-xs">
                                                                    <td class="px-3 py-2 text-gray-900">22:00</td>
                                                                    <td class="px-3 py-2 text-gray-900">110</td>
                                                                    <td class="px-3 py-2 text-gray-900">70</td>
                                                                    <td class="px-3 py-2 text-gray-900">60</td>
                                                                    <td class="px-3 py-2"><span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">{{ __('health_records_edit.night') }}</span></td>
                                                                </tr>
                                                                <tr class="text-xs">
                                                                    <td class="px-3 py-2 text-gray-900">02:00</td>
                                                                    <td class="px-3 py-2 text-gray-900">105</td>
                                                                    <td class="px-3 py-2 text-gray-900">68</td>
                                                                    <td class="px-3 py-2 text-gray-900">58</td>
                                                                    <td class="px-3 py-2"><span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">{{ __('health_records_edit.night') }}</span></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="bg-gray-50 px-4 py-2 text-xs text-gray-500">
                                                        <button type="button" class="text-purple-600 hover:text-purple-800 font-medium">
                                                            {{ __('health_records_edit.view_all_measurements_48_72_measurements') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
                                        <button type="button" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                            💾 {{ __('health_records_edit.save') }}
                                        </button>
                                        <button type="button" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                            🖨️ {{ __('health_records_edit.print_report') }}
                                        </button>
                                        <button type="button" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                            📊 {{ __('health_records_edit.compare_with_previous_examination') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SCAT - Sport Concussion Assessment Tool -->
                        <div class="grid grid-cols-1 gap-6 mb-6">
                            <div>
                                <label for="scat_evaluation" class="block text-sm font-medium text-gray-700 mb-2">
                                    🧠 SCAT - Sport Concussion Assessment Tool
                                </label>
                                
                                <!-- SCAT Comprehensive Interface -->
                                <div class="bg-white border border-gray-300 rounded-lg p-4 space-y-6">
                                    
                                    <!-- 1. Informations Générales sur l'Évaluation -->
                                    <div class="border-b border-gray-200 pb-4">
                                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('health_records_edit.k_1_general_assessment_information') }}</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label for="scat_date" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.assessment_date') }}
                                                </label>
                                                <input 
                                                    type="date" 
                                                    id="scat_date" 
                                                    name="scat_date" 
                                                    value="{{ old('scat_date', date('Y-m-d')) }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                >
                                            </div>
                                            <div>
                                                <label for="scat_time" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.assessment_time') }}
                                                </label>
                                                <input 
                                                    type="time" 
                                                    id="scat_time" 
                                                    name="scat_time" 
                                                    value="{{ old('scat_time', date('H:i')) }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                >
                                            </div>
                                            <div>
                                                <label for="scat_context" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.context') }}
                                                </label>
                                                <select 
                                                    id="scat_context" 
                                                    name="scat_context" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                >
                                                    <option value="">{{ __('health_records_edit.select_the_context') }}</option>
                                                    <option value="terrain_match" {{ old('scat_context') == 'terrain_match' ? 'selected' : '' }}>{{ __('health_records_edit.on_field_match') }}</option>
                                                    <option value="clinique_suivi" {{ old('scat_context') == 'clinique_suivi' ? 'selected' : '' }}>{{ __('health_records_edit.in_clinic_follow_up_day_2') }}</option>
                                                    <option value="bilan_pre_saison" {{ old('scat_context') == 'bilan_pre_saison' ? 'selected' : '' }}>{{ __('health_records_edit.pre_season_assessment') }}</option>
                                                    <option value="entrainement" {{ old('scat_context') == 'entrainement' ? 'selected' : '' }}>{{ __('health_records_edit.training') }}</option>
                                                    <option value="autre" {{ old('scat_context') == 'autre' ? 'selected' : '' }}>{{ __('health_records_edit.other') }}</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label for="scat_evaluator" class="block text-xs font-medium text-gray-600 mb-1">
                                                    {{ __('health_records_edit.assessor_s_name') }}
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="scat_evaluator" 
                                                    name="scat_evaluator" 
                                                    value="{{ old('scat_evaluator') }}"
                                                    placeholder="{{ __('health_records_edit.dr_name_sports_physician') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                >
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Signes d'Alerte et Symptômes -->
                                    <div class="border-b border-gray-200 pb-4">
                                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('health_records_edit.k_2_warning_signs_and_symptoms') }}</h4>
                                        
                                        <!-- Drapeaux Rouges -->
                                        <div class="bg-red-50 p-4 rounded-lg mb-4">
                                            <h5 class="text-sm font-semibold text-red-800 mb-3">{{ __('health_records_edit.red_flags') }}</h5>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_red_flags[]" value="douleur_cervicale" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                    <span class="text-sm text-red-700">{{ __('health_records_edit.neck_pain') }}</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_red_flags[]" value="convulsions" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                    <span class="text-sm text-red-700">{{ __('health_records_edit.seizures') }}</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_red_flags[]" value="vomissements_repetes" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                    <span class="text-sm text-red-700">{{ __('health_records_edit.repeated_vomiting') }}</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_red_flags[]" value="perte_conscience" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                    <span class="text-sm text-red-700">{{ __('health_records_edit.loss_of_consciousness') }}</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_red_flags[]" value="troubles_vision" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                    <span class="text-sm text-red-700">{{ __('health_records_edit.vision_disturbances') }}</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_red_flags[]" value="faiblesse_membres" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                    <span class="text-sm text-red-700">{{ __('health_records_edit.limb_weakness') }}</span>
                                                </label>
                                            </div>
                                            <div id="red-flags-alert" class="hidden mt-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                                                <strong>{{ __('health_records_edit.medical_emergency') }}</strong> {{ __('health_records_edit.immediate_medical_transfer_recommended') }}
                                            </div>
                                        </div>

                                        <!-- Signes Observables -->
                                        <div class="bg-yellow-50 p-4 rounded-lg mb-4">
                                            <h5 class="text-sm font-semibold text-yellow-800 mb-3">{{ __('health_records_edit.observable_signs') }}</h5>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_observable_signs[]" value="hebetude" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                                    <span class="text-sm text-yellow-700">{{ __('health_records_edit.appears_dazed') }}</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_observable_signs[]" value="problemes_equilibre" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                                    <span class="text-sm text-yellow-700">{{ __('health_records_edit.balance_problems') }}</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_observable_signs[]" value="confusion" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                                    <span class="text-sm text-yellow-700">Confusion</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_observable_signs[]" value="lenteur_mouvements" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                                    <span class="text-sm text-yellow-700">{{ __('health_records_edit.slowness_of_movement') }}</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_observable_signs[]" value="troubles_parole" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                                    <span class="text-sm text-yellow-700">{{ __('health_records_edit.speech_disturbances') }}</span>
                                                </label>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_observable_signs[]" value="emotion_inappropriee" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                                    <span class="text-sm text-yellow-700">{{ __('health_records_edit.inappropriate_emotion') }}</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Évaluation des Symptômes -->
                                        <div class="bg-blue-50 p-4 rounded-lg">
                                            <h5 class="text-sm font-semibold text-blue-800 mb-3">{{ __('health_records_edit.symptom_evaluation_22_items') }}</h5>
                                            <div class="space-y-3">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                                    <div class="font-medium text-gray-700">{{ __('health_records_edit.symptom') }}</div>
                                                    <div class="font-medium text-gray-700 text-center">{{ __('health_records_edit.severity_0_6') }}</div>
                                                    <div class="font-medium text-gray-700 text-center">Score</div>
                                                </div>
                                                
                                                <!-- Symptômes individuels -->
                                                <div class="space-y-2">
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                                        <div class="text-sm">{{ __('health_records_edit.headache') }}</div>
                                                        <div class="flex items-center space-x-2">
                                                            <input type="range" min="0" max="6" value="0" class="w-full" name="scat_headache" id="scat_headache">
                                                            <span class="text-xs w-8 text-center" id="scat_headache_value">0</span>
                                                        </div>
                                                        <div class="text-xs text-center" id="scat_headache_score">0</div>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                                        <div class="text-sm">{{ __('health_records_edit.nausea') }}</div>
                                                        <div class="flex items-center space-x-2">
                                                            <input type="range" min="0" max="6" value="0" class="w-full" name="scat_nausea" id="scat_nausea">
                                                            <span class="text-xs w-8 text-center" id="scat_nausea_value">0</span>
                                                        </div>
                                                        <div class="text-xs text-center" id="scat_nausea_score">0</div>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                                        <div class="text-sm">{{ __('health_records_edit.dizziness') }}</div>
                                                        <div class="flex items-center space-x-2">
                                                            <input type="range" min="0" max="6" value="0" class="w-full" name="scat_dizziness" id="scat_dizziness">
                                                            <span class="text-xs w-8 text-center" id="scat_dizziness_value">0</span>
                                                        </div>
                                                        <div class="text-xs text-center" id="scat_dizziness_score">0</div>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                                        <div class="text-sm">Fatigue</div>
                                                        <div class="flex items-center space-x-2">
                                                            <input type="range" min="0" max="6" value="0" class="w-full" name="scat_fatigue" id="scat_fatigue">
                                                            <span class="text-xs w-8 text-center" id="scat_fatigue_value">0</span>
                                                        </div>
                                                        <div class="text-xs text-center" id="scat_fatigue_score">0</div>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                                        <div class="text-sm">{{ __('health_records_edit.sensitivity_to_light') }}</div>
                                                        <div class="flex items-center space-x-2">
                                                            <input type="range" min="0" max="6" value="0" class="w-full" name="scat_light_sensitivity" id="scat_light_sensitivity">
                                                            <span class="text-xs w-8 text-center" id="scat_light_sensitivity_value">0</span>
                                                        </div>
                                                        <div class="text-xs text-center" id="scat_light_sensitivity_score">0</div>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                                        <div class="text-sm">{{ __('health_records_edit.sensitivity_to_noise') }}</div>
                                                        <div class="flex items-center space-x-2">
                                                            <input type="range" min="0" max="6" value="0" class="w-full" name="scat_noise_sensitivity" id="scat_noise_sensitivity">
                                                            <span class="text-xs w-8 text-center" id="scat_noise_sensitivity_value">0</span>
                                                        </div>
                                                        <div class="text-xs text-center" id="scat_noise_sensitivity_score">0</div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Résumé des scores -->
                                                <div class="mt-4 p-3 bg-blue-100 rounded-lg">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                        <div>
                                                            <strong>{{ __('health_records_edit.total_number_of_symptoms') }}</strong>
                                                            <span id="total_symptoms" class="ml-2 font-bold text-blue-800">0</span>
                                                        </div>
                                                        <div>
                                                            <strong>{{ __('health_records_edit.total_severity_score') }}</strong>
                                                            <span id="total_severity" class="ml-2 font-bold text-blue-800">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. Évaluation Cognitive et Neurologique -->
                                    <div class="border-b border-gray-200 pb-4">
                                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('health_records_edit.k_3_cognitive_and_neurological_assessment') }}</h4>
                                        
                                        <!-- Bilan Cognitif (SAC) -->
                                        <div class="bg-green-50 p-4 rounded-lg mb-4">
                                            <h5 class="text-sm font-semibold text-green-800 mb-3">{{ __('health_records_edit.cognitive_assessment_sac_standardised_as') }}</h5>
                                            <div class="space-y-4">
                                                <!-- Orientation -->
                                                <div>
                                                    <h6 class="text-sm font-medium text-green-700 mb-2">Orientation (Score /5)</h6>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs text-gray-600 mb-1">{{ __('health_records_edit.month') }}</label>
                                                            <input type="text" name="scat_orientation_month" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-600 mb-1">Date</label>
                                                            <input type="text" name="scat_orientation_date" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-600 mb-1">{{ __('health_records_edit.year') }}</label>
                                                            <input type="text" name="scat_orientation_year" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-600 mb-1">{{ __('health_records_edit.time') }}</label>
                                                            <input type="text" name="scat_orientation_time" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-gray-600 mb-1">{{ __('health_records_edit.day_of_the_week') }}</label>
                                                            <input type="text" name="scat_orientation_day" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                        </div>
                                                    </div>
                                                    <div class="mt-2 text-xs">
                                                        <strong>{{ __('health_records_edit.orientation_score') }}</strong> <span id="orientation_score" class="font-bold text-green-700">0</span>/5
                                                    </div>
                                                </div>

                                                <!-- Mémoire Immédiate -->
                                                <div>
                                                    <h6 class="text-sm font-medium text-green-700 mb-2">{{ __('health_records_edit.immediate_memory_score_15') }}</h6>
                                                    <div class="space-y-2">
                                                        <div class="text-xs text-gray-600">{{ __('health_records_edit.words_to_remember') }} <strong>{{ __('health_records_edit.elephant_parsley_treasure_kettle_concret') }}</strong></div>
                                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                            <div>
                                                                <label class="block text-xs text-gray-600 mb-1">{{ __('health_records_edit.trial_1') }}</label>
                                                                <input type="text" name="scat_immediate_memory_trial1" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 mb-1">{{ __('health_records_edit.trial_2') }}</label>
                                                                <input type="text" name="scat_immediate_memory_trial2" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 mb-1">{{ __('health_records_edit.trial_3') }}</label>
                                                                <input type="text" name="scat_immediate_memory_trial3" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                            </div>
                                                        </div>
                                                        <div class="mt-2 text-xs">
                                                            <strong>{{ __('health_records_edit.immediate_memory_score') }}</strong> <span id="immediate_memory_score" class="font-bold text-green-700">0</span>/15
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Concentration -->
                                                <div>
                                                    <h6 class="text-sm font-medium text-green-700 mb-2">Concentration (Score /5)</h6>
                                                    <div class="space-y-2">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            <div>
                                                                <label class="block text-xs text-gray-600 mb-1">{{ __('health_records_edit.digit_span_backwards') }}</label>
                                                                <input type="text" name="scat_concentration_digits" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 mb-1">{{ __('health_records_edit.months_in_reverse_order') }}</label>
                                                                <input type="text" name="scat_concentration_months" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                            </div>
                                                        </div>
                                                        <div class="mt-2 text-xs">
                                                            <strong>{{ __('health_records_edit.concentration_score') }}</strong> <span id="concentration_score" class="font-bold text-green-700">0</span>/5
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Rappel Différé -->
                                                <div>
                                                    <h6 class="text-sm font-medium text-green-700 mb-2">{{ __('health_records_edit.delayed_recall_score_5') }}</h6>
                                                    <div>
                                                        <input type="text" name="scat_delayed_recall" placeholder="{{ __('health_records_edit.k_5_word_recall') }}" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                                    </div>
                                                    <div class="mt-2 text-xs">
                                                        <strong>{{ __('health_records_edit.delayed_recall_score') }}</strong> <span id="delayed_recall_score" class="font-bold text-green-700">0</span>/5
                                                    </div>
                                                </div>

                                                <!-- Score SAC Total -->
                                                <div class="mt-4 p-3 bg-green-100 rounded-lg">
                                                    <div class="text-sm font-bold text-green-800">
                                                        <strong>{{ __('health_records_edit.total_sac_score') }}</strong> <span id="sac_total_score" class="text-lg">0</span>/30
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Examen Neurologique et Équilibre -->
                                        <div class="bg-purple-50 p-4 rounded-lg">
                                            <h5 class="text-sm font-semibold text-purple-800 mb-3">{{ __('health_records_edit.neurological_and_balance_examination') }}</h5>
                                            
                                            <!-- Bilan de l'Équilibre (mBESS) -->
                                            <div class="mb-4">
                                                <h6 class="text-sm font-medium text-purple-700 mb-2">{{ __('health_records_edit.balance_assessment_mbess') }}</h6>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div>
                                                        <h7 class="text-xs font-medium text-gray-600">{{ __('health_records_edit.position_1_feet_together') }}</h7>
                                                        <div class="space-y-1 mt-1">
                                                            <div class="flex justify-between text-xs">
                                                                <span>{{ __('health_records_edit.firm_surface') }}</span>
                                                                <input type="number" min="0" max="10" name="scat_mbess_firm_feet" class="w-12 px-1 py-1 border border-gray-300 rounded text-xs">
                                                            </div>
                                                            <div class="flex justify-between text-xs">
                                                                <span>{{ __('health_records_edit.foam_surface') }}</span>
                                                                <input type="number" min="0" max="10" name="scat_mbess_foam_feet" class="w-12 px-1 py-1 border border-gray-300 rounded text-xs">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h7 class="text-xs font-medium text-gray-600">{{ __('health_records_edit.position_2_tandem') }}</h7>
                                                        <div class="space-y-1 mt-1">
                                                            <div class="flex justify-between text-xs">
                                                                <span>{{ __('health_records_edit.firm_surface') }}</span>
                                                                <input type="number" min="0" max="10" name="scat_mbess_firm_tandem" class="w-12 px-1 py-1 border border-gray-300 rounded text-xs">
                                                            </div>
                                                            <div class="flex justify-between text-xs">
                                                                <span>{{ __('health_records_edit.foam_surface') }}</span>
                                                                <input type="number" min="0" max="10" name="scat_mbess_foam_tandem" class="w-12 px-1 py-1 border border-gray-300 rounded text-xs">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h7 class="text-xs font-medium text-gray-600">{{ __('health_records_edit.position_3_single_leg_stance') }}</h7>
                                                        <div class="space-y-1 mt-1">
                                                            <div class="flex justify-between text-xs">
                                                                <span>{{ __('health_records_edit.firm_surface') }}</span>
                                                                <input type="number" min="0" max="10" name="scat_mbess_firm_single" class="w-12 px-1 py-1 border border-gray-300 rounded text-xs">
                                                            </div>
                                                            <div class="flex justify-between text-xs">
                                                                <span>{{ __('health_records_edit.foam_surface') }}</span>
                                                                <input type="number" min="0" max="10" name="scat_mbess_foam_single" class="w-12 px-1 py-1 border border-gray-300 rounded text-xs">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3 text-xs">
                                                    <strong>{{ __('health_records_edit.total_mbess_error_score') }}</strong> <span id="mbess_total_score" class="font-bold text-purple-700">0</span>
                                                </div>
                                            </div>

                                            <!-- Examen de la Colonne Cervicale -->
                                            <div>
                                                <h6 class="text-sm font-medium text-purple-700 mb-2">{{ __('health_records_edit.cervical_spine_examination') }}</h6>
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="scat_cervical_normal" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                                    <span class="text-sm text-purple-700">{{ __('health_records_edit.no_neck_pain_or_tenderness') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 4. Décision Médicale et Plan de Suivi -->
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('health_records_edit.k_4_medical_decision_and_follow_up_plan') }}</h4>
                                        <div class="space-y-4">
                                            <!-- Diagnostic / Décision -->
                                            <div>
                                                <label for="scat_diagnosis" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('health_records_edit.diagnosis_decision') }}
                                                </label>
                                                <select 
                                                    id="scat_diagnosis" 
                                                    name="scat_diagnosis" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                                >
                                                    <option value="">{{ __('health_records_edit.select_the_diagnosis') }}</option>
                                                    <option value="concussion_diagnosed" {{ old('scat_diagnosis') == 'concussion_diagnosed' ? 'selected' : '' }}>{{ __('health_records_edit.diagnosed_concussion') }}</option>
                                                    <option value="no_concussion" {{ old('scat_diagnosis') == 'no_concussion' ? 'selected' : '' }}>{{ __('health_records_edit.no_concussion') }}</option>
                                                    <option value="uncertain_surveillance" {{ old('scat_diagnosis') == 'uncertain_surveillance' ? 'selected' : '' }}>{{ __('health_records_edit.uncertain_diagnosis_monitoring_required') }}</option>
                                                </select>
                                            </div>

                                            <!-- Résumé et Plan de Suivi -->
                                            <div>
                                                <label for="scat_follow_up_plan" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('health_records_edit.summary_and_follow_up_plan') }}
                                                </label>
                                                <textarea 
                                                    id="scat_follow_up_plan" 
                                                    name="scat_follow_up_plan" 
                                                    rows="6"
                                                    placeholder="{{ __('health_records_edit.assessment_conclusions_graduated_return_') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                                >{{ old('scat_follow_up_plan') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
                                        <button type="button" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                            💾 {{ __('health_records_edit.save_assessment') }}
                                        </button>
                                        <button type="button" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                            🖨️ {{ __('health_records_edit.print_official_scat_report') }}
                                        </button>
                                        <button type="button" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                            📊 {{ __('health_records_edit.compare_with_baseline_assessment') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Imaging Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="mri_results" class="block text-sm font-medium text-gray-700 mb-2">
                                    🧠 {{ __('health_records_edit.mri_magnetic_resonance_imaging_loinc_187') }}
                                </label>
                                <textarea 
                                    id="mri_results" 
                                    name="mri_results" 
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="{{ __('health_records_edit.mri_results_anatomical_areas_pathologies') }}"
                                >{{ old('mri_results') }}</textarea>
                            </div>
                        </div>


                    </div>

                    <!-- Medical Imaging Upload Section -->
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-green-900">📷 {{ __('health_records_edit.medical_imaging') }}</h3>
                        </div>
                        <p class="text-green-700 mb-4">{{ __('health_records_edit.upload_and_analyze_medical_images_with_a') }}</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- ECG Upload -->
                            <div>
                                <label for="ecg_file" class="block text-sm font-medium text-gray-700 mb-2">
                                    📈 {{ __('health_records_edit.ecg_cardiogram') }}
                                </label>
                                <input 
                                    type="file" 
                                    id="ecg_file" 
                                    name="ecg_file"
                                    accept=".pdf,.jpg,.jpeg,.png,.dcm,.bmp,.tiff,.tif"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                >
                                <p class="text-xs text-gray-500 mt-1">Formats: PDF, JPG, PNG, DICOM, BMP, TIFF</p>
                            </div>
                            
                            <!-- MRI Upload -->
                            <div>
                                <label for="mri_files" class="block text-sm font-medium text-gray-700 mb-2">
                                    🧠 {{ __('health_records_edit.mri_ct_scan_multiple') }}
                                </label>
                                <input 
                                    type="file" 
                                    id="mri_files" 
                                    name="mri_files[]"
                                    multiple
                                    accept=".pdf,.jpg,.jpeg,.png,.dcm,.bmp,.tiff,.tif"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                >
                                <p class="text-xs text-gray-500 mt-1">Formats: PDF, JPG, PNG, DICOM, BMP, TIFF {{ __('health_records_edit.select_multiple_files') }}</p>
                                <div id="mri-files-preview" class="mt-2 space-y-1"></div>
                            </div>
                            
                            <!-- CT Scan Upload -->
                            <div>
                                <label for="ct_files" class="block text-sm font-medium text-gray-700 mb-2">
                                    🏥 {{ __('health_records_edit.ct_scan_multiple') }}
                                </label>
                                <input 
                                    type="file" 
                                    id="ct_files" 
                                    name="ct_files[]"
                                    multiple
                                    accept=".pdf,.jpg,.jpeg,.png,.dcm,.bmp,.tiff,.tif"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                >
                                <p class="text-xs text-gray-500 mt-1">Formats: PDF, JPG, PNG, DICOM, BMP, TIFF {{ __('health_records_edit.select_multiple_files') }}</p>
                                <div id="ct-files-preview" class="mt-2 space-y-1"></div>
                            </div>
                            
                            <!-- X-Ray Upload -->
                            <div>
                                <label for="xray_file" class="block text-sm font-medium text-gray-700 mb-2">
                                    🦴 {{ __('health_records_edit.x_ray') }}
                                </label>
                                <input 
                                    type="file" 
                                    id="xray_file" 
                                    name="xray_file"
                                    accept=".pdf,.jpg,.jpeg,.png,.dcm"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                >
                                <p class="text-xs text-gray-500 mt-1">Formats: PDF, JPG, PNG, DICOM</p>
                            </div>
                        </div>

                        <!-- ECG d'Effort et Scintigraphie Modules -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ECG d'Effort Module -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-green-800 mb-3">📈 {{ __('health_records_edit.exercise_ecg_loinc_11524_6') }}</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label for="ecg_effort_date" class="block text-xs font-medium text-gray-600 mb-1">
                                            {{ __('health_records_edit.examination_date') }}
                                        </label>
                                        <input 
                                            type="date" 
                                            id="ecg_effort_date" 
                                            name="ecg_effort_date" 
                                            value="{{ old('ecg_effort_date', date('Y-m-d')) }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                        >
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="ecg_effort_max_fc" class="block text-xs font-medium text-gray-600 mb-1">
                                                {{ __('health_records_edit.max_heart_rate_bpm') }}
                                            </label>
                                            <input 
                                                type="number" 
                                                id="ecg_effort_max_fc" 
                                                name="ecg_effort_max_fc" 
                                                value="{{ old('ecg_effort_max_fc') }}"
                                                placeholder="180"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                            >
                                        </div>
                                        <div>
                                            <label for="ecg_effort_duration" class="block text-xs font-medium text-gray-600 mb-1">
                                                {{ __('health_records_edit.test_duration_minutes') }}
                                            </label>
                                            <input 
                                                type="number" 
                                                id="ecg_effort_duration" 
                                                name="ecg_effort_duration" 
                                                value="{{ old('ecg_effort_duration') }}"
                                                placeholder="12"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                            >
                                        </div>
                                    </div>
                                    <div>
                                        <label for="ecg_effort_file" class="block text-xs font-medium text-gray-600 mb-1">
                                            📁 {{ __('health_records_edit.choose_an_ecg_file') }}
                                        </label>
                                        <input 
                                            type="file" 
                                            id="ecg_effort_file" 
                                            name="ecg_effort_file"
                                            accept=".pdf,.jpg,.jpeg,.png,.gif,.bmp,.tiff,.tif,.dcm,.svg,.webp"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                        >
                                        <p class="text-xs text-gray-500 mt-1">{{ __('health_records_edit.accepted_formats_pdf_jpg_png_gif_bmp_tif') }}</p>
                                    </div>
                                    <div>
                                        <label for="ecg_effort_results" class="block text-xs font-medium text-gray-600 mb-1">
                                            {{ __('health_records_edit.results_and_abnormalities') }}
                                        </label>
                                        <textarea 
                                            id="ecg_effort_results" 
                                            name="ecg_effort_results" 
                                            rows="3"
                                            placeholder="{{ __('health_records_edit.exercise_stress_test_max_heart_rate_abno') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                        >{{ old('ecg_effort_results') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Scintigraphie Module -->
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-orange-800 mb-3">☢️ {{ __('health_records_edit.scintigraphy_loinc_18748_4') }}</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label for="scintigraphy_date" class="block text-xs font-medium text-gray-600 mb-1">
                                            {{ __('health_records_edit.examination_date') }}
                                        </label>
                                        <input 
                                            type="date" 
                                            id="scintigraphy_date" 
                                            name="scintigraphy_date" 
                                            value="{{ old('scintigraphy_date', date('Y-m-d')) }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
                                        >
                                    </div>
                                    <div>
                                        <label for="scintigraphy_type" class="block text-xs font-medium text-gray-600 mb-1">
                                            {{ __('health_records_edit.scintigraphy_type') }}
                                        </label>
                                        <select 
                                            id="scintigraphy_type" 
                                            name="scintigraphy_type" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
                                        >
                                            <option value="">{{ __('health_records_edit.select_the_type') }}</option>
                                            <option value="osseuse" {{ old('scintigraphy_type') == 'osseuse' ? 'selected' : '' }}>{{ __('health_records_edit.bone_scintigraphy') }}</option>
                                            <option value="myocardique" {{ old('scintigraphy_type') == 'myocardique' ? 'selected' : '' }}>{{ __('health_records_edit.myocardial_scintigraphy') }}</option>
                                            <option value="pulmonaire" {{ old('scintigraphy_type') == 'pulmonaire' ? 'selected' : '' }}>{{ __('health_records_edit.lung_scintigraphy') }}</option>
                                            <option value="renale" {{ old('scintigraphy_type') == 'renale' ? 'selected' : '' }}>{{ __('health_records_edit.renal_scintigraphy') }}</option>
                                            <option value="thyroidienne" {{ old('scintigraphy_type') == 'thyroidienne' ? 'selected' : '' }}>{{ __('health_records_edit.thyroid_scintigraphy') }}</option>
                                            <option value="autre" {{ old('scintigraphy_type') == 'autre' ? 'selected' : '' }}>{{ __('health_records_edit.other') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="scintigraphy_file" class="block text-xs font-medium text-gray-600 mb-1">
                                            📁 {{ __('health_records_edit.choose_a_scintigraphy_file') }}
                                        </label>
                                        <input 
                                            type="file" 
                                            id="scintigraphy_file" 
                                            name="scintigraphy_file"
                                            accept=".pdf,.jpg,.jpeg,.png,.gif,.bmp,.tiff,.tif,.dcm,.svg,.webp"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
                                        >
                                        <p class="text-xs text-gray-500 mt-1">{{ __('health_records_edit.accepted_formats_pdf_jpg_png_gif_bmp_tif') }}</p>
                                    </div>
                                    <div>
                                        <label for="scintigraphy_results" class="block text-xs font-medium text-gray-600 mb-1">
                                            {{ __('health_records_edit.results_and_interpretation') }}
                                        </label>
                                        <textarea 
                                            id="scintigraphy_results" 
                                            name="scintigraphy_results" 
                                            rows="3"
                                            placeholder="{{ __('health_records_edit.bone_scan_myocardial_scan_other_investig') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
                                        >{{ old('scintigraphy_results') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Analysis Section -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-purple-900">🤖 {{ __('health_records_edit.medical_ai_analysis') }}</h3>
                        </div>
                        <p class="text-purple-700 mb-4">{{ __('health_records_edit.automatically_analyze_medical_images_wit') }}</p>
                        
                        <div class="flex flex-wrap gap-3 mb-4">
                            <button 
                                type="button" 
                                id="ai-check-ecg-btn"
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors flex items-center"
                            >
                                🔍 {{ __('health_records_edit.analyze_ecg') }}
                            </button>
                            <button 
                                type="button" 
                                id="ai-check-ecg-effort-btn"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center"
                            >
                                📈 {{ __('health_records_edit.analyze_exercise_ecg') }}
                            </button>
                            <button 
                                type="button" 
                                id="ai-check-scintigraphy-btn"
                                class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors flex items-center"
                            >
                                ☢️ {{ __('health_records_edit.analyze_scintigraphy') }}
                            </button>
                            <button 
                                type="button" 
                                id="ai-check-scat-btn"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors flex items-center"
                            >
                                🧠 {{ __('health_records_edit.analyze_scat') }}
                            </button>
                            <button 
                                type="button" 
                                id="ai-check-mri-btn"
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors flex items-center"
                            >
                                🧠 {{ __('health_records_edit.analyze_mri') }}
                            </button>
                            <button 
                                type="button" 
                                id="ai-check-ct-btn"
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors flex items-center"
                            >
                                🏥 {{ __('health_records_edit.analyze_ct') }}
                            </button>
                            <button 
                                type="button" 
                                id="ai-check-xray-btn"
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors flex items-center"
                            >
                                🦴 {{ __('health_records_edit.analyze_x_ray') }}
                            </button>
                            <button 
                                type="button" 
                                id="ai-check-all-btn"
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors flex items-center"
                            >
                                🚀 {{ __('health_records_edit.full_analysis') }}
                            </button>
                        </div>
                        
                        <div id="ai-analysis-status" class="hidden mb-4">
                            <div class="flex items-center">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-600 mr-2"></div>
                                <span class="text-purple-700">{{ __('health_records_edit.analysis_in_progress_2') }}</span>
                            </div>
                        </div>
                        
                        <div id="ai-analysis-results" class="hidden bg-white border border-purple-200 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-purple-900 mb-3">{{ __('health_records_edit.ai_analysis_results') }}</h4>
                            <div id="ai-results-content" class="text-sm text-gray-700"></div>
                            <div id="hl7-cda-status" class="hidden mt-3">
                                <div class="flex items-center">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                                    <span class="text-blue-700">{{ __('health_records_edit.generating_hl7_cda_report') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DICOM Viewer Section -->
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-green-900">🔍 {{ __('health_records_edit.dicom_viewer') }}</h3>
                        </div>
                        <p class="text-green-700 mb-4">{{ __('health_records_edit.view_and_analyze_medical_images') }}</p>
                        
                        <div class="mb-4">
                            <label for="dicom-viewer-select" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.select_a_file_to_view') }}
                            </label>
                            <select 
                                id="dicom-viewer-select" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            >
                                <option value="">{{ __('health_records_edit.choose_a_file') }}</option>
                                <option value="ecg">{{ __('health_records_edit.ecg_cardiogram') }}</option>
                                <option value="mri">{{ __('health_records_edit.mri_ct_scan') }}</option>
                                <option value="xray">{{ __('health_records_edit.x_ray') }}</option>
                            </select>
                        </div>
                        
                        <!-- Viewer Controls -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button type="button" id="dicom-zoom-in" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                🔍+
                            </button>
                            <button type="button" id="dicom-zoom-out" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                🔍-
                            </button>
                            <button type="button" id="dicom-reset" class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700">
                                🔄 Reset
                            </button>
                            <button type="button" id="dicom-fullscreen" class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                                ⛶ {{ __('health_records_edit.full_screen') }}
                            </button>
                        </div>
                        
                        <!-- Measurement Tools -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button type="button" id="dicom-measure-distance" class="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">
                                📏 Distance
                            </button>
                            <button type="button" id="dicom-measure-angle" class="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">
                                📐 Angle
                            </button>
                            <button type="button" id="dicom-measure-surface" class="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">
                                📊 {{ __('health_records_edit.area') }}
                            </button>
                        </div>
                        
                        <!-- Viewer Canvas -->
                        <div class="border border-gray-300 rounded-lg overflow-hidden bg-gray-100" style="height: 400px;">
                            <div id="dicom-loading" class="hidden flex items-center justify-center h-full">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                                    <p class="text-gray-600">{{ __('health_records_edit.loading_image') }}</p>
                                </div>
                            </div>
                            
                            <div id="dicom-error" class="hidden flex items-center justify-center h-full">
                                <div class="text-center text-red-600">
                                    <p>{{ __('health_records_edit.error_loading_image') }}</p>
                                </div>
                            </div>
                            
                            <div id="dicom-placeholder" class="flex items-center justify-center h-full">
                                <div class="text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p>{{ __('health_records_edit.select_a_file_to_start_viewing') }}</p>
                                </div>
                            </div>
                            
                            <canvas id="dicom-canvas" class="hidden w-full h-full cursor-crosshair"></canvas>
                        </div>
                        
                        <!-- Metadata and Measurements -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div id="dicom-metadata" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">{{ __('health_records_edit.metadata') }}</h4>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <div><strong>Type:</strong> <span id="dicom-file-type">-</span></div>
                                    <div><strong>Dimensions:</strong> <span id="dicom-image-dimensions">-</span></div>
                                    <div><strong>Format:</strong> <span id="dicom-format">-</span></div>
                                    <div><strong>{{ __('health_records_edit.size') }}</strong> <span id="dicom-file-size">-</span></div>
                                </div>
                            </div>
                            
                            <div id="dicom-measurements" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">{{ __('health_records_edit.measurements') }}</h4>
                                <div id="dicom-measurements-content" class="text-xs text-gray-600">
                                    <p>{{ __('health_records_edit.no_measurement_taken') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnosis and Treatment -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.diagnosis') }}
                            </label>
                            <textarea 
                                id="diagnosis" 
                                name="diagnosis" 
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('health_records_edit.diagnosis_established') }}"
                            >{{ old('diagnosis') }}</textarea>
                        </div>
                        
                        <div>
                            <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.treatment_plan') }}
                            </label>
                            <textarea 
                                id="treatment_plan" 
                                name="treatment_plan" 
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('health_records_edit.recommended_treatment_plan') }}"
                            >{{ old('treatment_plan') }}</textarea>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="record_date" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.consultation_date') }}
                            </label>
                            <input 
                                type="date" 
                                id="record_date" 
                                name="record_date" 
                                value="{{ old('record_date', $healthRecord->record_date ? $healthRecord->record_date->format('Y-m-d') : date('Y-m-d')) }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        
                        <div>
                            <label for="next_checkup_date" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.next_appointment') }}
                            </label>
                            <input 
                                type="date" 
                                id="next_checkup_date" 
                                name="next_checkup_date" 
                                value="{{ old('next_checkup_date') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional EMR Fields -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">📋 {{ __('health_records_edit.additional_visit_information') }}</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Physical Examination -->
                    <div>
                        <label for="physical_examination" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_edit.physical_examination') }}
                        </label>
                        <textarea 
                            id="physical_examination" 
                            name="physical_examination" 
                            rows="4"
                            placeholder="{{ __('health_records_edit.physical_examination_details') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >{{ old('physical_examination') }}</textarea>
                    </div>

                    <!-- Laboratory Results -->
                    <div>
                        <label for="laboratory_results" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_edit.laboratory_results') }}
                        </label>
                        <textarea 
                            id="laboratory_results" 
                            name="laboratory_results" 
                            rows="4"
                            placeholder="{{ __('health_records_edit.laboratory_test_results') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >{{ old('laboratory_results') }}</textarea>
                    </div>

                    <!-- Imaging Results -->
                    <div>
                        <label for="imaging_results" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_edit.imaging_results') }}
                        </label>
                        <textarea 
                            id="imaging_results" 
                            name="imaging_results" 
                            rows="4"
                            placeholder="{{ __('health_records_edit.imaging_examination_results_x_ray_ultras') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >{{ old('imaging_results') }}</textarea>
                    </div>

                    <!-- Prescriptions -->
                    <div>
                        <label for="prescriptions" class="block text-sm font-medium text-gray-700 mb-2">
                            Prescriptions
                        </label>
                        <textarea 
                            id="prescriptions" 
                            name="prescriptions" 
                            rows="4"
                            placeholder="{{ __('health_records_edit.prescribed_medications_and_dosage') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >{{ old('prescriptions') }}</textarea>
                    </div>

                    <!-- Follow-up Instructions -->
                    <div>
                        <label for="follow_up_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_edit.follow_up_instructions') }}
                        </label>
                        <textarea 
                            id="follow_up_instructions" 
                            name="follow_up_instructions" 
                            rows="4"
                            placeholder="{{ __('health_records_edit.instructions_for_follow_up_and_next_step') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >{{ old('follow_up_instructions') }}</textarea>
                    </div>

                    <!-- Visit Notes -->
                    <div>
                        <label for="visit_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_edit.visit_notes') }}
                        </label>
                        <textarea 
                            id="visit_notes" 
                            name="visit_notes" 
                            rows="4"
                            placeholder="{{ __('health_records_edit.additional_visit_notes') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >{{ old('visit_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('health-records.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    {{ __('health_records_edit.back_to_list') }}
                </a>
                
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                    💾 {{ __('health_records_edit.update_medical_record') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const EDIT_LABELS = {
    error_during_ai_analysis_colon: @json(__('health_records_edit.error_during_ai_analysis_colon')),
    a_g_ratio: @json(__('health_records_edit.a_g_ratio')),
    abnormalities_detected: @json(__('health_records_edit.abnormalities_detected')),
    abnormalities_detected_medical_consultat: @json(__('health_records_edit.abnormalities_detected_medical_consultat')),
    age_difference: @json(__('health_records_edit.age_difference')),
    ai_analysis: @json(__('health_records_edit.ai_analysis')),
    ai_analysis_completed: @json(__('health_records_edit.ai_analysis_completed')),
    ai_data_application_feature_is_under_dev: @json(__('health_records_edit.ai_data_application_feature_is_under_dev')),
    ai_scat_analysis: @json(__('health_records_edit.ai_scat_analysis')),
    alanine_aminotransferase: @json(__('health_records_edit.alanine_aminotransferase')),
    albumin: @json(__('health_records_edit.albumin')),
    albumin_globulin_ratio: @json(__('health_records_edit.albumin_globulin_ratio')),
    alkaline_phosphatase: @json(__('health_records_edit.alkaline_phosphatase')),
    all_tests_from_the_complete_panel_58410_: @json(__('health_records_edit.all_tests_from_the_complete_panel_58410_')),
    analysis_in_progress: @json(__('health_records_edit.analysis_in_progress')),
    analysis_in_progress_2: @json(__('health_records_edit.analysis_in_progress_2')),
    analysis_results: @json(__('health_records_edit.analysis_results')),
    analysis_summary: @json(__('health_records_edit.analysis_summary')),
    analysis_type: @json(__('health_records_edit.analysis_type')),
    analyze_ct: @json(__('health_records_edit.analyze_ct')),
    analyze_ecg: @json(__('health_records_edit.analyze_ecg')),
    analyze_exercise_ecg: @json(__('health_records_edit.analyze_exercise_ecg')),
    analyze_mri: @json(__('health_records_edit.analyze_mri')),
    analyze_scat: @json(__('health_records_edit.analyze_scat')),
    analyze_scintigraphy: @json(__('health_records_edit.analyze_scintigraphy')),
    analyze_with_ai: @json(__('health_records_edit.analyze_with_ai')),
    analyze_x_ray: @json(__('health_records_edit.analyze_x_ray')),
    analyzing_ct: @json(__('health_records_edit.analyzing_ct')),
    analyzing_ecg: @json(__('health_records_edit.analyzing_ecg')),
    analyzing_exercise_ecg: @json(__('health_records_edit.analyzing_exercise_ecg')),
    analyzing_mri: @json(__('health_records_edit.analyzing_mri')),
    analyzing_scat: @json(__('health_records_edit.analyzing_scat')),
    analyzing_scintigraphy: @json(__('health_records_edit.analyzing_scintigraphy')),
    analyzing_x_ray: @json(__('health_records_edit.analyzing_x_ray')),
    anterior: @json(__('health_records_edit.anterior')),
    anterior_view: @json(__('health_records_edit.anterior_view')),
    anti_double_stranded_dna_antibodies: @json(__('health_records_edit.anti_double_stranded_dna_antibodies')),
    anti_la_ssb_antibodies: @json(__('health_records_edit.anti_la_ssb_antibodies')),
    anti_mitochondrial_antibodies: @json(__('health_records_edit.anti_mitochondrial_antibodies')),
    anti_ro_ssa_antibodies: @json(__('health_records_edit.anti_ro_ssa_antibodies')),
    anti_sm_antibodies: @json(__('health_records_edit.anti_sm_antibodies')),
    anti_smooth_muscle_antibodies: @json(__('health_records_edit.anti_smooth_muscle_antibodies')),
    antinuclear_antibodies: @json(__('health_records_edit.antinuclear_antibodies')),
    api_error: @json(__('health_records_edit.api_error')),
    apolipoprotein_a: @json(__('health_records_edit.apolipoprotein_a')),
    apolipoprotein_b: @json(__('health_records_edit.apolipoprotein_b')),
    apply_ai_data_to_fields: @json(__('health_records_edit.apply_ai_data_to_fields')),
    area: @json(__('health_records_edit.area')),
    arthritis: @json(__('health_records_edit.arthritis')),
    aspartate_aminotransferase: @json(__('health_records_edit.aspartate_aminotransferase')),
    authentication_error_please_log_in_again: @json(__('health_records_edit.authentication_error_please_log_in_again')),
    autoimmunity_markers: @json(__('health_records_edit.autoimmunity_markers')),
    b_type_natriuretic_peptide: @json(__('health_records_edit.b_type_natriuretic_peptide')),
    basic_metabolic_panel_loinc_58409_4: @json(__('health_records_edit.basic_metabolic_panel_loinc_58409_4')),
    basic_panel_see_details_below: @json(__('health_records_edit.basic_panel_see_details_below')),
    basophils: @json(__('health_records_edit.basophils')),
    biochemistry: @json(__('health_records_edit.biochemistry')),
    blood_urea_nitrogen: @json(__('health_records_edit.blood_urea_nitrogen')),
    blood_urea_nitrogen_2: @json(__('health_records_edit.blood_urea_nitrogen_2')),
    blood_urea_nitrogen_7_20_mg_dl: @json(__('health_records_edit.blood_urea_nitrogen_7_20_mg_dl')),
    bone_age: @json(__('health_records_edit.bone_age')),
    bone_density: @json(__('health_records_edit.bone_density')),
    bone_structure: @json(__('health_records_edit.bone_structure')),
    c_reactive_protein: @json(__('health_records_edit.c_reactive_protein')),
    cardiac_markers: @json(__('health_records_edit.cardiac_markers')),
    caries: @json(__('health_records_edit.caries')),
    ceruloplasmin: @json(__('health_records_edit.ceruloplasmin')),
    chloride: @json(__('health_records_edit.chloride')),
    chloride_96_106_meq_l: @json(__('health_records_edit.chloride_96_106_meq_l')),
    chronological_age: @json(__('health_records_edit.chronological_age')),
    click_on_multiple_points_to_draw_a_shape: @json(__('health_records_edit.click_on_multiple_points_to_draw_a_shape')),
    click_on_the_image_to_add_markers: @json(__('health_records_edit.click_on_the_image_to_add_markers')),
    click_on_three_points_to_measure_the_ang: @json(__('health_records_edit.click_on_three_points_to_measure_the_ang')),
    click_on_two_points_to_measure_the_dista: @json(__('health_records_edit.click_on_two_points_to_measure_the_dista')),
    clinical_notes: @json(__('health_records_edit.clinical_notes')),
    complete_cbc: @json(__('health_records_edit.complete_cbc')),
    complete_panel_see_details_below: @json(__('health_records_edit.complete_panel_see_details_below')),
    complex_data_see_technical_details: @json(__('health_records_edit.complex_data_see_technical_details')),
    comprehensive_metabolic_panel_loinc_5841: @json(__('health_records_edit.comprehensive_metabolic_panel_loinc_5841')),
    confidence: @json(__('health_records_edit.confidence')),
    connection_error: @json(__('health_records_edit.connection_error')),
    copper: @json(__('health_records_edit.copper')),
    creatine_kinase: @json(__('health_records_edit.creatine_kinase')),
    creatine_kinase_mb: @json(__('health_records_edit.creatine_kinase_mb')),
    creatine_phosphokinase: @json(__('health_records_edit.creatine_phosphokinase')),
    creatine_phosphokinase_30_200_u_l: @json(__('health_records_edit.creatine_phosphokinase_30_200_u_l')),
    creatinine: @json(__('health_records_edit.creatinine')),
    creatinine_0_6_1_2_mg_dl_f_0_8_1_3_mg_dl: @json(__('health_records_edit.creatinine_0_6_1_2_mg_dl_f_0_8_1_3_mg_dl')),
    critical: @json(__('health_records_edit.critical')),
    critically_high: @json(__('health_records_edit.critically_high')),
    critically_low_2: @json(__('health_records_edit.critically_low_2')),
    cyclic_citrullinated_peptide: @json(__('health_records_edit.cyclic_citrullinated_peptide')),
    delete: @json(__('health_records_edit.delete')),
    dental_tooth_word: @json(__('health_records_edit.dental_tooth_word')),
    depending_on_substance: @json(__('health_records_edit.depending_on_substance')),
    depending_on_the_test: @json(__('health_records_edit.depending_on_the_test')),
    diagnosis: @json(__('health_records_edit.diagnosis')),
    diagnosis_2: @json(__('health_records_edit.diagnosis_2')),
    dicom_metadata: @json(__('health_records_edit.dicom_metadata')),
    direct_bilirubin: @json(__('health_records_edit.direct_bilirubin')),
    dislocations: @json(__('health_records_edit.dislocations')),
    download_xml: @json(__('health_records_edit.download_xml')),
    end_ai_analysis: @json(__('health_records_edit.end_ai_analysis')),
    eosinophils: @json(__('health_records_edit.eosinophils')),
    error_during_ct_analysis: @json(__('health_records_edit.error_during_ct_analysis')),
    error_during_ecg_analysis: @json(__('health_records_edit.error_during_ecg_analysis')),
    error_during_exercise_ecg_analysis: @json(__('health_records_edit.error_during_exercise_ecg_analysis')),
    error_during_full_analysis: @json(__('health_records_edit.error_during_full_analysis')),
    error_during_generation: @json(__('health_records_edit.error_during_generation')),
    error_during_mri_analysis: @json(__('health_records_edit.error_during_mri_analysis')),
    error_during_scat_analysis: @json(__('health_records_edit.error_during_scat_analysis')),
    error_during_scintigraphy_analysis: @json(__('health_records_edit.error_during_scintigraphy_analysis')),
    error_during_x_ray_analysis: @json(__('health_records_edit.error_during_x_ray_analysis')),
    error_generating_hl7_cda_report: @json(__('health_records_edit.error_generating_hl7_cda_report')),
    error_message: @json(__('health_records_edit.error_message')),
    erythrocyte_sedimentation_rate: @json(__('health_records_edit.erythrocyte_sedimentation_rate')),
    extended_metabolic_panel_loinc_58408_6: @json(__('health_records_edit.extended_metabolic_panel_loinc_58408_6')),
    extended_panel_see_details_below: @json(__('health_records_edit.extended_panel_see_details_below')),
    extracted_data: @json(__('health_records_edit.extracted_data')),
    ferritin: @json(__('health_records_edit.ferritin')),
    ferritin_13_150_ng_ml_f_30_400_ng_ml_m: @json(__('health_records_edit.ferritin_13_150_ng_ml_f_30_400_ng_ml_m')),
    file_word: @json(__('health_records_edit.file_word')),
    folic_acid: @json(__('health_records_edit.folic_acid')),
    free_t3: @json(__('health_records_edit.free_t3')),
    free_t3_2_3_4_2_pg_ml: @json(__('health_records_edit.free_t3_2_3_4_2_pg_ml')),
    free_t4: @json(__('health_records_edit.free_t4')),
    free_t4_0_8_1_8_ng_dl: @json(__('health_records_edit.free_t4_0_8_1_8_ng_dl')),
    full_analysis: @json(__('health_records_edit.full_analysis')),
    full_analysis_in_progress: @json(__('health_records_edit.full_analysis_in_progress')),
    gamma_glutamyl_transferase: @json(__('health_records_edit.gamma_glutamyl_transferase')),
    generate_hl7_cda: @json(__('health_records_edit.generate_hl7_cda')),
    generating: @json(__('health_records_edit.generating')),
    globulins: @json(__('health_records_edit.globulins')),
    glucose_70_100_mg_dl_fasting: @json(__('health_records_edit.glucose_70_100_mg_dl_fasting')),
    glycated_hemoglobin: @json(__('health_records_edit.glycated_hemoglobin')),
    growth_hormone: @json(__('health_records_edit.growth_hormone')),
    hdl_cholesterol: @json(__('health_records_edit.hdl_cholesterol')),
    heart_rate: @json(__('health_records_edit.heart_rate')),
    hematocrit: @json(__('health_records_edit.hematocrit')),
    hematology: @json(__('health_records_edit.hematology')),
    hemoglobin: @json(__('health_records_edit.hemoglobin')),
    hemoglobin_12_0_16_0_g_dl_f_14_0_18_0_g_: @json(__('health_records_edit.hemoglobin_12_0_16_0_g_dl_f_14_0_18_0_g_')),
    high: @json(__('health_records_edit.high')),
    high_sensitivity_crp: @json(__('health_records_edit.high_sensitivity_crp')),
    high_sensitivity_crp_1_0_mg_l: @json(__('health_records_edit.high_sensitivity_crp_1_0_mg_l')),
    hl7_cda_report_generated_successfully: @json(__('health_records_edit.hl7_cda_report_generated_successfully')),
    homocysteine: @json(__('health_records_edit.homocysteine')),
    includes_biochemistry_electrolytes: @json(__('health_records_edit.includes_biochemistry_electrolytes')),
    includes_cbc_biochemistry_lipids_liver_e: @json(__('health_records_edit.includes_cbc_biochemistry_lipids_liver_e')),
    includes_complete_cardiac_markers_hormon: @json(__('health_records_edit.includes_complete_cardiac_markers_hormon')),
    indirect_bilirubin: @json(__('health_records_edit.indirect_bilirubin')),
    inflammatory_markers: @json(__('health_records_edit.inflammatory_markers')),
    insulin: @json(__('health_records_edit.insulin')),
    insulin_3_25_iu_ml: @json(__('health_records_edit.insulin_3_25_iu_ml')),
    interleukin_6: @json(__('health_records_edit.interleukin_6')),
    iron_saturation: @json(__('health_records_edit.iron_saturation')),
    joint_alignment: @json(__('health_records_edit.joint_alignment')),
    k_70_100_fasting: @json(__('health_records_edit.k_70_100_fasting')),
    k_70_100_fasting_2: @json(__('health_records_edit.k_70_100_fasting_2')),
    lactate_dehydrogenase: @json(__('health_records_edit.lactate_dehydrogenase')),
    lactate_dehydrogenase_140_280_u_l: @json(__('health_records_edit.lactate_dehydrogenase_140_280_u_l')),
    lateral: @json(__('health_records_edit.lateral')),
    lateral_view: @json(__('health_records_edit.lateral_view')),
    ldl_cholesterol: @json(__('health_records_edit.ldl_cholesterol')),
    lipids: @json(__('health_records_edit.lipids')),
    lipoprotein_a: @json(__('health_records_edit.lipoprotein_a')),
    liver_enzymes: @json(__('health_records_edit.liver_enzymes')),
    low: @json(__('health_records_edit.low')),
    low_2: @json(__('health_records_edit.low_2')),
    lower_dentition: @json(__('health_records_edit.lower_dentition')),
    magnesium: @json(__('health_records_edit.magnesium')),
    magnesium_1_5_2_5_mg_dl: @json(__('health_records_edit.magnesium_1_5_2_5_mg_dl')),
    marker: @json(__('health_records_edit.marker')),
    mean_corpuscular_hemoglobin: @json(__('health_records_edit.mean_corpuscular_hemoglobin')),
    mean_corpuscular_hemoglobin_concentratio_2: @json(__('health_records_edit.mean_corpuscular_hemoglobin_concentratio_2')),
    mean_corpuscular_volume: @json(__('health_records_edit.mean_corpuscular_volume')),
    mean_platelet_volume: @json(__('health_records_edit.mean_platelet_volume')),
    measured_value: @json(__('health_records_edit.measured_value')),
    measurements: @json(__('health_records_edit.measurements')),
    measurements_2: @json(__('health_records_edit.measurements_2')),
    med_gemini_analysis_results: @json(__('health_records_edit.med_gemini_analysis_results')),
    med_gemini_api_error: @json(__('health_records_edit.med_gemini_api_error')),
    metadata: @json(__('health_records_edit.metadata')),
    missing: @json(__('health_records_edit.missing')),
    model: @json(__('health_records_edit.model')),
    neutrophils: @json(__('health_records_edit.neutrophils')),
    no_analysis_available_please_run_an_ai_a: @json(__('health_records_edit.no_analysis_available_please_run_an_ai_a')),
    non_hdl_cholesterol: @json(__('health_records_edit.non_hdl_cholesterol')),
    none: @json(__('health_records_edit.none')),
    normal_examination_no_abnormalities_dete: @json(__('health_records_edit.normal_examination_no_abnormalities_dete')),
    normal_range: @json(__('health_records_edit.normal_range')),
    not_available: @json(__('health_records_edit.not_available')),
    not_detectable: @json(__('health_records_edit.not_detectable')),
    not_detected: @json(__('health_records_edit.not_detected')),
    pdf_viewing_is_under_development: @json(__('health_records_edit.pdf_viewing_is_under_development')),
    phosphorus: @json(__('health_records_edit.phosphorus')),
    phosphorus_2_5_4_5_mg_dl: @json(__('health_records_edit.phosphorus_2_5_4_5_mg_dl')),
    platelets: @json(__('health_records_edit.platelets')),
    please_enter_clinical_notes_for_the_ai_a: @json(__('health_records_edit.please_enter_clinical_notes_for_the_ai_a')),
    please_load_an_image_first: @json(__('health_records_edit.please_load_an_image_first')),
    please_select_a_file_first: @json(__('health_records_edit.please_select_a_file_first')),
    please_select_a_scintigraphy_file_for_an: @json(__('health_records_edit.please_select_a_scintigraphy_file_for_an')),
    please_select_an_ecg_file_for_analysis: @json(__('health_records_edit.please_select_an_ecg_file_for_analysis')),
    please_select_an_exercise_ecg_file_for_a: @json(__('health_records_edit.please_select_an_exercise_ecg_file_for_a')),
    please_select_an_x_ray_file_for_analysis: @json(__('health_records_edit.please_select_an_x_ray_file_for_analysis')),
    please_select_at_least_one_ct_file_for_a: @json(__('health_records_edit.please_select_at_least_one_ct_file_for_a')),
    please_select_at_least_one_medical_file_: @json(__('health_records_edit.please_select_at_least_one_medical_file_')),
    please_select_at_least_one_mri_file_for_: @json(__('health_records_edit.please_select_at_least_one_mri_file_for_')),
    posterior: @json(__('health_records_edit.posterior')),
    posterior_view: @json(__('health_records_edit.posterior_view')),
    processing_time: @json(__('health_records_edit.processing_time')),
    progesterone: @json(__('health_records_edit.progesterone')),
    prolactin: @json(__('health_records_edit.prolactin')),
    recommendations: @json(__('health_records_edit.recommendations')),
    red_blood_cells: @json(__('health_records_edit.red_blood_cells')),
    red_cell_distribution_width: @json(__('health_records_edit.red_cell_distribution_width')),
    report_id: @json(__('health_records_edit.report_id')),
    restoration: @json(__('health_records_edit.restoration')),
    rheumatoid_factor: @json(__('health_records_edit.rheumatoid_factor')),
    rhythm: @json(__('health_records_edit.rhythm')),
    saved_in_the_player_s_medical_record: @json(__('health_records_edit.saved_in_the_player_s_medical_record')),
    select: @json(__('health_records_edit.select')),
    select_2: @json(__('health_records_edit.select_2')),
    selenium: @json(__('health_records_edit.selenium')),
    serum_iron: @json(__('health_records_edit.serum_iron')),
    serum_iron_60_170_g_dl: @json(__('health_records_edit.serum_iron_60_170_g_dl')),
    skeletal_maturity: @json(__('health_records_edit.skeletal_maturity')),
    status: @json(__('health_records_edit.status')),
    status_2: @json(__('health_records_edit.status_2')),
    status_analysis_word: @json(__('health_records_edit.status_analysis_word')),
    status_completed_word: @json(__('health_records_edit.status_completed_word')),
    testosterone: @json(__('health_records_edit.testosterone')),
    testosterone_300_1000_ng_dl: @json(__('health_records_edit.testosterone_300_1000_ng_dl')),
    tests_included: @json(__('health_records_edit.tests_included')),
    total_bilirubin: @json(__('health_records_edit.total_bilirubin')),
    total_bilirubin_0_3_1_2_mg_dl: @json(__('health_records_edit.total_bilirubin_0_3_1_2_mg_dl')),
    total_cholesterol: @json(__('health_records_edit.total_cholesterol')),
    total_cholesterol_200_mg_dl: @json(__('health_records_edit.total_cholesterol_200_mg_dl')),
    total_cholesterol_hdl_ratio: @json(__('health_records_edit.total_cholesterol_hdl_ratio')),
    total_co2: @json(__('health_records_edit.total_co2')),
    total_co2_22_28_meq_l: @json(__('health_records_edit.total_co2_22_28_meq_l')),
    total_iron_binding_capacity: @json(__('health_records_edit.total_iron_binding_capacity')),
    total_protein: @json(__('health_records_edit.total_protein')),
    transferrin: @json(__('health_records_edit.transferrin')),
    transferrin_200_400_mg_dl: @json(__('health_records_edit.transferrin_200_400_mg_dl')),
    triglycerides: @json(__('health_records_edit.triglycerides')),
    triglycerides_150_mg_dl: @json(__('health_records_edit.triglycerides_150_mg_dl')),
    troponin: @json(__('health_records_edit.troponin')),
    troponin_0_04_ng_ml: @json(__('health_records_edit.troponin_0_04_ng_ml')),
    tumor_markers: @json(__('health_records_edit.tumor_markers')),
    unit: @json(__('health_records_edit.unit')),
    unknown_error: @json(__('health_records_edit.unknown_error')),
    unsaturated_iron_binding_capacity: @json(__('health_records_edit.unsaturated_iron_binding_capacity')),
    unsupported_file_format: @json(__('health_records_edit.unsupported_file_format')),
    upper_dentition: @json(__('health_records_edit.upper_dentition')),
    uric_acid: @json(__('health_records_edit.uric_acid')),
    uric_acid_2_4_6_0_mg_dl_f_3_4_7_0_mg_dl_: @json(__('health_records_edit.uric_acid_2_4_6_0_mg_dl_f_3_4_7_0_mg_dl_')),
    use_the_tools_in_the_top_toolbar: @json(__('health_records_edit.use_the_tools_in_the_top_toolbar')),
    value: @json(__('health_records_edit.value')),
    variable_depending_on_method: @json(__('health_records_edit.variable_depending_on_method')),
    view_report: @json(__('health_records_edit.view_report')),
    vitamin_b12: @json(__('health_records_edit.vitamin_b12')),
    vitamin_d: @json(__('health_records_edit.vitamin_d')),
    vitamin_d_30_100_ng_ml: @json(__('health_records_edit.vitamin_d_30_100_ng_ml')),
    white_blood_cells: @json(__('health_records_edit.white_blood_cells')),
    x_ray: @json(__('health_records_edit.x_ray')),
};
document.addEventListener('DOMContentLoaded', function() {
    try {
        console.log('First DOMContentLoaded event listener starting...');
        const aiAnalyzeBtn = document.getElementById('ai-analyze-btn');
        const clearNotesBtn = document.getElementById('clear-notes-btn');
        const clinicalNotes = document.getElementById('clinical_notes');
        const aiResults = document.getElementById('ai-results');
        const aiContent = document.getElementById('ai-content');
        
        console.log('AI elements found:', {
            aiAnalyzeBtn: !!aiAnalyzeBtn,
            clearNotesBtn: !!clearNotesBtn,
            clinicalNotes: !!clinicalNotes,
            aiResults: !!aiResults,
            aiContent: !!aiContent
        });

    // AI Analysis for Clinical Notes
    aiAnalyzeBtn.addEventListener('click', async function() {
        const notes = clinicalNotes.value.trim();
        if (!notes) {
            alert(EDIT_LABELS.please_enter_clinical_notes_for_the_ai_a);
            return;
        }

        aiAnalyzeBtn.disabled = true;
        aiAnalyzeBtn.textContent = EDIT_LABELS.analysis_in_progress;

        try {
            const response = await fetch('/api/v1/pcmas/prefill-from-transcript', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    transcript: notes,
                    athlete_id: document.getElementById('player_id').value || 1,
                    pcma_type: 'general'
                })
            });

            const data = await response.json();
            
            if (data.success) {
                aiContent.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <span class="text-green-600 font-semibold">${EDIT_LABELS.ai_analysis_completed}</span>
                            <span class="ml-2 text-sm text-gray-500">${EDIT_LABELS.confidence} ${Math.round((data.confidence_score || 0.7) * 100)}%</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <h4 class="font-semibold text-gray-900 mb-2">${EDIT_LABELS.extracted_data}</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                ${Object.entries(data.data || {}).map(([key, value]) => 
                                    `<li><strong>${key}:</strong> ${value || EDIT_LABELS.not_detected}</li>`
                                ).join('')}
                            </ul>
                        </div>
                        <button type="button" id="apply-ai-data" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            ${EDIT_LABELS.apply_ai_data_to_fields}
                        </button>
                    </div>
                `;
                aiResults.classList.remove('hidden');
            } else {
                aiContent.innerHTML = `
                    <div class="text-red-600">
                        <p>${EDIT_LABELS.error_during_ai_analysis_colon} ${data.message || EDIT_LABELS.unknown_error}</p>
                    </div>
                `;
                aiResults.classList.remove('hidden');
            }
        } catch (error) {
            aiContent.innerHTML = `
                <div class="text-red-600">
                    <p>${EDIT_LABELS.connection_error}${error.message}</p>
                </div>
            `;
            aiResults.classList.remove('hidden');
        } finally {
            aiAnalyzeBtn.disabled = false;
            aiAnalyzeBtn.textContent = '🔍 ${EDIT_LABELS.analyze_with_ai}';
        }
    });

    // Clear Notes
    clearNotesBtn.addEventListener('click', function() {
        clinicalNotes.value = '';
        aiResults.classList.add('hidden');
    });

    // Apply AI Data
    document.addEventListener('click', function(e) {
        if (e.target.id === 'apply-ai-data') {
            // This would populate form fields with AI-extracted data
            alert(EDIT_LABELS.ai_data_application_feature_is_under_dev);
        }
    });

    // Medical Imaging AI Analysis
    const aiCheckEcgBtn = document.getElementById('ai-check-ecg-btn');
    const aiCheckMriBtn = document.getElementById('ai-check-mri-btn');
    const aiCheckXrayBtn = document.getElementById('ai-check-xray-btn');
    const aiCheckAllBtn = document.getElementById('ai-check-all-btn');
    const aiAnalysisStatus = document.getElementById('ai-analysis-status');
    const aiAnalysisResults = document.getElementById('ai-analysis-results');
    const aiResultsContent = document.getElementById('ai-results-content');

    // AI Analysis for ECG
    aiCheckEcgBtn.addEventListener('click', async function() {
        const ecgFile = document.getElementById('ecg_file').files[0];
        if (!ecgFile) {
            alert(EDIT_LABELS.please_select_an_ecg_file_for_analysis);
            return;
        }

        const formData = new FormData();
        formData.append('ecg_file', ecgFile);

        aiCheckEcgBtn.disabled = true;
        aiCheckEcgBtn.textContent = EDIT_LABELS.analyzing_ecg;
        aiAnalysisStatus.classList.remove('hidden');
        aiAnalysisResults.classList.add('hidden');

        try {
            const response = await fetch('{{ route("pcma.ai-analyze-ecg") }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
                               if (data.success) {
                       // Handle nested response structure from AI service
                       const analysisData = data.analysis || data;
                       displayAIResults(analysisData, 'ECG');
                   } else {
                       displayAIError(data.message || EDIT_LABELS.error_during_ecg_analysis);
                   }
        } catch (error) {
            if (error.message.includes('<!DOCTYPE')) {
                displayAIError(EDIT_LABELS.authentication_error_please_log_in_again);
            } else {
                displayAIError(EDIT_LABELS.connection_error + error.message);
            }
        } finally {
            aiCheckEcgBtn.disabled = false;
            aiCheckEcgBtn.textContent = '🔍 ${EDIT_LABELS.analyze_ecg}';
            aiAnalysisStatus.classList.add('hidden');
        }
    });

    // AI Analysis for MRI
    aiCheckMriBtn.addEventListener('click', async function() {
        const mriFiles = document.getElementById('mri_files').files;
        if (!mriFiles || mriFiles.length === 0) {
            alert(EDIT_LABELS.please_select_at_least_one_mri_file_for_);
            return;
        }

        const formData = new FormData();
        // Add all MRI files
        for (let i = 0; i < mriFiles.length; i++) {
            formData.append('mri_files[]', mriFiles[i]);
        }

        aiCheckMriBtn.disabled = true;
        aiCheckMriBtn.textContent = `🧠 ${EDIT_LABELS.analyzing_mri} (${mriFiles.length} ${EDIT_LABELS.file_word}${mriFiles.length > 1 ? 's' : ''})...`;
        aiAnalysisStatus.classList.remove('hidden');
        aiAnalysisResults.classList.add('hidden');

        try {
            const response = await fetch('{{ route("pcma.ai-analyze-mri") }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                // Handle nested response structure from AI service
                const analysisData = data.analysis || data;
                displayAIResults(analysisData, 'MRI');
            } else {
                displayAIError(data.message || EDIT_LABELS.error_during_mri_analysis);
            }
        } catch (error) {
            if (error.message.includes('<!DOCTYPE')) {
                displayAIError(EDIT_LABELS.authentication_error_please_log_in_again);
            } else {
                displayAIError(EDIT_LABELS.connection_error + error.message);
            }
        } finally {
            aiCheckMriBtn.disabled = false;
            aiCheckMriBtn.textContent = '🧠 ${EDIT_LABELS.analyze_mri}';
            aiAnalysisStatus.classList.add('hidden');
        }
    });

    // AI Analysis for X-Ray
    aiCheckXrayBtn.addEventListener('click', async function() {
        const xrayFile = document.getElementById('xray_file').files[0];
        if (!xrayFile) {
            alert(EDIT_LABELS.please_select_an_x_ray_file_for_analysis);
            return;
        }

        const formData = new FormData();
        formData.append('xray_file', xrayFile);

        aiCheckXrayBtn.disabled = true;
        aiCheckXrayBtn.textContent = EDIT_LABELS.analyzing_x_ray;
        aiAnalysisStatus.classList.remove('hidden');
        aiAnalysisResults.classList.add('hidden');

        try {
            const response = await fetch('{{ route("pcma.ai-analyze-xray") }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
                                           if (data.success) {
                // Handle nested response structure from AI service
                const analysisData = data.analysis || data;
                console.log('X-ray analysis data before displayAIResults:', analysisData);
                displayAIResults(analysisData, 'X-Ray');
            } else {
                displayAIError(data.message || EDIT_LABELS.error_during_x_ray_analysis);
            }
        } catch (error) {
            if (error.message.includes('<!DOCTYPE')) {
                displayAIError(EDIT_LABELS.authentication_error_please_log_in_again);
            } else {
                displayAIError(EDIT_LABELS.connection_error + error.message);
            }
        } finally {
            aiCheckXrayBtn.disabled = false;
            aiCheckXrayBtn.textContent = '🦴 ${EDIT_LABELS.analyze_x_ray}';
            aiAnalysisStatus.classList.add('hidden');
        }
    });

    // AI Analysis for CT
    const aiCheckCtBtn = document.getElementById('ai-check-ct-btn');
    aiCheckCtBtn.addEventListener('click', async function() {
        const ctFiles = document.getElementById('ct_files').files;
        if (!ctFiles || ctFiles.length === 0) {
            alert(EDIT_LABELS.please_select_at_least_one_ct_file_for_a);
            return;
        }

        const formData = new FormData();
        // Add all CT files
        for (let i = 0; i < ctFiles.length; i++) {
            formData.append('ct_files[]', ctFiles[i]);
        }

        aiCheckCtBtn.disabled = true;
        aiCheckCtBtn.textContent = `🏥 ${EDIT_LABELS.analyzing_ct} (${ctFiles.length} ${EDIT_LABELS.file_word}${ctFiles.length > 1 ? 's' : ''})...`;
        aiAnalysisStatus.classList.remove('hidden');
        aiAnalysisResults.classList.add('hidden');

        try {
            const response = await fetch('{{ route("pcma.ai-analyze-xray") }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                // Handle nested response structure from AI service
                const analysisData = data.analysis || data;
                displayAIResults(analysisData, 'CT');
            } else {
                displayAIError(data.message || EDIT_LABELS.error_during_ct_analysis);
            }
        } catch (error) {
            if (error.message.includes('<!DOCTYPE')) {
                displayAIError(EDIT_LABELS.authentication_error_please_log_in_again);
            } else {
                displayAIError(EDIT_LABELS.connection_error + error.message);
            }
        } finally {
            aiCheckCtBtn.disabled = false;
            aiCheckCtBtn.textContent = '🏥 ${EDIT_LABELS.analyze_ct}';
            aiAnalysisStatus.classList.add('hidden');
        }
    });

    // AI Analysis for ECG d'Effort
    const aiCheckEcgEffortBtn = document.getElementById('ai-check-ecg-effort-btn');
    aiCheckEcgEffortBtn.addEventListener('click', async function() {
        const ecgEffortFile = document.getElementById('ecg_effort_file').files[0];
        if (!ecgEffortFile) {
            alert(EDIT_LABELS.please_select_an_exercise_ecg_file_for_a);
            return;
        }

        const formData = new FormData();
        formData.append('ecg_effort_file', ecgEffortFile);

        aiCheckEcgEffortBtn.disabled = true;
        aiCheckEcgEffortBtn.textContent = EDIT_LABELS.analyzing_exercise_ecg;
        aiAnalysisStatus.classList.remove('hidden');
        aiAnalysisResults.classList.add('hidden');

        try {
            const response = await fetch('{{ route("pcma.ai.ecg-effort") }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                const analysisData = data.analysis || data;
                displayAIResults(analysisData, 'Exercise ECG');
            } else {
                displayAIError(data.message || EDIT_LABELS.error_during_exercise_ecg_analysis);
            }
        } catch (error) {
            if (error.message.includes('<!DOCTYPE')) {
                displayAIError(EDIT_LABELS.authentication_error_please_log_in_again);
            } else {
                displayAIError(EDIT_LABELS.connection_error + error.message);
            }
        } finally {
            aiCheckEcgEffortBtn.disabled = false;
            aiCheckEcgEffortBtn.textContent = '📈 ${EDIT_LABELS.analyze_exercise_ecg}';
            aiAnalysisStatus.classList.add('hidden');
        }
    });

    // AI Analysis for Scintigraphie
    const aiCheckScintigraphyBtn = document.getElementById('ai-check-scintigraphy-btn');
    aiCheckScintigraphyBtn.addEventListener('click', async function() {
        const scintigraphyFile = document.getElementById('scintigraphy_file').files[0];
        if (!scintigraphyFile) {
            alert(EDIT_LABELS.please_select_a_scintigraphy_file_for_an);
            return;
        }

        const formData = new FormData();
        formData.append('scintigraphy_file', scintigraphyFile);

        aiCheckScintigraphyBtn.disabled = true;
        aiCheckScintigraphyBtn.textContent = EDIT_LABELS.analyzing_scintigraphy;
        aiAnalysisStatus.classList.remove('hidden');
        aiAnalysisResults.classList.add('hidden');

        try {
            const response = await fetch('{{ route("pcma.ai.scintigraphy") }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                const analysisData = data.analysis || data;
                displayAIResults(analysisData, 'Scintigraphy');
            } else {
                displayAIError(data.message || EDIT_LABELS.error_during_scintigraphy_analysis);
            }
        } catch (error) {
            if (error.message.includes('<!DOCTYPE')) {
                displayAIError(EDIT_LABELS.authentication_error_please_log_in_again);
            } else {
                displayAIError(EDIT_LABELS.connection_error + error.message);
            }
        } finally {
            aiCheckScintigraphyBtn.disabled = false;
            aiCheckScintigraphyBtn.textContent = '☢️ ${EDIT_LABELS.analyze_scintigraphy}';
            aiAnalysisStatus.classList.add('hidden');
        }
    });

    // AI Analysis for SCAT
    const aiCheckScatBtn = document.getElementById('ai-check-scat-btn');
    aiCheckScatBtn.addEventListener('click', async function() {
        // Collect SCAT form data
        const scatData = {
            evaluation_date: document.getElementById('scat_evaluation_date').value,
            context: document.getElementById('scat_context').value,
            evaluator_name: document.getElementById('scat_evaluator_name').value,
            red_flags: Array.from(document.querySelectorAll('input[name="scat_red_flags[]"]:checked')).map(cb => cb.value),
            observable_signs: Array.from(document.querySelectorAll('input[name="scat_observable_signs[]"]:checked')).map(cb => cb.value),
            symptoms: {}
        };

        // Collect symptom severity scores
        const symptomSliders = document.querySelectorAll('input[name^="scat_symptom_"]');
        symptomSliders.forEach(slider => {
            const symptomName = slider.name.replace('scat_symptom_', '');
            scatData.symptoms[symptomName] = slider.value;
        });

        // Collect SAC scores
        scatData.sac = {
            orientation: document.getElementById('scat_sac_orientation').value,
            immediate_memory: document.getElementById('scat_sac_immediate_memory').value,
            concentration: document.getElementById('scat_sac_concentration').value,
            delayed_recall: document.getElementById('scat_sac_delayed_recall').value
        };

        // Collect mBESS scores
        scatData.mbess = {
            firm_surface_errors: document.getElementById('scat_mbess_firm_errors').value,
            foam_surface_errors: document.getElementById('scat_mbess_foam_errors').value
        };

        // Collect medical decision
        scatData.medical_decision = document.getElementById('scat_medical_decision').value;
        scatData.follow_up_plan = document.getElementById('scat_follow_up_plan').value;

        aiCheckScatBtn.disabled = true;
        aiCheckScatBtn.textContent = EDIT_LABELS.analyzing_scat;
        aiAnalysisStatus.classList.remove('hidden');
        aiAnalysisResults.classList.add('hidden');

        try {
            const response = await fetch('{{ route("pcma.ai.scat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(scatData)
            });

            const data = await response.json();
            
            if (data.success) {
                const analysisData = data.analysis || data;
                displayAIResults(analysisData, 'SCAT');
                
                // Insert SCAT analysis into the follow-up plan textarea
                if (analysisData.text && document.getElementById('scat_follow_up_plan')) {
                    const currentPlan = document.getElementById('scat_follow_up_plan').value;
                    const aiAnalysis = `\n\n${EDIT_LABELS.ai_scat_analysis}\n${analysisData.text}\n${EDIT_LABELS.end_ai_analysis}\n`;
                    document.getElementById('scat_follow_up_plan').value = currentPlan + aiAnalysis;
                }
            } else {
                displayAIError(data.message || EDIT_LABELS.error_during_scat_analysis);
            }
        } catch (error) {
            if (error.message.includes('<!DOCTYPE')) {
                displayAIError(EDIT_LABELS.authentication_error_please_log_in_again);
            } else {
                displayAIError(EDIT_LABELS.connection_error + error.message);
            }
        } finally {
            aiCheckScatBtn.disabled = false;
            aiCheckScatBtn.textContent = '🧠 ${EDIT_LABELS.analyze_scat}';
            aiAnalysisStatus.classList.add('hidden');
        }
    });

    // AI Analysis for All Files
    aiCheckAllBtn.addEventListener('click', async function() {
        const ecgFile = document.getElementById('ecg_file').files[0];
        const mriFiles = document.getElementById('mri_files').files;
        const ctFiles = document.getElementById('ct_files').files;
        const xrayFile = document.getElementById('xray_file').files[0];

        if (!ecgFile && (!mriFiles || mriFiles.length === 0) && (!ctFiles || ctFiles.length === 0) && !xrayFile) {
            alert(EDIT_LABELS.please_select_at_least_one_medical_file_);
            return;
        }

        const formData = new FormData();
        if (ecgFile) formData.append('ecg_file', ecgFile);
        
        // Add all MRI files
        if (mriFiles && mriFiles.length > 0) {
            for (let i = 0; i < mriFiles.length; i++) {
                formData.append('mri_files[]', mriFiles[i]);
            }
        }
        
        // Add all CT files
        if (ctFiles && ctFiles.length > 0) {
            for (let i = 0; i < ctFiles.length; i++) {
                formData.append('ct_files[]', ctFiles[i]);
            }
        }
        
        if (xrayFile) formData.append('xray_file', xrayFile);

        aiCheckAllBtn.disabled = true;
        aiCheckAllBtn.textContent = EDIT_LABELS.full_analysis_in_progress;
        aiAnalysisStatus.classList.remove('hidden');
        aiAnalysisResults.classList.add('hidden');

        try {
            const response = await fetch('{{ route("pcma.ai.complete") }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                displayAIResults(data.analysis, 'Complete');
            } else {
                displayAIError(data.message || EDIT_LABELS.error_during_full_analysis);
            }
        } catch (error) {
            if (error.message.includes('<!DOCTYPE')) {
                displayAIError(EDIT_LABELS.authentication_error_please_log_in_again);
            } else {
                displayAIError(EDIT_LABELS.connection_error + error.message);
            }
        } finally {
            aiCheckAllBtn.disabled = false;
            aiCheckAllBtn.textContent = '🚀 ${EDIT_LABELS.full_analysis}';
            aiAnalysisStatus.classList.add('hidden');
        }
    });

    function displayAIResults(analysis, type) {
        console.log('displayAIResults called with:', { analysis, type });
        
        // Check if this is an error response from the API
        if (analysis.success === false && analysis.error) {
            aiResultsContent.innerHTML = `
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-red-600 font-semibold">${EDIT_LABELS.med_gemini_api_error}</span>
                    </div>
                    <div class="bg-red-50 p-3 rounded border border-red-200">
                        <h4 class="font-semibold text-red-900 mb-2">${EDIT_LABELS.api_error}</h4>
                        <div class="text-sm text-red-700">
                            <p><strong>${EDIT_LABELS.error_message}</strong> ${analysis.error}</p>
                            <p><strong>${EDIT_LABELS.analysis_type}</strong> ${type}</p>
                            <p><strong>${EDIT_LABELS.model}</strong> ${analysis.model || 'N/A'}</p>
                            <p><strong>Timestamp:</strong> ${analysis.timestamp || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            `;
            aiAnalysisResults.classList.remove('hidden');
            return;
        }

        // Check if this is a successful API response with text
        if (analysis.text) {
            // Try to parse JSON from the text response for better formatting
            let formattedContent = analysis.text;
            let isJsonResponse = false;
            
            try {
                // Check if the text contains JSON
                if (analysis.text.includes('{') && analysis.text.includes('}')) {
                    // Try to extract JSON from the text
                    const jsonMatch = analysis.text.match(/\{[\s\S]*\}/);
                    if (jsonMatch) {
                        const jsonData = JSON.parse(jsonMatch[0]);
                        isJsonResponse = true;
                        
                        // Format the JSON data nicely with medical report styling
                        let formattedHtml = '';
                        let hasAbnormalities = false;
                        
                        for (const [key, value] of Object.entries(jsonData)) {
                            const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            
                            // Handle different value types
                            let displayValue = '';
                            let isAbnormal = false;
                            
                            if (typeof value === 'object' && value !== null) {
                                // Check if it's a nested object with bone/medical structure or joint alignment
                                if (Object.keys(value).length > 0 && 
                                    (Object.keys(value).some(key => 
                                        ['tibia', 'fibula', 'femur', 'humerus', 'radius', 'ulna', 'scapula', 'clavicle', 
                                         'pelvis', 'spine', 'skull', 'ribs', 'other_bones', 'joints', 'ligaments', 
                                         'muscles', 'tendons', 'cartilage', 'knee', 'ankle', 'hip', 'shoulder', 'elbow',
                                         'wrist', 'spine_joints', 'other_joints'].includes(key.toLowerCase())
                                    ))) {
                                    // Format as medical bone structure or joint alignment report
                                    let medicalReport = '';
                                    for (const [itemName, itemDescription] of Object.entries(value)) {
                                        const formattedItemName = itemName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                        
                                        // Check for abnormal keywords in both bone and joint contexts
                                        const abnormalKeywords = [
                                            'fracture', 'broken', 'damage', 'disruption', 'injury', 'pathological',
                                            'malalignment', 'difficult', 'potential', 'requires', 'abnormal', 'dislocation',
                                            'instability', 'degenerative', 'narrowing', 'impingement', 'impingement'
                                        ];
                                        
                                        const hasAbnormalKeywords = abnormalKeywords.some(keyword => 
                                            itemDescription.toLowerCase().includes(keyword)
                                        );
                                        
                                        if (hasAbnormalKeywords) isAbnormal = true;
                                        
                                        // Determine if this is a joint or bone based on the key
                                        const isJoint = ['knee', 'ankle', 'hip', 'shoulder', 'elbow', 'wrist', 'spine_joints', 'other_joints'].includes(itemName.toLowerCase());
                                        const icon = isJoint ? '🦴' : '🦴';
                                        const bgColor = hasAbnormalKeywords ? 'bg-orange-50 border-l-2 border-orange-400' : 'bg-blue-50 border-l-2 border-blue-400';
                                        const textColor = hasAbnormalKeywords ? 'text-orange-800' : 'text-blue-700';
                                        
                                        medicalReport += `
                                            <div class="mb-2 p-2 ${bgColor}">
                                                <div class="flex items-center mb-1">
                                                    <span class="font-semibold text-sm ${textColor}">
                                                        ${hasAbnormalKeywords ? '⚠️' : '✅'} ${icon} ${formattedItemName}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-600 leading-relaxed">${itemDescription}</p>
                                            </div>
                                        `;
                                    }
                                    displayValue = medicalReport;
                                } else if (value.text) {
                                    displayValue = value.text;
                                } else if (value.description) {
                                    displayValue = value.description;
                                } else if (value.value) {
                                    displayValue = value.value;
                                } else if (value.content) {
                                    displayValue = value.content;
                                } else {
                                    // If no specific field, try to stringify the object
                                    try {
                                        displayValue = JSON.stringify(value, null, 2);
                                    } catch (e) {
                                        displayValue = EDIT_LABELS.complex_data_see_technical_details;
                                    }
                                }
                            } else {
                                // If it's a string, number, or other primitive
                                displayValue = String(value);
                            }
                            
                            // Check for abnormal findings
                            const abnormalKeywords = ['fracture', 'broken', 'damage', 'abnormal', 'pathological', 'disease', 'injury', 'lesion'];
                            isAbnormal = abnormalKeywords.some(keyword => 
                                displayValue.toLowerCase().includes(keyword)
                            );
                            
                            if (isAbnormal) hasAbnormalities = true;
                            
                            // Determine card styling based on content
                            const cardClass = isAbnormal 
                                ? 'mb-4 p-4 bg-red-50 rounded-lg border-l-4 border-red-500 shadow-sm'
                                : 'mb-4 p-4 bg-green-50 rounded-lg border-l-4 border-green-500 shadow-sm';
                            
                            const titleClass = isAbnormal 
                                ? 'font-semibold text-red-900 mb-2 flex items-center'
                                : 'font-semibold text-green-900 mb-2 flex items-center';
                            
                            const icon = isAbnormal ? '⚠️' : '✅';
                            
                            formattedHtml += `
                                <div class="${cardClass}">
                                    <h5 class="${titleClass}">
                                        <span class="mr-2">${icon}</span>
                                        ${formattedKey}
                                    </h5>
                                    <div class="text-gray-700 text-sm leading-relaxed bg-white p-3 rounded border">
                                        ${displayValue}
                                    </div>
                                </div>
                            `;
                        }
                        
                        // Add summary section
                        const summaryClass = hasAbnormalities 
                            ? 'bg-red-100 border-red-300 text-red-800'
                            : 'bg-green-100 border-green-300 text-green-800';
                        
                        const summaryIcon = hasAbnormalities ? '🚨' : '✅';
                        const summaryText = hasAbnormalities 
                            ? EDIT_LABELS.abnormalities_detected_medical_consultat
                            : EDIT_LABELS.normal_examination_no_abnormalities_dete;
                        
                        formattedHtml = `
                            <div class="mb-4 p-4 ${summaryClass} rounded-lg border-2">
                                <div class="flex items-center">
                                    <span class="text-xl mr-3">${summaryIcon}</span>
                                    <div>
                                        <h4 class="font-bold">${EDIT_LABELS.analysis_summary}</h4>
                                        <p class="text-sm">${summaryText}</p>
                                    </div>
                                </div>
                            </div>
                            ${formattedHtml}
                        `;
                        
                        formattedContent = formattedHtml;
                    }
                }
            } catch (e) {
                // If JSON parsing fails, use the original text
                formattedContent = analysis.text;
            }
            
            aiResultsContent.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg border border-green-200">
                        <div class="flex items-center space-x-2">
                            <span class="text-green-600 text-xl">✓</span>
                            <span class="text-green-800 font-semibold">${EDIT_LABELS.status_analysis_word} ${type} ${EDIT_LABELS.status_completed_word}</span>
                        </div>
                        <div class="text-sm text-green-600">
                            <span class="font-medium">${EDIT_LABELS.model}</span> ${analysis.model || 'N/A'}
                        </div>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                            <h4 class="font-semibold text-gray-900">${EDIT_LABELS.med_gemini_analysis_results}</h4>
                        </div>
                        <div class="p-4">
                            ${isJsonResponse ? formattedContent : `
                                <div class="text-gray-700 whitespace-pre-wrap leading-relaxed">
                                    ${analysis.text}
                                </div>
                            `}
                        </div>
                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-200 rounded-b-lg">
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <div>
                                    <span>${EDIT_LABELS.processing_time} <span class="font-medium">${analysis.processingTime || 'N/A'}ms</span></span>
                                    <span class="ml-4">Timestamp: <span class="font-medium">${analysis.timestamp || 'N/A'}</span></span>
                                </div>
                                                                        <button 
                            type="button" 
                            id="generate-hl7-cda-btn"
                            class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors flex items-center"
                        >
                            📋 ${EDIT_LABELS.generate_hl7_cda}
                        </button>

                            </div>
                        </div>
                    </div>
                </div>
            `;
            aiAnalysisResults.classList.remove('hidden');
            return;
        }

        // Fallback for other response formats
        const fileType = analysis.file_type || 'Image';
        const fileExtension = analysis.file_extension || 'N/A';
        const dicomMetadata = analysis.dicom_metadata ? `<div class="mt-2 p-2 bg-blue-50 rounded"><strong>${EDIT_LABELS.dicom_metadata}</strong> ${JSON.stringify(analysis.dicom_metadata, null, 2)}</div>` : '';

        // Handle different analysis types
        let resultsHtml = '';
        
        if (type === 'X-Ray') {
            // X-ray specific fields
            resultsHtml = `
                <p><strong>${EDIT_LABELS.bone_structure}</strong> ${analysis.bone_structure || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.joint_alignment}</strong> ${analysis.joint_alignment || EDIT_LABELS.not_available}</p>
                <p><strong>Fractures:</strong> ${analysis.fractures || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.dislocations}</strong> ${analysis.dislocations || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.arthritis}</strong> ${analysis.arthritis || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.bone_density}</strong> ${analysis.bone_density || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.abnormalities_detected}</strong> ${analysis.abnormalities || EDIT_LABELS.none}</p>
                <p><strong>${EDIT_LABELS.recommendations}</strong> ${analysis.recommendations || EDIT_LABELS.not_available}</p>
            `;
        } else if (type === 'ECG') {
            // ECG specific fields
            resultsHtml = `
                <p><strong>${EDIT_LABELS.rhythm}</strong> ${analysis.rhythm || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.heart_rate}</strong> ${analysis.heart_rate || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.abnormalities_detected}</strong> ${analysis.abnormalities || EDIT_LABELS.none}</p>
                <p><strong>${EDIT_LABELS.recommendations}</strong> ${analysis.recommendations || EDIT_LABELS.not_available}</p>
            `;
        } else if (type === 'MRI') {
            // MRI specific fields
            resultsHtml = `
                <p><strong>${EDIT_LABELS.bone_age}</strong> ${analysis.bone_age || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.chronological_age}</strong> ${analysis.chronological_age || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.age_difference}</strong> ${analysis.age_difference || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.skeletal_maturity}</strong> ${analysis.skeletal_maturity || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.abnormalities_detected}</strong> ${analysis.abnormalities || EDIT_LABELS.none}</p>
                <p><strong>${EDIT_LABELS.recommendations}</strong> ${analysis.recommendations || EDIT_LABELS.not_available}</p>
            `;
        } else {
            // Generic fields
            resultsHtml = `
                <p><strong>${EDIT_LABELS.diagnosis_2}</strong> ${analysis.diagnosis || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.abnormalities_detected}</strong> ${analysis.abnormalities || EDIT_LABELS.none}</p>
                <p><strong>${EDIT_LABELS.recommendations}</strong> ${analysis.recommendations || EDIT_LABELS.not_available}</p>
                <p><strong>${EDIT_LABELS.confidence}</strong> ${analysis.confidence || EDIT_LABELS.not_available}</p>
            `;
        }

        aiResultsContent.innerHTML = `
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-green-600 font-semibold">✓ ${EDIT_LABELS.status_analysis_word} ${type} ${EDIT_LABELS.status_completed_word}</span>
                    <span class="text-sm text-gray-500">Type: ${fileType} (${fileExtension})</span>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <h4 class="font-semibold text-gray-900 mb-2">${EDIT_LABELS.analysis_results}</h4>
                    <div class="text-sm text-gray-700">
                        ${resultsHtml}
                        ${dicomMetadata}
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-2 border-t border-gray-200 rounded-b-lg">
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <div>
                            <span>${EDIT_LABELS.processing_time} <span class="font-medium">${analysis.processingTime || 'N/A'}ms</span></span>
                            <span class="ml-4">Timestamp: <span class="font-medium">${analysis.timestamp || 'N/A'}</span></span>
                        </div>
                        <button 
                            type="button" 
                            id="generate-hl7-cda-btn"
                            class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors flex items-center"
                        >
                            📋 ${EDIT_LABELS.generate_hl7_cda}
                        </button>

                    </div>
                </div>
            </div>
        `;
        aiAnalysisResults.classList.remove('hidden');
        
        // Store current analysis data for HL7 CDA generation
        const newAnalysisData = {
            type: type,
            analysis: analysis,
            timestamp: new Date().toISOString()
        };
        
        // Track the change
        window.analysisDataHistory.push({
            action: 'STORING_REAL_DATA',
            timestamp: new Date().toISOString(),
            oldData: window.currentAnalysisData,
            newData: newAnalysisData
        });
        
        window.currentAnalysisData = newAnalysisData;
        
        // Debug: Log the stored data
        console.log('=== STORING REAL ANALYSIS DATA ===');
        console.log('Analysis type:', type);
        console.log('Analysis data:', analysis);
        console.log('Stored analysis data:', window.currentAnalysisData);
        console.log('Stored analysis data type:', typeof window.currentAnalysisData.analysis);
        console.log('Analysis content:', JSON.stringify(analysis, null, 2));
        console.log('Window object before storing:', Object.keys(window).filter(key => key.includes('analysis')));
        
        // Test if data persists after a short delay
        setTimeout(() => {
            console.log('=== CHECKING DATA PERSISTENCE ===');
            console.log('Data after 1 second:', window.currentAnalysisData);
            console.log('Data still exists:', !!window.currentAnalysisData);
            console.log('Window object after 1 second:', Object.keys(window).filter(key => key.includes('analysis')));
        }, 1000);
    }

    function displayAIError(message) {
        aiResultsContent.innerHTML = `
            <div class="text-red-600">
                <p>${message}</p>
            </div>
        `;
        aiAnalysisResults.classList.remove('hidden');
    }

    // File Preview Functionality
    const mriFilesInput = document.getElementById('mri_files');
    const ctFilesInput = document.getElementById('ct_files');
    const mriFilesPreview = document.getElementById('mri-files-preview');
    const ctFilesPreview = document.getElementById('ct-files-preview');

    // MRI Files Preview
    mriFilesInput.addEventListener('change', function() {
        mriFilesPreview.innerHTML = '';
        if (this.files.length > 0) {
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                const fileDiv = document.createElement('div');
                fileDiv.className = 'flex items-center justify-between p-2 bg-blue-50 rounded border border-blue-200';
                fileDiv.innerHTML = `
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-2">📄</span>
                        <span class="text-sm text-gray-700">${file.name}</span>
                        <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                    </div>
                    <button type="button" class="text-red-500 hover:text-red-700 text-sm" onclick="removeFile(this, 'mri_files')">
                        ✕
                    </button>
                `;
                mriFilesPreview.appendChild(fileDiv);
            }
        }
    });

    // CT Files Preview
    ctFilesInput.addEventListener('change', function() {
        ctFilesPreview.innerHTML = '';
        if (this.files.length > 0) {
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                const fileDiv = document.createElement('div');
                fileDiv.className = 'flex items-center justify-between p-2 bg-green-50 rounded border border-green-200';
                fileDiv.innerHTML = `
                    <div class="flex items-center">
                        <span class="text-green-600 mr-2">📄</span>
                        <span class="text-sm text-gray-700">${file.name}</span>
                        <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                    </div>
                    <button type="button" class="text-red-500 hover:text-red-700 text-sm" onclick="removeFile(this, 'ct_files')">
                        ✕
                    </button>
                `;
                ctFilesPreview.appendChild(fileDiv);
            }
        }
    });

    // Remove file function
    window.removeFile = function(button, inputId) {
        const fileDiv = button.parentElement.parentElement;
        fileDiv.remove();
        // Note: We can't directly remove files from FileList, but we can clear and re-add
        // This is a simplified approach - in a real app you'd need more complex handling
    };

    // HL7 CDA Generation
    const hl7CdaStatus = document.getElementById('hl7-cda-status');
    
    // Global tracking for analysis data changes
    window.analysisDataHistory = [];

        // Use event delegation for the dynamically created button
    document.addEventListener('click', async function(event) {
        if (event.target && event.target.id === 'generate-hl7-cda-btn') {
            console.log('=== HL7 CDA BUTTON CLICKED ===');
            console.log('Current analysis data:', window.currentAnalysisData);
            console.log('Window object keys:', Object.keys(window).filter(key => key.includes('analysis')));
            console.log('Analysis data type:', typeof window.currentAnalysisData);
            console.log('Analysis data content:', JSON.stringify(window.currentAnalysisData, null, 2));
            console.log('Analysis data exists:', !!window.currentAnalysisData);
            console.log('Analysis data type property:', typeof window.currentAnalysisData?.type);
            console.log('Analysis data analysis property:', typeof window.currentAnalysisData?.analysis);
            console.log('Data exists:', !!window.currentAnalysisData);
            console.log('Data has analysis property:', !!(window.currentAnalysisData && window.currentAnalysisData.analysis));
            console.log('Analysis data history:', window.analysisDataHistory);
            
            if (!window.currentAnalysisData) {
                console.log('No analysis data found. Please run an AI analysis first.');
                alert(EDIT_LABELS.no_analysis_available_please_run_an_ai_a);
                return;
            }

        event.target.disabled = true;
        event.target.textContent = EDIT_LABELS.generating;
        hl7CdaStatus.classList.remove('hidden');

                    try {
                const requestData = {
                    analysis_data: window.currentAnalysisData,
                    player_id: document.getElementById('player_id').value || 1,
                    record_date: document.getElementById('record_date').value,
                    diagnosis: document.getElementById('diagnosis').value,
                    treatment_plan: document.getElementById('treatment_plan').value
                };
                
                console.log('Sending request data:', requestData);
                console.log('Request URL:', '{{ route("health-records.generate-hl7-cda") }}');
                
                const response = await fetch('{{ route("health-records.generate-hl7-cda") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(requestData)
                });

            const data = await response.json();
            console.log('HL7 CDA response:', data);
            console.log('Response status:', response.status);
            
            if (data.success) {
                // Show success message
                const successDiv = document.createElement('div');
                successDiv.className = 'mt-3 p-3 bg-green-50 border border-green-200 rounded-lg';
                successDiv.innerHTML = `
                    <div class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        <span class="text-green-800 font-semibold">${EDIT_LABELS.hl7_cda_report_generated_successfully}</span>
                    </div>
                    <div class="mt-2 text-sm text-green-700">
                        <p><strong>${EDIT_LABELS.report_id}</strong> ${data.report_id}</p>
                        <p><strong>Type:</strong> ${data.report_type}</p>
                        <p><strong>Date:</strong> ${data.generated_at}</p>
                        <p><strong>${EDIT_LABELS.status_2}</strong> ${EDIT_LABELS.saved_in_the_player_s_medical_record}</p>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <a href="/health-records/view-hl7-cda/${data.report_id}" target="_blank" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                            ${EDIT_LABELS.view_report}
                        </a>
                        <a href="${data.download_url}" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            ${EDIT_LABELS.download_xml}
                        </a>
                    </div>
                `;
                
                // Insert after the results content
                const resultsContainer = document.getElementById('ai-results-content');
                resultsContainer.appendChild(successDiv);
                
                // Hide the generate button
                event.target.style.display = 'none';
            } else {
                throw new Error(data.message || EDIT_LABELS.error_generating_hl7_cda_report);
            }
        } catch (error) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mt-3 p-3 bg-red-50 border border-red-200 rounded-lg';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <span class="text-red-600 mr-2">✗</span>
                    <span class="text-red-800 font-semibold">${EDIT_LABELS.error_during_generation}</span>
                </div>
                <div class="mt-2 text-sm text-red-700">
                    <p>${error.message}</p>
                </div>
            `;
            
            const resultsContainer = document.getElementById('ai-results-content');
            resultsContainer.appendChild(errorDiv);
        } finally {
            event.target.disabled = false;
            event.target.textContent = '📋 ${EDIT_LABELS.generate_hl7_cda}';
            hl7CdaStatus.classList.add('hidden');
        }
    }
    });

    // DICOM Viewer Functionality
    const dicomViewerSelect = document.getElementById('dicom-viewer-select');
    const dicomCanvas = document.getElementById('dicom-canvas');
    const dicomLoading = document.getElementById('dicom-loading');
    const dicomError = document.getElementById('dicom-error');
    const dicomPlaceholder = document.getElementById('dicom-placeholder');
    const dicomMetadata = document.getElementById('dicom-metadata');
    const dicomMeasurements = document.getElementById('dicom-measurements');
    const dicomZoomIn = document.getElementById('dicom-zoom-in');
    const dicomZoomOut = document.getElementById('dicom-zoom-out');
    const dicomReset = document.getElementById('dicom-reset');
    const dicomFullscreen = document.getElementById('dicom-fullscreen');

    // File input mapping
    const fileInputs = {
        'ecg': 'ecg_file',
        'mri': 'mri_file',
        'xray': 'xray_file'
    };

    // DicomViewer class
    class DicomViewer {
        constructor(canvas) {
            this.canvas = canvas;
            this.ctx = canvas.getContext('2d');
            this.image = null;
            this.zoom = 1.0;
            this.offset = { x: 0, y: 0 };
            this.isDragging = false;
            this.lastMousePos = { x: 0, y: 0 };
            
            this.setupEventListeners();
        }

        setupEventListeners() {
            // Mouse events
            this.canvas.addEventListener('mousedown', (e) => this.onMouseDown(e));
            this.canvas.addEventListener('mousemove', (e) => this.onMouseMove(e));
            this.canvas.addEventListener('mouseup', (e) => this.onMouseUp(e));
            this.canvas.addEventListener('wheel', (e) => this.onWheel(e));

            // Touch events for mobile
            this.canvas.addEventListener('touchstart', (e) => this.onTouchStart(e));
            this.canvas.addEventListener('touchmove', (e) => this.onTouchMove(e));
            this.canvas.addEventListener('touchend', (e) => this.onTouchEnd(e));
            
            // Resize handler
            window.addEventListener('resize', () => {
                if (this.image) {
                    this.render();
                }
            });
        }

        onMouseDown(e) {
            this.isDragging = true;
            this.lastMousePos = { x: e.clientX, y: e.clientY };
            this.canvas.style.cursor = 'grabbing';
        }

        onMouseMove(e) {
            if (!this.isDragging) return;
            
            const deltaX = e.clientX - this.lastMousePos.x;
            const deltaY = e.clientY - this.lastMousePos.y;
            
            this.offset.x += deltaX;
            this.offset.y += deltaY;
            
            this.lastMousePos = { x: e.clientX, y: e.clientY };
            this.render();
        }

        onMouseUp(e) {
            this.isDragging = false;
            this.canvas.style.cursor = 'crosshair';
        }

        onWheel(e) {
            e.preventDefault();
            const zoomFactor = e.deltaY > 0 ? 0.9 : 1.1;
            this.zoom = Math.max(0.1, Math.min(5.0, this.zoom * zoomFactor));
            this.render();
        }

        onTouchStart(e) {
            if (e.touches.length === 1) {
                this.isDragging = true;
                this.lastMousePos = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            }
        }

        onTouchMove(e) {
            if (!this.isDragging || e.touches.length !== 1) return;
            
            const deltaX = e.touches[0].clientX - this.lastMousePos.x;
            const deltaY = e.touches[0].clientY - this.lastMousePos.y;
            
            this.offset.x += deltaX;
            this.offset.y += deltaY;
            
            this.lastMousePos = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            this.render();
        }

        onTouchEnd(e) {
            this.isDragging = false;
        }

        async loadImage(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        console.log('Image loaded successfully:', img.width, 'x', img.height);
                        this.image = img;
                        this.resetView();
                        this.render();
                        
                        // Update dimensions in metadata
                        const dimensionsElement = document.getElementById('dicom-image-dimensions');
                        if (dimensionsElement) {
                            dimensionsElement.textContent = `${img.width} × ${img.height} pixels`;
                        }
                        
                        resolve(img);
                    };
                    img.onerror = (error) => {
                        console.error('Image loading error:', error);
                        reject(error);
                    };
                    img.src = e.target.result;
                };
                
                reader.onerror = (error) => {
                    console.error('File reading error:', error);
                    reject(error);
                };
                reader.readAsDataURL(file);
            });
        }

        resetView() {
            this.zoom = 1.0;
            this.offset = { x: 0, y: 0 };
            
            // Force a render to ensure proper sizing
            if (this.image) {
                setTimeout(() => {
                    this.render();
                }, 50);
            }
        }

        render() {
            if (!this.image) {
                console.log('No image to render');
                return;
            }

            const canvas = this.canvas;
            const ctx = this.ctx;
            
            // Get container dimensions
            const container = canvas.parentElement;
            const containerWidth = container.offsetWidth;
            const containerHeight = container.offsetHeight;
            
            // Set canvas size to match container
            canvas.width = containerWidth;
            canvas.height = containerHeight;
            
            console.log('Container size:', containerWidth, 'x', containerHeight);
            console.log('Canvas size:', canvas.width, 'x', canvas.height);
            console.log('Image size:', this.image.width, 'x', this.image.height);
            console.log('Zoom level:', this.zoom);
            
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Calculate image dimensions to fit container while maintaining aspect ratio
            const imageAspectRatio = this.image.width / this.image.height;
            const containerAspectRatio = containerWidth / containerHeight;
            
            let displayWidth, displayHeight;
            
            // Determine if image is portrait or landscape
            const isPortrait = this.image.height > this.image.width;
            const isLandscape = this.image.width > this.image.height;
            
            console.log('Image orientation:', isPortrait ? 'Portrait' : isLandscape ? 'Landscape' : 'Square');
            
            if (imageAspectRatio > containerAspectRatio) {
                // Image is wider than container - fit to width
                displayWidth = containerWidth * 0.85; // 85% of container width for better fit
                displayHeight = displayWidth / imageAspectRatio;
            } else {
                // Image is taller than container - fit to height
                displayHeight = containerHeight * 0.85; // 85% of container height for better fit
                displayWidth = displayHeight * imageAspectRatio;
            }
            
            // Apply zoom
            const scaledWidth = displayWidth * this.zoom;
            const scaledHeight = displayHeight * this.zoom;
            
            // Center the image
            const x = (canvas.width - scaledWidth) / 2 + this.offset.x;
            const y = (canvas.height - scaledHeight) / 2 + this.offset.y;
            
            console.log('Display size:', displayWidth, 'x', displayHeight);
            console.log('Scaled size:', scaledWidth, 'x', scaledHeight);
            console.log('Drawing image at:', x, y);
            
            // Draw image
            ctx.drawImage(this.image, x, y, scaledWidth, scaledHeight);
            
            // Update info
            const dimensionsElement = document.getElementById('dicom-image-dimensions');
            if (dimensionsElement) {
                dimensionsElement.textContent = `${this.image.width} × ${this.image.height}`;
            }
        }
    }

    // Initialize DICOM viewer
    console.log('Initializing DICOM viewer with canvas:', dicomCanvas);
    const dicomViewer = new DicomViewer(dicomCanvas);

    // File selection handler
    dicomViewerSelect.addEventListener('change', async function() {
        const selectedType = this.value;
        console.log('File type selected:', selectedType);
        
        if (!selectedType) {
            showPlaceholder();
            return;
        }

        const fileInputId = fileInputs[selectedType];
        const fileInput = document.getElementById(fileInputId);
        
        console.log('File input ID:', fileInputId);
        console.log('File input found:', !!fileInput);
        
        if (!fileInput || !fileInput.files[0]) {
            alert('${EDIT_LABELS.please_select_a_file_first} ' + selectedType);
            return;
        }

        const file = fileInput.files[0];
        const extension = file.name.toLowerCase().split('.').pop();
        const isDicom = extension === 'dcm';
        const isImage = ['jpg', 'jpeg', 'png', 'bmp', 'tiff', 'tif'].includes(extension);
        const isPdf = extension === 'pdf';

        console.log('File:', file.name, 'Extension:', extension);
        console.log('Is DICOM:', isDicom, 'Is Image:', isImage, 'Is PDF:', isPdf);

        showLoading();

        try {
            if (isDicom) {
                console.log('Loading DICOM file...');
                await dicomViewer.loadImage(file);
                showViewer();
                updateMetadata(file, selectedType, 'DICOM');
            } else if (isImage) {
                console.log('Loading image file...');
                await dicomViewer.loadImage(file);
                showViewer();
                updateMetadata(file, selectedType, 'Image');
            } else if (isPdf) {
                console.log('Loading PDF file...');
                showPdfViewer(file);
                updateMetadata(file, selectedType, 'PDF');
            } else {
                throw new Error(EDIT_LABELS.unsupported_file_format);
            }
        } catch (error) {
            console.error('Error loading file:', error);
            showError(error.message);
        }
    });

    function showLoading() {
        dicomLoading.classList.remove('hidden');
        dicomError.classList.add('hidden');
        dicomPlaceholder.classList.add('hidden');
        dicomCanvas.classList.add('hidden');
        dicomMetadata.classList.add('hidden');
        dicomMeasurements.classList.add('hidden');
    }

    function showViewer() {
        console.log('Showing viewer...');
        dicomLoading.classList.add('hidden');
        dicomError.classList.add('hidden');
        dicomPlaceholder.classList.add('hidden');
        dicomCanvas.classList.remove('hidden');
        dicomMetadata.classList.remove('hidden');
        dicomMeasurements.classList.remove('hidden');
        
        // Force a re-render after showing the canvas
        setTimeout(() => {
            if (dicomViewer.image) {
                console.log('Re-rendering after show...');
                dicomViewer.render();
            }
        }, 100);
    }

    function showError(message) {
        dicomLoading.classList.add('hidden');
        dicomError.classList.remove('hidden');
        dicomPlaceholder.classList.add('hidden');
        dicomCanvas.classList.add('hidden');
        dicomMetadata.classList.add('hidden');
        dicomMeasurements.classList.add('hidden');
        
        const errorElement = dicomError.querySelector('p');
        if (errorElement) {
            errorElement.textContent = message;
        }
    }

    function showPlaceholder() {
        dicomLoading.classList.add('hidden');
        dicomError.classList.add('hidden');
        dicomPlaceholder.classList.remove('hidden');
        dicomCanvas.classList.add('hidden');
        dicomMetadata.classList.add('hidden');
        dicomMeasurements.classList.add('hidden');
    }

    function updateMetadata(file, type, format) {
        document.getElementById('dicom-file-type').textContent = type.toUpperCase();
        document.getElementById('dicom-format').textContent = format;
        document.getElementById('dicom-file-size').textContent = formatFileSize(file.size);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showPdfViewer(file) {
        // For PDF files, we'll show a placeholder for now
        showError(EDIT_LABELS.pdf_viewing_is_under_development);
    }

    // Viewer controls
    dicomZoomIn.addEventListener('click', () => {
        if (dicomViewer.image) {
            dicomViewer.zoom = Math.min(5.0, dicomViewer.zoom * 1.2);
            dicomViewer.render();
        }
    });

    dicomZoomOut.addEventListener('click', () => {
        if (dicomViewer.image) {
            dicomViewer.zoom = Math.max(0.1, dicomViewer.zoom / 1.2);
            dicomViewer.render();
        }
    });

    dicomReset.addEventListener('click', () => {
        if (dicomViewer.image) {
            dicomViewer.resetView();
        }
    });

    dicomFullscreen.addEventListener('click', () => {
        if (dicomCanvas.requestFullscreen) {
            dicomCanvas.requestFullscreen();
        }
    });

    // Measurement Tools Implementation
    const dicomMeasureDistance = document.getElementById('dicom-measure-distance');
    const dicomMeasureAngle = document.getElementById('dicom-measure-angle');
    const dicomMeasureSurface = document.getElementById('dicom-measure-surface');
    const dicomMeasurementsContent = document.getElementById('dicom-measurements-content');

    console.log('Measurement buttons found:', {
        distance: !!dicomMeasureDistance,
        angle: !!dicomMeasureAngle,
        surface: !!dicomMeasureSurface,
        content: !!dicomMeasurementsContent
    });

    let measurementMode = null;
    let measurementPoints = [];
    let measurementResults = [];

    // Distance measurement
    dicomMeasureDistance.addEventListener('click', () => {
        console.log('Distance measurement button clicked');
        if (!dicomViewer.image) {
            alert(EDIT_LABELS.please_load_an_image_first);
            return;
        }
        
        measurementMode = 'distance';
        measurementPoints = [];
        dicomCanvas.style.cursor = 'crosshair';
        
        // Update button states
        dicomMeasureDistance.classList.add('bg-green-600');
        dicomMeasureAngle.classList.remove('bg-green-600');
        dicomMeasureSurface.classList.remove('bg-green-600');
        
        alert(EDIT_LABELS.click_on_two_points_to_measure_the_dista);
    });

    // Angle measurement
    dicomMeasureAngle.addEventListener('click', () => {
        if (!dicomViewer.image) {
            alert(EDIT_LABELS.please_load_an_image_first);
            return;
        }
        
        measurementMode = 'angle';
        measurementPoints = [];
        dicomCanvas.style.cursor = 'crosshair';
        
        // Update button states
        dicomMeasureDistance.classList.remove('bg-green-600');
        dicomMeasureAngle.classList.add('bg-green-600');
        dicomMeasureSurface.classList.remove('bg-green-600');
        
        alert(EDIT_LABELS.click_on_three_points_to_measure_the_ang);
    });

    // Surface measurement
    dicomMeasureSurface.addEventListener('click', () => {
        if (!dicomViewer.image) {
            alert(EDIT_LABELS.please_load_an_image_first);
            return;
        }
        
        measurementMode = 'surface';
        measurementPoints = [];
        dicomCanvas.style.cursor = 'crosshair';
        
        // Update button states
        dicomMeasureDistance.classList.remove('bg-green-600');
        dicomMeasureAngle.classList.remove('bg-green-600');
        dicomMeasureSurface.classList.add('bg-green-600');
        
        alert(EDIT_LABELS.click_on_multiple_points_to_draw_a_shape);
    });

    // Enhanced mouse click handler for measurements
    dicomCanvas.addEventListener('mousedown', function(e) {
        if (measurementMode && dicomViewer.image) {
            handleMeasurementClick(e);
        }
    });

    function handleMeasurementClick(e) {
        console.log('Measurement click detected, mode:', measurementMode);
        const rect = dicomCanvas.getBoundingClientRect();
        const x = (e.clientX - rect.left - dicomViewer.offset.x) / dicomViewer.zoom;
        const y = (e.clientY - rect.top - dicomViewer.offset.y) / dicomViewer.zoom;
        
        console.log('Click coordinates:', { x, y, clientX: e.clientX, clientY: e.clientY });
        
        measurementPoints.push({ x, y });
        console.log('Measurement points:', measurementPoints.length);
        
        // Draw measurement point
        dicomViewer.ctx.save();
        dicomViewer.ctx.fillStyle = 'red';
        dicomViewer.ctx.beginPath();
        dicomViewer.ctx.arc(x * dicomViewer.zoom + dicomViewer.offset.x, y * dicomViewer.zoom + dicomViewer.offset.y, 3, 0, 2 * Math.PI);
        dicomViewer.ctx.fill();
        dicomViewer.ctx.restore();
        
        if (measurementMode === 'distance' && measurementPoints.length === 2) {
            console.log('Calculating distance...');
            calculateDistance();
        } else if (measurementMode === 'angle' && measurementPoints.length === 3) {
            console.log('Calculating angle...');
            calculateAngle();
        } else if (measurementMode === 'surface' && measurementPoints.length >= 3) {
            // Allow more points for surface measurement
            if (e.ctrlKey || e.metaKey) {
                console.log('Calculating surface...');
                calculateSurface();
            }
        }
    }

    function calculateDistance() {
        const p1 = measurementPoints[0];
        const p2 = measurementPoints[1];
        
        const dx = p2.x - p1.x;
        const dy = p2.y - p1.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        
        // Convert to pixels and estimate real-world units
        const pixelSize = 0.1; // Assume 0.1mm per pixel (adjust based on DICOM metadata)
        const realDistance = distance * pixelSize;
        
        const result = {
            type: 'Distance',
            value: realDistance.toFixed(2),
            unit: 'mm',
            points: measurementPoints
        };
        
        measurementResults.push(result);
        updateMeasurementsDisplay();
        resetMeasurementMode();
    }

    function calculateAngle() {
        const p1 = measurementPoints[0];
        const p2 = measurementPoints[1];
        const p3 = measurementPoints[2];
        
        // Calculate vectors
        const v1 = { x: p1.x - p2.x, y: p1.y - p2.y };
        const v2 = { x: p3.x - p2.x, y: p3.y - p2.y };
        
        // Calculate angle
        const dot = v1.x * v2.x + v1.y * v2.y;
        const det = v1.x * v2.y - v1.y * v2.x;
        const angle = Math.atan2(det, dot) * 180 / Math.PI;
        
        const result = {
            type: 'Angle',
            value: Math.abs(angle).toFixed(1),
            unit: '°',
            points: measurementPoints
        };
        
        measurementResults.push(result);
        updateMeasurementsDisplay();
        resetMeasurementMode();
    }

    function calculateSurface() {
        if (measurementPoints.length < 3) return;
        
        // Calculate area using shoelace formula
        let area = 0;
        for (let i = 0; i < measurementPoints.length; i++) {
            const j = (i + 1) % measurementPoints.length;
            area += measurementPoints[i].x * measurementPoints[j].y;
            area -= measurementPoints[j].x * measurementPoints[i].y;
        }
        area = Math.abs(area) / 2;
        
        // Convert to real-world units
        const pixelSize = 0.1; // Assume 0.1mm per pixel
        const realArea = area * pixelSize * pixelSize;
        
        const result = {
            type: 'Surface',
            value: realArea.toFixed(2),
            unit: 'mm²',
            points: measurementPoints
        };
        
        measurementResults.push(result);
        updateMeasurementsDisplay();
        resetMeasurementMode();
    }

    function updateMeasurementsDisplay() {
        let html = '<h4 class="font-semibold mb-2">${EDIT_LABELS.measurements_2}</h4>';
        
        measurementResults.forEach((result, index) => {
            html += `
                <div class="mb-2 p-2 bg-gray-50 rounded">
                    <div class="font-medium">${result.type}: ${result.value} ${result.unit}</div>
                    <button onclick="removeMeasurement(${index})" class="text-xs text-red-600 hover:text-red-800">${EDIT_LABELS.delete}</button>
                </div>
            `;
        });
        
        dicomMeasurementsContent.innerHTML = html;
    }

    function removeMeasurement(index) {
        measurementResults.splice(index, 1);
        updateMeasurementsDisplay();
        // Re-render to remove the measurement lines
        if (dicomViewer.image) {
            dicomViewer.render();
            drawAllMeasurements();
        }
    }

    function resetMeasurementMode() {
        measurementMode = null;
        measurementPoints = [];
        dicomCanvas.style.cursor = 'crosshair';
        
        // Reset button states
        dicomMeasureDistance.classList.remove('bg-green-600');
        dicomMeasureAngle.classList.remove('bg-green-600');
        dicomMeasureSurface.classList.remove('bg-green-600');
    }

    function drawAllMeasurements() {
        measurementResults.forEach(result => {
            if (result.type === 'Distance' && result.points.length === 2) {
                drawDistanceLine(result.points[0], result.points[1]);
            } else if (result.type === 'Angle' && result.points.length === 3) {
                drawAngleLines(result.points[0], result.points[1], result.points[2]);
            } else if (result.type === 'Surface' && result.points.length >= 3) {
                drawSurfacePolygon(result.points);
            }
        });
    }

    function drawDistanceLine(p1, p2) {
        dicomViewer.ctx.save();
        dicomViewer.ctx.strokeStyle = 'red';
        dicomViewer.ctx.lineWidth = 2;
        dicomViewer.ctx.beginPath();
        dicomViewer.ctx.moveTo(p1.x * dicomViewer.zoom + dicomViewer.offset.x, p1.y * dicomViewer.zoom + dicomViewer.offset.y);
        dicomViewer.ctx.lineTo(p2.x * dicomViewer.zoom + dicomViewer.offset.x, p2.y * dicomViewer.zoom + dicomViewer.offset.y);
        dicomViewer.ctx.stroke();
        dicomViewer.ctx.restore();
    }

    function drawAngleLines(p1, p2, p3) {
        dicomViewer.ctx.save();
        dicomViewer.ctx.strokeStyle = 'blue';
        dicomViewer.ctx.lineWidth = 2;
        dicomViewer.ctx.beginPath();
        dicomViewer.ctx.moveTo(p1.x * dicomViewer.zoom + dicomViewer.offset.x, p1.y * dicomViewer.zoom + dicomViewer.offset.y);
        dicomViewer.ctx.lineTo(p2.x * dicomViewer.zoom + dicomViewer.offset.x, p2.y * dicomViewer.zoom + dicomViewer.offset.y);
        dicomViewer.ctx.lineTo(p3.x * dicomViewer.zoom + dicomViewer.offset.x, p3.y * dicomViewer.zoom + dicomViewer.offset.y);
        dicomViewer.ctx.stroke();
        dicomViewer.ctx.restore();
    }

    function drawSurfacePolygon(points) {
        dicomViewer.ctx.save();
        dicomViewer.ctx.strokeStyle = 'green';
        dicomViewer.ctx.fillStyle = 'rgba(0, 255, 0, 0.2)';
        dicomViewer.ctx.lineWidth = 2;
        dicomViewer.ctx.beginPath();
        dicomViewer.ctx.moveTo(points[0].x * dicomViewer.zoom + dicomViewer.offset.x, points[0].y * dicomViewer.zoom + dicomViewer.offset.y);
        for (let i = 1; i < points.length; i++) {
            dicomViewer.ctx.lineTo(points[i].x * dicomViewer.zoom + dicomViewer.offset.x, points[i].y * dicomViewer.zoom + dicomViewer.offset.y);
        }
        dicomViewer.ctx.closePath();
        dicomViewer.ctx.fill();
        dicomViewer.ctx.stroke();
        dicomViewer.ctx.restore();
    }

    // Override the render method to include measurements
    const originalRender = dicomViewer.render;
    dicomViewer.render = function() {
        originalRender.call(this);
        drawAllMeasurements();
    };

    // Global function for removing measurements
    window.removeMeasurement = removeMeasurement;

    // Doping Control Result Fields Management
    function updateDopingResultFields() {
        const dopingSelect = document.getElementById('loinc_doping_panel');
        const resultFields = document.getElementById('doping-result-fields');
        const unitField = document.getElementById('doping_result_unit');
        const normalRangeField = document.getElementById('doping_normal_range');
        const thresholdField = document.getElementById('doping_threshold');

        if (dopingSelect.value) {
            resultFields.style.display = 'block';
            
            // Define doping test configurations
            const dopingConfigs = {
                // Stéroïdes anabolisants
                'LOINC_11559-2_TEST': {
                    unit: 'Ratio',
                    normalRange: '0.5 - 4.0',
                    threshold: '4.0 (WADA)'
                },
                'LOINC_11559-2_NAND': {
                    unit: 'ng/mL',
                    normalRange: '< 2.0',
                    threshold: '2.0 ng/mL'
                },
                'LOINC_11559-2_STAN': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '1.0 ng/mL'
                },
                'LOINC_11559-2_METH': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '1.0 ng/mL'
                },
                'LOINC_11559-2_DECA': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '2.0 ng/mL'
                },
                'LOINC_11559-2_BOLD': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '1.0 ng/mL'
                },
                'LOINC_11559-2_TREN': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '1.0 ng/mL'
                },
                'LOINC_11559-2_OXAN': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '1.0 ng/mL'
                },
                
                // Hormones peptidiques
                'LOINC_11560-0_GH': {
                    unit: 'ng/mL',
                    normalRange: '0.1 - 10.0',
                    threshold: EDIT_LABELS.variable_depending_on_method
                },
                'LOINC_11560-0_IGF': {
                    unit: 'ng/mL',
                    normalRange: '100 - 300',
                    threshold: EDIT_LABELS.variable_depending_on_method
                },
                'LOINC_11560-0_EPO': {
                    unit: 'mIU/mL',
                    normalRange: '3.7 - 16.9',
                    threshold: EDIT_LABELS.variable_depending_on_method
                },
                'LOINC_11560-0_HCG': {
                    unit: 'mIU/mL',
                    normalRange: '< 5.0',
                    threshold: '5.0 mIU/mL'
                },
                'LOINC_11560-0_LH': {
                    unit: 'mIU/mL',
                    normalRange: '1.7 - 8.6',
                    threshold: EDIT_LABELS.variable_depending_on_method
                },
                'LOINC_11560-0_FSH': {
                    unit: 'mIU/mL',
                    normalRange: '1.5 - 12.4',
                    threshold: EDIT_LABELS.variable_depending_on_method
                },
                'LOINC_11560-0_ACTH': {
                    unit: 'pg/mL',
                    normalRange: '7.2 - 63.3',
                    threshold: EDIT_LABELS.variable_depending_on_method
                },
                'LOINC_11560-0_TSH': {
                    unit: 'μIU/mL',
                    normalRange: '0.4 - 4.0',
                    threshold: EDIT_LABELS.variable_depending_on_method
                },
                
                // Bêta-2 agonistes
                'LOINC_11561-8_SALB': {
                    unit: 'ng/mL',
                    normalRange: '< 100',
                    threshold: '1000 ng/mL'
                },
                'LOINC_11561-8_TERB': {
                    unit: 'ng/mL',
                    normalRange: '< 50',
                    threshold: '500 ng/mL'
                },
                'LOINC_11561-8_FORM': {
                    unit: 'ng/mL',
                    normalRange: '< 20',
                    threshold: '200 ng/mL'
                },
                'LOINC_11561-8_SALM': {
                    unit: 'ng/mL',
                    normalRange: '< 10',
                    threshold: '100 ng/mL'
                },
                'LOINC_11561-8_CLEN': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '2.0 ng/mL'
                },
                'LOINC_11561-8_FENO': {
                    unit: 'ng/mL',
                    normalRange: '< 30',
                    threshold: '300 ng/mL'
                },
                
                // Diurétiques
                'LOINC_11562-6_FURO': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '50 ng/mL'
                },
                'LOINC_11562-6_HCTZ': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '50 ng/mL'
                },
                'LOINC_11562-6_SPIR': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '50 ng/mL'
                },
                'LOINC_11562-6_AMIL': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '50 ng/mL'
                },
                'LOINC_11562-6_TRIAM': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '50 ng/mL'
                },
                'LOINC_11562-6_CHLOR': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '50 ng/mL'
                },
                
                // Stimulants
                'LOINC_11564-2_AMPH': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '500 ng/mL'
                },
                'LOINC_11564-2_METH': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '500 ng/mL'
                },
                'LOINC_11564-2_EPHE': {
                    unit: 'ng/mL',
                    normalRange: '< 100',
                    threshold: '1000 ng/mL'
                },
                'LOINC_11564-2_PSEU': {
                    unit: 'ng/mL',
                    normalRange: '< 100',
                    threshold: '1000 ng/mL'
                },
                'LOINC_11564-2_COCA': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '150 ng/mL'
                },
                'LOINC_11564-2_METHY': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '500 ng/mL'
                },
                'LOINC_11564-2_MODAF': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '500 ng/mL'
                },
                
                // Cannabinoïdes
                'LOINC_11566-7_THC': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '150 ng/mL'
                },
                'LOINC_11566-7_CBD': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '150 ng/mL'
                },
                'LOINC_11566-7_CBN': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '150 ng/mL'
                },
                'LOINC_11566-7_METAB': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '150 ng/mL'
                },
                
                // Glucocorticoïdes
                'LOINC_11567-5_PRED': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '30 ng/mL'
                },
                'LOINC_11567-5_DEXA': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '30 ng/mL'
                },
                'LOINC_11567-5_HYDRO': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '30 ng/mL'
                },
                'LOINC_11567-5_METHY': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '30 ng/mL'
                },
                'LOINC_11567-5_TRIAM': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '30 ng/mL'
                },
                'LOINC_11567-5_BETAM': {
                    unit: 'ng/mL',
                    normalRange: EDIT_LABELS.not_detectable,
                    threshold: '30 ng/mL'
                }
            };

            const selectedValue = dopingSelect.value;
            const config = dopingConfigs[selectedValue];

            if (config) {
                unitField.value = config.unit;
                normalRangeField.value = config.normalRange;
                thresholdField.value = config.threshold;
            } else {
                // For general panels and methods
                unitField.value = 'Variable';
                normalRangeField.value = EDIT_LABELS.depending_on_substance;
                thresholdField.value = EDIT_LABELS.depending_on_substance;
            }
        } else {
            resultFields.style.display = 'none';
        }
    }

    // Global function for doping control
    window.updateDopingResultFields = updateDopingResultFields;

    // Blood Test Result Fields Management
    function updateBloodTestResultFields() {
        console.log('updateBloodTestResultFields called');
        const bloodTestSelect = document.getElementById('blood_test_panel');
        const resultFields = document.getElementById('blood-test-result-fields');
        const unitField = document.getElementById('blood_test_result_unit');
        const normalRangeField = document.getElementById('blood_test_normal_range');
        const referenceField = document.getElementById('blood_test_reference');
        
        console.log('Selected value:', bloodTestSelect.value);

        if (bloodTestSelect.value) {
            resultFields.style.display = 'block';
            
            // Define blood test configurations
            const bloodTestConfigs = {
                // Hématologie
                'LOINC_58410-2_CBC': {
                    unit: 'K/μL',
                    normalRange: '4.5 - 11.0',
                    reference: EDIT_LABELS.complete_cbc
                },
                'LOINC_58410-2_HGB': {
                    unit: 'g/dL',
                    normalRange: '12.0 - 16.0 (F), 14.0 - 18.0 (M)',
                    reference: EDIT_LABELS.hemoglobin
                },
                'LOINC_58410-2_HCT': {
                    unit: '%',
                    normalRange: '36.0 - 46.0 (F), 41.0 - 50.0 (M)',
                    reference: EDIT_LABELS.hematocrit
                },
                'LOINC_58410-2_RBC': {
                    unit: 'M/μL',
                    normalRange: '4.2 - 5.4 (F), 4.7 - 6.1 (M)',
                    reference: EDIT_LABELS.red_blood_cells
                },
                'LOINC_58410-2_WBC': {
                    unit: 'K/μL',
                    normalRange: '4.5 - 11.0',
                    reference: EDIT_LABELS.white_blood_cells
                },
                'LOINC_58410-2_PLT': {
                    unit: 'K/μL',
                    normalRange: '150 - 450',
                    reference: EDIT_LABELS.platelets
                },
                'LOINC_58410-2_MCV': {
                    unit: 'fL',
                    normalRange: '80 - 100',
                    reference: EDIT_LABELS.mean_corpuscular_volume
                },
                'LOINC_58410-2_MCH': {
                    unit: 'pg',
                    normalRange: '27 - 33',
                    reference: EDIT_LABELS.mean_corpuscular_hemoglobin
                },
                'LOINC_58410-2_MCHC': {
                    unit: 'g/dL',
                    normalRange: '32 - 36',
                    reference: EDIT_LABELS.mean_corpuscular_hemoglobin_concentratio_2
                },
                'LOINC_58410-2_RDW': {
                    unit: '%',
                    normalRange: '11.5 - 14.5',
                    reference: EDIT_LABELS.red_cell_distribution_width
                },
                'LOINC_58410-2_MPV': {
                    unit: 'fL',
                    normalRange: '7.5 - 11.5',
                    reference: EDIT_LABELS.mean_platelet_volume
                },
                'LOINC_58410-2_NEUT': {
                    unit: '%',
                    normalRange: '40 - 70',
                    reference: EDIT_LABELS.neutrophils
                },
                'LOINC_58410-2_LYMPH': {
                    unit: '%',
                    normalRange: '20 - 40',
                    reference: 'Lymphocytes'
                },
                'LOINC_58410-2_MONO': {
                    unit: '%',
                    normalRange: '2 - 8',
                    reference: 'Monocytes'
                },
                'LOINC_58410-2_EOS': {
                    unit: '%',
                    normalRange: '1 - 4',
                    reference: EDIT_LABELS.eosinophils
                },
                'LOINC_58410-2_BASO': {
                    unit: '%',
                    normalRange: '0.5 - 1',
                    reference: EDIT_LABELS.basophils
                },
                
                // Biochimie
                'LOINC_58409-4_GLU': {
                    unit: 'mg/dL',
                    normalRange: EDIT_LABELS.k_70_100_fasting,
                    reference: 'Glucose'
                },
                'LOINC_58409-4_CREA': {
                    unit: 'mg/dL',
                    normalRange: '0.6 - 1.2 (F), 0.8 - 1.3 (M)',
                    reference: EDIT_LABELS.creatinine
                },
                'LOINC_58409-4_BUN': {
                    unit: 'mg/dL',
                    normalRange: '7 - 20',
                    reference: EDIT_LABELS.blood_urea_nitrogen
                },
                'LOINC_58409-4_NA': {
                    unit: 'mEq/L',
                    normalRange: '135 - 145',
                    reference: 'Sodium'
                },
                'LOINC_58409-4_K': {
                    unit: 'mEq/L',
                    normalRange: '3.5 - 5.0',
                    reference: 'Potassium'
                },
                'LOINC_58409-4_CL': {
                    unit: 'mEq/L',
                    normalRange: '96 - 106',
                    reference: EDIT_LABELS.chloride
                },
                'LOINC_58409-4_CO2': {
                    unit: 'mEq/L',
                    normalRange: '22 - 28',
                    reference: EDIT_LABELS.total_co2
                },
                'LOINC_58409-4_CA': {
                    unit: 'mg/dL',
                    normalRange: '8.5 - 10.5',
                    reference: 'Calcium'
                },
                'LOINC_58409-4_PHOS': {
                    unit: 'mg/dL',
                    normalRange: '2.5 - 4.5',
                    reference: EDIT_LABELS.phosphorus
                },
                'LOINC_58409-4_MG': {
                    unit: 'mg/dL',
                    normalRange: '1.5 - 2.5',
                    reference: EDIT_LABELS.magnesium
                },
                'LOINC_58409-4_UA': {
                    unit: 'mg/dL',
                    normalRange: '2.4 - 6.0 (F), 3.4 - 7.0 (M)',
                    reference: EDIT_LABELS.uric_acid
                },
                'LOINC_58409-4_LDH': {
                    unit: 'U/L',
                    normalRange: '140 - 280',
                    reference: EDIT_LABELS.lactate_dehydrogenase
                },
                'LOINC_58409-4_CPK': {
                    unit: 'U/L',
                    normalRange: '30 - 200',
                    reference: EDIT_LABELS.creatine_phosphokinase
                },
                
                // Lipides
                'LOINC_58408-6_CHOL': {
                    unit: 'mg/dL',
                    normalRange: '< 200',
                    reference: EDIT_LABELS.total_cholesterol
                },
                'LOINC_58408-6_HDL': {
                    unit: 'mg/dL',
                    normalRange: '> 40 (M), > 50 (F)',
                    reference: EDIT_LABELS.hdl_cholesterol
                },
                'LOINC_58408-6_LDL': {
                    unit: 'mg/dL',
                    normalRange: '< 100 (optimal)',
                    reference: EDIT_LABELS.ldl_cholesterol
                },
                'LOINC_58408-6_TRIG': {
                    unit: 'mg/dL',
                    normalRange: '< 150',
                    reference: EDIT_LABELS.triglycerides
                },
                'LOINC_58408-6_APOA': {
                    unit: 'mg/dL',
                    normalRange: '110 - 200',
                    reference: EDIT_LABELS.apolipoprotein_a
                },
                'LOINC_58408-6_APOB': {
                    unit: 'mg/dL',
                    normalRange: '60 - 130',
                    reference: EDIT_LABELS.apolipoprotein_b
                },
                'LOINC_58408-6_LP': {
                    unit: 'mg/dL',
                    normalRange: '< 30',
                    reference: EDIT_LABELS.lipoprotein_a
                },
                'LOINC_58408-6_NONHDL': {
                    unit: 'mg/dL',
                    normalRange: '< 130',
                    reference: EDIT_LABELS.non_hdl_cholesterol
                },
                'LOINC_58408-6_RATIO': {
                    unit: 'Ratio',
                    normalRange: '< 5.0',
                    reference: EDIT_LABELS.total_cholesterol_hdl_ratio
                },
                
                // Enzymes hépatiques
                'LOINC_58408-6_ALT': {
                    unit: 'U/L',
                    normalRange: '7 - 55',
                    reference: EDIT_LABELS.alanine_aminotransferase
                },
                'LOINC_58408-6_AST': {
                    unit: 'U/L',
                    normalRange: '8 - 48',
                    reference: EDIT_LABELS.aspartate_aminotransferase
                },
                'LOINC_58408-6_ALP': {
                    unit: 'U/L',
                    normalRange: '44 - 147',
                    reference: EDIT_LABELS.alkaline_phosphatase
                },
                'LOINC_58408-6_GGT': {
                    unit: 'U/L',
                    normalRange: '9 - 48 (F), 12 - 64 (M)',
                    reference: EDIT_LABELS.gamma_glutamyl_transferase
                },
                'LOINC_58408-6_TBIL': {
                    unit: 'mg/dL',
                    normalRange: '0.3 - 1.2',
                    reference: EDIT_LABELS.total_bilirubin
                },
                'LOINC_58408-6_DBIL': {
                    unit: 'mg/dL',
                    normalRange: '0.1 - 0.3',
                    reference: EDIT_LABELS.direct_bilirubin
                },
                'LOINC_58408-6_IBIL': {
                    unit: 'mg/dL',
                    normalRange: '0.2 - 0.9',
                    reference: EDIT_LABELS.indirect_bilirubin
                },
                'LOINC_58408-6_ALB': {
                    unit: 'g/dL',
                    normalRange: '3.4 - 5.4',
                    reference: EDIT_LABELS.albumin
                },
                'LOINC_58408-6_TP': {
                    unit: 'g/dL',
                    normalRange: '6.0 - 8.3',
                    reference: EDIT_LABELS.total_protein
                },
                'LOINC_58408-6_GLOB': {
                    unit: 'g/dL',
                    normalRange: '2.0 - 3.5',
                    reference: EDIT_LABELS.globulins
                },
                'LOINC_58408-6_AG': {
                    unit: 'Ratio',
                    normalRange: '1.1 - 2.2',
                    reference: EDIT_LABELS.albumin_globulin_ratio
                },
                
                // Marqueurs cardiaques
                'LOINC_58408-6_TROP': {
                    unit: 'ng/mL',
                    normalRange: '< 0.04',
                    reference: EDIT_LABELS.troponin
                },
                'LOINC_58408-6_CK': {
                    unit: 'U/L',
                    normalRange: '30 - 200',
                    reference: EDIT_LABELS.creatine_kinase
                },
                'LOINC_58408-6_CKMB': {
                    unit: 'ng/mL',
                    normalRange: '< 5.0',
                    reference: EDIT_LABELS.creatine_kinase_mb
                },
                'LOINC_58408-6_BNP': {
                    unit: 'pg/mL',
                    normalRange: '< 100',
                    reference: EDIT_LABELS.b_type_natriuretic_peptide
                },
                'LOINC_58408-6_NT': {
                    unit: 'pg/mL',
                    normalRange: '< 125',
                    reference: 'NT-proBNP'
                },
                'LOINC_58408-6_CRP': {
                    unit: 'mg/L',
                    normalRange: '< 3.0',
                    reference: EDIT_LABELS.c_reactive_protein
                },
                'LOINC_58408-6_ESR': {
                    unit: 'mm/h',
                    normalRange: '0 - 20 (F), 0 - 15 (M)',
                    reference: EDIT_LABELS.erythrocyte_sedimentation_rate
                },
                'LOINC_58408-6_HSCRP': {
                    unit: 'mg/L',
                    normalRange: '< 1.0',
                    reference: EDIT_LABELS.high_sensitivity_crp
                },
                'LOINC_58408-6_LPA': {
                    unit: 'mg/dL',
                    normalRange: '< 30',
                    reference: EDIT_LABELS.lipoprotein_a
                },
                'LOINC_58408-6_HOMOC': {
                    unit: 'μmol/L',
                    normalRange: '5 - 15',
                    reference: EDIT_LABELS.homocysteine
                },
                
                // Hormones
                'LOINC_58408-6_TSH': {
                    unit: 'μIU/mL',
                    normalRange: '0.4 - 4.0',
                    reference: 'TSH'
                },
                'LOINC_58408-6_T4': {
                    unit: 'ng/dL',
                    normalRange: '0.8 - 1.8',
                    reference: EDIT_LABELS.free_t4
                },
                'LOINC_58408-6_T3': {
                    unit: 'pg/mL',
                    normalRange: '2.3 - 4.2',
                    reference: EDIT_LABELS.free_t3
                },
                'LOINC_58408-6_CORT': {
                    unit: 'μg/dL',
                    normalRange: '6.2 - 19.4',
                    reference: 'Cortisol'
                },
                'LOINC_58408-6_INSU': {
                    unit: 'μIU/mL',
                    normalRange: '3 - 25',
                    reference: EDIT_LABELS.insulin
                },
                'LOINC_58408-6_HBA1C': {
                    unit: '%',
                    normalRange: '4.0 - 5.6',
                    reference: EDIT_LABELS.glycated_hemoglobin
                },
                'LOINC_58408-6_VITD': {
                    unit: 'ng/mL',
                    normalRange: '30 - 100',
                    reference: EDIT_LABELS.vitamin_d
                },
                'LOINC_58408-6_FOL': {
                    unit: 'ng/mL',
                    normalRange: '2.0 - 20.0',
                    reference: EDIT_LABELS.folic_acid
                },
                'LOINC_58408-6_B12': {
                    unit: 'pg/mL',
                    normalRange: '200 - 900',
                    reference: EDIT_LABELS.vitamin_b12
                },
                'LOINC_58408-6_TEST': {
                    unit: 'ng/dL',
                    normalRange: '300 - 1000',
                    reference: EDIT_LABELS.testosterone
                },
                'LOINC_58408-6_EST': {
                    unit: 'pg/mL',
                    normalRange: '12.5 - 166 (F)',
                    reference: 'Estradiol'
                },
                'LOINC_58408-6_PROG': {
                    unit: 'ng/mL',
                    normalRange: '0.1 - 0.8 (F)',
                    reference: EDIT_LABELS.progesterone
                },
                'LOINC_58408-6_FSH': {
                    unit: 'mIU/mL',
                    normalRange: '1.5 - 12.4',
                    reference: 'FSH'
                },
                'LOINC_58408-6_LH': {
                    unit: 'mIU/mL',
                    normalRange: '1.7 - 8.6',
                    reference: 'LH'
                },
                'LOINC_58408-6_PROL': {
                    unit: 'ng/mL',
                    normalRange: '4.8 - 23.3',
                    reference: EDIT_LABELS.prolactin
                },
                'LOINC_58408-6_GH': {
                    unit: 'ng/mL',
                    normalRange: '0.1 - 10.0',
                    reference: EDIT_LABELS.growth_hormone
                },
                'LOINC_58408-6_IGF': {
                    unit: 'ng/mL',
                    normalRange: '100 - 300',
                    reference: 'IGF-1'
                },
                
                // Marqueurs inflammatoires
                'LOINC_58408-6_IL6': {
                    unit: 'pg/mL',
                    normalRange: '< 5.0',
                    reference: EDIT_LABELS.interleukin_6
                },
                'LOINC_58408-6_TNF': {
                    unit: 'pg/mL',
                    normalRange: '< 8.1',
                    reference: 'TNF-α'
                },
                'LOINC_58408-6_FER': {
                    unit: 'ng/mL',
                    normalRange: '13 - 150 (F), 30 - 400 (M)',
                    reference: EDIT_LABELS.ferritin
                },
                'LOINC_58408-6_IRON': {
                    unit: 'μg/dL',
                    normalRange: '60 - 170',
                    reference: EDIT_LABELS.serum_iron
                },
                'LOINC_58408-6_TIBC': {
                    unit: 'μg/dL',
                    normalRange: '240 - 450',
                    reference: EDIT_LABELS.total_iron_binding_capacity
                },
                'LOINC_58408-6_UIBC': {
                    unit: 'μg/dL',
                    normalRange: '111 - 343',
                    reference: EDIT_LABELS.unsaturated_iron_binding_capacity
                },
                'LOINC_58408-6_SAT': {
                    unit: '%',
                    normalRange: '20 - 50',
                    reference: EDIT_LABELS.iron_saturation
                },
                'LOINC_58408-6_TRANS': {
                    unit: 'mg/dL',
                    normalRange: '200 - 400',
                    reference: EDIT_LABELS.transferrin
                },
                'LOINC_58408-6_CERUL': {
                    unit: 'mg/dL',
                    normalRange: '20 - 60',
                    reference: EDIT_LABELS.ceruloplasmin
                },
                'LOINC_58408-6_COPPER': {
                    unit: 'μg/dL',
                    normalRange: '70 - 140',
                    reference: EDIT_LABELS.copper
                },
                'LOINC_58408-6_ZINC': {
                    unit: 'μg/dL',
                    normalRange: '60 - 120',
                    reference: 'Zinc'
                },
                'LOINC_58408-6_SELEN': {
                    unit: 'μg/L',
                    normalRange: '70 - 150',
                    reference: EDIT_LABELS.selenium
                },
                
                // Marqueurs tumoraux
                'LOINC_58408-6_PSA': {
                    unit: 'ng/mL',
                    normalRange: '< 4.0',
                    reference: 'PSA'
                },
                'LOINC_58408-6_CEA': {
                    unit: 'ng/mL',
                    normalRange: '< 3.0',
                    reference: 'CEA'
                },
                'LOINC_58408-6_AFP': {
                    unit: 'ng/mL',
                    normalRange: '< 10.0',
                    reference: 'AFP'
                },
                'LOINC_58408-6_CA125': {
                    unit: 'U/mL',
                    normalRange: '< 35.0',
                    reference: 'CA 125'
                },
                'LOINC_58408-6_CA199': {
                    unit: 'U/mL',
                    normalRange: '< 37.0',
                    reference: 'CA 19-9'
                },
                'LOINC_58408-6_CA153': {
                    unit: 'U/mL',
                    normalRange: '< 30.0',
                    reference: 'CA 15-3'
                },
                'LOINC_58408-6_CA724': {
                    unit: 'U/mL',
                    normalRange: '< 6.9',
                    reference: 'CA 72-4'
                },
                'LOINC_58408-6_SCC': {
                    unit: 'ng/mL',
                    normalRange: '< 1.5',
                    reference: 'SCC'
                },
                'LOINC_58408-6_NSE': {
                    unit: 'ng/mL',
                    normalRange: '< 16.3',
                    reference: 'NSE'
                },
                'LOINC_58408-6_CYFRA': {
                    unit: 'ng/mL',
                    normalRange: '< 3.3',
                    reference: 'CYFRA 21-1'
                },
                
                // Marqueurs d'auto-immunité
                'LOINC_58408-6_ANA': {
                    unit: 'Titre',
                    normalRange: '< 1:40',
                    reference: EDIT_LABELS.antinuclear_antibodies
                },
                'LOINC_58408-6_RF': {
                    unit: 'IU/mL',
                    normalRange: '< 14',
                    reference: EDIT_LABELS.rheumatoid_factor
                },
                'LOINC_58408-6_CCP': {
                    unit: 'U/mL',
                    normalRange: '< 17',
                    reference: EDIT_LABELS.cyclic_citrullinated_peptide
                },
                'LOINC_58408-6_DSDNA': {
                    unit: 'IU/mL',
                    normalRange: '< 30',
                    reference: EDIT_LABELS.anti_double_stranded_dna_antibodies
                },
                'LOINC_58408-6_SM': {
                    unit: 'U/mL',
                    normalRange: '< 20',
                    reference: EDIT_LABELS.anti_sm_antibodies
                },
                'LOINC_58408-6_RO': {
                    unit: 'U/mL',
                    normalRange: '< 20',
                    reference: EDIT_LABELS.anti_ro_ssa_antibodies
                },
                'LOINC_58408-6_LA': {
                    unit: 'U/mL',
                    normalRange: '< 20',
                    reference: EDIT_LABELS.anti_la_ssb_antibodies
                },
                'LOINC_58408-6_ANCA': {
                    unit: 'Titre',
                    normalRange: '< 1:20',
                    reference: 'ANCA'
                },
                'LOINC_58408-6_ASMA': {
                    unit: 'Titre',
                    normalRange: '< 1:20',
                    reference: EDIT_LABELS.anti_smooth_muscle_antibodies
                },
                'LOINC_58408-6_AMA': {
                    unit: 'Titre',
                    normalRange: '< 1:20',
                    reference: EDIT_LABELS.anti_mitochondrial_antibodies
                }
            };

            const selectedValue = bloodTestSelect.value;
            const config = bloodTestConfigs[selectedValue];

            if (config) {
                unitField.value = config.unit;
                normalRangeField.value = config.normalRange;
                referenceField.value = config.reference;
            } else if (selectedValue === '58410-2' || selectedValue === '58409-4' || selectedValue === '58408-6') {
                console.log('Panel detected:', selectedValue);
                // Handle panel selections
                const panelConfigs = {
                    '58410-2': {
                        unit: 'Multiple',
                        normalRange: EDIT_LABELS.complete_panel_see_details_below,
                        reference: EDIT_LABELS.comprehensive_metabolic_panel_loinc_5841,
                        description: EDIT_LABELS.includes_cbc_biochemistry_lipids_liver_e,
                        tests: [
                            EDIT_LABELS.hemoglobin_12_0_16_0_g_dl_f_14_0_18_0_g_,
                            EDIT_LABELS.glucose_70_100_mg_dl_fasting,
                            EDIT_LABELS.creatinine_0_6_1_2_mg_dl_f_0_8_1_3_mg_dl,
                            'Sodium: 135-145 mEq/L',
                            'Potassium: 3.5-5.0 mEq/L',
                            EDIT_LABELS.total_cholesterol_200_mg_dl,
                            'HDL: > 40 mg/dL (M), > 50 mg/dL (F)',
                            'LDL: < 100 mg/dL (optimal)',
                            EDIT_LABELS.triglycerides_150_mg_dl,
                            'ALT: 7-55 U/L',
                            'AST: 8-48 U/L',
                            'ALP: 44-147 U/L',
                            EDIT_LABELS.total_bilirubin_0_3_1_2_mg_dl
                        ]
                    },
                    '58409-4': {
                        unit: 'Multiple',
                        normalRange: EDIT_LABELS.basic_panel_see_details_below,
                        reference: EDIT_LABELS.basic_metabolic_panel_loinc_58409_4,
                        description: EDIT_LABELS.includes_biochemistry_electrolytes,
                        tests: [
                            EDIT_LABELS.glucose_70_100_mg_dl_fasting,
                            EDIT_LABELS.creatinine_0_6_1_2_mg_dl_f_0_8_1_3_mg_dl,
                            EDIT_LABELS.blood_urea_nitrogen_7_20_mg_dl,
                            'Sodium: 135-145 mEq/L',
                            'Potassium: 3.5-5.0 mEq/L',
                            EDIT_LABELS.chloride_96_106_meq_l,
                            EDIT_LABELS.total_co2_22_28_meq_l,
                            'Calcium: 8.5-10.5 mg/dL',
                            EDIT_LABELS.phosphorus_2_5_4_5_mg_dl,
                            EDIT_LABELS.magnesium_1_5_2_5_mg_dl,
                            EDIT_LABELS.uric_acid_2_4_6_0_mg_dl_f_3_4_7_0_mg_dl_,
                            EDIT_LABELS.lactate_dehydrogenase_140_280_u_l,
                            EDIT_LABELS.creatine_phosphokinase_30_200_u_l
                        ]
                    },
                    '58408-6': {
                        unit: 'Multiple',
                        normalRange: EDIT_LABELS.extended_panel_see_details_below,
                        reference: EDIT_LABELS.extended_metabolic_panel_loinc_58408_6,
                        description: EDIT_LABELS.includes_complete_cardiac_markers_hormon,
                        tests: [
                            EDIT_LABELS.all_tests_from_the_complete_panel_58410_,
                            EDIT_LABELS.troponin_0_04_ng_ml,
                            'CPK-MB: < 5.0 ng/mL',
                            'BNP: < 100 pg/mL',
                            'NT-proBNP: < 125 pg/mL',
                            'CRP: < 3.0 mg/L',
                            EDIT_LABELS.high_sensitivity_crp_1_0_mg_l,
                            'TSH: 0.4-4.0 μIU/mL',
                            EDIT_LABELS.free_t4_0_8_1_8_ng_dl,
                            EDIT_LABELS.free_t3_2_3_4_2_pg_ml,
                            'Cortisol: 6.2-19.4 μg/dL',
                            EDIT_LABELS.insulin_3_25_iu_ml,
                            'HbA1c: 4.0-5.6%',
                            EDIT_LABELS.vitamin_d_30_100_ng_ml,
                            EDIT_LABELS.testosterone_300_1000_ng_dl,
                            EDIT_LABELS.ferritin_13_150_ng_ml_f_30_400_ng_ml_m,
                            EDIT_LABELS.serum_iron_60_170_g_dl,
                            EDIT_LABELS.transferrin_200_400_mg_dl,
                            'PSA: < 4.0 ng/mL',
                            'CEA: < 3.0 ng/mL',
                            'AFP: < 10.0 ng/mL'
                        ]
                    }
                };

                const panelConfig = panelConfigs[selectedValue];
                if (panelConfig) {
                    unitField.value = panelConfig.unit;
                    normalRangeField.value = panelConfig.normalRange;
                    referenceField.value = panelConfig.reference;
                    
                    // Show panel tests container and create dynamic test cards
                    const panelContainer = document.getElementById('panel-tests-container');
                    const panelGrid = document.getElementById('panel-tests-grid');
                    
                    console.log('Panel container found:', !!panelContainer);
                    console.log('Panel grid found:', !!panelGrid);
                    
                    if (panelContainer && panelGrid) {
                        panelContainer.style.display = 'block';
                        panelGrid.innerHTML = ''; // Clear existing cards
                        
                        // Create detailed test configurations for each panel
                        const detailedPanelTests = {
                            '58410-2': [
                                { name: EDIT_LABELS.hemoglobin, unit: 'g/dL', normalRange: '12.0-16.0 (F), 14.0-18.0 (M)', reference: 'HGB' },
                                { name: EDIT_LABELS.hematocrit, unit: '%', normalRange: '36.0-46.0 (F), 41.0-50.0 (M)', reference: 'HCT' },
                                { name: EDIT_LABELS.red_blood_cells, unit: 'M/μL', normalRange: '4.2-5.4 (F), 4.7-6.1 (M)', reference: 'RBC' },
                                { name: EDIT_LABELS.white_blood_cells, unit: 'K/μL', normalRange: '4.5-11.0', reference: 'WBC' },
                                { name: EDIT_LABELS.platelets, unit: 'K/μL', normalRange: '150-450', reference: 'PLT' },
                                { name: 'Glucose', unit: 'mg/dL', normalRange: EDIT_LABELS.k_70_100_fasting_2, reference: 'GLU' },
                                { name: EDIT_LABELS.creatinine, unit: 'mg/dL', normalRange: '0.6-1.2 (F), 0.8-1.3 (M)', reference: 'CREA' },
                                { name: EDIT_LABELS.blood_urea_nitrogen_2, unit: 'mg/dL', normalRange: '7-20', reference: 'BUN' },
                                { name: 'Sodium', unit: 'mEq/L', normalRange: '135-145', reference: 'NA' },
                                { name: 'Potassium', unit: 'mEq/L', normalRange: '3.5-5.0', reference: 'K' },
                                { name: EDIT_LABELS.chloride, unit: 'mEq/L', normalRange: '96-106', reference: 'CL' },
                                { name: EDIT_LABELS.total_co2, unit: 'mEq/L', normalRange: '22-28', reference: 'CO2' },
                                { name: 'Calcium', unit: 'mg/dL', normalRange: '8.5-10.5', reference: 'CA' },
                                { name: EDIT_LABELS.phosphorus, unit: 'mg/dL', normalRange: '2.5-4.5', reference: 'PHOS' },
                                { name: EDIT_LABELS.magnesium, unit: 'mg/dL', normalRange: '1.5-2.5', reference: 'MG' },
                                { name: EDIT_LABELS.total_cholesterol, unit: 'mg/dL', normalRange: '< 200', reference: 'CHOL' },
                                { name: 'HDL', unit: 'mg/dL', normalRange: '> 40 (M), > 50 (F)', reference: 'HDL' },
                                { name: 'LDL', unit: 'mg/dL', normalRange: '< 100 (optimal)', reference: 'LDL' },
                                { name: EDIT_LABELS.triglycerides, unit: 'mg/dL', normalRange: '< 150', reference: 'TRIG' },
                                { name: 'ALT', unit: 'U/L', normalRange: '7-55', reference: 'ALT' },
                                { name: 'AST', unit: 'U/L', normalRange: '8-48', reference: 'AST' },
                                { name: 'ALP', unit: 'U/L', normalRange: '44-147', reference: 'ALP' },
                                { name: EDIT_LABELS.total_bilirubin, unit: 'mg/dL', normalRange: '0.3-1.2', reference: 'TBIL' },
                                { name: EDIT_LABELS.total_protein, unit: 'g/dL', normalRange: '6.0-8.3', reference: 'TP' },
                                { name: EDIT_LABELS.albumin, unit: 'g/dL', normalRange: '3.4-5.0', reference: 'ALB' },
                                { name: EDIT_LABELS.globulins, unit: 'g/dL', normalRange: '2.0-3.5', reference: 'GLOB' },
                                { name: EDIT_LABELS.a_g_ratio, unit: 'Ratio', normalRange: '1.1-2.2', reference: 'A/G' }
                            ],
                            '58409-4': [
                                { name: 'Glucose', unit: 'mg/dL', normalRange: EDIT_LABELS.k_70_100_fasting_2, reference: 'GLU' },
                                { name: EDIT_LABELS.creatinine, unit: 'mg/dL', normalRange: '0.6-1.2 (F), 0.8-1.3 (M)', reference: 'CREA' },
                                { name: EDIT_LABELS.blood_urea_nitrogen_2, unit: 'mg/dL', normalRange: '7-20', reference: 'BUN' },
                                { name: 'Sodium', unit: 'mEq/L', normalRange: '135-145', reference: 'NA' },
                                { name: 'Potassium', unit: 'mEq/L', normalRange: '3.5-5.0', reference: 'K' },
                                { name: EDIT_LABELS.chloride, unit: 'mEq/L', normalRange: '96-106', reference: 'CL' },
                                { name: EDIT_LABELS.total_co2, unit: 'mEq/L', normalRange: '22-28', reference: 'CO2' },
                                { name: 'Calcium', unit: 'mg/dL', normalRange: '8.5-10.5', reference: 'CA' },
                                { name: EDIT_LABELS.phosphorus, unit: 'mg/dL', normalRange: '2.5-4.5', reference: 'PHOS' },
                                { name: EDIT_LABELS.magnesium, unit: 'mg/dL', normalRange: '1.5-2.5', reference: 'MG' },
                                { name: EDIT_LABELS.uric_acid, unit: 'mg/dL', normalRange: '2.4-6.0 (F), 3.4-7.0 (M)', reference: 'UA' },
                                { name: EDIT_LABELS.lactate_dehydrogenase, unit: 'U/L', normalRange: '140-280', reference: 'LDH' },
                                { name: EDIT_LABELS.creatine_phosphokinase, unit: 'U/L', normalRange: '30-200', reference: 'CPK' }
                            ],
                            '58408-6': [
                                // Include all tests from 58410-2
                                { name: EDIT_LABELS.hemoglobin, unit: 'g/dL', normalRange: '12.0-16.0 (F), 14.0-18.0 (M)', reference: 'HGB' },
                                { name: 'Glucose', unit: 'mg/dL', normalRange: EDIT_LABELS.k_70_100_fasting_2, reference: 'GLU' },
                                { name: EDIT_LABELS.creatinine, unit: 'mg/dL', normalRange: '0.6-1.2 (F), 0.8-1.3 (M)', reference: 'CREA' },
                                { name: EDIT_LABELS.total_cholesterol, unit: 'mg/dL', normalRange: '< 200', reference: 'CHOL' },
                                { name: 'HDL', unit: 'mg/dL', normalRange: '> 40 (M), > 50 (F)', reference: 'HDL' },
                                { name: 'LDL', unit: 'mg/dL', normalRange: '< 100 (optimal)', reference: 'LDL' },
                                { name: EDIT_LABELS.triglycerides, unit: 'mg/dL', normalRange: '< 150', reference: 'TRIG' },
                                { name: 'ALT', unit: 'U/L', normalRange: '7-55', reference: 'ALT' },
                                { name: 'AST', unit: 'U/L', normalRange: '8-48', reference: 'AST' },
                                { name: 'ALP', unit: 'U/L', normalRange: '44-147', reference: 'ALP' },
                                { name: EDIT_LABELS.total_bilirubin, unit: 'mg/dL', normalRange: '0.3-1.2', reference: 'TBIL' },
                                // Extended tests
                                { name: EDIT_LABELS.troponin, unit: 'ng/mL', normalRange: '< 0.04', reference: 'TROP' },
                                { name: 'CPK-MB', unit: 'ng/mL', normalRange: '< 5.0', reference: 'CPKMB' },
                                { name: 'BNP', unit: 'pg/mL', normalRange: '< 100', reference: 'BNP' },
                                { name: 'NT-proBNP', unit: 'pg/mL', normalRange: '< 125', reference: 'NTBNP' },
                                { name: 'CRP', unit: 'mg/L', normalRange: '< 3.0', reference: 'CRP' },
                                { name: EDIT_LABELS.high_sensitivity_crp, unit: 'mg/L', normalRange: '< 1.0', reference: 'HSCRP' },
                                { name: 'TSH', unit: 'μIU/mL', normalRange: '0.4-4.0', reference: 'TSH' },
                                { name: EDIT_LABELS.free_t4, unit: 'ng/dL', normalRange: '0.8-1.8', reference: 'T4' },
                                { name: EDIT_LABELS.free_t3, unit: 'pg/mL', normalRange: '2.3-4.2', reference: 'T3' },
                                { name: 'Cortisol', unit: 'μg/dL', normalRange: '6.2-19.4', reference: 'CORT' },
                                { name: EDIT_LABELS.insulin, unit: 'μIU/mL', normalRange: '3-25', reference: 'INSU' },
                                { name: 'HbA1c', unit: '%', normalRange: '4.0-5.6', reference: 'HBA1C' },
                                { name: EDIT_LABELS.vitamin_d, unit: 'ng/mL', normalRange: '30-100', reference: 'VITD' },
                                { name: EDIT_LABELS.testosterone, unit: 'ng/dL', normalRange: '300-1000', reference: 'TEST' },
                                { name: EDIT_LABELS.ferritin, unit: 'ng/mL', normalRange: '13-150 (F), 30-400 (M)', reference: 'FERR' },
                                { name: EDIT_LABELS.serum_iron, unit: 'μg/dL', normalRange: '60-170', reference: 'FE' },
                                { name: EDIT_LABELS.transferrin, unit: 'mg/dL', normalRange: '200-400', reference: 'TRANS' },
                                { name: 'PSA', unit: 'ng/mL', normalRange: '< 4.0', reference: 'PSA' },
                                { name: 'CEA', unit: 'ng/mL', normalRange: '< 3.0', reference: 'CEA' },
                                { name: 'AFP', unit: 'ng/mL', normalRange: '< 10.0', reference: 'AFP' }
                            ]
                        };
                        
                        const tests = detailedPanelTests[selectedValue] || [];
                        console.log('Tests for panel:', tests.length);
                        
                        tests.forEach((test, index) => {
                            console.log('Creating test card for:', test.name);
                            const testCard = createTestCard(test, index);
                            panelGrid.appendChild(testCard);
                        });
                    }
                    
                    // Update interpretation field with panel details
                    const interpretationField = document.getElementById('blood_test_interpretation');
                    if (interpretationField) {
                        interpretationField.value = `${panelConfig.description}\n\n${EDIT_LABELS.tests_included}\n${panelConfig.tests.join('\n')}`;
                    }
                }
            } else {
                // For general panels
                unitField.value = 'Variable';
                normalRangeField.value = EDIT_LABELS.depending_on_the_test;
                referenceField.value = EDIT_LABELS.depending_on_the_test;
                
                // Hide panel tests container
                const panelContainer = document.getElementById('panel-tests-container');
                if (panelContainer) {
                    panelContainer.style.display = 'none';
                }
            }
        } else {
            // Hide both result fields and panel container
            resultFields.style.display = 'none';
            const panelContainer = document.getElementById('panel-tests-container');
            if (panelContainer) {
                panelContainer.style.display = 'none';
            }
        }
    }

    // Function to create individual test cards
    function createTestCard(test, index) {
        const card = document.createElement('div');
        card.className = 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow';
        card.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <h5 class="text-sm font-semibold text-gray-800">${test.name}</h5>
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">${test.reference}</span>
            </div>
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">${EDIT_LABELS.measured_value}</label>
                        <input 
                            type="text" 
                            name="panel_test_${index}_value" 
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder=EDIT_LABELS.value
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">${EDIT_LABELS.unit}</label>
                        <input 
                            type="text" 
                            value="${test.unit}" 
                            readonly 
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-50"
                        >
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">${EDIT_LABELS.normal_range}</label>
                        <input 
                            type="text" 
                            value="${test.normalRange}" 
                            readonly 
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-50"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">${EDIT_LABELS.status}</label>
                        <select 
                            name="panel_test_${index}_status" 
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">${EDIT_LABELS.select_2}</option>
                            <option value="normal">Normal</option>
                            <option value="high">${EDIT_LABELS.high}</option>
                            <option value="low">${EDIT_LABELS.low_2}</option>
                            <option value="critical_high">${EDIT_LABELS.critically_high}</option>
                            <option value="critical_low">${EDIT_LABELS.critically_low_2}</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                    <textarea 
                        name="panel_test_${index}_notes" 
                        rows="2" 
                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                        placeholder=EDIT_LABELS.clinical_notes
                    ></textarea>
                </div>
            </div>
        `;
        return card;
    }

    // Global function for blood test control
    window.updateBloodTestResultFields = updateBloodTestResultFields;

    // AUT Fields Management
    function updateAUTFields() {
        const autSelect = document.getElementById('snomed_ct_aut');
        const autDetailsSection = document.getElementById('aut-details-section');
        
        console.log('updateAUTFields called');
        console.log('Selected AUT value:', autSelect.value);
        
        if (autSelect.value) {
            autDetailsSection.style.display = 'block';
            console.log('AUT details section shown');
        } else {
            autDetailsSection.style.display = 'none';
            console.log('AUT details section hidden');
        }
    }

    // Function to download IAAF Therapeutic Use Exemptions Application Form
    function downloadIAAFForm() {
        // Create a simple IAAF form template
        const formContent = `
IAAF THERAPEUTIC USE EXEMPTIONS APPLICATION FORM

SECTION 1: ATHLETE INFORMATION
Name: _________________________________
Date of Birth: _________________________
Nationality: ___________________________
Sport/Event: ___________________________
Federation: ____________________________

SECTION 2: MEDICAL INFORMATION
Diagnosis: ____________________________
Substance Requested: __________________
Dosage: ______________________________
Duration of Treatment: ________________

SECTION 3: MEDICAL JUSTIFICATION
Please provide detailed medical justification for the use of the prohibited substance:

_________________________________________________________________
_________________________________________________________________
_________________________________________________________________

SECTION 4: SUPPORTING DOCUMENTATION
- Medical reports
- Laboratory results
- Specialist consultations
- Previous treatment history

SECTION 5: DECLARATION
I declare that the information provided is accurate and complete.

Signature: ___________________________ Date: ________________
        `;
        
        // Create a blob and download
        const blob = new Blob([formContent], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'IAAF_Therapeutic_Use_Exemptions_Form.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        console.log('IAAF form download initiated');
    }

    } catch (error) {
        console.error('Error in first JavaScript section:', error);
    }

    // Global functions
    window.updateAUTFields = updateAUTFields;
    window.downloadIAAFForm = downloadIAAFForm;

    // Dental Chart Functionality
    let teethStatus = {};
    let dentalTooltip = {
        visible: false,
        x: 0,
        y: 0,
        toothNumber: '',
        status: '',
        condition: ''
    };

    // Initialize dental chart
    function initializeDentalChart() {
        // Initialize teeth status for all 32 teeth
        for (let i = 1; i <= 32; i++) {
            teethStatus[i] = {
                status: 'healthy',
                condition: 'Normal',
                surfaces: {
                    mesial: { condition: 'healthy', status: 'healthy' },
                    distal: { condition: 'healthy', status: 'healthy' },
                    occlusal: { condition: 'healthy', status: 'healthy' },
                    lingual: { condition: 'healthy', status: 'healthy' },
                    buccal: { condition: 'healthy', status: 'healthy' }
                },
                notes: ''
            };
        }
        
        // Load dental chart SVG
        loadDentalChart();
        updateDentalSummary();
    }

    // Load dental chart SVG
    function loadDentalChart() {
        console.log('Looking for dental chart container...');
        const chartContainer = document.getElementById('dental-svg-container');
        console.log('Chart container found:', !!chartContainer);
        
        if (!chartContainer) {
            console.error('Dental chart container not found');
            console.log('Available elements with dental in ID:', document.querySelectorAll('[id*="dental"]').length);
            return;
        }

        console.log('Loading dental chart SVG...');
        // Generate dental chart SVG
        const dentalChartSvg = generateDentalChartSvg();
        chartContainer.innerHTML = dentalChartSvg;
        console.log('Dental chart SVG loaded');
    }

    // Generate dental chart SVG
    function generateDentalChartSvg() {
        return `
            <svg width="800" height="400" viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <style>
                        .tooth { cursor: pointer; transition: fill 0.2s ease; }
                        .tooth-surface { cursor: pointer; transition: fill 0.2s ease; }
                        .status-healthy { fill: #ffffff; stroke: #cccccc; }
                        .status-caries { fill: #ff4d4d; }
                        .status-restoration { fill: #4d94ff; }
                        .status-crown { stroke: #4d94ff; stroke-width: 3px; fill-opacity: 0.1; }
                        .status-missing { fill: #808080; opacity: 0.5; }
                    </style>
                </defs>
                
                <!-- Upper teeth (1-16) -->
                <g id="upper-teeth">
                    ${generateTeethRow(1, 16, 50, 100, 'upper')}
                </g>
                
                <!-- Lower teeth (17-32) -->
                <g id="lower-teeth">
                    ${generateTeethRow(17, 32, 50, 300, 'lower')}
                </g>
                
                <!-- Labels -->
                <text x="400" y="30" text-anchor="middle" font-size="16" font-weight="bold">${EDIT_LABELS.upper_dentition}</text>
                <text x="400" y="370" text-anchor="middle" font-size="16" font-weight="bold">${EDIT_LABELS.lower_dentition}</text>
            </svg>
        `;
    }

    // Generate teeth row
    function generateTeethRow(start, end, y, baseY, position) {
        let svg = '';
        const teeth = end - start + 1;
        const spacing = 700 / teeth;
        
        for (let i = 0; i < teeth; i++) {
            const toothNumber = start + i;
            const x = 50 + (i * spacing);
            
            // Main tooth
            svg += `
                <g id="tooth-${toothNumber}" class="tooth">
                    <rect 
                        id="tooth-${toothNumber}-main"
                        x="${x}" y="${baseY}" 
                        width="40" height="60" 
                        rx="5" 
                        class="tooth-surface status-healthy"
                        data-tooth="${toothNumber}"
                    />
                    <text 
                        x="${x + 20}" y="${baseY + 35}" 
                        text-anchor="middle" 
                        font-size="12" 
                        font-weight="bold"
                    >${toothNumber}</text>
                </g>
            `;
        }
        
        return svg;
    }



    // Hide dental tooltip
    function hideDentalTooltip() {
        dentalTooltip.visible = false;
        hideDentalTooltipElement();
    }

    // Show dental tooltip
    function showDentalTooltip() {
        const tooltip = document.getElementById('dental-tooltip');
        if (tooltip && dentalTooltip.visible) {
            tooltip.style.left = dentalTooltip.x + 'px';
            tooltip.style.top = dentalTooltip.y + 'px';
            tooltip.style.display = 'block';
            
            const toothNumberElement = document.getElementById('tooltip-tooth-number');
            const statusElement = document.getElementById('tooltip-status');
            const conditionElement = document.getElementById('tooltip-condition');
            
            if (toothNumberElement) toothNumberElement.textContent = `${EDIT_LABELS.dental_tooth_word} ${dentalTooltip.toothNumber}`;
            if (statusElement) statusElement.textContent = dentalTooltip.status;
            if (conditionElement) conditionElement.textContent = dentalTooltip.condition;
        }
    }

    // Hide dental tooltip element
    function hideDentalTooltipElement() {
        const tooltip = document.getElementById('dental-tooltip');
        if (tooltip) {
            tooltip.style.display = 'none';
        }
    }

    // Cycle tooth status
    function cycleToothStatus(toothNumber) {
        const statuses = ['healthy', 'caries', 'restoration', 'missing'];
        const currentStatus = teethStatus[toothNumber].status;
        const currentIndex = statuses.indexOf(currentStatus);
        const nextIndex = (currentIndex + 1) % statuses.length;
        const newStatus = statuses[nextIndex];
        
        teethStatus[toothNumber].status = newStatus;
        teethStatus[toothNumber].condition = getDentalConditionLabel(newStatus);
    }

    // Get dental condition label
    function getDentalConditionLabel(status) {
        const labels = {
            'healthy': 'Normal',
            'caries': EDIT_LABELS.caries,
            'restoration': EDIT_LABELS.restoration,
            'missing': EDIT_LABELS.missing
        };
        return labels[status] || 'Normal';
    }

    // Update dental chart styles
    function updateDentalChartStyles() {
        Object.keys(teethStatus).forEach(toothNumber => {
            const tooth = teethStatus[toothNumber];
            const mainElement = document.getElementById(`tooth-${toothNumber}-main`);
            
            if (mainElement) {
                mainElement.className = `tooth-surface status-${tooth.status}`;
            }
        });
    }

    // Update dental summary
    function updateDentalSummary() {
        const healthyCount = Object.values(teethStatus).filter(tooth => tooth.status === 'healthy').length;
        const cariesCount = Object.values(teethStatus).filter(tooth => tooth.status === 'caries').length;
        const restorationCount = Object.values(teethStatus).filter(tooth => tooth.status === 'restoration').length;
        const missingCount = Object.values(teethStatus).filter(tooth => tooth.status === 'missing').length;

        const healthyElement = document.getElementById('healthy-teeth-count');
        const cariesElement = document.getElementById('caries-count');
        const restorationElement = document.getElementById('restoration-count');
        const missingElement = document.getElementById('missing-teeth-count');

        if (healthyElement) healthyElement.textContent = healthyCount;
        if (cariesElement) cariesElement.textContent = cariesCount;
        if (restorationElement) restorationElement.textContent = restorationCount;
        if (missingElement) missingElement.textContent = missingCount;
    }

    // Add dental chart event listeners
    function addDentalChartEventListeners() {
        console.log('Looking for dental chart container for event listeners...');
        const chartContainer = document.getElementById('dental-svg-container');
        console.log('Chart container found for event listeners:', !!chartContainer);
        
        if (!chartContainer) {
            console.error('Dental chart container not found for event listeners');
            console.log('Available elements with dental in ID:', document.querySelectorAll('[id*="dental"]').length);
            return;
        }

        // Add click event listener for tooth status cycling
        chartContainer.addEventListener('click', function(event) {
            const target = event.target;
            const toothId = target.getAttribute('data-tooth');
            
            if (toothId) {
                const toothNumber = parseInt(toothId);
                cycleToothStatus(toothNumber);
                updateDentalChartStyles();
                updateDentalSummary();
            }
        });

        // Add mouseover event listener for tooltips
        chartContainer.addEventListener('mouseover', function(event) {
            const target = event.target;
            const toothId = target.getAttribute('data-tooth');
            
            if (toothId) {
                const toothNumber = parseInt(toothId);
                const tooth = teethStatus[toothNumber];
                
                dentalTooltip = {
                    visible: true,
                    x: event.clientX + 10,
                    y: event.clientY - 10,
                    toothNumber: toothNumber,
                    status: tooth.status,
                    condition: tooth.condition
                };
                
                showDentalTooltip();
            }
        });

        // Add mouseleave event listener to hide tooltip
        chartContainer.addEventListener('mouseleave', function() {
            hideDentalTooltip();
        });
    }

    // Initialize dental chart when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing dental chart...');
        setTimeout(function() {
            console.log('Initializing dental chart with delay...');
            initializeDentalChart();
            addDentalChartEventListeners();
            console.log('Dental chart initialization complete');
        }, 500);
    });

    // Fallback initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Fallback DOM loaded, initializing dental chart...');
            setTimeout(function() {
                initializeDentalChart();
                addDentalChartEventListeners();
            }, 1000);
        });
    } else {
        console.log('Document already loaded, initializing dental chart immediately...');
        setTimeout(function() {
            initializeDentalChart();
            addDentalChartEventListeners();
        }, 1000);
    }

    // Global dental functions
    window.hideDentalTooltip = hideDentalTooltip;

    // =============================================================================
    // POSTURAL ASSESSMENT MODULE INTEGRATION
    // =============================================================================
    
    // Initialize postural assessment component
    function initializePosturalAssessment() {
        console.log('Initializing postural assessment component...');
        
        // Postural assessment state
        const posturalState = {
            currentView: 'anterior',
            currentTool: 'marker',
            selectedColor: '#ff0000',
            showPlumbLine: false,
            markers: [],
            angles: [],
            anglePoints: [],
            markerColors: ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff']
        };
        
        // Initialize UI elements
        const viewSelector = document.getElementById('postural-view-selector');
        const markerTool = document.getElementById('postural-marker-tool');
        const angleTool = document.getElementById('postural-angle-tool');
        const plumbTool = document.getElementById('postural-plumb-tool');
        const clearBtn = document.getElementById('postural-clear-btn');
        const exportBtn = document.getElementById('postural-export-btn');
        const colorPalette = document.getElementById('postural-color-palette');
        const svgContent = document.getElementById('postural-svg-content');
        const annotationsSvg = document.getElementById('postural-annotations');
        
        // Load initial SVG content
        loadSvgContent();
        
        // Event listeners
        if (viewSelector) {
            viewSelector.addEventListener('change', (e) => {
                posturalState.currentView = e.target.value;
                loadSvgContent();
                updatePosturalSummary();
            });
        }
        
        if (markerTool) {
            markerTool.addEventListener('click', () => {
                setCurrentTool('marker');
            });
        }
        
        if (angleTool) {
            angleTool.addEventListener('click', () => {
                setCurrentTool('angle');
            });
        }
        
        if (plumbTool) {
            plumbTool.addEventListener('click', () => {
                togglePlumbLine();
            });
        }
        
        if (clearBtn) {
            clearBtn.addEventListener('click', clearAnnotations);
        }
        
        if (exportBtn) {
            exportBtn.addEventListener('click', exportData);
        }
        
        // Color palette event listeners
        const colorButtons = colorPalette?.querySelectorAll('[data-color]');
        if (colorButtons) {
            colorButtons.forEach(button => {
                button.addEventListener('click', () => {
                    posturalState.selectedColor = button.dataset.color;
                    updateColorPalette();
                });
            });
        }
        
        // SVG click handler
        if (svgContent) {
            svgContent.addEventListener('click', handleCanvasClick);
        }
        
        // Functions
        function setCurrentTool(tool) {
            posturalState.currentTool = tool;
            
            // Update button styles
            [markerTool, angleTool, plumbTool].forEach(btn => {
                if (btn) btn.classList.remove('bg-blue-500', 'text-white');
                if (btn) btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            const activeButton = tool === 'marker' ? markerTool : 
                               tool === 'angle' ? angleTool : 
                               tool === 'plumb' ? plumbTool : null;
            
            if (activeButton) {
                activeButton.classList.remove('bg-gray-200', 'text-gray-700');
                activeButton.classList.add('bg-blue-500', 'text-white');
            }
            
            // Show/hide color palette
            if (colorPalette) {
                colorPalette.style.display = tool === 'marker' ? 'flex' : 'none';
            }
        }
        
        function togglePlumbLine() {
            posturalState.showPlumbLine = !posturalState.showPlumbLine;
            updateAnnotations();
            updatePosturalSummary();
        }
        
        function clearAnnotations() {
            posturalState.markers = [];
            posturalState.angles = [];
            posturalState.anglePoints = [];
            updateAnnotations();
            updatePosturalSummary();
            updateFormData();
        }
        
        function handleCanvasClick(event) {
            const rect = event.currentTarget.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;
            
            if (posturalState.currentTool === 'marker') {
                addMarker(x, y);
            } else if (posturalState.currentTool === 'angle') {
                addAnglePoint(x, y);
            }
        }
        
        function addMarker(x, y) {
            posturalState.markers.push({
                x: x,
                y: y,
                color: posturalState.selectedColor
            });
            updateAnnotations();
            updatePosturalSummary();
            updateFormData();
        }
        
        function addAnglePoint(x, y) {
            posturalState.anglePoints.push({ x, y });
            
            if (posturalState.anglePoints.length === 3) {
                calculateAngle();
                posturalState.anglePoints = [];
            }
        }
        
        function calculateAngle() {
            const [p1, p2, p3] = posturalState.anglePoints;
            
            // Calculate vectors
            const v1 = { x: p1.x - p2.x, y: p1.y - p2.y };
            const v2 = { x: p3.x - p2.x, y: p3.y - p2.y };
            
            // Calculate angle
            const dot = v1.x * v2.x + v1.y * v2.y;
            const mag1 = Math.sqrt(v1.x * v1.x + v1.y * v1.y);
            const mag2 = Math.sqrt(v2.x * v2.x + v2.y * v2.y);
            const angle = Math.acos(dot / (mag1 * mag2)) * (180 / Math.PI);
            
            posturalState.angles.push({
                points: [...posturalState.anglePoints],
                degrees: Math.round(angle)
            });
            
            updateAnnotations();
            updatePosturalSummary();
            updateFormData();
        }
        
        function updateAnnotations() {
            if (!annotationsSvg) return;
            
            // Clear existing annotations
            annotationsSvg.innerHTML = '';
            
            // Add markers
            posturalState.markers.forEach((marker, index) => {
                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('cx', marker.x);
                circle.setAttribute('cy', marker.y);
                circle.setAttribute('r', '8');
                circle.setAttribute('fill', marker.color);
                circle.setAttribute('stroke', '#000');
                circle.setAttribute('stroke-width', '2');
                circle.setAttribute('opacity', '0.8');
                annotationsSvg.appendChild(circle);
            });
            
            // Add angles
            posturalState.angles.forEach((angle, index) => {
                const [p1, p2, p3] = angle.points;
                
                // Lines
                const line1 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line1.setAttribute('x1', p1.x);
                line1.setAttribute('y1', p1.y);
                line1.setAttribute('x2', p2.x);
                line1.setAttribute('y2', p2.y);
                line1.setAttribute('stroke', '#000');
                line1.setAttribute('stroke-width', '2');
                annotationsSvg.appendChild(line1);
                
                const line2 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line2.setAttribute('x1', p2.x);
                line2.setAttribute('y1', p2.y);
                line2.setAttribute('x2', p3.x);
                line2.setAttribute('y2', p3.y);
                line2.setAttribute('stroke', '#000');
                line2.setAttribute('stroke-width', '2');
                annotationsSvg.appendChild(line2);
                
                // Angle text
                const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('x', p2.x);
                text.setAttribute('y', p2.y - 10);
                text.setAttribute('class', 'text-sm font-bold');
                text.setAttribute('fill', '#000');
                text.textContent = angle.degrees + '°';
                annotationsSvg.appendChild(text);
            });
            
            // Add plumb line
            if (posturalState.showPlumbLine) {
                const plumbLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                plumbLine.setAttribute('x1', '300');
                plumbLine.setAttribute('y1', '0');
                plumbLine.setAttribute('x2', '300');
                plumbLine.setAttribute('y2', '800');
                plumbLine.setAttribute('stroke', '#000');
                plumbLine.setAttribute('stroke-width', '2');
                plumbLine.setAttribute('stroke-dasharray', '5,5');
                annotationsSvg.appendChild(plumbLine);
            }
        }
        
        function updateColorPalette() {
            const colorButtons = colorPalette?.querySelectorAll('[data-color]');
            if (colorButtons) {
                colorButtons.forEach(button => {
                    if (button.dataset.color === posturalState.selectedColor) {
                        button.classList.remove('border-gray-300');
                        button.classList.add('border-gray-800');
                    } else {
                        button.classList.remove('border-gray-800');
                        button.classList.add('border-gray-300');
                    }
                });
            }
        }
        
        function updatePosturalSummary() {
            const markersCount = document.getElementById('postural-markers-count');
            const anglesCount = document.getElementById('postural-angles-count');
            
            if (markersCount) markersCount.textContent = posturalState.markers.length;
            if (anglesCount) anglesCount.textContent = posturalState.angles.length;
            
            updateMarkersList();
            updateAnglesList();
            updateExportData();
        }
        
        function updateMarkersList() {
            const markersList = document.getElementById('postural-markers-list');
            if (!markersList) return;
            
            markersList.innerHTML = '';
            posturalState.markers.forEach((marker, index) => {
                const markerDiv = document.createElement('div');
                markerDiv.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
                markerDiv.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: ${marker.color}"></div>
                        <span class="text-sm">Point ${index + 1}</span>
                    </div>
                    <button onclick="removeMarker(${index})" class="text-red-500 hover:text-red-700 text-sm">×</button>
                `;
                markersList.appendChild(markerDiv);
            });
        }
        
        function updateAnglesList() {
            const anglesList = document.getElementById('postural-angles-list');
            if (!anglesList) return;
            
            anglesList.innerHTML = '';
            posturalState.angles.forEach((angle, index) => {
                const angleDiv = document.createElement('div');
                angleDiv.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
                angleDiv.innerHTML = `
                    <span class="text-sm">${angle.degrees}°</span>
                    <button onclick="removeAngle(${index})" class="text-red-500 hover:text-red-700 text-sm">×</button>
                `;
                anglesList.appendChild(angleDiv);
            });
        }
        
        function updateExportData() {
            const exportData = document.getElementById('postural-export-data');
            if (!exportData) return;
            
            const data = {
                view: posturalState.currentView,
                markers: posturalState.markers,
                angles: posturalState.angles,
                plumbLine: posturalState.showPlumbLine
            };
            
            exportData.value = JSON.stringify(data, null, 2);
        }
        
        function updateFormData() {
            const hiddenField = document.getElementById('postural_assessment_data');
            if (hiddenField) {
                const formData = {
                    view: posturalState.currentView,
                    markers: posturalState.markers,
                    angles: posturalState.angles,
                    plumbLine: posturalState.showPlumbLine
                };
                hiddenField.value = JSON.stringify(formData);
            }
        }
        
        function loadSvgContent() {
            if (!svgContent) return;
            
            const content = {
                anterior: '<div class="text-center p-8 bg-white border-2 border-dashed border-gray-300 rounded-lg" style="width: 600px; height: 800px;"><div class="flex flex-col items-center justify-center h-full"><div class="text-6xl mb-4">👤</div><h3 class="text-xl font-semibold text-gray-700 mb-2">${EDIT_LABELS.anterior_view}</h3><p class="text-gray-500 text-sm">${EDIT_LABELS.click_on_the_image_to_add_markers}</p><p class="text-gray-500 text-sm mt-2">${EDIT_LABELS.use_the_tools_in_the_top_toolbar}</p></div></div>',
                posterior: '<div class="text-center p-8 bg-white border-2 border-dashed border-gray-300 rounded-lg" style="width: 600px; height: 800px;"><div class="flex flex-col items-center justify-center h-full"><div class="text-6xl mb-4">👤</div><h3 class="text-xl font-semibold text-gray-700 mb-2">${EDIT_LABELS.posterior_view}</h3><p class="text-gray-500 text-sm">${EDIT_LABELS.click_on_the_image_to_add_markers}</p><p class="text-gray-500 text-sm mt-2">${EDIT_LABELS.use_the_tools_in_the_top_toolbar}</p></div></div>',
                lateral: '<div class="text-center p-8 bg-white border-2 border-dashed border-gray-300 rounded-lg" style="width: 600px; height: 800px;"><div class="flex flex-col items-center justify-center h-full"><div class="text-6xl mb-4">👤</div><h3 class="text-xl font-semibold text-gray-700 mb-2">${EDIT_LABELS.lateral_view}</h3><p class="text-gray-500 text-sm">${EDIT_LABELS.click_on_the_image_to_add_markers}</p><p class="text-gray-500 text-sm mt-2">${EDIT_LABELS.use_the_tools_in_the_top_toolbar}</p></div></div>'
            };
            
            svgContent.innerHTML = content[posturalState.currentView] || '';
        }
        
        function exportData() {
            const data = {
                view: posturalState.currentView,
                markers: posturalState.markers,
                angles: posturalState.angles,
                plumbLine: posturalState.showPlumbLine
            };
            
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'postural-assessment-data.json';
            a.click();
            URL.revokeObjectURL(url);
        }
        
        // Global functions for remove buttons
        window.removeMarker = function(index) {
            posturalState.markers.splice(index, 1);
            updateAnnotations();
            updatePosturalSummary();
            updateFormData();
        };
        
        window.removeAngle = function(index) {
            posturalState.angles.splice(index, 1);
            updateAnnotations();
            updatePosturalSummary();
            updateFormData();
        };
        
        // Initialize
        setCurrentTool('marker');
        updatePosturalSummary();
        updateFormData();
    }

    // Initialize postural assessment when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing postural assessment...');
        setTimeout(function() {
            initializePosturalAssessment();
        }, 1000);
    });

    // Fallback initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                initializePosturalAssessment();
            }, 1500);
        });
    } else {
        setTimeout(function() {
            initializePosturalAssessment();
        }, 1500);
    }
});
</script>
@endsection 