<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Player;
use App\Models\Club;
use App\Models\Federation;
use App\Services\Transfers\TmsTransferBridge;
use App\Services\Transfers\TmsTransferPreparation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class TransferController extends Controller
{
    public function __construct(
        private readonly TmsTransferPreparation $tmsPreparation,
        private readonly TmsTransferBridge $tmsBridge,
    ) {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des transferts
     */
    public function index(Request $request): View
    {
        $query = $this->scopeTransfersForUser(
            Transfer::with(['player', 'clubOrigin', 'clubDestination', 'federationOrigin', 'federationDestination'])
        );

        // Filtres
        if ($request->filled('status')) {
            $query->where('transfer_status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('transfer_type', $request->type);
        }

        if ($request->filled('club_id')) {
            $query->where(function($q) use ($request) {
                $q->where('club_origin_id', $request->club_id)
                  ->orWhere('club_destination_id', $request->club_id);
            });
        }

        if ($request->filled('player_id')) {
            $query->where('player_id', $request->player_id);
        }

        $transfers = $query->orderBy('created_at', 'desc')->paginate(20);
        $clubs = Club::all();
        $players = Player::all();

        return view('transfers.index', compact('transfers', 'clubs', 'players'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(): View
    {
        $this->authorizeTransferManagement();

        $players = Player::where('is_transfer_eligible', true)->get();
        $clubs = Club::where('can_conduct_transfers', true)->get();
        $federations = Federation::active()->get();

        return view('transfers.create', compact('players', 'clubs', 'federations'));
    }

    /**
     * Stocker un nouveau transfert
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeTransferManagement();

        $request->validate([
            'player_id' => 'required|exists:players,id',
            'club_origin_id' => 'required|exists:clubs,id',
            'club_destination_id' => 'required|exists:clubs,id|different:club_origin_id',
            'transfer_type' => 'required|in:permanent,loan,free_agent',
            'transfer_date' => 'required|date',
            'contract_start_date' => 'required|date|after_or_equal:transfer_date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'transfer_fee' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'is_minor_transfer' => 'boolean',
            'special_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $player = Player::withoutGlobalScopes()->findOrFail($request->player_id);
            $clubOrigin = Club::withoutGlobalScopes()->findOrFail($request->club_origin_id);
            $clubDestination = Club::withoutGlobalScopes()->findOrFail($request->club_destination_id);
            $this->authorizeTransferParties($player, $clubOrigin, $clubDestination);

            // Le club d'origine doit correspondre au club courant du joueur dans FIT.
            if ((int) $player->club_id !== (int) $clubOrigin->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le club d’origine ne correspond pas au club actuel du joueur dans FIT.',
                ], 422);
            }

            // Vérifier si le joueur est éligible au transfert
            if (!$player->is_transfer_eligible) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le joueur n\'est pas éligible au transfert',
                ], 400);
            }

            // Vérifier si les clubs peuvent effectuer des transferts
            if (!$clubOrigin->can_conduct_transfers || !$clubDestination->can_conduct_transfers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Un ou les deux clubs ne peuvent pas effectuer de transferts',
                ], 400);
            }

            // Déterminer si c'est un transfert international
            $isInternational = $clubOrigin->country !== $clubDestination->country;

            DB::beginTransaction();

            $transfer = Transfer::create([
                'player_id' => $request->player_id,
                'club_origin_id' => $request->club_origin_id,
                'club_destination_id' => $request->club_destination_id,
                'federation_origin_id' => $clubOrigin->federation_id,
                'federation_destination_id' => $clubDestination->federation_id,
                'transfer_type' => $request->transfer_type,
                'transfer_status' => 'draft',
                // Le schéma canonique des transferts n'a pas de valeur « not_required ».
                // Pour un transfert national, is_international=false porte la sémantique « ITC non requis ».
                'itc_status' => 'not_requested',
                'transfer_window_start' => now()->startOfMonth(),
                'transfer_window_end' => now()->endOfMonth()->addDays(7),
                'transfer_date' => $request->transfer_date,
                'contract_start_date' => $request->contract_start_date,
                'contract_end_date' => $request->contract_end_date,
                'transfer_fee' => $request->transfer_fee,
                'currency' => $request->currency,
                'payment_status' => $request->transfer_fee > 0 ? 'pending' : 'completed',
                'is_minor_transfer' => $request->boolean('is_minor_transfer'),
                'is_international' => $isInternational,
                'special_conditions' => $request->special_conditions,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // Créer le contrat associé
            $transfer->contract()->create([
                'player_id' => $request->player_id,
                'club_id' => $request->club_destination_id,
                'transfer_id' => $transfer->id,
                'contract_type' => $request->transfer_type,
                'start_date' => $request->contract_start_date,
                'end_date' => $request->contract_end_date,
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Transfer created', [
                'transfer_id' => $transfer->id,
                'player_id' => $request->player_id,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transfert créé avec succès',
                'transfer_id' => $transfer->id,
                'redirect_url' => route('transfers.show', $transfer->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof HttpExceptionInterface) {
                throw $e;
            }
            Log::error('Error creating transfer', [
                'error_class' => $e::class,
                'error' => $e->getMessage(),
                'player_id' => $request->input('player_id'),
                'club_destination_id' => $request->input('club_destination_id'),
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du transfert',
            ], 500);
        }
    }

    /**
     * Afficher un transfert spécifique
     */
    public function show(Request $request, Transfer $transfer): View
    {
        $this->authorizeTransferAccess($transfer);

        $transfer->load([
            'player', 'clubOrigin', 'clubDestination',
            'federationOrigin', 'federationDestination',
            'contract', 'documents', 'payments'
        ]);
        $requiredDocuments = ['passport', 'contract'];
        if ($transfer->is_minor_transfer) {
            $requiredDocuments[] = 'parental_consent';
        }
        $approvedDocuments = $transfer->documents->where('validation_status', 'approved')->pluck('document_type')->all();
        $missingDocuments = array_values(array_diff($requiredDocuments, $approvedDocuments));
        $tmsReadiness = $this->tmsPreparation->readiness($transfer);
        $tmsBridgeConfigured = $this->tmsBridge->isConfigured();
        $user = $request->user();
        $clubOperator = $user && in_array($user->role, ['club_admin', 'club_manager'], true)
            && $user->club_id && in_array((int) $user->club_id, [(int) $transfer->club_origin_id, (int) $transfer->club_destination_id], true);
        $associationOperator = $user && in_array($user->role, ['association_admin', 'association_registrar'], true)
            && $user->association_id && in_array((int) $user->association_id, [(int) $transfer->clubOrigin?->association_id, (int) $transfer->clubDestination?->association_id], true);
        $canUploadDocuments = in_array($transfer->transfer_status, ['draft', 'rejected'], true) && ($clubOperator || $associationOperator);
        $canValidateDocuments = $associationOperator;
        $canDownloadDocuments = ($user?->isSystemAdmin() ?? false) || $clubOperator || $associationOperator;

        return view('transfers.show', compact(
            'transfer', 'requiredDocuments', 'approvedDocuments', 'missingDocuments', 'tmsReadiness', 'tmsBridgeConfigured',
            'canUploadDocuments', 'canValidateDocuments', 'canDownloadDocuments', 'associationOperator'
        ));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Transfer $transfer): View
    {
        $this->authorizeTransferManagement();
        $this->authorizeTransferAccess($transfer);

        $transfer->load(['player', 'clubOrigin', 'clubDestination']);
        $clubs = Club::where('can_conduct_transfers', true)->get();
        $federations = Federation::active()->get();

        return view('transfers.edit', compact('transfer', 'clubs', 'federations'));
    }

    /**
     * Mettre à jour un transfert
     */
    public function update(Request $request, Transfer $transfer): JsonResponse
    {
        $this->authorizeTransferManagement();
        $this->authorizeTransferAccess($transfer);

        $request->validate([
            'transfer_fee' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'special_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $transfer->update([
                'transfer_fee' => $request->transfer_fee,
                'currency' => $request->currency,
                'payment_status' => $request->transfer_fee > 0 ? 'pending' : 'completed',
                'special_conditions' => $request->special_conditions,
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ]);

            if (in_array($transfer->tms_sync_status, ['ready', 'linked', 'synced'], true)) {
                $transfer->forceFill(['tms_sync_status' => 'stale'])->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfert mis à jour avec succès',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating transfer', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du transfert',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Supprimer un transfert
     */
    public function destroy(Transfer $transfer): JsonResponse
    {
        $this->authorizeTransferManagement();
        $this->authorizeTransferAccess($transfer);

        try {
            // Vérifier si le transfert peut être supprimé
            if (!in_array($transfer->transfer_status, ['draft', 'rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le transfert ne peut pas être supprimé dans son état actuel',
                ], 400);
            }

            $transfer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transfert supprimé avec succès',
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting transfer', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du transfert',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function prepareForTms(Request $request, Transfer $transfer)
    {
        $this->authorizeAssociationTransfer($request, $transfer);
        $prepared = $this->tmsPreparation->prepare($transfer, $request->user());
        if (!$request->expectsJson()) {
            return redirect()->route('transfers.show', $transfer)->with('success', 'Dossier FIT prêt pour traitement dans FIFA TMS.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Dossier FIT prêt pour traitement dans FIFA TMS.',
            'tms_sync_status' => $prepared->tms_sync_status,
            'payload_sha256' => $prepared->tms_payload_sha256,
        ]);
    }

    public function exportTmsPackage(Request $request, Transfer $transfer)
    {
        $this->authorizeAssociationTransfer($request, $transfer);
        abort_unless(in_array($transfer->tms_sync_status, ['ready', 'linked', 'synced'], true), 409);
        abort_unless(is_array($transfer->tms_snapshot) && $transfer->tms_payload_sha256, 409);

        $payload = [
            'schema' => 'FIT-TMS-PREPARATION/1.0',
            'fit_transfer_id' => $transfer->id,
            'prepared_at' => optional($transfer->tms_prepared_at)->toIso8601String(),
            'prepared_by' => $transfer->tms_prepared_by,
            'payload_sha256' => $transfer->tms_payload_sha256,
            'tms_transfer_id' => $transfer->tms_transfer_id,
            'snapshot' => $transfer->tms_snapshot,
        ];

        return response()->streamDownload(function () use ($payload) {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, 'FIT-TMS-transfer-'.$transfer->id.'.json', [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function linkTmsReference(Request $request, Transfer $transfer)
    {
        $this->authorizeAssociationTransfer($request, $transfer);
        $data = $request->validate(['tms_transfer_id' => 'required|string|max:255']);
        $linked = $this->tmsPreparation->link($transfer, $data['tms_transfer_id']);
        if (!$request->expectsJson()) {
            return redirect()->route('transfers.show', $transfer)->with('success', 'Référence TMS rattachée au dossier FIT.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Référence TMS rattachée au dossier FIT.',
            'tms_transfer_id' => $linked->tms_transfer_id,
            'tms_sync_status' => $linked->tms_sync_status,
        ]);
    }

    public function syncFromTms(Request $request, Transfer $transfer)
    {
        $this->authorizeAssociationTransfer($request, $transfer);
        abort_unless($transfer->tms_transfer_id, 409);

        $result = $this->tmsBridge->fetchTransfer($transfer->tms_transfer_id);
        if (!$result['success']) {
            $status = in_array(($result['code'] ?? null), ['not_configured', 'disabled'], true) ? 503 : 502;
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $result['error']], $status);
            }
            return back()->with('error', $result['error']);
        }

        $data = $result['data'];
        abort_unless(($data['tms_transfer_id'] ?? $transfer->tms_transfer_id) === $transfer->tms_transfer_id, 409);

        $updates = [
            'tms_sync_status' => 'synced',
            'tms_remote_status' => $data['status'] ?? null,
            'tms_last_synced_at' => now(),
            'tms_last_response' => $data,
        ];
        if (!empty($data['itc_id'])) {
            $updates['fifa_itc_id'] = $data['itc_id'];
        }
        if (in_array($data['itc_status'] ?? null, ['not_requested', 'requested', 'pending', 'approved', 'rejected', 'expired'], true)) {
            $updates['itc_status'] = $data['itc_status'];
        }
        $transfer->forceFill($updates)->save();

        if (!$request->expectsJson()) {
            return redirect()->route('transfers.show', $transfer)->with('success', 'Données FIFA TMS synchronisées dans FIT.');
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function submitToFifa(Transfer $transfer): JsonResponse
    {
        $this->authorizeTransferManagement();
        $this->authorizeTransferAccess($transfer);

        return response()->json([
            'success' => false,
            'code' => 'direct_tms_submission_disabled',
            'message' => 'FIT prépare le dossier ; le transfert doit être réalisé dans FIFA TMS puis rattaché dans FIT.',
        ], 410);
    }

    public function checkItcStatus(Transfer $transfer): JsonResponse
    {
        $this->authorizeTransferManagement();
        $this->authorizeTransferAccess($transfer);

        return response()->json([
            'success' => false,
            'code' => 'direct_itc_check_disabled',
            'message' => 'Le statut ITC doit provenir de FIFA TMS ; FIT ne lance plus de contrôle ITC direct.',
        ], 410);
    }

    private function scopeTransfersForUser(Builder $query): Builder
    {
        $user = Auth::user();
        abort_unless($user, 401);

        if ($user->isSystemAdmin()) {
            return $query;
        }

        if ($user->isPlayer()) {
            return $user->player_id
                ? $query->where('player_id', $user->player_id)
                : $query->whereRaw('1 = 0');
        }

        if (in_array($user->role, ['club_admin', 'club_manager'], true)) {
            return $user->club_id
                ? $query->where(function ($q) use ($user) {
                    $q->where('club_origin_id', $user->club_id)
                        ->orWhere('club_destination_id', $user->club_id);
                })
                : $query->whereRaw('1 = 0');
        }

        if (in_array($user->role, ['association_admin', 'association_registrar'], true)) {
            if (!$user->association_id) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where(function ($q) use ($user) {
                $q->whereHas('clubOrigin', fn ($club) =>
                    $club->where('association_id', $user->association_id)
                )->orWhereHas('clubDestination', fn ($club) =>
                    $club->where('association_id', $user->association_id)
                );
            });
        }

        return $query->whereRaw('1 = 0');
    }

    private function authorizeTransferManagement(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && (
                $user->isSystemAdmin()
                || in_array($user->role, ['club_admin', 'club_manager', 'association_admin', 'association_registrar'], true)
            ),
            403
        );
    }

    private function authorizeTransferAccess(Transfer $transfer): void
    {
        $visible = $this->scopeTransfersForUser(
            Transfer::query()->whereKey($transfer->getKey())
        )->exists();

        abort_unless($visible, 403);
    }

    private function authorizeAssociationTransfer(Request $request, Transfer $transfer): void
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['association_admin', 'association_registrar'], true), 403);
        $this->authorizeTransferAccess($transfer);
    }

    private function authorizeTransferParties(Player $player, Club $origin, Club $destination): void
    {
        $user = Auth::user();
        abort_unless($user, 401);

        if ($user->isSystemAdmin()) {
            return;
        }

        if (in_array($user->role, ['club_admin', 'club_manager'], true)) {
            abort_unless($user->club_id && in_array((int) $user->club_id, [(int) $origin->id, (int) $destination->id], true), 403);
            return;
        }

        if (in_array($user->role, ['association_admin', 'association_registrar'], true)) {
            abort_unless(
                $user->association_id
                && in_array((int) $user->association_id, [(int) $origin->association_id, (int) $destination->association_id], true),
                403
            );
            return;
        }

        abort(403);
    }

    /**
     * Obtenir les statistiques des transferts
     */
    public function statistics(): JsonResponse
    {
        try {
            $query = $this->scopeTransfersForUser(Transfer::query());

            $stats = [
                'total' => (clone $query)->count(),
                'pending' => (clone $query)->where('transfer_status', 'pending')->count(),
                'approved' => (clone $query)->where('transfer_status', 'approved')->count(),
                'rejected' => (clone $query)->where('transfer_status', 'rejected')->count(),
                'international' => (clone $query)->where('is_international', true)->count(),
                'this_month' => (clone $query)->whereMonth('created_at', now()->month)->count(),
                'total_fees' => (clone $query)->where('transfer_status', 'approved')->sum('transfer_fee'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transfer statistics', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
