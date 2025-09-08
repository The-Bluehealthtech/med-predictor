<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo - Doctor Sign-Off Component</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div id="app" class="container mx-auto py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">🏥 Demo - Doctor Sign-Off Component</h1>
                <p class="text-gray-600 mb-6">
                    Cette page démontre l'intégration du composant Vue.js pour la signature médicale.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">📋 Données de Test</h3>
                        <div class="space-y-2 text-sm">
                            <div><strong>Nom du Joueur:</strong> {{ $athlete->name ?? 'John Doe' }}</div>
                            <div><strong>Décision Fitness:</strong> 
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    FIT
                                </span>
                            </div>
                            <div><strong>Date d'Examen:</strong> {{ now()->format('d/m/Y') }}</div>
                            <div><strong>ID d'Évaluation:</strong> PCMA-{{ rand(1000, 9999) }}</div>
                            <div><strong>Médecin:</strong> Dr. Smith</div>
                            <div><strong>Licence:</strong> MED-{{ rand(10000, 99999) }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-green-900 mb-3">✅ Fonctionnalités</h3>
                        <ul class="space-y-2 text-sm">
                            <li>✓ Signature digitale avec canvas</li>
                            <li>✓ Déclaration légale obligatoire</li>
                            <li>✓ Ré-authentification sécurisée</li>
                            <li>✓ Validation complète des données</li>
                            <li>✓ Interface professionnelle médicale</li>
                            <li>✓ Export des données signées</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Vue.js Component Integration -->
            <div id="doctor-signoff-container">
                <!-- This is where the Vue component would be mounted -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-3">🔄 Intégration Vue.js</h3>
                    <p class="text-yellow-700 mb-4">
                        Le composant DoctorSignOff.vue serait intégré ici avec les données suivantes:
                    </p>
                    <div class="bg-white rounded-lg p-4 text-left text-sm">
                        <pre class="text-xs overflow-x-auto">
{
  "playerName": "{{ $athlete->name ?? 'John Doe' }}",
  "fitnessDecision": "FIT",
  "examinationDate": "{{ now()->format('d/m/Y') }}",
  "assessmentId": "PCMA-{{ rand(1000, 9999) }}",
  "clinicalNotes": "Examen complet réalisé. Tous les paramètres dans les normes.",
  "doctorName": "Dr. Smith",
  "licenseNumber": "MED-{{ rand(10000, 99999) }}"
}
                        </pre>
                    </div>
                </div>
            </div>
            
            <!-- Integration Instructions -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">📚 Instructions d'Intégration</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <h4 class="font-semibold text-gray-700">1. Import du Composant</h4>
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs">
                            import DoctorSignOff from './components/DoctorSignOff.vue'
                        </code>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-700">2. Utilisation dans le Template</h4>
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs block">
                            &lt;DoctorSignOff 
                                :player-name="playerName"
                                :fitness-decision="fitnessDecision"
                                :examination-date="examinationDate"
                                :assessment-id="assessmentId"
                                :clinical-notes="clinicalNotes"
                                :doctor-name="doctorName"
                                :license-number="licenseNumber"
                                @signed="handleSigned"
                            /&gt;
                        </code>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-700">3. Gestion de l'Événement</h4>
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs block">
                            const handleSigned = (data) => {
                                console.log('Signed data:', data);
                                // Envoyer les données au serveur
                            }
                        </code>
                    </div>
                </div>
            </div>
            
            <!-- Features List -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">✍️ Signature Digitale</h4>
                    <p class="text-sm text-gray-600">
                        Capture de signature tactile et souris avec validation et confirmation.
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">⚖️ Déclaration Légale</h4>
                    <p class="text-sm text-gray-600">
                        Checkbox obligatoire pour confirmer la responsabilité médicale.
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">🔐 Sécurité</h4>
                    <p class="text-sm text-gray-600">
                        Ré-authentification et validation des données avant signature.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Demo Vue.js integration
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    showComponent: false,
                    signedData: null
                }
            },
            methods: {
                showDoctorSignoff() {
                    this.showComponent = true;
                },
                handleSigned(data) {
                    this.signedData = data;
                    console.log('Signed data received:', data);
                    alert('✅ Signature médicale validée!\n\nDonnées reçues:\n' + JSON.stringify(data, null, 2));
                }
            }
        }).mount('#app');
    </script>
</body>
</html> 