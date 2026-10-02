<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Federation;
use App\Models\Player;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PassportController extends Controller
{
    public function clubPassport(Request $request, Club $club): JsonResponse
    {
        $this->authorizeClub($request, $club);

        $players = Player::withoutGlobalScopes()
            ->where(function (Builder $query) use ($club) {
                $query->where('club_id', $club->id)
                    ->orWhere('current_club_id', $club->id);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $latestTransfers = Transfer::query()
            ->whereIn('player_id', $players->pluck('id'))
            ->where(function (Builder $query) use ($club) {
                $query->where('club_origin_id', $club->id)
                    ->orWhere('club_destination_id', $club->id);
            })
            ->orderByDesc('created_at')
            ->get()
            ->unique('player_id')
            ->keyBy('player_id');

        $data = $players->map(function (Player $player) use ($latestTransfers) {
            $transfer = $latestTransfers->get($player->id);

            return [
                'id' => $player->id,
                'name' => trim((string) ($player->name ?: ($player->first_name . ' ' . $player->last_name))),
                'nationality' => $player->nationality,
                'position' => $player->position,
                'photo_url' => $player->player_picture_url,
                'fifa_license_status' => $player->fifa_license_status,
                'is_international' => (bool) ($transfer?->is_international ?? false),
                'itc_status' => $transfer?->itc_status,
                'suspensions_count' => 0,
                'is_eligible' => (bool) $player->is_transfer_eligible
                    && !in_array($player->fifa_license_status, ['suspended', 'expired', 'revoked'], true),
            ];
        })->values();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function federationPassport(Request $request, Federation $federation): JsonResponse
    {
        $this->authorizeFederation($request, $federation);

        $transfers = Transfer::with(['player', 'clubOrigin', 'clubDestination'])
            ->where(function (Builder $query) use ($federation) {
                $query->where('federation_origin_id', $federation->id)
                    ->orWhere('federation_destination_id', $federation->id)
                    ->orWhereHas('clubOrigin', fn (Builder $club) => $club->where('federation_id', $federation->id))
                    ->orWhereHas('clubDestination', fn (Builder $club) => $club->where('federation_id', $federation->id));
            })
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $data = $transfers->map(fn (Transfer $transfer) => [
            'id' => $transfer->id,
            'transfer_status' => $transfer->transfer_status,
            'transfer_type' => $transfer->transfer_type,
            'transfer_date' => $transfer->transfer_date?->format('Y-m-d'),
            'itc_status' => $transfer->itc_status,
            'is_international' => (bool) $transfer->is_international,
            'player' => [
                'id' => $transfer->player?->id,
                'name' => trim((string) ($transfer->player?->name
                    ?: (($transfer->player?->first_name ?? '') . ' ' . ($transfer->player?->last_name ?? '')))),
            ],
            'club_origin' => [
                'id' => $transfer->clubOrigin?->id,
                'name' => $transfer->clubOrigin?->name,
            ],
            'club_destination' => [
                'id' => $transfer->clubDestination?->id,
                'name' => $transfer->clubDestination?->name,
            ],
        ])->values();

        $alerts = $transfers
            ->filter(fn (Transfer $transfer) =>
                ($transfer->is_international && !in_array($transfer->itc_status, ['approved', 'not_required'], true))
                || $transfer->is_minor_transfer
            )
            ->take(20)
            ->map(function (Transfer $transfer) {
                $reasons = [];
                if ($transfer->is_minor_transfer) {
                    $reasons[] = 'Transfert de joueur mineur : contrôle réglementaire requis.';
                }
                if ($transfer->is_international && !in_array($transfer->itc_status, ['approved', 'not_required'], true)) {
                    $reasons[] = 'ITC à vérifier : ' . ($transfer->itc_status ?: 'statut non renseigné') . '.';
                }

                return [
                    'id' => 'transfer-' . $transfer->id,
                    'transfer_id' => $transfer->id,
                    'message' => implode(' ', $reasons),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'transfers' => $data,
                'alerts' => $alerts,
            ],
        ]);
    }

    public function players(Request $request): JsonResponse
    {
        $players = $this->scopePlayersForUser($request, Player::query())
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(200)
            ->get();

        $clubIds = $players
            ->flatMap(fn (Player $player) => [$player->current_club_id, $player->club_id])
            ->filter()
            ->unique()
            ->values();

        $clubs = Club::query()
            ->whereIn('id', $clubIds)
            ->get(['id', 'name'])
            ->keyBy('id');

        return response()->json([
            'success' => true,
            'data' => $players->map(function (Player $player) use ($clubs) {
                $clubId = $player->current_club_id ?: $player->club_id;
                $club = $clubId ? $clubs->get($clubId) : null;

                return [
                    'id' => $player->id,
                    'name' => trim((string) ($player->name ?: ($player->first_name . ' ' . $player->last_name))),
                    'position' => $player->position,
                    'nationality' => $player->nationality,
                    'fifa_license_status' => $player->fifa_license_status,
                    'current_club' => $club ? [
                        'id' => $club->id,
                        'name' => $club->name,
                    ] : null,
                ];
            })->values(),
        ]);
    }

    public function statistics(Request $request): JsonResponse
    {
        $transfers = $this->scopeTransfersForUser($request, Transfer::query());
        $players = $this->scopePlayersForUser($request, Player::query());

        $eligiblePlayers = (clone $players)
            ->where('is_transfer_eligible', true)
            ->where(function (Builder $query) {
                $query->whereNull('fifa_license_status')
                    ->orWhereNotIn('fifa_license_status', ['suspended', 'expired', 'revoked']);
            })
            ->count();

        $pendingItc = (clone $transfers)
            ->where('is_international', true)
            ->where(function (Builder $query) {
                $query->whereNull('itc_status')
                    ->orWhereNotIn('itc_status', ['approved', 'not_required']);
            })
            ->count();

        $alerts = (clone $transfers)
            ->where(function (Builder $query) {
                $query->where('is_minor_transfer', true)
                    ->orWhere(function (Builder $international) {
                        $international->where('is_international', true)
                            ->where(function (Builder $itc) {
                                $itc->whereNull('itc_status')
                                    ->orWhereNotIn('itc_status', ['approved', 'not_required']);
                            });
                    });
            })
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'eligiblePlayers' => $eligiblePlayers,
                'approvedTransfers' => (clone $transfers)->where('transfer_status', 'approved')->count(),
                'pendingItc' => $pendingItc,
                'alerts' => $alerts,
            ],
        ]);
    }

    public function playerTransfers(Request $request, Player $player): JsonResponse
    {
        $query = $this->scopeTransfersForUser($request, Transfer::with(['clubOrigin', 'clubDestination']))
            ->where('player_id', $player->id)
            ->orderByDesc('transfer_date')
            ->orderByDesc('created_at');

        return response()->json([
            'success' => true,
            'data' => $query->get()->map(fn (Transfer $transfer) => [
                'id' => $transfer->id,
                'transfer_type' => $transfer->transfer_type,
                'transfer_status' => $transfer->transfer_status,
                'transfer_date' => $transfer->transfer_date?->format('Y-m-d'),
                'contract_start_date' => $transfer->contract_start_date?->format('Y-m-d'),
                'contract_end_date' => $transfer->contract_end_date?->format('Y-m-d'),
                'transfer_fee' => $transfer->transfer_fee,
                'currency' => $transfer->currency,
                'is_international' => (bool) $transfer->is_international,
                'itc_status' => $transfer->itc_status,
                'club_origin' => [
                    'id' => $transfer->clubOrigin?->id,
                    'name' => $transfer->clubOrigin?->name,
                ],
                'club_destination' => [
                    'id' => $transfer->clubDestination?->id,
                    'name' => $transfer->clubDestination?->name,
                ],
            ])->values(),
        ]);
    }

    private function authorizeClub(Request $request, Club $club): void
    {
        $user = $request->user();
        abort_unless($user, 401);

        if ($user->isSystemAdmin()) {
            return;
        }

        if ($user->isClubUser() && $user->club_id && (int) $user->club_id === (int) $club->id) {
            return;
        }

        if ($user->isAssociationUser() && $user->association_id
            && (int) $user->association_id === (int) $club->association_id) {
            return;
        }

        abort(403);
    }

    private function authorizeFederation(Request $request, Federation $federation): void
    {
        $user = $request->user();
        abort_unless($user, 401);

        if ($user->isSystemAdmin()) {
            return;
        }

        if ($user->isClubUser() && $user->club_id
            && $federation->clubs()->whereKey($user->club_id)->exists()) {
            return;
        }

        if ($user->isAssociationUser() && $user->association_id
            && $federation->clubs()->where('association_id', $user->association_id)->exists()) {
            return;
        }

        abort(403);
    }

    private function scopePlayersForUser(Request $request, Builder $query): Builder
    {
        $user = $request->user();
        abort_unless($user, 401);

        if ($user->isSystemAdmin()) {
            return $query;
        }

        if ($user->isPlayer()) {
            return $user->player_id
                ? $query->whereKey($user->player_id)
                : $query->whereRaw('1 = 0');
        }

        if ($user->isClubUser()) {
            return $user->club_id
                ? $query->where(function (Builder $players) use ($user) {
                    $players->where('club_id', $user->club_id)
                        ->orWhere('current_club_id', $user->club_id);
                })
                : $query->whereRaw('1 = 0');
        }

        if ($user->isAssociationUser()) {
            if (!$user->association_id) {
                return $query->whereRaw('1 = 0');
            }

            $clubIds = Club::query()
                ->where('association_id', $user->association_id)
                ->pluck('id');

            return $query->where(function (Builder $players) use ($user, $clubIds) {
                $players->where('association_id', $user->association_id)
                    ->orWhereIn('club_id', $clubIds)
                    ->orWhereIn('current_club_id', $clubIds);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    private function scopeTransfersForUser(Request $request, Builder $query): Builder
    {
        $user = $request->user();
        abort_unless($user, 401);

        if ($user->isSystemAdmin()) {
            return $query;
        }

        if ($user->isPlayer()) {
            return $user->player_id
                ? $query->where('player_id', $user->player_id)
                : $query->whereRaw('1 = 0');
        }

        if ($user->isClubUser()) {
            return $user->club_id
                ? $query->where(function (Builder $transfers) use ($user) {
                    $transfers->where('club_origin_id', $user->club_id)
                        ->orWhere('club_destination_id', $user->club_id);
                })
                : $query->whereRaw('1 = 0');
        }

        if ($user->isAssociationUser()) {
            if (!$user->association_id) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where(function (Builder $transfers) use ($user) {
                $transfers->whereHas('clubOrigin', fn (Builder $club) =>
                    $club->where('association_id', $user->association_id)
                )->orWhereHas('clubDestination', fn (Builder $club) =>
                    $club->where('association_id', $user->association_id)
                );
            });
        }

        return $query->whereRaw('1 = 0');
    }
}
