# Moteur de calcul « rôle et apport » — proposition (rien créé)

Branche `feature/role-evaluation-schema`. Ce document propose une méthode et une architecture pour le moteur de calcul (score / fiabilité / intervalle / adéquation au rôle), conformément à votre confirmation : même discipline que les Livrables 1 à 3 — je propose, je m'arrête, j'attends votre validation avant d'écrire la moindre migration ou ligne de code. Aucun fichier de code, aucune migration, aucune donnée n'est modifié par ce document.

## Découverte majeure : un moteur de calcul existe déjà

Avant de concevoir quoi que ce soit, j'ai cherché comment le cockpit actuel calcule déjà des scores, pour respecter la règle « champs alignés sur les indicateurs du cockpit actuel ». J'ai trouvé bien plus qu'un alignement de champs : **une phase 1 d'un moteur de score est déjà implémentée, testée et documentée**, indépendamment de ce mandat.

- `app/Services/PerformanceScoreCalculator.php` (498 lignes) : classe pure, sans accès base de données, qui reçoit un tableau de joueurs (position, matchs, statistiques) et un tableau de configuration `$cfg` **injecté au constructeur**, et retourne pour chaque joueur : score brut, score corrigé, intervalle à 80 %, fiabilité, niveau de fiabilité, détail par dimension.
- `app/Services/PerformanceScoreDataSource.php` : lit `performances`, `external_player_performance_metrics`, `player_season_stats`, filtrées sur `score_origin = 'observed'`, et prépare le tableau attendu par le calculateur.
- `config/player_performance_score.php` (version `1.1.0`) : la configuration injectée — postes → familles, poids par famille et par dimension, dimensions et leurs indicateurs, correspondance indicateur → colonne brute, groupes de redondance, hyperparamètres statistiques.
- `docs/PERFORMANCE_SCORE_PHASE1.md` : la documentation de méthode, avec ses tests (`PerformanceScoreCalculatorTest`, `PerformanceScoreDataSourceTest`, 10 tests / 40 assertions, verts au 29 septembre 2026).
- La migration `2026_09_29_080000_add_score_observation_provenance.php` ajoute `score_origin` (défaut `unverified`) à `performances`, `external_player_performance_metrics`, `player_season_stats`. L'adaptateur n'accepte que `score_origin = 'observed'` ; l'historique, `synthetic` et `unverified` sont exclus. C'est un mécanisme de provenance **parallèle** au `is_demo` que j'ai posé au Livrable 1, sur un pipeline de données différent (voir plus bas, « Réconciliation »).

Je n'ai touché à aucun de ces fichiers — lecture seule, pour cadrer ma proposition dessus.

### Ce que ce moteur calcule déjà (méthode)

