@extends('layouts.app')

@section('title', 'Comptabilité & Finances - Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">💰</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Comptabilité & Finances
                                </h1>
                                <p class="text-sm text-gray-600">Dashboard financier {{ ucfirst($userType) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Modules</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">💰 Dashboard Financier</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Gestion complète de la comptabilité et des finances pour {{ $userType === 'club' ? 'votre club' : 'votre association' }}
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Données temps réel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                            Rapports automatisés
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Financial Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">📈</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Revenus Totaux</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($financialData['total_revenue']) }} €</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-green-600">+12.5%</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">📉</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Dépenses Totales</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($financialData['total_expenses']) }} €</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-red-600">+8.2%</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">💎</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Bénéfice Net</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($financialData['net_profit']) }} €</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-blue-600">+15.3%</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">💰</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Budget Restant</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($financialData['budget_remaining']) }} €</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-purple-600">{{ $budgetData['budget_percentage'] }}% utilisé</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Breakdown -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Répartition des Revenus</h3>
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 text-sm">📊</span>
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Expense Breakdown -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Répartition des Dépenses</h3>
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-red-600 text-sm">📊</span>
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas id="expenseChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Budget Overview -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Aperçu du Budget</h3>
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 text-sm">💰</span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-blue-600 text-2xl">📅</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Budget Annuel</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($budgetData['annual_budget']) }} €</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-red-600 text-2xl">💸</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Montant Dépensé</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($budgetData['spent_amount']) }} €</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-green-600 text-2xl">✅</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Budget Restant</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($budgetData['remaining_amount']) }} €</p>
                </div>
            </div>
            <div class="mt-6">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Progression du budget</span>
                    <span>{{ $budgetData['budget_percentage'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full" style="width: {{ $budgetData['budget_percentage'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Transactions Récentes</h3>
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600 text-sm">💳</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($transactionsData['recent_transactions'] as $transaction)
                    <div class="flex items-center justify-between py-4 px-4 {{ $transaction->type === 'revenue' ? 'bg-green-50' : 'bg-red-50' }} rounded-lg border {{ $transaction->type === 'revenue' ? 'border-green-200' : 'border-red-200' }}">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r {{ $transaction->type === 'revenue' ? 'from-green-500 to-green-600' : 'from-red-500 to-red-600' }} rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white text-lg">{{ $transaction->type === 'revenue' ? '📈' : '📉' }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->description }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->date->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold {{ $transaction->type === 'revenue' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }} €
                            </span>
                            <p class="text-xs text-gray-400">{{ ucfirst($transaction->status) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6 text-center">
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Voir toutes les transactions →</a>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions Rapides</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('modules.finance.reports') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                        <span class="text-blue-600 text-xl">📊</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Rapports</h3>
                        <p class="text-sm text-gray-600">Voir les rapports détaillés</p>
                    </div>
                </a>
                
                <a href="{{ route('modules.finance.budgets') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                        <span class="text-green-600 text-xl">💰</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Budgets</h3>
                        <p class="text-sm text-gray-600">Gérer les budgets</p>
                    </div>
                </a>
                
                <a href="{{ route('modules.finance.transaction.edit', 'new') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center mr-3">
                        <span class="text-yellow-600 text-xl">✏️</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Nouvelle Transaction</h3>
                        <p class="text-sm text-gray-600">Ajouter une transaction</p>
                    </div>
                </a>
                
                <a href="{{ route('modules.finance.integrations') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                        <span class="text-purple-600 text-xl">🔗</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Intégrations</h3>
                        <p class="text-sm text-gray-600">Connecter des logiciels</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Actions Bancaires -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Intégrations Bancaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('modules.finance.bank-integrations') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                        <span class="text-blue-600 text-xl">🏦</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Banques</h3>
                        <p class="text-sm text-gray-600">Connecter vos comptes bancaires</p>
                    </div>
                </a>
                
                <button onclick="showBankStats()" 
                        class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                        <span class="text-green-600 text-xl">📊</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Statistiques</h3>
                        <p class="text-sm text-gray-600">Voir les données bancaires</p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @if($userType === 'club')
            'Matchday Revenue',
            'Sponsorship',
            'Merchandising',
            'Player Transfers',
            'Prize Money',
            'Other Revenue'
            @else
            'Club Contributions',
            'FIFA Grants',
            'Competition Fees',
            'Sponsorship',
            'Broadcasting Rights',
            'Other Revenue'
            @endif
        ],
        datasets: [{
            data: [
                @if($userType === 'club')
                {{ $revenueData['matchday_revenue'] }},
                {{ $revenueData['sponsorship'] }},
                {{ $revenueData['merchandising'] }},
                {{ $revenueData['player_transfers'] }},
                {{ $revenueData['prize_money'] }},
                {{ $revenueData['other_revenue'] }}
                @else
                {{ $revenueData['club_contributions'] }},
                {{ $revenueData['fifa_grants'] }},
                {{ $revenueData['competition_fees'] }},
                {{ $revenueData['sponsorship'] }},
                {{ $revenueData['broadcasting_rights'] }},
                {{ $revenueData['other_revenue'] }}
                @endif
            ],
            backgroundColor: [
                '#10B981',
                '#3B82F6',
                '#F59E0B',
                '#EF4444',
                '#8B5CF6',
                '#6B7280'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Expense Chart
const expenseCtx = document.getElementById('expenseChart').getContext('2d');
const expenseChart = new Chart(expenseCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @if($userType === 'club')
            'Player Salaries',
            'Staff Salaries',
            'Facility Maintenance',
            'Travel Expenses',
            'Equipment',
            'Other Expenses'
            @else
            'Staff Salaries',
            'Competition Organization',
            'Facility Rental',
            'Travel Expenses',
            'Equipment',
            'Other Expenses'
            @endif
        ],
        datasets: [{
            data: [
                @if($userType === 'club')
                {{ $expenseData['player_salaries'] }},
                {{ $expenseData['staff_salaries'] }},
                {{ $expenseData['facility_maintenance'] }},
                {{ $expenseData['travel_expenses'] }},
                {{ $expenseData['equipment'] }},
                {{ $expenseData['other_expenses'] }}
                @else
                {{ $expenseData['staff_salaries'] }},
                {{ $expenseData['competition_organization'] }},
                {{ $expenseData['facility_rental'] }},
                {{ $expenseData['travel_expenses'] }},
                {{ $expenseData['equipment'] }},
                {{ $expenseData['other_expenses'] }}
                @endif
            ],
            backgroundColor: [
                '#EF4444',
                '#F97316',
                '#EAB308',
                '#84CC16',
                '#06B6D4',
                '#8B5CF6'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Fonction pour afficher les statistiques bancaires
function showBankStats() {
    const stats = {
        connectedBanks: 3,
        activeAccounts: 8,
        totalBalance: 1250000,
        lastSync: '2024-01-15 14:30',
        transactionsToday: 23
    };
    
    const message = `
        📊 Statistiques Bancaires:
        
        🏦 Banques connectées: ${stats.connectedBanks}
        💳 Comptes actifs: ${stats.activeAccounts}
        💰 Solde total: ${stats.totalBalance.toLocaleString()} €
        🔄 Dernière sync: ${stats.lastSync}
        📈 Transactions aujourd'hui: ${stats.transactionsToday}
    `;
    
    alert(message);
}
</script>
@endsection
