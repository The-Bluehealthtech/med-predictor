@extends('layouts.app')

@section('title', 'Gestion des Transferts - ' . ucfirst($transferType))

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        @if($transferType === 'domestic')
                            🏠 Gestion des Transferts Nationaux
                        @elseif($transferType === 'international')
                            🌍 Gestion des Transferts Internationaux
                        @elseif($transferType === 'loan')
                            📋 Gestion des Prêts
                        @elseif($transferType === 'free_transfer')
                            🆓 Gestion des Transferts Libres
                        @endif
                    </h1>
                    <p class="mt-2 text-gray-600">
                        @if($transferType === 'domestic')
                            Transferts entre clubs du même pays
                        @elseif($transferType === 'international')
                            Transferts entre clubs de pays différents
                        @elseif($transferType === 'loan')
                            Prêts temporaires de joueurs
                        @elseif($transferType === 'free_transfer')
                            Transferts sans frais de transfert
                        @endif
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.transfer-management.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        ← Retour
                    </a>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        + Nouveau Transfert
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 font-bold">{{ count($transfers) }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ count($transfers) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <span class="text-yellow-600 font-bold">{{ collect($transfers)->where('status', 'pending')->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">En Attente</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ collect($transfers)->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-green-600 font-bold">{{ collect($transfers)->where('status', 'approved')->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Approuvés</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ collect($transfers)->where('status', 'approved')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <span class="text-red-600 font-bold">{{ collect($transfers)->where('status', 'rejected')->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Rejetés</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ collect($transfers)->where('status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="approved">Approuvé</option>
                        <option value="rejected">Rejeté</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Club d'origine</label>
                    <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les clubs</option>
                        @foreach(collect($transfers)->pluck('from_club')->unique() as $club)
                            <option value="{{ $club }}">{{ $club }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Club de destination</label>
                    <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les clubs</option>
                        @foreach(collect($transfers)->pluck('to_club')->unique() as $club)
                            <option value="{{ $club }}">{{ $club }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Filtrer
                    </button>
                </div>
            </div>
        </div>

        <!-- Transfers Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Liste des Transferts</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club d'origine</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club de destination</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transfers as $transfer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $transfer['player'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $transfer['from_club'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $transfer['to_club'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transfer['status'] === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        En attente
                                    </span>
                                @elseif($transfer['status'] === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Approuvé
                                    </span>
                                @elseif($transfer['status'] === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Rejeté
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($transfer['date'])->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($transfer['status'] === 'pending')
                                        <button onclick="approveTransfer({{ $transfer['id'] }})" class="text-green-600 hover:text-green-900">Approuver</button>
                                        <button onclick="rejectTransfer({{ $transfer['id'] }})" class="text-red-600 hover:text-red-900">Rejeter</button>
                                    @endif
                                    <button onclick="viewTransfer({{ $transfer['id'] }})" class="text-blue-600 hover:text-blue-900">Voir</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function approveTransfer(id) {
    if (confirm('Êtes-vous sûr de vouloir approuver ce transfert ?')) {
        showNotification('Transfert approuvé avec succès', 'success');
        // Here you would make an API call to approve the transfer
    }
}

function rejectTransfer(id) {
    if (confirm('Êtes-vous sûr de vouloir rejeter ce transfert ?')) {
        showNotification('Transfert rejeté', 'warning');
        // Here you would make an API call to reject the transfer
    }
}

function viewTransfer(id) {
    showNotification(`Ouverture des détails du transfert #${id}`, 'info');
    // Here you would open a modal or redirect to transfer details
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
    
    const colors = {
        'info': 'bg-blue-500 text-white',
        'success': 'bg-green-500 text-white',
        'warning': 'bg-yellow-500 text-white',
        'error': 'bg-red-500 text-white'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>
@endsection
