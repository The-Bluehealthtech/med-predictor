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
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            app(\App\Services\MedicalRecordAccess::class)->authorizeRole($request->user());
            return $next($request);
        });
    }
    private function scopedRecords()
    {
        return app(\App\Services\MedicalRecordAccess::class)->scope(auth()->user(), PCMA::query());
    }

    /**
     * Display a listing of PCMAs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $this->scopedRecords()->with(['player', 'athlete', 'assessor']);

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
            $validatedData = app(\App\Services\MedicalRecordAccess::class)->input($request->user(), $validatedData);
            $validatedData['fifa_compliant'] = false;

            $fifaId = $validatedData['fifa_id'] ?? null;
            unset($validatedData['fifa_connect_id']);
            
            // Set default values
            $validatedData['status'] = $validatedData['status'] ?? 'pending';
            $validatedData['form_version'] = $validatedData['form_version'] ?? '1.0';
            $validatedData['last_updated_at'] = now();
            
            // Le lien interne validé est conservé ; le FIFA ID ne remplace jamais le joueur.
            // Handle file uploads
            $fileFields = ['ecg_file', 'mri_file', 'xray_file', 'ct_scan_file', 'ultrasound_file'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $path = $file->store('medical_imaging', 'local');
                    $validatedData[$field] = $path;
                }
            }
            
            // Create PCMA
            $validatedData = app(\App\Services\MedicationCatalogue::class)->applySelection($validatedData);
            $validatedData = app(\App\Services\WhoIcd11::class)->applySelections($validatedData);
            $pcma = PCMA::create($validatedData);

            // Dispatch event for cardio PCMAs
            if ($pcma->type === 'cardio') {
                CardioPCMASubmitted::dispatch($pcma);
            }

            // Une complétude de formulaire ne constitue pas une certification FIFA.

            return response()->json([
                'success' => true,
                'message' => 'PCMA créé avec succès',
                'data' => $pcma->load(['player', 'athlete', 'assessor'])
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du PCMA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du PCMA',
                'error' => __('pcma_workflow.service_unavailable')
            ], 500);
        }
    }

    /**
     * Store a draft PCMA.
     */
    public function storeDraft(Request $request): JsonResponse
    {
        return app(\App\Http\Controllers\PcmaDraftController::class)->save($request);
    }

    /**
     * Display the specified PCMA.
     */
    public function show(PCMA $pcma): JsonResponse
    {
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma);
        $pcma->load(['player', 'athlete', 'assessor']);

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
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma, true);
        try {
            $validatedData = $request->validated();
            $validatedData = app(\App\Services\MedicalRecordAccess::class)->input($request->user(), $validatedData);
            $validatedData = app(\App\Services\PcmaFormData::class)->withoutSignature($validatedData);
            abort_if(isset($validatedData['player_id'])
                && (int) $validatedData['player_id'] !== (int) $pcma->player_id, 409,
                'Le joueur d’un dossier existant ne peut pas être changé.');
            $validatedData['fifa_compliant'] = false;
            unset($validatedData['fifa_connect_id']);
            $validatedData['last_updated_at'] = now();

            // Handle file uploads
            $fileFields = ['ecg_file', 'mri_file', 'xray_file', 'ct_scan_file', 'ultrasound_file'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $path = $file->store('medical_imaging', 'local');
                    $validatedData[$field] = $path;
                }
            }

            $validatedData = app(\App\Services\MedicationCatalogue::class)->applySelection($validatedData, $pcma->result_json);
            $validatedData = app(\App\Services\WhoIcd11::class)->applySelections($validatedData, $pcma->result_json);
            $pcma->update($validatedData);

            // Une complétude de formulaire ne constitue pas une certification FIFA.

            return response()->json([
                'success' => true,
                'message' => 'PCMA mis à jour avec succès',
                'data' => $pcma->load(['player', 'athlete', 'assessor'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du PCMA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du PCMA',
                'error' => __('pcma_workflow.service_unavailable')
            ], 500);
        }
    }

    /**
     * Remove the specified PCMA.
     */
    public function destroy(PCMA $pcma): JsonResponse
    {
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma, true);
        try {
            $pcma->delete();

            return response()->json([
                'success' => true,
                'message' => 'PCMA supprimé avec succès'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du PCMA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du PCMA',
                'error' => __('pcma_workflow.service_unavailable')
            ], 500);
        }
    }

    /**
     * Get PCMAs for a specific athlete.
     */
    public function getAthletePCMAs(Athlete $athlete): JsonResponse
    {
        $this->authorizeAthleteMedicalAccess($athlete);

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
        $this->authorizeAthleteMedicalAccess($athlete);

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

    private function authorizeAthleteMedicalAccess(Athlete $athlete): void
    {
        app(\App\Services\MedicalRecordAccess::class)->authorize(auth()->user(), null, $athlete);
    }

    /**
     * Get PCMA data for a specific player
     */
    public function getPlayerPCMAs(Player $player): JsonResponse
    {
        app(\App\Services\MedicalRecordAccess::class)->authorize(auth()->user(), $player, null);
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des PCMA du joueur: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des PCMA du joueur',
                'error' => __('pcma_workflow.service_unavailable')
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des PCMA FIFA Connect: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des PCMA FIFA Connect',
                'error' => __('pcma_workflow.service_unavailable')
            ], 500);
        }
    }

    /**
     * Add anatomical annotation to PCMA.
     */
    public function addAnatomicalAnnotation(Request $request, PCMA $pcma): JsonResponse
    {
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma, true);
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout d\'annotation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout d\'annotation',
                'error' => __('pcma_workflow.service_unavailable')
            ], 500);
        }
    }

    /**
     * Remove anatomical annotation from PCMA.
     */
    public function removeAnatomicalAnnotation(Request $request, PCMA $pcma): JsonResponse
    {
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma, true);
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression d\'annotation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression d\'annotation',
                'error' => __('pcma_workflow.service_unavailable')
            ], 500);
        }
    }

    /**
     * Mark PCMA as FIFA compliant.
     */
    public function markAsFifaCompliant(Request $request, PCMA $pcma): JsonResponse
    {
        app(\App\Services\MedicalRecordAccess::class)->record($request->user(), $pcma);
        return response()->json(['success' => false,
            'message' => 'La certification FIFA doit être vérifiée ; elle ne peut pas être déclarée automatiquement.'], 409);
    }

    /**
     * Prefill PCMA from transcript using AI.
     */
    public function prefillFromTranscript(Request $request): JsonResponse
    {
        $request->validate([
            'transcript' => 'required|string|max:2000',
            'athlete_id' => 'nullable|required_without:player_id|exists:athletes,id',
            'player_id' => 'nullable|required_without:athlete_id|exists:players,id',
            'pcma_type' => 'required|string|in:bpma,cardio,dental,neurological,orthopedic',
        ]);

        app(\App\Services\MedicalRecordAccess::class)->input($request->user(),
            array_merge($request->only(['player_id', 'athlete_id']), ['assessor_id' => $request->user()->id]));
        try {
            $response = Http::timeout(config('services.ai.timeout', 30))->withToken(config('services.ai.api_key', ''))->post(config('services.ai.base_url') . '/api/ai/pcma-extractor/extract-pcma-data', [
                'transcript' => $request->transcript,
                ...$request->only(['athlete_id', 'player_id']),
                'pcma_type' => $request->pcma_type,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                app(\App\Services\MedicalAiResult::class)->rejectSimulated($data ?? []);
                if (($data['success'] ?? null) !== true || !is_array($data['data'] ?? null))
                    throw new \RuntimeException('Invalid extraction response');

                return response()->json([
                    'success' => true,
                    'data' => $data['data'] ?? [],
                    'confidence_score' => $data['confidence_score'] ?? null,
                    'extracted_fields' => $data['extracted_fields'] ?? []
                ]);
            } else {
                throw new \Exception('AI service failed to extract data from transcript');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Exception during ' . $request->pcma_type . ' extraction: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Service d’extraction IA indisponible.',
            ], 503);
        }
    }

    /**
     * Transcribe audio using Whisper.
     */
    public function whisperTranscribe(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => 'required|file|mimes:wav,mp3,m4a,mpeg,webm,ogg|max:10240', // 10MB max
        ]);

        try {
            $audioFile = $request->file('audio');
            $tempPath = $audioFile->storeAs('temp/whisper', uniqid() . '.' . $audioFile->getClientOriginalExtension());

            $response = Http::timeout(config('services.ai.timeout', 30))->withToken(config('services.ai.api_key', ''))->attach(
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
                app(\App\Services\MedicalAiResult::class)->rejectSimulated($data ?? []);
                if (($data['success'] ?? null) !== true || !is_string($data['transcription'] ?? null)
                    || trim($data['transcription']) === '') throw new \RuntimeException('Empty extraction');

                return response()->json([
                    'success' => true,
                    'transcription' => $data['transcription'] ?? '',
                    'confidence' => $data['confidence'] ?? null,
                    'language' => $data['language'] ?? 'fr',
                ]);
            } else {
                throw new \Exception('Whisper transcription failed');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Whisper transcription error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la transcription audio',
                'error' => __('pcma_workflow.service_unavailable')
            ], 503);
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

        $configured = rtrim(config('services.fhir.base_url', ''), '/');
        abort_unless($configured && rtrim($request->server_url, '/') === $configured,
            422, 'Le serveur FHIR doit correspondre à la configuration de la plateforme.');
        try {
            $fhirUrl = $configured . '/Patient/' . rawurlencode($request->patient_id);
            $response = Http::timeout(config('services.fhir.timeout', 30))->withOptions(['allow_redirects' => false])->get($fhirUrl);

            if ($response->successful()) {
                $patientData = $response->json();
                
                // Fetch related resources
                $resourcesUrl = $configured . '/' . $request->resource_type . '?patient=' . rawurlencode($request->patient_id);
                $resourcesResponse = Http::timeout(config('services.fhir.timeout', 30))->withOptions(['allow_redirects' => false])->get($resourcesUrl);
                
                $resources = [];
                if ($resourcesResponse->successful()) {
                    $resources = $resourcesResponse->json()['entry'] ?? [];
                }

                return response()->json([
                    'success' => true,
                    'patient_name' => $patientData['name'][0]['text'] ?? null,
                    'resources_count' => count($resources),
                    'resources' => $resources,
                ]);
            } else {
                throw new \Exception('Failed to fetch FHIR data');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('FHIR fetch error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données FHIR',
                'error' => __('pcma_workflow.service_unavailable')
            ], 503);
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

            $response = Http::timeout(config('services.ai.timeout', 30))->withToken(config('services.ai.api_key', ''))->attach(
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
                app(\App\Services\MedicalAiResult::class)->rejectSimulated($data ?? []);
                if (($data['success'] ?? null) !== true || !is_string($data['extracted_text'] ?? null)
                    || trim($data['extracted_text']) === '') throw new \RuntimeException('Empty extraction');

                return response()->json([
                    'success' => true,
                    'extracted_text' => $data['extracted_text'] ?? '',
                    'confidence' => $data['confidence'] ?? null,
                    'word_count' => $data['word_count'] ?? 0,
                ]);
            } else {
                throw new \Exception('OCR extraction failed');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('OCR extraction error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'extraction OCR',
                'error' => __('pcma_workflow.service_unavailable')
            ], 503);
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
     * Analyze ECG using Med-Gemini AI.
     */
    public function aiAnalyzeEcg(Request $request): JsonResponse
    {
        return app(\App\Http\Controllers\PCMAController::class)->aiAnalyzeEcg($request);
    }

    /**
     * Analyze MRI for bone age assessment using Med-Gemini AI.
     */
    public function aiAnalyzeMri(Request $request): JsonResponse
    {
        if ($request->hasFile('mri_file') && !$request->hasFile('mri_files')) {
            $request->files->set('mri_files', [$request->file('mri_file')]);
        }
        return app(\App\Http\Controllers\PCMAController::class)->aiAnalyzeMri($request);
    }

    /**
     * Complete AI analysis of both ECG and MRI files.
     */
    public function aiAnalyzeComplete(Request $request): JsonResponse
    {
        if ($request->hasFile('mri_file') && !$request->hasFile('mri_files')) {
            $request->files->set('mri_files', [$request->file('mri_file')]);
        }
        return app(\App\Http\Controllers\PCMAController::class)->aiAnalyzeComplete($request);
    }

    /**
     * Call Med-Gemini AI service for medical image analysis.
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Medical file processing error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du fichier médical',
                'error' => __('pcma_workflow.service_unavailable')
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
        // NOTE (audit factice -> reel, 2026-09) : patient_name,
        // patient_id, study_date, institution, physician et description
        // étaient des valeurs fixes ("Patient PCMA", "Dr. Médecin",
        // "Centre Médical FIFA"), identiques pour tout fichier, alors
        // qu'aucune de ces informations n'est réellement extraite de
        // l'image. La modalité (déjà réelle, déduite du nom de fichier)
        // et les dimensions (déjà réelles, via getimagesize() ci-dessous)
        // sont conservées ; le reste est explicitement "non disponible".
        $metadata = [
            'patient_name' => null,
            'patient_id' => null,
            'study_date' => null,
            'modality' => $this->getModalityFromFilename($file->getClientOriginalName()),
            'institution' => null,
            'physician' => null,
            'description' => null,
            'image_dimensions' => null
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
            } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
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

            // NOTE (audit factice -> reel, 2026-09) : ces métadonnées
            // étaient entièrement fixes ("Patient PCMA", "Dr. Médecin",
            // "Centre Médical FIFA", modalité toujours "CT", dimensions
            // toujours "512 × 512 pixels"), identiques pour tout fichier,
            // présentées comme si elles provenaient réellement du DICOM.
            // Aucune bibliothèque DICOM (dcmtk/pydicom ou équivalent PHP)
            // n'est installée dans cette application : les tags DICOM
            // réels (nom patient, établissement, médecin...) ne peuvent
            // donc pas être extraits. Seules les données réellement
            // disponibles sont renseignées ; le reste est explicitement
            // "non disponible" plutôt qu'inventé.
            $metadata = [
                'patient_name' => null,
                'patient_id' => null,
                'study_date' => null,
                'modality' => $this->getModalityFromFilename($file),
                'institution' => null,
                'physician' => null,
                'description' => null,
                'image_dimensions' => null,
                'dicom_tags_available' => false,
                'note' => "Extraction des tags DICOM non disponible : aucune bibliothèque DICOM n'est installée sur ce serveur.",
                'file_size' => Storage::disk('public')->size($filePath),
                'uploaded_at' => now()->toISOString()
            ];

            return response()->json([
                'success' => true,
                'data' => $metadata
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('DICOM metadata extraction error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'extraction des métadonnées',
                'error' => __('pcma_workflow.service_unavailable')
            ], 500);
        }
    }

    /**
     * Extract DICOM metadata from file
     */
    private function extractDicomMetadata($file): array
    {
        // NOTE (audit factice -> reel, 2026-09) : ces métadonnées étaient
        // entièrement fixes ("Patient PCMA", "Dr. Médecin", "Centre
        // Médical FIFA", modalité toujours "CT", dimensions toujours
        // "512 × 512 pixels"), identiques pour tout fichier. Aucune
        // bibliothèque DICOM (dcmtk/pydicom ou équivalent PHP) n'est
        // installée dans cette application : les tags DICOM réels ne
        // peuvent donc pas être extraits. Seule la modalité est déduite
        // (de façon réelle, à partir du nom de fichier) ; le reste est
        // explicitement "non disponible" plutôt qu'inventé.
        $metadata = [
            'patient_name' => null,
            'patient_id' => null,
            'study_date' => null,
            'modality' => $this->getModalityFromFilename($file->getClientOriginalName()),
            'institution' => null,
            'physician' => null,
            'description' => null,
            'image_dimensions' => null,
            'dicom_tags_available' => false
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Données invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
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
        $pcmas = $this->scopedRecords()->with(['player', 'athlete', 'assessor'])
            ->where('is_signed', true)
            ->orderBy('signed_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'pcmas' => $pcmas
        ]);
    }
}