- **Comptage par 90 minutes** pour les compteurs ; **taux stabilisé** par un nombre configurable de pseudo-tentatives (15, ou 40 pour les arrêts de gardien) mélangé au taux médian de la famille — évite qu'un joueur à 2 tirs cadrés sur 2 sorte avec un taux de 100 %.
- **Pondération récence** : demi-vie de 8 matchs. **Pondération minutes** : `min(1, minutes / 60)` par match.
- **Groupe de référence** : même famille de poste, ≥ 270 minutes cumulées, ≥ 30 joueurs ; à défaut, élargissement configuré vers des familles voisines puis vers l'ensemble des joueurs de champ (jamais pour les gardiens).
- **Z-scores robustes** : médiane et MAD × 1,4826 (avec plancher), bornés à ±3, sens inversé pour les indicateurs négatifs (fautes, cartons, erreurs, pénalités concédées, ballons perdus).
- **Anti-redondance** : volume et taux d'une même action (passes, dribbles, centres, tacles) ne comptent que pour une seule voix dans leur dimension.
- **Score** = `clamp(50 + 15 × z_global, 0, 100)`.
- **Fiabilité** : modèle de variance à deux composantes — variance intra-joueur (bruit de match) mise en commun par famille, variance inter-joueurs — donnant un ratio `R_stat`, multiplié par la couverture (fraction d'indicateurs disponibles) pour donner la fiabilité affichée. Sous 30 %, le score et l'intervalle sont masqués et remplacés par « Fiabilité insuffisante ».
- **Score corrigé** : rapproché de la moyenne de la famille proportionnellement à `R_stat` (technique de rétrécissement / shrinkage), pour ne pas sur-interpréter un petit échantillon.
- **Intervalle à 80 %** : `1,28 × 15 × τ × √(1 − R_stat)`.
- **Mode approximé** : si seules des moyennes de saison sont disponibles (pas de matchs détaillés), une variance approximative (Poisson pour les comptages, binomiale pour les taux) remplace la variance intra-joueur réelle, et le résultat porte `fiabilite_approximative: true`.

Cette méthode répond très précisément à ce que le mandat demande pour le moteur de calcul (score, fiabilité, intervalle) — à l'exception de « l'adéquation au rôle », qui n'existe pas encore (voir plus bas).

## Comparaison avec le schéma cible du Livrable 1

Le Livrable 1 (déjà validé) a posé `role_config_versions`, `role_config_weights`, `player_role_evaluations` sans savoir, à l'époque, qu'un moteur de score existait déjà ailleurs dans le code. Bonne nouvelle : l'alignement est fort.

**Familles de poste : correspondance exacte.** Les 8 familles anglaises codées en dur dans `config/player_performance_score.php` (`central`, `lateral`, `defensive_midfield`, `central_midfield`, `attacking_midfield`, `winger`, `striker`, `goalkeeper`) correspondent une à une aux 8 valeurs françaises que j'ai choisies pour `position_catalog.family` au Livrable 1 (`défenseur central`, `latéral`, `milieu défensif`, `milieu relayeur`, `milieu offensif`, `ailier`, `avant-centre`, `gardien`). Ce n'était pas concerté — c'est une coïncidence rassurante, pas une garantie que je revérifierai poste par poste avant d'écrire quoi que ce soit.

**Poids par famille et par dimension : même esprit.** Le tableau `weights` du fichier de config (ex. `'central' => [5, 25, 10, 30, 25, 5]`, un poids par dimension dans l'ordre des dimensions) est exactement ce que `role_config_weights` (une ligne par version × famille × dimension) normalise en base. La différence est l'endroit où ça vit : un fichier de config statique, une seule version active à la fois, contre une table versionnée avec statut `draft / published / archived`, plusieurs versions pouvant coexister. La table en base est plus proche de ce que le mandat demande (« versionnée ») et permet, plus tard, de comparer plusieurs configurations sans redéploiement.

**Ce qui manque au moteur actuel : l'adéquation au rôle.** `PerformanceScoreCalculator` ne note un joueur que sur la famille qu'il a **jouée** (déterminée par les minutes cumulées par famille). Il ne compare jamais un joueur à d'autres familles que la sienne. Le mandat demande « adéquation au rôle » comme une sortie distincte du score — cela suppose de savoir comment un joueur se situerait s'il était jugé sur une **autre** famille que celle jouée. C'est une capacité à ajouter, pas à réutiliser telle quelle (détail plus bas).

## Recommandation d'architecture : réutiliser le calculateur existant, ne pas en écrire un second

`PerformanceScoreCalculator` est une classe pure : son constructeur prend `$cfg` en paramètre, elle ne lit jamais la base, elle ne connaît aucune des tables de ce mandat. Rien ne l'empêche de recevoir des données issues de mes tables Livrable 1 plutôt que de `performances`/`player_season_stats`, à condition de lui fournir les deux choses qu'elle attend :

1. **Un tableau de joueurs** au format `[id, position, matches => [[date, match_id, position, minutes, stats], ...]]` — c'est le rôle d'une nouvelle classe, analogue à `PerformanceScoreDataSource` mais lisant `match_participations` + `player_match_detailed_stats` + `matches`, filtrable par `is_demo` et par lot d'import.
2. **Un tableau `$cfg`** construit dynamiquement à partir de `role_config_versions` + `role_config_weights` pour une version donnée, plutôt que lu depuis `config/player_performance_score.php`.

Je propose donc, sous réserve de votre accord :

- **Ne pas dupliquer** la logique statistique (z-scores robustes, groupes de référence, modèle de variance, shrinkage, intervalle) : elle est déjà écrite, déjà testée, déjà éprouvée sur un cas réel documenté (`docs/PERFORMANCE_SCORE_PHASE1.md`). L'écrire une deuxième fois créerait deux moteurs à faire évoluer en parallèle, avec un risque réel de divergence silencieuse.
- **Ajouter, sans toucher à l'existant** : (a) l'adaptateur de données Livrable 1 → format d'entrée du calculateur ; (b) l'adaptateur de configuration `role_config_weights` → `$cfg` ; (c) une capacité nouvelle « adéquation au rôle » qui n'existe dans aucun des deux fichiers actuels.
- Cette réutilisation ne modifie ni ne touche `PerformanceScoreCalculator.php`, `PerformanceScoreDataSource.php` ni `config/player_performance_score.php` — je les lis, je ne les édite pas. Le cockpit actuel (`PerformanceScoreCalculator` en fait déjà partie, au sens large) reste intact, conformément à « ne touche ni au cockpit ni à l'onglet Statistiques avancées ».

**Question ouverte n°1** : êtes-vous d'accord avec cette réutilisation, plutôt qu'un moteur entièrement séparé ? C'est la décision d'architecture la plus structurante de cette proposition.

## Vocabulaire des dimensions : reprendre celui de la phase 1, ou en définir un nouveau ?

La migration `role_config_weights` (Livrable 1) ne contient, en commentaire, qu'un exemple de vocabulaire (« passing, duels, defending, chance_creation, gk_shot_stopping ») — ce n'était pas un choix arrêté, juste une illustration. Le moteur existant, lui, a un vocabulaire réel, déjà utilisé par ses poids et ses tests :

- **Joueurs de champ** : `finition_creation` (buts, passes décisives, tirs cadrés, passes clés), `construction` (passes, taux de passes, passes longues), `progression_dribbles` (dribbles, centres), `duels` (tacles, duels au sol, duels aériens), `recuperation` (interceptions, récupérations), `discipline` (fautes, cartons, erreurs, pénalités concédées, ballons perdus).
- **Gardiens** : `arrets_buts_evites` (taux d'arrêts, buts évités), `gestion_surface` (sorties aériennes, sorties au sol), `jeu_au_pied` (taux de passes, passes longues), `securite` (erreurs, pénalités concédées, ballons perdus).

Je recommande de **reprendre ce vocabulaire tel quel** pour `role_config_weights.dimension_key`, plutôt que d'en inventer un nouveau : c'est celui qui est déjà relié aux indicateurs, déjà testé, déjà compris par quiconque a lu la phase 1. Les 13 colonnes ajoutées au Livrable 1 pour « aligner sur le cockpit » (xG, xG cadré, passes progressives, duels offensifs/défensifs détaillés, dribbles dans le dernier tiers, occasions créées, erreurs menant à un tir/but) ne sont utilisées par **aucune** dimension du moteur actuel — je propose de les laisser en réserve pour une itération future plutôt que de redécouper les dimensions existantes maintenant, ce qui élargirait le périmètre de cette proposition au-delà de ce que vous avez validé.

**Question ouverte n°2** : reprendre ce vocabulaire de dimensions tel quel, ou souhaitez-vous en revoir le découpage ?

## Correspondance champs bruts → indicateurs

J'ai comparé, indicateur par indicateur, ce que `config/player_performance_score.php` attend comme noms de colonnes avec les colonnes réellement présentes dans `player_match_detailed_stats` (96 colonnes d'origine + 22 ajoutées au Livrable 1).

**Correspondance directe ou par renommage simple dans l'adaptateur (aucune migration nécessaire)** : buts, passes décisives, tirs cadrés, passes clés, passes (total/réussies), dribbles (tentés/réussis), centres (total/réussis), tacles (total/gagnés), interceptions, récupérations, fautes, cartons jaunes/rouges, ballons perdus (`times_dispossessed`), duels au sol et aériens (tentés/gagnés), passes longues (total/réussies), buts évités du gardien (calculable à partir de `gk_expected_goals_faced − gk_goals_conceded`, exactement le repli que le moteur utilise déjà quand `goals_prevented` n'est pas directement disponible). Deux noms diffèrent légèrement et nécessitent seulement un renommage dans l'adaptateur, pas de nouvelle colonne : le moteur attend `mistakes_leading_to_chances`/`mistakes_leading_to_goals` (pluriel), mes colonnes Livrable 1 s'appellent `mistakes_leading_to_shot`/`mistakes_leading_to_goal` (singulier).

**Deux lacunes réelles**, sans solution de contournement propre :
- `penalties_conceded` : aucune colonne ne l'enregistre nulle part dans `player_match_detailed_stats` (ni dans les 96 colonnes d'origine, ni dans celles ajoutées au Livrable 1). L'indicateur de discipline correspondant restera systématiquement absent pour les joueurs générés ou importés via ce mandat.
- `high_claim_rate` et `sweeper_rate` (dimension gardien « gestion_surface ») : le moteur attend une distinction tentatives/réussites pour les sorties aériennes et les sorties au sol. Au Livrable 1, j'ai regroupé ces deux notions dans une seule colonne `gk_claims_exits` (« option A », un choix volontairement minimal à l'époque). Conséquence : ces deux indicateurs, et donc toute la dimension `gestion_surface` du gardien, resteront à `null` faute de couverture — le moteur tolère les indicateurs manquants (couverture réduite plutôt qu'erreur), mais une dimension entièrement vide donne un score de dimension `null` pour tous les gardiens.

Je propose d'**accepter ces deux lacunes pour cette itération**, sans nouvelle migration : les indicateurs concernés seront simplement absents du calcul (comme le moteur le permet déjà pour tout indicateur manquant), et je le noterai explicitement dans le rapport de tout résultat calculé. Une correction éventuelle (séparer `gk_claims_exits` en deux colonnes, ajouter `penalties_conceded`) suivrait la même règle que la correction `joueurs`/`players` : une migration préparée, pas exécutée sans votre accord écrit, dans un lot séparé.

**Question ouverte n°3** : acceptez-vous ces deux lacunes pour l'instant, ou préférez-vous que je prépare (sans l'exécuter) une migration corrective avant d'aller plus loin ?

