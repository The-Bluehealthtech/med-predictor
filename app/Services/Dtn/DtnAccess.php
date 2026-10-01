<?php

namespace App\Services\Dtn;

use App\Models\NationalSelection;
use App\Models\NationalSelectionReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Règles d'accès de l'outil DTN, regroupées ici pour rester cohérentes.
 *
 *  - Côté DTN (convoque, rédige l'état de retour) : dtn et rôles association,
 *    limités aux sélections de leur fédération (association_id).
 *  - Côté club (rédige l'état de départ, accuse réception du retour) : staff
 *    du club, limité aux sélections de ses joueurs (club_id).
 *  - Partie médicale : lue et saisie uniquement par les rôles médicaux du côté
 *    concerné (club_medical / team_doctor côté club, association_medical côté
 *    DTN). L'administrateur système n'y a pas accès.
 */
final class DtnAccess
{
    private const DTN_ROLES = ['dtn', 'association_admin', 'association_registrar', 'association_medical'];
    private const CLUB_ROLES = ['club_admin', 'club_manager', 'club_medical', 'team_doctor', 'team_official'];
    private const CLUB_MEDICAL = ['club_medical', 'team_doctor'];
    private const DTN_MEDICAL = ['association_medical'];

    public function isDtnSide(User $user): bool
    {
        return $user->isSystemAdmin() || in_array($user->role, self::DTN_ROLES, true);
    }

    public function isClubSide(User $user): bool
    {
        return in_array($user->role, self::CLUB_ROLES, true);
    }

    public function canUseTool(User $user): bool
    {
        return $this->isDtnSide($user) || $this->isClubSide($user);
    }

    /** Sélections visibles par l'utilisateur. */
    public function scope(Builder $query, User $user): Builder
    {
        if ($user->isSystemAdmin()) {
            return $query;
        }
        if (in_array($user->role, self::DTN_ROLES, true)) {
            return $user->association_id ? $query->where('association_id', $user->association_id) : $query->whereRaw('1 = 0');
        }
        if ($this->isClubSide($user)) {
            return $user->club_id ? $query->where('club_id', $user->club_id) : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }

    public function canView(User $user, NationalSelection $selection): bool
    {
        if ($user->isSystemAdmin()) {
            return true;
        }
        if (in_array($user->role, self::DTN_ROLES, true)) {
            return $user->association_id !== null && (int) $selection->association_id === (int) $user->association_id;
        }
        if ($this->isClubSide($user)) {
            return $user->club_id !== null && (int) $selection->club_id === (int) $user->club_id;
        }

        return false;
    }

    public function canConvoke(User $user): bool
    {
        return $user->isSystemAdmin() || in_array($user->role, ['dtn', 'association_admin', 'association_registrar'], true);
    }

    public function canEditDeparture(User $user, NationalSelection $selection): bool
    {
        return $this->canView($user, $selection)
            && ($user->isSystemAdmin() || $this->isClubSide($user))
            && $selection->status === NationalSelection::STATUS_CONVOKED;
    }

    public function canEditReturn(User $user, NationalSelection $selection): bool
    {
        return $this->canView($user, $selection)
            && $this->isDtnSide($user)
            && in_array($selection->status, [NationalSelection::STATUS_DEPARTURE_SENT, NationalSelection::STATUS_IN_SELECTION], true);
    }

    public function canAcknowledge(User $user, NationalSelection $selection): bool
    {
        return $this->canView($user, $selection)
            && ($user->isSystemAdmin() || $this->isClubSide($user))
            && $selection->status === NationalSelection::STATUS_RETURN_SENT;
    }

    public function canCancel(User $user, NationalSelection $selection): bool
    {
        return $this->canView($user, $selection) && $this->canConvoke($user)
            && !in_array($selection->status, [NationalSelection::STATUS_CLOSED, NationalSelection::STATUS_CANCELLED], true);
    }

    /** Lecture de la partie médicale d'un état : rôle médical, des deux côtés, sur une sélection visible. */
    public function canSeeMedical(User $user, NationalSelection $selection): bool
    {
        return in_array($user->role, array_merge(self::CLUB_MEDICAL, self::DTN_MEDICAL), true) && $this->canView($user, $selection);
    }

    /** Saisie de la partie médicale : médical du club pour le départ, médical de la DTN pour le retour. */
    public function canEditMedical(User $user, NationalSelection $selection, string $direction): bool
    {
        if ($direction === NationalSelectionReport::DEPARTURE) {
            return in_array($user->role, self::CLUB_MEDICAL, true) && $this->canEditDeparture($user, $selection);
        }

        return in_array($user->role, self::DTN_MEDICAL, true) && $this->canEditReturn($user, $selection);
    }
}
