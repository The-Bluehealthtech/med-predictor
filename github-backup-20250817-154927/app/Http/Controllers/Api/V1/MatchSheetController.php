<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMatchSheetRequest;
use App\Http\Requests\Api\V1\UpdateMatchSheetRequest;
use App\Http\Resources\Api\V1\MatchSheetResource;
use App\Http\Resources\Api\V1\MatchSheetCollection;
use App\Models\MatchSheet;
use App\Services\AuditTrailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MatchSheetController extends Controller
{
    use AuthorizesRequests;

    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MatchSheet::class);

        $query = MatchSheet::with([
            'match.homeTeam', 'match.awayTeam', 'match.competition',
            'referee', 'mainReferee', 'assistantReferee1', 'assistantReferee2',
            'fourthOfficial', 'varReferee', 'varAssistant', 'matchObserver',
            'validator', 'rejector', 'faValidator', 'assignedReferee'
        ]);

        // Apply filters
        if ($request->has('match_id')) {
            $query->where('match_id', $request->match_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->has('match_status')) {
            $query->where('match_status', $request->match_status);
        }

        if ($request->has('referee_id')) {
            $query->where('referee_id', $request->referee_id);
        }

        if ($request->has('assigned_referee_id')) {
            $query->where('assigned_referee_id', $request->assigned_referee_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('kickoff_time', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('kickoff_time', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('match_number', 'like', "%{$search}%")
                  ->orWhere('stadium_venue', 'like', "%{$search}%")
                  ->orWhereHas('match.homeTeam', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('match.awayTeam', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // If the user is a referee, only show match sheets assigned to them
        if (auth()->user()->hasRole('referee')) {
            $query->where('referee_id', auth()->id());
        }

        // If the user is a team official, only show match sheets for their club's teams
        if (auth()->user()->hasRole('team_official') && auth()->user()->club_id) {
            $query->whereHas('match', function ($q) {
                $q->whereHas('homeTeam', function ($q2) {
                    $q2->where('club_id', auth()->user()->club_id);
                })->orWhereHas('awayTeam', function ($q2) {
                    $q2->where('club_id', auth()->user()->club_id);
                });
            });
        }

        // Apply sorting
        $sortField = $request->get('sort_by', 'kickoff_time');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $matchSheets = $query->paginate($request->get('per_page', 15));

        return response()->json(new MatchSheetCollection($matchSheets));
    }

    public function store(StoreMatchSheetRequest $request): JsonResponse
    {
        $this->authorize('create', MatchSheet::class);

        DB::beginTransaction();
        try {
            $matchSheet = MatchSheet::create($request->validated());

            $this->auditTrailService->log('match_sheet_created', 'Match sheet created', $matchSheet, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Match sheet created successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition',
                    'referee', 'mainReferee', 'assistantReferee1', 'assistantReferee2',
                    'fourthOfficial', 'varReferee', 'varAssistant', 'matchObserver'
                ]))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create match sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('view', $matchSheet);

        return response()->json([
            'data' => new MatchSheetResource($matchSheet->load([
                'match.homeTeam', 'match.awayTeam', 'match.competition',
                'referee', 'mainReferee', 'assistantReferee1', 'assistantReferee2',
                'fourthOfficial', 'varReferee', 'varAssistant', 'matchObserver',
                'validator', 'rejector', 'faValidator', 'assignedReferee',
                'events'
            ]))
        ]);
    }

    public function update(UpdateMatchSheetRequest $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('update', $matchSheet);

        DB::beginTransaction();
        try {
            $matchSheet->update($request->validated());

            $this->auditTrailService->log('match_sheet_updated', 'Match sheet updated', $matchSheet, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Match sheet updated successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition',
                    'referee', 'mainReferee', 'assistantReferee1', 'assistantReferee2',
                    'fourthOfficial', 'varReferee', 'varAssistant', 'matchObserver'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update match sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('delete', $matchSheet);

        DB::beginTransaction();
        try {
            $this->auditTrailService->log('match_sheet_deleted', 'Match sheet deleted', $matchSheet, auth()->user());
            
            $matchSheet->delete();

            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete match sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submit(MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('submit', $matchSheet);

        DB::beginTransaction();
        try {
            $matchSheet->submit();

            $this->auditTrailService->log('match_sheet_submitted', 'Match sheet submitted', $matchSheet, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Match sheet submitted successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to submit match sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function validate(Request $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('validate', $matchSheet);

        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $matchSheet->validate(auth()->user(), $request->notes);

            $this->auditTrailService->log('match_sheet_validated', 'Match sheet validated', $matchSheet, auth()->user(), [
                'notes' => $request->notes
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Match sheet validated successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition',
                    'validator'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to validate match sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('reject', $matchSheet);

        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $matchSheet->reject(auth()->user(), $request->reason);

            $this->auditTrailService->log('match_sheet_rejected', 'Match sheet rejected', $matchSheet, auth()->user(), [
                'reason' => $request->reason
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Match sheet rejected successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition',
                    'rejector'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to reject match sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignReferee(Request $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('assignReferee', $matchSheet);

        $request->validate([
            'referee_id' => 'required|exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            $referee = \App\Models\User::findOrFail($request->referee_id);
            $success = $matchSheet->assignReferee($referee, auth()->user());

            if (!$success) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Failed to assign referee - insufficient permissions or invalid state',
                ], 422);
            }

            $this->auditTrailService->log('referee_assigned', 'Referee assigned to match sheet', $matchSheet, auth()->user(), [
                'referee_id' => $referee->id,
                'referee_name' => $referee->name
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Referee assigned successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition',
                    'assignedReferee'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to assign referee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function signByTeam(Request $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('signByTeam', $matchSheet);

        $request->validate([
            'team_type' => 'required|in:home,away',
            'signature' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $matchSheet->signByTeam($request->team_type, $request->signature);

            $this->auditTrailService->log('team_signed', 'Team signed match sheet', $matchSheet, auth()->user(), [
                'team_type' => $request->team_type
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Team signature recorded successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to record team signature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function signByReferee(Request $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('signByReferee', $matchSheet);

        $request->validate([
            'signature' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $matchSheet->signByReferee($request->signature);

            $this->auditTrailService->log('referee_signed', 'Referee signed match sheet', $matchSheet, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Referee signature recorded successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to record referee signature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function signByObserver(Request $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('signByObserver', $matchSheet);

        $request->validate([
            'signature' => 'required|string|max:255',
            'comments' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $matchSheet->signByObserver($request->signature, $request->comments);

            $this->auditTrailService->log('observer_signed', 'Observer signed match sheet', $matchSheet, auth()->user(), [
                'comments' => $request->comments
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Observer signature recorded successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition',
                    'matchObserver'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to record observer signature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function signLineup(Request $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('signLineup', $matchSheet);

        $request->validate([
            'team_type' => 'required|in:home,away',
            'signature' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $matchSheet->signLineup($request->team_type, auth()->user(), $request->signature);

            $this->auditTrailService->log('lineup_signed', 'Lineup signed', $matchSheet, auth()->user(), [
                'team_type' => $request->team_type
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Lineup signature recorded successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to record lineup signature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function signPostMatch(Request $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('signPostMatch', $matchSheet);

        $request->validate([
            'team_type' => 'required|in:home,away',
            'signature' => 'required|string|max:255',
            'comments' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $matchSheet->signPostMatch($request->team_type, auth()->user(), $request->signature, $request->comments);

            $this->auditTrailService->log('post_match_signed', 'Post match signed', $matchSheet, auth()->user(), [
                'team_type' => $request->team_type,
                'comments' => $request->comments
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Post match signature recorded successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to record post match signature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function faValidate(Request $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('faValidate', $matchSheet);

        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $matchSheet->faValidate(auth()->user(), $request->notes);

            $this->auditTrailService->log('fa_validated', 'FA validation completed', $matchSheet, auth()->user(), [
                'notes' => $request->notes
            ]);

            DB::commit();

            return response()->json([
                'message' => 'FA validation completed successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition',
                    'faValidator'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to complete FA validation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function lockLineups(MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('lockLineups', $matchSheet);

        DB::beginTransaction();
        try {
            $matchSheet->lockLineups();

            $this->auditTrailService->log('lineups_locked', 'Lineups locked', $matchSheet, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Lineups locked successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to lock lineups',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function unlockLineups(MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('unlockLineups', $matchSheet);

        DB::beginTransaction();
        try {
            $matchSheet->unlockLineups();

            $this->auditTrailService->log('lineups_unlocked', 'Lineups unlocked', $matchSheet, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Lineups unlocked successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to unlock lineups',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function lockMatchEvents(MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('lockMatchEvents', $matchSheet);

        DB::beginTransaction();
        try {
            $matchSheet->lockMatchEvents();

            $this->auditTrailService->log('match_events_locked', 'Match events locked', $matchSheet, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Match events locked successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to lock match events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function unlockMatchEvents(MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('unlockMatchEvents', $matchSheet);

        DB::beginTransaction();
        try {
            $matchSheet->unlockMatchEvents();

            $this->auditTrailService->log('match_events_unlocked', 'Match events unlocked', $matchSheet, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Match events unlocked successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to unlock match events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function transitionStage(Request $request, MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('transitionStage', $matchSheet);

        $request->validate([
            'new_stage' => 'required|string|in:in_progress,before_game_signed,after_game_signed,fa_validated'
        ]);

        DB::beginTransaction();
        try {
            $matchSheet->transitionToStage($request->new_stage, auth()->user());

            $this->auditTrailService->log('stage_transitioned', 'Stage transitioned', $matchSheet, auth()->user(), [
                'new_stage' => $request->new_stage
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Stage transitioned successfully',
                'data' => new MatchSheetResource($matchSheet->load([
                    'match.homeTeam', 'match.awayTeam', 'match.competition'
                ]))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to transition stage',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStageProgress(MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('view', $matchSheet);

        return response()->json([
            'data' => $matchSheet->getStageProgress()
        ]);
    }

    public function getStatistics(MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('view', $matchSheet);

        $statistics = [
            'total_goals' => $matchSheet->getTotalGoals(),
            'goal_difference' => $matchSheet->getGoalDifference(),
            'winner' => $matchSheet->getWinner(),
            'match_statistics' => $matchSheet->match_statistics,
            'var_decisions' => $matchSheet->var_decisions,
            'penalty_shootout_data' => $matchSheet->penalty_shootout_data
        ];

        return response()->json([
            'data' => $statistics
        ]);
    }

    public function export(MatchSheet $matchSheet): JsonResponse
    {
        $this->authorize('export', $matchSheet);

        return response()->json([
            'data' => $matchSheet->toJsonExport()
        ]);
    }
} 