<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\LicenseType;
use App\Models\PlayerLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LicenseTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:system_admin,association_admin');
    }

    /**
     * Display a listing of license types
     */
    public function index(Request $request)
    {
        $query = LicenseType::with(['createdBy', 'updatedBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $licenseTypes = $query->orderBy('name')->paginate(15);

        // Get statistics
        $stats = [
            'total' => LicenseType::count(),
            'active' => LicenseType::where('is_active', true)->count(),
            'inactive' => LicenseType::where('is_active', false)->count(),
        ];

        return view('back-office.license-types.index', compact('licenseTypes', 'stats'));
    }

    /**
     * Show the form for creating a new license type
     */
    public function create()
    {
        $positions = \App\Helpers\NationalityHelper::getPositions();
        $currencies = ['USD', 'EUR', 'GBP', 'CAD', 'AUD'];
        
        return view('back-office.license-types.create', compact('positions', 'currencies'));
    }

    /**
     * Store a newly created license type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:license_types,code',
            'description' => 'nullable|string',
            'requirements' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'approval_process' => 'nullable|array',
            'validity_period_months' => 'required|integer|min:1|max:120',
            'fee_amount' => 'required|numeric|min:0',
            'fee_currency' => 'required|string|size:3',
            'requires_medical_clearance' => 'boolean',
            'requires_fitness_certificate' => 'boolean',
            'requires_contract' => 'boolean',
            'requires_work_permit' => 'boolean',
            'requires_international_clearance' => 'boolean',
            'age_restrictions' => 'nullable|array',
            'position_restrictions' => 'nullable|array',
            'experience_requirements' => 'nullable|array',
            'requires_association_approval' => 'boolean',
            'requires_club_approval' => 'boolean',
            'max_players_per_club' => 'nullable|integer|min:1',
            'max_players_total' => 'nullable|integer|min:1',
            'document_templates' => 'nullable|array',
            'notification_settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $licenseType = LicenseType::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'requirements' => $request->requirements,
                'validation_rules' => $request->validation_rules,
                'approval_process' => $request->approval_process,
                'validity_period_months' => $request->validity_period_months,
                'fee_amount' => $request->fee_amount,
                'fee_currency' => strtoupper($request->fee_currency),
                'requires_medical_clearance' => $request->boolean('requires_medical_clearance'),
                'requires_fitness_certificate' => $request->boolean('requires_fitness_certificate'),
                'requires_contract' => $request->boolean('requires_contract'),
                'requires_work_permit' => $request->boolean('requires_work_permit'),
                'requires_international_clearance' => $request->boolean('requires_international_clearance'),
                'age_restrictions' => $request->age_restrictions,
                'position_restrictions' => $request->position_restrictions,
                'experience_requirements' => $request->experience_requirements,
                'requires_association_approval' => $request->boolean('requires_association_approval'),
                'requires_club_approval' => $request->boolean('requires_club_approval'),
                'max_players_per_club' => $request->max_players_per_club,
                'max_players_total' => $request->max_players_total,
                'document_templates' => $request->document_templates,
                'notification_settings' => $request->notification_settings,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            Log::info('License type created', [
                'license_type_id' => $licenseType->id,
                'name' => $licenseType->name,
                'code' => $licenseType->code,
                'created_by' => auth()->id()
            ]);

            return redirect()->route('back-office.license-types.index')
                ->with('success', 'License type created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create license type', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create license type: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified license type
     */
    public function show(LicenseType $licenseType)
    {
        $licenseType->load(['createdBy', 'updatedBy']);
        
        // Get usage statistics
        $usageStats = [
            'total_licenses' => PlayerLicense::where('license_type', $licenseType->code)->count(),
            'active_licenses' => PlayerLicense::where('license_type', $licenseType->code)
                ->where('status', 'active')->count(),
            'pending_licenses' => PlayerLicense::where('license_type', $licenseType->code)
                ->where('status', 'pending')->count(),
            'expired_licenses' => PlayerLicense::where('license_type', $licenseType->code)
                ->where('status', 'expired')->count(),
        ];

        return view('back-office.license-types.show', compact('licenseType', 'usageStats'));
    }

    /**
     * Show the form for editing the specified license type
     */
    public function edit(LicenseType $licenseType)
    {
        $positions = \App\Helpers\NationalityHelper::getPositions();
        $currencies = ['USD', 'EUR', 'GBP', 'CAD', 'AUD'];
        
        return view('back-office.license-types.edit', compact('licenseType', 'positions', 'currencies'));
    }

    /**
     * Update the specified license type
     */
    public function update(Request $request, LicenseType $licenseType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:license_types,code,' . $licenseType->id,
            'description' => 'nullable|string',
            'requirements' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'approval_process' => 'nullable|array',
            'validity_period_months' => 'required|integer|min:1|max:120',
            'fee_amount' => 'required|numeric|min:0',
            'fee_currency' => 'required|string|size:3',
            'requires_medical_clearance' => 'boolean',
            'requires_fitness_certificate' => 'boolean',
            'requires_contract' => 'boolean',
            'requires_work_permit' => 'boolean',
            'requires_international_clearance' => 'boolean',
            'age_restrictions' => 'nullable|array',
            'position_restrictions' => 'nullable|array',
            'experience_requirements' => 'nullable|array',
            'requires_association_approval' => 'boolean',
            'requires_club_approval' => 'boolean',
            'max_players_per_club' => 'nullable|integer|min:1',
            'max_players_total' => 'nullable|integer|min:1',
            'document_templates' => 'nullable|array',
            'notification_settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $oldCode = $licenseType->code;
            
            $licenseType->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'requirements' => $request->requirements,
                'validation_rules' => $request->validation_rules,
                'approval_process' => $request->approval_process,
                'validity_period_months' => $request->validity_period_months,
                'fee_amount' => $request->fee_amount,
                'fee_currency' => strtoupper($request->fee_currency),
                'requires_medical_clearance' => $request->boolean('requires_medical_clearance'),
                'requires_fitness_certificate' => $request->boolean('requires_fitness_certificate'),
                'requires_contract' => $request->boolean('requires_contract'),
                'requires_work_permit' => $request->boolean('requires_work_permit'),
                'requires_international_clearance' => $request->boolean('requires_international_clearance'),
                'age_restrictions' => $request->age_restrictions,
                'position_restrictions' => $request->position_restrictions,
                'experience_requirements' => $request->experience_requirements,
                'requires_association_approval' => $request->boolean('requires_association_approval'),
                'requires_club_approval' => $request->boolean('requires_club_approval'),
                'max_players_per_club' => $request->max_players_per_club,
                'max_players_total' => $request->max_players_total,
                'document_templates' => $request->document_templates,
                'notification_settings' => $request->notification_settings,
                'updated_by' => auth()->id(),
            ]);

            // Update existing licenses if code changed
            if ($oldCode !== $licenseType->code) {
                PlayerLicense::where('license_type', $oldCode)
                    ->update(['license_type' => $licenseType->code]);
            }

            DB::commit();

            Log::info('License type updated', [
                'license_type_id' => $licenseType->id,
                'name' => $licenseType->name,
                'code' => $licenseType->code,
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('back-office.license-types.index')
                ->with('success', 'License type updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update license type', [
                'license_type_id' => $licenseType->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update license type: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Toggle the active status of a license type
     */
    public function toggleStatus(LicenseType $licenseType)
    {
        $licenseType->update([
            'is_active' => !$licenseType->is_active,
            'updated_by' => auth()->id()
        ]);

        $status = $licenseType->is_active ? 'activated' : 'deactivated';
        
        Log::info("License type {$status}", [
            'license_type_id' => $licenseType->id,
            'name' => $licenseType->name,
            'updated_by' => auth()->id()
        ]);

        return redirect()->back()
            ->with('success', "License type {$status} successfully.");
    }

    /**
     * Remove the specified license type
     */
    public function destroy(LicenseType $licenseType)
    {
        // Check if license type is in use
        $activeLicenses = PlayerLicense::where('license_type', $licenseType->code)
            ->whereIn('status', ['active', 'pending'])
            ->count();

        if ($activeLicenses > 0) {
            return redirect()->back()
                ->with('error', "Cannot delete license type. There are {$activeLicenses} active or pending licenses using this type.");
        }

        DB::beginTransaction();
        try {
            // Update existing licenses to remove the type reference
            PlayerLicense::where('license_type', $licenseType->code)
                ->update(['license_type' => null]);

            $licenseType->delete();

            DB::commit();

            Log::info('License type deleted', [
                'license_type_id' => $licenseType->id,
                'name' => $licenseType->name,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('back-office.license-types.index')
                ->with('success', 'License type deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete license type', [
                'license_type_id' => $licenseType->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete license type: ' . $e->getMessage());
        }
    }
} 