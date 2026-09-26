<?php

namespace App\Http\Controllers;

use App\Models\FhirPatient;
use App\Models\FhirCondition;
use App\Models\FhirCarePlan;
use App\Models\ClinicalConsultation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur pour le workflow clinique FIT
 * Gère les interactions Patient-Clinicien-Agent IA
 */
class ClinicalWorkflowController extends Controller
{
    /**
     * Afficher le portail patient
     */
    public function patientPortal()
    {
        $patient = Auth::user()->fhirPatient;
        
        return view('clinical.patient-portal', compact('patient'));
    }

    /**
     * Afficher le portail clinicien
     */
    public function clinicianPortal()
    {
        try {
            // Récupérer les rendez-vous futurs (comme dans secretary dashboard)
            $upcomingAppointments = \App\Models\Appointment::with('athlete')
                ->where('appointment_date', '>=', now())
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->orderBy('appointment_date', 'asc')
                ->limit(50)
                ->get();
            
            // Récupérer les dossiers médicaux récents pour les statistiques
            $recentHealthRecords = \App\Models\HealthRecord::with('player')
                ->orderBy('record_date', 'desc')
                ->limit(20)
                ->get();
            
            $pcmas = \App\Models\PCMA::with('player')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
            
            // Statistiques réelles
            $stats = [
                'total_patients' => \App\Models\Player::count(),
                'upcoming_appointments' => \App\Models\Appointment::where('appointment_date', '>=', now())->whereIn('status', ['scheduled', 'confirmed'])->count(),
                'active_health_records' => \App\Models\HealthRecord::where('status', 'active')->count(),
                'pending_pcmas' => \App\Models\PCMA::where('status', 'pending')->count(),
                'completed_pcmas' => \App\Models\PCMA::where('status', 'completed')->count(),
                'consultations_today' => \App\Models\HealthRecord::whereDate('record_date', today())->count(),
                'alerts' => \App\Models\HealthRecord::where('status', 'pending')->count()
            ];
            
            return view('clinical.clinician-portal', compact('upcomingAppointments', 'recentHealthRecords', 'pcmas', 'stats'));
            
        } catch (\Exception $e) {
            \Log::error('Erreur dans clinicianPortal: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Retourner une vue d'erreur ou une réponse JSON pour debug
            return response()->json([
                'error' => 'Erreur dans le portail clinicien',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * API: Créer un nouveau patient FHIR
     */
    public function createPatient(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|array',
            'name.family' => 'required|string|max:255',
            'name.given' => 'required|array',
            'gender' => 'required|in:male,female,other,unknown',
            'birth_date' => 'required|date|before:today',
            'telecom' => 'nullable|array',
            'address' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $patient = new FhirPatient();
            $patient->fit_patient_id = FhirPatient::generateFitPatientId();
            $patient->resource_type = 'Patient';
            $patient->name = $request->name;
            $patient->gender = $request->gender;
            $patient->birth_date = $request->birth_date;
            $patient->telecom = $request->telecom;
            $patient->address = $request->address;
            $patient->created_by = Auth::id();
            $patient->updated_by = Auth::id();

            $errors = $patient->validateFhirData();
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'errors' => $errors
                ], 422);
            }

            $patient->save();

            Log::info('Nouveau patient FHIR créé', [
                'patient_id' => $patient->id,
                'fit_patient_id' => $patient->fit_patient_id,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'patient' => $patient->toFhirJson(),
                'message' => 'Patient créé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du patient', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du patient'
            ], 500);
        }
    }

    /**
     * API: Saisir des symptômes (Patient)
     */
    public function submitSymptoms(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:fhir_patients,id',
            'symptoms' => 'required|array|min:1',
            'symptoms.*' => 'required|string|max:500',
            'severity' => 'nullable|in:mild,moderate,severe',
            'duration' => 'nullable|string|max:255',
            'additional_info' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $patient = FhirPatient::findOrFail($request->patient_id);
            
            // Créer une condition basée sur les symptômes
            $condition = FhirCondition::createFromSymptoms(
                $request->symptoms,
                $patient->id,
                Auth::id()
            );

            // Ajouter des informations supplémentaires
            if ($request->severity) {
                $condition->severity = [
                    'coding' => [
                        [
                            'system' => 'http://snomed.info/sct',
                            'code' => $this->getSeverityCode($request->severity),
                            'display' => ucfirst($request->severity)
                        ]
                    ]
                ];
            }

            if ($request->duration) {
                $condition->onset_string = $request->duration;
            }

            if ($request->additional_info) {
                $condition->note = [
                    [
                        'text' => $request->additional_info,
                        'authorReference' => [
                            'reference' => 'User/' . Auth::id()
                        ]
                    ]
                ];
            }

            $condition->save();

            // Déclencher l'analyse IA des symptômes
            $aiAnalysis = $this->analyzeSymptomsWithAI($request->symptoms, $request->additional_info);

            Log::info('Symptômes soumis par le patient', [
                'patient_id' => $patient->id,
                'condition_id' => $condition->id,
                'symptoms_count' => count($request->symptoms),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'condition' => $condition->toFhirJson(),
                'ai_analysis' => $aiAnalysis,
                'message' => 'Symptômes enregistrés avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la soumission des symptômes', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement des symptômes'
            ], 500);
        }
    }

    /**
     * API: Consultation initiale (Clinicien)
     */
    public function initialConsultation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:fhir_patients,id',
            'chief_complaint' => 'required|string|max:1000',
            'history_present_illness' => 'required|string|max:2000',
            'physical_exam' => 'nullable|string|max:2000',
            'assessment' => 'nullable|string|max:1000',
            'plan' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $patient = FhirPatient::findOrFail($request->patient_id);
            
