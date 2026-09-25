@extends('layouts.app')

@section('title', $player->full_name . ' - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête du joueur -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if($player->player_face_url)
                            <img class="h-24 w-24 rounded-full border-4 border-white" src="{{ $player->player_face_url }}" alt="{{ $player->full_name }}">
                        @else
                            <div class="h-24 w-24 rounded-full bg-white flex items-center justify-center border-4 border-white">
                                <span class="text-blue-600 font-bold text-3xl">
                                    {{ substr($player->first_name, 0, 1) . substr($player->last_name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="ml-6 text-white">
                        <h1 class="text-3xl font-bold">{{ $player->full_name }}</h1>
                        <p class="text-blue-100 text-lg">{{ $player->position }} • {{ $player->nationality }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-2xl font-bold">{{ $player->age }} ans</span>
                            @if($player->overall_rating)
                                <div class="ml-4 flex items-center">
                                    <span class="text-lg font-semibold">Note: {{ $player->overall_rating }}</span>
                                    @if($player->potential_rating && $player->potential_rating > $player->overall_rating)
                                        <span class="ml-2 text-sm bg-green-500 text-white px-2 py-1 rounded">
                                            +{{ $player->potential_rating - $player->overall_rating }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="ml-auto">
                        <div class="flex space-x-4">
                            <a href="{{ route('players.edit', $player) }}" 
                               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Modifier
                            </a>
                            <a href="{{ route('players.health-records', $player) }}" 
                               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Dossiers Médicaux
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations générales -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Informations Générales</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Informations Personnelles</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                                        <dd class="text-sm text-gray-900">{{ $player->full_name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Date de naissance</dt>
                                        <dd class="text-sm text-gray-900">{{ $player->date_of_birth ? $player->date_of_birth->format('d/m/Y') : 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Âge</dt>
                                        <dd class="text-sm text-gray-900">{{ $player->age }} ans</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Nationalité</dt>
                                        <dd class="text-sm text-gray-900">{{ $player->nationality }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Caractéristiques Physiques</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Position</dt>
                                        <dd class="text-sm text-gray-900">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $player->position }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Taille</dt>
                                        <dd class="text-sm text-gray-900">{{ $player->height ? $player->height . ' cm' : 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Poids</dt>
                                        <dd class="text-sm text-gray-900">{{ $player->weight ? $player->weight . ' kg' : 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Pied préféré</dt>
                                        <dd class="text-sm text-gray-900">{{ $player->preferred_foot ?? 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques FIFA -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Statistiques FIFA</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Notes</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Note globale</dt>
                                        <dd class="text-sm text-gray-900 font-semibold">{{ $player->overall_rating ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Note potentielle</dt>
                                        <dd class="text-sm text-gray-900 font-semibold">{{ $player->potential_rating ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Pied faible</dt>
                                        <dd class="text-sm text-gray-900">{{ $player->weak_foot ? $player->weak_foot . '/5' : 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Gesticulations</dt>
                                        <dd class="text-sm text-gray-900">{{ $player->skill_moves ? $player->skill_moves . '/5' : 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Valeur</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Valeur marchande</dt>
                                        <dd class="text-sm text-gray-900 font-semibold">
                                            {{ $player->value_eur ? '€' . number_format($player->value_eur, 0, ',', ' ') : 'N/A' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Salaire</dt>
                                        <dd class="text-sm text-gray-900">
                                            {{ $player->wage_eur ? '€' . number_format($player->wage_eur, 0, ',', ' ') . '/semaine' : 'N/A' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Clause de libération</dt>
                                        <dd class="text-sm text-gray-900">
                                            {{ $player->release_clause_eur ? '€' . number_format($player->release_clause_eur, 0, ',', ' ') : 'N/A' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Réputation internationale</dt>
                                        <dd class="text-sm text-gray-900">{{ $player->international_reputation ? $player->international_reputation . '/5' : 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dossiers médicaux récents -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-800">Dossiers Médicaux Récents</h2>
                            <a href="{{ route('players.health-records', $player) }}" 
                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                Voir tous →
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($player->healthRecords->count() > 0)
                            <div class="space-y-4">
                                @foreach($player->healthRecords->take(3) as $record)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-900">{{ $record->diagnosis }}</h3>
                                                <p class="text-sm text-gray-500">{{ $record->date->format('d/m/Y') }}</p>
                                            </div>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $record->severity === 'High' ? 'bg-red-100 text-red-800' : 
                                                   ($record->severity === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                {{ $record->severity }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-gray-400 mb-4">
                                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun dossier médical</h3>
                                <p class="text-gray-500 mb-4">Ce joueur n'a pas encore de dossier médical enregistré.</p>
                                <button class="bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg cursor-not-allowed" disabled>
                                    Créer un dossier (fonctionnalité à venir)
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Club et association -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Club</h2>
                    </div>
                    <div class="p-6">
                        @if($player->club)
                            <div class="text-center">
                                @if($player->club_logo_url)
                                    <img class="h-16 w-16 mx-auto mb-4" src="{{ $player->club_logo_url }}" alt="{{ $player->club->name }}">
                                @endif
                                <h3 class="text-lg font-semibold text-gray-900">{{ $player->club->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $player->club->league ?? 'Ligue inconnue' }}</p>
                                @if($player->contract_valid_until)
                                    <p class="text-sm text-gray-500 mt-2">
                                        Contrat jusqu'au {{ \Carbon\Carbon::parse($player->contract_valid_until)->format('d/m/Y') }}
                                    </p>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-gray-400 mb-4">
                                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Sans club</h3>
                                <p class="text-gray-500">Ce joueur n'est actuellement affilié à aucun club.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Actions Rapides</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <button onclick="loadFifaStats()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nouveau dossier médical
                        </button>
                        <a href="{{ route('medical-predictions.create', ['player_id' => $player->id]) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            Générer prédiction
                        </a>
                        <button onclick="loadFifaStats()" 
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Statistiques FIFA
                        </button>
                    </div>
                </div>

                <!-- Informations FIFA -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Informations FIFA</h2>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Version FIFA</dt>
                                <dd class="text-sm text-gray-900">{{ $player->fifa_version ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dernière mise à jour</dt>
                                <dd class="text-sm text-gray-900">{{ $player->last_updated ? $player->last_updated->format('d/m/Y H:i') : 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID FIFA Connect</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $player->fifa_connect_id ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadFifaStats() {
    // Simulation du chargement des statistiques FIFA
    alert('Fonctionnalité en cours de développement. Les statistiques FIFA seront bientôt disponibles !');
}
</script>
@endsection 