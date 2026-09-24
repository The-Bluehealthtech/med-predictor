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
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    Enregistrer une métrique FIT
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Saisie manuelle. Toute nouvelle métrique reste non vérifiée
                    jusqu'à une vérification séparée.
                </p>

                <form id="fit-metric-recording-form"
                      class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="fit_metric_type"
                               class="block text-sm font-medium text-gray-700">
                            Axe FIT
                        </label>
                        <select id="fit_metric_type"
                                required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Sélectionner un axe</option>
                            @foreach($catalog as $axis => $definitions)
                                <option value="{{ $axis }}">{{ $axis }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="fit_metric_name"
                               class="block text-sm font-medium text-gray-700">
                            Métrique
                        </label>
                        <select id="fit_metric_name"
                                required
                                disabled
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Sélectionner d'abord un axe</option>
                        </select>
                    </div>

                    <div>
                        <label for="fit_metric_value"
                               class="block text-sm font-medium text-gray-700">
                            Valeur mesurée
                        </label>
                        <input id="fit_metric_value"
                               type="number"
                               step="any"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label for="fit_metric_unit"
                               class="block text-sm font-medium text-gray-700">
                            Unité
                        </label>
                        <input id="fit_metric_unit"
                               type="text"
                               maxlength="50"
                               required
                               readonly
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50">
                    </div>

                    <div id="fit_social_mode_container"
                         class="hidden">
                        <label for="fit_social_mode"
                               class="block text-sm font-medium text-gray-700">
                            Mode de mesure sociale
                        </label>
                        <select id="fit_social_mode"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Sélectionner un mode</option>
                            <option value="percentage">Pourcentage (%)</option>
                            <option value="explicit">Échelle explicite</option>
                        </select>
                    </div>

                    <div id="fit_scale_min_container"
                         class="hidden">
                        <label for="fit_scale_min"
                               class="block text-sm font-medium text-gray-700">
                            Minimum de l'échelle
                        </label>
                        <input id="fit_scale_min"
                               type="number"
                               step="any"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div id="fit_scale_max_container"
                         class="hidden">
                        <label for="fit_scale_max"
                               class="block text-sm font-medium text-gray-700">
                            Maximum de l'échelle
                        </label>
                        <input id="fit_scale_max"
                               type="number"
                               step="any"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label for="fit_measurement_date"
                               class="block text-sm font-medium text-gray-700">
                            Date et heure de mesure
                        </label>
                        <input id="fit_measurement_date"
                               type="datetime-local"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label for="fit_confidence_score"
                               class="block text-sm font-medium text-gray-700">
                            Niveau de confiance (0 à 1)
                        </label>
                        <input id="fit_confidence_score"
                               type="number"
                               min="0"
                               max="1"
                               step="0.01"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="md:col-span-2">
                        <label for="fit_notes"
                               class="block text-sm font-medium text-gray-700">
                            Notes
                        </label>
                        <textarea id="fit_notes"
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div class="md:col-span-2 text-sm text-gray-500">
                        Source : <span class="font-medium">manual</span>
                        (saisie humaine)
                    </div>

                    <div id="fit_metric_form_message"
                         class="hidden md:col-span-2 p-3 rounded-md text-sm whitespace-pre-line"
                         role="alert"></div>

                    <div class="md:col-span-2">
                        <button id="fit_metric_submit"
                                type="submit"
                                class="px-4 py-2 rounded-md bg-blue-600 text-white disabled:opacity-50">
                            Enregistrer la métrique
                        </button>
                    </div>
                </form>
            </div>

            <script>
                (() => {
                    const catalog = @json($catalog);
                    const endpoint = @json(
                        route(
                            'api.fit.performance-metrics.store',
                            ['player' => $selectedPlayer->id],
                            false
                        )
                    );
                    const csrfToken = @json(csrf_token());

                    const form = document.getElementById(
                        'fit-metric-recording-form'
                    );
                    const typeInput = document.getElementById(
                        'fit_metric_type'
                    );
                    const nameInput = document.getElementById(
                        'fit_metric_name'
                    );
                    const valueInput = document.getElementById(
                        'fit_metric_value'
                    );
                    const unitInput = document.getElementById(
                        'fit_metric_unit'
                    );
                    const socialModeContainer = document.getElementById(
                        'fit_social_mode_container'
                    );
                    const socialModeInput = document.getElementById(
                        'fit_social_mode'
                    );
                    const scaleMinContainer = document.getElementById(
                        'fit_scale_min_container'
                    );
                    const scaleMaxContainer = document.getElementById(
                        'fit_scale_max_container'
                    );
                    const scaleMinInput = document.getElementById(
                        'fit_scale_min'
                    );
                    const scaleMaxInput = document.getElementById(
                        'fit_scale_max'
                    );
                    const measurementDateInput = document.getElementById(
                        'fit_measurement_date'
                    );
                    const confidenceInput = document.getElementById(
                        'fit_confidence_score'
                    );
                    const notesInput = document.getElementById(
                        'fit_notes'
                    );
                    const message = document.getElementById(
                        'fit_metric_form_message'
                    );
                    const submit = document.getElementById(
                        'fit_metric_submit'
                    );

                    const showMessage = (text, error = false) => {
                        message.textContent = text;
                        message.classList.remove(
                            'hidden',
                            'bg-red-50',
                            'text-red-700',
                            'bg-green-50',
                            'text-green-700'
                        );
                        message.classList.add(
                            error ? 'bg-red-50' : 'bg-green-50',
                            error ? 'text-red-700' : 'text-green-700'
                        );
                    };

                    const clearScaleBounds = () => {
                        valueInput.removeAttribute('min');
                        valueInput.removeAttribute('max');
                    };

                    const resetSocialFields = () => {
                        socialModeInput.value = '';
                        scaleMinInput.value = '';
                        scaleMaxInput.value = '';

                        socialModeContainer.classList.add('hidden');
                        scaleMinContainer.classList.add('hidden');
                        scaleMaxContainer.classList.add('hidden');

                        socialModeInput.required = false;
                        scaleMinInput.required = false;
                        scaleMaxInput.required = false;
                    };

                    const selectedConfig = () => {
                        const axis = typeInput.value;
                        const metric = nameInput.value;

                        return catalog[axis]?.[metric] ?? null;
                    };

                    const configureMetric = () => {
                        resetSocialFields();
                        clearScaleBounds();

                        unitInput.value = '';
                        unitInput.readOnly = true;
                        unitInput.classList.add('bg-gray-50');

                        const config = selectedConfig();

                        if (!config) {
                            return;
                        }

                        if (config.scale === 'percentage') {
                            unitInput.value = config.unit;
                            valueInput.min = '0';
                            valueInput.max = '100';
                            return;
                        }

                        if (config.scale === 'score_10') {
                            unitInput.value = config.unit;
                            valueInput.min = '0';
                            valueInput.max = '10';
                            return;
                        }

                        if (config.scale === 'explicit') {
                            socialModeContainer.classList.remove('hidden');
                            socialModeInput.required = true;
                        }
                    };

                    typeInput.addEventListener('change', () => {
                        nameInput.innerHTML =
                            '<option value="">Sélectionner une métrique</option>';

                        const definitions = catalog[typeInput.value];

                        if (!definitions) {
                            nameInput.disabled = true;
                            configureMetric();
                            return;
                        }

                        Object.keys(definitions).forEach((name) => {
                            const option = document.createElement('option');
                            option.value = name;
                            option.textContent = name;
                            nameInput.appendChild(option);
                        });

                        nameInput.disabled = false;
                        configureMetric();
                    });

                    nameInput.addEventListener(
                        'change',
                        configureMetric
                    );

                    socialModeInput.addEventListener('change', () => {
                        scaleMinInput.value = '';
                        scaleMaxInput.value = '';
                        scaleMinContainer.classList.add('hidden');
                        scaleMaxContainer.classList.add('hidden');

                        scaleMinInput.required = false;
                        scaleMaxInput.required = false;

                        clearScaleBounds();

                        if (socialModeInput.value === 'percentage') {
                            unitInput.value = '%';
                            unitInput.readOnly = true;
                            unitInput.classList.add('bg-gray-50');
                            valueInput.min = '0';
                            valueInput.max = '100';
                            return;
                        }

                        if (socialModeInput.value === 'explicit') {
                            unitInput.value = '';
                            unitInput.readOnly = false;
                            unitInput.classList.remove('bg-gray-50');

                            scaleMinContainer.classList.remove('hidden');
                            scaleMaxContainer.classList.remove('hidden');

                            scaleMinInput.required = true;
                            scaleMaxInput.required = true;
                            return;
                        }

                        unitInput.value = '';
                        unitInput.readOnly = true;
                        unitInput.classList.add('bg-gray-50');
                    });

                    form.addEventListener('submit', async (event) => {
                        event.preventDefault();

                        message.classList.add('hidden');

                        if (!form.reportValidity()) {
                            return;
                        }

                        const config = selectedConfig();

                        if (!config) {
                            showMessage(
                                'La métrique sélectionnée ne fait pas partie du catalogue FIT.',
                                true
                            );
                            return;
                        }

                        const payload = {
                            metric_type: typeInput.value,
                            metric_name: nameInput.value,
                            metric_value: Number(valueInput.value),
                            metric_unit: unitInput.value,
                            measurement_date: new Date(
                                measurementDateInput.value
                            ).toISOString(),
                            data_source: 'manual',
                            confidence_score: Number(
                                confidenceInput.value
                            ),
                            notes: notesInput.value || null,
                            metadata: null,
                        };

                        if (config.scale === 'explicit') {
                            if (!socialModeInput.value) {
                                showMessage(
                                    'Sélectionnez le mode de mesure sociale.',
                                    true
                                );
                                return;
                            }

                            if (socialModeInput.value === 'explicit') {
                                const scaleMin = Number(
                                    scaleMinInput.value
                                );
                                const scaleMax = Number(
                                    scaleMaxInput.value
                                );

                                if (scaleMax <= scaleMin) {
                                    showMessage(
                                        'Le maximum de l’échelle doit être supérieur au minimum.',
                                        true
                                    );
                                    return;
                                }

                                payload.metadata = {
                                    scale_min: scaleMin,
                                    scale_max: scaleMax,
                                };
                            }
                        }

                        submit.disabled = true;

                        try {
                            const response = await fetch(endpoint, {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify(payload),
                            });

                            const body = await response.json()
                                .catch(() => ({}));

                            if (!response.ok) {
                                const errors = body.errors
                                    ? Object.values(body.errors).flat()
                                    : [];

                                showMessage(
                                    errors.length
                                        ? errors.join('\n')
                                        : (
                                            body.message
                                            ?? 'Enregistrement refusé.'
                                        ),
                                    true
                                );
                                return;
                            }

                            window.location.reload();
                        } catch (error) {
                            showMessage(
                                'La requête n’a pas pu être envoyée.',
                                true
                            );
                        } finally {
                            submit.disabled = false;
                        }
                    });
                })();
            </script>

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
                                            <span>Vérifiée</span>
                                        @else
                                            <span>En attente</span>

                                            @if($canVerify && $metric->fit_eligible)
                                                <button
                                                    type="button"
                                                    class="fit-verify-button ml-3 px-3 py-1 rounded-md bg-green-600 text-white text-xs disabled:opacity-50"
                                                    data-verify-url="{{ route(
                                                        'api.fit.performance-metrics.verify',
                                                        [
                                                            'player' => $selectedPlayer->id,
                                                            'metric' => $metric->id,
                                                        ],
                                                        false
                                                    ) }}"
                                                >
                                                    Vérifier
                                                </button>

                                                <span
                                                    class="fit-verify-message ml-2 text-xs"
                                                    aria-live="polite"
                                                ></span>
                                            @endif
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

            @if($canVerify)
                <script>
                    (() => {
                        const csrfToken = @json(csrf_token());

                        document.querySelectorAll(
                            '.fit-verify-button'
                        ).forEach((button) => {
                            button.addEventListener(
                                'click',
                                async () => {
                                    const message =
                                        button.parentElement.querySelector(
                                            '.fit-verify-message'
                                        );

                                    button.disabled = true;
                                    message.textContent = '';

                                    try {
                                        const response = await fetch(
                                            button.dataset.verifyUrl,
                                            {
                                                method: 'POST',
                                                credentials: 'same-origin',
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'X-CSRF-TOKEN': csrfToken,
                                                    'X-Requested-With':
                                                        'XMLHttpRequest',
                                                },
                                            }
                                        );

                                        const body = await response.json()
                                            .catch(() => ({}));

                                        if (!response.ok) {
                                            message.textContent =
                                                body.message
                                                ?? 'Vérification refusée.';
                                            message.classList.add(
                                                'text-red-600'
                                            );
                                            button.disabled = false;
                                            return;
                                        }

                                        window.location.reload();
                                    } catch (error) {
                                        message.textContent =
                                            'La requête n’a pas pu être envoyée.';
                                        message.classList.add(
                                            'text-red-600'
                                        );
                                        button.disabled = false;
                                    }
                                }
                            );
                        });
                    })();
                </script>
            @endif
        @endif
    </div>
</div>
@endsection
