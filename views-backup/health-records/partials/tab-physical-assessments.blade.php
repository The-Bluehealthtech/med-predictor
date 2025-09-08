<!-- Onglet: Évaluations Physiques -->
<div class="space-y-6">
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">💪 Évaluations Physiques</h3>
        <p class="text-green-700 mb-4">Tests et évaluations physiques complètes</p>
        
        <!-- Cardiovascular Assessment -->
        <div class="space-y-4">
            <h4 class="text-md font-semibold text-gray-800">🫀 Évaluation Cardiovasculaire</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="cardio_blood_pressure" class="block text-sm font-medium text-gray-700 mb-2">
                        Tension Artérielle (mmHg)
                    </label>
                    <div class="flex space-x-2">
                        <input 
                            type="number" 
                            id="cardio_systolic" 
                            name="cardio_systolic"
                            placeholder="Systolique"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <span class="self-center text-gray-500">/</span>
                        <input 
                            type="number" 
                            id="cardio_diastolic" 
                            name="cardio_diastolic"
                            placeholder="Diastolique"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                    </div>
                </div>
                
                <div>
                    <label for="cardio_heart_rate" class="block text-sm font-medium text-gray-700 mb-2">
                        Fréquence Cardiaque (bpm)
                    </label>
                    <input 
                        type="number" 
                        id="cardio_heart_rate" 
                        name="cardio_heart_rate"
                        min="40" max="200"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                </div>
                
                <div>
                    <label for="cardio_oxygen_saturation" class="block text-sm font-medium text-gray-700 mb-2">
                        Saturation O₂ (%)
                    </label>
                    <input 
                        type="number" 
                        id="cardio_oxygen_saturation" 
                        name="cardio_oxygen_saturation"
                        min="70" max="100"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="cardio_ecg" class="block text-sm font-medium text-gray-700 mb-2">
                        ECG
                    </label>
                    <select 
                        id="cardio_ecg" 
                        name="cardio_ecg"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                        <option value="">Sélectionner</option>
                        <option value="normal">Normal</option>
                        <option value="abnormal">Anormal</option>
                        <option value="not_performed">Non effectué</option>
                    </select>
                </div>
                
                <div>
                    <label for="cardio_ecg_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes ECG
                    </label>
                    <textarea 
                        id="cardio_ecg_notes" 
                        name="cardio_ecg_notes"
                        rows="2"
                        placeholder="Observations ECG..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    ></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Musculoskeletal Assessment -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">🦴 Évaluation Musculo-squelettique</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">Force Musculaire</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Membres supérieurs</span>
                        <select name="muscle_strength_upper" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="5">5/5 - Normal</option>
                            <option value="4">4/5 - Bon</option>
                            <option value="3">3/5 - Moyen</option>
                            <option value="2">2/5 - Faible</option>
                            <option value="1">1/5 - Très faible</option>
                            <option value="0">0/5 - Aucune</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Membres inférieurs</span>
                        <select name="muscle_strength_lower" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="5">5/5 - Normal</option>
                            <option value="4">4/5 - Bon</option>
                            <option value="3">3/5 - Moyen</option>
                            <option value="2">2/5 - Faible</option>
                            <option value="1">1/5 - Très faible</option>
                            <option value="0">0/5 - Aucune</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Tronc</span>
                        <select name="muscle_strength_trunk" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="5">5/5 - Normal</option>
                            <option value="4">4/5 - Bon</option>
                            <option value="3">3/5 - Moyen</option>
                            <option value="2">2/5 - Faible</option>
                            <option value="1">1/5 - Très faible</option>
                            <option value="0">0/5 - Aucune</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">Amplitude Articulaire</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Épaules</span>
                        <select name="rom_shoulders" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normale</option>
                            <option value="limited">Limitée</option>
                            <option value="restricted">Restreinte</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Genoux</span>
                        <select name="rom_knees" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normale</option>
                            <option value="limited">Limitée</option>
                            <option value="restricted">Restreinte</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Chevilles</span>
                        <select name="rom_ankles" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normale</option>
                            <option value="limited">Limitée</option>
                            <option value="restricted">Restreinte</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <label for="musculoskeletal_notes" class="block text-sm font-medium text-gray-700 mb-2">
                Notes Musculo-squelettiques
            </label>
            <textarea 
                id="musculoskeletal_notes" 
                name="musculoskeletal_notes"
                rows="3"
                placeholder="Observations, limitations, douleurs..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
        </div>
    </div>

    <!-- Neurological Assessment -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">🧠 Évaluation Neurologique</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">Réflexes</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Réflexes rotuliens</span>
                        <select name="reflexes_patellar" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="hyperactive">Hyperactif</option>
                            <option value="hypoactive">Hypoactif</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Réflexes achilléens</span>
                        <select name="reflexes_achilles" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="hyperactive">Hyperactif</option>
                            <option value="hypoactive">Hypoactif</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Réflexes plantaires</span>
                        <select name="reflexes_plantar" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="brisk">Vif</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">Sensibilité</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Tact</span>
                        <select name="sensation_touch" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="decreased">Diminué</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Douleur</span>
                        <select name="sensation_pain" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="decreased">Diminué</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Proprioception</span>
                        <select name="sensation_proprioception" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="normal">Normal</option>
                            <option value="decreased">Diminué</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <label for="neurological_notes" class="block text-sm font-medium text-gray-700 mb-2">
                Notes Neurologiques
            </label>
            <textarea 
                id="neurological_notes" 
                name="neurological_notes"
                rows="3"
                placeholder="Observations neurologiques..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            ></textarea>
        </div>
    </div>

    <!-- Functional Assessment -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-4">🏃 Évaluation Fonctionnelle</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">Tests de Performance</h4>
                <div class="space-y-3">
                    <div>
                        <label for="functional_walk_test" class="block text-sm font-medium text-gray-700 mb-2">
                            Test de marche 6 minutes (m)
                        </label>
                        <input 
                            type="number" 
                            id="functional_walk_test" 
                            name="functional_walk_test"
                            min="0" max="1000"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="functional_balance_test" class="block text-sm font-medium text-gray-700 mb-2">
                            Test d'équilibre (sec)
                        </label>
                        <input 
                            type="number" 
                            id="functional_balance_test" 
                            name="functional_balance_test"
                            min="0" max="300"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="functional_strength_test" class="block text-sm font-medium text-gray-700 mb-2">
                            Test de force (reps)
                        </label>
                        <input 
                            type="number" 
                            id="functional_strength_test" 
                            name="functional_strength_test"
                            min="0" max="100"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        >
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">Activités de la Vie Quotidienne</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Marche</span>
                        <select name="adl_walking" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="independent">Indépendant</option>
                            <option value="assisted">Avec assistance</option>
                            <option value="dependent">Dépendant</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Transferts</span>
                        <select name="adl_transfers" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="independent">Indépendant</option>
                            <option value="assisted">Avec assistance</option>
                            <option value="dependent">Dépendant</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Habillement</span>
                        <select name="adl_dressing" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="independent">Indépendant</option>
                            <option value="assisted">Avec assistance</option>
                            <option value="dependent">Dépendant</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Hygiene</span>
                        <select name="adl_hygiene" class="px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">-</option>
                            <option value="independent">Indépendant</option>
                            <option value="assisted">Avec assistance</option>
                            <option value="dependent">Dépendant</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <label for="functional_notes" class="block text-sm font-medium text-gray-700 mb-2">
                Notes Fonctionnelles
            </label>
            <textarea 
                id="functional_notes" 
                name="functional_notes"
                rows="3"
                placeholder="Observations fonctionnelles, limitations..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
            ></textarea>
        </div>
    </div>
</div> 