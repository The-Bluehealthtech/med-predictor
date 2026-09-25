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
        Schema::create('fifa_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->integer('status_code')->nullable();
            $table->text('error_message')->nullable();
            $table->string('operation_type')->default('sync'); // sync, webhook, manual
            $table->string('fifa_endpoint')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['entity_type', 'entity_id']);
            $table->index('status_code');
            $table->index('operation_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fifa_sync_logs');
    }
};
