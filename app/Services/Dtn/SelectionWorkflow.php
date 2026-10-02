<?php

namespace App\Services\Dtn;

use App\Models\NationalSelection;
use App\Models\NationalSelectionReport;
use App\Models\Player;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Opérations des sélections nationales, communes à l'interface et à l'API :
 * chaque opération revérifie les droits (DtnAccess), quel que soit l'appelant.
 */
final class SelectionWorkflow
{
    public const DEPARTURE_FIELDS = ['availability', 'load_recommendation', 'vigilance', 'technical_notes', 'contact'];
    public const DEPARTURE_MEDICAL_FIELDS = ['current_injuries', 'restrictions', 'treatments', 'aut', 'recommendations'];
    public const RETURN_FIELDS = ['matches', 'starts', 'minutes', 'goals', 'assists', 'yellow_cards', 'red_cards', 'avg_rating',
        'training_sessions', 'incidents', 'staff_evaluation', 'evaluation_comment', 'fatigue_level', 'injury_risk', 'recommendations'];
    public const RETURN_MEDICAL_FIELDS = ['injuries', 'treatments_given', 'followup'];
    public const AVAILABILITY = ['available' => 'Disponible', 'available_limited' => 'Disponible avec gestion de la charge', 'unavailable' => 'Indisponible'];

    public function __construct(private readonly DtnAccess $access, private readonly SelectionSnapshot $snapshots)
    {
    }

