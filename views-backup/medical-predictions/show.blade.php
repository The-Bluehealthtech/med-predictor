@extends('layouts.app')

@section('title', 'Détails de la Prédiction - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🔮 Détails de la Prédiction</h1>
                    <p class="text-gray-600 mt-2">Analyse complète de la prédiction médicale</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('medical-predictions.edit', $medicalPrediction) }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Modifier</span>
                    </a>
                    <a href="{{ route('medical-predictions.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Retour</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Prediction Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Prediction Overview -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Vue d'Ensemble</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Player Info -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du Joueur</h3>
                                @if($medicalPrediction->player)
                                    <div class="flex items-center space-x-4 mb-4">
                                        @if($medicalPrediction->player->player_face_url)
                                            <img class="h-16 w-16 rounded-full object-cover" src="{{ $medicalPrediction->player->player_face_url }}" alt="{{ $medicalPrediction->player->full_name }}">
                                        @else
                                            <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-lg font-medium text-gray-700">{{ substr($medicalPrediction->player->first_name, 0, 1) }}{{ substr($medicalPrediction->player->last_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $medicalPrediction->player->full_name }}</h4>
                                            <p class="text-gray-600">{{ $medicalPrediction->player->position }} - {{ $medicalPrediction->player->age }} ans</p>
                                            <p class="text-gray-500">{{ $medicalPrediction->player->club->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700">Note FIFA:</span>
                                            <span class="text-gray-900">{{ $medicalPrediction->player->overall_rating }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Potentiel:</span>
                                            <span class="text-gray-900">{{ $medicalPrediction->player->potential_rating }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Pied Préféré:</span>
                                            <span class="text-gray-900">{{ $medicalPrediction->player->preferred_foot }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">BMI:</span>
                                            <span class="text-gray-900">{{ $medicalPrediction->player->bmi }} ({{ $medicalPrediction->player->bmi_category }})</span>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-500">Joueur supprimé</p>
                                @endif
                            </div>

                            <!-- Prediction Info -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Détails de la Prédiction</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="font-medium text-gray-700">Type:</span>
                                        <span class="inline-flex ml-2 px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $medicalPrediction->prediction_type == 'injury_risk' ? 'bg-red-100 text-red-800' : 
                                               ($medicalPrediction->prediction_type == 'performance_prediction' ? 'bg-blue-100 text-blue-800' : 
                                               ($medicalPrediction->prediction_type == 'health_condition' ? 'bg-green-100 text-green-800' : 
                                               ($medicalPrediction->prediction_type == 'recovery_prediction' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800'))) }}">
                                            @switch($medicalPrediction->prediction_type)
                                                @case('injury_risk')
                                                    Risque de Blessure
                                                    @break
                                                @case('performance_prediction')
                                                    Prédiction de Performance
                                                    @break
                                                @case('health_condition')
                                                    État de Santé
                                                    @break
                                                @case('recovery_prediction')
                                                    Prédiction de Récupération
                                                    @break
                                                @case('fitness_assessment')
                                                    Évaluation de Forme
                                                    @break
                                                @default
                                                    {{ $medicalPrediction->prediction_type }}
                                            @endswitch
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Condition Prédite:</span>
                                        <p class="text-gray-900 mt-1">{{ $medicalPrediction->predicted_condition }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Statut:</span>
                                        <span class="inline-flex ml-2 px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $medicalPrediction->status == 'active' ? 'bg-green-100 text-green-800' : 
                                               ($medicalPrediction->status == 'expired' ? 'bg-red-100 text-red-800' : 
                                               ($medicalPrediction->status == 'verified' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                            @switch($medicalPrediction->status)
                                                @case('active')
                                                    Actif
                                                    @break
                                                @case('expired')
                                                    Expiré
                                                    @break
                                                @case('verified')
                                                    Vérifié
                                                    @break
                                                @case('false_positive')
                                                    Faux Positif
                                                    @break
                                                @default
                                                    {{ $medicalPrediction->status }}
                                            @endswitch
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Généré par:</span>
                                        <p class="text-gray-900">{{ $medicalPrediction->user->name ?? 'Système' }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Date de Prédiction:</span>
                                        <p class="text-gray-900">{{ $medicalPrediction->prediction_date->format('d/m/Y H:i') }}</p>
                                    </div>
                                    @if($medicalPrediction->valid_until)
                                        <div>
                                            <span class="font-medium text-gray-700">Valide jusqu'au:</span>
                                            <p class="text-gray-900">{{ $medicalPrediction->valid_until->format('d/m/Y H:i') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk and Confidence -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Analyse des Risques et Confiance</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Risk Probability -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Probabilité de Risque</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Niveau de Risque</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ round($medicalPrediction->risk_probability * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-{{ $medicalPrediction->risk_probability > 0.7 ? 'red' : ($medicalPrediction->risk_probability > 0.4 ? 'yellow' : 'green') }}-500 h-3 rounded-full transition-all duration-300" 
                                             style="width: {{ $medicalPrediction->risk_probability * 100 }}%"></div>
                                    </div>
                                    <div class="text-center">
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                            {{ $medicalPrediction->risk_probability > 0.7 ? 'bg-red-100 text-red-800' : 
                                               ($medicalPrediction->risk_probability > 0.4 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ $medicalPrediction->risk_level }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Confidence Score -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Score de Confiance</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Niveau de Confiance</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ round($medicalPrediction->confidence_score * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-{{ $medicalPrediction->confidence_score > 0.8 ? 'green' : ($medicalPrediction->confidence_score > 0.6 ? 'yellow' : 'red') }}-500 h-3 rounded-full transition-all duration-300" 
                                             style="width: {{ $medicalPrediction->confidence_score * 100 }}%"></div>
                                    </div>
                                    <div class="text-center">
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                            {{ $medicalPrediction->confidence_score > 0.8 ? 'bg-green-100 text-green-800' : 
                                               ($medicalPrediction->confidence_score > 0.6 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $medicalPrediction->confidence_level }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Factors -->
                @if($medicalPrediction->prediction_factors)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">Facteurs de Risque Analysés</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($medicalPrediction->prediction_factors as $factor => $data)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-medium text-gray-900">{{ $data['description'] ?? ucfirst($factor) }}</h4>
                                            <span class="text-sm font-semibold text-gray-600">{{ $data['value'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                <div class="bg-{{ $data['risk'] > 0.5 ? 'red' : ($data['risk'] > 0.3 ? 'yellow' : 'green') }}-500 h-2 rounded-full" 
                                                     style="width: {{ $data['risk'] * 100 }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ round($data['risk'] * 100) }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Recommendations -->
                @if($medicalPrediction->recommendations)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">Recommandations</h2>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-3">
                                @foreach($medicalPrediction->recommendations as $recommendation)
                                    <li class="flex items-start space-x-3">
                                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-gray-700">{{ $recommendation }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Health Record Link -->
                @if($medicalPrediction->healthRecord)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Dossier Médical</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Date:</span>
                                    <p class="text-gray-900">{{ $medicalPrediction->healthRecord->record_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Statut:</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $medicalPrediction->healthRecord->status == 'active' ? 'bg-green-100 text-green-800' : 
                                           ($medicalPrediction->healthRecord->status == 'archived' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($medicalPrediction->healthRecord->status) }}
                                    </span>
                                </div>
                                <a href="{{ route('health-records.show', $medicalPrediction->healthRecord) }}"
                                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-lg transition-colors">
                                    Voir le dossier médical
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- AI Model Info -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Informations IA</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Version du Modèle:</span>
                                <p class="text-gray-900">{{ $medicalPrediction->ai_model_version }}</p>
                            </div>
                            @if($medicalPrediction->prediction_notes)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Points de Données:</span>
                                    <p class="text-gray-900">{{ $medicalPrediction->prediction_notes['data_points_analyzed'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Généré le:</span>
                                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($medicalPrediction->prediction_notes['generated_at'] ?? now())->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('medical-predictions.edit', $medicalPrediction) }}" 
                           class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center px-4 py-2 rounded-lg transition-colors">
                            Modifier la Prédiction
                        </a>
                        <form action="{{ route('medical-predictions.destroy', $medicalPrediction) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette prédiction ?')"
                                    class="block w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 