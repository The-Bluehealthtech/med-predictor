@extends('layouts.app')

@section('title', 'Gestion des Transferts - Administration')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-teal-600 to-cyan-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">🔄</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Gestion des Transferts
                                </h1>
                                <p class="text-sm text-gray-600">Gérer les transferts de joueurs connecté à FIFA TMS</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.administration.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour à l'Administration</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statut FIFA TMS -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Connexion FIFA TMS</h3>
                        <p class="mt-1 text-sm text-gray-500">Statut de la connexion avec le système FIFA Transfer Matching System</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if($fifaTmsStatus['status'] === 'connected')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                Connecté
                            </span>
                        @elseif($fifaTmsStatus['status'] === 'disconnected')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                Déconnecté
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                                Erreur
                            </span>
                        @endif
                        
                        <form action="{{ route('admin.transfer-management.sync-fifa-tms') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                🔄 Synchroniser
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="mt-4 text-sm text-gray-600">
                    <p><strong>Message:</strong> {{ $fifaTmsStatus['message'] }}</p>
                    @if($fifaTmsStatus['last_sync'])
                        <p><strong>Dernière synchronisation:</strong> {{ $fifaTmsStatus['last_sync']->format('d/m/Y H:i') }}</p>
                    @endif
                    @if(isset($fifaTmsStatus['api_version']))
                        <p><strong>Version API:</strong> {{ $fifaTmsStatus['api_version'] }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📊</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Transferts</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_transfers'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">⏳</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">En Attente</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-yellow-600">{{ $stats['pending_transfers'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">✅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Approuvés</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-green-600">{{ $stats['approved_transfers'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">❌</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Rejetés</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-red-600">{{ $stats['rejected_transfers'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🌍</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">FIFA TMS</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-blue-600">{{ $stats['fifa_tms_synced'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🏠</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Locaux</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-purple-600">{{ $stats['local_transfers'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Types de transferts -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Types de Transferts</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($transferTypes as $type => $config)
                        <div class="p-6 rounded-lg border-2 border-gray-200 hover:border-{{ $config['color'] }}-300 hover:bg-{{ $config['color'] }}-50 transition-all">
                            <div class="flex items-center mb-3">
                                <span class="text-3xl mr-4">{{ $config['icon'] }}</span>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">{{ $config['name'] }}</h4>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">{{ $config['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('admin.transfer-management.transfers') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        📋 Voir tous les Transferts
                    </a>
                    <a href="{{ route('admin.transfer-management.export', ['format' => 'csv']) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        📤 Exporter CSV
                    </a>
                    <a href="{{ route('admin.transfer-management.export', ['format' => 'json']) }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        📄 Exporter JSON
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

