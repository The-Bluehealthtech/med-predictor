@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">FIFA Transfer System</h1>
            <p class="mt-2 text-gray-600">Gestion des transferts internationaux</p>
        </div>

        <!-- Navigation -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="flex space-x-8">
                <a href="{{ route('fifa-dashboard.index') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Vue d'ensemble
                </a>
                <a href="{{ route('fifa-dashboard.transfers') }}" class="border-b-2 border-blue-500 text-gray-900 py-4 px-1 text-sm font-medium">
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

        <!-- Content -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Transferts</h2>
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                    Nouveau Transfert
                </button>
            </div>

            <!-- Transfer Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ \App\Models\Transfer::where('transfer_status', 'pending')->count() }}</div>
                    <div class="text-sm text-blue-600">En attente</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ \App\Models\Transfer::where('transfer_status', 'approved')->count() }}</div>
                    <div class="text-sm text-green-600">Approuvés</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">{{ \App\Models\Transfer::where('transfer_status', 'rejected')->count() }}</div>
                    <div class="text-sm text-red-600">Rejetés</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-gray-600">{{ \App\Models\Transfer::count() }}</div>
                    <div class="text-sm text-gray-600">Total</div>
                </div>
            </div>

            <!-- Transfer List -->
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
                        @php
                            $transfers = \App\Models\Transfer::with(['player', 'clubOrigin', 'clubDestination'])
                                ->latest()
                                ->take(10)
                                ->get();
                        @endphp
                        
                        @if($transfers->count() > 0)
                            @foreach($transfers as $transfer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $transfer->player->first_name ?? 'N/A' }} {{ $transfer->player->last_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $transfer->clubOrigin->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $transfer->clubDestination->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($transfer->transfer_status === 'approved') bg-green-100 text-green-800
                                            @elseif($transfer->transfer_status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($transfer->transfer_status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($transfer->transfer_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transfer->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900">Voir</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Aucun transfert trouvé
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 