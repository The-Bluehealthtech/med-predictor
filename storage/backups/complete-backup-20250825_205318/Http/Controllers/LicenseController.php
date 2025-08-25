<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Club;
use App\Models\Association;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LicenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical,system_admin');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('LicenseController::index called', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'club_id' => $user->club_id,
            'association_id' => $user->association_id
        ]);

        $licenses = collect();

        // Check if user has club-related role - ONLY show club-specific data
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            if (!$user->club_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'Vous n\'êtes associé à aucun club.');
            }
            
            $licenses = License::where('club_id', $user->club_id)
                ->with(['club', 'association', 'requestedByUser', 'approvedByUser'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } 
        // Check if user has association-related role - show all clubs in association
        elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            if (!$user->association_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'Vous n\'êtes associé à aucune association.');
            }
            
            $licenses = License::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->with(['club', 'association', 'requestedByUser', 'approvedByUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        }
        // For system admin and admin, show all licenses
        elseif (in_array($user->role, ['system_admin', 'admin'])) {
            $licenses = License::with(['club', 'association', 'requestedByUser', 'approvedByUser'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('licenses.index', compact('licenses'));
    }

    public function create()
    {
        $user = Auth::user();
        $clubs = collect();
        $associations = collect();

        // Get clubs based on user role
        if (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        } elseif (in_array($user->role, ['system_admin', 'admin'])) {
            $clubs = Club::orderBy('name')->get();
        }

        // Get associations for system admin and admin
        if (in_array($user->role, ['system_admin', 'admin'])) {
            $associations = Association::orderBy('name')->get();
        }

        return view('licenses.create', compact('clubs', 'associations'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'license_type' => 'required|in:player,staff,medical',
            'applicant_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'club_id' => in_array($user->role, ['association_admin', 'association_registrar', 'association_medical']) ? 'required|exists:clubs,id' : 'nullable',
            'license_reason' => 'required|string|max:1000',
            'validity_period' => 'required|in:1_year,2_years,3_years,5_years',
            'id_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'medical_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'proof_of_age' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'additional_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Determine club_id based on user role
            if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
                $validated['club_id'] = $user->club_id;
            }

            // Upload documents
            $documents = [];
            $uploadFields = ['id_document', 'medical_certificate', 'proof_of_age'];
            
            foreach ($uploadFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('licenses/documents', 'public');
                    $documents[$field] = $path;
                }
            }

            // Handle additional documents
            if ($request->hasFile('additional_documents')) {
                $additionalDocs = [];
                foreach ($request->file('additional_documents') as $file) {
                    $path = $file->store('licenses/additional', 'public');
                    $additionalDocs[] = $path;
                }
                $documents['additional_documents'] = $additionalDocs;
            }

            // Create license with pending status
            $license = License::create([
                'license_type' => $validated['license_type'],
                'applicant_name' => $validated['applicant_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'nationality' => $validated['nationality'],
                'position' => $validated['position'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'club_id' => $validated['club_id'],
                'association_id' => $user->association_id ?? Club::find($validated['club_id'])->association_id,
                'license_reason' => $validated['license_reason'],
                'validity_period' => $validated['validity_period'],
                'documents' => $documents,
                'status' => 'pending',
                'requested_by' => $user->id,
                'requested_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('licenses.index')
                ->with('success', 'Demande de licence soumise avec succès. Elle sera examinée par l\'association.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded files on error
            foreach ($documents as $path) {
                if (is_array($path)) {
                    foreach ($path as $filePath) {
                        Storage::disk('public')->delete($filePath);
                    }
                } else {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()->withInput()
                ->with('error', 'Erreur lors de la soumission de la demande de licence: ' . $e->getMessage());
        }
    }

    public function edit(License $license)
    {
        $this->authorizeLicenseAccess($license);
        
        $user = Auth::user();
        $clubs = collect();
        $associations = collect();

        // Get clubs based on user role
        if (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        } elseif (in_array($user->role, ['system_admin', 'admin'])) {
            $clubs = Club::orderBy('name')->get();
        }

        return view('licenses.edit', compact('license', 'clubs'));
    }

    public function show(License $license)
    {
        $this->authorizeLicenseAccess($license);
        
        // Load relationships
        $license->load(['club', 'association', 'requestedByUser', 'approvedByUser']);
        
        // Return JSON for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'id' => $license->id,
                'applicant_name' => $license->applicant_name,
                'email' => $license->email,
                'phone' => $license->phone,
                'date_of_birth' => $license->date_of_birth ? $license->date_of_birth->format('d/m/Y') : null,
                'nationality' => $license->nationality,
                'position' => $license->position,
                'license_type_label' => $license->license_type_label,
                'license_reason' => $license->license_reason,
                'validity_period_label' => $license->validity_period_label,
                'status_badge' => $license->status_badge,
                'documents' => $license->documents,
                'club' => $license->club ? [
                    'id' => $license->club->id,
                    'name' => $license->club->name,
                    'city' => $license->club->city
                ] : null,
                'association' => $license->association ? [
                    'id' => $license->association->id,
                    'name' => $license->association->name
                ] : null,
                'fraud_risk' => $license->fraud_risk,
                'fraud_score' => $license->fraud_score,
                'created_at' => $license->created_at ? $license->created_at->format('d/m/Y H:i') : null,
                'updated_at' => $license->updated_at ? $license->updated_at->format('d/m/Y H:i') : null,
            ]);
        }
        
        // Return view for regular requests
        return view('licenses.show', compact('license'));
    }

    public function update(Request $request, License $license)
    {
        $this->authorizeLicenseAccess($license);
        
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'license_reason' => 'required|string|max:1000',
            'validity_period' => 'required|in:1_year,2_years,3_years,5_years',
        ]);

        $license->update($validated);

        return redirect()->route('licenses.index')
            ->with('success', 'Licence modifiée avec succès.');
    }

    public function destroy(License $license)
    {
        $this->authorizeLicenseAccess($license);
        
        // Clean up documents
        if ($license->documents) {
            foreach ($license->documents as $path) {
                if (is_array($path)) {
                    foreach ($path as $filePath) {
                        Storage::disk('public')->delete($filePath);
                    }
                } else {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $license->delete();

        return redirect()->route('licenses.index')
            ->with('success', 'Licence supprimée.');
    }

    public function approve(License $license)
    {
        $user = Auth::user();
        
        // Only association users can approve licenses
        if (!in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            return back()->with('error', 'Vous n\'avez pas les permissions pour approuver cette licence.');
        }

        $license->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('licenses.index')
            ->with('success', 'Licence approuvée avec succès.');
    }

    public function reject(License $license)
    {
        $user = Auth::user();
        
        // Only association users can reject licenses
        if (!in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            return back()->with('error', 'Vous n\'avez pas les permissions pour rejeter cette licence.');
        }

        $license->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('licenses.index')
            ->with('success', 'Licence rejetée.');
    }

    public function validation()
    {
        $user = Auth::user();
        
        // System admin and association users can access this page
        if (!in_array($user->role, ['system_admin', 'association_admin', 'association_registrar', 'association_medical'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions pour accéder à cette page.');
        }
        
        // For System Admin, show all licenses. For association users, filter by association
        if ($user->role === 'system_admin') {
            $licenses = License::with(['club', 'association', 'requestedByUser', 'approvedByUser'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            
            // Statistics for System Admin
            $pendingCount = License::where('status', 'pending')->count();
            $approvedCount = License::where('status', 'approved')->count();
            $rejectedCount = License::where('status', 'rejected')->count();
            $totalCount = $pendingCount + $approvedCount + $rejectedCount;
            
            // All clubs for System Admin
            $clubs = Club::orderBy('name')->get();
        } else {
            // Association users - filter by association
            $licenses = License::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })
            ->with(['club', 'association', 'requestedByUser', 'approvedByUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
            // Statistics for association users
            $pendingCount = License::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })->where('status', 'pending')->count();
            
            $approvedCount = License::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })->where('status', 'approved')->count();
            
            $rejectedCount = License::whereHas('club', function ($query) use ($user) {
                $query->where('association_id', $user->association_id);
            })->where('status', 'rejected')->count();
            
            $totalCount = $pendingCount + $approvedCount + $rejectedCount;
            
            // Clubs of the association
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        }
        
        return view('licenses.validation', compact(
            'licenses', 
            'pendingCount', 
            'approvedCount', 
            'rejectedCount', 
            'totalCount',
            'clubs'
        ));
    }

    /**
     * Run fraud detection on all licenses
     */
    public function batchFraudDetection(Request $request): JsonResponse
    {
        try {
            $licenses = License::with(['player', 'club'])->get();
            $results = [];
            
            foreach ($licenses as $license) {
                $fraudAnalysis = $this->performFraudAnalysis($license);
                $results[] = [
                    'license_id' => $license->id,
                    'applicant_name' => $license->applicant_name,
                    'fraud_analysis' => $fraudAnalysis
                ];
                
                // Update license with fraud analysis
                $license->update([
                    'fraud_risk' => $fraudAnalysis['fraud_detected'] ? 'high' : 'low',
                    'fraud_score' => $fraudAnalysis['risk_score'],
                    'fraud_analysis' => json_encode($fraudAnalysis)
                ]);
            }
            
            $summary = [
                'total_analyzed' => count($results),
                'fraud_detected' => count(array_filter($results, fn($r) => $r['fraud_analysis']['fraud_detected'])),
                'high_risk' => count(array_filter($results, fn($r) => $r['fraud_analysis']['risk_score'] > 70)),
                'medium_risk' => count(array_filter($results, fn($r) => $r['fraud_analysis']['risk_score'] > 40 && $r['fraud_analysis']['risk_score'] <= 70)),
                'low_risk' => count(array_filter($results, fn($r) => $r['fraud_analysis']['risk_score'] <= 40))
            ];
            
            Log::info('License Batch Fraud Detection Completed', $summary);
            
            return response()->json($summary);
            
        } catch (\Exception $e) {
            Log::error('License Batch Fraud Detection Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to perform batch fraud detection',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze specific license for fraud
     */
    public function analyzeLicenseFraud(Request $request, $licenseId): JsonResponse
    {
        try {
            $license = License::with(['player', 'club'])->findOrFail($licenseId);
            
            $fraudAnalysis = $this->performFraudAnalysis($license);
            
            // Update license with fraud analysis
            $license->update([
                'fraud_risk' => $fraudAnalysis['fraud_detected'] ? 'high' : 'low',
                'fraud_score' => $fraudAnalysis['risk_score'],
                'fraud_analysis' => json_encode($fraudAnalysis)
            ]);
            
            Log::info('License Fraud Analysis Completed', [
                'license_id' => $licenseId,
                'fraud_detected' => $fraudAnalysis['fraud_detected'],
                'risk_score' => $fraudAnalysis['risk_score']
            ]);
            
            return response()->json($fraudAnalysis);
            
        } catch (\Exception $e) {
            Log::error('License Fraud Analysis Error', [
                'error' => $e->getMessage(),
                'license_id' => $licenseId
            ]);
            
            return response()->json([
                'error' => 'Failed to analyze license fraud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check all licenses for fraud
     */
    public function checkAllLicenses(Request $request): JsonResponse
    {
        try {
            $licenses = License::with(['player', 'club'])->get();
            $alerts = [];
            $totalChecked = 0;
            
            foreach ($licenses as $license) {
                $fraudAnalysis = $this->performFraudAnalysis($license);
                $totalChecked++;
                
                if ($fraudAnalysis['fraud_detected']) {
                    $alerts[] = [
                        'license_id' => $license->id,
                        'applicant_name' => $license->applicant_name,
                        'fraud_type' => $fraudAnalysis['fraud_types'][0] ?? 'Unknown',
                        'risk_score' => $fraudAnalysis['risk_score'],
                        'analysis' => $fraudAnalysis['detailed_analysis']
                    ];
                }
                
                // Update license with fraud analysis
                $license->update([
                    'fraud_risk' => $fraudAnalysis['fraud_detected'] ? 'high' : 'low',
                    'fraud_score' => $fraudAnalysis['risk_score'],
                    'fraud_analysis' => json_encode($fraudAnalysis)
                ]);
            }
            
            $summary = [
                'total_checked' => $totalChecked,
                'alerts_generated' => count($alerts),
                'summary' => "Vérification terminée: {$totalChecked} licences analysées, " . count($alerts) . " alertes générées"
            ];
            
            Log::info('License Fraud Check All Completed', $summary);
            
            return response()->json($summary);
            
        } catch (\Exception $e) {
            Log::error('License Fraud Check All Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Failed to check all licenses for fraud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze license for fraud patterns
     */
    private function performFraudAnalysis($license): array
    {
        $fraudDetected = false;
        $fraudTypes = [];
        $riskScore = 0;
        $analysis = "Analyse de fraude pour la licence: ";
        
        // Check for age fraud
        if ($license->player && $license->player->date_of_birth) {
            $claimedAge = $license->player->age ?? 0;
            $actualAge = Carbon::parse($license->player->date_of_birth)->age;
            
            if (abs($claimedAge - $actualAge) > 2) {
                $fraudDetected = true;
                $fraudTypes[] = 'age_fraud';
                $riskScore += 40;
                $analysis .= "Discrepancy d'âge détectée (claimé: {$claimedAge}, réel: {$actualAge}). ";
            }
        }
        
        // Check for identity fraud
        if ($license->applicant_name && $license->player) {
            $applicantName = strtolower($license->applicant_name);
            $playerName = strtolower($license->player->first_name . ' ' . $license->player->last_name);
            
            if ($applicantName !== $playerName) {
                $fraudDetected = true;
                $fraudTypes[] = 'identity_fraud';
                $riskScore += 35;
                $analysis .= "Nom du demandeur ne correspond pas au joueur. ";
            }
        }
        
        // Check for duplicate applications
        $duplicateApplications = License::where('applicant_email', $license->applicant_email)
            ->where('id', '!=', $license->id)
            ->count();
            
        if ($duplicateApplications > 0) {
            $fraudDetected = true;
            $fraudTypes[] = 'duplicate_application';
            $riskScore += 25;
            $analysis .= "Demandes multiples détectées. ";
        }
        
        // Check for suspicious patterns
        if ($license->created_at && $license->created_at->diffInDays(now()) < 1) {
            $riskScore += 10;
            $analysis .= "Demande très récente. ";
        }
        
        // Check for missing required documents
        if (!$license->documents || empty($license->documents)) {
            $riskScore += 15;
            $analysis .= "Documents requis manquants. ";
        }
        
        if ($fraudDetected) {
            $analysis .= "🚨 Fraude détectée. Vérification manuelle requise.";
        } else {
            $analysis .= "✅ Aucune fraude détectée.";
        }
        
        return [
            'fraud_detected' => $fraudDetected,
            'fraud_types' => $fraudTypes,
            'risk_score' => min($riskScore, 100),
            'detailed_analysis' => $analysis,
            'recommendations' => $fraudDetected ? 
                'Vérification manuelle requise. Contacter le demandeur pour clarification.' : 
                'Licence peut être approuvée automatiquement.',
            'analysis_timestamp' => now()->toISOString()
        ];
    }

    protected function authorizeLicenseAccess(License $license)
    {
        $user = Auth::user();
        
        // System admin and admin can access all licenses
        if (in_array($user->role, ['system_admin', 'admin'])) {
            return;
        }
        
        // Club users can only access their club's licenses
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            if ($license->club_id !== $user->club_id) {
                abort(403, 'Vous n\'avez pas accès à cette licence.');
            }
        }
        
        // Association users can access licenses from clubs in their association
        if (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            if ($license->association_id !== $user->association_id) {
                abort(403, 'Vous n\'avez pas accès à cette licence.');
            }
        }
    }
} 