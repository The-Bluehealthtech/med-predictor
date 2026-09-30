# Livrable 2 — Importeur

**Statut : code et tests écrits, non exécutés. Rien touché en base réelle.**
Branche `feature/role-evaluation-schema`, proposition de commit local à la fin de ce document.

---

## Ce qui a été fait

### 1. Une commande d'import générique, pilotée par mapping

`php artisan role-eval:import {type} {file} --mapping=... [--source=...] [--dry-run]`

Un seul type de commande couvre les 4 catégories de données du mandat (`participations`, `player-match-stats`, `team-stats`, `events`), pilotée par un **fichier de correspondance JSON** déclaratif — plutôt qu'une classe PHP par fournisseur, ce qui évite de coder un nouvel import à chaque nouvelle source de données : on écrit un nouveau fichier de mapping.

Fichiers créés dans `app/Services/RoleEvaluationImport/` :

| Fichier | Rôle |
|---|---|
| `ImportMapping.php` | Charge et valide le fichier de correspondance JSON |
| `RowValidator.php` | Règles du mandat : minutes [0,130], taux [0,1], comptages, booléens, codes de poste |
| `EntityResolver.php` | Résout joueur/équipe/match (par identifiant interne ou par identifiant FIFA/nom+date) |
| `RoleEvaluationImporter.php` | Orchestre la lecture CSV, la validation, la résolution, l'écriture |
| `ImportReport.php` | Compteurs + raisons de rejet, persistés sur `import_batches` |
| `DryRunRollback.php` | Détail technique interne pour annuler proprement un essai `--dry-run` |

Et `app/Console/Commands/ImportRoleEvaluationDataCommand.php` pour la commande elle-même.

### 2. Toutes les règles du mandat, implémentées

