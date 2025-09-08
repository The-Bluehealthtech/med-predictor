@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Garmin Connect</h2>
                            <p class="text-gray-600 mt-1">Connexion aux montres et trackers Garmin</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('device-connections.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connection Status -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Connection Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statut de Connexion</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Statut</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Déconnecté
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Token expiré</span>
                            <span class="text-sm text-gray-500">Il y a 2 jours</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Dernière sync</span>
                            <span class="text-sm text-gray-500">Il y a 2 jours</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Appareils</span>
                            <span class="text-sm text-gray-500">0 connectés</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-2">
                        <button class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded text-sm font-medium">
                            Se connecter
                        </button>
                        <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded text-sm font-medium" disabled>
                            Rafraîchir Token
                        </button>
                    </div>
                </div>
            </div>

            <!-- Devices Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Appareils Disponibles</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-sm">Garmin Fenix 7</div>
                                <div class="text-xs text-gray-500">Montre multisport</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Non connecté
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-sm">Garmin Forerunner 955</div>
                                <div class="text-xs text-gray-500">Montre running</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Non connecté
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-sm">Garmin Vivoactive 4</div>
                                <div class="text-xs text-gray-500">Montre fitness</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Non connecté
                            </span>
                        </div>
                    </div>
                    
                    <button class="w-full mt-4 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded text-sm font-medium">
                        Découvrir des appareils
                    </button>
                </div>
            </div>

            <!-- Connection Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de Connexion</h3>
                    
                    <div class="space-y-4">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Connexion requise
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Vous devez vous connecter à Garmin Connect pour accéder aux données de vos appareils Garmin.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-600">Synchronisation automatique</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-600">Données GPS précises</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-600">Métriques avancées</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Setup Instructions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Instructions de Configuration</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Étape 1 : Créer un compte Garmin</h4>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
                            <li>Allez sur <a href="https://connect.garmin.com" class="text-orange-600 hover:text-orange-800">connect.garmin.com</a></li>
                            <li>Cliquez sur "Créer un compte"</li>
                            <li>Remplissez vos informations personnelles</li>
                            <li>Vérifiez votre email</li>
                        </ol>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Étape 2 : Connecter votre appareil</h4>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
                            <li>Téléchargez l'app Garmin Connect</li>
                            <li>Connectez votre appareil Garmin</li>
                            <li>Synchronisez vos données</li>
                            <li>Autorisez l'accès aux données</li>
                        </ol>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Étape 3 : Autoriser l'intégration</h4>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
                            <li>Cliquez sur "Se connecter" ci-dessus</li>
                            <li>Connectez-vous à votre compte Garmin</li>
                            <li>Autorisez l'accès aux données</li>
                            <li>Confirmez la connexion</li>
                        </ol>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Étape 4 : Synchronisation</h4>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600">
                            <li>Vos données seront synchronisées automatiquement</li>
                            <li>Les nouvelles activités apparaîtront ici</li>
                            <li>Vous pouvez forcer une synchronisation manuelle</li>
                            <li>Surveillez les métriques en temps réel</li>
                        </ol>
                    </div>
                </div>
                
                <div class="mt-6 text-center">
                    <button class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-lg text-lg">
                        Commencer la Configuration
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 