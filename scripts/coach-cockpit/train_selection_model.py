r"""Modèle de sélection : prédire la note de match d'un joueur à un poste donné,
uniquement à partir des informations disponibles AVANT le match.

Données : participations démo des 16 clubs (feuilles de match effectives :
titularisation, poste joué, minutes), statistiques détaillées par match,
événements (buts, passes décisives, cartons), statistiques d'équipe et
historique des scores « Rôle et apport ».

Validation temporelle : apprentissage J4–J22, test J23–J30.
Sortie : resources/data/coach-selection-model.json — coefficients du modèle
retenu, paramètres des caractéristiques et métriques. Les caractéristiques
des joueurs sont recalculées en PHP (App\Services\CoachCockpit\SelectionModel)
avec exactement les mêmes définitions : toute modification ici doit y être
reportée.

Utilisation :
  1. psql "$DATABASE" -v out=/chemin/dossier -f scripts/coach-cockpit/export_training_data.sql
  2. python3 scripts/coach-cockpit/train_selection_model.py /chemin/dossier resources/data/coach-selection-model.json
"""
import json
import sys

import numpy as np
import pandas as pd
from sklearn.ensemble import HistGradientBoostingRegressor
from sklearn.linear_model import RidgeCV
from sklearn.metrics import mean_absolute_error, r2_score

D = sys.argv[1]
OUT = sys.argv[2]
MIN_MINUTES = 20
FAMS = ['gardien', 'défenseur central', 'latéral', 'milieu défensif', 'milieu relayeur', 'milieu offensif', 'ailier', 'avant-centre']
NEAR = {
    'gardien': [], 'défenseur central': ['latéral', 'milieu défensif'], 'latéral': ['défenseur central', 'ailier'],
    'milieu défensif': ['milieu relayeur', 'défenseur central'], 'milieu relayeur': ['milieu défensif', 'milieu offensif'],
    'milieu offensif': ['milieu relayeur', 'ailier', 'avant-centre'], 'ailier': ['milieu offensif', 'latéral', 'avant-centre'],
    'avant-centre': ['ailier', 'milieu offensif'],
}

pm = pd.read_csv(f'{D}/participations.csv')
pm['home'] = pm['home'].map({'t': 1, 'f': 0})
pm['is_starter'] = pm['is_starter'].map({'t': 1, 'f': 0})
tm = pd.read_csv(f'{D}/team_matches.csv')
rh = pd.read_csv(f'{D}/role_history.csv')
GLOBAL_MEAN = pm['rating'].mean()
FAM_MEAN = pm.groupby('family')['rating'].mean().to_dict()


def shrink(total, n, prior, k):
    return (total + k * prior) / (n + k)


def team_to_date(team_id, t):
    """Forces d'équipe calculées sur les matchs strictement antérieurs à la journée t."""
    h = tm[(tm.team_id == team_id) & (tm.matchday < t)]
    n = len(h)
    lg = tm[tm.matchday < t]
    lx = lg['xg'].mean() if len(lg) else tm['xg'].mean()
    if n == 0:
        return 1.0, 1.0, 1.4
    pts = ((h.gf > h.ga) * 3 + (h.gf == h.ga) * 1).sum()
    return shrink(h['xg'].sum(), n, lx, 3) / lx, shrink(h['xga'].sum(), n, lx, 3) / lx, shrink(pts, n, 1.4, 3)


