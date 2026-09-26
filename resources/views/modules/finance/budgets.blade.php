@extends('layouts.app')

@section('title', 'Gestion des Budgets')

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
                                    Gestion des Budgets
                                </h1>
                                <p class="text-sm text-gray-600">Planification et suivi des budgets {{ ucfirst($userType) }}</p>
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
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <p class="text-yellow-800 font-semibold mb-2">⚠️ Gestion des budgets non disponible</p>
            <p class="text-sm text-yellow-800">
                Cette application ne dispose pas encore d'un module de comptabilité générale : il n'existe aujourd'hui
                aucune donnée réelle de budget annuel, de budget par catégorie (salaires, maintenance, déplacements, équipement...)
                ni de suivi trimestriel. Les chiffres qui s'affichaient ici auparavant (budget annuel, montants dépensés, pourcentages)
                étaient des exemples fixes, identiques pour tous les clubs et associations, et ont été retirés.
            </p>
            <p class="text-sm text-yellow-800 mt-3">
                Mettre en place une vraie gestion de budgets nécessiterait de créer ce modèle de données dans l'application ;
                cela dépasse le cadre d'un nettoyage de données factices et devrait être traité comme un projet à part.
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-8 p-6 text-center">
            <p class="text-sm text-gray-500">
                En attendant, les seules données financières réelles disponibles pour {{ $userType === 'club' ? 'votre club' : 'votre association' }}
                sont les paiements de transferts de joueurs, visibles depuis le
                <a href="{{ route('modules.finance.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">Dashboard Financier</a>.
            </p>
        </div>
    </div>
</div>
@endsection
