<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Association;
use App\Models\Player;
use App\Models\GameMatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FinanceIntegrationService;
use App\Services\BankIntegrationService;

class FinanceController extends Controller
{
    /**
     * Affiche le dashboard financier pour clubs et associations
     */
    public function index()
    {
        $user = Auth::user();
        $userType = $this->getUserType($user);
        
        $financialData = $this->getFinancialData($user, $userType);
        $revenueData = $this->getRevenueData($user, $userType);
        $expenseData = $this->getExpenseData($user, $userType);
        $budgetData = $this->getBudgetData($user, $userType);
        $transactionsData = $this->getTransactionsData($user, $userType);
        
        return view('modules.finance.dashboard', compact(
            'financialData',
            'revenueData',
            'expenseData',
            'budgetData',
            'transactionsData',
            'userType'
        ));
    }

    /**
     * Détermine le type d'utilisateur (club ou association)
     */
    private function getUserType($user)
    {
        if ($user->club_id) {
            return 'club';
        } elseif ($user->association_id) {
            return 'association';
        }
        return 'system';
    }

    /**
     * Récupère les données financières principales
     */
    private function getFinancialData($user, $userType)
    {
        try {
            if ($userType === 'club') {
                return [
                    'total_revenue' => $this->getClubRevenue($user->club_id),
                    'total_expenses' => $this->getClubExpenses($user->club_id),
                    'net_profit' => $this->getClubRevenue($user->club_id) - $this->getClubExpenses($user->club_id),
                    'budget_allocated' => $this->getClubBudget($user->club_id),
                    'budget_remaining' => $this->getClubBudget($user->club_id) - $this->getClubExpenses($user->club_id),
                    'player_salaries' => $this->getPlayerSalaries($user->club_id),
                    'transfer_fees' => $this->getTransferFees($user->club_id),
                    'match_revenue' => $this->getMatchRevenue($user->club_id)
                ];
            } elseif ($userType === 'association') {
                return [
                    'total_revenue' => $this->getAssociationRevenue($user->association_id),
                    'total_expenses' => $this->getAssociationExpenses($user->association_id),
                    'net_profit' => $this->getAssociationRevenue($user->association_id) - $this->getAssociationExpenses($user->association_id),
                    'budget_allocated' => $this->getAssociationBudget($user->association_id),
                    'budget_remaining' => $this->getAssociationBudget($user->association_id) - $this->getAssociationExpenses($user->association_id),
                    'club_contributions' => $this->getClubContributions($user->association_id),
                    'fifa_grants' => $this->getFifaGrants($user->association_id),
                    'competition_revenue' => $this->getCompetitionRevenue($user->association_id)
                ];
            }
        } catch (\Exception $e) {
            // Données simulées en cas d'erreur
            return $this->getSimulatedFinancialData($userType);
        }
        
        return $this->getSimulatedFinancialData($userType);
    }

    /**
     * Récupère les données de revenus
     */
    private function getRevenueData($user, $userType)
    {
        try {
            if ($userType === 'club') {
                return [
                    'matchday_revenue' => 45000,
                    'sponsorship' => 120000,
                    'merchandising' => 25000,
                    'player_transfers' => 80000,
                    'prize_money' => 15000,
                    'other_revenue' => 10000
                ];
            } elseif ($userType === 'association') {
                return [
                    'club_contributions' => 200000,
                    'fifa_grants' => 150000,
                    'competition_fees' => 75000,
                    'sponsorship' => 100000,
                    'broadcasting_rights' => 300000,
                    'other_revenue' => 50000
                ];
            }
        } catch (\Exception $e) {
            return $this->getSimulatedRevenueData($userType);
        }
        
        return $this->getSimulatedRevenueData($userType);
    }

