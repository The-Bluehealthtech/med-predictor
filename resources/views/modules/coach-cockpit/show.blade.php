@extends('layouts.app')

@section('title', 'Cockpit entraîneur')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600&display=swap">
<style>
/* Cockpit entraîneur : styles limités au conteneur .cc pour ne pas toucher au reste du site. */
/* Mise en page : du plus synthétique (bilan) au plus détaillé (effectif, définitions). */
.cc {
  --bg: #f4f6f3;
  --surface: #fcfcfb;
  --ink: #111613;
  --ink-2: #4b524d;
  --muted: #7d847f;
  --line: #e0e4df;
  --axis: #c3c8c2;
  --accent: #1d6a46;
  --accent-soft: #e3efe8;
  --s1: #2a78d6;
  --s1-fill: rgba(42, 120, 214, 0.14);
  --s2: #eb6834;
  --pos: #2a78d6;
  --neg: #e34948;
  --good: #0ca30c;
  --good-ink: #006300;
  --warn: #fab219;
  --serious: #ec835a;
  --critical: #d03b3b;
  --draw: #898781;
  --font-display: "Barlow Condensed", "Arial Narrow", "Roboto Condensed", system-ui, sans-serif;
  --font-body: "IBM Plex Sans", system-ui, -apple-system, "Segoe UI", sans-serif;
  --radius: 10px;
}
.cc, .cc * { box-sizing: border-box; }
.cc {
  background: var(--bg);
  color: var(--ink);
  font-family: var(--font-body);
  font-size: 15px;
  line-height: 1.5;
  padding-inline: 20px;
  padding-block: 28px 56px;
}
.cc .wrap { max-width: 1180px; margin: 0 auto; display: grid; gap: 28px; }
.cc h1, .cc h2, .cc h3 { font-family: var(--font-display); font-weight: 700; letter-spacing: 0.01em; text-wrap: balance; margin: 0; }
.cc h1 { font-size: clamp(2rem, 4vw, 2.8rem); line-height: 1.05; }
.cc h2 { font-size: 1.45rem; line-height: 1.15; }
.cc h3 { font-size: 1.1rem; }
.cc p { margin: 0; }
.cc .eyebrow { font-size: 0.72rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); }
.cc .num { font-family: var(--font-display); font-weight: 600; }
.cc .tnum { font-variant-numeric: tabular-nums; }
.cc .panel { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 20px; }
.cc .section-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
.cc .section-head p { color: var(--ink-2); font-size: 0.9rem; max-width: 62ch; }

/* En-tête */ .cc header.top { display: flex; gap: 20px; align-items: center; flex-wrap: wrap; justify-content: space-between; }
.cc .identity { display: flex; gap: 16px; align-items: center; }
.cc .crest {
  width: 60px; height: 60px; border-radius: 50%; display: grid; place-items: center; flex: none;
  background: var(--accent); color: var(--surface); font-family: var(--font-display); font-weight: 700; font-size: 1.5rem;
  box-shadow: inset 0 0 0 3px var(--surface), 0 0 0 2px var(--accent);
}
.cc .identity .meta { color: var(--ink-2); font-size: 0.92rem; }
.cc .tags { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
.cc .tag { font-size: 0.78rem; font-weight: 600; padding: 4px 10px; border-radius: 999px; border: 1px solid var(--line); color: var(--ink-2); background: var(--surface); }
.cc .tag.demo { border-color: var(--warn); color: var(--ink); background: color-mix(in srgb, var(--warn) 16%, var(--surface)); }

/* Tuiles de synthèse */ .cc .kpis { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 12px; }
.cc .kpi { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 14px 16px; display: grid; gap: 4px; align-content: start; }
.cc .kpi .label { font-size: 0.78rem; color: var(--ink-2); font-weight: 500; }
.cc .kpi .value { font-family: var(--font-display); font-weight: 700; font-size: 2.1rem; line-height: 1; }
.cc .kpi .value small { font-size: 1rem; color: var(--ink-2); font-weight: 600; margin-left: 2px; }
.cc .kpi .sub { font-size: 0.8rem; color: var(--muted); }
.cc .kpi.lead { background: var(--accent); border-color: var(--accent); color: var(--surface); }
.cc .kpi.lead .label, .cc .kpi.lead .sub, .cc .kpi.lead .value small { color: color-mix(in srgb, var(--surface) 82%, var(--accent)); }

/* Grilles */ .cc .grid-2 { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 16px; }
.cc .chart-box { position: relative; }
.cc svg.chart { display: block; width: 100%; height: auto; overflow: visible; }
.cc svg text { font-family: var(--font-body); }
.cc .legend { display: flex; flex-wrap: wrap; gap: 14px; font-size: 0.8rem; color: var(--ink-2); margin-top: 10px; }
.cc .legend > span { display: inline-flex; align-items: center; gap: 6px; }
.cc .swatch { width: 14px; height: 3px; border-radius: 2px; display: inline-block; }
.cc .swatch.dash { background: repeating-linear-gradient(90deg, var(--muted) 0 4px, transparent 4px 7px); }
.cc .swatch.box { width: 10px; height: 10px; border-radius: 2px; }

/* Forme */ .cc .form-row { display: flex; gap: 8px; flex-wrap: wrap; margin: 6px 0 16px; }
.cc .res { width: 40px; display: grid; justify-items: center; gap: 4px; font-size: 0.72rem; color: var(--muted); }
.cc .res b { width: 36px; height: 36px; border-radius: 8px; display: grid; place-items: center; font-family: var(--font-display); font-size: 1.15rem; color: #fff; }
.cc .res.V b { background: var(--good); }
.cc .res.N b { background: var(--draw); }
.cc .res.D b { background: var(--critical); }
.cc .facts { display: grid; gap: 10px; }
.cc .fact { display: flex; justify-content: space-between; gap: 12px; border-top: 1px solid var(--line); padding-top: 10px; font-size: 0.9rem; }
.cc .fact span:first-child { color: var(--ink-2); }
.cc .fact b { font-variant-numeric: tabular-nums; }

/* Indicateurs collectifs */ .cc .ind-groups { display: grid; gap: 22px; }
.cc .ind-group h3 { margin-bottom: 10px; color: var(--ink-2); font-family: var(--font-body); font-size: 0.78rem; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 600; }
.cc .ind-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
.cc .ind { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 14px 16px 12px; display: grid; gap: 6px; }
.cc .ind .top { display: flex; justify-content: space-between; align-items: baseline; gap: 8px; }
.cc .ind .name { font-size: 0.86rem; font-weight: 600; }
.cc .ind .kind { font-size: 0.66rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; padding: 2px 6px; border-radius: 4px; color: var(--ink-2); background: var(--bg); border: 1px solid var(--line); white-space: nowrap; }
.cc .ind .kind.calc { color: var(--accent); background: var(--accent-soft); border-color: transparent; }
.cc .ind .val { font-family: var(--font-display); font-weight: 700; font-size: 1.85rem; line-height: 1; }
.cc .ind .val small { font-size: 0.95rem; color: var(--ink-2); font-weight: 600; margin-left: 2px; }
.cc .ind .trend { font-size: 0.78rem; color: var(--muted); display: flex; gap: 6px; align-items: center; }
.cc .delta { font-weight: 600; font-variant-numeric: tabular-nums; }
.cc .delta.up { color: var(--good-ink); }
.cc .delta.down { color: var(--critical); }
.cc .delta.flat { color: var(--muted); }
.cc svg.spark { width: 100%; height: 30px; display: block; }

/* Effectif */ .cc .table-scroll { overflow-x: auto; }
.cc table { width: 100%; border-collapse: collapse; font-size: 0.86rem; }
.cc th, .cc td { padding: 8px 10px; text-align: right; white-space: nowrap; border-bottom: 1px solid var(--line); font-variant-numeric: tabular-nums; }
.cc th { font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--muted); }
.cc th:first-child, .cc td:first-child, .cc th.l, .cc td.l { text-align: left; }
.cc th button { all: unset; cursor: pointer; display: inline-flex; gap: 4px; align-items: center; }
.cc th button:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; border-radius: 2px; }
.cc th button[aria-sort="ascending"]::after { content: "▲"; font-size: 0.6rem; }
.cc th button[aria-sort="descending"]::after { content: "▼"; font-size: 0.6rem; }
.cc tbody tr:hover { background: color-mix(in srgb, var(--accent) 6%, transparent); }
.cc .player { display: flex; flex-direction: column; }
.cc .player small { color: var(--muted); font-size: 0.74rem; }
.cc .minbar { display: inline-flex; align-items: center; gap: 8px; }
.cc .minbar i { display: inline-block; height: 6px; border-radius: 3px; background: var(--s1); }
.cc .minbar .track { width: 70px; height: 6px; border-radius: 3px; background: var(--line); display: inline-flex; }
.cc .chip { font-size: 0.72rem; font-weight: 600; padding: 2px 7px; border-radius: 999px; border: 1px solid var(--line); color: var(--ink-2); }
.cc .muted { color: var(--muted); }

/* Points d'attention */ .cc .alerts { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
.cc .alert { background: var(--surface); border: 1px solid var(--line); border-left-width: 4px; border-radius: var(--radius); padding: 14px 16px; display: grid; gap: 6px; }
.cc .alert.critical { border-left-color: var(--critical); }
.cc .alert.serious { border-left-color: var(--serious); }
.cc .alert.warning { border-left-color: var(--warn); }
.cc .alert.good { border-left-color: var(--good); }
.cc .alert .head { display: flex; justify-content: space-between; gap: 10px; align-items: baseline; }
.cc .alert .head b { font-size: 0.95rem; }
.cc .sev { font-size: 0.72rem; font-weight: 600; white-space: nowrap; display: inline-flex; gap: 5px; align-items: center; color: var(--ink-2); }
.cc .sev::before { content: ""; width: 8px; height: 8px; border-radius: 50%; background: currentColor; }
.cc .sev.critical::before { background: var(--critical); }
.cc .sev.serious::before { background: var(--serious); }
.cc .sev.warning::before { background: var(--warn); }
.cc .sev.good::before { background: var(--good); }
.cc .alert p { color: var(--ink-2); font-size: 0.88rem; }

/* Dictionnaire */ .cc .dict td { white-space: normal; text-align: left; vertical-align: top; }
.cc .dict td:nth-child(3) { font-family: ui-monospace, "SF Mono", Menlo, monospace; font-size: 0.78rem; color: var(--ink-2); }
.cc .dict td:nth-child(4) { font-size: 0.78rem; color: var(--muted); }

.cc details { margin-top: 12px; }
.cc summary { cursor: pointer; font-size: 0.85rem; font-weight: 600; color: var(--accent); }
.cc summary:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

/* Infobulle */ .cc .tip {
  position: fixed; pointer-events: none; z-index: 5; min-width: 150px;
  background: var(--surface); color: var(--ink); border: 1px solid var(--line); border-radius: 8px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.14); padding: 8px 10px; font-size: 0.8rem; line-height: 1.4;
}
.cc .tip b { font-family: var(--font-display); font-size: 0.95rem; }
.cc .tip .row { display: flex; justify-content: space-between; gap: 12px; font-variant-numeric: tabular-nums; }
.cc .tip .row span:first-child { color: var(--ink-2); }

.cc footer { color: var(--muted); font-size: 0.8rem; }

