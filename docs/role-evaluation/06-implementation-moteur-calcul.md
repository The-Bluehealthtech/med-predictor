# Moteur de calcul « rôle et apport » — implémentation

Branche `feature/role-evaluation-schema`. Fait suite à `docs/role-evaluation/05-proposition-moteur-calcul.md`, validée le 30/09 (7 réponses, résumées ci-dessous). Rien n'a été exécuté sur votre base réelle : la seule migration de ce livrable (correction de colonnes, déjà déposée avant validation) reste non exécutée, en attente de votre accord pour `php artisan migrate`.

## Décisions validées (rappel)

1. Réutiliser `PerformanceScoreCalculator` tel quel — oui.
2. Découpage des dimensions — ajouter une 7e dimension de champ (« progression_avancee ») aux 6 existantes, sans toucher au reste.
3. Les deux lacunes de champs (pénalités concédées ; sorties/arrêts du gardien fusionnés) — migration corrective, préparée avant cette session (`2026_09_30_120000_add_missing_columns_for_role_evaluation_dimensions.php`), toujours non exécutée.
4. Recalcul des statistiques de référence sur données disponibles (y compris démo) ≠ réglage de poids interdit — confirmé.
5. Adéquation au rôle : familles voisines uniquement (`reference_fallback`).
6. Une ligne `player_role_evaluations` par famille comparée (option a).
7. `model_version` = `role-fit-v1+phase1-calculator@1.1.0`.

## Ce qui a été construit

