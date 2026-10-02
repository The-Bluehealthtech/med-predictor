{{-- Parcours d'une section de /modules : étapes numérotées (flow) ou deux couloirs (lanes).
     $workflow : configuration ; $items : modules visibles de la section ; $iconTone ; $spaceVisible. --}}
@php
    $byRoute = collect($items)->keyBy('route');
    $tr = fn ($s) => app()->getLocale() === 'en' ? (trans('modules_fit.workflow')[$s] ?? $s) : $s;
    $cardsOf = fn (array $step) => collect($step['routes'])->map(fn ($r) => $byRoute[$r] ?? null)->filter()->values();
    $cols = [3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4', 5 => 'lg:grid-cols-5'];
    $badge = fn (array $step) => $step['tone'] ?? $workflow['tone'];
    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="m9 6 6 6-6 6"/></svg>';
    $todo = $todo ?? [];
    $todoTone = ['action' => 'bg-amber-100 text-amber-800 ring-amber-200', 'info' => 'bg-slate-100 text-slate-700 ring-slate-200'];
    $tools = collect($workflow['tools'] ?? [])->map(fn ($r) => $byRoute[$r] ?? null)->filter()->values();
@endphp

@if($workflow['type'] === 'flow')
    <ol class="grid grid-cols-1 gap-4 {{ $cols[count($workflow['steps'])] ?? 'lg:grid-cols-4' }}">
        @foreach($workflow['steps'] as $i => $step)
            @php $cards = $cardsOf($step); @endphp
            <li class="module-step relative flex flex-col rounded-xl border p-4 {{ $cards->isEmpty() ? 'border-dashed border-gray-200 bg-gray-50' : 'border-gray-200 bg-gray-50/60' }}" data-step="{{ $i + 1 }}">
                @if(!$loop->last)
                    <span class="hidden lg:flex absolute -right-3.5 top-6 z-10 items-center justify-center w-7 h-7 rounded-full bg-white border border-gray-200 text-gray-400" aria-hidden="true">{!! $arrow !!}</span>
                @endif
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white text-sm font-bold {{ $badge($step) }}">{{ $i + 1 }}</span>
                    <span class="font-semibold text-gray-900">{{ $tr($step['label']) }}</span>
                    @if(isset($step['todo'], $todo[$step['todo']]) && $cards->isNotEmpty())
                        <span class="ml-auto inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold ring-1 {{ $todoTone[$todo[$step['todo']]['tone']] ?? $todoTone['info'] }}" data-todo="{{ $step['todo'] }}">{{ $todo[$step['todo']]['count'] }} {{ $tr($todo[$step['todo']]['label']) }}</span>
                    @endif
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $tr($step['role']) }}</p>
                <div class="mt-3 space-y-2 flex-1">
                    @forelse($cards as $item)
                        @include('modules.partials.card', ['item' => $item])
                    @empty
                        <p class="text-xs text-gray-400 italic">{{ $tr('Non accessible avec votre compte') }}</p>
                    @endforelse
                </div>
                <p class="mt-3 pt-2 border-t border-gray-200 text-xs text-gray-600"><span class="text-gray-400">→</span> {{ $tr($step['output']) }}</p>
            </li>
        @endforeach
    </ol>
@else
    {{-- Deux couloirs : chaque compte voit les modules de son couloir ; l'autre couloir n'apparaît que comme étape de contexte, sans module. --}}
    @php $total = count($workflow['steps']); @endphp
    <div class="space-y-3">
        @foreach($workflow['lanes'] as $laneKey => $lane)
            @php $laneOpen = $spaceVisible[$laneKey] ?? false; @endphp
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider {{ $lane['text'] }}">{{ $tr($lane['label']) }}</p>
                <ol class="grid grid-cols-1 gap-3 {{ $cols[$total] ?? 'lg:grid-cols-5' }}">
                    @foreach($workflow['steps'] as $i => $step)
                        @if($step['lane'] !== $laneKey)
                            <li class="hidden lg:block" aria-hidden="true"></li>
                            @continue
                        @endif
                        @php $cards = $laneOpen ? $cardsOf($step) : collect(); @endphp
                        <li class="module-step relative flex flex-col rounded-xl border p-3 {{ $laneOpen ? 'border-gray-200 bg-gray-50/60' : 'border-dashed border-gray-200 bg-gray-50' }}" data-step="{{ $i + 1 }}">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white text-sm font-bold {{ $laneOpen ? $lane['tone'] : 'bg-gray-300' }}">{{ $i + 1 }}</span>
                                <span class="font-semibold text-gray-900">{{ $tr($step['label']) }}</span>
                                @if($laneOpen && isset($step['todo'], $todo[$step['todo']]))
                                    <span class="ml-auto inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold ring-1 {{ $todoTone[$todo[$step['todo']]['tone']] ?? $todoTone['info'] }}" data-todo="{{ $step['todo'] }}">{{ $todo[$step['todo']]['count'] }} {{ $tr($todo[$step['todo']]['label']) }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-gray-500">{{ $tr($step['role']) }}</p>
                            @if($laneOpen)
                                <div class="mt-3 space-y-2 flex-1">
                                    @foreach($cards as $item)
                                        @include('modules.partials.card', ['item' => $item])
                                    @endforeach
                                </div>
                            @endif
                            <p class="mt-3 pt-2 border-t border-gray-200 text-xs text-gray-600"><span class="text-gray-400">→</span> {{ $tr($step['output']) }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endforeach
    </div>
@endif

@if($tools->isNotEmpty())
    <div class="mt-5 pt-4 border-t border-gray-100">
        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $tr($workflow['tools_label'] ?? 'Outils de la section') }}</p>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-4">
            @foreach($tools as $item)
                @include('modules.partials.card', ['item' => $item])
            @endforeach
        </div>
    </div>
@endif
