<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'system_admin',
            'association_admin',
            'association_registrar'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // System admin can view all users
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can view users in their association
        if ($user->isAssociationAdmin() && $model->association_id === $user->association_id) {
            return true;
        }

        // Club admin can view users in their club
        if ($user->isClubAdmin() && $model->club_id === $user->club_id) {
            return true;
        }

        // Users can view their own profile
        return $user->id === $model->id;
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
    public function update(User $user, User $model): bool
    {
        // System admin can update all users
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can update users in their association
        if ($user->isAssociationAdmin() && $model->association_id === $user->association_id) {
            return true;
        }

        // Club admin can update users in their club
        if ($user->isClubAdmin() && $model->club_id === $user->club_id) {
            return true;
        }

        // Users can update their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Prevent self-deletion
        if ($user->id === $model->id) {
            return false;
        }

        // Only system admin can delete users
        return $user->isSystemAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isSystemAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isSystemAdmin();
    }

    /**
     * Determine whether the user can update the user role.
     */
    public function updateRole(User $user, User $model): bool
    {
        // Prevent self-role-update
        if ($user->id === $model->id) {
            return false;
        }

        // System admin can update any role
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can update roles within their association
        if ($user->isAssociationAdmin() && $model->association_id === $user->association_id) {
            // Cannot promote to system admin - this will be checked in the controller
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the user permissions.
     */
    public function updatePermissions(User $user, User $model): bool
    {
        // Prevent self-permission-update
        if ($user->id === $model->id) {
            return false;
        }

        // System admin can update any permissions
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can update permissions within their association
        if ($user->isAssociationAdmin() && $model->association_id === $user->association_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the user status.
     */
    public function updateStatus(User $user, User $model): bool
    {
        // Prevent self-status-update
        if ($user->id === $model->id) {
            return false;
        }

        // System admin can update any status
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can update status within their association
        if ($user->isAssociationAdmin() && $model->association_id === $user->association_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view user statistics.
     */
    public function viewStatistics(User $user, User $model): bool
    {
        // System admin can view all statistics
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can view statistics within their association
        if ($user->isAssociationAdmin() && $model->association_id === $user->association_id) {
            return true;
        }

        // Club admin can view statistics within their club
        if ($user->isClubAdmin() && $model->club_id === $user->club_id) {
            return true;
        }

        // Users can view their own statistics
        return $user->id === $model->id;
    }
} 