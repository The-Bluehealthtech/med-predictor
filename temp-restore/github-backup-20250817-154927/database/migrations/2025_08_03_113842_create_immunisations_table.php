<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('immunisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->string('vaccine_code'); // CVX code
            $table->string('vaccine_name');
            $table->datetime('date_administered');
            $table->string('fhir_id')->nullable(); // FHIR resource ID
            $table->string('lot_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->date('expiration_date')->nullable();
            $table->integer('dose_number')->default(1);
            $table->integer('total_doses')->default(1);
            $table->string('route')->default('IM'); // IM, SC, ID, IN, PO
            $table->string('site')->default('LA'); // LA, RA, LD, RD, LG, RG, LVL, RVL
            $table->enum('status', ['active', 'expired', 'pending', 'incomplete'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('administered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('verification_date')->nullable();
            $table->enum('source', ['manual', 'fhir_sync', 'import'])->default('manual');
            $table->datetime('last_synced_at')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->default('pending');
            $table->text('sync_error')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['athlete_id', 'status']);
            $table->index(['athlete_id', 'date_administered']);
            $table->index(['expiration_date']);
            $table->index(['sync_status']);
            $table->index(['fhir_id']);
            $table->index(['vaccine_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunisations');
    }
};
