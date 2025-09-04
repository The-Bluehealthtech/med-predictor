<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\HealthRecord;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FitDashboardController extends Controller
{
    public function kpis()
    {
        // Get basic counts
        $totalPlayers = Player::count();
        $activePlayers = Player::where('status', 'active')->count();
        $totalHealthRecords = HealthRecord::count();
        
        // Calculate injury rate
        $injuredPlayers = HealthRecord::where('status', 'injured')->distinct('player_id')->count('player_id');
        $injuryRate = $totalPlayers > 0 ? round(($injuredPlayers / $totalPlayers) * 100, 1) : 0;
        
        // Calculate average fitness score (using risk_score as fitness indicator)
        $avgFitnessScore = HealthRecord::whereNotNull('risk_score')->avg('risk_score') ?? 0;
        $avgFitnessScore = round($avgFitnessScore * 100, 1); // Convert to percentage
        
        // Players at risk (high risk score)
        $playersAtRisk = HealthRecord::where('risk_score', '>', 0.7)->distinct('player_id')->count('player_id');
        
        // Top clubs by active players
        $topClubs = Club::withCount(['players as active_players_count' => function($query) {
            $query->where('status', 'active');
        }])
        ->orderByDesc('active_players_count')
        ->take(5)
        ->get(['name', 'active_players_count']);
        
        // Recent alerts (health records with issues)
        $recentAlerts = HealthRecord::whereIn('status', ['injured', 'unfit', 'pending'])
            ->orderByDesc('updated_at')
            ->take(5)
            ->count();
        
        // Health record compliance
        $playersWithHealthRecords = HealthRecord::distinct('player_id')->count('player_id');
        $compliance = $totalPlayers > 0 ? round(($playersWithHealthRecords / $totalPlayers) * 100, 1) : 0;
        
        return response()->json([
            'active_players' => $activePlayers,
            'injury_rate' => $injuryRate,
            'avg_fitness_score' => $avgFitnessScore,
            'players_at_risk' => $playersAtRisk,
            'top_clubs' => $topClubs,
            'recent_alerts' => $recentAlerts,
            'compliance' => $compliance,
        ]);
    }
} 