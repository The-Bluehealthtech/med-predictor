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
        // Add tenant_id to users table
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // Add tenant_id to associations table
        if (Schema::hasTable('associations') && !Schema::hasColumn('associations', 'tenant_id')) {
            Schema::table('associations', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // Add tenant_id to clubs table
        if (Schema::hasTable('clubs') && !Schema::hasColumn('clubs', 'tenant_id')) {
            Schema::table('clubs', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // Add tenant_id to federations table
        if (Schema::hasTable('federations') && !Schema::hasColumn('federations', 'tenant_id')) {
            Schema::table('federations', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // Add tenant_id to competitions table
        if (Schema::hasTable('competitions') && !Schema::hasColumn('competitions', 'tenant_id')) {
            Schema::table('competitions', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // Add tenant_id to teams table
        if (Schema::hasTable('teams') && !Schema::hasColumn('teams', 'tenant_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // Add tenant_id to players table
        if (Schema::hasTable('players') && !Schema::hasColumn('players', 'tenant_id')) {
            Schema::table('players', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // Add tenant_id to matches table (GameMatch)
        if (Schema::hasTable('matches') && !Schema::hasColumn('matches', 'tenant_id')) {
            Schema::table('matches', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }

        // Add tenant_id to roles table
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'tenant_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                $table->index('tenant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove tenant_id from all tables
        $tables = ['users', 'associations', 'clubs', 'federations', 'competitions', 'teams', 'players', 'matches', 'roles'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};






