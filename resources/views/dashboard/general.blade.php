@extends('layouts.app')

@section('title', 'Tableau de bord - FIT Platform')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
<style>
    .gd { --navy: #1E2761; --gold: #C9A227; --page: #F3F5FB; --card: #FFFFFF; --line: #DCE2F0; --ink: #1B2140; --ink-2: #4A5270; --muted: #6B7390;
          --clinique: #2A78D6; --performance: #EB6834; --selections: #1BAF7A; --administration: #CB8A00;
          --good: #0CA30C; --warn: #B8860B; --crit: #D03B3B;
          font-family: 'Public Sans', system-ui, -apple-system, 'Segoe UI', sans-serif; color: var(--ink); background: var(--page); }
    .gd h1, .gd h2, .gd h3, .gd .gd-value { font-family: 'Space Grotesk', 'Public Sans', system-ui, sans-serif; }
    .gd-wrap { box-sizing: border-box; width: 100%; max-width: 80rem; margin: 0 auto; padding: 28px 16px 40px; display: grid; gap: 20px; }
    .gd-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; background: var(--navy); color: #fff; border-radius: 14px; padding: 22px 24px; }
    .gd-head .eyebrow { font-size: .74rem; letter-spacing: .12em; text-transform: uppercase; color: var(--gold); font-weight: 600; }
    .gd-head h1 { margin: 4px 0 2px; font-size: 1.7rem; font-weight: 700; }
    .gd-head p { margin: 0; color: #D7DCEE; font-size: .92rem; }
    .gd-cta { display: inline-flex; align-items: center; gap: 8px; min-height: 44px; padding: 0 18px; border-radius: 10px; background: var(--gold); color: var(--navy); font-weight: 700; text-decoration: none; }
    .gd-cta:hover, .gd-cta:focus-visible { background: #DDB63A; outline: 2px solid #fff; outline-offset: 2px; }
    .gd-grid { display: grid; gap: 14px; }
    .gd-kpis { grid-template-columns: repeat(auto-fit, minmax(min(100%, 170px), 1fr)); }
    .gd-domains { grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); }
    .gd-two { grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); }
    .gd *, .gd *::before, .gd *::after { box-sizing: border-box; }
    .gd .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
    .gd-card { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 16px 18px; min-width: 0; }
    .gd-card h2 { margin: 0 0 4px; font-size: 1.05rem; font-weight: 700; }
    .gd-sub { margin: 0 0 12px; color: var(--ink-2); font-size: .86rem; }
    .gd-kpi .label { font-size: .8rem; color: var(--ink-2); font-weight: 500; }
    .gd-kpi .gd-value { display: block; font-size: 2rem; font-weight: 700; line-height: 1.1; margin: 6px 0 4px; color: var(--navy); }
    .gd-kpi .gd-value.na { font-size: 1rem; font-weight: 600; color: var(--muted); margin: 14px 0 8px; }
    .gd-kpi .hint { font-size: .76rem; color: var(--muted); }
    .gd-domain { border-top: 4px solid var(--c); display: grid; gap: 10px; }
    .gd-domain h3 { margin: 0; font-size: 1rem; display: flex; align-items: center; gap: 8px; }
    .gd-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--c); flex: none; }
    .gd-metrics { list-style: none; margin: 0; padding: 0; display: grid; gap: 6px; }
    .gd-metrics li { display: flex; justify-content: space-between; gap: 10px; font-size: .88rem; color: var(--ink-2); border-bottom: 1px dashed var(--line); padding-bottom: 6px; }
    .gd-metrics b { color: var(--ink); font-variant-numeric: tabular-nums; }
    .gd-link { display: inline-flex; align-items: center; min-height: 44px; color: var(--navy); font-weight: 600; font-size: .88rem; text-decoration: underline; text-underline-offset: 3px; }
    .gd-note { font-size: .78rem; color: var(--muted); }
    .gd-alerts { list-style: none; margin: 0; padding: 0; display: grid; gap: 8px; }
    .gd-alert { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 10px; padding: 10px 12px; border: 1px solid var(--line); border-radius: 10px; }
    .gd-alert a { color: var(--ink); text-decoration: none; font-weight: 600; font-size: .9rem; }
    .gd-alert a:hover, .gd-alert a:focus-visible { text-decoration: underline; }
    .gd-status { display: inline-flex; align-items: center; gap: 6px; font-size: .74rem; font-weight: 700; padding: 3px 9px; border-radius: 999px; border: 1px solid currentColor; white-space: nowrap; }
    .gd-status.todo { color: var(--warn); } .gd-status.info { color: var(--ink-2); } .gd-status.good { color: var(--good); }
    .gd-count { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.15rem; color: var(--navy); font-variant-numeric: tabular-nums; }
    .gd-domain-tag { font-size: .74rem; color: var(--muted); display: inline-flex; align-items: center; gap: 6px; }
    .gd-donut svg { width: 100%; max-width: 180px; height: auto; }
    .gd-donut { display: grid; grid-template-columns: 160px 1fr; gap: 16px; align-items: center; }
    .gd-legend { list-style: none; margin: 0; padding: 0; display: grid; gap: 8px; font-size: .88rem; }
    .gd-legend li { display: flex; align-items: center; gap: 8px; }
    .gd-legend b { margin-left: auto; font-variant-numeric: tabular-nums; }
    .gd-progress { height: 8px; border-radius: 999px; background: var(--line); overflow: hidden; margin: 8px 0 6px; }
    .gd-progress > i { display: block; height: 100%; background: var(--navy); border-radius: 999px; }
    .gd-feed { list-style: none; margin: 0; padding: 0; display: grid; }
    .gd-feed li { display: grid; grid-template-columns: auto 1fr auto; gap: 10px; align-items: baseline; padding: 9px 0; border-bottom: 1px solid var(--line); font-size: .88rem; }
    .gd-feed time { color: var(--muted); font-size: .78rem; white-space: nowrap; }
    .gd-feed .who { color: var(--muted); font-size: .8rem; }
    .gd-bars { display: grid; gap: 10px; }
    .gd-bar { display: grid; grid-template-columns: 150px 1fr 48px; gap: 10px; align-items: center; font-size: .86rem; }
    .gd-bar .track { height: 14px; background: var(--page); border-radius: 4px; overflow: hidden; }
    .gd-bar .track i { display: block; height: 100%; background: var(--c); border-radius: 0 4px 4px 0; }
    .gd-bar b { text-align: right; font-variant-numeric: tabular-nums; }
    .gd-chart svg { width: 100%; height: auto; display: block; }
    @media (max-width: 520px) { .gd-head h1 { font-size: 1.4rem; } .gd-feed li { grid-template-columns: auto 1fr; } .gd-feed time { grid-column: 2; } .gd-donut { grid-template-columns: 1fr; justify-items: center; } .gd-bar { grid-template-columns: 110px 1fr 40px; } }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $domainColor = ['clinique' => 'var(--clinique)', 'performance' => 'var(--performance)', 'selections' => 'var(--selections)', 'administration' => 'var(--administration)'];
    $domainHex = ['clinique' => '#2A78D6', 'performance' => '#EB6834', 'selections' => '#1BAF7A', 'administration' => '#CB8A00'];
    $shortName = ['clinique' => 'La clinique', 'performance' => 'Centre de performance', 'selections' => 'Sélections nationales', 'administration' => 'Administration'];
    $num = fn ($v) => number_format((float) $v, floor((float) $v) == (float) $v ? 0 : 1, ',', ' ');
    $activity = $board['activity'];
