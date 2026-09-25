<?php

namespace App\Http\Controllers;

use App\Services\GoogleGeminiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class GoogleGeminiController extends Controller
{
    protected GoogleGeminiService $geminiService;

    public function __construct(GoogleGeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Show the Gemini AI interface
     */
    public function index(): View
    {
        $configuration = $this->geminiService->getConfiguration();
        $availableModels = $this->geminiService->getAvailableModels();
        
        return view('gemini.index', compact('configuration', 'availableModels'));
    }

    /**
     * Test Gemini API connection
     */
    public function testConnection(): JsonResponse
    {
        $result = $this->geminiService->testConnection();
        
        return response()->json($result);
    }

    /**
     * Generate medical diagnosis
     */
    public function generateDiagnosis(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'symptoms' => 'required|array|min:1',
            'symptoms.*' => 'string|max:255',
            'patient_data' => 'array',
            'patient_data.age' => 'integer|min:0|max:120',
            'patient_data.gender' => 'string|in:male,female,other',
            'patient_data.medical_history' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->geminiService->suggestDiagnosis(
            $request->input('symptoms'),
            $request->input('patient_data', [])
        );

        return response()->json($result);
    }

    /**
     * Generate treatment recommendations
     */
    public function generateTreatment(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'diagnosis' => 'required|string|max:1000',
            'patient_data' => 'array',
            'patient_data.age' => 'integer|min:0|max:120',
            'patient_data.gender' => 'string|in:male,female,other',
            'patient_data.allergies' => 'array',
            'patient_data.medications' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->geminiService->suggestTreatment(
            $request->input('diagnosis'),
            $request->input('patient_data', [])
        );

        return response()->json($result);
    }

    /**
     * Analyze performance data
     */
    public function analyzePerformance(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'performance_data' => 'required|array',
            'performance_data.metrics' => 'array',
            'performance_data.history' => 'array',
            'performance_data.goals' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->geminiService->analyzePerformanceData(
            $request->input('performance_data')
        );

        return response()->json($result);
    }

    /**
     * Predict injury risk
     */
    public function predictInjuryRisk(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'player_data' => 'required|array',
            'player_data.age' => 'integer|min:0|max:120',
            'player_data.position' => 'string|max:100',
            'player_data.injury_history' => 'array',
            'performance_history' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->geminiService->predictInjuryRisk(
            $request->input('player_data'),
            $request->input('performance_history', [])
        );

        return response()->json($result);
    }

    /**
     * Generate rehabilitation plan
     */
    public function generateRehabPlan(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'injury' => 'required|string|max:500',
            'patient_data' => 'array',
            'patient_data.severity' => 'string|in:mild,moderate,severe',
            'patient_data.duration' => 'string|max:100',
            'patient_data.previous_treatments' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->geminiService->generateRehabPlan(
            $request->input('injury'),
            $request->input('patient_data', [])
        );

        return response()->json($result);
    }

    /**
     * Analyze medical image (text-based)
     */
    public function analyzeMedicalImage(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'image_description' => 'required|string|max:2000',
            'context' => 'string|max:1000',
            'image_type' => 'string|in:xray,mri,ct,ultrasound,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->geminiService->analyzeMedicalImage(
            $request->input('image_description'),
            $request->input('context', '')
        );

        return response()->json($result);
    }

    /**
     * Generate medical report
     */
    public function generateMedicalReport(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'patient_data' => 'required|array',
            'patient_data.name' => 'string|max:255',
            'patient_data.age' => 'integer|min:0|max:120',
            'patient_data.gender' => 'string|in:male,female,other',
            'findings' => 'required|array',
            'findings.examination' => 'array',
            'findings.tests' => 'array',
            'findings.diagnosis' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->geminiService->generateMedicalReport(
            $request->input('patient_data'),
            $request->input('findings')
        );

        return response()->json($result);
    }

    /**
     * Get service configuration
     */
    public function getConfiguration(): JsonResponse
    {
        $configuration = $this->geminiService->getConfiguration();
        $availableModels = $this->geminiService->getAvailableModels();
        
        return response()->json([
            'success' => true,
            'data' => [
                'configuration' => $configuration,
                'available_models' => $availableModels,
            ],
        ]);
    }

    /**
     * Get analysis history
     */
    public function getHistory(Request $request): JsonResponse
    {
        // This would typically fetch from database
        // For now, return empty array
        return response()->json([
            'success' => true,
            'data' => [
                'analyses' => [],
                'total' => 0,
            ],
        ]);
    }
} 