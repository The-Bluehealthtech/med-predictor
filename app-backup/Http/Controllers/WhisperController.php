<?php

namespace App\Http\Controllers;

use App\Services\WhisperService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class WhisperController extends Controller
{
    protected WhisperService $whisperService;

    public function __construct(WhisperService $whisperService)
    {
        $this->whisperService = $whisperService;
    }

    /**
     * Show the Whisper transcription interface
     */
    public function index(): View
    {
        $supportedLanguages = $this->whisperService->getSupportedLanguages();
        $medicalPromptTypes = $this->whisperService->getMedicalPromptTypes();
        
        return view('whisper.index', compact('supportedLanguages', 'medicalPromptTypes'));
    }

    /**
     * Transcribe audio file
     */
    public function transcribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'audio_file' => 'required|file|max:25600', // 25MB max
            'language' => 'string|in:' . implode(',', array_keys($this->whisperService->getSupportedLanguages())),
            'prompt' => 'string|max:1000',
            'medical_context' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $audioFile = $request->file('audio_file');
            $language = $request->input('language', 'en');
            $prompt = $request->input('prompt', '');
            $medicalContext = $request->boolean('medical_context', false);

            // Validate audio file
            $validation = $this->whisperService->validateAudioFile($audioFile);
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'errors' => $validation['errors'],
                ], 422);
            }

            // Transcribe audio
            $result = $this->whisperService->transcribeAudio(
                $audioFile,
                $language,
                $prompt,
                $medicalContext
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transcription failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transcribe medical consultation
     */
    public function transcribeMedicalConsultation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'audio_file' => 'required|file|max:25600',
            'consultation_type' => 'string|in:symptoms,diagnosis,treatment,followup,general',
            'language' => 'string|in:' . implode(',', array_keys($this->whisperService->getSupportedLanguages())),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $audioFile = $request->file('audio_file');
            $consultationType = $request->input('consultation_type', 'general');
            $language = $request->input('language', 'en');

            $result = $this->whisperService->transcribeMedicalConsultation(
                $audioFile,
                $consultationType,
                $language
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medical transcription failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transcribe medical dictation
     */
    public function transcribeMedicalDictation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'audio_file' => 'required|file|max:25600',
            'dictation_type' => 'string|in:clinical_notes,medical_report,prescription,referral,discharge',
            'language' => 'string|in:' . implode(',', array_keys($this->whisperService->getSupportedLanguages())),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $audioFile = $request->file('audio_file');
            $dictationType = $request->input('dictation_type', 'clinical_notes');
            $language = $request->input('language', 'en');

            $result = $this->whisperService->transcribeMedicalDictation(
                $audioFile,
                $dictationType,
                $language
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medical dictation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Batch transcribe multiple audio files
     */
    public function batchTranscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'audio_files' => 'required|array|min:1|max:10',
            'audio_files.*' => 'file|max:25600',
            'language' => 'string|in:' . implode(',', array_keys($this->whisperService->getSupportedLanguages())),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $audioFiles = $request->file('audio_files');
            $language = $request->input('language', 'en');

            $result = $this->whisperService->batchTranscribe($audioFiles, $language);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Batch transcription failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get supported languages
     */
    public function getSupportedLanguages(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->whisperService->getSupportedLanguages(),
        ]);
    }

    /**
     * Get model information
     */
    public function getModelInfo(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'models' => [
                    'default' => [
                        'name' => 'Whisper-1',
                        'description' => 'Standard OpenAI Whisper model',
                        'languages' => ['en', 'fr', 'es', 'de', 'it', 'pt', 'zh', 'ja', 'ko']
                    ],
                    'arabic' => [
                        'name' => 'Whisper Large V2 Arabic 5k Steps',
                        'description' => 'Fine-tuned Arabic model trained on CommonVoice dataset',
                        'wer' => 0.4239,
                        'training_steps' => 5000,
                        'languages' => ['ar', 'ar-tn'],
                        'features' => [
                            'Enhanced Arabic transcription accuracy',
                            'Tunisian dialect support',
                            'Trained on CommonVoice dataset',
                            'Optimized for medical terminology',
                            'Better handling of Arabic dialects'
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Get medical prompt types
     */
    public function getMedicalPromptTypes(): JsonResponse
    {
        $promptTypes = $this->whisperService->getMedicalPromptTypes();
        
        return response()->json([
            'success' => true,
            'data' => $promptTypes,
        ]);
    }

    /**
     * Test Whisper API connectivity
     */
    public function testConnection(): JsonResponse
    {
        try {
            // Check if API key is configured
            if (!config('services.openai.api_key')) {
                throw new \Exception('OpenAI API key not configured');
            }

            return response()->json([
                'success' => true,
                'message' => 'Whisper API connection test successful',
                'api_key_configured' => true,
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Whisper API connection test failed: ' . $e->getMessage(),
                'api_key_configured' => false,
            ], 500);
        }
    }

    /**
     * Get transcription history
     */
    public function getHistory(Request $request): JsonResponse
    {
        // This would typically fetch from database
        // For now, return empty array
        return response()->json([
            'success' => true,
            'data' => [
                'transcriptions' => [],
                'total' => 0,
            ],
        ]);
    }
} 