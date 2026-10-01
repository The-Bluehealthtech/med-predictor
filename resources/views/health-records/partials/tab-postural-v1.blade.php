@php
    $posturalCatalog = config('postural_assessment');
    $posturalAssessments = $healthRecord->posturalAssessments
        ->sortByDesc('assessment_date');
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Évaluations posturales</h2>
                <p class="text-sm text-gray-500 mt-1">Observations structurées et mesures posturales du joueur.</p>
            </div>
            <button type="button" id="postural-new-toggle"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg">
                Nouvelle évaluation
            </button>
        </div>

        <div class="p-6">
            @if($posturalAssessments->isEmpty())
                <p class="text-sm text-gray-500">Aucune évaluation posturale enregistrée.</p>
            @else
                <div class="space-y-3">
                    @foreach($posturalAssessments as $assessment)
                        <div class="border border-gray-200 rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <div class="font-semibold text-gray-900">
                                    {{ $assessment->type_label }}
                                    <span class="text-sm font-normal text-gray-500">
                                        · {{ $assessment->assessment_date?->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    {{ $assessment->findings->count() }} observation(s)
                                    · {{ $assessment->measurements->count() }} mesure(s)
                                    · {{ ucfirst($assessment->status) }}
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm text-gray-500 mr-2">{{ $assessment->clinician?->name ?? 'Clinicien non renseigné' }}</span>
                                @if($assessment->status === 'draft')
                                    <button type="button"
                                            class="postural-complete px-3 py-1 text-sm bg-blue-600 text-white rounded"
                                            data-url="{{ route('postural-assessments.complete', $assessment) }}">
                                        Terminer
                                    </button>
                                @elseif($assessment->status === 'completed')
                                    <button type="button"
                                            class="postural-validate px-3 py-1 text-sm bg-green-600 text-white rounded"
                                            data-url="{{ route('postural-assessments.validate', $assessment) }}">
                                        Valider
                                    </button>
                                @endif
                                @php
                                    $older = $posturalAssessments->values()->get($loop->index + 1);
                                @endphp
                                @if($older)
                                    <button type="button"
                                            class="postural-compare px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded"
                                            data-url="{{ route('postural-assessments.compare', [$older, $assessment]) }}">
                                        Comparer
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div id="postural-new-panel" class="bg-white rounded-lg shadow-md overflow-hidden hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Nouvelle évaluation posturale</h3>
            <p class="text-sm text-gray-500 mt-1">La nouvelle évaluation est enregistrée comme brouillon.</p>
        </div>

        <form id="postural-v1-form" class="p-6 space-y-6"
              data-store-url="{{ route('postural-assessments.store', $healthRecord) }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type d’évaluation</label>
                    <select name="assessment_type" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                        <option value="baseline">Baseline</option>
                        <option value="routine" selected>Routine</option>
                        <option value="injury">Blessure</option>
                        <option value="follow_up">Suivi</option>
                        <option value="return_to_play">Retour au jeu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="datetime-local" name="assessment_date"
                           value="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                </div>
            </div>

            <div>
                <div class="text-sm font-medium text-gray-700 mb-2">Conditions</div>
                <div class="flex flex-wrap gap-4 text-sm">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="footwear" value="barefoot" checked>
                        Pieds nus
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="footwear" value="shoes">
                        Chaussé
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="pain_during_assessment">
                        Douleur pendant l’évaluation
                    </label>
                </div>
            </div>

            @include('health-records.partials.postural-axis-map')

            <div>
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-800">Observations</h4>
                    <button type="button" id="postural-add-finding"
                            class="text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md">
                        + Ajouter une observation
                    </button>
                </div>
                <div id="postural-findings" class="space-y-3"></div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="font-semibold text-gray-800">Mesures</h4>
                        <p class="text-xs text-gray-500 mt-1">Saisie manuelle possible ; les points graphiques restent facultatifs.</p>
                    </div>
                    <button type="button" id="postural-add-measurement"
                            class="text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md">
                        + Ajouter une mesure
                    </button>
                </div>
                <div id="postural-measurements" class="space-y-3"></div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Impression clinique</label>
                <textarea name="overall_impression" rows="3"
                          class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recommandations</label>
                <textarea name="recommendations" rows="3"
                          class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
            </div>

            <div id="postural-form-errors" class="hidden bg-red-50 border border-red-200 text-red-700 rounded-md p-3 text-sm"></div>

            <div class="flex justify-end gap-3">
                <button type="button" id="postural-cancel"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-semibold">
                    Enregistrer le brouillon
                </button>
            </div>
        </form>
    </div>

    <div id="postural-compare-panel" class="bg-white rounded-lg shadow-md overflow-hidden hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Comparaison longitudinale</h3>
            <button type="button" id="postural-compare-close" class="text-sm text-gray-600">Fermer</button>
        </div>
        <div id="postural-compare-content" class="p-6 text-sm text-gray-700"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const catalog = @json($posturalCatalog);
    const findingsContainer = document.getElementById('postural-findings');
    const panel = document.getElementById('postural-new-panel');
    const form = document.getElementById('postural-v1-form');
    const errors = document.getElementById('postural-form-errors');
    const measurementsContainer = document.getElementById('postural-measurements');
    const comparePanel = document.getElementById('postural-compare-panel');
    const compareContent = document.getElementById('postural-compare-content');

    document.getElementById('postural-new-toggle')?.addEventListener('click', () => panel.classList.toggle('hidden'));
    document.getElementById('postural-cancel')?.addEventListener('click', () => panel.classList.add('hidden'));

    function addFindingRow() {
        const row = document.createElement('div');
        row.className = 'postural-finding-row grid grid-cols-1 md:grid-cols-6 gap-3 border border-gray-200 rounded-lg p-3';

        const findingOptions = Object.entries(catalog.findings || {})
            .map(([key, definition]) => '<option value="' + key + '">' + key.replaceAll('_', ' ') + '</option>')
            .join('');

        row.innerHTML = `
            <select class="finding-key md:col-span-2 border border-gray-300 rounded-md px-2 py-2">
                <option value="">Observation...</option>
                ${findingOptions}
            </select>
            <select class="finding-view border border-gray-300 rounded-md px-2 py-2" disabled>
                <option value="">Vue...</option>
            </select>
            <select class="finding-side border border-gray-300 rounded-md px-2 py-2" disabled>
                <option value="">Côté...</option>
            </select>
            <select class="finding-severity border border-gray-300 rounded-md px-2 py-2">
                <option value="">Sévérité...</option>
                <option value="trace">Trace</option>
                <option value="mild">Légère</option>
                <option value="moderate">Modérée</option>
                <option value="marked">Marquée</option>
            </select>
            <button type="button" class="remove-finding text-red-600 text-sm">Supprimer</button>
            <input type="text" class="finding-notes md:col-span-6 border border-gray-300 rounded-md px-2 py-2"
                   placeholder="Note facultative">
        `;

        const keySelect = row.querySelector('.finding-key');
        const viewSelect = row.querySelector('.finding-view');
        const sideSelect = row.querySelector('.finding-side');

        keySelect.addEventListener('change', function () {
            const definition = catalog.findings[this.value];
            viewSelect.innerHTML = '<option value="">Vue...</option>';
            sideSelect.innerHTML = '<option value="">Côté...</option>';

            if (!definition) {
                viewSelect.disabled = true;
                sideSelect.disabled = true;
                return;
            }

            definition.views.forEach(view => {
                viewSelect.insertAdjacentHTML('beforeend', '<option value="' + view + '">' + view.replaceAll('_', ' ') + '</option>');
            });
            definition.sides.forEach(side => {
                sideSelect.insertAdjacentHTML('beforeend', '<option value="' + side + '">' + side.replaceAll('_', ' ') + '</option>');
            });

            viewSelect.disabled = false;
            sideSelect.disabled = false;

            if (definition.views.length === 1) viewSelect.value = definition.views[0];
            if (definition.sides.length === 1) sideSelect.value = definition.sides[0];
        });

        row.querySelector('.remove-finding').addEventListener('click', () => row.remove());
        findingsContainer.appendChild(row);
    }

    document.getElementById('postural-add-finding')?.addEventListener('click', addFindingRow);

    function addMeasurementRow() {
        const row = document.createElement('div');
        row.className = 'postural-measurement-row grid grid-cols-1 md:grid-cols-6 gap-3 border border-gray-200 rounded-lg p-3';

        const options = Object.entries(catalog.measurements || {})
            .map(([key, definition]) => '<option value="' + key + '">' + key.replaceAll('_', ' ') + '</option>')
            .join('');

        row.innerHTML = `
            <select class="measurement-key md:col-span-2 border border-gray-300 rounded-md px-2 py-2">
                <option value="">Mesure...</option>
                ${options}
            </select>
            <select class="measurement-view border border-gray-300 rounded-md px-2 py-2">
                <option value="anterior">Antérieure</option>
                <option value="posterior">Postérieure</option>
                <option value="left_lateral">Latérale gauche</option>
                <option value="right_lateral">Latérale droite</option>
            </select>
            <select class="measurement-side border border-gray-300 rounded-md px-2 py-2">
                <option value="">Sans côté</option>
                <option value="left">Gauche</option>
                <option value="right">Droite</option>
                <option value="bilateral">Bilatéral</option>
                <option value="midline">Médian</option>
            </select>
            <input type="number" step="0.001" class="measurement-value border border-gray-300 rounded-md px-2 py-2" placeholder="Valeur">
            <input type="text" class="measurement-unit border border-gray-300 rounded-md px-2 py-2 bg-gray-50" readonly placeholder="Unité">
            <button type="button" class="remove-measurement md:col-span-6 justify-self-start text-red-600 text-sm">Supprimer</button>
        `;

        const keySelect = row.querySelector('.measurement-key');
        const unitInput = row.querySelector('.measurement-unit');
        keySelect.addEventListener('change', function () {
            const definition = catalog.measurements[this.value];
            unitInput.value = definition ? definition.unit : '';
        });
        row.querySelector('.remove-measurement').addEventListener('click', () => row.remove());

        measurementsContainer.appendChild(row);
    }

    document.getElementById('postural-add-measurement')?.addEventListener('click', addMeasurementRow);

    document.getElementById('postural-axis-map')?.addEventListener('postural-axis-selected', function (event) {
        addMeasurementRow();
        const rows = document.querySelectorAll('.postural-measurement-row');
        const row = rows[rows.length - 1];
        if (!row) return;

        const detail = event.detail || {};
        const keySelect = row.querySelector('.measurement-key');
        const viewSelect = row.querySelector('.measurement-view');
        const sideSelect = row.querySelector('.measurement-side');

        if (detail.measurement_key && catalog.measurements[detail.measurement_key]) {
            keySelect.value = detail.measurement_key;
            keySelect.dispatchEvent(new Event('change'));
        }

        if (detail.view) viewSelect.value = detail.view;
        if (detail.side) sideSelect.value = detail.side;

        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        row.querySelector('.measurement-value')?.focus();
    });

    async function postAction(url) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
            },
        });

        if (response.ok) {
            window.location.reload();
            return;
        }

        const body = await response.json().catch(() => ({}));
        alert(body.message || 'Action impossible.');
    }

    document.querySelectorAll('.postural-complete').forEach(button => {
        button.addEventListener('click', () => postAction(button.dataset.url));
    });

    document.querySelectorAll('.postural-validate').forEach(button => {
        button.addEventListener('click', () => postAction(button.dataset.url));
    });

    document.querySelectorAll('.postural-compare').forEach(button => {
        button.addEventListener('click', async () => {
            const response = await fetch(button.dataset.url, { headers: { 'Accept': 'application/json' } });
            const body = await response.json();
            if (!response.ok) {
                alert(body.message || 'Comparaison impossible.');
                return;
            }

            const findingRows = (body.data.finding_changes || []).map(change => {
                const before = change.before ? [change.before.severity, change.before.value, change.before.unit].filter(Boolean).join(' ') : '—';
                const after = change.after ? [change.after.severity, change.after.value, change.after.unit].filter(Boolean).join(' ') : '—';
                return '<tr><td class="py-2 pr-4">' + change.key + '</td><td class="py-2 pr-4">' + before + '</td><td class="py-2">' + after + '</td></tr>';
            }).join('');

            const measurementRows = (body.data.measurement_changes || []).map(change => {
                const before = change.before && change.before.value !== null ? change.before.value + ' ' + (change.before.unit || '') : '—';
                const after = change.after && change.after.value !== null ? change.after.value + ' ' + (change.after.unit || '') : '—';
                return '<tr><td class="py-2 pr-4">' + change.key + '</td><td class="py-2 pr-4">' + before + '</td><td class="py-2">' + after + '</td></tr>';
            }).join('');

            compareContent.innerHTML = `
                <div class="space-y-6">
                    <div>
                        <h4 class="font-semibold mb-2">Observations</h4>
                        <table class="w-full"><thead><tr class="text-left border-b"><th>Élément</th><th>Avant</th><th>Après</th></tr></thead><tbody>${findingRows || '<tr><td colspan="3" class="py-3 text-gray-500">Aucune observation comparable.</td></tr>'}</tbody></table>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2">Mesures</h4>
                        <table class="w-full"><thead><tr class="text-left border-b"><th>Mesure</th><th>Avant</th><th>Après</th></tr></thead><tbody>${measurementRows || '<tr><td colspan="3" class="py-3 text-gray-500">Aucune mesure comparable.</td></tr>'}</tbody></table>
                    </div>
                </div>
            `;
            comparePanel.classList.remove('hidden');
        });
    });

    document.getElementById('postural-compare-close')?.addEventListener('click', () => comparePanel.classList.add('hidden'));

    form?.addEventListener('submit', async function (event) {
        event.preventDefault();
        errors.classList.add('hidden');
        errors.textContent = '';

        const data = new FormData(form);
        const findings = [];

        document.querySelectorAll('.postural-finding-row').forEach(row => {
            const key = row.querySelector('.finding-key').value;
            if (!key || !catalog.findings[key]) return;

            findings.push({
                finding_key: key,
                region: catalog.findings[key].region,
                view: row.querySelector('.finding-view').value,
                side: row.querySelector('.finding-side').value,
                severity: row.querySelector('.finding-severity').value || null,
                source: 'clinician',
                notes: row.querySelector('.finding-notes').value || null,
            });
        });

        const measurements = [];
        document.querySelectorAll('.postural-measurement-row').forEach(row => {
            const key = row.querySelector('.measurement-key').value;
            const definition = catalog.measurements[key];
            const rawValue = row.querySelector('.measurement-value').value;
            if (!key || !definition || rawValue === '') return;

            measurements.push({
                measurement_key: key,
                measurement_type: definition.type,
                view: row.querySelector('.measurement-view').value,
                side: row.querySelector('.measurement-side').value || null,
                value: Number(rawValue),
                unit: definition.unit,
                points: null,
                metadata: { source: 'manual' },
            });
        });

        const payload = {
            assessment_type: data.get('assessment_type'),
            assessment_date: data.get('assessment_date'),
            context: {
                conditions: {
                    footwear: data.get('footwear'),
                    stance: 'natural',
                    pain_during_assessment: data.get('pain_during_assessment') === 'on',
                },
            },
            findings,
            measurements,
            overall_impression: data.get('overall_impression') || null,
            recommendations: data.get('recommendations') || null,
        };

        const response = await fetch(form.dataset.storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify(payload),
        });

        if (response.ok) {
            window.location.reload();
            return;
        }

        const body = await response.json().catch(() => ({}));
        const messages = body.errors
            ? Object.values(body.errors).flat().join(' ')
            : (body.message || 'Impossible d’enregistrer l’évaluation posturale.');

        errors.textContent = messages;
        errors.classList.remove('hidden');
    });
});
</script>
