<?php

namespace App\Services\RoleEvaluationEngine;

use Illuminate\Support\Facades\DB;

/**
 * Adaptateur de données pour les profils de période : les exports « Player
 * statistics » des clubs (moyennes par match sur une saison), enregistrés dans
 * external_player_performance_metrics, -> entrée "average" (mode « approximé »)
 * de app\Services\PerformanceScoreCalculator::calculate() (non modifié).
 *
 * Les moyennes par match sont traduites vers les champs attendus par le moteur
 * (config/role_evaluation_engine.php, clés "average*") ; les pourcentages de
 * réussite deviennent des réussites par match (tentatives × taux). Les duels au
 * sol sont déduits des duels totaux moins les duels aériens.
 *
 * Nombre de matchs : indicateur matches_played s'il existe, sinon déduit des
 * moyennes elles-mêmes (une moyenne à deux décimales × le bon nombre de matchs
 * redonne des entiers), sinon estimé à partir des minutes (signalé).
 *
 * Données réelles uniquement (pas de profils de démonstration).
 */
final class PeriodStatsDataSource
{
    /** Champ du moteur => indicateur enregistré (moyenne par match). */
    private const COUNTS = [
        'goals_scored' => 'goals',
        'assists_provided' => 'assists',
        'shots_on_target' => 'shots_on_target',
        'key_passes' => 'key_passes',
        'passes_total' => 'passes',
        'dribbles_attempted' => 'dribbles',
        'crosses_total' => 'crosses',
        'tackles_total' => 'tackles',
        'interceptions' => 'interceptions',
        'recoveries' => 'loose_ball_recoveries',
        'fouls_committed' => 'fouls_committed',
        'yellow_cards' => 'yellow_cards',
        'red_cards' => 'red_cards',
        'mistakes_leading_to_shot' => 'mistakes_leading_to_chances',
        'mistakes_leading_to_goal' => 'mistakes_leading_to_goals',
        'long_passes' => 'long_passes',
        'aerial_duels_total' => 'aerial_challenges',
        'expected_goals' => 'expected_goals',
        'progressive_passes' => 'progressive_passes',
        'dribbles_final_third' => 'dribbling_in_the_final_third',
        'chances_created' => 'chances_created',
    ];

    /** Champ « réussis » du moteur => [champ tentatives, indicateurs de réussite (taux ou nombre)]. */
    private const SUCCESSES = [
        'passes_completed' => ['passes_total', ['passes_accuracy', 'passes_accurate']],
        'dribbles_completed' => ['dribbles_attempted', ['dribbles_successful']],
        'crosses_completed' => ['crosses_total', ['crosses_accurate']],
        'tackles_won' => ['tackles_total', ['tackles_successful']],
        'long_passes_completed' => ['long_passes', ['long_passes_accurate']],
        'aerial_duels_won' => ['aerial_duels_total', ['aerial_challenges_won']],
        'progressive_passes_completed' => ['progressive_passes', ['progressive_passes_accurate']],
        'dribbles_final_third_completed' => ['dribbles_final_third', ['dribbling_in_the_final_third_successful']],
    ];

    private const MAX_MATCHES = 60;

    private const MAX_MINUTES_PER_MATCH = 130;

