<?php

namespace App\Console\Commands;

use App\Services\Fhir\FhirOrders;
use Illuminate\Console\Command;

/** Rattache les comptes rendus du serveur FHIR aux prescriptions d'examens actives (DiagnosticReport.basedOn). */
class FhirOrdersSync extends Command
{
    protected $signature = 'fhir:orders:sync';

    protected $description = 'Comptes rendus du serveur FHIR rattachés aux prescriptions d\'examens (ServiceRequest)';

    public function handle(FhirOrders $orders): int
    {
        $this->info($orders->sync() . ' prescription(s) passée(s) à « résultats reçus ».');

        return self::SUCCESS;
    }
}
