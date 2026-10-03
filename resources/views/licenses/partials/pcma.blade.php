{{-- Aptitude médicale (PCMA) d'une demande : exigence et état. Statut, date et conclusion seulement. $pcma --}}
@php
    $tone = $pcma['required']
        ? ($pcma['status']['valid'] ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-red-200 bg-red-50 text-red-900')
        : 'border-slate-200 bg-slate-50 text-slate-800';
@endphp
<div class="rounded-xl border px-3 py-2 text-sm {{ $tone }}" data-pcma-state="{{ $pcma['status']['state'] }}" data-pcma-required="{{ $pcma['required'] ? '1' : '0' }}">
    <p class="font-semibold">{{ $pcma['required'] ? ($pcma['status']['valid'] ? '✓ ' : '! ') : '' }}{{ $pcma['status']['label'] }}{{ $pcma['status']['date'] ? ' · bilan du ' . $pcma['status']['date']->format('d/m/Y') : '' }}</p>
    @if(!empty($pcma['status']['visit_date']))<p class="text-xs">Réalisé lors de la visite médicale PCMA du {{ $pcma['status']['visit_date']->format('d/m/Y') }}.</p>@endif
    <p class="text-xs">{{ $pcma['reason'] }}</p>
    @if($pcma['blocking'])<p class="mt-1 text-xs font-semibold">L'approbation est bloquée tant qu'un PCMA signé « apte », de moins de {{ config('licensing.pcma.validity_months') }} mois, n'est pas enregistré. Le secrétariat médical planifie une visite PCMA ; le médecin signe le bilan pendant la visite.</p>@endif
</div>
