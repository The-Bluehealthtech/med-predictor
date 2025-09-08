<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectLangGetRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('get') && $request->is('lang/*')) {
            // Redirige vers la page précédente ou l'accueil
            return redirect()->back(302)->with('status', __('Changement de langue : utilisez le menu.'));
        }
        return $next($request);
    }
} 