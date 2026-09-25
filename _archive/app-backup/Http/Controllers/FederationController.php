<?php

namespace App\Http\Controllers;

use App\Models\Federation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\FederationRequest;
use App\Services\AuditTrailService;

class FederationController extends Controller
{
    protected $auditTrailService;

    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\PermissionMiddleware::class . ':manage_federations');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Federation::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('fifa_code', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by region
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $federations = $query->paginate(15);

        // Get statistics
        $stats = [
            'total' => Federation::count(),
            'active' => Federation::where('status', 'active')->count(),
            'inactive' => Federation::where('status', 'inactive')->count(),
            'suspended' => Federation::where('status', 'suspended')->count(),
        ];

        $this->auditTrailService->logDataAccess(
            'view',
            'Viewed federation list',
            'Federation',
            null
        );

        return view('federations.index', compact('federations', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $regions = [
            'Europe' => 'Europe',
            'Asia' => 'Asia',
            'Africa' => 'Africa',
            'North America' => 'North America',
            'South America' => 'South America',
            'Oceania' => 'Oceania'
        ];

        $statuses = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended'
        ];

        return view('federations.create', compact('regions', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FederationRequest $request): RedirectResponse
    {
        $federation = Federation::create($request->validated());

        $this->auditTrailService->logModelChange(
            auth()->user(),
            'Federation',
            $federation->id,
            null,
            $federation->toArray(),
            'Created new federation'
        );

        return redirect()->route('federations.index')
            ->with('success', 'Federation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Federation $federation): View
    {
        $this->auditTrailService->logDataAccess(
            auth()->user(),
            'Federation',
            $federation->id,
            'Viewed federation details'
        );

        return view('federations.show', compact('federation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Federation $federation): View
    {
        $regions = [
            'Europe' => 'Europe',
            'Asia' => 'Asia',
            'Africa' => 'Africa',
            'North America' => 'North America',
            'South America' => 'South America',
            'Oceania' => 'Oceania'
        ];

        $statuses = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended'
        ];

        return view('federations.edit', compact('federation', 'regions', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FederationRequest $request, Federation $federation): RedirectResponse
    {
        $oldValues = $federation->toArray();
        $federation->update($request->validated());

        $this->auditTrailService->logModelChange(
            auth()->user(),
            'Federation',
            $federation->id,
            $oldValues,
            $federation->toArray(),
            'Updated federation'
        );

        return redirect()->route('federations.index')
            ->with('success', 'Federation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Federation $federation): RedirectResponse
    {
        $federationData = $federation->toArray();
        $federation->delete();

        $this->auditTrailService->logModelChange(
            auth()->user(),
            'Federation',
            $federation->id,
            $federationData,
            null,
            'Deleted federation'
        );

        return redirect()->route('federations.index')
            ->with('success', 'Federation deleted successfully.');
    }

    /**
     * Get federations for API
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $query = Federation::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        $federations = $query->get(['id', 'name', 'short_name', 'country', 'region', 'fifa_code', 'status']);

        return response()->json([
            'success' => true,
            'data' => $federations
        ]);
    }

    /**
     * Get federation details for API
     */
    public function apiShow(Federation $federation): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $federation
        ]);
    }

    /**
     * Update federation status
     */
    public function updateStatus(Request $request, Federation $federation): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended'
        ]);

        $oldStatus = $federation->status;
        $federation->update(['status' => $request->status]);

        $this->auditTrailService->logModelChange(
            auth()->user(),
            'Federation',
            $federation->id,
            ['status' => $oldStatus],
            ['status' => $request->status],
            'Updated federation status'
        );

        return response()->json([
            'success' => true,
            'message' => 'Federation status updated successfully',
            'data' => $federation
        ]);
    }
}
