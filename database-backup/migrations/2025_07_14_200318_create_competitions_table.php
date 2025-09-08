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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('type')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('season')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('registration_deadline')->nullable();
            $table->integer('max_teams')->nullable();
            $table->integer('min_teams')->nullable();
            $table->string('format')->nullable();
            $table->decimal('prize_pool', 15, 2)->nullable();
            $table->decimal('entry_fee', 15, 2)->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->text('rules')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('organizer')->nullable();
            $table->string('sponsors')->nullable();
            $table->string('broadcast_partners')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
