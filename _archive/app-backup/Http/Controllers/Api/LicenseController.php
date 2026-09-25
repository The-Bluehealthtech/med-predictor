<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequestFormRequest;
use App\Http\Requests\LicenseApprovalRequest;
use App\Services\SecurityLogService;
use Illuminate\Http\Request;
use App\Models\PlayerLicense;
use App\Models\Player;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LicenseController extends Controller
{
    // POST /api/licenses/request
    public function requestLicense(LicenseRequestFormRequest $request)
    {
        try {
            $user = Auth::user();
            
            // Vérifier les permissions avec la Policy
            if (!$user->can('create', PlayerLicense::class)) {
                SecurityLogService::logUnauthorizedAccess('license_creation', [
                    'user_id' => $user->id,
                    'user_role' => $user->role
                ], request());
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Traiter l'upload du document
            $documentPath = null;
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $documentPath = $file->storeAs('private/licenses', $fileName, 'local');
            }

            $license = PlayerLicense::create([
                'player_id' => $request->player_id,
                'license_type' => $request->license_type,
                'status' => 'pending',
                'requested_by' => $user->id,
                'document_path' => $documentPath,
                'notes' => $request->notes,
                'club_id' => $user->club_id,
            ]);

            // Log de l'action
            SecurityLogService::logLicenseAction('created', $license->id, [
                'player_id' => $license->player_id,
                'user_id' => $user->id,
                'club_id' => $user->club_id
            ], request());

            return response()->json([
                'message' => 'Demande de licence soumise avec succès',
                'license' => $license->load('player')
            ], 201);

        } catch (\Exception $e) {
            SecurityLogService::logSecurityError('License creation error', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null
            ], request());
            return response()->json(['error' => 'Erreur lors de la création de la licence'], 500);
        }
    }

    // GET /api/licenses/queue
    public function queue(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Vérifier les permissions avec la Policy
            if (!$user->can('viewAny', PlayerLicense::class)) {
                Log::warning('Tentative d\'accès non autorisé à la file d\'attente', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'ip' => request()->ip()
                ]);
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Filtrer selon le rôle
            $query = PlayerLicense::with(['player', 'requestedByUser']);
            
            if ($user->role === 'admin') {
                // Les admins voient toutes les licences en attente
                $licenses = $query->where('status', 'pending')->get();
            } else {
                // Les autres rôles voient seulement les licences de leur club
                $licenses = $query->where('status', 'pending')
                    ->where('club_id', $user->club_id)
                    ->get();
            }

            Log::info('Consultation de la file d\'attente des licences', [
                'user_id' => $user->id,
                'count' => $licenses->count()
            ]);

            return response()->json($licenses);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la consultation de la file d\'attente', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null
            ]);
            return response()->json(['error' => 'Erreur lors de la consultation de la file d\'attente'], 500);
        }
    }

    // POST /api/licenses/{license}/approve
    public function approve(LicenseApprovalRequest $request, $license_id)
    {
        try {
            $user = Auth::user();
            $license = PlayerLicense::findOrFail($license_id);
            
            // Vérifier les permissions avec la Policy
            if (!$user->can('approve', $license)) {
                Log::warning('Tentative d\'approbation non autorisée', [
                    'user_id' => $user->id,
                    'license_id' => $license_id,
                    'ip' => request()->ip()
                ]);
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Générer un numéro de licence unique
            $license->status = 'approved';
            $license->license_number = 'LIC-' . strtoupper(Str::random(8));
            $license->approved_by = $user->id;
            $license->approved_at = now();
            $license->save();

            // Log de l'action
            Log::info('Licence approuvée', [
                'license_id' => $license->id,
                'approved_by' => $user->id,
                'license_number' => $license->license_number
            ]);

            // Notifier le club (à implémenter)
            $requestedBy = User::find($license->requested_by);
            if ($requestedBy) {
                // Notification::send($requestedBy, new LicenseApprovedNotification($license));
            }

            return response()->json([
                'message' => 'Licence approuvée avec succès',
                'license' => $license->load(['player', 'approvedByUser'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Licence non trouvée'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'approbation de licence', [
                'error' => $e->getMessage(),
                'license_id' => $license_id,
                'user_id' => $user->id ?? null
            ]);
            return response()->json(['error' => 'Erreur lors de l\'approbation de la licence'], 500);
        }
    }

    // POST /api/licenses/{license}/reject
    public function reject(LicenseApprovalRequest $request, $license_id)
    {
        try {
            $user = Auth::user();
            $license = PlayerLicense::findOrFail($license_id);
            
            // Vérifier les permissions avec la Policy
            if (!$user->can('reject', $license)) {
                Log::warning('Tentative de rejet non autorisée', [
                    'user_id' => $user->id,
                    'license_id' => $license_id,
                    'ip' => request()->ip()
                ]);
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $license->status = 'rejected';
            $license->rejection_reason = $request->reason;
            $license->approved_by = $user->id;
            $license->approved_at = now();
            $license->save();

            // Log de l'action
            Log::info('Licence rejetée', [
                'license_id' => $license->id,
                'rejected_by' => $user->id,
                'reason' => $request->reason
            ]);

            // Notifier le club (à implémenter)
            $requestedBy = User::find($license->requested_by);
            if ($requestedBy) {
                // Notification::send($requestedBy, new LicenseRejectedNotification($license));
            }

            return response()->json([
                'message' => 'Licence rejetée avec succès',
                'license' => $license->load(['player', 'approvedByUser'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Licence non trouvée'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors du rejet de licence', [
                'error' => $e->getMessage(),
                'license_id' => $license_id,
                'user_id' => $user->id ?? null
            ]);
            return response()->json(['error' => 'Erreur lors du rejet de la licence'], 500);
        }
    }

    // POST /api/licenses/{license}/request-info
    public function requestInfo(Request $request, $license_id)
    {
        $user = Auth::user();
        if ($user->role !== 'license_agent') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $request->validate([
            'info_message' => ['required', 'string'],
        ]);
        $license = PlayerLicense::findOrFail($license_id);
        if ($license->status !== 'pending_review') {
            return response()->json(['error' => 'Statut non valide pour demande d\'info'], 400);
        }
        $license->status = 'awaiting_documents';
        $license->rejection_reason = $request->info_message;
        $license->processed_by_license_agent_id = $user->id;
        $license->save();
        // Notifier le club (à adapter)
        $clubAdmin = User::find($license->submitted_by_club_admin_id);
        if ($clubAdmin) {
            // Notification::send($clubAdmin, new LicenseInfoRequestedNotification($license));
        }
        return response()->json(['message' => 'Demande d\'information envoyée', 'license' => $license]);
    }
}
