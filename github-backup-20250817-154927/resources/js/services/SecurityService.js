/**
 * Service de sécurité frontend pour le module de gestion des licences
 */

class SecurityService {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.baseURL = '/api';
        this.maxFileSize = 2 * 1024 * 1024; // 2MB
        this.allowedFileTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    }

    /**
     * Valider un fichier uploadé
     */
    validateFile(file) {
        const errors = [];

        // Vérifier la taille
        if (file.size > this.maxFileSize) {
            errors.push(`Le fichier ne doit pas dépasser ${this.maxFileSize / 1024 / 1024} MB`);
        }

        // Vérifier le type
        if (!this.allowedFileTypes.includes(file.type)) {
            errors.push('Seuls les fichiers PDF, JPG et PNG sont acceptés');
        }

        // Vérifier le nom du fichier
        const fileName = file.name.toLowerCase();
        const allowedExtensions = ['.pdf', '.jpg', '.jpeg', '.png'];
        const hasValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));
        
        if (!hasValidExtension) {
            errors.push('Extension de fichier non autorisée');
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Valider les données d'un formulaire de licence
     */
    validateLicenseForm(data) {
        const errors = {};

        // Validation du joueur
        if (!data.player_id) {
            errors.player_id = 'Le joueur est requis';
        }

        // Validation du type de licence
        const validTypes = ['amateur', 'professional', 'international'];
        if (!data.license_type || !validTypes.includes(data.license_type)) {
            errors.license_type = 'Le type de licence doit être amateur, professionnel ou international';
        }

        // Validation des notes
        if (data.notes && data.notes.length > 1000) {
            errors.notes = 'Les notes ne doivent pas dépasser 1000 caractères';
        }

        // Validation du document
        if (data.document) {
            const fileValidation = this.validateFile(data.document);
            if (!fileValidation.isValid) {
                errors.document = fileValidation.errors;
            }
        }

        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    }

    /**
     * Valider les données d'approbation/rejet
     */
    validateApprovalForm(data) {
        const errors = {};

        if (!data.action || !['approve', 'reject'].includes(data.action)) {
            errors.action = 'L\'action doit être "approve" ou "reject"';
        }

        if (data.action === 'reject' && (!data.reason || data.reason.trim().length === 0)) {
            errors.reason = 'Une raison est requise lors du rejet';
        }

        if (data.reason && data.reason.length > 500) {
            errors.reason = 'La raison ne doit pas dépasser 500 caractères';
        }

        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    }

    /**
     * Faire une requête API sécurisée
     */
    async makeSecureRequest(endpoint, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        };

        const finalOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers
            }
        };

        try {
            const response = await fetch(`${this.baseURL}${endpoint}`, finalOptions);
            
            // Gérer les erreurs HTTP
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                
                switch (response.status) {
                    case 401:
                        this.handleUnauthorized();
                        break;
                    case 403:
                        this.handleForbidden();
                        break;
                    case 422:
                        return {
                            success: false,
                            errors: errorData.errors || {},
                            message: 'Erreurs de validation'
                        };
                    case 429:
                        this.handleRateLimit();
                        break;
                    case 500:
                        this.handleServerError();
                        break;
                    default:
                        this.handleGenericError(response.status);
                }

                return {
                    success: false,
                    message: errorData.message || 'Une erreur est survenue',
                    status: response.status
                };
            }

            const data = await response.json();
            return {
                success: true,
                data
            };

        } catch (error) {
            console.error('Erreur de requête:', error);
            this.handleNetworkError();
            return {
                success: false,
                message: 'Erreur de connexion au serveur'
            };
        }
    }

    /**
     * Gérer les erreurs d'authentification
     */
    handleUnauthorized() {
        // Rediriger vers la page de login
        window.location.href = '/login';
    }

    /**
     * Gérer les erreurs d'autorisation
     */
    handleForbidden() {
        this.showNotification('Accès refusé', 'Vous n\'avez pas les permissions nécessaires', 'error');
    }

    /**
     * Gérer les erreurs de rate limiting
     */
    handleRateLimit() {
        this.showNotification('Limite dépassée', 'Trop de requêtes. Veuillez patienter.', 'warning');
    }

    /**
     * Gérer les erreurs serveur
     */
    handleServerError() {
        this.showNotification('Erreur serveur', 'Une erreur interne est survenue', 'error');
    }

    /**
     * Gérer les erreurs génériques
     */
    handleGenericError(status) {
        this.showNotification('Erreur', `Erreur ${status}`, 'error');
    }

    /**
     * Gérer les erreurs réseau
     */
    handleNetworkError() {
        this.showNotification('Erreur réseau', 'Impossible de se connecter au serveur', 'error');
    }

    /**
     * Afficher une notification
     */
    showNotification(title, message, type = 'info') {
        // Utiliser le système de notification de l'application
        if (window.showNotification) {
            window.showNotification(title, message, type);
        } else {
            // Fallback simple
            alert(`${title}: ${message}`);
        }
    }

    /**
     * Nettoyer les données sensibles
     */
    sanitizeData(data) {
        if (typeof data !== 'object' || data === null) {
            return data;
        }

        const sanitized = {};
        for (const [key, value] of Object.entries(data)) {
            if (typeof value === 'string') {
                // Échapper les caractères spéciaux
                sanitized[key] = this.escapeHtml(value);
            } else if (typeof value === 'object') {
                sanitized[key] = this.sanitizeData(value);
            } else {
                sanitized[key] = value;
            }
        }

        return sanitized;
    }

    /**
     * Échapper les caractères HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    hasRole(role) {
        const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');
        return userRole === role;
    }

    /**
     * Vérifier si l'utilisateur a un des rôles spécifiés
     */
    hasAnyRole(roles) {
        const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');
        return roles.includes(userRole);
    }

    /**
     * Masquer les données sensibles dans les logs
     */
    maskSensitiveData(data) {
        const masked = { ...data };
        
        // Masquer les mots de passe
        if (masked.password) {
            masked.password = '***';
        }
        
        // Masquer les tokens
        if (masked.token) {
            masked.token = '***';
        }
        
        // Masquer les données personnelles
        if (masked.email) {
            masked.email = masked.email.replace(/(.{2}).*@/, '$1***@');
        }

        return masked;
    }
}

// Exporter le service
export default new SecurityService(); 