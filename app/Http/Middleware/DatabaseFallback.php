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
        } catch (\Throwable $exception) {
            $message = $request->getPreferredLanguage(['fr', 'en']) === 'en'
                ? 'Service temporarily unavailable. Please try again later.'
                : 'Service temporairement indisponible. Veuillez réessayer plus tard.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 503, ['Retry-After' => '30'])
                : response($message, 503, [
                    'Content-Type' => 'text/plain; charset=UTF-8',
                    'Retry-After' => '30',
                ]);
        }

        return $next($request);
    }
}
