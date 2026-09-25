<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicalModuleController extends Controller
{
    public function index(): View
    {
        $players = collect();
        $athletes = collect();
        
        // Try to get players if model exists
        try {
            if (class_exists('\App\Models\Player')) {
                $players = Player::with(['club'])->orderBy('first_name')->orderBy('last_name')->get();
            }
        } catch (\Exception $e) {
            // Player model might not exist or table is missing
        }
        
        // Try to get athletes if model exists
        try {
            if (class_exists('\App\Models\Athlete')) {
                $athletes = Athlete::with(['team'])->orderBy('name')->get();
            }
        } catch (\Exception $e) {
            // Athlete model might not exist or table is missing
        }
        
        // Combine players and athletes
        $allPlayers = $players->concat($athletes);
        
        return view('modules.medical.index', [
            'footballType' => 'association',
            'players' => $allPlayers
        ]);
    }
}


















