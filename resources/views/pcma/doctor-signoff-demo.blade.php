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
                    {{ __('pcma_extra.label_d614b244c0b7') }}
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">{{ __('pcma_extra.label_58e7f53f1f83') }}</h3>
                        <div class="space-y-2 text-sm">
                            <div><strong>{{ __('pcma_extra.label_08ae3743ab84') }}</strong> {{ $athlete->name ?? 'John Doe' }}</div>
                            <div><strong>{{ __('pcma_extra.label_fa7c3f22acc3') }}</strong>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    FIT
                                </span>
                            </div>
                            <div><strong>{{ __('pcma_extra.label_bea5a1b51b03') }}</strong> {{ now()->format('d/m/Y') }}</div>
                            <div><strong>{{ __('pcma_extra.label_0fca27c69298') }}</strong> PCMA-{{ rand(1000, 9999) }}</div>
                            <div><strong>{{ __('pcma.physician_label') }}</strong> Dr. Smith</div>
                            <div><strong>{{ __('pcma_extra.label_38aec7f0fe5a') }}</strong> MED-{{ rand(10000, 99999) }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-green-900 mb-3">{{ __('pcma_extra.label_e734f25b5819') }}</h3>
                        <ul class="space-y-2 text-sm">
                            <li>{{ __('pcma_extra.label_837c0a17f18b') }}</li>
                            <li>{{ __('pcma_extra.label_39e9360e0183') }}</li>
                            <li>{{ __('pcma_extra.label_f05baacdaa71') }}</li>
                            <li>{{ __('pcma_extra.label_88290d7af5a1') }}</li>
                            <li>{{ __('pcma_extra.label_876836413790') }}</li>
                            <li>{{ __('pcma_extra.label_c8c94982a40b') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Vue.js Component Integration -->
            <div id="doctor-signoff-container">
                <!-- This is where the Vue component would be mounted -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-3">{{ __('pcma_extra.label_c3fbb2a1046d') }}</h3>
                    <p class="text-yellow-700 mb-4">
                        {{ __('pcma_extra.label_bdf73fb902ba') }}
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
                <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('pcma_extra.label_511286f37ad1') }}</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <h4 class="font-semibold text-gray-700">{{ __('pcma_extra.label_f5812a5d59b5') }}</h4>
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs">
                            import DoctorSignOff from './components/DoctorSignOff.vue'
                        </code>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-700">{{ __('pcma_extra.label_8c9ea2853c5e') }}</h4>
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
                        <h4 class="font-semibold text-gray-700">{{ __('pcma_extra.label_1b31e2e25319') }}</h4>
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
                    <h4 class="font-semibold text-gray-900 mb-2">{{ __('pcma_extra.label_ef10d8f114eb') }}</h4>
                    <p class="text-sm text-gray-600">
                        {{ __('pcma_extra.label_8c6f3468cd7c') }}
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">{{ __('pcma_extra.label_6237566e0321') }}</h4>
                    <p class="text-sm text-gray-600">
                        {{ __('pcma_extra.label_534217015f87') }}
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">{{ __('pcma_extra.label_a9a1fd129238') }}</h4>
                    <p class="text-sm text-gray-600">
                        {{ __('pcma_extra.label_9f6666f39d1a') }}
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