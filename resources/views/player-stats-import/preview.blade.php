@extends('layouts.app')

@section('title', 'Aperçu de l\'import')

@php $fmt = fn ($v) => $v === null ? '—' : number_format((float) $v, 0, ',', ' '); @endphp

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Importer un export de statistiques joueurs · aperçu</p>
        <h1 class="text-2xl font-bold text-gray-900">{{ $club ? str_replace(' (Démo)', '', $club->name) : 'Club à choisir' }}</h1>
        <p class="text-sm text-gray-600">Rien n'est encore enregistré. Vérifiez la reconnaissance, puis confirmez.</p>
    </div>

    <section class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4"><div class="text-xs text-gray-500">Modèle</div><div class="font-semibold {{ $file['is_template'] ? 'text-emerald-700' : 'text-red-700' }}">{{ $file['is_template'] ? 'Reconnu' : 'Non reconnu' }}</div><div class="text-xs text-gray-500">Player statistics</div></div>
        <div class="bg-white rounded-lg shadow p-4"><div class="text-xs text-gray-500">Nature</div><div class="font-semibold text-gray-900">{{ $file['kind'] === 'period' ? 'Cumul de période' : 'Match unique' }}</div><div class="text-xs text-gray-500">{{ $file['kind'] === 'period' ? 'moyennes par match' : 'valeurs du match' }}</div></div>
        <div class="bg-white rounded-lg shadow p-4"><div class="text-xs text-gray-500">Export du</div><div class="font-semibold text-gray-900">{{ $file['file_date']?->format('d/m/Y') ?? 'date absente du nom' }}</div><div class="text-xs text-gray-500">{{ count($file['rows']) }} joueur(s) dans le fichier</div></div>
        <div class="bg-white rounded-lg shadow p-4"><div class="text-xs text-gray-500">Indicateurs reconnus</div><div class="font-semibold text-gray-900">{{ count($file['metric_columns']) }}</div><div class="text-xs text-gray-500">{{ count($file['unknown']) }} colonne(s) inconnue(s)</div></div>
    </section>

    @if(!$file['is_template'])
        <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">Le fichier ne correspond pas au modèle « Player statistics »@if($file['missing_required']) : colonnes manquantes {{ implode(', ', $file['missing_required']) }}@endif. <a href="{{ route('player-stats-import.create') }}" class="underline">Choisir un autre fichier</a></div>
    @elseif($file['kind'] !== 'period')
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">Ce fichier contient les statistiques d'un seul match. Son rattachement à une feuille de match arrive dans une prochaine étape ; il n'est pas importé ici.</div>
    @endif
    @if($file['unknown'])<p class="text-xs text-gray-500">Colonnes inconnues, ignorées : {{ implode(', ', $file['unknown']) }}</p>@endif

    @if($file['is_template'] && $file['kind'] === 'period')
        <form method="POST" action="{{ route('player-stats-import.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <section class="bg-white rounded-lg shadow p-5 grid grid-cols-1 md:grid-cols-4 gap-4">
                <label class="block text-sm font-medium text-gray-700 md:col-span-1">Club
                    <select name="club_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm" onchange="this.form.dataset.changed='1'">
                        @foreach($clubs as $c)<option value="{{ $c->id }}" @selected($club && $club->id === $c->id)>{{ str_replace(' (Démo)', '', $c->name) }}</option>@endforeach
                    </select>
                </label>
                <label class="block text-sm font-medium text-gray-700">Saison<input name="season" required maxlength="20" value="{{ $preview['season'] ?? '' }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm"></label>
                <label class="block text-sm font-medium text-gray-700">Compétition<input name="competition" required maxlength="120" value="{{ $competition }}" placeholder="ex. Saudi Professional League" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm"></label>
                <label class="block text-sm font-medium text-gray-700">Source<input name="source" required maxlength="40" value="KSA" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm"><span class="block text-xs text-gray-400 mt-1">« KSA » : lu par les portails joueur.</span></label>
            </section>

            @if($preview)
                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-5 py-3 border-b"><h2 class="font-semibold text-gray-900">Joueurs reconnus dans le club <span class="text-gray-400 font-normal">({{ count($preview['matched']) }})</span></h2></div>
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500"><tr><th class="px-5 py-2 text-left">Dans le fichier</th><th class="px-5 py-2 text-left">Joueur FIT</th><th class="px-5 py-2 text-left">Poste</th><th class="px-5 py-2 text-right">Minutes</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($preview['matched'] as $m)<tr><td class="px-5 py-2">{{ $m['name'] }}</td><td class="px-5 py-2 text-emerald-700">✓ {{ $m['player_name'] }}</td><td class="px-5 py-2">{{ $m['position'] }}</td><td class="px-5 py-2 text-right">{{ $fmt($m['minutes']) }}</td></tr>@endforeach
                        </tbody>
                    </table>
                </section>
                @if($preview['unmatched'])
                    <section class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-5 py-3 border-b"><h2 class="font-semibold text-gray-900">Joueurs introuvables dans le club <span class="text-gray-400 font-normal">({{ count($preview['unmatched']) }})</span></h2></div>
                        <ul class="divide-y divide-gray-100 text-sm">@foreach($preview['unmatched'] as $u)<li class="px-5 py-2 flex justify-between"><span>{{ $u['name'] }}</span><span class="text-gray-500">{{ $u['position'] }} · {{ $fmt($u['minutes']) }} min</span></li>@endforeach</ul>
                        <label class="flex items-center gap-2 px-5 py-3 border-t text-sm text-gray-700"><input type="hidden" name="create_missing" value="0"><input type="checkbox" name="create_missing" value="1" class="rounded border-gray-300"> Créer ces joueurs dans le club (sinon ils sont ignorés)</label>
                    </section>
                @endif
            @else
                <p class="text-sm text-amber-800">Le nom du fichier ne correspond à aucun club accessible : <a href="{{ route('player-stats-import.create') }}" class="underline">recommencez en choisissant le club</a> pour voir les joueurs reconnus avant d'importer.</p>
            @endif

            <div class="flex justify-end gap-3">
                <a href="{{ route('player-stats-import.create') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Recommencer</a>
                <button class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700" @disabled(!$preview)>Confirmer l'import</button>
            </div>
        </form>
    @endif
</div>
@endsection
