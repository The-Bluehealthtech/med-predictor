@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">FIFA Contract System</h1>
            <p class="mt-2 text-gray-600">Gestion des contrats internationaux</p>
        </div>

        <!-- Navigation -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="flex space-x-8">
                <a href="{{ route('fifa-dashboard.index') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Vue d'ensemble
                </a>
                <a href="{{ route('fifa-dashboard.transfers') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium">
                    Transferts
                </a>
                <a href="{{ route('fifa-dashboard.contracts') }}" class="border-b-2 border-blue-500 text-gray-900 py-4 px-1 text-sm font-medium">
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
                <h2 class="text-xl font-semibold text-gray-900">Contrats</h2>
                <a href="{{ route('contracts.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 inline-block">
                    Nouveau Contrat
                </a>
            </div>

            <!-- Contract Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ \App\Models\Contract::where('is_active', true)->count() }}</div>
                    <div class="text-sm text-green-600">Actifs</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">{{ \App\Models\Contract::where('is_active', false)->count() }}</div>
                    <div class="text-sm text-red-600">Expirés</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ \App\Models\Contract::where('expiry_date', '<=', now()->addMonths(3))->count() }}</div>
                    <div class="text-sm text-yellow-600">Expirent bientôt</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-gray-600">{{ \App\Models\Contract::count() }}</div>
                    <div class="text-sm text-gray-600">Total</div>
                </div>
            </div>

            <!-- Contract List -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de début</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de fin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $contracts = \App\Models\Contract::with(['player', 'club'])
                                ->latest()
                                ->take(10)
                                ->get();
                        @endphp
                        
                        @if($contracts->count() > 0)
                            @foreach($contracts as $contract)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $contract->player->first_name ?? 'N/A' }} {{ $contract->player->last_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $contract->club->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($contract->contract_type ?? 'Standard') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $contract->start_date ? $contract->start_date->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $contract->end_date ? $contract->end_date->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($contract->is_active) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $contract->is_active ? 'Actif' : 'Expiré' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('contracts.show', $contract) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Aucun contrat trouvé
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