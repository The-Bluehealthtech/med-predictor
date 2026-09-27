@extends('layouts.app')

@section('title', __('clinical.clinician_portal_page_title'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <img src="{{ asset('images/fit-logo.png') }}" alt="FIT Logo" class="w-10 h-10 mr-3">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    {{ __('clinical.clinician_portal_heading') }}
                                </h1>
                                <p class="text-sm text-gray-600">{{ __('clinical.clinician_portal_subtitle') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">{{ __('clinical.logged_in_as_clinician') }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                            {{ __('clinical.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Dashboard Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 text-sm font-bold">👥</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('clinical.stat_active_patients') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_patients'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 text-sm font-bold">📋</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('clinical.stat_consultations_today') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['consultations_today'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <span class="text-yellow-600 text-sm font-bold">⚠️</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('clinical.stat_pending_pcmas') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_pcmas'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 text-sm font-bold">🏥</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('clinical.stat_medical_records') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_health_records'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div id="consultation-choice-section" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="text-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">🩺 {{ __('clinical.new_consultation_heading') }}</h3>
                <p class="text-sm text-gray-600">{{ __('clinical.choose_consultation_type_medical') }}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Module Medical -->
                <a href="{{ route('create-health-record') }}" class="group relative overflow-hidden bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-200 rounded-xl p-6 hover:border-red-300 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-white text-xl">🏥</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900 group-hover:text-red-700">{{ __('clinical.new_medical_record') }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ __('clinical.create_new_medical_record_desc') }}</p>
                            </div>
                        </div>
                        <div class="text-red-500 group-hover:text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-red-600">
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-red-200 text-red-800">
                            {{ __('clinical.general_consultations') }}
                        </span>
                    </div>
                </a>
                
                <!-- PCMA -->
                <a href="{{ route('pcma.dashboard') }}" class="group relative overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-6 hover:border-blue-300 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <span class="text-white text-xl">📋</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900 group-hover:text-blue-700">PCMA</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ __('clinical.pre_competition_medical_evals') }}</p>
                            </div>
                        </div>
                        <div class="text-blue-500 group-hover:text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-blue-600">
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-200 text-blue-800">
                            {{ __('clinical.medical_control_badge') }}
                        </span>
                    </div>
                </a>
            </div>
            
            <!-- Indicateur de choix -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">{{ __('clinical.click_option_to_start_consultation') }}</p>
            </div>
        </div>

        <!-- Workflow Steps -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Step 1: Consultation Initiale -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                    <h3 class="text-lg font-semibold text-blue-900">{{ __('clinical.step1_initial_consultation') }}</h3>
                    <p class="text-sm text-blue-700">{{ __('clinical.step1_desc') }}</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <button onclick="showConsultationChoice()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            🩺 {{ __('clinical.new_consultation_button') }}
                        </button>
                        <button onclick="showPatientList()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            👥 {{ __('clinical.patient_list_button') }}
                        </button>
                        <button onclick="generateSummary()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            📝 {{ __('clinical.auto_summary') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Revue Clinique -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                    <h3 class="text-lg font-semibold text-green-900">{{ __('clinical.step2_clinical_review') }}</h3>
                    <p class="text-sm text-green-700">{{ __('clinical.step2_desc') }}</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <button onclick="clinicalReview()" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            🔍 {{ __('clinical.clinical_review_button') }}
                        </button>
                        <button onclick="evidenceResearch()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            📚 {{ __('clinical.evidence_research') }}
                        </button>
                        <button onclick="clinicalDecisionSupport()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            🧠 {{ __('clinical.decision_support') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Plan de Traitement -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-purple-50">
                    <h3 class="text-lg font-semibold text-purple-900">{{ __('clinical.step3_treatment_plan') }}</h3>
                    <p class="text-sm text-purple-700">{{ __('clinical.step3_desc') }}</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <button onclick="createTreatmentPlan()" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            💊 {{ __('clinical.treatment_plan_button') }}
                        </button>
                        <button onclick="findClinicalTrials()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            🧪 {{ __('clinical.clinical_trials') }}
                        </button>
                        <button onclick="generateReferral()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            📋 {{ __('clinical.generate_referral') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patients List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('clinical.upcoming_appointments') }}</h3>
                    <button onclick="refreshPatientList()" class="text-sm text-blue-600 hover:text-blue-800">
                        {{ __('clinical.refresh') }}
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('clinical.table_patient') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('clinical.table_age') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('clinical.table_last_consultation') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('clinical.table_status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('clinical.table_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($upcomingAppointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">{{ substr($appointment->athlete->name ?? 'N/A', 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $appointment->athlete->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $appointment->athlete_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $appointment->athlete->dob ? \Carbon\Carbon::parse($appointment->athlete->dob)->age : 'N/A' }} {{ __('clinical.years_old_suffix') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $appointment->appointment_date ? $appointment->appointment_date->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'scheduled') bg-yellow-100 text-yellow-800
                                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($appointment->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="openPatientModal({{ $appointment->athlete_id }}, '{{ $appointment->athlete->name ?? 'N/A' }}', '{{ $appointment->athlete->dob ?? 'N/A' }}', '{{ $appointment->athlete->fifa_id ?? 'N/A' }}', '{{ $appointment->type }}', '{{ $appointment->status }}')" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-user-md"></i> {{ __('clinical.consult_button') }}
                                </button>
                                <button onclick="viewPatientInfo({{ $appointment->athlete_id }})" 
                                        class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-eye"></i> {{ __('clinical.view_button') }}
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                {{ __('clinical.no_upcoming_appointments') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($upcomingAppointments->count() > 10)
            <div class="px-6 py-4 border-t border-gray-200 text-center">
                <a href="{{ route('secretary.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    {{ __('clinical.view_all_appointments') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Patient Selection Modal -->
<div id="patientModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">{{ __('clinical.choose_consultation_type_modal') }}</h3>
                <button onclick="closePatientModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-2">{{ __('clinical.selected_patient_label') }}</h4>
                <p class="text-sm text-gray-600"><strong>{{ __('clinical.name_label') }}</strong> <span id="modalPatientName"></span></p>
                <p class="text-sm text-gray-600"><strong>{{ __('clinical.dob_label') }}</strong> <span id="modalPatientDob"></span></p>
                <p class="text-sm text-gray-600"><strong>{{ __('clinical.fifa_id_label') }}</strong> <span id="modalPatientFifaId"></span></p>
                <p class="text-sm text-gray-600"><strong>{{ __('clinical.appointment_type_label') }}</strong> <span id="modalAppointmentType"></span></p>
            </div>
            
            <div class="grid grid-cols-1 gap-4">
                <button onclick="openMedicalRecord()" 
                        class="w-full flex items-center justify-center p-4 bg-red-50 border-2 border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-all duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xl">🏥</span>
                            </div>
                        </div>
                        <div class="ml-4 text-left">
                            <h4 class="text-lg font-semibold text-gray-900">{{ __('clinical.medical_module') }}</h4>
                            <p class="text-sm text-gray-600">{{ __('clinical.general_consultations_and_health_records') }}</p>
                        </div>
                    </div>
                </button>
                
                <button onclick="openPcmaRecord()" 
                        class="w-full flex items-center justify-center p-4 bg-blue-50 border-2 border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-all duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xl">📋</span>
                            </div>
                        </div>
                        <div class="ml-4 text-left">
                            <h4 class="text-lg font-semibold text-gray-900">PCMA</h4>
                            <p class="text-sm text-gray-600">{{ __('clinical.pre_competition_medical_evals') }}</p>
                        </div>
                    </div>
                </button>
            </div>
            
            <div class="mt-4 text-center">
                <button onclick="closePatientModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    {{ __('clinical.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Consultation Choice Modal -->
<div id="consultation-choice-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">🩺 {{ __('clinical.new_consultation_heading') }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ __('clinical.choose_consultation_type_medical') }}</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Module Medical -->
                    <a href="#" onclick="selectPatientForMedicalFromModal()" class="group relative overflow-hidden bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-200 rounded-xl p-6 hover:border-red-300 hover:shadow-lg transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                        <span class="text-white text-xl">🏥</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold text-gray-900 group-hover:text-red-700">{{ __('clinical.medical_module') }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ __('clinical.general_consultations_and_health_records') }}</p>
                                </div>
                            </div>
                            <div class="text-red-500 group-hover:text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-red-600">
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-red-200 text-red-800">
                                {{ __('clinical.general_consultations') }}
                            </span>
                        </div>
                    </a>
                    
                    <!-- PCMA -->
                    <a href="#" onclick="selectPatientForPCMAFromModal()" class="group relative overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-6 hover:border-blue-300 hover:shadow-lg transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                        <span class="text-white text-xl">📋</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold text-gray-900 group-hover:text-blue-700">PCMA</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ __('clinical.pre_competition_medical_evals') }}</p>
                                </div>
                            </div>
                            <div class="text-blue-500 group-hover:text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-blue-600">
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-200 text-blue-800">
                                {{ __('clinical.medical_control_badge') }}
                            </span>
                        </div>
                    </a>
                </div>
                
                <!-- Indicateur de choix -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">{{ __('clinical.click_option_to_start_consultation') }}</p>
                </div>
                
                <!-- Bouton Annuler -->
                <div class="mt-6 text-center">
                    <button onclick="closeConsultationChoiceModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        {{ __('clinical.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Patient List Modal -->
<div id="patient-list-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">👥 {{ __('clinical.patient_list_heading') }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ __('clinical.select_patient_to_start') }}</p>
            </div>
            <div class="p-6">
                <!-- Filtres -->
                <div class="mb-6 flex flex-wrap gap-4">
                    <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('clinical.all_statuses') }}</option>
                        <option value="Planifié">{{ __('clinical.status_scheduled') }}</option>
                        <option value="Confirmé">{{ __('clinical.status_confirmed') }}</option>
                        <option value="En cours">{{ __('clinical.status_in_progress') }}</option>
                        <option value="Terminé">{{ __('clinical.status_completed') }}</option>
                    </select>
                    <select id="type-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('clinical.all_types') }}</option>
                        <option value="consultation">{{ __('clinical.type_consultation') }}</option>
                        <option value="emergency">{{ __('clinical.type_emergency') }}</option>
                        <option value="follow_up">{{ __('clinical.type_follow_up') }}</option>
                        <option value="pre_season">{{ __('clinical.type_pre_season') }}</option>
                        <option value="post_match">{{ __('clinical.type_post_match') }}</option>
                        <option value="rehabilitation">{{ __('clinical.type_rehabilitation') }}</option>
                        <option value="routine_checkup">{{ __('clinical.type_routine_checkup') }}</option>
                        <option value="injury_assessment">{{ __('clinical.type_injury_assessment') }}</option>
                        <option value="cardiac_evaluation">{{ __('clinical.type_cardiac_evaluation') }}</option>
                        <option value="concussion_assessment">{{ __('clinical.type_concussion_assessment') }}</option>
                    </select>
                    <input type="date" id="date-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="{{ __('clinical.filter_by_date_placeholder') }}">
                </div>

                <!-- Liste des patients -->
                <div id="patient-list-container" class="space-y-4">
                    <!-- Les patients seront chargés ici via JavaScript -->
                </div>

                <!-- Bouton Fermer -->
                <div class="mt-6 text-center">
                    <button onclick="closePatientListModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        {{ __('clinical.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Clinician Portal JavaScript Functions
