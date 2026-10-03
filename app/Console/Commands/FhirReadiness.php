<?php

namespace App\Console\Commands;

use App\Services\Fhir\ReadinessCheck;
use Illuminate\Console\Command;

/** Vérification de mise en service de la chaîne FHIR (aussi disponible sur la page d'administration « Mise en service FHIR »). */
class FhirReadiness extends Command
{
    protected $signature = 'fhir:readiness';

    protected $description = 'Vérification de mise en service de la chaîne FHIR (serveur, sécurité, abonnement, consentement, imagerie)';

    public function handle(ReadinessCheck $check): int
    {
        $rows = $check->run();
        $this->table(['Point', 'État', 'Détail / action'], array_map(fn ($r) => [$r['label'], ['ok' => '✓', 'warn' => '! à faire', 'fail' => '✗ bloquant'][$r['state']], $r['detail']], $rows));
        $failed = collect($rows)->where('state', 'fail')->count();
        $this->line($failed === 0 ? 'Chaîne FHIR prête (points « à faire » : non bloquants).' : "{$failed} point(s) bloquant(s).");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
