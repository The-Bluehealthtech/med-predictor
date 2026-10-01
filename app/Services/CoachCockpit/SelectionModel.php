<?php

namespace App\Services\CoachCockpit;

use RuntimeException;

/**
 * Modèle de sélection du cockpit entraîneur : prédit la note de match d'un
 * joueur à un poste donné. Les coefficients sont appris hors ligne par
 * scripts/coach-cockpit/train_selection_model.py et versionnés dans
 * resources/data/coach-selection-model.json.
 *
 * Cette classe recalcule les caractéristiques « à date » des joueurs avec
 * EXACTEMENT les mêmes définitions que le script d'apprentissage
 * (player_features, team_to_date) : toute modification de l'un doit être
 * reportée dans l'autre, sinon les prédictions perdent leur sens.
 *
 * La prédiction elle-même (produit scalaire coefficients × caractéristiques,
 * affectation optimale des postes) est faite dans la vue, pour que le choix de
 * l'adversaire et du lieu se recalcule instantanément.
 */
final class SelectionModel
{
    private const PRIORS_PER90 = ['xg' => 0.1, 'kp' => 0.8, 'itc' => 1.0, 'rec' => 3.0, 'pp' => 2.0, 'cc' => 0.8];

    private const RATES = [
        'duel_pct' => ['dw', 'dt', 0.5],
        'aerial_pct' => ['aw', 'at', 0.5],
        'pass_pct' => ['pc', 'pt', 0.78],
        'tackle_pct' => ['tw', 'tt', 0.55],
        'save_pct' => ['sv', 'sf', 0.68],
    ];

    private array $model;

    public function __construct(?string $path = null)
    {
        $path ??= resource_path('data/coach-selection-model.json');
        $json = is_file($path) ? json_decode((string) file_get_contents($path), true) : null;
        if (!is_array($json) || !isset($json['coef'], $json['fams'], $json['params']['fam_mean'])) {
            throw new RuntimeException("Modèle de sélection introuvable ou invalide : {$path}");
        }
        $this->model = $json;
    }

    /**
     * @param  array<int, array<int, array>>  $playerRows  [player_id => lignes de participation (clés du script d'apprentissage)]
     * @param  array<int, array>  $teamMatches  lignes [team_id, matchday, gf, ga, xg, xga] de toute la compétition
     * @param  array<int, array>  $roleHistory  lignes [player_id, md, score, reliability]
     * @param  array<int, int>  $teamToClub  [team_id => club_id]
     */
    public function payload(array $playerRows, array $teamMatches, array $roleHistory, int $ownTeamId, array $teamToClub): array
    {
        $nextMatchday = 1 + (int) max(array_merge([0], array_column($teamMatches, 'matchday')));

        $players = [];
        foreach ($playerRows as $playerId => $rows) {
            usort($rows, fn ($a, $b) => $a['matchday'] <=> $b['matchday']);
            $role = $this->latestRole($roleHistory, (int) $playerId, $nextMatchday);
            foreach ($this->model['fams'] as $family) {
                $players[$playerId][$family] = $this->playerFeatures($rows, $family, $role);
            }
        }

        $opponents = [];
        foreach (array_unique(array_column($teamMatches, 'team_id')) as $teamId) {
            [$att, $def, $ppg] = $this->teamToDate($teamMatches, (int) $teamId, $nextMatchday);
            $opponents[$teamToClub[$teamId] ?? $teamId] = ['att' => round($att, 5), 'def' => round($def, 5), 'ppg' => round($ppg, 5)];
        }
        [$ownAtt, $ownDef, $ownPpg] = $this->teamToDate($teamMatches, $ownTeamId, $nextMatchday);

        return [
            'coef' => $this->model['coef'],
            'intercept' => $this->model['intercept'],
            'feats' => $this->model['feats'],
            'inter' => $this->model['inter'],
            'fams' => $this->model['fams'],
            'metrics' => $this->model['metrics'],
            'importance' => $this->model['importance'],
            'trained_on' => $this->model['trained_on'],
            'deployed' => $this->model['deployed'],
            'coach_gain_top11' => $this->model['coach_gain_top11'],
            'players' => $players,
            'opponents' => $opponents,
            'own' => ['att' => $ownAtt, 'def' => $ownDef, 'ppg' => $ownPpg],
            'next_matchday' => $nextMatchday,
        ];
    }

    private static function shrink(float $total, float $n, float $prior, float $k): float
    {
        return ($total + $k * $prior) / ($n + $k);
    }

