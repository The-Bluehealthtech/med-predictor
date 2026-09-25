<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view) {
            $user = auth()->user();

            $view->with([
                'canAccessPlayerRegistration' => $user && $user->can('access-player-registration'),
                'canAccessCompetitionManagement' => $user && $user->can('access-competition-management'),
                'canAccessLeagueChampionship' => $user && $user->can('access-league-championship'),
                'canAccessHealthcare' => $user && $user->can('access-healthcare'),
                'canAccessFifaConnect' => $user && $user->can('access-fifa-connect'),
                'canAccessBackOffice' => $user && $user->can('access-back-office'),
                'canAccessUserManagement' => $user && $user->can('access-user-management'),
                'canAccessRoleManagement' => $user && $user->can('access-role-management'),
                'canAccessAuditTrail' => $user && $user->can('access-audit-trail'),
                'canAccessClubManagement' => $user && $user->can('access-club-management'),
                'canAccessRefereePortal' => $user && $user->can('access-referee-portal'),
                'canAccessPlayerDashboard' => $user && $user->can('access-player-dashboard'),
                'canAccessRankings' => $user && $user->can('access-rankings'),
                'canAccessMatchSheet' => $user && $user->can('access-match-sheet'),
                'canAccessMedicalPredictions' => $user && $user->can('access-medical-predictions'),
                'canAccessHealthRecords' => $user && $user->can('access-health-records'),
                'canAccessCalendarManagement' => $user && $user->can('access-calendar-management'),
                'canAccessContentManagement' => $user && $user->can('access-content-management'),
                'canAccessLicenseManagement' => $user && $user->can('access-license-management'),
                'canAccessLogs' => $user && $user->can('access-logs'),
                'canAccessSettings' => $user && $user->can('access-settings'),
                'canAccessSeasons' => $user && $user->can('access-seasons'),
                'canAccessLicenseTypes' => $user && $user->can('access-license-types'),
                'canAccessAuditTrailView' => $user && $user->can('access-audit-trail-view'),
                'canAccessComplianceReport' => $user && $user->can('access-compliance-report'),
                'canAccessLicenseQueue' => $user && $user->can('access-license-queue'),
                'canAccessClubPlayerManagement' => $user && $user->can('access-club-player-management'),
                'canAccessLineupManagement' => $user && $user->can('access-lineup-management'),
                'canAccessTeamManagement' => $user && $user->can('access-team-management'),
                'canAccessPlayerImport' => $user && $user->can('access-player-import'),
                'canAccessPlayerList' => $user && $user->can('access-player-list'),
                'canAccessRegistrationRequests' => $user && $user->can('access-registration-requests'),
            ]);
        });
    }
} 