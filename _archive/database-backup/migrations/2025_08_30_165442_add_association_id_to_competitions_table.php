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
        Schema::table('competitions', function (Blueprint $table) {
            $table->unsignedBigInteger('association_id')->nullable()->after('id');
            $table->string('fifa_connect_id')->nullable()->after('association_id');
            $table->boolean('require_federation_license')->default(false)->after('fifa_connect_id');
            $table->boolean('fifa_sync_enabled')->default(false)->after('require_federation_license');
            $table->string('fifa_sync_status')->default('pending')->after('fifa_sync_enabled');
            $table->timestamp('fifa_sync_date')->nullable()->after('fifa_sync_status');
            $table->text('fifa_last_error')->nullable()->after('fifa_sync_date');
            
            // Ajouter la clé étrangère
            $table->foreign('association_id')->references('id')->on('associations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['association_id']);
            $table->dropColumn([
                'association_id',
                'fifa_connect_id',
                'require_federation_license',
                'fifa_sync_enabled',
                'fifa_sync_status',
                'fifa_sync_date',
                'fifa_last_error'
            ]);
        });
    }
};
