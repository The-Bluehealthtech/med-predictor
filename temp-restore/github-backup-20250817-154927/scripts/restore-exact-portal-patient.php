<?php

echo "=== RESTAURATION EXACTE DU LAYOUT PORTAL-PATIENT ===\n\n";

$file = 'resources/views/portail-joueur.blade.php';

if (!file_exists($file)) {
    echo "❌ Fichier non trouvé: $file\n";
    exit(1);
}

echo "📁 Fichier: $file\n";
echo "📊 Taille initiale: " . filesize($file) . " bytes\n\n";

// 1. RESTAURER EXACTEMENT LE LAYOUT PORTAL-PATIENT
echo "🔄 Restauration exacte du layout portal-patient...\n";

$exactPortalPatientContent = '@extends("layouts.app")

@section("content")
<div class="container mx-auto px-4 py-8">
    <!-- En-tête du patient -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="flex items-center space-x-6">
            <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                {{ substr($player->first_name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $player->first_name }} {{ $player->last_name }}</h1>
                <p class="text-lg text-gray-600">{{ $player->position ?? "Position non définie" }}</p>
                <p class="text-gray-500">{{ $player->nationality ?? "Nationalité non définie" }}</p>
            </div>
            <div class="ml-auto">
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600">{{ $player->fifa_overall_rating ?? "N/A" }}</div>
                    <div class="text-sm text-gray-500">Score FIFA</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-heartbeat text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Score GHS</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $player->ghs_overall_score ?? "N/A" }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-running text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Performances</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $player->performances->count() ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-file-medical text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Dossiers santé</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $player->healthRecords->count() ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Risque blessure</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $player->injury_risk_level ?? "N/A" }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails du patient -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Informations personnelles -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations personnelles</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Date de naissance:</span>
                    <span class="font-medium">{{ $player->date_of_birth ?? "N/A" }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Taille:</span>
                    <span class="font-medium">{{ $player->height ?? "N/A" }} cm</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Poids:</span>
                    <span class="font-medium">{{ $player->weight ?? "N/A" }} kg</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pied fort:</span>
                    <span class="font-medium">{{ $player->preferred_foot ?? "N/A" }}</span>
                </div>
            </div>
        </div>

        <!-- Scores détaillés -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Scores détaillés</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Physique:</span>
                    <span class="font-medium">{{ $player->fifa_physical_rating ?? "N/A" }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Mental:</span>
                    <span class="font-medium">{{ $player->fifa_mental_rating ?? "N/A" }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Technique:</span>
                    <span class="font-medium">{{ $player->fifa_technical_rating ?? "N/A" }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Vitesse:</span>
                    <span class="font-medium">{{ $player->fifa_speed_rating ?? "N/A" }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières performances -->
    @if($player->performances && $player->performances->count() > 0)
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Dernières performances</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minutes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($player->performances->take(5) as $performance)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $performance->match_date ?? "N/A" }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $performance->opponent ?? "N/A" }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $performance->score ?? "N/A" }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $performance->minutes_played ?? "N/A" }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Derniers dossiers de santé -->
    @if($player->healthRecords && $player->healthRecords->count() > 0)
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Derniers dossiers de santé</h2>
        <div class="space-y-4">
            @foreach($player->healthRecords->take(3) as $record)
            <div class="border-l-4 border-blue-500 pl-4 py-2">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-medium text-gray-900">Dossier #{{ $record->id }}</h3>
                        <p class="text-sm text-gray-600">{{ $record->record_date ?? "Date non définie" }}</p>
                        @if($record->diagnosis)
                        <p class="text-sm text-gray-700 mt-1">{{ $record->diagnosis }}</p>
                        @endif
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $record->status ?? "En cours" }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection';

// 2. SAUVEGARDER LE CONTENU EXACT
if (file_put_contents($file, $exactPortalPatientContent)) {
    echo "✅ Layout exact portal-patient restauré avec succès!\n";
    echo "📊 Taille finale: " . filesize($file) . " bytes\n";
} else {
    echo "❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 LAYOUT EXACT PORTAL-PATIENT RESTAURÉ!\n";
echo "🚀 Le portail a maintenant EXACTEMENT le format de portal-patient!\n";
echo "🎨 Aucune donnée statique, seulement les variables Blade!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Le format est maintenant IDENTIQUE à portal-patient!\n";
echo "✨ Aucune modification du layout, seulement les données dynamiques!\n";






