<?php

namespace App\Services;

/**
 * Calcul pur et déterministe. Entrée canonique : [id, position, matches =>
 * [date, match_id, position, minutes, stats], ou average =>
 * [matches, minutes, stats (moyennes par match)]]. Aucune donnée nominative.
 */
final class PerformanceScoreCalculator
{
    public function __construct(private readonly array $cfg)
    {
    }

    public function calculate(array $players): array
    {
        usort($players, fn ($a, $b) => ($a['id'] ?? 0) <=> ($b['id'] ?? 0));
        $prepared = [];
        foreach ($players as $player) {
            $prepared[$player['id']] = $this->prepare($player);
        }
        $references = $this->references($prepared);
        $raw = [];
        foreach ($prepared as $id => $player) {
            $raw[$id] = $this->scorePlayer($player, $references);
        }
        $variances = $this->varianceModels($raw);
        $out = [];
        foreach ($raw as $id => $row) {
            $out[] = $this->finalize($row, $variances);
        }

        return $out;
    }

    /** Diagnostic de validation : corrélation entre moyennes des matchs pairs et impairs. */
    public function splitHalfCorrelation(array $players): ?float
    {
        $prepared = [];
        foreach ($players as $player) $prepared[$player['id']] = $this->prepare($player);
        $refs = $this->references($prepared);
        $pairs = [];
        foreach ($prepared as $player) {
            if ($player['mode'] !== 'précis' || count($player['entries']) < 2) continue;
            $score = $this->scorePlayer($player, $refs);
            $scores = $score['scores_match'] ?? [];
            if (count($scores) < 2) continue;
            $odd = $even = [];
            foreach ($scores as $i => $item) {
                if ($i % 2) $odd[] = $item;
                else $even[] = $item;
            }
            $pairs[] = [$this->weighted($odd), $this->weighted($even)];
        }
        if (count($pairs) < 3) return null;
        $x = array_column($pairs, 0);
        $y = array_column($pairs, 1);
        $mx = array_sum($x) / count($x);
        $my = array_sum($y) / count($y);
        $cross = $vx = $vy = 0;
        foreach ($pairs as [$a, $b]) {
            $cross += ($a - $mx) * ($b - $my);
            $vx += ($a - $mx) ** 2;
            $vy += ($b - $my) ** 2;
        }
        return $vx > 0 && $vy > 0 ? $cross / sqrt($vx * $vy) : null;
    }

    private function number(mixed $value): ?float
    {
        if ($value === null || $value === '' || $value === 'Données non disponibles' || !is_numeric($value)) {
            return null;
        }
        $number = (float) $value;
        return is_finite($number) ? $number : null;
    }

    private function family(?string $position): ?string
    {
        return $this->cfg['positions'][$position ?? ''] ?? null;
    }

