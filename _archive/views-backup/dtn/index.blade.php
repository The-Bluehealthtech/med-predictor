@extends('layouts.app')

@section('title', 'DTN - Direction Technique Nationale')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">⚽</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    DTN - Direction Technique Nationale
                                </h1>
                                <p class="text-sm text-gray-600">Direction technique et développement du football</p>
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
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">⚽ Direction Technique Nationale</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Coordination et développement technique du football national
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Direction active
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Coordination nationale
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DTN Core Functions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Formation</p>
                        <p class="text-2xl font-bold text-gray-900">Éduquer</p>
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
                        <p class="text-sm font-medium text-gray-600">Développement</p>
                        <p class="text-2xl font-bold text-gray-900">Développer</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Coordination</p>
                        <p class="text-2xl font-bold text-gray-900">Coordonner</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Planning Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Planification Technique</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Programmes de Formation</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Formation des entraîneurs
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Formation des arbitres
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                            Formation des dirigeants
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Formation des médecins sportifs
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Développement Technique</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Méthodologie d'entraînement
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Scouting et détection
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                            Analyse vidéo
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            Innovation technologique
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button onclick="manageFormation()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📚 Formation
                </button>
                <button onclick="manageDevelopment()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🌱 Développement
                </button>
                <button onclick="manageCoordination()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🤝 Coordination
                </button>
                <button onclick="showReports()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📊 Rapports
                </button>
            </div>
        </div>

        <!-- Status Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut du Système</h3>
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
                    <p class="text-sm font-medium text-gray-900">Formation</p>
                    <p class="text-xs text-gray-500">En cours</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <span class="text-red-600 text-2xl">❌</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Innovation</p>
                    <p class="text-xs text-gray-500">En développement</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function manageFormation() {
    // Create and show formation modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">📚 Gestion des Formations</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-medium text-blue-900 mb-2">Formation des Entraîneurs</h4>
                            <p class="text-sm text-blue-700">Programmes de formation pour les entraîneurs de tous niveaux</p>
                            <button onclick="openFormationProgram('coaches')" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">Voir les programmes →</button>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <h4 class="font-medium text-green-900 mb-2">Formation des Arbitres</h4>
                            <p class="text-sm text-green-700">Formation et certification des arbitres</p>
                            <button onclick="openFormationProgram('referees')" class="mt-2 text-green-600 hover:text-green-800 text-sm font-medium">Voir les programmes →</button>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <h4 class="font-medium text-purple-900 mb-2">Formation des Dirigeants</h4>
                            <p class="text-sm text-purple-700">Formation en gestion et leadership</p>
                            <button onclick="openFormationProgram('managers')" class="mt-2 text-purple-600 hover:text-purple-800 text-sm font-medium">Voir les programmes →</button>
                        </div>
                        <div class="p-4 bg-orange-50 rounded-lg">
                            <h4 class="font-medium text-orange-900 mb-2">Formation Médicale</h4>
                            <p class="text-sm text-orange-700">Formation des médecins sportifs</p>
                            <button onclick="openFormationProgram('medical')" class="mt-2 text-orange-600 hover:text-orange-800 text-sm font-medium">Voir les programmes →</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    console.log('DTN Formation management started');
}

function manageDevelopment() {
    // Create and show development modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">🌱 Développement Technique</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-green-50 rounded-lg">
                            <h4 class="font-medium text-green-900 mb-2">Méthodologie d'Entraînement</h4>
                            <p class="text-sm text-green-700">Nouvelles méthodes et techniques d'entraînement</p>
                            <button onclick="openDevelopmentProgram('methodology')" class="mt-2 text-green-600 hover:text-green-800 text-sm font-medium">Explorer →</button>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-medium text-blue-900 mb-2">Scouting et Détection</h4>
                            <p class="text-sm text-blue-700">Systèmes de détection des talents</p>
                            <button onclick="openDevelopmentProgram('scouting')" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">Explorer →</button>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <h4 class="font-medium text-purple-900 mb-2">Analyse Vidéo</h4>
                            <p class="text-sm text-purple-700">Outils d'analyse vidéo avancés</p>
                            <button onclick="openDevelopmentProgram('video')" class="mt-2 text-purple-600 hover:text-purple-800 text-sm font-medium">Explorer →</button>
                        </div>
                        <div class="p-4 bg-indigo-50 rounded-lg">
                            <h4 class="font-medium text-indigo-900 mb-2">Innovation Technologique</h4>
                            <p class="text-sm text-indigo-700">Nouvelles technologies pour le football</p>
                            <button onclick="openDevelopmentProgram('innovation')" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">Explorer →</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    console.log('DTN Development management started');
}

