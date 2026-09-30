<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Livrable 1, §4 : colonnes pour les passes (destinataire, zones,
     * succès), en plus des colonnes déjà existantes (type, event_type,
     * assisted_by_player_id, substituted_player_id...) qui restent inchangées.
     * La journalisation passe-par-passe elle-même reste une extension
     * optionnelle du Livrable 2, pas un prérequis ici (cf. hypothèse §4).
     * Index ajoutés dès la création pour anticiper le volume (300-600
     * passes/match si la donnée passe-par-passe est un jour importée).
     */
    public function up(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            $table->foreignId('recipient_player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->string('origin_zone', 4)->nullable();
            $table->string('destination_zone', 4)->nullable();
            $table->boolean('is_successful')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
        });

        Schema::table('match_events', function (Blueprint $table) {
            $table->index(['match_id', 'type']);
            $table->index(['player_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            $table->dropIndex(['match_id', 'type']);
            $table->dropIndex(['player_id', 'type']);
            $table->dropConstrainedForeignId('import_batch_id');
            $table->dropColumn(['is_demo', 'is_successful', 'destination_zone', 'origin_zone']);
            $table->dropConstrainedForeignId('recipient_player_id');
        });
    }
};
