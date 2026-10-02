<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Journal d'activité de la plateforme (tableau de bord général) : une ligne par
 * action d'un utilisateur connecté, rattachée à un domaine (clinique,
 * performance, sélections, administration). Aucun contenu médical : seulement
 * le type d'action, l'objet (type + identifiant) et le périmètre (club, fédération).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('platform_activities')) {
            return;
        }
        Schema::create('platform_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('domain', 20);
            $table->string('action', 80);
            $table->string('subject_type', 120)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('club_id')->nullable();
            $table->unsignedBigInteger('association_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['domain', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_activities');
    }
};
