@extends('layouts.app')

@section('title', 'Nouveau PCMA - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">📋 Nouveau PCMA</h1>
            <p class="text-gray-600 mt-2">Créer une nouvelle évaluation médicale pré-compétition</p>
        </div>

        <!-- Input Method Selection -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Méthode de saisie</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Manual Input -->
                    <button type="button" id="manual-tab" class="input-method-tab active bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 transition-colors">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <h3 class="font-semibold">Saisie manuelle</h3>
                            <p class="text-sm opacity-90">Remplir le formulaire directement</p>
                        </div>
                    </button>

                    <!-- Voice Recording -->
                    <button type="button" id="voice-tab" class="input-method-tab bg-gray-100 text-gray-700 p-4 rounded-lg hover:bg-gray-200 transition-colors">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                            <h3 class="font-semibold">Enregistrement vocal</h3>
                            <p class="text-sm opacity-90">Dictée avec Whisper</p>
                        </div>
                    </button>

                    <!-- FHIR Download -->
                    <button type="button" id="fhir-tab" class="input-method-tab bg-gray-100 text-gray-700 p-4 rounded-lg hover:bg-gray-200 transition-colors">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <h3 class="font-semibold">Téléchargement FHIR</h3>
                            <p class="text-sm opacity-90">Importer depuis FHIR</p>
                        </div>
                    </button>

                    <!-- Image Scan -->
                    <button type="button" id="scan-tab" class="input-method-tab bg-gray-100 text-gray-700 p-4 rounded-lg hover:bg-gray-200 transition-colors">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="font-semibold">Scan d'image</h3>
                            <p class="text-sm opacity-90">OCR depuis papier</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Manual Input Section -->
        <div id="manual-section" class="input-section">
            <form action="{{ route('pcma.store') }}" method="POST" class="space-y-8">
                @csrf
                
                <!-- AI-Assisted Section -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <h2 class="text-xl font-semibold text-blue-900">Assistant IA PCMA</h2>
                    </div>
                    <p class="text-blue-700 mb-4">Décrivez les résultats de l'examen médical pour une analyse automatique</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes Cliniques
                            </label>
                            <textarea 
                                id="clinical_notes" 
                                name="clinical_notes" 
                                rows="4" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Exemple: Patient présente une tension artérielle de 120/80 mmHg, fréquence cardiaque de 65 bpm au repos. Pas d'antécédents cardiovasculaires. Examen neurologique normal. Pas de douleurs musculo-squelettiques..."
                            ></textarea>
                        </div>
                        
                        <div class="flex space-x-4">
                            <button 
                                type="button" 
                                id="ai-analyze-btn"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                            >
                                🔍 Analyser avec l'IA
                            </button>
                            <button 
                                type="button" 
                                id="clear-notes-btn"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
                            >
                                Effacer
                            </button>
                        </div>
                        
                        <div id="ai-results" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Analyse IA</h3>
                            <div id="ai-content" class="text-sm text-gray-700"></div>
                        </div>
                    </div>
                </div>

                <!-- PCMA Form -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Informations PCMA</h2>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Athlete Selection -->
                        <div>
                            <label for="athlete_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Athlète *
                            </label>
                            <select 
                                id="athlete_id" 
                                name="athlete_id" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Sélectionner un athlète</option>
                                @foreach($athletes as $athlete)
                                    <option value="{{ $athlete->id }}" {{ old('athlete_id') == $athlete->id ? 'selected' : '' }}>
                                        {{ $athlete->first_name }} {{ $athlete->last_name }} ({{ $athlete->club->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- FIFA Connect ID -->
                        <div>
                            <label for="fifa_connect_id" class="block text-sm font-medium text-gray-700 mb-2">
                                ID FIFA Connect
                            </label>
                            <input 
                                type="text" 
                                id="fifa_connect_id" 
                                name="fifa_connect_id" 
                                value="{{ old('fifa_connect_id') }}"
                                placeholder="Entrez l'ID FIFA Connect du joueur"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="text-xs text-gray-500 mt-1">Laissez vide si l'athlète n'a pas d'ID FIFA Connect</p>
                        </div>

                        <!-- PCMA Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Type d'évaluation *
                            </label>
                            <select 
                                id="type" 
                                name="type" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Sélectionner le type</option>
                                <option value="bpma" {{ old('type') == 'bpma' ? 'selected' : '' }}>BPMA</option>
                                <option value="cardio" {{ old('type') == 'cardio' ? 'selected' : '' }}>Cardiovasculaire</option>
                                <option value="dental" {{ old('type') == 'dental' ? 'selected' : '' }}>Dentaire</option>
                                <option value="neurological" {{ old('type') == 'neurological' ? 'selected' : '' }}>Neurologique</option>
                                <option value="orthopedic" {{ old('type') == 'orthopedic' ? 'selected' : '' }}>Orthopédique</option>
                            </select>
                        </div>

                        <!-- Assessor -->
                        <div>
                            <label for="assessor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Assesseur *
                            </label>
                            <select 
                                id="assessor_id" 
                                name="assessor_id" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Sélectionner un assesseur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assessor_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assessment Date -->
                        <div>
                            <label for="assessment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date d'évaluation *
                            </label>
                            <input 
                                type="date" 
                                id="assessment_date" 
                                name="assessment_date" 
                                value="{{ old('assessment_date', date('Y-m-d')) }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Statut *
                            </label>
                            <select 
                                id="status" 
                                name="status" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                                <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                            </select>
                        </div>



                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Notes additionnelles sur l'évaluation..."
                            >{{ old('notes') }}</textarea>
                        </div>

                        <!-- Detailed Medical Assessment Sections -->
                        
                        <!-- Vital Signs Section -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-yellow-900">📊 Signes Vitaux</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tension Artérielle
                                    </label>
                                    <input type="text" id="blood_pressure" name="blood_pressure" 
                                           value="{{ old('blood_pressure') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                           placeholder="120/80 mmHg">
                                </div>
                                
                                <div>
                                    <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fréquence Cardiaque
                                    </label>
                                    <input type="number" id="heart_rate" name="heart_rate" 
                                           value="{{ old('heart_rate') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                           placeholder="65 bpm">
                                </div>
                                
                                <div>
                                    <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">
                                        Température
                                    </label>
                                    <input type="number" id="temperature" name="temperature" 
                                           value="{{ old('temperature') }}"
                                           step="0.1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                           placeholder="36.8 °C">
                                </div>
                                
                                <div>
                                    <label for="respiratory_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fréquence Respiratoire
                                    </label>
                                    <input type="number" id="respiratory_rate" name="respiratory_rate" 
                                           value="{{ old('respiratory_rate') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                           placeholder="16/min">
                                </div>
                                
                                <div>
                                    <label for="oxygen_saturation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Saturation O₂
                                    </label>
                                    <input type="number" id="oxygen_saturation" name="oxygen_saturation" 
                                           value="{{ old('oxygen_saturation') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                           placeholder="98 %">
                                </div>
                                
                                <div>
                                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                        Poids
                                    </label>
                                    <input type="number" id="weight" name="weight" 
                                           value="{{ old('weight') }}"
                                           step="0.1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                           placeholder="75 kg">
                                </div>
                            </div>
                        </div>

                        <!-- Medical History Section -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-green-900">🏥 Antécédents Médicaux</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="cardiovascular_history" class="block text-sm font-medium text-gray-700 mb-2">
                                        Antécédents Cardio-vasculaires
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="cardiovascular_search" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="Rechercher des conditions cardio-vasculaires..."
                                               autocomplete="off">
                                        <div id="cardiovascular_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                                        <input type="hidden" id="cardiovascular_history" name="cardiovascular_history" value="{{ old('cardiovascular_history') }}">
                                        <div id="cardiovascular_selected" class="mt-2 space-y-1"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="surgical_history" class="block text-sm font-medium text-gray-700 mb-2">
                                        Antécédents Chirurgicaux
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="surgical_search" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="Rechercher des procédures chirurgicales..."
                                               autocomplete="off">
                                        <div id="surgical_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                                        <input type="hidden" id="surgical_history" name="surgical_history" value="{{ old('surgical_history') }}">
                                        <div id="surgical_selected" class="mt-2 space-y-1"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="medications" class="block text-sm font-medium text-gray-700 mb-2">
                                        Médicaments Actuels
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="medication_search" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="Rechercher des médicaments..."
                                               autocomplete="off">
                                        <div id="medication_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                                        <input type="hidden" id="medications" name="medications" value="{{ old('medications') }}">
                                        <div id="medication_selected" class="mt-2 space-y-1"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                                        Allergies
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="allergy_search" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="Rechercher des allergies..."
                                               autocomplete="off">
                                        <div id="allergy_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                                        <input type="hidden" id="allergies" name="allergies" value="{{ old('allergies') }}">
                                        <div id="allergy_selected" class="mt-2 space-y-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Physical Examination Section -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-red-900">🔍 Examen Physique</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="general_appearance" class="block text-sm font-medium text-gray-700 mb-2">
                                        Apparence Générale
                                    </label>
                                    <select id="general_appearance" name="general_appearance" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('general_appearance') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="abnormal" {{ old('general_appearance') == 'abnormal' ? 'selected' : '' }}>Anormal</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="skin_examination" class="block text-sm font-medium text-gray-700 mb-2">
                                        Examen Cutané
                                    </label>
                                    <select id="skin_examination" name="skin_examination" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('skin_examination') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="abnormal" {{ old('skin_examination') == 'abnormal' ? 'selected' : '' }}>Anormal</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="lymph_nodes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Ganglions Lymphatiques
                                    </label>
                                    <select id="lymph_nodes" name="lymph_nodes" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('lymph_nodes') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="enlarged" {{ old('lymph_nodes') == 'enlarged' ? 'selected' : '' }}>Hypertrophiés</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="abdomen_examination" class="block text-sm font-medium text-gray-700 mb-2">
                                        Examen Abdominal
                                    </label>
                                    <select id="abdomen_examination" name="abdomen_examination" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('abdomen_examination') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="abnormal" {{ old('abdomen_examination') == 'abnormal' ? 'selected' : '' }}>Anormal</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Imaging Upload Section -->
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-indigo-900">📷 Imagerie Médicale</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- ECG Upload -->
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <h4 class="font-semibold text-gray-900">❤️ Électrocardiogramme (ECG)</h4>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <label for="ecg_file" class="block text-sm font-medium text-gray-700 mb-2">
                                                Fichier ECG
                                            </label>
                                            <input type="file" id="ecg_file" name="ecg_file" accept=".pdf,.jpg,.jpeg,.png,.bmp,.tiff,.tif,.dcm"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, JPG, PNG, BMP, TIFF, DICOM</p>
                                        </div>
                                        
                                        <div>
                                            <label for="ecg_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                Date de l'ECG
                                            </label>
                                            <input type="date" id="ecg_date" name="ecg_date" 
                                                   value="{{ old('ecg_date') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        </div>
                                        
                                        <div>
                                            <label for="ecg_interpretation" class="block text-sm font-medium text-gray-700 mb-2">
                                                Interprétation ECG
                                            </label>
                                            <select id="ecg_interpretation" name="ecg_interpretation" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                <option value="">Sélectionner</option>
                                                <option value="normal" {{ old('ecg_interpretation') == 'normal' ? 'selected' : '' }}>Normal</option>
                                                <option value="sinus_bradycardia" {{ old('ecg_interpretation') == 'sinus_bradycardia' ? 'selected' : '' }}>Bradycardie sinusale</option>
                                                <option value="sinus_tachycardia" {{ old('ecg_interpretation') == 'sinus_tachycardia' ? 'selected' : '' }}>Tachycardie sinusale</option>
                                                <option value="atrial_fibrillation" {{ old('ecg_interpretation') == 'atrial_fibrillation' ? 'selected' : '' }}>Fibrillation auriculaire</option>
                                                <option value="ventricular_tachycardia" {{ old('ecg_interpretation') == 'ventricular_tachycardia' ? 'selected' : '' }}>Tachycardie ventriculaire</option>
                                                <option value="st_elevation" {{ old('ecg_interpretation') == 'st_elevation' ? 'selected' : '' }}>Élévation du segment ST</option>
                                                <option value="st_depression" {{ old('ecg_interpretation') == 'st_depression' ? 'selected' : '' }}>Dépression du segment ST</option>
                                                <option value="qt_prolongation" {{ old('ecg_interpretation') == 'qt_prolongation' ? 'selected' : '' }}>Prolongation QT</option>
                                                <option value="abnormal" {{ old('ecg_interpretation') == 'abnormal' ? 'selected' : '' }}>Anormal (préciser)</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="ecg_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                                Notes ECG
                                            </label>
                                            <textarea id="ecg_notes" name="ecg_notes" rows="3"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                      placeholder="Détails de l'interprétation ECG...">{{ old('ecg_notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- MRI Upload -->
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        <h4 class="font-semibold text-gray-900">🧠 Imagerie par Résonance Magnétique (IRM)</h4>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <label for="mri_file" class="block text-sm font-medium text-gray-700 mb-2">
                                                Fichier IRM
                                            </label>
                                            <input type="file" id="mri_file" name="mri_file" accept=".pdf,.jpg,.jpeg,.png,.bmp,.tiff,.tif,.dcm"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, JPG, PNG, BMP, TIFF, DICOM</p>
                                        </div>
                                        
                                        <div>
                                            <label for="mri_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                Date de l'IRM
                                            </label>
                                            <input type="date" id="mri_date" name="mri_date" 
                                                   value="{{ old('mri_date') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        </div>
                                        
                                        <div>
                                            <label for="mri_type" class="block text-sm font-medium text-gray-700 mb-2">
                                                Type d'IRM
                                            </label>
                                            <select id="mri_type" name="mri_type" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                <option value="">Sélectionner</option>
                                                <option value="brain" {{ old('mri_type') == 'brain' ? 'selected' : '' }}>IRM Cérébrale</option>
                                                <option value="spine" {{ old('mri_type') == 'spine' ? 'selected' : '' }}>IRM Rachidienne</option>
                                                <option value="knee" {{ old('mri_type') == 'knee' ? 'selected' : '' }}>IRM du Genou</option>
                                                <option value="shoulder" {{ old('mri_type') == 'shoulder' ? 'selected' : '' }}>IRM de l'Épaule</option>
                                                <option value="ankle" {{ old('mri_type') == 'ankle' ? 'selected' : '' }}>IRM de la Cheville</option>
                                                <option value="hip" {{ old('mri_type') == 'hip' ? 'selected' : '' }}>IRM de la Hanche</option>
                                                <option value="cardiac" {{ old('mri_type') == 'cardiac' ? 'selected' : '' }}>IRM Cardiaque</option>
                                                <option value="other" {{ old('mri_type') == 'other' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="mri_findings" class="block text-sm font-medium text-gray-700 mb-2">
                                                Résultats IRM
                                            </label>
                                            <select id="mri_findings" name="mri_findings" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                <option value="">Sélectionner</option>
                                                <option value="normal" {{ old('mri_findings') == 'normal' ? 'selected' : '' }}>Normal</option>
                                                <option value="mild_abnormality" {{ old('mri_findings') == 'mild_abnormality' ? 'selected' : '' }}>Anomalie légère</option>
                                                <option value="moderate_abnormality" {{ old('mri_findings') == 'moderate_abnormality' ? 'selected' : '' }}>Anomalie modérée</option>
                                                <option value="severe_abnormality" {{ old('mri_findings') == 'severe_abnormality' ? 'selected' : '' }}>Anomalie sévère</option>
                                                <option value="fracture" {{ old('mri_findings') == 'fracture' ? 'selected' : '' }}>Fracture</option>
                                                <option value="tumor" {{ old('mri_findings') == 'tumor' ? 'selected' : '' }}>Tumeur</option>
                                                <option value="inflammation" {{ old('mri_findings') == 'inflammation' ? 'selected' : '' }}>Inflammation</option>
                                                <option value="degenerative" {{ old('mri_findings') == 'degenerative' ? 'selected' : '' }}>Changements dégénératifs</option>
                                                <option value="other" {{ old('mri_findings') == 'other' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="mri_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                                Notes IRM
                                            </label>
                                            <textarea id="mri_notes" name="mri_notes" rows="3"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                      placeholder="Détails des résultats IRM...">{{ old('mri_notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Imaging -->
                            <div class="mt-6">
                                <h4 class="font-semibold text-gray-900 mb-3">📊 Autres Examens d'Imagerie</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="xray_file" class="block text-sm font-medium text-gray-700 mb-2">
                                            Radiographie (X-Ray)
                                        </label>
                                        <input type="file" id="xray_file" name="xray_file" accept=".pdf,.jpg,.jpeg,.png,.dcm"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    
                                    <div class="md:col-span-3">
                                        <label for="xray_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes Radiographie (X-Ray)
                                        </label>
                                        <textarea id="xray_notes" name="xray_notes" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                  placeholder="Résultats de l'analyse radiographique...">{{ old('xray_notes') }}</textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="ct_scan_file" class="block text-sm font-medium text-gray-700 mb-2">
                                            Scanner (CT)
                                        </label>
                                        <input type="file" id="ct_scan_file" name="ct_scan_file" accept=".pdf,.jpg,.jpeg,.png,.dcm"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    
                                    <div>
                                        <label for="ultrasound_file" class="block text-sm font-medium text-gray-700 mb-2">
                                            Échographie
                                        </label>
                                        <input type="file" id="ultrasound_file" name="ultrasound_file" accept=".pdf,.jpg,.jpeg,.png,.dcm"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    
                                    <div class="md:col-span-3">
                                        <label for="ct_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes Scanner (CT)
                                        </label>
                                        <textarea id="ct_notes" name="ct_notes" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                  placeholder="Résultats de l'analyse scanner...">{{ old('ct_notes') }}</textarea>
                                    </div>
                                    
                                    <div class="md:col-span-3">
                                        <label for="ultrasound_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes Échographie
                                        </label>
                                        <textarea id="ultrasound_notes" name="ultrasound_notes" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                  placeholder="Résultats de l'analyse échographique...">{{ old('ultrasound_notes') }}</textarea>
                                    </div>
                                                            </div>
                            
                            <!-- DICOM Viewer Section -->
                            <div class="mt-6 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-green-900">🔬 Visualiseur DICOM</h4>
                                </div>
                                
                                <div class="space-y-4">
                                    <p class="text-sm text-green-700">
                                        Visualisez les fichiers d'imagerie médicale (DICOM, images) avec des outils d'analyse intégrés
                                    </p>
                                    
                                    <!-- File Selection for Viewer -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Sélectionner un fichier à visualiser
                                            </label>
                                            <select id="dicom-viewer-select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                <option value="">Choisir un fichier...</option>
                                                <option value="ecg">ECG - Fichier sélectionné</option>
                                                <option value="mri">IRM - Fichier sélectionné</option>
                                                <option value="xray">Radiographie - Fichier sélectionné</option>
                                                <option value="ct">Scanner CT - Fichier sélectionné</option>
                                                <option value="ultrasound">Échographie - Fichier sélectionné</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Outils de visualisation
                                            </label>
                                            <div class="flex space-x-2">
                                                <button type="button" id="viewer-zoom-in" class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                                                    🔍+
                                                </button>
                                                <button type="button" id="viewer-zoom-out" class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                                                    🔍-
                                                </button>
                                                <button type="button" id="viewer-reset" class="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                                                    🔄
                                                </button>
                                                <button type="button" id="viewer-fullscreen" class="px-3 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
                                                    ⛶
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Mesures
                                            </label>
                                            <div class="flex space-x-2">
                                                <button type="button" id="viewer-measure" class="px-3 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                                                    📏
                                                </button>
                                                <button type="button" id="viewer-annotate" class="px-3 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition-colors">
                                                    ✏️
                                                </button>
                                                <button type="button" id="viewer-screenshot" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                                    📸
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- DICOM Viewer Container -->
                                    <div class="bg-black rounded-lg overflow-hidden">
                                        <div id="dicom-viewer-container" class="relative w-full h-96 bg-gray-900 flex items-center justify-center">
                                            <div id="dicom-loading" class="hidden text-white text-center">
                                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-2"></div>
                                                <p>Chargement de l'image...</p>
                                            </div>
                                            
                                            <div id="dicom-error" class="hidden text-red-400 text-center">
                                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                                <p>Erreur de chargement</p>
                                            </div>
                                            
                                            <div id="dicom-placeholder" class="text-gray-400 text-center">
                                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="text-lg font-semibold">Visualiseur DICOM</p>
                                                <p class="text-sm">Sélectionnez un fichier pour commencer</p>
                                            </div>
                                            
                                            <canvas id="dicom-canvas" class="hidden max-w-full max-h-full object-contain"></canvas>
                                        </div>
                                        
                                        <!-- Viewer Controls -->
                                        <div class="bg-gray-800 p-4">
                                            <div class="flex items-center justify-between text-white">
                                                <div class="flex items-center space-x-4">
                                                    <span id="dicom-info" class="text-sm">Aucun fichier sélectionné</span>
                                                    <span id="dicom-dimensions" class="text-sm text-gray-300"></span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span id="dicom-zoom" class="text-sm">100%</span>
                                                    <input type="range" id="dicom-zoom-slider" min="25" max="400" value="100" class="w-20">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- DICOM Metadata Panel -->
                                    <div id="dicom-metadata" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-gray-900 mb-3">📋 Métadonnées DICOM</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p><strong>Patient:</strong> <span id="dicom-patient-name">-</span></p>
                                                <p><strong>ID Patient:</strong> <span id="dicom-patient-id">-</span></p>
                                                <p><strong>Date d'examen:</strong> <span id="dicom-study-date">-</span></p>
                                                <p><strong>Modality:</strong> <span id="dicom-modality">-</span></p>
                                            </div>
                                            <div>
                                                <p><strong>Institution:</strong> <span id="dicom-institution">-</span></p>
                                                <p><strong>Médecin:</strong> <span id="dicom-physician">-</span></p>
                                                <p><strong>Description:</strong> <span id="dicom-description">-</span></p>
                                                <p><strong>Dimensions:</strong> <span id="dicom-image-dimensions">-</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Measurement Tools -->
                                    <div id="dicom-measurements" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-gray-900 mb-3">📏 Outils de Mesure</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Distance</label>
                                                <div class="flex space-x-2">
                                                    <button type="button" id="measure-distance" class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                                                        📏 Distance
                                                    </button>
                                                    <span id="distance-result" class="text-sm text-gray-600">-</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Angle</label>
                                                <div class="flex space-x-2">
                                                    <button type="button" id="measure-angle" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">
                                                        📐 Angle
                                                    </button>
                                                    <span id="angle-result" class="text-sm text-gray-600">-</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Surface</label>
                                                <div class="flex space-x-2">
                                                    <button type="button" id="measure-area" class="px-3 py-1 bg-purple-500 text-white rounded text-sm hover:bg-purple-600">
                                                        📐 Surface
                                                    </button>
                                                    <span id="area-result" class="text-sm text-gray-600">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- AI Analysis Section -->
                            <div class="mt-6 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-purple-900">🤖 Analyse IA - Med-Gemini</h4>
                                </div>
                                
                                <div class="space-y-4">
                                    <p class="text-sm text-purple-700">
                                        Analyse automatique des fichiers ECG et IRM pour détecter les anomalies et évaluer l'âge osseux
                                    </p>
                                    
                                    <div class="flex flex-wrap gap-3">
                                        <button type="button" id="ai-check-ecg" 
                                                class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            🔍 Analyser ECG
                                        </button>
                                        
                                        <button type="button" id="ai-check-mri" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                            </svg>
                                            🧠 Analyser IRM (Âge Osseux)
                                        </button>
                                        
                                        <button type="button" id="ai-check-xray" 
                                                class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                            </svg>
                                            📷 Analyser X-Ray
                                        </button>
                                        
                                        <button type="button" id="ai-check-ct" 
                                                class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            🖥️ Analyser CT
                                        </button>
                                        
                                        <button type="button" id="ai-check-ultrasound" 
                                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                            </svg>
                                            🔊 Analyser Échographie
                                        </button>
                                        
                                        <button type="button" id="ai-check-all" 
                                                class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                            </svg>
                                            🚀 Analyse Complète
                                        </button>
                                    </div>
                                    
                                    <!-- AI Analysis Results -->
                                    <div id="ai-analysis-results" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-gray-900 mb-3">📊 Résultats de l'Analyse IA</h5>
                                        <div id="ai-results-content" class="space-y-3">
                                            <!-- Results will be populated here -->
                                        </div>
                                    </div>
                                    

                                    
                                    <!-- AI Analysis Status -->
                                    <div id="ai-analysis-status" class="hidden">
                                        <div class="flex items-center text-sm">
                                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-600 mr-2"></div>
                                            <span class="text-purple-600">Analyse en cours...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cardiovascular Assessment Section -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-blue-900">❤️ Évaluation Cardiovasculaire</h3>
                            </div>
                            
                            <!-- ECG Diagram -->
                            <div class="text-center mb-6">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 inline-block">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Électrocardiogramme (ECG)</h4>
                                    <svg width="300" height="80" class="mx-auto">
                                        <defs>
                                            <linearGradient id="ecgGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                                                <stop offset="50%" style="stop-color:#1e40af;stop-opacity:1" />
                                                <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
                                            </linearGradient>
                                        </defs>
                                        <!-- ECG Waveform -->
                                        <path d="M10,40 Q20,20 30,40 Q40,60 50,40 Q60,20 70,40 Q80,60 90,40 Q100,20 110,40 Q120,60 130,40 Q140,20 150,40 Q160,60 170,40 Q180,20 190,40 Q200,60 210,40 Q220,20 230,40 Q240,60 250,40 Q260,20 270,40 Q280,60 290,40" 
                                              stroke="url(#ecgGradient)" stroke-width="3" fill="none"/>
                                        <!-- P Wave -->
                                        <circle cx="30" cy="35" r="2" fill="#3b82f6"/>
                                        <!-- QRS Complex -->
                                        <path d="M50,40 L50,20 L55,20 L55,60 L60,60 L60,40" stroke="#1e40af" stroke-width="2" fill="none"/>
                                        <!-- T Wave -->
                                        <path d="M70,40 Q75,25 80,40" stroke="#3b82f6" stroke-width="2" fill="none"/>
                                    </svg>
                                    <p class="text-xs text-gray-500 mt-2">Rythme sinusal normal - 65 bpm</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="cardiac_rhythm" class="block text-sm font-medium text-gray-700 mb-2">
                                        Rythme Cardiaque
                                    </label>
                                    <select id="cardiac_rhythm" name="cardiac_rhythm" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="sinus" {{ old('cardiac_rhythm') == 'sinus' ? 'selected' : '' }}>Rythme sinusal</option>
                                        <option value="irregular" {{ old('cardiac_rhythm') == 'irregular' ? 'selected' : '' }}>Rythme irrégulier</option>
                                        <option value="arrhythmia" {{ old('cardiac_rhythm') == 'arrhythmia' ? 'selected' : '' }}>Arythmie</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="heart_murmur" class="block text-sm font-medium text-gray-700 mb-2">
                                        Souffle Cardiaque
                                    </label>
                                    <select id="heart_murmur" name="heart_murmur" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="none" {{ old('heart_murmur') == 'none' ? 'selected' : '' }}>Aucun</option>
                                        <option value="systolic" {{ old('heart_murmur') == 'systolic' ? 'selected' : '' }}>Systolique</option>
                                        <option value="diastolic" {{ old('heart_murmur') == 'diastolic' ? 'selected' : '' }}>Diastolique</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="blood_pressure_rest" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tension au Repos
                                    </label>
                                    <input type="text" id="blood_pressure_rest" name="blood_pressure_rest" 
                                           value="{{ old('blood_pressure_rest') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="120/80 mmHg">
                                </div>
                                
                                <div>
                                    <label for="blood_pressure_exercise" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tension à l'Effort
                                    </label>
                                    <input type="text" id="blood_pressure_exercise" name="blood_pressure_exercise" 
                                           value="{{ old('blood_pressure_exercise') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="140/85 mmHg">
                                </div>
                            </div>
                        </div>

                        <!-- Neurological Assessment Section -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-purple-900">🧠 Évaluation Neurologique</h3>
                            </div>
                            
                            <!-- Brain Diagram -->
                            <div class="text-center mb-6">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 inline-block">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Anatomie Cérébrale</h4>
                                    <svg width="200" height="120" class="mx-auto">
                                        <!-- Brain Outline -->
                                        <path d="M100,20 Q120,30 130,50 Q135,70 130,90 Q120,110 100,120 Q80,110 70,90 Q65,70 70,50 Q80,30 100,20" 
                                              stroke="#8b5cf6" stroke-width="2" fill="#f3e8ff"/>
                                        <!-- Brain Lobes -->
                                        <path d="M85,40 Q95,35 105,40 Q110,50 105,60 Q95,65 85,60 Q80,50 85,40" 
                                              stroke="#7c3aed" stroke-width="1" fill="#ddd6fe"/>
                                        <path d="M95,70 Q105,65 115,70 Q120,80 115,90 Q105,95 95,90 Q90,80 95,70" 
                                              stroke="#7c3aed" stroke-width="1" fill="#ddd6fe"/>
                                        <!-- Brain Stem -->
                                        <rect x="95" y="100" width="10" height="15" fill="#7c3aed"/>
                                        <!-- Labels -->
                                        <text x="50" y="35" class="text-xs" fill="#6b7280">Frontal</text>
                                        <text x="140" y="85" class="text-xs" fill="#6b7280">Occipital</text>
                                        <text x="100" y="125" class="text-xs" fill="#6b7280">Tronc</text>
                                    </svg>
                                    <p class="text-xs text-gray-500 mt-2">Examen neurologique normal</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="consciousness" class="block text-sm font-medium text-gray-700 mb-2">
                                        Niveau de Conscience
                                    </label>
                                    <select id="consciousness" name="consciousness" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="alert" {{ old('consciousness') == 'alert' ? 'selected' : '' }}>Vigile</option>
                                        <option value="confused" {{ old('consciousness') == 'confused' ? 'selected' : '' }}>Confus</option>
                                        <option value="drowsy" {{ old('consciousness') == 'drowsy' ? 'selected' : '' }}>Somnolent</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="cranial_nerves" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nerfs Crâniens
                                    </label>
                                    <select id="cranial_nerves" name="cranial_nerves" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('cranial_nerves') == 'normal' ? 'selected' : '' }}>Normaux</option>
                                        <option value="abnormal" {{ old('cranial_nerves') == 'abnormal' ? 'selected' : '' }}>Anormaux</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="motor_function" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fonction Motrice
                                    </label>
                                    <select id="motor_function" name="motor_function" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('motor_function') == 'normal' ? 'selected' : '' }}>Normale</option>
                                        <option value="weakness" {{ old('motor_function') == 'weakness' ? 'selected' : '' }}>Faiblesse</option>
                                        <option value="paralysis" {{ old('motor_function') == 'paralysis' ? 'selected' : '' }}>Paralysie</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="sensory_function" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fonction Sensitive
                                    </label>
                                    <select id="sensory_function" name="sensory_function" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('sensory_function') == 'normal' ? 'selected' : '' }}>Normale</option>
                                        <option value="decreased" {{ old('sensory_function') == 'decreased' ? 'selected' : '' }}>Diminuée</option>
                                        <option value="absent" {{ old('sensory_function') == 'absent' ? 'selected' : '' }}>Absente</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Musculoskeletal Assessment Section -->
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-orange-900">💪 Évaluation Musculo-squelettique</h3>
                            </div>
                            
                            <!-- Body Diagram -->
                            <div class="text-center mb-6">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 inline-block">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Anatomie Musculo-squelettique</h4>
                                    <svg width="150" height="200" class="mx-auto">
                                        <!-- Head -->
                                        <circle cx="75" cy="20" r="15" fill="#f97316" stroke="#ea580c" stroke-width="1"/>
                                        <!-- Neck -->
                                        <rect x="70" y="35" width="10" height="15" fill="#f97316"/>
                                        <!-- Torso -->
                                        <rect x="50" y="50" width="50" height="60" fill="#f97316" stroke="#ea580c" stroke-width="1"/>
                                        <!-- Arms -->
                                        <rect x="20" y="60" width="8" height="40" fill="#f97316"/>
                                        <rect x="122" y="60" width="8" height="40" fill="#f97316"/>
                                        <!-- Legs -->
                                        <rect x="60" y="110" width="8" height="50" fill="#f97316"/>
                                        <rect x="82" y="110" width="8" height="50" fill="#f97316"/>
                                        <!-- Joints -->
                                        <circle cx="75" cy="50" r="3" fill="#ea580c"/>
                                        <circle cx="24" cy="100" r="3" fill="#ea580c"/>
                                        <circle cx="126" cy="100" r="3" fill="#ea580c"/>
                                        <circle cx="64" cy="160" r="3" fill="#ea580c"/>
                                        <circle cx="86" cy="160" r="3" fill="#ea580c"/>
                                        <!-- Labels -->
                                        <text x="75" y="15" class="text-xs" fill="#6b7280">Tête</text>
                                        <text x="75" y="85" class="text-xs" fill="#6b7280">Tronc</text>
                                        <text x="15" y="85" class="text-xs" fill="#6b7280">Bras</text>
                                        <text x="130" y="85" class="text-xs" fill="#6b7280">Bras</text>
                                        <text x="55" y="140" class="text-xs" fill="#6b7280">Jambe</text>
                                        <text x="85" y="140" class="text-xs" fill="#6b7280">Jambe</text>
                                    </svg>
                                    <p class="text-xs text-gray-500 mt-2">Examen musculo-squelettique normal</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="joint_mobility" class="block text-sm font-medium text-gray-700 mb-2">
                                        Mobilité Articulaire
                                    </label>
                                    <select id="joint_mobility" name="joint_mobility" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('joint_mobility') == 'normal' ? 'selected' : '' }}>Normale</option>
                                        <option value="limited" {{ old('joint_mobility') == 'limited' ? 'selected' : '' }}>Limitée</option>
                                        <option value="restricted" {{ old('joint_mobility') == 'restricted' ? 'selected' : '' }}>Restreinte</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="muscle_strength" class="block text-sm font-medium text-gray-700 mb-2">
                                        Force Musculaire
                                    </label>
                                    <select id="muscle_strength" name="muscle_strength" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('muscle_strength') == 'normal' ? 'selected' : '' }}>Normale</option>
                                        <option value="reduced" {{ old('muscle_strength') == 'reduced' ? 'selected' : '' }}>Réduite</option>
                                        <option value="weak" {{ old('muscle_strength') == 'weak' ? 'selected' : '' }}>Faible</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="pain_assessment" class="block text-sm font-medium text-gray-700 mb-2">
                                        Évaluation de la Douleur
                                    </label>
                                    <select id="pain_assessment" name="pain_assessment" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="none" {{ old('pain_assessment') == 'none' ? 'selected' : '' }}>Aucune</option>
                                        <option value="mild" {{ old('pain_assessment') == 'mild' ? 'selected' : '' }}>Légère</option>
                                        <option value="moderate" {{ old('pain_assessment') == 'moderate' ? 'selected' : '' }}>Modérée</option>
                                        <option value="severe" {{ old('pain_assessment') == 'severe' ? 'selected' : '' }}>Sévère</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="range_of_motion" class="block text-sm font-medium text-gray-700 mb-2">
                                        Amplitude de Mouvement
                                    </label>
                                    <select id="range_of_motion" name="range_of_motion" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="full" {{ old('range_of_motion') == 'full' ? 'selected' : '' }}>Complète</option>
                                        <option value="limited" {{ old('range_of_motion') == 'limited' ? 'selected' : '' }}>Limitée</option>
                                        <option value="restricted" {{ old('range_of_motion') == 'restricted' ? 'selected' : '' }}>Restreinte</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- FIFA Compliance Section -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-green-900">🏆 Conformité FIFA</h3>
                            </div>
                            <p class="text-green-700 mb-4">Informations requises pour la conformité FIFA</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- FIFA ID -->
                                <div>
                                    <label for="fifa_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        ID FIFA
                                    </label>
                                    <input 
                                        type="text" 
                                        id="fifa_id" 
                                        name="fifa_id" 
                                        value="{{ old('fifa_id') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="FIFA-2024-001"
                                    >
                                </div>

                                <!-- Competition Name -->
                                <div>
                                    <label for="competition_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nom de la compétition
                                    </label>
                                    <input 
                                        type="text" 
                                        id="competition_name" 
                                        name="competition_name" 
                                        value="{{ old('competition_name') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Championnat National 2024"
                                    >
                                </div>

                                <!-- Competition Date -->
                                <div>
                                    <label for="competition_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Date de la compétition
                                    </label>
                                    <input 
                                        type="date" 
                                        id="competition_date" 
                                        name="competition_date" 
                                        value="{{ old('competition_date') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                </div>

                                <!-- Team Name -->
                                <div>
                                    <label for="team_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nom de l'équipe
                                    </label>
                                    <input 
                                        type="text" 
                                        id="team_name" 
                                        name="team_name" 
                                        value="{{ old('team_name') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Équipe nationale"
                                    >
                                </div>

                                <!-- Position -->
                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                                        Poste du joueur
                                    </label>
                                    <select 
                                        id="position" 
                                        name="position" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                        <option value="">Sélectionner le poste</option>
                                        <option value="goalkeeper" {{ old('position') == 'goalkeeper' ? 'selected' : '' }}>Gardien</option>
                                        <option value="defender" {{ old('position') == 'defender' ? 'selected' : '' }}>Défenseur</option>
                                        <option value="midfielder" {{ old('position') == 'midfielder' ? 'selected' : '' }}>Milieu</option>
                                        <option value="forward" {{ old('position') == 'forward' ? 'selected' : '' }}>Attaquant</option>
                                    </select>
                                </div>

                                <!-- FIFA Compliant Checkbox -->
                                <div class="md:col-span-2">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            id="fifa_compliant" 
                                            name="fifa_compliant" 
                                            value="1"
                                            {{ old('fifa_compliant') ? 'checked' : '' }}
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                        >
                                        <label for="fifa_compliant" class="ml-2 block text-sm font-medium text-gray-700">
                                            ✅ Conforme aux standards FIFA
                                        </label>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">Cochez cette case si l'évaluation respecte tous les critères FIFA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Football Fitness Assessment -->
                <div class="mt-6 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h4 class="text-lg font-semibold text-purple-900">⚽ Évaluation Fitness Football Professionnel</h4>
                    </div>
                    
                    <p class="text-sm text-purple-700 mb-4">
                        Analyse complète de l'aptitude du joueur pour le football professionnel basée sur tous les examens médicaux
                    </p>
                    
                    <button type="button" id="ai-fitness-assessment" onclick="window.generateFitnessAssessment()"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        🤖 Générer Rapport Fitness Professionnel
                    </button>
                    
                    <!-- Fitness Assessment Results -->
                    <div id="fitness-assessment-results" class="hidden mt-4 bg-white border border-purple-200 rounded-lg p-4">
                        <h5 class="font-semibold text-purple-900 mb-3">📋 Rapport d'Évaluation Fitness</h5>
                        <div id="fitness-results-content" class="space-y-3">
                            <!-- Fitness assessment results will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- PDF and Print Actions -->
                <div class="mt-6 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h4 class="text-lg font-semibold text-green-900">📄 Export et Impression</h4>
                    </div>
                    
                    <p class="text-sm text-green-700 mb-4">
                        Générez des rapports PDF et imprimez les évaluations médicales
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <button type="button" id="generate-pdf" onclick="window.generatePDF()"
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            📄 Générer PDF
                        </button>
                        
                        <button type="button" id="print-report" onclick="window.printReport()"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            🖨️ Imprimer Rapport
                        </button>
                        

                        

                        
                        <button type="button" id="doctor-signoff" onclick="console.log('🔘 Doctor Sign-Off button clicked!'); window.openDoctorSignoff()"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            👨‍⚕️ Signature Médecin
                        </button>
                    </div>
                    
                    <!-- Export Status -->
                    <div id="export-status" class="hidden mt-4">
                        <div class="flex items-center text-sm">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-green-600 mr-2"></div>
                            <span class="text-green-600">Génération en cours...</span>
                        </div>
                    </div>
                    

                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('pcma.dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        ← Retour au Dashboard
                    </a>
                    
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        💾 Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Voice Recording Section -->
        <div id="voice-section" class="input-section hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">🎤 Enregistrement vocal avec Whisper</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">Enregistrez votre évaluation médicale et elle sera automatiquement transcrite</p>
                        
                        <div class="flex justify-center space-x-4 mb-6">
                            <button type="button" id="start-recording" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-full transition duration-200">
                                🎤 Commencer l'enregistrement
                            </button>
                            <button type="button" id="stop-recording" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-full transition duration-200 hidden">
                                ⏹️ Arrêter l'enregistrement
                            </button>
                        </div>
                        
                        <div id="recording-status" class="text-sm text-gray-500 mb-4"></div>
                        
                        <div id="audio-visualizer" class="hidden w-full h-20 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                            <div class="flex space-x-1">
                                <div class="w-1 h-4 bg-blue-500 rounded animate-pulse"></div>
                                <div class="w-1 h-6 bg-blue-500 rounded animate-pulse"></div>
                                <div class="w-1 h-8 bg-blue-500 rounded animate-pulse"></div>
                                <div class="w-1 h-6 bg-blue-500 rounded animate-pulse"></div>
                                <div class="w-1 h-4 bg-blue-500 rounded animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="transcription-result" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Transcription</h3>
                        <textarea id="transcribed-text" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="La transcription apparaîtra ici..."></textarea>
                        
                        <div class="flex space-x-4 mt-4">
                            <button type="button" id="process-transcription" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                🔍 Analyser avec l'IA
                            </button>
                            <button type="button" id="clear-transcription" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Effacer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FHIR Download Section -->
        <div id="fhir-section" class="input-section hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">📥 Téléchargement depuis FHIR</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <label for="fhir_server_url" class="block text-sm font-medium text-gray-700 mb-2">
                            URL du serveur FHIR
                        </label>
                        <input type="url" id="fhir_server_url" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="https://hapi.fhir.org/baseR4/">
                    </div>
                    
                    <div>
                        <label for="fhir_patient_id" class="block text-sm font-medium text-gray-700 mb-2">
                            ID du patient FHIR
                        </label>
                        <input type="text" id="fhir_patient_id" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="patient-123">
                    </div>
                    
                    <div>
                        <label for="fhir_resource_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de ressource
                        </label>
                        <select id="fhir_resource_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Observation">Observation (Observations médicales)</option>
                            <option value="Condition">Condition (Diagnostics)</option>
                            <option value="Procedure">Procedure (Procédures)</option>
                            <option value="MedicationRequest">MedicationRequest (Prescriptions)</option>
                        </select>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="button" id="fetch-fhir-data" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            🔍 Récupérer les données FHIR
                        </button>
                        <button type="button" id="clear-fhir" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            Effacer
                        </button>
                    </div>
                    
                    <div id="fhir-results" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Données FHIR récupérées</h3>
                        <div id="fhir-content" class="bg-gray-50 rounded-lg p-4 max-h-64 overflow-y-auto"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Scan Section -->
        <div id="scan-section" class="input-section hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">📷 Scan d'image avec OCR</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">Téléchargez une image de document médical pour extraction automatique</p>
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 mb-4">
                            <input type="file" id="image-upload" accept="image/*" class="hidden">
                            <label for="image-upload" class="cursor-pointer">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-gray-600">Cliquez pour sélectionner une image</p>
                                    <p class="text-sm text-gray-500">PNG, JPG, PDF jusqu'à 10MB</p>
                                </div>
                            </label>
                        </div>
                        
                        <div id="image-preview" class="hidden mb-4">
                            <img id="preview-img" class="max-w-md mx-auto rounded-lg shadow-md" alt="Aperçu">
                        </div>
                        
                        <div class="flex space-x-4">
                            <button type="button" id="process-image" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                🔍 Extraire le texte (OCR)
                            </button>
                            <button type="button" id="clear-image" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Effacer
                            </button>
                        </div>
                    </div>
                    
                    <div id="ocr-results" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Texte extrait</h3>
                        <textarea id="extracted-text" rows="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Le texte extrait apparaîtra ici..."></textarea>
                        
                        <div class="flex space-x-4 mt-4">
                            <button type="button" id="process-ocr-text" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                🔍 Analyser avec l'IA
                            </button>
                            <button type="button" id="clear-ocr" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Effacer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor Sign-Off Modal -->
        <div id="doctor-signoff-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl shadow-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">👨‍⚕️ Signature Médecin - PCMA</h3>
                        <button onclick="closeDoctorSignoff()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                                <!-- DoctorSignOff Component Container -->
            <div id="doctor-signoff-container">
                <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-6">
                    <!-- Header -->
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">🏥 Medical Fitness Assessment - Doctor Sign-Off</h2>
                        <p class="text-gray-600 mt-2">Final review and digital signature required for medical clearance</p>
                    </div>

                    <!-- Summary Block -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">📋 Assessment Summary</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700">Player Name:</span>
                                <span class="ml-2 text-gray-900" id="signoff-player-name">Loading...</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Fitness Decision:</span>
                                <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold" id="signoff-fitness-decision">Loading...</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Date of Examination:</span>
                                <span class="ml-2 text-gray-900" id="signoff-examination-date">Loading...</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Assessment ID:</span>
                                <span class="ml-2 text-gray-900" id="signoff-assessment-id">Loading...</span>
                            </div>
                        </div>
                        
                        <div class="mt-3" id="signoff-clinical-notes-container" style="display: none;">
                            <span class="font-semibold text-gray-700">Clinical Notes:</span>
                            <p class="mt-1 text-gray-700 text-sm" id="signoff-clinical-notes"></p>
                        </div>
                    </div>

                    <!-- Legal Declaration -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-3">⚖️ Legal Declaration</h3>
                        <div class="flex items-start space-x-3">
                            <input 
                                type="checkbox" 
                                id="legal-declaration"
                                class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="legal-declaration" class="text-sm text-gray-700 leading-relaxed">
                                I, the undersigned medical professional, confirm that I have reviewed the complete clinical information 
                                and assume full responsibility for the fitness decision rendered herein. I understand that this assessment 
                                will be used for professional football eligibility determination and I certify that all information provided 
                                is accurate to the best of my medical knowledge.
                            </label>
                        </div>
                    </div>

                    <!-- Signature Capture -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">✍️ Digital Signature</h3>
                        
                        <!-- Signature Canvas -->
                        <div class="border border-gray-300 rounded-lg bg-white p-4">
                            <canvas 
                                id="signature-canvas"
                                class="border border-gray-300 rounded w-full h-48 cursor-crosshair"
                            ></canvas>
                            
                            <!-- Signature Controls -->
                            <div class="flex justify-between items-center mt-3">
                                <div class="text-sm text-gray-600" id="signature-status">
                                    No signature captured
                                </div>
                                <div class="flex space-x-2">
                                    <button 
                                        id="clear-signature"
                                        class="px-3 py-1 text-sm bg-gray-500 hover:bg-gray-600 text-white rounded transition duration-200"
                                    >
                                        🗑️ Clear
                                    </button>
                                    <button 
                                        id="confirm-signature"
                                        class="px-3 py-1 text-sm bg-green-600 hover:bg-green-700 text-white rounded transition duration-200"
                                        disabled
                                    >
                                        ✅ Confirm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Information -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-3">👨‍⚕️ Doctor Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700">Doctor Name:</span>
                                <input type="text" id="signoff-doctor-name-input" 
                                       class="ml-2 px-2 py-1 border border-gray-300 rounded text-gray-900 text-sm"
                                       placeholder="Enter doctor name"
                                       value="Dr. Médecin Responsable">
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">License Number:</span>
                                <span class="ml-2 text-gray-900" id="signoff-license-number">Loading...</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Timestamp:</span>
                                <span class="ml-2 text-gray-900" id="signoff-timestamp">Loading...</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">IP Address:</span>
                                <span class="ml-2 text-gray-900" id="signoff-ip-address">192.168.1.100</span>
                            </div>
                        </div>
                    </div>

                    <!-- Final Action -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <div class="text-sm text-gray-500" id="action-status">
                            Legal declaration required
                        </div>
                        <button 
                            id="confirm-signoff"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 flex items-center"
                            disabled
                            onclick="console.log('🔘 Direct onclick handler triggered!');"
                        >
                            <svg id="loading-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="confirm-signoff-text">Confirm and Sign</span>
                        </button>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
console.log('🚀 SCRIPT TAG STARTING...');



// Define functions immediately at the top of the script
window.generatePDF = async function() {
    try {
        // Collect all form data
        const formData = new FormData();
        
        // Get all form inputs
        const pdfForm = document.querySelector('form');
        const formElements = pdfForm.elements;
        
        // Add all form fields to FormData
        console.log('📋 Collecting form data...');
        console.log('📋 Form elements found:', formElements.length);
        
        for (let element of formElements) {
            console.log('📋 Element:', element.name, '=', element.value, 'type:', element.type);
            if (element.name) {
                formData.append(element.name, element.value || '');
                console.log('📋 Added field:', element.name, '=', element.value || '');
            } else {
                console.log('📋 Skipped field without name:', element.id || element.type);
            }
        }
        console.log('📋 Total form fields collected:', formData.entries().length);
        
        // Add required fields if they're missing (same as in saveSignedPCMA)
        if (!formData.has('athlete_id') || !formData.get('athlete_id')) {
            formData.append('athlete_id', '1');
        }
        if (!formData.has('type') || !formData.get('type')) {
            formData.append('type', 'bpma');
        }
        if (!formData.has('assessor_id') || !formData.get('assessor_id')) {
            formData.append('assessor_id', '1');
        }
        if (!formData.has('assessment_date') || !formData.get('assessment_date')) {
            formData.append('assessment_date', new Date().toISOString().split('T')[0]);
        }
        if (!formData.has('status') || !formData.get('status')) {
            formData.append('status', 'completed');
        }
        // Only add default values if the fields are completely empty
        if (!formData.has('notes') || formData.get('notes') === '') {
            formData.append('notes', 'Évaluation médicale complétée');
            }
        if (!formData.has('clinical_notes') || formData.get('clinical_notes') === '') {
            formData.append('clinical_notes', 'Notes cliniques standard');
        }
        
        // Add fitness assessment results if available
        const fitnessResults = document.getElementById('fitness-results-content');
        if (fitnessResults && fitnessResults.innerHTML.trim()) {
            formData.append('fitness_assessment_results', fitnessResults.innerHTML);
        }
        
        // Add signature data if available
        if (window.signedPCMAData) {
            formData.append('signature_data', JSON.stringify(window.signedPCMAData));
            formData.append('is_signed', '1');
            formData.append('signed_by', window.signedPCMAData.signedBy);
            formData.append('license_number', window.signedPCMAData.licenseNumber);
            formData.append('signed_at', window.signedPCMAData.signedAt);
            formData.append('signature_image', window.signedPCMAData.signatureImage);
            formData.append('legal_declaration', 'confirmed');
            formData.append('signature_confirmed', 'true');
            console.log('📋 Adding signature data to PDF:', window.signedPCMAData);
        } else {
            formData.append('is_signed', '0');
            formData.append('legal_declaration', 'not_confirmed');
            formData.append('signature_confirmed', 'false');
            console.log('⚠️ No signature data available for PDF');
        }
        
        // Send to PDF generation endpoint
        console.log('📄 Sending PDF generation request to:', '{{ route("pcma.pdf.post") }}');
        console.log('📄 FormData entries:', formData.entries().length);
        
        // Test if the route exists
        console.log('📄 Testing route availability...');
        
        // Add CSRF token to FormData - get fresh token
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // If token is empty or invalid, try to get a fresh one
        if (!csrfToken || csrfToken === '') {
            console.log('📄 CSRF token is empty, attempting to refresh...');
            // Try to get a fresh token by making a request to the home page
            try {
                const tokenResponse = await fetch('/', { method: 'GET' });
                const tokenHtml = await tokenResponse.text();
                const tokenMatch = tokenHtml.match(/<meta name="csrf-token" content="([^"]+)"/);
                if (tokenMatch) {
                    csrfToken = tokenMatch[1];
                    console.log('📄 Fresh CSRF token obtained');
                }
            } catch (e) {
                console.error('📄 Failed to refresh CSRF token:', e);
            }
        }
        
        formData.append('_token', csrfToken);
        
        const response = await fetch('/api/pcma/pdf', {
            method: 'POST',
            body: formData
        });
        
        console.log('📄 Response status:', response.status);
        console.log('📄 Response ok:', response.ok);
        
        if (response.ok) {
            // Check if response is JSON (error) or PDF
            const contentType = response.headers.get('content-type');
            console.log('📄 Response content-type:', contentType);
            
            if (contentType && contentType.includes('application/json')) {
                // It's a JSON error response
                const errorData = await response.json();
                throw new Error(errorData.message || 'Unknown error');
            } else {
                // It's a PDF response
                try {
            const blob = await response.blob();
                    console.log('📄 Blob size:', blob.size, 'bytes');
                    console.log('📄 Blob type:', blob.type);
                    
                    if (blob.size === 0) {
                        throw new Error('PDF is empty (0 bytes)');
                    }
                    
                    // Check if it's actually a PDF
                    if (blob.type && !blob.type.includes('pdf') && !blob.type.includes('application/octet-stream')) {
                        console.warn('📄 Warning: Blob type is not PDF:', blob.type);
                    }
                    
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `PCMA_Assessment_${new Date().toISOString().split('T')[0]}.pdf`;
                    a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
                    
                    // Clean up after a delay
                    setTimeout(() => {
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
                    }, 100);
                    
                    console.log('📄 PDF download initiated');
                    
                    // Fallback: If download doesn't work, open in new tab
                    setTimeout(() => {
                        console.log('📄 Attempting fallback - opening PDF in new tab');
                        const newWindow = window.open(url, '_blank');
                        if (!newWindow) {
                            alert('📄 PDF generated but download blocked. Please check your browser settings.');
                        }
                    }, 2000);
                    
                } catch (blobError) {
                    console.error('📄 Error processing PDF blob:', blobError);
                    throw new Error('Failed to process PDF: ' + blobError.message);
                }
            }
            
        } else {
            // Try to get error message from response
            let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
            try {
                const errorData = await response.json();
                errorMessage = errorData.message || errorMessage;
            } catch (e) {
                // Could not parse JSON, use default error message
            }
            throw new Error(errorMessage);
        }
        
    } catch (error) {
        console.error('PDF Generation Error:', error);
        alert('❌ Erreur lors de la génération du PDF: ' + error.message);
    }
};

window.printReport = function() {
    try {
        console.log('🖨️ Print function called');
        console.log('📋 Signed data available:', window.signedPCMAData);
        
        // Create a print-friendly version of the form
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        
        // Get form data
        const printForm = document.querySelector('form');
        const formData = new FormData(printForm);
        const formDataObj = {};
        
        for (let [key, value] of formData.entries()) {
            formDataObj[key] = value;
        }
        
        // Create print-friendly HTML
        const printHTML = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>PCMA Assessment Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                    .section { margin-bottom: 20px; }
                    .section h3 { color: #2563eb; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                    .field { margin-bottom: 10px; }
                    .field label { font-weight: bold; display: inline-block; width: 200px; }
                    .field value { display: inline-block; }
                    .fitness-results { background: #f3f4f6; padding: 15px; border-radius: 5px; margin-top: 20px; }
                    @media print {
                        body { margin: 0; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>📋 Rapport d'Évaluation PCMA</h1>
                    <p>Date: ${new Date().toLocaleDateString('fr-FR')}</p>
                </div>
                
                <div class="section">
                    <h3>👤 Informations du Patient</h3>
                    <div class="field">
                        <label>Athlète:</label>
                        <value>${formDataObj.athlete_id || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Type d'évaluation:</label>
                        <value>${formDataObj.type || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Date d'évaluation:</label>
                        <value>${formDataObj.assessment_date || 'Non spécifié'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>📊 Signes Vitaux</h3>
                    <div class="field">
                        <label>Tension Artérielle:</label>
                        <value>${formDataObj.blood_pressure || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Fréquence Cardiaque:</label>
                        <value>${formDataObj.heart_rate || 'Non spécifié'} bpm</value>
                    </div>
                    <div class="field">
                        <label>Température:</label>
                        <value>${formDataObj.temperature || 'Non spécifié'} °C</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>🏥 Antécédents Médicaux</h3>
                    <div class="field">
                        <label>Antécédents Cardio-vasculaires:</label>
                        <value>${formDataObj.cardiovascular_history || 'Aucun'}</value>
                    </div>
                    <div class="field">
                        <label>Antécédents Chirurgicaux:</label>
                        <value>${formDataObj.surgical_history || 'Aucun'}</value>
                    </div>
                    <div class="field">
                        <label>Médicaments Actuels:</label>
                        <value>${formDataObj.medications || 'Aucun'}</value>
                    </div>
                    <div class="field">
                        <label>Allergies:</label>
                        <value>${formDataObj.allergies || 'Aucune'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>🔍 Examen Physique</h3>
                    <div class="field">
                        <label>Apparence Générale:</label>
                        <value>${formDataObj.general_appearance || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Examen Cutané:</label>
                        <value>${formDataObj.skin_examination || 'Non spécifié'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>❤️ Évaluation Cardiovasculaire</h3>
                    <div class="field">
                        <label>Rythme Cardiaque:</label>
                        <value>${formDataObj.cardiac_rhythm || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Souffle Cardiaque:</label>
                        <value>${formDataObj.heart_murmur || 'Non spécifié'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>🧠 Évaluation Neurologique</h3>
                    <div class="field">
                        <label>Niveau de Conscience:</label>
                        <value>${formDataObj.consciousness || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Nerfs Crâniens:</label>
                        <value>${formDataObj.cranial_nerves || 'Non spécifié'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>💪 Évaluation Musculo-squelettique</h3>
                    <div class="field">
                        <label>Mobilité Articulaire:</label>
                        <value>${formDataObj.joint_mobility || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Force Musculaire:</label>
                        <value>${formDataObj.muscle_strength || 'Non spécifié'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>🏆 Conformité FIFA</h3>
                    <div class="field">
                        <label>ID FIFA:</label>
                        <value>${formDataObj.fifa_id || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Nom de la compétition:</label>
                        <value>${formDataObj.competition_name || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Conforme aux standards FIFA:</label>
                        <value>${formDataObj.fifa_compliant ? 'Oui' : 'Non'}</value>
                    </div>
                </div>
                
                <div class="fitness-results" id="print-fitness-results">
                    <!-- Fitness assessment results will be populated here if available -->
                </div>
                
                <div class="section">
                    <h3>📝 Notes</h3>
                    <p>${formDataObj.notes || 'Aucune note'}</p>
                </div>
                
                         ${window.signedPCMAData ? `
         <div class="section">
             <h3>👨‍⚕️ Signature Médicale</h3>
             <div class="field">
                 <label>Signé par:</label>
                 <value>${window.signedPCMAData.signedBy}</value>
             </div>
             <div class="field">
                 <label>Numéro de licence:</label>
                 <value>${window.signedPCMAData.licenseNumber}</value>
             </div>
             <div class="field">
                 <label>Date de signature:</label>
                 <value>${new Date(window.signedPCMAData.signedAt).toLocaleString('fr-FR')}</value>
             </div>
             <div class="field">
                 <label>Adresse IP:</label>
                 <value>${window.signedPCMAData.ipAddress}</value>
             </div>
             <div class="field">
                 <label>Statut de fitness:</label>
                 <value>${window.signedPCMAData.fitnessStatus}</value>
             </div>
             <div class="field">
                 <label>Signature:</label>
                 <div style="margin-top: 10px;">
                     ${window.signedPCMAData.signatureImage ? 
                         `<img src="${window.signedPCMAData.signatureImage}" alt="Signature médicale" style="max-width: 200px; border: 1px solid #ccc; padding: 5px;">` : 
                         '<p style="color: #666; font-style: italic;">Signature numérique capturée</p>'
                     }
                 </div>
             </div>
             <div class="field">
                 <label>Déclaration légale:</label>
                 <value style="font-style: italic; color: #666;">✓ Confirmée par le médecin signataire</value>
             </div>
             <div class="field">
                 <label>Validation:</label>
                 <value style="color: #059669; font-weight: bold;">✓ Document médical validé et signé</value>
             </div>
         </div>
         ` : `
         <div class="section">
             <h3>👨‍⚕️ Signature Médicale</h3>
             <p style="color: #ef4444; font-style: italic;">⚠️ Signature médicale non effectuée</p>
             <p style="color: #ef4444; font-style: italic;">⚠️ Déclaration légale non confirmée</p>
             <p style="color: #ef4444; font-style: italic;">⚠️ Document non validé</p>
         </div>
         `}
                
                <div class="section no-print">
                    <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        🖨️ Imprimer ce rapport
                    </button>
                </div>
            </body>
            </html>
        `;
        
        printWindow.document.write(printHTML);
        printWindow.document.close();
        
        // Add fitness results if available
        const fitnessResults = document.getElementById('fitness-results-content');
        if (fitnessResults && fitnessResults.innerHTML.trim()) {
            const printFitnessResults = printWindow.document.getElementById('print-fitness-results');
            if (printFitnessResults) {
                printFitnessResults.innerHTML = `
                    <h3>⚽ Évaluation Fitness Football Professionnel</h3>
                    ${fitnessResults.innerHTML}
                `;
            }
        }
        
        // Auto-print after a short delay
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
        }, 500);
        
    } catch (error) {
        console.error('Print Error:', error);
        alert('❌ Erreur lors de l\'ouverture de la fenêtre d\'impression: ' + error.message);
    }
};

// Global variable to store signed data
window.signedPCMAData = null;

// Test function to check signature data
window.testSignatureData = function() {
    console.log('🔍 Testing signature data...');
    console.log('Current signedPCMAData:', window.signedPCMAData);
    
    if (window.signedPCMAData) {
        alert('✅ Signature data is available!\n\n' + 
              'Signed by: ' + window.signedPCMAData.signedBy + '\n' +
              'License: ' + window.signedPCMAData.licenseNumber + '\n' +
              'Date: ' + window.signedPCMAData.signedAt + '\n' +
              'Status: ' + window.signedPCMAData.fitnessStatus);
    } else {
        alert('❌ No signature data available');
    }
};

window.openDoctorSignoff = function() {
    console.log('🚪 openDoctorSignoff function called');
    try {
        // Get form data for the signoff
        const signoffForm = document.querySelector('form');
        const formData = new FormData(signoffForm);
        const formDataObj = {};
        
        for (let [key, value] of formData.entries()) {
            formDataObj[key] = value;
        }
        
        // Get athlete information
        const athleteSelect = document.getElementById('athlete_id');
        const selectedAthlete = athleteSelect.options[athleteSelect.selectedIndex];
        const athleteName = selectedAthlete ? selectedAthlete.text : 'Athlète non spécifié';
        
        // Get fitness assessment results if available
        const fitnessResults = document.getElementById('fitness-results-content');
        const fitnessDecision = fitnessResults && fitnessResults.innerHTML.includes('APT') ? 'FIT' : 'NOT_FIT';
        
        // Get doctor/assessor information
        const assessorSelect = document.getElementById('assessor_id');
        const selectedAssessor = assessorSelect ? assessorSelect.options[assessorSelect.selectedIndex] : null;
        const doctorName = selectedAssessor ? selectedAssessor.text : 'Dr. Médecin Responsable';
        
        // Create signoff data
        const signoffData = {
            playerName: athleteName,
            fitnessDecision: fitnessDecision,
            examinationDate: formDataObj.assessment_date || new Date().toISOString().split('T')[0],
            assessmentId: 'PCMA-' + Date.now(),
            clinicalNotes: formDataObj.notes || 'Aucune note clinique',
            doctorName: doctorName,
            licenseNumber: 'MED-' + Math.floor(Math.random() * 10000)
        };
        
        // Show the modal
        console.log('🚪 Showing modal...');
        const modal = document.getElementById('doctor-signoff-modal');
        if (modal) {
            modal.classList.remove('hidden');
            console.log('🚪 Modal shown successfully');
        } else {
            console.error('❌ Modal element not found!');
        }
        
        // Populate the signoff form with data
        document.getElementById('signoff-player-name').textContent = signoffData.playerName;
        document.getElementById('signoff-fitness-decision').textContent = signoffData.fitnessDecision;
        document.getElementById('signoff-fitness-decision').className = signoffData.fitnessDecision === 'FIT' ? 
            'ml-2 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800' : 
            'ml-2 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800';
        document.getElementById('signoff-examination-date').textContent = signoffData.examinationDate;
        document.getElementById('signoff-assessment-id').textContent = signoffData.assessmentId;
        document.getElementById('signoff-doctor-name-input').value = signoffData.doctorName;
        document.getElementById('signoff-license-number').textContent = signoffData.licenseNumber;
        document.getElementById('signoff-timestamp').textContent = new Date().toLocaleString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        if (signoffData.clinicalNotes) {
            document.getElementById('signoff-clinical-notes').textContent = signoffData.clinicalNotes;
            document.getElementById('signoff-clinical-notes-container').style.display = 'block';
        }
        
        // Initialize signature canvas
        initSignatureCanvas();
        
        // Set up event listeners
        console.log('🚪 Setting up event listeners...');
        setupSignoffEventListeners(signoffData);
        console.log('🚪 Event listeners setup complete');
        
    } catch (error) {
        console.error('Doctor Signoff Error:', error);
        alert('❌ Erreur lors de l\'ouverture de la signature médecin: ' + error.message);
    }
};

window.closeDoctorSignoff = function() {
    console.log('🔒 closeDoctorSignoff function called');
    const modal = document.getElementById('doctor-signoff-modal');
    if (modal) {
        console.log('🔒 Modal found, hiding it...');
        modal.classList.add('hidden');
        console.log('🔒 Modal hidden successfully');
    } else {
        console.error('❌ Modal element not found!');
    }
};

// Signature canvas variables
let signatureCanvas = null;
let signatureContext = null;
let isDrawing = false;
let hasSignature = false;
let signatureConfirmed = false;

// Initialize signature canvas
function initSignatureCanvas() {
    signatureCanvas = document.getElementById('signature-canvas');
    if (signatureCanvas) {
        signatureCanvas.width = signatureCanvas.offsetWidth;
        signatureCanvas.height = signatureCanvas.offsetHeight;
        signatureContext = signatureCanvas.getContext('2d');
        signatureContext.strokeStyle = '#1f2937';
        signatureContext.lineWidth = 2;
        signatureContext.lineCap = 'round';
    }
}

// Setup signoff event listeners
function setupSignoffEventListeners(signoffData) {
    console.log('🔧 Setting up signoff event listeners...');
    const legalDeclaration = document.getElementById('legal-declaration');
    const clearSignature = document.getElementById('clear-signature');
    const confirmSignature = document.getElementById('confirm-signature');
    const confirmSignoff = document.getElementById('confirm-signoff');
    
    console.log('🔧 Found elements:', {
        legalDeclaration: !!legalDeclaration,
        clearSignature: !!clearSignature,
        confirmSignature: !!confirmSignature,
        confirmSignoff: !!confirmSignoff
    });
    
    if (confirmSignoff) {
        console.log('🔧 Confirm signoff button details:', {
            id: confirmSignoff.id,
            className: confirmSignoff.className,
            textContent: confirmSignoff.textContent
        });
    } else {
        console.error('❌ Confirm signoff button not found!');
    }
    
    // Legal declaration checkbox
    legalDeclaration.addEventListener('change', updateActionStatus);
    
    // Clear signature button
    clearSignature.addEventListener('click', clearSignatureCanvas);
    
    // Confirm signature button
    confirmSignature.addEventListener('click', confirmSignatureCanvas);
    
    // Confirm signoff button
    confirmSignoff.addEventListener('click', () => {
        console.log('🔘 Confirm signoff button clicked!');
        console.log('🔘 Button element:', confirmSignoff);
        console.log('🔘 Button disabled state:', confirmSignoff.disabled);
        console.log('🔘 Signoff data:', signoffData);
        try {
            handleSignoff(signoffData);
        } catch (error) {
            console.error('❌ Error in handleSignoff:', error);
            alert('❌ Erreur lors du traitement de la signature: ' + error.message);
        }
    });
    
    // Test if button gets enabled
    console.log('🔧 Button initial disabled state:', confirmSignoff.disabled);
    
    // Enable button when signature is confirmed
    console.log('🔧 Setting up signature confirmation...');
    
    // Signature canvas events
    if (signatureCanvas) {
        signatureCanvas.addEventListener('mousedown', startDrawing);
        signatureCanvas.addEventListener('mousemove', draw);
        signatureCanvas.addEventListener('mouseup', stopDrawing);
        signatureCanvas.addEventListener('mouseleave', stopDrawing);
        signatureCanvas.addEventListener('touchstart', startDrawingTouch);
        signatureCanvas.addEventListener('touchmove', drawTouch);
        signatureCanvas.addEventListener('touchend', stopDrawing);
    }
}

// Signature drawing functions
function startDrawing(event) {
    isDrawing = true;
    const rect = signatureCanvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    signatureContext.beginPath();
    signatureContext.moveTo(x, y);
}

function draw(event) {
    if (!isDrawing) return;
    const rect = signatureCanvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    signatureContext.lineTo(x, y);
    signatureContext.stroke();
}

function startDrawingTouch(event) {
    event.preventDefault();
    isDrawing = true;
    const rect = signatureCanvas.getBoundingClientRect();
    const touch = event.touches[0];
    const x = touch.clientX - rect.left;
    const y = touch.clientY - rect.top;
    signatureContext.beginPath();
    signatureContext.moveTo(x, y);
}

function drawTouch(event) {
    event.preventDefault();
    if (!isDrawing) return;
    const rect = signatureCanvas.getBoundingClientRect();
    const touch = event.touches[0];
    const x = touch.clientX - rect.left;
    const y = touch.clientY - rect.top;
    signatureContext.lineTo(x, y);
    signatureContext.stroke();
}

function stopDrawing() {
    if (isDrawing) {
        isDrawing = false;
        hasSignature = true;
        updateSignatureStatus();
    }
}

function clearSignatureCanvas() {
    if (signatureContext) {
        signatureContext.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
        hasSignature = false;
        signatureConfirmed = false;
        updateSignatureStatus();
    }
}

function confirmSignatureCanvas() {
    console.log('🔧 confirmSignatureCanvas called');
    console.log('🔧 hasSignature:', hasSignature);
    if (hasSignature) {
        signatureConfirmed = true;
        console.log('🔧 Signature confirmed, updating status...');
        updateSignatureStatus();
    } else {
        console.log('🔧 No signature to confirm');
    }
}

function updateSignatureStatus() {
    const status = document.getElementById('signature-status');
    const confirmBtn = document.getElementById('confirm-signature');
    
    if (!hasSignature) {
        status.textContent = 'No signature captured';
        confirmBtn.disabled = true;
    } else if (!signatureConfirmed) {
        status.textContent = 'Signature captured - please confirm';
        confirmBtn.disabled = false;
    } else {
        status.textContent = 'Signature confirmed ✓';
        confirmBtn.disabled = true;
    }
    
    updateActionStatus();
}

function updateActionStatus() {
    const legalDeclaration = document.getElementById('legal-declaration');
    const actionStatus = document.getElementById('action-status');
    const confirmSignoff = document.getElementById('confirm-signoff');
    
    console.log('🔧 updateActionStatus called');
    console.log('🔧 legalDeclaration.checked:', legalDeclaration.checked);
    console.log('🔧 signatureConfirmed:', signatureConfirmed);
    
    if (!legalDeclaration.checked) {
        actionStatus.textContent = 'Legal declaration required';
        confirmSignoff.disabled = true;
        console.log('🔧 Button disabled: Legal declaration required');
    } else if (!signatureConfirmed) {
        actionStatus.textContent = 'Signature confirmation required';
        confirmSignoff.disabled = true;
        console.log('🔧 Button disabled: Signature confirmation required');
    } else {
        actionStatus.textContent = 'Ready to sign';
        confirmSignoff.disabled = false;
        console.log('🔧 Button enabled: Ready to sign');
    }
}

function handleSignoff(signoffData) {
    console.log('🚀 handleSignoff function called with data:', signoffData);
    const loadingSpinner = document.getElementById('loading-spinner');
    const confirmText = document.getElementById('confirm-signoff-text');
    const confirmBtn = document.getElementById('confirm-signoff');
    
    // Show loading state
    loadingSpinner.classList.remove('hidden');
    confirmText.textContent = 'Processing...';
    confirmBtn.disabled = true;
    
    // Simulate processing
    setTimeout(() => {
        // Get signature image
        const signatureImage = signatureCanvas.toDataURL('image/png');
        
        // Get the doctor name from the input field
        const doctorNameInput = document.getElementById('signoff-doctor-name-input');
        const actualDoctorName = doctorNameInput ? doctorNameInput.value : signoffData.doctorName;
        
        // Create signed data
        const signedData = {
            signedBy: actualDoctorName,
            licenseNumber: signoffData.licenseNumber,
            signedAt: new Date().toISOString(),
            signatureImage: signatureImage,
            fitnessStatus: signoffData.fitnessDecision,
            assessmentId: signoffData.assessmentId,
            playerName: signoffData.playerName,
            examinationDate: signoffData.examinationDate,
            ipAddress: '192.168.1.100'
        };
        
        // Store signed data globally
        window.signedPCMAData = signedData;
        console.log('✅ Signature data stored:', window.signedPCMAData);
        
        // Save the signed PCMA to database with timeout
        console.log('🔄 Starting signature save process...');
        
        // First, let's test if the save function works
        saveSignedPCMA(signedData).then(success => {
            console.log('📡 Signature save result:', success);
            
            // Reset loading state first
            loadingSpinner.classList.add('hidden');
            confirmText.textContent = 'Confirm and Sign';
            confirmBtn.disabled = false;
            
            if (success) {
                // Show success message
                alert('✅ Signature médicale validée!\n\nAssessment ID: ' + signedData.assessmentId + '\nSigned by: ' + signedData.signedBy + '\nTimestamp: ' + signedData.signedAt);
                
                // Close modal
                console.log('🔒 Closing modal...');
                window.closeDoctorSignoff();
                
                // Update the sign-off button to show it's completed
                const signoffBtn = document.getElementById('doctor-signoff');
                if (signoffBtn) {
                    signoffBtn.innerHTML = `
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        ✅ Signature Validée
                    `;
                    signoffBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                    signoffBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                }
                
                // Disable form editing after signing
                disableFormEditing();
            } else {
                // Show error message
                alert('❌ Erreur lors de la sauvegarde de la signature. Veuillez réessayer.');
            }
        }).catch(error => {
            console.error('❌ Error during signature save:', error);
            alert('❌ Erreur de connexion lors de la sauvegarde. Veuillez réessayer.');
            
            // Reset loading state
            loadingSpinner.classList.add('hidden');
            confirmText.textContent = 'Confirm and Sign';
            confirmBtn.disabled = false;
        });
    }, 1000);
}

// Save signed PCMA to database
function saveSignedPCMA(signedData) {
    console.log('🔄 Saving signed PCMA with data:', signedData);
    // Get form data
    const saveForm = document.querySelector('form');
    
    // Set default values in the form elements BEFORE creating FormData
    const athleteSelect = saveForm.querySelector('select[name="athlete_id"]');
    const typeSelect = saveForm.querySelector('select[name="type"]');
    const assessorSelect = saveForm.querySelector('select[name="assessor_id"]');
    const assessmentDateInput = saveForm.querySelector('input[name="assessment_date"]');
    const statusSelect = saveForm.querySelector('select[name="status"]');
    const notesInput = saveForm.querySelector('input[name="notes"], textarea[name="notes"]');
    const clinicalNotesInput = saveForm.querySelector('input[name="clinical_notes"], textarea[name="clinical_notes"]');
    
    // Set default values if they're empty (with null checks)
    if (athleteSelect && !athleteSelect.value) athleteSelect.value = '1';
    if (typeSelect && !typeSelect.value) typeSelect.value = 'bpma';
    if (assessorSelect && !assessorSelect.value) assessorSelect.value = '1';
    if (assessmentDateInput && !assessmentDateInput.value) assessmentDateInput.value = new Date().toISOString().split('T')[0];
    if (statusSelect && !statusSelect.value) statusSelect.value = 'completed';
    if (notesInput && !notesInput.value) notesInput.value = 'Évaluation médicale complétée';
    if (clinicalNotesInput && !clinicalNotesInput.value) clinicalNotesInput.value = 'Notes cliniques standard';
    
    // Debug: Log the form values after setting defaults
    console.log('🔧 Form values after setting defaults:', {
        athlete_id: athleteSelect ? athleteSelect.value : 'null',
        type: typeSelect ? typeSelect.value : 'null',
        assessor_id: assessorSelect ? assessorSelect.value : 'null',
        assessment_date: assessmentDateInput ? assessmentDateInput.value : 'null',
        status: statusSelect ? statusSelect.value : 'null',
        notes: notesInput ? notesInput.value : 'null',
        clinical_notes: clinicalNotesInput ? clinicalNotesInput.value : 'null'
    });
    
    // Now create FormData with the updated form values
    const formData = new FormData(saveForm);
    
    // Add signature data
    formData.append('signature_data', JSON.stringify(signedData));
    formData.append('is_signed', '1');
    formData.append('signed_at', signedData.signedAt);
    formData.append('signed_by', signedData.signedBy);
    formData.append('license_number', signedData.licenseNumber);
    formData.append('signature_image', signedData.signatureImage);
    
            console.log('📋 FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Log what's being sent to PDF generation
        console.log('📄 PDF Generation - FormData entries:');
        const pdfFormData = new FormData();
        
        // Copy all form data to PDF FormData
        for (let [key, value] of formData.entries()) {
            pdfFormData.append(key, value);
            console.log(`📄 PDF: ${key} = ${value}`);
        }
        
        // Ensure we have the basic required fields for PDF
        if (!formData.has('athlete_id')) formData.append('athlete_id', '1');
        if (!formData.has('type')) formData.append('type', 'bpma');
        if (!formData.has('assessor_id')) formData.append('assessor_id', '1');
        if (!formData.has('assessment_date')) formData.append('assessment_date', new Date().toISOString().split('T')[0]);
        if (!formData.has('status')) formData.append('status', 'completed');
        
        // Only add default values if the fields are actually empty (not just missing)
        // This ensures we don't override actual form data with defaults
        const saveForm2 = document.querySelector('form');
        const allFormElements = saveForm2.querySelectorAll('input, select, textarea');
        
        // Add all form elements to FormData, including empty ones
        allFormElements.forEach(element => {
            if (element.name && !formData.has(element.name)) {
                const value = element.value || '';
                formData.append(element.name, value);
                console.log(`📄 Added form field: ${element.name} = "${value}"`);
            }
        });
        
        // Add missing fields with empty values if they don't exist
        const requiredFields = [
            'blood_pressure', 'heart_rate', 'temperature', 'respiratory_rate', 'oxygen_saturation', 'weight',
            'medical_history', 'surgical_history', 'medications', 'allergies',
            'general_appearance', 'skin_examination', 'lymph_nodes', 'abdomen_examination',
            'cardiac_rhythm', 'heart_murmur', 'blood_pressure_rest', 'blood_pressure_exercise',
            'consciousness', 'cranial_nerves', 'motor_function', 'sensory_function',
            'joint_mobility', 'muscle_strength', 'pain_assessment', 'range_of_motion',
            'fifa_id', 'competition_name', 'competition_date', 'team_name', 'position', 'fifa_compliant'
        ];
        
        requiredFields.forEach(field => {
            if (!formData.has(field)) {
                formData.append(field, '');
                console.log(`📄 Added missing field: ${field} = ""`);
            }
        });
        
        console.log('📄 PDF FormData ready with', formData.entries().length, 'entries');
    
    // Log the actual URL being called
            console.log('🔄 Fetch URL:', '/api/pcma/store');
    console.log('🔄 Request method: POST');
    console.log('🔄 Has signature_data:', formData.has('signature_data'));
    console.log('🔄 Has is_signed:', formData.has('is_signed'));
    
    // Send to server and return a Promise
            console.log('🔄 About to send fetch request to:', '/api/pcma/store');
    console.log('🔄 CSRF token found:', !!document.querySelector('meta[name="csrf-token"]'));
            return fetch('/api/pcma/store', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📡 Server response status:', response.status);
        console.log('📡 Server response ok:', response.ok);
        console.log('📡 Server response headers:', response.headers);
        if (!response.ok) {
            console.error('❌ Server response not ok:', response.status, response.statusText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .catch(error => {
        console.error('❌ Error parsing JSON:', error.message);
        console.error('❌ Full error object:', error);
        throw error;
    })
    .then(data => {
        console.log('📡 Server response data:', data);
        console.log('📡 Data type:', typeof data);
        console.log('📡 Data keys:', Object.keys(data));
        if (data.success) {
            console.log('✅ PCMA saved successfully:', data);
            // Store the PCMA ID for future reference
            window.savedPCMAId = data.pcma_id;
            return true; // Success
        } else {
            console.error('❌ Error saving PCMA:', data.error);
            return false; // Error
        }
    })
    .catch(error => {
        console.error('❌ Error saving PCMA:', error);
        console.error('❌ Error stack:', error.stack);
        return false; // Error
    });
}

// Disable form editing after signing
function disableFormEditing() {
    const disableForm = document.querySelector('form');
    const inputs = disableForm.querySelectorAll('input, textarea, select, button[type="submit"]');
    
    inputs.forEach(input => {
        if (input.type !== 'hidden' && input.id !== 'print-report-btn') {
            input.disabled = true;
        }
    });
    
    // Add visual indicator
    const formContainer = document.querySelector('.container');
    const warningDiv = document.createElement('div');
    warningDiv.className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4';
    warningDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <strong>⚠️ Document signé</strong>
        </div>
        <p class="mt-1">Ce PCMA a été signé et ne peut plus être modifié. Seule l'impression est autorisée.</p>
    `;
    formContainer.insertBefore(warningDiv, formContainer.firstChild);
}

// Add global error handler to catch any JavaScript errors
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    console.error('Error details:', e.message, e.filename, e.lineno);
});

// Add unhandled promise rejection handler
window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled Promise Rejection:', e.reason);
});

// AI Fitness Assessment function
window.generateFitnessAssessment = async function() {
    console.log('🤖 AI Fitness Assessment started');
    
    try {
        // Get form data
        const fitnessForm = document.querySelector('form');
        const formData = new FormData(fitnessForm);
        
        // Add required fields if missing
        if (!formData.has('athlete_id')) formData.append('athlete_id', '1');
        if (!formData.has('type')) formData.append('type', 'bpma');
        if (!formData.has('assessor_id')) formData.append('assessor_id', '1');
        if (!formData.has('assessment_date')) formData.append('assessment_date', new Date().toISOString().split('T')[0]);
        if (!formData.has('status')) formData.append('status', 'completed');
        
        // Show loading state
        const button = document.getElementById('ai-fitness-assessment');
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="animate-spin w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            🔄 Génération en cours...
        `;
        button.disabled = true;
        
        // Send request to AI fitness assessment endpoint
        const response = await fetch('{{ route("pcma.ai-fitness-assessment") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            console.log('🤖 AI Fitness Assessment result:', data);
            
            // Display results
            const resultsDiv = document.getElementById('fitness-assessment-results');
            const contentDiv = document.getElementById('fitness-results-content');
            
            if (data.success && data.assessment) {
                contentDiv.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <h6 class="font-semibold text-green-900 mb-2">Décision Globale</h6>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${
                            data.assessment.overall_decision === 'FIT' ? 'bg-green-100 text-green-800' :
                            data.assessment.overall_decision === 'NOT_FIT' ? 'bg-red-100 text-red-800' :
                            'bg-yellow-100 text-yellow-800'
                        }">
                            ${data.assessment.overall_decision}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <h6 class="font-semibold text-blue-900 mb-1">Score Cardiovasculaire</h6>
                            <span class="text-2xl font-bold text-blue-600">${data.assessment.cardiovascular_score || 'N/A'}/10</span>
                        </div>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                            <h6 class="font-semibold text-orange-900 mb-1">Score Musculo-squelettique</h6>
                            <span class="text-2xl font-bold text-orange-600">${data.assessment.musculoskeletal_score || 'N/A'}/10</span>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                            <h6 class="font-semibold text-purple-900 mb-1">Score Neurologique</h6>
                            <span class="text-2xl font-bold text-purple-600">${data.assessment.neurological_score || 'N/A'}/10</span>
                        </div>
                    </div>
                    
                    ${data.assessment.executive_summary ? `
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h6 class="font-semibold text-gray-900 mb-2">Résumé Exécutif</h6>
                            <p class="text-gray-700 text-sm">${data.assessment.executive_summary}</p>
                        </div>
                    ` : ''}
                `;
                
                resultsDiv.classList.remove('hidden');
                
                // Scroll to results
                resultsDiv.scrollIntoView({ behavior: 'smooth' });
                
            } else {
                throw new Error(data.message || 'Erreur lors de la génération du rapport');
            }
            
        } else {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
    } catch (error) {
        console.error('🤖 AI Fitness Assessment Error:', error);
        alert('❌ Erreur lors de la génération du rapport fitness: ' + error.message);
        
    } finally {
        // Reset button state
        const button = document.getElementById('ai-fitness-assessment');
        button.innerHTML = `
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            🤖 Générer Rapport Fitness Professionnel
        `;
        button.disabled = false;
    }
};



document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - JavaScript is running!');
    
    // Test if Doctor Sign-Off button exists
    const doctorSignoffBtn = document.getElementById('doctor-signoff');
    console.log('🔍 Doctor Sign-Off button found:', !!doctorSignoffBtn);
    
    // Tab switching functionality
    const tabs = document.querySelectorAll('.input-method-tab');
    const sections = document.querySelectorAll('.input-section');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = this.id.replace('-tab', '-section');
            
            // Update tab styles
            tabs.forEach(t => {
                t.classList.remove('active', 'bg-blue-600', 'text-white');
                t.classList.add('bg-gray-100', 'text-gray-700');
            });
            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('active', 'bg-blue-600', 'text-white');
            
            // Show/hide sections
            sections.forEach(section => {
                section.classList.add('hidden');
            });
            document.getElementById(targetId).classList.remove('hidden');
        });
    });

    // Manual form AI analysis (existing functionality)
    const aiAnalyzeBtn = document.getElementById('ai-analyze-btn');
    const clearNotesBtn = document.getElementById('clear-notes-btn');
    const clinicalNotes = document.getElementById('clinical_notes');
    const aiResults = document.getElementById('ai-results');
});
</script>

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
@endpush
@endsection 