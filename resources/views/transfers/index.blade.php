@extends('layouts.app')

@section('title', 'Transferts - FIT')

@section('content')
@php
    $collection = $transfers->getCollection();
    $readyCount = $collection->where('tms_sync_status','ready')->count();
    $linkedCount = $collection->filter(fn($t)=>in_array($t->tms_sync_status,['linked','synced'],true))->count();
    $blockedCount = $collection->filter(fn($t)=>!($t->tms_readiness_summary['ready'] ?? false))->count();
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <x-page-header
        title="Transferts"
        subtitle="FIT prépare et contrôle les dossiers ; FIFA TMS exécute les transferts et reste la source externe des statuts TMS/ITC."
        eyebrow="Football operations · TMS"
    >
        @if($canCreateTransfer)
            <x-slot:actions>
                <a href="{{ route('transfers.create') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Nouveau transfert</a>
            </x-slot:actions>
        @endif
    </x-page-header>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Dossiers</p>
            <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $transfers->total() }}</p>
        </div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-blue-700">Prêts pour TMS</p>
            <p class="mt-1 text-2xl font-semibold text-blue-950">{{ $readyCount }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Liés / synchronisés</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-950">{{ $linkedCount }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-amber-700">Avec blocages</p>
            <p class="mt-1 text-2xl font-semibold text-amber-950">{{ $blockedCount }}</p>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <form method="GET" action="{{ route('transfers.index') }}" class="grid gap-3 md:grid-cols-4">
            <label class="text-sm font-medium text-slate-700">Statut FIT
                <select name="status" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    <option value="">Tous</option>
                    @foreach(['draft'=>'Brouillon','pending'=>'En attente','submitted'=>'Soumis','under_review'=>'En revue','approved'=>'Approuvé','rejected'=>'Refusé','cancelled'=>'Annulé'] as $value=>$label)
                        <option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-medium text-slate-700">Type
                <select name="type" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    <option value="">Tous</option>
                    <option value="permanent" @selected(request('type')==='permanent')>Permanent</option>
                    <option value="loan" @selected(request('type')==='loan')>Prêt</option>
                    <option value="free_agent" @selected(request('type')==='free_agent')>Joueur libre</option>
                </select>
            </label>
            <label class="text-sm font-medium text-slate-700">Club
                <select name="club_id" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    <option value="">Tous</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" @selected((string)request('club_id')===(string)$club->id)>{{ $club->name }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex items-end gap-2">
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filtrer</button>
                @if(request()->hasAny(['status','type','club_id','player_id']))
                    <a href="{{ route('transfers.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Réinitialiser</a>
                @endif
            </div>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-semibold text-slate-900">Dossiers de transfert</h2>
            <p class="mt-1 text-sm text-slate-500">Chaque ligne montre où se trouve le dossier, ce qui bloque et l’état de rattachement TMS.</p>
        </div>

        @forelse($transfers as $transfer)
            @php
                $readiness = $transfer->tms_readiness_summary ?? ['ready'=>false,'blockers'=>[]];
                $blockers = collect($readiness['blockers'] ?? []);
                $tmsStatus = $transfer->tms_sync_status ?: 'not_ready';
                $tmsLabels = [
                    'not_ready'=>'À préparer',
                    'ready'=>'Prêt pour TMS',
                    'linked'=>'Référence TMS liée',
                    'synced'=>'Synchronisé TMS',
                    'stale'=>'À resynchroniser',
                ];
                $tmsClasses = [
                    'not_ready'=>'bg-slate-100 text-slate-700',
                    'ready'=>'bg-blue-100 text-blue-800',
                    'linked'=>'bg-cyan-100 text-cyan-800',
                    'synced'=>'bg-emerald-100 text-emerald-800',
                    'stale'=>'bg-amber-100 text-amber-800',
                ];
            @endphp
            <article class="border-b border-slate-100 p-5 last:border-b-0">
                <div class="grid gap-5 lg:grid-cols-[1.2fr_1.4fr_1fr_auto] lg:items-center">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $transfer->is_international ? 'International' : 'National' }} · {{ ucfirst(str_replace('_',' ',$transfer->transfer_type)) }}</p>
                        <h3 class="mt-1 font-semibold text-slate-900">{{ $transfer->player?->full_name ?: trim(($transfer->player?->first_name ?? '').' '.($transfer->player?->last_name ?? '')) }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $transfer->clubOrigin?->name ?? 'Club origine' }} → {{ $transfer->clubDestination?->name ?? 'Club destination' }}</p>
                    </div>

                    <div>
                        @if($blockers->isEmpty())
                            <p class="text-sm font-semibold text-emerald-700">Aucun blocage FIT pour la préparation TMS</p>
                        @else
                            <p class="text-sm font-semibold text-amber-800">{{ $blockers->count() }} blocage(s)</p>
                            <p class="mt-1 text-xs text-slate-600">{{ $blockers->take(2)->pluck('label')->implode(' · ') }}@if($blockers->count()>2) · +{{ $blockers->count()-2 }}@endif</p>
                        @endif
                        @if($transfer->transfer_fee)
                            <p class="mt-2 text-xs text-slate-500">Indemnité : {{ number_format((float)$transfer->transfer_fee,2,',',' ') }} {{ $transfer->currency }}</p>
                        @endif
                    </div>

                    <div>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $tmsClasses[$tmsStatus] ?? 'bg-slate-100 text-slate-700' }}">{{ $tmsLabels[$tmsStatus] ?? ucfirst(str_replace('_',' ',$tmsStatus)) }}</span>
                        @if($transfer->tms_transfer_id)
                            <p class="mt-2 font-mono text-xs text-slate-500">{{ $transfer->tms_transfer_id }}</p>
                        @elseif($transfer->tms_payload_sha256)
                            <p class="mt-2 font-mono text-xs text-slate-400">SHA-256 {{ substr($transfer->tms_payload_sha256,0,12) }}…</p>
                        @endif
                    </div>

                    <div class="lg:text-right">
                        <a href="{{ route('transfers.show',$transfer) }}" class="inline-flex rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Ouvrir le dossier</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="px-5 py-12 text-center">
                <p class="font-medium text-slate-900">Aucun transfert trouvé.</p>
                <p class="mt-1 text-sm text-slate-500">Ajustez les filtres ou créez un nouveau dossier si votre rôle l’autorise.</p>
            </div>
        @endforelse

        @if($transfers->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $transfers->links() }}</div>
        @endif
    </section>
</div>
@endsection
