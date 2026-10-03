<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Documents publiés par FIT sur le serveur FHIR (IHE MHD ITI-65) : IPS du passeport
 * médical (IHE sIPS). Une nouvelle publication remplace la précédente (relatesTo replaces).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fhir_documents')) {
            return;
        }
        Schema::create('fhir_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->string('kind', 20)->default('ips');
            $table->string('purpose', 30);
            $table->string('document_reference_id', 64);   // DocumentReference sur le serveur
            $table->string('master_identifier', 120);      // uniqueId (Bundle.identifier)
            $table->string('status', 12)->default('current'); // current | superseded
            $table->foreignId('replaces_id')->nullable()->constrained('fhir_documents')->nullOnDelete();
            $table->foreignId('attestation_id')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at');
            $table->timestamps();
            $table->index(['player_id', 'kind', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fhir_documents');
    }
};
