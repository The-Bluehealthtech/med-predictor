<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\HealthRecord;
use App\Models\PCMA;
use App\Models\MedicalPrediction;
use App\Models\Club;
use App\Models\Association;
use App\Models\MatchModel;
use App\Models\Performance;
use App\Services\PlayerPerformanceService;
use App\Services\PlayerNotificationService;
use App\Services\PlayerHealthService;
use App\Services\PlayerMedicalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PlayerPortalController extends Controller
{
    public function __construct()
    {
        // Middleware plus flexible pour permettre l'accès aux admins via session
        $this->middleware(function ($request, $next) {
            // Debug: Vérifier l'état de l'authentification
            \Log::info('PlayerPortalController Middleware - Debug:', [
                'auth_check' => auth()->check(),
                'user_role' => session('user_role'),
                'session_id' => session()->getId(),
                'url' => $request->url()
            ]);
            
            // Vérifier si l'utilisateur est connecté OU s'il a une session admin
            if (auth()->check() || session('user_role') === 'admin') {
                \Log::info('PlayerPortalController Middleware - Accès autorisé');
                return $next($request);
            }
            
            \Log::info('PlayerPortalController Middleware - Accès refusé, redirection vers login');
            // Si pas d'authentification, rediriger vers la connexion
            return redirect()->route('login')
                           ->with('error', 'Vous devez vous connecter pour accéder au portail');
        });
    }

    /**
     * Dashboard principal du joueur
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $player = Player::with(['club', 'association'])->findOrFail($user->player_id);
        
        return view('player.dashboard', compact('player'));
    }

    /**
     * Afficher le portail du joueur
     */
    public function show($playerId = null): View|RedirectResponse
    {
        // Si aucun ID n'est fourni, utiliser l'utilisateur connecté
        if (!$playerId) {
            if (!Auth::check()) {
                return redirect()->route('joueurs.selection')
                               ->with('error', 'Veuillez sélectionner un joueur ou vous connecter');
            }

            $user = Auth::user();
            if (!$user->player_id) {
                return redirect()->route('joueurs.selection')
                               ->with('error', 'Aucun joueur associé à votre compte');
            }
            
            $playerId = $user->player_id;
        }

        try {
            // Récupérer le joueur avec toutes ses relations
            $player = Player::with([
                'club', 
                'association', 
                'healthRecords', 
                'performances', 
                'pcmas',
                'nationalTeamCallups',
                'trainingSessions',
                'medicalAppointments',
                'socialMediaAlerts',
                'matchPerformances',
                'trophies'
            ])->findOrFail($playerId);

            // Préparer les données pour le portail
            $portalData = $this->preparePortalData($player);
            
            // Debug: Vérifier la structure des données
            \Log::info('PlayerPortalController - Structure de portalData:', [
                'keys' => array_keys($portalData),
                'has_performanceStats' => isset($portalData['performanceStats']),
                'has_activityZones' => isset($portalData['activityZones'])
            ]);

            // Récupérer tous les joueurs pour la navigation admin
            $allPlayers = Player::with(['club'])->get();
                
            // Utiliser notre fichier corrigé avec les données dynamiques
            return view('portail-joueur-final-corrige-dynamique', compact('player', 'portalData', 'allPlayers'));

        } catch (\Exception $e) {
            \Log::error('PlayerPortalController show error: ' . $e->getMessage());
            return redirect()->route('player.access.form', ['playerId' => 2])
                           ->with('error', 'Erreur lors du chargement du portail');
        }
    }

    /**
     * Préparer les données pour le portail
     */
    private function preparePortalData(Player $player): array
    {
        // Calculer l'âge
        $age = $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : null;
        
        // Construire le tableau étape par étape
        $portalData = [
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
            'recentActivity' => [
                'last_health_check' => $player->healthRecords->sortByDesc('created_at')->first()?->created_at,
                'last_match' => $player->performances->sortByDesc('created_at')->first()?->created_at,
                'last_pcma' => $player->pcmas->sortByDesc('created_at')->first()?->created_at
            ],
            'recentPerformances' => $player->matchPerformances
                ->sortByDesc('match_date')
                ->take(5)
                ->map(function($performance) {
                    // Retourner juste le résultat pour l'affichage simple
                    return $performance->result ?? 'N';
                })
                ->values(),
            'playerStats' => [
                'age' => $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : null,
                'height' => $player->height,
                'weight' => $player->weight,
                'preferred_foot' => $player->preferred_foot,
                'total_goals' => $player->matchPerformances->sum('goals_scored'),
                'total_assists' => $player->matchPerformances->sum('assists'),
                'season_goals' => $player->matchPerformances->where('match_date', '>=', now()->startOfYear())->sum('goals_scored'),
                'season_assists' => $player->matchPerformances->where('match_date', '>=', now()->startOfYear())->sum('assists'),
                'trophy_count' => $player->trophies->count(),
                'ballon_dor_count' => $player->trophies->where('trophy_name', 'Ballon d\'Or')->count(),
                'champions_league_count' => $player->trophies->where('trophy_name', 'Champions League')->count()
            ],
            'images' => [
                'player_profile' => $player->profile_image ?? $player->player_picture ?? 'https://via.placeholder.com/200x200/cccccc/666666?text=Joueur',
                'player_flag' => $player->flag_image ?? $player->nation_flag_url ?? 'https://flagcdn.com/w80/fr.png',
                'country_flag' => $player->flag_image ?? $player->nation_flag_url ?? 'https://flagcdn.com/w80/fr.png',
                'club_logo' => $player->club?->logo_image ?? $player->club?->logo_url ?? 'https://via.placeholder.com/200x200/cccccc/666666?text=Club',
                'club_stadium' => $player->club?->stadium_image ?? 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=300&fit=crop',
                'association_logo' => $player->association?->logo_image ?? $player->association?->logo_url ?? 'https://via.placeholder.com/200x200/cccccc/666666?text=Association',
                'association_flag' => $player->association?->flag_image ?? 'https://flagcdn.com/w80/fr.png'
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
            'performanceData' => [
                'monthly_ratings' => $this->generateMonthlyRatings($player),
                'monthly_goals' => $this->generateMonthlyGoals($player),
                'monthly_assists' => $this->generateMonthlyAssists($player),
                'monthly_distance' => $this->generateMonthlyDistance($player),
                'monthly_form' => $this->generateMonthlyForm($player)
            ],
            'sdohData' => [
                'environment' => $this->generateSDOHScore($player, 'environment'),
                'social_support' => $this->generateSDOHScore($player, 'social_support'),
                'healthcare_access' => $this->generateSDOHScore($player, 'healthcare_access'),
                'financial_situation' => $this->generateSDOHScore($player, 'financial_situation'),
                'mental_wellbeing' => $this->generateSDOHScore($player, 'mental_wellbeing'),
                'fifa_average' => [
                    'environment' => rand(65, 85),
                    'social_support' => rand(60, 80),
                    'healthcare_access' => rand(70, 90),
                    'financial_situation' => rand(70, 85),
                    'mental_wellbeing' => rand(65, 80)
                ]
            ]
        ];

        // Ajouter les données manquantes pour la vue
        $portalData['seasonProgress'] = [
            'currentSeason' => '2024-25',
            'matchesPlayed' => $player->matchPerformances->count(),
            'goalsScored' => $player->matchPerformances->sum('goals_scored'),
            'assists' => $player->matchPerformances->sum('assists'),
            'cleanSheets' => $player->matchPerformances->where('goals_conceded', 0)->count(),
            'yellowCards' => $player->matchPerformances->sum('yellow_cards'),
            'redCards' => $player->matchPerformances->sum('red_cards')
        ];

        // Ajouter les statistiques de performance mensuelles
        $portalData['performanceStats'] = [
            'total_matches' => $player->matchPerformances->count(),
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
        ];

        // Ajouter les métriques héro manquantes
        $portalData['heroMetrics'] = [
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
            'match_availability' => $this->getMatchAvailability($player)
        ];

        // Ajouter les statistiques détaillées
        $portalData['detailedStats'] = [
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

        // Ajouter les données de progression de saison manquantes
        $portalData['seasonProgress']['completion'] = rand(60, 90);
        $portalData['seasonProgress']['matchesRemaining'] = rand(5, 15);

        // Ajouter les zones d'activité manquantes
        $portalData['activityZones'] = [
            'right_side_percentage' => rand(60, 80),
            'left_side_percentage' => rand(10, 30),
            'opponent_half_touches' => rand(15, 35),
            'own_half_touches' => rand(25, 45),
            'center_zone_touches' => rand(20, 40),
            'wide_areas_touches' => rand(30, 50),
            'preferred_zone' => 'Côté Droit',
            'touches_per_match' => rand(40, 60),
            'dribble_success_rate' => rand(65, 85)
        ];

        // Debug: Vérifier la structure finale
        \Log::info('PlayerPortalController - Structure finale de portalData:', [
            'keys' => array_keys($portalData),
            'has_performanceStats' => isset($portalData['performanceStats']),
            'performanceStats_keys' => isset($portalData['performanceStats']) ? array_keys($portalData['performanceStats']) : 'N/A'
        ]);

        return $portalData;
    }

    private function generateMonthlyRatings(Player $player): array
    {
        $baseRating = $player->overall_rating ?? 75;
        $ratings = [];
        
        for ($i = 0; $i < 6; $i++) {
            // Variation réaliste pour les ratings FIFA (0-100)
            $variation = rand(-8, 8); // ±8 points
            $rating = max(50, min(100, $baseRating + $variation));
            $ratings[] = $rating;
        }
        
        return $ratings;
    }

    private function generateMonthlyGoals(Player $player): array
    {
        $totalGoals = $player->matchPerformances->sum('goals_scored');
        $goals = [];
        
        for ($i = 0; $i < 6; $i++) {
            // Distribution réaliste des buts sur 6 mois
            $monthlyGoals = rand(0, max(1, intval($totalGoals / 6) + 1));
            $goals[] = $monthlyGoals;
        }
        
        return $goals;
    }

    private function generateMonthlyAssists(Player $player): array
    {
        $totalAssists = $player->matchPerformances->sum('assists');
        $assists = [];
        
        for ($i = 0; $i < 6; $i++) {
            // Distribution réaliste des assists sur 6 mois
            $monthlyAssists = rand(0, max(1, intval($totalAssists / 6) + 1));
            $assists[] = $monthlyAssists;
        }
        
        return $assists;
    }

    private function generateMonthlyDistance(Player $player): array
    {
        $baseDistance = rand(150, 300); // km par mois
        $distances = [];
        
        for ($i = 0; $i < 6; $i++) {
            // Variation réaliste de la distance
            $variation = rand(-20, 20) / 100; // ±20%
            $distance = max(100, $baseDistance * (1 + $variation));
            $distances[] = round($distance);
        }
        
        return $distances;
    }

    private function generateMonthlyForm(Player $player): array
    {
        $baseForm = $player->form_percentage ?? 80;
        $form = [];
        
        for ($i = 0; $i < 6; $i++) {
            // Variation réaliste de la forme
            $variation = rand(-15, 15) / 100; // ±15%
            $monthlyForm = max(60, min(100, $baseForm * (1 + $variation)));
            $form[] = round($monthlyForm);
        }
        
        return $form;
    }

    private function generateSDOHScore(Player $player, string $factor): int
    {
        // Base sur les attributs du joueur et génération aléatoire réaliste
        $baseScore = match($factor) {
            'environment' => $player->ghs_civic_score ?? rand(60, 90),
            'social_support' => $player->pcmas->count() > 0 ? min(100, $player->pcmas->count() * 8 + rand(20, 40)) : rand(50, 80),
            'healthcare_access' => $player->healthRecords->count() > 0 ? min(100, $player->healthRecords->count() * 10 + rand(30, 50)) : rand(70, 95),
            'financial_situation' => $player->market_value ? min(100, ($player->market_value / 200) * 100 + rand(-10, 10)) : rand(60, 90),
            'mental_wellbeing' => $player->ghs_sleep_score ?? rand(65, 85),
            default => rand(60, 85)
        };
        
        // Ajouter une variation pour éviter les valeurs statiques
        $variation = rand(-8, 8);
        return max(40, min(100, $baseScore + $variation));
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
}
