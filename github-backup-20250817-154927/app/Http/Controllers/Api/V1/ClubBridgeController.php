<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClubBridgeController extends Controller
{
    /**
     * Get medical data for a player
     */
    public function medicalData(string $fifaId): JsonResponse
    {
        $medicalData = [
            'fifaId' => $fifaId,
            'playerName' => 'Karim Benzema',
            'club' => 'Al-Ittihad',
            'lastCheckup' => now()->subDays(30)->toISOString(),
            'status' => 'healthy',
            'restrictions' => [],
            'medications' => [],
            'injuries' => [],
            'fitnessLevel' => 'excellent',
            'nextCheckup' => now()->addDays(30)->toISOString(),
        ];

        return response()->json([
            'success' => true,
            'data' => $medicalData,
            'message' => 'Medical data retrieved successfully'
        ]);
    }

    /**
     * Get training load data for a player
     */
    public function trainingLoad(string $fifaId): JsonResponse
    {
        $trainingData = [
            'fifaId' => $fifaId,
            'playerName' => 'Karim Benzema',
            'club' => 'Al-Ittihad',
            'currentWeek' => [
                'totalLoad' => 4200,
                'averageRPE' => 7.2,
                'sessions' => 5,
                'highIntensityDistance' => 1200,
                'totalDistance' => 8500,
            ],
            'lastWeek' => [
                'totalLoad' => 3800,
                'averageRPE' => 6.8,
                'sessions' => 4,
                'highIntensityDistance' => 1000,
                'totalDistance' => 7500,
            ],
            'acwr' => 1.1,
            'status' => 'normal',
        ];

        return response()->json([
            'success' => true,
            'data' => $trainingData,
            'message' => 'Training load data retrieved successfully'
        ]);
    }

    /**
     * Send feedback to a club
     */
    public function sendFeedback(Request $request, string $fifaId): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'required|string|in:medical,performance,general',
            'priority' => 'sometimes|string|in:low,medium,high,urgent',
        ]);

        $feedback = [
            'id' => rand(100, 999),
            'fifaId' => $fifaId,
            'message' => $validated['message'],
            'type' => $validated['type'],
            'priority' => $validated['priority'] ?? 'medium',
            'timestamp' => now()->toISOString(),
            'status' => 'sent',
        ];

        return response()->json([
            'success' => true,
            'data' => $feedback,
            'message' => 'Feedback sent successfully'
        ]);
    }
} 