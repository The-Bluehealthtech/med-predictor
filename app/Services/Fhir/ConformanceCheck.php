<?php

namespace App\Services\Fhir;

/**
 * Conformité du serveur FHIR de FIT : son CapabilityStatement est comparé à celui,
 * officiel, de chaque acteur IHE / HL7 attendu (resources/fhir/ihe). Niveau d'exigence
 * lu dans l'extension capabilitystatement-expectation ; sans extension, l'élément est une
 * exigence de l'acteur (SHALL). Les guides sont contrôlés par la présence de leurs profils.
 */
final class ConformanceCheck
{
    private const EXPECTATION = 'http://hl7.org/fhir/StructureDefinition/capabilitystatement-expectation';

    private const COMBINATION = 'http://hl7.org/fhir/StructureDefinition/capabilitystatement-search-parameter-combination';

    public function __construct(private readonly FhirClient $client)
    {
    }

    /** @return array{version:array, guides:array, actors:array<string, array{label:string, findings:array}>, ok:bool} */
    public function run(?array $only = null): array
    {
        $server = $this->client->capabilities();
        $version = ['expected' => config('fhir.fhir_version'), 'actual' => $server['fhirVersion'] ?? null];
        $version['ok'] = $version['expected'] === $version['actual'];

        $guides = [];
        foreach (config('fhir.profiles', []) as $package => $profile) {
            $found = (int) ($this->client->search('StructureDefinition', ['url' => $profile, '_summary' => 'count'])['total'] ?? 0) > 0;
            $guides[] = ['package' => $package . '#' . (config('fhir.implementation_guides')[$package] ?? '?'), 'profile' => $profile, 'ok' => $found];
        }

        $actors = [];
        foreach (config('fhir.actors', []) as $key => $actor) {
            if ($only && !in_array($key, $only, true)) {
                continue;
            }
            $expected = json_decode((string) file_get_contents(resource_path('fhir/ihe/' . $actor['file'])), true, 512, JSON_THROW_ON_ERROR);
            $actors[$key] = ['label' => $actor['label'], 'findings' => $this->compare($expected, $server)];
        }

        $ok = $version['ok'] && !in_array(false, array_column($guides, 'ok'), true)
            && collect($actors)->every(fn ($a) => collect($a['findings'])->every(fn ($f) => $f['ok'] || $f['level'] !== 'SHALL'));

        return compact('version', 'guides', 'actors', 'ok');
    }

    /** @return list<array{level:string, requirement:string, ok:bool}> */
    public function compare(array $expected, array $server): array
    {
        $rest = collect($server['rest'] ?? [])->firstWhere('mode', 'server') ?? [];
        $resources = collect($rest['resource'] ?? [])->keyBy('type');
        $systemInteractions = collect($rest['interaction'] ?? [])->pluck('code')->all();
        $systemOperations = collect($rest['operation'] ?? [])->map(fn ($o) => ltrim($o['name'] ?? '', '$'))->all();
        $findings = [];

        foreach (($expected['rest'][0] ?? [])['interaction'] ?? [] as $interaction) {
            $findings[] = ['level' => $this->level($interaction), 'requirement' => "interaction système {$interaction['code']}", 'ok' => in_array($interaction['code'], $systemInteractions, true)];
        }
        foreach (($expected['rest'][0] ?? [])['operation'] ?? [] as $operation) {
            $name = ltrim($operation['name'], '$');
            $findings[] = ['level' => $this->level($operation), 'requirement' => "opération système \${$name}", 'ok' => in_array($name, $systemOperations, true)];
        }

        foreach (($expected['rest'][0] ?? [])['resource'] ?? [] as $want) {
            $type = $want['type'];
            $level = $this->level($want);
            $have = $resources->get($type);
            $findings[] = ['level' => $level, 'requirement' => "ressource {$type}", 'ok' => $have !== null];
            $start = count($findings);
            $interactions = collect($have['interaction'] ?? [])->pluck('code')->all();
            $params = collect($have['searchParam'] ?? [])->pluck('name')->all();
            $operations = collect($have['operation'] ?? [])->map(fn ($o) => ltrim($o['name'] ?? '', '$'))->merge($systemOperations)->all();

            foreach ($want['interaction'] ?? [] as $interaction) {
                $findings[] = ['level' => $this->level($interaction, $level), 'requirement' => "{$type} : interaction {$interaction['code']}", 'ok' => in_array($interaction['code'], $interactions, true)];
            }
            // Paramètres exigés : ceux d'une combinaison SHALL le sont, les autres héritent du niveau déclaré.
            $required = [];
            foreach ($want['extension'] ?? [] as $extension) {
                if (($extension['url'] ?? null) === self::COMBINATION && $this->level($extension, 'SHOULD') === 'SHALL') {
                    foreach ($extension['extension'] ?? [] as $part) {
                        if (($part['url'] ?? null) === 'required') {
                            $required[$part['valueString']] = true;
                        }
                    }
                }
            }
            $seen = [];
            foreach ($want['searchParam'] ?? [] as $param) {
                $name = explode(':', $param['name'])[0]; // modificateur (:exact) porté par le type du paramètre
                if (isset($seen[$name])) {
                    continue;
                }
                $seen[$name] = true;
                $paramLevel = isset($required[$name]) ? 'SHALL' : $this->level($param, $level);
                $findings[] = ['level' => $paramLevel, 'requirement' => "{$type} : recherche {$name}", 'ok' => in_array($name, $params, true)];
            }
            foreach ($want['operation'] ?? [] as $operation) {
                $name = ltrim($operation['name'], '$');
                $findings[] = ['level' => $this->level($operation, $level), 'requirement' => "{$type} : opération \${$name}", 'ok' => in_array($name, $operations, true)];
            }
            // Ressource facultative (SHOULD) absente : ses exigences internes ne s'appliquent pas au-delà de son niveau.
            if ($have === null && $level !== 'SHALL') {
                for ($i = $start; $i < count($findings); $i++) {
                    $findings[$i]['level'] = $level;
                }
            }
        }

        return $findings;
    }

    private function level(array $element, string $default = 'SHALL'): string
    {
        foreach ($element['extension'] ?? [] as $extension) {
            if (($extension['url'] ?? null) === self::EXPECTATION) {
                return $extension['valueCode'];
            }
        }

        return $default;
    }
}
