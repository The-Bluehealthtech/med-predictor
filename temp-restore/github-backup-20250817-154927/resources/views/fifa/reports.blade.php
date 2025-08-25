@extends('layouts.app')

@section('title', 'FIFA Dashboard - Rapports')

@section('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endsection

@section('content')
<!-- Cache busting: {{ time() }} -->
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900">FIFA Dashboard - Rapports</h1>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-500">Version: {{ date('Y-m-d-H-i-s') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">FIFA Reports</h1>
                <p class="mt-2 text-gray-600">Rapports et documents du système FIFA</p>
            </div>

            <!-- Navigation -->
            <div class="border-b border-gray-200 mb-8">
                <nav class="flex space-x-8">
                    <a href="{{ route('fifa-dashboard.index') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                        Vue d'ensemble
                    </a>
                    <a href="{{ route('fifa-dashboard.transfers') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                        Transferts
                    </a>
                    <a href="{{ route('fifa-dashboard.contracts') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                        Contrats
                    </a>
                    <a href="{{ route('fifa-dashboard.federations') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                        Fédérations
                    </a>
                    <a href="{{ route('fifa-dashboard.analytics') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                        Analytics
                    </a>
                    <a href="{{ route('fifa-dashboard.reports') }}" class="border-b-2 border-blue-500 text-gray-900 py-4 px-1 text-sm font-medium">
                        Rapports
                    </a>
                </nav>
            </div>

            <!-- Reports Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Test Button -->
                <div class="lg:col-span-2 mb-4">
                    <button onclick="testJavaScript()" class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors duration-200">
                        Test JavaScript
                    </button>
                    <button onclick="forceReload()" class="bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors duration-200 ml-2">
                        Force Reload (Clear Cache)
                    </button>
                </div>
                
                <!-- Authentication Notice -->
                <div class="lg:col-span-2 mb-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Authentification requise</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Vous devez être connecté pour générer des rapports FIFA. Si vous n'êtes pas connecté, vous serez redirigé vers la page de connexion.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Available Reports -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rapports Disponibles</h3>
                    
                    <div class="space-y-4">
                        <!-- Transfer Report -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Rapport des Transferts</h4>
                                    <p class="text-sm text-gray-500">Transferts par période, statut et fédération</p>
                                </div>
                                <button onclick="generateReport('transfers')" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                    <span class="button-text">Générer</span>
                                    <span class="loading-spinner hidden">Génération...</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Contract Report -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Rapport des Contrats</h4>
                                    <p class="text-sm text-gray-500">Contrats actifs, expirés et renouvellements</p>
                                </div>
                                <button onclick="generateReport('contracts')" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                    <span class="button-text">Générer</span>
                                    <span class="loading-spinner hidden">Génération...</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Federation Report -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Rapport des Fédérations</h4>
                                    <p class="text-sm text-gray-500">Activité par fédération et région</p>
                                </div>
                                <button onclick="generateReport('federations')" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                    <span class="button-text">Générer</span>
                                    <span class="loading-spinner hidden">Génération...</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Compliance Report -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Rapport de Conformité</h4>
                                    <p class="text-sm text-gray-500">Conformité FIFA et réglementations</p>
                                </div>
                                <button onclick="generateReport('compliance')" class="bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                    <span class="button-text">Générer</span>
                                    <span class="loading-spinner hidden">Génération...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Reports -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rapports Récents</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Rapport Mensuel - Juillet 2025</h4>
                                <p class="text-xs text-gray-500">Généré le 19/07/2025 à 14:30</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewReport('monthly-july-2025')" class="text-blue-600 hover:text-blue-900 text-sm">Voir</button>
                                <button onclick="downloadReport_v{{ time() }}('monthly-july-2025')" class="text-green-600 hover:text-green-900 text-sm">Télécharger</button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Rapport des Transferts - Q2 2025</h4>
                                <p class="text-xs text-gray-500">Généré le 15/07/2025 à 09:15</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewReport('transfers-q2-2025')" class="text-blue-600 hover:text-blue-900 text-sm">Voir</button>
                                <button onclick="downloadReport_v{{ time() }}('transfers-q2-2025')" class="text-green-600 hover:text-green-900 text-sm">Télécharger</button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Rapport de Conformité - Juin 2025</h4>
                                <p class="text-xs text-gray-500">Généré le 30/06/2025 à 16:45</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewReport('compliance-june-2025')" class="text-blue-600 hover:text-blue-900 text-sm">Voir</button>
                                <button onclick="downloadReport_v{{ time() }}('compliance-june-2025')" class="text-green-600 hover:text-green-900 text-sm">Télécharger</button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Rapport des Fédérations - Q1 2025</h4>
                                <p class="text-xs text-gray-500">Généré le 15/04/2025 à 11:20</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewReport('federations-q1-2025')" class="text-blue-600 hover:text-blue-900 text-sm">Voir</button>
                                <button onclick="downloadReport_v{{ time() }}('federations-q1-2025')" class="text-green-600 hover:text-green-900 text-sm">Télécharger</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Templates -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Modèles de Rapports</h3>
                    
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Rapport Standard FIFA</h4>
                                    <p class="text-sm text-gray-500">Format officiel FIFA pour les rapports</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Utiliser
                                </button>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Rapport Exécutif</h4>
                                    <p class="text-sm text-gray-500">Résumé pour la direction</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Utiliser
                                </button>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Rapport Technique</h4>
                                    <p class="text-sm text-gray-500">Détails techniques et métriques</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Utiliser
                                </button>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Rapport Personnalisé</h4>
                                    <p class="text-sm text-gray-500">Créer votre propre modèle</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Créer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Settings -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Paramètres des Rapports</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Format d'export par défaut</label>
                            <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                                <option value="json">JSON</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Langue par défaut</label>
                            <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="fr">Français</option>
                                <option value="en">English</option>
                                <option value="es">Español</option>
                                <option value="de">Deutsch</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fuseau horaire</label>
                            <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="Europe/Paris">Europe/Paris</option>
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">America/New_York</option>
                                <option value="Asia/Tokyo">Asia/Tokyo</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                            <label class="ml-2 block text-sm text-gray-900">
                                Inclure les graphiques dans les rapports
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                            <label class="ml-2 block text-sm text-gray-900">
                                Envoyer par email automatiquement
                            </label>
                        </div>
                        
                        <div class="pt-4">
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                Sauvegarder les Paramètres
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notification" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg id="notification-icon" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p id="notification-message" class="text-sm font-medium text-gray-900"></p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="hideNotification()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // FIFA Reports JavaScript - Version: {{ time() }}
    // Cache busting: {{ date('Y-m-d-H-i-s') }}

    // Test function to verify JavaScript is working
    function testJavaScript() {
        showNotification('JavaScript fonctionne correctement!', 'success');
    }

    // Force reload function to clear cache
    function forceReload() {
        showNotification('Rechargement forcé de la page...', 'info');
        
        // Clear all caches and reload
        if ('caches' in window) {
            caches.keys().then(function(names) {
                for (let name of names) {
                    caches.delete(name);
                }
            });
        }
        
        // Force reload with cache clearing
        setTimeout(() => {
            window.location.reload(true);
        }, 1000);
    }

    // Versioned function alias to force cache refresh
    function downloadReport_v{{ time() }}(reportId) {
        return downloadReport(reportId);
    }

    function generateReport(reportType) {
        const button = event.target.closest('button');
        const buttonText = button.querySelector('.button-text');
        const loadingSpinner = button.querySelector('.loading-spinner');
        
        // Show loading state
        button.disabled = true;
        buttonText.classList.add('hidden');
        loadingSpinner.classList.remove('hidden');
        
        // Get CSRF token
        const tokenElement = document.querySelector('meta[name="csrf-token"]');
        
        if (!tokenElement) {
            showNotification('Erreur: Token de sécurité manquant', 'error');
            return;
        }
        
        const token = tokenElement.getAttribute('content');
        const url = `/fifa-dashboard/reports/generate/${reportType}`;
        
        // Make API request
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // Handle different response statuses
            if (response.status === 419) {
                throw new Error('CSRF token mismatch. Please refresh the page and try again.');
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                
                // Add to recent reports
                addToRecentReports(reportType, data.download_url);
                
                // Trigger download if URL is provided
                if (data.download_url) {
                    setTimeout(() => {
                        window.open(data.download_url, '_blank');
                    }, 1000);
                }
            } else {
                showNotification(data.message || 'Erreur lors de la génération du rapport', 'error');
            }
        })
        .catch(error => {
            // Provide specific error messages based on error type
            let errorMessage = 'Erreur lors de la génération du rapport';
            
            if (error.message.includes('CSRF token mismatch')) {
                errorMessage = 'Erreur de sécurité. Veuillez actualiser la page et réessayer.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Erreur de connexion. Vérifiez votre connexion internet.';
            } else if (error.message.includes('HTTP error')) {
                if (error.message.includes('401') || error.message.includes('403')) {
                    errorMessage = 'Authentification requise. Veuillez vous connecter pour générer des rapports.';
                } else if (error.message.includes('404')) {
                    errorMessage = 'Service non disponible. Veuillez réessayer plus tard.';
                } else {
                    errorMessage = 'Erreur serveur. Veuillez réessayer plus tard.';
                }
            }
            
            showNotification(errorMessage, 'error');
            
            // If authentication error, suggest login
            if (errorMessage.includes('Authentification requise')) {
                setTimeout(() => {
                    if (confirm('Voulez-vous être redirigé vers la page de connexion ?')) {
                        window.location.href = '/login';
                    }
                }, 2000);
            }
        })
        .finally(() => {
            // Reset button state
            button.disabled = false;
            buttonText.classList.remove('hidden');
            loadingSpinner.classList.add('hidden');
        });
    }

    function showNotification(message, type = 'success') {
        const notification = document.getElementById('notification');
        const messageElement = document.getElementById('notification-message');
        const icon = document.getElementById('notification-icon');
        
        messageElement.textContent = message;
        
        // Update icon based on type
        if (type === 'success') {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
            icon.classList.remove('text-red-600', 'text-yellow-600', 'text-blue-600');
            icon.classList.add('text-green-600');
        } else if (type === 'error') {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />';
            icon.classList.remove('text-green-600', 'text-yellow-600', 'text-blue-600');
            icon.classList.add('text-red-600');
        } else if (type === 'warning') {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />';
            icon.classList.remove('text-green-600', 'text-red-600', 'text-blue-600');
            icon.classList.add('text-yellow-600');
        } else if (type === 'info') {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
            icon.classList.remove('text-green-600', 'text-red-600', 'text-yellow-600');
            icon.classList.add('text-blue-600');
        }
        
        notification.classList.remove('hidden');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            hideNotification();
        }, 5000);
    }

    function hideNotification() {
        const notification = document.getElementById('notification');
        notification.classList.add('hidden');
    }

    function addToRecentReports(reportType, downloadUrl) {
        const recentReportsContainer = document.querySelector('.space-y-4');
        const reportNames = {
            'transfers': 'Rapport des Transferts',
            'contracts': 'Rapport des Contrats',
            'federations': 'Rapport des Fédérations',
            'compliance': 'Rapport de Conformité'
        };
        
        const now = new Date();
        const timeString = now.toLocaleString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const newReport = document.createElement('div');
        newReport.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
        newReport.innerHTML = `
            <div>
                <h4 class="text-sm font-medium text-gray-900">${reportNames[reportType]} - ${now.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })}</h4>
                <p class="text-xs text-gray-500">Généré le ${timeString}</p>
            </div>
            <div class="flex space-x-2">
                <button onclick="viewReport('${reportType}-${now.getTime()}')" class="text-blue-600 hover:text-blue-900 text-sm">Voir</button>
                <button onclick="window.open('${downloadUrl}', '_blank')" class="text-green-600 hover:text-green-900 text-sm">Télécharger</button>
            </div>
        `;
        
        // Insert at the top of recent reports
        const firstReport = recentReportsContainer.querySelector('.flex.items-center.justify-between.p-3.bg-gray-50.rounded-lg');
        if (firstReport) {
            recentReportsContainer.insertBefore(newReport, firstReport);
        } else {
            recentReportsContainer.appendChild(newReport);
        }
    }

    function viewReport(reportId) {
        // Map report IDs to readable names
        const reportNames = {
            'monthly-july-2025': 'Rapport Mensuel - Juillet 2025',
            'transfers-q2-2025': 'Rapport des Transferts - Q2 2025',
            'compliance-june-2025': 'Rapport de Conformité - Juin 2025',
            'federations-q1-2025': 'Rapport des Fédérations - Q1 2025'
        };
        
        const reportName = reportNames[reportId] || reportId;
        showNotification(`Ouverture du rapport: ${reportName}`, 'info');
        
        // In a real application, you would:
        // 1. Make an API call to get the report data
        // 2. Open a modal or navigate to a detail page
        // 3. Display the report content
        
        // For now, simulate opening a modal
        setTimeout(() => {
            alert(`Rapport: ${reportName}\n\nCeci est une simulation. Dans une vraie application, le rapport s'ouvrirait dans une fenêtre dédiée.`);
        }, 500);
    }

    function downloadReport(reportId) {
        // Map report IDs to readable names
        const reportNames = {
            'monthly-july-2025': 'Rapport Mensuel - Juillet 2025',
            'transfers-q2-2025': 'Rapport des Transferts - Q2 2025',
            'compliance-june-2025': 'Rapport de Conformité - Juin 2025',
            'federations-q1-2025': 'Rapport des Fédérations - Q1 2025'
        };
        
        const reportName = reportNames[reportId] || reportId;
        showNotification(`Téléchargement du rapport: ${reportName}`, 'info');
        
        // In a real application, you would:
        // 1. Make an API call to generate/download the report
        // 2. Trigger the actual file download
        
        // Generate filename in the correct format: type-YYYY-MM-DD-HH-MM-SS.pdf
        setTimeout(() => {
            const now = new Date();
            const timestamp = now.getFullYear() + '-' + 
                             String(now.getMonth() + 1).padStart(2, '0') + '-' +
                             String(now.getDate()).padStart(2, '0') + '-' +
                             String(now.getHours()).padStart(2, '0') + '-' +
                             String(now.getMinutes()).padStart(2, '0') + '-' +
                             String(now.getSeconds()).padStart(2, '0');
            
            // Map report IDs to valid types
            const typeMap = {
                'monthly-july-2025': 'transfers',
                'transfers-q2-2025': 'transfers',
                'compliance-june-2025': 'compliance',
                'federations-q1-2025': 'federations'
            };
            
            const reportType = typeMap[reportId] || 'transfers';
            const filename = `${reportType}-${timestamp}.pdf`;
            const downloadUrl = `/fifa-dashboard/reports/download/${filename}`;
            
            window.open(downloadUrl, '_blank');
        }, 1000);
    }
    </script>

    @endsection 