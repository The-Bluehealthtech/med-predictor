<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Player;
use App\Models\Lineup;
use App\Models\Team;
use App\Models\MatchModel;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClubManagementController extends Controller
{
    public function index()
    {
        return view('club-management.index');
    }

    public function dashboard()
    {
        return view('club-management.dashboard');
    }

    public function players(Request $request)
    {
        // Get all clubs for admin users, or just the user's club for regular users
        $clubs = collect();
        $club = null;

        if (Auth::user()->hasRole('admin')) {
            // Admin can see all clubs
            $clubs = Club::withCount('players')->get();
            
            // If a specific club is selected
            if ($request->has('club_id') && $request->club_id) {
                $club = Club::with('players')->find($request->club_id);
            }
        } else {
            // Regular users can only see their assigned club
            $userClub = Auth::user()->club;
            if ($userClub) {
                $clubs = collect([$userClub]);
                $club = $userClub->load('players');
            }
        }

        // Ensure $clubs is always a collection, never null
        if (!$clubs) {
            $clubs = collect();
        }

        return view('club-management.players.index', compact('clubs', 'club'));
    }

    public function teams()
    {
        // Get user's club
        $user = Auth::user();
        $club = null;
        $teams = collect();
        $totalPlayers = 0;

        if ($user->hasRole('admin')) {
            // Admin can see all teams
            $teams = Team::with(['club', 'players'])
                ->withCount('players')
                ->orderBy('created_at', 'desc')
                ->get();
            $totalPlayers = Player::count();
        } else {
            // Regular users can only see their club's teams
            $club = $user->club;
            if ($club) {
                $teams = Team::where('club_id', $club->id)
                    ->with(['club', 'players'])
                    ->withCount('players')
                    ->orderBy('created_at', 'desc')
                    ->get();
                $totalPlayers = Player::where('club_id', $club->id)->count();
            }
        }

        return view('club-management.teams.index', compact('teams', 'club', 'totalPlayers'));
    }

    public function licenses()
    {
        // Get user's club
        $user = Auth::user();
        $club = null;
        $licenses = collect();
        $expiringSoon = 0;

        if ($user->hasRole('admin')) {
            // Admin can see all licenses
            $licenses = \App\Models\PlayerLicense::with(['player', 'club'])
                ->orderBy('created_at', 'desc')
                ->get();
            $expiringSoon = \App\Models\PlayerLicense::where('status', 'active')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>', now())
                ->count();
        } else {
            // Regular users can only see their club's licenses
            $club = $user->club;
            if ($club) {
                $licenses = \App\Models\PlayerLicense::where('club_id', $club->id)
                    ->with(['player', 'club'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                $expiringSoon = \App\Models\PlayerLicense::where('club_id', $club->id)
                    ->where('status', 'active')
                    ->where('expiry_date', '<=', now()->addDays(30))
                    ->where('expiry_date', '>', now())
                    ->count();
            }
        }

        return view('club-management.licenses.index', compact('licenses', 'club', 'expiringSoon'));
    }

    public function lineups()
    {
        // Get user's club
        $user = Auth::user();
        $club = null;
        $lineups = collect();
        $teams = collect();
        $activeLineups = 0;
        $draftLineups = 0;
        $recentLineups = collect();

        if ($user->hasRole('admin')) {
            // Admin can see all lineups
            $lineups = Lineup::with(['team', 'club', 'competition'])
                ->orderBy('created_at', 'desc')
                ->get();
            $teams = Team::with('club')->get();
            $activeLineups = Lineup::where('status', 'active')->count();
            $draftLineups = Lineup::where('status', 'draft')->count();
            $recentLineups = Lineup::with(['team', 'club'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            // Regular users can only see their club's lineups
            $club = $user->club;
            if ($club) {
                $lineups = Lineup::where('club_id', $club->id)
                    ->with(['team', 'club', 'competition'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                $teams = Team::where('club_id', $club->id)->get();
                $activeLineups = Lineup::where('club_id', $club->id)
                    ->where('status', 'active')
                    ->count();
                $draftLineups = Lineup::where('club_id', $club->id)
                    ->where('status', 'draft')
                    ->count();
                $recentLineups = Lineup::where('club_id', $club->id)
                    ->with(['team', 'club'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }
        }

        return view('club-management.lineups.index', compact(
            'lineups', 
            'teams', 
            'club', 
            'activeLineups', 
            'draftLineups', 
            'recentLineups'
        ));
    }

    public function createLineup()
    {
        $user = Auth::user();
        $club = $user->club;
        
        if (!$club && !$user->hasRole('admin')) {
            abort(403, 'No club assigned');
        }

        $teams = $club ? Team::where('club_id', $club->id)->get() : Team::all();
        $players = $club ? Player::where('club_id', $club->id)->get() : Player::all();

        return view('club-management.lineups.create', compact('club', 'teams', 'players'));
    }

    public function storeLineup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'team_id' => 'required|exists:teams,id',
            'formation' => 'required|string|max:10',
            'match_type' => 'required|string|max:50',
            'players' => 'required|array|min:11',
            'players.*.player_id' => 'required|exists:players,id',
        ]);

        $user = Auth::user();
        $club = $user->club;

        $lineup = Lineup::create([
            'name' => $request->name,
            'team_id' => $request->team_id,
            'club_id' => $club->id,
            'formation' => $request->formation,
            'match_type' => $request->match_type,
            'opponent' => $request->opponent,
            'venue' => $request->venue,
            'weather_conditions' => $request->weather_conditions,
            'pitch_condition' => $request->pitch_condition,
            'pressing_intensity' => $request->pressing_intensity,
            'possession_style' => $request->possession_style,
            'defensive_line_height' => $request->defensive_line_height,
            'marking_system' => $request->marking_system,
            'tactical_notes' => $request->tactical_notes,
            'captain_id' => $request->captain_id,
            'vice_captain_id' => $request->vice_captain_id,
            'penalty_taker_id' => $request->penalty_taker_id,
            'free_kick_taker_id' => $request->free_kick_taker_id,
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        // Attach players to lineup
        foreach ($request->players as $index => $playerData) {
            if (!empty($playerData['player_id'])) {
                $lineup->players()->attach($playerData['player_id'], [
                    'is_substitute' => $playerData['is_substitute'] ?? 0,
                    'position_order' => $playerData['position_order'] ?? $index + 1,
                    'assigned_position' => $playerData['position'] ?? 'TBD',
                ]);
            }
        }

        return redirect()->route('club-management.lineups.show', $lineup)
            ->with('success', 'Lineup created successfully');
    }

    public function showLineup(Lineup $lineup)
    {
        $lineup->load(['team', 'club', 'competition', 'captain', 'viceCaptain', 'penaltyTaker', 'freeKickTaker', 'cornerTaker', 'players']);
        
        // Separate starters and substitutes
        $starters = $lineup->players()->wherePivot('is_substitute', 0)->get();
        $substitutes = $lineup->players()->wherePivot('is_substitute', 1)->get();
        
        $lineup->starters = $starters;
        $lineup->substitutes = $substitutes;

        return view('club-management.lineups.show', compact('lineup'));
    }

    public function editLineup(Lineup $lineup)
    {
        $user = Auth::user();
        $club = $user->club;
        
        $teams = $club ? Team::where('club_id', $club->id)->get() : Team::all();
        $players = $club ? Player::where('club_id', $club->id)->get() : Player::all();
        
        $lineup->load('players');

        return view('club-management.lineups.edit', compact('lineup', 'club', 'teams', 'players'));
    }

    public function updateLineup(Request $request, Lineup $lineup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'team_id' => 'required|exists:teams,id',
            'formation' => 'required|string|max:10',
            'match_type' => 'required|string|max:50',
        ]);

        $lineup->update([
            'name' => $request->name,
            'team_id' => $request->team_id,
            'formation' => $request->formation,
            'match_type' => $request->match_type,
            'opponent' => $request->opponent,
            'venue' => $request->venue,
            'weather_conditions' => $request->weather_conditions,
            'pitch_condition' => $request->pitch_condition,
            'pressing_intensity' => $request->pressing_intensity,
            'possession_style' => $request->possession_style,
            'defensive_line_height' => $request->defensive_line_height,
            'marking_system' => $request->marking_system,
            'tactical_notes' => $request->tactical_notes,
            'captain_id' => $request->captain_id,
            'vice_captain_id' => $request->vice_captain_id,
            'penalty_taker_id' => $request->penalty_taker_id,
            'free_kick_taker_id' => $request->free_kick_taker_id,
        ]);

        return redirect()->route('club-management.lineups.show', $lineup)
            ->with('success', 'Lineup updated successfully');
    }

    public function destroyLineup(Lineup $lineup)
    {
        $lineup->players()->detach();
        $lineup->delete();

        return redirect()->route('club-management.lineups.index')
            ->with('success', 'Lineup deleted successfully');
    }

    public function lineupGenerator()
    {
        $user = Auth::user();
        $club = $user->club;
        
        if (!$club && !$user->hasRole('admin')) {
            abort(403, 'No club assigned');
        }

        $teams = $club ? Team::where('club_id', $club->id)->get() : Team::all();
        
        // Get upcoming matches for the club's teams
        $teamIds = $teams->pluck('id');
        $upcomingMatches = MatchModel::where(function($query) use ($teamIds) {
            $query->whereIn('home_team_id', $teamIds)
                  ->orWhereIn('away_team_id', $teamIds);
        })
        ->where('match_date', '>', now())
        ->with(['home_team', 'away_team', 'competition'])
        ->orderBy('match_date')
        ->get();

        return view('club-management.lineups.generator', compact('club', 'teams', 'upcomingMatches'));
    }

    public function generateLineup(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'match_id' => 'required|exists:matches,id',
            'formation' => 'nullable|string|max:10',
            'tactical_style' => 'nullable|string|max:50',
        ]);

        $team = Team::findOrFail($request->team_id);
        $match = MatchModel::findOrFail($request->match_id);
        
        // Get available players for the team
        $players = Player::where('club_id', $team->club_id)
            ->where('team_id', $team->id)
            ->where('status', 'active')
            ->orderBy('rating', 'desc')
            ->get();

        if ($players->count() < 11) {
            return back()->with('error', 'Not enough players available for lineup generation');
        }

        // Generate lineup using the service
        $lineupService = new \App\Services\LineupGenerationService();
        $lineup = $lineupService->generateLineup($team, $match, $players, $request->formation, $request->tactical_style);

        return redirect()->route('club-management.lineups.show', $lineup)
            ->with('success', 'Lineup generated successfully');
    }

    public function bulkGenerateLineups(Request $request)
    {
        $request->validate([
            'match_ids' => 'required|array|min:1',
            'match_ids.*' => 'exists:matches,id',
            'formation' => 'nullable|string|max:10',
            'tactical_style' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $club = $user->club;
        
        if (!$club && !$user->hasRole('admin')) {
            abort(403, 'No club assigned');
        }

        $teams = $club ? Team::where('club_id', $club->id)->get() : Team::all();
        $lineupService = new \App\Services\LineupGenerationService();
        $generatedCount = 0;

        foreach ($request->match_ids as $matchId) {
            $match = MatchModel::findOrFail($matchId);
            
            // Find teams from this club that are playing in this match
            $clubTeams = $teams->filter(function($team) use ($match) {
                return $team->id === $match->home_team_id || $team->id === $match->away_team_id;
            });

            foreach ($clubTeams as $team) {
                $players = Player::where('club_id', $team->club_id)
                    ->where('team_id', $team->id)
                    ->where('status', 'active')
                    ->orderBy('rating', 'desc')
                    ->get();

                if ($players->count() >= 11) {
                    $lineupService->generateLineup($team, $match, $players, $request->formation, $request->tactical_style);
                    $generatedCount++;
                }
            }
        }

        return redirect()->route('club-management.lineups.index')
            ->with('success', "Successfully generated {$generatedCount} lineups");
    }

    public function createTeam()
    {
        $user = Auth::user();
        $club = $user->club;
        
        if (!$club && !$user->hasRole('admin')) {
            abort(403, 'No club assigned');
        }

        $teamTypes = [
            'first_team' => 'First Team',
            'reserve' => 'Reserve Team',
            'youth' => 'Youth Team',
            'academy' => 'Academy Team',
        ];

        $formations = [
            '4-4-2' => '4-4-2 (Classic)',
            '4-3-3' => '4-3-3 (Attacking)',
            '3-5-2' => '3-5-2 (Wingbacks)',
            '4-2-3-1' => '4-2-3-1 (Modern)',
            '3-4-3' => '3-4-3 (Attacking)',
            '5-3-2' => '5-3-2 (Defensive)',
            '4-1-4-1' => '4-1-4-1 (Balanced)',
        ];

        return view('club-management.teams.create', compact('club', 'teamTypes', 'formations'));
    }

    public function storeTeam(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:first_team,reserve,youth,academy',
            'formation' => 'required|string|max:10',
            'season' => 'required|string|max:50',
            'coach_name' => 'nullable|string|max:255',
            'budget_allocation' => 'nullable|numeric|min:0',
            'tactical_style' => 'nullable|string|max:255',
            'playing_philosophy' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $user = Auth::user();
        $club = $user->club;

        $team = Team::create([
            'club_id' => $club->id,
            'name' => $request->name,
            'type' => $request->type,
            'formation' => $request->formation,
            'season' => $request->season,
            'coach_name' => $request->coach_name,
            'budget_allocation' => $request->budget_allocation,
            'tactical_style' => $request->tactical_style,
            'playing_philosophy' => $request->playing_philosophy,
            'status' => $request->status,
            'competition_level' => $request->competition_level ?? 'local',
        ]);

        return redirect()->route('club-management.teams.show', $team)
            ->with('success', 'Team created successfully');
    }

    public function showTeam(Team $team)
    {
        $team->load(['club', 'players']);
        $club = $team->club;
        
        // Get team statistics
        $teamStats = [
            'total_players' => $team->players->count(),
            'starters' => $team->players->where('pivot.role', 'starter')->count(),
            'substitutes' => $team->players->where('pivot.role', 'substitute')->count(),
            'injured' => $team->players->where('health_status', 'injured')->count(),
            'available' => $team->players->where('health_status', 'available')->count(),
        ];

        return view('club-management.teams.show', compact('team', 'club', 'teamStats'));
    }

    public function editTeam(Team $team)
    {
        $user = Auth::user();
        $club = $user->club;
        
        $teamTypes = [
            'first_team' => 'First Team',
            'reserve' => 'Reserve Team',
            'youth' => 'Youth Team',
            'academy' => 'Academy Team',
        ];

        $formations = [
            '4-4-2' => '4-4-2 (Classic)',
            '4-3-3' => '4-3-3 (Attacking)',
            '3-5-2' => '3-5-2 (Wingbacks)',
            '4-2-3-1' => '4-2-3-1 (Modern)',
            '3-4-3' => '3-4-3 (Attacking)',
            '5-3-2' => '5-3-2 (Defensive)',
            '4-1-4-1' => '4-1-4-1 (Balanced)',
        ];

        return view('club-management.teams.edit', compact('team', 'club', 'teamTypes', 'formations'));
    }

    public function updateTeam(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:first_team,reserve,youth,academy',
            'formation' => 'required|string|max:10',
            'season' => 'required|string|max:50',
            'coach_name' => 'nullable|string|max:255',
            'budget_allocation' => 'nullable|numeric|min:0',
            'tactical_style' => 'nullable|string|max:255',
            'playing_philosophy' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $team->update([
            'name' => $request->name,
            'type' => $request->type,
            'formation' => $request->formation,
            'season' => $request->season,
            'coach_name' => $request->coach_name,
            'budget_allocation' => $request->budget_allocation,
            'tactical_style' => $request->tactical_style,
            'playing_philosophy' => $request->playing_philosophy,
            'status' => $request->status,
            'competition_level' => $request->competition_level ?? $team->competition_level,
        ]);

        return redirect()->route('club-management.teams.show', $team)
            ->with('success', 'Team updated successfully');
    }

    public function destroyTeam(Team $team)
    {
        $team->delete();
        return redirect()->route('club-management.teams.index')
            ->with('success', 'Team deleted successfully');
    }

    public function manageTeamPlayers(Team $team)
    {
        $team->load(['club', 'players']);
        $club = $team->club;
        
        // Get available players not in this team
        $availablePlayers = Player::where('club_id', $club->id)
            ->whereDoesntHave('teams', function($query) use ($team) {
                $query->where('team_id', $team->id);
            })
            ->get();

        return view('club-management.teams.manage-players', compact('team', 'club', 'availablePlayers'));
    }

    public function addPlayerToTeam(Request $request, Team $team)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'role' => 'required|in:starter,substitute,reserve',
            'squad_number' => 'nullable|integer|min:1|max:99',
        ]);

        $team->players()->attach($request->player_id, [
            'role' => $request->role,
            'squad_number' => $request->squad_number,
            'joined_date' => now(),
            'status' => 'active',
        ]);

        return redirect()->route('club-management.teams.manage-players', $team)
            ->with('success', 'Player added to team successfully');
    }

    public function removePlayerFromTeam(Team $team, Player $player)
    {
        $team->players()->detach($player->id);
        
        return redirect()->route('club-management.teams.manage-players', $team)
            ->with('success', 'Player removed from team successfully');
    }

    public function createLicense()
    {
        $user = Auth::user();
        $club = $user->club;
        
        if (!$club && !$user->hasRole('admin')) {
            abort(403, 'No club assigned');
        }

        $availablePlayers = $club ? Player::where('club_id', $club->id)->get() : Player::all();
        
        $licenseTypes = [
            'professional' => 'Professional',
            'amateur' => 'Amateur',
            'youth' => 'Youth',
            'international' => 'International',
        ];

        $licenseCategories = [
            'A' => 'Category A - Elite',
            'B' => 'Category B - Professional',
            'C' => 'Category C - Semi-Professional',
            'D' => 'Category D - Amateur',
            'E' => 'Category E - Youth',
        ];

        return view('club-management.licenses.create', compact('club', 'availablePlayers', 'licenseTypes', 'licenseCategories'));
    }

    public function storeLicense(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'license_type' => 'required|in:professional,amateur,youth,international',
            'license_category' => 'required|in:A,B,C,D,E',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'issuing_authority' => 'required|string|max:255',
            'contract_type' => 'required|in:permanent,loan,free_agent',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after:contract_start_date',
            'wage_agreement' => 'nullable|numeric|min:0',
            'release_clause' => 'nullable|numeric|min:0',
            'medical_clearance' => 'required|boolean',
            'fitness_certificate' => 'required|boolean',
            'disciplinary_record' => 'nullable|string',
            'international_clearance' => 'nullable|boolean',
            'work_permit' => 'nullable|boolean',
            'visa_status' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $club = $user->club;
        
        if (!$club && !$user->hasRole('admin')) {
            abort(403, 'No club assigned');
        }

        $player = Player::findOrFail($request->player_id);
        
        // Generate FIFA Connect ID if not exists
        $fifaConnectId = $player->fifa_connect_id;
        if (!$fifaConnectId) {
            $fifaConnectId = 'FIFA' . str_pad($player->id, 6, '0', STR_PAD_LEFT);
            $player->update(['fifa_connect_id' => $fifaConnectId]);
        }

        // Generate license number
        $licenseNumber = 'LIC' . str_pad(\App\Models\PlayerLicense::count() + 1, 6, '0', STR_PAD_LEFT);

        $license = \App\Models\PlayerLicense::create([
            'player_id' => $request->player_id,
            'club_id' => $club ? $club->id : $player->club_id,
            'fifa_connect_id' => $fifaConnectId,
            'license_number' => $licenseNumber,
            'license_type' => $request->license_type,
            'status' => 'pending',
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'issuing_authority' => $request->issuing_authority,
            'license_category' => $request->license_category,
            'contract_type' => $request->contract_type,
            'contract_start_date' => $request->contract_start_date,
            'contract_end_date' => $request->contract_end_date,
            'wage_agreement' => $request->wage_agreement,
            'release_clause' => $request->release_clause,
            'medical_clearance' => $request->medical_clearance,
            'fitness_certificate' => $request->fitness_certificate,
            'disciplinary_record' => $request->disciplinary_record,
            'international_clearance' => $request->international_clearance,
            'work_permit' => $request->work_permit,
            'visa_status' => $request->visa_status,
            'notes' => $request->notes,
            'requested_by' => $user->id,
        ]);

        return redirect()->route('club-management.licenses.show', $license)
            ->with('success', 'License request created successfully');
    }

    public function showLicense(\App\Models\PlayerLicense $license)
    {
        $license->load(['player', 'club']);
        $club = $license->club;
        
        return view('club-management.licenses.show', compact('license', 'club'));
    }

    public function editLicense(\App\Models\PlayerLicense $license)
    {
        $user = Auth::user();
        $club = $user->club;
        
        if (!$club && !$user->hasRole('admin')) {
            abort(403, 'No club assigned');
        }

        $availablePlayers = $club ? Player::where('club_id', $club->id)->get() : Player::all();
        
        $licenseTypes = [
            'professional' => 'Professional',
            'amateur' => 'Amateur',
            'youth' => 'Youth',
            'international' => 'International',
        ];

        $licenseCategories = [
            'A' => 'Category A - Elite',
            'B' => 'Category B - Professional',
            'C' => 'Category C - Semi-Professional',
            'D' => 'Category D - Amateur',
            'E' => 'Category E - Youth',
        ];

        return view('club-management.licenses.edit', compact('license', 'club', 'availablePlayers', 'licenseTypes', 'licenseCategories'));
    }

    public function updateLicense(Request $request, \App\Models\PlayerLicense $license)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'license_type' => 'required|in:professional,amateur,youth,international',
            'license_category' => 'required|in:A,B,C,D,E',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'issuing_authority' => 'required|string|max:255',
            'contract_type' => 'required|in:permanent,loan,free_agent',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after:contract_start_date',
            'wage_agreement' => 'nullable|numeric|min:0',
            'release_clause' => 'nullable|numeric|min:0',
            'medical_clearance' => 'required|boolean',
            'fitness_certificate' => 'required|boolean',
            'disciplinary_record' => 'nullable|string',
            'international_clearance' => 'nullable|boolean',
            'work_permit' => 'nullable|boolean',
            'visa_status' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $license->update([
            'player_id' => $request->player_id,
            'license_type' => $request->license_type,
            'license_category' => $request->license_category,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'issuing_authority' => $request->issuing_authority,
            'contract_type' => $request->contract_type,
            'contract_start_date' => $request->contract_start_date,
            'contract_end_date' => $request->contract_end_date,
            'wage_agreement' => $request->wage_agreement,
            'release_clause' => $request->release_clause,
            'medical_clearance' => $request->medical_clearance,
            'fitness_certificate' => $request->fitness_certificate,
            'disciplinary_record' => $request->disciplinary_record,
            'international_clearance' => $request->international_clearance,
            'work_permit' => $request->work_permit,
            'visa_status' => $request->visa_status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('club-management.licenses.show', $license)
            ->with('success', 'License updated successfully');
    }

    public function destroyLicense(\App\Models\PlayerLicense $license)
    {
        $license->delete();

        return redirect()->route('club-management.licenses.index')
            ->with('success', 'License deleted successfully');
    }

    public function printLicense(\App\Models\PlayerLicense $license)
    {
        $license->load(['player', 'club']);
        $club = $license->club;
        
        return view('club-management.licenses.pdf', compact('license', 'club'));
    }

    public function approveLicense(\App\Models\PlayerLicense $license)
    {
        $user = Auth::user();
        
        $license->update([
            'status' => 'active',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('club-management.licenses.show', $license)
            ->with('success', 'License approved successfully');
    }

    public function rejectLicense(Request $request, \App\Models\PlayerLicense $license)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        
        $license->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('club-management.licenses.show', $license)
            ->with('success', 'License rejected successfully');
    }

    public function renewLicense(\App\Models\PlayerLicense $license)
    {
        $user = Auth::user();
        
        // Create a new license based on the current one
        $newLicense = $license->replicate();
        $newLicense->status = 'pending';
        $newLicense->issue_date = now();
        $newLicense->expiry_date = now()->addYear();
        $newLicense->renewal_date = now();
        $newLicense->approved_by = null;
        $newLicense->approved_at = null;
        $newLicense->rejection_reason = null;
        $newLicense->requested_by = $user->id;
        $newLicense->save();

        return redirect()->route('club-management.licenses.show', $newLicense)
            ->with('success', 'License renewal request created successfully');
    }

    public function suspendLicense(Request $request, \App\Models\PlayerLicense $license)
    {
        $request->validate([
            'suspension_reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        
        $license->update([
            'status' => 'suspended',
            'notes' => $license->notes . "\n\nSUSPENDED: " . $request->suspension_reason . " (by " . $user->name . " on " . now()->format('Y-m-d H:i:s') . ")",
        ]);

        return redirect()->route('club-management.licenses.show', $license)
            ->with('success', 'License suspended successfully');
    }

    public function licenseTemplates()
    {
        $user = Auth::user();
        $club = $user->club;
        
        $templates = collect(); // You would implement LicenseTemplate model if needed
        
        return view('club-management.licenses.templates', compact('club', 'templates'));
    }

    public function createLicenseTemplate()
    {
        $user = Auth::user();
        $club = $user->club;
        
        return view('club-management.licenses.create-template', compact('club'));
    }

    public function storeLicenseTemplate(Request $request)
    {
        // Implementation for storing license templates
        return redirect()->route('club-management.licenses.templates')
            ->with('success', 'License template created successfully');
    }

    public function editLicenseTemplate($template)
    {
        $user = Auth::user();
        $club = $user->club;
        
        return view('club-management.licenses.create-template', compact('club', 'template'));
    }

    public function updateLicenseTemplate(Request $request, $template)
    {
        // Implementation for updating license templates
        return redirect()->route('club-management.licenses.templates')
            ->with('success', 'License template updated successfully');
    }

    public function destroyLicenseTemplate($template)
    {
        // Implementation for deleting license templates
        return redirect()->route('club-management.licenses.templates')
            ->with('success', 'License template deleted successfully');
    }

    public function licenseComplianceReport()
    {
        $user = Auth::user();
        $club = $user->club;
        
        $licenses = collect();
        $complianceStats = [
            'total' => 0,
            'compliant' => 0,
            'non_compliant' => 0,
            'expiring_soon' => 0,
        ];
        
        if ($user->hasRole('admin')) {
            $licenses = \App\Models\PlayerLicense::with(['player', 'club'])->get();
        } elseif ($club) {
            $licenses = \App\Models\PlayerLicense::where('club_id', $club->id)
                ->with(['player', 'club'])
                ->get();
        }
        
        if ($licenses->count() > 0) {
            $complianceStats['total'] = $licenses->count();
            $complianceStats['compliant'] = $licenses->where('status', 'active')->count();
            $complianceStats['non_compliant'] = $licenses->whereIn('status', ['expired', 'suspended', 'rejected'])->count();
            $complianceStats['expiring_soon'] = $licenses->where('status', 'active')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>', now())
                ->count();
        }
        
        return view('club-management.licenses.compliance-report', compact('club', 'licenses', 'complianceStats'));
    }

    public function licenseAuditTrail(\App\Models\PlayerLicense $license)
    {
        $license->load(['player', 'club']);
        $club = $license->club;
        
        // You would implement audit trail logic here
        $auditTrail = collect();
        
        return view('club-management.licenses.audit-trail', compact('license', 'club', 'auditTrail'));
    }

    public function clubPlayerAssignments()
    {
        $user = Auth::user();
        $club = null;
        $assignments = collect();
        $unassignedPlayers = collect();
        $teams = collect();

        if ($user->hasRole('admin')) {
            // Admin can see all assignments
            $assignments = \App\Models\Player::with(['club', 'teams'])
                ->whereNotNull('club_id')
                ->get();
            $unassignedPlayers = \App\Models\Player::whereNull('club_id')->get();
            $teams = \App\Models\Team::with('club')->get();
        } else {
            // Regular users can only see their club's assignments
            $club = $user->club;
            if ($club) {
                $assignments = \App\Models\Player::where('club_id', $club->id)
                    ->with(['club', 'teams'])
                    ->get();
                $unassignedPlayers = \App\Models\Player::whereNull('club_id')->get();
                $teams = \App\Models\Team::where('club_id', $club->id)->get();
            }
        }

        return view('club-management.assignments.index', compact('assignments', 'unassignedPlayers', 'teams', 'club'));
    }

    /**
     * Show the logo upload form for a club
     */
    public function showLogoUpload(Club $club)
    {
        return view('club-management.logo.upload', compact('club'));
    }

    /**
     * Handle logo upload for a club
     */
    public function uploadLogo(Request $request, Club $club)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = 'club-' . $club->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                
                // Store in storage/app/public/clubs/logos
                $path = $file->storeAs('clubs/logos', $filename, 'public');
                
                // Update club logo fields
                $club->update([
                    'logo_url' => asset('storage/' . $path),
                    'logo_path' => '/storage/' . $path
                ]);

                return redirect()->back()->with('success', 'Logo du club mis à jour avec succès !');
            }

            return redirect()->back()->with('error', 'Aucun fichier sélectionné.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'upload du logo : ' . $e->getMessage());
        }
    }
} 