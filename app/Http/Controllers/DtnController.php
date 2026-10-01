<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\NationalSelection;
use App\Models\NationalSelectionReport;
use App\Models\Player;
use App\Services\Dtn\DtnAccess;
use App\Services\Dtn\SelectionSnapshot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Outil DTN : partage de données entre le club et la Direction technique
 * nationale pour les joueurs sélectionnés.
 *
 * Cycle : la DTN convoque → le club envoie l'état de départ (pré-rempli à
 * partir de ses données) → le joueur est en sélection → la DTN envoie l'état
 * de retour (incidents, performances, risques, indice de performance) → le
 * club en accuse réception.
 */
class DtnController extends Controller
{
    public function __construct(private readonly DtnAccess $access, private readonly SelectionSnapshot $snapshots)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($this->access->canUseTool($user), 403, 'Accès réservé au staff des clubs et à la Direction technique nationale.');

        // Tables créées au démarrage par fit:deploy : tant qu'elles manquent, l'écran reste vide au lieu d'échouer.
        if (!Schema::hasTable('national_selections')) {
            return view('dtn.index', ['todo' => collect(), 'ongoing' => collect(), 'history' => collect(),
                'canConvoke' => false, 'side' => $this->access->isDtnSide($user) ? 'dtn' : 'club', 'notInstalled' => true]);
        }

        $selections = $this->access->scope(NationalSelection::query(), $user)
            ->with(['player', 'club', 'association', 'departure', 'returnReport'])
            ->orderByDesc('start_date')->get();

        $todo = $selections->filter(fn ($s) => $this->access->canEditDeparture($user, $s)
            || $this->access->canEditReturn($user, $s) || $this->access->canAcknowledge($user, $s));
        $ongoing = $selections->filter(fn ($s) => !$todo->contains($s)
            && !in_array($s->status, [NationalSelection::STATUS_CLOSED, NationalSelection::STATUS_CANCELLED], true));
        $history = $selections->filter(fn ($s) => in_array($s->status, [NationalSelection::STATUS_CLOSED, NationalSelection::STATUS_CANCELLED], true));

