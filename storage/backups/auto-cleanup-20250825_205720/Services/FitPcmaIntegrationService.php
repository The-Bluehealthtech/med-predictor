<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\VoiceSession;
use Exception;

class FitPcmaIntegrationService
{
    protected $baseUrl;
    protected $apiKey;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('fit.api_base_url', 'http://localhost:8000/api');
        $this->apiKey = config('fit.api_key');
        $this->timeout = config('fit.timeout', 30);
    }

    /**
     * Soumettre les données PCMA collectées par voix à l'API FIT
     */
    public function submitPcmaData(VoiceSession $session): array
    {
        try {
            Log::info("Submitting PCMA data to FIT API", [
                'session_id' => $session->id,
                'player_name' => $session->player_name
            ]);

            // Extraire les données de la session
            $pcmaData = $this->extractPcmaDataFromSession($session);
            
            // Valider les données avant soumission
            $validationResult = $this->validatePcmaData($pcmaData);
            if (!$validationResult['valid']) {
                return [
                    'success' => false,
                    'error' => 'Données PCMA invalides',
                    'details' => $validationResult['errors']
                ];
            }

            // Préparer la requête pour l'API FIT
            $requestData = $this->prepareFitApiRequest($pcmaData);
            
            // Appeler l'API FIT
            $response = $this->callFitApi('/pcma/submit', $requestData);
            
            if ($response['success']) {
                // Mettre à jour la session avec le statut de succès
                $this->updateSessionStatus($session, 'completed', $response['data']);
                
                Log::info("PCMA data submitted successfully", [
                    'session_id' => $session->id,
                    'fit_pcma_id' => $response['data']['pcma_id'] ?? null
                ]);
            }

            return $response;

        } catch (Exception $e) {
            Log::error("Error submitting PCMA data to FIT API", [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Mettre à jour la session avec le statut d'erreur
            $this->updateSessionStatus($session, 'error', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Erreur lors de la soumission PCMA',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Extraire les données PCMA de la session vocale
     */
    protected function extractPcmaDataFromSession(VoiceSession $session): array
    {
        $sessionData = $session->session_data ?? [];
        
        return [
            'player_name' => $sessionData['player_name'] ?? $session->player_name,
            'age' => $this->extractAge($sessionData['age1'] ?? null),
            'position' => $sessionData['position'] ?? null,
            'person' => $sessionData['person'] ?? null,
            'session_id' => $session->id,
            'language' => $session->language,
            'created_at' => $session->created_at,
            'completed_at' => now()
        ];
    }

    /**
     * Extraire l'âge depuis les données Dialogflow
     */
    protected function extractAge($ageData): ?int
    {
        if (is_array($ageData) && isset($ageData['amount'])) {
            return (int) $ageData['amount'];
        }
        
        if (is_numeric($ageData)) {
            return (int) $ageData;
        }
        
        return null;
    }

    /**
     * Valider les données PCMA avant soumission
     */
    protected function validatePcmaData(array $data): array
    {
        $errors = [];
        
        // Validation du nom du joueur
        if (empty($data['player_name']) || $data['player_name'] === 'Joueur') {
            $errors[] = 'Nom du joueur manquant ou invalide';
        }
        
        // Validation de l'âge
        if (empty($data['age']) || $data['age'] < 1 || $data['age'] > 100) {
            $errors[] = 'Âge manquant ou invalide (doit être entre 1 et 100 ans)';
        }
        
        // Validation de la position
        if (empty($data['position'])) {
            $errors[] = 'Position du joueur manquante';
        }
        
        // Validation des positions valides
        $validPositions = ['attaquant', 'défenseur', 'milieu', 'gardien', 'striker', 'defender', 'midfielder', 'goalkeeper'];
        if (!empty($data['position']) && !in_array(strtolower($data['position']), $validPositions)) {
            $errors[] = 'Position invalide';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Préparer la requête pour l'API FIT
     */
    protected function prepareFitApiRequest(array $pcmaData): array
    {
        return [
            'player_identity' => [
                'name' => $pcmaData['player_name'],
                'age' => $pcmaData['age'],
                'position' => $pcmaData['position']
            ],
            'voice_session' => [
                'session_id' => $pcmaData['session_id'],
                'language' => $pcmaData['language'],
                'completed_at' => $pcmaData['completed_at']
            ],
            'source' => 'google_assistant_voice',
            'status' => 'pending_medical_review'
        ];
    }

    /**
     * Appeler l'API FIT
     */
    protected function callFitApi(string $endpoint, array $data): array
    {
        try {
            $url = $this->baseUrl . $endpoint;
            
            Log::info("Calling FIT API", [
                'url' => $url,
                'data' => $data
            ]);
            
            // SIMULATION POUR LES TESTS - Commenter cette ligne pour utiliser la vraie API
            if (config('app.env') === 'local' || $this->baseUrl === 'http://localhost:8000') {
                Log::info("Simulating FIT API response for testing");
                
                // Simuler un délai de 1 seconde
                sleep(1);
                
                return [
                    'success' => true,
                    'data' => [
                        'pcma_id' => 'PCMA-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                        'status' => 'pending_medical_review',
                        'message' => 'Formulaire PCMA soumis avec succès',
                        'submitted_at' => now()->toISOString(),
                        'player_identity' => $data['player_identity'] ?? [],
                        'voice_session' => $data['voice_session'] ?? []
                    ],
                    'status_code' => 201
                ];
            }
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $data);
            
            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info("FIT API response successful", [
                    'status' => $response->status(),
                    'data' => $responseData
                ]);
                
                return [
                    'success' => true,
                    'data' => $responseData,
                    'status_code' => $response->status()
                ];
            } else {
                Log::warning("FIT API response error", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Erreur API FIT',
                    'status_code' => $response->status(),
                    'details' => $response->body()
                ];
            }
            
        } catch (Exception $e) {
            Log::error("Exception calling FIT API", [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Exception lors de l\'appel API FIT',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Mettre à jour le statut de la session
     */
    protected function updateSessionStatus(VoiceSession $session, string $status, array $additionalData = []): void
    {
        try {
            $updateData = [
                'status' => $status,
                'session_data' => array_merge($session->session_data ?? [], $additionalData)
            ];
            
            if ($status === 'completed') {
                $updateData['completed_at'] = now();
            }
            
            $session->update($updateData);
            
            Log::info("Session status updated", [
                'session_id' => $session->id,
                'status' => $status
            ]);
            
        } catch (Exception $e) {
            Log::error("Error updating session status", [
                'session_id' => $session->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Récupérer les données PCMA depuis l'API FIT
     */
    public function getPcmaData(string $pcmaId): array
    {
        try {
            $response = $this->callFitApi("/pcma/{$pcmaId}", []);
            
            if ($response['success']) {
                return [
                    'success' => true,
                    'data' => $response['data']
                ];
            }
            
            return $response;
            
        } catch (Exception $e) {
            Log::error("Error retrieving PCMA data", [
                'pcma_id' => $pcmaId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Erreur lors de la récupération des données PCMA',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Vérifier le statut de l'API FIT
     */
    public function checkFitApiHealth(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json'
                ])
                ->get($this->baseUrl . '/health');
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => 'healthy',
                    'response_time' => $response->handlerStats()['total_time'] ?? null
                ];
            }
            
            return [
                'success' => false,
                'status' => 'unhealthy',
                'status_code' => $response->status()
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'status' => 'unreachable',
                'error' => $e->getMessage()
            ];
        }
    }
}