## Réconciliation `score_origin` / `is_demo`

Ce sont deux mécanismes de provenance, sur deux pipelines de données différents, et je propose de **ne pas les fusionner** :

- `score_origin` (`performances`, `external_player_performance_metrics`, `player_season_stats`) distingue observé/non vérifié sur des données **agrégées par saison ou par ligne libre**, alimentées par un pipeline qui existait avant ce mandat.
- `is_demo` (mes tables Livrable 1 : `match_participations`, `player_match_detailed_stats`, `match_team_stats`, `match_events`, et maintenant `player_role_evaluations`) distingue démonstration/réel sur des données **match par match**, alimentées par l'importeur (Livrable 2) ou le générateur (Livrable 3).

Fusionner ces deux notions maintenant élargirait le périmètre à un système que le mandat ne m'a pas demandé de modifier. Je propose que le nouveau moteur, quand il lit mes tables Livrable 1, **n'utilise que `is_demo`** (jamais `score_origin`, qui n'existe pas sur ces tables) pour renseigner `player_role_evaluations.is_demo` — chaque ligne calculée hérite du `is_demo` des lignes de statistiques qui l'ont alimentée. Les deux mécanismes resteront documentés côte à côte dans le rapport, pour qu'un futur travail sache qu'ils coexistent délibérément.

**Point d'attention non demandé, mais que je dois signaler** : `app/Services/GoalkeeperCockpitData.php` (le cockpit gardien existant) lit déjà `player_match_detailed_stats` directement, **sans filtrer sur `is_demo`**. Cela signifie que les lignes générées par le Livrable 3 (`role-eval:generate-demo`) sont, dès aujourd'hui, potentiellement visibles dans l'onglet gardien du cockpit — avant même ce nouveau moteur de calcul, et indépendamment de lui. Le mandat m'interdit de toucher au cockpit dans ces livrables ; je ne propose donc aucune modification de ce fichier ici, mais je vous signale ce comportement pour que vous puissiez décider, séparément, si le bandeau « Données de démonstration » doit être ajouté à ce cockpit — une décision hors du périmètre « moteur de calcul » que vous avez validé.

## Distinction à valider : statistiques de référence recalculées ≠ réglage de poids interdit

Le mandat interdit d'utiliser les données de démonstration pour « régler des poids, des seuils ou des paramètres de modèle ». Le moteur existant recalcule, à **chaque exécution**, les médianes et MAD de référence par famille à partir de la population de joueurs disponible — démo ou réelle, peu importe la source, c'est un ingrédient normal du calcul d'un z-score (comme la moyenne d'une classe recalculée à chaque contrôle, pas un paramètre qu'on ajuste une fois pour toutes). Je distingue donc :

- **Ce qui est recalculé à chaque exécution, jamais figé** : médiane et MAD de référence par famille et indicateur — dérivées mécaniquement des données passées au moteur, démo ou réelles.
- **Ce qui reste un paramètre de configuration, soumis à la règle du mandat** : les poids (`role_config_weights.weight`), et les hyperparamètres du calculateur (demi-vie, pseudo-tentatives, z_cap, seuil de fiabilité affichée, etc.) — ceux-là ne doivent être choisis ou ajustés qu'avec de vraies données, jamais à partir d'un résultat observé sur le jeu de démonstration.

**Question ouverte n°4** : confirmez-vous cette lecture ? C'est la distinction qui me permettra de valider le code (voir plus bas, poids « brouillon ») sans enfreindre la règle.

## Adéquation au rôle (`role_fit_score`) : la vraie capacité à construire

C'est la seule partie du mandat pour laquelle aucun code existant ne sert de base. Le moteur actuel note un joueur uniquement sur la famille qu'il a jouée. « Adéquation au rôle » suppose de répondre à une question différente : *si ce joueur était jugé sur les exigences d'une autre famille, quel score obtiendrait-il ?*

Principe proposé : pour un joueur et une période donnés, exécuter le calcul de dimension/score (même méthode statistique : z-scores contre la référence de la famille candidate, mêmes poids de cette famille) en gardant les statistiques réellement observées du joueur, mais en substituant la référence et les poids d'une **autre** famille que celle jouée. Cela demande une variante interne du calculateur (une méthode qui accepte une famille cible différente de la famille jouée) — je ne peux pas dire à ce stade si cela s'obtient proprement en étendant `PerformanceScoreCalculator` par composition (une classe qui l'enveloppe et l'appelle plusieurs fois avec des familles différentes) ou s'il faut une modification interne plus fine ; je trancherai ce détail d'implémentation après votre validation du principe, pas avant.

Trois questions de conception restent ouvertes, et je préfère vous les poser plutôt que de choisir seul :

**Question ouverte n°5 — Portée de la comparaison** : comparer chaque joueur à *toutes* les familles (8 comparaisons), ou seulement aux familles « voisines » au sens de `reference_fallback` du moteur existant (ex. un latéral n'est comparé qu'à défenseur central et joueur de champ générique, jamais à gardien) ? Je recommande les familles voisines uniquement — comparer un gardien à « avant-centre » n'a pas de sens sportif et gonflerait le calcul sans valeur ajoutée — mais c'est votre appréciation métier qui doit trancher.

**Question ouverte n°6 — Format du résultat** : `player_role_evaluations` a une clé (`player_id`, `match_id`/période, `position_family_evaluated`, `role_config_version_id`). Deux lectures possibles :
  - (a) une ligne par famille comparée (ex. 3 lignes pour un latéral comparé à lui-même, défenseur central, et joueur de champ générique), chacune avec son propre `score`/`reliability`/`interval`, et `role_fit_score` renseigné sur toutes ;
  - (b) une seule ligne par joueur/période, sur la famille jouée uniquement, avec `role_fit_score` résumant l'écart vers la meilleure famille alternative (sans détailler chaque comparaison).

Je recommande (a) : le schéma du Livrable 1 s'y prête déjà exactement (clé composite sur `position_family_evaluated`), et cela permet à l'interface, plus tard, d'afficher un classement des familles plutôt qu'un chiffre isolé. Mais c'est plus de lignes calculées, donc à valider.

**Question ouverte n°7 — `model_version`** : je propose un identifiant du type `role-fit-v1+phase1-calculator@1.1.0` (composant maison + version du calculateur réutilisé), pour que toute ligne de résultat reste traçable jusqu'à la méthode exacte qui l'a produite, y compris si le fichier de config du moteur phase 1 évolue de son côté. À valider ou à reformuler selon votre convention.

## Ce que cette proposition NE fait PAS

Comme pour le Livrable 1 : rien n'est créé. Aucune migration, aucun modèle Eloquent, aucune commande, aucune ligne de `role_config_weights` insérée. `PerformanceScoreCalculator.php`, `PerformanceScoreDataSource.php`, `config/player_performance_score.php`, et tout fichier de cockpit restent non modifiés.

## Étapes suivantes (après votre validation)

1. Modèles Eloquent minces pour `position_catalog`, `role_config_versions`, `role_config_weights`, `player_role_evaluations` (aucun n'existe encore).
2. Adaptateur de données : `match_participations` + `player_match_detailed_stats` + `matches` → format d'entrée de `PerformanceScoreCalculator`, filtrable par `is_demo` et par lot.
3. Adaptateur de configuration : `role_config_weights` (pour une version donnée) → tableau `$cfg` injectable, avec le vocabulaire de dimensions validé (question n°2) et les familles `position_catalog.family`.
4. Extension « adéquation au rôle » selon la portée et le format validés (questions n°5 et 6).
5. Commande artisan qui matérialise des lignes `player_role_evaluations` pour un lot de matchs/joueurs et une version de configuration donnés — nom à définir, ex. `role-eval:compute`.
6. Une version de configuration `role_config_versions` avec statut `draft` et un intitulé explicite du type « Configuration de test (poids arbitraires, non calibrés) », pour valider que le code fonctionne — jamais présentée comme calibrée, jamais utilisée pour publier un résultat réel.
7. Tests unitaires : déterminisme du calcul, `is_demo` toujours vrai sur les résultats dérivés de données de démonstration, aucune donnée du jeu CS Sfaxien existant modifiée, couverture des deux lacunes de champs signalées plus haut.
8. Rapport de livraison : code, tests, hypothèses, ce que je n'aurai pas pu vérifier.

## Questions ouvertes — résumé

1. Réutiliser `PerformanceScoreCalculator` tel quel plutôt qu'écrire un second moteur ?
2. Reprendre le vocabulaire de dimensions de la phase 1, ou en revoir le découpage ?
3. Accepter les deux lacunes de champs (pénalités concédées ; sorties/arrêts gardien fusionnés) sans nouvelle migration pour l'instant ?
4. Confirmer que le recalcul des statistiques de référence (médiane/MAD) sur des données disponibles, y compris démo, n'est pas un « réglage de poids » interdit par le mandat — seuls les poids et les hyperparamètres du calculateur le sont ?
5. Comparer chaque joueur à toutes les familles, ou seulement aux familles voisines (`reference_fallback`) ?
6. Une ligne `player_role_evaluations` par famille comparée, ou une seule ligne par joueur/période avec un différentiel résumé ?
7. Convention de nommage pour `model_version` ?

Je m'arrête ici et j'attends votre validation avant d'écrire la moindre migration, modèle ou ligne de code.
