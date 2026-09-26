# Sources du portail joueur

Chaque carte est assemblée par `PlayerPortalDataService::forPlayer`. Une valeur affichée a une source et une signification précises ; le portail ne fabrique pas de valeur de repli à partir d'une autre mesure.

| Valeur affichée | Source | Règle |
| --- | --- | --- |
| Âge, taille du profil, poids du profil, pied | `players` | Profil du joueur ; les champs de test vides sont complétés sans écraser les valeurs existantes. |
| Poids mesuré, paramètres vitaux, IMC | Dernière `player_real_time_health` | Mesure datée ; le poids du profil ne remplace pas un poids non mesuré. |
| Préparation, énergie, qualité du sommeil | `player_real_time_health` ; énergie du journal si absente | La préparation utilise `readiness_score`, jamais `players.fitness_score`. |
| Nutrition et hydratation | `player_real_time_health.metadata.nutrition` et `hydration_level` | Un champ absent affiche « Données non disponibles ». |
| Fatigue, douleurs, étirements | Dernier `player_fitness_logs` | Le sommeil provient du dernier suivi disponible. |
| FIT global, cinq axes, radar | Dernier `fit_score_snapshots` complet (`fit_v1`) | Tous les emplacements affichent le même snapshot ; les métriques vérifiées alimentent son calcul. |
| Évaluation technique complémentaire | Dernier `player_performances` | Les cinq axes sont lus dans le snapshot FIT ; les aptitudes complémentaires restent des évaluations distinctes. |
| Matchs, buts, passes, minutes, cartons de saison | `player_season_stats` | La carte et le graphique lisent la même ligne. |
| Tacles, interceptions, tirs cadrés, précision des passes | Dernière `performances` | Statistiques d'un match daté ; les interceptions sont dans `additional_metrics`. |
| Tendances | `performance_trends` | Variation observée ; aucune prédiction future déduite automatiquement. |
| Traitements actifs | `health_records.medications` | Seuls les éléments dont `status=active` comptent. |
| Risque sanitaire, statut médical | `health_records.risk_score`, PCMA signé | Le risque est affiché comme risque ; aucun statut « apte » sans signature. |
| Score et axes PCMA | `pcmas.result_json` | Une évaluation de test non signée reste clairement non officielle. |

Les fixtures sont limitées aux enregistrements marqués `synthetic_demo`, préservent les champs existants et sont réexécutables. Le lanceur `scripts/run_player_portal_completion.sh` complète profil, nutrition, traitements et FIT pour tous les joueurs de test, puis affiche une couverture par joueur. Les identifiants FIFA ne sont pas générés.
