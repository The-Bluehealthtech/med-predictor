<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfederationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('confederations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('country')->nullable();
            $table->integer('fifa_ranking')->nullable();
            $table->string('fifa_version')->nullable();
            $table->string('fifa_sync_status')->default('pending');
            $table->timestamp('fifa_sync_date')->nullable();
            $table->text('fifa_last_error')->nullable();
            $table->string('confederation_logo_url')->nullable();
            $table->integer('founded_year')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confederations');
    }
}
