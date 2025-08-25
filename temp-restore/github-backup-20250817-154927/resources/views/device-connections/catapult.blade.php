@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Catapult Connect</h2>
                            <p class="text-gray-600 mt-1">Connexion aux appareils Catapult Vector</p>
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Connecté
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Token expiré</span>
                            <span class="text-sm text-gray-500">Dans 45 minutes</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Dernière sync</span>
                            <span class="text-sm text-gray-500">Il y a 2 heures</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Appareils</span>
                            <span class="text-sm text-gray-500">3 connectés</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-2">
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded text-sm font-medium">
                            Rafraîchir Token
                        </button>
                        <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded text-sm font-medium">
                            Déconnecter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Devices Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Appareils Connectés</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-sm">Catapult Vector S7</div>
                                <div class="text-xs text-gray-500">ID: VEC-001</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Actif
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-sm">Catapult Vector S7</div>
                                <div class="text-xs text-gray-500">ID: VEC-002</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Actif
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-sm">Catapult Vector S7</div>
                                <div class="text-xs text-gray-500">ID: VEC-003</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                En charge
                            </span>
                        </div>
                    </div>
                    
                    <button class="w-full mt-4 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded text-sm font-medium">
                        Ajouter un appareil
                    </button>
                </div>
            </div>

            <!-- Data Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques des Données</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Données totales</span>
                                <span class="font-medium">1.2 GB</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Sessions aujourd'hui</span>
                                <span class="font-medium">12</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 60%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Distance totale</span>
                                <span class="font-medium">45.2 km</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Temps d'activité</span>
                                <span class="font-medium">8h 32m</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-orange-600 h-2 rounded-full" style="width: 70%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Activité Récente</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appareil</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">VEC-001</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Entraînement</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1h 23m</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">8.5 km</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Terminé
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">VEC-002</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Match</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1h 45m</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">11.2 km</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Terminé
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">VEC-003</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Récupération</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45m</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3.2 km</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        En cours
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 