<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\MatchEvent;
use App\Models\MatchOfficial;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefereeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('referee.access');
    }

    /**
     * Referee dashboard
     */
    public function dashboard(Request $request)
    {
        try {
            $user = auth()->user();
            

            // Construction de la requête pour les matches assignés
            $assignedQuery = GameMatch::whereHas('officials', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['homeTeam.club', 'awayTeam.club', 'competition', 'officials'])
            ->where('status', '!=', 'completed');

            // Tri par défaut
            $sortBy = $request->get('sort_by', 'match_date');
            $sortOrder = $request->get('sort_order', 'asc');
            $assignedQuery->orderBy($sortBy, $sortOrder);

            // Pagination pour les matches assignés
            $assignedMatches = $assignedQuery->paginate(20)->appends($request->query());

            // Matches récents (sans pagination, limité à 5)
            $recentMatches = GameMatch::whereHas('officials', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['homeTeam.club', 'awayTeam.club', 'competition', 'officials'])
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

            $stats = [
                'upcoming_matches' => $assignedMatches->total(),
                'completed_matches' => $recentMatches->count(),
                'pending_reports' => 0, // Table match_events n'existe pas encore
                'active_competitions' => Competition::whereHas('matches.officials', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->where('status', 'active')->count(),
            ];

            return view('referee.dashboard', compact('assignedMatches', 'recentMatches', 'stats'));
        } catch (\Exception $e) {
            Log::error('Referee dashboard error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load dashboard');
        }
    }

    /**
     * Show match assignments
     */
    public function matchAssignments()
    {
        $user = auth()->user();
        
        $assignments = GameMatch::whereHas('officials', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['homeTeam', 'awayTeam', 'competition', 'officials'])
        ->orderBy('kickoff_time')
        ->paginate(10);

        return view('referee.match-assignments', compact('assignments'));
    }

    /**
     * Show match sheet for a specific match
     */
    public function matchSheet(GameMatch $match)
    {
        $user = auth()->user();
        
        // Check if referee is assigned to this match
        $isAssigned = $match->officials()
            ->where('user_id', $user->id)
            ->exists();

        if (!$isAssigned) {
            return redirect()->route('referee.dashboard')
                ->with('error', 'You are not assigned to this match');
        }

        $events = $match->events()
            ->with(['player', 'team', 'assistedByPlayer', 'substitutedPlayer'])
            ->orderBy('minute')
            ->orderBy('extra_time_minute')
            ->get();

        return view('referee.match-sheet', compact('match', 'events'));
    }


    /**
     * Show performance statistics
     */
    public function performanceStats()
    {
        $user = auth()->user();
        
        $stats = [
            'total_matches' => GameMatch::whereHas('officials', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            
            'completed_matches' => GameMatch::whereHas('officials', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('match_status', 'completed')->count(),
            
            'total_events' => MatchEvent::whereHas('match.officials', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            
            'cards_issued' => MatchEvent::whereHas('match.officials', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->whereIn('event_type', ['yellow_card', 'red_card'])->count(),
        ];

        $monthlyStats = GameMatch::whereHas('officials', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('match_status', 'completed')
        ->where('completed_at', '>=', now()->subMonths(6))
        ->selectRaw('strftime("%m", completed_at) as month, COUNT(*) as matches')
        ->groupBy('month')
        ->get();

        return view('referee.performance-stats', compact('stats', 'monthlyStats'));
    }

    /**
     * Show competition schedule
     */
    public function competitionSchedule()
    {
        $user = auth()->user();
        
        $competitions = Competition::whereHas('matches.officials', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['matches' => function ($query) use ($user) {
            $query->whereHas('officials', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orderBy('kickoff_time');
        }])
        ->where('status', 'active')
        ->get();

        return view('referee.competition-schedule', compact('competitions'));
    }

    /**
     * Show settings and preferences
     */
    public function settings()
    {
        $user = auth()->user();
        
        return view('referee.settings', compact('user'));
    }

    /**
     * Update referee settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'preferences' => 'nullable|array',
        ]);

        $user = auth()->user();
        $user->update($request->only(['name', 'email', 'phone']));

        if ($request->has('preferences')) {
            $user->preferences = $request->preferences;
            $user->save();
        }

        return redirect()->route('referee.settings')
            ->with('success', 'Settings updated successfully');
    }

    /**
     * Record match event
     */
    public function recordEvent(Request $request, GameMatch $gameMatch): JsonResponse
    {
        try {
            $request->validate([
                'event_type' => 'required|string',
                'player_id' => 'nullable|exists:players,id',
                'team_id' => 'required|exists:teams,id',
                'minute' => 'required|integer|min:1|max:120',
                'extra_time_minute' => 'nullable|integer|min:1|max:10',
                'period' => 'required|string|in:first_half,second_half,extra_time_first,extra_time_second',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'severity' => 'nullable|string|in:low,medium,high',
                'event_data' => 'nullable|array',
                'assisted_by_player_id' => 'nullable|exists:players,id',
                'substituted_player_id' => 'nullable|exists:players,id',
            ]);

            // Verify referee is assigned to this match
            $user = auth()->user();
            $isAssigned = $gameMatch->officials()
                ->where('user_id', $user->id)
                ->exists();

            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this match'
                ], 403);
            }

            $event = MatchEvent::create([
                'match_id' => $gameMatch->id,
                'player_id' => $request->player_id,
                'team_id' => $request->team_id,
                'assisted_by_player_id' => $request->assisted_by_player_id,
                'substituted_player_id' => $request->substituted_player_id,
                'recorded_by_user_id' => $user->id,
                'event_type' => $request->event_type,
                'minute' => $request->minute,
                'extra_time_minute' => $request->extra_time_minute,
                'period' => $request->period,
                'description' => $request->description,
                'location' => $request->location,
                'severity' => $request->severity,
                'event_data' => $request->event_data,
            ]);

            // Broadcast event for real-time updates
            broadcast(new \App\Events\MatchEventRecorded($event))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Event recorded successfully',
                'data' => $event->load(['player', 'team', 'assistedByPlayer', 'substitutedPlayer'])
            ]);
        } catch (\Exception $e) {
            Log::error('Event recording failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get match events
     */
    public function getMatchEvents(GameMatch $gameMatch): JsonResponse
    {
        try {
            $events = $gameMatch->events()
                ->with(['player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser'])
                ->orderBy('minute')
                ->orderBy('extra_time_minute')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $events
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch match events: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch match events'
            ], 500);
        }
    }

    /**
     * Update match status
     */
    public function updateMatchStatus(Request $request, GameMatch $gameMatch): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|string|in:scheduled,in_progress,completed,cancelled,postponed',
                'home_score' => 'nullable|integer|min:0',
                'away_score' => 'nullable|integer|min:0',
                'notes' => 'nullable|string',
            ]);

            $user = auth()->user();
            $isAssigned = $gameMatch->officials()
                ->where('user_id', $user->id)
                ->whereIn('role', ['referee', 'fourth_official'])
                ->exists();

            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only referees can update match status'
                ], 403);
            }

            $gameMatch->update([
                'match_status' => $request->status,
                'home_score' => $request->home_score,
                'away_score' => $request->away_score,
                'completed_at' => $request->status === 'completed' ? now() : null,
            ]);

            // If match is completed, dispatch processing job
            if ($request->status === 'completed') {
                \App\Jobs\ProcessCompletedMatch::dispatch($gameMatch);
            }

            return response()->json([
                'success' => true,
                'message' => 'Match status updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Match status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update match status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Contest an event
     */
    public function contestEvent(Request $request, MatchEvent $event): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $event->contest($request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Event contested successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Event contest failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to contest event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm an event
     */
    public function confirmEvent(MatchEvent $event): JsonResponse
    {
        try {
            $event->confirm();

            return response()->json([
                'success' => true,
                'message' => 'Event confirmed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Event confirmation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show create match report page
     */
    public function createMatchReport(Request $request)
    {
        try {
            $user = auth()->user();
            
            
            // Construction de la requête pour les matches assignés
            $assignedQuery = GameMatch::whereHas('officials', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['homeTeam.club', 'awayTeam.club', 'competition'])
            ->where('status', '!=', 'completed');

            // Tri par défaut
            $sortBy = $request->get('sort_by', 'match_date');
            $sortOrder = $request->get('sort_order', 'asc');
            $assignedQuery->orderBy($sortBy, $sortOrder);

            // Pagination pour les matches assignés
            $assignedMatches = $assignedQuery->paginate(20)->appends($request->query());
            
            // Récupérer les matches récents (sans pagination, limité à 5)
            $recentMatches = GameMatch::whereHas('officials', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['homeTeam.club', 'awayTeam.club', 'competition'])
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
            
            return view('referee.create-match-report', compact('assignedMatches', 'recentMatches'));
            
        } catch (\Exception $e) {
            Log::error('Create match report error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load create match report page');
        }
    }

    /**
     * Show detailed match report form for a specific match
     */
    public function createDetailedMatchReport($matchId)
    {
        try {
            $user = auth()->user();
            
            // Récupérer le match avec toutes les relations nécessaires
            $match = GameMatch::with([
                'homeTeam.club', 
                'awayTeam.club', 
                'competition',
                'officials.user'
            ])->find($matchId);
            
            if (!$match) {
                return redirect()->route('referee.create-match-report')->with('error', 'Match non trouvé');
            }
            
            // Vérifier si l'arbitre est assigné à ce match
            $isAssigned = $match->officials()->where('user_id', $user->id)->exists();
            if (!$isAssigned) {
                return redirect()->route('referee.create-match-report')->with('error', 'Vous n\'êtes pas assigné à ce match');
            }
            
            // Récupérer les joueurs des équipes
            $homeTeamPlayers = $match->homeTeam->players ?? collect([]);
            $awayTeamPlayers = $match->awayTeam->players ?? collect([]);
            
            // Récupérer les officiels du match
            $officials = $match->officials()->with('user')->get();
            
            return view('referee.create-report-form', compact('match', 'homeTeamPlayers', 'awayTeamPlayers', 'officials'));
            
        } catch (\Exception $e) {
            Log::error('Create detailed match report error: ' . $e->getMessage());
            return redirect()->route('referee.create-match-report')->with('error', 'Erreur lors du chargement du formulaire de rapport');
        }
    }
}
