<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\Player;
use App\Models\MedicalPrediction;
use App\Events\HealthRecordCreated;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;

class HealthRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request,$next){
            app(\App\Services\MedicalRecordAccess::class)->authorizeRole($request->user());
            return $next($request);
        });
    }
    private function authorizeRecord(HealthRecord $record): void
    {
        app(\App\Services\MedicalRecordAccess::class)->authorize(auth()->user(),$record->player,null);
    }
    private function baseRecordFor(HealthRecord $record): HealthRecord
    {
        return HealthRecord::where('player_id', $record->player_id)
            ->orderBy('record_date')
            ->orderBy('id')
            ->firstOrFail();
    }
    private function playersQuery()
    {
        return app(\App\Services\MedicalRecordAccess::class)->scopePlayers(auth()->user(),Player::query());
    }
    private function normalizeLists(Request $request): void
    {
        foreach((new HealthRecord)->getCasts() as $field=>$cast){
            if($cast!=='array' || !$request->has($field) || !is_string($request->input($field))) continue;
            $text=trim($request->input($field));
            $decoded=json_decode($text,true);
            $request->merge([$field=>$text===''?[]:(is_array($decoded)?$decoded:preg_split('/\r?\n/',$text))]);
        }
    }

    // Accepter les champs cliniques présents dans le schéma principal, selon leurs casts.
    // Les identifiants, scores calculés et chemins de fichiers restent protégés.
    private function extraRules(Request $request): array
    {
        $model=new HealthRecord; $casts=$model->getCasts();
        $columns=Schema::getColumnListing($model->getTable()); $rules=[];
        foreach($model->getFillable() as $field){
            if(!$request->has($field) || !in_array($field,$columns,true)
                || in_array($field,['icd11_diagnoses','user_id','player_id','visit_id','risk_score','prediction_confidence','bmi','status'],true)
                || str_ends_with($field,'_path'))continue;
            $type=$casts[$field]??'string';
            $rules[$field]=match($type){
                'array'=>'nullable|array', 'integer'=>'nullable|integer',
                'float'=>'nullable|numeric', 'boolean'=>'nullable|boolean',
                'datetime','date'=>'nullable|date', default=>'nullable|string|max:60000',
            };
        }
        return $rules;
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('modules.medical.index', request()->only('q'));
    }

    public function create(Request $request): View
    {
        $players = $this->playersQuery()->orderBy('name')->get();
        $visit = null;
        $selectedPlayer = null;
        $isDemo = false;
        
        // Si un player_id est fourni, récupérer le joueur de la base de données
        if ($request->has('player_id')) {
            $playerId = $request->player_id;
            $selectedPlayer = Player::with(['club'])->find($playerId);
        }
        
        if ($request->filled('player_id')) {
            abort_unless($selectedPlayer,404);
            app(\App\Services\MedicalRecordAccess::class)->authorize(auth()->user(),$selectedPlayer,null);
        }
        // Si un visit_id est fourni, récupérer les données de la visite
        if ($request->has('visit_id')) {
            $visit = \App\Models\Visit::with(['athlete.player', 'doctor', 'documents', 'appointment'])->find($request->visit_id);
            abort_unless($visit,404);
            app(\App\Services\MedicalRecordAccess::class)->authorize(auth()->user(),$visit->athlete?->player,$visit->athlete);
        }
        
        // Si un appointment_id est fourni, récupérer les données du rendez-vous
        $appointment = null;
        if ($request->has('appointment_id')) {
            $appointment = \App\Models\Appointment::with(['athlete'])->find($request->appointment_id);
            abort_unless($appointment,404);
            app(\App\Services\MedicalRecordAccess::class)->authorize(auth()->user(),$appointment->athlete?->player,$appointment->athlete);
        }
        
        // If no appointment is provided but an appointment_type comes from the query (e.g., clinician portal), use it as defaults
        if (!$appointment && $request->filled('appointment_type')) {
            $appointment = (object) [
                'type' => $request->get('appointment_type'),
                // Use today's date for visit_date default when not coming from an Appointment
                'appointment_date' => now(),
            ];
        }
        
        // Pré-remplir les valeurs par défaut avec les données du patient sélectionné et du rendez-vous
        $defaultValues = [
            'player_id' => $selectedPlayer ? $selectedPlayer->id : old('player_id'),
            'visit_date' => $appointment ? $appointment->appointment_date->format('Y-m-d') : old('visit_date', date('Y-m-d')),
            'doctor_name' => old('doctor_name', auth()->user()->name ?? ''),
            // Prefer explicit appointment_type from query when no appointment is loaded
            'visit_type' => $appointment
                ? (in_array($appointment->appointment_type, ['consultation','emergency','follow_up','pre_season','post_match','rehabilitation'], true)
                    ? $appointment->appointment_type
                    : 'consultation')
                : old('visit_type', $request->get('appointment_type')),
            // If no selected player, build a name from query params as a non-blocking display default
            'patient_name' => $selectedPlayer ? ($selectedPlayer->full_name ?? $selectedPlayer->name) : old('patient_name', trim(($request->get('first_name') ?? '').' '.($request->get('last_name') ?? '')) ?: null),
            'patient_birth_date' => $selectedPlayer ? $selectedPlayer->date_of_birth : old('patient_birth_date', $request->get('date_of_birth')),
            'patient_club' => $selectedPlayer && $selectedPlayer->club ? $selectedPlayer->club->name : old('patient_club'),
            'patient_position' => $selectedPlayer ? $selectedPlayer->position : old('patient_position'),
            'patient_nationality' => $selectedPlayer ? $selectedPlayer->nationality : old('patient_nationality'),
        ];
        
        $view = $request->boolean('advanced') ? 'health-records.create' : 'health-records.create-visit';

        return view($view, compact('players', 'visit', 'selectedPlayer', 'isDemo', 'defaultValues', 'appointment'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->input('workflow') === 'visit' && $request->filled('visit_date')) {
            $request->merge(['record_date' => $request->input('visit_date')]);
        }

        $this->normalizeLists($request);
        $sections = app(\App\Services\HealthRecordSections::class)->prepare($request);
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'visit_id' => 'nullable|exists:visits,id',
            'visit_date' => 'required|date',
            'doctor_name' => 'required|string|max:255',
            'visit_type' => 'required|string|in:consultation,emergency,follow_up,pre_season,post_match,rehabilitation',
            'blood_pressure_systolic' => 'nullable|integer|min:70|max:200',
            'blood_pressure_diastolic' => 'nullable|integer|min:40|max:130',
            'heart_rate' => 'nullable|integer|min:40|max:200',
            'temperature' => 'nullable|numeric|min:35|max:42',
            'weight' => 'nullable|numeric|min:30|max:200',
            'height' => 'nullable|numeric|min:100|max:250',
            'blood_type' => 'nullable|string|max:5',
            'allergies' => 'nullable|array',
            'medications' => 'nullable|array',
            'medical_history' => 'nullable|array',
            'symptoms' => 'nullable|array',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'record_date' => 'required|date',
            'next_checkup_date' => 'nullable|date|after:record_date',
            // Additional EMR fields
            'chief_complaint' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'laboratory_results' => 'nullable|string',
            'imaging_results' => 'nullable|string',
            'prescriptions' => 'nullable|string',
            'follow_up_instructions' => 'nullable|string',
            'visit_notes' => 'nullable|string',
            'prescribed_modules' => 'nullable|array|max:20',
            'prescribed_modules.*' => 'string|in:pcma,fmarc,scat,imaging,mri,mapa,ecg_effort,laboratory,dental,postural,specialist,physiotherapy',
        ] + $this->extraRules($request));

        app(\App\Services\MedicalRecordAccess::class)->authorize(auth()->user(),Player::findOrFail($validated['player_id']),null);
        $validated = array_replace($validated, app(\App\Services\HealthRecordIcd11::class)->resolve($request),
            app(\App\Services\HealthRecordMedication::class)->resolve($request));
        $validated = app(\App\Services\HealthRecordSections::class)->protectedColumns($validated,$sections);
        $request->validate(['prepare_aut'=>'sometimes|boolean']);
        $autController=app(MedicalAutController::class);
        // Le formulaire utilise des noms distincts du stockage canonique.
        $testInput = $request->validate([
            'doping_test_date'=>'nullable|date',
            'doping_test_type'=>'nullable|in:urine,blood,hair',
            'doping_test_result'=>'nullable|in:negative,positive,pending,invalid',
        ]);
        $hasDopingTest = $request->filled('doping_test_type') || $request->filled('doping_test_result');
        $autData=$request->boolean('prepare_aut')
            ?$autController->validateDraft($request,'aut_form','aut_documents'):null;
        // Une nouvelle consultation crée toujours un nouvel épisode clinique.
        // Le dossier longitudinal est l'agrégation des épisodes du joueur.
        [$healthRecord,$message]=\Illuminate\Support\Facades\DB::transaction(function()use($request,$validated,$autController,$autData,$testInput,$hasDopingTest,$sections){
            if ($hasDopingTest) {
                $validated['doping_tests'] = [[
                    'date'=>$testInput['doping_test_date'] ?? null,
                    'type'=>$testInput['doping_test_type'] ?? null,
                    'result'=>$testInput['doping_test_result'] ?? null,
                ]];
            }

            $validated['user_id'] = auth()->id();
            $validated['status'] = 'active';

            if (isset($validated['weight']) && isset($validated['height'])) {
                $heightInMeters = $validated['height'] / 100;
                $validated['bmi'] = round($validated['weight'] / ($heightInMeters * $heightInMeters), 2);
            }

            $healthRecord = HealthRecord::create($validated);
            $message = 'Visite médicale enregistrée avec succès.';

            app(\App\Services\HealthRecordSections::class)->persist($healthRecord,$sections);
            if($autData!==null){
                $autController->persistDraft($request,$healthRecord,$autData);
                $message.=' '.__('medical_aut.saved');
            }
            return [$healthRecord,$message];
        });

        // Broadcast health record created/updated event
        event(new HealthRecordCreated($healthRecord));

        // Aucun pronostic n'est créé sans modèle médical validé.

        if (!empty($validated['visit_id'])) {
            $visit = \App\Models\Visit::with('appointment')->find($validated['visit_id']);
            if ($visit) {
                $visitData = $visit->administrative_data ?? [];
                $visitData['health_record_id'] = $healthRecord->id;
                $visitData['completed_at'] = now()->toIso8601String();
                $visitData['prescribed_modules'] = array_values($validated['prescribed_modules'] ?? []);
                $visitData['orders_status'] = !empty($visitData['prescribed_modules']) ? 'pending' : 'none';
                $visitData['prescription_summary'] = $validated['prescriptions'] ?? null;
                $visit->update([
                    'status' => 'Terminé',
                    'notes' => $validated['visit_notes'] ?? $visit->notes,
                    'administrative_data' => $visitData,
                ]);
                $visit->appointment?->update(['status' => 'Terminé']);
            }
        }

        $dossier = $this->baseRecordFor($healthRecord);

        return redirect()->route('health-records.show', $dossier)
            ->with('success', $message);
    }

    /**
     * Update existing health record with new visit data
     */
    private function updateExistingRecord(HealthRecord $record, array $newData): void
    {
        // Merge visit-specific data
        $visitData = [
            'visit_date' => $newData['visit_date'],
            'doctor_name' => $newData['doctor_name'],
            'visit_type' => $newData['visit_type'],
            'chief_complaint' => $newData['chief_complaint'] ?? null,
            'physical_examination' => $newData['physical_examination'] ?? null,
            'laboratory_results' => $newData['laboratory_results'] ?? null,
            'imaging_results' => $newData['imaging_results'] ?? null,
            'prescriptions' => $newData['prescriptions'] ?? null,
            'follow_up_instructions' => $newData['follow_up_instructions'] ?? null,
            'visit_notes' => $newData['visit_notes'] ?? null,
        ];

        // Update basic health data if provided
        $healthData = [
            'blood_pressure_systolic' => $newData['blood_pressure_systolic'] ?? null,
            'blood_pressure_diastolic' => $newData['blood_pressure_diastolic'] ?? null,
            'heart_rate' => $newData['heart_rate'] ?? null,
            'temperature' => $newData['temperature'] ?? null,
            'weight' => $newData['weight'] ?? null,
            'height' => $newData['height'] ?? null,
            'blood_type' => $newData['blood_type'] ?? null,
            'allergies' => $newData['allergies'] ?? null,
            'medications' => $newData['medications'] ?? null,
            'medical_history' => $newData['medical_history'] ?? null,
            'symptoms' => $newData['symptoms'] ?? null,
            'diagnosis' => $newData['diagnosis'] ?? null,
            'treatment_plan' => $newData['treatment_plan'] ?? null,
            'next_checkup_date' => $newData['next_checkup_date'] ?? null,
        ];
        $healthData = array_intersect_key($healthData,$newData);
        $healthData['record_date'] = $newData['record_date'];

        // Calculate BMI if weight and height are provided
        if (isset($newData['weight']) && isset($newData['height'])) {
            $heightInMeters = $newData['height'] / 100;
            $healthData['bmi'] = round($newData['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        // Une section omise ne doit pas effacer les résultats d'une visite précédente.
        $visitData = array_intersect_key($visitData,$newData);
        // Merge all data
        $updateData = array_replace($newData, $visitData, $healthData);
        
        $record->update($updateData);
    }

    public function show(HealthRecord $healthRecord): View
    {
        $this->authorizeRecord($healthRecord);

        if (!request()->boolean('legacy')) {
            $healthRecord = $this->baseRecordFor($healthRecord);
        }

        $healthRecord->load([
            'user',
            'player',
            'predictions',
        ]);
        
        // Load PCMA records for this player
        $pcmaRecords = \App\Models\PCMA::where('player_id', $healthRecord->player_id)
            ->with(['player', 'assessor'])
            ->orderBy('assessment_date', 'desc')
            ->get();
        
        // Historique du même joueur, après contrôle de ses droits médicaux.
        $dopingRecords = HealthRecord::where('player_id', $healthRecord->player_id)
            ->orderByDesc('record_date')->get();
        $autRequests = Schema::hasTable('tue_requests') && Schema::hasColumn('tue_requests', 'player_id')
            ? \App\Models\TUERequest::where('player_id', $healthRecord->player_id)->orderByDesc('request_date')->get()
            : collect();
        $sectionHistory = app(\App\Services\HealthRecordSections::class)->history($dopingRecords);
        $sectionDocuments = Schema::hasTable('health_record_documents')
            ? \App\Models\HealthRecordDocument::where('player_id',$healthRecord->player_id)->orderByDesc('exam_date')->get() : collect();

        $intakeDocuments = collect();
        if (Schema::hasTable('documents') && Schema::hasTable('visits') && Schema::hasTable('athletes')) {
            $intakeDocuments = \App\Models\Document::whereHas('visit.athlete', function ($query) use ($healthRecord) {
                    $query->where('player_id', $healthRecord->player_id);
                })
                ->with(['visit.appointment', 'uploadedBy'])
                ->orderByDesc('created_at')
                ->get();
        }

        $posturalAssessments = collect();
        if (Schema::hasTable('postural_assessments')) {
            $posturalQuery = \App\Models\PosturalAssessment::where('player_id', $healthRecord->player_id)
                ->with('clinician')
                ->orderByDesc('assessment_date');

            $posturalRelations = [];
            if (Schema::hasTable('postural_findings')) {
                $posturalRelations[] = 'findings';
            }
            if (Schema::hasTable('postural_measurements')) {
                $posturalRelations[] = 'measurements';
            }
            if ($posturalRelations) {
                $posturalQuery->with($posturalRelations);
            }

            $posturalAssessments = $posturalQuery->get();
        }

        $vigilance = request()->boolean('legacy')
            ? null
            : app(\App\Services\PlayerVigilanceService::class)->assess($healthRecord->player);

        $view = request()->boolean('legacy') ? 'health-records.show' : 'health-records.workspace';

        return view($view, compact(
            'healthRecord',
            'pcmaRecords',
            'dopingRecords',
            'autRequests',
            'sectionHistory',
            'sectionDocuments',
            'intakeDocuments',
            'posturalAssessments',
            'vigilance'
        ));
    }

    public function module(HealthRecord $healthRecord, string $module): View
    {
        $this->authorizeRecord($healthRecord);
        $healthRecord = $this->baseRecordFor($healthRecord);

        $sections = app(\App\Services\HealthRecordSections::class);
        $definitions = $sections->definitions();
        abort_unless(isset($definitions[$module]), 404);

        $healthRecord->load('player.club');
        $definition = $definitions[$module];
        $sectionValues = $sections->formValues($healthRecord);

        return view('health-records.module', compact(
            'healthRecord',
            'module',
            'definition',
            'sectionValues'
        ));
    }

    public function storeModule(Request $request, HealthRecord $healthRecord, string $module): RedirectResponse
    {
        $this->authorizeRecord($healthRecord);
        $healthRecord = $this->baseRecordFor($healthRecord);

        $service = app(\App\Services\HealthRecordSections::class);
        abort_unless(isset($service->definitions()[$module]), 404);

        $capture = $request->input('capture', []);
        $capture[$module] = 1;
        $request->merge(['capture' => $capture]);

        $sections = $service->prepare($request);
        abort_unless(isset($sections[$module]), 422, 'Aucune donnée de module à enregistrer.');

        \Illuminate\Support\Facades\DB::transaction(function () use ($healthRecord, $service, $sections) {
            $locked = HealthRecord::whereKey($healthRecord->id)->lockForUpdate()->firstOrFail();
            $service->persist($locked, $sections);
        });

        return redirect()->route('health-records.show', [
            'healthRecord' => $healthRecord,
            'workspace' => 'exams',
        ])->with('success', 'Module spécialisé enregistré dans le dossier santé.');
    }

    public function edit(HealthRecord $healthRecord): View
    {
        $this->authorizeRecord($healthRecord);
        $players = $this->playersQuery()->orderBy('name')->get();
        $sectionValues = array_replace(app(\App\Services\HealthRecordSections::class)->formValues($healthRecord),session()->getOldInput());
        $view = request()->boolean('advanced') ? 'health-records.edit' : 'health-records.edit-visit';

        return view($view, compact('healthRecord', 'players','sectionValues'));
    }

    public function update(Request $request, HealthRecord $healthRecord): RedirectResponse
    {
        $this->authorizeRecord($healthRecord);

        if ($request->input('workflow') === 'visit' && $request->filled('visit_date')) {
            $request->merge(['record_date' => $request->input('visit_date')]);
        }
        $this->normalizeLists($request);
        $sections = app(\App\Services\HealthRecordSections::class)->prepare($request);
        abort_if($request->has('player_id') && (int)$request->player_id !== (int)$healthRecord->player_id,422,'Le joueur du dossier ne peut pas être remplacé.');
        $validated = $request->validate([
            'player_id' => 'nullable|exists:players,id',
            'visit_date' => 'nullable|date',
            'doctor_name' => 'nullable|string|max:255',
            'visit_type' => 'nullable|string|in:consultation,emergency,follow_up,pre_season,post_match,rehabilitation',
            'chief_complaint' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'prescriptions' => 'nullable|string',
            'follow_up_instructions' => 'nullable|string',
            'visit_notes' => 'nullable|string',
            'blood_pressure_systolic' => 'nullable|integer|min:70|max:200',
            'blood_pressure_diastolic' => 'nullable|integer|min:40|max:130',
            'heart_rate' => 'nullable|integer|min:40|max:200',
            'temperature' => 'nullable|numeric|min:35|max:42',
            'weight' => 'nullable|numeric|min:30|max:200',
            'height' => 'nullable|numeric|min:100|max:250',
            'blood_type' => 'nullable|string|max:5',
            'allergies' => 'nullable|array',
            'medications' => 'nullable|array',
            'medical_history' => 'nullable|array',
            'symptoms' => 'nullable|array',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'record_date' => 'required|date',
            'next_checkup_date' => 'nullable|date|after:record_date',
        ] + $this->extraRules($request));

        $validated = array_replace($validated, app(\App\Services\HealthRecordIcd11::class)->resolve($request),
            app(\App\Services\HealthRecordMedication::class)->resolve($request));
        $validated = app(\App\Services\HealthRecordSections::class)->protectedColumns($validated,$sections);

        // Recalculer le BMI si nécessaire
        if (isset($validated['weight']) && isset($validated['height'])) {
            $heightInMeters = $validated['height'] / 100;
            $validated['bmi'] = round($validated['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        \Illuminate\Support\Facades\DB::transaction(function()use($healthRecord,$validated,$sections){
            $locked = HealthRecord::lockForUpdate()->findOrFail($healthRecord->id);
            $locked->update($validated);
            app(\App\Services\HealthRecordSections::class)->persist($locked,$sections);
        });

        return redirect()->route('health-records.show', $healthRecord)
            ->with('success', 'Dossier médical mis à jour avec succès.');
    }

    public function destroy(HealthRecord $healthRecord): RedirectResponse
    {
        $this->authorizeRecord($healthRecord);
        $healthRecord->delete();
        return redirect()->route('health-records.index')
            ->with('success', 'Dossier médical supprimé avec succès.');
    }

    public function generatePrediction(HealthRecord $healthRecord): JsonResponse
    {
        $this->authorizeRecord($healthRecord);
        return response()->json(['success'=>false,'message'=>__('healthcare_repair.unvalidated')],503);
    }

    public function generateHl7Cda(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'analysis_data' => 'required|array',
                'player_id' => 'required|integer|exists:players,id',
                'record_date' => 'required|date',
                'diagnosis' => 'nullable|string',
                'treatment_plan' => 'nullable|string',
            ]);

            $analysisData = $request->input('analysis_data');
            $player = Player::findOrFail($request->input('player_id'));
            app(\App\Services\MedicalRecordAccess::class)->authorize(auth()->user(),$player,null);
            
            // Generate HL7 CDA XML
            $hl7CdaXml = $this->generateHl7CdaXml($analysisData, $player, $request->all());
            
            // Store the report in the database
            $reportId = $this->storeHl7CdaReport($analysisData, $player, $hl7CdaXml);
            
            // Generate download URL
            $downloadUrl = route('health-records.download-hl7-cda', $reportId);
            
            return response()->json([
                'success' => true,
                'report_id' => $reportId,
                'report_type' => $analysisData['type'] ?? 'AI Analysis',
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'download_url' => $downloadUrl,
                'message' => 'Rapport HL7 CDA généré avec succès'
            ]);

        } catch (\Illuminate\Validation\ValidationException|\Symfony\Component\HttpKernel\Exception\HttpException|\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('HL7 CDA Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('pcma_workflow.service_unavailable')
            ], 500);
        }
    }

    private function generateHl7CdaXml(array $analysisData, Player $player, array $requestData): string
    {

        
        $analysisType = $analysisData['type'] ?? 'Unknown';
        $analysis = $analysisData['analysis'] ?? [];
        $timestamp = $analysisData['timestamp'] ?? now()->toISOString();
        

        
        // Create HL7 CDA XML structure with better formatting
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="hl7-cda-stylesheet.xsl"?>' . "\n";
        $xml .= '<ClinicalDocument xmlns="urn:hl7-org:v3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . "\n";
        
        // Header
        $xml .= '  <realmCode code="FR"/>' . "\n";
        $xml .= '  <typeId root="2.16.840.1.113883.1.3" extension="POCD_HD000040"/>' . "\n";
        $xml .= '  <templateId root="2.16.840.1.113883.10.20.1"/>' . "\n";
        $xml .= '  <id root="' . uniqid() . '"/>' . "\n";
        $xml .= '  <code code="11506-3" codeSystem="2.16.840.1.113883.6.1" displayName="Progress note"/>' . "\n";
        $xml .= '  <title>Rapport d\'Analyse IA - ' . htmlspecialchars($analysisType) . '</title>' . "\n";
        $xml .= '  <effectiveTime value="' . date('YmdHis', strtotime($timestamp)) . '"/>' . "\n";
        $xml .= '  <confidentialityCode code="N" codeSystem="2.16.840.1.113883.5.25"/>' . "\n";
        $xml .= '  <languageCode code="fr-FR"/>' . "\n";
        
        // Patient Information
        $xml .= '  <recordTarget>' . "\n";
        $xml .= '    <patientRole>' . "\n";
        $xml .= '      <id root="2.16.840.1.113883.19.5" extension="' . htmlspecialchars($player->id) . '"/>' . "\n";
        $xml .= '      <patient>' . "\n";
        $xml .= '        <name>' . "\n";
        $xml .= '          <given>' . htmlspecialchars($player->name) . '</given>' . "\n";
        $xml .= '        </name>' . "\n";
        $xml .= '        <administrativeGenderCode code="' . ($player->gender ?? 'U') . '"/>' . "\n";
        $xml .= '        <birthTime value="' . ($player->date_of_birth ? date('Ymd', strtotime($player->date_of_birth)) : '') . '"/>' . "\n";
        $xml .= '      </patient>' . "\n";
        $xml .= '    </patientRole>' . "\n";
        $xml .= '  </recordTarget>' . "\n";
        
        // Author
        $xml .= '  <author>' . "\n";
        $xml .= '    <time value="' . date('YmdHis') . '"/>' . "\n";
        $xml .= '    <assignedAuthor>' . "\n";
        $xml .= '      <id root="2.16.840.1.113883.19.5" extension="' . (auth()->id() ?? 'SYSTEM') . '"/>' . "\n";
        $xml .= '      <assignedPerson>' . "\n";
        $xml .= '        <name>' . htmlspecialchars(auth()->user()->name ?? 'Système IA') . '</name>' . "\n";
        $xml .= '      </assignedPerson>' . "\n";
        $xml .= '    </assignedAuthor>' . "\n";
        $xml .= '  </author>' . "\n";
        
        // Custodian
        $xml .= '  <custodian>' . "\n";
        $xml .= '    <assignedCustodian>' . "\n";
        $xml .= '      <representedCustodianOrganization>' . "\n";
        $xml .= '        <id root="2.16.840.1.113883.19.5" extension="FIT-MEDICAL"/>' . "\n";
        $xml .= '        <name>FIT Medical System</name>' . "\n";
        $xml .= '      </representedCustodianOrganization>' . "\n";
        $xml .= '    </assignedCustodian>' . "\n";
        $xml .= '  </custodian>' . "\n";
        
        // Component
        $xml .= '  <component>' . "\n";
        $xml .= '    <structuredBody>' . "\n";
        
        // Analysis Results Section
        $xml .= '      <component>' . "\n";
        $xml .= '        <section>' . "\n";
        $xml .= '          <templateId root="2.16.840.1.113883.10.20.1.11"/>' . "\n";
        $xml .= '          <code code="8716-3" codeSystem="2.16.840.1.113883.6.1" displayName="Vital signs"/>' . "\n";
        $xml .= '          <title>Résultats de l\'Analyse IA</title>' . "\n";
        $xml .= '          <text>' . "\n";
        $xml .= '            <table border="1" width="100%">' . "\n";
        $xml .= '              <thead>' . "\n";
        $xml .= '                <tr>' . "\n";
        $xml .= '                  <th>Type d\'Analyse</th>' . "\n";
        $xml .= '                  <th>Résultats</th>' . "\n";
        $xml .= '                </tr>' . "\n";
        $xml .= '              </thead>' . "\n";
        $xml .= '              <tbody>' . "\n";
        
        // Add analysis results with better formatting
        if (is_array($analysis)) {
            foreach ($analysis as $key => $value) {
                if (is_string($value)) {
                    $xml .= '                <tr>' . "\n";
                    $xml .= '                  <td style="font-weight: bold; background-color: #f8f9fa;">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) . '</td>' . "\n";
                    $xml .= '                  <td style="padding: 8px; border-left: 1px solid #dee2e6;">' . htmlspecialchars($value) . '</td>' . "\n";
                    $xml .= '                </tr>' . "\n";
                } elseif (is_array($value)) {
                    // Handle nested arrays (like bone structure, joint alignment)
                    $formattedValue = $this->formatNestedAnalysis($value);
                    $xml .= '                <tr>' . "\n";
                    $xml .= '                  <td style="font-weight: bold; background-color: #f8f9fa;">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) . '</td>' . "\n";
                    $xml .= '                  <td style="padding: 8px; border-left: 1px solid #dee2e6;">' . $formattedValue . '</td>' . "\n";
                    $xml .= '                </tr>' . "\n";
                } elseif (is_object($value)) {
                    // Handle objects
                    $formattedValue = $this->formatNestedAnalysis((array)$value);
                    $xml .= '                <tr>' . "\n";
                    $xml .= '                  <td style="font-weight: bold; background-color: #f8f9fa;">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) . '</td>' . "\n";
                    $xml .= '                  <td style="padding: 8px; border-left: 1px solid #dee2e6;">' . $formattedValue . '</td>' . "\n";
                    $xml .= '                </tr>' . "\n";
                }
            }
        } elseif (is_string($analysis)) {
            // Handle case where analysis is a string (like raw text response)
            $xml .= '                <tr>' . "\n";
            $xml .= '                  <td style="font-weight: bold; background-color: #f8f9fa;">Résultats d\'Analyse</td>' . "\n";
            $xml .= '                  <td style="padding: 8px; border-left: 1px solid #dee2e6;">' . htmlspecialchars($analysis) . '</td>' . "\n";
            $xml .= '                </tr>' . "\n";
        }
        
        // If no analysis data, add a default entry
        if (empty($analysis) || !is_array($analysis)) {
            $xml .= '                <tr>' . "\n";
            $xml .= '                  <td style="font-weight: bold; background-color: #f8f9fa;">Type d\'Analyse</td>' . "\n";
            $xml .= '                  <td style="padding: 8px; border-left: 1px solid #dee2e6;">' . htmlspecialchars($analysisType) . '</td>' . "\n";
            $xml .= '                </tr>' . "\n";
            $xml .= '                <tr>' . "\n";
            $xml .= '                  <td style="font-weight: bold; background-color: #f8f9fa;">Données d\'Analyse</td>' . "\n";
            $xml .= '                  <td style="padding: 8px; border-left: 1px solid #dee2e6;">' . htmlspecialchars(json_encode($analysis, JSON_PRETTY_PRINT)) . '</td>' . "\n";
            $xml .= '                </tr>' . "\n";
        }
        
        $xml .= '              </tbody>' . "\n";
        $xml .= '            </table>' . "\n";
        $xml .= '          </text>' . "\n";
        $xml .= '        </section>' . "\n";
        $xml .= '      </component>' . "\n";
        
        // Diagnosis Section
        if (!empty($requestData['diagnosis'])) {
            $xml .= '      <component>' . "\n";
            $xml .= '        <section>' . "\n";
            $xml .= '          <templateId root="2.16.840.1.113883.10.20.1.16"/>' . "\n";
            $xml .= '          <code code="29548-5" codeSystem="2.16.840.1.113883.6.1" displayName="Diagnosis"/>' . "\n";
            $xml .= '          <title>Diagnostic</title>' . "\n";
            $xml .= '          <text>' . htmlspecialchars($requestData['diagnosis']) . '</text>' . "\n";
            $xml .= '        </section>' . "\n";
            $xml .= '      </component>' . "\n";
        }
        
        // Treatment Plan Section
        if (!empty($requestData['treatment_plan'])) {
            $xml .= '      <component>' . "\n";
            $xml .= '        <section>' . "\n";
            $xml .= '          <templateId root="2.16.840.1.113883.10.20.1.20"/>' . "\n";
            $xml .= '          <code code="18776-5" codeSystem="2.16.840.1.113883.6.1" displayName="Plan of care"/>' . "\n";
            $xml .= '          <title>Plan de Traitement</title>' . "\n";
            $xml .= '          <text>' . htmlspecialchars($requestData['treatment_plan']) . '</text>' . "\n";
            $xml .= '        </section>' . "\n";
            $xml .= '      </component>' . "\n";
        }
        
        $xml .= '    </structuredBody>' . "\n";
        $xml .= '  </component>' . "\n";
        $xml .= '</ClinicalDocument>';
        

        
        return $xml;
    }

    private function formatNestedAnalysis(array $data): string
    {
        $html = '<div style="font-family: monospace; font-size: 12px;">';
        foreach ($data as $key => $value) {
            $keyFormatted = ucfirst(str_replace('_', ' ', $key));
            if (is_string($value)) {
                $html .= '<div style="margin: 4px 0; padding: 4px; background-color: #f8f9fa; border-left: 3px solid #007bff;">';
                $html .= '<strong>' . htmlspecialchars($keyFormatted) . ':</strong> ';
                $html .= htmlspecialchars($value);
                $html .= '</div>';
            } elseif (is_array($value)) {
                $html .= '<div style="margin: 4px 0; padding: 4px; background-color: #e9ecef; border-left: 3px solid #28a745;">';
                $html .= '<strong>' . htmlspecialchars($keyFormatted) . ':</strong>';
                $html .= '<div style="margin-left: 16px; margin-top: 4px;">';
                $html .= $this->formatNestedAnalysis($value);
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        $html .= '</div>';
        return $html;
    }

    private function storeHl7CdaReport(array $analysisData, Player $player, string $xmlContent): string
    {
        // Create a unique report ID
        $reportId = 'HL7_' . uniqid();
        
        // Store the XML file
        $filename = $reportId . '.xml';
        $filePath = 'hl7_reports/' . $filename;
        
        \Storage::disk('local')->put($filePath, $xmlContent);
        
        // Store report metadata in database (you might want to create a dedicated table for this)
        // For now, we'll store it in the health_records table as a JSON field
                    $healthRecord = HealthRecord::create([
                'player_id' => $player->id,
                'user_id' => auth()->id(),
                'record_date' => now(),
                'diagnosis' => 'Rapport HL7 CDA - ' . ($analysisData['type'] ?? 'Analyse IA'),
                'treatment_plan' => json_encode([
                    'report_id' => $reportId,
                    'report_type' => $analysisData['type'] ?? 'AI Analysis',
                    'file_path' => $filePath,
                    'storage_disk' => 'local',
                    'analysis_data' => $analysisData,
                    'generated_at' => now()->toISOString(),
                    'player_id' => $player->id,
                    'player_name' => $player->name
                ]),
                'status' => 'hl7_report'
            ]);
        
        return $reportId;
    }

    public function downloadHl7Cda(string $reportId)
    {
        try {
            // Find the health record with this report ID
            $healthRecord = HealthRecord::where('status', 'hl7_report')
                ->whereJsonContains('treatment_plan->report_id', $reportId)
                ->firstOrFail();
            
            $this->authorizeRecord($healthRecord);
            $reportData = json_decode($healthRecord->treatment_plan, true);
            $filePath = $reportData['file_path'] ?? null;
            
            $disk=($reportData['storage_disk']??'public')==='local'?'local':'public';
            if (!$filePath || !\Storage::disk($disk)->exists($filePath)) {
                throw new \Exception('Rapport HL7 CDA non trouvé');
            }
            
            $content = \Storage::disk($disk)->get($filePath);
            $filename = $reportId . '.xml';
            
            return response($content)
                ->header('Content-Type', 'application/xml')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Illuminate\Validation\ValidationException|\Symfony\Component\HttpKernel\Exception\HttpException|\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('HL7 CDA Download Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('pcma_workflow.service_unavailable')
            ], 500);
        }
    }

    public function viewHl7Cda(string $reportId)
    {
        try {
            // Find the health record with this report ID
            $healthRecord = HealthRecord::where('status', 'hl7_report')
                ->whereJsonContains('treatment_plan->report_id', $reportId)
                ->firstOrFail();
            
            $this->authorizeRecord($healthRecord);
            $reportData = json_decode($healthRecord->treatment_plan, true);
            $filePath = $reportData['file_path'] ?? null;
            
            $disk=($reportData['storage_disk']??'public')==='local'?'local':'public';
            if (!$filePath || !\Storage::disk($disk)->exists($filePath)) {
                throw new \Exception('Rapport HL7 CDA non trouvé');
            }
            
            $xmlContent = \Storage::disk($disk)->get($filePath);
            
            // Convert XML to HTML for better viewing
            $htmlContent = '<!doctype html><html><meta charset="utf-8"><body><pre>'.htmlspecialchars($xmlContent, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8').'</pre></body></html>';
            
            return response($htmlContent)
                ->header('Content-Type', 'text/html');

        } catch (\Illuminate\Validation\ValidationException|\Symfony\Component\HttpKernel\Exception\HttpException|\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('HL7 CDA View Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('pcma_workflow.service_unavailable')
            ], 500);
        }
    }

    private function convertXmlToHtml(string $xmlContent, array $reportData): string
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xmlContent);
        
        $html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport HL7 CDA - ' . htmlspecialchars($reportData['report_type'] ?? 'Analyse IA') . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #007bff; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .section h2 { color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .patient-info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; }
        .analysis-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .analysis-table th, .analysis-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .analysis-table th { background-color: #007bff; color: white; }
        .analysis-table tr:nth-child(even) { background-color: #f8f9fa; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666; }
        .print-btn { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 10px; }
        .print-btn:hover { background-color: #0056b3; }
        @media print {
            .print-btn { display: none; }
            body { background-color: white; }
            .container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Rapport HL7 CDA - ' . htmlspecialchars($reportData['report_type'] ?? 'Analyse IA') . '</h1>
            <p>Généré le: ' . htmlspecialchars($reportData['generated_at'] ?? now()->format('Y-m-d H:i:s')) . '</p>
            <p>ID du rapport: ' . htmlspecialchars($reportData['report_id'] ?? 'N/A') . '</p>
        </div>
        
        <div class="section">
            <h2>Informations du Patient</h2>
            <div class="patient-info">
                <p><strong>ID:</strong> ' . htmlspecialchars($reportData['player_id'] ?? 'N/A') . '</p>
                <p><strong>Nom:</strong> ' . htmlspecialchars($reportData['player_name'] ?? 'N/A') . '</p>
            </div>
        </div>
        
        <div class="section">
            <h2>Résultats de l\'Analyse IA</h2>
            <table class="analysis-table">
                <thead>
                    <tr>
                        <th>Type d\'Analyse</th>
                        <th>Résultats</th>
                    </tr>
                </thead>
                <tbody>';
        
        // Extract analysis data from XML
        $analysisData = $reportData['analysis_data'] ?? [];
        if (!empty($analysisData['analysis'])) {
            $analysis = $analysisData['analysis'];
            if (is_array($analysis)) {
                foreach ($analysis as $key => $value) {
                    $keyFormatted = ucfirst(str_replace('_', ' ', $key));
                    if (is_string($value)) {
                        $html .= '<tr><td>' . htmlspecialchars($keyFormatted) . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
                    } elseif (is_array($value)) {
                        $formattedValue = $this->formatNestedAnalysis($value);
                        $html .= '<tr><td>' . htmlspecialchars($keyFormatted) . '</td><td>' . $formattedValue . '</td></tr>';
                    }
                }
            } elseif (is_string($analysis)) {
                // Handle case where analysis is a string
                $html .= '<tr><td>Résultats d\'Analyse</td><td>' . htmlspecialchars($analysis) . '</td></tr>';
            }
        } elseif (!empty($analysisData['text'])) {
            // Handle case where analysis data has a text field
            $html .= '<tr><td>Résultats d\'Analyse</td><td>' . htmlspecialchars($analysisData['text']) . '</td></tr>';
        }
        
        $html .= '</tbody></table></div>
        
        <div class="footer">
            <p>Document généré automatiquement par le système FIT Medical</p>
            <button class="print-btn" onclick="window.print()">Imprimer le Rapport</button>
            <button class="print-btn" onclick="window.location.href=\'/health-records/download-hl7-cda/' . htmlspecialchars($reportData['report_id']) . '\'">Télécharger XML</button>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
}
