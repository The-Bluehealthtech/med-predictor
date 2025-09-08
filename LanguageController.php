<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Change the application language
     */
    public function switchLanguage(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, ['fr', 'en'])) {
            abort(404);
        }

        // Set locale in session
        Session::put('locale', $locale);
        
        // Set locale for current request
        App::setLocale($locale);

        // Redirect back to previous page or home
        return redirect()->back()->with('success', __('common.language_changed'));
    }

    /**
     * Get current language
     */
    public function getCurrentLanguage()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'session_locale' => Session::get('locale', config('app.locale'))
        ]);
    }
}