const PORTAL_LABELS = {
    loadingPatients: @json(__('clinical.loading_patients')),
    demoModeLabel: @json(__('clinical.demo_mode_label')),
    demoDataDbUnavailable: @json(__('clinical.demo_data_db_unavailable')),
    demoDataApiUnavailable: @json(__('clinical.demo_data_api_unavailable')),
    errorPrefix: @json(__('clinical.error_prefix')),
    unknownError: @json(__('clinical.unknown_error')),
    noPatientsFound: @json(__('clinical.no_patients_found')),
    demoNationalityTunisian: @json(__('clinical.demo_nationality_tunisian')),
    demoPositionForward: @json(__('clinical.demo_position_forward')),
    demoPositionMidfielder: @json(__('clinical.demo_position_midfielder')),
    demoReasonRoutineCheckup: @json(__('clinical.type_routine_checkup')),
    demoReasonPreseasonEval: @json(__('clinical.demo_reason_preseason_eval')),
    fifaIdLabelShort: @json(__('clinical.fifa_id_label_short')),
    dobLabel: @json(__('clinical.dob_label')),
    appointmentLabelShort: @json(__('clinical.appointment_label_short')),
    atTimeConnector: @json(__('clinical.at_time_connector')),
    typeLabel: @json(__('clinical.type_label')),
    statusLabel: @json(__('clinical.status_label')),
    reasonLabel: @json(__('clinical.reason_label')),
    selectButton: @json(__('clinical.select_button')),
    newConsultationHeading: @json(__('clinical.new_consultation_heading')),
    chooseMedicalRecordTypeForPatient: @json(__('clinical.choose_medical_record_type_for_patient')),
    consultationSavedSuccess: @json(__('clinical.consultation_saved_success')),
    consultationSaveError: @json(__('clinical.consultation_save_error')),
    typeConsultation: @json(__('clinical.type_consultation')),
    typeEmergency: @json(__('clinical.type_emergency')),
    typeFollowUp: @json(__('clinical.type_follow_up')),
    typePreSeason: @json(__('clinical.type_pre_season')),
    typePostMatch: @json(__('clinical.type_post_match')),
    typeRehabilitation: @json(__('clinical.type_rehabilitation')),
    typeRoutineCheckup: @json(__('clinical.type_routine_checkup')),
    typeInjuryAssessment: @json(__('clinical.type_injury_assessment')),
    typeCardiacEvaluation: @json(__('clinical.type_cardiac_evaluation')),
    typeConcussionAssessment: @json(__('clinical.type_concussion_assessment')),
};
function showConsultationChoice() {
    document.getElementById('consultation-choice-modal').classList.remove('hidden');
}

