<?php

namespace App\Console\Commands;

use App\Models\FhirOrder;
use App\Services\Fhir\FhirOrders;
use Illuminate\Console\Command;

/** Transmet au serveur FHIR les examens prescrits restés en attente ou en échec (mise en service du serveur). */
class FhirOrdersResend extends Command
{
    protected $signature = 'fhir:orders:resend {--limit=200 : Nombre maximum de prescriptions traitées}';

    protected $description = 'Renvoi des demandes d\'examen (ServiceRequest) en attente ou en échec';

    public function handle(FhirOrders $orders): int
    {
        if (!config('fhir.base_url')) {
            $this->error('Serveur FHIR de FIT non configuré (FIT_FHIR_BASE_URL).');

            return self::FAILURE;
        }
        $sent = $failed = 0;
        FhirOrder::query()->whereIn('status', ['pending', 'error'])->with(['visit', 'player'])->orderBy('id')
            ->limit((int) $this->option('limit'))->get()->each(function (FhirOrder $order) use ($orders, &$sent, &$failed) {
                $orders->retry($order);
                $order->refresh()->status === 'active' ? $sent++ : $failed++;
            });
        $this->info("{$sent} demande(s) transmise(s), {$failed} en échec (détail sur le tableau de bord du secrétariat).");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
