<?php

namespace App\Http\Middleware;

use App\Services\Audit\Auditor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Journalise la consultation des données sensibles (routes de config/audit.php,
 * requêtes GET d'un utilisateur connecté) : qui a ouvert quel dossier, quand.
 * Seuls le nom de la route et ses paramètres sont gardés, jamais le contenu.
 */
class AuditSensitiveAccess
{
    public function __construct(private readonly Auditor $auditor)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $route = $request->route();
        // Routes sans nom (API v1) : repérées par leur adresse.
        $name = $route?->getName() ?? ($route ? '/' . ltrim($route->uri(), '/') : null);
        if ($request->isMethod('GET') && $name && $request->user() && $response->getStatusCode() < 400
            && Str::is(config('audit.sensitive_routes', []), $name)) {
            $parameters = collect($route->parameters())->map(fn ($value) => is_object($value) && method_exists($value, 'getKey') ? $value->getKey() : (is_scalar($value) ? $value : null))->all();
            $this->auditor->record(['event_type' => 'data_access', 'action' => 'view', 'module' => 'medical',
                'description' => 'Consultation : ' . $name, 'metadata' => ['route' => $name, 'parameters' => $parameters]]);
        }

        return $response;
    }
}
