<?php

namespace App\Http\Controllers;

use App\Models\RefereeReport;
use App\Models\GameMatch;
use App\Models\User;
use App\Models\Team;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RefereeReportController extends Controller
{
    /**
     * Afficher le formulaire de création de rapport
     */
    public function create($matchId)
    {
        $match = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])->find($matchId);
        
        if (!$match) {
            return redirect()->back()->with('error', 'Match non trouvé');
        }

        // Vérifier si l'arbitre est assigné
        $user = Auth::user();
        $isAssigned = $match->officials()->where('user_id', $user->id)->exists();
        
        if (!$isAssigned) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas assigné à ce match');
        }

        // Récupérer les joueurs des équipes
        $homeTeamPlayers = Player::where('team_id', $match->home_team_id)->get();
        $awayTeamPlayers = Player::where('team_id', $match->away_team_id)->get();

        // Vérifier si un rapport existe déjà
        $existingReport = RefereeReport::where('match_id', $matchId)
            ->where('referee_id', $user->id)
            ->first();

        return view('referee.create-report-form', compact('match', 'homeTeamPlayers', 'awayTeamPlayers', 'existingReport'));
    }

    /**
     * Sauvegarder le rapport
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'match_id' => 'required|exists:matches,id',
            'final_score' => 'required|string',
            'half_time_score' => 'nullable|string',
            'extra_time_minutes' => 'nullable|integer|min:0',
            'penalty_shootout' => 'boolean',
            'penalty_shootout_score' => 'nullable|string',
            'weather' => 'nullable|string',
            'pitch_condition' => 'nullable|string',
            'main_referee' => 'required|string',
            'assistant_referee_1' => 'required|string',
            'assistant_referee_2' => 'required|string',
            'fourth_official' => 'required|string',
            'var_referee' => 'nullable|string',
            'avar_referee' => 'nullable|string',
            'goals' => 'nullable|array',
            'yellow_cards' => 'nullable|array',
            'red_cards' => 'nullable|array',
            'substitutions' => 'nullable|array',
            'injuries' => 'nullable|array',
            'disciplinary_incidents' => 'nullable|string',
            'crowd_incidents' => 'nullable|string',
            'safety_issues' => 'nullable|string',
            'general_comments' => 'required|string|min:10',
            'match_quality_assessment' => 'nullable|string',
            'match_rating' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $match = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])->find($request->match_id);
        
        $report = RefereeReport::create([
            'match_id' => $request->match_id,
            'referee_id' => Auth::id(),
            'competition_name' => $match->competition->name ?? 'Unknown',
            'match_date' => $match->match_date,
            'stadium' => $match->venue ?? 'Unknown',
            'weather' => $request->weather,
            'pitch_condition' => $request->pitch_condition,
            'main_referee' => $request->main_referee,
            'assistant_referee_1' => $request->assistant_referee_1,
            'assistant_referee_2' => $request->assistant_referee_2,
            'fourth_official' => $request->fourth_official,
            'var_referee' => $request->var_referee,
            'avar_referee' => $request->avar_referee,
            'final_score' => $request->final_score,
            'half_time_score' => $request->half_time_score,
            'extra_time_minutes' => $request->extra_time_minutes ?? 0,
            'penalty_shootout' => $request->penalty_shootout ?? false,
            'penalty_shootout_score' => $request->penalty_shootout_score,
            'goals' => $request->goals,
            'yellow_cards' => $request->yellow_cards,
            'red_cards' => $request->red_cards,
            'substitutions' => $request->substitutions,
            'injuries' => $request->injuries,
            'disciplinary_incidents' => $request->disciplinary_incidents,
            'crowd_incidents' => $request->crowd_incidents,
            'safety_issues' => $request->safety_issues,
            'general_comments' => $request->general_comments,
            'match_quality_assessment' => $request->match_quality_assessment,
            'match_rating' => $request->match_rating,
            'status' => 'submitted',
            'submitted_at' => now(),
            'electronic_signature' => Auth::user()->name . ' - ' . now()->format('Y-m-d H:i:s'),
        ]);

        return redirect()->route('referee.report.success', $report->id)
            ->with('success', 'Rapport soumis avec succès');
    }

    /**
     * Afficher la page de succès
     */
    public function success($reportId)
    {
        $report = RefereeReport::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->findOrFail($reportId);

        return view('referee.report-success', compact('report'));
    }

    /**
     * Afficher un rapport existant
     */
    public function show($reportId)
    {
        $report = RefereeReport::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->findOrFail($reportId);

        // Vérifier les permissions
        if ($report->referee_id !== Auth::id() && !Auth::user()->hasRole('system_admin')) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        return view('referee.show-report', compact('report'));
    }

    /**
     * Éditer un rapport existant
     */
    public function edit($reportId)
    {
        $report = RefereeReport::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->findOrFail($reportId);

        // Vérifier les permissions
        if ($report->referee_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        // Récupérer les joueurs des équipes
        $homeTeamPlayers = Player::where('team_id', $report->match->home_team_id)->get();
        $awayTeamPlayers = Player::where('team_id', $report->match->away_team_id)->get();

        return view('referee.edit-report', compact('report', 'homeTeamPlayers', 'awayTeamPlayers'));
    }

    /**
     * Mettre à jour un rapport
     */
    public function update(Request $request, $reportId)
    {
        $report = RefereeReport::findOrFail($reportId);

        // Vérifier les permissions
        if ($report->referee_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        $validator = Validator::make($request->all(), [
            'final_score' => 'required|string',
            'general_comments' => 'required|string|min:10',
            'match_rating' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $report->update($request->all());

        return redirect()->route('referee.report.success', $report->id)
            ->with('success', 'Rapport mis à jour avec succès');
    }

    /**
     * API pour récupérer les données d'un match
     */
    public function getMatchData($matchId)
    {
        $match = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])->find($matchId);
        
        if (!$match) {
            return response()->json(['error' => 'Match non trouvé'], 404);
        }

        $homeTeamPlayers = Player::where('team_id', $match->home_team_id)->get();
        $awayTeamPlayers = Player::where('team_id', $match->away_team_id)->get();

        return response()->json([
            'match' => $match,
            'homeTeamPlayers' => $homeTeamPlayers,
            'awayTeamPlayers' => $awayTeamPlayers,
        ]);
    }
}

use Illuminate\Support\Facades\Validator;

class RefereeReportController extends Controller
{
    /**
     * Afficher le formulaire de création de rapport
     */
    public function create($matchId)
    {
        $match = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])->find($matchId);
        
        if (!$match) {
            return redirect()->back()->with('error', 'Match non trouvé');
        }

        // Vérifier si l'arbitre est assigné
        $user = Auth::user();
        $isAssigned = $match->officials()->where('user_id', $user->id)->exists();
        
        if (!$isAssigned) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas assigné à ce match');
        }

        // Récupérer les joueurs des équipes
        $homeTeamPlayers = Player::where('team_id', $match->home_team_id)->get();
        $awayTeamPlayers = Player::where('team_id', $match->away_team_id)->get();

        // Vérifier si un rapport existe déjà
        $existingReport = RefereeReport::where('match_id', $matchId)
            ->where('referee_id', $user->id)
            ->first();

        return view('referee.create-report-form', compact('match', 'homeTeamPlayers', 'awayTeamPlayers', 'existingReport'));
    }

    /**
     * Sauvegarder le rapport
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'match_id' => 'required|exists:matches,id',
            'final_score' => 'required|string',
            'half_time_score' => 'nullable|string',
            'extra_time_minutes' => 'nullable|integer|min:0',
            'penalty_shootout' => 'boolean',
            'penalty_shootout_score' => 'nullable|string',
            'weather' => 'nullable|string',
            'pitch_condition' => 'nullable|string',
            'main_referee' => 'required|string',
            'assistant_referee_1' => 'required|string',
            'assistant_referee_2' => 'required|string',
            'fourth_official' => 'required|string',
            'var_referee' => 'nullable|string',
            'avar_referee' => 'nullable|string',
            'goals' => 'nullable|array',
            'yellow_cards' => 'nullable|array',
            'red_cards' => 'nullable|array',
            'substitutions' => 'nullable|array',
            'injuries' => 'nullable|array',
            'disciplinary_incidents' => 'nullable|string',
            'crowd_incidents' => 'nullable|string',
            'safety_issues' => 'nullable|string',
            'general_comments' => 'required|string|min:10',
            'match_quality_assessment' => 'nullable|string',
            'match_rating' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $match = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])->find($request->match_id);
        
        $report = RefereeReport::create([
            'match_id' => $request->match_id,
            'referee_id' => Auth::id(),
            'competition_name' => $match->competition->name ?? 'Unknown',
            'match_date' => $match->match_date,
            'stadium' => $match->venue ?? 'Unknown',
            'weather' => $request->weather,
            'pitch_condition' => $request->pitch_condition,
            'main_referee' => $request->main_referee,
            'assistant_referee_1' => $request->assistant_referee_1,
            'assistant_referee_2' => $request->assistant_referee_2,
            'fourth_official' => $request->fourth_official,
            'var_referee' => $request->var_referee,
            'avar_referee' => $request->avar_referee,
            'final_score' => $request->final_score,
            'half_time_score' => $request->half_time_score,
            'extra_time_minutes' => $request->extra_time_minutes ?? 0,
            'penalty_shootout' => $request->penalty_shootout ?? false,
            'penalty_shootout_score' => $request->penalty_shootout_score,
            'goals' => $request->goals,
            'yellow_cards' => $request->yellow_cards,
            'red_cards' => $request->red_cards,
            'substitutions' => $request->substitutions,
            'injuries' => $request->injuries,
            'disciplinary_incidents' => $request->disciplinary_incidents,
            'crowd_incidents' => $request->crowd_incidents,
            'safety_issues' => $request->safety_issues,
            'general_comments' => $request->general_comments,
            'match_quality_assessment' => $request->match_quality_assessment,
            'match_rating' => $request->match_rating,
            'status' => 'submitted',
            'submitted_at' => now(),
            'electronic_signature' => Auth::user()->name . ' - ' . now()->format('Y-m-d H:i:s'),
        ]);

        return redirect()->route('referee.report.success', $report->id)
            ->with('success', 'Rapport soumis avec succès');
    }

    /**
     * Afficher la page de succès
     */
    public function success($reportId)
    {
        $report = RefereeReport::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->findOrFail($reportId);

        return view('referee.report-success', compact('report'));
    }

    /**
     * Afficher un rapport existant
     */
    public function show($reportId)
    {
        $report = RefereeReport::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->findOrFail($reportId);

        // Vérifier les permissions
        if ($report->referee_id !== Auth::id() && !Auth::user()->hasRole('system_admin')) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        return view('referee.show-report', compact('report'));
    }

    /**
     * Éditer un rapport existant
     */
    public function edit($reportId)
    {
        $report = RefereeReport::with(['match.homeTeam', 'match.awayTeam', 'match.competition'])
            ->findOrFail($reportId);

        // Vérifier les permissions
        if ($report->referee_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        // Récupérer les joueurs des équipes
        $homeTeamPlayers = Player::where('team_id', $report->match->home_team_id)->get();
        $awayTeamPlayers = Player::where('team_id', $report->match->away_team_id)->get();

        return view('referee.edit-report', compact('report', 'homeTeamPlayers', 'awayTeamPlayers'));
    }

    /**
     * Mettre à jour un rapport
     */
    public function update(Request $request, $reportId)
    {
        $report = RefereeReport::findOrFail($reportId);

        // Vérifier les permissions
        if ($report->referee_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        $validator = Validator::make($request->all(), [
            'final_score' => 'required|string',
            'general_comments' => 'required|string|min:10',
            'match_rating' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $report->update($request->all());

        return redirect()->route('referee.report.success', $report->id)
            ->with('success', 'Rapport mis à jour avec succès');
    }

    /**
     * API pour récupérer les données d'un match
     */
    public function getMatchData($matchId)
    {
        $match = GameMatch::with(['homeTeam', 'awayTeam', 'competition'])->find($matchId);
        
        if (!$match) {
            return response()->json(['error' => 'Match non trouvé'], 404);
        }

        $homeTeamPlayers = Player::where('team_id', $match->home_team_id)->get();
        $awayTeamPlayers = Player::where('team_id', $match->away_team_id)->get();

        return response()->json([
            'match' => $match,
            'homeTeamPlayers' => $homeTeamPlayers,
            'awayTeamPlayers' => $awayTeamPlayers,
        ]);
    }
}
