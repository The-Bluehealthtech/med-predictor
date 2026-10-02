@extends('layouts.app')

@section('title', 'Fiche joueur — ' . $profile['name'])

@php $d = $profile['data']; @endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    @include('dtn.federation.partials.nav', ['active' => 'players'])
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700">Fiche joueur</p>
            <h1 class="text-2xl font-bold text-gray-900">{{ $profile['name'] }}</h1>
            <p class="text-sm text-gray-600">{{ str_replace(' (Démo)', '', $profile['club']['name'] ?? '—') }} · {{ $profile['position'] ?? 'poste non renseigné' }}@if($profile['age']) · {{ $profile['age'] }} ans @endif @if($profile['height_cm']) · {{ $profile['height_cm'] }} cm @endif @if($profile['weight_kg']) · {{ $profile['weight_kg'] }} kg @endif</p>
        </div>
        <div class="flex items-center gap-3">
            @if($canConvoke)
                <a href="{{ route('dtn.selections.create', ['player_id' => $profile['id']]) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Convoquer ce joueur</a>
            @endif
            <a href="{{ route('dtn.players.index') }}" class="text-indigo-700 hover:text-indigo-900 text-sm">← Fiches joueurs</a>
        </div>
    </div>

    @if($profile['currently_selected'])
        <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">Ce joueur est actuellement en sélection.</div>
    @endif

    <section class="bg-white rounded-lg shadow p-5 space-y-4">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Données sportives (feuilles de match du club)</p>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            @foreach([
                ['Forme (5 derniers)', isset($d['form']['rating_last5']) ? number_format($d['form']['rating_last5'], 2, ',', ' ') : '—', 'note moyenne'],
                ['Note de saison', isset($d['form']['rating_season']) ? number_format($d['form']['rating_season'], 2, ',', ' ') : '—', ($d['season']['matches'] ?? 0) . ' matchs'],
                ['Charge (3 derniers)', ($d['load']['load_last3_pct'] ?? 0) . ' %', ($d['load']['minutes_last3'] ?? 0) . ' min'],
                ['Rôle et apport', isset($d['role_evaluation']['score']) ? number_format($d['role_evaluation']['score'], 1, ',', ' ') : '—', $d['role_evaluation']['family'] ?? 'non évalué'],
                ['Score FIT', isset($d['fit']['score']) ? number_format($d['fit']['score'], 1, ',', ' ') : '—', $d['fit']['date'] ?? ''],
            ] as [$label, $value, $sub])
                <div class="rounded-lg border border-gray-200 p-3"><div class="text-xs text-gray-500">{{ $label }}</div><div class="text-xl font-bold text-gray-900">{{ $value }}</div><div class="text-xs text-gray-500">{{ $sub }}</div></div>
            @endforeach
        </div>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3 text-sm">
            @foreach(['Matchs' => $d['season']['matches'] ?? 0, 'Titularisations' => $d['season']['starts'] ?? 0, 'Minutes' => number_format($d['season']['minutes'] ?? 0, 0, ',', ' '), 'Buts' => $d['season']['goals'] ?? 0, 'Passes déc.' => $d['season']['assists'] ?? 0, 'Cartons' => ($d['season']['yellow'] ?? 0) . ' J · ' . ($d['season']['red'] ?? 0) . ' R'] as $label => $value)
                <div><div class="text-xs text-gray-500">{{ $label }}</div><div class="font-semibold text-gray-900">{{ $value }}</div></div>
            @endforeach
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-xs uppercase tracking-wider text-gray-500"><tr><th class="py-1 text-left">Derniers matchs</th><th class="py-1 text-left">Score</th><th class="py-1 text-right">Min.</th><th class="py-1 text-right">Note</th><th class="py-1 text-right">B / PD</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($d['last_matches'] ?? [] as $m)
                        <tr><td class="py-1.5">{{ \Carbon\Carbon::parse($m['date'])->format('d/m/Y') }} · {{ $m['opponent'] }}</td><td>{{ $m['score'] }}</td><td class="text-right">{{ $m['minutes'] }}</td><td class="text-right">{{ $m['rating'] !== null ? number_format($m['rating'], 1, ',', ' ') : '—' }}</td><td class="text-right">{{ $m['goals'] }} / {{ $m['assists'] }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="py-2 text-gray-500">Aucun match enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(!empty($d['discipline']['one_yellow_from_suspension']))<p class="text-xs text-amber-700">⚠ À un carton jaune d'une suspension.</p>@endif
    </section>

    <section class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b"><h2 class="font-semibold text-gray-900">Historique des sélections</h2></div>
        @if(empty($profile['selections']))
            <p class="px-5 py-6 text-sm text-gray-500">Aucune sélection enregistrée.</p>
        @else
            <ul class="divide-y divide-gray-100 text-sm">
                @foreach($profile['selections'] as $s)
                    <li class="px-5 py-3 flex justify-between gap-4"><span>{{ $s['team_label'] }} · {{ $s['type'] }} — {{ $s['event'] }} ({{ \Carbon\Carbon::parse($s['start_date'])->format('d/m/Y') }})</span><span class="text-gray-600">{{ $s['status_label'] }}</span></li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
@endsection
