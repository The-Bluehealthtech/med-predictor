<?php

namespace Tests\Unit;

use App\Services\PerformanceScoreCalculator;
use PHPUnit\Framework\TestCase;

final class PerformanceScoreCalculatorTest extends TestCase
{
    private function service(): PerformanceScoreCalculator
    {
        return new PerformanceScoreCalculator(require dirname(__DIR__, 2) . '/config/player_performance_score.php');
    }

    private function player(int $id, int $count, string $position = 'CB', array $override = []): array
    {
        $matches = [];
        for ($i = 0; $i < $count; $i++) {
            $matches[] = ['match_id' => $i + 1, 'date' => sprintf('2025-01-%02d', $i + 1),
                'position' => $position, 'minutes' => 90,
                'stats' => array_merge(['passes_total' => 20 + $id % 20 + ($i % 3),
                    'passes_completed' => 15 + $id % 15 + ($i % 3),
                    'interceptions' => 2 + $id % 5, 'tackles_total' => 4,
                    'tackles_won' => 3, 'fouls_committed' => 1], $override)];
        }
        return ['id' => $id, 'position' => $position, 'matches' => $matches];
    }

    private function cohort(): array
    {
        $players = [];
        for ($i = 1; $i <= 40; $i++) {
            $players[] = $this->player($i, 8);
        }
        return $players;
    }

    private function find(array $results, int $id): array
    {
        foreach ($results as $result) {
            if ($result['player_id'] === $id) return $result;
        }
        self::fail('Joueur absent du résultat');
    }

    public function test_more_matches_increase_reliability(): void
    {
        $players = $this->cohort();
        $players[] = $this->player(100, 5);
        $players[] = $this->player(101, 30);
        $results = $this->service()->calculate($players);
        self::assertGreaterThan($this->find($results, 100)['fiabilite'], $this->find($results, 101)['fiabilite']);
    }

    public function test_extreme_short_sample_is_shrunk(): void
    {
        $players = $this->cohort();
        $players[] = $this->player(100, 5, 'CB', ['passes_total' => 120, 'passes_completed' => 115, 'interceptions' => 20]);
        $result = $this->find($this->service()->calculate($players), 100);
        self::assertNotNull($result['score_corrige']);
        self::assertLessThan(abs($result['score_brut'] - 50), abs($result['score_corrige'] - 50));
    }

    public function test_one_success_is_shrunk_to_family_prior(): void
    {
        $players = $this->cohort();
        $players[] = $this->player(100, 1, 'CB', ['passes_total' => 1, 'passes_completed' => 1]);
        $result = $this->find($this->service()->calculate($players), 100);
        self::assertNotNull($result['score_brut']);
        self::assertLessThan(2, abs($result['dimensions']['construction']['indicateurs']['pass_rate']));
    }

    public function test_missing_indicator_reduces_coverage(): void
    {
        $players = $this->cohort();
        $a = $this->player(100, 5);
        $b = $a;
        foreach ($b['matches'] as &$match) unset($match['stats']['interceptions']);
        unset($match);
        $players[] = $a;
        $full = $this->find($this->service()->calculate($players), 100);
        $players[count($players) - 1] = $b;
        $missing = $this->find($this->service()->calculate($players), 100);
        self::assertLessThan($full['couverture'], $missing['couverture']);
        self::assertNull($missing['dimensions']['recuperation']['score']);
        self::assertNotNull($missing['dimensions']['construction']['score']);
    }

    public function test_zero_minutes_missing_and_unknown_position_are_explicit(): void
    {
        $players = $this->cohort();
        $players[] = ['id' => 100, 'position' => 'CB', 'average' => ['matches' => 2, 'minutes' => 0, 'stats' => []]];
        $players[] = ['id' => 101, 'position' => 'CB', 'average' => ['matches' => 2, 'minutes' => 180, 'stats' => []]];
        $players[] = ['id' => 102, 'position' => 'UNKNOWN', 'average' => ['matches' => 2, 'minutes' => 180, 'stats' => []]];
        $players[] = ['id' => 103, 'position' => 'CB', 'average' => ['matches' => null, 'minutes' => null, 'stats' => []]];
        $results = $this->service()->calculate($players);
        foreach ([100, 101, 102, 103] as $id) {
            $r = $this->find($results, $id);
            self::assertNull($r['score_corrige']);
            self::assertNotEmpty($r['raison']);
        }
        self::assertNull($this->find($results, 103)['minutes']);
        self::assertNull($this->find($results, 103)['matchs']);
    }

    public function test_goalkeeper_has_separate_reference(): void
    {
        $players = $this->cohort();
        for ($i = 200; $i < 235; $i++) {
            $players[] = $this->player($i, 8, 'GK', ['shots_on_target_against' => 5, 'saves' => 4]);
        }
        $result = $this->find($this->service()->calculate($players), 200);
        self::assertSame('goalkeeper', $result['famille']);
        self::assertArrayHasKey('arrets_buts_evites', $result['dimensions']);
        self::assertArrayNotHasKey('finition_creation', $result['dimensions']);
    }

    public function test_results_are_deterministic_and_approximate_mode_is_labeled(): void
    {
        $players = $this->cohort();
        $players[] = ['id' => 100, 'position' => 'CB', 'average' => ['matches' => 8, 'minutes' => 720,
            'stats' => ['passes' => 32, 'passes_accuracy' => .8, 'interceptions' => 3]]];
        $first = $this->service()->calculate($players);
        self::assertSame($first, $this->service()->calculate(array_reverse($players)));
        self::assertSame('approximé', $this->find($first, 100)['mode']);
        self::assertTrue($this->find($first, 100)['fiabilite_approximative']);
    }

    public function test_split_half_diagnostic_is_comparable_to_statistical_reliability(): void
    {
        $players = $this->cohort();
        $correlation = $this->service()->splitHalfCorrelation($players);
        $results = $this->service()->calculate($players);
        self::assertNotNull($correlation);
        self::assertGreaterThanOrEqual(-1, $correlation);
        self::assertLessThanOrEqual(1, $correlation);
        $statistical = $this->find($results, 10)['fiabilite'] / $this->find($results, 10)['couverture'];
        self::assertGreaterThanOrEqual(0, $statistical);
        self::assertLessThanOrEqual(1, $statistical);
    }
}
