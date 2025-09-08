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
                        <h2 class="text-xl font-semibold text-red-800">⚠️ Document signé - Modification interdite</h2>
                        <p class="text-red-700 mt-2">Ce PCMA a été signé par <strong>{{ $pcma->signed_by }}</strong> le {{ \Carbon\Carbon::parse($pcma->signed_at)->format('d/m/Y H:i') }} et ne peut plus être modifié.</p>
                        <div class="mt-4 flex space-x-4">
                            <a href="{{ route('pcma.show', $pcma) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                👁️ Voir le document
                            </a>
                            <a href="{{ route('pcma.view.pdf', $pcma) }}" 
                               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                🖨️ Imprimer
                            </a>
                            <a href="{{ route('pcma.index') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                ← Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">✏️ Modifier PCMA</h1>
                <p class="text-gray-600 mt-2">Modifier l'évaluation médicale pré-compétition</p>
            </div>

            <form action="{{ route('pcma.update', $pcma) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">🏥 Informations de Base</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="athlete_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Athlète *
                            </label>
                            <select id="athlete_id" 
                                    name="athlete_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                <option value="">Sélectionner un athlète</option>
                                @foreach($athletes as $athlete)
                                    <option value="{{ $athlete->id }}" {{ old('athlete_id', $pcma->athlete_id) == $athlete->id ? 'selected' : '' }}>
                                        {{ $athlete->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="assessor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Assesseur *
                            </label>
                            <select id="assessor_id" 
                                    name="assessor_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                <option value="">Sélectionner un assesseur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assessor_id', $pcma->assessor_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="assessment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date d'Évaluation *
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
                                Type d'évaluation *
                            </label>
                            <select id="type" 
                                    name="type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                <option value="">Sélectionner le type</option>
                                <option value="bpma" {{ old('type', $pcma->type) === 'bpma' ? 'selected' : '' }}>BPMA</option>
                                <option value="cardio" {{ old('type', $pcma->type) === 'cardio' ? 'selected' : '' }}>Cardiovasculaire</option>
                                <option value="dental" {{ old('type', $pcma->type) === 'dental' ? 'selected' : '' }}>Dentaire</option>
                                <option value="neurological" {{ old('type', $pcma->type) === 'neurological' ? 'selected' : '' }}>Neurologique</option>
                                <option value="orthopedic" {{ old('type', $pcma->type) === 'orthopedic' ? 'selected' : '' }}>Orthopédique</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Statut
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="pending" {{ old('status', $pcma->status) === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="completed" {{ old('status', $pcma->status) === 'completed' ? 'selected' : '' }}>Terminé</option>
                                <option value="failed" {{ old('status', $pcma->status) === 'failed' ? 'selected' : '' }}>Échoué</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Assessment -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">🏥 Évaluation Médicale</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-6">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes Générales
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Notes supplémentaires, observations, recommandations...">{{ old('notes', $pcma->notes) }}</textarea>
                        </div>
                        
                        <div>
                            <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes Cliniques
                            </label>
                            <textarea id="clinical_notes" 
                                      name="clinical_notes" 
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Notes cliniques détaillées...">{{ old('clinical_notes', $pcma->result_json['clinical_notes'] ?? '') }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tension Artérielle
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
                                    Fréquence Cardiaque
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
                                    Température
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
                                    Fréquence Respiratoire
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
                                    Saturation O₂
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
                                    Poids (kg)
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
                    <h2 class="text-xl font-semibold text-gray-800">🏥 Antécédents Médicaux</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-6">
                        <div>
                            <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">
                                Antécédents Cardio-vasculaires
                            </label>
                            <textarea id="medical_history" 
                                      name="medical_history" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Antécédents cardiaques, hypertension, etc...">{{ old('medical_history', $pcma->result_json['medical_history']['cardiovascular_history'] ?? '') }}</textarea>
                        </div>
                        
                        <div>
                            <label for="surgical_history" class="block text-sm font-medium text-gray-700 mb-2">
                                Antécédents Chirurgicaux
                            </label>
                            <textarea id="surgical_history" 
                                      name="surgical_history" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Interventions chirurgicales...">{{ old('surgical_history', $pcma->result_json['medical_history']['surgical_history'] ?? '') }}</textarea>
                        </div>
                        
                        <div>
                            <label for="medications" class="block text-sm font-medium text-gray-700 mb-2">
                                Médicaments Actuels
                            </label>
                            <textarea id="medications" 
                                      name="medications" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Traitements en cours...">{{ old('medications', $pcma->result_json['medical_history']['medications'] ?? '') }}</textarea>
                        </div>
                        
                        <div>
                            <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                                Allergies
                            </label>
                            <textarea id="allergies" 
                                      name="allergies" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Allergies médicamenteuses, alimentaires...">{{ old('allergies', $pcma->result_json['medical_history']['allergies'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Physical Examination -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">🔍 Examen Physique</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="general_appearance" class="block text-sm font-medium text-gray-700 mb-2">
                                Apparence Générale
                            </label>
                            <select id="general_appearance" 
                                    name="general_appearance" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="normal" {{ old('general_appearance', $pcma->result_json['physical_examination']['general_appearance'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="abnormal" {{ old('general_appearance', $pcma->result_json['physical_examination']['general_appearance'] ?? '') === 'abnormal' ? 'selected' : '' }}>Anormal</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="skin_examination" class="block text-sm font-medium text-gray-700 mb-2">
                                Examen Cutané
                            </label>
                            <select id="skin_examination" 
                                    name="skin_examination" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="normal" {{ old('skin_examination', $pcma->result_json['physical_examination']['skin_examination'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="abnormal" {{ old('skin_examination', $pcma->result_json['physical_examination']['skin_examination'] ?? '') === 'abnormal' ? 'selected' : '' }}>Anormal</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="lymph_nodes" class="block text-sm font-medium text-gray-700 mb-2">
                                Ganglions Lymphatiques
                            </label>
                            <select id="lymph_nodes" 
                                    name="lymph_nodes" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="normal" {{ old('lymph_nodes', $pcma->result_json['physical_examination']['lymph_nodes'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="enlarged" {{ old('lymph_nodes', $pcma->result_json['physical_examination']['lymph_nodes'] ?? '') === 'enlarged' ? 'selected' : '' }}>Hypertrophiés</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="abdomen_examination" class="block text-sm font-medium text-gray-700 mb-2">
                                Examen Abdominal
                            </label>
                            <select id="abdomen_examination" 
                                    name="abdomen_examination" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="normal" {{ old('abdomen_examination', $pcma->result_json['physical_examination']['abdomen_examination'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="abnormal" {{ old('abdomen_examination', $pcma->result_json['physical_examination']['abdomen_examination'] ?? '') === 'abnormal' ? 'selected' : '' }}>Anormal</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cardiovascular Assessment -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">❤️ Évaluation Cardiovasculaire</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cardiac_rhythm" class="block text-sm font-medium text-gray-700 mb-2">
                                Rythme Cardiaque
                            </label>
                            <select id="cardiac_rhythm" 
                                    name="cardiac_rhythm" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="sinus" {{ old('cardiac_rhythm', $pcma->result_json['cardiovascular_assessment']['cardiac_rhythm'] ?? '') === 'sinus' ? 'selected' : '' }}>Sinus</option>
                                <option value="irregular" {{ old('cardiac_rhythm', $pcma->result_json['cardiovascular_assessment']['cardiac_rhythm'] ?? '') === 'irregular' ? 'selected' : '' }}>Irregular</option>
                                <option value="arrhythmia" {{ old('cardiac_rhythm', $pcma->result_json['cardiovascular_assessment']['cardiac_rhythm'] ?? '') === 'arrhythmia' ? 'selected' : '' }}>Arrythmie</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="heart_murmur" class="block text-sm font-medium text-gray-700 mb-2">
                                Souffle Cardiaque
                            </label>
                            <select id="heart_murmur" 
                                    name="heart_murmur" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="none" {{ old('heart_murmur', $pcma->result_json['cardiovascular_assessment']['heart_murmur'] ?? '') === 'none' ? 'selected' : '' }}>Aucun</option>
                                <option value="systolic" {{ old('heart_murmur', $pcma->result_json['cardiovascular_assessment']['heart_murmur'] ?? '') === 'systolic' ? 'selected' : '' }}>Systolique</option>
                                <option value="diastolic" {{ old('heart_murmur', $pcma->result_json['cardiovascular_assessment']['heart_murmur'] ?? '') === 'diastolic' ? 'selected' : '' }}>Diastolique</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="blood_pressure_rest" class="block text-sm font-medium text-gray-700 mb-2">
                                Tension au Repos
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
                                Tension à l'Effort
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
                    <h2 class="text-xl font-semibold text-gray-800">🧠 Évaluation Neurologique</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="consciousness" class="block text-sm font-medium text-gray-700 mb-2">
                                Niveau de Conscience
                            </label>
                            <select id="consciousness" 
                                    name="consciousness" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="alert" {{ old('consciousness', $pcma->result_json['neurological_assessment']['consciousness'] ?? '') === 'alert' ? 'selected' : '' }}>Alerte</option>
                                <option value="confused" {{ old('consciousness', $pcma->result_json['neurological_assessment']['consciousness'] ?? '') === 'confused' ? 'selected' : '' }}>Confus</option>
                                <option value="drowsy" {{ old('consciousness', $pcma->result_json['neurological_assessment']['consciousness'] ?? '') === 'drowsy' ? 'selected' : '' }}>Somnolent</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="cranial_nerves" class="block text-sm font-medium text-gray-700 mb-2">
                                Nerfs Crâniens
                            </label>
                            <select id="cranial_nerves" 
                                    name="cranial_nerves" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="normal" {{ old('cranial_nerves', $pcma->result_json['neurological_assessment']['cranial_nerves'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="abnormal" {{ old('cranial_nerves', $pcma->result_json['neurological_assessment']['cranial_nerves'] ?? '') === 'abnormal' ? 'selected' : '' }}>Anormal</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="motor_function" class="block text-sm font-medium text-gray-700 mb-2">
                                Fonction Motrice
                            </label>
                            <select id="motor_function" 
                                    name="motor_function" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="normal" {{ old('motor_function', $pcma->result_json['neurological_assessment']['motor_function'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="weakness" {{ old('motor_function', $pcma->result_json['neurological_assessment']['motor_function'] ?? '') === 'weakness' ? 'selected' : '' }}>Faiblesse</option>
                                <option value="paralysis" {{ old('motor_function', $pcma->result_json['neurological_assessment']['motor_function'] ?? '') === 'paralysis' ? 'selected' : '' }}>Paralysie</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="sensory_function" class="block text-sm font-medium text-gray-700 mb-2">
                                Fonction Sensitive
                            </label>
                            <select id="sensory_function" 
                                    name="sensory_function" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="normal" {{ old('sensory_function', $pcma->result_json['neurological_assessment']['sensory_function'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="decreased" {{ old('sensory_function', $pcma->result_json['neurological_assessment']['sensory_function'] ?? '') === 'decreased' ? 'selected' : '' }}>Diminuée</option>
                                <option value="absent" {{ old('sensory_function', $pcma->result_json['neurological_assessment']['sensory_function'] ?? '') === 'absent' ? 'selected' : '' }}>Absente</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Musculoskeletal Assessment -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">💪 Évaluation Musculo-squelettique</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="joint_mobility" class="block text-sm font-medium text-gray-700 mb-2">
                                Mobilité Articulaire
                            </label>
                            <select id="joint_mobility" 
                                    name="joint_mobility" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="normal" {{ old('joint_mobility', $pcma->result_json['musculoskeletal_assessment']['joint_mobility'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="limited" {{ old('joint_mobility', $pcma->result_json['musculoskeletal_assessment']['joint_mobility'] ?? '') === 'limited' ? 'selected' : '' }}>Limitée</option>
                                <option value="restricted" {{ old('joint_mobility', $pcma->result_json['musculoskeletal_assessment']['joint_mobility'] ?? '') === 'restricted' ? 'selected' : '' }}>Restreinte</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="muscle_strength" class="block text-sm font-medium text-gray-700 mb-2">
                                Force Musculaire
                            </label>
                            <select id="muscle_strength" 
                                    name="muscle_strength" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="normal" {{ old('muscle_strength', $pcma->result_json['musculoskeletal_assessment']['muscle_strength'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="reduced" {{ old('muscle_strength', $pcma->result_json['musculoskeletal_assessment']['muscle_strength'] ?? '') === 'reduced' ? 'selected' : '' }}>Réduite</option>
                                <option value="weak" {{ old('muscle_strength', $pcma->result_json['musculoskeletal_assessment']['muscle_strength'] ?? '') === 'weak' ? 'selected' : '' }}>Faible</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="pain_assessment" class="block text-sm font-medium text-gray-700 mb-2">
                                Évaluation de la Douleur
                            </label>
                            <select id="pain_assessment" 
                                    name="pain_assessment" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="none" {{ old('pain_assessment', $pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'none' ? 'selected' : '' }}>Aucune</option>
                                <option value="mild" {{ old('pain_assessment', $pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'mild' ? 'selected' : '' }}>Légère</option>
                                <option value="moderate" {{ old('pain_assessment', $pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'moderate' ? 'selected' : '' }}>Modérée</option>
                                <option value="severe" {{ old('pain_assessment', $pcma->result_json['musculoskeletal_assessment']['pain_assessment'] ?? '') === 'severe' ? 'selected' : '' }}>Sévère</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="range_of_motion" class="block text-sm font-medium text-gray-700 mb-2">
                                Amplitude de Mouvement
                            </label>
                            <select id="range_of_motion" 
                                    name="range_of_motion" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner</option>
                                <option value="full" {{ old('range_of_motion', $pcma->result_json['musculoskeletal_assessment']['range_of_motion'] ?? '') === 'full' ? 'selected' : '' }}>Complète</option>
                                <option value="limited" {{ old('range_of_motion', $pcma->result_json['musculoskeletal_assessment']['range_of_motion'] ?? '') === 'limited' ? 'selected' : '' }}>Limitée</option>
                                <option value="restricted" {{ old('range_of_motion', $pcma->result_json['musculoskeletal_assessment']['range_of_motion'] ?? '') === 'restricted' ? 'selected' : '' }}>Restreinte</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FIFA Compliance -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">⚽ Conformité FIFA</h2>
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
                                <span class="ml-2 text-sm text-gray-700">Conforme aux standards FIFA</span>
                            </label>
                        </div>
                        
                        <div>
                            <label for="fifa_connect_id" class="block text-sm font-medium text-gray-700 mb-2">
                                ID FIFA Connect (optionnel)
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
                                Compétition
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
                                Type d'Évaluation
                            </label>
                            <select id="assessment_type" 
                                    name="assessment_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="initial" {{ ($pcma->assessment_type ?? '') === 'initial' ? 'selected' : '' }}>Initiale</option>
                                <option value="renewal" {{ ($pcma->assessment_type ?? '') === 'renewal' ? 'selected' : '' }}>Renouvellement</option>
                                <option value="emergency" {{ ($pcma->assessment_type ?? '') === 'emergency' ? 'selected' : '' }}>Urgence</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">📝 Notes Supplémentaires</h2>
                </div>
                
                <div class="p-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes Générales
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Notes supplémentaires, observations, recommandations...">{{ $pcma->notes ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('pcma.show', $pcma) }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    ← Annuler
                </a>
                
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        💾 Sauvegarder
                    </button>
                    
                    <a href="{{ route('pcma.view.pdf', $pcma) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        📄 Exporter PDF
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
    const form = document.querySelector('form');
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
            alert('Veuillez remplir tous les champs obligatoires.');
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