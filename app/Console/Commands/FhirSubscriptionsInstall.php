<?php

namespace App\Console\Commands;

use App\Services\Fhir\FhirOrders;
use Illuminate\Console\Command;

/** Installe sur le serveur FHIR l'abonnement rest-hook qui prévient FIT à chaque compte rendu. */
class FhirSubscriptionsInstall extends Command
{
    protected $signature = 'fhir:subscriptions:install {--endpoint= : URL publique du point de notification (par défaut APP_URL/api/fhir/notify)}';

    protected $description = 'Abonnement FHIR R4 (rest-hook) aux comptes rendus des examens prescrits';

    public function handle(FhirOrders $orders): int
    {
        if (!config('fhir.base_url') || !config('fhir.webhook_secret')) {
            $this->error('FIT_FHIR_BASE_URL et FIT_FHIR_WEBHOOK_SECRET doivent être configurés.');

            return self::FAILURE;
        }
        $endpoint = $this->option('endpoint') ?: rtrim((string) config('app.url'), '/') . '/api/fhir/notify';
        if (!str_starts_with($endpoint, 'https://')) {
            $this->error('Le point de notification doit être en HTTPS.');

            return self::FAILURE;
        }
        $subscription = $orders->installSubscription($endpoint);
        $this->info('Abonnement installé : Subscription/' . ($subscription['id'] ?? '?') . ' → ' . $endpoint);

        return self::SUCCESS;
    }
}
