@extends('layouts.app')

@section('title', 'Fraud Detection Settings - Association')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-gray-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">⚙️</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Fraud Detection Settings
                                </h1>
                                <p class="text-sm text-gray-600">Paramètres du système de détection de fraude</p>
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
        <!-- System Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Statut du Système</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">Actif</div>
                        <div class="text-sm text-gray-600">Système de Détection</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">99.2%</div>
                        <div class="text-sm text-gray-600">Disponibilité</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">2.3s</div>
                        <div class="text-sm text-gray-600">Temps de Réponse</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detection Settings -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Sensitivity Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Paramètres de Sensibilité</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Seuil de Risque Élevé</label>
                        <div class="flex items-center space-x-4">
                            <input type="range" min="0" max="100" value="80" class="flex-1">
                            <span class="text-sm font-medium text-gray-900">80%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Alertes générées au-dessus de ce seuil</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Seuil de Risque Moyen</label>
                        <div class="flex items-center space-x-4">
                            <input type="range" min="0" max="100" value="60" class="flex-1">
                            <span class="text-sm font-medium text-gray-900">60%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Alertes générées au-dessus de ce seuil</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Seuil de Risque Faible</label>
                        <div class="flex items-center space-x-4">
                            <input type="range" min="0" max="100" value="40" class="flex-1">
                            <span class="text-sm font-medium text-gray-900">40%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Alertes générées au-dessus de ce seuil</p>
                    </div>
                </div>
            </div>

            <!-- AI Model Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Paramètres du Modèle IA</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-blue-900">Modèle de Licence</p>
                            <p class="text-sm text-blue-700">Détection des fraudes de licence</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="font-medium text-green-900">Modèle de Document</p>
                            <p class="text-sm text-green-700">Détection des documents falsifiés</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="font-medium text-yellow-900">Modèle de Paiement</p>
                            <p class="text-sm text-yellow-700">Détection des anomalies de paiement</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div>
                            <p class="font-medium text-purple-900">Modèle d'Inscription</p>
                            <p class="text-sm text-purple-700">Détection des inscriptions multiples</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Paramètres de Notification</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Alertes par Email</h4>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Alertes de risque élevé</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Rapports quotidiens</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Alertes de risque moyen</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Alertes de risque faible</span>
                        </label>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Notifications Push</h4>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Alertes urgentes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Résumé hebdomadaire</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Mises à jour système</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Maintenance planifiée</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Configuration -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Configuration Système</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Intervalle de Scan</label>
                    <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="5">5 minutes</option>
                        <option value="15" selected>15 minutes</option>
                        <option value="30">30 minutes</option>
                        <option value="60">1 heure</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rétention des Données</label>
                    <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="30">30 jours</option>
                        <option value="90" selected>90 jours</option>
                        <option value="180">180 jours</option>
                        <option value="365">1 an</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode de Détection</label>
                    <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="realtime" selected>Temps réel</option>
                        <option value="batch">Par lot</option>
                        <option value="scheduled">Planifié</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Niveau de Log</label>
                    <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="error">Erreur uniquement</option>
                        <option value="warning">Avertissement</option>
                        <option value="info" selected>Information</option>
                        <option value="debug">Debug</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    💾 Sauvegarder
                </button>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔄 Redémarrer
                </button>
                <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔧 Maintenance
                </button>
                <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    ⚠️ Urgence
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 