# Livrable 1 — Schéma cible pour l'évaluation « rôle et apport » et le cockpit de performance

**Statut : proposition uniquement. Aucune migration exécutée, aucune table créée ou modifiée, aucune donnée touchée.**
Travail effectué sur la branche `feature/role-evaluation-schema` (créée à partir de `feature/performance-score-phase1`, aucun commit poussé).

Base de référence : `database/database.sqlite` (schéma déjà audité — voir l'inventaire précédent). Rappel du constat qui motive ce livrable : la base ne contient que des données de démonstration (49 joueurs × 10 matchs pour les statistiques détaillées, produit cartésien synthétique, `players.contribution_score` figé à 0 partout, aucune composition d'équipe, aucun événement hors buts).

---

## 0. Ce que j'ai regardé avant de proposer

- Le schéma existant : `players`, `joueurs`, `teams`, `clubs`, `competitions`, `seasons`, `matches`, `match_events`, `match_sheets`, `player_match_detailed_stats`, `player_detailed_stats`, `player_season_stats`, et les tables vides (`lineups`, `lineup_players`, `match_rosters`, `match_results`, `match_performances`, `match_metrics`, `game_matches`, `performances`, `player_performances`, `performance_metrics`, `standings`, `team_players`, etc.).
- Les conventions de migration déjà en place (`database/migrations/`) : Laravel classique (`Schema::create`, `foreignId()->constrained()->cascadeOnDelete()`), avec un précédent explicite de gestion des différences SQLite (`2026_09_25_122000_allow_null_player_fifa_id_on_sqlite.php` teste `Schema::getConnection()->getDriverName()`).
- Le nommage des indicateurs déjà attendu par l'application, trouvé dans `app/Http/Controllers/DigitalTwinController.php` et `RpmController.php` (liste `$excludedKsaFields` : Minutes played, Goals, Assists, Yellow/Red cards, Fouls, Fouls suffered, Shots, Shots on target, Passes, Passes accurate (+%), Key passes, Crosses, Tackles, Tackles successful (+%), Interceptions, xG) et dans la migration récente `2026_09_28_090715_create_external_player_performance_metrics_table.php` (progressive_passes, challenges/defensive/attacking/aerial challenges, dribbles, dribbling_final_third, chances_created, mistakes_leading_to_goals, etc. — le catalogue "KSA"). Je n'ai **pas modifié** ces fichiers (cockpit et onglet Statistiques avancées) — je les ai seulement lus pour aligner les noms de champs.
- Point d'attention trouvé en cours de route : `config('ksa_portal_fields', [])` (utilisé par le cockpit) n'a pas de fichier de config correspondant dans `config/` — le catalogue "avancé" est donc vide en pratique aujourd'hui. Je le signale sans y toucher.

---

## 1. Participation au match

**Table existante réutilisable en partie :** `player_match_detailed_stats` a déjà `player_id`, `match_id`, `team_id`, `position_played`, `minutes_played`, `started_match`, `substituted_in`, `substituted_out`, `substitution_minute`. Insuffisant sur deux points : `substitution_minute` est unique et ambigu (entrée ou sortie ?), et `position_played` est un texte libre sans référentiel.

**Proposition : nouvelle table `match_participations`** (séparée des statistiques, pour ne pas mélanger "qui a joué où" et "ce qu'il a produit" — plus propre pour les jointures et les contrôles de cohérence du Livrable 3) :

| Colonne | Type | Notes |
|---|---|---|
| id | bigint PK | |
| match_id | FK → matches | |
| player_id | FK → players | voir §8 pour le choix players/joueurs |
| team_id | FK → teams | |
| detailed_position | string, FK logique → `position_catalog.code` (§9) | LCB, RCB, LB, RB, CDM, LCM, CAM, RCAM, LAM, RAM, CF, GK |
| is_starter | boolean | titulaire / remplaçant |
| minute_in | unsignedSmallInt, nullable | 0 si titulaire au coup d'envoi |
| minute_out | unsignedSmallInt, nullable | null si a fini le match |
| jersey_number | unsignedSmallInt, nullable | |
| is_demo | boolean, default false | §7 |
| import_batch_id | FK → import_batches, nullable | §7 |
| created_at / updated_at | timestamps | |