/* Prochain match */ .cc .nm-controls { display: flex; gap: 12px; flex-wrap: wrap; align-items: end; }
.cc .field { display: grid; gap: 4px; font-size: 0.78rem; color: var(--ink-2); font-weight: 500; }
.cc select { font: inherit; font-size: 0.92rem; color: var(--ink); background: var(--surface); border: 1px solid var(--axis); border-radius: 8px; padding: 7px 10px; min-width: 200px; }
.cc select:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
.cc .seg { display: inline-flex; border: 1px solid var(--axis); border-radius: 8px; overflow: hidden; }
.cc .seg button { font: inherit; font-size: 0.88rem; padding: 7px 14px; border: 0; background: var(--surface); color: var(--ink-2); cursor: pointer; }
.cc .seg button[aria-pressed="true"] { background: var(--accent); color: var(--surface); font-weight: 600; }
.cc .seg button:focus-visible { outline: 2px solid var(--accent); outline-offset: -2px; }
.cc .note { font-size: 0.8rem; color: var(--muted); }
.cc .nm-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.35fr); gap: 16px; margin-top: 16px; }
.cc .prob { display: flex; height: 34px; border-radius: 6px; overflow: hidden; gap: 2px; margin: 8px 0 6px; }
.cc .prob div { display: grid; place-items: center; color: #fff; font-family: var(--font-display); font-weight: 700; font-size: 1rem; min-width: 34px; }
.cc .prob .pw { background: var(--good); } .cc .prob .pd { background: var(--draw); } .cc .prob .pl { background: var(--critical); }
.cc .prob-legend { display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--ink-2); }
.cc .scoreline { display: flex; gap: 16px; align-items: baseline; margin-top: 14px; flex-wrap: wrap; }
.cc .scoreline .big { font-family: var(--font-display); font-weight: 700; font-size: 2.6rem; line-height: 1; }
.cc .reads { display: grid; gap: 8px; margin-top: 14px; }
.cc .read { display: flex; gap: 10px; align-items: baseline; font-size: 0.86rem; border-top: 1px solid var(--line); padding-top: 8px; }
.cc .read .k { color: var(--ink-2); min-width: 0; flex: 1; }
.cc .read .v { font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }
.cc .read .v.up { color: var(--critical); } .cc .read .v.down { color: var(--good-ink); }
.cc .plan { margin-top: 12px; padding: 10px 12px; border-radius: 8px; background: var(--accent-soft); font-size: 0.86rem; display: grid; gap: 4px; }
.cc .plan b { font-family: var(--font-display); font-size: 1.05rem; }
.cc .pitch-wrap { position: relative; }
.cc svg.pitch { display: block; width: 100%; max-width: 380px; height: auto; margin: 0 auto; }
.cc .bench { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; margin-top: 12px; }
.cc .bench .b { border: 1px solid var(--line); border-radius: 8px; padding: 8px 10px; font-size: 0.82rem; display: grid; gap: 2px; }
.cc .bench .b small { color: var(--muted); }
.cc .out { margin-top: 10px; font-size: 0.84rem; color: var(--ink-2); }
@media (max-width: 980px) { .cc .nm-grid { grid-template-columns: minmax(0, 1fr); } }
.cc .grid-pos td { vertical-align: top; }
.cc .cand { display: grid; gap: 1px; }
.cc .cand .n { font-weight: 600; }
.cc .cand .n.xi::after { content: " · titulaire"; font-weight: 600; font-size: 0.7rem; color: var(--accent); }
.cc .cand small { color: var(--muted); font-size: 0.74rem; }
.cc .pred { font-family: var(--font-display); font-weight: 700; font-size: 1.15rem; }
.cc .model-card { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr); gap: 20px; }
.cc .imp { display: grid; gap: 6px; font-size: 0.82rem; }
.cc .imp .r { display: grid; grid-template-columns: 170px minmax(0, 1fr) 40px; gap: 8px; align-items: center; }
.cc .imp .bar { height: 8px; border-radius: 0 4px 4px 0; background: var(--s1); }
.cc .imp .r span:last-child { text-align: right; color: var(--muted); font-variant-numeric: tabular-nums; }
.cc .badge { display: inline-flex; gap: 6px; align-items: center; font-size: 0.78rem; font-weight: 600; padding: 3px 9px; border-radius: 999px; background: var(--accent-soft); color: var(--accent); }
.cc tr.best td { font-weight: 600; }
.cc #model-metrics td:first-child { white-space: normal; min-width: 150px; }
.cc #model-metrics th, .cc #model-metrics td { padding-inline: 7px; }
@media (max-width: 980px) { .cc .model-card { grid-template-columns: minmax(0, 1fr); } .cc .imp .r { grid-template-columns: 130px minmax(0, 1fr) 40px; } }

