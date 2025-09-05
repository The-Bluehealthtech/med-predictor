<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseFallback
{
    public function handle(Request $request, Closure $next)
    {
        // Test de connexion à la base de données
        try {
            DB::connection()->getPdo();
            // Base accessible, on continue normalement
            return $next($request);
        } catch (\Exception $e) {
            // Base inaccessible, on ajoute des données simulées à la requête
            $request->merge([
                'db_fallback' => true,
                'simulated_data' => [
                    'players' => [
                        'total' => 25,
                        'active' => 22,
                        'avg_age' => 24.5
                    ],
                    'clubs' => [
                        'total' => 8,
                        'associations' => 3,
                        'confederations' => 2
                    ],
                    'performance' => [
                        'stats' => 156,
                        'avg_goals' => 12.3,
                        'avg_assists' => 8.7
                    ]
                ]
            ]);
            
            return $next($request);
        }
    }
}



