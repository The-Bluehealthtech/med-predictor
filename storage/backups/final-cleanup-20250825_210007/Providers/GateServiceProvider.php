<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class GateServiceProvider extends ServiceProvider
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
        // Gates pour l'accès aux modules
        Gate::define('access-player-registration', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin']);
        });

        Gate::define('access-competition-management', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-league-championship', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin', 'referee']);
        });

        Gate::define('access-healthcare', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'medical_staff']);
        });

        Gate::define('access-fifa-connect', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-back-office', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-user-management', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-role-management', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-audit-trail', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-club-management', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin']);
        });

        Gate::define('access-referee-portal', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'referee']);
        });

        Gate::define('access-player-dashboard', function ($user) {
            return in_array($user->role, ['player', 'system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-rankings', function ($user) {
            return true; // Accès public
        });

        Gate::define('access-match-sheet', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'referee']);
        });

        Gate::define('access-medical-predictions', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'medical_staff']);
        });

        Gate::define('access-health-records', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'medical_staff']);
        });

        Gate::define('access-calendar-management', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-content-management', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-license-management', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin']);
        });

        Gate::define('access-logs', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-settings', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-seasons', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-license-types', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-audit-trail-view', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-compliance-report', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin']);
        });

        Gate::define('access-license-queue', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('access-club-player-management', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin']);
        });

        Gate::define('access-lineup-management', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin']);
        });

        Gate::define('access-team-management', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin']);
        });

        Gate::define('access-player-import', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin']);
        });

        Gate::define('access-player-list', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin']);
        });

        Gate::define('access-registration-requests', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });

        Gate::define('manage_contracts', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin', 'club_admin']);
        });

        Gate::define('manage_federations', function ($user) {
            return in_array($user->role, ['system_admin', 'association_admin', 'admin']);
        });
    }
} 