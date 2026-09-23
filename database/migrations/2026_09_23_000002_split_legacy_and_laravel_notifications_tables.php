<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // L'ancienne table "notifications" est une table métier incompatible
        // avec le système natif Illuminate Notifications.
        if (
            Schema::hasTable('notifications') &&
            !Schema::hasColumn('notifications', 'notifiable_type')
        ) {
            if (Schema::hasTable('legacy_notifications')) {
                throw new \RuntimeException(
                    'legacy_notifications existe déjà; migration interrompue pour éviter une perte de données.'
                );
            }

            // Créer d'abord sous un nom temporaire afin d'éviter les collisions
            // de noms d'index PostgreSQL après le renommage de l'ancienne table.
            if (!Schema::hasTable('laravel_notifications')) {
                $this->createLaravelNotificationsTable('laravel_notifications');
            }

            Schema::rename('notifications', 'legacy_notifications');
            Schema::rename('laravel_notifications', 'notifications');

            return;
        }

        // Cas d'une installation où aucune table notifications n'existe.
        if (!Schema::hasTable('notifications')) {
            $this->createLaravelNotificationsTable('notifications');
        }
    }

    public function down(): void
    {
        // On ne restaure l'ancienne table que si cette migration l'avait préservée.
        if (Schema::hasTable('legacy_notifications')) {
            Schema::dropIfExists('notifications');
            Schema::rename('legacy_notifications', 'notifications');
        }
    }

    private function createLaravelNotificationsTable(string $tableName): void
    {
        Schema::create($tableName, function (Blueprint $table) use ($tableName) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(
                ['notifiable_type', 'notifiable_id'],
                $tableName . '_notifiable_index'
            );
        });
    }
};
