<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Identité clinique du joueur sur le serveur FHIR de FIT (IHE PIXm / PDQm) : le Patient
 * alimenté par FIT (role fit) et les Patients des sources externes rattachés après
 * confirmation humaine (role external, status linked) ou écartés (status rejected).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fhir_patient_links')) {
            return;
        }
        Schema::create('fhir_patient_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->string('role', 10);                    // fit | external
            $table->string('patient_id', 64)->nullable();  // id logique sur le serveur FHIR
            $table->string('status', 10)->default('linked'); // linked | rejected
            $table->string('matched_on', 20)->nullable();  // fifa_id | demographics
            $table->json('snapshot')->nullable();          // identité du candidat au moment de la décision
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->string('sync_error', 500)->nullable();
            $table->timestamps();
            $table->unique(['player_id', 'role', 'patient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fhir_patient_links');
    }
};
