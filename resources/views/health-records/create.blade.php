@extends("layouts.app")

@section("title", __('health_records_create.page_title'))

@push("scripts")
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="{{ asset('js/image-viewer.js') }}"></script>
@endpush

<style>
.tabs-nav {
    display: flex;
    border-bottom: 2px solid #e5e7eb;
    background: white;
    border-radius: 8px 8px 0 0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.tab-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 16px 24px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
    color: #6b7280;
}

.tab-button:hover {
    color: #374151;
    background: #f9fafb;
}

.tab-button.active {
    color: #2563eb;
    border-bottom: 2px solid #2563eb;
    background: #eff6ff;
    font-weight: 600;
}

.tab-icon {
    font-size: 18px;
}

.tab-label {
    font-size: 14px;
}

.tab-content {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.tab-panel {
    padding: 24px;
}

/* Styles pour le diagramme dentaire intégré */
.dental-chart-container {
    position: relative;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    width: 100%;
    height: 400px; /* Taille adaptée pour l'intégration */
    cursor: default;
    margin-bottom: 20px;
}

.dental-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    opacity: 0.9;
    pointer-events: none;
    max-height: 100%;
}

.tooth-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.tooth-zone {
    position: absolute;
    width: 30px; /* Taille adaptée pour l'intégration */
    height: 18px; /* Taille adaptée pour l'intégration */
    border: 2px solid #3b82f6;
    background: rgba(59, 130, 246, 0.3);
    cursor: grab;
    pointer-events: all;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px; /* Taille adaptée pour l'intégration */
    font-weight: bold;
    color: #1f2937;
    border-radius: 4px;
    user-select: none;
    z-index: 10;
}

.tooth-zone:hover {
    background: rgba(59, 130, 246, 0.5);
    border-color: #1d4ed8;
    transform: scale(1.1);
    z-index: 20;
}

.tooth-zone.selected {
    background: rgba(59, 130, 246, 0.7);
    border-color: #1d4ed8;
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.8);
    z-index: 30;
}

.tooth-zone.dragging {
    opacity: 0.9;
    z-index: 100;
    cursor: grabbing !important;
    transform: scale(1.2);
    box-shadow: 0 6px 20px rgba(0,0,0,0.4);
    border-color: #f59e0b;
    background: rgba(245, 158, 11, 0.4);
}

.tooth-zone.fixed {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.4);
    box-shadow: 0 0 6px rgba(16, 185, 129, 0.6);
}

/* Responsive pour le diagramme intégré */
@media (max-width: 1024px) {
    .dental-chart-container {
        height: 300px;
    }
    
    .tooth-zone {
        width: 25px;
        height: 15px;
        font-size: 8px;
    }
}

@media (max-width: 768px) {
    .tabs-nav {
        flex-wrap: wrap;
    }
    
    .tab-button {
        padding: 12px 16px;
        font-size: 12px;
    }
    
    .tab-icon {
        font-size: 16px;
    }
    
    .tab-label {
        display: none;
    }
    
    .dental-chart-container {
        height: 250px;
    }
    
    .tooth-zone {
        width: 20px;
        height: 12px;
        font-size: 7px;
    }
}
</style>