    private function prepare(array $player): array
    {
        $matches = $player['matches'] ?? [];
        $mode = count($matches) ? 'précis' : 'approximé';
        $entries = [];
        if ($mode === 'précis') {
            foreach ($matches as $match) {
                $observedMinutes = $this->number($match['minutes'] ?? null);
                $entries[] = [
                    'match_id' => $match['match_id'] ?? 0,
                    'date' => $match['date'] ?? '',
                    'family' => $this->family($match['position'] ?? $player['position'] ?? null),
                    'minutes' => $observedMinutes === null ? null : max(0, $observedMinutes),
                    'stats' => $match['stats'] ?? [],
                ];
            }
            usort($entries, fn ($a, $b) => [$a['date'], $a['match_id']] <=> [$b['date'], $b['match_id']]);
        } elseif (isset($player['average'])) {
            $avg = $player['average'];
            $observedMinutes = $this->number($avg['minutes'] ?? null);
            $observedMatches = $this->number($avg['matches'] ?? null);
            $entries[] = [
                'family' => $this->family($player['position'] ?? null),
                'minutes' => $observedMinutes === null ? null : max(0, $observedMinutes),
                'matches' => $observedMatches === null ? null : max(0, (int) $observedMatches),
                'stats' => $avg['stats'] ?? [],
            ];
        }
        $familyMinutes = [];
        foreach ($entries as $entry) {
            if ($entry['family'] !== null) {
                $familyMinutes[$entry['family']] = ($familyMinutes[$entry['family']] ?? 0) + $entry['minutes'];
            }
        }
        arsort($familyMinutes, SORT_NUMERIC);
        $family = array_key_first($familyMinutes) ?? $this->family($player['position'] ?? null);
        $knownMinutes = array_filter(array_column($entries, 'minutes'), fn ($value) => $value !== null);
        $minutes = count($knownMinutes) ? array_sum($knownMinutes) : null;
        $count = $mode === 'précis' ? count($entries) : ($entries[0]['matches'] ?? null);
        $weights = [];
        foreach ($entries as $i => $entry) {
            $age = $mode === 'précis' ? count($entries) - 1 - $i : 0;
            $perMatchMinutes = $mode === 'précis' ? ($entry['minutes'] ?? 0) : ($count ? ($entry['minutes'] ?? 0) / $count : 0);
            $weights[] = pow(0.5, $age / $this->cfg['half_life_matches'])
                * min(1, $perMatchMinutes / $this->cfg['minute_weight_denominator']);
        }
        return compact('family', 'minutes', 'count', 'mode', 'entries', 'weights') + ['id' => $player['id']];
    }

    private function indicator(array $entry, string $name, string $mode, float $prior = 0, ?float $familyRate = null): ?float
    {
        $spec = $this->cfg['indicators'][$name];
        $stats = $entry['stats'];
        $matches = $mode === 'précis' ? 1 : ($entry['matches'] ?? 0);
        $minutes = $entry['minutes'];
        if ($minutes <= 0 || $matches <= 0) {
            return null;
        }
        if ($spec['type'] === 'count') {
            $value = $this->number($stats[$mode === 'précis' ? $spec['field'] : $spec['average']] ?? null);
            return $value === null || $value < 0 ? null : $value * ($mode === 'précis' ? 90 : 90 * $matches) / $minutes;
        }
        if ($spec['type'] === 'prevented') {
            $value = $this->number($stats[$spec['field']] ?? null);
            if ($value === null) {
                $expected = $this->number($stats[$spec['expected']] ?? null);
                $conceded = $this->number($stats[$spec['conceded']] ?? null);
                $value = $expected !== null && $conceded !== null ? $expected - $conceded : null;
            }
            return $value === null ? null : $value * ($mode === 'précis' ? 90 : 90 * $matches) / $minutes;
        }
        $attempt = $this->number($stats[$mode === 'précis' ? $spec['attempt'] : $spec['average_attempt']] ?? null);
        if ($attempt === null || $attempt < 0) {
            return null;
        }
        if ($mode === 'précis') {
            $success = $this->number($stats[$spec['success']] ?? null);
        } else {
            if (isset($spec['average_success'])) {
                $success = $this->number($stats[$spec['average_success']] ?? null);
            } else {
                $rate = $this->number($stats[$spec['average_rate']] ?? null);
                if ($rate !== null && $rate > 1 && $rate <= 100) {
                    $rate /= 100;
                }
                $success = $rate === null ? null : $attempt * $rate;
            }
        }
        if ($success === null || $success < 0 || $success > $attempt) {
            return null;
        }
        $attempt *= $matches;
        $success *= $matches;
        if ($familyRate === null) {
            return $attempt > 0 ? $success / $attempt : null;
        }
        return ($success + $prior * $familyRate) / ($attempt + $prior);
    }

