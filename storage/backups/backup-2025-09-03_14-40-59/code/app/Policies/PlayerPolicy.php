<?php

namespace App\Policies;

use App\Models\Player;
use App\Models\User;

class PlayerPolicy
{
    /**
     * Determine whether the user can view any players.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'admin', 'organizer', 'license_agent', 'referee', 'committee_member', 'captain'
        ]);
    }

    /**
     * Determine whether the user can view the player.
     */
    public function view(User $user, Player $player): bool
    {
        // System admin can view all players
        if ($user->role === 'admin') {
            return true;
        }

        // Association users can view players in their association
        if (in_array($user->role, ['organizer', 'license_agent', 'committee_member'])) {
            return $user->association_id === $player->club->association_id;
        }

        // Club users can view players in their club
        if (in_array($user->role, ['captain'])) {
            return $user->club_id === $player->club_id;
        }

        // Referees can view players in matches they officiate
        if ($user->role === 'referee') {
            // This would need to be implemented based on match assignments
            return true; // Simplified for now
        }

        return false;
    }

    /**
     * Determine whether the user can create players.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            'admin', 'organizer', 'license_agent', 'captain'
        ]);
    }

    /**
     * Determine whether the user can update the player.
     */
    public function update(User $user, Player $player): bool
    {
        // System admin can update all players
        if ($user->role === 'admin') {
            return true;
        }

        // Association users can update players in their association
        if (in_array($user->role, ['organizer', 'license_agent'])) {
            return $user->association_id === $player->club->association_id;
        }

        // Club users can update players in their club
        if ($user->role === 'captain') {
            return $user->club_id === $player->club_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the player.
     */
    public function delete(User $user, Player $player): bool
    {
        // Only system admin and association organizers can delete players
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'organizer') {
            return $user->association_id === $player->club->association_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the player.
     */
    public function restore(User $user, Player $player): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the player.
     */
    public function forceDelete(User $user, Player $player): bool
    {
        return $user->role === 'admin';
    }
} 