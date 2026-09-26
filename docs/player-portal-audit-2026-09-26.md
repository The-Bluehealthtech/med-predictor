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

## Vérifications encore nécessaires

1. Inventorier chaque champ visible de chaque Blade et chaque route visée, y compris les sections conditionnelles et les intégrations.
2. Associer à chaque champ une colonne ou un calcul unique, et décider du sort des champs sans source réelle (par exemple une API de centre inexistant).
3. Sur la base externe, vérifier les valeurs de **chaque champ effectivement affiché** pour les 844 joueurs, pas seulement l'existence d'un dossier par catégorie ; publier les nombres de manquants par champ.
4. Faire rendre les routes concernées et contrôler les valeurs vides réelles. Les 104 branches de remplacement statiques ne permettent pas à elles seules de savoir combien sont activées.

**Verdict actuel :** couverture des cinq catégories contrôlées confirmée ; aucune preuve de complétude à 100 % des champs ni d'unicité des sources. Les valeurs `null` et les sources de remplacement ci-dessus réfutent déjà ces deux affirmations pour le code actuel.
