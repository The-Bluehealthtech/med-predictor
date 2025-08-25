<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class V3ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier la version de l'API
        $apiVersion = $request->header('X-API-Version');
        
        if ($apiVersion && $apiVersion !== '3.0.0') {
            Log::warning('Version API non supportée', [
                'requested_version' => $apiVersion,
                'supported_version' => '3.0.0',
                'endpoint' => $request->path(),
                'ip' => $request->ip(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Version API non supportée',
                'error' => 'API version ' . $apiVersion . ' is not supported. Please use version 3.0.0',
                'supported_versions' => ['3.0.0'],
                'current_version' => '3.0.0',
                'deprecation_info' => [
                    'v1' => 'Deprecated - Use v3',
                    'v2' => 'Deprecated - Use v3',
                    'v3' => 'Current stable version',
                ],
            ], 400);
        }

        // Ajouter les en-têtes V3
        $response = $next($request);
        
        if ($response instanceof JsonResponse) {
            $response->header('X-API-Version', '3.0.0');
            $response->header('X-FIT-Version', '3.0.0');
            $response->header('X-FIT-Codename', 'AI-Powered Football Intelligence');
        }

        // Log des requêtes V3
        Log::info('API V3 Request', [
            'method' => $request->method(),
            'endpoint' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return $response;
    }
}
