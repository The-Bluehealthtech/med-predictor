{{-- Pièces justificatives d'une demande : liste exigée (présente ou manquante) et pièces déposées. $license, $required, $missing. --}}
@php
    $labels = config('licensing.documents', []);
    $byType = $license->documents->groupBy('document_type');
    $size = fn ($b) => $b >= 1048576 ? number_format($b / 1048576, 1, ',', ' ') . ' Mo' : max(1, (int) round($b / 1024)) . ' Ko';
@endphp
<ul class="divide-y divide-slate-100 text-sm" data-license-documents>
    @foreach(array_unique(array_merge($required, $byType->keys()->all())) as $type)
        <li class="flex items-start justify-between gap-3 py-2">
            <span class="min-w-0 flex-1">
                <span class="font-medium text-slate-900">{{ $labels[$type] ?? $type }}</span>
                @if(in_array($type, $required, true))<span class="ml-1 text-xs text-slate-500">exigée</span>@endif
                @foreach($byType->get($type, collect()) as $document)
                    <a href="{{ route('licenses.document', $document) }}" target="_blank" rel="noopener" class="block text-xs text-blue-700 hover:underline">{{ $document->original_name }} · {{ $size($document->size) }} · {{ $document->created_at?->format('d/m/Y') }}</a>
                @endforeach
            </span>
            @if($byType->has($type))
                <span class="shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">✓ Fournie</span>
            @else
                <span class="shrink-0 rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-red-200">! Manquante</span>
            @endif
        </li>
    @endforeach
</ul>
