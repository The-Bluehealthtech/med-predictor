<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'association_manager', 'club_manager', 'referee', 'committee_member']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        // Admin can view all teams
        if ($user->hasRole('admin')) {
            return true;
        }

        // Association managers can view teams in their association
        if ($user->hasRole('association_manager')) {
            return $user->association_id === $team->association_id;
        }

        // Club managers can view teams in their club
        if ($user->hasRole('club_manager')) {
            return $user->club_id === $team->club_id;
        }

        // Referees and committee members can view all teams
        return $user->hasAnyRole(['referee', 'committee_member']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'association_manager', 'club_manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        // Admin can update all teams
        if ($user->hasRole('admin')) {
            return true;
        }

        // Association managers can update teams in their association
        if ($user->hasRole('association_manager')) {
            return $user->association_id === $team->association_id;
        }

        // Club managers can update teams in their club
        if ($user->hasRole('club_manager')) {
            return $user->club_id === $team->club_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        // Only admin and association managers can delete teams
        if (!$user->hasAnyRole(['admin', 'association_manager'])) {
            return false;
        }

        // Admin can delete any team
        if ($user->hasRole('admin')) {
            return true;
        }

        // Association managers can delete teams in their association
        return $user->association_id === $team->association_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Team $team): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Team $team): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can add players to the team.
     */
    public function addPlayer(User $user, Team $team): bool
    {
        return $this->update($user, $team);
    }

    /**
     * Determine whether the user can remove players from the team.
     */
    public function removePlayer(User $user, Team $team): bool
    {
        return $this->update($user, $team);
    }

    /**
     * Determine whether the user can view team roster.
     */
    public function viewRoster(User $user, Team $team): bool
    {
        return $this->view($user, $team);
    }

    /**
     * Determine whether the user can view team statistics.
     */
    public function viewStatistics(User $user, Team $team): bool
    {
        return $this->view($user, $team);
    }

    /**
     * Determine whether the user can view team standings.
     */
    public function viewStandings(User $user, Team $team): bool
    {
        return $this->view($user, $team);
    }
} 