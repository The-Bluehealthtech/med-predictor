<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Player;
use App\Models\Club;
use App\Models\Federation;
use App\Services\FifaTransferService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    private FifaTransferService $fifaService;

    public function __construct(FifaTransferService $fifaService)
    {
        $this->fifaService = $fifaService;
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des transferts
     */
    public function index(Request $request): View
    {
        $query = Transfer::with(['player', 'clubOrigin', 'clubDestination', 'federationOrigin', 'federationDestination']);

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
            DB::beginTransaction();

            $player = Player::findOrFail($request->player_id);
            $clubOrigin = Club::findOrFail($request->club_origin_id);
            $clubDestination = Club::findOrFail($request->club_destination_id);

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

            $transfer = Transfer::create([
                'player_id' => $request->player_id,
                'club_origin_id' => $request->club_origin_id,
                'club_destination_id' => $request->club_destination_id,
                'federation_origin_id' => $clubOrigin->federation_id,
                'federation_destination_id' => $clubDestination->federation_id,
                'transfer_type' => $request->transfer_type,
                'transfer_status' => 'draft',
                'itc_status' => $isInternational ? 'not_requested' : 'not_required',
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
            Log::error('Error creating transfer', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du transfert',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Afficher un transfert spécifique
     */
    public function show(Transfer $transfer): View
    {
        $transfer->load([
            'player', 'clubOrigin', 'clubDestination', 
            'federationOrigin', 'federationDestination',
            'contract', 'documents', 'payments'
        ]);

        return view('transfers.show', compact('transfer'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Transfer $transfer): View
    {
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

            // Si le transfert est déjà soumis à FIFA, le mettre à jour
            if ($transfer->fifa_transfer_id) {
                $result = $this->fifaService->updateTransfer($transfer);
                
                if (!$result['success']) {
                    Log::warning('Failed to update transfer in FIFA', [
                        'transfer_id' => $transfer->id,
                        'error' => $result['error'],
                    ]);
                }
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

    /**
     * Soumettre un transfert à FIFA
     */
    public function submitToFifa(Transfer $transfer): JsonResponse
    {
        try {
            // Vérifier si le transfert peut être soumis
            if (!$transfer->canBeSubmitted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le transfert ne peut pas être soumis (documents manquants ou fenêtre de transfert fermée)',
                ], 400);
            }

            $result = $this->fifaService->createTransfer($transfer);

            if ($result['success']) {
                // Si c'est un transfert international, demander l'ITC
                if ($transfer->isItcRequired()) {
                    $itcResult = $this->fifaService->requestItc($transfer);
                    
                    if (!$itcResult['success']) {
                        Log::warning('Failed to request ITC', [
                            'transfer_id' => $transfer->id,
                            'error' => $itcResult['error'],
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Transfert soumis à FIFA avec succès',
                    'fifa_transfer_id' => $result['fifa_transfer_id'],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la soumission à FIFA',
                    'error' => $result['error'],
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error submitting transfer to FIFA', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la soumission à FIFA',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Vérifier le statut ITC
     */
    public function checkItcStatus(Transfer $transfer): JsonResponse
    {
        try {
            $result = $this->fifaService->checkItcStatus($transfer);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'status' => $result['status'],
                    'data' => $result['data'],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la vérification du statut ITC',
                    'error' => $result['error'],
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error checking ITC status', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut ITC',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques des transferts
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total' => Transfer::count(),
                'pending' => Transfer::where('transfer_status', 'pending')->count(),
                'approved' => Transfer::count(),
                'rejected' => Transfer::where('transfer_status', 'rejected')->count(),
                'international' => Transfer::where('is_international', true)->count(),
                'this_month' => Transfer::whereMonth('created_at', now()->month)->count(),
                'total_fees' => Transfer::where('transfer_status', 'approved')->sum('transfer_fee'),
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
