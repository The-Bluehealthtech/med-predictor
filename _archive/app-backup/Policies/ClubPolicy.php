<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\User;

class ClubPolicy
{
    /**
     * Determine whether the user can view any clubs.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'admin', 'system_admin', 'organizer', 'license_agent', 'committee_member', 'club_manager'
        ]);
    }

    /**
     * Determine whether the user can view the club.
     */
    public function view(User $user, Club $club): bool
    {
        // System admin and admin can view all clubs
        if (in_array($user->role, ['admin', 'system_admin'])) {
            return true;
        }

        // Association users and club managers can view clubs in their association
        if (in_array($user->role, ['organizer', 'license_agent', 'committee_member', 'club_manager'])) {
            return $user->association_id === $club->association_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create clubs.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            'admin', 'system_admin', 'organizer', 'club_manager'
        ]);
    }

    /**
     * Determine whether the user can update the club.
     */
    public function update(User $user, Club $club): bool
    {
        // System admin and admin can update all clubs
        if (in_array($user->role, ['admin', 'system_admin'])) {
            return true;
        }

        // Association organizers and club managers can update clubs in their association
        if (in_array($user->role, ['organizer', 'club_manager'])) {
            return $user->association_id === $club->association_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the club.
     */
    public function delete(User $user, Club $club): bool
    {
        // Only system admin and admin can delete clubs
        return in_array($user->role, ['admin', 'system_admin']);
    }

    /**
     * Determine whether the user can restore the club.
     */
    public function restore(User $user, Club $club): bool
    {
        return in_array($user->role, ['admin', 'system_admin']);
    }

    /**
     * Determine whether the user can permanently delete the club.
     */
    public function forceDelete(User $user, Club $club): bool
    {
        return in_array($user->role, ['admin', 'system_admin']);
    }
} 