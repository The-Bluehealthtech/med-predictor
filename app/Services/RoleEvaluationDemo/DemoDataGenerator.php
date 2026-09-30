<?php

namespace App\Services\RoleEvaluationDemo;

use App\Services\RoleEvaluationImport\DryRunRollback;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Orchestrateur du Livrable 3 ("GÉNÉRATEUR DE DÉMONSTRATION", mandat).
 *
 * Ce que fait cette classe :
 * - crée de nouveaux clubs/équipes/joueurs fictifs, clairement nommés
 *   "(Démo)" (clubs/competitions n'ont pas de colonne is_demo, cf. Livrable 1 —
 *   le nom + l'identifiant FIFA Connect fictif "DEMO-CLUB-xxx" sont le seul
 *   marquage disponible sur ces deux tables, voir le rapport) ;
 * - construit un calendrier en double round-robin (ScheduleGenerator) ;
 * - simule chaque match (MatchStatSimulator) et écrit participations,
 *   statistiques joueur/équipe et événements ;
 * - marque TOUTES les lignes qui portent une colonne is_demo à true, et les
 *   rattache à un import_batches (batch_type='demo_generation').
 *
 * Ce que cette classe NE fait JAMAIS :
 * - UPDATE ou DELETE sur une table existante (grep du fichier : aucun appel
 *   ->update()/->delete()/->truncate() n'existe ici, seulement des insert()) ;
 * - toucher aux 49 joueurs / 10 matchs actuels (club CS Sfaxien, matchs 1-10) :
 *   ce jeu n'est identifié nulle part dans ce fichier, il n'est simplement
 *   jamais référencé ;
 * - régler le moindre poids/seuil de role_config_weights (mandat : "les
 *   données de démonstration ne servent jamais à régler des poids... ces
 *   réglages attendent les vraies données").
 *
 * Prérequis : les migrations du Livrable 1 doivent avoir été exécutées
 * (position_catalog, match_participations, match_team_stats, import_batches
 * doivent exister) — vérifié explicitement en tout début de run(), échec
 * rapide et explicite sinon.
 *
 * DEUXIÈME PRÉREQUIS, découvert en validant ce livrable (voir rapport,
 * "Découverte importante") : la migration
 * 2026_09_30_110000_fix_player_match_detailed_stats_player_foreign_key.php
 * doit ELLE AUSSI être exécutée AVANT cette commande.
 * player_match_detailed_stats.player_id référence aujourd'hui joueurs(id),
 * pas players(id) ; sans ce correctif, chaque écriture de statistiques
 * joueur générées échoue avec une violation de clé étrangère (confirmé sur
 * copie jetable, PRAGMA foreign_keys=ON comme en production). Cette
 * vérification n'est PAS automatisée ici (introspection de clé étrangère
 * non portable entre SQLite/MySQL/PostgreSQL) : si la migration n'a pas été
 * appliquée, l'échec survient à l'écriture, avec le message d'erreur SQL
 * d'origine — pas silencieusement.
 */
class DemoDataGenerator
{
    private SeededRandom $rng;
    private ScheduleGenerator $scheduler;
    private MatchStatSimulator $simulator;
    private DemoSquadFactory $squadFactory;

    private array $report = [
        'clubs_created' => 0,
        'teams_created' => 0,
        'players_created' => 0,
        'matches_created' => 0,
        'match_sheets_created' => 0,
        'participations_created' => 0,
        'player_stats_created' => 0,
        'team_stats_created' => 0,
        'events_created' => 0,
        'degraded_cases' => [],
    ];

    public function __construct(
        private int $seed,
        private int $clubCount = 16,
        private bool $dryRun = false
    ) {
        if ($this->clubCount < 4 || $this->clubCount % 2 !== 0) {
            throw new RuntimeException("clubCount doit être pair et >= 4 (double round-robin), reçu : {$this->clubCount}");
        }

        $this->rng = new SeededRandom($this->seed);
        $this->scheduler = new ScheduleGenerator();
        $this->simulator = new MatchStatSimulator($this->rng);
        $this->squadFactory = new DemoSquadFactory($this->rng);
    }

    /**
     * @return array{batch_id:?int, report:array}
     */
    public function run(): array
    {
        $this->assertPrerequisiteTablesExist();

        $result = null;
        if ($this->dryRun) {
            try {
                DB::transaction(function () use (&$result) {
                    $result = $this->runInternal();
                    throw new DryRunRollback($result['batch_id']);
                });
            } catch (DryRunRollback $e) {
                // Rollback volontaire : rien n'est persisté, voir Livrable 2
                // pour le même schéma (transaction + rollback interne).
            }
        } else {
            $result = DB::transaction(fn () => $this->runInternal());
        }

        return $result;
    }

    private function assertPrerequisiteTablesExist(): void
    {
        $required = ['position_catalog', 'event_types', 'import_batches', 'match_participations', 'match_team_stats'];
        foreach ($required as $table) {
            if (! \Illuminate\Support\Facades\Schema::hasTable($table)) {
                throw new RuntimeException(
                    "Table requise absente : '{$table}'. Les migrations du Livrable 1 ".
                    '(database/migrations/2026_09_30_09*.php) doivent être exécutées '.
                    "(php artisan migrate) avant `role-eval:generate-demo`."
                );
            }
        }
    }

    private function runInternal(): array
    {
        $startedAt = now();
        $batchId = DB::table('import_batches')->insertGetId([
            'batch_type' => 'demo_generation',
            'source_label' => 'role-eval:generate-demo',
            'status' => 'running',
            'seed' => (string) $this->seed,
            'params' => json_encode(['club_count' => $this->clubCount]),
            'started_at' => $startedAt,
            'created_at' => $startedAt,
            'updated_at' => $startedAt,
        ]);

        // --- 1. Clubs / équipes / joueurs fictifs -------------------------
        $clubDefs = $this->squadFactory->buildClubs($this->clubCount);
        $clubIds = [];
        $teamIds = [];
        $squads = []; // clubIndex => [squadKey => joueur]
        $playerIds = []; // clubIndex => [squadKey => player_id réel]
        $teamSkill = []; // clubIndex => niveau moyen [0,1]

        foreach ($clubDefs as $clubIndex => $club) {
            $clubId = DB::table('clubs')->insertGetId([
                'name' => $club['name'],
                'fifa_connect_id' => $club['fifa_connect_id'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $clubIds[$clubIndex] = $clubId;
            $this->report['clubs_created']++;

            $teamId = DB::table('teams')->insertGetId([
                'name' => 'Effectif Démo',
                'club_id' => $clubId,
                'type' => 'first_team',
                'status' => 'active',
                'is_demo' => true,
                'import_batch_id' => $batchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $teamIds[$clubIndex] = $teamId;
            $this->report['teams_created']++;

            $squad = $this->squadFactory->buildSquad();
            $squads[$clubIndex] = $squad;

            $ids = [];
            foreach ($squad as $key => $player) {
                $ids[$key] = DB::table('players')->insertGetId([
                    'name' => $player['name'],
                    'first_name' => $player['first_name'],
                    'last_name' => $player['last_name'],
                    'club_id' => $clubId,
                    'team_id' => $teamId,
                    'position' => $player['broad_group'],
                    'jersey_number' => $player['jersey_number'],
                    'status' => 'active',
                    'availability' => 'available',
                    'preferred_foot' => $this->rng->chance(0.78) ? 'right' : 'left',
                    'is_demo' => true,
                    'import_batch_id' => $batchId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $playerIds[$clubIndex] = $ids;
            $this->report['players_created'] += count($ids);

            $skills = array_column($squad, 'skill');
            $teamSkill[$clubIndex] = array_sum($skills) / count($skills);
        }

        // --- 2. Compétition dédiée -----------------------------------------
        // competitions n'a pas de colonne is_demo (Livrable 1) : marquage par
        // nom uniquement, cf. docstring de classe.
        $competitionId = DB::table('competitions')->insertGetId([
            'name' => 'Championnat Démonstration (Livrable 3)',
            'short_name' => 'DEMO-L3',
            'type' => 'league',
            'football_type' => '11aside',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // --- 3. Calendrier ---------------------------------------------------
        $fixtures = $this->scheduler->generateDoubleRoundRobin(array_keys($clubDefs));
        $roundsPerLeg = $this->clubCount - 1; // méthode du cercle : N-1 rondes par manche
        $totalRounds = $roundsPerLeg * 2;

        // Cas dégradés (mandat, LIVRABLE 3) : un petit nombre de matchs
        // choisis déterministement par index de calendrier, pas par tirage
        // aléatoire, pour rester repérables d'une exécution à l'autre.
        $missingIndicatorFixtures = $this->pickEvery($fixtures, 50, 0);
        $lateCameoFixtures = $this->pickEvery($fixtures, 50, 25);

        // Changement de poste en cours de saison : UN joueur, du club
        // d'index 0, initialement CDM ("LCM" en secours si l'effectif n'a
        // pas de CDM par construction — SQUAD_TEMPLATE en garantit toujours
        // au moins un), qui devient LCB à partir de la seconde moitié du
        // calendrier (round >= roundsPerLeg).
        $midSeasonClubIndex = 0;
        $midSeasonPlayerKey = $this->findFirstSquadKeyForPosition($squads[$midSeasonClubIndex], 'CDM');
        $midSeasonNewPosition = 'LCB';
        if ($midSeasonPlayerKey !== null) {
            $this->report['degraded_cases'][] = sprintf(
                'changement de poste en cours de saison : club #%d, joueur (clé effectif %d) CDM -> LCB à partir de la journée %d',
                $midSeasonClubIndex + 1,
                $midSeasonPlayerKey,
                $roundsPerLeg + 1
            );
        }

        // --- 4. Simulation + écriture match par match -----------------------
        $anchor = now()->startOfDay();
        foreach ($fixtures as $fixtureIndex => $fixture) {
            $homeIdx = $fixture['home'];
            $awayIdx = $fixture['away'];
            $round = $fixture['round'];

            $homeOverride = [];
            $awayOverride = [];
            if ($midSeasonPlayerKey !== null && $round >= $roundsPerLeg) {
                if ($homeIdx === $midSeasonClubIndex) {
                    $homeOverride = [$midSeasonPlayerKey => $midSeasonNewPosition];
                } elseif ($awayIdx === $midSeasonClubIndex) {
                    $awayOverride = [$midSeasonPlayerKey => $midSeasonNewPosition];
                }
            }

            $options = [
                'missingIndicators' => in_array($fixtureIndex, $missingIndicatorFixtures, true),
                'forceLateCameo' => in_array($fixtureIndex, $lateCameoFixtures, true),
                'homePositionOverride' => $homeOverride,
                'awayPositionOverride' => $awayOverride,
            ];

            $sim = $this->simulator->simulate(
                $squads[$homeIdx],
                $squads[$awayIdx],
                $teamSkill[$homeIdx],
                $teamSkill[$awayIdx],
                $options
            );

            $matchDate = $anchor->copy()->subWeeks($totalRounds - $round)->format('Y-m-d H:i:s');

            $matchId = DB::table('matches')->insertGetId([
                'name' => sprintf('%s - %s', $clubDefs[$homeIdx]['name'], $clubDefs[$awayIdx]['name']),
                'match_date' => $matchDate,
                'competition_id' => $competitionId,
                'home_team_id' => $teamIds[$homeIdx],
                'away_team_id' => $teamIds[$awayIdx],
                'home_club_id' => $clubIds[$homeIdx],
                'away_club_id' => $clubIds[$awayIdx],
                'kickoff_time' => $matchDate,
                'match_status' => 'completed',
                'status' => 'completed',
                'home_score' => $sim['home_score'],
                'away_score' => $sim['away_score'],
                'matchday' => $round + 1,
                'is_demo' => true,
                'import_batch_id' => $batchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->report['matches_created']++;

            // match_sheets : pas de colonne is_demo (Livrable 1 ne l'y a pas
            // ajoutée), champs par défaut, rattaché à un match is_demo=true.
            $matchSheetId = DB::table('match_sheets')->insertGetId([
                'match_id' => $matchId,
                'home_team_score' => $sim['home_score'],
                'away_team_score' => $sim['away_score'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->report['match_sheets_created']++;

            $this->writeParticipationsAndStats($sim['home_participations'], $sim['home_stats'], $matchId, $teamIds[$homeIdx], $playerIds[$homeIdx], $competitionId, $batchId);
            $this->writeParticipationsAndStats($sim['away_participations'], $sim['away_stats'], $matchId, $teamIds[$awayIdx], $playerIds[$awayIdx], $competitionId, $batchId);

            $this->writeTeamStats($sim['home_team_stats'], $matchId, $teamIds[$homeIdx], $batchId);
            $this->writeTeamStats($sim['away_team_stats'], $matchId, $teamIds[$awayIdx], $batchId);

            $this->writeEvents($sim['events'], $matchId, $matchSheetId, [
                'home' => ['team_id' => $teamIds[$homeIdx], 'player_ids' => $playerIds[$homeIdx]],
                'away' => ['team_id' => $teamIds[$awayIdx], 'player_ids' => $playerIds[$awayIdx]],
            ], $batchId);
        }

        $this->report['degraded_cases'][] = sprintf(
            'indicateurs manquants (NULL, jamais 0) : %d match(s) — indices calendrier %s',
            count($missingIndicatorFixtures),
            implode(', ', $missingIndicatorFixtures)
        );
        $this->report['degraded_cases'][] = sprintf(
            'joueur à très peu de minutes forcé : %d match(s) — indices calendrier %s',
            count($lateCameoFixtures),
            implode(', ', $lateCameoFixtures)
        );

        $finishedAt = now();
        DB::table('import_batches')->where('id', $batchId)->update([
            'status' => 'completed',
            'finished_at' => $finishedAt,
            'report' => json_encode($this->report),
            'updated_at' => $finishedAt,
        ]);
        // Remarque : ce ->update() cible UNIQUEMENT la ligne import_batches
        // que CE run vient de créer (ci-dessus, insertGetId) — jamais une
        // table de données existante.

        return ['batch_id' => $batchId, 'report' => $this->report];
    }

    private function writeParticipationsAndStats(array $participations, array $statsByKey, int $matchId, int $teamId, array $playerIdsByKey, int $competitionId, int $batchId): void
    {
        foreach ($participations as $p) {
            $playerId = $playerIdsByKey[$p['player_key']];
            DB::table('match_participations')->insert([
                'match_id' => $matchId,
                'player_id' => $playerId,
                'team_id' => $teamId,
                'detailed_position' => $p['detailed_position'],
                'is_starter' => $p['is_starter'],
                'minute_in' => $p['minute_in'],
                'minute_out' => $p['minute_out'],
                'jersey_number' => $p['jersey_number'],
                'is_demo' => true,
                'import_batch_id' => $batchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->report['participations_created']++;

            $stats = $statsByKey[$p['player_key']];
            $gk = $stats['gk'] ?? null;
            unset($stats['gk']);

            $row = array_merge($stats, [
                'player_id' => $playerId,
                'match_id' => $matchId,
                'team_id' => $teamId,
                'competition_id' => $competitionId,
                'is_demo' => true,
                'import_batch_id' => $batchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if ($gk !== null) {
                $row = array_merge($row, $gk);
            }
            DB::table('player_match_detailed_stats')->insert($row);
            $this->report['player_stats_created']++;
        }
    }

    private function writeTeamStats(array $teamStats, int $matchId, int $teamId, int $batchId): void
    {
        $isHome = $teamStats['is_home'];
        // possession : générée ici (paire home/away sommant à 100), pas
        // d'équivalent joueur par joueur dans ce schéma (voir MatchStatSimulator).
        $possession = $isHome
            ? $this->rng->noisyCount(50, 8, 30, 70)
            : (100 - DB::table('match_team_stats')->where('match_id', $matchId)->where('is_home', true)->value('possession_pct'));

        DB::table('match_team_stats')->insert([
            'match_id' => $matchId,
            'team_id' => $teamId,
            'is_home' => $isHome,
            'possession_pct' => $possession,
            'shots_total' => $teamStats['shots_total'],
            'shots_on_target' => $teamStats['shots_on_target'],
            'corners' => $this->rng->noisyCount(5, 2.5, 0),
            'fouls' => $teamStats['fouls'],
            'offsides' => $teamStats['offsides'],
            'yellow_cards' => $teamStats['yellow_cards'],
            'red_cards' => $teamStats['red_cards'],
            'expected_goals' => $teamStats['expected_goals'],
            'goals_scored' => $teamStats['goals_scored'],
            'is_demo' => true,
            'import_batch_id' => $batchId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->report['team_stats_created']++;
    }

    private function writeEvents(array $events, int $matchId, int $matchSheetId, array $sides, int $batchId): void
    {
        foreach ($events as $e) {
            $base = [
                'match_sheet_id' => $matchSheetId,
                'match_id' => $matchId,
                'minute' => $e['minute'],
                'team_id' => $sides[$e['team']]['team_id'],
                'is_demo' => true,
                'import_batch_id' => $batchId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($e['type'] === 'substitution') {
                DB::table('match_events')->insert(array_merge($base, [
                    'type' => 'substitution',
                    'player_id' => $sides[$e['player_side']]['player_ids'][$e['player_in_key']],
                    'substituted_player_id' => $sides[$e['player_side']]['player_ids'][$e['player_out_key']],
                    'description' => 'Remplacement (généré, Livrable 3)',
                ]));
                $this->report['events_created']++;

                continue;
            }

            if ($e['type'] === 'goal') {
                $description = ($e['own_goal'] ?? false) ? 'But contre son camp (généré, Livrable 3)' : 'But (généré, Livrable 3)';
                DB::table('match_events')->insert(array_merge($base, [
                    'type' => 'goal',
                    'player_id' => $sides[$e['player_side']]['player_ids'][$e['player_key']],
                    'assisted_by_player_id' => $e['assist_key'] !== null ? $sides[$e['assist_side']]['player_ids'][$e['assist_key']] : null,
                    'description' => $description,
                    'event_data' => ($e['own_goal'] ?? false) ? json_encode(['own_goal' => true]) : null,
                ]));
                $this->report['events_created']++;

                continue;
            }

            // yellow_card / red_card
            DB::table('match_events')->insert(array_merge($base, [
                'type' => $e['type'],
                'player_id' => $sides[$e['player_side']]['player_ids'][$e['player_key']],
                'description' => $e['type'] === 'yellow_card' ? 'Carton jaune (généré, Livrable 3)' : 'Carton rouge (généré, Livrable 3)',
            ]));
            $this->report['events_created']++;
        }
    }

    /**
     * Sélectionne les index de $fixtures dont l'index modulo $mod vaut
     * $remainder — répartit un petit nombre de cas dégradés uniformément sur
     * tout le calendrier sans tirage aléatoire supplémentaire.
     */
    private function pickEvery(array $fixtures, int $mod, int $remainder): array
    {
        $picked = [];
        foreach (array_keys($fixtures) as $i) {
            if ($i % $mod === $remainder) {
                $picked[] = $i;
            }
        }

        return $picked;
    }

    private function findFirstSquadKeyForPosition(array $squad, string $detailedPosition): ?int
    {
        foreach ($squad as $key => $player) {
            if ($player['detailed_position'] === $detailedPosition) {
                return $key;
            }
        }

        return null;
    }
}
