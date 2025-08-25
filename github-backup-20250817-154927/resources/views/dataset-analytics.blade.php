@extends('layouts.app')

@section('title', 'Dataset Analytics - FIT Platform')

@section('content')
<div id="dataset-analytics" class="min-h-screen bg-gray-50">
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
                                    Dataset Analytics
                                </h1>
                                <p class="text-sm text-gray-600">Évaluation de la Valeur et Qualité des Données FIFA</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Dashboard</a>
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
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Analytics des Données FIFA</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Évaluez la valeur et la qualité de votre dataset avec des métriques en temps réel
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Couverture 100%
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Score de Valeur 8.7/10
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                            Qualité 87.3%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation des Onglets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-4">
                <div class="flex flex-wrap gap-2">
                    <button 
                        onclick="changeTab('overview')"
                        class="px-6 py-3 rounded-lg font-medium text-sm transition-all duration-200 bg-purple-600 text-white shadow-md"
                        id="btn-overview"
                    >
                        📊 Vue d'Ensemble
                    </button>
                    <button 
                        onclick="changeTab('data-quality')"
                        class="px-6 py-3 rounded-lg font-medium text-sm transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200"
                        id="btn-data-quality"
                    >
                        🛡️ Qualité des Données
                    </button>
                    <button 
                        onclick="changeTab('coverage')"
                        class="px-6 py-3 rounded-lg font-medium text-sm transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200"
                        id="btn-coverage"
                    >
                        📈 Couverture
                    </button>
                    <button 
                        onclick="changeTab('trends')"
                        class="px-6 py-3 rounded-lg font-medium text-sm transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200"
                        id="btn-trends"
                    >
                        📈 Tendances
                    </button>
                    <button 
                        onclick="changeTab('value-assessment')"
                        class="px-6 py-3 rounded-lg font-medium text-sm transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200"
                        id="btn-value-assessment"
                    >
                        ⭐ Évaluation de Valeur
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenu Principal -->
        <div id="tab-overview" class="space-y-8">
            <!-- Métriques Globales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-2xl">
                                    👥
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Joueurs</h3>
                                <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($overviewData['totalPlayers']) }}</div>
                                <div class="text-sm text-gray-600 mb-4">Joueurs enregistrés dans le système</div>
                                <div class="text-sm text-green-600 font-medium">
                                    📈 {{ $overviewData['playersGrowth'] }}% ce mois
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-2xl">
                                    💾
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Enregistrements</h3>
                                <div class="text-3xl font-bold text-green-600 mb-2">{{ number_format($overviewData['totalRecords']) }}</div>
                                <div class="text-sm text-gray-600 mb-4">Données stockées dans la base</div>
                                <div class="text-sm text-green-600 font-medium">
                                    📈 {{ $overviewData['recordsGrowth'] }}% ce mois
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center text-2xl">
                                    📊
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Qualité des Données</h3>
                                <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $overviewData['avgDataQuality'] }}%</div>
                                <div class="text-sm text-gray-600 mb-4">Score moyen de qualité</div>
                                <div class="text-sm text-yellow-600 font-medium">
                                    📈 {{ $overviewData['qualityGrowth'] }}% ce mois
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center text-2xl">
                                    ⭐
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Score de Valeur</h3>
                                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $overviewData['datasetValue'] }}</div>
                                <div class="text-sm text-gray-600 mb-4">Valeur globale du dataset</div>
                                <div class="text-sm text-purple-600 font-medium">
                                    🏆 {{ $overviewData['datasetValue'] >= 9.0 ? 'Excellent' : ($overviewData['datasetValue'] >= 8.0 ? 'Très Bon' : ($overviewData['datasetValue'] >= 7.0 ? 'Bon' : 'Moyen')) }}/10
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques de Vue d'Ensemble -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                            📊 Répartition par Type de Données
                        </h3>
                        <div class="h-80">
                            <canvas id="dataTypeChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                            📈 Évolution de la Qualité
                        </h3>
                        <div class="h-80">
                            <canvas id="qualityEvolutionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                    <!-- Onglet Qualité des Données -->
            <div id="tab-data-quality" class="space-y-8" style="display: none;">
            <!-- Score de Qualité Global -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        🛡️ Score de Qualité Global
                    </h3>
                    <div class="text-center mb-8">
                        <div class="text-6xl font-bold text-green-600 mb-4" id="global-quality-score">--</div>
                        <div class="text-xl text-gray-600 mb-2">Qualité Globale des Données</div>
                        <div class="text-lg text-green-600 font-semibold" id="global-quality-rating">--</div>
                    </div>
                    
                    <!-- Barre de Progression -->
                    <div class="w-full bg-gray-200 rounded-full h-4 mb-8">
                        <div class="bg-green-500 h-4 rounded-full transition-all duration-1000" id="quality-progress-bar" style="width: 0%"></div>
                    </div>
                    
                    <!-- Métriques de Qualité -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 mb-2" id="completeness-score">--</div>
                            <div class="text-lg text-gray-700">Complétude</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-600 mb-2" id="accuracy-score">--</div>
                            <div class="text-lg text-gray-700">Précision</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600 mb-2" id="consistency-score">--</div>
                            <div class="text-lg text-gray-700">Cohérence</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analyse Détaillée par Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        📋 Qualité par Table
                    </h3>
                    <div class="space-y-4" id="tables-quality-container">
                        <!-- Les données des tables seront chargées dynamiquement ici -->
                        <div class="text-center py-8">
                            <div class="text-gray-500">Chargement des données de qualité...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Couverture -->
        <div id="tab-coverage" class="space-y-8" style="display: none;">
            <!-- Couverture Globale -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        📈 Couverture des Données
                    </h3>
                    <div class="text-center mb-8">
                        <div class="text-6xl font-bold text-green-600 mb-4">100%</div>
                        <div class="text-xl text-gray-600">Couverture Globale</div>
                        <div class="text-lg text-green-600 font-semibold">Toutes les données affichées sont couvertes</div>
                    </div>
                </div>
            </div>

            <!-- Couverture par Catégorie -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            ⚽ Couverture Sportive
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-lg text-gray-700">Statistiques Offensives</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">100%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg text-gray-700">Statistiques Physiques</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">100%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg text-gray-700">Statistiques Techniques</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">100%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg text-gray-700">Statistiques de Match</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">100%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg text-gray-700">Performances</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">100%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            ❤️ Couverture Médicale
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-lg text-gray-700">Signaux Vitaux</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-blue-600">100%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg text-gray-700">Métriques de Santé</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-blue-600">100%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg text-gray-700">Sommeil</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-blue-600">100%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg text-gray-700">Stress & Bien-être</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-blue-600">100%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-lg text-gray-700">Récupération</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                                    </div>
                                    <span class="text-lg font-bold text-blue-600">100%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Tendances -->
        <div id="tab-trends" class="space-y-8" style="display: none;">
            <!-- Évolution Temporelle -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        📈 Évolution des Données
                    </h3>
                    <div class="h-80">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Analyse des Patterns -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            📊 Croissance des Données
                        </h3>
                        <div class="space-y-6" id="trends-growth-container">
                            <!-- Les données de croissance seront chargées dynamiquement ici -->
                            <div class="text-center py-8">
                                <div class="text-gray-500">Chargement des tendances...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            ⏰ Fréquence de Mise à Jour
                        </h3>
                        <div class="space-y-6" id="trends-updates-container">
                            <!-- Les données de mise à jour seront chargées dynamiquement ici -->
                            <div class="text-center py-8">
                                <div class="text-gray-500">Chargement des fréquences...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Évaluation de Valeur -->
        <div id="tab-value-assessment" class="space-y-8" style="display: none;">
            <!-- Score de Valeur Global -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        ⭐ Évaluation de la Valeur du Dataset
                    </h3>
                    <div class="text-center mb-8">
                        <div class="text-6xl font-bold text-yellow-600 mb-4" id="value-overall-score">--</div>
                        <div class="text-xl text-gray-600 mb-2">Score Global de Valeur</div>
                        <div class="text-lg text-yellow-600 font-semibold" id="value-overall-rating">--</div>
                    </div>
                    
                    <!-- Critères d'Évaluation -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="value-criteria-container">
                        <!-- Les critères seront chargés dynamiquement ici -->
                        <div class="text-center py-8 col-span-3">
                            <div class="text-gray-500">Chargement des critères...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analyse des Points Forts et Faibles -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            👍 Points Forts
                        </h3>
                        <div class="space-y-4" id="value-strengths-container">
                            <!-- Les points forts seront chargés dynamiquement ici -->
                            <div class="text-center py-8">
                                <div class="text-gray-500">Chargement des points forts...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            💡 Points d'Amélioration
                        </h3>
                        <div class="space-y-4" id="value-improvements-container">
                            <!-- Les points d'amélioration seront chargés dynamiquement ici -->
                            <div class="text-center py-8">
                                <div class="text-gray-500">Chargement des améliorations...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommandations -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        💡 Recommandations
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="value-recommendations-container">
                        <!-- Les recommandations seront chargées dynamiquement ici -->
                        <div class="text-center py-8 col-span-2">
                            <div class="text-gray-500">Chargement des recommandations...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les graphiques après le chargement de la page
        setTimeout(() => {
            initializeCharts();
        }, 500);
    });
    
    // Fonction pour changer l'onglet actif
    function changeTab(tabName) {
        console.log('Changement d\'onglet vers:', tabName);
        
        // Masquer tous les contenus d'onglets
        const allTabs = ['overview', 'data-quality', 'coverage', 'trends', 'value-assessment'];
        allTabs.forEach(tab => {
            const tabContent = document.getElementById('tab-' + tab);
            if (tabContent) {
                tabContent.style.display = 'none';
                console.log('Masqué:', tab);
            }
        });
        
        // Afficher l'onglet sélectionné
        const selectedTab = document.getElementById('tab-' + tabName);
        if (selectedTab) {
            selectedTab.style.display = 'block';
            console.log('Affiché:', tabName);
        } else {
            console.error('Onglet non trouvé:', tabName);
        }
        
        // Mettre à jour les boutons d'onglets
        allTabs.forEach(tab => {
            const button = document.querySelector(`[onclick="changeTab('${tab}')"]`);
            if (button) {
                if (tab === tabName) {
                    button.className = 'px-6 py-3 rounded-lg font-medium text-sm transition-all duration-200 bg-purple-600 text-white shadow-md';
                } else {
                    button.className = 'px-6 py-3 rounded-lg font-medium text-sm transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200';
                }
            }
        });
        
        // Initialiser les graphiques si nécessaire
        if (tabName === 'overview' || tabName === 'trends') {
            setTimeout(() => {
                initializeCharts();
            }, 100);
        }
    }
            
    // Fonction pour initialiser les graphiques
    function initializeCharts() {
        createDataTypeChart();
        createQualityEvolutionChart();
        createTrendsChart();
    }
            
    function createDataTypeChart() {
                const ctx = document.getElementById('dataTypeChart');
                if (!ctx) return;
                
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Joueurs', 'Statistiques', 'Santé', 'Appareils', 'SDOH', 'Matchs'],
                        datasets: [{
                            data: [{{ $overviewData['totalPlayers'] }}, 15678, 45678, 2345, 1234, 23456],
                            backgroundColor: [
                                '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#374151',
                                    font: { size: 12 }
                                }
                            }
                        }
                    }
                });
            }
            
    function createQualityEvolutionChart() {
                const ctx = document.getElementById('qualityEvolutionChart');
                if (!ctx) return;
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août'],
                        datasets: [{
                            label: 'Qualité Globale',
                            data: [75, 78, 82, 85, 87, 89, 88, {{ $overviewData['avgDataQuality'] }}],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#374151' }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: { color: '#374151' }
                            },
                            x: {
                                ticks: { color: '#374151' }
                            }
                        }
                    }
                });
            }
            
    function createTrendsChart() {
                const ctx = document.getElementById('trendsChart');
                if (!ctx) return;
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Joueurs', 'Santé', 'Matchs', 'Appareils', 'SDOH'],
                        datasets: [{
                            label: 'Croissance (%)',
                            data: [{{ $overviewData['playersGrowth'] }}, 23.8, 18.2, 7.3, 15.6],
                            backgroundColor: [
                                '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#374151' }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { color: '#374151' }
                            },
                            x: {
                                ticks: { color: '#374151' }
                            }
                        }
                    }
                });
    }
    
    // Fonction pour charger les données de qualité
    async function loadDataQualityData() {
        try {
            const response = await fetch('/api/dataset-analytics/data-quality');
            const data = await response.json();
            
            if (data.success) {
                // Mettre à jour le score global
                document.getElementById('global-quality-score').textContent = data.data.globalScore + '%';
                document.getElementById('global-quality-rating').textContent = data.data.globalRating;
                document.getElementById('quality-progress-bar').style.width = data.data.globalScore + '%';
                
                // Mettre à jour les métriques
                document.getElementById('completeness-score').textContent = data.data.completeness + '%';
                document.getElementById('accuracy-score').textContent = data.data.accuracy + '%';
                document.getElementById('consistency-score').textContent = data.data.consistency + '%';
                
                // Mettre à jour les tables
                updateTablesQuality(data.data.tables);
            }
        } catch (error) {
            console.error('Erreur lors du chargement des données de qualité:', error);
        }
    }
    
    // Fonction pour mettre à jour la qualité des tables
    function updateTablesQuality(tables) {
        const container = document.getElementById('tables-quality-container');
        if (!container) return;
        
        let html = '';
        tables.forEach(table => {
            const colorClass = table.color === 'green' ? 'bg-green-500' : 
                             table.color === 'yellow' ? 'bg-yellow-500' : 
                             table.color === 'orange' ? 'bg-orange-500' : 'bg-red-500';
            
            html += `
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full mr-3 ${colorClass}"></span>
                        <span class="text-lg font-semibold text-gray-700">${table.name}</span>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="text-center">
                            <div class="text-lg font-bold text-green-600">${table.score}%</div>
                            <div class="text-sm text-gray-500">Score</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-700">${table.records}</div>
                            <div class="text-sm text-gray-500">Enregistrements</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-700">${table.fields}</div>
                            <div class="text-sm text-gray-500">Champs</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-700">${table.lastUpdate}</div>
                            <div class="text-sm text-gray-500">Dernière MAJ</div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Fonction pour charger les données de tendances
    async function loadTrendsData() {
        try {
            const response = await fetch('/api/dataset-analytics/trends');
            const data = await response.json();
            
            if (data.success) {
                updateTrendsGrowth(data.data.growth);
                updateTrendsUpdates(data.data.updates);
            }
        } catch (error) {
            console.error('Erreur lors du chargement des tendances:', error);
        }
    }
    
    // Fonction pour mettre à jour la croissance
    function updateTrendsGrowth(growth) {
        const container = document.getElementById('trends-growth-container');
        if (!container) return;
        
        let html = '';
        growth.forEach(trend => {
            const colorClass = trend.color === 'green' ? 'text-green-600' : 
                             trend.color === 'blue' ? 'text-blue-600' : 
                             trend.color === 'yellow' ? 'text-yellow-600' : 'text-purple-600';
            
            html += `
                <div class="flex items-center justify-between">
                    <span class="text-lg text-gray-700">${trend.metric}</span>
                    <div class="flex items-center space-x-3">
                        <span class="text-lg font-bold ${colorClass}">${trend.value}</span>
                        <span class="text-sm ${colorClass}">${trend.change}</span>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Fonction pour mettre à jour les fréquences
    function updateTrendsUpdates(updates) {
        const container = document.getElementById('trends-updates-container');
        if (!container) return;
        
        let html = '';
        updates.forEach(update => {
            html += `
                <div class="flex items-center justify-between">
                    <span class="text-lg text-gray-700">${update.table}</span>
                    <div class="flex items-center space-x-3">
                        <span class="text-lg font-bold text-green-600">${update.frequency}</span>
                        <span class="text-sm text-gray-500">par jour</span>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Fonction pour charger l'évaluation de valeur
    async function loadValueAssessmentData() {
        try {
            const response = await fetch('/api/dataset-analytics/value-assessment');
            const data = await response.json();
            
            if (data.success) {
                // Mettre à jour le score global
                document.getElementById('value-overall-score').textContent = data.data.overallScore;
                document.getElementById('value-overall-rating').textContent = data.data.overallRating;
                
                // Mettre à jour les critères
                updateValueCriteria(data.data.criteria);
                
                // Mettre à jour les points forts
                updateValueStrengths(data.data.strengths);
                
                // Mettre à jour les améliorations
                updateValueImprovements(data.data.improvements);
                
                // Mettre à jour les recommandations
                updateValueRecommendations(data.data.recommendations);
            }
        } catch (error) {
            console.error('Erreur lors du chargement de l\'évaluation de valeur:', error);
        }
    }
    
    // Fonction pour mettre à jour les critères
    function updateValueCriteria(criteria) {
        const container = document.getElementById('value-criteria-container');
        if (!container) return;
        
        let html = '';
        criteria.forEach(criterion => {
            const colorClass = criterion.color === 'green' ? 'text-green-600' : 
                             criterion.color === 'blue' ? 'text-blue-600' : 
                             criterion.color === 'yellow' ? 'text-yellow-600' : 'text-orange-600';
            
            html += `
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <div class="text-4xl font-bold mb-2 ${colorClass}">${criterion.score}</div>
                    <div class="text-lg text-gray-700 mb-2">${criterion.name}</div>
                    <div class="text-sm text-gray-500">${criterion.description}</div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Fonction pour mettre à jour les points forts
    function updateValueStrengths(strengths) {
        const container = document.getElementById('value-strengths-container');
        if (!container) return;
        
        let html = '';
        strengths.forEach(strength => {
            html += `
                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                    <span class="text-green-500 text-xl">✅</span>
                    <div>
                        <div class="font-semibold text-green-700">${strength.title}</div>
                        <div class="text-sm text-green-600">${strength.description}</div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Fonction pour mettre à jour les améliorations
    function updateValueImprovements(improvements) {
        const container = document.getElementById('value-improvements-container');
        if (!container) return;
        
        let html = '';
        improvements.forEach(improvement => {
            html += `
                <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg border-l-4 border-orange-500">
                    <span class="text-green-500 text-xl">💡</span>
                    <div>
                        <div class="font-semibold text-orange-700">${improvement.title}</div>
                        <div class="text-sm text-orange-600">${improvement.description}</div>
                        <div class="text-xs text-orange-500">
                            <strong>Impact :</strong> <span>${improvement.impact}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Fonction pour mettre à jour les recommandations
    function updateValueRecommendations(recommendations) {
        const container = document.getElementById('value-recommendations-container');
        if (!container) return;
        
        let html = '';
        recommendations.forEach(recommendation => {
            html += `
                <div class="p-6 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center mb-3">
                        <span class="text-blue-500 text-xl mr-3">➡️</span>
                        <div class="font-semibold text-blue-700">${recommendation.title}</div>
                    </div>
                    <div class="text-sm text-blue-600 mb-3">${recommendation.description}</div>
                    <div class="text-xs text-blue-500">
                        <strong>Impact :</strong> <span>${recommendation.impact}</span>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Charger les données au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Charger les données des onglets dynamiques
        loadDataQualityData();
        loadTrendsData();
        loadValueAssessmentData();
    });
</script>
@endsection
