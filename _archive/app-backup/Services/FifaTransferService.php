<?php

namespace App\Services;

use App\Models\Transfer;
use App\Models\Player;
use App\Models\Club;
use App\Models\Federation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class FifaTransferService
{
    private string $baseUrl;
    private string $apiKey;
    private string $apiSecret;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('fifa.api_url', 'https://api.fifa.com');
        $this->apiKey = config('fifa.api_key', '');
        $this->apiSecret = config('fifa.api_secret', '');
        $this->timeout = config('fifa.timeout', 30);
    }

    /**
     * Créer un transfert dans FIFA ITMS/DTMS
     */
    public function createTransfer(Transfer $transfer): array
    {
        try {
            $payload = $this->buildTransferPayload($transfer);
            
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post($this->baseUrl . '/v1/transfers', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Mettre à jour le transfert avec l'ID FIFA
                $transfer->update([
                    'fifa_transfer_id' => $data['transfer_id'] ?? null,
                    'fifa_payload' => $payload,
                    'fifa_response' => $data,
                    'transfer_status' => 'submitted',
                ]);

                Log::info('Transfer created in FIFA', [
                    'transfer_id' => $transfer->id,
                    'fifa_transfer_id' => $data['transfer_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'fifa_transfer_id' => $data['transfer_id'] ?? null,
                    'data' => $data,
                ];
            } else {
                $error = $response->json();
                
                $transfer->update([
                    'fifa_payload' => $payload,
                    'fifa_response' => $error,
                    'fifa_error_message' => $error['message'] ?? 'Unknown error',
                    'transfer_status' => 'rejected',
                ]);

                Log::error('Failed to create transfer in FIFA', [
                    'transfer_id' => $transfer->id,
                    'error' => $error,
                ]);

                return [
                    'success' => false,
                    'error' => $error['message'] ?? 'Unknown error',
                    'data' => $error,
                ];
            }
        } catch (Exception $e) {
            Log::error('Exception creating transfer in FIFA', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Demander un ITC (International Transfer Certificate)
     */
    public function requestItc(Transfer $transfer): array
    {
        if (!$transfer->isItcRequired()) {
            return [
                'success' => false,
                'error' => 'ITC not required for this transfer',
            ];
        }

        try {
            $payload = [
                'transfer_id' => $transfer->fifa_transfer_id,
                'player_id' => $transfer->player->fifa_player_id,
                'origin_federation' => $transfer->federationOrigin->fifa_code,
                'destination_federation' => $transfer->federationDestination->fifa_code,
                'request_date' => now()->format('Y-m-d'),
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post($this->baseUrl . '/v1/itc/request', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                $transfer->update([
                    'itc_status' => 'requested',
                    'itc_request_date' => now(),
                    'fifa_itc_id' => $data['itc_id'] ?? null,
                ]);

                Log::info('ITC requested from FIFA', [
                    'transfer_id' => $transfer->id,
                    'itc_id' => $data['itc_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'itc_id' => $data['itc_id'] ?? null,
                    'data' => $data,
                ];
            } else {
                $error = $response->json();
                
                Log::error('Failed to request ITC from FIFA', [
                    'transfer_id' => $transfer->id,
                    'error' => $error,
                ]);

                return [
                    'success' => false,
                    'error' => $error['message'] ?? 'Unknown error',
                    'data' => $error,
                ];
            }
        } catch (Exception $e) {
            Log::error('Exception requesting ITC from FIFA', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifier le statut d'un ITC
     */
    public function checkItcStatus(Transfer $transfer): array
    {
        if (!$transfer->fifa_itc_id) {
            return [
                'success' => false,
                'error' => 'No FIFA ITC ID available',
            ];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->get($this->baseUrl . '/v1/itc/' . $transfer->fifa_itc_id);

            if ($response->successful()) {
                $data = $response->json();
                
                $transfer->update([
                    'itc_status' => $data['status'] ?? $transfer->itc_status,
                    'itc_response_date' => $data['response_date'] ? now() : $transfer->itc_response_date,
                    'fifa_response' => $data,
                ]);

                return [
                    'success' => true,
                    'status' => $data['status'] ?? null,
                    'data' => $data,
                ];
            } else {
                $error = $response->json();
                
                Log::error('Failed to check ITC status from FIFA', [
                    'transfer_id' => $transfer->id,
                    'error' => $error,
                ]);

                return [
                    'success' => false,
                    'error' => $error['message'] ?? 'Unknown error',
                    'data' => $error,
                ];
            }
        } catch (Exception $e) {
            Log::error('Exception checking ITC status from FIFA', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mettre à jour un transfert dans FIFA
     */
    public function updateTransfer(Transfer $transfer): array
    {
        if (!$transfer->fifa_transfer_id) {
            return [
                'success' => false,
                'error' => 'No FIFA transfer ID available',
            ];
        }

        try {
            $payload = $this->buildTransferPayload($transfer);
            
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->put($this->baseUrl . '/v1/transfers/' . $transfer->fifa_transfer_id, $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                $transfer->update([
                    'fifa_payload' => $payload,
                    'fifa_response' => $data,
                ]);

                Log::info('Transfer updated in FIFA', [
                    'transfer_id' => $transfer->id,
                    'fifa_transfer_id' => $transfer->fifa_transfer_id,
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                ];
            } else {
                $error = $response->json();
                
                $transfer->update([
                    'fifa_payload' => $payload,
                    'fifa_response' => $error,
                    'fifa_error_message' => $error['message'] ?? 'Unknown error',
                ]);

                Log::error('Failed to update transfer in FIFA', [
                    'transfer_id' => $transfer->id,
                    'error' => $error,
                ]);

                return [
                    'success' => false,
                    'error' => $error['message'] ?? 'Unknown error',
                    'data' => $error,
                ];
            }
        } catch (Exception $e) {
            Log::error('Exception updating transfer in FIFA', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Construire le payload pour FIFA
     */
    private function buildTransferPayload(Transfer $transfer): array
    {
        return [
            'transfer' => [
                'id' => $transfer->id,
                'type' => $transfer->transfer_type,
                'date' => $transfer->transfer_date->format('Y-m-d'),
                'is_international' => $transfer->is_international,
                'is_minor' => $transfer->is_minor_transfer,
                'fee' => $transfer->transfer_fee,
                'currency' => $transfer->currency,
            ],
            'player' => [
                'fifa_id' => $transfer->player->fifa_player_id,
                'name' => $transfer->player->name,
                'nationality' => $transfer->player->nationality,
                'birth_date' => $transfer->player->date_of_birth?->format('Y-m-d'),
                'position' => $transfer->player->position,
            ],
            'clubs' => [
                'origin' => [
                    'fifa_id' => $transfer->clubOrigin->fifa_club_id,
                    'name' => $transfer->clubOrigin->name,
                    'country' => $transfer->clubOrigin->country,
                    'federation' => $transfer->federationOrigin?->fifa_code,
                ],
                'destination' => [
                    'fifa_id' => $transfer->clubDestination->fifa_club_id,
                    'name' => $transfer->clubDestination->name,
                    'country' => $transfer->clubDestination->country,
                    'federation' => $transfer->federationDestination?->fifa_code,
                ],
            ],
            'contract' => [
                'start_date' => $transfer->contract_start_date->format('Y-m-d'),
                'end_date' => $transfer->contract_end_date?->format('Y-m-d'),
                'type' => $transfer->contract?->contract_type,
            ],
            'documents' => $transfer->documents()
                ->where('validation_status', 'approved')
                ->get()
                ->map(fn($doc) => [
                    'type' => $doc->document_type,
                    'name' => $doc->document_name,
                    'fifa_id' => $doc->fifa_document_id,
                ])
                ->toArray(),
        ];
    }

    /**
     * Obtenir les headers d'authentification
     */
    private function getHeaders(): array
    {
        $token = $this->getAccessToken();
        
        return [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Obtenir le token d'accès FIFA
     */
    private function getAccessToken(): string
    {
        $cacheKey = 'fifa_access_token';
        
        // Vérifier si on a déjà un token en cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/v1/oauth/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->apiKey,
                    'client_secret' => $this->apiSecret,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                
                if ($token) {
                    // Mettre en cache le token pour 1 heure
                    Cache::put($cacheKey, $token, now()->addHour());
                    return $token;
                }
            }

            Log::error('Failed to get FIFA access token', [
                'response' => $response->json(),
            ]);

            throw new Exception('Failed to get FIFA access token');
        } catch (Exception $e) {
            Log::error('Exception getting FIFA access token', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Traiter les webhooks FIFA
     */
    public function processWebhook(array $data): array
    {
        try {
            $transferId = $data['transfer_id'] ?? null;
            $eventType = $data['event_type'] ?? null;
            
            if (!$transferId || !$eventType) {
                return [
                    'success' => false,
                    'error' => 'Missing required fields',
                ];
            }

            $transfer = Transfer::where('fifa_transfer_id', $transferId)->first();
            
            if (!$transfer) {
                return [
                    'success' => false,
                    'error' => 'Transfer not found',
                ];
            }

            switch ($eventType) {
                case 'transfer_approved':
                    $transfer->update([
                        'transfer_status' => 'approved',
                        'fifa_response' => $data,
                    ]);
                    break;
                    
                case 'transfer_rejected':
                    $transfer->update([
                        'transfer_status' => 'rejected',
                        'fifa_error_message' => $data['reason'] ?? 'Transfer rejected by FIFA',
                        'fifa_response' => $data,
                    ]);
                    break;
                    
                case 'itc_approved':
                    $transfer->update([
                        'itc_status' => 'approved',
                        'itc_response_date' => now(),
                        'fifa_response' => $data,
                    ]);
                    break;
                    
                case 'itc_rejected':
                    $transfer->update([
                        'itc_status' => 'rejected',
                        'itc_response_date' => now(),
                        'fifa_error_message' => $data['reason'] ?? 'ITC rejected by FIFA',
                        'fifa_response' => $data,
                    ]);
                    break;
                    
                default:
                    Log::warning('Unknown FIFA webhook event type', [
                        'event_type' => $eventType,
                        'transfer_id' => $transferId,
                    ]);
            }

            Log::info('FIFA webhook processed', [
                'event_type' => $eventType,
                'transfer_id' => $transferId,
            ]);

            return [
                'success' => true,
                'transfer_id' => $transfer->id,
                'event_type' => $eventType,
            ];
        } catch (Exception $e) {
            Log::error('Exception processing FIFA webhook', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
} 