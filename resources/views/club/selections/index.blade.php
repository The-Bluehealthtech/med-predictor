@extends('layouts.app')

@section('title', 'Sélections nationales — Espace club')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Club · Sélections nationales</p>
            <h1 class="text-2xl font-bold text-gray-900">Joueurs sélectionnés</h1>
            <p class="text-sm text-gray-600 max-w-2xl">Recevez les convocations de la Direction technique nationale, préparez l'état de départ de vos joueurs et recevez leur état de retour de sélection.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('club.selections.api-access') }}" class="text-sm text-gray-600 hover:text-gray-900">Accès API</a>
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
                @include('dtn.partials.selection-table', ['items' => $group['items'], 'showRoute' => 'club.selections.show', 'counterpart' => 'Fédération'])
            @endif
        </section>
    @endforeach
</div>
@endsection
