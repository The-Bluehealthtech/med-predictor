<!-- Onglet: Catégories Médicales -->
<div class="space-y-6">
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">🏥 Catégories Médicales (ICD-10, SNOMED CT, LOINC)</h3>
        <p class="text-purple-700 mb-4">Classification standardisée des diagnostics et procédures médicales</p>
        
        <!-- ICD-10 Diagnoses -->
        <div class="space-y-4">
            <div>
                <label for="icd10_diagnoses" class="block text-sm font-medium text-gray-700 mb-2">
                    🏷️ Diagnostics ICD-10
                </label>
                <select id="icd10_diagnoses" name="icd10_diagnoses[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" size="8">
                    <optgroup label="🫀 Maladies Cardiovasculaires (I00-I99)">
                        <option value="I10">I10 - Hypertension essentielle (primitive)</option>
                        <option value="I21.9">I21.9 - Infarctus aigu du myocarde, sans précision</option>
                        <option value="I50.9">I50.9 - Insuffisance cardiaque, sans précision</option>
                        <option value="I63.9">I63.9 - Infarctus cérébral, sans précision</option>
                    </optgroup>
                    <optgroup label="🫁 Maladies Respiratoires (J00-J99)">
                        <option value="J44.9">J44.9 - Bronchopneumopathie chronique obstructive, sans précision</option>
                        <option value="J45.9">J45.9 - Asthme, sans précision</option>
                        <option value="J18.9">J18.9 - Pneumonie, sans précision</option>
                    </optgroup>
                    <optgroup label="🦴 Maladies Musculo-squelettiques (M00-M99)">
                        <option value="M79.3">M79.3 - Douleur dans les membres</option>
                        <option value="M54.5">M54.5 - Lombalgie, sans précision</option>
                        <option value="S93.4">S93.4 - Entorse de la cheville</option>
                    </optgroup>
                    <optgroup label="🩺 Maladies Endocriniennes (E00-E89)">
                        <option value="E11.9">E11.9 - Diabète sucré de type 2 sans complications</option>
                        <option value="E04.9">E04.9 - Goitre non toxique, sans précision</option>
                    </optgroup>
                </select>
                <p class="text-xs text-gray-500 mt-1">Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs diagnostics</p>
            </div>
        </div>
    </div>

    <!-- SNOMED CT -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">🔬 SNOMED CT - Terminologie Clinique</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="snomed_findings" class="block text-sm font-medium text-gray-700 mb-2">
                    🔍 Constatations Cliniques
                </label>
                <select id="snomed_findings" name="snomed_findings[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" size="6">
                    <option value="386661006">386661006 - Fièvre (constatation)</option>
                    <option value="25064002">25064002 - Douleur thoracique</option>
                    <option value="267036007">267036007 - Dyspnée</option>
                    <option value="422587007">422587007 - Nausée</option>
                    <option value="2470005">2470005 - Fatigue</option>
                    <option value="300904002">300904002 - Vertiges</option>
                </select>
            </div>
            
            <div>
                <label for="snomed_procedures" class="block text-sm font-medium text-gray-700 mb-2">
                    🏥 Procédures Médicales
                </label>
                <select id="snomed_procedures" name="snomed_procedures[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" size="6">
                    <option value="169690007">169690007 - Examen physique</option>
                    <option value="410006001">410006001 - Électrocardiogramme</option>
                    <option value="71651007">71651007 - Radiographie thoracique</option>
                    <option value="166001000">166001000 - Analyse sanguine</option>
                    <option value="430193006">430193006 - Échographie</option>
                    <option value="24165007">24165007 - Tomodensitométrie</option>
                </select>
            </div>
        </div>
    </div>

    <!-- LOINC -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">🧪 LOINC - Observations de Laboratoire</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="loinc_lab_tests" class="block text-sm font-medium text-gray-700 mb-2">
                    🧬 Tests de Laboratoire
                </label>
                <select id="loinc_lab_tests" name="loinc_lab_tests[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" size="8">
                    <optgroup label="🩸 Hématologie">
                        <option value="789-8">789-8 - Numération des globules rouges</option>
                        <option value="718-7">718-7 - Hémoglobine</option>
                        <option value="4544-3">4544-3 - Hématocrite</option>
                        <option value="777-3">777-3 - Numération des plaquettes</option>
                        <option value="6690-2">6690-2 - Numération des globules blancs</option>
                    </optgroup>
                    <optgroup label="🧪 Biochimie">
                        <option value="2345-7">2345-7 - Glucose</option>
                        <option value="2160-0">2160-0 - Créatinine</option>
                        <option value="2951-2">2951-2 - Sodium</option>
                        <option value="2823-3">2823-3 - Potassium</option>
                        <option value="2075-0">2075-0 - Chlorure</option>
                    </optgroup>
                    <optgroup label="🫀 Marqueurs Cardiaques">
                        <option value="10839-9">10839-9 - Troponine I</option>
                        <option value="10840-7">10840-7 - Troponine T</option>
                        <option value="2156-8">2156-8 - CK-MB</option>
                        <option value="10835-7">10835-7 - BNP</option>
                    </optgroup>
                </select>
            </div>
            
            <div>
                <label for="loinc_vital_signs" class="block text-sm font-medium text-gray-700 mb-2">
                    💓 Signes Vitaux
                </label>
                <select id="loinc_vital_signs" name="loinc_vital_signs[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" size="8">
                    <option value="85354-9">85354-9 - Tension artérielle systolique</option>
                    <option value="8462-4">8462-4 - Tension artérielle diastolique</option>
                    <option value="8867-4">8867-4 - Fréquence cardiaque</option>
                    <option value="8310-5">8310-5 - Température corporelle</option>
                    <option value="2708-6">2708-6 - Saturation en oxygène</option>
                    <option value="8302-2">8302-2 - Taille</option>
                    <option value="29463-7">29463-7 - Poids</option>
                    <option value="39156-5">39156-5 - Indice de masse corporelle</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Medical Categories Summary -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-4">📊 Résumé des Catégories</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-800 mb-2">🏷️ ICD-10</h4>
                <div id="icd10-summary" class="text-sm text-gray-700">
                    <span class="text-yellow-600">0</span> diagnostic(s) sélectionné(s)
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-800 mb-2">🔬 SNOMED CT</h4>
                <div id="snomed-summary" class="text-sm text-gray-700">
                    <span class="text-yellow-600">0</span> constatation(s) / procédure(s)
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-800 mb-2">🧪 LOINC</h4>
                <div id="loinc-summary" class="text-sm text-gray-700">
                    <span class="text-yellow-600">0</span> test(s) sélectionné(s)
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Medical Categories Management
document.addEventListener('DOMContentLoaded', function() {
    initializeMedicalCategories();
});

