<?php

// Configuration STRUCTURELLE du moteur de calcul « rôle et apport »
// (docs/role-evaluation/05-proposition-moteur-calcul.md, questions validées
// le 30/09). Ce fichier ne contient AUCUN poids (role_config_weights, en
// base, versionné, est la seule source de poids — jamais ce fichier) :
// seulement la structure, commune à toutes les versions de configuration —
// dimensions, indicateurs, correspondance vers les colonnes de
// player_match_detailed_stats, hyperparamètres statistiques.
//
// Réutilise directement app/Services/PerformanceScoreCalculator.php (non
// modifié) : ce tableau, complété par les poids d'une version
// role_config_versions et par position_catalog, devient le $cfg injecté à
// son constructeur (voir app/Services/RoleEvaluationEngine/CalculatorConfigBuilder.php).
//
// Vocabulaire des dimensions : repris de config/player_performance_score.php
// (moteur « phase 1 », préexistant à ce mandat) pour les 6 dimensions de
// champ et les 4 dimensions gardien — décision validée le 30/09 (question 2)
// — PLUS une 7e dimension de champ, "progression_avancee", qui exploite les
// colonnes ajoutées au Livrable 1 et non utilisées par la phase 1 (xG, xG
// cadré, passes progressives, dribbles dans le dernier tiers, occasions
// créées).
//
// Les familles sont en français, alignées sur position_catalog.family
// (Livrable 1, §9) : défenseur central, latéral, milieu défensif, milieu
// relayeur, milieu offensif, ailier, avant-centre, gardien. Elles
// correspondent une à une aux 8 familles anglaises de la phase 1 (voir
// proposition, section « Vocabulaire des dimensions »).
return [
    // Hyperparamètres statistiques : copiés tels quels depuis
    // config/player_performance_score.php (version 1.1.0), qui prédate ce
    // mandat et n'a jamais été calibrée sur des données de démonstration.
    // Rester identique ici évite d'introduire une deuxième méthode
    // statistique divergente pour un même type de calcul. Toute évolution de
    // ces valeurs reste un réglage de modèle : soumis à la même règle que les
    // poids (jamais sur données de démonstration).
    'min_reference_minutes' => 270,
    'min_reference_players' => 30,
    // Profils de saison (exports « Player statistics ») : un export couvre un
    // seul club, la référence d'un poste est donc d'abord l'effectif lui-même,
    // élargi automatiquement à chaque nouveau club importé. Seuil plus bas
    // assumé et tracé (model_version « -period », taille de la référence).
    'period_min_reference_players' => 12,
    // Repères affichés dans le cockpit (indicatifs, non utilisés par le calcul) :
    // joueurs réguliers par poste pour estimer la variance entre joueurs,
    // clubs importés de la même compétition, minutes jouées pour qu'un score
    // individuel dépasse en pratique la fiabilité minimale.
    'period_guidance' => ['family_regulars' => 10, 'min_clubs' => 6, 'player_minutes' => 450],
    'half_life_matches' => 8,
    'minute_weight_denominator' => 60,
    'rate_prior_attempts' => 15,
    'keeper_save_prior_attempts' => 40,
    'z_cap' => 3,
    'mad_scale' => 1.4826,
    'mad_floor' => 0.05,
    'score_center' => 50,
    'score_scale' => 15,
    'tau_variance_floor' => 0.0001,
    'interval_multiplier_80' => 1.28,
    'minimum_reliability_display' => 30,
    'reliability_levels' => ['medium' => 40, 'solid' => 70],

    // Pool de référence : élargissement vers des familles voisines quand une
    // famille n'a pas assez de joueurs (>= min_reference_players). Repris tel
    // quel de config/player_performance_score.php, EN ANGLAIS.
    //
    // DÉCOUVERTE (exécution réelle) : PerformanceScoreCalculator compare la
    // chaîne littérale "goalkeeper" à 5 endroits de son code (non modifié)
    // pour son traitement spécifique du gardien — il n'est donc PAS
    // agnostique au nom des familles, contrairement à ce que la proposition
    // affirmait. Tout le calcul interne (CalculatorConfigBuilder,
    // RoleFitEvaluator) reste donc en anglais, comme la phase 1 ; seules les
    // colonnes en base (position_catalog.family, role_config_weights.
    // position_family, player_role_evaluations.position_family_evaluated)
    // restent en français, via PositionFamilyTranslator aux deux bords.
    // "field" n'est jamais une vraie famille, seulement l'agrégat "tout
    // joueur de champ" utilisé par le moteur phase 1 pour l'élargissement du
    // pool de référence ; RoleFitEvaluator l'exclut des familles candidates
    // pour l'adéquation au rôle (question 5, validée : familles voisines
    // uniquement).
    'reference_fallback' => [
        'central' => ['lateral', 'field'],
        'lateral' => ['central', 'field'],
        'defensive_midfield' => ['central_midfield', 'field'],
        'central_midfield' => ['defensive_midfield', 'field'],
        'attacking_midfield' => ['winger', 'field'],
        'winger' => ['attacking_midfield', 'field'],
        'striker' => ['attacking_midfield', 'field'],
        'goalkeeper' => [],
    ],

    // Dimensions par type de poste ("field" = tout joueur de champ,
    // "goalkeeper" = gardien ; ce ne sont PAS des familles, seulement les
    // deux gabarits de dimensions que le moteur phase 1 utilise déjà).
    'dimensions' => [
        'field' => [
            'finition_creation' => ['goals', 'assists', 'shots_on_target', 'key_passes'],
            'construction' => ['passes', 'pass_rate', 'long_pass_rate'],
            'progression_dribbles' => ['dribbles', 'dribble_rate', 'crosses', 'cross_rate'],
            'duels' => ['tackles', 'tackle_rate', 'ground_duel_rate', 'aerial_duel_rate'],
            'recuperation' => ['interceptions', 'recoveries'],
            'discipline' => ['fouls', 'yellow_cards', 'red_cards', 'mistakes_chances', 'mistakes_goals', 'penalties_conceded', 'balls_lost'],
            // 7e dimension (décision validée du 30/09) : exploite les
            // colonnes Livrable 1 non utilisées par la phase 1.
            'progression_avancee' => ['xg', 'xg_cadre', 'passes_progressives', 'dribbles_dernier_tiers', 'occasions_creees'],
        ],
        'goalkeeper' => [
            'arrets_buts_evites' => ['save_rate', 'goals_prevented'],
            // Complétée par le correctif du 30/09 (gk_high_claims_*,
            // gk_sweeper_actions_*) : avant ce correctif, ces deux
            // indicateurs — et donc toute la dimension — restaient à null,
            // faute de colonnes distinguant tentatives et réussites.
            'gestion_surface' => ['high_claim_rate', 'sweeper_rate'],
            'jeu_au_pied' => ['pass_rate', 'long_pass_rate', 'passes'],
            'securite' => ['mistakes_chances', 'mistakes_goals', 'penalties_conceded', 'balls_lost'],
        ],
    ],

    // Anti-redondance : au sein d'UNE dimension, volume et taux d'une même
    // action ne comptent que pour une seule voix (mécanisme du moteur
    // existant, réutilisé tel quel).
    'redundancy_groups' => [
        ['passes', 'pass_rate'],
        ['dribbles', 'dribble_rate'],
        ['crosses', 'cross_rate'],
        ['tackles', 'tackle_rate'],
    ],

    // Indicateurs où une valeur plus élevée est défavorable (sens du
    // z-score inversé). Tout indicateur absent d'ici est positif par défaut.
    'direction' => [
        'fouls' => -1, 'yellow_cards' => -1, 'red_cards' => -1,
        'mistakes_chances' => -1, 'mistakes_goals' => -1,
        'penalties_conceded' => -1, 'balls_lost' => -1,
    ],

    // Correspondance indicateur -> colonnes de player_match_detailed_stats.
    // Ce moteur ne fonctionne qu'en mode "précis" (matchs détaillés, jamais
    // en mode "approximé"/moyennes de saison que la phase 1 gère pour ses
    // propres tables) : MatchStatsDataSource ne fournit jamais de joueur
    // sans le tableau "matches", donc les clés average_*/average ne sont
    // jamais lues en pratique ; elles sont renseignées par cohérence avec
    // le format attendu par PerformanceScoreCalculator, jamais exercées.
    'indicators' => [
        // -- Repris tels quels de config/player_performance_score.php,
        //    seuls les noms de colonnes sources changent quand ils diffèrent --
        'goals' => ['type' => 'count', 'field' => 'goals_scored', 'average' => 'goals_scored'],
        'assists' => ['type' => 'count', 'field' => 'assists_provided', 'average' => 'assists_provided'],
        'shots_on_target' => ['type' => 'count', 'field' => 'shots_on_target', 'average' => 'shots_on_target'],
        'key_passes' => ['type' => 'count', 'field' => 'key_passes', 'average' => 'key_passes'],
        'passes' => ['type' => 'count', 'field' => 'passes_total', 'average' => 'passes_total'],
        'dribbles' => ['type' => 'count', 'field' => 'dribbles_attempted', 'average' => 'dribbles_attempted'],
        'crosses' => ['type' => 'count', 'field' => 'crosses_total', 'average' => 'crosses_total'],
        'tackles' => ['type' => 'count', 'field' => 'tackles_total', 'average' => 'tackles_total'],
        'interceptions' => ['type' => 'count', 'field' => 'interceptions', 'average' => 'interceptions'],
        'recoveries' => ['type' => 'count', 'field' => 'recoveries', 'average' => 'recoveries'],
        'fouls' => ['type' => 'count', 'field' => 'fouls_committed', 'average' => 'fouls_committed'],
        'yellow_cards' => ['type' => 'count', 'field' => 'yellow_cards', 'average' => 'yellow_cards'],
        'red_cards' => ['type' => 'count', 'field' => 'red_cards', 'average' => 'red_cards'],
        // Renommage simple (le moteur phase 1 attend le pluriel ; nos
        // colonnes Livrable 1 sont au singulier) — aucune migration requise.
        'mistakes_chances' => ['type' => 'count', 'field' => 'mistakes_leading_to_shot', 'average' => 'mistakes_leading_to_shot'],
        'mistakes_goals' => ['type' => 'count', 'field' => 'mistakes_leading_to_goal', 'average' => 'mistakes_leading_to_goal'],
        // Comblé par le correctif du 30/09 (absent jusque-là).
        'penalties_conceded' => ['type' => 'count', 'field' => 'penalties_conceded', 'average' => 'penalties_conceded'],
        'balls_lost' => ['type' => 'count', 'field' => 'times_dispossessed', 'average' => 'times_dispossessed'],
        'pass_rate' => ['type' => 'rate', 'attempt' => 'passes_total', 'success' => 'passes_completed', 'average_attempt' => 'passes_total', 'average_success' => 'passes_completed'],
        'long_pass_rate' => ['type' => 'rate', 'attempt' => 'long_passes', 'success' => 'long_passes_completed', 'average_attempt' => 'long_passes', 'average_success' => 'long_passes_completed'],
        'dribble_rate' => ['type' => 'rate', 'attempt' => 'dribbles_attempted', 'success' => 'dribbles_completed', 'average_attempt' => 'dribbles_attempted', 'average_success' => 'dribbles_completed'],
        'cross_rate' => ['type' => 'rate', 'attempt' => 'crosses_total', 'success' => 'crosses_completed', 'average_attempt' => 'crosses_total', 'average_success' => 'crosses_completed'],
        'tackle_rate' => ['type' => 'rate', 'attempt' => 'tackles_total', 'success' => 'tackles_won', 'average_attempt' => 'tackles_total', 'average_success' => 'tackles_won'],
        'ground_duel_rate' => ['type' => 'rate', 'attempt' => 'ground_duels_total', 'success' => 'ground_duels_won', 'average_attempt' => 'ground_duels_total', 'average_success' => 'ground_duels_won'],
        'aerial_duel_rate' => ['type' => 'rate', 'attempt' => 'aerial_duels_total', 'success' => 'aerial_duels_won', 'average_attempt' => 'aerial_duels_total', 'average_success' => 'aerial_duels_won'],

        // -- Gardien : noms de colonnes "gk_*" du Livrable 1 --
        'save_rate' => ['type' => 'rate', 'attempt' => 'gk_shots_faced_on_target', 'success' => 'gk_saves', 'average_attempt' => 'gk_shots_faced_on_target', 'average_success' => 'gk_saves'],
        // Comblés par le correctif du 30/09 (gk_claims_exits, la colonne
        // fusionnée du Livrable 1, reste inutilisée par ces deux
        // indicateurs — elle n'est ni supprimée ni renommée, voir la
        // migration correctrice).
        'high_claim_rate' => ['type' => 'rate', 'attempt' => 'gk_high_claims_attempted', 'success' => 'gk_high_claims_completed', 'average_attempt' => 'gk_high_claims_attempted', 'average_success' => 'gk_high_claims_completed'],
        'sweeper_rate' => ['type' => 'rate', 'attempt' => 'gk_sweeper_actions_attempted', 'success' => 'gk_sweeper_actions_completed', 'average_attempt' => 'gk_sweeper_actions_attempted', 'average_success' => 'gk_sweeper_actions_completed'],
        'goals_prevented' => ['type' => 'prevented', 'field' => 'goals_prevented_unused', 'expected' => 'gk_expected_goals_faced', 'conceded' => 'gk_goals_conceded'],

        // -- Nouvelle dimension "progression_avancee" (question 2) --
        'xg' => ['type' => 'count', 'field' => 'expected_goals', 'average' => 'expected_goals'],
        'xg_cadre' => ['type' => 'count', 'field' => 'expected_goals_on_target', 'average' => 'expected_goals_on_target'],
        'passes_progressives' => ['type' => 'rate', 'attempt' => 'progressive_passes', 'success' => 'progressive_passes_completed', 'average_attempt' => 'progressive_passes', 'average_success' => 'progressive_passes_completed'],
        'dribbles_dernier_tiers' => ['type' => 'rate', 'attempt' => 'dribbles_final_third', 'success' => 'dribbles_final_third_completed', 'average_attempt' => 'dribbles_final_third', 'average_success' => 'dribbles_final_third_completed'],
        'occasions_creees' => ['type' => 'count', 'field' => 'chances_created', 'average' => 'chances_created'],
    ],
];
