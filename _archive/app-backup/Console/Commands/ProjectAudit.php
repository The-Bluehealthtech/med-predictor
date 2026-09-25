<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProjectAudit extends Command
{
    protected $signature = 'project:audit {--format=text : Format de sortie (text, json, html)}';
    protected $description = 'Audit complet du projet : code, routes, vues et structure';

    private $results = [];
    private $orphanRoutes = [];
    private $orphanViews = [];
    private $unusedControllers = [];
    private $missingRoutes = [];

    public function handle()
    {
        $this->info('🔍 Début de l\'audit complet du projet...');
        
        // 1. Audit des routes
        $this->auditRoutes();
        
        // 2. Audit des vues
        $this->auditViews();
        
        // 3. Audit des contrôleurs
        $this->auditControllers();
        
        // 4. Audit des middlewares
        $this->auditMiddlewares();
        
        // 5. Audit des composants
        $this->auditComponents();
        
        // 6. Génération du rapport
        $this->generateReport();
        
        $this->info('✅ Audit du projet terminé !');
        
        return 0;
    }

    private function auditRoutes()
    {
        $this->info('🛣️  Audit des routes...');
        
        $routes = Route::getRoutes();
        $definedRoutes = [];
        $routeFiles = [];
        
        // Analyser les routes définies
        foreach ($routes as $route) {
            $uri = $route->uri();
            $methods = $route->methods();
            $action = $route->getActionName();
            
            $definedRoutes[] = [
                'uri' => $uri,
                'methods' => $methods,
                'action' => $action,
                'middleware' => $route->middleware()
            ];
            
            // Identifier les routes sans contrôleur
            if (Str::startsWith($action, 'Closure')) {
                $this->orphanRoutes[] = [
                    'uri' => $uri,
                    'type' => 'Closure',
                    'methods' => $methods
                ];
            }
        }
        
        // Vérifier les fichiers de routes
        $routeFiles = [
            'routes/web.php',
            'routes/api.php',
            'routes/console.php',
            'routes/channels.php'
        ];
        
        foreach ($routeFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);
                $this->analyzeRouteFile($file, $content);
            }
        }
        
        $this->results['routes'] = [
            'total' => count($definedRoutes),
            'defined' => $definedRoutes,
            'orphan' => $this->orphanRoutes,
            'files' => $routeFiles
        ];
    }

    private function auditViews()
    {
        $this->info('👁️  Audit des vues...');
        
        $viewPath = resource_path('views');
        $views = [];
        $orphanViews = [];
        
        if (File::exists($viewPath)) {
            $this->scanViews($viewPath, $views, $orphanViews);
        }
        
        // Vérifier les vues utilisées dans les contrôleurs
        $usedViews = $this->findUsedViews();
        
        $this->results['views'] = [
            'total' => count($views),
            'files' => $views,
            'orphan' => $orphanViews,
            'used' => $usedViews
        ];
    }

    private function auditControllers()
    {
        $this->info('🎮 Audit des contrôleurs...');
        
        $controllerPath = app_path('Http/Controllers');
        $controllers = [];
        $unusedControllers = [];
        
        if (File::exists($controllerPath)) {
            $this->scanControllers($controllerPath, $controllers, $unusedControllers);
        }
        
        $this->results['controllers'] = [
            'total' => count($controllers),
            'files' => $controllers,
            'unused' => $unusedControllers
        ];
    }

    private function auditMiddlewares()
    {
        $this->info('🛡️  Audit des middlewares...');
        
        $middlewarePath = app_path('Http/Middleware');
        $middlewares = [];
        
        if (File::exists($middlewarePath)) {
            $this->scanMiddlewares($middlewarePath, $middlewares);
        }
        
        $this->results['middlewares'] = [
            'total' => count($middlewares),
            'files' => $middlewares
        ];
    }

    private function auditComponents()
    {
        $this->info('🧩 Audit des composants...');
        
        $componentPath = resource_path('views/components');
        $components = [];
        
        if (File::exists($componentPath)) {
            $this->scanComponents($componentPath, $components);
        }
        
        $this->results['components'] = [
            'total' => count($components),
            'files' => $components
        ];
    }

    private function scanViews($path, &$views, &$orphanViews)
    {
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'blade.php') {
                $relativePath = str_replace(resource_path('views/'), '', $file->getPathname());
                $viewName = str_replace('.blade.php', '', $relativePath);
                $viewName = str_replace('/', '.', $viewName);
                
                $views[] = [
                    'name' => $viewName,
                    'path' => $file->getPathname(),
                    'size' => $file->getSize()
                ];
                
                // Vérifier si la vue est utilisée
                if (!$this->isViewUsed($viewName)) {
                    $orphanViews[] = $viewName;
                }
            }
        }
    }

    private function scanControllers($path, &$controllers, &$unusedControllers)
    {
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $className = str_replace('.php', '', $file->getFilename());
                $fullClassName = 'App\\Http\\Controllers\\' . $className;
                
                $controllers[] = [
                    'name' => $className,
                    'path' => $file->getPathname(),
                    'size' => $file->getSize()
                ];
                
                // Vérifier si le contrôleur est utilisé dans les routes
                if (!$this->isControllerUsed($fullClassName)) {
                    $unusedControllers[] = $className;
                }
            }
        }
    }

    private function scanMiddlewares($path, &$middlewares)
    {
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $className = str_replace('.php', '', $file->getFilename());
                
                $middlewares[] = [
                    'name' => $className,
                    'path' => $file->getPathname(),
                    'size' => $file->getSize()
                ];
            }
        }
    }

    private function scanComponents($path, &$components)
    {
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'blade.php') {
                $componentName = str_replace('.blade.php', '', $file->getFilename());
                
                $components[] = [
                    'name' => $componentName,
                    'path' => $file->getPathname(),
                    'size' => $file->getSize()
                ];
            }
        }
    }

    private function isViewUsed($viewName)
    {
        // Recherche simple dans le code source
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

    private function findUsedViews()
    {
        $usedViews = [];
        $searchPaths = [
            app_path('Http/Controllers'),
            app_path('Http/Requests'),
            app_path('Services')
        ];
        
        foreach ($searchPaths as $path) {
            if (File::exists($path)) {
                $files = File::allFiles($path);
                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $content = File::get($file->getPathname());
                        preg_match_all("/view\s*\(\s*['\"]([^'\"]+)['\"]/", $content, $matches);
                        if (!empty($matches[1])) {
                            $usedViews = array_merge($usedViews, $matches[1]);
                        }
                    }
                }
            }
        }
        
        return array_unique($usedViews);
    }

    private function analyzeRouteFile($file, $content)
    {
        // Analyse basique du fichier de routes
        $lines = explode("\n", $content);
        $routeCount = 0;
        
        foreach ($lines as $line) {
            if (Str::contains($line, 'Route::') || Str::contains($line, '->name(')) {
                $routeCount++;
            }
        }
        
        $this->results['route_files'][$file] = [
            'lines' => count($lines),
            'routes' => $routeCount
        ];
    }

    private function generateReport()
    {
        $this->info('📝 Génération du rapport...');
        
        $format = $this->option('format');
        
        switch ($format) {
            case 'json':
                $this->generateJsonReport();
                break;
            case 'html':
                $this->generateHtmlReport();
                break;
            default:
                $this->generateTextReport();
        }
    }

    private function generateTextReport()
    {
        $this->newLine();
        $this->info('📊 RÉSUMÉ DE L\'AUDIT DU PROJET');
        $this->info('================================');
        
        $this->info("Routes totales: " . $this->results['routes']['total']);
        $this->info("Routes orphelines: " . count($this->results['routes']['orphan']));
        $this->info("Vues totales: " . $this->results['views']['total']);
        $this->info("Vues orphelines: " . count($this->results['views']['orphan']));
        $this->info("Contrôleurs: " . $this->results['controllers']['total']);
        $this->info("Contrôleurs non utilisés: " . count($this->results['controllers']['unused']));
        $this->info("Middlewares: " . $this->results['middlewares']['total']);
        $this->info("Composants: " . $this->results['components']['total']);
        
        if (!empty($this->results['routes']['orphan'])) {
            $this->warn("\n⚠️  Routes orphelines détectées:");
            foreach ($this->results['routes']['orphan'] as $route) {
                $this->line("  - {$route['uri']} ({$route['type']})");
            }
        }
        
        if (!empty($this->results['views']['orphan'])) {
            $this->warn("\n⚠️  Vues orphelines détectées:");
            foreach (array_slice($this->results['views']['orphan'], 0, 10) as $view) {
                $this->line("  - $view");
            }
            if (count($this->results['views']['orphan']) > 10) {
                $this->line("  ... et " . (count($this->results['views']['orphan']) - 10) . " autres");
            }
        }
        
        if (!empty($this->results['controllers']['unused'])) {
            $this->warn("\n⚠️  Contrôleurs non utilisés:");
            foreach ($this->results['controllers']['unused'] as $controller) {
                $this->line("  - $controller");
            }
        }
    }

    private function generateJsonReport()
    {
        $reportFile = storage_path('logs/project-audit-' . date('Ymd_His') . '.json');
        File::put($reportFile, json_encode($this->results, JSON_PRETTY_PRINT));
        $this->info("📄 Rapport JSON généré: $reportFile");
    }

    private function generateHtmlReport()
    {
        $reportFile = storage_path('logs/project-audit-' . date('Ymd_His') . '.html');
        $html = $this->generateHtmlContent();
        File::put($reportFile, $html);
        $this->info("📄 Rapport HTML généré: $reportFile");
    }

    private function generateHtmlContent()
    {
        return view('reports.project-audit', ['results' => $this->results])->render();
    }
}
