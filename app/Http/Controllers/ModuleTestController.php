<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class ModuleTestController extends Controller
{
    public function testModules()
    {
        $modules = [
            'appointments' => ['index'],
            'association' => ['index', 'registration'],
            'association/fraud-detection' => ['index', 'alerts', 'analysis', 'reports', 'settings'],
            'associations' => ['index', 'edit', 'show-simple', 'show'],
            'clinical' => ['support-dashboard'],
            'club' => ['player-licenses/index'],
            'clubs' => ['index', 'edit', 'show', 'test'],
            'competitions' => ['index', 'test'],
            'confederations' => ['index', 'edit', 'show'],
            'dataset' => ['analytics'],
            'device-connections' => ['index'],
            'dtn' => ['dashboard', 'expats', 'index', 'medical', 'planning', 'reports', 'selections', 'settings', 'teams'],
            'fifa' => ['dashboard'],
            'fixtures' => ['index'],
            'healthcare' => ['dashboard', 'index', 'predictions'],
            'healthcare/records' => ['edit', 'show'],
            'licenses' => ['index', 'validation'],
            'medical' => ['athlete-edit', 'athlete-profile', 'athlete', 'index'],
            'pcma' => ['fraud-detection'],
            'performances' => ['index'],
            'player-passports' => ['index'],
            'player-registration' => ['bulk-import', 'create-stakeholder', 'create', 'dashboard', 'edit', 'health-records', 'index', 'show'],
            'players' => ['index', 'registration'],
            'portal' => ['dashboard'],
            'profile' => ['show'],
            'rankings' => ['index'],
            'referee' => ['dashboard'],
            'referees' => ['index'],
            'role-management' => ['create', 'edit', 'index', 'show'],
            'rpm' => ['attendance', 'calendar', 'dashboard', 'load', 'matches', 'reports', 'sessions', 'settings', 'sync'],
            'secretary' => ['dashboard'],
            'teams' => ['create', 'edit', 'index', 'show'],
            'user-management' => ['create', 'dashboard', 'edit', 'index', 'show']
        ];

        $results = [];
        $baseUrl = url('/');

        foreach ($modules as $module => $views) {
            foreach ($views as $view) {
                $viewPath = "modules.{$module}.{$view}";
                $status = $this->testView($viewPath);
                $results[] = [
                    'module' => $module,
                    'view' => $view,
                    'view_path' => $viewPath,
                    'status' => $status['status'],
                    'error' => $status['error'] ?? null,
                    'url' => $this->getModuleUrl($module, $view)
                ];
            }
        }

        return view('module-test-results', compact('results'));
    }

    private function testView($viewPath)
    {
        try {
            if (View::exists($viewPath)) {
                return ['status' => 'OK', 'error' => null];
            } else {
                return ['status' => 'VIEW_NOT_FOUND', 'error' => 'View file does not exist'];
            }
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'error' => $e->getMessage()];
        }
    }

    private function getModuleUrl($module, $view)
    {
        // Mapping des modules vers leurs routes
        $routeMap = [
            'appointments' => 'appointments.index',
            'association' => 'association.index',
            'associations' => 'associations.index',
            'clubs' => 'clubs.index',
            'competitions' => 'competitions.index',
            'confederations' => 'confederations.index',
            'fixtures' => 'fixtures.index',
            'healthcare' => 'healthcare.dashboard',
            'licenses' => 'licenses.index',
            'medical' => 'medical.index',
            'performances' => 'performances.index',
            'player-passports' => 'player-passports.index',
            'player-registration' => 'player-registration.index',
            'players' => 'players.index',
            'portal' => 'portal.dashboard',
            'profile' => 'profile.show',
            'rankings' => 'rankings.index',
            'referee' => 'referee.dashboard',
            'referees' => 'referees.index',
            'role-management' => 'role-management.index',
            'rpm' => 'rpm.dashboard',
            'secretary' => 'secretary.dashboard',
            'teams' => 'teams.index',
            'user-management' => 'user-management.index'
        ];

        if (isset($routeMap[$module])) {
            try {
                return route($routeMap[$module]);
            } catch (\Exception $e) {
                return 'Route not found: ' . $routeMap[$module];
            }
        }

        return 'No route mapped';
    }
}
