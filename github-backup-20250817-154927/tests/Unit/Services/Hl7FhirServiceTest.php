<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Hl7FhirService;
use App\Models\Player;
use App\Models\Performance;
use App\Models\MedicalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;

class Hl7FhirServiceTest extends TestCase
{
    use RefreshDatabase;

    private Hl7FhirService $hl7Service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hl7Service = new Hl7FhirService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_create_patient_resource()
    {
        // Arrange
        $player = Player::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1995-01-15',
            'nationality' => 'France',
            'gender' => 'male'
        ]);

        $fhirResponse = [
            'resourceType' => 'Patient',
            'id' => 'patient-12345',
            'identifier' => [
                [
                    'system' => 'https://fhir.example.com/players',
                    'value' => $player->id
                ]
            ],
            'name' => [
                [
                    'use' => 'official',
                    'family' => 'Doe',
                    'given' => ['John']
                ]
            ],
            'gender' => 'male',
            'birthDate' => '1995-01-15'
        ];

        Http::fake([
            'fhir-server.com/Patient' => Http::response($fhirResponse, 201)
        ]);

        // Act
        $result = $this->hl7Service->createPatientResource($player);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Patient resource created successfully', $result['message']);
        $this->assertEquals('patient-12345', $result['data']['id']);
    }

    /** @test */
    public function it_can_update_patient_resource()
    {
        // Arrange
        $player = Player::factory()->create([
            'fhir_patient_id' => 'patient-12345',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $fhirResponse = [
            'resourceType' => 'Patient',
            'id' => 'patient-12345',
            'name' => [
                [
                    'use' => 'official',
                    'family' => 'Doe',
                    'given' => ['John']
                ]
            ]
        ];

        Http::fake([
            'fhir-server.com/Patient/patient-12345' => Http::response($fhirResponse, 200)
        ]);

        // Act
        $result = $this->hl7Service->updatePatientResource($player);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Patient resource updated successfully', $result['message']);
    }

    /** @test */
    public function it_can_create_performance_observation()
    {
        // Arrange
        $player = Player::factory()->create(['fhir_patient_id' => 'patient-12345']);
        $performance = Performance::factory()->create([
            'player_id' => $player->id,
            'match_date' => '2024-01-15',
            'distance_covered' => 10500,
            'sprint_count' => 25,
            'max_speed' => 32.5
        ]);

        $fhirResponse = [
            'resourceType' => 'Observation',
            'id' => 'observation-67890',
            'subject' => [
                'reference' => 'Patient/patient-12345'
            ],
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => 'performance-metrics',
                        'display' => 'Sports Performance Metrics'
                    ]
                ]
            ],
            'effectiveDateTime' => '2024-01-15',
            'valueQuantity' => [
                'value' => 10500,
                'unit' => 'meters',
                'system' => 'http://unitsofmeasure.org',
                'code' => 'm'
            ]
        ];

        Http::fake([
            'fhir-server.com/Observation' => Http::response($fhirResponse, 201)
        ]);

        // Act
        $result = $this->hl7Service->createPerformanceObservation($performance);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Performance observation created successfully', $result['message']);
        $this->assertEquals('observation-67890', $result['data']['id']);
    }

    /** @test */
    public function it_can_create_document_reference()
    {
        // Arrange
        $player = Player::factory()->create(['fhir_patient_id' => 'patient-12345']);
        $medicalRecord = MedicalRecord::factory()->create([
            'player_id' => $player->id,
            'document_type' => 'medical_clearance',
            'file_path' => '/documents/medical_clearance.pdf'
        ]);

        $fhirResponse = [
            'resourceType' => 'DocumentReference',
            'id' => 'document-11111',
            'subject' => [
                'reference' => 'Patient/patient-12345'
            ],
            'type' => [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => 'medical-clearance',
                        'display' => 'Medical Clearance Certificate'
                    ]
                ]
            ],
            'content' => [
                [
                    'attachment' => [
                        'contentType' => 'application/pdf',
                        'url' => 'https://example.com/documents/medical_clearance.pdf'
                    ]
                ]
            ]
        ];

        Http::fake([
            'fhir-server.com/DocumentReference' => Http::response($fhirResponse, 201)
        ]);

        // Act
        $result = $this->hl7Service->createDocumentReference($medicalRecord);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Document reference created successfully', $result['message']);
        $this->assertEquals('document-11111', $result['data']['id']);
    }

    /** @test */
    public function it_validates_fhir_resources()
    {
        // Arrange
        $invalidPatient = [
            'resourceType' => 'Patient',
            'name' => 'Invalid name format' // Should be an array
        ];

        // Act
        $result = $this->hl7Service->validateResource($invalidPatient);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('validation', strtolower($result['message']));
    }

    /** @test */
    public function it_handles_fhir_server_errors()
    {
        // Arrange
        $player = Player::factory()->create();

        Http::fake([
            'fhir-server.com/Patient' => Http::response(['error' => 'Server error'], 500)
        ]);

        // Act
        $result = $this->hl7Service->createPatientResource($player);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals('Failed to create patient resource: Server error', $result['message']);
        $this->assertEquals(500, $result['status_code']);
    }

    /** @test */
    public function it_can_search_patient_resources()
    {
        // Arrange
        $searchParams = [
            'identifier' => 'player-12345',
            'name' => 'John Doe'
        ];

        $fhirResponse = [
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'entry' => [
                [
                    'resource' => [
                        'resourceType' => 'Patient',
                        'id' => 'patient-12345',
                        'name' => [
                            [
                                'family' => 'Doe',
                                'given' => ['John']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        Http::fake([
            'fhir-server.com/Patient*' => Http::response($fhirResponse, 200)
        ]);

        // Act
        $result = $this->hl7Service->searchPatientResources($searchParams);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals(1, count($result['data']['entry']));
        $this->assertEquals('patient-12345', $result['data']['entry'][0]['resource']['id']);
    }

    /** @test */
    public function it_can_retrieve_patient_history()
    {
        // Arrange
        $player = Player::factory()->create(['fhir_patient_id' => 'patient-12345']);

        $fhirResponse = [
            'resourceType' => 'Bundle',
            'type' => 'history',
            'entry' => [
                [
                    'resource' => [
                        'resourceType' => 'Observation',
                        'id' => 'observation-1',
                        'effectiveDateTime' => '2024-01-15'
                    ]
                ],
                [
                    'resource' => [
                        'resourceType' => 'Observation',
                        'id' => 'observation-2',
                        'effectiveDateTime' => '2024-01-10'
                    ]
                ]
            ]
        ];

        Http::fake([
            'fhir-server.com/Patient/patient-12345/_history*' => Http::response($fhirResponse, 200)
        ]);

        // Act
        $result = $this->hl7Service->getPatientHistory($player->fhir_patient_id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals(2, count($result['data']['entry']));
    }

    /** @test */
    public function it_logs_fhir_operations_for_audit()
    {
        // Arrange
        $player = Player::factory()->create();

        Http::fake([
            'fhir-server.com/Patient' => Http::response(['resourceType' => 'Patient', 'id' => 'patient-12345'], 201)
        ]);

        Log::shouldReceive('info')->once();

        // Act
        $this->hl7Service->createPatientResource($player);

        // Assert
        // Log::info should have been called
    }

    /** @test */
    public function it_handles_network_timeouts_gracefully()
    {
        // Arrange
        $player = Player::factory()->create();

        Http::fake([
            'fhir-server.com/Patient' => Http::timeout()
        ]);

        // Act
        $result = $this->hl7Service->createPatientResource($player);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('timeout', strtolower($result['message']));
    }

    /** @test */
    public function it_validates_required_fhir_fields()
    {
        // Arrange
        $player = Player::factory()->create([
            'first_name' => '', // Missing required field
            'last_name' => 'Doe'
        ]);

        // Act
        $result = $this->hl7Service->createPatientResource($player);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('validation', strtolower($result['message']));
    }

    /** @test */
    public function it_can_batch_create_multiple_resources()
    {
        // Arrange
        $players = Player::factory()->count(3)->create();

        $fhirResponse = [
            'resourceType' => 'Bundle',
            'type' => 'transaction-response',
            'entry' => [
                ['resource' => ['resourceType' => 'Patient', 'id' => 'patient-1']],
                ['resource' => ['resourceType' => 'Patient', 'id' => 'patient-2']],
                ['resource' => ['resourceType' => 'Patient', 'id' => 'patient-3']]
            ]
        ];

        Http::fake([
            'fhir-server.com' => Http::response($fhirResponse, 200)
        ]);

        // Act
        $result = $this->hl7Service->batchCreateResources($players, 'Patient');

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals(3, count($result['data']['entry']));
    }
} 