<?php

namespace App\Http\Controllers;

use App\Models\DentalAnnotation;
use App\Models\HealthRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DentalController extends Controller
{
    /**
     * Obtenir toutes les annotations dentaires pour un dossier de santé
     */
    public function index(Request $request): JsonResponse
    {
        $healthRecordId = $request->input('health_record_id');
        
        if (!$healthRecordId) {
            return response()->json(['error' => 'health_record_id requis'], 400);
        }

        $annotations = DentalAnnotation::where('health_record_id', $healthRecordId)
            ->orderBy('tooth_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $annotations,
            'count' => $annotations->count()
        ]);
    }

    /**
     * Obtenir une annotation dentaire spécifique
     */
    public function show(DentalAnnotation $dentalAnnotation): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $dentalAnnotation
        ]);
    }

    /**
     * Créer une nouvelle annotation dentaire
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'health_record_id' => 'required|exists:health_records,id',
            'tooth_id' => 'required|string|max:10',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'status' => 'nullable|string|in:normal,selected,fixed,problem,warning',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array'
        ]);

        $annotation = DentalAnnotation::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Annotation dentaire créée avec succès',
            'data' => $annotation
        ], 201);
    }

    /**
     * Mettre à jour une annotation dentaire
     */
    public function update(Request $request, DentalAnnotation $dentalAnnotation): JsonResponse
    {
        $request->validate([
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'status' => 'nullable|string|in:normal,selected,fixed,problem,warning',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array'
        ]);

        $dentalAnnotation->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Annotation dentaire mise à jour avec succès',
            'data' => $dentalAnnotation
        ]);
    }

    /**
     * Supprimer une annotation dentaire
     */
    public function destroy(DentalAnnotation $dentalAnnotation): JsonResponse
    {
        $dentalAnnotation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Annotation dentaire supprimée avec succès'
        ]);
    }

    /**
     * Sauvegarder toutes les annotations pour un dossier de santé
     */
    public function saveAll(Request $request): JsonResponse
    {
        $request->validate([
            'health_record_id' => 'required|exists:health_records,id',
            'annotations' => 'required|array',
            'annotations.*.tooth_id' => 'required|string',
            'annotations.*.position_x' => 'nullable|integer',
            'annotations.*.position_y' => 'nullable|integer',
            'annotations.*.status' => 'nullable|string',
            'annotations.*.notes' => 'nullable|string',
            'annotations.*.metadata' => 'nullable|array'
        ]);

        $healthRecordId = $request->input('health_record_id');
        $annotations = $request->input('annotations');

        // Supprimer les anciennes annotations
        DentalAnnotation::where('health_record_id', $healthRecordId)->delete();

        // Créer les nouvelles annotations
        $createdAnnotations = [];
        foreach ($annotations as $annotation) {
            $annotation['health_record_id'] = $healthRecordId;
            $createdAnnotations[] = DentalAnnotation::create($annotation);
        }

        return response()->json([
            'success' => true,
            'message' => 'Annotations dentaires sauvegardées avec succès',
            'data' => $createdAnnotations,
            'count' => count($createdAnnotations)
        ]);
    }

    /**
     * Obtenir les statistiques dentaires pour un dossier de santé
     */
    public function getStats(Request $request): JsonResponse
    {
        $healthRecordId = $request->input('health_record_id');
        
        if (!$healthRecordId) {
            return response()->json(['error' => 'health_record_id requis'], 400);
        }

        $annotations = DentalAnnotation::where('health_record_id', $healthRecordId)->get();

        $stats = [
            'total' => $annotations->count(),
            'fixed' => $annotations->where('status', 'fixed')->count(),
            'selected' => $annotations->where('status', 'selected')->count(),
            'problem' => $annotations->where('status', 'problem')->count(),
            'warning' => $annotations->where('status', 'warning')->count(),
            'normal' => $annotations->where('status', 'normal')->count(),
            'with_notes' => $annotations->whereNotNull('notes')->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Réinitialiser toutes les annotations pour un dossier de santé
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'health_record_id' => 'required|exists:health_records,id'
        ]);

        $healthRecordId = $request->input('health_record_id');
        
        DentalAnnotation::where('health_record_id', $healthRecordId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Annotations dentaires réinitialisées avec succès'
        ]);
    }
}
