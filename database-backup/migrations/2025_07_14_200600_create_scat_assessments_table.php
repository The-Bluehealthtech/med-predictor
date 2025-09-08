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
        Schema::create('scat_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->foreignId('assessor_id')->constrained('users')->onDelete('cascade');
            $table->json('data_json');
            $table->enum('result', ['normal', 'abnormal', 'inconclusive'])->default('normal');
            $table->boolean('concussion_confirmed')->default(false);
            $table->timestamp('assessment_date');
            $table->enum('assessment_type', ['baseline', 'post_injury', 'follow_up'])->default('post_injury');
            $table->integer('scat_score')->nullable();
            $table->text('recommendations')->nullable();
            $table->json('fifa_concussion_data')->nullable();
            $table->timestamps();
            
            $table->index(['athlete_id']);
            $table->index(['assessor_id']);
            $table->index(['assessment_date']);
            $table->index(['result']);
            $table->index(['concussion_confirmed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scat_assessments');
    }
}; 