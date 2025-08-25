<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DentalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DentalRecordController extends Controller
{
    /**
     * Afficher la liste des enregistrements dentaires pour un patient.
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $dentalRecords = DentalRecord::where('patient_id', $request->patient_id)
            ->with(['patient', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'records' => $dentalRecords,
                'count' => $dentalRecords->count()
            ]
        ]);
    }

    /**
     * Afficher un enregistrement dentaire spécifique.
     */
    public function show(DentalRecord $dentalRecord): JsonResponse
    {
        $dentalRecord->load(['patient', 'user']);

        return response()->json([
            'success' => true,
            'data' => [
                'record' => $dentalRecord,
                'dental_data' => $dentalRecord->dental_data,
                'teeth_count' => $dentalRecord->getTeethCountByStatus(),
            ]
        ]);
    }

    /**
     * Créer un nouvel enregistrement dentaire.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'dental_data' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:draft,completed,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $dentalRecord = DentalRecord::create([
            'patient_id' => $request->patient_id,
            'user_id' => Auth::id() ?? 1, // Utiliser l'utilisateur 1 par défaut si pas d'auth
            'dental_data' => $request->dental_data ?? [],
            'notes' => $request->notes,
            'status' => $request->status ?? 'completed',
            'examined_at' => now(),
        ]);

        $dentalRecord->load(['patient', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Enregistrement dentaire créé avec succès',
            'data' => [
                'record' => $dentalRecord,
                'dental_data' => $dentalRecord->dental_data,
                'teeth_count' => $dentalRecord->getTeethCountByStatus(),
            ]
        ], 201);
    }

    /**
     * Mettre à jour un enregistrement dentaire.
     */
    public function update(Request $request, DentalRecord $dentalRecord): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'dental_data' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:draft,completed,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $dentalRecord->update([
            'dental_data' => $request->dental_data ?? $dentalRecord->dental_data,
            'notes' => $request->notes,
            'status' => $request->status ?? $dentalRecord->status,
            'examined_at' => now(),
        ]);

        $dentalRecord->load(['patient', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Enregistrement dentaire mis à jour avec succès',
            'data' => [
                'record' => $dentalRecord,
                'dental_data' => $dentalRecord->dental_data,
                'teeth_count' => $dentalRecord->getTeethCountByStatus(),
            ]
        ]);
    }

    /**
     * Mettre à jour l'état d'une dent spécifique.
     */
    public function updateTooth(Request $request, DentalRecord $dentalRecord): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tooth_number' => 'required|string|max:2',
            'status' => 'required|in:healthy,cavity,crown,extracted,treatment',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $dentalRecord->updateToothStatus(
            $request->tooth_number,
            $request->status,
            $request->notes ?? ''
        );

        $dentalRecord->load(['patient', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'État de la dent mis à jour avec succès',
            'data' => [
                'record' => $dentalRecord,
                'dental_data' => $dentalRecord->dental_data,
                'teeth_count' => $dentalRecord->getTeethCountByStatus(),
            ]
        ]);
    }

    /**
     * Supprimer un enregistrement dentaire.
     */
    public function destroy(DentalRecord $dentalRecord): JsonResponse
    {
        $dentalRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enregistrement dentaire supprimé avec succès'
        ]);
    }

    /**
     * Obtenir les statistiques dentaires pour un patient.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $latestRecord = DentalRecord::where('patient_id', $request->patient_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestRecord) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_teeth' => 32,
                    'healthy_teeth' => 32,
                    'cavity_teeth' => 0,
                    'crown_teeth' => 0,
                    'extracted_teeth' => 0,
                    'treatment_teeth' => 0,
                    'last_examination' => null,
                ]
            ]);
        }

        $teethCount = $latestRecord->getTeethCountByStatus();

        return response()->json([
            'success' => true,
            'data' => [
                'total_teeth' => 32,
                'healthy_teeth' => $teethCount['healthy'],
                'cavity_teeth' => $teethCount['cavity'],
                'crown_teeth' => $teethCount['crown'],
                'extracted_teeth' => $teethCount['extracted'],
                'treatment_teeth' => $teethCount['treatment'],
                'last_examination' => $latestRecord->examined_at,
            ]
        ]);
    }
}
