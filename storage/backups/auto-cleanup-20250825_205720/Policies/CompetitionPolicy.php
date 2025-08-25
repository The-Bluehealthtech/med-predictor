<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompetitionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin', 'organizer', 'referee', 'club_manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Competition $competition): bool
    {
        // System admin can view all competitions
        if ($user->hasRole('system_admin')) {
            return true;
        }

        // Association admin can view competitions in their association
        if ($user->hasRole('association_admin')) {
            return $competition->association_id === $user->association_id;
        }

        // Organizer can view competitions they manage
        if ($user->hasRole('organizer')) {
            return $competition->association_id === $user->association_id;
        }

        // Referee can view competitions they're assigned to
        if ($user->hasRole('referee')) {
            return $competition->association_id === $user->association_id;
        }

        // Club manager can view competitions their club participates in
        if ($user->hasRole('club_manager')) {
            return $competition->teams()
                ->whereHas('club', function ($query) use ($user) {
                    $query->where('id', $user->club_id);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin', 'organizer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Competition $competition): bool
    {
        // System admin can update all competitions
        if ($user->hasRole('system_admin')) {
            return true;
        }

        // Association admin can update competitions in their association
        if ($user->hasRole('association_admin')) {
            return $competition->association_id === $user->association_id;
        }

        // Organizer can update competitions they manage
        if ($user->hasRole('organizer')) {
            return $competition->association_id === $user->association_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Competition $competition): bool
    {
        // Only system admin and association admin can delete competitions
        if ($user->hasRole('system_admin')) {
            return true;
        }

        if ($user->hasRole('association_admin')) {
            return $competition->association_id === $user->association_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Competition $competition): bool
    {
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Competition $competition): bool
    {
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can manage teams in the competition.
     */
    public function manageTeams(User $user, Competition $competition): bool
    {
        return $this->update($user, $competition);
    }

    /**
     * Determine whether the user can view standings.
     */
    public function viewStandings(User $user, Competition $competition): bool
    {
        return $this->view($user, $competition);
    }

    /**
     * Determine whether the user can view statistics.
     */
    public function viewStatistics(User $user, Competition $competition): bool
    {
        return $this->view($user, $competition);
    }

    /**
     * Determine whether the user can manage seasons.
     */
    public function manageSeasons(User $user, Competition $competition): bool
    {
        return $this->update($user, $competition);
    }
}
