<?php

namespace App\Services\RoleEvaluationDemo;

/**
 * Simule UN match entre deux effectifs déjà construits (DemoSquadFactory) et
 * produit, en mémoire, tout ce qu'il faut pour écrire :
 * - les participations (match_participations)
 * - les statistiques joueur brutes (player_match_detailed_stats)
 * - les statistiques équipe (match_team_stats), TOUJOURS agrégées à partir
 *   des statistiques joueurs déjà produites (mandat : "cohérentes avec la
 *   somme des statistiques joueurs" — garanti par construction, pas par un
 *   contrôle a posteriori)
 * - les événements (match_events : buts, cartons, remplacements)
 * - le score, DÉRIVÉ des événements de but déjà produits (mandat :
 *   "événements cohérents avec le score" — également garanti par
 *   construction)
 *
 * N'écrit rien en base : DemoDataGenerator seul parle à la base, ce qui rend
 * cette classe testable unitairement sans PHPUnit ni Laravel (voir
 * validation Python équivalente du rapport Livrable 3).
 *
 * Accommodation documentée (rapport Livrable 3, point ouvert) : la colonne
 * pré-existante match_events.type porte une contrainte CHECK restreinte à
 * ('goal','yellow_card','red_card','substitution','injury') — sans
 * 'own_goal', 'second_yellow_card' ni 'pass'. Cette contrainte n'est PAS
 * modifiée ici (règle du mandat : aucune modification d'une table existante
 * sans accord écrit). En conséquence :
 * - un but contre son camp est enregistré avec type='goal', team_id = l'ÉQUIPE
 *   BÉNÉFICIAIRE du but, player_id = l'auteur (de l'autre équipe), et
 *   event_data contient {"own_goal": true} ; il n'est jamais crédité au
 *   goals_scored du joueur auteur dans player_match_detailed_stats (comme en
 *   football réel) mais compte bien dans le score de l'équipe bénéficiaire.
 * - un deuxième carton jaune synonyme d'exclusion n'est pas modélisé : un
 *   joueur ne reçoit jamais plus d'un carton (jaune OU rouge) par match dans
 *   ce générateur, pour rester strictement dans le vocabulaire existant.
 * - aucun événement de type 'pass' n'est généré (voir le rapport pour la
 *   proposition de migration séparée, non appliquée, qui élargirait le CHECK).
 */
class MatchStatSimulator
{
    private const MATCH_MINUTES = 90;

    public function __construct(private SeededRandom $rng)
    {
    }

