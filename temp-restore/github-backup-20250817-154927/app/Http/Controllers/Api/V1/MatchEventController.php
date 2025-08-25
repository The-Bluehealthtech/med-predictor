<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MatchEvent\StoreMatchEventRequest;
use App\Http\Requests\Api\V1\MatchEvent\UpdateMatchEventRequest;
use App\Http\Resources\Api\V1\MatchEventResource;
use App\Http\Resources\Api\V1\MatchEventCollection;
use App\Models\MatchEvent;
use App\Models\MatchModel;
use App\Services\AuditTrailService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchEventController extends Controller
{
    use AuthorizesRequests;
    
    protected AuditTrailService $auditTrailService;

    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MatchEvent::class);

        $query = MatchEvent::with(['match', 'player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser']);

        // Filter by match
        if ($request->has('match_id')) {
            $query->where('match_id', $request->match_id);
        }

        // Filter by team
        if ($request->has('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        // Filter by player
        if ($request->has('player_id')) {
            $query->where('player_id', $request->player_id);
        }

        // Filter by event type
        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by period
        if ($request->has('period')) {
            $query->where('period', $request->period);
        }

        // Filter by confirmation status
        if ($request->has('is_confirmed')) {
            $query->where('is_confirmed', $request->boolean('is_confirmed'));
        }

        // Filter by contest status
        if ($request->has('is_contested')) {
            $query->where('is_contested', $request->boolean('is_contested'));
        }

        // Filter by severity
        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('recorded_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('recorded_at', '<=', $request->date_to);
        }

        $events = $query->orderBy('recorded_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json(new MatchEventCollection($events));
    }

    public function store(StoreMatchEventRequest $request): JsonResponse
    {
        $this->authorize('create', MatchEvent::class);

        DB::beginTransaction();
        try {
            $eventData = $request->validated();
            $eventData['recorded_by_user_id'] = auth()->id();
            $eventData['recorded_at'] = now();
            
            // Set the type field based on event_type to satisfy the NOT NULL constraint
            $eventData['type'] = $eventData['event_type'];

            $matchEvent = MatchEvent::create($eventData);

            $this->auditTrailService->log('match_event_created', 'Match event created', $matchEvent, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Match event created successfully',
                'data' => new MatchEventResource($matchEvent->load(['match', 'player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create match event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(MatchEvent $matchEvent): JsonResponse
    {
        $this->authorize('view', $matchEvent);

        return response()->json([
            'data' => new MatchEventResource($matchEvent->load(['match', 'player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser']))
        ]);
    }

    public function update(UpdateMatchEventRequest $request, MatchEvent $matchEvent): JsonResponse
    {
        $this->authorize('update', $matchEvent);

        DB::beginTransaction();
        try {
            $matchEvent->update($request->validated());

            $this->auditTrailService->log('match_event_updated', 'Match event updated', $matchEvent, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Match event updated successfully',
                'data' => new MatchEventResource($matchEvent->load(['match', 'player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update match event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(MatchEvent $matchEvent): JsonResponse
    {
        $this->authorize('delete', $matchEvent);

        DB::beginTransaction();
        try {
            $this->auditTrailService->log('match_event_deleted', 'Match event deleted', $matchEvent, auth()->user());
            
            $matchEvent->delete();

            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete match event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function confirm(MatchEvent $matchEvent): JsonResponse
    {
        $this->authorize('confirm', $matchEvent);

        DB::beginTransaction();
        try {
            $matchEvent->confirm();

            $this->auditTrailService->log('match_event_confirmed', 'Match event confirmed', $matchEvent, auth()->user());

            DB::commit();

            return response()->json([
                'message' => 'Match event confirmed successfully',
                'data' => new MatchEventResource($matchEvent->load(['match', 'player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to confirm match event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function contest(Request $request, MatchEvent $matchEvent): JsonResponse
    {
        $this->authorize('contest', $matchEvent);

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $matchEvent->contest($request->reason);

            $this->auditTrailService->log('match_event_contested', 'Match event contested', $matchEvent, auth()->user(), ['reason' => $request->reason]);

            DB::commit();

            return response()->json([
                'message' => 'Match event contested successfully',
                'data' => new MatchEventResource($matchEvent->load(['match', 'player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to contest match event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function byMatch(MatchModel $match): JsonResponse
    {
        $this->authorize('viewAny', MatchEvent::class);

        $events = MatchEvent::with(['player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser'])
            ->where('match_id', $match->id)
            ->orderBy('minute', 'asc')
            ->orderBy('extra_time_minute', 'asc')
            ->get();

        return response()->json([
            'data' => MatchEventResource::collection($events)
        ]);
    }

    public function byTeam(Request $request, $teamId): JsonResponse
    {
        $this->authorize('viewAny', MatchEvent::class);

        $query = MatchEvent::with(['match', 'player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser'])
            ->where('team_id', $teamId);

        // Additional filters
        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->has('date_from')) {
            $query->whereDate('recorded_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('recorded_at', '<=', $request->date_to);
        }

        $events = $query->orderBy('recorded_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json(new MatchEventCollection($events));
    }

    public function byPlayer(Request $request, $playerId): JsonResponse
    {
        $this->authorize('viewAny', MatchEvent::class);

        $query = MatchEvent::with(['match', 'player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser'])
            ->where('player_id', $playerId);

        // Additional filters
        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->has('date_from')) {
            $query->whereDate('recorded_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('recorded_at', '<=', $request->date_to);
        }

        $events = $query->orderBy('recorded_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json(new MatchEventCollection($events));
    }

    public function statistics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MatchEvent::class);

        $query = MatchEvent::query();

        // Apply filters
        if ($request->has('match_id')) {
            $query->where('match_id', $request->match_id);
        }

        if ($request->has('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->has('player_id')) {
            $query->where('player_id', $request->player_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('recorded_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('recorded_at', '<=', $request->date_to);
        }

        $statistics = [
            'total_events' => $query->count(),
            'goals' => $query->clone()->goals()->count(),
            'assists' => $query->clone()->where('event_type', 'assist')->count(),
            'yellow_cards' => $query->clone()->where('event_type', 'yellow_card')->count(),
            'red_cards' => $query->clone()->where('event_type', 'red_card')->count(),
            'substitutions' => $query->clone()->substitutions()->count(),
            'confirmed_events' => $query->clone()->confirmed()->count(),
            'contested_events' => $query->clone()->contested()->count(),
            'events_by_type' => $query->clone()
                ->selectRaw('event_type, COUNT(*) as count')
                ->groupBy('event_type')
                ->pluck('count', 'event_type')
                ->toArray(),
            'events_by_period' => $query->clone()
                ->selectRaw('period, COUNT(*) as count')
                ->groupBy('period')
                ->pluck('count', 'period')
                ->toArray(),
            'events_by_severity' => $query->clone()
                ->selectRaw('severity, COUNT(*) as count')
                ->groupBy('severity')
                ->pluck('count', 'severity')
                ->toArray()
        ];

        return response()->json([
            'data' => $statistics
        ]);
    }

    public function timeline(Request $request, MatchModel $match): JsonResponse
    {
        $this->authorize('viewAny', MatchEvent::class);

        $events = MatchEvent::with(['player', 'team', 'assistedByPlayer', 'substitutedPlayer'])
            ->where('match_id', $match->id)
            ->orderBy('minute', 'asc')
            ->orderBy('extra_time_minute', 'asc')
            ->get()
            ->groupBy('period');

        $timeline = [];
        foreach ($events as $period => $periodEvents) {
            $timeline[$period] = $periodEvents->map(function ($event) {
                return [
                    'id' => $event->id,
                    'minute' => $event->minute,
                    'extra_time_minute' => $event->extra_time_minute,
                    'display_time' => $event->display_time,
                    'event_type' => $event->event_type,
                    'event_type_label' => $event->event_type_label,
                    'event_type_color' => $event->event_type_color,
                    'description' => $event->getEventDescription(),
                    'player' => $event->player ? [
                        'id' => $event->player->id,
                        'name' => $event->player->name
                    ] : null,
                    'team' => $event->team ? [
                        'id' => $event->team->id,
                        'name' => $event->team->name
                    ] : null,
                    'is_confirmed' => $event->is_confirmed,
                    'is_contested' => $event->is_contested
                ];
            });
        }

        return response()->json([
            'data' => $timeline
        ]);
    }
} 