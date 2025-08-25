<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ClinicalDataSupportService;
use App\Models\PCMA;
use App\Models\HealthRecord;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClinicalDataSupportController extends Controller
{
    protected $clinicalService;

    public function __construct(ClinicalDataSupportService $clinicalService)
    {
        $this->clinicalService = $clinicalService;
    }

    /**
     * Show Clinical Data Support dashboard
     */
    public function index()
    {
        $insights = $this->clinicalService->getClinicalInsights();
        
        return view('modules.clinical.support-dashboard', compact('insights'));
    }

    /**
     * Analyze PCMA with clinical insights
     */
    public function analyzePCMA(Request $request, $pCMAId): JsonResponse
    {
        try {
            $pCMA = PCMA::with('player')->findOrFail($pCMAId);
            
            $playerData = [
                'first_name' => $pCMA->player->first_name ?? '',
                'last_name' => $pCMA->player->last_name ?? '',
                'date_of_birth' => $pCMA->player->date_of_birth ?? '',
                'position' => $pCMA->player->position ?? '',
                'nationality' => $pCMA->player->nationality ?? ''
            ];
            
            $pCMAData = $pCMA->toArray();
            
            // Perform Gemini clinical analysis
            $clinicalAnalysis = $this->clinicalService->analyzePCMAClinicalData($pCMAData, $playerData);
            
            // Update PCMA record with clinical analysis
            $pCMA->update([
                'clinical_analysis' => json_encode($clinicalAnalysis),
                'clinical_insights' => $clinicalAnalysis['detailed_analysis'],
                'medical_clearance' => $clinicalAnalysis['medical_clearance']['status'] ?? 'Pending',
                'clinical_recommendations' => json_encode($clinicalAnalysis['recommendations'] ?? [])
            ]);
            
            Log::info('PCMA Clinical Analysis Completed', [
                'pCMA_id' => $pCMAId,
                'medical_clearance' => $clinicalAnalysis['medical_clearance']['status'] ?? 'Unknown'
            ]);
            
            return response()->json($clinicalAnalysis);
            
        } catch (\Exception $e) {
            Log::error('PCMA Clinical Analysis Error', [
                'error' => $e->getMessage(),
                'pCMA_id' => $pCMAId
            ]);
            
            return response()->json([
                'error' => 'Failed to analyze PCMA clinical data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze visit with clinical insights
     */
    public function analyzeVisit(Request $request, $visitId): JsonResponse
    {
        try {
            $visit = HealthRecord::with('player')->findOrFail($visitId);
            
            $playerData = [
                'first_name' => $visit->player->first_name ?? '',
                'last_name' => $visit->player->last_name ?? '',
                'date_of_birth' => $visit->player->date_of_birth ?? '',
                'position' => $visit->player->position ?? '',
                'nationality' => $visit->player->nationality ?? ''
            ];
            
            $visitData = $visit->toArray();
            
            // Perform Gemini clinical analysis
            $clinicalAnalysis = $this->clinicalService->analyzeVisitClinicalData($visitData, $playerData);
            
            // Update visit record with clinical analysis
            $visit->update([
                'clinical_analysis' => json_encode($clinicalAnalysis),
                'clinical_insights' => $clinicalAnalysis['detailed_analysis'],
                'treatment_evaluation' => json_encode($clinicalAnalysis['treatment_evaluation'] ?? []),
                'recovery_assessment' => json_encode($clinicalAnalysis['recovery_assessment'] ?? [])
            ]);
            
            Log::info('Visit Clinical Analysis Completed', [
                'visit_id' => $visitId,
                'diagnosis' => $visit->diagnosis
            ]);
            
            return response()->json($clinicalAnalysis);
            
        } catch (\Exception $e) {
            Log::error('Visit Clinical Analysis Error', [
                'error' => $e->getMessage(),
                'visit_id' => $visitId
            ]);
            
            return response()->json([
                'error' => 'Failed to analyze visit clinical data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch analyze all PCMA records
     */
    public function batchAnalyzePCMA(): JsonResponse
    {
        try {
            $pCMARecords = PCMA::with('player')->get();
            $results = [];
            
            foreach ($pCMARecords as $pCMA) {
                $playerData = [
                    'first_name' => $pCMA->player->first_name ?? '',
                    'last_name' => $pCMA->player->last_name ?? '',
                    'date_of_birth' => $pCMA->player->date_of_birth ?? '',
                    'position' => $pCMA->player->position ?? '',
                    'nationality' => $pCMA->player->nationality ?? ''
                ];
                
                $pCMAData = $pCMA->toArray();
                $analysis = $this->clinicalService->analyzePCMAClinicalData($pCMAData, $playerData);
                
                $results[] = [
                    'pCMA_id' => $pCMA->id,
                    'player_name' => $playerData['first_name'] . ' ' . $playerData['last_name'],
                    'analysis' => $analysis
                ];
            }
            
            $summary = [
                'total_analyzed' => count($results),
                'cleared_cases' => count(array_filter($results, fn($r) => 
                    ($r['analysis']['medical_clearance']['status'] ?? '') === 'Cleared'
                )),
                'restricted_cases' => count(array_filter($results, fn($r) => 
                    !empty($r['analysis']['medical_clearance']['restrictions'])
                )),
                'high_risk_cases' => count(array_filter($results, fn($r) => 
                    ($r['analysis']['risk_assessment']['cardiovascular_risk'] ?? '') === 'High' ||
                    ($r['analysis']['risk_assessment']['injury_risk'] ?? '') === 'High'
                ))
            ];
            
            Log::info('PCMA Batch Clinical Analysis Completed', $summary);
            
            return response()->json([
                'results' => $results,
                'summary' => $summary
            ]);
            
        } catch (\Exception $e) {
            Log::error('PCMA Batch Clinical Analysis Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to perform batch PCMA analysis',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch analyze all visit records
     */
    public function batchAnalyzeVisits(): JsonResponse
    {
        try {
            $visitRecords = HealthRecord::with('player')->get();
            $results = [];
            
            foreach ($visitRecords as $visit) {
                $playerData = [
                    'first_name' => $visit->player->first_name ?? '',
                    'last_name' => $visit->player->last_name ?? '',
                    'date_of_birth' => $visit->player->date_of_birth ?? '',
                    'position' => $visit->player->position ?? '',
                    'nationality' => $visit->player->nationality ?? ''
                ];
                
                $visitData = $visit->toArray();
                $analysis = $this->clinicalService->analyzeVisitClinicalData($visitData, $playerData);
                
                $results[] = [
                    'visit_id' => $visit->id,
                    'player_name' => $playerData['first_name'] . ' ' . $playerData['last_name'],
                    'diagnosis' => $visit->diagnosis,
                    'analysis' => $analysis
                ];
            }
            
            $summary = [
                'total_analyzed' => count($results),
                'injury_cases' => count(array_filter($results, fn($r) => 
                    strpos(strtolower($r['diagnosis']), 'injury') !== false
                )),
                'illness_cases' => count(array_filter($results, fn($r) => 
                    strpos(strtolower($r['diagnosis']), 'illness') !== false
                )),
                'preventive_cases' => count(array_filter($results, fn($r) => 
                    strpos(strtolower($r['diagnosis']), 'checkup') !== false
                ))
            ];
            
            Log::info('Visit Batch Clinical Analysis Completed', $summary);
            
            return response()->json([
                'results' => $results,
                'summary' => $summary
            ]);
            
        } catch (\Exception $e) {
            Log::error('Visit Batch Clinical Analysis Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to perform batch visit analysis',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Gemini connection
     */
    public function testGeminiConnection(): JsonResponse
    {
        try {
            $result = $this->clinicalService->testGeminiConnection();
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Gemini Connection Test Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to test Gemini connection',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get clinical statistics
     */
    public function getClinicalStats(): JsonResponse
    {
        try {
            $stats = [
                'total_pcma' => PCMA::count(),
                'total_visits' => HealthRecord::count(),
                'cleared_pcma' => PCMA::where('medical_clearance', 'Cleared')->count(),
                'restricted_pcma' => PCMA::where('medical_clearance', '!=', 'Cleared')->count(),
                'recent_injuries' => HealthRecord::where('record_type', 'injury')
                    ->where('record_date', '>=', now()->subDays(30))
                    ->count(),
                'recent_illnesses' => HealthRecord::where('record_type', 'illness')
                    ->where('record_date', '>=', now()->subDays(30))
                    ->count(),
                'high_risk_cases' => PCMA::where('risk_score', '>', 70)->count(),
                'clinical_alerts' => count($this->clinicalService->getClinicalAlerts())
            ];
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            Log::error('Clinical Stats Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to fetch clinical statistics'
            ], 500);
        }
    }

    /**
     * Get clinical recommendations
     */
    public function getClinicalRecommendations(): JsonResponse
    {
        try {
            $recommendations = [
                'preventive_measures' => [
                    'Regular cardiovascular screening for players over 25',
                    'Annual musculoskeletal assessments',
                    'Nutrition counseling for optimal performance',
                    'Mental health support programs'
                ],
                'training_modifications' => [
                    'Gradual intensity progression for new players',
                    'Recovery protocols for injured players',
                    'Position-specific training programs',
                    'Age-appropriate training regimens'
                ],
                'monitoring_parameters' => [
                    'Heart rate variability tracking',
                    'Sleep quality monitoring',
                    'Nutrition intake tracking',
                    'Stress level assessment'
                ],
                'emergency_protocols' => [
                    'Cardiac emergency response plan',
                    'Concussion management protocol',
                    'Heat illness prevention',
                    'Injury assessment guidelines'
                ]
            ];
            
            return response()->json($recommendations);
            
        } catch (\Exception $e) {
            Log::error('Clinical Recommendations Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to fetch clinical recommendations'
            ], 500);
        }
    }

    /**
     * Generate clinical report
     */
    public function generateClinicalReport(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', now()->format('Y-m-d'));
            
            $pCMARecords = PCMA::whereBetween('assessment_date', [$startDate, $endDate])->get();
            $visitRecords = HealthRecord::whereBetween('record_date', [$startDate, $endDate])->get();
            
            $report = [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'pCMA_summary' => [
                    'total_assessments' => $pCMARecords->count(),
                    'cleared_cases' => $pCMARecords->where('medical_clearance', 'Cleared')->count(),
                    'restricted_cases' => $pCMARecords->where('medical_clearance', '!=', 'Cleared')->count(),
                    'average_risk_score' => $pCMARecords->avg('risk_score') ?? 0
                ],
                'visit_summary' => [
                    'total_visits' => $visitRecords->count(),
                    'injury_visits' => $visitRecords->where('record_type', 'injury')->count(),
                    'illness_visits' => $visitRecords->where('record_type', 'illness')->count(),
                    'preventive_visits' => $visitRecords->where('record_type', 'checkup')->count()
                ],
                'clinical_insights' => $this->generateClinicalInsights($pCMARecords, $visitRecords),
                'recommendations' => $this->generateRecommendations($pCMARecords, $visitRecords),
                'report_generated' => now()->toISOString()
            ];
            
            return response()->json($report);
            
        } catch (\Exception $e) {
            Log::error('Clinical Report Generation Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to generate clinical report'
            ], 500);
        }
    }

    /**
     * Generate clinical insights from data
     */
    private function generateClinicalInsights($pCMARecords, $visitRecords): array
    {
        $insights = [];
        
        // PCMA insights
        $highRiskPCMA = $pCMARecords->where('risk_score', '>', 70);
        if ($highRiskPCMA->count() > 0) {
            $insights[] = [
                'type' => 'High Risk PCMA',
                'message' => "{$highRiskPCMA->count()} high-risk PCMA cases identified",
                'severity' => 'High'
            ];
        }
        
        // Injury insights
        $recentInjuries = $visitRecords->where('record_type', 'injury')
            ->where('record_date', '>=', now()->subDays(30));
        if ($recentInjuries->count() > 0) {
            $insights[] = [
                'type' => 'Recent Injuries',
                'message' => "{$recentInjuries->count()} injuries in the last 30 days",
                'severity' => 'Medium'
            ];
        }
        
        return $insights;
    }

    /**
     * Generate recommendations from data
     */
    private function generateRecommendations($pCMARecords, $visitRecords): array
    {
        $recommendations = [];
        
        // Based on PCMA data
        $restrictedPCMA = $pCMARecords->where('medical_clearance', '!=', 'Cleared');
        if ($restrictedPCMA->count() > 0) {
            $recommendations[] = 'Implement enhanced monitoring for restricted players';
        }
        
        // Based on visit data
        $injuryVisits = $visitRecords->where('record_type', 'injury');
        if ($injuryVisits->count() > 0) {
            $recommendations[] = 'Strengthen injury prevention programs';
        }
        
        return $recommendations;
    }
} 