    /**
     * @param array<int, array> $homeSquad joueurs du club recevant (DemoSquadFactory::buildSquad())
     * @param array<int, array> $awaySquad joueurs du club visiteur
     * @param array{missingIndicators?: bool, forceLateCameo?: bool, positionOverride?: array<string,string>} $options
     *   positionOverride : [playerKeyDansLeSquad => nouveauCodeDetaille] pour le cas dégradé
     *   "changement de poste en cours de saison" (appliqué à CE match précisément).
     */
    public function simulate(
        array $homeSquad,
        array $awaySquad,
        float $homeSkillLevel,
        float $awaySkillLevel,
        array $options = []
    ): array {
        $missingIndicators = $options['missingIndicators'] ?? false;
        $forceLateCameo = $options['forceLateCameo'] ?? false;
        $homeOverride = $options['homePositionOverride'] ?? [];
        $awayOverride = $options['awayPositionOverride'] ?? [];

        $home = $this->selectMatchdaySquadAndSubs($homeSquad, $homeOverride);
        $away = $this->selectMatchdaySquadAndSubs($awaySquad, $awayOverride);

        $homeParticipations = $this->buildParticipations($home, $forceLateCameo);
        $awayParticipations = $this->buildParticipations($away, false);

        $events = [];

        // Remplacements : un événement match_events par substitution déjà
        // décidée dans buildParticipations (cohérence intégrale avec
        // minute_in/minute_out des participations, par construction).
        foreach ([$homeParticipations, $awayParticipations] as $sideIdx => $participations) {
            $teamKey = $sideIdx === 0 ? 'home' : 'away';
            foreach ($participations as $p) {
                if (! $p['is_starter'] && $p['minute_in'] > 0) {
                    $events[] = [
                        'type' => 'substitution',
                        'minute' => $p['minute_in'],
                        'team' => $teamKey,
                        'player_side' => $teamKey,
                        'player_in_key' => $p['player_key'],
                        'player_out_key' => $p['substituted_for_key'],
                    ];
                }
            }
        }

        // Buts : nombre par équipe influencé par le niveau moyen de chaque
        // effectif, avec bruit. Tirés indépendamment (pas de corrélation
        // artificielle imposée entre les deux équipes).
        $homeGoalCount = $this->rng->noisyCount(0.9 + 1.6 * $homeSkillLevel + 0.25 * ($homeSkillLevel - $awaySkillLevel), 1.1, 0, 8);
        $awayGoalCount = $this->rng->noisyCount(0.7 + 1.4 * $awaySkillLevel + 0.25 * ($awaySkillLevel - $homeSkillLevel), 1.0, 0, 8);

        $goalEvents = [];
        $goalEvents = array_merge(
            $goalEvents,
            $this->buildGoalEvents($homeParticipations, $awayParticipations, 'home', $homeGoalCount)
        );
        $goalEvents = array_merge(
            $goalEvents,
            $this->buildGoalEvents($awayParticipations, $homeParticipations, 'away', $awayGoalCount)
        );
        $events = array_merge($events, $goalEvents);

        // Cartons : indépendants du score, un seul carton maximum par joueur
        // (voir accommodation "second_yellow_card" ci-dessus).
        $events = array_merge($events, $this->buildCardEvents($homeParticipations, 'home'));
        $events = array_merge($events, $this->buildCardEvents($awayParticipations, 'away'));

        // Score DÉRIVÉ des événements de but déjà générés (construction, pas contrôle a posteriori).
        $homeScore = count(array_filter($events, fn ($e) => $e['type'] === 'goal' && $e['team'] === 'home'));
        $awayScore = count(array_filter($events, fn ($e) => $e['type'] === 'goal' && $e['team'] === 'away'));

        // Statistiques joueur brutes, pour chaque participant.
        $homeStats = [];
        foreach ($homeParticipations as $p) {
            $homeStats[$p['player_key']] = $this->buildPlayerStats($p, $events, 'home', $missingIndicators, $homeScore, $awayScore);
        }
        $awayStats = [];
        foreach ($awayParticipations as $p) {
            $awayStats[$p['player_key']] = $this->buildPlayerStats($p, $events, 'away', $missingIndicators, $awayScore, $homeScore);
        }

        // Statistiques équipe = agrégation des statistiques joueurs déjà produites.
        $homeTeamStats = $this->aggregateTeamStats($homeStats, $homeScore, true);
        $awayTeamStats = $this->aggregateTeamStats($awayStats, $awayScore, false);

        return [
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'home_participations' => $homeParticipations,
            'away_participations' => $awayParticipations,
            'home_stats' => $homeStats,
            'away_stats' => $awayStats,
            'home_team_stats' => $homeTeamStats,
            'away_team_stats' => $awayTeamStats,
            'events' => $events,
        ];
    }

    /**
     * Sélectionne le groupe de 18 joueurs convoqués (11 titulaires + 7
     * remplaçants disponibles) à partir de l'effectif complet (~20), et
     * décide le calendrier des remplacements (0 à 4 par équipe).
     * $override permet le cas dégradé "changement de poste en cours de
     * saison" : ce match précis voit le joueur désigné jouer à un autre
     * poste détaillé que celui de son effectif de référence.
     */
    private function selectMatchdaySquadAndSubs(array $squad, array $override): array
    {
        // player_key = index dans $squad, stable pour tout le match.
        $byPosition = [];
        foreach ($squad as $key => $player) {
            $pos = $override[$key] ?? $player['detailed_position'];
            $byPosition[$pos][] = $key;
        }

        $starterKeys = [];
        foreach (PositionCatalog::STARTING_ELEVEN_SLOTS as $slot) {
            $candidates = $byPosition[$slot] ?? [];
            if ($candidates === []) {
                // Filet de sécurité : ne devrait pas arriver avec SQUAD_TEMPLATE,
                // mais évite un titulaire manquant si $override a vidé une ligne.
                $candidates = array_keys($squad);
            }
            $starterKeys[] = $this->rng->pick(array_values(array_diff($candidates, $starterKeys)) ?: $candidates);
        }

        $benchKeys = array_values(array_diff(array_keys($squad), $starterKeys));
        $benchKeys = $this->rng->shuffleArray($benchKeys);
        $availableSubKeys = array_slice($benchKeys, 0, min(7, count($benchKeys)));

        $subCount = $this->rng->nextInt(0, min(4, count($availableSubKeys)));
        $subsUsed = array_slice($this->rng->shuffleArray($availableSubKeys), 0, $subCount);

        $substitutions = [];
        $offPoolKeys = $this->rng->shuffleArray($starterKeys);
        foreach ($subsUsed as $i => $subKey) {
            $substitutions[] = [
                'off_key' => $offPoolKeys[$i % count($offPoolKeys)],
                'on_key' => $subKey,
                'minute' => $this->rng->nextInt(46, 89),
            ];
        }
        // Un seul remplacement par sortant, on retire les doublons de "off".
        $seenOff = [];
        $substitutions = array_values(array_filter($substitutions, function ($s) use (&$seenOff) {
            if (isset($seenOff[$s['off_key']])) {
                return false;
            }
            $seenOff[$s['off_key']] = true;

            return true;
        }));

        return [
            'squad' => $squad,
            'override' => $override,
            'starter_keys' => $starterKeys,
            'substitutions' => $substitutions,
        ];
    }

