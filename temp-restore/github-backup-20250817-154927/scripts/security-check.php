<?php

/**
 * Script de vérification de sécurité pour le module de gestion des licences
 * Usage: php scripts/security-check.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SecurityChecker
{
    private array $issues = [];
    private array $warnings = [];
    private array $success = [];

    public function run(): void
    {
        echo "🔒 Vérification de sécurité du module de gestion des licences\n";
        echo "========================================================\n\n";

        $this->checkEnvironment();
        $this->checkDatabaseSecurity();
        $this->checkFilePermissions();
        $this->checkLogFiles();
        $this->checkRateLimiting();
        $this->checkUserPermissions();
        $this->checkLicenseData();

        $this->printReport();
    }

    private function checkEnvironment(): void
    {
        echo "📋 Vérification de l'environnement...\n";

        // Vérifier APP_DEBUG
        if (config('app.debug')) {
            $this->warnings[] = "APP_DEBUG est activé en production";
        } else {
            $this->success[] = "APP_DEBUG est désactivé";
        }

        // Vérifier HTTPS
        if (request()->isSecure()) {
            $this->success[] = "HTTPS est activé";
        } else {
            $this->warnings[] = "HTTPS n'est pas activé";
        }

        // Vérifier les variables d'environnement sensibles
        $sensitiveVars = ['APP_KEY', 'DB_PASSWORD', 'MAIL_PASSWORD'];
        foreach ($sensitiveVars as $var) {
            if (env($var) && env($var) !== '') {
                $this->success[] = "Variable $var est configurée";
            } else {
                $this->issues[] = "Variable $var manquante ou vide";
            }
        }
    }

    private function checkDatabaseSecurity(): void
    {
        echo "🗄️ Vérification de la base de données...\n";

        try {
            // Vérifier les contraintes de sécurité
            $tables = ['users', 'player_licenses', 'players', 'clubs'];
            
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $this->success[] = "Table $table existe";
                    
                    // Vérifier les colonnes sensibles
                    $columns = DB::getSchemaBuilder()->getColumnListing($table);
                    if (in_array('password', $columns)) {
                        $this->success[] = "Colonne password présente dans $table";
                    }
                } else {
                    $this->issues[] = "Table $table manquante";
                }
            }

            // Vérifier les index de sécurité
            $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_email_unique'");
            if (!empty($indexes)) {
                $this->success[] = "Index unique sur email des utilisateurs";
            } else {
                $this->warnings[] = "Index unique manquant sur email des utilisateurs";
            }

        } catch (Exception $e) {
            $this->issues[] = "Erreur de connexion à la base de données: " . $e->getMessage();
        }
    }

    private function checkFilePermissions(): void
    {
        echo "📁 Vérification des permissions de fichiers...\n";

        $paths = [
            'storage/logs' => 0755,
            'storage/app' => 0755,
            'storage/framework' => 0755,
            '.env' => 0644,
        ];

        foreach ($paths as $path => $expectedPerms) {
            $fullPath = base_path($path);
            if (file_exists($fullPath)) {
                $perms = fileperms($fullPath) & 0777;
                if ($perms === $expectedPerms) {
                    $this->success[] = "Permissions correctes pour $path";
                } else {
                    $this->warnings[] = "Permissions incorrectes pour $path (actuel: " . decoct($perms) . ", attendu: " . decoct($expectedPerms) . ")";
                }
            } else {
                $this->issues[] = "Chemin $path n'existe pas";
            }
        }
    }

    private function checkLogFiles(): void
    {
        echo "📝 Vérification des fichiers de logs...\n";

        $logFiles = [
            'storage/logs/laravel.log',
            'storage/logs/security.log',
            'storage/logs/licenses.log',
            'storage/logs/files.log',
        ];

        foreach ($logFiles as $logFile) {
            $fullPath = base_path($logFile);
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                if ($size > 50 * 1024 * 1024) { // 50MB
                    $this->warnings[] = "Fichier de log $logFile trop volumineux (" . round($size / 1024 / 1024, 2) . "MB)";
                } else {
                    $this->success[] = "Fichier de log $logFile OK";
                }
            } else {
                $this->success[] = "Fichier de log $logFile n'existe pas encore (normal)";
            }
        }
    }

    private function checkRateLimiting(): void
    {
        echo "⚡ Vérification du rate limiting...\n";

        // Vérifier que le cache Redis/Memcached est configuré
        $cacheDriver = config('cache.default');
        if (in_array($cacheDriver, ['redis', 'memcached'])) {
            $this->success[] = "Cache $cacheDriver configuré pour le rate limiting";
        } else {
            $this->warnings[] = "Cache file utilisé pour le rate limiting (moins performant)";
        }
    }

    private function checkUserPermissions(): void
    {
        echo "👥 Vérification des permissions utilisateur...\n";

        try {
            // Vérifier les rôles utilisateur
            $roles = DB::table('users')->distinct()->pluck('role')->toArray();
            $expectedRoles = ['admin', 'license_agent', 'captain', 'player'];
            
            foreach ($expectedRoles as $role) {
                if (in_array($role, $roles)) {
                    $this->success[] = "Rôle $role présent";
                } else {
                    $this->warnings[] = "Rôle $role manquant";
                }
            }

            // Vérifier les utilisateurs sans rôle
            $usersWithoutRole = DB::table('users')->whereNull('role')->orWhere('role', '')->count();
            if ($usersWithoutRole > 0) {
                $this->issues[] = "$usersWithoutRole utilisateur(s) sans rôle défini";
            } else {
                $this->success[] = "Tous les utilisateurs ont un rôle défini";
            }

        } catch (Exception $e) {
            $this->issues[] = "Erreur lors de la vérification des permissions: " . $e->getMessage();
        }
    }

    private function checkLicenseData(): void
    {
        echo "📋 Vérification des données de licences...\n";

        try {
            // Vérifier les licences sans statut
            $licensesWithoutStatus = DB::table('player_licenses')->whereNull('status')->count();
            if ($licensesWithoutStatus > 0) {
                $this->issues[] = "$licensesWithoutStatus licence(s) sans statut";
            } else {
                $this->success[] = "Toutes les licences ont un statut";
            }

            // Vérifier les licences orphelines (sans joueur)
            $orphanedLicenses = DB::table('player_licenses')
                ->leftJoin('players', 'player_licenses.player_id', '=', 'players.id')
                ->whereNull('players.id')
                ->count();
            
            if ($orphanedLicenses > 0) {
                $this->issues[] = "$orphanedLicenses licence(s) orpheline(s)";
            } else {
                $this->success[] = "Aucune licence orpheline";
            }

        } catch (Exception $e) {
            $this->issues[] = "Erreur lors de la vérification des licences: " . $e->getMessage();
        }
    }

    private function printReport(): void
    {
        echo "\n📊 RAPPORT DE SÉCURITÉ\n";
        echo "======================\n\n";

        if (!empty($this->success)) {
            echo "✅ SUCCÈS (" . count($this->success) . "):\n";
            foreach ($this->success as $success) {
                echo "  ✓ $success\n";
            }
            echo "\n";
        }

        if (!empty($this->warnings)) {
            echo "⚠️ AVERTISSEMENTS (" . count($this->warnings) . "):\n";
            foreach ($this->warnings as $warning) {
                echo "  ⚠ $warning\n";
            }
            echo "\n";
        }

        if (!empty($this->issues)) {
            echo "❌ PROBLÈMES CRITIQUES (" . count($this->issues) . "):\n";
            foreach ($this->issues as $issue) {
                echo "  ✗ $issue\n";
            }
            echo "\n";
        }

        $totalChecks = count($this->success) + count($this->warnings) + count($this->issues);
        $successRate = $totalChecks > 0 ? round((count($this->success) / $totalChecks) * 100, 1) : 0;

        echo "📈 RÉSUMÉ:\n";
        echo "  - Succès: " . count($this->success) . "\n";
        echo "  - Avertissements: " . count($this->warnings) . "\n";
        echo "  - Problèmes: " . count($this->issues) . "\n";
        echo "  - Taux de succès: $successRate%\n\n";

        if (empty($this->issues)) {
            echo "🎉 Aucun problème critique détecté!\n";
        } else {
            echo "🔧 Veuillez corriger les problèmes critiques avant la mise en production.\n";
        }
    }
}

// Lancer la vérification
$checker = new SecurityChecker();
$checker->run(); 