def player_features(hist, fam, role_row):
    """Caractéristiques d'un joueur pour un poste, à partir de son historique (DataFrame trié par journée)."""
    f = {}
    played = hist[hist.mins > 0]
    n = len(played)
    mins = played['mins'].sum()
    fmean = FAM_MEAN[fam]
    f['n_prior'] = min(n, 30)
    f['rating_avg'] = shrink(played['rating'].sum(), n, fmean, 3)
    last3, last5 = played.tail(3), played.tail(5)
    f['rating_last3'] = shrink(last3['rating'].sum(), len(last3), f['rating_avg'], 1)
    f['rating_last5'] = shrink(last5['rating'].sum(), len(last5), f['rating_avg'], 1)
    at_fam = played[played.family == fam]
    f['rating_fam'] = shrink(at_fam['rating'].sum(), len(at_fam), f['rating_avg'], 2)
    f['fam_share'] = at_fam['mins'].sum() / mins if mins else 0.0
    f['near_share'] = played[played.family.isin(NEAR[fam])]['mins'].sum() / mins if mins else 0.0
    f['starter_rate'] = shrink(played['is_starter'].sum(), n, 0.5, 2)
    if len(hist):
        last_md = hist['matchday'].max()
        f['mins_last3'] = hist[hist.matchday > last_md - 3]['mins'].sum() / 270
    else:
        f['mins_last3'] = 0.0
    for col, prior in [('xg', 0.1), ('kp', 0.8), ('itc', 1.0), ('rec', 3.0), ('pp', 2.0), ('cc', 0.8)]:
        f[f'{col}90'] = shrink(played[col].sum(), mins / 90, prior, 3)
    f['ga90'] = shrink(played['g'].sum() + played['a'].sum(), mins / 90, 0.1, 3)
    rate = lambda num, den, prior: shrink(played[num].sum(), played[den].sum(), prior, 10)
    f['duel_pct'] = rate('dw', 'dt', 0.5)
    f['aerial_pct'] = rate('aw', 'at', 0.5)
    f['pass_pct'] = rate('pc', 'pt', 0.78)
    f['tackle_pct'] = rate('tw', 'tt', 0.55)
    f['save_pct'] = rate('sv', 'sf', 0.68)
    f['cards_pm'] = shrink(played['yc'].sum() + 2 * played['rc'].sum(), n, 0.15, 3)
    if role_row is not None:
        f['role_score'] = role_row['score']
        f['role_rel'] = role_row['reliability']
    else:
        f['role_score'] = 47.5
        f['role_rel'] = 0.0
    return f


def latest_role(player_id, t):
    r = rh[(rh.player_id == player_id) & (rh.md < t)]
    return None if r.empty else r.sort_values('md').iloc[-1]


# ---------- Construction du jeu d'apprentissage ----------
rows = []
by_player = {pid: g.sort_values('matchday') for pid, g in pm.groupby('player_id')}
team_cache = {}
for r in pm.itertuples(index=False):
    if r.matchday < 4 or r.mins < MIN_MINUTES or pd.isna(r.rating):
        continue
    hist = by_player[r.player_id]
    hist = hist[hist.matchday < r.matchday]
    if hist[hist.mins > 0].shape[0] < 2:
        continue
    feats = player_features(hist, r.family, latest_role(r.player_id, r.matchday))
    for key, team_id in (('opp', r.opp_id), ('own', r.team_id)):
        ck = (team_id, r.matchday)
        if ck not in team_cache:
            team_cache[ck] = team_to_date(team_id, r.matchday)
        a, d, p = team_cache[ck]
        feats[f'{key}_att'], feats[f'{key}_def'], feats[f'{key}_ppg'] = a, d, p
    feats['home'] = r.home
    for fam in FAMS:
        feats['pos_' + fam] = 1.0 if r.family == fam else 0.0
    feats['_y'] = r.rating
    feats['_md'] = r.matchday
    feats['_team'] = r.team_id
    feats['_match'] = r.match_id
    feats['_player'] = r.player_id
    feats['_starter'] = r.is_starter
    rows.append(feats)

df = pd.DataFrame(rows)
FEATS = [c for c in df.columns if not c.startswith('_')]
# Interactions poste × profil : le même indicateur ne pèse pas pareil selon le poste
INTER = ['xg90', 'kp90', 'itc90', 'duel_pct', 'aerial_pct', 'pass_pct', 'save_pct', 'cc90', 'rec90']
for fam in FAMS:
    for col in INTER:
        df[f'{col}__x__{fam}'] = df[col] * df['pos_' + fam]
FEATS_LIN = FEATS + [f'{c}__x__{f}' for f in FAMS for c in INTER]

train, test = df[df._md <= 22], df[df._md >= 23]
print(f'lignes apprentissage: {len(train)} | test: {len(test)} | caractéristiques: {len(FEATS)} (+{len(FEATS_LIN) - len(FEATS)} interactions)')

results = {}
y_tr, y_te = train._y.values, test._y.values
# Références
results['Moyenne globale'] = np.full(len(test), y_tr.mean())
results['Moyenne du joueur (avant match)'] = test['rating_avg'].values
results['Forme 5 derniers'] = test['rating_last5'].values
# Ridge (standardisé)
mu, sd = train[FEATS_LIN].mean(), train[FEATS_LIN].std().replace(0, 1)
ridge = RidgeCV(alphas=np.logspace(-1, 3, 30)).fit((train[FEATS_LIN] - mu) / sd, y_tr)
results['Ridge (linéaire + interactions poste)'] = ridge.predict((test[FEATS_LIN] - mu) / sd)
# Gradient boosting
gbm = HistGradientBoostingRegressor(max_iter=300, learning_rate=0.05, max_leaf_nodes=15, min_samples_leaf=40, l2_regularization=1.0, random_state=0)
gbm.fit(train[FEATS], y_tr)
results['Gradient boosting'] = gbm.predict(test[FEATS])


