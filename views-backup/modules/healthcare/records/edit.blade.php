@extends('layouts.app')

@section('title', 'Modifier le Dossier Médical')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">✏️ Modifier le Dossier Médical</h1>
                    <p class="text-gray-600 mt-2">Modifier les informations du dossier médical</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('modules.healthcare.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        ← Retour
                    </a>
                    <button type="submit" form="edit-form" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        💾 Sauvegarder
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form id="edit-form" action="#" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Patient Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du Patient</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="patient_name" class="block text-sm font-medium text-gray-700 mb-2">Nom du Patient</label>
                        <input type="text" id="patient_name" name="patient_name" value="Patient Example" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="record_date" class="block text-sm font-medium text-gray-700 mb-2">Date de Création</label>
                        <input type="date" id="record_date" name="record_date" value="2024-08-01" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select id="status" name="status" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active" selected>Actif</option>
                            <option value="archived">Archivé</option>
                            <option value="pending">En Attente</option>
                        </select>
                    </div>
                    <div>
                        <label for="risk_score" class="block text-sm font-medium text-gray-700 mb-2">Score de Risque</label>
                        <input type="range" id="risk_score" name="risk_score" min="0" max="100" value="45" 
                               class="w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>0%</span>
                            <span id="risk_value">45%</span>
                            <span>100%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations Médicales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">Antécédents Médicaux</label>
                        <textarea id="medical_history" name="medical_history" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Décrivez les antécédents médicaux...">Aucun antécédent médical significatif noté.</textarea>
                    </div>
                    <div>
                        <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                        <textarea id="allergies" name="allergies" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Listez les allergies...">Aucune allergie connue.</textarea>
                    </div>
                    <div>
                        <label for="current_medications" class="block text-sm font-medium text-gray-700 mb-2">Médicaments Actuels</label>
                        <textarea id="current_medications" name="current_medications" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Listez les médicaments actuels...">Aucun médicament en cours.</textarea>
                    </div>
                    <div>
                        <label for="special_conditions" class="block text-sm font-medium text-gray-700 mb-2">Conditions Spéciales</label>
                        <textarea id="special_conditions" name="special_conditions" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Décrivez les conditions spéciales...">Aucune condition spéciale.</textarea>
                    </div>
                </div>
            </div>

            <!-- AI Predictions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Prédictions IA</h3>
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-900">Risque de Blessure</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Modéré
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">Évaluation basée sur les données de performance</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-900">Recommandations</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Actif
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">Exercices de prévention recommandés</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('risk_score').addEventListener('input', function() {
    document.getElementById('risk_value').textContent = this.value + '%';
});
</script>
@endsection 