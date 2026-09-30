<?php

namespace Tests\Unit\RoleEvaluationDemo;

use App\Services\RoleEvaluationDemo\ScheduleGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Aucun accès base. Vérifie les propriétés structurelles attendues du
 * calendrier (mandat Livrable 3 : "au moins 30 matchs par équipe",
 * "aucun produit cartésien").
 */
class ScheduleGeneratorTest extends TestCase
{
    public function test_sixteen_teams_gives_exactly_thirty_matches_per_team(): void
    {
        $fixtures = (new ScheduleGenerator())->generateDoubleRoundRobin(range(0, 15));

        $this->assertCount(16 * 30 / 2, $fixtures); // 240 matchs au total

        $countByTeam = array_fill(0, 16, 0);
        foreach ($fixtures as $f) {
            $countByTeam[$f['home']]++;
            $countByTeam[$f['away']]++;
        }
        foreach ($countByTeam as $team => $count) {
            $this->assertSame(30, $count, "équipe {$team} devrait avoir 30 matchs");
        }
    }

    public function test_no_team_plays_itself(): void
    {
        $fixtures = (new ScheduleGenerator())->generateDoubleRoundRobin(range(0, 7));
        foreach ($fixtures as $f) {
            $this->assertNotSame($f['home'], $f['away']);
        }
    }

    public function test_every_pair_meets_exactly_once_home_and_once_away(): void
    {
        $teams = range(0, 5);
        $fixtures = (new ScheduleGenerator())->generateDoubleRoundRobin($teams);

        $seen = [];
        foreach ($fixtures as $f) {
            $key = $f['home'].'->'.$f['away'];
            $this->assertArrayNotHasKey($key, $seen, "match {$key} généré en double");
            $seen[$key] = true;
        }

        foreach ($teams as $a) {
            foreach ($teams as $b) {
                if ($a === $b) {
                    continue;
                }
                $this->assertArrayHasKey("{$a}->{$b}", $seen, "{$a} devrait recevoir {$b} une fois");
            }
        }
    }

    public function test_odd_number_of_teams_uses_a_bye_without_error(): void
    {
        $fixtures = (new ScheduleGenerator())->generateDoubleRoundRobin(range(0, 4)); // 5 équipes
        foreach ($fixtures as $f) {
            $this->assertNotNull($f['home']);
            $this->assertNotNull($f['away']);
        }
        // Avec bye, chaque équipe joue 2*(5-1) - (matchs sautés contre le bye) : au moins un match généré.
        $this->assertNotEmpty($fixtures);
    }
}
