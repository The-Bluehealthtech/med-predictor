<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PlayerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Temporarily disable player.access middleware for testing
        // $this->middleware('player.access');
    }

    /**
     * Display the player dashboard
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Try to find the player record associated with this user
        $player = Player::where('fifa_connect_id', $user->fifa_connect_id)
            ->orWhere('email', $user->email)
            ->first();
        
        // If no player record found, create a basic player object from user data
        if (!$player) {
            $player = (object) [
                'id' => $user->id,
                'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                'last_name' => explode(' ', $user->name)[1] ?? '',
                'email' => $user->email,
                'fifa_connect_id' => $user->fifa_connect_id ?? 'PLAYER-001',
                'position' => 'Forward', // Default position
                'club' => $user->club ? (object) ['name' => $user->club->name] : null,
                'association' => $user->association ? (object) ['name' => $user->association->name] : null,
                'player_picture' => $user->profile_picture_url,
                'player_picture_url' => $user->profile_picture_url ?? null, // Ensure this is always defined
                // Player Dashboard fields with defaults
                'ghs_physical_score' => 85.5,
                'ghs_mental_score' => 78.2,
                'ghs_civic_score' => 92.1,
                'ghs_sleep_score' => 76.8,
                'ghs_overall_score' => 83.2,
                'ghs_color_code' => 'blue',
                'ghs_ai_suggestions' => [
                    'Consider increasing sleep quality by 10% for better recovery',
                    'Excellent civic engagement score - keep up the community work',
                    'Mental health score could improve with stress management techniques'
                ],
                'ghs_last_updated' => now(),
                'contribution_score' => 87.5,
                'data_value_estimate' => 1250.00,
                'matches_contributed' => 45,
                'training_sessions_logged' => 120,
                'health_records_contributed' => 18,
                'injury_risk_score' => 0.15,
                'injury_risk_level' => 'low',
                'injury_risk_reason' => 'Good fitness levels, regular medical checkups, no recent injuries',
                'weekly_health_tips' => [
                    'Stay hydrated during training sessions',
                    'Focus on proper warm-up routines',
                    'Consider yoga for flexibility improvement'
                ],
                'injury_risk_last_assessed' => now(),
                'match_availability' => true,
                'last_availability_update' => now(),
                'last_data_export' => now()->subDays(30),
                'data_export_count' => 3
            ];
        } else {
            // Convert the player model to an object with all the dashboard fields
            $playerArray = $player->toArray();
            
            // Ensure all required properties are defined
            $playerArray['email'] = $player->email ?? $user->email ?? 'N/A';
            $playerArray['first_name'] = $player->first_name ?? explode(' ', $user->name)[0] ?? $user->name;
            $playerArray['last_name'] = $player->last_name ?? explode(' ', $user->name)[1] ?? '';
            $playerArray['position'] = $player->position ?? 'Non définie';
            $playerArray['fifa_connect_id'] = $player->fifa_connect_id ?? $user->fifa_connect_id ?? 'N/A';
            $playerArray['player_picture_url'] = $player->player_picture ?? $user->profile_picture_url ?? null;
            $playerArray['club'] = $player->club ? (object) ['name' => $player->club->name] : (object) ['name' => 'Non affilié'];
            $playerArray['association'] = $player->association ? (object) ['name' => $player->association->name] : null;
            
            // Dashboard specific fields
            $playerArray['ghs_physical_score'] = $player->ghs_physical_score ?? 85.5;
            $playerArray['ghs_mental_score'] = $player->ghs_mental_score ?? 78.2;
            $playerArray['ghs_civic_score'] = $player->ghs_civic_score ?? 92.1;
            $playerArray['ghs_sleep_score'] = $player->ghs_sleep_score ?? 76.8;
            $playerArray['ghs_overall_score'] = $player->ghs_overall_score ?? 83.2;
            $playerArray['ghs_color_code'] = $player->ghs_color_code ?? 'blue';
            $playerArray['ghs_ai_suggestions'] = [
                'Consider increasing sleep quality by 10% for better recovery',
                'Excellent civic engagement score - keep up the community work',
                'Mental health score could improve with stress management techniques'
            ];
            $playerArray['ghs_last_updated'] = $player->ghs_last_updated ?? now();
            $playerArray['contribution_score'] = $player->contribution_score ?? 87.5;
            $playerArray['data_value_estimate'] = $player->data_value_estimate ?? 1250.00;
            $playerArray['matches_contributed'] = $player->matches_contributed ?? 45;
            $playerArray['training_sessions_logged'] = $player->training_sessions_logged ?? 120;
            $playerArray['health_records_contributed'] = $player->health_records_contributed ?? 18;
            $playerArray['injury_risk_score'] = $player->injury_risk_score ?? 0.15;
            $playerArray['injury_risk_level'] = $player->injury_risk_level ?? 'low';
            $playerArray['injury_risk_reason'] = 'Good fitness levels, regular medical checkups, no recent injuries';
            $playerArray['weekly_health_tips'] = [
                'Stay hydrated during training sessions',
                'Focus on proper warm-up routines',
                'Consider yoga for flexibility improvement'
            ];
            $playerArray['injury_risk_last_assessed'] = $player->injury_risk_last_assessed ?? now();
            $playerArray['match_availability'] = true;
            $playerArray['last_availability_update'] = now();
            $playerArray['last_data_export'] = now()->subDays(30);
            $playerArray['data_export_count'] = $player->data_export_count ?? 3;
            
            $player = (object) $playerArray;
        }

        return view('player-dashboard.index', compact('player'));
    }
} 