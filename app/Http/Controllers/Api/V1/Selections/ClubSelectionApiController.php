<?php

namespace App\Http\Controllers\Api\V1\Selections;

use App\Models\NationalSelection;
use App\Services\Dtn\ApiAbilities;
use App\Services\Dtn\DtnAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * API de l'espace club : recevoir les convocations, envoyer l'état de départ,
 * recevoir l'état de retour de sélection et en accuser réception.
 */
class ClubSelectionApiController extends SelectionApiController
{
    /** GET /api/v1/club/selections?status= */
    public function selections(Request $request): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::CLUB_SELECTIONS_READ);
        $this->requirePermission($request, DtnAccess::CLUB_PERMISSION);
        $request->validate(['status' => ['nullable', Rule::in(array_keys(NationalSelection::STATUS_LABELS))]]);
        $items = $this->access->scopeClub(NationalSelection::query(), $request->user())->orderByDesc('start_date')->get()
            ->filter(fn ($s) => !$request->filled('status') || $s->effectiveStatus() === $request->input('status'));

        return response()->json(['data' => $items->map(fn ($s) => $this->workflow->present($s, $request->user(), $this->wantsMedical($request)))->values()]);
    }

    /** GET /api/v1/club/selections/{selection} — convocation, état de départ et état de retour */
    public function selection(Request $request, NationalSelection $selection): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::CLUB_SELECTIONS_READ);
        $this->requirePermission($request, DtnAccess::CLUB_PERMISSION);
        abort_unless($this->access->canViewAsClub($request->user(), $selection), 404);

        return response()->json(['data' => $this->workflow->present($selection, $request->user(), $this->wantsMedical($request))]);
    }

    /** PUT /api/v1/club/selections/{selection}/departure — corps : champs de l'état de départ, send=true pour envoyer */
    public function submitDeparture(Request $request, NationalSelection $selection): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::CLUB_SELECTIONS_WRITE);
        $this->requirePermission($request, DtnAccess::CLUB_PERMISSION);
        abort_unless($this->access->canViewAsClub($request->user(), $selection), 404);
        $data = $request->validate($this->workflow->departureRules() + ['send' => ['nullable', 'boolean'], 'refresh_data' => ['nullable', 'boolean']]);
        $this->workflow->saveDeparture($request->user(), $selection, $data, (bool) ($data['send'] ?? false), (bool) ($data['refresh_data'] ?? false), $this->medicalWriteAllowed($request));

        return response()->json(['data' => $this->workflow->present($selection->fresh(), $request->user(), $this->wantsMedical($request))]);
    }

    /** POST /api/v1/club/selections/{selection}/acknowledge */
    public function acknowledge(Request $request, NationalSelection $selection): JsonResponse
    {
        $this->requireAbility($request, ApiAbilities::CLUB_SELECTIONS_WRITE);
        $this->requirePermission($request, DtnAccess::CLUB_PERMISSION);
        abort_unless($this->access->canViewAsClub($request->user(), $selection), 404);
        $this->workflow->acknowledge($request->user(), $selection);

        return response()->json(['data' => $this->workflow->present($selection->fresh(), $request->user())]);
    }
}
