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
        // Ajouter visit_id à la table health_records
        Schema::table('health_records', function (Blueprint $table) {
            $table->foreignId('visit_id')->nullable()->after('player_id')->constrained('visits')->onDelete('set null');
        });

        // Ajouter visit_id à la table pcmas
        Schema::table('pcmas', function (Blueprint $table) {
            $table->foreignId('visit_id')->nullable()->after('athlete_id')->constrained('visits')->onDelete('set null');
        });

        // Ajouter visit_id à la table injuries (si elle existe)
        if (Schema::hasTable('injuries')) {
            Schema::table('injuries', function (Blueprint $table) {
                $table->foreignId('visit_id')->nullable()->after('athlete_id')->constrained('visits')->onDelete('set null');
            });
        }

        // Ajouter visit_id à la table scat_assessments (si elle existe)
        if (Schema::hasTable('scat_assessments')) {
            Schema::table('scat_assessments', function (Blueprint $table) {
                $table->foreignId('visit_id')->nullable()->after('athlete_id')->constrained('visits')->onDelete('set null');
            });
        }

        // Ajouter visit_id à la table medical_notes (si elle existe)
        if (Schema::hasTable('medical_notes')) {
            Schema::table('medical_notes', function (Blueprint $table) {
                $table->foreignId('visit_id')->nullable()->after('athlete_id')->constrained('visits')->onDelete('set null');
            });
        }

        // Ajouter visit_id à la table documents (si elle existe)
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->foreignId('visit_id')->nullable()->after('athlete_id')->constrained('visits')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer visit_id de toutes les tables
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropColumn('visit_id');
        });

        Schema::table('pcmas', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropColumn('visit_id');
        });

        if (Schema::hasTable('injuries')) {
            Schema::table('injuries', function (Blueprint $table) {
                $table->dropForeign(['visit_id']);
                $table->dropColumn('visit_id');
            });
        }

        if (Schema::hasTable('scat_assessments')) {
            Schema::table('scat_assessments', function (Blueprint $table) {
                $table->dropForeign(['visit_id']);
                $table->dropColumn('visit_id');
            });
        }

        if (Schema::hasTable('medical_notes')) {
            Schema::table('medical_notes', function (Blueprint $table) {
                $table->dropForeign(['visit_id']);
                $table->dropColumn('visit_id');
            });
        }

        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropForeign(['visit_id']);
                $table->dropColumn('visit_id');
            });
        }
    }
};