    /**
     * Récupère les données de dépenses
     */
    private function getExpenseData($user, $userType)
    {
        try {
            if ($userType === 'club') {
                return [
                    'player_salaries' => 180000,
                    'staff_salaries' => 45000,
                    'facility_maintenance' => 25000,
                    'travel_expenses' => 15000,
                    'equipment' => 10000,
                    'other_expenses' => 20000
                ];
            } elseif ($userType === 'association') {
                return [
                    'staff_salaries' => 120000,
                    'competition_organization' => 80000,
                    'facility_rental' => 40000,
                    'travel_expenses' => 30000,
                    'equipment' => 15000,
                    'other_expenses' => 25000
                ];
            }
        } catch (\Exception $e) {
            return $this->getSimulatedExpenseData($userType);
        }
        
        return $this->getSimulatedExpenseData($userType);
    }

    /**
     * Récupère les données de budget
     */
    private function getBudgetData($user, $userType)
    {
        try {
            if ($userType === 'club') {
                return [
                    'annual_budget' => 500000,
                    'spent_amount' => 295000,
                    'remaining_amount' => 205000,
                    'budget_percentage' => 59.0,
                    'quarterly_budget' => 125000,
                    'quarterly_spent' => 75000,
                    'quarterly_remaining' => 50000
                ];
            } elseif ($userType === 'association') {
                return [
                    'annual_budget' => 1000000,
                    'spent_amount' => 310000,
                    'remaining_amount' => 690000,
                    'budget_percentage' => 31.0,
                    'quarterly_budget' => 250000,
                    'quarterly_spent' => 80000,
                    'quarterly_remaining' => 170000
                ];
            }
        } catch (\Exception $e) {
            return $this->getSimulatedBudgetData($userType);
        }
        
        return $this->getSimulatedBudgetData($userType);
    }

    /**
     * Récupère les données de transactions récentes
     */
    private function getTransactionsData($user, $userType)
    {
        try {
            return [
                'recent_transactions' => [
                    (object)[
                        'id' => 1,
                        'type' => 'revenue',
                        'description' => 'Sponsorship - Nike',
                        'amount' => 50000,
                        'date' => now()->subDays(2),
                        'status' => 'completed'
                    ],
                    (object)[
                        'id' => 2,
                        'type' => 'expense',
                        'description' => 'Player Salary - Jean Dupont',
                        'amount' => -15000,
                        'date' => now()->subDays(5),
                        'status' => 'completed'
                    ],
                    (object)[
                        'id' => 3,
                        'type' => 'revenue',
                        'description' => 'Matchday Revenue',
                        'amount' => 25000,
                        'date' => now()->subDays(7),
                        'status' => 'completed'
                    ],
                    (object)[
                        'id' => 4,
                        'type' => 'expense',
                        'description' => 'Facility Maintenance',
                        'amount' => -8000,
                        'date' => now()->subDays(10),
                        'status' => 'pending'
                    ],
                    (object)[
                        'id' => 5,
                        'type' => 'revenue',
                        'description' => 'Transfer Fee - Player Sale',
                        'amount' => 100000,
                        'date' => now()->subDays(15),
                        'status' => 'completed'
                    ]
                ],
                'pending_transactions' => 3,
                'total_transactions_month' => 25
            ];
        } catch (\Exception $e) {
            return [
                'recent_transactions' => collect(),
                'pending_transactions' => 0,
                'total_transactions_month' => 0
            ];
        }
    }

    // Méthodes pour récupérer les données réelles (à implémenter selon la structure de la DB)
    private function getClubRevenue($clubId) { return 295000; }
    private function getClubExpenses($clubId) { return 180000; }
    private function getClubBudget($clubId) { return 500000; }
    private function getPlayerSalaries($clubId) { return 120000; }
    private function getTransferFees($clubId) { return 50000; }
    private function getMatchRevenue($clubId) { return 45000; }

    private function getAssociationRevenue($associationId) { return 875000; }
    private function getAssociationExpenses($associationId) { return 310000; }
    private function getAssociationBudget($associationId) { return 1000000; }
    private function getClubContributions($associationId) { return 200000; }
    private function getFifaGrants($associationId) { return 150000; }
    private function getCompetitionRevenue($associationId) { return 300000; }

