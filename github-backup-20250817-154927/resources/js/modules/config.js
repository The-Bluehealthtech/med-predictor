/**
 * Configuration des modules DTN et RPM
 * Paramètres globaux et configuration d'environnement
 */

export const moduleConfig = {
  // Configuration générale
  app: {
    name: 'FIT - Football Intelligence & Tracking',
    version: '1.0.0',
    modules: ['dtn', 'rpm']
  },

  // Configuration DTN Manager
  dtn: {
    name: 'DTN Manager',
    description: 'Direction Technique Nationale - Gestion des Équipes Nationales',
    icon: '🏆',
    color: 'blue',
    version: '1.0.0',
    
    // API Configuration
    api: {
      fifa: {
        baseUrl: import.meta.env.VITE_FIFA_API_URL || 'https://api.fifa.com/v1',
        timeout: 30000,
        retries: 3
      },
      clubBridge: {
        baseUrl: import.meta.env.VITE_CLUB_BRIDGE_URL || '/api/club',
        timeout: 15000,
        retries: 2
      },
      fhir: {
        baseUrl: import.meta.env.VITE_FHIR_URL || '/api/fhir',
        timeout: 20000,
        retries: 2
      }
    },

    // Fonctionnalités
    features: {
      nationalTeams: true,
      internationalSelections: true,
      expatsTracking: true,
      medicalInterface: true,
      technicalPlanning: true,
      fifaExport: true,
      pdfReports: true
    },

    // Permissions
    permissions: [
      'dtn_view',
      'dtn_teams_view',
      'dtn_teams_create',
      'dtn_teams_edit',
      'dtn_selections_view',
      'dtn_selections_create',
      'dtn_selections_edit',
      'dtn_selections_manage',
      'dtn_expats_view',
      'dtn_medical_view',
      'dtn_planning_view',
      'dtn_reports_view',
      'dtn_settings',
      'dtn_admin'
    ],

    // Routes
    routes: {
      dashboard: '/dtn/dashboard',
      teams: '/dtn/teams',
      selections: '/dtn/selections',
      expats: '/dtn/expats',
      medical: '/dtn/medical',
      planning: '/dtn/planning',
      reports: '/dtn/reports',
      settings: '/dtn/settings'
    }
  },

  // Configuration RPM
  rpm: {
    name: 'RPM',
    description: 'Régulation & Préparation Matchs',
    icon: '⚽',
    color: 'green',
    version: '1.0.0',
    
    // API Configuration
    api: {
      rpm: {
        baseUrl: import.meta.env.VITE_RPM_API_URL || '/api/rpm',
        timeout: 15000,
        retries: 2
      },
      performance: {
        baseUrl: import.meta.env.VITE_PERFORMANCE_API_URL || '/api/performance',
        timeout: 20000,
        retries: 2
      }
    },

    // Fonctionnalités
    features: {
      trainingCalendar: true,
      sessionManagement: true,
      matchPreparation: true,
      playerLoadMonitoring: true,
      attendanceTracking: true,
      performanceSync: true,
      reports: true
    },

    // Permissions
    permissions: [
      'rpm_view',
      'rpm_calendar_view',
      'rpm_sessions_view',
      'rpm_sessions_create',
      'rpm_sessions_edit',
      'rpm_matches_view',
      'rpm_matches_create',
      'rpm_matches_edit',
      'rpm_load_view',
      'rpm_attendance_view',
      'rpm_reports_view',
      'rpm_sync',
      'rpm_settings',
      'rpm_admin'
    ],

    // Routes
    routes: {
      dashboard: '/rpm/dashboard',
      calendar: '/rpm/calendar',
      sessions: '/rpm/sessions',
      matches: '/rpm/matches',
      load: '/rpm/load',
      attendance: '/rpm/attendance',
      reports: '/rpm/reports',
      sync: '/rpm/sync',
      settings: '/rpm/settings'
    }
  },

  // Configuration de sécurité
  security: {
    // Authentification
    auth: {
      type: 'oauth2',
      tokenExpiry: 3600, // 1 heure
      refreshTokenExpiry: 86400, // 24 heures
      autoRefresh: true
    },

    // Chiffrement
    encryption: {
      algorithm: 'AES-256-GCM',
      keyRotation: 30 // jours
    },

    // Audit
    audit: {
      enabled: true,
      logLevel: 'info',
      retention: 90 // jours
    }
  },

  // Configuration des données
  data: {
    // Cache
    cache: {
      enabled: true,
      ttl: 300, // 5 minutes
      maxSize: 100 // MB
    },

    // Pagination
    pagination: {
      defaultPageSize: 20,
      maxPageSize: 100,
      pageSizeOptions: [10, 20, 50, 100]
    },

    // Export
    export: {
      formats: ['pdf', 'excel', 'csv'],
      maxRecords: 10000,
      compression: true
    }
  },

  // Configuration de l'interface
  ui: {
    // Thème
    theme: {
      primary: '#3B82F6',
      secondary: '#10B981',
      accent: '#F59E0B',
      error: '#EF4444',
      warning: '#F97316',
      success: '#22C55E'
    },

    // Responsive
    responsive: {
      breakpoints: {
        sm: 640,
        md: 768,
        lg: 1024,
        xl: 1280,
        '2xl': 1536
      }
    },

    // Localisation
    localization: {
      defaultLocale: 'fr',
      supportedLocales: ['fr', 'en', 'ar'],
      dateFormat: 'DD/MM/YYYY',
      timeFormat: 'HH:mm',
      currency: 'EUR'
    }
  },

  // Configuration des notifications
  notifications: {
    // Types
    types: {
      success: {
        icon: 'check-circle',
        color: 'green',
        duration: 5000
      },
      error: {
        icon: 'x-circle',
        color: 'red',
        duration: 10000
      },
      warning: {
        icon: 'exclamation-triangle',
        color: 'yellow',
        duration: 8000
      },
      info: {
        icon: 'information-circle',
        color: 'blue',
        duration: 6000
      }
    },

    // Position
    position: 'top-right',

    // Son
    sound: {
      enabled: true,
      volume: 0.5
    }
  },

  // Configuration des logs
  logging: {
    // Niveaux
    levels: {
      error: 0,
      warn: 1,
      info: 2,
      debug: 3
    },

    // Transport
    transport: {
      console: true,
      file: true,
      remote: false
    },

    // Format
    format: {
      timestamp: true,
      level: true,
      module: true,
      message: true
    }
  }
}

