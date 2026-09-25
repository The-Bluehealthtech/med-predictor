@extends('layouts.app')

@section('title', 'Modifier la Prédiction - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">✏️ Modifier la Prédiction</h1>
                    <p class="text-gray-600 mt-2">Modifier les détails de la prédiction médicale</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('medical-predictions.show', $medicalPrediction) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Retour</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Modifier la Prédiction</h2>
            </div>
            
            <form action="{{ route('medical-predictions.update', $medicalPrediction) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <!-- Player Info (Read-only) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Joueur</label>
                    <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-md">
                        @if($medicalPrediction->player)
                            @if($medicalPrediction->player->player_face_url)
                                <img class="h-12 w-12 rounded-full object-cover" src="{{ $medicalPrediction->player->player_face_url }}" alt="{{ $medicalPrediction->player->full_name }}">
                            @else
                                <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">{{ substr($medicalPrediction->player->first_name, 0, 1) }}{{ substr($medicalPrediction->player->last_name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $medicalPrediction->player->full_name }}</h4>
                                <p class="text-gray-600">{{ $medicalPrediction->player->position }} - {{ $medicalPrediction->player->club->name ?? 'N/A' }}</p>
                            </div>
                        @else
                            <span class="text-gray-500">Joueur supprimé</span>
                        @endif
                    </div>
                </div>

                <!-- Prediction Type -->
                <div class="mb-6">
                    <label for="prediction_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type de Prédiction <span class="text-red-500">*</span>
                    </label>
                    <select name="prediction_type" id="prediction_type" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($predictionTypes as $value => $label)
                            <option value="{{ $value }}" {{ $medicalPrediction->prediction_type == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('prediction_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Predicted Condition -->
                <div class="mb-6">
                    <label for="predicted_condition" class="block text-sm font-medium text-gray-700 mb-2">
                        Condition Prédite <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="predicted_condition" id="predicted_condition" 
                           value="{{ old('predicted_condition', $medicalPrediction->predicted_condition) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: Risque de blessure musculaire modéré">
                    @error('predicted_condition')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Risk and Confidence -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Risk Probability -->
                    <div>
                        <label for="risk_probability" class="block text-sm font-medium text-gray-700 mb-2">
                            Probabilité de Risque <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="range" name="risk_probability" id="risk_probability" 
                                   min="0" max="1" step="0.01" 
                                   value="{{ old('risk_probability', $medicalPrediction->risk_probability) }}" required
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                                   oninput="updateRiskValue(this.value)">
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0%</span>
                                <span id="risk_value">{{ round($medicalPrediction->risk_probability * 100) }}%</span>
                                <span>100%</span>
                            </div>
                        </div>
                        @error('risk_probability')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confidence Score -->
                    <div>
                        <label for="confidence_score" class="block text-sm font-medium text-gray-700 mb-2">
                            Score de Confiance <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="range" name="confidence_score" id="confidence_score" 
                                   min="0" max="1" step="0.01" 
                                   value="{{ old('confidence_score', $medicalPrediction->confidence_score) }}" required
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                                   oninput="updateConfidenceValue(this.value)">
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0%</span>
                                <span id="confidence_value">{{ round($medicalPrediction->confidence_score * 100) }}%</span>
                                <span>100%</span>
                            </div>
                        </div>
                        @error('confidence_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active" {{ $medicalPrediction->status == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="expired" {{ $medicalPrediction->status == 'expired' ? 'selected' : '' }}>Expiré</option>
                        <option value="verified" {{ $medicalPrediction->status == 'verified' ? 'selected' : '' }}>Vérifié</option>
                        <option value="false_positive" {{ $medicalPrediction->status == 'false_positive' ? 'selected' : '' }}>Faux Positif</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Recommendations -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Recommandations
                    </label>
                    <div id="recommendations-container" class="space-y-2">
                        @if($medicalPrediction->recommendations)
                            @foreach($medicalPrediction->recommendations as $index => $recommendation)
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="recommendations[]" value="{{ $recommendation }}" 
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Recommandation">
                                    <button type="button" onclick="removeRecommendation(this)" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-center space-x-2">
                                <input type="text" name="recommendations[]" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Recommandation">
                                <button type="button" onclick="removeRecommendation(this)" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addRecommendation()" 
                            class="mt-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Ajouter une Recommandation</span>
                    </button>
                </div>

                <!-- Prediction Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Notes de Prédiction
                    </label>
                    <div id="notes-container" class="space-y-2">
                        @if($medicalPrediction->prediction_notes && is_array($medicalPrediction->prediction_notes))
                            @foreach($medicalPrediction->prediction_notes as $key => $note)
                                @if(!in_array($key, ['generated_at', 'model_version', 'data_points_analyzed']))
                                    <div class="flex items-center space-x-2">
                                        <input type="text" name="prediction_notes[{{ $key }}]" value="{{ $note }}" 
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="Note">
                                        <button type="button" onclick="removeNote(this)" 
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <button type="button" onclick="addNote()" 
                            class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Ajouter une Note</span>
                    </button>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('medical-predictions.show', $medicalPrediction) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Mettre à Jour</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Original Prediction Info -->
        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">📊 Informations Originales</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                <div>
                    <span class="font-medium">Généré par:</span> {{ $medicalPrediction->user->name ?? 'Système' }}
                </div>
                <div>
                    <span class="font-medium">Date de création:</span> {{ $medicalPrediction->prediction_date->format('d/m/Y H:i') }}
                </div>
                <div>
                    <span class="font-medium">Version IA:</span> {{ $medicalPrediction->ai_model_version }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.slider::-webkit-slider-thumb {
    appearance: none;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
}

.slider::-moz-range-thumb {
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
    border: none;
}
</style>

<script>
function updateRiskValue(value) {
    document.getElementById('risk_value').textContent = Math.round(value * 100) + '%';
}

function updateConfidenceValue(value) {
    document.getElementById('confidence_value').textContent = Math.round(value * 100) + '%';
}

function addRecommendation() {
    const container = document.getElementById('recommendations-container');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <input type="text" name="recommendations[]" 
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Recommandation">
        <button type="button" onclick="removeRecommendation(this)" 
                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeRecommendation(button) {
    button.parentElement.remove();
}

function addNote() {
    const container = document.getElementById('notes-container');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <input type="text" name="prediction_notes[note_${Date.now()}]" 
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Note">
        <button type="button" onclick="removeNote(this)" 
                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeNote(button) {
    button.parentElement.remove();
}
</script>
@endsection 