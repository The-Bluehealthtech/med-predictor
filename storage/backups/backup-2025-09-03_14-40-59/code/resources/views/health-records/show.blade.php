@extends('layouts.app')

@section('title', 'Dossier Médical - Med Predictor')

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

<style>
.tab-button {
    @apply px-4 py-2 text-sm font-medium border-b-2 border-transparent;
    transition: all 0.2s ease-in-out;
}

.tab-button:hover {
    @apply text-gray-700 border-gray-300;
}

.tab-button.active {
    @apply text-blue-600 border-blue-600;
}

.tab-content {
    @apply space-y-6;
}
</style>

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🏥 Dossier Médical</h1>
                <p class="text-gray-600 mt-2">
                    {{ $healthRecord->player ? $healthRecord->player->full_name : 'Patient anonyme' }}
                    - {{ $healthRecord->record_date->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('health-records.edit', $healthRecord) }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    ✏️ Modifier
                </a>
                <a href="{{ route('health-records.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                    ← Retour
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
                        📋 Général
                    </button>
                    <button onclick="showTab('vitals')" class="tab-button">
                        💓 Signes Vitaux
                    </button>
                    <button onclick="showTab('medical')" class="tab-button">
                        🏥 Médical
                    </button>
                    <button onclick="showTab('pcma')" class="tab-button">
                        📊 PCMA
                    </button>
                    <button onclick="showTab('dental')" class="tab-button">
                        🦷 Dentaire
                    </button>
                    <button onclick="showTab('codes')" class="tab-button">
                        🏷️ Codes
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
                        <h2 class="text-xl font-semibold text-gray-800">👤 Informations du Patient</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nom complet</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $healthRecord->player ? $healthRecord->player->full_name : 'Non spécifié' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nationalité</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $healthRecord->player ? $healthRecord->player->nationality : 'Non spécifié' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date d'enregistrement</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $healthRecord->record_date->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Prochaine visite</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $healthRecord->next_checkup_date ? $healthRecord->next_checkup_date->format('d/m/Y') : 'Non planifiée' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de la Visite -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">📋 Informations de la Visite</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date de Visite</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $healthRecord->visit_date ? $healthRecord->visit_date->format('d/m/Y') : 'Non spécifiée' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Médecin</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $healthRecord->doctor_name ?: 'Non spécifié' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Type de Visite</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @switch($healthRecord->visit_type)
                                        @case('consultation')
                                            Consultation
                                            @break
                                        @case('emergency')
                                            Urgence
                                            @break
                                        @case('follow_up')
                                            Suivi
                                            @break
                                        @case('pre_season')
                                            Pré-saison
                                            @break
                                        @case('post_match')
                                            Post-match
                                            @break
                                        @case('rehabilitation')
                                            Rééducation
                                            @break
                                        @default
                                            Non spécifié
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        
                        @if($healthRecord->chief_complaint)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Motif de Consultation</label>
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
                        <h2 class="text-xl font-semibold text-gray-800">💓 Signes Vitaux</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ $healthRecord->blood_pressure_systolic ?: 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">Pression systolique (mmHg)</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ $healthRecord->blood_pressure_diastolic ?: 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">Pression diastolique (mmHg)</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">
                                    {{ $healthRecord->heart_rate ?: 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">Rythme cardiaque (bpm)</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{ $healthRecord->temperature ? number_format($healthRecord->temperature, 1) : 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">Température (°C)</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ $healthRecord->weight ? number_format($healthRecord->weight, 1) : 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">Poids (kg)</div>
                            </div>
                            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                                <div class="text-2xl font-bold text-indigo-600">
                                    {{ $healthRecord->height ?: 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-600">Taille (cm)</div>
                            </div>
                        </div>
                        
                        @if($healthRecord->bmi)
                            <div class="mt-6 text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-gray-800">
                                    {{ number_format($healthRecord->bmi, 1) }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    IMC - {{ $healthRecord->bmi_category }}
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
                        <h2 class="text-xl font-semibold text-gray-800">🏥 Informations Médicales</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Groupe sanguin</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $healthRecord->blood_type ?: 'Non spécifié' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Statut</label>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $healthRecord->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($healthRecord->status === 'archived' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($healthRecord->status) }}
                                </span>
                            </div>
                        </div>

                        @if($healthRecord->allergies)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">Allergies</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($healthRecord->allergies as $allergy)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ $allergy }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($healthRecord->medications)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">Médicaments</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($healthRecord->medications as $medication)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $medication }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($healthRecord->diagnosis)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">Diagnostic</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $healthRecord->diagnosis }}</p>
                            </div>
                        @endif

                        @if($healthRecord->treatment_plan)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">Plan de traitement</label>
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
                                <h2 class="text-xl font-semibold text-gray-800">📊 PCMA - Évaluations Médicales Pré-Compétition</h2>
                                <a href="{{ route('pcma.create', ['player_id' => $healthRecord->player_id]) }}" 
                                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    ➕ Nouvelle PCMA
                                </a>
                            </div>
                    </div>
                    <div class="p-6">
                            @php
                                $pcmas = \App\Models\PCMA::where('player_id', $healthRecord->player_id)
                                    ->orderBy('assessment_date', 'desc')
                                    ->get();
                            @endphp
                            
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
                                                            Date: {{ $pcma->assessment_date ? $pcma->assessment_date->format('d/m/Y') : 'N/A' }} • 
                                                            Statut: {{ ucfirst($pcma->status) }}
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
                                                    <a href="{{ route('pcma.show', $pcma) }}" class="text-blue-600 hover:text-blue-800 text-sm">Voir Détails</a>
                                                    <a href="{{ route('pcma.edit', $pcma) }}" class="text-green-600 hover:text-green-800 text-sm">Modifier</a>
                                                    <a href="{{ route('pcma.pdf', $pcma) }}" class="text-purple-600 hover:text-purple-800 text-sm">PDF</a>
                                </div>
                                </div>
                                        </div>
                                    @endforeach
                                        </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="text-gray-400 text-6xl mb-4">📋</div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune PCMA trouvée</h3>
                                    <p class="text-gray-600 mb-4">Aucune évaluation médicale pré-compétition n'a été effectuée pour ce joueur.</p>
                                    <a href="{{ route('pcma.create', ['player_id' => $healthRecord->player_id]) }}" 
                                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                        ➕ Créer la première PCMA
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
                            <h2 class="text-xl font-semibold text-gray-800">🦷 Évaluation Dentaire</h2>
                    </div>
                        <div class="p-6">
                            <p class="text-gray-600">Les données dentaires seront affichées ici.</p>
                        </div>
                        </div>
                        </div>

                <!-- Codes Tab -->
                <div id="codes-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">🏷️ Codes Médicaux</h2>
                    </div>
                        <div class="p-6">
                            @if($healthRecord->icd_10_codes || $healthRecord->snomed_ct_codes || $healthRecord->loinc_codes)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @if($healthRecord->icd_10_codes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Codes ICD-10</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach($healthRecord->icd_10_codes as $code)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $code }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($healthRecord->snomed_ct_codes)
                        <div>
                                        <label class="block text-sm font-medium text-gray-700">Codes SNOMED-CT</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach($healthRecord->snomed_ct_codes as $code)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $code }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($healthRecord->loinc_codes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Codes LOINC</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach($healthRecord->loinc_codes as $code)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $code }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                            @else
                                <p class="text-gray-600">Aucun code médical enregistré.</p>
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
                        <h3 class="text-lg font-semibold text-gray-800">⚠️ Score de Risque</h3>
                    </div>
                    <div class="p-6">
                        @if($healthRecord->risk_score)
                            <div class="text-center">
                                <div class="text-3xl font-bold text-{{ $healthRecord->risk_score > 0.7 ? 'red' : ($healthRecord->risk_score > 0.4 ? 'yellow' : 'green') }}-600">
                                    {{ number_format($healthRecord->risk_score * 100, 0) }}%
                                </div>
                                <div class="text-sm text-gray-600 mt-2">
                                    Niveau de risque: {{ $healthRecord->risk_level }}
                                </div>
                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $healthRecord->risk_score > 0.7 ? 'red' : ($healthRecord->risk_score > 0.4 ? 'yellow' : 'green') }}-500 h-2 rounded-full" 
                                             style="width: {{ $healthRecord->risk_score * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-center">Score non calculé</p>
                        @endif
                    </div>
                </div>

                <!-- Prédictions -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">🔮 Prédictions</h3>
                    </div>
                    <div class="p-6">
                        @if($healthRecord->predictions->count() > 0)
                            <div class="space-y-4">
                                @foreach($healthRecord->predictions->take(3) as $prediction)
                                    <div class="border-l-4 border-blue-500 pl-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $prediction->predicted_condition }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $prediction->prediction_date->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-600 mt-1">
                                            Confiance: {{ number_format($prediction->confidence_score * 100, 0) }}%
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($healthRecord->predictions->count() > 3)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('medical-predictions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        Voir toutes les prédictions ({{ $healthRecord->predictions->count() }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500 text-center">Aucune prédiction</p>
                        @endif
                        
                        <div class="mt-4">
                            <button onclick="generatePrediction()" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                🔮 Générer une prédiction
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">⚡ Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('health-records.edit', $healthRecord) }}" 
                           class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            ✏️ Modifier le dossier
                        </a>

                        <a href="{{ route('medical-predictions.create') }}" 
                           class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            🔮 Nouvelle prédiction
                        </a>
                        <form action="{{ route('health-records.destroy', $healthRecord) }}" method="POST" class="inline w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier ?')">
                                🗑️ Supprimer
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
            alert('Erreur lors de la génération de la prédiction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la génération de la prédiction');
    });
}
</script>
@endsection 