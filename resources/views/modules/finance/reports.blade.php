@extends('layouts.app')

@section('title', 'Rapports Financiers')

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
                                <span class="text-white font-bold text-lg">📊</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Rapports Financiers
                                </h1>
                                <p class="text-sm text-gray-600">Analyse détaillée des finances {{ ucfirst($userType) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.finance.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Reports Section -->
        <div class="mb-8 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md p-4 text-sm">
            Aucun système de comptabilité générale (revenus, dépenses, marge, ROI) n'est encore connecté à cette plateforme.
            Les seules données financières réelles disponibles concernent les paiements de transferts de joueurs
            (voir le <a href="{{ route('modules.finance.dashboard') }}" class="underline font-medium">tableau de bord</a>).
        </div>

        <!-- Detailed Reports -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Rapports Détaillés</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 opacity-60">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-green-600">📈</span>
                            </div>
                            <h4 class="font-medium text-gray-900">Rapport Mensuel</h4>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Analyse des revenus et dépenses du mois</p>
                        <button disabled title="Fonctionnalité à venir : aucune donnée réelle disponible pour le moment" class="w-full bg-gray-300 text-gray-600 py-2 px-4 rounded-lg cursor-not-allowed">
                            Générer PDF (à venir)
                        </button>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 opacity-60">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-blue-600">📊</span>
                            </div>
                            <h4 class="font-medium text-gray-900">Rapport Trimestriel</h4>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Bilan financier du trimestre</p>
                        <button disabled title="Fonctionnalité à venir : aucune donnée réelle disponible pour le moment" class="w-full bg-gray-300 text-gray-600 py-2 px-4 rounded-lg cursor-not-allowed">
                            Générer PDF (à venir)
                        </button>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 opacity-60">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-purple-600">📋</span>
                            </div>
                            <h4 class="font-medium text-gray-900">Rapport Annuel</h4>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Bilan complet de l'année</p>
                        <button disabled title="Fonctionnalité à venir : aucune donnée réelle disponible pour le moment" class="w-full bg-gray-300 text-gray-600 py-2 px-4 rounded-lg cursor-not-allowed">
                            Générer PDF (à venir)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
