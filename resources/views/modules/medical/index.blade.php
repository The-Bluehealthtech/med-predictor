@extends('layouts.app')

@section('title', 'Poste de travail médical - FIT')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7">

        <header class="mb-6">
            <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-5">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full mb-3">
                        Médical
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-950">Poste de travail médical</h1>
                    <p class="mt-2 text-slate-600 max-w-2xl">
                        Recherchez un joueur, ouvrez son tableau de bord santé ou initialisez son dossier médical de base.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('health-records.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-sm hover:bg-blue-700">
                        <span class="text-lg leading-none">+</span>
                        Initialiser un dossier
                    </a>
                </div>
            </div>
        </header>

        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 sm:p-5 mb-5">
            <form method="get" class="flex flex-col lg:flex-row gap-3 lg:items-center">
                <div class="flex-1 relative">
                    <label for="medical-search" class="sr-only">Rechercher un joueur</label>
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="7" stroke-width="2"></circle>
                        <path d="m20 20-3.5-3.5" stroke-width="2" stroke-linecap="round"></path>
                    </svg>
                    <input id="medical-search"
                           type="search"
                           name="q"
                           value="{{ $search }}"
                           maxlength="200"
                           placeholder="Rechercher par nom ou prénom..."
                           class="w-full pl-11 pr-4 py-3 rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button class="px-5 py-3 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                    Rechercher
                </button>
                @if($search !== '')
                    <a href="{{ route('modules.medical.index') }}"
                       class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 text-center hover:bg-slate-50">
                        Effacer
                    </a>
                @endif
            </form>
        </section>

        @if($waitingAppointments->count())
        <section class="bg-white border border-amber-200 rounded-2xl shadow-sm overflow-hidden mb-5">
            <div class="px-5 py-4 border-b border-amber-100 bg-amber-50/60 flex items-center justify-between gap-3">
                <div>
                    <div class="text-xs uppercase tracking-wide font-semibold text-amber-700">Salle d’attente médicale</div>
                    <h2 class="font-semibold text-slate-950 mt-1">{{ $waitingAppointments->count() }} joueur(s) prêt(s) à être reçu(s)</h2>
                    <p class="text-sm text-slate-600 mt-1">Pré-accueil terminé par le secrétariat. Les informations et documents sont déjà disponibles.</p>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($waitingAppointments as $appointment)
                    @php
                        $waitingPlayer = $appointment->athlete->player;
                        $waitingVisit = $appointment->visit;
                        $preIntake = data_get($waitingVisit?->administrative_data, 'pre_intake', []);
                    @endphp
                    <div class="px-5 py-4 grid grid-cols-1 lg:grid-cols-[1fr_220px_1.2fr_auto] gap-4 lg:items-center">
                        <div>
                            <div class="font-semibold text-slate-950">{{ $waitingPlayer->full_name ?? $waitingPlayer->name }}</div>
                            <div class="text-sm text-slate-500 mt-0.5">{{ $waitingPlayer->club?->name ?? 'Club non renseigné' }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Rendez-vous</div>
                            <div class="text-sm text-slate-800 mt-1">{{ $appointment->appointment_date?->format('d/m/Y H:i') }}</div>
                            <div class="text-xs text-slate-500 mt-1">{{ $appointment->type_label }}</div>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Pré-accueil</div>
                            <div class="text-sm text-slate-700 mt-1 line-clamp-2">
                                {{ data_get($preIntake, 'reason_confirmed') ?: $appointment->reason ?: 'Motif non précisé' }}
                            </div>
                            @if($waitingVisit?->documents?->count())
                                <div class="text-xs text-blue-700 font-medium mt-1">{{ $waitingVisit->documents->count() }} document(s) joint(s)</div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('secretary.appointments.receive', $appointment) }}" class="lg:justify-self-end">
                            @csrf
                            <button class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                                Recevoir le joueur
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <section class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6" aria-label="Indicateurs médicaux">
            <div class="bg-white border border-slate-200 rounded-2xl p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Aujourd'hui</div>
                        <div class="mt-1 text-2xl font-bold text-slate-950">{{ $todayVisits }}</div>
                        <div class="mt-1 text-sm text-slate-600">consultation(s)</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-700 text-xl">🩺</div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">À revoir</div>
                        <div class="mt-1 text-2xl font-bold {{ $followUpsDue > 0 ? 'text-amber-700' : 'text-slate-950' }}">{{ $followUpsDue }}</div>
                        <div class="mt-1 text-sm text-slate-600">suivi(s) arrivé(s) à échéance</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-700 text-xl">⏱</div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Patients</div>
                        <div class="mt-1 text-2xl font-bold text-slate-950">{{ $patientsFollowed }}</div>
                        <div class="mt-1 text-sm text-slate-600">joueur(s) suivi(s)</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700 text-xl">👥</div>
                </div>
            </div>
        </section>

        <div class="space-y-5">
            <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="font-semibold text-slate-950">Patients</h2>
                        <p class="text-sm text-slate-500 mt-0.5">
                            Ouvrez le dossier pour consulter la synthèse, l'historique, les examens et les documents.
                        </p>
                    </div>
                    <div class="text-xs text-slate-400">{{ $players->total() }} résultat(s)</div>
                </div>

                @forelse($players as $player)
                    @php
                        $dossier = $player->baseHealthRecord;
                        $latest = $player->latestHealthRecord;
                        $allergies = $latest?->allergies ?? [];
                        $allergyLabel = '';
                        if (is_array($allergies) && count($allergies)) {
                            $allergyLabel = collect($allergies)->map(fn($v) => is_scalar($v) ? $v : null)->filter()->take(2)->implode(', ');
                        }
                        $followUpDate = $latest?->next_checkup_date;
                        $followUpDue = $followUpDate && $followUpDate->isPast();
                    @endphp

                    <article class="relative px-5 sm:px-6 py-5 border-b border-slate-100 last:border-b-0 hover:bg-slate-50/70 transition-colors">
                        <div class="grid grid-cols-1 lg:grid-cols-[minmax(240px,1.1fr)_minmax(220px,1fr)_180px_auto] gap-4 lg:items-center">

                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                                    @if($player->player_picture_url)
                                        <img src="{{ $player->player_picture_url }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <span class="font-semibold text-slate-600">
                                            {{ mb_substr($player->full_name ?? $player->name ?? 'P', 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ $dossier ? route('health-records.show', $dossier) : route('health-records.create', ['player_id' => $player->id]) }}"
                                       class="font-semibold text-slate-950 truncate hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
                                        {{ $player->full_name ?? $player->name }}
                                    </a>
                                    <div class="mt-0.5 text-sm text-slate-500 truncate">
                                        {{ $player->club?->name ?? 'Club non renseigné' }}
                                        @if($player->position) · {{ $player->position }} @endif
                                    </div>
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @if($allergyLabel)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-red-50 text-red-700 text-xs font-medium">
                                                ⚠ Allergie : {{ $allergyLabel }}
                                            </span>
                                        @endif
                                        @if($followUpDue)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-amber-50 text-amber-800 text-xs font-medium">
                                                Suivi à revoir
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="min-w-0">
                                <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Dernière visite</div>
                                @if($latest)
                                    <div class="mt-1 text-sm font-medium text-slate-800">
                                        {{ $latest->visit_date?->format('d/m/Y') ?? $latest->record_date?->format('d/m/Y') ?? '—' }}
                                        @if($latest->visit_type)
                                            · {{ ucfirst(str_replace('_', ' ', $latest->visit_type)) }}
                                        @endif
                                    </div>
                                    <div class="mt-1 text-sm text-slate-500 line-clamp-2">
                                        {{ $latest->chief_complaint ?: $latest->diagnosis ?: 'Motif non renseigné' }}
                                    </div>
                                @else
                                    <div class="mt-1 text-sm text-slate-500">Aucune visite enregistrée</div>
                                @endif
                            </div>

                            <div>
                                <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Prochain suivi</div>
                                @if($followUpDate)
                                    <div class="mt-1 text-sm font-medium {{ $followUpDue ? 'text-amber-800' : 'text-slate-800' }}">
                                        {{ $followUpDate->format('d/m/Y') }}
                                    </div>
                                @else
                                    <div class="mt-1 text-sm text-slate-400">Non planifié</div>
                                @endif
                                <div class="mt-1 text-xs text-slate-400">{{ $player->health_records_count }} visite(s)</div>
                            </div>

                            <div class="flex flex-wrap gap-2 lg:justify-self-end">
                                @if($dossier)
                                    <a href="{{ route('health-records.show', $dossier) }}"
                                       class="inline-flex justify-center px-3.5 py-2 rounded-lg bg-blue-600 text-sm font-semibold text-white hover:bg-blue-700">
                                        Tableau de bord santé
                                    </a>
                                @else
                                    <a href="{{ route('health-records.create', ['player_id' => $player->id]) }}"
                                       class="inline-flex justify-center px-3.5 py-2 rounded-lg bg-blue-600 text-sm font-semibold text-white hover:bg-blue-700">
                                        Initialiser le dossier
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="px-6 py-16 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-xl">🔎</div>
                        <h3 class="mt-4 font-semibold text-slate-900">Aucun joueur trouvé</h3>
                        <p class="mt-1 text-sm text-slate-500">Modifiez la recherche ou démarrez une nouvelle consultation.</p>
                    </div>
                @endforelse

                @if($players->hasPages())
                    <div class="px-5 sm:px-6 py-4 border-t border-slate-200 bg-slate-50">
                        {{ $players->links() }}
                    </div>
                @endif
            </section>

            <details class="bg-white border border-slate-200 rounded-2xl shadow-sm group">
                <summary class="list-none cursor-pointer px-5 py-4 flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-slate-800">Examens et démarches spécialisées</div>
                        <div class="text-xs text-slate-500 mt-0.5">PCMA et AUT restent disponibles sans encombrer le parcours principal.</div>
                    </div>
                    <span class="text-slate-400 transition-transform group-open:rotate-90">→</span>
                </summary>
                <div class="px-5 pb-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('pcma.create') }}" class="px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50">
                        <div class="text-sm font-semibold text-slate-800">PCMA</div>
                        <div class="text-xs text-slate-500 mt-1">Examen médical structuré</div>
                    </a>
                    <a href="{{ route('medical-aut.choose') }}" class="px-4 py-3 rounded-xl border border-slate-200 hover:bg-slate-50">
                        <div class="text-sm font-semibold text-slate-800">AUT</div>
                        <div class="text-xs text-slate-500 mt-1">Autorisation thérapeutique</div>
                    </a>
                </div>
            </details>
        </div>
    </div>
</div>
@endsection