@endphp
<div class="gd" data-general-dashboard>
<div class="gd-wrap">
    <header class="gd-head">
        <div>
            <span class="eyebrow">FIT · Tableau de bord général</span>
            <h1>Bonjour {{ $user->name }}</h1>
            <p>{{ $board['scope'] }} · {{ now()->locale('fr')->translatedFormat('l j F Y') }}</p>
        </div>
        <a class="gd-cta" href="{{ route('modules.index') }}">Accéder aux modules <span aria-hidden="true">→</span></a>
    </header>

    <section aria-labelledby="gd-kpis-title">
        <h2 id="gd-kpis-title" class="sr-only">Indicateurs clés</h2>
        <div class="gd-grid gd-kpis">
            @foreach($board['kpis'] as $kpi)
                <div class="gd-card gd-kpi" data-kpi="{{ $kpi['key'] }}">
                    <span class="label">{{ $kpi['label'] }}</span>
                    @if($kpi['value'] === null)
                        <span class="gd-value na">Non disponible</span>
                    @else
                        <span class="gd-value">{{ $num($kpi['value']) }}@if(!empty($kpi['unit']))<small style="font-size:.9rem;font-weight:600"> {{ $kpi['unit'] }}</small>@endif</span>
                    @endif
                    <span class="hint">{{ $kpi['hint'] }}</span>
                </div>
            @endforeach
        </div>
        <p class="gd-note" style="margin:8px 2px 0">Les tendances (évolution sur la période) apparaîtront lorsqu'un historique quotidien réel sera disponible.</p>
    </section>

    <section aria-labelledby="gd-domains-title">
        <h2 id="gd-domains-title" class="sr-only">Domaines</h2>
        <div class="gd-grid gd-domains">
            @foreach($board['cards'] as $key => $card)
                <article class="gd-card gd-domain" style="--c: {{ $domainColor[$key] }}" data-domain="{{ $key }}">
                    <h3><span class="gd-dot" aria-hidden="true"></span>{{ $card['label'] }}</h3>
                    <ul class="gd-metrics">
                        @foreach($card['metrics'] as [$label, $value])
                            <li><span>{{ $label }}</span><b>{{ $value === null ? 'Non disponible' : $num($value) }}</b></li>
                        @endforeach
                    </ul>
                    <span class="gd-note">Taux de complétude : règle métier à définir.</span>
                    <a class="gd-link" href="{{ route('modules.index', ['section' => $key]) }}" aria-label="Ouvrir {{ $shortName[$key] }} dans les modules">Ouvrir dans les modules <span aria-hidden="true">→</span></a>
                </article>
            @endforeach
        </div>
    </section>

    <div class="gd-grid gd-two">
        <section class="gd-card" aria-labelledby="gd-alerts-title">
            <h2 id="gd-alerts-title">Actions à traiter</h2>
            <p class="gd-sub">Ce qui attend une action dans vos modules, dans votre périmètre.</p>
            @if($board['alerts'] === [])
                <p><span class="gd-status good"><span aria-hidden="true">✓</span> Rien à traiter</span></p>
            @else
                <ul class="gd-alerts">
                    @foreach($board['alerts'] as $alert)
                        <li class="gd-alert">
                            <span class="gd-status {{ $alert['level'] }}">@if($alert['level'] === 'todo')<span aria-hidden="true">!</span> À traiter @else<span aria-hidden="true">i</span> Pour information @endif</span>
                            <span>
                                @if($alert['url'])<a href="{{ $alert['url'] }}">{{ $alert['label'] }}</a>@else<span style="font-weight:600">{{ $alert['label'] }}</span>@endif
                                <span class="gd-domain-tag"><span class="gd-dot" style="--c: {{ $domainColor[$alert['domain']] }}" aria-hidden="true"></span>{{ $shortName[$alert['domain']] }}</span>
                            </span>
                            <span class="gd-count">{{ $num($alert['count']) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
            <p class="gd-note" style="margin-top:10px">Les seuils d'alerte critique (par exemple un bilan non signé après 48 h) seront ajoutés après validation par les équipes métier.</p>
        </section>

        <section class="gd-card" aria-labelledby="gd-modules-title">
            <h2 id="gd-modules-title">Modules par domaine</h2>
            <p class="gd-sub">Modules auxquels vous avez accès.</p>
            @php
                $total = array_sum($board['modules']);
                $r = 60; $c = 2 * M_PI * $r; $gap = count($board['modules']) > 1 ? 3 : 0; $offset = 0;
            @endphp
            @if($total === 0)
                <p class="gd-note">Aucun module accessible avec ce compte.</p>
            @else
                <div class="gd-donut">
                    <svg viewBox="0 0 160 160" role="img" aria-label="Répartition de {{ $total }} modules par domaine">
                        <circle cx="80" cy="80" r="{{ $r }}" fill="none" stroke="#F3F5FB" stroke-width="22"/>
                        @foreach($board['modules'] as $key => $n)
                            @php $len = max(0, $c * $n / $total - $gap); @endphp
                            <circle cx="80" cy="80" r="{{ $r }}" fill="none" stroke="{{ $domainHex[$key] }}" stroke-width="22"
                                    stroke-dasharray="{{ round($len, 2) }} {{ round($c - $len, 2) }}" stroke-dashoffset="{{ round(-$offset, 2) }}" transform="rotate(-90 80 80)">
                                <title>{{ $shortName[$key] }} : {{ $n }} module(s)</title>
                            </circle>
                            @php $offset += $c * $n / $total; @endphp
                        @endforeach
                        <text x="80" y="78" text-anchor="middle" font-family="Space Grotesk, sans-serif" font-size="26" font-weight="700" fill="#1E2761">{{ $total }}</text>
                        <text x="80" y="98" text-anchor="middle" font-size="11" fill="#4A5270">modules</text>
                    </svg>
                    <ul class="gd-legend">
                        @foreach($board['modules'] as $key => $n)
                            <li><span class="gd-dot" style="--c: {{ $domainColor[$key] }}" aria-hidden="true"></span>{{ $shortName[$key] }}<b>{{ $n }}</b></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    </div>

    <div class="gd-grid gd-two">
        <section class="gd-card" aria-labelledby="gd-trend-title" data-activity-ready="{{ $activity['ready'] ? '1' : '0' }}">
            <h2 id="gd-trend-title">Activité sur 30 jours</h2>
            @if(!$activity['ready'])
                <p class="gd-sub">Le journal d'activité est en service depuis peu : les courbes s'afficheront après {{ \App\Services\Dashboard\GeneralDashboard::HISTORY_DAYS }} jours d'historique réel, sans aucune estimation d'ici là.</p>
                <div class="gd-progress" role="progressbar" aria-valuemin="0" aria-valuemax="{{ \App\Services\Dashboard\GeneralDashboard::HISTORY_DAYS }}" aria-valuenow="{{ min($activity['history_days'], 30) }}" aria-label="Historique disponible"><i style="width: {{ min(100, round(100 * $activity['history_days'] / 30)) }}%"></i></div>
                <span class="gd-note">Historique disponible : {{ $activity['history_days'] }} jour(s) sur {{ \App\Services\Dashboard\GeneralDashboard::HISTORY_DAYS }}.</span>
            @else
                @php
                    $days = array_values($activity['daily']); $dates = array_keys($activity['daily']);
                    $max = max(1, max($days)); $w = 600; $h = 160; $step = $w / max(1, count($days) - 1);
                    $points = collect($days)->map(fn ($v, $i) => round($i * $step, 1) . ',' . round($h - 10 - ($h - 30) * $v / $max, 1))->implode(' ');
                @endphp
                <p class="gd-sub">Actions enregistrées par jour, tous domaines accessibles ({{ array_sum($days) }} au total).</p>
                <div class="gd-chart">
                    <svg viewBox="-4 0 {{ $w + 8 }} {{ $h + 18 }}" role="img" aria-label="Actions par jour sur 30 jours, maximum {{ $max }}">
                        <line x1="0" y1="{{ $h - 10 }}" x2="{{ $w }}" y2="{{ $h - 10 }}" stroke="#DCE2F0"/>
                        <polyline points="{{ $points }}" fill="none" stroke="#1E2761" stroke-width="2" stroke-linejoin="round"/>
                        @foreach($days as $i => $v)
                            <circle cx="{{ round($i * $step, 1) }}" cy="{{ round($h - 10 - ($h - 30) * $v / $max, 1) }}" r="7" fill="transparent"><title>{{ \Illuminate\Support\Carbon::parse($dates[$i])->format('d/m') }} : {{ $v }} action(s)</title></circle>
                        @endforeach
                        <text x="0" y="{{ $h + 12 }}" font-size="11" fill="#6B7390">{{ \Illuminate\Support\Carbon::parse($dates[0])->format('d/m') }}</text>
                        <text x="{{ $w }}" y="{{ $h + 12 }}" font-size="11" fill="#6B7390" text-anchor="end">{{ \Illuminate\Support\Carbon::parse(end($dates))->format('d/m') }}</text>
                        <text x="{{ $w }}" y="12" font-size="11" fill="#6B7390" text-anchor="end">max {{ $max }} / jour</text>
                    </svg>
                </div>
                <h3 style="font-size:.92rem;margin:14px 0 8px">Volume par domaine</h3>
                @php $maxDomain = max(1, max($activity['by_domain'] ?: [0])); @endphp
                <div class="gd-bars">
                    @foreach($board['domains'] as $key => $label)
                        <div class="gd-bar" style="--c: {{ $domainColor[$key] }}"><span>{{ $shortName[$key] }}</span><span class="track"><i style="width: {{ round(100 * ($activity['by_domain'][$key] ?? 0) / $maxDomain) }}%"></i></span><b>{{ $activity['by_domain'][$key] ?? 0 }}</b></div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="gd-card" aria-labelledby="gd-feed-title">
            <h2 id="gd-feed-title">Activité récente</h2>
            <p class="gd-sub">Dernières actions dans vos domaines.</p>
            @if($activity['recent'] === [])
                <p class="gd-note">Aucune action enregistrée depuis la mise en service du journal d'activité.</p>
            @else
                <ul class="gd-feed">
                    @foreach($activity['recent'] as $item)
                        <li>
                            <span class="gd-dot" style="--c: {{ $domainColor[$item['domain']] ?? 'var(--muted)' }}" title="{{ $shortName[$item['domain']] ?? $item['domain'] }}"></span>
                            <span>{{ $item['action'] }} <span class="who">· {{ $shortName[$item['domain']] ?? $item['domain'] }}{{ $item['club'] ? ' · ' . $item['club'] : '' }}{{ $item['user'] ? ' · ' . $item['user'] : '' }}</span></span>
                            <time datetime="{{ $item['at']->toIso8601String() }}">{{ $item['at']->locale('fr')->diffForHumans() }}</time>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</div>
</div>
@endsection
