/**
 * SpeechRecognitionService.js
 * Module ES6 pour la reconnaissance vocale via Google Speech-to-Text API
 * Version production - Prêt pour intégration Laravel
 */

class SpeechRecognitionService {
    constructor() {
        // Configuration par défaut
        this.config = {
            apiKey: null,
            language: 'fr-FR',
            sampleRate: 16000,
            channels: 1,
            encoding: 'WEBM_OPUS',
            model: 'latest_long',
            useEnhanced: true,
            enableAutomaticPunctuation: true
        };

        // État du service
        this.isListening = false;
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.audioStream = null;
        this.onResultCallback = null;
        this.onErrorCallback = null;
        this.onStatusChangeCallback = null;

        // Bind des méthodes
        this.handleDataAvailable = this.handleDataAvailable.bind(this);
        this.handleStop = this.handleStop.bind(this);
        this.handleError = this.handleError.bind(this);
    }

    /**
     * Initialiser le service avec une clé API
     * @param {string} apiKey - Clé API Google Speech-to-Text
     * @param {Object} options - Options de configuration
     * @returns {Promise<boolean>} - Succès de l'initialisation
     */
    async init(apiKey, options = {}) {
        try {
            // Validation de la clé API
            if (!apiKey || typeof apiKey !== 'string') {
                throw new Error('Clé API invalide ou manquante');
            }

            this.config.apiKey = apiKey;
            Object.assign(this.config, options);

            // Vérification de la disponibilité des APIs
            if (!this.checkBrowserSupport()) {
                throw new Error('Navigateur non supporté');
            }

            // Test de la clé API
            await this.testAPIKey();
            
            this.updateStatus('ready', 'Service initialisé avec succès');
            console.log('✅ SpeechRecognitionService initialisé');
            return true;

        } catch (error) {
            this.handleError(error);
            return false;
        }
    }

    /**
     * Vérifier la compatibilité du navigateur
     * @returns {boolean}
     */
    checkBrowserSupport() {
        const hasMediaRecorder = 'MediaRecorder' in window;
        const hasGetUserMedia = navigator.mediaDevices && navigator.mediaDevices.getUserMedia;
        const hasFetch = 'fetch' in window;

        if (!hasMediaRecorder) {
            throw new Error('MediaRecorder non supporté par votre navigateur');
        }
        if (!hasGetUserMedia) {
            throw new Error('Accès microphone non supporté');
        }
        if (!hasFetch) {
            throw new Error('Fetch API non supportée');
        }

        return true;
    }

    /**
     * Tester la clé API Google
     * @returns {Promise<boolean>}
     */
    async testAPIKey() {
        try {
            const testAudio = new Blob(['test'], { type: 'audio/webm' });
            const result = await this.callGoogleSpeechAPI(testAudio);
            return result.success;
        } catch (error) {
            throw new Error(`Clé API invalide: ${error.message}`);
        }
    }

