@extends('layouts.app')

@section('title', 'Modifier PCMA - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Check if PCMA is signed -->
        @if($pcma->is_signed)
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-red-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <div>
                        <h2 class="text-xl font-semibold text-red-800">{{ __('pcma_extra.label_e775b01919c9') }}</h2>
                        <p class="text-red-700 mt-2">{{ __('pcma_extra.label_ca8b981d7b2b') }} <strong>{{ $pcma->signed_by }}</strong> le {{ \Carbon\Carbon::parse($pcma->signed_at)->format('d/m/Y H:i') }} et ne peut plus être modifié.</p>
                        <div class="mt-4 flex space-x-4">
                            <a href="{{ route('pcma.show', $pcma) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                {{ __('pcma_extra.label_54a3dea01ab8') }}
                            </a>
                            <a href="{{ route('pcma.pdf', $pcma) }}"
                               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                {{ __('pcma_extra.label_26d5eac2bb68') }}
                            </a>
                            <a href="{{ route('pcma.index') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                {{ __('health_records_edit.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ __('pcma_extra.label_60210220fed7') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('pcma_extra.label_fd521e60706f') }}</p>
            </div>

            <form id="pcma-edit-form" enctype="multipart/form-data" action="{{ route('pcma.update', $pcma) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_dd75b97c9a79') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.athlete_label') }}
                            </label>
                            <select id="player_id"
                                    name="player_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                <option value="">{{ __('pcma.select_athlete_placeholder') }}</option>
                                @foreach($athletes as $athlete)
                                    <option value="{{ $athlete->id }}" {{ old('player_id', $pcma->player_id) == $athlete->id ? 'selected' : '' }}>
                                        {{ $athlete->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="assessor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.assessor_label') }}
                            </label>
                            <select id="assessor_id" 
                                    name="assessor_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                <option value="">{{ __('pcma.select_assessor_placeholder') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assessor_id', $pcma->assessor_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="assessment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma_extra.label_80c4a7f4ea4f') }}
                            </label>
                            <input type="date" 
                                   id="assessment_date" 
                                   name="assessment_date" 
                                   value="{{ old('assessment_date', $pcma->result_json['assessment_date'] ?? $pcma->created_at->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                        </div>
                        
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.assessment_type_label') }}
                            </label>
                            <select id="type" 
                                    name="type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                <option value="">{{ __('pcma.select_type_placeholder') }}</option>
                                <option value="bpma" {{ old('type', $pcma->type) === 'bpma' ? 'selected' : '' }}>{{ __('pcma_workflow.type_pcma') }}</option>
                                <option value="cardio" {{ old('type', $pcma->type) === 'cardio' ? 'selected' : '' }}>{{ __('pcma.type_cardio') }}</option>
                                <option value="dental" {{ old('type', $pcma->type) === 'dental' ? 'selected' : '' }}>{{ __('pcma.type_dental') }}</option>
                                <option value="neurological" {{ old('type', $pcma->type) === 'neurological' ? 'selected' : '' }}>{{ __('pcma.type_neurological') }}</option>
                                <option value="orthopedic" {{ old('type', $pcma->type) === 'orthopedic' ? 'selected' : '' }}>{{ __('pcma.type_orthopedic') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Statut') }}
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="pending" {{ old('status', $pcma->status) === 'pending' ? 'selected' : '' }}>{{ __('pcma.status_pending') }}</option>
                                <option value="completed" {{ old('status', $pcma->status) === 'completed' ? 'selected' : '' }}>{{ __('clinical.status_completed') }}</option>
                                <option value="failed" {{ old('status', $pcma->status) === 'failed' ? 'selected' : '' }}>{{ __('pcma.status_failed') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Assessment -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_3a5cf044c6e6') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-6">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_create.general_notes_label') }}
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="{{ __('pcma_extra.label_31d8cb190653') }}">{{ old('notes', $pcma->notes) }}</textarea>
                        </div>
                        
                        <div>
                            <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.clinical_notes_label') }}
                            </label>
                            <textarea id="clinical_notes" 
                                      name="clinical_notes" 
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="{{ __('pcma_extra.label_2e7cab030921') }}">{{ old('clinical_notes', $pcma->result_json['clinical_notes'] ?? '') }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Tension Artérielle') }}
                                </label>
                                <input type="text" 
                                       id="blood_pressure" 
                                       name="blood_pressure" 
                                       value="{{ old('blood_pressure', $pcma->result_json['vital_signs']['blood_pressure'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="120/80">
                            </div>
                            
                            <div>
                                <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('pcma.heart_rate_label') }}
                                </label>
                                <input type="number" 
                                       id="heart_rate" 
                                       name="heart_rate" 
                                       value="{{ old('heart_rate', $pcma->result_json['vital_signs']['heart_rate'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="65">
                            </div>
                            
                            <div>
                                <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('pcma.temperature_label') }}
                                </label>
                                <input type="number" 
                                       id="temperature" 
                                       name="temperature" 
                                       value="{{ old('temperature', $pcma->result_json['vital_signs']['temperature'] ?? '') }}"
                                       step="0.1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="36.8">
                            </div>
                            
                            <div>
                                <label for="respiratory_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('pcma.respiratory_rate_label') }}
                                </label>
                                <input type="number" 
                                       id="respiratory_rate" 
                                       name="respiratory_rate" 
                                       value="{{ old('respiratory_rate', $pcma->result_json['vital_signs']['respiratory_rate'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="16">
                            </div>
                            
                            <div>
                                <label for="oxygen_saturation" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('pcma.oxygen_saturation_label') }}
                                </label>
                                <input type="number" 
                                       id="oxygen_saturation" 
                                       name="oxygen_saturation" 
                                       value="{{ old('oxygen_saturation', $pcma->result_json['vital_signs']['oxygen_saturation'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="98">
                            </div>
                            
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('health_records_edit.weight_kg') }}
                                </label>
                                <input type="number" 
                                       id="weight" 
                                       name="weight" 
                                       value="{{ old('weight', $pcma->result_json['vital_signs']['weight'] ?? '') }}"
                                       step="0.1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="75">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical History -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_3235555a712f') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-6">
                        <div>
                            @include('pcma.partials.icd11-history', ['section'=>'cardiovascular','textField'=>'cardiovascular_history','label'=>'pcma.cardiovascular_history_label'])
                        </div>
                        <div>
                            @include('pcma.partials.icd11-history', ['section'=>'surgical','textField'=>'surgical_history','label'=>'pcma.surgical_history_label'])
                        </div>
                        
                        <div>
                            @include('pcma.partials.medications')
                        </div>
                        
                        <div>
                            @include('pcma.partials.icd11-history', ['section'=>'allergies','textField'=>'allergies','label'=>'pcma.allergies_label'])
                        </div>
                    </div>
                </div>
            </div>

            <!-- Physical Examination -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_eb2c9ce1a3ff') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="general_appearance" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.general_appearance_label') }}
                            </label>
                            <select id="general_appearance" 
                                    name="general_appearance" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="normal" {{ old('general_appearance', $pcma->result_json['physical_examination']['general_appearance'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="abnormal" {{ old('general_appearance', $pcma->result_json['physical_examination']['general_appearance'] ?? '') === 'abnormal' ? 'selected' : '' }}>{{ __('pcma.abnormal_option') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="skin_examination" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.skin_exam_label') }}
                            </label>
                            <select id="skin_examination" 
                                    name="skin_examination" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="normal" {{ old('skin_examination', $pcma->result_json['physical_examination']['skin_examination'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="abnormal" {{ old('skin_examination', $pcma->result_json['physical_examination']['skin_examination'] ?? '') === 'abnormal' ? 'selected' : '' }}>{{ __('pcma.abnormal_option') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="lymph_nodes" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.lymph_nodes_label') }}
                            </label>
                            <select id="lymph_nodes" 
                                    name="lymph_nodes" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="normal" {{ old('lymph_nodes', $pcma->result_json['physical_examination']['lymph_nodes'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="enlarged" {{ old('lymph_nodes', $pcma->result_json['physical_examination']['lymph_nodes'] ?? '') === 'enlarged' ? 'selected' : '' }}>{{ __('pcma.lymph_enlarged_option') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="abdomen_examination" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.abdomen_exam_label') }}
                            </label>
                            <select id="abdomen_examination" 
                                    name="abdomen_examination" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="normal" {{ old('abdomen_examination', $pcma->result_json['physical_examination']['abdomen_examination'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="abnormal" {{ old('abdomen_examination', $pcma->result_json['physical_examination']['abdomen_examination'] ?? '') === 'abnormal' ? 'selected' : '' }}>{{ __('pcma.abnormal_option') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cardiovascular Assessment -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma.cardio_assessment_title') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cardiac_rhythm" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.cardiac_rhythm_label') }}
                            </label>
                            <select id="cardiac_rhythm" 
                                    name="cardiac_rhythm" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="sinus" {{ old('cardiac_rhythm', $pcma->result_json['cardiovascular_assessment']['cardiac_rhythm'] ?? '') === 'sinus' ? 'selected' : '' }}>Sinus</option>
                                <option value="irregular" {{ old('cardiac_rhythm', $pcma->result_json['cardiovascular_assessment']['cardiac_rhythm'] ?? '') === 'irregular' ? 'selected' : '' }}>Irregular</option>
                                <option value="arrhythmia" {{ old('cardiac_rhythm', $pcma->result_json['cardiovascular_assessment']['cardiac_rhythm'] ?? '') === 'arrhythmia' ? 'selected' : '' }}>{{ __('pcma_extra.label_0e80ec38fa79') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="heart_murmur" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.heart_murmur_label') }}
                            </label>
                            <select id="heart_murmur" 
                                    name="heart_murmur" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="none" {{ old('heart_murmur', $pcma->result_json['cardiovascular_assessment']['heart_murmur'] ?? '') === 'none' ? 'selected' : '' }}>{{ __('pcma.report_none') }}</option>
                                <option value="systolic" {{ old('heart_murmur', $pcma->result_json['cardiovascular_assessment']['heart_murmur'] ?? '') === 'systolic' ? 'selected' : '' }}>{{ __('pcma.murmur_systolic_option') }}</option>
                                <option value="diastolic" {{ old('heart_murmur', $pcma->result_json['cardiovascular_assessment']['heart_murmur'] ?? '') === 'diastolic' ? 'selected' : '' }}>{{ __('pcma.murmur_diastolic_option') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="blood_pressure_rest" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.bp_rest_label') }}
                            </label>
                            <input type="text" 
                                   id="blood_pressure_rest" 
                                   name="blood_pressure_rest" 
                                   value="{{ old('blood_pressure_rest', $pcma->result_json['cardiovascular_assessment']['blood_pressure_rest'] ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="120/80 mmHg">
                        </div>
                        
                        <div>
                            <label for="blood_pressure_exercise" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.bp_exercise_label') }}
                            </label>
                            <input type="text" 
                                   id="blood_pressure_exercise" 
                                   name="blood_pressure_exercise" 
                                   value="{{ old('blood_pressure_exercise', $pcma->result_json['cardiovascular_assessment']['blood_pressure_exercise'] ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="140/85 mmHg">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Neurological Assessment -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma.neuro_assessment_title') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="consciousness" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.consciousness_label') }}
                            </label>
                            <select id="consciousness" 
                                    name="consciousness" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="alert" {{ old('consciousness', $pcma->result_json['neurological_assessment']['consciousness'] ?? '') === 'alert' ? 'selected' : '' }}>{{ __('pcma_extra.label_672e2f601f7a') }}</option>
                                <option value="confused" {{ old('consciousness', $pcma->result_json['neurological_assessment']['consciousness'] ?? '') === 'confused' ? 'selected' : '' }}>{{ __('pcma.confused_state_option') }}</option>
                                <option value="drowsy" {{ old('consciousness', $pcma->result_json['neurological_assessment']['consciousness'] ?? '') === 'drowsy' ? 'selected' : '' }}>{{ __('pcma.drowsy_state_option') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="cranial_nerves" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.cranial_nerves_label') }}
                            </label>
                            <select id="cranial_nerves" 
                                    name="cranial_nerves" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="normal" {{ old('cranial_nerves', $pcma->result_json['neurological_assessment']['cranial_nerves'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="abnormal" {{ old('cranial_nerves', $pcma->result_json['neurological_assessment']['cranial_nerves'] ?? '') === 'abnormal' ? 'selected' : '' }}>{{ __('pcma.abnormal_option') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="motor_function" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.motor_function_label') }}
                            </label>
                            <select id="motor_function" 
                                    name="motor_function" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="normal" {{ old('motor_function', $pcma->result_json['neurological_assessment']['motor_function'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="weakness" {{ old('motor_function', $pcma->result_json['neurological_assessment']['motor_function'] ?? '') === 'weakness' ? 'selected' : '' }}>{{ __('pcma.weakness_option') }}</option>
                                <option value="paralysis" {{ old('motor_function', $pcma->result_json['neurological_assessment']['motor_function'] ?? '') === 'paralysis' ? 'selected' : '' }}>{{ __('pcma.paralysis_option') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="sensory_function" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.sensory_function_label') }}
                            </label>
                            <select id="sensory_function" 
                                    name="sensory_function" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="normal" {{ old('sensory_function', $pcma->result_json['neurological_assessment']['sensory_function'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="decreased" {{ old('sensory_function', $pcma->result_json['neurological_assessment']['sensory_function'] ?? '') === 'decreased' ? 'selected' : '' }}>{{ __('pcma.sensory_decreased_option') }}</option>
                                <option value="absent" {{ old('sensory_function', $pcma->result_json['neurological_assessment']['sensory_function'] ?? '') === 'absent' ? 'selected' : '' }}>{{ __('pcma.sensory_absent_option') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Musculoskeletal Assessment -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma.msk_assessment_title') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="joint_mobility" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.joint_mobility_label') }}
                            </label>
                            <select id="joint_mobility" 
                                    name="joint_mobility" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="normal" {{ old('joint_mobility', $pcma->result_json['musculoskeletal_assessment']['joint_mobility'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="limited" {{ old('joint_mobility', $pcma->result_json['musculoskeletal_assessment']['joint_mobility'] ?? '') === 'limited' ? 'selected' : '' }}>{{ __('pcma.limited_option') }}</option>
                                <option value="restricted" {{ old('joint_mobility', $pcma->result_json['musculoskeletal_assessment']['joint_mobility'] ?? '') === 'restricted' ? 'selected' : '' }}>{{ __('pcma.restricted_option') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="muscle_strength" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.muscle_strength_label') }}
                            </label>
                            <select id="muscle_strength" 
                                    name="muscle_strength" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="normal" {{ old('muscle_strength', $pcma->result_json['musculoskeletal_assessment']['muscle_strength'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="reduced" {{ old('muscle_strength', $pcma->result_json['musculoskeletal_assessment']['muscle_strength'] ?? '') === 'reduced' ? 'selected' : '' }}>{{ __('pcma.reduced_option') }}</option>
                                <option value="weak" {{ old('muscle_strength', $pcma->result_json['musculoskeletal_assessment']['muscle_strength'] ?? '') === 'weak' ? 'selected' : '' }}>{{ __('pcma.weak_option') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="pain_assessment" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.pain_assessment_label') }}
                            </label>
                            <select id="pain_assessment" 
                                    name="pain_assessment" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="none" {{ old('pain_assessment', $pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'none' ? 'selected' : '' }}>{{ __('pcma.report_none_f') }}</option>
                                <option value="mild" {{ old('pain_assessment', $pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'mild' ? 'selected' : '' }}>{{ __('pcma.pain_mild_option') }}</option>
                                <option value="moderate" {{ old('pain_assessment', $pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'moderate' ? 'selected' : '' }}>{{ __('pcma.pain_moderate_option') }}</option>
                                <option value="severe" {{ old('pain_assessment', $pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'severe' ? 'selected' : '' }}>{{ __('pcma.pain_severe_option') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="range_of_motion" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma.rom_label') }}
                            </label>
                            <select id="range_of_motion" 
                                    name="range_of_motion" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">{{ __('pcma.select_placeholder') }}</option>
                                <option value="full" {{ old('range_of_motion', $pcma->result_json['musculoskeletal_assessment']['range_of_motion'] ?? '') === 'full' ? 'selected' : '' }}>{{ __('pcma.rom_full_option') }}</option>
                                <option value="limited" {{ old('range_of_motion', $pcma->result_json['musculoskeletal_assessment']['range_of_motion'] ?? '') === 'limited' ? 'selected' : '' }}>{{ __('pcma.limited_option') }}</option>
                                <option value="restricted" {{ old('range_of_motion', $pcma->result_json['musculoskeletal_assessment']['range_of_motion'] ?? '') === 'restricted' ? 'selected' : '' }}>{{ __('pcma.restricted_option') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FIFA Compliance -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_a9a348ecaaf6') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="fifa_compliant" class="flex items-center">
                                <input type="checkbox" 
                                       id="fifa_compliant" 
                                       name="fifa_compliant" 
                                       value="1"
                                       {{ ($pcma->fifa_compliant ?? false) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('pcma.fifa_compliant_label') }}</span>
                            </label>
                        </div>
                        
                        <div>
                            <label for="fifa_connect_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma_extra.label_147df95d5d3a') }}
                            </label>
                            <input type="text" 
                                   id="fifa_connect_id" 
                                   name="fifa_connect_id" 
                                   value="{{ old('fifa_connect_id', $pcma->result_json['fifa_connect_id'] ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="FIFA-123456">
                        </div>
                        
                        <div>
                            <label for="competition_name" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('competitions.competition') }}
                            </label>
                            <input type="text" 
                                   id="competition_name" 
                                   name="competition_name" 
                                   value="{{ $pcma->competition_name ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Championnat National">
                        </div>
                        
                        <div>
                            <label for="assessment_type" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma_extra.label_50b2ef0312a1') }}
                            </label>
                            <select id="assessment_type" 
                                    name="assessment_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="initial" {{ ($pcma->assessment_type ?? '') === 'initial' ? 'selected' : '' }}>{{ __('pcma_extra.label_6e2ba30f2907') }}</option>
                                <option value="renewal" {{ ($pcma->assessment_type ?? '') === 'renewal' ? 'selected' : '' }}>{{ __('pcma_extra.label_5b961f3ab582') }}</option>
                                <option value="emergency" {{ ($pcma->assessment_type ?? '') === 'emergency' ? 'selected' : '' }}>{{ __('clinical.type_emergency') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">{{ __('pcma_extra.label_f6a5c9db29a3') }}</h2>
                </div>
                
                <div class="p-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_create.general_notes_label') }}
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="{{ __('pcma_extra.label_31d8cb190653') }}">{{ $pcma->notes ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('pcma.show', $pcma) }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    {{ __('pcma_extra.label_f2aa09dfb172') }}
                </a>
                
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        {{ __('health_records_create.save_button') }}
                    </button>
                    
                    <a href="{{ route('pcma.pdf', $pcma) }}"
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        {{ __('pcma_extra.label_0eb5fff4ce15') }}
                    </a>
                </div>
            </div>
            </form>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.querySelector('#pcma-edit-form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert(@json(__('Veuillez remplir tous les champs obligatoires.')));
        }
    });
    
    // Real-time validation
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
            }
        });
    });
});
</script>
@endsection 