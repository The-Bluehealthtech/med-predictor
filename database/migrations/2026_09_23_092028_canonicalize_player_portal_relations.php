<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Ces trois tables utilisent le Player moderne,
         * pas l'ancien modèle Joueur.
         */
        $driver = DB::getDriverName();

        foreach ([
            'player_real_time_health',
            'player_sdoh_data',
            'player_connected_devices',
        ] as $tableName) {
            // SQLite cannot alter/drop existing foreign keys in place.
            // Test databases keep the existing column and skip only the
            // constraint rebinding; PostgreSQL production performs it.
            if ($driver === 'sqlite') {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['player_id']);
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('player_id')
                    ->references('id')
                    ->on('players')
                    ->cascadeOnDelete();
            });
        }

        /*
         * Bridge legacy Athlete -> Player.
         *
         * Un joueur synthétique peut avoir un Athlete local
         * sans qu'on invente un identifiant FIFA externe.
         */
        if (!Schema::hasColumn('athletes', 'player_id')) {
            Schema::table('athletes', function (Blueprint $table) {
                $table->foreignId('player_id')
                    ->nullable()
                    ->unique()
                    ->constrained('players')
                    ->nullOnDelete();
            });
        }

        /*
         * fifa_id doit rester un identifiant FIFA réel.
         * Les joueurs synthétiques peuvent donc avoir NULL.
         */
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE athletes ALTER COLUMN fifa_id DROP NOT NULL'
            );
        } else {
            Schema::table('athletes', function (Blueprint $table) {
                $table->string('fifa_id')->nullable()->change();
            });
        }

        /*
         * Injury devient directement adressable par Player
         * tout en conservant athlete_id pour le code legacy.
         */
        if (!Schema::hasColumn('injuries', 'player_id')) {
            Schema::table('injuries', function (Blueprint $table) {
                $table->foreignId('player_id')
                    ->nullable()
                    ->constrained('players')
                    ->nullOnDelete();

                $table->index(['player_id', 'date']);
            });
        }

        /*
         * Backfill non destructif pour d'éventuelles anciennes données
         * déjà reliées à un Athlete ayant un player_id.
         */
        foreach (['injuries', 'pcmas'] as $targetTable) {
            DB::table($targetTable)
                ->whereNull('player_id')
                ->whereNotNull('athlete_id')
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($targetTable) {
                    $athleteIds = $rows
                        ->pluck('athlete_id')
                        ->filter()
                        ->unique()
                        ->values();

                    $links = DB::table('athletes')
                        ->whereIn('id', $athleteIds)
                        ->whereNotNull('player_id')
                        ->pluck('player_id', 'id');

                    foreach ($rows as $row) {
                        $playerId = $links->get($row->athlete_id);

                        if ($playerId !== null) {
                            DB::table($targetTable)
                                ->where('id', $row->id)
                                ->update(['player_id' => $playerId]);
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        /*
         * Ne jamais fabriquer un FIFA ID pour permettre un rollback.
         */
        if (DB::table('athletes')->whereNull('fifa_id')->exists()) {
            throw new \RuntimeException(
                'Rollback impossible: certains athletes n’ont pas de fifa_id réel.'
            );
        }

        if (Schema::hasColumn('injuries', 'player_id')) {
            Schema::table('injuries', function (Blueprint $table) {
                $table->dropIndex(['player_id', 'date']);
                $table->dropConstrainedForeignId('player_id');
            });
        }

        if (Schema::hasColumn('athletes', 'player_id')) {
            Schema::table('athletes', function (Blueprint $table) {
                $table->dropConstrainedForeignId('player_id');
            });
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE athletes ALTER COLUMN fifa_id SET NOT NULL'
            );
        } else {
            Schema::table('athletes', function (Blueprint $table) {
                $table->string('fifa_id')->nullable(false)->change();
            });
        }

        foreach ([
            'player_real_time_health',
            'player_sdoh_data',
            'player_connected_devices',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['player_id']);
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('player_id')
                    ->references('id')
                    ->on('joueurs')
                    ->cascadeOnDelete();
            });
        }
    }
};
