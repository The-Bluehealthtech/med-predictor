<!-- Onglet: Contrôle Anti-Dopage -->
<div class="space-y-6">
    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-red-900 mb-4">{{ __('health_records_create.doping_control_heading') }}</h3>
        <p class="text-red-700 mb-4">{{ __('health_records_extra.label_41c9e647095c') }}</p>
        
        <!-- Doping Tests -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h4 class="text-md font-semibold text-gray-800">{{ __('health_records_create.doping_tests_heading') }}</h4>
                <button 
                    type="button" 
                    id="add-doping-test-btn"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                >
                    {{ __('health_records_extra.label_ffcaaa4473fe') }}
                </button>
            </div>
            
            <!-- Doping Test Form -->
            <div id="add-doping-test-form" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                <h5 class="text-md font-semibold text-gray-800 mb-4">{{ __('health_records_extra.label_1bfac8cf75c9') }}</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="doping_test_type" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_5678190cf3d7') }}
                        </label>
                        <select 
                            id="doping_test_type" 
                            name="doping_test_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="">{{ __('secretary.select_type_placeholder') }}</option>
                            <option value="urine">{{ __('health_records_extra.label_e10f0ff27a4c') }}</option>
                            <option value="blood">{{ __('health_records_extra.label_4722d131fa87') }}</option>
                            <option value="saliva">{{ __('health_records_extra.label_9503cc65d565') }}</option>
                            <option value="hair">{{ __('health_records_extra.label_0306731c0554') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="doping_test_date" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_d758481d44b1') }}
                        </label>
                        <input 
                            type="date" 
                            id="doping_test_date" 
                            name="doping_test_date"
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="doping_test_result" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_1496fb77ab72') }}
                        </label>
                        <select 
                            id="doping_test_result" 
                            name="doping_test_result"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="">{{ __('health_records_extra.label_0a5809c095b2') }}</option>
                            <option value="negative">{{ __('health_records_edit.negative') }}</option>
                            <option value="positive">{{ __('health_records_edit.positive') }}</option>
                            <option value="inconclusive">{{ __('health_records_edit.inconclusive') }}</option>
                            <option value="pending">{{ __('healthcare.status_pending') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="doping_test_laboratory" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_0f78e26d87bf') }}
                        </label>
                        <input 
                            type="text" 
                            id="doping_test_laboratory" 
                            name="doping_test_laboratory"
                            placeholder="{{ __('Nom du laboratoire') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="doping_test_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea 
                        id="doping_test_notes" 
                        name="doping_test_notes"
                        rows="3"
                        placeholder="{{ __('health_records_extra.label_b1bdb0697f98') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    ></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button 
                        type="button" 
                        id="cancel-doping-test-btn"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        {{ __('common.cancel') }}
                    </button>
                    <button 
                        type="button" 
                        id="save-doping-test-btn"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        {{ __('common.save') }}
                    </button>
                </div>
            </div>
            
            <!-- Doping Tests List -->
            <div id="doping-tests-list" class="space-y-3">
                <!-- Tests will be added here dynamically -->
            </div>
        </div>
    </div>

    <!-- Therapeutic Use Exemptions (AUT) -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('health_records_extra.label_3d4f37e11c88') }}</h3>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h4 class="text-md font-semibold text-gray-800">{{ __('health_records_extra.label_89ecc0eeab10') }}</h4>
                <button 
                    type="button" 
                    id="add-aut-btn"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    {{ __('health_records_extra.label_f6f5a3050069') }}
                </button>
            </div>
            
            <!-- AUT Form -->
            <div id="add-aut-form" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                <h5 class="text-md font-semibold text-gray-800 mb-4">{{ __('health_records_extra.label_a635c3c3220c') }}</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="aut_medication" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_488992861e92') }}
                        </label>
                        <input 
                            type="text" 
                            id="aut_medication" 
                            name="aut_medication"
                            placeholder="{{ __('health_records_extra.label_1c5d71198d84') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="aut_diagnosis" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_72945d1ffac9') }}
                        </label>
                        <input 
                            type="text" 
                            id="aut_diagnosis" 
                            name="aut_diagnosis"
                            placeholder="Diagnostic justifiant l'AUT"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="aut_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_b8a1b42187f4') }}
                        </label>
                        <input 
                            type="date" 
                            id="aut_start_date" 
                            name="aut_start_date"
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="aut_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_4528f01994e5') }}
                        </label>
                        <input 
                            type="date" 
                            id="aut_end_date" 
                            name="aut_end_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="aut_status" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('pcma.status_label') }}
                        </label>
                        <select 
                            id="aut_status" 
                            name="aut_status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">{{ __('health_records_extra.label_552c0f1db1ef') }}</option>
                            <option value="pending">{{ __('healthcare.status_pending') }}</option>
                            <option value="approved">{{ __('health_records_edit.approved') }}</option>
                            <option value="rejected">{{ __('health_records_edit.rejected') }}</option>
                            <option value="expired">{{ __('health_records_edit.expired') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="aut_dosage" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_extra.label_d3b05e17389b') }}
                        </label>
                        <input 
                            type="text" 
                            id="aut_dosage" 
                            name="aut_dosage"
                            placeholder="Ex: 10mg/jour"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="aut_justification" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_extra.label_d6c78de09359') }}
                    </label>
                    <textarea 
                        id="aut_justification" 
                        name="aut_justification"
                        rows="4"
                        placeholder="{{ __('health_records_extra.label_d02e4e4bffb8') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    ></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button 
                        type="button" 
                        id="cancel-aut-btn"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        {{ __('common.cancel') }}
                    </button>
                    <button 
                        type="button" 
                        id="save-aut-btn"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        {{ __('common.submit') }}
                    </button>
                </div>
            </div>
            
            <!-- AUT List -->
            <div id="aut-list" class="space-y-3">
                <!-- AUTs will be added here dynamically -->
            </div>
        </div>
    </div>

    <!-- Doping Control Summary -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-4">{{ __('health_records_extra.label_aba83b9ae043') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-800 mb-2">{{ __('health_records_extra.label_45cb1811f300') }}</h4>
                <div id="doping-tests-summary" class="text-sm text-gray-700">
                    <span class="text-yellow-600">0</span> {{ __('health_records_extra.label_1e0c13cb2017') }}
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-800 mb-2">{{ __('health_records_extra.label_d01adf25e6a2') }}</h4>
                <div id="aut-summary" class="text-sm text-gray-700">
                    <span class="text-yellow-600">0</span> {{ __('health_records_extra.label_b1ff0f6544ab') }}
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-800 mb-2">{{ __('health_records_extra.label_ca5f8c7dd923') }}</h4>
                <div id="doping-alerts" class="text-sm text-gray-700">
                    <span class="text-green-600">{{ __('health_records_extra.label_5792b61b625f') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden fields for form submission -->
    <input type="hidden" name="doping_data" id="doping_data" value="{{ old('doping_data') }}">
</div>

<script>
// Doping Control Management
let dopingTests = [];
let auts = [];

document.addEventListener('DOMContentLoaded', function() {
    initializeDopingControl();
});

function initializeDopingControl() {
    // Doping test buttons
    document.getElementById('add-doping-test-btn').addEventListener('click', function() {
        document.getElementById('add-doping-test-form').classList.remove('hidden');
    });
    
    document.getElementById('cancel-doping-test-btn').addEventListener('click', function() {
        document.getElementById('add-doping-test-form').classList.add('hidden');
        clearDopingTestForm();
    });
    
    document.getElementById('save-doping-test-btn').addEventListener('click', function() {
        saveDopingTest();
    });
    
    // AUT buttons
    document.getElementById('add-aut-btn').addEventListener('click', function() {
        document.getElementById('add-aut-form').classList.remove('hidden');
    });
    
    document.getElementById('cancel-aut-btn').addEventListener('click', function() {
        document.getElementById('add-aut-form').classList.add('hidden');
        clearAUTForm();
    });
    
    document.getElementById('save-aut-btn').addEventListener('click', function() {
        saveAUT();
    });
}

function saveDopingTest() {
    const formData = {
        test_type: document.getElementById('doping_test_type').value,
        test_date: document.getElementById('doping_test_date').value,
        test_result: document.getElementById('doping_test_result').value,
        test_laboratory: document.getElementById('doping_test_laboratory').value,
        test_notes: document.getElementById('doping_test_notes').value,
        id: Date.now()
    };
    
    if (!formData.test_type || !formData.test_date || !formData.test_result) {
        alert(@json(__('Veuillez remplir les champs obligatoires')));
        return;
    }
    
    dopingTests.push(formData);
    updateDopingTestsList();
    updateDopingData();
    
    document.getElementById('add-doping-test-form').classList.add('hidden');
    clearDopingTestForm();
}

function clearDopingTestForm() {
    document.getElementById('doping_test_type').value = '';
    document.getElementById('doping_test_date').value = '{{ date("Y-m-d") }}';
    document.getElementById('doping_test_result').value = '';
    document.getElementById('doping_test_laboratory').value = '';
    document.getElementById('doping_test_notes').value = '';
}

function updateDopingTestsList() {
    const list = document.getElementById('doping-tests-list');
    list.innerHTML = '';
    
    dopingTests.forEach((test, index) => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200';
        
        const resultColor = test.test_result === 'positive' ? 'text-red-600' : 
                          test.test_result === 'negative' ? 'text-green-600' : 'text-yellow-600';
        
        div.innerHTML = `
            <div class="flex-1">
                <div class="font-medium text-gray-800">${getTestTypeDisplayName(test.test_type)}</div>
                <div class="text-sm text-gray-600">
                    Date: ${formatDate(test.test_date)} | 
                    Résultat: <span class="${resultColor} font-semibold">${getResultDisplayName(test.test_result)}</span>
                </div>
                ${test.test_laboratory ? `<div class="text-xs text-gray-500">Labo: ${test.test_laboratory}</div>` : ''}
                ${test.test_notes ? `<div class="text-xs text-gray-500 mt-1">${test.test_notes}</div>` : ''}
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="editDopingTest(${index})" class="text-blue-600 hover:text-blue-800 text-sm">✏️</button>
                <button onclick="deleteDopingTest(${index})" class="text-red-600 hover:text-red-800 text-sm">🗑️</button>
            </div>
        `;
        list.appendChild(div);
    });
}

function saveAUT() {
    const formData = {
        medication: document.getElementById('aut_medication').value,
        diagnosis: document.getElementById('aut_diagnosis').value,
        start_date: document.getElementById('aut_start_date').value,
        end_date: document.getElementById('aut_end_date').value,
        status: document.getElementById('aut_status').value,
        dosage: document.getElementById('aut_dosage').value,
        justification: document.getElementById('aut_justification').value,
        id: Date.now()
    };
    
    if (!formData.medication || !formData.diagnosis || !formData.start_date || !formData.end_date || !formData.status || !formData.justification) {
        alert(@json(__('Veuillez remplir les champs obligatoires')));
        return;
    }
    
    auts.push(formData);
    updateAUTList();
    updateDopingData();
    
    document.getElementById('add-aut-form').classList.add('hidden');
    clearAUTForm();
}

function clearAUTForm() {
    document.getElementById('aut_medication').value = '';
    document.getElementById('aut_diagnosis').value = '';
    document.getElementById('aut_start_date').value = '{{ date("Y-m-d") }}';
    document.getElementById('aut_end_date').value = '';
    document.getElementById('aut_status').value = '';
    document.getElementById('aut_dosage').value = '';
    document.getElementById('aut_justification').value = '';
}

function updateAUTList() {
    const list = document.getElementById('aut-list');
    list.innerHTML = '';
    
    auts.forEach((aut, index) => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200';
        
        const statusColor = aut.status === 'approved' ? 'text-green-600' : 
                          aut.status === 'rejected' ? 'text-red-600' : 
                          aut.status === 'expired' ? 'text-gray-600' : 'text-yellow-600';
        
        div.innerHTML = `
            <div class="flex-1">
                <div class="font-medium text-gray-800">${aut.medication}</div>
                <div class="text-sm text-gray-600">
                    Diagnostic: ${aut.diagnosis} | 
                    Statut: <span class="${statusColor} font-semibold">${getStatusDisplayName(aut.status)}</span>
                </div>
                <div class="text-xs text-gray-500">
                    ${formatDate(aut.start_date)} - ${formatDate(aut.end_date)}
                    ${aut.dosage ? ` | Posologie: ${aut.dosage}` : ''}
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="editAUT(${index})" class="text-blue-600 hover:text-blue-800 text-sm">✏️</button>
                <button onclick="deleteAUT(${index})" class="text-red-600 hover:text-red-800 text-sm">🗑️</button>
            </div>
        `;
        list.appendChild(div);
    });
}

function getTestTypeDisplayName(type) {
    const types = {
        'urine': 'Test urinaire',
        'blood': 'Test sanguin',
        'saliva': 'Test salivaire',
        'hair': 'Test capillaire'
    };
    return types[type] || type;
}

function getResultDisplayName(result) {
    const results = {
        'negative': 'Négatif',
        'positive': 'Positif',
        'inconclusive': 'Inconclusif',
        'pending': 'En attente'
    };
    return results[result] || result;
}

function getStatusDisplayName(status) {
    const statuses = {
        'pending': 'En attente',
        'approved': 'Approuvée',
        'rejected': 'Rejetée',
        'expired': 'Expirée'
    };
    return statuses[status] || status;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function editDopingTest(index) {
    const test = dopingTests[index];
    
    document.getElementById('doping_test_type').value = test.test_type;
    document.getElementById('doping_test_date').value = test.test_date;
    document.getElementById('doping_test_result').value = test.test_result;
    document.getElementById('doping_test_laboratory').value = test.test_laboratory;
    document.getElementById('doping_test_notes').value = test.test_notes;
    
    dopingTests.splice(index, 1);
    
    document.getElementById('add-doping-test-form').classList.remove('hidden');
}

function deleteDopingTest(index) {
    if (confirm(@json(__('Êtes-vous sûr de vouloir supprimer ce test ?')))) {
        dopingTests.splice(index, 1);
        updateDopingTestsList();
        updateDopingData();
    }
}

function editAUT(index) {
    const aut = auts[index];
    
    document.getElementById('aut_medication').value = aut.medication;
    document.getElementById('aut_diagnosis').value = aut.diagnosis;
    document.getElementById('aut_start_date').value = aut.start_date;
    document.getElementById('aut_end_date').value = aut.end_date;
    document.getElementById('aut_status').value = aut.status;
    document.getElementById('aut_dosage').value = aut.dosage;
    document.getElementById('aut_justification').value = aut.justification;
    
    auts.splice(index, 1);
    
    document.getElementById('add-aut-form').classList.remove('hidden');
}

function deleteAUT(index) {
    if (confirm(@json(__('Êtes-vous sûr de vouloir supprimer cette AUT ?')))) {
        auts.splice(index, 1);
        updateAUTList();
        updateDopingData();
    }
}

function updateDopingData() {
    const data = {
        tests: dopingTests,
        auts: auts
    };
    document.getElementById('doping_data').value = JSON.stringify(data);
    
    // Update summary
    updateDopingSummary();
}

function updateDopingSummary() {
    const testsSummary = document.getElementById('doping-tests-summary');
    const autSummary = document.getElementById('aut-summary');
    const alerts = document.getElementById('doping-alerts');
    
    if (testsSummary) {
        testsSummary.innerHTML = `<span class="text-yellow-600">${dopingTests.length}</span> test(s) enregistré(s)`;
    }
    
    if (autSummary) {
        const activeAUTs = auts.filter(aut => aut.status === 'approved');
        autSummary.innerHTML = `<span class="text-yellow-600">${activeAUTs.length}</span> AUT active(s)`;
    }
    
    if (alerts) {
        const positiveTests = dopingTests.filter(test => test.test_result === 'positive');
        const expiredAUTs = auts.filter(aut => aut.status === 'expired');
        
        if (positiveTests.length > 0) {
            alerts.innerHTML = `<span class="text-red-600">⚠️ ${positiveTests.length} test(s) positif(s)</span>`;
        } else if (expiredAUTs.length > 0) {
            alerts.innerHTML = `<span class="text-yellow-600">⚠️ ${expiredAUTs.length} AUT expirée(s)</span>`;
        } else {
            alerts.innerHTML = `<span class="text-green-600">{{ __('health_records_extra.label_5792b61b625f') }}</span>`;
        }
    }
}
</script> 