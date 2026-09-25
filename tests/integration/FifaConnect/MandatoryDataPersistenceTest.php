<?php

namespace Tests\Integration\FifaConnect;

use App\Services\FifaConnect\MandatoryDataService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MandatoryDataPersistenceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_authoritative_parts_keep_order_subpath_and_provenance(): void
    {
        $service = app(MandatoryDataService::class);
        $data = $service->persist([
            'service_name' => 'RegistrationService',
            'parameter_name' => 'PersonLocal',
            'parts' => [
                ['path' => '/PersonLocal/@Nationality', 'is_attribute' => true],
                ['path' => '/PersonLocal/PlayerRegistration', 'is_attribute' => false,
                    'sub_path' => ['path' => '@Discipline', 'is_attribute' => true]],
            ],
        ], 'authorized-fixture:mandatory-data-1');
        $parts = $data->parts()->with('subPath')->get();
        $this->assertSame(2, $parts->count());
        $this->assertSame('/PersonLocal/@Nationality', $parts[0]->path);
        $this->assertSame(1, $parts[0]->order_number);
        $this->assertSame('/PersonLocal/PlayerRegistration', $parts[1]->path);
        $this->assertSame(2, $parts[1]->order_number);
        $this->assertSame('@Discipline', $parts[1]->subPath->path);
        $this->assertTrue($parts[1]->subPath->is_attribute);
        $this->assertSame('authorized-fixture:mandatory-data-1', $data->source_reference);
        $this->assertSame(64, strlen($data->payload_hash));

        $updated = $service->persist([
            'service_name' => 'RegistrationService',
            'parameter_name' => 'PersonLocal',
            'parts' => [['path' => '/PersonLocal/@Gender', 'is_attribute' => true]],
        ], 'authorized-fixture:mandatory-data-3');
        $this->assertSame($data->id, $updated->id);
        $this->assertSame(['/PersonLocal/@Gender'], $updated->parts->pluck('path')->all());
    }

    public function test_unknown_mandatory_part_fields_are_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        app(MandatoryDataService::class)->persist([
            'service_name' => 'RegistrationService',
            'parameter_name' => 'PersonLocal',
            'parts' => [[
                'path' => '/PersonLocal/@Nationality',
                'is_attribute' => true,
                'unmapped_value' => 'must not disappear',
            ]],
        ], 'authorized-fixture:mandatory-data-2');
    }
}