function initializeMedicalCategories() {
    // Add event listeners for summary updates
    const selects = ['icd10_diagnoses', 'snomed_findings', 'snomed_procedures', 'loinc_lab_tests', 'loinc_vital_signs'];
    
    selects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select) {
            select.addEventListener('change', updateMedicalCategoriesSummary);
        }
    });
    
    // Initial summary update
    updateMedicalCategoriesSummary();
}

function updateMedicalCategoriesSummary() {
    // Update ICD-10 summary
    const icd10Select = document.getElementById('icd10_diagnoses');
    const icd10Count = icd10Select ? icd10Select.selectedOptions.length : 0;
    const icd10Summary = document.getElementById('icd10-summary');
    if (icd10Summary) {
        icd10Summary.innerHTML = `<span class="text-yellow-600">${icd10Count}</span> diagnostic(s) sélectionné(s)`;
    }
    
    // Update SNOMED CT summary
    const snomedFindings = document.getElementById('snomed_findings');
    const snomedProcedures = document.getElementById('snomed_procedures');
    const snomedCount = (snomedFindings ? snomedFindings.selectedOptions.length : 0) + 
                       (snomedProcedures ? snomedProcedures.selectedOptions.length : 0);
    const snomedSummary = document.getElementById('snomed-summary');
    if (snomedSummary) {
        snomedSummary.innerHTML = `<span class="text-yellow-600">${snomedCount}</span> constatation(s) / procédure(s)`;
    }
    
    // Update LOINC summary
    const loincLabTests = document.getElementById('loinc_lab_tests');
    const loincVitalSigns = document.getElementById('loinc_vital_signs');
    const loincCount = (loincLabTests ? loincLabTests.selectedOptions.length : 0) + 
                      (loincVitalSigns ? loincVitalSigns.selectedOptions.length : 0);
    const loincSummary = document.getElementById('loinc-summary');
    if (loincSummary) {
        loincSummary.innerHTML = `<span class="text-yellow-600">${loincCount}</span> test(s) sélectionné(s)`;
    }
}
</script> 