<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use App\Traits\Searchable;

class AdminController extends Controller
{
    use Searchable;
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

        // Définition des champs de recherche
        $searchFields = [
            [
                'name' => 'name',
                'label' => 'Nom du joueur',
                'type' => 'text',
                'placeholder' => 'Rechercher par nom...',
                'columns' => ['first_name', 'last_name'],
                'mode' => 'contains'
            ],
            [
                'name' => 'position',
                'label' => 'Position',
                'type' => 'select',
                'column' => 'position',
                'options' => [
                    'Goalkeeper' => 'Gardien',
                    'Defender' => 'Défenseur',
                    'Midfielder' => 'Milieu',
                    'Forward' => 'Attaquant'
                ]
            ],
            [
                'name' => 'club_id',
                'label' => 'Club',
                'type' => 'select',
                'column' => 'club_id',
                'options' => \App\Models\Club::pluck('name', 'id')->toArray()
            ],
            [
                'name' => 'nationality',
                'label' => 'Nationalité',
                'type' => 'text',
                'placeholder' => 'Rechercher par nationalité...',
                'column' => 'nationality',
                'mode' => 'contains'
            ],
            [
                'name' => 'birth_date',
                'label' => 'Date de naissance',
                'type' => 'date_range',
                'column' => 'birth_date'
            ],
            [
                'name' => 'status',
                'label' => 'Statut',
                'type' => 'select',
                'column' => 'status',
                'options' => [
                    'active' => 'Actif',
                    'inactive' => 'Inactif',
                    'suspended' => 'Suspendu',
                    'injured' => 'Blessé'
                ]
            ]
        ];

        // Construction de la requête
        $query = Player::with(['club', 'association']);

        // Application des filtres de recherche
        $this->applySearchFilters($query, $request, $searchFields);

        // Tri par défaut
        $sortBy = $request->get('sort_by', 'first_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $players = $this->applyPagination($query, $request, 20);

        // Préparation des données pour le composant de recherche
        $searchData = $this->prepareSearchData($players, $searchFields, $request);

        return view('modules.players.index', compact('players', 'searchFields') + $searchData);
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