def selection_quality(pred):
    """Pour chaque équipe-match du test : note réelle moyenne des 11 joueurs les mieux classés par le modèle
    parmi ceux qui ont joué, comparée à la moyenne de tous les participants (le hasard)."""
    t = test.assign(_p=pred)
    gains, rhos = [], []
    for _, g in t.groupby(['_match', '_team']):
        if len(g) < 12:
            continue
        top = g.nlargest(11, '_p')['_y'].mean()
        gains.append(top - g['_y'].mean())
        rhos.append(pd.Series(g['_p'].values).corr(pd.Series(g['_y'].values), method='spearman'))
    return float(np.mean(gains)), float(np.nanmean(rhos))


metrics = []
for name, pred in results.items():
    mae = mean_absolute_error(y_te, pred)
    r2 = r2_score(y_te, pred)
    gain, rho = selection_quality(pred)
    metrics.append({'model': name, 'mae': round(mae, 4), 'r2': round(r2, 4), 'gain_top11': round(gain, 4), 'spearman': None if np.isnan(rho) else round(rho, 4)})
    print(f'{name:42s} MAE {mae:.3f} | R² {r2:+.3f} | gain top-11 {gain:+.3f} | Spearman intra-match {rho:+.3f}')
# Choix réel de l'entraîneur (titulaires) comme repère de sélection
coach = []
for _, g in test.groupby(['_match', '_team']):
    if len(g) >= 12 and g['_starter'].sum() >= 8:
        coach.append(g[g._starter == 1]['_y'].mean() - g['_y'].mean())
print(f'{"Titulaires réellement alignés (repère)":42s} gain top-11 {np.mean(coach):+.3f}')

# ---------- Modèle final : réentraîné sur J4–J30 ----------
best = min((m for m in metrics if m['model'].startswith(('Ridge', 'Gradient'))), key=lambda m: m['mae'])
print('modèle le plus précis sur le test :', best['model'])
mu_all, sd_all = df[FEATS_LIN].mean(), df[FEATS_LIN].std().replace(0, 1)
final = RidgeCV(alphas=np.logspace(-1, 3, 30)).fit((df[FEATS_LIN] - mu_all) / sd_all, df._y.values)
coef = dict(zip(FEATS_LIN, final.coef_ / sd_all.values))
intercept = float(final.intercept_ - sum(coef[c] * mu_all[c] for c in FEATS_LIN))
# Importance : |coef standardisé| agrégé par caractéristique de base
imp = {}
for c, v in zip(FEATS_LIN, final.coef_):
    base = c.split('__x__')[0]
    imp[base] = imp.get(base, 0) + abs(v)
imp = dict(sorted(imp.items(), key=lambda kv: -kv[1]))

out = {
    'trained_on': {'rows': int(len(df)), 'train_rows': int(len(train)), 'test_rows': int(len(test)), 'clubs': int(pm.team_id.nunique()),
                   'matches': int(pm.match_id.nunique()), 'split': 'apprentissage J4–J22, test J23–J30', 'min_minutes': MIN_MINUTES},
    'metrics': metrics, 'coach_gain_top11': round(float(np.mean(coach)), 4), 'selected_for_test': best['model'],
    'deployed': 'Ridge (linéaire + interactions poste), réentraîné J4–J30', 'alpha': float(final.alpha_),
    'feats': FEATS, 'inter': INTER, 'fams': FAMS, 'intercept': intercept, 'coef': {k: float(v) for k, v in coef.items()},
    'importance': {k: round(float(v), 4) for k, v in list(imp.items())[:12]},
    'params': {'fam_mean': {k: round(float(v), 5) for k, v in FAM_MEAN.items()}, 'near': NEAR, 'min_minutes': MIN_MINUTES},
}
json.dump(out, open(OUT, 'w'), ensure_ascii=False, indent=1, allow_nan=False)
print('importance (top 8):', list(imp.items())[:8])
print('modèle exporté :', OUT)
