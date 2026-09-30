<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Livrable 1, §3 : table normalisée (une ligne par équipe par match),
     * pour permettre un contrôle générique "somme des stats joueurs == stats
     * équipe" sans distinguer home/away à chaque requête. Les colonnes
     * home_*/away_* existantes de "matches" ne sont pas touchées ni dépréciées
     * ici (dépréciation à discuter séparément, cf. Livrable 1 §3).
     */
    public function up(): void
    {
        Schema::create('match_team_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->boolean('is_home');
            $table->decimal('possession_pct', 5, 2)->nullable();
            $table->unsignedSmallInteger('shots_total')->nullable();
            $table->unsignedSmallInteger('shots_on_target')->nullable();
            $table->unsignedSmallInteger('corners')->nullable();
            $table->unsignedSmallInteger('fouls')->nullable();
            $table->unsignedSmallInteger('offsides')->nullable();
            $table->unsignedSmallInteger('yellow_cards')->nullable();
            $table->unsignedSmallInteger('red_cards')->nullable();
            $table->decimal('expected_goals', 6, 3)->nullable();
            $table->unsignedSmallInteger('goals_scored')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->timestamps();

            $table->unique(['match_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_team_stats');
    }
};
