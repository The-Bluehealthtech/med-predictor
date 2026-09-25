<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameMatch;
use App\Models\User;
use App\Models\MatchOfficial;
use App\Models\Competition;
use Illuminate\Support\Facades\DB;

class AdminRefereeAssignmentController extends Controller
{
    /**
     * Afficher la page de gestion des assignations
     */
    public function index()
    {
        try {
            // Version simplifiée pour test
            $matchesToAssign = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])
                ->where('status', 'scheduled')
                ->orderBy('match_date')
                ->limit(5)
                ->get();

            $recentlyAssigned = collect(); // Version simplifiée
            $referees = User::where('role', 'referee')->limit(5)->get();

            return view('admin.referee-assignments', compact('matchesToAssign', 'recentlyAssigned', 'referees'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Assigner des arbitres à un match
     */
    public function assignReferees(Request $request)
    {
        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'main_referee' => 'required|exists:users,id',
            'assistant_referee_1' => 'required|exists:users,id',
            'assistant_referee_2' => 'required|exists:users,id',
            'fourth_official' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $match = GameMatch::findOrFail($request->match_id);

            // Supprimer les assignations existantes
            MatchOfficial::where('match_id', $match->id)->delete();

            // Créer les nouvelles assignations
            $assignments = [
                [
                    'match_id' => $match->id,
                    'user_id' => $request->main_referee,
                    'role' => 'main_referee',
                ],
                [
                    'match_id' => $match->id,
                    'user_id' => $request->assistant_referee_1,
                    'role' => 'assistant_referee_1',
                ],
                [
                    'match_id' => $match->id,
                    'user_id' => $request->assistant_referee_2,
                    'role' => 'assistant_referee_2',
                ],
            ];

            // Ajouter le 4ème arbitre si spécifié
            if ($request->fourth_official) {
                $assignments[] = [
                    'match_id' => $match->id,
                    'user_id' => $request->fourth_official,
                    'role' => 'fourth_official',
                ];
            }

            // Insérer toutes les assignations
            foreach ($assignments as $assignment) {
                MatchOfficial::create($assignment);
            }

            DB::commit();

            return redirect()->route('admin.referee-assignments')
                ->with('success', 'Arbitres assignés avec succès au match.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'assignation des arbitres: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les informations d'un match pour le modal
     */
    public function getMatchInfo($matchId)
    {
        $match = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])
            ->findOrFail($matchId);

        return response()->json([
            'home_team' => $match->homeTeam->name ?? 'TBD',
            'away_team' => $match->awayTeam->name ?? 'TBD',
            'date' => $match->match_date ? $match->match_date->format('D, M j, Y g:i A') : 'TBD',
            'venue' => $match->venue ?? 'TBD',
            'competition' => $match->competition->name ?? 'TBD',
        ]);
    }

    /**
     * Supprimer une assignation d'arbitre
     */
    public function removeAssignment(Request $request)
    {
        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'role' => 'required|in:main_referee,assistant_referee_1,assistant_referee_2,fourth_official',
        ]);

        try {
            MatchOfficial::where('match_id', $request->match_id)
                ->where('role', $request->role)
                ->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Afficher les statistiques des assignations
     */
    public function statistics()
    {
        $stats = [
            'total_matches' => GameMatch::whereHas('competition', function ($query) {
                $query->where('name', 'LIKE', '%Tunisien%');
            })->count(),
            
            'assigned_matches' => GameMatch::whereHas('competition', function ($query) {
                $query->where('name', 'LIKE', '%Tunisien%');
            })->whereHas('officials', function ($query) {
                $query->where('role', 'main_referee');
            })->count(),
            
            'unassigned_matches' => GameMatch::whereHas('competition', function ($query) {
                $query->where('name', 'LIKE', '%Tunisien%');
            })->whereDoesntHave('officials', function ($query) {
                $query->where('role', 'main_referee');
            })->count(),
            
            'total_referees' => User::where('role', 'referee')
                ->count(),
        ];

        return response()->json($stats);
    }
}
