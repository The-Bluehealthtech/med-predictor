<?php

namespace App\Http\Controllers;

use App\Models\PlayerLicense as License;
use App\Models\Club;
use App\Models\Association;
use App\Models\User;
use App\Models\LicensePhoto;
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
        $this->middleware(
            'role:club_admin,club_manager,club_medical,association_admin,association_registrar,association_medical,system_admin'
        );
    }

    public function index(Request $request)
    {
        return redirect()->route('modules.licenses.index');
    }

    public function create(Request $request)
    {
        $playerId = filter_var(
            $request->query('player_id'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($playerId !== false) {
            return redirect()->route(
                'player-licenses.request.create',
                ['player' => (int) $playerId]
            );
        }

        return redirect()->route('modules.licenses.index');
    }

    public function store(Request $request)
    {
        return redirect()
            ->route('modules.licenses.index')
            ->with(
                'error',
                'Ancien formulaire de licence désactivé. Utilisez la demande liée à un joueur.'
            );
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
            'license_type' => 'required|in:amateur,semi_pro,professional,international',
            'player_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        try {
            DB::beginTransaction();

            // Gérer l'upload de photo si fournie
            if ($request->hasFile('player_photo')) {
                $photoPath = $request->file('player_photo')->store('player_photos', 'public');
                
                // Mettre à jour la photo du joueur si la licence est liée à un joueur
                if ($license->player_id) {
                    $license->player->update([
                        'player_picture' => $photoPath,
                        'updated_at' => now(),
                    ]);
                }
                
                // Créer un enregistrement de photo de licence
                LicensePhoto::create([
                    'player_id' => $license->player_id,
                    'club_id' => $license->club_id,
                    'photo_path' => $photoPath,
                    'uploaded_by' => auth()->id(),
                    'uploaded_at' => now(),
                ]);
            }

            // Mettre à jour la licence
            $license->update($validated);

            // Si la licence était en attente de correction, la remettre en attente d'approbation
            if (in_array($license->status, ['rejected', 'pending_correction'])) {
                $license->update([
                    'status' => 'pending',
                    'rejection_reason' => null, // Effacer la raison de rejet
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('licenses.index')
                ->with('success', 'Licence modifiée avec succès et remise en attente d\'approbation.');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Supprimer la photo si elle a été uploadée
            if (isset($photoPath) && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return redirect()->back()
                ->with('error', 'Erreur lors de la modification : ' . $e->getMessage())
                ->withInput();
        }
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

    public function approve(License $license): JsonResponse
    {
        $this->authorizeLicenseAccess($license);

        $user = Auth::user();

        abort_unless(
            $user->isSystemAdmin() || $user->isAssociationUser(),
            403
        );

        if ($license->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Seule une licence en attente peut être approuvée.',
            ], 422);
        }

        if (!$license->expiry_date || $license->expiry_date->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Une date d’expiration future est requise avant approbation.',
            ], 422);
        }

        $license->update([
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'issue_date' => now()->toDateString(),
            'issued_date' => now()->toDateString(),
            'issued_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Licence approuvée avec succès.',
        ]);
    }

    public function reject(Request $request, License $license): JsonResponse
    {
        $this->authorizeLicenseAccess($license);

        $user = Auth::user();

        abort_unless(
            $user->isSystemAdmin() || $user->isAssociationUser(),
            403
        );

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($license->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Seule une licence en attente peut être rejetée.',
            ], 422);
        }

        $license->update([
            'status' => 'revoked',
            'approval_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Licence rejetée.',
        ]);
    }

    public function validation()
    {
        $user = Auth::user();
        abort_unless($user, 401);

        abort_unless(
            $user->isSystemAdmin() || $user->isAssociationUser(),
            403
        );

        $query = License::query()
            ->with(['player', 'club'])
            ->orderByDesc('created_at');

        if ($user->isAssociationUser()) {
            abort_unless($user->association_id, 403);

            $query->whereHas('club', function ($club) use ($user) {
                $club->where('association_id', $user->association_id);
            });
        }

        $statsQuery = clone $query;

        $pendingCount = (clone $statsQuery)
            ->where('status', 'pending')
            ->count();
        $approvedCount = (clone $statsQuery)
            ->where('status', 'active')
            ->count();
        $rejectedCount = (clone $statsQuery)
            ->where('status', 'revoked')
            ->count();
        $totalCount = (clone $statsQuery)->count();

        $licenses = $query->paginate(20);

        return view('licenses.validation-canonical', compact(
            'licenses',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalCount'
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
        abort_unless($user, 401);

        if ($user->isSystemAdmin()) {
            return;
        }

        if ($user->isClubUser()) {
            abort_unless(
                $user->club_id
                && (int) $license->club_id === (int) $user->club_id,
                403,
                'Vous n\'avez pas accès à cette licence.'
            );
            return;
        }

        if ($user->isAssociationUser()) {
            abort_unless(
                $user->association_id
                && $license->club
                && (int) $license->club->association_id === (int) $user->association_id,
                403,
                'Vous n\'avez pas accès à cette licence.'
            );
            return;
        }

        abort(403, 'Vous n\'avez pas accès à cette licence.');
    }
} 