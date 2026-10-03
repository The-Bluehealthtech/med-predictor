<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Fhir\ConformanceCheck;
use App\Services\Fhir\FhirException;
use App\Services\Fhir\FhirOrders;
use App\Services\Fhir\ReadinessCheck;
use App\Models\FhirOrder;
use Illuminate\Http\Request;

/**
 * Mise en service de la chaîne FHIR depuis l'interface (le service web n'offre pas de shell) :
 * vérification complète, conformité détaillée aux acteurs IHE, installation de l'abonnement des
 * comptes rendus, renvoi des examens en attente. Administrateur système uniquement.
 */
class FhirSetupController extends Controller
{
    public function index(Request $request, ReadinessCheck $readiness, ConformanceCheck $conformance)
    {
        $this->authorizeAdmin($request);
        $details = null;
        $error = null;
        if ($request->boolean('conformance') && config('fhir.base_url')) {
            try {
                $details = $conformance->run();
            } catch (FhirException $e) {
                $error = $e->getMessage();
            }
        }

        return view('admin.fhir-setup', ['rows' => $readiness->run(), 'details' => $details, 'error' => $error,
            'endpoint' => rtrim((string) config('app.url'), '/') . '/api/fhir/notify']);
    }

    public function installSubscription(Request $request, FhirOrders $orders)
    {
        $this->authorizeAdmin($request);
        $endpoint = rtrim((string) config('app.url'), '/') . '/api/fhir/notify';
        if (!config('fhir.base_url') || !config('fhir.webhook_secret') || !str_starts_with($endpoint, 'https://')) {
            return back()->with('error', 'FIT_FHIR_BASE_URL, FIT_FHIR_WEBHOOK_SECRET et un APP_URL en HTTPS sont nécessaires.');
        }
        try {
            $subscription = $orders->installSubscription($endpoint);
        } catch (FhirException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Abonnement installé : Subscription/' . ($subscription['id'] ?? '?') . ' → ' . $endpoint);
    }

    public function resendOrders(Request $request, FhirOrders $orders)
    {
        $this->authorizeAdmin($request);
        abort_unless(config('fhir.base_url'), 422, 'Serveur FHIR non configuré.');
        $sent = $failed = 0;
        FhirOrder::query()->whereIn('status', ['pending', 'error'])->with(['visit', 'player'])->orderBy('id')->limit(200)->get()
            ->each(function (FhirOrder $order) use ($orders, &$sent, &$failed) {
                $orders->retry($order);
                $order->refresh()->status === 'active' ? $sent++ : $failed++;
            });

        return back()->with($failed ? 'error' : 'success', "{$sent} demande(s) d’examen transmise(s), {$failed} en échec.");
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(in_array($request->user()?->role, ['system_admin', 'super_admin'], true), 403);
    }
}
