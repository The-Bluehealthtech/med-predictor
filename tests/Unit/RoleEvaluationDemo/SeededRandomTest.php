<?php

namespace Tests\Unit\RoleEvaluationDemo;

use App\Services\RoleEvaluationDemo\SeededRandom;
use PHPUnit\Framework\TestCase;

/**
 * Aucun accès base : teste uniquement la reproductibilité du RNG
 * (mandat Livrable 3 : "reproductible, graine fixe").
 */
class SeededRandomTest extends TestCase
{
    public function test_same_seed_produces_identical_sequence(): void
    {
        $a = new SeededRandom(1234);
        $b = new SeededRandom(1234);

        $seqA = [];
        $seqB = [];
        for ($i = 0; $i < 50; $i++) {
            $seqA[] = $a->nextInt(0, 1000);
            $seqB[] = $b->nextInt(0, 1000);
        }

        $this->assertSame($seqA, $seqB);
    }

    public function test_different_seeds_diverge(): void
    {
        $a = new SeededRandom(1);
        $b = new SeededRandom(2);

        $seqA = [];
        $seqB = [];
        for ($i = 0; $i < 20; $i++) {
            $seqA[] = $a->nextInt(0, 1_000_000);
            $seqB[] = $b->nextInt(0, 1_000_000);
        }

        $this->assertNotSame($seqA, $seqB);
    }

    public function test_next_int_stays_within_bounds(): void
    {
        $rng = new SeededRandom(7);
        for ($i = 0; $i < 200; $i++) {
            $v = $rng->nextInt(5, 9);
            $this->assertGreaterThanOrEqual(5, $v);
            $this->assertLessThanOrEqual(9, $v);
        }
    }

    public function test_chance_zero_never_true_and_one_always_true(): void
    {
        $rng = new SeededRandom(99);
        for ($i = 0; $i < 50; $i++) {
            $this->assertFalse($rng->chance(0.0));
        }
        for ($i = 0; $i < 50; $i++) {
            $this->assertTrue($rng->chance(1.0));
        }
    }

    public function test_shuffle_is_a_permutation(): void
    {
        $rng = new SeededRandom(55);
        $items = range(1, 20);
        $shuffled = $rng->shuffleArray($items);

        sort($shuffled);
        $this->assertSame($items, $shuffled);
    }

    public function test_noisy_count_respects_bounds(): void
    {
        $rng = new SeededRandom(321);
        for ($i = 0; $i < 200; $i++) {
            $v = $rng->noisyCount(2.0, 5.0, 0, 10);
            $this->assertGreaterThanOrEqual(0, $v);
            $this->assertLessThanOrEqual(10, $v);
        }
    }
}
