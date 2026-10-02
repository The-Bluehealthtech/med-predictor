@extends('layouts.app')

@section('title', 'DTN — ' . trim(($selection->player->first_name ?? '') . ' ' . ($selection->player->last_name ?? '')))

{{-- Espace fédération (DTN) : détail d'une sélection. État de départ reçu du club (lecture), état de retour rédigé par la DTN. --}}
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
    @include('dtn.federation.partials.nav', ['active' => 'selections'])
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $selection->team_label }} · {{ $selection->eventTypeLabel() }}@if($selection->is_demo) · <span class="text-amber-700">Données de démonstration</span>@endif</p>
            <h1 class="text-2xl font-bold text-gray-900">{{ $playerName }}</h1>
            <p class="text-sm text-gray-600">{{ $clubName }} → {{ $selection->association->name ?? 'Fédération' }} · {{ $selection->event_name }}@if($selection->opponent) ({{ $selection->opponent }})@endif · du {{ $selection->start_date->format('d/m/Y') }} au {{ $selection->end_date->format('d/m/Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($canCancel ?? false)
                <form method="POST" action="{{ route('dtn.selections.cancel', $selection) }}" onsubmit="return window.confirm('Annuler cette convocation ?')">
                    @csrf
                    <button class="text-sm text-red-600 hover:text-red-800">Annuler la convocation</button>
                </form>
            @endif
            <a href="{{ route('dtn.index') }}" class="text-indigo-700 hover:text-indigo-900 text-sm">← Convocations et retours</a>
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
                <li class="rounded-lg px-3 py-2 border {{ $i < $reached ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($i === $reached ? 'bg-indigo-600 border-indigo-600 text-white font-semibold' : 'bg-white border-gray-200 text-gray-500') }}">
                    <span class="block text-[10px] uppercase tracking-wider opacity-80">Étape {{ $i + 1 }}</span>{{ $label }}
                </li>
            @endforeach
        </ol>
    @endif

    @if($selection->convocation_note)
        <div class="bg-white rounded-lg shadow p-5 text-sm"><p class="font-semibold text-gray-900">Message de convocation</p><p class="text-gray-700 mt-1 whitespace-pre-line">{{ $selection->convocation_note }}</p></div>
    @endif

    {{-- ================= ÉTAT DE DÉPART (reçu du club) ================= --}}
    @if($departure)
    <section class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b flex flex-wrap items-baseline justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">État de départ <span class="text-sm font-normal text-gray-500">— reçu du club</span></h2>
                <p class="text-xs text-gray-500">
                    Envoyé le {{ $departure->sent_at?->format('d/m/Y à H:i') }}@if($departure->author) par {{ $departure->author->name }}@endif
                    @if(!empty($snapshot['generated_at'])) · données du {{ \Carbon\Carbon::parse($snapshot['generated_at'])->format('d/m/Y H:i') }} @endif
                </p>
            </div>
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ ['fit' => 'bg-emerald-100 text-emerald-800', 'fit_with_restrictions' => 'bg-amber-100 text-amber-800', 'unfit' => 'bg-red-100 text-red-800'][$departure?->fitness_status] ?? 'bg-gray-100 text-gray-600' }}">Aptitude : {{ $departure?->fitnessLabel() ?? 'Non renseigné' }}</span>
        </div>

        {{-- Données préparées automatiquement --}}
        <div class="p-5 space-y-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Données du club au départ</p>
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
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Informations du staff du club</p>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    @foreach(['Disponibilité' => $availability[$dc['availability'] ?? ''] ?? '—', 'Contact au club' => $dc['contact'] ?? '—', 'Recommandations de charge' => $dc['load_recommendation'] ?? '—', 'Points de vigilance' => $dc['vigilance'] ?? '—', 'Notes techniques et tactiques' => $dc['technical_notes'] ?? '—'] as $label => $value)
                        <div><dt class="text-gray-500">{{ $label }}</dt><dd class="text-gray-900 whitespace-pre-line">{{ $value !== '' && $value !== null ? $value : '—' }}</dd></div>
                    @endforeach
                </dl>
                @include('dtn.federation.partials.medical-departure', ['editable' => false, 'medical' => $departureMedical, 'report' => $departure])
        </div>
    </section>
    @else
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">En attente de l'état de départ : le club prépare les informations du joueur avant le rassemblement.</div>
    @endif

    {{-- ================= ÉTAT DE RETOUR ================= --}}
    @if($returnReport || ($canEditReturn ?? false))
        <section class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b flex flex-wrap items-baseline justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">État de retour <span class="text-sm font-normal text-gray-500">— à envoyer au club</span></h2>
                    <p class="text-xs text-gray-500">
                        @if($returnReport?->isSent()) Envoyé le {{ $returnReport->sent_at?->format('d/m/Y à H:i') }}@if($returnReport->author) par {{ $returnReport->author->name }}@endif
                            @if($returnReport->acknowledged_at) · lu par le club le {{ $returnReport->acknowledged_at->format('d/m/Y') }}@endif
                        @else Brouillon — rédigé par la DTN à la fin du rassemblement @endif
                    </p>
                </div>
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ ['fit' => 'bg-emerald-100 text-emerald-800', 'fit_with_restrictions' => 'bg-amber-100 text-amber-800', 'unfit' => 'bg-red-100 text-red-800'][$returnReport?->fitness_status] ?? 'bg-gray-100 text-gray-600' }}">Aptitude au retour : {{ $returnReport?->fitnessLabel() ?? 'Non renseigné' }}</span>
            </div>

            @if($performance && $performance['index'] !== null)
                <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4 border-b">
                    <div class="rounded-lg bg-indigo-600 text-white p-4">
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
                @if($canEditReturn ?? false)
                    <form method="POST" action="{{ route('dtn.selections.return', $selection) }}" class="space-y-4">
                        @csrf
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Performances en sélection</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach(['matches' => 'Matchs', 'starts' => 'Titularisations', 'minutes' => 'Minutes', 'training_sessions' => 'Séances', 'goals' => 'Buts', 'assists' => 'Passes déc.', 'yellow_cards' => 'Cartons jaunes', 'red_cards' => 'Cartons rouges'] as $k => $l)
                                <label class="block text-sm font-medium text-gray-700">{{ $l }}<input type="number" min="0" name="{{ $k }}" value="{{ $rc[$k] ?? '' }}" class="{{ $input }}"></label>
                            @endforeach
                            <label class="block text-sm font-medium text-gray-700">Note moyenne (1–10)<input type="number" step="0.1" min="1" max="10" name="avg_rating" value="{{ $rc['avg_rating'] ?? '' }}" class="{{ $input }}"></label>
                            <label class="block text-sm font-medium text-gray-700">Évaluation du staff (1–10)<input type="number" step="0.5" min="1" max="10" name="staff_evaluation" value="{{ $rc['staff_evaluation'] ?? '' }}" class="{{ $input }}"></label>
                        </div>
                        <label class="block text-sm font-medium text-gray-700">Commentaire du staff national<textarea name="evaluation_comment" rows="2" maxlength="2000" class="{{ $input }}">{{ $rc['evaluation_comment'] ?? '' }}</textarea></label>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 pt-2">Incidents et risques</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="block text-sm font-medium text-gray-700 md:col-span-2">Incidents (sportifs, disciplinaires)<textarea name="incidents" rows="2" maxlength="2000" class="{{ $input }}" placeholder="Cartons, comportement, événements notables…">{{ $rc['incidents'] ?? '' }}</textarea></label>
                            <label class="block text-sm font-medium text-gray-700">Niveau de fatigue<select name="fatigue_level" class="{{ $input }}"><option value="">—</option>@foreach($levels as $k => $l)<option value="{{ $k }}" @selected(($rc['fatigue_level'] ?? '') === $k)>{{ $l }}</option>@endforeach</select></label>
                            <label class="block text-sm font-medium text-gray-700">Risque de blessure<select name="injury_risk" class="{{ $input }}"><option value="">—</option>@foreach($levels as $k => $l)<option value="{{ $k }}" @selected(($rc['injury_risk'] ?? '') === $k)>{{ $l }}</option>@endforeach</select></label>
                            <label class="block text-sm font-medium text-gray-700 md:col-span-2">Recommandations au club<textarea name="recommendations" rows="2" maxlength="2000" class="{{ $input }}" placeholder="Récupération, charge des prochains jours…">{{ $rc['recommendations'] ?? '' }}</textarea></label>
                        </div>
                        @include('dtn.federation.partials.medical-return', ['editable' => $canEditReturnMedical ?? false, 'medical' => $returnMedical, 'report' => $returnReport])
                        <div class="flex justify-end gap-3 pt-2">
                            <button name="action" value="save" class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Enregistrer le brouillon</button>
                            <button name="action" value="send" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700" onclick="return window.confirm('Envoyer l\'état de retour au club ?')">Envoyer au club</button>
                        </div>
                    </form>
                @elseif($returnReport)
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
                    @include('dtn.federation.partials.medical-return', ['editable' => false, 'medical' => $returnMedical, 'report' => $returnReport])
                @endif

            </div>
        </section>
    @endif
</div>
@endsection
