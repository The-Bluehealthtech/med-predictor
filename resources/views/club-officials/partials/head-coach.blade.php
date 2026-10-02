{{-- Encart de la fiche club : entraîneur principal au format FIFA Connect. --}}
@php
    $officials = app(\App\Services\ClubOfficials\ClubOfficials::class);
    $coach = $officials->headCoach($club);
@endphp
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-2">@include('modules.partials.icon', ['name' => 'whistle', 'class' => 'w-5 h-5 text-slate-600']) Entraîneur principal <span class="text-xs font-normal text-gray-400">format FIFA Connect</span></h3>
        @if(auth()->check() && $officials->canView(auth()->user(), $club))
            <a href="{{ route('club-officials.club', $club) }}" class="text-sm text-blue-600 hover:text-blue-800">Dirigeants et staff du club →</a>
        @endif
    </div>
    @if($coach)
        <style>.fc-table{width:100%;font-size:.85rem;border-collapse:collapse}.fc-table th{text-align:left;font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;color:#475569;background:#f8fafc;padding:.4rem .5rem}.fc-table td{padding:.35rem .5rem;border-bottom:1px solid #f1f5f9;vertical-align:top}.fc-k{color:#64748b;font-family:ui-monospace,monospace;font-size:.75rem;width:18%}.fc-empty{color:#94a3b8;font-style:italic}</style>
        <p class="text-lg font-semibold text-gray-900 mb-2">{{ $coach->fullName() }}</p>
        @include('club-officials.partials._sheet', ['official' => $coach])
    @else
        <p class="text-sm text-gray-500">Aucun entraîneur principal enregistré pour ce club.
            @if(auth()->check() && $officials->canManage(auth()->user(), $club))
                <a href="{{ route('club-officials.create', [$club, 'type' => 'TeamOfficial']) }}" class="text-blue-600 hover:text-blue-800">Ajouter la fiche de l'entraîneur</a>
            @endif
        </p>
    @endif
</div>
