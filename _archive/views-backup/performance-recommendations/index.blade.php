@extends('layouts.app')

@section('title', 'Recommandations de Performance')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Recommandations de Performance</h1>
                    <p class="mt-1 text-sm text-gray-500">Recommandations IA pour l'amélioration des performances</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('performance-recommendations.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nouvelle Recommandation
                    </a>
                    <button onclick="generateAIRecommendations()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        IA Génération
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Nom du joueur, titre...">
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" id="type" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Tous les types</option>
                        <option value="improvement" {{ request('type') == 'improvement' ? 'selected' : '' }}>Amélioration</option>
                        <option value="maintenance" {{ request('type') == 'maintenance' ? 'selected' : '' }}>Maintien</option>
                        <option value="health_based" {{ request('type') == 'health_based' ? 'selected' : '' }}>Basé sur la santé</option>
                        <option value="trend_based" {{ request('type') == 'trend_based' ? 'selected' : '' }}>Basé sur les tendances</option>
                    </select>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="category" id="category" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Toutes les catégories</option>
                        <option value="physical" {{ request('category') == 'physical' ? 'selected' : '' }}>Physique</option>
                        <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Technique</option>
                        <option value="tactical" {{ request('category') == 'tactical' ? 'selected' : '' }}>Tactique</option>
                        <option value="mental" {{ request('category') == 'mental' ? 'selected' : '' }}>Mental</option>
                        <option value="social" {{ request('category') == 'social' ? 'selected' : '' }}>Social</option>
                        <option value="medical" {{ request('category') == 'medical' ? 'selected' : '' }}>Médical</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priorité</label>
                    <select name="priority" id="priority" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Toutes les priorités</option>
                        <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critique</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Élevée</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Faible</option>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Recommandations</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $recommendations->total() }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Terminées</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $recommendations->where('status', 'completed')->count() }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">En cours</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $recommendations->where('status', 'in_progress')->count() }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Progrès moyen</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($recommendations->avg('progress'), 1) }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($recommendations as $recommendation)
                <li>
                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($recommendation->player->player_picture)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $recommendation->player->player_picture) }}" alt="{{ $recommendation->player->first_name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($recommendation->player->first_name, 0, 1) }}{{ substr($recommendation->player->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $recommendation->title }}
                                        </p>
                                        @if($recommendation->ai_model_version)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                IA v{{ $recommendation->ai_model_version }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center mt-1 space-x-4">
                                        <p class="text-sm text-gray-500">
                                            {{ $recommendation->player->first_name }} {{ $recommendation->player->last_name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ ucfirst($recommendation->category) }} • {{ ucfirst($recommendation->type) }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $recommendation->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                        {{ Str::limit($recommendation->description, 150) }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <!-- Priority Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $recommendation->priority === 'critical' ? 'bg-red-100 text-red-800' : 
                                    ($recommendation->priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                                    ($recommendation->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) 
                                }}">
                                    {{ ucfirst($recommendation->priority) }}
                                </span>

                                <!-- Progress Bar -->
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $recommendation->progress }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $recommendation->progress }}%</span>
                                </div>

                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $recommendation->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($recommendation->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $recommendation->status)) }}
                                </span>

                                <!-- Confidence Score -->
                                @if($recommendation->confidence_score)
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-500">Confiance:</span>
                                    <span class="ml-1 text-sm font-medium {{ $recommendation->confidence_score > 0.8 ? 'text-green-600' : ($recommendation->confidence_score > 0.6 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($recommendation->confidence_score * 100, 0) }}%
                                    </span>
                                </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('performance-recommendations.show', $recommendation) }}" 
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Voir
                                    </a>
                                    <a href="{{ route('performance-recommendations.edit', $recommendation) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Modifier
                                    </a>
                                    @if($recommendation->status === 'pending')
                                    <button onclick="implementRecommendation({{ $recommendation->id }})" 
                                            class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        Implémenter
                                    </button>
                                    @endif
                                    <button onclick="deleteRecommendation({{ $recommendation->id }})" 
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune recommandation trouvée</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle recommandation ou générer des recommandations IA.</p>
                        <div class="mt-6 space-x-3">
                            <a href="{{ route('performance-recommendations.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Nouvelle Recommandation
                            </a>
                            <button onclick="generateAIRecommendations()" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Générer IA
                            </button>
                        </div>
                    </div>
                </li>
                @endforelse
            </ul>
        </div>

        <!-- Pagination -->
        @if($recommendations->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($recommendations->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Précédent
                    </span>
                @else
                    <a href="{{ $recommendations->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Précédent
                    </a>
                @endif

                @if($recommendations->hasMorePages())
                    <a href="{{ $recommendations->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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
                        Affichage de <span class="font-medium">{{ $recommendations->firstItem() }}</span> à <span class="font-medium">{{ $recommendations->lastItem() }}</span> sur <span class="font-medium">{{ $recommendations->total() }}</span> résultats
                    </p>
                </div>
                <div>
                    {{ $recommendations->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- AI Generation Modal -->
<div id="aiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Génération IA</h3>
                <button onclick="closeAIModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="aiModalContent">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-4 text-gray-600">Analyse des performances et génération des recommandations...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Implementation Modal -->
<div id="implementationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Implémenter la recommandation</h3>
                <button onclick="closeImplementationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="implementationForm">
                <div class="space-y-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début *</label>
                        <input type="date" name="start_date" id="start_date" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="expected_completion_date" class="block text-sm font-medium text-gray-700">Date de fin prévue *</label>
                        <input type="date" name="expected_completion_date" id="expected_completion_date" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="implementation_notes" class="block text-sm font-medium text-gray-700">Notes d'implémentation</label>
                        <textarea name="implementation_notes" id="implementation_notes" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="Notes sur l'implémentation..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeImplementationModal()" 
                            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Implémenter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentRecommendationId = null;

function deleteRecommendation(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette recommandation ?')) {
        fetch(`/performance-recommendations/${id}`, {
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

function implementRecommendation(id) {
    currentRecommendationId = id;
    document.getElementById('implementationModal').classList.remove('hidden');
}

function closeImplementationModal() {
    document.getElementById('implementationModal').classList.add('hidden');
    currentRecommendationId = null;
}

document.getElementById('implementationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/performance-recommendations/${currentRecommendationId}/implement`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Erreur lors de l\'implémentation: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'implémentation');
    });
});

function generateAIRecommendations() {
    document.getElementById('aiModal').classList.remove('hidden');
    
    // Simulate AI recommendation generation
    setTimeout(() => {
        document.getElementById('aiModalContent').innerHTML = `
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Recommandations générées</h3>
                <p class="mt-1 text-sm text-gray-500">8 nouvelles recommandations IA ont été créées.</p>
                <div class="mt-6">
                    <button onclick="closeAIModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Fermer
                    </button>
                </div>
            </div>
        `;
    }, 3000);
}

function closeAIModal() {
    document.getElementById('aiModal').classList.add('hidden');
}
</script>
@endpush 