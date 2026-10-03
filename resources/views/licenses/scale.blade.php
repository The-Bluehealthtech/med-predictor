@extends('layouts.app')

@section('title', 'Barème des licences - FIT Platform')

@php
    $genders = config('licensing.genders');
    $disciplines = config('licensing.disciplines');
    $levels = config('licensing.levels');
    $documents = config('licensing.documents');
    $rows = $categories->sortBy(fn ($c) => $c->max_age ?? PHP_INT_MAX)->values()->map(fn ($c) => $c->toArray())->all();
    $rows[] = ['code' => '', 'label' => '', 'max_age' => null, 'allowed_levels' => ['amateur'], 'fees' => [], 'pcma_rule' => 'pro', 'required_documents' => ['identity', 'photo', 'medical'], 'new' => true];
    $old = old('categories');
    $months = [1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $tab = fn ($g, $d) => route('licenses.scale', array_filter(['gender' => $g, 'discipline' => $d, 'association_id' => $associations->isNotEmpty() ? $associationId : null]));
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        title="Barème des licences"
        :subtitle="'Par genre, discipline puis catégorie d\'âge, et licences des officiels' . ($associationName ? ' — ' . $associationName : '')"
        eyebrow="Licences · fédération"
        :back-href="route('modules.index', ['section' => 'administration'])"
        back-label="Retour aux modules"
    >
        <x-slot:actions>
            <a href="{{ route('licenses.validation') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Approbation des licences</a>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    @if($associations->isNotEmpty())
        <form method="GET" action="{{ route('licenses.scale') }}" class="mb-4 flex flex-wrap items-end gap-2">
            <input type="hidden" name="gender" value="{{ $gender }}"><input type="hidden" name="discipline" value="{{ $discipline }}">
            <label class="text-sm"><span class="block font-semibold text-slate-700">Fédération</span>
                <select name="association_id" onchange="this.form.submit()" class="mt-1 rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    @foreach($associations as $association)<option value="{{ $association->id }}" @selected($association->id === $associationId)>{{ $association->name }}</option>@endforeach
                </select></label>
        </form>
    @endif

    <p class="mb-4 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
        Structure de l'enregistrement FIFA Connect : <strong>genre</strong> de la personne, <strong>discipline</strong>, <strong>niveau</strong> (amateur, professionnel) et <strong>nature</strong> (enregistrement, prêt).
        Les <strong>catégories d'âge</strong> sont un réglage de la fédération : elles ne figurent pas dans l'enregistrement FIFA Connect (seulement dans les compétitions). Une licence vaut au plus une saison.
        @unless($saved) <span class="font-semibold text-amber-800">Barème par défaut, pas encore enregistré.</span>@endunless
    </p>

    {{-- Genre puis discipline --}}
    <nav class="mb-4 space-y-2" aria-label="Genre et discipline">
        @foreach($genders as $g => $gLabel)
            <div class="flex flex-wrap items-center gap-2">
                <span class="w-20 text-sm font-semibold text-slate-700">{{ $gLabel }}</span>
                @foreach($disciplines as $d => $dLabel)
                    <a href="{{ $tab($g, $d) }}" @if($g === $gender && $d === $discipline) aria-current="page" @endif
                       class="rounded-full px-3 py-1.5 text-sm font-semibold ring-1 {{ $g === $gender && $d === $discipline ? 'bg-slate-900 text-white ring-slate-900' : 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50' }}">{{ $dLabel }}</a>
                @endforeach
            </div>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('licenses.scale.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="association_id" value="{{ $associationId }}">
        <input type="hidden" name="gender" value="{{ $gender }}">
        <input type="hidden" name="discipline" value="{{ $discipline }}">

        <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-settings">
            <h2 id="h-settings" class="text-base font-semibold text-slate-900">Réglages généraux de la fédération</h2>
            <div class="mt-3 grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
                <label class="text-sm"><span class="block font-semibold text-slate-700">Devise</span>
                    <input name="currency" value="{{ old('currency', $settings->currency) }}" maxlength="3" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm uppercase"></label>
                <label class="text-sm"><span class="block font-semibold text-slate-700">Langue des noms locaux</span>
                    <input name="local_language" value="{{ old('local_language', $settings->local_language) }}" maxlength="3" placeholder="fra, ara…" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase" aria-describedby="local-language-help">
                    <span id="local-language-help" class="mt-1 block text-xs text-slate-500">Code ISO 639-2 transmis à FIFA Connect</span></label>
                <label class="text-sm"><span class="block font-semibold text-slate-700">Début de saison (jour)</span>
                    <input type="number" name="season_start_day" min="1" max="28" value="{{ old('season_start_day', $settings->season_start_day) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></label>
                <label class="text-sm"><span class="block font-semibold text-slate-700">… mois</span>
                    <select name="season_start_month" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">@foreach($months as $m => $n)<option value="{{ $m }}" @selected((int) old('season_start_month', $settings->season_start_month) === $m)>{{ $n }}</option>@endforeach</select></label>
                <label class="text-sm"><span class="block font-semibold text-slate-700">Âge calculé au (jour)</span>
                    <input type="number" name="reference_day" min="1" max="28" value="{{ old('reference_day', $settings->reference_day) }}" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></label>
                <label class="text-sm"><span class="block font-semibold text-slate-700">… mois</span>
                    <select name="reference_month" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">@foreach($months as $m => $n)<option value="{{ $m }}" @selected((int) old('reference_month', $settings->reference_month) === $m)>{{ $n }}</option>@endforeach</select></label>
            </div>
            <p class="mt-2 text-xs text-slate-500">Saison en cours : {{ $season['label'] }} (du {{ $season['start']->format('d/m/Y') }} au {{ $season['end']->format('d/m/Y') }}). Une licence expire au plus tard en fin de saison. L'âge est calculé à la date de référence comprise dans la saison ; « U-17 » = moins de 17 ans à cette date.</p>

            <h3 class="mt-4 text-sm font-semibold text-slate-900">Licences des officiels d'équipe et des dirigeants ({{ $settings->currency }})</h3>
            <div class="mt-2 grid gap-3 sm:grid-cols-2">
                @foreach(['TeamOfficial' => 'Officiel d’équipe (TeamOfficial)', 'OrganisationOfficial' => 'Dirigeant (OrganisationOfficial)'] as $type => $label)
                    <label class="text-sm"><span class="block text-slate-700">{{ $label }}</span>
                        <input type="number" step="0.01" min="0" name="official_fees[{{ $type }}]" value="{{ old("official_fees.{$type}", $settings->official_fees[$type] ?? '') }}" placeholder="tarif non défini" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></label>
                @endforeach
            </div>
            <p class="mt-1 text-xs text-slate-500">Pièces exigées pour un officiel : {{ collect(config('licensing.official_documents'))->map(fn ($t) => mb_strtolower($documents[$t] ?? $t))->implode(', ') }}. Pas de catégorie d'âge ni de PCMA.</p>
        </section>

        <h2 class="pt-2 text-lg font-semibold text-slate-900">Joueurs — {{ $genders[$gender] }} · {{ $disciplines[$discipline] }}</h2>

        @foreach($rows as $i => $row)
            @php $row = array_merge($row, $old[$i] ?? []); $isNew = !empty($row['new']) && empty($old[$i]['code'] ?? null); @endphp
            <section class="rounded-2xl border {{ $isNew ? 'border-dashed border-slate-300 bg-slate-50' : 'border-slate-200 bg-white' }} p-5" aria-label="Catégorie {{ $row['label'] ?: 'nouvelle' }}">
                <div class="flex flex-wrap items-end gap-3">
                    <h3 class="mr-auto text-base font-semibold text-slate-900">{{ $isNew ? 'Ajouter une catégorie' : 'Catégorie ' . $row['label'] }}</h3>
                    @unless($isNew)<label class="flex items-center gap-2 text-sm text-red-700"><input type="checkbox" name="categories[{{ $i }}][delete]" value="1" @checked(!empty($row['delete']))> Supprimer</label>@endunless
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-3">
                    <label class="text-sm"><span class="block font-semibold text-slate-700">Code</span>
                        <input name="categories[{{ $i }}][code]" value="{{ $row['code'] }}" maxlength="20" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm uppercase" placeholder="U13"></label>
                    <label class="text-sm"><span class="block font-semibold text-slate-700">Libellé</span>
                        <input name="categories[{{ $i }}][label]" value="{{ $row['label'] }}" maxlength="60" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="U-13"></label>
                    <label class="text-sm"><span class="block font-semibold text-slate-700">Moins de (ans)</span>
                        <input type="number" name="categories[{{ $i }}][max_age]" min="5" max="99" value="{{ $row['max_age'] }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="vide = senior"></label>
                </div>
                <div class="mt-4 grid gap-4 lg:grid-cols-3">
                    <fieldset>
                        <legend class="text-sm font-semibold text-slate-700">Niveaux autorisés et tarif ({{ $settings->currency }})</legend>
                        <div class="mt-2 grid gap-2">
                            @foreach($levels as $level => $label)
                                <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2">
                                    <label class="flex flex-1 items-center gap-2 text-sm text-slate-800"><input type="checkbox" name="categories[{{ $i }}][allowed_levels][]" value="{{ $level }}" @checked(in_array($level, $row['allowed_levels'] ?? [], true))> {{ $label }}</label>
                                    <input type="number" step="0.01" min="0" name="categories[{{ $i }}][fees][{{ $level }}]" value="{{ $row['fees'][$level] ?? '' }}" class="w-24 rounded-lg border border-slate-300 px-2 py-1 text-right text-sm" placeholder="tarif" aria-label="Tarif {{ $label }}">
                                </div>
                            @endforeach
                        </div>
                    </fieldset>
                    <label class="text-sm"><span class="block font-semibold text-slate-700">PCMA exigé</span>
                        <select name="categories[{{ $i }}][pcma_rule]" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            @foreach($pcmaRules as $value => $label)<option value="{{ $value }}" @selected(($row['pcma_rule'] ?? 'pro') === $value)>{{ $label }}</option>@endforeach
                        </select></label>
                    <fieldset>
                        <legend class="text-sm font-semibold text-slate-700">Pièces exigées</legend>
                        <div class="mt-2 grid gap-1">
                            @foreach(['identity', 'photo', 'medical', 'parental'] as $type)
                                <label class="flex items-center gap-2 text-sm text-slate-800"><input type="checkbox" name="categories[{{ $i }}][required_documents][]" value="{{ $type }}" @checked(in_array($type, $row['required_documents'] ?? [], true))> {{ $documents[$type] }}</label>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-slate-500">S'y ajoutent : contrat signé (professionnel), accord de prêt (prêt).</p>
                    </fieldset>
                </div>
            </section>
        @endforeach

        <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Enregistrer ({{ $genders[$gender] }} · {{ $disciplines[$discipline] }} et réglages généraux)</button>
    </form>
</div>
@endsection
