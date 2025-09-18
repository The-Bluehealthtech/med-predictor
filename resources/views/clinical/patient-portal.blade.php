@extends('layouts.app')

@section('title', 'Portail Patient - Workflow Clinique')

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
                                    Portail Patient
                                </h1>
                                <p class="text-sm text-gray-600">Workflow Clinique FIT</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Connecté en tant que Patient</span>
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
        <!-- Patient Info Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informations Patient</h2>
            </div>
            <div class="p-6">
                @if($patient)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nom complet</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->full_name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Âge</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->age }} ans</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Genre</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($patient->gender) }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-4">Aucun profil patient trouvé</p>
                        <button onclick="createPatientProfile()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Créer mon profil patient
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Workflow Steps -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Step 1: Recherche et Préparation -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                    <h3 class="text-lg font-semibold text-blue-900">1. Recherche et Préparation</h3>
                    <p class="text-sm text-blue-700">Préparez-vous pour votre consultation</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <button onclick="openSymptomInput()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            📝 Saisir mes symptômes
                        </button>
                        <button onclick="getHealthInformation()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            🔍 Rechercher des informations
                        </button>
                        <button onclick="prepareQuestions()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            ❓ Préparer mes questions
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Consultation -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                    <h3 class="text-lg font-semibold text-green-900">2. Consultation</h3>
                    <p class="text-sm text-green-700">Suivez votre consultation médicale</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <button onclick="viewConsultationHistory()" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            📋 Historique des consultations
                        </button>
                        <button onclick="viewDiagnosis()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            🩺 Voir mes diagnostics
                        </button>
                        <button onclick="viewTreatmentPlan()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            💊 Plan de traitement
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Suivi et Éducation -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-purple-50">
                    <h3 class="text-lg font-semibold text-purple-900">3. Suivi et Éducation</h3>
                    <p class="text-sm text-purple-700">Continuez votre parcours de soins</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <button onclick="askFollowUpQuestions()" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            💬 Questions de suivi
                        </button>
                        <button onclick="getPatientEducation()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            📚 Éducation patient
                        </button>
                        <button onclick="prepareSecondOpinion()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            👨‍⚕️ Préparer une seconde opinion
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Activité Récente</h3>
            </div>
            <div class="p-6">
                <div id="recent-activity" class="space-y-4">
                    <!-- Activity items will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Symptom Input Modal -->
<div id="symptom-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Saisie des Symptômes</h3>
            </div>
            <div class="p-6">
                <form id="symptom-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Symptômes principaux</label>
                            <textarea id="symptoms" name="symptoms" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Décrivez vos symptômes en détail..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sévérité</label>
                            <select id="severity" name="severity" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="mild">Légère</option>
                                <option value="moderate">Modérée</option>
                                <option value="severe">Sévère</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Durée</label>
                            <input type="text" id="duration" name="duration" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Depuis combien de temps ?">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Informations supplémentaires</label>
                            <textarea id="additional-info" name="additional_info" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Autres informations pertinentes..."></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeSymptomModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Patient Portal JavaScript Functions
function createPatientProfile() {
    // Implementation for creating patient profile
    console.log('Creating patient profile...');
}

function openSymptomInput() {
    document.getElementById('symptom-modal').classList.remove('hidden');
}

function closeSymptomModal() {
    document.getElementById('symptom-modal').classList.add('hidden');
}

function getHealthInformation() {
    // Implementation for health information search
    console.log('Getting health information...');
}

function prepareQuestions() {
    // Implementation for preparing questions
    console.log('Preparing questions...');
}

function viewConsultationHistory() {
    // Implementation for viewing consultation history
    console.log('Viewing consultation history...');
}

function viewDiagnosis() {
    // Implementation for viewing diagnosis
    console.log('Viewing diagnosis...');
}

function viewTreatmentPlan() {
    // Implementation for viewing treatment plan
    console.log('Viewing treatment plan...');
}

function askFollowUpQuestions() {
    // Implementation for follow-up questions
    console.log('Asking follow-up questions...');
}

function getPatientEducation() {
    // Implementation for patient education
    console.log('Getting patient education...');
}

function prepareSecondOpinion() {
    // Implementation for second opinion preparation
    console.log('Preparing second opinion...');
}

// Form submission for symptoms
document.getElementById('symptom-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const symptoms = formData.get('symptoms').split(',').map(s => s.trim()).filter(s => s);
    
    const data = {
        patient_id: '{{ $patient->id ?? "" }}',
        symptoms: symptoms,
        severity: formData.get('severity'),
        duration: formData.get('duration'),
        additional_info: formData.get('additional_info')
    };
    
    fetch('/api/clinical/symptoms', {
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
            alert('Symptômes enregistrés avec succès !');
            closeSymptomModal();
            loadRecentActivity();
        } else {
            alert('Erreur lors de l\'enregistrement des symptômes');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'enregistrement des symptômes');
    });
});

// Load recent activity
function loadRecentActivity() {
    // Implementation for loading recent activity
    const activityContainer = document.getElementById('recent-activity');
    activityContainer.innerHTML = `
        <div class="text-center text-gray-500 py-4">
            Chargement de l'activité récente...
        </div>
    `;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadRecentActivity();
});
</script>
@endsection