/**
 * Fonction pour obtenir la configuration d'un module
 */
export const getModuleConfig = (moduleName) => {
  return moduleConfig[moduleName] || null
}

/**
 * Fonction pour obtenir la configuration d'une section
 */
export const getConfigSection = (section) => {
  return moduleConfig[section] || null
}

/**
 * Fonction pour vérifier si une fonctionnalité est activée
 */
export const isFeatureEnabled = (moduleName, featureName) => {
  const module = getModuleConfig(moduleName)
  if (!module || !module.features) return false
  
  return module.features[featureName] === true
}

/**
 * Fonction pour obtenir les permissions d'un module
 */
export const getModulePermissions = (moduleName) => {
  const module = getModuleConfig(moduleName)
  return module?.permissions || []
}

/**
 * Fonction pour obtenir les routes d'un module
 */
export const getModuleRoutes = (moduleName) => {
  const module = getModuleConfig(moduleName)
  return module?.routes || {}
}

/**
 * Fonction pour valider la configuration
 */
export const validateConfig = () => {
  const errors = []

  // Vérifier les modules requis
  const requiredModules = ['dtn', 'rpm']
  requiredModules.forEach(module => {
    if (!moduleConfig[module]) {
      errors.push(`Module ${module} manquant dans la configuration`)
    }
  })

  // Vérifier les API endpoints
  Object.keys(moduleConfig).forEach(moduleName => {
    const module = moduleConfig[moduleName]
    if (module.api) {
      Object.keys(module.api).forEach(apiName => {
        const api = module.api[apiName]
        if (!api.baseUrl) {
          errors.push(`URL de base manquante pour ${moduleName}.${apiName}`)
        }
      })
    }
  })

  return {
    valid: errors.length === 0,
    errors
  }
}

/**
 * Fonction pour obtenir la configuration d'environnement
 */
export const getEnvironmentConfig = () => {
  return {
    development: {
      debug: true,
      apiTimeout: 30000,
      cacheEnabled: false
    },
    production: {
      debug: false,
      apiTimeout: 15000,
      cacheEnabled: true
    },
    testing: {
      debug: true,
      apiTimeout: 5000,
      cacheEnabled: false
    }
  }[import.meta.env.NODE_ENV] || {}
}

export default moduleConfig 