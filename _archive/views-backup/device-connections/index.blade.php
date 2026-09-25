@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Connexions d'Appareils</h2>
                        <p class="text-gray-600 mt-1">Gérez vos connexions aux appareils de tracking sportif</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('healthcare.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Retour à la Santé
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Connection Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Catapult Connect -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Catapult Connect</h3>
                            <p class="text-sm text-gray-500">GPS et biométrie avancés</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Statut</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Connecté
                            </span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Dernière sync</span>
                            <span class="text-sm text-gray-500">Il y a 2 heures</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Données</span>
                            <span class="text-sm text-gray-500">1.2 GB</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('catapult-connect.index') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded text-sm font-medium">
                            Gérer
                        </a>
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded text-sm font-medium">
                            Sync
                        </button>
                    </div>
                </div>
            </div>

            <!-- Apple HealthKit -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Apple HealthKit</h3>
                            <p class="text-sm text-gray-500">Données de santé Apple Watch</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Statut</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Connecté
                            </span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Dernière sync</span>
                            <span class="text-sm text-gray-500">Il y a 30 min</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Données</span>
                            <span class="text-sm text-gray-500">856 MB</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('apple-health-kit.index') }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded text-sm font-medium">
                            Gérer
                        </a>
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded text-sm font-medium">
                            Sync
                        </button>
                    </div>
                </div>
            </div>

            <!-- Garmin Connect -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Garmin Connect</h3>
                            <p class="text-sm text-gray-500">Montres et trackers Garmin</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Statut</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Déconnecté
                            </span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Dernière sync</span>
                            <span class="text-sm text-gray-500">Il y a 2 jours</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Données</span>
                            <span class="text-sm text-gray-500">2.1 GB</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('garmin-connect.index') }}" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white text-center py-2 px-4 rounded text-sm font-medium">
                            Connecter
                        </a>
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded text-sm font-medium" disabled>
                            Sync
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques de Synchronisation</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">4.2 GB</div>
                        <div class="text-sm text-gray-500">Données totales</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">2/3</div>
                        <div class="text-sm text-gray-500">Appareils connectés</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">156</div>
                        <div class="text-sm text-gray-500">Sessions sync</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">98%</div>
                        <div class="text-sm text-gray-500">Taux de réussite</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 