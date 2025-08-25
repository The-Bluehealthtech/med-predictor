<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update users with legacy club associations to have direct club_id
        DB::statement("
            UPDATE users 
            SET club_id = entity_id 
            WHERE entity_type = 'club' 
            AND entity_id IS NOT NULL 
            AND club_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear club_id for users that were updated
        DB::statement("
            UPDATE users 
            SET club_id = NULL 
            WHERE entity_type = 'club' 
            AND entity_id IS NOT NULL
        ");
    }
};