    private function buildParticipations(array $selection, bool $forceLateCameo): array
    {
        $squad = $selection['squad'];
        $override = $selection['override'];
        $starterKeys = $selection['starter_keys'];
        $substitutions = $selection['substitutions'];

        $offMinuteByKey = [];
        $onInfoByKey = [];
        foreach ($substitutions as $i => $s) {
            $minute = $s['minute'];
            if ($forceLateCameo && $i === count($substitutions) - 1) {
                // Cas dégradé "joueur à très peu de minutes" : le dernier
                // changement de ce match est forcé en toute fin de rencontre.
                // Borné à 89 (pas 90) : un remplaçant entrant à la 90e minute
                // exacte joue 0 minute, ce qui n'est plus une participation
                // (bogue détecté par le harnais de validation réel, voir rapport).
                $minute = $this->rng->nextInt(86, 89);
            }
            $offMinuteByKey[$s['off_key']] = $minute;
            $onInfoByKey[$s['on_key']] = ['minute' => $minute, 'replaces' => $s['off_key']];
        }

        $participations = [];
        foreach ($starterKeys as $key) {
            $player = $squad[$key];
            $detailedPosition = $override[$key] ?? $player['detailed_position'];
            $minuteOut = $offMinuteByKey[$key] ?? self::MATCH_MINUTES;
            $participations[] = [
                'player_key' => $key,
                'player' => $player,
                'detailed_position' => $detailedPosition,
                'is_starter' => true,
                'minute_in' => 0,
                'minute_out' => $minuteOut,
                'minutes_played' => $minuteOut,
                'jersey_number' => $player['jersey_number'],
                'substituted_for_key' => null,
            ];
        }
        foreach ($onInfoByKey as $key => $info) {
            $player = $squad[$key];
            // Un remplaçant hérite du poste détaillé du joueur qu'il remplace
            // (remplacement "poste pour poste"), hypothèse documentée dans le
            // rapport plutôt qu'une réorganisation tactique complète.
            $detailedPosition = $squad[$info['replaces']]['detailed_position'];
            $participations[] = [
                'player_key' => $key,
                'player' => $player,
                'detailed_position' => $detailedPosition,
                'is_starter' => false,
                'minute_in' => $info['minute'],
                'minute_out' => self::MATCH_MINUTES,
                'minutes_played' => self::MATCH_MINUTES - $info['minute'],
                'jersey_number' => $player['jersey_number'],
                'substituted_for_key' => $info['replaces'],
            ];
        }

        return $participations;
    }

    private function playersOnPitchAtMinute(array $participations, int $minute): array
    {
        return array_values(array_filter(
            $participations,
            fn ($p) => $p['minute_in'] <= $minute && $minute < $p['minute_out']
        ));
    }

    private function buildGoalEvents(array $scoringSideParticipations, array $concedingSideParticipations, string $scoringTeamKey, int $goalCount): array
    {
        $events = [];
        // Poids d'ouverture au but par poste : les attaquants/ailiers marquent
        // plus souvent, les gardiens jamais (simplification assumée).
        $scoreWeight = [
            'CF' => 5, 'LAM' => 3, 'RAM' => 3, 'CAM' => 3, 'RCAM' => 2.5,
            'LCM' => 1.5, 'CDM' => 0.6, 'LCB' => 0.4, 'RCB' => 0.4, 'LB' => 0.5, 'RB' => 0.5, 'GK' => 0,
        ];

        for ($i = 0; $i < $goalCount; $i++) {
            $minute = $this->rng->nextInt(1, 90);
            $onPitch = $this->playersOnPitchAtMinute($scoringSideParticipations, $minute);
            if ($onPitch === []) {
                continue;
            }

            $isOwnGoal = $this->rng->chance(0.02);
            if ($isOwnGoal) {
                $concedingOnPitch = $this->playersOnPitchAtMinute($concedingSideParticipations, $minute);
                if ($concedingOnPitch === []) {
                    $isOwnGoal = false;
                }
            }

            // 'player_side'/'assist_side' distinguent explicitement de quelle
            // liste de participations (domicile/extérieur) le player_key doit
            // être résolu : les clés de joueur sont des index d'effectif
            // (0..19) réutilisés indépendamment côté domicile et extérieur,
            // donc 'team' (bénéficiaire du but) seul ne suffit PAS à retrouver
            // le bon joueur pour un but contre son camp.
            $scoringSideKey = $scoringTeamKey;
            $concedingSideKey = $scoringTeamKey === 'home' ? 'away' : 'home';

            if ($isOwnGoal) {
                $scorer = $this->weightedPick($concedingOnPitch, $scoreWeight, 'detailed_position', 0.2);
                $events[] = [
                    'type' => 'goal',
                    'minute' => $minute,
                    'team' => $scoringTeamKey, // équipe BÉNÉFICIAIRE
                    'player_key' => $scorer['player_key'],
                    'player_side' => $concedingSideKey, // l'auteur appartient à l'équipe qui ENCAISSE
                    'own_goal' => true,
                    'assist_key' => null,
                    'assist_side' => null,
                ];

                continue;
            }

            $scorer = $this->weightedPick($onPitch, $scoreWeight, 'detailed_position', 0.3);
            $assistCandidates = array_values(array_filter($onPitch, fn ($p) => $p['player_key'] !== $scorer['player_key']));
            $assistKey = null;
            if ($assistCandidates !== [] && $this->rng->chance(0.6)) {
                $assistCandidate = $this->weightedPick($assistCandidates, $scoreWeight, 'detailed_position', 0.5);
                $assistKey = $assistCandidate['player_key'];
            }

            $events[] = [
                'type' => 'goal',
                'minute' => $minute,
                'team' => $scoringTeamKey,
                'player_key' => $scorer['player_key'],
                'player_side' => $scoringSideKey,
                'own_goal' => false,
                'assist_key' => $assistKey,
                'assist_side' => $assistKey !== null ? $scoringSideKey : null,
            ];
        }

        return $events;
    }