        return view('dtn.index', [
            'todo' => $todo->values(),
            'ongoing' => $ongoing->values(),
            'history' => $history->values(),
            'canConvoke' => $this->access->canConvoke($user),
            'side' => $this->access->isDtnSide($user) ? 'dtn' : 'club',
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($this->access->canConvoke($request->user()), 403);

        // Une fédération convoque des joueurs de tous les clubs : la recherche n'est pas limitée
        // à l'organisation de l'utilisateur (action réservée aux rôles DTN par canConvoke).
        return view('dtn.create', [
            'players' => Player::withoutGlobalScopes()->with(['club' => fn ($q) => $q->withoutGlobalScopes()])
                ->whereNotNull('club_id')->orderBy('last_name')->orderBy('first_name')
                ->get(['id', 'first_name', 'last_name', 'club_id', 'position']),
            'associations' => $request->user()->isSystemAdmin() ? Association::query()->orderBy('name')->get(['id', 'name']) : collect(),
            'eventTypes' => NationalSelection::EVENT_TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->access->canConvoke($user), 403);

        $data = $request->validate([
            'player_id' => ['required', 'integer', Rule::exists('players', 'id')],
            'association_id' => [$user->isSystemAdmin() ? 'required' : 'nullable', 'integer', Rule::exists('associations', 'id')],
            'team_label' => ['required', 'string', 'max:120'],
            'event_type' => ['required', Rule::in(array_keys(NationalSelection::EVENT_TYPES))],
            'event_name' => ['required', 'string', 'max:160'],
            'opponent' => ['nullable', 'string', 'max:120'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'convocation_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $player = Player::withoutGlobalScopes()->findOrFail($data['player_id']);
        abort_if($player->club_id === null, 422, 'Le joueur doit être rattaché à un club.');

        $selection = NationalSelection::create(array_merge($data, [
            'club_id' => $player->club_id,
            'association_id' => $user->isSystemAdmin() ? $data['association_id'] : $user->association_id,
            'status' => NationalSelection::STATUS_CONVOKED,
            'created_by' => $user->id,
            'is_demo' => (bool) $player->is_demo,
        ]));
        $selection->reports()->create([
            'direction' => NationalSelectionReport::DEPARTURE,
            'status' => NationalSelectionReport::STATUS_DRAFT,
            'snapshot' => $this->snapshots->forPlayer($player->id),
        ]);

        return redirect()->route('dtn.selections.show', $selection)->with('status', 'Convocation créée : le club doit maintenant préparer l\'état de départ.');
    }

    public function show(Request $request, NationalSelection $selection): View
    {
        $user = $request->user();
        abort_unless($this->access->canView($user, $selection), 404);
        $selection->load(['player', 'club', 'association', 'creator', 'departure.author', 'returnReport.author']);

        $canSeeMedical = $this->access->canSeeMedical($user, $selection);
        $departure = $selection->departure;
        $return = $selection->returnReport;

        return view('dtn.show', [
            'selection' => $selection,
            'departure' => $departure,
            'returnReport' => $return,
            'snapshot' => $departure?->snapshot ?? [],
            'performance' => $return ? $this->snapshots->performanceIndex($return->content ?? [], $departure?->snapshot) : null,
            'canSeeMedical' => $canSeeMedical,
            'departureMedical' => $canSeeMedical ? ($departure?->medical ?? []) : null,
            'returnMedical' => $canSeeMedical ? ($return?->medical ?? []) : null,
            'canEditDeparture' => $this->access->canEditDeparture($user, $selection),
            'canEditDepartureMedical' => $this->access->canEditMedical($user, $selection, NationalSelectionReport::DEPARTURE),
            'canEditReturn' => $this->access->canEditReturn($user, $selection),
            'canEditReturnMedical' => $this->access->canEditMedical($user, $selection, NationalSelectionReport::RETURN),
            'canAcknowledge' => $this->access->canAcknowledge($user, $selection),
            'canCancel' => $this->access->canCancel($user, $selection),
            'levels' => NationalSelectionReport::LEVELS,
            'fitness' => NationalSelectionReport::FITNESS,
        ]);
    }

    public function saveDeparture(Request $request, NationalSelection $selection): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->access->canEditDeparture($user, $selection), 403);

        $data = $request->validate([
            'availability' => ['nullable', Rule::in(['available', 'available_limited', 'unavailable'])],
            'load_recommendation' => ['nullable', 'string', 'max:2000'],
            'vigilance' => ['nullable', 'string', 'max:2000'],
            'technical_notes' => ['nullable', 'string', 'max:2000'],
            'contact' => ['nullable', 'string', 'max:300'],
            'fitness_status' => ['nullable', Rule::in(array_keys(NationalSelectionReport::FITNESS))],
            'medical.current_injuries' => ['nullable', 'string', 'max:2000'],
            'medical.restrictions' => ['nullable', 'string', 'max:2000'],
            'medical.treatments' => ['nullable', 'string', 'max:2000'],
            'medical.aut' => ['nullable', 'string', 'max:1000'],
            'medical.recommendations' => ['nullable', 'string', 'max:2000'],
            'action' => ['required', Rule::in(['save', 'send', 'refresh'])],
        ]);

        $report = $selection->departure ?? $selection->reports()->make(['direction' => NationalSelectionReport::DEPARTURE]);
        $report->content = Arr::only($data, ['availability', 'load_recommendation', 'vigilance', 'technical_notes', 'contact']);
        $report->author_id = $user->id;
        if ($data['action'] === 'refresh' || empty($report->snapshot)) {
            $report->snapshot = $this->snapshots->forPlayer($selection->player_id);
        }
        if ($this->access->canEditMedical($user, $selection, NationalSelectionReport::DEPARTURE)) {
            $report->medical = $data['medical'] ?? [];
            $report->fitness_status = $data['fitness_status'] ?? null;
            $report->medical_author_id = $user->id;
        }
        if ($data['action'] === 'send') {
            $report->status = NationalSelectionReport::STATUS_SENT;
            $report->sent_at = now();
            $selection->update(['status' => NationalSelection::STATUS_DEPARTURE_SENT]);
        }
        $report->save();

        return redirect()->route('dtn.selections.show', $selection)->with('status', match ($data['action']) {
            'send' => 'État de départ envoyé à la Direction technique nationale.',
            'refresh' => 'Données du joueur actualisées.',
            default => 'Brouillon enregistré.',
        });
    }

    public function saveReturn(Request $request, NationalSelection $selection): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->access->canEditReturn($user, $selection), 403);

        $data = $request->validate([
            'matches' => ['nullable', 'integer', 'min:0', 'max:20'],
            'starts' => ['nullable', 'integer', 'min:0', 'max:20'],
            'minutes' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'goals' => ['nullable', 'integer', 'min:0', 'max:50'],
            'assists' => ['nullable', 'integer', 'min:0', 'max:50'],
            'yellow_cards' => ['nullable', 'integer', 'min:0', 'max:20'],
            'red_cards' => ['nullable', 'integer', 'min:0', 'max:5'],
            'avg_rating' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'training_sessions' => ['nullable', 'integer', 'min:0', 'max:60'],
            'incidents' => ['nullable', 'string', 'max:2000'],
            'staff_evaluation' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'evaluation_comment' => ['nullable', 'string', 'max:2000'],
            'fatigue_level' => ['nullable', Rule::in(array_keys(NationalSelectionReport::LEVELS))],
            'injury_risk' => ['nullable', Rule::in(array_keys(NationalSelectionReport::LEVELS))],
            'recommendations' => ['nullable', 'string', 'max:2000'],
            'fitness_status' => ['nullable', Rule::in(array_keys(NationalSelectionReport::FITNESS))],
            'medical.injuries' => ['nullable', 'string', 'max:2000'],
            'medical.treatments_given' => ['nullable', 'string', 'max:2000'],
            'medical.followup' => ['nullable', 'string', 'max:2000'],
            'action' => ['required', Rule::in(['save', 'send'])],
        ]);

        $report = $selection->returnReport ?? $selection->reports()->make(['direction' => NationalSelectionReport::RETURN]);
        $report->content = Arr::except($data, ['medical', 'fitness_status', 'action']);
        $report->author_id = $user->id;
        if ($this->access->canEditMedical($user, $selection, NationalSelectionReport::RETURN)) {
            $report->medical = $data['medical'] ?? [];
            $report->fitness_status = $data['fitness_status'] ?? null;
            $report->medical_author_id = $user->id;
        }
        if ($data['action'] === 'send') {
            $report->status = NationalSelectionReport::STATUS_SENT;
            $report->sent_at = now();
            $selection->update(['status' => NationalSelection::STATUS_RETURN_SENT]);
        }
        $report->save();

        return redirect()->route('dtn.selections.show', $selection)
            ->with('status', $data['action'] === 'send' ? 'État de retour envoyé au club.' : 'Brouillon enregistré.');
    }

    public function acknowledge(Request $request, NationalSelection $selection): RedirectResponse
    {
        $user = $request->user();
        abort_unless($this->access->canAcknowledge($user, $selection), 403);

        $selection->returnReport?->update([
            'status' => NationalSelectionReport::STATUS_ACKNOWLEDGED,
            'acknowledged_by' => $user->id,
            'acknowledged_at' => now(),
        ]);
        $selection->update(['status' => NationalSelection::STATUS_CLOSED]);

        return redirect()->route('dtn.selections.show', $selection)->with('status', 'Retour de sélection pris en compte. Sélection clôturée.');
    }

    public function cancel(Request $request, NationalSelection $selection): RedirectResponse
    {
        abort_unless($this->access->canCancel($request->user(), $selection), 403);
        $selection->update(['status' => NationalSelection::STATUS_CANCELLED]);

        return redirect()->route('dtn.index')->with('status', 'Convocation annulée.');
    }
}
