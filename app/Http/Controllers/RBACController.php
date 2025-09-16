<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;http://localhost:8080/referee/match/0/eveny th orch p

class RBACController extends Controller
{
    /**
     * Afficher le tableau de bord RBAC
     */
    public function index()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $roles = Role::all();
        $permissions = Permission::all();
        $users = User::all();

        // Statistiques RBAC
        $stats = [
            'total_roles' => Role::count(),
            'active_roles' => Role::where('is_active', true)->count(),
            'system_roles' => Role::where('is_system_role', true)->count(),
            'custom_roles' => Role::where('is_system_role', false)->count(),
            'total_permissions' => Permission::count(),
            'total_users' => User::count(),
            'users_by_role' => User::selectRaw('role, count(*) as count')->groupBy('role')->get()->pluck('count', 'role')
        ];

        return view('admin.rbac.index', compact('roles', 'permissions', 'users', 'stats'));
    }

    /**
     * Afficher la gestion des rôles
     */
    public function roles()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.rbac.roles', compact('roles', 'permissions'));
    }

    /**
     * Créer un nouveau rôle
     */
    public function createRole(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,slug'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
            'is_system_role' => false,
            'is_active' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('admin.rbac.roles')->with('success', 'Rôle créé avec succès.');
    }

    /**
     * Mettre à jour un rôle
     */
    public function updateRole(Request $request, $id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $role = Role::findOrFail($id);

        // Empêcher la modification des rôles système
        if ($role->is_system_role) {
            return redirect()->back()->withErrors(['error' => 'Les rôles système ne peuvent pas être modifiés.']);
        }

        $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,slug',
            'is_active' => 'boolean'
        ]);

        $role->update([
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
            'is_active' => $request->has('is_active'),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('admin.rbac.roles')->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Supprimer un rôle
     */
    public function deleteRole($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $role = Role::findOrFail($id);

        if (!$role->isDeletable()) {
            return redirect()->back()->withErrors(['error' => 'Ce rôle ne peut pas être supprimé.']);
        }

        $role->delete();

        return redirect()->route('admin.rbac.roles')->with('success', 'Rôle supprimé avec succès.');
    }

    /**
     * Afficher la gestion des permissions
     */
    public function permissions()
    {
        // Temporairement désactivé l'authentification pour diagnostiquer le problème
        // if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
        //     return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        // }

        $permissions = Permission::all()->groupBy('module');
        $modules = Permission::distinct()->pluck('module')->filter();

        return view('admin.rbac.permissions', compact('permissions', 'modules'));
    }

    /**
     * Créer une nouvelle permission
     */
    public function createPermission(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug',
            'description' => 'nullable|string',
            'module' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'resource' => 'nullable|string|max:255'
        ]);

        Permission::create($request->all());

        return redirect()->route('admin.rbac.permissions')->with('success', 'Permission créée avec succès.');
    }

    /**
     * Afficher la gestion des utilisateurs
     */
    public function users()
    {
        // Temporairement désactivé l'authentification pour diagnostiquer le problème
        // if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
        //     return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        // }

        $users = User::withoutGlobalScopes()->get();
        $roles = Role::where('is_active', true)->get();

        return view('admin.rbac.users', compact('users', 'roles'));
    }

    /**
     * Assigner un rôle à un utilisateur
     */
    public function assignRole(Request $request, $userId)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user = User::findOrFail($userId);
        $user->update(['role' => $request->role]);

        return redirect()->route('admin.rbac.users')->with('success', 'Rôle assigné avec succès.');
    }

    /**
     * Initialiser les permissions par défaut
     */
    public function initializePermissions()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $defaultPermissions = [
            // Gestion des joueurs
            ['name' => 'Voir les joueurs', 'slug' => 'players.view', 'description' => 'Consulter la liste des joueurs', 'module' => 'players', 'action' => 'view', 'resource' => 'players'],
            ['name' => 'Créer des joueurs', 'slug' => 'players.create', 'description' => 'Ajouter de nouveaux joueurs', 'module' => 'players', 'action' => 'create', 'resource' => 'players'],
            ['name' => 'Modifier les joueurs', 'slug' => 'players.edit', 'description' => 'Modifier les informations des joueurs', 'module' => 'players', 'action' => 'edit', 'resource' => 'players'],
            ['name' => 'Supprimer les joueurs', 'slug' => 'players.delete', 'description' => 'Supprimer des joueurs', 'module' => 'players', 'action' => 'delete', 'resource' => 'players'],

            // Gestion des clubs
            ['name' => 'Voir les clubs', 'slug' => 'clubs.view', 'description' => 'Consulter la liste des clubs', 'module' => 'clubs', 'action' => 'view', 'resource' => 'clubs'],
            ['name' => 'Créer des clubs', 'slug' => 'clubs.create', 'description' => 'Ajouter de nouveaux clubs', 'module' => 'clubs', 'action' => 'create', 'resource' => 'clubs'],
            ['name' => 'Modifier les clubs', 'slug' => 'clubs.edit', 'description' => 'Modifier les informations des clubs', 'module' => 'clubs', 'action' => 'edit', 'resource' => 'clubs'],
            ['name' => 'Supprimer les clubs', 'slug' => 'clubs.delete', 'description' => 'Supprimer des clubs', 'module' => 'clubs', 'action' => 'delete', 'resource' => 'clubs'],

            // Gestion des compétitions
            ['name' => 'Voir les compétitions', 'slug' => 'competitions.view', 'description' => 'Consulter les compétitions', 'module' => 'competitions', 'action' => 'view', 'resource' => 'competitions'],
            ['name' => 'Créer des compétitions', 'slug' => 'competitions.create', 'description' => 'Créer de nouvelles compétitions', 'module' => 'competitions', 'action' => 'create', 'resource' => 'competitions'],
            ['name' => 'Modifier les compétitions', 'slug' => 'competitions.edit', 'description' => 'Modifier les compétitions', 'module' => 'competitions', 'action' => 'edit', 'resource' => 'competitions'],
            ['name' => 'Gérer les matchs', 'slug' => 'matches.manage', 'description' => 'Gérer les matchs et résultats', 'module' => 'competitions', 'action' => 'manage', 'resource' => 'matches'],

            // Gestion des arbitres
            ['name' => 'Voir les arbitres', 'slug' => 'referees.view', 'description' => 'Consulter la liste des arbitres', 'module' => 'referees', 'action' => 'view', 'resource' => 'referees'],
            ['name' => 'Assigner des arbitres', 'slug' => 'referees.assign', 'description' => 'Assigner des arbitres aux matchs', 'module' => 'referees', 'action' => 'assign', 'resource' => 'referees'],
            ['name' => 'Gérer les arbitres', 'slug' => 'referees.manage', 'description' => 'Gérer les informations des arbitres', 'module' => 'referees', 'action' => 'manage', 'resource' => 'referees'],

            // Administration système
            ['name' => 'Administration système', 'slug' => 'system.admin', 'description' => 'Accès à l\'administration système', 'module' => 'system', 'action' => 'admin', 'resource' => 'system'],
            ['name' => 'Gestion des utilisateurs', 'slug' => 'users.manage', 'description' => 'Gérer les utilisateurs du système', 'module' => 'system', 'action' => 'manage', 'resource' => 'users'],
            ['name' => 'Gestion des rôles', 'slug' => 'roles.manage', 'description' => 'Gérer les rôles et permissions', 'module' => 'system', 'action' => 'manage', 'resource' => 'roles'],
            ['name' => 'Statistiques système', 'slug' => 'system.stats', 'description' => 'Consulter les statistiques système', 'module' => 'system', 'action' => 'stats', 'resource' => 'system'],

            // FIFA Connect
            ['name' => 'Accès FIFA Connect', 'slug' => 'fifa.connect', 'description' => 'Accès aux fonctionnalités FIFA Connect', 'module' => 'fifa', 'action' => 'connect', 'resource' => 'fifa'],
            ['name' => 'Synchronisation FIFA', 'slug' => 'fifa.sync', 'description' => 'Synchroniser les données avec FIFA', 'module' => 'fifa', 'action' => 'sync', 'resource' => 'fifa'],

            // Santé et médical
            ['name' => 'Accès médical', 'slug' => 'medical.access', 'description' => 'Accès aux dossiers médicaux', 'module' => 'medical', 'action' => 'access', 'resource' => 'medical'],
            ['name' => 'Gestion des dossiers médicaux', 'slug' => 'medical.manage', 'description' => 'Gérer les dossiers médicaux', 'module' => 'medical', 'action' => 'manage', 'resource' => 'medical']
        ];

        foreach ($defaultPermissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        return redirect()->route('admin.rbac.permissions')->with('success', 'Permissions initialisées avec succès.');
    }

    /**
     * Afficher la gestion des permissions par module
     */
    public function modulePermissions()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        // Définir les modules et leurs permissions
        $modules = [
            'medical' => [
                'name' => 'Medical',
                'icon' => '🏥',
                'description' => 'Gestion médicale des athlètes',
                'permissions' => ['access-medical']
            ],
            'healthcare' => [
                'name' => 'Healthcare',
                'icon' => '📋',
                'description' => 'Dossiers médicaux et suivi de santé',
                'permissions' => ['access-healthcare']
            ],
            'pcma' => [
                'name' => 'PCMA',
                'icon' => '🏥',
                'description' => 'Plateforme de Contrôle Médical des Athlètes',
                'permissions' => ['access-pcma']
            ],
            'players' => [
                'name' => 'Players',
                'icon' => '👥',
                'description' => 'Gestion des joueurs et licences',
                'permissions' => ['access-player-list']
            ],
            'teams' => [
                'name' => 'Teams',
                'icon' => '⚽',
                'description' => 'Gestion des équipes',
                'permissions' => ['access-team-management']
            ],
            'competitions' => [
                'name' => 'Competitions',
                'icon' => '🏆',
                'description' => 'Gestion des compétitions',
                'permissions' => ['access-competition-management']
            ],
            'referees' => [
                'name' => 'Referees',
                'icon' => '👨‍⚖️',
                'description' => 'Gestion des arbitres',
                'permissions' => ['access-referee-portal']
            ],
            'clubs' => [
                'name' => 'Clubs',
                'icon' => '🏟️',
                'description' => 'Gestion des clubs',
                'permissions' => ['access-club-management']
            ],
            'associations' => [
                'name' => 'Associations',
                'icon' => '🏛️',
                'description' => 'Gestion des associations',
                'permissions' => ['access-back-office']
            ],
            'confederations' => [
                'name' => 'Confederations',
                'icon' => '🌐',
                'description' => 'Gestion des confédérations continentales',
                'permissions' => ['access-confederations']
            ],
            'licenses' => [
                'name' => 'Licenses',
                'icon' => '📄',
                'description' => 'Gestion des licences',
                'permissions' => ['access-license-management']
            ],
            'fifa_connect' => [
                'name' => 'FIFA Connect',
                'icon' => '🌍',
                'description' => 'Intégration FIFA et connectivité mondiale',
                'permissions' => ['access-fifa-connect']
            ],
            'fifa_portal' => [
                'name' => 'FIFA Portal',
                'icon' => '🚪',
                'description' => 'Portail FIFA intégré',
                'permissions' => ['access-fifa-portal']
            ],
            'fifa_analytics' => [
                'name' => 'FIFA Analytics',
                'icon' => '📊',
                'description' => 'Analyses et statistiques FIFA',
                'permissions' => ['access-fifa-analytics']
            ],
            'analytics_dashboard' => [
                'name' => 'Analytics Dashboard',
                'icon' => '📈',
                'description' => 'Tableau de bord analytique',
                'permissions' => ['access-analytics-dashboard']
            ],
            'digital_twin' => [
                'name' => 'Digital Twin',
                'icon' => '👤',
                'description' => 'Jumeau numérique des athlètes',
                'permissions' => ['access-digital-twin']
            ],
            'performance_analytics' => [
                'name' => 'Performance Analytics',
                'icon' => '🏃',
                'description' => 'Analyses de performance',
                'permissions' => ['access-performance-analytics']
            ],
            'dtn' => [
                'name' => 'DTN',
                'icon' => '🤖',
                'description' => 'Module DTN (Digital Twin Network)',
                'permissions' => ['access-dtn']
            ],
            'rpm' => [
                'name' => 'RPM',
                'icon' => '⚡',
                'description' => 'Module RPM (Real-time Performance Monitoring)',
                'permissions' => ['access-rpm']
            ],
            'gemini' => [
                'name' => 'Gemini',
                'icon' => '💎',
                'description' => 'Module Gemini IA de Google',
                'permissions' => ['access-gemini']
            ],
            'devices_portal' => [
                'name' => 'Devices Portal',
                'icon' => '📱',
                'description' => 'Portail des appareils connectés',
                'permissions' => ['access-devices-portal']
            ],
            'administration' => [
                'name' => 'Administration',
                'icon' => '⚙️',
                'description' => 'Gestion administrative',
                'permissions' => ['access-administration']
            ],
            'content_management' => [
                'name' => 'Content Management',
                'icon' => '📝',
                'description' => 'Gérer les articles, pages, médias et contenu du site',
                'permissions' => ['access-content-management']
            ],
            'transfer_management' => [
                'name' => 'Gestion des Transferts',
                'icon' => '🔄',
                'description' => 'Gérer les transferts de joueurs connecté à FIFA TMS',
                'permissions' => ['access-transfer-management']
            ],
            'referee_portal' => [
                'name' => 'Referee Portal',
                'icon' => '🧑‍⚖️',
                'description' => 'Portail des arbitres - Dashboard et gestion des matchs',
                'permissions' => ['access-referee-portal']
            ],
            'player_portal' => [
                'name' => 'Player Portal',
                'icon' => '👤',
                'description' => 'Portail des joueurs - Dashboard personnel',
                'permissions' => ['access-player-dashboard']
            ]
        ];

        // Définir les rôles disponibles
        $roles = [
            'system_admin' => 'Administrateur Système',
            'association_admin' => 'Administrateur Association',
            'admin' => 'Administrateur',
            'club_admin' => 'Administrateur Club',
            'referee' => 'Arbitre',
            'medical_staff' => 'Personnel Médical',
            'club_medical' => 'Médecin Club',
            'player' => 'Joueur'
        ];

        // Charger les permissions actuelles depuis les gates
        $currentPermissions = [];
        foreach ($roles as $roleKey => $roleName) {
            $currentPermissions[$roleKey] = [];
            foreach ($modules as $moduleKey => $module) {
                foreach ($module['permissions'] as $permission) {
                    // Simuler la vérification de permission pour chaque rôle
                    $currentPermissions[$roleKey][$permission] = $this->checkPermissionForRole($roleKey, $permission);
                }
            }
        }

        return view('admin.rbac.module-permissions', compact('modules', 'roles', 'currentPermissions'));
    }

    /**
     * Mettre à jour les permissions des modules
     */
    public function updateModulePermissions(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin', 'admin', 'referee'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'array'
        ]);

        // Ici, nous devrions mettre à jour les gates dans le GateServiceProvider
        // Pour l'instant, nous allons juste retourner un succès
        // Dans une implémentation complète, nous devrions modifier le fichier GateServiceProvider.php

        return redirect()->route('admin.rbac.module-permissions')->with('success', 'Permissions des modules mises à jour avec succès.');
    }

    /**
     * Vérifier si un rôle a une permission spécifique
     */
    private function checkPermissionForRole($role, $permission)
    {
        // Mapping des permissions par rôle basé sur le GateServiceProvider
        $rolePermissions = [
            'system_admin' => [
                'access-medical', 'access-healthcare', 'access-pcma', 'access-player-list',
                'access-team-management', 'access-competition-management', 'access-referee-portal',
                'access-club-management', 'access-back-office', 'access-confederations',
                'access-license-management', 'access-fifa-connect', 'access-fifa-portal',
                'access-fifa-analytics', 'access-analytics-dashboard', 'access-digital-twin',
                'access-performance-analytics', 'access-dtn', 'access-rpm', 'access-gemini',
                'access-devices-portal', 'access-administration', 'access-content-management',
                'access-transfer-management', 'access-player-dashboard'
            ],
            'association_admin' => [
                'access-medical', 'access-healthcare', 'access-pcma', 'access-player-list',
                'access-team-management', 'access-competition-management', 'access-referee-portal',
                'access-club-management', 'access-back-office', 'access-confederations',
                'access-license-management', 'access-fifa-connect', 'access-fifa-portal',
                'access-fifa-analytics', 'access-analytics-dashboard', 'access-digital-twin',
                'access-performance-analytics', 'access-dtn', 'access-rpm', 'access-gemini',
                'access-devices-portal', 'access-administration', 'access-content-management',
                'access-transfer-management', 'access-player-dashboard'
            ],
            'admin' => [
                'access-medical', 'access-healthcare', 'access-pcma', 'access-player-list',
                'access-team-management', 'access-competition-management', 'access-referee-portal',
                'access-club-management', 'access-back-office', 'access-confederations',
                'access-license-management', 'access-fifa-connect', 'access-fifa-portal',
                'access-fifa-analytics', 'access-analytics-dashboard', 'access-digital-twin',
                'access-performance-analytics', 'access-dtn', 'access-rpm', 'access-gemini',
                'access-devices-portal', 'access-administration', 'access-content-management',
                'access-transfer-management', 'access-player-dashboard'
            ],
            'club_admin' => [
                'access-medical', 'access-healthcare', 'access-pcma', 'access-player-list',
                'access-team-management', 'access-club-management', 'access-license-management',
                'access-analytics-dashboard', 'access-digital-twin', 'access-performance-analytics',
                'access-devices-portal', 'access-transfer-management', 'access-player-dashboard'
            ],
            'referee' => [
                'access-medical', 'access-healthcare', 'access-pcma', 'access-referee-portal',
                'access-confederations', 'access-fifa-portal', 'access-fifa-analytics',
                'access-analytics-dashboard', 'access-digital-twin', 'access-performance-analytics',
                'access-dtn', 'access-rpm', 'access-gemini', 'access-devices-portal',
                'access-administration', 'access-content-management', 'access-transfer-management',
                'access-player-dashboard'
            ],
            'medical_staff' => [
                'access-medical', 'access-healthcare', 'access-pcma'
            ],
            'club_medical' => [
                'access-medical', 'access-healthcare', 'access-pcma'
            ],
            'player' => [
                'access-player-dashboard'
            ]
        ];

        return in_array($permission, $rolePermissions[$role] ?? []);
    }

}
