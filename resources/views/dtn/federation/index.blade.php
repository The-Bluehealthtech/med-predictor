@extends('layouts.app')

@section('title', 'DTN — Espace fédération')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700">Direction technique nationale · Espace fédération</p>
            <h1 class="text-2xl font-bold text-gray-900">Sélections nationales</h1>
            <p class="text-sm text-gray-600 max-w-2xl">Consultez les fiches joueurs, convoquez, recevez l'état de départ préparé par le club et renvoyez l'état de retour (incidents, performances, risques, indice de performance).</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('dtn.players.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-indigo-200 bg-white text-indigo-700 text-sm font-semibold hover:bg-indigo-50">Fiches joueurs</a>
            @if($canConvoke)
                <a href="{{ route('dtn.selections.create') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">+ Convoquer un joueur</a>
            @endif
            <a href="{{ route('dtn.api-access') }}" class="text-sm text-gray-600 hover:text-gray-900">Accès API</a>
            <a href="{{ route('modules.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Modules</a>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if(!empty($notInstalled))
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Le module des sélections nationales est en cours d'installation : ses tables seront créées au prochain démarrage de l'application.</div>
    @endif

    @foreach($groups as $group)
        <section class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h2 class="font-semibold text-gray-900">{{ $group['title'] }} <span class="text-gray-400 font-normal">({{ $group['items']->count() }})</span></h2>
                <p class="text-xs text-gray-500">{{ $group['subtitle'] }}</p>
            </div>
            @if($group['items']->isEmpty())
                <p class="px-5 py-6 text-sm text-gray-500">{{ $group['empty'] }}</p>
            @else
                @include('dtn.partials.selection-table', ['items' => $group['items'], 'showRoute' => 'dtn.selections.show', 'counterpart' => 'Club'])
            @endif
        </section>
    @endforeach
</div>
@endsection
