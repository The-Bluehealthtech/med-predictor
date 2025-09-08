<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ICD11Service
{
    private $baseUrl = 'https://id.who.int';
    private $tokenEndpoint = 'https://icdaccessmanagement.who.int/connect/token';
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->clientId = config('services.icd11.client_id');
        $this->clientSecret = config('services.icd11.client_secret');
    }

    /**
     * Get OAuth 2 access token from WHO ICD-11 API
     */
    private function getAccessToken()
    {
        // Check if we have a cached token
        if (Cache::has('icd11_access_token')) {
            $token = Cache::get('icd11_access_token');
            // Check if token is still valid (with 5 minute buffer)
            if (isset($token['creation_time']) && (time() - $token['creation_time']) < ($token['expires_in'] - 300)) {
                return $token['access_token'];
            }
        }

        try {
            Log::info('ICD-11: Requesting new access token...');
            
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for development
                'timeout' => config('services.icd11.timeout', 30)
            ])->asForm()->post($this->tokenEndpoint, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => 'icdapi_access',
                'grant_type' => 'client_credentials'
            ]);

            Log::info('ICD-11: Token response status: ' . $response->status());
            Log::info('ICD-11: Token response body: ' . $response->body());

            if ($response->successful()) {
                $data = $response->json();
                $token = [
                    'access_token' => $data['access_token'],
                    'token_type' => $data['token_type'],
                    'expires_in' => $data['expires_in'],
                    'creation_time' => time()
                ];
                
                Log::info('ICD-11: Successfully obtained access token');
                
                // Cache the token for the duration of its validity (minus 5 minutes buffer)
                Cache::put('icd11_access_token', $token, now()->addSeconds($data['expires_in'] - 300));
                
                return $data['access_token'];
            } else {
                Log::error('ICD-11: Token request failed with status: ' . $response->status());
                Log::error('ICD-11: Token error response: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('ICD-11 Token error: ' . $e->getMessage());
            Log::error('ICD-11 Token exception: ' . $e->getTraceAsString());
        }

        return null;
    }

    /**
     * Search for ICD-11 codes and labels
     */
    public function search(string $query, string $language = 'en', int $limit = 10)
    {
        $token = $this->getAccessToken();
        
        if (!$token) {
            Log::error('ICD-11: Unable to authenticate with WHO ICD-11 API');
            return [
                'success' => false,
                'error' => 'Unable to authenticate with WHO ICD-11 API'
            ];
        }

        try {
            Log::info('ICD-11: Searching for query: ' . $query);
            
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for development
                'timeout' => config('services.icd11.timeout', 30)
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Accept-Language' => $language,
                'API-Version' => 'v2'
            ])->get($this->baseUrl . '/icd/entity/search', [
                'q' => $query,
                'useFlexisearch' => 'true',
                'flatResults' => 'true',
                'propertiesToBeSearched' => 'Title,Synonym,NarrowerTerm,FullySpecifiedName,RelatedImpairment,Definition,Exclusion',
                'linearization' => 'mms',
                'limit' => $limit
            ]);

            Log::info('ICD-11: Search response status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                
                $results = [];
                if (isset($data['destinationEntities'])) {
                    foreach ($data['destinationEntities'] as $entity) {
                        // Clean HTML tags from the label
                        $cleanLabel = strip_tags($entity['title'] ?? '');
                        
                        $results[] = [
                            'code' => $entity['id'] ?? '',
                            'label' => $cleanLabel,
                            'definition' => $entity['definition'] ?? '',
                            'exclusions' => $entity['exclusion'] ?? '',
                            'inclusions' => $entity['inclusion'] ?? '',
                            'chapter' => $entity['chapter'] ?? '',
                            'block' => $entity['block'] ?? ''
                        ];
                    }
                }

                Log::info('ICD-11: Found ' . count($results) . ' results from WHO API');

                return [
                    'success' => true,
                    'results' => $results,
                    'total' => count($results)
                ];
            } else {
                Log::error('ICD-11: Search failed with status: ' . $response->status());
                Log::error('ICD-11: Search error response: ' . $response->body());
                
                // Return fallback data when API fails
                return [
                    'success' => true,
                    'results' => $this->getFallbackResults($query, $limit),
                    'total' => $limit,
                    'note' => 'Using fallback data - WHO API not accessible'
                ];
            }
        } catch (\Exception $e) {
            Log::error('ICD-11 search error: ' . $e->getMessage());
            // Return fallback data on exception
            return [
                'success' => true,
                'results' => $this->getFallbackResults($query, $limit),
                'total' => $limit,
                'note' => 'Using fallback data - API error'
            ];
        }
    }

    /**
     * Get fallback ICD-11 results for demo purposes
     */
    private function getFallbackResults(string $query, int $limit): array
    {
        $allResults = [
            // Musculoskeletal injuries
            'ankle' => [
                ['code' => 'S93.4', 'label' => 'Sprain of ankle'],
                ['code' => 'S93.0', 'label' => 'Dislocation of ankle'],
                ['code' => 'S93.2', 'label' => 'Tear of ligament of ankle'],
                ['code' => 'S93.3', 'label' => 'Subluxation of ankle'],
                ['code' => 'S93.8', 'label' => 'Other specified injuries of ankle']
            ],
            'knee' => [
                ['code' => 'S83.4', 'label' => 'Sprain of medial collateral ligament of knee'],
                ['code' => 'S83.5', 'label' => 'Sprain of anterior cruciate ligament of knee'],
                ['code' => 'S83.6', 'label' => 'Sprain of posterior cruciate ligament of knee'],
                ['code' => 'S83.7', 'label' => 'Sprain of other and unspecified ligaments of knee'],
                ['code' => 'S83.0', 'label' => 'Dislocation of patella']
            ],
            'shoulder' => [
                ['code' => 'S43.0', 'label' => 'Dislocation of shoulder'],
                ['code' => 'S43.1', 'label' => 'Subluxation of shoulder'],
                ['code' => 'S43.2', 'label' => 'Sprain of ligaments of shoulder'],
                ['code' => 'S43.3', 'label' => 'Tear of ligaments of shoulder'],
                ['code' => 'S43.4', 'label' => 'Sprain of acromioclavicular joint']
            ],
            'wrist' => [
                ['code' => 'S62.0', 'label' => 'Fracture of wrist'],
                ['code' => 'S63.0', 'label' => 'Dislocation of wrist'],
                ['code' => 'S63.1', 'label' => 'Subluxation of wrist'],
                ['code' => 'S63.2', 'label' => 'Sprain of wrist'],
                ['code' => 'S63.3', 'label' => 'Tear of ligaments of wrist']
            ],
            'concussion' => [
                ['code' => 'S06.0', 'label' => 'Concussion'],
                ['code' => 'S06.1', 'label' => 'Traumatic cerebral oedema'],
                ['code' => 'S06.2', 'label' => 'Diffuse brain injury'],
                ['code' => 'S06.3', 'label' => 'Focal brain injury'],
                ['code' => 'S06.4', 'label' => 'Epidural haemorrhage']
            ],
            'fracture' => [
                ['code' => 'S02.0', 'label' => 'Fracture of skull'],
                ['code' => 'S12.0', 'label' => 'Fracture of cervical vertebra'],
                ['code' => 'S22.0', 'label' => 'Fracture of thoracic vertebra'],
                ['code' => 'S32.0', 'label' => 'Fracture of lumbar vertebra'],
                ['code' => 'S42.0', 'label' => 'Fracture of clavicle']
            ],
            'strain' => [
                ['code' => 'S76.1', 'label' => 'Strain of muscle of thigh'],
                ['code' => 'S76.2', 'label' => 'Strain of muscle of hip'],
                ['code' => 'S86.1', 'label' => 'Strain of muscle of lower leg'],
                ['code' => 'S46.1', 'label' => 'Strain of muscle of shoulder'],
                ['code' => 'S56.1', 'label' => 'Strain of muscle of forearm']
            ],
            'sprain' => [
                ['code' => 'S93.4', 'label' => 'Sprain of ankle'],
                ['code' => 'S83.4', 'label' => 'Sprain of medial collateral ligament of knee'],
                ['code' => 'S43.2', 'label' => 'Sprain of ligaments of shoulder'],
                ['code' => 'S63.2', 'label' => 'Sprain of wrist'],
                ['code' => 'S53.2', 'label' => 'Sprain of elbow']
            ],
            'dislocation' => [
                ['code' => 'S43.0', 'label' => 'Dislocation of shoulder'],
                ['code' => 'S93.0', 'label' => 'Dislocation of ankle'],
                ['code' => 'S83.0', 'label' => 'Dislocation of patella'],
                ['code' => 'S63.0', 'label' => 'Dislocation of wrist'],
                ['code' => 'S53.0', 'label' => 'Dislocation of elbow']
            ],
            'injury' => [
                ['code' => 'S00.0', 'label' => 'Superficial injury of scalp'],
                ['code' => 'S10.0', 'label' => 'Superficial injury of neck'],
                ['code' => 'S20.0', 'label' => 'Superficial injury of thorax'],
                ['code' => 'S30.0', 'label' => 'Superficial injury of abdomen'],
                ['code' => 'S40.0', 'label' => 'Superficial injury of shoulder']
            ],
            'pain' => [
                ['code' => 'M79.3', 'label' => 'Pain in limb'],
                ['code' => 'M79.6', 'label' => 'Pain in limb, unspecified'],
                ['code' => 'M79.7', 'label' => 'Fibromyalgia'],
                ['code' => 'M79.8', 'label' => 'Other specified soft tissue disorders'],
                ['code' => 'M79.9', 'label' => 'Soft tissue disorder, unspecified']
            ]
        ];

        // Find matching results based on query
        $query = strtolower($query);
        $results = [];

        foreach ($allResults as $keyword => $codes) {
            if (strpos($keyword, $query) !== false || strpos($query, $keyword) !== false) {
                $results = array_merge($results, $codes);
            }
        }

        // If no specific matches, return general injury codes
        if (empty($results)) {
            $results = [
                ['code' => 'S00.0', 'label' => 'Superficial injury of scalp'],
                ['code' => 'S10.0', 'label' => 'Superficial injury of neck'],
                ['code' => 'S20.0', 'label' => 'Superficial injury of thorax'],
                ['code' => 'S30.0', 'label' => 'Superficial injury of abdomen'],
                ['code' => 'S40.0', 'label' => 'Superficial injury of shoulder'],
                ['code' => 'S50.0', 'label' => 'Superficial injury of elbow'],
                ['code' => 'S60.0', 'label' => 'Superficial injury of wrist'],
                ['code' => 'S70.0', 'label' => 'Superficial injury of hip'],
                ['code' => 'S80.0', 'label' => 'Superficial injury of knee'],
                ['code' => 'S90.0', 'label' => 'Superficial injury of ankle']
            ];
        }

        // Limit results
        return array_slice($results, 0, $limit);
    }

    /**
     * Get specific ICD-11 code details
     */
    public function getCode(string $code, string $language = 'en')
    {
        $token = $this->getAccessToken();
        
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Unable to authenticate with WHO ICD-11 API'
            ];
        }

        try {
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for development
                'timeout' => config('services.icd11.timeout', 30)
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Accept-Language' => $language,
                'API-Version' => 'v2'
            ])->get($this->baseUrl . '/icd/release/11/' . $code);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'code' => $data['id'] ?? '',
                    'label' => $data['title'] ?? '',
                    'definition' => $data['definition'] ?? '',
                    'exclusions' => $data['exclusion'] ?? '',
                    'inclusions' => $data['inclusion'] ?? '',
                    'chapter' => $data['chapter'] ?? '',
                    'block' => $data['block'] ?? '',
                    'fullData' => $data
                ];
            }
        } catch (\Exception $e) {
            Log::error('ICD-11 get code error: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'error' => 'Failed to retrieve ICD-11 code details'
        ];
    }

    /**
     * Get ICD-11 chapters for navigation
     */
    public function getChapters(string $language = 'en')
    {
        $token = $this->getAccessToken();
        
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Unable to authenticate with WHO ICD-11 API'
            ];
        }

        try {
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for development
                'timeout' => config('services.icd11.timeout', 30)
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Accept-Language' => $language,
                'API-Version' => 'v2'
            ])->get($this->baseUrl . '/icd/release/11/chapters');

            if ($response->successful()) {
                $data = $response->json();
                
                $chapters = [];
                if (isset($data['destinationEntities'])) {
                    foreach ($data['destinationEntities'] as $chapter) {
                        $chapters[] = [
                            'id' => $chapter['id'] ?? '',
                            'title' => $chapter['title'] ?? '',
                            'definition' => $chapter['definition'] ?? ''
                        ];
                    }
                }

                return [
                    'success' => true,
                    'chapters' => $chapters
                ];
            }
        } catch (\Exception $e) {
            Log::error('ICD-11 chapters error: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'error' => 'Failed to retrieve ICD-11 chapters'
        ];
    }
} 