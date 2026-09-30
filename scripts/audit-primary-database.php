<?php

// Audit statique en lecture seule. Aucun contenu de ligne ni donnée nominative.
require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$root = dirname(__DIR__);
$items = [];
$unresolved = [];
foreach (Illuminate\Support\Facades\Route::getRoutes() as $route) {
    $handler = $route->getAction('uses');
    try {
        if ($handler instanceof Closure) {
            $reflection = new ReflectionFunction($handler);
            $label = 'Closure';
        } elseif (is_string($handler)) {
            [$class, $method] = str_contains($handler, '@') ? explode('@', $handler, 2) : [$handler, '__invoke'];
            $reflection = new ReflectionMethod($class, $method);
            $label = $handler;
        } else {
            continue;
        }
        $lines = file($reflection->getFileName());
        $source = implode('', array_slice($lines, $reflection->getStartLine() - 1,
            $reflection->getEndLine() - $reflection->getStartLine() + 1));
        $writes = preg_match('/(?:->(?:save|update|delete|insert|upsert|create|attach|detach|sync|restore|forceDelete)\s*\(|::(?:create|insert|upsert|updateOrCreate|firstOrCreate)\s*\()/i', $source) === 1;
        $mutatingVerb = count(array_intersect($route->methods(), ['POST', 'PUT', 'PATCH', 'DELETE'])) > 0;
        if ($mutatingVerb || $writes) {
            $items[] = [implode('|', $route->methods()), $route->uri(), $label,
                $writes ? 'écriture directe repérée' : 'délégation ou commande à examiner'];
        }
    } catch (Throwable $error) {
        $unresolved[] = [implode('|', $route->methods()), $route->uri(), is_string($handler) ? $handler : 'Closure'];
    }
}
usort($items, fn ($a, $b) => [$a[1], $a[0]] <=> [$b[1], $b[0]]);
$overrides = [];
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/app', FilesystemIterator::SKIP_DOTS));
foreach ($files as $file) {
    if ($file->getExtension() !== 'php') continue;
    foreach (file($file->getPathname()) as $index => $line) {
        if (preg_match('/(?:protected|public)\s+\$connection\s*=|(?:DB::connection|setConnection|->connection)\s*\(\s*[\'\"]/', $line)) {
            $overrides[] = substr($file->getPathname(), strlen($root) + 1) . ':' . ($index + 1);
        }
    }
}
$document = "# Inventaire des interfaces vers la base principale\n\n";
$document .= 'Routes actives : ' . count(Illuminate\Support\Facades\Route::getRoutes()) . '. Routes de commande ou avec écriture directe repérée : ' . count($items) . ".\n\n";
$document .= "Audit statique des méthodes réellement enregistrées. Les appels délégués nécessitent une vérification des services et un test de persistance. Une détection directe ne prouve pas que la transaction ou l'autorisation est correcte. Aucun scénario utilisateur n'est exécuté par cet audit.\n\n";
$document .= 'Connexions nommées explicites repérées dans app : ' . count($overrides) . ".\n\n";
foreach ($overrides as $override) $document .= '- `' . $override . "`\n";
$document .= "\n| Méthodes | URI | Action | Résultat statique |\n|---|---|---|---|\n";
foreach ($items as $item) {
    $escaped = array_map(fn ($value) => str_replace('|', '\\|', $value), $item);
    $document .= '| ' . implode(' | ', $escaped) . " |\n";
}
$document .= "\n## Actions non résolues\n\n";
foreach ($unresolved as $item) $document .= '- `' . implode(' — ', $item) . "`\n";
if (!$unresolved) $document .= "Aucune.\n";
file_put_contents($root . '/docs/PRIMARY_DATABASE_ROUTE_AUDIT.md', $document);
echo json_encode(['routes' => count(Illuminate\Support\Facades\Route::getRoutes()),
    'command_routes' => count($items), 'connection_overrides' => count($overrides),
    'unresolved_actions' => count($unresolved)], JSON_UNESCAPED_UNICODE) . PHP_EOL;
