<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FinanceIntegrationService
{
    /**
     * Configuration des APIs des logiciels comptables
     */
    private $apiConfigs = [
        'sage' => [
            'base_url' => 'https://api.sage.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'transactions' => '/transactions',
                'accounts' => '/accounts',
                'contacts' => '/contacts',
                'reports' => '/reports'
            ]
        ],
        'quickbooks' => [
            'base_url' => 'https://sandbox-quickbooks.api.intuit.com/v3/company',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'reports' => '/reports',
                'items' => '/items'
            ]
        ],
        'xero' => [
            'base_url' => 'https://api.xero.com/api.xro/2.0',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'contacts' => '/Contacts',
                'accounts' => '/Accounts',
                'transactions' => '/Transactions',
                'reports' => '/Reports'
            ]
        ],
        'ciel' => [
            'base_url' => 'https://api.ciel.com/v1',
            'auth_type' => 'api_key',
            'endpoints' => [
                'ecritures' => '/ecritures',
                'comptes' => '/comptes',
                'tiers' => '/tiers',
                'rapports' => '/rapports'
            ]
        ]
    ];

    /**
     * Synchronise les données avec un logiciel externe
     */
    public function syncData($software, $type, $data = [])
    {
        try {
            $config = $this->apiConfigs[$software] ?? null;
            if (!$config) {
                throw new \Exception("Configuration non trouvée pour {$software}");
            }

            // Vérifier l'authentification
            $token = $this->getAccessToken($software);
            if (!$token) {
                throw new \Exception("Token d'accès non disponible pour {$software}");
            }

            // Effectuer la synchronisation selon le type
            switch ($type) {
                case 'import':
                    return $this->importData($software, $config, $token, $data);
                case 'export':
                    return $this->exportData($software, $config, $token, $data);
                default:
                    throw new \Exception("Type de synchronisation non supporté: {$type}");
            }

        } catch (\Exception $e) {
            Log::error("Erreur de synchronisation avec {$software}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Importe les données depuis un logiciel externe
     */
    private function importData($software, $config, $token, $data)
    {
        $endpoint = $config['endpoints']['transactions'] ?? $config['endpoints']['accounts'];
        $url = $config['base_url'] . $endpoint;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($url, $data);

        if ($response->successful()) {
            $importedData = $response->json();
            
            // Traiter et formater les données importées
            $processedData = $this->processImportedData($software, $importedData);
            
            Log::info("Import réussi depuis {$software}: " . count($processedData) . " éléments");
            
            return [
                'success' => true,
                'software' => $software,
                'type' => 'import',
                'items_count' => count($processedData),
                'data' => $processedData,
                'timestamp' => now()->toISOString()
            ];
        } else {
            throw new \Exception("Erreur API {$software}: " . $response->body());
        }
    }

    /**
     * Exporte les données vers un logiciel externe
     */
    private function exportData($software, $config, $token, $data)
    {
        $endpoint = $config['endpoints']['transactions'] ?? $config['endpoints']['accounts'];
        $url = $config['base_url'] . $endpoint;

        // Formater les données pour l'export
        $formattedData = $this->formatDataForExport($software, $data);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post($url, $formattedData);

        if ($response->successful()) {
            $exportResult = $response->json();
            
            Log::info("Export réussi vers {$software}: " . count($formattedData) . " éléments");
            
            return [
                'success' => true,
                'software' => $software,
                'type' => 'export',
                'items_count' => count($formattedData),
                'result' => $exportResult,
                'timestamp' => now()->toISOString()
            ];
        } else {
            throw new \Exception("Erreur API {$software}: " . $response->body());
        }
    }

    /**
     * Récupère le token d'accès pour un logiciel
     */
    private function getAccessToken($software)
    {
        // En production, récupérer depuis la base de données ou un service de gestion des tokens
        $tokens = Cache::get('finance_tokens', []);
        
        return $tokens[$software] ?? null;
    }

    /**
     * Traite les données importées selon le logiciel
     */
    private function processImportedData($software, $rawData)
    {
        switch ($software) {
            case 'sage':
                return $this->processSageData($rawData);
            case 'quickbooks':
                return $this->processQuickBooksData($rawData);
            case 'xero':
                return $this->processXeroData($rawData);
            case 'ciel':
                return $this->processCielData($rawData);
            default:
                return $rawData;
        }
    }

    /**
     * Traite les données Sage
     */
    private function processSageData($data)
    {
        $processed = [];
        foreach ($data['transactions'] ?? [] as $transaction) {
            $processed[] = [
                'id' => $transaction['id'],
                'type' => $transaction['amount'] > 0 ? 'revenue' : 'expense',
                'amount' => abs($transaction['amount']),
                'description' => $transaction['description'],
                'category' => $transaction['category'],
                'date' => $transaction['date'],
                'status' => 'completed',
                'external_id' => $transaction['id'],
                'software' => 'sage'
            ];
        }
        return $processed;
    }

    /**
     * Traite les données QuickBooks
     */
    private function processQuickBooksData($data)
    {
        $processed = [];
        foreach ($data['QueryResponse']['Account'] ?? [] as $account) {
            $processed[] = [
                'id' => $account['Id'],
                'name' => $account['Name'],
                'type' => $account['AccountType'],
                'balance' => $account['CurrentBalance'] ?? 0,
                'external_id' => $account['Id'],
                'software' => 'quickbooks'
            ];
        }
        return $processed;
    }

    /**
     * Traite les données Xero
     */
    private function processXeroData($data)
    {
        $processed = [];
        foreach ($data['Contacts'] ?? [] as $contact) {
            $processed[] = [
                'id' => $contact['ContactID'],
                'name' => $contact['Name'],
                'email' => $contact['EmailAddress'] ?? '',
                'phone' => $contact['Phones'][0]['PhoneNumber'] ?? '',
                'external_id' => $contact['ContactID'],
                'software' => 'xero'
            ];
        }
        return $processed;
    }

    /**
     * Traite les données Ciel
     */
    private function processCielData($data)
    {
        $processed = [];
        foreach ($data['ecritures'] ?? [] as $ecriture) {
            $processed[] = [
                'id' => $ecriture['id'],
                'type' => $ecriture['sens'] === 'D' ? 'expense' : 'revenue',
                'amount' => $ecriture['montant'],
                'description' => $ecriture['libelle'],
                'date' => $ecriture['date'],
                'compte' => $ecriture['compte'],
                'external_id' => $ecriture['id'],
                'software' => 'ciel'
            ];
        }
        return $processed;
    }

    /**
     * Formate les données pour l'export selon le logiciel
     */
    private function formatDataForExport($software, $data)
    {
        switch ($software) {
            case 'sage':
                return $this->formatForSage($data);
            case 'quickbooks':
                return $this->formatForQuickBooks($data);
            case 'xero':
                return $this->formatForXero($data);
            case 'ciel':
                return $this->formatForCiel($data);
            default:
                return $data;
        }
    }

    /**
     * Formate les données pour Sage
     */
    private function formatForSage($data)
    {
        return [
            'transactions' => array_map(function($item) {
                return [
                    'amount' => $item['amount'],
                    'description' => $item['description'],
                    'category' => $item['category'],
                    'date' => $item['date']
                ];
            }, $data)
        ];
    }

    /**
     * Formate les données pour QuickBooks
     */
    private function formatForQuickBooks($data)
    {
        return [
            'Account' => array_map(function($item) {
                return [
                    'Name' => $item['name'],
                    'AccountType' => $item['type'],
                    'CurrentBalance' => $item['balance']
                ];
            }, $data)
        ];
    }

    /**
     * Formate les données pour Xero
     */
    private function formatForXero($data)
    {
        return [
            'Contacts' => array_map(function($item) {
                return [
                    'Name' => $item['name'],
                    'EmailAddress' => $item['email'],
                    'Phones' => [
                        ['PhoneNumber' => $item['phone']]
                    ]
                ];
            }, $data)
        ];
    }

    /**
     * Formate les données pour Ciel
     */
    private function formatForCiel($data)
    {
        return [
            'ecritures' => array_map(function($item) {
                return [
                    'montant' => $item['amount'],
                    'libelle' => $item['description'],
                    'date' => $item['date'],
                    'compte' => $item['account'],
                    'sens' => $item['type'] === 'expense' ? 'D' : 'C'
                ];
            }, $data)
        ];
    }

    /**
     * Teste la connexion avec un logiciel
     */
    public function testConnection($software)
    {
        try {
            $config = $this->apiConfigs[$software] ?? null;
            if (!$config) {
                throw new \Exception("Configuration non trouvée pour {$software}");
            }

            $token = $this->getAccessToken($software);
            if (!$token) {
                throw new \Exception("Token d'accès non disponible pour {$software}");
            }

            // Test simple de connexion
            $endpoint = $config['endpoints']['accounts'] ?? $config['endpoints']['contacts'];
            $url = $config['base_url'] . $endpoint;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($url . '?limit=1');

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'message' => $response->successful() ? 'Connexion réussie' : 'Erreur de connexion',
                'software' => $software,
                'timestamp' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'software' => $software,
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * Récupère la liste des logiciels supportés
     */
    public function getSupportedSoftware()
    {
        return array_keys($this->apiConfigs);
    }

    /**
     * Configure un nouveau logiciel
     */
    public function configureSoftware($software, $config)
    {
        if (!isset($this->apiConfigs[$software])) {
            $this->apiConfigs[$software] = $config;
        }
        
        return true;
    }
}
