<?php

namespace App\Http\Controllers;

use App\Models\PCMA;
use App\Models\FifaConnect\Organisation as FifaOrganisation;
use App\Models\FifaConnect\Person as FifaPerson;
use App\Models\FifaConnect\Registration as FifaRegistration;
use App\Models\Player;
use App\Models\Athlete;
use App\Models\User;
use App\Rules\FifaIdentifier;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

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
    public function signed()
    {
        return response()->json(['success' => true, 'pcmas' => $this->scopedRecords()
            ->with(['player', 'athlete', 'assessor'])->where('is_signed', true)->orderByDesc('signed_at')->get()]);
    }

    private function activeTeamDoctorRegistration(User $user): ?FifaRegistration
    {
        $fifaId = $user->fifa_connect_id;
        if (!$fifaId || !in_array($user->role, ['team_doctor', 'doctor', 'club_medical', 'association_medical'], true)) {
            return null;
        }

        $person = FifaPerson::where('person_fifa_id', $fifaId)->first();
        if (!$person) {
            return null;
        }

        $today = now()->toDateString();
        return FifaRegistration::where('person_id', $person->id)
            ->where('person_fifa_id', $fifaId)
            ->where('registration_type', FifaRegistration::TYPE_TEAM_OFFICIAL)
            ->where('team_official_role', 'TeamDoctor')
            ->where('status', 'active')
            ->whereDate('registration_valid_from', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('registration_valid_to')
                    ->orWhereDate('registration_valid_to', '>=', $today);
            })->first();
    }

    public function index(): View
    {
        $pcmas = $this->scopedRecords()->with(['player', 'athlete', 'assessor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pcma.index', compact('pcmas'));
    }

    public function create(): View
    {
        // Vérifier les autorisations
        $user = auth()->user();
        
        abort_unless($user, 401);
        
        $athletes = app(\App\Services\MedicalRecordAccess::class)
            ->scopePlayers($user, Player::query())->orderBy('first_name')->orderBy('last_name')->get();
        
        // Seul le médecin connecté, identifié par FIFA et inscrit TeamDoctor,
        // peut signer. Les autres utilisateurs ne sont pas proposés comme signataires.
        $teamDoctorRegistration = $this->activeTeamDoctorRegistration($user);
        // Un médecin peut préparer un brouillon ; seul TeamDoctor peut signer.
        $users = collect([$user]);

        // PCMA ouvert depuis une visite PCMA du secrétariat médical : joueur imposé, visite rattachée.
        $pcmaVisit = null;
        if ($visitId = request()->integer('visit_id')) {
            $visit = \App\Models\Visit::with('athlete')->findOrFail($visitId);
            abort_unless($athletes->contains('id', (int) $visit->athlete?->player_id), 403);
            try {
                $pcmaVisit = app(\App\Services\Medical\PcmaVisit::class)->linkable($visit->id, (int) $visit->athlete->player_id);
            } catch (\Illuminate\Validation\ValidationException $e) {
                abort(422, collect($e->errors())->flatten()->first());
            }
            $pcmaVisit->load('appointment');
        }

        return view('pcma.create', compact('athletes', 'users', 'teamDoctorRegistration', 'pcmaVisit'));
    }

    /**
     * Recherche d'un joueur pour le PCMA (saisie vocale : FIFA ID ou nom dicté), limitée aux joueurs
     * auxquels le médecin a accès. Identité minimale, aucune donnée médicale ; rien n'est journalisé.
     */
    public function searchPlayers(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);
        $data = $request->validate(['fifa_id' => 'nullable|string|max:20|regex:/^[A-Za-z0-9]+$/', 'name' => 'nullable|string|min:2|max:100']);
        abort_unless(!empty($data['fifa_id']) || !empty($data['name']), 422, 'FIFA ID ou nom requis.');
        $query = app(\App\Services\MedicalRecordAccess::class)->scopePlayers($user, Player::query())->with('club');
        if (!empty($data['fifa_id'])) {
            $query->where('fifa_connect_id', strtoupper($data['fifa_id']));
        } else {
            $term = '%' . str_replace(['%', '_'], ['\\%', '\\_'], trim($data['name'])) . '%';
            $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('first_name', 'like', $term)->orWhere('last_name', 'like', $term));
        }
        $player = $query->orderBy('last_name')->first();
        if (!$player) {
            return response()->json(['success' => false, 'message' => 'Aucun joueur de votre périmètre ne correspond.']);
        }

        return response()->json(['success' => true, 'player' => [
            'id' => $player->id,
            'name' => trim($player->first_name . ' ' . $player->last_name) ?: $player->name,
            'fifa_connect_id' => $player->fifa_connect_id,
            'club' => $player->club?->name,
            'position' => $player->position,
            'age' => $player->date_of_birth ? \Illuminate\Support\Carbon::parse($player->date_of_birth)->age : null,
            'nationality' => $player->nationality,
        ]]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate(app(\App\Services\PcmaFormData::class)->rules());
            $validated = app(\App\Services\MedicalRecordAccess::class)->input($request->user(), $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ne pas journaliser les données médicales de la requête.
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'details' => $e->errors()
                ], 422);
            }
            
            throw $e;
        }
        
        $validated = $this->normalizeFifaIdentifierInput(
            $validated
        );
        if (!empty($validated['visit_id'])) {
            // PCMA réalisé pendant une visite PCMA du secrétariat médical.
            app(\App\Services\Medical\PcmaVisit::class)->linkable((int) $validated['visit_id'], (int) $validated['player_id'], !empty($validated['pcma_id']) ? (int) $validated['pcma_id'] : null);
        } else {
            unset($validated['visit_id']); // un brouillon déjà rattaché garde sa visite
        }

        // La sélection client ne constitue pas une preuve d'identité du signataire.

        // La vérification du médecin ne certifie pas le PCMA comme objet FIFA Connect.
        $validated = app(\App\Services\PcmaFormData::class)->withoutSignature($validated);
        $validated['fifa_compliant'] = false;

                        // Handle signature data
                if ($request->boolean('is_signed')) {
                    $doctor = $request->user();
                    $registration = $doctor ? $this->activeTeamDoctorRegistration($doctor) : null;
                    $player = Player::find($validated['player_id']);
                    $sameClub = $registration && $player?->club_id
                        && FifaOrganisation::where('organisation_fifa_id', $registration->organisation_fifa_id)
                            ->where('club_id', $player->club_id)->exists();
                    if (!$sameClub || (int) $validated['assessor_id'] !== (int) $doctor->id) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'assessor_id' => 'Signature réservée au médecin connecté disposant d’un FIFA ID et d’une inscription TeamDoctor active.',
                        ]);
                    }
                    $validated['is_signed'] = true;
                    $validated['signed_at'] = now();
                    $validated['signed_by'] = $doctor->name;
                    $validated['license_number'] = null;
                    $signaturePayload = json_decode($request->input('signature_data', '{}'), true);
                    $signaturePayload = is_array($signaturePayload) ? $signaturePayload : [];
                    unset($signaturePayload['licenseNumber'], $signaturePayload['signedBy'], $signaturePayload['doctorFifaId']);
                    $signaturePayload['signedBy'] = $doctor->name;
                    $signaturePayload['doctorFifaId'] = $registration->person_fifa_id;
                    $signaturePayload['teamDoctorRegistrationId'] = $registration->id;
                    $signaturePayload['finalStatement'] = $validated['final_statement'];
                    $signaturePayload['signedAt'] = $validated['signed_at']->toISOString();
                    $validated['signature_data'] = $signaturePayload;
                    
                    // Add default result_json if not provided
                    if (!isset($validated['result_json'])) {
                        $validated['result_json'] = json_encode([
                            'assessment_type' => $validated['type'],
                            'status' => $validated['status'],
                            'signed_by' => $doctor->name,
                            'signed_at' => $validated['signed_at']->toISOString(),
                            'signature_data' => $signaturePayload
                        ]);
                    }
            
            // Handle signature image (base64 data)
            if ($request->has('signature_image')) {
                $signatureData = $request->signature_image;
                if (is_string($signatureData) && str_starts_with($signatureData, 'data:image/png;base64,')) {
                    $imageData = base64_decode(substr($signatureData, 22), true);
                    if (!$imageData || !str_starts_with($imageData, "\x89PNG\r\n\x1a\n")) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'signature_image' => 'Image de signature PNG invalide.']);
                    }
                    // En base : le service n'a pas de disque persistant.
                    $validated['signature_image'] = \App\Models\MedicalFile::query()->create([
                        'owner_type' => 'pcma', 'field' => 'signature_image', 'file_name' => 'signature.png', 'mime_type' => 'image/png',
                        'size' => strlen($imageData), 'sha256' => hash('sha256', $imageData), 'content_base64' => base64_encode($imageData),
                        'uploaded_by' => auth()->id(),
                    ])->ref();
                }
            }
        }
        
        // Handle file uploads
        $fileFields = ['ecg_file', 'mri_file', 'xray_file', 'ct_scan_file', 'ultrasound_file'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $path = app(\App\Services\MedicalFileStore::class)->put($file, 'pcma', $field)->ref(); // en base : pas de disque persistant
                $validated[$field] = $path;
            }
        }
        
                        // Add default result_json if not provided (for non-signed PCMAs)
                if (!isset($validated['result_json'])) {
                    $validated['result_json'] = json_encode([
                        'assessment_type' => $validated['type'],
                        'status' => $validated['status'],
                        'created_at' => now()->toISOString()
                    ]);
                }
                
                $pcma = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
                    $record = !empty($validated['pcma_id'])
                        ? PCMA::lockForUpdate()->findOrFail($validated['pcma_id']) : new PCMA();
                    if ($record->exists) {
                        app(\App\Services\MedicalRecordAccess::class)->record($request->user(), $record, true);
                        abort_unless($record->status === 'pending'
                            && (int) $record->player_id === (int) $validated['player_id'], 409);
                    }
                    $payload = $this->preserveClinicalFields($validated, $record->result_json);
                    unset($payload['pcma_id'], $payload['draft_token']);
                    $record->fill($payload)->save();
                    return $record;
                });

        // Return JSON response for API calls
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'pcma_id' => $pcma->id,
                'message' => 'PCMA créé avec succès.'
            ]);
        }

        return redirect()->route('pcma.show', $pcma)
            ->with('success', 'PCMA créé avec succès.');
    }

    public function show(PCMA $pcma): View
    {
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma);
        $pcma->load(['player', 'athlete', 'assessor']);
        $documentSignatureProviders = collect(app(\App\Services\Documents\DocumentSignatureService::class)->allStatuses());
        $documentSignatureRequests = \App\Models\DocumentSignatureRequest::query()
            ->where('workflow', 'pcma.final_document')
            ->whereRaw("metadata->'document'->>'pcma_id' = ?", [(string) $pcma->id])
            ->latest('id')->get();
        
        return view('pcma.show', compact('pcma', 'documentSignatureProviders', 'documentSignatureRequests'));
    }

    public function edit(PCMA $pcma): View
    {
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma);
        // Réutiliser exactement le périmètre de joueurs autorisé à la création.
        $data = $this->create()->getData();
        return view('pcma.edit', array_merge($data, ['pcma' => $pcma]));
    }

    public function update(Request $request, PCMA $pcma): RedirectResponse
    {
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma, true);
        $validated = $request->validate(app(\App\Services\PcmaFormData::class)->rules(false));
        $validated = app(\App\Services\MedicalRecordAccess::class)->input($request->user(), $validated);


        $validated = $this->normalizeFifaIdentifierInput(
            $validated
        );

        // Handle FIFA compliant checkbox
        $validated = app(\App\Services\PcmaFormData::class)->withoutSignature($validated);
        abort_unless((int) $validated['player_id'] === (int) $pcma->player_id,
            409, 'Le joueur d’un dossier existant ne peut pas être changé.');
        // La visite d'un PCMA déjà rattaché ne change pas ; un PCMA libre peut être rattaché à une visite PCMA ouverte.
        if ($pcma->visit_id) {
            $validated['visit_id'] = $pcma->visit_id;
        } elseif (!empty($validated['visit_id'])) {
            app(\App\Services\Medical\PcmaVisit::class)->linkable((int) $validated['visit_id'], (int) $pcma->player_id, $pcma->id);
        } else {
            unset($validated['visit_id']);
        }
        $validated['fifa_compliant'] = false;
        
        // Handle file uploads
        $fileFields = ['ecg_file', 'mri_file', 'xray_file', 'ct_scan_file', 'ultrasound_file'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $path = app(\App\Services\MedicalFileStore::class)->put($file, 'pcma', $field)->ref(); // en base : pas de disque persistant
                $validated[$field] = $path;
            }
        }

        $validated = $this->preserveClinicalFields($validated, $pcma->result_json);
        $pcma->update($validated);

        return redirect()->route('pcma.show', $pcma)
            ->with('success', 'PCMA mis à jour avec succès.');
    }

    public function destroy(PCMA $pcma): RedirectResponse
    {
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma, true);
        $pcma->delete();

        return redirect()->route('pcma.index')
            ->with('success', 'PCMA supprimé avec succès.');
    }

    public function complete(PCMA $pcma): RedirectResponse
    {
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma, true);
        $pcma->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('pcma.show', $pcma)
            ->with('success', 'PCMA marqué comme complété.');
    }

    public function fail(PCMA $pcma): RedirectResponse
    {
        app(\App\Services\MedicalRecordAccess::class)->record(auth()->user(), $pcma, true);
        $pcma->update([
            'status' => 'failed',
            'completed_at' => now(),
        ]);

        return redirect()->route('pcma.show', $pcma)
            ->with('success', 'PCMA marqué comme échoué.');
    }

    public function dashboard(): View
    {
        $stats = [
            'total_pcmas' => $this->scopedRecords()->count(),
            'pending_pcmas' => $this->scopedRecords()->where('status', 'pending')->count(),
            'completed_pcmas' => $this->scopedRecords()->where('status', 'completed')->count(),
            'failed_pcmas' => $this->scopedRecords()->where('status', 'failed')->count(),
        ];

        $recentPcmas = $this->scopedRecords()->with(['player', 'athlete', 'assessor'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('pcma.dashboard', compact('stats', 'recentPcmas'));
    }

    public function exportPdf(PCMA $pcma)
    {
        return app(PcmaDocumentController::class)->export($pcma);
    }

    private function preserveClinicalFields(array $data, $previous = null): array
    {
        return app(\App\Services\PcmaFormData::class)->preserve($data, $previous);
    }

    private function normalizeFifaIdentifierInput(
        array $validated
    ): array {
        $legacy = $validated['fifa_connect_id'] ?? null;
        $official = $validated['fifa_id'] ?? null;

        if ($legacy && $official && $legacy !== $official) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'fifa_connect_id' =>
                    'The legacy FIFA Connect field must match fifa_id.',
            ]);
        }

        if ($legacy && !$official) {
            $validated['fifa_id'] = $legacy;
        }

        unset($validated['fifa_connect_id']);

        return $validated;
    }

    // AI Analysis Methods
    public function aiAnalyzeEcg(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ecg_file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
            ]);

            $file = $request->file('ecg_file');
            $fileName = time() . '_ecg.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('temp_analysis', $fileName, 'local');

            $extension = strtolower($file->getClientOriginalExtension());
            $isDicom = $extension === 'dcm';
            $analysisType = $isDicom ? 'ecg_dicom' : 'ecg_image';

            $result = $this->callMedGeminiAI($analysisType, $path);

            // Clean up temporary file
            Storage::disk('local')->delete($path);

            return response()->json([
                'success' => true,
                'analysis' => $result['analysis'] ?? $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false,
                'message' => 'Données fournies invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PCMA AI service unavailable');
            
            return response()->json([
                'success' => false,
                'message' => __('pcma_workflow.service_unavailable')
            ], 503);
        }
    }

    public function aiAnalyzeMri(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'mri_files' => 'required|array',
                'mri_files.*' => 'file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
            ]);

            $files = $request->file('mri_files');
            $analyses = [];
            $tempFiles = [];

            foreach ($files as $index => $file) {
                $fileName = time() . '_mri_' . $index . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('temp_analysis', $fileName, 'local');
                $tempFiles[] = $path;

                $extension = strtolower($file->getClientOriginalExtension());
                $isDicom = $extension === 'dcm';
                $analysisType = $isDicom ? 'mri_dicom_bone_age' : 'mri_image_bone_age';

                $result = $this->callMedGeminiAI($analysisType, $path);
                $analyses[] = [
                    'file_name' => $file->getClientOriginalName(),
                    'analysis' => $result['analysis'] ?? $result
                ];
            }

            // Clean up temporary files
            foreach ($tempFiles as $tempFile) {
                Storage::disk('local')->delete($tempFile);
            }

            // Combine all analyses into a comprehensive report
            $combinedAnalysis = $this->combineMultipleAnalyses($analyses, 'MRI');

            return response()->json([
                'success' => true,
                'analysis' => $combinedAnalysis
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false,
                'message' => 'Données fournies invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PCMA AI service unavailable');
            
            return response()->json([
                'success' => false,
                'message' => __('pcma_workflow.service_unavailable')
            ], 503);
        }
    }

    public function aiAnalyzeXray(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'xray_file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            ]);

            $file = $request->file('xray_file');
            $fileName = time() . '_xray.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('temp_analysis', $fileName, 'local');

            $extension = strtolower($file->getClientOriginalExtension());
            $isDicom = $extension === 'dcm';
            $analysisType = $isDicom ? 'xray_dicom' : 'xray_image';

            $result = $this->callMedGeminiAI($analysisType, $path);

            // Clean up temporary file
            Storage::disk('local')->delete($path);

            return response()->json([
                'success' => true,
                'analysis' => $result['analysis'] ?? $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false,
                'message' => 'Données fournies invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PCMA AI service unavailable');
            
            // Clean up temporary file if it exists
            if (isset($path) && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
            
            return response()->json([
                'success' => false,
                'message' => __('pcma_workflow.service_unavailable')
            ], 503);
        }
    }

    public function aiAnalyzeEcgEffort(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ecg_effort_file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif,svg,webp|max:10240',
            ]);

            $file = $request->file('ecg_effort_file');
            $fileName = time() . '_ecg_effort.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('temp_analysis', $fileName, 'local');

            $extension = strtolower($file->getClientOriginalExtension());
            $isDicom = $extension === 'dcm';
            $analysisType = $isDicom ? 'ecg_effort_dicom' : 'ecg_effort_image';

            $result = $this->callMedGeminiAI($analysisType, $path);

            // Clean up temporary file
            Storage::disk('local')->delete($path);

            return response()->json([
                'success' => true,
                'analysis' => $result['analysis'] ?? $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false,
                'message' => 'Données fournies invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PCMA AI service unavailable');
            
            return response()->json([
                'success' => false,
                'message' => __('pcma_workflow.service_unavailable')
            ], 503);
        }
    }

    public function aiAnalyzeScintigraphy(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'scintigraphy_file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif,svg,webp|max:10240',
            ]);

            $file = $request->file('scintigraphy_file');
            $fileName = time() . '_scintigraphy.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('temp_analysis', $fileName, 'local');

            $extension = strtolower($file->getClientOriginalExtension());
            $isDicom = $extension === 'dcm';
            $analysisType = $isDicom ? 'scintigraphy_dicom' : 'scintigraphy_image';

            $result = $this->callMedGeminiAI($analysisType, $path);

            // Clean up temporary file
            Storage::disk('local')->delete($path);

            return response()->json([
                'success' => true,
                'analysis' => $result['analysis'] ?? $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false,
                'message' => 'Données fournies invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PCMA AI service unavailable');
            
            return response()->json([
                'success' => false,
                'message' => __('pcma_workflow.service_unavailable')
            ], 503);
        }
    }

    public function aiAnalyzeScat(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'evaluation_date' => 'required|date',
                'context' => 'required|string',
                'evaluator_name' => 'required|string',
                'red_flags' => 'array',
                'observable_signs' => 'array',
                'symptoms' => 'array',
                'sac' => 'array',
                'mbess' => 'array',
                'medical_decision' => 'required|string',
                'follow_up_plan' => 'nullable|string',
            ]);

            // Prepare SCAT data for AI analysis
            $scatData = [
                'evaluation_date' => $request->input('evaluation_date'),
                'context' => $request->input('context'),
                'evaluator_name' => $request->input('evaluator_name'),
                'red_flags' => $request->input('red_flags', []),
                'observable_signs' => $request->input('observable_signs', []),
                'symptoms' => $request->input('symptoms', []),
                'sac' => $request->input('sac', []),
                'mbess' => $request->input('mbess', []),
                'medical_decision' => $request->input('medical_decision'),
                'follow_up_plan' => $request->input('follow_up_plan', ''),
            ];

            // Calculate symptom scores
            $symptomCount = count(array_filter($scatData['symptoms'], function($score) {
                return $score > 0;
            }));
            $symptomSeverity = array_sum($scatData['symptoms']);

            // Calculate SAC total score
            $sacTotal = array_sum($scatData['sac']);

            // Calculate mBESS total errors
            $mbessTotal = array_sum($scatData['mbess']);

            // Create comprehensive SCAT analysis prompt
            $analysisPrompt = $this->getScatAnalysisPrompt($scatData, $symptomCount, $symptomSeverity, $sacTotal, $mbessTotal);

            // Call Med-Gemini AI with SCAT data
            $result = $this->callMedGeminiAI('scat_analysis', null, $analysisPrompt);

            return response()->json([
                'success' => true,
                'analysis' => $result['analysis'] ?? $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false,
                'message' => 'Données fournies invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PCMA AI service unavailable');
            
            return response()->json([
                'success' => false,
                'message' => __('pcma_workflow.service_unavailable')
            ], 503);
        }
    }

    public function aiAnalyzeComplete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ecg_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
                'mri_files' => 'nullable|array',
                'mri_files.*' => 'file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
                'ct_files' => 'nullable|array',
                'ct_files.*' => 'file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
                'xray_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            ]);

            // Ne jamais conclure à une aptitude en l'absence de données médicales.
            if (!$request->hasFile('ecg_file') && !$request->hasFile('mri_files')
                && !$request->hasFile('ct_files') && !$request->hasFile('xray_file')) {
                return response()->json(['success' => false,
                    'message' => 'Aucune pièce médicale fournie pour l’analyse.'], 422);
            }
            $analyses = [];
            $tempFiles = [];

            if ($request->hasFile('ecg_file')) {
                $file = $request->file('ecg_file');
                $fileName = time() . '_ecg.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('temp_analysis', $fileName, 'local');
                $tempFiles[] = $path;

                $extension = strtolower($file->getClientOriginalExtension());
                $isDicom = $extension === 'dcm';
                $analysisType = $isDicom ? 'ecg_dicom' : 'ecg_image';

                $analyses['ecg'] = $this->callMedGeminiAI($analysisType, $path);
            }

            if ($request->hasFile('mri_files')) {
                $mriFiles = $request->file('mri_files');
                $mriAnalyses = [];

                foreach ($mriFiles as $index => $file) {
                    $fileName = time() . '_mri_' . $index . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('temp_analysis', $fileName, 'local');
                    $tempFiles[] = $path;

                    $extension = strtolower($file->getClientOriginalExtension());
                    $isDicom = $extension === 'dcm';
                    $analysisType = $isDicom ? 'mri_dicom_bone_age' : 'mri_image_bone_age';

                    $mriAnalyses[] = [
                        'file_name' => $file->getClientOriginalName(),
                        'analysis' => $this->callMedGeminiAI($analysisType, $path)
                    ];
                }

                if (!empty($mriAnalyses)) {
                    $analyses['mri'] = $this->combineMultipleAnalyses($mriAnalyses, 'MRI');
                }
            }

            if ($request->hasFile('ct_files')) {
                $ctFiles = $request->file('ct_files');
                $ctAnalyses = [];

                foreach ($ctFiles as $index => $file) {
                    $fileName = time() . '_ct_' . $index . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('temp_analysis', $fileName, 'local');
                    $tempFiles[] = $path;

                    $extension = strtolower($file->getClientOriginalExtension());
                    $isDicom = $extension === 'dcm';
                    $analysisType = $isDicom ? 'xray_dicom' : 'xray_image'; // Use X-ray analysis for CT

                    $ctAnalyses[] = [
                        'file_name' => $file->getClientOriginalName(),
                        'analysis' => $this->callMedGeminiAI($analysisType, $path)
                    ];
                }

                if (!empty($ctAnalyses)) {
                    $analyses['ct'] = $this->combineMultipleAnalyses($ctAnalyses, 'CT');
                }
            }

            if ($request->hasFile('xray_file')) {
                $file = $request->file('xray_file');
                $fileName = time() . '_xray.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('temp_analysis', $fileName, 'local');
                $tempFiles[] = $path;

                $extension = strtolower($file->getClientOriginalExtension());
                $isDicom = $extension === 'dcm';
                $analysisType = $isDicom ? 'xray_dicom' : 'xray_image';

                $analyses['xray'] = $this->callMedGeminiAI($analysisType, $path);
            }

            // Clean up all temporary files
            foreach ($tempFiles as $tempFile) {
                Storage::disk('local')->delete($tempFile);
            }

            $overallAssessment = $this->generateOverallAssessment($analyses);

            return response()->json([
                'success' => true,
                'analysis' => array_merge($analyses, ['overall_assessment' => $overallAssessment])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false,
                'message' => 'Données fournies invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PCMA AI service unavailable');
            
            return response()->json([
                'success' => false,
                'message' => __('pcma_workflow.service_unavailable')
            ], 503);
        }
    }

    public function aiAnalyzeCt(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ct_file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
            ]);

            $file = $request->file('ct_file');
            $fileName = time() . '_ct.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('temp_analysis', $fileName, 'local');

            $extension = strtolower($file->getClientOriginalExtension());
            $isDicom = $extension === 'dcm';
            $analysisType = $isDicom ? 'ct_dicom' : 'ct_image';

            $result = $this->callMedGeminiAI($analysisType, $path);

            // Clean up temporary file
            Storage::disk('local')->delete($path);

            return response()->json([
                'success' => true,
                'analysis' => $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false,
                'message' => 'Données fournies invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PCMA AI service unavailable');

            return response()->json([
                'success' => false,
                'message' => 'Service d’analyse IA indisponible pour le scanner.',
            ], 503);
        }
    }

    public function aiAnalyzeUltrasound(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ultrasound_file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm,bmp,tiff,tif|max:10240',
            ]);

            $file = $request->file('ultrasound_file');
            $fileName = time() . '_ultrasound.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('temp_analysis', $fileName, 'local');

            $extension = strtolower($file->getClientOriginalExtension());
            $isDicom = $extension === 'dcm';
            $analysisType = $isDicom ? 'ultrasound_dicom' : 'ultrasound_image';

            $result = $this->callMedGeminiAI($analysisType, $path);

            // Clean up temporary file
            Storage::disk('local')->delete($path);

            return response()->json([
                'success' => true,
                'analysis' => $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false,
                'message' => 'Données fournies invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PCMA AI service unavailable');

            return response()->json([
                'success' => false,
                'message' => 'Service d’analyse IA indisponible pour l’échographie.',
            ], 503);
        }
    }

    public function aiFitnessAssessment(Request $request): JsonResponse
    {
        try {
            // Collect all form data for comprehensive analysis
            $formData = $request->all();
            $aiAnalysisResults = $request->input('ai_analysis_results');
            $clinicalKeys = ['blood_pressure', 'heart_rate', 'temperature', 'respiratory_rate',
                'oxygen_saturation', 'weight', 'medical_history', 'cardiovascular_history',
                'surgical_history', 'medications', 'allergies', 'clinical_notes'];
            $hasData = collect($clinicalKeys)->contains(fn ($key) =>
                array_key_exists($key, $formData) && $formData[$key] !== null && $formData[$key] !== '');
            if (!$hasData) return response()->json(['success' => false,
                'message' => 'Aucune donnée clinique fournie.'], 422);

            
            // Create a comprehensive prompt for fitness assessment
            $fitnessPrompt = $this->getFitnessAssessmentPrompt($formData, $aiAnalysisResults);
            
            // Call the AI service for fitness assessment
            $result = $this->callMedGeminiAI('fitness_assessment', null, $fitnessPrompt);

            // Check if the AI service returned a successful result
            if (isset($result['success']) && $result['success'] && isset($result['analysis'])) {
                return response()->json([
                    'success' => true,
                    'assessment' => $result['analysis']
                ]);
            } else {
                Log::warning('AI fitness assessment unavailable');

                return response()->json([
                    'success' => false,
                    'message' => 'Service d’évaluation IA indisponible.',
                ], 503);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false,
                'message' => 'Données fournies invalides.',
                'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('PCMA AI service unavailable');

            return response()->json([
                'success' => false,
                'message' => 'Service d’évaluation IA indisponible.',
            ], 503);
        }
    }

    private function callMedGeminiAI(string $analysisType, ?string $filePath = null, ?string $customPrompt = null): array
    {
        try {
        $payload = ['analysis_type' => $analysisType,
            'prompt' => $customPrompt ?? $this->getAnalysisPrompt($analysisType)];
        if (!in_array($analysisType, ['scat_analysis', 'fitness_assessment'], true)) {
            if (!$filePath) throw new \RuntimeException('Medical file required');
            $payload['file_content'] = base64_encode(Storage::disk('local')->get($filePath));
            $payload['file_type'] = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        }
        $client = Http::timeout(config('services.ai.timeout', 30));
        if (config('services.ai.api_key')) $client = $client->withToken(config('services.ai.api_key'));
        $response = $client->post(rtrim(config('services.ai.base_url'), '/').'/api/v1/med-gemini/analyze', $payload);
        if ($response->status() === 422) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'analysis' => 'Données manquantes ou format non pris en charge par le service IA.']);
        }
        if (!$response->successful()) throw new \RuntimeException('AI service unavailable');
        return app(\App\Services\MedicalAiResult::class)->normalize($response->json() ?? []);
        } finally {
            if ($filePath) Storage::disk('local')->delete($filePath);
        }
    }

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
            
            case 'xray_dicom':
                return "Analyze this DICOM X-ray file for bone and joint assessment. Examine bone structure, joint alignment, fractures, dislocations, arthritis, and any pathological findings. Provide detailed analysis of bone density, alignment, and any abnormalities. This is a DICOM medical image file with enhanced metadata. Format the response as JSON with fields: bone_structure, joint_alignment, fractures, dislocations, arthritis, bone_density, abnormalities, recommendations, dicom_metadata.";
            
            case 'xray_image':
                return "Analyze this X-ray image (non-DICOM format) for bone and joint assessment. Examine bone structure, joint alignment, fractures, dislocations, arthritis, and any pathological findings. Provide detailed analysis of bone density, alignment, and any abnormalities. Format the response as JSON with fields: bone_structure, joint_alignment, fractures, dislocations, arthritis, bone_density, abnormalities, recommendations.";
            
            case 'ecg_effort_dicom':
                return "Analyze this DICOM ECG stress test file and provide detailed medical interpretation including: exercise capacity, heart rate response, ST segment changes during exercise, arrhythmias, blood pressure response, and clinical recommendations for cardiac fitness. This is a DICOM medical image file with enhanced metadata. Format the response as JSON with fields: exercise_capacity, heart_rate_response, st_changes, arrhythmias, blood_pressure, clinical_recommendations, dicom_metadata.";
            
            case 'ecg_effort_image':
                return "Analyze this ECG stress test image (non-DICOM format) and provide detailed medical interpretation including: exercise capacity, heart rate response, ST segment changes during exercise, arrhythmias, blood pressure response, and clinical recommendations for cardiac fitness. Format the response as JSON with fields: exercise_capacity, heart_rate_response, st_changes, arrhythmias, blood_pressure, clinical_recommendations.";
            
            case 'scintigraphy_dicom':
                return "Analyze this DICOM nuclear medicine scintigraphy file and provide detailed medical interpretation including: tracer distribution, organ function, pathological findings, comparison with normal patterns, and clinical recommendations. This is a DICOM medical image file with enhanced metadata. Format the response as JSON with fields: tracer_distribution, organ_function, pathological_findings, normal_comparison, clinical_recommendations, dicom_metadata.";
            
            case 'scintigraphy_image':
                return "Analyze this nuclear medicine scintigraphy image (non-DICOM format) and provide detailed medical interpretation including: tracer distribution, organ function, pathological findings, comparison with normal patterns, and clinical recommendations. Format the response as JSON with fields: tracer_distribution, organ_function, pathological_findings, normal_comparison, clinical_recommendations.";
            
            case 'ct_dicom':
                return "Analyze this DICOM CT scan file and provide detailed medical interpretation including: anatomical region, tissue density, lesions, masses or tumors, hemorrhages, calcifications, vascular abnormalities, and clinical recommendations. This is a DICOM medical image file with enhanced metadata. Format the response as JSON with fields: anatomical_region, tissue_density, lesions, masses, hemorrhages, calcifications, vascular_abnormalities, recommendations, dicom_metadata.";
            
            case 'ct_image':
                return "Analyze this CT scan image (non-DICOM format) and provide detailed medical interpretation including: anatomical region, tissue density, lesions, masses or tumors, hemorrhages, calcifications, vascular abnormalities, and clinical recommendations. Format the response as JSON with fields: anatomical_region, tissue_density, lesions, masses, hemorrhages, calcifications, vascular_abnormalities, recommendations.";
            
            case 'ultrasound_dicom':
                return "Analyze this DICOM ultrasound file and provide detailed medical interpretation including: organ examined, echogenicity, tissue structure, cysts, masses, effusions, vascular abnormalities, and clinical recommendations. This is a DICOM medical image file with enhanced metadata. Format the response as JSON with fields: organ_examined, echogenicity, tissue_structure, cysts, masses, effusions, vascular_abnormalities, recommendations, dicom_metadata.";
            
            case 'ultrasound_image':
                return "Analyze this ultrasound image (non-DICOM format) and provide detailed medical interpretation including: organ examined, echogenicity, tissue structure, cysts, masses, effusions, vascular abnormalities, and clinical recommendations. Format the response as JSON with fields: organ_examined, echogenicity, tissue_structure, cysts, masses, effusions, vascular_abnormalities, recommendations.";
            
            case 'fitness_assessment':
                return "Analyze the complete medical dataset for professional football fitness assessment. Consider all medical examinations, AI analysis results, and FIFA compliance requirements. Provide a comprehensive evaluation with detailed scores, FIFA compliance assessment, and professional recommendations.";
            
            default:
                return "Analyze this medical image and provide a comprehensive medical assessment.";
        }
    }

    private function getFitnessAssessmentPrompt(array $formData, ?string $aiAnalysisResults): string
    {
        $prompt = "Analyze the complete medical dataset for professional football fitness assessment. ";
        $prompt .= "Consider all medical examinations, AI analysis results, and FIFA compliance requirements. ";
        $prompt .= "Provide a comprehensive evaluation with the following structure:\n\n";
        
        $prompt .= "FORM DATA SUMMARY:\n";
        $prompt .= "- Athlete Information: " . ($formData['player_id'] ?? 'Not specified') . "\n";
        $prompt .= "- Assessment Type: " . ($formData['type'] ?? 'Not specified') . "\n";
        $prompt .= "- Assessment Date: " . ($formData['assessment_date'] ?? 'Not specified') . "\n";
        $prompt .= "- Assessor: " . ($formData['assessor_id'] ?? 'Not specified') . "\n\n";
        
        if ($aiAnalysisResults) {
            $prompt .= "AI ANALYSIS RESULTS:\n";
            $prompt .= $aiAnalysisResults . "\n\n";
        }
        
        $prompt .= "MEDICAL DATA:\n";
        $prompt .= "- Vital Signs: " . json_encode(array_filter([
            'blood_pressure' => $formData['blood_pressure'] ?? null,
            'heart_rate' => $formData['heart_rate'] ?? null,
            'temperature' => $formData['temperature'] ?? null,
            'respiratory_rate' => $formData['respiratory_rate'] ?? null,
            'oxygen_saturation' => $formData['oxygen_saturation'] ?? null,
            'weight' => $formData['weight'] ?? null
        ])) . "\n";
        
        $prompt .= "- Medical History: " . json_encode(array_filter([
            'cardiovascular_history' => $formData['cardiovascular_history'] ?? null,
            'surgical_history' => $formData['surgical_history'] ?? null,
            'current_medications' => $formData['current_medications'] ?? null,
            'allergies' => $formData['allergies'] ?? null
        ])) . "\n";
        
        $prompt .= "- Physical Examination: " . json_encode(array_filter([
            'general_appearance' => $formData['general_appearance'] ?? null,
            'skin_examination' => $formData['skin_examination'] ?? null,
            'lymph_nodes' => $formData['lymph_nodes'] ?? null,
            'abdominal_examination' => $formData['abdominal_examination'] ?? null
        ])) . "\n";
        
        $prompt .= "- Imaging Results: " . json_encode(array_filter([
            'ecg_interpretation' => $formData['ecg_interpretation'] ?? null,
            'ecg_notes' => $formData['ecg_notes'] ?? null,
            'mri_type' => $formData['mri_type'] ?? null,
            'mri_findings' => $formData['mri_findings'] ?? null,
            'mri_notes' => $formData['mri_notes'] ?? null,
            'xray_notes' => $formData['xray_notes'] ?? null,
            'ct_notes' => $formData['ct_notes'] ?? null,
            'ultrasound_notes' => $formData['ultrasound_notes'] ?? null
        ])) . "\n\n";
        
        $prompt .= "EVALUATION CRITERIA:\n";
        $prompt .= "1. Cardiovascular Health (0-10): Assess heart function, ECG results, stress test performance\n";
        $prompt .= "2. Musculoskeletal Health (0-10): Evaluate bone structure, joint alignment, injury history\n";
        $prompt .= "3. Neurological Health (0-10): Assess brain function, cognitive abilities, concussion history\n";
        $prompt .= "4. General Fitness (0-10): Overall physical condition, endurance, strength\n";
        $prompt .= "5. FIFA Compliance: Age verification, bone age assessment, injury risk\n\n";
        
        $prompt .= "REQUIRED OUTPUT FORMAT (JSON):\n";
        $prompt .= "{\n";
        $prompt .= "  \"overall_decision\": \"FIT|NOT_FIT|CONDITIONAL\",\n";
        $prompt .= "  \"cardiovascular_score\": \"number 0-10\",\n";
        $prompt .= "  \"musculoskeletal_score\": \"number 0-10\",\n";
        $prompt .= "  \"neurological_score\": \"number 0-10\",\n";
        $prompt .= "  \"general_fitness_score\": \"number 0-10\",\n";
        $prompt .= "  \"fifa_compliance\": \"boolean\",\n";
        $prompt .= "  \"bone_age_assessment\": \"string\",\n";
        $prompt .= "  \"injury_risk\": \"LOW|MODERATE|HIGH\",\n";
        $prompt .= "  \"executive_summary\": \"comprehensive summary\",\n";
        $prompt .= "  \"detailed_analysis\": [\n";
        $prompt .= "    {\"category\": \"Cardiovascular\", \"findings\": \"detailed findings\", \"recommendations\": \"specific recommendations\"},\n";
        $prompt .= "    {\"category\": \"Musculoskeletal\", \"findings\": \"detailed findings\", \"recommendations\": \"specific recommendations\"},\n";
        $prompt .= "    {\"category\": \"Neurological\", \"findings\": \"detailed findings\", \"recommendations\": \"specific recommendations\"}\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"justifications\": [\n";
        $prompt .= "    {\"reason\": \"specific reason\", \"explanation\": \"detailed explanation\"}\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"follow_up_recommendations\": [\n";
        $prompt .= "    {\"type\": \"Medical Follow-up\", \"description\": \"specific recommendation\", \"timeline\": \"timeframe\"}\n";
        $prompt .= "  ]\n";
        $prompt .= "}\n\n";
        
        $prompt .= "IMPORTANT: If the decision is NOT_FIT, provide comprehensive justifications explaining why the player is not suitable for professional football. ";
        $prompt .= "Include specific medical reasons, risks, and potential complications. ";
        $prompt .= "If the decision is CONDITIONAL, specify what conditions must be met for clearance. ";
        $prompt .= "If the decision is FIT, provide recommendations for maintaining fitness and preventing injuries.";
        
        return $prompt;
    }

    private function getScatAnalysisPrompt(array $scatData, int $symptomCount, int $symptomSeverity, int $sacTotal, int $mbessTotal): string
    {
        $redFlagsText = !empty($scatData['red_flags']) ? 'Red Flags: ' . implode(', ', $scatData['red_flags']) : 'No red flags detected';
        $observableSignsText = !empty($scatData['observable_signs']) ? 'Observable Signs: ' . implode(', ', $scatData['observable_signs']) : 'No observable signs detected';
        
        return "Analyze this SCAT (Sport Concussion Assessment Tool) data and provide comprehensive medical interpretation:

EVALUATION CONTEXT:
- Date: {$scatData['evaluation_date']}
- Context: {$scatData['context']}
- Evaluator: {$scatData['evaluator_name']}

RED FLAGS & OBSERVABLE SIGNS:
- {$redFlagsText}
- {$observableSignsText}

SYMPTOM ASSESSMENT:
- Total symptoms: {$symptomCount}/22
- Total severity score: {$symptomSeverity}
- Individual symptoms: " . json_encode($scatData['symptoms']) . "

SAC (Standardized Assessment of Concussion) SCORES:
- Orientation: {$scatData['sac']['orientation']}/5
- Immediate Memory: {$scatData['sac']['immediate_memory']}/15
- Concentration: {$scatData['sac']['concentration']}/5
- Delayed Recall: {$scatData['sac']['delayed_recall']}/5
- SAC Total: {$sacTotal}/30

mBESS (Modified Balance Error Scoring System):
- Firm Surface Errors: {$scatData['mbess']['firm_surface_errors']}
- Foam Surface Errors: {$scatData['mbess']['foam_surface_errors']}
- Total Errors: {$mbessTotal}

MEDICAL DECISION:
- {$scatData['medical_decision']}

Provide a comprehensive analysis including:
1. Concussion risk assessment
2. Severity classification
3. Return-to-play recommendations
4. Follow-up requirements
5. Clinical recommendations

Format the response as JSON with fields: concussion_risk, severity_classification, return_to_play_recommendations, follow_up_requirements, clinical_recommendations, overall_assessment.";
    }

    private function generateOverallAssessment(array $analyses): array
    {
        $available = $analyses !== [];
        foreach ($analyses as $analysis) {
            try { app(\App\Services\MedicalAiResult::class)->normalize($analysis); }
            catch (\Throwable $e) { $available = false; }
        }
        return ['medical_status' => $available ? 'Analyse à valider' : 'Données insuffisantes',
            'sports_eligibility' => 'Pending medical clearance',
            'recommendations' => 'Une évaluation et une conclusion du médecin sont nécessaires.'];
    }

    private function combineMultipleAnalyses(array $analyses, string $type): array
    {
        $combinedAnalysis = [
            'type' => $type,
            'total_files' => count($analyses),
            'individual_analyses' => $analyses,
            'summary' => '',
            'overall_findings' => [],
            'recommendations' => []
        ];

        $allFindings = [];
        $allRecommendations = [];

        foreach ($analyses as $analysis) {
            $analysisData = $analysis['analysis'];
            
            // Extract findings
            if (isset($analysisData['abnormalities']) && $analysisData['abnormalities'] !== 'Aucune') {
                $allFindings[] = $analysis['file_name'] . ': ' . $analysisData['abnormalities'];
            }

            // Extract recommendations
            if (isset($analysisData['recommendations'])) {
                $allRecommendations[] = $analysis['file_name'] . ': ' . $analysisData['recommendations'];
            }
        }

        // Generate combined summary
        if (!empty($allFindings)) {
            $combinedAnalysis['summary'] = 'Analyse de ' . count($analyses) . ' fichier(s) ' . $type . ' - Anomalies détectées dans certains fichiers.';
            $combinedAnalysis['overall_findings'] = $allFindings;
        } else {
            $combinedAnalysis['summary'] = 'Analyse de ' . count($analyses) . ' fichier(s) ' . $type . ' - Aucune anomalie détectée.';
        }

        $combinedAnalysis['recommendations'] = $allRecommendations;

        return $combinedAnalysis;
    }
}
