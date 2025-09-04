/**
 * 🎤 ServiceVocal.js - Service de reconnaissance vocale robuste
 * Intégration directe avec le flux de transcription pour formulaires médicaux
 * Compatible FIT (VueJS + Tailwind + NodeJS backend)
 */

class ServiceVocal {
    constructor() {
        this.recognition = null;
        this.isListening = false;
        this.isInitialized = false;
        this.onTranscriptCallback = null;
        this.onErrorCallback = null;
        this.onStartCallback = null;
        this.onStopCallback = null;
        this.interimResults = [];
        this.finalResults = [];
        this.confidence = 0;
        this.language = 'fr-FR';
        this.maxAlternatives = 3;
        
        console.log('🎤 ServiceVocal initialisé');
    }

    /**
     * 🔧 Initialiser le service de reconnaissance vocale
     */
    async initialize() {
        try {
            // Vérifier la disponibilité de l'API
            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                throw new Error('API de reconnaissance vocale non supportée par ce navigateur');
            }

            // Créer l'instance de reconnaissance
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();

            // Configuration avancée
            this.recognition.continuous = true;           // Reconnaissance continue
            this.recognition.interimResults = true;       // Résultats intermédiaires
            this.recognition.maxAlternatives = this.maxAlternatives;
            this.recognition.lang = this.language;

            // 🔧 NOUVEAU : Configuration pour la reconnaissance médicale
            this.recognition.grammars = this.createMedicalGrammar();

            // Configuration des événements
            this.setupEventHandlers();

            this.isInitialized = true;
            console.log('✅ ServiceVocal initialisé avec succès');
            
            return true;

        } catch (error) {
            console.error('❌ Erreur lors de l\'initialisation de ServiceVocal:', error);
            this.onErrorCallback?.(error);
            return false;
        }
    }

    /**
     * 🔧 Créer une grammaire médicale pour améliorer la reconnaissance
     */
    createMedicalGrammar() {
        try {
            // Créer une grammaire JSGF pour les termes médicaux
            const medicalGrammar = new SpeechGrammarList();
            
            // Grammaire pour les diagnostics courants
            const diagnosticGrammar = `
                #JSGF V1.0;
                grammar diagnostics;
                public <diagnostic> = 
                    entorse | fracture | luxation | contusion | ecchymose |
                    tendinite | bursite | arthrite | arthrose | hernie |
                    sciatique | lombalgie | cervicalgie | céphalée | migraine |
                    hypertension | diabète | asthme | bronchite | pneumonie |
                    infarctus | angine | arythmie | insuffisance | insuffisance cardiaque |
                    insuffisance rénale | insuffisance hépatique | cirrhose | hépatite |
                    ulcère | gastrite | colite | appendicite | péritonite |
                    tumeur | cancer | métastase | rémission | récidive;
            `;
            
            medicalGrammar.addFromString(diagnosticGrammar, 1.0);
            
            return medicalGrammar;
            
        } catch (error) {
            console.warn('⚠️ Grammaire médicale non supportée, utilisation de la reconnaissance standard');
            return null;
        }
    }

    /**
     * 🔧 Configuration des gestionnaires d'événements
     */
    setupEventHandlers() {
        if (!this.recognition) return;

        // 🎯 Résultats intermédiaires (en temps réel)
        this.recognition.onresult = (event) => {
            console.log('🎯 Résultats vocaux reçus:', event.results);
            
            let interimTranscript = '';
            let finalTranscript = '';
            let maxConfidence = 0;

            // Traiter tous les résultats
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                const confidence = event.results[i][0].confidence;
                const isFinal = event.results[i].isFinal;

                if (isFinal) {
                    finalTranscript += transcript;
                    this.finalResults.push({
                        transcript,
                        confidence,
                        timestamp: Date.now()
                    });
                } else {
                    interimTranscript += transcript;
                    this.interimResults.push({
                        transcript,
                        confidence,
                        timestamp: Date.now()
                    });
                }

                maxConfidence = Math.max(maxConfidence, confidence);
            }

            this.confidence = maxConfidence;

            // 🔧 NOUVEAU : Déclencher le callback onTranscript en temps réel
            if (this.onTranscriptCallback) {
                this.onTranscriptCallback({
                    transcript: finalTranscript || interimTranscript,
                    confidence: this.confidence,
                    isFinal: finalTranscript.length > 0,
                    interim: interimTranscript,
                    final: finalTranscript,
                    allResults: event.results
                });
            }

            console.log('📝 Transcription:', {
                interim: interimTranscript,
                final: finalTranscript,
                confidence: this.confidence,
                isFinal: finalTranscript.length > 0
            });
        };

        // 🎯 Démarrage de la reconnaissance
        this.recognition.onstart = () => {
            this.isListening = true;
            console.log('🎤 Reconnaissance vocale démarrée');
            this.onStartCallback?.();
        };

        // 🎯 Arrêt de la reconnaissance
        this.recognition.onend = () => {
            this.isListening = false;
            console.log('⏸️ Reconnaissance vocale arrêtée');
            this.onStopCallback?.();
        };

        // 🎯 Erreurs
        this.recognition.onerror = (event) => {
            console.error('❌ Erreur de reconnaissance vocale:', event.error);
            this.isListening = false;
            this.onErrorCallback?.(event.error);
        };

        // 🎯 Pas de parole détectée
        this.recognition.onspeechend = () => {
            console.log('🔇 Fin de parole détectée');
        };

        // 🎯 Audio détecté
        this.recognition.onaudiostart = () => {
            console.log('🔊 Audio détecté');
        };

        // 🎯 Fin d'audio
        this.recognition.onaudioend = () => {
            console.log('🔇 Fin d\'audio');
        };

        // 🎯 Pas de parole
        this.recognition.onnomatch = () => {
            console.log('🔇 Aucune correspondance trouvée');
        };
    }

    /**
     * 🎤 Démarrer la reconnaissance vocale
     */
    startListening() {
        if (!this.isInitialized) {
            console.error('❌ ServiceVocal non initialisé');
            return false;
        }

        if (this.isListening) {
            console.warn('⚠️ Reconnaissance vocale déjà en cours');
            return false;
        }

        try {
            this.recognition.start();
            console.log('🎤 Démarrage de la reconnaissance vocale...');
            return true;
        } catch (error) {
            console.error('❌ Erreur lors du démarrage:', error);
            this.onErrorCallback?.(error);
            return false;
        }
    }

    /**
     * ⏸️ Arrêter la reconnaissance vocale
     */
    stopListening() {
        if (!this.isInitialized || !this.isListening) {
            console.warn('⚠️ Reconnaissance vocale non en cours');
            return false;
        }

        try {
            this.recognition.stop();
            console.log('⏸️ Arrêt de la reconnaissance vocale...');
            return true;
        } catch (error) {
            console.error('❌ Erreur lors de l\'arrêt:', error);
            this.onErrorCallback?.(error);
            return false;
        }
    }

    /**
     * 🔧 Définir le callback de transcription
     */
    onTranscript(callback) {
        this.onTranscriptCallback = callback;
        console.log('✅ Callback onTranscript configuré');
    }

    /**
     * 🔧 Définir le callback d'erreur
     */
    onError(callback) {
        this.onErrorCallback = callback;
        console.log('✅ Callback onError configuré');
    }

    /**
     * 🔧 Définir le callback de démarrage
     */
    onStart(callback) {
        this.onStartCallback = callback;
        console.log('✅ Callback onStart configuré');
    }

    /**
     * 🔧 Définir le callback d'arrêt
     */
    onStop(callback) {
        this.onStopCallback = callback;
        console.log('✅ Callback onStop configuré');
    }

    /**
     * 🔧 Changer la langue
     */
    setLanguage(language) {
        if (this.recognition) {
            this.recognition.lang = language;
            this.language = language;
            console.log(`🌍 Langue changée vers: ${language}`);
        }
    }

    /**
     * 🔧 Obtenir le statut actuel
     */
    getStatus() {
        return {
            isInitialized: this.isInitialized,
            isListening: this.isListening,
            confidence: this.confidence,
            language: this.language,
            interimResults: this.interimResults.length,
            finalResults: this.finalResults.length
        };
    }

    /**
     * 🔧 Réinitialiser les résultats
     */
    resetResults() {
        this.interimResults = [];
        this.finalResults = [];
        this.confidence = 0;
        console.log('🔄 Résultats réinitialisés');
    }

    /**
     * 🔧 Obtenir les derniers résultats
     */
    getLatestResults() {
        return {
            interim: this.interimResults.slice(-5),  // 5 derniers résultats intermédiaires
            final: this.finalResults.slice(-5),      // 5 derniers résultats finaux
            confidence: this.confidence
        };
    }

    /**
     * 🔧 Nettoyer les ressources
     */
    destroy() {
        if (this.recognition) {
            this.recognition.stop();
            this.recognition = null;
        }
        
        this.isListening = false;
        this.isInitialized = false;
        this.onTranscriptCallback = null;
        this.onErrorCallback = null;
        this.onStartCallback = null;
        this.onStopCallback = null;
        
        console.log('🗑️ ServiceVocal détruit');
    }
}

// 🔧 Export pour utilisation dans d'autres modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ServiceVocal;
} else if (typeof window !== 'undefined') {
    window.ServiceVocal = ServiceVocal;
}

console.log('✅ ServiceVocal.js chargé avec succès');

