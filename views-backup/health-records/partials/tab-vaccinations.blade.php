<!-- Onglet: Vaccinations -->
<div class="space-y-6">
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">💉 Historique Vaccinal</h3>
        <p class="text-green-700 mb-4">Gestion complète des vaccinations selon les standards internationaux</p>
        
        <!-- Vaccination Records -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h4 class="text-md font-semibold text-gray-800">Vaccinations enregistrées</h4>
                <button 
                    type="button" 
                    id="add-vaccination-btn"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                    ➕ Ajouter une vaccination
                </button>
            </div>
            
            <!-- Vaccination List -->
            <div id="vaccination-list" class="space-y-3">
                <!-- Vaccinations will be added here dynamically -->
            </div>
            
            <!-- Add Vaccination Form -->
            <div id="add-vaccination-form" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                <h5 class="text-md font-semibold text-gray-800 mb-4">Nouvelle Vaccination</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du Vaccin *
                        </label>
                        <select 
                            id="vaccine_name" 
                            name="vaccine_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                            <option value="">Sélectionner un vaccin</option>
                            <optgroup label="🏥 Vaccins Obligatoires">
                                <option value="diphtheria_tetanus_polio">DTP - Diphtérie, Tétanos, Poliomyélite</option>
                                <option value="measles_mumps_rubella">ROR - Rougeole, Oreillons, Rubéole</option>
                                <option value="hepatitis_b">Hépatite B</option>
                                <option value="pneumococcal">Pneumocoque</option>
                                <option value="meningococcal">Méningocoque</option>
                                <option value="varicella">Varicelle</option>
                            </optgroup>
                            <optgroup label="🏃 Vaccins Recommandés pour les Sportifs">
                                <option value="influenza">Grippe saisonnière</option>
                                <option value="hepatitis_a">Hépatite A</option>
                                <option value="typhoid">Fièvre typhoïde</option>
                                <option value="yellow_fever">Fièvre jaune</option>
                                <option value="rabies">Rage</option>
                                <option value="tetanus_booster">Rappel Tétanos</option>
                            </optgroup>
                            <optgroup label="🌍 Vaccins de Voyage">
                                <option value="japanese_encephalitis">Encéphalite japonaise</option>
                                <option value="tick_borne_encephalitis">Encéphalite à tiques</option>
                                <option value="cholera">Choléra</option>
                                <option value="meningococcal_acwy">Méningocoque ACWY</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vaccine_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de Vaccination *
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
                            Numéro de Lot
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
                            Fabricant
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
                            Numéro de Dose
                        </label>
                        <select 
                            id="vaccine_dose" 
                            name="vaccine_dose"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                            <option value="1">1ère dose</option>
                            <option value="2">2ème dose</option>
                            <option value="3">3ème dose</option>
                            <option value="booster">Rappel</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vaccine_expiry" class="block text-sm font-medium text-gray-700 mb-2">
                            Date d'Expiration
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
                            Voie d'Administration
                        </label>
                        <select 
                            id="vaccine_route" 
                            name="vaccine_route"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                            <option value="IM">Intramusculaire (IM)</option>
                            <option value="SC">Sous-cutanée (SC)</option>
                            <option value="ID">Intradermique (ID)</option>
                            <option value="IN">Intranasale (IN)</option>
                            <option value="PO">Orale (PO)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="vaccine_site" class="block text-sm font-medium text-gray-700 mb-2">
                            Site d'Injection
                        </label>
                        <select 
                            id="vaccine_site" 
                            name="vaccine_site"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                            <option value="LA">Bras gauche, antérieur (LA)</option>
                            <option value="RA">Bras droit, antérieur (RA)</option>
                            <option value="LD">Bras gauche, deltoïde (LD)</option>
                            <option value="RD">Bras droit, deltoïde (RD)</option>
                            <option value="LG">Jambe gauche (LG)</option>
                            <option value="RG">Jambe droite (RG)</option>
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
                        placeholder="Observations, réactions, etc."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    ></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button 
                        type="button" 
                        id="cancel-vaccination-btn"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Annuler
                    </button>
                    <button 
                        type="button" 
                        id="save-vaccination-btn"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                    >
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vaccination Schedule -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">📅 Calendrier Vaccinal</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">Vaccins à jour</h4>
                <div id="up-to-date-vaccines" class="space-y-2">
                    <div class="flex items-center p-3 bg-green-100 rounded-lg">
                        <span class="text-green-600 mr-2">✅</span>
                        <span class="text-sm text-green-800">DTP - Dernière dose: 15/01/2023</span>
                    </div>
                    <div class="flex items-center p-3 bg-green-100 rounded-lg">
                        <span class="text-green-600 mr-2">✅</span>
                        <span class="text-sm text-green-800">Hépatite B - Dernière dose: 20/03/2023</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-3">Vaccins à effectuer</h4>
                <div id="due-vaccines" class="space-y-2">
                    <div class="flex items-center p-3 bg-yellow-100 rounded-lg">
                        <span class="text-yellow-600 mr-2">⚠️</span>
                        <span class="text-sm text-yellow-800">Rappel Tétanos - Échéance: 15/01/2024</span>
                    </div>
                    <div class="flex items-center p-3 bg-red-100 rounded-lg">
                        <span class="text-red-600 mr-2">🚨</span>
                        <span class="text-sm text-red-800">Grippe saisonnière - En retard</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vaccination Certificates -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">📋 Certificats Vaccinaux</h3>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Certificats disponibles</span>
                <button 
                    type="button" 
                    id="generate-certificate-btn"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm"
                >
                    📄 Générer un certificat
                </button>
            </div>
            
            <div id="certificates-list" class="space-y-2">
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                    <div>
                        <span class="font-medium text-gray-800">Certificat vaccinal complet</span>
                        <span class="text-sm text-gray-500 block">Généré le 15/01/2024</span>
                    </div>
                    <button class="text-purple-600 hover:text-purple-800 text-sm">📥 Télécharger</button>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                    <div>
                        <span class="font-medium text-gray-800">Certificat de voyage</span>
                        <span class="text-sm text-gray-500 block">Généré le 10/01/2024</span>
                    </div>
                    <button class="text-purple-600 hover:text-purple-800 text-sm">📥 Télécharger</button>
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