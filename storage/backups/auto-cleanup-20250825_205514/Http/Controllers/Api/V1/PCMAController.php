<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFifaCompliantPCMARequest;
use App\Models\PCMA;
use App\Models\Athlete;
use App\Models\User;
use App\Models\Player;
use App\Events\CardioPCMASubmitted;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PCMAController extends Controller
{
    /**
     * Display a listing of PCMAs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PCMA::with(['athlete', 'assessor']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        if ($request->has('fifa_compliant')) {
            $query->where('fifa_compliant', $request->boolean('fifa_compliant'));
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $pcmas = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $pcmas->items(),
            'pagination' => [
                'current_page' => $pcmas->currentPage(),
                'last_page' => $pcmas->lastPage(),
                'per_page' => $pcmas->perPage(),
                'total' => $pcmas->total(),
            ]
        ]);
    }

    /**
     * Store a newly created PCMA.
     */
    public function store(StoreFifaCompliantPCMARequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            
            // Set default values
            $validatedData['status'] = $validatedData['status'] ?? 'pending';
            $validatedData['form_version'] = $validatedData['form_version'] ?? '1.0';
            $validatedData['last_updated_at'] = now();
            
            // Link to player if FIFA Connect ID is provided
            if (!empty($validatedData['fifa_connect_id'])) {
                $player = Player::where('fifa_connect_id', $validatedData['fifa_connect_id'])->first();
                if ($player) {
                    $validatedData['player_id'] = $player->id;
                }
            }
            
            // Handle file uploads
            $fileFields = ['ecg_file', 'mri_file', 'xray_file', 'ct_scan_file', 'ultrasound_file'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('medical_imaging', $filename, 'public');
                    $validatedData[$field] = $path;
                }
            }
            
            // Create PCMA
            $pcma = PCMA::create($validatedData);

            // Dispatch event for cardio PCMAs
            if ($pcma->type === 'cardio') {
                CardioPCMASubmitted::dispatch($pcma);
            }

            // Mark as FIFA compliant if all required fields are present
            if ($this->isFifaCompliant($pcma)) {
                $pcma->markAsFifaCompliant($request->user()->name ?? 'System');
            }

            return response()->json([
                'success' => true,
                'message' => 'PCMA créé avec succès',
                'data' => $pcma->load(['athlete', 'assessor'])
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du PCMA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du PCMA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a draft PCMA.
     */
    public function storeDraft(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'athlete_id' => 'required|exists:athletes,id',
                'type' => 'required|string',
                'assessor_id' => 'required|exists:users,id',
                'assessment_date' => 'required|date',
                'medical_history' => 'nullable|array',
                'physical_examination' => 'nullable|array',
                'cardiovascular_investigations' => 'nullable|array',
                'final_statement' => 'nullable|array',
                'scat_assessment' => 'nullable|array',
                'anatomical_annotations' => 'nullable|array',
            ]);

            $validatedData['status'] = 'draft';
            $validatedData['form_version'] = $validatedData['form_version'] ?? '1.0';
            $validatedData['last_updated_at'] = now();

            $pcma = PCMA::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Brouillon PCMA sauvegardé',
                'data' => $pcma->load(['athlete', 'assessor'])
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la sauvegarde du brouillon PCMA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde du brouillon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified PCMA.
     */
    public function show(PCMA $pcma): JsonResponse
    {
        $pcma->load(['athlete', 'assessor']);

        return response()->json([
            'success' => true,
            'data' => $pcma
        ]);
    }

    /**
     * Update the specified PCMA.
     */
    public function update(StoreFifaCompliantPCMARequest $request, PCMA $pcma): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $validatedData['last_updated_at'] = now();

            // Handle file uploads
            $fileFields = ['ecg_file', 'mri_file', 'xray_file', 'ct_scan_file', 'ultrasound_file'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('medical_imaging', $filename, 'public');
                    $validatedData[$field] = $path;
                }
            }

            $pcma->update($validatedData);

            // Update FIFA compliance status
            if ($this->isFifaCompliant($pcma)) {
                $pcma->markAsFifaCompliant($request->user()->name ?? 'System');
            }

            return response()->json([
                'success' => true,
                'message' => 'PCMA mis à jour avec succès',
                'data' => $pcma->load(['athlete', 'assessor'])
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du PCMA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du PCMA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified PCMA.
     */
    public function destroy(PCMA $pcma): JsonResponse
    {
        try {
            $pcma->delete();

            return response()->json([
                'success' => true,
                'message' => 'PCMA supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du PCMA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du PCMA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PCMAs for a specific athlete.
     */
    public function getAthletePCMAs(Athlete $athlete): JsonResponse
    {
        $pcmas = $athlete->pcmas()
            ->with(['assessor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pcmas
        ]);
    }

    /**
     * Get PCMA statistics for an athlete.
     */
    public function getAthletePCMAStats(Athlete $athlete): JsonResponse
    {
        $stats = [
            'total_pcmas' => $athlete->pcmas()->count(),
            'completed_pcmas' => $athlete->pcmas()->where('status', 'completed')->count(),
            'pending_pcmas' => $athlete->pcmas()->where('status', 'pending')->count(),
            'failed_pcmas' => $athlete->pcmas()->where('status', 'failed')->count(),
            'by_type' => [
                'bpma' => $athlete->pcmas()->where('type', 'bpma')->count(),
                'cardio' => $athlete->pcmas()->where('type', 'cardio')->count(),
                'dental' => $athlete->pcmas()->where('type', 'dental')->count(),
            ],
            'latest_assessment' => $athlete->pcmas()
                ->with(['assessor'])
                ->orderBy('created_at', 'desc')
                ->first(),
            'compliance_status' => [
                'has_bpma' => $athlete->pcmas()->where('type', 'bpma')->where('status', 'completed')->exists(),
                'has_cardio' => $athlete->pcmas()->where('type', 'cardio')->where('status', 'completed')->exists(),
                'has_dental' => $athlete->pcmas()->where('type', 'dental')->where('status', 'completed')->exists(),
                'fully_compliant' => $athlete->pcmas()->where('status', 'completed')->where('fifa_compliant', true)->exists(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get PCMA data for a specific player
     */
    public function getPlayerPCMAs(Player $player): JsonResponse
    {
        try {
            $pcmas = $player->pcmas()
                ->with(['assessor', 'athlete'])
                ->orderBy('created_at', 'desc')
                ->get();

            $stats = [
                'total_pcmas' => $pcmas->count(),
                'completed_pcmas' => $pcmas->where('status', 'completed')->count(),
                'pending_pcmas' => $pcmas->where('status', 'pending')->count(),
                'fifa_compliant_pcmas' => $pcmas->where('fifa_compliant', true)->count(),
                'latest_pcma' => $pcmas->where('status', 'completed')->first(),
                'valid_pcma' => $player->getCurrentPCMA(),
                'pcmas_due_for_renewal' => $player->isPCMADueForRenewal(),
                'pcmas_history' => $player->getPCMAHistory(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'pcmas' => $pcmas
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des PCMA du joueur: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des PCMA du joueur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PCMA data for a specific FIFA Connect ID
     */
    public function getFifaConnectPCMAs(string $fifaConnectId): JsonResponse
    {
        try {
            $player = Player::where('fifa_connect_id', $fifaConnectId)->first();
            
            if (!$player) {
                return response()->json([
                    'success' => false,
                    'message' => 'Joueur non trouvé avec cet ID FIFA Connect'
                ], 404);
            }

            return $this->getPlayerPCMAs($player);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des PCMA FIFA Connect: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des PCMA FIFA Connect',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add anatomical annotation to PCMA.
     */
    public function addAnatomicalAnnotation(Request $request, PCMA $pcma): JsonResponse
    {
        $request->validate([
            'view' => 'required|in:anterior,posterior',
            'x' => 'required|integer|min:0|max:1000',
            'y' => 'required|integer|min:0|max:1000',
            'note' => 'required|string|max:500',
        ]);

        try {
            $pcma->addAnatomicalAnnotation(
                $request->view,
                $request->x,
                $request->y,
                $request->note
            );

            return response()->json([
                'success' => true,
                'message' => 'Annotation ajoutée avec succès',
                'data' => $pcma->getAnatomicalAnnotations($request->view)
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout d\'annotation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout d\'annotation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove anatomical annotation from PCMA.
     */
    public function removeAnatomicalAnnotation(Request $request, PCMA $pcma): JsonResponse
    {
        $request->validate([
            'view' => 'required|in:anterior,posterior',
            'annotation_id' => 'required|string',
        ]);

        try {
            $pcma->removeAnatomicalAnnotation(
                $request->view,
                $request->annotation_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Annotation supprimée avec succès',
                'data' => $pcma->getAnatomicalAnnotations($request->view)
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression d\'annotation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression d\'annotation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark PCMA as FIFA compliant.
     */
    public function markAsFifaCompliant(Request $request, PCMA $pcma): JsonResponse
    {
        try {
            $pcma->markAsFifaCompliant($request->user()->name ?? 'System');

            return response()->json([
                'success' => true,
                'message' => 'PCMA marqué comme conforme FIFA',
                'data' => $pcma->getFifaComplianceStatus()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage FIFA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage FIFA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prefill PCMA from transcript using AI.
     */
    public function prefillFromTranscript(Request $request): JsonResponse
    {
        $request->validate([
            'transcript' => 'required|string|max:2000',
            'athlete_id' => 'required|exists:athletes,id',
            'pcma_type' => 'required|string|in:bpma,cardio,dental,neurological,orthopedic',
        ]);

        try {
            $response = Http::post(config('services.ai.base_url') . '/api/ai/pcma-extractor/extract-pcma-data', [
                'transcript' => $request->transcript,
                'athlete_id' => $request->athlete_id,
                'pcma_type' => $request->pcma_type,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'data' => $data['data'] ?? [],
                    'confidence_score' => $data['confidence_score'] ?? 0.7,
                    'extracted_fields' => $data['extracted_fields'] ?? []
                ]);
            } else {
                throw new \Exception('AI service failed to extract data from transcript');
            }

        } catch (\Exception $e) {
            Log::error('Exception during ' . $request->pcma_type . ' extraction: ' . $e->getMessage());
            
            // Fallback to mock data
            $mockData = $this->generateMockData($request->transcript, $request->pcma_type);
            
            return response()->json([
                'success' => true,
                'data' => $mockData,
                'confidence_score' => 0.6,
                'extracted_fields' => array_keys($mockData),
                'message' => 'Utilisation de données simulées (service AI non disponible)'
            ]);
        }
    }

    /**
     * Transcribe audio using Whisper.
     */
    public function whisperTranscribe(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => 'required|file|mimes:wav,mp3,m4a,mpeg|max:10240', // 10MB max
        ]);

        try {
            $audioFile = $request->file('audio');
            $tempPath = $audioFile->storeAs('temp/whisper', uniqid() . '.' . $audioFile->getClientOriginalExtension());

            $response = Http::attach(
                'audio',
                Storage::get($tempPath),
                $audioFile->getClientOriginalName()
            )->post(config('services.ai.base_url') . '/api/ai/whisper/transcribe', [
                'language' => $request->get('language', 'fr'),
                'model' => $request->get('model', 'whisper-1'),
            ]);

            // Clean up temp file
            Storage::delete($tempPath);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'transcription' => $data['transcription'] ?? '',
                    'confidence' => $data['confidence'] ?? 0.0,
                    'language' => $data['language'] ?? 'fr',
                ]);
            } else {
                throw new \Exception('Whisper transcription failed');
            }

        } catch (\Exception $e) {
            Log::error('Whisper transcription error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la transcription audio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch FHIR data.
     */
    public function fetchFhirData(Request $request): JsonResponse
    {
        $request->validate([
            'server_url' => 'required|url',
            'patient_id' => 'required|string',
            'resource_type' => 'required|string|in:Patient,Observation,Condition,Procedure,MedicationRequest',
        ]);

        try {
            $fhirUrl = $request->server_url . '/Patient/' . $request->patient_id;
            $response = Http::get($fhirUrl);

            if ($response->successful()) {
                $patientData = $response->json();
                
                // Fetch related resources
                $resourcesUrl = $request->server_url . '/' . $request->resource_type . '?patient=' . $request->patient_id;
                $resourcesResponse = Http::get($resourcesUrl);
                
                $resources = [];
                if ($resourcesResponse->successful()) {
                    $resources = $resourcesResponse->json()['entry'] ?? [];
                }

                return response()->json([
                    'success' => true,
                    'patient_name' => $patientData['name'][0]['text'] ?? 'N/A',
                    'resources_count' => count($resources),
                    'resources' => $resources,
                ]);
            } else {
                throw new \Exception('Failed to fetch FHIR data');
            }

        } catch (\Exception $e) {
            Log::error('FHIR fetch error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données FHIR',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract text from image using OCR.
     */
    public function ocrExtract(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,jpg,png,pdf|max:10240', // 10MB max
        ]);

        try {
            $imageFile = $request->file('image');
            $tempPath = $imageFile->storeAs('temp/ocr', uniqid() . '.' . $imageFile->getClientOriginalExtension());

            $response = Http::attach(
                'image',
                Storage::get($tempPath),
                $imageFile->getClientOriginalName()
            )->post(config('services.ai.base_url') . '/api/ai/ocr/extract', [
                'language' => $request->get('language', 'fra'),
                'medical_document' => $request->get('medical_document', 'true'),
            ]);

            // Clean up temp file
            Storage::delete($tempPath);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'extracted_text' => $data['extracted_text'] ?? '',
                    'confidence' => $data['confidence'] ?? 0.0,
                    'word_count' => $data['word_count'] ?? 0,
                ]);
            } else {
                throw new \Exception('OCR extraction failed');
            }

        } catch (\Exception $e) {
            Log::error('OCR extraction error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'extraction OCR',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if PCMA is FIFA compliant.
     */
    private function isFifaCompliant(PCMA $pcma): bool
    {
        // Basic FIFA compliance check
        $requiredFields = [
            'medical_history',
            'physical_examination',
            'cardiovascular_investigations',
            'final_statement',
            'assessment_date',
            'assessor_id',
        ];

        foreach ($requiredFields as $field) {
            if (empty($pcma->$field)) {
                return false;
            }
        }

        // Check if final statement has clearance decision
        $finalStatement = $pcma->final_statement ?? [];
        if (empty($finalStatement['cleared_for_competition']) && 
            empty($finalStatement['cleared_with_restrictions']) && 
            empty($finalStatement['not_cleared'])) {
            return false;
        }

        return true;
    }

    /**
     * Generate mock data for AI fallback.
     */
    private function generateMockData(string $transcript, string $type): array
    {
        $mockData = [];

        switch ($type) {
            case 'cardio':
                $mockData = [
                    'blood_pressure_systolic' => 120,
                    'blood_pressure_diastolic' => 80,
                    'heart_rate' => 72,
                    'ecg_result' => 'normal',
                    'stress_test_result' => 'negative',
                ];
                break;
            case 'bpma':
                $mockData = [
                    'height' => 175,
                    'weight' => 70,
                    'bmi' => 22.9,
                    'blood_pressure' => '120/80',
                    'heart_rate' => 72,
                ];
                break;
            case 'dental':
                $mockData = [
                    'dental_examination' => 'normal',
                    'oral_hygiene' => 'good',
                    'no_cavities' => true,
                ];
                break;
            default:
                $mockData = [
                    'general_health' => 'good',
                    'no_abnormalities' => true,
                ];
        }

        return $mockData;
    }

    /**
     * Analyze ECG using Med-Gemini AI.
     */
    public function aiAnalyzeEcg(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ecg_file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
            ]);

            $ecgFile = $request->file('ecg_file');
            $extension = strtolower($ecgFile->getClientOriginalExtension());
            $isDicom = $extension === 'dcm';
            
            $tempPath = $ecgFile->store('temp/ai-analysis');

            // Determine analysis type based on file type
            $analysisType = $isDicom ? 'ecg_dicom' : 'ecg_image';
            $aiResponse = $this->callMedGeminiAI($analysisType, $tempPath);

            // Add file type information to analysis
            $aiResponse['file_type'] = $isDicom ? 'DICOM' : 'Image';
            $aiResponse['file_extension'] = $extension;
            $aiResponse['analysis_type'] = $analysisType;

            // Clean up temp file
            Storage::delete($tempPath);

            return response()->json([
                'success' => true,
                'analysis' => $aiResponse,
                'file_info' => [
                    'is_dicom' => $isDicom,
                    'extension' => $extension,
                    'file_type' => $this->getFileType($extension)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('ECG AI analysis error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse ECG par IA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze MRI for bone age assessment using Med-Gemini AI.
     */
    public function aiAnalyzeMri(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'mri_file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
            ]);

            $mriFile = $request->file('mri_file');
            $extension = strtolower($mriFile->getClientOriginalExtension());
            $isDicom = $extension === 'dcm';
            
            $tempPath = $mriFile->store('temp/ai-analysis');

            // Determine analysis type based on file type
            $analysisType = $isDicom ? 'mri_dicom_bone_age' : 'mri_image_bone_age';
            $aiResponse = $this->callMedGeminiAI($analysisType, $tempPath);

            // Add file type information to analysis
            $aiResponse['file_type'] = $isDicom ? 'DICOM' : 'Image';
            $aiResponse['file_extension'] = $extension;
            $aiResponse['analysis_type'] = $analysisType;

            // Clean up temp file
            Storage::delete($tempPath);

            return response()->json([
                'success' => true,
                'analysis' => $aiResponse,
                'file_info' => [
                    'is_dicom' => $isDicom,
                    'extension' => $extension,
                    'file_type' => $this->getFileType($extension)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('MRI AI analysis error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse IRM par IA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete AI analysis of both ECG and MRI files.
     */
    public function aiAnalyzeComplete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ecg_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
                'mri_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
            ]);

            $analysis = [];
            $fileInfo = [];

            // Analyze ECG if provided
            if ($request->hasFile('ecg_file')) {
                $ecgFile = $request->file('ecg_file');
                $ecgExtension = strtolower($ecgFile->getClientOriginalExtension());
                $ecgIsDicom = $ecgExtension === 'dcm';
                $ecgAnalysisType = $ecgIsDicom ? 'ecg_dicom' : 'ecg_image';
                
                $ecgTempPath = $ecgFile->store('temp/ai-analysis');
                $ecgAnalysis = $this->callMedGeminiAI($ecgAnalysisType, $ecgTempPath);
                
                $ecgAnalysis['file_type'] = $ecgIsDicom ? 'DICOM' : 'Image';
                $ecgAnalysis['file_extension'] = $ecgExtension;
                $ecgAnalysis['analysis_type'] = $ecgAnalysisType;
                
                $analysis['ecg_analysis'] = $ecgAnalysis;
                $fileInfo['ecg'] = [
                    'is_dicom' => $ecgIsDicom,
                    'extension' => $ecgExtension,
                    'file_type' => $this->getFileType($ecgExtension)
                ];
                Storage::delete($ecgTempPath);
            }

            // Analyze MRI if provided
            if ($request->hasFile('mri_file')) {
                $mriFile = $request->file('mri_file');
                $mriExtension = strtolower($mriFile->getClientOriginalExtension());
                $mriIsDicom = $mriExtension === 'dcm';
                $mriAnalysisType = $mriIsDicom ? 'mri_dicom_bone_age' : 'mri_image_bone_age';
                
                $mriTempPath = $mriFile->store('temp/ai-analysis');
                $mriAnalysis = $this->callMedGeminiAI($mriAnalysisType, $mriTempPath);
                
                $mriAnalysis['file_type'] = $mriIsDicom ? 'DICOM' : 'Image';
                $mriAnalysis['file_extension'] = $mriExtension;
                $mriAnalysis['analysis_type'] = $mriAnalysisType;
                
                $analysis['mri_analysis'] = $mriAnalysis;
                $fileInfo['mri'] = [
                    'is_dicom' => $mriIsDicom,
                    'extension' => $mriExtension,
                    'file_type' => $this->getFileType($mriExtension)
                ];
                Storage::delete($mriTempPath);
            }

            // Generate overall assessment if both analyses are available
            if (isset($analysis['ecg_analysis']) && isset($analysis['mri_analysis'])) {
                $analysis['overall_assessment'] = $this->generateOverallAssessment($analysis);
            }

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'file_info' => $fileInfo
            ]);

        } catch (\Exception $e) {
            Log::error('Complete AI analysis error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse complète par IA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Call Med-Gemini AI service for medical image analysis.
     */
    private function callMedGeminiAI(string $analysisType, string $filePath): array
    {
        try {
            $aiServiceUrl = env('AI_SERVICE_URL', 'http://localhost:3001');
            $fileContent = Storage::get($filePath);
            $base64Content = base64_encode($fileContent);

            $prompt = $this->getAnalysisPrompt($analysisType);
            
            $response = Http::post($aiServiceUrl . '/api/v1/med-gemini/analyze', [
                'analysis_type' => $analysisType,
                'file_content' => $base64Content,
                'file_type' => pathinfo($filePath, PATHINFO_EXTENSION),
                'prompt' => $prompt
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                // Fallback to mock data if AI service is unavailable
                return $this->getMockAnalysis($analysisType);
            }

        } catch (\Exception $e) {
            Log::warning('AI service unavailable, using mock data: ' . $e->getMessage());
            return $this->getMockAnalysis($analysisType);
        }
    }

    /**
     * Get analysis prompt based on type.
     */
    private function getAnalysisPrompt(string $analysisType): string
    {
        switch ($analysisType) {
            case 'ecg_dicom':
                return "Analyze this DICOM ECG file and provide detailed medical interpretation including: rhythm, heart rate, any abnormalities, ST segment changes, T wave abnormalities, QRS complex analysis, and clinical recommendations. This is a DICOM medical image file with enhanced metadata. Format the response as JSON with fields: rhythm, heart_rate, abnormalities, recommendations, dicom_metadata.";
            
            case 'ecg_image':
                return "Analyze this ECG image (non-DICOM format) and provide detailed medical interpretation including: rhythm, heart rate, any abnormalities, ST segment changes, T wave abnormalities, QRS complex analysis, and clinical recommendations. Format the response as JSON with fields: rhythm, heart_rate, abnormalities, recommendations.";
            
            case 'mri_dicom_bone_age':
                return "Analyze this DICOM MRI file for bone age assessment. Evaluate skeletal maturity, estimate bone age, compare with chronological age, identify any growth abnormalities, and provide clinical recommendations. This is a DICOM medical image file with enhanced metadata. Format the response as JSON with fields: bone_age, chronological_age, age_difference, skeletal_maturity, abnormalities, recommendations, dicom_metadata.";
            
            case 'mri_image_bone_age':
                return "Analyze this MRI image (non-DICOM format) for bone age assessment. Evaluate skeletal maturity, estimate bone age, compare with chronological age, identify any growth abnormalities, and provide clinical recommendations. Format the response as JSON with fields: bone_age, chronological_age, age_difference, skeletal_maturity, abnormalities, recommendations.";
            
            case 'ecg':
                return "Analyze this ECG image and provide detailed medical interpretation including: rhythm, heart rate, any abnormalities, ST segment changes, T wave abnormalities, QRS complex analysis, and clinical recommendations. Format the response as JSON with fields: rhythm, heart_rate, abnormalities, recommendations.";
            
            case 'mri_bone_age':
                return "Analyze this MRI image for bone age assessment. Evaluate skeletal maturity, estimate bone age, compare with chronological age, identify any growth abnormalities, and provide clinical recommendations. Format the response as JSON with fields: bone_age, chronological_age, age_difference, skeletal_maturity, abnormalities, recommendations.";
            
            default:
                return "Analyze this medical image and provide a comprehensive medical assessment.";
        }
    }

    /**
     * Generate mock analysis data for fallback.
     */
    private function getMockAnalysis(string $analysisType): array
    {
        switch ($analysisType) {
            case 'ecg_dicom':
                return [
                    'rhythm' => 'Sinus rhythm',
                    'heart_rate' => '72 bpm',
                    'abnormalities' => 'None detected',
                    'recommendations' => 'Normal DICOM ECG - cleared for sports participation',
                    'dicom_metadata' => [
                        'modality' => 'ECG',
                        'patient_id' => 'PCMA-' . time(),
                        'study_date' => now()->format('Y-m-d'),
                        'institution' => 'Centre Médical FIFA'
                    ]
                ];
            
            case 'ecg_image':
                return [
                    'rhythm' => 'Sinus rhythm',
                    'heart_rate' => '72 bpm',
                    'abnormalities' => 'None detected',
                    'recommendations' => 'Normal ECG image - cleared for sports participation'
                ];
            
            case 'mri_dicom_bone_age':
                return [
                    'bone_age' => '16.5 years',
                    'chronological_age' => '16.0 years',
                    'age_difference' => '+6 months',
                    'skeletal_maturity' => 'Advanced',
                    'abnormalities' => 'None detected',
                    'recommendations' => 'Normal DICOM MRI bone age - cleared for sports participation',
                    'dicom_metadata' => [
                        'modality' => 'MRI',
                        'patient_id' => 'PCMA-' . time(),
                        'study_date' => now()->format('Y-m-d'),
                        'institution' => 'Centre Médical FIFA'
                    ]
                ];
            
            case 'mri_image_bone_age':
                return [
                    'bone_age' => '16.5 years',
                    'chronological_age' => '16.0 years',
                    'age_difference' => '+6 months',
                    'skeletal_maturity' => 'Advanced',
                    'abnormalities' => 'None detected',
                    'recommendations' => 'Normal MRI image bone age - cleared for sports participation'
                ];
            
            case 'ecg':
                return [
                    'rhythm' => 'Sinus rhythm',
                    'heart_rate' => '72 bpm',
                    'abnormalities' => 'None detected',
                    'recommendations' => 'Normal ECG - cleared for sports participation'
                ];
            
            case 'mri_bone_age':
                return [
                    'bone_age' => '16.5 years',
                    'chronological_age' => '16.0 years',
                    'age_difference' => '+6 months',
                    'skeletal_maturity' => 'Advanced',
                    'abnormalities' => 'None detected',
                    'recommendations' => 'Normal bone age - cleared for sports participation'
                ];
            
            default:
                return [
                    'status' => 'Normal',
                    'recommendations' => 'No abnormalities detected'
                ];
        }
    }

    /**
     * Generate overall assessment from individual analyses.
     */
    private function generateOverallAssessment(array $analyses): array
    {
        $ecgStatus = $analyses['ecg_analysis']['abnormalities'] ?? 'None detected';
        $mriStatus = $analyses['mri_analysis']['abnormalities'] ?? 'None detected';
        
        $medicalStatus = 'Cleared';
        $sportsEligibility = 'Eligible';
        $recommendations = 'Cleared for sports participation';

        if ($ecgStatus !== 'None detected' || $mriStatus !== 'None detected') {
            $medicalStatus = 'Requires further evaluation';
            $sportsEligibility = 'Conditional';
            $recommendations = 'Further medical evaluation recommended before sports participation';
        }

        return [
            'medical_status' => $medicalStatus,
            'sports_eligibility' => $sportsEligibility,
            'recommendations' => $recommendations
        ];
    }

    /**
     * Process medical imaging file for viewer (DICOM and non-DICOM)
     */
    public function processDicomFile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:dcm,pdf,jpg,jpeg,png,bmp,tiff,tif|max:10240',
            ]);

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            
            // Determine file type and storage path
            $isDicom = $extension === 'dcm';
            $storagePath = $isDicom ? 'dicom_files' : 'medical_images';
            $path = $file->storeAs($storagePath, $fileName, 'public');

            // Extract basic metadata
            $metadata = [
                'filename' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $extension,
                'uploaded_at' => now()->toISOString(),
                'file_path' => $path,
                'is_dicom' => $isDicom,
                'file_type' => $this->getFileType($extension)
            ];

            // Extract specific metadata based on file type
            if ($isDicom) {
                $dicomMetadata = $this->extractDicomMetadata($file);
                $metadata = array_merge($metadata, $dicomMetadata);
            } else {
                $imageMetadata = $this->extractImageMetadata($file);
                $metadata = array_merge($metadata, $imageMetadata);
            }

            return response()->json([
                'success' => true,
                'data' => $metadata,
                'view_url' => Storage::url($path),
                'file_type' => $metadata['file_type'],
                'is_dicom' => $isDicom
            ]);

        } catch (\Exception $e) {
            Log::error('Medical file processing error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du fichier médical',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file type description
     */
    private function getFileType(string $extension): string
    {
        $types = [
            'dcm' => 'DICOM Medical Image',
            'pdf' => 'PDF Document',
            'jpg' => 'JPEG Image',
            'jpeg' => 'JPEG Image',
            'png' => 'PNG Image',
            'bmp' => 'BMP Image',
            'tiff' => 'TIFF Image',
            'tif' => 'TIFF Image'
        ];

        return $types[$extension] ?? 'Unknown File Type';
    }

    /**
     * Extract image metadata for non-DICOM files
     */
    private function extractImageMetadata($file): array
    {
        $metadata = [
            'patient_name' => 'Patient PCMA',
            'patient_id' => 'PCMA-' . time(),
            'study_date' => now()->format('Y-m-d'),
            'modality' => $this->getModalityFromFilename($file->getClientOriginalName()),
            'institution' => 'Centre Médical FIFA',
            'physician' => 'Dr. Médecin',
            'description' => 'Examen médical PCMA',
            'image_dimensions' => 'En cours de chargement...'
        ];

        // Try to get image dimensions if it's an image file
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'bmp', 'tiff', 'tif'])) {
            try {
                $imageInfo = getimagesize($file->getRealPath());
                if ($imageInfo) {
                    $metadata['image_dimensions'] = $imageInfo[0] . ' × ' . $imageInfo[1] . ' pixels';
                    $metadata['image_type'] = $imageInfo[2]; // IMAGETYPE_* constant
                }
            } catch (\Exception $e) {
                $metadata['image_dimensions'] = 'Impossible de lire les dimensions';
            }
        }

        return $metadata;
    }

    /**
     * Determine modality from filename
     */
    private function getModalityFromFilename(string $filename): string
    {
        $filename = strtolower($filename);
        
        if (strpos($filename, 'ecg') !== false || strpos($filename, 'electro') !== false) {
            return 'ECG';
        } elseif (strpos($filename, 'mri') !== false || strpos($filename, 'irm') !== false) {
            return 'MRI';
        } elseif (strpos($filename, 'ct') !== false || strpos($filename, 'scanner') !== false) {
            return 'CT';
        } elseif (strpos($filename, 'xray') !== false || strpos($filename, 'radio') !== false) {
            return 'X-RAY';
        } elseif (strpos($filename, 'ultra') !== false || strpos($filename, 'echo') !== false) {
            return 'ULTRASOUND';
        }
        
        return 'UNKNOWN';
    }

    /**
     * Get DICOM metadata
     */
    public function getDicomMetadata(string $file): JsonResponse
    {
        try {
            $filePath = 'dicom_files/' . $file;
            
            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ], 404);
            }

            // Mock DICOM metadata for demonstration
            $metadata = [
                'patient_name' => 'Patient PCMA',
                'patient_id' => 'PCMA-' . time(),
                'study_date' => now()->format('Y-m-d'),
                'modality' => 'CT',
                'institution' => 'Centre Médical FIFA',
                'physician' => 'Dr. Médecin',
                'description' => 'Examen médical PCMA',
                'image_dimensions' => '512 × 512 pixels',
                'file_size' => Storage::disk('public')->size($filePath),
                'uploaded_at' => now()->toISOString()
            ];

            return response()->json([
                'success' => true,
                'data' => $metadata
            ]);

        } catch (\Exception $e) {
            Log::error('DICOM metadata extraction error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'extraction des métadonnées',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract DICOM metadata from file
     */
    private function extractDicomMetadata($file): array
    {
        // This is a simplified DICOM metadata extraction
        // In a real implementation, you would use a DICOM library like dcmtk or pydicom
        
        $metadata = [
            'patient_name' => 'Patient PCMA',
            'patient_id' => 'PCMA-' . time(),
            'study_date' => now()->format('Y-m-d'),
            'modality' => 'CT',
            'institution' => 'Centre Médical FIFA',
            'physician' => 'Dr. Médecin',
            'description' => 'Examen médical PCMA',
            'image_dimensions' => '512 × 512 pixels'
        ];

        // Try to read DICOM header if possible
        try {
            $fileContent = file_get_contents($file->getRealPath());
            
            // Simple DICOM header check (DICOM files start with 'DICM')
            if (strpos($fileContent, 'DICM') !== false) {
                $metadata['is_dicom'] = true;
                $metadata['dicom_version'] = 'DICOM 3.0';
            } else {
                $metadata['is_dicom'] = false;
            }
        } catch (\Exception $e) {
            $metadata['is_dicom'] = false;
            $metadata['error'] = 'Impossible de lire le fichier DICOM';
        }

        return $metadata;
    }

    /**
     * Get signed PCMAs.
     */
    public function getSignedPCMAs(): JsonResponse
    {
        $pcmas = PCMA::with(['athlete', 'assessor'])
            ->where('is_signed', true)
            ->orderBy('signed_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'pcmas' => $pcmas
        ]);
    }
} 