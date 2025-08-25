<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PCMA;
use App\Models\Player;
use Carbon\Carbon;

class PCMAFraudDetectionService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    /**
     * Analyze PCMA data for fraud detection
     */
    public function analyzePCMAFraud(array $pCMAData, array $playerData): array
    {
        try {
            $prompt = $this->buildPCMAFraudDetectionPrompt($pCMAData, $playerData);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => 'gpt-4-turbo-preview',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a specialized medical fraud detection AI for PCMA (Physical Capacity Medical Assessment) data. Focus on age fraud, identity fraud, and medical data inconsistencies.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 1200,
                'temperature' => 0.2
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $analysis = $result['choices'][0]['message']['content'];
                
                return $this->parsePCMAFraudResponse($analysis, $pCMAData, $playerData);
            } else {
                Log::error('PCMA Fraud Detection API Error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                return $this->fallbackPCMAAnalysis($pCMAData, $playerData);
            }
            
        } catch (\Exception $e) {
            Log::error('PCMA Fraud Detection Error', [
                'error' => $e->getMessage(),
                'data' => $pCMAData
            ]);
            
            return $this->fallbackPCMAAnalysis($pCMAData, $playerData);
        }
    }

    /**
     * Build the PCMA fraud detection prompt
     */
    protected function buildPCMAFraudDetectionPrompt(array $pCMAData, array $playerData): string
    {
        $playerAge = Carbon::parse($playerData['date_of_birth'])->age;
        $assessmentAge = $pCMAData['age_at_assessment'] ?? $playerAge;
        
        return "
Analyze the following PCMA (Physical Capacity Medical Assessment) data for potential fraud:

PLAYER INFORMATION:
- Name: {$playerData['first_name']} {$playerData['last_name']}
- Date of Birth: {$playerData['date_of_birth']}
- Claimed Age: {$assessmentAge}
- Actual Age: {$playerAge}
- Nationality: {$playerData['nationality']}

PCMA ASSESSMENT DATA:
- Assessment Date: {$pCMAData['assessment_date']}
- Height: {$pCMAData['height_cm']} cm
- Weight: {$pCMAData['weight_kg']} kg
- BMI: {$pCMAData['bmi']}
- Blood Pressure: {$pCMAData['blood_pressure']}
- Heart Rate: {$pCMAData['heart_rate']} bpm
- MRI Results: {$pCMAData['mri_results']}
- ECG Results: {$pCMAData['ecg_results']}
- Fitness Test Score: {$pCMAData['fitness_test_score']}

FOCUS ON:
1. Age Fraud Detection:
   - Compare claimed age vs actual age
   - Check if MRI/ECG results match claimed age
   - Look for age-related medical inconsistencies

2. Identity Fraud Detection:
   - Check if medical data matches player identity
   - Verify if photo matches medical records
   - Look for multiple identities

3. Medical Data Consistency:
   - Check if physical measurements are realistic
   - Verify if medical results are consistent
   - Look for impossible medical patterns

Provide analysis in JSON format:
{
    \"fraud_detected\": boolean,
    \"fraud_types\": [\"age_fraud\", \"identity_fraud\", \"medical_inconsistency\"],
    \"risk_score\": number (0-100),
    \"age_discrepancy\": number,
    \"mri_age_consistency\": boolean,
    \"ecg_age_consistency\": boolean,
    \"identity_verification\": boolean,
    \"medical_consistency\": boolean,
    \"detailed_analysis\": \"detailed text\",
    \"recommendations\": \"recommendations text\"
}
        ";
    }

    /**
     * Parse GPT-4 response for PCMA fraud detection
     */
    protected function parsePCMAFraudResponse(string $response, array $pCMAData, array $playerData): array
    {
        try {
            // Try to extract JSON from response
            if (preg_match('/\{.*\}/s', $response, $matches)) {
                $jsonData = json_decode($matches[0], true);
                
                if ($jsonData && isset($jsonData['fraud_detected'])) {
                    return [
                        'fraud_detected' => $jsonData['fraud_detected'],
                        'fraud_types' => $jsonData['fraud_types'] ?? [],
                        'risk_score' => $jsonData['risk_score'] ?? 0,
                        'age_discrepancy' => $jsonData['age_discrepancy'] ?? 0,
                        'mri_age_consistency' => $jsonData['mri_age_consistency'] ?? true,
                        'ecg_age_consistency' => $jsonData['ecg_age_consistency'] ?? true,
                        'identity_verification' => $jsonData['identity_verification'] ?? true,
                        'medical_consistency' => $jsonData['medical_consistency'] ?? true,
                        'detailed_analysis' => $jsonData['detailed_analysis'] ?? $response,
                        'recommendations' => $jsonData['recommendations'] ?? '',
                        'gpt4_model' => 'gpt-4-turbo-preview',
                        'analysis_timestamp' => now()->toISOString()
                    ];
                }
            }
            
            // Fallback parsing
            return $this->parsePCMATextResponse($response, $pCMAData, $playerData);
            
        } catch (\Exception $e) {
            Log::error('PCMA Fraud Response Parsing Error', ['error' => $e->getMessage()]);
            return $this->fallbackPCMAAnalysis($pCMAData, $playerData);
        }
    }

    /**
     * Parse text response when JSON parsing fails
     */
    protected function parsePCMATextResponse(string $response, array $pCMAData, array $playerData): array
    {
        $fraudDetected = false;
        $fraudTypes = [];
        $riskScore = 0;
        
        $responseLower = strtolower($response);
        
        // Check for age fraud indicators
        if (strpos($responseLower, 'age') !== false && 
            (strpos($responseLower, 'discrepancy') !== false || strpos($responseLower, 'inconsistent') !== false)) {
            $fraudDetected = true;
            $fraudTypes[] = 'age_fraud';
            $riskScore += 40;
        }
        
        // Check for identity fraud indicators
        if (strpos($responseLower, 'identity') !== false || strpos($responseLower, 'photo') !== false) {
            $fraudDetected = true;
            $fraudTypes[] = 'identity_fraud';
            $riskScore += 35;
        }
        
        // Check for medical inconsistency
        if (strpos($responseLower, 'medical') !== false && strpos($responseLower, 'inconsistent') !== false) {
            $fraudDetected = true;
            $fraudTypes[] = 'medical_inconsistency';
            $riskScore += 25;
        }
        
        return [
            'fraud_detected' => $fraudDetected,
            'fraud_types' => $fraudTypes,
            'risk_score' => min($riskScore, 100),
            'age_discrepancy' => 0,
            'mri_age_consistency' => true,
            'ecg_age_consistency' => true,
            'identity_verification' => true,
            'medical_consistency' => true,
            'detailed_analysis' => $response,
            'recommendations' => $fraudDetected ? 'Manual verification required' : 'Proceed with approval',
            'gpt4_model' => 'gpt-4-turbo-preview',
            'analysis_timestamp' => now()->toISOString()
        ];
    }

    /**
     * Fallback analysis when GPT-4 is unavailable
     */
    protected function fallbackPCMAAnalysis(array $pCMAData, array $playerData): array
    {
        $fraudDetected = false;
        $fraudTypes = [];
        $riskScore = 0;
        $analysis = "⚠️ PCMA Fraud Analysis (Fallback Mode): ";
        
        // Age fraud detection
        $playerAge = Carbon::parse($playerData['date_of_birth'])->age;
        $claimedAge = $pCMAData['age_at_assessment'] ?? $playerAge;
        $ageDifference = abs($playerAge - $claimedAge);
        
        if ($ageDifference > 2) {
            $fraudDetected = true;
            $fraudTypes[] = 'age_fraud';
            $riskScore += 40;
            $analysis .= "Age discrepancy detected: claimed {$claimedAge}, actual {$playerAge}. ";
        }
        
        // MRI/ECG age consistency check
        $mriAgeConsistent = $this->checkMRIAgeConsistency($pCMAData, $claimedAge);
        $ecgAgeConsistent = $this->checkECGAgeConsistency($pCMAData, $claimedAge);
        
        if (!$mriAgeConsistent) {
            $fraudDetected = true;
            $fraudTypes[] = 'medical_inconsistency';
            $riskScore += 25;
            $analysis .= "MRI results inconsistent with claimed age. ";
        }
        
        if (!$ecgAgeConsistent) {
            $fraudDetected = true;
            $fraudTypes[] = 'medical_inconsistency';
            $riskScore += 25;
            $analysis .= "ECG results inconsistent with claimed age. ";
        }
        
        // Medical data consistency
        $medicalConsistent = $this->checkMedicalConsistency($pCMAData);
        if (!$medicalConsistent) {
            $fraudDetected = true;
            $fraudTypes[] = 'medical_inconsistency';
            $riskScore += 20;
            $analysis .= "Medical data inconsistencies detected. ";
        }
        
        if ($fraudDetected) {
            $analysis .= "❌ Fraud detected. Manual verification required.";
        } else {
            $analysis .= "✅ No fraud detected. Proceed with approval.";
        }
        
        return [
            'fraud_detected' => $fraudDetected,
            'fraud_types' => $fraudTypes,
            'risk_score' => $riskScore,
            'age_discrepancy' => $ageDifference,
            'mri_age_consistency' => $mriAgeConsistent,
            'ecg_age_consistency' => $ecgAgeConsistent,
            'identity_verification' => true, // Would need photo comparison
            'medical_consistency' => $medicalConsistent,
            'detailed_analysis' => $analysis,
            'recommendations' => $fraudDetected ? 'Manual verification required' : 'Proceed with approval',
            'gpt4_model' => 'fallback-analysis',
            'analysis_timestamp' => now()->toISOString()
        ];
    }

    /**
     * Check MRI age consistency
     */
    private function checkMRIAgeConsistency(array $pCMAData, int $claimedAge): bool
    {
        // Simulate MRI age consistency check
        $mriResults = strtolower($pCMAData['mri_results'] ?? '');
        
        // Check for age-related inconsistencies
        if (strpos($mriResults, 'bone density') !== false && $claimedAge < 25) {
            return false; // Bone density suggests older age
        }
        
        if (strpos($mriResults, 'degenerative') !== false && $claimedAge < 30) {
            return false; // Degenerative changes suggest older age
        }
        
        return true;
    }

    /**
     * Check ECG age consistency
     */
    private function checkECGAgeConsistency(array $pCMAData, int $claimedAge): bool
    {
        // Simulate ECG age consistency check
        $ecgResults = strtolower($pCMAData['ecg_results'] ?? '');
        
        // Check for age-related inconsistencies
        if (strpos($ecgResults, 'st segment') !== false && $claimedAge < 25) {
            return false; // ST segment changes suggest older age
        }
        
        if (strpos($ecgResults, 't wave') !== false && $claimedAge < 30) {
            return false; // T wave changes suggest older age
        }
        
        return true;
    }

    /**
     * Check medical data consistency
     */
    private function checkMedicalConsistency(array $pCMAData): bool
    {
        // Check for impossible medical combinations
        $height = $pCMAData['height_cm'] ?? 0;
        $weight = $pCMAData['weight_kg'] ?? 0;
        $bmi = $pCMAData['bmi'] ?? 0;
        
        // Check if BMI calculation is consistent
        if ($height > 0 && $weight > 0) {
            $calculatedBMI = $weight / pow($height / 100, 2);
            if (abs($calculatedBMI - $bmi) > 2) {
                return false; // BMI inconsistency
            }
        }
        
        // Check for impossible physical measurements
        if ($height < 140 || $height > 220) {
            return false; // Impossible height
        }
        
        if ($weight < 40 || $weight > 150) {
            return false; // Impossible weight for athlete
        }
        
        return true;
    }

    /**
     * Batch analyze multiple PCMA records
     */
    public function batchAnalyzePCMAFraud(): array
    {
        $pCMARecords = PCMA::with('player')->get();
        $results = [];
        
        foreach ($pCMARecords as $pCMA) {
            $playerData = [
                'first_name' => $pCMA->player->first_name ?? '',
                'last_name' => $pCMA->player->last_name ?? '',
                'date_of_birth' => $pCMA->player->date_of_birth ?? '',
                'nationality' => $pCMA->player->nationality ?? ''
            ];
            
            $pCMAData = $pCMA->toArray();
            $analysis = $this->analyzePCMAFraud($pCMAData, $playerData);
            
            $results[] = [
                'pCMA_id' => $pCMA->id,
                'player_name' => $playerData['first_name'] . ' ' . $playerData['last_name'],
                'analysis' => $analysis
            ];
        }
        
        return $results;
    }
} 