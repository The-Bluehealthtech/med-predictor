@extends('layouts.app')

@section('title', 'Licence d\'officiel ou de dirigeant')

@php
    $typeLabel = config('licensing.registration_types.' . $official->registration_type, $official->registration_type);
    $role = $official->registration_type === 'TeamOfficial'
        ? config('fifa_connect_roles.team_official.' . $official->team_official_role . '.label', $official->team_official_role)
        : config('fifa_connect_roles.organisation_official.' . $official->organisation_official_role . '.label', $official->organisation_official_role);
    $name = trim($official->international_first_name . ' ' . $official->international_last_name) ?: $official->popular_name;
    $money = fn ($r) => $r['fee'] === null ? 'tarif non défini par la fédération' : number_format($r['fee'], 2, ',', ' ') . ' ' . $r['currency'];
@endphp

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <x-page-header :title="'Licence ' . mb_strtolower($typeLabel)" :subtitle="$name . ' — ' . $role" eyebrow="Licences · officiels et dirigeants"
        :back-href="route('modules.licenses.index')" back-label="Retour aux demandes" />

    @if(session('error'))<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <p class="mb-5 text-sm text-slate-600">Enregistrement « {{ $official->registration_type }} » au sens de FIFA Connect, rôle « {{ $role }} » (fiche « Dirigeants et staff »). Pas de catégorie d'âge ni de PCMA. Tarif : <strong>{{ $money($rules) }}</strong>.
            @unless($official->person_fifa_id) <strong>Pas d'identifiant FIFA sur la fiche :</strong> la vérification FIFA ID ne sera pas possible.@endunless</p>

        <form method="POST" action="{{ route('official-licenses.request.store', $official) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block text-sm"><span class="font-semibold text-slate-700">Saison</span>
                    <select name="season" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($seasons as $label => $season)<option value="{{ $label }}" @selected(old('season') === $label)>{{ $label }} (du {{ $season['start']->format('d/m/Y') }} au {{ $season['end']->format('d/m/Y') }})</option>@endforeach
                    </select></label>
                <label class="block text-sm"><span class="font-semibold text-slate-700">Discipline</span>
                    <select name="discipline" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach(config('licensing.disciplines') as $value => $label)<option value="{{ $value }}" @selected(old('discipline', $official->discipline ?: 'Football') === $value)>{{ $label }}</option>@endforeach
                    </select></label>
            </div>

            <fieldset class="rounded-xl border border-slate-200 p-4">
                <legend class="px-1 text-sm font-semibold text-slate-700">Pièces justificatives</legend>
                <div class="grid gap-3">
                    @foreach($documents as $type => $label)
                        @continue(!in_array($type, $rules['documents'], true) && $type !== 'other')
                        <label class="grid gap-1 sm:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)] sm:items-center">
                            <span class="text-sm text-slate-800">{{ $label }} @if(in_array($type, $rules['documents'], true))<span class="ml-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">exigée</span>@endif</span>
                            <input type="file" name="documents[{{ $type }}]" accept=".pdf,.jpg,.jpeg,.png" @required(in_array($type, $rules['documents'], true)) class="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold">
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
