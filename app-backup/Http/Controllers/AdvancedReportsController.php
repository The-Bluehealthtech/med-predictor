<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\Player;
use App\Models\Club;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdvancedReportsController extends Controller
{
    /**
     * Afficher la page des rapports avancés
     */
    public function index(Request $request)
    {
        // Récupérer les données pour les filtres
        $competitions = Competition::where('status', 'active')->get();
        $clubs = Club::with('association')->get();
        $referees = User::where('role', 'referee')->get();
        $players = Player::with('club')->get();

        // Statistiques générales
        $stats = $this->getGeneralStats();

        return view('competitions.association.rapports-avances', compact(
            'competitions',
            'clubs', 
            'referees',
            'players',
            'stats'
        ));
    }

    /**
     * Générer un rapport de matchs
     */
    public function generateMatchReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:matches,players,referees,clubs,competitions',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'competition_id' => 'nullable|exists:competitions,id',
            'club_id' => 'nullable|exists:clubs,id',
            'referee_id' => 'nullable|exists:users,id',
            'player_id' => 'nullable|exists:players,id',
            'format' => 'required|in:pdf,excel,web'
        ]);

        try {
            $filters = $request->only([
                'date_from', 'date_to', 'competition_id', 
                'club_id', 'referee_id', 'player_id'
            ]);

            $data = $this->getMatchReportData($filters);
            
            if ($request->format === 'pdf') {
                return $this->generatePDFReport('match-report', $data, $filters);
            } elseif ($request->format === 'excel') {
                return $this->generateExcelReport('match-report', $data, $filters);
            } else {
                return view('reports.match-report', compact('data', 'filters'));
            }

        } catch (\Exception $e) {
            Log::error('Erreur génération rapport matchs', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return back()->with('error', 'Erreur lors de la génération du rapport');
        }
    }

    /**
     * Afficher le rapport de performance des joueurs (GET)
     */
    public function showPlayerPerformanceReport(Request $request)
    {
        try {
            $filters = $request->only([
                'date_from', 'date_to', 'competition_id', 
                'club_id', 'player_id', 'search'
            ]);

            $data = $this->getPlayerPerformanceData($filters, true);
            
            return view('reports.player-performance', [
                'players' => $data['players'],
                'filters_applied' => $data['filters_applied'],
                'search' => $request->get('search', ''),
                'clubs' => $data['clubs'] ?? collect()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur affichage rapport performance joueurs', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return back()->with('error', 'Erreur lors de l\'affichage du rapport');
        }
    }

    /**
     * Générer un rapport de performance des joueurs
     */
    public function generatePlayerPerformanceReport(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'competition_id' => 'nullable|exists:competitions,id',
            'club_id' => 'nullable|exists:clubs,id',
            'player_id' => 'nullable|exists:players,id',
            'format' => 'required|in:pdf,excel,web'
        ]);

        try {
            $filters = $request->only([
                'date_from', 'date_to', 'competition_id', 
                'club_id', 'player_id'
            ]);

            $data = $this->getPlayerPerformanceData($filters);
            
            if ($request->format === 'pdf') {
                return $this->generatePDFReport('player-performance', $data, $filters);
            } elseif ($request->format === 'excel') {
                return $this->generateExcelReport('player-performance', $data, $filters);
            } else {
                return view('reports.player-performance', [
                    'players' => $data['players'],
                    'filters_applied' => $data['filters_applied']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erreur génération rapport performance joueurs', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return back()->with('error', 'Erreur lors de la génération du rapport');
        }
    }

    /**
     * Obtenir les données du rapport de matchs
     */
    private function getMatchReportData(array $filters)
    {
        $query = GameMatch::with([
            'competition',
            'homeTeam',
            'awayTeam',
            'officials.user'
        ]);

        // Appliquer les filtres
        if (!empty($filters['date_from'])) {
            $query->where('match_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('match_date', '<=', $filters['date_to']);
        }
        if (!empty($filters['competition_id'])) {
            $query->where('competition_id', $filters['competition_id']);
        }
        if (!empty($filters['club_id'])) {
            $query->where(function($q) use ($filters) {
                $q->where('home_team_id', $filters['club_id'])
                  ->orWhere('away_team_id', $filters['club_id']);
            });
        }

        $matches = $query->orderBy('match_date', 'desc')->get();

        // Calculer les statistiques
        $stats = [
            'total_matches' => $matches->count(),
            'completed_matches' => $matches->where('status', 'completed')->count(),
            'total_goals' => $matches->sum(function($match) {
                return ($match->home_score ?? 0) + ($match->away_score ?? 0);
            }),
            'average_goals_per_match' => $matches->where('status', 'completed')->count() > 0 
                ? round($matches->sum(function($match) {
                    return ($match->home_score ?? 0) + ($match->away_score ?? 0);
                }) / $matches->where('status', 'completed')->count(), 2)
                : 0
        ];

        return [
            'matches' => $matches,
            'statistics' => $stats,
            'filters_applied' => $filters
        ];
    }

    /**
     * Obtenir les données de performance des joueurs
     */
    private function getPlayerPerformanceData(array $filters, $paginated = false)
    {
        $query = Player::with(['club']);

        // Filtres
        if (!empty($filters['club_id'])) {
            $query->where('club_id', $filters['club_id']);
        }
        if (!empty($filters['player_id'])) {
            $query->where('id', $filters['player_id']);
        }
        
        // Recherche par nom
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_email', 'LIKE', "%{$search}%")
                  ->orWhereHas('club', function($clubQuery) use ($search) {
                      $clubQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Tri par nom
        $query->orderBy('name', 'asc');

        if ($paginated) {
            // Pagination à 20 éléments
            $players = $query->paginate(20);
            
            // Calculer les performances pour chaque joueur
            $performanceData = $players->map(function($player) use ($filters) {
                return [
                    'player' => $player,
                    'matches_played' => rand(0, 25), // Données fictives pour la démo
                    'goals' => rand(0, 15),
                    'assists' => rand(0, 10),
                    'yellow_cards' => rand(0, 5),
                    'red_cards' => rand(0, 2),
                    'average_rating' => round(rand(60, 95) / 10, 1)
                ];
            });
            
            // Remplacer les données paginées
            $players->setCollection($performanceData);
            
            return [
                'players' => $players,
                'filters_applied' => $filters,
                'clubs' => Club::orderBy('name')->get()
            ];
        } else {
            // Version non paginée pour les exports
            $players = $query->get();
            
            $performanceData = $players->map(function($player) use ($filters) {
                return [
                    'player' => $player,
                    'matches_played' => 0,
                    'goals' => 0,
                    'assists' => 0,
                    'yellow_cards' => 0,
                    'red_cards' => 0,
                    'average_rating' => 0
                ];
            });

            return [
                'players' => $performanceData,
                'filters_applied' => $filters
            ];
        }
    }

    /**
     * Obtenir les statistiques générales
     */
    private function getGeneralStats()
    {
        return [
            'total_matches' => GameMatch::count(),
            'total_players' => Player::count(),
            'total_clubs' => Club::count(),
            'total_referees' => User::where('role', 'referee')->count(),
            'total_competitions' => Competition::count(),
            'matches_this_month' => GameMatch::whereMonth('match_date', now()->month)->count(),
            'matches_this_year' => GameMatch::whereYear('match_date', now()->year)->count()
        ];
    }

    /**
     * Générer un rapport PDF
     */
    private function generatePDFReport(string $type, array $data, array $filters)
    {
        // TODO: Implémenter la génération PDF
        return response()->json(['message' => 'PDF generation not implemented yet']);
    }

    /**
     * Générer un rapport Excel
     */
    private function generateExcelReport(string $type, array $data, array $filters)
    {
        // TODO: Implémenter la génération Excel
        return response()->json(['message' => 'Excel generation not implemented yet']);
    }
}
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdvancedReportsController extends Controller
{
    /**
     * Afficher la page des rapports avancés
     */
    public function index(Request $request)
    {
        // Récupérer les données pour les filtres
        $competitions = Competition::where('status', 'active')->get();
        $clubs = Club::with('association')->get();
        $referees = User::where('role', 'referee')->get();
        $players = Player::with('club')->get();

        // Statistiques générales
        $stats = $this->getGeneralStats();

        return view('competitions.association.rapports-avances', compact(
            'competitions',
            'clubs', 
            'referees',
            'players',
            'stats'
        ));
    }

    /**
     * Générer un rapport de matchs
     */
    public function generateMatchReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:matches,players,referees,clubs,competitions',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'competition_id' => 'nullable|exists:competitions,id',
            'club_id' => 'nullable|exists:clubs,id',
            'referee_id' => 'nullable|exists:users,id',
            'player_id' => 'nullable|exists:players,id',
            'format' => 'required|in:pdf,excel,web'
        ]);

        try {
            $filters = $request->only([
                'date_from', 'date_to', 'competition_id', 
                'club_id', 'referee_id', 'player_id'
            ]);

            $data = $this->getMatchReportData($filters);
            
            if ($request->format === 'pdf') {
                return $this->generatePDFReport('match-report', $data, $filters);
            } elseif ($request->format === 'excel') {
                return $this->generateExcelReport('match-report', $data, $filters);
            } else {
                return view('reports.match-report', compact('data', 'filters'));
            }

        } catch (\Exception $e) {
            Log::error('Erreur génération rapport matchs', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return back()->with('error', 'Erreur lors de la génération du rapport');
        }
    }

    /**
     * Afficher le rapport de performance des joueurs (GET)
     */
    public function showPlayerPerformanceReport(Request $request)
    {
        try {
            $filters = $request->only([
                'date_from', 'date_to', 'competition_id', 
                'club_id', 'player_id', 'search'
            ]);

            $data = $this->getPlayerPerformanceData($filters, true);
            
            return view('reports.player-performance', [
                'players' => $data['players'],
                'filters_applied' => $data['filters_applied'],
                'search' => $request->get('search', ''),
                'clubs' => $data['clubs'] ?? collect()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur affichage rapport performance joueurs', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return back()->with('error', 'Erreur lors de l\'affichage du rapport');
        }
    }

    /**
     * Générer un rapport de performance des joueurs
     */
    public function generatePlayerPerformanceReport(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'competition_id' => 'nullable|exists:competitions,id',
            'club_id' => 'nullable|exists:clubs,id',
            'player_id' => 'nullable|exists:players,id',
            'format' => 'required|in:pdf,excel,web'
        ]);

        try {
            $filters = $request->only([
                'date_from', 'date_to', 'competition_id', 
                'club_id', 'player_id'
            ]);

            $data = $this->getPlayerPerformanceData($filters);
            
            if ($request->format === 'pdf') {
                return $this->generatePDFReport('player-performance', $data, $filters);
            } elseif ($request->format === 'excel') {
                return $this->generateExcelReport('player-performance', $data, $filters);
            } else {
                return view('reports.player-performance', [
                    'players' => $data['players'],
                    'filters_applied' => $data['filters_applied']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erreur génération rapport performance joueurs', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return back()->with('error', 'Erreur lors de la génération du rapport');
        }
    }

    /**
     * Obtenir les données du rapport de matchs
     */
    private function getMatchReportData(array $filters)
    {
        $query = GameMatch::with([
            'competition',
            'homeTeam',
            'awayTeam',
            'officials.user'
        ]);

        // Appliquer les filtres
        if (!empty($filters['date_from'])) {
            $query->where('match_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('match_date', '<=', $filters['date_to']);
        }
        if (!empty($filters['competition_id'])) {
            $query->where('competition_id', $filters['competition_id']);
        }
        if (!empty($filters['club_id'])) {
            $query->where(function($q) use ($filters) {
                $q->where('home_team_id', $filters['club_id'])
                  ->orWhere('away_team_id', $filters['club_id']);
            });
        }

        $matches = $query->orderBy('match_date', 'desc')->get();

        // Calculer les statistiques
        $stats = [
            'total_matches' => $matches->count(),
            'completed_matches' => $matches->where('status', 'completed')->count(),
            'total_goals' => $matches->sum(function($match) {
                return ($match->home_score ?? 0) + ($match->away_score ?? 0);
            }),
            'average_goals_per_match' => $matches->where('status', 'completed')->count() > 0 
                ? round($matches->sum(function($match) {
                    return ($match->home_score ?? 0) + ($match->away_score ?? 0);
                }) / $matches->where('status', 'completed')->count(), 2)
                : 0
        ];

        return [
            'matches' => $matches,
            'statistics' => $stats,
            'filters_applied' => $filters
        ];
    }

    /**
     * Obtenir les données de performance des joueurs
     */
    private function getPlayerPerformanceData(array $filters, $paginated = false)
    {
        $query = Player::with(['club']);

        // Filtres
        if (!empty($filters['club_id'])) {
            $query->where('club_id', $filters['club_id']);
        }
        if (!empty($filters['player_id'])) {
            $query->where('id', $filters['player_id']);
        }
        
        // Recherche par nom
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_email', 'LIKE', "%{$search}%")
                  ->orWhereHas('club', function($clubQuery) use ($search) {
                      $clubQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Tri par nom
        $query->orderBy('name', 'asc');

        if ($paginated) {
            // Pagination à 20 éléments
            $players = $query->paginate(20);
            
            // Calculer les performances pour chaque joueur
            $performanceData = $players->map(function($player) use ($filters) {
                return [
                    'player' => $player,
                    'matches_played' => rand(0, 25), // Données fictives pour la démo
                    'goals' => rand(0, 15),
                    'assists' => rand(0, 10),
                    'yellow_cards' => rand(0, 5),
                    'red_cards' => rand(0, 2),
                    'average_rating' => round(rand(60, 95) / 10, 1)
                ];
            });
            
            // Remplacer les données paginées
            $players->setCollection($performanceData);
            
            return [
                'players' => $players,
                'filters_applied' => $filters,
                'clubs' => Club::orderBy('name')->get()
            ];
        } else {
            // Version non paginée pour les exports
            $players = $query->get();
            
            $performanceData = $players->map(function($player) use ($filters) {
                return [
                    'player' => $player,
                    'matches_played' => 0,
                    'goals' => 0,
                    'assists' => 0,
                    'yellow_cards' => 0,
                    'red_cards' => 0,
                    'average_rating' => 0
                ];
            });

            return [
                'players' => $performanceData,
                'filters_applied' => $filters
            ];
        }
    }

    /**
     * Obtenir les statistiques générales
     */
    private function getGeneralStats()
    {
        return [
            'total_matches' => GameMatch::count(),
            'total_players' => Player::count(),
            'total_clubs' => Club::count(),
            'total_referees' => User::where('role', 'referee')->count(),
            'total_competitions' => Competition::count(),
            'matches_this_month' => GameMatch::whereMonth('match_date', now()->month)->count(),
            'matches_this_year' => GameMatch::whereYear('match_date', now()->year)->count()
        ];
    }

    /**
     * Générer un rapport PDF
     */
    private function generatePDFReport(string $type, array $data, array $filters)
    {
        // TODO: Implémenter la génération PDF
        return response()->json(['message' => 'PDF generation not implemented yet']);
    }
    /**
     * Générer un rapport Excel
     */
    private function generateExcelReport(string $type, array $data, array $filters)
    {
        // TODO: Implémenter la génération Excel
        return response()->json(['message' => 'Excel generation not implemented yet']);
    }
}
