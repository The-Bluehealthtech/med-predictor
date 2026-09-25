/**
 * Initialisation de la sécurité frontend
 * Ce fichier charge et configure tous les composants de sécurité
 */

import SecurityService from './services/SecurityService.js';
import SecureNotification from './components/SecureNotification.js';
import SecureForm from './components/SecureForm.js';
import RouteGuard from './components/RouteGuard.js';

class SecurityInitializer {
    constructor() {
        this.initialized = false;
        this.components = {};
    }

    /**
     * Initialiser la sécurité frontend
     */
    async init() {
        if (this.initialized) {
            return;
        }

        try {
            console.log('🔒 Initialisation de la sécurité frontend...');

            // Initialiser les composants de sécurité
            await this.initializeComponents();
            
            // Configurer les protections
            this.setupProtections();
            
            // Configurer les listeners globaux
            this.setupGlobalListeners();
            
            // Appliquer les protections immédiatement
            this.applyProtections();

            this.initialized = true;
            console.log('✅ Sécurité frontend initialisée avec succès');

        } catch (error) {
            console.error('❌ Erreur lors de l\'initialisation de la sécurité:', error);
            this.handleInitializationError(error);
        }
    }

    /**
     * Initialiser les composants de sécurité
     */
    async initializeComponents() {
        // Service de sécurité
        this.components.securityService = SecurityService;

        // Système de notifications
        this.components.notification = SecureNotification;

        // Protection des routes
        this.components.routeGuard = RouteGuard;

        // Initialiser les formulaires sécurisés
        this.initializeSecureForms();

        console.log('📦 Composants de sécurité chargés');
    }

    /**
     * Initialiser les formulaires sécurisés
     */
    initializeSecureForms() {
        const forms = document.querySelectorAll('form[data-secure="true"]');
        
        forms.forEach(form => {
            const options = this.parseFormOptions(form);
            this.components[`form_${form.id || Math.random()}`] = new SecureForm(form, options);
        });

        console.log(`📝 ${forms.length} formulaires sécurisés initialisés`);
    }

    /**
     * Parser les options d'un formulaire
     */
    parseFormOptions(form) {
        const options = {};

        // Options de validation
        if (form.hasAttribute('data-validate-on-input')) {
            options.validateOnInput = form.getAttribute('data-validate-on-input') === 'true';
        }

        if (form.hasAttribute('data-validate-on-blur')) {
            options.validateOnBlur = form.getAttribute('data-validate-on-blur') === 'true';
        }

        // Callbacks
        if (form.hasAttribute('data-on-success')) {
            options.onSuccess = window[form.getAttribute('data-on-success')];
        }

        if (form.hasAttribute('data-on-error')) {
            options.onError = window[form.getAttribute('data-on-error')];
        }

        // Redirection
        if (form.hasAttribute('data-redirect-url')) {
            options.redirectUrl = form.getAttribute('data-redirect-url');
        }

        return options;
    }

    /**
     * Configurer les protections
     */
    setupProtections() {
        // Protection contre les attaques XSS
        this.setupXSSProtection();

        // Protection contre les attaques CSRF
        this.setupCSRFProtection();

        // Protection contre les injections
        this.setupInjectionProtection();

        // Protection des données sensibles
        this.setupDataProtection();

        console.log('🛡️ Protections configurées');
    }

    /**
     * Protection contre les attaques XSS
     */
    setupXSSProtection() {
        // Échapper le contenu dynamique
        document.addEventListener('DOMContentLoaded', () => {
            const dynamicElements = document.querySelectorAll('[data-dynamic-content]');
            dynamicElements.forEach(element => {
                const content = element.getAttribute('data-dynamic-content');
                if (content) {
                    element.textContent = content; // Utiliser textContent au lieu de innerHTML
                }
            });
        });

        // Intercepter les modifications innerHTML
        const originalInnerHTML = Element.prototype.innerHTML;
        Element.prototype.innerHTML = function(value) {
            if (value && typeof value === 'string') {
                // Échapper le HTML
                const div = document.createElement('div');
                div.textContent = value;
                return originalInnerHTML.call(this, div.innerHTML);
            }
            return originalInnerHTML.call(this, value);
        };
    }

    /**
     * Protection contre les attaques CSRF
     */
    setupCSRFProtection() {
        // Vérifier la présence du token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.warn('⚠️ Token CSRF manquant');
        }