function manageCoordination() {
    // Create and show coordination modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">🤝 Coordination Nationale</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <h4 class="font-medium text-purple-900 mb-2">Coordination Régionale</h4>
                            <p class="text-sm text-purple-700">Coordination des activités par région</p>
                            <button onclick="openCoordinationProgram('regional')" class="mt-2 text-purple-600 hover:text-purple-800 text-sm font-medium">Gérer →</button>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-medium text-blue-900 mb-2">Coordination Technique</h4>
                            <p class="text-sm text-blue-700">Coordination des programmes techniques</p>
                            <button onclick="openCoordinationProgram('technical')" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">Gérer →</button>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <h4 class="font-medium text-green-900 mb-2">Coordination Formation</h4>
                            <p class="text-sm text-green-700">Coordination des programmes de formation</p>
                            <button onclick="openCoordinationProgram('training')" class="mt-2 text-green-600 hover:text-green-800 text-sm font-medium">Gérer →</button>
                        </div>
                        <div class="p-4 bg-orange-50 rounded-lg">
                            <h4 class="font-medium text-orange-900 mb-2">Coordination Compétitions</h4>
                            <p class="text-sm text-orange-700">Coordination des compétitions nationales</p>
                            <button onclick="openCoordinationProgram('competitions')" class="mt-2 text-orange-600 hover:text-orange-800 text-sm font-medium">Gérer →</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    console.log('DTN Coordination management started');
}

function showReports() {
    // Create and show reports modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">📊 Rapports Techniques</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-indigo-50 rounded-lg">
                            <h4 class="font-medium text-indigo-900 mb-2">Rapport Formation</h4>
                            <p class="text-sm text-indigo-700">Statistiques des programmes de formation</p>
                            <button onclick="generateReport('formation')" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">Générer →</button>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <h4 class="font-medium text-green-900 mb-2">Rapport Développement</h4>
                            <p class="text-sm text-green-700">Progrès des programmes de développement</p>
                            <button onclick="generateReport('development')" class="mt-2 text-green-600 hover:text-green-800 text-sm font-medium">Générer →</button>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <h4 class="font-medium text-purple-900 mb-2">Rapport Coordination</h4>
                            <p class="text-sm text-purple-700">Activités de coordination nationale</p>
                            <button onclick="generateReport('coordination')" class="mt-2 text-purple-600 hover:text-purple-800 text-sm font-medium">Générer →</button>
                        </div>
                        <div class="p-4 bg-orange-50 rounded-lg">
                            <h4 class="font-medium text-orange-900 mb-2">Rapport Performance</h4>
                            <p class="text-sm text-orange-700">Performance globale du DTN</p>
                            <button onclick="generateReport('performance')" class="mt-2 text-orange-600 hover:text-orange-800 text-sm font-medium">Générer →</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    console.log('DTN Reports displayed');
}

// Helper functions
function closeModal() {
    const modals = document.querySelectorAll('.fixed.inset-0.bg-gray-600.bg-opacity-50');
    modals.forEach(modal => modal.remove());
}

function openFormationProgram(type) {
    alert(`📚 Programme de formation ${type} - Accès aux détails et gestion...`);
    console.log(`Opening formation program: ${type}`);
}

function openDevelopmentProgram(type) {
    alert(`🌱 Programme de développement ${type} - Accès aux outils et ressources...`);
    console.log(`Opening development program: ${type}`);
}

function openCoordinationProgram(type) {
    alert(`🤝 Programme de coordination ${type} - Accès à la gestion...`);
    console.log(`Opening coordination program: ${type}`);
}

function generateReport(type) {
    alert(`📊 Génération du rapport ${type} - Rapport en cours de création...`);
    console.log(`Generating report: ${type}`);
}

// Debug information
console.log('DTN Page loaded successfully');
console.log('Current URL:', window.location.href);
console.log('DTN Features available:', {
    formation: true,
    development: true,
    coordination: true,
    reports: true
});
</script>
@endsection 