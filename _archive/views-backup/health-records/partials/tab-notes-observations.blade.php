<!-- Onglet: Notes et Observations -->
<div class="space-y-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">📝 Notes et Observations Cliniques</h3>
        <p class="text-blue-700 mb-4">Notes détaillées et observations cliniques</p>
        
        <!-- Clinical Notes -->
        <div class="space-y-4">
            <div>
                <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    📋 Notes Cliniques Détaillées
                </label>
                <textarea 
                    id="clinical_notes" 
                    name="clinical_notes"
                    rows="8"
                    placeholder="Décrivez en détail l'examen clinique, les observations, les symptômes rapportés par le patient..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >{{ old('clinical_notes') }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="physical_examination" class="block text-sm font-medium text-gray-700 mb-2">
                        🩺 Examen Physique
                    </label>
                    <textarea 
                        id="physical_examination" 
                        name="physical_examination"
                        rows="6"
                        placeholder="Résultats de l'examen physique, signes cliniques observés..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >{{ old('physical_examination') }}</textarea>
                </div>
                
                <div>
                    <label for="differential_diagnosis" class="block text-sm font-medium text-gray-700 mb-2">
                        🤔 Diagnostic Différentiel
                    </label>
                    <textarea 
                        id="differential_diagnosis" 
                        name="differential_diagnosis"
                        rows="6"
                        placeholder="Hypothèses diagnostiques à considérer..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >{{ old('differential_diagnosis') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Treatment Plan -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">💊 Plan de Traitement</h3>
        
        <div class="space-y-4">
            <div>
                <label for="treatment_plan" class="block text-sm font-medium text-gray-700 mb-2">
                    📋 Plan de Traitement Proposé
                </label>
                <textarea 
                    id="treatment_plan" 
                    name="treatment_plan"
                    rows="6"
                    placeholder="Décrivez le plan de traitement proposé, les médicaments, les recommandations..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >{{ old('treatment_plan') }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="medications_prescribed" class="block text-sm font-medium text-gray-700 mb-2">
                        💊 Médicaments Prescrits
                    </label>
                    <textarea 
                        id="medications_prescribed" 
                        name="medications_prescribed"
                        rows="4"
                        placeholder="Liste des médicaments prescrits avec posologie..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >{{ old('medications_prescribed') }}</textarea>
                </div>
                
                <div>
                    <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                        💡 Recommandations
                    </label>
                    <textarea 
                        id="recommendations" 
                        name="recommendations"
                        rows="4"
                        placeholder="Recommandations pour le patient (mode de vie, suivi...)..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >{{ old('recommendations') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Follow-up Plan -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-4">📅 Plan de Suivi</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="follow_up_date" class="block text-sm font-medium text-gray-700 mb-2">
                    📅 Date de Suivi Proposée
                </label>
                <input 
                    type="date" 
                    id="follow_up_date" 
                    name="follow_up_date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label for="follow_up_type" class="block text-sm font-medium text-gray-700 mb-2">
                    🏥 Type de Suivi
                </label>
                <select 
                    id="follow_up_type" 
                    name="follow_up_type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                >
                    <option value="">Sélectionner</option>
                    <option value="consultation">Consultation de suivi</option>
                    <option value="telephone">Appel téléphonique</option>
                    <option value="video">Visio-consultation</option>
                    <option value="emergency">Suivi d'urgence</option>
                    <option value="specialist">Consultation spécialiste</option>
                </select>
            </div>
        </div>
        
        <div class="mt-4">
            <label for="follow_up_notes" class="block text-sm font-medium text-gray-700 mb-2">
                📝 Notes de Suivi
            </label>
            <textarea 
                id="follow_up_notes" 
                name="follow_up_notes"
                rows="4"
                placeholder="Points à vérifier lors du suivi, critères d'amélioration..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
            >{{ old('follow_up_notes') }}</textarea>
        </div>
    </div>

    <!-- Referrals -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">🏥 Orientations et Références</h3>
        
        <div class="space-y-4">
            <div>
                <label for="referrals" class="block text-sm font-medium text-gray-700 mb-2">
                    🏥 Orientations Prescrites
                </label>
                <textarea 
                    id="referrals" 
                    name="referrals"
                    rows="4"
                    placeholder="Spécialistes vers lesquels orienter le patient..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >{{ old('referrals') }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="urgent_referral" class="block text-sm font-medium text-gray-700 mb-2">
                        🚨 Orientation Urgente
                    </label>
                    <select 
                        id="urgent_referral" 
                        name="urgent_referral"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="">Aucune</option>
                        <option value="emergency_room">Service d'urgence</option>
                        <option value="cardiology">Cardiologie</option>
                        <option value="neurology">Neurologie</option>
                        <option value="orthopedics">Orthopédie</option>
                        <option value="surgery">Chirurgie</option>
                    </select>
                </div>
                
                <div>
                    <label for="referral_urgency" class="block text-sm font-medium text-gray-700 mb-2">
                        ⏰ Niveau d'Urgence
                    </label>
                    <select 
                        id="referral_urgency" 
                        name="referral_urgency"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="">Sélectionner</option>
                        <option value="immediate">Immédiat</option>
                        <option value="urgent">Urgent (24-48h)</option>
                        <option value="routine">Routine (1-2 semaines)</option>
                        <option value="elective">Électif (1-3 mois)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Consent and Legal -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-red-900 mb-4">📋 Consentement et Aspects Légaux</h3>
        
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="informed_consent" class="block text-sm font-medium text-gray-700 mb-2">
                        ✅ Consentement Éclairé
                    </label>
                    <select 
                        id="informed_consent" 
                        name="informed_consent"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    >
                        <option value="">Sélectionner</option>
                        <option value="obtained">Obtenu</option>
                        <option value="refused">Refusé</option>
                        <option value="not_applicable">Non applicable</option>
                        <option value="emergency">Consentement d'urgence</option>
                    </select>
                </div>
                
                <div>
                    <label for="legal_guardian" class="block text-sm font-medium text-gray-700 mb-2">
                        👥 Tuteur Légal
                    </label>
                    <input 
                        type="text" 
                        id="legal_guardian" 
                        name="legal_guardian"
                        placeholder="Nom du tuteur légal si applicable"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    >
                </div>
            </div>
            
            <div>
                <label for="legal_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    ⚖️ Notes Légales
                </label>
                <textarea 
                    id="legal_notes" 
                    name="legal_notes"
                    rows="3"
                    placeholder="Aspects légaux particuliers, déclarations du patient..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                >{{ old('legal_notes') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Summary and Final Notes -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Résumé et Notes Finales</h3>
        
        <div class="space-y-4">
            <div>
                <label for="visit_summary" class="block text-sm font-medium text-gray-700 mb-2">
                    📋 Résumé de la Visite
                </label>
                <textarea 
                    id="visit_summary" 
                    name="visit_summary"
                    rows="4"
                    placeholder="Résumé concis de la visite, points clés..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                >{{ old('visit_summary') }}</textarea>
            </div>
            
            <div>
                <label for="final_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Notes Finales
                </label>
                <textarea 
                    id="final_notes" 
                    name="final_notes"
                    rows="4"
                    placeholder="Observations finales, impressions cliniques..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                >{{ old('final_notes') }}</textarea>
            </div>
        </div>
    </div>
</div> 