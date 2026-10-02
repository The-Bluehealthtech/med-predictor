{{-- Bandeau de l'espace fédération (DTN). $active : selections | players | api --}}
@php
    $tabs = [
        'selections' => ['Convocations et retours', route('dtn.index')],
        'players' => ['Fiches joueurs', route('dtn.players.index')],
        'api' => ['Accès API', route('dtn.api-access')],
    ];
@endphp
<div class="rounded-xl bg-indigo-700 text-white shadow">
    <div class="px-5 pt-4 pb-3 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white/15">@include('modules.partials.icon', ['name' => 'flag', 'class' => 'w-6 h-6'])</span>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-widest text-indigo-200">Espace fédération</p>
                <p class="text-lg font-bold leading-tight">Direction technique nationale</p>
            </div>
        </div>
        <a href="{{ route('modules.index') }}" class="text-sm text-indigo-100 hover:text-white">← Modules</a>
    </div>
    <nav class="px-3 flex flex-wrap gap-1" aria-label="Espace fédération">
        @foreach($tabs as $key => [$label, $url])
            <a href="{{ $url }}" @if($active === $key) aria-current="page" @endif
               class="px-4 py-2 text-sm font-medium rounded-t-lg {{ $active === $key ? 'bg-white text-indigo-800' : 'text-indigo-100 hover:bg-indigo-600' }}">{{ $label }}</a>
        @endforeach
    </nav>
</div>
