@extends('layouts.app')

@section('title', app()->getLocale() === 'fr' ? 'Profil Utilisateur' : 'User Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            {{ app()->getLocale() === 'fr' ? 'Profil Utilisateur' : 'User Profile' }}
        </h1>
        <p class="text-gray-600">
            {{ app()->getLocale() === 'fr' 
                ? 'Gérez vos informations personnelles et vos paramètres de compte.' 
                : 'Manage your personal information and account settings.' }}
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">
                    {{ app()->getLocale() === 'fr' ? 'Informations Personnelles' : 'Personal Information' }}
                </h2>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ app()->getLocale() === 'fr' ? 'Nom Complet' : 'Full Name' }}
                            </label>
                            <p class="text-gray-900">{{ $user->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <p class="text-gray-900">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ app()->getLocale() === 'fr' ? 'Rôle' : 'Role' }}
                            </label>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $user->role }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ app()->getLocale() === 'fr' ? 'Statut' : 'Status' }}
                            </label>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $user->status }}
                            </span>
                        </div>
                    </div>

                    @if($user->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ app()->getLocale() === 'fr' ? 'Téléphone' : 'Phone' }}
                        </label>
                        <p class="text-gray-900">{{ $user->phone }}</p>
                    </div>
                    @endif

                    @if($user->fifa_connect_id)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            FIFA Connect ID
                        </label>
                        <p class="text-gray-900 font-mono text-sm">{{ $user->fifa_connect_id }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ app()->getLocale() === 'fr' ? 'Langue' : 'Language' }}
                            </label>
                            <p class="text-gray-900">{{ $user->language ?? 'fr' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ app()->getLocale() === 'fr' ? 'Fuseau Horaire' : 'Timezone' }}
                            </label>
                            <p class="text-gray-900">{{ $user->timezone ?? 'UTC' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ app()->getLocale() === 'fr' ? 'Actions Rapides' : 'Quick Actions' }}
                </h3>
                
                <div class="space-y-3">
                    <a href="{{ route('modules.index') }}" 
                       class="block w-full px-4 py-2 text-center bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        {{ app()->getLocale() === 'fr' ? 'Retour aux Modules' : 'Back to Modules' }}
                    </a>
                    
                    <a href="{{ route('dashboard') }}" 
                       class="block w-full px-4 py-2 text-center bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                        {{ app()->getLocale() === 'fr' ? 'Dashboard' : 'Dashboard' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
