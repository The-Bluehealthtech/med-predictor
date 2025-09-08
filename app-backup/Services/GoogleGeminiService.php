<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleGeminiService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected int $timeout;
    protected int $maxTokens;
    protected float $temperature;

    public function __construct()
    {
        $this->apiKey = config('services.google.api_key') ?? '';
        $this->baseUrl = config('services.google.base_url') ?? '';
        $this->model = config('services.google.model', 'gemini-pro');
        $this->timeout = config('services.google.timeout', 60);
        $this->maxTokens = config('services.google.max_tokens', 1000);
        $this->temperature = config('services.google.temperature', 0.7);
    }

    /**
     * Generate medical analysis using Gemini
     */
    public function analyzeMedicalData(array $data, string $analysisType = 'general'): array
    {
        try {
            if (!$this->apiKey) {
                throw new \Exception('Google Gemini API key not configured');
            }

            $prompt = $this->buildMedicalPrompt($data, $analysisType);
            $response = $this->makeGeminiRequest($prompt);

            return [
                'success' => true,
                'analysis' => $response['text'] ?? '',
                'analysis_type' => $analysisType,
                'confidence' => $this->calculateConfidence($response),
                'tokens_used' => $response['usage']['total_tokens'] ?? 0,
                'timestamp' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error('Gemini medical analysis failed', [
                'error' => $e->getMessage(),
                'analysis_type' => $analysisType,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'analysis' => '',
                'analysis_type' => $analysisType,
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    /**
     * Generate medical diagnosis suggestions
     */
    public function suggestDiagnosis(array $symptoms, array $patientData = []): array
    {
        $data = [
            'symptoms' => $symptoms,
            'patient_data' => $patientData,
        ];

        return $this->analyzeMedicalData($data, 'diagnosis');
    }

    /**
     * Generate treatment recommendations
     */
    public function suggestTreatment(string $diagnosis, array $patientData = []): array
    {
        $data = [
            'diagnosis' => $diagnosis,
            'patient_data' => $patientData,
        ];

        return $this->analyzeMedicalData($data, 'treatment');
    }

    /**
     * Analyze medical performance data
     */
    public function analyzePerformanceData(array $performanceData): array
    {
        return $this->analyzeMedicalData($performanceData, 'performance');
    }

    /**
     * Predict injury risk
     */
    public function predictInjuryRisk(array $playerData, array $performanceHistory = []): array
    {
        $data = [
            'player_data' => $playerData,
            'performance_history' => $performanceHistory,
        ];

        return $this->analyzeMedicalData($data, 'injury_prediction');
    }

    /**
     * Generate rehabilitation plan
     */
    public function generateRehabPlan(string $injury, array $patientData = []): array
    {
        $data = [
            'injury' => $injury,
            'patient_data' => $patientData,
        ];

        return $this->analyzeMedicalData($data, 'rehabilitation');
    }

    /**
     * Analyze medical images (text-based analysis)
     */
    public function analyzeMedicalImage(string $imageDescription, string $context = ''): array
    {
        $data = [
            'image_description' => $imageDescription,
            'context' => $context,
        ];

        return $this->analyzeMedicalData($data, 'image_analysis');
    }

    /**
     * Generate medical report
     */
    public function generateMedicalReport(array $patientData, array $findings): array
    {
        $data = [
            'patient_data' => $patientData,
            'findings' => $findings,
        ];

        return $this->analyzeMedicalData($data, 'medical_report');
    }

    /**
     * Build medical-specific prompts
     */
    protected function buildMedicalPrompt(array $data, string $analysisType): string
    {
        $basePrompt = "You are a medical AI assistant specializing in sports medicine and athletic healthcare. ";
        $basePrompt .= "Provide accurate, evidence-based medical analysis while maintaining professional medical standards. ";
        $basePrompt .= "Always include disclaimers that this is AI-generated advice and should be reviewed by qualified healthcare professionals.\n\n";

        switch ($analysisType) {
            case 'diagnosis':
                return $basePrompt . $this->buildDiagnosisPrompt($data);
            
            case 'treatment':
                return $basePrompt . $this->buildTreatmentPrompt($data);
            
            case 'performance':
                return $basePrompt . $this->buildPerformancePrompt($data);
            
            case 'injury_prediction':
                return $basePrompt . $this->buildInjuryPredictionPrompt($data);
            
            case 'rehabilitation':
                return $basePrompt . $this->buildRehabilitationPrompt($data);
            
            case 'image_analysis':
                return $basePrompt . $this->buildImageAnalysisPrompt($data);
            
            case 'medical_report':
                return $basePrompt . $this->buildMedicalReportPrompt($data);
            
            default:
                return $basePrompt . "Analyze the following medical data: " . json_encode($data, JSON_PRETTY_PRINT);
        }
    }

    /**
     * Build diagnosis prompt
     */
    protected function buildDiagnosisPrompt(array $data): string
    {
        $symptoms = implode(', ', $data['symptoms'] ?? []);
        $patientData = json_encode($data['patient_data'] ?? [], JSON_PRETTY_PRINT);
        
        return "Based on the following symptoms and patient data, suggest possible diagnoses:\n\n" .
               "Symptoms: {$symptoms}\n" .
               "Patient Data: {$patientData}\n\n" .
               "Provide:\n" .
               "1. Possible diagnoses (most likely to least likely)\n" .
               "2. Recommended diagnostic tests\n" .
               "3. Red flags to watch for\n" .
               "4. When to seek immediate medical attention";
    }

    /**
     * Build treatment prompt
     */
    protected function buildTreatmentPrompt(array $data): string
    {
        $diagnosis = $data['diagnosis'] ?? '';
        $patientData = json_encode($data['patient_data'] ?? [], JSON_PRETTY_PRINT);
        
        return "Based on the diagnosis and patient data, suggest treatment options:\n\n" .
               "Diagnosis: {$diagnosis}\n" .
               "Patient Data: {$patientData}\n\n" .
               "Provide:\n" .
               "1. Recommended treatment approaches\n" .
               "2. Medication considerations\n" .
               "3. Rehabilitation exercises\n" .
               "4. Recovery timeline\n" .
               "5. Prevention strategies";
    }

    /**
     * Build performance analysis prompt
     */
    protected function buildPerformancePrompt(array $data): string
    {
        $performanceData = json_encode($data, JSON_PRETTY_PRINT);
        
        return "Analyze the following athletic performance data:\n\n" .
               "Performance Data: {$performanceData}\n\n" .
               "Provide:\n" .
               "1. Performance trends and patterns\n" .
               "2. Areas of strength and improvement\n" .
               "3. Recommendations for optimization\n" .
               "4. Potential health implications";
    }

    /**
     * Build injury prediction prompt
     */
    protected function buildInjuryPredictionPrompt(array $data): string
    {
        $playerData = json_encode($data['player_data'] ?? [], JSON_PRETTY_PRINT);
        $performanceHistory = json_encode($data['performance_history'] ?? [], JSON_PRETTY_PRINT);
        
        return "Based on the player data and performance history, assess injury risk:\n\n" .
               "Player Data: {$playerData}\n" .
               "Performance History: {$performanceHistory}\n\n" .
               "Provide:\n" .
               "1. Injury risk assessment\n" .
               "2. Risk factors identified\n" .
               "3. Prevention recommendations\n" .
               "4. Monitoring suggestions";
    }

    /**
     * Build rehabilitation prompt
     */
    protected function buildRehabilitationPrompt(array $data): string
    {
        $injury = $data['injury'] ?? '';
        $patientData = json_encode($data['patient_data'] ?? [], JSON_PRETTY_PRINT);
        
        return "Create a rehabilitation plan for the following injury:\n\n" .
               "Injury: {$injury}\n" .
               "Patient Data: {$patientData}\n\n" .
               "Provide:\n" .
               "1. Phase-based rehabilitation plan\n" .
               "2. Specific exercises and protocols\n" .
               "3. Progress milestones\n" .
               "4. Return-to-play criteria\n" .
               "5. Precautions and contraindications";
    }

    /**
     * Build image analysis prompt
     */
    protected function buildImageAnalysisPrompt(array $data): string
    {
        $imageDescription = $data['image_description'] ?? '';
        $context = $data['context'] ?? '';
        
        return "Analyze the following medical image description:\n\n" .
               "Image Description: {$imageDescription}\n" .
               "Context: {$context}\n\n" .
               "Provide:\n" .
               "1. Potential findings\n" .
               "2. Differential diagnoses\n" .
               "3. Recommended follow-up\n" .
               "4. Clinical significance";
    }

    /**
     * Build medical report prompt
     */
    protected function buildMedicalReportPrompt(array $data): string
    {
        $patientData = json_encode($data['patient_data'] ?? [], JSON_PRETTY_PRINT);
        $findings = json_encode($data['findings'] ?? [], JSON_PRETTY_PRINT);
        
        return "Generate a professional medical report based on the following data:\n\n" .
               "Patient Data: {$patientData}\n" .
               "Findings: {$findings}\n\n" .
               "Provide a structured medical report including:\n" .
               "1. Executive summary\n" .
               "2. Detailed findings\n" .
               "3. Analysis and interpretation\n" .
               "4. Recommendations\n" .
               "5. Follow-up plan";
    }

    /**
     * Make Gemini API request
     */
    protected function makeGeminiRequest(string $prompt): array
    {
        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $requestData = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => $this->maxTokens,
                'temperature' => $this->temperature,
                'topP' => 0.8,
                'topK' => 40,
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, $requestData);

        if (!$response->successful()) {
            $error = $response->json();
            throw new \Exception("Gemini API error: " . ($error['error']['message'] ?? $response->body()));
        }

        $data = $response->json();
        
        return [
            'text' => $data['candidates'][0]['content']['parts'][0]['text'] ?? '',
            'usage' => $data['usage'] ?? [],
            'safety_ratings' => $data['candidates'][0]['safetyRatings'] ?? [],
        ];
    }

    /**
     * Calculate confidence score
     */
    protected function calculateConfidence(array $response): float
    {
        // Simple confidence calculation based on response length and safety ratings
        $text = $response['text'] ?? '';
        $safetyRatings = $response['safety_ratings'] ?? [];
        
        $baseConfidence = min(1.0, strlen($text) / 1000); // Normalize by expected length
        
        // Adjust confidence based on safety ratings
        $safetyScore = 1.0;
        foreach ($safetyRatings as $rating) {
            if ($rating['probability'] === 'HIGH') {
                $safetyScore *= 0.8;
            }
        }
        
        return round($baseConfidence * $safetyScore, 3);
    }

    /**
     * Test Gemini API connection
     */
    public function testConnection(): array
    {
        try {
            if (!$this->apiKey) {
                throw new \Exception('Google Gemini API key not configured');
            }

            $testPrompt = "Hello, this is a test message. Please respond with 'Connection successful' if you can read this.";
            $response = $this->makeGeminiRequest($testPrompt);

            return [
                'success' => true,
                'message' => 'Gemini API connection successful',
                'response' => $response['text'],
                'timestamp' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gemini API connection failed: ' . $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    /**
     * Get available models
     */
    public function getAvailableModels(): array
    {
        return [
            'gemini-pro' => 'Gemini Pro (Text)',
            'gemini-pro-vision' => 'Gemini Pro Vision (Text + Images)',
        ];
    }

    /**
     * Get service configuration
     */
    public function getConfiguration(): array
    {
        return [
            'api_key_configured' => !empty($this->apiKey),
            'base_url' => $this->baseUrl,
            'model' => $this->model,
            'timeout' => $this->timeout,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ];
    }
} 