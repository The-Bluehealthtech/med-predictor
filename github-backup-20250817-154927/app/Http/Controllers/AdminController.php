<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Afficher le tableau de bord administrateur
     */
    public function dashboard()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['system_admin', 'association_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $players = Player::with(['club', 'association'])->orderBy('first_name')->get();
        
        return view('admin.dashboard', compact('players'));
    }

    /**
     * Afficher la liste des joueurs pour la navigation
     */
    public function playersList()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['system_admin', 'association_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $players = Player::with(['club', 'association'])
            ->orderBy('first_name')
            ->paginate(20);

        return view('admin.dashboard', compact('players'));
    }

    /**
     * Rechercher des joueurs
     */
    public function searchPlayers(Request $request)
    {
        $query = $request->get('search');
        
        if (empty($query)) {
            // Si aucun terme de recherche, retourner tous les joueurs pour la navigation
            $players = Player::with(['club', 'association'])
                ->orderBy('first_name')
                ->get();
        } else {
            // Recherche avec filtres
            $players = Player::with(['club', 'association'])
                ->where(function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%")
                      ->orWhere('position', 'like', "%{$query}%");
                })
                ->orWhereHas('club', function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->orderBy('first_name')
                ->limit(10)
                ->get();
        }

        return response()->json(['players' => $players]);
    }

    /**
     * Statistiques du système
     */
    public function systemStats()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['system_admin', 'association_admin'])) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $stats = [
            'total_players' => Player::count(),
            'players_with_club' => Player::whereNotNull('club_id')->count(),
            'players_with_association' => Player::whereNotNull('association_id')->count(),
            'recent_players' => Player::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return response()->json($stats);
    }
}
