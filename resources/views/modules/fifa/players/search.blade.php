@extends('layouts.app')

@section('title', 'Recherche joueurs FIFA Connect')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold mb-4">Recherche des joueurs enregistrés</h1>
    <form action="{{ route('fifa.players.search') }}" method="GET" class="flex gap-3 mb-6">
        <label for="q" class="sr-only">Nom ou identifiant FIFA Connect</label>
        <input id="q" name="q" value="{{ $query }}" maxlength="100" placeholder="Nom ou identifiant FIFA Connect" class="border rounded px-3 py-2 flex-1">
        <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded">Rechercher</button>
    </form>
    @if($query !== '')
        <p class="mb-4">{{ $players->count() }} résultat(s), 25 maximum.</p>
        <ul class="divide-y bg-white rounded shadow">
            @forelse($players as $player)
                <li class="px-4 py-3">
                    <span class="font-medium">{{ $player->first_name }} {{ $player->last_name }}</span>
                    <span class="text-gray-600">— {{ $player->club?->name ?? 'Club non renseigné' }}</span>
                    <span class="block text-sm text-gray-600">Identifiant FIFA Connect : {{ $player->fifa_connect_id ?: 'Non renseigné' }}</span>
                </li>
            @empty
                <li class="px-4 py-3">Aucun joueur trouvé.</li>
            @endforelse
        </ul>
    @endif
</div>
@endsection
