<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
     * jeu de démonstration actuel (49 joueurs / 10 matchs) ne "fonctionne"
     * que par cette coïncidence de plage — sémantiquement, les lignes
     * player_match_detailed_stats existantes pointent vers joueurs, pas
     * vers les 49 premiers joueurs de players.
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
     * trois livrables (les deux dernières sont explicitement des tables de
     * santé interdites) — signalé au rapport, pas traité ici.
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
     * NON EXÉCUTÉE : comme toutes les migrations de ce mandat, cette
     * proposition attend votre validation avant `php artisan migrate`
     * (RÈGLE globale : aucune modification d'une table ou contrainte
     * existante sans accord écrit). Elle est un PRÉREQUIS du Livrable 3 :
     * sans elle, l'écriture des statistiques joueur générées échoue avec
     * une violation de clé étrangère (vérifié sur copie jetable).
     *
     * Portabilité SQLite/MySQL/PostgreSQL : dropForeign()/foreign() passent
     * par doctrine/dbal (présent, composer.json : "doctrine/dbal": "^3.10")
     * pour la reconstruction de table que SQLite exige afin de changer une
     * clé étrangère. Non exécutée ici (pas de PHP sur la machine connectée),
     * donc non vérifiée en conditions réelles — voir "Ce que je n'ai pas pu
     * vérifier" du rapport.
     */
    public function up(): void
    {
        Schema::table('player_match_detailed_stats', function (Blueprint $table) {
            $table->dropForeign(['player_id']); // actuellement -> joueurs(id)
        });

        Schema::table('player_match_detailed_stats', function (Blueprint $table) {
            $table->foreign('player_id')->references('id')->on('players')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('player_match_detailed_stats', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
        });

        Schema::table('player_match_detailed_stats', function (Blueprint $table) {
            $table->foreign('player_id')->references('id')->on('joueurs')->cascadeOnDelete();
        });
    }
};
