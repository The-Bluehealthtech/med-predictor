@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
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
                                    Performance Analytics
                                </h1>
                                <p class="text-sm text-gray-600">Analyse des performances des athlètes</p>
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
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">📊 Performance Analytics</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Analyse avancée des performances des athlètes
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                            Données en temps réel
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Performance Metrics</p>
                        <p class="text-2xl font-bold text-gray-900">Analyser</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Trends</p>
                        <p class="text-2xl font-bold text-gray-900">Tendances</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Alerts</p>
                        <p class="text-2xl font-bold text-gray-900">Alertes</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Recommendations</p>
                        <p class="text-2xl font-bold text-gray-900">Recommandations</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Performance Metrics -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Performance Metrics</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-blue-900">Speed Analysis</p>
                            <p class="text-sm text-blue-700">Analyse de la vitesse des athlètes</p>
                        </div>
                        <span class="text-blue-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="font-medium text-green-900">Endurance Metrics</p>
                            <p class="text-sm text-green-700">Métriques d'endurance</p>
                        </div>
                        <span class="text-green-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div>
                            <p class="font-medium text-purple-900">Strength Analysis</p>
                            <p class="text-sm text-purple-700">Analyse de la force</p>
                        </div>
                        <span class="text-purple-600">→</span>
                    </div>
                </div>
            </div>

            <!-- Performance Trends -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Performance Trends</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg">
                        <div>
                            <p class="font-medium text-indigo-900">Improvement Trends</p>
                            <p class="text-sm text-indigo-700">Tendances d'amélioration</p>
                        </div>
                        <span class="text-indigo-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="font-medium text-yellow-900">Decline Alerts</p>
                            <p class="text-sm text-yellow-700">Alertes de déclin</p>
                        </div>
                        <span class="text-yellow-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg">
                        <div>
                            <p class="font-medium text-pink-900">Recovery Patterns</p>
                            <p class="text-sm text-pink-700">Modèles de récupération</p>
                        </div>
                        <span class="text-pink-600">→</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📊 Analytics
                </a>
                <a href="#" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📈 Trends
                </a>
                <a href="#" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    ⚠️ Alerts
                </a>
                <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    💡 Recommendations
                </a>
            </div>
        </div>
    </div>
</div>
@endsection






