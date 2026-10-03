<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('license_biometric_checks')) {
            return;
        }

        Schema::create('license_biometric_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_license_id')->constrained('player_licenses')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('capability', 32);
            $table->string('provider', 64);
            $table->string('source_reference', 64);
            $table->string('target_reference', 64);
            $table->decimal('score', 7, 4)->nullable();
            $table->decimal('threshold', 7, 4)->nullable();
            $table->string('status', 32);
            $table->json('metadata')->nullable();
            $table->timestamp('checked_at')->useCurrent();
            $table->timestamps();
            $table->index(['player_license_id', 'capability', 'checked_at'], 'lic_bio_license_capability_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_biometric_checks');
    }
};