function closeConsultationChoiceModal() {
    document.getElementById('consultation-choice-modal').classList.add('hidden');
}

function scrollToConsultationChoice() {
    document.getElementById('consultation-choice-section').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
}

function startConsultation(patientId) {
    document.getElementById('patient-id').value = patientId;
    showConsultationChoice();
}

function viewPatient(patientId) {
    // Implementation for viewing patient details
    console.log('Viewing patient:', patientId);
}

function showPatientList() {
    document.getElementById('patient-list-modal').classList.remove('hidden');
    loadPatientList();
}

function closePatientListModal() {
    document.getElementById('patient-list-modal').classList.add('hidden');
}

function loadPatientList() {
    const container = document.getElementById('patient-list-container');
    container.innerHTML = `<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2 text-gray-600">${PORTAL_LABELS.loadingPatients}</p></div>`;

    // Récupérer les filtres
    const statusFilter = document.getElementById('status-filter').value;
    const typeFilter = document.getElementById('type-filter').value;
    const dateFilter = document.getElementById('date-filter').value;

    // Appel API pour récupérer les patients
    fetch(`/api/clinical/patients-test?status=${statusFilter}&type=${typeFilter}&date=${dateFilter}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayPatientList(data.patients);
                if (data.demo_mode) {
                    // Afficher un message si on est en mode démonstration
                    const demoMessage = document.createElement('div');
                    demoMessage.className = 'mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg';
                    demoMessage.innerHTML = `<p class="text-sm text-yellow-800"><strong>${PORTAL_LABELS.demoModeLabel}</strong> ${PORTAL_LABELS.demoDataDbUnavailable}</p>`;
                    container.parentNode.insertBefore(demoMessage, container);
                }
            } else {
                container.innerHTML = `<div class="text-center py-8 text-red-600">${PORTAL_LABELS.errorPrefix}${data.error || PORTAL_LABELS.unknownError}</div>`;
            }
        })
        .catch(error => {
            console.error('Error loading patients:', error);
            // Afficher des données de démonstration en cas d'erreur
            const demoPatients = [
                {
                    id: 1,
                    player_id: 1,
                    first_name: 'Ahmed',
                    last_name: 'Ben Ali',
                    date_of_birth: '1995-03-15',
                    fifa_connect_id: null,
                    nationality: PORTAL_LABELS.demoNationalityTunisian,
                    position: PORTAL_LABELS.demoPositionForward,
                    appointment_date: new Date(Date.now() + 24*60*60*1000).toISOString(),
                    appointment_type: 'consultation',
                    status: 'Confirmé',
                    reason: PORTAL_LABELS.demoReasonRoutineCheckup
                },
                {
                    id: 2,
                    player_id: 2,
                    first_name: 'Fatma',
                    last_name: 'Trabelsi',
                    date_of_birth: '1998-07-22',
                    fifa_connect_id: null,
                    nationality: PORTAL_LABELS.demoNationalityTunisian,
                    position: PORTAL_LABELS.demoPositionMidfielder,
                    appointment_date: new Date(Date.now() + 2*24*60*60*1000).toISOString(),
                    appointment_type: 'pre_season',
                    status: 'Planifié',
                    reason: PORTAL_LABELS.demoReasonPreseasonEval
                }
            ];
            
            container.innerHTML = `<div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg"><p class="text-sm text-yellow-800"><strong>${PORTAL_LABELS.demoModeLabel}</strong> ${PORTAL_LABELS.demoDataApiUnavailable}</p></div>`;
            displayPatientList(demoPatients);
        });
}

