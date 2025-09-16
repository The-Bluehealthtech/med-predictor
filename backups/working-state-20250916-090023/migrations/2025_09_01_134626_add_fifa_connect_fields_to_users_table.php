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
        Schema::table('users', function (Blueprint $table) {
            // FIFA CONNECT required fields
            $table->string('phone')->nullable();
            $table->string('entity_type')->nullable(); // club, association
            $table->unsignedBigInteger('entity_id')->nullable(); // club_id or association_id
            $table->unsignedBigInteger('club_id')->nullable();
            $table->unsignedBigInteger('association_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('player_id')->nullable();
            $table->string('fifa_connect_id')->nullable();
            $table->json('permissions')->nullable();
            $table->json('preferences')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->integer('login_count')->default(0);
            $table->string('timezone')->default('UTC');
            $table->string('language')->default('fr');
            $table->boolean('notifications_email')->default(true);
            $table->boolean('notifications_sms')->default(false);
            $table->string('profile_picture_url')->nullable();
            $table->string('profile_picture_alt')->nullable();
            $table->timestamp('email_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'entity_type', 'entity_id', 'club_id', 'association_id', 
                'team_id', 'player_id', 'fifa_connect_id', 'permissions', 'preferences',
                'status', 'last_login_at', 'login_count', 'timezone', 'language',
                'notifications_email', 'notifications_sms', 'profile_picture_url',
                'profile_picture_alt', 'email_verified_at'
            ]);
        });
    }
};
