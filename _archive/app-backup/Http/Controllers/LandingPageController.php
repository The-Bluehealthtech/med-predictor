<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LandingPageController extends Controller
{
    public function index()
    {
        // Get locale from session first, then cookie, then fallback to 'fr'
        $locale = Session::get('locale', request()->cookie('locale', 'fr'));
        App::setLocale($locale);
        
        // Debug: Log the current locale
        Log::info("LandingPageController - Cookie locale: " . request()->cookie('locale'));
        Log::info("LandingPageController - Session locale: " . Session::get('locale'));
        Log::info("LandingPageController - App locale: " . App::getLocale());
        Log::info("LandingPageController - Current locale variable: " . $locale);
        Log::info("LandingPageController - app()->getLocale(): " . app()->getLocale());
        Log::info("LandingPageController - config('app.locale'): " . config('app.locale'));
        
        // Si l'utilisateur est connecté, rediriger vers le dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        // Sinon, afficher la landing page avec en-têtes de cache
        return response()->view('welcome')->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate, private',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT'
        ]);
    }
}
