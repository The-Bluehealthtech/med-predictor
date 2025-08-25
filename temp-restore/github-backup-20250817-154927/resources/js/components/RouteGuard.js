/**
 * Composant de protection des routes
 */

import SecurityService from '../services/SecurityService.js';

class RouteGuard {
    constructor() {
        this.protectedRoutes = new Map();
        this.currentUser = null;
        this.init();
    }

    /**
     * Initialiser le guard
     */
    init() {
        this.loadCurrentUser();
        this.setupRouteProtection();
        this.setupNavigationGuards();
    }

    /**
     * Charger les informations de l'utilisateur actuel
     */
    loadCurrentUser() {
        const userMeta = document.querySelector('meta[name="user-role"]');
        if (userMeta) {
            this.currentUser = {
                role: userMeta.getAttribute('content'),
                id: document.querySelector('meta[name="user-id"]')?.getAttribute('content'),
                clubId: document.querySelector('meta[name="user-club-id"]')?.getAttribute('content')
            };
        }
    }

    /**
     * Configurer la protection des routes
     */
    setupRouteProtection() {
        // Routes protégées avec leurs rôles requis
        this.protectedRoutes.set('/licenses/request', ['admin', 'captain']);
        this.protectedRoutes.set('/licenses/queue', ['admin', 'license_agent']);
        this.protectedRoutes.set('/licenses/approve', ['admin', 'license_agent']);
        this.protectedRoutes.set('/licenses/reject', ['admin', 'license_agent']);
        this.protectedRoutes.set('/licenses/review', ['admin', 'license_agent']);
        this.protectedRoutes.set('/admin', ['admin']);
        this.protectedRoutes.set('/club-management', ['admin', 'captain']);
    }

    /**
     * Configurer les guards de navigation
     */
    setupNavigationGuards() {
        // Intercepter les clics sur les liens
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && link.href) {
                const url = new URL(link.href);
                if (!this.canAccessRoute(url.pathname)) {
                    e.preventDefault();
                    this.handleUnauthorizedAccess(url.pathname);
                }
            }
        });

        // Intercepter les soumissions de formulaire
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.action) {
                const url = new URL(form.action);
                if (!this.canAccessRoute(url.pathname)) {
                    e.preventDefault();
                    this.handleUnauthorizedAccess(url.pathname);
                }
            }
        });

        // Vérifier la route actuelle au chargement
        this.checkCurrentRoute();
    }

    /**
     * Vérifier si l'utilisateur peut accéder à une route
     */
    canAccessRoute(pathname) {
        // Routes publiques
        const publicRoutes = ['/', '/login', '/register', '/password/reset'];
        if (publicRoutes.includes(pathname)) {
            return true;
        }

        // Vérifier si l'utilisateur est connecté
        if (!this.currentUser || !this.currentUser.role) {
            return false;
        }

        // Vérifier les permissions pour les routes protégées
        const requiredRoles = this.protectedRoutes.get(pathname);
        if (requiredRoles) {
            return requiredRoles.includes(this.currentUser.role);
        }

        // Par défaut, autoriser l'accès
        return true;
    }

    /**
     * Vérifier la route actuelle
     */
    checkCurrentRoute() {
        const currentPath = window.location.pathname;
        if (!this.canAccessRoute(currentPath)) {
            this.handleUnauthorizedAccess(currentPath);
        }
    }

    /**
     * Gérer l'accès non autorisé
     */
    handleUnauthorizedAccess(pathname) {
        console.warn(`Accès non autorisé à: ${pathname}`);
        
        // Afficher une notification
        window.showNotification(
            'Accès refusé',
            'Vous n\'avez pas les permissions nécessaires pour accéder à cette page',
            'error'
        );

        // Rediriger vers la page appropriée
        if (!this.currentUser) {
            // Utilisateur non connecté
            window.location.href = '/login';
        } else {
            // Utilisateur connecté mais sans permissions
            // window.location.href = '/dashboard'; // Disabled for debugging FIT BI card
        }
    }

    /**
     * Masquer les éléments selon les permissions
     */
    hideUnauthorizedElements() {
        const elements = document.querySelectorAll('[data-require-role]');
        
        elements.forEach(element => {
            const requiredRoles = element.getAttribute('data-require-role').split(',');
            const hasPermission = requiredRoles.some(role => 
                this.currentUser && this.currentUser.role === role.trim()
            );

            if (!hasPermission) {
                element.style.display = 'none';
            }
        });
    }

    /**
     * Désactiver les éléments selon les permissions
     */
    disableUnauthorizedElements() {
        const elements = document.querySelectorAll('[data-require-role]');
        
        elements.forEach(element => {
            const requiredRoles = element.getAttribute('data-require-role').split(',');
            const hasPermission = requiredRoles.some(role => 
                this.currentUser && this.currentUser.role === role.trim()
            );

            if (!hasPermission) {
                if (element.tagName === 'BUTTON' || element.tagName === 'INPUT') {
                    element.disabled = true;
                    element.title = 'Permission requise';
                }
            }
        });
    }

    /**
     * Vérifier les permissions pour une action spécifique
     */
    canPerformAction(action) {
        const actionPermissions = {
            'create_license': ['admin', 'captain'],
            'approve_license': ['admin', 'license_agent'],
            'reject_license': ['admin', 'license_agent'],
            'view_all_licenses': ['admin'],
            'manage_users': ['admin'],
            'manage_clubs': ['admin'],
            'view_reports': ['admin', 'license_agent']
        };

        const requiredRoles = actionPermissions[action];
        if (!requiredRoles) {
            return true; // Action non définie, autoriser par défaut
        }

        return requiredRoles.includes(this.currentUser?.role);
    }

    /**
     * Obtenir les informations de l'utilisateur actuel
     */
    getCurrentUser() {
        return this.currentUser;
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    hasRole(role) {
        return this.currentUser && this.currentUser.role === role;
    }

    /**
     * Vérifier si l'utilisateur a un des rôles spécifiés
     */
    hasAnyRole(roles) {
        return this.currentUser && roles.includes(this.currentUser.role);
    }

    /**
     * Vérifier si l'utilisateur appartient à un club spécifique
     */
    belongsToClub(clubId) {
        return this.currentUser && this.currentUser.clubId === clubId.toString();
    }

    /**
     * Mettre à jour les informations utilisateur
     */
    updateUserInfo(userInfo) {
        this.currentUser = { ...this.currentUser, ...userInfo };
    }

    /**
     * Déconnecter l'utilisateur
     */
    logout() {
        this.currentUser = null;
        window.location.href = '/login';
    }

    /**
     * Appliquer les protections à la page actuelle
     */
    applyProtections() {
        this.hideUnauthorizedElements();
        this.disableUnauthorizedElements();
        this.checkCurrentRoute();
    }
}

// Créer l'instance globale
const routeGuard = new RouteGuard();

// Appliquer les protections au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    routeGuard.applyProtections();
});

// Exporter pour utilisation dans d'autres modules
export default routeGuard; 