    private function buildCardEvents(array $participations, string $teamKey): array
    {
        $events = [];
        $cardedKeys = [];
        $yellowCount = $this->rng->noisyCount(1.6, 1.1, 0, 6);
        for ($i = 0; $i < $yellowCount; $i++) {
            $minute = $this->rng->nextInt(1, 90);
            $onPitch = $this->playersOnPitchAtMinute($participations, $minute);
            $onPitch = array_values(array_filter($onPitch, fn ($p) => ! isset($cardedKeys[$p['player_key']])));
            if ($onPitch === []) {
                continue;
            }
            $player = $this->rng->pick($onPitch);
            $cardedKeys[$player['player_key']] = true;
            $events[] = ['type' => 'yellow_card', 'minute' => $minute, 'team' => $teamKey, 'player_key' => $player['player_key'], 'player_side' => $teamKey];
        }

        if ($this->rng->chance(0.06)) {
            $minute = $this->rng->nextInt(1, 90);
            $onPitch = $this->playersOnPitchAtMinute($participations, $minute);
            $onPitch = array_values(array_filter($onPitch, fn ($p) => ! isset($cardedKeys[$p['player_key']])));
            if ($onPitch !== []) {
                $player = $this->rng->pick($onPitch);
                $events[] = ['type' => 'red_card', 'minute' => $minute, 'team' => $teamKey, 'player_key' => $player['player_key'], 'player_side' => $teamKey];
            }
        }

        return $events;
    }

    /**
     * Tirage pondéré simple (poids + plancher pour ne pas exclure totalement
     * un poste peu probable).
     */
    private function weightedPick(array $items, array $weights, string $weightKeyField, float $floor): array
    {
        $total = 0.0;
        $cumulative = [];
        foreach ($items as $item) {
            $w = ($weights[$item[$weightKeyField]] ?? 0.3) + $floor;
            $total += $w;
            $cumulative[] = [$total, $item];
        }
        $r = $this->rng->nextFloat(0, $total);
        foreach ($cumulative as [$threshold, $item]) {
            if ($r <= $threshold) {
                return $item;
            }
        }

        return end($cumulative)[1];
    }

