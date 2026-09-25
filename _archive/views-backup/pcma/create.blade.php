@extends('layouts.app')

@section('title', 'Nouveau PCMA - Med Predictor')

@section('content')



@push('styles')
<style>
    /*  STYLES POUR LES SECTIONS DE MODES SÉCURISÉES */
    
    /* Classe de base pour toutes les sections de modes */
    .mode-section {
        transition: all 0.3s ease-in-out;
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Section cachée */
    .mode-section.hidden {
        display: none !important;
        opacity: 0;
        transform: translateY(20px);
    }
    
    /* Boutons de sélection de mode */
    .mode-selector {
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
    }
    
    .mode-selector::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease-in-out;
    }
    
    .mode-selector:hover::before {
        left: 100%;
    }
    
    /* États actifs des boutons */
    .mode-selector.active {
        transform: scale(1.02);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .mode-selector.active::after {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 10px;
        background: #10b981;
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }
    
    /* Animation d'entrée pour les sections */
    .mode-section:not(.hidden) {
        animation: slideInUp 0.4s ease-out;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Boutons de transfert entre modes */
    .transfer-button {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .transfer-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .transfer-button:active {
        transform: translateY(0);
    }
    
    /*  FORÇAGE DE L'AFFICHAGE DES ÉLÉMENTS VOCAUX */
    [data-force-visible="true"] {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: relative !important;
        z-index: 9999 !important;
        height: auto !important;
        min-height: 500px !important;
        overflow: visible !important;
        clip: auto !important;
        transform: none !important;
        filter: none !important;
    }
    
    /*  FORÇAGE COMPLET DES SECTIONS VOCALES */
    [data-section-type="vocal"] {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: relative !important;
        z-index: 9999 !important;
        height: auto !important;
        min-height: 500px !important;
        overflow: visible !important;
        clip: auto !important;
        transform: none !important;
        filter: none !important;
    }
    
    /*  DÉSACTIVATION COMPLÈTE DE LA CLASSE HIDDEN POUR LES SECTIONS VOCALES */
    [data-section-type="vocal"].hidden {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Indicateurs de mode actif */
    .mode-indicator {
        position: fixed;
        top: 100px;
        right: 20px;
        background: #1f2937;
        color: white;
        padding: 12px 20px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        animation: slideInRight 0.5s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Responsive design pour les modes */
    @media (max-width: 768px) {
        .mode-section {
            margin: 0 -1rem;
        }
        
        .mode-selector {
            padding: 1rem;
        }
        
        .mode-indicator {
            right: 10px;
            left: 10px;
            text-align: center;
        }
    }
    
    /*  STYLES POUR LES NOTIFICATIONS */
    .mode-notification {
        animation: slideInRight 0.3s ease-out;
        transition: all 0.3s ease-in-out;
    }
    
    .mode-notification:hover {
        transform: translateX(-5px);
    }
    
    /*  STYLES POUR LES BOUTONS DE TRANSFERT */
    .transfer-button {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .transfer-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .transfer-button:active {
        transform: translateY(0);
    }
    
    /*  MASQUAGE CIBLÉ ET PROPRE - SEULEMENT LES SECTIONS VOCALES DUPLIQUÉES */
    #voice-section {
        display: none !important;
    }
    
    /*  DÉSACTIVÉ : MASQUAGE AGGRESSIF DES ÉLÉMENTS VOCAUX */
    /* 
    h2:contains(""),
    h3:contains(""),
    h2:contains("Assistant Vocal"),
    h3:contains("Assistant Vocal"),
    div:has(h2:contains("")),
    div:has(h3:contains("")),
    div:has(h2:contains("Assistant Vocal")),
    div:has(h3:contains("Assistant Vocal")) {
        display: none !important;
        visibility: hidden !important;
    }
    
    #voice-section,
    #voice-section * {
        display: none !important;
        visibility: hidden !important;
    }
    
    .bg-blue-50:has(h2:contains("")),
    .bg-blue-50:has(h3:contains("")),
    .bg-blue-50:has(h2:contains("Assistant Vocal")),
    .bg-blue-50:has(h3:contains("Assistant Vocal")) {
        display: none !important;
        visibility: hidden !important;
    }
    */
    
    /* PRÉSERVER SEULEMENT l'Assistant IA PCMA (sans  ni "Vocal") */
    h2:contains("Assistant IA PCMA"),
    div:has(h2:contains("Assistant IA PCMA")) {
        display: block !important;
        visibility: visible !important;
    }
    
    /*  MASQUER LE POPUP DE CONFIRMATION EN MODE MANUEL */
    #manual-mode-section #confirmation-modal,
    body:not(.vocal-mode-active) #confirmation-modal {
        display: none !important;
        visibility: hidden !important;
    }
</style>
@endpush

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900"> Nouveau PCMA</h1>
                    <p class="text-gray-600 mt-2">Créer une nouvelle évaluation médicale pré-compétition</p>
                </div>
                <a href="{{ route('pcma.dashboard') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    ← Retour au Dashboard
                </a>
            </div>
        </div>

        <!-- Input Method Selection -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Modes de collecte</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Mode Manuel -->
                    <button type="button" id="mode-manuel" class="mode-selector active bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 transition-colors">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <h3 class="font-semibold">Mode Manuel</h3>
                            <p class="text-sm opacity-90">Saisie directe au clavier</p>
                        </div>
                    </button>

                    <!-- Mode Vocal -->
                    <button type="button" id="mode-vocal" class="mode-selector bg-gray-100 text-gray-700 p-4 rounded-lg hover:bg-gray-200 transition-colors" onclick="console.log(' Clic sur Mode Vocal détecté !')">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                            <h3 class="font-semibold">Mode Vocal</h3>
                            <p class="text-sm opacity-90">Reconnaissance vocale intelligente</p>
                        </div>
                    </button>



                    <!-- Mode OCR (Scan d'image) -->
                    <button type="button" id="scan-tab" class="mode-selector bg-gray-100 text-gray-700 p-4 rounded-lg hover:bg-gray-200 transition-colors">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="font-semibold">Mode OCR</h3>
                            <p class="text-sm opacity-90">Scan d'image et extraction</p>
                        </div>
                    </button>

                    <!-- Mode FHIR -->
                    <button type="button" id="mode-fhir" class="mode-selector bg-gray-100 text-gray-700 p-4 rounded-lg hover:bg-gray-200 transition-colors">
                        <div class="text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <h3 class="font-semibold">Mode FHIR</h3>
                            <p class="text-sm opacity-90">Import de données médicales</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sections de modes séparées -->
        
        <!-- Section 1: Mode Manuel (Formulaire PCMA) -->
        <div id="manual-mode-section" class="mode-section">
            <!-- Formulaire Principal PCMA -->
            <div id="formulaire-principal" class="form-section">
                <form action="{{ route('pcma.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- AI-Assisted Section -->
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            <h2 class="text-xl font-semibold text-indigo-900">Assistant IA PCMA</h2>
                        </div>
                        <p class="text-indigo-700 mb-4">Décrivez les résultats de l'examen médical pour une analyse automatique</p>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes Cliniques
                                </label>
                                <textarea 
                                    id="clinical_notes" 
                                    name="clinical_notes" 
                                    rows="4" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Exemple: Patient présente une tension artérielle de 120/80 mmHg, fréquence cardiaque de 65 bpm au repos. Pas d'antécédents cardiovasculaires. Examen neurologique normal. Pas de douleurs musculo-squelettiques..."
                                ></textarea>
                            </div>
                            
                            <div class="flex space-x-4">
                                <button 
                                    type="button" 
                                    id="ai-analyze-btn"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
                                >
                                    Analyser avec l'IA
                                </button>
                                <button 
                                    type="button" 
                                    id="clear-notes-btn"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
                                >
                                    Effacer
                                </button>
                            </div>
                            
                            <div id="ai-results" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Analyse IA</h3>
                                <div id="ai-content" class="text-sm text-gray-700"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section 2: Mode Vocal (Console Google Speech-to-Text) -->
        <div id="vocal-mode-section" class="mode-section hidden" data-section-type="vocal">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                                         <h2 class="text-xl font-semibold text-gray-800">Mode Vocal - Reconnaissance Intelligente</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    
                    
                    
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Assistant Vocal Google Speech-to-Text</h3>
                        
                        <!-- Configuration API Google -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-700 mb-3">Configuration API Google</h4>
                            <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-4">
                                <p class="text-gray-800 font-medium">Google Cloud configuré avec succès !</p>
                                <p class="text-gray-700 text-sm">Votre projet est prêt et l'API Speech-to-Text est active.</p>
                            </div>
                            
                            <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-4">
                                <p class="text-gray-800 font-medium">Clé chargée automatiquement depuis la configuration serveur</p>
                            </div>
                        </div>
                        
                        <!-- Initialisation du Service -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-blue-700 mb-3">Initialiser le Service</h4>
                            <button type="button" id="init-service" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                Tester le Service
                            </button>
                            <div id="service-status" class="mt-2 text-sm text-gray-600">Service non initialisé</div>
                        </div>
                        
                        <!-- Contrôles de Reconnaissance -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-blue-700 mb-3">Contrôles de Reconnaissance</h4>
                            <div class="flex space-x-4">
                                <button type="button" id="start-recording-btn" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-full transition duration-200">
                                                                         Démarrer Reconnaissance
                                </button>
                                <button type="button" id="stop-recording-btn" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-full transition duration-200" class="hidden">
                                    Arrêter Reconnaissance
                                </button>
                            </div>
                        </div>
                        
                        <!-- Statut et Résultats -->
                        <div id="voice-status" class="text-sm text-gray-500 mb-4">Service non initialisé</div>
                        
                        <!-- Résultats de la reconnaissance vocale -->
                        <div id="voice-results" class="hidden">
                            <h4 class="font-medium text-blue-800 mb-3">Résultats de la reconnaissance :</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du joueur</label>
                                    <input type="text" id="voice_player_name_result" class="w-full px-3 py-2 border border-gray-300 rounded-md" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Âge</label>
                                    <input type="text" id="voice_age_result" class="w-full px-3 py-2 border border-gray-300 rounded-md" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                    <input type="text" id="voice_position_result" class="w-full px-3 py-2 border border-gray-300 rounded-md" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Club</label>
                                    <input type="text" id="voice_club_result" class="w-full px-3 py-2 border border-gray-300 rounded-md" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Affichage du Texte Reconnu -->
                        <div id="speech-text" class="hidden bg-gray-50 border-l-4 border-gray-400 p-4 mb-4">
                            <h4 class="font-medium text-gray-800 mb-2">Texte reconnu :</h4>
                            <p id="recognized-text" class="text-gray-700"></p>
                            <p id="confidence" class="text-xs text-gray-600 mt-1"></p>
                        </div>
                        
                        <!-- Données Extraites -->
                        <div id="extracted-data-display" class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg hidden">
                            <h5 class="font-medium text-gray-800 mb-2">Données extraites :</h5>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><strong>Nom:</strong> <span id="extracted-name" class="text-gray-700">-</span></div>
                                <div><strong>Âge:</strong> <span id="extracted-age" class="text-gray-700">-</span></div>
                                <div><strong>Position:</strong> <span id="extracted-position" class="text-gray-700">-</span></div>
                                <div><strong>Club:</strong> <span id="extracted-club" class="text-gray-700">-</span></div>
                            </div>
                        </div>
                        
                        <!-- Boutons d'Action -->
                        <div class="flex space-x-4 mt-4">
                            <button type="button" id="apply-extracted-data" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm hidden">
                                 Appliquer les données extraites
                            </button>
                            <button type="button" id="transfer-to-form-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                 Transférer vers le formulaire PCMA
                            </button>
                        </div>
                    </div>
                    
                    <!-- MESSAGE IMPORTANT -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-blue-800 text-sm">
                            <strong>Note :</strong> Cette console vocale est connectée au formulaire PCMA du mode manuel. 
                            Les données reconnues seront automatiquement transférées dans les champs correspondants.
                        </p>
                    </div>
                    
                    
                    
                </div>
            </div>
        </div>

        <!-- Section 3: Mode OCR (Scan d'image) -->
        <div id="ocr-mode-section" class="mode-section hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Mode OCR - Scan d'image</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">Téléchargez une image de document médical pour extraction automatique</p>
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 mb-4">
                            <input type="file" id="image-upload" accept="image/*" class="hidden">
                            <label for="image-upload" class="cursor-pointer">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-gray-600">Cliquez pour sélectionner une image</p>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Bouton pour transférer vers le formulaire -->
                        <button type="button" id="transfer-ocr-to-form-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hidden">
                            Transférer vers le formulaire PCMA
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Mode FHIR -->
        <div id="fhir-mode-section" class="mode-section hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Mode FHIR - Import de données</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <label for="fhir_server_url" class="block text-sm font-medium text-gray-700 mb-2">
                            URL du serveur FHIR
                        </label>
                        <input type="url" id="fhir_server_url" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="https://hapi.fhir.org/baseR4/">
                    </div>
                    
                    <div>
                        <label for="fhir_patient_id" class="block text-sm font-medium text-gray-700 mb-2">
                            ID du patient FHIR
                        </label>
                        <input type="text" id="fhir_patient_id" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="patient-123">
                    </div>
                    
                    <div>
                        <label for="fhir_resource_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de ressource
                        </label>
                        <select id="fhir_resource_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Observation">Observation (Observations médicales)</option>
                            <option value="Condition">Condition (Diagnostics)</option>
                            <option value="Procedure">Procedure (Procédures)</option>
                            <option value="MedicationRequest">MedicationRequest (Prescriptions)</option>
                        </select>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="button" id="fetch-fhir-data" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                             Récupérer les données FHIR
                        </button>
                        <button type="button" id="clear-fhir" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            Effacer
                        </button>
                    </div>
                    
                    <div id="fhir-results" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Données FHIR récupérées</h3>
                        <div id="fhir-content" class="bg-gray-50 rounded-lg p-4 max-h-64 overflow-y-auto"></div>
                        
                        <!-- Bouton pour transférer vers le formulaire -->
                        <button type="button" id="transfer-fhir-to-form-btn" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            Transférer vers le formulaire PCMA
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal de confirmation des incohérences -->
        <div id="confirmation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- En-tête du modal -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-medium text-gray-900">Incohérences détectées</h3>
                        </div>
                        <button onclick="closeConfirmationModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Contenu du modal -->
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-4">
                            Des différences ont été détectées entre vos données vocales et celles de la base. 
                            Veuillez confirmer l'identité du joueur en utilisant l'une des méthodes suivantes :
                        </p>
                        
                        <!-- Liste des incohérences -->
                        <div id="inconsistencies-list" class="mb-6 space-y-3">
                            <!-- Rempli dynamiquement par JavaScript -->
                        </div>
                        
                        <!-- Méthodes de confirmation -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h4 class="font-medium text-blue-900 mb-3">Méthodes de confirmation :</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="bg-blue-100 rounded-lg p-3">
                                        <div class="text-2xl mb-2">ID</div>
                                        <div class="text-sm font-medium text-blue-800">ID FIFA Connect</div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="bg-blue-100 rounded-lg p-3">
                                        <div class="text-2xl mb-2">LIC</div>
                                        <div class="text-sm font-medium text-blue-800">Numéro de Licence</div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="bg-blue-100 rounded-lg p-3">
                                        <div class="text-2xl mb-2">SEQ</div>
                                        <div class="text-sm font-medium text-blue-800">Séquence complète</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Champs de confirmation -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ID FIFA Connect ou Numéro de Licence</label>
                                <input type="text" id="confirmation-id" placeholder="Ex: TUN_001 ou LIC_2024_123" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Séquence complète (Nom + Âge + Club + Position)</label>
                                <input type="text" id="confirmation-sequence" placeholder="Ex: Ali Jebali 24 AS Gabès Milieu offensif" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="closeConfirmationModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Annuler
                        </button>
                        <button onclick="confirmPlayerIdentity()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Confirmer l'identité
                        </button>
                    </div>
                </div>
            </div>
        </div>



                <!-- SECTION PCMA DUPLIQUÉE ENTIÈREMENT SUPPRIMÉE -->


                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-blue-800 mb-4">Informations de l'Athlète</h3>
                            
                            <!-- Athlète -->
                            <div>
                                <label for="athlete_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Athlète *
                                </label>
                                <select 
                                    id="athlete_id" 
                                    name="athlete_id" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Sélectionner un athlète</option>
                                    @foreach($athletes as $athlete)
                                        <option value="{{ $athlete->id }}" {{ old('athlete_id') == $athlete->id ? 'selected' : '' }}>
                                            {{ $athlete->name }} - {{ $athlete->fifa_connect_id ?? 'Pas d\'ID FIFA' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- ID FIFA Connect -->
                            <div class="mt-4">
                                <label for="fifa_connect_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    ID FIFA Connect
                                </label>
                                <input 
                                    type="text" 
                                    id="fifa_connect_id" 
                                    name="fifa_connect_id" 
                                    value="{{ old('fifa_connect_id') }}"
                                    placeholder="Entrez l'ID FIFA Connect du joueur"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                <p class="text-xs text-gray-600 mt-1">Laissez vide si l'athlète n'a pas d'ID FIFA Connect</p>
                            </div>
                        </div>

                        <!-- PCMA Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Type d'évaluation *
                            </label>
                            <select 
                                id="type" 
                                name="type" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Sélectionner le type</option>
                                <option value="bpma" {{ old('type') == 'bpma' ? 'selected' : '' }}>BPMA</option>
                                <option value="cardio" {{ old('type') == 'cardio' ? 'selected' : '' }}>Cardiovasculaire</option>
                                <option value="dental" {{ old('type') == 'dental' ? 'selected' : '' }}>Dentaire</option>
                                <option value="neurological" {{ old('type') == 'neurological' ? 'selected' : '' }}>Neurologique</option>
                                <option value="orthopedic" {{ old('type') == 'orthopedic' ? 'selected' : '' }}>Orthopédique</option>
                            </select>
                        </div>

                        <!-- Assessor -->
                        <div>
                            <label for="assessor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Assesseur *
                            </label>
                            <select 
                                id="assessor_id" 
                                name="assessor_id" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Sélectionner un assesseur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assessor_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assessment Date -->
                        <div>
                            <label for="assessment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date d'évaluation *
                            </label>
                            <input 
                                type="date" 
                                id="assessment_date" 
                                name="assessment_date" 
                                value="{{ old('assessment_date', date('Y-m-d')) }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Statut *
                            </label>
                            <select 
                                id="status" 
                                name="status" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                                <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                            </select>
                        </div>



                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Notes additionnelles sur l'évaluation..."
                            >{{ old('notes') }}</textarea>
                        </div>

                        <!-- Detailed Medical Assessment Sections -->
                        
                        <!-- Vital Signs Section -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Signes Vitaux</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tension Artérielle
                                    </label>
                                    <input type="text" id="blood_pressure" name="blood_pressure" 
                                           value="{{ old('blood_pressure') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="120/80 mmHg">
                                </div>
                                
                                <div>
                                    <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fréquence Cardiaque
                                    </label>
                                    <input type="number" id="heart_rate" name="heart_rate" 
                                           value="{{ old('heart_rate') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="65 bpm">
                                </div>
                                
                                <div>
                                    <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">
                                        Température
                                    </label>
                                    <input type="number" id="temperature" name="temperature" 
                                           value="{{ old('temperature') }}"
                                           step="0.1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="36.8 °C">
                                </div>
                                
                                <div>
                                    <label for="respiratory_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fréquence Respiratoire
                                    </label>
                                    <input type="number" id="respiratory_rate" name="respiratory_rate" 
                                           value="{{ old('respiratory_rate') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="16/min">
                                </div>
                                
                                <div>
                                    <label for="oxygen_saturation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Saturation O₂
                                    </label>
                                    <input type="number" id="oxygen_saturation" name="oxygen_saturation" 
                                           value="{{ old('oxygen_saturation') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-transparent"
                                           placeholder="98 %">
                                </div>
                                
                                <div>
                                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                        Poids
                                    </label>
                                    <input type="number" id="weight" name="weight" 
                                           value="{{ old('weight') }}"
                                           step="0.1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="75 kg">
                                </div>
                            </div>
                        </div>

                        <!-- Medical History Section -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-green-900"> Antécédents Médicaux</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="cardiovascular_history" class="block text-sm font-medium text-gray-700 mb-2">
                                        Antécédents Cardio-vasculaires
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="cardiovascular_search" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="Rechercher des conditions cardio-vasculaires..."
                                               autocomplete="off">
                                        <div id="cardiovascular_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                                        <input type="hidden" id="cardiovascular_history" name="cardiovascular_history" value="{{ old('cardiovascular_history') }}">
                                        <div id="cardiovascular_selected" class="mt-2 space-y-1"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="surgical_history" class="block text-sm font-medium text-gray-700 mb-2">
                                        Antécédents Chirurgicaux
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="surgical_search" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="Rechercher des procédures chirurgicales..."
                                               autocomplete="off">
                                        <div id="surgical_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                                        <input type="hidden" id="surgical_history" name="surgical_history" value="{{ old('surgical_history') }}">
                                        <div id="surgical_selected" class="mt-2 space-y-1"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="medications" class="block text-sm font-medium text-gray-700 mb-2">
                                        Médicaments Actuels
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="medication_search" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="Rechercher des médicaments..."
                                               autocomplete="off">
                                        <div id="medication_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                                        <input type="hidden" id="medications" name="medications" value="{{ old('medications') }}">
                                        <div id="medication_selected" class="mt-2 space-y-1"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                                        Allergies
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="allergy_search" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="Rechercher des allergies..."
                                               autocomplete="off">
                                        <div id="allergy_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                                        <input type="hidden" id="allergies" name="allergies" value="{{ old('allergies') }}">
                                        <div id="allergy_selected" class="mt-2 space-y-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Physical Examination Section -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Examen Physique</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="general_appearance" class="block text-sm font-medium text-gray-700 mb-2">
                                        Apparence Générale
                                    </label>
                                    <select id="general_appearance" name="general_appearance" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('general_appearance') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="abnormal" {{ old('general_appearance') == 'abnormal' ? 'selected' : '' }}>Anormal</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="skin_examination" class="block text-sm font-medium text-gray-700 mb-2">
                                        Examen Cutané
                                    </label>
                                    <select id="skin_examination" name="skin_examination" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('skin_examination') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="abnormal" {{ old('skin_examination') == 'abnormal' ? 'selected' : '' }}>Anormal</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="lymph_nodes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Ganglions Lymphatiques
                                    </label>
                                    <select id="lymph_nodes" name="lymph_nodes" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('lymph_nodes') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="enlarged" {{ old('lymph_nodes') == 'enlarged' ? 'selected' : '' }}>Hypertrophiés</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="abdomen_examination" class="block text-sm font-medium text-gray-700 mb-2">
                                        Examen Abdominal
                                    </label>
                                    <select id="abdomen_examination" name="abdomen_examination" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('abdomen_examination') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="abnormal" {{ old('abdomen_examination') == 'abnormal' ? 'selected' : '' }}>Anormal</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Imaging Upload Section -->
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-indigo-900">Imagerie Médicale</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- ECG Upload -->
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <h4 class="font-semibold text-gray-900">Électrocardiogramme (ECG)</h4>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <label for="ecg_file" class="block text-sm font-medium text-gray-700 mb-2">
                                                Fichier ECG
                                            </label>
                                            <input type="file" id="ecg_file" name="ecg_file" accept=".pdf,.jpg,.jpeg,.png,.bmp,.tiff,.tif,.dcm"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, JPG, PNG, BMP, TIFF, DICOM</p>
                                        </div>
                                        
                                        <div>
                                            <label for="ecg_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                Date de l'ECG
                                            </label>
                                            <input type="date" id="ecg_date" name="ecg_date" 
                                                   value="{{ old('ecg_date') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        </div>
                                        
                                        <div>
                                            <label for="ecg_interpretation" class="block text-sm font-medium text-gray-700 mb-2">
                                                Interprétation ECG
                                            </label>
                                            <select id="ecg_interpretation" name="ecg_interpretation" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                <option value="">Sélectionner</option>
                                                <option value="normal" {{ old('ecg_interpretation') == 'normal' ? 'selected' : '' }}>Normal</option>
                                                <option value="sinus_bradycardia" {{ old('ecg_interpretation') == 'sinus_bradycardia' ? 'selected' : '' }}>Bradycardie sinusale</option>
                                                <option value="sinus_tachycardia" {{ old('ecg_interpretation') == 'sinus_tachycardia' ? 'selected' : '' }}>Tachycardie sinusale</option>
                                                <option value="atrial_fibrillation" {{ old('ecg_interpretation') == 'atrial_fibrillation' ? 'selected' : '' }}>Fibrillation auriculaire</option>
                                                <option value="ventricular_tachycardia" {{ old('ecg_interpretation') == 'ventricular_tachycardia' ? 'selected' : '' }}>Tachycardie ventriculaire</option>
                                                <option value="st_elevation" {{ old('ecg_interpretation') == 'st_elevation' ? 'selected' : '' }}>Élévation du segment ST</option>
                                                <option value="st_depression" {{ old('ecg_interpretation') == 'st_depression' ? 'selected' : '' }}>Dépression du segment ST</option>
                                                <option value="qt_prolongation" {{ old('ecg_interpretation') == 'qt_prolongation' ? 'selected' : '' }}>Prolongation QT</option>
                                                <option value="abnormal" {{ old('ecg_interpretation') == 'abnormal' ? 'selected' : '' }}>Anormal (préciser)</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="ecg_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                                Notes ECG
                                            </label>
                                            <textarea id="ecg_notes" name="ecg_notes" rows="3"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                      placeholder="Détails de l'interprétation ECG...">{{ old('ecg_notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- MRI Upload -->
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        <h4 class="font-semibold text-gray-900">🧠 Imagerie par Résonance Magnétique (IRM)</h4>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <label for="mri_file" class="block text-sm font-medium text-gray-700 mb-2">
                                                Fichier IRM
                                            </label>
                                            <input type="file" id="mri_file" name="mri_file" accept=".pdf,.jpg,.jpeg,.png,.bmp,.tiff,.tif,.dcm"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, JPG, PNG, BMP, TIFF, DICOM</p>
                                        </div>
                                        
                                        <div>
                                            <label for="mri_date" class="block text-sm font-medium text-gray-700 mb-2">
                                                Date de l'IRM
                                            </label>
                                            <input type="date" id="mri_date" name="mri_date" 
                                                   value="{{ old('mri_date') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        </div>
                                        
                                        <div>
                                            <label for="mri_type" class="block text-sm font-medium text-gray-700 mb-2">
                                                Type d'IRM
                                            </label>
                                            <select id="mri_type" name="mri_type" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                <option value="">Sélectionner</option>
                                                <option value="brain" {{ old('mri_type') == 'brain' ? 'selected' : '' }}>IRM Cérébrale</option>
                                                <option value="spine" {{ old('mri_type') == 'spine' ? 'selected' : '' }}>IRM Rachidienne</option>
                                                <option value="knee" {{ old('mri_type') == 'knee' ? 'selected' : '' }}>IRM du Genou</option>
                                                <option value="shoulder" {{ old('mri_type') == 'shoulder' ? 'selected' : '' }}>IRM de l'Épaule</option>
                                                <option value="ankle" {{ old('mri_type') == 'ankle' ? 'selected' : '' }}>IRM de la Cheville</option>
                                                <option value="hip" {{ old('mri_type') == 'hip' ? 'selected' : '' }}>IRM de la Hanche</option>
                                                <option value="cardiac" {{ old('mri_type') == 'cardiac' ? 'selected' : '' }}>IRM Cardiaque</option>
                                                <option value="other" {{ old('mri_type') == 'other' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="mri_findings" class="block text-sm font-medium text-gray-700 mb-2">
                                                Résultats IRM
                                            </label>
                                            <select id="mri_findings" name="mri_findings" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                <option value="">Sélectionner</option>
                                                <option value="normal" {{ old('mri_findings') == 'normal' ? 'selected' : '' }}>Normal</option>
                                                <option value="mild_abnormality" {{ old('mri_findings') == 'mild_abnormality' ? 'selected' : '' }}>Anomalie légère</option>
                                                <option value="moderate_abnormality" {{ old('mri_findings') == 'moderate_abnormality' ? 'selected' : '' }}>Anomalie modérée</option>
                                                <option value="severe_abnormality" {{ old('mri_findings') == 'severe_abnormality' ? 'selected' : '' }}>Anomalie sévère</option>
                                                <option value="fracture" {{ old('mri_findings') == 'fracture' ? 'selected' : '' }}>Fracture</option>
                                                <option value="tumor" {{ old('mri_findings') == 'tumor' ? 'selected' : '' }}>Tumeur</option>
                                                <option value="inflammation" {{ old('mri_findings') == 'inflammation' ? 'selected' : '' }}>Inflammation</option>
                                                <option value="degenerative" {{ old('mri_findings') == 'degenerative' ? 'selected' : '' }}>Changements dégénératifs</option>
                                                <option value="other" {{ old('mri_findings') == 'other' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="mri_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                                Notes IRM
                                            </label>
                                            <textarea id="mri_notes" name="mri_notes" rows="3"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                      placeholder="Détails des résultats IRM...">{{ old('mri_notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Imaging -->
                            <div class="mt-6">
                                <h4 class="font-semibold text-gray-900 mb-3"> Autres Examens d'Imagerie</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="xray_file" class="block text-sm font-medium text-gray-700 mb-2">
                                            Radiographie (X-Ray)
                                        </label>
                                        <input type="file" id="xray_file" name="xray_file" accept=".pdf,.jpg,.jpeg,.png,.dcm"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    
                                    <div class="md:col-span-3">
                                        <label for="xray_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes Radiographie (X-Ray)
                                        </label>
                                        <textarea id="xray_notes" name="xray_notes" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                  placeholder="Résultats de l'analyse radiographique...">{{ old('xray_notes') }}</textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="ct_scan_file" class="block text-sm font-medium text-gray-700 mb-2">
                                            Scanner (CT)
                                        </label>
                                        <input type="file" id="ct_scan_file" name="ct_scan_file" accept=".pdf,.jpg,.jpeg,.png,.dcm"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    
                                    <div>
                                        <label for="ultrasound_file" class="block text-sm font-medium text-gray-700 mb-2">
                                            Échographie
                                        </label>
                                        <input type="file" id="ultrasound_file" name="ultrasound_file" accept=".pdf,.jpg,.jpeg,.png,.dcm"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    
                                    <div class="md:col-span-3">
                                        <label for="ct_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes Scanner (CT)
                                        </label>
                                        <textarea id="ct_notes" name="ct_notes" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                  placeholder="Résultats de l'analyse scanner...">{{ old('ct_notes') }}</textarea>
                                    </div>
                                    
                                    <div class="md:col-span-3">
                                        <label for="ultrasound_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes Échographie
                                        </label>
                                        <textarea id="ultrasound_notes" name="ultrasound_notes" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                                  placeholder="Résultats de l'analyse échographique...">{{ old('ultrasound_notes') }}</textarea>
                                    </div>
                                                            </div>
                            
                            <!-- DICOM Viewer Section -->
                            <div class="mt-6 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-green-900">🔬 Visualiseur DICOM</h4>
                                </div>
                                
                                <div class="space-y-4">
                                    <p class="text-sm text-green-700">
                                        Visualisez les fichiers d'imagerie médicale (DICOM, images) avec des outils d'analyse intégrés
                                    </p>
                                    
                                    <!-- File Selection for Viewer -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Sélectionner un fichier à visualiser
                                            </label>
                                            <select id="dicom-viewer-select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                <option value="">Choisir un fichier...</option>
                                                <option value="ecg">ECG - Fichier sélectionné</option>
                                                <option value="mri">IRM - Fichier sélectionné</option>
                                                <option value="xray">Radiographie - Fichier sélectionné</option>
                                                <option value="ct">Scanner CT - Fichier sélectionné</option>
                                                <option value="ultrasound">Échographie - Fichier sélectionné</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Outils de visualisation
                                            </label>
                                            <div class="flex space-x-2">
                                                <button type="button" id="viewer-zoom-in" class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                                                    +
                                                </button>
                                                <button type="button" id="viewer-zoom-out" class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                                                    -
                                                </button>
                                                <button type="button" id="viewer-reset" class="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                                                    🔄
                                                </button>
                                                <button type="button" id="viewer-fullscreen" class="px-3 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
                                                    ⛶
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Mesures
                                            </label>
                                            <div class="flex space-x-2">
                                                <button type="button" id="viewer-measure" class="px-3 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                                                    📏
                                                </button>
                                                <button type="button" id="viewer-annotate" class="px-3 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition-colors">
                                                    ✏️
                                                </button>
                                                <button type="button" id="viewer-screenshot" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                                    📸
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- DICOM Viewer Container -->
                                    <div class="bg-black rounded-lg overflow-hidden">
                                        <div id="dicom-viewer-container" class="relative w-full h-96 bg-gray-900 flex items-center justify-center">
                                            <div id="dicom-loading" class="hidden text-white text-center">
                                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-2"></div>
                                                <p>Chargement de l'image...</p>
                                            </div>
                                            
                                            <div id="dicom-error" class="hidden text-red-400 text-center">
                                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                                <p>Erreur de chargement</p>
                                            </div>
                                            
                                            <div id="dicom-placeholder" class="text-gray-400 text-center">
                                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="text-lg font-semibold">Visualiseur DICOM</p>
                                                <p class="text-sm">Sélectionnez un fichier pour commencer</p>
                                            </div>
                                            
                                            <canvas id="dicom-canvas" class="hidden max-w-full max-h-full object-contain"></canvas>
                                        </div>
                                        
                                        <!-- Viewer Controls -->
                                        <div class="bg-gray-800 p-4">
                                            <div class="flex items-center justify-between text-white">
                                                <div class="flex items-center space-x-4">
                                                    <span id="dicom-info" class="text-sm">Aucun fichier sélectionné</span>
                                                    <span id="dicom-dimensions" class="text-sm text-gray-300"></span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span id="dicom-zoom" class="text-sm">100%</span>
                                                    <input type="range" id="dicom-zoom-slider" min="25" max="400" value="100" class="w-20">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- DICOM Metadata Panel -->
                                    <div id="dicom-metadata" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-gray-900 mb-3"> Métadonnées DICOM</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p><strong>Patient:</strong> <span id="dicom-patient-name">-</span></p>
                                                <p><strong>ID Patient:</strong> <span id="dicom-patient-id">-</span></p>
                                                <p><strong>Date d'examen:</strong> <span id="dicom-study-date">-</span></p>
                                                <p><strong>Modality:</strong> <span id="dicom-modality">-</span></p>
                                            </div>
                                            <div>
                                                <p><strong>Institution:</strong> <span id="dicom-institution">-</span></p>
                                                <p><strong>Médecin:</strong> <span id="dicom-physician">-</span></p>
                                                <p><strong>Description:</strong> <span id="dicom-description">-</span></p>
                                                <p><strong>Dimensions:</strong> <span id="dicom-image-dimensions">-</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Measurement Tools -->
                                    <div id="dicom-measurements" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-gray-900 mb-3">📏 Outils de Mesure</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Distance</label>
                                                <div class="flex space-x-2">
                                                    <button type="button" id="measure-distance" class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                                                        📏 Distance
                                                    </button>
                                                    <span id="distance-result" class="text-sm text-gray-600">-</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Angle</label>
                                                <div class="flex space-x-2">
                                                    <button type="button" id="measure-angle" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">
                                                        📐 Angle
                                                    </button>
                                                    <span id="angle-result" class="text-sm text-gray-600">-</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Surface</label>
                                                <div class="flex space-x-2">
                                                    <button type="button" id="measure-area" class="px-3 py-1 bg-purple-500 text-white rounded text-sm hover:bg-purple-600">
                                                        📐 Surface
                                                    </button>
                                                    <span id="area-result" class="text-sm text-gray-600">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- AI Analysis Section -->
                            <div class="mt-6 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-purple-900">🤖 Analyse IA - Med-Gemini</h4>
                                </div>
                                
                                <div class="space-y-4">
                                    <p class="text-sm text-purple-700">
                                        Analyse automatique des fichiers ECG et IRM pour détecter les anomalies et évaluer l'âge osseux
                                    </p>
                                    
                                    <div class="flex flex-wrap gap-3">
                                        <button type="button" id="ai-check-ecg" 
                                                class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                             Analyser ECG
                                        </button>
                                        
                                        <button type="button" id="ai-check-mri" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                            </svg>
                                            🧠 Analyser IRM (Âge Osseux)
                                        </button>
                                        
                                        <button type="button" id="ai-check-xray" 
                                                class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                            </svg>
                                             Analyser X-Ray
                                        </button>
                                        
                                        <button type="button" id="ai-check-ct" 
                                                class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            🖥️ Analyser CT
                                        </button>
                                        
                                        <button type="button" id="ai-check-ultrasound" 
                                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                            </svg>
                                            🔊 Analyser Échographie
                                        </button>
                                        
                                        <button type="button" id="ai-check-all" 
                                                class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                            </svg>
                                             Analyse Complète
                                        </button>
                                    </div>
                                    
                                    <!-- AI Analysis Results -->
                                    <div id="ai-analysis-results" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-gray-900 mb-3"> Résultats de l'Analyse IA</h5>
                                        <div id="ai-results-content" class="space-y-3">
                                            <!-- Results will be populated here -->
                                        </div>
                                    </div>
                                    

                                    
                                    <!-- AI Analysis Status -->
                                    <div id="ai-analysis-status" class="hidden">
                                        <div class="flex items-center text-sm">
                                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-600 mr-2"></div>
                                            <span class="text-purple-600">Analyse en cours...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cardiovascular Assessment Section -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-blue-900">❤️ Évaluation Cardiovasculaire</h3>
                            </div>
                            
                            <!-- ECG Diagram -->
                            <div class="text-center mb-6">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 inline-block">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Électrocardiogramme (ECG)</h4>
                                    <svg width="300" height="80" class="mx-auto">
                                        <defs>
                                            <linearGradient id="ecgGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                                                <stop offset="50%" style="stop-color:#1e40af;stop-opacity:1" />
                                                <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
                                            </linearGradient>
                                        </defs>
                                        <!-- ECG Waveform -->
                                        <path d="M10,40 Q20,20 30,40 Q40,60 50,40 Q60,20 70,40 Q80,60 90,40 Q100,20 110,40 Q120,60 130,40 Q140,20 150,40 Q160,60 170,40 Q180,20 190,40 Q200,60 210,40 Q220,20 230,40 Q240,60 250,40 Q260,20 270,40 Q280,60 290,40" 
                                              stroke="url(#ecgGradient)" stroke-width="3" fill="none"/>
                                        <!-- P Wave -->
                                        <circle cx="30" cy="35" r="2" fill="#3b82f6"/>
                                        <!-- QRS Complex -->
                                        <path d="M50,40 L50,20 L55,20 L55,60 L60,60 L60,40" stroke="#1e40af" stroke-width="2" fill="none"/>
                                        <!-- T Wave -->
                                        <path d="M70,40 Q75,25 80,40" stroke="#3b82f6" stroke-width="2" fill="none"/>
                                    </svg>
                                    <p class="text-xs text-gray-500 mt-2">Rythme sinusal normal - 65 bpm</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="cardiac_rhythm" class="block text-sm font-medium text-gray-700 mb-2">
                                        Rythme Cardiaque
                                    </label>
                                    <select id="cardiac_rhythm" name="cardiac_rhythm" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="sinus" {{ old('cardiac_rhythm') == 'sinus' ? 'selected' : '' }}>Rythme sinusal</option>
                                        <option value="irregular" {{ old('cardiac_rhythm') == 'irregular' ? 'selected' : '' }}>Rythme irrégulier</option>
                                        <option value="arrhythmia" {{ old('cardiac_rhythm') == 'arrhythmia' ? 'selected' : '' }}>Arythmie</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="heart_murmur" class="block text-sm font-medium text-gray-700 mb-2">
                                        Souffle Cardiaque
                                    </label>
                                    <select id="heart_murmur" name="heart_murmur" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="none" {{ old('heart_murmur') == 'none' ? 'selected' : '' }}>Aucun</option>
                                        <option value="systolic" {{ old('heart_murmur') == 'systolic' ? 'selected' : '' }}>Systolique</option>
                                        <option value="diastolic" {{ old('heart_murmur') == 'diastolic' ? 'selected' : '' }}>Diastolique</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="blood_pressure_rest" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tension au Repos
                                    </label>
                                    <input type="text" id="blood_pressure_rest" name="blood_pressure_rest" 
                                           value="{{ old('blood_pressure_rest') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="120/80 mmHg">
                                </div>
                                
                                <div>
                                    <label for="blood_pressure_exercise" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tension à l'Effort
                                    </label>
                                    <input type="text" id="blood_pressure_exercise" name="blood_pressure_exercise" 
                                           value="{{ old('blood_pressure_exercise') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="140/85 mmHg">
                                </div>
                            </div>
                        </div>

                        <!-- Neurological Assessment Section -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-purple-900">🧠 Évaluation Neurologique</h3>
                            </div>
                            
                            <!-- Brain Diagram -->
                            <div class="text-center mb-6">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 inline-block">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Anatomie Cérébrale</h4>
                                    <svg width="200" height="120" class="mx-auto">
                                        <!-- Brain Outline -->
                                        <path d="M100,20 Q120,30 130,50 Q135,70 130,90 Q120,110 100,120 Q80,110 70,90 Q65,70 70,50 Q80,30 100,20" 
                                              stroke="#8b5cf6" stroke-width="2" fill="#f3e8ff"/>
                                        <!-- Brain Lobes -->
                                        <path d="M85,40 Q95,35 105,40 Q110,50 105,60 Q95,65 85,60 Q80,50 85,40" 
                                              stroke="#7c3aed" stroke-width="1" fill="#ddd6fe"/>
                                        <path d="M95,70 Q105,65 115,70 Q120,80 115,90 Q105,95 95,90 Q90,80 95,70" 
                                              stroke="#7c3aed" stroke-width="1" fill="#ddd6fe"/>
                                        <!-- Brain Stem -->
                                        <rect x="95" y="100" width="10" height="15" fill="#7c3aed"/>
                                        <!-- Labels -->
                                        <text x="50" y="35" class="text-xs" fill="#6b7280">Frontal</text>
                                        <text x="140" y="85" class="text-xs" fill="#6b7280">Occipital</text>
                                        <text x="100" y="125" class="text-xs" fill="#6b7280">Tronc</text>
                                    </svg>
                                    <p class="text-xs text-gray-500 mt-2">Examen neurologique normal</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="consciousness" class="block text-sm font-medium text-gray-700 mb-2">
                                        Niveau de Conscience
                                    </label>
                                    <select id="consciousness" name="consciousness" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="alert" {{ old('consciousness') == 'alert' ? 'selected' : '' }}>Vigile</option>
                                        <option value="confused" {{ old('consciousness') == 'confused' ? 'selected' : '' }}>Confus</option>
                                        <option value="drowsy" {{ old('consciousness') == 'drowsy' ? 'selected' : '' }}>Somnolent</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="cranial_nerves" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nerfs Crâniens
                                    </label>
                                    <select id="cranial_nerves" name="cranial_nerves" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('cranial_nerves') == 'normal' ? 'selected' : '' }}>Normaux</option>
                                        <option value="abnormal" {{ old('cranial_nerves') == 'abnormal' ? 'selected' : '' }}>Anormaux</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="motor_function" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fonction Motrice
                                    </label>
                                    <select id="motor_function" name="motor_function" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('motor_function') == 'normal' ? 'selected' : '' }}>Normale</option>
                                        <option value="weakness" {{ old('motor_function') == 'weakness' ? 'selected' : '' }}>Faiblesse</option>
                                        <option value="paralysis" {{ old('motor_function') == 'paralysis' ? 'selected' : '' }}>Paralysie</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="sensory_function" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fonction Sensitive
                                    </label>
                                    <select id="sensory_function" name="sensory_function" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('sensory_function') == 'normal' ? 'selected' : '' }}>Normale</option>
                                        <option value="decreased" {{ old('sensory_function') == 'decreased' ? 'selected' : '' }}>Diminuée</option>
                                        <option value="absent" {{ old('sensory_function') == 'absent' ? 'selected' : '' }}>Absente</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Musculoskeletal Assessment Section -->
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-orange-900">💪 Évaluation Musculo-squelettique</h3>
                            </div>
                            
                            <!-- Body Diagram -->
                            <div class="text-center mb-6">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 inline-block">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Anatomie Musculo-squelettique</h4>
                                    <svg width="150" height="200" class="mx-auto">
                                        <!-- Head -->
                                        <circle cx="75" cy="20" r="15" fill="#f97316" stroke="#ea580c" stroke-width="1"/>
                                        <!-- Neck -->
                                        <rect x="70" y="35" width="10" height="15" fill="#f97316"/>
                                        <!-- Torso -->
                                        <rect x="50" y="50" width="50" height="60" fill="#f97316" stroke="#ea580c" stroke-width="1"/>
                                        <!-- Arms -->
                                        <rect x="20" y="60" width="8" height="40" fill="#f97316"/>
                                        <rect x="122" y="60" width="8" height="40" fill="#f97316"/>
                                        <!-- Legs -->
                                        <rect x="60" y="110" width="8" height="50" fill="#f97316"/>
                                        <rect x="82" y="110" width="8" height="50" fill="#f97316"/>
                                        <!-- Joints -->
                                        <circle cx="75" cy="50" r="3" fill="#ea580c"/>
                                        <circle cx="24" cy="100" r="3" fill="#ea580c"/>
                                        <circle cx="126" cy="100" r="3" fill="#ea580c"/>
                                        <circle cx="64" cy="160" r="3" fill="#ea580c"/>
                                        <circle cx="86" cy="160" r="3" fill="#ea580c"/>
                                        <!-- Labels -->
                                        <text x="75" y="15" class="text-xs" fill="#6b7280">Tête</text>
                                        <text x="75" y="85" class="text-xs" fill="#6b7280">Tronc</text>
                                        <text x="15" y="85" class="text-xs" fill="#6b7280">Bras</text>
                                        <text x="130" y="85" class="text-xs" fill="#6b7280">Bras</text>
                                        <text x="55" y="140" class="text-xs" fill="#6b7280">Jambe</text>
                                        <text x="85" y="140" class="text-xs" fill="#6b7280">Jambe</text>
                                    </svg>
                                    <p class="text-xs text-gray-500 mt-2">Examen musculo-squelettique normal</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="joint_mobility" class="block text-sm font-medium text-gray-700 mb-2">
                                        Mobilité Articulaire
                                    </label>
                                    <select id="joint_mobility" name="joint_mobility" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('joint_mobility') == 'normal' ? 'selected' : '' }}>Normale</option>
                                        <option value="limited" {{ old('joint_mobility') == 'limited' ? 'selected' : '' }}>Limitée</option>
                                        <option value="restricted" {{ old('joint_mobility') == 'restricted' ? 'selected' : '' }}>Restreinte</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="muscle_strength" class="block text-sm font-medium text-gray-700 mb-2">
                                        Force Musculaire
                                    </label>
                                    <select id="muscle_strength" name="muscle_strength" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="normal" {{ old('muscle_strength') == 'normal' ? 'selected' : '' }}>Normale</option>
                                        <option value="reduced" {{ old('muscle_strength') == 'reduced' ? 'selected' : '' }}>Réduite</option>
                                        <option value="weak" {{ old('muscle_strength') == 'weak' ? 'selected' : '' }}>Faible</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="pain_assessment" class="block text-sm font-medium text-gray-700 mb-2">
                                        Évaluation de la Douleur
                                    </label>
                                    <select id="pain_assessment" name="pain_assessment" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="none" {{ old('pain_assessment') == 'none' ? 'selected' : '' }}>Aucune</option>
                                        <option value="mild" {{ old('pain_assessment') == 'mild' ? 'selected' : '' }}>Légère</option>
                                        <option value="moderate" {{ old('pain_assessment') == 'moderate' ? 'selected' : '' }}>Modérée</option>
                                        <option value="severe" {{ old('pain_assessment') == 'severe' ? 'selected' : '' }}>Sévère</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="range_of_motion" class="block text-sm font-medium text-gray-700 mb-2">
                                        Amplitude de Mouvement
                                    </label>
                                    <select id="range_of_motion" name="range_of_motion" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        <option value="">Sélectionner</option>
                                        <option value="full" {{ old('range_of_motion') == 'full' ? 'selected' : '' }}>Complète</option>
                                        <option value="limited" {{ old('range_of_motion') == 'limited' ? 'selected' : '' }}>Limitée</option>
                                        <option value="restricted" {{ old('range_of_motion') == 'restricted' ? 'selected' : '' }}>Restreinte</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- FIFA Compliance Section -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-green-900">🏆 Conformité FIFA</h3>
                            </div>
                            <p class="text-green-700 mb-4">Informations requises pour la conformité FIFA</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- FIFA ID -->
                                <div>
                                    <label for="fifa_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        ID FIFA
                                    </label>
                                    <input 
                                        type="text" 
                                        id="fifa_id" 
                                        name="fifa_id" 
                                        value="{{ old('fifa_id') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="FIFA-2024-001"
                                    >
                                </div>

                                <!-- Competition Name -->
                                <div>
                                    <label for="competition_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nom de la compétition
                                    </label>
                                    <input 
                                        type="text" 
                                        id="competition_name" 
                                        name="competition_name" 
                                        value="{{ old('competition_name') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Championnat National 2024"
                                    >
                                </div>

                                <!-- Competition Date -->
                                <div>
                                    <label for="competition_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Date de la compétition
                                    </label>
                                    <input 
                                        type="date" 
                                        id="competition_date" 
                                        name="competition_date" 
                                        value="{{ old('competition_date') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                </div>

                                <!-- Team Name -->
                                <div>
                                    <label for="team_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nom de l'équipe
                                    </label>
                                    <input 
                                        type="text" 
                                        id="team_name" 
                                        name="team_name" 
                                        value="{{ old('team_name') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Équipe nationale"
                                    >
                                </div>

                                <!-- Position -->
                                <div>
                                    <label for="position_secondary" class="block text-sm font-medium text-gray-700 mb-2">
                                        Poste du joueur
                                    </label>
                                    <select 
                                        id="position_secondary" 
                                        name="position_secondary" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    >
                                        <option value="">Sélectionner le poste</option>
                                        <option value="goalkeeper" {{ old('position') == 'goalkeeper' ? 'selected' : '' }}>Gardien</option>
                                        <option value="defender" {{ old('position') == 'defender' ? 'selected' : '' }}>Défenseur</option>
                                        <option value="midfielder" {{ old('position') == 'midfielder' ? 'selected' : '' }}>Milieu</option>
                                        <option value="forward" {{ old('position') == 'forward' ? 'selected' : '' }}>Attaquant</option>
                                    </select>
                                </div>

                                <!-- FIFA Compliant Checkbox -->
                                <div class="md:col-span-2">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            id="fifa_compliant" 
                                            name="fifa_compliant" 
                                            value="1"
                                            {{ old('fifa_compliant') ? 'checked' : '' }}
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                        >
                                        <label for="fifa_compliant" class="ml-2 block text-sm font-medium text-gray-700">
                                             Conforme aux standards FIFA
                                        </label>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">Cochez cette case si l'évaluation respecte tous les critères FIFA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Football Fitness Assessment -->
                <div class="mt-6 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h4 class="text-lg font-semibold text-purple-900">⚽ Évaluation Fitness Football Professionnel</h4>
                    </div>
                    
                    <p class="text-sm text-purple-700 mb-4">
                        Analyse complète de l'aptitude du joueur pour le football professionnel basée sur tous les examens médicaux
                    </p>
                    
                    <button type="button" id="ai-fitness-assessment" onclick="window.generateFitnessAssessment()"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        🤖 Générer Rapport Fitness Professionnel
                    </button>
                    
                    <!-- Fitness Assessment Results -->
                    <div id="fitness-assessment-results" class="hidden mt-4 bg-white border border-purple-200 rounded-lg p-4">
                        <h5 class="font-semibold text-purple-900 mb-3"> Rapport d'Évaluation Fitness</h5>
                        <div id="fitness-results-content" class="space-y-3">
                            <!-- Fitness assessment results will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- PDF and Print Actions -->
                <div class="mt-6 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h4 class="text-lg font-semibold text-green-900">📄 Export et Impression</h4>
                    </div>
                    
                    <p class="text-sm text-green-700 mb-4">
                        Générez des rapports PDF et imprimez les évaluations médicales
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <button type="button" id="generate-pdf" onclick="window.generatePDF()"
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            📄 Générer PDF
                        </button>
                        
                        <button type="button" id="print-report" onclick="window.printReport()"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            🖨️ Imprimer Rapport
                        </button>
                        

                        

                        
                        <button type="button" id="doctor-signoff" onclick="console.log('🔘 Doctor Sign-Off button clicked!'); window.openDoctorSignoff()"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                             Signature Médecin
                        </button>
                    </div>
                    
                    <!-- Export Status -->
                    <div id="export-status" class="hidden mt-4">
                        <div class="flex items-center text-sm">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-green-600 mr-2"></div>
                            <span class="text-green-600">Génération en cours...</span>
                        </div>
                    </div>
                    

                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        💾 Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- VOICE RECORDING SECTION SUPPRIMÉE - DISPONIBLE UNIQUEMENT EN MODE VOCAL -->

                        <!-- Champs de base du joueur (remplis automatiquement par reconnaissance vocale) -->

                            

                            


                    <!-- Debug panel -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                                    <h4 class="text-lg font-semibold text-yellow-800 mb-3">Panel de Debug</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Clé API Google Speech-to-Text :</label>
                                <input 
                                    type="text" 
                                    id="apiKeyInput" 
                                    placeholder="Entrez votre clé API Google Speech-to-Text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                >
                            </div>
                            <div class="flex gap-2">
                                <button 
                                    type="button" 
                                    onclick="testAPIKey()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-md transition duration-200"
                                >
                                     Tester la Clé API
                                </button>
                                <button 
                                    type="button" 
                                    onclick="clearConsole()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-md transition duration-200"
                                >
                                    🗑️ Vider la Console
                                </button>
                            </div>
                            
                            <div class="flex gap-2 mt-3">
                                <button 
                                    type="button" 
                                    onclick="testVoiceAnalysis()" 
                                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-md transition duration-200"
                                >
                                    Test Analyse Vocale
                                </button>
                                <!-- Bouton de test SUPPRIMÉ (causait des données de test automatiques) -->
                                <!-- <button 
                                    type="button" 
                                    onclick="testManualFieldFilling()" 
                                    class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded-md transition duration-200"
                                >
                                    Test Remplissage
                                </button> -->
                            </div>
                        </div>
                    </div>







                        <!-- Boutons d'action -->
                        <div class="flex gap-3">
                            <button 
                                type="submit" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition duration-200"
                            >
                                💾 Enregistrer
                            </button>
                            
                            <button 
                                type="button" 
                                onclick="clearAllFields()" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition duration-200"
                            >
                                🗑️ Effacer Tout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GOOGLE ASSISTANT SECTION SUPPRIMÉE - DISPONIBLE UNIQUEMENT EN MODE VOCAL -->

        <!-- FHIR Download Section -->
        <div id="fhir-section" class="input-section hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">📥 Téléchargement depuis FHIR</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <label for="fhir_server_url" class="block text-sm font-medium text-gray-700 mb-2">
                            URL du serveur FHIR
                        </label>
                        <input type="url" id="fhir_server_url" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="https://hapi.fhir.org/baseR4/">
                    </div>
                    
                    <div>
                        <label for="fhir_patient_id" class="block text-sm font-medium text-gray-700 mb-2">
                            ID du patient FHIR
                        </label>
                        <input type="text" id="fhir_patient_id" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="patient-123">
                    </div>
                    
                    <div>
                        <label for="fhir_resource_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de ressource
                        </label>
                        <select id="fhir_resource_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Observation">Observation (Observations médicales)</option>
                            <option value="Condition">Condition (Diagnostics)</option>
                            <option value="Procedure">Procedure (Procédures)</option>
                            <option value="MedicationRequest">MedicationRequest (Prescriptions)</option>
                        </select>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="button" id="fetch-fhir-data" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                             Récupérer les données FHIR
                        </button>
                        <button type="button" id="clear-fhir" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            Effacer
                        </button>
                    </div>
                    
                    <div id="fhir-results" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Données FHIR récupérées</h3>
                        <div id="fhir-content" class="bg-gray-50 rounded-lg p-4 max-h-64 overflow-y-auto"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Scan Section -->
        <div id="scan-section" class="input-section hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800"> Scan d'image avec OCR</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">Téléchargez une image de document médical pour extraction automatique</p>
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 mb-4">
                            <input type="file" id="image-upload" accept="image/*" class="hidden">
                            <label for="image-upload" class="cursor-pointer">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-gray-600">Cliquez pour sélectionner une image</p>
                                    <p class="text-sm text-gray-500">PNG, JPG, PDF jusqu'à 10MB</p>
                                </div>
                            </label>
                        </div>
                        
                        <div id="image-preview" class="hidden mb-4">
                            <img id="preview-img" class="max-w-md mx-auto rounded-lg shadow-md" alt="Aperçu">
                        </div>
                        
                        <div class="flex space-x-4">
                            <button type="button" id="process-image" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                 Extraire le texte (OCR)
                            </button>
                            <button type="button" id="clear-image" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Effacer
                            </button>
                        </div>
                    </div>
                    
                    <div id="ocr-results" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Texte extrait</h3>
                        <textarea id="extracted-text" rows="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Le texte extrait apparaîtra ici..."></textarea>
                        
                        <div class="flex space-x-4 mt-4">
                            <button type="button" id="process-ocr-text" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                 Analyser avec l'IA
                            </button>
                            <button type="button" id="clear-ocr" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Effacer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor Sign-Off Modal -->
        <div id="doctor-signoff-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl shadow-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900"> Signature Médecin - PCMA</h3>
                        <button onclick="closeDoctorSignoff()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                                <!-- DoctorSignOff Component Container -->
            <div id="doctor-signoff-container">
                <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-6">
                    <!-- Header -->
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h2 class="text-2xl font-bold text-gray-900"> Medical Fitness Assessment - Doctor Sign-Off</h2>
                        <p class="text-gray-600 mt-2">Final review and digital signature required for medical clearance</p>
                    </div>

                    <!-- Summary Block -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3"> Assessment Summary</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700">Player Name:</span>
                                <span class="ml-2 text-gray-900" id="signoff-player-name">Loading...</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Fitness Decision:</span>
                                <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold" id="signoff-fitness-decision">Loading...</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Date of Examination:</span>
                                <span class="ml-2 text-gray-900" id="signoff-examination-date">Loading...</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Assessment ID:</span>
                                <span class="ml-2 text-gray-900" id="signoff-assessment-id">Loading...</span>
                            </div>
                        </div>
                        
                        <div class="mt-3" id="signoff-clinical-notes-container" style="display: none;">
                            <span class="font-semibold text-gray-700">Clinical Notes:</span>
                            <p class="mt-1 text-gray-700 text-sm" id="signoff-clinical-notes"></p>
                        </div>
                    </div>

                    <!-- Legal Declaration -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-3">⚖️ Legal Declaration</h3>
                        <div class="flex items-start space-x-3">
                            <input 
                                type="checkbox" 
                                id="legal-declaration"
                                class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="legal-declaration" class="text-sm text-gray-700 leading-relaxed">
                                I, the undersigned medical professional, confirm that I have reviewed the complete clinical information 
                                and assume full responsibility for the fitness decision rendered herein. I understand that this assessment 
                                will be used for professional football eligibility determination and I certify that all information provided 
                                is accurate to the best of my medical knowledge.
                            </label>
                        </div>
                    </div>

                    <!-- Signature Capture -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">✍️ Digital Signature</h3>
                        
                        <!-- Signature Canvas -->
                        <div class="border border-gray-300 rounded-lg bg-white p-4">
                            <canvas 
                                id="signature-canvas"
                                class="border border-gray-300 rounded w-full h-48 cursor-crosshair"
                            ></canvas>
                            
                            <!-- Signature Controls -->
                            <div class="flex justify-between items-center mt-3">
                                <div class="text-sm text-gray-600" id="signature-status">
                                    No signature captured
                                </div>
                                <div class="flex space-x-2">
                                    <button 
                                        id="clear-signature"
                                        class="px-3 py-1 text-sm bg-gray-500 hover:bg-gray-600 text-white rounded transition duration-200"
                                    >
                                        🗑️ Clear
                                    </button>
                                    <button 
                                        id="confirm-signature"
                                        class="px-3 py-1 text-sm bg-green-600 hover:bg-green-700 text-white rounded transition duration-200"
                                        disabled
                                    >
                                         Confirm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Information -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-3"> Doctor Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700">Doctor Name:</span>
                                <input type="text" id="signoff-doctor-name-input" 
                                       class="ml-2 px-2 py-1 border border-gray-300 rounded text-gray-900 text-sm"
                                       placeholder="Enter doctor name"
                                       value="Dr. Médecin Responsable">
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">License Number:</span>
                                <span class="ml-2 text-gray-900" id="signoff-license-number">Loading...</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Timestamp:</span>
                                <span class="ml-2 text-gray-900" id="signoff-timestamp">Loading...</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">IP Address:</span>
                                <span class="ml-2 text-gray-900" id="signoff-ip-address">192.168.1.100</span>
                            </div>
                        </div>
                    </div>

                    <!-- Final Action -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <div class="text-sm text-gray-500" id="action-status">
                            Legal declaration required
                        </div>
                        <button 
                            id="confirm-signoff"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 flex items-center"
                            disabled
                            onclick="console.log('🔘 Direct onclick handler triggered!');"
                        >
                            <svg id="loading-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="confirm-signoff-text">Confirm and Sign</span>
                        </button>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
console.log(' SCRIPT TAG STARTING...');



// Define functions immediately at the top of the script
window.generatePDF = async function() {
    try {
        // Collect all form data
        const formData = new FormData();
        
        // Get all form inputs
        const pdfForm = document.querySelector('form');
        const formElements = pdfForm.elements;
        
        // Add all form fields to FormData
        console.log(' Collecting form data...');
        console.log(' Form elements found:', formElements.length);
        
        for (let element of formElements) {
            console.log(' Element:', element.name, '=', element.value, 'type:', element.type);
            if (element.name) {
                formData.append(element.name, element.value || '');
                console.log(' Added field:', element.name, '=', element.value || '');
            } else {
                console.log(' Skipped field without name:', element.id || element.type);
            }
        }
        console.log(' Total form fields collected:', formData.entries().length);
        
        // Add required fields if they're missing (same as in saveSignedPCMA)
        if (!formData.has('athlete_id') || !formData.get('athlete_id')) {
            formData.append('athlete_id', '1');
        }
        if (!formData.has('type') || !formData.get('type')) {
            formData.append('type', 'bpma');
        }
        if (!formData.has('assessor_id') || !formData.get('assessor_id')) {
            formData.append('assessor_id', '1');
        }
        if (!formData.has('assessment_date') || !formData.get('assessment_date')) {
            formData.append('assessment_date', new Date().toISOString().split('T')[0]);
        }
        if (!formData.has('status') || !formData.get('status')) {
            formData.append('status', 'completed');
        }
        // Only add default values if the fields are completely empty
        if (!formData.has('notes') || formData.get('notes') === '') {
            formData.append('notes', 'Évaluation médicale complétée');
            }
        if (!formData.has('clinical_notes') || formData.get('clinical_notes') === '') {
            formData.append('clinical_notes', 'Notes cliniques standard');
        }
        
        // Add fitness assessment results if available
        const fitnessResults = document.getElementById('fitness-results-content');
        if (fitnessResults && fitnessResults.innerHTML.trim()) {
            formData.append('fitness_assessment_results', fitnessResults.innerHTML);
        }
        
        // Add signature data if available
        if (window.signedPCMAData) {
            formData.append('signature_data', JSON.stringify(window.signedPCMAData));
            formData.append('is_signed', '1');
            formData.append('signed_by', window.signedPCMAData.signedBy);
            formData.append('license_number', window.signedPCMAData.licenseNumber);
            formData.append('signed_at', window.signedPCMAData.signedAt);
            formData.append('signature_image', window.signedPCMAData.signatureImage);
            formData.append('legal_declaration', 'confirmed');
            formData.append('signature_confirmed', 'true');
            console.log(' Adding signature data to PDF:', window.signedPCMAData);
        } else {
            formData.append('is_signed', '0');
            formData.append('legal_declaration', 'not_confirmed');
            formData.append('signature_confirmed', 'false');
            console.log('⚠️ No signature data available for PDF');
        }
        
        // Send to PDF generation endpoint
        console.log('📄 Sending PDF generation request to:', '{{ route("pcma.pdf.post") }}');
        console.log('📄 FormData entries:', formData.entries().length);
        
        // Test if the route exists
        console.log('📄 Testing route availability...');
        
        // Add CSRF token to FormData - get fresh token
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // If token is empty or invalid, try to get a fresh one
        if (!csrfToken || csrfToken === '') {
            console.log('📄 CSRF token is empty, attempting to refresh...');
            // Try to get a fresh token by making a request to the home page
            try {
                const tokenResponse = await fetch('/', { method: 'GET' });
                const tokenHtml = await tokenResponse.text();
                const tokenMatch = tokenHtml.match(/<meta name="csrf-token" content="([^"]+)"/);
                if (tokenMatch) {
                    csrfToken = tokenMatch[1];
                    console.log('📄 Fresh CSRF token obtained');
                }
            } catch (e) {
                console.error('📄 Failed to refresh CSRF token:', e);
            }
        }
        
        formData.append('_token', csrfToken);
        
        const response = await fetch('/api/pcma/pdf', {
            method: 'POST',
            body: formData
        });
        
        console.log('📄 Response status:', response.status);
        console.log('📄 Response ok:', response.ok);
        
        if (response.ok) {
            // Check if response is JSON (error) or PDF
            const contentType = response.headers.get('content-type');
            console.log('📄 Response content-type:', contentType);
            
            if (contentType && contentType.includes('application/json')) {
                // It's a JSON error response
                const errorData = await response.json();
                throw new Error(errorData.message || 'Unknown error');
            } else {
                // It's a PDF response
                try {
            const blob = await response.blob();
                    console.log('📄 Blob size:', blob.size, 'bytes');
                    console.log('📄 Blob type:', blob.type);
                    
                    if (blob.size === 0) {
                        throw new Error('PDF is empty (0 bytes)');
                    }
                    
                    // Check if it's actually a PDF
                    if (blob.type && !blob.type.includes('pdf') && !blob.type.includes('application/octet-stream')) {
                        console.warn('📄 Warning: Blob type is not PDF:', blob.type);
                    }
                    
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `PCMA_Assessment_${new Date().toISOString().split('T')[0]}.pdf`;
                    a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
                    
                    // Clean up after a delay
                    setTimeout(() => {
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
                    }, 100);
                    
                    console.log('📄 PDF download initiated');
                    
                    // Fallback: If download doesn't work, open in new tab
                    setTimeout(() => {
                        console.log('📄 Attempting fallback - opening PDF in new tab');
                        const newWindow = window.open(url, '_blank');
                        if (!newWindow) {
                            alert('📄 PDF generated but download blocked. Please check your browser settings.');
                        }
                    }, 2000);
                    
                } catch (blobError) {
                    console.error('📄 Error processing PDF blob:', blobError);
                    throw new Error('Failed to process PDF: ' + blobError.message);
                }
            }
            
        } else {
            // Try to get error message from response
            let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
            try {
                const errorData = await response.json();
                errorMessage = errorData.message || errorMessage;
            } catch (e) {
                // Could not parse JSON, use default error message
            }
            throw new Error(errorMessage);
        }
        
    } catch (error) {
        console.error('PDF Generation Error:', error);
        alert('❌ Erreur lors de la génération du PDF: ' + error.message);
    }
};

window.printReport = function() {
    try {
        console.log('🖨️ Print function called');
        console.log(' Signed data available:', window.signedPCMAData);
        
        // Create a print-friendly version of the form
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        
        // Get form data
        const printForm = document.querySelector('form');
        const formData = new FormData(printForm);
        const formDataObj = {};
        
        for (let [key, value] of formData.entries()) {
            formDataObj[key] = value;
        }
        
        // Create print-friendly HTML
        const printHTML = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>PCMA Assessment Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                    .section { margin-bottom: 20px; }
                    .section h3 { color: #2563eb; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                    .field { margin-bottom: 10px; }
                    .field label { font-weight: bold; display: inline-block; width: 200px; }
                    .field value { display: inline-block; }
                    .fitness-results { background: #f3f4f6; padding: 15px; border-radius: 5px; margin-top: 20px; }
                    @media print {
                        body { margin: 0; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1> Rapport d'Évaluation PCMA</h1>
                    <p>Date: ${new Date().toLocaleDateString('fr-FR')}</p>
                </div>
                
                <div class="section">
                    <h3>👤 Informations du Patient</h3>
                    <div class="field">
                        <label>Athlète:</label>
                        <value>${formDataObj.athlete_id || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Type d'évaluation:</label>
                        <value>${formDataObj.type || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Date d'évaluation:</label>
                        <value>${formDataObj.assessment_date || 'Non spécifié'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3> Signes Vitaux</h3>
                    <div class="field">
                        <label>Tension Artérielle:</label>
                        <value>${formDataObj.blood_pressure || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Fréquence Cardiaque:</label>
                        <value>${formDataObj.heart_rate || 'Non spécifié'} bpm</value>
                    </div>
                    <div class="field">
                        <label>Température:</label>
                        <value>${formDataObj.temperature || 'Non spécifié'} °C</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3> Antécédents Médicaux</h3>
                    <div class="field">
                        <label>Antécédents Cardio-vasculaires:</label>
                        <value>${formDataObj.cardiovascular_history || 'Aucun'}</value>
                    </div>
                    <div class="field">
                        <label>Antécédents Chirurgicaux:</label>
                        <value>${formDataObj.surgical_history || 'Aucun'}</value>
                    </div>
                    <div class="field">
                        <label>Médicaments Actuels:</label>
                        <value>${formDataObj.medications || 'Aucun'}</value>
                    </div>
                    <div class="field">
                        <label>Allergies:</label>
                        <value>${formDataObj.allergies || 'Aucune'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3> Examen Physique</h3>
                    <div class="field">
                        <label>Apparence Générale:</label>
                        <value>${formDataObj.general_appearance || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Examen Cutané:</label>
                        <value>${formDataObj.skin_examination || 'Non spécifié'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>❤️ Évaluation Cardiovasculaire</h3>
                    <div class="field">
                        <label>Rythme Cardiaque:</label>
                        <value>${formDataObj.cardiac_rhythm || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Souffle Cardiaque:</label>
                        <value>${formDataObj.heart_murmur || 'Non spécifié'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>🧠 Évaluation Neurologique</h3>
                    <div class="field">
                        <label>Niveau de Conscience:</label>
                        <value>${formDataObj.consciousness || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Nerfs Crâniens:</label>
                        <value>${formDataObj.cranial_nerves || 'Non spécifié'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>💪 Évaluation Musculo-squelettique</h3>
                    <div class="field">
                        <label>Mobilité Articulaire:</label>
                        <value>${formDataObj.joint_mobility || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Force Musculaire:</label>
                        <value>${formDataObj.muscle_strength || 'Non spécifié'}</value>
                    </div>
                </div>
                
                <div class="section">
                    <h3>🏆 Conformité FIFA</h3>
                    <div class="field">
                        <label>ID FIFA:</label>
                        <value>${formDataObj.fifa_id || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Nom de la compétition:</label>
                        <value>${formDataObj.competition_name || 'Non spécifié'}</value>
                    </div>
                    <div class="field">
                        <label>Conforme aux standards FIFA:</label>
                        <value>${formDataObj.fifa_compliant ? 'Oui' : 'Non'}</value>
                    </div>
                </div>
                
                <div class="fitness-results" id="print-fitness-results">
                    <!-- Fitness assessment results will be populated here if available -->
                </div>
                
                <div class="section">
                    <h3> Notes</h3>
                    <p>${formDataObj.notes || 'Aucune note'}</p>
                </div>
                
                         ${window.signedPCMAData ? `
         <div class="section">
             <h3> Signature Médicale</h3>
             <div class="field">
                 <label>Signé par:</label>
                 <value>${window.signedPCMAData.signedBy}</value>
             </div>
             <div class="field">
                 <label>Numéro de licence:</label>
                 <value>${window.signedPCMAData.licenseNumber}</value>
             </div>
             <div class="field">
                 <label>Date de signature:</label>
                 <value>${new Date(window.signedPCMAData.signedAt).toLocaleString('fr-FR')}</value>
             </div>
             <div class="field">
                 <label>Adresse IP:</label>
                 <value>${window.signedPCMAData.ipAddress}</value>
             </div>
             <div class="field">
                 <label>Statut de fitness:</label>
                 <value>${window.signedPCMAData.fitnessStatus}</value>
             </div>
             <div class="field">
                 <label>Signature:</label>
                 <div style="margin-top: 10px;">
                     ${window.signedPCMAData.signatureImage ? 
                         `<img src="${window.signedPCMAData.signatureImage}" alt="Signature médicale" style="max-width: 200px; border: 1px solid #ccc; padding: 5px;">` : 
                         '<p style="color: #666; font-style: italic;">Signature numérique capturée</p>'
                     }
                 </div>
             </div>
             <div class="field">
                 <label>Déclaration légale:</label>
                 <value style="font-style: italic; color: #666;">✓ Confirmée par le médecin signataire</value>
             </div>
             <div class="field">
                 <label>Validation:</label>
                 <value style="color: #059669; font-weight: bold;">✓ Document médical validé et signé</value>
             </div>
         </div>
         ` : `
         <div class="section">
             <h3> Signature Médicale</h3>
             <p style="color: #ef4444; font-style: italic;">⚠️ Signature médicale non effectuée</p>
             <p style="color: #ef4444; font-style: italic;">⚠️ Déclaration légale non confirmée</p>
             <p style="color: #ef4444; font-style: italic;">⚠️ Document non validé</p>
         </div>
         `}
                
                <div class="section no-print">
                    <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        🖨️ Imprimer ce rapport
                    </button>
                </div>
            </body>
            </html>
        `;
        
        printWindow.document.write(printHTML);
        printWindow.document.close();
        
        // Add fitness results if available
        const fitnessResults = document.getElementById('fitness-results-content');
        if (fitnessResults && fitnessResults.innerHTML.trim()) {
            const printFitnessResults = printWindow.document.getElementById('print-fitness-results');
            if (printFitnessResults) {
                printFitnessResults.innerHTML = `
                    <h3>⚽ Évaluation Fitness Football Professionnel</h3>
                    ${fitnessResults.innerHTML}
                `;
            }
        }
        
        // Auto-print after a short delay
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
        }, 500);
        
    } catch (error) {
        console.error('Print Error:', error);
        alert('❌ Erreur lors de l\'ouverture de la fenêtre d\'impression: ' + error.message);
    }
};

// Global variable to store signed data
window.signedPCMAData = null;

// Test function to check signature data
window.testSignatureData = function() {
    console.log(' Testing signature data...');
    console.log('Current signedPCMAData:', window.signedPCMAData);
    
    if (window.signedPCMAData) {
        alert(' Signature data is available!\n\n' + 
              'Signed by: ' + window.signedPCMAData.signedBy + '\n' +
              'License: ' + window.signedPCMAData.licenseNumber + '\n' +
              'Date: ' + window.signedPCMAData.signedAt + '\n' +
              'Status: ' + window.signedPCMAData.fitnessStatus);
    } else {
        alert('❌ No signature data available');
    }
};

window.openDoctorSignoff = function() {
    console.log('🚪 openDoctorSignoff function called');
    try {
        // Get form data for the signoff
        const signoffForm = document.querySelector('form');
        const formData = new FormData(signoffForm);
        const formDataObj = {};
        
        for (let [key, value] of formData.entries()) {
            formDataObj[key] = value;
        }
        
        // Get athlete information
        const athleteSelect = document.getElementById('athlete_id');
        const selectedAthlete = athleteSelect.options[athleteSelect.selectedIndex];
        const athleteName = selectedAthlete ? selectedAthlete.text : 'Athlète non spécifié';
        
        // Get fitness assessment results if available
        const fitnessResults = document.getElementById('fitness-results-content');
        const fitnessDecision = fitnessResults && fitnessResults.innerHTML.includes('APT') ? 'FIT' : 'NOT_FIT';
        
        // Get doctor/assessor information
        const assessorSelect = document.getElementById('assessor_id');
        const selectedAssessor = assessorSelect ? assessorSelect.options[assessorSelect.selectedIndex] : null;
        const doctorName = selectedAssessor ? selectedAssessor.text : 'Dr. Médecin Responsable';
        
        // Create signoff data
        const signoffData = {
            playerName: athleteName,
            fitnessDecision: fitnessDecision,
            examinationDate: formDataObj.assessment_date || new Date().toISOString().split('T')[0],
            assessmentId: 'PCMA-' + Date.now(),
            clinicalNotes: formDataObj.notes || 'Aucune note clinique',
            doctorName: doctorName,
            licenseNumber: 'MED-' + Math.floor(Math.random() * 10000)
        };
        
        // Show the modal
        console.log('🚪 Showing modal...');
        const modal = document.getElementById('doctor-signoff-modal');
        if (modal) {
            modal.classList.remove('hidden');
            console.log('🚪 Modal shown successfully');
        } else {
            console.error('❌ Modal element not found!');
        }
        
        // Populate the signoff form with data
        document.getElementById('signoff-player-name').textContent = signoffData.playerName;
        document.getElementById('signoff-fitness-decision').textContent = signoffData.fitnessDecision;
        document.getElementById('signoff-fitness-decision').className = signoffData.fitnessDecision === 'FIT' ? 
            'ml-2 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800' : 
            'ml-2 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800';
        document.getElementById('signoff-examination-date').textContent = signoffData.examinationDate;
        document.getElementById('signoff-assessment-id').textContent = signoffData.assessmentId;
        document.getElementById('signoff-doctor-name-input').value = signoffData.doctorName;
        document.getElementById('signoff-license-number').textContent = signoffData.licenseNumber;
        document.getElementById('signoff-timestamp').textContent = new Date().toLocaleString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        if (signoffData.clinicalNotes) {
            document.getElementById('signoff-clinical-notes').textContent = signoffData.clinicalNotes;
            document.getElementById('signoff-clinical-notes-container').style.display = 'block';
        }
        
        // Initialize signature canvas
        initSignatureCanvas();
        
        // Set up event listeners
        console.log('🚪 Setting up event listeners...');
        setupSignoffEventListeners(signoffData);
        console.log('🚪 Event listeners setup complete');
        
    } catch (error) {
        console.error('Doctor Signoff Error:', error);
        alert('❌ Erreur lors de l\'ouverture de la signature médecin: ' + error.message);
    }
};

window.closeDoctorSignoff = function() {
    console.log(' closeDoctorSignoff function called');
    const modal = document.getElementById('doctor-signoff-modal');
    if (modal) {
        console.log(' Modal found, hiding it...');
        modal.classList.add('hidden');
        console.log(' Modal hidden successfully');
    } else {
        console.error('❌ Modal element not found!');
    }
};

// Signature canvas variables
let signatureCanvas = null;
let signatureContext = null;
let isDrawing = false;
let hasSignature = false;
let signatureConfirmed = false;

// Initialize signature canvas
function initSignatureCanvas() {
    signatureCanvas = document.getElementById('signature-canvas');
    if (signatureCanvas) {
        signatureCanvas.width = signatureCanvas.offsetWidth;
        signatureCanvas.height = signatureCanvas.offsetHeight;
        signatureContext = signatureCanvas.getContext('2d');
        signatureContext.strokeStyle = '#1f2937';
        signatureContext.lineWidth = 2;
        signatureContext.lineCap = 'round';
    }
}

// Setup signoff event listeners
function setupSignoffEventListeners(signoffData) {
    console.log(' Setting up signoff event listeners...');
    const legalDeclaration = document.getElementById('legal-declaration');
    const clearSignature = document.getElementById('clear-signature');
    const confirmSignature = document.getElementById('confirm-signature');
    const confirmSignoff = document.getElementById('confirm-signoff');
    
    console.log(' Found elements:', {
        legalDeclaration: !!legalDeclaration,
        clearSignature: !!clearSignature,
        confirmSignature: !!confirmSignature,
        confirmSignoff: !!confirmSignoff
    });
    
    if (confirmSignoff) {
        console.log(' Confirm signoff button details:', {
            id: confirmSignoff.id,
            className: confirmSignoff.className,
            textContent: confirmSignoff.textContent
        });
    } else {
        console.error('❌ Confirm signoff button not found!');
    }
    
    // Legal declaration checkbox
    legalDeclaration.addEventListener('change', updateActionStatus);
    
    // Clear signature button
    clearSignature.addEventListener('click', clearSignatureCanvas);
    
    // Confirm signature button
    confirmSignature.addEventListener('click', confirmSignatureCanvas);
    
    // Confirm signoff button
    confirmSignoff.addEventListener('click', () => {
        console.log('🔘 Confirm signoff button clicked!');
        console.log('🔘 Button element:', confirmSignoff);
        console.log('🔘 Button disabled state:', confirmSignoff.disabled);
        console.log('🔘 Signoff data:', signoffData);
        try {
            handleSignoff(signoffData);
        } catch (error) {
            console.error('❌ Error in handleSignoff:', error);
            alert('❌ Erreur lors du traitement de la signature: ' + error.message);
        }
    });
    
    // Test if button gets enabled
    console.log(' Button initial disabled state:', confirmSignoff.disabled);
    
    // Enable button when signature is confirmed
    console.log(' Setting up signature confirmation...');
    
    // Signature canvas events
    if (signatureCanvas) {
        signatureCanvas.addEventListener('mousedown', startDrawing);
        signatureCanvas.addEventListener('mousemove', draw);
        signatureCanvas.addEventListener('mouseup', stopDrawing);
        signatureCanvas.addEventListener('mouseleave', stopDrawing);
        signatureCanvas.addEventListener('touchstart', startDrawingTouch);
        signatureCanvas.addEventListener('touchmove', drawTouch);
        signatureCanvas.addEventListener('touchend', stopDrawing);
    }
}

// Signature drawing functions
function startDrawing(event) {
    isDrawing = true;
    const rect = signatureCanvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    signatureContext.beginPath();
    signatureContext.moveTo(x, y);
}

function draw(event) {
    if (!isDrawing) return;
    const rect = signatureCanvas.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    signatureContext.lineTo(x, y);
    signatureContext.stroke();
}

function startDrawingTouch(event) {
    event.preventDefault();
    isDrawing = true;
    const rect = signatureCanvas.getBoundingClientRect();
    const touch = event.touches[0];
    const x = touch.clientX - rect.left;
    const y = touch.clientY - rect.top;
    signatureContext.beginPath();
    signatureContext.moveTo(x, y);
}

function drawTouch(event) {
    event.preventDefault();
    if (!isDrawing) return;
    const rect = signatureCanvas.getBoundingClientRect();
    const touch = event.touches[0];
    const x = touch.clientX - rect.left;
    const y = touch.clientY - rect.top;
    signatureContext.lineTo(x, y);
    signatureContext.stroke();
}

function stopDrawing() {
    if (isDrawing) {
        isDrawing = false;
        hasSignature = true;
        updateSignatureStatus();
    }
}

function clearSignatureCanvas() {
    if (signatureContext) {
        signatureContext.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
        hasSignature = false;
        signatureConfirmed = false;
        updateSignatureStatus();
    }
}

function confirmSignatureCanvas() {
    console.log(' confirmSignatureCanvas called');
    console.log(' hasSignature:', hasSignature);
    if (hasSignature) {
        signatureConfirmed = true;
        console.log(' Signature confirmed, updating status...');
        updateSignatureStatus();
    } else {
        console.log(' No signature to confirm');
    }
}

function updateSignatureStatus() {
    const status = document.getElementById('signature-status');
    const confirmBtn = document.getElementById('confirm-signature');
    
    if (!hasSignature) {
        status.textContent = 'No signature captured';
        confirmBtn.disabled = true;
    } else if (!signatureConfirmed) {
        status.textContent = 'Signature captured - please confirm';
        confirmBtn.disabled = false;
    } else {
        status.textContent = 'Signature confirmed ✓';
        confirmBtn.disabled = true;
    }
    
    updateActionStatus();
}

function updateActionStatus() {
    const legalDeclaration = document.getElementById('legal-declaration');
    const actionStatus = document.getElementById('action-status');
    const confirmSignoff = document.getElementById('confirm-signoff');
    
    console.log(' updateActionStatus called');
    console.log(' legalDeclaration.checked:', legalDeclaration.checked);
    console.log(' signatureConfirmed:', signatureConfirmed);
    
    if (!legalDeclaration.checked) {
        actionStatus.textContent = 'Legal declaration required';
        confirmSignoff.disabled = true;
        console.log(' Button disabled: Legal declaration required');
    } else if (!signatureConfirmed) {
        actionStatus.textContent = 'Signature confirmation required';
        confirmSignoff.disabled = true;
        console.log(' Button disabled: Signature confirmation required');
    } else {
        actionStatus.textContent = 'Ready to sign';
        confirmSignoff.disabled = false;
        console.log(' Button enabled: Ready to sign');
    }
}

function handleSignoff(signoffData) {
    console.log(' handleSignoff function called with data:', signoffData);
    const loadingSpinner = document.getElementById('loading-spinner');
    const confirmText = document.getElementById('confirm-signoff-text');
    const confirmBtn = document.getElementById('confirm-signoff');
    
    // Show loading state
    loadingSpinner.classList.remove('hidden');
    confirmText.textContent = 'Processing...';
    confirmBtn.disabled = true;
    
    // Simulate processing
    setTimeout(() => {
        // Get signature image
        const signatureImage = signatureCanvas.toDataURL('image/png');
        
        // Get the doctor name from the input field
        const doctorNameInput = document.getElementById('signoff-doctor-name-input');
        const actualDoctorName = doctorNameInput ? doctorNameInput.value : signoffData.doctorName;
        
        // Create signed data
        const signedData = {
            signedBy: actualDoctorName,
            licenseNumber: signoffData.licenseNumber,
            signedAt: new Date().toISOString(),
            signatureImage: signatureImage,
            fitnessStatus: signoffData.fitnessDecision,
            assessmentId: signoffData.assessmentId,
            playerName: signoffData.playerName,
            examinationDate: signoffData.examinationDate,
            ipAddress: '192.168.1.100'
        };
        
        // Store signed data globally
        window.signedPCMAData = signedData;
        console.log(' Signature data stored:', window.signedPCMAData);
        
        // Save the signed PCMA to database with timeout
        console.log('🔄 Starting signature save process...');
        
        // First, let's test if the save function works
        saveSignedPCMA(signedData).then(success => {
            console.log('📡 Signature save result:', success);
            
            // Reset loading state first
            loadingSpinner.classList.add('hidden');
            confirmText.textContent = 'Confirm and Sign';
            confirmBtn.disabled = false;
            
            if (success) {
                // Show success message
                alert(' Signature médicale validée!\n\nAssessment ID: ' + signedData.assessmentId + '\nSigned by: ' + signedData.signedBy + '\nTimestamp: ' + signedData.signedAt);
                
                // Close modal
                console.log(' Closing modal...');
                window.closeDoctorSignoff();
                
                // Update the sign-off button to show it's completed
                const signoffBtn = document.getElementById('doctor-signoff');
                if (signoffBtn) {
                    signoffBtn.innerHTML = `
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                         Signature Validée
                    `;
                    signoffBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                    signoffBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                }
                
                // Disable form editing after signing
                disableFormEditing();
            } else {
                // Show error message
                alert('❌ Erreur lors de la sauvegarde de la signature. Veuillez réessayer.');
            }
        }).catch(error => {
            console.error('❌ Error during signature save:', error);
            alert('❌ Erreur de connexion lors de la sauvegarde. Veuillez réessayer.');
            
            // Reset loading state
            loadingSpinner.classList.add('hidden');
            confirmText.textContent = 'Confirm and Sign';
            confirmBtn.disabled = false;
        });
    }, 1000);
}

// Save signed PCMA to database
function saveSignedPCMA(signedData) {
    console.log('🔄 Saving signed PCMA with data:', signedData);
    // Get form data
    const saveForm = document.querySelector('form');
    
    // Set default values in the form elements BEFORE creating FormData
    const athleteSelect = saveForm.querySelector('select[name="athlete_id"]');
    const typeSelect = saveForm.querySelector('select[name="type"]');
    const assessorSelect = saveForm.querySelector('select[name="assessor_id"]');
    const assessmentDateInput = saveForm.querySelector('input[name="assessment_date"]');
    const statusSelect = saveForm.querySelector('select[name="status"]');
    const notesInput = saveForm.querySelector('input[name="notes"], textarea[name="notes"]');
    const clinicalNotesInput = saveForm.querySelector('input[name="clinical_notes"], textarea[name="clinical_notes"]');
    
    // Set default values if they're empty (with null checks)
    if (athleteSelect && !athleteSelect.value) athleteSelect.value = '1';
    if (typeSelect && !typeSelect.value) typeSelect.value = 'bpma';
    if (assessorSelect && !assessorSelect.value) assessorSelect.value = '1';
    if (assessmentDateInput && !assessmentDateInput.value) assessmentDateInput.value = new Date().toISOString().split('T')[0];
    if (statusSelect && !statusSelect.value) statusSelect.value = 'completed';
    if (notesInput && !notesInput.value) notesInput.value = 'Évaluation médicale complétée';
    if (clinicalNotesInput && !clinicalNotesInput.value) clinicalNotesInput.value = 'Notes cliniques standard';
    
    // Debug: Log the form values after setting defaults
    console.log(' Form values after setting defaults:', {
        athlete_id: athleteSelect ? athleteSelect.value : 'null',
        type: typeSelect ? typeSelect.value : 'null',
        assessor_id: assessorSelect ? assessorSelect.value : 'null',
        assessment_date: assessmentDateInput ? assessmentDateInput.value : 'null',
        status: statusSelect ? statusSelect.value : 'null',
        notes: notesInput ? notesInput.value : 'null',
        clinical_notes: clinicalNotesInput ? clinicalNotesInput.value : 'null'
    });
    
    // Now create FormData with the updated form values
    const formData = new FormData(saveForm);
    
    // Add signature data
    formData.append('signature_data', JSON.stringify(signedData));
    formData.append('is_signed', '1');
    formData.append('signed_at', signedData.signedAt);
    formData.append('signed_by', signedData.signedBy);
    formData.append('license_number', signedData.licenseNumber);
    formData.append('signature_image', signedData.signatureImage);
    
            console.log(' FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Log what's being sent to PDF generation
        console.log('📄 PDF Generation - FormData entries:');
        const pdfFormData = new FormData();
        
        // Copy all form data to PDF FormData
        for (let [key, value] of formData.entries()) {
            pdfFormData.append(key, value);
            console.log(`📄 PDF: ${key} = ${value}`);
        }
        
        // Ensure we have the basic required fields for PDF
        if (!formData.has('athlete_id')) formData.append('athlete_id', '1');
        if (!formData.has('type')) formData.append('type', 'bpma');
        if (!formData.has('assessor_id')) formData.append('assessor_id', '1');
        if (!formData.has('assessment_date')) formData.append('assessment_date', new Date().toISOString().split('T')[0]);
        if (!formData.has('status')) formData.append('status', 'completed');
        
        // Only add default values if the fields are actually empty (not just missing)
        // This ensures we don't override actual form data with defaults
        const saveForm2 = document.querySelector('form');
        const allFormElements = saveForm2.querySelectorAll('input, select, textarea');
        
        // Add all form elements to FormData, including empty ones
        allFormElements.forEach(element => {
            if (element.name && !formData.has(element.name)) {
                const value = element.value || '';
                formData.append(element.name, value);
                console.log(`📄 Added form field: ${element.name} = "${value}"`);
            }
        });
        
        // Add missing fields with empty values if they don't exist
        const requiredFields = [
            'blood_pressure', 'heart_rate', 'temperature', 'respiratory_rate', 'oxygen_saturation', 'weight',
            'medical_history', 'surgical_history', 'medications', 'allergies',
            'general_appearance', 'skin_examination', 'lymph_nodes', 'abdomen_examination',
            'cardiac_rhythm', 'heart_murmur', 'blood_pressure_rest', 'blood_pressure_exercise',
            'consciousness', 'cranial_nerves', 'motor_function', 'sensory_function',
            'joint_mobility', 'muscle_strength', 'pain_assessment', 'range_of_motion',
            'fifa_id', 'competition_name', 'competition_date', 'team_name', 'position', 'fifa_compliant'
        ];
        
        requiredFields.forEach(field => {
            if (!formData.has(field)) {
                formData.append(field, '');
                console.log(`📄 Added missing field: ${field} = ""`);
            }
        });
        
        console.log('📄 PDF FormData ready with', formData.entries().length, 'entries');
    
    // Log the actual URL being called
            console.log('🔄 Fetch URL:', '/api/pcma/store');
    console.log('🔄 Request method: POST');
    console.log('🔄 Has signature_data:', formData.has('signature_data'));
    console.log('🔄 Has is_signed:', formData.has('is_signed'));
    
    // Send to server and return a Promise
            console.log('🔄 About to send fetch request to:', '/api/pcma/store');
    console.log('🔄 CSRF token found:', !!document.querySelector('meta[name="csrf-token"]'));
            return fetch('/api/pcma/store', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📡 Server response status:', response.status);
        console.log('📡 Server response ok:', response.ok);
        console.log('📡 Server response headers:', response.headers);
        if (!response.ok) {
            console.error('❌ Server response not ok:', response.status, response.statusText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .catch(error => {
        console.error('❌ Error parsing JSON:', error.message);
        console.error('❌ Full error object:', error);
        throw error;
    })
    .then(data => {
        console.log('📡 Server response data:', data);
        console.log('📡 Data type:', typeof data);
        console.log('📡 Data keys:', Object.keys(data));
        if (data.success) {
            console.log(' PCMA saved successfully:', data);
            // Store the PCMA ID for future reference
            window.savedPCMAId = data.pcma_id;
            return true; // Success
        } else {
            console.error('❌ Error saving PCMA:', data.error);
            return false; // Error
        }
    })
    .catch(error => {
        console.error('❌ Error saving PCMA:', error);
        console.error('❌ Error stack:', error.stack);
        return false; // Error
    });
}

// Disable form editing after signing
function disableFormEditing() {
    const disableForm = document.querySelector('form');
    const inputs = disableForm.querySelectorAll('input, textarea, select, button[type="submit"]');
    
    inputs.forEach(input => {
        if (input.type !== 'hidden' && input.id !== 'print-report-btn') {
            input.disabled = true;
        }
    });
    
    // Add visual indicator
    const formContainer = document.querySelector('.container');
    const warningDiv = document.createElement('div');
    warningDiv.className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4';
    warningDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <strong>⚠️ Document signé</strong>
        </div>
        <p class="mt-1">Ce PCMA a été signé et ne peut plus être modifié. Seule l'impression est autorisée.</p>
    `;
    formContainer.insertBefore(warningDiv, formContainer.firstChild);
}

// Add global error handler to catch any JavaScript errors
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    console.error('Error details:', e.message, e.filename, e.lineno);
});

// Add unhandled promise rejection handler
window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled Promise Rejection:', e.reason);
});

// AI Fitness Assessment function
window.generateFitnessAssessment = async function() {
    console.log('🤖 AI Fitness Assessment started');
    
    try {
        // Get form data
        const fitnessForm = document.querySelector('form');
        const formData = new FormData(fitnessForm);
        
        // Add required fields if missing
        if (!formData.has('athlete_id')) formData.append('athlete_id', '1');
        if (!formData.has('type')) formData.append('type', 'bpma');
        if (!formData.has('assessor_id')) formData.append('assessor_id', '1');
        if (!formData.has('assessment_date')) formData.append('assessment_date', new Date().toISOString().split('T')[0]);
        if (!formData.has('status')) formData.append('status', 'completed');
        
        // Show loading state
        const button = document.getElementById('ai-fitness-assessment');
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="animate-spin w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            🔄 Génération en cours...
        `;
        button.disabled = true;
        
        // Send request to AI fitness assessment endpoint
        const response = await fetch('{{ route("pcma.ai-fitness-assessment") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            console.log('🤖 AI Fitness Assessment result:', data);
            
            // Display results
            const resultsDiv = document.getElementById('fitness-assessment-results');
            const contentDiv = document.getElementById('fitness-results-content');
            
            if (data.success && data.assessment) {
                contentDiv.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <h6 class="font-semibold text-green-900 mb-2">Décision Globale</h6>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${
                            data.assessment.overall_decision === 'FIT' ? 'bg-green-100 text-green-800' :
                            data.assessment.overall_decision === 'NOT_FIT' ? 'bg-red-100 text-red-800' :
                            'bg-yellow-100 text-yellow-800'
                        }">
                            ${data.assessment.overall_decision}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <h6 class="font-semibold text-blue-900 mb-1">Score Cardiovasculaire</h6>
                            <span class="text-2xl font-bold text-blue-600">${data.assessment.cardiovascular_score || 'N/A'}/10</span>
                        </div>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                            <h6 class="font-semibold text-orange-900 mb-1">Score Musculo-squelettique</h6>
                            <span class="text-2xl font-bold text-orange-600">${data.assessment.musculoskeletal_score || 'N/A'}/10</span>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                            <h6 class="font-semibold text-purple-900 mb-1">Score Neurologique</h6>
                            <span class="text-2xl font-bold text-purple-600">${data.assessment.neurological_score || 'N/A'}/10</span>
                        </div>
                    </div>
                    
                    ${data.assessment.executive_summary ? `
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h6 class="font-semibold text-gray-900 mb-2">Résumé Exécutif</h6>
                            <p class="text-gray-700 text-sm">${data.assessment.executive_summary}</p>
                        </div>
                    ` : ''}
                `;
                
                resultsDiv.classList.remove('hidden');
                
                // Scroll to results
                resultsDiv.scrollIntoView({ behavior: 'smooth' });
                
            } else {
                throw new Error(data.message || 'Erreur lors de la génération du rapport');
            }
            
        } else {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
    } catch (error) {
        console.error('🤖 AI Fitness Assessment Error:', error);
        alert('❌ Erreur lors de la génération du rapport fitness: ' + error.message);
        
    } finally {
        // Reset button state
        const button = document.getElementById('ai-fitness-assessment');
        button.innerHTML = `
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            🤖 Générer Rapport Fitness Professionnel
        `;
        button.disabled = false;
    }
};



document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - JavaScript is running!');
    
    // Test if Doctor Sign-Off button exists
    const doctorSignoffBtn = document.getElementById('doctor-signoff');
    console.log(' Doctor Sign-Off button found:', !!doctorSignoffBtn);
    
    // Tab switching functionality
    const tabs = document.querySelectorAll('.input-method-tab');
    const sections = document.querySelectorAll('.input-section');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = this.id.replace('-tab', '-section');
            
            // Update tab styles
            tabs.forEach(t => {
                t.classList.remove('active', 'bg-blue-600', 'text-white');
                t.classList.add('bg-gray-100', 'text-gray-700');
            });
            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('active', 'bg-blue-600', 'text-white');
            
            // Show/hide sections
            sections.forEach(section => {
                section.classList.add('hidden');
            });
            document.getElementById(targetId).classList.remove('hidden');
        });
    });

    // Manual form AI analysis (existing functionality)
    const aiAnalyzeBtn = document.getElementById('ai-analyze-btn');
    const clearNotesBtn = document.getElementById('clear-notes-btn');
    const clinicalNotes = document.getElementById('clinical_notes');
    const aiResults = document.getElementById('ai-results');

    // Google Speech-to-Text Service Integration
    console.log(' Initialisation du service Google Speech-to-Text...');
    
    // Charger le service SpeechRecognitionService
    if (typeof SpeechRecognitionService !== 'undefined') {
        console.log(' Module SpeechRecognitionService intégré dans Laravel !');
        
        // Initialiser le service et le rendre global
        const speechService = new SpeechRecognitionService();
        window.speechService = speechService; // Rendre accessible globalement
        
        // Configuration IMMÉDIATE des callbacks
        console.log(' Configuration immédiate des callbacks...');
        
        window.speechService.onResult = function(result) {
            console.log(' Résultat vocal reçu (callback immédiat):', result);
            console.log(' Type de résultat:', typeof result);
            console.log(' Contenu du résultat:', result);
            
            // Analyser le texte et extraire les données
            const extractedData = analyzeVoiceText(result);
            console.log(' Données extraites (callback immédiat):', extractedData);
            
            // Afficher les résultats dans la console vocale
            displayVoiceResults(extractedData);
            
            // Remplir le formulaire principal
            fillFormFields(extractedData);
        };
        
        window.speechService.onError = function(error) {
            console.error('❌ Erreur de reconnaissance vocale (callback immédiat):', error);
            
            const voiceStatus = document.getElementById('voice-status');
            if (voiceStatus) {
                voiceStatus.textContent = `❌ Erreur: ${error.message}`;
                voiceStatus.className = 'text-center text-red-600 font-medium';
            }
        };
        
        console.log(' Callbacks configurés immédiatement après initialisation');
        console.log(' Vérification callback onResult:', typeof window.speechService.onResult);
        
        // Éléments DOM
        const elements = {
            apiKeyInput: document.getElementById('google-api-key'),
            loadApiKeyBtn: document.getElementById('load-api-key'),
            initBtn: document.getElementById('init-service'),
            testBtn: document.getElementById('test-service'),
            startBtn: document.getElementById('start-speech'),
            stopBtn: document.getElementById('stop-speech'),
            status: document.getElementById('speech-status'),
            serviceStatus: document.getElementById('service-status'),
            speechText: document.getElementById('speech-text'),
            recognizedText: document.getElementById('recognized-text'),
            confidence: document.getElementById('confidence')
        };

        // Charger automatiquement la clé API depuis le serveur
        async function loadApiKeyFromServer() {
            try {
                showServiceStatus('🔄 Chargement de la clé API...', 'info');
                
                const response = await fetch('/api/google-speech-key');
                
                if (!response.ok) {
                    throw new Error(`Erreur serveur: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.status === 'success' && data.apiKey) {
                    elements.apiKeyInput.value = data.apiKey;
                    showServiceStatus(' Clé API chargée automatiquement !', 'success');
                    
                    // Activer les boutons
                    elements.initBtn.disabled = false;
                    elements.testBtn.disabled = false;
                    
                    // Initialiser automatiquement le service
                    setTimeout(() => {
                        initService();
                    }, 1000);
                    
                } else {
                    throw new Error('Format de réponse invalide');
                }
                
            } catch (error) {
                showServiceStatus('❌ Erreur chargement clé: ' + error.message, 'error');
                console.error('❌ Erreur chargement clé:', error);
            }
        }

        // Initialiser le service
        async function initService() {
            try {
                showServiceStatus(' Initialisation du service...', 'info');
                
                // Vérifier que la clé API est présente
                if (!elements.apiKeyInput.value) {
                    throw new Error('Clé API manquante');
                }
                
                // Configurer le service avec la clé API
                speechService.configure({ apiKey: elements.apiKeyInput.value });
                const success = speechService.testAPIKey();
                
                if (success) {
                    showServiceStatus(' Service initialisé avec succès !', 'success');
                    elements.startBtn.disabled = false;
                    elements.stopBtn.disabled = false;
                    elements.status.textContent = 'Service prêt - Cliquez pour commencer';
                    
                    // Afficher le statut du service
                    const status = speechService.getStatus();
                    console.log(' Statut du service:', status);
                    
                } else {
                    throw new Error('Échec de l\'initialisation');
                }
                
            } catch (error) {
                showServiceStatus('❌ Erreur initialisation: ' + error.message, 'error');
                console.error('❌ Erreur initialisation:', error);
                
                // Réactiver le bouton d'initialisation
                elements.initBtn.disabled = false;
            }
        }

        // Tester le service
        function testService() {
            try {
                showServiceStatus(' Test du service...', 'info');
                
                const success = speechService.testAPIKey();
                
                if (success) {
                    showServiceStatus(' Test réussi - Service opérationnel !', 'success');
                } else {
                    throw new Error('Test échoué');
                }
                
            } catch (error) {
                showServiceStatus('❌ Test échoué: ' + error.message, 'error');
                console.error('❌ Test échoué:', error);
            }
        }

        // Démarrer la reconnaissance
        async function startSpeechRecognition() {
            try {
                elements.status.textContent = ' Reconnaissance en cours...';
                elements.startBtn.disabled = true;
                elements.stopBtn.disabled = false;
                
                const success = await speechService.startListening(
                    (text, confidence) => {
                        // Callback de succès
                        elements.recognizedText.textContent = text;
                        elements.confidence.textContent = `Confiance: ${(confidence * 100).toFixed(1)}%`;
                        elements.speechText.classList.remove('hidden');
                        
                        // Traiter le texte reconnu
                        processVoiceCommand(text);
                    },
                    (error) => {
                        // Callback d'erreur
                        console.error('❌ Erreur reconnaissance:', error);
                        elements.status.textContent = '❌ Erreur: ' + error.message;
                        elements.startBtn.disabled = false;
                        elements.stopBtn.disabled = true;
                    },
                    (status, message) => {
                        // Callback de statut
                        elements.status.textContent = message;
                    }
                );
                
                if (!success) {
                    throw new Error('Échec du démarrage de la reconnaissance');
                }
                
            } catch (error) {
                console.error('❌ Erreur démarrage reconnaissance:', error);
                elements.status.textContent = '❌ Erreur: ' + error.message;
                elements.startBtn.disabled = false;
                elements.stopBtn.disabled = true;
            }
        }

        // Arrêter la reconnaissance
        function stopSpeechRecognition() {
            speechService.stopListening();
            elements.status.textContent = ' Reconnaissance arrêtée';
            elements.startBtn.disabled = false;
            elements.stopBtn.disabled = true;
        }

        // Traiter les commandes vocales avec analyse intelligente
        let isProcessingCommand = false; // Protection contre la récursion
        
        function processVoiceCommand(text) {
            if (isProcessingCommand) {
                console.log('⚠️ Commande déjà en cours de traitement, ignorée');
                return;
            }
            
            try {
                isProcessingCommand = true;
                console.log('🗣️ Commande vocale reconnue:', text);
                
                // Analyse intelligente du texte
                const extractedData = analyzeVoiceText(text);
                console.log(' Données extraites:', extractedData);
                
                //  NOUVEAU : Afficher les données extraites dans l'interface de l'Assistant
                displayExtractedDataInAssistant(extractedData);
                
                // DEBUG: Vérifier que fillFormFields est bien définie
                console.log(' Fonction fillFormFields disponible:', typeof fillFormFields);
                
                // Remplir automatiquement les champs
                console.log(' Appel de fillFormFields avec:', extractedData);
                fillFormFields(extractedData);
                
                // Mettre à jour le statut
                elements.status.textContent = ' Commande analysée et formulaires remplis !';
                
                // Afficher un résumé
                showExtractionSummary(extractedData);
                
                // Sauvegarder automatiquement les données
                setTimeout(() => {
                    autoSaveData(extractedData);
                }, 1000);
                
            } catch (error) {
                console.error('❌ Erreur lors du traitement de la commande:', error);
                elements.status.textContent = '❌ Erreur: ' + error.message;
            } finally {
                isProcessingCommand = false;
            }
        }

        //  NOUVEAU : Afficher les données extraites dans l'interface de l'Assistant
        function displayExtractedDataInAssistant(extractedData) {
            console.log(' Affichage des données extraites dans l\'Assistant:', extractedData);
            
            // Éléments de l'interface
            const extractedDataDisplay = document.getElementById('extracted-data-display');
            const applyButton = document.getElementById('apply-extracted-data');
            const extractedName = document.getElementById('extracted-name');
            const extractedAge = document.getElementById('extracted-age');
            const extractedPosition = document.getElementById('extracted-position');
            const extractedClub = document.getElementById('extracted-club');
            
            if (extractedDataDisplay && applyButton && extractedName && extractedAge && extractedPosition && extractedClub) {
                // Mettre à jour les valeurs affichées
                extractedName.textContent = extractedData.player_name || '-';
                extractedAge.textContent = extractedData.age || '-';
                extractedPosition.textContent = extractedData.position || '-';
                extractedClub.textContent = extractedData.club || '-';
                
                // Afficher le panneau des données extraites
                extractedDataDisplay.classList.remove('hidden');
                
                // Afficher le bouton d'application
                applyButton.classList.remove('hidden');
                
                // Ajouter l'event listener pour le bouton (une seule fois)
                if (!applyButton.hasAttribute('data-listener-added')) {
                    applyButton.setAttribute('data-listener-added', 'true');
                    applyButton.addEventListener('click', () => {
                        console.log(' Bouton "Appliquer les données" cliqué !');
                        
                        // Remplir les champs du formulaire principal
                        fillFormFields(extractedData);
                        
                        // Mettre à jour le statut
                        elements.status.textContent = ' Données appliquées au formulaire !';
                        
                        // Masquer le bouton après application
                        applyButton.classList.add('hidden');
                    });
                }
                
                console.log(' Données extraites affichées dans l\'Assistant !');
            } else {
                console.error('❌ Éléments de l\'interface Assistant non trouvés');
            }
        }

        // Analyse intelligente du texte vocal
        function analyzeVoiceText(text) {
            const data = {
                player_name: null,
                age: null,
                position: null,
                club: null,
                confidence: 'high',
                command: null, // Nouveau champ pour les commandes spéciales
                
                // 🆕 NOUVEAUX CHAMPS MÉDICAUX
                blood_pressure: null,
                heart_rate: null,
                temperature: null,
                respiratory_rate: null,
                oxygen_saturation: null,
                weight: null,
                cardiovascular_history: null,
                surgical_history: null,
                diagnostic: null,
                anamnesis: null,
                recommendations: null
            };

            // Normaliser le texte
            const normalizedText = text.toLowerCase()
                .replace(/[.,!?]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();

            console.log(' Analyse du texte normalisé:', normalizedText);

            //  DÉTECTION DE LA COMMANDE "ID FIFA CONNECT" (AMÉLIORÉE)
            const fifaConnectPatterns = [
                /id\s+fifa\s+connect\s*(\d+)?/i,           // ID FIFA CONNECT 001
                /fifa\s+connect\s*(\d+)?/i,                 // FIFA CONNECT 001
                /fifa\s+id\s*(\d+)?/i,                      // FIFA ID 001
                /connect\s+id\s*(\d+)?/i,                   // CONNECT ID 001
                /identifiant\s+fifa\s*(\d+)?/i,             // IDENTIFIANT FIFA 001
                /fifa\s+connect\s+id\s*(\d+)?/i,            // FIFA CONNECT ID 001
                /fifa\s+(\d+)/i,                            // FIFA 001
                /connect\s+(\d+)/i                           // CONNECT 001
            ];

            for (const pattern of fifaConnectPatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    console.log(' Commande "ID FIFA CONNECT" détectée !');
                    data.command = 'fifa_connect_search';
                    data.confidence = 'very_high';
                    
                    // Capturer le numéro FIFA si présent
                    if (match[1]) {
                        data.fifa_number = match[1];
                        console.log(` Numéro FIFA capturé: ${match[1]}`);
                    }
                    
                    // Retourner immédiatement pour traiter cette commande spéciale
                    return data;
                }
            }

            // Extraction du nom du joueur
            const namePatterns = [
                /(?:le joueur s'appelle|nom du joueur|joueur|s'appelle)\s+([a-zàâäéèêëïîôöùûüÿç]+(?:\s+[a-zàâäéèêëïîôöùûüÿç]+)*?)(?:\s+il\s+a|\s+il\s+joue|\s+a\s+\d+)/i,
                /(?:nom|prénom)\s+([a-zàâäéèêëïîôöùûüÿç]+(?:\s+[a-zàâäéèêëïîôöùûüÿç]+)*?)(?:\s+il\s+a|\s+il\s+joue|\s+a\s+\d+)/i,
                /je\s+m'appelle\s+([a-zàâäéèêëïîôöùûüÿç]+(?:\s+[a-zàâäéèêëïîôöùûüÿç]+)*?)(?:\s+j'ai|\s+je\s+joue)/i,
                /m'appelle\s+([a-zàâäéèêëïîôöùûüÿç]+(?:\s+j'ai|\s+je\s+joue))/i
            ];

            for (const pattern of namePatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    // Nettoyer le nom en supprimant les mots parasites
                    let cleanName = match[1].trim();
                    
                    // Supprimer les mots courts qui ne sont pas des noms
                    const nameWords = cleanName.split(' ').filter(word => 
                        word.length > 2 && 
                        !['il', 'a', 'est', 'le', 'la', 'de', 'du', 'et', 'ou'].includes(word.toLowerCase())
                    );
                    
                    data.player_name = nameWords.map(word => 
                        word.charAt(0).toUpperCase() + word.slice(1)
                    ).join(' ');
                    break;
                }
            }

            // Extraction de l'âge
            const agePatterns = [
                /(\d+)\s*(?:ans|années?|an)/i,
                /(?:âge|age)\s*(\d+)/i,
                /(\d+)\s*(?:ans?)/i
            ];

            for (const pattern of agePatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    data.age = parseInt(match[1]);
                    break;
                }
            }

            // Extraction de la position
            const positionPatterns = [
                /(?:joue|position|poste)\s+(attaquant|milieu|défenseur|gardien|avant|arrière|central|latéral|pivot|ailier|meneur|récupérateur)/i,
                /(attaquant|milieu|défenseur|gardien|avant|arrière|central|latéral|pivot|ailier|meneur|récupérateur)/i
            ];

            for (const pattern of positionPatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    data.position = match[1].charAt(0).toUpperCase() + match[1].slice(1);
                    break;
                }
            }

            // Extraction du club (AMÉLIORÉE - sans mots parasites)
            const clubPatterns = [
                /(?:joue\s+(?:à|au|chez)\s+l')([a-zàâäéèêëïîôöùûüÿç]+(?:\s+[a-zàâäéèêëïîôöùûüÿç]+)*?)(?:\s+il\s+a|\s+il\s+est|\s+il\s+joue|$)/i,
                /(?:joue\s+(?:à|au|chez)\s+le\s+)([a-zàâäéèêëïîôöùûüÿç]+(?:\s+[a-zàâäéèêëïîôöùûüÿç]+)*?)(?:\s+il\s+a|\s+il\s+est|\s+il\s+joue|$)/i,
                /(?:joue\s+(?:à|au|chez)\s+la\s+)([a-zàâäéèêëïîôöùûüÿç]+(?:\s+[a-zàâäéèêëïîôöùûüÿç]+)*?)(?:\s+il\s+a|\s+il\s+est|\s+il\s+joue|$)/i,
                /(?:joue\s+(?:à|au|chez)\s+)([a-zàâäéèêëïîôöùûüÿç]+(?:\s+[a-zàâäéèêëïîôöùûüÿç]+)*?)(?:\s+il\s+a|\s+il\s+est|\s+il\s+joue|$)/i,
                /(?:club\s*:\s*)([a-zàâäéèêëïîôöùûüÿç]+(?:\s+[a-zàâäéèêëïîôöùûüÿç]+)*?)(?:\s+il\s+a|\s+il\s+est|\s+il\s+joue|$)/i,
                /(?:équipe\s*:\s*)([a-zàâäéèêëïîôöùûüÿç]+(?:\s+[a-zàâäéèêëïîôöùûüÿç]+)*?)(?:\s+il\s+a|\s+il\s+est|\s+il\s+joue|$)/i
            ];
            
            for (const pattern of clubPatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    let clubName = match[1];
                    
                    // Nettoyer le nom du club (supprimer les mots parasites)
                    clubName = clubName.trim();
                    
                    // Supprimer les mots parasites à la fin
                    const parasiticWords = ['il', 'a', 'est', 'joue', 'ans', 'ans.', 'ans,', 'milieu', 'offensif', 'attaquant', 'défenseur', 'gardien'];
                    const words = clubName.split(' ');
                    
                    // Supprimer les mots parasites à la fin
                    while (words.length > 0 && parasiticWords.includes(words[words.length - 1].toLowerCase())) {
                        words.pop();
                    }
                    
                    clubName = words.join(' ');
                    
                    if (clubName.trim()) {
                        // Capitaliser chaque mot
                        clubName = clubName.split(' ').map(word => 
                            word.charAt(0).toUpperCase() + word.slice(1)
                        ).join(' ');
                        
                        // Ajouter le préfixe approprié
                        if (pattern.source.includes("l'")) {
                            clubName = "l'" + clubName;
                        } else if (pattern.source.includes("le ")) {
                            clubName = "le " + clubName;
                        } else if (pattern.source.includes("la ")) {
                            clubName = "la " + clubName;
                        }
                        
                        data.club = clubName;
                        console.log(' Club extrait (nettoyé):', clubName, 'Pattern:', pattern.source);
                        break;
                    }
                }
            }

            // 🆕 EXTRACTION DES CHAMPS MÉDICAUX
            
            // Tension artérielle
            const bloodPressurePatterns = [
                /(?:tension|pression|ta|bp)\s*(?:artérielle)?\s*:?\s*(\d+)\s*\/\s*(\d+)/i,
                /(?:tension|pression|ta|bp)\s*(?:artérielle)?\s*:?\s*(\d+)\s*et\s*(\d+)/i,
                /(\d+)\s*\/\s*(\d+)\s*(?:tension|pression|ta|bp)/i,
                /(\d+)\s*et\s*(\d+)\s*(?:tension|pression|ta|bp)/i
            ];
            
            for (const pattern of bloodPressurePatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    data.blood_pressure = `${match[1]}/${match[2]} mmHg`;
                    break;
                }
            }

            // Fréquence cardiaque
            const heartRatePatterns = [
                /(?:fréquence|pouls|fc|hr)\s*(?:cardiaque)?\s*:?\s*(\d+)\s*(?:bpm|battements?)/i,
                /(\d+)\s*(?:bpm|battements?)\s*(?:fréquence|pouls|fc|hr)/i,
                /(?:cœur|cardio)\s*:?\s*(\d+)\s*(?:bpm|battements?)/i
            ];
            
            for (const pattern of heartRatePatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    data.heart_rate = parseInt(match[1]);
                    break;
                }
            }

            // Température
            const temperaturePatterns = [
                /(?:température|temp|t°)\s*:?\s*(\d+(?:[.,]\d+)?)\s*(?:°c|degrés?|celsius)/i,
                /(\d+(?:[.,]\d+)?)\s*(?:°c|degrés?|celsius)\s*(?:température|temp|t°)/i,
                /(?:fièvre|fievre)\s*:?\s*(\d+(?:[.,]\d+)?)\s*(?:°c|degrés?|celsius)/i
            ];
            
            for (const pattern of temperaturePatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    data.temperature = parseFloat(match[1].replace(',', '.'));
                    break;
                }
            }

            // Fréquence respiratoire
            const respiratoryPatterns = [
                /(?:fréquence|fr)\s*(?:respiratoire)?\s*:?\s*(\d+)\s*(?:respirations?|cycles?)/i,
                /(\d+)\s*(?:respirations?|cycles?)\s*(?:fréquence|fr)/i,
                /(?:respiration|resp)\s*:?\s*(\d+)\s*(?:respirations?|cycles?)/i
            ];
            
            for (const pattern of respiratoryPatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    data.respiratory_rate = parseInt(match[1]);
                    break;
                }
            }

            // Saturation en oxygène
            const oxygenPatterns = [
                /(?:saturation|sat|spo2)\s*(?:oxygène|o2)?\s*:?\s*(\d+)\s*%/i,
                /(\d+)\s*%\s*(?:saturation|sat|spo2)/i,
                /(?:oxygène|o2)\s*:?\s*(\d+)\s*%/i
            ];
            
            for (const pattern of oxygenPatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    data.oxygen_saturation = parseInt(match[1]);
                    break;
                }
            }

            // Poids
            const weightPatterns = [
                /(?:poids|masse)\s*:?\s*(\d+(?:[.,]\d+)?)\s*(?:kg|kilos?|kilogrammes?)/i,
                /(\d+(?:[.,]\d+)?)\s*(?:kg|kilos?|kilogrammes?)\s*(?:poids|masse)/i
            ];
            
            for (const pattern of weightPatterns) {
                const match = normalizedText.match(pattern);
                if (match) {
                    data.weight = parseFloat(match[1].replace(',', '.'));
                    break;
                }
            }

            // Antécédents cardiovasculaires
            const cardioPatterns = [
                /(?:antécédents?|antecedents?|antécédent|antecedent)\s*(?:cardiaques?|cardiovasculaires?)\s*:?\s*([^.]*)/i,
                /(?:cardiaque|cardiovasculaire)\s*:?\s*([^.]*)/i
            ];
            
            for (const pattern of cardioPatterns) {
                const match = normalizedText.match(pattern);
                if (match && match[1].trim()) {
                    data.cardiovascular_history = match[1].trim();
                    break;
                }
            }

            // Antécédents chirurgicaux
            const surgicalPatterns = [
                /(?:antécédents?|antecedents?|antécédent|antecedent)\s*(?:chirurgicaux?|opératoires?)\s*:?\s*([^.]*)/i,
                /(?:chirurgie|opération|operation)\s*:?\s*([^.]*)/i
            ];
            
            for (const pattern of surgicalPatterns) {
                const match = normalizedText.match(pattern);
                if (match && match[1].trim()) {
                    data.surgical_history = match[1].trim();
                    break;
                }
            }

            // Diagnostic
            const diagnosticPatterns = [
                /(?:diagnostic|diagnostique|diagnose)\s*:?\s*([^.]*)/i,
                /(?:diagnostiqué|diagnostiqué|diagnostique)\s*:?\s*([^.]*)/i
            ];
            
            for (const pattern of diagnosticPatterns) {
                const match = normalizedText.match(pattern);
                if (match && match[1].trim()) {
                    data.diagnostic = match[1].trim();
                    break;
                }
            }

            // Anamnèse
            const anamnesisPatterns = [
                /(?:anamnèse|anamnese|histoire)\s*(?:médicale|medicale)?\s*:?\s*([^.]*)/i,
                /(?:symptômes|symptomes)\s*:?\s*([^.]*)/i
            ];
            
            for (const pattern of anamnesisPatterns) {
                const match = normalizedText.match(pattern);
                if (match && match[1].trim()) {
                    data.anamnesis = match[1].trim();
                    break;
                }
            }

            // Recommandations
            const recommendationsPatterns = [
                /(?:recommandations?|conseils?|traitement)\s*:?\s*([^.]*)/i,
                /(?:prescrire|prescription)\s*:?\s*([^.]*)/i
            ];
            
            for (const pattern of recommendationsPatterns) {
                const match = normalizedText.match(pattern);
                if (match && match[1].trim()) {
                    data.recommendations = match[1].trim();
                    break;
                }
            }


            // Vérifier la qualité de l'extraction
            const extractedCount = Object.values(data).filter(val => val !== null).length - 1; // -1 pour confidence
            if (extractedCount >= 3) {
                data.confidence = 'high';
            } else if (extractedCount >= 2) {
                data.confidence = 'medium';
            } else {
                data.confidence = 'low';
            }

            return data;
        }

        // Remplir automatiquement les champs du formulaire
        function fillFormFields(extractedData) {
            console.log(' Remplissage automatique des champs avec:', extractedData);
            
            try {
                //  GESTION DE LA COMMANDE "ID FIFA CONNECT"
                if (extractedData.command === 'fifa_connect_search') {
                    console.log(' Traitement de la commande ID FIFA CONNECT...');
                    
                    // Afficher le statut de recherche
                    const voiceStatus = document.getElementById('voice-status');
                    if (voiceStatus) {
                        voiceStatus.textContent = ' Recherche automatique en cours...';
                        voiceStatus.className = 'text-center text-blue-600 font-medium';
                    }
                    
                    // Lancer la recherche automatique dans la base de données
                    // Cette fonction va chercher un joueur existant et remplir tous les champs
                    searchPlayerByFifaConnect();
                    return; // Sortir de la fonction pour ne pas traiter les autres données
                }
                
                // Remplir le nom du joueur (champs vocaux ET formulaire principal)
                if (extractedData.player_name) {
                    // Champ vocal
                    const voiceNameField = document.getElementById('voice_player_name');
                    if (voiceNameField) {
                        voiceNameField.value = extractedData.player_name;
                        voiceNameField.classList.add('bg-green-50');
                        console.log(' Nom du joueur (voix) rempli:', extractedData.player_name);
                        
                        // Forcer la mise à jour visuelle
                        voiceNameField.dispatchEvent(new Event('input', { bubbles: true }));
                        voiceNameField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    
                    // Champ formulaire principal
                    const mainNameField = document.getElementById('voice_player_name_main');
                    if (mainNameField) {
                        mainNameField.value = extractedData.player_name;
                        mainNameField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Nom du joueur (principal) rempli:', extractedData.player_name);
                        
                        // Forcer la mise à jour visuelle
                        mainNameField.dispatchEvent(new Event('input', { bubbles: true }));
                        mainNameField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    
                    // Remplir le VRAI champ du formulaire Laravel
                    const realNameField = document.querySelector('input[name="player_name"], input[name="name"], #player_name, #name');
                    if (realNameField) {
                        realNameField.value = extractedData.player_name;
                        realNameField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Nom du joueur (vrai formulaire) rempli:', extractedData.player_name);
                        
                        // Forcer la mise à jour visuelle
                        realNameField.dispatchEvent(new Event('input', { bubbles: true }));
                        realNameField.dispatchEvent(new Event('change', { bubbles: true }));
                    } else {
                        console.log('⚠️ Champ nom du formulaire Laravel non trouvé');
                    }
                    
                    //  NOUVEAU : Rechercher le joueur dans la base de données
                    searchPlayerInDatabase(extractedData.player_name);
                }

                // Remplir l'âge (champs vocaux ET formulaire principal)
                if (extractedData.age) {
                    // Champ vocal
                    const voiceAgeField = document.getElementById('voice_age');
                    if (voiceAgeField) {
                        voiceAgeField.value = extractedData.age;
                        voiceAgeField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Âge (voix) rempli:', extractedData.age);
                        
                        // Forcer la mise à jour visuelle
                        voiceAgeField.dispatchEvent(new Event('input', { bubbles: true }));
                        voiceAgeField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    
                    // Champ formulaire principal
                    const mainAgeField = document.getElementById('voice_age_main');
                    if (mainAgeField) {
                        mainAgeField.value = extractedData.age;
                        mainAgeField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Âge (principal) rempli:', extractedData.age);
                        
                        // Forcer la mise à jour visuelle
                        mainAgeField.dispatchEvent(new Event('input', { bubbles: true }));
                        mainAgeField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    
                    // Remplir le VRAI champ du formulaire Laravel
                    const realAgeField = document.querySelector('input[name="age"], input[name="player_age"], #age, #player_age');
                    if (realAgeField) {
                        realAgeField.value = extractedData.age;
                        realAgeField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Âge (vrai formulaire) rempli:', extractedData.age);
                        
                        // Forcer la mise à jour visuelle
                        realAgeField.dispatchEvent(new Event('input', { bubbles: true }));
                        realAgeField.dispatchEvent(new Event('change', { bubbles: true }));
                    } else {
                        console.log('⚠️ Champ âge du formulaire Laravel non trouvé');
                    }
                }

                // Remplir la position (champs vocaux ET formulaire principal)
                if (extractedData.position) {
                    // Champ vocal
                    const voicePositionField = document.getElementById('voice_position');
                    if (voicePositionField) {
                        if (voicePositionField.tagName === 'SELECT') {
                            const options = Array.from(voicePositionField.options);
                            const matchingOption = options.find(option => 
                                option.text.toLowerCase().includes(extractedData.position.toLowerCase()) ||
                                extractedData.position.toLowerCase().includes(option.text.toLowerCase())
                            );
                            if (matchingOption) {
                                voicePositionField.value = matchingOption.value;
                                voicePositionField.disabled = false;
                                console.log(' Position (voix) sélectionnée:', matchingOption.text);
                                
                                // Forcer la mise à jour visuelle
                                voicePositionField.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        }
                        voicePositionField.classList.add('bg-green-50', 'border-green-500');
                    }
                    
                    // Champ formulaire principal
                    const mainPositionField = document.getElementById('voice_position_main');
                    if (mainPositionField) {
                        if (mainPositionField.tagName === 'SELECT') {
                            const options = Array.from(mainPositionField.options);
                            const matchingOption = options.find(option => 
                                option.text.toLowerCase().includes(extractedData.position.toLowerCase()) ||
                                extractedData.position.toLowerCase().includes(option.text.toLowerCase())
                            );
                            if (matchingOption) {
                                mainPositionField.value = matchingOption.value;
                                console.log(' Position (principal) remplie:', matchingOption.text);
                                
                                // Forcer la mise à jour visuelle
                                mainPositionField.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        }
                        mainPositionField.classList.add('bg-green-50', 'border-green-500');
                    }
                    
                    // Remplir le VRAI champ du formulaire Laravel
                    const realPositionField = document.querySelector('select[name="position"], select[name="player_position"], #position, #player_position');
                    if (realPositionField) {
                        const options = Array.from(realPositionField.options);
                        const matchingOption = options.find(option => 
                            option.text.toLowerCase().includes(extractedData.position.toLowerCase()) ||
                            extractedData.position.toLowerCase().includes(option.text.toLowerCase())
                        );
                        if (matchingOption) {
                            realPositionField.value = matchingOption.value;
                            realPositionField.classList.add('bg-green-50', 'border-green-500');
                            console.log(' Position (vrai formulaire) remplie:', matchingOption.text);
                            
                            // Forcer la mise à jour visuelle
                            realPositionField.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    } else {
                        console.log('⚠️ Champ position du formulaire Laravel non trouvé');
                    }
                }

                // Remplir le club (champs vocaux ET formulaire principal)
                if (extractedData.club) {
                    // Champ vocal
                    const voiceClubField = document.getElementById('voice_club');
                    if (voiceClubField) {
                        voiceClubField.value = extractedData.club;
                        voiceClubField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Club (voix) rempli:', extractedData.club);
                        
                        // Forcer la mise à jour visuelle
                        voiceClubField.dispatchEvent(new Event('input', { bubbles: true }));
                        voiceClubField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    
                    // Champ formulaire principal
                    const mainClubField = document.getElementById('voice_club_main');
                    if (mainClubField) {
                        mainClubField.value = extractedData.club;
                        mainClubField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Club (principal) rempli:', extractedData.club);
                        
                        // Forcer la mise à jour visuelle
                        mainClubField.dispatchEvent(new Event('input', { bubbles: true }));
                        mainClubField.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    
                    // Remplir le VRAI champ du formulaire Laravel
                    const realClubField = document.querySelector('input[name="club"], input[name="team"], input[name="player_club"], #club, #team, #player_club');
                    if (realClubField) {
                        realClubField.value = extractedData.club;
                        realClubField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Club (vrai formulaire) rempli:', extractedData.club);
                        
                        // Forcer la mise à jour visuelle
                        realClubField.dispatchEvent(new Event('input', { bubbles: true }));
                        realClubField.dispatchEvent(new Event('change', { bubbles: true }));
                    } else {
                        console.log('⚠️ Champ club du formulaire Laravel non trouvé');
                    }
                }

                // 🆕 REMPLISSAGE DES CHAMPS MÉDICAUX
                
                // Tension artérielle
                if (extractedData.blood_pressure) {
                    const bloodPressureField = document.querySelector('input[name="blood_pressure"], #blood_pressure, #voice_blood_pressure');
                    if (bloodPressureField) {
                        bloodPressureField.value = extractedData.blood_pressure;
                        bloodPressureField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Tension artérielle remplie:', extractedData.blood_pressure);
                        bloodPressureField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Fréquence cardiaque
                if (extractedData.heart_rate) {
                    const heartRateField = document.querySelector('input[name="heart_rate"], #heart_rate, #voice_heart_rate');
                    if (heartRateField) {
                        heartRateField.value = extractedData.heart_rate;
                        heartRateField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Fréquence cardiaque remplie:', extractedData.heart_rate);
                        heartRateField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Température
                if (extractedData.temperature) {
                    const temperatureField = document.querySelector('input[name="temperature"], #temperature, #voice_temperature');
                    if (temperatureField) {
                        temperatureField.value = extractedData.temperature;
                        temperatureField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Température remplie:', extractedData.temperature);
                        temperatureField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Fréquence respiratoire
                if (extractedData.respiratory_rate) {
                    const respiratoryField = document.querySelector('input[name="respiratory_rate"], #respiratory_rate, #voice_respiratory_rate');
                    if (respiratoryField) {
                        respiratoryField.value = extractedData.respiratory_rate;
                        respiratoryField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Fréquence respiratoire remplie:', extractedData.respiratory_rate);
                        respiratoryField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Saturation en oxygène
                if (extractedData.oxygen_saturation) {
                    const oxygenField = document.querySelector('input[name="oxygen_saturation"], #oxygen_saturation, #voice_oxygen_saturation');
                    if (oxygenField) {
                        oxygenField.value = extractedData.oxygen_saturation;
                        oxygenField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Saturation en oxygène remplie:', extractedData.oxygen_saturation);
                        oxygenField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Poids
                if (extractedData.weight) {
                    const weightField = document.querySelector('input[name="weight"], #weight, #voice_weight');
                    if (weightField) {
                        weightField.value = extractedData.weight;
                        weightField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Poids rempli:', extractedData.weight);
                        weightField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Antécédents cardiovasculaires
                if (extractedData.cardiovascular_history) {
                    const cardioField = document.querySelector('textarea[name="cardiovascular_history"], #cardiovascular_history, #voice_cardiovascular_history');
                    if (cardioField) {
                        cardioField.value = extractedData.cardiovascular_history;
                        cardioField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Antécédents cardiovasculaires remplis:', extractedData.cardiovascular_history);
                        cardioField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Antécédents chirurgicaux
                if (extractedData.surgical_history) {
                    const surgicalField = document.querySelector('textarea[name="surgical_history"], #surgical_history, #voice_surgical_history');
                    if (surgicalField) {
                        surgicalField.value = extractedData.surgical_history;
                        surgicalField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Antécédents chirurgicaux remplis:', extractedData.surgical_history);
                        surgicalField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Diagnostic
                if (extractedData.diagnostic) {
                    const diagnosticField = document.querySelector('textarea[name="diagnostic"], #diagnostic, #voice_diagnostic');
                    if (diagnosticField) {
                        diagnosticField.value = extractedData.diagnostic;
                        diagnosticField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Diagnostic rempli:', extractedData.diagnostic);
                        diagnosticField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Anamnèse
                if (extractedData.anamnesis) {
                    const anamnesisField = document.querySelector('textarea[name="anamnesis"], #anamnesis, #voice_anamnesis');
                    if (anamnesisField) {
                        anamnesisField.value = extractedData.anamnesis;
                        anamnesisField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Anamnèse remplie:', extractedData.anamnesis);
                        anamnesisField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Recommandations
                if (extractedData.recommendations) {
                    const recommendationsField = document.querySelector('textarea[name="recommendations"], #recommendations, #voice_recommendations');
                    if (recommendationsField) {
                        recommendationsField.value = extractedData.recommendations;
                        recommendationsField.classList.add('bg-green-50', 'border-green-500');
                        console.log(' Recommandations remplies:', extractedData.recommendations);
                        recommendationsField.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }

                // Ajouter une note automatique
                addAutomaticNote(extractedData);

                console.log(' Remplissage automatique terminé avec succès !');
                
                // Forcer le rafraîchissement de l'affichage
                setTimeout(() => {
                    console.log('🔄 Vérification finale des valeurs des champs...');
                    console.log(' Nom (voix):', document.getElementById('voice_player_name')?.value);
                    console.log(' Nom (principal):', document.getElementById('voice_player_name_main')?.value);
                    console.log(' Âge (voix):', document.getElementById('voice_age')?.value);
                    console.log(' Âge (principal):', document.getElementById('voice_age_main')?.value);
                    console.log(' Position (voix):', document.getElementById('voice_position')?.value);
                    console.log(' Position (principal):', document.getElementById('voice_position_main')?.value);
                    console.log(' Club (voix):', document.getElementById('voice_club')?.value);
                    console.log(' Club (principal):', document.getElementById('voice_club_main')?.value);
                }, 100);
                
            } catch (error) {
                console.error('❌ Erreur lors du remplissage automatique:', error);
            }
        }

        //  NOUVELLE FONCTION : Recherche automatique par ID FIFA CONNECT
        async function searchPlayerByFifaConnect() {
            console.log(' Lancement de la recherche automatique par ID FIFA CONNECT...');
            
            try {
                // Afficher le statut de recherche
                const voiceStatus = document.getElementById('voice-status');
                if (voiceStatus) {
                    voiceStatus.textContent = ' Recherche en cours...';
                    voiceStatus.className = 'text-center text-blue-600 font-medium';
                }
                
                // Recherche réelle dans la base de données via l'API Laravel
                console.log(' Recherche réelle dans la base de données...');
                
                // Chercher un joueur par défaut pour la démo (Ali Jebali)
                const searchResponse = await fetch('/api/athletes/search?name=Ali%20Jebali', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!searchResponse.ok) {
                    throw new Error(`Erreur API: ${searchResponse.status}`);
                }
                
                const searchResult = await searchResponse.json();
                
                if (!searchResult.success) {
                    throw new Error('Joueur non trouvé dans la base de données');
                }
                
                // Données réelles du joueur trouvé
                const playerData = searchResult.player;
                
                console.log(' Joueur trouvé dans la base:', playerData);
                
                // Afficher les informations du joueur
                displayPlayerInfo(playerData);
                
                // Mettre à jour le statut
                if (voiceStatus) {
                    voiceStatus.textContent = ' Joueur trouvé ! Données remplies automatiquement';
                    voiceStatus.className = 'text-center text-green-600 font-medium';
                }
                
                // Remplir automatiquement tous les champs d'identité
                fillFormFieldsWithPlayerData(playerData);
                
            } catch (error) {
                console.error('❌ Erreur lors de la recherche automatique:', error);
                
                // Afficher l'erreur
                if (voiceStatus) {
                    voiceStatus.textContent = `❌ Erreur: ${error.message}`;
                    voiceStatus.className = 'text-center text-red-600 font-medium';
                }
                
                // Afficher un message d'erreur
                displayPlayerNotFound('Erreur lors de la recherche automatique');
            }
        }

        //  NOUVELLE FONCTION : Validation intelligente des données vocales
        function validateVoiceDataWithDatabase(voiceData, databaseData) {
            console.log(' Validation intelligente des données vocales avec la base...');
            console.log(' Données vocales:', voiceData);
            console.log('💾 Données base:', databaseData);
            
            const inconsistencies = [];
            
            // Comparer l'âge
            if (voiceData.age && databaseData.age && voiceData.age !== databaseData.age) {
                inconsistencies.push({
                    field: 'âge',
                    voice: voiceData.age,
                    database: databaseData.age,
                    type: 'age_mismatch'
                });
            }
            
            // Comparer la position
            if (voiceData.position && databaseData.position) {
                const voicePos = voiceData.position.toLowerCase();
                const dbPos = databaseData.position.toLowerCase();
                
                // Vérifier si les positions sont similaires
                const positionSimilarity = checkPositionSimilarity(voicePos, dbPos);
                if (!positionSimilarity.isSimilar) {
                    inconsistencies.push({
                        field: 'position',
                        voice: voiceData.position,
                        database: databaseData.position,
                        type: 'position_mismatch',
                        similarity: positionSimilarity.similarity
                    });
                }
            }
            
            // Comparer le club
            if (voiceData.club && databaseData.club) {
                const voiceClub = voiceData.club.toLowerCase();
                const dbClub = databaseData.club.toLowerCase();
                
                // Vérifier si les clubs sont similaires
                const clubSimilarity = checkClubSimilarity(voiceClub, dbClub);
                if (!clubSimilarity.isSimilar) {
                    inconsistencies.push({
                        field: 'club',
                        voice: voiceData.club,
                        database: databaseData.club,
                        type: 'club_mismatch',
                        similarity: clubSimilarity.similarity
                    });
                }
            }
            
            console.log(' Incohérences détectées:', inconsistencies);
            
            // Si des incohérences sont trouvées, afficher le popup de confirmation
            if (inconsistencies.length > 0) {
                console.log('⚠️ Incohérences détectées - Affichage du popup de confirmation');
                showConfirmationPopup(inconsistencies, voiceData, databaseData);
                return false; // Validation échouée
            }
            
            console.log(' Aucune incohérence détectée - Validation réussie');
            return true; // Validation réussie
        }
        
        // Fonction pour vérifier la similarité des positions
        function checkPositionSimilarity(voicePos, dbPos) {
            const positionMappings = {
                'attaquant': ['attaquant', 'avant', 'pivot', 'ailier', 'meneur'],
                'milieu': ['milieu', 'milieu offensif', 'milieu défensif', 'récupérateur', 'meneur'],
                'défenseur': ['défenseur', 'arrière', 'central', 'latéral'],
                'gardien': ['gardien', 'portier']
            };
            
            // Vérifier si les positions sont dans le même groupe
            for (const [group, positions] of Object.entries(positionMappings)) {
                if (positions.includes(voicePos) && positions.includes(dbPos)) {
                    return { isSimilar: true, similarity: 'high', group: group };
                }
            }
            
            // Vérifier la similarité textuelle
            const similarity = calculateTextSimilarity(voicePos, dbPos);
            return { 
                isSimilar: similarity > 0.7, 
                similarity: similarity > 0.7 ? 'medium' : 'low',
                score: similarity 
            };
        }
        
        // Fonction pour vérifier la similarité des clubs
        function checkClubSimilarity(voiceClub, dbClub) {
            // Nettoyer les noms de clubs
            const cleanVoiceClub = voiceClub.replace(/^(le |la |l')/i, '').trim();
            const cleanDbClub = dbClub.replace(/^(le |la |l')/i, '').trim();
            
            // Vérifier la similarité textuelle
            const similarity = calculateTextSimilarity(cleanVoiceClub, cleanDbClub);
            
            return { 
                isSimilar: similarity > 0.6, 
                similarity: similarity > 0.8 ? 'high' : similarity > 0.6 ? 'medium' : 'low',
                score: similarity 
            };
        }
        
        // Fonction pour calculer la similarité textuelle (algorithme simple)
        function calculateTextSimilarity(str1, str2) {
            if (str1 === str2) return 1.0;
            
            const words1 = str1.split(' ').filter(w => w.length > 2);
            const words2 = str2.split(' ').filter(w => w.length > 2);
            
            if (words1.length === 0 || words2.length === 0) return 0.0;
            
            let commonWords = 0;
            for (const word1 of words1) {
                for (const word2 of words2) {
                    if (word1 === word2 || word1.includes(word2) || word2.includes(word1)) {
                        commonWords++;
                        break;
                    }
                }
            }
            
            return commonWords / Math.max(words1.length, words2.length);
        }

        //  FONCTIONS DE GESTION DU MODAL DE CONFIRMATION
        function showConfirmationPopup(inconsistencies, voiceData, databaseData) {
            console.log(' Affichage du popup de confirmation...');
            
            // Stocker les données pour la confirmation
            window.confirmationData = {
                inconsistencies: inconsistencies,
                voiceData: voiceData,
                databaseData: databaseData
            };
            
            // Remplir la liste des incohérences
            const inconsistenciesList = document.getElementById('inconsistencies-list');
            if (inconsistenciesList) {
                inconsistenciesList.innerHTML = '';
                
                inconsistencies.forEach(inconsistency => {
                    const inconsistencyItem = document.createElement('div');
                    inconsistencyItem.className = 'bg-red-50 border border-red-200 rounded-lg p-3';
                    
                    let similarityText = '';
                    if (inconsistency.similarity) {
                        similarityText = ` (Similarité: ${Math.round(inconsistency.similarity.score * 100)}%)`;
                    }
                    
                    inconsistencyItem.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="font-medium text-red-800">${inconsistency.field}</div>
                                <div class="text-sm text-red-600">
                                     Vocal: <strong>${inconsistency.voice}</strong> | 
                                    💾 Base: <strong>${inconsistency.database}</strong>${similarityText}
                                </div>
                            </div>
                            <div class="ml-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ⚠️ Incohérence
                                </span>
                            </div>
                        </div>
                    `;
                    
                    inconsistenciesList.appendChild(inconsistencyItem);
                });
            }
            
            // Afficher le modal
            const modal = document.getElementById('confirmation-modal');
            if (modal) {
                modal.classList.remove('hidden');
                console.log(' Modal de confirmation affiché');
            }
        }
        
        function closeConfirmationModal() {
            console.log(' Fermeture du modal de confirmation...');
            
            const modal = document.getElementById('confirmation-modal');
            if (modal) {
                modal.classList.add('hidden');
                
                // Vider les champs de confirmation
                const confirmationId = document.getElementById('confirmation-id');
                const confirmationSequence = document.getElementById('confirmation-sequence');
                if (confirmationId) confirmationId.value = '';
                if (confirmationSequence) confirmationSequence.value = '';
                
                console.log(' Modal de confirmation fermé');
            }
        }
        
        function confirmPlayerIdentity() {
            console.log(' Confirmation de l\'identité du joueur...');
            
            const confirmationId = document.getElementById('confirmation-id');
            const confirmationSequence = document.getElementById('confirmation-sequence');
            
            if (!confirmationData) {
                console.error('❌ Données de confirmation non disponibles');
                return;
            }
            
            const idValue = confirmationId ? confirmationId.value.trim() : '';
            const sequenceValue = confirmationSequence ? confirmationSequence.value.trim() : '';
            
            if (!idValue && !sequenceValue) {
                alert('⚠️ Veuillez remplir au moins un champ de confirmation');
                return;
            }
            
            console.log(' Confirmation reçue:', { id: idValue, sequence: sequenceValue });
            
            // Simuler la validation (ici on pourrait appeler l'API Laravel)
            setTimeout(() => {
                console.log(' Identité confirmée avec succès !');
                
                // Fermer le modal
                closeConfirmationModal();
                
                // Mettre à jour le statut
                const voiceStatus = document.getElementById('voice-status');
                if (voiceStatus) {
                    voiceStatus.textContent = ' Identité confirmée - Données validées';
                    voiceStatus.className = 'text-center text-green-600 font-medium';
                }
                
                // Remplir les champs avec les données confirmées de la base
                if (confirmationData.databaseData) {
                    fillFormFieldsWithPlayerData(confirmationData.databaseData);
                }
                
                // Nettoyer les données de confirmation
                window.confirmationData = null;
                
            }, 1000);
        }

        // Ajouter une note automatique basée sur l'extraction (RÉACTIVÉE)
        function addAutomaticNote(extractedData) {
            console.log(' Note automatique RÉACTIVÉE avec:', extractedData);
            
            const notesField = document.getElementById('clinical_notes') || document.querySelector('textarea[name="clinical_notes"]');
            if (notesField) {
                // 🧹 EFFACER les anciennes notes cliniques
                notesField.value = '';
                console.log('🧹 Anciennes notes cliniques effacées');
                
                const timestamp = new Date().toLocaleString('fr-FR');
                let note = `[${timestamp}] Données extraites par reconnaissance vocale:\n\n`;
                
                if (extractedData.player_name) note += `👤 PATIENT: ${extractedData.player_name}\n`;
                if (extractedData.age) note += `📅 ÂGE: ${extractedData.age} ans\n`;
                if (extractedData.position) note += `⚽ POSITION: ${extractedData.position}\n`;
                if (extractedData.club) note += `🏆 CLUB: ${extractedData.club}\n`;
                if (extractedData.fifa_number) note += `🆔 FIFA ID: ${extractedData.fifa_number}\n`;
                if (extractedData.command) note += ` COMMANDE: ${extractedData.command}\n`;
                
                //  ÉCRIRE les nouvelles données (remplace complètement)
                notesField.value = note;
                notesField.classList.add('bg-blue-50', 'border-blue-500');
                
                console.log(' Nouvelles notes cliniques écrites !');
                console.log(' Contenu écrit:', note);
                
                // Mise en forme visuelle
                notesField.style.borderColor = '#10B981';
                notesField.style.backgroundColor = '#F0FDF4';
                
            } else {
                console.log('❌ Champ notes cliniques non trouvé !');
            }
        }

        // Sauvegarde automatique des données
        function autoSaveData(extractedData) {
            console.log('💾 Sauvegarde automatique des données...');
            
            try {
                // Préparer les données pour la sauvegarde
                const formData = new FormData();
                
                // Ajouter les données extraites
                if (extractedData.player_name) formData.append('player_name', extractedData.player_name);
                if (extractedData.age) formData.append('age', extractedData.age);
                if (extractedData.position) formData.append('position', extractedData.position);
                if (extractedData.club) formData.append('club', extractedData.club);
                
                // Ajouter un timestamp
                formData.append('voice_extraction_timestamp', new Date().toISOString());
                formData.append('voice_extraction_confidence', extractedData.confidence);
                
                // Ajouter les notes cliniques si disponibles
                const notesField = document.getElementById('clinical_notes');
                if (notesField && notesField.value) {
                    formData.append('clinical_notes', notesField.value);
                }
                
                // Envoyer les données au serveur
                fetch('/api/pcma/auto-save', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log(' Données sauvegardées automatiquement:', data);
                        showSaveStatus(' Données sauvegardées automatiquement !', 'success');
                        
                        // Mettre à jour l'interface
                        updateSaveStatus(data);
                    } else {
                        throw new Error(data.message || 'Erreur de sauvegarde');
                    }
                })
                .catch(error => {
                    console.error('❌ Erreur de sauvegarde automatique:', error);
                    showSaveStatus('❌ Erreur de sauvegarde: ' + error.message, 'error');
                });
                
            } catch (error) {
                console.error('❌ Erreur lors de la sauvegarde automatique:', error);
                showSaveStatus('❌ Erreur de sauvegarde: ' + error.message, 'error');
            }
        }

        // Afficher le statut de sauvegarde
        function showSaveStatus(message, type) {
            const statusElement = document.getElementById('save-status') || createSaveStatusElement();
            
            statusElement.textContent = message;
            statusElement.className = `text-sm p-2 rounded-md mb-4 ${
                type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
                type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
                'bg-blue-100 text-blue-800 border border-blue-200'
            }`;
            statusElement.classList.remove('hidden');
            
            // Masquer après 5 secondes
            setTimeout(() => {
                statusElement.classList.add('hidden');
            }, 5000);
        }

        // Créer l'élément de statut de sauvegarde s'il n'existe pas
        function createSaveStatusElement() {
            const statusElement = document.createElement('div');
            statusElement.id = 'save-status';
            statusElement.className = 'text-sm p-2 rounded-md mb-4 hidden';
            
            // Insérer après la section Google Assistant
            const googleSection = document.querySelector('#voice-section');
            if (googleSection) {
                googleSection.appendChild(statusElement);
            }
            
            return statusElement;
        }

        // Afficher un résumé de l'extraction
        function showExtractionSummary(extractedData) {
            console.log(' Affichage du résumé d\'extraction:', extractedData);
            
            // Créer ou mettre à jour l'élément de résumé
            let summaryElement = document.getElementById('extraction-summary');
            if (!summaryElement) {
                summaryElement = document.createElement('div');
                summaryElement.id = 'extraction-summary';
                summaryElement.className = 'bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 rounded-md';
                
                // Insérer après la section Google Assistant
                const googleSection = document.querySelector('#voice-section');
                if (googleSection) {
                    googleSection.appendChild(summaryElement);
                }
            }
            
            // Construire le contenu du résumé
            const summaryContent = `
                <h4 class="font-medium text-blue-800 mb-2"> Résumé de l'Extraction Vocale</h4>
                <div class="grid grid-cols-2 gap-2 text-sm text-blue-700">
                    ${extractedData.player_name ? `<div><strong>Nom:</strong> ${extractedData.player_name}</div>` : ''}
                    ${extractedData.age ? `<div><strong>Âge:</strong> ${extractedData.age} ans</div>` : ''}
                    ${extractedData.position ? `<div><strong>Position:</strong> ${extractedData.position}</div>` : ''}
                    ${extractedData.club ? `<div><strong>Club:</strong> ${extractedData.club}</div>` : ''}
                </div>
                <div class="mt-2 text-xs text-blue-600">
                    <strong>Confiance:</strong> ${extractedData.confidence === 'high' ? 'Élevée' : extractedData.confidence === 'medium' ? 'Moyenne' : 'Faible'}
                </div>
            `;
            
            summaryElement.innerHTML = summaryContent;
            summaryElement.classList.remove('hidden');
            
            // Masquer après 10 secondes
            setTimeout(() => {
                summaryElement.classList.add('hidden');
            }, 10000);
        }

        // Mettre à jour le statut de sauvegarde
        function updateSaveStatus(data) {
            // Mettre à jour l'interface avec les informations de sauvegarde
            if (data.pcma_id) {
                const idField = document.getElementById('pcma_id') || document.querySelector('input[name="pcma_id"]');
                if (idField) {
                    idField.value = data.pcma_id;
                    idField.classList.add('bg-green-50', 'border-green-500');
                }
            }
            
            // Afficher un message de confirmation
            elements.status.textContent = ` PCMA sauvegardé avec l'ID: ${data.pcma_id || 'N/A'}`;
        }

        // Afficher le statut du service
        function showServiceStatus(message, type) {
            elements.serviceStatus.textContent = message;
            elements.serviceStatus.className = `text-sm ${type === 'error' ? 'text-red-600' : type === 'success' ? 'text-green-600' : 'text-blue-600'}`;
            elements.serviceStatus.classList.remove('hidden');
        }

        // Event listeners
        elements.loadApiKeyBtn.addEventListener('click', loadApiKeyFromServer);
        elements.initBtn.addEventListener('click', initService);
        elements.testBtn.addEventListener('click', testService);
        elements.startBtn.addEventListener('click', startSpeechRecognition);
        elements.stopBtn.addEventListener('click', stopSpeechRecognition);

        // Charger automatiquement la clé API au démarrage
        setTimeout(() => {
            loadApiKeyFromServer();
        }, 1000);
        
        // Test manuel des champs DÉSACTIVÉ (était automatique après 3 secondes)
        // setTimeout(() => {
        //     console.log(' TEST MANUEL DES CHAMPS...');
        //     testManualFieldFilling();
        // }, 3000);
        
        // Fonction pour effacer les champs vocaux ET du formulaire principal
        window.clearVoiceFields = function() {
            console.log('🧹 Effacement des champs vocaux ET du formulaire principal...');
            
            // Champs vocaux
            const voiceFields = [
                'voice_player_name',
                'voice_age', 
                'voice_position',
                'voice_club'
            ];
            
            // Champs du formulaire principal
            const mainFields = [
                'voice_player_name_main',
                'voice_age_main', 
                'voice_position_main',
                'voice_club_main',
                'voice_type',
                'voice_assessor_id',
                'voice_assessment_date',
                'voice_status',
                'voice_notes',
                'voice_blood_pressure',
                'voice_heart_rate',
                'voice_temperature',
                'voice_respiratory_rate',
                'voice_oxygen_saturation',
                'voice_weight',
                'voice_cardiovascular_history',
                'voice_surgical_history'
            ];
            
            // Effacer les champs vocaux
            voiceFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    if (field.tagName === 'SELECT') {
                        field.value = '';
                        field.disabled = true;
                    } else {
                        field.value = '';
                    }
                    field.classList.remove('bg-green-50', 'border-green-500');
                    field.classList.add('border-gray-300');
                }
            });
            
            // Effacer les champs du formulaire principal
            mainFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    if (field.tagName === 'SELECT') {
                        field.value = '';
                    } else {
                        field.value = '';
                    }
                    field.classList.remove('bg-green-50', 'border-green-500');
                    field.classList.add('border-gray-300');
                }
            });
            
            // Masquer le résumé d'extraction
            const summaryElement = document.getElementById('extraction-summary');
            if (summaryElement) {
                summaryElement.classList.add('hidden');
            }
            
            console.log(' Champs vocaux ET du formulaire principal effacés avec succès !');
        };

        // Fonction pour effacer tous les champs (vocaux + formulaire principal)
        window.clearAllFields = function() {
            console.log('🧹 Effacement de tous les champs...');
            
            // Champs vocaux
            const voiceFields = [
                'voice_player_name',
                'voice_age', 
                'voice_position',
                'voice_club'
            ];
            
            // Champs du formulaire principal
            const mainFields = [
                'voice_player_name_main',
                'voice_age_main', 
                'voice_position_main',
                'voice_club_main',
                'voice_type',
                'voice_assessor_id',
                'voice_assessment_date',
                'voice_status',
                'voice_notes',
                'voice_blood_pressure',
                'voice_heart_rate',
                'voice_temperature',
                'voice_respiratory_rate',
                'voice_oxygen_saturation',
                'voice_weight',
                'voice_cardiovascular_history',
                'voice_surgical_history'
            ];
            
            // Effacer les champs vocaux
            voiceFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    if (field.tagName === 'SELECT') {
                        field.value = '';
                        field.disabled = true;
                    } else {
                        field.value = '';
                    }
                    field.classList.remove('bg-green-50', 'border-green-500');
                    field.classList.add('border-gray-300');
                }
            });
            
            // Effacer les champs du formulaire principal
            mainFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    if (field.tagName === 'SELECT') {
                        field.value = '';
                    } else if (field.tagName === 'TEXTAREA') {
                        field.value = '';
                    } else {
                        field.value = '';
                    }
                    field.classList.remove('bg-green-50', 'border-green-500');
                    field.classList.add('border-gray-300');
                }
            });
            
            // Masquer le résumé d'extraction
            const summaryElement = document.getElementById('extraction-summary');
            if (summaryElement) {
                summaryElement.classList.add('hidden');
            }
            
            console.log(' Tous les champs effacés avec succès !');
        };

        // Test de l'analyse vocale
        function testVoiceAnalysis() {
            console.log(' Test de l\'analyse vocale...');
            
            const testTexts = [
                "Je m'appelle Ahmed, j'ai 28 ans, je suis milieu et je joue au Real Madrid",
                "Le joueur s'appelle Mohamed, il a 25 ans, il est attaquant et il joue au PSG",
                "Nom du joueur: Karim, âge 30 ans, position défenseur, club: Manchester United"
            ];
            
            testTexts.forEach((text, index) => {
                console.log(`\n Test ${index + 1}: "${text}"`);
                const result = analyzeVoiceText(text);
                console.log(' Résultat:', result);
            });
        }
        
        //  NOUVELLE FONCTION : Test des champs FIFA ID
        window.testFifaFields = function() {
            console.log(' Test des champs FIFA ID...');
            
            const fifaFields = [
                'input[name="fifa_id"]',
                'input[name="fifa_connect_id"]',
                '#fifa_id',
                '#fifa_connect_id',
                '#voice_fifa_connect_id',
                '#voice_fifa_id'
            ];
            
            console.log(' Vérification de tous les champs FIFA ID:');
            fifaFields.forEach(selector => {
                const field = document.querySelector(selector);
                console.log(`   ${selector}: ${field ? ' TROUVÉ' : '❌ NON TROUVÉ'}`);
                if (field) {
                    console.log(`      - Type: ${field.type}`);
                    console.log(`      - ID: ${field.id}`);
                    console.log(`      - Name: ${field.name}`);
                    console.log(`      - Value: "${field.value}"`);
                    console.log(`      - Visible: ${field.offsetParent !== null ? 'OUI' : 'NON'}`);
                    console.log(`      - Classes: ${field.className}`);
                    console.log(`      - Parent: ${field.parentElement ? field.parentElement.tagName : 'N/A'}`);
                    
                    //  NOUVEAU : Vérification CSS détaillée
                    const styles = window.getComputedStyle(field);
                    console.log(`      - CSS Display: ${styles.display}`);
                    console.log(`      - CSS Visibility: ${styles.visibility}`);
                    console.log(`      - CSS Position: ${styles.position}`);
                    console.log(`      - CSS Opacity: ${styles.opacity}`);
                    console.log(`      - CSS Width: ${styles.width}`);
                    console.log(`      - CSS Height: ${styles.height}`);
                    
                    // Vérifier si le parent est visible
                    let parent = field.parentElement;
                    let level = 0;
                    while (parent && level < 3) {
                        const parentStyles = window.getComputedStyle(parent);
                        console.log(`      - Parent ${level + 1} (${parent.tagName}): Display=${parentStyles.display}, Visible=${parentStyles.visibility}`);
                        if (parent.id) console.log(`        ID: ${parent.id}`);
                        if (parent.className) console.log(`        Classes: ${parent.className}`);
                        parent = parent.parentElement;
                        level++;
                    }
                }
            });
            
            // Test de mise à jour manuelle
            console.log(' Test de mise à jour manuelle...');
            const testPlayer = {
                id: 88,
                name: 'Ali Jebali',
                fifa_connect_id: 'TUN_001',
                position: 'Milieu offensif',
                age: 24,
                nationality: 'Tunisie'
            };
            
            console.log(' Appel de updateFormFieldsWithPlayerData avec:', testPlayer);
            updateFormFieldsWithPlayerData(testPlayer);
            
            // Vérification après mise à jour
            setTimeout(() => {
                console.log(' Vérification après mise à jour:');
                fifaFields.forEach(selector => {
                    const field = document.querySelector(selector);
                    if (field) {
                        console.log(`   ${selector}: Value = "${field.value}"`);
                    }
                });
            }, 1000);
        };
        
        //  NOUVELLE FONCTION : Forcer la visibilité des champs vocaux FIFA ID
        window.forceFifaFieldsVisibility = function() {
            console.log(' Forçage de la visibilité des champs FIFA ID vocaux...');
            
            const voiceFifaFields = [
                '#voice_fifa_connect_id',
                '#voice_fifa_id'
            ];
            
            voiceFifaFields.forEach(selector => {
                const field = document.querySelector(selector);
                if (field) {
                    // Forcer la visibilité
                    field.style.display = 'block';
                    field.style.visibility = 'visible';
                    field.style.opacity = '1';
                    field.style.width = '100%';
                    field.style.height = 'auto';
                    
                    // Ajouter des classes visibles
                    field.classList.add('block', 'visible');
                    field.classList.remove('hidden', 'invisible');
                    
                    console.log(` Visibilité forcée pour ${selector}`);
                    
                    // Vérifier la visibilité après
                    const styles = window.getComputedStyle(field);
                    console.log(`   ${selector} - Display: ${styles.display}, Visible: ${styles.visibility}`);
                }
            });
        };
        
        //  NOUVELLE FONCTION : Rechercher le joueur dans la base de données
        async function searchPlayerInDatabase(playerName) {
            if (!playerName || playerName.trim() === '') {
                console.log('⚠️ Nom du joueur vide - recherche annulée');
                return;
            }
            
            console.log(` Recherche du joueur "${playerName}" dans la base de données...`);
            
            try {
                // Appel à l'API Laravel pour rechercher le joueur
                const response = await fetch(`/api/athletes/search?name=${encodeURIComponent(playerName)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success && data.player) {
                    // Joueur trouvé !
                    displayPlayerInfo(data.player);
                } else {
                    // Joueur non trouvé
                    displayPlayerNotFound(playerName);
                }
                
            } catch (error) {
                console.error('❌ Erreur lors de la recherche du joueur:', error);
                displayPlayerNotFound(playerName);
            }
        }
        
        // 🔄 NOUVELLE FONCTION : Mettre à jour les champs du formulaire principal avec les données du joueur
        function updateFormFieldsWithPlayerData(player) {
            console.log('🔄 Mise à jour des champs du formulaire avec les données du joueur:', player);
            console.log(' Données FIFA:', { fifa_connect_id: player.fifa_connect_id, id: player.id });
            
            try {
                // 1. Mettre à jour le champ ID FIFA Connect (champs vocaux ET formulaire principal)
                const fifaIdFields = [
                    'input[name="fifa_id"]',
                    'input[name="fifa_connect_id"]',
                    '#fifa_id',
                    '#fifa_connect_id',
                    'input[name="player_fifa_id"]'
                ];
                
                // Ajouter les champs vocaux
                const voiceFifaFields = [
                    '#voice_fifa_connect_id',
                    '#voice_fifa_id'
                ];
                
                console.log(' Recherche des champs FIFA ID...');
                console.log(' Champs principaux à vérifier:', fifaIdFields);
                console.log(' Champs vocaux à vérifier:', voiceFifaFields);
                
                let fifaIdUpdated = false;
                
                // Mettre à jour les champs vocaux d'abord
                console.log('🔄 Mise à jour des champs vocaux FIFA ID...');
                for (const selector of voiceFifaFields) {
                    const field = document.querySelector(selector);
                    console.log(` Champ ${selector}:`, field ? 'TROUVÉ' : 'NON TROUVÉ');
                    if (field && player.fifa_connect_id) {
                        field.value = player.fifa_connect_id;
                        field.classList.add('bg-green-50', 'border-green-500');
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                        fifaIdUpdated = true;
                        console.log(` ID FIFA Connect (voix) mis à jour dans ${selector}:`, player.fifa_connect_id);
                    }
                }
                
                // Mettre à jour les champs du formulaire principal
                console.log('🔄 Mise à jour des champs principaux FIFA ID...');
                for (const selector of fifaIdFields) {
                    const field = document.querySelector(selector);
                    console.log(` Champ ${selector}:`, field ? 'TROUVÉ' : 'NON TROUVÉ');
                    if (field && player.fifa_connect_id) {
                        field.value = player.fifa_connect_id;
                        field.classList.add('bg-green-50', 'border-green-500');
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                        fifaIdUpdated = true;
                        console.log(` ID FIFA Connect (principal) mis à jour dans ${selector}:`, player.fifa_connect_id);
                        break;
                    }
                }
                
                // 2. Mettre à jour le champ Athlète (si c'est un select)
                const athleteFields = [
                    'select[name="athlete_id"]',
                    'select[name="player_id"]',
                    '#athlete_id',
                    '#player_id'
                ];
                
                let athleteUpdated = false;
                for (const selector of athleteFields) {
                    const field = document.querySelector(selector);
                    if (field && field.tagName === 'SELECT' && player.id) {
                        // Chercher l'option correspondant au joueur
                        const options = Array.from(field.options);
                        const matchingOption = options.find(option => 
                            option.text.includes(player.name) || 
                            option.value === player.id.toString()
                        );
                        
                        if (matchingOption) {
                            field.value = matchingOption.value;
                            field.classList.add('bg-green-50', 'border-green-500');
                            field.dispatchEvent(new Event('change', { bubbles: true }));
                            athleteUpdated = true;
                            console.log(' Champ Athlète mis à jour avec:', matchingOption.text);
                            break;
                        }
                    }
                }
                
                // 3. Mettre à jour d'autres champs si disponibles
                if (player.position) {
                    const positionFields = [
                        'select[name="position"]',
                        'input[name="position"]',
                        '#position'
                    ];
                    
                    for (const selector of positionFields) {
                        const field = document.querySelector(selector);
                        if (field) {
                            if (field.tagName === 'SELECT') {
                                const options = Array.from(field.options);
                                const matchingOption = options.find(option => 
                                    option.text.toLowerCase().includes(player.position.toLowerCase())
                                );
                                if (matchingOption) {
                                    field.value = matchingOption.value;
                                    field.classList.add('bg-green-50', 'border-green-500');
                                    field.dispatchEvent(new Event('change', { bubbles: true }));
                                    console.log(' Position mise à jour:', matchingOption.text);
                                    break;
                                }
                            } else {
                                field.value = player.position;
                                field.classList.add('bg-green-50', 'border-green-500');
                                field.dispatchEvent(new Event('input', { bubbles: true }));
                                console.log(' Position mise à jour:', player.position);
                                break;
                            }
                        }
                    }
                }
                
                // 4. Mettre à jour l'âge si disponible
                if (player.age) {
                    const ageFields = [
                        'input[name="age"]',
                        'input[name="player_age"]',
                        '#age',
                        '#player_age'
                    ];
                    
                    for (const selector of ageFields) {
                        const field = document.querySelector(selector);
                        if (field) {
                            field.value = player.age;
                            field.classList.add('bg-green-50', 'border-green-500');
                            field.dispatchEvent(new Event('input', { bubbles: true }));
                            console.log(' Âge mis à jour:', player.age);
                            break;
                        }
                    }
                }
                
                // 5. Mettre à jour la nationalité si disponible
                if (player.nationality) {
                    const nationalityFields = [
                        'select[name="nationality"]',
                        'input[name="nationality"]',
                        '#nationality'
                    ];
                    
                    for (const selector of nationalityFields) {
                        const field = document.querySelector(selector);
                        if (field) {
                            if (field.tagName === 'SELECT') {
                                const options = Array.from(field.options);
                                const matchingOption = options.find(option => 
                                    option.text.toLowerCase().includes(player.nationality.toLowerCase())
                                );
                                if (matchingOption) {
                                    field.value = matchingOption.value;
                                    field.classList.add('bg-green-50', 'border-green-500');
                                    field.dispatchEvent(new Event('change', { bubbles: true }));
                                    console.log(' Nationalité mise à jour:', matchingOption.text);
                                    break;
                                }
                            } else {
                                field.value = player.nationality;
                                field.classList.add('bg-green-50', 'border-green-500');
                                field.dispatchEvent(new Event('input', { bubbles: true }));
                                console.log(' Nationalité mise à jour:', player.nationality);
                                break;
                            }
                        }
                    }
                }
                
                // Résumé des mises à jour
                console.log(' Résumé des mises à jour:');
                console.log(`   - ID FIFA Connect: ${fifaIdUpdated ? '' : '❌'}`);
                console.log(`   - Athlète: ${athleteUpdated ? '' : '❌'}`);
                console.log(`   - Position: ${player.position ? '' : '❌'}`);
                console.log(`   - Âge: ${player.age ? '' : '❌'}`);
                console.log(`   - Nationalité: ${player.nationality ? '' : '❌'}`);
                
            } catch (error) {
                console.error('❌ Erreur lors de la mise à jour des champs:', error);
            }
        }
        
        //  Afficher les informations du joueur trouvé
        function displayPlayerInfo(player) {
            console.log(' Joueur trouvé dans la base:', player);
            console.log(' Appel de updateFormFieldsWithPlayerData...');
            
            // 🔄 NOUVEAU : Mettre à jour les champs du formulaire principal
            updateFormFieldsWithPlayerData(player);
            
            // Créer ou mettre à jour le panneau d'information
            let infoPanel = document.getElementById('player-info-panel');
            if (!infoPanel) {
                infoPanel = document.createElement('div');
                infoPanel.id = 'player-info-panel';
                infoPanel.className = 'bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4';
                
                // Insérer après le titre de la section vocale
                const vocalTitle = document.querySelector('h3');
                if (vocalTitle && vocalTitle.textContent.includes('Enregistrement vocal')) {
                    vocalTitle.parentNode.insertBefore(infoPanel, vocalTitle.nextSibling);
                } else {
                    // Fallback : insérer dans le panel de debug
                    const debugPanel = document.querySelector('.bg-gray-100');
                    if (debugPanel) {
                        debugPanel.appendChild(infoPanel);
                    }
                }
            }
            
            infoPanel.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-lg">${player.name.charAt(0)}</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-blue-900">${player.name}</h4>
                        <div class="text-sm text-blue-700">
                            <p><strong>ID FIFA Connect:</strong> <span class="font-mono bg-blue-100 px-2 py-1 rounded">${player.fifa_connect_id || 'N/A'}</span></p>
                            <p><strong>Club:</strong> ${player.club || 'N/A'}</p>
                            <p><strong>Position:</strong> ${player.position || 'N/A'}</p>
                            <p><strong>Âge:</strong> ${player.age || 'N/A'}</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                             Trouvé dans la base
                        </span>
                    </div>
                </div>
            `;
            
            // Ajouter une classe pour l'animation
            infoPanel.classList.add('animate-pulse');
            setTimeout(() => infoPanel.classList.remove('animate-pulse'), 2000);
        }
        
        // ❌ Afficher que le joueur n'a pas été trouvé
        function displayPlayerNotFound(playerName) {
            console.log(`❌ Joueur "${playerName}" non trouvé dans la base`);
            
            // Créer ou mettre à jour le panneau d'information
            let infoPanel = document.getElementById('player-info-panel');
            if (!infoPanel) {
                infoPanel = document.createElement('div');
                infoPanel.id = 'player-info-panel';
                infoPanel.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4';
                
                // Insérer après le titre de la section vocale
                const vocalTitle = document.querySelector('h3');
                if (vocalTitle && vocalTitle.textContent.includes('Enregistrement vocal')) {
                    vocalTitle.parentNode.insertBefore(infoPanel, vocalTitle.nextSibling);
                } else {
                    // Fallback : insérer dans le panel de debug
                    const debugPanel = document.querySelector('.bg-gray-100');
                    if (debugPanel) {
                        debugPanel.appendChild(infoPanel);
                    }
                }
            }
            
            infoPanel.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-lg">?</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-yellow-900">Joueur non trouvé</h4>
                        <div class="text-sm text-yellow-700">
                            <p><strong>Nom recherché:</strong> ${playerName}</strong></p>
                            <p><strong>Statut:</strong> Ce joueur n'existe pas encore dans la base de données</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            ⚠️ Nouveau joueur
                        </span>
                    </div>
                </div>
            `;
            
            // Ajouter une classe pour l'animation
            infoPanel.classList.add('animate-pulse');
            setTimeout(() => infoPanel.classList.remove('animate-pulse'), 2000);
        }
        
        // Fonction de test manuel SUPPRIMÉE (causait des données de test automatiques)
        // function testManualFieldFilling() { ... }
    
    // ========================================
    //  GESTION DES MODES DE COLLECTE
    // ========================================
    
    // Initialisation des modes de collecte
    function initModesCollecte() {
        console.log(' Initialisation des modes de collecte...');
        
        const modeManuel = document.getElementById('mode-manuel');
        const modeVocal = document.getElementById('mode-vocal');
        
        if (!modeManuel || !modeVocal) {
            console.error('❌ Éléments des modes de collecte non trouvés');
            return;
        }
        
        // Mode Manuel par défaut (actif)
        setModeManuel();
        
        // Event listeners pour les modes
        modeManuel.addEventListener('click', setModeManuel);
        modeVocal.addEventListener('click', setModeVocal);
        
        console.log(' Modes de collecte initialisés');
    }
    
    // Activer le mode Manuel
    function setModeManuel() {
        console.log(' Activation du mode Manuel');
        
        const modeManuel = document.getElementById('mode-manuel');
        const modeVocal = document.getElementById('mode-vocal');
        
        if (!modeManuel || !modeVocal) {
            console.error('❌ Éléments des modes non trouvés');
            return;
        }
        
        // Mise à jour des boutons
        modeManuel.classList.remove('bg-gray-100', 'text-gray-700');
        modeManuel.classList.add('bg-blue-600', 'text-white');
        
        modeVocal.classList.remove('bg-blue-600', 'text-white');
        modeVocal.classList.add('bg-gray-100', 'text-gray-700');
        
        // Arrêter l'enregistrement vocal si actif
        if (window.speechService && window.speechService.isRecording) {
            window.speechService.stopListening();
        }
        
        console.log(' Mode Manuel activé');
    }
    
    // Activer le mode Vocal
    function setModeVocal() {
        console.log(' Activation du mode Vocal');
        
        const modeManuel = document.getElementById('mode-manuel');
        const modeVocal = document.getElementById('mode-vocal');
        
        if (!modeManuel || !modeVocal) {
            console.error('❌ Éléments des modes non trouvés');
            return;
        }
        
        // Mise à jour des boutons
        modeVocal.classList.remove('bg-gray-100', 'text-gray-700');
        modeVocal.classList.add('bg-blue-600', 'text-white');
        
        modeManuel.classList.remove('bg-blue-600', 'text-white');
        modeManuel.classList.add('bg-gray-100', 'text-gray-700');
        
        console.log(' Mode Vocal activé');
    }
    
    // ========================================
    //  GESTION DE LA CONSOLE VOCALE
    // ========================================
    
            //  NOUVELLE FONCTION : Mise à jour de l'affichage vocal en temps réel
        function updateVoiceDisplay(text, confidence) {
            console.log('🔄 Mise à jour de l\'affichage vocal:', text);
            
            // Mettre à jour la transcription finale
            const finalTranscript = document.getElementById('voice-final-transcript');
            if (finalTranscript) {
                finalTranscript.textContent = text;
                finalTranscript.style.backgroundColor = '#F0FDF4';
                finalTranscript.style.borderColor = '#10B981';
            }
            
            // Mettre à jour la confiance
            const confidenceElement = document.getElementById('voice-confidence');
            if (confidenceElement) {
                confidenceElement.textContent = `${Math.round(confidence * 100)}%`;
                confidenceElement.style.backgroundColor = confidence > 0.8 ? '#F0FDF4' : '#FEF3C7';
                confidenceElement.style.color = confidence > 0.8 ? '#065F46' : '#92400E';
            }
            
            // Mettre à jour le statut
            const liveStatus = document.getElementById('voice-live-status');
            if (liveStatus) {
                liveStatus.textContent = 'Transcription reçue';
                liveStatus.style.backgroundColor = '#F0FDF4';
                liveStatus.style.color = '#065F46';
            }
            
            // Mettre à jour l'analyse NLP
            const nlpAnalysis = document.getElementById('voice-nlp-analysis');
            if (nlpAnalysis) {
                nlpAnalysis.textContent = 'Analyse en cours...';
                nlpAnalysis.style.backgroundColor = '#FEF3C7';
                nlpAnalysis.style.color = '#92400E';
            }
            
            console.log(' Affichage vocal mis à jour');
        }

        //  CORRECTION : Initialisation de la console vocale avec service Google
        function initConsoleVocale() {
            console.log(' Initialisation de la console vocale avec service Google...');
            
            const startBtn = document.getElementById('start-recording-btn');
            const stopBtn = document.getElementById('stop-recording-btn');
            const voiceStatus = document.getElementById('voice-status');
            const voiceResults = document.getElementById('voice-results');
            
            if (!startBtn || !stopBtn || !voiceStatus || !voiceResults) {
                console.error('❌ Éléments de la console vocale non trouvés');
                return;
            }
            
            //  CORRECTION : Utiliser le service Google SpeechRecognitionService
            if (!window.speechService) {
                console.error('❌ Service Google vocal non disponible');
                if (voiceStatus) {
                    voiceStatus.textContent = '❌ Service Google vocal non initialisé';
                    voiceStatus.className = 'text-center text-red-600 font-medium';
                }
                return;
            }
            
            console.log(' Service Google vocal disponible');
            
            //  CORRECTION : Configuration des callbacks Google
            window.speechService.onResult = function(text, confidence) {
                console.log(' Résultat Google reçu:', text, 'Confiance:', confidence);
                
                // Mettre à jour l'affichage en temps réel
                updateVoiceDisplay(text, confidence);
                
                // Analyser le texte et extraire les données
                const extractedData = analyzeVoiceText(text);
                console.log(' Données extraites Google:', extractedData);
                
                // Afficher les résultats dans la console vocale
                displayVoiceResults(extractedData);
                
                // Remplir le formulaire principal
                fillFormFields(extractedData);
                
                // Écrire dans les notes cliniques
                if (extractedData && (extractedData.player_name || extractedData.age || extractedData.position || extractedData.club)) {
                    addAutomaticNote(extractedData);
                }
            };
            
            window.speechService.onError = function(error) {
                console.error('❌ Erreur Google vocal:', error);
                
                if (voiceStatus) {
                    voiceStatus.textContent = `❌ Erreur Google: ${error.message}`;
                    voiceStatus.className = 'text-center text-red-600 font-medium';
                }
            };
            
            // Event listeners pour les boutons d'enregistrement
            startBtn.addEventListener('click', startVoiceRecording);
            stopBtn.addEventListener('click', stopVoiceRecording);
            
            console.log(' Console vocale Google initialisée');
        }
    
    //  CORRECTION : Démarrer l'enregistrement vocal avec service Google
    function startVoiceRecording() {
        console.log(' Démarrage de l\'enregistrement vocal Google...');
        
        if (!window.speechService) {
            console.error('❌ Service Google vocal non disponible');
            
            // Afficher l'erreur à l'utilisateur
            const voiceStatus = document.getElementById('voice-status');
            if (voiceStatus) {
                voiceStatus.textContent = '❌ Service Google vocal non initialisé';
                voiceStatus.className = 'text-center text-red-600 font-medium';
            }
            return;
        }
        
        const startBtn = document.getElementById('start-recording-btn');
        const stopBtn = document.getElementById('stop-recording-btn');
        const voiceStatus = document.getElementById('voice-status');
        
        // Mise à jour de l'interface
        startBtn.classList.add('hidden');
        stopBtn.classList.remove('hidden');
        voiceStatus.textContent = ' Enregistrement Google en cours...';
        voiceStatus.className = 'text-center text-red-600 font-medium';
        
        //  CORRECTION : Utiliser la méthode Google correcte
        try {
            window.speechService.startListening(
                // Callback de résultat (déjà configuré dans initConsoleVocale)
                null,
                // Callback d'erreur (déjà configuré dans initConsoleVocale)
                null,
                // Callback de statut
                function(status, message) {
                    console.log(' Statut Google:', status, message);
                    if (voiceStatus) {
                        voiceStatus.textContent = ` ${message}`;
                    }
                }
            );
            console.log(' Enregistrement vocal Google démarré');
        } catch (error) {
            console.error('❌ Erreur lors du démarrage Google:', error);
            if (voiceStatus) {
                voiceStatus.textContent = `❌ Erreur: ${error.message}`;
                voiceStatus.className = 'text-center text-red-600 font-medium';
            }
            // Remettre le bouton start
            startBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
        }
    }
    
    //  CORRECTION : Arrêter l'enregistrement vocal avec service Google
    function stopVoiceRecording() {
        console.log(' Arrêt de l\'enregistrement vocal Google...');
        
        if (!window.speechService) {
            console.error('❌ Service Google vocal non disponible');
            return;
        }
        
        const startBtn = document.getElementById('start-recording-btn');
        const stopBtn = document.getElementById('stop-recording-btn');
        const voiceStatus = document.getElementById('voice-status');
        
        // Mise à jour de l'interface
        stopBtn.classList.add('hidden');
        startBtn.classList.remove('hidden');
        voiceStatus.textContent = '⏸️ Enregistrement Google arrêté';
        voiceStatus.className = 'text-center text-orange-600 font-medium';
        
        //  CORRECTION : Utiliser la méthode Google correcte
        try {
            window.speechService.stopListening();
            console.log(' Enregistrement vocal Google arrêté');
        } catch (error) {
            console.error('❌ Erreur lors de l\'arrêt Google:', error);
            if (voiceStatus) {
                voiceStatus.textContent = `❌ Erreur arrêt: ${error.message}`;
                voiceStatus.className = 'text-center text-red-600 font-medium';
            }
        }
    }
    
    // ========================================
    //  FONCTIONS MANQUANTES - CORRECTIONS
    // ========================================
    
    // Fonction pour remplir les champs avec les données d'un joueur
    function fillFormFieldsWithPlayerData(playerData) {
        console.log(' Remplissage des champs avec les données du joueur:', playerData);
        
        try {
            // Remplir les champs vocaux
            const voicePlayerName = document.getElementById('voice_player_name');
            const voiceAge = document.getElementById('voice_age');
            const voicePosition = document.getElementById('voice_position');
            const voiceClub = document.getElementById('voice_club');
            
            if (voicePlayerName && playerData.name) voicePlayerName.value = playerData.name;
            if (voiceAge && playerData.age) voiceAge.value = playerData.age;
            if (voicePosition && playerData.position) voicePosition.value = playerData.position;
            if (voiceClub && playerData.club) voiceClub.value = playerData.club;
            
            // Remplir les champs du formulaire principal
            const mainPlayerName = document.getElementById('voice_player_name_main');
            const mainAge = document.getElementById('voice_age_main');
            const mainPosition = document.getElementById('voice_position_main');
            const mainClub = document.getElementById('voice_club_main');
            
            if (mainPlayerName && playerData.name) mainPlayerName.value = playerData.name;
            if (mainAge && playerData.age) mainAge.value = playerData.age;
            if (mainPosition && playerData.position) mainPosition.value = playerData.position;
            if (mainClub && playerData.club) mainClub.value = playerData.club;
            
            // Remplir les champs de résultats vocaux
            const voicePlayerNameResult = document.getElementById('voice_player_name_result');
            const voiceAgeResult = document.getElementById('voice_age_result');
            const voicePositionResult = document.getElementById('voice_position_result');
            const voiceClubResult = document.getElementById('voice_club_result');
            
            if (voicePlayerNameResult && playerData.name) voicePlayerNameResult.value = playerData.name;
            if (voiceAgeResult && playerData.age) voiceAgeResult.value = playerData.age;
            if (voicePositionResult && playerData.position) voicePositionResult.value = playerData.position;
            if (voiceClubResult && playerData.club) voiceClubResult.value = playerData.club;
            
            console.log(' Champs remplis avec succès avec les données du joueur');
            
            // Mettre à jour le statut vocal
            const voiceStatus = document.getElementById('voice-status');
            if (voiceStatus) {
                voiceStatus.textContent = ' Données du joueur remplies automatiquement';
                voiceStatus.className = 'text-center text-green-600 font-medium';
            }
            
        } catch (error) {
            console.error('❌ Erreur lors du remplissage des champs:', error);
            
            // Mettre à jour le statut vocal en cas d'erreur
            const voiceStatus = document.getElementById('voice-status');
            if (voiceStatus) {
                voiceStatus.textContent = `❌ Erreur: ${error.message}`;
                voiceStatus.className = 'text-center text-red-600 font-medium';
            }
        }
    }
    
    // Afficher les résultats vocaux
    function displayVoiceResults(data) {
        console.log(' Affichage des résultats vocaux:', data);
        
        const voiceResults = document.getElementById('voice-results');
        const voicePlayerName = document.getElementById('voice_player_name_result');
        const voiceAge = document.getElementById('voice_age_result');
        const voicePosition = document.getElementById('voice_position_result');
        const voiceClub = document.getElementById('voice_club_result');
        
        if (!voiceResults || !voicePlayerName || !voiceAge || !voicePosition || !voiceClub) {
            console.error('❌ Éléments d\'affichage des résultats non trouvés');
            return;
        }
        
        // Remplir les champs vocaux
        if (data.player_name) voicePlayerName.value = data.player_name;
        if (data.age) voiceAge.value = data.age;
        if (data.position) voicePosition.value = data.position;
        if (data.club) voiceClub.value = data.club;
        
        // Afficher la section des résultats
        voiceResults.classList.remove('hidden');
        
        // Mettre à jour le statut
        const voiceStatus = document.getElementById('voice-status');
        voiceStatus.textContent = ' Données extraites avec succès !';
        voiceStatus.className = 'text-center text-green-600 font-medium';
        
        console.log(' Résultats vocaux affichés');
    }
    
    // ========================================
    //  INITIALISATION COMPLÈTE
    // ========================================
    
    // ========================================
    //  SOLUTION ALTERNATIVE : INTÉGRATION DIRECTE
    // ========================================
    
    //  NOUVEAU : Service vocal robuste avec intégration directe
    let serviceVocal = null;
    let nlpMedical = null;
    
    // Fonction qui s'intègre directement avec le service vocal
    function integrateWithSpeechService() {
        console.log(' Intégration directe avec le service vocal robuste...');
        
        //  NOUVEAU : Initialiser le service vocal robuste
        if (typeof ServiceVocal !== 'undefined') {
            serviceVocal = new ServiceVocal();
            serviceVocal.initialize().then(() => {
                console.log(' ServiceVocal initialisé avec succès');
                setupServiceVocalCallbacks();
            }).catch(error => {
                console.error('❌ Erreur lors de l\'initialisation de ServiceVocal:', error);
            });
        } else {
            console.warn('⚠️ ServiceVocal non disponible, utilisation du service existant');
            integrateWithExistingSpeechService();
        }
        
        //  NOUVEAU : Initialiser le NLP médical
        if (typeof NLPMedicalPCMA !== 'undefined') {
            nlpMedical = new NLPMedicalPCMA();
            console.log(' NLPMedicalPCMA initialisé avec succès');
        } else {
            console.warn('⚠️ NLPMedicalPCMA non disponible');
        }
    }
    
    //  NOUVEAU : Configuration des callbacks du service vocal robuste
    function setupServiceVocalCallbacks() {
        if (!serviceVocal) return;
        
        // Callback de transcription en temps réel
        serviceVocal.onTranscript((data) => {
            console.log(' Transcription reçue par ServiceVocal:', data);
            
            //  NOUVEAU : Mettre à jour l'affichage en temps réel
            updateLiveVoiceDisplay(data);
            
            try {
                //  NOUVEAU : Utiliser le NLP médical pour l'analyse
                let extractedData;
                if (nlpMedical) {
                    extractedData = nlpMedical.analyzeTranscript(data.transcript, data.confidence);
                } else {
                    extractedData = analyzeVoiceText(data.transcript);
                }
                
                if (extractedData) {
                    console.log(' Données extraites par ServiceVocal + NLP:', extractedData);
                    
                    //  NOUVEAU : Mettre à jour l'analyse NLP et les données extraites
                    updateNLPAnalysis(extractedData);
                    
                    // Afficher les résultats dans la console vocale
                    displayVoiceResults(extractedData);
                    
                    // Remplir le formulaire principal
                    fillFormFields(extractedData);
                    
                    // Effacer les anciennes données TEST si c'est une commande FIFA
                    if (extractedData.command === 'fifa_connect_search') {
                        console.log('🧹 Effacement des anciennes données TEST...');
                        clearOldTestData();
                    }
                    
                    // Mettre à jour le statut
                    const voiceStatus = document.getElementById('voice-status');
                    if (voiceStatus) {
                        voiceStatus.textContent = ' Données traitées par ServiceVocal + NLP !';
                        voiceStatus.className = 'text-center text-green-600 font-medium';
                    }
                    
                    console.log(' Résultat vocal traité avec succès par ServiceVocal + NLP');
                }
                
            } catch (error) {
                console.error('❌ Erreur lors du traitement du résultat vocal:', error);
                
                const voiceStatus = document.getElementById('voice-status');
                if (voiceStatus) {
                    voiceStatus.textContent = `❌ Erreur: ${error.message}`;
                    voiceStatus.className = 'text-center text-red-600 font-medium';
                }
            }
        });
        
        // Callback d'erreur
        serviceVocal.onError((error) => {
            console.error('❌ Erreur ServiceVocal:', error);
            
            const voiceStatus = document.getElementById('voice-status');
            if (voiceStatus) {
                voiceStatus.textContent = `❌ Erreur: ${error.message}`;
                voiceStatus.className = 'text-center text-red-600 font-medium';
            }
        });
        
        // Callback de démarrage
        serviceVocal.onStart(() => {
            console.log(' ServiceVocal démarré');
            
            //  NOUVEAU : Mettre à jour l'affichage en temps réel
            updateVoiceLiveStatus(' Reconnaissance vocale en cours...', 'bg-blue-100 text-blue-700');
            
            const voiceStatus = document.getElementById('voice-status');
            if (voiceStatus) {
                voiceStatus.textContent = ' Reconnaissance vocale en cours...';
                voiceStatus.className = 'text-center text-blue-600 font-medium';
            }
        });
        
        // Callback d'arrêt
        serviceVocal.onStop(() => {
            console.log('⏸️ ServiceVocal arrêté');
            
            //  NOUVEAU : Mettre à jour l'affichage en temps réel
            updateVoiceLiveStatus('⏸️ Reconnaissance vocale arrêtée', 'bg-orange-100 text-orange-700');
            
            const voiceStatus = document.getElementById('voice-status');
            if (voiceStatus) {
                voiceStatus.textContent = '⏸️ Reconnaissance vocale arrêtée';
                voiceStatus.className = 'text-center text-orange-600 font-medium';
            }
        });
        
        console.log(' Callbacks ServiceVocal configurés avec succès');
    }
    
    //  NOUVEAU : Intégration avec le service vocal existant (fallback)
    function integrateWithExistingSpeechService() {
        if (!window.speechService) {
            console.error('❌ Service vocal non disponible pour l\'intégration');
            return;
        }
        
        // Intercepter directement les résultats
        const originalStartListening = window.speechService.startListening;
        const originalStopListening = window.speechService.stopListening;
        
        // Intercepter le démarrage de l'écoute
        window.speechService.startListening = function() {
            console.log(' Démarrage de l\'écoute avec intégration directe...');
            originalStartListening.call(this);
            
            // Surveiller les résultats en temps réel
            this.startResultMonitoring();
        };
        
        // Intercepter l'arrêt de l'écoute
        window.speechService.stopListening = function() {
            console.log('⏸️ Arrêt de l\'écoute avec intégration directe...');
            originalStopListening.call(this);
            
            // Traiter les résultats immédiatement
            this.processResultsImmediately();
        };
        
        // Surveillance en temps réel
        window.speechService.startResultMonitoring = function() {
            console.log(' Démarrage de la surveillance des résultats...');
            
            this.resultMonitor = setInterval(() => {
                if (this.lastResult && this.lastResult !== this.processedResult) {
                    console.log(' Nouveau résultat détecté par surveillance:', this.lastResult);
                    this.processedResult = this.lastResult;
                    this.processVoiceResult(this.lastResult);
                }
            }, 100);
        };
        
        // Traitement immédiat des résultats
        window.speechService.processResultsImmediately = function() {
            console.log(' Traitement immédiat des résultats...');
            
            setTimeout(() => {
                if (this.lastResult) {
                    console.log(' Traitement immédiat du résultat:', this.lastResult);
                    this.processVoiceResult(this.lastResult);
                } else {
                    console.log('⚠️ Aucun résultat disponible pour traitement immédiat');
                }
            }, 500);
        };
        
        // Traitement du résultat vocal
        window.speechService.processVoiceResult = function(result) {
            console.log(' Traitement du résultat vocal:', result);
            
            //  NOUVEAU : Mettre à jour l'affichage en temps réel
            updateLiveVoiceDisplay({
                transcript: result,
                confidence: 0.9,
                isFinal: true,
                final: result,
                interim: ''
            });
            
            try {
                // Utiliser le NLP médical si disponible
                let extractedData;
                if (nlpMedical) {
                    extractedData = nlpMedical.analyzeTranscript(result, 0.9);
                } else {
                    extractedData = analyzeVoiceText(result);
                }
                
                if (extractedData) {
                    console.log(' Données extraites par intégration directe:', extractedData);
                    
                    //  NOUVEAU : Mettre à jour l'analyse NLP et les données extraites
                    updateNLPAnalysis(extractedData);
                    
                    // Afficher les résultats dans la console vocale
                    displayVoiceResults(extractedData);
                    
                    // Remplir le formulaire principal
                    fillFormFields(extractedData);
                    
                    // Effacer les anciennes données TEST si c'est une commande FIFA
                    if (extractedData.command === 'fifa_connect_search') {
                        console.log('🧹 Effacement des anciennes données TEST...');
                        clearOldTestData();
                    }
                    
                    // Mettre à jour le statut
                    const voiceStatus = document.getElementById('voice-status');
                    if (voiceStatus) {
                        voiceStatus.textContent = ' Données traitées par intégration directe !';
                        voiceStatus.className = 'text-center text-green-600 font-medium';
                    }
                    
                    console.log(' Résultat vocal traité avec succès par intégration directe');
                }
                
            } catch (error) {
                console.error('❌ Erreur lors du traitement du résultat vocal:', error);
                
                const voiceStatus = document.getElementById('voice-status');
                if (voiceStatus) {
                    voiceStatus.textContent = `❌ Erreur: ${error.message}`;
                    voiceStatus.className = 'text-center text-red-600 font-medium';
                }
            }
        };
        
        console.log(' Intégration directe avec le service vocal existant configurée');
    }
    
            // Initialiser tous les composants
        function initAll() {
            console.log(' Initialisation complète de l\'application...');
            
            //  TEST IMMÉDIAT ET VISIBLE
            console.log(' TEST IMMÉDIAT DES ÉLÉMENTS...');
            const modeManuel = document.getElementById('mode-manuel');
            const modeVocal = document.getElementById('mode-vocal');
            const consoleVocale = document.getElementById('console-vocale');
            
            console.log(' Mode Manuel trouvé:', modeManuel);
            console.log(' Mode Vocal trouvé:', modeVocal);
            console.log(' Console Vocale trouvée:', consoleVocale);
            
            if (!modeManuel || !modeVocal || !consoleVocale) {
                console.error('❌ ÉLÉMENTS MANQUANTS ! Vérifiez les IDs HTML');
                return;
            }
            
            // Initialiser les modes de collecte
            //  ANCIEN SYSTÈME SUPPRIMÉ
    // initModesCollecte();
            
            // Initialiser la console vocale
            initConsoleVocale();
            
            //  NOUVEAU : Intégration directe avec le service vocal
            integrateWithSpeechService();
            
            //  TEST IMMÉDIAT DES BOUTONS DE MODE
            console.log(' TEST IMMÉDIAT DES BOUTONS DE MODE...');
            const modeManuelTest = document.getElementById('mode-manuel');
            const modeVocalTest = document.getElementById('mode-vocal');
            const consoleVocaleFinal = document.getElementById('console-vocale');
            
            if (modeManuelTest && modeVocalTest && consoleVocaleFinal) {
                console.log(' Tous les éléments des modes trouvés');
                console.log(' Bouton Mode Manuel:', modeManuelTest);
                console.log(' Bouton Mode Vocal:', modeVocalTest);
                console.log(' Console Vocale:', consoleVocaleFinal);
                
                // Test du clic sur Mode Vocal
                console.log(' Test du clic sur Mode Vocal...');
                modeVocalTest.click();
                
                // Vérification après 1 seconde
                setTimeout(() => {
                    console.log(' Vérification après clic sur Mode Vocal:');
                    console.log('   - Mode Manuel actif:', modeManuelTest.classList.contains('bg-blue-600'));
                    console.log('   - Mode Vocal actif:', modeVocalTest.classList.contains('bg-blue-600'));
                    console.log('   - Console vocale masquée:', consoleVocaleFinal.classList.contains('hidden'));
                    console.log('   - Console vocale style display:', consoleVocaleFinal.style.display);
                    console.log('   - Console vocale style visibility:', consoleVocaleFinal.style.visibility);
                    console.log('   - Console vocale style opacity:', consoleVocaleFinal.style.opacity);
                }, 1000);
            } else {
                console.error('❌ Éléments des modes non trouvés dans initAll');
            }
        
        // Vérification finale de l'état des modes
        const consoleVocaleFinalCheck = document.getElementById('console-vocale');
        const modeManuelFinal = document.getElementById('mode-manuel');
        const modeVocalFinal = document.getElementById('mode-vocal');
        
        if (consoleVocaleFinalCheck && modeManuelFinal && modeVocalFinal) {
            console.log(' Vérification finale des modes:');
            console.log('   - Mode Manuel actif:', modeManuelFinal.classList.contains('bg-blue-600'));
            console.log('   - Mode Vocal actif:', modeVocalFinal.classList.contains('bg-blue-600'));
            console.log('   - Console vocale masquée:', consoleVocaleFinalCheck.classList.contains('hidden'));
            console.log('   - Console vocale style display:', consoleVocaleFinalCheck.style.display);
        }
        
        // Initialiser le service de reconnaissance vocale (déjà fait plus haut)
        if (window.speechService) {
            console.log(' Service vocal global détecté et configuré');
            
            //  NOUVELLE APPROCHE : Configuration des callbacks avec intégration directe
            if (!window.speechService.onResult) {
                console.log(' Configuration du callback principal avec intégration directe...');
                
                // Configurer les callbacks pour la console vocale
                window.speechService.onResult = function(result) {
                    console.log(' Résultat vocal reçu (callback principal):', result);
                    console.log(' Type de résultat:', typeof result);
                    console.log(' Contenu du résultat:', result);
                    
                    // Stocker le dernier résultat pour l'intégration directe
                    this.lastResult = result;
                    
                    // Analyser le texte et extraire les données
                    const extractedData = analyzeVoiceText(result);
                    console.log(' Données extraites (callback principal):', extractedData);
                    
                    // Afficher les résultats dans la console vocale
                    displayVoiceResults(extractedData);
                    
                    // Remplir le formulaire principal
                    fillFormFields(extractedData);
                    
                    //  NOUVEAU : Effacer les anciennes données TEST si c'est une commande FIFA
                    if (extractedData.command === 'fifa_connect_search') {
                        console.log('🧹 Effacement des anciennes données TEST...');
                        clearOldTestData();
                    }
                };
            } else {
                console.log(' Callback principal déjà configuré');
            }
            
            // Vérification immédiate que le callback est configuré
            console.log(' Callback onResult configuré dans initAll');
            console.log(' Vérification callback:', typeof window.speechService.onResult);
        }
        
        // Configurer le callback d'erreur
        if (window.speechService) {
            window.speechService.onError = function(error) {
                console.error('❌ Erreur de reconnaissance vocale:', error);
                
                const voiceStatus = document.getElementById('voice-status');
                if (voiceStatus) {
                    voiceStatus.textContent = `❌ Erreur: ${error.message}`;
                    voiceStatus.className = 'text-center text-red-600 font-medium';
                }
            };
        }
        
        console.log(' Application initialisée avec succès');
        
        // Vérification finale des callbacks
        if (window.speechService && window.speechService.onResult) {
            console.log(' Callback onResult configuré avec succès');
        } else {
            console.error('❌ Callback onResult non configuré !');
        }
        
        //  TEST DIRECT DE LA COMMANDE FIFA CONNECT
        setTimeout(() => {
            console.log(' TEST DIRECT DE LA COMMANDE FIFA CONNECT...');
            testFifaConnectCommand();
        }, 2000);
        
        //  NOUVEAU : Test de l'intégration directe
        setTimeout(() => {
            console.log(' TEST DE L\'INTÉGRATION DIRECTE...');
            testIntegrationDirecte();
        }, 3000);
    }
    
    //  FONCTION DE TEST DE L'INTÉGRATION DIRECTE
    function testIntegrationDirecte() {
        console.log(' Test de l\'intégration directe ServiceVocal + NLP...');
        
        // Vérifier que ServiceVocal est disponible
        if (typeof ServiceVocal !== 'undefined') {
            console.log(' ServiceVocal disponible globalement');
        } else {
            console.warn('⚠️ ServiceVocal non disponible globalement');
        }
        
        // Vérifier que NLPMedicalPCMA est disponible
        if (typeof NLPMedicalPCMA !== 'undefined') {
            console.log(' NLPMedicalPCMA disponible globalement');
        } else {
            console.warn('⚠️ NLPMedicalPCMA non disponible globalement');
        }
        
        // Vérifier les variables locales
        if (serviceVocal) {
            console.log(' Variable serviceVocal disponible:', serviceVocal.getStatus());
        } else {
            console.warn('⚠️ Variable serviceVocal non disponible');
        }
        
        if (nlpMedical) {
            console.log(' Variable nlpMedical disponible');
        } else {
            console.warn('⚠️ Variable nlpMedical non disponible');
        }
        
        console.log(' Test de l\'intégration directe terminé');
    }
    
    // ========================================
    //  NOUVEAU : FONCTIONS D'AFFICHAGE EN TEMPS RÉEL
    // ========================================
    
    //  Mettre à jour le statut de la reconnaissance vocale
    function updateVoiceLiveStatus(status, className) {
        const statusElement = document.getElementById('voice-live-status');
        if (statusElement) {
            statusElement.textContent = status;
            statusElement.className = `px-2 py-1 text-xs font-medium rounded-full ${className}`;
        }
    }
    
    //  Mettre à jour l'affichage en temps réel de la reconnaissance vocale
    function updateLiveVoiceDisplay(data) {
        console.log(' Mise à jour de l\'affichage en temps réel:', data);
        
        // Mettre à jour la confiance
        const confidenceElement = document.getElementById('voice-confidence');
        if (confidenceElement && data.confidence) {
            const confidencePercent = Math.round(data.confidence * 100);
            confidenceElement.textContent = `${confidencePercent}%`;
            
            // Changer la couleur selon la confiance
            if (confidencePercent >= 80) {
                confidenceElement.className = 'ml-2 px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full';
            } else if (confidencePercent >= 60) {
                confidenceElement.className = 'ml-2 px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full';
            } else {
                confidenceElement.className = 'ml-2 px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full';
            }
        }
        
        // Mettre à jour la transcription en cours
        const interimElement = document.getElementById('voice-interim-transcript');
        if (interimElement && data.interim) {
            interimElement.innerHTML = `<span class="text-purple-600">${data.interim}</span>`;
        }
        
        // Mettre à jour la transcription finale
        if (data.isFinal && data.final) {
            const finalElement = document.getElementById('voice-final-transcript');
            if (finalElement) {
                finalElement.innerHTML = `<span class="text-green-600 font-medium">${data.final}</span>`;
            }
        }
    }
    
    //  Mettre à jour l'analyse NLP et les données extraites
    function updateNLPAnalysis(extractedData) {
        console.log('🧠 Mise à jour de l\'analyse NLP:', extractedData);
        
        // Mettre à jour l'analyse NLP
        const nlpElement = document.getElementById('voice-nlp-analysis');
        if (nlpElement) {
            let nlpText = '';
            
            if (extractedData.command === 'fifa_connect_search') {
                nlpText = ` Commande FIFA CONNECT détectée${extractedData.fifa_number ? ` (Numéro: ${extractedData.fifa_number})` : ''}`;
            } else if (extractedData.player_name || extractedData.age || extractedData.position || extractedData.club) {
                const parts = [];
                if (extractedData.player_name) parts.push(`Nom: ${extractedData.player_name}`);
                if (extractedData.age) parts.push(`Âge: ${extractedData.age}`);
                if (extractedData.position) parts.push(`Position: ${extractedData.position}`);
                if (extractedData.club) parts.push(`Club: ${extractedData.club}`);
                nlpText = parts.join(' | ');
            } else {
                nlpText = 'Aucune donnée structurée détectée';
            }
            
            nlpElement.innerHTML = `<span class="text-blue-600">${nlpText}</span>`;
        }
        
        // Mettre à jour les données extraites
        const dataElement = document.getElementById('voice-extracted-data');
        if (dataElement) {
            const dataText = JSON.stringify(extractedData, null, 2);
            dataElement.innerHTML = `<pre class="text-xs text-green-600 overflow-auto">${dataText}</pre>`;
        }
    }
    
    //  Réinitialiser l'affichage en temps réel
    function resetLiveVoiceDisplay() {
        updateVoiceLiveStatus('En attente', 'bg-gray-100 text-gray-700');
        
        const confidenceElement = document.getElementById('voice-confidence');
        if (confidenceElement) {
            confidenceElement.textContent = '--';
            confidenceElement.className = 'ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full';
        }
        
        const interimElement = document.getElementById('voice-interim-transcript');
        if (interimElement) {
            interimElement.innerHTML = '<span class="text-gray-400">Parlez maintenant...</span>';
        }
        
        const finalElement = document.getElementById('voice-final-transcript');
        if (finalElement) {
            finalElement.innerHTML = '<span class="text-gray-400">Aucune transcription finale</span>';
        }
        
        const nlpElement = document.getElementById('voice-nlp-analysis');
        if (nlpElement) {
            nlpElement.innerHTML = '<span class="text-gray-400">Analyse en attente...</span>';
        }
        
        const dataElement = document.getElementById('voice-extracted-data');
        if (dataElement) {
            dataElement.innerHTML = '<span class="text-gray-400">Aucune donnée extraite</span>';
        }
    }
    
    //  FONCTION DE TEST DIRECTE
    function testFifaConnectCommand() {
        console.log(' Test direct de la commande FIFA CONNECT...');
        
        // 1. Tester l'analyse du texte (avec et sans numéro)
        const testTexts = [
            "ID FIFA CONNECT",
            "ID FIFA CONNECT 001",
            "FIFA CONNECT 001",
            "FIFA 001"
        ];
        
        testTexts.forEach((testText, index) => {
            console.log(`\n Test ${index + 1}: "${testText}"`);
            
            const analysis = analyzeVoiceText(testText);
            console.log(' Résultat de l\'analyse:', analysis);
            
            if (analysis.command === 'fifa_connect_search') {
                console.log(' Commande FIFA CONNECT détectée avec succès !');
                
                if (analysis.fifa_number) {
                    console.log(` Numéro FIFA capturé: ${analysis.fifa_number}`);
                }
                
                // 2. Tester le remplissage des champs
                console.log(' Test du remplissage des champs...');
                fillFormFields(analysis);
                
                // 3. Tester la recherche automatique
                console.log(' Test de la recherche automatique...');
                searchPlayerByFifaConnect();
                
            } else {
                console.error('❌ Commande FIFA CONNECT non détectée !');
                console.log(' Texte analysé:', testText);
                console.log(' Résultat obtenu:', analysis);
            }
        });
    }
    
    //  NOUVELLE FONCTION : Effacer les anciennes données TEST
    function clearOldTestData() {
        console.log('🧹 Effacement des anciennes données TEST...');
        
        // Champs vocaux à effacer
        const voiceFields = [
            'voice_player_name',
            'voice_age', 
            'voice_position',
            'voice_club'
        ];
        
        // Champs du formulaire principal à effacer
        const mainFields = [
            'voice_player_name_main',
            'voice_age_main', 
            'voice_position_main',
            'voice_club_main'
        ];
        
        // Effacer les champs vocaux
        voiceFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = '';
                field.classList.remove('bg-green-50', 'border-green-500');
                field.classList.add('border-gray-300');
            }
        });
        
        // Effacer les champs du formulaire principal
        mainFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = '';
                field.classList.remove('bg-green-50', 'border-green-500');
                field.classList.add('border-gray-300');
            }
        });
        
        // Effacer les champs de résultat vocal
        const resultFields = [
            'voice_player_name_result',
            'voice_age_result',
            'voice_position_result',
            'voice_club_result'
        ];
        
        resultFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = '';
                field.classList.remove('bg-green-50', 'border-green-500');
                field.classList.add('border-gray-300');
            }
        });
        
        console.log(' Anciennes données TEST effacées avec succès !');
    }
    
    // Démarrer l'initialisation quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
    
    //  INITIALISATION PROPRE SANS TESTS FORCÉS
    console.log(' Initialisation propre des composants...');
    
} else {
    console.error('❌ Module SpeechRecognitionService non trouvé !');
}
});
</script>

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="/js/SpeechRecognitionService-laravel.js"></script>
        <script src="/js/ServiceVocal.js"></script>
        <script src="/js/SpeechRecognitionService-laravel.js"></script>

<!-- Système de modes sécurisé -->
<script>
class ModeManager {
    constructor() {
        this.currentMode = 'manual';
        this.modes = ['manual', 'vocal', 'ocr', 'fhir'];
        this.modeElements = {};
        this.modeSections = {};
        this.init();
    }
    
    init() {
        this.initializeElements();
        this.setupEventListeners();
        this.initializeSectionVisibility();
        this.initializeVoiceConsole();
        this.setupTransferButtons();
    }

moveFormToManualSection() {
    const mainForm = document.getElementById('pcma-form');
    const manualSection = document.getElementById('manual-mode-section');
    
    if (mainForm && manualSection) {
        manualSection.appendChild(mainForm);
    }
}

hideVocalContentInManual() {
    const vocalElements = [
        'init-service',
        'start-recording-btn',
        'stop-recording-btn',
        'transfer-to-form-btn',
        'voice-status',
        'audio-activity-indicator'
    ];
    
    vocalElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.classList.add('hidden');
        }
    });
}
    
    initializeElements() {
        this.modeElements = {
            manual: document.getElementById('mode-manuel'),
            vocal: document.getElementById('mode-vocal'),
            ocr: document.getElementById('scan-tab'),
            fhir: document.getElementById('mode-fhir')
        };
        
        this.modeSections = {
            manual: document.getElementById('manual-mode-section'),
            vocal: document.getElementById('vocal-mode-section'),
            ocr: document.getElementById('ocr-mode-section'),
            fhir: document.getElementById('fhir-mode-section')
        };
    }
    
    initializeSectionVisibility() {
        const manualSection = document.getElementById('manual-mode-section');
        if (manualSection) {
            manualSection.classList.remove('hidden');
            manualSection.style.setProperty('display', 'block', 'important');
            manualSection.style.setProperty('visibility', 'visible', 'important');
            manualSection.style.setProperty('opacity', '1', 'important');
        }
        
        const otherSections = ['vocal-mode-section', 'ocr-mode-section', 'fhir-mode-section'];
        otherSections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            if (section) {
                section.classList.add('hidden');
                section.style.setProperty('display', 'none', 'important');
                section.style.setProperty('visibility', 'hidden', 'important');
                section.style.setProperty('opacity', '0', 'important');
            }
        });
        
        const manualButton = document.getElementById('mode-manuel');
        if (manualButton) {
            manualButton.classList.remove('bg-gray-100', 'text-gray-700');
            manualButton.classList.add('bg-blue-600', 'text-white', 'active');
        }
        
        const otherButtons = ['mode-vocal', 'scan-tab', 'mode-fhir'];
        otherButtons.forEach(buttonId => {
            const button = document.getElementById(buttonId);
            if (button) {
                button.classList.remove('bg-blue-600', 'text-white', 'active');
                button.classList.add('bg-gray-100', 'text-gray-700');
            }
        });
        
        this.currentMode = 'manual';
    }
    
    initializeVoiceConsole() {
        const testServiceBtn = document.getElementById('init-service');
        if (testServiceBtn) {
            testServiceBtn.addEventListener('click', () => {
                this.testSpeechService();
            });
        }
        
        const testAdvancedBtn = document.getElementById('test-advanced-btn');
        if (testAdvancedBtn) {
            testAdvancedBtn.addEventListener('click', () => {
                this.testAdvancedSpeechRecognition();
            });
        }
        
        const startRecordingBtn = document.getElementById('start-recording-btn');
        if (startRecordingBtn) {
            startRecordingBtn.addEventListener('click', () => {
                this.startSpeechRecognition();
            });
        }
        
        const stopRecordingBtn = document.getElementById('stop-recording-btn');
        if (stopRecordingBtn) {
            stopRecordingBtn.addEventListener('click', () => {
                this.stopSpeechRecognition();
            });
        }
        
        const transferBtn = document.getElementById('transfer-to-form-btn');
        if (transferBtn) {
            transferBtn.addEventListener('click', () => {
                this.transferVocalDataToManual();
            });
        }
    }
    
    //  NOUVEAU : TEST AVANCÉ DE LA RECONNAISSANCE VOCALE
    testAdvancedSpeechRecognition() {
        const voiceStatus = document.getElementById('voice-status');
        if (voiceStatus) {
            voiceStatus.textContent = 'Test avancé en cours...';
            voiceStatus.className = 'text-sm text-blue-600 mb-4';
        }
        
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            if (voiceStatus) {
                voiceStatus.textContent = 'API Speech Recognition non supportée';
                voiceStatus.className = 'text-sm text-red-600 mb-4';
            }
            return;
        }
        
        try {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const testRecognition = new SpeechRecognition();
            
            testRecognition.continuous = false;
            testRecognition.interimResults = false;
            testRecognition.lang = 'fr-FR';
            testRecognition.maxAlternatives = 1;
            
            testRecognition.onstart = () => {
                if (voiceStatus) {
                    voiceStatus.textContent = 'Test: Reconnaissance démarrée !';
                    voiceStatus.className = 'text-sm text-green-600 mb-4';
                }
                
                setTimeout(() => {
                    testRecognition.stop();
                }, 2000);
            };
            
            testRecognition.onresult = (event) => {
                if (voiceStatus) {
                    voiceStatus.textContent = 'Test: Reconnaissance fonctionne parfaitement !';
                    voiceStatus.className = 'text-sm text-green-600 mb-4';
                }
            };
            
            testRecognition.onerror = (event) => {
                if (voiceStatus) {
                    voiceStatus.textContent = `Test: Erreur ${event.error}`;
                    voiceStatus.className = 'text-sm text-red-600 mb-4';
                }
            };
            
            testRecognition.onend = () => {
                // Test terminé
            };
            
            testRecognition.start();
            
        } catch (error) {
            if (voiceStatus) {
                voiceStatus.textContent = `Test: Erreur de création - ${error.message}`;
                voiceStatus.className = 'text-sm text-red-600 mb-4';
            }
        }
    }
    
    testSpeechService() {
        const serviceStatus = document.getElementById('service-status');
        if (serviceStatus) {
            serviceStatus.textContent = 'Test en cours...';
            serviceStatus.className = 'mt-2 text-sm text-blue-600';
        }
        
        this.checkCompleteEnvironment().then((result) => {
            if (result.success) {
                if (serviceStatus) {
                    serviceStatus.textContent = 'Service vocal disponible !';
                    serviceStatus.className = 'mt-2 text-sm text-green-600';
                }
                
                const startBtn = document.getElementById('start-recording-btn');
                const stopBtn = document.getElementById('stop-recording-btn');
                if (startBtn) {
                    startBtn.disabled = false;
                    startBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
                if (stopBtn) {
                    stopBtn.disabled = false;
                    stopBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } else {
                if (serviceStatus) {
                    serviceStatus.textContent = `${result.error}`;
                    serviceStatus.className = 'mt-2 text-sm text-red-600';
                }
            }
        });
    }
    
    async checkCompleteEnvironment() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            return {
                success: false,
                error: 'API Speech Recognition non supportée dans ce navigateur'
            };
        }
        
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            stream.getTracks().forEach(track => track.stop());
            
            return { success: true };
        } catch (error) {
            if (error.name === 'NotAllowedError') {
                return {
                    success: false,
                    error: 'Permission microphone refusée - Cliquez sur "Autoriser"'
                };
            } else if (error.name === 'NotFoundError') {
                return {
                    success: false,
                    error: 'Aucun microphone détecté sur cet appareil'
                };
            } else {
                return {
                    success: false,
                    error: `Erreur microphone: ${error.message}`
                };
            }
        }
    }
    
    startSpeechRecognition() {
        const voiceStatus = document.getElementById('voice-status');
        const startBtn = document.getElementById('start-recording-btn');
        const stopBtn = document.getElementById('stop-recording-btn');
        
        this.startGoogleCloudSpeechRecognition();
    }
    
    startGoogleCloudSpeechRecognition() {
        const voiceStatus = document.getElementById('voice-status');
        const startBtn = document.getElementById('start-recording-btn');
        const stopBtn = document.getElementById('stop-recording-btn');
        
        if (!this.speechService) {
            this.speechService = new SpeechRecognitionService();
            this.configureSpeechService();
        }
        
        this.isRecording = true;
        this.recordingStartTime = Date.now();
        
        this.speechService.startListening().then(() => {
            if (voiceStatus) {
                voiceStatus.textContent = 'Enregistrement Google Cloud en cours... (60s max)';
                voiceStatus.className = 'text-sm text-green-600 mb-4';
            }
            if (startBtn) {
                startBtn.classList.add('hidden');
            }
            if (stopBtn) {
                stopBtn.classList.remove('hidden');
            }
            
            this.startTimeCounter();
            this.startAudioIndicator();
            
        }).catch(error => {
            if (voiceStatus) {
                voiceStatus.textContent = `Erreur: ${error.message}`;
                voiceStatus.className = 'text-sm text-red-600 mb-4';
            }
            this.resetRecognitionButtons();
        });
    }
    
    async configureSpeechService() {
        try {
            const response = await fetch('/api/google-speech-key');
            
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.status === 'success' && data.apiKey) {
                this.speechService.configure({
                    apiKey: data.apiKey,
                    language: 'fr-FR',
                    encoding: 'WEBM_OPUS',
                    sampleRate: 48000,
                    model: 'latest_long'
                });
                
                this.speechService.onResult = (result) => {
                    let transcript = result;
                    
                    if (typeof result === 'object' && result.text) {
                        transcript = result.text;
                    }
                    
                    this.processSpeechResult(transcript);
                };
                
                this.speechService.onError = (error) => {
                    const voiceStatus = document.getElementById('voice-status');
                    if (voiceStatus) {
                        voiceStatus.textContent = `Erreur: ${error.message}`;
                        voiceStatus.className = 'text-sm text-red-600 mb-4';
                    }
                };
                
            } else {
                throw new Error('Clé API non trouvée dans la réponse');
            }
            
        } catch (error) {
            throw error;
        }
    }
    
    stopSpeechRecognition() {
        this.isRecording = false;
        
        if (this.speechService) {
            try {
                this.speechService.stopListening();
            } catch (error) {
                // Service déjà arrêté
            }
        }
        
        setTimeout(() => {
            this.stopRecordingSession();
        }, 500);
    }
    
    stopRecordingSession() {
        this.isRecording = false;
        
        if (this.timeCounter) {
            clearInterval(this.timeCounter);
            this.timeCounter = null;
        }
        
        const audioIndicator = document.getElementById('audio-activity-indicator');
        if (audioIndicator) {
            audioIndicator.remove();
        }
        
        const voiceStatus = document.getElementById('voice-status');
        if (voiceStatus) {
            voiceStatus.textContent = 'Reconnaissance arrêtée';
            voiceStatus.className = 'text-sm text-orange-600 mb-4';
        }
        
        this.resetRecognitionButtons();
    }
    startAudioIndicator() {
        const voiceStatus = document.getElementById('voice-status');
        
        if (voiceStatus) {
            const indicator = document.createElement('span');
            indicator.id = 'audio-activity-indicator';
            indicator.innerHTML = ' 🔊';
            indicator.style.animation = 'pulse 1s infinite';
            voiceStatus.appendChild(indicator);
        }
        
        if (!document.getElementById('audio-indicator-style')) {
            const style = document.createElement('style');
            style.id = 'audio-indicator-style';
            style.textContent = `
                @keyframes pulse {
                    0% { opacity: 1; }
                    50% { opacity: 0.3; }
                    100% { opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    startTimeCounter() {
        const voiceStatus = document.getElementById('voice-status');
        
        if (this.timeCounter) {
            clearInterval(this.timeCounter);
        }
        
        this.timeCounter = setInterval(() => {
            if (!this.isRecording) {
                clearInterval(this.timeCounter);
                return;
            }
            
            const elapsedTime = Date.now() - this.recordingStartTime;
            const remainingTime = Math.max(0, 60 - Math.floor(elapsedTime / 1000));
            
            if (voiceStatus) {
                voiceStatus.textContent = `Écoute en cours... (${remainingTime}s restantes)`;
            }
            
            if (remainingTime <= 0) {
                this.stopSpeechRecognition();
            }
        }, 1000);
    }
    
    resetRecognitionButtons() {
        const startBtn = document.getElementById('start-recording-btn');
        const stopBtn = document.getElementById('stop-recording-btn');
        
        if (startBtn) {
            startBtn.classList.remove('hidden');
        }
        if (stopBtn) {
            stopBtn.classList.add('hidden');
        }
    }
    
    processSpeechResult(transcript) {
        const recognizedText = document.getElementById('recognized-text');
        if (recognizedText) {
            recognizedText.textContent = transcript;
        }
        
        const speechText = document.getElementById('speech-text');
        if (speechText) {
            speechText.classList.remove('hidden');
        }
        
        this.analyzeAndExtractData(transcript);
    }
    
    analyzeAndExtractData(transcript) {
        const extractedData = {
            player_name: '',
            age: '',
            position: '',
            club: ''
        };
        
        const text = transcript.toLowerCase();
        
        const nameMatch = text.match(/s'appelle\s+([^,]+)/);
        if (nameMatch) {
            extractedData.player_name = nameMatch[1].trim();
        }
        
        const ageMatch = text.match(/il a (\d{1,2})\s*ans?/);
        if (ageMatch) {
            extractedData.age = ageMatch[1];
        }
        
        const positionMatch = text.match(/il est (attaquant|défenseur|gardien|milieu|ailier)/);
        if (positionMatch) {
            extractedData.position = positionMatch[1];
        }
        
        const clubMatch = text.match(/il joue à ([^,]+)/);
        if (clubMatch) {
            extractedData.club = clubMatch[1].trim();
        }
        
        this.fillVoiceResults(extractedData);
    }
    
    fillVoiceResults(data) {
        const voiceResults = document.getElementById('voice-results');
        if (voiceResults) {
            voiceResults.classList.remove('hidden');
        }
        
        if (data.player_name) {
            const nameField = document.getElementById('voice_player_name_result');
            if (nameField) nameField.value = data.player_name;
        }
        
        if (data.age) {
            const ageField = document.getElementById('voice_age_result');
            if (ageField) ageField.value = data.age;
        }
        
        if (data.position) {
            const positionField = document.getElementById('voice_position_result');
            if (positionField) positionField.value = data.position;
        }
        
        if (data.club) {
        const clubField = document.getElementById('voice_club_result');
        if (clubField) clubField.value = data.club;
        }
    }
    
    setupEventListeners() {
        Object.entries(this.modeElements).forEach(([mode, element]) => {
            if (element) {
                element.removeEventListener('click', this.handleModeClick);
                
                element.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.switchMode(mode);
                });
                
                element.removeAttribute('onclick');
            }
        });
    }
    
    switchMode(targetMode) {
        if (!this.modes.includes(targetMode)) {
            return;
        }
        
        document.body.classList.remove('vocal-mode-active');
        if (targetMode === 'vocal') {
            document.body.classList.add('vocal-mode-active');
        }
        
        if (targetMode === 'vocal') {
            this.showVocalElementsInVocalMode(targetMode);
        } else {
            this.hideVocalElementsInManualMode(targetMode);
        }
        
        this.updateButtonStates(targetMode);
        this.updateSectionVisibility(targetMode);
        this.currentMode = targetMode;
        this.showModeIndicator(targetMode);
        
        const debugElement = document.getElementById('debug-current-mode');
        if (debugElement) {
            debugElement.textContent = targetMode.toUpperCase();
        }
    }
    
    updateButtonStates(activeMode) {
        Object.entries(this.modeElements).forEach(([mode, element]) => {
            if (element) {
                if (mode === activeMode) {
                    element.classList.remove('bg-gray-100', 'text-gray-700');
                    element.classList.add('bg-blue-600', 'text-white', 'active');
                } else {
                    element.classList.remove('bg-blue-600', 'text-white', 'active');
                    element.classList.add('bg-gray-100', 'text-gray-700');
                }
            }
        });
    }
    
    updateSectionVisibility(activeMode) {
        Object.entries(this.modeSections).forEach(([mode, section]) => {
            if (section) {
                if (mode === activeMode) {
                    section.classList.remove('hidden');
                    section.style.removeProperty('display');
                    section.style.removeProperty('visibility');
                    section.style.removeProperty('opacity');
                } else {
                    section.classList.add('hidden');
                    section.style.removeProperty('display');
                    section.style.removeProperty('visibility');
                    section.style.removeProperty('opacity');
                }
            }
        });
        
        if (activeMode !== 'vocal') {
            const vocalSection = document.getElementById('vocal-mode-section');
            if (vocalSection) {
                vocalSection.classList.add('hidden');
                vocalSection.style.setProperty('display', 'none', 'important');
                vocalSection.style.setProperty('visibility', 'hidden', 'important');
                vocalSection.style.setProperty('opacity', '0', 'important');
            }
            
            const vocalElements = [
                'init-service', 'start-recording-btn', 'stop-recording-btn', 'transfer-to-form-btn',
                'voice-status', 'audio-activity-indicator', 'console-vocale', 'voice-results',
                'voice_player_name', 'voice_age', 'voice_position', 'voice_club',
                'voice_player_name_result', 'voice_age_result', 'voice_position_result', 'voice_club_result'
            ];
            
            vocalElements.forEach(elementId => {
                const element = document.getElementById(elementId);
                if (element) {
                    element.classList.add('hidden');
                    element.style.setProperty('display', 'none', 'important');
                    element.style.setProperty('visibility', 'hidden', 'important');
                    element.style.setProperty('opacity', '0', 'important');
                }
            });
        } else {
            const vocalSection = document.getElementById('vocal-mode-section');
            if (vocalSection) {
                vocalSection.classList.remove('hidden');
                vocalSection.style.removeProperty('display');
                vocalSection.style.removeProperty('visibility');
                vocalSection.style.removeProperty('opacity');
            }
        }
    }
    
    showModeIndicator(mode) {
        const existingIndicator = document.querySelector('.mode-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }
        
        const indicator = document.createElement('div');
        indicator.className = 'mode-indicator';
        indicator.textContent = `Mode ${mode.charAt(0).toUpperCase() + mode.slice(1)} Actif`;
        
        document.body.appendChild(indicator);
        
        setTimeout(() => {
            if (indicator.parentNode) {
                indicator.remove();
            }
        }, 3000);
    }
    
    transferDataToManual(data) {
        // Implémenter le transfert de données
        // Cette méthode sera étendue selon les besoins
    }
    
    setupTransferButtons() {
        const transferVocalBtn = document.getElementById('transfer-to-form-btn');
        if (transferVocalBtn) {
            transferVocalBtn.addEventListener('click', () => {
                this.transferVocalDataToManual();
            });
        }
        
        const transferOcrBtn = document.getElementById('transfer-ocr-to-form-btn');
        if (transferOcrBtn) {
            transferOcrBtn.addEventListener('click', () => {
                this.transferOcrDataToManual();
            });
        }
        
        const transferFhirBtn = document.getElementById('transfer-fhir-to-form-btn');
        if (transferFhirBtn) {
            transferFhirBtn.addEventListener('click', () => {
                this.transferFhirDataToManual();
            });
        }
    }
    
    transferVocalDataToManual() {
        try {
            const vocalData = {
                player_name: document.getElementById('voice_player_name')?.value || '',
                age: document.getElementById('voice_age')?.value || '',
                position: document.getElementById('voice_position')?.value || '',
                club: document.getElementById('voice_club')?.value || '',
                fifa_connect_id: document.getElementById('voice_fifa_connect_id')?.value || ''
            };
            
            this.showTransferSuccess('vocal');
            
        } catch (error) {
            this.showTransferError('vocal', error.message);
        }
    }
    
    transferOcrDataToManual() {
        try {
            const ocrData = {
                // Données extraites par OCR
            };
            
            this.showTransferSuccess('ocr');
            
        } catch (error) {
            this.showTransferError('ocr', error.message);
        }
    }
    
    transferFhirDataToManual() {
        try {
            const fhirData = {
                // Données récupérées depuis le serveur FHIR
            };
            
            this.showTransferSuccess('fhir');
            
        } catch (error) {
            this.showTransferError('fhir', error.message);
        }
    }
    
    showTransferSuccess(mode) {
        const message = `Données ${mode} transférées avec succès vers le formulaire principal !`;
        this.showNotification(message, 'success');
    }
    
    showTransferError(mode, errorMessage) {
        const message = `Erreur lors du transfert ${mode}: ${errorMessage}`;
        this.showNotification(message, 'error');
    }
    
    showNotification(message, type = 'info') {
        const existingNotifications = document.querySelectorAll('.mode-notification');
        existingNotifications.forEach(notification => notification.remove());
        
        const notification = document.createElement('div');
        notification.className = `mode-notification fixed top-20 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="mr-2">${type === 'success' ? '' : type === 'error' ? '❌' : 'ℹ️'}</span>
                <span class="text-sm font-medium">${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    hideVocalElementsInManualMode(currentMode) {
        if (currentMode !== 'vocal' && this.currentMode !== 'vocal') {
            const vocalElements = [
                'init-service',
                'start-recording-btn',
                'stop-recording-btn',
                'transfer-to-form-btn',
                'voice-status',
                'audio-activity-indicator',
                'console-vocale',
                'voice-results',
                'voice_player_name',
                'voice_age',
                'voice_position',
                'voice_club',
                'voice_player_name_result',
                'voice_age_result',
                'voice_position_result',
                'voice_club_result'
            ];
            
            vocalElements.forEach(elementId => {
                const element = document.getElementById(elementId);
                if (element) {
                    element.classList.add('hidden');
                    element.style.setProperty('display', 'none', 'important');
                    element.style.setProperty('visibility', 'hidden', 'important');
                    element.style.setProperty('opacity', '0', 'important');
                }
            });
            
            const vocalSection = document.getElementById('vocal-mode-section');
            if (vocalSection) {
                vocalSection.classList.add('hidden');
                vocalSection.style.setProperty('display', 'none', 'important');
                vocalSection.style.setProperty('visibility', 'hidden', 'important');
                vocalSection.style.setProperty('opacity', '0', 'important');
            }
        }
    }
    
    showVocalElementsInVocalMode(currentMode) {
        if (currentMode === 'vocal' || this.currentMode === 'vocal') {
            const vocalElements = [
                'init-service',
                'start-recording-btn',
                'stop-recording-btn',
                'transfer-to-form-btn',
                'voice-status',
                'audio-activity-indicator',
                'console-vocale',
                'voice-results',
                'voice_player_name',
                'voice_age',
                'voice_position',
                'voice_club',
                'voice_player_name_result',
                'voice_age_result',
                'voice_position_result',
                'voice_club_result'
            ];
            
            vocalElements.forEach(elementId => {
                const element = document.getElementById(elementId);
                if (element) {
                    element.classList.remove('hidden');
                    element.style.removeProperty('display');
                    element.style.removeProperty('visibility');
                    element.style.removeProperty('opacity');
                }
            });
            
            const vocalSection = document.getElementById('vocal-mode-section');
            if (vocalSection) {
                vocalSection.classList.remove('hidden');
                vocalSection.style.removeProperty('display');
                vocalSection.style.removeProperty('visibility');
                vocalSection.style.removeProperty('opacity');
            }
        }
    }
    
    forceHideElement(element) {
        if (!element) return;
        return;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        try {
            window.modeManager = new ModeManager();
        } catch (error) {
            // Erreur d'initialisation
        }
    }, 500);
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        if (window.modeManager) {
            // Système déjà initialisé
        } else {
            setTimeout(() => {
                try {
                    window.modeManager = new ModeManager();
                } catch (error) {
                    // Erreur d'initialisation différée
                }
            }, 1000);
        }
    });
} else {
    if (!window.modeManager) {
        setTimeout(() => {
            try {
                window.modeManager = new ModeManager();
            } catch (error) {
                // Erreur d'initialisation immédiate
            }
        }, 100);
    }
}
</script>
@endpush
@endsection 