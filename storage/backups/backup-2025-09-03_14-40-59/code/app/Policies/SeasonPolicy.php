<?php

namespace App\Policies;

use App\Models\Season;
use App\Models\User;

class SeasonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'system_admin',
            'association_admin',
            'association_registrar',
            'club_admin',
            'club_manager'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Season $season): bool
    {
        return $user->hasAnyRole([
            'system_admin',
            'association_admin',
            'association_registrar',
            'club_admin',
            'club_manager'
        ]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            'system_admin',
            'association_admin'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Season $season): bool
    {
        return $user->hasAnyRole([
            'system_admin',
            'association_admin'
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Season $season): bool
    {
        // Only system admin can delete seasons
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Season $season): bool
    {
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Season $season): bool
    {
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can set the season as current.
     */
    public function setCurrent(User $user, Season $season): bool
    {
        return $user->hasAnyRole([
            'system_admin',
            'association_admin'
        ]);
    }

    /**
     * Determine whether the user can update the season status.
     */
    public function updateStatus(User $user, Season $season): bool
    {
        return $user->hasAnyRole([
            'system_admin',
            'association_admin'
        ]);
    }

    /**
     * Determine whether the user can view season statistics.
     */
    public function viewStatistics(User $user, Season $season): bool
    {
        return $user->hasAnyRole([
            'system_admin',
            'association_admin',
            'association_registrar',
            'club_admin',
            'club_manager'
        ]);
    }
} 