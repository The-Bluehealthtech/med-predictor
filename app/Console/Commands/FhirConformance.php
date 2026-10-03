<?php

namespace App\Console\Commands;

use App\Services\Fhir\ConformanceCheck;
use App\Services\Fhir\FhirClient;
use App\Services\Fhir\FhirException;
use Illuminate\Console\Command;

/**
 * Conformité du serveur FHIR de FIT aux acteurs IHE / HL7 attendus (PDQm, PIXm, MHD,
 * QEDm, BALP, IPS) : version FHIR, guides installés, CapabilityStatement comparé aux
 * CapabilityStatement officiels. Lecture seule ; échec si une exigence SHALL manque.
 */
class FhirConformance extends Command
{
    protected $signature = 'fhir:conformance {--actor=* : Acteurs à contrôler (clés de config/fhir.php), tous par défaut} {--all : Afficher aussi les exigences satisfaites}';

    protected $description = 'Conformité du serveur FHIR de FIT aux profils IHE / HL7 (CapabilityStatement, guides installés)';

    public function handle(FhirClient $client, ConformanceCheck $check): int
    {
        if (!$client->configured()) {
            $this->error('Serveur FHIR de FIT non configuré (FIT_FHIR_BASE_URL) : installation prévue avant la mise en production.');

            return self::FAILURE;
        }
        try {
            $result = $check->run($this->option('actor') ?: null);
        } catch (FhirException $e) {
            $this->error($e->getMessage());
            foreach ($e->issues() as $issue) {
                $this->line('  ' . $issue);
            }

            return self::FAILURE;
        }

        $this->line(sprintf('Version FHIR : %s (attendue %s) %s', $result['version']['actual'] ?? '—', $result['version']['expected'], $result['version']['ok'] ? '✓' : '✗'));
        $this->table(['Guide', 'Profil témoin', ''], array_map(fn ($g) => [$g['package'], $g['profile'], $g['ok'] ? '✓' : '✗ absent'], $result['guides']));

        foreach ($result['actors'] as $actor) {
            $findings = collect($actor['findings']);
            $missingShall = $findings->where('ok', false)->where('level', 'SHALL')->count();
            $missingOther = $findings->where('ok', false)->where('level', '!=', 'SHALL')->count();
            $this->newLine();
            $this->line(sprintf('<options=bold>%s</> — %d exigence(s), %d SHALL manquante(s), %d SHOULD/MAY manquante(s)', $actor['label'], $findings->count(), $missingShall, $missingOther));
            $rows = $findings->filter(fn ($f) => $this->option('all') || !$f['ok'])
                ->map(fn ($f) => [$f['level'], $f['requirement'], $f['ok'] ? '✓' : '✗'])->values()->all();
            if ($rows) {
                $this->table(['Niveau', 'Exigence', ''], $rows);
            }
        }

        $this->newLine();
        $this->line($result['ok'] ? 'Serveur conforme aux exigences SHALL.' : 'Serveur non conforme : exigences SHALL manquantes.');

        return $result['ok'] ? self::SUCCESS : self::FAILURE;
    }
}
