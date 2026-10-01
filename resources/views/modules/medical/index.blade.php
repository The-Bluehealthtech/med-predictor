@extends('layouts.app')

@section('title', 'Salle d’attente médicale - FIT')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-7">

        <header class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full mb-3">
                        Médical
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-950">Salle d’attente médicale</h1>
                    <p class="mt-2 text-slate-600 max-w-2xl">
                        Seuls les joueurs accueillis par le secrétariat et prêts à être reçus apparaissent ici.
                    </p>
                </div>
                <div class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm text-slate-600">
                    <span class="font-semibold text-slate-950">{{ $waitingAppointments->count() }}</span>
                    patient(s) en attente
                </div>
            </div>
        </header>

        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50/70">
                <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">File transmise par le secrétariat</div>
                <h2 class="font-semibold text-slate-950 mt-1">Patients prêts à être reçus</h2>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($waitingAppointments as $appointment)
                    @php
                        $player = $appointment->athlete->player;
                        $visit = $appointment->visit;
                        $dossier = $player->baseHealthRecord;
                        $preIntake = data_get($visit?->administrative_data, 'pre_intake', []);
                        $allergies = data_get($preIntake, 'patient_reported_allergies');
                        $medications = data_get($preIntake, 'patient_reported_medications');
                    @endphp

                    <article class="p-5 sm:p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-[minmax(220px,1fr)_200px_minmax(260px,1.3fr)_auto] gap-5 lg:items-center">
                            <div>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0">
                                        <span class="font-semibold text-slate-600">
                                            {{ mb_substr($player->full_name ?? $player->name ?? 'P', 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-lg text-slate-950 truncate">
                                            {{ $player->full_name ?? $player->name }}
                                        </div>
                                        <div class="text-sm text-slate-500 truncate">
                                            {{ $player->club?->name ?? 'Club non renseigné' }}
                                            @if($player->position) · {{ $player->position }} @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    @if($dossier)
                                        <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                            Dossier existant
                                        </span>
                                    @else
                                        <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold">
                                            Premier dossier à initialiser
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Rendez-vous</div>
                                <div class="mt-1 text-sm font-medium text-slate-800">
                                    {{ $appointment->appointment_date?->format('d/m/Y H:i') }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">{{ $appointment->type_label }}</div>
                            </div>

                            <div class="min-w-0">
                                <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Pré-accueil</div>
                                <div class="mt-1 text-sm font-medium text-slate-800">
                                    {{ data_get($preIntake, 'reason_confirmed') ?: $appointment->reason ?: 'Motif non précisé' }}
                                </div>
                                @if(data_get($preIntake, 'symptoms_summary'))
                                    <div class="mt-1 text-sm text-slate-500 line-clamp-2">
                                        {{ data_get($preIntake, 'symptoms_summary') }}
                                    </div>
                                @endif

                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @if($allergies)
                                        <span class="px-2 py-1 rounded-md bg-red-50 text-red-700 text-xs font-medium">
                                            Allergies déclarées
                                        </span>
                                    @endif
                                    @if($medications)
                                        <span class="px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium">
                                            Traitements déclarés
                                        </span>
                                    @endif
                                    @if($visit?->documents?->count())
                                        <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-medium">
                                            {{ $visit->documents->count() }} document(s)
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <form method="POST" action="{{ route('secretary.appointments.receive', $appointment) }}" class="lg:justify-self-end">
                                @csrf
                                <button class="w-full lg:w-auto px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                                    Recevoir et ouvrir le dossier
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-xl">✓</div>
                        <h3 class="mt-4 font-semibold text-slate-900">Aucun patient en attente</h3>
                        <p class="mt-1 text-sm text-slate-500 max-w-md mx-auto">
                            Les joueurs apparaîtront ici après leur pré-accueil par le secrétariat médical.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        <div class="mt-5 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">
            Le médecin ne sélectionne pas un joueur dans un annuaire : le secrétariat prépare le patient et le transmet à cette file.
        </div>
    </div>
</div>
@endsection
