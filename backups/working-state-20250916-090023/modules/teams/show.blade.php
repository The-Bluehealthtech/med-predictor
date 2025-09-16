@extends('layouts.app')

@section('title', 'Détails de l\'Équipe')

@section('content')
<div class="container-fluid">
    <!-- Header avec boutons -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-users text-blue-600 mr-3"></i>
                {{ $team->name }}
            </h1>
            <p class="text-gray-600 mt-2">Détails de l'équipe</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('modules.teams.edit', $team) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            <a href="{{ route('modules.teams.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Carte principale -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nom de l'équipe</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $team->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Club</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $team->club->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Association</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $team->club->association->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Niveau</label>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                            @if($team->level === 'professional') bg-purple-100 text-purple-800
                            @elseif($team->level === 'semi-professional') bg-blue-100 text-blue-800
                            @elseif($team->level === 'amateur') bg-green-100 text-green-800
                            @elseif($team->level === 'youth') bg-yellow-100 text-yellow-800
                            @elseif($team->level === 'academy') bg-indigo-100 text-indigo-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($team->level ?? 'N/A') }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Catégorie d'âge</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $team->age_category ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Discipline</label>
                        <p class="text-lg font-semibold text-gray-900">{{ ucfirst($team->discipline ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Statut</label>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                            @if($team->status === 'active') bg-green-100 text-green-800
                            @elseif($team->status === 'inactive') bg-red-100 text-red-800
                            @elseif($team->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($team->status ?? 'N/A') }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">ID Équipe</label>
                        <p class="text-lg font-semibold text-gray-900">#{{ $team->id }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte du club -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du club</h3>
                @if($team->club)
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nom du club</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $team->club->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Ville</label>
                            <p class="text-gray-900">{{ $team->club->city ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Pays</label>
                            <p class="text-gray-900">{{ $team->club->country ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Stade</label>
                            <p class="text-gray-900">{{ $team->club->stadium ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Année de fondation</label>
                            <p class="text-gray-900">{{ $team->club->founded_year ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Statut du club</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($team->club->status === 'active') bg-green-100 text-green-800
                                @elseif($team->club->status === 'inactive') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($team->club->status ?? 'N/A') }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">Aucune information de club disponible</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
        <div class="flex flex-wrap gap-4">
            <button onclick="viewPlayers()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-users mr-2"></i>
                Voir les joueurs
            </button>
            <button onclick="viewMatches()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-futbol mr-2"></i>
                Voir les matchs
            </button>
            <button onclick="viewStatistics()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-chart-bar mr-2"></i>
                Statistiques
            </button>
            <button onclick="exportTeam()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-download mr-2"></i>
                Exporter
            </button>
        </div>
    </div>
</div>

<script>
function viewPlayers() {
    alert('Fonctionnalité de visualisation des joueurs à implémenter');
}

function viewMatches() {
    alert('Fonctionnalité de visualisation des matchs à implémenter');
}

function viewStatistics() {
    alert('Fonctionnalité de visualisation des statistiques à implémenter');
}

function exportTeam() {
    alert('Fonctionnalité d\'export à implémenter');
}
</script>
@endsection



