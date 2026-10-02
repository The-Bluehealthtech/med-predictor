@extends('layouts.app')

@section('title', 'Demande de licence joueur')

@php
    $genders = config('licensing.genders');
    $disciplines = config('licensing.disciplines');
    $levels = config('licensing.levels');
    $natures = config('licensing.natures');
    $firstSeason = array_key_first($seasons);
@endphp

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <x-page-header
        title="Licence de joueur"
        :subtitle="trim($player->first_name . ' ' . $player->last_name) . ($player->club ? ' — ' . $player->club->name : '')"
        eyebrow="Licences · étape 2 sur 3"
        :back-href="route('modules.licenses.index')"
        back-label="Retour aux demandes"
    />

    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="mb-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Votre objectif</p>
        <p class="mt-1 font-semibold text-slate-900">Préparer puis envoyer la demande à la fédération</p>
        <p class="mt-1 text-sm text-slate-600">Vérifiez les informations de licence, ajoutez les pièces demandées et envoyez le dossier. Les exigences médicales et documentaires s'adaptent automatiquement au joueur.</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        @unless($player->fifa_connect_id)
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                <strong>Identité internationale non vérifiable pour le moment.</strong>
                La demande peut être préparée, mais la fédération ne pourra pas effectuer la vérification FIFA ID tant que l'identifiant du joueur n'est pas renseigné.
            </div>
        @endunless

        <form method="POST" action="{{ route('player-licenses.request.store', $player) }}" enctype="multipart/form-data" class="space-y-5" id="license-request">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block text-sm"><span class="font-semibold text-slate-700">Saison</span>
                    <select name="season" id="season" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($seasons as $label => $season)
                            <option value="{{ $label }}" @selected(old('season', $firstSeason) === $label)>{{ $label }} (du {{ $season['start']->format('d/m/Y') }} au {{ $season['end']->format('d/m/Y') }})</option>
                        @endforeach
                    </select></label>
                <label class="block text-sm"><span class="font-semibold text-slate-700">Genre</span>
                    @if($player->gender && isset($genders[$player->gender]))
                        <input type="text" value="{{ $genders[$player->gender] }}" disabled class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                    @else
                        <select name="gender" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="">À renseigner (obligatoire FIFA Connect)</option>
                            @foreach($genders as $value => $label)<option value="{{ $value }}" @selected(old('gender') === $value)>{{ $label }}</option>@endforeach
                        </select>
                    @endif
                </label>
            </div>

            <div class="border-t border-slate-100 pt-5">
                <h2 class="text-base font-semibold text-slate-900">Type de licence</h2>
                <p class="mt-1 text-sm text-slate-500">Choisissez uniquement ce qui correspond à la situation du joueur.</p>
            </div>

            <fieldset>
                <legend class="text-sm font-semibold text-slate-700">Discipline</legend>
                <div class="mt-2 grid gap-2 sm:grid-cols-3">
                    @foreach($disciplines as $value => $label)
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 has-[:checked]:border-slate-900 has-[:checked]:ring-1 has-[:checked]:ring-slate-900">
                            <input type="radio" name="discipline" value="{{ $value }}" required @checked(old('discipline', 'Football') === $value)> {{ $label }}</label>
                    @endforeach
                </div>
            </fieldset>

            <div id="category" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700" aria-live="polite"></div>

            <div class="grid gap-4 sm:grid-cols-2">
                <fieldset>
                    <legend class="text-sm font-semibold text-slate-700">Niveau</legend>
                    <div class="mt-2 grid gap-2">
                        @foreach($levels as $value => $label)
                            <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm" data-level="{{ $value }}">
                                <input type="radio" name="level" value="{{ $value }}" required @checked(old('level', 'amateur') === $value)> <span>{{ $label }}</span> <span class="ml-auto text-xs text-slate-500" data-fee></span></label>
                        @endforeach
                    </div>
                </fieldset>
                <fieldset>
                    <legend class="text-sm font-semibold text-slate-700">Nature de l'enregistrement</legend>
                    <div class="mt-2 grid gap-2">
                        @foreach($natures as $value => $label)
                            <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm"><input type="radio" name="registration_nature" value="{{ $value }}" required @checked(old('registration_nature', 'Registration') === $value)> {{ $label }}</label>
                        @endforeach
                    </div>
                </fieldset>
            </div>

            <div id="pcma-notice" class="hidden rounded-xl border px-3 py-2 text-sm" data-pcma-state="{{ $pcmaStatus['state'] }}">
                <p class="font-semibold">Aptitude médicale (PCMA) exigée</p>
                <p class="text-xs" data-pcma-reason></p>
                <p class="mt-1 text-xs">État du joueur : <strong>{{ $pcmaStatus['label'] }}</strong>{{ $pcmaStatus['date'] ? ' (bilan du ' . $pcmaStatus['date']->format('d/m/Y') . ')' : '' }}.
                    @unless($pcmaStatus['valid']) Vous pouvez envoyer la demande, mais la fédération ne pourra l'approuver qu'avec un PCMA signé « apte ».@endunless</p>
            </div>

            <fieldset class="rounded-xl border border-slate-200 p-4">
                <legend class="px-1 text-sm font-semibold text-slate-700">Pièces justificatives</legend>
                <p class="mb-3 text-xs text-slate-500">PDF, JPG ou PNG, {{ (int) (config('licensing.max_kilobytes') / 1024) }} Mo maximum par fichier. Les pièces « exigée » dépendent de la catégorie d'âge, du niveau et de la nature (barème de la fédération).</p>
                <div class="grid gap-3">
                    @foreach($documents as $type => $label)
                        <label class="grid gap-1 sm:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)] sm:items-center" data-doc="{{ $type }}">
                            <span class="text-sm text-slate-800">{{ $label }} <span class="doc-required ml-1 hidden rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">exigée</span></span>
                            <input type="file" name="documents[{{ $type }}]" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold">
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <label class="block text-sm"><span class="font-semibold text-slate-700">Notes pour la fédération <span class="font-normal text-slate-500">(facultatif)</span></span>
                <textarea name="notes" rows="3" maxlength="2000" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea></label>

            <div class="flex gap-3">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Envoyer la demande à la fédération</button>
                <a href="{{ route('modules.licenses.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Barème de la fédération pour ce joueur : [genre][saison][discipline][niveau].
    (function () {
        const rules = @json($rules);
        const natureDocs = @json(config('licensing.nature_documents'));
        const pcmaValid = @json($pcmaStatus['valid']);
        const form = document.getElementById('license-request');
        const val = (name) => (form.querySelector(`[name="${name}"]:checked`) || form.querySelector(`[name="${name}"]`) || {}).value;
        const money = (r) => r.fee === null ? 'tarif non défini' : new Intl.NumberFormat('fr-FR', { style: 'currency', currency: r.currency }).format(r.fee);
        const sync = () => {
            const gender = val('gender') || @json($player->gender) || Object.keys(rules)[0];
            const byLevel = (((rules[gender] || {})[val('season')] || {})[val('discipline')]) || {};
            const r = byLevel[val('level')];
            const any = Object.values(byLevel)[0];
            if (any) {
                document.getElementById('category').textContent = 'Catégorie ' + any.category_label + (any.age_unknown ? ' — date de naissance inconnue : règles senior appliquées par précaution.' : ' — ' + any.age + ' ans au ' + new Date(any.reference_date).toLocaleDateString('fr-FR') + ' (date de référence du barème).');
            }
            form.querySelectorAll('[data-level]').forEach((row) => {
                const lr = byLevel[row.dataset.level];
                const input = row.querySelector('input');
                input.disabled = !lr || !lr.allowed;
                row.classList.toggle('opacity-50', input.disabled);
                row.querySelector('[data-fee]').textContent = lr ? (lr.allowed ? money(lr) : 'non autorisé en ' + lr.category_label) : '';
                if (input.disabled && input.checked) input.checked = false;
            });
            const docs = r ? r.documents.concat(natureDocs[val('registration_nature')] || []) : [];
            form.querySelectorAll('[data-doc]').forEach((row) => {
                const needed = docs.includes(row.dataset.doc);
                row.querySelector('.doc-required').classList.toggle('hidden', !needed);
                row.querySelector('input[type=file]').required = needed;
            });
            const notice = document.getElementById('pcma-notice');
            notice.classList.toggle('hidden', !(r && r.pcma_required));
            notice.classList.remove('border-emerald-200', 'bg-emerald-50', 'border-amber-200', 'bg-amber-50');
            notice.classList.add(...(pcmaValid ? ['border-emerald-200', 'bg-emerald-50'] : ['border-amber-200', 'bg-amber-50']));
            if (r) notice.querySelector('[data-pcma-reason]').textContent = r.pcma_reason;
        };
        form.addEventListener('change', sync);
        sync();
    })();
</script>
@endpush