| Exigence du mandat | Implémentation |
|---|---|
| Fichier de correspondance entre champs fournisseur et colonnes | Fichier JSON déclaratif (`docs/role-evaluation/livrable-2-importer/example-mapping-*.json`) |
| Minutes entre 0 et 130 | `RowValidator::validateMinutes()` |
| Taux entre 0 et 1 | `RowValidator::validateRate()` (disponible, non utilisé dans l'exemple — voir "Hypothèses") |
| Réussites ≤ tentatives | `attempt_success_pairs` déclarées dans le mapping, vérifiées par `checkAttemptSuccessPair()` |
| Joueur et match connus | `EntityResolver` — ligne rejetée avec raison explicite si non trouvé |
| "Données non disponibles" → NULL, jamais 0 | `ImportMapping::isNotAvailable()`, appliqué avant toute conversion de type |
| Lot d'importation identifié | Chaque exécution crée une ligne `import_batches` (table du Livrable 1) |
| Importation rejouable sans doublon | Upsert sur clé métier (`match_id`+`player_id` ou `match_id`+`team_id`, déjà contraintes uniques en base) ; pour les événements (sans clé métier naturelle), une clé technique `source_row_hash` dédiée — voir limite ci-dessous |
| Rapport de qualité (lu/importé/rejeté + raison) | Colonnes `rows_read`/`rows_imported`/`rows_rejected`/`report` (JSON) ajoutées à `import_batches`, affichées aussi dans la console |
| Tests unitaires | `tests/Unit/RoleEvaluationImport/` (logique pure) + `tests/Feature/RoleEvaluationImportCommandTest.php` (bout en bout) |
| Fichier d'exemple de format | `docs/role-evaluation/livrable-2-importer/example-*.json` et `.csv` (2 jeux : participations, stats joueur) |

### 3. Une migration supplémentaire (13e, après les 12 du Livrable 1)

`2026_09_30_100000_add_import_traceability_for_role_evaluation.php` — ajoute `source_row_hash` aux 4 tables cibles (traçabilité/déduplication) et `rows_read`/`rows_imported`/`rows_rejected`/`report` à `import_batches`. Toujours additif, aucune donnée existante touchée.

---

## Découverte importante en cours de route

En préparant l'exemple pour `player-match-stats`, j'ai trouvé une contrainte déjà présente en base que le Livrable 1 n'avait pas eu à traiter : la colonne existante `player_match_detailed_stats.position_played` a une contrainte `CHECK` limitée à un **ancien référentiel à 10 codes** (`GK, CB, LB, RB, DM, CM, AM, LW, RW, ST`), différent du **nouveau référentiel à 12 codes** (`LCB, RCB, LCM`, etc.) créé au Livrable 1 pour `match_participations.detailed_position`. Cette colonne est **obligatoire** (NOT NULL sans valeur par défaut) : un import qui crée une toute nouvelle ligne dans `player_match_detailed_stats` doit donc fournir une valeur compatible avec l'ancien référentiel, jamais avec le nouveau.

J'ai traité ça en ajoutant un type de colonne dédié dans le validateur (`legacy_position_code`, vocabulaire figé aux 10 anciens codes), utilisé uniquement pour cette colonne historique, bien distinct du `position_code` (12 codes) utilisé pour `match_participations`. C'est documenté dans le code et dans l'exemple de mapping `player-match-stats`. Aucune modification de la contrainte existante : je m'y suis simplement conformé.

---

## Validation faite (sans PHP)

Même limite qu'au Livrable 1 : aucun `php` disponible sur la machine connectée, donc rien de tout ceci n'a pu être exécuté avec le vrai framework Laravel/PHPUnit. Pour compenser, deux niveaux de vérification indépendants du code PHP lui-même :

1. **Cohérence syntaxique** : comptage des accolades sur chaque fichier PHP (aucun déséquilibre).
2. **Simulation complète en Python** de la logique de l'importeur (résolution, validation, upsert), exécutée sur une **copie jetable** de `database.sqlite` (supprimée après coup), avec des données de test correspondant exactement au fichier d'exemple livré :
   - 1er import : 4 lignes lues, 2 importées, 2 rejetées — avec exactement les raisons attendues (code de poste inconnu, minute hors intervalle).
   - Valeur `minute_out` bien `NULL` (pas `0`) pour la ligne "Données non disponibles".
   - 2e import du même fichier : toujours 2 lignes en base (aucun doublon), un nouveau lot tracé dans `import_batches`.
   - Import en dry-run : aucune ligne ni aucun lot conservé.
   - `PRAGMA integrity_check` : `ok`, aucune anomalie de clé étrangère.

Cette simulation suit fidèlement la logique du code PHP (mêmes règles, même stratégie de clé de déduplication), mais **n'exécute pas le code PHP lui-même** — une différence de syntaxe ou de comportement Laravel spécifique resterait invisible à cette méthode. C'est la meilleure vérification possible sans accès à PHP.

---

## Hypothèses posées

1. **Résolution des entités** : deux modes supportés (identifiant interne direct, ou identifiant FIFA Connect / nom d'équipe + date). D'autres modes de résolution (ex. code externe spécifique à un fournisseur) demanderaient d'étendre `EntityResolver`, non fait ici faute de connaître un fournisseur réel.
2. **Déduplication des événements de match** : pas de clé métier naturelle n'existant en base pour `match_events` (plusieurs événements peuvent partager match/joueur/type), j'ai utilisé un hash du contenu de la ligne source comme clé de ré-import. **Limite documentée** : si le fournisseur modifie le contenu d'une ligne déjà importée (correction), le hash change et une NOUVELLE ligne sera créée plutôt qu'une mise à jour de l'ancienne — contrairement aux 3 autres types, qui ont une vraie clé métier et gèrent cette correction proprement. À db1attre avec vous si les événements doivent être rejoués plus finement.
3. **`taux entre 0 et 1`** : règle implémentée et testée (`RowValidator::validateRate`), mais aucune colonne du Livrable 1 n'est actuellement de type "taux" au sens strict — le mandat demande des valeurs BRUTES, pas des moyennes, donc je n'ai mappé aucune colonne de taux dans l'exemple. La règle reste disponible si un futur mapping en a besoin.
4. Le format source est CSV avec en-tête. Le mapping JSON prévoit un indicateur `csv.has_header`, mais seul `true` est actuellement supporté — un fichier sans en-tête serait refusé avec un message clair plutôt que mal interprété.
5. Les rejets par ligne accumulent TOUTES les erreurs trouvées sur cette ligne (pas seulement la première), pour un rapport de qualité plus utile en un seul passage.

## Ce que je n'ai pas pu vérifier

- L'exécution réelle avec PHP/Laravel (voir plus haut).
- Le comportement avec un vrai fichier fournisseur (l'exemple livré est un format que j'ai inventé pour illustrer le mécanisme — un vrai fournisseur aura probablement d'autres noms de colonnes, ce que le mapping JSON permet d'adapter sans toucher au code).
- La performance sur un très gros fichier (des dizaines de milliers de lignes d'événements par saison, si la donnée passe-par-passe est un jour importée) : chaque ligne fait plusieurs requêtes SQL (résolution + vérification d'existence). Pour un usage occasionnel (import par lot après un match ou une journée), c'est largement suffisant ; pour un import massif répété, une optimisation par lot serait à envisager plus tard.

## Rappel des règles respectées

- Aucune table ni donnée existante modifiée ou supprimée par la migration (uniquement des ajouts).
- Toutes les lignes importées ont `is_demo = false` (ce sont, par définition, de VRAIES données importées, jamais de la démonstration).
- Aucune donnée médicale/santé touchée.
- Aucune donnée nominative réelle dans le code, les tests ou ce document (les identifiants FIFA et noms d'équipe des exemples sont fictifs : "FIFA-000123", "AS Exemple", "FC Demonstration").
- Le cockpit et l'onglet "Statistiques avancées" n'ont pas été touchés.

---

## Important : comment lancer les tests sur ce dépôt

`phpunit.xml` ne fait tourner par défaut qu'une liste restreinte de tests (testsuite "Working"). Les nouveaux tests de ce Livrable n'y sont **pas inclus automatiquement** — il faut les cibler explicitement :

```
php artisan test tests/Unit/RoleEvaluationImport tests/Feature/RoleEvaluationImportCommandTest.php tests/Feature/RoleEvaluationSchemaTest.php
```

---

## Prochaine étape proposée

Un commit local (sans push), regroupant la 13e migration, les services, la commande, les tests et les fichiers d'exemple. Dites-moi si je le crée, et si les 3 hypothèses ci-dessus (résolution, déduplication des événements, taux non utilisé) vous conviennent.

Une fois validé, il restera le Livrable 3 (générateur de démonstration reproductible) — que je n'ai pas commencé, conformément au mandat ("après ma validation").
