<?php

namespace App\Http\Controllers\Api\V1\Selections;

use App\Models\NationalSelection;
use App\Models\Player;
use App\Services\Dtn\ApiAbilities;
use App\Services\Dtn\DtnAccess;
use App\Services\Dtn\PlayerProfile;
use App\Services\Dtn\SelectionWorkflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * API de l'espace fédération (module DTN) : consulter les fiches joueurs,
 * convoquer, suivre ses sélections, envoyer l'état de retour.
 */
class FederationApiController extends SelectionApiController
{
    public function __construct(DtnAccess $access, SelectionWorkflow $workflow, private readonly PlayerProfile $profiles)
    {
        parent::__construct($access, $workflow);
    }

    /** GET /api/v1/dtn/players?q=&club_id=&position=&per_page= */
    public function players(Request $request): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::DTN_PLAYERS_READ);
        $this->requirePermission($request, DtnAccess::FEDERATION_PERMISSION);
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:80'], 'club_id' => ['nullable', 'integer'], 'position' => ['nullable', 'string', 'max:10'], 'per_page' => ['nullable', 'integer', 'min:5', 'max:100']]);
        $page = $this->profiles->search($filters['q'] ?? null, $filters['club_id'] ?? null, $filters['position'] ?? null, $filters['per_page'] ?? 25);

        return response()->json([
            'data' => collect($page->items())->map(fn ($p) => $this->profiles->summary($p))->values(),
            'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'per_page' => $page->perPage(), 'total' => $page->total()],
        ]);
    }

    /** GET /api/v1/dtn/players/{player} — fiche joueur (aucune donnée médicale). */
    public function player(Request $request, int $player): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::DTN_PLAYERS_READ);
        $this->requirePermission($request, DtnAccess::FEDERATION_PERMISSION);

        return response()->json(['data' => $this->profiles->profile(Player::withoutGlobalScopes()->findOrFail($player))]);
    }

    /** GET /api/v1/dtn/selections?status= */
    public function selections(Request $request): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::DTN_SELECTIONS_READ);
        $this->requirePermission($request, DtnAccess::FEDERATION_PERMISSION);
        $request->validate(['status' => ['nullable', Rule::in(array_keys(NationalSelection::STATUS_LABELS))]]);
        $items = $this->access->scopeFederation(NationalSelection::query(), $request->user())->orderByDesc('start_date')->get()
            ->filter(fn ($s) => !$request->filled('status') || $s->effectiveStatus() === $request->input('status'));

        return response()->json(['data' => $items->map(fn ($s) => $this->workflow->present($s, $request->user(), $this->wantsMedical($request)))->values()]);
    }

    /** GET /api/v1/dtn/selections/{selection} */
    public function selection(Request $request, NationalSelection $selection): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::DTN_SELECTIONS_READ);
        $this->requirePermission($request, DtnAccess::FEDERATION_PERMISSION);
        abort_unless($this->access->canViewAsFederation($request->user(), $selection), 404);

        return response()->json(['data' => $this->workflow->present($selection, $request->user(), $this->wantsMedical($request))]);
    }

    /** POST /api/v1/dtn/selections — convocation */
    public function convoke(Request $request): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::DTN_SELECTIONS_WRITE);
        $this->requirePermission($request, DtnAccess::FEDERATION_PERMISSION);
        $selection = $this->workflow->convoke($request->user(), $request->validate($this->workflow->convocationRules($request->user())));

        return response()->json(['data' => $this->workflow->present($selection->fresh(), $request->user())], 201);
    }

    /** PUT /api/v1/dtn/selections/{selection}/return — corps : champs de l'état de retour, send=true pour envoyer */
    public function submitReturn(Request $request, NationalSelection $selection): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::DTN_SELECTIONS_WRITE);
        $this->requirePermission($request, DtnAccess::FEDERATION_PERMISSION);
        abort_unless($this->access->canViewAsFederation($request->user(), $selection), 404);
        $data = $request->validate($this->workflow->returnRules() + ['send' => ['nullable', 'boolean']]);
        $this->workflow->saveReturn($request->user(), $selection, $data, (bool) ($data['send'] ?? false), $this->medicalWriteAllowed($request));

        return response()->json(['data' => $this->workflow->present($selection->fresh(), $request->user(), $this->wantsMedical($request))]);
    }

    /** POST /api/v1/dtn/selections/{selection}/cancel */
    public function cancel(Request $request, NationalSelection $selection): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::DTN_SELECTIONS_WRITE);
        $this->requirePermission($request, DtnAccess::FEDERATION_PERMISSION);
        abort_unless($this->access->canViewAsFederation($request->user(), $selection), 404);
        $this->workflow->cancel($request->user(), $selection);

        return response()->json(['data' => $this->workflow->present($selection->fresh(), $request->user())]);
    }
}
