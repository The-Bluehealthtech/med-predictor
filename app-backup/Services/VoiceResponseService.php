<?php

namespace App\Services;

class VoiceResponseService
{
    private $responseVariants = [
        'welcome' => [
            "Bonjour ! Je vais vous accompagner pour remplir le formulaire PCMA. Commençons par le nom du joueur.",
            "Bienvenue ! Je suis là pour vous guider dans le formulaire PCMA. Dites-moi le nom du joueur.",
            "Salut ! Remplissons ensemble le formulaire PCMA. Quel est le nom du joueur ?"
        ],
        'name_confirmed' => [
            "Parfait ! {name} est bien enregistré. Maintenant, dites-moi son âge.",
            "Très bien ! J'ai noté {name}. Quel âge a-t-il ?",
            "Excellent ! {name}, c'est noté. Pouvez-vous me dire son âge ?"
        ],
        'age_confirmed' => [
            "Parfait ! {age} ans. Maintenant, quelle est sa position sur le terrain ?",
            "Très bien ! {age} ans, c'est noté. Sa position de jeu, s'il vous plaît ?",
            "Excellent ! {age} ans. Dites-moi sa position : attaquant, défenseur, milieu ou gardien ?"
        ],
        'position_confirmed' => [
            "Parfait ! Position {position} enregistrée. Récapitulons : {name} a {age} ans et joue {position}. Voulez-vous soumettre ce formulaire ?",
            "Excellent ! {position}, c'est noté. Pour résumer : {name}, {age} ans, {position}. Dois-je envoyer ces informations ?",
            "Très bien ! Récapitulatif : {name}, {age} ans, poste {position}. Confirmez-vous l'envoi du formulaire ?"
        ],
        'success_submission' => [
            "Formidable ! Le formulaire PCMA a été envoyé avec succès. Référence : {reference}. Un médecin examinera ces informations.",
            "Excellent ! Formulaire soumis ! Votre numéro de référence est {reference}. L'équipe médicale sera informée.",
            "Parfait ! C'est fait ! Référence {reference}. Le personnel médical recevra ces informations sous peu."
        ],
        'incomplete_data' => [
            "Il manque encore des informations. Pouvons-nous les compléter ensemble ?",
            "Quelques détails sont manquants. Continuons à remplir le formulaire.",
            "Des informations sont incomplètes. Reprenons là où nous nous sommes arrêtés."
        ]
    ];

    private $ssmlTemplates = [
        'welcome' => '<speak><prosody rate="medium" pitch="medium">Bonjour ! <break time="0.5s"/> Je vais vous accompagner pour remplir le formulaire PCMA. <break time="0.3s"/> Commençons par le nom du joueur.</prosody></speak>',
        'success' => '<speak><prosody rate="medium" pitch="+2st">Formidable !</prosody> <break time="0.5s"/> Le formulaire PCMA a été envoyé avec succès. <break time="0.3s"/> Référence : <prosody rate="slow">{reference}</prosody></speak>'
    ];

    /**
     * Générer une réponse vocale variée
     */
    public function generateResponse(string $type, array $variables = [], bool $useSsml = false): array
    {
        if (!isset($this->responseVariants[$type])) {
            return $this->createSimpleResponse("Réponse non trouvée pour le type : $type");
        }

        $variants = $this->responseVariants[$type];
        $selectedVariant = $variants[array_rand($variants)];
        
        // Remplacer les variables
        foreach ($variables as $key => $value) {
            $selectedVariant = str_replace("{" . $key . "}", $value, $selectedVariant);
        }

        // Utiliser SSML si demandé et disponible
        if ($useSsml && isset($this->ssmlTemplates[$type])) {
            $ssmlResponse = $this->ssmlTemplates[$type];
            foreach ($variables as $key => $value) {
                $ssmlResponse = str_replace("{" . $key . "}", $value, $ssmlResponse);
            }
            
            return $this->createSsmlResponse($ssmlResponse, $selectedVariant);
        }

        return $this->createSimpleResponse($selectedVariant);
    }

    /**
     * Créer une réponse simple
     */
    private function createSimpleResponse(string $text): array
    {
        return [
            'fulfillmentText' => $text,
            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => [$text]
                    ]
                ]
            ]
        ];
    }

    /**
     * Créer une réponse avec SSML
     */
    private function createSsmlResponse(string $ssml, string $fallbackText): array
    {
        return [
            'fulfillmentText' => $fallbackText,
            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => [$fallbackText]
                    ]
                ],
                [
                    'payload' => [
                        'google' => [
                            'expectUserResponse' => true,
                            'richResponse' => [
                                'items' => [
                                    [
                                        'simpleResponse' => [
                                            'ssml' => $ssml
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Générer une réponse d'encouragement
     */
    public function generateEncouragementResponse(): array
    {
        $encouragements = [
            "Très bien ! Continuons.",
            "Parfait ! On avance bien.",
            "Excellent ! Poursuivons.",
            "Superbe ! Une étape de plus."
        ];

        return $this->createSimpleResponse($encouragements[array_rand($encouragements)]);
    }

    /**
     * Générer une réponse de patience
     */
    public function generatePatienceResponse(): array
    {
        $patienceMessages = [
            "Prenez votre temps, je vous écoute.",
            "Pas de souci, dites-moi quand vous êtes prêt.",
            "Je patiente, répondez à votre rythme."
        ];

        return $this->createSimpleResponse($patienceMessages[array_rand($patienceMessages)]);
    }

    /**
     * Générer une réponse personnalisée selon l'heure
     */
    public function generateContextualGreeting(): array
    {
        $hour = date('H');
        
        if ($hour < 12) {
            $greetings = [
                "Bonjour ! Belle matinée pour un formulaire PCMA !",
                "Salut ! Commençons cette matinée par votre formulaire médical.",
                "Bonjour ! Démarrons ensemble ce formulaire PCMA."
            ];
        } elseif ($hour < 18) {
            $greetings = [
                "Bonjour ! Parfait timing pour remplir le formulaire PCMA.",
                "Salut ! Occupons-nous de votre formulaire médical.",
                "Bonjour ! L'après-midi idéal pour le formulaire PCMA."
            ];
        } else {
            $greetings = [
                "Bonsoir ! Finissons cette journée avec le formulaire PCMA.",
                "Salut ! Terminons par votre formulaire médical.",
                "Bonsoir ! Occupons-nous de ce formulaire PCMA."
            ];
        }

        return $this->createSimpleResponse($greetings[array_rand($greetings)]);
    }
}
