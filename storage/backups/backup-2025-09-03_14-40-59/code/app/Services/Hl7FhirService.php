<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Performance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class Hl7FhirService
{
    protected $baseUrl;
    protected $apiKey;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.hl7_fhir.base_url', 'https://fhir.example.com');
        $this->apiKey = config('services.hl7_fhir.api_key');
        $this->timeout = config('services.hl7_fhir.timeout', 30);
    }

    /**
     * Create a patient resource in FHIR
     */
    public function createPatientResource(Player $player): array
    {
        try {
            $patientData = [
                'resourceType' => 'Patient',
                'identifier' => [
                    [
                        'system' => 'https://med-predictor.com/players',
                        'value' => (string) $player->id
                    ],
                    [
                        'system' => 'https://fifa.com/players',
                        'value' => $player->fifa_connect_id
                    ]
                ],
                'name' => [
                    [
                        'use' => 'official',
                        'family' => $player->last_name,
                        'given' => [$player->first_name]
                    ]
                ],
                'gender' => $player->gender ?? 'unknown',
                'birthDate' => $player->birth_date?->format('Y-m-d'),
                'address' => [
                    [
                        'type' => 'physical',
                        'country' => $player->nationality
                    ]
                ],
                'extension' => [
                    [
                        'url' => 'https://med-predictor.com/player-position',
                        'valueString' => $player->position
                    ],
                    [
                        'url' => 'https://med-predictor.com/player-team',
                        'valueReference' => [
                            'reference' => "Organization/{$player->team_id}"
                        ]
                    ]
                ]
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/fhir+json',
                    'Accept' => 'application/fhir+json'
                ])
                ->post("{$this->baseUrl}/Patient", $patientData);

            if ($response->successful()) {
                $result = $response->json();
                
                // Update player with FHIR patient ID
                $player->update([
                    'fhir_patient_id' => $result['id'] ?? null,
                    'fhir_data' => json_encode($result)
                ]);

                return [
                    'success' => true,
                    'patient_id' => $result['id'],
                    'data' => $result
                ];
            }

            throw new Exception("FHIR API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FHIR patient creation error', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update a patient resource in FHIR
     */
    public function updatePatientResource(Player $player): array
    {
        try {
            if (!$player->fhir_patient_id) {
                return $this->createPatientResource($player);
            }

            $patientData = [
                'resourceType' => 'Patient',
                'id' => $player->fhir_patient_id,
                'identifier' => [
                    [
                        'system' => 'https://med-predictor.com/players',
                        'value' => (string) $player->id
                    ],
                    [
                        'system' => 'https://fifa.com/players',
                        'value' => $player->fifa_connect_id
                    ]
                ],
                'name' => [
                    [
                        'use' => 'official',
                        'family' => $player->last_name,
                        'given' => [$player->first_name]
                    ]
                ],
                'gender' => $player->gender ?? 'unknown',
                'birthDate' => $player->birth_date?->format('Y-m-d'),
                'address' => [
                    [
                        'type' => 'physical',
                        'country' => $player->nationality
                    ]
                ],
                'extension' => [
                    [
                        'url' => 'https://med-predictor.com/player-position',
                        'valueString' => $player->position
                    ],
                    [
                        'url' => 'https://med-predictor.com/player-team',
                        'valueReference' => [
                            'reference' => "Organization/{$player->team_id}"
                        ]
                    ]
                ]
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/fhir+json',
                    'Accept' => 'application/fhir+json'
                ])
                ->put("{$this->baseUrl}/Patient/{$player->fhir_patient_id}", $patientData);

            if ($response->successful()) {
                $result = $response->json();
                
                // Update player FHIR data
                $player->update([
                    'fhir_data' => json_encode($result)
                ]);

                return [
                    'success' => true,
                    'patient_id' => $result['id'],
                    'data' => $result
                ];
            }

            throw new Exception("FHIR API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FHIR patient update error', [
                'player_id' => $player->id,
                'fhir_patient_id' => $player->fhir_patient_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a performance observation in FHIR
     */
    public function createPerformanceObservation(Performance $performance): array
    {
        try {
            $observationData = [
                'resourceType' => 'Observation',
                'status' => 'final',
                'category' => [
                    [
                        'coding' => [
                            [
                                'system' => 'http://terminology.hl7.org/CodeSystem/observation-category',
                                'code' => 'vital-signs',
                                'display' => 'Vital Signs'
                            ]
                        ]
                    ]
                ],
                'code' => [
                    'coding' => [
                        [
                            'system' => 'https://med-predictor.com/performance-metrics',
                            'code' => 'overall-performance',
                            'display' => 'Overall Performance Score'
                        ]
                    ]
                ],
                'subject' => [
                    'reference' => "Patient/{$performance->player->fhir_patient_id}"
                ],
                'effectiveDateTime' => $performance->performance_date->format('c'),
                'issued' => now()->format('c'),
                'valueQuantity' => [
                    'value' => $performance->overall_performance_score,
                    'unit' => 'score',
                    'system' => 'https://med-predictor.com/units',
                    'code' => 'score'
                ],
                'component' => [
                    [
                        'code' => [
                            'coding' => [
                                [
                                    'system' => 'https://med-predictor.com/performance-metrics',
                                    'code' => 'physical-score',
                                    'display' => 'Physical Performance Score'
                                ]
                            ]
                        ],
                        'valueQuantity' => [
                            'value' => $performance->physical_score,
                            'unit' => 'score'
                        ]
                    ],
                    [
                        'code' => [
                            'coding' => [
                                [
                                    'system' => 'https://med-predictor.com/performance-metrics',
                                    'code' => 'technical-score',
                                    'display' => 'Technical Performance Score'
                                ]
                            ]
                        ],
                        'valueQuantity' => [
                            'value' => $performance->technical_score,
                            'unit' => 'score'
                        ]
                    ],
                    [
                        'code' => [
                            'coding' => [
                                [
                                    'system' => 'https://med-predictor.com/performance-metrics',
                                    'code' => 'tactical-score',
                                    'display' => 'Tactical Performance Score'
                                ]
                            ]
                        ],
                        'valueQuantity' => [
                            'value' => $performance->tactical_score,
                            'unit' => 'score'
                        ]
                    ],
                    [
                        'code' => [
                            'coding' => [
                                [
                                    'system' => 'https://med-predictor.com/performance-metrics',
                                    'code' => 'mental-score',
                                    'display' => 'Mental Performance Score'
                                ]
                            ]
                        ],
                        'valueQuantity' => [
                            'value' => $performance->mental_score,
                            'unit' => 'score'
                        ]
                    ]
                ]
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/fhir+json',
                    'Accept' => 'application/fhir+json'
                ])
                ->post("{$this->baseUrl}/Observation", $observationData);

            if ($response->successful()) {
                $result = $response->json();
                
                // Update performance with FHIR observation ID
                $performance->update([
                    'fhir_observation_id' => $result['id'] ?? null,
                    'fhir_data' => json_encode($result)
                ]);

                return [
                    'success' => true,
                    'observation_id' => $result['id'],
                    'data' => $result
                ];
            }

            throw new Exception("FHIR API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FHIR observation creation error', [
                'performance_id' => $performance->id,
                'player_id' => $performance->player_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a document reference in FHIR
     */
    public function createDocumentReference(Player $player, array $documentData): array
    {
        try {
            $documentReference = [
                'resourceType' => 'DocumentReference',
                'status' => 'current',
                'type' => [
                    'coding' => [
                        [
                            'system' => 'http://loinc.org',
                            'code' => '11506-3',
                            'display' => 'Progress note'
                        ]
                    ]
                ],
                'category' => [
                    [
                        'coding' => [
                            [
                                'system' => 'https://med-predictor.com/document-categories',
                                'code' => 'performance-report',
                                'display' => 'Performance Report'
                            ]
                        ]
                    ]
                ],
                'subject' => [
                    'reference' => "Patient/{$player->fhir_patient_id}"
                ],
                'date' => now()->format('c'),
                'content' => [
                    [
                        'attachment' => [
                            'contentType' => 'application/pdf',
                            'url' => $documentData['url'] ?? null,
                            'title' => $documentData['title'] ?? 'Performance Report'
                        ]
                    ]
                ]
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/fhir+json',
                    'Accept' => 'application/fhir+json'
                ])
                ->post("{$this->baseUrl}/DocumentReference", $documentReference);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'document_id' => $response->json()['id'],
                    'data' => $response->json()
                ];
            }

            throw new Exception("FHIR API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FHIR document reference creation error', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Validate FHIR resources
     */
    public function validateFhirResource(array $resource): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/fhir+json',
                    'Accept' => 'application/fhir+json'
                ])
                ->post("{$this->baseUrl}/\$validate", $resource);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'valid' => empty($result['issue']),
                    'issues' => $result['issue'] ?? []
                ];
            }

            return [
                'valid' => false,
                'issues' => ['Validation request failed']
            ];
        } catch (Exception $e) {
            Log::error('FHIR validation error', [
                'error' => $e->getMessage()
            ]);
            return [
                'valid' => false,
                'issues' => ['Validation service unavailable']
            ];
        }
    }

    /**
     * Handle FHIR server errors
     */
    public function handleFhirServerError(Exception $e): array
    {
        Log::error('FHIR server error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'success' => false,
            'error' => 'FHIR server temporarily unavailable',
            'retry_after' => 300 // 5 minutes
        ];
    }

    /**
     * Search patient resources
     */
    public function searchPatientResources(array $searchParams): array
    {
        try {
            $queryString = http_build_query($searchParams);
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/fhir+json'
                ])
                ->get("{$this->baseUrl}/Patient?{$queryString}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("FHIR search error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FHIR patient search error', [
                'search_params' => $searchParams,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Retrieve patient history
     */
    public function retrievePatientHistory(string $patientId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/fhir+json'
                ])
                ->get("{$this->baseUrl}/Patient/{$patientId}/_history");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("FHIR history error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FHIR patient history error', [
                'patient_id' => $patientId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Log FHIR operations for audit
     */
    public function logFhirOperation(string $operation, array $data): void
    {
        Log::info("FHIR operation: {$operation}", [
            'operation' => $operation,
            'data' => $data,
            'timestamp' => now(),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle network timeouts gracefully
     */
    public function handleNetworkTimeout(): array
    {
        return [
            'success' => false,
            'error' => 'Network timeout occurred',
            'retry_after' => 60 // 1 minute
        ];
    }

    /**
     * Validate required FHIR fields
     */
    public function validateRequiredFhirFields(array $resource, string $resourceType): array
    {
        $requiredFields = $this->getRequiredFields($resourceType);
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($resource[$field]) || empty($resource[$field])) {
                $missingFields[] = $field;
            }
        }

        return [
            'valid' => empty($missingFields),
            'missing_fields' => $missingFields
        ];
    }

    /**
     * Batch create multiple resources
     */
    public function batchCreateMultipleResources(array $resources): array
    {
        try {
            $bundle = [
                'resourceType' => 'Bundle',
                'type' => 'transaction',
                'entry' => []
            ];

            foreach ($resources as $resource) {
                $bundle['entry'][] = [
                    'resource' => $resource,
                    'request' => [
                        'method' => 'POST',
                        'url' => $resource['resourceType']
                    ]
                ];
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/fhir+json',
                    'Accept' => 'application/fhir+json'
                ])
                ->post("{$this->baseUrl}", $bundle);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'results' => $response->json()
                ];
            }

            throw new Exception("FHIR batch error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FHIR batch creation error', [
                'resource_count' => count($resources),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get required fields for a resource type
     */
    protected function getRequiredFields(string $resourceType): array
    {
        $requiredFields = [
            'Patient' => ['resourceType', 'identifier', 'name'],
            'Observation' => ['resourceType', 'status', 'code', 'subject'],
            'DocumentReference' => ['resourceType', 'status', 'type', 'subject']
        ];

        return $requiredFields[$resourceType] ?? [];
    }
} 