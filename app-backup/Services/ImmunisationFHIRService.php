<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Immunisation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ImmunisationFHIRService
{
    private $baseUrl;
    private $timeout;
    private $retryAttempts;
    private $retryDelay;

    public function __construct()
    {
        $this->baseUrl = config('services.fhir.base_url');
        $this->timeout = config('services.fhir.timeout', 30);
        $this->retryAttempts = config('services.fhir.retry_attempts', 3);
        $this->retryDelay = config('services.fhir.retry_delay', 5);
    }

    /**
     * Fetch immunization records from FHIR API for an athlete
     */
    public function fetchRecords(Athlete $athlete): array
    {
        if (!$athlete->fhir_id) {
            Log::warning("Athlete {$athlete->id} has no FHIR ID, skipping sync");
            return [
                'success' => false,
                'error' => 'Athlete has no FHIR ID'
            ];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/Immunization", [
                    'patient' => $athlete->fhir_id,
                    '_count' => 100,
                    '_sort' => '-date'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $immunizations = $data['entry'] ?? [];
                
                $syncedCount = 0;
                $errors = [];

                foreach ($immunizations as $entry) {
                    $resource = $entry['resource'];
                    $result = $this->syncFhirImmunization($athlete, $resource);
                    
                    if ($result['success']) {
                        $syncedCount++;
                    } else {
                        $errors[] = $result['error'];
                    }
                }

                return [
                    'success' => true,
                    'synced_count' => $syncedCount,
                    'total_found' => count($immunizations),
                    'errors' => $errors
                ];
            }

            Log::error("FHIR API error: " . $response->body());
            return [
                'success' => false,
                'error' => 'Failed to fetch immunization records from FHIR API'
            ];

        } catch (\Exception $e) {
            Log::error("FHIR sync error for athlete {$athlete->id}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Push immunization record to FHIR API
     */
    public function pushRecord(Immunisation $immunisation): array
    {
        if (!$immunisation->athlete->fhir_id) {
            return [
                'success' => false,
                'error' => 'Athlete has no FHIR ID'
            ];
        }

        try {
            $fhirResource = $immunisation->toFhirResource();
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/fhir+json',
                    'Accept' => 'application/fhir+json'
                ])
                ->post("{$this->baseUrl}/Immunization", $fhirResource);

            if ($response->successful()) {
                $data = $response->json();
                $fhirId = $data['id'] ?? null;
                
                if ($fhirId) {
                    $immunisation->update([
                        'fhir_id' => $fhirId,
                        'sync_status' => 'synced',
                        'last_synced_at' => now(),
                        'sync_error' => null
                    ]);
                }

                return [
                    'success' => true,
                    'fhir_id' => $fhirId
                ];
            }

            $error = $response->json()['issue'][0]['diagnostics'] ?? 'Unknown FHIR API error';
            Log::error("FHIR push error: " . $error);

            $immunisation->update([
                'sync_status' => 'failed',
                'sync_error' => $error,
                'last_synced_at' => now()
            ]);

            return [
                'success' => false,
                'error' => $error
            ];

        } catch (\Exception $e) {
            Log::error("FHIR push error for immunization {$immunisation->id}: " . $e->getMessage());
            
            $immunisation->update([
                'sync_status' => 'failed',
                'sync_error' => $e->getMessage(),
                'last_synced_at' => now()
            ]);

            return [
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update immunization record in FHIR API
     */
    public function updateRecord(Immunisation $immunisation): array
    {
        if (!$immunisation->fhir_id) {
            return $this->pushRecord($immunisation);
        }

        try {
            $fhirResource = $immunisation->toFhirResource();
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/fhir+json',
                    'Accept' => 'application/fhir+json'
                ])
                ->put("{$this->baseUrl}/Immunization/{$immunisation->fhir_id}", $fhirResource);

            if ($response->successful()) {
                $immunisation->update([
                    'sync_status' => 'synced',
                    'last_synced_at' => now(),
                    'sync_error' => null
                ]);

                return [
                    'success' => true
                ];
            }

            $error = $response->json()['issue'][0]['diagnostics'] ?? 'Unknown FHIR API error';
            Log::error("FHIR update error: " . $error);

            $immunisation->update([
                'sync_status' => 'failed',
                'sync_error' => $error,
                'last_synced_at' => now()
            ]);

            return [
                'success' => false,
                'error' => $error
            ];

        } catch (\Exception $e) {
            Log::error("FHIR update error for immunization {$immunisation->id}: " . $e->getMessage());
            
            $immunisation->update([
                'sync_status' => 'failed',
                'sync_error' => $e->getMessage(),
                'last_synced_at' => now()
            ]);

            return [
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete immunization record from FHIR API
     */
    public function deleteRecord(Immunisation $immunisation): array
    {
        if (!$immunisation->fhir_id) {
            return [
                'success' => true // Nothing to delete
            ];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->delete("{$this->baseUrl}/Immunization/{$immunisation->fhir_id}");

            if ($response->successful()) {
                return [
                    'success' => true
                ];
            }

            $error = $response->json()['issue'][0]['diagnostics'] ?? 'Unknown FHIR API error';
            Log::error("FHIR delete error: " . $error);

            return [
                'success' => false,
                'error' => $error
            ];

        } catch (\Exception $e) {
            Log::error("FHIR delete error for immunization {$immunisation->id}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync a single FHIR immunization resource to local database
     */
    private function syncFhirImmunization(Athlete $athlete, array $fhirResource): array
    {
        try {
            $fhirId = $fhirResource['id'] ?? null;
            $vaccineCode = $fhirResource['vaccineCode']['coding'][0]['code'] ?? null;
            $vaccineName = $fhirResource['vaccineCode']['text'] ?? $fhirResource['vaccineCode']['coding'][0]['display'] ?? null;
            $dateAdministered = $fhirResource['occurrenceDateTime'] ?? null;
            $lotNumber = $fhirResource['lotNumber'] ?? null;
            $expirationDate = $fhirResource['expirationDate'] ?? null;
            $site = $fhirResource['site']['coding'][0]['code'] ?? 'LA';
            $route = $fhirResource['route']['coding'][0]['code'] ?? 'IM';
            $notes = $fhirResource['note'][0]['text'] ?? null;

            if (!$vaccineCode || !$vaccineName || !$dateAdministered) {
                return [
                    'success' => false,
                    'error' => 'Missing required fields in FHIR resource'
                ];
            }

            // Check if immunization already exists
            $existing = Immunisation::where('fhir_id', $fhirId)->first();
            
            if ($existing) {
                // Update existing record
                $existing->update([
                    'vaccine_code' => $vaccineCode,
                    'vaccine_name' => $vaccineName,
                    'date_administered' => $dateAdministered,
                    'lot_number' => $lotNumber,
                    'expiration_date' => $expirationDate,
                    'site' => $site,
                    'route' => $route,
                    'notes' => $notes,
                    'source' => 'fhir_sync',
                    'sync_status' => 'synced',
                    'last_synced_at' => now(),
                    'sync_error' => null
                ]);
            } else {
                // Create new record
                Immunisation::create([
                    'athlete_id' => $athlete->id,
                    'vaccine_code' => $vaccineCode,
                    'vaccine_name' => $vaccineName,
                    'date_administered' => $dateAdministered,
                    'fhir_id' => $fhirId,
                    'lot_number' => $lotNumber,
                    'expiration_date' => $expirationDate,
                    'site' => $site,
                    'route' => $route,
                    'notes' => $notes,
                    'source' => 'fhir_sync',
                    'sync_status' => 'synced',
                    'last_synced_at' => now()
                ]);
            }

            return [
                'success' => true
            ];

        } catch (\Exception $e) {
            Log::error("Error syncing FHIR immunization: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test FHIR API connectivity
     */
    public function testConnectivity(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/metadata");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => 'connected',
                    'fhir_version' => $response->json()['fhirVersion'] ?? 'unknown'
                ];
            }

            return [
                'success' => false,
                'status' => 'error',
                'error' => 'FHIR API returned status: ' . $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'error' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get sync statistics
     */
    public function getSyncStatistics(): array
    {
        $total = Immunisation::count();
        $synced = Immunisation::where('sync_status', 'synced')->count();
        $pending = Immunisation::where('sync_status', 'pending')->count();
        $failed = Immunisation::where('sync_status', 'failed')->count();

        return [
            'total' => $total,
            'synced' => $synced,
            'pending' => $pending,
            'failed' => $failed,
            'sync_rate' => $total > 0 ? round(($synced / $total) * 100, 1) : 0
        ];
    }
} 