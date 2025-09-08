@extends('layouts.app')

@section('title', 'PCMA Fraud Detection - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-red-600 to-orange-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">🛡️</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    PCMA Fraud Detection
                                </h1>
                                <p class="text-sm text-gray-600">Détection de fraude pour les évaluations PCMA</p>
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
        <!-- Fraud Statistics -->
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
                        <p class="text-2xl font-bold text-gray-900">{{ $fraudStats['total_pcma'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Fraude Détectée</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $fraudStats['fraudulent_pcma'] }}</p>
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
                        <p class="text-sm font-medium text-gray-600">Fraude d'Âge</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $fraudStats['age_fraud'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Fraude d'Identité</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $fraudStats['identity_fraud'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Fraud Detection Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">🤖 Détection de Fraude IA</h3>
                <div class="flex items-center space-x-4">
                    <button id="batchAnalyzeBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        🔍 Analyser Tous les PCMA
                    </button>
                    <button id="testFraudBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        🧪 Test IA
                    </button>
                </div>
            </div>
            
            <div id="aiAnalysisStatus" class="hidden">
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-yellow-600"></div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">
                                Analyse IA en cours...
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="aiAnalysisResults" class="hidden">
                <div class="bg-white border border-gray-200 rounded-md p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Résultats de l'Analyse IA</h4>
                    <div id="aiAnalysisContent" class="text-sm text-gray-700"></div>
                </div>
            </div>
        </div>

        <!-- Fraudulent PCMA Records -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🚨 PCMA Frauduleux Détectés</h3>
            
            @if($fraudulentPCMA->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Joueur
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type de Fraude
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Score de Risque
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($fraudulentPCMA as $pCMA)
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
                                                    ID: {{ $pCMA->player->fifa_connect_id ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if(strpos($pCMA->fraud_type, 'age') !== false) bg-yellow-100 text-yellow-800
                                            @elseif(strpos($pCMA->fraud_type, 'identity') !== false) bg-purple-100 text-purple-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $pCMA->fraud_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="h-2 rounded-full 
                                                    @if($pCMA->risk_score > 70) bg-red-500
                                                    @elseif($pCMA->risk_score > 40) bg-yellow-500
                                                    @else bg-green-500 @endif"
                                                     style="width: {{ $pCMA->risk_score }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $pCMA->risk_score }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($pCMA->status === 'rejected') bg-red-100 text-red-800
                                            @elseif($pCMA->status === 'flagged') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($pCMA->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($pCMA->assessment_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="analyzePCMA({{ $pCMA->id }})" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            🔍 Analyser
                                        </button>
                                        <button onclick="viewDetails({{ $pCMA->id }})" 
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
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune fraude détectée</h3>
                        <p class="mt-1 text-sm text-gray-500">Tous les PCMA semblent légitimes.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const batchAnalyzeBtn = document.getElementById('batchAnalyzeBtn');
    const testFraudBtn = document.getElementById('testFraudBtn');
    const aiAnalysisStatus = document.getElementById('aiAnalysisStatus');
    const aiAnalysisResults = document.getElementById('aiAnalysisResults');
    const aiAnalysisContent = document.getElementById('aiAnalysisContent');

    // Batch analyze all PCMA records
    batchAnalyzeBtn.addEventListener('click', async function() {
        aiAnalysisStatus.classList.remove('hidden');
        aiAnalysisResults.classList.add('hidden');
        batchAnalyzeBtn.disabled = true;
        batchAnalyzeBtn.textContent = '🔍 Analyse en cours...';

        try {
            const response = await fetch('/api/v1/pcma/fraud-detection/batch-analyze', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (response.ok) {
                aiAnalysisContent.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Total Analysé:</span>
                            <span class="text-blue-600">${result.summary.total_analyzed}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Fraude Détectée:</span>
                            <span class="text-red-600">${result.summary.fraud_detected}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Score de Risque Moyen:</span>
                            <span class="text-yellow-600">${result.summary.average_risk_score.toFixed(1)}%</span>
                        </div>
                        <div class="mt-3">
                            <span class="font-medium">Types de Fraude:</span>
                            <ul class="mt-1 text-gray-600">
                                ${Object.entries(result.summary.fraud_types).map(([type, count]) => 
                                    `<li>• ${type}: ${count} cas</li>`
                                ).join('')}
                            </ul>
                        </div>
                    </div>
                `;
                
                aiAnalysisResults.classList.remove('hidden');
                
            } else {
                throw new Error(result.message || 'Erreur lors de l\'analyse');
            }

        } catch (error) {
            console.error('Batch analysis error:', error);
            aiAnalysisContent.innerHTML = `<div class="text-red-600">Erreur: ${error.message}</div>`;
            aiAnalysisResults.classList.remove('hidden');
        } finally {
            aiAnalysisStatus.classList.add('hidden');
            batchAnalyzeBtn.disabled = false;
            batchAnalyzeBtn.textContent = '🔍 Analyser Tous les PCMA';
        }
    });

    // Test fraud detection
    testFraudBtn.addEventListener('click', async function() {
        aiAnalysisStatus.classList.remove('hidden');
        aiAnalysisResults.classList.add('hidden');
        testFraudBtn.disabled = true;
        testFraudBtn.textContent = '🧪 Test en cours...';

        try {
            const response = await fetch('/api/v1/pcma/fraud-detection/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (response.ok) {
                aiAnalysisContent.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Test IA:</span>
                            <span class="text-green-600">✅ Réussi</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Fraude Détectée:</span>
                            <span class="text-red-600">${result.analysis.fraud_detected ? 'Oui' : 'Non'}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Score de Risque:</span>
                            <span class="text-yellow-600">${result.analysis.risk_score}%</span>
                        </div>
                        <div class="mt-3">
                            <span class="font-medium">Analyse:</span>
                            <p class="mt-1 text-gray-600">${result.analysis.detailed_analysis}</p>
                        </div>
                    </div>
                `;
                
                aiAnalysisResults.classList.remove('hidden');
                
            } else {
                throw new Error(result.message || 'Erreur lors du test');
            }

        } catch (error) {
            console.error('Test error:', error);
            aiAnalysisContent.innerHTML = `<div class="text-red-600">Erreur: ${error.message}</div>`;
            aiAnalysisResults.classList.remove('hidden');
        } finally {
            aiAnalysisStatus.classList.add('hidden');
            testFraudBtn.disabled = false;
            testFraudBtn.textContent = '🧪 Test IA';
        }
    });
});

// Analyze specific PCMA record
async function analyzePCMA(pCMAId) {
    try {
        const response = await fetch(`/api/v1/pcma/fraud-detection/analyze/${pCMAId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (response.ok) {
            alert(`Analyse PCMA ${pCMAId}:\nFraude détectée: ${result.fraud_detected ? 'Oui' : 'Non'}\nScore de risque: ${result.risk_score}%`);
        } else {
            alert(`Erreur: ${result.message}`);
        }

    } catch (error) {
        console.error('PCMA analysis error:', error);
        alert('Erreur lors de l\'analyse PCMA');
    }
}

// View PCMA details
function viewDetails(pCMAId) {
    window.open(`/pcma/${pCMAId}`, '_blank');
}
</script>
@endsection 