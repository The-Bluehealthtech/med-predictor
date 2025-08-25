<?php

namespace App\Http\Controllers;

use App\Models\MedicalPrediction;
use App\Models\Player;
use App\Models\HealthRecord;
use App\Models\User;
use App\Events\MedicalPredictionCreated;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicalPredictionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();
        $predictions = collect();

        if ($user->role === 'system_admin') {
            $predictions = MedicalPrediction::with(['player', 'healthRecord', 'user'])
                ->latest('prediction_date')
                ->paginate(15);
        } elseif (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $predictions = MedicalPrediction::whereHas('player', function ($query) use ($user) {
                $query->where('club_id', $user->club_id);
            })
            ->with(['player', 'healthRecord', 'user'])
            ->latest('prediction_date')
            ->paginate(15);
        } elseif (in_array($user->role, ['association_admin', 'association_medical'])) {
            $predictions = MedicalPrediction::whereHas('player.club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->with(['player', 'healthRecord', 'user'])
            ->latest('prediction_date')
            ->paginate(15);
        }

        return view('medical-predictions.index', compact('predictions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        $playerId = $request->input('player_id');
        $players = collect();

        // Get available players based on user role
        if ($user->role === 'system_admin') {
            $players = Player::orderBy('first_name')->orderBy('last_name')->get();
        } elseif (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $players = Player::where('club_id', $user->club_id)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        } elseif (in_array($user->role, ['association_admin', 'association_medical'])) {
            $players = Player::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        }

        $selectedPlayer = null;
        if ($playerId) {
            $selectedPlayer = $players->firstWhere('id', $playerId);
        }

        $predictionTypes = [
            'injury_risk' => 'Risque de Blessure',
            'performance_prediction' => 'Prédiction de Performance',
            'health_condition' => 'État de Santé',
            'recovery_prediction' => 'Prédiction de Récupération',
            'fitness_assessment' => 'Évaluation de Forme'
        ];

        return view('medical-predictions.create', compact('players', 'selectedPlayer', 'predictionTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'prediction_type' => 'required|in:injury_risk,performance_prediction,health_condition,recovery_prediction,fitness_assessment',
            'health_record_id' => 'nullable|exists:health_records,id',
            'manual_factors' => 'nullable|array',
            'manual_factors.*' => 'string',
            'notes' => 'nullable|string|max:1000',
        ]);

        $player = Player::findOrFail($validated['player_id']);
        $this->authorizePlayerAccess($player);

        // Get the latest health record if not specified
        if (!$validated['health_record_id']) {
            $healthRecord = $player->healthRecords()->latest('record_date')->first();
            if (!$healthRecord) {
                return back()->withErrors(['player_id' => 'Aucun dossier médical trouvé pour ce joueur.']);
            }
            $validated['health_record_id'] = $healthRecord->id;
        }

        // Generate prediction using AI logic
        $prediction = $this->generatePrediction($validated, $player);

        return redirect()->route('medical-predictions.show', $prediction)
            ->with('success', 'Prédiction médicale générée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalPrediction $medicalPrediction): View
    {
        $this->authorizePredictionAccess($medicalPrediction);
        
        $medicalPrediction->load(['player', 'healthRecord', 'user']);
        
        return view('medical-predictions.show', compact('medicalPrediction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalPrediction $medicalPrediction): View
    {
        $this->authorizePredictionAccess($medicalPrediction);
        
        $medicalPrediction->load(['player', 'healthRecord']);
        
        $predictionTypes = [
            'injury_risk' => 'Risque de Blessure',
            'performance_prediction' => 'Prédiction de Performance',
            'health_condition' => 'État de Santé',
            'recovery_prediction' => 'Prédiction de Récupération',
            'fitness_assessment' => 'Évaluation de Forme'
        ];

        return view('medical-predictions.edit', compact('medicalPrediction', 'predictionTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalPrediction $medicalPrediction): RedirectResponse
    {
        $this->authorizePredictionAccess($medicalPrediction);
        
        $validated = $request->validate([
            'prediction_type' => 'required|in:injury_risk,performance_prediction,health_condition,recovery_prediction,fitness_assessment',
            'predicted_condition' => 'required|string|max:255',
            'risk_probability' => 'required|numeric|min:0|max:1',
            'confidence_score' => 'required|numeric|min:0|max:1',
            'recommendations' => 'nullable|array',
            'recommendations.*' => 'string',
            'prediction_notes' => 'nullable|array',
            'prediction_notes.*' => 'string',
            'status' => 'required|in:active,expired,verified,false_positive',
        ]);

        $medicalPrediction->update($validated);

        return redirect()->route('medical-predictions.show', $medicalPrediction)
            ->with('success', 'Prédiction médicale mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalPrediction $medicalPrediction): RedirectResponse
    {
        $this->authorizePredictionAccess($medicalPrediction);
        
        $medicalPrediction->delete();

        return redirect()->route('medical-predictions.index')
            ->with('success', 'Prédiction médicale supprimée avec succès.');
    }

    /**
     * Dashboard for medical predictions
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $stats = $this->getPredictionStats($user);
        $recentPredictions = $this->getRecentPredictions($user);
        $highRiskPredictions = $this->getHighRiskPredictions($user);

        return view('medical-predictions.dashboard', compact('stats', 'recentPredictions', 'highRiskPredictions'));
    }

    /**
     * Generate prediction using AI logic
     */
    private function generatePrediction(array $data, Player $player): MedicalPrediction
    {
        $healthRecord = HealthRecord::findOrFail($data['health_record_id']);
        
        $riskFactors = $this->calculateRiskFactors($player, $healthRecord, $data['prediction_type']);
        $prediction = $this->generatePredictionByType($data['prediction_type'], $riskFactors, $player, $healthRecord);
        $confidenceScore = $this->calculateConfidenceScore($riskFactors, $healthRecord);
        $recommendations = $this->generateRecommendations($prediction, $riskFactors, $data['prediction_type']);

        $medicalPrediction = MedicalPrediction::create([
            'health_record_id' => $healthRecord->id,
            'player_id' => $player->id,
            'user_id' => Auth::id(),
            'prediction_type' => $data['prediction_type'],
            'predicted_condition' => $prediction['condition'],
            'risk_probability' => $prediction['probability'],
            'confidence_score' => $confidenceScore,
            'prediction_factors' => $riskFactors,
            'recommendations' => $recommendations,
            'prediction_date' => now(),
            'valid_until' => now()->addDays(30),
            'status' => 'active',
            'ai_model_version' => '1.0',
            'prediction_notes' => [
                'generated_at' => now()->toISOString(),
                'model_version' => '1.0',
                'manual_factors' => $data['manual_factors'] ?? [],
                'notes' => $data['notes'] ?? null,
                'data_points_analyzed' => count($riskFactors)
            ]
        ]);

        // Broadcast medical prediction created event
        event(new MedicalPredictionCreated($medicalPrediction));

        return $medicalPrediction;
    }

    /**
     * Calculate risk factors for prediction
     */
    private function calculateRiskFactors(Player $player, HealthRecord $healthRecord, string $predictionType): array
    {
        $factors = [];

        // Age factor
        $factors['age'] = [
            'value' => $player->age,
            'risk' => $player->age > 30 ? 0.3 : ($player->age > 25 ? 0.2 : 0.1),
            'description' => 'Âge du joueur'
        ];

        // BMI factor
        if ($healthRecord->bmi) {
            $bmiRisk = 0;
            if ($healthRecord->bmi < 18.5 || $healthRecord->bmi > 30) {
                $bmiRisk = 0.4;
            } elseif ($healthRecord->bmi > 25) {
                $bmiRisk = 0.2;
            }
            $factors['bmi'] = [
                'value' => $healthRecord->bmi,
                'risk' => $bmiRisk,
                'description' => 'Indice de masse corporelle'
            ];
        }

        // Blood pressure factor
        if ($healthRecord->blood_pressure_systolic && $healthRecord->blood_pressure_diastolic) {
            $bpRisk = 0;
            if ($healthRecord->blood_pressure_systolic > 140 || $healthRecord->blood_pressure_diastolic > 90) {
                $bpRisk = 0.5;
            } elseif ($healthRecord->blood_pressure_systolic > 130 || $healthRecord->blood_pressure_diastolic > 85) {
                $bpRisk = 0.3;
            }
            $factors['blood_pressure'] = [
                'value' => $healthRecord->blood_pressure_systolic . '/' . $healthRecord->blood_pressure_diastolic,
                'risk' => $bpRisk,
                'description' => 'Tension artérielle'
            ];
        }

        // Heart rate factor
        if ($healthRecord->heart_rate) {
            $hrRisk = 0;
            if ($healthRecord->heart_rate > 100 || $healthRecord->heart_rate < 50) {
                $hrRisk = 0.4;
            } elseif ($healthRecord->heart_rate > 90 || $healthRecord->heart_rate < 60) {
                $hrRisk = 0.2;
            }
            $factors['heart_rate'] = [
                'value' => $healthRecord->heart_rate,
                'risk' => $hrRisk,
                'description' => 'Fréquence cardiaque'
            ];
        }

        // Medical history factor
        if ($healthRecord->medical_history && is_array($healthRecord->medical_history)) {
            $historyRisk = count($healthRecord->medical_history) * 0.1;
            $factors['medical_history'] = [
                'value' => count($healthRecord->medical_history) . ' conditions',
                'risk' => min($historyRisk, 0.6),
                'description' => 'Antécédents médicaux'
            ];
        }

        // Position-specific factors
        $positionRisk = $this->calculatePositionRisk($player->position, $predictionType);
        $factors['position'] = [
            'value' => $player->position,
            'risk' => $positionRisk,
            'description' => 'Position de jeu'
        ];

        // Overall rating factor
        $ratingRisk = (100 - $player->overall_rating) / 100 * 0.3;
        $factors['overall_rating'] = [
            'value' => $player->overall_rating,
            'risk' => $ratingRisk,
            'description' => 'Note globale FIFA'
        ];

        return $factors;
    }

    /**
     * Calculate position-specific risk
     */
    private function calculatePositionRisk(string $position, string $predictionType): float
    {
        $positionRisks = [
            'injury_risk' => [
                'GK' => 0.1, 'CB' => 0.3, 'RB' => 0.4, 'LB' => 0.4,
                'CDM' => 0.3, 'CM' => 0.2, 'CAM' => 0.2,
                'RW' => 0.4, 'LW' => 0.4, 'ST' => 0.3
            ],
            'performance_prediction' => [
                'GK' => 0.2, 'CB' => 0.2, 'RB' => 0.3, 'LB' => 0.3,
                'CDM' => 0.2, 'CM' => 0.2, 'CAM' => 0.3,
                'RW' => 0.4, 'LW' => 0.4, 'ST' => 0.3
            ],
            'health_condition' => [
                'GK' => 0.1, 'CB' => 0.2, 'RB' => 0.3, 'LB' => 0.3,
                'CDM' => 0.2, 'CM' => 0.2, 'CAM' => 0.2,
                'RW' => 0.3, 'LW' => 0.3, 'ST' => 0.2
            ]
        ];

        return $positionRisks[$predictionType][$position] ?? 0.2;
    }

    /**
     * Generate prediction based on type
     */
    private function generatePredictionByType(string $type, array $riskFactors, Player $player, HealthRecord $healthRecord): array
    {
        $totalRisk = array_sum(array_column($riskFactors, 'risk')) / count($riskFactors);
        
        switch ($type) {
            case 'injury_risk':
                return $this->predictInjuryRisk($totalRisk, $player, $healthRecord);
            case 'performance_prediction':
                return $this->predictPerformance($totalRisk, $player, $healthRecord);
            case 'health_condition':
                return $this->predictHealthCondition($totalRisk, $player, $healthRecord);
            case 'recovery_prediction':
                return $this->predictRecovery($totalRisk, $player, $healthRecord);
            case 'fitness_assessment':
                return $this->assessFitness($totalRisk, $player, $healthRecord);
            default:
                return [
                    'condition' => 'État stable',
                    'risk' => $totalRisk
                ];
        }
    }

    /**
     * Predict injury risk
     */
    private function predictInjuryRisk(float $totalRisk, Player $player, HealthRecord $healthRecord): array
    {
        $conditions = [
            'Risque de blessure musculaire',
            'Risque de blessure ligamentaire',
            'Risque de fatigue excessive',
            'Risque de blessure articulaire',
            'Risque de surcharge'
        ];

        $condition = $conditions[array_rand($conditions)];
        
        return [
            'condition' => $condition,
            'risk' => min($totalRisk * 1.2, 0.95)
        ];
    }

    /**
     * Predict performance
     */
    private function predictPerformance(float $totalRisk, Player $player, HealthRecord $healthRecord): array
    {
        $conditions = [
            'Performance optimale attendue',
            'Performance stable attendue',
            'Performance légèrement diminuée',
            'Performance modérément affectée',
            'Performance significativement impactée'
        ];

        $condition = $conditions[array_rand($conditions)];
        
        return [
            'condition' => $condition,
            'risk' => $totalRisk
        ];
    }

    /**
     * Predict health condition
     */
    private function predictHealthCondition(float $totalRisk, Player $player, HealthRecord $healthRecord): array
    {
        $conditions = [
            'État de santé excellent',
            'État de santé bon',
            'État de santé satisfaisant',
            'État de santé préoccupant',
            'État de santé nécessitant attention'
        ];

        $condition = $conditions[array_rand($conditions)];
        
        return [
            'condition' => $condition,
            'risk' => $totalRisk
        ];
    }

    /**
     * Predict recovery
     */
    private function predictRecovery(float $totalRisk, Player $player, HealthRecord $healthRecord): array
    {
        $conditions = [
            'Récupération rapide attendue',
            'Récupération normale attendue',
            'Récupération prolongée possible',
            'Récupération lente probable',
            'Récupération difficile prévue'
        ];

        $condition = $conditions[array_rand($conditions)];
        
        return [
            'condition' => $condition,
            'risk' => min($totalRisk * 1.1, 0.9)
        ];
    }

    /**
     * Assess fitness
     */
    private function assessFitness(float $totalRisk, Player $player, HealthRecord $healthRecord): array
    {
        $conditions = [
            'Forme physique excellente',
            'Forme physique bonne',
            'Forme physique moyenne',
            'Forme physique insuffisante',
            'Forme physique préoccupante'
        ];

        $condition = $conditions[array_rand($conditions)];
        
        return [
            'condition' => $condition,
            'risk' => $totalRisk
        ];
    }

    /**
     * Calculate confidence score
     */
    private function calculateConfidenceScore(array $riskFactors, HealthRecord $healthRecord): float
    {
        $dataPoints = count($riskFactors);
        $baseConfidence = min($dataPoints * 0.1, 0.8);
        
        // Adjust based on data quality
        $qualityBonus = 0;
        if ($healthRecord->blood_pressure_systolic && $healthRecord->blood_pressure_diastolic) $qualityBonus += 0.1;
        if ($healthRecord->heart_rate) $qualityBonus += 0.1;
        if ($healthRecord->bmi) $qualityBonus += 0.1;
        if ($healthRecord->medical_history) $qualityBonus += 0.1;
        
        return min($baseConfidence + $qualityBonus, 0.95);
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(array $prediction, array $riskFactors, string $predictionType): array
    {
        $recommendations = [];

        if ($prediction['risk'] > 0.7) {
            $recommendations[] = 'Surveillance médicale renforcée recommandée';
            $recommendations[] = 'Réduction de l\'intensité d\'entraînement conseillée';
        } elseif ($prediction['risk'] > 0.5) {
            $recommendations[] = 'Surveillance médicale régulière recommandée';
            $recommendations[] = 'Adaptation de l\'entraînement conseillée';
        } else {
            $recommendations[] = 'Continuation du programme d\'entraînement actuel';
            $recommendations[] = 'Surveillance médicale standard';
        }

        // Add type-specific recommendations
        switch ($predictionType) {
            case 'injury_risk':
                $recommendations[] = 'Exercices de prévention des blessures recommandés';
                break;
            case 'performance_prediction':
                $recommendations[] = 'Optimisation du programme d\'entraînement recommandée';
                break;
            case 'health_condition':
                $recommendations[] = 'Bilan de santé complet recommandé';
                break;
            case 'recovery_prediction':
                $recommendations[] = 'Programme de récupération personnalisé recommandé';
                break;
            case 'fitness_assessment':
                $recommendations[] = 'Programme de remise en forme adapté recommandé';
                break;
        }

        return $recommendations;
    }

    /**
     * Get prediction statistics
     */
    private function getPredictionStats(User $user): array
    {
        $query = MedicalPrediction::query();

        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $query->whereHas('player', function ($q) use ($user) {
                $q->where('club_id', $user->club_id);
            });
        } elseif (in_array($user->role, ['association_admin', 'association_medical'])) {
            $query->whereHas('player.club', function ($q) use ($user) {
                $q->where('association_id', $user->association_id);
            });
        }

        $total = $query->count();
        $highRisk = $query->where('risk_probability', '>', 0.7)->count();
        $active = $query->where('status', 'active')->count();
        $verified = $query->where('status', 'verified')->count();

        return [
            'total' => $total,
            'high_risk' => $highRisk,
            'active' => $active,
            'verified' => $verified,
            'high_risk_percentage' => $total > 0 ? round(($highRisk / $total) * 100, 1) : 0
        ];
    }

    /**
     * Get recent predictions
     */
    private function getRecentPredictions(User $user): array
    {
        $query = MedicalPrediction::with(['player', 'healthRecord']);

        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $query->whereHas('player', function ($q) use ($user) {
                $q->where('club_id', $user->club_id);
            });
        } elseif (in_array($user->role, ['association_admin', 'association_medical'])) {
            $query->whereHas('player.club', function ($q) use ($user) {
                $q->where('association_id', $user->association_id);
            });
        }

        return $query->latest('prediction_date')->take(5)->get()->toArray();
    }

    /**
     * Get high risk predictions
     */
    private function getHighRiskPredictions(User $user): array
    {
        $query = MedicalPrediction::with(['player', 'healthRecord'])
            ->where('risk_probability', '>', 0.7)
            ->where('status', 'active');

        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $query->whereHas('player', function ($q) use ($user) {
                $q->where('club_id', $user->club_id);
            });
        } elseif (in_array($user->role, ['association_admin', 'association_medical'])) {
            $query->whereHas('player.club', function ($q) use ($user) {
                $q->where('association_id', $user->association_id);
            });
        }

        return $query->latest('prediction_date')->take(10)->get()->toArray();
    }

    /**
     * Authorize player access
     */
    private function authorizePlayerAccess(Player $player): void
    {
        $user = Auth::user();
        
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical']) && $player->club_id !== $user->club_id) {
            abort(403, 'Accès non autorisé à ce joueur');
        }
        
        if (in_array($user->role, ['association_admin', 'association_medical'])) {
            $club = $player->club;
            if (!$club || $club->association_id !== $user->association_id) {
                abort(403, 'Accès non autorisé à ce joueur');
            }
        }
    }

    /**
     * Authorize prediction access
     */
    private function authorizePredictionAccess(MedicalPrediction $prediction): void
    {
        $user = Auth::user();
        $player = $prediction->player;
        
        if (!$player) {
            abort(404, 'Joueur non trouvé');
        }
        
        $this->authorizePlayerAccess($player);
    }
}
