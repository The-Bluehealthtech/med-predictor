<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DTNController extends Controller
{
    /**
     * Get all national teams
     */
    public function teams(): JsonResponse
    {
        // Mock data for demonstration
        $teams = [
            [
                'id' => 1,
                'name' => 'Équipe A',
                'category' => 'Sénior',
                'gender' => 'Hommes',
                'playerCount' => 23,
                'status' => 'active',
                'nextMatch' => now()->addDays(7)->toISOString(),
            ],
            [
                'id' => 2,
                'name' => 'Équipe A',
                'category' => 'Sénior',
                'gender' => 'Femmes',
                'playerCount' => 20,
                'status' => 'active',
                'nextMatch' => now()->addDays(14)->toISOString(),
            ],
            [
                'id' => 3,
                'name' => 'U23',
                'category' => 'Espoirs',
                'gender' => 'Hommes',
                'playerCount' => 18,
                'status' => 'active',
                'nextMatch' => null,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $teams,
            'message' => 'National teams retrieved successfully'
        ]);
    }

    /**
     * Store a new national team
     */
    public function storeTeam(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'gender' => 'required|string|in:Hommes,Femmes',
        ]);

        // Mock creation
        $team = [
            'id' => rand(100, 999),
            ...$validated,
            'playerCount' => 0,
            'status' => 'active',
            'nextMatch' => null,
        ];

        return response()->json([
            'success' => true,
            'data' => $team,
            'message' => 'National team created successfully'
        ], 201);
    }

    /**
     * Get a specific national team
     */
    public function showTeam(int $team): JsonResponse
    {
        // Mock data
        $teamData = [
            'id' => $team,
            'name' => 'Équipe A',
            'category' => 'Sénior',
            'gender' => 'Hommes',
            'playerCount' => 23,
            'status' => 'active',
            'nextMatch' => now()->addDays(7)->toISOString(),
            'players' => [
                ['id' => 1, 'name' => 'Ahmed Ben Ali', 'position' => 'Attaquant'],
                ['id' => 2, 'name' => 'Karim Benzema', 'position' => 'Attaquant'],
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $teamData,
            'message' => 'National team retrieved successfully'
        ]);
    }

    /**
     * Update a national team
     */
    public function updateTeam(Request $request, int $team): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'gender' => 'sometimes|string|in:Hommes,Femmes',
        ]);

        // Mock update
        $teamData = [
            'id' => $team,
            ...$validated,
            'playerCount' => 23,
            'status' => 'active',
        ];

        return response()->json([
            'success' => true,
            'data' => $teamData,
            'message' => 'National team updated successfully'
        ]);
    }

    /**
     * Delete a national team
     */
    public function destroyTeam(int $team): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'National team deleted successfully'
        ]);
    }

    /**
     * Get all international selections
     */
    public function selections(): JsonResponse
    {
        $selections = [
            [
                'id' => 1,
                'name' => 'Coupe d\'Afrique 2024',
                'team' => 'Équipe A',
                'competition' => 'Coupe d\'Afrique',
                'status' => 'active',
                'date' => now()->addDays(30)->toISOString(),
                'playerCount' => 23,
            ],
            [
                'id' => 2,
                'name' => 'Qualifications Mondial 2025',
                'team' => 'Équipe A Femmes',
                'competition' => 'Qualifications Mondial',
                'status' => 'preparation',
                'date' => now()->addDays(60)->toISOString(),
                'playerCount' => 20,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $selections,
            'message' => 'International selections retrieved successfully'
        ]);
    }

    /**
     * Store a new international selection
     */
    public function storeSelection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'team' => 'required|string|max:255',
            'competition' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $selection = [
            'id' => rand(100, 999),
            ...$validated,
            'status' => 'preparation',
            'playerCount' => 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $selection,
            'message' => 'International selection created successfully'
        ], 201);
    }

    /**
     * Get a specific international selection
     */
    public function showSelection(int $selection): JsonResponse
    {
        $selectionData = [
            'id' => $selection,
            'name' => 'Coupe d\'Afrique 2024',
            'team' => 'Équipe A',
            'competition' => 'Coupe d\'Afrique',
            'status' => 'active',
            'date' => now()->addDays(30)->toISOString(),
            'playerCount' => 23,
            'players' => [
                ['id' => 1, 'name' => 'Ahmed Ben Ali', 'club' => 'Al-Ittihad'],
                ['id' => 2, 'name' => 'Karim Benzema', 'club' => 'Al-Ittihad'],
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $selectionData,
            'message' => 'International selection retrieved successfully'
        ]);
    }

    /**
     * Update an international selection
     */
    public function updateSelection(Request $request, int $selection): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'team' => 'sometimes|string|max:255',
            'competition' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
        ]);

        $selectionData = [
            'id' => $selection,
            ...$validated,
            'status' => 'active',
        ];

        return response()->json([
            'success' => true,
            'data' => $selectionData,
            'message' => 'International selection updated successfully'
        ]);
    }

    /**
     * Delete an international selection
     */
    public function destroySelection(int $selection): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'International selection deleted successfully'
        ]);
    }

    /**
     * Get all expatriate players
     */
    public function expats(): JsonResponse
    {
        $expats = [
            [
                'id' => 1,
                'name' => 'Karim Benzema',
                'club' => 'Al-Ittihad',
                'country' => 'Arabie Saoudite',
                'position' => 'Attaquant',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Hakim Ziyech',
                'club' => 'Galatasaray',
                'country' => 'Turquie',
                'position' => 'Milieu',
                'status' => 'active',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $expats,
            'message' => 'Expatriate players retrieved successfully'
        ]);
    }

    /**
     * Get a specific expatriate player
     */
    public function showExpat(int $player): JsonResponse
    {
        $playerData = [
            'id' => $player,
            'name' => 'Karim Benzema',
            'club' => 'Al-Ittihad',
            'country' => 'Arabie Saoudite',
            'position' => 'Attaquant',
            'status' => 'active',
            'medicalData' => [
                'lastCheckup' => now()->subDays(30)->toISOString(),
                'status' => 'healthy',
                'restrictions' => [],
            ],
            'performanceData' => [
                'matches' => 15,
                'goals' => 8,
                'assists' => 3,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $playerData,
            'message' => 'Expatriate player retrieved successfully'
        ]);
    }

    /**
     * Get medical data for a player
     */
    public function medicalData(int $playerId): JsonResponse
    {
        $medicalData = [
            'playerId' => $playerId,
            'lastCheckup' => now()->subDays(30)->toISOString(),
            'status' => 'healthy',
            'restrictions' => [],
            'medications' => [],
            'injuries' => [],
            'fitnessLevel' => 'excellent',
        ];

        return response()->json([
            'success' => true,
            'data' => $medicalData,
            'message' => 'Medical data retrieved successfully'
        ]);
    }

    /**
     * Send feedback to a club
     */
    public function sendFeedback(Request $request, int $playerId): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'required|string|in:medical,performance,general',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback sent successfully'
        ]);
    }

    /**
     * Export data to FIFA
     */
    public function exportToFifa(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:team,selection,player',
            'id' => 'required|integer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data exported to FIFA successfully',
            'fifaId' => 'FIFA_' . rand(100000, 999999),
        ]);
    }

    /**
     * Get FIFA calendar
     */
    public function fifaCalendar(): JsonResponse
    {
        $calendar = [
            [
                'id' => 1,
                'competition' => 'Coupe d\'Afrique 2024',
                'startDate' => now()->addDays(30)->toISOString(),
                'endDate' => now()->addDays(60)->toISOString(),
                'status' => 'confirmed',
            ],
            [
                'id' => 2,
                'competition' => 'Qualifications Mondial 2025',
                'startDate' => now()->addDays(90)->toISOString(),
                'endDate' => now()->addDays(120)->toISOString(),
                'status' => 'pending',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $calendar,
            'message' => 'FIFA calendar retrieved successfully'
        ]);
    }

    /**
     * Get DTN reports
     */
    public function reports(): JsonResponse
    {
        $reports = [
            [
                'id' => 1,
                'name' => 'Rapport Mensuel DTN',
                'type' => 'monthly',
                'date' => now()->toISOString(),
                'status' => 'completed',
            ],
            [
                'id' => 2,
                'name' => 'Rapport Sélections Internationales',
                'type' => 'selections',
                'date' => now()->subDays(7)->toISOString(),
                'status' => 'completed',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $reports,
            'message' => 'DTN reports retrieved successfully'
        ]);
    }

    /**
     * Generate a new report
     */
    public function generateReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:monthly,selections,medical,performance',
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