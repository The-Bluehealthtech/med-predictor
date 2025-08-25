<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RPMController extends Controller
{
    /**
     * Get all training sessions
     */
    public function sessions(): JsonResponse
    {
        $sessions = [
            [
                'id' => 1,
                'title' => 'Entraînement Technique',
                'date' => now()->toISOString(),
                'time' => '09:00 - 11:00',
                'type' => 'Technique',
                'playerCount' => 18,
                'status' => 'completed',
            ],
            [
                'id' => 2,
                'title' => 'Préparation Physique',
                'date' => now()->addDay()->toISOString(),
                'time' => '14:00 - 16:00',
                'type' => 'Physique',
                'playerCount' => 20,
                'status' => 'scheduled',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $sessions,
            'message' => 'Training sessions retrieved successfully'
        ]);
    }

    /**
     * Store a new training session
     */
    public function storeSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|string|max:100',
            'type' => 'required|string|in:Technique,Physique,Match,Récupération',
            'objectives' => 'sometimes|string|max:1000',
            'players' => 'sometimes|array',
        ]);

        $session = [
            'id' => rand(100, 999),
            ...$validated,
            'playerCount' => count($validated['players'] ?? []),
            'status' => 'scheduled',
        ];

        return response()->json([
            'success' => true,
            'data' => $session,
            'message' => 'Training session created successfully'
        ], 201);
    }

    /**
     * Get a specific training session
     */
    public function showSession(int $session): JsonResponse
    {
        $sessionData = [
            'id' => $session,
            'title' => 'Entraînement Technique',
            'date' => now()->toISOString(),
            'time' => '09:00 - 11:00',
            'type' => 'Technique',
            'objectives' => 'Amélioration de la technique de tir',
            'playerCount' => 18,
            'status' => 'completed',
            'players' => [
                ['id' => 1, 'name' => 'Ahmed Ben Ali', 'attendance' => 'present'],
                ['id' => 2, 'name' => 'Karim Benzema', 'attendance' => 'present'],
            ],
            'loadData' => [
                'averageRPE' => 7.2,
                'totalDistance' => 8500,
                'highIntensityDistance' => 1200,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $sessionData,
            'message' => 'Training session retrieved successfully'
        ]);
    }

    /**
     * Update a training session
     */
    public function updateSession(Request $request, int $session): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'time' => 'sometimes|string|max:100',
            'type' => 'sometimes|string|in:Technique,Physique,Match,Récupération',
            'objectives' => 'sometimes|string|max:1000',
        ]);

        $sessionData = [
            'id' => $session,
            ...$validated,
            'status' => 'scheduled',
        ];

        return response()->json([
            'success' => true,
            'data' => $sessionData,
            'message' => 'Training session updated successfully'
        ]);
    }

    /**
     * Delete a training session
     */
    public function destroySession(int $session): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Training session deleted successfully'
        ]);
    }

    /**
     * Get all matches
     */
    public function matches(): JsonResponse
    {
        $matches = [
            [
                'id' => 1,
                'title' => 'Match Amical',
                'date' => now()->addDays(7)->toISOString(),
                'time' => '16:00 - 18:00',
                'opponent' => 'Équipe B',
                'type' => 'Match',
                'status' => 'scheduled',
            ],
            [
                'id' => 2,
                'title' => 'Match de Préparation',
                'date' => now()->addDays(14)->toISOString(),
                'time' => '20:00 - 22:00',
                'opponent' => 'Équipe C',
                'type' => 'Match',
                'status' => 'scheduled',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $matches,
            'message' => 'Matches retrieved successfully'
        ]);
    }

    /**
     * Store a new match
     */
    public function storeMatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|string|max:100',
            'opponent' => 'required|string|max:255',
            'objectives' => 'sometimes|string|max:1000',
        ]);

        $match = [
            'id' => rand(100, 999),
            ...$validated,
            'type' => 'Match',
            'status' => 'scheduled',
        ];

        return response()->json([
            'success' => true,
            'data' => $match,
            'message' => 'Match created successfully'
        ], 201);
    }

    /**
     * Get a specific match
     */
    public function showMatch(int $match): JsonResponse
    {
        $matchData = [
            'id' => $match,
            'title' => 'Match Amical',
            'date' => now()->addDays(7)->toISOString(),
            'time' => '16:00 - 18:00',
            'opponent' => 'Équipe B',
            'type' => 'Match',
            'objectives' => 'Tester la nouvelle formation',
            'status' => 'scheduled',
            'players' => [
                ['id' => 1, 'name' => 'Ahmed Ben Ali', 'position' => 'Attaquant'],
                ['id' => 2, 'name' => 'Karim Benzema', 'position' => 'Attaquant'],
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $matchData,
            'message' => 'Match retrieved successfully'
        ]);
    }

    /**
     * Update a match
     */
    public function updateMatch(Request $request, int $match): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'time' => 'sometimes|string|max:100',
            'opponent' => 'sometimes|string|max:255',
            'objectives' => 'sometimes|string|max:1000',
        ]);

        $matchData = [
            'id' => $match,
            ...$validated,
            'status' => 'scheduled',
        ];

        return response()->json([
            'success' => true,
            'data' => $matchData,
            'message' => 'Match updated successfully'
        ]);
    }

    /**
     * Delete a match
     */
    public function destroyMatch(int $match): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Match deleted successfully'
        ]);
    }

    /**
     * Get player load data
     */
    public function playerLoads(): JsonResponse
    {
        $loads = [
            [
                'id' => 1,
                'name' => 'Ahmed Ben Ali',
                'position' => 'Attaquant',
                'load' => 8.5,
                'sessions' => 5,
                'status' => 'high',
            ],
            [
                'id' => 2,
                'name' => 'Karim Benzema',
                'position' => 'Attaquant',
                'load' => 7.2,
                'sessions' => 4,
                'status' => 'medium',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $loads,
            'message' => 'Player loads retrieved successfully'
        ]);
    }

    /**
     * Get a specific player's load data
     */
    public function showPlayerLoad(int $playerId): JsonResponse
    {
        $loadData = [
            'playerId' => $playerId,
            'name' => 'Ahmed Ben Ali',
            'position' => 'Attaquant',
            'currentLoad' => 8.5,
            'weeklyLoad' => 42.5,
            'monthlyLoad' => 180.0,
            'acwr' => 1.2,
            'status' => 'high',
            'sessions' => [
                [
                    'id' => 1,
                    'date' => now()->subDays(1)->toISOString(),
                    'rpe' => 8,
                    'duration' => 90,
                ],
                [
                    'id' => 2,
                    'date' => now()->subDays(2)->toISOString(),
                    'rpe' => 7,
                    'duration' => 75,
                ],
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $loadData,
            'message' => 'Player load data retrieved successfully'
        ]);
    }

    /**
     * Update player load data
     */
    public function updatePlayerLoad(Request $request, int $playerId): JsonResponse
    {
        $validated = $request->validate([
            'rpe' => 'required|integer|min:1|max:10',
            'duration' => 'required|integer|min:1',
            'sessionId' => 'required|integer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Player load data updated successfully'
        ]);
    }

    /**
     * Get attendance data
     */
    public function attendance(): JsonResponse
    {
        $attendance = [
            [
                'id' => 1,
                'sessionId' => 1,
                'sessionTitle' => 'Entraînement Technique',
                'date' => now()->toISOString(),
                'present' => 18,
                'absent' => 2,
                'total' => 20,
            ],
            [
                'id' => 2,
                'sessionId' => 2,
                'sessionTitle' => 'Préparation Physique',
                'date' => now()->addDay()->toISOString(),
                'present' => 0,
                'absent' => 0,
                'total' => 20,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $attendance,
            'message' => 'Attendance data retrieved successfully'
        ]);
    }

    /**
     * Store attendance data
     */
    public function storeAttendance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sessionId' => 'required|integer',
            'players' => 'required|array',
            'players.*.playerId' => 'required|integer',
            'players.*.status' => 'required|string|in:present,absent,late,injured',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance data stored successfully'
        ]);
    }

    /**
     * Get attendance for a specific session
     */
    public function showAttendance(int $sessionId): JsonResponse
    {
        $attendanceData = [
            'sessionId' => $sessionId,
            'sessionTitle' => 'Entraînement Technique',
            'date' => now()->toISOString(),
            'players' => [
                [
                    'id' => 1,
                    'name' => 'Ahmed Ben Ali',
                    'status' => 'present',
                    'time' => '08:55',
                ],
                [
                    'id' => 2,
                    'name' => 'Karim Benzema',
                    'status' => 'present',
                    'time' => '09:00',
                ],
            ],
            'summary' => [
                'present' => 18,
                'absent' => 2,
                'late' => 0,
                'injured' => 0,
                'total' => 20,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $attendanceData,
            'message' => 'Session attendance retrieved successfully'
        ]);
    }

    /**
     * Sync data to Performance module
     */
    public function syncToPerformance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sessionId' => 'required|integer',
            'data' => 'required|array',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data synced to Performance module successfully',
            'syncId' => 'SYNC_' . rand(100000, 999999),
        ]);
    }

    /**
     * Get sync status
     */
    public function syncStatus(): JsonResponse
    {
        $status = [
            'lastSync' => now()->subHours(2)->toISOString(),
            'status' => 'completed',
            'syncedSessions' => 15,
            'pendingSessions' => 0,
            'errors' => 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $status,
            'message' => 'Sync status retrieved successfully'
        ]);
    }

    /**
     * Get calendar data
     */
    public function calendar(): JsonResponse
    {
        $calendar = [
            [
                'id' => 1,
                'title' => 'Entraînement Technique',
                'date' => now()->toISOString(),
                'type' => 'session',
                'status' => 'completed',
            ],
            [
                'id' => 2,
                'title' => 'Préparation Physique',
                'date' => now()->addDay()->toISOString(),
                'type' => 'session',
                'status' => 'scheduled',
            ],
            [
                'id' => 3,
                'title' => 'Match Amical',
                'date' => now()->addDays(7)->toISOString(),
                'type' => 'match',
                'status' => 'scheduled',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $calendar,
            'message' => 'Calendar data retrieved successfully'
        ]);
    }

    /**
     * Get weekly calendar
     */
    public function weekCalendar(string $date): JsonResponse
    {
        $weekData = [
            'startDate' => $date,
            'endDate' => now()->addDays(6)->toISOString(),
            'days' => [
                [
                    'date' => now()->toISOString(),
                    'day' => 'Lundi',
                    'sessions' => [
                        [
                            'id' => 1,
                            'title' => 'Entraînement Technique',
                            'time' => '09:00 - 11:00',
                            'type' => 'Technique',
                        ],
                    ],
                ],
                [
                    'date' => now()->addDay()->toISOString(),
                    'day' => 'Mardi',
                    'sessions' => [
                        [
                            'id' => 2,
                            'title' => 'Préparation Physique',
                            'time' => '14:00 - 16:00',
                            'type' => 'Physique',
                        ],
                    ],
                ],
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $weekData,
            'message' => 'Weekly calendar retrieved successfully'
        ]);
    }

    /**
     * Get RPM reports
     */
    public function reports(): JsonResponse
    {
        $reports = [
            [
                'id' => 1,
                'name' => 'Rapport Hebdomadaire RPM',
                'type' => 'weekly',
                'date' => now()->toISOString(),
                'status' => 'completed',
            ],
            [
                'id' => 2,
                'name' => 'Rapport Charge Joueurs',
                'type' => 'load',
                'date' => now()->subDays(7)->toISOString(),
                'status' => 'completed',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $reports,
            'message' => 'RPM reports retrieved successfully'
        ]);
    }

    /**
     * Generate a new report
     */
    public function generateReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:weekly,load,attendance,performance',
            'dateRange' => 'sometimes|array',
        ]);

        $report = [
            'id' => rand(100, 999),
            'name' => 'Rapport ' . ucfirst($validated['type']),
            'type' => $validated['type'],
            'date' => now()->toISOString(),
            'status' => 'generating',
        ];

        return response()->json([
            'success' => true,
            'data' => $report,
            'message' => 'Report generation started successfully'
        ]);
    }
} 