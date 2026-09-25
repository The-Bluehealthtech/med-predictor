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

class HealthRecordController extends Controller
{
    public function index(): View
    {
        $healthRecords = HealthRecord::with(['user', 'player'])
            ->orderBy('record_date', 'desc')
            ->paginate(15);

        return view('health-records.index', compact('healthRecords'));
    }

    public function create(Request $request): View
    {
        $players = Player::orderBy('name')->get();
        $visit = null;
        $selectedPlayer = null;
        $isDemo = false;
        
        // Demo players data
        $demoPlayers = [
            1 => ['id' => 1, 'name' => 'John Smith', 'full_name' => 'John Smith', 'first_name' => 'John', 'last_name' => 'Smith', 'date_of_birth' => '1995-03-15', 'age' => 29, 'position' => 'ST', 'nationality' => 'USA', 'club' => ['name' => 'Team Alpha']],
            2 => ['id' => 2, 'name' => 'Sarah Johnson', 'full_name' => 'Sarah Johnson', 'first_name' => 'Sarah', 'last_name' => 'Johnson', 'date_of_birth' => '1993-07-22', 'age' => 31, 'position' => 'MF', 'nationality' => 'Canada', 'club' => ['name' => 'Team Beta']],
            3 => ['id' => 3, 'name' => 'Mike Wilson', 'full_name' => 'Mike Wilson', 'first_name' => 'Mike', 'last_name' => 'Wilson', 'date_of_birth' => '1997-11-08', 'age' => 27, 'position' => 'DF', 'nationality' => 'UK', 'club' => ['name' => 'Team Gamma']],
            4 => ['id' => 4, 'name' => 'Emma Davis', 'full_name' => 'Emma Davis', 'first_name' => 'Emma', 'last_name' => 'Davis', 'date_of_birth' => '1994-05-12', 'age' => 30, 'position' => 'GK', 'nationality' => 'Australia', 'club' => ['name' => 'Team Delta']],
            5 => ['id' => 5, 'name' => 'Alex Brown', 'full_name' => 'Alex Brown', 'first_name' => 'Alex', 'last_name' => 'Brown', 'date_of_birth' => '1996-09-30', 'age' => 28, 'position' => 'FW', 'nationality' => 'Germany', 'club' => ['name' => 'Team Echo']]
        ];
        
        // Si un player_id est fourni, vérifier s'il s'agit d'un joueur démo
        if ($request->has('player_id')) {
            $playerId = $request->player_id;
            
            if (isset($demoPlayers[$playerId])) {
                $selectedPlayer = (object) $demoPlayers[$playerId];
                $isDemo = true;
            } else {
                $selectedPlayer = Player::find($playerId);
            }
        }
        
        // Si un visit_id est fourni, récupérer les données de la visite
        if ($request->has('visit_id')) {
            $visit = \App\Models\Visit::with(['athlete', 'doctor'])->find($request->visit_id);
        }
        
        return view('health-records.create', compact('players', 'visit', 'selectedPlayer', 'isDemo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
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
        ]);

        // Check if there's an existing health record for this player
        $existingRecord = HealthRecord::where('player_id', $validated['player_id'])
            ->where('status', 'active')
            ->first();

        if ($existingRecord) {
            // Update existing record with new visit data
            $this->updateExistingRecord($existingRecord, $validated);
            $healthRecord = $existingRecord;
            $message = 'Dossier médical mis à jour avec succès.';
        } else {
            // Create new health record for this player
            $validated['user_id'] = auth()->id();
            $validated['status'] = 'active';
            
            // Calculate BMI if weight and height are provided
            if (isset($validated['weight']) && isset($validated['height'])) {
                $heightInMeters = $validated['height'] / 100;
                $validated['bmi'] = round($validated['weight'] / ($heightInMeters * $heightInMeters), 2);
            }

            $healthRecord = HealthRecord::create($validated);
            $message = 'Nouveau dossier médical créé avec succès.';
        }

        // Broadcast health record created/updated event
        event(new HealthRecordCreated($healthRecord));

        // Generate medical prediction
        $this->generateMedicalPrediction($healthRecord);

        return redirect()->route('health-records.show', $healthRecord)
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
        $healthData = array_filter([
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
        ]);

        // Calculate BMI if weight and height are provided
        if (isset($newData['weight']) && isset($newData['height'])) {
            $heightInMeters = $newData['height'] / 100;
            $healthData['bmi'] = round($newData['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        // Merge all data
        $updateData = array_merge($visitData, $healthData);
        
        $record->update($updateData);
    }

    public function show(HealthRecord $healthRecord): View
    {
        $healthRecord->load(['user', 'player', 'predictions']);
        
        // Load PCMA records for this player
        $pcmaRecords = \App\Models\PCMA::where('player_id', $healthRecord->player_id)
            ->with(['player', 'assessor'])
            ->orderBy('assessment_date', 'desc')
            ->get();
        
        return view('health-records.show', compact('healthRecord', 'pcmaRecords'));
    }

    public function edit(HealthRecord $healthRecord): View
    {
        $players = Player::orderBy('name')->get();
        return view('health-records.edit', compact('healthRecord', 'players'));
    }

    public function update(Request $request, HealthRecord $healthRecord): RedirectResponse
    {
        $validated = $request->validate([
            'player_id' => 'nullable|exists:players,id',
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
        ]);

        // Recalculer le BMI si nécessaire
        if (isset($validated['weight']) && isset($validated['height'])) {
            $heightInMeters = $validated['height'] / 100;
            $validated['bmi'] = round($validated['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        $healthRecord->update($validated);

        return redirect()->route('health-records.show', $healthRecord)
            ->with('success', 'Dossier médical mis à jour avec succès.');
    }

    public function destroy(HealthRecord $healthRecord): RedirectResponse
    {
        $healthRecord->delete();
        return redirect()->route('health-records.index')
            ->with('success', 'Dossier médical supprimé avec succès.');
    }

    public function generatePrediction(HealthRecord $healthRecord): JsonResponse
    {
        $prediction = $this->generateMedicalPrediction($healthRecord);
        
        return response()->json([
            'success' => true,
            'prediction' => $prediction,
            'message' => 'Prédiction médicale générée avec succès.'
        ]);
    }

    private function generateMedicalPrediction(HealthRecord $healthRecord): MedicalPrediction
    {
        // Logique de prédiction médicale basée sur les données
        $riskFactors = $this->calculateRiskFactors($healthRecord);
        $predictedCondition = $this->predictCondition($healthRecord);
        $riskProbability = $this->calculateRiskProbability($riskFactors);
        $confidenceScore = $this->calculateConfidenceScore($healthRecord);

        return MedicalPrediction::create([
            'health_record_id' => $healthRecord->id,
            'player_id' => $healthRecord->player_id,
            'user_id' => auth()->id(),
            'prediction_type' => 'health_condition',
            'predicted_condition' => $predictedCondition,
            'risk_probability' => $riskProbability,
            'confidence_score' => $confidenceScore,
            'prediction_factors' => $riskFactors,
            'recommendations' => $this->generateRecommendations($riskFactors, $predictedCondition),
            'prediction_date' => now(),
            'valid_until' => now()->addDays(30),
            'status' => 'active',
            'ai_model_version' => '1.0',
            'prediction_notes' => [
                'generated_at' => now()->toISOString(),
                'model_version' => '1.0',
                'data_points_analyzed' => count($riskFactors)
            ]
        ]);
    }

    private function calculateRiskFactors(HealthRecord $healthRecord): array
    {
        $factors = [];

        // Analyse de la pression artérielle
        if ($healthRecord->blood_pressure_systolic && $healthRecord->blood_pressure_diastolic) {
            if ($healthRecord->blood_pressure_systolic > 140 || $healthRecord->blood_pressure_diastolic > 90) {
                $factors[] = 'hypertension';
            }
        }

        // Analyse du rythme cardiaque
        if ($healthRecord->heart_rate) {
            if ($healthRecord->heart_rate > 100) {
                $factors[] = 'tachycardie';
            } elseif ($healthRecord->heart_rate < 60) {
                $factors[] = 'bradycardie';
            }
        }

        // Analyse du BMI
        if ($healthRecord->bmi) {
            if ($healthRecord->bmi > 30) {
                $factors[] = 'obésité';
            } elseif ($healthRecord->bmi > 25) {
                $factors[] = 'surpoids';
            } elseif ($healthRecord->bmi < 18.5) {
                $factors[] = 'insuffisance_pondérale';
            }
        }

        // Analyse de la température
        if ($healthRecord->temperature) {
            if ($healthRecord->temperature > 38) {
                $factors[] = 'fièvre';
            }
        }

        return $factors;
    }

    private function predictCondition(HealthRecord $healthRecord): string
    {
        $riskFactors = $this->calculateRiskFactors($healthRecord);
        
        if (in_array('hypertension', $riskFactors)) {
            return 'Risque cardiovasculaire';
        }
        
        if (in_array('obésité', $riskFactors)) {
            return 'Risque métabolique';
        }
        
        if (in_array('fièvre', $riskFactors)) {
            return 'Infection possible';
        }
        
        if (empty($riskFactors)) {
            return 'État de santé normal';
        }
        
        return 'Surveillance recommandée';
    }

    private function calculateRiskProbability(array $riskFactors): float
    {
        $baseRisk = 0.1;
        $riskPerFactor = 0.15;
        
        return min(1.0, $baseRisk + (count($riskFactors) * $riskPerFactor));
    }

    private function calculateConfidenceScore(HealthRecord $healthRecord): float
    {
        $dataPoints = 0;
        $totalPoints = 8; // Nombre total de points de données possibles
        
        if ($healthRecord->blood_pressure_systolic) $dataPoints++;
        if ($healthRecord->blood_pressure_diastolic) $dataPoints++;
        if ($healthRecord->heart_rate) $dataPoints++;
        if ($healthRecord->temperature) $dataPoints++;
        if ($healthRecord->weight) $dataPoints++;
        if ($healthRecord->height) $dataPoints++;
        if ($healthRecord->blood_type) $dataPoints++;
        if ($healthRecord->medical_history) $dataPoints++;
        
        return round($dataPoints / $totalPoints, 4);
    }

    private function generateRecommendations(array $riskFactors, string $predictedCondition): array
    {
        $recommendations = [];
        
        if (in_array('hypertension', $riskFactors)) {
            $recommendations[] = 'Surveillance régulière de la pression artérielle';
            $recommendations[] = 'Consultation cardiologique recommandée';
        }
        
        if (in_array('obésité', $riskFactors)) {
            $recommendations[] = 'Programme de perte de poids supervisé';
            $recommendations[] = 'Consultation nutritionnelle';
        }
        
        if (in_array('fièvre', $riskFactors)) {
            $recommendations[] = 'Surveillance de la température';
            $recommendations[] = 'Consultation médicale si persistance';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Maintenir les bonnes pratiques de santé';
            $recommendations[] = 'Contrôle médical annuel recommandé';
        }
        
        return $recommendations;
    }

    public function generateHl7Cda(Request $request): JsonResponse
    {
        try {
            \Log::info('HL7 CDA Generation Request received', [
                'request_data' => $request->all(),
                'has_analysis_data' => $request->has('analysis_data'),
                'analysis_data_type' => gettype($request->input('analysis_data'))
            ]);
            
            $request->validate([
                'analysis_data' => 'required|array',
                'player_id' => 'required|integer|exists:players,id',
                'record_date' => 'required|date',
                'diagnosis' => 'nullable|string',
                'treatment_plan' => 'nullable|string',
            ]);

            $analysisData = $request->input('analysis_data');
            $player = Player::findOrFail($request->input('player_id'));
            
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

        } catch (\Exception $e) {
            \Log::error('HL7 CDA Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du rapport HL7 CDA: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateHl7CdaXml(array $analysisData, Player $player, array $requestData): string
    {
        \Log::info('HL7 CDA XML Generation - Analysis Data:', [
            'analysisData' => $analysisData,
            'player' => $player->toArray(),
            'requestData' => $requestData
        ]);
        
        $analysisType = $analysisData['type'] ?? 'Unknown';
        $analysis = $analysisData['analysis'] ?? [];
        $timestamp = $analysisData['timestamp'] ?? now()->toISOString();
        
        \Log::info('HL7 CDA XML Generation - Processed Data:', [
            'analysisType' => $analysisType,
            'analysis' => $analysis,
            'timestamp' => $timestamp
        ]);
        
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
        
        \Log::info('HL7 CDA XML Generated:', [
            'xml_length' => strlen($xml),
            'xml_preview' => substr($xml, 0, 500) . '...'
        ]);
        
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
        
        \Storage::disk('public')->put($filePath, $xmlContent);
        
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
            
            $reportData = json_decode($healthRecord->treatment_plan, true);
            $filePath = $reportData['file_path'] ?? null;
            
            if (!$filePath || !\Storage::disk('public')->exists($filePath)) {
                throw new \Exception('Rapport HL7 CDA non trouvé');
            }
            
            $content = \Storage::disk('public')->get($filePath);
            $filename = $reportId . '.xml';
            
            return response($content)
                ->header('Content-Type', 'application/xml')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            \Log::error('HL7 CDA Download Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement du rapport HL7 CDA: ' . $e->getMessage()
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
            
            $reportData = json_decode($healthRecord->treatment_plan, true);
            $filePath = $reportData['file_path'] ?? null;
            
            if (!$filePath || !\Storage::disk('public')->exists($filePath)) {
                throw new \Exception('Rapport HL7 CDA non trouvé');
            }
            
            $xmlContent = \Storage::disk('public')->get($filePath);
            
            // Convert XML to HTML for better viewing
            $htmlContent = $this->convertXmlToHtml($xmlContent, $reportData);
            
            return response($htmlContent)
                ->header('Content-Type', 'text/html');
                
        } catch (\Exception $e) {
            \Log::error('HL7 CDA View Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'affichage du rapport HL7 CDA: ' . $e->getMessage()
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
