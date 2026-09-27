<!-- Onglet: Imagerie Médicale -->
<div class="space-y-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('health_records_create.medical_imaging_heading') }}</h3>
        <p class="text-blue-700 mb-4">{{ __('health_records_extra.label_25958a058952') }}</p>
        
        <!-- Imaging Records -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h4 class="text-md font-semibold text-gray-800">{{ __('health_records_create.imaging_exams_heading') }}</h4>
                <button 
                    type="button" 
                    id="add-imaging-btn"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    {{ __('health_records_create.add_exam_button') }}
                </button>
            </div>
            
            <!-- Add Imaging Form -->
            <div id="add-imaging-form" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                <h5 class="text-md font-semibold text-gray-800 mb-4">{{ __('health_records_create.new_imaging_exam_heading') }}</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="imaging_type" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_create.exam_type_label') }}
                        </label>
                        <select 
                            id="imaging_type" 
                            name="imaging_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">{{ __('secretary.select_type_placeholder') }}</option>
                            <optgroup label="🦴 Radiographie">
                                <option value="xray_chest">{{ __('health_records_create.xray_chest') }}</option>
                                <option value="xray_spine">{{ __('health_records_create.xray_spine') }}</option>
                                <option value="xray_limb">{{ __('health_records_create.xray_limb') }}</option>
                                <option value="xray_skull">{{ __('health_records_create.xray_skull') }}</option>
                            </optgroup>
                            <optgroup label="🧠 Scanner">
                                <option value="ct_head">{{ __('health_records_create.ct_head') }}</option>
                                <option value="ct_chest">{{ __('health_records_create.ct_chest') }}</option>
                                <option value="ct_abdomen">{{ __('health_records_create.ct_abdomen') }}</option>
                                <option value="ct_spine">{{ __('health_records_create.ct_spine') }}</option>
                            </optgroup>
                            <optgroup label="🧲 IRM">
                                <option value="mri_brain">{{ __('health_records_create.mri_brain') }}</option>
                                <option value="mri_spine">{{ __('health_records_create.mri_spine') }}</option>
                                <option value="mri_knee">{{ __('health_records_create.mri_knee') }}</option>
                                <option value="mri_shoulder">{{ __('health_records_create.mri_shoulder') }}</option>
                            </optgroup>
                            <optgroup label="🔍 Échographie">
                                <option value="us_abdomen">{{ __('health_records_create.us_abdomen') }}</option>
                                <option value="us_heart">{{ __('health_records_create.us_heart') }}</option>
                                <option value="us_vascular">{{ __('health_records_create.us_vascular') }}</option>
                                <option value="us_musculoskeletal">{{ __('health_records_create.us_musculoskeletal') }}</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div>
                        <label for="imaging_date" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_create.exam_date_label') }}
                        </label>
                        <input 
                            type="date" 
                            id="imaging_date" 
                            name="imaging_date"
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="imaging_facility" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_create.facility_label') }}
                        </label>
                        <input 
                            type="text" 
                            id="imaging_facility" 
                            name="imaging_facility"
                            placeholder="{{ __('health_records_create.facility_placeholder') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="imaging_radiologist" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('health_records_create.radiologist_label') }}
                        </label>
                        <input 
                            type="text" 
                            id="imaging_radiologist" 
                            name="imaging_radiologist"
                            placeholder="{{ __('health_records_create.radiologist_placeholder') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="imaging_indication" class="block text-sm font-medium text-gray-700 mb-2">
                            Indication
                        </label>
                        <input 
                            type="text" 
                            id="imaging_indication" 
                            name="imaging_indication"
                            placeholder="{{ __('health_records_edit.reason_for_examination') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="imaging_technique" class="block text-sm font-medium text-gray-700 mb-2">
                            Technique
                        </label>
                        <select 
                            id="imaging_technique" 
                            name="imaging_technique"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">{{ __('clinical.select_button') }}</option>
                            <option value="standard">Standard</option>
                            <option value="contrast">{{ __('health_records_create.technique_contrast') }}</option>
                            <option value="functional">{{ __('health_records_create.technique_functional') }}</option>
                            <option value="dynamic">{{ __('health_records_create.technique_dynamic') }}</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="imaging_findings" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('health_records_create.findings_label') }}
                    </label>
                    <textarea 
                        id="imaging_findings" 
                        name="imaging_findings"
                        rows="4"
                        placeholder="{{ __('health_records_create.findings_placeholder') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    ></textarea>
                </div>
                
                <div class="mt-4">
                    <label for="imaging_conclusion" class="block text-sm font-medium text-gray-700 mb-2">
                        Conclusion
                    </label>
                    <textarea 
                        id="imaging_conclusion" 
                        name="imaging_conclusion"
                        rows="3"
                        placeholder="{{ __('health_records_create.conclusion_placeholder') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    ></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button 
                        type="button" 
                        id="cancel-imaging-btn"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        {{ __('common.cancel') }}
                    </button>
                    <button 
                        type="button" 
                        id="save-imaging-btn"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        {{ __('common.save') }}
                    </button>
                </div>
            </div>
            
            <!-- Imaging List -->
            <div id="imaging-list" class="space-y-3">
                <!-- Imaging records will be added here dynamically -->
            </div>
        </div>
    </div>

    <!-- Imaging Summary -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('health_records_create.imaging_summary_heading') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg border border-green-200">
                <h4 class="font-medium text-green-800 mb-2">{{ __('health_records_create.exams_performed_heading') }}</h4>
                <div id="imaging-count" class="text-sm text-gray-700">
                    <span class="text-green-600">0</span> {{ __('health_records_create.exam_count_suffix') }}
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-green-200">
                <h4 class="font-medium text-green-800 mb-2">{{ __('health_records_create.exam_types_heading') }}</h4>
                <div id="imaging-types" class="text-sm text-gray-700">
                    <span class="text-green-600">{{ __('pcma.report_none') }}</span>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-green-200">
                <h4 class="font-medium text-green-800 mb-2">{{ __('health_records_create.abnormal_results_heading') }}</h4>
                <div id="imaging-abnormal" class="text-sm text-gray-700">
                    <span class="text-green-600">0</span> {{ __('health_records_create.abnormal_count_suffix') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden fields for form submission -->
    <input type="hidden" name="imaging_data" id="imaging_data" value="{{ old('imaging_data') }}">
</div>

<script>
// Imaging Management
let imagingRecords = [];

document.addEventListener('DOMContentLoaded', function() {
    initializeImagingManagement();
});

function initializeImagingManagement() {
    // Add imaging button
    document.getElementById('add-imaging-btn').addEventListener('click', function() {
        document.getElementById('add-imaging-form').classList.remove('hidden');
    });
    
    // Cancel imaging button
    document.getElementById('cancel-imaging-btn').addEventListener('click', function() {
        document.getElementById('add-imaging-form').classList.add('hidden');
        clearImagingForm();
    });
    
    // Save imaging button
    document.getElementById('save-imaging-btn').addEventListener('click', function() {
        saveImagingRecord();
    });
}

function saveImagingRecord() {
    const formData = {
        imaging_type: document.getElementById('imaging_type').value,
        imaging_date: document.getElementById('imaging_date').value,
        imaging_facility: document.getElementById('imaging_facility').value,
        imaging_radiologist: document.getElementById('imaging_radiologist').value,
        imaging_indication: document.getElementById('imaging_indication').value,
        imaging_technique: document.getElementById('imaging_technique').value,
        imaging_findings: document.getElementById('imaging_findings').value,
        imaging_conclusion: document.getElementById('imaging_conclusion').value,
        id: Date.now()
    };
    
    if (!formData.imaging_type || !formData.imaging_date || !formData.imaging_findings) {
        alert(@json(__('Veuillez remplir les champs obligatoires')));
        return;
    }
    
    imagingRecords.push(formData);
    updateImagingList();
    updateImagingData();
    
    document.getElementById('add-imaging-form').classList.add('hidden');
    clearImagingForm();
}

function clearImagingForm() {
    document.getElementById('imaging_type').value = '';
    document.getElementById('imaging_date').value = '{{ date("Y-m-d") }}';
    document.getElementById('imaging_facility').value = '';
    document.getElementById('imaging_radiologist').value = '';
    document.getElementById('imaging_indication').value = '';
    document.getElementById('imaging_technique').value = '';
    document.getElementById('imaging_findings').value = '';
    document.getElementById('imaging_conclusion').value = '';
}

function updateImagingList() {
    const list = document.getElementById('imaging-list');
    list.innerHTML = '';
    
    imagingRecords.forEach((record, index) => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200';
        div.innerHTML = `
            <div class="flex-1">
                <div class="font-medium text-gray-800">${getImagingTypeDisplayName(record.imaging_type)}</div>
                <div class="text-sm text-gray-600">
                    Date: ${formatDate(record.imaging_date)} | 
                    ${record.imaging_facility ? `Établissement: ${record.imaging_facility}` : ''}
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    ${record.imaging_findings.substring(0, 100)}${record.imaging_findings.length > 100 ? '...' : ''}
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="editImagingRecord(${index})" class="text-blue-600 hover:text-blue-800 text-sm">✏️</button>
                <button onclick="deleteImagingRecord(${index})" class="text-red-600 hover:text-red-800 text-sm">🗑️</button>
            </div>
        `;
        list.appendChild(div);
    });
}

function getImagingTypeDisplayName(type) {
    const types = {
        'xray_chest': 'Radiographie thoracique',
        'xray_spine': 'Radiographie rachis',
        'xray_limb': 'Radiographie membre',
        'xray_skull': 'Radiographie crâne',
        'ct_head': 'Scanner cérébral',
        'ct_chest': 'Scanner thoracique',
        'ct_abdomen': 'Scanner abdominal',
        'ct_spine': 'Scanner rachis',
        'mri_brain': 'IRM cérébrale',
        'mri_spine': 'IRM rachis',
        'mri_knee': 'IRM genou',
        'mri_shoulder': 'IRM épaule',
        'us_abdomen': 'Échographie abdominale',
        'us_heart': 'Échographie cardiaque',
        'us_vascular': 'Échographie vasculaire',
        'us_musculoskeletal': 'Échographie musculo-squelettique'
    };
    return types[type] || type;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function editImagingRecord(index) {
    const record = imagingRecords[index];
    
    document.getElementById('imaging_type').value = record.imaging_type;
    document.getElementById('imaging_date').value = record.imaging_date;
    document.getElementById('imaging_facility').value = record.imaging_facility;
    document.getElementById('imaging_radiologist').value = record.imaging_radiologist;
    document.getElementById('imaging_indication').value = record.imaging_indication;
    document.getElementById('imaging_technique').value = record.imaging_technique;
    document.getElementById('imaging_findings').value = record.imaging_findings;
    document.getElementById('imaging_conclusion').value = record.imaging_conclusion;
    
    imagingRecords.splice(index, 1);
    
    document.getElementById('add-imaging-form').classList.remove('hidden');
}

function deleteImagingRecord(index) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet examen d\'imagerie ?')) {
        imagingRecords.splice(index, 1);
        updateImagingList();
        updateImagingData();
    }
}

