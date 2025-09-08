@extends('layouts.app')

@section('title', 'Fraud Detection Analysis - Association')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-yellow-600 to-orange-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">🔍</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Fraud Detection Analysis
                                </h1>
                                <p class="text-sm text-gray-600">Analyse approfondie des cas de fraude</p>
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
        <!-- Analysis Overview -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Vue d'Ensemble de l'Analyse</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">89</div>
                        <div class="text-sm text-gray-600">Analyses en Cours</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">156</div>
                        <div class="text-sm text-gray-600">Cas Résolus</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">98.5%</div>
                        <div class="text-sm text-gray-600">Taux de Précision</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis Tools -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Pattern Analysis -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Analyse des Modèles</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">Modèle de Licence</h4>
                        <p class="text-sm text-blue-700 mb-2">Détection de patterns suspects dans les demandes de licence</p>
                        <div class="flex items-center text-xs text-blue-600">
                            <span>Confiance: 95%</span>
                            <span class="mx-2">•</span>
                            <span>Cas détectés: 23</span>
                        </div>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <h4 class="font-medium text-green-900 mb-2">Modèle de Document</h4>
                        <p class="text-sm text-green-700 mb-2">Analyse des documents pour détecter les falsifications</p>
                        <div class="flex items-center text-xs text-green-600">
                            <span>Confiance: 92%</span>
                            <span class="mx-2">•</span>
                            <span>Cas détectés: 18</span>
                        </div>
                    </div>
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <h4 class="font-medium text-yellow-900 mb-2">Modèle de Paiement</h4>
                        <p class="text-sm text-yellow-700 mb-2">Détection d'anomalies dans les transactions</p>
                        <div class="flex items-center text-xs text-yellow-600">
                            <span>Confiance: 88%</span>
                            <span class="mx-2">•</span>
                            <span>Cas détectés: 15</span>
                        </div>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <h4 class="font-medium text-purple-900 mb-2">Modèle d'Inscription</h4>
                        <p class="text-sm text-purple-700 mb-2">Détection d'inscriptions multiples suspectes</p>
                        <div class="flex items-center text-xs text-purple-600">
                            <span>Confiance: 94%</span>
                            <span class="mx-2">•</span>
                            <span>Cas détectés: 12</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risk Assessment -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Évaluation des Risques</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                        <div>
                            <p class="font-medium text-red-900">Risque Élevé</p>
                            <p class="text-sm text-red-700">12 cas détectés</p>
                        </div>
                        <span class="text-red-600 font-bold">12</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="font-medium text-yellow-900">Risque Moyen</p>
                            <p class="text-sm text-yellow-700">34 cas détectés</p>
                        </div>
                        <span class="text-yellow-600 font-bold">34</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-blue-900">Risque Faible</p>
                            <p class="text-sm text-blue-700">43 cas détectés</p>
                        </div>
                        <span class="text-blue-600 font-bold">43</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="font-medium text-green-900">Cas Résolus</p>
                            <p class="text-sm text-green-700">156 cas résolus</p>
                        </div>
                        <span class="text-green-600 font-bold">156</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analysis -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Analyse Détaillée</h3>
            <div class="space-y-6">
                <!-- Case Study 1 -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">Cas #FD-2024-001</h4>
                            <p class="text-sm text-gray-600">Suspicious License Activity</p>
                        </div>
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Risque Élevé</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Détails de l'Analyse</h5>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• 5 demandes de licence depuis la même IP</li>
                                <li>• Intervalle de temps: 2 heures</li>
                                <li>• Données similaires dans les formulaires</li>
                                <li>• Pattern de fraude détecté: 95% de confiance</li>
                            </ul>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Actions Recommandées</h5>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Bloquer l'IP temporairement</li>
                                <li>• Vérifier les documents fournis</li>
                                <li>• Contacter les autorités si nécessaire</li>
                                <li>• Surveiller les futures activités</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Case Study 2 -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">Cas #FD-2024-002</h4>
                            <p class="text-sm text-gray-600">Document Format Mismatch</p>
                        </div>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Risque Moyen</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Détails de l'Analyse</h5>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Format de fichier inattendu</li>
                                <li>• Métadonnées suspectes</li>
                                <li>• Taille de fichier anormale</li>
                                <li>• Pattern de fraude détecté: 88% de confiance</li>
                            </ul>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900 mb-2">Actions Recommandées</h5>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Demander un nouveau document</li>
                                <li>• Vérifier l'authenticité</li>
                                <li>• Contacter l'émetteur</li>
                                <li>• Documenter le cas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis Tools -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Outils d'Analyse</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔍 Analyse Profonde
                </button>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📊 Rapport Détaillé
                </button>
                <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🎯 Investigation
                </button>
                <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    ⚙️ Paramètres IA
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 