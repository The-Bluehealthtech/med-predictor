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
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $players = Player::with(['club', 'association'])->orderBy('first_name')->get();
        
        return view('admin.dashboard', compact('players'));
    }

    /**
     * Afficher la liste des joueurs pour la navigation
     */
    public function playersList(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $query = Player::with(['club', 'association']);

        // Recherche par nom, position, club ou nationalité
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('position', 'like', "%{$searchTerm}%")
                  ->orWhere('nationality', 'like', "%{$searchTerm}%")
                  ->orWhereHas('club', function($clubQuery) use ($searchTerm) {
                      $clubQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Filtre par position
        if ($request->filled('position')) {
            $query->where('position', $request->get('position'));
        }

        // Filtre par club
        if ($request->filled('club')) {
            $query->whereHas('club', function($clubQuery) use ($request) {
                $clubQuery->where('name', $request->get('club'));
            });
        }

        $players = $query->orderBy('first_name')->paginate(20);

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
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin'])) {
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
