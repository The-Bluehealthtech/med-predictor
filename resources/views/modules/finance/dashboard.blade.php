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

        @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-8 text-sm">
            ℹ️ {{ session('info') }}
        </div>
        @endif

        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">💰 Dashboard Financier</h2>
                    <p class="text-lg text-gray-600 mb-2">
                        Gestion de la comptabilité et des finances pour {{ $userType === 'club' ? 'votre club' : 'votre association' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Avis : pas de comptabilite generale -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
            <p class="text-sm text-yellow-800">
                ⚠️ <strong>Comptabilité générale non disponible.</strong> Cette application n'a pas de module de comptabilité générale
                (revenus, dépenses, budgets, salaires) : aucun logiciel comptable ou compte bancaire n'y est connecté.
                Les seules données financières réellement enregistrées ci-dessous concernent les <strong>paiements de transferts de joueurs</strong>.
            </p>
        </div>

        <!-- Key Financial Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xl">📈</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Revenus Totaux</p>
                        <p class="text-2xl font-bold text-gray-400">Non disponible</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xl">📉</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Dépenses Totales</p>
                        <p class="text-2xl font-bold text-gray-400">Non disponible</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xl">🔁</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Frais de transfert reçus</p>
                        <p class="text-2xl font-bold text-gray-900">
                            @if($financialData['transfer_fees_received'] !== null)
                                {{ number_format($financialData['transfer_fees_received'], 0, ',', ' ') }} €
                            @else
                                Non disponible
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xl">🔁</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Frais de transfert payés</p>
                        <p class="text-2xl font-bold text-gray-900">
                            @if($financialData['transfer_fees_paid'] !== null)
                                {{ number_format($financialData['transfer_fees_paid'], 0, ',', ' ') }} €
                            @else
                                Non disponible
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Paiements de Transferts Récents</h3>
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600 text-sm">💳</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if($transactionsData['recent_transactions']->isEmpty())
                <p class="text-sm text-gray-500 text-center py-6">Aucun paiement de transfert enregistré pour le moment.</p>
                @else
                <div class="space-y-4">
                    @foreach($transactionsData['recent_transactions'] as $transaction)
                    @php
                        $statusColors = [
                            'completed' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-600'],
                            'pending' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-600'],
                            'cancelled' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-600'],
                        ];
                        $colors = $statusColors[$transaction->payment_status] ?? ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-600'];
                    @endphp
                    <div class="flex items-center justify-between py-4 px-4 {{ $colors['bg'] }} rounded-lg border {{ $colors['border'] }}">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white text-lg">🔁</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->payment_type ?? 'Transfert')) }}
                                    — {{ $transaction->payer->name ?? '—' }} → {{ $transaction->payee->name ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $transaction->payment_date ? $transaction->payment_date->format('d/m/Y') : 'Date non renseignée' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold {{ $colors['text'] }}">
                                {{ number_format((float) $transaction->amount, 0, ',', ' ') }} €
                            </span>
                            <p class="text-xs text-gray-400">{{ ucfirst($transaction->payment_status ?? '—') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($transactionsData['pending_transactions'] > 0)
                <p class="text-xs text-gray-500 mt-4">{{ $transactionsData['pending_transactions'] }} paiement(s) en attente.</p>
                @endif
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions Rapides</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <div class="grid grid-cols-1 gap-4">
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
            </div>
        </div>
    </div>
</div>
@endsection
