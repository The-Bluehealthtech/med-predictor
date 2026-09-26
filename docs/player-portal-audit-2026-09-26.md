# Audit du portail joueur — 26 septembre 2026

## Portée vérifiée

- Le contrôle exécuté après les fixtures a rapporté 844/844 joueurs pour profil, nutrition, traitement actif, match fictif et FIT complet. Il ne vérifie pas tous les champs des vues ni toutes les routes.
- La route `/test-portail-joueur-simple` passe par `PlayerPortalSimpleController::show`, `PlayerPortalDataService::forPlayer` et `resources/views/test-portail-joueur-simple.blade.php`.
- Le Blade contient 104 occurrences de « Données non disponibles », dont plusieurs sont des branches conditionnelles. Ce nombre ne représente pas 104 valeurs manquantes dans la base.

## Écarts certains dans le code

| Affichage | Source actuelle | Problème |
| --- | --- | --- |
| API des centres de physiothérapie | `PlayerPortalDataService.php`, `physioCenters.api_endpoint` | Valeur explicitement `null`, puis affichée par le Blade : `API:` vide. |
| API et durée des applications de santé mentale | `PlayerPortalDataService.php`, `mentalHealthApps` | Valeurs explicitement `null`, puis affichées par le Blade (`API:` et `min` vides). L'objet présenté comme application est construit depuis `player_sdoh_data`, sans enregistrement d'application. |
| Code WADA, licence du médecin | `PlayerPortalDataService.php` | Valeurs explicitement `null` dans les objets d'affichage ; vérifier les branches correspondantes avant de conclure à leur visibilité pour chaque joueur. |
| Énergie | `player_real_time_health.energy_level`, puis `player_fitness_logs.energy_level` | Deux sources possibles pour le même champ ; sélectionner une source canonique. |
| Sommeil et récupération | `player_real_time_health`, puis `player_fitness_logs` | Sources de remplacement pour plusieurs champs ; documenter ou supprimer ces substitutions selon la règle d'une seule source. |
| Statut médical | PCMA signé uniquement | `null` si aucun PCMA signé : la couverture des dossiers PCMA ne suffit pas à prouver la présence d'un statut médical. Ne pas signer artificiellement une évaluation clinique de test. |
| Identifiant FIFA | `players.fifa_connect_id`, puis `passport.fifa_connect_id` | Deux sources possibles ; la fixture ne fabrique pas d'identifiants FIFA. |

## Écarts supplémentaires relevés

| Affichage ou route | Source actuelle | Problème |
| --- | --- | --- |
| Licence club en haut du portail | Requête `DB::table('player_licenses')` directement dans le Blade | Sélection indépendante de `playerLicenses` fourni par le service ; `N/A` si aucune licence active ou si `license_number` est vide. |
| Club et association dans le tableau des licences | Requêtes `clubs` et `associations` directement dans le Blade | La vue effectue elle-même une résolution de source, hors du service de données du portail. |
| Statut de conformité PCMA | `pcmas.status`, sans condition sur `is_signed` | Peut afficher « APTE » depuis une évaluation non signée, alors que l'aptitude médicale du même portail exige une signature. Incohérence fonctionnelle vérifiée dans le code. |
| Approbation WADA d'une AUT | `health_records.aut_status`, identique à l'approbation FIFA | Une seule colonne est affichée sous deux autorités distinctes, sans preuve d'une approbation WADA ; le Blade affiche en plus « WADA ✅ » pour toute valeur non vide, y compris un éventuel statut en attente. |
| `/performances/analytics` | Moyennes des lignes `player_performances` | Route et calcul indépendants du snapshot FIT du portail. Les cinq libellés d'axes sont les mêmes mais désignent des évaluations de performance distinctes. La couverture FIT ne garantit pas que les valeurs de cette route sont complètes. |

## Vérifications encore nécessaires

1. Inventorier chaque champ visible de chaque Blade et chaque route visée, y compris les sections conditionnelles et les intégrations.
2. Associer à chaque champ une colonne ou un calcul unique, et décider du sort des champs sans source réelle (par exemple une API de centre inexistant).
3. Sur la base externe, vérifier les valeurs de **chaque champ effectivement affiché** pour les 844 joueurs, pas seulement l'existence d'un dossier par catégorie ; publier les nombres de manquants par champ.
4. Faire rendre les routes concernées et contrôler les valeurs vides réelles. Les 104 branches de remplacement statiques ne permettent pas à elles seules de savoir combien sont activées.

**Verdict actuel :** couverture des cinq catégories contrôlées confirmée ; aucune preuve de complétude à 100 % des champs ni d'unicité des sources. Les valeurs `null` et les sources de remplacement ci-dessus réfutent déjà ces deux affirmations pour le code actuel.

## Audit de champs automatisé

Exécuter `bash scripts/run_player_portal_field_audit.sh` avec l'URL PostgreSQL saisie sans écho. Le script ne modifie aucune donnée ; il affiche, pour les 844 joueurs de test, le nombre de valeurs absentes par colonne ou chemin JSON sur 14 sources consultées par le portail et des exemples d'identifiants de joueurs. Son code est dans `scripts/audit_player_portal_fields.php`.

Cet audit de données ne prétend pas couvrir toutes les routes et toutes les branches conditionnelles. Un résultat sans absence sur ces champs ne lève pas les écarts de sources et les champs explicitement `null` documentés ci-dessus.

## Suite au contrôle des 844 joueurs

L'audit du 26 septembre a relevé 15 écarts : quatre colonnes demandées à `player_fitness_logs` absentes du schéma, dix champs de test complétables et 844 identifiants FIFA absents. La correction du service lit les valeurs de récupération dans les colonnes existantes et dans `player_real_time_health.metadata.recovery`. La fixture `PlayerPortalFieldCompletionSeeder` prépare uniquement les champs synthétiques complétables. Elle conserve les données existantes et n'attribue aucun identifiant FIFA ni signature médicale. Les URL `example.invalid` sont des marqueurs de test et ne désignent pas une API fonctionnelle.

Un nouveau contrôle sur la base externe reste nécessaire pour mesurer le résultat après exécution.

## Corrections des fausses attestations

Le portail ne présente plus le questionnaire SDOH comme une application connectée ; la carte reste présente et indique qu'aucune application n'est connectée. Une AUT n'est plus déclarée approuvée par FIFA ou WADA à partir de son seul `aut_status`. Le statut PCMA non signé est affiché comme évaluation non officielle dans la synthèse de conformité. Les lignes `API:` et `Code WADA:` sans source sont cachées ; la présentation des cartes est conservée.

La licence active affichée en en-tête provient désormais de `playerLicenses` préparé par le service. L'identifiant FIT CONNECT affiché lit uniquement `players.fifa_connect_id` ; son absence sur 844 dossiers de test reste signalée, aucun identifiant officiel n'étant généré.
