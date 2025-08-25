<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectRoutesClean extends Command
{
    protected $signature = 'project:routes:clean {--dry-run : Afficher seulement ce qui serait supprimé} {--force : Forcer la suppression}';
    protected $description = 'Nettoyage des routes, vues et colonnes orphelines basé sur les audits';

    private $dryRun;
    private $force;
    private $orphanRoutes = [];
    private $orphanViews = [];
    private $orphanColumns = [];
    private $unusedControllers = [];

    public function handle()
    {
        $this->dryRun = $this->option('dry-run');
        $this->force = $this->option('force');
        
        if ($this->dryRun) {
            $this->info('🔍 Mode DRY-RUN : Aucune suppression ne sera effectuée');
        }
        
        $this->info('🧹 Début du nettoyage du projet...');
        
        // 1. Analyser les éléments orphelins
        $this->analyzeOrphanElements();
        
        // 2. Afficher le résumé
        $this->displaySummary();
        
        // 3. Demander confirmation si pas en dry-run
        if (!$this->dryRun && !$this->force) {
            if (!$this->confirm('Voulez-vous continuer avec la suppression ?')) {
                $this->info('❌ Nettoyage annulé');
                return 0;
            }
        }
        
        // 4. Effectuer le nettoyage
        $this->performCleanup();
        
        $this->info('✅ Nettoyage terminé !');
        
        return 0;
    }

    private function analyzeOrphanElements()
    {
        $this->info('🔍 Analyse des éléments orphelins...');
        
        // Analyser les routes orphelines
        $this->analyzeOrphanRoutes();
        
        // Analyser les vues orphelines
        $this->analyzeOrphanViews();
        
        // Analyser les colonnes orphelines
        $this->analyzeOrphanColumns();
        
        // Analyser les contrôleurs non utilisés
        $this->analyzeUnusedControllers();
    }

    private function analyzeOrphanRoutes()
    {
        $this->info('🛣️  Analyse des routes orphelines...');
        
        $routes = Route::getRoutes();
        
        foreach ($routes as $route) {
            $action = $route->getActionName();
            
            // Routes avec des closures (potentiellement orphelines)
            if (Str::startsWith($action, 'Closure')) {
                $this->orphanRoutes[] = [
                    'uri' => $route->uri(),
                    'methods' => $route->methods(),
                    'action' => $action,
                    'type' => 'Closure'
                ];
            }
            
            // Routes vers des contrôleurs inexistants
            if (Str::contains($action, 'App\\Http\\Controllers\\')) {
                $controllerClass = $this->extractControllerClass($action);
                if ($controllerClass && !class_exists($controllerClass)) {
                    $this->orphanRoutes[] = [
                        'uri' => $route->uri(),
                        'methods' => $route->methods(),
                        'action' => $action,
                        'type' => 'Controller inexistant'
                    ];
                }
            }
        }
    }

    private function analyzeOrphanViews()
    {
        $this->info('👁️  Analyse des vues orphelines...');
        
        $viewPath = resource_path('views');
        if (!File::exists($viewPath)) {
            return;
        }
        
        $views = File::allFiles($viewPath);
        
        foreach ($views as $view) {
            if ($view->getExtension() === 'blade.php') {
                $viewName = $this->getViewName($view);
                
                if (!$this->isViewUsed($viewName)) {
                    $this->orphanViews[] = [
                        'name' => $viewName,
                        'path' => $view->getPathname(),
                        'size' => $view->getSize()
                    ];
                }
            }
        }
    }

    private function analyzeOrphanColumns()
    {
        $this->info('🗄️  Analyse des colonnes orphelines...');
        
        try {
            $tables = Schema::getAllTables();
            
            foreach ($tables as $table) {
                $tableName = $table->name;
                $columns = Schema::getColumnListing($tableName);
                
                foreach ($columns as $column) {
                    if (!$this->isColumnUsed($tableName, $column)) {
                        $this->orphanColumns[] = [
                            'table' => $tableName,
                            'column' => $column,
                            'type' => $this->getColumnType($tableName, $column)
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            $this->warn("⚠️  Impossible d'analyser la base de données: " . $e->getMessage());
        }
    }

    private function analyzeUnusedControllers()
    {
        $this->info('🎮 Analyse des contrôleurs non utilisés...');
        
        $controllerPath = app_path('Http/Controllers');
        if (!File::exists($controllerPath)) {
            return;
        }
        
        $controllers = File::allFiles($controllerPath);
        
        foreach ($controllers as $controller) {
            if ($controller->getExtension() === 'php') {
                $className = str_replace('.php', '', $controller->getFilename());
                $fullClassName = 'App\\Http\\Controllers\\' . $className;
                
                if (!$this->isControllerUsed($fullClassName)) {
                    $this->unusedControllers[] = [
                        'name' => $className,
                        'path' => $controller->getPathname(),
                        'size' => $controller->getSize()
                    ];
                }
            }
        }
    }

    private function extractControllerClass($action)
    {
        if (preg_match('/App\\\Http\\\Controllers\\\([^@]+)/', $action, $matches)) {
            return 'App\\Http\\Controllers\\' . $matches[1];
        }
        return null;
    }

    private function getViewName($file)
    {
        $relativePath = str_replace(resource_path('views/'), '', $file->getPathname());
        $viewName = str_replace('.blade.php', '', $relativePath);
        return str_replace('/', '.', $viewName);
    }

    private function isViewUsed($viewName)
    {
        $searchPaths = [
            app_path('Http/Controllers'),
            app_path('Http/Requests'),
            app_path('Services'),
            resource_path('views')
        ];
        
        foreach ($searchPaths as $path) {
            if (File::exists($path)) {
                $files = File::allFiles($path);
                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $content = File::get($file->getPathname());
                        if (Str::contains($content, "view('$viewName'") || 
                            Str::contains($content, "view(\"$viewName\"")) {
                            return true;
                        }
                    }
                }
            }
        }
        
        return false;
    }

    private function isColumnUsed($tableName, $column)
    {
        $searchPaths = [
            app_path('Http/Controllers'),
            app_path('Models'),
            app_path('Http/Requests'),
            app_path('Services')
        ];
        
        foreach ($searchPaths as $path) {
            if (File::exists($path)) {
                $files = File::allFiles($path);
                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $content = File::get($file->getPathname());
                        if (Str::contains($content, "$tableName.$column") || 
                            Str::contains($content, "'$column'") ||
                            Str::contains($content, "\"$column\"")) {
                            return true;
                        }
                    }
                }
            }
        }
        
        return false;
    }

    private function isControllerUsed($controllerClass)
    {
        $routes = Route::getRoutes();
        
        foreach ($routes as $route) {
            $action = $route->getActionName();
            if (Str::contains($action, $controllerClass)) {
                return true;
            }
        }
        
        return false;
    }

    private function getColumnType($tableName, $column)
    {
        try {
            $columns = DB::select("PRAGMA table_info($tableName)");
            foreach ($columns as $col) {
                if ($col->name === $column) {
                    return $col->type;
                }
            }
        } catch (\Exception $e) {
            return 'unknown';
        }
        return 'unknown';
    }

    private function displaySummary()
    {
        $this->newLine();
        $this->info('📊 RÉSUMÉ DES ÉLÉMENTS ORPHELINS');
        $this->info('================================');
        
        $this->info("Routes orphelines: " . count($this->orphanRoutes));
        $this->info("Vues orphelines: " . count($this->orphanViews));
        $this->info("Colonnes orphelines: " . count($this->orphanColumns));
        $this->info("Contrôleurs non utilisés: " . count($this->unusedControllers));
        
        $totalSize = 0;
        foreach ($this->orphanViews as $view) {
            $totalSize += $view['size'];
        }
        foreach ($this->unusedControllers as $controller) {
            $totalSize += $controller['size'];
        }
        
        $this->info("Taille totale à nettoyer: " . $this->formatBytes($totalSize));
        
        if (!empty($this->orphanRoutes)) {
            $this->warn("\n⚠️  Routes orphelines détectées:");
            foreach (array_slice($this->orphanRoutes, 0, 5) as $route) {
                $this->line("  - {$route['uri']} ({$route['type']})");
            }
            if (count($this->orphanRoutes) > 5) {
                $this->line("  ... et " . (count($this->orphanRoutes) - 5) . " autres");
            }
        }
        
        if (!empty($this->orphanViews)) {
            $this->warn("\n⚠️  Vues orphelines détectées:");
            foreach (array_slice($this->orphanViews, 0, 5) as $view) {
                $this->line("  - {$view['name']} (" . $this->formatBytes($view['size']) . ")");
            }
            if (count($this->orphanViews) > 5) {
                $this->line("  ... et " . (count($this->orphanViews) - 5) . " autres");
            }
        }
        
        if (!empty($this->orphanColumns)) {
            $this->warn("\n⚠️  Colonnes orphelines détectées:");
            foreach (array_slice($this->orphanColumns, 0, 5) as $column) {
                $this->line("  - {$column['table']}.{$column['column']} ({$column['type']})");
            }
            if (count($this->orphanColumns) > 5) {
                $this->line("  ... et " . (count($this->orphanColumns) - 5) . " autres");
            }
        }
    }

    private function performCleanup()
    {
        $this->info('🧹 Début du nettoyage...');
        
        $deletedCount = 0;
        $deletedSize = 0;
        
        // Supprimer les vues orphelines
        foreach ($this->orphanViews as $view) {
            if (File::exists($view['path'])) {
                if (!$this->dryRun) {
                    File::delete($view['path']);
                    $this->info("🗑️  Vue supprimée: {$view['name']}");
                } else {
                    $this->line("🗑️  Vue à supprimer: {$view['name']}");
                }
                $deletedCount++;
                $deletedSize += $view['size'];
            }
        }
        
        // Supprimer les contrôleurs non utilisés
        foreach ($this->unusedControllers as $controller) {
            if (File::exists($controller['path'])) {
                if (!$this->dryRun) {
                    File::delete($controller['path']);
                    $this->info("🗑️  Contrôleur supprimé: {$controller['name']}");
                } else {
                    $this->line("🗑️  Contrôleur à supprimer: {$controller['name']}");
                }
                $deletedCount++;
                $deletedSize += $controller['size'];
            }
        }
        
        // Note: Les routes et colonnes ne sont pas supprimées automatiquement
        // car cela nécessite une intervention manuelle plus prudente
        
        if ($this->dryRun) {
            $this->info("🔍 DRY-RUN: $deletedCount éléments seraient supprimés");
            $this->info("🔍 DRY-RUN: " . $this->formatBytes($deletedSize) . " seraient libérés");
        } else {
            $this->info("✅ $deletedCount éléments supprimés");
            $this->info("✅ " . $this->formatBytes($deletedSize) . " libérés");
        }
        
        // Générer un rapport de nettoyage
        $this->generateCleanupReport($deletedCount, $deletedSize);
    }

    private function generateCleanupReport($deletedCount, $deletedSize)
    {
        $reportPath = storage_path('logs/cleanup-report-' . date('Ymd_His') . '.txt');
        
        $report = "RAPPORT DE NETTOYAGE\n";
        $report .= "==================\n\n";
        $report .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $report .= "Mode: " . ($this->dryRun ? 'DRY-RUN' : 'EXÉCUTION') . "\n";
        $report .= "Éléments supprimés: $deletedCount\n";
        $report .= "Espace libéré: " . $this->formatBytes($deletedSize) . "\n\n";
        
        $report .= "DÉTAILS:\n";
        $report .= "--------\n";
        
        if (!empty($this->orphanViews)) {
            $report .= "\nVues orphelines:\n";
            foreach ($this->orphanViews as $view) {
                $report .= "- {$view['name']} (" . $this->formatBytes($view['size']) . ")\n";
            }
        }
        
        if (!empty($this->unusedControllers)) {
            $report .= "\nContrôleurs non utilisés:\n";
            foreach ($this->unusedControllers as $controller) {
                $report .= "- {$controller['name']} (" . $this->formatBytes($controller['size']) . ")\n";
            }
        }
        
        if (!empty($this->orphanRoutes)) {
            $report .= "\nRoutes orphelines (à vérifier manuellement):\n";
            foreach ($this->orphanRoutes as $route) {
                $report .= "- {$route['uri']} ({$route['type']})\n";
            }
        }
        
        if (!empty($this->orphanColumns)) {
            $report .= "\nColonnes orphelines (à vérifier manuellement):\n";
            foreach ($this->orphanColumns as $column) {
                $report .= "- {$column['table']}.{$column['column']} ({$column['type']})\n";
            }
        }
        
        File::put($reportPath, $report);
        $this->info("📄 Rapport de nettoyage généré: {$reportPath}");
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