    // Méthodes pour les données simulées
    private function getSimulatedFinancialData($userType)
    {
        if ($userType === 'club') {
            return [
                'total_revenue' => 295000,
                'total_expenses' => 180000,
                'net_profit' => 115000,
                'budget_allocated' => 500000,
                'budget_remaining' => 320000,
                'player_salaries' => 120000,
                'transfer_fees' => 50000,
                'match_revenue' => 45000
            ];
        } else {
            return [
                'total_revenue' => 875000,
                'total_expenses' => 310000,
                'net_profit' => 565000,
                'budget_allocated' => 1000000,
                'budget_remaining' => 690000,
                'club_contributions' => 200000,
                'fifa_grants' => 150000,
                'competition_revenue' => 300000
            ];
        }
    }

    private function getSimulatedRevenueData($userType)
    {
        if ($userType === 'club') {
            return [
                'matchday_revenue' => 45000,
                'sponsorship' => 120000,
                'merchandising' => 25000,
                'player_transfers' => 80000,
                'prize_money' => 15000,
                'other_revenue' => 10000
            ];
        } else {
            return [
                'club_contributions' => 200000,
                'fifa_grants' => 150000,
                'competition_fees' => 75000,
                'sponsorship' => 100000,
                'broadcasting_rights' => 300000,
                'other_revenue' => 50000
            ];
        }
    }

    private function getSimulatedExpenseData($userType)
    {
        if ($userType === 'club') {
            return [
                'player_salaries' => 180000,
                'staff_salaries' => 45000,
                'facility_maintenance' => 25000,
                'travel_expenses' => 15000,
                'equipment' => 10000,
                'other_expenses' => 20000
            ];
        } else {
            return [
                'staff_salaries' => 120000,
                'competition_organization' => 80000,
                'facility_rental' => 40000,
                'travel_expenses' => 30000,
                'equipment' => 15000,
                'other_expenses' => 25000
            ];
        }
    }

    private function getSimulatedBudgetData($userType)
    {
        if ($userType === 'club') {
            return [
                'annual_budget' => 500000,
                'spent_amount' => 295000,
                'remaining_amount' => 205000,
                'budget_percentage' => 59.0,
                'quarterly_budget' => 125000,
                'quarterly_spent' => 75000,
                'quarterly_remaining' => 50000
            ];
        } else {
            return [
                'annual_budget' => 1000000,
                'spent_amount' => 310000,
                'remaining_amount' => 690000,
                'budget_percentage' => 31.0,
                'quarterly_budget' => 250000,
                'quarterly_spent' => 80000,
                'quarterly_remaining' => 170000
            ];
        }
    }

    /**
     * Affiche les rapports financiers détaillés
     */
    public function reports()
    {
        $user = Auth::user();
        $userType = $this->getUserType($user);
        
        return view('modules.finance.reports', compact('userType'));
    }

    /**
     * Affiche la gestion des budgets
     */
    public function budgets()
    {
        $user = Auth::user();
        $userType = $this->getUserType($user);
        
        return view('modules.finance.budgets', compact('userType'));
    }

    /**
     * Affiche le formulaire d'édition de transaction
     */
    public function editTransaction($id = null)
    {
        $transaction = null;
        if ($id && $id !== 'new') {
            try {
                // Simuler la récupération d'une transaction
                $transaction = (object)[
                    'id' => $id,
                    'type' => 'revenue',
                    'amount' => 50000,
                    'description' => 'Sponsorship - Nike',
                    'category' => 'sponsorship',
                    'date' => now()->subDays(2)->format('Y-m-d'),
                    'status' => 'completed',
                    'notes' => 'Contrat annuel de sponsoring'
                ];
            } catch (\Exception $e) {
                Log::error("Error loading transaction {$id}: " . $e->getMessage());
            }
        }
        
        return view('modules.finance.edit-transaction', compact('transaction'));
    }

