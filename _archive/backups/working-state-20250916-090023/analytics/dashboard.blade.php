@extends('layouts.app')

@section('title', 'Analytics Dashboard - FIT Platform')

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
                                    Analytics Dashboard
                                </h1>
                                <p class="text-sm text-gray-600">Analyse avancée et insights</p>
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
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">📊 Analytics Dashboard</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Analyse avancée, tendances et alertes de performance
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

        <!-- Analytics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">📊 Analytics</p>
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
                        <p class="text-sm font-medium text-gray-600">📈 Trends</p>
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
                        <p class="text-sm font-medium text-gray-600">⚠️ Performance Alerts</p>
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
                        <p class="text-sm font-medium text-gray-600">💡 Recommendations</p>
                        <p class="text-2xl font-bold text-gray-900">Recommandations</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('analytics.digital-twin') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">🔄 Digital Twin Network</p>
                        <p class="text-2xl font-bold text-gray-900">Simulation</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Analytics Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Analytics Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Analytics</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-blue-900">Performance Metrics</p>
                            <p class="text-sm text-blue-700">Analyse des performances des athlètes</p>
                        </div>
                        <span class="text-blue-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="font-medium text-green-900">Health Analytics</p>
                            <p class="text-sm text-green-700">Suivi de la santé et bien-être</p>
                        </div>
                        <span class="text-green-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div>
                            <p class="font-medium text-purple-900">Training Analytics</p>
                            <p class="text-sm text-purple-700">Analyse des sessions d'entraînement</p>
                        </div>
                        <span class="text-purple-600">→</span>
                    </div>
                </div>
            </div>

            <!-- Trends Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Trends</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg">
                        <div>
                            <p class="font-medium text-indigo-900">Performance Trends</p>
                            <p class="text-sm text-indigo-700">Évolution des performances</p>
                        </div>
                        <span class="text-indigo-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="font-medium text-yellow-900">Injury Trends</p>
                            <p class="text-sm text-yellow-700">Analyse des blessures</p>
                        </div>
                        <span class="text-yellow-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg">
                        <div>
                            <p class="font-medium text-pink-900">Recovery Trends</p>
                            <p class="text-sm text-pink-700">Temps de récupération</p>
                        </div>
                        <span class="text-pink-600">→</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Alerts -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Performance Alerts</h3>
            <div class="space-y-4">
                <div class="flex items-center p-4 bg-red-50 border-l-4 border-red-400 rounded-lg">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">High Risk Player Detected</p>
                        <p class="text-sm text-red-700">Player ID: 123 - Risk score: 85%</p>
                    </div>
                </div>
                <div class="flex items-center p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800">Performance Decline</p>
                        <p class="text-sm text-yellow-700">Player ID: 456 - Performance dropped 15%</p>
                    </div>
                </div>
                <div class="flex items-center p-4 bg-blue-50 border-l-4 border-blue-400 rounded-lg">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">Recovery Milestone</p>
                        <p class="text-sm text-blue-700">Player ID: 789 - 80% recovered from injury</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Recommendations</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Training Optimization</h4>
                    <p class="text-sm text-gray-600 mb-3">Based on performance data, recommend reducing training intensity for Player ID: 123</p>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            High Priority
                        </span>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Recovery Protocol</h4>
                    <p class="text-sm text-gray-600 mb-3">Implement enhanced recovery protocol for Player ID: 456</p>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Medium Priority
                        </span>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Nutrition Plan</h4>
                    <p class="text-sm text-gray-600 mb-3">Adjust nutrition plan for Player ID: 789 based on performance metrics</p>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Low Priority
                        </span>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Medical Check</h4>
                    <p class="text-sm text-gray-600 mb-3">Schedule medical evaluation for Player ID: 123 due to risk factors</p>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Critical
                        </span>
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