@extends('layouts.app')

@section('title', 'Dossiers médicaux - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dossiers médicaux</h1>
            <p class="text-gray-600 mt-2">Un dossier par joueur. Les consultations restent distinctes dans la chronologie.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('medical-aut.choose') }}" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700">AUT</a>
            <a href="{{ route('health-records.create') }}"
               class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                + Nouvelle visite
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-slate-50">
            <h2 class="font-semibold text-gray-900">Patients suivis</h2>
        </div>

        @if($patients->count())
            <div class="divide-y">
                @foreach($patients as $player)
                    @php($latest = $player->latestHealthRecord)
                    <div class="p-5 grid grid-cols-1 lg:grid-cols-[minmax(240px,1fr)_180px_170px_170px_auto] gap-4 items-center hover:bg-slate-50">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-12 h-12 rounded-full bg-blue-50 border flex items-center justify-center overflow-hidden shrink-0">
                                @if($player->player_picture_url)
                                    <img src="{{ $player->player_picture_url }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="font-semibold text-blue-700">{{ mb_substr($player->full_name ?? $player->name ?? 'P',0,1) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-900 truncate">{{ $player->full_name ?? $player->name }}</div>
                                <div class="text-sm text-gray-500 truncate">
                                    {{ $player->club?->name ?? 'Club non renseigné' }}
                                    @if($player->position) · {{ $player->position }} @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-400">Dernière visite</div>
                            <div class="text-sm text-gray-800 mt-1">{{ $latest?->visit_date?->format('d/m/Y') ?? $latest?->record_date?->format('d/m/Y') ?? '—' }}</div>
                        </div>

                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-400">Motif</div>
                            <div class="text-sm text-gray-800 mt-1 line-clamp-2">{{ $latest?->chief_complaint ?: $latest?->diagnosis ?: '—' }}</div>
                        </div>

                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-400">Historique</div>
                            <div class="text-sm text-gray-800 mt-1">{{ $player->health_records_count }} visite(s)</div>
                        </div>

                        <div class="flex flex-wrap gap-2 lg:justify-end">
                            @if($latest)
                                <a href="{{ route('health-records.show', $latest) }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-white">Ouvrir</a>
                            @endif
                            <a href="{{ route('health-records.create', ['player_id'=>$player->id]) }}" class="px-3 py-2 rounded-lg bg-blue-600 text-sm text-white font-medium">Nouvelle visite</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t">
                {{ $patients->links() }}
            </div>
        @else
            <div class="px-6 py-14 text-center">
                <div class="text-gray-400 text-4xl mb-3">🩺</div>
                <h3 class="text-lg font-medium text-gray-900">Aucun dossier médical</h3>
                <p class="text-gray-500 mt-2 mb-5">Commencez par enregistrer une première visite.</p>
                <a href="{{ route('health-records.create') }}" class="inline-flex px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold">Créer une visite</a>
            </div>
        @endif
    </div>
</div>
@endsection
