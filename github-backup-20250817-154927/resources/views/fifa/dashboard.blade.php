@extends('layouts.app')

@section('title', 'Dashboard FIFA - Système de Transferts')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard FIFA</h1>
                    <p class="mt-1 text-sm text-gray-500">Système de gestion des transferts internationaux</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm text-gray-600">Connecté à FIFA</span>
                    </div>
                    <a href="{{ route('fifa.connectivity') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Statut Connexion
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex space-x-8">
                <a href="{{ route('fifa-dashboard.index') }}" class="border-b-2 border-blue-500 text-gray-900 py-4 px-1 text-sm font-medium">
                    Vue d'ensemble
                </a>
                <a href="{{ route('fifa-dashboard.transfers') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Transferts
                </a>
                <a href="{{ route('fifa-dashboard.contracts') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Contrats
                </a>
                <a href="{{ route('fifa-dashboard.federations') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Fédérations
                </a>
                <a href="{{ route('fifa-dashboard.analytics') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Analytics
                </a>
                <a href="{{ route('fifa-dashboard.reports') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Rapports
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Transferts -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Transferts Actifs</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ \App\Models\Transfer::where('transfer_status', 'pending')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('fifa-dashboard.transfers') }}" class="font-medium text-blue-700 hover:text-blue-900">Voir tous les transferts</a>
                    </div>
                </div>
            </div>

            <!-- Contrats -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Contrats Actifs</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ \App\Models\Contract::where('is_active', true)->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('fifa-dashboard.contracts') }}" class="font-medium text-green-700 hover:text-green-900">Voir tous les contrats</a>
                    </div>
                </div>
            </div>

            <!-- Fédérations -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Fédérations</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ \App\Models\Federation::count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('fifa-dashboard.federations') }}" class="font-medium text-purple-700 hover:text-purple-900">Voir toutes les fédérations</a>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Documents en attente</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ \App\Models\TransferDocument::where('validation_status', 'pending')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('transfer-documents.index') }}" class="font-medium text-yellow-700 hover:text-yellow-900">Voir tous les documents</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Transfers -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Transferts Récents</h3>
                </div>
                <div class="p-6">
                    @php
                        $recentTransfers = \App\Models\Transfer::with(['player', 'clubOrigin', 'clubDestination'])
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($recentTransfers->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentTransfers as $transfer)
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $transfer->player->first_name ?? 'N/A' }} {{ $transfer->player->last_name ?? 'N/A' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $transfer->clubOrigin->name ?? 'N/A' }} → {{ $transfer->clubDestination->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($transfer->transfer_status === 'approved') bg-green-100 text-green-800
                                            @elseif($transfer->transfer_status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($transfer->transfer_status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($transfer->transfer_status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Aucun transfert récent</p>
                    @endif
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Statut du Système</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- FIFA Connection -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">Connexion FIFA</span>
                            </div>
                            <span class="text-sm text-gray-500">Connecté</span>
                        </div>

                        <!-- Database -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">Base de données</span>
                            </div>
                            <span class="text-sm text-gray-500">Opérationnel</span>
                        </div>

                        <!-- API Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">API FIFA</span>
                            </div>
                            <span class="text-sm text-gray-500">Disponible</span>
                        </div>

                        <!-- Sync Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-400 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">Synchronisation</span>
                            </div>
                            <span class="text-sm text-gray-500">En cours</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('fifa.connectivity') }}" class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Vérifier le statut complet
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Actions Rapides</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('transfers.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">Nouveau Transfert</h4>
                            <p class="text-sm text-gray-500">Créer un nouveau transfert</p>
                        </div>
                    </a>

                    <a href="{{ route('contracts.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">Nouveau Contrat</h4>
                            <p class="text-sm text-gray-500">Créer un nouveau contrat</p>
                        </div>
                    </a>

                    <a href="{{ route('federations.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">Nouvelle Fédération</h4>
                            <p class="text-sm text-gray-500">Ajouter une fédération</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 