@extends('layouts.app')

@section('title', 'Convoquer un joueur — DTN')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 space-y-6">
    @include('dtn.federation.partials.nav', ['active' => 'players'])
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Convoquer un joueur</h1>
            <p class="text-sm text-gray-600">Le club du joueur recevra une demande d'état de départ, pré-rempli à partir de ses données de match.</p>
        </div>
        <a href="{{ route('dtn.players.index') }}" class="text-indigo-700 hover:text-indigo-900 text-sm">← Fiches joueurs</a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">La convocation n'a pas été créée :</p>
            <ul class="list-disc ml-5 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('dtn.selections.store') }}" class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf
        <div>
            <span class="block text-sm font-medium text-gray-700">Joueur</span>
            @if($player)
                <input type="hidden" name="player_id" value="{{ $player->id }}">
                <div class="mt-1 flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <span><b>{{ $player->first_name }} {{ $player->last_name }}</b> · {{ str_replace(' (Démo)', '', $player->club->name ?? '—') }}@if($player->position) · {{ $player->position }}@endif</span>
                    <a href="{{ route('dtn.players.show', $player->id) }}" class="text-indigo-700 hover:text-indigo-900">Voir la fiche</a>
                </div>
            @else
                <p class="mt-1 text-sm text-gray-600">Choisissez d'abord un joueur dans les <a href="{{ route('dtn.players.index') }}" class="text-indigo-700 underline">fiches joueurs</a>, après avoir consulté ses données.</p>
            @endif
        </div>

        @if($associations->isNotEmpty())
            <div>
                <label for="association_id" class="block text-sm font-medium text-gray-700">Fédération (Direction technique nationale)</label>
                <select id="association_id" name="association_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    @foreach($associations as $a)
                        <option value="{{ $a->id }}" @selected((int) old('association_id') === $a->id)>{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="team_label" class="block text-sm font-medium text-gray-700">Équipe nationale</label>
                <input id="team_label" name="team_label" value="{{ old('team_label', 'Équipe nationale A') }}" required maxlength="120" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="event_type" class="block text-sm font-medium text-gray-700">Type de rassemblement</label>
                <select id="event_type" name="event_type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    @foreach($eventTypes as $key => $label)
                        <option value="{{ $key }}" @selected(old('event_type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="event_name" class="block text-sm font-medium text-gray-700">Rassemblement</label>
                <input id="event_name" name="event_name" value="{{ old('event_name') }}" required maxlength="160" placeholder="Ex. Fenêtre internationale de novembre" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="opponent" class="block text-sm font-medium text-gray-700">Adversaire(s) <span class="text-gray-400">(facultatif)</span></label>
                <input id="opponent" name="opponent" value="{{ old('opponent') }}" maxlength="120" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Début</label>
                <input id="start_date" type="date" name="start_date" value="{{ old('start_date') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Fin</label>
                <input id="end_date" type="date" name="end_date" value="{{ old('end_date') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
            </div>
        </div>

        <div>
            <label for="convocation_note" class="block text-sm font-medium text-gray-700">Message au club <span class="text-gray-400">(facultatif)</span></label>
            <textarea id="convocation_note" name="convocation_note" rows="3" maxlength="2000" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Lieu et heure de rendez-vous, informations utiles…">{{ old('convocation_note') }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('dtn.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Annuler</a>
            <button type="submit" @disabled(!$player) class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 disabled:opacity-50">Envoyer la convocation</button>
        </div>
    </form>
</div>
@endsection
