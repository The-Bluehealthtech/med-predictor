<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SeasonManagementController extends Controller
{
    public function index()
    {
        $seasons = Season::with(['createdBy', 'updatedBy'])
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return view('back-office.seasons.index', compact('seasons'));
    }

    public function create()
    {
        return view('back-office.seasons.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_start_date' => 'required|date|before_or_equal:start_date',
            'registration_end_date' => 'required|date|after:registration_start_date|before_or_equal:end_date',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $seasonData = $request->only([
                'name', 'short_name', 'start_date', 'end_date',
                'registration_start_date', 'registration_end_date',
                'description', 'settings'
            ]);

            $seasonData['status'] = 'upcoming';
            $seasonData['is_current'] = false;
            $seasonData['created_by'] = auth()->id();

            $season = Season::create($seasonData);

            DB::commit();

            return redirect()->route('back-office.seasons.index')
                ->with('success', 'Season created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create season: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Season $season)
    {
        $season->load(['competitions', 'players', 'createdBy', 'updatedBy']);
        
        return view('back-office.seasons.show', compact('season'));
    }

    public function edit(Season $season)
    {
        return view('back-office.seasons.edit', compact('season'));
    }

    public function update(Request $request, Season $season)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_start_date' => 'required|date|before_or_equal:start_date',
            'registration_end_date' => 'required|date|after:registration_start_date|before_or_equal:end_date',
            'status' => 'required|in:upcoming,active,completed,archived',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $seasonData = $request->only([
                'name', 'short_name', 'start_date', 'end_date',
                'registration_start_date', 'registration_end_date',
                'status', 'description', 'settings'
            ]);

            $seasonData['updated_by'] = auth()->id();

            $season->update($seasonData);

            DB::commit();

            return redirect()->route('back-office.seasons.index')
                ->with('success', 'Season updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update season: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Season $season)
    {
        // Check if season has competitions or players
        if ($season->competitions()->count() > 0 || $season->players()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete season with existing competitions or players.');
        }

        try {
            $season->delete();
            return redirect()->route('back-office.seasons.index')
                ->with('success', 'Season deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete season: ' . $e->getMessage());
        }
    }

    public function setCurrent(Season $season)
    {
        DB::beginTransaction();
        try {
            // Deactivate current season
            Season::where('is_current', true)->update(['is_current' => false]);
            
            // Set new current season
            $season->update(['is_current' => true]);

            DB::commit();

            return redirect()->route('back-office.seasons.index')
                ->with('success', 'Current season updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update current season: ' . $e->getMessage());
        }
    }

    public function activate(Season $season)
    {
        try {
            $season->update(['status' => 'active']);
            
            return redirect()->route('back-office.seasons.index')
                ->with('success', 'Season activated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to activate season: ' . $e->getMessage());
        }
    }

    public function complete(Season $season)
    {
        try {
            $season->update(['status' => 'completed']);
            
            return redirect()->route('back-office.seasons.index')
                ->with('success', 'Season marked as completed!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to complete season: ' . $e->getMessage());
        }
    }

    public function archive(Season $season)
    {
        try {
            $season->update(['status' => 'archived']);
            
            return redirect()->route('back-office.seasons.index')
                ->with('success', 'Season archived successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to archive season: ' . $e->getMessage());
        }
    }
} 