@extends('layouts.app')

@section('title', 'Digital Twin - Simulation')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🔄 Digital Twin — scénario what-if</h1>
            <p class="text-sm text-gray-600">
                Simulation mathématique basée sur la dernière performance réellement enregistrée.
            </p>
        </div>
        <a href="{{ route('analytics.dashboard') }}" class="text-blue-600 hover:text-blue-800">← Analytics</a>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-900">
        Les résultats ci-dessous sont des <strong>scénarios simulés</strong>, pas des mesures,
        pas un diagnostic médical et pas le score FIT canonique.
        L'ajustement applique simplement le pourcentage choisi aux scores disponibles.
    </div>

    <form method="GET" action="{{ route('analytics.digital-twin') }}" class="bg-white rounded-lg shadow p-5 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700">Joueur</label>
            <select name="player_id" required class="mt-1 w-full rounded border-gray-300">
                <option value="">Sélectionner</option>
                @foreach($players as $player)
                    <option value="{{ $player->id }}" @selected($selectedPlayer?->id === $player->id)>
                        {{ trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? '')) ?: ('Joueur #' . $player->id) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Ajustement du scénario (%)</label>
            <input type="number" name="adjustment_pct" min="-20" max="20" step="0.5"
                   value="{{ request('adjustment_pct', 0) }}"
                   class="mt-1 w-full rounded border-gray-300">
            <p class="text-xs text-gray-500 mt-1">Plage autorisée : -20 % à +20 %.</p>
        </div>
        <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
            Calculer le scénario
        </button>
    </form>

    @if($selectedPlayer)
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-gray-900">
                Baseline : {{ trim(($selectedPlayer->first_name ?? '') . ' ' . ($selectedPlayer->last_name ?? '')) }}
            </h2>

            @if(!$baseline)
                <p class="mt-3 text-sm text-gray-500">
                    Aucune performance réelle n'est disponible pour ce joueur. Aucun scénario n'est généré.
                </p>
            @else
                <p class="mt-1 text-sm text-gray-500">
                    Performance du {{ $baseline->performance_date?->format('d/m/Y') ?? 'date inconnue' }}.
                </p>

                <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mt-5">
                    @foreach([
                        'Global' => 'overall_performance_score',
                        'Physique' => 'physical_score',
                        'Technique' => 'technical_score',
                        'Tactique' => 'tactical_score',
                        'Mental' => 'mental_score',
                        'Social' => 'social_score',
                    ] as $label => $field)
                        <div class="rounded border p-3">
                            <div class="text-xs text-gray-500">{{ $label }}</div>
                            <div class="text-xl font-bold">{{ $baseline->{$field} ?? 'N/A' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if($scenario)
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-gray-900">
                Scénario simulé : {{ $scenario['adjustment_pct'] >= 0 ? '+' : '' }}{{ $scenario['adjustment_pct'] }} %
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mt-5">
                @foreach([
                    'Global' => 'overall',
                    'Physique' => 'physical',
                    'Technique' => 'technical',
                    'Tactique' => 'tactical',
                    'Mental' => 'mental',
                    'Social' => 'social',
                ] as $label => $key)
                    <div class="rounded border border-indigo-200 bg-indigo-50 p-3">
                        <div class="text-xs text-indigo-700">{{ $label }}</div>
                        <div class="text-xl font-bold text-indigo-900">{{ $scenario['scores'][$key] ?? 'N/A' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
