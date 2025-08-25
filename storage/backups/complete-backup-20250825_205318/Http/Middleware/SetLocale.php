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
        // Get locale from session, fallback to default
        $sessionLocale = Session::get('locale');
        $currentLocale = App::getLocale();
        
        // Debug logging
        Log::info('SetLocale Middleware', [
            'session_locale' => $sessionLocale,
            'current_locale' => $currentLocale,
            'request_url' => $request->url()
        ]);
        
        // If session has a locale and it's different from current, set it
        if ($sessionLocale && in_array($sessionLocale, ['fr', 'en'])) {
            App::setLocale($sessionLocale);
            Log::info('Locale set to: ' . $sessionLocale);
        }
        
        return $next($request);
    }
} 