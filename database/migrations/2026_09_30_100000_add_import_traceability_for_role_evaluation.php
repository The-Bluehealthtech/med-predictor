<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Livrable 2 : ajouts nécessaires à l'importeur, en plus du schéma du
     * Livrable 1. Rien n'est modifié ni supprimé sur l'existant.
     *
     * - source_row_hash sur les 4 tables cibles de l'import : clé technique
     *   de ré-importation sans doublon. Pour match_participations,
     *   player_match_detailed_stats et match_team_stats, le doublon est déjà
     *   évité par la clé métier existante (match_id+player_id ou
     *   match_id+team_id) ; source_row_hash sert alors de trace d'audit
     *   ("quelle ligne source a produit cette ligne ?"), pas de clé de
     *   déduplication. Pour match_events, qui n'a pas de clé métier naturelle
     *   (plusieurs événements peuvent partager match/joueur/type), c'est la
     *   clé de déduplication réelle : voir docs/role-evaluation/
     *   03-implementation-livrable-2.md pour la limite documentée de cette
     *   approche.
     * - report / rows_read / rows_imported / rows_rejected sur import_batches :
     *   le rapport de qualité exigé par le mandat à chaque lot.
     */
    public function up(): void
    {
        foreach (['match_participations', 'player_match_detailed_stats', 'match_team_stats', 'match_events'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('source_row_hash', 64)->nullable();
            });
        }

        // Index simple (traçabilité) sur les trois tables à clé métier déjà unique.
        Schema::table('match_participations', function (Blueprint $t) {
            $t->index('source_row_hash');
        });
        Schema::table('player_match_detailed_stats', function (Blueprint $t) {
            $t->index('source_row_hash');
        });
        Schema::table('match_team_stats', function (Blueprint $t) {
            $t->index('source_row_hash');
        });

        // match_events : source_row_hash EST la clé de déduplication (pas
        // seulement un index de traçabilité), combinée à match_id pour rester
        // portable (un hash pourrait en théorie se répéter entre deux matchs
        // différents si le contenu mappé était strictement identique).
        Schema::table('match_events', function (Blueprint $t) {
            $t->unique(['match_id', 'source_row_hash'], 'match_events_match_id_source_row_hash_unique');
        });

        Schema::table('import_batches', function (Blueprint $t) {
            $t->unsignedInteger('rows_read')->nullable();
            $t->unsignedInteger('rows_imported')->nullable();
            $t->unsignedInteger('rows_rejected')->nullable();
            $t->json('report')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('import_batches', function (Blueprint $t) {
            $t->dropColumn(['rows_read', 'rows_imported', 'rows_rejected', 'report']);
        });

        Schema::table('match_events', function (Blueprint $t) {
            $t->dropUnique('match_events_match_id_source_row_hash_unique');
            $t->dropColumn('source_row_hash');
        });

        foreach (['match_team_stats', 'player_match_detailed_stats', 'match_participations'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropIndex([$table . '_source_row_hash_index']);
                $t->dropColumn('source_row_hash');
            });
        }
    }
};