@media (max-width: 980px) {
  .cc .kpis { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  .cc .ind-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .cc .grid-2 { grid-template-columns: minmax(0, 1fr); }
}
@media (max-width: 560px) {
  .cc { padding-inline: 16px; }
  .cc .kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .cc .ind-grid, .cc .alerts { grid-template-columns: minmax(0, 1fr); }
}
@media (prefers-reduced-motion: reduce) { .cc, .cc * { transition: none !important; animation: none !important; } }
.cc .club-picker { display: flex; gap: 10px; align-items: end; flex-wrap: wrap; background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 12px 16px; }
.cc .club-picker a { font-size: 0.85rem; color: var(--accent); }
</style>
@endpush

@section('content')
@php
    $clubShort = $cockpit ? str_replace(' (Démo)', '', $cockpit['club']['name']) : '';
    $initials = $cockpit ? collect(preg_split('/[\s-]+/u', $clubShort))->filter()->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('') : '';
@endphp
<div class="cc" style="padding-bottom:0">
  <div class="wrap" style="gap:12px">
    <a href="{{ route('modules.index') }}" style="font-size:0.85rem;color:var(--accent)">← Modules</a>
    <form method="get" action="{{ route('modules.coach-cockpit') }}" class="club-picker">
      <label class="field" for="club-select">Équipe à afficher
        <select id="club-select" name="club_id" onchange="this.form.submit()">
          @foreach($clubs as $club)
            <option value="{{ $club->id }}" @selected((int) $club->id === (int) $clubId)>{{ str_replace(' (Démo)', '', $club->name) }}</option>
          @endforeach
        </select>
      </label>
      <noscript><button type="submit">Afficher</button></noscript>
      @if($clubs->count() === 1)<span class="note">Votre compte donne accès à votre club uniquement.</span>@endif
    </form>
  </div>
</div>

<div class="cc" style="padding-top:0">
  <div class="wrap">
    <section class="panel" aria-labelledby="h-role-eval-activation">
      <div class="section-head">
        <h2 id="h-role-eval-activation">Évaluation joueur — rôle et apport</h2>
        <p>
          Pipeline canonique : importer des observations réelles, les valider par dry-run,
          puis calculer les scores dans PostgreSQL. Les données de démonstration restent séparées.
        </p>
      </div>

      @if(session('success'))
        <div style="margin-bottom:12px;padding:10px 12px;border-radius:8px;background:#e9f8ef;color:#176b37">
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div style="margin-bottom:12px;padding:10px 12px;border-radius:8px;background:#fff1f0;color:#a61b1b">
          {{ session('error') }}
        </div>
      @endif

      <div class="kpis" style="margin-bottom:18px">
        @foreach([
          ['Participations réelles', $roleEvaluationStatus['real_participations']],
          ['Stats réelles', $roleEvaluationStatus['real_stats']],
          ['Évaluations réelles', $roleEvaluationStatus['real_evaluations']],
          ['Évaluations démo', $roleEvaluationStatus['demo_evaluations']],
        ] as [$label, $value])
          <div class="kpi">
            <span class="eyebrow">{{ $label }}</span>
            <strong style="font-size:1.55rem">{{ number_format($value, 0, ',', ' ') }}</strong>
          </div>
        @endforeach
      </div>

      @if($roleEvaluationStatus['published_configs']->isEmpty())
        <div style="margin-bottom:18px;padding:12px 14px;border:1px solid #e8b04a;border-radius:10px;background:#fff9e8">
          <strong>Calcul réel verrouillé.</strong>
          <span>
            Aucune configuration de poids n'est publiée. La configuration de démonstration reste en brouillon
            et ne sera jamais utilisée comme résultat réel.
          </span>
        </div>
      @endif

      <div class="grid-2">
        <div>
          <h3 style="margin-bottom:8px">1. Importer des observations réelles</h3>
          <p class="note" style="margin-bottom:10px">
            Le système effectue d'abord un dry-run transactionnel. Si une ligne est rejetée,
            aucune donnée n'est écrite.
          </p>
          <form method="post"
                enctype="multipart/form-data"
                action="{{ route('modules.coach-cockpit.role-evaluation.import') }}"
                style="display:grid;gap:10px">
            @csrf
            <label class="field">Type de données
              <select name="type" required>
                <option value="participations">Participations</option>
                <option value="player-match-stats">Statistiques joueur / match</option>
                <option value="team-stats">Statistiques équipe / match</option>
                <option value="events">Événements de match</option>
              </select>
            </label>
            <label class="field">Source
              <input type="text" name="source" placeholder="Ex. fournisseur officiel, club, fédération">
            </label>
            <label class="field">Fichier CSV
              <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required>
            </label>
            <label class="field">Mapping JSON
              <input type="file" name="mapping_file" accept=".json,application/json,text/plain" required>
            </label>
            <button type="submit">Valider puis importer</button>
          </form>
        </div>

        <div>
          <h3 style="margin-bottom:8px">2. Calculer les évaluations réelles</h3>
          <p class="note" style="margin-bottom:10px">
            Le calcul utilise exclusivement <code>is_demo=0</code> et exige une configuration publiée.
          </p>
          <form method="post"
                action="{{ route('modules.coach-cockpit.role-evaluation.compute') }}"
                style="display:grid;gap:10px">
            @csrf
            <input type="hidden" name="club_id" value="{{ $clubId }}">
            <label class="field">Configuration publiée
              <select name="config_version" required @disabled($roleEvaluationStatus['published_configs']->isEmpty())>
                @forelse($roleEvaluationStatus['published_configs'] as $config)
                  <option value="{{ $config->id }}">#{{ $config->id }} — {{ $config->label }}</option>
                @empty
                  <option value="">Aucune configuration publiée</option>
                @endforelse
              </select>
            </label>
            <button type="submit" @disabled($roleEvaluationStatus['published_configs']->isEmpty())>
              Calculer les scores réels
            </button>
          </form>

          @if($roleEvaluationStatus['draft_configs']->isNotEmpty())
            <p class="note" style="margin-top:12px">
              Brouillon présent :
              {{ $roleEvaluationStatus['draft_configs']->map(fn ($c) => '#'.$c->id.' '.$c->label)->implode(', ') }}.
            </p>
          @endif
        </div>
      </div>
    </section>
  </div>
</div>

@if(!$cockpit)
<div class="cc"><div class="wrap"><div class="panel"><h2>Aucune donnée de match</h2><p class="note" style="margin-top:8px">Aucun match joué avec feuille de match n'est enregistré pour cette équipe. Le cockpit s'affiche dès que des matchs sont saisis.</p></div></div></div>
@else
<div class="cc">
<div class="wrap">
  <header class="top">
    <div class="identity">
      <div class="crest" aria-hidden="true">{{ $initials }}</div>
      <div>
        <p class="eyebrow">Cockpit entraîneur</p>
        <h1>{{ $clubShort }}</h1>
        <p class="meta" id="meta"></p>
      </div>
    </div>
    <div class="tags">
      @if($cockpit['isDemo'])<span class="tag demo">Données de démonstration</span>@endif
      <span class="tag" id="tag-updated"></span>
    </div>
  </header>

  <section aria-labelledby="h-bilan">
    <h2 id="h-bilan" class="eyebrow" style="font-family:var(--font-body);font-size:0.72rem;margin-bottom:10px">Bilan de la saison</h2>
    <div class="kpis" id="kpis"></div>
  </section>

  <section class="grid-2">
    <div class="panel">
      <div class="section-head">
        <h2>Trajectoire au classement</h2>
        <p>Points cumulés journée par journée, face au leader et à la médiane du championnat.</p>
      </div>
      <div class="chart-box" id="traj-box"></div>
      <div class="legend">
        <span><i class="swatch" style="background:var(--s1)"></i>{{ $clubShort }}</span>
        <span><i class="swatch" style="background:var(--s2)"></i><span>Leader (<span id="leader-name"></span>)</span></span>
        <span><i class="swatch dash"></i>Médiane des 16 clubs</span>
      </div>
    </div>
    <div class="panel">
      <div class="section-head"><h2>Forme récente</h2></div>
      <p class="eyebrow">5 derniers matchs</p>
      <div class="form-row" id="form"></div>
      <div class="facts" id="facts"></div>
    </div>
  </section>

  <section class="panel" id="next-match" aria-labelledby="h-next">
    <div class="section-head">
      <h2 id="h-next">Prochain match : pronostic et composition recommandée</h2>
      <p>Modèle explicable fondé sur les xG des deux équipes et sur la forme de chaque joueur. Une aide à la décision, pas une certitude.</p>
    </div>
    <div class="nm-controls">
      <label class="field" for="opp-select">Adversaire
        <select id="opp-select"></select>
      </label>
      <div class="field"><span id="venue-label">Lieu</span>
        <div class="seg" role="group" aria-labelledby="venue-label">
          <button type="button" id="venue-home" aria-pressed="true">Domicile</button>
          <button type="button" id="venue-away" aria-pressed="false">Extérieur</button>
        </div>
      </div>
      <p class="note">Les 30 journées du calendrier démo sont jouées : aucun prochain match n'est programmé dans la base. Choisissez l'adversaire et le lieu. La grille des postes ci-dessous suit le même choix.</p>
    </div>
    <div class="nm-grid">
      <div>
        <p class="eyebrow">Pronostic</p>
        <div class="prob" id="prob" role="img"></div>
        <div class="prob-legend"><span>Victoire {{ $clubShort }}</span><span>Nul</span><span>Défaite</span></div>
        <div class="scoreline" id="scoreline"></div>
        <div class="reads" id="reads"></div>
        <div class="plan" id="plan"></div>
      </div>
      <div>
        <p class="eyebrow">Onze optimal du modèle <span class="badge" id="xi-total" style="margin-left:6px"></span></p>
        <div class="pitch-wrap" id="pitch-box"></div>
        <p class="eyebrow" style="margin-top:14px">Remplaçants proposés</p>
        <div class="bench" id="bench"></div>
        <p class="out" id="out"></p>
      </div>
    </div>
    <details>
      <summary>Comment la composition et le pronostic sont calculés</summary>
      <div style="display:grid;gap:8px;margin-top:10px;font-size:0.86rem;color:var(--ink-2);max-width:80ch">
        <p><b>Pronostic.</b> Buts attendus de chaque équipe = xG moyen du championnat × force offensive de l'équipe × faiblesse défensive de l'adversaire × facteur du lieu. Les forces mêlent la saison (70 %) et les 5 derniers matchs (30 %). Le facteur du lieu est mesuré sur les 240 matchs démo. Les probabilités suivent une loi de Poisson.</p>
        <p><b>Composition.</b> Le modèle appris prédit la note de chaque joueur à chaque poste face à l'adversaire choisi. Le onze retenu est l'affectation joueur-poste qui maximise la somme des notes prédites (algorithme d'affectation optimale), un joueur par poste, sans les suspendus. Un gardien ne joue que dans le but et inversement.</p>
        <p><b>Système.</b> 4-2-3-1 si l'adversaire attaque au-dessus de la moyenne ou domine le ballon, 4-3-3 sinon.</p>
      </div>
    </details>
  </section>

  <section class="panel" aria-labelledby="h-grid">
    <div class="section-head">
      <h2 id="h-grid">Grille des postes</h2>
      <p>Pour chaque poste, les trois meilleurs joueurs disponibles selon la note prédite par le modèle face à l'adversaire choisi. Les joueurs du onze optimal sont signalés.</p>
    </div>
    <div class="table-scroll"><table class="grid-pos" id="grid-pos"></table></div>
  </section>

  <section class="panel" aria-labelledby="h-model">
    <div class="section-head">
      <h2 id="h-model">Fiche du modèle de sélection</h2>
      <p id="model-sub"></p>
    </div>
    <div class="model-card">
      <div>
        <p class="eyebrow" style="margin-bottom:8px">Validation sur des matchs jamais vus (J23 à J30)</p>
        <div class="table-scroll"><table id="model-metrics"></table></div>
        <p class="note" style="margin-top:8px">Gain du top-11 : note réelle moyenne des 11 joueurs que la méthode aurait choisis, moins celle de l'ensemble des joueurs du match. Corrélation : classement prédit contre classement réel, au sein de chaque match.</p>
      </div>
      <div>
        <p class="eyebrow" style="margin-bottom:8px">Ce qui pèse le plus dans la prédiction</p>
        <div class="imp" id="model-imp"></div>
      </div>
    </div>
    <details>
      <summary>Données d'apprentissage et limites</summary>
      <div style="display:grid;gap:8px;margin-top:10px;font-size:0.86rem;color:var(--ink-2);max-width:80ch" id="model-limits"></div>
    </details>
  </section>

  <section class="panel">
    <div class="section-head">
      <h2>Match par match</h2>
      <p>Barre : différence de buts. Point : différence d'xG. Un point au-dessus de la barre signale un résultat inférieur à ce que le jeu a produit.</p>
    </div>
    <div class="chart-box" id="matches-box"></div>
    <div class="legend">
      <span><i class="swatch box" style="background:var(--pos)"></i>Victoire (différence positive)</span>
      <span><i class="swatch box" style="background:var(--neg)"></i>Défaite (différence négative)</span>
      <span><i class="swatch box" style="background:var(--draw)"></i>Nul</span>
      <span><i class="swatch box" style="background:var(--ink);border-radius:50%"></i>Différence d'xG</span>
    </div>
    <details>
      <summary>Voir le tableau des 30 matchs</summary>
      <div class="table-scroll"><table id="matches-table"></table></div>
    </details>
  </section>

  <section>
    <div class="section-head">
      <h2>Indicateurs de jeu</h2>
      <p>Moyenne sur la saison, écart des 5 derniers matchs par rapport à cette moyenne, et évolution match par match. « Collecté » : relevé tel quel. « Calculé » : dérivé de plusieurs relevés.</p>
    </div>
    <div class="ind-groups" id="indicators"></div>
  </section>

  <section class="panel">
    <div class="section-head">
      <h2>Effectif et temps de jeu</h2>
      <p>Cliquer sur un en-tête pour trier. Le score « Rôle et apport » est calculé sur la famille de poste réellement jouée, avec sa fiabilité.</p>
    </div>
    <div class="facts" id="squad-facts" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:16px"></div>
    <div class="table-scroll"><table id="squad"></table></div>
  </section>

  <section>
    <div class="section-head">
      <h2>Points d'attention</h2>
      <p>Signaux calculés automatiquement à partir des indicateurs ci-dessus.</p>
    </div>
    <div class="alerts" id="alerts"></div>
  </section>

  <section class="panel">
    <div class="section-head">
      <h2>Dictionnaire des indicateurs</h2>
      <p>Chaque indicateur du cockpit, sa nature, son mode de calcul et sa source dans la base.</p>
    </div>
    <div class="table-scroll"><table class="dict" id="dict"></table></div>
  </section>

  <footer>Source : base Med Predictor, {{ $cockpit['competition'] }} ({{ count($cockpit['table']) }} clubs, {{ count($cockpit['matches']) }} matchs joués par {{ $clubShort }}).@if($cockpit['isDemo']) Données fictives générées pour la démonstration.@endif Calculé à partir des feuilles de match, statistiques et événements enregistrés.</footer>
</div>
<div class="tip" id="tip" hidden></div>
</div>
@endif
@endsection

@if($cockpit)
@push('scripts')
<script>window.COACH_COCKPIT_DATA = @json($cockpit);</script>
<script>
@verbatim
const DATA = window.COACH_COCKPIT_DATA;
const M = DATA.matches, TABLE = DATA.table, SQUAD = DATA.squad, CUM = DATA.cum, CLUBS = DATA.clubs, PM = DATA.pm;
const TEAM_ID = DATA.club.id;
const CLUB = DATA.club.name.replace(' (Démo)', '');
const CLUB_LABEL = CLUB.split(' ').slice(-1)[0];
const fr = (x, d = 1) => Number(x).toLocaleString('fr-FR', { minimumFractionDigits: d, maximumFractionDigits: d });
const pct = (a, b, d = 1) => b > 0 ? fr(100 * a / b, d) : '–';
const sum = (arr, f) => arr.reduce((s, r) => s + (Number(f(r)) || 0), 0);
const res = r => r.gf > r.ga ? 'V' : r.gf < r.ga ? 'D' : 'N';
const esc = s => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
const short = n => n.replace(' (Démo)', '');

// ---------- Bilan ----------
const N = M.length;
const W = M.filter(r => res(r) === 'V').length, D = M.filter(r => res(r) === 'N').length, L = N - W - D;
const PTS = 3 * W + D, GF = sum(M, r => r.gf), GA = sum(M, r => r.ga);
const XG = sum(M, r => r.xg), XGA = sum(M, r => r.xga);
const CS = M.filter(r => r.ga === 0).length;
const rank = TABLE.findIndex(t => t.id === TEAM_ID) + 1;
const leader = TABLE[0];
const lastDate = new Date(M[N - 1].date);

document.getElementById('meta').textContent = `${DATA.competition} · ${N} journées jouées · ${TABLE.length} clubs`;
document.getElementById('tag-updated').textContent = 'Après la J' + M[N - 1].md + ' du ' + lastDate.toLocaleDateString('fr-FR');
document.getElementById('leader-name').textContent = short(leader.name);

const kpis = [
  { lead: true, label: 'Classement', value: rank + '<small>e / ' + TABLE.length + '</small>', sub: PTS === leader.pts ? `${PTS} pts · en tête` : `${PTS} pts · ${leader.pts - PTS} pts derrière le leader` },
  { label: 'Points par match', value: fr(PTS / N, 2), sub: `Leader : ${fr(leader.pts / leader.j, 2)}` },
  { label: 'Bilan', value: `${W}<small>V</small> ${D}<small>N</small> ${L}<small>D</small>`, sub: `${pct(W, N, 0)} % de victoires` },
  { label: 'Buts pour / contre', value: `${GF}<small>–</small>${GA}`, sub: `Différence ${GF - GA >= 0 ? '+' : ''}${GF - GA}` },
  { label: 'xG pour / contre', value: `${fr(XG, 1)}<small>–</small>${fr(XGA, 1)}`, sub: `Finition ${GF - XG >= 0 ? '+' : ''}${fr(GF - XG, 1)} but vs xG` },
  { label: 'Matchs sans encaisser', value: CS, sub: `${pct(CS, N, 0)} % des matchs` },
];
document.getElementById('kpis').innerHTML = kpis.map(k => `<div class="kpi${k.lead ? ' lead' : ''}"><span class="label">${k.label}</span><span class="value">${k.value}</span><span class="sub">${k.sub}</span></div>`).join('');

// ---------- Infobulle ----------
const tip = document.getElementById('tip');
function showTip(box, html, x, y) {
  tip.innerHTML = html; tip.hidden = false;
  const b = box.getBoundingClientRect(), t = tip.getBoundingClientRect();
  let left = b.left + x + 14, top = b.top + y - t.height - 10;
  if (left + t.width > document.documentElement.clientWidth - 8) left = b.left + x - t.width - 14;
  if (top < 8) top = b.top + y + 14;
  tip.style.left = left + 'px'; tip.style.top = top + 'px';
}
const hideTip = () => { tip.hidden = true; };

// ---------- Trajectoire ----------
(function trajectory() {
  const box = document.getElementById('traj-box');
  const team = CUM[TEAM_ID], lead = CUM[leader.id];
  const median = team.map((_, i) => {
    const v = Object.values(CUM).map(a => a[i]).sort((a, b) => a - b);
    return (v[7] + v[8]) / 2;
  });
  const w = 720, h = 300, ml = 36, mr = 96, mt = 14, mb = 30;
  const maxY = Math.ceil(Math.max(...lead) / 10) * 10;
  const x = i => ml + i * (w - ml - mr) / (N - 1);
  const y = v => mt + (h - mt - mb) * (1 - v / maxY);
  let g = '';
  for (let v = 0; v <= maxY; v += 10) g += `<line x1="${ml}" x2="${w - mr}" y1="${y(v)}" y2="${y(v)}" stroke="var(--line)"/><text x="${ml - 8}" y="${y(v) + 4}" text-anchor="end" font-size="11" fill="var(--muted)">${v}</text>`;
  [1, 5, 10, 15, 20, 25, 30].forEach(md => g += `<text x="${x(md - 1)}" y="${h - 8}" text-anchor="middle" font-size="11" fill="var(--muted)">J${md}</text>`);
  const path = a => a.map((v, i) => (i ? 'L' : 'M') + x(i).toFixed(1) + ' ' + y(v).toFixed(1)).join(' ');
  const area = path(team) + ` L${x(N - 1)} ${y(0)} L${x(0)} ${y(0)} Z`;
  const ends = [
    { v: lead[N - 1], y: y(lead[N - 1]), color: 'var(--s2)', text: short(leader.name).split(' ').pop() + ' ' + lead[N - 1], weight: 600, ink: 'var(--ink)' },
    { v: median[N - 1], y: y(median[N - 1]), color: null, text: 'Médiane ' + fr(median[N - 1], 1).replace(',0', ''), weight: 400, ink: 'var(--muted)' },
    { v: team[N - 1], y: y(team[N - 1]), color: 'var(--s1)', text: CLUB_LABEL + ' ' + team[N - 1], weight: 600, ink: 'var(--ink)' },
  ].sort((a, b) => a.y - b.y);
  for (let i = 1; i < ends.length; i++) ends[i].ly = Math.max(ends[i].y, (ends[i - 1].ly ?? ends[i - 1].y) + 15);
  ends[0].ly = ends[0].y;
  const endLabels = ends.map(e => (e.color ? `<circle cx="${x(N - 1)}" cy="${e.y}" r="4.5" fill="${e.color}" stroke="var(--surface)" stroke-width="2"/>` : '') +
    `<text x="${x(N - 1) + 10}" y="${e.ly + 4}" font-size="12" font-weight="${e.weight}" fill="${e.ink}">${esc(e.text)}</text>`).join('');
  box.innerHTML = `<svg class="chart" viewBox="0 0 ${w} ${h}" role="img" aria-label="Points cumulés de ${esc(CLUB)}, du leader et de la médiane sur ${N} journées">
    ${g}<line x1="${ml}" x2="${w - mr}" y1="${y(0)}" y2="${y(0)}" stroke="var(--axis)"/>
    <path d="${area}" fill="var(--s1-fill)"/>
    <path d="${path(median)}" fill="none" stroke="var(--muted)" stroke-width="2" stroke-dasharray="5 4"/>
    <path d="${path(lead)}" fill="none" stroke="var(--s2)" stroke-width="2" stroke-linejoin="round"/>
    <path d="${path(team)}" fill="none" stroke="var(--s1)" stroke-width="2.5" stroke-linejoin="round"/>
    ${endLabels}
    <line id="traj-cross" x1="0" x2="0" y1="${mt}" y2="${h - mb}" stroke="var(--axis)" visibility="hidden"/>
    <circle id="traj-dot" r="5" fill="var(--s1)" stroke="var(--surface)" stroke-width="2" visibility="hidden"/>
    <rect x="${ml}" y="${mt}" width="${w - ml - mr}" height="${h - mt - mb}" fill="transparent" id="traj-hit"/>
  </svg>`;
  const svg = box.querySelector('svg'), hit = box.querySelector('#traj-hit'), cross = box.querySelector('#traj-cross'), dot = box.querySelector('#traj-dot');
  hit.addEventListener('mousemove', e => {
    const p = svg.createSVGPoint(); p.x = e.clientX; p.y = e.clientY;
    const lp = p.matrixTransform(svg.getScreenCTM().inverse());
    const i = Math.max(0, Math.min(N - 1, Math.round((lp.x - ml) / ((w - ml - mr) / (N - 1)))));
    cross.setAttribute('x1', x(i)); cross.setAttribute('x2', x(i)); cross.setAttribute('visibility', 'visible');
    dot.setAttribute('cx', x(i)); dot.setAttribute('cy', y(team[i])); dot.setAttribute('visibility', 'visible');
    const r = M[i], sc = svg.getBoundingClientRect().width / w;
    showTip(box, `<b>J${r.md} · ${res(r) === 'V' ? 'Victoire' : res(r) === 'D' ? 'Défaite' : 'Nul'} ${r.gf}–${r.ga}</b><div class="muted">${r.home ? 'vs' : 'à'} ${esc(short(r.opp))}</div>
      <div class="row"><span>${esc(CLUB_LABEL)}</span><span>${team[i]} pts</span></div><div class="row"><span>Leader</span><span>${lead[i]} pts</span></div><div class="row"><span>Médiane</span><span>${fr(median[i], 1)}</span></div>`, x(i) * sc, y(team[i]) * sc);
  });
  hit.addEventListener('mouseleave', () => { hideTip(); cross.setAttribute('visibility', 'hidden'); dot.setAttribute('visibility', 'hidden'); });
})();

// ---------- Forme ----------
(function form() {
  const last = M.slice(-5);
  document.getElementById('form').innerHTML = last.map(r => `<div class="res ${res(r)}" title="J${r.md} ${r.home ? 'vs' : 'à'} ${esc(short(r.opp))} ${r.gf}–${r.ga}"><b aria-label="${res(r) === 'V' ? 'Victoire' : res(r) === 'D' ? 'Défaite' : 'Nul'}">${res(r)}</b><span>${r.gf}–${r.ga}</span></div>`).join('');
  const pts5 = sum(last, r => res(r) === 'V' ? 3 : res(r) === 'N' ? 1 : 0);
  const home = M.filter(r => r.home), away = M.filter(r => !r.home);
  const ptsOf = a => sum(a, r => res(r) === 'V' ? 3 : res(r) === 'N' ? 1 : 0);
  let best = 0, cur = 0; M.forEach(r => { cur = res(r) === 'D' ? 0 : cur + 1; best = Math.max(best, cur); });
  const facts = [
    ['Points sur les 5 derniers', `${pts5} / 15`],
    ['Points par match à domicile', fr(ptsOf(home) / home.length, 2)],
    ['Points par match à l\'extérieur', fr(ptsOf(away) / away.length, 2)],
    ['Plus longue série sans défaite', best + ' matchs'],
    ['xG contre, 5 derniers / saison', `${fr(sum(last, r => r.xga) / 5, 2)} / ${fr(XGA / N, 2)}`],
  ];
  document.getElementById('facts').innerHTML = facts.map(f => `<div class="fact"><span>${f[0]}</span><b>${f[1]}</b></div>`).join('');
})();

// ---------- Match par match ----------
(function matches() {
  const box = document.getElementById('matches-box');
  const w = 720, h = 230, ml = 30, mr = 8, mt = 12, mb = 28;
  const lim = Math.max(4, Math.ceil(Math.max(...M.map(r => Math.abs(r.gf - r.ga)), ...M.map(r => Math.abs(r.xg - r.xga)))));
  const bw = (w - ml - mr) / N, y = v => mt + (h - mt - mb) * (lim - v) / (2 * lim);
  let s = '';
  for (let v = -lim; v <= lim; v += 2) s += `<line x1="${ml}" x2="${w - mr}" y1="${y(v)}" y2="${y(v)}" stroke="${v === 0 ? 'var(--axis)' : 'var(--line)'}"/><text x="${ml - 6}" y="${y(v) + 4}" text-anchor="end" font-size="11" fill="var(--muted)">${v > 0 ? '+' + v : v}</text>`;
  M.forEach((r, i) => {
    const d = r.gf - r.ga, cx = ml + i * bw + bw / 2, bwid = Math.max(6, bw - 6);
    const color = d > 0 ? 'var(--pos)' : d < 0 ? 'var(--neg)' : 'var(--draw)';
    const top = d >= 0 ? y(d) : y(0), hh = Math.max(2, Math.abs(y(d) - y(0)));
    s += `<rect x="${cx - bwid / 2}" y="${d === 0 ? y(0) - 1 : top}" width="${bwid}" height="${d === 0 ? 2 : hh}" rx="3" fill="${color}"/>`;
    s += `<circle cx="${cx}" cy="${y(r.xg - r.xga)}" r="4" fill="var(--ink)" stroke="var(--surface)" stroke-width="2"/>`;
    if ((r.md - 1) % 5 === 0 || r.md === N) s += `<text x="${cx}" y="${h - 8}" text-anchor="middle" font-size="11" fill="var(--muted)">J${r.md}</text>`;
    s += `<rect x="${ml + i * bw}" y="${mt}" width="${bw}" height="${h - mt - mb}" fill="transparent" data-i="${i}" class="mhit"/>`;
  });
  box.innerHTML = `<svg class="chart" viewBox="0 0 ${w} ${h}" role="img" aria-label="Différence de buts et différence d'xG pour chacun des ${N} matchs">${s}</svg>`;
  const svg = box.querySelector('svg');
  box.querySelectorAll('.mhit').forEach(el => {
    el.addEventListener('mouseenter', () => {
      const r = M[+el.dataset.i], sc = svg.getBoundingClientRect().width / w;
      const cx = (ml + (+el.dataset.i) * bw + bw / 2) * sc;
      showTip(box, `<b>J${r.md} · ${r.gf}–${r.ga} ${r.home ? 'vs' : 'à'} ${esc(short(r.opp))}</b>
        <div class="row"><span>xG pour / contre</span><span>${fr(r.xg, 2)} / ${fr(r.xga, 2)}</span></div>
        <div class="row"><span>Tirs (cadrés)</span><span>${r.sh} (${r.sot})</span></div>
        <div class="row"><span>Possession</span><span>${fr(r.poss, 0)} %</span></div>`, cx, y(Math.max(r.gf - r.ga, 0)) * sc);
    });
    el.addEventListener('mouseleave', hideTip);
  });
  const rows = M.map(r => `<tr><td class="l">J${r.md}</td><td class="l">${new Date(r.date).toLocaleDateString('fr-FR')}</td><td class="l">${r.home ? 'Dom.' : 'Ext.'}</td><td class="l">${esc(short(r.opp))}</td><td>${r.gf}–${r.ga}</td><td>${fr(r.xg, 2)}</td><td>${fr(r.xga, 2)}</td><td>${fr(r.poss, 0)} %</td><td>${r.sh}</td><td>${r.sot}</td><td>${pct(r.pc, r.pt, 0)} %</td><td>${fr(r.km, 1)}</td></tr>`).join('');
  document.getElementById('matches-table').innerHTML = `<thead><tr><th>J.</th><th class="l">Date</th><th class="l">Lieu</th><th class="l">Adversaire</th><th>Score</th><th>xG</th><th>xG contre</th><th>Poss.</th><th>Tirs</th><th>Cadrés</th><th>Passes</th><th>km</th></tr></thead><tbody>${rows}</tbody>`;
})();

// ---------- Indicateurs ----------
// Chaque indicateur : valeur par match (série) et valeur saison (agrégée sur les sommes pour les ratios).
const IND = [
  { group: 'Attaque', name: 'xG par match', kind: 'Collecté', unit: '', d: 2, per: r => r.xg, season: () => XG / N, better: 1 },
  { group: 'Attaque', name: 'Tirs par match', kind: 'Collecté', unit: '', d: 1, per: r => r.sh, season: () => sum(M, r => r.sh) / N, better: 1 },
  { group: 'Attaque', name: 'Tirs cadrés', kind: 'Calculé', unit: '%', d: 0, per: r => r.sh ? 100 * r.sot / r.sh : null, season: () => 100 * sum(M, r => r.sot) / sum(M, r => r.sh), better: 1 },
  { group: 'Attaque', name: 'Conversion des tirs', kind: 'Calculé', unit: '%', d: 1, per: r => r.sh ? 100 * r.gf / r.sh : null, season: () => 100 * GF / sum(M, r => r.sh), better: 1 },
  { group: 'Défense', name: 'xG contre par match', kind: 'Collecté', unit: '', d: 2, per: r => r.xga, season: () => XGA / N, better: -1 },
  { group: 'Défense', name: 'Tirs concédés par match', kind: 'Collecté', unit: '', d: 1, per: r => r.sh_a, season: () => sum(M, r => r.sh_a) / N, better: -1 },
  { group: 'Défense', name: 'Duels gagnés', kind: 'Calculé', unit: '%', d: 1, per: r => 100 * (r.gdw + r.adw) / (r.gdt + r.adt), season: () => 100 * sum(M, r => r.gdw + r.adw) / sum(M, r => r.gdt + r.adt), better: 1 },
  { group: 'Défense', name: 'Récupérations par match', kind: 'Collecté', unit: '', d: 1, per: r => r.rec, season: () => sum(M, r => r.rec) / N, better: 1 },
  { group: 'Construction', name: 'Possession', kind: 'Collecté', unit: '%', d: 1, per: r => r.poss, season: () => sum(M, r => r.poss) / N, better: 1 },
  { group: 'Construction', name: 'Précision des passes', kind: 'Calculé', unit: '%', d: 1, per: r => 100 * r.pc / r.pt, season: () => 100 * sum(M, r => r.pc) / sum(M, r => r.pt), better: 1 },
  { group: 'Construction', name: 'Passes progressives par match', kind: 'Collecté', unit: '', d: 1, per: r => r.pp, season: () => sum(M, r => r.pp) / N, better: 1 },
  { group: 'Construction', name: 'Occasions créées par match', kind: 'Collecté', unit: '', d: 1, per: r => r.cc, season: () => sum(M, r => r.cc) / N, better: 1 },
  { group: 'Intensité et discipline', name: 'Distance parcourue', kind: 'Calculé', unit: 'km', d: 1, per: r => r.km, season: () => sum(M, r => r.km) / N, better: 1 },
  { group: 'Intensité et discipline', name: 'Duels aériens gagnés', kind: 'Calculé', unit: '%', d: 1, per: r => r.adt ? 100 * r.adw / r.adt : null, season: () => 100 * sum(M, r => r.adw) / sum(M, r => r.adt), better: 1 },
  { group: 'Intensité et discipline', name: 'Fautes par match', kind: 'Collecté', unit: '', d: 1, per: r => r.fo, season: () => sum(M, r => r.fo) / N, better: -1 },
  { group: 'Intensité et discipline', name: 'Cartons par match', kind: 'Calculé', unit: '', d: 2, per: r => r.yc + 2 * r.rc, season: () => sum(M, r => r.yc + 2 * r.rc) / N, better: -1, note: 'jaune = 1, rouge = 2' },
];
function spark(values, better) {
  const v = values.map(x => x == null ? null : +x), ok = v.filter(x => x != null);
  const mn = Math.min(...ok), mx = Math.max(...ok), w = 200, h = 30, pad = 3;
  const x = i => pad + i * (w - 2 * pad) / (v.length - 1), y = val => mx === mn ? h / 2 : pad + (h - 2 * pad) * (1 - (val - mn) / (mx - mn));
  const pts = v.map((val, i) => val == null ? null : [x(i), y(val)]).filter(Boolean);
  const line = pts.map((p, i) => (i ? 'L' : 'M') + p[0].toFixed(1) + ' ' + p[1].toFixed(1)).join(' ');
  const area = line + ` L${pts[pts.length - 1][0]} ${h} L${pts[0][0]} ${h} Z`;
  const last = pts[pts.length - 1];
  const avg = ok.reduce((a, b) => a + b, 0) / ok.length;
  return `<svg class="spark" viewBox="0 0 ${w} ${h}" preserveAspectRatio="none" aria-hidden="true"><line x1="0" x2="${w}" y1="${y(avg)}" y2="${y(avg)}" stroke="var(--line)" stroke-dasharray="3 3" vector-effect="non-scaling-stroke"/><path d="${area}" fill="var(--s1-fill)"/><path d="${line}" fill="none" stroke="var(--s1)" stroke-width="1.5" vector-effect="non-scaling-stroke"/><circle cx="${last[0]}" cy="${last[1]}" r="2.5" fill="var(--s1)"/></svg>`;
}
(function indicators() {
  const groups = [...new Set(IND.map(i => i.group))];
  document.getElementById('indicators').innerHTML = groups.map(gname => `<div class="ind-group"><h3>${gname}</h3><div class="ind-grid">${IND.filter(i => i.group === gname).map(ind => {
    const season = ind.season(), last5 = M.slice(-5).map(ind.per).filter(v => v != null);
    const l5 = last5.reduce((a, b) => a + b, 0) / last5.length, delta = l5 - season;
    const rel = season ? Math.abs(delta) / Math.abs(season) : 0;
    const dir = rel < 0.03 ? 'flat' : (delta * ind.better > 0 ? 'up' : 'down');
    const arrow = rel < 0.03 ? '=' : delta > 0 ? '▲' : '▼';
    const word = dir === 'flat' ? 'stable' : dir === 'up' ? 'mieux' : 'moins bien';
    return `<div class="ind"><div class="top"><span class="name">${ind.name}</span><span class="kind${ind.kind === 'Calculé' ? ' calc' : ''}">${ind.kind}</span></div>
      <span class="val">${fr(season, ind.d)}${ind.unit ? `<small>${ind.unit}</small>` : ''}</span>
      <span class="trend"><span class="delta ${dir}">${arrow} ${delta >= 0 ? '+' : ''}${fr(delta, ind.d)}</span><span>5 derniers, ${word}</span></span>
      ${spark(M.map(ind.per), ind.better)}</div>`;
  }).join('')}</div></div>`).join('');
})();

// ---------- Effectif ----------
const FAM = { 'gardien': 'GB', 'défenseur central': 'DC', 'latéral': 'LAT', 'milieu défensif': 'MD', 'milieu relayeur': 'MR', 'milieu offensif': 'MO', 'ailier': 'AIL', 'avant-centre': 'AC' };
const MAXMIN = N * 90;
const SQ = SQUAD.map(p => ({
  ...p,
  minPct: 100 * p.mins / MAXMIN,
  ga90: p.mins ? 90 * (p.g + p.a) / p.mins : 0,
  passPct: p.pt ? 100 * p.pc / p.pt : null,
  duelPct: p.dt ? 100 * p.dw / p.dt : null,
  cards: p.yc + 2 * p.rc,
}));
(function squadFacts() {
  const used = SQ.filter(p => p.mins > 0).length;
  const sorted = [...SQ].sort((a, b) => b.mins - a.mins);
  const top11 = sum(sorted.slice(0, 11), p => p.mins) / sum(SQ, p => p.mins);
  const heavy = SQ.filter(p => p.minPct >= 80).length;
  const topScorer = [...SQ].sort((a, b) => b.g - a.g)[0];
  const facts = [
    ['Joueurs utilisés', used + ' / ' + SQ.length],
    ['Temps de jeu des 11 plus utilisés', fr(100 * top11, 0) + ' %'],
    ['Joueurs à plus de 80 % des minutes', heavy],
    ['Meilleur buteur', `${esc(topScorer.name)} (${topScorer.g})`],
  ];
  document.getElementById('squad-facts').innerHTML = facts.map(f => `<div class="fact" style="border:1px solid var(--line);border-radius:8px;padding:10px 12px;flex-direction:column;gap:2px"><span style="font-size:0.78rem">${f[0]}</span><b class="num" style="font-size:1.35rem">${f[1]}</b></div>`).join('');
})();
const COLS = [
  { key: 'name', label: 'Joueur', l: true, fmt: p => `<span class="player"><span>${p.num ? '<span class="muted">' + p.num + '</span> ' : ''}${esc(p.name)}</span><small>${p.family ? p.family : '–'}</small></span>`, sortVal: p => p.name },
  { key: 'family', label: 'Poste', fmt: p => `<span class="chip" title="${p.family || ''}">${FAM[p.family] || '–'}</span>` },
  { key: 'mj', label: 'MJ', fmt: p => p.mj },
  { key: 'tit', label: 'Tit.', fmt: p => p.tit },
  { key: 'mins', label: 'Minutes', fmt: p => `<span class="minbar"><span class="track"><i style="width:${Math.min(100, p.minPct).toFixed(0)}%"></i></span>${p.mins}</span>` },
  { key: 'minPct', label: '% min.', fmt: p => fr(p.minPct, 0) + ' %' },
  { key: 'g', label: 'Buts', fmt: p => p.g },
  { key: 'a', label: 'Passes D.', fmt: p => p.a },
  { key: 'ga90', label: 'B+PD /90', fmt: p => fr(p.ga90, 2) },
  { key: 'xg', label: 'xG', fmt: p => p.xg != null ? fr(p.xg, 2) : '–' },
  { key: 'passPct', label: 'Passes %', fmt: p => p.passPct != null ? fr(p.passPct, 0) : '–' },
  { key: 'duelPct', label: 'Duels %', fmt: p => p.duelPct != null ? fr(p.duelPct, 0) : '–' },
  { key: 'rating', label: 'Note moy.', fmt: p => p.rating != null ? fr(p.rating, 2) : '–' },
  { key: 'score', label: 'Rôle et apport', fmt: p => p.score != null ? `${fr(p.score, 1)} <span class="muted">· ${fr(100 * p.reliability, 0)} %</span>` : '<span class="muted" title="Fiabilité insuffisante ou poste non évalué">–</span>' },
  { key: 'cards', label: 'Cartons', fmt: p => p.yc || p.rc ? `${p.yc}J${p.rc ? ' ' + p.rc + 'R' : ''}` : '–' },
];
let sortKey = 'mins', sortDir = -1;
function renderSquad() {
  const rows = [...SQ].sort((a, b) => {
    const col = COLS.find(c => c.key === sortKey), va = col.sortVal ? col.sortVal(a) : a[sortKey], vb = col.sortVal ? col.sortVal(b) : b[sortKey];
    if (va == null) return 1; if (vb == null) return -1;
    return (typeof va === 'string' ? va.localeCompare(vb, 'fr') : va - vb) * sortDir;
  });
  document.getElementById('squad').innerHTML = `<thead><tr>${COLS.map(c => `<th class="${c.l ? 'l' : ''}"><button type="button" data-k="${c.key}" aria-sort="${sortKey === c.key ? (sortDir > 0 ? 'ascending' : 'descending') : 'none'}">${c.label}</button></th>`).join('')}</tr></thead>
    <tbody>${rows.map(p => `<tr>${COLS.map(c => `<td class="${c.l ? 'l' : ''}">${c.fmt(p)}</td>`).join('')}</tr>`).join('')}</tbody>`;
  document.querySelectorAll('#squad th button').forEach(b => b.addEventListener('click', () => {
    const k = b.dataset.k; sortDir = sortKey === k ? -sortDir : (k === 'name' || k === 'family' ? 1 : -1); sortKey = k; renderSquad();
    document.querySelector(`#squad th button[data-k="${k}"]`).focus();
  }));
}
renderSquad();

// ---------- Points d'attention ----------
(function alerts() {
  const out = [];
  const fin = GF - XG;
  out.push(fin < -2
    ? { sev: 'serious', t: 'Finition en dessous du volume créé', p: `${GF} buts pour ${fr(XG, 1)} xG produits (${fr(fin, 1)}). Le volume d'occasions n'est pas le problème : travailler la conversion devant le but.` }
    : fin > 2 ? { sev: 'warning', t: 'Réussite offensive au-dessus des xG', p: `${GF} buts pour ${fr(XG, 1)} xG (+${fr(fin, 1)}). Une partie des résultats repose sur une efficacité difficile à maintenir.` }
    : { sev: 'good', t: 'Finition conforme au volume créé', p: `${GF} buts pour ${fr(XG, 1)} xG : l'efficacité correspond aux occasions produites.` });
  const xga5 = sum(M.slice(-5), r => r.xga) / 5, xgaS = XGA / N;
  if (xga5 > xgaS * 1.15) out.push({ sev: 'serious', t: 'Défense plus exposée récemment', p: `${fr(xga5, 2)} xG concédés par match sur les 5 derniers, contre ${fr(xgaS, 2)} sur la saison.` });
  else out.push({ sev: 'good', t: 'Solidité défensive maintenue', p: `${fr(xga5, 2)} xG concédés par match sur les 5 derniers, pour ${fr(xgaS, 2)} sur la saison.` });
  const disc = SQ.filter(p => p.yc >= 5 || p.rc >= 1).sort((a, b) => b.cards - a.cards);
  if (disc.length) out.push({ sev: disc.some(p => p.rc) ? 'serious' : 'warning', t: 'Discipline', p: disc.slice(0, 4).map(p => `${esc(p.name)} (${p.yc}J${p.rc ? ', ' + p.rc + 'R' : ''})`).join(', ') + '. Risque de suspension sur les prochains matchs.' });
  const heavy = SQ.filter(p => p.minPct >= 80).sort((a, b) => b.minPct - a.minPct);
  if (heavy.length) out.push({ sev: 'warning', t: 'Charge de jeu concentrée', p: heavy.map(p => `${esc(p.name)} (${fr(p.minPct, 0)} %)`).join(', ') + ' des minutes possibles. Prévoir une rotation pour limiter la fatigue.' });
  const scorers = [...SQ].sort((a, b) => b.g - a.g), share = GF ? scorers[0].g / GF : 0;
  if (share >= 0.3) out.push({ sev: 'warning', t: 'Dépendance à un buteur', p: `${esc(scorers[0].name)} a marqué ${fr(100 * share, 0)} % des buts de l'équipe (${scorers[0].g} sur ${GF}).` });
  else out.push({ sev: 'good', t: 'Buts bien répartis', p: `Le meilleur buteur, ${esc(scorers[0].name)}, pèse ${fr(100 * share, 0)} % des buts : la menace offensive est partagée.` });
  const home = M.filter(r => r.home), away = M.filter(r => !r.home);
  const ppg = a => sum(a, r => res(r) === 'V' ? 3 : res(r) === 'N' ? 1 : 0) / a.length;
  if (Math.abs(ppg(home) - ppg(away)) >= 0.5) out.push({ sev: 'warning', t: ppg(home) > ppg(away) ? 'Écart domicile / extérieur' : 'Moins performant à domicile', p: `${fr(ppg(home), 2)} point par match à domicile contre ${fr(ppg(away), 2)} à l'extérieur.` });
  const SEV = { critical: 'Critique', serious: 'Important', warning: 'À surveiller', good: 'Point fort' };
  const order = { critical: 0, serious: 1, warning: 2, good: 3 };
  document.getElementById('alerts').innerHTML = out.sort((a, b) => order[a.sev] - order[b.sev]).map(a => `<div class="alert ${a.sev}"><div class="head"><b>${a.t}</b><span class="sev ${a.sev}">${SEV[a.sev]}</span></div><p>${a.p}</p></div>`).join('');
})();

// ---------- Prochain match (modèle appris) ----------
(function nextMatch() {
  const MODEL = DATA.model;
  const club = id => CLUBS.find(c => c.id === id);
  const us = club(TEAM_ID);
  const sumC = f => CLUBS.reduce((s, c) => s + f(c), 0);
  const L = sumC(c => c.xg) / sumC(c => c.j);
  const HOME = (sumC(c => c.xgh) / sumC(c => c.jh)) / L, AWAY = (sumC(c => c.xgaw) / sumC(c => c.j - c.jh)) / L;
  const LPOSS = sumC(c => c.poss) / CLUBS.length, LAER = sumC(c => c.aw) / sumC(c => c.at);
  const att = c => 0.7 * (c.xg / c.j) / L + 0.3 * (c.last5.reduce((s, r) => s + r[3], 0) / c.last5.length) / L;
  const def = c => 0.7 * (c.xga / c.j) / L + 0.3 * (c.last5.reduce((s, r) => s + r[4], 0) / c.last5.length) / L;
  const pois = (k, l) => Math.exp(-l) * Math.pow(l, k) / [1, 1, 2, 6, 24, 120, 720, 5040, 40320][k];
  const FAMS = MODEL.fams;
  const ABBR = { 'gardien': 'GB', 'défenseur central': 'DC', 'latéral': 'LAT', 'milieu défensif': 'MD', 'milieu relayeur': 'MR', 'milieu offensif': 'MO', 'ailier': 'AIL', 'avant-centre': 'AC' };
  const LABEL = { rating_fam: 'Note moyenne au poste', rating_avg: 'Note moyenne (saison)', rating_last5: 'Forme, 5 derniers', rating_last3: 'Forme, 3 derniers', itc90: 'Interceptions / 90', ga90: 'Buts + passes D. / 90',
    kp90: 'Passes clés / 90', aerial_pct: 'Duels aériens gagnés', cc90: 'Occasions créées / 90', rec90: 'Récupérations / 90', xg90: 'xG / 90', pass_pct: 'Précision des passes', duel_pct: 'Duels gagnés', save_pct: 'Arrêts', fam_share: 'Habitude du poste',
    near_share: 'Postes voisins', starter_rate: 'Taux de titularisation', mins_last3: 'Charge récente', role_score: 'Score Rôle et apport', opp_att: 'Attaque adverse', opp_def: 'Défense adverse', pp90: 'Passes progressives / 90', tackle_pct: 'Tacles réussis', cards_pm: 'Cartons / match', n_prior: 'Matchs disputés' };

  // Suspensions : rouge au dernier match, ou 5e (10e…) jaune reçu au dernier match
  const lastMd = Math.max(...PM.map(r => r[1]));
  const suspended = {};
  SQUAD.forEach(sq => {
    let yc = 0;
    PM.filter(r => r[0] === sq.id).sort((a, b) => a[1] - b[1]).forEach(r => {
      const before = yc; yc += r[20];
      if (r[1] === lastMd && (r[21] || Math.floor(yc / 5) > Math.floor(before / 5))) suspended[sq.id] = r[21] ? 'carton rouge en J' + lastMd : yc + 'e carton jaune en J' + lastMd;
    });
  });

  // Prédiction : note attendue d'un joueur à un poste, face à un adversaire et un lieu donnés
  function features(pid, fam, oppId, home) {
    const base = MODEL.players[pid][fam], o = MODEL.opponents[oppId];
    const x = { ...base, opp_att: o.att, opp_def: o.def, opp_ppg: o.ppg, own_att: MODEL.own.att, own_def: MODEL.own.def, own_ppg: MODEL.own.ppg, home: home ? 1 : 0 };
    FAMS.forEach(f => x['pos_' + f] = f === fam ? 1 : 0);
    MODEL.inter.forEach(c => FAMS.forEach(f => x[c + '__x__' + f] = x[c] * x['pos_' + f]));
    return x;
  }
  const predict = x => Object.entries(MODEL.coef).reduce((s, [k, w]) => s + w * (x[k] ?? 0), MODEL.intercept);
  const eligible = (pid, fam) => {
    const b = MODEL.players[pid]?.[fam]; if (!b) return false;
    const gk = MODEL.players[pid]['gardien'].fam_share;
    if (fam === 'gardien') return gk >= 0.2;
    return gk < 0.5;
  };
  // Explication : contributions de chaque variable par rapport à la moyenne des candidats au même poste
  function explain(pid, fam, oppId, home, pool) {
    const xs = pool.map(id => features(id, fam, oppId, home)), x = features(pid, fam, oppId, home), agg = {};
    Object.entries(MODEL.coef).forEach(([k, w]) => {
      const mean = xs.reduce((s, v) => s + (v[k] ?? 0), 0) / xs.length;
      const base = k.split('__x__')[0];
      agg[base] = (agg[base] || 0) + w * ((x[k] ?? 0) - mean);
    });
    return Object.entries(agg).filter(([k]) => !k.startsWith('pos_') && !['home', 'opp_att', 'opp_def', 'opp_ppg', 'own_att', 'own_def', 'own_ppg'].includes(k)).sort((a, b) => Math.abs(b[1]) - Math.abs(a[1])).slice(0, 3);
  }

  // Affectation optimale (algorithme hongrois, minimisation) : lignes = postes, colonnes = joueurs
  function hungarian(cost) {
    const n = cost.length, m = cost[0].length, INF = 1e9, u = Array(n + 1).fill(0), v = Array(m + 1).fill(0), p = Array(m + 1).fill(0), way = Array(m + 1).fill(0);
    for (let i = 1; i <= n; i++) {
      p[0] = i; let j0 = 0; const minv = Array(m + 1).fill(INF), used = Array(m + 1).fill(false);
      do {
        used[j0] = true; const i0 = p[j0]; let delta = INF, j1 = 0;
        for (let j = 1; j <= m; j++) if (!used[j]) {
          const cur = cost[i0 - 1][j - 1] - u[i0] - v[j];
          if (cur < minv[j]) { minv[j] = cur; way[j] = j0; }
          if (minv[j] < delta) { delta = minv[j]; j1 = j; }
        }
        for (let j = 0; j <= m; j++) { if (used[j]) { u[p[j]] += delta; v[j] -= delta; } else minv[j] -= delta; }
        j0 = j1;
      } while (p[j0] !== 0);
      do { const j1 = way[j0]; p[j0] = p[j1]; j0 = j1; } while (j0);
    }
    const res = Array(n).fill(-1);
    for (let j = 1; j <= m; j++) if (p[j]) res[p[j] - 1] = j - 1;
    return res;
  }

  const FORMS = {
    '4-3-3': [['gardien', 50, 92], ['latéral', 14, 72], ['défenseur central', 37, 76], ['défenseur central', 63, 76], ['latéral', 86, 72], ['milieu défensif', 50, 57], ['milieu relayeur', 30, 47], ['milieu offensif', 70, 47], ['ailier', 15, 25], ['avant-centre', 50, 17], ['ailier', 85, 25]],
    '4-2-3-1': [['gardien', 50, 92], ['latéral', 14, 72], ['défenseur central', 37, 76], ['défenseur central', 63, 76], ['latéral', 86, 72], ['milieu défensif', 36, 58], ['milieu relayeur', 64, 58], ['ailier', 16, 36], ['milieu offensif', 50, 38], ['ailier', 84, 36], ['avant-centre', 50, 17]],
  };
  const byId = Object.fromEntries(SQUAD.map(p => [p.id, p]));
  const lastName = n => n.split(' ').slice(1).join(' ') || n;

  let oppId = leader.id === TEAM_ID ? TABLE[1].id : leader.id, home = true;
  const sel = document.getElementById('opp-select');
  sel.innerHTML = TABLE.filter(t => t.id !== TEAM_ID).map(t => `<option value="${t.id}">${TABLE.indexOf(t) + 1}. ${esc(short(t.name))}</option>`).join('');
  sel.value = String(oppId);
  sel.addEventListener('change', () => { oppId = +sel.value; render(); });
  const bh = document.getElementById('venue-home'), ba = document.getElementById('venue-away');
  const setVenue = h => { home = h; bh.setAttribute('aria-pressed', String(h)); ba.setAttribute('aria-pressed', String(!h)); render(); };
  bh.addEventListener('click', () => setVenue(true)); ba.addEventListener('click', () => setVenue(false));

  function render() {
    const opp = club(oppId), oppAtt = att(opp), oppDef = def(opp);
    const lUs = L * att(us) * oppDef * (home ? HOME : AWAY), lOpp = L * oppAtt * def(us) * (home ? AWAY : HOME);
    let pw = 0, pd = 0, pl = 0, best = [0, 0, 0];
    for (let i = 0; i <= 8; i++) for (let j = 0; j <= 8; j++) { const q = pois(i, lUs) * pois(j, lOpp); if (i > j) pw += q; else if (i === j) pd += q; else pl += q; if (q > best[2]) best = [i, j, q]; }
    const tot = pw + pd + pl; pw /= tot; pd /= tot; pl /= tot;
    const prob = document.getElementById('prob');
    prob.setAttribute('aria-label', `Victoire ${fr(100 * pw, 0)} %, nul ${fr(100 * pd, 0)} %, défaite ${fr(100 * pl, 0)} %`);
    prob.innerHTML = `<div class="pw" style="flex:${pw}">${fr(100 * pw, 0)} %</div><div class="pd" style="flex:${pd}">${fr(100 * pd, 0)} %</div><div class="pl" style="flex:${pl}">${fr(100 * pl, 0)} %</div>`;
    document.getElementById('scoreline').innerHTML = `<div><p class="eyebrow">Score le plus probable</p><span class="big">${best[0]}–${best[1]}</span></div>
      <div><p class="eyebrow">Buts attendus</p><span class="big" style="font-size:1.8rem">${fr(lUs, 2)} <small style="font-size:1rem;color:var(--ink-2)">${esc(CLUB_LABEL)}</small> · ${fr(lOpp, 2)} <small style="font-size:1rem;color:var(--ink-2)">${esc(short(opp.name).split(' ').pop())}</small></span></div>`;
    const oPoss = opp.poss, oAer = opp.aw / opp.at;
    const reads = [['Attaque (xG produits, pondérés forme)', oppAtt - 1, 'up'], ['Défense (xG concédés, pondérés forme)', oppDef - 1, 'down'], ['Possession moyenne', (oPoss - LPOSS) / LPOSS, 'up'], ['Duels aériens gagnés', (oAer - LAER) / LAER, 'up']];
    document.getElementById('reads').innerHTML = `<p class="eyebrow" style="margin-top:4px">Profil de ${esc(short(opp.name))}, par rapport à la moyenne</p>` +
      reads.map(r => { const v = r[1], cls = Math.abs(v) < 0.04 ? '' : (v > 0 ? r[2] : (r[2] === 'up' ? 'down' : 'up')); return `<div class="read"><span class="k">${r[0]}</span><span class="v ${cls}">${v >= 0 ? '+' : ''}${fr(100 * v, 0)} %</span></div>`; }).join('');
    const formation = (oppAtt > 1.06 || oPoss > LPOSS + 1.5) ? '4-2-3-1' : '4-3-3';
    const plan = [];
    if (oppAtt > 1.06) plan.push('attaque adverse au-dessus de la moyenne : double pivot devant la défense');
    if (oPoss > LPOSS + 1.5) plan.push('adversaire dominant au ballon : densité au milieu');
    if (oppDef > 1.06) plan.push('défense adverse perméable : trois joueurs offensifs derrière l\'avant-centre');
    if (!plan.length) plan.push('adversaire dans la moyenne : système offensif en 4-3-3');
    document.getElementById('plan').innerHTML = `<b>Système ${formation}</b><span>${plan.map(x => x.charAt(0).toUpperCase() + x.slice(1)).join('. ')}.</span>`;

    // Prédictions pour tous les joueurs disponibles, à chaque poste
    const avail = SQUAD.filter(p => !suspended[p.id] && MODEL.players[p.id]).map(p => p.id);
    const pred = {};
    avail.forEach(pid => FAMS.forEach(f => { if (eligible(pid, f)) pred[pid + '|' + f] = predict(features(pid, f, oppId, home)); }));
    const slots = FORMS[formation];
    const cost = slots.map(s => avail.map(pid => pred[pid + '|' + s[0]] != null ? -pred[pid + '|' + s[0]] : 100));
    const assign = hungarian(cost);
    const xi = slots.map((s, si) => ({ s, pid: avail[assign[si]], v: pred[avail[assign[si]] + '|' + s[0]] }));
    const xiIds = new Set(xi.map(x => x.pid));
    document.getElementById('xi-total').textContent = `note prédite moyenne ${fr(xi.reduce((a, x) => a + x.v, 0) / 11, 2)}`;

    // Terrain
    const W_ = 340, H_ = 420, box = document.getElementById('pitch-box');
    const X = v => 10 + v / 100 * (W_ - 20), Y = v => 10 + v / 100 * (H_ - 20);
    let g = `<rect x="10" y="10" width="${W_ - 20}" height="${H_ - 20}" rx="6" fill="var(--accent-soft)" stroke="var(--axis)"/>
      <line x1="10" x2="${W_ - 10}" y1="${H_ / 2}" y2="${H_ / 2}" stroke="var(--axis)"/><circle cx="${W_ / 2}" cy="${H_ / 2}" r="38" fill="none" stroke="var(--axis)"/>
      <rect x="${W_ / 2 - 80}" y="10" width="160" height="58" fill="none" stroke="var(--axis)"/><rect x="${W_ / 2 - 80}" y="${H_ - 68}" width="160" height="58" fill="none" stroke="var(--axis)"/>
      <rect x="${W_ / 2 - 36}" y="10" width="72" height="22" fill="none" stroke="var(--axis)"/><rect x="${W_ / 2 - 36}" y="${H_ - 32}" width="72" height="22" fill="none" stroke="var(--axis)"/>
      <text x="16" y="${H_ - 16}" font-size="9.5" fill="var(--muted)">attaque ↑</text>`;
    xi.forEach((x, si) => {
      const p = byId[x.pid], cx = X(x.s[1]), cy = Y(x.s[2]);
      g += `<g class="pl" data-si="${si}" tabindex="0" role="button" aria-label="${esc(p.name)}, ${x.s[0]}, note prédite ${fr(x.v, 2)}">
        <circle cx="${cx}" cy="${cy}" r="17" fill="var(--s1)" stroke="var(--surface)" stroke-width="2.5"/>
        <text x="${cx}" y="${cy + 5}" text-anchor="middle" font-size="13" font-weight="700" fill="#fff" font-family="var(--font-display)">${p.num ?? ''}</text>
        <text x="${cx}" y="${cy + 31}" text-anchor="middle" font-size="11" font-weight="600" fill="var(--ink)">${esc(lastName(p.name))}</text>
        <text x="${cx}" y="${cy + 44}" text-anchor="middle" font-size="9.5" fill="var(--ink-2)">${ABBR[x.s[0]]} · ${fr(x.v, 2)}</text></g>`;
    });
    box.innerHTML = `<svg class="pitch" viewBox="0 0 ${W_} ${H_ + 6}" role="img" aria-label="Onze optimal en ${formation}">${g}</svg>`;
    const svg = box.querySelector('svg');
    box.querySelectorAll('.pl').forEach(el => {
      const show = () => {
        const x = xi[+el.dataset.si], p = byId[x.pid], sc = svg.getBoundingClientRect().width / W_;
        const pool = avail.filter(id => pred[id + '|' + x.s[0]] != null);
        const why = explain(x.pid, x.s[0], oppId, home, pool);
        showTip(box, `<b>${esc(p.name)} · ${x.s[0]}</b><div class="row"><span>Note prédite</span><span><b>${fr(x.v, 2)}</b></span></div>
          <div class="muted" style="margin-top:4px">Écart à la moyenne des candidats au poste :</div>
          ${why.map(([k, v]) => `<div class="row"><span>${LABEL[k] || k}</span><span>${v >= 0 ? '+' : ''}${fr(v, 2)}</span></div>`).join('')}`, X(x.s[1]) * sc, Y(x.s[2]) * sc);
      };
      el.addEventListener('mouseenter', show); el.addEventListener('focus', show);
      el.addEventListener('mouseleave', hideTip); el.addEventListener('blur', hideTip);
    });

    // Remplaçants : meilleure note prédite restante, chacun à son meilleur poste
    const bench = avail.filter(id => !xiIds.has(id)).map(id => {
      const b = FAMS.filter(f => pred[id + '|' + f] != null && MODEL.players[id][f].fam_share + MODEL.players[id][f].near_share >= 0.15).map(f => ({ f, v: pred[id + '|' + f] })).sort((a, b) => b.v - a.v)[0];
      return b ? { id, ...b } : null;
    }).filter(Boolean).sort((a, b) => b.v - a.v).slice(0, 7);
    document.getElementById('bench').innerHTML = bench.map(r => `<div class="b"><span><span class="muted">${byId[r.id].num ?? ''}</span> ${esc(byId[r.id].name)}</span><small>${r.f} · ${fr(r.v, 2)}</small></div>`).join('');
    const out = Object.keys(suspended);
    document.getElementById('out').innerHTML = (out.length ? `<b>Indisponible :</b> ${out.map(id => `${esc(byId[id].name)} (suspendu, ${suspended[id]})`).join(', ')}. ` : '<b>Aucun suspendu.</b> ') +
      'Blessures non prises en compte : aucune donnée d\'indisponibilité médicale pour les joueurs démo.';

    // Grille des postes : 3 meilleurs disponibles par poste
    const fams = FAMS;
    document.getElementById('grid-pos').innerHTML = `<thead><tr><th class="l">Poste</th><th class="l">1er choix</th><th class="l">2e choix</th><th class="l">3e choix</th><th>Écart 1er / 2e</th></tr></thead><tbody>` +
      fams.map(f => {
        const ranked = avail.filter(id => pred[id + '|' + f] != null && MODEL.players[id][f].fam_share + MODEL.players[id][f].near_share >= 0.1)
          .map(id => ({ id, v: pred[id + '|' + f] })).sort((a, b) => b.v - a.v).slice(0, 3);
        const pool = avail.filter(id => pred[id + '|' + f] != null);
        const cell = r => {
          if (!r) return '<td class="l muted">–</td>';
          const why = explain(r.id, f, oppId, home, pool)[0];
          const inXi = xi.some(x => x.pid === r.id && x.s[0] === f);
          return `<td class="l"><div class="cand"><span class="n${inXi ? ' xi' : ''}">${esc(byId[r.id].name)}</span><span class="pred">${fr(r.v, 2)}</span><small>${why ? (LABEL[why[0]] || why[0]) + ' ' + (why[1] >= 0 ? '+' : '') + fr(why[1], 2) : ''}</small></div></td>`;
        };
        const gap = ranked.length > 1 ? ranked[0].v - ranked[1].v : null;
        return `<tr><td class="l"><b>${f.charAt(0).toUpperCase() + f.slice(1)}</b><br><span class="chip">${ABBR[f]}</span></td>${[0, 1, 2].map(i => cell(ranked[i])).join('')}<td>${gap != null ? '+' + fr(gap, 2) : '–'}</td></tr>`;
      }).join('') + '</tbody>';
  }
  render();

  // Fiche du modèle
  const T = MODEL.trained_on;
  document.getElementById('model-sub').textContent = `${MODEL.deployed}. Appris sur ${T.rows.toLocaleString('fr-FR')} participations de ${T.clubs} clubs et ${T.matches} matchs (joueurs ayant joué au moins ${T.min_minutes} minutes).`;
  document.getElementById('model-metrics').innerHTML = `<thead><tr><th class="l">Méthode</th><th>Erreur moy.</th><th>R²</th><th>Corrélation</th><th>Gain top-11</th></tr></thead><tbody>` +
    MODEL.metrics.map(m => `<tr class="${m.model.startsWith('Ridge') ? 'best' : ''}"><td class="l">${esc(m.model)}${m.model.startsWith('Ridge') ? ' · retenu' : ''}</td><td>${fr(m.mae, 3)}</td><td>${fr(m.r2, 2)}</td><td>${m.spearman == null ? '–' : fr(m.spearman, 2)}</td><td>${m.gain_top11 >= 0 ? '+' : ''}${fr(m.gain_top11, 3)}</td></tr>`).join('') +
    `<tr><td class="l muted">Titulaires réellement alignés (repère)</td><td></td><td></td><td></td><td class="muted">+${fr(MODEL.coach_gain_top11, 3)}</td></tr></tbody>`;
  const imp = Object.entries(MODEL.importance).slice(0, 10), mx = Math.max(...imp.map(i => i[1]));
  document.getElementById('model-imp').innerHTML = imp.map(([k, v]) => `<div class="r"><span>${LABEL[k] || k}</span><span class="bar" style="width:${(100 * v / mx).toFixed(0)}%"></span><span>${fr(100 * v / imp.reduce((a, b) => a + b[1], 0), 0)} %</span></div>`).join('');
  document.getElementById('model-limits').innerHTML = `
    <p><b>Ce que le modèle apprend.</b> À prédire la note de match d'un joueur à un poste donné, à partir uniquement de ce qui était connu avant le match : notes passées (globales, au poste, forme récente), statistiques par 90 minutes (xG, passes clés, interceptions, récupérations, passes progressives, occasions créées, buts et passes décisives), taux de réussite (passes, duels, duels aériens, tacles, arrêts), habitudes de poste et de titularisation tirées des feuilles de match passées, charge des 3 derniers matchs, discipline, dernier score « Rôle et apport » disponible, forces des deux équipes et lieu. Le poids de chaque statistique varie selon le poste.</p>
    <p><b>Validation.</b> ${T.split}, sans qu'aucune information postérieure à un match ne serve à le prédire. Le modèle retenu est ensuite réentraîné sur toute la saison.</p>
    <p><b>Notes de match recalibrées pour la démonstration.</b> Dans les données démo d'origine, la note d'un joueur ne dépendait d'aucun niveau propre : le passé ne prédisait rien (R² 0,04). Les notes ont été recalibrées en conservant les actions du match (buts, passes décisives, cartons, précision des passes) et en ajoutant un niveau stable par joueur et par poste, une pénalité hors poste et l'effet du résultat. Ces niveaux sont fictifs : sur de vraies données, il suffit de réentraîner le modèle.</p>
    <p><b>Non pris en compte.</b> Blessures et état de forme médical, composition probable de l'adversaire, consignes tactiques.</p>`;
})();

// ---------- Dictionnaire ----------
(function dict() {
  const rows = [
    ['Classement, points, bilan V/N/D', 'Calculé', 'V = 3 pts, N = 1 pt ; tri points, puis différence, puis buts pour', 'matches (scores)'],
    ['Points par match', 'Calculé', 'points ÷ matchs joués', 'matches'],
    ['Buts pour / contre, différence', 'Collecté', 'somme des scores', 'matches'],
    ['xG pour / contre', 'Collecté', 'somme des expected_goals de l\'équipe et de l\'adversaire', 'match_team_stats'],
    ['Finition', 'Calculé', 'buts marqués − xG', 'matches, match_team_stats'],
    ['Matchs sans encaisser', 'Calculé', 'nombre de matchs avec 0 but contre', 'matches'],
    ['Trajectoire, médiane', 'Calculé', 'points cumulés par journée ; médiane des 16 clubs', 'matches'],
    ['Forme, séries, domicile / extérieur', 'Calculé', 'résultats des 5 derniers matchs ; série la plus longue sans défaite', 'matches'],
    ['Tirs, tirs concédés, possession', 'Collecté', 'valeur par match', 'match_team_stats'],
    ['Tirs cadrés %', 'Calculé', 'tirs cadrés ÷ tirs', 'match_team_stats'],
    ['Conversion des tirs', 'Calculé', 'buts ÷ tirs', 'matches, match_team_stats'],
    ['Précision des passes', 'Calculé', 'passes réussies ÷ passes tentées (somme des joueurs)', 'player_match_detailed_stats'],
    ['Duels gagnés %', 'Calculé', '(duels au sol + aériens gagnés) ÷ duels disputés', 'player_match_detailed_stats'],
    ['Passes progressives, occasions créées, récupérations', 'Collecté', 'somme des joueurs, par match', 'player_match_detailed_stats'],
    ['Distance parcourue', 'Calculé', 'somme des distances des joueurs, par match', 'player_match_detailed_stats'],
    ['Cartons par match', 'Calculé', 'jaunes + 2 × rouges', 'match_team_stats'],
    ['Écart 5 derniers', 'Calculé', 'moyenne des 5 derniers matchs − moyenne de la saison ; « stable » sous 3 %', 'toutes sources ci-dessus'],
    ['Minutes, % des minutes possibles', 'Calculé', 'somme (sortie − entrée) ; ÷ (matchs × 90)', 'match_participations'],
    ['Buts + passes décisives par 90 min', 'Calculé', '90 × (buts + passes décisives) ÷ minutes', 'match_events, match_participations'],
    ['Note moyenne', 'Collecté', 'moyenne des notes de match', 'player_match_detailed_stats'],
    ['Score « Rôle et apport » et fiabilité', 'Calculé', 'moteur de calcul par famille de poste jouée, version de poids n° 1 (brouillon)', 'player_role_evaluations'],
    ['Pronostic du prochain match', 'Calculé', 'buts attendus = xG moyen × attaque équipe × défense adverse × facteur du lieu (saison 70 %, 5 derniers 30 %) ; probabilités de Poisson', 'match_team_stats, matches'],
    ['Profil de l\'adversaire', 'Calculé', 'écart à la moyenne du championnat : xG produits, xG concédés, possession, duels aériens', 'match_team_stats, player_match_detailed_stats'],
    ['Note prédite (modèle appris)', 'Calculé', 'régression ridge sur 39 variables + interactions poste × statistiques, apprise sur les 16 clubs ; validation temporelle J4–J22 / J23–J30', 'match_participations, player_match_detailed_stats, match_events, match_team_stats, player_role_evaluations'],
    ['Onze optimal', 'Calculé', 'affectation joueur-poste maximisant la somme des notes prédites (algorithme hongrois) ; suspendus exclus', 'note prédite, match_events (cartons)'],
    ['Grille des postes', 'Calculé', 'trois meilleures notes prédites par poste parmi les joueurs disponibles ; principale raison du classement', 'note prédite'],
    ['Points d\'attention', 'Calculé', 'règles : finition à ±2 buts des xG, xG contre récent > +15 %, ≥ 5 jaunes ou 1 rouge, ≥ 80 % des minutes, buteur ≥ 30 % des buts, écart dom./ext. ≥ 0,5 pt', 'indicateurs ci-dessus'],
  ];
  document.getElementById('dict').innerHTML = `<thead><tr><th class="l">Indicateur</th><th class="l">Nature</th><th class="l">Calcul</th><th class="l">Source</th></tr></thead><tbody>${rows.map(r => `<tr><td><b>${r[0]}</b></td><td><span class="chip">${r[1]}</span></td><td>${esc(r[2])}</td><td>${r[3]}</td></tr>`).join('')}</tbody>`;
})();
@endverbatim
</script>
@endpush
@endif
