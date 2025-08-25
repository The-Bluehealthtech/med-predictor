<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\UploadedDocument;
use App\Models\Athlete;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SecretaryController extends Controller
{
    /**
     * Dashboard du secrétariat
     */
    public function dashboard()
    {
        $stats = [
            'total_appointments' => Appointment::count(),
            'upcoming_appointments' => Appointment::upcoming()->count(),
            'total_documents' => UploadedDocument::count(),
            'pending_documents' => UploadedDocument::byStatus('pending')->count(),
        ];

        $recentAppointments = Appointment::with(['athlete', 'doctor'])
            ->orderBy('appointment_date', 'desc')
            ->limit(10)
            ->get();

        $recentDocuments = UploadedDocument::with(['athlete', 'uploadedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('secretary.dashboard', compact('stats', 'recentAppointments', 'recentDocuments'));
    }

    /**
     * Rechercher un athlète par FIFA Connect ID ou nom
     */
    public function searchAthlete(Request $request): JsonResponse
    {
        $query = $request->get('query');
        
        if (empty($query)) {
            return response()->json(['athletes' => []]);
        }

        $athletes = Athlete::where('fifa_connect_id', 'LIKE', "%{$query}%")
            ->orWhere('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'fifa_connect_id', 'email', 'phone')
            ->limit(10)
            ->get();

        return response()->json(['athletes' => $athletes]);
    }

    /**
     * Obtenir les détails d'un athlète
     */
    public function getAthleteDetails(string $fifaConnectId): JsonResponse
    {
        $athlete = Athlete::where('fifa_connect_id', $fifaConnectId)
            ->with(['appointments' => function($query) {
                $query->orderBy('appointment_date', 'desc')->limit(5);
            }, 'documents' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }])
            ->first();

        if (!$athlete) {
            return response()->json(['error' => 'Athlète non trouvé'], 404);
        }

        return response()->json(['athlete' => $athlete]);
    }

    /**
     * Créer un rendez-vous
     */
    public function createAppointment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fifa_connect_id' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'appointment_date' => 'required|date|after:now',
            'type' => 'required|in:consultation,examination,follow_up,emergency',
            'location' => 'nullable|string|max:255',
            'doctor_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $appointment = Appointment::createForAthlete(
                $request->fifa_connect_id,
                $request->all()
            );

            return response()->json([
                'message' => 'Rendez-vous créé avec succès',
                'appointment' => $appointment->load(['athlete', 'doctor'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Obtenir les rendez-vous
     */
    public function getAppointments(Request $request): JsonResponse
    {
        $query = Appointment::with(['athlete', 'doctor']);

        // Filtrer par athlète si spécifié
        if ($request->has('fifa_connect_id')) {
            $query->where('fifa_connect_id', $request->fifa_connect_id);
        }

        // Filtrer par statut
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filtrer par type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filtrer par date
        if ($request->has('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(20);

        return response()->json($appointments);
    }

    /**
     * Mettre à jour un rendez-vous
     */
    public function updateAppointment(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'appointment_date' => 'sometimes|date',
            'status' => 'sometimes|in:scheduled,confirmed,completed,cancelled',
            'type' => 'sometimes|in:consultation,examination,follow_up,emergency',
            'location' => 'nullable|string|max:255',
            'doctor_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appointment->update($request->all());

        return response()->json([
            'message' => 'Rendez-vous mis à jour avec succès',
            'appointment' => $appointment->load(['athlete', 'doctor'])
        ]);
    }

    /**
     * Supprimer un rendez-vous
     */
    public function deleteAppointment(int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json(['message' => 'Rendez-vous supprimé avec succès']);
    }

    /**
     * Uploader un document
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fifa_connect_id' => 'required|string',
            'document' => 'required|file|max:10240', // 10MB max
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:medical_record,imaging,lab_result,prescription,certificate,other'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $file = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents', $filename, 'public');

            $document = UploadedDocument::createForAthlete($request->fifa_connect_id, [
                'uploaded_by' => auth()->id(),
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'title' => $request->title,
                'description' => $request->description,
                'document_type' => $request->document_type,
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Document uploadé avec succès',
                'document' => $document->load(['athlete', 'uploadedBy'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Obtenir les documents
     */
    public function getDocuments(Request $request): JsonResponse
    {
        $query = UploadedDocument::with(['athlete', 'uploadedBy']);

        // Filtrer par athlète si spécifié
        if ($request->has('fifa_connect_id')) {
            $query->where('fifa_connect_id', $request->fifa_connect_id);
        }

        // Filtrer par type
        if ($request->has('document_type')) {
            $query->byType($request->document_type);
        }

        // Filtrer par statut
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($documents);
    }

    /**
     * Supprimer un document
     */
    public function deleteDocument(int $id): JsonResponse
    {
        $document = UploadedDocument::findOrFail($id);
        
        // Supprimer le fichier physique
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['message' => 'Document supprimé avec succès']);
    }

    /**
     * Analyser un document avec l'IA
     */
    public function analyzeDocument(int $id): JsonResponse
    {
        $document = UploadedDocument::findOrFail($id);

        // Simulation de l'analyse IA
        $analysis = [
            'document_type' => $document->document_type,
            'confidence' => rand(80, 95),
            'extracted_text' => 'Texte extrait du document...',
            'key_findings' => [
                'Trouvé: Informations médicales importantes',
                'Recommandations: Suivi recommandé'
            ],
            'analyzed_at' => now()->toISOString()
        ];

        $document->update([
            'ai_analysis' => $analysis,
            'status' => 'analyzed'
        ]);

        return response()->json([
            'message' => 'Document analysé avec succès',
            'analysis' => $analysis
        ]);
    }

    /**
     * Obtenir les statistiques
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'appointments' => [
                'total' => Appointment::count(),
                'upcoming' => Appointment::upcoming()->count(),
                'today' => Appointment::whereDate('appointment_date', today())->count(),
                'by_status' => Appointment::selectRaw('status, count(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status')
            ],
            'documents' => [
                'total' => UploadedDocument::count(),
                'pending' => UploadedDocument::byStatus('pending')->count(),
                'analyzed' => UploadedDocument::byStatus('analyzed')->count(),
                'by_type' => UploadedDocument::selectRaw('document_type, count(*) as count')
                    ->groupBy('document_type')
                    ->pluck('count', 'document_type')
            ],
            'athletes' => [
                'total' => Athlete::count(),
                'with_appointments' => Athlete::whereHas('appointments')->count(),
                'with_documents' => Athlete::whereHas('documents')->count()
            ]
        ];

        return response()->json($stats);
    }
} 