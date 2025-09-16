@extends('layouts.app')

@section('title', 'Dossier Médical - Portail Athlète')

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
                            <a href="{{ route('portal.devices') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Appareils</a>
                            <a href="{{ route('portal.medical-record') }}" class="px-3 py-2 rounded-md text-sm font-medium bg-white bg-opacity-20">Dossier Médical</a>
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
                <h2 class="text-2xl font-bold text-gray-900">Dossier Médical</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Consultez votre historique médical et vos données de santé.
                </p>
            </div>

            <!-- Informations Générales -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Profil Médical -->
                <div class="portal-card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profil Médical</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Nom</span>
                            <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">FIFA Connect ID</span>
                            <span class="text-sm font-medium text-gray-900">{{ auth()->user()->athlete->fifa_connect_id ?? 'Non défini' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Groupe sanguin</span>
                            <span class="text-sm font-medium text-gray-900">O+</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Allergies</span>
                            <span class="text-sm font-medium text-gray-900">Aucune</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Médicaments</span>
                            <span class="text-sm font-medium text-gray-900">Aucun</span>
                        </div>
                    </div>
                </div>

                <!-- Dernières Mesures -->
                <div class="portal-card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Dernières Mesures</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Tension artérielle</span>
                            <span class="text-sm font-medium text-gray-900">120/80 mmHg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Fréquence cardiaque</span>
                            <span class="text-sm font-medium text-gray-900">72 bpm</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Température</span>
                            <span class="text-sm font-medium text-gray-900">36.8°C</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Poids</span>
                            <span class="text-sm font-medium text-gray-900">75 kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Taille</span>
                            <span class="text-sm font-medium text-gray-900">180 cm</span>
                        </div>
                    </div>
                </div>

                <!-- Statut de Vaccination -->
                <div class="portal-card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut de Vaccination</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">COVID-19</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                À jour
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Tétanos</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                À jour
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Hépatite B</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                À jour
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Grippe</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Expire bientôt
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique Médical -->
            <div class="portal-card p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique Médical</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-blue-600">🏥</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Examen médical annuel</p>
                                <p class="text-xs text-gray-500">15/07/2024 - Dr. Martin</p>
                                <p class="text-xs text-gray-600">Résultats normaux, apte à la compétition</p>
                            </div>
                        </div>
                        <span class="text-green-600 text-sm">✅ Terminé</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-green-600">💉</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Vaccination grippe</p>
                                <p class="text-xs text-gray-500">10/10/2023 - Centre médical</p>
                                <p class="text-xs text-gray-600">Vaccin grippe saisonnière</p>
                            </div>
                        </div>
                        <span class="text-green-600 text-sm">✅ Terminé</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-yellow-600">🦴</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Entorse cheville</p>
                                <p class="text-xs text-gray-500">20/03/2024 - Dr. Dubois</p>
                                <p class="text-xs text-gray-600">Entorse légère, repos 2 semaines</p>
                            </div>
                        </div>
                        <span class="text-green-600 text-sm">✅ Guéri</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-purple-600">📋</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">PCMA Cardio</p>
                                <p class="text-xs text-gray-500">05/01/2024 - Dr. Laurent</p>
                                <p class="text-xs text-gray-600">Évaluation cardiovasculaire complète</p>
                            </div>
                        </div>
                        <span class="text-green-600 text-sm">✅ Terminé</span>
                    </div>
                </div>
            </div>

            <!-- Prochains Rendez-vous -->
            <div class="portal-card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Prochains Rendez-vous</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-blue-600">📅</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Examen médical pré-saison</p>
                                <p class="text-xs text-gray-500">15/08/2024 - 14:00 - Dr. Martin</p>
                                <p class="text-xs text-gray-600">Centre médical principal</p>
                            </div>
                        </div>
                        <span class="text-blue-600 text-sm">⏰ Prochain</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-green-600">💉</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Rappel vaccination grippe</p>
                                <p class="text-xs text-gray-500">20/10/2024 - 10:30 - Centre médical</p>
                                <p class="text-xs text-gray-600">Vaccination saisonnière</p>
                            </div>
                        </div>
                        <span class="text-gray-600 text-sm">📅 Planifié</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection 