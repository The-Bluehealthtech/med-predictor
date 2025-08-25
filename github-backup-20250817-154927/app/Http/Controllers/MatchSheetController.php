<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\MatchSheet;
use App\Models\MatchEvent;
use App\Models\MatchRoster;
use App\Models\MatchRosterPlayer;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\MatchOfficial;
use App\Models\Competition;

class MatchSheetController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $matchSheets = collect();
        $upcomingMatches = collect();
        $recentMatches = collect();

        if ($user->hasRole('admin') || $user->hasRole('referee')) {
            // Admins and referees can see all match sheets
            $matchSheets = MatchSheet::with(['match.homeTeam.club', 'match.awayTeam.club', 'match.competition'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $upcomingMatches = GameMatch::with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->where('scheduled_at', '>', now())
                ->orderBy('scheduled_at')
                ->get();
        } else {
            // Regular users see only their club's matches
            $club = $user->club;
            if ($club) {
                $matchSheets = MatchSheet::whereHas('match', function($query) use ($club) {
                    $query->where(function($q) use ($club) {
                        $q->whereHas('homeTeam', function($q2) use ($club) {
                            $q2->where('club_id', $club->id);
                        })->orWhereHas('awayTeam', function($q2) use ($club) {
                            $q2->where('club_id', $club->id);
                        });
                    });
                })->with(['match.homeTeam.club', 'match.awayTeam.club', 'match.competition'])
                ->orderBy('created_at', 'desc')
                ->get();

                $upcomingMatches = GameMatch::where(function($query) use ($club) {
                    $query->whereHas('homeTeam', function($q) use ($club) {
                        $q->where('club_id', $club->id);
                    })->orWhereHas('awayTeam', function($q) use ($club) {
                        $q->where('club_id', $club->id);
                    });
                })->with(['homeTeam.club', 'awayTeam.club', 'competition'])
                ->where('scheduled_at', '>', now())
                ->orderBy('scheduled_at')
                ->get();
            }
        }

        // Get recent matches (last 30 days)
        $recentMatches = GameMatch::with(['homeTeam.club', 'awayTeam.club', 'competition'])
            ->where('scheduled_at', '>=', now()->subDays(30))
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return view('match-sheet.index', compact('matchSheets', 'upcomingMatches', 'recentMatches'));
    }

    public function show(GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            $matchSheet = MatchSheet::create([
                'match_id' => $match->id,
                'status' => 'draft',
                'stage' => 'in_progress',
                'stage_in_progress_at' => now(),
                'match_status' => 'scheduled',
                'version' => 1,
                // Pre-fill with match data
                'match_number' => 'MS-' . str_pad($match->id, 6, '0', STR_PAD_LEFT),
                'stadium_venue' => $match->homeTeam->club->stadium ?? 'TBD',
                'kickoff_time' => $match->scheduled_at,
            ]);
        }

        // Check permissions
        if (!$matchSheet->canView(Auth::user())) {
            abort(403, 'You do not have permission to view this match sheet.');
        }

        $match->load(['homeTeam.club', 'awayTeam.club', 'competition']);
        
        // Get team rosters
        $homeTeamRoster = MatchRoster::where('match_id', $match->id)
            ->where('team_id', $match->home_team_id)
            ->with(['players.player'])
            ->first();
            
        $awayTeamRoster = MatchRoster::where('match_id', $match->id)
            ->where('team_id', $match->away_team_id)
            ->with(['players.player'])
            ->first();

        // Get match events
        $events = MatchEvent::where('match_sheet_id', $matchSheet->id)
            ->with(['player', 'team', 'assistedByPlayer', 'substitutedPlayer'])
            ->orderBy('minute')
            ->orderBy('extra_time_minute')
            ->get();

        // Group events by type
        $goals = $events->where('type', 'goal');
        $cards = $events->whereIn('type', ['yellow_card', 'red_card']);
        $substitutions = $events->whereIn('type', ['substitution_in', 'substitution_out']);

        // Check permissions for view
        $canEdit = $matchSheet->canEdit(Auth::user());
        $canView = $matchSheet->canView(Auth::user());
        $canValidate = $matchSheet->canValidate(Auth::user());
        $canAssignReferee = $matchSheet->canAssignReferee(Auth::user());
        $canSignLineup = $matchSheet->canSignLineup(Auth::user(), 'home') || $matchSheet->canSignLineup(Auth::user(), 'away');
        $canSignPostMatch = $matchSheet->canSignPostMatch(Auth::user(), 'home') || $matchSheet->canSignPostMatch(Auth::user(), 'away');

        // Get stage progress
        $stageProgress = $matchSheet->getStageProgress();

        // Get available referees for assignment (with their qualifications)
        $availableReferees = User::where('role', 'referee')
            ->orderBy('name')
            ->get()
            ->map(function($referee) {
                return [
                    'id' => $referee->id,
                    'name' => $referee->name,
                    'email' => $referee->email,
                    'qualifications' => $referee->referee_qualifications ?? 'FIFA Licensed',
                    'experience_years' => $referee->experience_years ?? '5+',
                    'specializations' => $referee->specializations ?? 'Premier League, Champions League'
                ];
            });

        // Get match officials if assigned
        $matchOfficials = MatchOfficial::where('match_id', $match->id)
            ->with('user')
            ->get()
            ->groupBy('role');

        // Pre-fill venue information
        $venueInfo = [
            'stadium' => $match->homeTeam->club->stadium ?? 'TBD',
            'capacity' => $match->homeTeam->club->stadium_capacity ?? 'TBD',
            'address' => $match->homeTeam->club->address ?? 'TBD',
            'city' => $match->homeTeam->club->city ?? 'TBD',
        ];

        // Get team officials (coaches, managers)
        $homeTeamOfficials = [
            'coach' => $matchSheet->home_team_coach ?? 'TBD',
            'manager' => $matchSheet->home_team_manager ?? 'TBD',
        ];

        $awayTeamOfficials = [
            'coach' => $matchSheet->away_team_coach ?? 'TBD',
            'manager' => $matchSheet->away_team_manager ?? 'TBD',
        ];

        return view('match-sheet.show', compact(
            'match',
            'matchSheet',
            'homeTeamRoster',
            'awayTeamRoster',
            'events',
            'goals',
            'cards',
            'substitutions',
            'canEdit',
            'canView',
            'canValidate',
            'canAssignReferee',
            'canSignLineup',
            'canSignPostMatch',
            'stageProgress',
            'availableReferees',
            'matchOfficials',
            'venueInfo',
            'homeTeamOfficials',
            'awayTeamOfficials'
        ));
    }

    public function edit(GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            $matchSheet = MatchSheet::create([
                'match_id' => $match->id,
                'status' => 'draft',
                'match_status' => 'completed',
                'version' => 1,
            ]);
        }

        // Check permissions
        if (!$matchSheet->canEdit(Auth::user())) {
            abort(403, 'You do not have permission to edit this match sheet.');
        }

        $match->load(['homeTeam', 'awayTeam', 'competition']);
        
        // Get available players for both teams
        $homeTeamPlayers = Player::whereHas('teamPlayers', function ($query) use ($match) {
            $query->where('team_id', $match->home_team_id);
        })->get();
        
        $awayTeamPlayers = Player::whereHas('teamPlayers', function ($query) use ($match) {
            $query->where('team_id', $match->away_team_id);
        })->get();

        // Get available referees
        $referees = User::where('role', 'referee')->get();

        return view('match-sheet.edit', compact(
            'match',
            'matchSheet',
            'homeTeamPlayers',
            'awayTeamPlayers',
            'referees'
        ));
    }

    public function update(Request $request, GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            $matchSheet = MatchSheet::create([
                'match_id' => $match->id,
                'status' => 'draft',
                'match_status' => 'completed',
                'version' => 1,
            ]);
        }

        // Check permissions
        if (!$matchSheet->canEdit(Auth::user())) {
            abort(403, 'You do not have permission to edit this match sheet.');
        }

        $validator = Validator::make($request->all(), [
            'match_number' => 'nullable|string|max:50',
            'stadium_venue' => 'nullable|string|max:255',
            'weather_conditions' => 'nullable|string|max:100',
            'pitch_conditions' => 'nullable|string|max:100',
            'kickoff_time' => 'nullable|date_format:H:i',
            'home_team_score' => 'nullable|integer|min:0|max:50',
            'away_team_score' => 'nullable|integer|min:0|max:50',
            'home_team_coach' => 'nullable|string|max:255',
            'away_team_coach' => 'nullable|string|max:255',
            'home_team_manager' => 'nullable|string|max:255',
            'away_team_manager' => 'nullable|string|max:255',
            'main_referee_id' => 'nullable|exists:users,id',
            'assistant_referee_1_id' => 'nullable|exists:users,id',
            'assistant_referee_2_id' => 'nullable|exists:users,id',
            'fourth_official_id' => 'nullable|exists:users,id',
            'var_referee_id' => 'nullable|exists:users,id',
            'var_assistant_id' => 'nullable|exists:users,id',
            'referee_report' => 'nullable|string',
            'match_status' => 'required|in:completed,suspended,abandoned',
            'suspension_reason' => 'nullable|string',
            'crowd_issues' => 'nullable|string',
            'protests_incidents' => 'nullable|string',
            'notes' => 'nullable|string',
            'home_team_roster' => 'nullable|array',
            'away_team_roster' => 'nullable|array',
            'home_team_substitutes' => 'nullable|array',
            'away_team_substitutes' => 'nullable|array',
            'match_statistics' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Update match sheet
            $matchSheet->update($request->only([
                'match_number',
                'stadium_venue',
                'weather_conditions',
                'pitch_conditions',
                'kickoff_time',
                'home_team_score',
                'away_team_score',
                'home_team_coach',
                'away_team_coach',
                'home_team_manager',
                'away_team_manager',
                'main_referee_id',
                'assistant_referee_1_id',
                'assistant_referee_2_id',
                'fourth_official_id',
                'var_referee_id',
                'var_assistant_id',
                'referee_report',
                'match_status',
                'suspension_reason',
                'crowd_issues',
                'protests_incidents',
                'notes',
                'home_team_roster',
                'away_team_roster',
                'home_team_substitutes',
                'away_team_substitutes',
                'match_statistics',
            ]));

            // Update match scores
            $match->update([
                'home_score' => $request->home_team_score,
                'away_score' => $request->away_team_score,
            ]);

            // Update team rosters if provided
            if ($request->has('home_team_roster')) {
                $this->updateTeamRoster($match->id, $match->home_team_id, $request->home_team_roster, $request->home_team_substitutes ?? []);
            }
            
            if ($request->has('away_team_roster')) {
                $this->updateTeamRoster($match->id, $match->away_team_id, $request->away_team_roster, $request->away_team_substitutes ?? []);
            }

            DB::commit();

            return redirect()->route('competition-management.matches.match-sheet', $match)
                ->with('success', 'Match sheet updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update match sheet: ' . $e->getMessage())->withInput();
        }
    }

    public function submit(GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            return back()->with('error', 'Match sheet not found.');
        }

        // Check permissions
        if (!$matchSheet->canEdit(Auth::user())) {
            abort(403, 'You do not have permission to submit this match sheet.');
        }

        // Validate required fields
        $validator = Validator::make($matchSheet->toArray(), [
            'home_team_score' => 'required|integer|min:0',
            'away_team_score' => 'required|integer|min:0',
            'main_referee_id' => 'required|exists:users,id',
            'referee_report' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('error', 'Please complete all required fields before submitting.');
        }

        $matchSheet->submit();

        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Match sheet submitted successfully for validation.');
    }

    public function validate(Request $request, GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            return back()->with('error', 'Match sheet not found.');
        }

        // Check permissions
        if (!$matchSheet->canValidate(Auth::user())) {
            abort(403, 'You do not have permission to validate this match sheet.');
        }

        $validator = Validator::make($request->all(), [
            'validation_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $matchSheet->validate(Auth::user(), $request->validation_notes);

        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Match sheet validated successfully.');
    }

    public function reject(Request $request, GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            return back()->with('error', 'Match sheet not found.');
        }

        // Check permissions
        if (!$matchSheet->canReject(Auth::user())) {
            abort(403, 'You do not have permission to reject this match sheet.');
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $matchSheet->reject(Auth::user(), $request->rejection_reason);

        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Match sheet rejected. Referee can make corrections and resubmit.');
    }

    public function addEvent(Request $request, GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            return back()->with('error', 'Match sheet not found.');
        }

        // Check permissions
        if (!$matchSheet->canEdit(Auth::user())) {
            abort(403, 'You do not have permission to add events to this match sheet.');
        }

        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string|in:goal,yellow_card,red_card,substitution_in,substitution_out,injury,assist,missed_penalty,penalty_saved,own_goal,var_decision',
            'player_id' => 'required|exists:players,id',
            'team_id' => 'required|exists:teams,id',
            'minute' => 'required|integer|min:1|max:120',
            'extra_time_minute' => 'nullable|integer|min:1|max:30',
            'period' => 'required|in:first_half,second_half,extra_time_first,extra_time_second,penalty_shootout',
            'description' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:100',
            'severity' => 'nullable|in:low,medium,high',
            'assisted_by_player_id' => 'nullable|exists:players,id',
            'substituted_player_id' => 'nullable|exists:players,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        MatchEvent::create([
            'match_id' => $match->id,
            'player_id' => $request->player_id,
            'team_id' => $request->team_id,
            'assisted_by_player_id' => $request->assisted_by_player_id,
            'substituted_player_id' => $request->substituted_player_id,
            'recorded_by_user_id' => Auth::id(),
            'event_type' => $request->event_type,
            'minute' => $request->minute,
            'extra_time_minute' => $request->extra_time_minute,
            'period' => $request->period,
            'description' => $request->description,
            'location' => $request->location,
            'severity' => $request->severity,
            'recorded_at' => now(),
        ]);

        return back()->with('success', 'Event added successfully.');
    }

    public function removeEvent(MatchEvent $event)
    {
        $matchSheet = MatchSheet::where('match_id', $event->match_id)->first();
        
        if (!$matchSheet) {
            return back()->with('error', 'Match sheet not found.');
        }

        // Check permissions
        if (!$matchSheet->canEdit(Auth::user())) {
            abort(403, 'You do not have permission to remove events from this match sheet.');
        }

        $event->delete();

        return back()->with('success', 'Event removed successfully.');
    }

    public function export(GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            return back()->with('error', 'Match sheet not found.');
        }

        // Check permissions
        if (!$matchSheet->canView(Auth::user())) {
            abort(403, 'You do not have permission to export this match sheet.');
        }

        $data = $matchSheet->toJsonExport();
        
        $filename = "match_sheet_{$match->id}_{$matchSheet->match_number}_{$match->match_date->format('Y-m-d')}.json";
        
        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    private function updateTeamRoster(int $matchId, int $teamId, array $starters, array $substitutes): void
    {
        // Delete existing roster
        MatchRoster::where('match_id', $matchId)
            ->where('team_id', $teamId)
            ->delete();

        // Create new roster
        $roster = MatchRoster::create([
            'match_id' => $matchId,
            'team_id' => $teamId,
            'submitted_at' => now(),
        ]);

        // Add starters
        foreach ($starters as $index => $playerId) {
            if ($playerId) {
                MatchRosterPlayer::create([
                    'match_roster_id' => $roster->id,
                    'player_id' => $playerId,
                    'jersey_number' => $index + 1,
                    'is_starter' => true,
                    'position' => $this->getPositionByNumber($index + 1),
                ]);
            }
        }

        // Add substitutes
        foreach ($substitutes as $index => $playerId) {
            if ($playerId) {
                MatchRosterPlayer::create([
                    'match_roster_id' => $roster->id,
                    'player_id' => $playerId,
                    'jersey_number' => 12 + $index, // Substitutes start from 12
                    'is_starter' => false,
                    'position' => 'substitute',
                ]);
            }
        }
    }

    private function getPositionByNumber(int $number): string
    {
        return match($number) {
            1 => 'goalkeeper',
            2, 3, 4, 5 => 'defender',
            6, 7, 8, 10 => 'midfielder',
            9, 11 => 'forward',
            default => 'player',
        };
    }

    // Stage-based action methods
    public function assignReferee(Request $request, GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            abort(404, 'Match sheet not found.');
        }

        // Check permissions
        if (!$matchSheet->canAssignReferee(Auth::user())) {
            abort(403, 'You do not have permission to assign referees to this match.');
        }

        $request->validate([
            'referee_id' => 'required|exists:users,id',
            'assistant_referee_1_id' => 'nullable|exists:users,id',
            'assistant_referee_2_id' => 'nullable|exists:users,id',
            'fourth_official_id' => 'nullable|exists:users,id',
            'var_referee_id' => 'nullable|exists:users,id',
            'var_assistant_id' => 'nullable|exists:users,id',
            'match_observer_id' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            // Assign main referee
            $referee = User::findOrFail($request->referee_id);
            $matchSheet->assignReferee($referee, Auth::user());

            // Update match sheet with all officials
            $matchSheet->update([
                'main_referee_id' => $request->referee_id,
                'assistant_referee_1_id' => $request->assistant_referee_1_id,
                'assistant_referee_2_id' => $request->assistant_referee_2_id,
                'fourth_official_id' => $request->fourth_official_id,
                'var_referee_id' => $request->var_referee_id,
                'var_assistant_id' => $request->var_assistant_id,
                'match_observer_id' => $request->match_observer_id,
            ]);

            // Create/update match officials records
            $this->updateMatchOfficials($match->id, $request->all());

            DB::commit();
            return back()->with('success', 'Referee team assigned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to assign referee team: ' . $e->getMessage());
        }
    }

    private function updateMatchOfficials(int $matchId, array $officials): void
    {
        // Clear existing officials
        MatchOfficial::where('match_id', $matchId)->delete();

        // Add new officials
        $officialRoles = [
            'referee_id' => 'main_referee',
            'assistant_referee_1_id' => 'assistant_referee_1',
            'assistant_referee_2_id' => 'assistant_referee_2',
            'fourth_official_id' => 'fourth_official',
            'var_referee_id' => 'var_referee',
            'var_assistant_id' => 'var_assistant',
            'match_observer_id' => 'match_observer',
        ];

        foreach ($officialRoles as $field => $role) {
            if (!empty($officials[$field])) {
                MatchOfficial::create([
                    'match_id' => $matchId,
                    'user_id' => $officials[$field],
                    'role' => $role,
                ]);
            }
        }
    }

    public function signLineup(Request $request, GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            abort(404, 'Match sheet not found.');
        }

        $request->validate([
            'team_type' => 'required|in:home,away',
            'signature' => 'required|string|max:255'
        ]);

        $teamType = $request->team_type;
        
        if ($matchSheet->signLineup($teamType, Auth::user(), $request->signature)) {
            return back()->with('success', 'Lineup signed successfully.');
        }

        return back()->with('error', 'Failed to sign lineup.');
    }

    public function signPostMatch(Request $request, GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            abort(404, 'Match sheet not found.');
        }

        $request->validate([
            'team_type' => 'required|in:home,away',
            'signature' => 'required|string|max:255',
            'comments' => 'nullable|string|max:1000'
        ]);

        $teamType = $request->team_type;
        
        if ($matchSheet->signPostMatch($teamType, Auth::user(), $request->signature, $request->comments)) {
            return back()->with('success', 'Post-match signature added successfully.');
        }

        return back()->with('error', 'Failed to add post-match signature.');
    }

    public function faValidate(Request $request, GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            abort(404, 'Match sheet not found.');
        }

        $request->validate([
            'validation_notes' => 'nullable|string|max:1000'
        ]);

        if ($matchSheet->faValidate(Auth::user(), $request->validation_notes)) {
            return back()->with('success', 'Match sheet validated by FA successfully.');
        }

        return back()->with('error', 'Failed to validate match sheet.');
    }

    public function exportAllForCompetition(Competition $competition)
    {
        // Get all matches for this competition
        $matches = GameMatch::where('competition_id', $competition->id)
            ->with(['homeTeam', 'awayTeam', 'matchSheet'])
            ->get();

        // Generate PDF for all match sheets
        $pdf = \PDF::loadView('match-sheet.export-all', compact('matches', 'competition'));
        
        return $pdf->download('match-sheets-' . $competition->name . '.pdf');
    }

    /**
     * Show the import players form for match sheet
     */
    public function showImportPlayers(GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            abort(404, 'Match sheet not found.');
        }

        // Check permissions
        if (!$matchSheet->canEdit(Auth::user())) {
            abort(403, 'You do not have permission to import players for this match sheet.');
        }

        $match->load(['homeTeam.club', 'awayTeam.club', 'competition']);

        // Get available clubs for import
        $clubs = \App\Models\Club::with(['players' => function($query) {
            $query->where('status', 'active');
        }])->get();

        // Get current rosters
        $homeTeamRoster = MatchRoster::where('match_id', $match->id)
            ->where('team_id', $match->home_team_id)
            ->with(['players.player'])
            ->first();
            
        $awayTeamRoster = MatchRoster::where('match_id', $match->id)
            ->where('team_id', $match->away_team_id)
            ->with(['players.player'])
            ->first();

        return view('match-sheet.import-players', compact(
            'match',
            'matchSheet',
            'clubs',
            'homeTeamRoster',
            'awayTeamRoster'
        ));
    }

    /**
     * Process the import of players for match sheet
     */
    public function processImportPlayers(Request $request, GameMatch $match)
    {
        $matchSheet = MatchSheet::where('match_id', $match->id)->first();
        
        if (!$matchSheet) {
            abort(404, 'Match sheet not found.');
        }

        // Check permissions
        if (!$matchSheet->canEdit(Auth::user())) {
            abort(403, 'You do not have permission to import players for this match sheet.');
        }

        $request->validate([
            'team_type' => 'required|in:home,away',
            'club_id' => 'required|exists:clubs,id',
            'players' => 'required|array|min:1',
            'players.*' => 'exists:players,id'
        ]);

        $teamType = $request->input('team_type');
        $clubId = $request->input('club_id');
        $playerIds = $request->input('players');

        // Determine which team to update
        $teamId = $teamType === 'home' ? $match->home_team_id : $match->away_team_id;

        // Get or create match roster
        $roster = MatchRoster::firstOrCreate([
            'match_id' => $match->id,
            'team_id' => $teamId
        ]);

        // Clear existing players
        $roster->players()->delete();

        // Add selected players to roster
        foreach ($playerIds as $index => $playerId) {
            $player = \App\Models\Player::find($playerId);
            if ($player) {
                MatchRosterPlayer::create([
                    'match_roster_id' => $roster->id,
                    'player_id' => $playerId,
                    'jersey_number' => $index + 1,
                    'position' => $this->getPositionByNumber($index + 1),
                    'is_starter' => $index < 11, // First 11 are starters
                    'substitution_time' => null,
                    'substitution_reason' => null
                ]);
            }
        }

        return redirect()->route('competition-management.matches.match-sheet', $match)
            ->with('success', 'Players imported successfully for ' . ucfirst($teamType) . ' team.');
    }

    /**
     * Handle upload of hand-signed match sheet image or PDF
     */
    public function uploadSignedSheet(Request $request, GameMatch $match)
    {
        $request->validate([
            'signed_sheet' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:8192',
        ]);

        $matchSheet = MatchSheet::where('match_id', $match->id)->firstOrFail();

        // Store the file
        $path = $request->file('signed_sheet')->store('signed_sheets', 'public');

        // Update the match sheet
        $matchSheet->signed_sheet_path = $path;
        $matchSheet->save();

        return redirect()->back()->with('success', 'Hand-signed match sheet uploaded successfully.');
    }

    /**
     * Handle team official signature
     */
    public function signTeam(Request $request, GameMatch $match)
    {
        $request->validate([
            'team_type' => 'required|in:home,away',
            'signature' => 'required|string|max:255',
        ]);

        $matchSheet = MatchSheet::where('match_id', $match->id)->firstOrFail();
        $user = auth()->user();

        // Check if user is authorized to sign for this team
        $teamId = $request->team_type === 'home' ? $match->home_team_id : $match->away_team_id;
        
        if (!$user->isTeamOfficial() || $user->team_id !== $teamId) {
            return redirect()->back()->with('error', 'You are not authorized to sign for this team.');
        }

        // Check if already signed
        $signatureField = $request->team_type === 'home' ? 'home_team_signature' : 'away_team_signature';
        if ($matchSheet->$signatureField) {
            return redirect()->back()->with('error', 'This team has already been signed.');
        }

        // Sign the match sheet
        $matchSheet->signByTeam($request->team_type, $request->signature);
        $matchSheet->logUserAction('team_signed', $user, [
            'team_type' => $request->team_type,
            'signature' => $request->signature
        ]);

        return redirect()->back()->with('success', 'Team signature added successfully.');
    }
} 