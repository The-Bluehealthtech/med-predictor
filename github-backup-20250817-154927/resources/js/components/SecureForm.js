/**
 * Composant de formulaire sécurisé avec validation
 */

import SecurityService from '../services/SecurityService.js';

class SecureForm {
    constructor(formElement, options = {}) {
        this.form = formElement;
        this.options = {
            validateOnInput: true,
            validateOnBlur: true,
            showErrors: true,
            autoSubmit: false,
            ...options
        };
        
        this.errors = {};
        this.isSubmitting = false;
        this.validators = {};
        
        this.init();
    }

    /**
     * Initialiser le formulaire
     */
    init() {
        this.setupEventListeners();
        this.setupValidation();
        this.setupSecurity();
    }

    /**
     * Configurer les écouteurs d'événements
     */
    setupEventListeners() {
        // Validation en temps réel
        if (this.options.validateOnInput) {
            this.form.addEventListener('input', (e) => {
                this.validateField(e.target);
            });
        }

        // Validation lors de la perte de focus
        if (this.options.validateOnBlur) {
            this.form.addEventListener('blur', (e) => {
                this.validateField(e.target);
            }, true);
        }

        // Soumission sécurisée
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });

        // Protection contre les soumissions multiples
        this.form.addEventListener('submit', () => {
            if (this.isSubmitting) {
                return false;
            }
        });
    }

    /**
     * Configurer la validation
     */
    setupValidation() {
        // Validation des champs requis
        this.form.querySelectorAll('[required]').forEach(field => {
            this.addValidator(field.name, (value) => {
                if (!value || value.trim() === '') {
                    return 'Ce champ est requis';
                }
                return null;
            });
        });

        // Validation des emails
        this.form.querySelectorAll('[type="email"]').forEach(field => {
            this.addValidator(field.name, (value) => {
                if (value && !this.isValidEmail(value)) {
                    return 'Format d\'email invalide';
                }
                return null;
            });
        });

        // Validation des fichiers
        this.form.querySelectorAll('[type="file"]').forEach(field => {
            this.addValidator(field.name, (value) => {
                if (value && value.files && value.files[0]) {
                    const file = value.files[0];
                    const validation = SecurityService.validateFile(file);
                    if (!validation.isValid) {
                        return validation.errors[0];
                    }
                }
                return null;
            });
        });
    }

    /**
     * Configurer la sécurité
     */
    setupSecurity() {
        // Ajouter le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            this.form.appendChild(csrfInput);
        }

        // Protection contre les attaques XSS
        this.form.querySelectorAll('input[type="text"], input[type="email"], textarea').forEach(field => {
            field.addEventListener('input', (e) => {
                e.target.value = SecurityService.escapeHtml(e.target.value);
            });
        });
    }

    /**
     * Ajouter un validateur personnalisé
     */
    addValidator(fieldName, validator) {
        if (!this.validators[fieldName]) {
            this.validators[fieldName] = [];
        }
        this.validators[fieldName].push(validator);
    }

    /**
     * Valider un champ spécifique
     */
    validateField(field) {
        const fieldName = field.name;
        const value = field.value;
        let fieldErrors = [];

        // Exécuter tous les validateurs pour ce champ
        if (this.validators[fieldName]) {
            this.validators[fieldName].forEach(validator => {
                const error = validator(value, field);
                if (error) {
                    fieldErrors.push(error);
                }
            });
        }

        // Mettre à jour les erreurs
        if (fieldErrors.length > 0) {
            this.errors[fieldName] = fieldErrors;
            this.showFieldError(field, fieldErrors[0]);
        } else {
            delete this.errors[fieldName];
            this.hideFieldError(field);
        }

        return fieldErrors.length === 0;
    }

    /**
     * Valider tout le formulaire
     */
    validateForm() {
        let isValid = true;
        const fields = this.form.querySelectorAll('input, select, textarea');

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Afficher l'erreur d'un champ
     */
    showFieldError(field, message) {
        if (!this.options.showErrors) return;

        // Supprimer l'ancienne erreur
        this.hideFieldError(field);

        // Créer l'élément d'erreur
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error text-red-600 text-sm mt-1';
        errorElement.textContent = message;
        errorElement.setAttribute('data-field', field.name);

        // Ajouter l'erreur après le champ
        field.parentNode.appendChild(errorElement);

        // Ajouter la classe d'erreur au champ
        field.classList.add('border-red-500');
        field.classList.remove('border-gray-300');
    }

    /**
     * Masquer l'erreur d'un champ
     */
    hideFieldError(field) {
        const errorElement = field.parentNode.querySelector(`[data-field="${field.name}"]`);
        if (errorElement) {
            errorElement.remove();
        }

        // Retirer la classe d'erreur du champ
        field.classList.remove('border-red-500');
        field.classList.add('border-gray-300');
    }

    /**
     * Gérer la soumission du formulaire
     */
    async handleSubmit() {
        if (this.isSubmitting) {
            return;
        }

        // Valider le formulaire
        if (!this.validateForm()) {
            this.showFormError('Veuillez corriger les erreurs dans le formulaire');
            return;
        }

        this.isSubmitting = true;
        this.showLoading();

        try {
            const formData = new FormData(this.form);
            const data = this.serializeFormData(formData);

            // Nettoyer les données
            const sanitizedData = SecurityService.sanitizeData(data);

            // Faire la requête sécurisée
            const response = await SecurityService.makeSecureRequest(this.form.action, {
                method: this.form.method || 'POST',
                body: formData
            });

            if (response.success) {
                this.handleSuccess(response.data);
            } else {
                this.handleError(response);
            }

        } catch (error) {
            console.error('Erreur de soumission:', error);
            this.handleError({
                message: 'Erreur lors de la soumission du formulaire'
            });
        } finally {
            this.isSubmitting = false;
            this.hideLoading();
        }
    }

    /**
     * Sérialiser les données du formulaire
     */
    serializeFormData(formData) {
        const data = {};
        for (const [key, value] of formData.entries()) {
            if (data[key]) {
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }
        return data;
    }

    /**
     * Gérer le succès
     */
    handleSuccess(data) {
        // Appeler le callback de succès si défini
        if (this.options.onSuccess) {
            this.options.onSuccess(data);
        } else {
            // Comportement par défaut
            window.showNotification('Succès', 'Formulaire soumis avec succès', 'success');
            
            // Rediriger si une URL est spécifiée
            if (this.options.redirectUrl) {
                setTimeout(() => {
                    window.location.href = this.options.redirectUrl;
                }, 1000);
            }
        }
    }

    /**
     * Gérer l'erreur
     */
    handleError(response) {
        // Afficher les erreurs de validation
        if (response.errors) {
            Object.keys(response.errors).forEach(fieldName => {
                const field = this.form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    this.showFieldError(field, response.errors[fieldName][0]);
                }
            });
        }

        // Afficher le message d'erreur général
        this.showFormError(response.message || 'Une erreur est survenue');

        // Appeler le callback d'erreur si défini
        if (this.options.onError) {
            this.options.onError(response);
        }
    }

    /**
     * Afficher une erreur de formulaire
     */
    showFormError(message) {
        window.showNotification('Erreur', message, 'error');
    }

    /**
     * Afficher l'état de chargement
     */
    showLoading() {
        const submitButton = this.form.querySelector('[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Envoi en cours...';
        }
    }

    /**
     * Masquer l'état de chargement
     */
    hideLoading() {
        const submitButton = this.form.querySelector('[type="submit"]');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = submitButton.getAttribute('data-original-text') || 'Envoyer';
        }
    }

    /**
     * Vérifier si un email est valide
     */
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Réinitialiser le formulaire
     */
    reset() {
        this.form.reset();
        this.errors = {};
        this.form.querySelectorAll('.field-error').forEach(error => error.remove());
        this.form.querySelectorAll('.border-red-500').forEach(field => {
            field.classList.remove('border-red-500');
            field.classList.add('border-gray-300');
        });
    }

    /**
     * Obtenir les données du formulaire
     */
    getData() {
        const formData = new FormData(this.form);
        return this.serializeFormData(formData);
    }

    /**
     * Définir les données du formulaire
     */
    setData(data) {
        Object.keys(data).forEach(key => {
            const field = this.form.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'file') {
                    // Ne pas définir les fichiers pour des raisons de sécurité
                    return;
                }
                field.value = data[key];
            }
        });
    }
}

// Exporter la classe
export default SecureForm; 