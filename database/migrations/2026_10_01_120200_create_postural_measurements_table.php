<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postural_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postural_assessment_id')->constrained('postural_assessments')->cascadeOnDelete();
            $table->string('view', 24);
            $table->string('measurement_type', 32);
            $table->string('measurement_key', 80)->nullable();
            $table->string('anatomical_region', 40)->nullable();
            $table->string('side', 24)->nullable();
            $table->decimal('value', 10, 3)->nullable();
            $table->string('unit', 16)->nullable();
            $table->json('points')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('postural_assessment_id');
            $table->index(['postural_assessment_id', 'measurement_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postural_measurements');
    }
};
