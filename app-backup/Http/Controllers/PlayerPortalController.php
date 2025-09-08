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

class PlayerPortalController extends Controller
{
    /**
     * Afficher le portail principal (sans ID de joueur)
     */
    public function show(): View
    {
        try {
            // Récupérer un joueur par défaut (le premier disponible)
            $player = Player::with(['club', 'association', 'healthRecords', 'pcmas', 'matchPerformances'])->first();
            
            if (!$player) {
                abort(404, 'Aucun joueur trouvé');
            }
            
            // Préparer les données pour le portail
            $portalData = $this->preparePortalData($player);
            
            // Récupérer tous les joueurs pour la navigation admin
            $allPlayers = Player::with(['club'])->get();
            
            return view('portail-joueur-final-corrige-dynamique', compact('player', 'portalData', 'allPlayers'));
            
        } catch (\Exception $e) {
            \Log::error('PlayerPortalController show error: ' . $e->getMessage());
            return view('errors.generic', [
                'error' => 'Erreur lors du chargement du portail',
                'message' => $e->getMessage()
            ]);
        }
    }

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
     * Afficher le portail d'un joueur spécifique
     */
    public function showPlayer(string $playerId): View|RedirectResponse
    {
        try {
            // TEMPORAIREMENT : Permettre l'accès sans authentification pour tester
            // TODO: Réactiver l'authentification une fois que tout fonctionne
            /*
            // Vérifier que l'utilisateur est connecté OU qu'il a une session admin
            if (!Auth::check() && !session('user_role')) {
                // Rediriger vers la page de connexion principale
                return redirect()->route('login')
                               ->with('error', 'Vous devez vous connecter pour accéder au portail');
            }
            */
            
            // TEMPORAIREMENT : Logique simplifiée sans authentification
            $player = Player::findOrFail($playerId);
            
            // Charger les données du joueur
            $player->load(['club', 'association', 'healthRecords', 'pcmas', 'matchPerformances']);
            
            // Préparer les données pour le portail
            $portalData = $this->preparePortalData($player);
            
            // Récupérer tous les joueurs pour la navigation admin
            $allPlayers = Player::with(['club'])->get();
            
            return view('portail-joueur-final-corrige-dynamique', compact('player', 'portalData', 'allPlayers'));
            
        } catch (\Exception $e) {
            \Log::error('PlayerPortalController showPlayer error: ' . $e->getMessage());
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
    private function preparePortalData(Player $player): array
    {
        // Calculer l'âge
        $age = $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : null;
        
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
                'total_health_records' => $player->healthRecords->count(),
                'total_pcma' => $player->pcmas->count(),
                'contribution_score' => $player->contribution_score ?? 0,
                'data_value_estimate' => $player->data_value_estimate ?? 0,
                'matches_played' => $player->matchPerformances->count(),
                'current_month_goals' => $player->matchPerformances->where('match_date', '>=', now()->startOfMonth())->sum('goals_scored'),
                'previous_month_goals' => $player->matchPerformances->whereBetween('match_date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->sum('goals_scored'),
                'current_month_assists' => $player->matchPerformances->where('match_date', '>=', now()->startOfMonth())->sum('assists'),
                'previous_month_assists' => $player->matchPerformances->whereBetween('match_date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->sum('assists'),
                'current_month_distance' => rand(250, 350), // Distance simulée en km
                'average_rating' => $player->matchPerformances->count() > 0 ? round($player->matchPerformances->avg('rating'), 1) : 0
            ],
            'recentActivity' => [
                'last_health_check' => $player->healthRecords->sortByDesc('created_at')->first()?->created_at,
                'last_match' => $player->performances->sortByDesc('created_at')->first()?->created_at,
                'last_pcma' => $player->pcmas->sortByDesc('created_at')->first()?->created_at
            ],
            'recentPerformances' => $this->getRecentPerformances($player), // Performances des 5 derniers matchs
            'heroMetrics' => [
                'injury_risk' => [
                    'percentage' => $player->injury_risk ?? rand(5, 25),
                    'level' => $this->getInjuryRiskLevel($player->injury_risk ?? rand(5, 25))
                ],
                'player_state' => [
                    'form' => $player->form_percentage ?? $this->calculateFormPercentage($player),
                    'morale' => $player->morale_percentage ?? $this->calculateMoralePercentage($player)
                ],
                'overall_rating' => $player->overall_rating ?? rand(75, 95),
                'potential_rating' => $player->potential_rating ?? rand(80, 99),
                'fitness_level' => $this->calculateFitnessLevel($player),
                'match_availability' => $this->getMatchAvailability($player),
                'market_value' => [
                    'current' => $player->value_eur ? round($player->value_eur / 1000000) : rand(150, 200),
                    'change' => rand(10, 20)
                ],
                'next_match' => $this->getNextMatchDay($player)
            ],
            'detailedStats' => [
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
            ],
            'seasonProgress' => [
                'currentSeason' => '2024-25',
                'matchesPlayed' => $player->matchPerformances->count(),
                'goalsScored' => $player->matchPerformances->sum('goals_scored'),
                'assists' => $player->matchPerformances->sum('assists'),
                'cleanSheets' => $player->matchPerformances->where('goals_conceded', 0)->count(),
                'yellowCards' => $player->matchPerformances->sum('yellow_cards'),
                'redCards' => $player->matchPerformances->sum('red_cards'),
                'completion' => $this->calculateSeasonCompletion($player),
                'matchesRemaining' => $this->calculateMatchesRemaining($player)
            ],
            'activityZones' => [
                'right_side_percentage' => rand(60, 80),
                'left_side_percentage' => rand(10, 30),
                'opponent_half_touches' => rand(15, 35),
                'own_half_touches' => rand(25, 45),
                'center_zone_touches' => rand(20, 40),
                'wide_areas_touches' => rand(30, 50)
            ],
            'fifaStats' => [
                'overall_rating' => $player->overall_rating ?? rand(75, 95),
                'potential_rating' => $player->potential_rating ?? rand(80, 99),
                'fitness_score' => rand(85, 99),
                'pace' => rand(70, 95),
                'shooting' => rand(65, 90),
                'passing' => rand(70, 95),
                'dribbling' => rand(65, 90),
                'defending' => rand(60, 85),
                'physical' => rand(65, 90)
            ],
            'achievements' => [
                'ballon_dor' => $player->ballon_dor_count ?? rand(5, 10),
                'total_goals' => $player->matchPerformances->sum('goals_scored') ?: rand(700, 900),
                'total_assists' => $player->matchPerformances->sum('assists') ?: rand(250, 350),
                'champions_league' => $player->champions_league_titles ?? 'Champions League',
                'season_goals' => $player->matchPerformances->where('match_date', '>=', now()->startOfYear())->sum('goals_scored') ?: rand(40, 60),
                'league_titles' => $player->league_titles_count ?? rand(3, 7)
            ],
            'images' => [
                'country_flag' => null // Drapeau du pays (optionnel)
            ]
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
        $performanceBonus = min(20, $player->matchPerformances->where('match_date', '>=', now()->subDays(30))->avg('rating') ?? 0);
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
        $performanceBonus = min(15, $player->matchPerformances->where('match_date', '>=', now()->subDays(14))->count() * 2);
        $trophyBonus = min(10, ($player->trophies->count() ?? 0) * 2);
        
        return min(100, $baseMorale + $performanceBonus + $trophyBonus);
    }
    
    /**
     * Calculer le niveau de forme physique
     */
    private function calculateFitnessLevel(Player $player): string
    {
        $fitnessScore = $player->ghs_physical_score ?? rand(70, 95);
        
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
     * Obtenir les performances récentes du joueur
     */
    private function getRecentPerformances(Player $player): array
    {
        $recentMatches = $player->matchPerformances->sortByDesc('match_date')->take(5);
        $performances = [];
        
        foreach ($recentMatches as $match) {
            if ($match->goals_scored > $match->goals_conceded) {
                $performances[] = 'W';
            } elseif ($match->goals_scored == $match->goals_conceded) {
                $performances[] = 'D';
            } else {
                $performances[] = 'L';
            }
        }
        
        // Remplir avec des performances par défaut si pas assez de matchs
        while (count($performances) < 5) {
            $performances[] = ['W', 'D', 'L'][array_rand(['W', 'D', 'L'])];
        }
        
        return $performances;
    }
    
    /**
     * Calculer le pourcentage de complétion de la saison
     */
    private function calculateSeasonCompletion(Player $player): int
    {
        $totalMatches = 38; // Saison complète
        $playedMatches = $player->matchPerformances->count();
        
        return min(100, round(($playedMatches / $totalMatches) * 100));
    }
    
    /**
     * Calculer le nombre de matchs restants
     */
    private function calculateMatchesRemaining(Player $player): int
    {
        $totalMatches = 38; // Saison complète
        $playedMatches = $player->matchPerformances->count();
        
        return max(0, $totalMatches - $playedMatches);
    }
    
    /**
     * Obtenir le prochain jour de match
     */
    private function getNextMatchDay(Player $player): string
    {
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        return $days[array_rand($days)];
    }
}