    private function references(array $prepared): array
    {
        $groups = [];
        foreach ($prepared as $player) {
            if ($player['family'] !== null && $player['minutes'] >= $this->cfg['min_reference_minutes']) {
                $groups[$player['family']][] = $player;
                if ($player['family'] !== 'goalkeeper') {
                    $groups['field'][] = $player;
                }
            }
        }
        $references = [];
        foreach (array_unique(array_values($this->cfg['positions'])) as $family) {
            $pool = $groups[$family] ?? [];
            $expanded = false;
            if (count($pool) < $this->cfg['min_reference_players']) {
                foreach ($this->cfg['reference_fallback'][$family] as $neighbor) {
                    $pool = $neighbor === 'field' ? ($groups['field'] ?? []) : array_merge($pool, $groups[$neighbor] ?? []);
                    $expanded = true;
                    if (count($pool) >= $this->cfg['min_reference_players']) {
                        break;
                    }
                }
            }
            $values = $rates = [];
            foreach ($this->cfg['indicators'] as $name => $spec) {
                foreach ($pool as $person) {
                    $observations = [];
                    foreach ($person['entries'] as $i => $entry) {
                        if ($entry['family'] === $family || $family !== 'goalkeeper') {
                            $value = $this->indicator($entry, $name, $person['mode']);
                            if ($value !== null) {
                                $observations[] = [$value, $person['weights'][$i]];
                            }
                        }
                    }
                    $mean = $this->weighted($observations);
                    if ($mean !== null) {
                        $values[$name][] = $mean;
                        if ($spec['type'] === 'rate') {
                            $rates[$name][] = $mean;
                        }
                    }
                }
            }
            $stats = [];
            foreach ($values as $name => $samples) {
                $median = $this->median($samples);
                $mad = $this->median(array_map(fn ($x) => abs($x - $median), $samples));
                $stats[$name] = ['median' => $median, 'spread' => max($this->cfg['mad_floor'], $mad * $this->cfg['mad_scale'])];
            }
            $references[$family] = ['stats' => $stats, 'rates' => array_map(fn ($x) => $this->median($x), $rates),
                'expanded' => $expanded, 'size' => count($pool)];
        }
        return $references;
    }

    private function median(array $values): float
    {
        sort($values, SORT_NUMERIC);
        $n = count($values);
        return $n % 2 ? $values[intdiv($n, 2)] : ($values[$n / 2 - 1] + $values[$n / 2]) / 2;
    }

    private function weighted(array $pairs): ?float
    {
        $denominator = array_sum(array_column($pairs, 1));
        return $denominator > 0 ? array_sum(array_map(fn ($p) => $p[0] * $p[1], $pairs)) / $denominator : null;
    }

    private function dimension(array $entry, string $mode, string $family, string $dimension, array $reference): array
    {
        $names = $this->cfg['dimensions'][$family === 'goalkeeper' ? 'goalkeeper' : 'field'][$dimension];
        $available = [];
        foreach ($names as $name) {
            if (!isset($reference['stats'][$name])) {
                continue;
            }
            $spec = $this->cfg['indicators'][$name];
            $prior = $name === 'save_rate' ? $this->cfg['keeper_save_prior_attempts'] : $this->cfg['rate_prior_attempts'];
            $rate = $reference['rates'][$name] ?? null;
            $value = $this->indicator($entry, $name, $mode, $prior, $rate);
            if ($value === null) {
                continue;
            }
            $ref = $reference['stats'][$name];
            $z = ($value - $ref['median']) / $ref['spread'] * ($this->cfg['direction'][$name] ?? 1);
            $available[$name] = max(-$this->cfg['z_cap'], min($this->cfg['z_cap'], $z));
        }
        $groups = [];
        foreach ($available as $name => $z) {
            $group = $name;
            foreach ($this->cfg['redundancy_groups'] as $pair) {
                if (in_array($name, $pair, true)) {
                    $group = $pair[0];
                    break;
                }
            }
            $groups[$group][$name] = $z;
        }
        $groupScores = array_map(fn ($set) => array_sum($set) / count($set), $groups);
        $z = count($groupScores) ? array_sum($groupScores) / count($groupScores) : null;
        $contributions = [];
        foreach ($groups as $set) {
            foreach ($set as $name => $value) {
                $contributions[$name] = $value / count($set) / count($groups);
            }
        }
        return ['z' => $z, 'coverage' => count($available) / count($names), 'indicators' => $contributions];
    }

