<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RBACController extends Controller
{
    /**
     * Afficher le tableau de bord RBAC
     */
    public function index()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
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
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
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
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
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
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
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
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
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
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $permissions = Permission::all()->groupBy('module');
        $modules = Permission::distinct()->pluck('module')->filter();

        return view('admin.rbac.permissions', compact('permissions', 'modules'));
    }

    /**
     * Créer une nouvelle permission
     */
    public function createPermission(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
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
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $users = User::all();
        $roles = Role::where('is_active', true)->get();

        return view('admin.rbac.users', compact('users', 'roles'));
    }

    /**
     * Assigner un rôle à un utilisateur
     */
    public function assignRole(Request $request, $userId)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
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
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
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
}
