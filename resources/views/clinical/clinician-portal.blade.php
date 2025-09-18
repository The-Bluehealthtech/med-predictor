@extends('layouts.app')

@section('title', 'Portail Clinicien - Workflow Clinique')

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
                                    Portail Clinicien
                                </h1>
                                <p class="text-sm text-gray-600">Workflow Clinique avec IA</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Connecté en tant que Clinicien</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                            Déconnexion
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
                        <p class="text-sm font-medium text-gray-500">Patients Actifs</p>
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
                        <p class="text-sm font-medium text-gray-500">Consultations Aujourd'hui</p>
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
                        <p class="text-sm font-medium text-gray-500">PCMA En Attente</p>
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
                        <p class="text-sm font-medium text-gray-500">Dossiers Médicaux</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_health_records'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div id="consultation-choice-section" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="text-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">🩺 Nouvelle Consultation</h3>
                <p class="text-sm text-gray-600">Choisissez le type de consultation médicale</p>
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
                                <h4 class="text-lg font-semibold text-gray-900 group-hover:text-red-700">Nouveau Dossier Médical</h4>
                                <p class="text-sm text-gray-600 mt-1">Créer un nouveau dossier médical pour un patient</p>
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
                            Consultations générales
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
                                <p class="text-sm text-gray-600 mt-1">Évaluations médicales pré-compétition</p>
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
                            Contrôle médical
                        </span>
                    </div>
                </a>
            </div>
            
            <!-- Indicateur de choix -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">Cliquez sur l'option souhaitée pour commencer la consultation</p>
            </div>
        </div>

        <!-- Workflow Steps -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Step 1: Consultation Initiale -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                    <h3 class="text-lg font-semibold text-blue-900">1. Consultation Initiale</h3>
                    <p class="text-sm text-blue-700">Entrée des données patient</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <button onclick="showConsultationChoice()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            🩺 Nouvelle consultation
                        </button>
                        <button onclick="showPatientList()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            👥 Liste des patients
                        </button>
                        <button onclick="generateSummary()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            📝 Résumé automatique
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Revue Clinique -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                    <h3 class="text-lg font-semibold text-green-900">2. Revue Clinique</h3>
                    <p class="text-sm text-green-700">Analyse et diagnostic</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <button onclick="clinicalReview()" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            🔍 Revue clinique
                        </button>
                        <button onclick="evidenceResearch()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            📚 Recherche de preuves
                        </button>
                        <button onclick="clinicalDecisionSupport()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            🧠 Support décisionnel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Plan de Traitement -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-purple-50">
                    <h3 class="text-lg font-semibold text-purple-900">3. Plan de Traitement</h3>
                    <p class="text-sm text-purple-700">Traitement et suivi</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <button onclick="createTreatmentPlan()" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            💊 Plan de traitement
                        </button>
                        <button onclick="findClinicalTrials()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            🧪 Essais cliniques
                        </button>
                        <button onclick="generateReferral()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            📋 Générer référent
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patients List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Rendez-vous à Venir</h3>
                    <button onclick="refreshPatientList()" class="text-sm text-blue-600 hover:text-blue-800">
                        Actualiser
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Âge</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière Consultation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $appointment->athlete->dob ? \Carbon\Carbon::parse($appointment->athlete->dob)->age : 'N/A' }} ans</td>
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
                                    <i class="fas fa-user-md"></i> Consulter
                                </button>
                                <button onclick="viewPatientInfo({{ $appointment->athlete_id }})" 
                                        class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-eye"></i> Voir
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Aucun rendez-vous à venir trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($upcomingAppointments->count() > 10)
            <div class="px-6 py-4 border-t border-gray-200 text-center">
                <a href="{{ route('secretary.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Voir tous les rendez-vous →
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
                <h3 class="text-lg font-medium text-gray-900">Choisir le type de consultation</h3>
                <button onclick="closePatientModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-2">Patient sélectionné :</h4>
                <p class="text-sm text-gray-600"><strong>Nom :</strong> <span id="modalPatientName"></span></p>
                <p class="text-sm text-gray-600"><strong>Date de naissance :</strong> <span id="modalPatientDob"></span></p>
                <p class="text-sm text-gray-600"><strong>FIFA ID :</strong> <span id="modalPatientFifaId"></span></p>
                <p class="text-sm text-gray-600"><strong>Type de RDV :</strong> <span id="modalAppointmentType"></span></p>
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
                            <h4 class="text-lg font-semibold text-gray-900">Module Medical</h4>
                            <p class="text-sm text-gray-600">Consultations générales et dossiers de santé</p>
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
                            <p class="text-sm text-gray-600">Évaluations médicales pré-compétition</p>
                        </div>
                    </div>
                </button>
            </div>
            
            <div class="mt-4 text-center">
                <button onclick="closePatientModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Annuler
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
                <h3 class="text-xl font-semibold text-gray-900">🩺 Nouvelle Consultation</h3>
                <p class="text-sm text-gray-600 mt-1">Choisissez le type de consultation médicale</p>
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
                                    <h4 class="text-lg font-semibold text-gray-900 group-hover:text-red-700">Module Medical</h4>
                                    <p class="text-sm text-gray-600 mt-1">Consultations générales et dossiers de santé</p>
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
                                Consultations générales
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
                                    <p class="text-sm text-gray-600 mt-1">Évaluations médicales pré-compétition</p>
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
                                Contrôle médical
                            </span>
                        </div>
                    </a>
                </div>
                
                <!-- Indicateur de choix -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">Cliquez sur l'option souhaitée pour commencer la consultation</p>
                </div>
                
                <!-- Bouton Annuler -->
                <div class="mt-6 text-center">
                    <button onclick="closeConsultationChoiceModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Annuler
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
                <h3 class="text-xl font-semibold text-gray-900">👥 Liste des Patients</h3>
                <p class="text-sm text-gray-600 mt-1">Sélectionnez un patient pour commencer la consultation</p>
            </div>
            <div class="p-6">
                <!-- Filtres -->
                <div class="mb-6 flex flex-wrap gap-4">
                    <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="Planifié">Planifié</option>
                        <option value="Confirmé">Confirmé</option>
                        <option value="En cours">En cours</option>
                        <option value="Terminé">Terminé</option>
                    </select>
                    <select id="type-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les types</option>
                        <option value="consultation">Consultation</option>
                        <option value="emergency">Urgence</option>
                        <option value="follow_up">Suivi</option>
                        <option value="pre_season">Pré-saison</option>
                        <option value="post_match">Post-match</option>
                        <option value="rehabilitation">Rééducation</option>
                        <option value="routine_checkup">Contrôle de routine</option>
                        <option value="injury_assessment">Évaluation de blessure</option>
                        <option value="cardiac_evaluation">Évaluation cardiaque</option>
                        <option value="concussion_assessment">Évaluation commotion</option>
                    </select>
                    <input type="date" id="date-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Filtrer par date">
                </div>

                <!-- Liste des patients -->
                <div id="patient-list-container" class="space-y-4">
                    <!-- Les patients seront chargés ici via JavaScript -->
                </div>

                <!-- Bouton Fermer -->
                <div class="mt-6 text-center">
                    <button onclick="closePatientListModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Clinician Portal JavaScript Functions
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
    container.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2 text-gray-600">Chargement des patients...</p></div>';

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
                    demoMessage.innerHTML = '<p class="text-sm text-yellow-800"><strong>Mode démonstration:</strong> Données d\'exemple affichées car la base de données n\'est pas accessible.</p>';
                    container.parentNode.insertBefore(demoMessage, container);
                }
            } else {
                container.innerHTML = `<div class="text-center py-8 text-red-600">Erreur: ${data.error || 'Erreur inconnue'}</div>`;
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
                    fifa_connect_id: 'FIFA123456',
                    nationality: 'Tunisienne',
                    position: 'Attaquant',
                    appointment_date: new Date(Date.now() + 24*60*60*1000).toISOString(),
                    appointment_type: 'consultation',
                    status: 'Confirmé',
                    reason: 'Contrôle de routine'
                },
                {
                    id: 2,
                    player_id: 2,
                    first_name: 'Fatma',
                    last_name: 'Trabelsi',
                    date_of_birth: '1998-07-22',
                    fifa_connect_id: 'FIFA789012',
                    nationality: 'Tunisienne',
                    position: 'Milieu',
                    appointment_date: new Date(Date.now() + 2*24*60*60*1000).toISOString(),
                    appointment_type: 'pre_season',
                    status: 'Planifié',
                    reason: 'Évaluation pré-saison'
                }
            ];
            
            container.innerHTML = '<div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg"><p class="text-sm text-yellow-800"><strong>Mode démonstration:</strong> Données d\'exemple affichées car l\'API n\'est pas accessible.</p></div>';
            displayPatientList(demoPatients);
        });
}

