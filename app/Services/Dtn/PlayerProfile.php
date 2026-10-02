<?php

namespace App\Services\Dtn;

use App\Models\NationalSelection;
use App\Models\Player;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * Fiches joueurs consultées par la Direction technique nationale avant une
 * convocation : identité sportive, données de match et historique des
 * sélections. Aucune donnée médicale.
 *
 * Une fédération suit les joueurs de tous les clubs : la recherche n'est pas
 * limitée à l'organisation de l'utilisateur (réservée à l'espace fédération).
 */
final class PlayerProfile
{
    public function __construct(private readonly SelectionSnapshot $snapshots)
    {
    }

    public function search(?string $term, ?int $clubId, ?string $position, int $perPage = 25): LengthAwarePaginator
    {
        $query = Player::withoutGlobalScopes()->with(['club' => fn ($q) => $q->withoutGlobalScopes()])->whereNotNull('club_id');
        if ($term !== null && trim($term) !== '') {
            foreach (preg_split('/\s+/', trim($term)) as $word) {
                $like = '%' . mb_strtolower($word) . '%';
                $query->where(fn ($q) => $q->whereRaw('LOWER(first_name) LIKE ?', [$like])->orWhereRaw('LOWER(last_name) LIKE ?', [$like]));
            }
        }
        if ($clubId) {
            $query->where('club_id', $clubId);
        }
        if ($position) {
            $query->where('position', $position);
        }

        return $query->orderBy('last_name')->orderBy('first_name')->paginate(min(100, max(5, $perPage)));
    }

    public function summary(Player $player): array
    {
        return [
            'id' => $player->id,
            'name' => trim($player->first_name . ' ' . $player->last_name),
            'club' => ['id' => $player->club_id, 'name' => $player->club?->name],
            'position' => $player->position,
            'age' => $player->date_of_birth ? Carbon::parse($player->date_of_birth)->age : null,
            'nationality' => $player->nationality,
        ];
    }

    public function profile(Player $player): array
    {
        $player->loadMissing(['club' => fn ($q) => $q->withoutGlobalScopes()]);
        $history = NationalSelection::query()->where('player_id', $player->id)->orderByDesc('start_date')
            ->get(['id', 'team_label', 'event_type', 'event_name', 'start_date', 'end_date', 'status']);

        return array_merge($this->summary($player), [
            'date_of_birth' => $player->date_of_birth ? Carbon::parse($player->date_of_birth)->toDateString() : null,
            'preferred_foot' => $player->preferred_foot,
            'height_cm' => $player->height,
            'weight_kg' => $player->weight,
            'jersey_number' => $player->jersey_number,
            'data' => $this->snapshots->forPlayer($player->id),
            'selections' => $history->map(fn ($s) => [
                'id' => $s->id, 'team_label' => $s->team_label, 'event' => $s->event_name, 'type' => $s->eventTypeLabel(),
                'start_date' => $s->start_date?->toDateString(), 'end_date' => $s->end_date?->toDateString(),
                'status' => $s->effectiveStatus(), 'status_label' => NationalSelection::STATUS_LABELS[$s->effectiveStatus()] ?? $s->status,
            ])->all(),
            'currently_selected' => $history->contains(fn ($s) => in_array($s->effectiveStatus(), [NationalSelection::STATUS_IN_SELECTION], true)),
        ]);
    }
}
