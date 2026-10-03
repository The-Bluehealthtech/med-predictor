<?php

namespace Tests\Feature;

use App\Services\Fhir\ConformanceCheck;
use App\Services\Fhir\FhirClient;
use App\Services\Fhir\FhirException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Serveur FHIR de FIT : client REST FHIR R4 et contrôle de conformité aux
 * CapabilityStatement officiels des acteurs IHE / HL7 (resources/fhir/ihe).
 */
class FhirConformanceTest extends TestCase
{
    private const BASE = 'http://fit-fhir.test/fhir';

    protected function setUp(): void
    {
        parent::setUp();
        config(['fhir.base_url' => self::BASE]);
    }

    /** CapabilityStatement d'un serveur qui tient tous les acteurs attendus (union des déclarations officielles). */
    private function compliantServer(array $without = []): array
    {
        $resources = [];
        $system = ['interaction' => [], 'operation' => []];
        foreach (config('fhir.actors') as $actor) {
            $rest = json_decode(file_get_contents(resource_path('fhir/ihe/' . $actor['file'])), true)['rest'][0];
            foreach (['interaction', 'operation'] as $kind) {
                foreach ($rest[$kind] ?? [] as $item) {
                    $system[$kind][$item['code'] ?? $item['name']] = array_intersect_key($item, array_flip(['code', 'name']));
                }
            }
            foreach ($rest['resource'] ?? [] as $resource) {
                $merged = $resources[$resource['type']] ?? ['type' => $resource['type'], 'interaction' => [], 'searchParam' => [], 'operation' => []];
                foreach ($resource['interaction'] ?? [] as $i) {
                    $merged['interaction'][$i['code']] = ['code' => $i['code']];
                }
                foreach ($resource['searchParam'] ?? [] as $p) {
                    $name = explode(':', $p['name'])[0];
                    $merged['searchParam'][$name] = ['name' => $name, 'type' => $p['type'] ?? 'token'];
                }
                foreach ($resource['operation'] ?? [] as $o) {
                    $merged['operation'][$o['name']] = ['name' => ltrim($o['name'], '$'), 'definition' => $o['definition'] ?? ''];
                }
                $resources[$resource['type']] = $merged;
            }
        }
        foreach ($without as $path) {
            data_forget($resources, $path);
        }

        return ['resourceType' => 'CapabilityStatement', 'fhirVersion' => '4.0.1', 'rest' => [[
            'mode' => 'server',
            'interaction' => array_values($system['interaction']),
            'operation' => array_values($system['operation']),
            'resource' => array_values(array_map(fn ($r) => ['type' => $r['type'], 'interaction' => array_values($r['interaction']),
                'searchParam' => array_values($r['searchParam']), 'operation' => array_values($r['operation'])], $resources)),
        ]]];
    }

    private function fakeServer(array $capabilities, bool $guides = true): void
    {
        Http::fake([
            self::BASE . '/metadata' => Http::response($capabilities, 200, ['Content-Type' => FhirClient::MIME]),
            self::BASE . '/StructureDefinition*' => Http::response(['resourceType' => 'Bundle', 'type' => 'searchset', 'total' => $guides ? 1 : 0]),
        ]);
    }

    public function test_client_speaks_fhir_json_and_surfaces_the_operation_outcome(): void
    {
        Http::fake([
            self::BASE . '/Patient?identifier=*' => Http::response(['resourceType' => 'Patient', 'id' => '1'], 200),
            self::BASE . '/Observation' => Http::response(['resourceType' => 'OperationOutcome', 'issue' => [['severity' => 'error', 'code' => 'processing', 'diagnostics' => 'Profile violation']]], 422),
        ]);
        $client = app(FhirClient::class);

        $client->conditionalUpdate(['resourceType' => 'Patient'], ['identifier' => 'https://example.org/sid|42']);
        Http::assertSent(fn (Request $r) => $r->method() === 'PUT' && $r->hasHeader('Accept', FhirClient::MIME) && $r->hasHeader('Content-Type', FhirClient::MIME)
            && $r->url() === self::BASE . '/Patient?identifier=' . rawurlencode('https://example.org/sid|42'));

        try {
            $client->create(['resourceType' => 'Observation']);
            $this->fail('erreur serveur non remontée');
        } catch (FhirException $e) {
            $this->assertSame(422, $e->getCode());
            $this->assertSame(['error processing Profile violation'], $e->issues());
        }
    }

