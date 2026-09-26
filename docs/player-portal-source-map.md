# Sources du portail joueur

Chaque carte est assemblée par `PlayerPortalDataService::forPlayer`. Une valeur affichée a une source et une signification précises ; le portail ne fabrique pas de valeur de repli à partir d'une autre mesure.

| Valeur affichée | Source | Règle |
| --- | --- | --- |
| Âge, taille du profil, poids du profil, pied | `players` | Profil du joueur ; les champs de test vides sont complétés sans écraser les valeurs existantes. |
| Poids mesuré, paramètres vitaux, IMC | Dernière `player_real_time_health` | Mesure datée ; le poids du profil ne remplace pas un poids non mesuré. |
| Préparation, énergie, qualité du sommeil | `player_real_time_health` pour préparation, énergie et sommeil | La préparation utilise `readiness_score`, jamais `players.fitness_score`. |
| Nutrition et hydratation | `player_real_time_health.metadata.nutrition` et `hydration_level` | Un champ absent affiche « Données non disponibles ». |
| Fatigue, douleurs, étirements | Dernier `player_fitness_logs` | La fatigue provient du journal ; douleurs et étirements viennent de `player_real_time_health.metadata.recovery`. Le sommeil vient de `player_real_time_health`. |
| FIT global, cinq axes, radar | Dernier `fit_score_snapshots` complet (`fit_v1`) | Tous les emplacements affichent le même snapshot ; les métriques vérifiées alimentent son calcul. |
| Évaluation technique complémentaire | Dernier `player_performances` | Les cinq axes sont lus dans le snapshot FIT ; les aptitudes complémentaires restent des évaluations distinctes. |
| Matchs, buts, passes, minutes, cartons de saison | `player_season_stats` | La carte et le graphique lisent la même ligne. |
| Tacles, interceptions, tirs cadrés, précision des passes | Dernière `performances` | Statistiques d'un match daté ; les interceptions sont dans `additional_metrics`. |
| Tendances | `performance_trends` | Variation observée ; aucune prédiction future déduite automatiquement. |
| Traitements actifs | `health_records.medications` | Seuls les éléments dont `status=active` comptent. |
| Risque sanitaire, statut médical | `health_records.risk_score`, PCMA signé | Le risque est affiché comme risque ; aucun statut « apte » sans signature. |
| Score et axes PCMA | `pcmas.result_json` | Une évaluation de test non signée reste clairement non officielle. |
| Prime simulée de formation (test) | `player_licenses.bonus_structure.training_compensation_test` | Montant en unités de test, signalé comme simulation et jamais présenté comme prime FIFA officielle. |
| Substances du panel antidopage | `doping_controls.substances_tested` | Le nombre de détections n’est affiché que si ce résultat est enregistré ; autrement, résultat individuel non enregistré. |

Les fixtures sont limitées aux enregistrements marqués `synthetic_demo`, préservent les champs existants et sont réexécutables. Le lanceur `scripts/run_player_portal_completion.sh` complète profil, nutrition, traitements et FIT pour tous les joueurs de test, puis affiche une couverture par joueur. Les identifiants FIFA ne sont pas générés.

L'identifiant FIFA affiché provient exclusivement de `players.fifa_connect_id` ; la licence active en en-tête provient de la même collection `playerLicenses` que le tableau des licences.


Les intitulés de sections, catégories, liens de navigation et explications sont des textes d’interface. Les scores FIT, pourcentages et agrégats sont des calculs d’affichage à partir de données enregistrées. Le contrôle de rendu des 844 joueurs constate un seul champ personnel absent : `players.fifa_connect_id`, qui reste réservé à l’API FIFA. Ce contrôle concerne le portail joueur et la route analytique examinée, pas toutes les routes de l’application.
