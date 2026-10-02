@extends('layouts.app')

@section('title', 'Club — ' . trim(($selection->player->first_name ?? '') . ' ' . ($selection->player->last_name ?? '')))

{{-- Espace club : détail d'une sélection. État de départ rédigé par le club, état de retour reçu de la DTN (lecture). --}}
@section('content')
@php
    $playerName = trim(($selection->player->first_name ?? '') . ' ' . ($selection->player->last_name ?? ''));
    $clubName = str_replace(' (Démo)', '', $selection->club->name ?? '—');
    $status = $selection->effectiveStatus();
    $steps = [
        'convoked' => 'Convocation',
        'departure_sent' => 'État de départ',
        'in_selection' => 'En sélection',
        'return_sent' => 'État de retour',
        'closed' => 'Clôture',
    ];
    $order = array_keys($steps);
    $reached = array_search($status, $order, true);
    $dc = $departure?->content ?? [];
    $rc = $returnReport?->content ?? [];
    $availability = ['available' => 'Disponible', 'available_limited' => 'Disponible avec gestion de la charge', 'unavailable' => 'Indisponible'];
    $input = 'mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm';
@endphp
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    @include('club.selections.partials.nav', ['active' => $selection->effectiveStatus() === 'return_sent' || $selection->effectiveStatus() === 'closed' ? 'returns' : 'index'])
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $selection->team_label }} · {{ $selection->eventTypeLabel() }}@if($selection->is_demo) · <span class="text-amber-700">Données de démonstration</span>@endif</p>
            <h1 class="text-2xl font-bold text-gray-900">{{ $playerName }}</h1>
            <p class="text-sm text-gray-600">{{ $clubName }} → {{ $selection->association->name ?? 'Fédération' }} · {{ $selection->event_name }}@if($selection->opponent) ({{ $selection->opponent }})@endif · du {{ $selection->start_date->format('d/m/Y') }} au {{ $selection->end_date->format('d/m/Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('club.selections.index') }}" class="text-emerald-700 hover:text-emerald-900 text-sm">← Convocations reçues</a>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><ul class="list-disc ml-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    {{-- Étapes --}}
    @if($status === 'cancelled')
        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">Cette convocation a été annulée.</div>
    @else
        <ol class="grid grid-cols-5 gap-2 text-xs">
            @foreach($steps as $key => $label)
                @php $i = array_search($key, $order, true); @endphp
                <li class="rounded-lg px-3 py-2 border {{ $i < $reached ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($i === $reached ? 'bg-emerald-600 border-emerald-600 text-white font-semibold' : 'bg-white border-gray-200 text-gray-500') }}">
                    <span class="block text-[10px] uppercase tracking-wider opacity-80">Étape {{ $i + 1 }}</span>{{ $label }}
                </li>
            @endforeach
        </ol>
    @endif

    @if($selection->convocation_note)
        <div class="bg-white rounded-lg shadow p-5 text-sm"><p class="font-semibold text-gray-900">Message de la DTN</p><p class="text-gray-700 mt-1 whitespace-pre-line">{{ $selection->convocation_note }}</p></div>
    @endif

    {{-- ================= ÉTAT DE DÉPART ================= --}}
    <section class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b flex flex-wrap items-baseline justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">État de départ <span class="text-sm font-normal text-gray-500">— à envoyer à la DTN</span></h2>
                <p class="text-xs text-gray-500">
                    @if($departure?->isSent()) Envoyé le {{ $departure->sent_at?->format('d/m/Y à H:i') }}@if($departure->author) par {{ $departure->author->name }}@endif
                    @else Brouillon — préparé par le club avant le rassemblement @endif
                    @if(!empty($snapshot['generated_at'])) · données du {{ \Carbon\Carbon::parse($snapshot['generated_at'])->format('d/m/Y H:i') }} @endif
                </p>
            </div>
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ ['fit' => 'bg-emerald-100 text-emerald-800', 'fit_with_restrictions' => 'bg-amber-100 text-amber-800', 'unfit' => 'bg-red-100 text-red-800'][$departure?->fitness_status] ?? 'bg-gray-100 text-gray-600' }}">Aptitude : {{ $departure?->fitnessLabel() ?? 'Non renseigné' }}</span>
        </div>

        {{-- Données préparées automatiquement --}}
        <div class="p-5 space-y-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Données du club, préparées automatiquement</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach([
                    ['Forme (5 derniers)', isset($snapshot['form']['rating_last5']) ? number_format($snapshot['form']['rating_last5'], 2, ',', ' ') : '—', 'note moyenne de match'],
                    ['Note de saison', isset($snapshot['form']['rating_season']) ? number_format($snapshot['form']['rating_season'], 2, ',', ' ') : '—', ($snapshot['season']['matches'] ?? 0) . ' matchs'],
                    ['Charge (3 derniers)', ($snapshot['load']['load_last3_pct'] ?? 0) . ' %', ($snapshot['load']['minutes_last3'] ?? 0) . ' min sur 270 possibles'],
                    ['Rôle et apport', isset($snapshot['role_evaluation']['score']) ? number_format($snapshot['role_evaluation']['score'], 1, ',', ' ') : '—', $snapshot['role_evaluation'] ? (($snapshot['role_evaluation']['family'] ?? '') . ' · fiabilité ' . ($snapshot['role_evaluation']['reliability'] ?? '—') . ' %') : 'non évalué'],
                ] as [$label, $value, $sub])
                    <div class="rounded-lg border border-gray-200 p-3"><div class="text-xs text-gray-500">{{ $label }}</div><div class="text-xl font-bold text-gray-900">{{ $value }}</div><div class="text-xs text-gray-500">{{ $sub }}</div></div>
                @endforeach
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-xs uppercase tracking-wider text-gray-500"><tr><th class="py-1 text-left">Derniers matchs</th><th class="py-1 text-left">Score</th><th class="py-1 text-right">Min.</th><th class="py-1 text-right">Note</th><th class="py-1 text-right">B / PD</th><th class="py-1 text-right">Cartons</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($snapshot['last_matches'] ?? [] as $m)
                                <tr><td class="py-1.5">{{ \Carbon\Carbon::parse($m['date'])->format('d/m') }} · {{ $m['opponent'] }}@unless($m['starter']) <span class="text-xs text-gray-400">(remplaçant)</span>@endunless</td><td>{{ $m['score'] }}</td><td class="text-right">{{ $m['minutes'] }}</td><td class="text-right">{{ $m['rating'] !== null ? number_format($m['rating'], 1, ',', ' ') : '—' }}</td><td class="text-right">{{ $m['goals'] }} / {{ $m['assists'] }}</td><td class="text-right">{{ $m['yellow'] ? $m['yellow'] . 'J' : '' }}{{ $m['red'] ? ' ' . $m['red'] . 'R' : '' }}{{ !$m['yellow'] && !$m['red'] ? '—' : '' }}</td></tr>
                            @empty
                                <tr><td colspan="6" class="py-2 text-gray-500">Aucun match enregistré pour ce joueur.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-gray-100 pb-1"><span class="text-gray-600">Saison</span><span class="font-medium">{{ $snapshot['season']['matches'] ?? 0 }} m · {{ number_format($snapshot['season']['minutes'] ?? 0, 0, ',', ' ') }} min</span></div>
                    <div class="flex justify-between border-b border-gray-100 pb-1"><span class="text-gray-600">Buts / passes déc.</span><span class="font-medium">{{ $snapshot['season']['goals'] ?? 0 }} / {{ $snapshot['season']['assists'] ?? 0 }}</span></div>
                    <div class="flex justify-between border-b border-gray-100 pb-1"><span class="text-gray-600">Cartons</span><span class="font-medium">{{ $snapshot['season']['yellow'] ?? 0 }} J · {{ $snapshot['season']['red'] ?? 0 }} R</span></div>
                    <div class="flex justify-between border-b border-gray-100 pb-1"><span class="text-gray-600">Score FIT</span><span class="font-medium">{{ isset($snapshot['fit']['score']) ? number_format($snapshot['fit']['score'], 1, ',', ' ') . ' / 100' : '—' }}</span></div>
                    @if(!empty($snapshot['discipline']['one_yellow_from_suspension']))<p class="text-xs text-amber-700">⚠ À un carton jaune d'une suspension.</p>@endif
                    @if(!empty($snapshot['discipline']['red_last_match']))<p class="text-xs text-red-700">⚠ Carton rouge au dernier match.</p>@endif
                </div>
            </div>
        </div>

        {{-- Saisie du club --}}
        <div class="border-t p-5">
            @if($canEditDeparture ?? false)
                <form method="POST" action="{{ route('club.selections.departure', $selection) }}" class="space-y-4">
                    @csrf
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Informations du staff du club</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="block text-sm font-medium text-gray-700">Disponibilité
                            <select name="availability" class="{{ $input }}"><option value="">—</option>@foreach($availability as $k => $l)<option value="{{ $k }}" @selected(($dc['availability'] ?? '') === $k)>{{ $l }}</option>@endforeach</select>
                        </label>
                        <label class="block text-sm font-medium text-gray-700">Contact au club
                            <input name="contact" value="{{ $dc['contact'] ?? '' }}" maxlength="300" placeholder="Nom, fonction, téléphone ou e-mail" class="{{ $input }}">
                        </label>
                        <label class="block text-sm font-medium text-gray-700">Recommandations de charge
                            <textarea name="load_recommendation" rows="3" maxlength="2000" class="{{ $input }}" placeholder="Temps de jeu conseillé, séances à alléger…">{{ $dc['load_recommendation'] ?? '' }}</textarea>
                        </label>
                        <label class="block text-sm font-medium text-gray-700">Points de vigilance
                            <textarea name="vigilance" rows="3" maxlength="2000" class="{{ $input }}" placeholder="Discipline, fatigue, contexte personnel…">{{ $dc['vigilance'] ?? '' }}</textarea>
                        </label>
                        <label class="block text-sm font-medium text-gray-700 md:col-span-2">Notes techniques et tactiques
                            <textarea name="technical_notes" rows="2" maxlength="2000" class="{{ $input }}" placeholder="Poste et rôle actuels au club, points travaillés…">{{ $dc['technical_notes'] ?? '' }}</textarea>
                        </label>
                    </div>
                    @include('club.selections.partials.medical-departure', ['editable' => $canEditDepartureMedical ?? false, 'medical' => $departureMedical, 'report' => $departure])
                    <div class="flex flex-wrap justify-end gap-3 pt-2">
                        <button name="action" value="refresh" class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Actualiser les données du joueur</button>
                        <button name="action" value="save" class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Enregistrer le brouillon</button>
                        <button name="action" value="send" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700" onclick="return window.confirm('Envoyer l\'état de départ à la DTN ? Il ne pourra plus être modifié.')">Envoyer à la DTN</button>
                    </div>
                </form>
            @else
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Informations du staff du club</p>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    @foreach(['Disponibilité' => $availability[$dc['availability'] ?? ''] ?? '—', 'Contact au club' => $dc['contact'] ?? '—', 'Recommandations de charge' => $dc['load_recommendation'] ?? '—', 'Points de vigilance' => $dc['vigilance'] ?? '—', 'Notes techniques et tactiques' => $dc['technical_notes'] ?? '—'] as $label => $value)
                        <div><dt class="text-gray-500">{{ $label }}</dt><dd class="text-gray-900 whitespace-pre-line">{{ $value !== '' && $value !== null ? $value : '—' }}</dd></div>
                    @endforeach
                </dl>
                @include('club.selections.partials.medical-departure', ['editable' => false, 'medical' => $departureMedical, 'report' => $departure])
            @endif
        </div>
    </section>

    {{-- ================= ÉTAT DE RETOUR ================= --}}
    @if($returnReport)
        <section class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b flex flex-wrap items-baseline justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">État de retour <span class="text-sm font-normal text-gray-500">— reçu de la DTN</span></h2>
                    <p class="text-xs text-gray-500">
                        Reçu le {{ $returnReport->sent_at?->format('d/m/Y à H:i') }}@if($returnReport->author) de {{ $returnReport->author->name }}@endif
                        @if($returnReport->acknowledged_at) · accusé de réception le {{ $returnReport->acknowledged_at->format('d/m/Y') }}@endif
                    </p>
                </div>
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ ['fit' => 'bg-emerald-100 text-emerald-800', 'fit_with_restrictions' => 'bg-amber-100 text-amber-800', 'unfit' => 'bg-red-100 text-red-800'][$returnReport?->fitness_status] ?? 'bg-gray-100 text-gray-600' }}">Aptitude au retour : {{ $returnReport?->fitnessLabel() ?? 'Non renseigné' }}</span>
            </div>

            @if($performance && $performance['index'] !== null)
                <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4 border-b">
                    <div class="rounded-lg bg-emerald-600 text-white p-4">
                        <div class="text-xs uppercase tracking-wider opacity-80">Indice de performance en sélection</div>
                        <div class="text-3xl font-bold">{{ number_format($performance['index'], 1, ',', ' ') }} <span class="text-base font-semibold opacity-80">/ 100</span></div>
                        <div class="text-xs opacity-80">60 % note de match, 40 % évaluation du staff national</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Note en sélection / en club</div>
                        <div class="text-2xl font-bold text-gray-900">{{ isset($rc['avg_rating']) && $rc['avg_rating'] !== null ? number_format((float) $rc['avg_rating'], 1, ',', ' ') : '—' }} <span class="text-base text-gray-500">/ {{ $performance['club_rating'] !== null ? number_format($performance['club_rating'], 2, ',', ' ') : '—' }}</span></div>
                        @if($performance['rating_delta'] !== null)
                            <div class="text-xs font-semibold {{ $performance['rating_delta'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ $performance['rating_delta'] >= 0 ? '▲ +' : '▼ ' }}{{ number_format($performance['rating_delta'], 2, ',', ' ') }} par rapport au niveau en club</div>
                        @endif
                    </div>
                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="text-xs text-gray-500">Évaluation du staff national</div>
                        <div class="text-2xl font-bold text-gray-900">{{ isset($rc['staff_evaluation']) && $rc['staff_evaluation'] !== null ? number_format((float) $rc['staff_evaluation'], 1, ',', ' ') . ' / 10' : '—' }}</div>
                        <div class="text-xs text-gray-500 line-clamp-2">{{ $rc['evaluation_comment'] ?? '' }}</div>
                    </div>
                </div>
            @endif

            <div class="p-5">
                @if($returnReport)
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm mb-4">
                        @foreach(['matches' => 'Matchs', 'starts' => 'Titularisations', 'minutes' => 'Minutes', 'goals' => 'Buts', 'assists' => 'Passes déc.', 'yellow_cards' => 'Cartons jaunes', 'red_cards' => 'Cartons rouges', 'training_sessions' => 'Séances'] as $k => $l)
                            <div class="rounded-lg border border-gray-200 p-2"><div class="text-xs text-gray-500">{{ $l }}</div><div class="font-semibold text-gray-900">{{ $rc[$k] ?? '—' }}</div></div>
                        @endforeach
                        <div class="rounded-lg border border-gray-200 p-2"><div class="text-xs text-gray-500">Fatigue</div><div class="font-semibold text-gray-900">{{ $levels[$rc['fatigue_level'] ?? ''] ?? '—' }}</div></div>
                        <div class="rounded-lg border border-gray-200 p-2"><div class="text-xs text-gray-500">Risque de blessure</div><div class="font-semibold {{ ($rc['injury_risk'] ?? '') === 'high' ? 'text-red-700' : 'text-gray-900' }}">{{ $levels[$rc['injury_risk'] ?? ''] ?? '—' }}</div></div>
                    </div>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        @foreach(['Incidents' => $rc['incidents'] ?? null, 'Recommandations au club' => $rc['recommendations'] ?? null, 'Commentaire du staff national' => $rc['evaluation_comment'] ?? null] as $label => $value)
                            <div><dt class="text-gray-500">{{ $label }}</dt><dd class="text-gray-900 whitespace-pre-line">{{ $value ?: '—' }}</dd></div>
                        @endforeach
                    </dl>
                    @include('club.selections.partials.medical-return', ['editable' => false, 'medical' => $returnMedical, 'report' => $returnReport])
                @endif

                @if($canAcknowledge ?? false)
                    <form method="POST" action="{{ route('club.selections.acknowledge', $selection) }}" class="mt-5 flex justify-end">
                        @csrf
                        <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Accuser réception et clôturer</button>
                    </form>
                @endif
            </div>
        </section>
    @elseif($departure?->isSent())
        <div class="rounded-lg border border-gray-200 bg-white px-5 py-4 text-sm text-gray-600">L'état de retour sera rédigé par la Direction technique nationale à la fin du rassemblement.</div>
    @endif
</div>
@endsection
