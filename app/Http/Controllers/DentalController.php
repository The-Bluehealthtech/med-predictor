<?php

namespace App\Http\Controllers;

use App\Models\DentalAnnotation;
use App\Models\HealthRecord;
use App\Services\MedicalRecordAccess;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DentalController extends Controller
{
    private function authorizeRecord(Request $request, HealthRecord $record): void
    {
        app(MedicalRecordAccess::class)->authorize($request->user(), $record->player, null);
    }

    private function recordFromRequest(Request $request): HealthRecord
    {
        $record = HealthRecord::with('player')->findOrFail($request->input('health_record_id'));
        $this->authorizeRecord($request, $record);

        return $record;
    }

    private function authorizeAnnotation(Request $request, DentalAnnotation $annotation): void
    {
        $record = $annotation->healthRecord()->with('player')->firstOrFail();
        $this->authorizeRecord($request, $record);
    }

    /**
     * Obtenir toutes les annotations dentaires pour un dossier de santé
     */
    public function index(Request $request): JsonResponse
    {
        $healthRecordId = $request->input('health_record_id');
        
        if (!$healthRecordId) {
            return response()->json(['error' => 'health_record_id requis'], 400);
        }

        $record = $this->recordFromRequest($request);

        $annotations = DentalAnnotation::where('health_record_id', $record->id)
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
    public function show(Request $request, DentalAnnotation $dentalAnnotation): JsonResponse
    {
        $this->authorizeAnnotation($request, $dentalAnnotation);

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

        $this->recordFromRequest($request);

        $annotation = DentalAnnotation::create($request->only([
            'health_record_id',
            'tooth_id',
            'position_x',
            'position_y',
            'status',
            'notes',
            'metadata',
        ]));

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
        $this->authorizeAnnotation($request, $dentalAnnotation);

        $request->validate([
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'status' => 'nullable|string|in:normal,selected,fixed,problem,warning',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array'
        ]);

        $dentalAnnotation->update($request->only([
            'position_x',
            'position_y',
            'status',
            'notes',
            'metadata',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Annotation dentaire mise à jour avec succès',
            'data' => $dentalAnnotation
        ]);
    }

    /**
     * Supprimer une annotation dentaire
     */
    public function destroy(Request $request, DentalAnnotation $dentalAnnotation): JsonResponse
    {
        $this->authorizeAnnotation($request, $dentalAnnotation);
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

        $record = $this->recordFromRequest($request);
        $healthRecordId = $record->id;
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

        $record = $this->recordFromRequest($request);
        $annotations = DentalAnnotation::where('health_record_id', $record->id)->get();

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

        $record = $this->recordFromRequest($request);

        DentalAnnotation::where('health_record_id', $record->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Annotations dentaires réinitialisées avec succès'
        ]);
    }
}