function displayPatientList(patients) {
    const container = document.getElementById('patient-list-container');
    
    if (patients.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">Aucun patient trouvé</div>';
        return;
    }

    let html = '';
    patients.forEach(patient => {
        const appointmentDate = new Date(patient.appointment_date).toLocaleDateString('fr-FR');
        const appointmentTime = new Date(patient.appointment_date).toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
        
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
                                <p><strong>ID FIFA:</strong> ${patient.fifa_connect_id || 'N/A'}</p>
                                <p><strong>Date de naissance:</strong> ${patient.date_of_birth ? new Date(patient.date_of_birth).toLocaleDateString('fr-FR') : 'N/A'}</p>
                                <p><strong>RDV:</strong> ${appointmentDate} à ${appointmentTime}</p>
                                <p><strong>Type:</strong> ${getAppointmentTypeLabel(patient.appointment_type)}</p>
                                <p><strong>Statut:</strong> 
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(patient.status)}">
                                        ${patient.status}
                                    </span>
                                </p>
                                ${patient.reason ? `<p><strong>Motif:</strong> ${patient.reason}</p>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="selectPatient(${patient.player_id}, '${patient.first_name}', '${patient.last_name}', '${patient.fifa_connect_id}', '${patient.date_of_birth}', '${patient.appointment_type}', '${patient.status}')" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            📋 Sélectionner
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
        'consultation': 'Consultation',
        'emergency': 'Urgence',
        'follow_up': 'Suivi',
        'pre_season': 'Pré-saison',
        'post_match': 'Post-match',
        'rehabilitation': 'Rééducation',
        'routine_checkup': 'Contrôle de routine',
        'injury_assessment': 'Évaluation de blessure',
        'cardiac_evaluation': 'Évaluation cardiaque',
        'concussion_assessment': 'Évaluation commotion'
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
        titleElement.innerHTML = `🩺 Nouvelle Consultation - ${window.selectedPatient.first_name} ${window.selectedPatient.last_name}`;
        descriptionElement.textContent = 'Choisissez le type de dossier médical pour ce patient';
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
            alert('Consultation enregistrée avec succès !');
            closeConsultationModal();
            location.reload();
        } else {
            alert('Erreur lors de l\'enregistrement de la consultation');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'enregistrement de la consultation');
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