function displayPatientList(patients) {
    const container = document.getElementById('patient-list-container');
    
    if (patients.length === 0) {
        container.innerHTML = `<div class="text-center py-8 text-gray-500">${PORTAL_LABELS.noPatientsFound}</div>`;
        return;
    }

    let html = '';
    patients.forEach(patient => {
        const appointmentDate = new Date(patient.appointment_date).toLocaleDateString('en-GB');
        const appointmentTime = new Date(patient.appointment_date).toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'});
        
        html += `
            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 text-lg font-semibold">
                                    ${patient.name ? patient.name.substring(0, 2).toUpperCase() : 'NA'}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">
                                ${patient.name || 'N/A'}
                            </h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><strong>${PORTAL_LABELS.fifaIdLabelShort}</strong> ${patient.fifa_connect_id || 'N/A'}</p>
                                <p><strong>${PORTAL_LABELS.dobLabel}</strong> ${patient.date_of_birth ? new Date(patient.date_of_birth).toLocaleDateString('en-GB') : 'N/A'}</p>
                                <p><strong>${PORTAL_LABELS.appointmentLabelShort}</strong> ${appointmentDate} ${PORTAL_LABELS.atTimeConnector} ${appointmentTime}</p>
                                <p><strong>${PORTAL_LABELS.typeLabel}</strong> ${getAppointmentTypeLabel(patient.appointment_type)}</p>
                                <p><strong>${PORTAL_LABELS.statusLabel}</strong>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(patient.status)}">
                                        ${patient.status}
                                    </span>
                                </p>
                                ${patient.reason ? `<p><strong>${PORTAL_LABELS.reasonLabel}</strong> ${patient.reason}</p>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="selectPatient(${patient.player_id}, '${patient.first_name}', '${patient.last_name}', '${patient.fifa_connect_id}', '${patient.date_of_birth}', '${patient.appointment_type}', '${patient.status}')" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            📋 ${PORTAL_LABELS.selectButton}
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function getAppointmentTypeLabel(type) {
    const labels = {
        'consultation': PORTAL_LABELS.typeConsultation,
        'emergency': PORTAL_LABELS.typeEmergency,
        'follow_up': PORTAL_LABELS.typeFollowUp,
        'pre_season': PORTAL_LABELS.typePreSeason,
        'post_match': PORTAL_LABELS.typePostMatch,
        'rehabilitation': PORTAL_LABELS.typeRehabilitation,
        'routine_checkup': PORTAL_LABELS.typeRoutineCheckup,
        'injury_assessment': PORTAL_LABELS.typeInjuryAssessment,
        'cardiac_evaluation': PORTAL_LABELS.typeCardiacEvaluation,
        'concussion_assessment': PORTAL_LABELS.typeConcussionAssessment
    };
    return labels[type] || type;
}

function getStatusColor(status) {
    const colors = {
        'Planifié': 'bg-yellow-100 text-yellow-800',
        'Confirmé': 'bg-blue-100 text-blue-800',
        'En cours': 'bg-green-100 text-green-800',
        'Terminé': 'bg-gray-100 text-gray-800',
        'Annulé': 'bg-red-100 text-red-800',
        'No-show': 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

function selectPatient(playerId, firstName, lastName, fifaConnectId, dateOfBirth, appointmentType, status) {
    // Fermer le modal de liste des patients
    closePatientListModal();
    
    // Stocker les données du patient sélectionné
    window.selectedPatient = {
        player_id: playerId,
        first_name: firstName,
        last_name: lastName,
        fifa_connect_id: fifaConnectId,
        date_of_birth: dateOfBirth,
        appointment_type: appointmentType,
        status: status
    };
    
    // Ouvrir le modal de choix de type de dossier
    showConsultationChoiceForPatient();
}

function showConsultationChoiceForPatient() {
    // Modifier le titre du modal pour inclure le nom du patient
    const modal = document.getElementById('consultation-choice-modal');
    const titleElement = modal.querySelector('h3');
    const descriptionElement = modal.querySelector('p');
    
    if (window.selectedPatient) {
        titleElement.innerHTML = `🩺 ${PORTAL_LABELS.newConsultationHeading} - ${window.selectedPatient.first_name} ${window.selectedPatient.last_name}`;
        descriptionElement.textContent = PORTAL_LABELS.chooseMedicalRecordTypeForPatient;
    }
    
    // Ouvrir le modal
    modal.classList.remove('hidden');
}

function selectPatientForMedical(playerId, firstName, lastName, fifaConnectId, dateOfBirth) {
    const params = new URLSearchParams({
        patient_id: playerId,
        first_name: firstName,
        last_name: lastName,
        fifa_connect_id: fifaConnectId,
        date_of_birth: dateOfBirth,
        source: 'clinician_portal'
    });
    
    window.location.href = `/modules/medical?${params.toString()}`;
}

function selectPatientForPCMA(playerId, firstName, lastName, fifaConnectId, dateOfBirth) {
    const params = new URLSearchParams({
        patient_id: playerId,
        first_name: firstName,
        last_name: lastName,
        fifa_connect_id: fifaConnectId,
        date_of_birth: dateOfBirth,
        source: 'clinician_portal'
    });
    
    window.location.href = `/pcma/dashboard?${params.toString()}`;
}

function selectPatientForMedicalFromModal() {
    if (window.selectedPatient) {
        const params = new URLSearchParams({
            patient_id: window.selectedPatient.player_id,
            first_name: window.selectedPatient.first_name,
            last_name: window.selectedPatient.last_name,
            fifa_connect_id: window.selectedPatient.fifa_connect_id,
            date_of_birth: window.selectedPatient.date_of_birth,
            appointment_type: window.selectedPatient.appointment_type,
            status: window.selectedPatient.status,
            source: 'clinician_portal'
        });
        
        window.location.href = `/modules/medical?${params.toString()}`;
    }
}

function selectPatientForPCMAFromModal() {
    if (window.selectedPatient) {
        const params = new URLSearchParams({
            patient_id: window.selectedPatient.player_id,
            first_name: window.selectedPatient.first_name,
            last_name: window.selectedPatient.last_name,
            fifa_connect_id: window.selectedPatient.fifa_connect_id,
            date_of_birth: window.selectedPatient.date_of_birth,
            appointment_type: window.selectedPatient.appointment_type,
            status: window.selectedPatient.status,
            source: 'clinician_portal'
        });
        
        window.location.href = `/pcma/dashboard?${params.toString()}`;
    }
}

function generateSummary() {
    // Implementation for generating summary
    console.log('Generating summary...');
}

function clinicalReview() {
    // Implementation for clinical review
    console.log('Clinical review...');
}

function evidenceResearch() {
    // Implementation for evidence research
    console.log('Evidence research...');
}

function clinicalDecisionSupport() {
    // Implementation for clinical decision support
    console.log('Clinical decision support...');
}

function createTreatmentPlan() {
    // Implementation for creating treatment plan
    console.log('Creating treatment plan...');
}

function findClinicalTrials() {
    // Implementation for finding clinical trials
    console.log('Finding clinical trials...');
}

function generateReferral() {
    // Implementation for generating referral
    console.log('Generating referral...');
}

function refreshPatientList() {
    // Implementation for refreshing patient list
    location.reload();
}

// Form submission for consultation
document.getElementById('consultation-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        patient_id: formData.get('patient_id'),
        chief_complaint: formData.get('chief_complaint'),
        history_present_illness: formData.get('history_present_illness'),
        physical_exam: formData.get('physical_exam'),
        assessment: formData.get('assessment'),
        plan: formData.get('plan')
    };
    
    fetch('/api/clinical/consultations', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(PORTAL_LABELS.consultationSavedSuccess);
            closeConsultationModal();
            location.reload();
        } else {
            alert(PORTAL_LABELS.consultationSaveError);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(PORTAL_LABELS.consultationSaveError);
    });
});

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Clinician portal initialized');
});
</script>
@endsection