    /** Forces d'équipe sur les matchs strictement antérieurs à la journée $t (team_to_date). */
    private function teamToDate(array $teamMatches, int $teamId, int $t): array
    {
        $before = array_filter($teamMatches, fn ($r) => $r['matchday'] < $t);
        $league = $before ?: $teamMatches;
        // Comme pandas : un xG manquant est exclu de la moyenne et compte 0 dans les sommes.
        $known = array_filter(array_column($league, 'xg'), fn ($x) => $x !== null);
        $lx = array_sum($known) / max(1, count($known));
        $own = array_values(array_filter($before, fn ($r) => (int) $r['team_id'] === $teamId));
        $n = count($own);
        if ($n === 0 || $lx <= 0) {
            return [1.0, 1.0, 1.4];
        }
        $pts = array_sum(array_map(fn ($r) => $r['gf'] > $r['ga'] ? 3 : ($r['gf'] == $r['ga'] ? 1 : 0), $own));

        return [
            self::shrink(array_sum(array_column($own, 'xg')), $n, $lx, 3) / $lx,
            self::shrink(array_sum(array_column($own, 'xga')), $n, $lx, 3) / $lx,
            self::shrink($pts, $n, 1.4, 3),
        ];
    }

    private function latestRole(array $roleHistory, int $playerId, int $t): ?array
    {
        $rows = array_filter($roleHistory, fn ($r) => (int) $r['player_id'] === $playerId && $r['md'] !== null && $r['md'] < $t);
        if (!$rows) {
            return null;
        }
        usort($rows, fn ($a, $b) => $a['md'] <=> $b['md']);

        return end($rows);
    }

    /** Caractéristiques d'un joueur pour un poste, à partir de son historique trié par journée (player_features). */
    private function playerFeatures(array $hist, string $family, ?array $role): array
    {
        $near = $this->model['params']['near'][$family] ?? [];
        $played = array_values(array_filter($hist, fn ($r) => $r['mins'] > 0));
        $n = count($played);
        $mins = array_sum(array_column($played, 'mins'));
        $sum = fn (array $rows, string $k) => array_sum(array_map(fn ($r) => (float) ($r[$k] ?? 0), $rows));
        $fmean = (float) $this->model['params']['fam_mean'][$family];

        $f = [];
        $f['n_prior'] = min($n, 30);
        $f['rating_avg'] = self::shrink($sum($played, 'rating'), $n, $fmean, 3);
        $last3 = array_slice($played, -3);
        $last5 = array_slice($played, -5);
        $f['rating_last3'] = self::shrink($sum($last3, 'rating'), count($last3), $f['rating_avg'], 1);
        $f['rating_last5'] = self::shrink($sum($last5, 'rating'), count($last5), $f['rating_avg'], 1);
        $atFamily = array_values(array_filter($played, fn ($r) => $r['family'] === $family));
        $f['rating_fam'] = self::shrink($sum($atFamily, 'rating'), count($atFamily), $f['rating_avg'], 2);
        $f['fam_share'] = $mins ? $sum($atFamily, 'mins') / $mins : 0.0;
        $f['near_share'] = $mins ? $sum(array_filter($played, fn ($r) => in_array($r['family'], $near, true)), 'mins') / $mins : 0.0;
        $f['starter_rate'] = self::shrink($sum($played, 'is_starter'), $n, 0.5, 2);
        if ($hist) {
            $lastMd = max(array_column($hist, 'matchday'));
            $f['mins_last3'] = $sum(array_filter($hist, fn ($r) => $r['matchday'] > $lastMd - 3), 'mins') / 270;
        } else {
            $f['mins_last3'] = 0.0;
        }
        foreach (self::PRIORS_PER90 as $col => $prior) {
            $f[$col . '90'] = self::shrink($sum($played, $col), $mins / 90, $prior, 3);
        }
        $f['ga90'] = self::shrink($sum($played, 'g') + $sum($played, 'a'), $mins / 90, 0.1, 3);
        foreach (self::RATES as $name => [$num, $den, $prior]) {
            $f[$name] = self::shrink($sum($played, $num), $sum($played, $den), $prior, 10);
        }
        $f['cards_pm'] = self::shrink($sum($played, 'yc') + 2 * $sum($played, 'rc'), $n, 0.15, 3);
        $f['role_score'] = $role !== null ? (float) $role['score'] : 47.5;
        $f['role_rel'] = $role !== null ? (float) $role['reliability'] : 0.0;

        return array_map(fn ($v) => round((float) $v, 5), $f);
    }
}
