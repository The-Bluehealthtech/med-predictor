<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Outil DTN : partage de données entre un club et la Direction technique
 * nationale autour de la sélection d'un joueur.
 *
 *  - national_selections : une convocation (joueur, club, fédération,
 *    rassemblement, dates) et son cycle de vie
 *    convoqué → état de départ envoyé → en sélection → état de retour envoyé → clôturé.
 *  - national_selection_reports : l'état de départ (club → DTN) et l'état de
 *    retour (DTN → club). La partie médicale est isolée dans une colonne à
 *    part, servie uniquement aux rôles médicaux.
 *
 * Idempotente : appliquée au démarrage par fit:deploy, elle ne recrée rien
 * si les tables existent déjà.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('national_selections')) {
            Schema::create('national_selections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
                $table->foreignId('club_id')->nullable()->constrained('clubs')->nullOnDelete();
                $table->foreignId('association_id')->nullable()->constrained('associations')->nullOnDelete();
                $table->string('team_label');
                $table->string('event_type', 20);
                $table->string('event_name');
                $table->string('opponent')->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status', 20)->default('convoked');
                $table->text('convocation_note')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_demo')->default(false);
                $table->timestamps();
                $table->index(['club_id', 'status']);
                $table->index(['association_id', 'status']);
            });
        }

        if (!Schema::hasTable('national_selection_reports')) {
            Schema::create('national_selection_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('national_selection_id')->constrained('national_selections')->cascadeOnDelete();
                $table->string('direction', 10);
                $table->string('status', 15)->default('draft');
                $table->string('fitness_status', 30)->nullable();
                $table->json('snapshot')->nullable();
                $table->json('content')->nullable();
                $table->json('medical')->nullable();
                $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('medical_author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('sent_at')->nullable();
                $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('acknowledged_at')->nullable();
                $table->timestamps();
                $table->unique(['national_selection_id', 'direction']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('national_selection_reports');
        Schema::dropIfExists('national_selections');
    }
};
