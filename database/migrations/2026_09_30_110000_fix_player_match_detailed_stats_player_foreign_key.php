<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CORRECTIF découvert pendant la validation du Livrable 3 (voir
     * docs/role-evaluation/04-implementation-livrable-3.md, "Découverte
     * importante" et le §8 du Livrable 1, "table de référence unique pour
     * les joueurs").
     *
     * player_match_detailed_stats.player_id référence AUJOURD'HUI
     * joueurs(id), pas players(id) — vérifié par introspection réelle
     * (PRAGMA foreign_key_list) sur une copie jetable de la base, avec
     * PRAGMA foreign_keys=ON (comme en production : config/database.php
     * a foreign_key_constraints => true, non désactivé par .env).
     *
     * Preuve que ce n'est pas juste un détail de nommage : players.id et
     * joueurs.id 1..49 désignent des personnes COMPLÈTEMENT DIFFÉRENTES qui
     * partagent la même plage d'identifiants par coïncidence (ex. id=1 :
     * "Alexandre Michel" dans players, "Bouazza Samir" dans joueurs). Le
     * jeu de démonstration actuel (49 joueurs / 10 matchs, 490 lignes dans
     * player_match_detailed_stats) ne "fonctionne" que par cette coïncidence
     * de plage — sémantiquement, ces 490 lignes pointent vers joueurs, pas
     * vers les 49 premiers joueurs de players. Cette migration NE CHANGE
     * AUCUNE valeur de player_id : les 490 lignes gardent exactement les
     * mêmes entiers ; seule la table que ces entiers désignent change,
     * conformément à ce que le code applicatif suppose déjà (voir plus bas).
     *
     * joueurs est par ailleurs l'ancrage d'un sous-système distinct incluant
     * player_real_time_health et player_sdoh_data (RÈGLES du mandat :
     * "aucune donnée médicale ni de santé... dans ces travaux") — encore
     * une raison de ne PAS construire dessus pour le nouveau travail
     * "rôle et apport", et de consolider sur players, conformément à la
     * recommandation du Livrable 1 §8.
     *
     * Portée volontairement minimale : seule la FK de
     * player_match_detailed_stats.player_id est corrigée ici.
     * player_detailed_stats, player_connected_devices,
     * player_real_time_health, player_sdoh_data référencent aussi joueurs et
     * ont probablement la même ambiguïté, mais sont hors du périmètre des
     * livrables de ce mandat (les deux dernières sont explicitement des
     * tables de santé interdites) — signalé au rapport, pas traité ici.
     *
     * Élément rassurant sur ce correctif : le code applicatif existant
     * suppose DÉJÀ la relation corrigée. app/Models/Player.php (ligne 216)
     * déclare `hasMany(PlayerMatchDetailedStats::class, 'player_id')` — donc
     * Eloquent traite déjà player_match_detailed_stats.player_id comme une
     * référence à players, pas à joueurs. Le modèle Joueur.php, lui, ne
     * déclare AUCUNE relation vers player_match_detailed_stats. La
     * contrainte FK en base contredit donc le code applicatif lui-même :
     * ce correctif aligne le schéma sur ce que l'application suppose déjà,
     * il ne change pas le comportement applicatif.
     *
     * DÉCOUVERTE (exécution réelle, moteur de calcul, 30/09) : la première
     * version de cette migration utilisait Schema::table(...)->dropForeign()
     * puis ->foreign(...) — cela suppose que Laravel reconstruit
     * silencieusement la table via doctrine/dbal, comme documenté au
     * Livrable 3. C'est FAUX en pratique : Laravel 10 lève explicitement
     * `BadMethodCallException: SQLite doesn't support dropping foreign
     * keys (you would need to re-create the table)` pour toute tentative
     * de dropForeign() sur SQLite, que doctrine/dbal soit installé ou non
     * (vérifié : présent, version 3.10.6, l'exception est quand même levée).
     * Doctrine/dbal n'intervient pas du tout dans ce chemin de code pour
     * cette version de Laravel.
     *
     * MÉTHODE CORRIGÉE : reconstruction manuelle de la table, seule approche
     * qui fonctionne sur SQLite pour changer une clé étrangère :
     *  1. Lire le SQL exact de la table actuelle depuis sqlite_master (pas
     *     un Blueprint retapé à la main : le SQL réel garantit qu'aucune
     *     colonne, contrainte ou valeur par défaut n'est oubliée ou altérée
     *     par erreur, sur une table qui compte aujourd'hui 123 colonnes).
     *  2. Remplacer uniquement la clause `references "joueurs"("id")` par
     *     `references "players"("id")` dans ce texte — échec explicite et
     *     immédiat (RuntimeException, rien créé ni modifié) si cette clause
     *     exacte n'est pas trouvée une fois, pour ne jamais reconstruire une
     *     table à partir d'un SQL mal transformé.
     *  3. Créer la table reconstruite sous un nom temporaire, copier
     *     TOUTES les lignes existantes (490 dans le jeu de démonstration
     *     actuel) sans toucher à la moindre valeur, recréer les 7 index
     *     existants (dont l'unique player_id+match_id) sur la nouvelle
     *     table, supprimer l'ancienne table, renommer la nouvelle à sa
     *     place.
     *  4. down() applique la même méthode en sens inverse (players -> joueurs).
     *
     * Validée par exécution réelle sur une copie jetable de la base réelle
     * (490 lignes) : succès, 490 lignes après comme avant, aucune valeur de
     * colonne modifiée (comparaison ligne à ligne), FK réellement appliquée
     * (INSERT avec player_id inexistant dans players rejeté par SQLite,
     * PRAGMA foreign_keys=ON). Voir le rapport pour le détail.
     *
     * NON EXÉCUTÉE sur votre base réelle : comme toutes les migrations de ce
     * mandat, cette proposition attend votre validation avant
     * `php artisan migrate` (RÈGLE globale : aucune modification d'une
     * table ou contrainte existante sans accord écrit).
     */
    public function up(): void
    {
        $this->rebuildForeignKey(from: 'joueurs', to: 'players');
    }

    public function down(): void
    {
        $this->rebuildForeignKey(from: 'players', to: 'joueurs');
    }

    private function rebuildForeignKey(string $from, string $to): void
    {
        $table = 'player_match_detailed_stats';
        $rebuiltName = $table . '_fk_rebuild_tmp';

        $originalRow = DB::selectOne(
            "SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ?",
            [$table]
        );

        if (!$originalRow) {
            throw new \RuntimeException("Correctif FK : table {$table} introuvable — migration interrompue, rien modifié.");
        }

        // DÉCOUVERTE (exécution réelle) : après qu'une migration ait modifié
        // des colonnes de cette même table via dropColumn() (qui, contrairement
        // à dropForeign(), reconstruit bien la table sur SQLite), le SQL brut
        // stocké dans sqlite_master est réécrit par le moteur sous-jacent dans
        // un format différent (casse, espacement, clause "ON DELETE" en
        // majuscules avec suffixes) de celui d'origine généré par Laravel. Un
        // remplacement de texte littéral ne survit donc pas à ce genre de
        // migration ultérieure sur la même table. On cible donc la clause FK
        // par une expression tolérante à la casse et à l'espacement, ancrée
        // sur la colonne "player_id" (jamais team_id/match_id/etc.) pour ne
        // jamais risquer de toucher une autre clé étrangère de la table.
        $pattern = '/(FOREIGN\s+KEY\s*\(\s*"?player_id"?\s*\)\s*REFERENCES\s*)"?' . preg_quote($from, '/') . '"?(\s*\(\s*"?id"?\s*\))/i';
        $count = preg_match_all($pattern, $originalRow->sql);

        if ($count !== 1) {
            throw new \RuntimeException(
                "Correctif FK : la clause FK player_id -> {$from} devait apparaître une fois dans le SQL de {$table}, ".
                "trouvée {$count} fois — migration interrompue par sécurité, aucune table modifiée. ".
                "Le schéma a probablement changé depuis l'écriture de ce correctif ; à corriger avant de réessayer."
            );
        }

        $rebuiltSql = preg_replace($pattern, '${1}"' . $to . '"${2}', $originalRow->sql);
        $rebuiltSql = $this->renameTableInCreateStatement($rebuiltSql, $table, $rebuiltName);

        $indexRows = DB::select(
            "SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ? AND sql IS NOT NULL",
            [$table]
        );

        DB::statement($rebuiltSql);
        DB::statement("INSERT INTO \"{$rebuiltName}\" SELECT * FROM \"{$table}\"");

        $before = DB::selectOne("SELECT COUNT(*) AS n FROM \"{$table}\"")->n;
        $after = DB::selectOne("SELECT COUNT(*) AS n FROM \"{$rebuiltName}\"")->n;
        if ($before !== $after) {
            DB::statement("DROP TABLE \"{$rebuiltName}\"");
            throw new \RuntimeException(
                "Correctif FK : {$before} lignes dans {$table} mais {$after} après copie dans la table reconstruite — ".
                "migration interrompue, table temporaire supprimée, {$table} original intact."
            );
        }

        Schema::drop($table);
        Schema::rename($rebuiltName, $table);

        // Les CREATE INDEX capturés visent déjà le nom final "player_match_
        // detailed_stats" (celui de la table avant reconstruction) ; comme
        // Schema::rename() restaure exactement ce même nom, ils se rejouent
        // sans aucune modification.
        foreach ($indexRows as $indexRow) {
            DB::statement($indexRow->sql);
        }
    }

    private function renameTableInCreateStatement(string $sql, string $from, string $to): string
    {
        // Comme pour la clause FK ci-dessus : selon qu'une précédente
        // migration a ou non déjà fait reconstruire cette table par le
        // moteur sous-jacent, "CREATE TABLE" peut être suivi du nom de table
        // avec ou sans guillemets. On tolère les deux formats.
        $pattern = '/CREATE TABLE\s+"?' . preg_quote($from, '/') . '"?\s*\(/i';
        if (preg_match_all($pattern, $sql) !== 1) {
            throw new \RuntimeException("Correctif FK : impossible de renommer la table dans le SQL reconstruit (motif CREATE TABLE {$from} introuvable ou ambigu).");
        }

        return preg_replace($pattern, 'CREATE TABLE "' . $to . '" (', $sql, 1);
    }
};
