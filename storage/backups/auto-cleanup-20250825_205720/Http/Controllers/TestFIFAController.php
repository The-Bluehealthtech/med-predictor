<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class TestFIFAController extends Controller
{
    /**
     * Test simple de l'API FIFA Connect
     */
    public function testAPI($playerId)
    {
        try {
            // Récupérer le joueur
            $player = Player::find($playerId);
            
            if (!$player) {
                return response()->json([
                    'error' => 'Joueur non trouvé',
                    'player_id' => $playerId
                ], 404);
            }
            
            // Données de test basées sur le joueur
            $testData = [
                'player_id' => $player->id,
                'first_name' => $player->first_name,
                'last_name' => $player->last_name,
                'overall_rating' => $player->overall_rating ?? 75,
                'position' => $player->position ?? 'midfielder',
                'age' => $player->age ?? 25,
                
                // Statistiques simulées basées sur le rating FIFA
                'goals_scored' => $this->calculateTestGoals($player->overall_rating ?? 75),
                'assists' => $this->calculateTestAssists($player->overall_rating ?? 75),
                'matches_played' => $this->calculateTestMatches($player->overall_rating ?? 75),
                'minutes_played' => $this->calculateTestMinutes($player->overall_rating ?? 75),
                
                // Ratings par catégorie
                'attacking_rating' => $this->calculateTestAttackingRating($player),
                'defending_rating' => $this->calculateTestDefendingRating($player),
                'physical_rating' => $this->calculateTestPhysicalRating($player),
                'technical_rating' => $this->calculateTestTechnicalRating($player),
                'mental_rating' => $this->calculateTestMentalRating($player),
                
                // Statistiques physiques
                'distance_covered' => $this->calculateTestDistance($player->overall_rating ?? 75),
                'max_speed' => $this->calculateTestSpeed($player->overall_rating ?? 75),
                'sprints_count' => $this->calculateTestSprints($player->overall_rating ?? 75),
                
                // Pourcentages
                'shot_accuracy' => $this->calculateTestAccuracy($player->overall_rating ?? 75),
                'pass_accuracy' => $this->calculateTestPassAccuracy($player->overall_rating ?? 75),
                'fitness_score' => $player->fitness_score ?? 80,
                'form_percentage' => $player->form_percentage ?? 75
            ];
            
            return response()->json([
                'message' => 'API FIFA Connect Test fonctionne !',
                'data' => $testData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du traitement',
                'message' => $e->getMessage(),
                'player_id' => $playerId
            ], 500);
        }
    }
    
    /**
     * Calculer des buts de test basés sur le rating FIFA
     */
    private function calculateTestGoals($overallRating)
    {
        $baseGoals = 0.3; // 0.3 buts par match en moyenne
        $ratingMultiplier = $overallRating / 75;
        $matches = 25; // Saison de 25 matchs
        
        return round($baseGoals * $ratingMultiplier * $matches);
    }
    
    /**
     * Calculer des passes de test basées sur le rating FIFA
     */
    private function calculateTestAssists($overallRating)
    {
        $baseAssists = 0.2; // 0.2 passes par match en moyenne
        $ratingMultiplier = $overallRating / 75;
        $matches = 25;
        
        return round($baseAssists * $ratingMultiplier * $matches);
    }
    
    /**
     * Calculer des matchs de test basés sur le rating FIFA
     */
    private function calculateTestMatches($overallRating)
    {
        $baseMatches = 25;
        $ratingAdjustment = ($overallRating - 75) / 10; // ±2 matchs selon le rating
        
        return max(15, min(35, round($baseMatches + $ratingAdjustment)));
    }
    
    /**
     * Calculer des minutes de test basées sur le rating FIFA
     */
    private function calculateTestMinutes($overallRating)
    {
        $matches = $this->calculateTestMatches($overallRating);
        $minutesPerMatch = 85; // 85 minutes par match en moyenne
        
        if ($overallRating >= 85) {
            $minutesPerMatch = 90; // Titulaire indiscutable
        } elseif ($overallRating >= 80) {
            $minutesPerMatch = 87; // Titulaire régulier
        } elseif ($overallRating >= 75) {
            $minutesPerMatch = 82; // Titulaire occasionnel
        }
        
        return round($matches * $minutesPerMatch);
    }
    
    /**
     * Calculer le rating d'attaque de test
     */
    private function calculateTestAttackingRating($player)
    {
        $baseRating = $player->overall_rating ?? 75;
        $position = strtolower($player->position ?? 'midfielder');
        
        $positionBonus = 0;
        if (in_array($position, ['striker', 'forward', 'attacker'])) {
            $positionBonus = 12;
        } elseif (in_array($position, ['winger', 'left wing', 'right wing'])) {
            $positionBonus = 8;
        }
        
        return min(99, max(50, round($baseRating + $positionBonus)));
    }
    
    /**
     * Calculer le rating de défense de test
     */
    private function calculateTestDefendingRating($player)
    {
        $baseRating = $player->overall_rating ?? 75;
        $position = strtolower($player->position ?? 'midfielder');
        
        $positionBonus = 0;
        if (in_array($position, ['defender', 'centre back', 'full back'])) {
            $positionBonus = 15;
        } elseif (in_array($position, ['defensive midfielder', 'holding midfielder'])) {
            $positionBonus = 10;
        }
        
        return min(99, max(50, round($baseRating + $positionBonus)));
    }
    
    /**
     * Calculer le rating physique de test
     */
    private function calculateTestPhysicalRating($player)
    {
        $baseRating = $player->overall_rating ?? 75;
        $age = $player->age ?? 25;
        
        $ageBonus = 0;
        if ($age <= 23) {
            $ageBonus = 8;
        } elseif ($age <= 26) {
            $ageBonus = 5;
        } elseif ($age >= 30) {
            $ageBonus = -3;
        }
        
        return min(99, max(50, round($baseRating + $ageBonus)));
    }
    
    /**
     * Calculer le rating technique de test
     */
    private function calculateTestTechnicalRating($player)
    {
        $baseRating = $player->overall_rating ?? 75;
        $skillMoves = $player->skill_moves ?? 3;
        $weakFoot = $player->weak_foot ?? 3;
        
        $skillBonus = ($skillMoves - 3) * 3;
        $weakFootBonus = ($weakFoot - 3) * 2;
        
        return min(99, max(50, round($baseRating + $skillBonus + $weakFootBonus)));
    }
    
    /**
     * Calculer le rating mental de test
     */
    private function calculateTestMentalRating($player)
    {
        $baseRating = $player->overall_rating ?? 75;
        $internationalRep = $player->international_reputation ?? 1;
        $age = $player->age ?? 25;
        
        $reputationBonus = ($internationalRep - 1) * 3;
        $experienceBonus = $age >= 28 ? 5 : 0;
        
        return min(99, max(50, round($baseRating + $reputationBonus + $experienceBonus)));
    }
    
    /**
     * Calculer la distance de test
     */
    private function calculateTestDistance($overallRating)
    {
        $baseDistance = 10.5; // km par match
        $ratingMultiplier = $overallRating / 75;
        $matches = 25;
        
        return round($baseDistance * $ratingMultiplier * $matches, 1);
    }
    
    /**
     * Calculer la vitesse de test
     */
    private function calculateTestSpeed($overallRating)
    {
        $baseSpeed = 30.0; // km/h
        $ratingMultiplier = $overallRating / 75;
        
        return round($baseSpeed * $ratingMultiplier, 1);
    }
    
    /**
     * Calculer les sprints de test
     */
    private function calculateTestSprints($overallRating)
    {
        $baseSprints = 2.5; // sprints par match
        $ratingMultiplier = $overallRating / 75;
        $matches = 25;
        
        return round($baseSprints * $ratingMultiplier * $matches);
    }
    
    /**
     * Calculer la précision de test
     */
    private function calculateTestAccuracy($overallRating)
    {
        $baseAccuracy = 65; // %
        $ratingBonus = ($overallRating - 75) * 0.5;
        
        return min(95, max(45, round($baseAccuracy + $ratingBonus)));
    }
    
    /**
     * Calculer la précision des passes de test
     */
    private function calculateTestPassAccuracy($overallRating)
    {
        $baseAccuracy = 80; // %
        $ratingBonus = ($overallRating - 75) * 0.4;
        
        return min(98, max(65, round($baseAccuracy + $ratingBonus)));
    }
}

