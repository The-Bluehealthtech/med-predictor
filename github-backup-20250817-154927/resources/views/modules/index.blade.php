@extends('layouts.app')

@section('title', 'Modules - FIT Platform')

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
                                    Modules FIT
                                </h1>
                                <p class="text-sm text-gray-600">Plateforme Football Intelligence & Tracking</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Dashboard Admin</a>
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
                        Sélectionnez un module pour accéder aux fonctionnalités de gestion du football
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            {{ count($modules) }} modules disponibles
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($modules as $module)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl
                                @if($module['color'] === 'blue') bg-blue-100 text-blue-600
                                @elseif($module['color'] === 'green') bg-green-100 text-green-600
                                @elseif($module['color'] === 'purple') bg-purple-100 text-purple-600
                                @elseif($module['color'] === 'yellow') bg-yellow-100 text-yellow-600
                                @elseif($module['color'] === 'red') bg-red-100 text-red-600
                                @elseif($module['color'] === 'indigo') bg-indigo-100 text-indigo-600
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ $module['icon'] }}
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $module['name'] }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ $module['description'] }}</p>
                            <a href="{{ route($module['route']) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white
                                      @if($module['color'] === 'blue') bg-blue-600 hover:bg-blue-700
                                      @elseif($module['color'] === 'green') bg-green-600 hover:bg-green-700
                                      @elseif($module['color'] === 'purple') bg-purple-600 hover:bg-purple-700
                                      @elseif($module['color'] === 'yellow') bg-yellow-600 hover:bg-yellow-700
                                      @elseif($module['color'] === 'red') bg-red-600 hover:bg-red-700
                                      @elseif($module['color'] === 'indigo') bg-indigo-600 hover:bg-indigo-700
                                      @else bg-gray-600 hover:bg-gray-700
                                      @endif transition-colors duration-200">
                                Accéder au module
                                <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Portal Access Section -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Accès aux Portails</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Referee Portal -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl bg-orange-100 text-orange-600">
                                    🏟️
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Portail Arbitre</h3>
                                <p class="text-sm text-gray-600 mb-4">Gestion des matchs, rapports et assignations d'arbitrage</p>
                                <a href="{{ route('referee.dashboard') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-colors duration-200">
                                    Accéder au portail
                                    <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Player Portal -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl bg-green-100 text-green-600">
                                    🏃‍♂️
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Portail Athlète</h3>
                                <p class="text-sm text-gray-600 mb-4">Accès mobile aux dossiers médicaux et formulaires de bien-être</p>
                                <a href="{{ route('portal.dashboard') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors duration-200">
                                    Accéder au portail
                                    <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secretary Portal -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl bg-purple-100 text-purple-600">
                                    🏥
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Secrétariat Médical</h3>
                                <p class="text-sm text-gray-600 mb-4">Gestion des rendez-vous et documents médicaux</p>
                                <a href="{{ route('secretary.dashboard') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-colors duration-200">
                                    Accéder au portail
                                    <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Portail Joueur -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl bg-blue-100 text-blue-600">
                                    👤
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Portail Joueur</h3>
                                <p class="text-sm text-gray-600 mb-4">Accès aux portails individuels des joueurs et gestion des profils</p>
                                <a href="{{ route('players.list') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                    Accéder au portail
                                    <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600">🏥</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Dossiers médicaux</p>
                        <p class="text-2xl font-bold text-gray-900">1,234</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600">📋</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Licences actives</p>
                        <p class="text-2xl font-bold text-gray-900">567</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600">🏆</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Compétitions</p>
                        <p class="text-2xl font-bold text-gray-900">89</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <span class="text-yellow-600">👥</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Athlètes</p>
                        <p class="text-2xl font-bold text-gray-900">2,456</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Activité Récente</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Nouveau dossier médical créé</p>
                            <p class="text-xs text-gray-600">Il y a 2 heures • Athlète #1234</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">PCMA complété</p>
                            <p class="text-xs text-gray-600">Il y a 4 heures • Évaluation cardiovasculaire</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Licence renouvelée</p>
                            <p class="text-xs text-gray-600">Il y a 6 heures • Joueur professionnel</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Nouvelle compétition ajoutée</p>
                            <p class="text-xs text-gray-600">Il y a 1 jour • Championnat régional</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 