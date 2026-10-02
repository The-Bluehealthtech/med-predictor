{{-- Évaluation joueur « Rôle et apport » dans le cockpit : état du club en langage clair,
     parcours de mise à jour en 3 étapes (réservé aux comptes autorisés) et aide à la lecture. --}}
@php
    $re = app(\App\Services\CoachCockpit\RoleEvaluationPanel::class)->forClub($clubId ? (int) $clubId : null, auth()->user());
    $c = $re['club'];
    $states = [
        'locked' => ['Calcul impossible pour l\'instant', 'Aucune grille de pondération n\'est publiée : il faut en publier une avant de calculer les scores.', 're-warn'],
        'no_data' => ['Pas encore de données', 'Aucune donnée de performance pour cette équipe : ni statistiques de match, ni export de saison importé.', 're-warn'],
        'to_compute' => ['Scores à calculer', 'Les données de performance sont là, mais aucun joueur de l\'équipe n\'a encore de score.', 're-warn'],
        'partial' => ['Scores partiels', 'Certains joueurs ayant joué n\'ont pas encore de score : relancez le calcul après les derniers matchs.', 're-info'],
        'up_to_date' => ['Scores à jour', 'Tous les joueurs ayant joué ont un score « Rôle et apport ».', 're-ok'],
    ];
    [$stateTitle, $stateText, $stateClass] = $states[$re['state']];
    $grid = $re['published']->first();
    $stepClass = fn (bool $done) => $done ? 're-step re-done' : 're-step';
    $hasData = $c['participations'] > 0 || $c['period_profiles'] > 0;
