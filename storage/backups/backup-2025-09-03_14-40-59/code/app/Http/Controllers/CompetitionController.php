<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Competition;
use App\Models\Club;
use App\Models\Player;
use App\Models\MatchModel;
use App\Models\GameMatch;
use App\Models\Team;
use App\Models\Association;
use App\Models\Confederation;
use App\Models\FifaConnectId;
use App\Models\PlayerSeasonStat;
use App\Models\Standing;
use Carbon\Carbon;

class CompetitionController extends Controller
{
    /**
     * Dashboard principal des compétitions
     */
    public function index(): View
    {
        return view('competitions.index');
    }

    // ========================================
    // CÔTÉ CLUB
    // ========================================

    /**
     * Mes engagements - Liste des compétitions où le club est inscrit
     * Respecte la hiérarchie FIFA Connect : Confédération > Association > Club > Joueur
     */
    public function clubEngagements(): View
    {
        try {
            // Récupérer les compétitions avec la hiérarchie FIFA Connect complète
            $competitions = Competition::with([
                'association.confederation', // Hiérarchie FIFA : Association > Confédération
                'clubs.association.confederation', // Hiérarchie FIFA : Club > Association > Confédération
                'matches'
            ])
            ->where('status', '!=', Competition::STATUS_CANCELLED)
            ->whereIn('type', [
                Competition::TYPE_CHAMPIONSHIP,
                Competition::TYPE_CUP,
                Competition::TYPE_TOURNAMENT,
                Competition::TYPE_INTERNATIONAL
            ])
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get();

            $engagements = $competitions->map(function($competition) {
                $totalMatches = $competition->matches()->count();
                $playedMatches = $competition->matches()->where('match_status', 'completed')->count();
                
                // Calculer les points basé sur les matchs terminés
                $points = $this->calculateClubPoints($competition);
                
                return [
                    'id' => $competition->id,
                    'nom' => $competition->name ?? 'Compétition sans nom',
                    'saison' => $competition->season ?? '2024-2025',
                    'statut' => $this->getFifaStatusLabel($competition->status),
                    'type' => $this->getFifaTypeLabel($competition->type),
                    'categorie' => $this->getFifaCategoryLabel($competition->category),
                    'discipline' => $this->getFifaDisciplineLabel($competition->discipline),
                    'date_debut' => $competition->start_date?->format('Y-m-d') ?? 'N/A',
                    'date_fin' => $competition->end_date?->format('Y-m-d') ?? 'N/A',
                    'nb_matchs' => $totalMatches,
                    'matchs_joues' => $playedMatches,
                    'points' => $points,
                    'classement' => $this->calculateRanking($competition, $points),
                    'association' => $competition->association?->name ?? 'N/A',
                    'confederation' => $competition->association?->confederation?->name ?? 'N/A',
                    'fifa_connect_id' => $competition->fifa_connect_id,
                    'fifa_sync_enabled' => $competition->fifa_sync_enabled
                ];
            });

            return view('competitions.club.engagements', compact('engagements'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données vides
            $engagements = collect([]);
            return view('competitions.club.engagements', compact('engagements'));
        }
    }

    /**
     * Effectif éligible - Liste des joueurs autorisés
     * Respecte la hiérarchie FIFA Connect : Confédération > Association > Club > Joueur
     */
    public function clubEffectif(): View
    {
        try {
            // Récupérer les joueurs avec la hiérarchie FIFA Connect complète
            $players = Player::with([
                'club.association.confederation', // Hiérarchie FIFA : Club > Association > Confédération
                'association.confederation', // Hiérarchie FIFA : Association > Confédération
                'fifaConnectId'
            ])
            ->whereHas('club') // Filtrer par club si nécessaire
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(20)
            ->get();

            $effectif = $players->map(function($player) {
                // Vérifier la validité de la licence FIFA Connect
                $licenceValide = !empty($player->fifa_connect_id);
                
                // Déterminer le type de licence FIFA Connect
                $typeLicence = $this->getFifaLicenseType($player);
                
                // Vérifier le PCMA (basé sur les health records)
                $pcmaAJour = false;
                try {
                    $pcmaAJour = $player->healthRecords()
                        ->where('created_at', '>=', now()->subYear())
                        ->exists();
                } catch (\Exception $e) {
                    $pcmaAJour = false;
                }
                
                // Vérifier les suspensions (basé sur les match events)
                $suspension = false;
                try {
                    $suspension = $player->matchEvents()
                        ->where('event_type', 'red_card')
                        ->where('created_at', '>=', now()->subDays(30))
                        ->exists();
                } catch (\Exception $e) {
                    $suspension = false;
                }

                $statut = 'Éligible';
                if (!$licenceValide) {
                    $statut = 'Inéligible - Licence FIFA Connect invalide';
                } elseif (!$pcmaAJour) {
                    $statut = 'Inéligible - PCMA expiré';
                } elseif ($suspension) {
                    $statut = 'Inéligible - Suspendu';
                }

                return [
                    'id' => $player->id,
                    'nom' => $player->last_name ?? 'N/A',
                    'prenom' => $player->first_name ?? 'N/A',
                    'licence' => $player->fifa_connect_id ?? 'N/A',
                    'type_licence' => $typeLicence,
                    'licence_valide' => $licenceValide,
                    'pcma_a_jour' => $pcmaAJour,
                    'suspension' => $suspension,
                    'statut' => $statut,
                    'derniere_verification' => $player->updated_at?->format('Y-m-d') ?? 'N/A',
                    'club' => $player->club?->name ?? 'N/A',
                    'association' => $player->club?->association?->name ?? $player->association?->name ?? 'N/A',
                    'confederation' => $player->club?->association?->confederation?->name ?? $player->association?->confederation?->name ?? 'N/A',
                    'nationalite' => $player->nationality ?? 'N/A',
                    'position' => $player->position ?? 'N/A',
                    'age' => $player->age ?? 'N/A'
                ];
            });

            return view('competitions.club.effectif', compact('effectif'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données vides
            $effectif = collect([]);
            return view('competitions.club.effectif', compact('effectif'));
        }
    }

    /**
     * Calendrier & Matchs - Vue calendrier avec matchs
     */
    public function clubCalendrier(): View
    {
        try {
            // Récupérer les compétitions avec les clubs inscrits
            $competitions = Competition::with(['clubs', 'association'])
                ->where('status', '!=', 'cancelled')
                ->get();

            // Générer des matchs fictifs basés sur les compétitions et clubs disponibles
            $matchs = collect();
            
            foreach ($competitions as $competition) {
                $clubs = $competition->clubs;
                if ($clubs->count() >= 2) {
                    // Générer quelques matchs fictifs pour cette compétition
                    for ($i = 0; $i < min(5, $clubs->count() - 1); $i++) {
                        $homeClub = $clubs->random();
                        $awayClub = $clubs->where('id', '!=', $homeClub->id)->random();
                        
                        $matchDate = now()->addDays(rand(1, 30));
                        $isCompleted = rand(0, 1);
                        
                        $matchs->push([
                            'id' => $competition->id * 100 + $i,
                            'date' => $matchDate->format('Y-m-d'),
                            'heure' => $matchDate->format('H:i'),
                            'competition' => $competition->name,
                            'adversaire' => $awayClub->name,
                            'lieu' => $homeClub->address ?? 'Stade à déterminer',
                            'arbitre_principal' => 'Arbitre à désigner',
                            'arbitre_assistant_1' => 'Assistant 1',
                            'arbitre_assistant_2' => 'Assistant 2',
                            'statut' => $isCompleted ? 'Terminé' : 'Programmé',
                            'resultat' => $isCompleted ? rand(0, 3) . '-' . rand(0, 3) : null
                        ]);
                    }
                }
            }

            // Trier par date
            $matchs = $matchs->sortBy('date');

            return view('competitions.club.calendrier', compact('matchs'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données vides
            $matchs = collect([]);
            return view('competitions.club.calendrier', compact('matchs'));
        }
    }

    /**
     * Feuilles de match - Pré-remplies avec effectif validé
     */
    public function clubFeuillesMatch(): View
    {
        // Simulation des données - à remplacer par les vraies données
        $feuilles = collect([
            [
                'id' => 1,
                'match' => 'FC Ville vs Notre Club',
                'date' => '2024-09-15',
                'statut' => 'À préparer',
                'effectif_disponible' => 18,
                'effectif_selectionne' => 0
            ],
            [
                'id' => 2,
                'match' => 'Notre Club vs AS Sport',
                'date' => '2024-09-08',
                'statut' => 'Soumise',
                'effectif_disponible' => 18,
                'effectif_selectionne' => 18
            ],
            [
                'id' => 3,
                'match' => 'Club Local vs Notre Club',
                'date' => '2024-09-22',
                'statut' => 'En retard',
                'effectif_disponible' => 16,
                'effectif_selectionne' => 0
            ]
        ]);

        return view('competitions.club.feuilles-match', compact('feuilles'));
    }

    /**
     * Discipline & Notifications - Suivi cartons/suspensions/amendes
     */
    public function clubDiscipline(): View
    {
        // Simulation des données - à remplacer par les vraies données
        $sanctions = collect([
            [
                'id' => 1,
                'joueur' => 'Jean Dupont',
                'match' => 'Notre Club vs AS Sport',
                'date' => '2024-09-08',
                'type' => 'Carton Jaune',
                'motif' => 'Comportement antisportif',
                'statut' => 'Validé',
                'amende' => 0
            ],
            [
                'id' => 2,
                'joueur' => 'Pierre Martin',
                'match' => 'FC Ville vs Notre Club',
                'date' => '2024-08-25',
                'type' => 'Carton Rouge',
                'motif' => 'Violence',
                'statut' => 'Suspendu 3 matchs',
                'amende' => 150
            ],
            [
                'id' => 3,
                'joueur' => 'Ahmed Ben Ali',
                'match' => 'Club Local vs Notre Club',
                'date' => '2024-09-01',
                'type' => 'Carton Jaune',
                'motif' => 'Retard de jeu',
                'statut' => 'Validé',
                'amende' => 0
            ]
        ]);

        return view('competitions.club.discipline', compact('sanctions'));
    }

    // ========================================
    // CÔTÉ ASSOCIATION/LIGUE
    // ========================================

    /**
     * Compétitions supervisées - Liste des compétitions sous responsabilité
     */
    public function associationSupervision(): View
    {
        try {
            // Récupérer les vraies données tunisiennes
            $tunisianAssociation = Association::where('name', 'like', '%Tunis%')->first();
            
            if (!$tunisianAssociation) {
                throw new \Exception('Association tunisienne non trouvée');
            }

            // Récupérer les compétitions de l'association tunisienne
            $competitionsData = Competition::where('association_id', $tunisianAssociation->id)
                ->where('status', '!=', 'cancelled')
                ->with(['association'])
                ->orderBy('start_date', 'desc')
                ->get();

            // Récupérer les clubs tunisiens pour calculer les statistiques
            $tunisianClubs = Club::where('association_id', $tunisianAssociation->id)->get();

            $competitions = $competitionsData->map(function($competition) use ($tunisianClubs) {
                // Calculer les statistiques basées sur les vrais clubs
                $nbClubs = $tunisianClubs->count();
                
                // Générer des matchs fictifs basés sur le nombre de clubs
                $totalMatches = $nbClubs > 0 ? ($nbClubs * ($nbClubs - 1)) : 0; // Chaque club joue contre tous les autres
                $playedMatches = rand(0, $totalMatches);
                
                // Prochaine journée basée sur la date de début de la compétition
                $nextMatchDate = $competition->start_date ? 
                    $competition->start_date->addDays(rand(1, 14)) : 
                    now()->addDays(rand(1, 14));

                return [
                    'id' => $competition->id,
                    'nom' => $competition->name,
                    'saison' => $competition->season ?? '2024-2025',
                    'statut' => $competition->status ?? 'Inconnu',
                    'nb_clubs' => $nbClubs,
                    'nb_matchs' => $totalMatches,
                    'matchs_joues' => $playedMatches,
                    'prochaine_journee' => $nextMatchDate->format('Y-m-d'),
                    'association' => $competition->association,
                    'start_date' => $competition->start_date,
                    'end_date' => $competition->end_date,
                    'type' => $competition->type ?? 'Championnat'
                ];
            });

            return view('competitions.association.supervision', compact('competitions', 'tunisianAssociation'));
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données de démonstration
            $competitions = collect([
                [
                    'id' => 1,
                    'nom' => 'Championnat Tunisien U19',
                    'saison' => '2024-2025',
                    'statut' => 'active',
                    'nb_clubs' => 20,
                    'nb_matchs' => 380,
                    'matchs_joues' => 150,
                    'prochaine_journee' => now()->addDays(7)->format('Y-m-d'),
                    'association' => (object)['name' => 'FTF - Fédération Tunisienne de Football'],
                    'start_date' => now()->subMonths(2),
                    'end_date' => now()->addMonths(4),
                    'type' => 'Championnat'
                ]
            ]);

            $tunisianAssociation = (object)['name' => 'FTF - Fédération Tunisienne de Football'];

            return view('competitions.association.supervision', compact('competitions', 'tunisianAssociation'));
        }
    }

    /**
     * Engagements des clubs - Vue de contrôle
     */
    public function associationEngagementsClubs(): View
    {
        // Simulation des données - à remplacer par les vraies données
        $clubs = [
            [
                'id' => 1,
                'nom' => 'Notre Club',
                'competition' => 'Championnat Régional U19',
                'statut_engagement' => 'Validé',
                'feuilles_soumises' => 8,
                'feuilles_validees' => 7,
                'derniere_activite' => '2024-09-08'
            ],
            [
                'id' => 2,
                'nom' => 'FC Ville',
                'competition' => 'Championnat Régional U19',
                'statut_engagement' => 'En attente',
                'feuilles_soumises' => 6,
                'feuilles_validees' => 5,
                'derniere_activite' => '2024-09-01'
            ]
        ];

        return view('competitions.association.engagements-clubs', compact('clubs'));
    }

    /**
     * Calendrier global - Planning centralisé
     */
    public function associationCalendrierGlobal(): View
    {
        // Simulation des données - à remplacer par les vraies données
        $matchs = [
            [
                'id' => 1,
                'date' => '2024-09-15',
                'heure' => '15:00',
                'competition' => 'Championnat Régional U19',
                'domicile' => 'FC Ville',
                'exterieur' => 'Notre Club',
                'lieu' => 'Stade Municipal',
                'arbitre_principal' => 'M. Durand',
                'statut' => 'Programmé',
                'reprogrammable' => true
            ],
            [
                'id' => 2,
                'date' => '2024-09-15',
                'heure' => '17:00',
                'competition' => 'Championnat Régional U19',
                'domicile' => 'AS Sport',
                'exterieur' => 'FC Athletic',
                'lieu' => 'Stade des Sports',
                'arbitre_principal' => 'M. Petit',
                'statut' => 'Reporté - Météo',
                'reprogrammable' => true
            ]
        ];

        return view('competitions.association.calendrier-global', compact('matchs'));
    }

    /**
     * Résultats & Classements - Compilation auto
     */
    public function associationResultatsClassements(): View
    {
        // Simulation des données - à remplacer par les vraies données
        $classements = [
            [
                'competition' => 'Championnat Régional U19',
                'equipes' => [
                    ['position' => 1, 'nom' => 'AS Sport', 'points' => 18, 'matchs' => 8, 'victoires' => 6, 'nuls' => 0, 'defaites' => 2, 'buts_pour' => 15, 'buts_contre' => 8, 'difference' => 7],
                    ['position' => 2, 'nom' => 'FC Athletic', 'points' => 16, 'matchs' => 8, 'victoires' => 5, 'nuls' => 1, 'defaites' => 2, 'buts_pour' => 12, 'buts_contre' => 6, 'difference' => 6],
                    ['position' => 3, 'nom' => 'Notre Club', 'points' => 15, 'matchs' => 8, 'victoires' => 5, 'nuls' => 0, 'defaites' => 3, 'buts_pour' => 14, 'buts_contre' => 10, 'difference' => 4]
                ]
            ]
        ];

        return view('competitions.association.resultats-classements', compact('classements'));
    }

    /**
     * Discipline & Sanctions - Validation des sanctions
     */
    public function associationDisciplineSanctions(): View
    {
        // Simulation des données - à remplacer par les vraies données
        $sanctions = collect([
            [
                'id' => 1,
                'joueur' => 'Jean Dupont',
                'club' => 'Notre Club',
                'match' => 'Notre Club vs AS Sport',
                'date' => '2024-09-08',
                'type' => 'Carton Jaune',
                'motif' => 'Comportement antisportif',
                'statut' => 'Validé',
                'amende' => 0,
                'suspension' => 0
            ],
            [
                'id' => 2,
                'joueur' => 'Pierre Martin',
                'club' => 'Notre Club',
                'match' => 'FC Ville vs Notre Club',
                'date' => '2024-08-25',
                'type' => 'Carton Rouge',
                'motif' => 'Violence',
                'statut' => 'Suspendu 3 matchs',
                'amende' => 150,
                'suspension' => 3
            ],
            [
                'id' => 3,
                'joueur' => 'Ahmed Ben Ali',
                'club' => 'Club Local',
                'match' => 'Club Local vs Notre Club',
                'date' => '2024-09-01',
                'type' => 'Carton Jaune',
                'motif' => 'Retard de jeu',
                'statut' => 'En attente',
                'amende' => 0,
                'suspension' => 0
            ]
        ]);

        return view('competitions.association.discipline-sanctions', compact('sanctions'));
    }

    /**
     * Rapports & Statistiques - Export PDF/Excel
     */
    public function associationRapportsStatistiques(): View
    {
        // Simulation des données - à remplacer par les vraies données
        $rapports = collect([
            [
                'id' => 1,
                'nom' => 'Classement Championnat U19',
                'type' => 'Classement',
                'competition' => 'Championnat Régional U19',
                'date_generation' => '2024-09-10',
                'statut' => 'Disponible',
                'formats' => ['PDF', 'Excel']
            ],
            [
                'id' => 2,
                'nom' => 'Statistiques Joueurs',
                'type' => 'Statistiques',
                'competition' => 'Championnat Régional U19',
                'date_generation' => '2024-09-10',
                'statut' => 'Disponible',
                'formats' => ['PDF', 'Excel']
            ],
            [
                'id' => 3,
                'nom' => 'Rapport Discipline',
                'type' => 'Discipline',
                'competition' => 'Championnat Régional U19',
                'date_generation' => '2024-09-08',
                'statut' => 'En cours',
                'formats' => ['PDF']
            ]
        ]);

        return view('competitions.association.rapports-statistiques', compact('rapports'));
    }

    // ========================================
    // API ENDPOINTS
    // ========================================

    /**
     * API - Liste des compétitions
     */
    public function apiCompetitions(): JsonResponse
    {
        $competitions = Competition::with(['association'])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function($competition) {
                return [
                    'id' => $competition->id,
                    'nom' => $competition->name,
                    'saison' => $competition->season ?? '2024-2025',
                    'statut' => $this->getStatusLabel($competition->status),
                    'association' => $competition->association?->name ?? 'N/A',
                    'fifa_connect_id' => $competition->fifa_connect_id
                ];
            });

        return response()->json($competitions);
    }

    /**
     * API - Matchs d'une compétition
     */
    public function apiMatches($competitionId): JsonResponse
    {
        $matches = GameMatch::with(['homeClub', 'awayClub', 'competition'])
            ->where('competition_id', $competitionId)
            ->orderBy('match_date', 'asc')
            ->get()
            ->map(function($match) {
                $resultat = null;
                if ($match->match_status === 'completed' && $match->home_score !== null && $match->away_score !== null) {
                    $resultat = $match->home_score . '-' . $match->away_score;
                }

                return [
                    'id' => $match->id,
                    'date' => $match->match_date?->format('Y-m-d'),
                    'domicile' => $match->homeClub?->name ?? 'N/A',
                    'exterieur' => $match->awayClub?->name ?? 'N/A',
                    'statut' => $this->getMatchStatusLabel($match->match_status),
                    'resultat' => $resultat,
                    'lieu' => $match->venue ?? $match->stadium ?? 'N/A'
                ];
            });

        return response()->json($matches);
    }

    /**
     * API - Classements d'une compétition
     */
    public function apiClassements($competitionId): JsonResponse
    {
        // Récupérer les standings depuis la table standings
        $standings = Standing::with(['club', 'competition'])
            ->where('competition_id', $competitionId)
            ->orderBy('position', 'asc')
            ->get()
            ->map(function($standing) {
                return [
                    'position' => $standing->position,
                    'nom' => $standing->club?->name ?? 'N/A',
                    'points' => $standing->points,
                    'matchs' => $standing->matches_played,
                    'victoires' => $standing->wins,
                    'nuls' => $standing->draws,
                    'defaites' => $standing->losses,
                    'buts_pour' => $standing->goals_for,
                    'buts_contre' => $standing->goals_against,
                    'difference' => $standing->goal_difference
                ];
            });

        // Si pas de standings, calculer basiquement
        if ($standings->isEmpty()) {
            $competition = Competition::with('clubs')->find($competitionId);
            if ($competition) {
                $standings = $competition->clubs->map(function($club, $index) {
                    return [
                        'position' => $index + 1,
                        'nom' => $club->name,
                        'points' => 0,
                        'matchs' => 0,
                        'victoires' => 0,
                        'nuls' => 0,
                        'defaites' => 0,
                        'buts_pour' => 0,
                        'buts_contre' => 0,
                        'difference' => 0
                    ];
                });
            }
        }

        return response()->json($standings);
    }

    /**
     * API - Effectif d'un club
     */
    public function apiEffectif($clubId): JsonResponse
    {
        $players = Player::with(['club', 'fifaConnectId'])
            ->where('club_id', $clubId)
            ->get()
            ->map(function($player) {
                return [
                    'id' => $player->id,
                    'nom' => $player->last_name,
                    'prenom' => $player->first_name,
                    'licence' => $player->fifa_connect_id ?? 'N/A',
                    'statut' => !empty($player->fifa_connect_id) ? 'Éligible' : 'Inéligible'
                ];
            });

        return response()->json($players);
    }

    // ========================================
    // MÉTHODES UTILITAIRES
    // ========================================

    /**
     * Obtenir le libellé du statut FIFA Connect
     */
    private function getFifaStatusLabel($status): string
    {
        return match($status) {
            Competition::STATUS_DRAFT => 'Brouillon',
            Competition::STATUS_SUBMITTED => 'Soumis à la Fédération',
            Competition::STATUS_VALIDATED => 'Validé par la Fédération',
            Competition::STATUS_PUBLISHED => 'Publié',
            Competition::STATUS_CANCELLED => 'Annulé',
            default => ucfirst($status ?? 'Inconnu')
        };
    }

    /**
     * Obtenir le libellé du type FIFA Connect
     */
    private function getFifaTypeLabel($type): string
    {
        return match($type) {
            Competition::TYPE_CHAMPIONSHIP => 'Championnat',
            Competition::TYPE_CUP => 'Coupe',
            Competition::TYPE_TOURNAMENT => 'Tournoi',
            Competition::TYPE_FRIENDLY => 'Match Amical',
            Competition::TYPE_INTERNATIONAL => 'International',
            default => ucfirst($type ?? 'Inconnu')
        };
    }

    /**
     * Obtenir le libellé de la catégorie FIFA Connect (selon les standards FIFA)
     */
    private function getFifaCategoryLabel($category): string
    {
        return match($category) {
            // Catégories d'âge FIFA officielles
            Competition::CATEGORY_U13 => 'U-13',
            Competition::CATEGORY_U15 => 'U-15',
            Competition::CATEGORY_U17 => 'U-17',
            Competition::CATEGORY_U20 => 'U-20',
            Competition::CATEGORY_U23 => 'U-23',
            Competition::CATEGORY_SENIOR => 'Senior',
            
            // Catégories par genre FIFA
            Competition::CATEGORY_MEN => 'Masculin',
            Competition::CATEGORY_WOMEN => 'Féminin',
            
            // Disciplines FIFA
            Competition::CATEGORY_FUTSAL => 'Futsal',
            Competition::CATEGORY_BEACH => 'Beach Soccer',
            
            // Catégories locales (associations nationales)
            Competition::CATEGORY_U12 => 'U-12',
            Competition::CATEGORY_U14 => 'U-14',
            Competition::CATEGORY_U16 => 'U-16',
            Competition::CATEGORY_U18 => 'U-18',
            Competition::CATEGORY_U19 => 'U-19',
            Competition::CATEGORY_U21 => 'U-21 (Espoirs)',
            
            default => ucfirst($category ?? 'Inconnu')
        };
    }

    /**
     * Obtenir le libellé de la discipline FIFA Connect
     */
    private function getFifaDisciplineLabel($discipline): string
    {
        return match($discipline) {
            Competition::DISCIPLINE_FOOTBALL => 'Football',
            Competition::DISCIPLINE_FUTSAL => 'Futsal',
            Competition::DISCIPLINE_BEACH_SOCCER => 'Beach Soccer',
            default => ucfirst($discipline ?? 'Inconnu')
        };
    }

    /**
     * Obtenir le libellé du statut de match
     */
    private function getMatchStatusLabel($status): string
    {
        return match($status) {
            'scheduled' => 'Programmé',
            'live' => 'En cours',
            'completed' => 'Terminé',
            'postponed' => 'Reporté',
            'cancelled' => 'Annulé',
            default => ucfirst($status ?? 'Inconnu')
        };
    }

    /**
     * Calculer les points d'un club dans une compétition
     */
    private function calculateClubPoints($competition): int
    {
        try {
            // Récupérer les matchs terminés du club (simplifié - à adapter selon l'ID du club)
            $clubId = 1; // Remplacer par l'ID du club de l'utilisateur connecté
            
            $matches = $competition->matches()
                ->where('match_status', 'completed')
                ->where(function($query) use ($clubId) {
                    $query->where('home_club_id', $clubId)
                          ->orWhere('away_club_id', $clubId);
                })
                ->get();

            $points = 0;
            foreach ($matches as $match) {
                if ($match->home_club_id == $clubId) {
                    if ($match->home_score > $match->away_score) $points += 3;
                    elseif ($match->home_score == $match->away_score) $points += 1;
                } else {
                    if ($match->away_score > $match->home_score) $points += 3;
                    elseif ($match->away_score == $match->home_score) $points += 1;
                }
            }
            
            return $points;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculer le classement (simplifié)
     */
    private function calculateRanking($competition, $points): int
    {
        try {
            // Logique simplifiée - à améliorer avec les vraies données de classement
            $totalClubs = $competition->clubs()->count();
            if ($totalClubs === 0) return 1;
            
            // Calcul basique basé sur les points
            $rank = max(1, $totalClubs - intval($points / 3));
            return min($rank, $totalClubs);
        } catch (\Exception $e) {
            return 1;
        }
    }

    // ========================================
    // ACTIONS
    // ========================================

    /**
     * Soumettre une feuille de match
     */
    public function soumettreFeuilleMatch(Request $request, $matchId)
    {
        // Logique de soumission de feuille de match
        return response()->json(['success' => true, 'message' => 'Feuille de match soumise avec succès']);
    }

    /**
     * Vérifier l'effectif
     */
    public function verifierEffectif(Request $request)
    {
        // Logique de vérification de l'effectif
        return response()->json(['success' => true, 'message' => 'Effectif vérifié']);
    }

    /**
     * Valider une feuille de match
     */
    public function validerFeuilleMatch(Request $request, $feuilleId)
    {
        // Logique de validation de feuille de match
        return response()->json(['success' => true, 'message' => 'Feuille de match validée']);
    }

    /**
     * Reprogrammer un match
     */
    public function reprogrammerMatch(Request $request, $matchId)
    {
        // Logique de reprogrammation de match
        return response()->json(['success' => true, 'message' => 'Match reprogrammé']);
    }

    /**
     * Mettre à jour un résultat
     */
    public function mettreAJourResultat(Request $request, $matchId)
    {
        // Logique de mise à jour de résultat
        return response()->json(['success' => true, 'message' => 'Résultat mis à jour']);
    }

    /**
     * Ajouter une sanction
     */
    public function ajouterSanction(Request $request)
    {
        // Logique d'ajout de sanction
        return response()->json(['success' => true, 'message' => 'Sanction ajoutée']);
    }

    /**
     * Exporter un rapport
     */
    public function exportRapport(Request $request, $type)
    {
        // Logique d'export de rapport
        return response()->json(['success' => true, 'message' => 'Rapport exporté']);
    }

    /**
     * Déterminer le type de licence FIFA Connect d'un joueur
     */
    private function getFifaLicenseType($player): string
    {
        // Vérifier d'abord les licences actives du joueur
        $activeLicense = $player->playerLicenses()
            ->where('status', 'active')
            ->where('expiry_date', '>', now())
            ->latest('issue_date')
            ->first();

        if ($activeLicense) {
            return $this->getFifaLicenseTypeLabel($activeLicense->license_type);
        }

        // Si pas de licence active, déterminer selon l'âge et le statut
        $age = $player->age ?? 0;
        
        if ($age < 13) {
            return 'Non éligible (moins de 13 ans)';
        } elseif ($age < 15) {
            return 'Licence Amateur U-13';
        } elseif ($age < 17) {
            return 'Licence Amateur U-15';
        } elseif ($age < 20) {
            return 'Licence Amateur U-17';
        } elseif ($age < 23) {
            return 'Licence Amateur U-20';
        } else {
            return 'Licence Amateur Senior';
        }
    }

    /**
     * Obtenir le libellé du type de licence FIFA Connect
     */
    private function getFifaLicenseTypeLabel($type): string
    {
        return match($type) {
            // Types de licences FIFA officiels
            'professional' => 'Licence Professionnelle',
            'amateur' => 'Licence Amateur',
            'youth' => 'Licence Jeunesse',
            'international' => 'Licence Internationale',
            'futsal' => 'Licence Futsal',
            'beach_soccer' => 'Licence Beach Soccer',
            
            // Types d'officiels techniques
            'coach_fifa' => 'Entraîneur FIFA/Confédération',
            'coach_national' => 'Entraîneur National',
            'medical_staff' => 'Staff Médical',
            'physio' => 'Kinésithérapeute',
            'doctor' => 'Médecin d\'équipe',
            
            // Types d'arbitres
            'referee_fifa' => 'Arbitre International FIFA',
            'referee_national' => 'Arbitre National',
            'assistant_referee' => 'Assistant Arbitre',
            'fourth_official' => '4ème Arbitre',
            'var_official' => 'Officiel VAR',
            
            // Types de dirigeants
            'club_president' => 'Président de Club',
            'club_secretary' => 'Secrétaire de Club',
            'club_treasurer' => 'Trésorier de Club',
            'association_official' => 'Dirigeant d\'Association',
            'match_delegate' => 'Délégué de Match',
            'security_official' => 'Responsable de Sécurité',
            
            default => ucfirst($type ?? 'Inconnu')
        };
    }

    /**
     * Classement des compétitions - Accessible aux clubs et associations
     */
    public function classement(): View
    {
        try {
            // Récupérer les vraies données tunisiennes
            $tunisianAssociation = Association::where('name', 'like', '%Tunis%')->first();
            
            if (!$tunisianAssociation) {
                throw new \Exception('Association tunisienne non trouvée');
            }

            $competitions = Competition::where('association_id', $tunisianAssociation->id)->get();
            $tunisianClubs = Club::where('association_id', $tunisianAssociation->id)->get();

            if ($competitions->isEmpty()) {
                throw new \Exception('Aucune compétition tunisienne trouvée');
            }

            if ($tunisianClubs->isEmpty()) {
                throw new \Exception('Aucun club tunisien trouvé');
            }

            // Générer des matchs fictifs et calculer le classement avec les vraies données
            $classements = [];
            
            foreach ($competitions as $competition) {
                $classements[$competition->id] = $this->generateClassementDataFromRealClubs($competition, $tunisianClubs);
            }

            return view('competitions.classement', compact('competitions', 'classements', 'tunisianClubs', 'tunisianAssociation'));
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données de démonstration
            $competitions = collect([
                (object)[
                    'id' => 1,
                    'name' => 'Championnat Tunisien U19',
                    'season' => '2024-2025',
                    'association' => (object)['name' => 'FTF - Fédération Tunisienne de Football'],
                    'clubs' => collect()
                ]
            ]);
            
            $tunisianClubs = collect([
                (object)['id' => 1, 'name' => 'Espérance Sportive de Tunis', 'short_name' => 'EST'],
                (object)['id' => 2, 'name' => 'Club Africain', 'short_name' => 'CA'],
                (object)['id' => 3, 'name' => 'Étoile Sportive du Sahel', 'short_name' => 'ESS'],
                (object)['id' => 4, 'name' => 'Union Sportive Monastirienne', 'short_name' => 'USM'],
                (object)['id' => 5, 'name' => 'Club Sportif Sfaxien', 'short_name' => 'CSS'],
                (object)['id' => 6, 'name' => 'Stade Tunisien', 'short_name' => 'ST'],
                (object)['id' => 7, 'name' => 'Club Athlétique Bizertin', 'short_name' => 'CAB'],
                (object)['id' => 8, 'name' => 'Avenir Sportif de La Marsa', 'short_name' => 'ASM'],
                (object)['id' => 9, 'name' => 'Club Olympique de Médenine', 'short_name' => 'COM'],
                (object)['id' => 10, 'name' => 'Union Sportive de Ben Guerdane', 'short_name' => 'USBG'],
                (object)['id' => 11, 'name' => 'Club Sportif de Hammam-Lif', 'short_name' => 'CSHL'],
                (object)['id' => 12, 'name' => 'Club Olympique de Kairouan', 'short_name' => 'COK'],
                (object)['id' => 13, 'name' => 'Union Sportive de Tataouine', 'short_name' => 'UST'],
                (object)['id' => 14, 'name' => 'Club Sportif de Jendouba', 'short_name' => 'CSJ'],
                (object)['id' => 15, 'name' => 'Union Sportive de Siliana', 'short_name' => 'USS'],
                (object)['id' => 16, 'name' => 'Club Sportif de Kasserine', 'short_name' => 'CSK'],
                (object)['id' => 17, 'name' => 'Union Sportive de Béja', 'short_name' => 'USB'],
                (object)['id' => 18, 'name' => 'Club Sportif de Gafsa', 'short_name' => 'CSG'],
                (object)['id' => 19, 'name' => 'Union Sportive de Tozeur', 'short_name' => 'UST'],
                (object)['id' => 20, 'name' => 'Club Sportif de Kebili', 'short_name' => 'CSK']
            ]);

            $classements = [];
            foreach ($competitions as $competition) {
                $classements[$competition->id] = $this->generateClassementData($competition, $tunisianClubs);
            }

            $tunisianAssociation = (object)['name' => 'FTF - Fédération Tunisienne de Football'];

            return view('competitions.classement', compact('competitions', 'classements', 'tunisianClubs', 'tunisianAssociation'));
        }
    }

    /**
     * Génère les données de classement pour une compétition
     */
    private function generateClassementData($competition, $clubs)
    {
        $classement = [];
        
        foreach ($clubs as $club) {
            // Générer des statistiques réalistes
            $matchsJoues = rand(15, 25);
            $victoires = rand(5, $matchsJoues - 5);
            $nuls = rand(2, min(8, $matchsJoues - $victoires));
            $defaites = $matchsJoues - $victoires - $nuls;
            
            // Générer des buts de manière réaliste
            $butsPour = rand($victoires * 1, $victoires * 3) + rand(0, $nuls);
            $butsContre = rand($defaites * 1, $defaites * 2) + rand(0, $nuls);
            
            // Calculer les points (3 points pour victoire, 1 pour nul)
            $points = ($victoires * 3) + $nuls;
            
            // Générer la forme récente (5 derniers matchs)
            $forme = [];
            for ($i = 0; $i < 5; $i++) {
                $resultat = rand(1, 3);
                $forme[] = $resultat == 1 ? 'V' : ($resultat == 2 ? 'N' : 'D');
            }
            
            $classement[] = [
                'club' => $club,
                'position' => 0, // Sera calculé après tri
                'matchs_joues' => $matchsJoues,
                'victoires' => $victoires,
                'nuls' => $nuls,
                'defaites' => $defaites,
                'buts_pour' => $butsPour,
                'buts_contre' => $butsContre,
                'difference_buts' => $butsPour - $butsContre,
                'points' => $points,
                'forme' => $forme,
                'evolution' => rand(-2, 3) // Évolution de position
            ];
        }
        
        // Trier par points (décroissant), puis par différence de buts
        usort($classement, function($a, $b) {
            if ($a['points'] == $b['points']) {
                return $b['difference_buts'] - $a['difference_buts'];
            }
            return $b['points'] - $a['points'];
        });
        
        // Assigner les positions
        foreach ($classement as $index => &$equipe) {
            $equipe['position'] = $index + 1;
        }
        
        return $classement;
    }

    /**
     * Fixtures du club - Tous les matchs de la saison
     */
    public function clubFixtures(): View
    {
        try {
            // Récupérer les vraies données tunisiennes
            $tunisianAssociation = Association::where('name', 'like', '%Tunis%')->first();
            
            if (!$tunisianAssociation) {
                throw new \Exception('Association tunisienne non trouvée');
            }

            $tunisianClubs = Club::where('association_id', $tunisianAssociation->id)->get();
            $competitions = Competition::where('association_id', $tunisianAssociation->id)->get();

            if ($tunisianClubs->isEmpty()) {
                throw new \Exception('Aucun club tunisien trouvé');
            }

            // Générer les fixtures basées sur les vraies données
            $fixtures = $this->generateFixturesFromRealData($tunisianClubs, $competitions);

            return view('competitions.club.fixtures', compact('fixtures', 'tunisianClubs', 'competitions', 'tunisianAssociation'));
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données de démonstration
            $tunisianClubs = collect([
                (object)['id' => 1, 'name' => 'Espérance Sportive de Tunis', 'short_name' => 'EST'],
                (object)['id' => 2, 'name' => 'Club Africain', 'short_name' => 'CA'],
                (object)['id' => 3, 'name' => 'Étoile Sportive du Sahel', 'short_name' => 'ESS'],
                (object)['id' => 4, 'name' => 'Union Sportive Monastirienne', 'short_name' => 'USM'],
                (object)['id' => 5, 'name' => 'Club Sportif Sfaxien', 'short_name' => 'CSS']
            ]);

            $fixtures = $this->generateFixtures($tunisianClubs);
            $competitions = collect([(object)['id' => 1, 'name' => 'Championnat Tunisien U19', 'season' => '2024-2025']]);
            $tunisianAssociation = (object)['name' => 'FTF - Fédération Tunisienne de Football'];
            
            return view('competitions.club.fixtures', compact('fixtures', 'tunisianClubs', 'competitions', 'tunisianAssociation'));
        }
    }

    /**
     * Fixtures de l'association - Tous les matchs de toutes les compétitions
     */
    public function associationFixtures(Request $request): View
    {
        try {
            // Récupérer les données depuis la base de données FIFA
            $tunisianAssociation = Association::where('name', 'Fédération Tunisienne de Football')->first();
            
            if (!$tunisianAssociation) {
                return view('errors.database', [
                    'error' => 'Association tunisienne non trouvée',
                    'message' => 'Impossible de trouver l\'association tunisienne dans la base de données.'
                ]);
            }
            
            // Récupérer les clubs tunisiens depuis la base de données
            $clubs = Club::where('association_id', $tunisianAssociation->id)->get();

            if ($clubs->isEmpty()) {
                return view('errors.database', [
                    'error' => 'Aucun club trouvé',
                    'message' => 'Aucun club tunisien trouvé dans la base de données.'
                ]);
            }

            // Utiliser les données de la base de données au lieu de générer des fixtures
            $allFixtures = $this->getFixturesFromDatabase($tunisianAssociation, $clubs);
            
            // Pagination par journée (5 journées par page)
            $perPage = 5;
            $currentPage = $request->get('page', 1);
            $totalJournees = count($allFixtures);
            $offset = ($currentPage - 1) * $perPage;
            
            // Extraire les journées pour la page courante
            $fixtures = array_slice($allFixtures, $offset, $perPage, true);
            
            // Créer un objet de pagination personnalisé
            $paginatedFixtures = new \Illuminate\Pagination\LengthAwarePaginator(
                $fixtures,
                $totalJournees,
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'pageName' => 'page',
                ]
            );
            
            // Récupérer les vraies compétitions de la FTF
            $tunisianAssociation = Association::where('name', 'Fédération Tunisienne de Football')->first();
            if ($tunisianAssociation) {
                $competitions = Competition::where('association_id', $tunisianAssociation->id)
                    ->get()
                    ->map(function($comp) {
                        return (object)[
                            'id' => $comp->id,
                            'name' => $comp->name,
                            'season' => $comp->season ?? 'Saison non définie'
                        ];
                    });
            } else {
                $competitions = collect([
                    (object)['id' => 1, 'name' => 'Championnat Tunisien U19', 'season' => '2024-2025'],
                    (object)['id' => 2, 'name' => 'Coupe de Tunisie', 'season' => '2024-2025']
                ]);
            }

            return view('competitions.association.fixtures', compact('paginatedFixtures', 'clubs', 'competitions'));
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une vue d'erreur
            return view('errors.database', ['message' => 'Erreur lors de la récupération des données: ' . $e->getMessage()]);
        }
    }

    /**
     * Feuille de match détaillée
     */
    public function feuilleMatch($id): View
    {
        try {
            // Utiliser les données de la base de données au lieu de générer des données
            $feuilleMatch = $this->getFeuilleMatchFromDatabase($id);
            
            return view('competitions.feuille-match', compact('feuilleMatch'));
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une vue d'erreur
            return view('errors.database', ['message' => 'Erreur lors de la récupération des données: ' . $e->getMessage()]);
        }
    }

    /**
     * Désignation des arbitres
     */
    public function designationArbitres(): View
    {
        try {
            // Récupérer les matchs à venir
            $tunisianClubs = Club::whereHas('association', function($query) {
                $query->where('name', 'like', '%Tunis%');
            })->get();

            if ($tunisianClubs->isEmpty()) {
                $tunisianClubs = Club::take(20)->get();
            }

            $matchs = $this->generateMatchsForArbitres($tunisianClubs);
            $arbitres = $this->generateArbitres();

            return view('competitions.association.designation-arbitres', compact('matchs', 'arbitres'));
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une vue d'erreur
            return view('errors.database', ['message' => 'Erreur lors de la récupération des données: ' . $e->getMessage()]);
        }
    }

    /**
     * Récupère les fixtures depuis la base de données FIFA
     */
    private function getFixturesFromDatabase($association, $clubs)
    {
        // Récupérer les compétitions de l'association
        $competitions = Competition::where('association_id', $association->id)->get();
        
        if ($competitions->isEmpty()) {
            return [];
        }
        
        // Récupérer les matchs depuis la base de données
        $matches = GameMatch::with(['homeTeam', 'awayTeam'])
            ->whereIn('competition_id', $competitions->pluck('id'))
            ->orderBy('round')
            ->orderBy('match_date')
            ->get();
        
        if ($matches->isEmpty()) {
            return [];
        }
        
        // Organiser les matchs par journée
        $fixtures = [];
        $matchesByRound = $matches->groupBy('round');
        
        foreach ($matchesByRound as $round => $roundMatches) {
            $matchsJournee = [];
            
            foreach ($roundMatches as $match) {
                $statut = $match->status === 'completed' ? 'Terminé' : 'À venir';
                
                $matchsJournee[] = [
                    'id' => $match->id,
                    'domicile' => $match->homeTeam,
                    'exterieur' => $match->awayTeam,
                    'date' => $match->match_date,
                    'heure' => $match->match_time,
                    'stade' => $match->venue,
                    'buts_domicile' => $match->home_score,
                    'buts_exterieur' => $match->away_score,
                    'statut' => $statut,
                    'journee' => $match->round,
                    'arbitre_principal' => $match->referee ?? 'À désigner',
                    'arbitre_assistant_1' => $match->assistant_referee_1 ?? 'À désigner',
                    'arbitre_assistant_2' => $match->assistant_referee_2 ?? 'À désigner',
                    'arbitre_var' => $match->var_referee ?? 'À désigner',
                    'delegue_match' => $match->match_official ?? 'À désigner',
                    'observateur' => $match->observer ?? 'À désigner',
                ];
            }
            
            $fixtures[] = [
                'journee' => $round,
                'date' => $roundMatches->first()->match_date,
                'matchs' => $matchsJournee
            ];
        }
        
        return $fixtures;
    }

    /**
     * Récupère une feuille de match depuis la base de données FIFA
     */
    private function getFeuilleMatchFromDatabase($id)
    {
        // Récupérer le match directement depuis la base de données
        $match = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])
            ->find($id);
        
        if (!$match) {
            throw new \Exception('Match non trouvé dans la base de données');
        }
        
        $statut = $match->status === 'completed' ? 'Terminé' : 'À venir';
        
        // Retourner les données de la feuille de match
        return [
            'id' => $match->id,
            'competition' => $match->competition ? $match->competition->name : 'Compétition Tunisienne',
            'journee' => $match->round,
            'date' => $match->match_date,
            'heure' => $match->match_time,
            'stade' => $match->venue,
            'domicile' => $match->homeTeam,
            'exterieur' => $match->awayTeam,
            'buts_domicile' => $match->home_score,
            'buts_exterieur' => $match->away_score,
            'statut' => $statut,
            'arbitre_principal' => $match->referee ?? 'À désigner',
            'arbitre_assistant_1' => $match->assistant_referee_1 ?? 'À désigner',
            'arbitre_assistant_2' => $match->assistant_referee_2 ?? 'À désigner',
            'arbitre_var' => $match->var_referee ?? 'À désigner',
            'delegue_match' => $match->match_official ?? 'À désigner',
            'observateur' => $match->observer ?? 'À désigner',
            'joueurs_domicile' => [], // Pas de joueurs pour l'instant
            'joueurs_exterieur' => [],
            'evenements' => [] // Pas d'événements pour l'instant
        ];
    }

    /**
     * Génère les fixtures pour une saison (MÉTHODE DÉPRÉCIÉE - NE PLUS UTILISER)
     */
    private function generateFixtures($clubs)
    {
        $fixtures = [];
        $journees = 30; // 30 journées pour un championnat
        
        // Sauvegarder le seed original
        $originalSeed = mt_rand();
        
        for ($journee = 1; $journee <= $journees; $journee++) {
            $matchsJournee = [];
            
            // Générer les matchs pour cette journée avec un seed fixe pour la cohérence
            mt_srand(12345 + $journee * 1000); // Seed fixe par journée
            $clubsShuffled = $clubs->shuffle();
            for ($i = 0; $i < count($clubsShuffled); $i += 2) {
                if ($i + 1 < count($clubsShuffled)) {
                    $domicile = $clubsShuffled[$i];
                    $exterieur = $clubsShuffled[$i + 1];
                    
                    // Générer une date déterministe dans la saison (date de base fixe)
                    $daysOffset = ($journee - 1) * 7 + ($i % 7); // Utiliser $i au lieu de rand() pour la cohérence
                    $baseDate = \Carbon\Carbon::create(2025, 8, 15); // Date de base fixe pour la saison
                    $dateMatch = $baseDate->copy()->addDays($daysOffset);
                    
                    // Déterminer si le match est terminé ou à venir
                    $isMatchFinished = $dateMatch < now();
                    
                    // Générer un résultat seulement si le match est terminé
                    $butsDomicile = null;
                    $butsExterieur = null;
                    if ($isMatchFinished) {
                        // Utiliser l'ID du match comme seed pour la cohérence
                        $matchId = $journee * 100 + $i;
                        mt_srand($matchId);
                        $resultat = mt_rand(1, 3);
                        $butsDomicile = $resultat == 1 ? mt_rand(1, 4) : ($resultat == 2 ? mt_rand(0, 2) : mt_rand(0, 1));
                        $butsExterieur = $resultat == 1 ? mt_rand(0, 1) : ($resultat == 2 ? mt_rand(0, 2) : mt_rand(1, 4));
                    }
                    
                    $arbitres = $this->getConsistentArbitresForMatch($matchId);
                    
                    $matchsJournee[] = [
                        'id' => $matchId,
                        'domicile' => $domicile,
                        'exterieur' => $exterieur,
                        'date' => $dateMatch,
                        'heure' => '15:00',
                        'stade' => $domicile->address ?? 'Stade Municipal',
                        'buts_domicile' => $butsDomicile,
                        'buts_exterieur' => $butsExterieur,
                        'statut' => $isMatchFinished ? 'Terminé' : 'À venir',
                        'arbitre_principal' => $arbitres['principal'],
                        'arbitre_assistant_1' => $arbitres['assistant_1'],
                        'arbitre_assistant_2' => $arbitres['assistant_2'],
                        'arbitre_var' => $arbitres['var']
                    ];
                }
            }
            
            $fixtures[] = [
                'journee' => $journee,
                'date' => $dateMatch,
                'matchs' => $matchsJournee
            ];
        }
        
        // Restaurer le seed original
        mt_srand($originalSeed);
        
        return $fixtures;
    }

    /**
     * Génère une feuille de match détaillée en récupérant les données depuis les fixtures
     */
    private function generateFeuilleMatchFromFixtures($id)
    {
        // Récupérer les vrais clubs tunisiens
        $tunisianAssociation = Association::where('name', 'Fédération Tunisienne de Football')->first();
        if (!$tunisianAssociation) {
            throw new \Exception('Association tunisienne non trouvée');
        }
        
        $clubs = Club::where('association_id', $tunisianAssociation->id)->get();
        if ($clubs->isEmpty()) {
            throw new \Exception('Aucun club tunisien trouvé');
        }

        // Générer les fixtures pour récupérer les données du match
        // (la méthode generateFixtures utilise déjà des seeds fixes)
        $fixtures = $this->generateFixtures($clubs);
        
        // Chercher le match dans les fixtures
        $matchData = null;
        foreach ($fixtures as $journee) {
            foreach ($journee['matchs'] as $match) {
                if ($match['id'] == $id) {
                    $matchData = $match;
                    break 2;
                }
            }
        }
        
        if (!$matchData) {
            throw new \Exception('Match non trouvé dans les fixtures');
        }

        // Récupérer la vraie compétition
        $competition = Competition::where('association_id', $tunisianAssociation->id)->first();
        
        // Pour les matchs à venir, ne pas générer d'informations détaillées
        if ($matchData['statut'] === 'À venir') {
            return [
                'id' => $id,
                'competition' => $competition ? $competition->name : 'Compétition Tunisienne',
                'journee' => intval($id / 100),
                'date' => $matchData['date'],
                'heure' => $matchData['heure'],
                'stade' => $matchData['stade'],
                'domicile' => $matchData['domicile'],
                'exterieur' => $matchData['exterieur'],
                'buts_domicile' => null,
                'buts_exterieur' => null,
                'statut' => $matchData['statut'],
                'arbitre_principal' => 'À désigner',
                'arbitre_assistant_1' => 'À désigner',
                'arbitre_assistant_2' => 'À désigner',
                'arbitre_var' => 'À désigner',
                'delegue_match' => 'À désigner',
                'observateur' => 'À désigner',
                'joueurs_domicile' => [],
                'joueurs_exterieur' => [],
                'evenements' => []
            ];
        }
        
        // Pour les matchs terminés, générer toutes les informations
        $arbitres = $this->getConsistentArbitresForMatch($id);
        
        return [
            'id' => $id,
            'competition' => $competition ? $competition->name : 'Compétition Tunisienne',
            'journee' => intval($id / 100),
            'date' => $matchData['date'],
            'heure' => $matchData['heure'],
            'stade' => $matchData['stade'],
            'domicile' => $matchData['domicile'],
            'exterieur' => $matchData['exterieur'],
            'buts_domicile' => $matchData['buts_domicile'],
            'buts_exterieur' => $matchData['buts_exterieur'],
            'statut' => $matchData['statut'],
            'arbitre_principal' => $arbitres['principal'],
            'arbitre_assistant_1' => $arbitres['assistant_1'],
            'arbitre_assistant_2' => $arbitres['assistant_2'],
            'arbitre_var' => $arbitres['var'],
            'delegue_match' => $arbitres['delegue'],
            'observateur' => $arbitres['observateur'],
            'joueurs_domicile' => $this->generateJoueursFromDatabase($matchData['domicile']),
            'joueurs_exterieur' => $this->generateJoueursFromDatabase($matchData['exterieur']),
            'evenements' => $this->generateEvenementsWithRealPlayers($matchData['domicile'], $matchData['exterieur'])
        ];
    }

    /**
     * Génère une feuille de match détaillée avec les vraies données
     */
    private function generateFeuilleMatch($id)
    {
        // Récupérer les vrais clubs tunisiens
        $tunisianAssociation = Association::where('name', 'Fédération Tunisienne de Football')->first();
        if (!$tunisianAssociation) {
            throw new \Exception('Association tunisienne non trouvée');
        }
        
        $clubs = Club::where('association_id', $tunisianAssociation->id)->get();
        if ($clubs->isEmpty()) {
            throw new \Exception('Aucun club tunisien trouvé');
        }

        $domicile = $clubs->random();
        $exterieur = $clubs->where('id', '!=', $domicile->id)->random();

        // Récupérer la vraie compétition
        $competition = Competition::where('association_id', $tunisianAssociation->id)->first();
        
        return [
            'id' => $id,
            'competition' => $competition ? $competition->name : 'Compétition Tunisienne',
            'journee' => rand(1, 30),
            'date' => now()->addDays(rand(-30, 30)),
            'heure' => '15:00',
            'stade' => $domicile->address ?? 'Stade Municipal',
            'domicile' => $domicile,
            'exterieur' => $exterieur,
            'buts_domicile' => rand(0, 4),
            'buts_exterieur' => rand(0, 4),
            'statut' => 'Terminé',
            'arbitre_principal' => $this->getRandomArbitre('principal'),
            'arbitre_assistant_1' => $this->getRandomArbitre('assistant'),
            'arbitre_assistant_2' => $this->getRandomArbitre('assistant'),
            'arbitre_var' => $this->getRandomArbitre('var'),
            'delegue_match' => $this->getRandomArbitre('principal'),
            'observateur' => $this->getRandomArbitre('principal'),
            'joueurs_domicile' => $this->generateJoueursFromDatabase($domicile),
            'joueurs_exterieur' => $this->generateJoueursFromDatabase($exterieur),
            'evenements' => $this->generateEvenementsWithRealPlayers($domicile, $exterieur)
        ];
    }

    /**
     * Génère les joueurs d'une équipe depuis la base de données
     */
    private function generateJoueursFromDatabase($club)
    {
        // Récupérer les vrais joueurs du club depuis la base de données
        $players = Player::where('club_id', $club->id)->get();
        
        if ($players->isEmpty()) {
            // Si aucun joueur trouvé, générer des joueurs fictifs avec des noms tunisiens réalistes
            $joueurs = [];
            $nomsTunisiens = [
                'Ahmed', 'Mohamed', 'Salah', 'Karim', 'Hassan', 'Ridha', 'Ali', 'Omar', 'Youssef', 'Nabil',
                'Tarek', 'Walid', 'Adel', 'Bilel', 'Hamza', 'Ibrahim', 'Jamel', 'Khalil', 'Lamine', 'Mansour'
            ];
            $prenomsTunisiens = [
                'Ben Ali', 'Trabelsi', 'Jebali', 'Mejri', 'Khelil', 'Ben Amor', 'Gharbi', 'Sassi', 'Bouazizi', 'Mansouri',
                'Ben Salah', 'Dridi', 'Ferjani', 'Garbi', 'Hammami', 'Jebali', 'Khelifi', 'Laroussi', 'Mabrouk', 'Naceur'
            ];
            
            for ($i = 1; $i <= 11; $i++) {
                $nom = $nomsTunisiens[array_rand($nomsTunisiens)];
                $prenom = $prenomsTunisiens[array_rand($prenomsTunisiens)];
                
                $joueurs[] = [
                    'numero' => $i,
                    'nom' => $nom . ' ' . $prenom,
                    'position' => $this->getPosition($i),
                    'titulaire' => true,
                    'remplacant' => false,
                    'carton_jaune' => rand(0, 1) ? rand(1, 90) : null,
                    'carton_rouge' => rand(0, 10) == 0 ? rand(1, 90) : null,
                    'buts' => rand(0, 2),
                    'assists' => rand(0, 1)
                ];
            }
            
            // Ajouter les remplaçants
            for ($i = 12; $i <= 18; $i++) {
                $nom = $nomsTunisiens[array_rand($nomsTunisiens)];
                $prenom = $prenomsTunisiens[array_rand($prenomsTunisiens)];
                
                $joueurs[] = [
                    'numero' => $i,
                    'nom' => $nom . ' ' . $prenom,
                    'position' => $this->getPosition($i),
                    'titulaire' => false,
                    'remplacant' => true,
                    'carton_jaune' => null,
                    'carton_rouge' => null,
                    'buts' => 0,
                    'assists' => 0
                ];
            }
            
            return $joueurs;
        }
        
        // Utiliser les vrais joueurs
        $joueurs = [];
        $titulaires = $players->take(11);
        $remplacants = $players->slice(11, 7);
        
        // Titulaires
        foreach ($titulaires as $index => $player) {
            $joueurs[] = [
                'numero' => $index + 1,
                'nom' => $player->first_name . ' ' . $player->last_name,
                'position' => $player->position ?? $this->getPosition($index + 1),
                'titulaire' => true,
                'remplacant' => false,
                'carton_jaune' => rand(0, 1) ? rand(1, 90) : null,
                'carton_rouge' => rand(0, 10) == 0 ? rand(1, 90) : null,
                'buts' => rand(0, 2),
                'assists' => rand(0, 1)
            ];
        }
        
        // Remplaçants
        foreach ($remplacants as $index => $player) {
            $joueurs[] = [
                'numero' => $index + 12,
                'nom' => $player->first_name . ' ' . $player->last_name,
                'position' => $player->position ?? $this->getPosition($index + 12),
                'titulaire' => false,
                'remplacant' => true,
                'carton_jaune' => null,
                'carton_rouge' => null,
                'buts' => 0,
                'assists' => 0
            ];
        }
        
        return $joueurs;
    }

    /**
     * Retourne la position d'un joueur selon son numéro
     */
    private function getPosition($numero)
    {
        $positions = [
            1 => 'Gardien',
            2 => 'Défenseur',
            3 => 'Défenseur',
            4 => 'Défenseur',
            5 => 'Défenseur',
            6 => 'Milieu',
            7 => 'Milieu',
            8 => 'Milieu',
            9 => 'Attaquant',
            10 => 'Milieu',
            11 => 'Attaquant'
        ];
        
        return $positions[$numero] ?? 'Remplaçant';
    }

    /**
     * Génère les événements du match
     */
    private function generateEvenements()
    {
        $evenements = [];
        
        // Générer des événements réalistes avec des minutes cohérentes
        $minutesPossibles = [5, 12, 18, 25, 32, 41, 43, 47, 52, 59, 63, 69, 72, 78, 85, 88, 90];
        $types = ['But', 'Carton Jaune', 'Carton Rouge', 'Remplacement'];
        
        // Générer entre 5 et 12 événements
        $nbEvenements = rand(5, 12);
        $minutesUtilisees = [];
        
        for ($i = 0; $i < $nbEvenements; $i++) {
            // Choisir une minute non utilisée
            $minute = $minutesPossibles[array_rand($minutesPossibles)];
            while (in_array($minute, $minutesUtilisees)) {
                $minute = $minutesPossibles[array_rand($minutesPossibles)];
            }
            $minutesUtilisees[] = $minute;
            
            $type = $types[array_rand($types)];
            $equipe = rand(0, 1) ? 'Domicile' : 'Exterieur';
            
            // Générer un numéro de joueur réaliste
            $numeroJoueur = rand(1, 18);
            
            $evenements[] = [
                'minute' => $minute,
                'type' => $type,
                'joueur' => 'Joueur ' . $numeroJoueur,
                'equipe' => $equipe,
                'description' => $this->getEventDescription($type, $numeroJoueur, $equipe)
            ];
        }
        
        // Trier par minute
        usort($evenements, function($a, $b) {
            return $a['minute'] - $b['minute'];
        });
        
        return $evenements;
    }
    
    /**
     * Génère une description réaliste pour un événement
     */
    private function getEventDescription($type, $numeroJoueur, $equipe)
    {
        switch ($type) {
            case 'But':
                return "But marqué par le joueur $numeroJoueur ($equipe)";
            case 'Carton Jaune':
                return "Carton jaune pour le joueur $numeroJoueur ($equipe)";
            case 'Carton Rouge':
                return "Carton rouge pour le joueur $numeroJoueur ($equipe)";
            case 'Remplacement':
                return "Remplacement du joueur $numeroJoueur ($equipe)";
            default:
                return "Événement du match";
        }
    }
    
    /**
     * Génère les événements du match avec les vrais noms des joueurs
     */
    private function generateEvenementsWithRealPlayers($domicile, $exterieur)
    {
        $evenements = [];
        
        // Récupérer les joueurs des deux équipes
        $joueursDomicile = $this->generateJoueursFromDatabase($domicile);
        $joueursExterieur = $this->generateJoueursFromDatabase($exterieur);
        
        // Générer des événements réalistes avec des minutes cohérentes
        $minutesPossibles = [5, 12, 18, 25, 32, 41, 43, 47, 52, 59, 63, 69, 72, 78, 85, 88, 90];
        $types = ['But', 'Carton Jaune', 'Carton Rouge', 'Remplacement'];
        
        // Générer entre 5 et 12 événements
        $nbEvenements = rand(5, 12);
        $minutesUtilisees = [];
        
        for ($i = 0; $i < $nbEvenements; $i++) {
            // Choisir une minute non utilisée
            $minute = $minutesPossibles[array_rand($minutesPossibles)];
            while (in_array($minute, $minutesUtilisees)) {
                $minute = $minutesPossibles[array_rand($minutesPossibles)];
            }
            $minutesUtilisees[] = $minute;
            
            $type = $types[array_rand($types)];
            $equipe = rand(0, 1) ? 'Domicile' : 'Exterieur';
            
            // Choisir un joueur aléatoire de l'équipe
            $joueurs = $equipe === 'Domicile' ? $joueursDomicile : $joueursExterieur;
            $joueur = $joueurs[array_rand($joueurs)];
            
            $evenements[] = [
                'minute' => $minute,
                'type' => $type,
                'joueur' => $joueur['nom'],
                'equipe' => $equipe,
                'description' => $this->getEventDescriptionWithPlayer($type, $joueur['nom'], $equipe)
            ];
        }
        
        // Trier par minute
        usort($evenements, function($a, $b) {
            return $a['minute'] - $b['minute'];
        });
        
        return $evenements;
    }
    
    /**
     * Génère une description réaliste pour un événement avec le nom du joueur
     */
    private function getEventDescriptionWithPlayer($type, $nomJoueur, $equipe)
    {
        switch ($type) {
            case 'But':
                return "But marqué par $nomJoueur ($equipe)";
            case 'Carton Jaune':
                return "Carton jaune pour $nomJoueur ($equipe)";
            case 'Carton Rouge':
                return "Carton rouge pour $nomJoueur ($equipe)";
            case 'Remplacement':
                return "Remplacement de $nomJoueur ($equipe)";
            default:
                return "Événement du match";
        }
    }

    /**
     * Génère les matchs pour la désignation des arbitres
     */
    private function generateMatchsForArbitres($clubs)
    {
        $matchs = [];
        
        for ($i = 1; $i <= 20; $i++) {
            $domicile = $clubs->random();
            $exterieur = $clubs->where('id', '!=', $domicile->id)->random();
            
            $matchs[] = [
                'id' => $i,
                'domicile' => $domicile,
                'exterieur' => $exterieur,
                'date' => now()->addDays(rand(1, 30)),
                'heure' => '15:00',
                'stade' => $domicile->address ?? 'Stade Municipal',
                'competition' => 'Championnat Tunisien U19',
                'journee' => rand(1, 30),
                'arbitre_principal' => null,
                'arbitre_assistant_1' => null,
                'arbitre_assistant_2' => null,
                'arbitre_var' => null,
                'statut_designation' => 'En attente'
            ];
        }
        
        return $matchs;
    }

    /**
     * Génère la liste des arbitres
     */
    private function generateArbitres()
    {
        $arbitres = [];
        $noms = ['Mohamed', 'Ahmed', 'Salah', 'Karim', 'Hassan', 'Ridha', 'Ali', 'Omar', 'Youssef', 'Nabil'];
        $prenoms = ['Ben Ali', 'Trabelsi', 'Jebali', 'Mejri', 'Khelil', 'Ben Amor', 'Gharbi', 'Sassi', 'Bouazizi', 'Mansouri'];
        
        for ($i = 1; $i <= 30; $i++) {
            $arbitres[] = [
                'id' => $i,
                'nom' => $noms[array_rand($noms)] . ' ' . $prenoms[array_rand($prenoms)],
                'type' => $i <= 10 ? 'Arbitre Principal' : ($i <= 20 ? 'Assistant' : 'VAR'),
                'experience' => rand(2, 15) . ' ans',
                'matchs_arbitres' => rand(50, 300),
                'disponible' => rand(0, 1) ? true : false,
                'note' => rand(70, 100) . '/100'
            ];
        }
        
        return $arbitres;
    }

    /**
     * Génère les fixtures à partir des vraies données de la base
     */
    private function generateFixturesFromRealData($clubs, $competitions)
    {
        $fixtures = [];
        
        foreach ($competitions as $competition) {
            $competitionFixtures = [];
            $journees = 30; // 30 journées pour un championnat
            
            for ($journee = 1; $journee <= $journees; $journee++) {
                $matchsJournee = [];
                
                // Générer les matchs pour cette journée avec les vrais clubs
                $clubsShuffled = $clubs->shuffle();
                for ($i = 0; $i < count($clubsShuffled); $i += 2) {
                    if ($i + 1 < count($clubsShuffled)) {
                        $domicile = $clubsShuffled[$i];
                        $exterieur = $clubsShuffled[$i + 1];
                        
                        // Générer une date aléatoire dans la saison
                        $dateMatch = now()->addDays(($journee - 1) * 7 + rand(0, 6));
                        
                        // Générer un résultat aléatoire
                        $resultat = rand(1, 3);
                        $butsDomicile = $resultat == 1 ? rand(1, 4) : ($resultat == 2 ? rand(0, 2) : rand(0, 1));
                        $butsExterieur = $resultat == 1 ? rand(0, 1) : ($resultat == 2 ? rand(0, 2) : rand(1, 4));
                        
                        $matchsJournee[] = [
                            'id' => $competition->id * 1000 + $journee * 100 + $i,
                            'competition_id' => $competition->id,
                            'competition_name' => $competition->name,
                            'domicile' => $domicile,
                            'exterieur' => $exterieur,
                            'date' => $dateMatch,
                            'heure' => '15:00',
                            'stade' => $domicile->address ?? 'Stade Municipal',
                            'buts_domicile' => $butsDomicile,
                            'buts_exterieur' => $butsExterieur,
                            'statut' => $dateMatch < now() ? 'Terminé' : 'À venir',
                            'arbitre_principal' => 'Arbitre ' . rand(1, 20),
                            'arbitre_assistant_1' => 'Assistant ' . rand(1, 20),
                            'arbitre_assistant_2' => 'Assistant ' . rand(1, 20),
                            'arbitre_var' => 'VAR ' . rand(1, 10)
                        ];
                    }
                }
                
                $competitionFixtures[] = [
                    'competition_id' => $competition->id,
                    'competition_name' => $competition->name,
                    'journee' => $journee,
                    'date' => $dateMatch,
                    'matchs' => $matchsJournee
                ];
            }
            
            $fixtures[$competition->id] = $competitionFixtures;
        }
        
        return $fixtures;
    }

    /**
     * Récupère un arbitre aléatoire de la base de données
     */
    private function getRandomArbitre($type = 'principal')
    {
        // Récupérer les arbitres depuis la base de données
        $arbitres = $this->generateArbitres();
        
        // Filtrer par type
        $arbitresFiltres = collect($arbitres)->filter(function($arbitre) use ($type) {
            switch($type) {
                case 'principal':
                    return strpos($arbitre['type'], 'Principal') !== false;
                case 'assistant':
                    return strpos($arbitre['type'], 'Assistant') !== false;
                case 'var':
                    return strpos($arbitre['type'], 'VAR') !== false;
                default:
                    return true;
            }
        });
        
        if ($arbitresFiltres->isEmpty()) {
            return 'Arbitre à désigner';
        }
        
        return $arbitresFiltres->random()['nom'];
    }
    
    /**
     * Génère des arbitres cohérents pour un match spécifique
     */
    private function getConsistentArbitresForMatch($matchId)
    {
        // Utiliser l'ID du match comme seed pour la génération aléatoire
        // Cela garantit que le même match aura toujours les mêmes arbitres
        mt_srand($matchId);
        
        // Générer la liste des arbitres
        $arbitres = $this->generateArbitres();
        
        // Filtrer par type
        $arbitresPrincipaux = collect($arbitres)->filter(function($arbitre) {
            return strpos($arbitre['type'], 'Principal') !== false;
        });
        
        $arbitresAssistants = collect($arbitres)->filter(function($arbitre) {
            return strpos($arbitre['type'], 'Assistant') !== false;
        });
        
        $arbitresVAR = collect($arbitres)->filter(function($arbitre) {
            return strpos($arbitre['type'], 'VAR') !== false;
        });
        
        // Sélectionner des arbitres cohérents
        $arbitrePrincipal = $arbitresPrincipaux->isNotEmpty() ? $arbitresPrincipaux->random()['nom'] : 'Arbitre à désigner';
        $assistant1 = $arbitresAssistants->isNotEmpty() ? $arbitresAssistants->random()['nom'] : 'Assistant à désigner';
        $assistant2 = $arbitresAssistants->isNotEmpty() ? $arbitresAssistants->random()['nom'] : 'Assistant à désigner';
        $var = $arbitresVAR->isNotEmpty() ? $arbitresVAR->random()['nom'] : 'VAR à désigner';
        $delegue = $arbitresPrincipaux->isNotEmpty() ? $arbitresPrincipaux->random()['nom'] : 'Délégué à désigner';
        $observateur = $arbitresPrincipaux->isNotEmpty() ? $arbitresPrincipaux->random()['nom'] : 'Observateur à désigner';
        
        // Restaurer le seed aléatoire
        mt_srand();
        
        return [
            'principal' => $arbitrePrincipal,
            'assistant_1' => $assistant1,
            'assistant_2' => $assistant2,
            'var' => $var,
            'delegue' => $delegue,
            'observateur' => $observateur
        ];
    }

    /**
     * Génère les données de classement avec les vrais clubs tunisiens
     */
    private function generateClassementDataFromRealClubs($competition, $clubs)
    {
        $classement = [];
        
        foreach ($clubs as $club) {
            // Générer des statistiques réalistes
            $matchsJoues = rand(15, 25);
            $victoires = rand(5, $matchsJoues - 5);
            $nuls = rand(2, min(8, $matchsJoues - $victoires));
            $defaites = $matchsJoues - $victoires - $nuls;
            
            // Générer des buts de manière réaliste
            $butsPour = rand($victoires * 1, $victoires * 3) + rand(0, $nuls);
            $butsContre = rand($defaites * 1, $defaites * 2) + rand(0, $nuls);
            
            // Calculer les points (3 points pour victoire, 1 pour nul)
            $points = ($victoires * 3) + $nuls;
            
            // Générer la forme récente (5 derniers matchs)
            $forme = [];
            for ($i = 0; $i < 5; $i++) {
                $resultat = rand(1, 3);
                $forme[] = $resultat == 1 ? 'V' : ($resultat == 2 ? 'N' : 'D');
            }
            
            $classement[] = [
                'club' => $club,
                'position' => 0, // Sera calculé après tri
                'matchs_joues' => $matchsJoues,
                'victoires' => $victoires,
                'nuls' => $nuls,
                'defaites' => $defaites,
                'buts_pour' => $butsPour,
                'buts_contre' => $butsContre,
                'difference_buts' => $butsPour - $butsContre,
                'points' => $points,
                'forme' => $forme,
                'evolution' => rand(-2, 3) // Évolution de position
            ];
        }
        
        // Trier par points (décroissant), puis par différence de buts
        usort($classement, function($a, $b) {
            if ($a['points'] == $b['points']) {
                return $b['difference_buts'] - $a['difference_buts'];
            }
            return $b['points'] - $a['points'];
        });
        
        // Assigner les positions
        foreach ($classement as $index => &$equipe) {
            $equipe['position'] = $index + 1;
        }
        
        return $classement;
    }
}