    private function scorePlayer(array $player, array $references): array
    {
        $base = ['player_id' => $player['id'], 'famille' => $player['family'], 'mode' => $player['mode'],
            'matchs' => $player['count'], 'minutes' => $player['minutes'], 'version_configuration' => $this->cfg['version']];
        if (!$player['entries']) {
            return $base + ['raison' => 'aucune donnée observée'];
        }
        if ($player['family'] === null) {
            return $base + ['raison' => 'poste détaillé inconnu'];
        }
        if ($player['minutes'] === null || $player['minutes'] <= 0 || $player['count'] === null || $player['count'] <= 0) {
            return $base + ['raison' => 'minutes ou matchs insuffisants'];
        }
        $family = $player['family'];
        if (($references[$family]['size'] ?? 0) < $this->cfg['min_reference_players']) {
            return $base + ['raison' => 'référence de poste insuffisante'];
        }
        $scores = $coverage = $dimensions = [];
        foreach ($player['entries'] as $i => $entry) {
            $playedFamily = $entry['family'];
            if ($playedFamily === null || $entry['minutes'] <= 0) {
                continue;
            }
            $ref = $references[$playedFamily];
            $names = array_keys($this->cfg['dimensions'][$playedFamily === 'goalkeeper' ? 'goalkeeper' : 'field']);
            $weights = $this->cfg['weights'][$playedFamily];
            $valid = $total = $covered = 0;
            foreach ($names as $j => $name) {
                $dim = $this->dimension($entry, $player['mode'], $playedFamily, $name, $ref);
                $covered += $weights[$j] * $dim['coverage'];
                $dimensions[$name][] = [$dim, $player['weights'][$i], $weights[$j]];
                if ($dim['z'] !== null) {
                    $valid += $weights[$j] * $dim['z'];
                    $total += $weights[$j];
                }
            }
            if ($total > 0) {
                $scores[] = [max(0, min(100, $this->cfg['score_center'] + $this->cfg['score_scale'] * $valid / $total)), $player['weights'][$i]];
                $coverage[] = [$covered / array_sum($weights), $player['weights'][$i]];
            }
        }
        $raw = $this->weighted($scores);
        if ($raw === null) {
            return $base + ['raison' => 'aucun indicateur exploitable'];
        }
        $dimOut = $dimScores = [];
        foreach ($dimensions as $name => $observations) {
            $zs = [];
            $indicatorContributions = [];
            foreach ($observations as [$dim, $weight]) {
                if ($dim['z'] !== null) {
                    $zs[] = [$dim['z'], $weight];
                    foreach ($dim['indicators'] as $indicator => $contribution) {
                        $indicatorContributions[$indicator][] = [$contribution, $weight];
                    }
                }
            }
            $z = $this->weighted($zs);
            $dimScores[$name] = $zs;
            $dimOut[$name] = ['score' => $z === null ? null : max(0, min(100, $this->cfg['score_center'] + $this->cfg['score_scale'] * $z)),
                'indicateurs' => array_map(fn ($pairs) => $this->weighted($pairs), $indicatorContributions),
                'coverage' => $this->weighted(array_map(fn ($o) => [$o[0]['coverage'], $o[1]], $observations))];
        }
        $nEff = pow(array_sum(array_column($scores, 1)), 2) /
            array_sum(array_map(fn ($p) => $p[1] ** 2, $scores));
        return $base + ['score_brut_interne' => $raw, 'scores_match' => $scores, 'n_eff' => $nEff,
            'couverture' => 100 * $this->weighted($coverage), 'dimensions' => $dimOut,
            'scores_dimension_interne' => $dimScores,
            'approx_variance' => $player['mode'] === 'approximé' ? $this->approximateVariance($player, $references[$family]) : null,
            'reference_elargie' => $references[$family]['expanded'], 'taille_reference' => $references[$family]['size']];
    }

