<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fit_score_snapshots', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->nullable()
                ->after('player_id')
                ->constrained('tenants')
                ->nullOnDelete();
        });

        // Backfill déterministe depuis le joueur propriétaire,
        // sans inventer de tenant pour les joueurs legacy.
        DB::table('fit_score_snapshots')
            ->select(['id', 'player_id'])
            ->orderBy('id')
            ->chunkById(100, function ($snapshots) {
                $tenantIds = DB::table('players')
                    ->whereIn('id', $snapshots->pluck('player_id'))
                    ->pluck('tenant_id', 'id');

                foreach ($snapshots as $snapshot) {
                    $tenantId = $tenantIds[$snapshot->player_id] ?? null;

                    if ($tenantId !== null) {
                        DB::table('fit_score_snapshots')
                            ->where('id', $snapshot->id)
                            ->update(['tenant_id' => $tenantId]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('fit_score_snapshots', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};
