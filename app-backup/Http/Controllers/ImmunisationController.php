<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Immunisation;
use App\Services\ImmunisationFHIRService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ImmunisationController extends Controller
{
    protected $fhirService;

    public function __construct(ImmunisationFHIRService $fhirService)
    {
        $this->fhirService = $fhirService;
    }

    /**
     * Display a listing of immunization records for an athlete
     */
    public function index(Request $request, Athlete $athlete): JsonResponse
    {
        $query = $athlete->immunisations()
            ->with(['administeredBy', 'verifiedBy'])
            ->orderBy('date_administered', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by vaccine
        if ($request->has('vaccine')) {
            $query->where('vaccine_name', 'like', '%' . $request->vaccine . '%');
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('date_administered', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('date_administered', '<=', $request->date_to);
        }

        $immunisations = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $immunisations->items(),
            'pagination' => [
                'current_page' => $immunisations->currentPage(),
                'last_page' => $immunisations->lastPage(),
                'per_page' => $immunisations->perPage(),
                'total' => $immunisations->total()
            ]
        ]);
    }

    /**
     * Store a newly created immunization record
     */
    public function store(Request $request, Athlete $athlete): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vaccine_code' => 'required|string|max:50',
            'vaccine_name' => 'required|string|max:255',
            'date_administered' => 'required|date',
            'lot_number' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date|after:date_administered',
            'dose_number' => 'nullable|integer|min:1',
            'total_doses' => 'nullable|integer|min:1',
            'route' => 'nullable|string|in:IM,SC,ID,IN,PO',
            'site' => 'nullable|string|in:LA,RA,LD,RD,LG,RG,LVL,RVL',
            'notes' => 'nullable|string',
            'administered_by' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['athlete_id'] = $athlete->id;
            $data['administered_by'] = $data['administered_by'] ?? auth()->id();
            $data['status'] = 'active';
            $data['source'] = 'manual';
            $data['sync_status'] = 'pending';

            $immunisation = Immunisation::create($data);

            // Try to sync with FHIR API
            $syncResult = $this->fhirService->pushRecord($immunisation);

            return response()->json([
                'success' => true,
                'data' => $immunisation->load(['administeredBy', 'verifiedBy']),
                'sync_result' => $syncResult
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error creating immunization record: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to create immunization record'
            ], 500);
        }
    }

    /**
     * Display the specified immunization record
     */
    public function show(Immunisation $immunisation): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $immunisation->load(['athlete', 'administeredBy', 'verifiedBy'])
        ]);
    }

    /**
     * Update the specified immunization record
     */
    public function update(Request $request, Immunisation $immunisation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vaccine_code' => 'sometimes|required|string|max:50',
            'vaccine_name' => 'sometimes|required|string|max:255',
            'date_administered' => 'sometimes|required|date',
            'lot_number' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date|after:date_administered',
            'dose_number' => 'nullable|integer|min:1',
            'total_doses' => 'nullable|integer|min:1',
            'route' => 'nullable|string|in:IM,SC,ID,IN,PO',
            'site' => 'nullable|string|in:LA,RA,LD,RD,LG,RG,LVL,RVL',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:active,expired,pending,incomplete'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['sync_status'] = 'pending'; // Mark for re-sync

            $immunisation->update($data);

            // Try to sync with FHIR API
            $syncResult = $this->fhirService->updateRecord($immunisation);

            return response()->json([
                'success' => true,
                'data' => $immunisation->load(['athlete', 'administeredBy', 'verifiedBy']),
                'sync_result' => $syncResult
            ]);

        } catch (\Exception $e) {
            Log::error("Error updating immunization record: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update immunization record'
            ], 500);
        }
    }

    /**
     * Remove the specified immunization record
     */
    public function destroy(Immunisation $immunisation): JsonResponse
    {
        try {
            // Try to delete from FHIR API first
            $syncResult = $this->fhirService->deleteRecord($immunisation);

            $immunisation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Immunization record deleted successfully',
                'sync_result' => $syncResult
            ]);

        } catch (\Exception $e) {
            Log::error("Error deleting immunization record: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete immunization record'
            ], 500);
        }
    }

    /**
     * Sync immunization records with FHIR API
     */
    public function sync(Request $request, Athlete $athlete): JsonResponse
    {
        try {
            $result = $this->fhirService->fetchRecords($athlete);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] 
                    ? "Successfully synced {$result['synced_count']} immunization records"
                    : $result['error'],
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error("Error syncing immunization records: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to sync immunization records'
            ], 500);
        }
    }

    /**
     * Get immunization statistics for an athlete
     */
    public function statistics(Athlete $athlete): JsonResponse
    {
        $stats = [
            'total' => $athlete->immunisations()->count(),
            'active' => $athlete->immunisations()->where('status', 'active')->count(),
            'expired' => $athlete->immunisations()->where('status', 'expired')->count(),
            'expiring_soon' => $athlete->immunisations()->expiringSoon()->count(),
            'pending_sync' => $athlete->immunisations()->pendingSync()->count(),
            'failed_sync' => $athlete->immunisations()->failedSync()->count(),
            'by_vaccine' => $athlete->immunisations()
                ->selectRaw('vaccine_name, COUNT(*) as count')
                ->groupBy('vaccine_name')
                ->orderBy('count', 'desc')
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Test FHIR API connectivity
     */
    public function testConnectivity(): JsonResponse
    {
        $result = $this->fhirService->testConnectivity();

        return response()->json([
            'success' => $result['success'],
            'data' => $result
        ]);
    }

    /**
     * Get sync statistics
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
     * Verify an immunization record
     */
    public function verify(Request $request, Immunisation $immunisation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'verified_by' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $immunisation->update([
                'verified_by' => $request->verified_by,
                'verification_date' => now(),
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'data' => $immunisation->load(['verifiedBy'])
            ]);

        } catch (\Exception $e) {
            Log::error("Error verifying immunization record: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to verify immunization record'
            ], 500);
        }
    }

    /**
     * Export immunization records
     */
    public function export(Request $request, Athlete $athlete): JsonResponse
    {
        $query = $athlete->immunisations()
            ->with(['administeredBy', 'verifiedBy'])
            ->orderBy('date_administered', 'desc');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->where('date_administered', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('date_administered', '<=', $request->date_to);
        }

        $immunisations = $query->get();

        $exportData = $immunisations->map(function ($immunisation) {
            return [
                'id' => $immunisation->id,
                'vaccine_code' => $immunisation->vaccine_code,
                'vaccine_name' => $immunisation->vaccine_name,
                'date_administered' => $immunisation->date_administered->format('Y-m-d'),
                'lot_number' => $immunisation->lot_number,
                'manufacturer' => $immunisation->manufacturer,
                'expiration_date' => $immunisation->expiration_date?->format('Y-m-d'),
                'dose_number' => $immunisation->dose_number,
                'total_doses' => $immunisation->total_doses,
                'route' => $immunisation->route,
                'site' => $immunisation->site,
                'status' => $immunisation->status,
                'notes' => $immunisation->notes,
                'administered_by' => $immunisation->administeredBy?->name,
                'verified_by' => $immunisation->verifiedBy?->name,
                'verification_date' => $immunisation->verification_date?->format('Y-m-d'),
                'source' => $immunisation->source,
                'sync_status' => $immunisation->sync_status,
                'created_at' => $immunisation->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $immunisation->updated_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $exportData,
            'filename' => "immunization_records_{$athlete->id}_" . date('Y-m-d_H-i-s') . ".json"
        ]);
    }
} 