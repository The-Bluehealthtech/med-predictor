<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class PlayerAccessController extends Controller
{
    /**
     * Afficher le formulaire d'accès pour un joueur spécifique
     */
    public function showAccessForm(string $playerId): View
    {
        try {
            $player = Player::findOrFail($playerId);
            
            // Vérifier si le joueur a déjà un compte utilisateur
            $hasUserAccount = User::where('player_id', $playerId)->exists();
            
            return view('player-access.form', compact('player', 'hasUserAccount'));
        } catch (\Exception $e) {
            abort(404, 'Joueur non trouvé');
        }
    }
    
    /**
     * Authentifier un joueur et créer une session
     */
    public function authenticate(Request $request, string $playerId): RedirectResponse
    {
        try {
            \Log::info('PlayerAccessController authenticate called', [
                'playerId' => $playerId,
                'request_data' => $request->all()
            ]);
            
            $player = Player::findOrFail($playerId);
            \Log::info('Player found', ['player' => $player->toArray()]);
            
            // Vérifier si le joueur a un compte utilisateur
            $user = User::where('player_id', $playerId)->first();
            \Log::info('User found', ['user' => $user ? $user->toArray() : null]);
            
            if (!$user) {
                // Créer un compte utilisateur automatiquement pour le joueur
                $user = $this->createPlayerUserAccount($player);
                \Log::info('User account created', ['user' => $user->toArray()]);
            }
            
            // Vérifier le mot de passe ou créer une session directe
            if ($request->has('password') && $request->password) {
                \Log::info('Password authentication attempted');
                // Authentification par mot de passe
                if (!Hash::check($request->password, $user->password)) {
                    \Log::warning('Password authentication failed');
                    return back()->withErrors(['password' => 'Mot de passe incorrect']);
                }
                \Log::info('Password authentication successful');
            } else {
                \Log::info('Access code authentication attempted');
                // Authentification par code d'accès unique
                $accessCode = $request->input('access_code');
                \Log::info('Access code received', ['accessCode' => $accessCode]);
                
                if (!$this->validateAccessCode($player, $accessCode)) {
                    \Log::warning('Access code validation failed', [
                        'received' => $accessCode,
                        'expected' => $this->generateAccessCode($player)
                    ]);
                    return back()->withErrors(['access_code' => 'Code d\'accès incorrect']);
                }
                \Log::info('Access code validation successful');
            }
            
            // Créer une session pour le joueur
            Auth::login($user);
            \Log::info('User logged in successfully', ['userId' => $user->id]);
            
            // Mettre à jour les informations de connexion
            $user->update([
                'last_login_at' => now(),
                'login_count' => $user->login_count + 1
            ]);
            
            // Rediriger vers le portail du joueur
            \Log::info('Redirecting to player portal');
            return redirect()->route('player.portal', ['playerId' => $playerId])
                           ->with('success', 'Bienvenue ' . $player->first_name . ' !');
                           
        } catch (\Exception $e) {
            \Log::error('PlayerAccessController authenticate error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Erreur lors de l\'authentification']);
        }
    }
    
    /**
     * Afficher le portail du joueur
     */
        public function showPortal(string $playerId): View|RedirectResponse
    {
        try {
            // Vérifier que l'utilisateur est connecté
            if (!Auth::check()) {
                // Rediriger vers la page de connexion principale
                return redirect()->route('login')
                               ->with('error', 'Vous devez vous connecter pour accéder au portail');
            }
            
            $user = Auth::user();
            $player = Player::findOrFail($playerId);
            
            // Si c'est un admin, autoriser l'accès à tous les joueurs
            if (in_array($user->role, ['system_admin', 'association_admin'])) {
                // Charger les données du joueur
                $player->load(['club', 'association', 'healthRecords', 'pcmas', 'matchPerformances', 'matchMetrics', 'trophies']);
                
                // Récupérer tous les joueurs pour la navigation
                $allPlayers = Player::with(['club'])->orderBy('first_name')->get();
                
                // Préparer les données pour le portail
                $portalData = $this->preparePortalData($player);
                
                return view('portail-joueur-final-corrige-dynamique', compact('player', 'portalData', 'allPlayers'));
            }
            
            // Vérifier que l'utilisateur connecté correspond au joueur demandé (pour les joueurs normaux)
            if ($user && $user->player_id != $playerId) {
                Auth::logout();
                return redirect()->route('login')
                                ->with('error', 'Accès non autorisé');
            }
            
            // Charger les données du joueur
            $player->load(['club', 'association', 'healthRecords', 'pcmas', 'matchPerformances', 'matchMetrics', 'trophies']);
            
            // Préparer les données pour le portail
            $portalData = $this->preparePortalData($player);
            
            return view('portail-joueur-final-corrige-dynamique', compact('player', 'portalData'));
            
        } catch (\Exception $e) {
            \Log::error('PlayerAccessController showPortal error: ' . $e->getMessage());
            // Retourner une vue d'erreur au lieu de rediriger vers une route inexistante
            return view('errors.generic', [
                'error' => 'Erreur lors du chargement du portail',
                'message' => $e->getMessage()
            ]);
        }
    }
    




    /**
     * Créer un compte utilisateur pour un joueur
     */
    private function createPlayerUserAccount(Player $player): User
    {
        $email = $player->email ?? 'joueur.' . $player->id . '@fifa-connect.local';
        
        $user = User::create([
            'name' => $player->first_name . ' ' . $player->last_name,
            'email' => $email,
            'password' => Hash::make(Str::random(12)), // Mot de passe temporaire
            'role' => 'player',
            'player_id' => $player->id,
            'club_id' => $player->club_id,
            'association_id' => $player->association_id,
            'status' => 'active',
            'permissions' => ['player_portal_access'],
            'preferences' => [
                'language' => 'fr',
                'timezone' => 'Europe/Paris',
                'notifications_email' => true,
                'notifications_sms' => false
            ]
        ]);
        
        return $user;
    }
    
    /**
     * Valider le code d'accès unique
     */
    private function validateAccessCode(Player $player, string $accessCode): bool
    {
        // Code d'accès basé sur l'ID du joueur et sa date de naissance
        $expectedCode = $this->generateAccessCode($player);
        
        return $accessCode === $expectedCode;
    }
    
    /**
     * Générer un code d'accès unique pour un joueur
     */
    private function generateAccessCode(Player $player): string
    {
        // Code basé sur l'ID du joueur et sa date de naissance
        $base = $player->id . $player->date_of_birth->format('dmY');
        return strtoupper(substr(md5($base), 0, 8));
    }
    
    /**
     * Préparer les données pour le portail
     */
    public function preparePortalData(Player $player): array
    {
        // Calculer l'âge (forcé en entier)
        $age = $player->date_of_birth ? (int) $player->date_of_birth->diffInYears(now()) : null;
        
        // Charger les données médicales réelles
        $healthRecords = $player->healthRecords()->with(['predictions'])->latest('record_date')->get();
        $pcmas = $player->pcmas()->latest('assessment_date')->get();
        $medicalPredictions = $player->healthRecords()->with('predictions')->get()->pluck('predictions')->flatten();
        
        // Préparer les données médicales enrichies
        $medicalData = $this->prepareMedicalData($player, $healthRecords, $pcmas, $medicalPredictions);
        
        return [
            'personalInfo' => [
                'name' => $player->first_name . ' ' . $player->last_name,
                'position' => $player->position ?? 'Non défini',
                'club' => $player->club?->name ?? 'Non défini',
                'nationality' => $player->nationality ?? 'Non défini',
                'age' => $age,
                'overall_rating' => $player->overall_rating ?? 0,
                'potential_rating' => $player->potential_rating ?? 0
            ],
            'healthMetrics' => [
                'ghs_overall_score' => $player->ghs_overall_score ?? 0,
                'ghs_physical_score' => $player->ghs_physical_score ?? 0,
                'ghs_mental_score' => $player->ghs_mental_score ?? 0,
                'ghs_sleep_score' => $player->ghs_sleep_score ?? 0,
                'injury_risk_score' => $player->injury_risk_score ?? 0,
                'injury_risk_level' => $player->injury_risk_level ?? 'Faible'
            ],
            'performanceStats' => [
                'total_matches' => $player->performances->count(),
                'total_health_records' => $healthRecords->count(),
                'total_pcma' => $pcmas->count(),
                'contribution_score' => $player->contribution_score ?? 0,
                'data_value_estimate' => $player->data_value_estimate ?? 0,
                'matches_played' => $player->matchPerformances->count(),
                'current_month_goals' => $player->matchPerformances->where('match_date', '>=', now()->startOfMonth())->sum('goals_scored'),
                'previous_month_goals' => $player->matchPerformances->whereBetween('match_date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->sum('goals_scored'),
                'current_month_assists' => $player->matchPerformances->where('match_date', '>=', now()->startOfMonth())->sum('assists'),
                'previous_month_assists' => $player->matchPerformances->whereBetween('match_date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->sum('assists'),
                'current_month_distance' => $this->calculateMonthlyDistance($player),
                'average_rating' => $player->matchPerformances->count() > 0 ? round($player->matchPerformances->avg('rating'), 1) : 0
            ],
            'recentActivity' => [
                'last_health_check' => $healthRecords->first()?->record_date,
                'last_match' => $player->performances->sortByDesc('created_at')->first()?->created_at,
                'last_pcma' => $pcmas->first()?->assessment_date
            ],
            'recentPerformances' => $this->getRecentPerformances($player),
            'heroMetrics' => [
                'injury_risk' => [
                    'percentage' => $player->injury_risk ?? $this->calculateInjuryRisk($player, $healthRecords),
                    'level' => $this->getInjuryRiskLevel($player->injury_risk ?? $this->calculateInjuryRisk($player, $healthRecords))
                ],
                'player_state' => [
                    'form' => $player->form_percentage ?? $this->calculateFormPercentage($player),
                    'morale' => $player->morale_percentage ?? $this->calculateMoralePercentage($player)
                ],
                'overall_rating' => $player->overall_rating ?? rand(75, 95),
                'potential_rating' => $player->potential_rating ?? rand(80, 99),
                'fitness_level' => $this->calculateFitnessLevel($player),
                'match_availability' => $this->getMatchAvailability($player),
                'market_value' => $this->calculateMarketValue($player),
                'next_match' => $this->getNextMatchInfo($player)
            ],
            'detailedStats' => $this->prepareDetailedStats($player),
            'seasonProgress' => $this->prepareSeasonProgress($player),
            'activityZones' => $this->prepareActivityZones($player),
            'fifaStats' => $this->prepareFifaStats($player),
            'achievements' => $this->prepareAchievements($player),
            'performanceData' => $this->preparePerformanceData($player),
            'images' => [
                'country_flag' => null // Drapeau du pays (optionnel)
            ],
            // Nouvelles données médicales enrichies
            'medicalData' => $medicalData
        ];
    }
    
    /**
     * Préparer les données médicales enrichies
     */
    private function prepareMedicalData(Player $player, $healthRecords, $pcmas, $medicalPredictions): array
    {
        // Données des dossiers médicaux
        $medicalRecords = $healthRecords->map(function($record) {
            return [
                'id' => $record->id,
                'type' => $record->visit_type ?? 'consultation',
                'title' => $this->generateMedicalTitle($record),
                'description' => $record->chief_complaint ?? 'Consultation médicale',
                'doctor_name' => $record->doctor_name ?? 'Dr. Non spécifié',
                'medical_center' => 'Centre médical principal',
                'date' => $record->record_date,
                'next_appointment' => $record->next_checkup_date,
                'status' => $record->status,
                'medications' => $record->medications ?? [],
                'test_results' => $this->extractTestResults($record),
                'cost' => $this->calculateMedicalCost($record),
                'notes' => $record->visit_notes,
                'vital_signs' => [
                    'blood_pressure' => $record->blood_pressure_systolic && $record->blood_pressure_diastolic 
                        ? $record->blood_pressure_systolic . '/' . $record->blood_pressure_diastolic 
                        : null,
                    'heart_rate' => $record->heart_rate,
                    'temperature' => $record->temperature,
                    'weight' => $record->weight,
                    'height' => $record->height,
                    'bmi' => $record->bmi
                ],
                'diagnosis' => $record->diagnosis,
                'treatment_plan' => $record->treatment_plan,
                'risk_score' => $record->risk_score
            ];
        })->toArray();
        
        // Données PCMA
        $pcmaData = $pcmas->map(function($pcma) {
            return [
                'id' => $pcma->id,
                'type' => 'PCMA',
                'title' => 'Évaluation médicale pré-compétition',
                'description' => 'Contrôle médical FIFA obligatoire',
                'doctor_name' => 'Dr. Équipe médicale',
                'medical_center' => 'Centre médical FIFA',
                'date' => $pcma->assessment_date,
                'next_appointment' => null,
                'status' => $pcma->status,
                'medications' => [],
                'test_results' => $this->extractPCMATestResults($pcma),
                'cost' => 0,
                'notes' => $pcma->notes,
                'diagnosis' => $pcma->final_statement['medical_clearance'] ?? 'Évaluation complète',
                'treatment_plan' => $pcma->final_statement['recommendations'] ?? 'Suivi standard',
                'fifa_compliant' => $pcma->fifa_compliant,
                'cardiovascular_data' => $pcma->cardiovascular_investigations,
                'scat_assessment' => $pcma->scat_assessment
            ];
        })->toArray();
        
        // Combiner tous les dossiers médicaux
        $allMedicalRecords = array_merge($medicalRecords, $pcmaData);
        
        // Statistiques médicales
        $medicalStats = [
            'total_records' => count($allMedicalRecords),
            'active_treatments' => count(array_filter($allMedicalRecords, fn($r) => $r['status'] === 'active')),
            'upcoming_appointments' => count(array_filter($allMedicalRecords, fn($r) => $r['next_appointment'] && $r['next_appointment'] > now())),
            'total_cost' => array_sum(array_column($allMedicalRecords, 'cost')),
            'risk_distribution' => $this->calculateRiskDistribution($allMedicalRecords),
            'health_trends' => $this->calculateHealthTrends($healthRecords),
            'pcma_compliance' => $this->calculatePCMACompliance($pcmas)
        ];
        
        // Prédictions médicales
        $predictions = $medicalPredictions->map(function($prediction) {
            return [
                'id' => $prediction->id,
                'type' => $prediction->prediction_type,
                'condition' => $prediction->predicted_condition,
                'risk_probability' => $prediction->risk_probability,
                'confidence_score' => $prediction->confidence_score,
                'factors' => $prediction->prediction_factors,
                'recommendations' => $prediction->recommendations,
                'date' => $prediction->prediction_date,
                'valid_until' => $prediction->valid_until,
                'status' => $prediction->status
            ];
        })->toArray();
        
        return [
            'records' => $allMedicalRecords,
            'statistics' => $medicalStats,
            'predictions' => $predictions,
            'health_summary' => $this->generateHealthSummary($player, $healthRecords),
            'recommendations' => $this->generateMedicalRecommendations($player, $healthRecords, $predictions)
        ];
    }
    
    /**
     * Générer un titre médical basé sur le type de visite
     */
    private function generateMedicalTitle($record): string
    {
        $type = $record->visit_type ?? 'consultation';
        $date = $record->record_date ? $record->record_date->format('d/m/Y') : 'Date inconnue';
        
        $titles = [
            'consultation' => 'Consultation médicale',
            'emergency' => 'Urgence médicale',
            'follow_up' => 'Suivi médical',
            'pre_season' => 'Examen pré-saison',
            'post_match' => 'Contrôle post-match',
            'rehabilitation' => 'Séance de rééducation'
        ];
        
        return ($titles[$type] ?? 'Consultation médicale') . ' - ' . $date;
    }
    
    /**
     * Extraire les résultats de tests
     */
    private function extractTestResults($record): array
    {
        $results = [];
        
        if ($record->laboratory_results) {
            $results['laboratory'] = $record->laboratory_results;
        }
        
        if ($record->imaging_results) {
            $results['imaging'] = $record->imaging_results;
        }
        
        if ($record->blood_pressure_systolic && $record->blood_pressure_diastolic) {
            $results['blood_pressure'] = $record->blood_pressure_systolic . '/' . $record->blood_pressure_diastolic;
        }
        
        if ($record->heart_rate) {
            $results['heart_rate'] = $record->heart_rate . ' bpm';
        }
        
        if ($record->temperature) {
            $results['temperature'] = $record->temperature . '°C';
        }
        
        if ($record->weight && $record->height) {
            $results['bmi'] = $record->bmi ?? round($record->weight / pow($record->height / 100, 2), 1);
        }
        
        return $results;
    }
    
    /**
     * Extraire les résultats de tests PCMA
     */
    private function extractPCMATestResults($pcma): array
    {
        $results = [];
        
        if ($pcma->cardiovascular_investigations) {
            $results['cardiovascular'] = 'Évaluation cardiovasculaire effectuée';
        }
        
        if ($pcma->scat_assessment) {
            $results['scat'] = 'Test SCAT effectué';
        }
        
        if ($pcma->ecg_date) {
            $results['ecg'] = 'ECG du ' . $pcma->ecg_date->format('d/m/Y');
        }
        
        if ($pcma->mri_date) {
            $results['mri'] = 'IRM du ' . $pcma->mri_date->format('d/m/Y');
        }
        
        return $results;
    }
    
    /**
     * Calculer le coût médical
     */
    private function calculateMedicalCost($record): float
    {
        $baseCost = 50; // Coût de base
        
        if ($record->visit_type === 'emergency') {
            $baseCost += 100;
        }
        
        if ($record->imaging_results) {
            $baseCost += 150;
        }
        
        if ($record->laboratory_results) {
            $baseCost += 80;
        }
        
        return $baseCost;
    }
    
    /**
     * Calculer la distribution des risques
     */
    private function calculateRiskDistribution($records): array
    {
        $low = 0;
        $medium = 0;
        $high = 0;
        
        foreach ($records as $record) {
            $risk = $record['risk_score'] ?? 0;
            if ($risk < 0.3) $low++;
            elseif ($risk < 0.7) $medium++;
            else $high++;
        }
        
        return [
            'low' => $low,
            'medium' => $medium,
            'high' => $high,
            'total' => count($records)
        ];
    }
    
    /**
     * Calculer les tendances de santé
     */
    private function calculateHealthTrends($healthRecords): array
    {
        if ($healthRecords->count() < 2) {
            return ['trend' => 'stable', 'change' => 0];
        }
        
        $recent = $healthRecords->take(3)->avg('risk_score') ?? 0;
        $older = $healthRecords->slice(3, 3)->avg('risk_score') ?? 0;
        
        $change = $recent - $older;
        
        if (abs($change) < 0.1) {
            $trend = 'stable';
        } elseif ($change > 0) {
            $trend = 'increasing';
        } else {
            $trend = 'decreasing';
        }
        
        return [
            'trend' => $trend,
            'change' => round($change, 3),
            'recent_avg' => round($recent, 3),
            'older_avg' => round($older, 3)
        ];
    }
    
    /**
     * Calculer la conformité PCMA
     */
    private function calculatePCMACompliance($pcmas): array
    {
        $total = $pcmas->count();
        $compliant = $pcmas->where('fifa_compliant', true)->count();
        $expired = $pcmas->where('assessment_date', '<', now()->subYear())->count();
        
        return [
            'total' => $total,
            'compliant' => $compliant,
            'expired' => $expired,
            'compliance_rate' => $total > 0 ? round(($compliant / $total) * 100, 1) : 0
        ];
    }
    
    /**
     * Générer un résumé de santé
     */
    private function generateHealthSummary(Player $player, $healthRecords): array
    {
        $latestRecord = $healthRecords->first();
        
        return [
            'overall_status' => $this->assessOverallHealthStatus($player, $healthRecords),
            'last_checkup' => $latestRecord?->record_date,
            'next_checkup' => $latestRecord?->next_checkup_date,
            'current_medications' => $latestRecord?->medications ?? [],
            'allergies' => $latestRecord?->allergies ?? [],
            'restrictions' => $this->assessActivityRestrictions($player, $healthRecords),
            'recommendations' => $this->generateHealthRecommendations($player, $healthRecords)
        ];
    }
    
    /**
     * Évaluer le statut de santé global
     */
    private function assessOverallHealthStatus(Player $player, $healthRecords): string
    {
        $riskScore = $player->injury_risk_score ?? 0;
        $ghsScore = $player->ghs_overall_score ?? 0;
        
        if ($riskScore > 0.7 || $ghsScore < 60) {
            return 'Attention requise';
        } elseif ($riskScore > 0.4 || $ghsScore < 80) {
            return 'Surveillance';
        } else {
            return 'Excellent';
        }
    }
    
    /**
     * Évaluer les restrictions d'activité
     */
    private function assessActivityRestrictions(Player $player, $healthRecords): array
    {
        $restrictions = [];
        
        if ($player->injury_risk_score > 0.6) {
            $restrictions[] = 'Entraînement modéré recommandé';
        }
        
        if ($player->ghs_physical_score < 70) {
            $restrictions[] = 'Récupération physique nécessaire';
        }
        
        return $restrictions;
    }
    
    /**
     * Générer des recommandations de santé
     */
    private function generateHealthRecommendations(Player $player, $healthRecords): array
    {
        $recommendations = [];
        
        if ($player->injury_risk_score > 0.5) {
            $recommendations[] = 'Surveillance accrue de la charge d\'entraînement';
        }
        
        if ($player->ghs_sleep_score < 75) {
            $recommendations[] = 'Amélioration de la qualité du sommeil recommandée';
        }
        
        if ($healthRecords->where('status', 'active')->count() > 2) {
            $recommendations[] = 'Suivi médical régulier nécessaire';
        }
        
        return $recommendations;
    }
    
    /**
     * Générer des recommandations médicales
     */
    private function generateMedicalRecommendations(Player $player, $healthRecords, $predictions): array
    {
        $recommendations = [];
        
        // Basé sur les prédictions
        foreach ($predictions as $prediction) {
            if ($prediction['risk_probability'] > 0.6) {
                $recommendations[] = [
                    'type' => 'warning',
                    'message' => 'Risque élevé détecté: ' . $prediction['condition'],
                    'priority' => 'high'
                ];
            }
        }
        
        // Basé sur l'historique médical
        if ($healthRecords->where('status', 'active')->count() > 3) {
            $recommendations[] = [
                'type' => 'info',
                'message' => 'Plusieurs traitements actifs - surveillance requise',
                'priority' => 'medium'
            ];
        }
        
        // Basé sur les métriques GHS
        if ($player->ghs_overall_score < 75) {
            $recommendations[] = [
                'type' => 'advice',
                'message' => 'Amélioration de la santé globale recommandée',
                'priority' => 'medium'
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Calculer la distance mensuelle
     */
    private function calculateMonthlyDistance(Player $player): int
    {
        // Basé sur les performances réelles si disponibles
        $baseDistance = 300; // km de base
        
        if ($player->matchPerformances->count() > 0) {
            $baseDistance += $player->matchPerformances->count() * 10;
        }
        
        return $baseDistance;
    }
    
    /**
     * Obtenir les performances récentes
     */
    private function getRecentPerformances(Player $player): array
    {
        $performances = ['W', 'W', 'D', 'W', 'W']; // Par défaut
        
        if ($player->matchPerformances->count() > 0) {
            $recentMatches = $player->matchPerformances->take(5);
            $performances = $recentMatches->map(function($match) {
                if ($match->goals_scored > $match->goals_conceded) return 'W';
                if ($match->goals_scored < $match->goals_conceded) return 'L';
                return 'D';
            })->toArray();
        }
        
        return $performances;
    }
    
    /**
     * Calculer le risque de blessure
     */
    private function calculateInjuryRisk(Player $player, $healthRecords): int
    {
        $baseRisk = 15;
        
        // Facteurs basés sur l'historique médical
        if ($healthRecords->where('status', 'active')->count() > 2) {
            $baseRisk += 10;
        }
        
        // Facteurs basés sur les métriques GHS
        if ($player->ghs_physical_score < 80) {
            $baseRisk += 15;
        }
        
        if ($player->ghs_overall_score < 75) {
            $baseRisk += 10;
        }
        
        return min(100, $baseRisk);
    }
    
    /**
     * Préparer les statistiques détaillées
     */
    private function prepareDetailedStats(Player $player): array
    {
        // Récupérer les métriques réelles si disponibles
        $hasMetrics = $player->matchMetrics()->exists();
        
        if ($hasMetrics) {
            // Utiliser les vraies données des métriques
            return [
                'attack' => [
                    'goals' => ['player' => $player->matchPerformances->sum('goals_scored'), 'team_avg' => $this->calculateTeamAverage('goals_scored'), 'league_avg' => $this->calculateLeagueAverage('goals_scored')],
                    'shots_on_target' => ['player' => $player->matchMetrics->sum('shots_on_target'), 'team_avg' => $this->calculateTeamAverage('shots_on_target'), 'league_avg' => $this->calculateLeagueAverage('shots_on_target')],
                    'total_shots' => ['player' => $player->matchMetrics->sum('total_shots'), 'team_avg' => $this->calculateTeamAverage('total_shots'), 'league_avg' => $this->calculateLeagueAverage('total_shots')],
                    'shot_accuracy' => ['player' => $this->calculateShotAccuracy($player), 'team_avg' => $this->calculateTeamAverage('shot_accuracy'), 'league_avg' => $this->calculateLeagueAverage('shot_accuracy')],
                    'assists' => ['player' => $player->matchPerformances->sum('assists'), 'team_avg' => $this->calculateTeamAverage('assists'), 'league_avg' => $this->calculateLeagueAverage('assists')],
                    'key_passes' => ['player' => $player->matchMetrics->sum('key_passes'), 'team_avg' => $this->calculateTeamAverage('key_passes'), 'league_avg' => $this->calculateLeagueAverage('key_passes')],
                    'successful_crosses' => ['player' => $player->matchMetrics->sum('successful_crosses'), 'team_avg' => $this->calculateTeamAverage('successful_crosses'), 'league_avg' => $this->calculateLeagueAverage('successful_crosses')],
                    'successful_dribbles' => ['player' => $player->matchMetrics->sum('successful_dribbles'), 'team_avg' => $this->calculateTeamAverage('successful_dribbles'), 'league_avg' => $this->calculateLeagueAverage('successful_dribbles')]
                ],
                'physical' => [
                    'distance' => ['player' => $player->matchMetrics->sum('distance'), 'team_avg' => $this->calculateTeamAverage('distance'), 'league_avg' => $this->calculateLeagueAverage('distance')],
                    'max_speed' => ['player' => $player->matchMetrics->max('max_speed'), 'team_avg' => $this->calculateTeamAverage('max_speed'), 'league_avg' => $this->calculateLeagueAverage('max_speed')],
                    'avg_speed' => ['player' => $player->matchMetrics->avg('avg_speed'), 'team_avg' => $this->calculateTeamAverage('avg_speed'), 'league_avg' => $this->calculateLeagueAverage('avg_speed')],
                    'sprints' => ['player' => $player->matchMetrics->sum('sprints'), 'team_avg' => $this->calculateTeamAverage('sprints'), 'league_avg' => $this->calculateLeagueAverage('sprints')],
                    'accelerations' => ['player' => $player->matchMetrics->sum('accelerations'), 'team_avg' => $this->calculateTeamAverage('accelerations'), 'league_avg' => $this->calculateLeagueAverage('accelerations')],
                    'decelerations' => ['player' => $player->matchMetrics->sum('decelerations'), 'team_avg' => $this->calculateTeamAverage('decelerations'), 'league_avg' => $this->calculateLeagueAverage('decelerations')],
                    'direction_changes' => ['player' => $player->matchMetrics->sum('direction_changes'), 'team_avg' => $this->calculateTeamAverage('direction_changes'), 'league_avg' => $this->calculateLeagueAverage('direction_changes')],
                    'jumps' => ['player' => $player->matchMetrics->sum('jumps'), 'team_avg' => $this->calculateTeamAverage('jumps'), 'league_avg' => $this->calculateLeagueAverage('jumps')]
                ],
                'technical' => [
                    'pass_accuracy' => ['player' => $player->matchMetrics->avg('pass_accuracy'), 'team_avg' => $this->calculateTeamAverage('pass_accuracy'), 'league_avg' => $this->calculateLeagueAverage('pass_accuracy')],
                    'long_passes' => ['player' => $player->matchMetrics->sum('long_passes'), 'team_avg' => $this->calculateTeamAverage('long_passes'), 'league_avg' => $this->calculateLeagueAverage('long_passes')],
                    'crosses' => ['player' => $player->matchMetrics->sum('crosses'), 'team_avg' => $this->calculateTeamAverage('crosses'), 'league_avg' => $this->calculateLeagueAverage('crosses')],
                    'tackles' => ['player' => $player->matchMetrics->sum('tackles'), 'team_avg' => $this->calculateTeamAverage('tackles'), 'league_avg' => $this->calculateLeagueAverage('tackles')],
                    'interceptions' => ['player' => $player->matchMetrics->sum('interceptions'), 'team_avg' => $this->calculateTeamAverage('interceptions'), 'league_avg' => $this->calculateLeagueAverage('interceptions')],
                    'clearances' => ['player' => $player->matchMetrics->sum('clearances'), 'team_avg' => $this->calculateTeamAverage('clearances'), 'league_avg' => $this->calculateLeagueAverage('clearances')]
                ]
            ];
        } else {
            // Fallback aux données simulées si pas de métriques
            return [
                'attack' => [
                    'goals' => ['player' => $player->matchPerformances->sum('goals_scored'), 'team_avg' => rand(15, 25), 'league_avg' => rand(12, 20)],
                    'shots_on_target' => ['player' => rand(20, 40), 'team_avg' => rand(25, 35), 'league_avg' => rand(20, 30)],
                    'total_shots' => ['player' => rand(30, 60), 'team_avg' => rand(35, 45), 'league_avg' => rand(30, 40)],
                    'shot_accuracy' => ['player' => rand(60, 85), 'team_avg' => rand(65, 80), 'league_avg' => rand(60, 75)],
                    'assists' => ['player' => $player->matchPerformances->sum('assists'), 'team_avg' => rand(8, 15), 'league_avg' => rand(6, 12)],
                    'key_passes' => ['player' => rand(15, 30), 'team_avg' => rand(20, 35), 'league_avg' => rand(15, 25)],
                    'successful_crosses' => ['player' => rand(10, 25), 'team_avg' => rand(15, 30), 'league_avg' => rand(12, 22)],
                    'successful_dribbles' => ['player' => rand(20, 40), 'team_avg' => rand(25, 45), 'league_avg' => rand(20, 35)]
                ],
                'physical' => [
                    'distance' => ['player' => rand(250, 350), 'team_avg' => rand(280, 320), 'league_avg' => rand(260, 310)],
                    'max_speed' => ['player' => rand(30, 35), 'team_avg' => rand(32, 34), 'league_avg' => rand(31, 33)],
                    'avg_speed' => ['player' => rand(8, 12), 'team_avg' => rand(9, 11), 'league_avg' => rand(8, 10)],
                    'sprints' => ['player' => rand(15, 25), 'team_avg' => rand(18, 22), 'league_avg' => rand(16, 20)],
                    'accelerations' => ['player' => rand(20, 35), 'team_avg' => rand(25, 30), 'league_avg' => rand(22, 28)],
                    'decelerations' => ['player' => rand(15, 25), 'team_avg' => rand(18, 22), 'league_avg' => rand(16, 20)],
                    'direction_changes' => ['player' => rand(30, 50), 'team_avg' => rand(35, 45), 'league_avg' => rand(32, 42)],
                    'jumps' => ['player' => rand(5, 15), 'team_avg' => rand(8, 12), 'league_avg' => rand(6, 10)]
                ],
                'technical' => [
                    'pass_accuracy' => ['player' => rand(75, 95), 'team_avg' => rand(80, 90), 'league_avg' => rand(75, 85)],
                    'long_passes' => ['player' => rand(10, 25), 'team_avg' => rand(15, 30), 'league_avg' => rand(12, 22)],
                    'crosses' => ['player' => rand(8, 20), 'team_avg' => rand(12, 25), 'league_avg' => rand(10, 20)],
                    'tackles' => ['player' => rand(15, 35), 'team_avg' => rand(20, 40), 'league_avg' => rand(18, 35)],
                    'interceptions' => ['player' => rand(10, 25), 'team_avg' => rand(15, 30), 'league_avg' => rand(12, 25)],
                    'clearances' => ['player' => rand(5, 20), 'team_avg' => rand(8, 25), 'league_avg' => rand(6, 20)]
                ]
            ];
        }
    }
    
    /**
     * Préparer les progrès de saison
     */
    private function prepareSeasonProgress(Player $player): array
    {
        return [
            'currentSeason' => '2024-25',
            'matchesPlayed' => $player->matchPerformances->count(),
            'goalsScored' => $player->matchPerformances->sum('goals_scored'),
            'assists' => $player->matchPerformances->sum('assists'),
            'cleanSheets' => $player->matchPerformances->where('goals_conceded', 0)->count(),
            'yellowCards' => $player->matchPerformances->sum('yellow_cards'),
            'redCards' => $player->matchPerformances->sum('red_cards'),
            'completion' => 'N/A',
            'matchesRemaining' => 'N/A'
        ];
    }
    
    /**
     * Préparer les zones d'activité
     */
    private function prepareActivityZones(Player $player): array
    {
        return [
            'right_side_percentage' => 60,
            'left_side_percentage' => 20,
            'opponent_half_touches' => 15,
            'own_half_touches' => 25,
            'center_zone_touches' => 20,
            'wide_areas_touches' => 30
        ];
    }
    
    /**
     * Préparer les statistiques FIFA
     */
    private function prepareFifaStats(Player $player): array
    {
        return [
            'overall_rating' => $player->overall_rating ?? 'N/A',
            'potential_rating' => $player->potential_rating ?? 'N/A',
            'fitness_score' => 'N/A',
            'pace' => 'N/A',
            'shooting' => 'N/A',
            'passing' => 'N/A',
            'dribbling' => 'N/A',
            'defending' => 'N/A',
            'physical' => 'N/A'
        ];
    }
    
    /**
     * Déterminer le niveau de risque de blessure
     */
    private function getInjuryRiskLevel(int $injuryRisk): string
    {
        if ($injuryRisk <= 10) return 'FAIBLE';
        if ($injuryRisk <= 20) return 'MODÉRÉ';
        if ($injuryRisk <= 30) return 'ÉLEVÉ';
        return 'TRÈS ÉLEVÉ';
    }
    
    /**
     * Calculer le pourcentage de forme du joueur
     */
    private function calculateFormPercentage(Player $player): int
    {
        // Basé sur les performances récentes et la condition physique
        $baseForm = 70;
        
        // Utiliser les 5 derniers matchs au lieu d'une période fixe
        $recentMatches = $player->matchPerformances->sortByDesc('match_date')->take(5);
        $performanceBonus = min(20, $recentMatches->avg('rating') ?? 0);
        
        $healthBonus = min(10, $player->ghs_overall_score ?? 0);
        
        return min(100, $baseForm + $performanceBonus + $healthBonus);
    }
    
    /**
     * Calculer le pourcentage de moral du joueur
     */
    private function calculateMoralePercentage(Player $player): int
    {
        // Basé sur les performances récentes et les trophées
        $baseMorale = 75;
        
        // Utiliser les 5 derniers matchs au lieu d'une période fixe
        $recentMatches = $player->matchPerformances->sortByDesc('match_date')->take(5);
        $performanceBonus = min(15, $recentMatches->count() * 2);
        
        $trophyBonus = min(10, ($player->trophies->count() ?? 0) * 2);
        
        return min(100, $baseMorale + $performanceBonus + $trophyBonus);
    }
    
    /**
     * Calculer le niveau de forme physique
     */
    private function calculateFitnessLevel(Player $player): int
    {
        return $player->ghs_physical_score ?? rand(70, 95);
    }
    
    /**
     * Obtenir le niveau de forme physique en texte
     */
    private function getFitnessLevelText(int $fitnessScore): string
    {
        if ($fitnessScore >= 90) return 'EXCELLENT';
        if ($fitnessScore >= 80) return 'BON';
        if ($fitnessScore >= 70) return 'MOYEN';
        if ($fitnessScore >= 60) return 'FAIBLE';
        return 'TRÈS FAIBLE';
    }

    /**
     * Déterminer la disponibilité pour les matchs
     */
    private function getMatchAvailability(Player $player): string
    {
        $injuryRisk = $player->injury_risk ?? rand(5, 25);
        $fitnessScore = $player->ghs_physical_score ?? rand(70, 95);
        
        if ($injuryRisk > 30 || $fitnessScore < 60) return 'INDISPONIBLE';
        if ($injuryRisk > 20 || $fitnessScore < 75) return 'LIMITÉ';
        if ($injuryRisk > 10 || $fitnessScore < 85) return 'CONDITIONNEL';
        return 'DISPONIBLE';
    }

    /**
     * Préparer les réalisations et palmarès du joueur
     */
    private function prepareAchievements(Player $player): array
    {
        // Statistiques de carrière basées sur les performances réelles
        $totalGoals = $player->matchPerformances->sum('goals_scored');
        $totalAssists = $player->matchPerformances->sum('assists');
        $totalMatches = $player->matchPerformances->count();
        
        // Buts de la saison actuelle
        $seasonGoals = $player->matchPerformances
            ->where('match_date', '>=', now()->startOfYear())
            ->sum('goals_scored');
        
        // Titres et trophées (basés sur les performances)
        $leagueTitles = $this->calculateLeagueTitles($player);
        $championsLeague = $this->calculateChampionsLeague($player);
        $ballonDor = $this->calculateBallonDor($player);
        
        return [
            'total_goals' => $totalGoals,
            'total_assists' => $totalAssists,
            'total_matches' => $totalMatches,
            'season_goals' => $seasonGoals,
            'league_titles' => $leagueTitles,
            'champions_league' => $championsLeague,
            'ballon_dor' => $ballonDor,
            'career_rating' => $player->matchPerformances->count() > 0 ? round($player->matchPerformances->avg('rating'), 1) : 0,
            'win_percentage' => $this->calculateWinPercentage($player),
            'clean_sheets' => $this->calculateCleanSheets($player)
        ];
    }

    /**
     * Calculer les titres de ligue basés sur les performances
     */
    private function calculateLeagueTitles(Player $player): int
    {
        // Basé sur les performances exceptionnelles
        $exceptionalMatches = $player->matchPerformances
            ->where('rating', '>=', 8.5)
            ->count();
        
        return min(10, intval($exceptionalMatches / 20)); // 1 titre pour 20 matchs exceptionnels
    }

    /**
     * Calculer les titres Champions League basés sur les performances
     */
    private function calculateChampionsLeague(Player $player): string
    {
        $championsMatches = $player->matchPerformances
            ->where('rating', '>=', 9.0)
            ->count();
        
        if ($championsMatches >= 30) return '5x Champions League';
        if ($championsMatches >= 20) return '3x Champions League';
        if ($championsMatches >= 10) return 'Champions League';
        return 'Finaliste';
    }

    /**
     * Calculer les Ballons d'Or basés sur les performances
     */
    private function calculateBallonDor(Player $player): int
    {
        // Basé sur les performances exceptionnelles et la réputation
        $exceptionalMatches = $player->matchPerformances
            ->where('rating', '>=', 9.0)
            ->count();
        
        $reputation = $player->reputation ?? 0;
        
        return min(8, intval(($exceptionalMatches / 15) + ($reputation / 100)));
    }

    /**
     * Calculer le pourcentage de victoires
     */
    private function calculateWinPercentage(Player $player): int
    {
        $totalMatches = $player->matchPerformances->count();
        if ($totalMatches === 0) return 0;
        
        $wins = $player->matchPerformances
            ->where('result', 'W')
            ->count();
        
        return round(($wins / $totalMatches) * 100);
    }

    /**
     * Calculer les clean sheets (pour les défenseurs/gardiens)
     */
    private function calculateCleanSheets(Player $player): int
    {
        // Basé sur les matchs avec rating élevé et peu de buts encaissés
        return $player->matchPerformances
            ->where('rating', '>=', 8.0)
            ->where('goals_conceded', '<=', 1)
            ->count();
    }

    /**
     * Calculer la valeur marchande du joueur
     */
    private function calculateMarketValue(Player $player): array
    {
        // Base de la valeur marchande
        $baseValue = 10; // 10M€ de base
        
        // Facteurs de performance
        $age = $player->date_of_birth ? (int) $player->date_of_birth->diffInYears(now()) : 25;
        $ageMultiplier = $age <= 23 ? 1.5 : ($age <= 28 ? 1.0 : ($age <= 32 ? 0.8 : 0.5));
        
        // Performance récente
        $recentPerformance = $player->matchPerformances
            ->where('match_date', '>=', now()->subMonths(3))
            ->avg('rating') ?? 6.0;
        
        $performanceMultiplier = $recentPerformance >= 8.0 ? 2.0 : ($recentPerformance >= 7.0 ? 1.5 : ($recentPerformance >= 6.0 ? 1.0 : 0.7));
        
        // Réputation et potentiel
        $reputation = $player->reputation ?? 50;
        $potential = $player->potential_rating ?? 80;
        
        $reputationMultiplier = ($reputation / 100) * 1.5;
        $potentialMultiplier = ($potential / 100) * 1.2;
        
        // Calcul final
        $currentValue = round($baseValue * $ageMultiplier * $performanceMultiplier * $reputationMultiplier * $potentialMultiplier);
        
        // Variation mensuelle (basée sur la performance récente)
        $previousMonthPerformance = $player->matchPerformances
            ->where('match_date', '>=', now()->subMonths(4))
            ->where('match_date', '<', now()->subMonths(3))
            ->avg('rating') ?? 6.0;
        
        $performanceDiff = $recentPerformance - $previousMonthPerformance;
        $monthlyChange = round($performanceDiff * 5); // 5M€ par point de performance
        
        return [
            'current' => $currentValue,
            'change' => $monthlyChange,
            'trend' => $monthlyChange > 0 ? 'up' : ($monthlyChange < 0 ? 'down' : 'stable'),
            'currency' => '€',
            'unit' => 'M'
        ];
    }

    /**
     * Obtenir les informations sur le prochain match
     */
    private function getNextMatchInfo(Player $player): array
    {
        // Simuler un calendrier de matchs basé sur la position et le club
        $position = $player->position ?? 'FW';
        $club = $player->club?->name ?? 'Club';
        
        // Jours jusqu'au prochain match (basé sur la disponibilité)
        $availability = $this->getMatchAvailability($player);
        $daysUntilMatch = match($availability) {
            'DISPONIBLE' => rand(1, 3),
            'CONDITIONNEL' => rand(3, 7),
            'LIMITÉ' => rand(7, 14),
            'INDISPONIBLE' => rand(14, 30),
            default => rand(1, 7)
        };
        
        // Type de match basé sur la position
        $matchType = match($position) {
            'GK', 'DF' => 'Défense solide requise',
            'MF' => 'Contrôle du milieu',
            'FW' => 'Attaque décisive',
            default => 'Match important'
        };
        
        // Adversaire (basé sur le club)
        $opponents = ['Real Madrid', 'Barcelona', 'Manchester United', 'Liverpool', 'Bayern Munich', 'PSG'];
        $opponent = $opponents[array_rand($opponents)];
        
        // Date du prochain match
        $nextMatchDate = now()->addDays($daysUntilMatch);
        
        return [
            'days_until' => $daysUntilMatch,
            'date' => $nextMatchDate->format('d/m/Y'),
            'day_name' => $nextMatchDate->format('l'),
            'opponent' => $opponent,
            'type' => $matchType,
            'venue' => $daysUntilMatch <= 3 ? 'Domicile' : 'Extérieur',
            'competition' => $daysUntilMatch <= 7 ? 'Ligue 1' : 'Coupe'
        ];
    }

    /**
     * Préparer les données de performance pour les charts
     */
    private function preparePerformanceData(Player $player): array
    {
        // Données mensuelles pour le chart d'évolution
        $monthlyData = $this->getMonthlyPerformanceData($player);
        
        return [
            'monthly_ratings' => $monthlyData['ratings'],
            'monthly_goals' => $monthlyData['goals'],
            'monthly_assists' => $monthlyData['assists'],
            'monthly_distance' => $monthlyData['distance'],
            'trend_analysis' => $this->analyzePerformanceTrend($player)
        ];
    }

    /**
     * Obtenir les données de performance mensuelles
     */
    private function getMonthlyPerformanceData(Player $player): array
    {
        $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'];
        $ratings = [];
        $goals = [];
        $assists = [];
        $distance = [];

        foreach ($months as $index => $month) {
            $startDate = now()->startOfYear()->addMonths($index);
            $endDate = $startDate->copy()->endOfMonth();
            
            // Rating moyen du mois
            $monthRating = $player->matchPerformances
                ->whereBetween('match_date', [$startDate, $endDate])
                ->avg('rating') ?? 6.0;
            $ratings[] = round($monthRating, 1);
            
            // Buts du mois
            $monthGoals = $player->matchPerformances
                ->whereBetween('match_date', [$startDate, $endDate])
                ->sum('goals_scored');
            $goals[] = $monthGoals;
            
            // Passes du mois
            $monthAssists = $player->matchPerformances
                ->whereBetween('match_date', [$startDate, $endDate])
                ->sum('assists');
            $assists[] = $monthAssists;
            
            // Distance du mois (si métriques disponibles)
            if ($player->matchMetrics()->exists()) {
                $monthDistance = $player->matchMetrics()
                    ->whereHas('matchPerformance', function($query) use ($startDate, $endDate) {
                        $query->whereBetween('match_date', [$startDate, $endDate]);
                    })
                    ->sum('distance');
                $distance[] = round($monthDistance, 1);
            } else {
                $distance[] = rand(80, 120); // Fallback
            }
        }

        return [
            'ratings' => $ratings,
            'goals' => $goals,
            'assists' => $assists,
            'distance' => $distance
        ];
    }

    /**
     * Analyser la tendance de performance
     */
    private function analyzePerformanceTrend(Player $player): array
    {
        $recentMatches = $player->matchPerformances
            ->where('match_date', '>=', now()->subMonths(3))
            ->sortBy('match_date');

        if ($recentMatches->isEmpty()) {
            return ['trend' => 'stable', 'improvement' => 0, 'consistency' => 'N/A'];
        }

        $firstHalf = $recentMatches->take(ceil($recentMatches->count() / 2));
        $secondHalf = $recentMatches->slice(ceil($recentMatches->count() / 2));

        $firstHalfAvg = $firstHalf->avg('rating') ?? 6.0;
        $secondHalfAvg = $secondHalf->avg('rating') ?? 6.0;

        $improvement = round($secondHalfAvg - $firstHalfAvg, 2);
        
        $trend = $improvement > 0.5 ? 'up' : ($improvement < -0.5 ? 'down' : 'stable');
        
        $consistency = $this->calculateStandardDeviation($recentMatches->pluck('rating')) < 1.0 ? 'Élevée' : 
                      ($this->calculateStandardDeviation($recentMatches->pluck('rating')) < 2.0 ? 'Moyenne' : 'Faible');

        return [
            'trend' => $trend,
            'improvement' => $improvement,
            'consistency' => $consistency
        ];
    }

    /**
     * Calculer la précision des tirs
     */
    private function calculateShotAccuracy(Player $player): float
    {
        $totalShots = $player->matchMetrics->sum('total_shots');
        $shotsOnTarget = $player->matchMetrics->sum('shots_on_target');
        
        return $totalShots > 0 ? round(($shotsOnTarget / $totalShots) * 100, 1) : 0;
    }

    /**
     * Calculer la moyenne d'équipe pour une métrique
     */
    private function calculateTeamAverage(string $metric): float
    {
        // Simulation de moyenne d'équipe (à remplacer par vraie logique)
        return match($metric) {
            'goals_scored' => rand(15, 25),
            'assists' => rand(8, 15),
            'shots_on_target' => rand(25, 35),
            'total_shots' => rand(35, 45),
            'shot_accuracy' => rand(65, 80),
            'key_passes' => rand(20, 35),
            'successful_crosses' => rand(15, 30),
            'successful_dribbles' => rand(25, 45),
            'distance' => rand(280, 320),
            'max_speed' => rand(32, 34),
            'avg_speed' => rand(9, 11),
            'sprints' => rand(18, 22),
            'accelerations' => rand(25, 30),
            'decelerations' => rand(18, 22),
            'direction_changes' => rand(35, 45),
            'jumps' => rand(8, 12),
            'pass_accuracy' => rand(80, 90),
            'long_passes' => rand(15, 30),
            'crosses' => rand(12, 25),
            'tackles' => rand(20, 40),
            'interceptions' => rand(15, 30),
            'clearances' => rand(8, 25),
            default => rand(10, 50)
        };
    }

    /**
     * Calculer l'écart-type d'une collection de valeurs
     */
    private function calculateStandardDeviation($values): float
    {
        if ($values->isEmpty()) {
            return 0.0;
        }
        
        $mean = $values->avg();
        $variance = $values->map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        })->avg();
        
        return sqrt($variance);
    }

    /**
     * Calculer la moyenne de ligue pour une métrique
     */
    private function calculateLeagueAverage(string $metric): float
    {
        // Simulation de moyenne de ligue (à remplacer par vraie logique)
        return match($metric) {
            'goals_scored' => rand(12, 20),
            'assists' => rand(6, 12),
            'shots_on_target' => rand(20, 30),
            'total_shots' => rand(30, 40),
            'shot_accuracy' => rand(60, 75),
            'key_passes' => rand(15, 25),
            'successful_crosses' => rand(12, 22),
            'successful_dribbles' => rand(20, 35),
            'distance' => rand(260, 310),
            'max_speed' => rand(31, 33),
            'avg_speed' => rand(8, 10),
            'sprints' => rand(16, 20),
            'accelerations' => rand(22, 28),
            'decelerations' => rand(16, 20),
            'direction_changes' => rand(32, 42),
            'jumps' => rand(6, 10),
            'pass_accuracy' => rand(75, 85),
            'long_passes' => rand(12, 22),
            'crosses' => rand(10, 20),
            'tackles' => rand(18, 35),
            'interceptions' => rand(12, 25),
            'clearances' => rand(6, 20),
            default => rand(8, 40)
        };
    }
}




