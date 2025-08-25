<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCommitteeMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['committee_member', 'association_admin', 'system_admin'])) {
            return response()->json(['message' => 'Accès refusé. Rôle de membre du comité requis.'], 403);
        }

        return $next($request);
    }
}
