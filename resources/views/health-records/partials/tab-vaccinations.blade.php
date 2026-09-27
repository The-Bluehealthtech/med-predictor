<!-- Onglet: Vaccinations -->
<div class="space-y-6">
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('health_records_extra.label_310058e44628') }}</h3>
        <p class="text-green-700 mb-4">{{ __('health_records_extra.label_9421c42c7cb3') }}</p>
        
        <!-- Vaccination Records -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h4 class="text-md font-semibold text-gray-800">{{ __('health_records_extra.label_327976b60601') }}</h4>
                <button 
                    type="button" 
                    id="add-vaccination-btn"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                    {{ __('health_records_extra.label_edf1eca48d89') }}
                </button>
            </div>
            
            <!-- Vaccination List -->
            <div id="vaccination-list" class="space-y-3">
                <!-- Vaccinations will be added here dynamically -->
            </div>
            
            <!-- Add Vaccination Form -->
            <div id="add-vaccination-form" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                <h5 class="text-md font-semibold text-gray-800 mb-4">{{ __('health_records_extra.label_4ad59e37f332') }}</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_3c3832129d33') }}
                        </label>
                        <select 
                            id="vaccine_name" 
                            name="vaccine_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                            <option value="">{{ __('health_records_extra.label_da1f98c03926') }}</option>
                            <optgroup label="🏥 Vaccins Obligatoires">
                                <option value="diphtheria_tetanus_polio">{{ __('health_records_extra.label_7aaee3bd7c26') }}</option>
                                <option value="measles_mumps_rubella">{{ __('health_records_extra.label_800bc9323299') }}</option>
                                <option value="hepatitis_b">{{ __('health_records_create.vaccine_hepatitis_b') }}</option>
                                <option value="pneumococcal">{{ __('health_records_create.vaccine_pneumococcal') }}</option>
                                <option value="meningococcal">{{ __('health_records_create.vaccine_meningococcal') }}</option>
                                <option value="varicella">{{ __('health_records_extra.label_2d1b9da16f47') }}</option>
                            </optgroup>
                            <optgroup label="🏃 Vaccins Recommandés pour les Sportifs">
                                <option value="influenza">{{ __('health_records_create.vaccine_influenza') }}</option>
                                <option value="hepatitis_a">{{ __('health_records_extra.label_c843de6cc811') }}</option>
                                <option value="typhoid">{{ __('health_records_extra.label_009e5c84eb8f') }}</option>
                                <option value="yellow_fever">{{ __('health_records_extra.label_0b657cfa0736') }}</option>
                                <option value="rabies">{{ __('health_records_extra.label_513564b7132d') }}</option>
                                <option value="tetanus_booster">{{ __('health_records_extra.label_6e652dafb7ba') }}</option>
                            </optgroup>
                            <optgroup label="🌍 Vaccins de Voyage">
                                <option value="japanese_encephalitis">{{ __('health_records_extra.label_b5520d85f5c4') }}</option>
                                <option value="tick_borne_encephalitis">{{ __('health_records_extra.label_493e61a629c6') }}</option>
                                <option value="cholera">{{ __('health_records_extra.label_682578253bf8') }}</option>
                                <option value="meningococcal_acwy">{{ __('health_records_extra.label_6ad8cd9300ea') }}</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vaccine_date" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_ea5e72849107') }}
                        </label>
                        <input 
                            type="date" 
                            id="vaccine_date" 
                            name="vaccine_date"
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="vaccine_lot" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_f4bcfe8eb588') }}
                        </label>
                        <input 
                            type="text" 
                            id="vaccine_lot" 
                            name="vaccine_lot"
                            placeholder="Ex: LOT123456"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="vaccine_manufacturer" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_733e0a23ad37') }}
                        </label>
                        <input 
                            type="text" 
                            id="vaccine_manufacturer" 
                            name="vaccine_manufacturer"
                            placeholder="Ex: Pfizer, Moderna, AstraZeneca"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="vaccine_dose" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_cde743724d66') }}
                        </label>
                        <select 
                            id="vaccine_dose" 
                            name="vaccine_dose"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                            <option value="1">{{ __('health_records_create.vaccine_dose_1') }}</option>
                            <option value="2">{{ __('health_records_create.vaccine_dose_2') }}</option>
                            <option value="3">{{ __('health_records_create.vaccine_dose_3') }}</option>
                            <option value="booster">{{ __('health_records_create.vaccine_dose_booster') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vaccine_expiry" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_e044f61a42e6') }}
                        </label>
                        <input 
                            type="date" 
                            id="vaccine_expiry" 
                            name="vaccine_expiry"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="vaccine_route" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_c453edb7b7c1') }}
                        </label>
                        <select 
                            id="vaccine_route" 
                            name="vaccine_route"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                            <option value="IM">{{ __('health_records_extra.label_c108eade3baf') }}</option>
                            <option value="SC">{{ __('health_records_extra.label_7147c24ba3d2') }}</option>
                            <option value="ID">{{ __('health_records_extra.label_9e5a7252551f') }}</option>
                            <option value="IN">{{ __('health_records_extra.label_0bc9c3370797') }}</option>
                            <option value="PO">{{ __('health_records_extra.label_60d7984e398e') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vaccine_site" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_7f83c20ef67c') }}
                        </label>
                        <select 
                            id="vaccine_site" 
                            name="vaccine_site"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                            <option value="LA">{{ __('health_records_extra.label_9c1a04e10130') }}</option>
                            <option value="RA">{{ __('health_records_extra.label_281292e0f854') }}</option>
                            <option value="LD">{{ __('health_records_extra.label_fe855488e1d1') }}</option>
                            <option value="RD">{{ __('health_records_extra.label_5b6b3d03ff4f') }}</option>
                            <option value="LG">{{ __('health_records_extra.label_093d56f1adb2') }}</option>
                            <option value="RG">{{ __('health_records_extra.label_675783006856') }}</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="vaccine_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea 
                        id="vaccine_notes" 
                        name="vaccine_notes"
                        rows="3"
                        placeholder="{{ __('health_records_extra.label_246996a3d1e4') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    ></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button 
                        type="button" 
                        id="cancel-vaccination-btn"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        {{ __('common.cancel') }}
                    </button>
                    <button 
                        type="button" 
                        id="save-vaccination-btn"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                    >
                        {{ __('common.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vaccination Schedule -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('health_records_extra.label_9adc5ec5e3f1') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_extra.label_776b6714723a') }}</h4>
                <div id="up-to-date-vaccines" class="space-y-2">
                    <div class="flex items-center p-3 bg-green-100 rounded-lg">
                        <span class="text-green-600 mr-2">✅</span>
                        <span class="text-sm text-green-800">{{ __('health_records_extra.label_36be23ea6231') }}</span>
                    </div>
                    <div class="flex items-center p-3 bg-green-100 rounded-lg">
                        <span class="text-green-600 mr-2">✅</span>
                        <span class="text-sm text-green-800">{{ __('health_records_extra.label_f230f1e89a63') }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('health_records_extra.label_91ec0800e280') }}</h4>
                <div id="due-vaccines" class="space-y-2">
                    <div class="flex items-center p-3 bg-yellow-100 rounded-lg">
                        <span class="text-yellow-600 mr-2">⚠️</span>
                        <span class="text-sm text-yellow-800">{{ __('health_records_extra.label_16710032062c') }}</span>
                    </div>
                    <div class="flex items-center p-3 bg-red-100 rounded-lg">
                        <span class="text-red-600 mr-2">🚨</span>
                        <span class="text-sm text-red-800">{{ __('health_records_extra.label_42a056b1a287') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vaccination Certificates -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">{{ __('health_records_extra.label_f8f520a73967') }}</h3>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">{{ __('health_records_extra.label_76b03fc52dec') }}</span>
                <button 
                    type="button" 
                    id="generate-certificate-btn"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm"
                >
                    {{ __('health_records_extra.label_7f6a8590d1f5') }}
                </button>
            </div>
            
            <div id="certificates-list" class="space-y-2">
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                    <div>
                        <span class="font-medium text-gray-800">{{ __('health_records_extra.label_5ff20ff35448') }}</span>
                        <span class="text-sm text-gray-500 block">{{ __('health_records_extra.label_4e6131759607') }}</span>
                    </div>
                    <button class="text-purple-600 hover:text-purple-800 text-sm">{{ __('health_records_extra.label_9fca98ca6512') }}</button>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                    <div>
                        <span class="font-medium text-gray-800">{{ __('health_records_extra.label_f3f08af081a4') }}</span>
                        <span class="text-sm text-gray-500 block">{{ __('health_records_extra.label_11242d9dfbf1') }}</span>
                    </div>
                    <button class="text-purple-600 hover:text-purple-800 text-sm">{{ __('health_records_extra.label_9fca98ca6512') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden fields for form submission -->
    <input type="hidden" name="vaccination_data" id="vaccination_data" value="{{ old('vaccination_data') }}">
</div>

<script>
// Vaccination Management
let vaccinations = [];

document.addEventListener('DOMContentLoaded', function() {
    initializeVaccinationManagement();
});

function initializeVaccinationManagement() {
    // Add vaccination button
    document.getElementById('add-vaccination-btn').addEventListener('click', function() {
        document.getElementById('add-vaccination-form').classList.remove('hidden');
    });
    
    // Cancel vaccination button
    document.getElementById('cancel-vaccination-btn').addEventListener('click', function() {
        document.getElementById('add-vaccination-form').classList.add('hidden');
        clearVaccinationForm();
    });
    
    // Save vaccination button
    document.getElementById('save-vaccination-btn').addEventListener('click', function() {
        saveVaccination();
    });
    
    // Generate certificate button
    document.getElementById('generate-certificate-btn').addEventListener('click', function() {
        generateVaccinationCertificate();
    });
}

function saveVaccination() {
    const formData = {
        vaccine_name: document.getElementById('vaccine_name').value,
        vaccine_date: document.getElementById('vaccine_date').value,
        vaccine_lot: document.getElementById('vaccine_lot').value,
        vaccine_manufacturer: document.getElementById('vaccine_manufacturer').value,
        vaccine_dose: document.getElementById('vaccine_dose').value,
        vaccine_expiry: document.getElementById('vaccine_expiry').value,
        vaccine_route: document.getElementById('vaccine_route').value,
        vaccine_site: document.getElementById('vaccine_site').value,
        vaccine_notes: document.getElementById('vaccine_notes').value,
        id: Date.now() // Unique ID for this vaccination
    };
    
    if (!formData.vaccine_name || !formData.vaccine_date) {
        alert('Veuillez remplir les champs obligatoires');
        return;
    }
    
    vaccinations.push(formData);
    updateVaccinationList();
    updateVaccinationData();
    
    // Hide form and clear
    document.getElementById('add-vaccination-form').classList.add('hidden');
    clearVaccinationForm();
}

function clearVaccinationForm() {
    document.getElementById('vaccine_name').value = '';
    document.getElementById('vaccine_date').value = '{{ date("Y-m-d") }}';
    document.getElementById('vaccine_lot').value = '';
    document.getElementById('vaccine_manufacturer').value = '';
    document.getElementById('vaccine_dose').value = '1';
    document.getElementById('vaccine_expiry').value = '';
    document.getElementById('vaccine_route').value = 'IM';
    document.getElementById('vaccine_site').value = 'LA';
    document.getElementById('vaccine_notes').value = '';
}

function updateVaccinationList() {
    const list = document.getElementById('vaccination-list');
    list.innerHTML = '';
    
    vaccinations.forEach((vaccination, index) => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200';
        div.innerHTML = `
            <div class="flex-1">
                <div class="font-medium text-gray-800">${getVaccineDisplayName(vaccination.vaccine_name)}</div>
                <div class="text-sm text-gray-600">
                    Date: ${formatDate(vaccination.vaccine_date)} | 
                    Dose: ${vaccination.vaccine_dose} | 
                    Site: ${vaccination.vaccine_site}
                </div>
                ${vaccination.vaccine_notes ? `<div class="text-xs text-gray-500 mt-1">${vaccination.vaccine_notes}</div>` : ''}
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="editVaccination(${index})" class="text-blue-600 hover:text-blue-800 text-sm">✏️</button>
                <button onclick="deleteVaccination(${index})" class="text-red-600 hover:text-red-800 text-sm">🗑️</button>
            </div>
        `;
        list.appendChild(div);
    });
}

function getVaccineDisplayName(vaccineName) {
    const vaccineNames = {
        'diphtheria_tetanus_polio': 'DTP - Diphtérie, Tétanos, Poliomyélite',
        'measles_mumps_rubella': 'ROR - Rougeole, Oreillons, Rubéole',
        'hepatitis_b': 'Hépatite B',
        'pneumococcal': 'Pneumocoque',
        'meningococcal': 'Méningocoque',
        'varicella': 'Varicelle',
        'influenza': 'Grippe saisonnière',
        'hepatitis_a': 'Hépatite A',
        'typhoid': 'Fièvre typhoïde',
        'yellow_fever': 'Fièvre jaune',
        'rabies': 'Rage',
        'tetanus_booster': 'Rappel Tétanos',
        'japanese_encephalitis': 'Encéphalite japonaise',
        'tick_borne_encephalitis': 'Encéphalite à tiques',
        'cholera': 'Choléra',
        'meningococcal_acwy': 'Méningocoque ACWY'
    };
    
    return vaccineNames[vaccineName] || vaccineName;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function editVaccination(index) {
    const vaccination = vaccinations[index];
    
    document.getElementById('vaccine_name').value = vaccination.vaccine_name;
    document.getElementById('vaccine_date').value = vaccination.vaccine_date;
    document.getElementById('vaccine_lot').value = vaccination.vaccine_lot;
    document.getElementById('vaccine_manufacturer').value = vaccination.vaccine_manufacturer;
    document.getElementById('vaccine_dose').value = vaccination.vaccine_dose;
    document.getElementById('vaccine_expiry').value = vaccination.vaccine_expiry;
    document.getElementById('vaccine_route').value = vaccination.vaccine_route;
    document.getElementById('vaccine_site').value = vaccination.vaccine_site;
    document.getElementById('vaccine_notes').value = vaccination.vaccine_notes;
    
    // Remove the old vaccination
    vaccinations.splice(index, 1);
    
    // Show form
    document.getElementById('add-vaccination-form').classList.remove('hidden');
}

function deleteVaccination(index) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette vaccination ?')) {
        vaccinations.splice(index, 1);
        updateVaccinationList();
        updateVaccinationData();
    }
}

function updateVaccinationData() {
    document.getElementById('vaccination_data').value = JSON.stringify(vaccinations);
}

function generateVaccinationCertificate() {
    if (vaccinations.length === 0) {
        alert('Aucune vaccination enregistrée pour générer un certificat');
        return;
    }
    
    // Simulate certificate generation
    const certificateData = {
        patient: 'Nom du patient',
        date: new Date().toLocaleDateString('fr-FR'),
        vaccinations: vaccinations
    };
    
    // Create and download certificate
    const blob = new Blob([JSON.stringify(certificateData, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'certificat-vaccinal.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    alert('Certificat vaccinal généré avec succès !');
}
</script> 