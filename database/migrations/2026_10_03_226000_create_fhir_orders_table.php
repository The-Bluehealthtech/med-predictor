<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Prescriptions d'examens transmises au serveur FHIR (ServiceRequest) et suivi de leurs
 * comptes rendus (DiagnosticReport.basedOn).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fhir_orders')) {
            return;
        }
        Schema::create('fhir_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->string('module', 30);                       // laboratory | imaging | mri
            $table->string('service_request_id', 64)->nullable();
            $table->string('status', 20)->default('pending');   // pending | active | results_received | error
            $table->text('details')->nullable();
            $table->json('report_ids')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('results_at')->nullable();
            $table->string('error', 500)->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['visit_id', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fhir_orders');
    }
};
