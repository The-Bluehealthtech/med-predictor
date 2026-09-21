<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [
                'phone' => fn() => $table->string('phone')->nullable(),
                'entity_type' => fn() => $table->string('entity_type')->nullable(),
                'entity_id' => fn() => $table->unsignedBigInteger('entity_id')->nullable(),
                'club_id' => fn() => $table->unsignedBigInteger('club_id')->nullable(),
                'association_id' => fn() => $table->unsignedBigInteger('association_id')->nullable(),
                'team_id' => fn() => $table->unsignedBigInteger('team_id')->nullable(),
                'player_id' => fn() => $table->unsignedBigInteger('player_id')->nullable(),
                'fifa_connect_id' => fn() => $table->string('fifa_connect_id')->nullable(),
                'permissions' => fn() => $table->json('permissions')->nullable(),
                'preferences' => fn() => $table->json('preferences')->nullable(),
                'status' => fn() => $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('active'),
                'last_login_at' => fn() => $table->timestamp('last_login_at')->nullable(),
                'login_count' => fn() => $table->integer('login_count')->default(0),
                'timezone' => fn() => $table->string('timezone')->default('UTC'),
                'language' => fn() => $table->string('language')->default('fr'),
                'notifications_email' => fn() => $table->boolean('notifications_email')->default(true),
                'notifications_sms' => fn() => $table->boolean('notifications_sms')->default(false),
                'profile_picture_url' => fn() => $table->string('profile_picture_url')->nullable(),
                'profile_picture_alt' => fn() => $table->string('profile_picture_alt')->nullable(),
                'email_verified_at' => fn() => $table->timestamp('email_verified_at')->nullable(),
            ];
            foreach ($cols as $name => $add) {
                if (!Schema::hasColumn('users', $name)) {
                    $add();
                }
            }
        });
    }

    public function down(): void
    {
        // Volontairement vide : colonnes potentiellement partagées avec d'autres migrations
    }
};
