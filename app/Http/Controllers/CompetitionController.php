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
use App\Models\User;
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

            $clubId = auth()->user()->club_id ?? null;

            $engagements = $competitions->map(function($competition) use ($clubId) {
                $totalMatches = $competition->matches()->count();
                $playedMatches = $competition->matches()->where('match_status', 'completed')->count();
                
                // Calculer les points reels du club connecte, bases sur les matchs termines
                $points = $this->calculateClubPoints($competition, $clubId);
                
                return [
                    'id' => $competition->id,
                    'nom' => $competition->name ?? __('competitions.unnamed_competition'),
                    'saison' => $competition->season ?? '2024-2025',
                    'statut' => $this->getFifaStatusLabel($competition->status),
                    'statut_raw' => $competition->status,
                    'type' => $this->getFifaTypeLabel($competition->type),
                    'categorie' => $this->getFifaCategoryLabel($competition->category),
                    'discipline' => $this->getFifaDisciplineLabel($competition->discipline),
                    'date_debut' => $competition->start_date?->format('Y-m-d') ?? 'N/A',
                    'date_fin' => $competition->end_date?->format('Y-m-d') ?? 'N/A',
                    'nb_matchs' => $totalMatches,
                    'matchs_joues' => $playedMatches,
                    'points' => $points,
                    'classement' => $this->calculateRanking($competition, $clubId),
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
                'fifaConnectRecord'
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
            // NOTE (audit factice -> reel, 2026-09) : cette methode generait
            // des matchs fictifs (dates, resultats, arbitres aleatoires) via
            // rand(). La table "matches" existe reellement (App\Models\GameMatch)
            // avec home_club_id/away_club_id, scores, arbitres. On l'utilise
            // desormais directement, scopee au club de l'utilisateur.
            $clubId = auth()->user()->club_id;

            $query = GameMatch::with(['competition', 'homeClub', 'awayClub'])
                ->where('status', '!=', 'cancelled');

            if ($clubId) {
                $query->where(function ($q) use ($clubId) {
                    $q->where('home_club_id', $clubId)->orWhere('away_club_id', $clubId);
                });
            }

            $realMatches = $query->orderBy('match_date')->limit(100)->get();

            $matchs = $realMatches->map(function ($match) use ($clubId) {
                $isHome = $clubId && $match->home_club_id == $clubId;
                $adversaire = $isHome
                    ? ($match->awayClub->name ?? 'N/A')
                    : ($match->homeClub->name ?? 'N/A');
                $isCompleted = in_array($match->status, ['completed', 'finished']) || $match->match_status === 'completed';

                return [
                    'id' => $match->id,
                    'date' => $match->match_date?->format('Y-m-d') ?? 'N/A',
                    'heure' => $match->kickoff_time?->format('H:i') ?? 'N/A',
                    'competition' => $match->competition->name ?? 'N/A',
                    'adversaire' => $adversaire,
                    'lieu' => $match->venue ?? $match->stadium ?? 'Non renseigné',
                    'arbitre_principal' => $match->referee ?? 'Non désigné',
                    'arbitre_assistant_1' => $match->assistant_referee_1 ?? 'Non désigné',
                    'arbitre_assistant_2' => $match->assistant_referee_2 ?? 'Non désigné',
                    'statut' => $isCompleted ? 'Terminé' : 'Programmé',
                    'resultat' => $isCompleted ? ($match->home_score ?? 0) . '-' . ($match->away_score ?? 0) : null
                ];
            });

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
        try {
            // NOTE (audit factice -> reel, 2026-09) : 3 feuilles de match
            // fictives ("FC Ville vs Notre Club"...) remplacees par les
            // vraies feuilles (App\Models\MatchSheet), scopees au club de
            // l'utilisateur via matches.home_club_id/away_club_id.
            $clubId = auth()->user()->club_id;

            $query = \App\Models\MatchSheet::with(['match.homeTeam', 'match.awayTeam']);

            if ($clubId) {
                $query->whereHas('match', function ($q) use ($clubId) {
                    $q->where('home_club_id', $clubId)->orWhere('away_club_id', $clubId);
                });
            }

            $sheets = $query->orderByDesc('created_at')->limit(50)->get();

            $feuilles = $sheets->map(function ($sheet) {
                $match = $sheet->match;
                $homeName = $match?->homeTeam->name ?? 'N/A';
                $awayName = $match?->awayTeam->name ?? 'N/A';

                $statutLabels = [
                    'draft' => 'À préparer',
                    'submitted' => 'Soumise',
                    'validated' => 'Validée',
                    'rejected' => 'Rejetée',
                ];
                $statut = $statutLabels[$sheet->status] ?? ucfirst($sheet->status ?? 'N/A');
                if (($sheet->status ?? 'draft') === 'draft' && $match?->match_date && $match->match_date->isPast()) {
                    $statut = 'En retard';
                }

                $rosterCount = is_array($sheet->home_team_roster) ? count($sheet->home_team_roster) : 0;

                return [
                    'id' => $sheet->id,
                    'match' => "{$homeName} vs {$awayName}",
                    'date' => $match?->match_date?->format('Y-m-d') ?? 'N/A',
                    'statut' => $statut,
                    'effectif_disponible' => $rosterCount,
                    'effectif_selectionne' => ($sheet->status ?? 'draft') !== 'draft' ? $rosterCount : 0
                ];
            });

            return view('competitions.club.feuilles-match', compact('feuilles'));
        } catch (\Exception $e) {
            $feuilles = collect([]);
            return view('competitions.club.feuilles-match', compact('feuilles'));
        }
    }

    /**
     * Discipline & Notifications - Suivi cartons/suspensions/amendes
     */
    public function clubDiscipline(): View
    {
        try {
            // NOTE (audit factice -> reel, 2026-09) : 3 sanctions fictives
            // ("Jean Dupont", "Pierre Martin", "Ahmed Ben Ali") remplacees par
            // les vraies sanctions issues des rapports d'arbitres
            // (referee_reports), meme source que
            // associationDisciplineSanctions(), filtree sur les equipes du
            // club de l'utilisateur.
            $clubId = auth()->user()->club_id;
            $teamNames = $clubId
                ? \App\Models\Team::where('club_id', $clubId)->pluck('name')->all()
                : [];

            $sanctions = collect();

            $reportsQuery = \DB::table('referee_reports')->orderBy('match_date', 'desc');
            if ($clubId) {
                $reportsQuery->where(function ($q) use ($teamNames) {
                    $q->whereIn('home_team', $teamNames)->orWhereIn('away_team', $teamNames);
                });
            }
            $refereeReports = $reportsQuery->get();

            $sanctionId = 1;

            foreach ($refereeReports as $report) {
                if ($report->yellow_cards) {
                    $yellowCards = json_decode($report->yellow_cards, true);
                    if (is_array($yellowCards)) {
                        foreach ($yellowCards as $card) {
                            $sanctions->push([
                                'id' => $sanctionId++,
                                'joueur' => $card['player'] ?? __('competitions.discipline_page.unknown_player'),
                                'match' => trim(($report->home_team ?? '') . ' vs ' . ($report->away_team ?? '')),
                                'date' => $report->match_date ? \Carbon\Carbon::parse($report->match_date)->format('Y-m-d') : date('Y-m-d'),
                                'type' => 'Carton Jaune',
                                'motif' => $card['reason'] ?? __('competitions.discipline_page.unspecified'),
                                'statut' => 'Validé',
                                'statut_code' => 'validated',
                                'suspension_days' => 0,
                                'amende' => $this->getFineAmountByType('Carton Jaune')
                            ]);
                        }
                    }
                }

                if ($report->red_cards) {
                    $redCards = json_decode($report->red_cards, true);
                    if (is_array($redCards)) {
                        foreach ($redCards as $card) {
                            $suspensionDays = $this->getSuspensionDaysByType('Carton Rouge');
                            $sanctions->push([
                                'id' => $sanctionId++,
                                'joueur' => $card['player'] ?? __('competitions.discipline_page.unknown_player'),
                                'match' => trim(($report->home_team ?? '') . ' vs ' . ($report->away_team ?? '')),
                                'date' => $report->match_date ? \Carbon\Carbon::parse($report->match_date)->format('Y-m-d') : date('Y-m-d'),
                                'type' => 'Carton Rouge',
                                'motif' => $card['reason'] ?? __('competitions.discipline_page.unspecified'),
                                'statut' => $suspensionDays > 0 ? "Suspendu {$suspensionDays} matchs" : 'Validé',
                                'statut_code' => $suspensionDays > 0 ? 'suspended' : 'validated',
                                'suspension_days' => $suspensionDays,
                                'amende' => $this->getFineAmountByType('Carton Rouge')
                            ]);
                        }
                    }
                }
            }

            return view('competitions.club.discipline', compact('sanctions'));
        } catch (\Exception $e) {
            $sanctions = collect([]);
            return view('competitions.club.discipline', compact('sanctions'));
        }
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
            // NOTE (audit factice -> reel, 2026-09) : cette methode ignorait
            // l'utilisateur connecte et affichait toujours l'association
            // tunisienne codee en dur ("%Tunis%"), avec des matchs joues et
            // une prochaine journee tirees au hasard (rand()). Corrige pour
            // utiliser la vraie association de l'utilisateur et les vrais
            // matchs (App\Models\GameMatch).
            $user = auth()->user();
            $associationId = $user->association_id;

            $association = $associationId ? Association::find($associationId) : null;

            if (!$association) {
                throw new \Exception('Association non trouvée pour cet utilisateur');
            }

            $competitionsData = Competition::where('association_id', $association->id)
                ->where('status', '!=', 'cancelled')
                ->with(['association'])
                ->orderBy('start_date', 'desc')
                ->get();

            $associationClubs = Club::where('association_id', $association->id)->get();

            $competitions = $competitionsData->map(function ($competition) use ($associationClubs) {
                $nbClubs = $associationClubs->count();
                $totalMatches = GameMatch::where('competition_id', $competition->id)->count();
                $playedMatches = GameMatch::where('competition_id', $competition->id)
                    ->whereIn('status', ['completed', 'finished'])
                    ->count();

                $nextMatch = GameMatch::where('competition_id', $competition->id)
                    ->where('match_date', '>=', now())
                    ->orderBy('match_date')
                    ->first();

                return [
                    'id' => $competition->id,
                    'nom' => $competition->name,
                    'saison' => $competition->season ?? 'N/A',
                    'statut' => $competition->status ?? 'Inconnu',
                    'nb_clubs' => $nbClubs,
                    'nb_matchs' => $totalMatches,
                    'matchs_joues' => $playedMatches,
                    'prochaine_journee' => $nextMatch?->match_date?->format('Y-m-d') ?? 'Non programmée',
                    'association' => $competition->association,
                    'start_date' => $competition->start_date,
                    'end_date' => $competition->end_date,
                    'type' => $competition->type ?? 'Championnat'
                ];
            });

            $tunisianAssociation = $association;

            return view('competitions.association.supervision', compact('competitions', 'tunisianAssociation'));

        } catch (\Exception $e) {
            // NOTE (audit factice -> reel, 2026-09) : le repli affichait une
            // fausse competition "Championnat Tunisien U19" avec 380 matchs.
            // Repli honnete : liste vide plutot que donnees inventees.
            $competitions = collect([]);
            $tunisianAssociation = $user->association ?? null;

            return view('competitions.association.supervision', compact('competitions', 'tunisianAssociation'));
        }
    }

    /**
     * Engagements des clubs - Vue de contrôle
     */
    public function associationEngagementsClubs(): View
    {
        try {
            // Récupérer tous les matchs avec leurs équipes et clubs
            $matches = GameMatch::with([
                'homeTeam.club', 
                'awayTeam.club', 
                'competition'
            ])->get();
            
            // Extraire tous les clubs uniques des matchs
            $clubsData = collect();
            
            // Traiter les équipes domicile
            $homeClubs = $matches->pluck('homeTeam.club')->filter()->unique('id');
            foreach ($homeClubs as $club) {
                if ($club) {
                    $clubsData->put($club->id, [
                        'club' => $club,
                        'matches' => collect(),
                        'competitions' => collect()
                    ]);
                }
            }
            
            // Traiter les équipes extérieures
            $awayClubs = $matches->pluck('awayTeam.club')->filter()->unique('id');
            foreach ($awayClubs as $club) {
                if ($club) {
                    if (!$clubsData->has($club->id)) {
                        $clubsData->put($club->id, [
                            'club' => $club,
                            'matches' => collect(),
                            'competitions' => collect()
                        ]);
                    }
                }
            }
            
            // Remplir les données pour chaque club
            foreach ($matches as $match) {
                // Club domicile
                if ($match->homeTeam && $match->homeTeam->club) {
                    $clubId = $match->homeTeam->club->id;
                    if ($clubsData->has($clubId)) {
                        $clubsData[$clubId]['matches']->push($match);
                        if ($match->competition) {
                            $clubsData[$clubId]['competitions']->push($match->competition);
                        }
                    }
                }
                
                // Club extérieur
                if ($match->awayTeam && $match->awayTeam->club) {
                    $clubId = $match->awayTeam->club->id;
                    if ($clubsData->has($clubId)) {
                        $clubsData[$clubId]['matches']->push($match);
                        if ($match->competition) {
                            $clubsData[$clubId]['competitions']->push($match->competition);
                        }
                    }
                }
            }
            
            // Transformer en format final
            $clubs = $clubsData->map(function($data) {
                $club = $data['club'];
                $matches = $data['matches']->unique('id');
                $competitions = $data['competitions']->unique('id');
                
                $totalMatches = $matches->count();
                $completedMatches = $matches->where('status', 'completed')->count();
                $lastMatch = $matches->sortByDesc('updated_at')->first();
                
                return [
                    'id' => $club->id,
                    'nom' => $club->short_name ?? $club->name,
                    'competition' => $competitions->first()->name ?? 'Aucune compétition',
                    'statut_engagement' => $competitions->isNotEmpty() ? 'Engagé' : 'Non engagé',
                    'feuilles_soumises' => $totalMatches,
                    'feuilles_validees' => $completedMatches,
                    'derniere_activite' => $lastMatch ? $lastMatch->updated_at->format('Y-m-d') : 'Aucune activité',
                    'competitions_count' => $competitions->count(),
                    'total_matches' => $totalMatches,
                    'completed_matches' => $completedMatches,
                    'teams_count' => $club->teams->count() ?? 0,
                    'club' => $club
                ];
            })->values();

            return view('competitions.association.engagements-clubs', compact('clubs'));
            
        } catch (\Exception $e) {
            return view('errors.database', [
                'error' => 'Erreur lors de la récupération des engagements',
                'message' => 'Impossible de charger les données des engagements: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Calendrier global - Planning centralisé
     */
    public function associationCalendrierGlobal(): View
    {
        // Récupérer les vrais matchs depuis la base de données
        $matchs = \App\Models\GameMatch::with([
            'competition',
            'homeTeam.club',
            'awayTeam.club',
            'officials' => function($query) {
                $query->where('role', 'main_referee');
            }
        ])
        ->orderBy('match_date', 'asc')
        ->orderBy('match_time', 'asc')
        ->get()
        ->map(function($match) {
            return [
                'id' => $match->id,
                'date' => $match->match_date ? \Carbon\Carbon::parse($match->match_date)->format('Y-m-d') : null,
                'heure' => $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('H:i') : null,
                'competition' => $match->competition->name ?? 'Compétition inconnue',
                'domicile' => $match->homeTeam->club->short_name ?? $match->homeTeam->club->name ?? 'Club domicile',
                'exterieur' => $match->awayTeam->club->short_name ?? $match->awayTeam->club->name ?? 'Club extérieur',
                'lieu' => $match->venue ?? 'Lieu à définir',
                'arbitre_principal' => $match->officials->first() ? $match->officials->first()->name : 'Arbitre à désigner',
                'statut' => $this->getMatchStatus($match->status),
                'reprogrammable' => in_array($match->status, ['scheduled', 'postponed']),
                'score_home' => $match->home_score,
                'score_away' => $match->away_score,
                'round' => $match->round
            ];
        });

        return view('competitions.association.calendrier-global', compact('matchs'));
    }

    /**
     * Convertir le statut de match en français
     */
    private function getMatchStatus($status): string
    {
        $statusMap = [
            'scheduled' => 'Programmé',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'postponed' => 'Reporté',
            'cancelled' => 'Annulé',
            'suspended' => 'Suspendu'
        ];

        return $statusMap[$status] ?? 'Statut inconnu';
    }

    /**
     * Résultats & Classements - Compilation auto
     */
    public function associationResultatsClassements(): View
    {
        try {
            // Récupérer les vraies données de compétitions et leurs classements
            $competitions = \App\Models\Competition::with(['clubs'])
                ->whereIn('status', ['published', 'active'])
                ->get();

            $classements = collect();

            foreach ($competitions as $competition) {
                // Calculer les vraies statistiques basées sur les matchs réels
                $clubsStats = collect();
                
                // Récupérer tous les clubs participants
                $participatingClubs = $competition->clubs;
                
                foreach ($participatingClubs as $club) {
                    // Récupérer tous les matchs de ce club dans cette compétition
                    $matches = \App\Models\GameMatch::where('competition_id', $competition->id)
                        ->where(function($query) use ($club) {
                            // Chercher les équipes de ce club
                            $teamIds = \App\Models\Team::where('club_id', $club->id)->pluck('id');
                            $query->whereIn('home_team_id', $teamIds)
                                  ->orWhereIn('away_team_id', $teamIds);
                        })
                        ->where('status', 'completed')
                        ->whereNotNull('home_score')
                        ->whereNotNull('away_score')
                        ->get();

                    $victoires = 0;
                    $nuls = 0;
                    $defaites = 0;
                    $buts_pour = 0;
                    $buts_contre = 0;

                    foreach ($matches as $match) {
                        // Déterminer si le club joue à domicile ou à l'extérieur
                        $homeTeam = \App\Models\Team::find($match->home_team_id);
                        $awayTeam = \App\Models\Team::find($match->away_team_id);
                        
                        $isHomeTeam = $homeTeam && $homeTeam->club_id == $club->id;
                        $isAwayTeam = $awayTeam && $awayTeam->club_id == $club->id;
                        
                        if ($isHomeTeam) {
                            // Club à domicile
                            $buts_pour += $match->home_score;
                            $buts_contre += $match->away_score;
                            
                            if ($match->home_score > $match->away_score) {
                                $victoires++;
                            } elseif ($match->home_score == $match->away_score) {
                                $nuls++;
                            } else {
                                $defaites++;
                            }
                        } elseif ($isAwayTeam) {
                            // Club à l'extérieur
                            $buts_pour += $match->away_score;
                            $buts_contre += $match->home_score;
                            
                            if ($match->away_score > $match->home_score) {
                                $victoires++;
                            } elseif ($match->away_score == $match->home_score) {
                                $nuls++;
                            } else {
                                $defaites++;
                            }
                        }
                    }

                    $points = ($victoires * 3) + ($nuls * 1);
                    $difference = $buts_pour - $buts_contre;

                    $clubsStats->push([
                        'club' => $club,
                        'points' => $points,
                        'matchs' => $matches->count(),
                        'victoires' => $victoires,
                        'nuls' => $nuls,
                        'defaites' => $defaites,
                        'buts_pour' => $buts_pour,
                        'buts_contre' => $buts_contre,
                        'difference' => $difference
                    ]);
                }

                // Trier par points (décroissant) puis par différence de buts
                $clubsStats = $clubsStats->sortByDesc(function($club) {
                    return [$club['points'], $club['difference']];
                })->values();

                if ($clubsStats->isNotEmpty()) {
                    $equipes = collect();
                    foreach ($clubsStats as $index => $clubStats) {
                        $equipes->push([
                            'position' => $index + 1,
                            'nom' => $clubStats['club']->short_name ?? $clubStats['club']->name,
                            'points' => $clubStats['points'],
                            'matchs' => $clubStats['matchs'],
                            'victoires' => $clubStats['victoires'],
                            'nuls' => $clubStats['nuls'],
                            'defaites' => $clubStats['defaites'],
                            'buts_pour' => $clubStats['buts_pour'],
                            'buts_contre' => $clubStats['buts_contre'],
                            'difference' => $clubStats['difference']
                        ]);
                    }

                    $classements->push([
                        'competition' => $competition->name,
                        'equipes' => $equipes->toArray()
                    ]);
                }
            }

            // Si aucune donnée réelle, retourner une collection vide
            // Pas de données de démonstration pour éviter les données incorrectes

            return view('competitions.association.resultats-classements', compact('classements'));

        } catch (\Exception $e) {
            // En cas d'erreur, retourner une collection vide
            $classements = collect();
            return view('competitions.association.resultats-classements', compact('classements'));
        }
    }

    /**
     * Discipline & Sanctions - Validation des sanctions
     */
    public function associationDisciplineSanctions(): View
    {
        try {
            // Récupérer les vraies données de sanctions depuis les rapports d'arbitres
            $sanctions = collect();
            
            // Récupérer tous les rapports d'arbitres avec les matchs associés
            $refereeReports = \DB::table('referee_reports')
                ->orderBy('match_date', 'desc')
                ->get();
            
            $sanctionId = 1;
            
            foreach ($refereeReports as $report) {
                // Traiter les cartons jaunes
                if ($report->yellow_cards) {
                    $yellowCards = json_decode($report->yellow_cards, true);
                    if (is_array($yellowCards)) {
                        foreach ($yellowCards as $card) {
                            $sanctions->push([
                                'id' => $sanctionId++,
                                'joueur' => $card['player'] ?? 'Joueur inconnu',
                                'club' => $this->getClubFromReport($report),
                                'match' => $report->competition_name ?? 'Match inconnu',
                                'date' => $report->match_date ? \Carbon\Carbon::parse($report->match_date)->format('Y-m-d') : date('Y-m-d'),
                                'type' => 'Carton Jaune',
                                'motif' => $card['reason'] ?? 'Non spécifié',
                                'statut' => 'Validé',
                                'amende' => $this->getFineAmountByType('Carton Jaune'),
                                'suspension' => $this->getSuspensionDaysByType('Carton Jaune'),
                                'minute' => $card['minute'] ?? null
                            ]);
                        }
                    }
                }
                
                // Traiter les cartons rouges
                if ($report->red_cards) {
                    $redCards = json_decode($report->red_cards, true);
                    if (is_array($redCards)) {
                        foreach ($redCards as $card) {
                            $sanctions->push([
                                'id' => $sanctionId++,
                                'joueur' => $card['player'] ?? 'Joueur inconnu',
                                'club' => $this->getClubFromReport($report),
                                'match' => $report->competition_name ?? 'Match inconnu',
                                'date' => $report->match_date ? \Carbon\Carbon::parse($report->match_date)->format('Y-m-d') : date('Y-m-d'),
                                'type' => 'Carton Rouge',
                                'motif' => $card['reason'] ?? 'Non spécifié',
                                'statut' => 'Validé',
                                'amende' => $this->getFineAmountByType('Carton Rouge'),
                                'suspension' => $this->getSuspensionDaysByType('Carton Rouge'),
                                'minute' => $card['minute'] ?? null
                            ]);
                        }
                    }
                }
                
                // Traiter les incidents disciplinaires
                if ($report->disciplinary_incidents) {
                    $sanctions->push([
                        'id' => $sanctionId++,
                        'joueur' => 'Incident disciplinaire',
                        'club' => $this->getClubFromReport($report),
                        'match' => $report->competition_name ?? 'Match inconnu',
                        'date' => $report->match_date ? \Carbon\Carbon::parse($report->match_date)->format('Y-m-d') : date('Y-m-d'),
                        'type' => 'Incident Disciplinaire',
                        'motif' => $report->disciplinary_incidents,
                        'statut' => 'En attente',
                        'amende' => 500,
                        'suspension' => 3,
                        'minute' => null
                    ]);
                }
            }
            
            // Si pas de sanctions, afficher un message
            if ($sanctions->isEmpty()) {
                $sanctions = collect([
                    [
                        'id' => 0,
                        'joueur' => 'Aucune sanction',
                        'club' => 'N/A',
                        'match' => 'N/A',
                        'date' => date('Y-m-d'),
                        'type' => 'Aucune',
                        'motif' => 'Aucune sanction enregistrée dans la base de données',
                        'statut' => 'Aucune',
                        'amende' => 0,
                        'suspension' => 0,
                        'minute' => null
                    ]
                ]);
            }

            return view('competitions.association.discipline-sanctions', compact('sanctions'));
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données d'erreur
            $sanctions = collect([
                [
                    'id' => 0,
                    'joueur' => 'Erreur',
                    'club' => 'N/A',
                    'match' => 'N/A',
                    'date' => date('Y-m-d'),
                    'type' => 'Erreur',
                    'motif' => 'Erreur lors du chargement des données: ' . $e->getMessage(),
                    'statut' => 'Erreur',
                    'amende' => 0,
                    'suspension' => 0,
                    'minute' => null
                ]
            ]);
            
            return view('competitions.association.discipline-sanctions', compact('sanctions'));
        }
    }
    
    /**
     * Obtenir le nom d'une équipe du match
     */
    private function getMatchTeamName($match, $isAway = false)
    {
        if ($isAway) {
            return $match->awayTeam->name ?? 'Équipe extérieure';
        } else {
            return $match->homeTeam->name ?? 'Équipe domicile';
        }
    }
    
    /**
     * Obtenir le nom du club depuis un rapport d'arbitre
     */
    private function getClubFromReport($report)
    {
        // Essayer de récupérer le club depuis le match associé
        try {
            $match = \DB::table('matches')->where('id', $report->match_id)->first();
            if ($match) {
                // Récupérer les équipes du match
                $homeTeam = \DB::table('teams')->where('id', $match->home_team_id)->first();
                $awayTeam = \DB::table('teams')->where('id', $match->away_team_id)->first();
                
                if ($homeTeam && $awayTeam) {
                    // Récupérer les noms des clubs
                    $homeClub = \DB::table('clubs')->where('id', $homeTeam->club_id)->first();
                    $awayClub = \DB::table('clubs')->where('id', $awayTeam->club_id)->first();
                    
                    if ($homeClub && $awayClub) {
                        return $homeClub->name . ' / ' . $awayClub->name;
                    }
                }
            }
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un club générique
        }
        
        // NOTE (audit factice -> reel, 2026-09) : ce fallback retournait
        // toujours "Club Tunisien" en dur, quelle que soit la competition.
        // On retourne desormais un etat honnete quand le club reel n'a pas
        // pu etre resolu.
        return 'Club non identifié';
    }
    
    /**
     * Obtenir le montant de l'amende par type
     */
    private function getFineAmountByType($type)
    {
        $fines = [
            'Carton Jaune' => 0,
            'Carton Rouge' => 150,
            'Carton Jaune + Rouge' => 200
        ];
        
        return $fines[$type] ?? 0;
    }
    
    /**
     * Obtenir le nombre de jours de suspension par type
     */
    private function getSuspensionDaysByType($type)
    {
        $suspensions = [
            'Carton Jaune' => 0,
            'Carton Rouge' => 3,
            'Carton Jaune + Rouge' => 4
        ];
        
        return $suspensions[$type] ?? 0;
    }
    
    /**
     * Obtenir le club du joueur
     */
    private function getPlayerClub($event, $match)
    {
        // Logique pour déterminer le club du joueur basée sur l'événement
        if ($event->team_id) {
            $team = \App\Models\Team::with('club')->find($event->team_id);
            return $team->club->name ?? 'Club inconnu';
        }
        
        // Fallback: déterminer par le match
        if ($event->player_id) {
            $player = \App\Models\Player::with('team.club')->find($event->player_id);
            return $player->team->club->name ?? 'Club inconnu';
        }
        
        return 'Club inconnu';
    }
    
    /**
     * Obtenir la description du match
     */
    private function getMatchDescription($match)
    {
        $homeTeam = $match->homeTeam->name ?? 'Équipe domicile';
        $awayTeam = $match->awayTeam->name ?? 'Équipe extérieure';
        return "{$homeTeam} vs {$awayTeam}";
    }
    
    /**
     * Obtenir le type de carton
     */
    private function getCardType($eventType)
    {
        $types = [
            'yellow_card' => 'Carton Jaune',
            'red_card' => 'Carton Rouge',
            'yellow_red_card' => 'Carton Jaune + Rouge'
        ];
        
        return $types[$eventType] ?? 'Carton inconnu';
    }
    
    /**
     * Obtenir le statut de la sanction
     */
    private function getSanctionStatus($event)
    {
        // Logique pour déterminer le statut basée sur l'événement
        if ($event->event_type === 'red_card') {
            return 'Suspendu';
        } elseif ($event->event_type === 'yellow_red_card') {
            return 'Suspendu';
        } else {
            return 'Validé';
        }
    }
    
    /**
     * Obtenir le montant de l'amende
     */
    private function getFineAmount($eventType)
    {
        $fines = [
            'yellow_card' => 0,
            'red_card' => 150,
            'yellow_red_card' => 200
        ];
        
        return $fines[$eventType] ?? 0;
    }
    
    /**
     * Obtenir le nombre de jours de suspension
     */
    private function getSuspensionDays($eventType)
    {
        $suspensions = [
            'yellow_card' => 0,
            'red_card' => 3,
            'yellow_red_card' => 4
        ];
        
        return $suspensions[$eventType] ?? 0;
    }

    /**
     * Rapports & Statistiques - Export PDF/Excel
     */
    public function associationRapportsStatistiques(): View
    {
        try {
            // Récupérer les compétitions pour générer des rapports dynamiques
            $competitions = \App\Models\Competition::with(['matches'])
                ->where('status', '!=', 'cancelled')
                ->get();
            
            $rapports = collect();
            
            foreach ($competitions as $competition) {
                $matchCount = $competition->matches->count();
                $completedMatches = $competition->matches->where('status', 'completed')->count();
                
                // Générer différents types de rapports pour chaque compétition
                $rapports->push([
                    'id' => $competition->id * 10 + 1,
                    'nom' => 'Classement ' . $competition->name,
                    'type' => 'Classement',
                    'competition' => $competition->name,
                    'date_generation' => now()->format('Y-m-d'),
                    'statut' => $completedMatches > 0 ? 'Disponible' : 'En attente',
                    'formats' => ['PDF', 'Excel'],
                    'details' => "{$completedMatches} matchs terminés sur {$matchCount}"
                ]);
                
                $rapports->push([
                    'id' => $competition->id * 10 + 2,
                    'nom' => 'Statistiques ' . $competition->name,
                    'type' => 'Statistiques',
                    'competition' => $competition->name,
                    'date_generation' => now()->format('Y-m-d'),
                    'statut' => $matchCount > 0 ? 'Disponible' : 'En attente',
                    'formats' => ['PDF', 'Excel'],
                    'details' => "Statistiques détaillées des {$matchCount} matchs"
                ]);
                
                $rapports->push([
                    'id' => $competition->id * 10 + 3,
                    'nom' => 'Rapport Discipline ' . $competition->name,
                    'type' => 'Discipline',
                    'competition' => $competition->name,
                    'date_generation' => now()->format('Y-m-d'),
                    'statut' => $completedMatches > 0 ? 'Disponible' : 'En attente',
                    'formats' => ['PDF'],
                    'details' => "Sanctions et cartons des matchs terminés"
                ]);
                
                // Rapport financier si il y a des matchs
                if ($matchCount > 0) {
                    $rapports->push([
                        'id' => $competition->id * 10 + 4,
                        'nom' => 'Rapport Financier ' . $competition->name,
                        'type' => 'Financier',
                        'competition' => $competition->name,
                        'date_generation' => now()->format('Y-m-d'),
                        'statut' => 'Disponible',
                        'formats' => ['PDF', 'Excel'],
                        'details' => "Amendes et sanctions financières"
                    ]);
                }
            }
            
            // Si pas de compétitions, afficher un message
            if ($rapports->isEmpty()) {
                $rapports = collect([
                    [
                        'id' => 0,
                        'nom' => 'Aucun rapport disponible',
                        'type' => 'Aucun',
                        'competition' => 'N/A',
                        'date_generation' => now()->format('Y-m-d'),
                        'statut' => 'Aucun',
                        'formats' => [],
                        'details' => 'Aucune compétition trouvée dans la base de données'
                    ]
                ]);
            }

            return view('competitions.association.rapports-statistiques', compact('rapports'));
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données d'erreur
            $rapports = collect([
                [
                    'id' => 0,
                    'nom' => 'Erreur de chargement',
                    'type' => 'Erreur',
                    'competition' => 'N/A',
                    'date_generation' => now()->format('Y-m-d'),
                    'statut' => 'Erreur',
                    'formats' => [],
                    'details' => 'Erreur lors du chargement des données: ' . $e->getMessage()
                ]
            ]);
            
            return view('competitions.association.rapports-statistiques', compact('rapports'));
        }
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
                    'statut' => $this->getFifaStatusLabel($competition->status),
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
        // Récupérer les standings depuis la table standings.
        // NOTE (audit factice -> reel, 2026-09) : Standing::club et les
        // attributs matches_played/wins/draws/losses n'existent pas
        // (le modèle Standing expose team()/played/won/drawn/lost) ; ces
        // champs se résolvaient donc toujours à null. Corrigé pour
        // utiliser la vraie relation (Standing -> Team -> Club) et les
        // vraies colonnes.
        $standings = Standing::with(['team.club', 'competition'])
            ->where('competition_id', $competitionId)
            ->orderBy('position', 'asc')
            ->get()
            ->map(function($standing) {
                return [
                    'position' => $standing->position,
                    'nom' => $standing->team?->club?->name ?? $standing->team?->name ?? 'N/A',
                    'points' => $standing->points,
                    'matchs' => $standing->played,
                    'victoires' => $standing->won,
                    'nuls' => $standing->drawn,
                    'defaites' => $standing->lost,
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
        $players = Player::with(['club', 'fifaConnectRecord'])
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
        $labels = __('competitions.fifa_status');
        return $labels[$status] ?? ucfirst($status ?? __('competitions.unknown'));
    }

    /**
     * Obtenir le libellé du type FIFA Connect
     */
    private function getFifaTypeLabel($type): string
    {
        $labels = __('competitions.fifa_type');
        return $labels[$type] ?? ucfirst($type ?? __('competitions.unknown'));
    }

    /**
     * Obtenir le libellé de la catégorie FIFA Connect (selon les standards FIFA)
     */
    private function getFifaCategoryLabel($category): string
    {
        $labels = __('competitions.fifa_category');
        return $labels[$category] ?? ucfirst($category ?? __('competitions.unknown'));
    }

    /**
     * Obtenir le libellé de la discipline FIFA Connect
     */
    private function getFifaDisciplineLabel($discipline): string
    {
        $labels = __('competitions.fifa_discipline');
        return $labels[$discipline] ?? ucfirst($discipline ?? __('competitions.unknown'));
    }

    /**
     * Obtenir le libellé du statut de match
     */
    private function getMatchStatusLabel($status): string
    {
        $labels = __('competitions.match_status_label');
        return $labels[$status] ?? ucfirst($status ?? __('competitions.unknown'));
    }

    /**
     * Calculer les points d'un club dans une compétition, à partir des vrais
     * matchs terminés de la compétition (colonnes home_club_id/away_club_id,
     * home_score/away_score).
     *
     * NOTE (audit factice -> reel, 2026-09) : $clubId était auparavant
     * codé en dur à 1 ("à adapter selon l'ID du club"), ce qui affichait
     * systématiquement les points du club n°1 pour tout le monde. Le club
     * réellement concerné (celui de l'utilisateur connecté) est désormais
     * passé en paramètre.
     */
    private function calculateClubPoints($competition, ?int $clubId): int
    {
        if (!$clubId) {
            return 0;
        }

        try {
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
     * Calculer le classement réel d'un club dans une compétition : les
     * points de chaque club inscrit sont calculés à partir des vrais
     * matchs terminés, puis triés pour situer le club demandé.
     *
     * NOTE (audit factice -> reel, 2026-09) : l'ancienne version ne
     * dépendait pas des vraies données ("Logique simplifiée") et
     * dérivait un rang arbitraire à partir du seul nombre de points du
     * club (totalClubs - points / 3), sans comparer aux autres clubs.
     */
    private function calculateRanking($competition, ?int $clubId): int
    {
        try {
            $clubs = $competition->clubs;
            $totalClubs = $clubs->count();
            if ($totalClubs === 0 || !$clubId) {
                return $totalClubs > 0 ? $totalClubs : 1;
            }

            $pointsByClub = $clubs->mapWithKeys(function ($club) use ($competition) {
                return [$club->id => $this->calculateClubPoints($competition, $club->id)];
            });

            $sorted = $pointsByClub->sortDesc()->keys()->values();
            $position = $sorted->search($clubId);

            if ($position === false) {
                return $totalClubs;
            }

            return $position + 1;
        } catch (\Exception $e) {
            return 1;
        }
    }

    // NOTE (audit factice -> reel, 2026-09) : le bloc "ACTIONS" (7 methodes :
    // soumettreFeuilleMatch, verifierEffectif, validerFeuilleMatch,
    // reprogrammerMatch, mettreAJourResultat, ajouterSanction, exportRapport)
    // a ete supprime. Chacune se limitait a un commentaire "// Logique de ..."
    // suivi d'un retour JSON de succes fixe, sans aucune logique reelle, et
    // aucune n'etait reliee a une route ni appelee depuis nulle part dans
    // l'application (verifie par recherche exhaustive).

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
            return __('competitions.license_age_labels.not_eligible');
        } elseif ($age < 15) {
            return __('competitions.license_age_labels.u13');
        } elseif ($age < 17) {
            return __('competitions.license_age_labels.u15');
        } elseif ($age < 20) {
            return __('competitions.license_age_labels.u17');
        } elseif ($age < 23) {
            return __('competitions.license_age_labels.u20');
        } else {
            return __('competitions.license_age_labels.senior');
        }
    }

    /**
     * Obtenir le libellé du type de licence FIFA Connect
     */
    private function getFifaLicenseTypeLabel($type): string
    {
        $labels = __('competitions.fifa_license_type');
        return $labels[$type] ?? ucfirst($type ?? __('competitions.unknown'));
    }

    /**
     * Classement des compétitions - Accessible aux clubs et associations
     *
     * NOTE (audit factice -> reel, 2026-09) : cette méthode ciblait en dur
     * une "association tunisienne" (Association::where('name', 'like',
     * '%Tunis%')) pour tout le monde, avec un classement calculé par
     * rand() (victoires, buts, forme, évolution) même dans le "chemin
     * normal" (generateClassementDataFromRealClubs n'était pas plus
     * réel que le générateur de démonstration), et un jeu de 20 clubs
     * tunisiens fictifs + une compétition fictive en cas d'erreur.
     * Remplacé par les compétitions réelles (celles de l'association de
     * l'utilisateur connecté, ou toutes les compétitions actives si
     * l'utilisateur n'est pas rattaché à une association) et un
     * classement calculé à partir des vrais matchs terminés.
     */
    public function classement(): View
    {
        try {
            $user = auth()->user();

            $query = Competition::with(['association', 'clubs'])
                ->whereIn('status', ['published', 'active']);

            if ($user && $user->association_id) {
                $query->where('association_id', $user->association_id);
            }

            $competitions = $query->orderBy('start_date', 'desc')->get();

            $classements = [];
            $matchsInfo = [];

            foreach ($competitions as $competition) {
                $classements[$competition->id] = $this->generateClassementDataFromMatches($competition, $competition->clubs);
                $matchsInfo[$competition->id] = [
                    'joues' => $competition->matches()->where('match_status', 'completed')->count(),
                    'total' => $competition->matches()->count(),
                ];
            }

            $tunisianClubs = $competitions->flatMap(function ($competition) {
                return $competition->clubs;
            })->unique('id')->values();

            $tunisianAssociation = $user->association ?? null;

            return view('competitions.classement', compact('competitions', 'classements', 'tunisianClubs', 'tunisianAssociation', 'matchsInfo'));

        } catch (\Exception $e) {
            // Etat honnete en cas d'erreur : listes vides, plus de
            // donnees de demonstration fictives.
            $competitions = collect();
            $classements = [];
            $tunisianClubs = collect();
            $tunisianAssociation = null;
            $matchsInfo = [];

            return view('competitions.classement', compact('competitions', 'classements', 'tunisianClubs', 'tunisianAssociation', 'matchsInfo'));
        }
    }

    /**
     * Calcule le classement réel d'une compétition à partir des vrais
     * matchs terminés (colonnes home_club_id/away_club_id, home_score/
     * away_score). Remplace generateClassementData()/
     * generateClassementDataFromRealClubs(), qui généraient entièrement
     * ces statistiques avec rand().
     */
    private function generateClassementDataFromMatches($competition, $clubs)
    {
        $classement = [];

        foreach ($clubs as $club) {
            $matches = $competition->matches()
                ->where('match_status', 'completed')
                ->where(function ($query) use ($club) {
                    $query->where('home_club_id', $club->id)
                          ->orWhere('away_club_id', $club->id);
                })
                ->orderBy('match_date')
                ->get();

            $victoires = 0;
            $nuls = 0;
            $defaites = 0;
            $butsPour = 0;
            $butsContre = 0;

            foreach ($matches as $match) {
                $isHome = $match->home_club_id == $club->id;
                $pour = (int) ($isHome ? $match->home_score : $match->away_score);
                $contre = (int) ($isHome ? $match->away_score : $match->home_score);
                $butsPour += $pour;
                $butsContre += $contre;

                if ($pour > $contre) {
                    $victoires++;
                } elseif ($pour == $contre) {
                    $nuls++;
                } else {
                    $defaites++;
                }
            }

            // Forme réelle : résultats des 5 derniers matchs terminés.
            $forme = $matches->slice(-5)->map(function ($match) use ($club) {
                $isHome = $match->home_club_id == $club->id;
                $pour = (int) ($isHome ? $match->home_score : $match->away_score);
                $contre = (int) ($isHome ? $match->away_score : $match->home_score);
                if ($pour > $contre) return 'V';
                if ($pour == $contre) return 'N';
                return 'D';
            })->values()->all();

            $classement[] = [
                'club' => $club,
                'position' => 0, // Sera calculé après tri
                'matchs_joues' => $matches->count(),
                'victoires' => $victoires,
                'nuls' => $nuls,
                'defaites' => $defaites,
                'buts_pour' => $butsPour,
                'buts_contre' => $butsContre,
                'difference_buts' => $butsPour - $butsContre,
                'points' => ($victoires * 3) + $nuls,
                'forme' => $forme,
                // Aucune donnée historique de classement (pas de snapshot
                // journée par journée) n'existe pour calculer une vraie
                // évolution de position : état honnête = pas de variation.
                'evolution' => 0,
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
     *
     * NOTE (audit factice -> reel, 2026-09) : ciblait en dur une
     * "association tunisienne" pour tout le monde, avec des fixtures
     * entièrement générées par rand() aussi bien dans le "chemin normal"
     * (generateFixturesFromRealData) que dans le fallback de démonstration
     * (generateFixtures, méthode explicitement marquée "DÉPRÉCIÉE"), y
     * compris pour 5 clubs tunisiens fictifs. Remplacé par les vrais
     * matchs du club de l'utilisateur connecté.
     */
    public function clubFixtures(): View
    {
        try {
            $clubId = auth()->user()->club_id ?? null;
            $club = $clubId ? Club::find($clubId) : null;

            if (!$club) {
                $fixtures = [];
                $clubStats = ['victoires' => 0, 'points' => 0, 'prochain_match' => null];
                return view('competitions.club.fixtures', compact('fixtures', 'clubStats'));
            }

            $competitions = Competition::whereHas('matches', function ($query) use ($clubId) {
                $query->where('home_club_id', $clubId)->orWhere('away_club_id', $clubId);
            })->get();

            $fixtures = $this->generateFixturesFromRealData(collect([$club]), $competitions);
            $clubStats = $this->computeClubQuickStats($fixtures, $clubId);

            return view('competitions.club.fixtures', compact('fixtures', 'clubStats'));
            
        } catch (\Exception $e) {
            $fixtures = [];
            $clubStats = ['victoires' => 0, 'points' => 0, 'prochain_match' => null];
            return view('competitions.club.fixtures', compact('fixtures', 'clubStats'));
        }
    }

    /**
     * Calcule les statistiques rapides reelles d'un club (victoires, points,
     * prochain match) a partir des fixtures deja generees (elles-memes
     * basees sur les vrais matchs de GameMatch).
     *
     * NOTE (audit factice -> reel, 2026-09) : la vue competitions.club.
     * fixtures.blade.php affichait auparavant "Victoires" et "Points" via
     * rand(8, 15) / rand(25, 45), et "Prochain Match" via
     * now()->addDays(7) (toujours exactement 7 jours plus tard, quel que
     * soit le vrai calendrier) — des valeurs entierement fictives, sans
     * rapport avec les matchs reels du club. Ces trois valeurs sont
     * desormais calculees a partir des vrais matchs.
     */
    private function computeClubQuickStats(array $fixtures, int $clubId): array
    {
        $victoires = 0;
        $points = 0;
        $prochainMatch = null;

        foreach ($fixtures as $competitionFixtures) {
            foreach ($competitionFixtures as $journee) {
                foreach ($journee['matchs'] as $match) {
                    $isHome = $match['domicile'] && $match['domicile']->id == $clubId;
                    $isAway = $match['exterieur'] && $match['exterieur']->id == $clubId;

                    if (!$isHome && !$isAway) {
                        continue;
                    }

                    if ($match['statut'] === 'Terminé') {
                        $butsClub = $isHome ? $match['buts_domicile'] : $match['buts_exterieur'];
                        $butsAdverse = $isHome ? $match['buts_exterieur'] : $match['buts_domicile'];

                        if ($butsClub !== null && $butsAdverse !== null) {
                            if ($butsClub > $butsAdverse) {
                                $victoires++;
                                $points += 3;
                            } elseif ($butsClub == $butsAdverse) {
                                $points += 1;
                            }
                        }
                    } else {
                        $matchDate = $match['date'];
                        if ($matchDate && (!$prochainMatch || $matchDate->lt($prochainMatch))) {
                            $prochainMatch = $matchDate;
                        }
                    }
                }
            }
        }

        return [
            'victoires' => $victoires,
            'points' => $points,
            'prochain_match' => $prochainMatch,
        ];
    }

    /**
     * Fixtures de l'association - Tous les matchs de toutes les compétitions
     */
    public function associationFixtures(Request $request): View
    {
        try {
            // NOTE (audit factice -> reel, 2026-09) : cette méthode
            // recherchait en dur Association::where('name', 'Fédération
            // Tunisienne de Football'), ce qui affichait les fixtures de
            // la FTF à tout utilisateur association quelle que soit son
            // association réelle. Remplacé par l'association de
            // l'utilisateur connecté.
            $associationId = auth()->user()->association_id ?? null;
            $association = $associationId ? Association::find($associationId) : null;

            if (!$association) {
                return view('errors.database', [
                    'error' => 'Association non trouvée',
                    'message' => 'Aucune association n\'est associée à votre compte.'
                ]);
            }
            
            // Récupérer les clubs de l'association depuis la base de données
            $clubs = Club::where('association_id', $association->id)->get();

            if ($clubs->isEmpty()) {
                return view('errors.database', [
                    'error' => 'Aucun club trouvé',
                    'message' => 'Aucun club trouvé pour votre association dans la base de données.'
                ]);
            }

            // Utiliser les données de la base de données au lieu de générer des fixtures
            $allFixtures = $this->getFixturesFromDatabase($association, $clubs);
            
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
            
            // Récupérer les vraies compétitions de l'association connectée
            $competitions = Competition::where('association_id', $association->id)
                ->get()
                ->map(function($comp) {
                    return (object)[
                        'id' => $comp->id,
                        'name' => $comp->name,
                        'season' => $comp->season ?? 'Saison non définie'
                    ];
                });

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
            // Récupérer toutes les compétitions
            $competitions = \App\Models\Competition::orderBy('name')->get();

            // Récupérer les matchs programmés
            $matches = \App\Models\GameMatch::with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->where('status', 'scheduled')
                ->orderBy('match_date')
                ->get();

            // NOTE (audit factice -> reel, 2026-09) : en l'absence
            // d'arbitres réels (ou en cas d'erreur), 4 arbitres fictifs
            // identiques ("Arbitre Principal 1"...@example.com) étaient
            // affichés pour tout le monde. Remplacé par une vraie
            // requête ; liste réellement vide si aucun arbitre n'existe.
            $referees = \App\Models\User::where('role', 'referee')->orderBy('name')->get();

            // Variables pour les filtres
            $competitionId = request('competition_id');
            $dateFrom = request('date_from') ? \Carbon\Carbon::parse(request('date_from')) : \Carbon\Carbon::now()->subDays(30);
            $dateTo = request('date_to') ? \Carbon\Carbon::parse(request('date_to')) : \Carbon\Carbon::now()->addDays(90);

            return view('competitions.association.designation-arbitres', compact('matches', 'referees', 'competitions', 'competitionId', 'dateFrom', 'dateTo'));
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des données minimales
            $competitions = collect();
            $matches = collect();
            $referees = collect();
            $competitionId = null;
            $dateFrom = \Carbon\Carbon::now()->subDays(30);
            $dateTo = \Carbon\Carbon::now()->addDays(90);
            
            return view('competitions.association.designation-arbitres', compact('matches', 'referees', 'competitions', 'competitionId', 'dateFrom', 'dateTo'));
        }
    }

    /**
     * Sauvegarde les assignations d'arbitres
     */
    public function saveArbitreAssignments(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'match_id' => 'required|integer|exists:matches,id',
                'main_referee_id' => 'nullable|integer|exists:users,id',
                'assistant_referee_1_id' => 'nullable|integer|exists:users,id',
                'assistant_referee_2_id' => 'nullable|integer|exists:users,id',
                'var_referee_id' => 'nullable|integer|exists:users,id',
                'fourth_official_id' => 'nullable|integer|exists:users,id',
            ]);

            $match = GameMatch::findOrFail($request->match_id);
            
            // Supprimer les assignations existantes pour ce match
            \App\Models\MatchOfficial::where('match_id', $match->id)->delete();
            
            // Créer les nouvelles assignations
            $assignments = [
                'main_referee' => $request->main_referee_id,
                'assistant_referee_1' => $request->assistant_referee_1_id,
                'assistant_referee_2' => $request->assistant_referee_2_id,
                'var_referee' => $request->var_referee_id,
                'fourth_official' => $request->fourth_official_id,
            ];
            
            foreach ($assignments as $role => $userId) {
                if ($userId) {
                    \App\Models\MatchOfficial::create([
                        'match_id' => $match->id,
                        'user_id' => $userId,
                        'role' => $role,
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Assignations d\'arbitres sauvegardées avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
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
        //
        // NOTE (audit factice -> reel, 2026-09) : orderBy('round') /
        // groupBy('round') / $match->round et $match->match_time
        // referencaient des colonnes qui n'existent pas sur la table
        // matches (elle expose 'matchday' et 'kickoff_time', pas 'round'
        // ni 'match_time') : orderBy('round') provoquait une
        // QueryException a chaque appel, systematiquement rattrapee par
        // le catch de associationFixtures() qui affichait une page
        // d'erreur generique. Corrige pour utiliser les vraies colonnes.
        $matches = GameMatch::with(['homeTeam.club', 'awayTeam.club'])
            ->whereIn('competition_id', $competitions->pluck('id'))
            ->orderBy('matchday')
            ->orderBy('match_date')
            ->get();
        
        if ($matches->isEmpty()) {
            return [];
        }
        
        // Organiser les matchs par journée
        $fixtures = [];
        $matchesByRound = $matches->groupBy('matchday');
        
        foreach ($matchesByRound as $round => $roundMatches) {
            $matchsJournee = [];
            
            foreach ($roundMatches as $match) {
                $statut = $match->status === 'completed' ? 'Terminé' : 'À venir';
                
                $matchsJournee[] = [
                    'id' => $match->id,
                    'domicile' => $match->homeTeam->club->short_name ?? $match->homeTeam->club->name ?? 'Club inconnu',
                    'exterieur' => $match->awayTeam->club->short_name ?? $match->awayTeam->club->name ?? 'Club inconnu',
                    'date' => $match->match_date,
                    'heure' => $match->kickoff_time,
                    'stade' => $match->venue,
                    'buts_domicile' => $match->home_score,
                    'buts_exterieur' => $match->away_score,
                    'statut' => $statut,
                    'journee' => $match->matchday,
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
     * Dashboard du module compétitions avec données synchronisées
     */
    public function moduleDashboard(Request $request): View
    {
        try {
            // NOTE (audit factice -> reel, 2026-09) : cette méthode
            // recherchait en dur Association::where('name', 'Fédération
            // Tunisienne de Football'), affichant les mêmes compétitions
            // à tout utilisateur quelle que soit son association ou son
            // club réels. Remplacé par une portée réelle : le club ou
            // l'association de l'utilisateur connecté, ou toutes les
            // compétitions actives pour un administrateur système.
            $user = auth()->user();

            $competitionsQuery = Competition::where('status', 'active');

            if ($user && $user->club_id) {
                $competitionsQuery->whereHas('clubs', function ($q) use ($user) {
                    $q->where('clubs.id', $user->club_id);
                });
            } elseif ($user && $user->association_id) {
                $competitionsQuery->where('association_id', $user->association_id);
            }
            // Sinon (administrateur système, ou utilisateur sans club/association) :
            // vue globale sur toutes les compétitions actives.

            $competitions = $competitionsQuery->get();
            $competitionIds = $competitions->pluck('id');
            
            // Récupérer les statistiques des matchs
            $totalMatches = GameMatch::whereIn('competition_id', $competitionIds)->count();
            $completedMatches = GameMatch::whereIn('competition_id', $competitionIds)
                ->where('status', 'completed')->count();
            $upcomingMatches = GameMatch::whereIn('competition_id', $competitionIds)
                ->where('status', 'scheduled')->count();
            
            // Récupérer les prochains matchs
            $nextMatches = GameMatch::with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->whereIn('competition_id', $competitionIds)
                ->where('status', 'scheduled')
                ->orderBy('match_date')
                ->limit(5)
                ->get();
            
            // Récupérer les derniers résultats
            $recentResults = GameMatch::with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->whereIn('competition_id', $competitionIds)
                ->where('status', 'completed')
                ->orderBy('match_date', 'desc')
                ->limit(5)
                ->get();
            
            return view('modules.competitions.index', compact(
                'competitions',
                'totalMatches',
                'completedMatches',
                'upcomingMatches',
                'nextMatches',
                'recentResults'
            ));
            
        } catch (\Exception $e) {
            return view('errors.database', [
                'error' => 'Erreur lors du chargement du module',
                'message' => 'Impossible de charger les données du module compétitions: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export des engagements
     */
    public function exportEngagements(Request $request)
    {
        try {
            // Récupérer les données des engagements
            $matches = GameMatch::with(['homeTeam.club', 'awayTeam.club', 'competition'])->get();
            
            $clubsData = collect();
            $homeClubs = $matches->pluck('homeTeam.club')->filter()->unique('id');
            $awayClubs = $matches->pluck('awayTeam.club')->filter()->unique('id');
            
            foreach ($homeClubs as $club) {
                if ($club) {
                    $clubsData->put($club->id, ['club' => $club, 'matches' => collect(), 'competitions' => collect()]);
                }
            }
            
            foreach ($awayClubs as $club) {
                if ($club) {
                    if (!$clubsData->has($club->id)) {
                        $clubsData->put($club->id, ['club' => $club, 'matches' => collect(), 'competitions' => collect()]);
                    }
                }
            }
            
            foreach ($matches as $match) {
                if ($match->homeTeam && $match->homeTeam->club) {
                    $clubId = $match->homeTeam->club->id;
                    if ($clubsData->has($clubId)) {
                        $clubsData[$clubId]['matches']->push($match);
                        if ($match->competition) {
                            $clubsData[$clubId]['competitions']->push($match->competition);
                        }
                    }
                }
                
                if ($match->awayTeam && $match->awayTeam->club) {
                    $clubId = $match->awayTeam->club->id;
                    if ($clubsData->has($clubId)) {
                        $clubsData[$clubId]['matches']->push($match);
                        if ($match->competition) {
                            $clubsData[$clubId]['competitions']->push($match->competition);
                        }
                    }
                }
            }
            
            $clubs = $clubsData->map(function($data) {
                $club = $data['club'];
                $matches = $data['matches']->unique('id');
                $competitions = $data['competitions']->unique('id');
                
                return [
                    'Club' => $club->name,
                    'Compétition' => $competitions->first()->name ?? 'Aucune',
                    'Statut' => $competitions->isNotEmpty() ? 'Engagé' : 'Non engagé',
                    'Total Matchs' => $matches->count(),
                    'Matchs Terminés' => $matches->where('status', 'completed')->count(),
                    'Dernière Activité' => $matches->sortByDesc('updated_at')->first() ? $matches->sortByDesc('updated_at')->first()->updated_at->format('Y-m-d') : 'Aucune'
                ];
            })->values();
            
            // Générer le CSV
            $filename = 'engagements_clubs_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($clubs) {
                $file = fopen('php://output', 'w');
                
                // En-têtes
                fputcsv($file, array_keys($clubs->first() ?? []));
                
                // Données
                foreach ($clubs as $club) {
                    fputcsv($file, $club);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'export: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Valider tous les engagements
     */
    public function validateAllEngagements(Request $request)
    {
        try {
            // NOTE (audit factice -> reel, 2026-09) : cette action
            // renvoyait toujours success:true avec 'validated_count' => 0
            // en dur ("À remplacer par le vrai nombre") sans valider quoi
            // que ce soit réellement en base. En l'absence d'une logique
            // métier définie pour "valider un engagement" (quelle ligne
            // de competition_club, quel statut cible), on renvoie
            // désormais un état honnête plutôt qu'un faux succès.
            return response()->json([
                'success' => false,
                'error' => "La validation groupée des engagements n'est pas encore disponible : aucune logique de validation n'est connectée à ce bouton."
            ], 501);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la validation: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Valider un engagement spécifique
     */
    public function validateEngagement(Request $request, $clubId)
    {
        try {
            // Ici on pourrait ajouter la logique de validation d'un club spécifique
            
            return response()->json([
                'success' => true,
                'message' => "L'engagement du club {$clubId} a été validé avec succès"
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la validation: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Détails d'un club
     */
    public function clubDetails($clubId)
    {
        try {
            $club = Club::with(['teams.matches', 'teams.competitions'])->find($clubId);
            
            if (!$club) {
                return response()->json(['error' => 'Club non trouvé'], 404);
            }
            
            return response()->json([
                'success' => true,
                'club' => $club
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export des données d'un club
     */
    public function exportClubData(Request $request, $clubId)
    {
        try {
            $club = Club::with(['teams.matches', 'teams.competitions'])->find($clubId);
            
            if (!$club) {
                return response()->json(['error' => 'Club non trouvé'], 404);
            }
            
            // Générer les données du club
            $clubData = [
                'Club' => $club->name,
                'Équipes' => $club->teams->count(),
                'Compétitions' => $club->teams->flatMap->competitions->unique('id')->count(),
                'Total Matchs' => $club->teams->sum(function($team) {
                    return $team->matches->count();
                }),
                'Matchs Terminés' => $club->teams->sum(function($team) {
                    return $team->matches->where('status', 'completed')->count();
                })
            ];
            
            $filename = 'club_' . $club->name . '_' . date('Y-m-d_H-i-s') . '.json';
            $headers = [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            return response()->json($clubData, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'export: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Suspendre un engagement
     */
    public function suspendEngagement(Request $request, $clubId)
    {
        try {
            // Ici on pourrait ajouter la logique de suspension d'un engagement
            
            return response()->json([
                'success' => true,
                'message' => "L'engagement du club {$clubId} a été suspendu"
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suspension: ' . $e->getMessage()], 500);
        }
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
        //
        // NOTE (audit factice -> reel, 2026-09) : $match->round /
        // $match->match_time referencaient des colonnes inexistantes
        // (matchday / kickoff_time) et 'Compétition Tunisienne' etait un
        // nom d'affichage fixe pour toute competition sans nom. Corrige.
        return [
            'id' => $match->id,
            'competition' => $match->competition ? $match->competition->name : 'N/A',
            'journee' => $match->matchday,
            'date' => $match->match_date,
            'heure' => $match->kickoff_time,
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
     * Génère les fixtures d\'un ou plusieurs clubs à partir des vrais
     * matchs de la base de données (colonnes home_club_id/away_club_id,
     * matchday, kickoff_time...).
     *
     * NOTE (audit factice -> reel, 2026-09) : générait auparavant 30
     * "journées" par compétition avec des clubs tirés au hasard, des
     * dates, des résultats et des noms d\'arbitres ("Arbitre 7",
     * "Assistant 12"...) entièrement fabriqués par rand(), y compris les
     * ID de match (jamais de vrais ID, donc le lien "Voir" vers la
     * feuille de match réelle pointait vers un match inexistant).
     */
    private function generateFixturesFromRealData($clubs, $competitions)
    {
        $fixtures = [];
        $clubIds = $clubs->pluck('id');

        foreach ($competitions as $competition) {
            $matches = GameMatch::with(['homeClub', 'awayClub'])
                ->where('competition_id', $competition->id)
                ->where(function ($query) use ($clubIds) {
                    $query->whereIn('home_club_id', $clubIds)
                          ->orWhereIn('away_club_id', $clubIds);
                })
                ->orderBy('matchday')
                ->orderBy('match_date')
                ->get();

            if ($matches->isEmpty()) {
                continue;
            }

            $competitionFixtures = [];
            $matchesByJournee = $matches->groupBy('matchday');

            foreach ($matchesByJournee as $journee => $journeeMatches) {
                $matchsJournee = $journeeMatches->map(function ($match) use ($competition) {
                    $isCompleted = in_array($match->match_status, ['completed', 'finished']);

                    return [
                        'id' => $match->id,
                        'competition_id' => $competition->id,
                        'competition_name' => $competition->name,
                        'domicile' => $match->homeClub,
                        'exterieur' => $match->awayClub,
                        'date' => $match->match_date,
                        'heure' => $match->kickoff_time?->format('H:i') ?? 'N/A',
                        'stade' => $match->venue ?? $match->stadium ?? 'Non renseigné',
                        'buts_domicile' => $match->home_score,
                        'buts_exterieur' => $match->away_score,
                        'statut' => $isCompleted ? 'Terminé' : 'À venir',
                        'arbitre_principal' => $match->referee ?? 'À désigner',
                        'arbitre_assistant_1' => $match->assistant_referee_1 ?? 'À désigner',
                        'arbitre_assistant_2' => $match->assistant_referee_2 ?? 'À désigner',
                        'arbitre_var' => $match->var_referee ?? 'À désigner',
                    ];
                })->values()->all();

                $competitionFixtures[] = [
                    'competition_id' => $competition->id,
                    'competition_name' => $competition->name,
                    'journee' => $journee,
                    'date' => $journeeMatches->first()->match_date,
                    'matchs' => $matchsJournee
                ];
            }
            
            $fixtures[$competition->id] = $competitionFixtures;
        }
        
        return $fixtures;
    }

}
