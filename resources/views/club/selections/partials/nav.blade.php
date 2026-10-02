{{-- Bandeau de l'espace club (sélections nationales). $active : index | returns | api --}}
@php
    $tabs = [
        'index' => ['Convocations reçues', route('club.selections.index')],
        'returns' => ['Retours de sélection', route('club.selections.returns')],
        'api' => ['Accès API', route('club.selections.api-access')],
    ];
@endphp
<div class="rounded-xl bg-emerald-700 text-white shadow">
    <div class="px-5 pt-4 pb-3 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="text-2xl" aria-hidden="true">🎽</span>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-widest text-emerald-200">Espace club</p>
                <p class="text-lg font-bold leading-tight">Sélections nationales</p>
            </div>
        </div>
        <a href="{{ route('modules.index') }}" class="text-sm text-emerald-100 hover:text-white">← Modules</a>
    </div>
    <nav class="px-3 flex flex-wrap gap-1" aria-label="Espace club">
        @foreach($tabs as $key => [$label, $url])
            <a href="{{ $url }}" @if($active === $key) aria-current="page" @endif
               class="px-4 py-2 text-sm font-medium rounded-t-lg {{ $active === $key ? 'bg-white text-emerald-800' : 'text-emerald-100 hover:bg-emerald-600' }}">{{ $label }}</a>
        @endforeach
    </nav>
</div>
