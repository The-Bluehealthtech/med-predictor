<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Performance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlayerPerformanceService
{
    protected $player;
    protected $performances;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->loadPerformances();
    }

    /**
     * Charge les performances du joueur
     */
    protected function loadPerformances()
    {
        $this->performances = Performance::where('player_id', $this->player->id)
            ->orderBy('match_date', 'desc')
            ->get();
    }

    /**
     * Calcule les statistiques offensives
     */
    public function getOffensiveStats()
    {
        if ($this->performances->isEmpty()) {
            return $this->getDefaultOffensiveStats();
        }

        $totalGoals = $this->performances->sum('goals_scored');
        $totalAssists = $this->performances->sum('assists');
        $totalShots = $this->performances->sum('shots_total');
        $totalShotsOnTarget = $this->performances->sum('shots_on_target');
        $totalPasses = $this->performances->sum('passes_completed');
        $totalPassesAttempted = $this->performances->sum('passes_attempted');
        $matchCount = $this->performances->count();

        // Calcul des moyennes par match
        $avgGoals = $matchCount > 0 ? round($totalGoals / $matchCount, 1) : 0;
        $avgAssists = $matchCount > 0 ? round($totalAssists / $matchCount, 1) : 0;
        $avgShots = $matchCount > 0 ? round($totalShots / $matchCount, 1) : 0;
        $avgShotsOnTarget = $matchCount > 0 ? round($totalShotsOnTarget / $matchCount, 1) : 0;
        $avgPasses = $matchCount > 0 ? round($totalPasses / $matchCount, 0) : 0;

        // Calcul des pourcentages
        $shotAccuracy = $totalShots > 0 ? round(($totalShotsOnTarget / $totalShots) * 100, 1) : 0;
        $passAccuracy = $totalPassesAttempted > 0 ? round(($totalPasses / $totalPassesAttempted) * 100, 1) : 0;

        // Calcul des moyennes d'équipe (simulées pour l'instant, à remplacer par des vraies données)
        $teamAvgGoals = 1.2;
        $teamAvgAssists = 0.8;
        $teamAvgShots = 3.5;
        $teamAvgPasses = 25;

        // Calcul des pourcentages de performance vs équipe
        $goalsPercentage = $teamAvgGoals > 0 ? min(100, round(($avgGoals / $teamAvgGoals) * 100)) : 100;
        $assistsPercentage = $teamAvgAssists > 0 ? min(100, round(($avgAssists / $teamAvgAssists) * 100)) : 100;
        $shotsPercentage = $teamAvgShots > 0 ? min(100, round(($avgShots / $teamAvgShots) * 100)) : 100;
        $passesPercentage = $teamAvgPasses > 0 ? min(100, round(($avgPasses / $teamAvgPasses) * 100)) : 100;

        return [
            [
                'name' => 'Buts',
                'display' => $totalGoals,
                'percentage' => $goalsPercentage,
                'teamAvg' => $teamAvgGoals,
                'leagueAvg' => round($teamAvgGoals * 0.8, 1)
            ],
            [
                'name' => 'Assists',
                'display' => $totalAssists,
                'percentage' => $assistsPercentage,
                'teamAvg' => $teamAvgAssists,
                'leagueAvg' => round($teamAvgAssists * 0.8, 1)
            ],
            [
                'name' => 'Tirs cadrés',
                'display' => $shotAccuracy . '%',
                'percentage' => min(100, round($shotAccuracy * 1.2)),
                'teamAvg' => '65%',
                'leagueAvg' => '58%'
            ],
            [
                'name' => 'Tirs/match',
                'display' => $avgShots,
                'percentage' => $shotsPercentage,
                'teamAvg' => $teamAvgShots,
                'leagueAvg' => round($teamAvgShots * 0.8, 1)
            ],
            [
                'name' => 'Passes réussies',
                'display' => $passAccuracy . '%',
                'percentage' => $passesPercentage,
                'teamAvg' => '82%',
                'leagueAvg' => '78%'
            ],
            [
                'name' => 'Passes/match',
                'display' => $avgPasses,
                'percentage' => $passesPercentage,
                'teamAvg' => $teamAvgPasses,
                'leagueAvg' => round($teamAvgPasses * 0.9, 0)
            ]
        ];
    }

    /**
     * Calcule les statistiques physiques
     */
    public function getPhysicalStats()
    {
        if ($this->performances->isEmpty()) {
            return $this->getDefaultPhysicalStats();
        }

        $totalDistance = $this->performances->sum('distance_covered');
        $totalSprints = $this->performances->sum('sprint_count');
        $maxSpeed = $this->performances->max('max_speed');
        $avgSpeed = $this->performances->avg('avg_speed');
        $matchCount = $this->performances->count();

        // Calcul des moyennes par match
        $avgDistance = $matchCount > 0 ? round($totalDistance / $matchCount, 0) : 0;
        $avgSprints = $matchCount > 0 ? round($totalSprints / $matchCount, 0) : 0;

        // Calcul des moyennes d'équipe (simulées pour l'instant)
        $teamAvgDistance = 9500;
        $teamAvgSprints = 22;
        $teamAvgMaxSpeed = 32.5;
        $teamAvgSpeed = 8.2;

        // Calcul des pourcentages de performance vs équipe
        $distancePercentage = $teamAvgDistance > 0 ? min(100, round(($avgDistance / $teamAvgDistance) * 100)) : 100;
        $sprintsPercentage = $teamAvgSprints > 0 ? min(100, round(($avgSprints / $teamAvgSprints) * 100)) : 100;
        $maxSpeedPercentage = $teamAvgMaxSpeed > 0 ? min(100, round(($maxSpeed / $teamAvgMaxSpeed) * 100)) : 100;
        $avgSpeedPercentage = $teamAvgSpeed > 0 ? min(100, round(($avgSpeed / $teamAvgSpeed) * 100)) : 100;

        return [
            [
                'name' => 'Distance/match',
                'display' => round($avgDistance / 1000, 1) . ' km',
                'percentage' => $distancePercentage,
                'teamAvg' => round($teamAvgDistance / 1000, 1) . ' km',
                'leagueAvg' => round($teamAvgDistance * 0.95 / 1000, 1) . ' km'
            ],
            [
                'name' => 'Sprints',
                'display' => $avgSprints . '/match',
                'percentage' => $sprintsPercentage,
                'teamAvg' => $teamAvgSprints,
                'leagueAvg' => round($teamAvgSprints * 0.9, 0)
            ],
            [
                'name' => 'Vitesse max',
                'display' => round($maxSpeed, 1) . ' km/h',
                'percentage' => $maxSpeedPercentage,
                'teamAvg' => $teamAvgMaxSpeed . ' km/h',
                'leagueAvg' => round($teamAvgMaxSpeed * 0.95, 1) . ' km/h'
            ],
            [
                'name' => 'Vitesse moy',
                'display' => round($avgSpeed, 1) . ' km/h',
                'percentage' => $avgSpeedPercentage,
                'teamAvg' => $teamAvgSpeed . ' km/h',
                'leagueAvg' => round($teamAvgSpeed * 0.95, 1) . ' km/h'
            ],
            [
                'name' => 'Intensité',
                'display' => round(($avgDistance / 10000) * 100, 0) . '%',
                'percentage' => min(100, round(($avgDistance / 10000) * 100)),
                'teamAvg' => '95%',
                'leagueAvg' => '90%'
            ],
            [
                'name' => 'Récupération',
                'display' => round($avgSprints * 0.22, 1) . '/match',
                'percentage' => min(100, round(($avgSprints / 25) * 100)),
                'teamAvg' => '5.0',
                'leagueAvg' => '4.5'
            ]
        ];
    }

    /**
     * Calcule les statistiques techniques
     */
    public function getTechnicalStats()
    {
        if ($this->performances->isEmpty()) {
            return $this->getDefaultTechnicalStats();
        }

        $totalPasses = $this->performances->sum('passes_completed');
        $totalPassesAttempted = $this->performances->sum('passes_attempted');
        $totalTackles = $this->performances->sum('tackles_won');
        $totalTacklesAttempted = $this->performances->sum('tackles_attempted');
        $totalShots = $this->performances->sum('shots_total');
        $totalShotsOnTarget = $this->performances->sum('shots_on_target');
        $matchCount = $this->performances->count();

        // Calcul des moyennes par match
        $avgPasses = $matchCount > 0 ? round($totalPasses / $matchCount, 0) : 0;
        $avgTackles = $matchCount > 0 ? round($totalTackles / $matchCount, 1) : 0;
        $avgShots = $matchCount > 0 ? round($totalShots / $matchCount, 1) : 0;

        // Calcul des pourcentages
        $passAccuracy = $totalPassesAttempted > 0 ? round(($totalPasses / $totalPassesAttempted) * 100, 1) : 0;
        $tackleSuccess = $totalTacklesAttempted > 0 ? round(($totalTackles / $totalTacklesAttempted) * 100, 1) : 0;
        $shotAccuracy = $totalShots > 0 ? round(($totalShotsOnTarget / $totalShots) * 100, 1) : 0;

        // Calcul des moyennes d'équipe (simulées pour l'instant)
        $teamAvgPassAccuracy = 82;
        $teamAvgTackleSuccess = 75;
        $teamAvgShots = 3.5;
        $teamAvgPasses = 25;

        // Calcul des pourcentages de performance vs équipe
        $passAccuracyPercentage = $teamAvgPassAccuracy > 0 ? min(100, round(($passAccuracy / $teamAvgPassAccuracy) * 100)) : 100;
        $tackleSuccessPercentage = $teamAvgTackleSuccess > 0 ? min(100, round(($tackleSuccess / $teamAvgTackleSuccess) * 100)) : 100;
        $shotsPercentage = $teamAvgShots > 0 ? min(100, round(($avgShots / $teamAvgShots) * 100)) : 100;
        $passesPercentage = $teamAvgPasses > 0 ? min(100, round(($avgPasses / $teamAvgPasses) * 100)) : 100;

        return [
            [
                'name' => 'Précision passes',
                'display' => $passAccuracy . '%',
                'percentage' => $passAccuracyPercentage,
                'teamAvg' => $teamAvgPassAccuracy . '%',
                'leagueAvg' => round($teamAvgPassAccuracy * 0.95, 0) . '%'
            ],
            [
                'name' => 'Passes longues',
                'display' => round($passAccuracy * 0.88, 0) . '%',
                'percentage' => min(100, round($passAccuracy * 0.88 * 1.1)),
                'teamAvg' => '72%',
                'leagueAvg' => '68%'
            ],
            [
                'name' => 'Centres réussis',
                'display' => round($passAccuracy * 0.48, 0) . '%',
                'percentage' => min(100, round($passAccuracy * 0.48 * 1.2)),
                'teamAvg' => '39%',
                'leagueAvg' => '34%'
            ],
            [
                'name' => 'Contrôles',
                'display' => round($passAccuracy * 1.1, 0) . '%',
                'percentage' => min(100, round($passAccuracy * 1.1 * 1.05)),
                'teamAvg' => '90%',
                'leagueAvg' => '85%'
            ],
            [
                'name' => 'Touches/match',
                'display' => $avgPasses * 6.2,
                'percentage' => $passesPercentage,
                'teamAvg' => $teamAvgPasses * 5.4,
                'leagueAvg' => round($teamAvgPasses * 5.4 * 0.95, 0)
            ],
            [
                'name' => 'Passes clés',
                'display' => round($avgPasses * 0.13, 1) . '/match',
                'percentage' => min(100, round(($avgPasses * 0.13 / 3.2) * 100)),
                'teamAvg' => '3.2',
                'leagueAvg' => '2.8'
            ]
        ];
    }

    /**
     * Calcule l'évolution des performances sur les 6 derniers mois
     */
    public function getPerformanceEvolution()
    {
        if ($this->performances->isEmpty()) {
            return $this->getDefaultPerformanceEvolution();
        }

        $months = [];
        $ratings = [];
        $goals = [];
        $assists = [];

        // Grouper par mois
        $monthlyData = $this->performances->groupBy(function ($performance) {
            return Carbon::parse($performance->match_date)->format('M');
        });

        // Remplir les 6 derniers mois
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('M');
            $months[] = $month;
            
            if (isset($monthlyData[$month])) {
                $monthPerformances = $monthlyData[$month];
                $ratings[] = round($monthPerformances->avg('rating'), 1);
                $goals[] = $monthPerformances->sum('goals_scored');
                $assists[] = $monthPerformances->sum('assists');
            } else {
                $ratings[] = 0;
                $goals[] = 0;
                $assists[] = 0;
            }
        }

        return [
            'labels' => $months,
            'ratings' => $ratings,
            'goals' => $goals,
            'assists' => $assists
        ];
    }

    /**
     * Calcule le résumé de la saison
     */
    public function getSeasonSummary()
    {
        if ($this->performances->isEmpty()) {
            return $this->getDefaultSeasonSummary();
        }

        $totalGoals = $this->performances->sum('goals_scored');
        $totalAssists = $this->performances->sum('assists');
        $totalMatches = $this->performances->count();
        $avgRating = $this->performances->avg('rating');
        $totalDistance = $this->performances->sum('distance_covered');

        // Calcul de la tendance (comparaison avec le mois précédent)
        $currentMonth = Carbon::now()->month;
        $currentMonthPerformances = $this->performances->filter(function ($perf) use ($currentMonth) {
            return Carbon::parse($perf->match_date)->month === $currentMonth;
        });
        
        $previousMonth = $currentMonth - 1;
        if ($previousMonth < 1) $previousMonth = 12;
        $previousMonthPerformances = $this->performances->filter(function ($perf) use ($previousMonth) {
            return Carbon::parse($perf->match_date)->month === $previousMonth;
        });

        $currentGoals = $currentMonthPerformances->sum('goals_scored');
        $previousGoals = $previousMonthPerformances->sum('goals_scored');
        $goalsTrend = $currentGoals > $previousGoals ? '+' . ($currentGoals - $previousGoals) : ($currentGoals - $previousGoals);

        return [
            'goals' => [
                'total' => $totalGoals,
                'trend' => $goalsTrend,
                'avg' => $totalMatches > 0 ? round($totalGoals / $totalMatches, 1) : 0
            ],
            'assists' => [
                'total' => $totalAssists,
                'trend' => '+1',
                'avg' => $totalMatches > 0 ? round($totalAssists / $totalMatches, 1) : 0
            ],
            'matches' => [
                'total' => $totalMatches,
                'rating' => round($avgRating, 1),
                'distance' => round($totalDistance / 1000, 1) . ' km'
            ]
        ];
    }

    /**
     * Données par défaut si aucune performance
     */
    protected function getDefaultOffensiveStats()
    {
        return [
            ['name' => 'Buts', 'display' => '0', 'percentage' => 0, 'teamAvg' => '0', 'leagueAvg' => '0'],
            ['name' => 'Assists', 'display' => '0', 'percentage' => 0, 'teamAvg' => '0', 'leagueAvg' => '0'],
            ['name' => 'Tirs cadrés', 'display' => '0%', 'percentage' => 0, 'teamAvg' => '0%', 'leagueAvg' => '0%'],
            ['name' => 'Tirs/match', 'display' => '0', 'percentage' => 0, 'teamAvg' => '0', 'leagueAvg' => '0'],
            ['name' => 'Passes réussies', 'display' => '0%', 'percentage' => 0, 'teamAvg' => '0%', 'leagueAvg' => '0%'],
            ['name' => 'Passes/match', 'display' => '0', 'percentage' => 0, 'teamAvg' => '0', 'leagueAvg' => '0']
        ];
    }

    protected function getDefaultPhysicalStats()
    {
        return [
            ['name' => 'Distance/match', 'display' => '0 km', 'percentage' => 0, 'teamAvg' => '0 km', 'leagueAvg' => '0 km'],
            ['name' => 'Sprints', 'display' => '0/match', 'percentage' => 0, 'teamAvg' => '0', 'leagueAvg' => '0'],
            ['name' => 'Vitesse max', 'display' => '0 km/h', 'percentage' => 0, 'teamAvg' => '0 km/h', 'leagueAvg' => '0 km/h'],
            ['name' => 'Vitesse moy', 'display' => '0 km/h', 'percentage' => 0, 'teamAvg' => '0 km/h', 'leagueAvg' => '0 km/h'],
            ['name' => 'Intensité', 'display' => '0%', 'percentage' => 0, 'teamAvg' => '0%', 'leagueAvg' => '0%'],
            ['name' => 'Récupération', 'display' => '0/match', 'percentage' => 0, 'teamAvg' => '0', 'leagueAvg' => '0']
        ];
    }

    protected function getDefaultTechnicalStats()
    {
        return [
            ['name' => 'Précision passes', 'display' => '0%', 'percentage' => 0, 'teamAvg' => '0%', 'leagueAvg' => '0%'],
            ['name' => 'Passes longues', 'display' => '0%', 'percentage' => 0, 'teamAvg' => '0%', 'leagueAvg' => '0%'],
            ['name' => 'Centres réussis', 'display' => '0%', 'percentage' => 0, 'teamAvg' => '0%', 'leagueAvg' => '0%'],
            ['name' => 'Contrôles', 'display' => '0%', 'percentage' => 0, 'teamAvg' => '0%', 'leagueAvg' => '0%'],
            ['name' => 'Touches/match', 'display' => '0', 'percentage' => 0, 'teamAvg' => '0', 'leagueAvg' => '0'],
            ['name' => 'Passes clés', 'display' => '0/match', 'percentage' => 0, 'teamAvg' => '0', 'leagueAvg' => '0']
        ];
    }

    protected function getDefaultPerformanceEvolution()
    {
        return [
            'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            'ratings' => [0, 0, 0, 0, 0, 0],
            'goals' => [0, 0, 0, 0, 0, 0],
            'assists' => [0, 0, 0, 0, 0, 0]
        ];
    }

    protected function getDefaultSeasonSummary()
    {
        return [
            'goals' => ['total' => 0, 'trend' => '0', 'avg' => 0],
            'assists' => ['total' => 0, 'trend' => '0', 'avg' => 0],
            'matches' => ['total' => 0, 'rating' => 0, 'distance' => '0 km']
        ];
    }
}
