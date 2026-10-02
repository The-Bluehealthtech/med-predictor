<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Relance la conversion des licences de démonstration (2026_10_03_140000) :
 * sa première version paginait par décalage et sautait une partie des lignes.
 * Rejouable : seules les licences encore sans type d'enregistrement sont traitées.
 */
return new class extends Migration
{
    public function up(): void
    {
        (require database_path('migrations/2026_10_03_140000_convert_demo_licenses_to_fifa_connect.php'))->up();
    }

    public function down(): void
    {
    }
};
