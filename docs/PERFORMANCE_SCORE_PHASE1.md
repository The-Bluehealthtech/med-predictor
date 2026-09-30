# Score de performance — phase 1

Version de configuration : `config/player_performance_score.php` (`1.1.0`). Le service `PerformanceScoreCalculator` est pur : il ne lit pas la base, ne journalise pas de joueur et retourne une entrée JSON sérialisable par identifiant. `PerformanceScoreDataSource` lit les tables locales ; aucune route ou carte du cockpit n'est modifiée dans cette phase.

## Sources et identité

Le portail est fondé sur `players`. Les observations de match proviennent de `performances.player_id`, une clé étrangère directe vers `players.id` ; **aucun FIFA ID n'est nécessaire**. Le champ `position_played` peut préciser le poste joué. Les lignes de `player_match_detailed_stats` référencent l'autre table `joueurs` et sont exclues. Les métriques `external_player_performance_metrics` sont des moyennes par match via `KsaPlayerCockpitData`, puis, si absentes, les cumuls `player_season_stats` sont convertis en moyennes seulement si le nombre de matchs est positif. Chaque résultat indique `mode`.

La migration `2026_09_29_080000_add_score_observation_provenance.php` ajoute `score_origin` à ces trois sources, avec la valeur par défaut `unverified`. L'adaptateur accepte exclusivement `observed`. Les lignes historiques, `synthetic` et `unverified` restent exclues. Un importeur ne doit marquer `observed` qu'après contrôle de sa provenance et de sa liaison à `players.id`. Le champ ne peut pas être déduit du FIFA ID ni d'un numéro identique dans une autre table.

Les valeurs distinctes observées sont `players.position` : `DEF`, `FWD`, `GK`, `MID` ; CSV KSA : `CAM`, `CDM`, `CF`, `LAM`, `LB`, `LCB`, `LCM`, `RAM`, `RB`, `RCAM`, `RCB` ; lignes de match : `AM`, `CB`, `DM`, `GK`, `RB`, `ST`. `GK` est confirmé. Les postes génériques `DEF`, `FWD`, `MID` ne permettent pas de choisir une famille sans supposition : résultat sans score, raison explicite. La famille principale des profils précis est celle où le joueur cumule le plus de minutes ; les poids du match prennent sa famille *jouée*.

## Correspondance

La configuration décrit chaque indicateur : `field` pour le comptage du match, `average` pour la moyenne, ou `attempt`/`success` et `average_attempt`/`average_rate` (ou `average_success`) pour le taux. L'adaptateur mappe `performances.passes_attempted` vers `passes_total`, `performances.assists` vers `assists_provided` et `performances.tackles_attempted` vers `tackles_total`. Les clés complémentaires autorisées viennent de `additional_metrics`. Par exemple : taux de passes `passes_completed / passes_total` ↔ `passes_accuracy`, arrêts `saves / shots_on_target_against`, buts évités `goals_prevented` sinon `post_shot_xg − goals_conceded`. Une tentative nulle sans réussite ne forme pas un taux exploitable. Un succès supérieur aux tentatives est rejeté. Absence, chaîne « Données non disponibles » et taux invalide restent manquants. `performances` contient des colonnes à zéro par défaut : un zéro historique ne prouve pas que la mesure a été saisie ; seules les lignes dont l'importateur a vérifié la présence et la provenance sont admissibles.

## Calcul

Comptage par 90 ; taux stabilisé avec le taux médian familial et le nombre configurable de pseudo-tentatives (15, ou 40 pour les arrêts). Récence : `0,5^(âge / 8)` ; minutes : `min(1, minutes / 60)`. Référence : même famille, au moins 270 minutes et 30 joueurs, élargissement configuré aux voisins puis aux joueurs de champ. Le gardien ne rejoint jamais un groupe de champ ; si 30 gardiens ne sont pas disponibles, la raison est `référence de poste insuffisante`. Les z robustes utilisent médiane, MAD × 1,4826 et plancher d'écart, sont bornés à ±3, et le sens des fautes et erreurs est inversé. Les volumes et taux d'une même action (passes, dribbles, centres, tacles) partagent une seule voix dans une dimension. Le poids des dimensions présentes est redistribué, la couverture tient compte de la fraction d'indicateurs disponibles.

Un score de match vaut `clamp(50 + 15 × z_global, 0, 100)` ; le score brut du joueur est leur moyenne pondérée. `n_eff = (Σw)²/Σw²`. La variance intrajoueur est mise en commun dans la famille ; `τ²` est la variance des moyennes familiales moins le bruit `σ²/n_eff`, avec plancher positif. `R_stat = τ²/(τ² + σ²/n_eff)` ; fiabilité = `R_stat × couverture`. Le mode approximé utilise un bruit delta fondé sur Poisson pour les comptages et binomial pour les taux, et porte `fiabilite_approximative: true`. Une fiabilité sous 30 % masque score brut, score corrigé et intervalle. Le score corrigé se rapproche de la moyenne familiale. L'intervalle à 80 % est calculé avec `1,28 × 15 × τ × sqrt(1−R_stat)` puis borné à 0–100. Les dimensions exposent score, fiabilité, contributions et leur couverture implicite dans la fiabilité. Le diagnostic `splitHalfCorrelation` compare les matchs pairs et impairs sans publier de données nominatives.

## Limites vérifiables

- La table locale `performances` ne contient aucune ligne. Les 187 lignes de saison sont issues du jeu de démonstration et la table KSA est vide localement. Aucun score local n'est publiable ; l'adaptateur renvoie « aucune donnée observée ».
- Il manque localement les séries match par match reliées aux joueurs du portail, les mesures gardien (arrêts, PSxG, sorties), les postes détaillés de nombreux profils et un indicateur de présence fiable pour les colonnes à zéro par défaut. La couverture et la fiabilité en production ne sont pas vérifiées.
- Les variances approximées omettent les covariances faute d'historique ; elles sont signalées comme approximatives. Les corrélations documentées évitent le double compte direct, sans garantir l'indépendance d'autres indicateurs.
- Aucun endpoint, calcul nocturne, prédiction ni changement visuel du cockpit ne fait partie de la phase 1.

## Vérification locale du 29 septembre 2026

`phpunit --no-coverage tests/Unit/PerformanceScoreCalculatorTest.php` : 8 tests, 33 assertions, réussis. Les scénarios couvrent 30 contre 5 matchs, correction d'un score extrême, 1/1 stabilisé, indicateur manquant, minutes nulles ou zéro, poste inconnu, gardien isolé, déterminisme, mode approximé et diagnostic pair/impair. Sur cette cohorte synthétique, corrélation pair/impair `0,999996` et `R_stat` du joueur 10 `0,999533` ; les valeurs très hautes reflètent la régularité artificielle de l'échantillon et ne valident pas la calibration réelle.

Après correction : `phpunit --no-coverage tests/Unit/PerformanceScoreCalculatorTest.php tests/Feature/PerformanceScoreDataSourceTest.php` : 10 tests, 40 assertions, réussis. Les deux tests d'adaptation vérifient un joueur sans FIFA ID, l'exclusion des lignes `synthetic` et `unverified`, ainsi que la réponse sans score. La migration a été exécutée uniquement sur la base SQLite locale.

La première lecture locale avait utilisé les agrégats de démonstration et n'est plus une validation admissible. Après filtrage `score_origin = observed`, les 825 joueurs locaux ne possèdent aucune donnée observée. Aucun score n'a été publié ni stocké. Une validation numérique réelle nécessite un import de matchs ou d'agrégats observés, liés par `players.id`.
