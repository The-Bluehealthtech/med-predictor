@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl mr-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                        🏦
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Intégrations Bancaires</h1>
                        <p class="text-gray-600 mt-1">Connexion de comptes bancaires</p>
                    </div>
                </div>
                <a href="{{ route('modules.finance.dashboard') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    ← Retour au Dashboard
                </a>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <p class="text-yellow-800 font-semibold mb-2">⚠️ Aucune banque n'est réellement connectée</p>
            <p class="text-sm text-yellow-800">
                Cette page affichait auparavant une liste fixe de banques (BNP Paribas, Barclays, JPMorgan Chase, Revolut...)
                marquées « Connecté », un solde total et un historique de synchronisation — des données d'exemple identiques
                pour tous les utilisateurs, alors qu'aucune connexion bancaire réelle (API Open Banking / PSD2, identifiants,
                comptes) n'existe dans cette application pour {{ $userType === 'club' ? 'votre club' : 'votre association' }}.
            </p>
            <p class="text-sm text-yellow-800 mt-3">
                Ces éléments ont été retirés. Mettre en place de vraies connexions bancaires nécessiterait d'intégrer un
                vrai fournisseur Open Banking/PSD2 et de créer le modèle de données correspondant (comptes, identifiants,
                synchronisations) ; cela dépasse le cadre d'un nettoyage de données factices et devrait être traité comme
                un projet à part.
            </p>
        </div>
    </div>
</div>
@endsection
