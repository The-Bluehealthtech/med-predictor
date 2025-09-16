@extends('layouts.app')

@section('title', 'Fraud Detection Alerts - Association')

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
                                <span class="text-white font-bold text-lg">🚨</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Fraud Detection Alerts
                                </h1>
                                <p class="text-sm text-gray-600">Alertes de détection de fraude</p>
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
        <!-- Alert Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtres d'Alertes</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Niveau de Risque</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Tous les niveaux</option>
                            <option value="high">Risque Élevé</option>
                            <option value="medium">Risque Moyen</option>
                            <option value="low">Risque Faible</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type d'Alerte</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Tous les types</option>
                            <option value="license">Licence</option>
                            <option value="document">Document</option>
                            <option value="payment">Paiement</option>
                            <option value="registration">Inscription</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Tous les statuts</option>
                            <option value="active">Actif</option>
                            <option value="resolved">Résolu</option>
                            <option value="investigating">En cours</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Alerts -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Alertes Actives</h3>
            <div class="space-y-4">
                <!-- High Risk Alert -->
                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-red-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="font-semibold text-red-900">Suspicious License Activity</h4>
                                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Risque Élevé</span>
                                </div>
                                <p class="text-sm text-red-700 mb-2">Multiple license applications detected from the same IP address within a short time period.</p>
                                <div class="flex items-center text-xs text-red-600">
                                    <span>ID: #FD-2024-001</span>
                                    <span class="mx-2">•</span>
                                    <span>Détecté: 2 heures ago</span>
                                    <span class="mx-2">•</span>
                                    <span>IP: 192.168.1.100</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Investigate</button>
                            <button class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">Dismiss</button>
                        </div>
                    </div>
                </div>

                <!-- Medium Risk Alert -->
                <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="font-semibold text-yellow-900">Unusual Document Upload</h4>
                                    <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Risque Moyen</span>
                                </div>
                                <p class="text-sm text-yellow-700 mb-2">Document format mismatch detected. File type doesn't match expected format.</p>
                                <div class="flex items-center text-xs text-yellow-600">
                                    <span>ID: #FD-2024-002</span>
                                    <span class="mx-2">•</span>
                                    <span>Détecté: 4 heures ago</span>
                                    <span class="mx-2">•</span>
                                    <span>Fichier: medical_cert.pdf</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">Review</button>
                            <button class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">Dismiss</button>
                        </div>
                    </div>
                </div>

                <!-- Medium Risk Alert -->
                <div class="border border-orange-200 rounded-lg p-4 bg-orange-50">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-orange-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="font-semibold text-orange-900">Duplicate Registration</h4>
                                    <span class="ml-2 px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">Risque Moyen</span>
                                </div>
                                <p class="text-sm text-orange-700 mb-2">Similar player data detected across multiple registrations.</p>
                                <div class="flex items-center text-xs text-orange-600">
                                    <span>ID: #FD-2024-003</span>
                                    <span class="mx-2">•</span>
                                    <span>Détecté: 6 heures ago</span>
                                    <span class="mx-2">•</span>
                                    <span>Joueur: Ahmed Ben Ali</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 bg-orange-600 text-white text-xs rounded hover:bg-orange-700">Review</button>
                            <button class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">Dismiss</button>
                        </div>
                    </div>
                </div>

                <!-- Low Risk Alert -->
                <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="font-semibold text-blue-900">Payment Anomaly</h4>
                                    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Risque Faible</span>
                                </div>
                                <p class="text-sm text-blue-700 mb-2">Unusual payment pattern detected for license renewal.</p>
                                <div class="flex items-center text-xs text-blue-600">
                                    <span>ID: #FD-2024-004</span>
                                    <span class="mx-2">•</span>
                                    <span>Détecté: 8 heures ago</span>
                                    <span class="mx-2">•</span>
                                    <span>Montant: 150€</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Review</button>
                            <button class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">Dismiss</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Statistics -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques des Alertes</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">4</div>
                    <div class="text-sm text-gray-600">Alertes Actives</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">2</div>
                    <div class="text-sm text-gray-600">En Investigation</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">156</div>
                    <div class="text-sm text-gray-600">Résolues</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">98.5%</div>
                    <div class="text-sm text-gray-600">Précision</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 