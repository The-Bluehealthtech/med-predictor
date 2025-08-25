<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\GameMatch;
use App\Models\Team;
use App\Services\LeagueSchedulingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class CalendarManagementController extends Controller
{
    protected $schedulingService;

    public function __construct(LeagueSchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
        $this->middleware('auth');
    }

    /**
     * Generate full schedule automatically
     */
    public function generateFullSchedule(Request $request, Competition $competition): JsonResponse
    {
        try {
            $request->validate([
                'start_date' => 'required|date|after:today',
                'match_interval_days' => 'required|integer|min:1|max:14',
            ]);

            // Check if competition has teams
            if ($competition->teams->count() < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'La compétition doit avoir au moins 2 équipes pour générer un calendrier.'
                ], 400);
            }

            // Check if schedule already exists
            $existingMatches = $competition->matches()->count();
            if ($existingMatches > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Un calendrier existe déjà pour cette compétition. Veuillez d\'abord supprimer les matchs existants.'
                ], 400);
            }

            $result = $this->schedulingService->generateFullSchedule(
                $competition,
                $request->start_date,
                $request->match_interval_days
            );

            // Clear cache after generating schedule
            $this->clearScheduleCache($competition);

            return response()->json([
                'success' => true,
                'message' => 'Calendrier généré avec succès !',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Schedule generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Échec de la génération du calendrier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get competition schedule
     */
    public function getSchedule(Competition $competition): JsonResponse
    {
        try {
            // Cache key for this competition's schedule
            $cacheKey = "competition_schedule_{$competition->id}";
            
            // Try to get from cache first
            $schedule = Cache::remember($cacheKey, 300, function () use ($competition) {
                $matches = $competition->matches()
                    ->with([
                        'homeTeam:id,name,club_id',
                        'awayTeam:id,name,club_id',
                        'homeTeam.club:id,name',
                        'awayTeam.club:id,name'
                    ])
                    ->orderBy('matchday')
                    ->orderBy('kickoff_time')
                    ->get()
                    ->groupBy('matchday');

                $schedule = [];
                foreach ($matches as $matchday => $matchdayMatches) {
                    $schedule[] = [
                        'matchday' => $matchday,
                        'matches' => $matchdayMatches->map(function ($match) {
                            return [
                                'id' => $match->id,
                                'home_team' => [
                                    'id' => $match->homeTeam->id,
                                    'name' => $match->homeTeam->name,
                                    'club' => $match->homeTeam->club?->name,
                                ],
                                'away_team' => [
                                    'id' => $match->awayTeam->id,
                                    'name' => $match->awayTeam->name,
                                    'club' => $match->awayTeam->club?->name,
                                ],
                                'kickoff_time' => $match->kickoff_time,
                                'venue' => $match->venue,
                                'status' => $match->match_status,
                            ];
                        })
                    ];
                }
                
                return $schedule;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'competition' => [
                        'id' => $competition->id,
                        'name' => $competition->name,
                        'teams_count' => $competition->teams()->count(),
                    ],
                    'schedule' => $schedule,
                    'total_matchdays' => count($schedule),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération du calendrier'
            ], 500);
        }
    }

    /**
     * Validate and create manual match
     */
    public function validateAndCreateMatch(Request $request, Competition $competition): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'home_team_id' => 'required|exists:teams,id',
                'away_team_id' => 'required|exists:teams,id|different:home_team_id',
                'matchday' => 'required|integer|min:1',
                'kickoff_time' => 'required|date|after:now',
                'venue' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if teams belong to this competition
            $homeTeam = $competition->teams()->find($request->home_team_id);
            $awayTeam = $competition->teams()->find($request->away_team_id);

            if (!$homeTeam || !$awayTeam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une ou les deux équipes ne participent pas à cette compétition.'
                ], 400);
            }

            $matchData = $request->only([
                'home_team_id', 'away_team_id', 'matchday', 'kickoff_time', 'venue'
            ]);
            $matchData['competition_id'] = $competition->id;

            // Validate using scheduling service
            $validationErrors = $this->schedulingService->validateManualMatch($competition, $matchData);

            if (!empty($validationErrors)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Conflit de calendrier',
                    'message' => implode(' ', $validationErrors),
                    'errors' => $validationErrors
                ], 422);
            }

            // Create the match
            $match = GameMatch::create($matchData);

            // Clear cache after creating match
            $this->clearScheduleCache($competition);

            return response()->json([
                'success' => true,
                'message' => 'Match créé avec succès',
                'data' => [
                    'id' => $match->id,
                    'home_team' => [
                        'id' => $homeTeam->id,
                        'name' => $homeTeam->name,
                    ],
                    'away_team' => [
                        'id' => $awayTeam->id,
                        'name' => $awayTeam->name,
                    ],
                    'matchday' => $match->matchday,
                    'kickoff_time' => $match->kickoff_time,
                    'venue' => $match->venue,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Match creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Échec de la création du match: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update match
     */
    public function updateMatch(Request $request, GameMatch $gameMatch): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'kickoff_time' => 'sometimes|required|date|after:now',
                'venue' => 'sometimes|nullable|string|max:255',
                'match_status' => 'sometimes|required|string|in:scheduled,in_progress,completed,cancelled,postponed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If updating kickoff_time, validate conflicts
            if ($request->has('kickoff_time')) {
                $matchData = [
                    'home_team_id' => $gameMatch->home_team_id,
                    'away_team_id' => $gameMatch->away_team_id,
                    'kickoff_time' => $request->kickoff_time,
                    'venue' => $request->venue ?? $gameMatch->venue,
                ];

                $validationErrors = $this->schedulingService->validateManualMatch($gameMatch->competition, $matchData);

                if (!empty($validationErrors)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Conflit de calendrier',
                        'message' => implode(' ', $validationErrors),
                        'errors' => $validationErrors
                    ], 422);
                }
            }

            $gameMatch->update($request->only(['kickoff_time', 'venue', 'match_status']));

            // Clear cache after updating match
            $this->clearScheduleCache($gameMatch->competition);

            return response()->json([
                'success' => true,
                'message' => 'Match mis à jour avec succès',
                'data' => $gameMatch->fresh(['homeTeam', 'awayTeam'])
            ]);

        } catch (\Exception $e) {
            Log::error('Match update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Échec de la mise à jour du match: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete match
     */
    public function deleteMatch(GameMatch $gameMatch): JsonResponse
    {
        try {
            // Only allow deletion of scheduled matches
            if ($gameMatch->match_status !== 'scheduled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Seuls les matchs programmés peuvent être supprimés.'
                ], 400);
            }

            $gameMatch->delete();

            // Clear cache after deleting match
            $this->clearScheduleCache($gameMatch->competition);

            return response()->json([
                'success' => true,
                'message' => 'Match supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Match deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Échec de la suppression du match: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available teams for competition
     */
    public function getAvailableTeams(Competition $competition): JsonResponse
    {
        try {
            $cacheKey = "competition_teams_{$competition->id}";
            
            $teams = Cache::remember($cacheKey, 600, function () use ($competition) {
                return $competition->teams()
                    ->with(['club:id,name'])
                    ->select('teams.id', 'teams.name', 'teams.club_id')
                    ->orderBy('teams.name')
                    ->get()
                    ->map(function ($team) {
                        return [
                            'id' => $team->id,
                            'name' => $team->name,
                            'club' => $team->club?->name,
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data' => $teams
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch available teams: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération des équipes'
            ], 500);
        }
    }

    /**
     * Get available venues
     */
    public function getAvailableVenues(): JsonResponse
    {
        try {
            // You can implement venue management later
            // For now, return a list of default venues
            $venues = [
                'Stade Principal',
                'Stade Municipal',
                'Complexe Sportif',
                'Terrain de Football',
                'Arena Sportive',
            ];

            return response()->json([
                'success' => true,
                'data' => $venues
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch venues: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Échec de la récupération des terrains'
            ], 500);
        }
    }

    /**
     * Clear all matches for a competition
     */
    public function clearSchedule(Competition $competition): JsonResponse
    {
        try {
            // Only allow clearing if no matches are in progress or completed
            $activeMatches = $competition->matches()
                ->whereIn('match_status', ['in_progress', 'completed'])
                ->count();

            if ($activeMatches > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer le calendrier car certains matchs sont en cours ou terminés.'
                ], 400);
            }

            $deletedCount = $competition->matches()->delete();

            // Clear cache after clearing schedule
            $this->clearScheduleCache($competition);

            return response()->json([
                'success' => true,
                'message' => "Calendrier supprimé avec succès ({$deletedCount} matchs supprimés)"
            ]);

        } catch (\Exception $e) {
            Log::error('Schedule clearing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Échec de la suppression du calendrier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear competition schedule cache
     */
    private function clearScheduleCache(Competition $competition): void
    {
        Cache::forget("competition_schedule_{$competition->id}");
        Cache::forget("competition_teams_{$competition->id}");
    }
}
