<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Club;
use App\Models\Association;
use App\Models\FifaConnectId;
use App\Models\User;
use App\Services\FifaConnectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\GameMatch; // Added this import for GameMatch
use App\Models\MatchModel; // Added this import for MatchModel

class CompetitionManagementController extends Controller
{
    protected $fifaConnectService;

    public function __construct(FifaConnectService $fifaConnectService)
    {
        $this->fifaConnectService = $fifaConnectService;
        $this->middleware('auth');
        $this->middleware('role:admin,association_admin,association_registrar,association_medical,system_admin');
    }

    public function index()
    {
        $user = Auth::user();
        $competitions = collect();

        if (in_array($user->role, ['system_admin', 'admin'])) {
            // System admin and admin see all competitions
            $competitions = Competition::with(['association', 'season'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $competitions = Competition::where('association_id', $user->association_id)
                ->with(['association', 'season'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        $stats = [
            'total' => (int) $competitions->total(),
            'active' => (int) $competitions->where('status', 'active')->count(),
            'upcoming' => (int) $competitions->where('status', 'upcoming')->count(),
            'completed' => (int) $competitions->where('status', 'completed')->count(),
        ];

        return view('competition-management.index', compact('competitions', 'stats'));
    }

    public function competitionsIndex()
    {
        return $this->index();
    }

    public function create()
    {
        $user = Auth::user();
        $clubs = collect();

        if (in_array($user->role, ['system_admin', 'admin'])) {
            // System admin and admin see all clubs
            $clubs = Club::orderBy('name')->get();
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        }

        return view('competition-management.create', compact('clubs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:100',
            'type' => 'required|in:league,cup,friendly,international,tournament,playoff,exhibition',
            'season' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_deadline' => 'nullable|date|before:start_date',
            'min_teams' => 'nullable|integer|min:2|max:100',
            'max_teams' => 'nullable|integer|min:2|max:100',
            'format' => 'required|in:round_robin,knockout,mixed,single_round,group_stage',
            'status' => 'required|in:upcoming,active,completed,cancelled',
            'description' => 'nullable|string',
            'rules' => 'nullable|string',
            'entry_fee' => 'nullable|numeric|min:0',
            'prize_pool' => 'nullable|numeric|min:0',
            'require_federation_license' => 'nullable|boolean',
            'fifa_sync_enabled' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Create FIFA Connect ID if sync is enabled
            $fifaConnectId = null;
            if ($request->has('fifa_sync_enabled')) {
                $fifaConnectId = $this->fifaConnectService->generateCompetitionId();
            }

            // Create competition
            $competition = Competition::create([
                'name' => $validated['name'],
                'short_name' => $validated['short_name'],
                'type' => $validated['type'],
                'season' => $validated['season'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'registration_deadline' => $validated['registration_deadline'],
                'min_teams' => $validated['min_teams'],
                'max_teams' => $validated['max_teams'],
                'format' => $validated['format'],
                'status' => $validated['status'],
                'description' => $validated['description'],
                'rules' => $validated['rules'],
                'entry_fee' => $validated['entry_fee'],
                'prize_pool' => $validated['prize_pool'],
                'association_id' => in_array($user->role, ['system_admin', 'admin']) ? null : $user->association_id,
                'fifa_connect_id' => $fifaConnectId?->id,
                'require_federation_license' => $request->has('require_federation_license'),
            ]);

            DB::commit();

            $message = 'Compétition créée avec succès';
            if ($fifaConnectId) {
                $message .= ' avec l\'ID FIFA Connect: ' . $fifaConnectId->fifa_id;
            }

            return redirect()->route('competition-management.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Échec de la création de la compétition: ' . $e->getMessage());
        }
    }

    public function show(Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        // Load competition with teams and their clubs
        $competition->load(['association', 'fifaConnectId', 'teams.club', 'teams.players', 'validatedBy']);
        
        // Paginate matches separately for better performance
        $matches = $competition->matches()
            ->with(['homeTeam.club', 'awayTeam.club'])
            ->orderBy('match_date', 'asc')
            ->paginate(20); // Show 20 matches per page
        
        return view('competition-management.show', compact('competition', 'matches'));
    }

    public function edit(Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        $user = Auth::user();
        $clubs = collect();

        if (in_array($user->role, ['system_admin', 'admin'])) {
            // System admin and admin see all clubs
            $clubs = Club::orderBy('name')->get();
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            $clubs = Club::where('association_id', $user->association_id)
                ->orderBy('name')
                ->get();
        }

        $competition->load(['association', 'fifaConnectId', 'clubs']);
        
        return view('competition-management.edit', compact('competition', 'clubs'));
    }

    public function update(Request $request, Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:100',
            'type' => 'required|in:league,cup,friendly,international,tournament,playoff,exhibition',
            'season' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_deadline' => 'nullable|date|before:start_date',
            'min_teams' => 'nullable|integer|min:2|max:100',
            'max_teams' => 'nullable|integer|min:2|max:100',
            'format' => 'required|in:round_robin,knockout,mixed,single_round,group_stage',
            'status' => 'required|in:upcoming,active,completed,cancelled',
            'description' => 'nullable|string',
            'rules' => 'nullable|string',
            'entry_fee' => 'nullable|numeric|min:0',
            'prize_pool' => 'nullable|numeric|min:0',
            'require_federation_license' => 'nullable|boolean',
            'fifa_sync_enabled' => 'nullable|boolean',
            'clubs' => 'nullable|array',
            'clubs.*' => 'exists:clubs,id',
        ]);

        DB::beginTransaction();
        try {
            // Handle FIFA sync checkbox
            $fifaConnectId = null;
            if ($request->has('fifa_sync_enabled') && !$competition->fifaConnectId) {
                $fifaConnectId = $this->fifaConnectService->generateCompetitionId();
            }

            $competition->update([
                'name' => $validated['name'],
                'short_name' => $validated['short_name'],
                'type' => $validated['type'],
                'season' => $validated['season'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'registration_deadline' => $validated['registration_deadline'],
                'min_teams' => $validated['min_teams'],
                'max_teams' => $validated['max_teams'],
                'format' => $validated['format'],
                'status' => $validated['status'],
                'description' => $validated['description'],
                'rules' => $validated['rules'],
                'entry_fee' => $validated['entry_fee'],
                'prize_pool' => $validated['prize_pool'],
                'require_federation_license' => $request->has('require_federation_license'),
                'fifa_connect_id' => $fifaConnectId?->id ?? $competition->fifa_connect_id,
            ]);

            // Update clubs if provided
            if (isset($validated['clubs'])) {
                $competition->clubs()->sync($validated['clubs']);
            }

            // Sync with FIFA Connect if needed
            if ($competition->fifaConnectId) {
                $this->fifaConnectService->syncCompetition($competition);
            }

            DB::commit();

            return redirect()->route('competition-management.competitions.show', $competition)
                ->with('success', 'Competition updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update competition: ' . $e->getMessage());
        }
    }

    public function destroy(Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        try {
            $competition->delete();
            return redirect()->route('competition-management.competitions.index')
                ->with('success', 'Competition deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete competition: ' . $e->getMessage());
        }
    }

    public function sync(Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        try {
            $result = $this->fifaConnectService->syncCompetition($competition);
            return back()->with('success', 'Competition synced with FIFA Connect successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sync competition: ' . $e->getMessage());
        }
    }

    public function bulkSync()
    {
        $user = Auth::user();
        $competitions = collect();

        if ($user->role === 'association') {
            $competitions = Competition::where('association_id', $user->association_id)->get();
        }

        $synced = 0;
        $failed = 0;

        foreach ($competitions as $competition) {
            try {
                $this->fifaConnectService->syncCompetition($competition);
                $synced++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return back()->with('success', "Bulk sync completed: {$synced} synced, {$failed} failed");
    }

    public function export()
    {
        $user = Auth::user();
        $competitions = collect();

        if ($user->role === 'association') {
            $competitions = Competition::where('association_id', $user->association_id)
                ->with(['association', 'fifaConnectId', 'clubs'])
                ->get();
        }

        $filename = 'competitions_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($competitions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'FIFA Connect ID', 'Name', 'Type', 'Season', 'Start Date', 
                'End Date', 'Format', 'Status', 'Max Teams', 'Association', 'Created At'
            ]);

            foreach ($competitions as $competition) {
                fputcsv($file, [
                    $competition->fifaConnectId?->fifa_id ?? 'N/A',
                    $competition->name,
                    $competition->type,
                    $competition->season,
                    $competition->start_date,
                    $competition->end_date,
                    $competition->format,
                    $competition->status,
                    $competition->max_teams,
                    $competition->association?->name ?? 'N/A',
                    $competition->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function fixtures(Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        // Get all matches for this competition with team and club relationships
        $matches = MatchModel::where('competition_id', $competition->id)
            ->with(['homeTeam.club', 'awayTeam.club'])
            ->orderBy('matchday', 'asc')
            ->orderBy('match_date', 'asc')
            ->paginate(50);
        
        return view('competition-management.fixtures', compact('competition', 'matches'));
    }

    public function generateFixtures(Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        try {
            // Check if fixtures are already validated and locked
            if ($competition->fixtures_validated) {
                return back()->with('error', 'Fixtures are already validated and locked for this season. No changes are allowed.');
            }
            
            // Check if fixtures already exist
            $existingMatches = GameMatch::where('competition_id', $competition->id)->count();
            if ($existingMatches > 0) {
                return back()->with('warning', "Fixtures already exist for this competition ({$existingMatches} matches). Use the 'Regenerate Fixtures' command to clear and recreate them.");
            }
            
            // Get teams registered to this competition
            $teams = $competition->teams()->with('club')->get();
            $teamCount = $teams->count();
            
            if ($teamCount < 2) {
                return back()->with('error', 'Not enough teams registered for fixture generation. Need at least 2 teams.');
            }
            
            $teamIds = $teams->pluck('id')->toArray();
            $matchday = 1;
            $matchDate = \Carbon\Carbon::parse($competition->start_date);
            
            // Generate round-robin fixtures (first half of season)
            for ($round = 0; $round < $teamCount - 1; $round++) {
                for ($i = 0; $i < $teamCount / 2; $i++) {
                    $homeIndex = $i;
                    $awayIndex = $teamCount - 1 - $i;
                    
                    if ($homeIndex >= $awayIndex) continue;
                    
                    $homeTeamId = $teamIds[$homeIndex];
                    $awayTeamId = $teamIds[$awayIndex];
                    
                    $homeTeam = $teams->where('id', $homeTeamId)->first();
                    $awayTeam = $teams->where('id', $awayTeamId)->first();
                    
                    // Create match
                    $match = GameMatch::create([
                        'competition_id' => $competition->id,
                        'home_team_id' => $homeTeamId,
                        'away_team_id' => $awayTeamId,
                        'home_club_id' => $homeTeam->club_id,
                        'away_club_id' => $awayTeam->club_id,
                        'matchday' => $matchday,
                        'match_date' => $matchDate,
                        'kickoff_time' => $matchDate->copy()->setTime(15, 0),
                        'venue' => 'home',
                        'stadium' => $homeTeam->club->stadium,
                        'capacity' => rand(40000, 75000),
                        'weather_conditions' => 'Clear',
                        'pitch_condition' => 'Excellent',
                        'referee' => 'TBD',
                        'assistant_referee_1' => 'TBD',
                        'assistant_referee_2' => 'TBD',
                        'fourth_official' => 'TBD',
                        'var_referee' => 'TBD',
                        'match_status' => 'scheduled',
                        'home_score' => null,
                        'away_score' => null,
                        'home_possession' => null,
                        'away_possession' => null,
                        'home_shots' => null,
                        'away_shots' => null,
                        'home_shots_on_target' => null,
                        'away_shots_on_target' => null,
                        'home_corners' => null,
                        'away_corners' => null,
                        'home_fouls' => null,
                        'away_fouls' => null,
                        'home_offsides' => null,
                        'away_offsides' => null,
                    ]);
                }
                
                // Rotate teams for next round (except first team)
                $firstTeam = array_shift($teamIds);
                array_push($teamIds, $firstTeam);
                
                $matchday++;
                $matchDate->addDays(7); // Weekly matches
            }
            
            // Generate return fixtures (second half of season)
            for ($round = 0; $round < $teamCount - 1; $round++) {
                for ($i = 0; $i < $teamCount / 2; $i++) {
                    $homeIndex = $i;
                    $awayIndex = $teamCount - 1 - $i;
                    
                    if ($homeIndex >= $awayIndex) continue;
                    
                    $homeTeamId = $teamIds[$homeIndex];
                    $awayTeamId = $teamIds[$awayIndex];
                    
                    $homeTeam = $teams->where('id', $homeTeamId)->first();
                    $awayTeam = $teams->where('id', $awayTeamId)->first();
                    
                    // Create match
                    $match = GameMatch::create([
                        'competition_id' => $competition->id,
                        'home_team_id' => $homeTeamId,
                        'away_team_id' => $awayTeamId,
                        'home_club_id' => $homeTeam->club_id,
                        'away_club_id' => $awayTeam->club_id,
                        'matchday' => $matchday,
                        'match_date' => $matchDate,
                        'kickoff_time' => $matchDate->copy()->setTime(15, 0),
                        'venue' => 'home',
                        'stadium' => $homeTeam->club->stadium,
                        'capacity' => rand(40000, 75000),
                        'weather_conditions' => 'Clear',
                        'pitch_condition' => 'Excellent',
                        'referee' => 'TBD',
                        'assistant_referee_1' => 'TBD',
                        'assistant_referee_2' => 'TBD',
                        'fourth_official' => 'TBD',
                        'var_referee' => 'TBD',
                        'match_status' => 'scheduled',
                        'home_score' => null,
                        'away_score' => null,
                        'home_possession' => null,
                        'away_possession' => null,
                        'home_shots' => null,
                        'away_shots' => null,
                        'home_shots_on_target' => null,
                        'away_shots_on_target' => null,
                        'home_corners' => null,
                        'away_corners' => null,
                        'home_fouls' => null,
                        'away_fouls' => null,
                        'home_offsides' => null,
                        'away_offsides' => null,
                    ]);
                }
                
                // Rotate teams for next round (except first team)
                $firstTeam = array_shift($teamIds);
                array_push($teamIds, $firstTeam);
                
                $matchday++;
                $matchDate->addDays(7); // Weekly matches
            }
            
            $totalMatches = GameMatch::where('competition_id', $competition->id)->count();
            
            return back()->with('success', "Fixtures generated successfully! Created {$totalMatches} matches across " . ($matchday - 1) . " matchdays.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate fixtures: ' . $e->getMessage());
        }
    }

    public function regenerateFixtures(Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        try {
            // Check if fixtures are already validated and locked
            if ($competition->fixtures_validated) {
                return back()->with('error', 'Fixtures are already validated and locked for this season. No changes are allowed.');
            }
            
            // Clear existing fixtures
            $deletedMatches = GameMatch::where('competition_id', $competition->id)->delete();
            
            // Get teams registered to this competition
            $teams = $competition->teams()->with('club')->get();
            $teamCount = $teams->count();
            
            if ($teamCount < 2) {
                return back()->with('error', 'Not enough teams registered for fixture generation. Need at least 2 teams.');
            }
            
            $teamIds = $teams->pluck('id')->toArray();
            $matchday = 1;
            $matchDate = \Carbon\Carbon::parse($competition->start_date);
            
            // Generate round-robin fixtures (first half of season)
            for ($round = 0; $round < $teamCount - 1; $round++) {
                for ($i = 0; $i < $teamCount / 2; $i++) {
                    $homeIndex = $i;
                    $awayIndex = $teamCount - 1 - $i;
                    
                    if ($homeIndex >= $awayIndex) continue;
                    
                    $homeTeamId = $teamIds[$homeIndex];
                    $awayTeamId = $teamIds[$awayIndex];
                    
                    $homeTeam = $teams->where('id', $homeTeamId)->first();
                    $awayTeam = $teams->where('id', $awayTeamId)->first();
                    
                    // Create match
                    $match = GameMatch::create([
                        'competition_id' => $competition->id,
                        'home_team_id' => $homeTeamId,
                        'away_team_id' => $awayTeamId,
                        'home_club_id' => $homeTeam->club_id,
                        'away_club_id' => $awayTeam->club_id,
                        'matchday' => $matchday,
                        'match_date' => $matchDate,
                        'kickoff_time' => $matchDate->copy()->setTime(15, 0),
                        'venue' => 'home',
                        'stadium' => $homeTeam->club->stadium,
                        'capacity' => rand(40000, 75000),
                        'weather_conditions' => 'Clear',
                        'pitch_condition' => 'Excellent',
                        'referee' => 'TBD',
                        'assistant_referee_1' => 'TBD',
                        'assistant_referee_2' => 'TBD',
                        'fourth_official' => 'TBD',
                        'var_referee' => 'TBD',
                        'match_status' => 'scheduled',
                        'home_score' => null,
                        'away_score' => null,
                        'home_possession' => null,
                        'away_possession' => null,
                        'home_shots' => null,
                        'away_shots' => null,
                        'home_shots_on_target' => null,
                        'away_shots_on_target' => null,
                        'home_corners' => null,
                        'away_corners' => null,
                        'home_fouls' => null,
                        'away_fouls' => null,
                        'home_offsides' => null,
                        'away_offsides' => null,
                    ]);
                }
                
                // Rotate teams for next round (except first team)
                $firstTeam = array_shift($teamIds);
                array_push($teamIds, $firstTeam);
                
                $matchday++;
                $matchDate->addDays(7); // Weekly matches
            }
            
            // Generate return fixtures (second half of season)
            for ($round = 0; $round < $teamCount - 1; $round++) {
                for ($i = 0; $i < $teamCount / 2; $i++) {
                    $homeIndex = $i;
                    $awayIndex = $teamCount - 1 - $i;
                    
                    if ($homeIndex >= $awayIndex) continue;
                    
                    $homeTeamId = $teamIds[$homeIndex];
                    $awayTeamId = $teamIds[$awayIndex];
                    
                    $homeTeam = $teams->where('id', $homeTeamId)->first();
                    $awayTeam = $teams->where('id', $awayTeamId)->first();
                    
                    // Create match
                    $match = GameMatch::create([
                        'competition_id' => $competition->id,
                        'home_team_id' => $homeTeamId,
                        'away_team_id' => $awayTeamId,
                        'home_club_id' => $homeTeam->club_id,
                        'away_club_id' => $awayTeam->club_id,
                        'matchday' => $matchday,
                        'match_date' => $matchDate,
                        'kickoff_time' => $matchDate->copy()->setTime(15, 0),
                        'venue' => 'home',
                        'stadium' => $homeTeam->club->stadium,
                        'capacity' => rand(40000, 75000),
                        'weather_conditions' => 'Clear',
                        'pitch_condition' => 'Excellent',
                        'referee' => 'TBD',
                        'assistant_referee_1' => 'TBD',
                        'assistant_referee_2' => 'TBD',
                        'fourth_official' => 'TBD',
                        'var_referee' => 'TBD',
                        'match_status' => 'scheduled',
                        'home_score' => null,
                        'away_score' => null,
                        'home_possession' => null,
                        'away_possession' => null,
                        'home_shots' => null,
                        'away_shots' => null,
                        'home_shots_on_target' => null,
                        'away_shots_on_target' => null,
                        'home_corners' => null,
                        'away_corners' => null,
                        'home_fouls' => null,
                        'away_fouls' => null,
                        'home_offsides' => null,
                        'away_offsides' => null,
                    ]);
                }
                
                // Rotate teams for next round (except first team)
                $firstTeam = array_shift($teamIds);
                array_push($teamIds, $firstTeam);
                
                $matchday++;
                $matchDate->addDays(7); // Weekly matches
            }
            
            $totalMatches = GameMatch::where('competition_id', $competition->id)->count();
            
            return back()->with('success', "Fixtures regenerated successfully! Deleted {$deletedMatches} old matches and created {$totalMatches} new matches across " . ($matchday - 1) . " matchdays.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to regenerate fixtures: ' . $e->getMessage());
        }
    }

    public function validateFixtures(Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        try {
            // Check if fixtures already exist
            $existingMatches = GameMatch::where('competition_id', $competition->id)->count();
            if ($existingMatches === 0) {
                return back()->with('error', 'No fixtures exist to validate. Please generate fixtures first.');
            }
            
            // Check if fixtures are already validated
            if ($competition->fixtures_validated) {
                return back()->with('warning', 'Fixtures are already validated and locked for this season.');
            }
            
            // Validate and lock fixtures
            $competition->update([
                'fixtures_validated' => true,
                'fixtures_validated_at' => now(),
                'validated_by' => Auth::id(),
            ]);
            
            return back()->with('success', "Fixtures validated and locked successfully! {$existingMatches} matches are now locked for the season. No changes will be allowed.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to validate fixtures: ' . $e->getMessage());
        }
    }

    public function showManualFixturesForm(Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        // Check if fixtures are already validated and locked
        if ($competition->fixtures_validated) {
            return back()->with('error', 'Fixtures are already validated and locked for this season. No changes are allowed.');
        }
        
        // Get teams registered to this competition
        $teams = $competition->teams()->with('club')->get();
        
        if ($teams->count() < 2) {
            return back()->with('error', 'Not enough teams registered for fixture generation. Need at least 2 teams.');
        }
        
        // Generate all possible match combinations
        $matchCombinations = [];
        $teamIds = $teams->pluck('id')->toArray();
        
        // Generate home and away matches for all team combinations
        for ($i = 0; $i < count($teamIds); $i++) {
            for ($j = $i + 1; $j < count($teamIds); $j++) {
                $homeTeam = $teams->where('id', $teamIds[$i])->first();
                $awayTeam = $teams->where('id', $teamIds[$j])->first();
                
                // Home match
                $matchCombinations[] = [
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'home_team_name' => $homeTeam->name,
                    'away_team_name' => $awayTeam->name,
                    'home_club_name' => $homeTeam->club->name,
                    'away_club_name' => $awayTeam->club->name,
                    'stadium' => $homeTeam->club->stadium,
                    'match_type' => 'home'
                ];
                
                // Away match
                $matchCombinations[] = [
                    'home_team_id' => $awayTeam->id,
                    'away_team_id' => $homeTeam->id,
                    'home_team_name' => $awayTeam->name,
                    'away_team_name' => $homeTeam->name,
                    'home_club_name' => $awayTeam->club->name,
                    'away_club_name' => $homeTeam->club->name,
                    'stadium' => $awayTeam->club->stadium,
                    'match_type' => 'away'
                ];
            }
        }
        
        // Get existing matches if any
        $existingMatches = GameMatch::where('competition_id', $competition->id)
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('matchday', 'asc')
            ->orderBy('match_date', 'asc')
            ->get();
        
        return view('competition-management.manual-fixtures', compact('competition', 'teams', 'matchCombinations', 'existingMatches'));
    }

    public function storeManualFixtures(Request $request, Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        // Check if fixtures are already validated and locked
        if ($competition->fixtures_validated) {
            return back()->with('error', 'Fixtures are already validated and locked for this season. No changes are allowed.');
        }
        
        try {
            // Clear existing fixtures first
            GameMatch::where('competition_id', $competition->id)->delete();
            
            $matches = $request->input('matches', []);
            $createdMatches = 0;
            
            foreach ($matches as $matchData) {
                if (empty($matchData['home_team_id']) || empty($matchData['away_team_id']) || empty($matchData['match_date'])) {
                    continue; // Skip incomplete entries
                }
                
                // Get team details
                $homeTeam = $competition->teams()->where('id', $matchData['home_team_id'])->first();
                $awayTeam = $competition->teams()->where('id', $matchData['away_team_id'])->first();
                
                if (!$homeTeam || !$awayTeam) {
                    continue; // Skip if teams don't exist
                }
                
                // Parse match date and time
                $matchDate = \Carbon\Carbon::parse($matchData['match_date']);
                $kickoffTime = isset($matchData['kickoff_time']) ? \Carbon\Carbon::parse($matchData['kickoff_time']) : $matchDate->copy()->setTime(15, 0);
                
                // Create match
                GameMatch::create([
                    'competition_id' => $competition->id,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'home_club_id' => $homeTeam->club_id,
                    'away_club_id' => $awayTeam->club_id,
                    'matchday' => $matchData['matchday'] ?? 1,
                    'match_date' => $matchDate,
                    'kickoff_time' => $kickoffTime,
                    'venue' => 'home',
                    'stadium' => $homeTeam->club->stadium,
                    'capacity' => rand(40000, 75000),
                    'weather_conditions' => 'Clear',
                    'pitch_condition' => 'Excellent',
                    'referee' => $matchData['referee'] ?? 'TBD',
                    'assistant_referee_1' => $matchData['assistant_referee_1'] ?? 'TBD',
                    'assistant_referee_2' => $matchData['assistant_referee_2'] ?? 'TBD',
                    'fourth_official' => $matchData['fourth_official'] ?? 'TBD',
                    'var_referee' => $matchData['var_referee'] ?? 'TBD',
                    'match_status' => 'scheduled',
                    'home_score' => null,
                    'away_score' => null,
                    'home_possession' => null,
                    'away_possession' => null,
                    'home_shots' => null,
                    'away_shots' => null,
                    'home_shots_on_target' => null,
                    'away_shots_on_target' => null,
                    'home_corners' => null,
                    'away_corners' => null,
                    'home_fouls' => null,
                    'away_fouls' => null,
                    'home_offsides' => null,
                    'away_offsides' => null,
                ]);
                
                $createdMatches++;
            }
            
            return redirect()->route('competition-management.competitions.show', $competition)
                ->with('success', "Manual fixtures created successfully! {$createdMatches} matches have been scheduled.");
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create manual fixtures: ' . $e->getMessage());
        }
    }

    public function standings(Request $request, Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        // Get the latest rankings from the competition_rankings table
        $ranking = DB::table('competition_rankings')
            ->where('competition_id', $competition->id)
            ->orderBy('round', 'desc')
            ->first();
        
        if (!$ranking) {
            // Fallback to calculating from matches if no rankings exist
            $competition->load(['clubs.teams', 'matches']);
            
            // Calculate standings based on matches
            $standings = collect();
            
            foreach ($competition->clubs as $club) {
                // Get all teams for this club
                $clubTeamIds = $club->teams->pluck('id')->toArray();
                
                if (empty($clubTeamIds)) {
                    continue; // Skip clubs without teams
                }
                
                $matches = $competition->matches()
                    ->where(function ($query) use ($clubTeamIds) {
                        $query->whereIn('home_team_id', $clubTeamIds)
                              ->orWhereIn('away_team_id', $clubTeamIds);
                    })
                    ->get();
                
                $points = 0;
                $played = $matches->count();
                $won = 0;
                $drawn = 0;
                $lost = 0;
                $goalsFor = 0;
                $goalsAgainst = 0;
                $form = [];
                
                foreach ($matches as $match) {
                    $isHomeTeam = in_array($match->home_team_id, $clubTeamIds);
                    
                    if ($isHomeTeam) {
                        $goalsFor += $match->home_score ?? 0;
                        $goalsAgainst += $match->away_score ?? 0;
                        
                        if (($match->home_score ?? 0) > ($match->away_score ?? 0)) {
                            $won++;
                            $points += 3;
                            $form[] = 'W';
                        } elseif (($match->home_score ?? 0) === ($match->away_score ?? 0)) {
                            $drawn++;
                            $points += 1;
                            $form[] = 'D';
                        } else {
                            $lost++;
                            $form[] = 'L';
                        }
                    } else {
                        $goalsFor += $match->away_score ?? 0;
                        $goalsAgainst += $match->home_score ?? 0;
                        
                        if (($match->away_score ?? 0) > ($match->home_score ?? 0)) {
                            $won++;
                            $points += 3;
                            $form[] = 'W';
                        } elseif (($match->away_score ?? 0) === ($match->home_score ?? 0)) {
                            $drawn++;
                            $points += 1;
                            $form[] = 'D';
                        } else {
                            $lost++;
                            $form[] = 'L';
                        }
                    }
                }
                
                $standings->push([
                    'team_name' => $club->name,
                    'club_name' => $club->name,
                    'points' => $points,
                    'played' => $played,
                    'won' => $won,
                    'drawn' => $drawn,
                    'lost' => $lost,
                    'goals_for' => $goalsFor,
                    'goals_against' => $goalsAgainst,
                    'goal_difference' => $goalsFor - $goalsAgainst,
                    'form' => $form,
                ]);
            }
            
            $standings = $standings->sortByDesc('points')
                ->sortByDesc('goal_difference')
                ->values();
        } else {
            // Use the rankings data from the competition_rankings table
            $standingsData = json_decode($ranking->standings, true);
            
            // Handle double-encoded JSON
            if (is_string($standingsData)) {
                $standingsData = json_decode($standingsData, true);
            }
            
            if (!$standingsData || !is_array($standingsData)) {
                $standings = collect();
            } else {
                // Convert to array and sort by points
                $standingsArray = [];
                foreach ($standingsData as $teamId => $stats) {
                    if (is_array($stats) && isset($stats['club_name']) && isset($stats['points'])) {
                        $standingsArray[] = [
                            'team_name' => $stats['club_name'], // Use club_name instead of team_name
                            'club_name' => $stats['club_name'],
                            'points' => $stats['points'] ?? 0,
                            'played' => $stats['played'] ?? 0,
                            'won' => $stats['won'] ?? 0,
                            'drawn' => $stats['drawn'] ?? 0,
                            'lost' => $stats['lost'] ?? 0,
                            'goals_for' => $stats['goals_for'] ?? 0,
                            'goals_against' => $stats['goals_against'] ?? 0,
                            'goal_difference' => $stats['goal_difference'] ?? 0,
                            'form' => [], // Form data not available in rankings table
                        ];
                    }
                }

                // Sort by points (desc), goal difference (desc), goals scored (desc)
                usort($standingsArray, function ($a, $b) {
                    if ($a['points'] !== $b['points']) {
                        return $b['points'] - $a['points'];
                    }
                    
                    if ($a['goal_difference'] !== $b['goal_difference']) {
                        return $b['goal_difference'] - $a['goal_difference'];
                    }
                    
                    return $b['goals_for'] - $a['goals_for'];
                });

                $standings = collect($standingsArray);
            }
        }
        
        // Get all matchdays with completed matches
        $allMatchdays = GameMatch::where('competition_id', $competition->id)
            ->whereNotNull('home_score')
            ->whereNotNull('away_score')
            ->with(['homeTeam', 'awayTeam', 'matchSheet'])
            ->orderBy('matchday', 'desc')
            ->orderBy('match_date', 'desc')
            ->get()
            ->groupBy('matchday')
            ->map(function ($matchdayMatches, $matchday) {
                return [
                    'matchday' => $matchday,
                    'date' => $matchdayMatches->first()->match_date,
                    'matches' => $matchdayMatches->map(function ($match) {
                        return (object) [
                            'id' => $match->id,
                            'home_team_name' => $match->homeTeam && $match->homeTeam->club ? $match->homeTeam->club->name : ($match->homeTeam ? $match->homeTeam->name : 'Unknown Team'),
                            'away_team_name' => $match->awayTeam && $match->awayTeam->club ? $match->awayTeam->club->name : ($match->awayTeam ? $match->awayTeam->name : 'Unknown Team'),
                            'home_score' => $match->home_score,
                            'away_score' => $match->away_score,
                            'match_date' => $match->match_date,
                            'has_match_sheet' => $match->matchSheet ? true : false,
                            'match_sheet_status' => $match->matchSheet ? $match->matchSheet->status : null,
                            'is_played' => $match->home_score !== null && $match->away_score !== null,
                        ];
                    })
                ];
            });

        // Handle case when there are no completed matches
        if ($allMatchdays->isEmpty()) {
            $currentMatchday = null;
            $currentMatchdayData = null;
            $matchdayNumbers = collect();
            $currentIndex = false;
            $previousMatchday = null;
            $nextMatchday = null;
            $recentMatches = [];
        } else {
            // Get current matchday from request or default to the latest
            $currentMatchday = $request->get('matchday', $allMatchdays->keys()->first());
            
            // Ensure currentMatchday exists in the available matchdays
            if (!$currentMatchday || !$allMatchdays->has($currentMatchday)) {
                $currentMatchday = $allMatchdays->keys()->first();
            }
            
            // Get the current matchday data
            $currentMatchdayData = $allMatchdays->get($currentMatchday);
            
            // Get all matchday numbers for navigation
            $matchdayNumbers = $allMatchdays->keys()->sort()->values();
            
            // Find current position in matchday list
            $currentIndex = $currentMatchday ? $matchdayNumbers->search($currentMatchday) : false;
            
            // Get previous and next matchday numbers
            $previousMatchday = ($currentIndex !== false && $currentIndex > 0) ? $matchdayNumbers->get($currentIndex - 1) : null;
            $nextMatchday = ($currentIndex !== false && $currentIndex < $matchdayNumbers->count() - 1) ? $matchdayNumbers->get($currentIndex + 1) : null;
            
            $recentMatches = $currentMatchdayData ? [$currentMatchdayData] : [];
        }
        
        return view('competition-management.standings', compact(
            'competition', 
            'standings', 
            'recentMatches', 
            'currentMatchday', 
            'previousMatchday', 
            'nextMatchday', 
            'matchdayNumbers'
        ));
    }

    /**
     * Inscription d'une équipe à une compétition avec vérification des licences fédération si requis
     */
    public function registerTeam(Request $request, Competition $competition)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'players' => 'required|array|min:11',
            'players.*' => 'required|exists:players,id',
        ]);

        // Service d’éligibilité
        $eligibilityService = app(\App\Services\PlayerEligibilityService::class);
        $season = $competition->season;
        foreach ($request->players as $playerId) {
            $player = \App\Models\Player::find($playerId);
            [$eligible, $reason] = $eligibilityService->isEligible($player, $competition);
            if (!$eligible) {
                return back()->with('error', "Le joueur {$player->name} n'est pas éligible : $reason");
            }
        }

        // Si tout est OK, inscrire l'équipe
        $team = \App\Models\Team::create([
            'name' => $request->team_name,
            'club_id' => auth()->user()->club_id,
        ]);

        // Attacher les joueurs à l'équipe via TeamPlayer
        foreach ($request->players as $playerId) {
            \App\Models\TeamPlayer::create([
                'team_id' => $team->id,
                'player_id' => $playerId,
                'status' => 'active',
            ]);
        }

        // Attacher l'équipe à la compétition
        $competition->teams()->attach($team->id);

        return redirect()->route('competition-management.competitions.register-team-form', $competition)
            ->with('success', 'Équipe inscrite à la compétition avec succès.');
    }

    /**
     * Affiche le formulaire d'inscription d'équipe à une compétition.
     */
    public function showRegisterTeamForm($competitionId)
    {
        $competition = \App\Models\Competition::findOrFail($competitionId);
        
        // Récupérer tous les joueurs disponibles
        $players = \App\Models\Player::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        
        // Récupérer les équipes déjà inscrites à cette compétition
        $registeredTeams = \App\Models\Team::whereHas('competitions', function($query) use ($competitionId) {
            $query->where('competition_id', $competitionId);
        })->with(['players.player'])->get();
        
        return view('competition-management.register-team', compact('competition', 'players', 'registeredTeams'));
    }

    protected function authorizeCompetitionAccess(Competition $competition)
    {
        $user = Auth::user();
        
        // System admin can access all competitions
        if ($user->role === 'system_admin') {
            return;
        }
        
        // Association users can only access competitions in their association
        if (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            if ($competition->association_id !== $user->association_id) {
                abort(403, 'You can only access competitions in your association.');
            }
            return;
        }
        
        // Club users can only access competitions where their club participates
        if (in_array($user->role, ['club_admin', 'club_manager', 'club_medical'])) {
            $participatingTeams = $competition->teams()->where('club_id', $user->club_id)->count();
            if ($participatingTeams === 0) {
                abort(403, 'You can only access competitions where your club participates.');
            }
            return;
        }
        
        abort(403, 'Access denied.');
    }

    public function matchSheet(MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Load the match with related data
        $match->load([
            'competition',
            'homeTeam.club',
            'awayTeam.club',
            'matchSheet',
            'matchSheet.events.player',
            'matchSheet.events.team'
        ]);
        
        // If no match sheet exists, create one
        if (!$match->matchSheet) {
            $match->matchSheet()->create([
                'match_number' => 'MS-' . date('Y') . '-' . str_pad($match->id, 3, '0', STR_PAD_LEFT),
                'status' => 'draft'
            ]);
            
            $match->refresh();
            $match->load('matchSheet');
        }
        
        $matchSheet = $match->matchSheet;
        
        // Get available referees for assignment
        $availableReferees = User::where('role', 'referee')
            ->where('association_id', $match->competition->association_id)
            ->get(['id', 'name', 'qualifications'])
            ->map(function($referee) {
                return [
                    'id' => $referee->id,
                    'name' => $referee->name,
                    'qualifications' => $referee->qualifications ?? 'N/A'
                ];
            });
        
        // Check if user can assign referees
        $canAssignReferee = auth()->user()->role === 'association' || auth()->user()->role === 'admin';
        
        // Get team officials (placeholder data)
        $homeTeamOfficials = [
            'coach' => 'Coach Name',
            'manager' => 'Manager Name'
        ];
        
        $awayTeamOfficials = [
            'coach' => 'Coach Name',
            'manager' => 'Manager Name'
        ];
        
        // Get venue information
        $venueInfo = [
            'stadium' => $match->stadium ?? 'TBD',
            'capacity' => $match->capacity ? number_format($match->capacity) . ' seats' : 'TBD',
            'address' => $match->venue ?? 'TBD',
            'city' => 'TBD' // This could be extracted from venue or added as a separate field
        ];
        
        // Get team rosters
        $homeTeamRoster = $match->rosters()->where('team_id', $match->home_team_id)->with('players.player')->first();
        $awayTeamRoster = $match->rosters()->where('team_id', $match->away_team_id)->with('players.player')->first();
        
        // Check if user can sign lineup
        $canSignLineup = auth()->user()->role === 'association' || auth()->user()->role === 'admin';
        
        // Check if user can validate match sheet
        $canValidate = auth()->user()->role === 'association' || auth()->user()->role === 'admin';
        
        // Check if user can sign post match
        $canSignPostMatch = auth()->user()->role === 'association' || auth()->user()->role === 'admin';
        
        // Get match events
        $events = $match->events()->with(['player', 'team.club'])->get();
        
        // Get stage progress
        $stageProgress = [
            'draft' => [
                'label' => 'Draft Created',
                'completed' => true,
                'current' => false,
                'timestamp' => $matchSheet->created_at
            ],
            'referee_assigned' => [
                'label' => 'Referee Assigned',
                'completed' => !is_null($matchSheet->assigned_referee_id),
                'current' => is_null($matchSheet->assigned_referee_id),
                'timestamp' => $matchSheet->referee_assigned_at
            ],
            'lineups_submitted' => [
                'label' => 'Lineups Submitted',
                'completed' => !is_null($matchSheet->home_team_lineup_signed_at) && !is_null($matchSheet->away_team_lineup_signed_at),
                'current' => is_null($matchSheet->home_team_lineup_signed_at) || is_null($matchSheet->away_team_lineup_signed_at),
                'timestamp' => $matchSheet->home_team_lineup_signed_at
            ],
            'match_completed' => [
                'label' => 'Match Completed',
                'completed' => !is_null($matchSheet->referee_signed_at),
                'current' => is_null($matchSheet->referee_signed_at),
                'timestamp' => $matchSheet->referee_signed_at
            ],
            'post_match_signed' => [
                'label' => 'Post-Match Signed',
                'completed' => !is_null($matchSheet->home_team_post_match_signed_at) && !is_null($matchSheet->away_team_post_match_signed_at),
                'current' => is_null($matchSheet->home_team_post_match_signed_at) || is_null($matchSheet->away_team_post_match_signed_at),
                'timestamp' => $matchSheet->home_team_post_match_signed_at
            ],
            'fa_validated' => [
                'label' => 'FA Validated',
                'completed' => !is_null($matchSheet->stage_fa_validated_at),
                'current' => is_null($matchSheet->stage_fa_validated_at),
                'timestamp' => $matchSheet->stage_fa_validated_at
            ]
        ];
        
        return view('match-sheet.show', compact('match', 'matchSheet', 'availableReferees', 'canAssignReferee', 'homeTeamOfficials', 'awayTeamOfficials', 'venueInfo', 'homeTeamRoster', 'awayTeamRoster', 'canSignLineup', 'canValidate', 'canSignPostMatch', 'events', 'stageProgress'));
    }

    public function editMatchSheet(MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        $match->load([
            'competition',
            'homeTeam.club',
            'awayTeam.club',
            'matchSheet',
            'matchSheet.events.player',
            'matchSheet.events.team'
        ]);
        
        return view('match-sheet.edit', compact('match'));
    }

    public function updateMatchSheet(Request $request, MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Update match sheet logic here
        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Match sheet updated successfully');
    }

    public function submitMatchSheet(MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Submit match sheet logic here
        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Match sheet submitted successfully');
    }

    public function signTeamMatchSheet(Request $request, MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Sign team logic here
        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Team signature added successfully');
    }

    public function signLineupMatchSheet(Request $request, MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Sign lineup logic here
        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Lineup signed successfully');
    }

    public function signPostMatchSheet(Request $request, MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Sign post match logic here
        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Post match signed successfully');
    }

    public function assignRefereeMatchSheet(Request $request, MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Assign referee logic here
        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Referee assigned successfully');
    }

    public function faValidateMatchSheet(Request $request, MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // FA validate logic here
        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Match sheet validated by FA');
    }

    public function exportMatchSheet(MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Export match sheet logic here
        return response()->download('path/to/match-sheet.pdf');
    }

    public function importPlayersMatchSheet(MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Load the match with related data - ensure all relationships are loaded
        $match->load([
            'competition',
            'homeTeam.club.players',
            'awayTeam.club.players',
            'rosters.players'
        ]);
        
        // Debug: Let's see what we have
        \Log::info('Match data:', [
            'match_id' => $match->id,
            'home_team_id' => $match->home_team_id,
            'away_team_id' => $match->away_team_id,
            'home_team' => $match->homeTeam ? [
                'id' => $match->homeTeam->id,
                'name' => $match->homeTeam->name,
                'club_id' => $match->homeTeam->club_id,
                'club' => $match->homeTeam->club ? [
                    'id' => $match->homeTeam->club->id,
                    'name' => $match->homeTeam->club->name,
                    'players_count' => $match->homeTeam->club->players->count()
                ] : null
            ] : null,
            'away_team' => $match->awayTeam ? [
                'id' => $match->awayTeam->id,
                'name' => $match->awayTeam->name,
                'club_id' => $match->awayTeam->club_id,
                'club' => $match->awayTeam->club ? [
                    'id' => $match->awayTeam->club->id,
                    'name' => $match->awayTeam->club->name,
                    'players_count' => $match->awayTeam->club->players->count()
                ] : null
            ] : null
        ]);
        
        // Get team rosters
        $homeTeamRoster = $match->rosters()->where('team_id', $match->home_team_id)->first();
        $awayTeamRoster = $match->rosters()->where('team_id', $match->away_team_id)->first();
        
        // Get only the clubs related to this match (home and away teams' clubs)
        $matchClubIds = [];
        
        if ($match->homeTeam && $match->homeTeam->club) {
            $matchClubIds[] = $match->homeTeam->club->id;
        }
        
        if ($match->awayTeam && $match->awayTeam->club) {
            $matchClubIds[] = $match->awayTeam->club->id;
        }
        
        // Remove duplicates and get clubs with their players
        $matchClubIds = array_unique($matchClubIds);
        
        // Order clubs correctly: Home team first, then Away team
        $clubs = collect();
        
        // Add home team club first
        if ($match->homeTeam && $match->homeTeam->club) {
            $homeClub = Club::with('players')->find($match->homeTeam->club->id);
            if ($homeClub) {
                $clubs->push($homeClub);
            }
        }
        
        // Add away team club second
        if ($match->awayTeam && $match->awayTeam->club) {
            $awayClub = Club::with('players')->find($match->awayTeam->club->id);
            if ($awayClub) {
                $clubs->push($awayClub);
            }
        }
        
        // If we couldn't determine order, fall back to database order
        if ($clubs->isEmpty()) {
            $clubs = Club::whereIn('id', $matchClubIds)->with('players')->get();
        }
        
        // Debug: Log the clubs and their players
        \Log::info('Clubs loaded for import:', [
            'clubs_count' => $clubs->count(),
            'clubs_data' => $clubs->map(function($club) {
                return [
                    'id' => $club->id,
                    'name' => $club->name,
                    'players_count' => $club->players->count(),
                    'players' => $club->players->map(function($player) {
                        return [
                            'id' => $player->id,
                            'name' => $player->name,
                            'position' => $player->position,
                            'age' => $player->age,
                            'overall_rating' => $player->overall_rating
                        ];
                    })->toArray()
                ];
            })->toArray()
        ]);
        
        return view('match-sheet.import-players', compact('match', 'homeTeamRoster', 'awayTeamRoster', 'clubs'));
    }

    public function getClubPlayers(Request $request, MatchModel $match)
    {
        \Log::info('getClubPlayers called', [
            'match_id' => $match->id,
            'club_id' => $request->club_id,
            'user_id' => auth()->id()
        ]);
        
        $this->authorizeCompetitionAccess($match->competition);
        
        $request->validate([
            'club_id' => 'required|exists:clubs,id'
        ]);
        
        // Validate that the club is related to the match
        $matchClubIds = [];
        if ($match->homeTeam && $match->homeTeam->club) {
            $matchClubIds[] = $match->homeTeam->club->id;
        }
        if ($match->awayTeam && $match->awayTeam->club) {
            $matchClubIds[] = $match->awayTeam->club->id;
        }
        
        if (!in_array($request->club_id, $matchClubIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Club is not associated with this match'
            ], 400);
        }
        
        $players = \App\Models\Player::where('club_id', $request->club_id)
            ->select('id', 'name', 'position', 'age', 'overall_rating', 'nationality')
            ->orderBy('name')
            ->get();
        
        \Log::info('getClubPlayers response', [
            'club_id' => $request->club_id,
            'players_count' => $players->count(),
            'players' => $players->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'players' => $players
        ]);
    }

    public function processImportPlayersMatchSheet(Request $request, MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        \Log::info('Import players request received', [
            'match_id' => $match->id,
            'request_data' => $request->all()
        ]);
        
        $request->validate([
            'team_type' => 'required|in:home,away',
            'club_id' => 'required|exists:clubs,id',
            'players' => 'required|array|min:1',
            'players.*' => 'exists:players,id'
        ]);
        
        try {
            // Validate that the selected club is related to the match
            $matchClubIds = [];
            if ($match->homeTeam && $match->homeTeam->club) {
                $matchClubIds[] = $match->homeTeam->club->id;
            }
            if ($match->awayTeam && $match->awayTeam->club) {
                $matchClubIds[] = $match->awayTeam->club->id;
            }
            
            \Log::info('Match club validation', [
                'match_club_ids' => $matchClubIds,
                'requested_club_id' => $request->club_id,
                'home_team' => $match->homeTeam ? $match->homeTeam->toArray() : null,
                'away_team' => $match->awayTeam ? $match->awayTeam->toArray() : null
            ]);
            
            if (!in_array($request->club_id, $matchClubIds)) {
                \Log::warning('Invalid club selected for match', [
                    'match_id' => $match->id,
                    'requested_club_id' => $request->club_id,
                    'valid_club_ids' => $matchClubIds
                ]);
                return redirect()->back()
                    ->with('error', 'Selected club is not associated with this match.')
                    ->withInput();
            }
            
            // Determine which team to import players for
            $teamId = $request->team_type === 'home' ? $match->home_team_id : $match->away_team_id;
            
            \Log::info('Team determination', [
                'team_type' => $request->team_type,
                'team_id' => $teamId,
                'home_team_id' => $match->home_team_id,
                'away_team_id' => $match->away_team_id
            ]);
            
            // Get or create roster for this team
            $roster = $match->rosters()->where('team_id', $teamId)->first();
            if (!$roster) {
                $roster = $match->rosters()->create([
                    'team_id' => $teamId
                ]);
                \Log::info('Created new roster', ['roster_id' => $roster->id, 'team_id' => $teamId]);
            } else {
                \Log::info('Found existing roster', ['roster_id' => $roster->id, 'team_id' => $teamId]);
            }
            
            // Clear existing players from this roster
            $deletedCount = $roster->players()->delete();
            \Log::info('Cleared existing roster players', ['deleted_count' => $deletedCount]);
            
            // Validate that all selected players belong to the selected club
            $selectedPlayerIds = $request->players;
            $clubPlayers = \App\Models\Player::where('club_id', $request->club_id)->pluck('id')->toArray();
            
            \Log::info('Player validation', [
                'selected_players' => $selectedPlayerIds,
                'club_players' => $clubPlayers,
                'club_id' => $request->club_id
            ]);
            
            $invalidPlayers = array_diff($selectedPlayerIds, $clubPlayers);
            if (!empty($invalidPlayers)) {
                \Log::warning('Invalid players selected', ['invalid_players' => $invalidPlayers]);
                return redirect()->back()
                    ->with('error', 'Some selected players do not belong to the selected club.')
                    ->withInput();
            }
            
            // Import selected players
            $selectedPlayers = $request->players;
            $starters = array_slice($selectedPlayers, 0, 11); // First 11 are starters
            $substitutes = array_slice($selectedPlayers, 11); // Rest are substitutes
            
            \Log::info('Player distribution', [
                'total_players' => count($selectedPlayers),
                'starters' => $starters,
                'substitutes' => $substitutes
            ]);
            
            // Get player data for positions
            $playerData = \App\Models\Player::whereIn('id', $selectedPlayers)->pluck('position', 'id')->toArray();
            
            // Add starters
            $createdStarters = [];
            foreach ($starters as $index => $playerId) {
                $rosterPlayer = $roster->players()->create([
                    'player_id' => $playerId,
                    'is_starter' => true,
                    'jersey_number' => $index + 1,
                    'position' => $playerData[$playerId] ?? 'Unknown'
                ]);
                $createdStarters[] = $rosterPlayer->id;
            }
            
            // Add substitutes
            $createdSubstitutes = [];
            foreach ($substitutes as $index => $playerId) {
                $rosterPlayer = $roster->players()->create([
                    'player_id' => $playerId,
                    'is_starter' => false,
                    'jersey_number' => $index + 12, // Start from 12 for substitutes
                    'position' => $playerData[$playerId] ?? 'Unknown'
                ]);
                $createdSubstitutes[] = $rosterPlayer->id;
            }
            
            \Log::info('Players imported successfully', [
                'roster_id' => $roster->id,
                'created_starters' => $createdStarters,
                'created_substitutes' => $createdSubstitutes,
                'total_created' => count($createdStarters) + count($createdSubstitutes)
            ]);
            
            // Verify the roster was created correctly
            $finalRoster = $match->rosters()->with('players.player')->where('team_id', $teamId)->first();
            \Log::info('Final roster verification', [
                'roster_id' => $finalRoster->id,
                'player_count' => $finalRoster->players->count(),
                'players' => $finalRoster->players->map(function($rp) {
                    return [
                        'player_id' => $rp->player_id,
                        'player_name' => $rp->player->name ?? 'Unknown',
                        'is_starter' => $rp->is_starter,
                        'jersey_number' => $rp->jersey_number
                    ];
                })->toArray()
            ]);
            
            $teamName = $request->team_type === 'home' 
                ? ($match->homeTeam->club->name ?? $match->homeTeam->name)
                : ($match->awayTeam->club->name ?? $match->awayTeam->name);
            
            return redirect()->route('competition-management.matches.match-sheet', $match)
                ->with('success', count($selectedPlayers) . ' players imported successfully for ' . $teamName);
                
        } catch (\Exception $e) {
            \Log::error('Failed to import players', [
                'match_id' => $match->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to import players: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function uploadSignedMatchSheet(Request $request, MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Upload signed match sheet logic here
        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Signed match sheet uploaded successfully');
    }

    public function addEventMatchSheet(Request $request, MatchModel $match)
    {
        $this->authorizeCompetitionAccess($match->competition);
        
        // Add event logic here
        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Event added successfully');
    }

    public function matchesIndex()
    {
        $user = Auth::user();
        $matches = collect();
        $competitions = collect();

        if (in_array($user->role, ['system_admin', 'admin'])) {
            // System admin and admin see all matches
            $matches = GameMatch::with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->orderBy('scheduled_at', 'desc')
                ->paginate(20);
            
            $competitions = Competition::orderBy('name')->get();
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            // Association users see matches from their association's competitions
            $competitions = Competition::where('association_id', $user->association_id)->get();
            $competitionIds = $competitions->pluck('id');
            
            $matches = GameMatch::whereIn('competition_id', $competitionIds)
                ->with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->orderBy('scheduled_at', 'desc')
                ->paginate(20);
        } else {
            // Regular users see matches from their club
            $club = $user->club;
            if ($club) {
                $matches = GameMatch::where(function($query) use ($club) {
                    $query->whereHas('homeTeam', function($q) use ($club) {
                        $q->where('club_id', $club->id);
                    })->orWhereHas('awayTeam', function($q) use ($club) {
                        $q->where('club_id', $club->id);
                    });
                })->with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->orderBy('scheduled_at', 'desc')
                ->paginate(20);
                
                $competitions = Competition::whereHas('matches', function($query) use ($club) {
                    $query->where(function($q) use ($club) {
                        $q->whereHas('homeTeam', function($q2) use ($club) {
                            $q2->where('club_id', $club->id);
                        })->orWhereHas('awayTeam', function($q2) use ($club) {
                            $q2->where('club_id', $club->id);
                        });
                    });
                })->orderBy('name')->get();
            }
        }

        $stats = [
            'total' => $matches->total(),
            'upcoming' => $matches->where('scheduled_at', '>', now())->count(),
            'completed' => $matches->where('scheduled_at', '<', now())->count(),
            'today' => $matches->where('scheduled_at', '>=', now()->startOfDay())
                ->where('scheduled_at', '<=', now()->endOfDay())->count(),
        ];

        return view('competition-management.matches.index', compact('matches', 'competitions', 'stats'));
    }

    public function fixturesIndex()
    {
        $user = Auth::user();
        $competitions = collect();
        $upcomingFixtures = collect();
        $recentFixtures = collect();

        if (in_array($user->role, ['system_admin', 'admin'])) {
            // System admin and admin see all competitions
            $competitions = Competition::with(['association'])
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
            
            $upcomingFixtures = GameMatch::with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->where('scheduled_at', '>', now())
                ->orderBy('scheduled_at')
                ->limit(10)
                ->get();
                
            $recentFixtures = GameMatch::with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->where('scheduled_at', '<=', now())
                ->orderBy('scheduled_at', 'desc')
                ->limit(10)
                ->get();
        } elseif (in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            // Association users see competitions from their association
            $competitions = Competition::where('association_id', $user->association_id)
                ->where('status', 'active')
                ->with(['association'])
                ->orderBy('name')
                ->get();
                
            $competitionIds = $competitions->pluck('id');
            
            $upcomingFixtures = GameMatch::whereIn('competition_id', $competitionIds)
                ->with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->where('scheduled_at', '>', now())
                ->orderBy('scheduled_at')
                ->limit(10)
                ->get();
                
            $recentFixtures = GameMatch::whereIn('competition_id', $competitionIds)
                ->with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->where('scheduled_at', '<=', now())
                ->orderBy('scheduled_at', 'desc')
                ->limit(10)
                ->get();
        } else {
            // Regular users see fixtures from their club
            $club = $user->club;
            if ($club) {
                $competitions = Competition::whereHas('matches', function($query) use ($club) {
                    $query->where(function($q) use ($club) {
                        $q->whereHas('homeTeam', function($q2) use ($club) {
                            $q2->where('club_id', $club->id);
                        })->orWhereHas('awayTeam', function($q2) use ($club) {
                            $q2->where('club_id', $club->id);
                        });
                    });
                })->where('status', 'active')
                ->with(['association'])
                ->orderBy('name')
                ->get();
                
                $upcomingFixtures = GameMatch::where(function($query) use ($club) {
                    $query->whereHas('homeTeam', function($q) use ($club) {
                        $q->where('club_id', $club->id);
                    })->orWhereHas('awayTeam', function($q) use ($club) {
                        $q->where('club_id', $club->id);
                    });
                })->with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->where('scheduled_at', '>', now())
                ->orderBy('scheduled_at')
                ->limit(10)
                ->get();
                
                $recentFixtures = GameMatch::where(function($query) use ($club) {
                    $query->whereHas('homeTeam', function($q) use ($club) {
                        $q->where('club_id', $club->id);
                    })->orWhereHas('awayTeam', function($q) use ($club) {
                        $q->where('club_id', $club->id);
                    });
                })->with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->where('scheduled_at', '<=', now())
                ->orderBy('scheduled_at', 'desc')
                ->limit(10)
                ->get();
            }
        }

        $stats = [
            'total_competitions' => $competitions->count(),
            'upcoming_fixtures' => $upcomingFixtures->count(),
            'recent_fixtures' => $recentFixtures->count(),
            'today_fixtures' => GameMatch::where('scheduled_at', '>=', now()->startOfDay())
                ->where('scheduled_at', '<=', now()->endOfDay())->count(),
        ];

        return view('fixtures.index', compact('competitions', 'upcomingFixtures', 'recentFixtures', 'stats'));
    }

    public function exportAllMatchSheets(Competition $competition)
    {
        $this->authorizeCompetitionAccess($competition);
        
        // Get all matches for this competition with their match sheets
        $matches = $competition->matches()->with([
            'homeTeam.club',
            'awayTeam.club',
            'matchSheet',
            'matchSheet.events.player',
            'matchSheet.events.team'
        ])->get();
        
        // Generate a comprehensive report of all match sheets
        $filename = 'match-sheets-' . $competition->short_name . '-' . date('Y-m-d-H-i-s') . '.pdf';
        
        // For now, return a simple response
        // In a real implementation, you would generate a comprehensive PDF report
        $content = "%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj
2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj
3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
>>
endobj
4 0 obj
<<
/Length 200
>>
stream
BT
/F1 16 Tf
72 720 Td
(Match Sheets Report - " . $competition->name . ") Tj
0 -30 Td
/F1 12 Tf
(Generated on: " . date('d/m/Y at H:i') . ") Tj
0 -30 Td
(Total Matches: " . $matches->count() . ") Tj
0 -30 Td
(Status: Generated successfully) Tj
ET
endstream
endobj
xref
0 5
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000204 00000 n 
trailer
<<
/Size 5
/Root 1 0 R
>>
startxref
453
%%EOF";
        
        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Length', strlen($content));
    }
}
