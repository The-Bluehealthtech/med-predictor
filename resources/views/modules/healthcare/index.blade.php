@extends('layouts.app')

@section('title', 'Dossiers médicaux - FIT')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7">
        <header class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full mb-3">
                        Dossiers médicaux
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-950">Dossiers santé des joueurs</h1>
                    <p class="mt-2 text-slate-600 max-w-2xl">
                        Recherchez un joueur et ouvrez son dossier longitudinal : synthèse, parcours de soins, modules spécialisés et documents.
                    </p>
                </div>
                <div class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm text-slate-600">
                    <span class="font-semibold text-slate-950">{{ $players->total() }}</span>
                    dossier(s)
                </div>
            </div>
        </header>

        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 sm:p-5 mb-5">
            <form method="GET" action="{{ route('modules.healthcare.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <label for="healthcare-search" class="sr-only">Rechercher un joueur</label>
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="7" stroke-width="2"></circle>
                        <path d="m20 20-3.5-3.5" stroke-width="2" stroke-linecap="round"></path>
                    </svg>
                    <input id="healthcare-search" type="search" name="q" value="{{ $search }}"
                           placeholder="Rechercher un joueur par nom ou prénom..."
                           class="w-full pl-11 pr-4 py-3 rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <button class="px-5 py-3 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                    Rechercher
                </button>
                @if($search !== '')
                    <a href="{{ route('modules.healthcare.index') }}"
                       class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 text-center hover:bg-slate-50">
                        Effacer
                    </a>
                @endif
            </form>
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50/70">
                <h2 class="font-semibold text-slate-950">Joueurs disposant d’un dossier médical</h2>
                <p class="text-sm text-slate-500 mt-1">Un joueur correspond à un dossier longitudinal canonique.</p>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($players as $player)
                    @php
                        $dossier = $player->baseHealthRecord;
                        $latest = $player->latestHealthRecord;
                        $latestDate = $latest?->visit_date ?? $latest?->record_date;
                    @endphp
                    <article class="px-5 sm:px-6 py-5 hover:bg-slate-50/70 transition-colors">
                        <div class="grid grid-cols-1 lg:grid-cols-[minmax(240px,1fr)_190px_minmax(240px,1fr)_auto] gap-4 lg:items-center">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0">
                                    <span class="font-semibold text-emerald-700">
                                        {{ mb_substr($player->full_name ?? $player->name ?? 'P', 0, 1) }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-950 truncate">{{ $player->full_name ?? $player->name }}</div>
                                    <div class="mt-0.5 text-sm text-slate-500 truncate">
                                        {{ $player->club?->name ?? 'Club non renseigné' }}
                                        @if($player->position) · {{ $player->position }} @endif
                                    </div>
                                    <div class="mt-2 text-xs text-slate-400">Dossier #{{ $dossier?->id }}</div>
                                </div>
                            </div>

                            <div>
                                <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Ouvert le</div>
                                <div class="mt-1 text-sm font-medium text-slate-800">
                                    {{ $dossier?->record_date?->format('d/m/Y') ?? '—' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-400">{{ $player->health_records_count }} épisode(s)</div>
                            </div>

                            <div class="min-w-0">
                                <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Dernière activité</div>
                                <div class="mt-1 text-sm font-medium text-slate-800">
                                    {{ $latestDate?->format('d/m/Y') ?? '—' }}
                                </div>
                                <div class="mt-1 text-sm text-slate-500 line-clamp-2">
                                    {{ $latest?->chief_complaint ?: $latest?->diagnosis ?: 'Aucun résumé clinique disponible' }}
                                </div>
                            </div>

                            <div class="lg:justify-self-end">
                                <a href="{{ route('health-records.show', $dossier) }}"
                                   class="inline-flex w-full lg:w-auto justify-center items-center px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                                    Ouvrir le dossier
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-xl">📁</div>
                        <h3 class="mt-4 font-semibold text-slate-900">Aucun dossier médical trouvé</h3>
                        <p class="mt-1 text-sm text-slate-500 max-w-md mx-auto">
                            Les dossiers apparaissent ici après leur initialisation dans le parcours de prise en charge médicale.
                        </p>
                    </div>
                @endforelse
            </div>

            @if($players->hasPages())
                <div class="px-5 sm:px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $players->links() }}
                </div>
            @endif
        </section>

        <div class="mt-5 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">
            La création d’un dossier ne se fait pas ici : elle résulte de la première prise en charge médicale du joueur.
        </div>
    </div>
</div>
@endsection
