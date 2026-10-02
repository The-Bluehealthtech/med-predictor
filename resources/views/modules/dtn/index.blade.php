@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-page-header
            title="Digital Twin Network"
            subtitle="Simulation, modélisation et aide à la décision autour des athlètes."
            eyebrow="Performance"
            :back-href="route('modules.index')"
            back-label="Retour aux modules"
        />
        <!-- DTN Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Digital Twins</p>
                        <p class="text-2xl font-bold text-gray-900">Simuler</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Predictions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ __('Prédire') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Analytics</p>
                        <p class="text-2xl font-bold text-gray-900">Analyser</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Optimization</p>
                        <p class="text-2xl font-bold text-gray-900">Optimiser</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DTN Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Digital Twins -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔄 Digital Twins</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-blue-900">Athlete Models</p>
                            <p class="text-sm text-blue-700">{{ __('Modèles numériques des athlètes') }}</p>
                        </div>
                        <span class="text-blue-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="font-medium text-green-900">Performance Simulation</p>
                            <p class="text-sm text-green-700">{{ __('Simulation des performances') }}</p>
                        </div>
                        <span class="text-green-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div>
                            <p class="font-medium text-purple-900">Injury Prediction</p>
                            <p class="text-sm text-purple-700">{{ __('landing.injury_prediction') }}</p>
                        </div>
                        <span class="text-purple-600">→</span>
                    </div>
                </div>
            </div>

            <!-- Predictions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔮 Predictions</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg">
                        <div>
                            <p class="font-medium text-indigo-900">Performance Forecast</p>
                            <p class="text-sm text-indigo-700">{{ __('Prévision des performances') }}</p>
                        </div>
                        <span class="text-indigo-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="font-medium text-yellow-900">Recovery Timeline</p>
                            <p class="text-sm text-yellow-700">{{ __('Calendrier de récupération') }}</p>
                        </div>
                        <span class="text-yellow-600">→</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg">
                        <div>
                            <p class="font-medium text-pink-900">Training Optimization</p>
                            <p class="text-sm text-pink-700">{{ __('Optimisation de l\'entraînement') }}</p>
                        </div>
                        <span class="text-pink-600">→</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('dashboard.quick_actions') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔄 Create Twin
                </a>
                <a href="#" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔮 Predict
                </a>
                <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📊 Analytics
                </a>
                <a href="#" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    ⚙️ Optimize
                </a>
            </div>
        </div>
    </div>
</div>
@endsection






