<?php

namespace App\Http\Controllers\Dtn;

use App\Http\Controllers\Controller;
use App\Models\NationalSelection;
use App\Models\NationalSelectionReport;
use App\Services\Dtn\DtnAccess;
use App\Services\Dtn\SelectionWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Espace club — permission club-selections-space.
 * Convocations reçues, états de départ, retours de sélection.
 */
class ClubSelectionController extends Controller
{
    public function __construct(private readonly DtnAccess $access, private readonly SelectionWorkflow $workflow)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        if (!Schema::hasTable('national_selections')) {
            return view('club.selections.index', ['groups' => [], 'notInstalled' => true]);
        }
        $selections = $this->access->scopeClub(NationalSelection::query(), $user)
            ->with(['player', 'association', 'departure', 'returnReport'])->orderByDesc('start_date')->get();
        $by = fn (array $statuses) => $selections->filter(fn ($s) => in_array($s->effectiveStatus(), $statuses, true))->values();

        return view('club.selections.index', [
            'groups' => [
                ['title' => 'États de départ à préparer', 'subtitle' => 'Convocations reçues de la Direction technique nationale.', 'items' => $by([NationalSelection::STATUS_CONVOKED]), 'empty' => 'Aucune convocation à traiter.'],
                ['title' => 'Retours de sélection à lire', 'subtitle' => 'États de retour reçus de la DTN : incidents, performances, risques.', 'items' => $by([NationalSelection::STATUS_RETURN_SENT]), 'empty' => 'Aucun retour à lire.'],
                ['title' => 'Joueurs en sélection', 'subtitle' => 'État de départ envoyé, retour attendu.', 'items' => $by([NationalSelection::STATUS_DEPARTURE_SENT, NationalSelection::STATUS_IN_SELECTION]), 'empty' => 'Aucun joueur en sélection.'],
                ['title' => 'Historique', 'subtitle' => 'Sélections clôturées ou annulées.', 'items' => $by([NationalSelection::STATUS_CLOSED, NationalSelection::STATUS_CANCELLED]), 'empty' => 'Aucune sélection passée.'],
            ],
        ]);
    }

    public function show(Request $request, NationalSelection $selection): View
    {
        $user = $request->user();
        abort_unless($this->access->canViewAsClub($user, $selection), 404);
        $selection->load(['player', 'club', 'association', 'creator', 'departure.author', 'returnReport.author']);
        $canSeeMedical = $this->access->canSeeMedical($user, $selection);

        return view('club.selections.show', [
            'selection' => $selection,
            'departure' => $selection->departure,
            'returnReport' => $selection->returnReport,
            'snapshot' => $selection->departure?->snapshot ?? [],
            'performance' => $this->workflow->performance($selection),
            'departureMedical' => $canSeeMedical ? ($selection->departure?->medical ?? []) : null,
            'returnMedical' => $canSeeMedical ? ($selection->returnReport?->medical ?? []) : null,
            'levels' => NationalSelectionReport::LEVELS,
            'canEditDeparture' => $this->access->canEditDeparture($user, $selection),
            'canEditDepartureMedical' => $this->access->canEditMedical($user, $selection, NationalSelectionReport::DEPARTURE),
            'canAcknowledge' => $this->access->canAcknowledge($user, $selection),
        ]);
    }

    public function saveDeparture(Request $request, NationalSelection $selection): RedirectResponse
    {
        $data = $request->validate($this->workflow->departureRules() + ['action' => ['required', 'in:save,send,refresh']]);
        $this->workflow->saveDeparture($request->user(), $selection, $data, $data['action'] === 'send', $data['action'] === 'refresh');

        return redirect()->route('club.selections.show', $selection)->with('status', match ($data['action']) {
            'send' => 'État de départ envoyé à la Direction technique nationale.',
            'refresh' => 'Données du joueur actualisées.',
            default => 'Brouillon enregistré.',
        });
    }

    public function acknowledge(Request $request, NationalSelection $selection): RedirectResponse
    {
        $this->workflow->acknowledge($request->user(), $selection);

        return redirect()->route('club.selections.show', $selection)->with('status', 'Retour de sélection pris en compte. Sélection clôturée.');
    }
}
