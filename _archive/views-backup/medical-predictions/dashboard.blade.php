@extends('layouts.app')

@section('title', 'Tableau de Bord - Prédictions Médicales - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📊 Tableau de Bord - Prédictions Médicales</h1>
                    <p class="text-gray-600 mt-2">Vue d'ensemble des prédictions médicales et analyses</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('medical-predictions.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Nouvelle Prédiction</span>
                    </a>
                    <a href="{{ route('medical-predictions.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Voir Toutes</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Predictions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Prédictions</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <!-- High Risk Predictions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Risque Élevé</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['high_risk'] }}</p>
                        <p class="text-sm text-gray-500">{{ $stats['high_risk_percentage'] }}% du total</p>
                    </div>
                </div>
            </div>

            <!-- Active Predictions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Prédictions Actives</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['active'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Verified Predictions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Vérifiées</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['verified'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Predictions -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Prédictions Récentes</h2>
                </div>
                <div class="p-6">
                    @if(count($recentPredictions) > 0)
                        <div class="space-y-4">
                            @foreach($recentPredictions as $prediction)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                @if($prediction['player'])
                                                    <span class="font-medium text-gray-900">{{ $prediction['player']['first_name'] }} {{ $prediction['player']['last_name'] }}</span>
                                                    <span class="text-sm text-gray-500">({{ $prediction['player']['position'] }})</span>
                                                @else
                                                    <span class="text-gray-500">Joueur supprimé</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $prediction['predicted_condition'] }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($prediction['prediction_date'])->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="flex flex-col items-end space-y-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $prediction['prediction_type'] == 'injury_risk' ? 'bg-red-100 text-red-800' : 
                                                   ($prediction['prediction_type'] == 'performance_prediction' ? 'bg-blue-100 text-blue-800' : 
                                                   ($prediction['prediction_type'] == 'health_condition' ? 'bg-green-100 text-green-800' : 
                                                   ($prediction['prediction_type'] == 'recovery_prediction' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800'))) }}">
                                                @switch($prediction['prediction_type'])
                                                    @case('injury_risk')
                                                        Blessure
                                                        @break
                                                    @case('performance_prediction')
                                                        Performance
                                                        @break
                                                    @case('health_condition')
                                                        Santé
                                                        @break
                                                    @case('recovery_prediction')
                                                        Récupération
                                                        @break
                                                    @case('fitness_assessment')
                                                        Forme
                                                        @break
                                                    @default
                                                        {{ $prediction['prediction_type'] }}
                                                @endswitch
                                            </span>
                                            <div class="flex items-center space-x-1">
                                                <div class="w-12 bg-gray-200 rounded-full h-1">
                                                    <div class="bg-{{ $prediction['risk_probability'] > 0.7 ? 'red' : ($prediction['risk_probability'] > 0.4 ? 'yellow' : 'green') }}-500 h-1 rounded-full" 
                                                         style="width: {{ $prediction['risk_probability'] * 100 }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ round($prediction['risk_probability'] * 100) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('medical-predictions.index') }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Voir toutes les prédictions →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune prédiction récente</h3>
                            <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle prédiction.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- High Risk Predictions -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Prédictions à Risque Élevé</h2>
                </div>
                <div class="p-6">
                    @if(count($highRiskPredictions) > 0)
                        <div class="space-y-4">
                            @foreach($highRiskPredictions as $prediction)
                                <div class="border border-red-200 bg-red-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                @if($prediction['player'])
                                                    <span class="font-medium text-gray-900">{{ $prediction['player']['first_name'] }} {{ $prediction['player']['last_name'] }}</span>
                                                    <span class="text-sm text-gray-500">({{ $prediction['player']['position'] }})</span>
                                                @else
                                                    <span class="text-gray-500">Joueur supprimé</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $prediction['predicted_condition'] }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($prediction['prediction_date'])->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="flex flex-col items-end space-y-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                {{ round($prediction['risk_probability'] * 100) }}% Risque
                                            </span>
                                            <div class="flex items-center space-x-1">
                                                <div class="w-12 bg-gray-200 rounded-full h-1">
                                                    <div class="bg-red-500 h-1 rounded-full" 
                                                         style="width: {{ $prediction['risk_probability'] * 100 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($prediction['recommendations'])
                                        <div class="mt-3 pt-3 border-t border-red-200">
                                            <p class="text-xs font-medium text-red-800 mb-1">Recommandations:</p>
                                            <ul class="text-xs text-red-700 space-y-1">
                                                @foreach(array_slice($prediction['recommendations'], 0, 2) as $recommendation)
                                                    <li class="flex items-start space-x-1">
                                                        <span class="text-red-500 mt-0.5">•</span>
                                                        <span>{{ $recommendation }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if(count($highRiskPredictions) >= 10)
                            <div class="mt-4 text-center">
                                <a href="{{ route('medical-predictions.index', ['status' => 'active']) }}" 
                                   class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Voir toutes les prédictions à risque →
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune prédiction à risque élevé</h3>
                            <p class="mt-1 text-sm text-gray-500">Excellent ! Toutes les prédictions sont dans des limites acceptables.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Actions Rapides</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('medical-predictions.create') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Nouvelle Prédiction</h3>
                        <p class="text-sm text-gray-500">Générer une prédiction médicale</p>
                    </div>
                </a>

                <a href="{{ route('medical-predictions.index', ['status' => 'active']) }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Prédictions Actives</h3>
                        <p class="text-sm text-gray-500">Voir les prédictions en cours</p>
                    </div>
                </a>

                <a href="{{ route('medical-predictions.index', ['type' => 'injury_risk']) }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Risques de Blessure</h3>
                        <p class="text-sm text-gray-500">Analyser les risques</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 