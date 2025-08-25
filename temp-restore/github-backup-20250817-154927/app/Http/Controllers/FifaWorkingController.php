<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FifaWorkingController extends Controller
{
    /**
     * Affiche le dashboard FIFA Working avec les données de Lionel Messi
     */
    public function index()
    {
        // Créer un objet joueur avec les données de Lionel Messi
        $player = (object) [
            'id' => 1,
            'first_name' => 'Lionel',
            'last_name' => 'Messi',
            'name' => 'Lionel Messi',
            'position' => 'RW',
            'age' => 37,
            'height' => 170,
            'weight' => 72,
            'preferred_foot' => 'Gauche',
            'overall_rating' => 93,
            'potential_rating' => 82,
            'club' => (object) [
                'name' => 'Chelsea FC',
                'logo_url' => 'https://logos-world.net/wp-content/uploads/2020/06/Paris-Saint-Germain-PSG-Logo.png'
            ],
            'association' => (object) [
                'name' => 'The Football Association',
                'flag_url' => 'https://flagcdn.com/w40/ar.png'
            ],
            'ghs_overall_score' => 85,
            'ghs_physical_score' => 88,
            'ghs_mental_score' => 92,
            'ghs_civic_score' => 90,
            'ghs_sleep_score' => 87
        ];
        
        // Données de performance
        $performanceData = [
            'seasonSummary' => [
                'goals' => ['total' => 12, 'trend' => '+3', 'average' => '0.43', 'accuracy' => '78%'],
                'assists' => ['total' => 8, 'trend' => '+2', 'accuracy' => '85%', 'keyPasses' => 28],
                'matches' => ['total' => 28, 'rating' => '8.5', 'distance' => '245 km', 'avgSpeed' => '12.3'],
                'minutes' => ['total' => 2520, 'average' => '90', 'availability' => '95%', 'fatigue' => '15%']
            ],
            'recentMatches' => [
                ['result' => 'W', 'score' => '3-1', 'rating' => 8.5],
                ['result' => 'W', 'score' => '2-0', 'rating' => 8.8],
                ['result' => 'D', 'score' => '1-1', 'rating' => 7.5],
                ['result' => 'W', 'score' => '4-2', 'rating' => 9.0],
                ['result' => 'W', 'score' => '2-1', 'rating' => 8.2]
            ]
        ];
        
        // Données de santé et forme
        $healthData = [
            'injuryRisk' => '15% - FAIBLE',
            'marketValue' => '€180M',
            'marketTrend' => '+€15M ce mois',
            'availability' => '✅ DISPONIBLE',
            'nextMatch' => 'Dimanche',
            'fitness' => '85%',
            'morale' => '92%',
            'seasonProgress' => '75%',
            'matchesPlayed' => 28,
            'matchesRemaining' => 10
        ];
        
        return view('player-portal.fifa-working', compact('player', 'performanceData', 'healthData'));
    }
}
