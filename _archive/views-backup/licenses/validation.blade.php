@extends('layouts.app')

@section('title', 'Validation des Licences - Association')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">🏛️ Validation des Licences</h1>
            <p class="text-gray-600 mt-2">Association - Gestion des demandes de licences en attente avec détection de fraude IA</p>
        </div>

        <!-- Fraud Detection Statistics -->
        <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-red-800">🛡️ Détection de Fraude IA</h3>
                    <p class="text-sm text-red-600">Analyse automatique des demandes de licences pour détecter les fraudes</p>
                </div>
                <div class="flex space-x-4">
                    <button id="runFraudDetection" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        🔍 Analyser Fraude
                    </button>
                    <button id="batchFraudCheck" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        🚨 Vérification Globale
                    </button>
                </div>
            </div>
            
            <!-- Fraud Detection Status Indicators -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-3 border border-red-200">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-red-800">Risque Élevé</span>
                    </div>
                    <p class="text-xs text-red-600 mt-1">Licences nécessitant une vérification immédiate</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-yellow-200">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-yellow-800">Risque Moyen</span>
                    </div>
                    <p class="text-xs text-yellow-600 mt-1">Licences nécessitant une vérification manuelle</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-green-200">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-green-800">Risque Faible</span>
                    </div>
                    <p class="text-xs text-green-600 mt-1">Licences approuvées automatiquement</p>
                </div>
            </div>
            
            <div id="fraudDetectionStatus" class="hidden mt-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-yellow-600"></div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">
                                Analyse de fraude en cours...
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="fraudDetectionResults" class="hidden mt-4">
                <div class="bg-white border border-gray-200 rounded-md p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Résultats de l'Analyse de Fraude</h4>
                    <div id="fraudDetectionContent" class="text-sm text-gray-700"></div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-wrap gap-4">
                <div>
                    <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select id="status_filter" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="approved">Approuvées</option>
                        <option value="rejected">Rejetées</option>
                        <option value="fraud_detected">Fraude Détectée</option>
                    </select>
                </div>
                <div>
                    <label for="type_filter" class="block text-sm font-medium text-gray-700 mb-2">Type de licence</label>
                    <select id="type_filter" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les types</option>
                        <option value="player">Licence Joueur</option>
                        <option value="staff">Licence Staff</option>
                        <option value="medical">Licence Médicale</option>
                    </select>
                </div>
                <div>
                    <label for="club_filter" class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                    <select id="club_filter" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les clubs</option>
                        @foreach($clubs ?? [] as $club)
                            <option value="{{ $club->id }}">{{ $club->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="fraud_filter" class="block text-sm font-medium text-gray-700 mb-2">Risque de Fraude</label>
                    <select id="fraud_filter" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les risques</option>
                        <option value="high">Risque Élevé</option>
                        <option value="medium">Risque Moyen</option>
                        <option value="low">Risque Faible</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-800">En attente</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $pendingCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-800">Approuvées</p>
                        <p class="text-2xl font-bold text-green-900">{{ $approvedCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-red-800">Rejetées</p>
                        <p class="text-2xl font-bold text-red-900">{{ $rejectedCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-purple-800">Fraude Détectée</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $fraudDetectedCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-800">Total</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $totalCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des demandes -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Demandes de Licences</h2>
            </div>
            
            @if($licenses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Demandeur
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Club
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date de demande
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Risque de Fraude
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($licenses as $license)
                                <tr class="hover:bg-gray-50 @if($license->fraud_risk === 'high') bg-red-50 @elseif($license->fraud_risk === 'medium') bg-yellow-50 @endif">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $license->applicant_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $license->email }}</div>
                                            @if($license->fraud_risk === 'high')
                                                <div class="text-xs text-red-600 font-medium">🚨 Risque élevé</div>
                                            @elseif($license->fraud_risk === 'medium')
                                                <div class="text-xs text-yellow-600 font-medium">⚠️ Risque moyen</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $license->license_type_label }}</div>
                                        <div class="text-sm text-gray-500">{{ $license->position }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $license->club->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $license->club->city ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $license->requested_at ? $license->requested_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($license->fraud_risk === 'high')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                🔴 Élevé
                                            </span>
                                        @elseif($license->fraud_risk === 'medium')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                🟡 Moyen
                                            </span>
                                        @elseif($license->fraud_risk === 'low')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                🟢 Faible
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                ⚪ Non évalué
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {!! $license->status_badge !!}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="viewLicense({{ $license->id }})" 
                                                    class="text-blue-600 hover:text-blue-900">
                                                Voir
                                            </button>
                                            <button onclick="runFraudAnalysis({{ $license->id }})" 
                                                    class="text-purple-600 hover:text-purple-900">
                                                🔍 Fraude
                                            </button>
                                            @if($license->isPending())
                                                <button onclick="approveLicense({{ $license->id }})" 
                                                        class="text-green-600 hover:text-green-900">
                                                    Approuver
                                                </button>
                                                <button onclick="rejectLicense({{ $license->id }})" 
                                                        class="text-red-600 hover:text-red-900">
                                                    Rejeter
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $licenses->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune demande de licence</h3>
                    <p class="mt-1 text-sm text-gray-500">Aucune demande de licence en attente de validation.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de détails de licence -->
<div id="licenseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Détails de la Licence</h3>
                <button onclick="closeLicenseModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="licenseModalContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Modal de fraude -->
<div id="fraudModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-red-900">🚨 Analyse de Fraude</h3>
                <button onclick="closeFraudModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="fraudModalContent">
                <!-- Fraud analysis content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const runFraudDetectionBtn = document.getElementById('runFraudDetection');
    const batchFraudCheckBtn = document.getElementById('batchFraudCheck');
    const fraudDetectionStatus = document.getElementById('fraudDetectionStatus');
    const fraudDetectionResults = document.getElementById('fraudDetectionResults');
    const fraudDetectionContent = document.getElementById('fraudDetectionContent');

    // Run fraud detection on all licenses
    runFraudDetectionBtn.addEventListener('click', async function() {
        fraudDetectionStatus.classList.remove('hidden');
        fraudDetectionResults.classList.add('hidden');
        runFraudDetectionBtn.disabled = true;
        runFraudDetectionBtn.textContent = '🔍 Analyse en cours...';

        try {
            const response = await fetch('/api/v1/licenses/fraud-detection/batch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (response.ok) {
                fraudDetectionContent.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Total Analysé:</span>
                            <span class="text-blue-600">${result.total_analyzed || 0}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Fraude Détectée:</span>
                            <span class="text-red-600">${result.fraud_detected || 0}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Risque Élevé:</span>
                            <span class="text-red-600">${result.high_risk || 0}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Risque Moyen:</span>
                            <span class="text-yellow-600">${result.medium_risk || 0}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Risque Faible:</span>
                            <span class="text-green-600">${result.low_risk || 0}</span>
                        </div>
                    </div>
                `;
                
                fraudDetectionResults.classList.remove('hidden');
                
                // Reload page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                
            } else {
                throw new Error(result.message || 'Erreur lors de l\'analyse');
            }

        } catch (error) {
            console.error('Fraud detection error:', error);
            fraudDetectionContent.innerHTML = `<div class="text-red-600">Erreur: ${error.message}</div>`;
            fraudDetectionResults.classList.remove('hidden');
        } finally {
            fraudDetectionStatus.classList.add('hidden');
            runFraudDetectionBtn.disabled = false;
            runFraudDetectionBtn.textContent = '🔍 Analyser Fraude';
        }
    });

    // Batch fraud check
    batchFraudCheckBtn.addEventListener('click', async function() {
        fraudDetectionStatus.classList.remove('hidden');
        fraudDetectionResults.classList.add('hidden');
        batchFraudCheckBtn.disabled = true;
        batchFraudCheckBtn.textContent = '🚨 Vérification en cours...';

        try {
            const response = await fetch('/api/v1/licenses/fraud-detection/check-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (response.ok) {
                fraudDetectionContent.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Vérification Globale:</span>
                            <span class="text-green-600">✅ Terminée</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Licences Vérifiées:</span>
                            <span class="text-blue-600">${result.total_checked || 0}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Alertes Générées:</span>
                            <span class="text-red-600">${result.alerts_generated || 0}</span>
                        </div>
                        <div class="mt-3">
                            <span class="font-medium">Résumé:</span>
                            <p class="mt-1 text-gray-600">${result.summary || 'Vérification terminée avec succès'}</p>
                        </div>
                    </div>
                `;
                
                fraudDetectionResults.classList.remove('hidden');
                
            } else {
                throw new Error(result.message || 'Erreur lors de la vérification');
            }

        } catch (error) {
            console.error('Batch fraud check error:', error);
            fraudDetectionContent.innerHTML = `<div class="text-red-600">Erreur: ${error.message}</div>`;
            fraudDetectionResults.classList.remove('hidden');
        } finally {
            fraudDetectionStatus.classList.add('hidden');
            batchFraudCheckBtn.disabled = false;
            batchFraudCheckBtn.textContent = '🚨 Vérification Globale';
        }
    });
});

// Run fraud analysis on specific license
async function runFraudAnalysis(licenseId) {
    try {
        const response = await fetch(`/api/v1/licenses/fraud-detection/analyze/${licenseId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (response.ok) {
            // Show fraud modal with results
            const fraudModal = document.getElementById('fraudModal');
            const fraudModalContent = document.getElementById('fraudModalContent');
            
            fraudModalContent.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Résultats de l'Analyse de Fraude</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-red-700">Risque de Fraude:</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    ${result.fraud_detected ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                                    ${result.fraud_detected ? '🚨 Détecté' : '✅ Aucun risque'}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-red-700">Score de Risque:</span>
                                <span class="text-sm font-medium">${result.risk_score || 0}%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-red-700">Type de Fraude:</span>
                                <span class="text-sm font-medium">${result.fraud_types?.join(', ') || 'Aucun'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-gray-800 mb-2">Analyse Détaillée</h4>
                        <p class="text-sm text-gray-700">${result.detailed_analysis || 'Aucune analyse disponible'}</p>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Recommandations</h4>
                        <p class="text-sm text-blue-700">${result.recommendations || 'Aucune recommandation'}</p>
                    </div>
                </div>
            `;
            
            fraudModal.classList.remove('hidden');
            
        } else {
            alert(`Erreur: ${result.message}`);
        }

    } catch (error) {
        console.error('Fraud analysis error:', error);
        alert('Erreur lors de l\'analyse de fraude');
    }
}

// Close fraud modal
function closeFraudModal() {
    document.getElementById('fraudModal').classList.add('hidden');
}

// Existing functions
function viewLicense(licenseId) {
    // Show loading state
    const modal = document.getElementById('licenseModal');
    const content = document.getElementById('licenseModalContent');
    content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2 text-gray-600">Chargement...</p></div>';
    modal.classList.remove('hidden');
    
    // Charger les détails de la licence via AJAX
    fetch(`/licenses/${licenseId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-gray-900">Informations du demandeur</h4>
                        <p><strong>Nom:</strong> ${data.applicant_name || 'N/A'}</p>
                        <p><strong>Email:</strong> ${data.email || 'N/A'}</p>
                        <p><strong>Téléphone:</strong> ${data.phone || 'N/A'}</p>
                        <p><strong>Date de naissance:</strong> ${data.date_of_birth || 'N/A'}</p>
                        <p><strong>Nationalité:</strong> ${data.nationality || 'N/A'}</p>
                        <p><strong>Position:</strong> ${data.position || 'N/A'}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900">Informations de la licence</h4>
                        <p><strong>Type:</strong> ${data.license_type_label || 'N/A'}</p>
                        <p><strong>Club:</strong> ${data.club?.name || 'N/A'}</p>
                        <p><strong>Motif:</strong> ${data.license_reason || 'N/A'}</p>
                        <p><strong>Validité:</strong> ${data.validity_period_label || 'N/A'}</p>
                        <p><strong>Statut:</strong> ${data.status_badge || 'N/A'}</p>
                        <p><strong>Risque de fraude:</strong> ${data.fraud_risk || 'Non évalué'}</p>
                        <p><strong>Score de fraude:</strong> ${data.fraud_score || 'N/A'}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900">Documents</h4>
                    <div class="space-y-2">
                        ${data.documents ? Object.entries(data.documents).map(([key, value]) => 
                            `<p><strong>${key}:</strong> <a href="/storage/${value}" target="_blank" class="text-blue-600 hover:underline">Voir le document</a></p>`
                        ).join('') : 'Aucun document'}
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    <p><strong>Créé le:</strong> ${data.created_at || 'N/A'}</p>
                    <p><strong>Modifié le:</strong> ${data.updated_at || 'N/A'}</p>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading license details:', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="text-red-600 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Erreur de chargement</h3>
                    <p class="text-gray-600">Impossible de charger les détails de la licence.</p>
                    <p class="text-sm text-gray-500 mt-2">${error.message}</p>
                </div>
            `;
        });
    }

function approveLicense(licenseId) {
    if (confirm('Êtes-vous sûr de vouloir approuver cette licence ?')) {
        fetch(`/licenses/${licenseId}/approve`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'approbation');
            }
        });
    }
}

function rejectLicense(licenseId) {
    const reason = prompt('Motif du rejet:');
    if (reason !== null) {
        fetch(`/licenses/${licenseId}/reject`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ rejection_reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors du rejet');
            }
        });
    }
}

function closeLicenseModal() {
    document.getElementById('licenseModal').classList.add('hidden');
}
</script>
@endsection 