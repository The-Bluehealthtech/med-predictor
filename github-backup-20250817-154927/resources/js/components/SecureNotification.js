/**
 * Composant de notification sécurisé
 */

class SecureNotification {
    constructor() {
        this.notifications = [];
        this.container = null;
        this.init();
    }

    /**
     * Initialiser le composant
     */
    init() {
        this.createContainer();
        this.setupGlobalHandler();
    }

    /**
     * Créer le conteneur de notifications
     */
    createContainer() {
        this.container = document.createElement('div');
        this.container.id = 'secure-notifications';
        this.container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(this.container);
    }

    /**
     * Configurer le gestionnaire global
     */
    setupGlobalHandler() {
        window.showNotification = (title, message, type = 'info', duration = 5000) => {
            this.show(title, message, type, duration);
        };
    }

    /**
     * Afficher une notification
     */
    show(title, message, type = 'info', duration = 5000) {
        const notification = this.createNotificationElement(title, message, type);
        this.container.appendChild(notification);

        // Animation d'entrée
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Auto-suppression
        if (duration > 0) {
            setTimeout(() => {
                this.hide(notification);
            }, duration);
        }

        // Stocker la notification
        this.notifications.push(notification);

        return notification;
    }

    /**
     * Créer l'élément de notification
     */
    createNotificationElement(title, message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type} transform translate-x-full transition-all duration-300 ease-in-out`;
        
        const icon = this.getIconForType(type);
        const bgColor = this.getBackgroundColor(type);
        const borderColor = this.getBorderColor(type);

        notification.innerHTML = `
            <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden ${bgColor} ${borderColor}">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${icon}
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900">${this.escapeHtml(title)}</p>
                            <p class="mt-1 text-sm text-gray-500">${this.escapeHtml(message)}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                                <span class="sr-only">Fermer</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        return notification;
    }

    /**
     * Obtenir l'icône selon le type
     */
    getIconForType(type) {
        const icons = {
            success: `<svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
            error: `<svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
            warning: `<svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>`,
            info: `<svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`
        };

        return icons[type] || icons.info;
    }

    /**
     * Obtenir la couleur de fond selon le type
     */
    getBackgroundColor(type) {
        const colors = {
            success: 'bg-green-50',
            error: 'bg-red-50',
            warning: 'bg-yellow-50',
            info: 'bg-blue-50'
        };

        return colors[type] || colors.info;
    }

    /**
     * Obtenir la couleur de bordure selon le type
     */
    getBorderColor(type) {
        const colors = {
            success: 'border-l-4 border-green-400',
            error: 'border-l-4 border-red-400',
            warning: 'border-l-4 border-yellow-400',
            info: 'border-l-4 border-blue-400'
        };

        return colors[type] || colors.info;
    }

    /**
     * Masquer une notification
     */
    hide(notification) {
        notification.classList.remove('show');
        notification.classList.add('translate-x-full');
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            
            // Retirer de la liste
            const index = this.notifications.indexOf(notification);
            if (index > -1) {
                this.notifications.splice(index, 1);
            }
        }, 300);
    }

    /**
     * Masquer toutes les notifications
     */
    hideAll() {
        this.notifications.forEach(notification => {
            this.hide(notification);
        });
    }

    /**
     * Échapper le HTML pour éviter les XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Afficher une notification de succès
     */
    success(title, message, duration = 5000) {
        return this.show(title, message, 'success', duration);
    }

    /**
     * Afficher une notification d'erreur
     */
    error(title, message, duration = 8000) {
        return this.show(title, message, 'error', duration);
    }

    /**
     * Afficher une notification d'avertissement
     */
    warning(title, message, duration = 6000) {
        return this.show(title, message, 'warning', duration);
    }

    /**
     * Afficher une notification d'information
     */
    info(title, message, duration = 5000) {
        return this.show(title, message, 'info', duration);
    }
}

// Créer l'instance globale
const secureNotification = new SecureNotification();

// Exporter pour utilisation dans d'autres modules
export default secureNotification; 