@extends('layouts.app')

@section('title', 'Accueil - FIT Platform')

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
                                <span class="text-white font-bold text-lg">FIT</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    FIT Platform
                                </h1>
                                <p class="text-sm text-gray-600">Plateforme Football Intelligence & Tracking</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Accéder aux Modules
                    </a>
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
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Bienvenue sur la plateforme FIT</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Plateforme complète de gestion du football avec modules médicaux, compétitions et analyses
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Modules disponibles
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-2xl">
                            🏥
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Modules Médicaux</h3>
                            <p class="text-sm text-gray-600 mb-4">Gestion des dossiers de santé et évaluations</p>
                            <a href="{{ route('modules.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                Accéder →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-2xl">
                            ⚽
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Compétitions</h3>
                            <p class="text-sm text-gray-600 mb-4">Gestion des ligues et tournois</p>
                            <a href="{{ route('modules.competitions.index') }}" class="text-green-600 hover:text-green-700 font-medium">
                                Accéder →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center text-2xl">
                            🏟️
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Clubs</h3>
                            <p class="text-sm text-gray-600 mb-4">Administration des clubs et équipes</p>
                            <a href="{{ route('modules.clubs.index') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                                Accéder →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">État du Système</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mx-auto mb-2"></div>
                        <p class="text-sm text-gray-600">Base de données</p>
                    </div>
                    <div class="text-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mx-auto mb-2"></div>
                        <p class="text-sm text-gray-600">Authentification</p>
                    </div>
                    <div class="text-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mx-auto mb-2"></div>
                        <p class="text-sm text-gray-600">Modules</p>
                    </div>
                    <div class="text-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mx-auto mb-2"></div>
                        <p class="text-sm text-gray-600">API</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection










