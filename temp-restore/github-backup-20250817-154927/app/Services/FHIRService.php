<?php

namespace App\Services;

use App\Models\Athlete;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FHIRService
{
    private string $baseUrl;
    private PendingRequest $httpClient;

    public function __construct()
    {
        $this->baseUrl = config('services.fhir.base_url');
        $this->httpClient = Http::withHeaders([
            'Content-Type' => 'application/fhir+json',
            'Accept' => 'application/fhir+json',
        ])->timeout(30);
    }

    /**
     * Push athlete data to FHIR server as Patient resource.
     */
    public function pushPatient(Athlete $athlete): array
    {
        try {
            $fhirPatient = $this->transformAthleteToFHIRPatient($athlete);
            
            $response = $this->httpClient->put(
                "{$this->baseUrl}/Patient/{$athlete->fifa_id}",
                $fhirPatient
            );

            if ($response->successful()) {
                Log::info("Successfully pushed athlete {$athlete->id} to FHIR server", [
                    'athlete_id' => $athlete->id,
                    'fifa_id' => $athlete->fifa_id,
                    'fhir_response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'message' => 'Patient successfully pushed to FHIR server',
                    'fhir_id' => $athlete->fifa_id,
                    'response' => $response->json()
                ];
            } else {
                Log::error("Failed to push athlete {$athlete->id} to FHIR server", [
                    'athlete_id' => $athlete->id,
                    'fifa_id' => $athlete->fifa_id,
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to push patient to FHIR server',
                    'status_code' => $response->status(),
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error("Exception while pushing athlete {$athlete->id} to FHIR server", [
                'athlete_id' => $athlete->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while pushing to FHIR server',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Transform Athlete model to FHIR Patient resource.
     */
    private function transformAthleteToFHIRPatient(Athlete $athlete): array
    {
        $patient = [
            'resourceType' => 'Patient',
            'id' => $athlete->fifa_id,
            'identifier' => [
                [
                    'system' => 'https://fifa.com/player',
                    'value' => $athlete->fifa_id
                ],
                [
                    'system' => 'https://fit.medical/athlete',
                    'value' => (string) $athlete->id
                ]
            ],
            'active' => $athlete->active,
            'name' => [
                [
                    'use' => 'official',
                    'text' => $athlete->name,
                    'family' => $this->extractLastName($athlete->name),
                    'given' => $this->extractGivenNames($athlete->name)
                ]
            ],
            'gender' => $this->mapGender($athlete->gender),
            'birthDate' => $athlete->dob->format('Y-m-d'),
            'extension' => [
                [
                    'url' => 'https://fit.medical/athlete/position',
                    'valueString' => $athlete->position
                ],
                [
                    'url' => 'https://fit.medical/athlete/jersey_number',
                    'valueInteger' => $athlete->jersey_number
                ],
                [
                    'url' => 'https://fit.medical/athlete/blood_type',
                    'valueCodeableConcept' => [
                        'coding' => [
                            [
                                'system' => 'http://hl7.org/fhir/ValueSet/blood-type',
                                'code' => $athlete->blood_type,
                                'display' => $athlete->blood_type
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Add nationality if available
        if ($athlete->nationality) {
            $patient['extension'][] = [
                'url' => 'https://fit.medical/athlete/nationality',
                'valueString' => $athlete->nationality
            ];
        }

        // Add emergency contact if available
        if ($athlete->emergency_contact) {
            $patient['contact'][] = [
                'relationship' => [
                    [
                        'coding' => [
                            [
                                'system' => 'http://terminology.hl7.org/CodeSystem/v2-0131',
                                'code' => 'C',
                                'display' => 'Emergency Contact'
                            ]
                        ]
                    ]
                ],
                'name' => [
                    'use' => 'official',
                    'text' => $athlete->emergency_contact['name'] ?? 'Emergency Contact'
                ],
                'telecom' => [
                    [
                        'system' => 'phone',
                        'value' => $athlete->emergency_contact['phone'] ?? '',
                        'use' => 'mobile'
                    ]
                ]
            ];
        }

        // Add medical history as observations
        if ($athlete->medical_history) {
            $patient['extension'][] = [
                'url' => 'https://fit.medical/athlete/medical_history',
                'valueString' => json_encode($athlete->medical_history)
            ];
        }

        // Add allergies as observations
        if ($athlete->allergies) {
            $patient['extension'][] = [
                'url' => 'https://fit.medical/athlete/allergies',
                'valueString' => json_encode($athlete->allergies)
            ];
        }

        // Add medications as observations
        if ($athlete->medications) {
            $patient['extension'][] = [
                'url' => 'https://fit.medical/athlete/medications',
                'valueString' => json_encode($athlete->medications)
            ];
        }

        return $patient;
    }

    /**
     * Extract last name from full name.
     */
    private function extractLastName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));
        return end($parts) ?: '';
    }

    /**
     * Extract given names from full name.
     */
    private function extractGivenNames(string $fullName): array
    {
        $parts = explode(' ', trim($fullName));
        array_pop($parts); // Remove last name
        return array_filter($parts);
    }

    /**
     * Map internal gender to FHIR gender codes.
     */
    private function mapGender(?string $gender): ?string
    {
        return match ($gender) {
            'male' => 'male',
            'female' => 'female',
            'other' => 'other',
            default => 'unknown'
        };
    }

    /**
     * Push health score as Observation resource.
     */
    public function pushHealthScore(Athlete $athlete, $healthScore): array
    {
        try {
            $observation = [
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
                            'system' => 'https://fit.medical/health-score',
                            'code' => 'health-score',
                            'display' => 'Health Score'
                        ]
                    ],
                    'text' => 'Athlete Health Score'
                ],
                'subject' => [
                    'reference' => "Patient/{$athlete->fifa_id}"
                ],
                'effectiveDateTime' => $healthScore->calculated_date->toISOString(),
                'valueQuantity' => [
                    'value' => $healthScore->score,
                    'unit' => 'score',
                    'system' => 'https://fit.medical/health-score',
                    'code' => 'score'
                ],
                'component' => [
                    [
                        'code' => [
                            'coding' => [
                                [
                                    'system' => 'https://fit.medical/health-score',
                                    'code' => 'trend',
                                    'display' => 'Health Trend'
                                ]
                            ]
                        ],
                        'valueCodeableConcept' => [
                            'coding' => [
                                [
                                    'system' => 'https://fit.medical/health-score/trend',
                                    'code' => $healthScore->trend,
                                    'display' => ucfirst($healthScore->trend)
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $response = $this->httpClient->post(
                "{$this->baseUrl}/Observation",
                $observation
            );

            if ($response->successful()) {
                Log::info("Successfully pushed health score for athlete {$athlete->id}", [
                    'athlete_id' => $athlete->id,
                    'health_score_id' => $healthScore->id,
                    'fhir_response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'message' => 'Health score successfully pushed to FHIR server',
                    'fhir_id' => $response->json('id'),
                    'response' => $response->json()
                ];
            } else {
                Log::error("Failed to push health score for athlete {$athlete->id}", [
                    'athlete_id' => $athlete->id,
                    'health_score_id' => $healthScore->id,
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to push health score to FHIR server',
                    'status_code' => $response->status(),
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error("Exception while pushing health score for athlete {$athlete->id}", [
                'athlete_id' => $athlete->id,
                'health_score_id' => $healthScore->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while pushing health score to FHIR server',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check FHIR server connectivity.
     */
    public function checkConnectivity(): array
    {
        try {
            $response = $this->httpClient->get("{$this->baseUrl}/metadata");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'FHIR server is accessible',
                    'capabilities' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'FHIR server is not accessible',
                    'status_code' => $response->status(),
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception occurred while checking FHIR server connectivity',
                'error' => $e->getMessage()
            ];
        }
    }
} 