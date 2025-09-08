<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BankIntegrationService
{
    /**
     * Configuration des APIs bancaires françaises et internationales
     */
    private $bankConfigs = [
        // Banques françaises
        'bnp_paribas' => [
            'name' => 'BNP Paribas',
            'country' => 'FR',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.bnpparibas.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'statements' => '/statements'
            ],
            'features' => ['real_time', 'reconciliation', 'multi_currency']
        ],
        'credit_agricole' => [
            'name' => 'Crédit Agricole',
            'country' => 'FR',
            'api_type' => 'psd2',
            'base_url' => 'https://api.credit-agricole.fr/v2',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'cards' => '/cards'
            ],
            'features' => ['real_time', 'reconciliation', 'card_management']
        ],
        'societe_generale' => [
            'name' => 'Société Générale',
            'country' => 'FR',
            'api_type' => 'psd2',
            'base_url' => 'https://api.societegenerale.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'transfers' => '/transfers'
            ],
            'features' => ['real_time', 'reconciliation', 'transfers']
        ],
        'credit_mutuel' => [
            'name' => 'Crédit Mutuel',
            'country' => 'FR',
            'api_type' => 'psd2',
            'base_url' => 'https://api.creditmutuel.fr/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances'
            ],
            'features' => ['real_time', 'reconciliation']
        ],
        'lcl' => [
            'name' => 'LCL (Le Crédit Lyonnais)',
            'country' => 'FR',
            'api_type' => 'psd2',
            'base_url' => 'https://api.lcl.fr/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances'
            ],
            'features' => ['real_time', 'reconciliation']
        ],
        'banque_populaire' => [
            'name' => 'Banque Populaire',
            'country' => 'FR',
            'api_type' => 'psd2',
            'base_url' => 'https://api.banquepopulaire.fr/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances'
            ],
            'features' => ['real_time', 'reconciliation']
        ],
        
        // Banques internationales
        'hsbc' => [
            'name' => 'HSBC',
            'country' => 'INT',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.hsbc.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'forex' => '/forex'
            ],
            'features' => ['real_time', 'reconciliation', 'multi_currency', 'forex']
        ],
        'deutsche_bank' => [
            'name' => 'Deutsche Bank',
            'country' => 'DE',
            'api_type' => 'psd2',
            'base_url' => 'https://api.deutsche-bank.de/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'investments' => '/investments'
            ],
            'features' => ['real_time', 'reconciliation', 'investments']
        ],
        'commerzbank' => [
            'name' => 'Commerzbank',
            'country' => 'DE',
            'api_type' => 'psd2',
            'base_url' => 'https://api.commerzbank.de/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'business' => '/business'
            ],
            'features' => ['real_time', 'reconciliation', 'business']
        ],
        'unicredit' => [
            'name' => 'UniCredit',
            'country' => 'IT',
            'api_type' => 'psd2',
            'base_url' => 'https://api.unicredit.it/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'cards' => '/cards'
            ],
            'features' => ['real_time', 'reconciliation', 'card_management']
        ],
        'intesa_sanpaolo' => [
            'name' => 'Intesa Sanpaolo',
            'country' => 'IT',
            'api_type' => 'psd2',
            'base_url' => 'https://api.intesasanpaolo.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'investments' => '/investments'
            ],
            'features' => ['real_time', 'reconciliation', 'investments']
        ],
        'bbva' => [
            'name' => 'BBVA',
            'country' => 'ES',
            'api_type' => 'psd2',
            'base_url' => 'https://api.bbva.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'cards' => '/cards'
            ],
            'features' => ['real_time', 'reconciliation', 'card_management']
        ],
        'santander' => [
            'name' => 'Banco Santander',
            'country' => 'ES',
            'api_type' => 'psd2',
            'base_url' => 'https://api.santander.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'business' => '/business'
            ],
            'features' => ['real_time', 'reconciliation', 'business']
        ],
        'ing' => [
            'name' => 'ING Bank',
            'country' => 'NL',
            'api_type' => 'psd2',
            'base_url' => 'https://api.ing.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'savings' => '/savings'
            ],
            'features' => ['real_time', 'reconciliation', 'savings']
        ],
        'rabobank' => [
            'name' => 'Rabobank',
            'country' => 'NL',
            'api_type' => 'psd2',
            'base_url' => 'https://api.rabobank.nl/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'business' => '/business'
            ],
            'features' => ['real_time', 'reconciliation', 'business']
        ],
        'ubs' => [
            'name' => 'UBS',
            'country' => 'CH',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.ubs.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'investments' => '/investments',
                'wealth' => '/wealth'
            ],
            'features' => ['real_time', 'reconciliation', 'investments', 'wealth_management']
        ],
        'credit_suisse' => [
            'name' => 'Credit Suisse',
            'country' => 'CH',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.credit-suisse.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'investments' => '/investments',
                'wealth' => '/wealth'
            ],
            'features' => ['real_time', 'reconciliation', 'investments', 'wealth_management']
        ],
        'jpmorgan' => [
            'name' => 'JPMorgan Chase',
            'country' => 'US',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.jpmorgan.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'investments' => '/investments',
                'business' => '/business'
            ],
            'features' => ['real_time', 'reconciliation', 'investments', 'business']
        ],
        'bank_of_america' => [
            'name' => 'Bank of America',
            'country' => 'US',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.bankofamerica.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'credit_cards' => '/credit-cards',
                'mortgages' => '/mortgages'
            ],
            'features' => ['real_time', 'reconciliation', 'credit_cards', 'mortgages']
        ],
        'wells_fargo' => [
            'name' => 'Wells Fargo',
            'country' => 'US',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.wellsfargo.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'business' => '/business',
                'investments' => '/investments'
            ],
            'features' => ['real_time', 'reconciliation', 'business', 'investments']
        ],
        'citibank' => [
            'name' => 'Citibank',
            'country' => 'US',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.citibank.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'credit_cards' => '/credit-cards',
                'business' => '/business'
            ],
            'features' => ['real_time', 'reconciliation', 'credit_cards', 'business']
        ],
        'goldman_sachs' => [
            'name' => 'Goldman Sachs',
            'country' => 'US',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.goldmansachs.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'investments' => '/investments',
                'wealth' => '/wealth'
            ],
            'features' => ['real_time', 'reconciliation', 'investments', 'wealth_management']
        ],
        'morgan_stanley' => [
            'name' => 'Morgan Stanley',
            'country' => 'US',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.morganstanley.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'investments' => '/investments',
                'wealth' => '/wealth'
            ],
            'features' => ['real_time', 'reconciliation', 'investments', 'wealth_management']
        ],
        'barclays' => [
            'name' => 'Barclays',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.barclays.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'cards' => '/cards',
                'loans' => '/loans'
            ],
            'features' => ['real_time', 'reconciliation', 'card_management', 'lending']
        ],
        'lloyds' => [
            'name' => 'Lloyds Bank',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.lloydsbank.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'savings' => '/savings'
            ],
            'features' => ['real_time', 'reconciliation', 'savings']
        ],
        'hsbc_uk' => [
            'name' => 'HSBC UK',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.hsbc.co.uk/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'forex' => '/forex',
                'business' => '/business'
            ],
            'features' => ['real_time', 'reconciliation', 'multi_currency', 'forex', 'business']
        ],
        'natwest' => [
            'name' => 'NatWest',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.natwest.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'business' => '/business',
                'investments' => '/investments'
            ],
            'features' => ['real_time', 'reconciliation', 'business', 'investments']
        ],
        'rbs' => [
            'name' => 'Royal Bank of Scotland',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.rbs.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'business' => '/business'
            ],
            'features' => ['real_time', 'reconciliation', 'business']
        ],
        'santander_uk' => [
            'name' => 'Santander UK',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.santander.co.uk/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'mortgages' => '/mortgages'
            ],
            'features' => ['real_time', 'reconciliation', 'mortgages']
        ],
        'halifax' => [
            'name' => 'Halifax',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.halifax.co.uk/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'savings' => '/savings'
            ],
            'features' => ['real_time', 'reconciliation', 'savings']
        ],
        'tsb' => [
            'name' => 'TSB Bank',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.tsb.co.uk/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances'
            ],
            'features' => ['real_time', 'reconciliation']
        ],
        'nationwide' => [
            'name' => 'Nationwide Building Society',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.nationwide.co.uk/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'mortgages' => '/mortgages'
            ],
            'features' => ['real_time', 'reconciliation', 'mortgages']
        ],
        'metro_bank' => [
            'name' => 'Metro Bank',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.metrobank.co.uk/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'business' => '/business'
            ],
            'features' => ['real_time', 'reconciliation', 'business']
        ],
        'virgin_money' => [
            'name' => 'Virgin Money',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.virginmoney.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'credit_cards' => '/credit-cards'
            ],
            'features' => ['real_time', 'reconciliation', 'credit_cards']
        ],
        'first_direct' => [
            'name' => 'First Direct',
            'country' => 'UK',
            'api_type' => 'open_banking',
            'base_url' => 'https://api.firstdirect.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances'
            ],
            'features' => ['real_time', 'reconciliation']
        ],
        'monese' => [
            'name' => 'Monese',
            'country' => 'UK',
            'api_type' => 'fintech',
            'base_url' => 'https://api.monese.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'cards' => '/cards',
                'forex' => '/forex'
            ],
            'features' => ['real_time', 'reconciliation', 'multi_currency', 'forex', 'card_management']
        ],
        'starling' => [
            'name' => 'Starling Bank',
            'country' => 'UK',
            'api_type' => 'fintech',
            'base_url' => 'https://api.starlingbank.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'cards' => '/cards',
                'business' => '/business'
            ],
            'features' => ['real_time', 'reconciliation', 'card_management', 'business']
        ],
        'monzo' => [
            'name' => 'Monzo',
            'country' => 'UK',
            'api_type' => 'fintech',
            'base_url' => 'https://api.monzo.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'pots' => '/pots',
                'webhooks' => '/webhooks'
            ],
            'features' => ['real_time', 'reconciliation', 'savings_pots', 'webhooks']
        ],
        
        // Fintech et néobanques
        'revolut' => [
            'name' => 'Revolut',
            'country' => 'INT',
            'api_type' => 'fintech',
            'base_url' => 'https://api.revolut.com/v1',
            'auth_type' => 'api_key',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'cards' => '/cards',
                'forex' => '/forex'
            ],
            'features' => ['real_time', 'reconciliation', 'multi_currency', 'forex', 'card_management']
        ],
        'n26' => [
            'name' => 'N26',
            'country' => 'DE',
            'api_type' => 'fintech',
            'base_url' => 'https://api.n26.com/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'cards' => '/cards'
            ],
            'features' => ['real_time', 'reconciliation', 'card_management']
        ],
        'qonto' => [
            'name' => 'Qonto',
            'country' => 'FR',
            'api_type' => 'fintech',
            'base_url' => 'https://api.qonto.com/v2',
            'auth_type' => 'api_key',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'attachments' => '/attachments'
            ],
            'features' => ['real_time', 'reconciliation', 'attachments']
        ],
        'shin' => [
            'name' => 'Shine',
            'country' => 'FR',
            'api_type' => 'fintech',
            'base_url' => 'https://api.shine.fr/v1',
            'auth_type' => 'oauth2',
            'endpoints' => [
                'accounts' => '/accounts',
                'transactions' => '/transactions',
                'balances' => '/balances',
                'invoices' => '/invoices'
            ],
            'features' => ['real_time', 'reconciliation', 'invoicing']
        ]
    ];

    /**
     * Synchronise les données bancaires
     */
    public function syncBankData($bankCode, $type, $accountId = null, $data = [])
    {
        try {
            $config = $this->bankConfigs[$bankCode] ?? null;
            if (!$config) {
                throw new \Exception("Configuration bancaire non trouvée pour {$bankCode}");
            }

            // Vérifier l'authentification
            $token = $this->getBankAccessToken($bankCode);
            if (!$token) {
                throw new \Exception("Token d'accès bancaire non disponible pour {$bankCode}");
            }

            // Effectuer la synchronisation selon le type
            switch ($type) {
                case 'accounts':
                    return $this->syncAccounts($bankCode, $config, $token);
                case 'transactions':
                    return $this->syncTransactions($bankCode, $config, $token, $accountId, $data);
                case 'balances':
                    return $this->syncBalances($bankCode, $config, $token, $accountId);
                case 'statements':
                    return $this->syncStatements($bankCode, $config, $token, $accountId, $data);
                default:
                    throw new \Exception("Type de synchronisation bancaire non supporté: {$type}");
            }

        } catch (\Exception $e) {
            Log::error("Erreur de synchronisation bancaire avec {$bankCode}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Synchronise les comptes bancaires
     */
    private function syncAccounts($bankCode, $config, $token)
    {
        $endpoint = $config['endpoints']['accounts'];
        $url = $config['base_url'] . $endpoint;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($url);

        if ($response->successful()) {
            $accountsData = $response->json();
            $processedAccounts = $this->processBankAccounts($bankCode, $accountsData);
            
            Log::info("Synchronisation des comptes réussie pour {$bankCode}: " . count($processedAccounts) . " comptes");
            
            return [
                'success' => true,
                'bank' => $bankCode,
                'type' => 'accounts',
                'accounts_count' => count($processedAccounts),
                'accounts' => $processedAccounts,
                'timestamp' => now()->toISOString()
            ];
        } else {
            throw new \Exception("Erreur API bancaire {$bankCode}: " . $response->body());
        }
    }

    /**
     * Synchronise les transactions bancaires
     */
    private function syncTransactions($bankCode, $config, $token, $accountId, $data)
    {
        $endpoint = $config['endpoints']['transactions'];
        $url = $config['base_url'] . $endpoint;
        
        $params = array_merge([
            'account_id' => $accountId,
            'from_date' => $data['from_date'] ?? now()->subDays(30)->format('Y-m-d'),
            'to_date' => $data['to_date'] ?? now()->format('Y-m-d'),
            'limit' => $data['limit'] ?? 100
        ], $data);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($url, $params);

        if ($response->successful()) {
            $transactionsData = $response->json();
            $processedTransactions = $this->processBankTransactions($bankCode, $transactionsData);
            
            Log::info("Synchronisation des transactions réussie pour {$bankCode}: " . count($processedTransactions) . " transactions");
            
            return [
                'success' => true,
                'bank' => $bankCode,
                'type' => 'transactions',
                'transactions_count' => count($processedTransactions),
                'transactions' => $processedTransactions,
                'account_id' => $accountId,
                'timestamp' => now()->toISOString()
            ];
        } else {
            throw new \Exception("Erreur API bancaire {$bankCode}: " . $response->body());
        }
    }

    /**
     * Synchronise les soldes bancaires
     */
    private function syncBalances($bankCode, $config, $token, $accountId)
    {
        $endpoint = $config['endpoints']['balances'];
        $url = $config['base_url'] . $endpoint;
        
        $params = $accountId ? ['account_id' => $accountId] : [];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($url, $params);

        if ($response->successful()) {
            $balancesData = $response->json();
            $processedBalances = $this->processBankBalances($bankCode, $balancesData);
            
            Log::info("Synchronisation des soldes réussie pour {$bankCode}");
            
            return [
                'success' => true,
                'bank' => $bankCode,
                'type' => 'balances',
                'balances' => $processedBalances,
                'timestamp' => now()->toISOString()
            ];
        } else {
            throw new \Exception("Erreur API bancaire {$bankCode}: " . $response->body());
        }
    }

    /**
     * Synchronise les relevés bancaires
     */
    private function syncStatements($bankCode, $config, $token, $accountId, $data)
    {
        $endpoint = $config['endpoints']['statements'] ?? $config['endpoints']['transactions'];
        $url = $config['base_url'] . $endpoint;
        
        $params = array_merge([
            'account_id' => $accountId,
            'statement_type' => 'monthly',
            'format' => 'json'
        ], $data);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($url, $params);

        if ($response->successful()) {
            $statementsData = $response->json();
            $processedStatements = $this->processBankStatements($bankCode, $statementsData);
            
            Log::info("Synchronisation des relevés réussie pour {$bankCode}");
            
            return [
                'success' => true,
                'bank' => $bankCode,
                'type' => 'statements',
                'statements' => $processedStatements,
                'timestamp' => now()->toISOString()
            ];
        } else {
            throw new \Exception("Erreur API bancaire {$bankCode}: " . $response->body());
        }
    }

    /**
     * Traite les comptes bancaires selon la banque
     */
    private function processBankAccounts($bankCode, $rawData)
    {
        $processed = [];
        
        switch ($bankCode) {
            case 'bnp_paribas':
            case 'credit_agricole':
            case 'societe_generale':
                foreach ($rawData['accounts'] ?? [] as $account) {
                    $processed[] = [
                        'id' => $account['account_id'],
                        'name' => $account['account_name'],
                        'type' => $account['account_type'],
                        'iban' => $account['iban'],
                        'currency' => $account['currency'],
                        'status' => $account['status'],
                        'bank' => $bankCode,
                        'external_id' => $account['account_id']
                    ];
                }
                break;
                
            case 'revolut':
            case 'n26':
                foreach ($rawData['accounts'] ?? [] as $account) {
                    $processed[] = [
                        'id' => $account['id'],
                        'name' => $account['name'],
                        'type' => $account['type'],
                        'iban' => $account['iban'],
                        'currency' => $account['currency'],
                        'status' => $account['status'],
                        'bank' => $bankCode,
                        'external_id' => $account['id']
                    ];
                }
                break;
                
            case 'qonto':
                foreach ($rawData['accounts'] ?? [] as $account) {
                    $processed[] = [
                        'id' => $account['slug'],
                        'name' => $account['name'],
                        'type' => $account['type'],
                        'iban' => $account['iban'],
                        'currency' => $account['currency'],
                        'status' => $account['status'],
                        'bank' => $bankCode,
                        'external_id' => $account['slug']
                    ];
                }
                break;
                
            default:
                $processed = $rawData;
        }
        
        return $processed;
    }

    /**
     * Traite les transactions bancaires selon la banque
     */
    private function processBankTransactions($bankCode, $rawData)
    {
        $processed = [];
        
        foreach ($rawData['transactions'] ?? [] as $transaction) {
            $processed[] = [
                'id' => $transaction['transaction_id'] ?? $transaction['id'],
                'account_id' => $transaction['account_id'],
                'amount' => $transaction['amount'],
                'currency' => $transaction['currency'],
                'description' => $transaction['description'],
                'date' => $transaction['date'],
                'type' => $transaction['amount'] > 0 ? 'credit' : 'debit',
                'category' => $transaction['category'] ?? 'uncategorized',
                'reference' => $transaction['reference'] ?? '',
                'bank' => $bankCode,
                'external_id' => $transaction['transaction_id'] ?? $transaction['id']
            ];
        }
        
        return $processed;
    }

    /**
     * Traite les soldes bancaires selon la banque
     */
    private function processBankBalances($bankCode, $rawData)
    {
        $processed = [];
        
        foreach ($rawData['balances'] ?? [] as $balance) {
            $processed[] = [
                'account_id' => $balance['account_id'],
                'balance' => $balance['balance'],
                'currency' => $balance['currency'],
                'date' => $balance['date'],
                'bank' => $bankCode
            ];
        }
        
        return $processed;
    }

    /**
     * Traite les relevés bancaires selon la banque
     */
    private function processBankStatements($bankCode, $rawData)
    {
        $processed = [];
        
        foreach ($rawData['statements'] ?? [] as $statement) {
            $processed[] = [
                'id' => $statement['statement_id'],
                'account_id' => $statement['account_id'],
                'period_start' => $statement['period_start'],
                'period_end' => $statement['period_end'],
                'opening_balance' => $statement['opening_balance'],
                'closing_balance' => $statement['closing_balance'],
                'currency' => $statement['currency'],
                'bank' => $bankCode
            ];
        }
        
        return $processed;
    }

    /**
     * Récupère le token d'accès bancaire
     */
    private function getBankAccessToken($bankCode)
    {
        // En production, récupérer depuis la base de données ou un service de gestion des tokens
        $tokens = Cache::get('bank_tokens', []);
        
        return $tokens[$bankCode] ?? null;
    }

    /**
     * Teste la connexion avec une banque
     */
    public function testBankConnection($bankCode)
    {
        try {
            $config = $this->bankConfigs[$bankCode] ?? null;
            if (!$config) {
                throw new \Exception("Configuration bancaire non trouvée pour {$bankCode}");
            }

            $token = $this->getBankAccessToken($bankCode);
            if (!$token) {
                throw new \Exception("Token d'accès bancaire non disponible pour {$bankCode}");
            }

            // Test simple de connexion
            $endpoint = $config['endpoints']['accounts'];
            $url = $config['base_url'] . $endpoint;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($url . '?limit=1');

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'message' => $response->successful() ? 'Connexion bancaire réussie' : 'Erreur de connexion bancaire',
                'bank' => $bankCode,
                'bank_name' => $config['name'],
                'timestamp' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'bank' => $bankCode,
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * Effectue la réconciliation automatique des transactions
     */
    public function reconcileTransactions($bankTransactions, $systemTransactions)
    {
        $reconciled = [];
        $unmatched = [];
        
        foreach ($bankTransactions as $bankTransaction) {
            $matched = false;
            
            foreach ($systemTransactions as $systemTransaction) {
                if ($this->isTransactionMatch($bankTransaction, $systemTransaction)) {
                    $reconciled[] = [
                        'bank_transaction' => $bankTransaction,
                        'system_transaction' => $systemTransaction,
                        'match_type' => 'exact',
                        'confidence' => 100
                    ];
                    $matched = true;
                    break;
                }
            }
            
            if (!$matched) {
                $unmatched[] = $bankTransaction;
            }
        }
        
        return [
            'reconciled_count' => count($reconciled),
            'unmatched_count' => count($unmatched),
            'reconciled_transactions' => $reconciled,
            'unmatched_transactions' => $unmatched
        ];
    }

    /**
     * Vérifie si deux transactions correspondent
     */
    private function isTransactionMatch($bankTransaction, $systemTransaction)
    {
        // Correspondance exacte par montant et date
        $amountMatch = abs($bankTransaction['amount']) === abs($systemTransaction['amount']);
        $dateMatch = $bankTransaction['date'] === $systemTransaction['date'];
        
        return $amountMatch && $dateMatch;
    }

    /**
     * Récupère la liste des banques supportées
     */
    public function getSupportedBanks()
    {
        return array_keys($this->bankConfigs);
    }

    /**
     * Récupère les informations d'une banque
     */
    public function getBankInfo($bankCode)
    {
        return $this->bankConfigs[$bankCode] ?? null;
    }

    /**
     * Configure une nouvelle banque
     */
    public function configureBank($bankCode, $config)
    {
        if (!isset($this->bankConfigs[$bankCode])) {
            $this->bankConfigs[$bankCode] = $config;
        }
        
        return true;
    }
}
