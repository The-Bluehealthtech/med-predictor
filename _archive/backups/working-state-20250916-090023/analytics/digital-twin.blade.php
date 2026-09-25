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
            
            <!-- Alternative buttons with direct links -->
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h4 class="text-sm font-medium text-yellow-800 mb-2">Alternative: Boutons avec liens directs</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('fifa.analytics') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-center transition-colors block">
                        🔄 Simulation (FIFA Analytics)
                    </a>
                    <a href="{{ route('performances.analytics') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors block">
                        ⚡ Optimisation (Performance Analytics)
                    </a>
                    <a href="{{ route('analytics.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors block">
                        🔮 Prédiction (Analytics Dashboard)
                    </a>
                    <a href="{{ route('analytics.digital-twin') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors block">
                        📊 Analytics (Digital Twin)
                    </a>
                </div>
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
// Test script execution
console.log('Digital Twin script loaded successfully');
alert('Script chargé - Test initial');

function startSimulation() {
    alert('🔄 Simulation démarrée!');
    console.log('Digital Twin Simulation started');
    
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '🔄 Simulation en cours...';
    btn.disabled = true;
    
    // Simulate simulation process
    setTimeout(() => {
        btn.innerHTML = '✅ Simulation terminée';
        btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
        btn.classList.add('bg-green-600');
        
        // Show results
        showNotification('🔄 Simulation terminée avec succès! Modèle de performance généré.', 'success');
        
        // Reset button after 3 seconds
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('bg-green-600');
            btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
        }, 3000);
    }, 2000);
}

function startOptimization() {
    alert('⚡ Optimisation démarrée!');
    console.log('Digital Twin Optimization started');
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '⚡ Optimisation en cours...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = '✅ Optimisation terminée';
        btn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
        btn.classList.add('bg-green-600');
        
        showNotification('⚡ Optimisation terminée! Paramètres optimaux identifiés.', 'success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('bg-green-600');
            btn.classList.add('bg-purple-600', 'hover:bg-purple-700');
        }, 3000);
    }, 2500);
    
    console.log('Digital Twin Optimization started');
}

function startPrediction() {
    alert('🔮 Prédiction démarrée!');
    console.log('Digital Twin Prediction started');
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '🔮 Prédiction en cours...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = '✅ Prédiction terminée';
        btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        btn.classList.add('bg-green-600');
        
        showNotification('🔮 Prédiction terminée! Tendances futures identifiées.', 'success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('bg-green-600');
            btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }, 3000);
    }, 3000);
    
    console.log('Digital Twin Prediction started');
}

function showAnalytics() {
    alert('📊 Analytics démarrés!');
    console.log('Digital Twin Analytics displayed');
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '📊 Chargement...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = '📊 Analytics chargés';
        btn.classList.remove('bg-green-600', 'hover:bg-green-700');
        btn.classList.add('bg-blue-600');
        
        showNotification('📊 Analytics chargés! Données d\'analyse disponibles.', 'info');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('bg-blue-600');
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
        }, 3000);
    }, 1500);
    
    console.log('Digital Twin Analytics displayed');
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">×</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
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

// Add click handlers for better UX
document.addEventListener('DOMContentLoaded', function() {
    console.log('Digital Twin Network initialized');
    
    // Add hover effects to feature cards
    const featureCards = document.querySelectorAll('.bg-white.rounded-lg.shadow-md.p-6');
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

    <!-- Detailed Information Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Access by Role Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔐 Accès par Rôle</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                    <h4 class="font-medium text-red-900 mb-2">System Admin</h4>
                    <p class="text-sm text-red-700">Accès complet</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-medium text-blue-900 mb-2">Association Admin</h4>
                    <p class="text-sm text-blue-700">Gestion des équipes</p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <h4 class="font-medium text-green-900 mb-2">Medical Director</h4>
                    <p class="text-sm text-green-700">Accès aux données médicales</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <h4 class="font-medium text-purple-900 mb-2">Coach</h4>
                    <p class="text-sm text-purple-700">Simulation et optimisation</p>
                </div>
            </div>
        </div>

        <!-- Module Objectives Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Objectifs du Module</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                    <h4 class="font-medium text-indigo-900 mb-3">1. Optimisation des Performances</h4>
                    <ul class="space-y-2 text-sm text-indigo-700">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                            Modélisation prédictive des performances
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                            Identification des facteurs de réussite
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                            Optimisation des programmes d'entraînement
                        </li>
                    </ul>
                </div>
                <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                    <h4 class="font-medium text-red-900 mb-3">2. Prévention des Blessures</h4>
                    <ul class="space-y-2 text-sm text-red-700">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Évaluation des risques de blessure
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Optimisation des temps de récupération
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Recommandations personnalisées
                        </li>
                    </ul>
                </div>
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <h4 class="font-medium text-green-900 mb-3">3. Planification Stratégique</h4>
                    <ul class="space-y-2 text-sm text-green-700">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Simulation d'équipe pour les stratégies
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Analyse comparative des joueurs
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Optimisation des compositions d'équipe
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Current Status Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔧 État Actuel</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <h4 class="font-medium text-green-900 mb-3">✅ Fonctionnalités Opérationnelles</h4>
                    <ul class="space-y-2 text-sm text-green-700">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Interface utilisateur complète
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système de simulation de base
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Intégration avec les modules analytiques
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système de permissions RBAC
                        </li>
                    </ul>
                </div>
                <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <h4 class="font-medium text-yellow-900 mb-3">🟡 En Développement</h4>
                    <ul class="space-y-2 text-sm text-yellow-700">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                            Optimisation des temps de récupération
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                            Métriques de performance en temps réel
                        </li>
                    </ul>
                </div>
                <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                    <h4 class="font-medium text-red-900 mb-3">🔴 Planifiées</h4>
                    <ul class="space-y-2 text-sm text-red-700">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Simulation de stratégie d'équipe
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Analytics alimentés par l'IA
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Innovation Section -->
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Innovation Technologique</h3>
            <div class="text-center">
                <p class="text-lg text-gray-700 mb-4">
                    Le module Digital Twin représente une <strong>innovation majeure</strong> dans le football professionnel en combinant :
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                    <div class="p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-2xl mb-2">🔢</div>
                        <p class="font-medium text-gray-900">Modélisation numérique avancée</p>
                    </div>
                    <div class="p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-2xl mb-2">🤖</div>
                        <p class="font-medium text-gray-900">Intelligence artificielle prédictive</p>
                    </div>
                    <div class="p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-2xl mb-2">🔗</div>
                        <p class="font-medium text-gray-900">Intégration de données multi-sources</p>
                    </div>
                    <div class="p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-2xl mb-2">🎨</div>
                        <p class="font-medium text-gray-900">Interface utilisateur intuitive</p>
                    </div>
                </div>
                <div class="mt-6 p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-lg font-medium text-gray-900">
                        C'est un outil puissant pour <strong>optimiser les performances</strong>, <strong>prévenir les blessures</strong> et <strong>améliorer la planification stratégique</strong> des équipes de football ! ⚽🤖
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection 