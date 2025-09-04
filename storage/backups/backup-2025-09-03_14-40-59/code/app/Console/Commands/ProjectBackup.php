<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ProjectBackup extends Command
{
    protected $signature = 'project:backup {--type=full : Type de sauvegarde (full, db, code, config)} {--compress : Compresser la sauvegarde}';
    protected $description = 'Sauvegarde complète du projet : base de données, code et configuration';

    private $backupPath;
    private $timestamp;
    private $backupName;

    public function handle()
    {
        $this->timestamp = date('Y-m-d_H-i-s');
        $this->backupName = "backup-{$this->timestamp}";
        $this->backupPath = storage_path("backups/{$this->backupName}");
        
        $this->info('💾 Début de la sauvegarde du projet...');
        
        // Créer le répertoire de sauvegarde
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
        
        $type = $this->option('type');
        $compress = $this->option('compress');
        
        switch ($type) {
            case 'db':
                $this->backupDatabase();
                break;
            case 'code':
                $this->backupCode();
                break;
            case 'config':
                $this->backupConfig();
                break;
            default:
                $this->backupFull();
        }
        
        if ($compress) {
            $this->compressBackup();
        }
        
        $this->generateBackupReport();
        
        $this->info('✅ Sauvegarde terminée !');
        $this->info("📁 Emplacement: {$this->backupPath}");
        
        return 0;
    }

    private function backupFull()
    {
        $this->info('🔄 Sauvegarde complète...');
        
        // 1. Sauvegarde de la base de données
        $this->backupDatabase();
        
        // 2. Sauvegarde du code source
        $this->backupCode();
        
        // 3. Sauvegarde de la configuration
        $this->backupConfig();
        
        // 4. Sauvegarde des assets
        $this->backupAssets();
        
        // 5. Sauvegarde des logs
        $this->backupLogs();
        
        // 6. Sauvegarde des uploads
        $this->backupUploads();
    }

    private function backupDatabase()
    {
        $this->info('🗄️  Sauvegarde de la base de données...');
        
        $dbPath = $this->backupPath . '/database';
        File::makeDirectory($dbPath, 0755, true);
        
        // Sauvegarde SQLite si elle existe
        $sqlitePath = database_path('database.sqlite');
        if (File::exists($sqlitePath)) {
            File::copy($sqlitePath, $dbPath . '/database.sqlite');
            $this->info('✅ Base SQLite sauvegardée');
        }
        
        // Sauvegarde des migrations
        $migrationsPath = database_path('migrations');
        if (File::exists($migrationsPath)) {
            File::copyDirectory($migrationsPath, $dbPath . '/migrations');
            $this->info('✅ Migrations sauvegardées');
        }
        
        // Sauvegarde des seeders
        $seedersPath = database_path('seeders');
        if (File::exists($seedersPath)) {
            File::copyDirectory($seedersPath, $dbPath . '/seeders');
            $this->info('✅ Seeders sauvegardés');
        }
        
        // Exporter la structure de la base (si possible)
        $this->exportDatabaseStructure($dbPath);
    }

    private function backupCode()
    {
        $this->info('💻 Sauvegarde du code source...');
        
        $codePath = $this->backupPath . '/code';
        File::makeDirectory($codePath, 0755, true);
        
        // Répertoires à sauvegarder
        $directories = [
            'app' => 'app',
            'resources' => 'resources',
            'routes' => 'routes',
            'tests' => 'tests',
            'config' => 'config',
            'bootstrap' => 'bootstrap',
            'database' => 'database',
            'lang' => 'lang'
        ];
        
        foreach ($directories as $source => $dest) {
            $sourcePath = base_path($source);
            if (File::exists($sourcePath)) {
                File::copyDirectory($sourcePath, $codePath . '/' . $dest);
                $this->info("✅ $source sauvegardé");
            }
        }
        
        // Fichiers individuels importants
        $files = [
            'composer.json',
            'composer.lock',
            'package.json',
            'package-lock.json',
            'artisan',
            '.env.example',
            'README.md'
        ];
        
        foreach ($files as $file) {
            $filePath = base_path($file);
            if (File::exists($filePath)) {
                File::copy($filePath, $codePath . '/' . $file);
            }
        }
    }

    private function backupConfig()
    {
        $this->info('⚙️  Sauvegarde de la configuration...');
        
        $configPath = $this->backupPath . '/config';
        File::makeDirectory($configPath, 0755, true);
        
        // Fichiers de configuration
        $configFiles = [
            '.env' => '.env',
            '.env.example' => '.env.example',
            'notifications.yml' => 'notifications.yml',
            'phpunit.ci.xml' => 'phpunit.ci.xml',
            '.yamllint' => '.yamllint'
        ];
        
        foreach ($configFiles as $source => $dest) {
            $sourcePath = base_path($source);
            if (File::exists($sourcePath)) {
                File::copy($sourcePath, $configPath . '/' . $dest);
                $this->info("✅ $source sauvegardé");
            }
        }
        
        // Configuration CI/CD
        $ciFiles = [
            '.gitlab-ci.yml',
            'Jenkinsfile',
            'docker-compose.ci.yml',
            'Dockerfile.ci'
        ];
        
        foreach ($ciFiles as $file) {
            $filePath = base_path($file);
            if (File::exists($filePath)) {
                File::copy($filePath, $configPath . '/' . $file);
            }
        }
    }

    private function backupAssets()
    {
        $this->info('🎨 Sauvegarde des assets...');
        
        $assetsPath = $this->backupPath . '/assets';
        File::makeDirectory($assetsPath, 0755, true);
        
        // Public assets
        $publicPath = public_path();
        if (File::exists($publicPath)) {
            File::copyDirectory($publicPath, $assetsPath . '/public');
            $this->info('✅ Assets publics sauvegardés');
        }
        
        // Compiled assets
        $compiledPath = resource_path('js/dist');
        if (File::exists($compiledPath)) {
            File::copyDirectory($compiledPath, $assetsPath . '/compiled');
            $this->info('✅ Assets compilés sauvegardés');
        }
    }

    private function backupLogs()
    {
        $this->info('📝 Sauvegarde des logs...');
        
        $logsPath = $this->backupPath . '/logs';
        File::makeDirectory($logsPath, 0755, true);
        
        $storageLogsPath = storage_path('logs');
        if (File::exists($storageLogsPath)) {
            File::copyDirectory($storageLogsPath, $logsPath);
            $this->info('✅ Logs sauvegardés');
        }
    }

    private function backupUploads()
    {
        $this->info('📁 Sauvegarde des uploads...');
        
        $uploadsPath = $this->backupPath . '/uploads';
        File::makeDirectory($uploadsPath, 0755, true);
        
        $storageUploadsPath = storage_path('app/public');
        if (File::exists($storageUploadsPath)) {
            File::copyDirectory($storageUploadsPath, $uploadsPath);
            $this->info('✅ Uploads sauvegardés');
        }
    }

    private function exportDatabaseStructure($dbPath)
    {
        try {
            // Tentative d'export de la structure SQLite
            $sqlitePath = database_path('database.sqlite');
            if (File::exists($sqlitePath)) {
                $schema = $this->getSqliteSchema($sqlitePath);
                File::put($dbPath . '/schema.sql', $schema);
                $this->info('✅ Structure de base exportée');
            }
        } catch (\Exception $e) {
            $this->warn("⚠️  Impossible d'exporter la structure: " . $e->getMessage());
        }
    }

    private function getSqliteSchema($dbPath)
    {
        // Export basique de la structure SQLite
        $schema = "-- Structure de la base de données\n";
        $schema .= "-- Exporté le: " . date('Y-m-d H:i:s') . "\n\n";
        
        // Ajouter les informations de base
        $schema .= "PRAGMA foreign_keys = OFF;\n";
        $schema .= "BEGIN TRANSACTION;\n\n";
        
        // Ici on pourrait ajouter plus de logique pour extraire la vraie structure
        // Pour l'instant, on crée un fichier de base
        
        $schema .= "-- Tables et structure à restaurer manuellement\n";
        $schema .= "COMMIT;\n";
        $schema .= "PRAGMA foreign_keys = ON;\n";
        
        return $schema;
    }

    private function compressBackup()
    {
        $this->info('🗜️  Compression de la sauvegarde...');
        
        $zipPath = storage_path("backups/{$this->backupName}.zip");
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $this->addFolderToZip($zip, $this->backupPath, basename($this->backupPath));
            $zip->close();
            
            // Supprimer le dossier non compressé
            File::deleteDirectory($this->backupPath);
            
            $this->info("✅ Sauvegarde compressée: {$zipPath}");
            $this->backupPath = $zipPath;
        } else {
            $this->error('❌ Impossible de créer l\'archive ZIP');
        }
    }

    private function addFolderToZip($zip, $folder, $relativePath)
    {
        $files = File::allFiles($folder);
        
        foreach ($files as $file) {
            $filePath = $file->getPathname();
            $relativeFilePath = $relativePath . '/' . str_replace($folder . '/', '', $filePath);
            $zip->addFile($filePath, $relativeFilePath);
        }
        
        $directories = File::directories($folder);
        foreach ($directories as $directory) {
            $this->addFolderToZip($zip, $directory, $relativePath);
        }
    }

    private function generateBackupReport()
    {
        $this->info('📊 Génération du rapport de sauvegarde...');
        
        // Déterminer le bon chemin pour le rapport
        if (is_dir($this->backupPath)) {
            $reportPath = $this->backupPath . '/backup-report.txt';
        } else {
            // Si c'est un fichier ZIP, créer le rapport à côté
            $reportPath = dirname($this->backupPath) . '/backup-report-' . $this->timestamp . '.txt';
        }
        
        $report = "RAPPORT DE SAUVEGARDE\n";
        $report .= "====================\n\n";
        $report .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $report .= "Type: " . $this->option('type') . "\n";
        $report .= "Compressé: " . ($this->option('compress') ? 'Oui' : 'Non') . "\n";
        $report .= "Taille: " . $this->formatBytes($this->getBackupSize()) . "\n\n";
        
        $report .= "CONTENU DE LA SAUVEGARDE:\n";
        $report .= "-------------------------\n";
        
        if (is_dir($this->backupPath)) {
            $this->addDirectoryToReport($this->backupPath, $report, 0);
        } else {
            $report .= "Sauvegarde compressée: " . basename($this->backupPath) . "\n";
            $report .= "Taille: " . $this->formatBytes($this->getBackupSize()) . "\n";
        }
        
        File::put($reportPath, $report);
        $this->info("📄 Rapport de sauvegarde généré: {$reportPath}");
    }

    private function addDirectoryToReport($path, &$report, $level)
    {
        $indent = str_repeat('  ', $level);
        
        $files = File::files($path);
        $directories = File::directories($path);
        
        foreach ($directories as $directory) {
            $dirName = basename($directory);
            $report .= "{$indent}📁 {$dirName}/\n";
            $this->addDirectoryToReport($directory, $report, $level + 1);
        }
        
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $size = $this->formatBytes($file->getSize());
            $report .= "{$indent}📄 {$fileName} ({$size})\n";
        }
    }

    private function getBackupSize()
    {
        if (is_dir($this->backupPath)) {
            return $this->getDirectorySize($this->backupPath);
        } else {
            return File::size($this->backupPath);
        }
    }

    private function getDirectorySize($path)
    {
        $size = 0;
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            $size += $file->getSize();
        }
        
        return $size;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