    /**
     * Démarrer l'écoute vocale
     * @param {Function} onResult - Callback appelé avec le résultat
     * @param {Function} onError - Callback appelé en cas d'erreur
     * @param {Function} onStatusChange - Callback pour les changements de statut
     * @returns {Promise<boolean>}
     */
    async startListening(onResult, onError, onStatusChange) {
        try {
            if (this.isListening) {
                throw new Error('Écoute déjà en cours');
            }

            // Enregistrer les callbacks
            this.onResultCallback = onResult;
            this.onErrorCallback = onError;
            this.onStatusChangeCallback = onStatusChange;

            // Demander l'accès au microphone
            this.audioStream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    sampleRate: this.config.sampleRate,
                    channelCount: this.config.channels,
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true
                }
            });

            // Créer l'enregistreur audio
            this.mediaRecorder = new MediaRecorder(this.audioStream, {
                mimeType: 'audio/webm;codecs=opus'
            });

            // Configuration des événements
            this.mediaRecorder.ondataavailable = this.handleDataAvailable;
            this.mediaRecorder.onstop = this.handleStop;
            this.mediaRecorder.onerror = this.handleError;

            // Démarrer l'enregistrement
            this.mediaRecorder.start(1000); // Chunk toutes les secondes
            this.isListening = true;
            this.audioChunks = [];

            this.updateStatus('listening', 'Écoute en cours - Parlez maintenant !');
            console.log('🎤 Écoute vocale démarrée');
            return true;

        } catch (error) {
            this.handleError(error);
            return false;
        }
    }

    /**
     * Arrêter l'écoute vocale
     * @returns {boolean}
     */
    stopListening() {
        try {
            if (!this.isListening) {
                return false;
            }

            this.isListening = false;

            // Arrêter l'enregistrement
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                this.mediaRecorder.stop();
            }

            // Arrêter le stream audio
            if (this.audioStream) {
                this.audioStream.getTracks().forEach(track => track.stop());
                this.audioStream = null;
            }

            this.updateStatus('stopped', 'Écoute arrêtée');
            console.log('🛑 Écoute vocale arrêtée');
            return true;

        } catch (error) {
            this.handleError(error);
            return false;
        }
    }

    /**
     * Gérer les données audio disponibles
     * @param {MediaRecorderDataAvailableEvent} event
     */
    handleDataAvailable(event) {
        if (event.data.size > 0) {
            this.audioChunks.push(event.data);
        }
    }

    /**
     * Gérer l'arrêt de l'enregistrement
     */
    async handleStop() {
        try {
            if (this.audioChunks.length === 0) {
                console.log('⚠️ Aucun audio à traiter');
                return;
            }

            this.updateStatus('processing', 'Traitement de l\'audio...');
            console.log('🎤 Enregistrement terminé, traitement via API...');

            // Traiter l'audio via Google Speech-to-Text
            const result = await this.callGoogleSpeechAPI();
            
            if (result.success && this.onResultCallback) {
                this.onResultCallback(result.text, result.confidence);
            }

            // Réinitialiser les chunks
            this.audioChunks = [];

        } catch (error) {
            this.handleError(error);
        }
    }

    /**
     * Appeler l'API Google Speech-to-Text
     * @returns {Promise<Object>}
     */
    async callGoogleSpeechAPI() {
        try {
            if (!this.config.apiKey) {
                throw new Error('Clé API non configurée');
            }

            if (this.audioChunks.length === 0) {
                throw new Error('Aucun audio à traiter');
            }

            // Créer un blob audio
            const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
            
            // Convertir en base64
            const arrayBuffer = await audioBlob.arrayBuffer();
            const base64Audio = btoa(String.fromCharCode(...new Uint8Array(arrayBuffer)));

            // Configuration de la requête
            const apiUrl = `https://speech.googleapis.com/v1/speech:recognize?key=${this.config.apiKey}`;
            const requestBody = {
                config: {
                    encoding: this.config.encoding,
                    sampleRateHertz: this.config.sampleRate,
                    languageCode: this.config.language,
                    enableAutomaticPunctuation: this.config.enableAutomaticPunctuation,
                    model: this.config.model,
                    useEnhanced: this.config.useEnhanced
                },
                audio: {
                    content: base64Audio
                }
            };

            // Appel à l'API
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Erreur API Google: ${response.status} - ${errorText}`);
            }

            const result = await response.json();
            
            if (result.results && result.results.length > 0) {
                const transcript = result.results[0].alternatives[0].transcript;
                const confidence = result.results[0].alternatives[0].confidence;
                
                console.log('🎯 Texte reconnu par Google:', transcript);
                console.log('📊 Confiance:', confidence);
                
                return {
                    success: true,
                    text: transcript,
                    confidence: confidence,
                    source: 'Google Speech-to-Text'
                };
            } else {
                throw new Error('Aucun résultat de reconnaissance');
            }

        } catch (error) {
            console.error('❌ Erreur API Google Speech-to-Text:', error);
            throw error;
        }
    }

    /**
     * Gérer les erreurs
     * @param {Error} error
     */
    handleError(error) {
        console.error('❌ Erreur SpeechRecognitionService:', error);
        
        if (this.onErrorCallback) {
            this.onErrorCallback(error);
        }

        this.updateStatus('error', `Erreur: ${error.message}`);
    }

    /**
     * Mettre à jour le statut
     * @param {string} status - Statut actuel
     * @param {string} message - Message descriptif
     */
    updateStatus(status, message) {
        console.log(`📊 Statut: ${status} - ${message}`);
        
        if (this.onStatusChangeCallback) {
            this.onStatusChangeCallback(status, message);
        }
    }

    /**
     * Obtenir le statut actuel
     * @returns {Object}
     */
    getStatus() {
        return {
            isListening: this.isListening,
            hasApiKey: !!this.config.apiKey,
            audioChunksCount: this.audioChunks.length,
            config: { ...this.config }
        };
    }

    /**
     * Nettoyer les ressources
     */
    destroy() {
        this.stopListening();
        this.onResultCallback = null;
        this.onErrorCallback = null;
        this.onStatusChangeCallback = null;
        this.config.apiKey = null;
        console.log('🧹 SpeechRecognitionService détruit');
    }
}

// Export pour utilisation ES6
export default SpeechRecognitionService;

// Export pour utilisation globale (fallback)
if (typeof window !== 'undefined') {
    window.SpeechRecognitionService = SpeechRecognitionService;
}

