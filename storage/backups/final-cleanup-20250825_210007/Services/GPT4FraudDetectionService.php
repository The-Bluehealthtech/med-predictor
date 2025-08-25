<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GPT4FraudDetectionService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    /**
     * Analyze association registration data for fraud detection
     */
    public function analyzeAssociationRegistration(array $data): array
    {
        try {
            $prompt = $this->buildFraudDetectionPrompt($data);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => 'gpt-4-turbo-preview',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional fraud detection AI specialized in analyzing football association registration data. Provide detailed analysis with risk scores and recommendations.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.3
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $analysis = $result['choices'][0]['message']['content'];
                
                return $this->parseGPT4Response($analysis, $data);
            } else {
                Log::error('GPT-4 API Error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                return $this->fallbackAnalysis($data);
            }
            
        } catch (\Exception $e) {
            Log::error('GPT-4 Fraud Detection Error', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            return $this->fallbackAnalysis($data);
        }
    }

    /**
     * Build the fraud detection prompt for GPT-4
     */
    protected function buildFraudDetectionPrompt(array $data): string
    {
        return "
Analyze the following football association registration data for potential fraud indicators:

Association Name: {$data['association_name']}
Association Type: {$data['association_type']}
Contact Email: {$data['contact_email']}
Contact Phone: {$data['contact_phone']}
Address: {$data['address']}
Country: {$data['country']}
Description: {$data['description']}

Please provide a comprehensive fraud analysis including:

1. Risk Score (0-100): Evaluate the overall risk level
2. Fraud Indicators: List any suspicious patterns or red flags
3. Data Validation: Check for completeness and consistency
4. Recommendations: Suggest next steps for verification

Format your response as JSON with the following structure:
{
    \"risk_score\": number,
    \"status\": \"approved\" or \"rejected\",
    \"analysis\": \"detailed analysis text\",
    \"fraud_indicators\": [\"list of indicators\"],
    \"recommendations\": \"recommendations text\"
}

Focus on:
- Email validity and suspicious patterns
- Phone number format and validity
- Address completeness and consistency
- Association name authenticity
- Geographic consistency
- Data completeness
        ";
    }

    /**
     * Parse GPT-4 response and extract structured data
     */
    protected function parseGPT4Response(string $response, array $data): array
    {
        try {
            // Try to extract JSON from response
            if (preg_match('/\{.*\}/s', $response, $matches)) {
                $jsonData = json_decode($matches[0], true);
                
                if ($jsonData && isset($jsonData['risk_score'])) {
                    return [
                        'risk_score' => $jsonData['risk_score'],
                        'status' => $jsonData['status'] ?? 'approved',
                        'analysis' => $jsonData['analysis'] ?? $response,
                        'fraud_indicators' => $jsonData['fraud_indicators'] ?? [],
                        'recommendations' => $jsonData['recommendations'] ?? '',
                        'gpt4_model' => 'gpt-4-turbo-preview',
                        'analysis_timestamp' => now()->toISOString()
                    ];
                }
            }
            
            // Fallback parsing
            return $this->parseTextResponse($response, $data);
            
        } catch (\Exception $e) {
            Log::error('GPT-4 Response Parsing Error', ['error' => $e->getMessage()]);
            return $this->fallbackAnalysis($data);
        }
    }

    /**
     * Parse text response when JSON parsing fails
     */
    protected function parseTextResponse(string $response, array $data): array
    {
        $riskScore = 0;
        $status = 'approved';
        
        // Simple keyword-based analysis
        $suspiciousKeywords = ['test', 'fake', 'temp', 'invalid', 'suspicious'];
        $responseLower = strtolower($response);
        
        foreach ($suspiciousKeywords as $keyword) {
            if (strpos($responseLower, $keyword) !== false) {
                $riskScore += 20;
            }
        }
        
        if (strpos($responseLower, 'reject') !== false || strpos($responseLower, 'fraud') !== false) {
            $status = 'rejected';
            $riskScore = max($riskScore, 70);
        }
        
        return [
            'risk_score' => min($riskScore, 100),
            'status' => $status,
            'analysis' => $response,
            'fraud_indicators' => [],
            'recommendations' => $status === 'approved' ? 'Proceed with registration' : 'Manual verification required',
            'gpt4_model' => 'gpt-4-turbo-preview',
            'analysis_timestamp' => now()->toISOString()
        ];
    }

    /**
     * Fallback analysis when GPT-4 is unavailable
     */
    protected function fallbackAnalysis(array $data): array
    {
        $riskScore = 0;
        $analysis = "⚠️ Analyse de sécurité (mode fallback): ";
        $fraudIndicators = [];
        
        // Basic validation checks
        if (empty($data['association_name']) || strlen($data['association_name']) < 3) {
            $riskScore += 30;
            $fraudIndicators[] = "Nom d'association invalide";
            $analysis .= "Nom d'association trop court ou vide. ";
        }
        
        if (!filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
            $riskScore += 25;
            $fraudIndicators[] = "Email invalide";
            $analysis .= "Email de contact invalide. ";
        }
        
        if (empty($data['contact_phone']) || strlen($data['contact_phone']) < 8) {
            $riskScore += 20;
            $fraudIndicators[] = "Téléphone invalide";
            $analysis .= "Numéro de téléphone invalide. ";
        }
        
        if (empty($data['address']) || strlen($data['address']) < 10) {
            $riskScore += 15;
            $fraudIndicators[] = "Adresse incomplète";
            $analysis .= "Adresse incomplète. ";
        }
        
        // Check for suspicious patterns
        if (strpos(strtolower($data['contact_email']), 'temp') !== false ||
            strpos(strtolower($data['contact_email']), 'test') !== false) {
            $riskScore += 40;
            $fraudIndicators[] = "Email suspect";
            $analysis .= "Email suspect détecté. ";
        }
        
        if (strpos(strtolower($data['association_name']), 'test') !== false ||
            strpos(strtolower($data['association_name']), 'fake') !== false) {
            $riskScore += 35;
            $fraudIndicators[] = "Nom suspect";
            $analysis .= "Nom d'association suspect. ";
        }
        
        $status = $riskScore < 30 ? 'approved' : 'rejected';
        
        if ($riskScore < 30) {
            $analysis .= "✅ Données valides. Aucun risque de fraude détecté.";
        } else {
            $analysis .= "❌ Risques de fraude détectés. Vérification manuelle requise.";
        }
        
        return [
            'risk_score' => $riskScore,
            'status' => $status,
            'analysis' => $analysis,
            'fraud_indicators' => $fraudIndicators,
            'recommendations' => $status === 'approved' ? 'Proceed with registration' : 'Manual verification required',
            'gpt4_model' => 'fallback-analysis',
            'analysis_timestamp' => now()->toISOString()
        ];
    }

    /**
     * Test GPT-4 connectivity
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => 'gpt-4-turbo-preview',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Test connection'
                    ]
                ],
                'max_tokens' => 10
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
} 