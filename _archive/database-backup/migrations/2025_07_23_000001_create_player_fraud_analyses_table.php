<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('player_fraud_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_license_id')->constrained()->onDelete('cascade');
            $table->float('identity_score')->nullable();
            $table->float('age_score')->nullable();
            $table->json('anomalies')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, quarantined, etc.
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('player_fraud_analyses');
    }
}; 