@extends('layouts.app')

@section('title', 'Clinical Data Support - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">🏥</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Clinical Data Support
                                </h1>
                                <p class="text-sm text-gray-600">Système de support clinique basé sur Google Gemini</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('pcma.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Dashboard PCMA</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Clinical Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total PCMA</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $insights['total_pcma'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Visites Médicales</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $insights['total_visits'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Alertes Cliniques</p>
                        <p class="text-2xl font-bold text-gray-900">{{ count($insights['clinical_alerts']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">IA Gemini</p>
                        <p class="text-2xl font-bold text-gray-900">Active</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gemini AI Clinical Analysis Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">🤖 Analyse Clinique IA Gemini</h3>
                <div class="flex items-center space-x-4">
                    <button id="testGeminiBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        🧪 Test Gemini
                    </button>
                    <button id="batchAnalyzePCMABtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        🔍 Analyser PCMA
                    </button>
                    <button id="batchAnalyzeVisitsBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        🏥 Analyser Visites
                    </button>
                </div>
            </div>
            
            <div id="geminiAnalysisStatus" class="hidden">
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-yellow-600"></div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">
                                Analyse clinique IA en cours...
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="geminiAnalysisResults" class="hidden">
                <div class="bg-white border border-gray-200 rounded-md p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Résultats de l'Analyse Clinique IA</h4>
                    <div id="geminiAnalysisContent" class="text-sm text-gray-700"></div>
                </div>
            </div>
        </div>

        <!-- Clinical Alerts -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🚨 Alertes Cliniques</h3>
            
            @if(count($insights['clinical_alerts']) > 0)
                <div class="space-y-4">
                    @foreach($insights['clinical_alerts'] as $alert)
                        <div class="flex items-start p-4 border border-gray-200 rounded-lg 
                            @if($alert['severity'] === 'High') bg-red-50 border-red-200
                            @elseif($alert['severity'] === 'Medium') bg-yellow-50 border-yellow-200
                            @else bg-blue-50 border-blue-200 @endif">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center
                                    @if($alert['severity'] === 'High') bg-red-100 text-red-600
                                    @elseif($alert['severity'] === 'Medium') bg-yellow-100 text-yellow-600
                                    @else bg-blue-100 text-blue-600 @endif">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $alert['type'] }}</p>
                                <p class="text-sm text-gray-600">{{ $alert['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($alert['timestamp'])->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="ml-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($alert['severity'] === 'High') bg-red-100 text-red-800
                                    @elseif($alert['severity'] === 'Medium') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $alert['severity'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune alerte clinique</h3>
                        <p class="mt-1 text-sm text-gray-500">Tous les paramètres cliniques sont normaux.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Recent PCMA with Clinical Analysis -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 PCMA Récents avec Analyse Clinique</h3>
            
            @if(count($insights['recent_pcma']) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Joueur
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut Clinique
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Score Fitness
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($insights['recent_pcma'] as $pCMA)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-bold text-sm">
                                                        {{ substr($pCMA->player->first_name ?? 'A', 0, 1) }}{{ substr($pCMA->player->last_name ?? 'B', 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $pCMA->player->first_name ?? 'Unknown' }} {{ $pCMA->player->last_name ?? 'Player' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $pCMA->player->position ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($pCMA->assessment_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($pCMA->medical_clearance === 'Cleared') bg-green-100 text-green-800
                                            @elseif($pCMA->medical_clearance === 'Restricted') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($pCMA->medical_clearance ?? 'Pending') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="h-2 rounded-full 
                                                    @if($pCMA->fitness_test_score > 80) bg-green-500
                                                    @elseif($pCMA->fitness_test_score > 60) bg-yellow-500
                                                    @else bg-red-500 @endif"
                                                     style="width: {{ $pCMA->fitness_test_score }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $pCMA->fitness_test_score }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="analyzePCMAClinical({{ $pCMA->id }})" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            🔍 Analyser
                                        </button>
                                        <button onclick="viewPCMADetails({{ $pCMA->id }})" 
                                                class="text-green-600 hover:text-green-900">
                                            📋 Détails
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun PCMA récent</h3>
                        <p class="mt-1 text-sm text-gray-500">Aucun PCMA n'a été effectué récemment.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Recent Visits with Clinical Analysis -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🏥 Visites Médicales Récentes</h3>
            
            @if(count($insights['recent_visits']) > 0)
                <div class="space-y-4">
                    @foreach($insights['recent_visits'] as $visit)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-bold text-sm">
                                        {{ substr($visit->player->first_name ?? 'A', 0, 1) }}{{ substr($visit->player->last_name ?? 'B', 0, 1) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $visit->player->first_name ?? 'Unknown' }} {{ $visit->player->player->last_name ?? 'Player' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $visit->record_type }} - {{ \Carbon\Carbon::parse($visit->record_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $visit->diagnosis ?? 'No diagnosis' }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button onclick="analyzeVisitClinical({{ $visit->id }})" 
                                        class="text-blue-600 hover:text-blue-900 text-sm">
                                    🔍 Analyser
                                </button>
                                <button onclick="viewVisitDetails({{ $visit->id }})" 
                                        class="text-green-600 hover:text-green-900 text-sm">
                                    📋 Détails
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune visite récente</h3>
                        <p class="mt-1 text-sm text-gray-500">Aucune visite médicale n'a été enregistrée récemment.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testGeminiBtn = document.getElementById('testGeminiBtn');
    const batchAnalyzePCMABtn = document.getElementById('batchAnalyzePCMABtn');
    const batchAnalyzeVisitsBtn = document.getElementById('batchAnalyzeVisitsBtn');
    const geminiAnalysisStatus = document.getElementById('geminiAnalysisStatus');
    const geminiAnalysisResults = document.getElementById('geminiAnalysisResults');
    const geminiAnalysisContent = document.getElementById('geminiAnalysisContent');

    // Test Gemini connection
    testGeminiBtn.addEventListener('click', async function() {
        geminiAnalysisStatus.classList.remove('hidden');
        geminiAnalysisResults.classList.add('hidden');
        testGeminiBtn.disabled = true;
        testGeminiBtn.textContent = '🧪 Test en cours...';

        try {
            const response = await fetch('/api/v1/clinical/test-gemini', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                geminiAnalysisContent.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Test Gemini:</span>
                            <span class="text-green-600">✅ Réussi</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Statut:</span>
                            <span class="text-blue-600">${result.message}</span>
                        </div>
                        <div class="mt-3">
                            <span class="font-medium">Réponse:</span>
                            <p class="mt-1 text-gray-600">${result.response}</p>
                        </div>
                    </div>
                `;
                
                geminiAnalysisResults.classList.remove('hidden');
                
            } else {
                throw new Error(result.message || 'Erreur lors du test');
            }

        } catch (error) {
            console.error('Gemini test error:', error);
            geminiAnalysisContent.innerHTML = `<div class="text-red-600">Erreur: ${error.message}</div>`;
            geminiAnalysisResults.classList.remove('hidden');
        } finally {
            geminiAnalysisStatus.classList.add('hidden');
            testGeminiBtn.disabled = false;
            testGeminiBtn.textContent = '🧪 Test Gemini';
        }
    });

    // Batch analyze PCMA
    batchAnalyzePCMABtn.addEventListener('click', async function() {
        geminiAnalysisStatus.classList.remove('hidden');
        geminiAnalysisResults.classList.add('hidden');
        batchAnalyzePCMABtn.disabled = true;
        batchAnalyzePCMABtn.textContent = '🔍 Analyse en cours...';

        try {
            const response = await fetch('/api/v1/clinical/batch-analyze-pcma', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (response.ok) {
                geminiAnalysisContent.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Total Analysé:</span>
                            <span class="text-blue-600">${result.summary.total_analyzed}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Cas Approuvés:</span>
                            <span class="text-green-600">${result.summary.cleared_cases}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Cas Restreints:</span>
                            <span class="text-yellow-600">${result.summary.restricted_cases}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Cas à Risque Élevé:</span>
                            <span class="text-red-600">${result.summary.high_risk_cases}</span>
                        </div>
                    </div>
                `;
                
                geminiAnalysisResults.classList.remove('hidden');
                
            } else {
                throw new Error(result.message || 'Erreur lors de l\'analyse');
            }

        } catch (error) {
            console.error('PCMA batch analysis error:', error);
            geminiAnalysisContent.innerHTML = `<div class="text-red-600">Erreur: ${error.message}</div>`;
            geminiAnalysisResults.classList.remove('hidden');
        } finally {
            geminiAnalysisStatus.classList.add('hidden');
            batchAnalyzePCMABtn.disabled = false;
            batchAnalyzePCMABtn.textContent = '🔍 Analyser PCMA';
        }
    });

    // Batch analyze visits
    batchAnalyzeVisitsBtn.addEventListener('click', async function() {
        geminiAnalysisStatus.classList.remove('hidden');
        geminiAnalysisResults.classList.add('hidden');
        batchAnalyzeVisitsBtn.disabled = true;
        batchAnalyzeVisitsBtn.textContent = '🏥 Analyse en cours...';

        try {
            const response = await fetch('/api/v1/clinical/batch-analyze-visits', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (response.ok) {
                geminiAnalysisContent.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Total Analysé:</span>
                            <span class="text-blue-600">${result.summary.total_analyzed}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Cas de Blessures:</span>
                            <span class="text-red-600">${result.summary.injury_cases}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Cas de Maladies:</span>
                            <span class="text-yellow-600">${result.summary.illness_cases}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Visites Préventives:</span>
                            <span class="text-green-600">${result.summary.preventive_cases}</span>
                        </div>
                    </div>
                `;
                
                geminiAnalysisResults.classList.remove('hidden');
                
            } else {
                throw new Error(result.message || 'Erreur lors de l\'analyse');
            }

        } catch (error) {
            console.error('Visit batch analysis error:', error);
            geminiAnalysisContent.innerHTML = `<div class="text-red-600">Erreur: ${error.message}</div>`;
            geminiAnalysisResults.classList.remove('hidden');
        } finally {
            geminiAnalysisStatus.classList.add('hidden');
            batchAnalyzeVisitsBtn.disabled = false;
            batchAnalyzeVisitsBtn.textContent = '🏥 Analyser Visites';
        }
    });
});

// Analyze specific PCMA clinical data
async function analyzePCMAClinical(pCMAId) {
    try {
        const response = await fetch(`/api/v1/clinical/analyze-pcma/${pCMAId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (response.ok) {
            alert(`Analyse clinique PCMA ${pCMAId}:\nStatut: ${result.medical_clearance.status}\nAnalyse: ${result.detailed_analysis.substring(0, 100)}...`);
        } else {
            alert(`Erreur: ${result.message}`);
        }

    } catch (error) {
        console.error('PCMA clinical analysis error:', error);
        alert('Erreur lors de l\'analyse clinique PCMA');
    }
}

// Analyze specific visit clinical data
async function analyzeVisitClinical(visitId) {
    try {
        const response = await fetch(`/api/v1/clinical/analyze-visit/${visitId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (response.ok) {
            alert(`Analyse clinique visite ${visitId}:\nÉvaluation: ${result.treatment_evaluation.appropriateness}\nAnalyse: ${result.detailed_analysis.substring(0, 100)}...`);
        } else {
            alert(`Erreur: ${result.message}`);
        }

    } catch (error) {
        console.error('Visit clinical analysis error:', error);
        alert('Erreur lors de l\'analyse clinique visite');
    }
}

// View PCMA details
function viewPCMADetails(pCMAId) {
    window.open(`/pcma/${pCMAId}`, '_blank');
}

// View visit details
function viewVisitDetails(visitId) {
    window.open(`/health-records/${visitId}`, '_blank');
}
</script>
@endsection 