    private function varianceModels(array $raw): array
    {
        $groups = [];
        foreach ($raw as $row) {
            if (isset($row['score_brut_interne'])) {
                $groups[$row['famille']][] = $row;
            }
        }
        $models = [];
        foreach ($groups as $family => $players) {
            $within = [];
            foreach ($players as $player) {
                $pairs = $player['scores_match'];
                if (count($pairs) > 1) {
                    $within[] = array_sum(array_map(fn ($p) => $p[1] * ($p[0] - $player['score_brut_interne']) ** 2, $pairs))
                        / array_sum(array_column($pairs, 1)) / ($this->cfg['score_scale'] ** 2);
                }
            }
            $sigma = count($within) ? array_sum($within) / count($within) : 1.0;
            $means = array_column($players, 'score_brut_interne');
            $mean = array_sum($means) / count($means);
            $between = array_sum(array_map(fn ($x) => (($x - $mean) / $this->cfg['score_scale']) ** 2, $means)) / count($means);
            $noise = array_sum(array_map(fn ($p) => $sigma / $p['n_eff'], $players)) / count($players);
            $dimensionModels = [];
            foreach (array_keys($players[0]['dimensions']) as $dimension) {
                $dimensionPlayers = array_filter($players, fn ($p) => count($p['scores_dimension_interne'][$dimension] ?? []) > 0);
                if (!$dimensionPlayers) continue;
                $dimensionMeans = $dimensionWithin = $dimensionNoise = [];
                foreach ($dimensionPlayers as $p) {
                    $items = $p['scores_dimension_interne'][$dimension];
                    $average = $this->weighted($items);
                    $dimensionMeans[] = $average;
                    $effective = pow(array_sum(array_column($items, 1)), 2) /
                        array_sum(array_map(fn ($item) => $item[1] ** 2, $items));
                    if (count($items) > 1) {
                        $dimensionWithin[] = array_sum(array_map(fn ($item) => $item[1] * ($item[0] - $average) ** 2, $items)) /
                            array_sum(array_column($items, 1));
                    }
                    $dimensionNoise[] = $effective;
                }
                $dsigma = count($dimensionWithin) ? array_sum($dimensionWithin) / count($dimensionWithin) : 1;
                $dm = array_sum($dimensionMeans) / count($dimensionMeans);
                $dbetween = array_sum(array_map(fn ($z) => ($z - $dm) ** 2, $dimensionMeans)) / count($dimensionMeans);
                $dnoise = array_sum(array_map(fn ($n) => $dsigma / $n, $dimensionNoise)) / count($dimensionNoise);
                $dimensionModels[$dimension] = ['sigma' => $dsigma, 'tau' => max($this->cfg['tau_variance_floor'], $dbetween - $dnoise)];
            }
            $models[$family] = ['sigma' => $sigma, 'tau' => max($this->cfg['tau_variance_floor'], $between - $noise),
                'mean' => $mean, 'dimensions' => $dimensionModels];
        }
        return $models;
    }

