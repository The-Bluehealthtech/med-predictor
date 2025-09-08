<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountRequest;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class AccountRequestController extends Controller
{
    public function store(Request $request)
    {
        // Validation des champs obligatoires
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'organization_name' => 'nullable|string|max:255',
            'organization_type' => 'nullable|string|max:100',
            'football_type' => 'nullable|string|max:100',
            'association_id' => 'nullable|string|max:100',
            'fifa_connect_type' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            // Créer la demande de compte
            $accountRequest = AccountRequest::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'organization_name' => $validated['organization_name'] ?? null,
                'organization_type' => $validated['organization_type'] ?? null,
                'football_type' => $validated['football_type'] ?? null,
                'country' => null,
                'association_id' => $validated['association_id'] ?? null,
                'fifa_connect_type' => $validated['fifa_connect_type'] ?? null,
                'city' => $validated['city'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
            ]);

            // Envoyer la notification aux administrateurs
            $notificationService = app(NotificationService::class);
            $notificationService->sendAccountRequestSubmitted($accountRequest);

            // Retourner une page de succès avec redirection automatique
            return view('account-request.success', [
                'message' => app()->getLocale() === 'fr'
                    ? 'Votre demande de compte a été soumise avec succès ! Nous vous contacterons bientôt.'
                    : 'Your account request has been submitted successfully! We will contact you soon.'
            ]);
        } catch (\Exception $e) {
            Log::error('Account request submission error: ' . $e->getMessage());
            return back()->withErrors([
                'error' => app()->getLocale() === 'fr'
                    ? 'Une erreur est survenue lors de la soumission. Veuillez réessayer.'
                    : 'An error occurred during submission. Please try again.'
            ]);
        }
    }
}
