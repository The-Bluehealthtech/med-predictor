<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\GPT4FraudDetectionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AssociationRegistrationController extends Controller
{
    protected $fraudDetectionService;

    public function __construct(GPT4FraudDetectionService $fraudDetectionService)
    {
        $this->fraudDetectionService = $fraudDetectionService;
    }

    /**
     * Show the association registration form
     */
    public function create()
    {
        return view('modules.association.registration');
    }

    /**
     * Store the association registration
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'association_name' => 'required|string|min:3|max:255',
            'association_type' => 'required|string|in:National Association,Regional Association,League Administrator,Refereeing Body',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|min:8|max:20',
            'address' => 'required|string|min:10|max:500',
            'country' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'association_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            // Perform GPT-4 fraud detection
            $fraudAnalysis = $this->fraudDetectionService->analyzeAssociationRegistration($validated);

            // Log the fraud analysis
            Log::info('Association Registration Fraud Analysis', [
                'association_name' => $validated['association_name'],
                'risk_score' => $fraudAnalysis['risk_score'],
                'status' => $fraudAnalysis['status'],
                'analysis' => $fraudAnalysis['analysis']
            ]);

            // Handle logo upload if provided
            if ($request->hasFile('association_logo')) {
                $logoPath = $request->file('association_logo')->store('association-logos', 'public');
                $validated['logo_url'] = Storage::url($logoPath);
            }

            // Create association record (simulate for now)
            // In a real application, you would save to database
            $associationData = [
                'name' => $validated['association_name'],
                'type' => $validated['association_type'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'],
                'address' => $validated['address'],
                'country' => $validated['country'],
                'description' => $validated['description'] ?? '',
                'logo_url' => $validated['logo_url'] ?? null,
                'fraud_analysis' => $fraudAnalysis,
                'registration_date' => now(),
                'status' => $fraudAnalysis['status']
            ];

            // Log the registration
            Log::info('Association Registration Completed', [
                'association_name' => $validated['association_name'],
                'fraud_status' => $fraudAnalysis['status'],
                'risk_score' => $fraudAnalysis['risk_score']
            ]);

            return redirect()->route('association.dashboard')
                ->with('success', 'Association enregistrée avec succès après validation GPT-4')
                ->with('fraud_analysis', $fraudAnalysis);

        } catch (\Exception $e) {
            Log::error('Association Registration Error', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return back()->withErrors(['error' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * API endpoint for fraud detection
     */
    public function fraudDetection(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'association_name' => 'required|string|min:3|max:255',
                'association_type' => 'required|string|in:National Association,Regional Association,League Administrator,Refereeing Body',
                'contact_email' => 'required|email|max:255',
                'contact_phone' => 'required|string|min:8|max:20',
                'address' => 'required|string|min:10|max:500',
                'country' => 'required|string|max:100',
                'description' => 'nullable|string|max:1000'
            ]);

            // Perform GPT-4 fraud detection
            $fraudAnalysis = $this->fraudDetectionService->analyzeAssociationRegistration($validated);

            // Log the API call
            Log::info('Fraud Detection API Call', [
                'association_name' => $validated['association_name'],
                'risk_score' => $fraudAnalysis['risk_score'],
                'status' => $fraudAnalysis['status']
            ]);

            return response()->json($fraudAnalysis);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Fraud Detection API Error', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Erreur lors de l\'analyse de fraude'
            ], 500);
        }
    }

    /**
     * Test GPT-4 connectivity
     */
    public function testGPT4Connection(): JsonResponse
    {
        try {
            $isConnected = $this->fraudDetectionService->testConnection();
            
            return response()->json([
                'connected' => $isConnected,
                'message' => $isConnected ? 'GPT-4 connection successful' : 'GPT-4 connection failed',
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('GPT-4 Connection Test Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'connected' => false,
                'message' => 'GPT-4 connection test failed',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get fraud detection statistics
     */
    public function getFraudStats(): JsonResponse
    {
        try {
            // In a real application, you would fetch from database
            $stats = [
                'total_analyses' => 150,
                'approved_registrations' => 120,
                'rejected_registrations' => 30,
                'average_risk_score' => 25.5,
                'high_risk_cases' => 15,
                'gpt4_success_rate' => 98.5,
                'last_analysis' => now()->subMinutes(5)->toISOString()
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Fraud Stats Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to fetch fraud statistics'
            ], 500);
        }
    }
} 