<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('pcma_extra.label_588d22627bb4') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8 text-blue-600">
            {{ __('pcma_extra.label_99a77ce7ff90') }}
        </h1>
        
        <!-- Section vocale simplifiée -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-green-600">
                🗣️ Google Assistant
            </h2>
            
            <div class="space-y-4">
                <div class="flex space-x-4">
                    <button id="start-voice-btn" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold">
                        {{ __('pcma_extra.label_fe394bfdd5e4') }}
                    </button>
                    <button id="stop-voice-btn" class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hidden">
                        {{ __('pcma_extra.label_0001512c0407') }}
                    </button>
                </div>
                
                <div id="voice-status" class="text-lg font-semibold text-gray-600">
                    {{ __('pcma_extra.label_1cf9e7291ff1') }}
                </div>
                
                <div id="voice-preview" class="bg-gray-100 p-4 rounded-lg min-h-[60px]">
                    <p class="text-gray-500">{{ __('pcma_extra.label_4ad2eba889b9') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-3 text-blue-800">{{ __('pcma_extra.label_8454a2f0db59') }}</h3>
            <ul class="list-disc list-inside space-y-2 text-blue-700">
                <li>{{ __('pcma_extra.label_b2eb0dc81d7c') }}</li>
                <li>{{ __('pcma_extra.label_cd1341384420') }}</li>
                <li>{{ __('pcma_extra.label_d5ed62f372b5') }}</li>
                <li>{{ __('pcma_extra.label_fd7e01b06d5e') }}</li>
                <li>{{ __('pcma_extra.label_7aac5dafe000') }}</li>
            </ul>
        </div>
    </div>

    <script>
    // Assistant vocal PCMA - Version de test simplifiée
    let recognition = null;
    let isListening = false;

    function initSpeechRecognition() {
        if (!("webkitSpeechRecognition" in window)) {
            alert("La reconnaissance vocale n'est pas supportée par votre navigateur.");
            return;
        }
        
        recognition = new webkitSpeechRecognition();
        recognition.continuous = true;           // Mode continu activé
        recognition.interimResults = true;      // Résultats intermédiaires
        recognition.lang = "fr-FR";             // Langue française
        
        recognition.onstart = function() {
            console.log("🎤 Reconnaissance vocale démarrée");
            isListening = true;
            document.getElementById("voice-status").textContent = "🎤 Écoute en cours...";
            document.getElementById("voice-status").className = "text-green-600 font-semibold";
            
            // Afficher/masquer les boutons
            document.getElementById("start-voice-btn").classList.add("hidden");
            document.getElementById("stop-voice-btn").classList.remove("hidden");
        };
        
        recognition.onend = function() {
            console.log("🎤 Reconnaissance vocale terminée");
            if (isListening) {
                console.log("🔄 Redémarrage automatique de la reconnaissance...");
                setTimeout(() => {
                    if (isListening) {
                        recognition.start();
                    }
                }, 100);
            }
        };
        
        recognition.onspeechend = function() {
            console.log("🎤 Fin de parole détectée");
            // NE PAS arrêter la reconnaissance ici
        };
        
        recognition.onresult = function(event) {
            let finalTranscript = "";
            let interimTranscript = "";
            
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    finalTranscript += transcript;
                } else {
                    interimTranscript += transcript;
                }
            }
            
            if (finalTranscript) {
                console.log("🎤 Texte final:", finalTranscript);
                processVoiceInput(finalTranscript);
            }
            
            if (interimTranscript) {
                console.log("🎤 Texte intermédiaire:", interimTranscript);
                document.getElementById("voice-preview").textContent = "Vous avez dit: " + interimTranscript;
            }
        };
        
        recognition.onerror = function(event) {
            console.log("🎤 Erreur de reconnaissance:", event.error);
            if (event.error === "no-speech") {
                console.log("🔄 Tentative de redémarrage après erreur no-speech...");
                setTimeout(() => {
                    if (isListening) {
                        recognition.start();
                    }
                }, 100);
            } else if (event.error === "network") {
                document.getElementById("voice-status").textContent = "❌ Erreur réseau";
                document.getElementById("voice-status").className = "text-red-600 font-semibold";
            }
        };
    }

    function startVoiceRecognition() {
        console.log("🚀 Démarrage de la reconnaissance vocale...");
        
        if (!recognition) {
            initSpeechRecognition();
        }
        
        if (recognition && !isListening) {
            try {
                recognition.start();
                speakResponse("Assistant vocal PCMA activé. Dites-moi les informations du formulaire.");
            } catch (error) {
                console.error("Erreur lors du démarrage:", error);
                // Réinitialiser et réessayer
                recognition = null;
                initSpeechRecognition();
                recognition.start();
            }
        }
    }

    function stopVoiceRecognition() {
        console.log("🛑 Arrêt de la reconnaissance vocale...");
        isListening = false;
        if (recognition) {
            recognition.stop();
        }
        document.getElementById("voice-status").textContent = "⏸️ Reconnaissance arrêtée";
        document.getElementById("voice-status").className = "text-yellow-600 font-semibold";
        
        // Afficher/masquer les boutons
        document.getElementById("start-voice-btn").classList.remove("hidden");
        document.getElementById("stop-voice-btn").classList.add("hidden");
    }

    function processVoiceInput(text) {
        const lowerText = text.toLowerCase();
        console.log("🎯 Traitement de l'entrée vocale:", text);
        
        // Instructions de base
        if (lowerText.includes("commencer") || lowerText.includes("démarrer")) {
            speakResponse("Examen PCMA démarré. Dites-moi les informations du joueur.");
            return;
        }
        
        if (lowerText.includes("arrêter") || lowerText.includes("stop")) {
            stopVoiceRecognition();
            return;
        }
        
        // Remplissage automatique des champs
        if (lowerText.includes("nom du joueur") || lowerText.includes("nom joueur")) {
            const playerName = text.split(":")[1] || text.split("est")[1] || text.split("c'est")[1];
            if (playerName) {
                speakResponse("Nom du joueur enregistré: " + playerName.trim());
            }
            return;
        }
        
        if (lowerText.includes("âge") || lowerText.includes("age")) {
            const age = text.match(/\d+/);
            if (age) {
                speakResponse("Âge enregistré: " + age[0] + " ans");
            }
            return;
        }
        
        // Réponse par défaut
        speakResponse("J'ai entendu: " + text + ". Continuez à me donner les informations du formulaire.");
    }

    function speakResponse(text) {
        if ("speechSynthesis" in window) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = "fr-FR";
            utterance.rate = 0.9;
            speechSynthesis.speak(utterance);
        }
        
        // Afficher dans l'interface
        document.getElementById("voice-preview").textContent = "Assistant: " + text;
    }

    // Initialisation au chargement de la page
    document.addEventListener("DOMContentLoaded", function() {
        console.log("📄 DOM Content Loaded - JavaScript is running!");
        
        // Initialiser la reconnaissance vocale
        initSpeechRecognition();
        
        // Ajouter les event listeners
        const startBtn = document.getElementById("start-voice-btn");
        const stopBtn = document.getElementById("stop-voice-btn");
        
        if (startBtn) {
            startBtn.addEventListener("click", function() {
                console.log("Bouton de démarrage cliqué");
                startVoiceRecognition();
            });
        }
        
        if (stopBtn) {
            stopBtn.addEventListener("click", function() {
                stopVoiceRecognition();
            });
        }
    });
    </script>
</body>
</html> 