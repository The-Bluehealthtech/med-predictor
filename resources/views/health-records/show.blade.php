@extends('layouts.app')

@section('title', __('health_records.show_page.page_title'))

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    function showTab(tabName) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tab buttons
        const tabButtons = document.querySelectorAll('.tab-button');
        tabButtons.forEach(button => {
            button.classList.remove('active');
        });
        
        // Show selected tab content
        const selectedTab = document.getElementById(tabName + '-tab');
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
        }
        
        // Add active class to selected tab button
        const selectedButton = document.querySelector('[onclick="showTab(\'' + tabName + '\')"]');
        if (selectedButton) {
            selectedButton.classList.add('active');
        }
    }
    
    // Make showTab function globally available
    window.showTab = showTab;
    
    // Show first tab by default
    showTab('general');
});
</script>
@endpush

@push('styles')
<style>
.health-record-page .tab-button {
    padding: .5rem 1rem;
    font-size: .875rem;
    font-weight: 500;
    border-bottom: 2px solid transparent;
    white-space: nowrap;
    transition: color 150ms ease, border-color 150ms ease;
}
.health-record-page .tab-button:hover {
    color: var(--fifa-gray-700);
    border-bottom-color: var(--fifa-gray-300);
}
.health-record-page .tab-button.active {
    color: var(--fifa-blue-secondary);
    border-bottom-color: var(--fifa-blue-secondary);
}
.health-record-page nav { overflow-x: auto; }
.health-record-page p { overflow-wrap: anywhere; }
</style>
@endpush

