<!-- Onglet: Catégories Médicales -->
<div class="space-y-6">
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">{{ __('health_records_create.medical_categories_heading') }}</h3>
        <p class="text-purple-700 mb-4">{{ __('health_records_create.medical_categories_subtitle') }}</p>
        
        <!-- ICD-10 Diagnoses -->
        <div class="space-y-4">
            <div>
                <label for="icd10_diagnoses" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_create.icd10_diagnoses_label') }}
                </label>
                <select id="icd10_diagnoses" name="icd10_diagnoses[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" size="8">
                    <optgroup label="🫀 Maladies Cardiovasculaires (I00-I99)">
                        <option value="I10">{{ __('health_records_create.icd10_i10') }}</option>
                        <option value="I21.9">{{ __('health_records_create.icd10_i21_9') }}</option>
                        <option value="I50.9">{{ __('health_records_create.icd10_i50_9') }}</option>
                        <option value="I63.9">{{ __('health_records_create.icd10_i63_9') }}</option>
                    </optgroup>
                    <optgroup label="🫁 Maladies Respiratoires (J00-J99)">
                        <option value="J44.9">{{ __('health_records_create.icd10_j44_9') }}</option>
                        <option value="J45.9">{{ __('health_records_create.icd10_j45_9') }}</option>
                        <option value="J18.9">{{ __('health_records_create.icd10_j18_9') }}</option>
                    </optgroup>
                    <optgroup label="🦴 Maladies Musculo-squelettiques (M00-M99)">
                        <option value="M79.3">{{ __('health_records_create.icd10_m79_3') }}</option>
                        <option value="M54.5">{{ __('health_records_create.icd10_m54_5') }}</option>
                        <option value="S93.4">{{ __('health_records_create.icd10_s93_4') }}</option>
                    </optgroup>
                    <optgroup label="🩺 Maladies Endocriniennes (E00-E89)">
                        <option value="E11.9">{{ __('health_records_extra.label_697794ed20ec') }}</option>
                        <option value="E04.9">{{ __('health_records_extra.label_8666d03047ad') }}</option>
                    </optgroup>
                </select>
                <p class="text-xs text-gray-500 mt-1">{{ __('health_records_create.icd10_multi_select_hint') }}</p>
            </div>
        </div>
    </div>

    <!-- SNOMED CT -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('health_records_extra.label_8611f54be5fc') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="snomed_findings" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_2f8dceb21fd2') }}
                </label>
                <select id="snomed_findings" name="snomed_findings[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" size="6">
                    <option value="386661006">{{ __('health_records_extra.label_165e0746c8a8') }}</option>
                    <option value="25064002">{{ __('health_records_extra.label_88a784f6ef86') }}</option>
                    <option value="267036007">{{ __('health_records_extra.label_0a16c632f442') }}</option>
                    <option value="422587007">{{ __('health_records_extra.label_e880b51172b1') }}</option>
                    <option value="2470005">2470005 - Fatigue</option>
                    <option value="300904002">{{ __('health_records_extra.label_6d9e9d124e8e') }}</option>
                </select>
            </div>
            
            <div>
                <label for="snomed_procedures" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_07973e92c7bf') }}
                </label>
                <select id="snomed_procedures" name="snomed_procedures[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" size="6">
                    <option value="169690007">{{ __('health_records_extra.label_08ba1b1e8b85') }}</option>
                    <option value="410006001">{{ __('health_records_extra.label_b276af874807') }}</option>
                    <option value="71651007">{{ __('health_records_extra.label_2590b5dbbcb9') }}</option>
                    <option value="166001000">{{ __('health_records_extra.label_f21c8b85c193') }}</option>
                    <option value="430193006">{{ __('health_records_extra.label_342e6e2f99eb') }}</option>
                    <option value="24165007">{{ __('health_records_extra.label_83083e2c15cf') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- LOINC -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('health_records_extra.label_8dda633a25a9') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="loinc_lab_tests" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('health_records_extra.label_622ce6e620e8') }}
                </label>
                <select id="loinc_lab_tests" name="loinc_lab_tests[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" size="8">
                    <optgroup label="🩸 Hématologie">
                        <option value="789-8">{{ __('health_records_extra.label_13d3f2f85bca') }}</option>
                        <option value="718-7">{{ __('health_records_extra.label_6950bdf44124') }}</option>
                        <option value="4544-3">{{ __('health_records_extra.label_0ad8d00d1dc4') }}</option>
                        <option value="777-3">{{ __('health_records_extra.label_4c36d5863e97') }}</option>
                        <option value="6690-2">{{ __('health_records_extra.label_5501135ad4f9') }}</option>
                    </optgroup>
                    <optgroup label="🧪 Biochimie">
                        <option value="2345-7">2345-7 - Glucose</option>
                        <option value="2160-0">{{ __('health_records_extra.label_65beab5fa6fa') }}</option>
                        <option value="2951-2">2951-2 - Sodium</option>
                        <option value="2823-3">2823-3 - Potassium</option>
                        <option value="2075-0">{{ __('health_records_extra.label_9b0a240d6dc1') }}</option>
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
                    {{ __('health_records_create.vital_signs_heading') }}
                </label>
                <select id="loinc_vital_signs" name="loinc_vital_signs[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" size="8">
                    <option value="85354-9">{{ __('health_records_extra.label_4972829bd79f') }}</option>
                    <option value="8462-4">{{ __('health_records_extra.label_322e5e3f7702') }}</option>
                    <option value="8867-4">{{ __('health_records_extra.label_eb9a10372334') }}</option>
                    <option value="8310-5">{{ __('health_records_extra.label_a67ae4188b2f') }}</option>
                    <option value="2708-6">{{ __('health_records_extra.label_75d417ad7201') }}</option>
                    <option value="8302-2">{{ __('health_records_extra.label_27b6aaaa7af5') }}</option>
                    <option value="29463-7">{{ __('health_records_extra.label_cfd6dad55d15') }}</option>
                    <option value="39156-5">{{ __('health_records_extra.label_cbb36ffd2f29') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Medical Categories Summary -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-4">{{ __('health_records_extra.label_44e407c4a5f6') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-800 mb-2">🏷️ ICD-10</h4>
                <div id="icd10-summary" class="text-sm text-gray-700">
                    <span class="text-yellow-600">0</span> {{ __('health_records_extra.label_f9568b521cb0') }}
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-800 mb-2">🔬 SNOMED CT</h4>
                <div id="snomed-summary" class="text-sm text-gray-700">
                    <span class="text-yellow-600">0</span> {{ __('health_records_extra.label_53d256db17fe') }}
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-800 mb-2">🧪 LOINC</h4>
                <div id="loinc-summary" class="text-sm text-gray-700">
                    <span class="text-yellow-600">0</span> {{ __('health_records_extra.label_ee9261b4eda6') }}
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