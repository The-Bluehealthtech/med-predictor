<?php

namespace App\Http\Controllers\Fhir;

use App\Http\Controllers\Controller;
use App\Services\Fhir\FhirException;
use App\Services\Fhir\FhirOrders;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Point de notification de l'abonnement FHIR R4 (rest-hook sans contenu) : le serveur FHIR de
 * FIT signale un compte rendu ; FIT relit lui-même les comptes rendus des prescriptions actives.
 * Accès par secret partagé (en-tête Authorization), aucune donnée reçue n'est utilisée.
 */
class SubscriptionNotificationController extends Controller
{
    public function __invoke(Request $request, FhirOrders $orders): JsonResponse
    {
        $secret = (string) config('fhir.webhook_secret');
        abort_unless($secret !== '' && hash_equals('Bearer ' . $secret, (string) $request->header('Authorization')), 401);
        try {
            $completed = $orders->sync();
        } catch (FhirException $e) {
            Log::warning('Synchronisation FHIR des comptes rendus en échec', ['error' => $e->getMessage()]);

            return response()->json(['status' => 'error'], 502);
        }

        return response()->json(['status' => 'ok', 'completed' => $completed]);
    }
}
