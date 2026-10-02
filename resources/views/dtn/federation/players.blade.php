@extends('layouts.app')

@section('title', 'Fiches joueurs — DTN')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700">Direction technique nationale · Espace fédération</p>
            <h1 class="text-2xl font-bold text-gray-900">Fiches joueurs</h1>
            <p class="text-sm text-gray-600">Données sportives des joueurs de tous les clubs, pour préparer une convocation. Aucune donnée médicale.</p>
        </div>
        <a href="{{ route('dtn.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Espace fédération</a>
    </div>

    <form method="GET" class="bg-white rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
        <label class="block text-sm font-medium text-gray-700">Nom ou prénom
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="mt-1 block w-64 rounded-lg border-gray-300 shadow-sm text-sm" placeholder="Ex. Roux">
        </label>
        <label class="block text-sm font-medium text-gray-700">Club
            <select name="club_id" class="mt-1 block w-56 rounded-lg border-gray-300 shadow-sm text-sm">
                <option value="">Tous les clubs</option>
                @foreach($clubs as $club)<option value="{{ $club->id }}" @selected((int) ($filters['club_id'] ?? 0) === $club->id)>{{ str_replace(' (Démo)', '', $club->name) }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Poste
            <select name="position" class="mt-1 block w-32 rounded-lg border-gray-300 shadow-sm text-sm">
                <option value="">Tous</option>
                @foreach($positions as $pos)<option value="{{ $pos }}" @selected(($filters['position'] ?? '') === $pos)>{{ $pos }}</option>@endforeach
            </select>
        </label>
        <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Rechercher</button>
    </form>

    <section class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr><th class="px-5 py-2 text-left">Joueur</th><th class="px-5 py-2 text-left">Club</th><th class="px-5 py-2 text-left">Poste</th><th class="px-5 py-2 text-left">Âge</th><th class="px-5 py-2"></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($players as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $p->last_name }} {{ $p->first_name }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ str_replace(' (Démo)', '', $p->club->name ?? '—') }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ $p->position ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ $p->date_of_birth ? \Carbon\Carbon::parse($p->date_of_birth)->age . ' ans' : '—' }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('dtn.players.show', $p->id) }}" class="text-indigo-700 hover:text-indigo-900 font-medium">Fiche</a>
                                @if($canConvoke)<span class="text-gray-300 mx-1">|</span><a href="{{ route('dtn.selections.create', ['player_id' => $p->id]) }}" class="text-blue-600 hover:text-blue-800 font-medium">Convoquer</a>@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-6 text-gray-500">Aucun joueur ne correspond à cette recherche.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ $players->links() }}</div>
    </section>
</div>
@endsection
