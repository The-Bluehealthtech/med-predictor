<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Onglet Médical - FIT Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center">🏥 Test de l'Onglet Médical</h1>
        
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">📊 Données du Joueur</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p><strong>Nom:</strong> {{ $player->first_name }} {{ $player->last_name }}</p>
                    <p><strong>Position:</strong> {{ $player->position ?? 'Non défini' }}</p>
                    <p><strong>Club:</strong> {{ $player->club?->name ?? 'Non défini' }}</p>
                </div>
                <div>
                    <p><strong>Âge:</strong> {{ $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : 'Non défini' }}</p>
                    <p><strong>Nationalité:</strong> {{ $player->nationality ?? 'Non définie' }}</p>
                </div>
            </div>
        </div>

        <!-- Onglet Médical -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-blue-400">🏥 Onglet Médical</h2>
            
            <!-- Résumé médical -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-blue-500/20 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-400">{{ $portalData['medicalData']['statistics']['total_records'] }}</div>
                    <div class="text-sm text-blue-200">Dossiers médicaux</div>
                </div>
                <div class="bg-green-500/20 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-400">{{ $portalData['medicalData']['statistics']['active_treatments'] }}</div>
                    <div class="text-sm text-green-200">Traitements actifs</div>
                </div>
                <div class="bg-yellow-500/20 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-400">{{ $portalData['medicalData']['statistics']['upcoming_appointments'] }}</div>
                    <div class="text-sm text-yellow-200">Rendez-vous</div>
                </div>
                <div class="bg-purple-500/20 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-purple-400">€{{ $portalData['medicalData']['statistics']['total_cost'] }}</div>
                    <div class="text-sm text-purple-200">Coût total</div>
                </div>
            </div>

            <!-- Distribution des risques -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4 text-green-400">📈 Distribution des Risques</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($portalData['medicalData']['statistics']['risk_distribution'] as $risk => $count)
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <div class="text-lg font-semibold text-{{ $risk === 'low' ? 'green' : ($risk === 'medium' ? 'yellow' : 'red') }}-400">
                            {{ ucfirst($risk) }}
                        </div>
                        <div class="text-2xl font-bold">{{ $count }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Conformité PCMA -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4 text-blue-400">⚽ Conformité PCMA FIFA</h3>
                <div class="bg-gray-700 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span>Taux de conformité</span>
                        <span class="text-xl font-bold text-blue-400">{{ $portalData['medicalData']['statistics']['pcma_compliance']['compliance_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-600 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $portalData['medicalData']['statistics']['pcma_compliance']['compliance_rate'] }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-400 mt-2">
                        <span>Total: {{ $portalData['medicalData']['statistics']['pcma_compliance']['total'] }}</span>
                        <span>Conformes: {{ $portalData['medicalData']['statistics']['pcma_compliance']['compliant'] }}</span>
                        <span>Expirés: {{ $portalData['medicalData']['statistics']['pcma_compliance']['expired'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Dossiers médicaux récents -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4 text-orange-400">📋 Dossiers Médicaux Récents</h3>
                <div class="space-y-4">
                    @foreach(array_slice($portalData['medicalData']['records'], 0, 3) as $record)
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold text-blue-400">{{ $record['title'] }}</h4>
                            <span class="text-sm text-gray-400">{{ $record['date'] ? \Carbon\Carbon::parse($record['date'])->format('d/m/Y') : 'Date inconnue' }}</span>
                        </div>
                        <p class="text-gray-300 mb-2">{{ $record['description'] }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                            <div><strong>Médecin:</strong> {{ $record['doctor_name'] }}</div>
                            <div><strong>Centre:</strong> {{ $record['medical_center'] }}</div>
                            @if(isset($record['diagnosis']))
                            <div><strong>Diagnostic:</strong> {{ $record['diagnosis'] }}</div>
                            @endif
                            @if(isset($record['treatment_plan']))
                            <div><strong>Traitement:</strong> {{ $record['treatment_plan'] }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Prédictions IA -->
            @if(count($portalData['medicalData']['predictions']) > 0)
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4 text-purple-400">🔮 Prédictions Médicales IA</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($portalData['medicalData']['predictions'] as $prediction)
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <h4 class="font-semibold text-purple-400 mb-2">{{ ucfirst($prediction['type']) }}</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>Condition:</strong> {{ $prediction['condition'] }}</div>
                            <div><strong>Risque:</strong> {{ $prediction['risk_probability'] }}%</div>
                            <div><strong>Confiance:</strong> {{ $prediction['confidence_score'] }}%</div>
                            @if($prediction['factors'])
                            <div><strong>Facteurs:</strong> {{ is_array($prediction['factors']) ? implode(', ', $prediction['factors']) : $prediction['factors'] }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Résumé de santé -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4 text-green-400">💚 Résumé de Santé Global</h3>
                <div class="bg-gray-700 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Statut global:</strong> <span class="text-{{ $portalData['medicalData']['health_summary']['overall_status'] === 'Excellent' ? 'green' : ($portalData['medicalData']['health_summary']['overall_status'] === 'Surveillance' ? 'yellow' : 'red') }}-400">{{ $portalData['medicalData']['health_summary']['overall_status'] }}</span></p>
                            <p><strong>Dernier contrôle:</strong> {{ $portalData['medicalData']['health_summary']['last_checkup'] ? \Carbon\Carbon::parse($portalData['medicalData']['health_summary']['last_checkup'])->format('d/m/Y') : 'Non défini' }}</p>
                            <p><strong>Prochain contrôle:</strong> {{ $portalData['medicalData']['health_summary']['next_checkup'] ? \Carbon\Carbon::parse($portalData['medicalData']['health_summary']['next_checkup'])->format('d/m/Y') : 'Non défini' }}</p>
                        </div>
                        <div>
                            <p><strong>Médicaments actuels:</strong> {{ count($portalData['medicalData']['health_summary']['current_medications']) }}</p>
                            <p><strong>Allergies:</strong> {{ count($portalData['medicalData']['health_summary']['allergies']) }}</p>
                            <p><strong>Restrictions:</strong> {{ count($portalData['medicalData']['health_summary']['restrictions']) }}</p>
                        </div>
                    </div>
                </div>
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




