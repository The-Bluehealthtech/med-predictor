<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MatchPerformance;
use App\Models\MatchMetric;

class MatchMetricsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🌱 Seeding MatchMetrics...\n";
        
        // Récupérer tous les match performances existants
        $matchPerformances = MatchPerformance::all();
        
        if ($matchPerformances->isEmpty()) {
            echo "⚠️ Aucun match performance trouvé. Création de données de test...\n";
            return;
        }
        
        foreach ($matchPerformances as $matchPerformance) {
            // Créer des métriques réalistes basées sur la position du joueur
            $position = $matchPerformance->player->position ?? 'FW';
            
            $metrics = $this->generateRealisticMetrics($position, $matchPerformance->rating ?? 6.0);
            
            MatchMetric::create([
                'match_performance_id' => $matchPerformance->id,
                ...$metrics
            ]);
        }
        
        echo "✅ " . $matchPerformances->count() . " métriques créées avec succès !\n";
    }
    
    private function generateRealisticMetrics(string $position, float $rating): array
    {
        // Multiplicateur basé sur le rating
        $ratingMultiplier = $rating / 10.0;
        
        // Métriques d'attaque (plus élevées pour les attaquants)
        $attackMultiplier = in_array($position, ['FW', 'ST', 'LW', 'RW']) ? 1.5 : 1.0;
        
        $shots_on_target = max(0, min(10, round(rand(2, 8) * $ratingMultiplier * $attackMultiplier)));
        $total_shots = $shots_on_target + rand(0, 5);
        $shot_accuracy = $total_shots > 0 ? round(($shots_on_target / $total_shots) * 100, 1) : 0;
        
        $key_passes = max(0, min(8, round(rand(1, 6) * $ratingMultiplier)));
        $successful_crosses = max(0, min(6, round(rand(0, 4) * $ratingMultiplier)));
        $successful_dribbles = max(0, min(8, round(rand(2, 6) * $ratingMultiplier * $attackMultiplier)));
        
        // Métriques physiques
        $distance = round(rand(8, 12) + ($ratingMultiplier * 2), 1); // 8-14 km
        $max_speed = round(rand(28, 34) + ($ratingMultiplier * 2), 1); // 28-36 km/h
        $avg_speed = round(rand(7, 11) + ($ratingMultiplier * 1), 1); // 7-12 km/h
        
        $sprints = max(0, min(30, round(rand(12, 22) + ($ratingMultiplier * 8))));
        $accelerations = max(0, min(40, round(rand(18, 28) + ($ratingMultiplier * 10))));
        $decelerations = max(0, min(30, round(rand(12, 20) + ($ratingMultiplier * 8))));
        $direction_changes = max(0, min(60, round(rand(25, 40) + ($ratingMultiplier * 15))));
        $jumps = max(0, min(20, round(rand(3, 12) + ($ratingMultiplier * 5))));
        
        // Métriques techniques
        $pass_accuracy = max(60, min(95, round(rand(70, 85) + ($ratingMultiplier * 10))));
        $long_passes = max(0, min(8, round(rand(1, 5) * $ratingMultiplier)));
        $crosses = max(0, min(6, round(rand(0, 4) * $ratingMultiplier)));
        
        // Métriques défensives (plus élevées pour les défenseurs)
        $defenseMultiplier = in_array($position, ['DF', 'CB', 'LB', 'RB', 'GK']) ? 1.5 : 1.0;
        
        $tackles = max(0, min(12, round(rand(2, 8) * $ratingMultiplier * $defenseMultiplier)));
        $interceptions = max(0, min(10, round(rand(1, 6) * $ratingMultiplier * $defenseMultiplier)));
        $clearances = max(0, min(8, round(rand(1, 5) * $ratingMultiplier * $defenseMultiplier)));
        
        return [
            'shots_on_target' => $shots_on_target,
            'total_shots' => $total_shots,
            'shot_accuracy' => $shot_accuracy,
            'key_passes' => $key_passes,
            'successful_crosses' => $successful_crosses,
            'successful_dribbles' => $successful_dribbles,
            'distance' => $distance,
            'max_speed' => $max_speed,
            'avg_speed' => $avg_speed,
            'sprints' => $sprints,
            'accelerations' => $accelerations,
            'decelerations' => $decelerations,
            'direction_changes' => $direction_changes,
            'jumps' => $jumps,
            'pass_accuracy' => $pass_accuracy,
            'long_passes' => $long_passes,
            'crosses' => $crosses,
            'tackles' => $tackles,
            'interceptions' => $interceptions,
            'clearances' => $clearances
        ];
    }
}
