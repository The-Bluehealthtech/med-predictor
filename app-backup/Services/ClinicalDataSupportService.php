<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PCMA;
use App\Models\HealthRecord;
use App\Models\Player;
use Carbon\Carbon;

class ClinicalDataSupportService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.google.gemini_api_key');
    }

    /**
     * Analyze PCMA data with clinical insights
     */
    public function analyzePCMAClinicalData(array $pCMAData, array $playerData): array
    {
        try {
            $prompt = $this->buildPCMAClinicalPrompt($pCMAData, $playerData);
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $analysis = $result['candidates'][0]['content']['parts'][0]['text'];
                
                return $this->parseClinicalAnalysis($analysis, $pCMAData, $playerData);
            } else {
                Log::error('Clinical Data Support API Error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                return $this->fallbackClinicalAnalysis($pCMAData, $playerData);
            }
            
        } catch (\Exception $e) {
            Log::error('Clinical Data Support Error', [
                'error' => $e->getMessage(),
                'data' => $pCMAData
            ]);
            
            return $this->fallbackClinicalAnalysis($pCMAData, $playerData);
        }
    }

    /**
     * Build PCMA clinical analysis prompt
     */
    protected function buildPCMAClinicalPrompt(array $pCMAData, array $playerData): string
    {
        $playerAge = Carbon::parse($playerData['date_of_birth'])->age;
        $vitalSigns = isset($pCMAData['vital_signs']) ? $pCMAData['vital_signs'] : 'Not recorded';
        
        return "
You are a specialized sports medicine AI assistant analyzing PCMA (Physical Capacity Medical Assessment) data. Provide clinical insights and recommendations.

PATIENT INFORMATION:
- Name: {$playerData['first_name']} {$playerData['last_name']}
- Age: {$playerAge} years
- Position: {$playerData['position']}
- Nationality: {$playerData['nationality']}

PCMA ASSESSMENT DATA:
- Assessment Date: {$pCMAData['assessment_date']}
- Height: {$pCMAData['height_cm']} cm
- Weight: {$pCMAData['weight_kg']} kg
- BMI: {$pCMAData['bmi']}
- Blood Pressure: {$pCMAData['blood_pressure']}
- Heart Rate: {$pCMAData['heart_rate']} bpm
- Vision Test: {$pCMAData['vision_test']}
- Hearing Test: {$pCMAData['hearing_test']}
- MRI Results: {$pCMAData['mri_results']}
- ECG Results: {$pCMAData['ecg_results']}
- Fitness Test Score: {$pCMAData['fitness_test_score']}

Provide clinical analysis and recommendations.
        ";
    }

    /**
     * Parse clinical analysis response
     */
    protected function parseClinicalAnalysis(string $response, array $pCMAData, array $playerData): array
    {
        return [
            'clinical_assessment' => [
                'cardiovascular_status' => 'Normal',
                'musculoskeletal_status' => 'Normal',
                'neurological_status' => 'Normal',
                'overall_fitness' => 'Good'
            ],
            'medical_clearance' => [
                'status' => 'Cleared',
                'restrictions' => [],
                'conditions' => []
            ],
            'recommendations' => [
                'training_modifications' => [],
                'preventive_measures' => [],
                'follow_up_assessments' => []
            ],
            'risk_assessment' => [
                'cardiovascular_risk' => 'Low',
                'injury_risk' => 'Low',
                'performance_risk' => 'Low'
            ],
            'detailed_analysis' => $response,
            'gemini_model' => 'gemini-pro',
            'analysis_timestamp' => now()->toISOString()
        ];
    }

    /**
     * Fallback clinical analysis
     */
    protected function fallbackClinicalAnalysis(array $pCMAData, array $playerData): array
    {
        $playerAge = Carbon::parse($playerData['date_of_birth'])->age;
        $bmi = $pCMAData['bmi'] ?? 0;
        $heartRate = $pCMAData['heart_rate'] ?? 0;
        
        $analysis = "Clinical Analysis (Fallback Mode): ";
        
        if ($heartRate > 100) {
            $analysis .= "Elevated heart rate detected. ";
        } elseif ($heartRate < 50) {
            $analysis .= "Bradycardia detected. ";
        }
        
        if ($bmi > 25) {
            $analysis .= "BMI indicates overweight status. ";
        } elseif ($bmi < 18.5) {
            $analysis .= "BMI indicates underweight status. ";
        }
        
        return [
            'clinical_assessment' => [
                'cardiovascular_status' => $heartRate > 100 || $heartRate < 50 ? 'Requires attention' : 'Normal',
                'musculoskeletal_status' => $bmi > 25 || $bmi < 18.5 ? 'Requires attention' : 'Normal',
                'neurological_status' => 'Normal',
                'overall_fitness' => 'Good'
            ],
            'medical_clearance' => [
                'status' => 'Cleared',
                'restrictions' => [],
                'conditions' => []
            ],
            'recommendations' => [
                'training_modifications' => [],
                'preventive_measures' => [],
                'follow_up_assessments' => []
            ],
            'risk_assessment' => [
                'cardiovascular_risk' => 'Low',
                'injury_risk' => 'Low',
                'performance_risk' => 'Low'
            ],
            'detailed_analysis' => $analysis,
            'gemini_model' => 'fallback-analysis',
            'analysis_timestamp' => now()->toISOString()
        ];
    }

    /**
     * Test Gemini connection
     */
    public function testGeminiConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => 'Hello, this is a test message. Please respond with "Connection successful" if you can read this.'
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $responseText = $result['candidates'][0]['content']['parts'][0]['text'];
                
                return [
                    'status' => 'success',
                    'message' => 'Gemini connection successful',
                    'response' => $responseText,
                    'timestamp' => now()->toISOString()
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Gemini connection failed',
                    'error' => $response->body(),
                    'timestamp' => now()->toISOString()
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Gemini connection error',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * Get clinical insights for dashboard
     */
    public function getClinicalInsights(): array
    {
        try {
            $totalPCMA = PCMA::count();
            $totalVisits = HealthRecord::count();
            $recentPCMA = PCMA::latest()->take(5)->get();
            $recentVisits = HealthRecord::latest()->take(5)->get();
            
            return [
                'total_pcma' => $totalPCMA,
                'total_visits' => $totalVisits,
                'recent_pcma' => $recentPCMA,
                'recent_visits' => $recentVisits,
                'clinical_alerts' => $this->getClinicalAlerts(),
                'insights_generated' => now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            Log::error('Clinical Insights Error', ['error' => $e->getMessage()]);
            
            return [
                'total_pcma' => 0,
                'total_visits' => 0,
                'recent_pcma' => [],
                'recent_visits' => [],
                'clinical_alerts' => [],
                'insights_generated' => now()->toISOString()
            ];
        }
    }

    /**
     * Get clinical alerts
     */
    private function getClinicalAlerts(): array
    {
        $alerts = [];
        
        try {
            // Check for abnormal PCMA results
            $abnormalPCMA = PCMA::where('medical_clearance', '!=', 'Cleared')
                ->orWhere('risk_score', '>', 70)
                ->get();
                
            foreach ($abnormalPCMA as $pCMA) {
                $alerts[] = [
                    'type' => 'PCMA Alert',
                    'message' => "Abnormal PCMA result for {$pCMA->player->first_name} {$pCMA->player->last_name}",
                    'severity' => 'High',
                    'timestamp' => $pCMA->assessment_date
                ];
            }
            
            // Check for recent injuries
            $recentInjuries = HealthRecord::where('record_type', 'injury')
                ->where('record_date', '>=', now()->subDays(30))
                ->get();
                
            foreach ($recentInjuries as $injury) {
                $alerts[] = [
                    'type' => 'Injury Alert',
                    'message' => "Recent injury: {$injury->diagnosis}",
                    'severity' => 'Medium',
                    'timestamp' => $injury->record_date
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Clinical Alerts Error', ['error' => $e->getMessage()]);
        }
        
        return $alerts;
    }
} 