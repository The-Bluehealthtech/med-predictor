<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Visit;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents.
     */
    public function index(Request $request): View
    {
        $query = Document::with(['athlete', 'visit']);

        // Filtres
        if ($request->filled('visit_id')) {
            $query->where('visit_id', $request->visit_id);
        }

        if ($request->filled('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20);
        $visits = Visit::with(['athlete'])->get();
        $athletes = Athlete::all();

        return view('documents.index', compact('documents', 'visits', 'athletes'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create(Request $request): View
    {
        $visitId = $request->get('visit_id');
        $athletes = Athlete::all();
        $visits = Visit::with(['athlete'])->get();

        return view('documents.create', compact('athletes', 'visits', 'visitId'));
    }

    /**
     * Store a newly created document.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'visit_id' => 'nullable|exists:visits,id',
            'document_type' => 'required|in:medical_report,consent_form,insurance_form,prescription,lab_result,imaging_report,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm,txt,doc,docx|max:10240',
            'metadata' => 'nullable|array',
        ]);

        // Upload du fichier
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        $validated['file_path'] = $filePath;
        $validated['file_name'] = $fileName;
        $validated['file_size'] = $file->getSize();
        $validated['mime_type'] = $file->getMimeType();

        $document = Document::create($validated);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document créé avec succès.');
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document): View
    {
        $document->load(['athlete', 'visit']);
        
        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(Document $document): View
    {
        $athletes = Athlete::all();
        $visits = Visit::with(['athlete'])->get();
        
        return view('documents.edit', compact('document', 'athletes', 'visits'));
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, Document $document): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'visit_id' => 'nullable|exists:visits,id',
            'document_type' => 'required|in:medical_report,consent_form,insurance_form,prescription,lab_result,imaging_report,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm,txt,doc,docx|max:10240',
            'metadata' => 'nullable|array',
        ]);

        // Si un nouveau fichier est uploadé
        if ($request->hasFile('file')) {
            // Supprimer l'ancien fichier
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $fileName;
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        $document->update($validated);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document mis à jour avec succès.');
    }

    /**
     * Remove the specified document.
     */
    public function destroy(Document $document): RedirectResponse
    {
        // Supprimer le fichier
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document supprimé avec succès.');
    }

    /**
     * Analyze uploaded document with AI.
     */
    public function analyzeUpload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            'visit_id' => 'nullable|exists:visits,id',
            'document_type' => 'required|in:medical_report,consent_form,insurance_form,prescription,lab_result,imaging_report,other',
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/temp', $fileName, 'public');

            // Appel à l'API Med-Gemini pour l'analyse
            $analysis = $this->callMedGeminiAI($validated['document_type'], $filePath);

            // Nettoyer le fichier temporaire
            Storage::disk('public')->delete($filePath);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'suggested_title' => $analysis['suggested_title'] ?? '',
                'suggested_description' => $analysis['suggested_description'] ?? '',
                'extracted_data' => $analysis['extracted_data'] ?? [],
            ]);

        } catch (\Exception $e) {
            Log::error('Document analysis failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse du document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a document.
     */
    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Fichier non trouvé.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }

    /**
     * Get documents for a specific visit.
     */
    public function getVisitDocuments(Visit $visit): JsonResponse
    {
        $documents = $visit->documents()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($document) {
                return [
                    'id' => $document->id,
                    'title' => $document->title,
                    'document_type' => $document->document_type,
                    'file_name' => $document->file_name,
                    'created_at' => $document->created_at->format('d/m/Y H:i'),
                    'download_url' => route('documents.download', $document),
                ];
            });

        return response()->json($documents);
    }

    /**
     * Call Med-Gemini AI for document analysis.
     */
    private function callMedGeminiAI(string $documentType, string $filePath): array
    {
        $apiUrl = config('services.med_gemini.url', 'http://localhost:3001');
        
        try {
            $response = Http::timeout(30)->post($apiUrl . '/api/v1/med-gemini/analyze', [
                'analysis_type' => $documentType . '_document',
                'file_path' => $filePath,
                'custom_prompt' => $this->getDocumentAnalysisPrompt($documentType),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Med-Gemini API response not successful', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $this->getMockDocumentAnalysis($documentType);

        } catch (\Exception $e) {
            Log::error('Med-Gemini API call failed: ' . $e->getMessage());
            return $this->getMockDocumentAnalysis($documentType);
        }
    }

    /**
     * Get analysis prompt for document type.
     */
    private function getDocumentAnalysisPrompt(string $documentType): string
    {
        return match ($documentType) {
            'medical_report' => 'Analysez ce rapport médical et extrayez les informations clés : diagnostic, traitement, recommandations, dates importantes.',
            'consent_form' => 'Analysez ce formulaire de consentement et extrayez les informations : type de consentement, date, signataire, procédure concernée.',
            'insurance_form' => 'Analysez ce formulaire d\'assurance et extrayez les informations : numéro de police, couverture, dates de validité.',
            'prescription' => 'Analysez cette ordonnance et extrayez les informations : médicaments, posologie, durée, instructions.',
            'lab_result' => 'Analysez ce résultat de laboratoire et extrayez les informations : paramètres, valeurs, normes, conclusions.',
            'imaging_report' => 'Analysez ce rapport d\'imagerie et extrayez les informations : type d\'examen, résultats, conclusions, recommandations.',
            default => 'Analysez ce document et extrayez les informations principales.',
        };
    }

    /**
     * Get mock analysis for document type.
     */
    private function getMockDocumentAnalysis(string $documentType): array
    {
        $mockData = [
            'medical_report' => [
                'suggested_title' => 'Rapport Médical - Consultation',
                'suggested_description' => 'Rapport de consultation médicale avec évaluation clinique',
                'extracted_data' => [
                    'diagnostic' => 'Diagnostic principal extrait',
                    'traitement' => 'Traitement prescrit',
                    'recommandations' => 'Recommandations médicales',
                ]
            ],
            'consent_form' => [
                'suggested_title' => 'Formulaire de Consentement',
                'suggested_description' => 'Consentement éclairé pour procédure médicale',
                'extracted_data' => [
                    'type_consentement' => 'Type de consentement',
                    'date_signature' => 'Date de signature',
                    'signataire' => 'Nom du signataire',
                ]
            ],
            'insurance_form' => [
                'suggested_title' => 'Formulaire d\'Assurance',
                'suggested_description' => 'Document d\'assurance médicale',
                'extracted_data' => [
                    'numero_police' => 'Numéro de police',
                    'couverture' => 'Type de couverture',
                    'date_validite' => 'Date de validité',
                ]
            ],
            'prescription' => [
                'suggested_title' => 'Ordonnance Médicale',
                'suggested_description' => 'Prescription de médicaments',
                'extracted_data' => [
                    'medicaments' => 'Liste des médicaments',
                    'posologie' => 'Posologie prescrite',
                    'duree' => 'Durée du traitement',
                ]
            ],
            'lab_result' => [
                'suggested_title' => 'Résultats de Laboratoire',
                'suggested_description' => 'Analyses de laboratoire',
                'extracted_data' => [
                    'parametres' => 'Paramètres analysés',
                    'valeurs' => 'Valeurs obtenues',
                    'normes' => 'Valeurs de référence',
                ]
            ],
            'imaging_report' => [
                'suggested_title' => 'Rapport d\'Imagerie',
                'suggested_description' => 'Résultats d\'examen d\'imagerie',
                'extracted_data' => [
                    'type_examen' => 'Type d\'examen',
                    'resultats' => 'Résultats de l\'examen',
                    'conclusions' => 'Conclusions médicales',
                ]
            ],
        ];

        return $mockData[$documentType] ?? $mockData['medical_report'];
    }
}
