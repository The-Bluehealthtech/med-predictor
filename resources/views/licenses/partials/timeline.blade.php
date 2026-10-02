{{-- Historique de la demande (suivi partagé club / fédération). $license --}}
<ol class="relative ml-2 border-l border-slate-200 text-sm" data-license-timeline>
    @forelse($license->events as $event)
        <li class="mb-3 ml-4">
            <span class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full border-2 border-white {{ in_array($event->action, ['approved'], true) ? 'bg-emerald-500' : (in_array($event->action, ['rejected'], true) ? 'bg-red-500' : (in_array($event->action, ['info_requested'], true) ? 'bg-orange-500' : 'bg-slate-400')) }}" aria-hidden="true"></span>
            <p class="font-medium text-slate-900">{{ $event->label() }}</p>
            <p class="text-xs text-slate-500">{{ $event->created_at?->format('d/m/Y à H:i') }}{{ $event->user ? ' · ' . $event->user->name : '' }}</p>
            @if($event->message)<p class="mt-0.5 text-slate-700">{{ $event->message }}</p>@endif
        </li>
    @empty
        <li class="ml-4 text-slate-500">Aucun événement enregistré (demande antérieure au suivi).</li>
    @endforelse
</ol>
