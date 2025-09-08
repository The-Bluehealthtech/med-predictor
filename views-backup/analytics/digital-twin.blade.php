@extends('layouts.app')

@section('title', 'Digital Twin Network - Analytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">🔄</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Digital Twin Network
                                </h1>
                                <p class="text-sm text-gray-600">Simulation et modélisation avancée</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('analytics.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Analytics</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">🔄 Digital Twin Network</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Simulation et modélisation avancée pour l'optimisation des performances
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                            Simulation en temps réel
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DTN Features -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Simulation</p>
                        <p class="text-2xl font-bold text-gray-900">Modéliser</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Optimisation</p>
                        <p class="text-2xl font-bold text-gray-900">Optimiser</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Prédiction</p>
                        <p class="text-2xl font-bold text-gray-900">Prédire</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Planning Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Technical Planning</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Simulation Models</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Performance prediction models
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Injury risk assessment
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                            Recovery time optimization
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Team strategy simulation
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Data Integration</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Wearable device data
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Medical records integration
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                            Real-time performance metrics
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            AI-powered analytics
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button onclick="startSimulation()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔄 Simulation
                </button>
                <button onclick="startOptimization()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    ⚡ Optimisation
                </button>
                <button onclick="startPrediction()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔮 Prédiction
                </button>
                <button onclick="showAnalytics()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📊 Analytics
                </button>
            </div>
        </div>

        <!-- Status Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Système Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-green-600 text-2xl">✅</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">DTN Core</p>
                    <p class="text-xs text-gray-500">Opérationnel</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-yellow-600 text-2xl">⚠️</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Simulation Engine</p>
                    <p class="text-xs text-gray-500">En cours</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-red-600 text-2xl">❌</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">AI Models</p>
                    <p class="text-xs text-gray-500">En développement</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function startSimulation() {
    alert('🔄 Simulation démarrée - Modélisation des performances en cours...');
    console.log('Digital Twin Simulation started');
}

function startOptimization() {
    alert('⚡ Optimisation démarrée - Analyse des données en cours...');
    console.log('Digital Twin Optimization started');
}

function startPrediction() {
    alert('🔮 Prédiction démarrée - Analyse prédictive en cours...');
    console.log('Digital Twin Prediction started');
}

function showAnalytics() {
    alert('📊 Analytics - Affichage des analyses avancées...');
    console.log('Digital Twin Analytics displayed');
}

// Debug information
console.log('Digital Twin Network Page loaded successfully');
console.log('Current URL:', window.location.href);
console.log('Digital Twin Features available:', {
    simulation: true,
    optimization: true,
    prediction: true,
    analytics: true
});
</script>
@endsection 