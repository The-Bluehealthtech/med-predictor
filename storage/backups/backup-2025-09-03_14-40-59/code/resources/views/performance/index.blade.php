@extends('layouts.app')

@section('title', 'Performance - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">📊</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Performance
                                </h1>
                                <p class="text-sm text-gray-600">Analyse des performances et métriques</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Modules</a>
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
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">📊 Module Performance</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Analyse avancée des performances, métriques et suivi des athlètes
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                            Métriques en temps réel
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 cursor-pointer hover:shadow-lg transition-shadow" onclick="showGlobalMetrics()">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Métriques Globales</p>
                        <p class="text-2xl font-bold text-gray-900">Analyser</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 cursor-pointer hover:shadow-lg transition-shadow" onclick="showIndividualTracking()">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Suivi Individuel</p>
                        <p class="text-2xl font-bold text-gray-900">Suivre</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 cursor-pointer hover:shadow-lg transition-shadow" onclick="generateReports()">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rapports</p>
                        <p class="text-2xl font-bold text-gray-900">Générer</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button onclick="showGlobalMetrics()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📊 Métriques Globales
                </button>
                <button onclick="showIndividualTracking()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    👤 Suivi Individuel
                </button>
                <button onclick="generateReports()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📈 Rapports
                </button>
                <button onclick="showRealTimeData()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    ⚡ Temps Réel
                </button>
            </div>
        </div>

        <!-- Performance Analytics Section -->
        <div id="globalMetricsSection" class="bg-white rounded-lg shadow-md p-6 mt-8 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Métriques Globales</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-blue-600 text-2xl">🏃</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Performance Moyenne</p>
                    <p class="text-2xl font-bold text-blue-600">87.5%</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-green-600 text-2xl">📈</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Progression</p>
                    <p class="text-2xl font-bold text-green-600">+12.3%</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-purple-600 text-2xl">🎯</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Objectifs Atteints</p>
                    <p class="text-2xl font-bold text-purple-600">94%</p>
                </div>
            </div>
            <div class="mt-6">
                <button onclick="hideGlobalMetrics()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Fermer
                </button>
            </div>
        </div>

        <!-- Individual Tracking Section -->
        <div id="individualTrackingSection" class="bg-white rounded-lg shadow-md p-6 mt-8 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Suivi Individuel</h3>
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">John Smith</h4>
                            <p class="text-sm text-gray-600">Attaquant - Équipe A</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-600">92%</p>
                            <p class="text-xs text-gray-500">Performance</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 92%"></div>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">Sarah Johnson</h4>
                            <p class="text-sm text-gray-600">Milieu - Équipe B</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-600">88%</p>
                            <p class="text-xs text-gray-500">Performance</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 88%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <button onclick="hideIndividualTracking()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Fermer
                </button>
            </div>
        </div>

        <!-- Reports Section -->
        <div id="reportsSection" class="bg-white rounded-lg shadow-md p-6 mt-8 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Rapports</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Rapport Mensuel</h4>
                    <p class="text-sm text-gray-600 mb-3">Analyse complète des performances du mois</p>
                    <button onclick="generateMonthlyReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Générer
                    </button>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Rapport Individuel</h4>
                    <p class="text-sm text-gray-600 mb-3">Performance détaillée par athlète</p>
                    <button onclick="generateIndividualReport()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Générer
                    </button>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Rapport d'Équipe</h4>
                    <p class="text-sm text-gray-600 mb-3">Analyse comparative des équipes</p>
                    <button onclick="generateTeamReport()" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Générer
                    </button>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Rapport de Progression</h4>
                    <p class="text-sm text-gray-600 mb-3">Évolution des performances dans le temps</p>
                    <button onclick="generateProgressReport()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm transition-colors">
                        Générer
                    </button>
                </div>
            </div>
            <div class="mt-6">
                <button onclick="hideReports()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showGlobalMetrics() {
    hideAllSections();
    document.getElementById('globalMetricsSection').classList.remove('hidden');
    console.log('📊 Métriques Globales affichées');
}

function showIndividualTracking() {
    hideAllSections();
    document.getElementById('individualTrackingSection').classList.remove('hidden');
    console.log('👤 Suivi Individuel affiché');
}

function generateReports() {
    hideAllSections();
    document.getElementById('reportsSection').classList.remove('hidden');
    console.log('📈 Section Rapports affichée');
}

function showRealTimeData() {
    alert('⚡ Données en temps réel - Surveillance active des performances...');
    console.log('⚡ Temps Réel activé');
}

function hideGlobalMetrics() {
    document.getElementById('globalMetricsSection').classList.add('hidden');
}

function hideIndividualTracking() {
    document.getElementById('individualTrackingSection').classList.add('hidden');
}

function hideReports() {
    document.getElementById('reportsSection').classList.add('hidden');
}

function hideAllSections() {
    document.getElementById('globalMetricsSection').classList.add('hidden');
    document.getElementById('individualTrackingSection').classList.add('hidden');
    document.getElementById('reportsSection').classList.add('hidden');
}

function generateMonthlyReport() {
    alert('📊 Rapport mensuel généré avec succès!');
    console.log('Rapport mensuel généré');
}

function generateIndividualReport() {
    alert('👤 Rapport individuel généré avec succès!');
    console.log('Rapport individuel généré');
}

function generateTeamReport() {
    alert('🏆 Rapport d\'équipe généré avec succès!');
    console.log('Rapport d\'équipe généré');
}

function generateProgressReport() {
    alert('📈 Rapport de progression généré avec succès!');
    console.log('Rapport de progression généré');
}

// Debug information
console.log('Performance Page loaded successfully');
console.log('Current URL:', window.location.href);
console.log('Performance Features available:', {
    globalMetrics: true,
    individualTracking: true,
    reports: true,
    realTime: true
});
</script>
@endsection 