<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseFallback
{
    public function handle(Request $request, Closure $next)
    {
        try {
            DB::connection()->getPdo();
        } catch (\Exception $exception) {
            // Une panne de la base principale interdit la saisie : aucune donnée
            // simulée ni réponse de succès ne doit remplacer une écriture durable.
            $message = $request->getPreferredLanguage(['fr', 'en']) === 'en'
                ? 'Service temporarily unavailable. Please try again later.'
                : 'Service temporairement indisponible. Veuillez réessayer plus tard.';
            return $request->expectsJson()
                ? response()->json(['message' => $message], 503, ['Retry-After' => '30'])
                : response($message, 503, ['Content-Type' => 'text/plain; charset=UTF-8', 'Retry-After' => '30']);
        }

        // Les exceptions métier suivent le gestionnaire Laravel. Ne jamais relancer
        // une action d'écriture après une exception du contrôleur.
        return $next($request);
    }
}
