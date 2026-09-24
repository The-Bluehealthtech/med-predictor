@extends('layouts.app')

@section('title', 'Métriques FIT canoniques')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-3xl font-bold text-gray-900">
                Métriques FIT canoniques
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Données traçables utilisées par le calcul du Score FIT.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form method="GET"
                  action="{{ route('performances.fit-metrics') }}"
                  class="max-w-xl">
                <label for="player_id"
                       class="block text-sm font-medium text-gray-700">
                    Joueur
                </label>

                <select name="player_id"
                        id="player_id"
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">Sélectionner un joueur</option>

                    @foreach($players as $player)
                        <option value="{{ $player->id }}"
                            @selected(
                                $selectedPlayer
                                && $selectedPlayer->id === $player->id
                            )>
                            {{
                                trim(
                                    ($player->first_name ?? '')
                                    . ' '
                                    . ($player->last_name ?? '')
                                )
                                ?: ($player->name ?? 'Joueur #' . $player->id)
                            }}
                            @if($player->fifa_connect_id)
                                — {{ $player->fifa_connect_id }}
                            @endif
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                        class="mt-4 px-4 py-2 rounded-md bg-blue-600 text-white">
                    Afficher les métriques
                </button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Catalogue FIT accepté
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($catalog as $axis => $definitions)
                    <div class="border rounded-lg p-4">
                        <h3 class="font-semibold capitalize mb-3">
                            {{ $axis }}
                        </h3>

                        <ul class="space-y-2 text-sm">
                            @foreach($definitions as $name => $config)
                                <li>
                                    <span class="font-medium">
                                        {{ $name }}
                                    </span>

                                    <span class="text-gray-500">
                                        —
                                        @if(($config['scale'] ?? null) === 'percentage')
                                            0–100 %
                                        @elseif(($config['scale'] ?? null) === 'score_10')
                                            score 0–10
                                        @else
                                            % ou échelle explicite
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>

        @if($selectedPlayer)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Historique des métriques
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Vérification autorisée :
                        {{ $canVerify ? 'oui' : 'non' }}
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">
                                    Date
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">
                                    Axe
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">
                                    Métrique
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">
                                    Valeur
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">
                                    Source
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">
                                    FIT
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">
                                    Vérification
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($metrics as $metric)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                        {{ $metric->measurement_date?->format('Y-m-d H:i') }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $metric->metric_type }}
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ $metric->metric_name }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $metric->metric_value }}
                                        {{ $metric->metric_unit }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $metric->data_source }}
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        {{ $metric->fit_eligible ? 'Acceptée' : 'Non éligible' }}
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        @if($metric->is_verified)
                                            Vérifiée
                                        @else
                                            En attente
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7"
                                        class="px-4 py-8 text-center text-sm text-gray-500">
                                        Aucune métrique enregistrée pour ce joueur.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($metrics->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $metrics->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
