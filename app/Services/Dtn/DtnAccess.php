<?php

namespace App\Services\Dtn;

use App\Models\NationalSelection;
use App\Models\NationalSelectionReport;
use App\Models\User;
use App\Services\RBACService;
use Illuminate\Database\Eloquent\Builder;

/**
 * Règles d'accès des sélections nationales, communes à l'interface et à l'API.
 *
 * Deux espaces séparés par le RBAC :
 *  - Espace fédération (permission dtn-federation-space) : consulte les fiches
 *    joueurs, convoque, rédige l'état de retour ; limité aux sélections de sa
 *    fédération (association_id).
 *  - Espace club (permission club-selections-space) : reçoit les convocations,
 *    rédige l'état de départ, reçoit l'état de retour et en accuse réception ;
 *    limité aux sélections de ses joueurs (club_id).
 *
 * Partie médicale : lue et saisie uniquement par les rôles médicaux du côté
 * concerné (club_medical / team_doctor côté club, association_medical côté
 * fédération). L'administrateur système n'y a pas accès.
 */
final class DtnAccess
{
    public const FEDERATION_PERMISSION = 'dtn-federation-space';
    public const CLUB_PERMISSION = 'club-selections-space';

    private const CLUB_MEDICAL = ['club_medical', 'team_doctor'];
    private const DTN_MEDICAL = ['association_medical'];

    public function __construct(private readonly RBACService $rbac)
    {
    }

    public function isDtnSide(User $user): bool
    {
        return $this->rbac->userHasPermission($user, self::FEDERATION_PERMISSION);
    }

    public function isClubSide(User $user): bool
    {
        return $this->rbac->userHasPermission($user, self::CLUB_PERMISSION);
    }

    public function canUseTool(User $user): bool
    {
        return $this->isDtnSide($user) || $this->isClubSide($user);
    }

    public function isMedical(User $user): bool
    {
        return in_array($user->role, array_merge(self::CLUB_MEDICAL, self::DTN_MEDICAL), true);
    }

    /** Sélections visibles dans l'espace fédération. */
    public function scopeFederation(Builder $query, User $user): Builder
    {
        if (!$this->isDtnSide($user)) {
            return $query->whereRaw('1 = 0');
        }

        return $user->isSystemAdmin() ? $query
            : ($user->association_id ? $query->where('association_id', $user->association_id) : $query->whereRaw('1 = 0'));
    }

    /** Sélections visibles dans l'espace club. */
    public function scopeClub(Builder $query, User $user): Builder
    {
        if (!$this->isClubSide($user)) {
            return $query->whereRaw('1 = 0');
        }

        return $user->isSystemAdmin() ? $query
            : ($user->club_id ? $query->where('club_id', $user->club_id) : $query->whereRaw('1 = 0'));
    }

    public function canViewAsFederation(User $user, NationalSelection $selection): bool
    {
        return $this->isDtnSide($user)
            && ($user->isSystemAdmin() || ($user->association_id !== null && (int) $selection->association_id === (int) $user->association_id));
    }

    public function canViewAsClub(User $user, NationalSelection $selection): bool
    {
        return $this->isClubSide($user)
            && ($user->isSystemAdmin() || ($user->club_id !== null && (int) $selection->club_id === (int) $user->club_id));
    }

    public function canView(User $user, NationalSelection $selection): bool
    {
        return $this->canViewAsFederation($user, $selection) || $this->canViewAsClub($user, $selection);
    }

    public function canConvoke(User $user): bool
    {
        return $this->isDtnSide($user) && !in_array($user->role, self::DTN_MEDICAL, true);
    }

    public function canEditDeparture(User $user, NationalSelection $selection): bool
    {
        return $this->canViewAsClub($user, $selection) && $selection->status === NationalSelection::STATUS_CONVOKED;
    }

    public function canEditReturn(User $user, NationalSelection $selection): bool
    {
        return $this->canViewAsFederation($user, $selection)
            && in_array($selection->status, [NationalSelection::STATUS_DEPARTURE_SENT, NationalSelection::STATUS_IN_SELECTION], true);
    }

    public function canAcknowledge(User $user, NationalSelection $selection): bool
    {
        return $this->canViewAsClub($user, $selection) && $selection->status === NationalSelection::STATUS_RETURN_SENT;
    }

    public function canCancel(User $user, NationalSelection $selection): bool
    {
        return $this->canViewAsFederation($user, $selection) && $this->canConvoke($user)
            && !in_array($selection->status, [NationalSelection::STATUS_CLOSED, NationalSelection::STATUS_CANCELLED], true);
    }

    /** Lecture de la partie médicale : rôle médical, sur une sélection visible de son côté. */
    public function canSeeMedical(User $user, NationalSelection $selection): bool
    {
        return (in_array($user->role, self::CLUB_MEDICAL, true) && $this->canViewAsClub($user, $selection))
            || (in_array($user->role, self::DTN_MEDICAL, true) && $this->canViewAsFederation($user, $selection));
    }

    /** Saisie de la partie médicale : médical du club pour le départ, médical de la fédération pour le retour. */
    public function canEditMedical(User $user, NationalSelection $selection, string $direction): bool
    {
        if ($direction === NationalSelectionReport::DEPARTURE) {
            return in_array($user->role, self::CLUB_MEDICAL, true) && $this->canEditDeparture($user, $selection);
        }

        return in_array($user->role, self::DTN_MEDICAL, true) && $this->canEditReturn($user, $selection);
    }
}
