<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        // Vérifier que la locale est valide
        if (!in_array($locale, ['fr', 'en'])) {
            return Redirect::back()->with('error', 'Langue non supportée');
        }

        // Sauvegarder la locale dans la session
        Session::put('locale', $locale);
        App::setLocale($locale);
        Session::save();

        // Logger le changement
        Log::info('Language switched', [
            'locale' => $locale,
            'session_id' => Session::getId(),
            'session_locale' => Session::get('locale'),
            'user_id' => auth()->id() ?? 'guest'
        ]);

        return Redirect::back()->with('success', 'Langue changée avec succès');
    }
} 