@section("content")
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('health_records_create.heading') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('health_records_create.subtitle') }}</p>
        </div>

        <form action="{{ route("health-records.store") }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Interface à Onglets -->
            <div id="health-records-tabs" class="bg-white rounded-lg shadow-lg">
                <!-- Barre de navigation des onglets -->
                <div class="tabs-nav">
                    <button type="button"
                        v-for="tab in tabs" 
                        :key="tab.id"
                        @click="activeTab = tab.id" 
                        :class="{ 
                            'active': activeTab === tab.id,
                            'tab-button': true
                        }"
                        class="tab-button"
                    >
                        <span class="tab-icon">@{{ tab.icon }}</span>
                        <span class="tab-label">@{{ tab.label }}</span>
                    </button>
                </div>

                <!-- Contenu des onglets -->
                <div class="tab-content">
                    <!-- Onglet 1: Informations Générales -->
                    <div v-show="activeTab === 'general'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('health_records_create.patient_info_heading') }}</h3>
                                
                                <!-- Player Selection -->
                                <div class="mb-6">
                                    <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('health_records_create.player_label') }}
                                    </label>
                                    <select 
                                        id="player_id" 
                                        name="player_id" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">{{ __('health_records_create.select_player_placeholder') }}</option>
                                        @foreach($players ?? [] as $player)
                                            <option value="{{ $player->id }}" 
                                                {{ old('player_id') == $player->id || ($selectedPlayer && $selectedPlayer->id == $player->id) || (($defaultValues['player_id'] ?? null) == $player->id) ? 'selected' : '' }}>
                                                {{ $player->full_name ?? $player->name }} ({{ $player->club->name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                        @if($selectedPlayer && isset($isDemo) && $isDemo)
                                            <option value="{{ $selectedPlayer->id }}" selected>
                                                {{ $selectedPlayer->full_name ?? $selectedPlayer->name }} ({{ $selectedPlayer->club['name'] ?? 'N/A' }}) - {{ __('health_records_create.demo_mode_badge') }}
                                            </option>
                                        @endif
                                    </select>
                                </div>

                                <!-- Player Information Display -->
                                @if($selectedPlayer)
                                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-blue-900 mb-2">
                                            {{ __('health_records_create.selected_patient_info_heading') }}
                                            @if(isset($isDemo) && $isDemo)
                                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded ml-2">{{ __('health_records_create.demo_mode_badge') }}</span>
                                            @endif
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium text-blue-800">{{ __('health_records_create.full_name_label') }}</span>
                                                <span class="text-blue-900">{{ $selectedPlayer->full_name ?? $selectedPlayer->name }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">{{ __('health_records_create.birth_date_label') }}</span>
                                                <span class="text-blue-900">
                                                    @if($selectedPlayer->date_of_birth)
                                                        {{ \Carbon\Carbon::parse($selectedPlayer->date_of_birth)->format('d/m/Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">{{ __('health_records_create.club_label') }}</span>
                                                <span class="text-blue-900">
                                                    @if(isset($isDemo) && $isDemo)
                                                        {{ $selectedPlayer->club['name'] ?? 'N/A' }}
                                                    @else
                                                        {{ $selectedPlayer->club->name ?? 'N/A' }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">{{ __('health_records_create.position_label') }}</span>
                                                <span class="text-blue-900">{{ $selectedPlayer->position ?? 'N/A' }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">{{ __('health_records_create.age_label') }}</span>
                                                <span class="text-blue-900">{{ $selectedPlayer->age ?? 'N/A' }} {{ __('health_records_create.age_suffix') }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">{{ __('health_records_create.nationality_label') }}</span>
                                                <span class="text-blue-900">{{ $selectedPlayer->nationality ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @elseif(!empty($defaultValues['patient_name']) || !empty($defaultValues['patient_birth_date']) || !empty($defaultValues['visit_type']))
                                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-blue-900 mb-2">{{ __('health_records_create.prefilled_patient_info_heading') }}</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium text-blue-800">{{ __('health_records_create.full_name_label') }}</span>
                                                <span class="text-blue-900">{{ $defaultValues['patient_name'] ?? 'N/A' }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">{{ __('health_records_create.birth_date_label') }}</span>
                                                <span class="text-blue-900">
                                                    @if(!empty($defaultValues['patient_birth_date']))
                                                        {{ \Carbon\Carbon::parse($defaultValues['patient_birth_date'])->format('d/m/Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">{{ __('health_records_create.visit_type_display_label') }}</span>
                                                <span class="text-blue-900">{{ $defaultValues['visit_type'] ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <p class="text-xs text-blue-700 mt-2">{{ __('health_records_create.prefill_hint') }}</p>
                                    </div>
                                @endif

                                <!-- Visit Information -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="visit_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.visit_date_label') }}
                                        </label>
                                        <input 
                                            type="date" 
                                            id="visit_date" 
                                            name="visit_date" 
                                            value="{{ old('visit_date', $defaultValues['visit_date'] ?? date('Y-m-d')) }}"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="doctor_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.doctor_label') }}
                                        </label>
                                        <input 
                                            type="text" 
                                            id="doctor_name" 
                                            name="doctor_name" 
                                            value="{{ old('doctor_name', auth()->user()->name ?? '') }}"
                                            required
                                            placeholder="{{ __('health_records_create.doctor_placeholder') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="visit_type" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.visit_type_label') }}
                                        </label>
                                        <select
                                            id="visit_type"
                                            name="visit_type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            required
                                        >
                                            <option value="">{{ __('health_records_create.select_visit_type_placeholder') }}</option>
                                            <option value="consultation" {{ (old('visit_type') == 'consultation' || (isset($defaultValues['visit_type']) && $defaultValues['visit_type'] == 'consultation')) ? 'selected' : '' }}>{{ __('health_records_create.visit_type_consultation') }}</option>
                                            <option value="emergency" {{ (old('visit_type') == 'emergency' || (isset($defaultValues['visit_type']) && $defaultValues['visit_type'] == 'emergency')) ? 'selected' : '' }}>{{ __('health_records_create.visit_type_emergency') }}</option>
                                            <option value="follow_up" {{ (old('visit_type') == 'follow_up' || (isset($defaultValues['visit_type']) && $defaultValues['visit_type'] == 'follow_up')) ? 'selected' : '' }}>{{ __('health_records_create.visit_type_follow_up') }}</option>
                                            <option value="examination" {{ (old('visit_type') == 'examination' || (isset($defaultValues['visit_type']) && $defaultValues['visit_type'] == 'examination')) ? 'selected' : '' }}>{{ __('health_records_create.visit_type_examination') }}</option>
                                            <option value="pre_season" {{ (old('visit_type') == 'pre_season' || (isset($defaultValues['visit_type']) && $defaultValues['visit_type'] == 'pre_season')) ? 'selected' : '' }}>{{ __('health_records_create.visit_type_pre_season') }}</option>
                                            <option value="pcma" {{ (old('visit_type') == 'pcma' || (isset($defaultValues['visit_type']) && $defaultValues['visit_type'] == 'pcma')) ? 'selected' : '' }}>{{ __('health_records_create.visit_type_pcma') }}</option>
                                            <option value="post_match" {{ (old('visit_type') == 'post_match' || (isset($defaultValues['visit_type']) && $defaultValues['visit_type'] == 'post_match')) ? 'selected' : '' }}>{{ __('health_records_create.visit_type_post_match') }}</option>
                                            <option value="rehabilitation" {{ (old('visit_type') == 'rehabilitation' || (isset($defaultValues['visit_type']) && $defaultValues['visit_type'] == 'rehabilitation')) ? 'selected' : '' }}>{{ __('health_records_create.visit_type_rehabilitation') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Chief Complaint -->
                                <div class="mt-6">
                                    <label for="chief_complaint" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('health_records_create.chief_complaint_label') }}
                                    </label>
                                    <textarea 
                                        id="chief_complaint" 
                                        name="chief_complaint" 
                                        rows="3"
                                        placeholder="{{ __('health_records_create.chief_complaint_placeholder') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >{{ old('chief_complaint') }}</textarea>
                                </div>
                            </div>

                            <!-- Vital Signs -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('health_records_create.vital_signs_heading') }}</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="blood_pressure_systolic" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.systolic_label') }}
                                        </label>
                                        <input 
                                            type="number" 
                                            id="blood_pressure_systolic" 
                                            name="blood_pressure_systolic" 
                                            value="{{ old('blood_pressure_systolic') }}"
                                            min="70" max="200"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="blood_pressure_diastolic" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.diastolic_label') }}
                                        </label>
                                        <input 
                                            type="number" 
                                            id="blood_pressure_diastolic" 
                                            name="blood_pressure_diastolic" 
                                            value="{{ old('blood_pressure_diastolic') }}"
                                            min="40" max="130"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.heart_rate_label') }}
                                        </label>
                                        <input 
                                            type="number" 
                                            id="heart_rate" 
                                            name="heart_rate" 
                                            value="{{ old('heart_rate') }}"
                                            min="40" max="200"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 2: Assistant IA -->
                    <div v-show="activeTab === 'ai-assistant'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-purple-900 mb-4">{{ __('health_records_create.ai_assistant_heading') }}</h3>
                                <p class="text-purple-700 mb-4">{{ __('health_records_create.ai_assistant_subtitle') }}</p>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.clinical_notes_label') }}
                                        </label>
                                        <textarea 
                                            id="clinical_notes" 
                                            name="clinical_notes" 
                                            rows="6" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                            placeholder="{{ __('health_records_create.clinical_notes_placeholder') }}"
                                        >{{ old('clinical_notes') }}</textarea>
                                    </div>
                                    
                                    <div class="flex space-x-4">
                                        <button 
                                            type="button" 
                                            id="ai-analyze-btn"
                                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center"
                                        >
                                            <span class="mr-2">🔍</span>
                                            {{ __('health_records_create.analyze_ai_button') }}
                                        </button>
                                        <button 
                                            type="button" 
                                            id="clear-notes-btn"
                                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                                        >
                                            {{ __('health_records_create.clear_button') }}
                                        </button>
                                    </div>
                                    
                                    <div id="ai-results" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">{{ __('health_records_create.ai_results_heading') }}</h4>
                                        <div id="ai-content" class="text-sm text-gray-700"></div>
                                    </div>
                                </div>
                            </div>

                            @include('health-records.icd11')
                            @include('health-records.medications')
                        </div>
                    </div>

                    <!-- Onglet 3: Catégories Médicales -->
                    <div v-show="activeTab === 'medical-categories'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('health_records_create.medical_categories_heading') }}</h3>
                                <p class="text-green-700 mb-4">{{ __('health_records_create.medical_categories_subtitle') }}</p>
                                
                                <!-- ICD-10 Diagnoses -->
                                <div class="space-y-4">
                                    <div>
                                        <label for="icd10_diagnoses" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.icd10_diagnoses_label') }}
                                        </label>
                                        <select id="icd10_diagnoses" name="icd10_diagnoses[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" size="8">
                                            <optgroup label="{{ __('health_records_create.icd10_group_cardio') }}">
                                                <option value="I10">{{ __('health_records_create.icd10_i10') }}</option>
                                                <option value="I21.9">{{ __('health_records_create.icd10_i21_9') }}</option>
                                                <option value="I50.9">{{ __('health_records_create.icd10_i50_9') }}</option>
                                                <option value="I63.9">{{ __('health_records_create.icd10_i63_9') }}</option>
                                            </optgroup>
                                            <optgroup label="{{ __('health_records_create.icd10_group_resp') }}">
                                                <option value="J44.9">{{ __('health_records_create.icd10_j44_9') }}</option>
                                                <option value="J45.9">{{ __('health_records_create.icd10_j45_9') }}</option>
                                                <option value="J18.9">{{ __('health_records_create.icd10_j18_9') }}</option>
                                            </optgroup>
                                            <optgroup label="{{ __('health_records_create.icd10_group_musculo') }}">
                                                <option value="M79.3">{{ __('health_records_create.icd10_m79_3') }}</option>
                                                <option value="M54.5">{{ __('health_records_create.icd10_m54_5') }}</option>
                                                <option value="S93.4">{{ __('health_records_create.icd10_s93_4') }}</option>
                                            </optgroup>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">{{ __('health_records_create.icd10_multi_select_hint') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 4: Dossier Dentaire -->
                    <div v-show="activeTab === 'dental-record'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-yellow-900 mb-4">{{ __('health_records_create.dental_record_heading') }}</h3>
                                <p class="text-yellow-700 mb-4">{{ __('health_records_create.dental_record_subtitle') }}</p>
                                

                                
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-gray-800">{{ __('health_records_create.patient_info_subheading') }}</h4>
                                    
                                    <!-- Player Information Display for Dental Tab -->
                                    @if($selectedPlayer)
                                        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-yellow-900 mb-2">
                                                {{ __('health_records_create.selected_patient_info_heading') }}
                                                @if(isset($isDemo) && $isDemo)
                                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded ml-2">{{ __('health_records_create.demo_mode_badge') }}</span>
                                                @endif
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.full_name_label') }}</span>
                                                    <span class="text-yellow-900">{{ $selectedPlayer->full_name ?? $selectedPlayer->name }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.birth_date_label') }}</span>
                                                    <span class="text-yellow-900">
                                                        @if($selectedPlayer->date_of_birth)
                                                            {{ \Carbon\Carbon::parse($selectedPlayer->date_of_birth)->format('d/m/Y') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.club_label') }}</span>
                                                    <span class="text-yellow-900">
                                                        @if(isset($isDemo) && $isDemo)
                                                            {{ $selectedPlayer->club['name'] ?? 'N/A' }}
                                                        @else
                                                            {{ $selectedPlayer->club->name ?? 'N/A' }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.position_label') }}</span>
                                                    <span class="text-yellow-900">{{ $selectedPlayer->position ?? 'N/A' }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.age_label') }}</span>
                                                    <span class="text-yellow-900">{{ $selectedPlayer->age ?? 'N/A' }} {{ __('health_records_create.age_suffix') }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.nationality_label') }}</span>
                                                    <span class="text-yellow-900">{{ $selectedPlayer->nationality ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div id="dental-player-info" class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4" style="display: none;">
                                            <h4 class="text-sm font-semibold text-yellow-900 mb-2">{{ __('health_records_create.selected_patient_info_heading') }}</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.full_name_label') }}</span>
                                                    <span id="dental-player-full-name" class="text-yellow-900"></span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.birth_date_label') }}</span>
                                                    <span id="dental-player-birthdate" class="text-yellow-900"></span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.club_label') }}</span>
                                                    <span id="dental-player-club" class="text-yellow-900"></span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.position_label') }}</span>
                                                    <span id="dental-player-position" class="text-yellow-900"></span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.age_label') }}</span>
                                                    <span id="dental-player-age" class="text-yellow-900"></span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">{{ __('health_records_create.nationality_label') }}</span>
                                                    <span id="dental-player-nationality" class="text-yellow-900"></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-6">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_create.dental_appointments_heading') }}</h4>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="dental_appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('health_records_create.appointment_date_label') }}
                                                </label>
                                                <input 
                                                    type="date" 
                                                    id="dental_appointment_date" 
                                                    name="dental_appointment_date"
                                                    value="{{ old('dental_appointment_date') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                                >
                                            </div>
                                            
                                            <div>
                                                <label for="dental_appointment_time" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('health_records_create.appointment_time_label') }}
                                                </label>
                                                <input 
                                                    type="time" 
                                                    id="dental_appointment_time" 
                                                    name="dental_appointment_time"
                                                    value="{{ old('dental_appointment_time') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                                >
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="dental_appointment_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.appointment_reason_label') }}
                                            </label>
                                            <textarea 
                                                id="dental_appointment_reason" 
                                                name="dental_appointment_reason"
                                                rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                            >{{ old('dental_appointment_reason') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_create.dental_notes_heading') }}</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label for="dental_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.general_notes_label') }}
                                            </label>
                                            <textarea 
                                                id="dental_notes" 
                                                name="dental_notes"
                                                rows="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                            >{{ old('dental_notes') }}</textarea>
                                        </div>
                                        
                                        <div>
                                            <label for="dental_treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.treatment_plan_label') }}
                                            </label>
                                            <textarea 
                                                id="dental_treatment_plan" 
                                                name="dental_treatment_plan"
                                                rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                            >{{ old('dental_treatment_plan') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Diagramme Dentaire Interactif -->
                                <div class="mt-6">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_create.interactive_chart_heading') }}</h4>
                                    <p class="text-sm text-gray-600 mb-4">{{ __('health_records_create.interactive_chart_subtitle') }}</p>
                                    
                                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h5 class="text-sm font-medium text-gray-800">{{ __('health_records_create.dental_chart_label') }}</h5>
                                            <div class="flex space-x-2">
                                                <button 
                                                    type="button" 
                                                    @click="forceInitializeDentalChart"
                                                    class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors"
                                                >
                                                    {{ __('health_records_create.reload_button') }}
                                                </button>
                                                <button 
                                                    type="button" 
                                                    @click="clearDentalSelection"
                                                    class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700 transition-colors"
                                                >
                                                    🗑️ {{ __('health_records_create.clear_button') }}
                                                </button>
                                                <button 
                                                    type="button" 
                                                    @click="saveDentalData"
                                                    class="px-3 py-1 bg-yellow-600 text-white rounded text-sm hover:bg-yellow-700 transition-colors"
                                                >
                                                    {{ __('health_records_create.save_button') }}
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Dental Chart Interactive SVG -->
                                        <div class="overflow-x-auto">
                                            <div id="dental-chart-interactive-container" class="w-full max-w-4xl mx-auto">
                                                <div id="dental-chart-svg-container" style="width: 100%; height: 300px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; position: relative;">
                                                    <!-- Le SVG sera chargé ici dynamiquement -->
                                                    <div id="dental-chart-loading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #6b7280;">
                                                        <div style="font-size: 24px; margin-bottom: 10px;">🦷</div>
                                                        <div>{{ __('health_records_create.chart_loading_text') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Dental Chart Info Panel -->
                                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Informations de la dent sélectionnée -->
                                            <div class="p-3 bg-blue-50 rounded-lg">
                                                <h6 class="text-sm font-medium text-blue-800 mb-2">{{ __('health_records_create.selected_tooth_heading') }}</h6>
                                                <div v-if="selectedDentalTooth" class="text-sm">
                                                    <p><strong>{{ __('health_records_create.tooth_label') }}</strong> @{{ selectedDentalTooth }}</p>
                                                    <p><strong>{{ __('health_records_create.type_label') }}</strong> @{{ getDentalToothType(selectedDentalTooth) }}</p>
                                                    <p><strong>{{ __('health_records_create.quadrant_label') }}</strong> @{{ getDentalQuadrant(selectedDentalTooth) }}</p>
                                                    <p><strong>{{ __('health_records_create.status_label') }}</strong>
                                                        <select v-model="dentalToothStatus" class="ml-2 px-2 py-1 border rounded text-xs">
                                                            <option value="healthy">{{ __('health_records_create.tooth_status_healthy') }}</option>
                                                            <option value="cavity">{{ __('health_records_create.tooth_status_cavity') }}</option>
                                                            <option value="filling">{{ __('health_records_create.tooth_status_filling') }}</option>
                                                            <option value="crown">{{ __('health_records_create.tooth_status_crown') }}</option>
                                                            <option value="missing">{{ __('health_records_create.tooth_status_missing') }}</option>
                                                            <option value="implant">{{ __('health_records_create.tooth_status_implant') }}</option>
                                                            <option value="treatment">{{ __('health_records_create.tooth_status_treatment') }}</option>
                                                        </select>
                                                    </p>
                                                    <textarea 
                                                        v-model="dentalToothNotes" 
                                                        placeholder="{{ __('health_records_create.tooth_notes_placeholder') }}"
                                                        class="mt-2 w-full px-2 py-1 border rounded text-xs"
                                                        rows="2"
                                                    ></textarea>
                                                </div>
                                                <div v-else class="text-sm text-gray-500">
                                                    {{ __('health_records_create.click_tooth_hint') }}
                                                </div>
                                            </div>
                                            
                                            <!-- Résumé de l'État Dentaire -->
                                            <div class="p-3 bg-gray-50 rounded-lg">
                                                <h6 class="text-sm font-medium text-gray-800 mb-2">{{ __('health_records_create.dental_summary_heading') }}</h6>
                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-green-100 border border-green-300 rounded mr-2"></div>
                                                        <span>{{ __('health_records_create.summary_healthy') }} <span id="healthy-count">@{{ dentalStats.healthy }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-red-500 border border-gray-300 rounded mr-2"></div>
                                                        <span>{{ __('health_records_create.summary_cavity') }} <span id="cavity-count">@{{ dentalStats.cavity }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-yellow-400 border border-gray-300 rounded mr-2"></div>
                                                        <span>{{ __('health_records_create.summary_filling') }} <span id="filling-count">@{{ dentalStats.filling }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-purple-500 border border-gray-300 rounded mr-2"></div>
                                                        <span>{{ __('health_records_create.summary_crown') }} <span id="crown-count">@{{ dentalStats.crown }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-gray-500 border border-gray-300 rounded mr-2"></div>
                                                        <span>{{ __('health_records_create.summary_missing') }} <span id="missing-count">@{{ dentalStats.missing }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-blue-500 border border-gray-300 rounded mr-2"></div>
                                                        <span>{{ __('health_records_create.summary_implant') }} <span id="implant-count">@{{ dentalStats.implant }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-orange-500 border border-gray-300 rounded mr-2"></div>
                                                        <span>{{ __('health_records_create.summary_treatment') }} <span id="treatment-count">@{{ dentalStats.treatment }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-gray-100 border border-gray-300 rounded mr-2"></div>
                                                        <span>{{ __('health_records_create.summary_unevaluated') }} <span id="unevaluated-count">@{{ dentalStats.unevaluated }}</span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Historique des sélections -->
                                        <div v-if="dentalHistory.length > 0" class="mt-4 p-3 bg-yellow-50 rounded-lg">
                                            <h6 class="text-sm font-medium text-yellow-800 mb-2">{{ __('health_records_create.selection_history_heading') }}</h6>
                                            <div class="flex flex-wrap gap-2">
                                                <span 
                                                    v-for="(tooth, index) in dentalHistory.slice(-8)" 
                                                    :key="index"
                                                    class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded text-xs"
                                                >
                                                    {{ __('health_records_create.tooth_prefix') }} @{{ tooth }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Viewer d'Images Dentaires -->
                                <div class="mt-6">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_create.dental_image_viewer_heading') }}</h4>
                                    <p class="text-sm text-gray-600 mb-4">{{ __('health_records_create.dental_image_viewer_subtitle') }}</p>
                                    
                                    <!-- Upload Section -->
                                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <h5 class="text-sm font-medium text-blue-800 mb-2">{{ __('health_records_create.upload_dental_image_heading') }}</h5>
                                        <div class="flex items-center space-x-4">
                                            <input 
                                                type="file" 
                                                id="dental-image-upload" 
                                                accept=".dcm,.dicom,.jpg,.jpeg,.png,.tiff,.tif"
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm"
                                            >
                                            <button 
                                                type="button" 
                                                id="load-dental-image-btn"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors"
                                            >
                                                {{ __('health_records_create.load_in_viewer_button') }}
                                            </button>
                                        </div>
                                        <p class="text-xs text-blue-600 mt-2">
                                            {{ __('health_records_create.supported_formats_text') }}
                                        </p>
                                    </div>
                                    
                                    <!-- Medical Image Viewer -->
                                    <div id="dental-image-viewer" class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                        <!-- Le viewer sera initialisé ici -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 5: Contrôle Anti-Dopage -->
                    <div v-show="activeTab === 'doping-control'" class="tab-panel">
                        <div class="space-y-6">
                            @include('health-records.aut-embedded')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-red-900 mb-4">{{ __('health_records_create.doping_control_heading') }}</h3>
                                <p class="text-red-700 mb-4">{{ __('health_records_create.doping_control_subtitle') }}</p>
                                
                                <!-- Anti-Doping Tests -->
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-gray-800">{{ __('health_records_create.doping_tests_heading') }}</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="doping_test_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.test_date_label') }}
                                            </label>
                                            <input 
                                                type="date" 
                                                id="doping_test_date" 
                                                name="doping_test_date"
                                                value="{{ old('doping_test_date', date('Y-m-d')) }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                            >
                                        </div>
                                        
                                        <div>
                                            <label for="doping_test_type" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.test_type_label') }}
                                            </label>
                                            <select 
                                                id="doping_test_type" 
                                                name="doping_test_type"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                            >
                                                <option value="">{{ __('health_records_create.select_placeholder') }}</option>
                                                <option value="urine">{{ __('health_records_create.test_type_urine') }}</option>
                                                <option value="blood">{{ __('health_records_create.test_type_blood') }}</option>
                                                <option value="hair">{{ __('health_records_create.test_type_hair') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <label for="doping_test_result" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.test_result_label') }}
                                        </label>
                                        <select 
                                            id="doping_test_result" 
                                            name="doping_test_result"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                        >
                                            <option value="">{{ __('health_records_create.select_placeholder') }}</option>
                                            <option value="negative">{{ __('health_records_create.test_result_negative') }}</option>
                                            <option value="positive">{{ __('health_records_create.test_result_positive') }}</option>
                                            <option value="pending">{{ __('health_records_create.test_result_pending') }}</option>
                                            <option value="invalid">{{ __('health_records_create.test_result_invalid') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 6: Évaluations Physiques -->
                    <div v-show="activeTab === 'physical-assessments'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-yellow-900 mb-4">{{ __('health_records_create.physical_assessments_heading') }}</h3>
                                <p class="text-yellow-700 mb-4">{{ __('health_records_create.physical_assessments_subtitle') }}</p>
                                
                                <!-- Cardiovascular Assessment -->
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-gray-800">{{ __('health_records_create.cardio_assessment_heading') }}</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="cardio_blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.blood_pressure_label') }}
                                            </label>
                                            <div class="flex space-x-2">
                                                <input 
                                                    type="number" 
                                                    id="cardio_systolic" 
                                                    name="cardio_systolic"
                                                    placeholder="{{ __('health_records_create.systolic_placeholder') }}"
                                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                                >
                                                <span class="self-center text-gray-500">/</span>
                                                <input 
                                                    type="number" 
                                                    id="cardio_diastolic" 
                                                    name="cardio_diastolic"
                                                    placeholder="{{ __('health_records_create.diastolic_placeholder') }}"
                                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                                >
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="cardio_heart_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.heart_rate_label') }}
                                            </label>
                                            <input 
                                                type="number" 
                                                id="cardio_heart_rate" 
                                                name="cardio_heart_rate"
                                                min="40" max="200"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                            >
                                        </div>
                                        
                                        <div>
                                            <label for="cardio_oxygen_saturation" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.oxygen_saturation_label') }}
                                            </label>
                                            <input 
                                                type="number" 
                                                id="cardio_oxygen_saturation" 
                                                name="cardio_oxygen_saturation"
                                                min="70" max="100"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 7: Évaluation Posturale -->
                    <div v-show="activeTab === 'postural-assessment'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-indigo-900 mb-4">{{ __('health_records_create.postural_assessment_heading') }}</h3>
                                <p class="text-indigo-700 mb-4">{{ __('health_records_create.postural_assessment_subtitle') }}</p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_create.sagittal_alignment_heading') }}</h4>
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">{{ __('health_records_create.head_label') }}</span>
                                                <select name="posture_head" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="normal">{{ __('health_records_create.posture_normal') }}</option>
                                                    <option value="forward">{{ __('health_records_create.posture_forward') }}</option>
                                                    <option value="backward">{{ __('health_records_create.posture_backward') }}</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">{{ __('health_records_create.shoulders_label') }}</span>
                                                <select name="posture_shoulders" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="normal">{{ __('health_records_create.posture_shoulders_normal') }}</option>
                                                    <option value="rounded">{{ __('health_records_create.posture_rounded') }}</option>
                                                    <option value="elevated">{{ __('health_records_create.posture_elevated') }}</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">{{ __('health_records_create.spine_label') }}</span>
                                                <select name="posture_spine" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="normal">{{ __('health_records_create.posture_spine_normal') }}</option>
                                                    <option value="kyphosis">{{ __('health_records_create.posture_kyphosis') }}</option>
                                                    <option value="lordosis">{{ __('health_records_create.posture_lordosis') }}</option>
                                                    <option value="scoliosis">{{ __('health_records_create.posture_scoliosis') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_create.frontal_alignment_heading') }}</h4>
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">{{ __('health_records_create.shoulders_label') }}</span>
                                                <select name="posture_shoulders_frontal" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="level">{{ __('health_records_create.posture_level') }}</option>
                                                    <option value="left_higher">{{ __('health_records_create.posture_left_higher') }}</option>
                                                    <option value="right_higher">{{ __('health_records_create.posture_right_higher') }}</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">{{ __('health_records_create.pelvis_label') }}</span>
                                                <select name="posture_pelvis" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="level">{{ __('health_records_create.posture_level') }}</option>
                                                    <option value="left_higher">{{ __('health_records_create.posture_left_higher') }}</option>
                                                    <option value="right_higher">{{ __('health_records_create.posture_right_higher') }}</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">{{ __('health_records_create.knees_label') }}</span>
                                                <select name="posture_knees" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="normal">{{ __('health_records_create.posture_shoulders_normal') }}</option>
                                                    <option value="valgus">{{ __('health_records_create.posture_valgus') }}</option>
                                                    <option value="varus">{{ __('health_records_create.posture_varus') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <label for="postural_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('health_records_create.postural_notes_label') }}
                                    </label>
                                    <textarea 
                                        id="postural_notes" 
                                        name="postural_notes"
                                        rows="4"
                                        placeholder="{{ __('health_records_create.postural_notes_placeholder') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 8: Vaccinations -->
                    <div v-show="activeTab === 'vaccinations'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-teal-50 border border-teal-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-teal-900 mb-4">{{ __('health_records_create.vaccinations_heading') }}</h3>
                                <p class="text-teal-700 mb-4">{{ __('health_records_create.vaccinations_subtitle') }}</p>
                                
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-gray-800">{{ __('health_records_create.vaccines_administered_heading') }}</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.vaccine_name_label') }}
                                            </label>
                                            <select 
                                                id="vaccine_name" 
                                                name="vaccine_name"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            >
                                                <option value="">{{ __('health_records_create.select_placeholder') }}</option>
                                                <option value="covid19">{{ __('health_records_create.vaccine_covid19') }}</option>
                                                <option value="influenza">{{ __('health_records_create.vaccine_influenza') }}</option>
                                                <option value="tetanus">{{ __('health_records_create.vaccine_tetanus') }}</option>
                                                <option value="hepatitis_b">{{ __('health_records_create.vaccine_hepatitis_b') }}</option>
                                                <option value="meningococcal">{{ __('health_records_create.vaccine_meningococcal') }}</option>
                                                <option value="pneumococcal">{{ __('health_records_create.vaccine_pneumococcal') }}</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="vaccine_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.vaccine_date_label') }}
                                            </label>
                                            <input 
                                                type="date" 
                                                id="vaccine_date" 
                                                name="vaccine_date"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            >
                                        </div>
                                        
                                        <div>
                                            <label for="vaccine_dose" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.vaccine_dose_label') }}
                                            </label>
                                            <select 
                                                id="vaccine_dose" 
                                                name="vaccine_dose"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            >
                                                <option value="">{{ __('health_records_create.select_placeholder') }}</option>
                                                <option value="1">{{ __('health_records_create.vaccine_dose_1') }}</option>
                                                <option value="2">{{ __('health_records_create.vaccine_dose_2') }}</option>
                                                <option value="3">{{ __('health_records_create.vaccine_dose_3') }}</option>
                                                <option value="booster">{{ __('health_records_create.vaccine_dose_booster') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <label for="vaccine_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.vaccine_notes_label') }}
                                        </label>
                                        <textarea 
                                            id="vaccine_notes" 
                                            name="vaccine_notes"
                                            rows="3"
                                            placeholder="{{ __('health_records_create.vaccine_notes_placeholder') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 9: Imagerie Médicale -->
                    <div v-show="activeTab === 'medical-imaging'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('health_records_create.medical_imaging_heading') }}</h3>
                                <p class="text-blue-700 mb-4">{{ __('health_records_create.medical_imaging_subtitle') }}</p>
                                
                                <!-- Imaging Records -->
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-md font-semibold text-gray-800">{{ __('health_records_create.imaging_exams_heading') }}</h4>
                                        <button 
                                            type="button" 
                                            id="add-imaging-btn"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                        >
                                            {{ __('health_records_create.add_exam_button') }}
                                        </button>
                                    </div>
                                    
                                    <!-- Add Imaging Form -->
                                    <div id="add-imaging-form" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                        <h5 class="text-md font-semibold text-gray-800 mb-4">{{ __('health_records_create.new_imaging_exam_heading') }}</h5>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="imaging_type" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('health_records_create.exam_type_label') }}
                                                </label>
                                                <select 
                                                    id="imaging_type" 
                                                    name="imaging_type"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                                    <option value="">{{ __('health_records_create.select_type_placeholder') }}</option>
                                                    <optgroup label="{{ __('health_records_create.imaging_group_xray') }}">
                                                        <option value="xray_chest">{{ __('health_records_create.xray_chest') }}</option>
                                                        <option value="xray_spine">{{ __('health_records_create.xray_spine') }}</option>
                                                        <option value="xray_limb">{{ __('health_records_create.xray_limb') }}</option>
                                                        <option value="xray_skull">{{ __('health_records_create.xray_skull') }}</option>
                                                    </optgroup>
                                                    <optgroup label="{{ __('health_records_create.imaging_group_ct') }}">
                                                        <option value="ct_head">{{ __('health_records_create.ct_head') }}</option>
                                                        <option value="ct_chest">{{ __('health_records_create.ct_chest') }}</option>
                                                        <option value="ct_abdomen">{{ __('health_records_create.ct_abdomen') }}</option>
                                                        <option value="ct_spine">{{ __('health_records_create.ct_spine') }}</option>
                                                    </optgroup>
                                                    <optgroup label="{{ __('health_records_create.imaging_group_mri') }}">
                                                        <option value="mri_brain">{{ __('health_records_create.mri_brain') }}</option>
                                                        <option value="mri_spine">{{ __('health_records_create.mri_spine') }}</option>
                                                        <option value="mri_knee">{{ __('health_records_create.mri_knee') }}</option>
                                                        <option value="mri_shoulder">{{ __('health_records_create.mri_shoulder') }}</option>
                                                    </optgroup>
                                                    <optgroup label="{{ __('health_records_create.imaging_group_us') }}">
                                                        <option value="us_abdomen">{{ __('health_records_create.us_abdomen') }}</option>
                                                        <option value="us_heart">{{ __('health_records_create.us_heart') }}</option>
                                                        <option value="us_vascular">{{ __('health_records_create.us_vascular') }}</option>
                                                        <option value="us_musculoskeletal">{{ __('health_records_create.us_musculoskeletal') }}</option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label for="imaging_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('health_records_create.exam_date_label') }}
                                                </label>
                                                <input 
                                                    type="date" 
                                                    id="imaging_date" 
                                                    name="imaging_date"
                                                    value="{{ date('Y-m-d') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                            </div>
                                            
                                            <div>
                                                <label for="imaging_facility" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('health_records_create.facility_label') }}
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="imaging_facility" 
                                                    name="imaging_facility"
                                                    placeholder="{{ __('health_records_create.facility_placeholder') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                            </div>
                                            
                                            <div>
                                                <label for="imaging_radiologist" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('health_records_create.radiologist_label') }}
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="imaging_radiologist" 
                                                    name="imaging_radiologist"
                                                    placeholder="{{ __('health_records_create.radiologist_placeholder') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                            </div>
                                            
                                            <div>
                                                <label for="imaging_indication" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('health_records_create.indication_label') }}
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="imaging_indication" 
                                                    name="imaging_indication"
                                                    placeholder="{{ __('health_records_create.indication_placeholder') }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                            </div>
                                            
                                            <div>
                                                <label for="imaging_technique" class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('health_records_create.technique_label') }}
                                                </label>
                                                <select 
                                                    id="imaging_technique" 
                                                    name="imaging_technique"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                                    <option value="">{{ __('health_records_create.select_placeholder') }}</option>
                                                    <option value="standard">{{ __('health_records_create.technique_standard') }}</option>
                                                    <option value="contrast">{{ __('health_records_create.technique_contrast') }}</option>
                                                    <option value="functional">{{ __('health_records_create.technique_functional') }}</option>
                                                    <option value="dynamic">{{ __('health_records_create.technique_dynamic') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- File Upload Section -->
                                        <div class="mt-4">
                                            <label for="imaging_file" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.upload_image_label') }}
                                            </label>
                                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                                <input 
                                                    type="file" 
                                                    id="imaging_file" 
                                                    name="imaging_file"
                                                    accept=".dcm,.dicom,.jpg,.jpeg,.png,.tiff,.tif"
                                                    class="hidden"
                                                >
                                                <div id="file-upload-area" class="cursor-pointer">
                                                    <div class="text-gray-500 mb-2">
                                                        <svg class="mx-auto h-12 w-12" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                    <p class="text-sm text-gray-600">{{ __('health_records_create.click_to_select_file') }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">{{ __('health_records_create.accepted_formats_text') }}</p>
                                                </div>
                                                <div id="file-preview" class="hidden mt-4">
                                                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                                        <div class="flex items-center">
                                                            <span class="text-green-600 mr-2">✅</span>
                                                            <span id="file-name" class="text-sm text-green-800"></span>
                                                        </div>
                                                        <button type="button" id="remove-file" class="text-red-600 hover:text-red-800">×</button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Upload Instructions -->
                                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                <h6 class="text-sm font-medium text-blue-800 mb-2">{{ __('health_records_create.upload_instructions_heading') }}</h6>
                                                <ul class="text-xs text-blue-700 space-y-1">
                                                    <li>• <strong>DICOM</strong> {{ __('health_records_create.upload_instr_dicom') }}</li>
                                                    <li>• <strong>JPG/PNG</strong> {{ __('health_records_create.upload_instr_jpgpng') }}</li>
                                                    <li>• <strong>{{ __('health_records_create.upload_instr_maxsize_label') }}</strong> {{ __('health_records_create.upload_instr_maxsize') }}</li>
                                                    <li>• <strong>{{ __('health_records_create.upload_instr_quality_label') }}</strong> {{ __('health_records_create.upload_instr_quality') }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <label for="imaging_findings" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.findings_label') }}
                                            </label>
                                            <textarea 
                                                id="imaging_findings" 
                                                name="imaging_findings"
                                                rows="4"
                                                placeholder="{{ __('health_records_create.findings_placeholder') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            ></textarea>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <label for="imaging_conclusion" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ __('health_records_create.conclusion_label') }}
                                            </label>
                                            <textarea 
                                                id="imaging_conclusion" 
                                                name="imaging_conclusion"
                                                rows="3"
                                                placeholder="{{ __('health_records_create.conclusion_placeholder') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            ></textarea>
                                        </div>
                                        
                                        <!-- AI Analysis Section -->
                                        <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                            <h6 class="text-sm font-semibold text-purple-900 mb-2">{{ __('health_records_create.ai_analysis_heading') }}</h6>
                                            <div class="flex space-x-2">
                                                <button 
                                                    type="button" 
                                                    id="analyze-imaging-btn"
                                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm"
                                                >
                                                    🔍 {{ __('health_records_create.analyze_ai_button') }}
                                                </button>
                                                <button 
                                                    type="button" 
                                                    id="generate-cda-btn"
                                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm"
                                                >
                                                    {{ __('health_records_create.generate_cda_button') }}
                                                </button>
                                            </div>
                                            <div id="ai-analysis-result" class="hidden mt-3 p-3 bg-white border border-purple-200 rounded-lg">
                                                <h6 class="text-sm font-semibold text-purple-800 mb-2">{{ __('health_records_create.ai_analysis_result_heading') }}</h6>
                                                <div id="ai-analysis-content" class="text-sm text-gray-700"></div>
                                            </div>
                                            
                                            <!-- AI Analysis Instructions -->
                                            <div class="mt-3 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                                <h6 class="text-sm font-medium text-purple-800 mb-2">{{ __('health_records_create.ai_analysis_process_heading') }}</h6>
                                                <ol class="text-xs text-purple-700 space-y-1">
                                                    <li>1. <strong>Upload</strong> {{ __('health_records_create.ai_process_step1') }}</li>
                                                    <li>2. <strong>{{ __('health_records_create.ai_process_step2_label') }}</strong> {{ __('health_records_create.ai_process_step2') }} "🔍 {{ __('health_records_create.analyze_ai_button') }}"</li>
                                                    <li>3. <strong>{{ __('health_records_create.ai_process_step3_label') }}</strong> {{ __('health_records_create.ai_process_step3') }}</li>
                                                    <li>4. <strong>{{ __('health_records_create.ai_process_step4_label') }}</strong> {{ __('health_records_create.ai_process_step4') }}</li>
                                                    <li>5. <strong>CDA</strong> {{ __('health_records_create.ai_process_step5') }}</li>
                                                </ol>
                                            </div>
                                        </div>
                                        
                                        <!-- Image Viewer Section -->
                                        <div class="mt-4 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                                            <h6 class="text-sm font-semibold text-indigo-900 mb-2">{{ __('health_records_create.image_viewer_section_heading') }}</h6>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <h6 class="text-xs font-medium text-indigo-800 mb-1">{{ __('health_records_create.viewer_features_heading') }}</h6>
                                                    <ul class="text-xs text-indigo-700 space-y-1">
                                                        <li>• <strong>Zoom</strong> {{ __('health_records_create.viewer_feature_zoom') }}</li>
                                                        <li>• <strong>Pan</strong> {{ __('health_records_create.viewer_feature_pan') }}</li>
                                                        <li>• <strong>{{ __('health_records_create.viewer_feature_measure_label') }}</strong> {{ __('health_records_create.viewer_feature_measure') }}</li>
                                                        <li>• <strong>Annotations</strong> {{ __('health_records_create.viewer_feature_annotations') }}</li>
                                                        <li>• <strong>{{ __('health_records_create.viewer_feature_contrast_label') }}</strong> {{ __('health_records_create.viewer_feature_contrast') }}</li>
                                                    </ul>
                                                </div>
                                                <div>
                                                    <h6 class="text-xs font-medium text-indigo-800 mb-1">{{ __('health_records_create.supported_formats_heading') }}</h6>
                                                    <ul class="text-xs text-indigo-700 space-y-1">
                                                        <li>• <strong>DICOM</strong> {{ __('health_records_create.format_dicom') }}</li>
                                                        <li>• <strong>JPG/PNG</strong> {{ __('health_records_create.format_jpgpng') }}</li>
                                                        <li>• <strong>TIFF</strong> {{ __('health_records_create.format_tiff') }}</li>
                                                        <li>• <strong>{{ __('health_records_create.format_multiplane_label') }}</strong> {{ __('health_records_create.format_multiplane') }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Medical Image Viewer -->
                                        <div class="mt-4">
                                            <h6 class="text-sm font-semibold text-gray-800 mb-2">{{ __('health_records_create.medical_image_viewer_heading') }}</h6>
                                            <div id="medical-image-viewer" class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                                <!-- Le viewer sera initialisé ici -->
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-end space-x-3 mt-4">
                                            <button 
                                                type="button" 
                                                id="cancel-imaging-btn"
                                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                                            >
                                                {{ __('health_records_create.cancel_button') }}
                                            </button>
                                            <button 
                                                type="button" 
                                                id="save-imaging-btn"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                            >
                                                {{ __('health_records_create.save_button_generic') }}
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Imaging List -->
                                    <div id="imaging-list" class="space-y-3">
                                        <!-- Imaging records will be added here dynamically -->
                                    </div>
                                </div>
                            </div>

                            <!-- Imaging Summary -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('health_records_create.imaging_summary_heading') }}</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-white p-4 rounded-lg border border-green-200">
                                        <h4 class="font-medium text-green-800 mb-2">{{ __('health_records_create.exams_performed_heading') }}</h4>
                                        <div id="imaging-count" class="text-sm text-gray-700">
                                            <span class="text-green-600">0</span> {{ __('health_records_create.exam_count_suffix') }}
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white p-4 rounded-lg border border-green-200">
                                        <h4 class="font-medium text-green-800 mb-2">{{ __('health_records_create.exam_types_heading') }}</h4>
                                        <div id="imaging-types" class="text-sm text-gray-700">
                                            <span class="text-green-600">{{ __('health_records_create.none_label') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white p-4 rounded-lg border border-green-200">
                                        <h4 class="font-medium text-green-800 mb-2">{{ __('health_records_create.abnormal_results_heading') }}</h4>
                                        <div id="imaging-abnormal" class="text-sm text-gray-700">
                                            <span class="text-green-600">0</span> {{ __('health_records_create.abnormal_count_suffix') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden fields for form submission -->
                            <input type="hidden" name="imaging_data" id="imaging_data" value="{{ old('imaging_data') }}">
                            <input type="hidden" name="dental_data" id="dental_data" value="{{ old('dental_data') }}">
                            <input type="hidden" name="selected_dental_tooth" id="selected_dental_tooth" value="{{ old('selected_dental_tooth') }}">
                            
                            <!-- Patient information fields -->
                            @if($selectedPlayer)
                                <input type="hidden" name="patient_name" value="{{ $selectedPlayer->full_name ?? $selectedPlayer->name }}">
                                <input type="hidden" name="patient_birth_date" value="{{ $selectedPlayer->date_of_birth }}">
                                <input type="hidden" name="patient_club" value="{{ $selectedPlayer->club ? $selectedPlayer->club->name : '' }}">
                                <input type="hidden" name="patient_position" value="{{ $selectedPlayer->position ?? '' }}">
                                <input type="hidden" name="patient_nationality" value="{{ $selectedPlayer->nationality ?? '' }}">
                            @endif
                        </div>
                    </div>

                    <!-- Onglet 10: Notes et Observations -->
                    <div v-show="activeTab === 'notes-observations'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('health_records_create.notes_observations_heading') }}</h3>
                                <p class="text-gray-700 mb-4">{{ __('health_records_create.notes_observations_subtitle') }}</p>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.clinical_notes_label') }}
                                        </label>
                                        <textarea 
                                            id="clinical_notes" 
                                            name="clinical_notes"
                                            rows="6"
                                            placeholder="{{ __('health_records_create.clinical_notes_placeholder_2') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                        ></textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.treatment_plan_label') }}
                                        </label>
                                        <textarea 
                                            id="treatment_plan" 
                                            name="treatment_plan"
                                            rows="4"
                                            placeholder="{{ __('health_records_create.treatment_plan_placeholder') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                        ></textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="follow_up" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('health_records_create.follow_up_label') }}
                                        </label>
                                        <textarea 
                                            id="follow_up" 
                                            name="follow_up"
                                            rows="3"
                                            placeholder="{{ __('health_records_create.follow_up_placeholder') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('health-records.sections-capture',['sectionValues'=>session()->getOldInput()])
            <!-- Boutons de soumission -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <button 
                    type="button" 
                    onclick="history.back()"
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                >
                    {{ __('health_records_create.back_button') }}
                </button>
                
                <div class="flex space-x-4">
                    <button 
                        type="button" 
                        id="save-draft-btn"
                        class="px-6 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
                    >
                        {{ __('health_records_create.save_draft_button') }}
                    </button>
                    
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        {{ __('health_records_create.save_visit_button') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Nom du professionnel de sante actuellement connecte (auteur reel du
// rapport CDA genere plus bas), et non plus "Dr. Radiologue" code en dur.
const CURRENT_CLINICIAN_NAME = @json(auth()->user()->name ?? null);

// Libelles traduits (voir resources/lang/{fr,en}/health_records_create.php)
const HRC_LABELS = {
    selectToothFirst: @json(__('health_records_create.js_select_tooth_first')),
    selectPatientFirst: @json(__('health_records_create.js_select_patient_first')),
    toothSaved: @json(__('health_records_create.js_tooth_saved')),
    fileTooLarge: @json(__('health_records_create.js_file_too_large')),
    fillRequiredFields: @json(__('health_records_create.js_fill_required_fields')),
    confirmDeleteImaging: @json(__('health_records_create.js_confirm_delete_imaging')),
    uploadOrFindingsRequired: @json(__('health_records_create.js_upload_or_findings_required')),
    aiNotAvailableHeading: @json(__('health_records_create.js_ai_not_available_heading')),
    aiNotAvailableText: @json(__('health_records_create.js_ai_not_available_text')),
    noImagingForCda: @json(__('health_records_create.js_no_imaging_for_cda')),
    cdaGenerated: @json(__('health_records_create.js_cda_generated')),
    patientUnspecified: @json(__('health_records_create.js_patient_unspecified')),
    doctorUnspecified: @json(__('health_records_create.js_doctor_unspecified')),
    cdaReportTitle: @json(__('health_records_create.js_cda_report_title')),
    cdaImagingCenter: @json(__('health_records_create.js_cda_imaging_center')),
    cdaExamsSectionTitle: @json(__('health_records_create.js_cda_exams_section_title')),
    cdaThType: @json(__('health_records_create.js_cda_th_type')),
    cdaThDate: @json(__('health_records_create.js_cda_th_date')),
    cdaThFacility: @json(__('health_records_create.js_cda_th_facility')),
    cdaThResults: @json(__('health_records_create.js_cda_th_results')),
    confirmDeleteDentalImaging: @json(__('health_records_create.js_confirm_delete_dental_imaging')),
    uploadOrTypeRequired: @json(__('health_records_create.js_upload_or_type_required')),
    dentalAiNotAvailable: @json(__('health_records_create.js_dental_ai_not_available')),
    viewerNotAvailable: @json(__('health_records_create.js_viewer_not_available')),
    viewerCheckScript: @json(__('health_records_create.js_viewer_check_script')),
    viewerReady: @json(__('health_records_create.js_viewer_ready')),
    viewerInitError: @json(__('health_records_create.js_viewer_init_error')),
    loadImageError: @json(__('health_records_create.js_load_image_error')),
    selectFile: @json(__('health_records_create.js_select_file')),
    loadImageErrorColon: @json(__('health_records_create.js_load_image_error_colon')),
    viewerNotAvailableReload: @json(__('health_records_create.js_viewer_not_available_reload')),
    imageLoadedSuccess: @json(__('health_records_create.js_image_loaded_success')),
    loadInViewerButton: @json(__('health_records_create.js_load_in_viewer_button')),
    noNotes: @json(__('health_records_create.js_no_notes')),
    dentalImagingTypes: {
        panoramic: @json(__('health_records_create.dental_imaging_panoramic')),
        bitewing: @json(__('health_records_create.dental_imaging_bitewing')),
        periapical: @json(__('health_records_create.dental_imaging_periapical')),
        occlusal: @json(__('health_records_create.dental_imaging_occlusal')),
        cbct: @json(__('health_records_create.dental_imaging_cbct')),
        dental_ct: @json(__('health_records_create.dental_imaging_dental_ct')),
        intraoral: @json(__('health_records_create.dental_imaging_intraoral')),
        extraoral: @json(__('health_records_create.dental_imaging_extraoral')),
        model: @json(__('health_records_create.dental_imaging_model'))
    },
    toothNames: {
        '11': @json(__('health_records_create.tooth_name_11')),
        '12': @json(__('health_records_create.tooth_name_12')),
        '13': @json(__('health_records_create.tooth_name_13')),
        '14': @json(__('health_records_create.tooth_name_14')),
        '15': @json(__('health_records_create.tooth_name_15')),
        '16': @json(__('health_records_create.tooth_name_16')),
        '17': @json(__('health_records_create.tooth_name_17')),
        '18': @json(__('health_records_create.tooth_name_18')),
        '21': @json(__('health_records_create.tooth_name_21')),
        '22': @json(__('health_records_create.tooth_name_22')),
        '23': @json(__('health_records_create.tooth_name_23')),
        '24': @json(__('health_records_create.tooth_name_24')),
        '25': @json(__('health_records_create.tooth_name_25')),
        '26': @json(__('health_records_create.tooth_name_26')),
        '27': @json(__('health_records_create.tooth_name_27')),
        '28': @json(__('health_records_create.tooth_name_28')),
        '31': @json(__('health_records_create.tooth_name_31')),
        '32': @json(__('health_records_create.tooth_name_32')),
        '33': @json(__('health_records_create.tooth_name_33')),
        '34': @json(__('health_records_create.tooth_name_34')),
        '35': @json(__('health_records_create.tooth_name_35')),
        '36': @json(__('health_records_create.tooth_name_36')),
        '37': @json(__('health_records_create.tooth_name_37')),
        '38': @json(__('health_records_create.tooth_name_38')),
        '41': @json(__('health_records_create.tooth_name_41')),
        '42': @json(__('health_records_create.tooth_name_42')),
        '43': @json(__('health_records_create.tooth_name_43')),
        '44': @json(__('health_records_create.tooth_name_44')),
        '45': @json(__('health_records_create.tooth_name_45')),
        '46': @json(__('health_records_create.tooth_name_46')),
        '47': @json(__('health_records_create.tooth_name_47')),
        '48': @json(__('health_records_create.tooth_name_48')),
        unknown: @json(__('health_records_create.tooth_name_unknown'))
    },
    quadrantNames: {
        1: @json(__('health_records_create.quadrant_desc_1')),
        2: @json(__('health_records_create.quadrant_desc_2')),
        3: @json(__('health_records_create.quadrant_desc_3')),
        4: @json(__('health_records_create.quadrant_desc_4')),
        unknown: @json(__('health_records_create.quadrant_desc_unknown'))
    },
    imagingTypes: {
        xray_chest: @json(__('health_records_create.imaging_xray_chest')),
        xray_spine: @json(__('health_records_create.imaging_xray_spine')),
        xray_limb: @json(__('health_records_create.imaging_xray_limb')),
        xray_skull: @json(__('health_records_create.imaging_xray_skull')),
        ct_head: @json(__('health_records_create.imaging_ct_head')),
        ct_chest: @json(__('health_records_create.imaging_ct_chest')),
        ct_abdomen: @json(__('health_records_create.imaging_ct_abdomen')),
        ct_spine: @json(__('health_records_create.imaging_ct_spine')),
        mri_brain: @json(__('health_records_create.imaging_mri_brain')),
        mri_spine: @json(__('health_records_create.imaging_mri_spine')),
        mri_knee: @json(__('health_records_create.imaging_mri_knee')),
        mri_shoulder: @json(__('health_records_create.imaging_mri_shoulder')),
        us_abdomen: @json(__('health_records_create.imaging_us_abdomen')),
        us_heart: @json(__('health_records_create.imaging_us_heart')),
        us_vascular: @json(__('health_records_create.imaging_us_vascular')),
        us_musculoskeletal: @json(__('health_records_create.imaging_us_musculoskeletal'))
    }
};

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier que Vue.js est chargé
    if (typeof Vue === 'undefined') {
        console.error('Vue.js n\'est pas chargé');
        return;
    }

    // Configuration des onglets
    const tabs = [
        { id: 'general', label: @json(__('health_records_create.tab_general')), icon: '👤' },
        { id: 'ai-assistant', label: @json(__('health_records_create.tab_ai_assistant')), icon: '🤖' },
        { id: 'medical-categories', label: @json(__('health_records_create.tab_medical_categories')), icon: '🏥' },
        { id: 'dental-record', label: @json(__('health_records_create.tab_dental_record')), icon: '🦷' },
        { id: 'doping-control', label: @json(__('health_records_create.tab_doping_control')), icon: '🧪' },
        { id: 'physical-assessments', label: @json(__('health_records_create.tab_physical_assessments')), icon: '💪' },
        { id: 'postural-assessment', label: @json(__('health_records_create.tab_postural_assessment')), icon: '🦴' },
        { id: 'vaccinations', label: @json(__('health_records_create.tab_vaccinations')), icon: '💉' },
        { id: 'medical-imaging', label: @json(__('health_records_create.tab_medical_imaging')), icon: '📷' },
        { id: 'notes-observations', label: @json(__('health_records_create.tab_notes_observations')), icon: '📝' }
    ];

    // Initialisation de Vue.js
    const { createApp, ref } = Vue;

    // Fonction pour initialiser l'affichage des informations du joueur
    function initializePlayerDisplay() {
        console.log('🔍 Initialisation de l\'affichage des informations du joueur...');
        
        const playerSelect = document.getElementById('player_id');
        const playerInfo = document.getElementById('player-info');
        
        console.log('playerSelect:', playerSelect);
        console.log('playerInfo:', playerInfo);
        
        if (playerSelect) {
            console.log('✅ Élément player_id trouvé, ajout de l\'écouteur d\'événement...');
            playerSelect.addEventListener('change', function() {
                const selectedPlayerId = this.value;
                console.log('🔄 Changement de joueur sélectionné:', selectedPlayerId);
                
                if (selectedPlayerId) {
                    console.log('📡 Récupération des informations du joueur...');
                    // Récupérer les informations du joueur via AJAX
                    fetch(`/api/players/${selectedPlayerId}`)
                        .then(response => {
                            console.log('📥 Réponse API reçue:', response.status);
                            return response.json();
                        })
                        .then(player => {
                            console.log('👤 Données du joueur reçues:', player);
                            
                            const fullNameElement = document.getElementById('player-full-name');
                            const birthdateElement = document.getElementById('player-birthdate');
                            const clubElement = document.getElementById('player-club');
                            const positionElement = document.getElementById('player-position');
                            const ageElement = document.getElementById('player-age');
                            const nationalityElement = document.getElementById('player-nationality');
                            
                            console.log('🔍 Éléments DOM:', {
                                fullNameElement,
                                birthdateElement,
                                clubElement,
                                positionElement,
                                ageElement,
                                nationalityElement
                            });
                            
                            if (fullNameElement) fullNameElement.textContent = player.full_name || player.name;
                            if (birthdateElement) birthdateElement.textContent = player.date_of_birth ? new Date(player.date_of_birth).toLocaleDateString('en-GB') : 'N/A';
                            if (clubElement) clubElement.textContent = player.club ? player.club.name : 'N/A';
                            if (positionElement) positionElement.textContent = player.position || 'N/A';
                            if (ageElement) ageElement.textContent = player.age ? player.age + ' ans' : 'N/A';
                            if (nationalityElement) nationalityElement.textContent = player.nationality || 'N/A';
                            
                            if (playerInfo) {
                                playerInfo.style.display = 'block';
                                console.log('✅ Informations du joueur affichées');
                            }
                            
                            // Mettre à jour aussi l'affichage dans l'onglet dentaire
                            const dentalPlayerInfo = document.getElementById('dental-player-info');
                            if (dentalPlayerInfo) {
                                const dentalFullNameElement = document.getElementById('dental-player-full-name');
                                const dentalBirthdateElement = document.getElementById('dental-player-birthdate');
                                const dentalClubElement = document.getElementById('dental-player-club');
                                const dentalPositionElement = document.getElementById('dental-player-position');
                                const dentalAgeElement = document.getElementById('dental-player-age');
                                const dentalNationalityElement = document.getElementById('dental-player-nationality');
                                
                                if (dentalFullNameElement) dentalFullNameElement.textContent = player.full_name || player.name;
                                if (dentalBirthdateElement) dentalBirthdateElement.textContent = player.date_of_birth ? new Date(player.date_of_birth).toLocaleDateString('en-GB') : 'N/A';
                                if (dentalClubElement) dentalClubElement.textContent = player.club ? player.club.name : 'N/A';
                                if (dentalPositionElement) dentalPositionElement.textContent = player.position || 'N/A';
                                if (dentalAgeElement) dentalAgeElement.textContent = player.age ? player.age + ' ans' : 'N/A';
                                if (dentalNationalityElement) dentalNationalityElement.textContent = player.nationality || 'N/A';
                                
                                dentalPlayerInfo.style.display = 'block';
                                console.log('✅ Informations du joueur affichées dans l\'onglet dentaire');
                            }
                        })
                        .catch(error => {
                            console.error('❌ Erreur lors de la récupération des informations du joueur:', error);
                            if (playerInfo) playerInfo.style.display = 'none';
                            
                            // Masquer aussi l'affichage dans l'onglet dentaire en cas d'erreur
                            const dentalPlayerInfo = document.getElementById('dental-player-info');
                            if (dentalPlayerInfo) {
                                dentalPlayerInfo.style.display = 'none';
                                console.log('🚫 Informations du joueur masquées dans l\'onglet dentaire (erreur)');
                            }
                        });
                } else {
                    console.log('🚫 Aucun joueur sélectionné, masquage des informations');
                    if (playerInfo) playerInfo.style.display = 'none';
                    
                    // Masquer aussi l'affichage dans l'onglet dentaire
                    const dentalPlayerInfo = document.getElementById('dental-player-info');
                    if (dentalPlayerInfo) {
                        dentalPlayerInfo.style.display = 'none';
                        console.log('🚫 Informations du joueur masquées dans l\'onglet dentaire');
                    }
                }
            });
        } else {
            console.error('❌ Élément player_id non trouvé dans le DOM');
        }
    }

    // Initialiser après que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePlayerDisplay);
    } else {
        initializePlayerDisplay();
    }
    
    // Déclencher l'affichage initial si un joueur est déjà sélectionné
    function triggerInitialPlayerDisplay() {
        const playerSelect = document.getElementById('player_id');
        if (playerSelect && playerSelect.value) {
            console.log('🔄 Déclenchement de l\'affichage initial pour le joueur sélectionné:', playerSelect.value);
            const event = new Event('change');
            playerSelect.dispatchEvent(event);
        }
    }
    
    // Déclencher l'affichage initial après un court délai
    setTimeout(triggerInitialPlayerDisplay, 100);

    try {
        const app = createApp({
            setup() {
                const autEnabled = ref(@json((bool) old('prepare_aut', false)));
                const activeTab = ref(@json(old('prepare_aut') ? 'doping-control' : 'general'));
                
                // Données pour le diagramme dentaire interactif
                const selectedDentalTooth = ref(null);
                const dentalToothStatus = ref('healthy');
                const dentalToothNotes = ref('');
                const dentalHistory = ref([]);
                const dentalData = ref({});
                const dentalStats = ref({
                    healthy: 32,
                    cavity: 0,
                    filling: 0,
                    crown: 0,
                    missing: 0,
                    implant: 0,
                    treatment: 0,
                    unevaluated: 0
                });

                // Méthodes pour le diagramme dentaire
                const initializeDentalChart = () => {
    
                    
                    const container = document.getElementById('dental-chart-svg-container');
                    const loading = document.getElementById('dental-chart-loading');
                    
                    if (!container) {
                        console.error('🦷 Conteneur SVG non trouvé');
                        return;
                    }
                    
                    // Charger le SVG dynamiquement
                    fetch('/images/dental-chart-interactive.svg')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                            return response.text();
                        })
                        .then(svgContent => {
    
                            
                            // Masquer le loading
                            if (loading) {
                                loading.style.display = 'none';
                            }
                            
                            // Charger le SVG
                            container.innerHTML = svgContent;
                            
                            // Ajouter les écouteurs d'événements directement sur les dents
                            setTimeout(() => {
                                const teeth = document.querySelectorAll('.tooth');

                                
                                teeth.forEach(tooth => {
                                    tooth.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const toothId = this.getAttribute('data-tooth-id');
                                        
                                        // Désélectionner la dent précédemment sélectionnée
                                        const prevSelected = document.querySelector('.tooth.selected');
                                        if (prevSelected) {
                                            prevSelected.classList.remove('selected');
                                        }
                                        
                                        // Sélectionner la nouvelle dent
                                        this.classList.add('selected');
                                        
                                        // Mettre à jour Vue.js
                                        selectedDentalTooth.value = toothId;
                                        dentalHistory.value.push(toothId);
                                        
                                        // Charger les données existantes de la dent
                                        if (dentalData.value[toothId]) {
                                            const toothData = dentalData.value[toothId];
                                            dentalToothStatus.value = toothData.status || 'healthy';
                                            dentalToothNotes.value = toothData.notes || '';
                                        } else {
                                            dentalToothStatus.value = 'healthy';
                                            dentalToothNotes.value = '';
                                        }
                                        

                                    });
                                    
                                    // Ajouter des tooltips
                                    tooth.addEventListener('mouseenter', function() {
                                        const toothId = this.getAttribute('data-tooth-id');
                                        this.setAttribute('title', `Dent ${toothId} - ${getDentalToothType(toothId)}`);
                                    });
                                });
                            }, 500);
                            

                            

                        })
                        .catch(error => {
                            console.error('🦷 Erreur lors du chargement du SVG:', error);
                            if (loading) {
                                loading.innerHTML = `
                                    <div style="color: #ef4444;">
                                        <div style="font-size: 24px; margin-bottom: 10px;">⚠️</div>
                                        <div>${HRC_LABELS.loadImageError}</div>
                                        <div style="font-size: 12px; margin-top: 5px;">${error.message}</div>
                                    </div>
                                `;
                            }
                        });
                };
                
                const clearDentalSelection = () => {
                    selectedDentalTooth.value = null;
                    dentalToothStatus.value = 'healthy';
                    dentalToothNotes.value = '';
                    
                    // Désélectionner visuellement dans le SVG
                    const selectedToothElement = document.querySelector('.tooth.selected');
                    if (selectedToothElement) {
                        selectedToothElement.classList.remove('selected');
                    }
                };
                
                const saveDentalData = () => {
                    if (!selectedDentalTooth.value) {
                        alert(HRC_LABELS.selectToothFirst);
                        return;
                    }
                    
                    // Vérifier que le patient est sélectionné
                    const playerId = document.getElementById('player_id').value;
                    if (!playerId) {
                        alert(HRC_LABELS.selectPatientFirst);
                        return;
                    }
                    
                    // Sauvegarder les données de la dent
                    dentalData.value[selectedDentalTooth.value] = {
                        status: dentalToothStatus.value,
                        notes: dentalToothNotes.value,
                        lastUpdated: new Date().toISOString(),
                        playerId: playerId
                    };
                    
                    // Mettre à jour les statistiques
                    updateDentalStats();
                    
                    // Mettre à jour la couleur de la dent dans le SVG
                    updateDentalToothColor(selectedDentalTooth.value, dentalToothStatus.value);
                    
                    // Mettre à jour le champ caché pour l'envoi au serveur
                    updateDentalDataField();
                    
                    alert(HRC_LABELS.toothSaved.replace(':tooth', selectedDentalTooth.value));
                };
                
                const updateDentalStats = () => {
                    const stats = {
                        healthy: 0,
                        cavity: 0,
                        filling: 0,
                        crown: 0,
                        missing: 0,
                        implant: 0,
                        treatment: 0,
                        unevaluated: 0
                    };
                    
                    // Compter les dents par statut
                    Object.values(dentalData.value).forEach(toothData => {
                        if (stats[toothData.status] !== undefined) {
                            stats[toothData.status]++;
                        }
                    });
                    
                    // Les dents non évaluées sont celles qui n'ont pas de données
                    stats.unevaluated = 32 - Object.keys(dentalData.value).length;
                    
                    dentalStats.value = stats;
                };
                
                const updateDentalToothColor = (toothId, status) => {
                    const tooth = document.querySelector(`[data-tooth-id="${toothId}"]`);
                    if (tooth) {
                        // Supprimer les classes de couleur précédentes
                        tooth.classList.remove('healthy', 'cavity', 'filling', 'crown', 'missing', 'implant', 'treatment');
                        
                        // Ajouter la nouvelle classe de couleur
                        tooth.classList.add(status);
                        
                        // Mettre à jour la couleur de remplissage
                        const colors = {
                            healthy: '#10b981',
                            cavity: '#ef4444',
                            filling: '#f59e0b',
                            crown: '#8b5cf6',
                            missing: '#6b7280',
                            implant: '#3b82f6',
                            treatment: '#f97316'
                        };
                        
                        if (colors[status]) {
                            tooth.style.fill = colors[status];
                        }
                    }
                };
                
                // Méthode pour forcer l'initialisation du diagramme dentaire
                const forceInitializeDentalChart = () => {
                    setTimeout(() => {
                        initializeDentalChart();
                    }, 1000);
                };
                
                // Méthode pour mettre à jour le champ caché avec les données dentaires
                const updateDentalDataField = () => {
                    const dentalDataField = document.getElementById('dental_data');
                    const selectedToothField = document.getElementById('selected_dental_tooth');
                    
                    if (dentalDataField) {
                        dentalDataField.value = JSON.stringify(dentalData.value);
                        dentalDataField.dispatchEvent(new Event('change',{bubbles:true}));
                    }
                    
                    if (selectedToothField && selectedDentalTooth.value) {
                        selectedToothField.value = selectedDentalTooth.value;
                    }
                };
                
                const getDentalToothType = (toothId) => {
                    return HRC_LABELS.toothNames[toothId] || HRC_LABELS.toothNames.unknown;
                };
                
                const getDentalQuadrant = (toothId) => {
                    const firstDigit = parseInt(toothId.charAt(0));
                    return HRC_LABELS.quadrantNames[firstDigit] || HRC_LABELS.quadrantNames.unknown;
                };

                return {
                    tabs,
                    autEnabled,
                    activeTab,
                    selectedDentalTooth,
                    dentalToothStatus,
                    dentalToothNotes,
                    dentalHistory,
                    dentalData,
                    dentalStats,
                    initializeDentalChart,
                    forceInitializeDentalChart,
                    clearDentalSelection,
                    saveDentalData,
                    getDentalToothType,
                    getDentalQuadrant,
                    updateDentalDataField
                };
            }
        });

        app.mount('#health-records-tabs');
        
        // Mettre à jour les données dentaires avant la soumission du formulaire
        const form = document.querySelector('form[action*="health-records.store"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Mettre à jour les champs cachés avec les données dentaires
                if (window.__VUE_APP__ && window.__VUE_APP__.updateDentalDataField) {
                    window.__VUE_APP__.updateDentalDataField();
                }
            });
        }
        
    } catch (error) {
        console.error('Erreur lors du montage de Vue.js:', error);
    }

    // Imaging Management
    let imagingRecords = [];

    function initializeImagingManagement() {
        // Add imaging button
        const addImagingBtn = document.getElementById('add-imaging-btn');
        if (addImagingBtn) {
            addImagingBtn.addEventListener('click', function() {
                document.getElementById('add-imaging-form').classList.remove('hidden');
            });
        }
        
        // Cancel imaging button
        const cancelImagingBtn = document.getElementById('cancel-imaging-btn');
        if (cancelImagingBtn) {
            cancelImagingBtn.addEventListener('click', function() {
                document.getElementById('add-imaging-form').classList.add('hidden');
                clearImagingForm();
            });
        }
        
        // Save imaging button
        const saveImagingBtn = document.getElementById('save-imaging-btn');
        if (saveImagingBtn) {
            saveImagingBtn.addEventListener('click', function() {
                saveImagingRecord();
            });
        }

        // File upload handling
        const fileUploadArea = document.getElementById('file-upload-area');
        const imagingFile = document.getElementById('imaging_file');
        const filePreview = document.getElementById('file-preview');
        const fileName = document.getElementById('file-name');
        const removeFile = document.getElementById('remove-file');

        if (fileUploadArea && imagingFile) {
            fileUploadArea.addEventListener('click', function() {
                imagingFile.click();
            });

            imagingFile.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 10 * 1024 * 1024) { // 10MB limit
                        alert(HRC_LABELS.fileTooLarge);
                        return;
                    }
                    
                    fileName.textContent = file.name;
                    filePreview.classList.remove('hidden');
                    fileUploadArea.classList.add('hidden');
                }
            });

            if (removeFile) {
                removeFile.addEventListener('click', function() {
                    imagingFile.value = '';
                    filePreview.classList.add('hidden');
                    fileUploadArea.classList.remove('hidden');
                });
            }
        }

        // AI Analysis button
        const analyzeImagingBtn = document.getElementById('analyze-imaging-btn');
        if (analyzeImagingBtn) {
            analyzeImagingBtn.addEventListener('click', function() {
                analyzeImagingWithAI();
            });
        }

        // Generate CDA button
        const generateCdaBtn = document.getElementById('generate-cda-btn');
        if (generateCdaBtn) {
            generateCdaBtn.addEventListener('click', function() {
                generateCDAReport();
            });
        }
    }

    function saveImagingRecord() {
        const formData = {
            imaging_type: document.getElementById('imaging_type').value,
            imaging_date: document.getElementById('imaging_date').value,
            imaging_facility: document.getElementById('imaging_facility').value,
            imaging_radiologist: document.getElementById('imaging_radiologist').value,
            imaging_indication: document.getElementById('imaging_indication').value,
            imaging_technique: document.getElementById('imaging_technique').value,
            imaging_findings: document.getElementById('imaging_findings').value,
            imaging_conclusion: document.getElementById('imaging_conclusion').value,
            id: Date.now()
        };
        
        if (!formData.imaging_type || !formData.imaging_date || !formData.imaging_findings) {
            alert(HRC_LABELS.fillRequiredFields);
            return;
        }
        
        const selectedFile=document.getElementById('imaging_file')?.files?.[0];
        if(selectedFile) {
            let bank=document.getElementById('medical-imaging-file-bank');
            if(!bank) {
                bank=document.createElement('input');bank.type='file';bank.multiple=true;
                bank.hidden=true;bank.id='medical-imaging-file-bank';bank.name='medical_files[imaging][]';
                document.getElementById('imaging_file').closest('form').appendChild(bank);
            }
            const transfer=new DataTransfer();
            Array.from(bank.files).forEach(file=>transfer.items.add(file));transfer.items.add(selectedFile);
            bank.files=transfer.files;formData.attachment_name=selectedFile.name;
        }
        imagingRecords.push(formData);
        updateImagingList();
        updateImagingData();
        
        document.getElementById('add-imaging-form').classList.add('hidden');
        clearImagingForm();
    }

    function clearImagingForm() {
        const fields = [
            'imaging_type', 'imaging_date', 'imaging_facility', 'imaging_radiologist',
            'imaging_indication', 'imaging_technique', 'imaging_findings', 'imaging_conclusion'
        ];
        
        fields.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                if (field === 'imaging_date') {
                    element.value = '{{ date("Y-m-d") }}';
                } else {
                    element.value = '';
                }
            }
        });

        // Clear file upload
        const imagingFile = document.getElementById('imaging_file');
        const filePreview = document.getElementById('file-preview');
        const fileUploadArea = document.getElementById('file-upload-area');
        
        if (imagingFile) imagingFile.value = '';
        if (filePreview) filePreview.classList.add('hidden');
        if (fileUploadArea) fileUploadArea.classList.remove('hidden');
    }

    function updateImagingList() {
        const list=document.getElementById('imaging-list');
        if(!list) return;
        list.replaceChildren();
        imagingRecords.forEach((record,index)=>{
            const row=document.createElement('div');
            row.className='flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200';
            const body=document.createElement('div');body.className='flex-1';
            [getImagingTypeDisplayName(record.imaging_type),record.imaging_date,
                record.imaging_facility,record.imaging_findings].forEach(value=>{
                const text=document.createElement('p');text.textContent=value || '—';body.appendChild(text);
            });
            row.appendChild(body);
            [['✏️',()=>editImagingRecord(index)],['🗑️',()=>deleteImagingRecord(index)]].forEach(([label,action])=>{
                const button=document.createElement('button');button.type='button';
                button.textContent=label;button.className='text-blue-600 px-2';
                button.addEventListener('click',action);row.appendChild(button);
            });
            list.appendChild(row);
        });
    }

    function getImagingTypeDisplayName(type) {
        const types = {
            'xray_chest': '{{ __('health_records_create.xray_chest') }}',
            'xray_spine': '{{ __('health_records_create.xray_spine') }}',
            'xray_limb': '{{ __('health_records_create.xray_limb') }}',
            'xray_skull': '{{ __('health_records_create.xray_skull') }}',
            'ct_head': '{{ __('health_records_create.ct_head') }}',
            'ct_chest': '{{ __('health_records_create.ct_chest') }}',
            'ct_abdomen': '{{ __('health_records_create.ct_abdomen') }}',
            'ct_spine': '{{ __('health_records_create.ct_spine') }}',
            'mri_brain': '{{ __('health_records_create.mri_brain') }}',
            'mri_spine': '{{ __('health_records_create.mri_spine') }}',
            'mri_knee': '{{ __('health_records_create.mri_knee') }}',
            'mri_shoulder': '{{ __('health_records_create.mri_shoulder') }}',
            'us_abdomen': '{{ __('health_records_create.us_abdomen') }}',
            'us_heart': '{{ __('health_records_create.us_heart') }}',
            'us_vascular': '{{ __('health_records_create.us_vascular') }}',
            'us_musculoskeletal': '{{ __('health_records_create.us_musculoskeletal') }}'
        };
        return types[type] || type;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB');
    }

    function editImagingRecord(index) {
        const record = imagingRecords[index];
        
        const fields = [
            'imaging_type', 'imaging_date', 'imaging_facility', 'imaging_radiologist',
            'imaging_indication', 'imaging_technique', 'imaging_findings', 'imaging_conclusion'
        ];
        
        fields.forEach(field => {
            const element = document.getElementById(field);
            if (element && record[field]) {
                element.value = record[field];
            }
        });
        
        imagingRecords.splice(index, 1);
        
        document.getElementById('add-imaging-form').classList.remove('hidden');
    }

    function deleteImagingRecord(index) {
        if (confirm(HRC_LABELS.confirmDeleteImaging)) {
            imagingRecords.splice(index, 1);
            const bank=document.getElementById('medical-imaging-file-bank');
            if(bank) {
                const transfer=new DataTransfer();
                Array.from(bank.files).filter(file=>imagingRecords.some(record=>record.attachment_name===file.name)).forEach(file=>transfer.items.add(file));
                bank.files=transfer.files;
            }
            updateImagingList();
            updateImagingData();
        }
    }

    function updateImagingData() {
        const imagingDataField = document.getElementById('imaging_data');
        if (imagingDataField) {
            imagingDataField.value = JSON.stringify(imagingRecords);
            imagingDataField.dispatchEvent(new Event('change',{bubbles:true}));
        }
        updateImagingSummary();
    }

    function updateImagingSummary() {
        const countElement = document.getElementById('imaging-count');
        const typesElement = document.getElementById('imaging-types');
        const abnormalElement = document.getElementById('imaging-abnormal');
        
        if (countElement) {
            countElement.innerHTML = `<span class="text-green-600">${imagingRecords.length}</span> {{ __('health_records_create.exam_count_suffix') }}`;
        }
        
        if (typesElement) {
            const types = [...new Set(imagingRecords.map(r => r.imaging_type))];
            if (types.length > 0) {
                typesElement.innerHTML = `<span class="text-green-600">${types.length}</span> {{ __('health_records_create.exam_types_count_suffix') }}`;
            } else {
                typesElement.innerHTML = `<span class="text-green-600">{{ __('health_records_create.none_label') }}</span>`;
            }
        }
        
        if (abnormalElement) {
            const abnormalCount = imagingRecords.filter(r => 
                r.imaging_findings.toLowerCase().includes('anormal') || 
                r.imaging_findings.toLowerCase().includes('abnormal') ||
                r.imaging_findings.toLowerCase().includes('pathologique') ||
                r.imaging_findings.toLowerCase().includes('pathological') ||
                r.imaging_findings.toLowerCase().includes('fracture') ||
                r.imaging_findings.toLowerCase().includes('lésion') ||
                r.imaging_findings.toLowerCase().includes('lesion')
            ).length;
            abnormalElement.innerHTML = `<span class="text-green-600">${abnormalCount}</span> {{ __('health_records_create.abnormal_count_suffix') }}`;
        }
    }

    function analyzeImagingWithAI() {
        const imagingFile = document.getElementById('imaging_file');
        const imagingFindings = document.getElementById('imaging_findings').value;
        
        if (!imagingFile.files[0] && !imagingFindings) {
            alert(HRC_LABELS.uploadOrFindingsRequired);
            return;
        }

        const aiResult = document.getElementById('ai-analysis-result');
        const aiContent = document.getElementById('ai-analysis-content');

        // NOTE (audit factice -> reel, 2026-09) : cette "analyse IA" ne
        // regardait jamais l'image : elle cherchait simplement les mots
        // "anormal"/"pathologique"/"fracture"/"lesion" dans le texte que le
        // medecin venait lui-meme de saisir dans "imaging_findings", puis
        // affichait ce mot-cle comme un "Diagnostic IA" avec un pourcentage
        // de confiance invente (85%/92%, toujours les memes deux valeurs).
        // Aucun service d'IA d'analyse d'imagerie medicale n'est reellement
        // connecte a cette application.
        aiContent.innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <p class="text-yellow-800 font-semibold mb-1">${HRC_LABELS.aiNotAvailableHeading}</p>
                <p class="text-sm text-yellow-800">
                    ${HRC_LABELS.aiNotAvailableText}
                </p>
            </div>
        `;
        aiResult.classList.remove('hidden');
    }

    function generateCDAReport() {
        const imagingRecords = JSON.parse(document.getElementById('imaging_data').value || '[]');
        
        if (imagingRecords.length === 0) {
            alert(HRC_LABELS.noImagingForCda);
            return;
        }

        // NOTE (audit factice -> reel, 2026-09) : le nom du patient etait
        // code en dur ("Patient Test") quel que soit le joueur reellement
        // selectionne dans le formulaire ; recupere desormais le vrai nom
        // depuis le select #player_id.
        const playerSelectEl = document.getElementById('player_id');
        const selectedOption = playerSelectEl ? playerSelectEl.options[playerSelectEl.selectedIndex] : null;
        const patientName = (selectedOption && selectedOption.value) ? selectedOption.textContent.trim() : HRC_LABELS.patientUnspecified;

        const cdaReport = generateCDAXML(imagingRecords, patientName);
        
        // Créer un blob et télécharger
        const blob = new Blob([cdaReport], { type: 'application/xml' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'rapport_imagerie_cda.xml';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        alert(HRC_LABELS.cdaGenerated);
    }

    function generateCDAXML(records, patientName) {
        const now = new Date().toISOString();
        patientName = patientName || HRC_LABELS.patientUnspecified;
        const authorName = CURRENT_CLINICIAN_NAME || HRC_LABELS.doctorUnspecified;
        
        const xmlContent = 
            '<ClinicalDocument xmlns="urn:hl7-org:v3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' +
            '<realmCode code="FR"/>' +
            '<typeId root="2.16.840.1.113883.1.3" extension="POCD_HD000040"/>' +
            '<templateId root="2.16.840.1.113883.10.20.1"/>' +
            '<id root="2.16.840.1.113883.19" extension="123456789"/>' +
            '<code code="11506-3" codeSystem="2.16.840.1.113883.6.1" displayName="Progress note"/>' +
            '<title>' + HRC_LABELS.cdaReportTitle + '</title>' +
            '<effectiveTime value="' + now + '"/>' +
            '<confidentialityCode code="N" codeSystem="2.16.840.1.113883.5.25"/>' +
            '<languageCode code="en-US"/>' +
            '<setId/>' +
            '<versionNumber/>' +
            '<recordTarget>' +
                '<patientRole>' +
                    '<id root="2.16.840.1.113883.19.5" extension="123456789"/>' +
                    '<patient>' +
                        '<name>' +
                            '<given>' + patientName + '</given>' +
                        '</name>' +
                    '</patient>' +
                '</patientRole>' +
            '</recordTarget>' +
            '<author>' +
                '<time value="' + now + '"/>' +
                '<assignedAuthor>' +
                    '<id root="2.16.840.1.113883.19.5" extension="RADIOLOGIST"/>' +
                    '<assignedPerson>' +
                        '<name>' +
                            '<given>' + authorName + '</given>' +
                        '</name>' +
                    '</assignedPerson>' +
                '</assignedAuthor>' +
            '</author>' +
            '<custodian>' +
                '<assignedCustodian>' +
                    '<representedCustodianOrganization>' +
                        '<id root="2.16.840.1.113883.19.5" extension="HOSPITAL"/>' +
                        '<name>' + HRC_LABELS.cdaImagingCenter + '</name>' +
                    '</representedCustodianOrganization>' +
                '</assignedCustodian>' +
            '</custodian>' +
            '<component>' +
                '<structuredBody>' +
                    '<component>' +
                        '<section>' +
                            '<templateId root="2.16.840.1.113883.10.20.1.11"/>' +
                            '<code code="8716-3" codeSystem="2.16.840.1.113883.6.1" displayName="Vital signs"/>' +
                            '<title>' + HRC_LABELS.cdaExamsSectionTitle + '</title>' +
                            '<text>' +
                                '<table border="1" width="100%">' +
                                    '<thead>' +
                                        '<tr>' +
                                            '<th>' + HRC_LABELS.cdaThType + '</th>' +
                                            '<th>' + HRC_LABELS.cdaThDate + '</th>' +
                                            '<th>{{ __('health_records_create.facility_label') }}</th>' +
                                            '<th>' + HRC_LABELS.cdaThResults + '</th>' +
                                        '</tr>' +
                                    '</thead>' +
                                    '<tbody>' +
                                        records.map(record => 
                                            '<tr>' +
                                                '<td>' + getImagingTypeDisplayName(record.imaging_type) + '</td>' +
                                                '<td>' + formatDate(record.imaging_date) + '</td>' +
                                                '<td>' + (record.imaging_facility || 'N/A') + '</td>' +
                                                '<td>' + record.imaging_findings + '</td>' +
                                            '</tr>'
                                        ).join('') +
                                    '</tbody>' +
                                '</table>' +
                            '</text>' +
                        '</section>' +
                    '</component>' +
                '</structuredBody>' +
            '</component>' +
            '</ClinicalDocument>';
        
        return xmlContent;
    }

    // Initialiser la gestion de l'imagerie
    initializeImagingManagement();
    
    // Dental Imaging Management
    let dentalImagingRecords = [];
    
    function initializeDentalImagingManagement() {
        // Dental file upload handling
        const dentalFileUploadArea = document.getElementById('dental-file-upload-area');
        const dentalImagingFile = document.getElementById('dental_imaging_file');
        const dentalFilePreview = document.getElementById('dental-file-preview');
        const dentalFileName = document.getElementById('dental-file-name');
        const removeDentalFile = document.getElementById('remove-dental-file');

        if (dentalFileUploadArea && dentalImagingFile) {
            dentalFileUploadArea.addEventListener('click', function() {
                dentalImagingFile.click();
            });

            dentalImagingFile.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 10 * 1024 * 1024) { // 10MB limit
                        alert(HRC_LABELS.fileTooLarge);
                        return;
                    }
                    
                    dentalFileName.textContent = file.name;
                    dentalFilePreview.classList.remove('hidden');
                    dentalFileUploadArea.classList.add('hidden');
                }
            });

            if (removeDentalFile) {
                removeDentalFile.addEventListener('click', function() {
                    dentalImagingFile.value = '';
                    dentalFilePreview.classList.add('hidden');
                    dentalFileUploadArea.classList.remove('hidden');
                });
            }
        }

        // Add dental imaging button
        const addDentalImagingBtn = document.getElementById('add-dental-imaging-btn');
        if (addDentalImagingBtn) {
            addDentalImagingBtn.addEventListener('click', function() {
                saveDentalImagingRecord();
            });
        }

        // Analyze dental imaging button
        const analyzeDentalImagingBtn = document.getElementById('analyze-dental-imaging-btn');
        if (analyzeDentalImagingBtn) {
            analyzeDentalImagingBtn.addEventListener('click', function() {
                analyzeDentalImagingWithAI();
            });
        }
    }

    function saveDentalImagingRecord() {
        const formData = {
            dental_imaging_type: document.getElementById('dental_imaging_type').value,
            dental_imaging_date: document.getElementById('dental_imaging_date').value,
            dental_imaging_notes: document.getElementById('dental_imaging_notes').value,
            id: Date.now()
        };
        
        if (!formData.dental_imaging_type || !formData.dental_imaging_date) {
            alert(HRC_LABELS.fillRequiredFields);
            return;
        }
        
        dentalImagingRecords.push(formData);
        updateDentalImagingList();
        
        // Clear form
        document.getElementById('dental_imaging_type').value = '';
        document.getElementById('dental_imaging_date').value = '{{ date("Y-m-d") }}';
        document.getElementById('dental_imaging_notes').value = '';
        
        // Clear file upload
        const dentalImagingFile = document.getElementById('dental_imaging_file');
        const dentalFilePreview = document.getElementById('dental-file-preview');
        const dentalFileUploadArea = document.getElementById('dental-file-upload-area');
        
        if (dentalImagingFile) dentalImagingFile.value = '';
        if (dentalFilePreview) dentalFilePreview.classList.add('hidden');
        if (dentalFileUploadArea) dentalFileUploadArea.classList.remove('hidden');
    }

    function updateDentalImagingList() {
        const list = document.getElementById('dental-imaging-list');
        if (!list) return;
        
        list.innerHTML = '';
        
        dentalImagingRecords.forEach((record, index) => {
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200';
            div.innerHTML = `
                <div class="flex-1">
                    <div class="font-medium text-gray-800">${getDentalImagingTypeDisplayName(record.dental_imaging_type)}</div>
                    <div class="text-sm text-gray-600">
                        Date: ${formatDate(record.dental_imaging_date)}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        ${record.dental_imaging_notes ? record.dental_imaging_notes.substring(0, 50) + '...' : HRC_LABELS.noNotes}
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="editDentalImagingRecord(${index})" class="text-blue-600 hover:text-blue-800 text-sm">✏️</button>
                    <button onclick="deleteDentalImagingRecord(${index})" class="text-red-600 hover:text-red-800 text-sm">🗑️</button>
                </div>
            `;
            list.appendChild(div);
        });
    }

    function getDentalImagingTypeDisplayName(type) {
        return HRC_LABELS.dentalImagingTypes[type] || type;
    }

    // NOTE (audit factice -> reel, 2026-09) : meme constat que
    // analyzeImagingWithAI() ci-dessus : cette "analyse IA" cherchait des
    // mots-cles dans les notes saisies par le medecin lui-meme
    // ("carie"/"anormal"/"lesion"/"probleme") et renvoyait un pourcentage
    // de confiance invente (87%/94%). Aucun service d'IA n'est reellement
    // connecte.
    function analyzeDentalImagingWithAI() {
        const dentalImagingFile = document.getElementById('dental_imaging_file');
        const dentalImagingType = document.getElementById('dental_imaging_type').value;
        
        if (!dentalImagingFile.files[0] && !dentalImagingType) {
            alert(HRC_LABELS.uploadOrTypeRequired);
            return;
        }

        alert(HRC_LABELS.dentalAiNotAvailable);
    }

    // Initialiser la gestion de l'imagerie dentaire
    initializeDentalImagingManagement();
    
    // Initialisation des Viewers d'Images Médicales
    let dentalImageViewer = null;
    let medicalImageViewer = null;
    
    // Initialiser le viewer dentaire
    function initializeDentalImageViewer() {
        if (typeof MedicalImageViewer !== 'undefined') {
            dentalImageViewer = new MedicalImageViewer('dental-image-viewer');
    
        } else {
            console.error('MedicalImageViewer non disponible');
        }
    }
    
    // Initialiser le viewer médical
    function initializeMedicalImageViewer() {

        
        // Vérifier que le div existe
        const viewerDiv = document.getElementById('medical-image-viewer');
        if (!viewerDiv) {
            console.error('❌ Div medical-image-viewer non trouvé');
            return;
        }
        
        
        // Vérifier que la classe MedicalImageViewer est disponible
        if (typeof MedicalImageViewer === 'undefined') {
            console.error('❌ MedicalImageViewer non disponible');

            
            // Attendre un peu plus et réessayer
            setTimeout(() => {
                if (typeof MedicalImageViewer !== 'undefined') {
    
                    createMedicalImageViewer();
                } else {
                    console.error('❌ MedicalImageViewer toujours indisponible');
                    // Ajouter un message d'erreur dans le div
                    viewerDiv.innerHTML = `
                        <div class="p-4 text-center text-red-600">
                            <p>${HRC_LABELS.viewerNotAvailable}</p>
                            <p class="text-sm">${HRC_LABELS.viewerCheckScript}</p>
                        </div>
                    `;
                }
            }, 1000);
            return;
        }
        
        createMedicalImageViewer();
    }
    
    function createMedicalImageViewer() {
        try {
            medicalImageViewer = new MedicalImageViewer('medical-image-viewer');

            
            // Ajouter un message de succès temporaire
            const viewerDiv = document.getElementById('medical-image-viewer');
            if (viewerDiv) {
                const successMsg = document.createElement('div');
                successMsg.className = 'p-2 text-center text-green-600 text-sm';
                successMsg.innerHTML = HRC_LABELS.viewerReady;
                viewerDiv.appendChild(successMsg);
                
                // Supprimer le message après 3 secondes
                setTimeout(() => {
                    if (successMsg.parentNode) {
                        successMsg.parentNode.removeChild(successMsg);
                    }
                }, 3000);
            }
        } catch (error) {
            console.error('❌ Erreur lors de l\'initialisation du viewer:', error);
            
            // Ajouter un message d'erreur dans le div
            const viewerDiv = document.getElementById('medical-image-viewer');
            if (viewerDiv) {
                viewerDiv.innerHTML = `
                    <div class="p-4 text-center text-red-600">
                        <p>${HRC_LABELS.viewerInitError}</p>
                        <p class="text-sm">${error.message}</p>
                    </div>
                `;
            }
        }
    }
    
    // Gérer l'upload d'images dentaires
    function handleDentalImageUpload() {
        const fileInput = document.getElementById('dental-image-upload');
        const loadBtn = document.getElementById('load-dental-image-btn');
        
        if (fileInput && loadBtn) {
            loadBtn.addEventListener('click', () => {
                const file = fileInput.files[0];
                if (file && dentalImageViewer) {
                    dentalImageViewer.loadImage(file).then(() => {
            
                    }).catch(error => {
                        console.error('Erreur lors du chargement:', error);
                        alert(HRC_LABELS.loadImageError);
                    });
                } else {
                    alert(HRC_LABELS.selectFile);
                }
            });
        }
    }
    
    // Gérer l'upload d'images médicales
    function handleMedicalImageUpload() {

        
        const fileInput = document.getElementById('imaging_file');
        const analyzeBtn = document.getElementById('analyze-imaging-btn');
        
        if (!fileInput) {
            console.error('❌ Input file imaging_file non trouvé');
            return;
        }
        
        if (!analyzeBtn) {
            console.error('❌ Bouton analyze-imaging-btn non trouvé');
            return;
        }
        
        
        
        // Ajouter un bouton pour charger dans le viewer
        const loadInViewerBtn = document.createElement('button');
        loadInViewerBtn.type = 'button';
        loadInViewerBtn.className = 'px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm ml-2';
        loadInViewerBtn.innerHTML = HRC_LABELS.loadInViewerButton;
        loadInViewerBtn.onclick = () => {

            
            const file = fileInput.files[0];
            if (!file) {
                alert(HRC_LABELS.selectFile);
                return;
            }
            
            
            
            if (!medicalImageViewer) {
                console.error('❌ Viewer médical non initialisé');
                alert(HRC_LABELS.viewerNotAvailableReload);
                return;
            }
            
            
            
            medicalImageViewer.loadImage(file).then(() => {
                
                alert(HRC_LABELS.imageLoadedSuccess);
            }).catch(error => {
                console.error('❌ Erreur lors du chargement:', error);
                alert(HRC_LABELS.loadImageErrorColon + error.message);
            });
        };
        
        // Insérer le bouton après le bouton d'analyse
        analyzeBtn.parentNode.insertBefore(loadInViewerBtn, analyzeBtn.nextSibling);

    }
    
    // Initialiser les viewers après le chargement de la page
    setTimeout(() => {
        initializeDentalImageViewer();
        initializeMedicalImageViewer();
        handleDentalImageUpload();
        handleMedicalImageUpload();
    }, 1000);
});

// Fonctions globales pour les boutons d'édition et suppression
function editImagingRecord(index) {
    // Cette fonction sera appelée depuis les boutons dans la liste

}

function deleteImagingRecord(index) {
    // Cette fonction sera appelée depuis les boutons dans la liste

}

// Fonctions globales pour les boutons d'édition et suppression dentaire
function editDentalImagingRecord(index) {

}

function deleteDentalImagingRecord(index) {
    if (confirm(HRC_LABELS.confirmDeleteDentalImaging)) {
        dentalImagingRecords.splice(index, 1);
        updateDentalImagingList();
    }
}

// Watcher pour réinitialiser le viewer médical à chaque changement d'onglet
if (window.__VUE_APP__) {
    window.__VUE_APP__.watch(
        () => window.__VUE_APP__.activeTab,
        (newTab) => {
            if (newTab === 'medical-imaging') {
                setTimeout(() => {
                    if (typeof initializeMedicalImageViewer === 'function') {
                        initializeMedicalImageViewer();
                    }
                }, 300);
            }
        }
    );
}
</script>

@endsection
