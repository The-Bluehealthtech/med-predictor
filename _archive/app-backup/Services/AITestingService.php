<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AITestingService
{
    protected array $providers = [
        'openai' => [
            'name' => 'OpenAI GPT-4',
            'endpoint' => 'https://api.openai.com/v1/chat/completions',
            'model' => 'gpt-4',
            'api_key_env' => 'OPENAI_API_KEY',
        ],
        'anthropic' => [
            'name' => 'Anthropic Claude',
            'endpoint' => 'https://api.anthropic.com/v1/messages',
            'model' => 'claude-3-sonnet-20240229',
            'api_key_env' => 'ANTHROPIC_API_KEY',
        ],
        'google' => [
            'name' => 'Google Gemini',
            'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent',
            'model' => 'gemini-1.5-flash',
            'api_key_env' => 'GOOGLE_API_KEY',
        ],
        'azure' => [
            'name' => 'Azure OpenAI',
            'endpoint' => 'https://your-resource.openai.azure.com/openai/deployments/your-deployment/chat/completions',
            'model' => 'gpt-4',
            'api_key_env' => 'AZURE_OPENAI_API_KEY',
        ],
        'cohere' => [
            'name' => 'Cohere Command',
            'endpoint' => 'https://api.cohere.ai/v1/generate',
            'model' => 'command',
            'api_key_env' => 'COHERE_API_KEY',
        ],
    ];

    protected array $testCases = [
        'medical_diagnosis' => [
            'prompt' => 'Analyze the following medical symptoms and provide a preliminary diagnosis: Patient reports fatigue, muscle weakness, and joint pain. Blood pressure is elevated at 140/90. What are the possible causes and recommended next steps?',
            'expected' => 'diagnosis_analysis',
        ],
        'performance_analysis' => [
            'prompt' => 'Analyze this athlete performance data: VO2 max: 65 ml/kg/min, Heart rate recovery: 25 bpm in 1 minute, Sprint speed: 10.2 m/s. What does this indicate about their fitness level and what recommendations would you make?',
            'expected' => 'performance_insights',
        ],
        'injury_prediction' => [
            'prompt' => 'Based on this athlete data: Age 24, Previous ACL injury, Current training load 85% of max, Recent fatigue score 7/10. What is the injury risk assessment and prevention recommendations?',
            'expected' => 'risk_assessment',
        ],
        'treatment_plan' => [
            'prompt' => 'Create a rehabilitation plan for a soccer player with grade 2 hamstring strain. Include timeline, exercises, and return-to-play criteria.',
            'expected' => 'treatment_plan',
        ],
        'medical_documentation' => [
            'prompt' => 'Write a professional medical note for a patient visit: 22-year-old male athlete, complains of right knee pain after training, no swelling, pain 4/10, range of motion slightly limited.',
            'expected' => 'medical_note',
        ],
    ];

    /**
     * Test all AI providers with medical scenarios
     */
    public function testAllProviders(): array
    {
        $results = [];
        $summary = [];

        foreach ($this->providers as $providerKey => $provider) {
            Log::info("Testing AI provider: {$provider['name']}");
            
            $providerResults = $this->testProvider($providerKey, $provider);
            $results[$providerKey] = $providerResults;
            
            $summary[$providerKey] = [
                'name' => $provider['name'],
                'success_rate' => $this->calculateSuccessRate($providerResults),
                'average_response_time' => $this->calculateAverageResponseTime($providerResults),
                'total_tests' => count($providerResults),
                'successful_tests' => count(array_filter($providerResults, fn($r) => $r['success'])),
                'error_count' => count(array_filter($providerResults, fn($r) => !$r['success'])),
            ];
        }

        return [
            'results' => $results,
            'summary' => $summary,
            'recommendations' => $this->generateRecommendations($summary),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Test a specific provider with all test cases
     */
    public function testProvider(string $providerKey, array $provider): array
    {
        $results = [];
        
        foreach ($this->testCases as $testKey => $testCase) {
            $cacheKey = "ai_test_{$providerKey}_{$testKey}";
            
            // Check cache first
            if (Cache::has($cacheKey)) {
                $results[$testKey] = Cache::get($cacheKey);
                continue;
            }

            $startTime = microtime(true);
            
            try {
                $response = $this->callAIProvider($providerKey, $provider, $testCase['prompt']);
                
                $endTime = microtime(true);
                $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
                
                $result = [
                    'success' => true,
                    'provider' => $provider['name'],
                    'test_case' => $testKey,
                    'prompt' => $testCase['prompt'],
                    'response' => $response['content'] ?? $response['text'] ?? $response,
                    'response_time_ms' => round($responseTime, 2),
                    'tokens_used' => $response['usage']['total_tokens'] ?? null,
                    'model' => $provider['model'],
                    'timestamp' => now()->toISOString(),
                ];
                
            } catch (\Exception $e) {
                $result = [
                    'success' => false,
                    'provider' => $provider['name'],
                    'test_case' => $testKey,
                    'prompt' => $testCase['prompt'],
                    'error' => $e->getMessage(),
                    'response_time_ms' => 0,
                    'timestamp' => now()->toISOString(),
                ];
            }
            
            // Cache result for 1 hour
            Cache::put($cacheKey, $result, 3600);
            $results[$testKey] = $result;
        }
        
        return $results;
    }

    /**
     * Call a specific AI provider
     */
    protected function callAIProvider(string $providerKey, array $provider, string $prompt): array
    {
        $apiKey = null;
        
        // Get API key from config instead of env
        switch ($providerKey) {
            case 'openai':
                $apiKey = config('services.openai.api_key');
                break;
            case 'google':
                $apiKey = config('services.google.api_key');
                break;
            case 'anthropic':
                $apiKey = config('services.anthropic.api_key');
                break;
            case 'azure':
                $apiKey = config('services.azure_openai.api_key');
                break;
            case 'cohere':
                $apiKey = config('services.cohere.api_key');
                break;
            default:
                $apiKey = env($provider['api_key_env']);
        }
        
        if (!$apiKey) {
            throw new \Exception("API key not configured for {$provider['name']}");
        }

        $headers = [
            'Content-Type' => 'application/json',
        ];

        // Google Gemini uses API key as URL parameter, not Bearer token
        if ($providerKey === 'google') {
            $endpoint = $provider['endpoint'] . "?key=" . $apiKey;
        } else {
            $headers['Authorization'] = "Bearer {$apiKey}";
            $endpoint = $provider['endpoint'];
        }

        $payload = $this->buildPayload($providerKey, $prompt);

        $response = Http::timeout(30)
            ->withHeaders($headers)
            ->post($endpoint, $payload);

        if (!$response->successful()) {
            throw new \Exception("API call failed: " . $response->body());
        }

        return $this->parseResponse($providerKey, $response->json());
    }

    /**
     * Build the appropriate payload for each provider
     */
    protected function buildPayload(string $providerKey, string $prompt): array
    {
        return match ($providerKey) {
            'openai' => [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a medical AI assistant specializing in sports medicine and athlete health. Provide detailed, professional analysis.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.3,
            ],
            'anthropic' => [
                'model' => 'claude-3-sonnet-20240229',
                'max_tokens' => 1000,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
            ],
            'google' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "You are a medical AI assistant. Please analyze: {$prompt}"]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 1000,
                    'temperature' => 0.3,
                ],
            ],
            'azure' => [
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a medical AI assistant specializing in sports medicine.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.3,
            ],
            'cohere' => [
                'model' => 'command',
                'prompt' => "Medical AI Assistant: {$prompt}",
                'max_tokens' => 1000,
                'temperature' => 0.3,
            ],
            default => throw new \Exception("Unknown provider: {$providerKey}"),
        };
    }

    /**
     * Parse the response from each provider
     */
    protected function parseResponse(string $providerKey, array $response): array
    {
        return match ($providerKey) {
            'openai' => [
                'content' => $response['choices'][0]['message']['content'] ?? '',
                'usage' => $response['usage'] ?? [],
            ],
            'anthropic' => [
                'content' => $response['content'][0]['text'] ?? '',
                'usage' => $response['usage'] ?? [],
            ],
            'google' => [
                'content' => $response['candidates'][0]['content']['parts'][0]['text'] ?? '',
                'usage' => $response['usageMetadata'] ?? [],
            ],
            'azure' => [
                'content' => $response['choices'][0]['message']['content'] ?? '',
                'usage' => $response['usage'] ?? [],
            ],
            'cohere' => [
                'content' => $response['generations'][0]['text'] ?? '',
                'usage' => $response['meta'] ?? [],
            ],
            default => [
                'content' => json_encode($response),
                'usage' => [],
            ],
        };
    }

    /**
     * Calculate success rate for a provider
     */
    protected function calculateSuccessRate(array $results): float
    {
        $total = count($results);
        $successful = count(array_filter($results, fn($r) => $r['success']));
        
        return $total > 0 ? round(($successful / $total) * 100, 2) : 0;
    }

    /**
     * Calculate average response time for a provider
     */
    protected function calculateAverageResponseTime(array $results): float
    {
        $successfulResults = array_filter($results, fn($r) => $r['success']);
        
        if (empty($successfulResults)) {
            return 0;
        }
        
        $totalTime = array_sum(array_column($successfulResults, 'response_time_ms'));
        return round($totalTime / count($successfulResults), 2);
    }

    /**
     * Generate recommendations based on test results
     */
    protected function generateRecommendations(array $summary): array
    {
        $recommendations = [];
        
        // Find best performing provider
        $bestProvider = collect($summary)
            ->filter(fn($s) => $s['success_rate'] > 0)
            ->sortByDesc('success_rate')
            ->first();
            
        if ($bestProvider) {
            $recommendations[] = "Best performing provider: {$bestProvider['name']} with {$bestProvider['success_rate']}% success rate";
        }
        
        // Find fastest provider
        $fastestProvider = collect($summary)
            ->filter(fn($s) => $s['average_response_time'] > 0)
            ->sortBy('average_response_time')
            ->first();
            
        if ($fastestProvider) {
            $recommendations[] = "Fastest provider: {$fastestProvider['name']} with {$fastestProvider['average_response_time']}ms average response time";
        }
        
        // Check for providers with errors
        $errorProviders = collect($summary)
            ->filter(fn($s) => $s['error_count'] > 0)
            ->keys();
            
        if ($errorProviders->isNotEmpty()) {
            $recommendations[] = "Providers with errors: " . $errorProviders->implode(', ');
        }
        
        return $recommendations;
    }

    /**
     * Get available providers
     */
    public function getAvailableProviders(): array
    {
        return collect($this->providers)->map(function ($provider, $key) {
            $apiKey = null;
            
            // Check based on provider key
            switch ($key) {
                case 'openai':
                    $apiKey = config('services.openai.api_key');
                    break;
                case 'google':
                    $apiKey = config('services.google.api_key');
                    break;
                case 'anthropic':
                    $apiKey = config('services.anthropic.api_key');
                    break;
                case 'azure':
                    $apiKey = config('services.azure_openai.api_key');
                    break;
                case 'cohere':
                    $apiKey = config('services.cohere.api_key');
                    break;
                default:
                    $apiKey = env($provider['api_key_env']);
            }
            
            return [
                'key' => $key,
                'name' => $provider['name'],
                'configured' => !empty($apiKey),
                'api_key_env' => $provider['api_key_env'],
            ];
        })->toArray();
    }

    /**
     * Test a single provider with a custom prompt
     */
    public function testSingleProvider(string $providerKey, string $prompt): array
    {
        if (!isset($this->providers[$providerKey])) {
            throw new \Exception("Unknown provider: {$providerKey}");
        }
        
        $provider = $this->providers[$providerKey];
        return $this->testProvider($providerKey, $provider);
    }
} 