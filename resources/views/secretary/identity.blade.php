@extends('layouts.secretary')

@section('title', 'Identité clinique')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <a href="{{ $back && str_starts_with($back, url('/')) ? $back : route('secretary.dashboard') }}" class="text-sm text-blue-600">← Retour</a>
        <h1 class="text-3xl font-bold text-slate-900 mt-2">Identité clinique — {{ $player->full_name ?? $player->name }}</h1>
        <p class="text-slate-600 mt-1">Rapprochement du joueur avec les dossiers des établissements (EMR, laboratoires, imagerie) sur le serveur FHIR de FIT. Profils IHE PIXm et PDQm : aucun rattachement sans confirmation, aucune fusion.</p>
    </div>

    @if(session('success'))<p class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-900" role="status">{{ session('success') }}</p>@endif
    @if($errors->has('fhir') || $error)<p class="rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-900" role="alert">{{ $errors->first('fhir') ?: $error }}</p>@endif

    @unless($configured)
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
            Le serveur FHIR de FIT n’est pas encore installé (prévu avant la mise en production). L’identité clinique sera transmise au premier pré-accueil suivant son installation.
        </section>
    @else
        <section class="bg-white border border-slate-200 rounded-2xl p-5" aria-labelledby="h-fit-patient">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 id="h-fit-patient" class="font-semibold text-slate-900">Patient alimenté par FIT</h2>
                    @if($fitLink?->patient_id)
                        <p class="text-sm text-slate-600 mt-1">Patient/{{ $fitLink->patient_id }} · transmis le {{ $fitLink->synced_at?->format('d/m/Y H:i') }}</p>
                    @else
                        <p class="text-sm text-slate-600 mt-1">Pas encore transmis.</p>
                    @endif
                    @if($fitLink?->sync_error)<p class="text-sm text-red-700 mt-1">Dernier échec : {{ $fitLink->sync_error }}</p>@endif
                </div>
                <form method="POST" action="{{ route('secretary.identity.feed', $player) }}">
                    @csrf
                    <button class="px-3 py-2 rounded-lg border border-slate-300 text-sm font-semibold hover:bg-slate-50">{{ $fitLink?->patient_id ? 'Mettre à jour' : 'Transmettre' }}</button>
                </form>
            </div>
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden" aria-labelledby="h-linked">
            <h2 id="h-linked" class="px-5 py-4 border-b font-semibold text-slate-900">Dossiers rattachés et écartés</h2>
            @forelse($externalLinks as $link)
                <div class="p-4 border-b last:border-0 grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3 md:items-center">
                    <div class="text-sm">
                        <div class="font-semibold text-slate-900">{{ data_get($link->snapshot, 'name') }} · Patient/{{ $link->patient_id }}</div>
                        <div class="text-slate-600">{{ data_get($link->snapshot, 'birthDate') ?? 'Date de naissance non renseignée' }}@if(data_get($link->snapshot, 'source')) · source {{ data_get($link->snapshot, 'source') }}@endif</div>
                        <div class="text-xs text-slate-500">{{ $link->status === 'linked' ? 'Rattaché' : 'Écarté' }} par {{ $link->decidedBy?->name ?? '—' }} le {{ $link->decided_at?->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($link->status === 'linked')
                        <form method="POST" action="{{ route('secretary.identity.decision', $player) }}">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $link->patient_id }}">
                            <input type="hidden" name="decision" value="reject">
                            <button class="px-3 py-2 rounded-lg border border-red-200 text-sm font-semibold text-red-700 hover:bg-red-50">Retirer le rattachement</button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="p-5 text-sm text-slate-500">Aucun dossier d’établissement rattaché.</p>
            @endforelse
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden" aria-labelledby="h-candidates">
            <div class="px-5 py-4 border-b flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="h-candidates" class="font-semibold text-slate-900">Dossiers correspondants possibles</h2>
                    <p class="text-sm text-slate-500">Recherche PDQm par FIFA ID, puis par nom et date de naissance. Vérifiez l’identité avant de rattacher.</p>
                </div>
                <a href="{{ route('secretary.identity', ['player' => $player, 'search' => 1]) }}" class="px-3 py-2 rounded-lg bg-slate-900 text-white text-sm font-semibold">Rechercher</a>
            </div>
            @if($candidates === null)
                <p class="p-5 text-sm text-slate-500">Lancez la recherche pour interroger le serveur.</p>
            @else
                @forelse($candidates as $candidate)
                    <div class="p-4 border-b last:border-0 grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3 md:items-center">
                        <div class="text-sm">
                            <div class="font-semibold text-slate-900">{{ $candidate['name'] }} · Patient/{{ $candidate['id'] }}</div>
                            <div class="text-slate-600">{{ $candidate['birthDate'] ?? 'Date de naissance non renseignée' }} · {{ $candidate['gender'] ?? 'sexe non renseigné' }}@if($candidate['source']) · source {{ $candidate['source'] }}@endif</div>
                            <div class="text-xs text-slate-500">Trouvé par {{ $candidate['matched_on'] === 'fifa_id' ? 'FIFA ID' : 'nom et date de naissance' }} — identifiants : {{ implode(', ', $candidate['identifiers']) ?: '—' }}</div>
                        </div>
                        <div class="flex gap-2">
                            @foreach(['link' => ['Rattacher', 'bg-emerald-700 text-white'], 'reject' => ['Écarter', 'border border-slate-300 text-slate-700']] as $decision => [$label, $style])
                                <form method="POST" action="{{ route('secretary.identity.decision', $player) }}">
                                    @csrf
                                    <input type="hidden" name="patient_id" value="{{ $candidate['id'] }}">
                                    <input type="hidden" name="matched_on" value="{{ $candidate['matched_on'] }}">
                                    <input type="hidden" name="decision" value="{{ $decision }}">
                                    <button class="px-3 py-2 rounded-lg text-sm font-semibold {{ $style }}">{{ $label }}</button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="p-5 text-sm text-slate-500">Aucun dossier correspondant sur le serveur.</p>
                @endforelse
            @endif
        </section>
    @endunless
</div>
@endsection
