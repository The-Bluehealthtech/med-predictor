@extends('layouts.app')

@section('title', 'FIFA Dashboard - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-green-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">⚽</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    FIFA Dashboard
                                </h1>
                                <p class="text-sm text-gray-600">Connectivité FIFA et gestion des contrats</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/modules" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Modules</a>
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
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">⚽ FIFA Dashboard</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Connectivité FIFA, synchronisation et gestion des contrats
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 {{ $connectivity['connected'] ? 'bg-green-500' : 'bg-red-500' }} rounded-full mr-2"></span>
                            {{ $connectivity['connected'] ? 'Système opérationnel' : 'Système hors ligne' }}
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 {{ $connectivity['connected'] ? 'bg-blue-500' : 'bg-gray-500' }} rounded-full mr-2"></span>
                            {{ $connectivity['connected'] ? 'FIFA Connecté' : 'FIFA Non connecté' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FIFA Connection Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Connectivité</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $connectivity['connected'] ? 'Connecté' : 'Déconnecté' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Synchronisation</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $fifaStats['confederations']['synced'] }}/{{ $fifaStats['confederations']['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Contrats</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $fifaStats['players']['total'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FIFA Connection Status -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Connection Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut de connexion FIFA</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 {{ $connectivity['connected'] ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 {{ $connectivity['connected'] ? 'bg-green-500' : 'bg-red-500' }} rounded-full mr-3"></div>
                            <div>
                                <p class="font-medium {{ $connectivity['connected'] ? 'text-green-900' : 'text-red-900' }}">FIFA Connect API</p>
                                <p class="text-sm {{ $connectivity['connected'] ? 'text-green-700' : 'text-red-700' }}">{{ $connectivity['message'] }}</p>
                            </div>
                        </div>
                        <span class="{{ $connectivity['connected'] ? 'text-green-600' : 'text-red-600' }}">{{ $connectivity['connected'] ? 'En ligne' : 'Hors ligne' }}</span>
                    </div>
                    
                    @foreach($confederations as $confederation)
                    <div class="flex items-center justify-between p-4 {{ $confederation->fifa_sync_status === 'synced' ? 'bg-green-50' : ($confederation->fifa_sync_status === 'failed' ? 'bg-red-50' : 'bg-yellow-50') }} rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 {{ $confederation->fifa_sync_status === 'synced' ? 'bg-green-500' : ($confederation->fifa_sync_status === 'failed' ? 'bg-red-500' : 'bg-yellow-500') }} rounded-full mr-3"></div>
                            <div>
                                <p class="font-medium {{ $confederation->fifa_sync_status === 'synced' ? 'text-green-900' : ($confederation->fifa_sync_status === 'failed' ? 'text-red-900' : 'text-yellow-900') }}">{{ $confederation->name }}</p>
                                <p class="text-sm {{ $confederation->fifa_sync_status === 'synced' ? 'text-green-700' : ($confederation->fifa_sync_status === 'failed' ? 'text-red-700' : 'text-yellow-700') }}">
                                    {{ $confederation->fifa_sync_status === 'synced' ? 'Synchronisé' : ($confederation->fifa_sync_status === 'failed' ? 'Échec' : 'En attente') }}
                                    @if($confederation->fifa_sync_date)
                                        - {{ $confederation->fifa_sync_date->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span class="{{ $confederation->fifa_sync_status === 'synced' ? 'text-green-600' : ($confederation->fifa_sync_status === 'failed' ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ ucfirst($confederation->fifa_sync_status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- FIFA Analytics -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques FIFA</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg">
                        <div>
                            <p class="font-medium text-indigo-900">Confédérations</p>
                            <p class="text-sm text-indigo-700">{{ $fifaStats['confederations']['total'] }} confédérations</p>
                        </div>
                        <span class="text-indigo-600">{{ $fifaStats['confederations']['synced'] }}/{{ $fifaStats['confederations']['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="font-medium text-green-900">Associations</p>
                            <p class="text-sm text-green-700">{{ $fifaStats['associations']['total'] }} associations</p>
                        </div>
                        <span class="text-green-600">{{ $fifaStats['associations']['synced'] }}/{{ $fifaStats['associations']['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-blue-900">Clubs</p>
                            <p class="text-sm text-blue-700">{{ $fifaStats['clubs']['total'] }} clubs</p>
                        </div>
                        <span class="text-blue-600">{{ $fifaStats['clubs']['synced'] }}/{{ $fifaStats['clubs']['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div>
                            <p class="font-medium text-purple-900">Joueurs</p>
                            <p class="text-sm text-purple-700">{{ $fifaStats['players']['total'] }} joueurs</p>
                        </div>
                        <span class="text-purple-600">{{ $fifaStats['players']['synced'] }}/{{ $fifaStats['players']['total'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FIFA Statistics -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques détaillées</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $fifaStats['confederations']['total'] }}</div>
                    <div class="text-sm text-gray-600">Confédérations</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $fifaStats['associations']['total'] }}</div>
                    <div class="text-sm text-gray-600">Associations</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $fifaStats['clubs']['total'] }}</div>
                    <div class="text-sm text-gray-600">Clubs</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $fifaStats['players']['total'] }}</div>
                    <div class="text-sm text-gray-600">Joueurs</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="/fifa/connectivity" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔗 Connectivité
                </a>
                <a href="/fifa/sync-dashboard" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔄 Synchronisation
                </a>
                <a href="/fifa/contracts" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📋 Contrats
                </a>
                <a href="/fifa/analytics" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📊 Analytics
                </a>
            </div>
        </div>

        @if($filteredConfederation)
        <!-- Confederation Filter Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtrage par confédération</h3>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    @if($filteredConfederation->confederation_logo_url)
                        <img src="{{ asset('storage/' . $filteredConfederation->confederation_logo_url) }}" 
                             alt="Logo {{ $filteredConfederation->name }}" 
                             class="h-12 w-12 object-contain rounded-lg mr-4">
                    @else
                        <div class="h-12 w-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-lg">{{ $filteredConfederation->short_name }}</span>
                        </div>
                    @endif
                    <div>
                        <h4 class="text-lg font-medium text-gray-900">{{ $filteredConfederation->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $filteredConfederation->country }} - {{ $filteredConfederation->associations->count() }} associations</p>
                    </div>
                </div>
                <a href="/fifa/dashboard" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Voir toutes les confédérations
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 