    /** Joueurs disposant d'un profil de période observé. */
    public function playerIds(): array
    {
        return DB::table('external_player_performance_metrics')->where('score_origin', 'observed')
            ->where('metric_name', 'minutes_played')->distinct()->pluck('player_id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * @param  int[]|null  $playerIds  null : tous les joueurs ayant un profil de période.
     * @return array [['id', 'position', 'average' => ['matches','minutes','stats'], 'period' => ['measured_at','matches_origin']], ...]
     */
    public function forPlayers(?array $playerIds = null): array
    {
        $query = DB::table('external_player_performance_metrics')->where('score_origin', 'observed');
        if ($playerIds !== null) {
            if ($playerIds === []) {
                return [];
            }
            $query->whereIn('player_id', $playerIds);
        }
        $positions = DB::table('players')->whereIn('id', (clone $query)->distinct()->pluck('player_id'))->pluck('position', 'id');

        $entries = [];
        foreach ($query->orderBy('measured_at')->orderBy('id')->get()->groupBy('player_id') as $playerId => $rows) {
            $entry = $this->entry((int) $playerId, $rows->keyBy('metric_name'), $positions[$playerId] ?? null);
            if ($entry !== null) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    private function entry(int $playerId, $metrics, ?string $playerPosition): ?array
    {
        $value = fn (string $name) => isset($metrics[$name]) && is_numeric($metrics[$name]->metric_value) && (float) $metrics[$name]->metric_value >= 0
            ? (float) $metrics[$name]->metric_value : null;

        $minutes = $value('minutes_played');
        if (!$minutes) {
            return null;
        }

        $stats = [];
        foreach (self::COUNTS as $field => $metric) {
            $stats[$field] = $value($metric);
        }
        foreach (self::SUCCESSES as $field => [$attemptField, $candidates]) {
            $attempt = $stats[$attemptField];
            foreach ($candidates as $metric) {
                $raw = $value($metric);
                if ($raw === null || $attempt === null) {
                    continue;
                }
                $isRate = ($metrics[$metric]->metric_unit ?? null) === 'percent';
                $success = $isRate ? $attempt * ($raw > 1 ? $raw / 100 : $raw) : $raw;
                $stats[$field] = min($attempt, $success);
                break;
            }
        }
        $duels = $value('challenges');
        $duelsRate = $value('challenges_won');
        if ($duels !== null && $stats['aerial_duels_total'] !== null) {
            $stats['ground_duels_total'] = max(0, $duels - $stats['aerial_duels_total']);
            if ($duelsRate !== null && isset($stats['aerial_duels_won'])) {
                $won = $duels * ($duelsRate > 1 ? $duelsRate / 100 : $duelsRate) - $stats['aerial_duels_won'];
                $stats['ground_duels_won'] = max(0, min($stats['ground_duels_total'], $won));
            }
        }
        $stats = array_filter($stats, fn ($v) => $v !== null);

        [$matches, $origin] = $this->matches($value('matches_played'), $minutes, array_map(fn ($m) => $value($m), array_diff(array_values(self::COUNTS), ['expected_goals'])));

        return [
            'id' => $playerId,
            'position' => $this->position($metrics, $playerPosition),
            'average' => ['matches' => $matches, 'minutes' => $minutes, 'stats' => $stats],
            'period' => ['measured_at' => optional($metrics->first())->measured_at, 'matches_origin' => $origin],
        ];
    }

    /** Poste détaillé de l'export (position_catalog.code), sinon celui de la fiche joueur. */
    private function position($metrics, ?string $playerPosition): ?string
    {
        foreach ($metrics as $row) {
            $raw = is_string($row->raw_data) ? json_decode($row->raw_data, true) : (array) $row->raw_data;
            $position = trim((string) ($raw['Position'] ?? ''));
            if ($position !== '' && $position !== '-') {
                return $position;
            }
        }

        return $playerPosition;
    }

    /**
     * Nombre de matchs : renseigné, sinon déduit des moyennes (plus petit n
     * pour lequel chaque moyenne × n est entière à l'arrondi près), sinon
     * estimé à 90 minutes par match.
     *
     * @return array{0: int, 1: string} [nombre, origine : declared|inferred|estimated]
     */
    public function matches(?float $declared, float $minutes, array $averages): array
    {
        if ($declared !== null && $declared >= 1 && floor($declared) === $declared) {
            return [(int) $declared, 'declared'];
        }
        $averages = array_values(array_filter($averages, fn ($v) => $v !== null && $v > 0 && floor($v) !== $v));
        $min = max(1, (int) ceil($minutes / self::MAX_MINUTES_PER_MATCH));
        if (count($averages) >= 3) {
            for ($n = $min; $n <= self::MAX_MATCHES; $n++) {
                $fits = true;
                foreach ($averages as $average) {
                    $total = $average * $n;
                    if (abs($total - round($total)) > 0.0051 * $n) {
                        $fits = false;
                        break;
                    }
                }
                if ($fits) {
                    return [$n, 'inferred'];
                }
            }
        }

        return [max($min, (int) round($minutes / 90)), 'estimated'];
    }
}