    private function buildPlayerStats(array $participation, array $events, string $teamKey, bool $missingIndicators, int $teamGoals, int $opponentGoals): array
    {
        $player = $participation['player'];
        $detailedPosition = $participation['detailed_position'];
        $broadGroup = PositionCatalog::BROAD_GROUP[$detailedPosition];
        $minutes = $participation['minutes_played'];
        $skill = $player['skill'];
        $factor = ($minutes / 90.0) * (0.6 + 0.8 * $skill);

        $profile = self::POSITION_PROFILES[$detailedPosition] ?? self::POSITION_PROFILES['LCM'];

        $goalsScored = count(array_filter(
            $events,
            fn ($e) => $e['type'] === 'goal' && ! $e['own_goal'] && $e['team'] === $teamKey && $e['player_key'] === $participation['player_key']
        ));
        $assists = count(array_filter(
            $events,
            fn ($e) => $e['type'] === 'goal' && $e['team'] === $teamKey && ($e['assist_key'] ?? null) === $participation['player_key']
        ));
        $yellowCards = count(array_filter($events, fn ($e) => $e['type'] === 'yellow_card' && $e['team'] === $teamKey && $e['player_key'] === $participation['player_key']));
        $redCards = count(array_filter($events, fn ($e) => $e['type'] === 'red_card' && $e['team'] === $teamKey && $e['player_key'] === $participation['player_key']));

        $passesTotal = $this->rng->noisyCount(55 * $profile['passing'] * $factor, 6, 0);
        $passRate = $this->clampRate(0.65 + 0.25 * $skill + $this->rng->gaussianNoise(0.05));
        $passesCompleted = $this->boundedSuccess($passesTotal, $passRate);

        $progressivePasses = $this->boundedSuccess($passesCompleted, $this->clampRate(0.25 * $profile['progressive'] + 0.1));
        $progRate = $this->clampRate(0.55 + 0.2 * $skill);
        $progressivePassesCompleted = $this->boundedSuccess($progressivePasses, $progRate);

        $longPasses = $this->rng->noisyCount(6 * $profile['passing'] * $factor, 2, 0);
        $longPassesCompleted = $this->boundedSuccess($longPasses, $this->clampRate(0.45 + 0.25 * $skill));

        $crossesTotal = $this->rng->noisyCount(6 * $profile['crossing'] * $factor, 2, 0);
        $crossesCompleted = $this->boundedSuccess($crossesTotal, $this->clampRate(0.3 + 0.2 * $skill));

        $dribblesAttempted = $this->rng->noisyCount(4 * $profile['dribbling'] * $factor, 1.5, 0);
        $dribblesCompleted = $this->boundedSuccess($dribblesAttempted, $this->clampRate(0.45 + 0.25 * $skill));

        $tacklesTotal = $this->rng->noisyCount(3.5 * $profile['defending'] * $factor, 1.5, 0);
        $tacklesWon = $this->boundedSuccess($tacklesTotal, $this->clampRate(0.5 + 0.25 * $skill));

        $aerialTotal = $this->rng->noisyCount(3 * $profile['aerial'] * $factor, 1.3, 0);
        $aerialWon = $this->boundedSuccess($aerialTotal, $this->clampRate(0.4 + 0.3 * $skill));

        $groundTotal = $this->rng->noisyCount(4 * (0.4 + $profile['defending'] * 0.3 + $profile['dribbling'] * 0.3) * $factor, 1.5, 0);
        $groundWon = $this->boundedSuccess($groundTotal, $this->clampRate(0.45 + 0.25 * $skill));

        $shotsTotal = $this->rng->noisyCount(2.6 * $profile['shooting'] * $factor, 1.2, 0);
        $shotsOnTarget = $this->boundedSuccess($shotsTotal, $this->clampRate(0.35 + 0.25 * $skill));
        $shotsBlocked = $this->boundedSuccess($shotsTotal - $shotsOnTarget, 0.4);
        $shotsOffTarget = max(0, $shotsTotal - $shotsOnTarget - $shotsBlocked);
        $shotsInsideBox = $this->boundedSuccess($shotsTotal, 0.7);
        $shotsOutsideBox = max(0, $shotsTotal - $shotsInsideBox);
        $expectedGoals = round($shotsTotal > 0 ? ($shotsOnTarget * (0.18 + 0.15 * $skill) + $shotsOutsideBox * 0.03) : 0.0, 3);

        $interceptions = $this->rng->noisyCount(2 * $profile['defending'] * $factor, 1, 0);
        $clearances = $this->rng->noisyCount(2 * $profile['defending'] * $profile['aerial'] * $factor, 1.2, 0);
        $blocks = $this->rng->noisyCount(0.8 * $profile['defending'] * $factor, 0.6, 0);
        $recoveries = $this->rng->noisyCount(4 * $profile['defending'] * $factor + 1, 1.5, 0);

        $foulsCommitted = $this->rng->noisyCount(1.2 * (1.1 - $skill * 0.4) * $factor, 0.8, 0);
        $foulsDrawn = $this->rng->noisyCount(1.0 * $profile['dribbling'] * $factor, 0.8, 0);
        $offsides = $this->rng->noisyCount(0.6 * $profile['shooting'] * $factor, 0.6, 0);

        $chancesCreated = $this->boundedSuccess($passesCompleted, $this->clampRate(0.03 + 0.05 * $profile['crossing'] + 0.05 * $profile['dribbling']));
        $mistakesLeadingToShot = $this->rng->chance(0.06) ? 1 : 0;
        $mistakesLeadingToGoal = $mistakesLeadingToShot === 1 && $this->rng->chance(0.15) ? 1 : 0;

        $challengesDefensive = $this->rng->noisyCount(3 * $profile['defending'] * $factor, 1.3, 0);
        $challengesDefensiveWon = $this->boundedSuccess($challengesDefensive, $this->clampRate(0.5 + 0.2 * $skill));
        $challengesAttacking = $this->rng->noisyCount(3 * $profile['dribbling'] * $factor, 1.3, 0);
        $challengesAttackingWon = $this->boundedSuccess($challengesAttacking, $this->clampRate(0.45 + 0.2 * $skill));

        $distanceCoveredKm = round((9.0 + 1.5 * $profile['pace']) * ($minutes / 90.0) + $this->rng->gaussianNoise(0.4), 1);
        $distanceCoveredKm = max(0.0, $distanceCoveredKm);
        $maxSpeedKmh = round(24 + 8 * $profile['pace'] + $this->rng->gaussianNoise(1.2), 1);

        $gk = null;
        if ($detailedPosition === 'GK') {
            $goalsConceded = count(array_filter(
                $events,
                fn ($e) => $e['type'] === 'goal' && $e['team'] !== $teamKey
            ));
            $saves = $this->rng->noisyCount(2.6 + $goalsConceded * 0.6, 1.3, 0);
            $shotsFacedOnTarget = $goalsConceded + $saves;
            $shotsFaced = $shotsFacedOnTarget + $this->rng->noisyCount(2, 1, 0);
            $gk = [
                'gk_shots_faced' => $shotsFaced,
                'gk_shots_faced_on_target' => $shotsFacedOnTarget,
                'gk_saves' => $saves,
                'gk_goals_conceded' => $goalsConceded,
                'gk_expected_goals_faced' => round($shotsFacedOnTarget * (0.25 + 0.15 * (1 - $skill)), 3),
                'gk_claims_exits' => $this->rng->noisyCount(2, 1, 0),
                'gk_long_passes' => $this->rng->noisyCount(18, 4, 5),
                'gk_long_passes_completed' => null, // rempli ci-dessous
                'gk_errors' => $this->rng->chance(0.08) ? 1 : 0,
            ];
            $gk['gk_long_passes_completed'] = $this->boundedSuccess($gk['gk_long_passes'], $this->clampRate(0.4 + 0.3 * $skill));
        }

        // Note de match : actions du match + niveau propre au joueur. Sans ce
        // niveau, la note passée d'un joueur ne prédisait rien de la suivante,
        // ce qui rendait les données démo inutilisables pour un modèle de
        // sélection (même calibrage que ~/demo-calibration/calibrate_ratings.py).
        $matchRating = round(min(10, max(1, 6.0
            + $goalsScored * 0.8 + $assists * 0.5
            - $yellowCards * 0.3 - $redCards * 1.5
            + ($passesTotal > 0 ? ($passesCompleted / $passesTotal - 0.75) * 2 : 0)
            + $this->playerRatingLevel($player, $detailedPosition)
            + 0.2 * ($teamGoals <=> $opponentGoals)
            + $this->rng->gaussianNoise(0.35))), 1);

        $stats = [
            'position_played' => PositionCatalog::TO_LEGACY[$detailedPosition],
            'minutes_played' => $minutes,
            'started_match' => $participation['is_starter'],
            'substituted_in' => ! $participation['is_starter'],
            'substituted_out' => $participation['minute_out'] < self::MATCH_MINUTES,
            'substitution_minute' => $participation['minute_out'] < self::MATCH_MINUTES ? $participation['minute_out'] : ($participation['minute_in'] > 0 ? $participation['minute_in'] : null),
            'goals_scored' => $goalsScored,
            'assists_provided' => $assists,
            'shots_total' => $shotsTotal,
            'shots_on_target' => $shotsOnTarget,
            'shots_off_target' => $shotsOffTarget,
            'shots_blocked' => $shotsBlocked,
            'shots_inside_box' => $shotsInsideBox,
            'shots_outside_box' => $shotsOutsideBox,
            'passes_total' => $passesTotal,
            'passes_completed' => $passesCompleted,
            'passes_failed' => $passesTotal - $passesCompleted,
            'key_passes' => $chancesCreated,
            'long_passes' => $longPasses,
            'long_passes_completed' => $longPassesCompleted,
            'crosses_total' => $crossesTotal,
            'crosses_completed' => $crossesCompleted,
            'dribbles_attempted' => $dribblesAttempted,
            'dribbles_completed' => $dribblesCompleted,
            'tackles_total' => $tacklesTotal,
            'tackles_won' => $tacklesWon,
            'tackles_lost' => $tacklesTotal - $tacklesWon,
            'interceptions' => $interceptions,
            'clearances' => $clearances,
            'blocks' => $blocks,
            'recoveries' => $recoveries,
            'aerial_duels_total' => $aerialTotal,
            'aerial_duels_won' => $aerialWon,
            'aerial_duels_lost' => $aerialTotal - $aerialWon,
            'ground_duels_total' => $groundTotal,
            'ground_duels_won' => $groundWon,
            'ground_duels_lost' => $groundTotal - $groundWon,
            'fouls_committed' => $foulsCommitted,
            'fouls_drawn' => $foulsDrawn,
            'yellow_cards' => $yellowCards,
            'red_cards' => $redCards,
            'second_yellow_cards' => 0,
            'offsides' => $offsides,
            'distance_covered_km' => $distanceCoveredKm,
            'max_speed_kmh' => $maxSpeedKmh,
            'match_rating' => $matchRating,
            'home_match' => $teamKey === 'home',
            'away_match' => $teamKey === 'away',
            'team_goals_scored' => $teamGoals,
            'team_goals_conceded' => $opponentGoals,
            'goal_difference' => $teamGoals - $opponentGoals,
            // match_result/match_importance/data_source/data_quality portent des
            // contraintes CHECK pré-existantes (découvertes en cours de
            // Livrable 3, aucune n'a d'option "démo"/"synthétique") : on choisit
            // la valeur autorisée la moins trompeuse plutôt que d'en violer une
            // ou d'en demander l'élargissement. is_demo reste le signal
            // faisant foi (mandat : bandeau "Données de démonstration").
            'match_result' => $teamGoals > $opponentGoals ? 'win' : ($teamGoals < $opponentGoals ? 'loss' : 'draw'),
            'match_importance' => 'league',
            'data_source' => 'manual_entry', // CHECK existant : pas d'option "démo" disponible
            'data_confidence' => 100,
            'data_quality' => 'fair', // CHECK existant restreint à excellent/good/fair/poor
            // Colonnes ajoutées par le Livrable 1 (§2), alignées cockpit/KSA :
            'expected_goals' => $expectedGoals,
            'expected_goals_on_target' => round($expectedGoals * 0.85, 3),
            'progressive_passes' => $progressivePasses,
            'progressive_passes_completed' => $progressivePassesCompleted,
            'challenges_defensive' => $challengesDefensive,
            'challenges_defensive_won' => $challengesDefensiveWon,
            'challenges_attacking' => $challengesAttacking,
            'challenges_attacking_won' => $challengesAttackingWon,
            'dribbles_final_third' => $this->boundedSuccess($dribblesAttempted, 0.5),
            'dribbles_final_third_completed' => null, // rempli ci-dessous
            'chances_created' => $chancesCreated,
            'mistakes_leading_to_shot' => $mistakesLeadingToShot,
            'mistakes_leading_to_goal' => $mistakesLeadingToGoal,
            'gk' => $gk,
        ];
        $stats['dribbles_final_third_completed'] = $this->boundedSuccess($stats['dribbles_final_third'], $this->clampRate(0.45 + 0.2 * $skill));

        if ($missingIndicators) {
            // Cas dégradé "indicateurs manquants" (mandat, LIVRABLE 3) :
            // quelques champs moins courants remontent NULL, jamais 0 —
            // simulant un fournisseur qui ne calcule pas ces indicateurs,
            // jamais une vraie valeur nulle.
            foreach (['expected_goals', 'expected_goals_on_target', 'chances_created', 'progressive_passes', 'progressive_passes_completed'] as $field) {
                $stats[$field] = null;
            }
            if ($gk !== null) {
                $stats['gk']['gk_expected_goals_faced'] = null;
            }
        }

        return $stats;
    }

