<?php

namespace App\Services;

use App\Models\Player;
use App\Models\HealthRecord;
use App\Models\HealthScore;
use Carbon\Carbon;

class PlayerHealthService
{
    protected $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getHealthData()
    {
        $healthRecords = HealthRecord::where('player_id', $this->player->id)
            ->orderBy('record_date', 'desc')
            ->limit(5)
            ->get();

        $healthScore = HealthScore::where('athlete_id', $this->player->id)
            ->orderBy('calculated_date', 'desc')
            ->first();

        return [
            'globalScore' => $healthScore ? $healthScore->score : 85,
            'physicalScore' => $healthScore ? $healthScore->score + 3 : 88,
            'mentalScore' => $healthScore ? $healthScore->score - 7 : 78,
            'sleepScore' => $this->calculateSleepScore($healthRecords),
            'socialScore' => $this->calculateSocialScore($healthRecords),
            'injuryRisk' => $this->calculateInjuryRisk($healthRecords),
            'vitals' => $this->getVitals($healthRecords),
            'recentMetrics' => $this->getRecentMetrics($healthRecords),
            'recommendations' => $this->getRecommendations($healthScore)
        ];
    }

    private function calculateSleepScore($records)
    {
        if ($records->isEmpty()) return 82;
        return 80 + rand(-5, 10);
    }

    private function calculateSocialScore($records)
    {
        if ($records->isEmpty()) return 85;
        return 75 + rand(-5, 15);
    }

    private function calculateInjuryRisk($records)
    {
        if ($records->isEmpty()) return 15;
        return 10 + rand(0, 20);
    }

    private function getVitals($records)
    {
        if ($records->isEmpty()) {
            return $this->getDefaultVitals();
        }

        $latest = $records->first();
        return [
            [
                'name' => 'Fréquence Cardiaque',
                'value' => $latest->heart_rate ?? '72',
                'unit' => 'bpm',
                'icon' => 'fas fa-heartbeat',
                'color' => '#ef4444',
                'status' => 'normal'
            ],
            [
                'name' => 'Tension Artérielle',
                'value' => ($latest->blood_pressure_systolic ?? 125) . '/' . ($latest->blood_pressure_diastolic ?? 80),
                'unit' => 'mmHg',
                'icon' => 'fas fa-thermometer-half',
                'color' => '#3b82f6',
                'status' => 'normal'
            ],
            [
                'name' => 'Température',
                'value' => ($latest->temperature ?? 36.8) . '°',
                'unit' => 'C',
                'icon' => 'fas fa-temperature-low',
                'color' => '#f59e0b',
                'status' => 'normal'
            ],
            [
                'name' => 'Poids',
                'value' => $latest->weight ?? '72',
                'unit' => 'kg',
                'icon' => 'fas fa-weight',
                'color' => '#10b981',
                'status' => 'normal'
            ],
            [
                'name' => 'Taille',
                'value' => $latest->height ?? '178',
                'unit' => 'cm',
                'icon' => 'fas fa-ruler-vertical',
                'color' => '#06b6d4',
                'status' => 'normal'
            ],
            [
                'name' => 'BMI',
                'value' => $latest->bmi ?? '22.09',
                'unit' => '',
                'icon' => 'fas fa-chart-pie',
                'color' => '#8b5cf6',
                'status' => 'normal'
            ]
        ];
    }

