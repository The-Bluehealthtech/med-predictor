@extends('layouts.app')

@section('title', 'Sélections nationales — DTN')

@php
    $statusColors = [
        'convoked' => 'bg-amber-100 text-amber-800',
        'departure_sent' => 'bg-blue-100 text-blue-800',
        'in_selection' => 'bg-indigo-100 text-indigo-800',
        'return_sent' => 'bg-emerald-100 text-emerald-800',
        'closed' => 'bg-gray-100 text-gray-700',
        'cancelled' => 'bg-gray-100 text-gray-500 line-through',
    ];
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $side === 'dtn' ? 'Direction technique nationale' : 'Club' }}</p>
            <h1 class="text-2xl font-bold text-gray-900">Sélections nationales</h1>
            <p class="text-sm text-gray-600 max-w-2xl">Partage de données entre le club et la Direction technique nationale : le club prépare un état de départ avant le rassemblement, la DTN renvoie un état de retour (incidents, performances, risques, indice de performance).</p>
        </div>
        <div class="flex items-center gap-3">
            @if($canConvoke)
                <a href="{{ route('dtn.selections.create') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">+ Convoquer un joueur</a>
            @endif
            <a href="{{ route('modules.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Modules</a>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if(!empty($notInstalled))
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Le module des sélections nationales est en cours d'installation : ses tables seront créées au prochain démarrage de l'application.</div>
    @endif

    @foreach([
        ['À traiter', 'Les sélections qui attendent une action de votre part.', $todo, 'Rien à traiter pour le moment.'],
        ['En cours', 'Convocations envoyées et joueurs en sélection.', $ongoing, 'Aucune sélection en cours.'],
        ['Historique', 'Sélections clôturées ou annulées.', $history, 'Aucune sélection passée.'],
    ] as [$title, $subtitle, $list, $empty])
        <section class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b flex items-baseline justify-between gap-4">
                <div>
                    <h2 class="font-semibold text-gray-900">{{ $title }} <span class="text-gray-400 font-normal">({{ $list->count() }})</span></h2>
                    <p class="text-xs text-gray-500">{{ $subtitle }}</p>
                </div>
            </div>
            @if($list->isEmpty())
                <p class="px-5 py-6 text-sm text-gray-500">{{ $empty }}</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-5 py-2 text-left">Joueur</th>
                                <th class="px-5 py-2 text-left">Club</th>
                                <th class="px-5 py-2 text-left">Rassemblement</th>
                                <th class="px-5 py-2 text-left">Dates</th>
                                <th class="px-5 py-2 text-left">Statut</th>
                                <th class="px-5 py-2 text-left">Aptitude</th>
                                <th class="px-5 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($list as $s)
                                @php $st = $s->effectiveStatus(); @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 font-medium text-gray-900">{{ trim(($s->player->first_name ?? '') . ' ' . ($s->player->last_name ?? '')) }}</td>
                                    <td class="px-5 py-3 text-gray-700">{{ str_replace(' (Démo)', '', $s->club->name ?? '—') }}</td>
                                    <td class="px-5 py-3 text-gray-700">{{ $s->team_label }} · {{ $s->eventTypeLabel() }}<div class="text-xs text-gray-500">{{ $s->event_name }}@if($s->opponent) — {{ $s->opponent }}@endif</div></td>
                                    <td class="px-5 py-3 text-gray-700 whitespace-nowrap">{{ $s->start_date->format('d/m/Y') }} → {{ $s->end_date->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3"><span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusColors[$st] ?? 'bg-gray-100' }}">{{ \App\Models\NationalSelection::STATUS_LABELS[$st] ?? $st }}</span></td>
                                    <td class="px-5 py-3 text-gray-700">{{ ($s->returnReport && $s->returnReport->isSent() ? $s->returnReport : $s->departure)?->fitnessLabel() ?? 'Non renseigné' }}</td>
                                    <td class="px-5 py-3 text-right"><a href="{{ route('dtn.selections.show', $s) }}" class="text-blue-600 hover:text-blue-800 font-medium">Ouvrir →</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endforeach
</div>
@endsection