    /**
     * Niveau de note propre au joueur, stable sur toute la saison :
     *  - niveau général dérivé de son skill (écart-type ≈ 0,4 point de note) ;
     *  - écart propre à chaque famille de poste (écart-type 0,25) ;
     *  - pénalité hors poste : −0,15 sur un poste voisin, −0,4 sinon.
     * L'écart par poste vient d'un générateur séparé, amorcé par l'identité
     * du joueur : il ne consomme aucun tirage du générateur principal, donc
     * le reste de la simulation (scores, événements, statistiques) est inchangé.
     */
    private function playerRatingLevel(array $player, string $playedPosition): float
    {
        $mainFamily = self::FAMILY[$player['detailed_position']] ?? null;
        $playedFamily = self::FAMILY[$playedPosition] ?? null;

        $level = ($player['skill'] - 0.5) * (0.4 / 0.15);

        $identity = sprintf('%d|%s|%s|%.6f|%s', $this->rng->seed(), $player['name'], $player['detailed_position'], $player['skill'], $playedFamily);
        $level += (new SeededRandom(crc32($identity)))->gaussianNoise(0.25);

        if ($mainFamily !== null && $playedFamily !== null && $mainFamily !== $playedFamily) {
            $isNeighbour = $mainFamily !== 'gardien' && $playedFamily !== 'gardien'
                && in_array($playedFamily, self::NEIGHBOUR_FAMILIES[$mainFamily], true);
            $level += $isNeighbour ? -0.15 : -0.4;
        }

        return $level;
    }

