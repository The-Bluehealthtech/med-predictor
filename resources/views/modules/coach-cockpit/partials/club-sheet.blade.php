{{-- Fiche club du cockpit, pour un club sans feuille de match : identité, entraîneur,
     effectif et profils de saison importés (exports « Player statistics »). --}}
@php
    $sheet = $sheet ?? null;
    $canImport = auth()->user() && app(\App\Services\RBACService::class)->userHasPermission(auth()->user(), 'record-performance-metrics');
    $fmt = function ($value, string $format) {
        if ($value === null) return '—';
        return match ($format) {
            'int' => number_format($value, 0, ',', ' '),
            'pct' => number_format(100 * ($value > 1 ? $value / 100 : $value), 0, ',', ' ') . ' %',
            default => number_format($value, 2, ',', ' '),
        };
    };
@endphp
<style>
    .cc .cs-notice { padding: 12px 16px; border-radius: var(--radius); border: 1px solid var(--line); background: var(--surface); color: var(--ink-2); font-size: .9rem; margin-bottom: 16px; }
    .cc .cs-head { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr); gap: 16px; margin-bottom: 16px; }
    .cc .cs-head h2 { margin: 0 0 4px; }
    .cc .cs-facts { display: flex; flex-wrap: wrap; gap: 6px 18px; color: var(--ink-2); font-size: .9rem; margin-top: 6px; }
    .cc .cs-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
    .cc .cs-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    .cc .cs-btn { display: inline-flex; align-items: center; padding: 7px 12px; border-radius: 8px; font-size: .86rem; font-weight: 600; background: var(--accent); color: #fff; text-decoration: none; }
    .cc .cs-btn.secondary { background: var(--surface); color: var(--ink); border: 1px solid var(--line); }
    .cc .cs-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
    .cc .cs-table th { text-align: left; font-size: .74rem; font-weight: 600; color: var(--ink-2); padding: 8px 10px; border-bottom: 1px solid var(--line); white-space: nowrap; }
    .cc .cs-table td { padding: 8px 10px; border-bottom: 1px solid var(--line); white-space: nowrap; }
    .cc .cs-table td.num, .cc .cs-table th.num { text-align: right; font-variant-numeric: tabular-nums; }
    .cc .cs-score { font-weight: 700; }
    @media (max-width: 760px) { .cc .cs-head { grid-template-columns: 1fr; } .cc .cs-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
</style>
<div class="cc"><div class="wrap" data-club-sheet>
@if(!$sheet)
    <div class="panel"><h2>Aucune équipe disponible</h2><p class="note" style="margin-top:8px">Aucun club n'est accessible avec ce compte.</p></div>
@else
    @php($s = $sheet['summary'])
    <p class="cs-notice">Aucune feuille de match n'est encore enregistrée pour ce club : le cockpit complet (bilan, classement, pronostic, composition) s'affichera dès que des matchs seront saisis. En attendant, voici la fiche du club{{ $s['profiles'] ? ' et les profils de saison importés' : '' }}.</p>

    <div class="cs-head">
        <div class="panel">
            <h2>{{ $sheet['club']['name'] }}</h2>
            <div class="cs-facts">
                @if($sheet['club']['association'])<span>Fédération : {{ $sheet['club']['association'] }}</span>@endif
                @if($sheet['club']['founded_year'])<span>Fondé en {{ $sheet['club']['founded_year'] }}</span>@endif
                @if($sheet['club']['website'])<span>{{ $sheet['club']['website'] }}</span>@endif
            </div>
            <div class="cs-actions">
                @if($canImport && Route::has('player-stats-import.create'))
                    <a class="cs-btn" href="{{ route('player-stats-import.create', ['club_id' => $sheet['club']['id']]) }}">Importer un export de statistiques joueurs (Excel)</a>
                @endif
                @if(Route::has('club-officials.club'))
                    <a class="cs-btn secondary" href="{{ route('club-officials.club', $sheet['club']['id']) }}">Dirigeants et staff</a>
                @endif
            </div>
        </div>
        <div class="panel">
            <h3 style="margin:0 0 6px;font-size:.95rem">Entraîneur principal</h3>
            @if($sheet['coach'])
                <p style="margin:0;font-weight:600">{{ $sheet['coach']['name'] }}</p>
                <div class="cs-facts">
                    <span>Nationalité : {{ $sheet['coach']['nationality'] }}</span>
                    @if($sheet['coach']['certification'])<span>Diplôme : {{ $sheet['coach']['certification'] }}</span>@endif
                    @if($sheet['coach']['since'])<span>En poste depuis {{ $sheet['coach']['since'] }}</span>@endif
                </div>
            @else
                <p class="note" style="margin:0">Non renseigné. Il peut être ajouté dans « Dirigeants et staff » (format FIFA Connect).</p>
            @endif
        </div>
    </div>

    <div class="cs-kpis">
        <div class="kpi"><span class="label">Joueurs dans l'effectif</span><span class="value">{{ $s['players'] }}</span></div>
        <div class="kpi"><span class="label">Profils de saison importés</span><span class="value">{{ $s['profiles'] }}</span></div>
        <div class="kpi"><span class="label">Joueurs avec un score « Rôle et apport »</span><span class="value">{{ $s['scored'] }}</span></div>
        <div class="kpi"><span class="label">Dernières statistiques</span><span class="value" style="font-size:1.2rem">{{ $s['measured_at']?->format('d/m/Y') ?? '—' }}</span>
            @if($s['season'] || $s['competition'])<span class="note">{{ trim(($s['competition'] ?? '') . ' ' . ($s['season'] ?? '')) }}</span>@endif</div>
    </div>

    <section class="panel">
        <div class="section-head">
            <h2>Effectif</h2>
            <p>{{ $s['profiles'] ? 'Moyennes par match de la saison, issues du dernier export importé, et score « Rôle et apport » sur le poste joué.' : 'Aucun export de statistiques importé pour ce club : seules les informations des fiches joueurs sont affichées.' }}</p>
        </div>
        @if($sheet['squad'] === [])
            <p class="note">Aucun joueur enregistré dans ce club.</p>
        @else
            <div class="table-scroll">
                <table class="cs-table">
                    <thead><tr>
                        <th>Joueur</th><th>Poste</th><th class="num">Âge</th><th>Nationalité</th>
                        @if($s['profiles'])@foreach($sheet['columns'] as [$label])<th class="num">{{ $label }}</th>@endforeach @endif
                        <th class="num">Rôle et apport</th>
                    </tr></thead>
                    <tbody>
                    @foreach($sheet['squad'] as $p)
                        <tr>
                            <td>{{ $p['name'] }}</td>
                            <td>{{ $p['position'] ?? '—' }}</td>
                            <td class="num">{{ $p['age'] ?: '—' }}</td>
                            <td>{{ $p['nationality'] ?? '—' }}</td>
                            @if($s['profiles'])@foreach($sheet['columns'] as $name => [, $format])<td class="num">{{ $fmt($p['stats'][$name], $format) }}</td>@endforeach @endif
                            <td class="num">@if($p['role'])<span class="cs-score">{{ number_format($p['role']['score'], 0) }}</span>@if($p['role']['reliability'] !== null) <span class="note">fiabilité {{ $p['role']['reliability'] }} %</span>@endif @else<span class="note">—</span>@endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endif
</div></div>
