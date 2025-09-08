<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RBACService;
use App\Models\User;

class ModulesController extends Controller
{
    protected RBACService $rbacService;

    public function __construct(RBACService $rbacService)
    {
        $this->rbacService = $rbacService;
        $this->middleware('auth');
    }

    /**
     * Display the modules index with RBAC filtering
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $footballType = $request->get('footballType', 'association');
        
        // Get all available modules with their required permissions
        $allModules = $this->getAllModules();
        
        // Filter modules based on user permissions
        $filteredModules = $this->filterModulesByPermissions($user, $allModules);
        
        return view('modules.index', [
            'footballType' => $footballType,
            'modules' => $filteredModules
        ]);
    }

    /**
     * Get all available modules with their required permissions
     */
    private function getAllModules(): array
    {
        return [
            // 🏥 SANTÉ & MÉDECINE
            [
                'name' => 'Medical',
                'description' => 'Gestion médicale des athlètes, vaccinations, et dossiers de santé',
                'icon' => '🏥',
                'route' => 'modules.medical.index',
                'status' => 'active',
                'color' => 'red',
                'category' => 'health',
                'required_permissions' => ['healthcare_access', 'health_record_view']
            ],
            [
                'name' => 'Healthcare',
                'description' => 'Dossiers médicaux et suivi de santé',
                'icon' => '📋',
                'route' => 'modules.healthcare.index',
                'status' => 'active',
                'color' => 'red',
                'category' => 'health',
                'required_permissions' => ['healthcare_access', 'health_record_view']
            ],
            [
                'name' => 'PCMA',
                'description' => 'Plateforme de Contrôle Médical des Athlètes',
                'icon' => '🏥',
                'route' => 'pcma.index',
                'status' => 'active',
                'color' => 'red',
                'category' => 'health',
                'required_permissions' => ['healthcare_access', 'health_record_view']
            ],
            
            // ⚽ GESTION DU FOOTBALL
            [
                'name' => 'Players',
                'description' => 'Gestion des joueurs et licences',
                'icon' => '👥',
                'route' => 'modules.players.index',
                'status' => 'active',
                'color' => 'green',
                'category' => 'sport',
                'required_permissions' => ['player_registration_access', 'player_view']
            ],
            [
                'name' => 'Teams',
                'description' => 'Gestion des équipes',
                'icon' => '⚽',
                'route' => 'modules.teams.index',
                'status' => 'active',
                'color' => 'green',
                'category' => 'sport',
                'required_permissions' => ['team_management', 'team_view']
            ],
            [
                'name' => 'Competitions',
                'description' => 'Gestion des compétitions',
                'icon' => '🏆',
                'route' => 'modules.competitions.index',
                'status' => 'active',
                'color' => 'green',
                'category' => 'sport',
                'required_permissions' => ['competition_management_access', 'competition_view']
            ],
            [
                'name' => 'Referees',
                'description' => 'Gestion des arbitres',
                'icon' => '👨‍⚖️',
                'route' => 'modules.referees.index',
                'status' => 'active',
                'color' => 'green',
                'category' => 'sport',
                'required_permissions' => ['referee_management', 'referee_view']
            ],
            
            // 🏢 ORGANISATIONS
            [
                'name' => 'Clubs',
                'description' => 'Gestion des clubs',
                'icon' => '🏟️',
                'route' => 'modules.clubs.index',
                'status' => 'active',
                'color' => 'blue',
                'category' => 'institutional',
                'required_permissions' => ['club_management', 'club_view']
            ],
            [
                'name' => 'Associations',
                'description' => 'Gestion des associations',
                'icon' => '🏛️',
                'route' => 'modules.associations.index',
                'status' => 'active',
                'color' => 'blue',
                'category' => 'institutional',
                'required_permissions' => ['association_management', 'association_view']
            ],
            [
                'name' => 'Confederations',
                'description' => 'Gestion des confédérations continentales',
                'icon' => '🌐',
                'route' => 'modules.confederations.index',
                'status' => 'active',
                'color' => 'blue',
                'category' => 'institutional',
                'required_permissions' => ['association_management', 'association_view']
            ],
            
            // 📋 LICENCES & DOCUMENTS
            [
                'name' => 'Licenses',
                'description' => 'Gestion des licences',
                'icon' => '📄',
                'route' => 'modules.licenses.index',
                'status' => 'active',
                'color' => 'indigo',
                'category' => 'administration',
                'required_permissions' => ['player_registration_access', 'player_view']
            ],
            
            // 🌍 FIFA & CONNECTIVITÉ
            [
                'name' => 'FIFA Connect',
                'description' => 'Intégration FIFA et connectivité mondiale',
                'icon' => '🌍',
                'route' => 'fifa.dashboard',
                'status' => 'active',
                'color' => 'purple',
                'category' => 'portals',
                'required_permissions' => ['fifa_data_sync', 'fifa_data_view']
            ],
            [
                'name' => 'FIFA Portal',
                'description' => 'Portail FIFA intégré',
                'icon' => '🚪',
                'route' => 'fifa.portal.integrated',
                'status' => 'active',
                'color' => 'purple',
                'category' => 'portals',
                'required_permissions' => ['fifa_data_view']
            ],
            [
                'name' => 'FIFA Analytics',
                'description' => 'Analyses et statistiques FIFA',
                'icon' => '📊',
                'route' => 'fifa.analytics',
                'status' => 'active',
                'color' => 'purple',
                'category' => 'portals',
                'required_permissions' => ['fifa_data_view']
            ],
            
            // 📊 ANALYTICS & PERFORMANCE
            [
                'name' => 'Analytics Dashboard',
                'description' => 'Tableau de bord analytique',
                'icon' => '📈',
                'route' => 'analytics.dashboard',
                'status' => 'active',
                'color' => 'yellow',
                'category' => 'analytics',
                'required_permissions' => ['system_configuration']
            ],
            [
                'name' => 'Digital Twin',
                'description' => 'Jumeau numérique des athlètes',
                'icon' => '👤',
                'route' => 'analytics.digital-twin',
                'status' => 'active',
                'color' => 'yellow',
                'category' => 'analytics',
                'required_permissions' => ['system_configuration']
            ],
            [
                'name' => 'Performance Analytics',
                'description' => 'Analyses de performance',
                'icon' => '🏃',
                'route' => 'performances.analytics',
                'status' => 'active',
                'color' => 'yellow',
                'category' => 'analytics',
                'required_permissions' => ['system_configuration']
            ],
            
            // 🤖 IA & TECHNOLOGIE
            [
                'name' => 'DTN',
                'description' => 'Module DTN (Digital Twin Network)',
                'icon' => '🤖',
                'route' => 'dtn.index',
                'status' => 'active',
                'color' => 'purple',
                'category' => 'technology',
                'required_permissions' => ['system_configuration']
            ],
            [
                'name' => 'RPM',
                'description' => 'Module RPM (Real-time Performance Monitoring)',
                'icon' => '⚡',
                'route' => 'rpm.index',
                'status' => 'active',
                'color' => 'purple',
                'category' => 'technology',
                'required_permissions' => ['system_configuration']
            ],
            [
                'name' => 'Gemini',
                'description' => 'Module Gemini IA de Google',
                'icon' => '💎',
                'route' => 'gemini.index',
                'status' => 'active',
                'color' => 'purple',
                'category' => 'technology',
                'required_permissions' => ['system_configuration']
            ],
            
            // 📱 DEVICES & CONNECTIVITÉ
            [
                'name' => 'Devices Portal',
                'description' => 'Portail des appareils connectés',
                'icon' => '📱',
                'route' => 'portal.devices',
                'status' => 'active',
                'color' => 'blue',
                'category' => 'technology',
                'required_permissions' => ['system_configuration']
            ],
            
            // ⚙️ ADMINISTRATION
            [
                'name' => 'Administration',
                'description' => 'Gestion administrative',
                'icon' => '⚙️',
                'route' => 'modules.administration.index',
                'status' => 'active',
                'color' => 'gray',
                'category' => 'administration',
                'required_permissions' => ['system_configuration', 'admin_access']
            ],
            [
                'name' => 'Content Management',
                'description' => 'Gérer les articles, pages, médias et contenu du site',
                'icon' => '📝',
                'route' => 'admin.content-management.index',
                'status' => 'active',
                'color' => 'pink',
                'category' => 'administration',
                'required_permissions' => ['admin_access']
            ],
            [
                'name' => 'Gestion des Transferts',
                'description' => 'Gérer les transferts de joueurs connecté à FIFA TMS',
                'icon' => '🔄',
                'route' => 'admin.transfer-management.index',
                'status' => 'active',
                'color' => 'teal',
                'category' => 'administration',
                'required_permissions' => ['fifa_data_sync', 'player_registration_access']
            ]
        ];
    }

    /**
     * Filter modules based on user permissions
     */
    private function filterModulesByPermissions(User $user, array $modules): array
    {
        $userPermissions = $this->rbacService->getUserPermissions($user);
        $filteredModules = [];

        foreach ($modules as $module) {
            $requiredPermissions = $module['required_permissions'] ?? [];
            
            // If no permissions required, allow access
            if (empty($requiredPermissions)) {
                $filteredModules[] = $module;
                continue;
            }
            
            // Check if user has any of the required permissions
            if ($this->rbacService->userHasAnyPermission($user, $requiredPermissions)) {
                $filteredModules[] = $module;
            }
        }

        return $filteredModules;
    }

}
