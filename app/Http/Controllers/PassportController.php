<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Federation;
use App\Models\Player;
use App\Models\Transfer;
use Illuminate\Http\Request;

final class PassportController extends Controller
{
    public function clubPassport(Request $request, Club $club)
    {
        $this->authorizeClub($request, $club);
        $players = Player::where('club_id', $club->id)->with('passport')->get()->map(function ($player) {
            $passport = $player->passport;
            return $player->only(['id', 'name', 'position', 'nationality']) + [
                'photo_url' => $player->player_picture,
                'fifa_license_status' => $passport?->license_status,
                'itc_status' => $passport?->itc_status,
                'is_international' => null,
                'suspensions_count' => $passport && is_array($passport->suspensions) ? count($passport->suspensions) : null,
                'is_eligible' => $passport?->getTransferEligibility()['eligible'] ?? null,
            ];
        });
        return response()->json(['success' => true, 'data' => $players]);
    }

    public function federationPassport(Request $request, Federation $federation)
    {
        $user = $request->user();
        abort_unless($user && ($user->isSystemAdmin() || $user->isAssociationUser()), 403);
        $clubs = Club::where('federation_id', $federation->id);
        if (!$user->isSystemAdmin()) {
            abort_unless($user->association_id, 403);
            $clubs->where('association_id', $user->association_id);
        }
        $ids = $clubs->pluck('id');
        $transfers = Transfer::where(function ($query) use ($ids) {
            $query->whereIn('club_origin_id', $ids)->orWhereIn('club_destination_id', $ids);
        })->with(['player:id,name', 'clubOrigin:id,name', 'clubDestination:id,name'])
            ->orderByDesc('transfer_date')->get()->map(fn ($transfer) => $this->transferData($transfer));
        // Aucune règle d'alerte réglementaire n'est déduite arbitrairement.
        return response()->json(['success' => true, 'data' => ['transfers' => $transfers, 'alerts' => []],
            'meta' => ['alerts_available' => false]]);
    }

    public function playerTransfers(Request $request, Player $player)
    {
        $user = $request->user();
        abort_unless($user, 401);
        if ($user->isPlayer()) abort_unless((int) $user->player_id === (int) $player->id, 403);
        else {
            abort_unless($player->club, 403);
            $this->authorizeClub($request, $player->club);
        }
        $query = Transfer::where('player_id', $player->id);
        if ($user->isClubUser()) {
            $query->where(fn ($q) => $q->where('club_origin_id', $user->club_id)->orWhere('club_destination_id', $user->club_id));
        }
        return response()->json(['success' => true, 'data' => $query
            ->with(['player:id,name', 'clubOrigin:id,name', 'clubDestination:id,name'])
            ->orderByDesc('transfer_date')->get()->map(fn ($transfer) => $this->transferData($transfer))]);
    }

    private function authorizeClub(Request $request, Club $club): void
    {
        $user = $request->user();
        abort_unless($user && ($user->isSystemAdmin()
            || ($user->isClubUser() && $user->club_id && (int) $user->club_id === (int) $club->id)
            || ($user->isAssociationUser() && $user->association_id && (int) $user->association_id === (int) $club->association_id)), 403);
    }

    private function transferData(Transfer $transfer): array
    {
        return $transfer->only(['id', 'transfer_date', 'transfer_status', 'transfer_type', 'transfer_fee', 'currency', 'itc_status']) + [
            'player' => ['id' => $transfer->player_id, 'name' => $transfer->player?->name],
            'club_origin' => ['id' => $transfer->club_origin_id, 'name' => $transfer->clubOrigin?->name],
            'club_destination' => ['id' => $transfer->club_destination_id, 'name' => $transfer->clubDestination?->name],
        ];
    }
}
