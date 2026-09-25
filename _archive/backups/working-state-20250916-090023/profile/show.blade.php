@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center space-x-6 mb-6">
            <!-- Photo de profil de l'utilisateur -->
            <div class="flex-shrink-0">
                @if(auth()->user()->hasProfilePicture())
                    <img src="{{ auth()->user()->getProfilePictureUrl() }}" 
                         alt="{{ auth()->user()->getProfilePictureAlt() }}" 
                         class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 shadow-lg">
                @else
                    <div class="w-32 h-32 rounded-full bg-blue-600 flex items-center justify-center text-white text-3xl font-semibold border-4 border-gray-200 shadow-lg">
                        {{ auth()->user()->getInitials() }}
                    </div>
                @endif
            </div>
            
            <!-- Informations de l'utilisateur -->
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ auth()->user()->name }}</h1>
                <p class="text-lg text-gray-600 mb-1">{{ auth()->user()->email }}</p>
                <p class="text-gray-500 mb-1">{{ auth()->user()->getRoleDisplay() }}</p>
                <p class="text-gray-500 mb-1">{{ auth()->user()->getEntityName() }}</p>
                @if(auth()->user()->fifa_connect_id)
                    <p class="text-gray-500">FIFA Connect ID: {{ auth()->user()->fifa_connect_id }}</p>
                @endif
            </div>
            
            <!-- Actions -->
            <div class="flex-shrink-0">
                <a href="{{ route('profile.edit') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier le profil
                </a>
            </div>
        </div>

        <!-- Statistiques de l'utilisateur -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Dernière connexion</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Jamais' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Statut</p>
                        <p class="text-lg font-semibold text-gray-900">{{ ucfirst(auth()->user()->status) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Membre depuis</p>
                        <p class="text-lg font-semibold text-gray-900">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nom complet</span>
                        <span class="font-medium">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email</span>
                        <span class="font-medium">{{ auth()->user()->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rôle</span>
                        <span class="font-medium">{{ auth()->user()->getRoleDisplay() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Entité</span>
                        <span class="font-medium">{{ auth()->user()->getEntityName() }}</span>
                    </div>
                    @if(auth()->user()->phone)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Téléphone</span>
                            <span class="font-medium">{{ auth()->user()->phone }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Préférences</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Langue</span>
                        <span class="font-medium">{{ auth()->user()->language ?? 'Français' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fuseau horaire</span>
                        <span class="font-medium">{{ auth()->user()->timezone ?? 'Europe/Paris' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Notifications email</span>
                        <span class="font-medium">{{ auth()->user()->notifications_email ? 'Activées' : 'Désactivées' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Notifications SMS</span>
                        <span class="font-medium">{{ auth()->user()->notifications_sms ? 'Activées' : 'Désactivées' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 