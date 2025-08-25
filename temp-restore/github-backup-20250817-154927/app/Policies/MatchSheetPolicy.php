<?php

namespace App\Policies;

use App\Models\MatchSheet;
use App\Models\User;

class MatchSheetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin', 'referee', 'club_admin', 'team_official']);
    }

    public function view(User $user, MatchSheet $matchSheet): bool
    {
        // System admin and association admin can view all match sheets
        if ($user->hasAnyRole(['system_admin', 'association_admin'])) {
            return true;
        }

        // Referees can view match sheets they're assigned to
        if ($user->hasRole('referee')) {
            return $matchSheet->referee_id === $user->id ||
                   $matchSheet->main_referee_id === $user->id ||
                   $matchSheet->assistant_referee_1_id === $user->id ||
                   $matchSheet->assistant_referee_2_id === $user->id ||
                   $matchSheet->fourth_official_id === $user->id ||
                   $matchSheet->var_referee_id === $user->id ||
                   $matchSheet->var_assistant_id === $user->id ||
                   $matchSheet->assigned_referee_id === $user->id;
        }

        // Club admin and team officials can view match sheets for their teams
        if ($user->hasAnyRole(['club_admin', 'team_official'])) {
            return $matchSheet->match->home_team_id === $user->club_id ||
                   $matchSheet->match->away_team_id === $user->club_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin', 'referee']);
    }

    public function update(User $user, MatchSheet $matchSheet): bool
    {
        // System admin and association admin can update all match sheets
        if ($user->hasAnyRole(['system_admin', 'association_admin'])) {
            return true;
        }

        // Referees can update match sheets they're assigned to
        if ($user->hasRole('referee')) {
            return $matchSheet->referee_id === $user->id ||
                   $matchSheet->main_referee_id === $user->id ||
                   $matchSheet->assistant_referee_1_id === $user->id ||
                   $matchSheet->assistant_referee_2_id === $user->id ||
                   $matchSheet->fourth_official_id === $user->id ||
                   $matchSheet->var_referee_id === $user->id ||
                   $matchSheet->var_assistant_id === $user->id ||
                   $matchSheet->assigned_referee_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, MatchSheet $matchSheet): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin']);
    }

    public function submit(User $user, MatchSheet $matchSheet): bool
    {
        // Only referees can submit match sheets
        if (!$user->hasRole('referee')) {
            return false;
        }

        // Referee must be assigned to this match sheet
        return $matchSheet->referee_id === $user->id ||
               $matchSheet->main_referee_id === $user->id ||
               $matchSheet->assigned_referee_id === $user->id;
    }

    public function validate(User $user, MatchSheet $matchSheet): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin']);
    }

    public function reject(User $user, MatchSheet $matchSheet): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin']);
    }

    public function assignReferee(User $user, MatchSheet $matchSheet): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin']);
    }

    public function signByTeam(User $user, MatchSheet $matchSheet): bool
    {
        // Team officials can sign for their team
        if ($user->hasRole('team_official')) {
            return $matchSheet->match->home_team_id === $user->club_id ||
                   $matchSheet->match->away_team_id === $user->club_id;
        }

        return false;
    }

    public function signByReferee(User $user, MatchSheet $matchSheet): bool
    {
        // Referees can sign match sheets they're assigned to
        if (!$user->hasRole('referee')) {
            return false;
        }

        return $matchSheet->referee_id === $user->id ||
               $matchSheet->main_referee_id === $user->id ||
               $matchSheet->assigned_referee_id === $user->id;
    }

    public function signByObserver(User $user, MatchSheet $matchSheet): bool
    {
        // Match observers can sign match sheets they're assigned to
        if (!$user->hasRole('match_observer')) {
            return false;
        }

        return $matchSheet->match_observer_id === $user->id;
    }

    public function signLineup(User $user, MatchSheet $matchSheet): bool
    {
        // Team officials can sign lineups for their team
        if ($user->hasRole('team_official')) {
            return $matchSheet->match->home_team_id === $user->club_id ||
                   $matchSheet->match->away_team_id === $user->club_id;
        }

        return false;
    }

    public function signPostMatch(User $user, MatchSheet $matchSheet): bool
    {
        // Team officials can sign post-match for their team
        if ($user->hasRole('team_official')) {
            return $matchSheet->match->home_team_id === $user->club_id ||
                   $matchSheet->match->away_team_id === $user->club_id;
        }

        return false;
    }

    public function faValidate(User $user, MatchSheet $matchSheet): bool
    {
        return $user->hasRole('association_admin');
    }

    public function lockLineups(User $user, MatchSheet $matchSheet): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin', 'referee']);
    }

    public function unlockLineups(User $user, MatchSheet $matchSheet): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin']);
    }

    public function lockMatchEvents(User $user, MatchSheet $matchSheet): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin', 'referee']);
    }

    public function unlockMatchEvents(User $user, MatchSheet $matchSheet): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin']);
    }

    public function transitionStage(User $user, MatchSheet $matchSheet): bool
    {
        return $user->hasAnyRole(['system_admin', 'association_admin', 'referee']);
    }

    public function export(User $user, MatchSheet $matchSheet): bool
    {
        return $this->view($user, $matchSheet);
    }
} 