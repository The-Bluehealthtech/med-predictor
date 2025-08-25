<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class WhisperService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected array $supportedLanguages = [
        'en' => 'English',
        'fr' => 'French',
        'es' => 'Spanish',
        'de' => 'German',
        'it' => 'Italian',
        'pt' => 'Portuguese',
        'ar' => 'Arabic',
        'ar-tn' => 'Tunisian Arabic',
        'zh' => 'Chinese',
        'ja' => 'Japanese',
        'ko' => 'Korean',
    ];

    protected array $modelConfigs = [
        'default' => 'whisper-1',
        'arabic' => 'whisper-large-v2-arabic-5k', // Custom Arabic model
        'tunisian' => 'whisper-large-v2-arabic-5k', // Same model for Tunisian dialect
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = 'https://api.openai.com/v1/audio/transcriptions';
    }

    /**
     * Transcribe audio file using Whisper
     */
    public function transcribeAudio(
        UploadedFile|string $audioFile,
        string $language = 'en',
        string $prompt = '',
        bool $medicalContext = false
    ): array {
        try {
            // Validate API key
            if (!$this->apiKey) {
                throw new \Exception('OpenAI API key not configured');
            }

            // Prepare the audio file
            $audioData = $this->prepareAudioFile($audioFile);
            
            // Build the prompt for medical context
            $finalPrompt = $this->buildMedicalPrompt($prompt, $medicalContext);

            // Make API request
            $response = $this->makeWhisperRequest($audioData, $language, $finalPrompt);

            // Process and enhance the response
            $transcription = $this->processTranscription($response, $medicalContext);

            Log::info('Whisper transcription completed', [
                'language' => $language,
                'medical_context' => $medicalContext,
                'word_count' => str_word_count($transcription['text']),
            ]);

            return $transcription;

        } catch (\Exception $e) {
            Log::error('Whisper transcription failed', [
                'error' => $e->getMessage(),
                'language' => $language,
                'medical_context' => $medicalContext,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'text' => '',
                'language' => $language,
                'confidence' => 0,
                'medical_terms' => [],
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    /**
     * Transcribe medical consultation audio
     */
    public function transcribeMedicalConsultation(
        UploadedFile|string $audioFile,
        string $consultationType = 'general',
        string $language = 'en'
    ): array {
        $prompts = [
            'symptoms' => 'Patient is describing symptoms and medical history.',
            'diagnosis' => 'Medical professional is providing diagnosis and assessment.',
            'treatment' => 'Treatment plan and medication instructions are being discussed.',
            'followup' => 'Follow-up instructions and next appointment details.',
            'general' => 'General medical consultation with patient and healthcare provider.',
        ];

        $prompt = $prompts[$consultationType] ?? $prompts['general'];

        return $this->transcribeAudio($audioFile, $language, $prompt, true);
    }

    /**
     * Transcribe medical dictation
     */
    public function transcribeMedicalDictation(
        UploadedFile|string $audioFile,
        string $dictationType = 'clinical_notes',
        string $language = 'en'
    ): array {
        $prompts = [
            'clinical_notes' => 'Clinical notes and patient assessment.',
            'medical_report' => 'Medical report and findings.',
            'prescription' => 'Prescription and medication details.',
            'referral' => 'Medical referral and specialist consultation.',
            'discharge' => 'Discharge instructions and follow-up care.',
        ];

        $prompt = $prompts[$dictationType] ?? $prompts['clinical_notes'];

        return $this->transcribeAudio($audioFile, $language, $prompt, true);
    }

    /**
     * Batch transcribe multiple audio files
     */
    public function batchTranscribe(array $audioFiles, string $language = 'en'): array
    {
        $results = [];
        $totalFiles = count($audioFiles);

        foreach ($audioFiles as $index => $audioFile) {
            $fileNumber = $index + 1;
            Log::info("Processing file {$fileNumber} of {$totalFiles}");
            
            $result = $this->transcribeAudio($audioFile, $language);
            $results[] = [
                'file_index' => $fileNumber,
                'filename' => $this->getFilename($audioFile),
                'result' => $result,
            ];
        }

        $successfulCount = 0;
        $failedCount = 0;
        
        foreach ($results as $result) {
            if ($result['result']['success']) {
                $successfulCount++;
            } else {
                $failedCount++;
            }
        }

        return [
            'success' => true,
            'total_files' => $totalFiles,
            'successful_transcriptions' => $successfulCount,
            'failed_transcriptions' => $failedCount,
            'results' => $results,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get supported languages
     */
    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }

    /**
     * Get medical prompt types
     */
    public function getMedicalPromptTypes(): array
    {
        return ['symptoms', 'diagnosis', 'treatment', 'followup', 'general'];
    }

    /**
     * Prepare audio file for API request
     */
    protected function prepareAudioFile(UploadedFile|string $audioFile): array
    {
        if ($audioFile instanceof UploadedFile) {
            // Handle uploaded file
            $tempPath = $audioFile->getRealPath();
            $filename = $audioFile->getClientOriginalName();
            $mimeType = $audioFile->getMimeType();
        } else {
            // Handle file path
            $tempPath = $audioFile;
            $filename = basename($audioFile);
            $mimeType = mime_content_type($audioFile);
        }

        // Validate file exists
        if (!file_exists($tempPath)) {
            throw new \Exception('Audio file not found');
        }

        // Validate file size (Whisper limit is 25MB)
        $fileSize = filesize($tempPath);
        if ($fileSize > 25 * 1024 * 1024) {
            throw new \Exception('Audio file too large. Maximum size is 25MB');
        }

        // Validate audio format
        $allowedMimes = [
            'audio/mpeg', 'audio/mp3', 'audio/mp4', 'audio/wav',
            'audio/webm', 'audio/ogg', 'audio/flac', 'audio/m4a'
        ];

        if (!in_array($mimeType, $allowedMimes)) {
            throw new \Exception('Unsupported audio format. Supported formats: MP3, WAV, M4A, FLAC, OGG, WEBM');
        }

        return [
            'path' => $tempPath,
            'filename' => $filename,
            'mime_type' => $mimeType,
            'size' => $fileSize,
        ];
    }

    /**
     * Build medical context prompt
     */
    protected function buildMedicalPrompt(string $prompt, bool $medicalContext): string
    {
        if (!$medicalContext) {
            return $prompt;
        }

        $medicalContextPrompt = 'This is a medical consultation. The audio contains medical terminology, patient symptoms, diagnoses, treatments, and healthcare professional discussions. Please transcribe accurately with proper medical terminology and maintain professional medical language.';

        return $prompt ? "{$medicalContextPrompt} {$prompt}" : $medicalContextPrompt;
    }

    /**
     * Make Whisper API request
     */
    protected function makeWhisperRequest(array $audioData, string $language, string $prompt): array
    {
        $headers = [
            'Authorization' => "Bearer {$this->apiKey}",
        ];

        // Select appropriate model based on language
        $model = $this->modelConfigs['default'];
        if ($language === 'ar' || $language === 'ar-tn') {
            $model = $this->modelConfigs['arabic'];
        }

        $data = [
            'file' => fopen($audioData['path'], 'r'),
            'model' => $model,
            'language' => $language,
            'response_format' => 'verbose_json',
        ];

        if ($prompt) {
            $data['prompt'] = $prompt;
        }

        $response = Http::timeout(60)
            ->withHeaders($headers)
            ->attach('file', $audioData['path'], $audioData['filename'])
            ->post($this->baseUrl, $data);

        if (!$response->successful()) {
            $error = $response->json();
            throw new \Exception("Whisper API error: " . ($error['error']['message'] ?? $response->body()));
        }

        return $response->json();
    }

    /**
     * Process and enhance transcription response
     */
    protected function processTranscription(array $response, bool $medicalContext): array
    {
        $text = $response['text'] ?? '';
        $language = $response['language'] ?? 'en';
        $duration = $response['duration'] ?? 0;

        // Extract medical terms if medical context
        $medicalTerms = $medicalContext ? $this->extractMedicalTerms($text) : [];

        // Calculate confidence based on segments
        $confidence = $this->calculateConfidence($response);

        // Clean and format text
        $cleanedText = $this->cleanTranscriptionText($text);

        return [
            'success' => true,
            'text' => $cleanedText,
            'original_text' => $text,
            'language' => $language,
            'duration' => $duration,
            'confidence' => $confidence,
            'medical_terms' => $medicalTerms,
            'word_count' => str_word_count($cleanedText),
            'segments' => $response['segments'] ?? [],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Extract medical terms from transcription
     */
    protected function extractMedicalTerms(string $text): array
    {
        // Common medical terms patterns
        $medicalPatterns = [
            'symptoms' => '/\b(fever|pain|headache|nausea|fatigue|dizziness|cough|shortness of breath)\b/i',
            'medications' => '/\b(aspirin|ibuprofen|acetaminophen|antibiotics|prescription)\b/i',
            'diagnoses' => '/\b(diagnosis|condition|syndrome|disease|infection)\b/i',
            'procedures' => '/\b(examination|test|scan|x-ray|surgery|procedure)\b/i',
            'body_parts' => '/\b(head|chest|abdomen|arm|leg|heart|lungs|stomach)\b/i',
        ];

        $medicalTerms = [];
        foreach ($medicalPatterns as $category => $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                $medicalTerms[$category] = array_unique($matches[0]);
            }
        }

        return $medicalTerms;
    }

    /**
     * Calculate confidence score from segments
     */
    protected function calculateConfidence(array $response): float
    {
        $segments = $response['segments'] ?? [];
        
        if (empty($segments)) {
            return 0.0;
        }

        $totalConfidence = 0;
        $segmentCount = count($segments);

        foreach ($segments as $segment) {
            $totalConfidence += $segment['avg_logprob'] ?? 0;
        }

        // Convert log probability to confidence (0-1 scale)
        $avgLogProb = $totalConfidence / $segmentCount;
        $confidence = exp($avgLogProb);

        return round($confidence, 3);
    }

    /**
     * Clean and format transcription text
     */
    protected function cleanTranscriptionText(string $text): string
    {
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Capitalize sentences
        $text = ucfirst($text);
        
        // Add proper punctuation if missing
        if (!preg_match('/[.!?]$/', $text)) {
            $text .= '.';
        }

        return trim($text);
    }

    /**
     * Get filename from audio file
     */
    protected function getFilename(UploadedFile|string $audioFile): string
    {
        if ($audioFile instanceof UploadedFile) {
            return $audioFile->getClientOriginalName();
        }
        
        return basename($audioFile);
    }

    /**
     * Validate audio file format and size
     */
    public function validateAudioFile(UploadedFile $file): array
    {
        $errors = [];
        
        // Check file size
        if ($file->getSize() > 25 * 1024 * 1024) {
            $errors[] = 'File size exceeds 25MB limit';
        }

        // Check file format
        $allowedMimes = [
            'audio/mpeg', 'audio/mp3', 'audio/mp4', 'audio/wav',
            'audio/webm', 'audio/ogg', 'audio/flac', 'audio/m4a'
        ];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Unsupported audio format. Supported: MP3, WAV, M4A, FLAC, OGG, WEBM';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    /**
     * Test connection to Whisper API
     */
    public function testConnection(): array
    {
        try {
            if (!$this->apiKey) {
                return [
                    'success' => false,
                    'error' => 'OpenAI API key not configured',
                    'model' => null,
                    'status' => 'not_configured',
                ];
            }

            // Make a simple test request to check connectivity
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->get('https://api.openai.com/v1/models');

            if ($response->successful()) {
                $models = $response->json();
                $whisperModels = array_filter($models['data'] ?? [], function($model) {
                    return strpos($model['id'], 'whisper') !== false;
                });

                return [
                    'success' => true,
                    'model' => 'whisper-1',
                    'status' => 'connected',
                    'available_models' => array_column($whisperModels, 'id'),
                    'message' => 'Whisper API connection successful',
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'API request failed: ' . $response->body(),
                    'model' => null,
                    'status' => 'connection_failed',
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'model' => null,
                'status' => 'error',
            ];
        }
    }
} 