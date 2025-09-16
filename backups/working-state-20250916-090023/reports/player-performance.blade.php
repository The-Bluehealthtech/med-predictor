@extends('layouts.app')

@section('title', 'Rapport de Performance des Joueurs - FIT')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-chart-bar text-green-500 mr-3"></i>
            Rapport de Performance des Joueurs
        </h1>
        <p class="text-gray-600">Analyse détaillée des performances des joueurs</p>
    </div>

    <!-- Barre de recherche et filtres -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-search text-blue-500 mr-2"></i>
            Recherche et Filtres
        </h3>
        
        <form method="GET" action="{{ route('competitions.association.player-performance-report') }}" class="space-y-4">
            <!-- Barre de recherche -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher un joueur</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Nom du joueur, email ou club..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <div class="md:w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                    <select name="club_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les clubs</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Rechercher
                </button>
                
                <a href="{{ route('competitions.association.player-performance-report') }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors text-center">
                    <i class="fas fa-times mr-2"></i>Effacer
                </a>
                
                <a href="{{ route('competitions.association.rapports-avances') }}" 
                   class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Retour aux Rapports
                </a>
            </div>
        </form>
    </div>

    <!-- Filtres appliqués -->
    @if(!empty($filters_applied) && (request('search') || request('club_id')))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">Filtres actifs</h3>
        <div class="flex flex-wrap gap-2">
            @if(request('search'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-search mr-1"></i>
                    Recherche: "{{ request('search') }}"
                </span>
            @endif
            @if(request('club_id'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-building mr-1"></i>
                    Club: {{ $clubs->where('id', request('club_id'))->first()->name ?? 'N/A' }}
                </span>
            @endif
        </div>
    </div>
    @endif

    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Joueurs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $players->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-futbol text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Buts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $players->sum('goals') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-handshake text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Passes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $players->sum('assists') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Cartons</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $players->sum('yellow_cards') + $players->sum('red_cards') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des performances -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-table text-gray-500 mr-2"></i>
                Détail des Performances
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matchs</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buts</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cartons J</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cartons R</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note Moy.</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($players as $playerData)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($playerData['player']->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $playerData['player']->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $playerData['player']->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $playerData['player']->club->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $playerData['player']->position ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $playerData['matches_played'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $playerData['goals'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $playerData['assists'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($playerData['yellow_cards'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $playerData['yellow_cards'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($playerData['red_cards'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $playerData['red_cards'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($playerData['average_rating'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ number_format($playerData['average_rating'], 1) }}/10
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-users text-3xl mb-2"></i>
                                <p>Aucun joueur trouvé avec les filtres appliqués</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($players->hasPages())
    <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Affichage de 
            <span class="font-medium">{{ $players->firstItem() }}</span>
            à 
            <span class="font-medium">{{ $players->lastItem() }}</span>
            sur 
            <span class="font-medium">{{ $players->total() }}</span>
            résultats
        </div>
        
        <div class="flex items-center space-x-2">
            {{ $players->appends(request()->query())->links() }}
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="mt-8 flex justify-end space-x-4">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-print mr-2"></i>Imprimer
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endpush
@section('title', 'Rapport de Performance des Joueurs - FIT')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-chart-bar text-green-500 mr-3"></i>
            Rapport de Performance des Joueurs
        </h1>
        <p class="text-gray-600">Analyse détaillée des performances des joueurs</p>
    </div>

    <!-- Barre de recherche et filtres -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-search text-blue-500 mr-2"></i>
            Recherche et Filtres
        </h3>
        
        <form method="GET" action="{{ route('competitions.association.player-performance-report') }}" class="space-y-4">
            <!-- Barre de recherche -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher un joueur</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Nom du joueur, email ou club..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <div class="md:w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                    <select name="club_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les clubs</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Rechercher
                </button>
                
                <a href="{{ route('competitions.association.player-performance-report') }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors text-center">
                    <i class="fas fa-times mr-2"></i>Effacer
                </a>
                
                <a href="{{ route('competitions.association.rapports-avances') }}" 
                   class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Retour aux Rapports
                </a>
            </div>
        </form>
    </div>

    <!-- Filtres appliqués -->
    @if(!empty($filters_applied) && (request('search') || request('club_id')))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">Filtres actifs</h3>
        <div class="flex flex-wrap gap-2">
            @if(request('search'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-search mr-1"></i>
                    Recherche: "{{ request('search') }}"
                </span>
            @endif
            @if(request('club_id'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-building mr-1"></i>
                    Club: {{ $clubs->where('id', request('club_id'))->first()->name ?? 'N/A' }}
                </span>
            @endif
        </div>
    </div>
    @endif

    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Joueurs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $players->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-futbol text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Buts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $players->sum('goals') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-handshake text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Passes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $players->sum('assists') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Cartons</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $players->sum('yellow_cards') + $players->sum('red_cards') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des performances -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-table text-gray-500 mr-2"></i>
                Détail des Performances
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matchs</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buts</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cartons J</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cartons R</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note Moy.</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($players as $playerData)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($playerData['player']->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $playerData['player']->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $playerData['player']->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $playerData['player']->club->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $playerData['player']->position ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $playerData['matches_played'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $playerData['goals'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $playerData['assists'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($playerData['yellow_cards'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $playerData['yellow_cards'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($playerData['red_cards'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $playerData['red_cards'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($playerData['average_rating'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ number_format($playerData['average_rating'], 1) }}/10
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-users text-3xl mb-2"></i>
                                <p>Aucun joueur trouvé avec les filtres appliqués</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($players->hasPages())
    <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Affichage de 
            <span class="font-medium">{{ $players->firstItem() }}</span>
            à 
            <span class="font-medium">{{ $players->lastItem() }}</span>
            sur 
            <span class="font-medium">{{ $players->total() }}</span>
            résultats
        </div>
        
        <div class="flex items-center space-x-2">
            {{ $players->appends(request()->query())->links() }}
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="mt-8 flex justify-end space-x-4">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-print mr-2"></i>Imprimer
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endpush