- `config/role_evaluation_engine.php` — structure fixe (dimensions, indicateurs, hyperparamètres, familles voisines) injectée dans `PerformanceScoreCalculator`. Aucun poids ici : les poids viennent uniquement de `role_config_weights`, en base.
- `app/Services/RoleEvaluationEngine/PositionFamilyTranslator.php` — traduction français ↔ anglais entre `position_catalog.family` (base) et les clés internes qu'attend `PerformanceScoreCalculator` (voir « Découverte critique » ci-dessous).
- `app/Services/RoleEvaluationEngine/MatchStatsDataSource.php` — lit `match_participations` + `player_match_detailed_stats` + `matches`, filtrable par `is_demo` (obligatoire) et par match, produit le format d'entrée du calculateur.
- `app/Services/RoleEvaluationEngine/CalculatorConfigBuilder.php` — construit le `$cfg` injecté, à partir de `position_catalog` et de `role_config_weights` pour une version donnée. Échoue explicitement si un poids manque pour une famille/dimension.
- `app/Services/RoleEvaluationEngine/RoleFitEvaluator.php` — le cœur du nouveau travail : score « famille jouée » (réutilisation directe du calculateur) + comparaison à chaque famille voisine (`role_fit_score` = écart signé entre le score de la famille comparée et celui de la famille jouée).
- `app/Console/Commands/RoleEvaluationComputeCommand.php` (`role-eval:compute`) — matérialise des lignes `player_role_evaluations`. `--is-demo` est obligatoire (jamais deviné). `--dry-run` disponible.
- `app/Models/PositionCatalog.php`, `RoleConfigVersion.php`, `RoleConfigWeight.php`, `PlayerRoleEvaluation.php` — modèles Eloquent minces (aucun n'existait avant ce livrable).
- Tests : `tests/Unit/RoleEvaluationEngine/PositionFamilyTranslatorTest.php` (8 tests), `tests/Feature/RoleEvaluationComputeCommandTest.php` (6 tests) — dry-run, `--is-demo` obligatoire, `is_demo`/familles françaises correctement écrits, poids manquant rejeté explicitement, données sources jamais modifiées, écart de comparaison de famille voisine vérifié numériquement.

## Validation par exécution réelle

Comme pour le Livrable 3, tout a été exécuté pour de vrai (PHP 8.4, copie jetable de votre base des 490 lignes réelles), pas seulement raisonné :

- Les **72 tests** de l'ensemble du domaine « rôle et apport » (Livrables 1 à 4) passent : 3069 assertions, 0 échec.
- Génération de 440 joueurs de démonstration (16+6 clubs), calcul réel : 691 lignes `player_role_evaluations` produites en ~74 secondes, dont 298 avec une comparaison de famille voisine aboutie (`role_fit_score` non nul), le reste avec seulement la ligne « famille jouée » (fiabilité insuffisante pour la comparaison, ou gardiens — jamais comparés transversalement).
- Score, fiabilité, intervalle : valeurs numériquement cohérentes (score autour de 50, fiabilité entre 0,33 et 0,86 sur ce brouillon de poids égaux arbitraires).

## Trois découvertes, toutes corrigées

**1. Un bug de syntaxe bloquait toutes les migrations du Livrable 1.** Un commentaire de `2026_09_30_090700_create_match_team_stats_table.php` contenait littéralement `*/`, terminant le commentaire PHP trop tôt. Jamais détecté avant (aucune exécution réelle n'avait eu lieu jusqu'à cette session). Corrigé par reformulation, aucun changement de comportement.

**2. Le correctif FK `joueurs`→`players` ne fonctionnait pas tel qu'écrit.** SQLite refuse `dropForeign()` — contrairement à ce que le rapport du Livrable 3 supposait (doctrine/dbal n'intervient pas ici, quelle que soit sa présence). Réécrit avec la seule méthode qui fonctionne sur SQLite (reconstruction de la table à partir du SQL réel de `sqlite_master`, expression tolérante à la casse et au format — une deuxième découverte en cours de route : ce format change lui-même après qu'une colonne a été ajoutée/supprimée sur la même table). Testé en conditions réelles sur vos 490 lignes réelles : succès, données identiques avant/après, contrainte appliquée, `down()` fonctionne aussi. **Toujours non exécutée** sur votre base réelle.

**3. Les commandes `role-eval:import` et `role-eval:generate-demo` n'étaient jamais réellement utilisables.** Ce projet enregistre ses commandes une par une dans `app/Console/Commands/Kernel.php` (`protected $commands = [...]`) plutôt que par découverte automatique ; les deux commandes des Livrables 2 et 3 n'y avaient jamais été ajoutées. Corrigé (2 lignes), et `role-eval:compute` ajoutée à la même liste.

## Découverte critique propre à ce livrable : le moteur n'est pas totalement agnostique au nom des familles

La proposition affirmait que `PerformanceScoreCalculator` était agnostique au nom des clés de famille, permettant d'utiliser directement `position_catalog.family` (français) partout. **C'était inexact pour un cas précis** : le calculateur compare la chaîne littérale anglaise `"goalkeeper"` à 5 endroits de son code (non modifié) pour son traitement spécifique du gardien (exclusion du pool de référence des joueurs de champ, sélection du jeu de dimensions à 4 plutôt qu'à 7). Utiliser `"gardien"` tel quel cassait silencieusement cette logique — trouvé par une vraie erreur d'exécution (`Undefined array key 4`) dès qu'un gardien était évalué.

Corrigé par `PositionFamilyTranslator`, une traduction aux deux bords : tout le calcul interne travaille en anglais (comme la phase 1), seules les colonnes en base (`position_catalog.family`, `role_config_weights.position_family`, `player_role_evaluations.position_family_evaluated`) restent en français. La correspondance un-à-un entre les 8 familles reste exacte ; seule l'affirmation « agnostique au nom » était fausse.

## Limite connue et assumée : léger effet d'auto-inclusion sur les familles comparées

Documentée dans la proposition avant d'écrire le code, confirmée par l'implémentation : pour évaluer un joueur sur une famille qu'il n'a pas jouée, la seule manière de réutiliser `PerformanceScoreCalculator` sans le modifier est de lui donner une population contenant ce joueur (poste substitué par un code synthétique). Ce joueur entre donc dans le pool utilisé pour calculer la référence (médiane/MAD) de la famille comparée, en plus des vrais joueurs de cette famille — un isolement complet demanderait de modifier le calculateur, ce que je me suis engagé à ne pas faire. Sur un pool réaliste (>= 30 joueurs), l'effet reste marginal, non nul.

## Ce que je n'ai pas fait valider mot pour mot

- ~~Le calcul exact de `role_fit_score`~~ : validé le 30/09 (« écart ok ») — écart signé entre le score de la famille comparée et celui de la famille jouée.
- **Caractéristique de performance** : ~74 secondes pour 440 joueurs (comparaison de familles voisines comprise) sur cette machine de validation. La complexité croît avec le nombre de joueurs × le nombre de comparaisons ; à surveiller si ce moteur est exécuté sur une population beaucoup plus grande.
- `source_import_batch_id` / `source_demo_batch_id` restent `NULL` sur les lignes calculées : une évaluation peut agréger plusieurs lots d'import, et leur attribuer un lot unique n'est pas nécessaire pour que le bandeau « Données de démonstration » fonctionne (il ne dépend que de `is_demo`, toujours correctement renseigné). Limite documentée, pas un oubli.

## Ce que je n'ai pas pu vérifier

- Le comportement de ce moteur sur MySQL/PostgreSQL (développé et validé uniquement sur SQLite, comme le reste du mandat).
- Le comportement à l'échelle de votre volumétrie réelle (des centaines ou milliers de joueurs) — seule une échelle de démonstration (440 joueurs) a été testée.
- L'exactitude sportive des résultats : impossible à juger sans vraies données ni expertise football, et de toute façon hors de portée tant que seuls des poids arbitraires existent (règle du mandat).

## Prochaines étapes proposées

1. ~~Validation du calcul de `role_fit_score`~~ — fait, le 30/09.
2. Votre accord pour exécuter les deux migrations en attente (correctif FK joueurs/players, correctif des colonnes manquantes) — aucune des deux n'a encore touché votre base réelle.
3. Le cas échéant, la mise en place d'une vraie version de configuration (poids réels) une fois de vraies données disponibles — jamais avant, conformément au mandat.
