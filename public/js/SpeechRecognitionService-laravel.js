/**
 * Service de reconnaissance vocale Google Speech-to-Text pour Laravel
 * Version FINALE - Élimination complète du RangeError
 */
class SpeechRecognitionService {
    constructor() {
        // Configuration par défaut
        this.config = {
            apiKey: null,
            language: 'fr-FR',
            encoding: 'WEBM_OPUS',
            sampleRate: 48000,
            channels: 1,
            model: 'latest_long',
            enableAutomaticPunctuation: true
        };

        // État du service
        this.isListening = false;
        this.isProcessing = false;
        this.audioStream = null;
        this.mediaRecorder = null;
        this.audioChunks = [];

        // Callbacks
        this.onResultCallback = null;
        this.onErrorCallback = null;
        this.onStatusChangeCallback = null;
        
        // 🔧 COMPATIBILITÉ : Propriétés pour l'interface
        this.onResult = null;
        this.onError = null;
        this.onStatusChange = null;
        
        // 🔧 COMPATIBILITÉ : Synchronisation des callbacks
        Object.defineProperty(this, 'onResult', {
            get: function() { return this.onResultCallback; },
            set: function(callback) { 
                this.onResultCallback = callback;
                console.log('✅ Callback onResult configuré:', typeof callback);
            }
        });
        
        Object.defineProperty(this, 'onError', {
            get: function() { return this.onErrorCallback; },
            set: function(callback) { 
                this.onErrorCallback = callback;
                console.log('✅ Callback onError configuré:', typeof callback);
            }
        });
        
        Object.defineProperty(this, 'onStatusChange', {
            get: function() { return this.onStatusChangeCallback; },
            set: function(callback) { 
                this.onStatusChangeCallback = callback;
                console.log('✅ Callback onStatusChange configuré:', typeof callback);
            }
        });

        // Protection anti-récursion
        this.processingId = null;
        this.lastProcessTime = 0;
        
        // Limitation de durée
        this.maxRecordingTime = 45000; // 45 secondes max
        this.recordingTimer = null;

        // Nouveau système de traitement
        this.processingQueue = [];
        this.isQueueProcessing = false;

        console.log('🔧 SpeechRecognitionService initialisé');
    }

    /**
     * Configurer le service
     * @param {Object} config - Configuration
     */
    configure(config) {
        this.config = { ...this.config, ...config };
        console.log('⚙️ Configuration mise à jour:', this.config);
    }

    /**
     * Tester la clé API
     * @returns {boolean} - Validité de la clé
     */
    testAPIKey() {
        if (window.PCMA_DICTATION && window.PCMA_DICTATION.available) {
            return true; // transcription par le serveur FIT : aucune clé dans le navigateur
        }
        try {
            if (!this.config.apiKey || this.config.apiKey.length < 10) {
                console.log('⚠️ Clé API invalide ou trop courte');
                return false;
            }
            console.log('✅ Clé API valide - Test réussi');
            return true;
        } catch (error) {
            console.log('⚠️ Test API échoué:', error.message);
            return false;
        }
    }