    /** Familles de poste, alignées sur la table position_catalog. */
    private const FAMILY = [
        'GK' => 'gardien',
        'LCB' => 'défenseur central', 'RCB' => 'défenseur central',
        'LB' => 'latéral', 'RB' => 'latéral',
        'CDM' => 'milieu défensif', 'LCM' => 'milieu relayeur',
        'CAM' => 'milieu offensif', 'RCAM' => 'milieu offensif',
        'LAM' => 'ailier', 'RAM' => 'ailier',
        'CF' => 'avant-centre',
    ];

    private const NEIGHBOUR_FAMILIES = [
        'gardien' => [],
        'défenseur central' => ['latéral', 'milieu défensif'],
        'latéral' => ['défenseur central', 'ailier'],
        'milieu défensif' => ['milieu relayeur', 'défenseur central'],
        'milieu relayeur' => ['milieu défensif', 'milieu offensif'],
        'milieu offensif' => ['milieu relayeur', 'ailier', 'avant-centre'],
        'ailier' => ['milieu offensif', 'latéral', 'avant-centre'],
        'avant-centre' => ['ailier', 'milieu offensif'],
    ];

    private function clampRate(float $rate): float
    {
        return max(0.0, min(1.0, $rate));
    }

    /**
     * Réussites <= tentatives, TOUJOURS (mandat, LIVRABLE 2, repris ici par
     * discipline de construction même si aucune contrainte SQL ne l'impose).
     */
    private function boundedSuccess(int $attempts, float $rate): int
    {
        if ($attempts <= 0) {
            return 0;
        }

        return (int) min($attempts, max(0, round($attempts * $this->clampRate($rate))));
    }

