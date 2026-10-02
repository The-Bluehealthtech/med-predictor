@extends('layouts.app')

@section('title', 'Analyse des performances')

@php
    $posLabel = ['GK' => 'G', 'DEF' => 'DEF', 'MID' => 'MIL', 'FWD' => 'ATT'];
    $alertTone = [
        'warning' => 'bg-amber-50 border-amber-200 text-amber-900',
        'info' => 'bg-slate-50 border-slate-200 text-slate-800',
        'positive' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
    ];
    $fmt = fn ($v, $d = 2) => $v === null ? '—' : number_format((float) $v, $d, ',', ' ');
    $clubName = optional($clubs->firstWhere('id', $clubId))->name;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Le centre de performance</p>
            <h1 class="text-2xl font-bold text-gray-900">Analyse des performances</h1>
            <p class="text-sm text-gray-600 max-w-3xl">Suivi individuel des joueurs à partir des feuilles de match : forme, temps de jeu, charge et efficacité, avec des alertes expliquées. Pour l'équipe et le prochain match, voir le cockpit entraîneur.</p>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('modules.coach-cockpit', array_filter(['club_id' => $clubId])) }}" class="text-blue-600 hover:text-blue-800">Cockpit entraîneur</a>
            <a href="{{ route('modules.index') }}" class="text-blue-600 hover:text-blue-800">← Modules</a>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
        <label class="block text-sm font-medium text-gray-700">Club
            <select name="club_id" class="mt-1 block w-64 rounded-lg border-gray-300 shadow-sm text-sm" @disabled($clubs->count() <= 1)>
                @foreach($clubs as $club)<option value="{{ $club->id }}" @selected((int) $club->id === (int) $clubId)>{{ str_replace(' (Démo)', '', $club->name) }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Période
            <select name="window" class="mt-1 block rounded-lg border-gray-300 shadow-sm text-sm">
                @foreach($windows as $key => $label)<option value="{{ $key }}" @selected($window === (string) $key)>{{ $label }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Poste
            <select name="position" class="mt-1 block rounded-lg border-gray-300 shadow-sm text-sm">
                <option value="">Tous</option>
                @foreach($positions as $key => $label)<option value="{{ $key }}" @selected($position === $key)>{{ $label }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Matchs minimum (classements)
            <input type="number" name="min_matches" min="1" max="20" value="{{ $minMatches }}" class="mt-1 block w-24 rounded-lg border-gray-300 shadow-sm text-sm">
        </label>
        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Appliquer</button>
    </form>

    @if(!$data)
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-600">Aucun match avec feuille de match pour ce périmètre : l'analyse se remplira dès les premiers matchs saisis.</div>
    @else
        @php
            $rec = $data['record'];
            $teamRatings = collect($data['team_trend']['values'])->slice(-$data['window_matches']);
            $warnings = $alerts->where('level', 'warning')->count();
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs text-gray-500">Bilan · {{ $windows[$window] ?? '' }}</div>
                <div class="text-2xl font-bold text-gray-900">{{ $rec['won'] }}V · {{ $rec['drawn'] }}N · {{ $rec['lost'] }}D</div>
                <div class="text-xs text-gray-500">{{ $rec['for'] }} buts marqués, {{ $rec['against'] }} encaissés</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs text-gray-500">Note moyenne de l'équipe</div>
                <div class="text-2xl font-bold text-gray-900">{{ $fmt($teamRatings->avg()) }}</div>
                <div class="text-xs text-gray-500">sur {{ $data['window_matches'] }} matchs ({{ $data['club_matches'] }} dans la saison)</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs text-gray-500">Joueurs utilisés</div>
                <div class="text-2xl font-bold text-gray-900">{{ count($data['players']) }}</div>
                <div class="text-xs text-gray-500">{{ $position ? $positions[$position] : 'tous postes' }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs text-gray-500">Points d'attention</div>
                <div class="text-2xl font-bold {{ $warnings ? 'text-amber-700' : 'text-gray-900' }}">{{ $warnings }}</div>
                <div class="text-xs text-gray-500">{{ $alerts->count() }} signaux au total</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <section class="bg-white rounded-lg shadow p-5 lg:col-span-2">
                <h2 class="font-semibold text-gray-900">Alertes et signaux</h2>
                <p class="text-xs text-gray-500 mb-3">Calculés sur les feuilles de match, chacun avec sa raison. Charge et forme portent sur les 3 derniers matchs du club.</p>
                @if($alerts->isEmpty())
                    <p class="text-sm text-gray-500">Aucun signal pour ce périmètre.</p>
                @else
                    <ul class="space-y-2 max-h-96 overflow-y-auto pr-1">
                        @foreach($alerts as $alert)
                            <li class="rounded-lg border px-3 py-2 text-sm {{ $alertTone[$alert['level']] ?? $alertTone['info'] }}">
                                <span class="font-semibold">{{ $alert['label'] }}</span> · {{ $alert['player'] }} <span class="text-xs opacity-70">{{ $posLabel[$alert['position']] ?? '' }}</span>
                                <div class="text-xs mt-0.5 opacity-90">{{ $alert['reason'] }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
            <section class="bg-white rounded-lg shadow p-5 space-y-5">
                <div>
                    <h2 class="font-semibold text-gray-900">Meilleures notes</h2>
                    <p class="text-xs text-gray-500 mb-2">Au moins {{ $minMatches }} matchs sur la période.</p>
                    <ol class="space-y-1 text-sm">
                        @forelse($data['leaders']['rating'] as $i => $p)
                            <li class="flex justify-between gap-2"><span>{{ $i + 1 }}. {{ $p['name'] }} <span class="text-xs text-gray-400">{{ $posLabel[$p['position']] ?? '' }}</span></span><span class="font-semibold">{{ $fmt($p['rating']) }} <span class="text-xs text-gray-400 font-normal">({{ $p['matches'] }} m)</span></span></li>
                        @empty
                            <li class="text-gray-500">Pas assez de matchs pour classer.</li>
                        @endforelse
                    </ol>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900">Les plus décisifs</h2>
                    <p class="text-xs text-gray-500 mb-2">Buts + passes décisives par 90 minutes, au moins 270 minutes.</p>
                    <ol class="space-y-1 text-sm">
                        @forelse($data['leaders']['ga_per90'] as $i => $p)
                            <li class="flex justify-between gap-2"><span>{{ $i + 1 }}. {{ $p['name'] }} <span class="text-xs text-gray-400">{{ $posLabel[$p['position']] ?? '' }}</span></span><span class="font-semibold">{{ $fmt($p['ga_per90']) }} <span class="text-xs text-gray-400 font-normal">({{ $p['goals'] }} B, {{ $p['assists'] }} PD)</span></span></li>
                        @empty
                            <li class="text-gray-500">Pas assez de temps de jeu pour classer.</li>
                        @endforelse
                    </ol>
                </div>
            </section>
        </div>

        <section class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold text-gray-900">Note moyenne de l'équipe, match par match</h2>
            <p class="text-xs text-gray-500 mb-3">Moyenne des notes des joueurs ayant joué · saison {{ str_replace(' (Démo)', '', $clubName ?? '') }}</p>
            <div class="h-64"><canvas id="teamTrend"></canvas></div>
        </section>

        <section class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h2 class="font-semibold text-gray-900">Joueurs · {{ $windows[$window] ?? '' }}</h2>
                <p class="text-xs text-gray-500">Cliquez sur un en-tête pour trier. Forme = note moyenne des 3 derniers matchs comparée à la moyenne de saison du joueur.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm" id="playersTable">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            @foreach(['Joueur' => 's', 'Poste' => 's', 'Matchs' => 'n', 'Tit.' => 'n', 'Minutes' => 'n', 'Note' => 'n', 'Forme' => 'n', 'Charge 3 m.' => 'n', 'B' => 'n', 'PD' => 'n', 'B+PD /90' => 'n', 'Cartons' => 'n'] as $head => $type)
                                <th class="px-3 py-2 {{ $type === 'n' ? 'text-right' : 'text-left' }} cursor-pointer select-none hover:text-gray-800" data-type="{{ $type }}">{{ $head }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($data['players'] as $p)
                            @php $d = $p['form_delta']; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $p['name'] }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $posLabel[$p['position']] ?? '—' }}</td>
                                <td class="px-3 py-2 text-right" data-v="{{ $p['matches'] }}">{{ $p['matches'] }}</td>
                                <td class="px-3 py-2 text-right" data-v="{{ $p['starts'] }}">{{ $p['starts'] }}</td>
                                <td class="px-3 py-2 text-right" data-v="{{ $p['minutes'] }}">{{ number_format($p['minutes'], 0, ',', ' ') }}</td>
                                <td class="px-3 py-2 text-right font-semibold" data-v="{{ $p['rating'] ?? -1 }}">{{ $fmt($p['rating']) }}</td>
                                <td class="px-3 py-2 text-right {{ $d === null ? 'text-gray-400' : ($d <= -0.5 ? 'text-red-700 font-semibold' : ($d >= 0.5 ? 'text-emerald-700 font-semibold' : 'text-gray-700')) }}" data-v="{{ $d ?? -99 }}">{{ $d === null ? '—' : (($d > 0 ? '▲ ' : ($d < 0 ? '▼ ' : '')) . number_format(abs($d), 2, ',', ' ')) }}</td>
                                <td class="px-3 py-2 text-right {{ $p['load_last3_pct'] >= 95 ? 'text-amber-700 font-semibold' : '' }}" data-v="{{ $p['load_last3_pct'] }}">{{ $p['load_last3_pct'] }} %</td>
                                <td class="px-3 py-2 text-right" data-v="{{ $p['goals'] }}">{{ $p['goals'] }}</td>
                                <td class="px-3 py-2 text-right" data-v="{{ $p['assists'] }}">{{ $p['assists'] }}</td>
                                <td class="px-3 py-2 text-right" data-v="{{ $p['ga_per90'] ?? -1 }}">{{ $fmt($p['ga_per90']) }}</td>
                                <td class="px-3 py-2 text-right" data-v="{{ $p['yellow'] + 3 * $p['red'] }}">{{ $p['yellow'] }} J{{ $p['red'] ? ' · ' . $p['red'] . ' R' : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <p class="text-xs text-gray-500">Méthode : notes de match et événements des feuilles de match du club. Baisse de forme ou progression : écart d'au moins {{ number_format(\App\Services\Analytics\PlayerFormAnalytics::FORM_DELTA, 1, ',', ' ') }} point entre les 3 derniers matchs et la moyenne de saison (au moins {{ \App\Services\Analytics\PlayerFormAnalytics::FORM_MIN_MATCHES }} matchs notés). Charge élevée : au moins {{ \App\Services\Analytics\PlayerFormAnalytics::HIGH_LOAD_PCT }} % des 270 minutes des 3 derniers matchs, joués en {{ \App\Services\Analytics\PlayerFormAnalytics::CONGESTED_DAYS }} jours ou moins. Suspension : règle d'un match tous les 5 cartons jaunes. Aucune donnée médicale n'est utilisée.</p>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                const trend = @json($data['team_trend']);
                const el = document.getElementById('teamTrend');
                if (el && window.Chart && trend.values.length) {
                    new Chart(el, {
                        type: 'line',
                        data: { labels: trend.labels, datasets: [{ label: 'Note moyenne', data: trend.values, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.25, pointRadius: 4,
                            pointBackgroundColor: trend.results.map(r => r === 'V' ? '#059669' : (r === 'D' ? '#dc2626' : '#9ca3af')) }] },
                        options: { maintainAspectRatio: false, plugins: { legend: { display: false },
                            tooltip: { callbacks: { afterLabel: ctx => ({ V: 'Victoire', N: 'Nul', D: 'Défaite' })[trend.results[ctx.dataIndex]] } } },
                            scales: { y: { suggestedMin: 5, suggestedMax: 7.5 } } }
                    });
                }
                // Tri du tableau des joueurs
                const table = document.getElementById('playersTable');
                if (!table) return;
                table.querySelectorAll('th').forEach((th, index) => {
                    let asc = false;
                    th.addEventListener('click', () => {
                        asc = !asc;
                        const rows = Array.from(table.tBodies[0].rows);
                        rows.sort((a, b) => {
                            const ca = a.cells[index], cb = b.cells[index];
                            const va = th.dataset.type === 'n' ? parseFloat(ca.dataset.v) : ca.textContent.trim();
                            const vb = th.dataset.type === 'n' ? parseFloat(cb.dataset.v) : cb.textContent.trim();
                            return (va > vb ? 1 : va < vb ? -1 : 0) * (asc ? 1 : -1);
                        });
                        rows.forEach(r => table.tBodies[0].appendChild(r));
                    });
                });
            })();
        </script>
    @endif
</div>
@endsection
