<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postural_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postural_assessment_id')->constrained('postural_assessments')->cascadeOnDelete();
            $table->string('view', 24);
            $table->string('region', 40);
            $table->string('finding_key', 80);
            $table->string('side', 24)->default('not_applicable');
            $table->string('severity', 24)->nullable();
            $table->decimal('value', 10, 3)->nullable();
            $table->string('unit', 16)->nullable();
            $table->string('source', 32)->default('clinician');
            $table->decimal('confidence', 5, 4)->nullable();
            $table->json('source_metadata')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('postural_assessment_id');
            $table->index(['postural_assessment_id', 'region']);
            $table->index(['region', 'finding_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postural_findings');
    }
};
