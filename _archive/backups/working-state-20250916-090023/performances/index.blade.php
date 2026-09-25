@extends('layouts.app')

@section('title', 'Gestion des Performances')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Gestion des Performances</h1>
                    <p class="mt-1 text-sm text-gray-500">Suivi intégré des performances sportives</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('performances.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nouvelle Performance
                    </a>
                    <button onclick="generateAIRecommendations()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        IA Recommandations
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Nom du joueur, FIFA ID...">
                </div>
                
                <div>
                    <label for="player_id" class="block text-sm font-medium text-gray-700">Joueur</label>
                    <select name="player_id" id="player_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Tous les joueurs</option>
                        @foreach($players as $player)
                            <option value="{{ $player->id }}" {{ request('player_id') == $player->id ? 'selected' : '' }}>
                                {{ $player->first_name }} {{ $player->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="data_source" class="block text-sm font-medium text-gray-700">Source de données</label>
                    <select name="data_source" id="data_source" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Toutes les sources</option>
                        <option value="match" {{ request('data_source') == 'match' ? 'selected' : '' }}>Match</option>
                        <option value="training" {{ request('data_source') == 'training' ? 'selected' : '' }}>Entraînement</option>
                        <option value="assessment" {{ request('data_source') == 'assessment' ? 'selected' : '' }}>Évaluation</option>
                        <option value="fifa_connect" {{ request('data_source') == 'fifa_connect' ? 'selected' : '' }}>FIFA Connect</option>
                        <option value="medical_device" {{ request('data_source') == 'medical_device' ? 'selected' : '' }}>Dispositif médical</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Performances</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $performances->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Moyenne Rating</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Distance Moyenne</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['average_distance'] ?? 0, 0) }}m</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">IA Recommandations</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $aiRecommendationsCount ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($performances as $performance)
                <li>
                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($performance->player->player_picture)
                                        <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $performance->player->player_picture) }}" alt="{{ $performance->player->first_name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($performance->player->first_name, 0, 1) }}{{ substr($performance->player->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $performance->player->first_name }} {{ $performance->player->last_name }}
                                        </p>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $performance->player->fifa_id }}
                                        </span>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <p class="text-sm text-gray-500">
                                            {{ $performance->match_date->format('d/m/Y') }} • 
                                            {{ $performance->distance_covered }}m • {{ $performance->sprint_count }} sprints
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <!-- Performance Metrics -->
                                <div class="flex items-center space-x-2">
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-gray-900">{{ number_format($performance->rating, 1) }}</div>
                                        <div class="text-xs text-gray-500">Rating</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $performance->max_speed }} km/h</div>
                                        <div class="text-xs text-gray-500">Vitesse max</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $performance->passes_completed }}/{{ $performance->passes_attempted }}</div>
                                        <div class="text-xs text-gray-500">Passes</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $performance->goals_scored }}</div>
                                        <div class="text-xs text-gray-500">Buts</div>
                                    </div>
                                </div>

                                <!-- Performance Level Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $performance->rating >= 8 ? 'bg-green-100 text-green-800' : 
                                       ($performance->rating >= 6 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $performance->rating >= 8 ? 'Excellent' : 
                                       ($performance->rating >= 6 ? 'Bon' : 'À améliorer') }}
                                </span>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('performances.show', $performance) }}" 
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Voir
                                    </a>
                                    <a href="{{ route('performances.edit', $performance) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Modifier
                                    </a>
                                    <button onclick="deletePerformance({{ $performance->id }})" 
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @empty
                <li class="px-4 py-8 text-center">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune performance trouvée</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter une nouvelle performance.</p>
                        <div class="mt-6">
                            <a href="{{ route('performances.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Nouvelle Performance
                            </a>
                        </div>
                    </div>
                </li>
                @endforelse
            </ul>
        </div>

        <!-- Pagination -->
        @if($performances->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($performances->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Précédent
                    </span>
                @else
                    <a href="{{ $performances->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Précédent
                    </a>
                @endif

                @if($performances->hasMorePages())
                    <a href="{{ $performances->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Suivant
                    </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Suivant
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Affichage de <span class="font-medium">{{ $performances->firstItem() }}</span> à <span class="font-medium">{{ $performances->lastItem() }}</span> sur <span class="font-medium">{{ $performances->total() }}</span> résultats
                    </p>
                </div>
                <div>
                    {{ $performances->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- AI Recommendations Modal -->
<div id="aiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Générer des recommandations IA</h3>
                <button onclick="closeAIModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="aiModalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function deletePerformance(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette performance ?')) {
        fetch(`/performances/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
        });
    }
}

function generateAIRecommendations() {
    document.getElementById('aiModal').classList.remove('hidden');
    document.getElementById('aiModalContent').innerHTML = `
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Génération des recommandations IA...</p>
        </div>
    `;
    
    // Simulate AI recommendation generation
    setTimeout(() => {
        document.getElementById('aiModalContent').innerHTML = `
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Recommandations générées</h3>
                <p class="mt-1 text-sm text-gray-500">5 nouvelles recommandations ont été créées.</p>
                <div class="mt-6">
                    <button onclick="closeAIModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Fermer
                    </button>
                </div>
            </div>
        `;
    }, 2000);
}

function closeAIModal() {
    document.getElementById('aiModal').classList.add('hidden');
}
</script>
@endpush 