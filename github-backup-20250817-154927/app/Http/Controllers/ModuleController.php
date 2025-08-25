<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Competition;
use App\Models\Club;
use App\Models\Player;

class ModuleController extends Controller
{
    /**
     * Afficher le dashboard DTN
     */
    public function dtnDashboard()
    {
        // Vérifier les permissions
        if (!Auth::user() || !$this->hasDTNPermission(Auth::user())) {
            abort(403, 'Accès non autorisé au module DTN');
        }

        return view('modules.dtn.dashboard', [
            'user' => Auth::user(),
            'module' => 'dtn',
            'permissions' => $this->getDTNPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher les équipes nationales
     */
    public function dtnTeams()
    {
        if (!Auth::user() || !$this->hasDTNPermission(Auth::user(), 'dtn_teams_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.dtn.teams', [
            'user' => Auth::user(),
            'module' => 'dtn',
            'permissions' => $this->getDTNPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher les sélections internationales
     */
    public function dtnSelections()
    {
        if (!Auth::user() || !$this->hasDTNPermission(Auth::user(), 'dtn_selections_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.dtn.selections', [
            'user' => Auth::user(),
            'module' => 'dtn',
            'permissions' => $this->getDTNPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher les expatriés
     */
    public function dtnExpats()
    {
        if (!Auth::user() || !$this->hasDTNPermission(Auth::user(), 'dtn_expats_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.dtn.expats', [
            'user' => Auth::user(),
            'module' => 'dtn',
            'permissions' => $this->getDTNPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher l'interface médicale
     */
    public function dtnMedical()
    {
        if (!Auth::user() || !$this->hasDTNPermission(Auth::user(), 'dtn_medical_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.dtn.medical', [
            'user' => Auth::user(),
            'module' => 'dtn',
            'permissions' => $this->getDTNPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher la planification technique
     */
    public function dtnPlanning()
    {
        if (!Auth::user() || !$this->hasDTNPermission(Auth::user(), 'dtn_planning_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.dtn.planning', [
            'user' => Auth::user(),
            'module' => 'dtn',
            'permissions' => $this->getDTNPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher les rapports DTN
     */
    public function dtnReports()
    {
        if (!Auth::user() || !$this->hasDTNPermission(Auth::user(), 'dtn_reports_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.dtn.reports', [
            'user' => Auth::user(),
            'module' => 'dtn',
            'permissions' => $this->getDTNPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher les paramètres DTN
     */
    public function dtnSettings()
    {
        if (!Auth::user() || !$this->hasDTNPermission(Auth::user(), 'dtn_settings')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.dtn.settings', [
            'user' => Auth::user(),
            'module' => 'dtn',
            'permissions' => $this->getDTNPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher le dashboard RPM
     */
    public function rpmDashboard()
    {
        if (!Auth::user() || !$this->hasRPMPermission(Auth::user())) {
            abort(403, 'Accès non autorisé au module RPM');
        }

        return view('modules.rpm.dashboard', [
            'user' => Auth::user(),
            'module' => 'rpm',
            'permissions' => $this->getRPMPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher le calendrier d'entraînement
     */
    public function rpmCalendar()
    {
        if (!Auth::user() || !$this->hasRPMPermission(Auth::user(), 'rpm_calendar_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.rpm.calendar', [
            'user' => Auth::user(),
            'module' => 'rpm',
            'permissions' => $this->getRPMPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher les sessions d'entraînement
     */
    public function rpmSessions()
    {
        if (!Auth::user() || !$this->hasRPMPermission(Auth::user(), 'rpm_sessions_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.rpm.sessions', [
            'user' => Auth::user(),
            'module' => 'rpm',
            'permissions' => $this->getRPMPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher les matchs
     */
    public function rpmMatches()
    {
        if (!Auth::user() || !$this->hasRPMPermission(Auth::user(), 'rpm_matches_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.rpm.matches', [
            'user' => Auth::user(),
            'module' => 'rpm',
            'permissions' => $this->getRPMPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher le monitoring de charge
     */
    public function rpmLoad()
    {
        if (!Auth::user() || !$this->hasRPMPermission(Auth::user(), 'rpm_load_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.rpm.load', [
            'user' => Auth::user(),
            'module' => 'rpm',
            'permissions' => $this->getRPMPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher le suivi de présence
     */
    public function rpmAttendance()
    {
        if (!Auth::user() || !$this->hasRPMPermission(Auth::user(), 'rpm_attendance_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.rpm.attendance', [
            'user' => Auth::user(),
            'module' => 'rpm',
            'permissions' => $this->getRPMPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher les rapports RPM
     */
    public function rpmReports()
    {
        if (!Auth::user() || !$this->hasRPMPermission(Auth::user(), 'rpm_reports_view')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.rpm.reports', [
            'user' => Auth::user(),
            'module' => 'rpm',
            'permissions' => $this->getRPMPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher la synchronisation RPM
     */
    public function rpmSync()
    {
        if (!Auth::user() || !$this->hasRPMPermission(Auth::user(), 'rpm_sync')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.rpm.sync', [
            'user' => Auth::user(),
            'module' => 'rpm',
            'permissions' => $this->getRPMPermissions(Auth::user())
        ]);
    }

    /**
     * Afficher les paramètres RPM
     */
    public function rpmSettings()
    {
        if (!Auth::user() || !$this->hasRPMPermission(Auth::user(), 'rpm_settings')) {
            abort(403, 'Accès non autorisé');
        }

        return view('modules.rpm.settings', [
            'user' => Auth::user(),
            'module' => 'rpm',
            'permissions' => $this->getRPMPermissions(Auth::user())
        ]);
    }

    public function competitionsIndex()
    {
        $user = Auth::user();
        
        // Récupérer les compétitions selon le rôle de l'utilisateur
        $competitions = collect();
        
        if ($user && in_array($user->role, ['system_admin', 'admin'])) {
            // System admin et admin voient toutes les compétitions
            $competitions = Competition::with(['association'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user && in_array($user->role, ['association_admin', 'association_registrar', 'association_medical'])) {
            // Les utilisateurs d'association voient seulement leurs compétitions
            $competitions = Competition::where('association_id', $user->association_id)
                ->with(['association'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Par défaut, afficher toutes les compétitions pour les utilisateurs non authentifiés ou autres rôles
            $competitions = Competition::with(['association'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        // Statistiques
        $stats = [
            'total_competitions' => Competition::count(),
            'active_competitions' => Competition::where('status', 'active')->count(),
            'total_players' => Player::count(),
            'total_clubs' => Club::count(),
        ];
        
        return view('modules.competitions.index', compact('competitions', 'stats'));
    }

    /**
     * Vérifier les permissions DTN
     */
    private function hasDTNPermission($user, $permission = 'dtn_view')
    {
        // System admin a accès à tout
        if ($user->role === 'system_admin') {
            return true;
        }

        // DTN manager a accès à tout le module DTN
        if ($user->role === 'dtn_manager') {
            return true;
        }

        // Vérifier les permissions spécifiques
        $dtnPermissions = [
            'dtn_view', 'dtn_teams_view', 'dtn_teams_create', 'dtn_teams_edit',
            'dtn_selections_view', 'dtn_selections_create', 'dtn_selections_edit', 'dtn_selections_manage',
            'dtn_expats_view', 'dtn_medical_view', 'dtn_planning_view', 'dtn_reports_view',
            'dtn_settings', 'dtn_admin'
        ];

        return in_array($permission, $dtnPermissions);
    }

    /**
     * Vérifier les permissions RPM
     */
    private function hasRPMPermission($user, $permission = 'rpm_view')
    {
        // System admin a accès à tout
        if ($user->role === 'system_admin') {
            return true;
        }

        // RPM manager a accès à tout le module RPM
        if ($user->role === 'rpm_manager') {
            return true;
        }

        // Vérifier les permissions spécifiques
        $rpmPermissions = [
            'rpm_view', 'rpm_calendar_view', 'rpm_sessions_view', 'rpm_sessions_create', 'rpm_sessions_edit',
            'rpm_matches_view', 'rpm_matches_create', 'rpm_matches_edit', 'rpm_load_view',
            'rpm_attendance_view', 'rpm_reports_view', 'rpm_sync', 'rpm_settings', 'rpm_admin'
        ];

        return in_array($permission, $rpmPermissions);
    }

    /**
     * Obtenir les permissions DTN de l'utilisateur
     */
    private function getDTNPermissions($user)
    {
        $permissions = [];

        if ($user->role === 'system_admin' || $user->role === 'dtn_manager') {
            $permissions = [
                'dtn_view', 'dtn_teams_view', 'dtn_teams_create', 'dtn_teams_edit',
                'dtn_selections_view', 'dtn_selections_create', 'dtn_selections_edit', 'dtn_selections_manage',
                'dtn_expats_view', 'dtn_medical_view', 'dtn_planning_view', 'dtn_reports_view',
                'dtn_settings', 'dtn_admin'
            ];
        }

        return $permissions;
    }

    /**
     * Obtenir les permissions RPM de l'utilisateur
     */
    private function getRPMPermissions($user)
    {
        $permissions = [];

        if ($user->role === 'system_admin' || $user->role === 'rpm_manager') {
            $permissions = [
                'rpm_view', 'rpm_calendar_view', 'rpm_sessions_view', 'rpm_sessions_create', 'rpm_sessions_edit',
                'rpm_matches_view', 'rpm_matches_create', 'rpm_matches_edit', 'rpm_load_view',
                'rpm_attendance_view', 'rpm_reports_view', 'rpm_sync', 'rpm_settings', 'rpm_admin'
            ];
        }

        return $permissions;
    }
} 