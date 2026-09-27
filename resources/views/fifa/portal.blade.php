@extends('layouts.app')

@section('title', 'FIFA Portal - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">FIFA Portal</h1>
                <p class="text-sm text-gray-600">{{ __('Données locales traçables et état de l\'intégration FIFA Connect') }}</p>
            </div>
            <a href="{{ route('modules.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                {{ __('← Retour aux modules') }}
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">FIFA Connect API</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $connectivity['message'] ?? 'État indisponible.' }}
                    </p>
                </div>
                <div>
                    @if(($connectivity['status'] ?? null) === 'online')
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            {{ __('fifa.connected') }}
                        </span>
                    @elseif(($connectivity['status'] ?? null) === 'unconfigured')
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            {{ __('Non configuré') }}
                        </span>
                    @elseif(($connectivity['status'] ?? null) === 'mock')
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">{{ __('Simulation explicite — non connectée') }}</span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            Hors ligne
                        </span>
                    @endif
                </div>
            </div>

            <dl class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">{{ __('clinical.table_status') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $connectivity['status'] ?? 'unknown' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Connexion live vérifiée') }}</dt>
                    <dd class="font-medium text-gray-900">{{ !empty($connectivity['connected']) ? 'Oui' : 'Non' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Dernière vérification') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $connectivity['timestamp'] ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Joueurs disponibles localement') }}</h2>

            <form method="GET" action="{{ route('fifa.portal.integrated') }}" class="flex flex-col md:flex-row gap-3">
                <select name="player_id" class="flex-1 rounded-md border-gray-300">
                    <option value="">{{ __('competitions.discipline_sanctions_page.select_player_placeholder') }}</option>
                    @foreach($players as $player)
                        <option value="{{ $player->id }}" @selected($selectedPlayer?->id === $player->id)>
                            {{ trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? '')) ?: ($player->name ?? ('Joueur #' . $player->id)) }}
                            @if($player->fifa_connect_id)
                                — FIFA ID {{ $player->fifa_connect_id }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Afficher
                </button>
            </form>

            @if($players->isEmpty())
                <p class="mt-4 text-sm text-gray-600">{{ __('Aucun joueur n\'est disponible dans le périmètre autorisé.') }}</p>
            @endif
        </div>

        @if($selectedPlayer)
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ trim(($selectedPlayer->first_name ?? '') . ' ' . ($selectedPlayer->last_name ?? '')) ?: ($selectedPlayer->name ?? ('Joueur #' . $selectedPlayer->id)) }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Les valeurs ci-dessous proviennent de l\'enregistrement local du joueur. Aucun fallback numérique n\'est généré.') }}</p>

                <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <dt class="text-sm text-gray-500">FIFA Connect ID</dt>
                        <dd class="font-medium text-gray-900">{{ $selectedPlayer->fifa_connect_id ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Club</dt>
                        <dd class="font-medium text-gray-900">{{ $selectedPlayer->club?->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Association</dt>
                        <dd class="font-medium text-gray-900">{{ $selectedPlayer->association?->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('health_records.show_page.nationality_label') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $selectedPlayer->nationality ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Position</dt>
                        <dd class="font-medium text-gray-900">{{ $selectedPlayer->position ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Note globale FIFA</dt>
                        <dd class="font-medium text-gray-900">{{ $selectedPlayer->overall_rating ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Potentiel FIFA</dt>
                        <dd class="font-medium text-gray-900">{{ $selectedPlayer->potential_rating ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('competitions.ranking.last_update') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $selectedPlayer->updated_at?->toISOString() ?? 'N/A' }}</dd>
                    </div>
                </dl>

                @if(!$selectedPlayer->fifa_connect_id)
                    <div class="mt-6 p-4 rounded-md bg-yellow-50 text-yellow-800 text-sm">{{ __('Synchronisation FIFA Connect indisponible : aucun FIFA Connect ID n\'est associé à ce joueur.') }}</div>
                @elseif(empty($connectivity['connected']))
                    <div class="mt-6 p-4 rounded-md bg-yellow-50 text-yellow-800 text-sm">{{ __('Synchronisation live non exécutée : l\'API FIFA Connect n\'est pas actuellement connectée.') }}</div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
