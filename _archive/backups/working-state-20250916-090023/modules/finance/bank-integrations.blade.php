@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl mr-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                        🏦
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Intégrations Bancaires</h1>
                        <p class="text-gray-600 mt-1">Connectez vos comptes bancaires pour une synchronisation automatique</p>
                    </div>
                </div>
                <a href="{{ route('modules.finance.dashboard') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    ← Retour au Dashboard
                </a>
            </div>
        </div>

        <!-- Statistiques des Intégrations Bancaires -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-4">
                        <span class="text-blue-600 text-xl">🏦</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Banques Connectées</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ $connectedBanks ?? 3 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-4">
                        <span class="text-green-600 text-xl">💳</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Comptes Actifs</h3>
                        <p class="text-2xl font-bold text-green-600">{{ $activeAccounts ?? 8 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-4">
                        <span class="text-purple-600 text-xl">🔄</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Synchronisations</h3>
                        <p class="text-2xl font-bold text-purple-600">{{ $syncCount ?? 1247 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center mr-4">
                        <span class="text-yellow-600 text-xl">💰</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Solde Total</h3>
                        <p class="text-2xl font-bold text-yellow-600">{{ number_format($totalBalance ?? 1250000) }} €</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres par Pays -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Filtrer par Pays</h2>
            <div class="flex flex-wrap gap-2">
                <button onclick="filterBanks('all')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Tous ({{ $totalBanks ?? 35 }})
                </button>
                <button onclick="filterBanks('FR')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    🇫🇷 France ({{ $frenchBanks ?? 6 }})
                </button>
                <button onclick="filterBanks('UK')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    🇬🇧 Royaume-Uni ({{ $ukBanks ?? 12 }})
                </button>
                <button onclick="filterBanks('DE')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    🇩🇪 Allemagne ({{ $germanBanks ?? 2 }})
                </button>
                <button onclick="filterBanks('US')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    🇺🇸 États-Unis ({{ $usBanks ?? 6 }})
                </button>
                <button onclick="filterBanks('INT')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    🌍 International ({{ $internationalBanks ?? 9 }})
                </button>
            </div>
        </div>

        <!-- Banques Disponibles -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Banques Disponibles</h2>
            
            <div id="banks-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Banques Françaises -->
                <div class="bank-card border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-country="FR">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-2xl">🏛️</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">BNP Paribas</h3>
                            <p class="text-sm text-gray-600">Banque française</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✅ Connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Synchronisation automatique des comptes et transactions en temps réel.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                            Configurer
                        </button>
                        <button onclick="testBankConnection('bnp_paribas')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                        <button onclick="syncBankData('bnp_paribas', 'transactions')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Sync
                        </button>
                    </div>
                </div>

                <div class="bank-card border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-country="FR">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                            <span class="text-green-600 text-2xl">🌾</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Crédit Agricole</h3>
                            <p class="text-sm text-gray-600">Banque française</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✅ Connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Gestion des comptes courants et épargne avec API PSD2.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Configurer
                        </button>
                        <button onclick="testBankConnection('credit_agricole')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                        <button onclick="syncBankData('credit_agricole', 'transactions')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Sync
                        </button>
                    </div>
                </div>

                <!-- Banques Anglaises -->
                <div class="bank-card border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-country="UK">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                            <span class="text-purple-600 text-2xl">🏦</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Barclays</h3>
                            <p class="text-sm text-gray-600">Banque britannique</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✅ Connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Open Banking UK avec gestion des cartes et prêts.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition-colors">
                            Configurer
                        </button>
                        <button onclick="testBankConnection('barclays')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                        <button onclick="syncBankData('barclays', 'transactions')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Sync
                        </button>
                    </div>
                </div>

                <div class="bank-card border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-country="UK">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-2xl">🦁</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Lloyds Bank</h3>
                            <p class="text-sm text-gray-600">Banque britannique</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            ⚪ Non connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Open Banking avec gestion de l'épargne et des investissements.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                            Connecter
                        </button>
                        <button onclick="testBankConnection('lloyds')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                        <button onclick="syncBankData('lloyds', 'transactions')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Sync
                        </button>
                    </div>
                </div>

                <div class="bank-card border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-country="UK">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                            <span class="text-green-600 text-2xl">⭐</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Starling Bank</h3>
                            <p class="text-sm text-gray-600">Néobanque britannique</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✅ Connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">API moderne avec gestion des cartes et comptes business.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Configurer
                        </button>
                        <button onclick="testBankConnection('starling')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                        <button onclick="syncBankData('starling', 'transactions')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Sync
                        </button>
                    </div>
                </div>

                <div class="bank-card border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-country="UK">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-pink-100 flex items-center justify-center mr-3">
                            <span class="text-pink-600 text-2xl">💳</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Monzo</h3>
                            <p class="text-sm text-gray-600">Néobanque britannique</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            ⚪ Non connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">API innovante avec pots d'épargne et webhooks temps réel.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-pink-600 text-white text-sm rounded hover:bg-pink-700 transition-colors">
                            Connecter
                        </button>
                        <button onclick="testBankConnection('monzo')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                        <button onclick="syncBankData('monzo', 'transactions')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Sync
                        </button>
                    </div>
                </div>

                <!-- Banques Américaines -->
                <div class="bank-card border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-country="US">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mr-3">
                            <span class="text-red-600 text-2xl">🏛️</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">JPMorgan Chase</h3>
                            <p class="text-sm text-gray-600">Banque américaine</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            ⚪ Non connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Open Banking US avec gestion des investissements et business.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                            Connecter
                        </button>
                        <button onclick="testBankConnection('jpmorgan')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                        <button onclick="syncBankData('jpmorgan', 'transactions')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Sync
                        </button>
                    </div>
                </div>

                <div class="bank-card border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-country="US">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-2xl">🏦</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Bank of America</h3>
                            <p class="text-sm text-gray-600">Banque américaine</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            ⚪ Non connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Gestion des cartes de crédit et hypothèques.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                            Connecter
                        </button>
                        <button onclick="testBankConnection('bank_of_america')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                        <button onclick="syncBankData('bank_of_america', 'transactions')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Sync
                        </button>
                    </div>
                </div>

                <!-- Fintech Internationales -->
                <div class="bank-card border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-country="INT">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                            <span class="text-purple-600 text-2xl">🌍</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Revolut</h3>
                            <p class="text-sm text-gray-600">Fintech internationale</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✅ Connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Multi-devises avec gestion des cartes et forex.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition-colors">
                            Configurer
                        </button>
                        <button onclick="testBankConnection('revolut')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                        <button onclick="syncBankData('revolut', 'transactions')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Sync
                        </button>
                    </div>
                </div>

                <div class="bank-card border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow" data-country="INT">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center mr-3">
                            <span class="text-orange-600 text-2xl">🏢</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Qonto</h3>
                            <p class="text-sm text-gray-600">Fintech française</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            ⚪ Non connecté
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Comptes business avec gestion des pièces jointes.</p>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-orange-600 text-white text-sm rounded hover:bg-orange-700 transition-colors">
                            Connecter
                        </button>
                        <button onclick="testBankConnection('qonto')" class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                            Tester
                        </button>
                        <button onclick="syncBankData('qonto', 'transactions')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                            Sync
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des Synchronisations Bancaires -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Historique des Synchronisations Bancaires</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Banque</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Éléments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                        <span class="text-blue-600 text-sm">🏛️</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">BNP Paribas</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Transactions</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-15 14:30</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Réussi
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">127 transactions</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900">Voir détails</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                        <span class="text-purple-600 text-sm">🏦</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Barclays</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Soldes</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-15 12:15</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Réussi
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3 comptes</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900">Voir détails</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                                        <span class="text-green-600 text-sm">⭐</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Starling Bank</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Comptes</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-14 16:45</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    ⚠️ Erreur
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">0 éléments</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-red-600 hover:text-red-900">Voir erreur</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour filtrer les banques par pays
function filterBanks(country) {
    const bankCards = document.querySelectorAll('.bank-card');
    const buttons = document.querySelectorAll('button[onclick^="filterBanks"]');
    
    // Mettre à jour les boutons
    buttons.forEach(button => {
        button.classList.remove('bg-blue-600', 'text-white');
        button.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    // Activer le bouton sélectionné
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    event.target.classList.add('bg-blue-600', 'text-white');
    
    // Filtrer les cartes
    bankCards.forEach(card => {
        if (country === 'all' || card.dataset.country === country) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Fonction pour tester la connexion avec une banque
async function testBankConnection(bankCode) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Afficher le loading
    button.textContent = 'Test...';
    button.disabled = true;
    button.classList.add('opacity-50');
    
    try {
        const response = await fetch('{{ route("modules.finance.test-bank-connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                bank: bankCode
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Succès
            button.textContent = '✅ Connecté';
            button.classList.remove('border-gray-300', 'text-gray-700');
            button.classList.add('border-green-500', 'text-green-700', 'bg-green-50');
            
            // Afficher une notification
            showNotification('Connexion bancaire réussie avec ' + result.bank_name, 'success');
        } else {
            // Erreur
            button.textContent = '❌ Erreur';
            button.classList.remove('border-gray-300', 'text-gray-700');
            button.classList.add('border-red-500', 'text-red-700', 'bg-red-50');
            
            // Afficher une notification d'erreur
            showNotification('Erreur de connexion bancaire: ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('Erreur lors du test de connexion bancaire:', error);
        button.textContent = '❌ Erreur';
        button.classList.remove('border-gray-300', 'text-gray-700');
        button.classList.add('border-red-500', 'text-red-700', 'bg-red-50');
        
        showNotification('Erreur lors du test de connexion bancaire', 'error');
    }
    
    // Réinitialiser le bouton après 3 secondes
    setTimeout(() => {
        button.textContent = originalText;
        button.disabled = false;
        button.classList.remove('opacity-50', 'border-green-500', 'text-green-700', 'bg-green-50', 'border-red-500', 'text-red-700', 'bg-red-50');
        button.classList.add('border-gray-300', 'text-gray-700');
    }, 3000);
}

// Fonction pour synchroniser les données bancaires
async function syncBankData(bankCode, type) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Afficher le loading
    button.textContent = 'Sync...';
    button.disabled = true;
    button.classList.add('opacity-50');
    
    try {
        const response = await fetch('{{ route("modules.finance.sync-bank-data") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                bank: bankCode,
                type: type
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            button.textContent = '✅ Terminé';
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-green-500', 'hover:bg-green-600');
            
            showNotification(`Synchronisation ${type} réussie avec ${bankCode}`, 'success');
        } else {
            button.textContent = '❌ Erreur';
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-red-600', 'hover:bg-red-700');
            
            showNotification('Erreur de synchronisation bancaire: ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('Erreur lors de la synchronisation bancaire:', error);
        button.textContent = '❌ Erreur';
        button.classList.remove('bg-green-600', 'hover:bg-green-700');
        button.classList.add('bg-red-600', 'hover:bg-red-700');
        
        showNotification('Erreur lors de la synchronisation bancaire', 'error');
    }
    
    // Réinitialiser le bouton après 3 secondes
    setTimeout(() => {
        button.textContent = originalText;
        button.disabled = false;
        button.classList.remove('opacity-50', 'bg-green-500', 'hover:bg-green-600', 'bg-red-600', 'hover:bg-red-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
    }, 3000);
}

// Fonction pour afficher des notifications
function showNotification(message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span>
            <span>${message}</span>
        </div>
    `;
    
    // Ajouter au DOM
    document.body.appendChild(notification);
    
    // Supprimer après 5 secondes
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endsection
