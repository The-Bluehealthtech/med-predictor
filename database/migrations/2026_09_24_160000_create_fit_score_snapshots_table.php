<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fit_score_snapshots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('player_id')
                ->constrained('players')
                ->onDelete('cascade');

            $table->timestamp('snapshot_at');

            $table->decimal('physical_score', 5, 2)->nullable();
            $table->decimal('technical_score', 5, 2)->nullable();
            $table->decimal('tactical_score', 5, 2)->nullable();
            $table->decimal('mental_score', 5, 2)->nullable();
            $table->decimal('social_score', 5, 2)->nullable();

            $table->decimal('fit_score', 5, 2)->nullable();

            // 0.0000 à 1.0000
            $table->decimal('confidence_score', 5, 4)->nullable();

            $table->boolean('is_complete')->default(false);

            $table->unsignedSmallInteger('window_days')->default(30);

            // Permet de comparer uniquement des snapshots calculés
            // avec exactement les mêmes règles.
            $table->string('calculation_version', 32);

            // Traçabilité des métriques réellement utilisées.
            $table->json('evidence')->nullable();

            // Nullable : aucun faux utilisateur système.
            $table->foreignId('generated_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['player_id', 'snapshot_at']);
            $table->index(['player_id', 'is_complete', 'snapshot_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fit_score_snapshots');
    }
};
