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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('stadium')->nullable();
            $table->string('stadium_name')->nullable();
            $table->integer('stadium_capacity')->nullable();
            $table->integer('founded_year')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('fifa_connect_id')->nullable();
            $table->string('league')->nullable();
            $table->string('division')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->decimal('budget_eur', 15, 2)->nullable();
            $table->decimal('wage_budget_eur', 15, 2)->nullable();
            $table->decimal('transfer_budget_eur', 15, 2)->nullable();
            $table->integer('reputation')->nullable();
            $table->integer('facilities_level')->nullable();
            $table->integer('youth_development')->nullable();
            $table->integer('scouting_network')->nullable();
            $table->integer('medical_team')->nullable();
            $table->integer('coaching_staff')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();

            $table->index(['name', 'country']);
            $table->index(['fifa_connect_id']);
            $table->index(['league', 'division']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
