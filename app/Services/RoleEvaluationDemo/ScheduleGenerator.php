<?php

namespace App\Services\RoleEvaluationDemo;

/**
 * Calendrier en double round-robin (méthode du cercle / "circle method") sur
 * un nombre pair d'équipes : chaque équipe rencontre toutes les autres une
 * fois à domicile et une fois à l'extérieur, soit 2*(N-1) matchs par équipe.
 *
 * Choisi pour satisfaire "au moins 30 matchs par équipe" (mandat, LIVRABLE 3)
 * de façon exacte et sans產match répété inutilement : avec N=16 équipes,
 * 2*(16-1) = 30 matchs/équipe pile. Aucune équipe ne se rencontre elle-même,
 * aucun match généré en double, chaque paire {domicile, extérieur} apparaît
 * exactement une fois dans chaque sens.
 */
class ScheduleGenerator
{
    /**
     * @param array<int, mixed> $teamIds identifiants opaques (ordre = seeding), doit être pair
     * @return array<int, array{round:int, home:mixed, away:mixed}>
     */
    public function generateDoubleRoundRobin(array $teamIds): array
    {
        $n = count($teamIds);
        if ($n < 2) {
            return [];
        }
        if ($n % 2 !== 0) {
            // Équipe fictive "bye" : ses matchs sont simplement omis, elle ne
            // joue jamais. Documenté comme hypothèse si jamais un nombre
            // impair de clubs démo est demandé un jour (le générateur actuel
            // impose un nombre pair, voir DemoDataGenerator).
            $teamIds[] = null;
            $n++;
        }

        $fixed = $teamIds[0];
        $rotating = array_slice($teamIds, 1);
        $roundsPerLeg = $n - 1;

        $firstLeg = [];
        for ($round = 0; $round < $roundsPerLeg; $round++) {
            $arrangement = array_merge([$fixed], $rotating);
            for ($i = 0; $i < intdiv($n, 2); $i++) {
                $home = $arrangement[$i];
                $away = $arrangement[$n - 1 - $i];
                if ($home === null || $away === null) {
                    continue;
                }
                // Alterne domicile/extérieur d'une ronde à l'autre pour éviter
                // qu'une même équipe joue systématiquement à domicile en position $i=0.
                if ($round % 2 === 1) {
                    [$home, $away] = [$away, $home];
                }
                $firstLeg[] = ['round' => $round, 'home' => $home, 'away' => $away];
            }
            // Rotation : dernier élément de $rotating passe en tête.
            array_unshift($rotating, array_pop($rotating));
        }

        $secondLeg = [];
        foreach ($firstLeg as $fixture) {
            $secondLeg[] = [
                'round' => $fixture['round'] + $roundsPerLeg,
                'home' => $fixture['away'],
                'away' => $fixture['home'],
            ];
        }

        return array_merge($firstLeg, $secondLeg);
    }
}
