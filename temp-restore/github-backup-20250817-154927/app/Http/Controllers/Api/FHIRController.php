<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImmunisationFHIRService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FHIRController extends Controller
{
    protected $fhirService;

    public function __construct(ImmunisationFHIRService $fhirService)
    {
        $this->fhirService = $fhirService;
    }

    /**
     * Test FHIR connectivity
     */
    public function connectivity(): JsonResponse
    {
        $result = $this->fhirService->testConnectivity();

        return response()->json([
            'success' => $result['success'],
            'status' => $result['success'] ? 'connected' : 'disconnected',
            'message' => $result['message'] ?? 'Unknown status',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get FHIR sync statistics
     */
    public function syncStatistics(): JsonResponse
    {
        $stats = $this->fhirService->getSyncStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Test FHIR server capabilities
     */
    public function capabilities(): JsonResponse
    {
        try {
            $baseUrl = config('services.fhir.base_url');
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->get("{$baseUrl}/metadata");

            if ($response->successful()) {
                $metadata = $response->json();
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'fhir_version' => $metadata['fhirVersion'] ?? 'Unknown',
                        'software' => $metadata['software']['name'] ?? 'Unknown',
                        'version' => $metadata['software']['version'] ?? 'Unknown',
                        'resources' => count($metadata['rest'][0]['resource'] ?? []),
                        'supported_resources' => collect($metadata['rest'][0]['resource'] ?? [])
                            ->pluck('type')
                            ->toArray()
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve FHIR server capabilities'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test specific FHIR resource access
     */
    public function testResource(Request $request): JsonResponse
    {
        $request->validate([
            'resource_type' => 'required|string|in:Patient,Immunization,Observation,Procedure'
        ]);

        $resourceType = $request->input('resource_type');
        $baseUrl = config('services.fhir.base_url');

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->get("{$baseUrl}/{$resourceType}", [
                    '_count' => 1,
                    '_summary' => 'count'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'resource_type' => $resourceType,
                        'total_count' => $data['total'] ?? 0,
                        'accessible' => true
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => "Failed to access {$resourceType} resource"
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage()
            ], 500);
        }
    }
} 