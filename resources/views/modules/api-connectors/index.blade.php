@extends('layouts.app')

@section('title', 'Configuration des API - FIT')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        title="Configuration des API"
        subtitle="État des connecteurs externes de FIT. Les secrets ne sont jamais affichés dans cette interface."
        eyebrow="Administration · outils transverses"
        :back-href="route('modules.index')"
        back-label="Retour aux modules"
    />

    @php
        $ready = collect($connectors)->where('status', 'ready')->count();
        $attention = collect($connectors)->whereNotIn('status', ['ready'])->count();
        $tones = [
            'ready' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
            'partial' => 'bg-blue-50 text-blue-800 ring-blue-200',
            'disabled' => 'bg-slate-100 text-slate-700 ring-slate-200',
            'sdk_required' => 'bg-amber-50 text-amber-800 ring-amber-200',
            'not_configured' => 'bg-amber-50 text-amber-800 ring-amber-200',
        ];
    @endphp

    @if(session('success'))<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ session('error') }}</div>@endif

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Connecteurs prêts</p>
            <p class="mt-1 text-3xl font-semibold text-emerald-950">{{ $ready }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-amber-700">À configurer ou valider</p>
            <p class="mt-1 text-3xl font-semibold text-amber-950">{{ $attention }}</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        @foreach($connectors as $connector)
            <article class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $connector['name'] }}</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ $connector['usage'] }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $tones[$connector['status']] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                        {{ $connector['label'] }}
                    </span>
                </div>

                <div class="mt-4 border-t border-slate-100 pt-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Variables attendues</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($connector['variables'] as $variable)
                            <code class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs text-slate-700">{{ $variable }}</code>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs text-slate-500">Les valeurs secrètes restent dans le gestionnaire de secrets du runtime et ne sont pas révélées ici.</p>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                    <form method="POST" action="{{ route('modules.api-connectors.test', $connector['slug']) }}">
                        @csrf
                        <button type="submit" class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Tester</button>
                    </form>
                    @if($connector['enabled'])
                        <form method="POST" action="{{ route('modules.api-connectors.activation', $connector['slug']) }}">
                            @csrf
                            <input type="hidden" name="enabled" value="0">
                            <button type="submit" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">Désactiver</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('modules.api-connectors.activation', $connector['slug']) }}">
                            @csrf
                            <input type="hidden" name="enabled" value="1">
                            <button type="submit" @disabled(!$connector['configured']) class="rounded-xl px-3 py-2 text-sm font-semibold {{ $connector['configured'] ? 'bg-slate-900 text-white hover:bg-slate-800' : 'cursor-not-allowed bg-slate-200 text-slate-500' }}">Activer</button>
                        </form>
                    @endif
                    <span class="ml-auto text-xs {{ $connector['runtime_enforced'] ? 'text-emerald-700' : 'text-amber-700' }}">{{ $connector['runtime_enforced'] ? 'Activation appliquée au runtime FIT' : 'État administratif · centralisation runtime à finaliser' }}</span>
                </div>
            </article>
        @endforeach
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="font-semibold text-slate-900">Règle d’activation</h2>
        <p class="mt-1 text-sm text-slate-600">Un connecteur n’est considéré opérationnel qu’après configuration des secrets, test technique contrôlé et validation métier. FIT ne simule jamais une connexion externe absente.</p>
    </section>
</div>
@endsection
