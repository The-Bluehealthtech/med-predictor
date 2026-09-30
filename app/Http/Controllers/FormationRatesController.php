<?php

namespace App\Http\Controllers;

final class FormationRatesController extends Controller
{
    public function index()
    {
        // Aucune table, version officielle ou source de barèmes n'est configurée.
        // Un taux fictif ne doit pas devenir une donnée comptable.
        return response()->json(['success' => false, 'data' => [],
            'message' => 'Barèmes de formation indisponibles : source officielle non configurée.'], 503);
    }
}
