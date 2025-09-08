<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Forcer l'encodage UTF-8 sur toutes les connexions MySQL
        try {
            $connection = DB::connection();
            if ($connection->getDriverName() === 'mysql') {
                $connection->statement("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
                $connection->statement("SET CHARACTER SET utf8mb4");
                $connection->statement("SET character_set_connection=utf8mb4");
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs de connexion au démarrage
        }
    }
}