    private function finalize(array $row, array $models): array
    {
        if (!isset($row['score_brut_interne'])) {
            return $row + ['score_brut' => null, 'score_corrige' => null, 'intervalle_80' => null,
                'fiabilite' => null, 'niveau' => null, 'couverture' => null,
                'reference_elargie' => null, 'dimensions' => [],
                'fiabilite_approximative' => $row['mode'] === 'approximé'];
        }
        $model = $models[$row['famille']];
        $sigma = $row['mode'] === 'approximé' ? $row['approx_variance'] : $model['sigma'];
        $r = $model['tau'] / ($model['tau'] + $sigma / $row['n_eff']);
        $reliability = 100 * $r * $row['couverture'] / 100;
        $level = $reliability < $this->cfg['reliability_levels']['medium'] ? 'faible'
            : ($reliability <= $this->cfg['reliability_levels']['solid'] ? 'moyenne' : 'solide');
        $corrected = $model['mean'] + $r * ($row['score_brut_interne'] - $model['mean']);
        $radius = $this->cfg['interval_multiplier_80'] * $this->cfg['score_scale'] * sqrt($model['tau']) * sqrt(1 - $r);
        $visible = $reliability >= $this->cfg['minimum_reliability_display'];
        foreach ($row['dimensions'] as $name => &$dimension) {
            $items = $row['scores_dimension_interne'][$name] ?? [];
            $dm = $model['dimensions'][$name] ?? null;
            if ($dm && count($items)) {
                $dn = pow(array_sum(array_column($items, 1)), 2) /
                    array_sum(array_map(fn ($item) => $item[1] ** 2, $items));
                $dsigma = $row['mode'] === 'approximé' ? $row['approx_variance'] : $dm['sigma'];
                $dr = $dm['tau'] / ($dm['tau'] + $dsigma / $dn);
                $dimension['fiabilite'] = round(100 * $dr * ($dimension['coverage'] ?? 0), 2);
            } else {
                $dimension['fiabilite'] = 0;
            }
            unset($dimension['coverage']);
        }
        unset($dimension);
        $raw = $row['score_brut_interne'];
        unset($row['score_brut_interne'], $row['scores_match'], $row['n_eff'], $row['approx_variance'], $row['scores_dimension_interne']);
        return $row + [
            'score_brut' => $visible ? round($raw, 2) : null,
            'score_corrige' => $visible ? round(max(0, min(100, $corrected)), 2) : null,
            'intervalle_80' => $visible ? [round(max(0, $corrected - $radius), 2), round(min(100, $corrected + $radius), 2)] : null,
            'fiabilite' => round($reliability, 2), 'niveau' => $level,
            'fiabilite_approximative' => $row['mode'] === 'approximé',
            'raison' => $visible ? null : 'Fiabilité insuffisante',
        ];
    }

    private function approximateVariance(array $player, array $reference): float
    {
        // Delta approximatif en unités z : Poisson pour les volumes, binomiale
        // pour les réussites. Les covariances inconnues imposent la mention dédiée.
        $entry = $player['entries'][0];
        $minutes = $player['minutes'];
        $matches = $player['count'];
        if ($minutes <= 0 || $matches <= 0) {
            return 1;
        }
        $variance = [];
        foreach ($this->cfg['dimensions'][$player['family'] === 'goalkeeper' ? 'goalkeeper' : 'field'] as $names) {
            foreach ($names as $name) {
                if (!isset($reference['stats'][$name])) {
                    continue;
                }
                $spec = $this->cfg['indicators'][$name];
                $spread = $reference['stats'][$name]['spread'];
                if ($spec['type'] === 'count') {
                    $perMatch = $this->number($entry['stats'][$spec['average']] ?? null);
                    if ($perMatch !== null && $perMatch >= 0) {
                        $variance[] = $perMatch * $matches * (90 / $minutes / $spread) ** 2;
                    }
                } elseif ($spec['type'] === 'rate') {
                    $attempt = $this->number($entry['stats'][$spec['average_attempt']] ?? null);
                    $rate = $this->indicator($entry, $name, 'approximé');
                    if ($attempt !== null && $attempt > 0 && $rate !== null) {
                        $prior = $name === 'save_rate' ? $this->cfg['keeper_save_prior_attempts'] : $this->cfg['rate_prior_attempts'];
                        $variance[] = ($attempt * $matches * $rate * (1 - $rate)) /
                            (($attempt * $matches + $prior) ** 2 * $spread ** 2);
                    }
                }
            }
        }
        return count($variance) ? max(0.0001, array_sum($variance) / (count($variance) ** 2)) : 1;
    }
}
