@extends("layouts.app")

@section("title", "Nouveau Dossier Médical - Med Predictor")

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
            <h1 class="text-3xl font-bold text-gray-900">🏥 Nouvelle Visite Médicale</h1>
            <p class="text-gray-600 mt-2">Enregistrer une nouvelle visite médicale - les données seront ajoutées au dossier existant du joueur</p>
        </div>

        <form action="{{ route("health-records.store") }}" method="POST" class="space-y-8">
            @csrf

            <!-- Interface à Onglets -->
            <div id="health-records-tabs" class="bg-white rounded-lg shadow-lg">
                <!-- Barre de navigation des onglets -->
                <div class="tabs-nav">
                    <button 
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
                                <h3 class="text-lg font-semibold text-blue-900 mb-4">👤 Informations du Patient</h3>
                                
                                <!-- Player Selection -->
                                <div class="mb-6">
                                    <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Joueur *
                                    </label>
                                    <select 
                                        id="player_id" 
                                        name="player_id" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Sélectionner un joueur</option>
                                        @foreach($players ?? [] as $player)
                                            <option value="{{ $player->id }}" 
                                                {{ old('player_id') == $player->id || ($selectedPlayer && !isset($isDemo) && $selectedPlayer->id == $player->id) ? 'selected' : '' }}>
                                                {{ $player->full_name ?? $player->name }} ({{ $player->club->name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                        @if($selectedPlayer && isset($isDemo) && $isDemo)
                                            <option value="{{ $selectedPlayer->id }}" selected>
                                                {{ $selectedPlayer->full_name ?? $selectedPlayer->name }} ({{ $selectedPlayer->club['name'] ?? 'N/A' }}) - Mode Démo
                                            </option>
                                        @endif
                                    </select>
                                </div>

                                <!-- Player Information Display -->
                                @if($selectedPlayer)
                                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-blue-900 mb-2">
                                            👤 Informations du Patient Sélectionné
                                            @if(isset($isDemo) && $isDemo)
                                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded ml-2">Mode Démo</span>
                                            @endif
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium text-blue-800">Nom complet:</span>
                                                <span class="text-blue-900">{{ $selectedPlayer->full_name ?? $selectedPlayer->name }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">Date de naissance:</span>
                                                <span class="text-blue-900">
                                                    @if($selectedPlayer->date_of_birth)
                                                        {{ \Carbon\Carbon::parse($selectedPlayer->date_of_birth)->format('d/m/Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">Club:</span>
                                                <span class="text-blue-900">
                                                    @if(isset($isDemo) && $isDemo)
                                                        {{ $selectedPlayer->club['name'] ?? 'N/A' }}
                                                    @else
                                                        {{ $selectedPlayer->club->name ?? 'N/A' }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">Position:</span>
                                                <span class="text-blue-900">{{ $selectedPlayer->position ?? 'N/A' }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">Âge:</span>
                                                <span class="text-blue-900">{{ $selectedPlayer->age ?? 'N/A' }} ans</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">Nationalité:</span>
                                                <span class="text-blue-900">{{ $selectedPlayer->nationality ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div id="player-info" class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4" style="display: none;">
                                        <h4 class="text-sm font-semibold text-blue-900 mb-2">👤 Informations du Patient Sélectionné</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium text-blue-800">Nom complet:</span>
                                                <span id="player-full-name" class="text-blue-900"></span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">Date de naissance:</span>
                                                <span id="player-birthdate" class="text-blue-900"></span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">Club:</span>
                                                <span id="player-club" class="text-blue-900"></span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">Position:</span>
                                                <span id="player-position" class="text-blue-900"></span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">Âge:</span>
                                                <span id="player-age" class="text-blue-900"></span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-blue-800">Nationalité:</span>
                                                <span id="player-nationality" class="text-blue-900"></span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Visit Information -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="visit_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Date de Visite *
                                        </label>
                                        <input 
                                            type="date" 
                                            id="visit_date" 
                                            name="visit_date" 
                                            value="{{ old('visit_date', date('Y-m-d')) }}"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="doctor_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Médecin *
                                        </label>
                                        <input 
                                            type="text" 
                                            id="doctor_name" 
                                            name="doctor_name" 
                                            value="{{ old('doctor_name') }}"
                                            required
                                            placeholder="Nom du médecin"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="visit_type" class="block text-sm font-medium text-gray-700 mb-2">
                                            Type de Visite *
                                        </label>
                                        <select
                                            id="visit_type"
                                            name="visit_type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            required
                                        >
                                            <option value="">Sélectionner le type de visite</option>
                                            <option value="consultation" {{ old('visit_type') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                                            <option value="emergency" {{ old('visit_type') == 'emergency' ? 'selected' : '' }}>Urgence</option>
                                            <option value="follow_up" {{ old('visit_type') == 'follow_up' ? 'selected' : '' }}>Suivi</option>
                                            <option value="pre_season" {{ old('visit_type') == 'pre_season' ? 'selected' : '' }}>Pré-saison</option>
                                            <option value="pcma" {{ old('visit_type') == 'pcma' ? 'selected' : '' }}>PCMA (Évaluation Capacité Physique)</option>
                                            <option value="post_match" {{ old('visit_type') == 'post_match' ? 'selected' : '' }}>Post-match</option>
                                            <option value="rehabilitation" {{ old('visit_type') == 'rehabilitation' ? 'selected' : '' }}>Rééducation</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Chief Complaint -->
                                <div class="mt-6">
                                    <label for="chief_complaint" class="block text-sm font-medium text-gray-700 mb-2">
                                        Motif de Consultation
                                    </label>
                                    <textarea 
                                        id="chief_complaint" 
                                        name="chief_complaint" 
                                        rows="3"
                                        placeholder="Décrivez le motif principal de la consultation..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >{{ old('chief_complaint') }}</textarea>
                                </div>
                            </div>

                            <!-- Vital Signs -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-green-900 mb-4">💓 Signes Vitaux</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="blood_pressure_systolic" class="block text-sm font-medium text-gray-700 mb-2">
                                            Tension Systolique (mmHg)
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
                                            Tension Diastolique (mmHg)
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
                                            Fréquence Cardiaque (bpm)
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
                                <h3 class="text-lg font-semibold text-purple-900 mb-4">🤖 Assistant IA Médical</h3>
                                <p class="text-purple-700 mb-4">Décrivez les symptômes et observations cliniques pour une analyse automatique</p>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes Cliniques
                                        </label>
                                        <textarea 
                                            id="clinical_notes" 
                                            name="clinical_notes" 
                                            rows="6" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                            placeholder="Exemple: Patient se plaint de douleurs thoraciques depuis 2 jours, tension artérielle 140/90, fréquence cardiaque 85 bpm. Pas d'essoufflement ni de vertiges..."
                                        >{{ old('clinical_notes') }}</textarea>
                                    </div>
                                    
                                    <div class="flex space-x-4">
                                        <button 
                                            type="button" 
                                            id="ai-analyze-btn"
                                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center"
                                        >
                                            <span class="mr-2">🔍</span>
                                            Analyser avec l'IA
                                        </button>
                                        <button 
                                            type="button" 
                                            id="clear-notes-btn"
                                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                                        >
                                            Effacer
                                        </button>
                                    </div>
                                    
                                    <div id="ai-results" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Analyse IA</h4>
                                        <div id="ai-content" class="text-sm text-gray-700"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- ICD-11 Search -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-blue-900 mb-4">🔍 Recherche ICD-11</h3>
                                <p class="text-blue-700 mb-4">Recherchez des codes de diagnostic ICD-11 pour standardiser les diagnostics</p>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="icd11_search" class="block text-sm font-medium text-gray-700 mb-2">
                                            Rechercher un diagnostic
                                        </label>
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                id="icd11_search" 
                                                placeholder="Exemple: diabète, fracture, infection..."
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            >
                                            <div id="icd11_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                                        </div>
                                    </div>
                                    
                                    <div id="selected_icd11" class="hidden">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Diagnostic sélectionné:</label>
                                        <div class="flex items-center justify-between p-3 bg-blue-100 rounded-md">
                                            <div>
                                                <span id="selected_icd11_code" class="font-semibold text-blue-800"></span> - 
                                                <span id="selected_icd11_label" class="text-blue-700"></span>
                                            </div>
                                            <button type="button" onclick="clearICD11Selection()" class="text-blue-600 hover:text-blue-800">×</button>
                                        </div>
                                        <input type="hidden" name="icd11_diagnostic" id="icd11_diagnostic" value="{{ old('icd11_diagnostic') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 3: Catégories Médicales -->
                    <div v-show="activeTab === 'medical-categories'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-green-900 mb-4">🏥 Catégories Médicales (ICD-10, SNOMED CT, LOINC)</h3>
                                <p class="text-green-700 mb-4">Classification standardisée des diagnostics et procédures médicales</p>
                                
                                <!-- ICD-10 Diagnoses -->
                                <div class="space-y-4">
                                    <div>
                                        <label for="icd10_diagnoses" class="block text-sm font-medium text-gray-700 mb-2">
                                            🏷️ Diagnostics ICD-10
                                        </label>
                                        <select id="icd10_diagnoses" name="icd10_diagnoses[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" size="8">
                                            <optgroup label="🫀 Maladies Cardiovasculaires (I00-I99)">
                                                <option value="I10">I10 - Hypertension essentielle (primitive)</option>
                                                <option value="I21.9">I21.9 - Infarctus aigu du myocarde, sans précision</option>
                                                <option value="I50.9">I50.9 - Insuffisance cardiaque, sans précision</option>
                                                <option value="I63.9">I63.9 - Infarctus cérébral, sans précision</option>
                                            </optgroup>
                                            <optgroup label="🫁 Maladies Respiratoires (J00-J99)">
                                                <option value="J44.9">J44.9 - Bronchopneumopathie chronique obstructive, sans précision</option>
                                                <option value="J45.9">J45.9 - Asthme, sans précision</option>
                                                <option value="J18.9">J18.9 - Pneumonie, sans précision</option>
                                            </optgroup>
                                            <optgroup label="🦴 Maladies Musculo-squelettiques (M00-M99)">
                                                <option value="M79.3">M79.3 - Douleur dans les membres</option>
                                                <option value="M54.5">M54.5 - Lombalgie, sans précision</option>
                                                <option value="S93.4">S93.4 - Entorse de la cheville</option>
                                            </optgroup>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs diagnostics</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 4: Dossier Dentaire -->
                    <div v-show="activeTab === 'dental-record'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-yellow-900 mb-4">🦷 Dossier Dentaire</h3>
                                <p class="text-yellow-700 mb-4">Gestion complète des dossiers dentaires et des rendez-vous dentaires</p>
                                

                                
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-gray-800">Informations du Patient</h4>
                                    
                                    <!-- Player Information Display for Dental Tab -->
                                    @if($selectedPlayer)
                                        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-yellow-900 mb-2">
                                                👤 Informations du Patient Sélectionné
                                                @if(isset($isDemo) && $isDemo)
                                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded ml-2">Mode Démo</span>
                                                @endif
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <span class="font-medium text-yellow-800">Nom complet:</span>
                                                    <span class="text-yellow-900">{{ $selectedPlayer->full_name ?? $selectedPlayer->name }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">Date de naissance:</span>
                                                    <span class="text-yellow-900">
                                                        @if($selectedPlayer->date_of_birth)
                                                            {{ \Carbon\Carbon::parse($selectedPlayer->date_of_birth)->format('d/m/Y') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">Club:</span>
                                                    <span class="text-yellow-900">
                                                        @if(isset($isDemo) && $isDemo)
                                                            {{ $selectedPlayer->club['name'] ?? 'N/A' }}
                                                        @else
                                                            {{ $selectedPlayer->club->name ?? 'N/A' }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">Position:</span>
                                                    <span class="text-yellow-900">{{ $selectedPlayer->position ?? 'N/A' }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">Âge:</span>
                                                    <span class="text-yellow-900">{{ $selectedPlayer->age ?? 'N/A' }} ans</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">Nationalité:</span>
                                                    <span class="text-yellow-900">{{ $selectedPlayer->nationality ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div id="dental-player-info" class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4" style="display: none;">
                                            <h4 class="text-sm font-semibold text-yellow-900 mb-2">👤 Informations du Patient Sélectionné</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <span class="font-medium text-yellow-800">Nom complet:</span>
                                                    <span id="dental-player-full-name" class="text-yellow-900"></span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">Date de naissance:</span>
                                                    <span id="dental-player-birthdate" class="text-yellow-900"></span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">Club:</span>
                                                    <span id="dental-player-club" class="text-yellow-900"></span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">Position:</span>
                                                    <span id="dental-player-position" class="text-yellow-900"></span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">Âge:</span>
                                                    <span id="dental-player-age" class="text-yellow-900"></span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-yellow-800">Nationalité:</span>
                                                    <span id="dental-player-nationality" class="text-yellow-900"></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-6">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">Rendez-vous Dentaires</h4>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="dental_appointment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Date du Rendez-vous
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
                                                    Heure du Rendez-vous
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
                                                Motif du Rendez-vous
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
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">Notes Dentaires</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label for="dental_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                                Notes Générales
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
                                                Plan de Traitement
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
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">🦷 Diagramme Dentaire Interactif</h4>
                                    <p class="text-sm text-gray-600 mb-4">Sélectionnez l'état de chaque dent pour documenter l'état de la dentition</p>
                                    
                                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h5 class="text-sm font-medium text-gray-800">Diagramme Dentaire</h5>
                                            <div class="flex space-x-2">
                                                <button 
                                                    type="button" 
                                                    @click="forceInitializeDentalChart"
                                                    class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors"
                                                >
                                                    🔄 Recharger
                                                </button>
                                                <button 
                                                    type="button" 
                                                    @click="clearDentalSelection"
                                                    class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700 transition-colors"
                                                >
                                                    🗑️ Effacer
                                                </button>
                                                <button 
                                                    type="button" 
                                                    @click="saveDentalData"
                                                    class="px-3 py-1 bg-yellow-600 text-white rounded text-sm hover:bg-yellow-700 transition-colors"
                                                >
                                                    💾 Sauvegarder
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
                                                        <div>Chargement du diagramme dentaire...</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Dental Chart Info Panel -->
                                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Informations de la dent sélectionnée -->
                                            <div class="p-3 bg-blue-50 rounded-lg">
                                                <h6 class="text-sm font-medium text-blue-800 mb-2">Dent Sélectionnée</h6>
                                                <div v-if="selectedDentalTooth" class="text-sm">
                                                    <p><strong>Dent:</strong> @{{ selectedDentalTooth }}</p>
                                                    <p><strong>Type:</strong> @{{ getDentalToothType(selectedDentalTooth) }}</p>
                                                    <p><strong>Quadrant:</strong> @{{ getDentalQuadrant(selectedDentalTooth) }}</p>
                                                    <p><strong>État:</strong> 
                                                        <select v-model="dentalToothStatus" class="ml-2 px-2 py-1 border rounded text-xs">
                                                            <option value="healthy">Sain</option>
                                                            <option value="cavity">Carie</option>
                                                            <option value="filling">Obturation</option>
                                                            <option value="crown">Couronne</option>
                                                            <option value="missing">Manquante</option>
                                                            <option value="implant">Implant</option>
                                                            <option value="treatment">En traitement</option>
                                                        </select>
                                                    </p>
                                                    <textarea 
                                                        v-model="dentalToothNotes" 
                                                        placeholder="Notes sur cette dent..."
                                                        class="mt-2 w-full px-2 py-1 border rounded text-xs"
                                                        rows="2"
                                                    ></textarea>
                                                </div>
                                                <div v-else class="text-sm text-gray-500">
                                                    Cliquez sur une dent pour voir ses informations
                                                </div>
                                            </div>
                                            
                                            <!-- Résumé de l'État Dentaire -->
                                            <div class="p-3 bg-gray-50 rounded-lg">
                                                <h6 class="text-sm font-medium text-gray-800 mb-2">Résumé de l'État Dentaire</h6>
                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-green-100 border border-green-300 rounded mr-2"></div>
                                                        <span>Sain: <span id="healthy-count">@{{ dentalStats.healthy }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-red-500 border border-gray-300 rounded mr-2"></div>
                                                        <span>Carie: <span id="cavity-count">@{{ dentalStats.cavity }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-yellow-400 border border-gray-300 rounded mr-2"></div>
                                                        <span>Obturation: <span id="filling-count">@{{ dentalStats.filling }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-purple-500 border border-gray-300 rounded mr-2"></div>
                                                        <span>Couronne: <span id="crown-count">@{{ dentalStats.crown }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-gray-500 border border-gray-300 rounded mr-2"></div>
                                                        <span>Manquante: <span id="missing-count">@{{ dentalStats.missing }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-blue-500 border border-gray-300 rounded mr-2"></div>
                                                        <span>Implant: <span id="implant-count">@{{ dentalStats.implant }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-orange-500 border border-gray-300 rounded mr-2"></div>
                                                        <span>Traitement: <span id="treatment-count">@{{ dentalStats.treatment }}</span></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 bg-gray-100 border border-gray-300 rounded mr-2"></div>
                                                        <span>Non évalué: <span id="unevaluated-count">@{{ dentalStats.unevaluated }}</span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Historique des sélections -->
                                        <div v-if="dentalHistory.length > 0" class="mt-4 p-3 bg-yellow-50 rounded-lg">
                                            <h6 class="text-sm font-medium text-yellow-800 mb-2">Historique des Sélections</h6>
                                            <div class="flex flex-wrap gap-2">
                                                <span 
                                                    v-for="(tooth, index) in dentalHistory.slice(-8)" 
                                                    :key="index"
                                                    class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded text-xs"
                                                >
                                                    Dent @{{ tooth }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Viewer d'Images Dentaires -->
                                <div class="mt-6">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">👁️ Viewer d'Images Dentaires</h4>
                                    <p class="text-sm text-gray-600 mb-4">Visualisez et analysez les images dentaires (DICOM, JPG, PNG)</p>
                                    
                                    <!-- Upload Section -->
                                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <h5 class="text-sm font-medium text-blue-800 mb-2">📁 Upload d'Image Dentaire</h5>
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
                                                🔍 Charger dans le Viewer
                                            </button>
                                        </div>
                                        <p class="text-xs text-blue-600 mt-2">
                                            Formats supportés: DICOM, JPG, PNG, TIFF (max 10MB)
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
                            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-red-900 mb-4">🧪 Contrôle Anti-Dopage & AUT</h3>
                                <p class="text-red-700 mb-4">Enregistrement des contrôles anti-dopage et des autorisations d'usage à des fins thérapeutiques (AUT)</p>
                                
                                <!-- Anti-Doping Tests -->
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-gray-800">Tests Anti-Dopage</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="doping_test_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                Date du Test
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
                                                Type de Test
                                            </label>
                                            <select 
                                                id="doping_test_type" 
                                                name="doping_test_type"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                            >
                                                <option value="">Sélectionner</option>
                                                <option value="urine">Urinaire</option>
                                                <option value="blood">Sanguin</option>
                                                <option value="hair">Capillaire</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <label for="doping_test_result" class="block text-sm font-medium text-gray-700 mb-2">
                                            Résultat du Test
                                        </label>
                                        <select 
                                            id="doping_test_result" 
                                            name="doping_test_result"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                        >
                                            <option value="">Sélectionner</option>
                                            <option value="negative">Négatif</option>
                                            <option value="positive">Positif</option>
                                            <option value="pending">En attente</option>
                                            <option value="invalid">Invalide</option>
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
                                <h3 class="text-lg font-semibold text-yellow-900 mb-4">💪 Évaluations Physiques</h3>
                                <p class="text-yellow-700 mb-4">Tests et évaluations physiques complètes</p>
                                
                                <!-- Cardiovascular Assessment -->
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-gray-800">🫀 Évaluation Cardiovasculaire</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="cardio_blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">
                                                Tension Artérielle (mmHg)
                                            </label>
                                            <div class="flex space-x-2">
                                                <input 
                                                    type="number" 
                                                    id="cardio_systolic" 
                                                    name="cardio_systolic"
                                                    placeholder="Systolique"
                                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                                >
                                                <span class="self-center text-gray-500">/</span>
                                                <input 
                                                    type="number" 
                                                    id="cardio_diastolic" 
                                                    name="cardio_diastolic"
                                                    placeholder="Diastolique"
                                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                                >
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="cardio_heart_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                                Fréquence Cardiaque (bpm)
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
                                                Saturation O₂ (%)
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
                                <h3 class="text-lg font-semibold text-indigo-900 mb-4">🦴 Évaluation Posturale</h3>
                                <p class="text-indigo-700 mb-4">Évaluation complète de la posture et de l'alignement corporel</p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-md font-semibold text-gray-800 mb-3">Alignement Sagittal</h4>
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">Tête</span>
                                                <select name="posture_head" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="normal">Normal</option>
                                                    <option value="forward">Antéposition</option>
                                                    <option value="backward">Rétroposition</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">Épaules</span>
                                                <select name="posture_shoulders" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="normal">Normales</option>
                                                    <option value="rounded">Arrondies</option>
                                                    <option value="elevated">Élevées</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">Colonne</span>
                                                <select name="posture_spine" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="normal">Normale</option>
                                                    <option value="kyphosis">Cyphose</option>
                                                    <option value="lordosis">Lordose</option>
                                                    <option value="scoliosis">Scoliose</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-md font-semibold text-gray-800 mb-3">Alignement Frontal</h4>
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">Épaules</span>
                                                <select name="posture_shoulders_frontal" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="level">Niveau</option>
                                                    <option value="left_higher">Gauche plus haute</option>
                                                    <option value="right_higher">Droite plus haute</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">Bassin</span>
                                                <select name="posture_pelvis" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="level">Niveau</option>
                                                    <option value="left_higher">Gauche plus haut</option>
                                                    <option value="right_higher">Droite plus haut</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-700">Genoux</span>
                                                <select name="posture_knees" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                                    <option value="">-</option>
                                                    <option value="normal">Normales</option>
                                                    <option value="valgus">Valgus</option>
                                                    <option value="varus">Varus</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <label for="postural_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Notes Posturales
                                    </label>
                                    <textarea 
                                        id="postural_notes" 
                                        name="postural_notes"
                                        rows="4"
                                        placeholder="Observations détaillées sur la posture..."
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
                                <h3 class="text-lg font-semibold text-teal-900 mb-4">💉 Vaccinations</h3>
                                <p class="text-teal-700 mb-4">Gestion complète des vaccinations et du calendrier vaccinal</p>
                                
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-gray-800">Vaccins Administrés</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                Nom du Vaccin
                                            </label>
                                            <select 
                                                id="vaccine_name" 
                                                name="vaccine_name"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            >
                                                <option value="">Sélectionner</option>
                                                <option value="covid19">COVID-19 (Pfizer/BioNTech)</option>
                                                <option value="influenza">Grippe saisonnière</option>
                                                <option value="tetanus">Tétanos-Diphtérie-Coqueluche</option>
                                                <option value="hepatitis_b">Hépatite B</option>
                                                <option value="meningococcal">Méningocoque</option>
                                                <option value="pneumococcal">Pneumocoque</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="vaccine_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                Date d'Administration
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
                                                Dose
                                            </label>
                                            <select 
                                                id="vaccine_dose" 
                                                name="vaccine_dose"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            >
                                                <option value="">Sélectionner</option>
                                                <option value="1">1ère dose</option>
                                                <option value="2">2ème dose</option>
                                                <option value="3">3ème dose</option>
                                                <option value="booster">Rappel</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <label for="vaccine_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes sur la Vaccination
                                        </label>
                                        <textarea 
                                            id="vaccine_notes" 
                                            name="vaccine_notes"
                                            rows="3"
                                            placeholder="Réactions, observations, lot du vaccin..."
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
                                <h3 class="text-lg font-semibold text-blue-900 mb-4">📷 Imagerie Médicale</h3>
                                <p class="text-blue-700 mb-4">Gestion des examens d'imagerie médicale avec analyse IA et génération de rapports CDA</p>
                                
                                <!-- Imaging Records -->
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-md font-semibold text-gray-800">Examens d'imagerie</h4>
                                        <button 
                                            type="button" 
                                            id="add-imaging-btn"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                        >
                                            ➕ Ajouter un examen
                                        </button>
                                    </div>
                                    
                                    <!-- Add Imaging Form -->
                                    <div id="add-imaging-form" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                        <h5 class="text-md font-semibold text-gray-800 mb-4">Nouvel Examen d'Imagerie</h5>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="imaging_type" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Type d'Examen *
                                                </label>
                                                <select 
                                                    id="imaging_type" 
                                                    name="imaging_type"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                                    <option value="">Sélectionner un type</option>
                                                    <optgroup label="🦴 Radiographie">
                                                        <option value="xray_chest">Radiographie thoracique</option>
                                                        <option value="xray_spine">Radiographie rachis</option>
                                                        <option value="xray_limb">Radiographie membre</option>
                                                        <option value="xray_skull">Radiographie crâne</option>
                                                    </optgroup>
                                                    <optgroup label="🧠 Scanner">
                                                        <option value="ct_head">Scanner cérébral</option>
                                                        <option value="ct_chest">Scanner thoracique</option>
                                                        <option value="ct_abdomen">Scanner abdominal</option>
                                                        <option value="ct_spine">Scanner rachis</option>
                                                    </optgroup>
                                                    <optgroup label="🧲 IRM">
                                                        <option value="mri_brain">IRM cérébrale</option>
                                                        <option value="mri_spine">IRM rachis</option>
                                                        <option value="mri_knee">IRM genou</option>
                                                        <option value="mri_shoulder">IRM épaule</option>
                                                    </optgroup>
                                                    <optgroup label="🔍 Échographie">
                                                        <option value="us_abdomen">Échographie abdominale</option>
                                                        <option value="us_heart">Échographie cardiaque</option>
                                                        <option value="us_vascular">Échographie vasculaire</option>
                                                        <option value="us_musculoskeletal">Échographie musculo-squelettique</option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label for="imaging_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Date d'Examen *
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
                                                    Établissement
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="imaging_facility" 
                                                    name="imaging_facility"
                                                    placeholder="Nom de l'établissement"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                            </div>
                                            
                                            <div>
                                                <label for="imaging_radiologist" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Radiologue
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="imaging_radiologist" 
                                                    name="imaging_radiologist"
                                                    placeholder="Nom du radiologue"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                            </div>
                                            
                                            <div>
                                                <label for="imaging_indication" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Indication
                                                </label>
                                                <input 
                                                    type="text" 
                                                    id="imaging_indication" 
                                                    name="imaging_indication"
                                                    placeholder="Motif de l'examen"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                            </div>
                                            
                                            <div>
                                                <label for="imaging_technique" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Technique
                                                </label>
                                                <select 
                                                    id="imaging_technique" 
                                                    name="imaging_technique"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                                    <option value="">Sélectionner</option>
                                                    <option value="standard">Standard</option>
                                                    <option value="contrast">Avec contraste</option>
                                                    <option value="functional">Fonctionnelle</option>
                                                    <option value="dynamic">Dynamique</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- File Upload Section -->
                                        <div class="mt-4">
                                            <label for="imaging_file" class="block text-sm font-medium text-gray-700 mb-2">
                                                📁 Upload d'Image (DICOM, JPG, PNG)
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
                                                    <p class="text-sm text-gray-600">Cliquez pour sélectionner un fichier</p>
                                                    <p class="text-xs text-gray-500 mt-1">DICOM, JPG, PNG acceptés (max 10MB)</p>
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
                                                <h6 class="text-sm font-medium text-blue-800 mb-2">📋 Instructions d'Upload :</h6>
                                                <ul class="text-xs text-blue-700 space-y-1">
                                                    <li>• <strong>DICOM</strong> : Format médical standard pour les examens radiologiques</li>
                                                    <li>• <strong>JPG/PNG</strong> : Images numérisées ou photos d'écrans</li>
                                                    <li>• <strong>Taille max</strong> : 10MB par fichier</li>
                                                    <li>• <strong>Qualité</strong> : Résolution minimale recommandée 1024x768</li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <label for="imaging_findings" class="block text-sm font-medium text-gray-700 mb-2">
                                                Résultats *
                                            </label>
                                            <textarea 
                                                id="imaging_findings" 
                                                name="imaging_findings"
                                                rows="4"
                                                placeholder="Description des résultats de l'examen..."
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            ></textarea>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <label for="imaging_conclusion" class="block text-sm font-medium text-gray-700 mb-2">
                                                Conclusion
                                            </label>
                                            <textarea 
                                                id="imaging_conclusion" 
                                                name="imaging_conclusion"
                                                rows="3"
                                                placeholder="Conclusion du radiologue..."
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            ></textarea>
                                        </div>
                                        
                                        <!-- AI Analysis Section -->
                                        <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                            <h6 class="text-sm font-semibold text-purple-900 mb-2">🤖 Analyse IA avec Med Gemini</h6>
                                            <div class="flex space-x-2">
                                                <button 
                                                    type="button" 
                                                    id="analyze-imaging-btn"
                                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm"
                                                >
                                                    🔍 Analyser avec l'IA
                                                </button>
                                                <button 
                                                    type="button" 
                                                    id="generate-cda-btn"
                                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm"
                                                >
                                                    📄 Générer Rapport CDA
                                                </button>
                                            </div>
                                            <div id="ai-analysis-result" class="hidden mt-3 p-3 bg-white border border-purple-200 rounded-lg">
                                                <h6 class="text-sm font-semibold text-purple-800 mb-2">Analyse IA :</h6>
                                                <div id="ai-analysis-content" class="text-sm text-gray-700"></div>
                                            </div>
                                            
                                            <!-- AI Analysis Instructions -->
                                            <div class="mt-3 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                                <h6 class="text-sm font-medium text-purple-800 mb-2">🔍 Processus d'Analyse IA :</h6>
                                                <ol class="text-xs text-purple-700 space-y-1">
                                                    <li>1. <strong>Upload</strong> : Sélectionnez votre fichier d'imagerie</li>
                                                    <li>2. <strong>Analyse</strong> : Cliquez sur "🔍 Analyser avec l'IA"</li>
                                                    <li>3. <strong>Résultats</strong> : L'IA Med Gemini analyse l'image</li>
                                                    <li>4. <strong>Diagnostic</strong> : Obtention d'un rapport structuré</li>
                                                    <li>5. <strong>CDA</strong> : Génération d'un rapport médical standard</li>
                                                </ol>
                                            </div>
                                        </div>
                                        
                                        <!-- Image Viewer Section -->
                                        <div class="mt-4 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                                            <h6 class="text-sm font-semibold text-indigo-900 mb-2">👁️ Viewer d'Images Médicales</h6>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <h6 class="text-xs font-medium text-indigo-800 mb-1">Fonctionnalités du Viewer :</h6>
                                                    <ul class="text-xs text-indigo-700 space-y-1">
                                                        <li>• <strong>Zoom</strong> : Agrandissement jusqu'à 400%</li>
                                                        <li>• <strong>Pan</strong> : Navigation dans l'image</li>
                                                        <li>• <strong>Mesures</strong> : Outils de mesure intégrés</li>
                                                        <li>• <strong>Annotations</strong> : Ajout de marqueurs</li>
                                                        <li>• <strong>Contraste</strong> : Ajustement des niveaux</li>
                                                    </ul>
                                                </div>
                                                <div>
                                                    <h6 class="text-xs font-medium text-indigo-800 mb-1">Formats Supportés :</h6>
                                                    <ul class="text-xs text-indigo-700 space-y-1">
                                                        <li>• <strong>DICOM</strong> : Lecture native des métadonnées</li>
                                                        <li>• <strong>JPG/PNG</strong> : Affichage standard</li>
                                                        <li>• <strong>TIFF</strong> : Support haute résolution</li>
                                                        <li>• <strong>Multi-plan</strong> : Séries d'images</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Medical Image Viewer -->
                                        <div class="mt-4">
                                            <h6 class="text-sm font-semibold text-gray-800 mb-2">🔍 Viewer d'Images Médicales</h6>
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
                                                Annuler
                                            </button>
                                            <button 
                                                type="button" 
                                                id="save-imaging-btn"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                            >
                                                Enregistrer
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
                                <h3 class="text-lg font-semibold text-green-900 mb-4">📊 Résumé Imagerie</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-white p-4 rounded-lg border border-green-200">
                                        <h4 class="font-medium text-green-800 mb-2">📷 Examens Effectués</h4>
                                        <div id="imaging-count" class="text-sm text-gray-700">
                                            <span class="text-green-600">0</span> examen(s)
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white p-4 rounded-lg border border-green-200">
                                        <h4 class="font-medium text-green-800 mb-2">🔍 Types d'Examens</h4>
                                        <div id="imaging-types" class="text-sm text-gray-700">
                                            <span class="text-green-600">Aucun</span>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white p-4 rounded-lg border border-green-200">
                                        <h4 class="font-medium text-green-800 mb-2">⚠️ Résultats Anormaux</h4>
                                        <div id="imaging-abnormal" class="text-sm text-gray-700">
                                            <span class="text-green-600">0</span> résultat(s) anormal(aux)
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden fields for form submission -->
                            <input type="hidden" name="imaging_data" id="imaging_data" value="{{ old('imaging_data') }}">
                            <input type="hidden" name="dental_data" id="dental_data" value="{{ old('dental_data') }}">
                            <input type="hidden" name="selected_dental_tooth" id="selected_dental_tooth" value="{{ old('selected_dental_tooth') }}">
                        </div>
                    </div>

                    <!-- Onglet 10: Notes et Observations -->
                    <div v-show="activeTab === 'notes-observations'" class="tab-panel">
                        <div class="space-y-6">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Notes et Observations</h3>
                                <p class="text-gray-700 mb-4">Notes cliniques détaillées et plan de traitement</p>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes Cliniques
                                        </label>
                                        <textarea 
                                            id="clinical_notes" 
                                            name="clinical_notes"
                                            rows="6"
                                            placeholder="Observations cliniques détaillées..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                        ></textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">
                                            Plan de Traitement
                                        </label>
                                        <textarea 
                                            id="treatment_plan" 
                                            name="treatment_plan"
                                            rows="4"
                                            placeholder="Plan de traitement, recommandations..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                        ></textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="follow_up" class="block text-sm font-medium text-gray-700 mb-2">
                                            Suivi Recommandé
                                        </label>
                                        <textarea 
                                            id="follow_up" 
                                            name="follow_up"
                                            rows="3"
                                            placeholder="Recommandations de suivi, prochain rendez-vous..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons de soumission -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <button 
                    type="button" 
                    onclick="history.back()"
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                >
                    ← Retour
                </button>
                
                <div class="flex space-x-4">
                    <button 
                        type="button" 
                        id="save-draft-btn"
                        class="px-6 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
                    >
                        💾 Sauvegarder brouillon
                    </button>
                    
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        ✅ Enregistrer la visite
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier que Vue.js est chargé
    if (typeof Vue === 'undefined') {
        console.error('Vue.js n\'est pas chargé');
        return;
    }

    // Configuration des onglets
    const tabs = [
        { id: 'general', label: 'Informations Générales', icon: '👤' },
        { id: 'ai-assistant', label: 'Assistant IA', icon: '🤖' },
        { id: 'medical-categories', label: 'Catégories Médicales', icon: '🏥' },
        { id: 'dental-record', label: 'Dossier Dentaire', icon: '🦷' },
        { id: 'doping-control', label: 'Contrôle Anti-Dopage', icon: '🧪' },
        { id: 'physical-assessments', label: 'Évaluations Physiques', icon: '💪' },
        { id: 'postural-assessment', label: 'Évaluation Posturale', icon: '🦴' },
        { id: 'vaccinations', label: 'Vaccinations', icon: '💉' },
        { id: 'medical-imaging', label: 'Imagerie Médicale', icon: '📷' },
        { id: 'notes-observations', label: 'Notes et Observations', icon: '📝' }
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
                            if (birthdateElement) birthdateElement.textContent = player.date_of_birth ? new Date(player.date_of_birth).toLocaleDateString('fr-FR') : 'N/A';
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
                                if (dentalBirthdateElement) dentalBirthdateElement.textContent = player.date_of_birth ? new Date(player.date_of_birth).toLocaleDateString('fr-FR') : 'N/A';
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
                const activeTab = ref('general');
                
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
                                        <div>Erreur de chargement</div>
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
                        alert('Veuillez sélectionner une dent avant de sauvegarder');
                        return;
                    }
                    
                    // Vérifier que le patient est sélectionné
                    const playerId = document.getElementById('player_id').value;
                    if (!playerId) {
                        alert('Veuillez d\'abord sélectionner un patient');
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
                    
                    alert(`Données de la dent ${selectedDentalTooth.value} sauvegardées avec succès !`);
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
                    }
                    
                    if (selectedToothField && selectedDentalTooth.value) {
                        selectedToothField.value = selectedDentalTooth.value;
                    }
                };
                
                const getDentalToothType = (toothId) => {
                    const toothTypes = {
                        '11': 'Incisive centrale droite',
                        '12': 'Incisive latérale droite',
                        '13': 'Canine droite',
                        '14': 'Première prémolaire droite',
                        '15': 'Deuxième prémolaire droite',
                        '16': 'Première molaire droite',
                        '17': 'Deuxième molaire droite',
                        '18': 'Troisième molaire droite',
                        '21': 'Incisive centrale gauche',
                        '22': 'Incisive latérale gauche',
                        '23': 'Canine gauche',
                        '24': 'Première prémolaire gauche',
                        '25': 'Deuxième prémolaire gauche',
                        '26': 'Première molaire gauche',
                        '27': 'Deuxième molaire gauche',
                        '28': 'Troisième molaire gauche',
                        '31': 'Incisive centrale droite inférieure',
                        '32': 'Incisive latérale droite inférieure',
                        '33': 'Canine droite inférieure',
                        '34': 'Première prémolaire droite inférieure',
                        '35': 'Deuxième prémolaire droite inférieure',
                        '36': 'Première molaire droite inférieure',
                        '37': 'Deuxième molaire droite inférieure',
                        '38': 'Troisième molaire droite inférieure',
                        '41': 'Incisive centrale gauche inférieure',
                        '42': 'Incisive latérale gauche inférieure',
                        '43': 'Canine gauche inférieure',
                        '44': 'Première prémolaire gauche inférieure',
                        '45': 'Deuxième prémolaire gauche inférieure',
                        '46': 'Première molaire gauche inférieure',
                        '47': 'Deuxième molaire gauche inférieure',
                        '48': 'Troisième molaire gauche inférieure'
                    };
                    return toothTypes[toothId] || 'Inconnue';
                };
                
                const getDentalQuadrant = (toothId) => {
                    const firstDigit = parseInt(toothId.charAt(0));
                    const quadrants = {
                        1: 'Quadrant 1 (Supérieur Droit)',
                        2: 'Quadrant 2 (Supérieur Gauche)',
                        3: 'Quadrant 3 (Inférieur Gauche)',
                        4: 'Quadrant 4 (Inférieur Droit)'
                    };
                    return quadrants[firstDigit] || 'Inconnu';
                };

                return {
                    tabs,
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
                        alert('Le fichier est trop volumineux. Taille maximum : 10MB');
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
            alert('Veuillez remplir les champs obligatoires');
            return;
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
        const list = document.getElementById('imaging-list');
        if (!list) return;
        
        list.innerHTML = '';
        
        imagingRecords.forEach((record, index) => {
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200';
            div.innerHTML = `
                <div class="flex-1">
                    <div class="font-medium text-gray-800">${getImagingTypeDisplayName(record.imaging_type)}</div>
                    <div class="text-sm text-gray-600">
                        Date: ${formatDate(record.imaging_date)} | 
                        ${record.imaging_facility ? `Établissement: ${record.imaging_facility}` : ''}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        ${record.imaging_findings.substring(0, 100)}${record.imaging_findings.length > 100 ? '...' : ''}
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="editImagingRecord(${index})" class="text-blue-600 hover:text-blue-800 text-sm">✏️</button>
                    <button onclick="deleteImagingRecord(${index})" class="text-red-600 hover:text-red-800 text-sm">🗑️</button>
                </div>
            `;
            list.appendChild(div);
        });
    }

    function getImagingTypeDisplayName(type) {
        const types = {
            'xray_chest': 'Radiographie thoracique',
            'xray_spine': 'Radiographie rachis',
            'xray_limb': 'Radiographie membre',
            'xray_skull': 'Radiographie crâne',
            'ct_head': 'Scanner cérébral',
            'ct_chest': 'Scanner thoracique',
            'ct_abdomen': 'Scanner abdominal',
            'ct_spine': 'Scanner rachis',
            'mri_brain': 'IRM cérébrale',
            'mri_spine': 'IRM rachis',
            'mri_knee': 'IRM genou',
            'mri_shoulder': 'IRM épaule',
            'us_abdomen': 'Échographie abdominale',
            'us_heart': 'Échographie cardiaque',
            'us_vascular': 'Échographie vasculaire',
            'us_musculoskeletal': 'Échographie musculo-squelettique'
        };
        return types[type] || type;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR');
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
        if (confirm('Êtes-vous sûr de vouloir supprimer cet examen d\'imagerie ?')) {
            imagingRecords.splice(index, 1);
            updateImagingList();
            updateImagingData();
        }
    }

    function updateImagingData() {
        const imagingDataField = document.getElementById('imaging_data');
        if (imagingDataField) {
            imagingDataField.value = JSON.stringify(imagingRecords);
        }
        updateImagingSummary();
    }

    function updateImagingSummary() {
        const countElement = document.getElementById('imaging-count');
        const typesElement = document.getElementById('imaging-types');
        const abnormalElement = document.getElementById('imaging-abnormal');
        
        if (countElement) {
            countElement.innerHTML = `<span class="text-green-600">${imagingRecords.length}</span> examen(s)`;
        }
        
        if (typesElement) {
            const types = [...new Set(imagingRecords.map(r => r.imaging_type))];
            if (types.length > 0) {
                typesElement.innerHTML = `<span class="text-green-600">${types.length}</span> type(s) d'examen`;
            } else {
                typesElement.innerHTML = `<span class="text-green-600">Aucun</span>`;
            }
        }
        
        if (abnormalElement) {
            const abnormalCount = imagingRecords.filter(r => 
                r.imaging_findings.toLowerCase().includes('anormal') || 
                r.imaging_findings.toLowerCase().includes('pathologique') ||
                r.imaging_findings.toLowerCase().includes('fracture') ||
                r.imaging_findings.toLowerCase().includes('lésion')
            ).length;
            abnormalElement.innerHTML = `<span class="text-green-600">${abnormalCount}</span> résultat(s) anormal(aux)`;
        }
    }

    function analyzeImagingWithAI() {
        const imagingFile = document.getElementById('imaging_file');
        const imagingFindings = document.getElementById('imaging_findings').value;
        const imagingType = document.getElementById('imaging_type').value;
        
        if (!imagingFile.files[0] && !imagingFindings) {
            alert('Veuillez uploader une image ou saisir des résultats pour l\'analyse IA');
            return;
        }

        const analyzeBtn = document.getElementById('analyze-imaging-btn');
        const aiResult = document.getElementById('ai-analysis-result');
        const aiContent = document.getElementById('ai-analysis-content');
        
        // Simuler l'analyse IA
        analyzeBtn.textContent = '🔍 Analyse en cours...';
        analyzeBtn.disabled = true;
        
        setTimeout(() => {
            const analysis = generateAIAnalysis(imagingType, imagingFindings);
            aiContent.innerHTML = analysis;
            aiResult.classList.remove('hidden');
            
            analyzeBtn.textContent = '🔍 Analyser avec l\'IA';
            analyzeBtn.disabled = false;
        }, 2000);
    }

    function generateAIAnalysis(type, findings) {
        const analysis = {
            'xray_chest': {
                'normal': 'Radiographie thoracique normale. Pas de signe de pathologie pulmonaire, cardiaque ou pleurale.',
                'abnormal': 'Anomalies détectées sur la radiographie thoracique. Recommandations pour examens complémentaires.'
            },
            'ct_head': {
                'normal': 'Scanner cérébral normal. Pas de lésion intracrânienne détectée.',
                'abnormal': 'Anomalies détectées sur le scanner cérébral. Évaluation neurologique recommandée.'
            },
            'mri_brain': {
                'normal': 'IRM cérébrale normale. Pas de lésion cérébrale détectée.',
                'abnormal': 'Anomalies détectées sur l\'IRM cérébrale. Consultation neurologique recommandée.'
            }
        };

        const typeAnalysis = analysis[type] || analysis['xray_chest'];
        const isAbnormal = findings.toLowerCase().includes('anormal') || 
                          findings.toLowerCase().includes('pathologique') ||
                          findings.toLowerCase().includes('fracture') ||
                          findings.toLowerCase().includes('lésion');

        return `
            <div class="space-y-2">
                <div class="font-medium text-purple-800">Analyse IA - ${getImagingTypeDisplayName(type)}</div>
                <div class="text-sm">
                    <strong>Diagnostic IA :</strong> ${isAbnormal ? 'Anormal' : 'Normal'}
                </div>
                <div class="text-sm">
                    <strong>Analyse détaillée :</strong> ${typeAnalysis[isAbnormal ? 'abnormal' : 'normal']}
                </div>
                <div class="text-sm">
                    <strong>Confiance IA :</strong> ${isAbnormal ? '85%' : '92%'}
                </div>
                <div class="text-sm">
                    <strong>Recommandations :</strong> ${isAbnormal ? 'Examens complémentaires recommandés' : 'Suivi standard'}
                </div>
            </div>
        `;
    }

    function generateCDAReport() {
        const imagingRecords = JSON.parse(document.getElementById('imaging_data').value || '[]');
        
        if (imagingRecords.length === 0) {
            alert('Aucun examen d\'imagerie à inclure dans le rapport CDA');
            return;
        }

        // Simuler la génération du rapport CDA
        const cdaReport = generateCDAXML(imagingRecords);
        
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
        
        alert('Rapport CDA généré et téléchargé avec succès !');
    }

    function generateCDAXML(records) {
        const now = new Date().toISOString();
        const patientName = 'Patient Test'; // À remplacer par les vraies données
        
        const xmlContent = 
            '<ClinicalDocument xmlns="urn:hl7-org:v3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' +
            '<realmCode code="FR"/>' +
            '<typeId root="2.16.840.1.113883.1.3" extension="POCD_HD000040"/>' +
            '<templateId root="2.16.840.1.113883.10.20.1"/>' +
            '<id root="2.16.840.1.113883.19" extension="123456789"/>' +
            '<code code="11506-3" codeSystem="2.16.840.1.113883.6.1" displayName="Progress note"/>' +
            '<title>Rapport d\'Imagerie Médicale</title>' +
            '<effectiveTime value="' + now + '"/>' +
            '<confidentialityCode code="N" codeSystem="2.16.840.1.113883.5.25"/>' +
            '<languageCode code="fr-FR"/>' +
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
                            '<given>Dr. Radiologue</given>' +
                        '</name>' +
                    '</assignedPerson>' +
                '</assignedAuthor>' +
            '</author>' +
            '<custodian>' +
                '<assignedCustodian>' +
                    '<representedCustodianOrganization>' +
                        '<id root="2.16.840.1.113883.19.5" extension="HOSPITAL"/>' +
                        '<name>Centre d\'Imagerie Médicale</name>' +
                    '</representedCustodianOrganization>' +
                '</assignedCustodian>' +
            '</custodian>' +
            '<component>' +
                '<structuredBody>' +
                    '<component>' +
                        '<section>' +
                            '<templateId root="2.16.840.1.113883.10.20.1.11"/>' +
                            '<code code="8716-3" codeSystem="2.16.840.1.113883.6.1" displayName="Vital signs"/>' +
                            '<title>Examens d\'Imagerie</title>' +
                            '<text>' +
                                '<table border="1" width="100%">' +
                                    '<thead>' +
                                        '<tr>' +
                                            '<th>Type d\'Examen</th>' +
                                            '<th>Date</th>' +
                                            '<th>Établissement</th>' +
                                            '<th>Résultats</th>' +
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
                        alert('Le fichier est trop volumineux. Taille maximum : 10MB');
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
            alert('Veuillez remplir les champs obligatoires');
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
                        ${record.dental_imaging_notes ? record.dental_imaging_notes.substring(0, 50) + '...' : 'Aucune note'}
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
        const types = {
            'panoramic': 'Panoramique dentaire',
            'bitewing': 'Bitewing (interproximale)',
            'periapical': 'Périapicale',
            'occlusal': 'Occlusale',
            'cbct': 'Cone Beam CT (CBCT)',
            'dental_ct': 'Scanner dentaire',
            'intraoral': 'Photo intra-orale',
            'extraoral': 'Photo extra-orale',
            'model': 'Modèle 3D'
        };
        return types[type] || type;
    }

    function analyzeDentalImagingWithAI() {
        const dentalImagingFile = document.getElementById('dental_imaging_file');
        const dentalImagingType = document.getElementById('dental_imaging_type').value;
        const dentalImagingNotes = document.getElementById('dental_imaging_notes').value;
        
        if (!dentalImagingFile.files[0] && !dentalImagingType) {
            alert('Veuillez uploader une image ou sélectionner un type d\'examen pour l\'analyse IA');
            return;
        }

        const analyzeBtn = document.getElementById('analyze-dental-imaging-btn');
        
        // Simuler l'analyse IA
        analyzeBtn.textContent = '🔍 Analyse en cours...';
        analyzeBtn.disabled = true;
        
        setTimeout(() => {
            const analysis = generateDentalAIAnalysis(dentalImagingType, dentalImagingNotes);
            alert(`Analyse IA terminée :\n\n${analysis}`);
            
            analyzeBtn.textContent = '🔍 Analyser avec l\'IA';
            analyzeBtn.disabled = false;
        }, 2000);
    }

    function generateDentalAIAnalysis(type, notes) {
        const analysis = {
            'panoramic': {
                'normal': 'Panoramique normale. Toutes les dents présentes et alignées. Pas de carie visible.',
                'abnormal': 'Anomalies détectées sur le panoramique. Caries, dents manquantes ou malpositionnées observées.'
            },
            'bitewing': {
                'normal': 'Bitewing normale. Pas de carie interproximale détectée.',
                'abnormal': 'Caries interproximales détectées. Recommandations de traitement.'
            },
            'periapical': {
                'normal': 'Périapicale normale. Racines saines, pas de lésion apicale.',
                'abnormal': 'Lésion apicale détectée. Évaluation endodontique recommandée.'
            }
        };

        const typeAnalysis = analysis[type] || analysis['panoramic'];
        const isAbnormal = notes.toLowerCase().includes('carie') || 
                          notes.toLowerCase().includes('anormal') ||
                          notes.toLowerCase().includes('lésion') ||
                          notes.toLowerCase().includes('problème');

        return `Analyse IA - ${getDentalImagingTypeDisplayName(type)}\n\n` +
               `Diagnostic IA : ${isAbnormal ? 'Anormal' : 'Normal'}\n` +
               `Analyse détaillée : ${typeAnalysis[isAbnormal ? 'abnormal' : 'normal']}\n` +
               `Confiance IA : ${isAbnormal ? '87%' : '94%'}\n` +
               `Recommandations : ${isAbnormal ? 'Consultation dentaire recommandée' : 'Suivi standard'}`;
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
                            <p>⚠️ Erreur: Viewer non disponible</p>
                            <p class="text-sm">Vérifiez que le script image-viewer.js est bien chargé</p>
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
                successMsg.innerHTML = '✅ Viewer médical prêt - Chargez une image pour commencer';
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
                        <p>⚠️ Erreur d'initialisation du viewer</p>
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
                        alert('Erreur lors du chargement de l\'image');
                    });
                } else {
                    alert('Veuillez sélectionner un fichier');
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
        loadInViewerBtn.innerHTML = '👁️ Charger dans le Viewer';
        loadInViewerBtn.onclick = () => {

            
            const file = fileInput.files[0];
            if (!file) {
                alert('Veuillez sélectionner un fichier');
                return;
            }
            
            
            
            if (!medicalImageViewer) {
                console.error('❌ Viewer médical non initialisé');
                alert('Erreur: Viewer médical non disponible. Veuillez recharger la page.');
                return;
            }
            
            
            
            medicalImageViewer.loadImage(file).then(() => {
                
                alert('Image chargée avec succès dans le viewer !');
            }).catch(error => {
                console.error('❌ Erreur lors du chargement:', error);
                alert('Erreur lors du chargement de l\'image: ' + error.message);
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
    if (confirm('Êtes-vous sûr de vouloir supprimer cet examen d\'imagerie dentaire ?')) {
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
