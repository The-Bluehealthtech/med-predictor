@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'backHref' => null,
    'backLabel' => null,
    'count' => null,
    'countLabel' => null,
    'dark' => false,
    // Pages autonomes (sans layouts.app, dont la barre du haut a déjà le bouton) : bouton de déconnexion.
    'logout' => false,
])

@php
    $surface = $dark
        ? 'bg-gray-800 border-gray-700 text-white'
        : 'bg-white border-slate-200 text-slate-950';
    $muted = $dark ? 'text-gray-300' : 'text-slate-600';
    $eyebrowTone = $dark
        ? 'bg-gray-700 text-gray-200 border-gray-600'
        : 'bg-slate-50 text-slate-600 border-slate-200';
    $actionBorder = $dark ? 'border-gray-600 text-gray-200 hover:bg-gray-700' : 'border-slate-200 text-slate-700 hover:bg-slate-50';
@endphp

<header {{ $attributes->merge(['class' => "mb-6 rounded-2xl border shadow-sm px-5 py-5 sm:px-6 {$surface}"]) }}>
    @if($backHref)
        <a href="{{ $backHref }}"
           class="inline-flex items-center gap-1.5 text-sm font-medium {{ $muted }} hover:underline mb-4">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path d="m15 18-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ $backLabel ?: __('Retour') }}
        </a>
    @endif

    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="min-w-0">
            @if($eyebrow)
                <div class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.12em] {{ $eyebrowTone }} mb-3">
                    {{ $eyebrow }}
                </div>
            @endif

            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight {{ $dark ? 'text-white' : 'text-slate-950' }}">
                {{ $title }}
            </h1>

            @if($subtitle)
                <p class="mt-2 max-w-3xl text-sm sm:text-base leading-6 {{ $muted }}">
                    {{ $subtitle }}
                </p>
            @endif

            @isset($meta)
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm {{ $muted }}">
                    {{ $meta }}
                </div>
            @endisset
        </div>

        <div class="flex flex-wrap items-center gap-2 lg:justify-end">
            @if($count !== null)
                <div class="rounded-xl border px-3.5 py-2 text-sm {{ $actionBorder }}">
                    <span class="font-semibold {{ $dark ? 'text-white' : 'text-slate-950' }}">{{ $count }}</span>
                    @if($countLabel)<span class="ml-1">{{ $countLabel }}</span>@endif
                </div>
            @endif

            @isset($actions)
                {{ $actions }}
            @endisset

            @if($logout)
                @include('partials.logout-button', ['variant' => $dark ? 'dark' : 'chip'])
            @endif
        </div>
    </div>
</header>
