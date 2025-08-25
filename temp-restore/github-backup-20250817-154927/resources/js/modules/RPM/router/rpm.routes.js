/**
 * Routes pour le module RPM (Régulation & Préparation Matchs)
 * Gestion des entraînements, matchs amicaux et charge de travail
 */

import { ref } from 'vue'

// Import all components directly
import RPMPortal from '../views/RPMPortal.vue'
import TrainingCalendar from '../components/TrainingCalendar.vue'
import SessionEditor from '../components/SessionEditor.vue'
import MatchPreparation from '../components/MatchPreparation.vue'
import PlayerLoadMonitor from '../components/PlayerLoadMonitor.vue'
import AttendanceTracker from '../components/AttendanceTracker.vue'
import SessionsList from '../components/SessionsList.vue'
import SessionDetail from '../components/SessionDetail.vue'
import MatchesList from '../components/MatchesList.vue'
import MatchDetail from '../components/MatchDetail.vue'
import PlayerLoadDetail from '../components/PlayerLoadDetail.vue'
import SessionAttendance from '../components/SessionAttendance.vue'
import RPMReports from '../components/RPMReports.vue'
import RPMSettings from '../components/RPMSettings.vue'
import RPMSync from '../components/RPMSync.vue'

// Store pour les permissions
const userPermissions = ref([])

/**
 * Vérifier les permissions utilisateur
 */
const checkPermission = (requiredPermission) => {
  return userPermissions.value.includes(requiredPermission) || 
         userPermissions.value.includes('rpm_admin')
}

/**
 * Routes du module RPM - Accessible sans authentification pour system admin
 */
const rpmRoutes = [
  {
    path: '/rpm',
    name: 'rpm',
    redirect: '/rpm/dashboard',
    meta: {
      title: 'RPM - Régulation & Préparation Matchs',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/dashboard',
    name: 'rpm.dashboard',
    component: RPMPortal,
    meta: {
      title: 'Dashboard RPM',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/calendar',
    name: 'rpm.calendar',
    component: TrainingCalendar,
    meta: {
      title: 'Calendrier d\'Entraînement',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/sessions',
    name: 'rpm.sessions',
    component: SessionsList,
    meta: {
      title: 'Sessions d\'Entraînement',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/sessions/create',
    name: 'rpm.sessions.create',
    component: SessionEditor,
    meta: {
      title: 'Créer une Session',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/sessions/:id',
    name: 'rpm.sessions.show',
    component: SessionDetail,
    meta: {
      title: 'Détails Session',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/sessions/:id/edit',
    name: 'rpm.sessions.edit',
    component: SessionEditor,
    meta: {
      title: 'Modifier Session',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/matches',
    name: 'rpm.matches',
    component: MatchesList,
    meta: {
      title: 'Matchs Amicaux',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/matches/create',
    name: 'rpm.matches.create',
    component: MatchPreparation,
    meta: {
      title: 'Créer un Match',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/matches/:id',
    name: 'rpm.matches.show',
    component: MatchDetail,
    meta: {
      title: 'Détails Match',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/matches/:id/edit',
    name: 'rpm.matches.edit',
    component: MatchPreparation,
    meta: {
      title: 'Modifier Match',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/load',
    name: 'rpm.load',
    component: PlayerLoadMonitor,
    meta: {
      title: 'Moniteur de Charge',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/load/:playerId',
    name: 'rpm.load.player',
    component: PlayerLoadDetail,
    meta: {
      title: 'Charge Joueur',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/attendance',
    name: 'rpm.attendance',
    component: AttendanceTracker,
    meta: {
      title: 'Suivi de Présence',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/attendance/:sessionId',
    name: 'rpm.attendance.session',
    component: SessionAttendance,
    meta: {
      title: 'Présence Session',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/sync',
    name: 'rpm.sync',
    component: RPMSync,
    meta: {
      title: 'Synchronisation Performance',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/reports',
    name: 'rpm.reports',
    component: RPMReports,
    meta: {
      title: 'Rapports RPM',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/settings',
    name: 'rpm.settings',
    component: RPMSettings,
    meta: {
      title: 'Paramètres RPM',
      requiresAuth: false,
      module: 'rpm'
    }
  },
  {
    path: '/rpm/:pathMatch(.*)*',
    name: 'rpm.catch-all',
    redirect: '/rpm/dashboard',
    meta: {
      title: 'RPM - Régulation & Préparation Matchs',
      requiresAuth: false,
      module: 'rpm'
    }
  }
]

/**
 * Fonction pour enregistrer les routes RPM dans le routeur principal
 */
export const registerRPMRoutes = (router) => {
  console.log('🔧 Registering RPM routes...');
  console.log('🔧 Routes to add:', rpmRoutes.map(r => r.path));
  
  rpmRoutes.forEach(route => {
    console.log('🔧 Adding route:', route.path);
    router.addRoute(route)
  })
  
  // Force router to recognize new routes
  console.log('🔧 All registered routes after RPM:', router.getRoutes().map(r => r.path));
  
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
  return rpmRoutes.filter(route => {
    if (!route.meta?.permissions) return true
    return route.meta.permissions.some(perm => checkPermission(perm))
  })
}

/**
 * Navigation items pour le menu RPM
 */
export const rpmNavigationItems = [
  {
    name: 'Dashboard',
    route: 'rpm.dashboard',
    icon: 'dashboard',
    permission: 'rpm_view'
  },
  {
    name: 'Calendrier',
    route: 'rpm.calendar',
    icon: 'calendar',
    permission: 'rpm_calendar_view'
  },
  {
    name: 'Sessions',
    route: 'rpm.sessions',
    icon: 'sessions',
    permission: 'rpm_sessions_view'
  },
  {
    name: 'Matchs',
    route: 'rpm.matches',
    icon: 'matches',
    permission: 'rpm_matches_view'
  },
  {
    name: 'Charge Joueurs',
    route: 'rpm.load',
    icon: 'load',
    permission: 'rpm_load_view'
  },
  {
    name: 'Présences',
    route: 'rpm.attendance',
    icon: 'attendance',
    permission: 'rpm_attendance_view'
  },
  {
    name: 'Rapports',
    route: 'rpm.reports',
    icon: 'reports',
    permission: 'rpm_reports_view'
  },
  {
    name: 'Synchronisation',
    route: 'rpm.sync',
    icon: 'sync',
    permission: 'rpm_sync'
  },
  {
    name: 'Paramètres',
    route: 'rpm.settings',
    icon: 'settings',
    permission: 'rpm_settings'
  }
]

/**
 * Fonction pour obtenir les items de navigation filtrés selon les permissions
 */
export const getFilteredNavigationItems = () => {
  return rpmNavigationItems.filter(item => {
    return !item.permission || checkPermission(item.permission)
  })
}

export default rpmRoutes 