<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('license_integrity_reviews')) {
            return;
        }

        Schema::create('license_integrity_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_license_id')->constrained('player_licenses')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('photo_status', 20)->default('insufficient');
            $table->string('signature_status', 20)->default('insufficient');
            $table->string('identity_status', 20)->default('insufficient');
            $table->string('age_status', 20)->default('insufficient');
            $table->json('evidence')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('reviewed_at')->useCurrent();
            $table->timestamps();
            $table->index(['player_license_id', 'reviewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_integrity_reviews');
    }
};
