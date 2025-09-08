@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl mr-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                        🔗
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Intégrations API</h1>
                        <p class="text-gray-600 mt-1">Connectez vos logiciels comptables professionnels</p>
                    </div>
                </div>
                <a href="{{ route('modules.finance.dashboard') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    ← Retour au Dashboard
                </a>
            </div>
        </div>

        <!-- Status des Intégrations -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-4">
                        <span class="text-green-600 text-xl">✅</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Intégrations Actives</h3>
                        <p class="text-2xl font-bold text-green-600">{{ $activeIntegrations ?? 2 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-4">
                        <span class="text-blue-600 text-xl">🔄</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Synchronisations</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ $syncCount ?? 156 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center mr-4">
                        <span class="text-yellow-600 text-xl">⚠️</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Erreurs</h3>
                        <p class="text-2xl font-bold text-yellow-600">{{ $errorCount ?? 3 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logiciels Disponibles -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Logiciels Comptables Disponibles</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Sage -->
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-2xl">📊</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Sage</h3>
                            <p class="text-sm text-gray-600">Comptabilité professionnelle</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✅ Connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Synchronisation automatique des écritures comptables et des rapports financiers.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                            Configurer
                        </button>
                        <button onclick="testConnection('sage')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                    </div>
                </div>

                <!-- QuickBooks -->
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                            <span class="text-green-600 text-2xl">💼</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">QuickBooks</h3>
                            <p class="text-sm text-gray-600">Gestion financière</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✅ Connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Import automatique des transactions et synchronisation des comptes.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Configurer
                        </button>
                        <button onclick="testConnection('quickbooks')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                    </div>
                </div>

                <!-- Xero -->
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                            <span class="text-purple-600 text-2xl">☁️</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Xero</h3>
                            <p class="text-sm text-gray-600">Comptabilité cloud</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            ⚪ Non connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Intégration cloud pour la synchronisation en temps réel.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition-colors">
                            Connecter
                        </button>
                        <button class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            En savoir plus
                        </button>
                    </div>
                </div>

                <!-- Ciel -->
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center mr-3">
                            <span class="text-orange-600 text-2xl">🏢</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Ciel</h3>
                            <p class="text-sm text-gray-600">Solutions comptables</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            ⚪ Non connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Intégration avec les solutions comptables françaises.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-orange-600 text-white text-sm rounded hover:bg-orange-700 transition-colors">
                            Connecter
                        </button>
                        <button class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            En savoir plus
                        </button>
                    </div>
                </div>

                <!-- Excel/CSV -->
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                            <span class="text-gray-600 text-2xl">📋</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Excel/CSV</h3>
                            <p class="text-sm text-gray-600">Import de fichiers</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✅ Disponible
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Import et export de données via fichiers Excel et CSV.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition-colors">
                            Importer
                        </button>
                        <button class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Exporter
                        </button>
                    </div>
                </div>

                <!-- API Personnalisée -->
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                            <span class="text-indigo-600 text-2xl">⚙️</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">API Personnalisée</h3>
                            <p class="text-sm text-gray-600">Intégration sur mesure</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            ⚪ Non configuré
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Connectez votre propre système via API REST.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition-colors">
                            Configurer
                        </button>
                        <button class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Documentation
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des Synchronisations -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Historique des Synchronisations</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logiciel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Éléments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                        <span class="text-blue-600 text-sm">📊</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Sage</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Import</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-15 14:30</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Réussi
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45 transactions</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900">Voir détails</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                                        <span class="text-green-600 text-sm">💼</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">QuickBooks</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Export</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-15 12:15</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Réussi
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">23 comptes</td>
                            <td class="px-6 py-4 whitespace-4 text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900">Voir détails</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                        <span class="text-purple-600 text-sm">☁️</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Xero</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Import</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-14 16:45</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    ⚠️ Erreur
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">0 éléments</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-red-600 hover:text-red-900">Voir erreur</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour tester la connexion avec un logiciel
async function testConnection(software) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Afficher le loading
    button.textContent = 'Test...';
    button.disabled = true;
    button.classList.add('opacity-50');
    
    try {
        const response = await fetch('{{ route("modules.finance.test-connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                software: software
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Succès
            button.textContent = '✅ Connecté';
            button.classList.remove('border-gray-300', 'text-gray-700');
            button.classList.add('border-green-500', 'text-green-700', 'bg-green-50');
            
            // Afficher une notification
            showNotification('Connexion réussie avec ' + software, 'success');
        } else {
            // Erreur
            button.textContent = '❌ Erreur';
            button.classList.remove('border-gray-300', 'text-gray-700');
            button.classList.add('border-red-500', 'text-red-700', 'bg-red-50');
            
            // Afficher une notification d'erreur
            showNotification('Erreur de connexion avec ' + software + ': ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('Erreur lors du test de connexion:', error);
        button.textContent = '❌ Erreur';
        button.classList.remove('border-gray-300', 'text-gray-700');
        button.classList.add('border-red-500', 'text-red-700', 'bg-red-50');
        
        showNotification('Erreur lors du test de connexion', 'error');
    }
    
    // Réinitialiser le bouton après 3 secondes
    setTimeout(() => {
        button.textContent = originalText;
        button.disabled = false;
        button.classList.remove('opacity-50', 'border-green-500', 'text-green-700', 'bg-green-50', 'border-red-500', 'text-red-700', 'bg-red-50');
        button.classList.add('border-gray-300', 'text-gray-700');
    }, 3000);
}

// Fonction pour afficher des notifications
function showNotification(message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span>
            <span>${message}</span>
        </div>
    `;
    
    // Ajouter au DOM
    document.body.appendChild(notification);
    
    // Supprimer après 5 secondes
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Fonction pour synchroniser les données
async function syncData(software, type) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Afficher le loading
    button.textContent = 'Synchronisation...';
    button.disabled = true;
    button.classList.add('opacity-50');
    
    try {
        const response = await fetch('{{ route("modules.finance.sync") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                software: software,
                type: type,
                data: []
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            button.textContent = '✅ Terminé';
            button.classList.remove('border-gray-300', 'text-gray-700');
            button.classList.add('border-green-500', 'text-green-700', 'bg-green-50');
            
            showNotification(`Synchronisation ${type} réussie avec ${software}`, 'success');
        } else {
            button.textContent = '❌ Erreur';
            button.classList.remove('border-gray-300', 'text-gray-700');
            button.classList.add('border-red-500', 'text-red-700', 'bg-red-50');
            
            showNotification('Erreur de synchronisation: ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('Erreur lors de la synchronisation:', error);
        button.textContent = '❌ Erreur';
        button.classList.remove('border-gray-300', 'text-gray-700');
        button.classList.add('border-red-500', 'text-red-700', 'bg-red-50');
        
        showNotification('Erreur lors de la synchronisation', 'error');
    }
    
    // Réinitialiser le bouton après 3 secondes
    setTimeout(() => {
        button.textContent = originalText;
        button.disabled = false;
        button.classList.remove('opacity-50', 'border-green-500', 'text-green-700', 'bg-green-50', 'border-red-500', 'text-red-700', 'bg-red-50');
        button.classList.add('border-gray-300', 'text-gray-700');
    }, 3000);
}
</script>
@endsection