function updateImagingData() {
    document.getElementById('imaging_data').value = JSON.stringify(imagingRecords);
    updateImagingSummary();
}

function updateImagingSummary() {
    const countElement = document.getElementById('imaging-count');
    const typesElement = document.getElementById('imaging-types');
    const abnormalElement = document.getElementById('imaging-abnormal');
    
    if (countElement) {
        countElement.innerHTML = `<span class="text-green-600">${imagingRecords.length}</span> examen(s)`;
    }
    
    if (typesElement) {
        const types = [...new Set(imagingRecords.map(r => r.imaging_type))];
        if (types.length > 0) {
            typesElement.innerHTML = `<span class="text-green-600">${types.length}</span> type(s) d'examen`;
        } else {
            typesElement.innerHTML = `<span class="text-green-600">{{ __('health_records_create.none_label') }}</span>`;
        }
    }
    
    if (abnormalElement) {
        const abnormalCount = imagingRecords.filter(r => 
            r.imaging_findings.toLowerCase().includes('anormal') || 
            r.imaging_findings.toLowerCase().includes('pathologique') ||
            r.imaging_findings.toLowerCase().includes('fracture') ||
            r.imaging_findings.toLowerCase().includes('lésion')
        ).length;
        abnormalElement.innerHTML = `<span class="text-green-600">${abnormalCount}</span> résultat(s) anormal(aux)`;
    }
}
</script> 