<?php

namespace App\Policies;

use App\Models\MatchEvent;
use App\Models\User;

class MatchEventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'system_admin',
            'association_admin',
            'association_registrar',
            'club_admin',
            'club_manager',
            'referee'
        ]);
    }

    public function view(User $user, MatchEvent $matchEvent): bool
    {
        // System admin can view all events
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can view events in their association
        if ($user->isAssociationAdmin() && $matchEvent->team && $matchEvent->team->club->association_id === $user->association_id) {
            return true;
        }

        // Club admin/manager can view events for their club's teams
        if (($user->isClubAdmin() || $user->isClubManager()) && $matchEvent->team && $matchEvent->team->club_id === $user->club_id) {
            return true;
        }

        // Referee can view events for matches they officiate
        if ($user->isReferee() && $matchEvent->match) {
            // Check if user is officiating this match
            return $matchEvent->match->officials()->where('user_id', $user->id)->exists();
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
            'club_manager',
            'referee'
        ]);
    }

    public function update(User $user, MatchEvent $matchEvent): bool
    {
        // System admin can update all events
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can update events in their association
        if ($user->isAssociationAdmin() && $matchEvent->team && $matchEvent->team->club->association_id === $user->association_id) {
            return true;
        }

        // Club admin/manager can update events for their club's teams
        if (($user->isClubAdmin() || $user->isClubManager()) && $matchEvent->team && $matchEvent->team->club_id === $user->club_id) {
            return true;
        }

        // Referee can update events for matches they officiate (within time limit)
        if ($user->isReferee() && $matchEvent->match) {
            $isOfficiating = $matchEvent->match->officials()->where('user_id', $user->id)->exists();
            $isRecent = $matchEvent->recorded_at && $matchEvent->recorded_at->diffInHours(now()) <= 24;
            return $isOfficiating && $isRecent;
        }

        return false;
    }

    public function delete(User $user, MatchEvent $matchEvent): bool
    {
        // Only system admin and association admin can delete events
        if ($user->isSystemAdmin()) {
            return true;
        }

        if ($user->isAssociationAdmin() && $matchEvent->team && $matchEvent->team->club->association_id === $user->association_id) {
            return true;
        }

        return false;
    }

    public function confirm(User $user, MatchEvent $matchEvent): bool
    {
        // System admin can confirm all events
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can confirm events in their association
        if ($user->isAssociationAdmin() && $matchEvent->team && $matchEvent->team->club->association_id === $user->association_id) {
            return true;
        }

        // Referee can confirm events for matches they officiate
        if ($user->isReferee() && $matchEvent->match) {
            return $matchEvent->match->officials()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    public function contest(User $user, MatchEvent $matchEvent): bool
    {
        // System admin can contest all events
        if ($user->isSystemAdmin()) {
            return true;
        }

        // Association admin can contest events in their association
        if ($user->isAssociationAdmin() && $matchEvent->team && $matchEvent->team->club->association_id === $user->association_id) {
            return true;
        }

        // Club admin/manager can contest events for their club's teams
        if (($user->isClubAdmin() || $user->isClubManager()) && $matchEvent->team && $matchEvent->team->club_id === $user->club_id) {
            return true;
        }

        // Referee can contest events for matches they officiate
        if ($user->isReferee() && $matchEvent->match) {
            return $matchEvent->match->officials()->where('user_id', $user->id)->exists();
        }

        return false;
    }
} 