Contrainte d'unicité recommandée : (`match_id`, `player_id`).
Contrainte de cohérence (à valider en base applicative, pas en contrainte SQL portable) : `minute_in < minute_out` quand les deux sont renseignés ; `minute_in`/`minute_out` entre 0 et 130 (le Livrable 2 la validera à l'import).

---

## 2. Statistiques joueur par match (valeurs brutes)

**Table existante à étendre :** `player_match_detailed_stats` (96 colonnes déjà présentes couvrent une bonne partie du besoin : shots_total/on_target/off_target/blocked/inside/outside_box, passes_total/completed/failed/pass_accuracy, key_passes, long_passes(+completed), crosses_total/completed, dribbles_attempted/completed, tackles_total/won/lost, interceptions, clearances, blocks, aerial/ground_duels, fouls, cartons, minutes_played, match_rating). C'est du comptage brut, conforme à la demande (aucune moyenne stockée).

**Colonnes à ajouter** (alignées sur le cockpit et l'onglet Statistiques avancées lus en §0, jamais modifiées) :

| Colonne à ajouter | Type | Alignement |
|---|---|---|
| expected_goals | decimal(6,3), nullable | "xG" — actuellement absent, cité comme champ existant côté cockpit (`$excludedKsaFields`) |
| expected_goals_on_target | decimal(6,3), nullable | pour le calcul du xG concédé côté gardien (§ plus bas) |
| progressive_passes | unsignedSmallInt, nullable | catalogue KSA (`external_player_performance_metrics`) |
| progressive_passes_completed | unsignedSmallInt, nullable | idem |
| challenges_defensive | unsignedSmallInt, nullable | "duels" — la table KSA distingue défensif/offensif/aérien, je propose de garder cette granularité au niveau match plutôt que de la limiter aux duels aériens/au sol déjà présents |
| challenges_defensive_won | unsignedSmallInt, nullable | |
| challenges_attacking | unsignedSmallInt, nullable | |
| challenges_attacking_won | unsignedSmallInt, nullable | |
| dribbles_final_third | unsignedSmallInt, nullable | |
| dribbles_final_third_completed | unsignedSmallInt, nullable | |
| chances_created | unsignedSmallInt, nullable | |
| mistakes_leading_to_shot | unsignedSmallInt, nullable | |
| mistakes_leading_to_goal | unsignedSmallInt, nullable | |

**Champs gardien** (le poste GK a des indicateurs sans équivalent chez les autres postes ; deux options) :

- Option A (recommandée) : ajouter les colonnes gardien directement sur `player_match_detailed_stats`, toutes `nullable`, non pertinentes pour les non-gardiens (elles restent NULL) :

| Colonne | Type |
|---|---|
| gk_shots_faced | unsignedSmallInt, nullable |
| gk_shots_faced_on_target | unsignedSmallInt, nullable |
| gk_saves | unsignedSmallInt, nullable |
| gk_goals_conceded | unsignedSmallInt, nullable |
| gk_expected_goals_faced | decimal(6,3), nullable (PSxG / xG sur tirs cadrés subis) |
| gk_claims_exits | unsignedSmallInt, nullable ("sorties") |
| gk_long_passes | unsignedSmallInt, nullable |
| gk_long_passes_completed | unsignedSmallInt, nullable |
| gk_errors | unsignedSmallInt, nullable |

  Avantage : une seule table à joindre, cohérent avec l'existant. Inconvénient : une vingtaine de colonnes toujours NULL pour 10 des 11 titulaires.

- Option B : table séparée `match_goalkeeper_stats` (1 ligne par gardien par match, FK vers `match_participations`). Plus propre normalement, mais complexifie les jointures pour le calcul de score (Livrable suivant, hors périmètre ici). Je recommande l'option A pour rester simple, sauf avis contraire.

**Colonnes communes ajoutées** (§7) : `is_demo`, `import_batch_id`.

---

## 3. Statistiques d'équipe par match

**Table existante :** `matches` porte déjà des compteurs équipe en format large (`home_score`, `away_score`, `home_possession`/`away_possession`, `home_shots`/`away_shots`, `home_shots_on_target`/`away_shots_on_target`, `home_corners`/`away_corners`, `home_fouls`/`away_fouls`, `home_offsides`/`away_offsides`, `home_yellow_cards`/`away_yellow_cards`, `home_red_cards`/`away_red_cards`). Aujourd'hui vides à plus de 70 % sauf le score.

**Problème avec ce format** : il est difficile d'écrire un contrôle générique "les statistiques d'équipe correspondent à la somme des statistiques joueurs" (exigence du Livrable 3) sans distinguer systématiquement domicile/extérieur dans chaque requête.

**Proposition : nouvelle table normalisée `match_team_stats`** (une ligne par équipe par match), sans toucher aux colonnes existantes de `matches` (laissées en l'état, dépréciation à discuter plus tard) :

| Colonne | Type |
|---|---|
| id | bigint PK |
| match_id | FK → matches |
| team_id | FK → teams |
| is_home | boolean |
| possession_pct | decimal(5,2), nullable |
| shots_total | unsignedSmallInt, nullable |
| shots_on_target | unsignedSmallInt, nullable |
| corners | unsignedSmallInt, nullable |
| fouls | unsignedSmallInt, nullable |
| offsides | unsignedSmallInt, nullable |
| yellow_cards | unsignedSmallInt, nullable |
| red_cards | unsignedSmallInt, nullable |
| expected_goals | decimal(6,3), nullable |
| goals_scored | unsignedSmallInt, nullable |
| is_demo | boolean, default false |
| import_batch_id | FK → import_batches, nullable |

Contrainte d'unicité : (`match_id`, `team_id`).

---

## 4. Événements de match

**Table existante largement réutilisable :** `match_events` a déjà `match_id`, `type`/`event_type`, `minute`, `extra_time_minute`, `period`, `player_id`, `team_id`, `assisted_by_player_id` (passeur décisif sur un but), `substituted_player_id`, `location`, `is_confirmed`, `is_contested`. Aujourd'hui seul le type "goal" est utilisé (48 lignes).

**À ajouter pour couvrir buts/cartons/remplacements proprement** :
- Rien de structurel à ajouter — le schéma le permet déjà. Il manque une **table de référentiel des types d'événements** (`event_types` : code, libellé FR/EN) pour éviter les valeurs libres non contrôlées dans `type`/`event_type` (aujourd'hui un simple varchar). Codes proposés : `goal`, `own_goal`, `yellow_card`, `second_yellow_card`, `red_card`, `substitution`, `pass` (voir ci-dessous).
- `substituted_player_id` existe déjà pour les remplacements (joueur sortant), il faudra aussi renseigner `player_id` comme joueur entrant.

**Pour les passes avec destinataire et zone (mentionné comme un second temps — "puis")** :

| Colonne à ajouter sur `match_events` | Type | Notes |
|---|---|---|
| recipient_player_id | FK → players, nullable | destinataire de la passe (distinct de `assisted_by_player_id`, qui est spécifique aux passes décisives sur but) |
| origin_zone | string(4), nullable | code de zone simplifié (ex. grille 6×4) plutôt que coordonnées x/y en pixels, plus stable entre sources |
| destination_zone | string(4), nullable | idem |
| is_successful | boolean, nullable | pour une passe |

**Point d'attention (hypothèse à valider) :** journaliser CHAQUE passe d'un match comme une ligne de `match_events` peut représenter plusieurs centaines de lignes par match (une équipe fait souvent 300 à 600 passes/match). Ça reste gérable en volume (quelques centaines de milliers de lignes pour une saison complète), mais je recommande d'indexer (`match_id`, `type`) et (`player_id`, `type`) dès la création, et de ne pas bloquer le Livrable 2 sur l'obtention de la donnée passe-par-passe si la source fournisseur ne la donne pas — les fournisseurs de stats ne livrent pas tous cette granularité. Je propose de traiter ça comme une extension optionnelle du modèle d'import (§ Livrable 2), pas comme un prérequis bloquant.

**Colonnes communes ajoutées** (§7) : `is_demo`, `import_batch_id`.

---

## 5. Configuration des rôles par famille de poste (versionnée)

Rien d'existant à réutiliser ici. Deux tables, pour séparer "une version de configuration" (immuable une fois publiée) de "les poids qu'elle contient" :

**`role_config_versions`**

| Colonne | Type |
|---|---|
| id | bigint PK |
| label | string (ex. "v1.0", "2026-Q4") |
| description | text, nullable |
| status | string : `draft`, `published`, `archived` |
| published_at | timestamp, nullable |
| created_by | FK → users, nullable |
| created_at / updated_at | timestamps |

**`role_config_weights`**

| Colonne | Type |
|---|---|
| id | bigint PK |
| role_config_version_id | FK → role_config_versions |
| position_family | string, FK logique → `position_catalog.family` (§9) |
| dimension_key | string (ex. "passing", "duels", "defending", "chance_creation", "gk_shot_stopping"…) |
| weight | decimal(6,4) |
| created_at / updated_at | timestamps |

Contrainte d'unicité : (`role_config_version_id`, `position_family`, `dimension_key`).
Règle métier (rappelée du mandat, à faire respecter côté application, pas en contrainte SQL) : ces poids ne doivent jamais être calés sur des lignes où `is_demo = true` — la table elle-même ne porte pas `is_demo` (ce n'est pas une donnée de match), mais le processus de calibration devra explicitement filtrer les données démo en amont.

---

## 6. Résultats calculés

**`player_role_evaluations`**

| Colonne | Type |
|---|---|
| id | bigint PK |
| player_id | FK → players |
| match_id | FK → matches, nullable | nullable pour permettre une évaluation agrégée (ex. sur une fenêtre de matchs) en plus d'une évaluation par match |
| period_start / period_end | date, nullable | utilisé si `match_id` est null |
| position_family_evaluated | string | famille évaluée (un joueur peut être évalué sur plusieurs familles) |
| role_config_version_id | FK → role_config_versions | |
| model_version | string | version du code/algorithme de calcul, distincte de la config de poids |
| score | decimal(6,2) | |
| reliability | decimal(5,4), nullable | ex. 0 à 1 |
| interval_low / interval_high | decimal(6,2), nullable | intervalle de confiance |
| role_fit_score | decimal(6,2), nullable | adéquation au rôle |
| is_demo | boolean, default false | hérité des données sources utilisées pour le calcul |
| computed_at | timestamp | |
| created_at / updated_at | timestamps |

Pas d'`import_batch_id` ici (ce n'est pas une donnée importée mais calculée) — je propose à la place `source_import_batch_id` et/ou `source_demo_batch_id` (nullable, FK → `import_batches`) pour tracer de quel(s) lot(s) les données d'entrée provenaient, utile pour le bandeau "Données de démonstration" côté interface.

---

## 7. `is_demo` et lot d'importation — vue d'ensemble

**Table transverse proposée : `import_batches`**

| Colonne | Type |
|---|---|
| id | bigint PK |
| batch_type | string : `import` (Livrable 2) ou `demo_generation` (Livrable 3) |
| source_label | string (ex. nom du fournisseur, ou "générateur démo v1") |
| status | string : `running`, `completed`, `failed` |
| seed | string, nullable | graine utilisée si génération démo |
| params | json, nullable | paramètres de génération/import |
| started_at / finished_at | timestamp, nullable |
| created_by | FK → users, nullable |
| created_at / updated_at | timestamps |

**Tables qui reçoivent `is_demo` (boolean, default false) + `import_batch_id` (FK nullable → import_batches)** : `matches`, `match_participations` (nouvelle), `player_match_detailed_stats` (existante, étendue), `match_team_stats` (nouvelle), `match_events` (existante, étendue). J'ajoute aussi `players` et `teams` à cette liste par cohérence : si le générateur de démonstration crée aussi des joueurs et équipes fictifs (Livrable 3), il faut pouvoir les distinguer des vraies fiches — sinon le bandeau "Données de démonstration" ne pourra pas s'appliquer correctement à une fiche joueur consultée isolément. Je signale ce point comme une extension de périmètre par rapport à votre liste ("toutes les tables de données de match") — à valider : voulez-vous que `players`/`teams` portent aussi `is_demo`, ou préférez-vous les exclure et gérer le marquage démo uniquement au niveau des matchs/statistiques ?

---

## 8. Table de référence unique pour les joueurs

**Constat factuel** (vérifié dans le code, pas seulement dans les données) :
- `players` : 825 lignes, 111 colonnes, référencée par des clés étrangères dans `teams`, `matches` (indirectement via les stats), `player_match_detailed_stats`, `player_season_stats`, `ai_predictions`, etc. C'est la table utilisée par l'application (aucun modèle Eloquent ne pointe vers `joueurs`).
- `joueurs` : 49 lignes, 113 colonnes en français, **aucun modèle Eloquent ni contrôleur ne la référence** (recherche effectuée dans `app/Models/`) — c'est une table orpheline, probablement un reliquat d'un import ou d'un prototype antérieur.

**Recommandation : `players` comme référence unique.** Description de la migration (non exécutée) :
1. Vérifier qu'aucune des 49 lignes de `joueurs` ne porte une information absente de `players` pour le même joueur (rapprochement par nom + date de naissance, puisqu'il n'y a pas de clé commune évidente). D'après le schéma, les colonnes de `joueurs` (buts, passes_decisives, matchs, minutes_jouees, note_moyenne, statistiques_*) sont des agrégats qui ont leur équivalent dans `player_season_stats`/`player_match_detailed_stats` côté `players` — donc probablement aucune perte.
2. Si le rapprochement confirme l'absence de donnée unique, renommer `joueurs` en `joueurs_archive_AAAAMMJJ` (pas de suppression) et retirer son usage de tout script qui la lirait encore (aucun trouvé pour l'instant).
3. Attendre votre accord écrit avant toute exécution, conformément à la règle du mandat.

**Tables fantômes signalées** (0 ligne, jamais alimentées, à ne pas confondre avec les nouvelles tables proposées ci-dessus) :

| Table fantôme | Chevauchement avec |
|---|---|
| game_matches | doublon structurel de `matches` |
| match_results, match_performances, match_metrics | doublon conceptuel de `player_match_detailed_stats` |
| performances, player_performances, performance_metrics | doublon conceptuel de `player_match_detailed_stats` / `player_season_stats` |
| lineups, lineup_players, match_rosters, match_roster_players, team_players | doublon conceptuel de la nouvelle `match_participations` (§1) |
| standings, competition_rankings, competition_team | à recalculer depuis `match_team_stats` plutôt qu'à alimenter séparément |
| national_team_callups | sans rapport direct avec ce mandat, signalé pour information |

Recommandation : ne rien supprimer maintenant ; une fois les nouvelles tables adoptées et alimentées, revenir vers vous avec une liste précise de tables à archiver.

---

## 9. Table de correspondance des postes

**`position_catalog`**

| Colonne | Type |
|---|---|
| code | string, PK (ex. LCB, RCB, LB, RB, CDM, LCM, CAM, RCAM, LAM, RAM, CF, GK) |
| label_fr | string |
| label_en | string |
| family | string (défenseur central, latéral, milieu défensif, milieu relayeur, milieu offensif, ailier, avant-centre, gardien) |
| broad_group | string : GK / DEF / MID / FWD (compatibilité avec les 4 codes déjà utilisés dans `players.position`) |
| display_order | unsignedSmallInt, nullable |

Correspondance proposée à partir de la liste fournie :

| code | family | broad_group |
|---|---|---|
| GK | gardien | GK |
| LCB, RCB | défenseur central | DEF |
| LB, RB | latéral | DEF |
| CDM | milieu défensif | MID |
| LCM | milieu relayeur | MID |
| CAM, RCAM | milieu offensif | MID |
| LAM, RAM | ailier | MID ou FWD selon convention retenue — **hypothèse : MID**, à confirmer |
| CF | avant-centre | FWD |

**Deux points à trancher avec vous, je n'ai pas tranché seul :**
- La liste fournie contient `LCM` sans `RCM`, et `CAM`/`RCAM` sans `LCAM` — asymétrie probablement volontaire (schéma tactique type 4-3-3 asymétrique) mais je préfère vous le signaler plutôt que de compléter de mon initiative. La table reste extensible si d'autres codes sont nécessaires plus tard (RCM, LCAM, ST, SW, RW/LW…).
- `LAM`/`RAM` : je les ai classés en famille "ailier" avec regroupement large "MID" par défaut ; certains référentiels les classent en attaque (FWD). Dites-moi si vous voulez FWD à la place.

`players.position` et `player_match_detailed_stats`/`match_participations.detailed_position` restent deux colonnes distinctes : la première garde son usage actuel (poste "principal", regroupement large), la seconde capture le poste réellement joué ce match-là, plus précis.

---

## Compatibilité SQLite / MySQL / PostgreSQL

- Tous les types proposés ci-dessus utilisent les méthodes portables du Query Builder Laravel (`bigInteger`/`id()`, `string()`, `text()`, `decimal()`, `boolean()`, `unsignedSmallInteger()`, `json()`, `timestamp()`) — aucun type propriétaire (pas d'`ENUM` MySQL, pas de `jsonb` Postgres explicite : `json()` de Laravel s'adapte au moteur).
- Les valeurs contrôlées (types d'événements, statut de lot, famille de poste) sont modélisées en `string` + table de référentiel plutôt qu'en `ENUM`, précisément parce que le support d'`ENUM` diffère entre SQLite (aucun support natif), MySQL et PostgreSQL.
- Les contraintes d'unicité composites et les clés étrangères (`foreignId()->constrained()`) sont supportées par les trois moteurs.
- Point de vigilance déjà rencontré dans ce projet (cf. migration `allow_null_player_fifa_id_on_sqlite`) : SQLite ne permet pas de modifier certaines contraintes NOT NULL après création sans recréer la table — à garder à l'esprit si des colonnes doivent changer de nullabilité plus tard.

---

## Hypothèses posées

1. `LAM`/`RAM` classés en famille "ailier"/regroupement "MID" (à confirmer, voir §9).
2. Les gardiens portent leurs statistiques directement sur `player_match_detailed_stats` (option A, §2) plutôt que sur une table séparée.
3. `players`/`teams` ne portent pas `is_demo` sauf validation contraire de votre part (voir §7) — je n'ai pas ajouté cette colonne par défaut sur ces deux tables dans le décompte du mandat, mais je signale que ça peut être nécessaire.
4. La journalisation passe-par-passe (§4) est traitée comme une extension optionnelle du Livrable 2, pas comme un prérequis du Livrable 1.
5. `matches.home_*/away_*` restent en l'état (non dépréciés dans l'immédiat) en parallèle de la nouvelle `match_team_stats`.

## Ce que je n'ai pas pu vérifier

- Le contenu exact attendu par l'onglet "Statistiques avancées" au-delà de la liste `$excludedKsaFields` et des colonnes de `external_player_performance_metrics` — le fichier de config `ksa_portal_fields` référencé par le contrôleur n'existe pas dans le dépôt, donc le catalogue complet réel (au-delà de ces deux sources) reste inconnu.
- Si un connecteur front-end (Vue/JS) consomme déjà certaines colonnes de `matches.home_*/away_*` par leur nom exact — je n'ai pas parcouru tout le JS/Vue du cockpit (hors périmètre : je ne devais pas y toucher), donc je ne peux pas garantir à 100 % qu'une dépréciation future de ces colonnes serait sans impact.
- L'exhaustivité de la liste des "tables fantômes" au-delà de celles déjà identifiées dans l'inventaire précédent — 158 tables existent au total, je n'ai réexaminé que celles conceptuellement proches de ce mandat.

## Ce qui a changé sur le dépôt à ce stade

Rien dans la base de données. Une branche git a été créée (`feature/role-evaluation-schema`, aucun commit). Ce document est le seul livrable de cette étape.
