<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Minimal - Données Médicales</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">🏥 Test Minimal - Données Médicales</h1>
        
        <!-- Informations du joueur -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">👤 Joueur</h2>
            <p><strong>ID:</strong> {{ $player->id }}</p>
            <p><strong>Nom:</strong> {{ $player->first_name }} {{ $player->last_name }}</p>
            <p><strong>Position:</strong> {{ $player->position ?? 'Non défini' }}</p>
        </div>

        <!-- Données médicales -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">📊 Données Médicales</h2>
            
            @if(isset($portalData['medicalData']))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-500/20 p-4 rounded-lg">
                        <h3 class="font-semibold text-blue-400">Statistiques</h3>
                        <p>Total dossiers: {{ $portalData['medicalData']['statistics']['total_records'] ?? 'N/A' }}</p>
                        <p>Traitements actifs: {{ $portalData['medicalData']['statistics']['active_treatments'] ?? 'N/A' }}</p>
                        <p>Rendez-vous: {{ $portalData['medicalData']['statistics']['upcoming_appointments'] ?? 'N/A' }}</p>
                    </div>
                    
                    <div class="bg-green-500/20 p-4 rounded-lg">
                        <h3 class="font-semibold text-green-400">Conformité PCMA</h3>
                        @if(isset($portalData['medicalData']['statistics']['pcma_compliance']))
                            <p>Taux: {{ $portalData['medicalData']['statistics']['pcma_compliance']['compliance_rate'] ?? 'N/A' }}%</p>
                            <p>Total: {{ $portalData['medicalData']['statistics']['pcma_compliance']['total'] ?? 'N/A' }}</p>
                        @else
                            <p>Données non disponibles</p>
                        @endif
                    </div>
                </div>

                <!-- Dossiers médicaux -->
                @if(isset($portalData['medicalData']['records']) && count($portalData['medicalData']['records']) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3 text-orange-400">📋 Dossiers ({{ count($portalData['medicalData']['records']) }})</h3>
                    <div class="space-y-3">
                        @foreach(array_slice($portalData['medicalData']['records'], 0, 3) as $index => $record)
                        <div class="bg-gray-700 p-3 rounded-lg">
                            <h4 class="font-semibold text-blue-400">{{ $index + 1 }}. {{ $record['title'] ?? 'Sans titre' }}</h4>
                            <p class="text-sm text-gray-300">{{ $record['description'] ?? 'Aucune description' }}</p>
                            <p class="text-xs text-gray-400">Type: {{ $record['type'] ?? 'Non défini' }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Prédictions -->
                @if(isset($portalData['medicalData']['predictions']) && count($portalData['medicalData']['predictions']) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3 text-purple-400">🔮 Prédictions ({{ count($portalData['medicalData']['predictions']) }})</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($portalData['medicalData']['predictions'] as $prediction)
                        <div class="bg-gray-700 p-3 rounded-lg">
                            <h4 class="font-semibold text-purple-400">{{ ucfirst($prediction['type'] ?? 'Type inconnu') }}</h4>
                            <p class="text-sm text-gray-300">Risque: {{ $prediction['risk_probability'] ?? 'N/A' }}%</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Tendances de santé -->
                @if(isset($portalData['medicalData']['statistics']['health_trends']))
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3 text-green-400">📈 Tendances de Santé</h3>
                    @php
                        $trends = $portalData['medicalData']['statistics']['health_trends'];
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="bg-gray-700 p-3 rounded-lg text-center">
                            <h4 class="font-semibold text-white">Tendance</h4>
                            <p class="text-lg">{{ $trends['trend'] ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-700 p-3 rounded-lg text-center">
                            <h4 class="font-semibold text-white">Variation</h4>
                            <p class="text-lg">{{ $trends['change'] ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-700 p-3 rounded-lg text-center">
                            <h4 class="font-semibold text-white">Score Récent</h4>
                            <p class="text-lg">{{ $trends['recent_avg'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                @endif

            @else
                <p class="text-red-400">❌ Aucune donnée médicale disponible</p>
            @endif
        </div>

        <!-- Debug des données -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">🐛 Debug des Données</h2>
            <div class="text-sm">
                <p><strong>Clés disponibles dans portalData:</strong></p>
                <ul class="list-disc list-inside ml-4 text-gray-300">
                    @if(isset($portalData))
                        @foreach(array_keys($portalData) as $key)
                            <li>{{ $key }}</li>
                        @endforeach
                    @else
                        <li class="text-red-400">portalData non défini</li>
                    @endif
                </ul>
                
                @if(isset($portalData['medicalData']))
                <p class="mt-4"><strong>Clés dans medicalData:</strong></p>
                <ul class="list-disc list-inside ml-4 text-gray-300">
                    @foreach(array_keys($portalData['medicalData']) as $key)
                        <li>{{ $key }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                ← Retour à l'accueil
            </a>
        </div>
    </div>
</body>
</html>
