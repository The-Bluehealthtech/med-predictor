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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('fifa_connect_id')->unique();
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('cascade');
            $table->foreignId('association_id')->nullable()->constrained('associations')->onDelete('cascade');
            $table->string('position')->nullable();
            $table->integer('jersey_number')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->integer('overall_rating')->nullable();
            $table->integer('potential_rating')->nullable();
            $table->integer('age')->nullable();
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->string('preferred_foot')->nullable();
            $table->string('work_rate')->nullable();
            $table->string('player_face_url')->nullable();
            $table->jsonb('medical_data_payload')->nullable();
            $table->string('gender')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
