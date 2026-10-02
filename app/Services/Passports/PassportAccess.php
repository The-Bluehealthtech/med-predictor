<?php

namespace App\Services\Passports;

use App\Models\Player;
use App\Models\User;
use App\Services\MedicalRecordAccess;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Qui peut voir quel passeport :
 * - passeport médical : le joueur lui-même, ou un rôle médical dans son périmètre (MedicalRecordAccess) ;
 * - passeport de transfert : le joueur, son club, sa fédération, l'admin système.
 */
final class PassportAccess
{
    public function __construct(private readonly MedicalRecordAccess $medical)
    {
    }

    public function canViewMedical(User $user, Player $player): bool
    {
        if ($user->isPlayer()) {
            return (int) $user->player_id === (int) $player->id;
        }
        try {
            $this->medical->authorize($user, $player, null);

            return true;
        } catch (HttpException) {
            return false;
        }
    }

    public function canViewTransfer(User $user, Player $player): bool
    {
        return match (true) {
            $user->isSystemAdmin() => true,
            $user->isPlayer() => (int) $user->player_id === (int) $player->id,
            $user->isClubUser() => $user->club_id && (int) $user->club_id === (int) $player->club_id,
            $user->isAssociationUser() => $user->association_id && (int) $user->association_id === (int) $player->club?->association_id,
            default => false,
        };
    }

    /** Joueurs listés dans la recherche du passeport médical (rôles médicaux uniquement). */
    public function medicalPlayers(User $user): Builder
    {
        return $this->medical->scopePlayers($user, Player::query());
    }

    public function transferPlayers(User $user): Builder
    {
        $query = Player::query();

        return match (true) {
            $user->isSystemAdmin() => $query,
            $user->isClubUser() && (bool) $user->club_id => $query->where('club_id', $user->club_id),
            $user->isAssociationUser() && (bool) $user->association_id => $query->whereHas('club', fn ($c) => $c->where('association_id', $user->association_id)),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