@endphp
<style>
    .cc .re-intro { color: var(--ink-2); font-size: .92rem; max-width: 70ch; margin: 2px 0 16px; }
    .cc .re-status { display: grid; grid-template-columns: minmax(0, 1fr) minmax(220px, 320px); gap: 16px; align-items: center; padding: 14px 16px; border-radius: var(--radius); border: 1px solid var(--line); margin-bottom: 18px; }
    .cc .re-status h3 { margin: 0 0 2px; font-size: 1rem; }
    .cc .re-status p { margin: 0; font-size: .86rem; color: var(--ink-2); }
    .cc .re-ok { background: #eef7f1; border-color: #bfe0cb; } .cc .re-info { background: #f3f6fb; border-color: #cfdcef; } .cc .re-warn { background: #fff8e8; border-color: #ecd291; }
    .cc .re-bar { height: 10px; border-radius: 999px; background: #e6e9e6; overflow: hidden; } .cc .re-bar > span { display: block; height: 100%; background: var(--accent); }
    .cc .re-cov { font-size: .82rem; color: var(--ink-2); margin-top: 6px; }
    .cc .re-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; counter-reset: step; }
    .cc .re-step { border: 1px solid var(--line); border-radius: var(--radius); padding: 14px; display: grid; gap: 8px; align-content: start; background: var(--surface); }
    .cc .re-step h4 { margin: 0; font-size: .95rem; display: flex; align-items: center; gap: 8px; }
    .cc .re-step h4::before { counter-increment: step; content: counter(step); display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 999px; background: var(--line); color: var(--ink); font-size: .8rem; font-weight: 700; }
    .cc .re-done h4::before { content: '✓'; background: var(--accent); color: var(--surface); }
    .cc .re-step p { margin: 0; font-size: .84rem; color: var(--ink-2); }
    .cc .re-step .re-state { font-size: .8rem; font-weight: 600; }
    .cc .re-btn { display: inline-flex; justify-content: center; align-items: center; padding: 8px 14px; border-radius: 8px; font-size: .86rem; font-weight: 600; background: var(--accent); color: var(--surface); border: 0; cursor: pointer; text-decoration: none; }
    .cc .re-btn.secondary { background: var(--surface); color: var(--ink); border: 1px solid var(--line); }
    .cc .re-btn[disabled] { opacity: .5; cursor: not-allowed; }
    .cc .re-details { border-top: 1px solid var(--line); padding-top: 8px; }
    .cc .re-details summary { cursor: pointer; font-size: .84rem; font-weight: 600; color: var(--accent); }
    .cc .re-details form { display: grid; gap: 10px; margin-top: 10px; }
    .cc .re-help { margin-top: 16px; } .cc .re-help summary { cursor: pointer; font-weight: 600; font-size: .9rem; }
    .cc .re-help dl { display: grid; grid-template-columns: minmax(120px, 180px) minmax(0, 1fr); gap: 6px 14px; margin: 10px 0 0; font-size: .86rem; } .cc .re-help dt { font-weight: 600; } .cc .re-help dd { margin: 0; color: var(--ink-2); }
    @media (max-width: 860px) { .cc .re-status, .cc .re-steps { grid-template-columns: minmax(0, 1fr); } .cc .re-help dl { grid-template-columns: minmax(0, 1fr); } }
</style>

<section class="panel" aria-labelledby="h-role-eval">
    <div class="section-head" style="margin-bottom:4px">
        <h2 id="h-role-eval">Évaluation joueur — rôle et apport</h2>
        @if($re['canManage'])<a href="{{ route('modules.coach-cockpit.role-evaluation.settings') }}" class="note">Paramétrage avancé →</a>@endif
    </div>
    <p class="re-intro">Le score <b>« Rôle et apport »</b> (sur 100) mesure ce que chaque joueur apporte au poste qu'il occupe réellement, comparé aux joueurs du même poste. Il s'accompagne d'une <b>fiabilité</b> : plus le joueur a joué, plus le score est sûr. Les scores apparaissent dans le tableau de l'effectif ci-dessus.</p>

    @if(session('success'))<div role="status" class="re-status re-ok" style="grid-template-columns:1fr"><p>{{ session('success') }}</p></div>@endif
    @if(session('error'))<div role="alert" class="re-status re-warn" style="grid-template-columns:1fr"><p>{{ session('error') }}</p></div>@endif

    <div class="re-status {{ $stateClass }}" data-role-eval-state="{{ $re['state'] }}">
        <div>
            <h3>{{ $stateTitle }}</h3>
            <p>{{ $stateText }}</p>
        </div>
        <div>
            <div class="re-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $re['coverage'] }}" aria-label="Joueurs évalués"><span style="width: {{ $re['coverage'] }}%"></span></div>
            <div class="re-cov"><b>{{ $c['players_evaluated'] }}</b> joueur(s) évalué(s) sur <b>{{ $c['players_played'] }}</b> ayant joué ({{ $re['coverage'] }} %)@if($c['last_computed_at']) · mis à jour le {{ $c['last_computed_at']->format('d/m/Y') }}@endif</div>
        </div>
    </div>

    @if($re['canManage'])
        <h3 style="font-size:.95rem;margin:0 0 10px">Mettre à jour les scores</h3>
        <div class="re-steps">
            <div class="{{ $stepClass((bool) $grid) }}">
                <h4>Grille de pondération</h4>
                <p>La grille fixe le poids de chaque statistique selon le poste. Elle doit être publiée pour calculer.</p>
                @if($grid)
                    <span class="re-state" style="color:var(--accent)">Publiée : {{ $grid->label }}@if($grid->published_at) ({{ \Illuminate\Support\Carbon::parse($grid->published_at)->format('d/m/Y') }})@endif</span>
                @else
                    <span class="re-state" style="color:#8a5a00">Aucune grille publiée{{ $re['drafts']->isNotEmpty() ? ' — ' . $re['drafts']->count() . ' brouillon(s) en attente' : '' }}</span>
                @endif
                <a href="{{ route('modules.coach-cockpit.role-evaluation.settings') }}" class="re-btn secondary">{{ $grid ? 'Voir ou modifier la grille' : 'Publier une grille' }}</a>
            </div>

            <div class="{{ $stepClass($hasData) }}">
                <h4>Données de performance</h4>
                <p>Les scores reposent sur les statistiques match par match ou, à défaut, sur les moyennes de saison des exports « Player statistics ».</p>
                <span class="re-state">{{ number_format($c['period_profiles'], 0, ',', ' ') }} profil(s) de saison importé(s) · {{ number_format($c['participations'], 0, ',', ' ') }} participations · {{ number_format($c['detailed_stats'], 0, ',', ' ') }} statistiques détaillées</span>
                <a href="{{ route('player-stats-import.create', ['club_id' => $clubId]) }}" class="re-btn">Importer un export de statistiques joueurs (Excel)</a>
                <details class="re-details">
                    <summary>Import avancé : CSV avec fichier de correspondance</summary>
                    <form method="post" enctype="multipart/form-data" action="{{ route('modules.coach-cockpit.role-evaluation.import') }}">
                        @csrf
                        <label class="field">Que contient le fichier ?
                            <select name="type" required>
                                <option value="participations">Les joueurs alignés par match (participations)</option>
                                <option value="player-match-stats">Les statistiques de chaque joueur par match</option>
                                <option value="team-stats">Les statistiques de chaque équipe par match</option>
                                <option value="events">Les événements de match (buts, cartons…)</option>
                            </select>
                        </label>
                        <label class="field">D'où viennent les données ? <span class="muted">(facultatif)</span>
                            <input type="text" name="source" placeholder="Ex. fournisseur de données, club, fédération">
                        </label>
                        <label class="field">Fichier de données (CSV)
                            <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required>
                        </label>
                        <label class="field">Fichier de correspondance des colonnes (JSON)
                            <input type="file" name="mapping_file" accept=".json,application/json,text/plain" required>
                            <span class="note">Il indique à quelle donnée FIT correspond chaque colonne du CSV.</span>
                        </label>
                        <p class="note">Tout le fichier est vérifié avant l'enregistrement : si une seule ligne est invalide, rien n'est importé et l'erreur est signalée.</p>
                        <button type="submit" class="re-btn">Vérifier et importer</button>
                    </form>
                </details>
            </div>

            <div class="{{ $stepClass($re['state'] === 'up_to_date') }}">
                <h4>Calcul des scores</h4>
                <p>Recalcule les scores des joueurs de cette équipe avec la grille publiée. À relancer après chaque journée.</p>
                <form method="post" action="{{ route('modules.coach-cockpit.role-evaluation.compute') }}" style="display:grid;gap:8px">
                    @csrf
                    <input type="hidden" name="club_id" value="{{ $clubId }}">
                    @if($re['published']->count() > 1)
                        <label class="field">Grille utilisée
                            <select name="config_version">@foreach($re['published'] as $config)<option value="{{ $config->id }}">{{ $config->label }}</option>@endforeach</select>
                        </label>
                    @elseif($grid)
                        <input type="hidden" name="config_version" value="{{ $grid->id }}">
                    @endif
                    <button type="submit" class="re-btn" @disabled(!$grid || !$hasData)>Calculer les scores de l'équipe</button>
                    @if(!$grid)<span class="note">Publiez d'abord une grille (étape 1).</span>@elseif(!$hasData)<span class="note">Importez d'abord des données de performance (étape 2).</span>@endif
                </form>
            </div>
        </div>
    @else
        <p class="note">La mise à jour des scores est réservée aux comptes autorisés à saisir des métriques de performance.</p>
    @endif

    <details class="re-help">
        <summary>Comment lire le score ?</summary>
        <dl>
            <dt>Score sur 100</dt><dd>Apport du joueur au poste joué, comparé aux joueurs du même poste : {{ config('role_evaluation_engine.score_center', 50) }} correspond au niveau de référence du poste, et {{ config('role_evaluation_engine.score_scale', 15) }} points d'écart représentent environ un écart type.</dd>
            <dt>Fiabilité</dt><dd>Confiance dans le score (en %) : elle augmente avec le nombre de minutes jouées à ce poste. En dessous de {{ config('role_evaluation_engine.minimum_reliability_display', 30) }} %, le score n'est pas affiché.</dd>
            <dt>Famille de poste</dt><dd>Le poste réellement occupé en match (gardien, défenseur central, latéral…), pas le poste théorique de la fiche joueur.@if(!empty($c['families'])) Dans cette équipe : {{ collect($c['families'])->map(fn ($n, $f) => $f . ' (' . $n . ')')->implode(', ') }}.@endif</dd>
            <dt>Grille de pondération</dt><dd>Le poids donné à chaque statistique selon le poste ; elle est versionnée et publiée par un responsable.</dd>
        </dl>
    </details>
</section>