    /**
     * Met à jour une transaction
     */
    public function updateTransaction(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:revenue,expense,transfer',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'category' => 'required|string',
            'date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        try {
            // Simuler la sauvegarde de la transaction
            Log::info("Transaction {$id} updated: " . json_encode($request->all()));
            
            return redirect()->route('modules.finance.dashboard')
                           ->with('success', 'Transaction mise à jour avec succès');
        } catch (\Exception $e) {
            Log::error("Error updating transaction {$id}: " . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Erreur lors de la mise à jour de la transaction')
                           ->withInput();
        }
    }

    /**
     * Affiche la page des intégrations API
     */
    public function integrations()
    {
        try {
            $activeIntegrations = $this->getActiveIntegrations();
            $syncCount = $this->getSyncCount();
            $errorCount = $this->getErrorCount();
        } catch (\Exception $e) {
            Log::error("Error loading integrations data: " . $e->getMessage());
            $activeIntegrations = 2;
            $syncCount = 156;
            $errorCount = 3;
        }
        
        return view('modules.finance.integrations', compact('activeIntegrations', 'syncCount', 'errorCount'));
    }

    /**
     * Synchronise avec un logiciel externe
     */
    public function syncWithExternal(Request $request)
    {
        $request->validate([
            'software' => 'required|string',
            'type' => 'required|in:import,export',
            'data' => 'nullable|array'
        ]);

        try {
            $software = $request->input('software');
            $type = $request->input('type');
            $data = $request->input('data', []);
            
            // Utiliser le service d'intégration
            $integrationService = new FinanceIntegrationService();
            $result = $integrationService->syncData($software, $type, $data);
            
            return response()->json([
                'success' => true,
                'message' => "Synchronisation {$type} avec {$software} réussie",
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error("Error syncing with {$request->input('software')}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Teste la connexion avec un logiciel externe
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'software' => 'required|string'
        ]);

        try {
            $software = $request->input('software');
            $integrationService = new FinanceIntegrationService();
            $result = $integrationService->testConnection($software);
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Error testing connection with {$request->input('software')}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test de connexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère le nombre d'intégrations actives
     */
    private function getActiveIntegrations()
    {
        // Simuler les intégrations actives
        return 2; // Sage et QuickBooks
    }

    /**
     * Récupère le nombre de synchronisations
     */
    private function getSyncCount()
    {
        // Simuler le nombre de synchronisations
        return 156;
    }

    /**
     * Récupère le nombre d'erreurs
     */
    private function getErrorCount()
    {
        // Simuler le nombre d'erreurs
        return 3;
    }

    /**
     * Effectue la synchronisation avec un logiciel externe
     */
    private function performSync($software, $type, $data = [])
    {
        // Simuler la synchronisation
        $result = [
            'software' => $software,
            'type' => $type,
            'timestamp' => now()->toISOString(),
            'items_processed' => rand(10, 100),
            'status' => 'success'
        ];

        // Simuler différents types de synchronisation
        switch ($software) {
            case 'sage':
                $result['api_endpoint'] = 'https://api.sage.com/v1/transactions';
                $result['items_processed'] = rand(20, 50);
                break;
            case 'quickbooks':
                $result['api_endpoint'] = 'https://sandbox-quickbooks.api.intuit.com/v3/company/1234567890/accounts';
                $result['items_processed'] = rand(15, 40);
                break;
            case 'xero':
                $result['api_endpoint'] = 'https://api.xero.com/api.xro/2.0/Contacts';
                $result['items_processed'] = rand(10, 30);
                break;
            default:
                $result['api_endpoint'] = 'custom_api_endpoint';
                $result['items_processed'] = rand(5, 25);
        }

        return $result;
    }

    /**
     * Affiche la page des intégrations bancaires
     */
    public function bankIntegrations()
    {
        try {
            $connectedBanks = $this->getConnectedBanks();
            $activeAccounts = $this->getActiveAccounts();
            $syncCount = $this->getBankSyncCount();
            $totalBalance = $this->getTotalBalance();
            $totalBanks = $this->getTotalBanks();
            $frenchBanks = $this->getFrenchBanks();
            $ukBanks = $this->getUKBanks();
            $germanBanks = $this->getGermanBanks();
            $usBanks = $this->getUSBanks();
            $internationalBanks = $this->getInternationalBanks();
        } catch (\Exception $e) {
            Log::error("Error loading bank integrations data: " . $e->getMessage());
            $connectedBanks = 3;
            $activeAccounts = 8;
            $syncCount = 1247;
            $totalBalance = 1250000;
            $totalBanks = 35;
            $frenchBanks = 6;
            $ukBanks = 12;
            $germanBanks = 2;
            $usBanks = 6;
            $internationalBanks = 9;
        }
        
        return view('modules.finance.bank-integrations', compact(
            'connectedBanks', 'activeAccounts', 'syncCount', 'totalBalance',
            'totalBanks', 'frenchBanks', 'ukBanks', 'germanBanks', 'usBanks', 'internationalBanks'
        ));
    }

    /**
     * Teste la connexion avec une banque
     */
    public function testBankConnection(Request $request)
    {
        $request->validate([
            'bank' => 'required|string'
        ]);

        try {
            $bankCode = $request->input('bank');
            $bankService = new BankIntegrationService();
            $result = $bankService->testBankConnection($bankCode);
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Error testing bank connection with {$request->input('bank')}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test de connexion bancaire',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchronise les données bancaires
     */
    public function syncBankData(Request $request)
    {
        $request->validate([
            'bank' => 'required|string',
            'type' => 'required|in:accounts,transactions,balances,statements',
            'account_id' => 'nullable|string',
            'data' => 'nullable|array'
        ]);

        try {
            $bankCode = $request->input('bank');
            $type = $request->input('type');
            $accountId = $request->input('account_id');
            $data = $request->input('data', []);
            
            $bankService = new BankIntegrationService();
            $result = $bankService->syncBankData($bankCode, $type, $accountId, $data);
            
            return response()->json([
                'success' => true,
                'message' => "Synchronisation {$type} avec {$bankCode} réussie",
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error("Error syncing bank data with {$request->input('bank')}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation bancaire',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Effectue la réconciliation automatique des transactions
     */
    public function reconcileTransactions(Request $request)
    {
        $request->validate([
            'bank_transactions' => 'required|array',
            'system_transactions' => 'required|array'
        ]);

        try {
            $bankTransactions = $request->input('bank_transactions');
            $systemTransactions = $request->input('system_transactions');
            
            $bankService = new BankIntegrationService();
            $result = $bankService->reconcileTransactions($bankTransactions, $systemTransactions);
            
            return response()->json([
                'success' => true,
                'message' => 'Réconciliation terminée',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error("Error reconciling transactions: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réconciliation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère le nombre de banques connectées
     */
    private function getConnectedBanks()
    {
        // Simuler les banques connectées
        return 3; // BNP Paribas, Crédit Agricole, Starling Bank
    }

    /**
     * Récupère le nombre de comptes actifs
     */
    private function getActiveAccounts()
    {
        // Simuler les comptes actifs
        return 8;
    }

    /**
     * Récupère le nombre de synchronisations bancaires
     */
    private function getBankSyncCount()
    {
        // Simuler le nombre de synchronisations
        return 1247;
    }

    /**
     * Récupère le solde total
     */
    private function getTotalBalance()
    {
        // Simuler le solde total
        return 1250000;
    }

    /**
     * Récupère le nombre total de banques
     */
    private function getTotalBanks()
    {
        return 35;
    }

    /**
     * Récupère le nombre de banques françaises
     */
    private function getFrenchBanks()
    {
        return 6;
    }

    /**
     * Récupère le nombre de banques britanniques
     */
    private function getUKBanks()
    {
        return 12;
    }

    /**
     * Récupère le nombre de banques allemandes
     */
    private function getGermanBanks()
    {
        return 2;
    }

    /**
     * Récupère le nombre de banques américaines
     */
    private function getUSBanks()
    {
        return 6;
    }

    /**
     * Récupère le nombre de banques internationales
     */
    private function getInternationalBanks()
    {
        return 9;
    }
}