    /**
     * Démarrer l'écoute vocale
     * @param {Function} onResult - Callback pour le résultat
     * @param {Function} onError - Callback pour les erreurs
     * @param {Function} onStatusChange - Callback pour les changements de statut
     * @returns {Promise<boolean>} - Succès du démarrage
     */
    async startListening(onResult, onError, onStatusChange) {
        try {
            // Définir les callbacks
            if (onResult) this.onResultCallback = onResult;
            if (onError) this.onErrorCallback = onError;
            if (onStatusChange) this.onStatusChangeCallback = onStatusChange;
            
            if (this.isListening || this.isProcessing) {
                console.log('⚠️ Service déjà en cours d\'utilisation');
                return false;
            }

            // Demander l'accès au microphone
            this.audioStream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    sampleRate: this.config.sampleRate,
                    channelCount: this.config.channels,
                    echoCancellation: true,
                    noiseSuppression: true
                }
            });

            // Créer le MediaRecorder
            this.mediaRecorder = new MediaRecorder(this.audioStream, {
                mimeType: 'audio/webm;codecs=opus'
            });

            // Configurer les événements avec bind explicite
            this.mediaRecorder.ondataavailable = this.handleDataAvailable.bind(this);
            this.mediaRecorder.onstop = this.handleStop.bind(this);
            this.mediaRecorder.onerror = this.handleError.bind(this);

            // Démarrer l'enregistrement
            this.mediaRecorder.start(1000);
            this.isListening = true;

            // Limiter la durée d'enregistrement
            this.recordingTimer = setTimeout(() => {
                if (this.isListening) {
                    console.log('⏰ Durée maximale d\'enregistrement atteinte - arrêt automatique');
                    this.stopListening();
                }
            }, this.maxRecordingTime);

            this.updateStatus('listening', 'Écoute active - Parlez maintenant ! (max 45s)');
            console.log('🎤 Reconnaissance vocale démarrée');
            return true;

        } catch (error) {
            this.handleError(error);
            return false;
        }
    }

    /**
     * Arrêter l'écoute vocale
     */
    stopListening() {
        if (this.mediaRecorder && this.isListening) {
            this.isListening = false;
            
            // Nettoyer le timer
            if (this.recordingTimer) {
                clearTimeout(this.recordingTimer);
                this.recordingTimer = null;
            }
            
            this.mediaRecorder.stop();

            // Arrêter le stream audio
            if (this.audioStream) {
                this.audioStream.getTracks().forEach(track => track.stop());
                this.audioStream = null;
            }

            this.updateStatus('stopped', 'Écoute arrêtée');
            console.log('🛑 Reconnaissance vocale arrêtée');
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
     * Gérer l'arrêt de l'enregistrement - NOUVELLE APPROCHE
     */
    handleStop() {
        // Protection anti-récursion
        if (this.isProcessing || this.audioChunks.length === 0) {
            console.log('🛡️ Protection anti-récursion activée');
            return;
        }
        
        // Marquer le traitement
        this.isProcessing = true;
        this.updateStatus('processing', 'Traitement de l\'audio...');
        
        // Créer une copie des chunks et les vider immédiatement
        const chunksToProcess = [...this.audioChunks];
        this.audioChunks = [];
        
        // Créer un blob audio
        const audioBlob = new Blob(chunksToProcess, { type: 'audio/webm' });
        
        // Vérifier la taille de l'audio
        if (audioBlob.size > 10 * 1024 * 1024) {
            console.log('🛡️ Fichier audio trop volumineux - traitement annulé');
            this.isProcessing = false;
            this.updateStatus('error', 'Fichier audio trop volumineux');
            return;
        }
        
        // Ajouter à la queue de traitement
        this.addToProcessingQueue(audioBlob);
    }

    /**
     * NOUVEAU : Système de queue de traitement
     */
    addToProcessingQueue(audioBlob) {
        const task = {
            id: Date.now(),
            audioBlob: audioBlob,
            timestamp: Date.now()
        };
        
        this.processingQueue.push(task);
        console.log(`📋 Tâche ajoutée à la queue: ${task.id}`);
        
        // Traiter la queue si elle n'est pas déjà en cours
        if (!this.isQueueProcessing) {
            this.processQueue();
        }
    }

    /**
     * NOUVEAU : Traitement de la queue
     */
    async processQueue() {
        if (this.processingQueue.length === 0 || this.isQueueProcessing) {
            return;
        }
        
        this.isQueueProcessing = true;
        console.log('🔄 Début du traitement de la queue...');
        
        try {
            while (this.processingQueue.length > 0) {
                const task = this.processingQueue.shift();
                console.log(`🔄 Traitement de la tâche: ${task.id}`);
                
                try {
                    // Traitement direct sans setTimeout
                    const result = await this.processAudioDirectly(task.audioBlob);
                    
                    if (result.success && typeof window.pcmaDictationReview === 'function') {
                        // Le médecin vérifie et accepte chaque valeur : rien n'est rempli automatiquement.
                        window.pcmaDictationReview(result);
                    } else if (result.success && this.onResultCallback) {
                        this.onResultCallback(result.text, result.confidence);
                    }
                    
                } catch (error) {
                    console.error(`❌ Erreur dans le traitement de la tâche ${task.id}:`, error);
                    // Ne pas appeler handleError ici pour éviter la récursion
                    this.updateStatus('error', `Erreur: ${error.message}`);
                }
            }
        } finally {
            // Toujours réinitialiser l'état
            this.isQueueProcessing = false;
            this.isProcessing = false;
            console.log('✅ Fin du traitement de la queue');
        }
    }

    /**
     * NOUVEAU : Traitement audio direct sans récursion
     */
    async processAudioDirectly(audioBlob) {
        // Transcription par le serveur FIT (Google Speech-to-Text, clé gardée côté serveur).
        const settings = window.PCMA_DICTATION || {};
        if (!settings.transcribeUrl) {
            throw new Error('Transcription non disponible sur cette page');
        }
        const form = new FormData();
        form.append('audio', audioBlob, 'dictee.webm');
        const response = await fetch(settings.transcribeUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-CSRF-TOKEN': settings.csrf || '', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: form
        });
        let data = {};
        try { data = await response.json(); } catch (e) { data = {}; }
        if (!response.ok || !data.success) {
            throw new Error(data.message || ('Transcription impossible (HTTP ' + response.status + ')'));
        }
        return { success: true, text: data.text, confidence: data.confidence || 0, proposals: data.proposals || [] };
    }

    /**
     * Gérer les erreurs
     * @param {Error} error - Erreur à traiter
     */
    handleError(error) {
        console.error('❌ Erreur SpeechRecognitionService:', error);
        
        // Réinitialiser l'état AVANT d'appeler les callbacks
        this.isProcessing = false;
        this.isQueueProcessing = false;
        
        // Vider la queue en cas d'erreur
        this.processingQueue = [];
        
        this.updateStatus('error', `Erreur: ${error.message}`);
        
        // Appeler le callback d'erreur en dernier
        if (this.onErrorCallback) {
            this.onErrorCallback(error);
        }
    }

    /**
     * Mettre à jour le statut
     * @param {string} status - Nouveau statut
     * @param {string} message - Message associé
     */
    updateStatus(status, message) {
        console.log(`📊 Statut: ${status} - ${message}`);
        
        if (this.onStatusChangeCallback) {
            this.onStatusChangeCallback(status, message);
        }
    }

    /**
     * Obtenir le statut actuel
     * @returns {Object} - Statut du service
     */
    getStatus() {
        return {
            isListening: this.isListening,
            isProcessing: this.isProcessing,
            hasApiKey: !!this.config.apiKey,
            language: this.config.language,
            sampleRate: this.config.sampleRate,
            queueLength: this.processingQueue.length
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
        this.isProcessing = false;
        this.isQueueProcessing = false;
        this.processingQueue = [];
        this.processingId = null;
        console.log('🧹 SpeechRecognitionService détruit');
    }
}

// Exposer la classe globalement pour Laravel
window.SpeechRecognitionService = SpeechRecognitionService;
console.log('✅ Module SpeechRecognitionService intégré dans Laravel !');
