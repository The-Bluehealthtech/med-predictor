<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if ($this->app->isDownForMaintenance()) {
            $data = $this->app->maintenanceMode()->data();
            
            if (isset($data['secret']) && $request->path() === $data['secret']) {
                return $next($request);
            }

            if ($this->inExceptArray($request)) {
                return $next($request);
            }

            abort(503);
        }

        return $next($request);
    }
}
