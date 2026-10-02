@extends('layouts.app')

@section('title', 'Retours de sélection — Espace club')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    @include('club.selections.partials.nav', ['active' => 'returns'])
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Retours de sélection</h1>
            <p class="text-sm text-gray-600 max-w-2xl">États de retour envoyés par la Direction technique nationale : incidents, performances, risques et indice de performance. Accusez réception pour clôturer.</p>
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
                @include('club.selections.partials.selection-table', ['items' => $group['items']])
            @endif
        </section>
    @endforeach
</div>
@endsection
