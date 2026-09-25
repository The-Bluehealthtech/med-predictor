<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

class ProjectDbAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:db:audit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit complet du projet : tables, modèles, vues et colonnes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Début de l\'audit complet du projet...');
        
        $auditData = [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'tables' => [],
            'models' => [],
            'views' => [],
            'orphan_tables' => [],
            'unused_models' => [],
            'orphan_views' => [],
            'orphan_columns' => [],
            'cleanliness_score' => 0
        ];

        // 1. Lister toutes les tables de la base
        $this->info('📊 1. Analyse des tables de la base de données...');
        $auditData['tables'] = $this->analyzeTables();

        // 2. Lister tous les modèles
        $this->info('🏗️ 2. Analyse des modèles Eloquent...');
        $auditData['models'] = $this->analyzeModels();

        // 3. Identifier les tables sans modèle
        $this->info('❌ 3. Identification des tables sans modèle...');
        $auditData['orphan_tables'] = $this->findOrphanTables($auditData['tables'], $auditData['models']);

        // 4. Identifier les modèles non utilisés
        $this->info('❌ 4. Identification des modèles non utilisés...');
        $auditData['unused_models'] = $this->findUnusedModels($auditData['models']);

        // 5. Lister toutes les vues Blade
        $this->info('👁️ 5. Analyse des vues Blade...');
        $auditData['views'] = $this->analyzeViews();

        // 6. Identifier les vues orphelines
        $this->info('❌ 6. Identification des vues orphelines...');
        $auditData['orphan_views'] = $this->findOrphanViews($auditData['views']);

        // 7. Analyser les colonnes de chaque table
        $this->info('🔍 7. Analyse des colonnes de chaque table...');
        $auditData['orphan_columns'] = $this->analyzeColumns($auditData['tables']);

        // 8. Calculer le score de propreté
        $this->info('📈 8. Calcul du score de propreté...');
        $auditData['cleanliness_score'] = $this->calculateCleanlinessScore($auditData);

        // 9. Générer le rapport
        $this->info('📝 9. Génération du rapport...');
        $this->generateReport($auditData);

        $this->info('✅ Audit terminé ! Rapport généré dans /storage/logs/');
        
        return 0;
    }

    /**
     * Analyser toutes les tables de la base de données
     */
    private function analyzeTables(): array
    {
        $tables = [];
        
        try {
            // Récupérer toutes les tables selon le driver de base de données
            $connection = config('database.default');
            $driver = config("database.connections.{$connection}.driver");
            
            if ($driver === 'sqlite') {
                // Pour SQLite
                $tableNames = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($tableNames as $table) {
                    $tableName = $table->name;
                    $tables[] = [
                        'name' => $tableName,
                        'columns' => $this->getTableColumns($tableName),
                        'row_count' => $this->getTableRowCount($tableName)
                    ];
                }
            } else {
                // Pour MySQL/PostgreSQL
                $tableNames = DB::select('SHOW TABLES');
                $databaseName = 'Tables_in_' . config("database.connections.{$connection}.database");
                
                foreach ($tableNames as $table) {
                    $tableName = $table->$databaseName;
                    $tables[] = [
                        'name' => $tableName,
                        'columns' => $this->getTableColumns($tableName),
                        'row_count' => $this->getTableRowCount($tableName)
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->error("Erreur lors de l'analyse des tables: " . $e->getMessage());
        }

        return $tables;
    }

    /**
     * Récupérer les colonnes d'une table
     */
    private function getTableColumns(string $tableName): array
    {
        try {
            $connection = config('database.default');
            $driver = config("database.connections.{$connection}.driver");
            
            if ($driver === 'sqlite') {
                // Pour SQLite
                $columns = DB::select("PRAGMA table_info({$tableName})");
                return array_map(function($column) {
                    return [
                        'name' => $column->name,
                        'type' => $column->type,
                        'null' => $column->notnull ? 'NO' : 'YES',
                        'key' => $column->pk ? 'PRI' : '',
                        'default' => $column->dflt_value,
                        'extra' => ''
                    ];
                }, $columns);
            } else {
                // Pour MySQL/PostgreSQL
                $columns = DB::select("SHOW COLUMNS FROM `{$tableName}`");
                return array_map(function($column) {
                    return [
                        'name' => $column->Field,
                        'type' => $column->Type,
                        'null' => $column->Null,
                        'key' => $column->Key,
                        'default' => $column->Default,
                        'extra' => $column->Extra
                    ];
                }, $columns);
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Récupérer le nombre de lignes d'une table
     */
    private function getTableRowCount(string $tableName): int
    {
        try {
            $result = DB::select("SELECT COUNT(*) as count FROM `{$tableName}`");
            return $result[0]->count ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Analyser tous les modèles Eloquent
     */
    private function analyzeModels(): array
    {
        $models = [];
        $modelsPath = app_path('Models');
        
        if (!File::exists($modelsPath)) {
            return $models;
        }

        $modelFiles = File::glob($modelsPath . '/*.php');
        
        foreach ($modelFiles as $modelFile) {
            $modelName = pathinfo($modelFile, PATHINFO_FILENAME);
            $models[] = [
                'name' => $modelName,
                'file' => $modelFile,
                'class' => "App\\Models\\{$modelName}",
                'table' => $this->getModelTable($modelName),
                'relationships' => $this->getModelRelationships($modelFile)
            ];
        }

        return $models;
    }

    /**
     * Récupérer la table associée à un modèle
     */
    private function getModelTable(string $modelName): ?string
    {
        try {
            $modelClass = "App\\Models\\{$modelName}";
            if (class_exists($modelClass)) {
                $model = new $modelClass();
                return $model->getTable();
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs
        }
        
        return null;
    }

    /**
     * Récupérer les relations d'un modèle
     */
    private function getModelRelationships(string $modelFile): array
    {
        $relationships = [];
        $content = File::get($modelFile);
        
        // Rechercher les méthodes de relation
        preg_match_all('/public\s+function\s+(\w+)\s*\(\)\s*:\s*(BelongsTo|HasOne|HasMany|BelongsToMany|MorphTo|MorphMany)/', $content, $matches);
        
        if (!empty($matches[1])) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $relationships[] = [
                    'method' => $matches[1][$i],
                    'type' => $matches[2][$i]
                ];
            }
        }
        
        return $relationships;
    }

    /**
     * Trouver les tables sans modèle
     */
    private function findOrphanTables(array $tables, array $models): array
    {
        $modelTables = array_filter(array_column($models, 'table'));
        $orphanTables = [];
        
        foreach ($tables as $table) {
            if (!in_array($table['name'], $modelTables)) {
                $orphanTables[] = $table['name'];
            }
        }
        
        return $orphanTables;
    }

    /**
     * Trouver les modèles non utilisés
     */
    private function findUnusedModels(array $models): array
    {
        $unusedModels = [];
        $usedModels = $this->findUsedModels();
        
        foreach ($models as $model) {
            if (!in_array($model['name'], $usedModels)) {
                $unusedModels[] = $model['name'];
            }
        }
        
        return $unusedModels;
    }

    /**
     * Trouver les modèles utilisés dans le code
     */
    private function findUsedModels(): array
    {
        $usedModels = [];
        $searchPaths = [
            app_path('Http/Controllers'),
            app_path('Http/Middleware'),
            app_path('Services'),
            app_path('Providers'),
            base_path('routes'),
            app_path('Console/Commands')
        ];
        
        foreach ($searchPaths as $path) {
            if (File::exists($path)) {
                $files = File::allFiles($path);
                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $content = File::get($file->getPathname());
                        $this->extractUsedModels($content, $usedModels);
                    }
                }
            }
        }
        
        return array_unique($usedModels);
    }

    /**
     * Extraire les modèles utilisés d'un fichier
     */
    private function extractUsedModels(string $content, array &$usedModels): void
    {
        // Rechercher les imports de modèles
        preg_match_all('/use\s+App\\\Models\\\\(\w+)/', $content, $matches);
        if (!empty($matches[1])) {
            $usedModels = array_merge($usedModels, $matches[1]);
        }
        
        // Rechercher les références directes
        preg_match_all('/App\\\Models\\\\(\w+)/', $content, $matches);
        if (!empty($matches[1])) {
            $usedModels = array_merge($usedModels, $matches[1]);
        }
    }

    /**
     * Analyser toutes les vues Blade
     */
    private function analyzeViews(): array
    {
        $views = [];
        $viewsPath = resource_path('views');
        
        if (!File::exists($viewsPath)) {
            return $views;
        }

        $viewFiles = File::allFiles($viewsPath);
        
        foreach ($viewFiles as $viewFile) {
            if ($viewFile->getExtension() === 'blade.php') {
                $relativePath = str_replace($viewsPath . '/', '', $viewFile->getPathname());
                $viewName = str_replace('.blade.php', '', $relativePath);
                $viewName = str_replace('/', '.', $viewName);
                
                $views[] = [
                    'name' => $viewName,
                    'path' => $viewFile->getPathname(),
                    'size' => $viewFile->getSize(),
                    'last_modified' => date('Y-m-d H:i:s', $viewFile->getMTime())
                ];
            }
        }

        return $views;
    }

    /**
     * Trouver les vues orphelines
     */
    private function findOrphanViews(array $views): array
    {
        $orphanViews = [];
        $usedViews = $this->findUsedViews();
        
        foreach ($views as $view) {
            if (!in_array($view['name'], $usedViews)) {
                $orphanViews[] = $view['name'];
            }
        }
        
        return $orphanViews;
    }

    /**
     * Trouver les vues utilisées dans le code
     */
    private function findUsedViews(): array
    {
        $usedViews = [];
        $searchPaths = [
            app_path('Http/Controllers'),
            app_path('Http/Middleware'),
            app_path('Services'),
            app_path('Providers'),
            base_path('routes'),
            app_path('Console/Commands')
        ];
        
        foreach ($searchPaths as $path) {
            if (File::exists($path)) {
                $files = File::allFiles($path);
                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $content = File::get($file->getPathname());
                        $this->extractUsedViews($content, $usedViews);
                    }
                }
            }
        }
        
        return array_unique($usedViews);
    }

    /**
     * Extraire les vues utilisées d'un fichier
     */
    private function extractUsedViews(string $content, array &$usedViews): void
    {
        // Rechercher les appels view()
        preg_match_all('/view\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
        if (!empty($matches[1])) {
            $usedViews = array_merge($usedViews, $matches[1]);
        }
        
        // Rechercher les appels View::make()
        preg_match_all('/View::make\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
        if (!empty($matches[1])) {
            $usedViews = array_merge($usedViews, $matches[1]);
        }
    }

    /**
     * Analyser les colonnes de chaque table
     */
    private function analyzeColumns(array $tables): array
    {
        $orphanColumns = [];
        
        foreach ($tables as $table) {
            $tableName = $table['name'];
            $orphanColumns[$tableName] = [];
            
            foreach ($table['columns'] as $column) {
                $columnName = $column['name'];
                
                // Ignorer les colonnes système
                if (in_array($columnName, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                    continue;
                }
                
                if (!$this->isColumnUsed($columnName, $tableName)) {
                    $orphanColumns[$tableName][] = [
                        'name' => $columnName,
                        'type' => $column['type'],
                        'null' => $column['null'],
                        'key' => $column['key']
                    ];
                }
            }
            
            // Supprimer les tables sans colonnes orphelines
            if (empty($orphanColumns[$tableName])) {
                unset($orphanColumns[$tableName]);
            }
        }
        
        return $orphanColumns;
    }

    /**
     * Vérifier si une colonne est utilisée dans le code
     */
    private function isColumnUsed(string $columnName, string $tableName): bool
    {
        $searchPaths = [
            app_path('Http/Controllers'),
            app_path('Http/Middleware'),
            app_path('Services'),
            app_path('Providers'),
            base_path('routes'),
            app_path('Console/Commands'),
            resource_path('views')
        ];
        
        foreach ($searchPaths as $path) {
            if (File::exists($path)) {
                $files = File::allFiles($path);
                foreach ($files as $file) {
                    if (in_array($file->getExtension(), ['php', 'blade.php'])) {
                        $content = File::get($file->getPathname());
                        
                        // Rechercher la colonne dans le contexte de la table
                        if (strpos($content, $columnName) !== false) {
                            // Vérifier si c'est dans le bon contexte
                            if ($this->isColumnInTableContext($content, $columnName, $tableName)) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Vérifier si une colonne est utilisée dans le contexte de sa table
     */
    private function isColumnInTableContext(string $content, string $columnName, string $tableName): bool
    {
        // Rechercher des patterns qui indiquent l'utilisation de la colonne
        $patterns = [
            "->{$columnName}",           // Accès direct à la propriété
            "['{$columnName}']",         // Accès via tableau
            "{$columnName}=",            // Assignation
            "{$columnName} ",            // Dans une requête
            "{$columnName},",            // Dans une liste
            "{$columnName})",            // Dans une fonction
            "{$columnName}\"",           // Dans une chaîne
            "{$columnName}'",            // Dans une chaîne
            "{$columnName}\n",           // Fin de ligne
            "{$columnName}\r",           // Fin de ligne
            "{$columnName}\t",           // Tabulation
            "{$columnName} ",            // Espace
        ];
        
        foreach ($patterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Calculer le score de propreté
     */
    private function calculateCleanlinessScore(array $auditData): float
    {
        $scores = [];
        
        // Score des tables avec modèle
        $totalTables = count($auditData['tables']);
        $tablesWithModel = $totalTables - count($auditData['orphan_tables']);
        $scores['tables'] = $totalTables > 0 ? ($tablesWithModel / $totalTables) * 100 : 100;
        
        // Score des modèles utilisés
        $totalModels = count($auditData['models']);
        $usedModels = $totalModels - count($auditData['unused_models']);
        $scores['models'] = $totalModels > 0 ? ($usedModels / $totalModels) * 100 : 100;
        
        // Score des vues utilisées
        $totalViews = count($auditData['views']);
        $usedViews = $totalViews - count($auditData['orphan_views']);
        $scores['views'] = $totalViews > 0 ? ($usedViews / $totalViews) * 100 : 100;
        
        // Score des colonnes utilisées
        $totalColumns = 0;
        $usedColumns = 0;
        foreach ($auditData['tables'] as $table) {
            $totalColumns += count($table['columns']);
        }
        foreach ($auditData['orphan_columns'] as $tableColumns) {
            $usedColumns += count($tableColumns);
        }
        $scores['columns'] = $totalColumns > 0 ? (($totalColumns - $usedColumns) / $totalColumns) * 100 : 100;
        
        // Moyenne pondérée
        $finalScore = array_sum($scores) / count($scores);
        
        return round($finalScore, 2);
    }

    /**
     * Générer le rapport d'audit
     */
    private function generateReport(array $auditData): void
    {
        $timestamp = now()->format('Ymd_His');
        $logPath = storage_path("logs/db-audit-{$timestamp}.log");
        
        $report = [
            'audit_summary' => [
                'timestamp' => $auditData['timestamp'],
                'cleanliness_score' => $auditData['cleanliness_score'] . '%',
                'total_tables' => count($auditData['tables']),
                'total_models' => count($auditData['models']),
                'total_views' => count($auditData['views']),
                'total_columns' => array_sum(array_map(fn($table) => count($table['columns']), $auditData['tables']))
            ],
            'tables_found' => array_map(fn($table) => [
                'name' => $table['name'],
                'columns_count' => count($table['columns']),
                'row_count' => $table['row_count']
            ], $auditData['tables']),
            'orphan_tables' => $auditData['orphan_tables'],
            'unused_models' => $auditData['unused_models'],
            'views_found' => array_map(fn($view) => [
                'name' => $view['name'],
                'size' => $view['size'],
                'last_modified' => $view['last_modified']
            ], $auditData['views']),
            'orphan_views' => $auditData['orphan_views'],
            'orphan_columns' => $auditData['orphan_columns'],
            'detailed_tables' => $auditData['tables'],
            'detailed_models' => $auditData['models']
        ];
        
        File::put($logPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info("📊 Rapport généré : {$logPath}");
        
        // Afficher un résumé dans la console
        $this->displaySummary($auditData);
    }

    /**
     * Afficher un résumé dans la console
     */
    private function displaySummary(array $auditData): void
    {
        $this->newLine();
        $this->info('📊 RÉSUMÉ DE L\'AUDIT');
        $this->info('==================');
        $this->info("Score de propreté : {$auditData['cleanliness_score']}%");
        $this->info("Tables trouvées : " . count($auditData['tables']));
        $this->info("Tables orphelines : " . count($auditData['orphan_tables']));
        $this->info("Modèles trouvés : " . count($auditData['models']));
        $this->info("Modèles non utilisés : " . count($auditData['unused_models']));
        $this->info("Vues trouvées : " . count($auditData['views']));
        $this->info("Vues orphelines : " . count($auditData['orphan_views']));
        
        if (!empty($auditData['orphan_columns'])) {
            $this->warn("⚠️  Colonnes orphelines détectées dans " . count($auditData['orphan_columns']) . " table(s)");
        }
        
        $this->newLine();
    }
}
