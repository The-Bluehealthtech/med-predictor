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
                        @if($player->has_picture)
                            <img class="h-24 w-24 rounded-full border-4 border-white object-cover" src="{{ $player->player_picture_url }}" alt="{{ $player->full_name }}">
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
                        
                        <!-- Club and Association Info -->
                        <div class="flex items-center mt-4 space-x-6">
                            @if($player->club)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 mr-2">
                                        @if($player->club->logo_url)
                                            <img class="h-8 w-8 object-contain bg-white rounded" src="{{ $player->club->logo_url }}" alt="{{ $player->club->name }} logo">
                                        @else
                                            <div class="h-8 w-8 bg-white rounded flex items-center justify-center">
                                                <span class="text-xs text-blue-600 font-bold">{{ substr($player->club->name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-blue-100">{{ $player->club->name }}</div>
                                        <div class="text-xs text-blue-200">{{ $player->club->country ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($player->association)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 mr-2">
                                        @if($player->association->association_logo_url)
                                            <img class="h-8 w-8 object-contain bg-white rounded" src="{{ $player->association->association_logo_url }}" alt="{{ $player->association->name }} logo">
                                        @elseif($player->association->nation_flag_url)
                                            <img class="h-8 w-8 object-contain bg-white rounded" src="{{ $player->association->nation_flag_url }}" alt="{{ $player->association->country }} flag">
                                        @else
                                            <div class="h-8 w-8 bg-white rounded flex items-center justify-center">
                                                <span class="text-xs text-blue-600 font-bold">{{ substr($player->association->name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-blue-100">{{ $player->association->name }}</div>
                                        <div class="text-xs text-blue-200">{{ $player->association->country ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="ml-auto">
                        <div class="flex space-x-4">
                            <a href="{{ route('player-registration.edit', $player) }}" 
                               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Modifier
                            </a>
                            <a href="{{ route('player-registration.health-records', $player) }}" 
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

                <!-- Club and Association Information -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Club et Association</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($player->club)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Club Actuel</h3>
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-16 w-16 mr-4">
                                            @if($player->club->logo_url)
                                                <img class="h-16 w-16 object-contain border border-gray-200 rounded-lg" src="{{ $player->club->logo_url }}" alt="{{ $player->club->name }} logo">
                                            @else
                                                <div class="h-16 w-16 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="text-lg text-gray-500 font-bold">{{ substr($player->club->name, 0, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-lg font-semibold text-gray-900">{{ $player->club->name }}</div>
                                            <div class="text-sm text-gray-600">{{ $player->club->country ?? 'N/A' }}</div>
                                            @if($player->club->league)
                                                <div class="text-sm text-gray-500">{{ $player->club->league }}</div>
                                            @endif
                                            @if($player->contract_valid_until)
                                                <div class="text-sm text-gray-500">Contrat jusqu'au {{ $player->contract_valid_until->format('d/m/Y') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($player->association)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Association Nationale</h3>
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-16 w-16 mr-4">
                                            @if($player->association->association_logo_url)
                                                <img class="h-16 w-16 object-contain border border-gray-200 rounded-lg" src="{{ $player->association->association_logo_url }}" alt="{{ $player->association->name }} logo">
                                            @elseif($player->association->nation_flag_url)
                                                <img class="h-16 w-16 object-contain border border-gray-200 rounded-lg" src="{{ $player->association->nation_flag_url }}" alt="{{ $player->association->country }} flag">
                                            @else
                                                <div class="h-16 w-16 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="text-lg text-gray-500 font-bold">{{ substr($player->association->name, 0, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-lg font-semibold text-gray-900">{{ $player->association->name }}</div>
                                            <div class="text-sm text-gray-600">{{ $player->association->country ?? 'N/A' }}</div>
                                            @if($player->association->confederation)
                                                <div class="text-sm text-gray-500">{{ $player->association->confederation }}</div>
                                            @endif
                                            @if($player->association->fifa_ranking)
                                                <div class="text-sm text-gray-500">Classement FIFA: {{ $player->association->fifa_ranking }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
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

                <!-- Statistiques de Compétition -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Statistiques de Compétition</h2>
                    </div>
                    <div class="p-6">
                        <!-- Current Team Information -->
                        @if($player->currentTeam)
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Équipe Actuelle</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 mr-3">
                                                @if($player->currentTeam->team->club->logo_url)
                                                    <img class="h-12 w-12 object-contain border border-gray-200 rounded-lg" src="{{ $player->currentTeam->team->club->logo_url }}" alt="{{ $player->currentTeam->team->club->name }} logo">
                                                @else
                                                    <div class="h-12 w-12 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                                                        <span class="text-sm text-gray-500 font-bold">{{ substr($player->currentTeam->team->club->name, 0, 2) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-lg font-semibold text-gray-900">{{ $player->currentTeam->team->club->name }}</div>
                                                <div class="text-sm text-gray-600">{{ $player->currentTeam->team->name }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-gray-500">Numéro {{ $player->currentSquadNumber ?? 'N/A' }}</div>
                                            <div class="text-sm font-medium text-gray-900">{{ ucfirst($player->currentRole ?? 'N/A') }}</div>
                                            <div class="text-xs text-gray-500">{{ ucfirst($player->currentTeam->status ?? 'N/A') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Overall Statistics -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-blue-600 uppercase tracking-wider mb-2">Matchs</h4>
                                <div class="text-2xl font-bold text-blue-900">{{ $player->total_matches_played }}</div>
                                <div class="text-sm text-blue-600">{{ $player->total_minutes_played }} minutes</div>
                                <div class="text-xs text-blue-500">{{ $player->minutes_per_match }} min/match</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-green-600 uppercase tracking-wider mb-2">Buts & Passes</h4>
                                <div class="text-2xl font-bold text-green-900">{{ $player->total_goals }}</div>
                                <div class="text-sm text-green-600">{{ $player->total_assists }} passes décisives</div>
                                <div class="text-xs text-green-500">{{ $player->goals_per_match }} buts/match</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-yellow-600 uppercase tracking-wider mb-2">Cartons</h4>
                                <div class="text-2xl font-bold text-yellow-900">{{ $player->total_yellow_cards }}</div>
                                <div class="text-sm text-yellow-600">{{ $player->total_red_cards }} rouges</div>
                                @if($player->position === 'GK')
                                    <div class="text-xs text-yellow-500">{{ $player->total_clean_sheets }} clean sheets</div>
                                @endif
                            </div>
                        </div>

                        <!-- Season Statistics -->
                        @if($player->currentSeasonStats->count() > 0)
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Statistiques par Saison</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compétition</th>
                                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Matchs</th>
                                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Minutes</th>
                                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Buts</th>
                                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Passes</th>
                                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cartons</th>
                                                @if($player->position === 'GK')
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Clean Sheets</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($player->currentSeasonStats as $stat)
                                                <tr>
                                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $stat->competition->name ?? 'N/A' }}</td>
                                                    <td class="px-3 py-2 text-sm text-center text-gray-900">{{ $stat->matches_played }}</td>
                                                    <td class="px-3 py-2 text-sm text-center text-gray-900">{{ $stat->minutes_played }}</td>
                                                    <td class="px-3 py-2 text-sm text-center text-gray-900">{{ $stat->goals }}</td>
                                                    <td class="px-3 py-2 text-sm text-center text-gray-900">{{ $stat->assists }}</td>
                                                    <td class="px-3 py-2 text-sm text-center text-gray-900">
                                                        <span class="inline-flex items-center">
                                                            <span class="text-yellow-600">{{ $stat->yellow_cards }}</span>
                                                            @if($stat->red_cards > 0)
                                                                <span class="ml-1 text-red-600">/ {{ $stat->red_cards }}</span>
                                                            @endif
                                                        </span>
                                                    </td>
                                                    @if($player->position === 'GK')
                                                        <td class="px-3 py-2 text-sm text-center text-gray-900">{{ $stat->clean_sheets }}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Recent Match Events -->
                        @if($player->recentMatchEvents->count() > 0)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Événements Récents</h3>
                                <div class="space-y-3">
                                    @foreach($player->recentMatchEvents as $event)
                                        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 mr-3">
                                                    @if($event->type === 'goal')
                                                        <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                                            <span class="text-white text-xs font-bold">⚽</span>
                                                        </div>
                                                    @elseif($event->type === 'yellow_card')
                                                        <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                                                            <span class="text-white text-xs font-bold">🟨</span>
                                                        </div>
                                                    @elseif($event->type === 'red_card')
                                                        <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                                            <span class="text-white text-xs font-bold">🟥</span>
                                                        </div>
                                                    @elseif($event->type === 'substitution')
                                                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                                            <span class="text-white text-xs font-bold">🔄</span>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-gray-500 rounded-full flex items-center justify-center">
                                                            <span class="text-white text-xs font-bold">•</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ ucfirst($event->type ?? 'event') }} - {{ $event->minute }}' {{ $event->extra_time_minute ? '+' . $event->extra_time_minute : '' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        @if($event->match)
                                                            {{ $event->match->homeTeam->club->name ?? 'N/A' }} vs {{ $event->match->awayTeam->club->name ?? 'N/A' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $event->created_at ? $event->created_at->format('d/m/Y') : 'N/A' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Dossiers médicaux récents -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-800">Dossiers Médicaux Récents</h2>
                            <a href="{{ route('player-registration.health-records', $player) }}" 
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
                                                <h3 class="text-sm font-medium text-gray-900">{{ $record->diagnosis ?? 'Diagnostic non spécifié' }}</h3>
                                                <p class="text-sm text-gray-500">{{ $record->record_date ? $record->record_date->format('d/m/Y') : 'Date non spécifiée' }}</p>
                                            </div>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $record->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                   ($record->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($record->status ?? 'inconnu') }}
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
                                <a href="{{ route('health-records.create', ['player_id' => $player->id]) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    Créer un dossier
                                </a>
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

                <!-- Statistiques Rapides -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Statistiques Rapides</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Matchs joués</span>
                                <span class="text-lg font-semibold text-gray-900">{{ $player->total_matches_played }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Buts marqués</span>
                                <span class="text-lg font-semibold text-green-600">{{ $player->total_goals }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Passes décisives</span>
                                <span class="text-lg font-semibold text-blue-600">{{ $player->total_assists }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Cartons jaunes</span>
                                <span class="text-lg font-semibold text-yellow-600">{{ $player->total_yellow_cards }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Cartons rouges</span>
                                <span class="text-lg font-semibold text-red-600">{{ $player->total_red_cards }}</span>
                            </div>
                            @if($player->position === 'GK')
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Clean sheets</span>
                                    <span class="text-lg font-semibold text-purple-600">{{ $player->total_clean_sheets }}</span>
                                </div>
                            @endif
                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Ratio buts/match</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $player->goals_per_match }}</span>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm text-gray-600">Minutes/match</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $player->minutes_per_match }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Actions Rapides</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <a href="{{ route('health-records.create', ['player_id' => $player->id]) }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nouveau dossier médical
                        </a>
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