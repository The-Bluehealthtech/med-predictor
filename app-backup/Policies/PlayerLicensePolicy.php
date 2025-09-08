<?php

namespace App\Policies;

use App\Models\PlayerLicense;
use App\Models\User;

class PlayerLicensePolicy
{
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

    public function view(User $user, PlayerLicense $playerLicense): bool
    {
        // System admin can view all licenses
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can view licenses in their association
        if ($user->isAssociationAdmin() && $playerLicense->club->association_id === $user->association_id) {
            return true;
        }

        // Club admin/manager can view licenses in their club
        if ($user->isClubUser() && $playerLicense->club_id === $user->club_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            'system_admin',
            'association_admin',
            'association_registrar',
            'club_admin',
            'club_manager'
        ]);
    }

    public function update(User $user, PlayerLicense $playerLicense): bool
    {
        // System admin can update all licenses
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can update licenses in their association
        if ($user->isAssociationAdmin() && $playerLicense->club->association_id === $user->association_id) {
            return true;
        }

        // Club admin/manager can update licenses in their club
        if ($user->isClubUser() && $playerLicense->club_id === $user->club_id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, PlayerLicense $playerLicense): bool
    {
        // Only system admin and association admin can delete licenses
        return $user->hasAnyRole(['system_admin', 'association_admin']);
    }

    public function restore(User $user, PlayerLicense $playerLicense): bool
    {
        return $user->isSystemAdmin();
    }

    public function forceDelete(User $user, PlayerLicense $playerLicense): bool
    {
        return $user->isSystemAdmin();
    }

    public function approve(User $user, PlayerLicense $playerLicense): bool
    {
        // Only association admin and registrar can approve licenses
        return $user->hasAnyRole(['association_admin', 'association_registrar']);
    }

    public function reject(User $user, PlayerLicense $playerLicense): bool
    {
        // Only association admin and registrar can reject licenses
        return $user->hasAnyRole(['association_admin', 'association_registrar']);
    }

    public function renew(User $user, PlayerLicense $playerLicense): bool
    {
        // System admin, association admin, and club admin can renew licenses
        return $user->hasAnyRole(['system_admin', 'association_admin', 'club_admin']);
    }

    public function suspend(User $user, PlayerLicense $playerLicense): bool
    {
        // System admin, association admin, and club admin can suspend licenses
        return $user->hasAnyRole(['system_admin', 'association_admin', 'club_admin']);
    }

    public function transfer(User $user, PlayerLicense $playerLicense): bool
    {
        // System admin, association admin, and club admin can transfer licenses
        return $user->hasAnyRole(['system_admin', 'association_admin', 'club_admin']);
    }
} 