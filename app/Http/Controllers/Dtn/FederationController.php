<?php

namespace App\Http\Controllers\Dtn;

use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Models\Club;
use App\Models\NationalSelection;
use App\Models\NationalSelectionReport;
use App\Models\Player;
use App\Services\Dtn\DtnAccess;
use App\Services\Dtn\PlayerProfile;
use App\Services\Dtn\SelectionWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Espace fédération (Direction technique nationale) — permission dtn-federation-space.
 * Fiches joueurs, convocations, états de retour.
 */
class FederationController extends Controller
{
    public function __construct(
        private readonly DtnAccess $access,
        private readonly SelectionWorkflow $workflow,
        private readonly PlayerProfile $profiles,
    ) {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        if (!Schema::hasTable('national_selections')) {
            return view('dtn.federation.index', ['groups' => [], 'notInstalled' => true, 'canConvoke' => false]);
        }
        $selections = $this->access->scopeFederation(NationalSelection::query(), $user)
            ->with(['player', 'club', 'departure', 'returnReport'])->orderByDesc('start_date')->get();

        $by = fn (array $statuses) => $selections->filter(fn ($s) => in_array($s->effectiveStatus(), $statuses, true))->values();

        return view('dtn.federation.index', [
            'groups' => [
                ['title' => 'États de retour à rédiger', 'subtitle' => 'Joueurs en sélection ou dont l\'état de départ a été reçu.', 'items' => $by([NationalSelection::STATUS_DEPARTURE_SENT, NationalSelection::STATUS_IN_SELECTION]), 'empty' => 'Aucun état de retour à rédiger.'],
                ['title' => 'En attente du club', 'subtitle' => 'Convocations envoyées : le club prépare l\'état de départ.', 'items' => $by([NationalSelection::STATUS_CONVOKED]), 'empty' => 'Aucune convocation en attente.'],
                ['title' => 'Historique', 'subtitle' => 'Retours envoyés, sélections clôturées ou annulées.', 'items' => $by([NationalSelection::STATUS_RETURN_SENT, NationalSelection::STATUS_CLOSED, NationalSelection::STATUS_CANCELLED]), 'empty' => 'Aucune sélection passée.'],
            ],
            'canConvoke' => $this->access->canConvoke($user),
        ]);
    }

    public function players(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:80'], 'club_id' => ['nullable', 'integer'], 'position' => ['nullable', 'string', 'max:10']]);

        return view('dtn.federation.players', [
            'players' => $this->profiles->search($filters['q'] ?? null, $filters['club_id'] ?? null, $filters['position'] ?? null)->withQueryString(),
            'clubs' => Club::withoutGlobalScopes()->whereIn('id', Player::withoutGlobalScopes()->whereNotNull('club_id')->select('club_id'))->orderBy('name')->get(['id', 'name']),
            'positions' => Player::withoutGlobalScopes()->whereNotNull('position')->distinct()->orderBy('position')->pluck('position'),
            'filters' => $filters,
            'canConvoke' => $this->access->canConvoke($request->user()),
        ]);
    }

    public function player(Request $request, int $player): View
    {
        $model = Player::withoutGlobalScopes()->findOrFail($player);

        return view('dtn.federation.player', [
            'profile' => $this->profiles->profile($model),
            'canConvoke' => $this->access->canConvoke($request->user()),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($this->access->canConvoke($request->user()), 403);
        $playerId = $request->integer('player_id') ?: null;

        return view('dtn.federation.create', [
            'player' => $playerId ? Player::withoutGlobalScopes()->with(['club' => fn ($q) => $q->withoutGlobalScopes()])->find($playerId) : null,
            'associations' => $request->user()->isSystemAdmin() ? Association::query()->orderBy('name')->get(['id', 'name']) : collect(),
            'eventTypes' => NationalSelection::EVENT_TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $selection = $this->workflow->convoke($user, $request->validate($this->workflow->convocationRules($user)));

        return redirect()->route('dtn.selections.show', $selection)->with('status', 'Convocation envoyée : le club doit maintenant préparer l\'état de départ.');
    }

    public function show(Request $request, NationalSelection $selection): View
    {
        $user = $request->user();
        abort_unless($this->access->canViewAsFederation($user, $selection), 404);

        return view('dtn.federation.show', $this->viewData($request, $selection) + [
            'canEditReturn' => $this->access->canEditReturn($user, $selection),
            'canEditReturnMedical' => $this->access->canEditMedical($user, $selection, NationalSelectionReport::RETURN),
            'canCancel' => $this->access->canCancel($user, $selection),
        ]);
    }

    public function saveReturn(Request $request, NationalSelection $selection): RedirectResponse
    {
        $data = $request->validate($this->workflow->returnRules() + ['action' => ['required', 'in:save,send']]);
        $this->workflow->saveReturn($request->user(), $selection, $data, $data['action'] === 'send');

        return redirect()->route('dtn.selections.show', $selection)
            ->with('status', $data['action'] === 'send' ? 'État de retour envoyé au club.' : 'Brouillon enregistré.');
    }

    public function cancel(Request $request, NationalSelection $selection): RedirectResponse
    {
        $this->workflow->cancel($request->user(), $selection);

        return redirect()->route('dtn.index')->with('status', 'Convocation annulée.');
    }

    private function viewData(Request $request, NationalSelection $selection): array
    {
        $selection->load(['player', 'club', 'association', 'creator', 'departure.author', 'returnReport.author']);
        $canSeeMedical = $this->access->canSeeMedical($request->user(), $selection);
        // L'état de départ n'est visible par la fédération qu'une fois envoyé par le club (jamais le brouillon).
        $departure = $selection->departure?->isSent() ? $selection->departure : null;

        return [
            'selection' => $selection,
            'departure' => $departure,
            'returnReport' => $selection->returnReport,
            'snapshot' => $departure?->snapshot ?? [],
            'performance' => $this->workflow->performance($selection),
            'departureMedical' => $canSeeMedical && $departure ? ($departure->medical ?? []) : null,
            'returnMedical' => $canSeeMedical ? ($selection->returnReport?->medical ?? []) : null,
            'levels' => NationalSelectionReport::LEVELS,
        ];
    }
}
