# Livrable 1 — Implémentation du schéma cible

**Statut : migrations écrites et testées, mais non exécutées sur la base de développement.**
Branche `feature/role-evaluation-schema`, aucun commit encore créé (proposition d'en créer un, local, sans push — voir en fin de document), aucune donnée existante modifiée ou supprimée.

Ce document fait suite à `01-schema-cible.md` (la proposition), que vous avez validée dans son ensemble ("on avance dans ce sens"). J'ai adopté mes propres recommandations pour les points que j'avais laissés ouverts — ils restent modifiables, ce sont des choix réversibles, listés ci-dessous.

---

## Décisions adoptées sur les points laissés ouverts

| Point ouvert (Livrable 1) | Décision adoptée | Réversibilité |
|---|---|---|
| Statistiques gardien : option A (colonnes sur `player_match_detailed_stats`) vs option B (table séparée) | **Option A**, comme recommandé | Migration à annuler + nouvelle si vous préférez B |
| `players`/`teams` doivent-ils porter `is_demo` ? | **Oui**, ajouté par cohérence (sinon le bandeau "Données de démonstration" ne peut pas s'appliquer à une fiche joueur/équipe consultée isolément) | Migration dédiée, facile à retirer si non souhaité |
| Asymétrie `LCM` sans `RCM`, `CAM`/`RCAM` sans `LCAM` | **Non complétée** — j'ai inséré exactement les 12 codes fournis, rien de plus, comme annoncé | Table `position_catalog` : ajouter une ligne suffit |
| `LAM`/`RAM` : famille "ailier", regroupement large MID ou FWD ? | **MID**, hypothèse documentée dans le Livrable 1, non confirmée | `UPDATE position_catalog SET broad_group = 'FWD' WHERE code IN ('LAM','RAM')` si vous préférez |

Si l'un de ces choix ne vous convient pas, dites-le : ce sont des ajustements mineurs, pas une remise en cause du schéma.

---

## Ce qui a été fait

### 1. Douze migrations Laravel écrites (non exécutées)

Toutes dans `database/migrations/`, préfixées `2026_09_30_09*`, dans l'ordre de dépendance (référentiels d'abord, tables qui les utilisent ensuite) :

1. `create_position_catalog_table` — référentiel des 12 postes détaillés + les 12 lignes de correspondance
2. `create_event_types_table` — référentiel des 7 types d'événements + les 7 lignes
3. `create_import_batches_table` — table transverse de traçabilité des lots
4. `add_is_demo_and_import_batch_to_matches_table`
5. `add_is_demo_and_import_batch_to_players_and_teams_tables`
6. `create_match_participations_table`
7. `add_role_evaluation_columns_to_player_match_detailed_stats_table` — 13 colonnes brutes + 9 colonnes gardien + traçabilité
8. `create_match_team_stats_table`
9. `add_event_extension_columns_to_match_events_table` — destinataire/zones de passe + index
10. `create_role_config_versions_table`
11. `create_role_config_weights_table`
12. `create_player_role_evaluations_table`

Chaque migration porte un commentaire PHP expliquant sa justification et son lien avec le Livrable 1. Aucune ne modifie ou supprime une colonne existante : uniquement des `Schema::create()` (nouvelles tables) et des `Schema::table()->…` en ajout de colonnes nullables ou à valeur par défaut. Chaque migration a une méthode `down()` symétrique.

### 2. Validation du DDL (sans PHP)

**Important — limite de cet environnement :** la machine connectée ne dispose d'aucun binaire `php` (ni `php`, ni `php8.x`, vérifié). Je n'ai donc **pas pu exécuter `php artisan migrate`**, ni faire tourner PHPUnit. Ce que j'ai fait à la place, pour vérifier que le schéma proposé est correct avant que vous ne lanciez vous-même les migrations :

- J'ai reconstruit à la main le SQL SQLite strictement équivalent à ce que chacune des 12 migrations doit produire.
- Je l'ai exécuté sur une **copie jetable** de `database/database.sqlite` (jamais le fichier réel), copie créée hors du dépôt puis supprimée après le test.
- Résultat : les 15 étapes DDL (12 migrations + 3 insertions de données de référence) se sont exécutées sans erreur. `PRAGMA foreign_key_check` : 0 anomalie. `PRAGMA integrity_check` : `ok`. Aucune perte de ligne sur les tables existantes (`players` : 825 lignes avant/après, `player_match_detailed_stats` : 490 lignes avant/après).

Cela donne une bonne confiance que les migrations Laravel s'exécuteront proprement, **sans toutefois être une garantie à 100 %** : Laravel peut générer un SQL légèrement différent de ma reconstruction manuelle sur certains points de syntaxe (voir "Ce que je n'ai pas pu vérifier").

### 3. Test automatisé écrit (non exécuté)

`tests/Feature/RoleEvaluationSchemaTest.php` — 11 tests qui vérifient, une fois les migrations appliquées :
- l'existence des nouvelles tables et de leurs colonnes,
- que `position_catalog` contient exactement les 12 codes attendus (pas un de plus),
- que `event_types` contient les 7 codes attendus,
- que les colonnes historiques de `player_match_detailed_stats`, `match_events`, `matches`, `players`, `teams` sont toujours là (preuve que rien n'a été perdu),
- que les contraintes d'unicité fonctionnent (`match_participations` : un joueur ne peut pas apparaître deux fois sur le même match ; `role_config_weights` : pas deux poids pour la même dimension/famille/version).

**Point de sécurité important :** ce test utilise `DatabaseTransactions` (comme `AdministrationViewSmokeTest` déjà dans le dépôt), jamais `RefreshDatabase`. C'est voulu : `phpunit.xml` pointe `DB_DATABASE` sur `database/database.sqlite`, c'est-à-dire **la base de développement elle-même**, pas une base de test séparée. `RefreshDatabase` (ou `migrate:fresh`) viderait entièrement cette base — strictement interdit par le mandat. `DatabaseTransactions` enveloppe chaque test dans une transaction annulée à la fin, donc rien n'est conservé même si le test est exécuté.

---

## Comment appliquer ceci vous-même (ou faire appliquer)

Cet environnement cloud n'a pas accès à `php`/`artisan` sur votre machine. Concrètement, pour activer ce schéma, quelqu'un ayant accès à un terminal avec PHP sur ce projet devra :

1. **Faire une sauvegarde de `database/database.sqlite`** avant toute manipulation (`cp database/database.sqlite database/database.sqlite.backup-avant-livrable1`).
2. Lancer `php artisan migrate` (jamais `migrate:fresh` ni `migrate:refresh`, qui suppriment les données existantes).
3. Vérifier avec `php artisan test --filter=RoleEvaluationSchemaTest`.
4. En cas de problème, `php artisan migrate:rollback` annule proprement les 12 migrations (chacune a sa méthode `down()`).

Si vous n'avez pas cet accès PHP vous-même, dites-le-moi : je peux voir avec vous comment l'obtenir (environnement de dev existant, Docker, Herd/Valet, etc.) plutôt que de deviner.

---

## Ce que je n'ai pas pu vérifier

- **Exécution réelle des migrations** : aucun PHP disponible dans cet environnement (ni sur la machine connectée, ni dans mon espace de travail cloud pour ce dépôt). La validation ci-dessus est une reconstruction manuelle du DDL, pas une exécution de `php artisan migrate`.
- **Exécution réelle des tests PHPUnit** : même limite.
- Le comportement exact de Laravel sur SQLite pour `dropConstrainedForeignId()` dans les méthodes `down()` — cette méthode existe dans les versions récentes du framework, cohérente avec `composer.json` (`laravel/framework` non vérifié en détail ici), mais je ne l'ai pas testée en conditions réelles faute de PHP.
- Le nom exact que Laravel donnerait automatiquement à certains index (`match_events_match_id_type_index`, etc.) — je les ai nommés moi-même par prudence pour la méthode `down()`, mais Laravel les nomme normalement de façon identique par convention ; à confirmer à la première exécution réelle.

---

## Rappel des règles respectées

- Aucune table ni donnée existante modifiée ou supprimée (uniquement des ajouts).
- Aucune donnée médicale/santé touchée.
- Aucune donnée nominative réelle dans les migrations, le test ou ce document.
- Aucun poids/seuil de modèle réglé (la migration `role_config_weights` ne contient aucune ligne insérée).
- Le cockpit et l'onglet "Statistiques avancées" n'ont pas été touchés.
- `is_demo` est ajouté avec une valeur par défaut `false` partout — rien n'est automatiquement marqué comme démo par ces migrations.

---

## Prochaine étape proposée

Un commit **local** (comme pour le module PCMA précédemment) regroupant les 12 migrations, le test, et la documentation — toujours sans push. Dites-moi si je le crée, et si vous validez les 4 décisions du tableau ci-dessus, ou si vous voulez que j'en change une avant de committer.

Une fois ceci validé et les migrations réellement exécutées de votre côté (ou avec mon aide si vous obtenez un accès PHP dans une prochaine session), je peux enchaîner sur le Livrable 2 (l'importeur).
