@extends('layouts.app')

@section('content')
<main class="max-w-5xl mx-auto p-6">
    <a href="{{ route('match-sheet.index') }}" class="text-blue-700 hover:underline">{{ app()->getLocale() === 'en' ? 'Back to match sheets' : 'Retour aux feuilles de match' }}</a>
    <div class="bg-white rounded-xl shadow p-6 mt-5">
        <p class="text-sm text-amber-800 mb-3">{{ app()->getLocale() === 'en' ? 'Demonstration data — unsigned match sheet' : 'Données de démonstration — feuille de match non signée' }}</p>
        <h1 class="text-2xl font-bold">{{ $match->homeTeam?->club?->name ?? '—' }} – {{ $match->awayTeam?->club?->name ?? '—' }}</h1>
        <p class="text-gray-600 mt-2">{{ $match->competition?->name }} · {{ $match->match_date?->format('d/m/Y H:i') }}</p>
        <p class="text-3xl font-semibold mt-5">{{ $match->home_score ?? '—' }} – {{ $match->away_score ?? '—' }}</p>
        <p class="mt-4">{{ app()->getLocale() === 'en' ? 'Sheet status' : 'Statut de la feuille' }} : {{ $sheet->status }}</p>
    </div>
    <section class="bg-white rounded-xl shadow p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">{{ app()->getLocale() === 'en' ? 'Match events' : 'Événements du match' }}</h2>
        @forelse($match->events->sortBy('minute') as $event)
            <div class="border-t py-3">{{ $event->minute }}′ · {{ $event->player?->name ?? '—' }} · {{ $event->event_type ?? $event->type }}</div>
        @empty
            <p class="text-gray-600">{{ app()->getLocale() === 'en' ? 'No recorded events.' : 'Aucun événement enregistré.' }}</p>
        @endforelse
    </section>
</main>
@endsection
