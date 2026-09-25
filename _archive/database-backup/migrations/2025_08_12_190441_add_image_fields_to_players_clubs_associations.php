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
        // Ajouter des champs d'images aux joueurs
        Schema::table('players', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('player_picture');
            $table->string('flag_image')->nullable()->after('nation_flag_url');
        });

        // Ajouter des champs d'images aux clubs
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('logo_image')->nullable()->after('logo_url');
            $table->string('stadium_image')->nullable()->after('home_ground');
        });

        // Ajouter des champs d'images aux associations
        Schema::table('associations', function (Blueprint $table) {
            $table->string('logo_image')->nullable()->after('logo_url');
            $table->string('flag_image')->nullable()->after('logo_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['profile_image', 'flag_image']);
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn(['logo_image', 'stadium_image']);
        });

        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn(['logo_image', 'flag_image']);
        });
    }
};
