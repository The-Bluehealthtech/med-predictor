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

## Résultat de l'exécution du complément (844 joueurs)

Après le complément de données, le contrôle des champs suivis dans les 14 sources n'indique plus de valeur manquante à l'exception de `players.fifa_connect_id` : 844/844 absents. Les sept mises à jour groupées ont chacune complété 844 lignes. Ce constat ne prouve pas que toutes les relations (`passport`, club, association) ou toutes les routes ont été rendues sans champ vide ; le contrôle reste limité aux champs explicitement répertoriés par `scripts/audit_player_portal_fields.php`.

Le Blade lit aussi `player.passport.passport_number` dans la carte identité. Cette relation n'était pas incluse dans la première série des 14 sources ; `player_passports` a été ajouté au contrôle suivant. Le résultat 14/14 ne prouve donc pas que le document d'identité est disponible.

## Passeports de test

Le second contrôle trouve les trois champs de `player_passports` absents pour 844/844 joueurs. `PlayerPassportTestFixtureSeeder` ajoute un passeport temporaire `SYNTH-PASSPORT-{player_id}` aux dossiers qui n'en ont pas, avec statut `pending_validation`, émetteur fictif et métadonnées `official=false`. La vue le nomme explicitement « Passeport de test (non validé) ». Il ne crée aucun identifiant FIFA. La fixture préserve les documents existants et ne complète que les champs absents.

## Extension aux affichages non couverts

L'inventaire statique du Blade a relevé d'autres objets visibles : blessures, alertes de performance, prédictions médicales, contrôles antidopage, les unités des analyses biologiques et les aptitudes détaillées. Le script d'audit inclut désormais les champs correspondants ; en particulier, `medical_predictions.recommendations` peut rendre le texte d'une notification vide. Les résultats du précédent contrôle ne portaient pas sur ces champs supplémentaires.

La fixture de complétion renseigne désormais uniquement les recommandations vides des prédictions médicales de test avec un texte explicitement fictif ; le contenu existant est conservé. Une réexécution de `scripts/run_player_portal_field_completion.sh` permet de mesurer en une fois l'ensemble des nouveaux champs contrôlés.

## Résultat du contrôle élargi et carte des appareils

Après insertion de 844 recommandations médicales fictives, le contrôle étendu aux blessures, alertes, antidopage, unités biologiques et aptitudes détaillées ne signale plus que `players.fifa_connect_id` (844/844). Un examen manuel supplémentaire de la carte des intégrations a découvert `player_connected_devices.sensors_available`, qui détermine l'affichage de « Données: » et n'était pas dans le contrôle : ce champ a été ajouté à la fixture et à l'audit. Les appareils au numéro de série `SYNTH-WEAR-` sont maintenant étiquetés « Démonstration (non connecté) » ; leur endpoint `example.invalid` est indiqué comme inactif, même si la ligne en base porte `connection_status=connected` pour les tests.

La carte « Alertes Blessures » lisait `players.injury_risk_score`, hors du jeu de données contrôlé, bien qu'une prédiction `medical_predictions.prediction_type=injury_risk` soit enregistrée. Elle lit désormais le risque et son libellé dans cette prédiction ; le champ de zone est explicitement renommé « Dernière zone blessée » et provient de l'historique `injuries.body_zone`. Le risque général de `health_records` n'est pas traité comme un risque de blessure.

Le tableau des licences exécutait encore des requêtes `clubs` et `associations` dans le Blade. Le service joint désormais ces noms à la collection canonique `playerLicenses`; l'audit contrôle également `player_licenses.club_id` pour détecter une licence sans club. Les indicateurs de source unique restent conditionnels aux relations effectivement présentes dans la base.

## Contrôle réexécuté après capteurs de démonstration

Résultat transmis par l'utilisateur : 844 capteurs de démonstration complétés ; les autres catégories de la fixture étaient déjà à jour (0 ligne modifiée). L'audit étendu sur 844 joueurs ne signale plus qu'un champ absent, `players.fifa_connect_id` pour 844/844. Les colonnes des sources répertoriées dans `scripts/audit_player_portal_fields.php` sont donc couvertes, sous réserve des champs hors inventaire et des branches d'affichage conditionnelles. Ce résultat n'est pas une preuve de rendu sans blanc sur toutes les routes.

## Vérification du rendu de la vue

`scripts/run_player_portal_render_audit.sh` effectue une lecture seule : il construit réellement le Blade `test-portail-joueur-simple` pour chacun des 844 joueurs en utilisant `PlayerPortalDataService`, puis compte les textes visibles « Données non disponibles », « N/A » et « Non renseigné ». Une erreur de rendu interrompt le contrôle. Le script affiche sa progression tous les 50 joueurs ; `bash scripts/run_player_portal_render_audit.sh 1` permet de vérifier un seul joueur si nécessaire. Il vérifie aussi le rendu de `/performances/analytics` sous l'identité de test utilisée par le script. Il ne couvre pas les contrôles d'accès HTTP ni les autres routes du projet.
