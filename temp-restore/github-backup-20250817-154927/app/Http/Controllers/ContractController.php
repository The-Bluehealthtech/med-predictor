<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Models\Club;
use App\Models\Contract;
use App\Models\Player;
use App\Services\AuditTrailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Contract::with(['player:id,first_name,last_name', 'club:id,name']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('player', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhereHas('club', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by contract type
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by club
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $contracts = $query->paginate(15);

        // Get statistics
        $stats = [
            'total' => Contract::count(),
            'active' => Contract::where('status', 'active')->count(),
            'expired' => Contract::where('status', 'expired')->count(),
            'terminated' => Contract::where('status', 'terminated')->count(),
            'professional' => Contract::where('contract_type', 'professional')->count(),
            'amateur' => Contract::where('contract_type', 'amateur')->count(),
            'youth' => Contract::where('contract_type', 'youth')->count(),
            'loan' => Contract::where('contract_type', 'loan')->count(),
        ];

        $clubs = Club::orderBy('name')->get(['id', 'name']);

        AuditTrailService::logDataAccess(
            'view',
            'Viewed contract list',
            'Contract',
            null
        );

        return view('contracts.index', compact('contracts', 'stats', 'clubs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $players = Player::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $clubs = Club::orderBy('name')->get(['id', 'name']);
        
        $contractTypes = [
            'professional' => 'Professional',
            'amateur' => 'Amateur',
            'youth' => 'Youth',
            'loan' => 'Loan'
        ];

        $statuses = [
            'active' => 'Active',
            'expired' => 'Expired',
            'terminated' => 'Terminated',
            'suspended' => 'Suspended'
        ];

        $currencies = [
            'EUR' => 'Euro (EUR)',
            'USD' => 'US Dollar (USD)',
            'GBP' => 'British Pound (GBP)'
        ];

        return view('contracts.create', compact('players', 'clubs', 'contractTypes', 'statuses', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContractRequest $request): RedirectResponse
    {
        $contract = Contract::create($request->validated());

        AuditTrailService::logModelChange(
            'create',
            $contract,
            null,
            $contract->toArray(),
            'Created new contract'
        );

        return redirect()->route('contracts.index')
            ->with('success', 'Contract created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract): View
    {
        $contract->load(['player', 'club']);

        AuditTrailService::logDataAccess(
            'view',
            'Viewed contract details',
            'Contract',
            $contract->id
        );

        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract): View
    {
        $players = Player::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $clubs = Club::orderBy('name')->get(['id', 'name']);
        
        $contractTypes = [
            'professional' => 'Professional',
            'amateur' => 'Amateur',
            'youth' => 'Youth',
            'loan' => 'Loan'
        ];

        $statuses = [
            'active' => 'Active',
            'expired' => 'Expired',
            'terminated' => 'Terminated',
            'suspended' => 'Suspended'
        ];

        $currencies = [
            'EUR' => 'Euro (EUR)',
            'USD' => 'US Dollar (USD)',
            'GBP' => 'British Pound (GBP)'
        ];

        return view('contracts.edit', compact('contract', 'players', 'clubs', 'contractTypes', 'statuses', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContractRequest $request, Contract $contract): RedirectResponse
    {
        $oldValues = $contract->toArray();
        $contract->update($request->validated());

        AuditTrailService::logModelChange(
            'update',
            $contract,
            $oldValues,
            $contract->toArray(),
            'Updated contract'
        );

        return redirect()->route('contracts.index')
            ->with('success', 'Contract updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract): RedirectResponse
    {
        $contractData = $contract->toArray();
        $contract->delete();

        AuditTrailService::logModelChange(
            'delete',
            $contract,
            $contractData,
            null,
            'Deleted contract'
        );

        return redirect()->route('contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }

    /**
     * Get contracts for API
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $query = Contract::with(['player:id,first_name,last_name', 'club:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        if ($request->filled('player_id')) {
            $query->where('player_id', $request->player_id);
        }

        $contracts = $query->get();

        return response()->json([
            'success' => true,
            'data' => $contracts
        ]);
    }

    /**
     * Get contract details for API
     */
    public function apiShow(Contract $contract): JsonResponse
    {
        $contract->load(['player', 'club']);

        return response()->json([
            'success' => true,
            'data' => $contract
        ]);
    }

    /**
     * Update contract status
     */
    public function updateStatus(Request $request, Contract $contract): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,expired,terminated,suspended'
        ]);

        $oldStatus = $contract->status;
        $contract->update(['status' => $request->status]);

        AuditTrailService::logModelChange(
            'update',
            $contract,
            ['status' => $oldStatus],
            ['status' => $request->status],
            'Updated contract status'
        );

        return response()->json([
            'success' => true,
            'message' => 'Contract status updated successfully',
            'data' => $contract
        ]);
    }

    /**
     * Get player's current contract
     */
    public function getPlayerCurrentContract(Player $player): JsonResponse
    {
        $contract = Contract::where('player_id', $player->id)
            ->where('status', 'active')
            ->with(['club:id,name'])
            ->first();

        return response()->json([
            'success' => true,
            'data' => $contract
        ]);
    }

    /**
     * Get club's active contracts
     */
    public function getClubActiveContracts(Club $club): JsonResponse
    {
        $contracts = Contract::where('club_id', $club->id)
            ->where('status', 'active')
            ->with(['player:id,first_name,last_name'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contracts
        ]);
    }
}