    public function convocationRules(User $user): array
    {
        return [
            'player_id' => ['required', 'integer', Rule::exists('players', 'id')],
            'association_id' => [$user->isSystemAdmin() ? 'required' : 'nullable', 'integer', Rule::exists('associations', 'id')],
            'team_label' => ['required', 'string', 'max:120'],
            'event_type' => ['required', Rule::in(array_keys(NationalSelection::EVENT_TYPES))],
            'event_name' => ['required', 'string', 'max:160'],
            'opponent' => ['nullable', 'string', 'max:120'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'convocation_note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function departureRules(): array
    {
        return [
            'availability' => ['nullable', Rule::in(array_keys(self::AVAILABILITY))],
            'load_recommendation' => ['nullable', 'string', 'max:2000'],
            'vigilance' => ['nullable', 'string', 'max:2000'],
            'technical_notes' => ['nullable', 'string', 'max:2000'],
            'contact' => ['nullable', 'string', 'max:300'],
            'fitness_status' => ['nullable', Rule::in(array_keys(NationalSelectionReport::FITNESS))],
            'medical' => ['nullable', 'array'],
            'medical.*' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function returnRules(): array
    {
        $count = fn ($max) => ['nullable', 'integer', 'min:0', "max:{$max}"];

        return [
            'matches' => $count(20), 'starts' => $count(20), 'minutes' => $count(2000), 'goals' => $count(50), 'assists' => $count(50),
            'yellow_cards' => $count(20), 'red_cards' => $count(5), 'training_sessions' => $count(60),
            'avg_rating' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'staff_evaluation' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'incidents' => ['nullable', 'string', 'max:2000'],
            'evaluation_comment' => ['nullable', 'string', 'max:2000'],
            'fatigue_level' => ['nullable', Rule::in(array_keys(NationalSelectionReport::LEVELS))],
            'injury_risk' => ['nullable', Rule::in(array_keys(NationalSelectionReport::LEVELS))],
            'recommendations' => ['nullable', 'string', 'max:2000'],
            'fitness_status' => ['nullable', Rule::in(array_keys(NationalSelectionReport::FITNESS))],
            'medical' => ['nullable', 'array'],
            'medical.*' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function convoke(User $user, array $data): NationalSelection
    {
        $this->authorize($this->access->canConvoke($user));
        $player = Player::withoutGlobalScopes()->findOrFail($data['player_id']);
        abort_if($player->club_id === null, 422, 'Le joueur doit être rattaché à un club.');

        return DB::transaction(function () use ($user, $data, $player) {
            $selection = NationalSelection::create(array_merge(Arr::only($data, ['team_label', 'event_type', 'event_name', 'opponent', 'start_date', 'end_date', 'convocation_note']), [
                'player_id' => $player->id,
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

            return $selection;
        });
    }

    /** @param bool $withMedical faux pour un appel d'API sans le droit selections:medical */
    public function saveDeparture(User $user, NationalSelection $selection, array $data, bool $send, bool $refresh = false, bool $withMedical = true): NationalSelectionReport
    {
        $this->authorize($this->access->canEditDeparture($user, $selection));

        $report = $selection->departure ?? $selection->reports()->make(['direction' => NationalSelectionReport::DEPARTURE]);
        $report->content = array_merge($report->content ?? [], Arr::only($data, self::DEPARTURE_FIELDS));
        $report->author_id = $user->id;
        if ($refresh || empty($report->snapshot)) {
            $report->snapshot = $this->snapshots->forPlayer($selection->player_id);
        }
        if ($withMedical && $this->access->canEditMedical($user, $selection, NationalSelectionReport::DEPARTURE)) {
            if (array_key_exists('medical', $data)) {
                $report->medical = Arr::only($data['medical'] ?? [], self::DEPARTURE_MEDICAL_FIELDS);
            }
            if (array_key_exists('fitness_status', $data)) {
                $report->fitness_status = $data['fitness_status'];
            }
            $report->medical_author_id = $user->id;
        }
        if ($send) {
            $report->status = NationalSelectionReport::STATUS_SENT;
            $report->sent_at = now();
            $selection->update(['status' => NationalSelection::STATUS_DEPARTURE_SENT]);
        }
        $report->save();

        return $report;
    }

    public function saveReturn(User $user, NationalSelection $selection, array $data, bool $send, bool $withMedical = true): NationalSelectionReport
    {
        $this->authorize($this->access->canEditReturn($user, $selection));

        $report = $selection->returnReport ?? $selection->reports()->make(['direction' => NationalSelectionReport::RETURN]);
        $report->content = array_merge($report->content ?? [], Arr::only($data, self::RETURN_FIELDS));
        $report->author_id = $user->id;
        if ($withMedical && $this->access->canEditMedical($user, $selection, NationalSelectionReport::RETURN)) {
            if (array_key_exists('medical', $data)) {
                $report->medical = Arr::only($data['medical'] ?? [], self::RETURN_MEDICAL_FIELDS);
            }
            if (array_key_exists('fitness_status', $data)) {
                $report->fitness_status = $data['fitness_status'];
            }
            $report->medical_author_id = $user->id;
        }
        if ($send) {
            $report->status = NationalSelectionReport::STATUS_SENT;
            $report->sent_at = now();
            $selection->update(['status' => NationalSelection::STATUS_RETURN_SENT]);
        }
        $report->save();

        return $report;
    }

    public function acknowledge(User $user, NationalSelection $selection): void
    {
        $this->authorize($this->access->canAcknowledge($user, $selection));
        $selection->returnReport?->update([
            'status' => NationalSelectionReport::STATUS_ACKNOWLEDGED, 'acknowledged_by' => $user->id, 'acknowledged_at' => now(),
        ]);
        $selection->update(['status' => NationalSelection::STATUS_CLOSED]);
    }

    public function cancel(User $user, NationalSelection $selection): void
    {
        $this->authorize($this->access->canCancel($user, $selection));
        $selection->update(['status' => NationalSelection::STATUS_CANCELLED]);
    }

    public function performance(NationalSelection $selection): ?array
    {
        $return = $selection->returnReport;

        return $return ? $this->snapshots->performanceIndex($return->content ?? [], $selection->departure?->snapshot) : null;
    }

    /**
     * Représentation d'une sélection (interface et API). La partie médicale n'est
     * incluse que si $includeMedical est vrai ET que l'utilisateur y a droit.
     */
    public function present(NationalSelection $selection, User $user, bool $includeMedical = false): array
    {
        $selection->loadMissing(['player', 'club', 'association', 'departure', 'returnReport']);
        $medical = $includeMedical && $this->access->canSeeMedical($user, $selection);
        $report = function (?NationalSelectionReport $r, bool $withSnapshot) use ($medical) {
            if (!$r) {
                return null;
            }

            return array_filter([
                'status' => $r->status,
                'fitness_status' => $r->fitness_status,
                'fitness_label' => $r->fitnessLabel(),
                'sent_at' => $r->sent_at?->toIso8601String(),
                'acknowledged_at' => $r->acknowledged_at?->toIso8601String(),
                'content' => $r->content ?? [],
                'prefilled_data' => $withSnapshot ? ($r->snapshot ?? []) : null,
                'medical' => $medical ? ($r->medical ?? []) : null,
            ], fn ($v) => $v !== null);
        };

        return [
            'id' => $selection->id,
            'status' => $selection->effectiveStatus(),
            'status_label' => NationalSelection::STATUS_LABELS[$selection->effectiveStatus()] ?? $selection->status,
            'player' => [
                'id' => $selection->player_id,
                'name' => trim(($selection->player->first_name ?? '') . ' ' . ($selection->player->last_name ?? '')),
            ],
            'club' => ['id' => $selection->club_id, 'name' => $selection->club->name ?? null],
            'federation' => ['id' => $selection->association_id, 'name' => $selection->association->name ?? null],
            'team_label' => $selection->team_label,
            'event' => ['type' => $selection->event_type, 'type_label' => $selection->eventTypeLabel(), 'name' => $selection->event_name, 'opponent' => $selection->opponent],
            'start_date' => $selection->start_date?->toDateString(),
            'end_date' => $selection->end_date?->toDateString(),
            'convocation_note' => $selection->convocation_note,
            'is_demo' => (bool) $selection->is_demo,
            'departure_report' => $report($selection->departure, true),
            'return_report' => $report($selection->returnReport, false),
            'performance' => $this->performance($selection),
            'medical_included' => $medical,
        ];
    }

    private function authorize(bool $allowed): void
    {
        if (!$allowed) {
            throw new AuthorizationException('Action non autorisée pour cet espace ou à cette étape de la sélection.');
        }
    }
}
