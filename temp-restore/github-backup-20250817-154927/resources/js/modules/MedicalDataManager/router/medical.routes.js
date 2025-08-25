/**
 * Routes pour le module Medical Data Manager
 * Gestion des routes médicales avec permissions
 */

import MedicalDashboard from '../views/MedicalDashboard.vue'
import PlayerMedicalProfile from '../views/PlayerMedicalProfile.vue'
import MedicalReports from '../views/MedicalReports.vue'

// Permissions utilisateur pour le module médical
let userPermissions = []

/**
 * Enregistrer les routes du module médical
 */
export const registerMedicalRoutes = (router) => {
  // Routes du module médical
  const medicalRoutes = [
    {
      path: '/medical',
      name: 'medical.dashboard',
      component: MedicalDashboard,
      meta: {
        requiresAuth: true,
        permissions: ['medical_view'],
        title: 'Dashboard Médical',
        icon: '🏥'
      }
    },
    {
      path: '/medical/records',
      name: 'medical.records',
      component: () => import('../components/MedicalRecord.vue'),
      meta: {
        requiresAuth: true,
        permissions: ['medical_records_view'],
        title: 'Dossiers Médicaux',
        icon: '📋'
      }
    },
    {
      path: '/medical/injuries',
      name: 'medical.injuries',
      component: () => import('../components/InjuryTracker.vue'),
      meta: {
        requiresAuth: true,
        permissions: ['medical_injuries_view'],
        title: 'Suivi des Blessures',
        icon: '🩹'
      }
    },
    {
      path: '/medical/treatments',
      name: 'medical.treatments',
      component: () => import('../components/TreatmentPlan.vue'),
      meta: {
        requiresAuth: true,
        permissions: ['medical_treatments_view'],
        title: 'Plans de Traitement',
        icon: '💊'
      }
    },
    {
      path: '/medical/examinations',
      name: 'medical.examinations',
      component: () => import('../components/MedicalExamination.vue'),
      meta: {
        requiresAuth: true,
        permissions: ['medical_examinations_view'],
        title: 'Examens Médicaux',
        icon: '🏥'
      }
    },
    {
      path: '/medical/compliance',
      name: 'medical.compliance',
      component: () => import('../components/ComplianceMonitor.vue'),
      meta: {
        requiresAuth: true,
        permissions: ['medical_compliance_view'],
        title: 'Conformité Médicale',
        icon: '✅'
      }
    },
    {
      path: '/medical/analytics',
      name: 'medical.analytics',
      component: () => import('../components/HealthDashboard.vue'),
      meta: {
        requiresAuth: true,
        permissions: ['medical_analytics_view'],
        title: 'Analytics de Santé',
        icon: '📊'
      }
    },
    {
      path: '/medical/reports',
      name: 'medical.reports',
      component: MedicalReports,
      meta: {
        requiresAuth: true,
        permissions: ['medical_reports_view'],
        title: 'Rapports Médicaux',
        icon: '📄'
      }
    },
    {
      path: '/medical/player/:id',
      name: 'medical.player.profile',
      component: PlayerMedicalProfile,
      props: true,
      meta: {
        requiresAuth: true,
        permissions: ['medical_records_view'],
        title: 'Profil Médical Joueur',
        icon: '👤'
      }
    }
  ]

  // Ajouter les routes au router
  medicalRoutes.forEach(route => {
    router.addRoute(route)
  })

  console.log('Routes du module Medical Data Manager enregistrées')
}

/**
 * Définir les permissions utilisateur pour le module médical
 */
export const setUserPermissions = (permissions) => {
  userPermissions = permissions || []
}

/**
 * Vérifier si l'utilisateur a les permissions nécessaires
 */
export const hasMedicalPermission = (permission) => {
  return userPermissions.includes(permission) || userPermissions.includes('medical_admin')
}

/**
 * Obtenir les routes accessibles selon les permissions
 */
export const getAccessibleMedicalRoutes = () => {
  const accessibleRoutes = [
    {
      name: 'Dashboard Médical',
      route: 'medical.dashboard',
      icon: '🏥',
      permissions: ['medical_view']
    },
    {
      name: 'Dossiers Médicaux',
      route: 'medical.records',
      icon: '📋',
      permissions: ['medical_records_view']
    },
    {
      name: 'Suivi des Blessures',
      route: 'medical.injuries',
      icon: '🩹',
      permissions: ['medical_injuries_view']
    },
    {
      name: 'Plans de Traitement',
      route: 'medical.treatments',
      icon: '💊',
      permissions: ['medical_treatments_view']
    },
    {
      name: 'Examens Médicaux',
      route: 'medical.examinations',
      icon: '🏥',
      permissions: ['medical_examinations_view']
    },
    {
      name: 'Conformité Médicale',
      route: 'medical.compliance',
      icon: '✅',
      permissions: ['medical_compliance_view']
    },
    {
      name: 'Analytics de Santé',
      route: 'medical.analytics',
      icon: '📊',
      permissions: ['medical_analytics_view']
    },
    {
      name: 'Rapports Médicaux',
      route: 'medical.reports',
      icon: '📄',
      permissions: ['medical_reports_view']
    }
  ]

  return accessibleRoutes.filter(route => 
    route.permissions.some(permission => hasMedicalPermission(permission))
  )
}

/**
 * Navigation items pour le module médical
 */
export const getMedicalNavigationItems = () => {
  const accessibleRoutes = getAccessibleMedicalRoutes()
  
  return accessibleRoutes.map(route => ({
    name: route.name,
    route: route.route,
    icon: route.icon,
    permissions: route.permissions
  }))
}

/**
 * Configuration des permissions médicales
 */
export const medicalPermissions = {
  // Permissions de base
  view: 'medical_view',
  admin: 'medical_admin',
  
  // Permissions pour les dossiers médicaux
  records: {
    view: 'medical_records_view',
    create: 'medical_records_create',
    edit: 'medical_records_edit'
  },
  
  // Permissions pour les blessures
  injuries: {
    view: 'medical_injuries_view',
    create: 'medical_injuries_create',
    edit: 'medical_injuries_edit'
  },
  
  // Permissions pour les traitements
  treatments: {
    view: 'medical_treatments_view',
    create: 'medical_treatments_create',
    edit: 'medical_treatments_edit'
  },
  
  // Permissions pour les examens
  examinations: {
    view: 'medical_examinations_view',
    create: 'medical_examinations_create',
    edit: 'medical_examinations_edit'
  },
  
  // Permissions pour la conformité
  compliance: {
    view: 'medical_compliance_view'
  },
  
  // Permissions pour les analytics
  analytics: {
    view: 'medical_analytics_view'
  },
  
  // Permissions pour les rapports
  reports: {
    view: 'medical_reports_view'
  },
  
  // Permissions pour les paramètres
  settings: {
    view: 'medical_settings'
  }
}

/**
 * Vérifier les permissions pour une action spécifique
 */
export const checkMedicalPermission = (action, subAction = null) => {
  if (hasMedicalPermission('medical_admin')) {
    return true
  }

  if (subAction && medicalPermissions[action] && medicalPermissions[action][subAction]) {
    return hasMedicalPermission(medicalPermissions[action][subAction])
  }

  if (medicalPermissions[action]) {
    return hasMedicalPermission(medicalPermissions[action])
  }

  return false
}

export default {
  registerMedicalRoutes,
  setUserPermissions,
  hasMedicalPermission,
  getAccessibleMedicalRoutes,
  getMedicalNavigationItems,
  medicalPermissions,
  checkMedicalPermission
} 