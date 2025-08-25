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
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->enum('metric_type', ['physical', 'technical', 'tactical', 'mental', 'medical', 'social']);
            $table->string('metric_name');
            $table->decimal('metric_value', 10, 4);
            $table->string('metric_unit', 50);
            $table->timestamp('measurement_date');
            $table->enum('data_source', ['manual', 'device', 'fifa_connect', 'hl7_fhir', 'itms', 'dtms']);
            $table->decimal('confidence_score', 3, 2)->default(1.00);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('fifa_connect_id')->nullable()->constrained('fifa_connect_ids')->onDelete('set null');
            $table->string('hl7_fhir_resource_id')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['player_id', 'metric_type']);
            $table->index(['player_id', 'measurement_date']);
            $table->index(['metric_type', 'measurement_date']);
            $table->index(['data_source', 'measurement_date']);
            $table->index(['is_verified', 'measurement_date']);
            $table->index('fifa_connect_id');
            $table->index('hl7_fhir_resource_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
    }
};
