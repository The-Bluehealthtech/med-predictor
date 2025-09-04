/**
 * Routes pour le module DTN Manager
 * Gestion des équipes nationales et des sélections internationales
 */

import { ref } from 'vue'

// Import all components directly
import DTNDashboard from '../views/DTNDashboard.vue'
import NationalTeams from '../components/NationalTeams.vue'
import InternationalSelections from '../components/InternationalSelections.vue'
import ExpatsClubSync from '../components/ExpatsClubSync.vue'
import MedicalClubInterface from '../components/MedicalClubInterface.vue'
import TechnicalPlanning from '../components/TechnicalPlanning.vue'
import TeamForm from '../components/TeamForm.vue'
import TeamDetail from '../components/TeamDetail.vue'
import SelectionForm from '../components/SelectionForm.vue'
import SelectionDetail from '../components/SelectionDetail.vue'
import SelectionPlayers from '../components/SelectionPlayers.vue'
import ExpatDetail from '../components/ExpatDetail.vue'
import PlayerMedicalData from '../components/PlayerMedicalData.vue'
import DTNReports from '../components/DTNReports.vue'
import DTNSettings from '../components/DTNSettings.vue'

// Store pour les permissions
const userPermissions = ref([])

/**
 * Vérifier les permissions utilisateur
 */
const checkPermission = (requiredPermission) => {
  return userPermissions.value.includes(requiredPermission) || 
         userPermissions.value.includes('dtn_admin')
}

/**
 * Routes du module DTN - Accessible sans authentification pour system admin
 */
const dtnRoutes = [
  {
    path: '/dtn',
    name: 'dtn',
    component: DTNDashboard,
    meta: {
      title: 'Dashboard DTN',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/dashboard',
    name: 'dtn.dashboard',
    component: DTNDashboard,
    meta: {
      title: 'Dashboard DTN',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/teams',
    name: 'dtn.teams',
    component: NationalTeams,
    meta: {
      title: 'Équipes Nationales',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/teams/create',
    name: 'dtn.teams.create',
    component: TeamForm,
    meta: {
      title: 'Créer une Équipe',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/teams/:id',
    name: 'dtn.teams.show',
    component: TeamDetail,
    meta: {
      title: 'Détails Équipe',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/teams/:id/edit',
    name: 'dtn.teams.edit',
    component: TeamForm,
    meta: {
      title: 'Modifier Équipe',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/selections',
    name: 'dtn.selections',
    component: InternationalSelections,
    meta: {
      title: 'Sélections Internationales',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/selections/create',
    name: 'dtn.selections.create',
    component: SelectionForm,
    meta: {
      title: 'Créer une Sélection',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/selections/:id',
    name: 'dtn.selections.show',
    component: SelectionDetail,
    meta: {
      title: 'Détails Sélection',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/selections/:id/edit',
    name: 'dtn.selections.edit',
    component: SelectionForm,
    meta: {
      title: 'Modifier Sélection',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/selections/:id/players',
    name: 'dtn.selections.players',
    component: SelectionPlayers,
    meta: {
      title: 'Joueurs de la Sélection',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/expats',
    name: 'dtn.expats',
    component: ExpatsClubSync,
    meta: {
      title: 'Joueurs Expatriés',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/expats/:id',
    name: 'dtn.expats.show',
    component: ExpatDetail,
    meta: {
      title: 'Détails Joueur Expatrié',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/medical',
    name: 'dtn.medical',
    component: MedicalClubInterface,
    meta: {
      title: 'Interface Médicale',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/medical/:playerId',
    name: 'dtn.medical.player',
    component: PlayerMedicalData,
    meta: {
      title: 'Données Médicales Joueur',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/planning',
    name: 'dtn.planning',
    component: TechnicalPlanning,
    meta: {
      title: 'Planification Technique',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/reports',
    name: 'dtn.reports',
    component: DTNReports,
    meta: {
      title: 'Rapports DTN',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/settings',
    name: 'dtn.settings',
    component: DTNSettings,
    meta: {
      title: 'Paramètres DTN',
      requiresAuth: false,
      module: 'dtn'
    }
  },
  {
    path: '/dtn/:pathMatch(.*)*',
    name: 'dtn.catch-all',
    redirect: '/dtn/dashboard',
    meta: {
      title: 'DTN Manager',
      requiresAuth: false,
      module: 'dtn'
    }
  }
]

/**
 * Fonction pour enregistrer les routes DTN dans le routeur principal
 */
export const registerDTNRoutes = (router) => {
  console.log('🔧 Registering DTN routes...');
  console.log('🔧 Routes to add:', dtnRoutes.map(r => r.path));
  
  dtnRoutes.forEach(route => {
    console.log('🔧 Adding route:', route.path);
    router.addRoute(route)
  })
  
  // Force router to recognize new routes
  console.log('🔧 All registered routes after DTN:', router.getRoutes().map(r => r.path));
  
  // Refresh router to ensure new routes are recognized
  if (router.refresh) {
    router.refresh()
  }
}

/**
 * Fonction pour définir les permissions utilisateur
 */
export const setUserPermissions = (permissions) => {
  userPermissions.value = permissions
}

/**
 * Fonction pour obtenir les permissions utilisateur
 */
export const getUserPermissions = () => {
  return userPermissions.value
}

/**
 * Fonction pour vérifier si l'utilisateur a une permission spécifique
 */
export const hasPermission = (permission) => {
  return checkPermission(permission)
}

/**
 * Fonction pour obtenir les routes accessibles selon les permissions
 */
export const getAccessibleRoutes = () => {
  return dtnRoutes.filter(route => {
    if (!route.meta?.permissions) return true
    return route.meta.permissions.some(perm => checkPermission(perm))
  })
}

/**
 * Navigation items pour le menu DTN
 */
export const dtnNavigationItems = [
  {
    name: 'Dashboard',
    route: 'dtn.dashboard',
    icon: 'dashboard',
    permission: 'dtn_view'
  },
  {
    name: 'Équipes Nationales',
    route: 'dtn.teams',
    icon: 'teams',
    permission: 'dtn_teams_view'
  },
  {
    name: 'Sélections Internationales',
    route: 'dtn.selections',
    icon: 'selections',
    permission: 'dtn_selections_view'
  },
  {
    name: 'Joueurs Expatriés',
    route: 'dtn.expats',
    icon: 'expats',
    permission: 'dtn_expats_view'
  },
  {
    name: 'Interface Médicale',
    route: 'dtn.medical',
    icon: 'medical',
    permission: 'dtn_medical_view'
  },
  {
    name: 'Planification Technique',
    route: 'dtn.planning',
    icon: 'planning',
    permission: 'dtn_planning_view'
  },
  {
    name: 'Rapports',
    route: 'dtn.reports',
    icon: 'reports',
    permission: 'dtn_reports_view'
  },
  {
    name: 'Paramètres',
    route: 'dtn.settings',
    icon: 'settings',
    permission: 'dtn_settings'
  }
]

/**
 * Fonction pour obtenir les items de navigation filtrés selon les permissions
 */
export const getFilteredNavigationItems = () => {
  return dtnNavigationItems.filter(item => {
    return !item.permission || checkPermission(item.permission)
  })
}

export default dtnRoutes 