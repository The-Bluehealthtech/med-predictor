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
        $pcmas = PCMA::with(['athlete', 'assessor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pcma.index', compact('pcmas'));
    }

    public function create(): View
    {
        // Vérifier les autorisations
        $user = auth()->user();
        
        abort_unless($user, 401);
        
        // Récupérer les joueurs selon le rôle de l'utilisateur
        $athletes = collect();
        
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $athletes = Player::where('club_id', $user->club_id)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $athletes = Player::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        } elseif (in_array($user->role, ['admin', 'super_admin'])) {
            $athletes = Player::orderBy('first_name')->orderBy('last_name')->get();
        }
        
        // Seul le médecin connecté, identifié par FIFA et inscrit TeamDoctor,
        // peut signer. Les autres utilisateurs ne sont pas proposés comme signataires.
        $teamDoctorRegistration = $this->activeTeamDoctorRegistration($user);
        $users = $teamDoctorRegistration ? collect([$user]) : collect();
        
        return view('pcma.create', compact('athletes', 'users', 'teamDoctorRegistration'));
    }

    public function store(Request $request)
    {
        // Debug: Log all incoming data
        Log::info('PCMA Store - Incoming request data', [
            'all_data' => $request->all(),
            'has_signature_data' => $request->has('signature_data'),
            'has_is_signed' => $request->has('is_signed'),
            'athlete_id' => $request->get('athlete_id'),
            'type' => $request->get('type'),
            'assessor_id' => $request->get('assessor_id'),
            'assessment_date' => $request->get('assessment_date'),
            'status' => $request->get('status'),
        ]);
        
        try {
            $validated = $request->validate([
                'player_id' => 'required|exists:players,id',
                'type' => 'required|in:bpma,cardio,dental,neurological,orthopedic',
                'assessor_id' => 'required|exists:users,id',
                'assessment_date' => 'required|date',
                'result_json' => 'nullable|json',
                'status' => 'required|in:pending,completed,failed',
                'notes' => 'nullable|string',
                'final_statement' => 'required|array',
                'final_statement.overall_decision' => 'required|in:FIT,NOT_FIT,CONDITIONAL',
                // FIFA Compliance Fields
                'fifa_connect_id' => [
                    'nullable',
                    new FifaIdentifier(),
                ],
                'fifa_id' => [
                    'nullable',
                    new FifaIdentifier(),
                ],
                'competition_name' => 'nullable|string|max:255',
                'competition_date' => 'nullable|date',
                'team_name' => 'nullable|string|max:255',
                'position' => 'nullable|in:goalkeeper,defender,midfielder,forward',
                'fifa_compliant' => 'nullable|boolean',
                // Vital Signs
                'blood_pressure' => 'nullable|string|max:255',
                'heart_rate' => 'nullable|integer|min:0|max:300',
                'temperature' => 'nullable|numeric|min:30|max:45',
                'respiratory_rate' => 'nullable|integer|min:0|max:100',
                'oxygen_saturation' => 'nullable|integer|min:0|max:100',
                'weight' => 'nullable|numeric|min:0|max:500',
                // Medical History
                'medical_history' => 'nullable|string',
                'surgical_history' => 'nullable|string',
                'medications' => 'nullable|string',
                'allergies' => 'nullable|string',
                // Physical Examination
                'general_appearance' => 'nullable|in:normal,abnormal',
                'skin_examination' => 'nullable|in:normal,abnormal',
                'lymph_nodes' => 'nullable|in:normal,enlarged',
                'abdomen_examination' => 'nullable|in:normal,abnormal',
                // Cardiovascular Assessment
                'cardiac_rhythm' => 'nullable|in:sinus,irregular,arrhythmia',
                'heart_murmur' => 'nullable|in:none,systolic,diastolic',
                'blood_pressure_rest' => 'nullable|string|max:255',
                'blood_pressure_exercise' => 'nullable|string|max:255',
                // Neurological Assessment
                'consciousness' => 'nullable|in:alert,confused,drowsy',
                'cranial_nerves' => 'nullable|in:normal,abnormal',
                'motor_function' => 'nullable|in:normal,weakness,paralysis',
                'sensory_function' => 'nullable|in:normal,decreased,absent',
                // Musculoskeletal Assessment
                'joint_mobility' => 'nullable|in:normal,limited,restricted',
                'muscle_strength' => 'nullable|in:normal,reduced,weak',
                'pain_assessment' => 'nullable|in:none,mild,moderate,severe',
                'range_of_motion' => 'nullable|in:full,limited,restricted',
                // Medical Imaging
                'ecg_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ecg_date' => 'nullable|date',
                'ecg_interpretation' => 'nullable|in:normal,sinus_bradycardia,sinus_tachycardia,atrial_fibrillation,ventricular_tachycardia,st_elevation,st_depression,qt_prolongation,abnormal',
                'ecg_notes' => 'nullable|string',
                'mri_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'mri_date' => 'nullable|date',
                'mri_type' => 'nullable|in:brain,spine,knee,shoulder,ankle,hip,cardiac,other',
                'mri_findings' => 'nullable|in:normal,mild_abnormality,moderate_abnormality,severe_abnormality,fracture,tumor,inflammation,degenerative,other',
                'mri_notes' => 'nullable|string',
                'xray_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ct_scan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                'ultrasound_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
                // Signature fields
                'is_signed' => 'nullable|boolean',
                'signed_at' => 'nullable|date',
                'signed_by' => 'nullable|string|max:255',
                'license_number' => 'nullable|string|max:255',
                'signature_image' => 'nullable|string',
                'signature_data' => 'nullable|json',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('PCMA Store - Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
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

        // La sélection client ne constitue pas une preuve d'identité du signataire.

        // La vérification du médecin ne certifie pas le PCMA comme objet FIFA Connect.
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
                    $validated['signature_data'] = json_encode($signaturePayload);
                    
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
                if (strpos($signatureData, 'data:image') === 0) {
                    // Extract base64 data
                    $imageData = base64_decode(explode(',', $signatureData)[1]);
                    $filename = 'signature_' . time() . '.png';
                    $path = 'signatures/' . $filename;
                    
                    // Store the signature image
                    Storage::disk('public')->put($path, $imageData);
                    $validated['signature_image'] = $path;
                }
            }
        }
        
        // Handle file uploads
        $fileFields = ['ecg_file', 'mri_file', 'xray_file', 'ct_scan_file', 'ultrasound_file'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('medical_imaging', $filename, 'public');
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
                
                $pcma = PCMA::create($validated);

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
        $pcma->load(['athlete', 'assessor']);
        
        return view('pcma.show', compact('pcma'));
    }

    public function edit(PCMA $pcma): View
    {
        $athletes = Athlete::orderBy('name')->get();
        $players = Player::orderBy('name')->get();
        
        return view('pcma.edit', compact('pcma', 'athletes', 'players'));
    }

    public function update(Request $request, PCMA $pcma): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'type' => 'required|in:cardio,neurological,musculoskeletal,general',
            'assessor_id' => 'required|exists:users,id',
            'assessment_date' => 'required|date',
            'result_json' => 'nullable|json',
            'status' => 'required|in:pending,completed,failed',
            'notes' => 'nullable|string',
            // FIFA Compliance Fields
            'fifa_connect_id' => [
                'nullable',
                new FifaIdentifier(),
            ],
            'fifa_id' => [
                'nullable',
                new FifaIdentifier(),
            ],
            'competition_name' => 'nullable|string|max:255',
            'competition_date' => 'nullable|date',
            'team_name' => 'nullable|string|max:255',
            'position' => 'nullable|in:goalkeeper,defender,midfielder,forward',
            'fifa_compliant' => 'nullable|boolean',
            // Vital Signs
            'blood_pressure' => 'nullable|string|max:255',
            'heart_rate' => 'nullable|integer|min:0|max:300',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'respiratory_rate' => 'nullable|integer|min:0|max:100',
            'oxygen_saturation' => 'nullable|integer|min:0|max:100',
            'weight' => 'nullable|numeric|min:0|max:500',
            // Medical History
            'medical_history' => 'nullable|string',
            'surgical_history' => 'nullable|string',
            'medications' => 'nullable|string',
            'allergies' => 'nullable|string',
            // Physical Examination
            'general_appearance' => 'nullable|in:normal,abnormal',
            'skin_examination' => 'nullable|in:normal,abnormal',
            'lymph_nodes' => 'nullable|in:normal,enlarged',
            'abdomen_examination' => 'nullable|in:normal,abnormal',
            // Cardiovascular Assessment
            'cardiac_rhythm' => 'nullable|in:sinus,irregular,arrhythmia',
            'heart_murmur' => 'nullable|in:none,systolic,diastolic',
            'blood_pressure_rest' => 'nullable|string|max:255',
            'blood_pressure_exercise' => 'nullable|string|max:255',
            // Neurological Assessment
            'consciousness' => 'nullable|in:alert,confused,drowsy',
            'cranial_nerves' => 'nullable|in:normal,abnormal',
            'motor_function' => 'nullable|in:normal,weakness,paralysis',
            'sensory_function' => 'nullable|in:normal,decreased,absent',
            // Musculoskeletal Assessment
            'joint_mobility' => 'nullable|in:normal,limited,restricted',
            'muscle_strength' => 'nullable|in:normal,reduced,weak',
            'pain_assessment' => 'nullable|in:none,mild,moderate,severe',
            'range_of_motion' => 'nullable|in:full,limited,restricted',
            // Medical Imaging
            'ecg_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            'ecg_date' => 'nullable|date',
            'ecg_interpretation' => 'nullable|in:normal,sinus_bradycardia,sinus_tachycardia,atrial_fibrillation,ventricular_tachycardia,st_elevation,st_depression,qt_prolongation,abnormal',
            'ecg_notes' => 'nullable|string',
            'mri_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            'mri_date' => 'nullable|date',
            'mri_type' => 'nullable|in:brain,spine,knee,shoulder,ankle,hip,cardiac,other',
            'mri_findings' => 'nullable|in:normal,mild_abnormality,moderate_abnormality,severe_abnormality,fracture,tumor,inflammation,degenerative,other',
            'mri_notes' => 'nullable|string',
            'xray_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            'ct_scan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            'ultrasound_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
        ]);

        $validated = $this->normalizeFifaIdentifierInput(
            $validated
        );

        // Handle FIFA compliant checkbox
        $validated['fifa_compliant'] = $request->has('fifa_compliant');
        
        // Handle file uploads
        $fileFields = ['ecg_file', 'mri_file', 'xray_file', 'ct_scan_file', 'ultrasound_file'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('medical_imaging', $filename, 'public');
                $validated[$field] = $path;
            }
        }

        $pcma->update($validated);

        return redirect()->route('pcma.show', $pcma)
            ->with('success', 'PCMA mis à jour avec succès.');
    }

    public function destroy(PCMA $pcma): RedirectResponse
    {
        $pcma->delete();

        return redirect()->route('pcma.index')
            ->with('success', 'PCMA supprimé avec succès.');
    }

    public function complete(PCMA $pcma): RedirectResponse
    {
        $pcma->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('pcma.show', $pcma)
            ->with('success', 'PCMA marqué comme complété.');
    }

    public function fail(PCMA $pcma): RedirectResponse
    {
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
            'total_pcmas' => PCMA::count(),
            'pending_pcmas' => PCMA::where('status', 'pending')->count(),
            'completed_pcmas' => PCMA::where('status', 'completed')->count(),
            'failed_pcmas' => PCMA::where('status', 'failed')->count(),
        ];

        $recentPcmas = PCMA::with(['athlete', 'assessor'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('pcma.dashboard', compact('stats', 'recentPcmas'));
    }

    public function exportPdf(PCMA $pcma)
    {
        $pcma->load(['athlete', 'assessor']);
        
        // Préparer les données pour la vue PDF
        $formData = [
            'type' => $pcma->type ?? 'standard',
            'assessment_date' => $pcma->assessment_date ?? now()->format('Y-m-d'),
            'assessment_id' => $pcma->id,
            'blood_pressure' => $pcma->blood_pressure ?? 'Non mesuré',
            'heart_rate' => $pcma->heart_rate ?? 'Non mesuré',
            'temperature' => $pcma->temperature ?? 'Non mesuré',
            'oxygen_saturation' => $pcma->oxygen_saturation ?? 'Non mesuré',
            'respiratory_rate' => $pcma->respiratory_rate ?? 'Non mesuré',
            'weight' => $pcma->weight ?? 'Non mesuré',
            'cardiovascular_history' => $pcma->cardiovascular_history ?? 'Aucun',
            'surgical_history' => $pcma->surgical_history ?? 'Aucun',
            'medications' => $pcma->current_medications ?? 'Aucun',
            'allergies' => $pcma->allergies ?? 'Aucune',
            'general_appearance' => $pcma->general_appearance ?? 'Non évalué',
            'skin_examination' => $pcma->skin_examination ?? 'Non évalué',
            'cardiac_rhythm' => $pcma->cardiac_rhythm ?? 'Non évalué',
            'heart_murmur' => $pcma->heart_murmur ?? 'Non évalué',
            'fifa_connect_id' => $pcma->fifa_id ?? 'Non spécifié',
            'status' => $pcma->status ?? 'pending'
        ];
        
        $athlete = $pcma->athlete;
        $generatedAt = now();
        
        $pdf = Pdf::loadView('pcma.pdf', compact('pcma', 'formData', 'athlete', 'generatedAt'));
        
        $athleteName = $pcma->athlete->name ?? 'unknown';
        return $pdf->download("PCMA-{$pcma->id}-{$athleteName}.pdf");
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

        } catch (\Exception $e) {
            Log::error('ECG Analysis Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse ECG: ' . $e->getMessage()
            ], 500);
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

        } catch (\Exception $e) {
            Log::error('MRI Analysis Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse IRM: ' . $e->getMessage()
            ], 500);
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

        } catch (\Exception $e) {
            Log::error('X-Ray Analysis Error: ' . $e->getMessage());
            
            // Clean up temporary file if it exists
            if (isset($path) && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse radiographie: ' . $e->getMessage()
            ], 500);
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

        } catch (\Exception $e) {
            Log::error('ECG Effort Analysis Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse ECG d\'Effort: ' . $e->getMessage()
            ], 500);
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

        } catch (\Exception $e) {
            Log::error('Scintigraphy Analysis Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse Scintigraphie: ' . $e->getMessage()
            ], 500);
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

        } catch (\Exception $e) {
            Log::error('SCAT Analysis Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse SCAT: ' . $e->getMessage()
            ], 500);
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

        } catch (\Exception $e) {
            Log::error('Complete Analysis Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse complète: ' . $e->getMessage()
            ], 500);
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

        } catch (\Exception $e) {
            Log::error('CT Analysis Error: ' . $e->getMessage());

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

        } catch (\Exception $e) {
            Log::error('Ultrasound Analysis Error: ' . $e->getMessage());

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
            
            Log::info('Fitness Assessment Request', [
                'form_data_keys' => array_keys($formData),
                'has_ai_results' => !empty($aiAnalysisResults),
                'user_id' => auth()->id()
            ]);
            
            // Create a comprehensive prompt for fitness assessment
            $fitnessPrompt = $this->getFitnessAssessmentPrompt($formData, $aiAnalysisResults);
            
            Log::info('Fitness Assessment Prompt Created', [
                'prompt_length' => strlen($fitnessPrompt),
                'prompt_preview' => substr($fitnessPrompt, 0, 200) . '...'
            ]);
            
            // Call the AI service for fitness assessment
            $result = $this->callMedGeminiAI('fitness_assessment', null, $fitnessPrompt);

            // Check if the AI service returned a successful result
            if (isset($result['success']) && $result['success'] && isset($result['analysis'])) {
                return response()->json([
                    'success' => true,
                    'assessment' => $result['analysis']
                ]);
            } else {
                Log::warning('AI fitness assessment unavailable', ['result' => $result]);

                return response()->json([
                    'success' => false,
                    'message' => 'Service d’évaluation IA indisponible.',
                ], 503);
            }

        } catch (\Exception $e) {
            Log::error('Fitness Assessment Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Service d’évaluation IA indisponible.',
            ], 503);
        }
    }

    private function callMedGeminiAI(string $analysisType, ?string $filePath = null, ?string $customPrompt = null): array
    {
        try {
            $aiServiceUrl = env('AI_SERVICE_URL', 'http://localhost:3001');
            
            if ($analysisType === 'scat_analysis') {
                // SCAT analysis doesn't require a file
                $prompt = $customPrompt ?? $this->getAnalysisPrompt($analysisType);
                
                $response = Http::timeout(30)->post($aiServiceUrl . '/api/v1/med-gemini/analyze', [
                    'analysis_type' => $analysisType,
                    'prompt' => $prompt
                ]);
            } elseif ($analysisType === 'fitness_assessment') {
                $prompt = $customPrompt ?? $this->getAnalysisPrompt($analysisType);

                $response = Http::timeout(30)->post($aiServiceUrl . '/api/v1/med-gemini/analyze', [
                    'analysis_type' => $analysisType,
                    'prompt' => $prompt,
                ]);
            } else {
                // File-based analysis
                if (!$filePath) {
                    throw new \Exception('File path is required for file-based analysis');
                }
                
                $fileContent = Storage::disk('local')->get($filePath);
                $base64Content = base64_encode($fileContent);

                $prompt = $customPrompt ?? $this->getAnalysisPrompt($analysisType);
            
                $response = Http::timeout(30)->post($aiServiceUrl . '/api/v1/med-gemini/analyze', [
                    'analysis_type' => $analysisType,
                    'file_content' => $base64Content,
                    'file_type' => pathinfo($filePath, PATHINFO_EXTENSION),
                    'prompt' => $prompt
                ]);
            }

            if ($response->successful()) {
                $result = $response->json();
                Log::info('AI service response successful', ['analysis_type' => $analysisType, 'result' => $result]);
                
                // Return the API response directly, including errors
                return $result;
            } else {
                Log::error('AI service HTTP error', [
                    'analysis_type' => $analysisType, 
                    'status' => $response->status(), 
                    'body' => $response->body(),
                    'url' => $aiServiceUrl . '/api/v1/med-gemini/analyze'
                ]);
                throw new \Exception('AI service HTTP error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('AI service error', ['analysis_type' => $analysisType, 'error' => $e->getMessage()]);
            throw $e; // Re-throw to let the calling method handle it
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
        $prompt .= "- Athlete Information: " . ($formData['athlete_id'] ?? 'Not specified') . "\n";
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
        $hasAbnormalities = false;
        $recommendations = [];

        if (isset($analyses['ecg'])) {
            if (strpos(strtolower($analyses['ecg']['abnormalities'] ?? ''), 'none') === false) {
                $hasAbnormalities = true;
                $recommendations[] = 'ECG abnormalities detected - cardiology consultation recommended';
            }
        }

        if (isset($analyses['mri'])) {
            if (strpos(strtolower($analyses['mri']['abnormalities'] ?? ''), 'none') === false) {
                $hasAbnormalities = true;
                $recommendations[] = 'MRI abnormalities detected - orthopedic consultation recommended';
            }
        }

        if (isset($analyses['xray'])) {
            if (strpos(strtolower($analyses['xray']['abnormalities'] ?? ''), 'none') === false) {
                $hasAbnormalities = true;
                $recommendations[] = 'X-ray abnormalities detected - orthopedic consultation recommended';
            }
        }

        return [
            'medical_status' => $hasAbnormalities ? 'Requires further evaluation' : 'Normal',
            'sports_eligibility' => $hasAbnormalities ? 'Pending medical clearance' : 'Cleared for sports',
            'recommendations' => empty($recommendations) ? 'All assessments within normal limits' : implode('; ', $recommendations)
        ];
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