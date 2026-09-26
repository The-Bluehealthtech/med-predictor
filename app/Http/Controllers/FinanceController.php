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

        // NOTE (audit factice -> reel, ' + '2026-09) :
        // Aucun modele de comptabilite generale (recettes, depenses, budget,
        // salaires) n'existe dans la base de donnees de cette application.
        // Les chiffres qui s'affichaient auparavant ici (ex: revenu club
        // toujours 295 000EUR, budget association toujours 1 000 000EUR) etaient
        // codes en dur, identiques pour tout le monde. Ils ont ete retires.
        // La seule donnee financiere reellement enregistree en base concerne
        // les paiements de transferts de joueurs (App\Models\TransferPayment),
        // utilisee ci-dessous. Une vraie gestion financiere (budgets, salaires,
        // recettes de billetterie, sponsoring...) necessiterait de creer ce
        // modele de donnees, ce qui depasse le cadre d'un nettoyage de donnees
        // factices et devrait etre traite comme un projet a part.

        $financialData = $this->getFinancialData($user, $userType);
        $transactionsData = $this->getTransactionsData($user, $userType);

        return view('modules.finance.dashboard', compact(
            'financialData',
            'transactionsData',
            'userType'
        ));
    }

    /**
     * Determine le type d'utilisateur (club ou association)
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
     * Recupere les donnees financieres reelles disponibles.
     * transfer_fees_received/paid viennent de TransferPayment (reel).
     * Tout le reste (revenus, depenses, budget, salaires) n'a pas de
     * modele de donnees reel dans l'application -> null ("non disponible"),
     * plutot qu'un chiffre invente.
     */
    private function getFinancialData($user, $userType)
    {
        $data = [
            'total_revenue' => null,
            'total_expenses' => null,
            'net_profit' => null,
            'budget_allocated' => null,
            'budget_remaining' => null,
            'transfer_fees_received' => null,
            'transfer_fees_paid' => null,
        ];

        if ($userType === 'club' && $user->club_id) {
            $data['transfer_fees_received'] = (float) \App\Models\TransferPayment::where('payee_id', $user->club_id)
                ->where('payment_status', 'completed')
                ->sum('amount');
            $data['transfer_fees_paid'] = (float) \App\Models\TransferPayment::where('payer_id', $user->club_id)
                ->where('payment_status', 'completed')
                ->sum('amount');
        }

        return $data;
    }

    /**
     * Recupere les transactions de transfert reelles (les seules
     * transactions financieres enregistrees dans l'application).
     */
    private function getTransactionsData($user, $userType)
    {
        $query = \App\Models\TransferPayment::with(['payer', 'payee']);

        if ($userType === 'club' && $user->club_id) {
            $query->where(function ($q) use ($user) {
                $q->where('payer_id', $user->club_id)->orWhere('payee_id', $user->club_id);
            });
        } elseif ($userType !== 'system') {
            // Pas de club/association identifie : aucune transaction a montrer.
            $query->whereRaw('1 = 0');
        }

        $recent = $query->orderByDesc('payment_date')->limit(10)->get();

        return [
            'recent_transactions' => $recent,
            'pending_transactions' => $recent->where('payment_status', 'pending')->count(),
        ];
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

    // NOTE (audit factice -> reel, 2026-09) : editTransaction()/updateTransaction()
    // ont ete supprimees. editTransaction() fabriquait toujours la MEME
    // transaction factice ("Sponsorship - Nike", 50000) quel que soit l'id
    // demande, et updateTransaction() ne faisait que logger puis affichait
    // "mise a jour avec succes" sans ecrire dans aucune base (aucune table
    // "transactions" generique n'existe). Les deux methodes n'etaient
    // appelees par aucune route (la route /modules/finance/transaction/edit/{id}
    // est une closure independante qui affiche deja un message honnete).
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
            $activeIntegrations = 0;
            $syncCount = 0;
            $errorCount = 0;
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
        // Aucune configuration d'integration comptable n'est persistee en base :
        // aucun logiciel externe n'est reellement connecte pour le moment.
        return 0;
    }

    /**
     * Récupère le nombre de synchronisations
     */
    private function getSyncCount()
    {
        // Aucune synchronisation reelle n'a eu lieu (aucune integration connectee).
        return 0;
    }

    /**
     * Récupère le nombre d'erreurs
     */
    private function getErrorCount()
    {
        // Aucune synchronisation reelle -> aucune erreur reelle.
        return 0;
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
        // Aucune banque n'est reellement connectee (pas de credentials configures).
        return 0;
    }

    /**
     * Récupère le nombre de comptes actifs
     */
    private function getActiveAccounts()
    {
        return 0;
    }

    /**
     * Récupère le nombre de synchronisations bancaires
     */
    private function getBankSyncCount()
    {
        return 0;
    }

    /**
     * Récupère le solde total
     */
    private function getTotalBalance()
    {
        // Aucun compte bancaire reel connecte : pas de solde a afficher.
        return null;
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
