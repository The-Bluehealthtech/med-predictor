@extends('layouts.app')

@section('title', 'Fraud Detection Reports - Association')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">📊</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Fraud Detection Reports
                                </h1>
                                <p class="text-sm text-gray-600">Rapports de détection de fraude</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('association.fraud-detection.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Report Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtres de Rapports</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="today">Aujourd'hui</option>
                            <option value="week">Cette semaine</option>
                            <option value="month" selected>Ce mois</option>
                            <option value="quarter">Ce trimestre</option>
                            <option value="year">Cette année</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de Rapport</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="summary">Résumé</option>
                            <option value="detailed">Détaillé</option>
                            <option value="trends">Tendances</option>
                            <option value="comparison">Comparaison</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Générer Rapport
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé Mensuel</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-blue-900">Alertes Générées</p>
                            <p class="text-sm text-blue-700">Total des alertes ce mois</p>
                        </div>
                        <span class="text-2xl font-bold text-blue-600">89</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="font-medium text-green-900">Cas Résolus</p>
                            <p class="text-sm text-green-700">Cas traités avec succès</p>
                        </div>
                        <span class="text-2xl font-bold text-green-600">156</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="font-medium text-yellow-900">Taux de Précision</p>
                            <p class="text-sm text-yellow-700">Précision du système</p>
                        </div>
                        <span class="text-2xl font-bold text-yellow-600">98.5%</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div>
                            <p class="font-medium text-purple-900">Temps de Réponse</p>
                            <p class="text-sm text-purple-700">Temps moyen de traitement</p>
                        </div>
                        <span class="text-2xl font-bold text-purple-600">2.3s</span>
                    </div>
                </div>
            </div>

            <!-- Trend Analysis -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Analyse des Tendances</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-red-50 rounded-lg">
                        <h4 class="font-medium text-red-900 mb-2">Tendances des Alertes</h4>
                        <p class="text-sm text-red-700 mb-2">Évolution des alertes sur les 30 derniers jours</p>
                        <div class="flex items-center text-xs text-red-600">
                            <span>Hausse: +15%</span>
                            <span class="mx-2">•</span>
                            <span>Pic: 12 alertes/jour</span>
                        </div>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <h4 class="font-medium text-green-900 mb-2">Taux de Résolution</h4>
                        <p class="text-sm text-green-700 mb-2">Progression du taux de résolution</p>
                        <div class="flex items-center text-xs text-green-600">
                            <span>Amélioration: +8%</span>
                            <span class="mx-2">•</span>
                            <span>Moyenne: 95%</span>
                        </div>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">Types de Fraude</h4>
                        <p class="text-sm text-blue-700 mb-2">Répartition par type de fraude détectée</p>
                        <div class="flex items-center text-xs text-blue-600">
                            <span>Licence: 45%</span>
                            <span class="mx-2">•</span>
                            <span>Document: 30%</span>
                            <span class="mx-2">•</span>
                            <span>Paiement: 25%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Reports -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rapports Détaillés</h3>
            <div class="space-y-4">
                <!-- Report 1 -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">Rapport Mensuel - Août 2024</h4>
                            <p class="text-sm text-gray-600">Rapport complet des activités de détection de fraude</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Complété</span>
                            <button class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Télécharger</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>• 89 alertes générées</div>
                        <div>• 156 cas résolus</div>
                        <div>• 98.5% de précision</div>
                    </div>
                </div>

                <!-- Report 2 -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">Analyse des Tendances - Q3 2024</h4>
                            <p class="text-sm text-gray-600">Analyse des tendances de fraude sur le trimestre</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">En cours</span>
                            <button class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">Générer</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>• Analyse en cours</div>
                        <div>• Données collectées</div>
                        <div>• Génération en cours</div>
                    </div>
                </div>

                <!-- Report 3 -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">Rapport de Performance - Système IA</h4>
                            <p class="text-sm text-gray-600">Évaluation des performances du système de détection</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Complété</span>
                            <button class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Télécharger</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>• 99.2% de disponibilité</div>
                        <div>• 2.3s temps de réponse</div>
                        <div>• 98.5% de précision</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Generation -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Génération de Rapports</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📊 Rapport Mensuel
                </button>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📈 Rapport de Tendances
                </button>
                <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🤖 Rapport IA
                </button>
                <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    ⚙️ Rapport Technique
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 