    public function test_unconfigured_server_is_reported_without_any_request(): void
    {
        config(['fhir.base_url' => null]);
        Http::fake();

        $this->assertSame(1, Artisan::call('fhir:conformance'));
        $this->assertStringContainsString('non configuré', Artisan::output());
        Http::assertNothingSent();
    }

    public function test_server_holding_every_official_actor_requirement_is_conformant(): void
    {
        $this->fakeServer($this->compliantServer());

        $result = app(ConformanceCheck::class)->run();
        $this->assertTrue($result['ok']);
        $this->assertSame(array_keys(config('fhir.actors')), array_keys($result['actors']));
        $this->assertSame(0, Artisan::call('fhir:conformance'));
        $this->assertStringContainsString('Serveur conforme aux exigences SHALL', Artisan::output());
    }

    public function test_missing_shall_requirements_fail_and_are_listed(): void
    {
        // PIXm Manager sans $ihe-pix ; QEDm sans la recherche Observation par catégorie (combinaison SHALL).
        $server = $this->compliantServer();
        foreach ($server['rest'][0]['resource'] as &$resource) {
            if ($resource['type'] === 'Patient') {
                $resource['operation'] = array_values(array_filter($resource['operation'], fn ($o) => $o['name'] !== 'ihe-pix'));
            }
            if ($resource['type'] === 'Observation') {
                $resource['searchParam'] = array_values(array_filter($resource['searchParam'], fn ($p) => $p['name'] !== 'category'));
            }
        }
        unset($resource);
        $this->fakeServer($server);

        $result = app(ConformanceCheck::class)->run();
        $this->assertFalse($result['ok']);
        $missing = fn (string $actor) => collect($result['actors'][$actor]['findings'])->where('ok', false)->map(fn ($f) => $f['level'] . ' ' . $f['requirement'])->values()->all();
        $this->assertSame(['SHALL Patient : opération $ihe-pix'], $missing('pixm_manager'));
        $this->assertSame(['SHALL Observation : recherche category'], $missing('qedm_source'));

        $this->assertSame(1, Artisan::call('fhir:conformance', ['--actor' => ['pixm_manager']]));
        $this->assertStringContainsString('Patient : opération $ihe-pix', Artisan::output());
    }

    public function test_optional_resource_absence_is_only_a_should_gap(): void
    {
        // QEDm : Encounter est SHOULD ; son absence n'impose pas ses combinaisons SHALL.
        $server = $this->compliantServer();
        $server['rest'][0]['resource'] = array_values(array_filter($server['rest'][0]['resource'], fn ($r) => $r['type'] !== 'Encounter'));
        $this->fakeServer($server);

        $findings = collect(app(ConformanceCheck::class)->run(['qedm_source'])['actors']['qedm_source']['findings'])->where('ok', false);
        $this->assertNotEmpty($findings);
        $this->assertSame(['SHOULD'], $findings->pluck('level')->unique()->values()->all());
    }

    public function test_missing_implementation_guide_or_wrong_fhir_version_fails(): void
    {
        $server = $this->compliantServer();
        $server['fhirVersion'] = '5.0.0';
        $this->fakeServer($server, false);

        $result = app(ConformanceCheck::class)->run();
        $this->assertFalse($result['version']['ok']);
        $this->assertFalse($result['guides'][0]['ok']);
        $this->assertFalse($result['ok']);
    }
}
