<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\GameMatch;
use App\Models\MatchRoster;
use App\Models\MatchOfficial;
use App\Models\PlayerSeasonStat;
use App\Models\Team;
use App\Services\LeagueSchedulingService;
use App\Services\StandingsService;
use App\Events\MatchUpdated;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeagueChampionshipController extends Controller
{
    protected $schedulingService;
    protected $standingsService;

    public function __construct(LeagueSchedulingService $schedulingService, StandingsService $standingsService)
    {
        $this->schedulingService = $schedulingService;
        $this->standingsService = $standingsService;
    }

    /**
     * Show competition details
     */
    public function show(Competition $competition): JsonResponse
    {
        try {
            $competition->load(['teams.club', 'gameMatches']);
            
            // Transform teams to include club information
            $transformedTeams = $competition->teams->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'type' => $team->type,
                    'formation' => $team->formation,
                    'club' => [
                        'id' => $team->club->id,
                        'name' => $team->club->name,
                        'short_name' => $team->club->short_name,
                    ]
                ];
            });
            
            $competitionData = [
                'id' => $competition->id,
                'name' => $competition->name,
                'short_name' => $competition->short_name,
                'type' => $competition->type,
                'season' => $competition->season,
                'start_date' => $competition->start_date,
                'end_date' => $competition->end_date,
                'status' => $competition->status,
                'description' => $competition->description,
                'teams' => $transformedTeams,
                'total_matches' => $competition->gameMatches->count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $competitionData
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch competition: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch competition'
            ], 500);
        }
    }

    /**
     * Generate round-robin schedule for a competition
     */
    public function generateSchedule(Request $request, Competition $competition): JsonResponse
    {
        try {
            $request->validate([
                'start_date' => 'required|date|after:today',
                'match_interval_days' => 'required|integer|min:1|max:14',
            ]);

            $schedule = $this->schedulingService->generateRoundRobinSchedule(
                $competition,
                $request->start_date,
                $request->match_interval_days
            );

            return response()->json([
                'success' => true,
                'message' => 'Schedule generated successfully',
                'data' => $schedule
            ]);
        } catch (\Exception $e) {
            Log::error('Schedule generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get competition schedule
     */
    public function getSchedule(Competition $competition): JsonResponse
    {
        try {
            $matches = $competition->gameMatches()
                ->with(['homeTeam.club', 'awayTeam.club'])
                ->orderBy('matchday')
                ->orderBy('match_date')
                ->get();

            // Transform the data to include club names in a more accessible format
            $transformedMatches = $matches->map(function ($match) {
                return [
                    'id' => $match->id,
                    'competition_id' => $match->competition_id,
                    'home_team_id' => $match->home_team_id,
                    'away_team_id' => $match->away_team_id,
                    'matchday' => $match->matchday,
                    'match_date' => $match->match_date,
                    'kickoff_time' => $match->kickoff_time,
                    'venue' => $match->venue,
                    'stadium' => $match->stadium,
                    'capacity' => $match->capacity,
                    'weather_conditions' => $match->weather_conditions,
                    'pitch_condition' => $match->pitch_condition,
                    'referee' => $match->referee,
                    'assistant_referee_1' => $match->assistant_referee_1,
                    'assistant_referee_2' => $match->assistant_referee_2,
                    'fourth_official' => $match->fourth_official,
                    'var_referee' => $match->var_referee,
                    'match_status' => $match->match_status,
                    'home_score' => $match->home_score,
                    'away_score' => $match->away_score,
                    'home_possession' => $match->home_possession,
                    'away_possession' => $match->away_possession,
                    'home_shots' => $match->home_shots,
                    'away_shots' => $match->away_shots,
                    'home_shots_on_target' => $match->home_shots_on_target,
                    'away_shots_on_target' => $match->away_shots_on_target,
                    'home_corners' => $match->home_corners,
                    'away_corners' => $match->away_corners,
                    'home_fouls' => $match->home_fouls,
                    'away_fouls' => $match->away_fouls,
                    'home_offsides' => $match->home_offsides,
                    'away_offsides' => $match->away_offsides,
                    'created_at' => $match->created_at,
                    'updated_at' => $match->updated_at,
                    'home_team' => [
                        'id' => $match->homeTeam->id,
                        'name' => $match->homeTeam->name,
                        'club' => [
                            'id' => $match->homeTeam->club->id,
                            'name' => $match->homeTeam->club->name,
                            'short_name' => $match->homeTeam->club->short_name,
                        ]
                    ],
                    'away_team' => [
                        'id' => $match->awayTeam->id,
                        'name' => $match->awayTeam->name,
                        'club' => [
                            'id' => $match->awayTeam->club->id,
                            'name' => $match->awayTeam->club->name,
                            'short_name' => $match->awayTeam->club->short_name,
                        ]
                    ],
                    'status' => $match->match_status,
                    'scheduled_at' => $match->match_date,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedMatches
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch schedule'
            ], 500);
        }
    }

    /**
     * Submit match roster
     */
    public function submitRoster(Request $request, GameMatch $gameMatch): JsonResponse
    {
        try {
            $request->validate([
                'team_id' => 'required|exists:teams,id',
                'players' => 'required|array|min:11|max:18',
                'players.*.player_id' => 'required|exists:players,id',
                'players.*.position' => 'required|string',
                'players.*.is_starter' => 'required|boolean',
                'players.*.jersey_number' => 'required|integer|min:1|max:99',
            ]);

            // Check if team is part of this match
            if ($gameMatch->home_team_id !== $request->team_id && $gameMatch->away_team_id !== $request->team_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team is not part of this match'
                ], 400);
            }

            // Check if roster already exists
            $existingRoster = MatchRoster::where('match_id', $gameMatch->id)
                ->where('team_id', $request->team_id)
                ->first();

            if ($existingRoster) {
                return response()->json([
                    'success' => false,
                    'message' => 'Roster already submitted for this team'
                ], 400);
            }

            DB::transaction(function () use ($request, $gameMatch) {
                $roster = MatchRoster::create([
                    'match_id' => $gameMatch->id,
                    'team_id' => $request->team_id,
                    'submitted_at' => now(),
                ]);

                foreach ($request->players as $playerData) {
                    $roster->players()->create([
                        'player_id' => $playerData['player_id'],
                        'position' => $playerData['position'],
                        'is_starter' => $playerData['is_starter'],
                        'jersey_number' => $playerData['jersey_number'],
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Roster submitted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Roster submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit roster: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get match rosters
     */
    public function getRosters(GameMatch $gameMatch): JsonResponse
    {
        try {
            $rosters = $gameMatch->rosters()
                ->with(['team', 'players.player'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $rosters
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch rosters: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rosters'
            ], 500);
        }
    }

    /**
     * Assign match officials
     */
    public function assignOfficials(Request $request, GameMatch $gameMatch): JsonResponse
    {
        try {
            $request->validate([
                'officials' => 'required|array',
                'officials.*.user_id' => 'required|exists:users,id',
                'officials.*.role' => 'required|string|in:referee,assistant_referee,fourth_official',
            ]);

            DB::transaction(function () use ($request, $gameMatch) {
                // Remove existing officials
                $gameMatch->officials()->delete();

                // Assign new officials
                foreach ($request->officials as $officialData) {
                    MatchOfficial::create([
                        'match_id' => $gameMatch->id,
                        'user_id' => $officialData['user_id'],
                        'role' => $officialData['role'],
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Officials assigned successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Official assignment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign officials: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get competition standings
     */
    public function getStandings(Competition $competition): JsonResponse
    {
        try {
            $standings = $this->standingsService->calculateStandings($competition);

            return response()->json([
                'success' => true,
                'data' => $standings
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to calculate standings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate standings'
            ], 500);
        }
    }

    /**
     * Get player season statistics
     */
    public function getPlayerStats(Competition $competition): JsonResponse
    {
        try {
            $stats = PlayerSeasonStat::where('competition_id', $competition->id)
                ->with(['player', 'team'])
                ->orderBy('goals', 'desc')
                ->orderBy('assists', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch player stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch player statistics'
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
                'status' => 'required|string|in:scheduled,in_progress,completed,cancelled',
                'home_score' => 'nullable|integer|min:0',
                'away_score' => 'nullable|integer|min:0',
            ]);

            $gameMatch->update([
                'match_status' => $request->status,
                'home_score' => $request->home_score,
                'away_score' => $request->away_score,
            ]);

            // Broadcast match update event
            event(new MatchUpdated($gameMatch));

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
     * Update match statistics
     */
    public function updateStatistics(Request $request, GameMatch $gameMatch): JsonResponse
    {
        try {
            $request->validate([
                'home_shots' => 'nullable|integer|min:0',
                'home_shots_on_target' => 'nullable|integer|min:0',
                'home_possession' => 'nullable|integer|min:0|max:100',
                'home_corners' => 'nullable|integer|min:0',
                'home_fouls' => 'nullable|integer|min:0',
                'home_offsides' => 'nullable|integer|min:0',
                'away_shots' => 'nullable|integer|min:0',
                'away_shots_on_target' => 'nullable|integer|min:0',
                'away_possession' => 'nullable|integer|min:0|max:100',
                'away_corners' => 'nullable|integer|min:0',
                'away_fouls' => 'nullable|integer|min:0',
                'away_offsides' => 'nullable|integer|min:0',
            ]);

            $gameMatch->update([
                'home_shots' => $request->home_shots,
                'home_shots_on_target' => $request->home_shots_on_target,
                'home_possession' => $request->home_possession,
                'home_corners' => $request->home_corners,
                'home_fouls' => $request->home_fouls,
                'home_offsides' => $request->home_offsides,
                'away_shots' => $request->away_shots,
                'away_shots_on_target' => $request->away_shots_on_target,
                'away_possession' => $request->away_possession,
                'away_corners' => $request->away_corners,
                'away_fouls' => $request->away_fouls,
                'away_offsides' => $request->away_offsides,
            ]);

            // Broadcast match update event
            event(new MatchUpdated($gameMatch));

            return response()->json([
                'success' => true,
                'message' => 'Match statistics updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Match statistics update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update match statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get individual match details
     */
    public function getMatch(GameMatch $gameMatch): JsonResponse
    {
        try {
            $gameMatch->load([
                'competition',
                'homeTeam.club',
                'awayTeam.club',
                'rosters.team',
                'rosters.players.player',
                'officials.user'
            ]);

            $matchData = [
                'id' => $gameMatch->id,
                'competition_id' => $gameMatch->competition_id,
                'home_team_id' => $gameMatch->home_team_id,
                'away_team_id' => $gameMatch->away_team_id,
                'matchday' => $gameMatch->matchday,
                'match_date' => $gameMatch->match_date,
                'kickoff_time' => $gameMatch->kickoff_time,
                'venue' => $gameMatch->venue,
                'stadium' => $gameMatch->stadium,
                'capacity' => $gameMatch->capacity,
                'weather_conditions' => $gameMatch->weather_conditions,
                'pitch_condition' => $gameMatch->pitch_condition,
                'referee' => $gameMatch->referee,
                'assistant_referee_1' => $gameMatch->assistant_referee_1,
                'assistant_referee_2' => $gameMatch->assistant_referee_2,
                'fourth_official' => $gameMatch->fourth_official,
                'var_referee' => $gameMatch->var_referee,
                'match_status' => $gameMatch->match_status,
                'home_score' => $gameMatch->home_score,
                'away_score' => $gameMatch->away_score,
                'home_possession' => $gameMatch->home_possession,
                'away_possession' => $gameMatch->away_possession,
                'home_shots' => $gameMatch->home_shots,
                'away_shots' => $gameMatch->away_shots,
                'home_shots_on_target' => $gameMatch->home_shots_on_target,
                'away_shots_on_target' => $gameMatch->away_shots_on_target,
                'home_corners' => $gameMatch->home_corners,
                'away_corners' => $gameMatch->away_corners,
                'home_fouls' => $gameMatch->home_fouls,
                'away_fouls' => $gameMatch->away_fouls,
                'home_offsides' => $gameMatch->home_offsides,
                'away_offsides' => $gameMatch->away_offsides,
                'created_at' => $gameMatch->created_at,
                'updated_at' => $gameMatch->updated_at,
                'competition' => [
                    'id' => $gameMatch->competition->id,
                    'name' => $gameMatch->competition->name,
                    'type' => $gameMatch->competition->type,
                ],
                'home_team' => [
                    'id' => $gameMatch->homeTeam->id,
                    'name' => $gameMatch->homeTeam->name,
                    'club' => [
                        'id' => $gameMatch->homeTeam->club->id,
                        'name' => $gameMatch->homeTeam->club->name,
                        'short_name' => $gameMatch->homeTeam->club->short_name,
                    ]
                ],
                'away_team' => [
                    'id' => $gameMatch->awayTeam->id,
                    'name' => $gameMatch->awayTeam->name,
                    'club' => [
                        'id' => $gameMatch->awayTeam->club->id,
                        'name' => $gameMatch->awayTeam->club->name,
                        'short_name' => $gameMatch->awayTeam->club->short_name,
                    ]
                ],
                'rosters' => $gameMatch->rosters->map(function ($roster) {
                    return [
                        'id' => $roster->id,
                        'team_id' => $roster->team_id,
                        'team' => [
                            'id' => $roster->team->id,
                            'name' => $roster->team->name,
                        ],
                        'players' => $roster->players->map(function ($rosterPlayer) {
                            return [
                                'id' => $rosterPlayer->id,
                                'player_id' => $rosterPlayer->player_id,
                                'position' => $rosterPlayer->position,
                                'is_starter' => $rosterPlayer->is_starter,
                                'jersey_number' => $rosterPlayer->jersey_number,
                                'player' => [
                                    'id' => $rosterPlayer->player->id,
                                    'name' => $rosterPlayer->player->first_name . ' ' . $rosterPlayer->player->last_name,
                                    'position' => $rosterPlayer->player->position,
                                ]
                            ];
                        })
                    ];
                }),
                'officials' => $gameMatch->officials->map(function ($official) {
                    return [
                        'id' => $official->id,
                        'role' => $official->role,
                        'user' => [
                            'id' => $official->user->id,
                            'name' => $official->user->name,
                            'email' => $official->user->email,
                        ]
                    ];
                }),
                'status' => $gameMatch->match_status,
                'scheduled_at' => $gameMatch->match_date,
            ];

            return response()->json([
                'success' => true,
                'data' => $matchData
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch match: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch match'
            ], 500);
        }
    }

    /**
     * Get match events
     */
    public function getEvents(GameMatch $gameMatch): JsonResponse
    {
        try {
            $events = $gameMatch->events()
                ->with(['player', 'team'])
                ->orderBy('minute')
                ->get();

            $transformedEvents = $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_type' => $event->event_type,
                    'minute' => $event->minute,
                    'player_id' => $event->player_id,
                    'team_id' => $event->team_id,
                    'description' => $event->description,
                    'additional_data' => $event->additional_data,
                    'player' => [
                        'id' => $event->player->id ?? null,
                        'name' => $event->player ? ($event->player->first_name . ' ' . $event->player->last_name) : null,
                    ] ?? null,
                    'team' => [
                        'id' => $event->team->id ?? null,
                        'name' => $event->team->name ?? null,
                    ] ?? null,
                    'type' => $event->event_type,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedEvents
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch events: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch events'
            ], 500);
        }
    }

    /**
     * Add match event
     */
    public function addEvent(Request $request, GameMatch $gameMatch): JsonResponse
    {
        try {
            $request->validate([
                'type' => 'required|string|in:goal,yellow_card,red_card,substitution,injury',
                'minute' => 'required|integer|min:1|max:120',
                'player_id' => 'required|exists:players,id',
                'description' => 'required|string|max:500',
            ]);

            $event = $gameMatch->events()->create([
                'event_type' => $request->type,
                'minute' => $request->minute,
                'player_id' => $request->player_id,
                'team_id' => $request->team_id ?? null,
                'description' => $request->description,
                'additional_data' => $request->additional_data ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Event added successfully',
                'data' => $event
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to add event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete match event
     */
    public function deleteEvent(GameMatch $gameMatch, $eventId): JsonResponse
    {
        try {
            $event = $gameMatch->events()->findOrFail($eventId);
            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete event: ' . $e->getMessage()
            ], 500);
        }
    }
} 