<script>
// Variables globales pour stocker les données du patient sélectionné
let selectedPatientData = {};

// Fonction pour ouvrir le modal de sélection de patient
function openPatientModal(athleteId, name, dob, fifaId, appointmentType, status) {
    // Stocker les données du patient
    selectedPatientData = {
        athleteId: athleteId,
        name: name,
        dob: dob,
        fifaId: fifaId,
        appointmentType: appointmentType,
        status: status
    };
    
    // Remplir le modal avec les informations du patient
    document.getElementById('modalPatientName').textContent = name;
    document.getElementById('modalPatientDob').textContent = dob;
    document.getElementById('modalPatientFifaId').textContent = fifaId;
    document.getElementById('modalAppointmentType').textContent = appointmentType;
    
    // Afficher le modal
    document.getElementById('patientModal').classList.remove('hidden');
}

// Fonction pour fermer le modal
function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
    selectedPatientData = {};
}

// Fonction pour ouvrir le dossier médical
function openMedicalRecord() {
    if (selectedPatientData.athleteId) {
        // Construire l'URL avec les paramètres du patient
        const params = new URLSearchParams({
            patient_id: selectedPatientData.athleteId,
            first_name: selectedPatientData.name.split(' ')[0] || '',
            last_name: selectedPatientData.name.split(' ').slice(1).join(' ') || '',
            fifa_connect_id: selectedPatientData.fifaId,
            date_of_birth: selectedPatientData.dob,
            appointment_type: selectedPatientData.appointmentType,
            status: selectedPatientData.status,
            source: 'clinician_portal'
        });
        
        // Rediriger vers le module médical avec les paramètres (utilise la vraie route)
        window.location.href = `/health-records/create?${params.toString()}`;
    }
}

// Fonction pour ouvrir le dossier PCMA
function openPcmaRecord() {
    if (selectedPatientData.athleteId) {
        // Construire l'URL avec les paramètres du patient
        const params = new URLSearchParams({
            patient_id: selectedPatientData.athleteId,
            first_name: selectedPatientData.name.split(' ')[0] || '',
            last_name: selectedPatientData.name.split(' ').slice(1).join(' ') || '',
            fifa_connect_id: selectedPatientData.fifaId,
            date_of_birth: selectedPatientData.dob,
            appointment_type: selectedPatientData.appointmentType,
            status: selectedPatientData.status,
            source: 'clinician_portal'
        });
        
        // Rediriger vers le module PCMA avec les paramètres (utilise la vraie route)
        window.location.href = `/pcma/create?${params.toString()}`;
    }
}

// Fonction pour voir les informations du patient (optionnel)
function viewPatientInfo(athleteId) {
    // Rediriger vers la page de profil du patient
    window.location.href = `/modules/medical/athlete/${athleteId}`;
}

// Fermer le modal en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    const modal = document.getElementById('patientModal');
    if (event.target === modal) {
        closePatientModal();
    }
});

// Fermer le modal avec la touche Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePatientModal();
    }
});
</script>
