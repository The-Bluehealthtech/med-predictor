<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\User;
use App\Models\Club;
use App\Models\MatchOfficial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RefereeAssignmentController extends Controller
{
    /**
     * Afficher la page de désignation des arbitres
     */
    public function index(Request $request)
    {
        $competitionId = $request->get('competition_id');
        $dateFrom = $request->get('date_from', now()->startOfWeek());
        $dateTo = $request->get('date_to', now()->endOfWeek());

        // Récupérer les matchs sans arbitres assignés
        $matches = GameMatch::with(['competition', 'homeTeam', 'awayTeam'])
            ->when($competitionId, function ($query, $competitionId) {
                return $query->where('competition_id', $competitionId);
            })
            ->whereBetween('match_date', [$dateFrom, $dateTo])
            ->where('status', 'scheduled')
            ->orderBy('match_date')
            ->get();

        // Récupérer tous les arbitres disponibles
        $referees = User::where('role', 'referee')
            ->where('status', 'active')
            ->with(['matchOfficials' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereHas('match', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('match_date', [$dateFrom, $dateTo]);
                });
            }])
            ->get();

        // Récupérer les compétitions pour le filtre
        $competitions = \App\Models\Competition::where('status', 'active')->get();

        return view('competitions.association.designation-arbitres', compact(
            'matches', 
            'referees', 
            'competitions',
            'competitionId',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Assigner un arbitre à un match
     */
    public function assignReferee(Request $request)
    {
        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'referee_id' => 'required|exists:users,id',
            'role' => 'required|in:referee,assistant_referee_1,assistant_referee_2,fourth_official,var_referee'
        ]);

        try {
            DB::beginTransaction();

            $match = GameMatch::findOrFail($request->match_id);
            $referee = User::findOrFail($request->referee_id);

            // Vérifier les antécédents avec les clubs
            $conflictCheck = $this->checkRefereeConflicts($referee, $match);
            if (!$conflictCheck['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $conflictCheck['message']
                ], 400);
            }

            // Vérifier la disponibilité
            $availabilityCheck = $this->checkRefereeAvailability($referee, $match);
            if (!$availabilityCheck['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $availabilityCheck['message']
                ], 400);
            }

            // Créer l'enregistrement dans match_officials
            MatchOfficial::updateOrCreate(
                [
                    'match_id' => $match->id,
                    'role' => $request->role
                ],
                [
                    'user_id' => $referee->id
                ]
            );

            DB::commit();

            Log::info('Arbitre assigné avec succès', [
                'match_id' => $match->id,
                'referee_id' => $referee->id,
                'role' => $request->role,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Arbitre assigné avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de l\'assignation d\'arbitre', [
                'error' => $e->getMessage(),
                'match_id' => $request->match_id,
                'referee_id' => $request->referee_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'assignation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier les conflits d'arbitre avec les clubs
     */
    private function checkRefereeConflicts($referee, $match)
    {
        // Vérifier si l'arbitre a des liens avec les clubs participants
        $homeClubId = $match->homeTeam->club_id ?? null;
        $awayClubId = $match->awayTeam->club_id ?? null;

        if ($homeClubId && $referee->club_id == $homeClubId) {
            return [
                'valid' => false,
                'message' => 'L\'arbitre ne peut pas arbitrer un match de son propre club'
            ];
        }

        if ($awayClubId && $referee->club_id == $awayClubId) {
            return [
                'valid' => false,
                'message' => 'L\'arbitre ne peut pas arbitrer un match de son propre club'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Vérifier la disponibilité de l'arbitre
     */
    private function checkRefereeAvailability($referee, $match)
    {
        $matchDate = Carbon::parse($match->match_date);
        $matchStart = $matchDate->copy()->subHours(2);
        $matchEnd = $matchDate->copy()->addHours(3);

        // Vérifier s'il y a des conflits d'horaires
        $conflictingAssignments = MatchOfficial::where('user_id', $referee->id)
            ->whereHas('match', function ($query) use ($matchStart, $matchEnd) {
                $query->whereBetween('match_date', [$matchStart, $matchEnd]);
            })
            ->count();

        if ($conflictingAssignments > 0) {
            return [
                'valid' => false,
                'message' => 'L\'arbitre a déjà une assignation à cette période'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Récupérer le calendrier des arbitres
     */
    public function getRefereeCalendar(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());

        $assignments = MatchOfficial::with(['match.competition', 'match.homeTeam', 'match.awayTeam', 'user'])
            ->whereHas('match', function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('match_date', [$dateFrom, $dateTo]);
            })
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->user->name . ' - ' . $assignment->role,
                    'start' => $assignment->match->match_date,
                    'end' => Carbon::parse($assignment->match->match_date)->addHours(2),
                    'match' => $assignment->match->homeTeam->name . ' vs ' . $assignment->match->awayTeam->name,
                    'competition' => $assignment->match->competition->name,
                    'role' => $assignment->role,
                    'referee_name' => $assignment->user->name,
                ];
            });

        return response()->json($assignments);
    }

    /**
     * Récupérer les statistiques des assignations
     */
    public function getAssignmentStats()
    {
        $assignments = MatchOfficial::with('match')
            ->whereHas('match', function ($query) {
                $query->where('status', 'scheduled');
            })
            ->get();

        $stats = [
            'total_assignments' => $assignments->count(),
            'referee_assignments' => $assignments->where('role', 'referee')->count(),
            'assistant_assignments' => $assignments->whereIn('role', ['assistant_referee_1', 'assistant_referee_2'])->count(),
            'fourth_official_assignments' => $assignments->where('role', 'fourth_official')->count(),
            'var_assignments' => $assignments->where('role', 'var_referee')->count(),
        ];

        return response()->json($stats);
    }
}