            // Créer la consultation
            $consultation = new ClinicalConsultation();
            $consultation->patient_id = $patient->id;
            $consultation->clinician_id = Auth::id();
            $consultation->consultation_type = 'initial';
            $consultation->chief_complaint = $request->chief_complaint;
            $consultation->history_present_illness = $request->history_present_illness;
            $consultation->physical_exam = $request->physical_exam;
            $consultation->assessment = $request->assessment;
            $consultation->plan = $request->plan;
            $consultation->status = 'completed';
            $consultation->save();

            // Générer le résumé automatique avec IA
            $summary = $this->generateConsultationSummary($consultation);

            // Mettre à jour la consultation avec le résumé
            $consultation->ai_summary = $summary;
            $consultation->save();

            Log::info('Consultation initiale créée', [
                'consultation_id' => $consultation->id,
                'patient_id' => $patient->id,
                'clinician_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'consultation' => $consultation,
                'ai_summary' => $summary,
                'message' => 'Consultation enregistrée avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la consultation', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la consultation'
            ], 500);
        }
    }

    /**
     * API: Support décisionnel clinique avec IA
     */
    public function clinicalDecisionSupport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:fhir_patients,id',
            'clinical_data' => 'required|string|max:2000',
            'question' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $patient = FhirPatient::with(['conditions', 'carePlans', 'observations'])
                                 ->findOrFail($request->patient_id);

            // Préparer les données pour l'IA
            $patientData = [
                'patient' => $patient->toFhirJson(),
                'conditions' => $patient->conditions->map->toFhirJson(),
                'care_plans' => $patient->carePlans->map->toFhirJson(),
                'observations' => $patient->observations->map->toFhirJson(),
                'clinical_data' => $request->clinical_data,
                'question' => $request->question
            ];

            // Appeler le service IA
            $aiResponse = $this->getClinicalDecisionSupport($patientData);

            Log::info('Support décisionnel clinique demandé', [
                'patient_id' => $patient->id,
                'clinician_id' => Auth::id(),
                'question' => $request->question
            ]);

            return response()->json([
                'success' => true,
                'ai_response' => $aiResponse,
                'patient_data' => $patientData,
                'message' => 'Support décisionnel généré avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du support décisionnel', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du support décisionnel'
            ], 500);
        }
    }

    /**
     * Analyser les symptômes avec l'IA
     */
    private function analyzeSymptomsWithAI(array $symptoms, ?string $additionalInfo): array
    {
        // NOTE (audit factice -> reel, 2026-09) : cette méthode renvoyait un
        // texte d'analyse, des explications et des recommandations
        // entièrement fixes ("Les symptômes décrits peuvent indiquer...",
        // score de confiance 0.75 en dur), identiques pour tous les
        // patients et tous les symptômes, sans jamais appeler de service
        // IA externe (le prompt était construit puis jeté). Aucun
        // fournisseur IA n'est configuré dans cette application
        // (services.openai.api_key est vide et aucun appel HTTP réel
        // n'est implémenté) : on renvoie donc un état honnête plutôt
        // qu'une fausse analyse.
        return [
            'available' => false,
            'analysis' => null,
            'explanations' => [],
            'recommendations' => [],
            'confidence_score' => null,
            'message' => "L'analyse IA des symptômes n'est pas disponible : aucun service d'intelligence artificielle n'est connecté à cette application.",
            'generated_at' => now()->toISOString()
        ];
    }

    /**
     * Générer un résumé de consultation avec l'IA
     */
    private function generateConsultationSummary(ClinicalConsultation $consultation): string
    {
        // NOTE (audit factice -> reel, 2026-09) : le commentaire d'origine
        // ("Simulation de la génération de résumé avec IA") était trompeur :
        // ce résumé n'est pas généré par une IA, il est construit
        // directement à partir des vrais champs de la consultation
        // (motif, histoire de la maladie, évaluation, plan). Comportement
        // inchangé, commentaire corrigé.
        $summary = "Résumé de la consultation du " . $consultation->created_at->format('d/m/Y') . ":\n\n";
        $summary .= "Motif de consultation: " . $consultation->chief_complaint . "\n\n";
        $summary .= "Histoire de la maladie: " . substr($consultation->history_present_illness, 0, 200) . "...\n\n";
        
        if ($consultation->assessment) {
            $summary .= "Évaluation: " . $consultation->assessment . "\n\n";
        }
        
        if ($consultation->plan) {
            $summary .= "Plan de traitement: " . $consultation->plan . "\n\n";
        }

        return $summary;
    }

    /**
     * Obtenir le support décisionnel clinique
     */
    private function getClinicalDecisionSupport(array $patientData): array
    {
        // NOTE (audit factice -> reel, 2026-09) : cette méthode renvoyait
        // un résumé de preuves, des recommandations et des facteurs de
        // risque entièrement fixes ("Facteurs de risque identifiés dans
        // l'historique du patient"...), identiques quel que soit le
        // patient ou sa question ($patientData n'était même pas utilisé).
        // Aucun service IA externe n'est configuré ni appelé : état
        // honnête plutôt qu'un faux support décisionnel.
        return [
            'available' => false,
            'evidence_summary' => null,
            'recommendations' => [],
            'clinical_trials' => [],
            'risk_factors' => [],
            'confidence_level' => null,
            'message' => "Le support décisionnel clinique par IA n'est pas disponible : aucun service d'intelligence artificielle n'est connecté à cette application.",
            'generated_at' => now()->toISOString()
        ];
    }

    /**
     * Obtenir le code de sévérité
     */
    private function getSeverityCode(string $severity): string
    {
        $codes = [
            'mild' => '255604002',
            'moderate' => '6736007',
            'severe' => '24484000'
        ];

        return $codes[$severity] ?? '255604002';
    }
}