@section('content')
<div class="health-record-page container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-4 my-4">
        <a class="inline-block bg-blue-600 text-white rounded px-4 py-2" href="{{ route('medical-aut.create',$healthRecord->id) }}">{{ __('medical_aut.title') }} — {{ app()->getLocale()==='fr'?'Ouvrir le formulaire':'Open form' }}</a>
        <a class="ml-4" href="{{ route('medical-aut.index',$healthRecord->id) }}">{{ app()->getLocale()==='fr'?'Demandes existantes':'Existing applications' }}</a>
        <h2 class="font-bold mt-4">{{ __('medical_aut.icd_title') }}</h2>
        @forelse($healthRecord->icd11_diagnoses ?? [] as $entry)
            <p>{{ $entry['code'] ?? '—' }} — {{ $entry['label'] ?? '—' }} · {{ $entry['release'] ?? '—' }} ({{ $entry['language'] ?? '—' }})</p>
        @empty
            <p>—</p>
        @endforelse
    </div>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🏥 {{ __('health_records.show_page.heading') }}</h1>
                <p class="text-gray-600 mt-2">
                    {{ $healthRecord->player ? $healthRecord->player->full_name : __('healthcare.anonymous_patient') }}
                    - {{ $healthRecord->record_date?->format('d/m/Y') ?? __('healthcare.na') }}
                </p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('health-records.edit', $healthRecord) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    ✏️ {{ __('healthcare.edit') }}
                </a>
                <a href="{{ route('health-records.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                    ← {{ __('health_records.show_page.back_button') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6">
                    <button onclick="showTab('general')" class="tab-button active">
                        📋 {{ __('health_records.show_page.tab_general') }}
                    </button>
                    <button onclick="showTab('vitals')" class="tab-button">
                        💓 {{ __('health_records.show_page.tab_vitals') }}
                    </button>
                    <button onclick="showTab('medical')" class="tab-button">
                        🏥 {{ __('health_records.show_page.tab_medical') }}
                    </button>
                    <button onclick="showTab('pcma')" class="tab-button">
                        📊 {{ __('health_records.show_page.tab_pcma') }}
                    </button>
                    <button onclick="showTab('dental')" class="tab-button">
                        🦷 {{ __('health_records.show_page.tab_dental') }}
                    </button>
                    <button onclick="showTab('codes')" class="tab-button">
                        🏷️ {{ __('health_records.show_page.tab_codes') }}
                    </button>
                </nav>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- General Tab -->
                <div id="general-tab" class="tab-content">
                    <div class="space-y-6">
                <!-- Informations du patient -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">👤 {{ __('health_records.show_page.patient_info_heading') }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.full_name_label') }}</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $healthRecord->player ? $healthRecord->player->full_name : __('health_records.show_page.not_specified') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.nationality_label') }}</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $healthRecord->player ? $healthRecord->player->nationality : __('health_records.show_page.not_specified') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.registration_date_label') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $healthRecord->record_date?->format('d/m/Y H:i') ?? __('healthcare.na') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.next_visit_label') }}</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $healthRecord->next_checkup_date ? $healthRecord->next_checkup_date->format('d/m/Y') : __('health_records.show_page.not_planned') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de la Visite -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">📋 {{ __('health_records.show_page.visit_info_heading') }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.visit_date_label') }}</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $healthRecord->visit_date ? $healthRecord->visit_date->format('d/m/Y') : __('health_records.show_page.not_specified_fem') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.doctor_label') }}</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $healthRecord->doctor_name ?: __('health_records.show_page.not_specified') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.visit_type_label') }}</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @switch($healthRecord->visit_type)
                                        @case('consultation')
                                            {{ __('health_records.show_page.visit_type_consultation') }}
                                            @break
                                        @case('emergency')
                                            {{ __('health_records.show_page.visit_type_emergency') }}
                                            @break
                                        @case('follow_up')
                                            {{ __('health_records.show_page.visit_type_follow_up') }}
                                            @break
                                        @case('pre_season')
                                            {{ __('health_records.show_page.visit_type_pre_season') }}
                                            @break
                                        @case('post_match')
                                            {{ __('health_records.show_page.visit_type_post_match') }}
                                            @break
                                        @case('rehabilitation')
                                            {{ __('health_records.show_page.visit_type_rehabilitation') }}
                                            @break
                                        @default
                                            {{ __('health_records.show_page.visit_type_default') }}
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        
                        @if($healthRecord->chief_complaint)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.chief_complaint_label') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $healthRecord->chief_complaint }}</p>
                        </div>
                        @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vitals Tab -->
                <div id="vitals-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">💓 {{ __('health_records.show_page.vitals_heading') }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ $healthRecord->blood_pressure_systolic ?: 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">{{ __('health_records.show_page.systolic_label') }}</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ $healthRecord->blood_pressure_diastolic ?: 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">{{ __('health_records.show_page.diastolic_label') }}</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">
                                    {{ $healthRecord->heart_rate ?: 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">{{ __('health_records.show_page.heart_rate_label') }}</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{ $healthRecord->temperature ? number_format($healthRecord->temperature, 1) : 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">{{ __('health_records.show_page.temperature_label') }}</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ $healthRecord->weight ? number_format($healthRecord->weight, 1) : 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">{{ __('health_records.show_page.weight_label') }}</div>
                            </div>
                            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                                <div class="text-2xl font-bold text-indigo-600">
                                    {{ $healthRecord->height ?: 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">{{ __('health_records.show_page.height_label') }}</div>
                            </div>
                        </div>
                        
                        @if($healthRecord->bmi)
                            <div class="mt-6 text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-gray-800">
                                    {{ number_format($healthRecord->bmi, 1) }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ __('health_records.show_page.bmi_prefix') }} - {{ $healthRecord->bmi_category }}
                                </div>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>

                <!-- Medical Tab -->
                <div id="medical-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">🏥 {{ __('health_records.show_page.medical_info_heading') }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.blood_type_label') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $healthRecord->blood_type ?: __('health_records.show_page.not_specified') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('healthcare.status') }}</label>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $healthRecord->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($healthRecord->status === 'archived' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ __('healthcare.status_' . $healthRecord->status) }}
                                </span>
                            </div>
                        </div>

                        @if($healthRecord->allergies)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.allergies_label') }}</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach(is_array($healthRecord->allergies) ? $healthRecord->allergies : [$healthRecord->allergies] as $allergy)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ is_array($allergy) ? json_encode($allergy, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE) : ($allergy ?? '—') }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($healthRecord->medications)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.medications_label') }}</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach(is_array($healthRecord->medications) ? $healthRecord->medications : [$healthRecord->medications] as $medication)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ is_array($medication) ? json_encode(array_diff_key($medication,['antidoping'=>true]), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE) : ($medication ?? '—') }}
                                        </span>
                                        @if(is_array($medication) && ($medication['source']??null)==='RxNorm')
                                            @include('health-records.medication-antidoping')
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($healthRecord->diagnosis)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.diagnosis_label') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $healthRecord->diagnosis }}</p>
                            </div>
                        @endif

                        @if($healthRecord->treatment_plan)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.treatment_plan_label') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $healthRecord->treatment_plan }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                    </div>

                <!-- PCMA Tab -->
                <div id="pcma-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-800">📊 {{ __('health_records.show_page.pcma_heading') }}</h2>
                                <a href="{{ route('pcma.create', ['player_id' => $healthRecord->player_id]) }}"
                                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    ➕ {{ __('health_records.show_page.new_pcma_button') }}
                                </a>
                            </div>
                    </div>
                    <div class="p-6">
                            @php($pcmas = $pcmaRecords)
                            
                            @if($pcmas->count() > 0)
                        <div class="space-y-4">
                                    @foreach($pcmas as $pcma)
                            <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                        <span class="text-green-600">📋</span>
                                                    </div>
                                    <div>
                                                        <h5 class="font-medium text-gray-900">PCMA #{{ $pcma->id }}</h5>
                                        <p class="text-sm text-gray-600">

                                                            {{ __('health_records.show_page.pcma_date_label') }} {{ $pcma->assessment_date ? $pcma->assessment_date->format('d/m/Y') : __('healthcare.na') }} •                                                            {{ __('health_records.show_page.pcma_status_label') }} {{ ucfirst($pcma->status) }}
                                        </p>
                                                        <div class="flex items-center space-x-4 mt-1">
                                                            <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $pcma->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                                   ($pcma->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($pcma->status) }}
                                        </span>
                                        @if($pcma->fifa_compliant)
                                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">FIFA Compliant</span>
                                        @endif
                                    </div>
                                </div>
                                </div>
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('pcma.show', $pcma) }}" class="text-blue-600 hover:text-blue-800 text-sm">{{ __('health_records.show_page.pcma_view_details') }}</a>
                                                    <a href="{{ route('pcma.edit', $pcma) }}" class="text-green-600 hover:text-green-800 text-sm">{{ __('healthcare.edit') }}</a>
                                                    <a href="{{ route('pcma.pdf', $pcma) }}" class="text-purple-600 hover:text-purple-800 text-sm">PDF</a>
                                </div>
                                </div>
                                        </div>
                                    @endforeach
                                        </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="text-gray-400 text-6xl mb-4">📋</div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('health_records.show_page.no_pcma_heading') }}</h3>
                                    <p class="text-gray-600 mb-4">{{ __('health_records.show_page.no_pcma_desc') }}</p>
                                    <a href="{{ route('pcma.create', ['player_id' => $healthRecord->player_id]) }}"
                                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                        ➕ {{ __('health_records.show_page.create_first_pcma') }}
                                    </a>
                </div>
                @endif
                    </div>
                        </div>
                        </div>

                <!-- Dental Tab -->
                <div id="dental-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">🦷 {{ __('health_records.show_page.dental_heading') }}</h2>
                    </div>
                        <div class="p-6">
                            <p class="text-gray-600">{{ __('health_records.show_page.dental_placeholder') }}</p>
                        </div>
                        </div>
                        </div>

                <!-- Codes Tab -->
                <div id="codes-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">🏷️ {{ __('health_records.show_page.codes_heading') }}</h2>
                    </div>
                        <div class="p-6">
                            @if($healthRecord->icd_10_codes || $healthRecord->snomed_ct_codes || $healthRecord->loinc_codes)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @if($healthRecord->icd_10_codes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.icd10_label') }}</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach(is_array($healthRecord->icd_10_codes) ? $healthRecord->icd_10_codes : [$healthRecord->icd_10_codes] as $code)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ is_array($code) ? json_encode($code, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE) : ($code ?? '—') }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($healthRecord->snomed_ct_codes)
                        <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.snomed_label') }}</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach(is_array($healthRecord->snomed_ct_codes) ? $healthRecord->snomed_ct_codes : [$healthRecord->snomed_ct_codes] as $code)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ is_array($code) ? json_encode($code, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE) : ($code ?? '—') }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($healthRecord->loinc_codes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('health_records.show_page.loinc_label') }}</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach(is_array($healthRecord->loinc_codes) ? $healthRecord->loinc_codes : [$healthRecord->loinc_codes] as $code)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ is_array($code) ? json_encode($code, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE) : ($code ?? '—') }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                            @else
                                <p class="text-gray-600">{{ __('health_records.show_page.no_medical_codes') }}</p>
                @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Score de risque -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">⚠️ {{ __('health_records.show_page.risk_score_heading') }}</h3>
                    </div>
                    <div class="p-6">
                        <p>{{ __('healthcare_repair.unvalidated') }}</p>
                    </div>
                </div>

                <!-- Prédictions -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">🔮 {{ __('health_records.show_page.predictions_heading') }}</h3>
                    </div>
                    <div class="p-6">
                        @if($healthRecord->predictions->count() > 0)
                            <div class="space-y-4">
                                @foreach($healthRecord->predictions->take(3) as $prediction)
                                    <div class="border-l-4 border-blue-500 pl-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ __('healthcare_repair.historical') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $prediction->prediction_date?->format('d/m/Y') ?? __('healthcare.na') }}
                                        </div>
                                        <div class="text-xs text-gray-600 mt-1">
                                            {{ __('healthcare_repair.unvalidated') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($healthRecord->predictions->count() > 3)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('healthcare.predictions') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        {{ __('health_records.show_page.view_all_predictions') }} ({{ $healthRecord->predictions->count() }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500 text-center">{{ __('health_records.show_page.no_prediction') }}</p>
                        @endif
                        
                        <div class="mt-4">
                            <button disabled title="{{ __('healthcare_repair.unvalidated') }}"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                🔮 {{ __('health_records.show_page.generate_prediction_button') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">⚡ {{ __('healthcare.actions') }}</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('health-records.edit', $healthRecord) }}"
                           class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            ✏️ {{ __('health_records.show_page.edit_record_button') }}
                        </a>

                        <a href="{{ route('healthcare.predictions') }}"
                           class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            🔮 {{ __('health_records.show_page.new_prediction_button') }}
                        </a>
                        <form action="{{ route('health-records.destroy', $healthRecord) }}" method="POST" class="inline w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                                    onclick="return confirm('{{ __('health_records.show_page.confirm_delete') }}')">
                                🗑️ {{ __('healthcare.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generatePrediction() {
    fetch('{{ route("health-records.generate-prediction", $healthRecord) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(@json(__('health_records.show_page.js_generate_prediction_error')));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(@json(__('health_records.show_page.js_generate_prediction_error')));
    });
}
</script>
@endsection 