        // Intercepter les requêtes AJAX pour ajouter le token
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            if (options.method && options.method.toUpperCase() !== 'GET') {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (token) {
                    if (!options.headers) {
                        options.headers = {};
                    }
                    options.headers['X-CSRF-TOKEN'] = token;
                }
            }
            return originalFetch.call(this, url, options);
        };
    }

    /**
     * Protection contre les injections
     */
    setupInjectionProtection() {
        // Intercepter les clics sur les liens suspects
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && link.href) {
                const url = new URL(link.href);
                if (url.protocol === 'javascript:' || url.protocol === 'data:') {
                    e.preventDefault();
                    console.warn('🚫 Lien suspect bloqué:', link.href);
                }
            }
        });

        // Bloquer les scripts inline suspects
        const scripts = document.querySelectorAll('script');
        scripts.forEach(script => {
            if (script.src && !script.src.startsWith(window.location.origin)) {
                console.warn('⚠️ Script externe détecté:', script.src);
            }
        });
    }

    /**
     * Protection des données sensibles
     */
    setupDataProtection() {
        // Masquer les données sensibles dans les logs
        const originalConsoleLog = console.log;
        console.log = function(...args) {
            const maskedArgs = args.map(arg => {
                if (typeof arg === 'object' && arg !== null) {
                    return SecurityService.maskSensitiveData(arg);
                }
                return arg;
            });
            return originalConsoleLog.apply(console, maskedArgs);
        };

        // Nettoyer les données sensibles lors de la navigation
        window.addEventListener('beforeunload', () => {
            // Nettoyer les données sensibles en mémoire
            this.clearSensitiveData();
        });
    }

    /**
     * Nettoyer les données sensibles
     */
    clearSensitiveData() {
        // Nettoyer les formulaires
        document.querySelectorAll('input[type="password"]').forEach(input => {
            input.value = '';
        });

        // Nettoyer les tokens en sessionStorage
        sessionStorage.removeItem('auth_token');
        sessionStorage.removeItem('user_data');
    }

    /**
     * Configurer les listeners globaux
     */
    setupGlobalListeners() {
        // Gestion des erreurs globales
        window.addEventListener('error', (e) => {
            console.error('Erreur JavaScript:', e.error);
            SecurityService.showNotification('Erreur', 'Une erreur JavaScript est survenue', 'error');
        });

        // Gestion des erreurs de promesses
        window.addEventListener('unhandledrejection', (e) => {
            console.error('Promesse rejetée:', e.reason);
            SecurityService.showNotification('Erreur', 'Une erreur réseau est survenue', 'error');
        });

        // Détection de déconnexion
        window.addEventListener('storage', (e) => {
            if (e.key === 'logout' && e.newValue === 'true') {
                this.handleLogout();
            }
        });

        // Protection contre les clics multiples
        let lastClickTime = 0;
        document.addEventListener('click', (e) => {
            const now = Date.now();
            if (now - lastClickTime < 100) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            lastClickTime = now;
        });
    }

    /**
     * Appliquer les protections
     */
    applyProtections() {
        // Appliquer les protections de route
        if (this.components.routeGuard) {
            this.components.routeGuard.applyProtections();
        }

        // Masquer les éléments non autorisés
        this.hideUnauthorizedElements();

        // Désactiver les éléments non autorisés
        this.disableUnauthorizedElements();

        // Sécuriser les formulaires
        this.secureForms();
    }

    /**
     * Masquer les éléments non autorisés
     */
    hideUnauthorizedElements() {
        const elements = document.querySelectorAll('[data-require-role]');
        elements.forEach(element => {
            const requiredRoles = element.getAttribute('data-require-role').split(',');
            const currentUser = this.components.routeGuard?.getCurrentUser();
            
            if (currentUser && !requiredRoles.includes(currentUser.role)) {
                element.style.display = 'none';
            }
        });
    }

    /**
     * Désactiver les éléments non autorisés
     */
    disableUnauthorizedElements() {
        const elements = document.querySelectorAll('[data-require-role]');
        elements.forEach(element => {
            const requiredRoles = element.getAttribute('data-require-role').split(',');
            const currentUser = this.components.routeGuard?.getCurrentUser();
            
            if (currentUser && !requiredRoles.includes(currentUser.role)) {
                if (element.tagName === 'BUTTON' || element.tagName === 'INPUT') {
                    element.disabled = true;
                    element.title = 'Permission requise';
                }
            }
        });
    }

    /**
     * Sécuriser les formulaires
     */
    secureForms() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            // Ajouter la validation côté client
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    }

    /**
     * Valider un formulaire
     */
    validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value || field.value.trim() === '') {
                this.showFieldError(field, 'Ce champ est requis');
                isValid = false;
            } else {
                this.hideFieldError(field);
            }
        });

        return isValid;
    }

    /**
     * Afficher une erreur de champ
     */
    showFieldError(field, message) {
        this.hideFieldError(field);
        
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error text-red-600 text-sm mt-1';
        errorElement.textContent = message;
        
        field.parentNode.appendChild(errorElement);
        field.classList.add('border-red-500');
    }

    /**
     * Masquer une erreur de champ
     */
    hideFieldError(field) {
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
        field.classList.remove('border-red-500');
    }

    /**
     * Gérer les erreurs d'initialisation
     */
    handleInitializationError(error) {
        console.error('Erreur d\'initialisation de la sécurité:', error);
        
        // Afficher une notification d'erreur
        if (window.showNotification) {
            window.showNotification(
                'Erreur de sécurité',
                'Impossible d\'initialiser la sécurité. Veuillez recharger la page.',
                'error'
            );
        }
    }

    /**
     * Gérer la déconnexion
     */
    handleLogout() {
        // Nettoyer les données sensibles
        this.clearSensitiveData();
        
        // Rediriger vers la page de login
        window.location.href = '/login';
    }

    /**
     * Obtenir un composant
     */
    getComponent(name) {
        return this.components[name];
    }

    /**
     * Vérifier si la sécurité est initialisée
     */
    isInitialized() {
        return this.initialized;
    }
}

// Créer l'instance globale
const securityInitializer = new SecurityInitializer();

// Initialiser automatiquement au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    securityInitializer.init();
});

// Exporter pour utilisation dans d'autres modules
export default securityInitializer; 