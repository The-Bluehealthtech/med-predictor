<?php

namespace App\Services\V3;

use App\Models\V3\PerformanceMetric;
use App\Models\Player;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PerformanceAnalyticsService
{
    /**
     * Analyser les tendances de performance d'un joueur
     */
    public function analyzePlayerTrends(int $playerId, string $metricType, int $days = 30): array
    {
        try {
            $cacheKey = "v3:trends:{$playerId}:{$metricType}:{$days}";
            
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $metrics = PerformanceMetric::where('player_id', $playerId)
                ->where('metric_type', $metricType)
                ->where('timestamp', '>=', now()->subDays($days))
                ->orderBy('timestamp')
                ->get();

            if ($metrics->isEmpty()) {
                return $this->getEmptyTrendData();
            }

            $trendData = $this->calculateTrends($metrics);
            
            Cache::put($cacheKey, $trendData, 3600); // Cache 1 heure
            
            return $trendData;

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'analyse des tendances", [
                'player_id' => $playerId,
                'metric_type' => $metricType,
                'error' => $e->getMessage()
            ]);

            return $this->getEmptyTrendData();
        }
    }

    /**
     * Calculer les tendances à partir des métriques
     */
    private function calculateTrends($metrics): array
    {
        $values = $metrics->pluck('value')->toArray();
        $timestamps = $metrics->pluck('timestamp')->toArray();

        // Calcul de la tendance linéaire
        $trend = $this->calculateLinearTrend($values);
        
        // Calcul des statistiques descriptives
        $stats = $this->calculateDescriptiveStats($values);
        
        // Détection des anomalies
        $anomalies = $this->detectAnomalies($values);
        
        // Prévision à court terme
        $forecast = $this->generateShortTermForecast($values, $timestamps);

        return [
            'trend' => $trend,
            'statistics' => $stats,
            'anomalies' => $anomalies,
            'forecast' => $forecast,
            'data_points' => count($values),
            'period_days' => Carbon::parse($timestamps[0])->diffInDays(end($timestamps)),
            'last_update' => now(),
        ];
    }

    /**
     * Calculer la tendance linéaire
     */
    private function calculateLinearTrend(array $values): array
    {
        $n = count($values);
        if ($n < 2) {
            return ['slope' => 0, 'direction' => 'stable', 'strength' => 0];
        }

        $x = range(1, $n);
        $sumX = array_sum($x);
        $sumY = array_sum($values);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $values[$i];
            $sumX2 += $x[$i] * $x[$i];
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        
        $direction = $slope > 0.01 ? 'increasing' : ($slope < -0.01 ? 'decreasing' : 'stable');
        $strength = abs($slope);

        return [
            'slope' => round($slope, 4),
            'direction' => $direction,
            'strength' => round($strength, 4),
        ];
    }

    /**
     * Calculer les statistiques descriptives
     */
    private function calculateDescriptiveStats(array $values): array
    {
        $n = count($values);
        if ($n === 0) return [];

        $mean = array_sum($values) / $n;
        $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $values)) / $n;
        $stdDev = sqrt($variance);

        sort($values);
        $median = $n % 2 === 0 
            ? ($values[$n/2 - 1] + $values[$n/2]) / 2 
            : $values[floor($n/2)];

        return [
            'mean' => round($mean, 4),
            'median' => round($median, 4),
            'std_dev' => round($stdDev, 4),
            'min' => round(min($values), 4),
            'max' => round(max($values), 4),
            'range' => round(max($values) - min($values), 4),
        ];
    }

    /**
     * Détecter les anomalies
     */
    private function detectAnomalies(array $values): array
    {
        if (count($values) < 3) return [];

        $mean = array_sum($values) / count($values);
        $stdDev = sqrt(array_sum(array_map(fn($x) => pow($x - $mean, 2), $values)) / count($values));
        
        $threshold = 2.5; // Seuil pour détecter les anomalies
        $anomalies = [];

        foreach ($values as $index => $value) {
            $zScore = abs(($value - $mean) / $stdDev);
            if ($zScore > $threshold) {
                $anomalies[] = [
                    'index' => $index,
                    'value' => $value,
                    'z_score' => round($zScore, 4),
                    'deviation' => round($value - $mean, 4),
                ];
            }
        }

        return $anomalies;
    }

    /**
     * Générer une prévision à court terme
     */
    private function generateShortTermForecast(array $values, array $timestamps): array
    {
        if (count($values) < 3) return [];

        // Utiliser une moyenne mobile pondérée pour la prévision
        $weights = [0.5, 0.3, 0.2]; // Poids pour les 3 dernières valeurs
        $recentValues = array_slice($values, -3);
        
        $forecast = 0;
        for ($i = 0; $i < count($recentValues); $i++) {
            $forecast += $recentValues[$i] * $weights[$i];
        }

        // Calculer l'intervalle de confiance
        $stdDev = sqrt(array_sum(array_map(fn($x) => pow($x - $forecast, 2), $recentValues)) / count($recentValues));
        $confidenceInterval = 1.96 * $stdDev; // 95% d'intervalle de confiance

        return [
            'next_value' => round($forecast, 4),
            'confidence_interval' => round($confidenceInterval, 4),
            'lower_bound' => round($forecast - $confidenceInterval, 4),
            'upper_bound' => round($forecast + $confidenceInterval, 4),
            'method' => 'weighted_moving_average',
        ];
    }

    /**
     * Données de tendance vides
     */
    private function getEmptyTrendData(): array
    {
        return [
            'trend' => ['slope' => 0, 'direction' => 'stable', 'strength' => 0],
            'statistics' => [],
            'anomalies' => [],
            'forecast' => [],
            'data_points' => 0,
            'period_days' => 0,
            'last_update' => now(),
        ];
    }

    /**
     * Comparer les performances de deux joueurs
     */
    public function comparePlayers(int $player1Id, int $player2Id, string $metricType, int $days = 30): array
    {
        $player1Trends = $this->analyzePlayerTrends($player1Id, $metricType, $days);
        $player2Trends = $this->analyzePlayerTrends($player2Id, $metricType, $days);

        return [
            'player1' => [
                'id' => $player1Id,
                'trends' => $player1Trends,
            ],
            'player2' => [
                'id' => $player2Id,
                'trends' => $player2Trends,
            ],
            'comparison' => [
                'trend_difference' => $player1Trends['trend']['slope'] - $player2Trends['trend']['slope'],
                'performance_gap' => $player1Trends['statistics']['mean'] - $player2Trends['statistics']['mean'],
                'consistency_comparison' => $player1Trends['statistics']['std_dev'] - $player2Trends['statistics']['std_dev'],
            ],
        ];
    }
}
