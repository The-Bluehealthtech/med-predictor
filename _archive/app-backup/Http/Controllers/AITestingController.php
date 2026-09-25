<?php

namespace App\Http\Controllers;

use App\Services\AITestingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AITestingController extends Controller
{
    protected AITestingService $aiTestingService;

    public function __construct(AITestingService $aiTestingService)
    {
        $this->aiTestingService = $aiTestingService;
    }

    /**
     * Show the AI testing dashboard
     */
    public function index(): View
    {
        $providers = $this->aiTestingService->getAvailableProviders();
        
        return view('ai-testing.index', compact('providers'));
    }

    /**
     * Run comprehensive AI tests
     */
    public function runTests(): JsonResponse
    {
        try {
            $results = $this->aiTestingService->testAllProviders();
            
            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'AI testing completed successfully',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI testing failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test a specific provider
     */
    public function testProvider(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required|string',
            'prompt' => 'required|string|max:2000',
        ]);

        try {
            $results = $this->aiTestingService->testSingleProvider(
                $request->provider,
                $request->prompt
            );
            
            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Provider test completed',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provider test failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available providers
     */
    public function getProviders(): JsonResponse
    {
        $providers = $this->aiTestingService->getAvailableProviders();
        
        return response()->json([
            'success' => true,
            'data' => $providers,
        ]);
    }

    /**
     * Test medical diagnosis scenario
     */
    public function testMedicalDiagnosis(): JsonResponse
    {
        $prompt = "Analyze the following medical symptoms and provide a preliminary diagnosis: Patient reports fatigue, muscle weakness, and joint pain. Blood pressure is elevated at 140/90. What are the possible causes and recommended next steps?";
        
        try {
            $results = $this->aiTestingService->testAllProviders();
            
            // Filter for medical diagnosis results
            $medicalResults = [];
            foreach ($results['results'] as $providerKey => $providerResults) {
                if (isset($providerResults['medical_diagnosis'])) {
                    $medicalResults[$providerKey] = $providerResults['medical_diagnosis'];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $medicalResults,
                'prompt' => $prompt,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medical diagnosis test failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test performance analysis scenario
     */
    public function testPerformanceAnalysis(): JsonResponse
    {
        $prompt = "Analyze this athlete performance data: VO2 max: 65 ml/kg/min, Heart rate recovery: 25 bpm in 1 minute, Sprint speed: 10.2 m/s. What does this indicate about their fitness level and what recommendations would you make?";
        
        try {
            $results = $this->aiTestingService->testAllProviders();
            
            // Filter for performance analysis results
            $performanceResults = [];
            foreach ($results['results'] as $providerKey => $providerResults) {
                if (isset($providerResults['performance_analysis'])) {
                    $performanceResults[$providerKey] = $providerResults['performance_analysis'];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $performanceResults,
                'prompt' => $prompt,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Performance analysis test failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test injury prediction scenario
     */
    public function testInjuryPrediction(): JsonResponse
    {
        $prompt = "Based on this athlete data: Age 24, Previous ACL injury, Current training load 85% of max, Recent fatigue score 7/10. What is the injury risk assessment and prevention recommendations?";
        
        try {
            $results = $this->aiTestingService->testAllProviders();
            
            // Filter for injury prediction results
            $injuryResults = [];
            foreach ($results['results'] as $providerKey => $providerResults) {
                if (isset($providerResults['injury_prediction'])) {
                    $injuryResults[$providerKey] = $providerResults['injury_prediction'];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $injuryResults,
                'prompt' => $prompt,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Injury prediction test failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get test results summary
     */
    public function getSummary(): JsonResponse
    {
        try {
            $results = $this->aiTestingService->testAllProviders();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $results['summary'],
                    'recommendations' => $results['recommendations'],
                    'timestamp' => $results['timestamp'],
                ],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get summary: ' . $e->getMessage(),
            ], 500);
        }
    }
} 