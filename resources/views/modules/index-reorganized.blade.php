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
                            <img src="{{ asset('images/fit-logo.png') }}" alt="FIT Logo" class="w-10 h-10 mr-3">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Modules FIT
                                </h1>
                                <p class="text-sm text-gray-600">Football Intelligence & Tracking</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Dashboard Général</a>
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
                            {{ count($modules) }} catégories disponibles
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modules by Category -->
        @foreach($modules as $categoryKey => $category)
        <div class="mb-8">
            <!-- Category Header -->
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-2xl mr-4
                    @if($category['color'] === 'red') bg-red-100 text-red-600
                    @elseif($category['color'] === 'green') bg-green-100 text-green-600
                    @elseif($category['color'] === 'blue') bg-blue-100 text-blue-600
                    @elseif($category['color'] === 'purple') bg-purple-100 text-purple-600
                    @elseif($category['color'] === 'yellow') bg-yellow-100 text-yellow-600
                    @elseif($category['color'] === 'indigo') bg-indigo-100 text-indigo-600
                    @else bg-gray-100 text-gray-600
                    @endif">
                    {{ $category['icon'] }}
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $category['title'] }}</h3>
                    <p class="text-sm text-gray-600">{{ $category['description'] }}</p>
                </div>
            </div>

            <!-- Category Items Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($category['items'] as $item)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 group">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg
                                    @if($category['color'] === 'red') bg-red-50 text-red-600
                                    @elseif($category['color'] === 'green') bg-green-50 text-green-600
                                    @elseif($category['color'] === 'blue') bg-blue-50 text-blue-600
                                    @elseif($category['color'] === 'purple') bg-purple-50 text-purple-600
                                    @elseif($category['color'] === 'yellow') bg-yellow-50 text-yellow-600
                                    @elseif($category['color'] === 'indigo') bg-indigo-50 text-indigo-600
                                    @else bg-gray-50 text-gray-600
                                    @endif">
                                    {{ $item['icon'] }}
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $item['name'] }}</h4>
                                    @if($item['status'] === 'beta')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Beta
                                    </span>
                                    @elseif($item['status'] === 'active')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Actif
                                    </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 mt-1">{{ $item['description'] }}</p>
                                <div class="mt-3">
                                    <a href="{{ route($item['route']) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white
                                              @if($category['color'] === 'red') bg-red-600 hover:bg-red-700
                                              @elseif($category['color'] === 'green') bg-green-600 hover:bg-green-700
                                              @elseif($category['color'] === 'blue') bg-blue-600 hover:bg-blue-700
                                              @elseif($category['color'] === 'purple') bg-purple-600 hover:bg-purple-700
                                              @elseif($category['color'] === 'yellow') bg-yellow-600 hover:bg-yellow-700
                                              @elseif($category['color'] === 'indigo') bg-indigo-600 hover:bg-indigo-700
                                              @else bg-gray-600 hover:bg-gray-700
                                              @endif transition-colors duration-200 group-hover:scale-105">
                                        Accéder
                                        <svg class="ml-1 -mr-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Quick Stats -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-red-600">🏥</span>
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
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600">🏆</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Compétitions</p>
                        <p class="text-2xl font-bold text-gray-900">89</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600">👥</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Utilisateurs actifs</p>
                        <p class="text-2xl font-bold text-gray-900">156</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Portal Access Section -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Accès Rapide aux Portails</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('player.portal') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-blue-600">👤</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Portail Joueur</h4>
                                <p class="text-xs text-gray-600">Accès direct pour les joueurs</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('club.portal') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-green-600">🏟️</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Portail Club</h4>
                                <p class="text-xs text-gray-600">Gestion des clubs</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('association.portal') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-purple-600">🏛️</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Portail Association</h4>
                                <p class="text-xs text-gray-600">Gestion des associations</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}
</style>
@endsection



