@extends('layouts.app')

@section('title', 'Transferts - Gestion des Transferts')

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
                                    Transferts
                                </h1>
                                <p class="text-sm text-gray-600">Gérer les transferts de joueurs</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.transfer-management.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour à la Gestion des Transferts</a>
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

        <!-- Actions -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-medium text-gray-900">Liste des Transferts</h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.transfer-management.export', ['format' => 'csv']) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    📤 Exporter CSV
                </a>
                <a href="{{ route('admin.transfer-management.export', ['format' => 'json']) }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                    📄 Exporter JSON
                </a>
            </div>
        </div>

        <!-- Liste des transferts -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($transfers->count() > 0)
                    <div class="space-y-4">
                        @foreach($transfers as $transfer)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $transfer['player_name'] }}</h3>
                                            @if($transfer['status'] === 'approved')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Approuvé
                                                </span>
                                            @elseif($transfer['status'] === 'pending')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    En Attente
                                                </span>
                                            @elseif($transfer['status'] === 'rejected')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    Rejeté
                                                </span>
                                            @endif
                                            
                                            @if($transfer['transfer_type'] === 'domestic')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    🏠 National
                                                </span>
                                            @elseif($transfer['transfer_type'] === 'international')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    🌍 International
                                                </span>
                                            @elseif($transfer['transfer_type'] === 'loan')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    🔄 Prêt
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    🆓 Libre
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                            <div>
                                                <p class="text-sm text-gray-600"><strong>De:</strong> {{ $transfer['from_club'] }}</p>
                                                <p class="text-sm text-gray-600"><strong>Vers:</strong> {{ $transfer['to_club'] }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600"><strong>Frais:</strong> {{ $transfer['transfer_fee'] }}</p>
                                                @if($transfer['fifa_tms_id'])
                                                    <p class="text-sm text-gray-600"><strong>FIFA TMS ID:</strong> {{ $transfer['fifa_tms_id'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><strong>Créé:</strong> {{ $transfer['created_at']->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="ml-6 flex-shrink-0">
                                        <div class="flex space-x-2">
                                            @if($transfer['status'] === 'pending')
                                                <form action="{{ route('admin.transfer-management.approve', $transfer['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 text-sm">
                                                        ✅ Approuver
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.transfer-management.reject', $transfer['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                                        ❌ Rejeter
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-4">Aucun transfert trouvé.</p>
                        <p class="text-sm text-gray-400">Les transferts apparaîtront ici une fois qu'ils seront créés ou synchronisés avec FIFA TMS.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

