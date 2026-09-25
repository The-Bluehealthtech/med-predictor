@extends('layouts.app')

@section('title', 'Appareils Connectés - Portail Athlète')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Navigation du Portail Athlète -->
    <nav class="portal-nav text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold">🏃‍♂️ Portail Athlète</h1>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="{{ route('portal.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Dashboard</a>
                            <a href="{{ route('portal.wellness') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Bien-être</a>
                            <a href="{{ route('portal.devices') }}" class="px-3 py-2 rounded-md text-sm font-medium bg-white bg-opacity-20">Appareils</a>
                            <a href="{{ route('portal.medical-record') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Dossier Médical</a>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <!-- Photo de profil de l'utilisateur -->
                        @if(auth()->user()->hasProfilePicture())
                            <img src="{{ auth()->user()->getProfilePictureUrl() }}" 
                                 alt="{{ auth()->user()->getProfilePictureAlt() }}" 
                                 class="w-8 h-8 rounded-full object-cover border-2 border-white shadow-sm">
                        @else
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-semibold border-2 border-white shadow-sm">
                                {{ auth()->user()->getInitials() }}
                            </div>
                        @endif
                        <span class="text-sm font-medium text-white">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu Principal -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Appareils Connectés</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Gérez vos appareils connectés et synchronisez vos données de santé.
                </p>
            </div>

            <!-- Appareils Connectés -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Montre Connectée -->
                <div class="portal-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 text-xl">⌚</span>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-semibold text-gray-900">Apple Watch</h3>
                                <p class="text-sm text-gray-500">Connectée</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Actif
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Dernière sync</span>
                            <span class="text-gray-900">Il y a 5 min</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Batterie</span>
                            <span class="text-gray-900">78%</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Pas aujourd'hui</span>
                            <span class="text-gray-900">8,432</span>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-2">
                        <button class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                            Synchroniser
                        </button>
                        <button class="px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50">
                            Détails
                        </button>
                    </div>
                </div>

                <!-- Podomètre -->
                <div class="portal-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600 text-xl">👟</span>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-semibold text-gray-900">Podomètre</h3>
                                <p class="text-sm text-gray-500">Connecté</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Actif
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Dernière sync</span>
                            <span class="text-gray-900">Il y a 2h</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Batterie</span>
                            <span class="text-gray-900">92%</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Distance</span>
                            <span class="text-gray-900">6.2 km</span>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-2">
                        <button class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                            Synchroniser
                        </button>
                        <button class="px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50">
                            Détails
                        </button>
                    </div>
                </div>

                <!-- Appareil de Fréquence Cardiaque -->
                <div class="portal-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <span class="text-red-600 text-xl">❤️</span>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-semibold text-gray-900">Cardio Belt</h3>
                                <p class="text-sm text-gray-500">Connecté</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Actif
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Dernière sync</span>
                            <span class="text-gray-900">Il y a 1h</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Batterie</span>
                            <span class="text-gray-900">65%</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">FC actuelle</span>
                            <span class="text-gray-900">72 bpm</span>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-2">
                        <button class="flex-1 px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                            Synchroniser
                        </button>
                        <button class="px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50">
                            Détails
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ajouter un Appareil -->
            <div class="portal-card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajouter un Appareil</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition-colors">
                        <span class="text-blue-600 text-xl mr-3">+</span>
                        <div class="text-left">
                            <p class="text-sm font-medium text-gray-900">Montre Connectée</p>
                            <p class="text-xs text-gray-500">Apple Watch, Garmin...</p>
                        </div>
                    </button>
                    <button class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-400 hover:bg-green-50 transition-colors">
                        <span class="text-green-600 text-xl mr-3">+</span>
                        <div class="text-left">
                            <p class="text-sm font-medium text-gray-900">Podomètre</p>
                            <p class="text-xs text-gray-500">Fitbit, Xiaomi...</p>
                        </div>
                    </button>
                    <button class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-red-400 hover:bg-red-50 transition-colors">
                        <span class="text-red-600 text-xl mr-3">+</span>
                        <div class="text-left">
                            <p class="text-sm font-medium text-gray-900">Cardio Belt</p>
                            <p class="text-xs text-gray-500">Polar, Wahoo...</p>
                        </div>
                    </button>
                    <button class="flex items-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-400 hover:bg-purple-50 transition-colors">
                        <span class="text-purple-600 text-xl mr-3">+</span>
                        <div class="text-left">
                            <p class="text-sm font-medium text-gray-900">Autre</p>
                            <p class="text-xs text-gray-500">Appareil personnalisé</p>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Statistiques de Synchronisation -->
            <div class="portal-card p-6 mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques de Synchronisation</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">3</div>
                        <div class="text-sm text-gray-500">Appareils connectés</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">98%</div>
                        <div class="text-sm text-gray-500">Taux de synchronisation</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">24h</div>
                        <div class="text-sm text-gray-500">Dernière synchronisation</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection 