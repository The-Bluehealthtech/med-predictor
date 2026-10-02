@extends('layouts.app')

@section('title', 'Importer un export de statistiques joueurs')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Le centre de performance · Données de match</p>
        <h1 class="text-2xl font-bold text-gray-900">Importer un export de statistiques joueurs</h1>
        <p class="text-sm text-gray-600 mt-1">Déposez le fichier « Player statistics » exporté par le club (Excel ou CSV), tel quel. FIT reconnaît le modèle, retrouve les joueurs du club et vous montre un aperçu : rien n'est enregistré avant votre confirmation.</p>
    </div>
    @if($errors->any())<div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
    <form method="POST" action="{{ route('player-stats-import.preview') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf
        <label class="block text-sm font-medium text-gray-700">Fichier
            <input type="file" name="file" accept=".xlsx,.csv" required class="mt-1 block w-full text-sm">
            <span class="block text-xs text-gray-500 mt-1">Exemple : « 27.09.2026 - Al-Hazem SC - Player statistics.xlsx ». La date et le club sont lus dans le nom du fichier.</span>
        </label>
        <label class="block text-sm font-medium text-gray-700">Club <span class="font-normal text-gray-400">(facultatif si le nom du fichier contient le club)</span>
            <select name="club_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                <option value="">Déduire du nom du fichier</option>
                @foreach($clubs as $club)<option value="{{ $club->id }}" @selected((int) $clubId === (int) $club->id)>{{ str_replace(' (Démo)', '', $club->name) }}</option>@endforeach
            </select>
        </label>
        <div class="flex justify-end gap-3">
            <a href="{{ route('modules.coach-cockpit', array_filter(['club_id' => $clubId])) }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Annuler</a>
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Analyser le fichier</button>
        </div>
    </form>
    <details class="text-sm text-gray-600">
        <summary class="cursor-pointer font-medium text-gray-800">Quel fichier est accepté ?</summary>
        <p class="mt-2">Le modèle « Player statistics » des exports de compétition (une feuille, une ligne par joueur) avec au moins les colonnes <b>Player</b>, <b>Minutes played</b> et <b>Position</b>, puis les indicateurs : passes et précision, passes clés, centres, duels, duels aériens, dribbles, tacles, interceptions, ballons récupérés, tirs, xG… Une valeur « - » signifie « donnée absente ». Un export de saison (moyennes par match) est enregistré comme profil de période du joueur.</p>
    </details>
</div>
@endsection
