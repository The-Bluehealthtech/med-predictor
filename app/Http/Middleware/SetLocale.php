<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SetLocale
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
        $requestedLocale = $request->query('lang');

        if (in_array($requestedLocale, ['fr', 'en'], true)) {
            Session::put('locale', $requestedLocale);
        }

        $locale = Session::get('locale', config('app.locale', 'fr'));

        if (!in_array($locale, ['fr', 'en'], true)) {
            $locale = 'fr';
        }

        App::setLocale($locale);

        return $next($request);
    }
} 