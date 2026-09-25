<?php

namespace App\Policies;

use App\Models\MatchModel;
use App\Models\User;

class MatchPolicy
{
    /**
     * Determine whether the user can view any matches.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'admin', 'system_admin', 'organizer', 'referee', 'committee_member', 'club_manager'
        ]);
    }

    /**
     * Determine whether the user can view the match.
     */
    public function view(User $user, MatchModel $match): bool
    {
        \Log::info('DEBUG MatchPolicy::view', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_association_id' => $user->association_id,
            'match_id' => $match->id,
            'match_competition_id' => $match->competition_id,
            'match_referee' => $match->referee,
            'match_competition_association_id' => $match->competition?->association_id
        ]);
        
        // Debug logging
        \Log::info('MatchPolicy::view called', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_association_id' => $user->association_id,
            'match_id' => $match->id,
            'match_competition_id' => $match->competition_id,
            'match_referee' => $match->referee,
        ]);

        // System admin and admin can view all matches
        if (in_array($user->role, ['admin', 'system_admin'])) {
            \Log::info('User is admin/system_admin - allowing access');
            return true;
        }

        // Referees can view matches they are assigned to
        if ($user->role === 'referee' && $match->referee === $user->name) {
            \Log::info('User is referee assigned to match - allowing access');
            return true;
        }

        // Association users can view matches in their association
        if (in_array($user->role, ['organizer', 'committee_member', 'club_manager'])) {
            $matchAssociationId = $this->getMatchAssociationId($match);
            \Log::info('Checking association access', [
                'user_association_id' => $user->association_id,
                'match_association_id' => $matchAssociationId,
                'has_access' => $user->association_id === $matchAssociationId
            ]);
            return $user->association_id === $matchAssociationId;
        }

        \Log::info('User does not have access');
        return false;
    }

    /**
     * Determine whether the user can create matches.
     */
    public function create(User $user): bool
    {
        // Admin and system admin can create matches anywhere
        if (in_array($user->role, ['admin', 'system_admin'])) {
            return true;
        }

        // Organizers can create matches in their association
        if ($user->role === 'organizer') {
            $canCreate = $user->association_id !== null;
            return $canCreate;
        }

        return false;
    }

    /**
     * Determine whether the user can update the match.
     */
    public function update(User $user, MatchModel $match): bool
    {
        // System admin and admin can update all matches
        if (in_array($user->role, ['admin', 'system_admin'])) {
            return true;
        }

        // Organizers can update matches in their association
        if ($user->role === 'organizer') {
            return $user->association_id === $this->getMatchAssociationId($match);
        }

        // Referees can update matches they are assigned to (limited fields)
        if ($user->role === 'referee' && $match->referee === $user->name) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the match.
     */
    public function delete(User $user, MatchModel $match): bool
    {
        // Only system admin and admin can delete matches
        return in_array($user->role, ['admin', 'system_admin']);
    }

    /**
     * Determine whether the user can restore the match.
     */
    public function restore(User $user, MatchModel $match): bool
    {
        return in_array($user->role, ['admin', 'system_admin']);
    }

    /**
     * Determine whether the user can permanently delete the match.
     */
    public function forceDelete(User $user, MatchModel $match): bool
    {
        return in_array($user->role, ['admin', 'system_admin']);
    }

    /**
     * Determine whether the user can add events to the match.
     */
    public function addEvent(User $user, MatchModel $match): bool
    {
        // System admin and admin can add events to any match
        if (in_array($user->role, ['admin', 'system_admin'])) {
            return true;
        }

        // Referees can add events to matches they are assigned to
        if ($user->role === 'referee' && $match->referee === $user->name) {
            return true;
        }

        // Organizers can add events to matches in their association
        if ($user->role === 'organizer') {
            return $user->association_id === $this->getMatchAssociationId($match);
        }

        return false;
    }

    /**
     * Determine whether the user can update match status.
     */
    public function updateStatus(User $user, MatchModel $match): bool
    {
        // System admin and admin can update status of any match
        if (in_array($user->role, ['admin', 'system_admin'])) {
            return true;
        }

        // Referees can update status of matches they are assigned to
        if ($user->role === 'referee' && $match->referee === $user->name) {
            return true;
        }

        // Organizers can update status of matches in their association
        if ($user->role === 'organizer') {
            return $user->association_id === $this->getMatchAssociationId($match);
        }

        return false;
    }

    /**
     * Helper method to safely get the association ID from the match's competition
     */
    private function getMatchAssociationId(MatchModel $match): ?int
    {
        // Always load the competition relationship to ensure association_id is available
        if (!$match->relationLoaded('competition') || !$match->competition) {
            $match->load('competition');
        }
        return $match->competition?->association_id;
    }
} 