    private const POSITION_PROFILES = [
        'GK' => ['passing' => .5, 'progressive' => .1, 'crossing' => 0, 'dribbling' => .05, 'defending' => .3, 'aerial' => .3, 'shooting' => 0, 'pace' => .2],
        'LCB' => ['passing' => .55, 'progressive' => .35, 'crossing' => .05, 'dribbling' => .15, 'defending' => .85, 'aerial' => .8, 'shooting' => .05, 'pace' => .45],
        'RCB' => ['passing' => .55, 'progressive' => .35, 'crossing' => .05, 'dribbling' => .15, 'defending' => .85, 'aerial' => .8, 'shooting' => .05, 'pace' => .45],
        'LB' => ['passing' => .55, 'progressive' => .4, 'crossing' => .55, 'dribbling' => .35, 'defending' => .65, 'aerial' => .35, 'shooting' => .1, 'pace' => .7],
        'RB' => ['passing' => .55, 'progressive' => .4, 'crossing' => .55, 'dribbling' => .35, 'defending' => .65, 'aerial' => .35, 'shooting' => .1, 'pace' => .7],
        'CDM' => ['passing' => .7, 'progressive' => .45, 'crossing' => .1, 'dribbling' => .3, 'defending' => .75, 'aerial' => .55, 'shooting' => .15, 'pace' => .5],
        'LCM' => ['passing' => .75, 'progressive' => .55, 'crossing' => .2, 'dribbling' => .45, 'defending' => .45, 'aerial' => .35, 'shooting' => .25, 'pace' => .55],
        'CAM' => ['passing' => .65, 'progressive' => .5, 'crossing' => .3, 'dribbling' => .6, 'defending' => .2, 'aerial' => .25, 'shooting' => .55, 'pace' => .6],
        'RCAM' => ['passing' => .65, 'progressive' => .5, 'crossing' => .3, 'dribbling' => .6, 'defending' => .2, 'aerial' => .25, 'shooting' => .55, 'pace' => .6],
        'LAM' => ['passing' => .55, 'progressive' => .35, 'crossing' => .65, 'dribbling' => .7, 'defending' => .15, 'aerial' => .2, 'shooting' => .5, 'pace' => .8],
        'RAM' => ['passing' => .55, 'progressive' => .35, 'crossing' => .65, 'dribbling' => .7, 'defending' => .15, 'aerial' => .2, 'shooting' => .5, 'pace' => .8],
        'CF' => ['passing' => .45, 'progressive' => .2, 'crossing' => .1, 'dribbling' => .55, 'defending' => .1, 'aerial' => .45, 'shooting' => .85, 'pace' => .7],
    ];

    private function aggregateTeamStats(array $playerStats, int $goalsScored, bool $isHome): array
    {
        $sum = function (string $field) use ($playerStats) {
            $values = array_filter(array_column($playerStats, $field), fn ($v) => $v !== null);

            return $values === [] ? null : array_sum($values);
        };

        return [
            'is_home' => $isHome,
            'possession_pct' => null, // renseigné par DemoDataGenerator (paire home/away sommant à 100)
            'shots_total' => $sum('shots_total'),
            'shots_on_target' => $sum('shots_on_target'),
            'corners' => null, // pas d'équivalent au niveau joueur dans ce schéma : généré indépendamment (voir rapport)
            'fouls' => $sum('fouls_committed'),
            'offsides' => $sum('offsides'),
            'yellow_cards' => $sum('yellow_cards'),
            'red_cards' => $sum('red_cards'),
            'expected_goals' => $sum('expected_goals'),
            'goals_scored' => $goalsScored,
        ];
    }
}
