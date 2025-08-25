<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhisperService;
use App\Models\VoiceSession;
use App\Services\PcmaMappingService;
use App\Services\VoiceResponseService;
use App\Events\PcmaVoiceCompleted;

class GoogleAssistantController extends Controller
{
    protected $whisperService;
    protected $pcmaMappingService;
    protected $voiceResponseService;

    public function __construct(
        WhisperService $whisperService, 
        PcmaMappingService $pcmaMappingService,
        VoiceResponseService $voiceResponseService
    ) {
        $this->whisperService = $whisperService;
        $this->pcmaMappingService = $pcmaMappingService;
        $this->voiceResponseService = $voiceResponseService;
    }

    public function handleIntent(Request $request)
    {
        try {
            $data = $request->all();
            \Log::info("Webhook received:", $data);
            
            $queryResult = $data['queryResult'] ?? null;
            if (!$queryResult) {
                return response()->json(['error' => 'No queryResult found']);
            }
            
            $intent = $queryResult['intent']['displayName'] ?? 'unknown';
            $parameters = $queryResult['parameters'] ?? [];
            $contexts = $queryResult['outputContexts'] ?? [];
            
            // Extraire le session ID depuis la structure Dialogflow
            $sessionId = null;
            if (isset($data['session'])) {
                $sessionId = $data['session'];
            } elseif (isset($queryResult['session'])) {
                $sessionId = $queryResult['session'];
            }
            
            \Log::info("Intent:", ['intent' => $intent]);
            \Log::info("Parameters:", $parameters);
            \Log::info("Contexts:", $contexts);
            \Log::info("Session ID:", ['session' => $sessionId]);
            
            // Extraire ou créer une session de conversation
            $session = $this->getOrCreateSession($sessionId);
            
            // Gérer les erreurs de reconnaissance vocale
            if ($intent === 'Default Fallback Intent' || ($queryResult['intentDetectionConfidence'] ?? 1.0) < 0.5) {
                $session->increment('error_count');
                
                // Si trop d'erreurs, proposer le fallback
                if ($session->error_count >= 3) {
                    return response()->json($this->handleFallbackToWeb($session));
                }
                
                // Réponse d'aide après quelques erreurs
                if ($session->error_count >= 2) {
                    return response()->json($this->generateHelpResponse($session));
                }
            } else {
                // Reset le compteur d'erreur en cas de succès
                $session->update(['error_count' => 0]);
            }
            
            // Mettre à jour la session avec les nouveaux paramètres
            $this->updateSessionParameters($session, $parameters, $contexts);
            
            $response = $this->generateContextualResponse($intent, $session, $parameters);
            
            \Log::info("Response generated:", $response);
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            \Log::error("Error in handleIntent:", ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            // Incrémenter le compteur d'erreur en cas d'exception
            if (isset($session)) {
                $session->increment('error_count');
                
                if ($session->error_count >= 3) {
                    return response()->json($this->handleFallbackToWeb($session));
                }
            }
            
            return response()->json([
                'fulfillmentText' => 'Désolé, une erreur technique s\'est produite. Pouvez-vous répéter votre demande ?',
                'fulfillmentMessages' => [
                    [
                        'text' => [
                            'text' => ['Désolé, une erreur technique s\'est produite. Pouvez-vous répéter votre demande ?']
                        ]
                    ]
                ]
            ]);
        }
    }
    
    private function getOrCreateSession($sessionId)
    {
        // Si pas de session ID, utiliser un ID par défaut
        if (!$sessionId) {
            $sessionId = 'default-session';
        }
        
        \Log::info("Looking for session with dialogflow_session:", ['sessionId' => $sessionId]);
        
        // Chercher une session existante par l'ID Dialogflow dans la colonne dialogflow_session
        $session = VoiceSession::where('dialogflow_session', $sessionId)->first();
        
        if ($session) {
            \Log::info("Existing session found:", [
                'id' => $session->id,
                'dialogflow_session' => $session->dialogflow_session ?? 'unknown',
                'player_name' => $session->player_name
            ]);
        } else {
            \Log::info("No existing session found, creating new one");
            
            try {
                // Récupérer l'utilisateur authentifié
                $userId = auth()->id() ?? 1; // Utilisateur par défaut si pas d'auth
                
                $session = VoiceSession::create([
                    'user_id' => $userId,
                    'player_name' => 'Joueur', // Valeur par défaut
                    'current_field' => 'identite', // Commencer par l'identité
                    'dialogflow_session' => $sessionId,
                    'session_data' => [
                        'player_name' => null,
                        'age1' => null,
                        'position' => null
                    ],
                    'status' => 'active',
                    'language' => 'fr'
                ]);
                
                \Log::info("New session created successfully:", [
                    'id' => $session->id,
                    'dialogflow_session' => $session->dialogflow_session,
                    'user_id' => $session->user_id,
                    'player_name' => $session->player_name
                ]);
                
            } catch (\Exception $e) {
                \Log::error("Failed to create session:", [
                    'error' => $e->getMessage(),
                    'sessionId' => $sessionId
                ]);
                
                // Créer une session temporaire en mémoire
                $session = new VoiceSession([
                    'user_id' => $userId ?? 1,
                    'player_name' => 'Joueur',
                    'current_field' => 'identite',
                    'dialogflow_session' => $sessionId,
                    'session_data' => [
                        'player_name' => null,
                        'age1' => null,
                        'position' => null
                    ],
                    'status' => 'active',
                    'language' => 'fr'
                ]);
                
                \Log::info("Temporary session created in memory");
            }
        }
        
        return $session;
    }
    
    private function updateSessionParameters($session, $parameters, $contexts)
    {
        $currentData = $session->session_data ?? [];
        
        // Extraire les paramètres du contexte
        $contextParams = [];
        foreach ($contexts as $context) {
            if (isset($context["parameters"])) {
                $contextParams = array_merge($contextParams, $context["parameters"]);
            }
        }
        
        // Mettre à jour avec les nouveaux paramètres
        if (isset($parameters["player_name"]) && !empty($parameters["player_name"])) {
            $currentData["player_name"] = $parameters["player_name"];
            $session->player_name = $parameters["player_name"];
        }
        if (isset($parameters["person"]["name"]) && !empty($parameters["person"]["name"])) {
            $currentData["player_name"] = $parameters["person"]["name"];
            $currentData["person"] = $parameters["person"];
            $session->player_name = $parameters["person"]["name"];
        }
        if (isset($parameters["age1"]) && !empty($parameters["age1"])) {
            $currentData["age1"] = $parameters["age1"];
        }
        if (isset($parameters["age"]) && !empty($parameters["age"])) {
            $currentData["age"] = $parameters["age"];
        }
        if (isset($parameters["position"]) && !empty($parameters["position"])) {
            $currentData["position"] = $parameters["position"];
        }
        
        // Mettre à jour la session
        $session->session_data = $currentData;
        
        // S'assurer que dialogflow_session est défini si ce n'est pas déjà fait
        if (empty($session->dialogflow_session) && !empty($session->getAttribute('dialogflow_session'))) {
            $session->dialogflow_session = $session->getAttribute('dialogflow_session');
        }
        
        $session->save();
        
        \Log::info("Session updated:", [
            'session_id' => $session->id,
            'dialogflow_session' => $session->dialogflow_session,
            'player_name' => $session->player_name,
            'session_data' => $currentData
        ]);
    }
    
    private function generateContextualResponse($intent, $session, $parameters)
    {
        \Log::info("generateContextualResponse called:", ['intent' => $intent]);
        
        switch ($intent) {
            case 'answer_field':
                return $this->generateAnswerFieldResponse($session, $parameters);
            case 'submit_pcma':
                return $this->submitPcmaToFitFromSession($session);
            case 'confirm_submit':
            case 'yes_intent':
                return $this->handleConfirmation($session, true);
            case 'cancel_pcma':
            case 'no_intent':
                return $this->handleConfirmation($session, false);
            case 'restart_pcma':
                return $this->handleRestart($session);
            case 'correct_field':
                return $this->handleCorrection($session, $parameters);
            default:
                return [
                    'fulfillmentText' => 'Je ne comprends pas cette intention.',
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ['Je ne comprends pas cette intention.']
                            ]
                        ]
                    ]
                ];
        }
    }
    
    private function generateAnswerFieldResponse($session, $newParameters)
    {
        \Log::info("=== generateAnswerFieldResponse DEBUG ===");
        \Log::info("New parameters received:", $newParameters);
        
        // Combiner les nouveaux paramètres avec les données de session existantes
        $sessionData = $session->session_data ?? [];
        
        $playerName = $newParameters["player_name"] ?? $sessionData["player_name"] ?? null;
        $age = $newParameters["age1"] ?? $newParameters["age"] ?? $sessionData["age1"] ?? $sessionData["age"] ?? null;
        $position = $newParameters["position"] ?? $sessionData["position"] ?? null;
        
        // Pour la logique de réponse, prioriser le nouveau paramètre reçu
        $newPlayerName = $newParameters["player_name"] ?? null;
        $newAge = $newParameters["age1"] ?? $newParameters["age"] ?? null;
        $newPosition = $newParameters["position"] ?? null;
        
        // Convertir les objets en chaînes si nécessaire
        if (is_array($playerName) || is_object($playerName)) {
            $playerName = $playerName["name"] ?? "Joueur";
        }
        
        if (is_array($age) || is_object($age)) {
            $age = $age["amount"] ?? $age;
        }
        
        if (is_array($position) || is_object($position)) {
            $position = $position["name"] ?? $position;
        }
        
        // S'assurer que toutes les valeurs sont des chaînes
        $playerName = (string) $playerName;
        $age = (string) $age;
        $position = (string) $position;
        
        \Log::info("Final extracted values:", [
            "playerName" => $playerName,
            "age" => $age,
            "position" => $position
        ]);
        
        // Déterminer la prochaine question basée sur le NOUVEAU paramètre reçu
        if (!empty($newPlayerName) && empty($newAge) && empty($newPosition)) {
            \Log::info("✅ Condition 1 met: new playerName received");
            return $this->voiceResponseService->generateResponse('name_confirmed', [
                'name' => $newPlayerName
            ]);
        }
        
        if ((!empty($newAge) || isset($newParameters["age1"]) || isset($newParameters["age"])) && empty($newPosition)) {
            \Log::info("✅ Condition 2 met: new age received");
            $ageText = "";
            if (is_array($newAge) || is_object($newAge)) {
                $ageText = $newAge["amount"] ?? "l'âge";
            } else {
                $ageText = $newAge;
            }
            return $this->voiceResponseService->generateResponse('age_confirmed', [
                'age' => $ageText
            ]);
        }
        
        if (!empty($newPosition)) {
            \Log::info("✅ Condition 3 met: new position received");
            return $this->voiceResponseService->generateResponse('position_confirmed', [
                'name' => $playerName,
                'age' => $age,
                'position' => $position
            ]);
        }
        
        \Log::info("❌ No conditions met, using default response");
        return [
            'fulfillmentText' => "Parfait ! Maintenant, dites-moi la prochaine information.",
            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => ["Parfait ! Maintenant, dites-moi la prochaine information."]
                    ]
                ]
            ]
        ];
    }

    /**
     * Soumettre les données PCMA à l'API FIT depuis une session
     */
    private function submitPcmaToFitFromSession($session)
    {
        try {
            \Log::info("Submit PCMA to FIT from session:", ['session_id' => $session->id]);
            
            // Vérifier que la session a tous les paramètres requis
            $sessionData = $session->session_data ?? [];
            if (empty($sessionData['player_name']) || empty($sessionData['age1']) || empty($sessionData['position'])) {
                return [
                    'fulfillmentText' => "Désolé, il manque des informations pour soumettre le formulaire PCMA. Veuillez compléter tous les champs.",
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ["Désolé, il manque des informations pour soumettre le formulaire PCMA. Veuillez compléter tous les champs."]
                            ]
                        ]
                    ]
                ];
            }
            
            // Utiliser le service FIT pour soumettre les données
            $fitService = app(\App\Services\FitPcmaIntegrationService::class);
            $result = $fitService->submitPcmaData($session);
            
            if ($result['success']) {
                $pcmaId = $result['data']['pcma_id'] ?? 'N/A';
                
                return [
                    'fulfillmentText' => "Excellent ! Le formulaire PCMA a été soumis avec succès au système FIT. Numéro de référence : $pcmaId. Un médecin examinera ces informations et vous contactera si nécessaire.",
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ["Excellent ! Le formulaire PCMA a été soumis avec succès au système FIT. Numéro de référence : $pcmaId. Un médecin examinera ces informations et vous contactera si nécessaire."]
                            ]
                        ]
                    ]
                ];
            } else {
                return [
                    'fulfillmentText' => "Désolé, une erreur s'est produite lors de la soumission du formulaire PCMA : " . ($result['error'] ?? 'Erreur inconnue'),
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ["Désolé, une erreur s'est produite lors de la soumission du formulaire PCMA : " . ($result['error'] ?? 'Erreur inconnue')]
                            ]
                        ]
                    ]
                ];
            }
            
        } catch (\Exception $e) {
            \Log::error("Error in submitPcmaToFitFromSession:", ['error' => $e->getMessage()]);
            
            return [
                'fulfillmentText' => "Une erreur inattendue s'est produite. Veuillez réessayer plus tard.",
                'fulfillmentMessages' => [
                    [
                        'text' => [
                            'text' => ["Une erreur inattendue s'est produite. Veuillez réessayer plus tard."]
                        ]
                    ]
                ]
            ];
        }
    }

    /**
     * Soumettre les données PCMA à l'API FIT
     */
    public function submitPcmaToFit(Request $request)
    {
        try {
            $data = $request->all();
            \Log::info("Submit PCMA to FIT request:", $data);
            
            $queryResult = $data['queryResult'] ?? null;
            if (!$queryResult) {
                return response()->json(['error' => 'No queryResult found']);
            }
            
            $intent = $queryResult['intent']['displayName'] ?? 'unknown';
            $sessionId = $data['session'] ?? null;
            
            if ($intent !== 'submit_pcma') {
                return response()->json(['error' => 'Invalid intent for PCMA submission']);
            }
            
            // Récupérer la session existante
            $session = $this->getOrCreateSession($sessionId);
            
            // Vérifier que la session a tous les paramètres requis
            $sessionData = $session->session_data ?? [];
            if (empty($sessionData['player_name']) || empty($sessionData['age1']) || empty($sessionData['position'])) {
                return response()->json([
                    'fulfillmentText' => "Désolé, il manque des informations pour soumettre le formulaire PCMA. Veuillez compléter tous les champs.",
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ["Désolé, il manque des informations pour soumettre le formulaire PCMA. Veuillez compléter tous les champs."]
                            ]
                        ]
                    ]
                ]);
            }
            
            // Utiliser le service FIT pour soumettre les données
            $fitService = app(\App\Services\FitPcmaIntegrationService::class);
            $result = $fitService->submitPcmaData($session);
            
            if ($result['success']) {
                $pcmaId = $result['data']['pcma_id'] ?? 'N/A';
                
                return response()->json([
                    'fulfillmentText' => "Excellent ! Le formulaire PCMA a été soumis avec succès au système FIT. Numéro de référence : $pcmaId. Un médecin examinera ces informations et vous contactera si nécessaire.",
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ["Excellent ! Le formulaire PCMA a été soumis avec succès au système FIT. Numéro de référence : $pcmaId. Un médecin examinera ces informations et vous contactera si nécessaire."]
                            ]
                        ]
                    ]
                ]);
            } else {
                return response()->json([
                    'fulfillmentText' => "Désolé, une erreur s'est produite lors de la soumission du formulaire PCMA : " . ($result['error'] ?? 'Erreur inconnue'),
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ["Désolé, une erreur s'est produite lors de la soumission du formulaire PCMA : " . ($result['error'] ?? 'Erreur inconnue')]
                            ]
                        ]
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error("Error in submitPcmaToFit:", ['error' => $e->getMessage()]);
            
            return response()->json([
                'fulfillmentText' => "Une erreur inattendue s'est produite. Veuillez réessayer plus tard.",
                'fulfillmentMessages' => [
                    [
                        'text' => [
                            'text' => ["Une erreur inattendue s'est produite. Veuillez réessayer plus tard."]
                        ]
                    ]
                ]
            ]);
        }
    }

    /**
     * Vérifier le statut de l'API FIT
     */
    public function checkFitApiHealth()
    {
        try {
            $fitService = app(\App\Services\FitPcmaIntegrationService::class);
            $health = $fitService->checkFitApiHealth();
            
            return response()->json($health);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function fallbackToWhisper(Request $request)
    {
        try {
            return response()->json([
                "fulfillmentText" => "Je n'ai pas compris. Pouvez-vous répéter ?",
                "fulfillmentMessages" => [
                    [
                        "text" => [
                            "text" => ["Je n'ai pas compris. Pouvez-vous répéter ?"]
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "fulfillmentText" => "Erreur technique. Utilisez l'interface web.",
                "fulfillmentMessages" => [
                    [
                        "text" => [
                            "text" => ["Erreur technique. Utilisez l'interface web."]
                        ]
                    ]
                ]
            ]);
        }
    }

    /**
     * Gérer les confirmations (oui/non)
     */
    private function handleConfirmation($session, $confirmed)
    {
        \Log::info("Handling confirmation:", ['confirmed' => $confirmed, 'session_id' => $session->id]);
        
        $sessionData = $session->session_data ?? [];
        
        if ($confirmed) {
            // Si confirmation positive, procéder à la soumission
            if (!empty($sessionData['player_name']) && !empty($sessionData['age1']) && !empty($sessionData['position'])) {
                return $this->submitPcmaToFitFromSession($session);
            } else {
                return $this->voiceResponseService->generateResponse('incomplete_data', []);
            }
        } else {
            // Si confirmation négative, proposer de corriger
            return [
                'fulfillmentText' => "D'accord ! Que souhaitez-vous corriger ? Dites 'nom', 'âge' ou 'position', ou 'recommencer' pour tout reprendre.",
                'fulfillmentMessages' => [
                    [
                        'text' => [
                            'text' => ["D'accord ! Que souhaitez-vous corriger ? Dites 'nom', 'âge' ou 'position', ou 'recommencer' pour tout reprendre."]
                        ]
                    ]
                ]
            ];
        }
    }

    /**
     * Gérer le redémarrage de la session
     */
    private function handleRestart($session)
    {
        \Log::info("Restarting session:", ['session_id' => $session->id]);
        
        // Reset des données de session
        $session->update([
            'session_data' => [
                'dialogflow_session' => $session->dialogflow_session ?? null,
                'player_name' => null,
                'age1' => null,
                'position' => null
            ],
            'current_field' => 'identite',
            'error_count' => 0,
            'fallback_requested' => false
        ]);
        
        return $this->voiceResponseService->generateContextualGreeting();
    }

    /**
     * Gérer les corrections de champs
     */
    private function handleCorrection($session, $parameters)
    {
        \Log::info("Handling field correction:", ['parameters' => $parameters, 'session_id' => $session->id]);
        
        $fieldToCorrect = $parameters['field'] ?? $parameters['correction_field'] ?? null;
        $sessionData = $session->session_data ?? [];
        
        switch ($fieldToCorrect) {
            case 'nom':
            case 'name':
                $sessionData['player_name'] = null;
                $session->update(['session_data' => $sessionData]);
                return [
                    'fulfillmentText' => "D'accord ! Dites-moi le nouveau nom du joueur.",
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ["D'accord ! Dites-moi le nouveau nom du joueur."]
                            ]
                        ]
                    ]
                ];
                
            case 'age':
            case 'âge':
                $sessionData['age1'] = null;
                $session->update(['session_data' => $sessionData]);
                return [
                    'fulfillmentText' => "Parfait ! Quel est le nouvel âge du joueur ?",
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ["Parfait ! Quel est le nouvel âge du joueur ?"]
                            ]
                        ]
                    ]
                ];
                
            case 'position':
                $sessionData['position'] = null;
                $session->update(['session_data' => $sessionData]);
                return [
                    'fulfillmentText' => "Très bien ! Quelle est la nouvelle position du joueur ?",
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ["Très bien ! Quelle est la nouvelle position du joueur ?"]
                            ]
                        ]
                    ]
                ];
                
            default:
                return [
                    'fulfillmentText' => "Je peux corriger le nom, l'âge ou la position. Que voulez-vous modifier ?",
                    'fulfillmentMessages' => [
                        [
                            'text' => [
                                'text' => ["Je peux corriger le nom, l'âge ou la position. Que voulez-vous modifier ?"]
                            ]
                        ]
                    ]
                ];
        }
    }

    /**
     * Gérer le fallback vers l'interface web
     */
    private function handleFallbackToWeb($session)
    {
        \Log::info("Activating web fallback for session:", ['session_id' => $session->id]);
        
        // Marquer la session comme nécessitant un fallback
        $session->update(['fallback_requested' => true]);
        
        $webUrl = url('/pcma/voice-fallback?session=' . $session->id);
        
        return [
            'fulfillmentText' => "Je rencontre des difficultés à vous comprendre. Vous pouvez continuer sur notre interface web : $webUrl ou dire 'recommencer' pour reprendre par la voix.",
            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => ["Je rencontre des difficultés à vous comprendre. Vous pouvez continuer sur notre interface web : $webUrl ou dire 'recommencer' pour reprendre par la voix."]
                    ]
                ]
            ]
        ];
    }

    /**
     * Générer une réponse d'aide
     */
    private function generateHelpResponse($session)
    {
        $sessionData = $session->session_data ?? [];
        $currentStep = $this->getCurrentStep($sessionData);
        
        $helpMessages = [
            'name' => "Dites-moi le nom du joueur, par exemple 'Le joueur s'appelle Mohamed'",
            'age' => "Indiquez l'âge du joueur, par exemple 'Il a 25 ans'", 
            'position' => "Précisez la position du joueur : attaquant, défenseur, milieu ou gardien",
            'confirm' => "Dites 'oui' pour confirmer ou 'non' pour corriger les informations"
        ];
        
        $helpText = $helpMessages[$currentStep] ?? "Dites-moi les informations du joueur : nom, âge et position.";
        
        return [
            'fulfillmentText' => "Je vais vous aider. $helpText",
            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => ["Je vais vous aider. $helpText"]
                    ]
                ]
            ]
        ];
    }

    /**
     * Déterminer l'étape actuelle de la conversation
     */
    private function getCurrentStep($sessionData)
    {
        if (empty($sessionData['player_name'])) {
            return 'name';
        }
        if (empty($sessionData['age1'])) {
            return 'age';
        }
        if (empty($sessionData['position'])) {
            return 'position';
        }
        return 'confirm';
    }

    public function health()
    {
        return response()->json(["status" => "healthy"]);
    }

    public function test(Request $request)
    {
        return response()->json([
            "message" => "Google Assistant webhook test successful",
            "request_data" => $request->all()
        ]);
    }

    /**
     * Récupérer les données d'une session PCMA
     */
    public function getSessionData($sessionId)
    {
        try {
            $session = VoiceSession::where('dialogflow_session', $sessionId)->first();
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session non trouvée'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'session' => $session
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération de session:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la session'
            ], 500);
        }
    }
}
