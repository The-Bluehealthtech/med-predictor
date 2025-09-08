<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountRequestController extends Controller
{
    public function create()
    {
        return view('account-request.create');
    }

    public function store(Request $request)
    {
        // Validation basique
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'organization_type' => 'required|string',
            'football_type' => 'required|string',
            'fifa_connect_type' => 'required|string',
            'message' => 'nullable|string|max:1000'
        ]);

        // Ici vous pouvez ajouter la logique pour sauvegarder la demande
        // Pour l'instant, on retourne un message de succès
        
        return redirect()->back()->with('success', 'Votre demande de compte a été envoyée avec succès. Nous vous contacterons bientôt.');
    }
}