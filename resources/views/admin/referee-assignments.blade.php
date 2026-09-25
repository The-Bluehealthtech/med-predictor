@extends('layouts.app')

@section('title', 'Désignation des arbitres')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Désignation des arbitres</h1>
    @if(session('success'))
        <p class="rounded bg-green-50 p-3 text-green-800">{{ session('success') }}</p>
    @endif
    @if($errors->any())
        <div class="rounded bg-red-50 p-3 text-red-800">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif
    <p class="text-gray-600">Matchs programmés et arbitres enregistrés.</p>
    @forelse($matchesToAssign as $match)
        <section class="rounded bg-white p-5 shadow">
            <h2 class="font-semibold">{{ $match->homeTeam?->name ?? 'Équipe non renseignée' }}
                — {{ $match->awayTeam?->name ?? 'Équipe non renseignée' }}</h2>
            <p class="text-sm text-gray-600">{{ $match->competition?->name ?? 'Compétition non renseignée' }}
                · {{ $match->match_date ?? 'Date non renseignée' }}</p>
            @if($referees->count() >= 3)
                <form class="mt-4 grid gap-3 md:grid-cols-4" method="POST"
                    action="{{ route('admin.assign-referees') }}">
                    @csrf
                    <input type="hidden" name="match_id" value="{{ $match->id }}">
                    @foreach(['main_referee' => 'Arbitre principal',
                        'assistant_referee_1' => 'Assistant 1',
                        'assistant_referee_2' => 'Assistant 2'] as $field => $label)
                        <label class="text-sm">{{ $label }}
                            <select name="{{ $field }}" required class="mt-1 block w-full rounded border-gray-300">
                                <option value="">Sélectionner</option>
                                @foreach($referees as $referee)
                                    <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endforeach
                    <button class="self-end rounded bg-blue-700 px-4 py-2 text-white">Enregistrer</button>
                </form>
            @else
                <p class="mt-3 text-sm text-gray-600">Trois arbitres enregistrés sont requis.</p>
            @endif
        </section>
    @empty
        <p class="rounded bg-white p-5 text-gray-600 shadow">Aucun match programmé à désigner.</p>
    @endforelse
</div>
@endsection
