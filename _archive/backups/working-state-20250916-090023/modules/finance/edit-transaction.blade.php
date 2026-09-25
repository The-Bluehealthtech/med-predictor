@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl mr-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white">
                        💰
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Édition Transaction</h1>
                        <p class="text-gray-600 mt-1">Modifier les données financières</p>
                    </div>
                </div>
                <a href="{{ route('modules.finance.dashboard') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    ← Retour au Dashboard
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" action="{{ route('modules.finance.transaction.update', $transaction->id ?? 'new') }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Type de Transaction -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de Transaction</label>
                        <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="revenue" {{ ($transaction->type ?? '') == 'revenue' ? 'selected' : '' }}>Revenu</option>
                            <option value="expense" {{ ($transaction->type ?? '') == 'expense' ? 'selected' : '' }}>Dépense</option>
                            <option value="transfer" {{ ($transaction->type ?? '') == 'transfer' ? 'selected' : '' }}>Transfert</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Montant</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">€</span>
                            <input type="number" name="amount" step="0.01" 
                                   value="{{ $transaction->amount ?? '' }}"
                                   class="w-full border border-gray-300 rounded-lg px-8 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   placeholder="0.00" required>
                        </div>
                    </div>
                </div>

                <!-- Description et Catégorie -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <input type="text" name="description" 
                               value="{{ $transaction->description ?? '' }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Description de la transaction" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                        <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Sélectionner une catégorie</option>
                            <optgroup label="Revenus">
                                <option value="matchday_revenue" {{ ($transaction->category ?? '') == 'matchday_revenue' ? 'selected' : '' }}>Recettes match</option>
                                <option value="sponsorship" {{ ($transaction->category ?? '') == 'sponsorship' ? 'selected' : '' }}>Sponsoring</option>
                                <option value="merchandising" {{ ($transaction->category ?? '') == 'merchandising' ? 'selected' : '' }}>Merchandising</option>
                                <option value="player_transfers" {{ ($transaction->category ?? '') == 'player_transfers' ? 'selected' : '' }}>Transferts joueurs</option>
                                <option value="prize_money" {{ ($transaction->category ?? '') == 'prize_money' ? 'selected' : '' }}>Prix et primes</option>
                            </optgroup>
                            <optgroup label="Dépenses">
                                <option value="player_salaries" {{ ($transaction->category ?? '') == 'player_salaries' ? 'selected' : '' }}>Salaires joueurs</option>
                                <option value="staff_salaries" {{ ($transaction->category ?? '') == 'staff_salaries' ? 'selected' : '' }}>Salaires staff</option>
                                <option value="facility_maintenance" {{ ($transaction->category ?? '') == 'facility_maintenance' ? 'selected' : '' }}>Maintenance installations</option>
                                <option value="travel_expenses" {{ ($transaction->category ?? '') == 'travel_expenses' ? 'selected' : '' }}>Frais de déplacement</option>
                                <option value="equipment" {{ ($transaction->category ?? '') == 'equipment' ? 'selected' : '' }}>Équipement</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <!-- Date et Statut -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" 
                               value="{{ $transaction->date ?? date('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="pending" {{ ($transaction->status ?? '') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="completed" {{ ($transaction->status ?? '') == 'completed' ? 'selected' : '' }}>Terminé</option>
                            <option value="cancelled" {{ ($transaction->status ?? '') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                              placeholder="Notes supplémentaires...">{{ $transaction->notes ?? '' }}</textarea>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('modules.finance.dashboard') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        {{ isset($transaction) ? 'Mettre à jour' : 'Créer' }} Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
