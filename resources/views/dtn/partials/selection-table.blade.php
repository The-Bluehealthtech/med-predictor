{{-- Tableau de sélections d'un espace. $showRoute : route du détail dans l'espace courant ; $counterpart : libellé de l'autre partie. --}}
@php
    $statusColors = [
        'convoked' => 'bg-amber-100 text-amber-800', 'departure_sent' => 'bg-blue-100 text-blue-800', 'in_selection' => 'bg-indigo-100 text-indigo-800',
        'return_sent' => 'bg-emerald-100 text-emerald-800', 'closed' => 'bg-gray-100 text-gray-700', 'cancelled' => 'bg-gray-100 text-gray-500 line-through',
    ];
@endphp
<div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
            <tr>
                <th class="px-5 py-2 text-left">Joueur</th>
                <th class="px-5 py-2 text-left">{{ $counterpart }}</th>
                <th class="px-5 py-2 text-left">Rassemblement</th>
                <th class="px-5 py-2 text-left">Dates</th>
                <th class="px-5 py-2 text-left">Statut</th>
                <th class="px-5 py-2 text-left">Aptitude</th>
                <th class="px-5 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($items as $s)
                @php $st = $s->effectiveStatus(); @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ trim(($s->player->first_name ?? '') . ' ' . ($s->player->last_name ?? '')) }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ $counterpart === 'Club' ? str_replace(' (Démo)', '', $s->club->name ?? '—') : ($s->association->name ?? '—') }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ $s->team_label }} · {{ $s->eventTypeLabel() }}<div class="text-xs text-gray-500">{{ $s->event_name }}@if($s->opponent) — {{ $s->opponent }}@endif</div></td>
                    <td class="px-5 py-3 text-gray-700 whitespace-nowrap">{{ $s->start_date->format('d/m/Y') }} → {{ $s->end_date->format('d/m/Y') }}</td>
                    <td class="px-5 py-3"><span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusColors[$st] ?? 'bg-gray-100' }}">{{ \App\Models\NationalSelection::STATUS_LABELS[$st] ?? $st }}</span></td>
                    <td class="px-5 py-3 text-gray-700">{{ ($s->returnReport && $s->returnReport->isSent() ? $s->returnReport : $s->departure)?->fitnessLabel() ?? 'Non renseigné' }}</td>
                    <td class="px-5 py-3 text-right"><a href="{{ route($showRoute, $s) }}" class="text-blue-600 hover:text-blue-800 font-medium">Ouvrir →</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