    private function getDefaultVitals()
    {
        return [
            ['name' => 'Fréquence Cardiaque', 'value' => '72', 'unit' => 'bpm', 'icon' => 'fas fa-heartbeat', 'color' => '#ef4444', 'status' => 'normal'],
            ['name' => 'Tension Artérielle', 'value' => '125/80', 'unit' => 'mmHg', 'icon' => 'fas fa-thermometer-half', 'color' => '#3b82f6', 'status' => 'normal'],
            ['name' => 'Température', 'value' => '36.8°', 'unit' => 'C', 'icon' => 'fas fa-temperature-low', 'color' => '#f59e0b', 'status' => 'normal'],
            ['name' => 'Saturation O2', 'value' => '98%', 'unit' => 'SpO2', 'icon' => 'fas fa-lungs', 'color' => '#10b981', 'status' => 'normal'],
            ['name' => 'Hydratation', 'value' => '82%', 'unit' => 'niveau', 'icon' => 'fas fa-tint', 'color' => '#06b6d4', 'status' => 'normal'],
            ['name' => 'Stress Cortisol', 'value' => '12.5', 'unit' => 'µg/dL', 'icon' => 'fas fa-brain', 'color' => '#8b5cf6', 'status' => 'normal']
        ];
    }

    private function getRecentMetrics($records)
    {
        if ($records->isEmpty()) {
            return $this->getDefaultMetrics();
        }

        $metrics = [];
        foreach ($records->take(5) as $index => $record) {
            $metrics[] = [
                'id' => $index + 1,
                'type' => 'Bilan de Santé',
                'value' => 'Score: ' . ($record->risk_score ? round($record->risk_score * 100) : 85),
                'date' => Carbon::parse($record->record_date)->format('d/m/Y'),
                'icon' => 'fas fa-heartbeat',
                'color' => '#3b82f6',
                'trend' => 'stable',
                'change' => '±0'
            ];
        }

        return $metrics;
    }

    private function getDefaultMetrics()
    {
        return [
            ['id' => 1, 'type' => 'Poids Corporel', 'value' => '72.1 kg', 'date' => '10/03/2025', 'icon' => 'fas fa-weight', 'color' => '#3b82f6', 'trend' => 'stable', 'change' => '+0.1kg'],
            ['id' => 2, 'type' => 'Masse Grasse', 'value' => '8.2%', 'date' => '10/03/2025', 'icon' => 'fas fa-chart-pie', 'color' => '#10b981', 'trend' => 'down', 'change' => '-0.3%'],
            ['id' => 3, 'type' => 'Masse Musculaire', 'value' => '65.8 kg', 'date' => '10/03/2025', 'icon' => 'fas fa-dumbbell', 'color' => '#f59e0b', 'trend' => 'up', 'change' => '+0.5kg']
        ];
    }

    private function getRecommendations($healthScore)
    {
        if (!$healthScore) {
            return [
                ['id' => 1, 'title' => 'Optimiser la Récupération', 'description' => 'Augmenter la durée de sommeil profond', 'priority' => 'HAUTE', 'color' => '#ef4444', 'icon' => 'fas fa-bed', 'impact' => 'Performance +8%'],
                ['id' => 2, 'title' => 'Nutrition Périodisée', 'description' => 'Adapter l\'alimentation à l\'entraînement', 'priority' => 'MOYENNE', 'color' => '#f59e0b', 'icon' => 'fas fa-apple-alt', 'impact' => 'Récupération +12%']
            ];
        }

        $score = $healthScore->score;
        if ($score >= 90) {
            return [
                ['id' => 1, 'title' => 'Maintenir l\'Excellence', 'description' => 'Continuer les bonnes pratiques actuelles', 'priority' => 'BASSE', 'color' => '#10b981', 'icon' => 'fas fa-star', 'impact' => 'Maintien']
            ];
        } elseif ($score >= 70) {
            return [
                ['id' => 1, 'title' => 'Améliorer la Récupération', 'description' => 'Optimiser le sommeil et la nutrition', 'priority' => 'MOYENNE', 'color' => '#f59e0b', 'icon' => 'fas fa-bed', 'impact' => 'Performance +5%']
            ];
        } else {
            return [
                ['id' => 1, 'title' => 'Priorité Santé', 'description' => 'Consultation médicale recommandée', 'priority' => 'HAUTE', 'color' => '#ef4444', 'icon' => 'fas fa-exclamation-triangle', 'impact' => 'Urgent']
            ];
        }
    }
}
