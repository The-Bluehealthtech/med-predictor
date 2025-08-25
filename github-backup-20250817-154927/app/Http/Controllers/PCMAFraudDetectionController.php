<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\PCMAFraudDetectionService;
use App\Models\PCMA;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PCMAFraudDetectionController extends Controller
{
    protected $fraudDetectionService;

    public function __construct(PCMAFraudDetectionService $fraudDetectionService)
    {
        $this->fraudDetectionService = $fraudDetectionService;
    }

    /**
     * Show PCMA fraud detection dashboard
     */
    public function index()
    {
        $fraudulentPCMA = PCMA::where('fraud_type', '!=', null)
            ->with('player')
            ->latest()
            ->get();

        $fraudStats = [
            'total_pcma' => PCMA::count(),
            'fraudulent_pcma' => PCMA::where('fraud_type', '!=', null)->count(),
            'age_fraud' => PCMA::where('fraud_type', 'like', '%age%')->count(),
            'identity_fraud' => PCMA::where('fraud_type', 'like', '%identity%')->count(),
            'medical_inconsistency' => PCMA::where('fraud_type', 'like', '%medical%')->count()
        ];

        return view('modules.pcma.fraud-detection', compact('fraudulentPCMA', 'fraudStats'));
    }

    /**
     * Analyze specific PCMA record for fraud
     */
    public function analyzePCMA(Request $request, $pCMAId): JsonResponse
    {
        try {
            $pCMA = PCMA::with('player')->findOrFail($pCMAId);
            
            $playerData = [
                'first_name' => $pCMA->player->first_name ?? '',
                'last_name' => $pCMA->player->last_name ?? '',
                'date_of_birth' => $pCMA->player->date_of_birth ?? '',
                'nationality' => $pCMA->player->nationality ?? ''
            ];
            
            $pCMAData = $pCMA->toArray();
            
            // Perform GPT-4 fraud detection
            $fraudAnalysis = $this->fraudDetectionService->analyzePCMAFraud($pCMAData, $playerData);
            
            // Update PCMA record with fraud analysis
            $pCMA->update([
                'fraud_analysis' => json_encode($fraudAnalysis),
                'fraud_detected' => $fraudAnalysis['fraud_detected'],
                'risk_score' => $fraudAnalysis['risk_score'],
                'status' => $fraudAnalysis['fraud_detected'] ? 'flagged' : 'approved'
            ]);
            
            Log::info('PCMA Fraud Analysis Completed', [
                'pCMA_id' => $pCMAId,
                'fraud_detected' => $fraudAnalysis['fraud_detected'],
                'risk_score' => $fraudAnalysis['risk_score']
            ]);
            
            return response()->json($fraudAnalysis);
            
        } catch (\Exception $e) {
            Log::error('PCMA Fraud Analysis Error', [
                'error' => $e->getMessage(),
                'pCMA_id' => $pCMAId
            ]);
            
            return response()->json([
                'error' => 'Failed to analyze PCMA fraud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch analyze all PCMA records
     */
    public function batchAnalyze(): JsonResponse
    {
        try {
            $results = $this->fraudDetectionService->batchAnalyzePCMAFraud();
            
            $summary = [
                'total_analyzed' => count($results),
                'fraud_detected' => count(array_filter($results, fn($r) => $r['analysis']['fraud_detected'])),
                'average_risk_score' => array_sum(array_column($results, 'analysis.risk_score')) / count($results),
                'fraud_types' => $this->getFraudTypeSummary($results)
            ];
            
            Log::info('PCMA Batch Fraud Analysis Completed', $summary);
            
            return response()->json([
                'results' => $results,
                'summary' => $summary
            ]);
            
        } catch (\Exception $e) {
            Log::error('PCMA Batch Analysis Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to perform batch analysis',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fraud statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_pcma' => PCMA::count(),
                'fraudulent_pcma' => PCMA::where('fraud_detected', true)->count(),
                'age_fraud' => PCMA::where('fraud_type', 'like', '%age%')->count(),
                'identity_fraud' => PCMA::where('fraud_type', 'like', '%identity%')->count(),
                'medical_inconsistency' => PCMA::where('fraud_type', 'like', '%medical%')->count(),
                'average_risk_score' => PCMA::whereNotNull('risk_score')->avg('risk_score') ?? 0,
                'high_risk_cases' => PCMA::where('risk_score', '>', 70)->count(),
                'flagged_cases' => PCMA::where('status', 'flagged')->count(),
                'rejected_cases' => PCMA::where('status', 'rejected')->count()
            ];
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            Log::error('PCMA Stats Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to fetch PCMA fraud statistics'
            ], 500);
        }
    }

    /**
     * Get detailed fraud report
     */
    public function getFraudReport(): JsonResponse
    {
        try {
            $fraudulentPCMA = PCMA::where('fraud_detected', true)
                ->with('player')
                ->get()
                ->map(function ($pCMA) {
                    $analysis = json_decode($pCMA->fraud_analysis ?? '{}', true);
                    return [
                        'id' => $pCMA->id,
                        'player_name' => $pCMA->player->first_name . ' ' . $pCMA->player->last_name,
                        'fraud_type' => $pCMA->fraud_type,
                        'risk_score' => $pCMA->risk_score,
                        'status' => $pCMA->status,
                        'assessment_date' => $pCMA->assessment_date,
                        'fraud_indicators' => json_decode($pCMA->fraud_indicators ?? '[]', true),
                        'analysis' => $analysis
                    ];
                });
            
            return response()->json([
                'fraudulent_records' => $fraudulentPCMA,
                'total_fraudulent' => $fraudulentPCMA->count(),
                'report_generated' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('PCMA Fraud Report Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to generate fraud report'
            ], 500);
        }
    }

    /**
     * Test PCMA fraud detection with sample data
     */
    public function testFraudDetection(): JsonResponse
    {
        try {
            // Test with sample fraudulent data
            $samplePCMAData = [
                'assessment_date' => now()->subDays(30),
                'age_at_assessment' => 19,
                'height_cm' => 175,
                'weight_kg' => 70,
                'bmi' => 22.9,
                'blood_pressure' => '120/80',
                'heart_rate' => 72,
                'mri_results' => 'Normal - Age inconsistent with MRI findings',
                'ecg_results' => 'Normal - Age inconsistent with ECG findings',
                'fitness_test_score' => 85
            ];
            
            $samplePlayerData = [
                'first_name' => 'Test',
                'last_name' => 'Player',
                'date_of_birth' => '1999-03-15', // Actually 25 years old
                'nationality' => 'Tunisian'
            ];
            
            $analysis = $this->fraudDetectionService->analyzePCMAFraud($samplePCMAData, $samplePlayerData);
            
            return response()->json([
                'test_data' => [
                    'pCMA' => $samplePCMAData,
                    'player' => $samplePlayerData
                ],
                'analysis' => $analysis,
                'test_completed' => true
            ]);
            
        } catch (\Exception $e) {
            Log::error('PCMA Test Fraud Detection Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to test fraud detection',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fraud type summary from batch analysis results
     */
    private function getFraudTypeSummary(array $results): array
    {
        $fraudTypes = [];
        
        foreach ($results as $result) {
            if (isset($result['analysis']['fraud_types'])) {
                foreach ($result['analysis']['fraud_types'] as $type) {
                    $fraudTypes[$type] = ($fraudTypes[$type] ?? 0) + 1;
                }
            }
        }
        
        return $fraudTypes;
    }
} 